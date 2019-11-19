<?php


namespace App\Services;


use App\Models\Parttime;
use App\Models\ParttimeRecord;
use App\Models\ParttimeUser;
use App\Transformers\ParttimeTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait ParttimeProcess
{
    use ParttimeAuthProcess;

    /**
     * UnNeed Auth
     * @param int $limit
     * @param int $page
     * @return array
     */
    public static function viewParttimes($limit = 8, $page = 1){
        $now = Carbon::now();
        $views = Parttime::query()
            # 条件
            # 未取消 未超过开始时间 未超过报名截止 人数未到上限或者限制是0
            ->where("cancelled", 0)
            # ->where("timestart", ">", $now)
            ->where("deadline", ">", $now)
            ->orderByDesc("created_at")
            ->get()->filter(function(Parttime $parttime){
                return !$parttime->reachLimited();
            })->forPage($page, $limit)->map(function (Parttime $parttime){
                return ParttimeTransformer::fastTransform($parttime, self::currentUser())->toArray();
            })->sortByDesc(function($value, $key){
                return $value['status'];
            })->all();
        return $views;
    }

    /**
     * @param $title
     * @param Carbon $from
     * @param Carbon $to
     * @param $location
     * @param Carbon $deadline
     * @param $detail
     * @param array $imgPath
     * @param int $limited
     * @param ParttimeUser|null $user
     * @return array withParttimeId
     */
    public static function createParttime(
        $title,
        Carbon $from, Carbon $to,
        $location,
        Carbon $deadline,
        $detail,
        array $imgPath = [],
        $limited = 0,
        ParttimeUser $user = null
    ){
        $succ = false;
        $msg = '';
        $data = null;
        $now = Carbon::now();
        if (
            # 所有日期都要是 未来
            # 报名截止 活动开始 活动结束
            $deadline->isAfter($from) ||
            $from->isAfter($to) ||
            $now->isAfter($from) ||
            $now->isAfter($to) ||
            $now->isAfter($deadline)
        ){
            $msg = 'date error';
        }else if(
            # 活动结束日期不能超过一个月
            $to->diffInDays($now) > 30 ||
            $deadline->diffInDays($now) > 30
        ){
            $msg = "date too late";
        }else if (count($locs = explode(",", $location)) != 2){
            $msg = "location error";
        }else {

            # TODO 地理位置转化为位置信息
            $location_str = "";

            if ($user == null) $user = self::currentUser();
            $parttime = Parttime::createNew($title, $from, $to, $location, $deadline, $user->getId(), $detail, $imgPath, $limited);
            $succ = $parttime != null;
        }
        return $succ ?
            ['succ' => true, 'id' => $parttime->getId()] :
            ['succ' => $succ, 'msg' => $msg];
    }

    /**
     * @param $parttime_id
     * @param ParttimeUser|null $user
     * @throws \Exception
     * @return array withNothing
     */
    public static function deleteParttime($parttime_id, ParttimeUser $user = null){
        /** @var Parttime $parttime */
        $parttime = Parttime::query()->find($parttime_id);
        $succ = false;$msg = "";
        /** @var ParttimeUser $user */
        if ($user == null) $user = Auth::guard("api")->user();
        if ($parttime == null){
            $msg = "not found";
        }else if (!$parttime->canOperate($user)){
            $msg = "permission denied";
        }else {
            $parttime->delete();
            $succ = true;
        }
        return $succ ? ['succ' => $succ] : ['succ' => $succ, 'msg' => $msg];
    }

    /**
     * @param $parttime_id
     * @return array withNothing
     */
    public static function cancelParttime($parttime_id, ParttimeUser $user = null){
        /** @var Parttime $parttime */
        $parttime = Parttime::query()->find($parttime_id);
        $succ = false;$msg = "";
        if ($user == null) $user = self::currentUser();
        if ($parttime == null){
            $msg = "not found";
        }else if (!$parttime->canOperate($user)){
            $msg = "permission denied";
        }else {
            $succ = true;
            $parttime->setCancelledBool(true);
            $parttime->save();
        }
        return $succ ? ['succ' => $succ] : ['succ' => $succ, 'msg' => $msg];
    }

    /**
     * @param $id
     * @return array withRecordId
     */
    public static function signParttime($id){
        /** @var Parttime $parttime */
        $parttime = Parttime::query()->find($id);
        $succ = false;$msg = "";$record = null;
        /** @var ParttimeUser $user */
        $user = self::currentUser();
        if ($parttime == null) {
            $msg = "not found";
        }else if($parttime->records()->where("user_id", $user->getId())->exists()) {
            $msg = "signed";
        }else if(!$parttime->canSign()){
            $msg = "failed";
        }else if($parttime->isOwner($user)){
            $msg = "owner";
        }else {
            $succ = true;
            $record = ParttimeRecord::createNew(
                $user->getId(), $parttime->getId(), ParttimeRecord::STATUS_SIGNED
            );
        }
        return $succ ?
            ['succ' => $succ, 'id' => $record->getId()] :
            ['succ' => $succ, 'msg' => $msg];
    }

    /**
     * @param $parttime_id
     * @param ParttimeUser|null $user
     * @return array withNothing
     */
    public static function checkParttime($parttime_id, ParttimeUser $user = null){
        /** @var Parttime $parttime */
        $parttime = Parttime::query()->find($parttime_id);
        $succ = false;$msg = "";
        if ($user == null) $user = self::currentUser();
        if ($parttime == null){
            $msg = "not found";
        }else if ((
            /** @var ParttimeRecord $record */
            $record = $parttime->records()->where("user_id", $user->getId())->first()
            ) == null){
            $msg = "unsign";
        }else if ($record->isCancelled()){
            $msg = "cancelled";
        }else if($record->isChecked()){
            $msg = "checked";
        }else if($parttime->isOwner($user)){
            $msg = "owner";
        }else if (!$parttime->canCheck()){
            $msg = "failed";
        }else{
            $succ = true;
            $record->setStatus(ParttimeRecord::STATUS_CHECKED);
            $record->save();
        }
        return $succ ? ['succ' => $succ] : ['succ' => $succ, "msg" => $msg];
    }

    /**
     * @param $parttime_id
     * @param ParttimeUser|null $user
     * @return array withNothing
     */
    public static function unsignParttime($parttime_id, ParttimeUser $user = null){
        /** @var Parttime $parttime */
        $parttime = Parttime::query()->find($parttime_id);
        $succ = false;$msg = "";
        if ($user == null) $user = self::currentUser();
        if ($parttime == null){
            $msg = "not found";
        }else if ((
                /** @var ParttimeRecord $record */
            $record = $parttime->records()->where("user_id", $user->getId())->first()
            ) == null){
            $msg = "unsign";
        }else if ($record->isCancelled()){
            $msg = "unsigned";
        }else if($record->isChecked()){
            $msg = "checked";
        }else if($parttime->isOwner($user)){
            $msg = "owner";
        }else if(!$parttime->canUnsign()){
            $msg = "failed";
        }else{
            $succ = true;
            $record->setStatus(ParttimeRecord::STATUS_CANCELLED);
            $record->save();
        }
        return $succ ? ['succ' => $succ] : ['succ' => $succ, "msg" => $msg];
    }
}