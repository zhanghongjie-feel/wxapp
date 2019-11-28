<?php

include "qiniu-sdk/autoload.php";

use Qiniu\Auth;

$ak = "P6jM_6ZDb4EzxrmTVz1rWePZjtgmyXuYj9RZVmdH";
$sk = "kpQ8ZzOGmhUrF_LFZT7_zHg8gRhZ3Z1QmsYPCWrb";

$bucket = "distantplace";

$obj = new Auth($ak,$sk);

echo $obj->uploadToken($bucket);
