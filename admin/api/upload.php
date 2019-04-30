<?php 
// var_dump($_FILES('avatar'));
//接收文件
//保存文件
//访问文件URL
if (empty($_FILES['avatar'])) {
	exit('必须传入参数');
}
$avatar = $_FILES['avatar'];
if ($avatar['error'] !== UPLOAD_ERR_OK) {
	exit('上传失败');
}

//检验类型大小....


//保存文件
$ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
$target = '../../static/uploads/img-' . uniqid() . '.' . $ext;
if (!move_uploaded_file($avatar['tmp_name'], $target)) {
	exit('上传失败');
}
//上传成功，返回路径
echo substr($target, 5);