<?php


namespace App\Services;


use App\Models\ParttimeMessage;
use App\Models\ParttimeUser;
use App\Transformers\ParttimeMessageTransformer;

trait ParttimeMessageProcess
{
    use ParttimeAuthProcess;

    /**
     * Need Auth
     * @param int $limit
     * @param int $page
     * @param ParttimeUser|null $user
     * @return array
     */
    public static function viewMessages($limit = 10, $page = 1, ParttimeUser $user = null){
        if ($user == null) $user = self::currentUser();
        $views = ParttimeMessage::query()
            ->where("user_id", $user->getId())
            ->get()->map(function(ParttimeMessage $msg){
                return ParttimeMessageTransformer::fastTransform($msg)
                    ->filterExcepts([]);
            });
        return $views;
    }

    public static function readMessage($msg_id, ParttimeUser $user = null){
        if ($user == null) $user = self::currentUser();
        /** @var ParttimeMessage $message */
        $message = ParttimeMessage::query()->find($msg_id);
        $succ = false;$msg = "";
        if ($message == null){
            $msg = "not found";
        }else if (!$message->canOperate($user)){
            $msg = "permission denied";
        }else {
            $succ = true;
            $msg = $message->getData();
            $message->onRead();
        }
        return ['succ' => $succ, 'msg' => $msg];
    }
}