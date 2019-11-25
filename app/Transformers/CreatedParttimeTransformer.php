<?php


namespace App\Transformers;


use App\Models\Parttime;

class CreatedParttimeTransformer extends BaseTransformer
{
    public function transform($model, array $excepts = [])
    {
        /** @var Parttime $model */
        $this->json['id'] = $model->getId();
        $this->json['location'] = $model->getLocation();
        $this->json['limited'] = $model->getLimited();
        $this->json['timestart'] = $model->getTimeStart();
        $this->json['timeend'] = $model->getTimeEnd();
        $this->json['currentsigners'] = $model->getCurrentSigner()->count();
        $this->json['title'] = $model->getTitle();
        $this->json['status'] = $model->getStatus();

        return $this;
    }
}