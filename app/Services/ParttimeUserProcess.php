<?php


namespace App\Services;


use App\Models\Parttime;
use App\Models\ParttimeRecord;
use App\Models\ParttimeUser;
use App\Transformers\ParttimeTransformer;
use App\Transformers\ParttimeUserTransformer;
use App\Transformers\CreatedParttimeTransformer;
use App\Transformers\SignedParttimeTransformer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait ParttimeUserProcess
{
    use ParttimeAuthProcess, ParttimeDiskProcess;

    # 游客视角，指定用户的公开信息
    public static function viewUser($uid, $filter = [
        "id", "phone", "credit", "password", "token", "signeds"
    ]){
        $query = ParttimeUser::query()->where("uid", $uid);
        $succ = false;$msg = "";$data = [];
        if (!$query->exists()){
            $msg = "not found";
        }else{
            /** @var ParttimeUser $target */
            $target = $query->first();
            $succ = true;
            $data = ParttimeUserTransformer::fastTransform($target);
            $data->filterExcepts($filter);
        }
        return $succ ? ['succ' => $succ, 'data' => $data->toArray()]
            : ['succ' => $succ, 'msg' => $msg];
    }


    # 指定用户的私人信息
    public static function viewPersonalUser($uid){
        return self::viewUser($uid, ['id', 'password']);
    }

    # 已登录用户自己的私人信息
    public static function viewSelfUser(){
        return self::viewPersonalUser(self::currentUser()->getUId());
    }

    public static function viewCreatedParttimes(ParttimeUser $user = null){
        if ($user == null) $user = self::currentUser();
        return collect($user->parttimes()->get())->map(function(Parttime $parttime){
            return CreatedParttimeTransformer::fastTransform($parttime)->toArray();
        })->sortByDesc(function($json){
            return $json['status'];
        })->values()->all();
    }

    public static function viewSignedParttimes(ParttimeUser $user = null){
        if ($user == null) $user = self::currentUser();
        return collect($user->records()->get())->map(function(ParttimeRecord $record){
            /** @var Parttime $parttime */
            $parttime = $record->parttime()->first();
            return SignedParttimeTransformer::fastTransformWithRecord($parttime, $record)->toArray();
        })->sortByDesc(function($json){
            return $json['record_status'];
        })->values()->all();
    }

    public static function configUser(
        array $modifies, ParttimeUser $user = null
    ){
        $user = $user == null ? self::currentUser() : $user;
        $succ = false;$msg = "";
        $successes = [];
        if ($user == null){
            $msg = "not found";
        }else{
            if (isset($modifies['avatar'])){
                self::setUserAvatar($modifies['avatar']);
                $successes[] = 'avatar';
            }
            if (isset($modifies['nickname'])){
                $nickname = $modifies['nickname'];
                $user->nickname = $nickname;
                $successes[] = 'nickname';
            }
            # Add After
            $succ = true;
            $user->save();
        }
        return [
            'succ' => $succ, 'msg' => $succ ? implode(',', $successes) : $msg
        ];
    }

    # withNothing
    public static function setUserAvatar(UploadedFile $f, ParttimeUser $user = null){
        $uid = $user == null ? self::currentUser()->getUId() : $user->getUId();
        self::saveAvatarImages($uid, $f);
    }

    public static function registerUser(
        $phone, $verifyCode, $passwordUnhashed = null
    )
    {
        $succ = false;
        $msg = "";
        # 是否登录
        # 检查验证码
        # 手机号是否已注册
        # 密码强度检查都放在控制器中执行
        if (!self::verifyPhoneCode($phone, $verifyCode)) {
            $msg = "code error";
        } else if (ParttimeUser::query()->where("phone", $phone)->exists()) {
            $msg = "registered";
        } else {
            $user = ParttimeUser::createNew(
                $phone,
                self::generateUID(), self::generateToken(),
                Hash::make($passwordUnhashed)
            );
            $succ = $user != null;
            if ($succ) $msg = $user->getToken();
        }
        return ['succ' => $succ, 'msg' => $msg, 'token' => $succ ? $msg : ""];
    }

    public static function loginUser($phone, array $securities){
        $succ = false;$msg = "";$userObj = null;
        if (!ParttimeUser::query()->where("phone", $phone)->exists()){
            $msg = "param error";
        }else {
            if (isset($securities['verified_code'])){
                $succ = self::verifyPhoneCode($phone, $securities['verified_code']);
                if (!$succ) {
                    $msg = "code error";
                }else {
                    $userObj = ParttimeUser::query()->where("phone", $phone)->first();
                }
            }else{
                $userObj = self::verifyUserByPassword($phone, $securities['password']);
                if ($userObj == null) $msg = "param error";
            }
        }
        $succ = $userObj != null;
        return $succ ? ['succ' => $succ, 'token' => $userObj->getToken()] : ['succ' => $succ, 'msg' => $msg];
    }

    public static function verifyUserByPassword($phone, $hashed){
        /** @var ParttimeUser $user */
        $user = ParttimeUser::query()->where('phone', $phone)->first();
        return $user->getPasswordHashed() != null && strval($user->getPasswordHashed()) === strval($hashed) ? $user : null;
    }

    public static function verifyPhoneCode($phone, $givenCode){
        $dbCode = PhoneCodeProcess::getPhoneCode($phone);
        if ($dbCode != null){
            $coderaw = $dbCode->onRead();
            if ($coderaw != null && strval($coderaw) == strval($givenCode)) return true;
        }
        return false;
    }

    public static function checkLogged(){
        return self::currentUser() != null;
    }

    public static function generateToken(){
        $code = '';
        while (
            $code == '' ||
            ParttimeUser::query()->where("token", $code)->exists()
        ){
            $code = Str::random(64);
        }
        return $code;
    }

    public static function generateUID(){
        $code = '';
        while (
            $code == '' ||
            ParttimeUser::query()->where("uid", $code)->exists()
        ){
            $code = mt_rand(100000, 999999999999);
        }
        return $code;
    }
}