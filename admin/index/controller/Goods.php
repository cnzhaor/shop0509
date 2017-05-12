<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\ProductCategory;
class Goods extends Controller
{
    public function product_category_add()
    {
        $pcModel = new ProductCategory();
        if (request()->isPost() && !empty(input('post.name'))){
            $pid = input('post.pid');
            $maxId = $pcModel->max('id');
            if ($pid != 0){
                //父级分类信息
                $pData = $pcModel->where('id',$pid)->field('level,path')->find();
                $pcModel->data([
                    'level' => $pData->level+1,
                    'name' => input('post.name'),
                    'pid'  => input('post.pid'),
                    'path' => $pData->path .',' . ($maxId+1)
                ]);
            }
            else{
                $pcModel->data([
                    'level' => 1,
                    'name' => input('post.name'),
                    'pid'  => input('post.pid'),
                    'path' => '0,' . ($maxId+1)
                ]);
            }
            if($pcModel->save())
                $this->success('添加分类成功!');
            else
                $this->error('添加分类失败！', url('product_category_add'));
        }
        $cats = $pcModel->order('path')->select();
        $this->assign('cats', $cats);
        return $this->fetch();
    }
    public function product_category()
    {
        return $this->fetch();
    }
}
