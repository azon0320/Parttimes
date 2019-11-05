<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ParttimeRecord extends Model
{
    const STATUS_UNKNOWN = 0;
    const STATUS_SIGNED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_CHECKED = 4;

    /**
     * @param integer $user_id 用户ID
     * @param integer $parttime_id ParttimeID
     * @param int $status 状态(已报名，已签到，已取消)
     * @return ParttimeRecord
     */
    public static function createNew(
        $user_id,
        $parttime_id,
        $status = self::STATUS_CHECKED
    ){
        $parttimeRecord = new ParttimeRecord();
        $parttimeRecord->user_id = $user_id;
        $parttimeRecord->parttime_id = $parttime_id;
        $parttimeRecord->status = $status;
        $parttimeRecord->save();
        return $parttimeRecord;
    }

    # 数据关系
    public function parttime(){
        return $this->belongsTo(
            Parttime::class,
            "parttime_id",
            "id"
        );
    }

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
    /** @return integer */
    public function getParttimeId(){
        return $this->parttime_id;
    }
    /** @return integer */
    public function getStatus(){
        return $this->status;
    }
    public function setStatus($int){
        $this->status = $int;
        return $this;
    }


    # 常用逻辑
    public function isSigned(){
        return $this->getStatus() == self::STATUS_SIGNED
            || $this->getStatus() == self::STATUS_CHECKED;
    }

    public function isChecked(){
        return $this->getStatus() == self::STATUS_CHECKED;
    }

    public function isCancelled(){
        return $this->getStatus() == self::STATUS_CANCELLED;
    }

    public function canOperate(ParttimeUser $user){
        return $this->getUserId() == $user->getId();
    }
}