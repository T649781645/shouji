<?php
//申明页面使用的编码避免乱码
header("Content-Type:text/html;charset=utf-8");
//连接数据库服务器
$connect=mysql_connect("127.0.0.1:3306","chj","chj") or die("链接数据库失败！");
//设置使用的编码
mysql_query('set names utf8',$connect);
//连接数据库(test)
mysql_select_db("shouji",$connect) or die (mysql_error());
//连接EXCEL文件,格式为.csv。例如：test.cvs
$temp=file("mobile.csv");
//echo count($temp);//count可获取excel数据行数
//下面通过循环取得数据并作一些处理
foreach($temp as $key=>$v) {
    //判断下键名排除掉标题行
    if($key > 0) {
        $v = iconv('GBK', 'utf-8', $v);//转码，避免乱码
        //$v = str_replace('?','',$v);//替换列中的一些字符（避免某些?乱码字符，这个根据你的需要替换）
        $v = explode(",", $v);
        //插入数据到数据库（根据你数据库字段和EXCEL文件对用列作相应调整）
        $q = "insert into mobile (num,pinpai,model,imei,color,peijian,user,buy,buy_time,local,status,remark,qingdian) values('$v[0]','$v[1]','$v[2]','$v[3]','$v[4]','$v[5]','$v[6]','$v[7]','$v[8]','$v[9]','$v[10]','$v[11]','$v[12]')";
        mysql_query($q,$connect) or die (mysql_error());
        if(!mysql_error()){
                echo $q.';</br>';//输出执行的每一条SQL语句
        }
        unset($v);
    }
}