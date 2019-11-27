<?php


namespace App\Transformers;


use App\Models\Parttime;
use App\Models\ParttimeRecord;
use App\Models\ParttimeUser;

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

        $this->json["signeds"] = $model->records()->get()->map(function(ParttimeRecord $record) use($model){
            return [
                'id' => $record->getParttimeId(),
                'status' => $record->getStatus()
            ];
        });

        $this->json["createds"] = $model->parttimes()->get()->map(function(Parttime $parttime){
            return [
                'id' => $parttime->getId(),
                'status' => $parttime->getStatus()
            ];
        });

        return $this;
    }

    public function filterExcepts(array $excepts = [])
    {
        return parent::filterExcepts($excepts);
    }
}