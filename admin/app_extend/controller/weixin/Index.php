<?php
namespace app\app_extend\controller\weixin;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        //访问路径http://www.0509.com/index.php/app_extend/weixin.index
        return $this->fetch();
    }
}
