<?php


namespace App\Transformers;


abstract class BaseTransformer
{

    /**
     * @param $model
     * @return BaseTransformer
     */
    public static function fastTransform($model){
        return (new static())->transform($model);
    }

    protected $json = [];

    public abstract function transform($model, array $excepts = []);

    public function filterExcepts(array $excepts = []){
        $this->json = array_filter($this->json, function($value, $key) use($excepts){
            return !in_array($key, $excepts);
        }, ARRAY_FILTER_USE_BOTH);
        return $this;
    }

    public function toArray(){
        return $this->json;
    }
}