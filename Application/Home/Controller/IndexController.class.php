<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\BaseController;
use Home\Controller\Util\Page;
class IndexController extends BaseController {

	//首页
    public function index() {
        C('SHOW_PAGE_TRACE',false);
    	$User = M('Mobile');
    	$count = $User->where(array('status'=>array('in','1,2')))->count();
        $page_size = cookie('pagesize')?cookie('pagesize'):15;
    	$Page = new Page($count,$page_size );
    	$Page->setConfig('prev','«');
    	$Page->setConfig('next','»');
    	$Page->setConfig('first','第一页');
    	$Page->setConfig('last','最末页');
    	$Page->setConfig('theme',"%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE%");
    	$show = $Page->show();
    	$list = $User->where(array('status'=>array('in','1,2')))->limit($Page->firstRow.','.$Page->listRows)->Order('num desc')->Select();
    	$this->list = $list;
    	$this->page = $show;
    	$this->display();
    }

    //借还手机
    public function rent() {
        C('SHOW_PAGE_TRACE',false);
        cookie('user',I('post.user'));
    	//手机编号（无论如何都处理成数组或false）
    	$num = isset($_GET['num'])?(isset($_GET['num'])?array(intval($_GET['num'])):false):(isset($_POST['num'])?$_POST['num']:false);
    	if($num === false) {
    		$this->error('缺少参数');
    	}else {
            $User1 = M('Mobile');
            $User1->startTrans();
            // 开启事务
            $User2 = M('History');
	    	switch (session('auth.role')) {
	    		case 0:
                    # 游客
	    			$this->error('没有权限，请先登录!');
    			break;
                case 1:
                    //普通用户
                    //处理逻辑待版本迭代
                break;
	    		case 2:
                    //管理员
                    switch (I('post.type')) {
                        case 2: //借用
                        $result1 = $User1->where(array('num'=>array('in',implode(',',$num))))->setField(array('user'=>I('post.user'),'status'=>1));//更新Mobile表
                        case 3: //归还
                        $result1 = $User1->where(array('num'=>array('in',implode(',',$num))))->setField(array('user'=>'仓库','status'=>1));//更新Mobile表
                    }
                    $dataList = array();
                    foreach ($num as $key=>$vo) {
                        //组合数组供批量提交
                        $dataList[$key]['orders'] = get_orders();
                        $dataList[$key]['num'] = intval($vo);
                        $dataList[$key]['user'] = I('post.user');
                        $dataList[$key]['time'] = time();
                        $dataList[$key]['type'] = I('param.type');
                        $dataList[$key]['ip'] = get_client_ip();
                        $dataList[$key]['remark'] = I('param.remark');
                        $dataList[$key]['status'] = 1;
                    }
                    $result2 = $User2->addAll($dataList);//批量提交（插入流水记录History表）
                    if (($result1 !== false) && ($result2 !== false)) {
                        //两张表都操作成功则提交事务
                        $User1->commit();
                        $this->success('操作成功!',U('Index/history'));
                    }else {
                        //任何一张表操作失败回滚事务
                        $User1->rollback();
                        $this->error('操作失败,没有任何数据被更改!');
                    }
                break;
	    		default:
                    //未知角色
                    $this->error('身份异常!');
	    	}
    	}
    }

    //新机器登记
    public function addnew() {
        C('SHOW_PAGE_TRACE',false);
        //判断是否登录
        if (session('?auth')) {
            //已经登录才可以执行（0、游客;1、普通用户;2、管理员）
            if (session('auth.role') == 2) {
                //并且拥有足够权限
                if (IS_POST OR IS_AJAX) {
                    //处理新机登记逻辑
                    $User1 = M('Mobile');
                    //开启事务
                    $User1->startTrans();
                    $data = $_POST;
                    $data['num'] = $User1->max('num')?$User1->max('num')+1:'编号获取错误';//自动获取最大编号+1作为新手机编号
                    $data['peijian'] = isset($data['peijian'])?$data['peijian']:array();
                    $data['peijian'] = implode(",",$data['peijian']);//分割复选框的值为字符串
                    $data['user'] = '仓库';
                    $data['buy_time'] = date('Y-m-d',time());
                    if ($data['buy'] == '其他') {
                        if ($data['buy_address'] != '') {
                            $data['buy'] = $data['buy_address'];
                        }else{
                            $data['buy'] = '未知';
                        }
                    }
                    $result1 = $User1->add($data);
                    //插入流水
                    $User2 = M('History');
                    $data['orders'] = get_orders();
                    $data['time'] = time();
                    $data['user'] = session('auth.username');
                    $data['type'] = 1;//交易类型
                    $data['status'] = 1;//有效状态
                    $data['ip'] = get_client_ip();
                    $result2 = $User2->add($data);
                    if ($result1 && $result2) {
                        //提交事务
                        $User1->commit();
                        $this->success('登记成功!');
                    }else {
                        //不成功，则回滚
                        $User1->rollback();
                        $this->error('登记失败!');
                    }

                }else{
                    //新机登记表单
                    $this->pinpai = array('华为','小米','红米','三星','联想','魅族','酷派','中兴','TCL','金立','VIVO','苹果','HTC','OPPO','努比亚','摩托罗拉','海尔','海信','乐视','白米','酷比','小辣椒');
                    $this->color = array('黑','白','蓝','红','灰','金','银','黄','粉红');
                    $this->ram = array('256M','512M','1GB','1.5GB','2GB','3GB','4GB');
                    $this->rom = array('512M','1GB','2GB','4GB','8GB','16GB','32GB','64GB','128GB');
                    $this->screen = array('3.0','3.5','4.0','4.2','4.3','4.5','4.7','4.8','5.0','5.1','5.15','5.2','5.5','5.7','6.0','6.4','6.8','7.0','7.9','8.0','9.6');
                    $this->lte = array('移动','联通','电信','双网通','全网通');
                    $this->display();
                }
            }else {
                $this->error('权限不足!');
            }
        }else {
            //未登录
            Header("Location:".U('Public/login'));
        }
    }

    //修改手机信息
    public function editMobile() {
        if (session('auth.role') == 2) {
            C('SHOW_PAGE_TRACE',false);
            if (IS_POST OR IS_AJAX) {
                $data = $_POST;
                $data['peijian'] = implode(',',$data['peijian']);
                $User = M('Mobile');
                $result = $User->where(array('num'=>$data['num']))->save($data);
                if (!false == $result) {
                    $this->success('修改成功!');
                }else {
                    $this->error('修改失败!');
                }
            }
            else {
                $num = isset($_GET['num'])?$_GET['num']:false;
                if(! $num == false){
                    $User = M('Mobile');
                    $result = $User->where(array('num'=>$num))->find();
                    $this->list = $result;
                    $this->peijian = array(array('name'=>'盒子','ico'=>'icon-inbox'),array('name'=>'数据线','ico'=>'icon-android'),array('name'=>'充电器','ico'=>'icon-bolt'),array('name'=>'耳机','ico'=>'icon-headphones'));
                    $this->pinpai = array('华为','小米','红米','三星','联想','魅族','酷派','中兴','TCL','金立','VIVO','苹果','HTC','OPPO','努比亚','摩托罗拉','海尔','海信','乐视','白米','酷比','小辣椒');
                    $this->color = array('黑','白','蓝','红','灰','金','银','黄','粉红');
                    $this->ram = array('256M','512M','1GB','1.5GB','2GB','3GB','4GB');
                    $this->rom = array('512M','1GB','2GB','4GB','8GB','16GB','32GB','64GB','128GB');
                    $this->screen = array('3.0','3.5','4.0','4.2','4.3','4.5','4.7','4.8','5.0','5.1','5.15','5.2','5.5','5.7','6.0','6.4','6.8','7.0','7.9','8.0','9.6');
                    $this->lte = array('移动','联通','电信','双网通','全网通');
                }else {
                    $this->error('参数错误!');
                }
                $this->display();
            }
        }else {
            $this->error('没有权限!');
        }
    }

    //历史操作记录
    public function history() {
        C('SHOW_PAGE_TRACE',false);
        if (session('auth.role') >= 0) {
            $User = M('History');
            if (isset($_GET['num'])) {
                //按编号搜
                $where = array('history.status'=>1,'history.num'=>intval(I('get.num')));
            }else if (isset($_GET['date'])) {
                //按日期搜
                $day = date('Ymd',intval(I('get.date')));
                $start = strtotime(date('Y-m-d 00:00:00',strtotime($day)));
                $end = strtotime(date('Y-m-d 23:59:59',strtotime($day)));
                $where = array('history.status'=>1,'history.time'=>array(array('egt',$start),array('elt',$end)));
            }else {
                //默认显示全部
                $where = array('history.status'=>1);
            }
            //分页
            $count = $User->where($where)->count();
            $page_size = cookie('pagesize')?cookie('pagesize'):15;
            $Page = new Page($count,$page_size );
            $Page->setConfig('prev','«');
            $Page->setConfig('next','»');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','最末页');
            $Page->setConfig('theme',"%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE%");
            $this->page = $Page->show();//赋值分页数据到模板
            //数据查询
            $User->field('history.id,history.num,mobile.pinpai,mobile.model,history.user,history.time,history.type,history.ip,history.remark');
            $User->join('mobile ON history.num = mobile.num','left');
            $result = $User->where($where)->limit($Page->firstRow.','.$Page->listRows)->Order('time desc,id desc')->Select();
            $this->list = $result;
            $this->display();
        }else {
            $this->error('没有权限!');
        }
    }

    //批量操作
    public function batch() {
        C('SHOW_PAGE_TRACE',false);
        $User = M('Mobile');
        $flag = I('param.flag');//具体操作指示参数
        switch ($flag) {
          //请求数据
          case 'ajax':
            $result = $User->field('num,pinpai,model,user,peijian,remark')->where(array('num'=>I('param.num')))->find();
            if ($result) {
              $this->success($result);
            }else {
              $this->error(false);
            }
          break;
          //借、还操作
          case 'rent':
            $num = I('param.num');//数组
            switch (session('auth.role')) {
              case 0:
                //游客
                $this->error('没有权限!');
              break;
              case 1:
                //普通用户
                //处理逻辑待版本迭代
              break;
              case 2:
                  //管理员
                  $orders = get_orders();//订单号
                  $User1 = M('History');
                  $User1->startTrans();//开启事物
                  $User2 = M('Mobile');
                  $dataList = array();
                  foreach ($num as $vo) {
                    switch (I('post.type')) {
                      case 'jie'://借用
                        $dataList[] = array('orders'=>$orders,'num'=>$vo,'user'=>I('post.username'),'time'=>time(),'type'=>2,'ip'=>get_client_ip(),'status'=>1,'remark'=>I('post.remark'));
                      break;
                      case 'huan'://归还
                        $dataList[] = array('orders'=>$orders,'num'=>$vo,'user'=>I('post.username'),'time'=>time(),'type'=>3,'ip'=>get_client_ip(),'status'=>1,'remark'=>I('post.remark'));
                      break;
                      case 'xiu'://返修
                        $dataList[] = array('orders'=>$orders,'num'=>$vo,'user'=>I('post.username'),'time'=>time(),'type'=>4,'ip'=>get_client_ip(),'status'=>1,'remark'=>I('post.remark'));
                      break;
                      case 'tui'://退货
                        $dataList[] = array('orders'=>$orders,'num'=>$vo,'user'=>I('post.username'),'time'=>time(),'type'=>5,'ip'=>get_client_ip(),'status'=>1,'remark'=>I('post.remark'));
                      break;
                      case 'chuli'://报废处理
                        $dataList[] = array('orders'=>$orders,'num'=>$vo,'user'=>I('post.username'),'time'=>time(),'type'=>6,'ip'=>get_client_ip(),'status'=>1,'remark'=>I('post.remark'));
                      break;
                      default:
                      $this->error('未知操作!');
                    }
                  }
                  $result1 = $User1->addAll($dataList);//借用、归还等的流水记录
                  switch (I('post.type')) {
                      case 'jie':
                        $result2 = $User2->where(array('num'=>array('in',implode(',',$num))))->save(array('user'=>I('post.username'),'status'=>1));
                      break;
                      case 'huan':
                        $result2 = $User2->where(array('num'=>array('in',implode(',',$num))))->save(array('user'=>'仓库','status'=>1));
                      break;
                      case 'xiu':
                        $result2 = $User2->where(array('num'=>array('in',implode(',',$num))))->save(array('user'=>'返修','status'=>2));
                      break;
                      case 'tui':
                        $result2 = $User2->where(array('num'=>array('in',implode(',',$num))))->save(array('user'=>'已退货','status'=>3));
                      break;
                      case 'chuli':
                        $result2 = $User2->where(array('num'=>array('in',implode(',',$num))))->save(array('user'=>'已报废处理','status'=>4));
                      break;
                      default:
                        $result2 = false;
                  }
                  if ((!false == $result1) && (!false == $result2)) {
                    $User1->commit();//提交事物
                    $this->success('操作成功!');
                  }else{
                    $User1->rollback();//回滚
                    $this->error('操作失败!');
                  }
              break;
            }
          break;
        }
        $this->display();
    }

    //导出数据
    public function export() {
        C('SHOW_PAGE_TRACE',false);
        if (session('auth.role') == 2) {
            $User = M('Mobile');
            $str = "编号,品牌,型号,颜色,手机位置,IMEI,入库日期,备注,配件\n";//其中\n为换行符号
            $str = iconv('utf-8','gbk',$str);//从utf-8转为gbk编码
            $result = $User->where(array('status'=>array('in','1,2')))->field('num,pinpai,model,color,user,imei,buy_time,remark,peijian')->select();
            foreach ($result as $value) {
              $str1 = implode(",",$value);//拼凑出以逗号分割的每行的数据
              $str1 = iconv('utf-8','gbk',$str1);
              $str = $str.$str1."\n"; //连接上标题
            }
            header("Content-type:text/csv");
            header("Content-Disposition:attachment;filename=".time().".csv");//用当前时间戳作为文件名
            echo $str;
            exit();
        }else {
          $this->error('你没有权限!');
        }
    }

    //数据导入
    public function import() {
      $this->error('此功能被禁止使用，请联系超级管理员!');
    }

    //用户面板（帐号相关）
    public function account()  {
      $this->error('功能开发中...');
    }

    //搜索
    public function search() {
        C('SHOW_PAGE_TRACE',false);
        $flag = I('param.flag');
        $User = M('Mobile');
        //条件
        switch ($flag) {
            case 'user' :
              # 按用户搜索
              $where = array('user'=>I('get.username'),'status'=>array('in','1,2'));
              $this->msg = array('title'=>I('get.username').'借用的手机','ico'=>'icon-mobile text-large');
            break;
            case 'pinpai':
              $where = array('pinpai'=>I('get.pinpai'),'status'=>array('in','1,2'));
              $this->msg = array('title'=>'搜索:'.I('get.pinpai'),'ico'=>'icon-search text-large');
            break;
            case 'model' :
              # 按型号搜索
              cookie('model',I('param.search_model'));
              $where = array('model'=>array('like','%'.I('param.search_model').'%'),'status'=>array('in','1,2'));
              $this->msg = array('title'=>'搜索','ico'=>'icon-search text-large');
            break;
            case 'advanced':
            # 高级搜索
            $this->msg = array('title'=>'高级搜索','ico'=>'icon-search text-large');
            if (IS_AJAX or IS_POST) {
                $where = array();
                if (I('pinpai') != '') {
                    $where = array('pinpai'=>array('eq',I('pinpai')));
                }
                if (I('model') != '') {
                  $model = array('model'=>array('like','%'.I('model').'%'));
                  $where = array_merge($where,$model);
                }
                if (I('ram') != '') {
                  $ram = array('ram'=>array('eq',I('ram')));
                  $where = array_merge($where,$ram);
                }
                if (I('rom') != '') {
                  $rom = array('rom'=>array('eq',I('rom')));
                  $where = array_merge($where,$rom);
                }
                if (I('system') != '') {
                  $system = array('system'=>array('eq',I('system')));
                  $where = array_merge($where,$system);
                }
                if (I('lte') != '') {
                  $lte = array('lte'=>array('eq',I('lte')));
                  $where = array_merge($where,$lte);
                }
                if (I('screen') != '') {
                  $screen = array('screen'=>array('eq',I('screen')));
                  $where = array_merge($where,$screen);
                }
                if (I('status') != '') {
                  $status = array('status'=>array('in',implode(',', I('status'))));
                  $where = array_merge($where,$status);
                }
                if (I('user') != '') {
                  $user = array('user'=>array('eq',I('user')));
                  $where = array_merge($where,$user);
                }
                $result = M('Mobile')->field('num,pinpai,model,ram,rom,lte,system,screen,color,peijian,user,status,remark')->where($where)->select();
                switch ($result) {
                  case false:
                    $this->error('查询出错或没有任何数据!');
                    break;
                  default:
                    $this->success($result);
                }
            }
            $this->display('searchAdvanced');
            exit;
            break;
            default:
              $this->error('参数错误!');
        }
        //处理查询
        $count = $User->where($where)->count();
        $page_size = cookie('pagesize')?cookie('pagesize'):15;
        $Page = new Page($count,$page_size );
        if ($flag == 'model') {
          foreach($where as $key=>$val) {
            $Page->parameter .= "$key=".urlencode($val).'&';
          }
        }
        if ($flag != 'model') {
          $Page->setConfig('prev','«');
          $Page->setConfig('next','»');
          $Page->setConfig('first','第一页');
          $Page->setConfig('last','最末页');
          $Page->setConfig('theme',"%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE%");
          $this->page = $Page->show();
          $list = $User->where($where)->limit($Page->firstRow.','.$Page->listRows)->Order('id asc')->Select();
        }else {
          //对POST搜索分页导航BUG的临时屏蔽(不使用分页)
          $list = $User->where($where)->Order('id asc')->Select();
        }
        $this->list = $list;
        $this->display();
    }


    /**
     * [report 财务报表]
     * @return [type] [description]
     */
    public function report($time = '',$type = '') {
        $Model = M('Report');
        if(empty($time) or empty($type)) {
          $result = $Model->order('month asc,create_time desc')->select();
          $this->list = $result;
          $this->display();
        }else {
          $Model = M('history');
          $where = array('history.type'=>$type,'history.time'=>array('between',getmonth(date('Y-m-d',$time),true)),'history.status'=>1);
          //分页
          $count = $Model->where($where)->count();
          $page_size = cookie('pagesize')?cookie('pagesize'):15;
          $Page = new Page($count,$page_size );
          $Page->setConfig('prev','«');
          $Page->setConfig('next','»');
          $Page->setConfig('first','第一页');
          $Page->setConfig('last','最末页');
          $Page->setConfig('theme',"%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE%");
          $this->page = $Page->show();//赋值分页数据到模板
          $Model->field('history.id,history.num,mobile.pinpai,mobile.model,history.user,history.time,history.type,history.ip,history.remark');
          $Model->join('mobile ON history.num = mobile.num','left');
          $result = $Model->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
          $this->list = $result;
          $this->display('reportList');

        }
    }

    /**
     * [report_check 财务结算]
     * @return [type] [description]
     */
    Public function report_check() {
        if (session('auth.role') == 2) {
          $month = I('month',date('Y-m-d',time()));//年月日 2015-12-01
          $param = array('income'=>1,'send'=>2,'repair'=>3,'return'=>4,'store'=>5);
          $data = array();
          foreach($param as $key => $type) {
            $data[$key] = self::report_count($month,$type);
          }
          $data = array_merge($data,array('month'=>date('Y-m',strtotime($month))),array('create_time'=>time()));
          $check_result ='';
          $Model = M('Report');
          try{
            $result = $Model->add($data);
            $this->success('结算成功!');
          }catch(\Exception $e){
            if($e->getCode() == 23000) {
              // 如果存在该月份数据则重新结算
              $result = $Model->save($data);
              if($result){
                $this->success('更新结算成功!');
              }else{
                $this->error('结算失败,请联系开发者!');
              }
            }
          }
        }else {
          $this->error('你没有权限执行结算!');
        }
    }

    /**
     * [report_count 报表结算统计]
     * @param  [string] $month [年月日] eg:2015-11-01
     * @param  [intval] $param [参数（1、入库;2、报废处理出库;3、返修;4、退货;5、结存）]
     * @return [intval]        [整型数值]
     */
    protected function report_count($month = '',$param = '') {
      $month = getmonth($month,true);//月份时间戳范围数组
      switch($param) {
        case 1:
          // 入库
          $result = M('History')->where(array('type'=>1,'status'=>1,'time'=>array('between',$month)))->count();
          return intval($result);
          break;
        case 2:
          // 报废处理
          $result = M('History')->where(array('type'=>6,'status'=>1,'time'=>array('between',$month)))->count();
          return intval($result);
          break;
        case 3:
          // 返修
          $result = M('History')->where(array('type'=>4,'status'=>1,'time'=>array('between',$month)))->count();
          return intval($result);
          break;
        case 4:
          // 退货
          $result = M('History')->where(array('type'=>5,'status'=>1,'time'=>array('between',$month)))->count();
          return intval($result);
        case 5:
          // 结存
          $result = M('Mobile')->where(array('status'=>array('in','1,2'),'buy_time'=>array('elt',date('Y-m-d',$month[1]))))->count();
          return intval($result);
        default :
          return false;
      }
    }

}