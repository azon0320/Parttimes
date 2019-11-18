<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Parttime extends Model
{

    const LIMITED_NOLIMIT = 0;


    const STATUS_ENDED = 1;
    const STATUS_CANCELLED = 2;

    const STATUS_SIGNING = 3;
    const STATUS_STARTING = 4;



    /**
     * 创建一个 Parttime
     * 不作任何合法性检查
     * Id 自增
     * @param string $title 标题
     * @param Carbon $from 开始时间
     * @param Carbon $to 结束时间
     * @param string $location 坐标，格式 x,y
     * @param Carbon $deadline 报名截止日期
     * @param integer $creator_id 发布者Id
     * @param string $detail 详细内容
     * @param array $img 配图，图片名字
     * @param int $limited 人数限制
     * @return Parttime
     */
    public static function createNew(
        $title,
        Carbon $from,
        Carbon $to,
        $location,
        Carbon $deadline,
        $creator_id,
        $detail = "",
        array $img = [],
        $limited = self::LIMITED_NOLIMIT
    ){
        $parttime = new Parttime();
        $parttime->title = $title;
        $parttime->setStartEndTime($from, $to);
        $parttime->location = $location;
        $parttime->deadline = $deadline;
        $parttime->detail = $detail;
        $parttime->setImgs($img);
        $parttime->setCancelledBool(false);
        $parttime->limited = $limited;
        $parttime->creator_id = $creator_id;
        $parttime->save();
        return $parttime;
    }


    # 数据关系
    public function records(){
        return $this->hasMany(
            ParttimeRecord::class,
            "parttime_id",
            "id"
        );
    }
    public function creator(){
        return $this->belongsTo(
            ParttimeUser::class,
            "creator_id",
            "id"
        );
    }

    # 基本属性
    /** @return integer */
    public function getId(){
        return $this->id;
    }
    /** @return string */
    public function getTitle(){
        return $this->title;
    }
    /** @return string */
    public function getTimeStart(){
        return $this->timestart;
    }
    /** @return string */
    public function getTimeEnd(){
        return $this->timeend;
    }
    public function setStartEndTime(Carbon $from, Carbon $to){
        $this->timestart = $from;
        $this->timeend = $to;
    }
    /** @return string */
    public function getLocation(){
        return $this->location;
    }
    /** @return string */
    public function getDeadline(){return $this->deadline;}
    /** @return string */
    public function getDetail(){return $this->detail;}
    /** @return string[] */
    public function getImgs(){return explode(";",$this->img);}
    public function setImgs(array $imgs){$this->img = implode(",", $imgs);}
    /** @return integer */
    public function getCancelledInt(){return $this->cancelled;}
    public function getCancelledBool(){return $this->getCancelledInt() == 1;}
    public function setCancelledBool(bool $flag){$this->cancelled = $flag ? 1 : 0;}
    /** @return integer */
    public function getLimited(){return $this->limited;}
    /** @return integer */
    public function getCreatorId(){return $this->creator_id;}


    # 基本逻辑
    public function isWasted(){
        return $this->getCancelledBool();
    }

    public function isOutdated(){
        return Carbon::now()->isAfter(Carbon::parse($this->getTimeEnd()));
    }

    public function isOutSigned(){
        return Carbon::now()->isAfter(Carbon::parse($this->getDeadline()));
    }

    public function isStarted(){
        return Carbon::now()->isAfter(Carbon::parse($this->getTimeStart()));
    }

    public function getCurrentSigner(){return $this->records();}

    public function reachLimited(){
        return $this->getLimited() != 0 && $this->getCurrentSigner()->count() >= $this->getLimited();
    }



    # 常用逻辑
    public function getStatus(){
        if ($this->isOutdated()){
            return self::STATUS_ENDED;
        }else if ($this->getCancelledBool()){
            return self::STATUS_CANCELLED;
        }else if ($this->isStarted()){
            return self::STATUS_STARTING;
        }else {
            return self::STATUS_SIGNING;
        }
    }

    /** 是否结束? @return bool */
    public function isEnds(){
        return $this->isWasted() || $this->isOutdated();
    }

    /** 能否报名? @return bool */
    public function canSign(){
        return !$this->isEnds() && !$this->isOutSigned() && !$this->reachLimited();
    }

    /** 能否签到? @return bool */
    public function canCheck(){
        return $this->isStarted() && !$this->isEnds();
    }

    /** 能否取消报名? @return bool */
    public function canUnsign(){
        return !$this->isStarted() && !$this->isEnds();
    }

    /** 能否作废? @return bool */
    public function canWasted(){
        return !$this->isWasted() && !$this->isOutdated();
    }

    public function canOperate(ParttimeUser $user){
        return $this->getCreatorId() == $user->getId();
    }

    public function isOwner(ParttimeUser $user){
        return $this->getCreatorId() == $user->getId();
    }
}