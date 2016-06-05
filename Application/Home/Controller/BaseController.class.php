<?php
namespace Home\Controller;
use Think\Controller;
    /**
    * 项目公用基类
    */
class BaseController extends Controller {

	//初始化方法
    public function _initialize() {
        define('MAC',MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        $this->referer_url = get_referer_url();
    	if (session('?auth')) {
       	    //已登录
            $this->is_login = true;
    	}else{
            //未登录
            $this->is_login = false;
            $this->session = false;
            //$this->error('请先登录!',U('Public/login'));
            //redirect(U('Public/login',array('session'=>'timeout')));
            redirect(U('Public/login'));
    	}
    }

 }