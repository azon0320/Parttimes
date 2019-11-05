<?php


namespace App\Http\Controllers\Parttime;


use App\Http\Controllers\Controller;
use App\Services\JsonProcess;
use App\Services\ParttimeDiskProcess;
use App\Services\ParttimeProcess;
use App\Services\ParttimeValidatorProcess;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParttimeController extends Controller
{
    use ParttimeProcess, ParttimeValidatorProcess, ParttimeDiskProcess, JsonProcess;

    public function __construct()
    {
        $this->middleware('auth:api')->except('view');
    }

    public function view(Request $request){
        $limit = $request->query("limit", 10);
        $limit = max($limit, 1);
        $page = $request->query('page', 1);
        $page = max($page, 1);
        return self::responseWithSuccess200(self::viewParttimes($limit, $page));
    }

    public function create(Request $request){
        $all = $request->all();
        # 检查图片数 (img_count) min:1 max:4
        # 检查图片文件是否完整 (遍历 N = 1 ~ img_count , imgName = img_N , N isset && instanceof File && size <= 256KB)
        # 检查地理位置合法性已经在Process做了检查
        # 基本Validator
        #  (img_count) min:1 max:4 integer required
        #  (title) min:4 max:16 required
        #  (from) date required
        #  (to) date required
        #  (location) string required
        #  (signuntil) date required
        # more 默认 空
        # max 默认 0
        $rules = [
            'img_count' => 'nullable|integer|between:1,4',
            'title' => 'required|string|between:4,16',
            # Date：strtotime()测试通过，格式是xxxx-xx-xx xx:xx
            'from' => 'required|date',
            'to' => 'required|date',
            'location' => 'required|string',
            'signuntil' => 'required|date',
            # 文件大小单位是kb
            'img_0' => 'required|image|max:256',
            'img_1' => ['image', 'max:256', isset($all['img_count']) && intval($all['img_count'] > 1) ? 'required' : 'nullable'],
            'img_2' => ['image', 'max:256', isset($all['img_count']) && intval($all['img_count'] > 2) ? 'required' : 'nullable'],
            'img_3' => ['image', 'max:256', isset($all['img_count']) && intval($all['img_count'] > 3) ? 'required' : 'nullable'],
            'max' => ['nullable', 'integer', 'between:0,100']
        ];
        $validator = Validator::make($all, $rules);
        if (!$validator->fails()){
            $img_count = intval($request->input('img_count', 1));
            /** @var File[] $imgFiles */
            $imgFiles = [];
            for ($i = 0; $i < $img_count; $i++) {
                $imgName = "img_$i";
                /** @var File $f */
                $f = $all[$imgName];
                $imgFiles[] = $f;
            }
            $returns = self::createParttime(
                $all['title'],
                Carbon::parse($all['from']), Carbon::parse($all['to']),
                $all['location'],
                Carbon::parse($all['signuntil']),
                $request->input('detail', ''),
                array_keys($imgFiles),
                intval($request->input('max', 0))
            );
            if ($returns['succ']) {
                self::saveParttimeImages($returns['id'], $imgFiles);
                return self::responseWithSuccess200($returns);
            }else{
                return self::responseWithError($returns);
            }
        }else{
            return self::responseWithErrorMessage("validator error", 500, $validator->failed());
        }
    }

    public function delete(Request $request){
        $validator = Validator::make($request->all('id'), ['id' => ['required', 'integer']]);
        if (!$validator->fails()){
            $id = intval($request->input('id'));
            $returns = self::deleteParttime($id);
            if ($returns['succ']){
                return self::responseWithSuccess200($returns);
            }else{
               return self::responseWithError($returns);
            }
        }else return self::responseWithErrorMessage('validator error', 500, $validator->failed());
    }

    public function cancel(Request $request){
        $validator = Validator::make($request->all('id'), ['id' => ['required', 'integer']]);
        return self::validateWithCallback($validator,
            function(\Illuminate\Validation\Validator $v) use($request) {
                $id = intval($request->input('id'));
                $returns = self::deleteParttime($id);
                if ($returns['succ']) {
                    return self::responseWithSuccess200($returns);
                } else {
                    return self::responseWithError($returns);
                }
            }
        );
    }

    public function sign(Request $request){
        return self::validateWithCallback(
            Validator::make($request->all('id'), ['id' => ['required', 'integer']]),
            function (\Illuminate\Validation\Validator $v) use($request){
                $id = intval($request->input('id'));
                $returns = self::cancelParttime($id);
                if ($returns['succ']) {
                    return self::responseWithSuccess200($returns);
                } else {
                    return self::responseWithError($returns);
                }
            }
        );
    }

    public function check(Request $request){
        return self::validateWithCallback(
            Validator::make($request->all('id'), ['id' => ['required', 'integer']]),
            function (\Illuminate\Validation\Validator $v) use($request){
                $id = intval($request->input('id'));
                $returns = self::checkParttime($id);
                if ($returns['succ']) {
                    return self::responseWithSuccess200($returns);
                } else {
                    return self::responseWithError($returns);
                }
            }
        );
    }

    public function unsign(Request $request){
        return self::validateWithCallback(
            Validator::make($request->all('id'), ['id' => ['required', 'integer']]),
            function (\Illuminate\Validation\Validator $v) use($request){
                $id = intval($request->input('id'));
                $returns = self::unsignParttime($id);
                if ($returns['succ']) {
                    return self::responseWithSuccess200($returns);
                } else {
                    return self::responseWithError($returns);
                }
            }
        );
    }
}