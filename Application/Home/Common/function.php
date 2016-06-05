<?php
	/**
	 * [rent 转换数据库代码为中文含义]
	 * @param  [type] $code [description]
	 * @return [type]       [description]
	 */
	function rent($code=null) {
		switch ($code) {
			case 1:
				$str = '入库';
				break;
			case 2:
				$str = '借用';
				break;
			case 3:
				$str = '归还';
				break;
			case 4:
				$str = '返修';
				break;
			case 5:
				$str = '退货';
				break;
			case '6':
				$str = '处理';
				break;
			default:
				$str = '未知操作';
		}
		return $str;
	}

	/**
	 * [get_orders 获取一个订单号]
	 * @return [type] [description]
	 */
	function get_orders() {
		$orders = '1'.date('ymd',time()).date('s',time()).rand(0,9);
		return $orders;
	}

	/**
	 * [getmonth 获取指定月的天范围]
	 * @param  string  $date [时间字符串或时间戳]
	 * @param  boolean $unix [是否返回时间戳格式]
	 * @return [type]        [指定月的范围]
	 */
	function getmonth($time = '', $unix = false) {
		if(is_int($time)) { //时间戳格式 1450758628
			$time = empty($time) ? time() : $time;
		}
		if(is_string($time)) { //日期时间格式 2015-12-01 10:35:42
			$time = empty($time) ? time() : strtotime($time);
		}
		$firstday = date('Y-m-01', $time);
		$lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
		if($unix) {
			return array(strtotime($firstday),strtotime($lastday . '23:59:59'));
		}else {
			return array($firstday,$lastday);
		}
	}
  /**
   * [get_referer_url 获取来路URL]
   * @return [type] [来路URL字符串]
   */
	function get_referer_url() {
		$url = $_SERVER["HTTP_REFERER"]; //获取完整的来路URL
		//$str = str_replace("http://","",$url); //去掉http://
		//$strdomain = explode("/",$str); // 以“/”分开成数组
		//$domain = $strdomain[0]; //取第一个“/”以前的字符
		return $url;
	}