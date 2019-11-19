<?php


namespace App\Transformers;


use App\Models\ParttimeRecord;

class ParttimeRecordTransformer extends BaseTransformer
{
    public function transform($model, array $excepts = [])
    {
        /** @var ParttimeRecord $model */
        $this->json['parttime_id'] = $model->getParttimeId();
        $this->json['status'] = $model->getStatus();
        $this->json['created_at'] = $model->created_at->toDateTimeString();
        $this->json['updated_at'] = $model->updated_at->toDateTimeString();
        return $this;
    }
}