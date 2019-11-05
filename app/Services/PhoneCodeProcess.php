<?php


namespace App\Services;


use App\Models\PhoneCode;
use Carbon\Carbon;

trait PhoneCodeProcess
{
    public static function allocateVerifyCode($phone){
        $codeQuery = PhoneCode::query()->where('phone', $phone);
        $succ = true;$msg = "";
        if ($codeQuery->exists()){
            /** @var PhoneCode $codeModel */
            $codeModel = $codeQuery->first();
            if (Carbon::now()->isAfter(
                $codeModel->getUpdatedTime()->addMinutes(1)
            )){
                # regen
                $codeModel->setCode(self::generateCode());
                $codeModel->update();
                $msg = $codeModel->getCode();
            }else{
                $succ = false;
                $msg = "throttled";
            }
        }else{
            # gen
            $codeModel = PhoneCode::createNew($phone, self::generateCode());
            $codeModel->save();
            $msg = $codeModel->getCode();
        }
        return [
            'succ' => $succ, 'msg' => $msg
        ];
    }

    /**
     * @param $phone
     * @return PhoneCode|null
     */
    public static function getPhoneCode($phone){
        $model = PhoneCode::query()->where('phone', $phone);
        return $model->exists() ? $model->first() : null;
    }

    /** @return string */
    public static function generateCode(){
        return str_pad(mt_rand(1999, 9999), 4, "0");
    }
}