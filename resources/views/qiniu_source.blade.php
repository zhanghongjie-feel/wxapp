<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <center>
        
          <table border=1>
                <tr>
                    <td>id</td>
                    <td>text</td>
                    <td>预览</td>
                    <td>状态</td>
                    <td>修改</td>
                    <td>删除</td>
                </tr>
                @foreach($data as $k=>$v)
                <tr>
                    <td>{{$v->id}}</td>
                    <td>{{$v->text}}</td>
                    <td><img style="width: 100px;" src="{{$v->path}}"></td>
                    @if($v->is_use==1)
                    <td>已经启用
                    </td>

                    @else
                    <td>
                        并没有启用
                        <a href="{{url('qiniu/is_use')}}?id={{$v->id}}">启用</a>
                    </td>
                    @endif

                    <td><a href="{{url('qiniu/update')}}?id={{$v->id}}">修改</a></td>
                    <td><a href="{{url('qiniu/delete')}}?id={{$v->id}}">删除</a></td>
                </tr>
                @endforeach
        </table>


    </center>
  
</body>
</html>