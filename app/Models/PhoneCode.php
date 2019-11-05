<?php


namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PhoneCode extends Model
{
    # 数据关系
    # 没有数据关系

    /**
     * @param string $phone 手机号
     * @param string $code  验证码
     * @return PhoneCode
     */
    public static function createNew(
        $phone,
        $code
    ){
        $phoneCode = new PhoneCode();
        $phoneCode->phone = $phone;
        $phoneCode->code = $code;
        $phoneCode->save();
        return $phoneCode;
    }

    # 基本属性
    /** @return string */
    public function getNumber(){
        return $this->number;
    }
    public function getPhone(){return $this->getNumber();}
    /** @return string */
    public function getCode(){
        return $this->code;
    }
    public function setCode($code){
        $this->code = $code;
    }
    /** @return string */
    public function getCreateTime(){
        return $this->created_at;
    }
    /** @return string */
    public function getUpdatedTime(){
        return $this->updated_at;
    }


    # 常用逻辑
    /** 是否过期? @return bool */
    public function isExpired(){
        return Carbon::now()->isAfter(Carbon::parse($this->getCreateTime())->addMinutes(1));
    }

    /**
     * @throws \Exception
     * @return string|null
     */
    public function onRead(){
        $code = $this->isExpired() ? null : $this->getCode();
        $this->delete();
        return $code;
    }

}