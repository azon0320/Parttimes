<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ParttimeMessage extends Model
{

    /**
     * @param integer $user_id ParttimeUser 用户Id
     * @param string $data 消息内容
     * @return ParttimeMessage
     */
    public static function createNew(
        $user_id,
        $data
    ){
        $parttimeMessage = new ParttimeMessage();
        $parttimeMessage->user_id = $user_id;
        $parttimeMessage->data = $data;
        $parttimeMessage->read = 0;
        $parttimeMessage->save();
        return $parttimeMessage;
    }

    # 数据关系
    public function user(){
        return $this->belongsTo(
            ParttimeUser::class,
            "user_id",
            "id"
        );
    }

    # 基本属性
    /** @return integer */
    public function getId(){
        return $this->id;
    }
    /** @return integer */
    public function getUserId(){
        return $this->user_id;
    }
    /** @return string */
    public function getData(){
        return $this->data;
    }
    /** @return bool */
    public function isRead(){
        return $this->read > 0;
    }

    # 数据动作
    public function onRead(){
        if ($this->read == 0) {
            $this->read = 1;
            $this->save();
        }
    }

    public function canOperate(ParttimeUser $user){
        return $this->getUserId() == $user->getId();
    }
}