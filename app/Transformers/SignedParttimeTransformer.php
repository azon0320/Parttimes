<?php


namespace App\Transformers;

use App\Models\Parttime;
use App\Models\ParttimeRecord;

class SignedParttimeTransformer extends CreatedParttimeTransformer
{

    public static function fastTransformWithRecord(Parttime $model, ParttimeRecord $record)
    {
        return (new SignedParttimeTransformer())->transform($model, [], $record);
    }

    public function transform($model, array $excepts = [], $record = null)
    {
        parent::transform($model, $excepts);

        $this->json['record_status'] = ParttimeRecord::STATUS_UNSIGN;

        if ($record != null){
            /**
             * @var ParttimeRecord $record
             */
            $this->json['record_status'] = $record->getStatus();
        }

        return $this;
    }
}