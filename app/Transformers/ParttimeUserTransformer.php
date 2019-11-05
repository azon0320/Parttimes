<?php


namespace App\Transformers;


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

        $this->json["records"] = $model->records()->count();
        $this->json["records_checked"] = $model->records()->where("status", ParttimeRecord::STATUS_CHECKED)->count();
        $this->json["parttimes"] = $model->parttimes()->count();
        $this->json["parttimes_passed"] = $model->parttimes()
            ->where("cancelled", 0)
            ->where("timeend", ">", Carbon::now())
        ->count();

        return $this;
    }
}