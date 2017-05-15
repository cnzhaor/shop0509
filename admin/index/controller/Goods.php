<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\ProductCategory;
use think\Db;

class Goods extends Controller
{
    //新增商品分类
    public function product_category_add()
    {
        $pcModel = new ProductCategory;
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

    //商品分类列表
    public function product_category()
    {
        return $this->fetch();
    }

    //AJAX获得商品分类
    public function product_category_ajax()
    {
//        $pcModel = new ProductCategory;
//        return $pcModel->field('id,pid,name')->select();
//        新写法
        return Db::name('ProductCategory')->field('id,pid,name')->select();
    }

    //删除商品分类
    public function product_category_del($id)
    {
        $pcModel = new ProductCategory;
        $hasChild = $pcModel->where('pid='.$id)->find();
        if($hasChild)
            return '此分类下有子分类，不能删除！';
        elseif(db('ProductCategory')->delete($id))
            return 1;
        else
            return false;
    }

    //新增商品
    public function product_add()
    {
        if(request()->isPost()){
            $tid = explode(',',input('post.tid'));
            $attributes = implode(',',$_POST['attributes']);
            $ret = Db::name('Goods')->insert([
                'goodsname' => input('post.goodsname'),
                'tid' => $tid[0],
                'tpid' => $tid[1],
                'unit' => input('post.unit'),
                'attributes' => $attributes,
                'imagepath' => input('post.file'),
                'reorder' => input('post.reorder'),
                'number' => input('post.number'),
                'barcode' => input('post.barcode'),
                'curprice' => input('post.curprice'),
                'oriprice' => input('post.oriprice'),
                'cosprice' => input('post.cosprice'),
                'inventory' => input('post.inventory'),
                'restrict' => input('post.restrict'),
                'already' => input('post.already'),
                'status' => input('post.status'),
                'freight' => input('post.freight'),
                'text' => input('post.editorValue'),
                'lastedit' => time(),
            ]);
            if($ret)
                $this->success('新增商品成功！',url('product_list'));
            else
                $this->error('新增商品失败！'.$ret);
        }
        else
        {
            $cats = Db::name('ProductCategory')->order('path')->select();
            $this->assign('cats', $cats);
            return view();
        }

    }
    //修改商品
    public function product_edit()
    {
        //PARAM变量是框架提供的用于自动识别GET、POST或者PUT请求的一种变量获取方式，是系统推荐的获取请求参数的方法
        if(request()->isPost()){
            $tid = explode(',',input('post.tid'));
            $attributes = implode(',',$_POST['attributes']);
            $ret = Db::name('Goods')
                ->where('id', input('param.id'))
                ->update([
                'goodsname' => input('post.goodsname'),
                'tid' => $tid[0],
                'tpid' => $tid[1],
                'unit' => input('post.unit'),
                'attributes' => $attributes,
                'imagepath' => input('post.file'),
                'reorder' => input('post.reorder'),
                'number' => input('post.number'),
                'barcode' => input('post.barcode'),
                'curprice' => input('post.curprice'),
                'oriprice' => input('post.oriprice'),
                'cosprice' => input('post.cosprice'),
                'inventory' => input('post.inventory'),
                'restrict' => input('post.restrict'),
                'already' => input('post.already'),
                'status' => input('post.status'),
                'freight' => input('post.freight'),
                'text' => input('post.editorValue'),
                'lastedit' => time(),
                ]);
            if($ret)
                $this->success('商品修改成功！',url('product_list'));
            else
                $this->error('商品修改失败！'.$ret);
        }
        else
        {
            $goods = Db::name('Goods')->where('id',input('param.id'))->find();
            $cats = Db::name('ProductCategory')->order('path')->select();
            $this->assign([
                'cats' => $cats,
                'goods' => $goods
            ]);
            return view();
        }

    }

    //商品列表
    public function product_list()
    {
        $goods = Db::name('goods')->select();
        $count = Db::name('goods')->count();
        $this->assign([
            'goods' => $goods,
            'count' => $count
        ]);
        return view();
    }

    //商品图片上传
    public function product_images_add()
    {
        // 获取表单上传文件
        $files = request()->file('image');
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                return json_encode([$info->getExtension(),$info->getFilename()]);
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo 1;
            }else{
                // 上传失败获取错误信息
                return json_encode($file->getError());
            }
        }
    }
}
