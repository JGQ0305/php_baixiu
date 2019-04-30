<?php 

/**
根据客户端传来的ID删除对用的数据
*/
require_once '../functions.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}
//sql 注入加（int）
// $id = (int)$_GET['id'];
$id = $_GET['id'];
//受影响行没必要写，这里没有写出来
xiu_execute('delete from posts where id in (' . $id . ');');
header('Location: ' . $_SERVER['HTTP_REFERER']);
