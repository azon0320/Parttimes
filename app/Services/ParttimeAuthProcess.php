<?php


namespace App\Services;


use App\Models\ParttimeUser;
use Illuminate\Support\Facades\Auth;

trait ParttimeAuthProcess
{
    /** @return ParttimeUser */
    public static function currentUser(){
        return Auth::guard("api")->user();
    }
}