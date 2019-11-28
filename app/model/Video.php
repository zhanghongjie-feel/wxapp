<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'video';//设置表名
    protected $primaryKey="v_id";//主键id
    public $timestamps = false;//关闭自动时间戳
    protected $guarded = [];//不能被批量赋值的属性
}
