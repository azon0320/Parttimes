<?php


namespace App\Transformers;


use App\Models\ParttimeMessage;

class ParttimeMessageTransformer extends BaseTransformer
{
    public function transform($model, array $excepts = [])
    {
        /** @var ParttimeMessage $model */
        $this->json['id'] = $model->getId();
        $this->json['data'] = $model->getData();

        $this->json['created_at'] = $model->created_at->toDateTimeString();

        return $this;
    }
}