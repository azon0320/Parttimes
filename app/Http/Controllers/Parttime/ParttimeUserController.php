<?php


namespace App\Http\Controllers\Parttime;


use App\Http\Controllers\Controller;
use App\Services\JsonProcess;
use App\Services\ParttimeUserProcess;
use App\Services\ParttimeValidatorProcess;
use App\Services\PhoneCodeProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParttimeUserController extends Controller
{

    use PhoneCodeProcess, ParttimeUserProcess, ParttimeValidatorProcess, JsonProcess;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['view', 'register', 'login']);
    }

    public function view(Request $request){
        return self::validateWithCallback(
            Validator::make(
                ['uid' => $request->query('uid')],
                [
                    'uid' => ['required', 'numeric']
                ]),
            function(\Illuminate\Validation\Validator $v) use($request){
                $uid = $request->query('uid');
                $results = self::viewUser($uid);
                return self::fastResponseBySucc($results);
            }
        );
    }

    public function viewself(Request $request){
        return self::fastResponseBySucc(self::viewSelfUser());
    }

    public function viewCreateds(Request $request){
        return self::viewCreatedParttimes();
    }

    public function viewSigneds(Request $request){
        return self::viewSignedParttimes();
    }

    public function config(Request $request){
        return self::validateWithCallback(
            Validator::make($request->all(['nickname', 'avatar']),
                [
                    'nickname' => ['nullable', 'string', 'min:4', 'max:10'],
                    'avatar' => ['nullable', 'image', 'max:256']
                ]
            ),
            function(\Illuminate\Validation\Validator $v) use($request){
                $modified = [];
                if (($nickname = $request->input('nickname')) != null){
                    $modified['nickname'] = strval($nickname);
                }
                if (($avatarFile = $request->file('avatar')) != null){
                    $modified['avatar'] = $avatarFile;
                }
                return self::fastResponseBySucc(self::configUser($modified));
            }
        );
    }

    public function register(Request $request){

        if (($logged = self::controllerCheckLogged()) != null) return $logged;

        return self::validateWithCallback(
            Validator::make($request->all(['phone', 'password', 'verified_code']),
                [
                    'phone' => ['required', 'string', 'size:11'],
                    'password' => ['nullable', 'string', 'between:6,20'],
                    'verified_code' => ['required', 'string', 'size:4']
                ]
            ),
            function(\Illuminate\Validation\Validator $v) use($request){
                $phone = $request->input('phone');
                if (is_numeric($phone)) {
                    $inputCode = $request->input('verified_code');
                    $password = $request->input('password', null);
                    return self::fastResponseBySucc(self::registerUser($phone, $inputCode, $password));
                }else return self::responseWithErrorMessage('phone error');
            }
        );
    }

    public function login(Request $request){

        if (($logged = self::controllerCheckLogged()) != null) return $logged;

        return self::validateWithCallback(
            Validator::make($request->all(['phone', 'password', 'verified_code']),
                [
                    'phone' => ['required', 'string', 'size:11'],
                    'verified_code' => ['nullable', 'string', 'size:4'],
                    'password' => [
                        $request->input('verified_code') == null ? 'required' : 'nullable',
                        'string', ''
                        #TODO 增加密码 Hashed 长度限制
                    ]
                ]
            ),
            function(\Illuminate\Validation\Validator $v) use($request){
                return self::fastResponseBySucc(self::loginUser(
                    $request->input('phone'),
                    array_filter(
                        [
                            'verified_code' => $request->input('verified_code'),
                            'password' => $request->input('password', null)
                        ],
                        function($value){return $value != null;}
                    )
                ));
            }
        );
    }


    public static function controllerCheckLogged(){
        return self::checkLogged() ? ['succ' => true, 'msg' => 'logged'] : null;
    }
}