<?php


namespace App\Http\Controllers\Parttime;


use App\Http\Controllers\Controller;
use App\Services\ParttimeMessageProcess;
use App\Services\ParttimeValidatorProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParttimeMessageController extends Controller
{

    use ParttimeMessageProcess, ParttimeValidatorProcess;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function view(Request $request){
        return self::viewMessages(
            min(10, max(1, $request->query('limit', 10))),
            min(99, max(1, $request->query('page', 1)))
        );
    }

    public function read(Request $request){
        return self::validateWithCallback(
            Validator::make($request->all('msg_id'), [
                'msg_id' => ['required', 'numeric']
            ]),
            function(\Illuminate\Validation\Validator $v) use($request){
                return self::fastResponseBySucc(self::readMessage($request->input('msg_id')));
            }
        );
    }
}