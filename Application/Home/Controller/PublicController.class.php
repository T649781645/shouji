<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {

    //初始化方法
    public function _initialize() {
        define('MAC',MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
    }

    //用户登录
    public function login() {
        C('SHOW_PAGE_TRACE',false);
    	//如果未登录
    	if (!session('?auth')) {
            if (I('get.user') == 'guest') {
                if (C('AUTH_GUEST')) {
                    //游客信息
                    $result = array('uid'=>'0','username'=>'guest','password'=>'','nickname'=>'guest','email'=>'guest@ltbl.cn','role'=>0,'status'=>1);
                    session('auth',$result);
                    redirect(U('Index/index'));
                }else{
                    $this->error('当前没有开启游客模式!');
                }
            }
	    	if (IS_POST OR IS_AJAX) {
                if (I('param.email') == '' or I('param.password') == '') {
                    $this->error('登录信息不完整!');
                }
	    		$User = M('User');
                $result = $User->where(array(array('email'=>I('email'),'status'=>1)))->find();
	            if (!false == $result) {
	                if (md5(I('password')) == $result['password']) {
                        unset($result['password']);
	                    session('auth',$result);
	                    $this->success('登录成功!',U('Index/index'));
	                }else{
	                    $this->error('帐号或密码错误!');
	                }
	            }else{
	                $this->error('帐号不存在或被禁用!');
	            }
	    	}else{
		    	C('SHOW_PAGE_TRACE',false);
		    	$this->display();
	    	}
    	}else{
    		//如果已经登录
    		redirect(U('/Index/index'));
    	}
    }

    //注销登录
    public function logout() {
    	if (session('?auth')) {
    		//销毁Session
	    	session('[destroy]');
	    	//删除cookie
	    	cookie('pagesize',null);
            cookie('user',null);
            cookie('model',null);
	    	//$this->success('登出成功！',U('Public/login'),1);
            header("location:".U('Public/login'));
    	}else{
    		redirect('Public/login');
    	}
    }

    //帐号注册
    public function register() {
    	$this->error('暂停注册！');
    }

    //建议反馈
    public function guestbook() {
        C('SHOW_PAGE_TRACE',false);
    	if (IS_POST OR IS_AJAX) {
    		# 处理反馈数据
            $User = M('Guestbook');
            $data = $_POST;
            $result = $User->add($data);
    		if ($result) {
    		  $this->success('你的建议我们已经收到！',U('Index/index'),3);
            }else {
                $this->error('反馈失败，请重新试!');
            }
		}else{
			# 反馈页表单
    		$this->display();
		}
    }

    //验证码
    public function verify() {
        $Verify = new \Think\Verify();
        $Verify->entry();
    }

    //设置分页大小
    public function pagesize() {
    	cookie('pagesize',I('param.pagesize'));
    	$this->success('设置分页成功！',U('Index/index'),1);
    }

    //返回请求的用户名数组
    public function request_user() {
        $Model = M('User');
        if (I('username',false)) {
             $list = $Model->where(array('username'=>array('like','%'.I('username').'%')))->getField('username',true);
         } else {
            $list = false;
         }
        $list = $list?$list:array();
        $this->success($list);
    }

}