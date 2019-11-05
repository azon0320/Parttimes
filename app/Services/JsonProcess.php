<?php


namespace App\Services;


trait JsonProcess
{
    public static function responseWithSuccess200(array $data = [], $delKeys = [], $headers = []){
        $data = array_filter($data, function($v, $k) use ($delKeys){return !in_array($k, $delKeys);},ARRAY_FILTER_USE_BOTH);
        return response()->json($data, 200, $headers);
    }

    public static function responseWithError(array $extras = [], $statusCode = 500, $headers = []){
        return response()->json($extras, $statusCode, $headers);
    }

    public static function responseWithErrorMessage($errmsg, $statusCode = 500, $extras = [], $headers = []){
        return response()->json(array_merge(['msg' => $errmsg], $extras), $statusCode, $headers);
    }

    public static function fastResponseBySucc(array $dat, $defaultSucc = true){
        if (!isset($dat['succ'])) $dat['succ'] = $defaultSucc;
        if (boolval($dat['succ'])) {
            return self::responseWithSuccess200($dat);
        }else return self::responseWithError($dat);
    }
}