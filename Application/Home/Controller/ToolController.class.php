<?php
/**
 * 工具类
 */
namespace Home\Controller;
use Think\Controller;
use Home\Controller\BaseController;
class ToolController extends Controller {
	//导入用户
    public function import_user() {
        if (session('auth.role') == 2) {
            //申明页面使用的编码避免乱码
            header("Content-Type:text/html;charset=utf-8");
            //连接数据库服务器
            $connect=mysql_connect("127.0.0.1:3306","chj","chj") or die("链接数据库失败！");
            //设置使用的编码
            mysql_query('set names utf8',$connect);
            //连接数据库(test)
            mysql_select_db("shouji",$connect) or die (mysql_error());
            //连接EXCEL文件,格式为.csv。例如：test.cvs
            $temp=file("user.csv");
            //echo count($temp);//count可获取excel数据行数
            //下面通过循环取得数据并作一些处理
            $password = md5('2879046TKHFHDF');
            foreach($temp as $key=>$v) {
                //判断下键名排除掉标题行
                if($key > 0) {
                    $v = iconv('GBK', 'utf-8', $v);//转码，避免乱码
                    //$v = str_replace('?','',$v);//替换列中的一些字符（避免某些?乱码字符，这个根据你的需要替换）
                    $v = explode(",", $v);
                    //插入数据到数据库（根据你数据库字段和EXCEL文件对用列作相应调整）
                    $q = "insert into user (username,password,nickname,email,role,status,remark) values('$v[0]','$password','$v[0]','$v[1]',0,0,'$v[2]')";
                    mysql_query($q,$connect) or die (mysql_error());
                    if(!mysql_error()){
                            echo $q.';</br>';//输出执行的每一条SQL语句
                    }
                    unset($v);
                }
            }
        } else {
            $this->error('你没有权限执行此操作!');
        }
    }

    //导入数据处理（初次使用导入数据处理之用）
    public function chuli() {
        /*ini_set("max_execution_time", "1800");
        $User = M('Mobile');
        $result = $User->select();
        foreach($result as $v) {
            $peijian = str_replace('-',',',$v['peijian']);
            $User->where(array('num'=>$v['num']))->setField('peijian',$peijian);
            echo $User->_sql().'</br>';
        }*/
    }

    public function test() {
        $t = strtotime('2016-04-27 14:20:25');
        dump($t);die;
        $time = getmonth(1446307200,true);
        dump($time);
        $t = '2015-11-01';
        dump(strtotime($t));
        dump(date('Y-m-d H:i:s',strtotime($t)));
    }

    public function hh() {
      $User = M('Mobile');
      $date = $User->where(array('status'=>array('in','0')))->select();
      echo $User->_sql();
      dump($date);

      //$T = M('mobile_loss');
      //$T->addAll($date);
    }


}