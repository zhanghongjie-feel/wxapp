<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\model\Video;
class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Video::get();
        return view('qiniu_source',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name=$_POST['name'];
        $password=$_POST['password'];
//        $name=$request->input('name');
//        $password=$request->input('password');
        if(empty($name) || empty($password)){
            return json_encode(['ret'=>0,'msg'=>'参数不能为空']);
        }
        if(!empty($request->hasFile('file')) && request()->file('file')->isValid()){
            $path=request()->file('file')->store('api');
//            dd('/storage/'.$path);
        }else{
            echo '上传失败';
        }
        $res=\DB::connection('test')->table('test')->insert([
            'name'=>$name,
            'password'=>$password,
            'upload'=>'/storage/'.$path
        ]);

        if($res){
            return json_encode(['ret'=>1,'msg'=>'添加成功']);
        }else{
            return json_encode(['ret'=>0,'msg'=>'添加失败']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=\DB::connection('test')->table('test')->where(['test_id'=>$id])->first();
        return json_encode($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $name=request('name');
        $pwd=$request->input('pwd');
        if(empty($id) || empty($name) || empty($pwd)){
            return json_encode(['ret'=>0,'msg'=>'api异常，修改失败']);die;
        }
        $res=\DB::connection('test')->table('test')->where(['test_id'=>$id])->update([
            'name'=>$name,
            'password'=>$pwd
        ]);
        if($res){
            return json_encode(['ret'=>1,'msg'=>'修改成功']);
        }else{
            return json_encode(['ret'=>0,'msg'=>'修改失败']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data=\DB::connection('test')->table('test')->where(['test_id'=>$id])->delete();
        if($data){
            return json_encode(['ret'=>1,'msg'=>'删除成功']);
//            return redirect('api/show_view');
        }else{
            echo 'fail';
        }
    }
}
