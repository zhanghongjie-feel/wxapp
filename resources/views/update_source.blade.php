<!DOCTYPE html>
<html>
<head>
	<title>
		修改图篇
	</title>
</head>
<body>
	<center>
		<form action="{{url('qiniu/do_update')}}">
			<p>修改资源</p>
			<input type="hidden" name="id" value="{{$id}}">
			<input type="text" name="text">
			<input type="submit" name="修改">
		</form>
		
	</center>
	
</body>
</html>