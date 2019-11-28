<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Tools\Tools;
use App\model\login;
use App\model\swiper;
use App\model\Video;
class AdminController extends Controller
{
    public $tools;
    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }


    /**
     * 这是点击轮播图跳转  播放视频
     */
    public function click_swiper(Request $request)
    {
        $id = $request->input('id');
        $path = Video::where('v_id',$id)->get();
        echo json_encode($path);
    }

    /**
     *
     * 小程序轮播图
     */
    public function swiper()
    {
        $data = swiper::get();
       echo json_encode($data);

    }
    /**
     * @param Request $request
     * 小程序搜索视频
     */
    public function search(Request $request)
    {
        $data = $request->input('data');
        $where = [];
        if($data){
            $where[] = ['search','like',"%$data%"];
        }
        $res = Video::where($where)->get();
        echo json_encode($res);
    }

    /***
     * guzzle传输文件
     */
    public function guzzle_upload($url,$path,$client,$is_video=0,$title='',$desc=''){
        $multipart=   [
            [
                'name'     => 'media',
                'contents' => fopen($path, 'r')//打开这个path找到本地资源（打开文件）
            ],
        ];
        if($is_video==1){
            $multipart[]=[
                'name'=> 'description',
                'contents'=>json_encode(['title'=>$title,'introduction'=>$desc],JSON_UNESCAPED_UNICODE)
            ];
        }
//        dd($multipart);
        $result=$client->request('POST',$url,[
            'multipart'=>$multipart
        ]);
//        dd($result);
        return $result->getBody();
    }



    public function do_upload(Request $request,Client $client)
    {
        $type = $request->all()['type'];
        $source_type = '';
        switch ($type) {
            case 1;
                $source_type = 'image';
                break;
            case 2;
                $source_type = 'voice';
                break;
            case 3;
                $source_type = 'video';
                break;
            case 4;
                $source_type = 'thumb';
                break;
            default;
        }

        $name = 'file_name';
        if (!empty(request()->hasFile($name)) && request()->file($name)->isValid()) {
            //大小 资源类型
            $ext = $request->file($name)->getClientOriginalExtension();//弄出文件类型
            $size = $request->file($name)->getClientSize() / 1024 / 1024;

            if ($source_type == 'video') {
                if (!in_array($ext, ['mp4'])) {
                    dd('非视频格式');
                }
                if ($size > 10) {
                    dd('视频过大');
                }

                //$local_path=request()->file($name)->store('wechat/'.$source_type);//存入本地storage
//            dd($local_path);
                $file_name = time() . rand(100000, 999999) . '.' . $ext;//随便用rand函数生成一个名字
                $path = request()->file($name)->storeAs('wechat/' . $source_type, $file_name);//storeAs,文件上传时，修改上传的文件名
            dd($path);
                $_path = '/storage/' . $path;
//            dd($_path);
                $path = realpath('./storage/' . $path);//realpath() 函数返回绝对路径。  这个可以直接在地址栏上输入绝对地址，直接在本地出来
//            dd($path);
                //新增临时素材接口
                //$url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->get_wechat_access_token().'&type='.$source_type;//新增临时素材。
                //新增其他类型永久素材
                $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $this->get_wechat_access_token() . '&type=' . $source_type;
                if ($source_type == 'video') {
                    $title = '标题';
                    $desc = '描述';
                    $result = $this->guzzle_upload($url, $path, $client, 1, $title, $desc);//guzzle上传video
                } else {
                    $result = $this->guzzle_upload($url, $path, $client);//guzzle上传除video外素材
                }
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //这是curl上传视频方式(如果非video则只需要传$url,$path)  {"type":"image","media_id":"loT4fyrpRqfwAeDSwJ5oLqQi_bEUY48zE22tCgqIEnOnGrP2KWqIy2r1T1ZD2KgB","created_at":1567774545}
                //$title='标题';
                //$desc='描述';
                //$result=$this->curl_upload($url,$path,$title,$desc);//调用上面curl_upload方法
                //dd($result);
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////
                //这是guzzle方法(注yi：这个不能用！！！！用上边)
//            $result=$this->guzzle_upload($url,$path,$client);
//            dd($result);
                ////////////////////////////////////////////////

                $re = json_decode($result, 1);
//                dd($re);
//插入数据库
                $search = $request->all()['search'];
                $resul = video::create([
                    'media_id' => $re['media_id'],
                    'type' => $type,
                    'path' => 'http://www.wxapp.com'.$_path,
                    'search' => $search,
                    'add_time' => time()
                ]);
                //拿到图片绝对路径
//            echo storage_path('app\public\wechat'.$path);//不要这个
//            dd($resul);
                if($resul){
                    return redirect('admin/index');
                }
            }
        }
    }

    public function upload()
    {
        return view('upload');
    }

    public function do_login(Request $request)
    {
        $data = $request->all();
//        dd($data);
        header("Content-Type:text/html;charset=utf-8");      //设置头部信息
        //isset()检测变量是否设置
        if(isset($_REQUEST['authcode'])){
            session_start();
            //strtolower()小写函数
            if(strtolower($_REQUEST['authcode'])== $_SESSION['authcode']){
                //跳转页面
                // echo "<script language=\"javascript\">";
                // // echo "document.location=\"./form.php\"";
                // echo "</script>";
            }else{
                //提示以及跳转页面
                echo "<script language=\"javascript\">";
                echo "alert('验证码错误!');";
                echo "document.location=\"/admin/login\"";
                echo "</script>";
            }

            $name = $data['username'];
            $pwd = $data['password'];
            $info = login::where(['user_name'=>$name,'user_pwd'=>$pwd])->get()->toArray();
            if(!$info){
                echo "<script language=\"javascript\">";
                echo "alert('登录信息错误!');";
                echo "document.location=\"/admin/login\"";
                echo "</script>";
            }

            return redirect("admin/index");

        }
    }


    public function AdminLogin()
    {
        return view('login');
    }
    public function Admin()
    {
        return view('index');
    }



    public function get_wechat_access_token(){
        return $this->tools->get_access_token();
    }


    public function show()
    {
        $data = swiper::get();
        return view('qiniu_source',['data'=>$data]);
    }

    public function delete(Request $Request)
    {
        $id = $Request->input('id');
        // $data = swiper::where('id',$id)->get();
        $res = swiper::where('id',$id)->delete();
        if($res){
            echo "<script language=\"javascript\">";
                echo "alert('删除成功!');";
                echo "document.location=\"/qiniu/show\"";
                echo "</script>";

        }
    }

    public function is_use(Request $Request)
    {
        $id = $Request->input('id');
        $res = swiper::where('id',$id)->update([
            'is_use'=>1
        ]);

        if($res){
            echo "<script language=\"javascript\">";
                echo "alert('启用成功!');";
                echo "document.location=\"/qiniu/show\"";
                echo "</script>";


        }
    }

    public function update(Request $Request)
    {
        $id = $Request->input('id');
        $data = swiper::where('id',$id)->select('text')->get();
        return view('update_source',['data'=>$data,'id'=>$id]);
    }

    public function do_update(Request $Request)
    {
        $text = $Request->input('text');
        $id = $Request->input('id');
        $res = swiper::where('id',$id)->update([
            'text'=>$text
        ]);

        if($res){
               echo "<script language=\"javascript\">";
                echo "alert('修改成功!');";
                echo "document.location=\"/qiniu/show\"";
                echo "</script>";

        }
    }


    
}
