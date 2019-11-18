<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ParttimeUser extends Model
{

    const DEFAULT_CREDIT = 500;

    /**
     * @param string $phone 手机号
     * @param string $hashedPassword Hash::make加密后的密码
     * @param string $uid 随机生成的用户UID
     * @param string $token 64长度令牌
     * @param int $credit 信用分，默认500
     * @param string $nickName 昵称，不设置就是手机号
     * @return ParttimeUser
     */
    public static function createNew(
        $phone,
        $uid,
        $token,
        $hashedPassword = null,
        $credit = self::DEFAULT_CREDIT,
        $nickName = ''
    ){
        $parttimeUser = new ParttimeUser();
        $parttimeUser->phone = $phone;
        $parttimeUser->password = $hashedPassword;
        $parttimeUser->uid = $uid;
        $parttimeUser->token = $token;
        $parttimeUser->credit = $credit;
        $parttimeUser->nickname = $nickName == '' ? $phone : $nickName;
        $parttimeUser->save();
        return $parttimeUser;
    }

    # 数据关系
    public function parttimes(){
        return $this->hasMany(
            Parttime::class,
            "creator_id",
            "id"
        );
    }

    public function records(){
        return $this->hasMany(
            ParttimeRecord::class,
            "user_id",
            "id"
        );
    }

    public function messages(){
        return $this->hasMany(
            ParttimeMessage::class,
            "user_id",
            "id"
        );
    }


    # 基本属性
    /** @return integer */
    public function getId(){
        return $this->id;
    }
    /** @return string */
    public function getPhone(){
        return $this->phone;
    }
    /** @return string */
    public function getUId(){
        return $this->uid;
    }
    /** @return integer */
    public function getCredit(){
        return $this->credit;
    }
    /** @return string */
    public function getPasswordHashed(){
        return $this->password;
    }
    /** @return string */
    public function getNickname(){
        return $this->nickname;
    }
    /** @return string */
    public function getToken(){
        return $this->token;
    }
}