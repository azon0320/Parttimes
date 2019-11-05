<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('msg', function(){
    $message = collect([
        '吃饭了吗？',
        '今天搞项目还是玩MC？',
        "感冒好了没有",
        "招聘会很快就要来了哦!"
    ])->random();

    \App\Models\ParttimeMessage::createNew(
        2, $message
    );

    $this->comment("发送信息成功");
});