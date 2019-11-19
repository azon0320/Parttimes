<?php


namespace App\Http\Controllers\Parttime;


use App\Http\Controllers\Controller;
use App\Services\ParttimeAuthProcess;
use App\Services\ParttimeRecordProcess;
use Illuminate\Http\Request;

class ParttimeRecordController extends Controller
{

    use ParttimeAuthProcess, ParttimeRecordProcess;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function view(Request $request){
        $limit = intval($request->query('limit', 5));
        $page = intval($request->query('page', 1));
        return self::viewRecords($limit, $page, null);
    }
}