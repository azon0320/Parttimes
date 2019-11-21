<?php


namespace App\Transformers;


use App\Models\Parttime;
use App\Models\ParttimeRecord;
use App\Models\ParttimeUser;

class ParttimeTransformer extends BaseTransformer
{

    public static function fastTransform($model, $user = null)
    {
        return (new static())->transformWithUser($model, $user);
    }

    public function transformWithUser($model, ParttimeUser $user = null){
        /**
         * @var Parttime $model
         * @var ParttimeRecord|null $recordObj
         */
        $record = ['status' => ParttimeRecord::STATUS_UNSIGN];
        if ($user != null){
            $recordObj = $user->records()->where('parttime_id', $model->getId())->first();
            if ($recordObj != null) $record['status'] = $recordObj->getStatus();
        }
        $this->json['record'] = $record;
        return $this->transform($model);
    }

    public function transform($model, array $excepts = [])
    {
        /** @var Parttime $model */
        # TODO 删除ID ，换用PID
        $this->json["id"] = $model->getId();
        $this->json["title"] = $model->getTitle();
        $this->json["timestart"] = $model->getTimeStart();
        $this->json["timeend"] = $model->getTimeEnd();
        $this->json["location"] = $model->getLocation();
        $this->json["location_str"] = $model->getLocationString();
        $this->json["deadline"] = $model->getDeadline();
        $this->json["detail"] = $model->getDetail();
        $this->json["img_count"] = count($model->getImgs());
        $this->json["cancelled"] = $model->isWasted();
        $this->json["currentsigners"] = $model->getCurrentSigner()->count();
        $this->json["limited"] = $model->getLimited();

        /** @var ParttimeUser $creator */
        $creator = $model->creator()->first();
        $this->json["creator_uid"] = $creator->getUId();
        $this->json["creator"] = $creator->getNickname();

        $this->json["created_at"] = $model->created_at->toDateTimeString();

        $this->json['status'] = $model->getStatus();

        # TODO DELETE
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