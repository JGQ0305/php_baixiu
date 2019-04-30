<?php

// 载入配置文件
// require_once '../config.php';

// // // 给用户找一个箱子（如果你之前有就用之前的，没有给个新的）
// session_start();

// function login () {
//   // 1. 接收并校验
//   // 2. 持久化
//   // 3. 响应
//   if (empty($_POST['email'])) {
//     $GLOBALS['message'] = '请填写邮箱';
//     return;
//   }
//   if (empty($_POST['password'])) {
//     $GLOBALS['message'] = '请填写密码';
//     return;
//   }

//   $email = $_POST['email'];
//   $password = $_POST['password'];

//   // 当客户端提交过来的完整的表单信息就应该开始对其进行数据校验
//   $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
//   if (!$conn) {
//     exit('<h1>连接数据库失败</h1>');
//   }

//   $query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");

//   if (!$query) {
//     $GLOBALS['message'] = '登录失败，请重试！';
//     return;
//   }

//   // 获取登录用户
//   $user = mysqli_fetch_assoc($query);

//   if (!$user) {
//     // 用户名不存在
//     $GLOBALS['message'] = '邮箱与密码不匹配';
//     return;
//   }

//   // 一般密码是加密存储的
//   if ($user['password'] !== $password) {
//     // 密码不正确
//     $GLOBALS['message'] = '邮箱与密码不匹配';
//     return;
//   }

//   // 存一个登录标识
//   // $_SESSION['is_logged_in'] = true;
//   $_SESSION['current_login_user'] = $user;

//   // 一切OK 可以跳转
//   header('Location: /admin/');
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//   login();
// }

?>

<?php 
require_once '../config.php';
session_start();//忘了加这条代码困扰半天！！！！！！！
function login(){
//1.接收并校验
  if (empty($_POST['email'])) {
    $GLOBALS['message'] = '请输入邮箱';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['message'] = '请输入密码';
    return;
  }
  $email = $_POST['email'];
  $password = $_POST['password'];

  $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  $conn->query("set names utf8");
  if (!$conn) {
    exit('<h2>连接数据库失败</h2>');
  }
  $query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");
  // limit 1查询到第一条符合的email就停止查询，节省时间
  if (!$query) {
    $GLOBALS['message'] = '登录失败，请重试！';
    return;
  }
  //获取登录用户
  $user = mysqli_fetch_assoc($query);
  if (!$user) {
    $GLOBALS['message'] = '用户名不存在';
    return;
  }
  if ($user['password']!==$password) {
    $GLOBALS['message'] = '用户名密码不匹配';
    return;
  }
  //设置登陆标识
  $_SESSION['current_login_user'] = $user;
  header('Location: /admin/');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}

//推出功能，不只是跳转到登录页面，并且要清除session里的内容，再次访问index页面就会直接跳转到登录页面
if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action']==='logout') {
  //删除登录标识，退出后不能直接登录其它页面
  unset($_SESSION['current_login_user']);
} 
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <!-- 可以通过在 form 上添加 novalidate 取消浏览器自带的校验功能 -->
    <!-- autocomplete="off" 关闭客户端的自动完成功能 -->
    <form class="login-wrap<?php echo isset($message) ? ' shake animated' : '' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 作为一个优秀的页面开发人员，必须考虑一个页面的不同状态下展示的内容不一样的情况 -->
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
        <!-- value数据保持功能 -->
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function($){
      //1. 单独作用域
      //2. 确保页面加载完后执行
      var emailFormat = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
      $('#email').on('blur',  function(res) {
        //console.log(this);
        var value = $(this).val();
        if (!value||!emailFormat.test(value)) return;
        // console.log(value);
        //用到AJAX发送请求
        $.get('/admin/api/avatar.php', {email: value},  function(res) {         
          if (!res) return;
          $('.avatar').attr('src', res);
          // console.log(res);
        });
      });
    })
  </script>
</body>
</html>
