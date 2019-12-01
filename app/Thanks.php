<?php


namespace App;


use App\Services\ParttimeDiskProcess;

interface Thanks
{

    /*
     * 这里是项目的特别感谢及快速安装的说明
     */
    const CREATE_COMMAND = "composer create-project laravel/laravel --prefer-dist";


    # 该项目用到以下开源库
    /**
     * Laravel 基本
     */
    const LIB_LARAVEL = "laravel/laravel";

    /**
     * Image 图片处理
     * 自动注册 ServiceProvider：/vendor/intervention/image/provides.json
     * @see ParttimeDiskProcess
     */
    const LIB_IMAGE = "intervention/image";

    /**
     * CORS 跨域处理
     * 自动注册 ServiceProvider: /vendor/barryvdh/laravel-cors/src/ServiceProvider
     */
    const LIB_CORS = "barryvdh/laravel-cors";
}