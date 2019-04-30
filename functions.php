<?php 
// require_once '../config.php';
//在comments.php中引入functions.php是出现问题，所以直接写入
//封装公用的函数
define('DB_HOST', 'localhost');

/*
* 数据库用户名
*/
define('DB_USER', 'root');

/*
* 数据库密码
*/
define('DB_PASS', '123456');

/*
* 数据库名字
*/
define('DB_NAME', 'baixiu');
session_start();

//获取当前用户，若没有获取到则跳转到登录页面
function xiu_get_current_user () {
	if (empty($_SESSION['current_login_user'])) {
  		header('Location: /admin/login.php');
  		//没获取到用户，后面代码没必要执行
  		exit();
	}
	return $_SESSION['current_login_user'];
}

//封装数据库查询获取数据的函数,$sql为SQL语句
//获取多条数据
function xiu_fetch_all($sql) {
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	//这段代码解决php查询数据库页面中文显示“？”的问题
	$conn->query("set names utf8");
	//mysql_query("set names 'utf8'",$conn);
	if (!$conn) {
		exit('连接数据库失败');
	}
	$query = mysqli_query($conn, $sql);
	if (!$query) {
		return false;
	}
	while ($row = mysqli_fetch_assoc($query)) {
		$result[] = $row; 
	}
	//释放资源，关闭数据库，可写可不写，不写自动关闭数据库
	mysqli_free_result($query);
	mysqli_close($conn);
	return $result;
}

//获取单条数据
function xiu_fetch_one($sql) {
	$res = xiu_fetch_all($sql);
	return isset($res[0]) ? $res[0] : null;
}



//数据库增删改
function xiu_execute ($sql) {
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	//这段代码解决php查询数据库页面中文显示“？”的问题
	$conn->query("set names utf8");
	//mysql_query("set names 'utf8'",$conn);
	if (!$conn) {
		exit('连接数据库失败');
	}
	$query = mysqli_query($conn, $sql);
	if (!$query) {
		return false;
	}
	$affected_rows = mysqli_affected_rows($conn);
	return $affected_rows;
}
