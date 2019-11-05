<?php


namespace App\Services;

# 这个Pattern类是Laravel自带
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Mockery\Matcher\Pattern;

trait ParttimeValidatorProcess
{
    /**
     * @param $datestr
     * @return Carbon|null
     */
    public static function getDateFromString($datestr){
        # 规则
        # ^开头 $结尾 \d匹配数字 {4}限制一定要4个字 {1,2}必须为1或2个数字
        # xxxx-xx-xx xx:xx:xx
        $pattern = new Pattern('/^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$/');
        $dateobj = null;
        if ($pattern->match($datestr)){
            $dateobj = Carbon::parse($datestr);
            $dateobj = $dateobj->isValid() ? $dateobj : null;
        }
        return $dateobj;
    }

    /**
     * @param string $regex
     * @param string $input
     * @return bool
     */
    public static function validateRegex($regex, $input){
        return preg_match($regex, $input) >= 1;
    }

    /**
     * @param Validator $validator
     * @param \Closure $onSuccess
     * @param \Closure|string $onFail
     * @return mixed|null
     */
    public static function validateWithCallback(Validator $validator, \Closure $onSuccess, $onFail = "validator error"){
        if (!$validator->fails()){
            return $onSuccess($validator);
        }else if ($onFail != null){
            if ($onFail instanceof \Closure){
                return $onFail($validator);
            }else {
                return JsonProcess::responseWithErrorMessage(strval($onFail),500,$validator->failed());
            }
        }
        return null;
    }
}