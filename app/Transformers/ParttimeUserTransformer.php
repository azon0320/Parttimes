<?php


namespace App\Transformers;


use App\Models\Parttime;
use App\Models\ParttimeRecord;
use App\Models\ParttimeUser;
use Carbon\Carbon;

class ParttimeUserTransformer extends BaseTransformer
{
    public function transform($model, array $excepts = [])
    {
        /** @var ParttimeUser $model */
        $this->json["id"] = $model->getId();
        $this->json["phone"] = $model->getPhone();
        $this->json["uid"] = $model->getUId();
        $this->json["credit"] = $model->getCredit();
        $this->json["password"] = $model->getPasswordHashed();
        $this->json["nickname"] = $model->getNickname();
        $this->json["token"] = $model->getToken();

        $this->json["parttimes"] = $model->parttimes()->get()->map(function(Parttime $parttime) use($model){
            return [
                'id' => $parttime->getId(),
                'status' => $parttime->records()->where('user_id', $model->getId())->first()->getStatus()
            ];
        });

        return $this;
    }
}