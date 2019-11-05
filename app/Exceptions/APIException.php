<?php


namespace App\Exceptions;

class APIException extends \Exception
{
    protected $apiCode;
    protected $apiMsg = "";
    protected $extras = [];
    protected $responseCode = 200;
    public function __construct($apiCode, $msg = "", $extras = [], $responseCode = 200, \Throwable $prev = null)
    {
        parent::__construct($msg, 0, $prev);
        $this->apiCode = $apiCode;
        $this->apiMsg = $msg;
        $this->extras = $extras;
        $this->responseCode = $responseCode;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Returns the renderable json
     * @return array
     */
    public function toJson(){
        $json = [
            'code' => $this->apiCode,
            'msg' => $this->apiMsg
        ];
        foreach ($this->extras as $key => $value){
            $json[$key] = $value;
        }
        if (config('app.debug', false)){
            foreach ($this->getTrace() as $key => $value){
                $json[$key] = $value;
            }
        }
        return $json;
    }
}