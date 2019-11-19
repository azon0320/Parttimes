<?php


namespace App\Services;


use App\Models\ParttimeRecord;
use App\Models\ParttimeUser;
use App\Transformers\ParttimeRecordTransformer;

trait ParttimeRecordProcess
{
    use ParttimeAuthProcess;

    public static function viewRecords($limit = 5, $page = 1, ParttimeUser $user = null){
        if ($user == null) $user = self::currentUser();
        $query = $user->records()->orderBy('status');
        return $query->get()->forPage($limit, $page)->map(function (ParttimeRecord $record){
            return ParttimeRecordTransformer::fastTransform($record)->toArray();
        })->sortByDesc(function($value, $key){
            return $value['status'];
        })->all();
    }
}