<?php


namespace App\Transformers;


use App\Models\Parttime;

class ParttimeTransformer extends BaseTransformer
{
    public function transform($model, array $excepts = [])
    {
        /** @var Parttime $model */
        $this->json["id"] = $model->getId();
        $this->json["title"] = $model->getTitle();
        $this->json["timestart"] = $model->getTimeStart();
        $this->json["timeend"] = $model->getTimeEnd();
        $this->json["location"] = $model->getLocation();
        $this->json["deadline"] = $model->getDeadline();
        $this->json["detail"] = $model->getDetail();
        $this->json["img_count"] = count($model->getImgs());
        $this->json["cancelled"] = $model->isWasted();
        $this->json["limited"] = $model->getLimited();
        $this->json["currentsigners"] = $model->getCurrentSigner()->count();
        $this->json["creator_id"] = $model->getCreatorId();

        $this->json["created_at"] = $model->created_at->toDateTimeString();

        $this->json["outdated"] = $model->isOutdated();
        $this->json["outsigned"] = $model->isOutSigned();
        $this->json["started"] = $model->isStarted();
        $this->json["ends"] = $model->isEnds();
        $this->json["cansign"] = $model->canSign();
        $this->json["cancheck"] = $model->canCheck();
        $this->json["canwaste"] = $model->canWasted();
        return $this;
    }
}