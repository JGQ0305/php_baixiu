<?php 
require_once '../functions.php';
xiu_get_current_user();
//接收筛选参数，分类筛选
$where = '1 = 1';
$search = '';//将筛选和分页功能综合到一起，既要有？page分页参数，也需要筛选的参数
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .= '&category=' . $_GET['category'];
}
//状态筛选
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .= '&status=' . $_GET['status'];
}
//获取数据库中的所有数据，打印在下面列表中
//数据库关联查询，减少查询次数
// $posts = xiu_fetch_all('select * from posts;');
//as可以省略
//处理分页显示问题
//$_GET[]得到的是字符串类型，要转换成整型

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
//没有设置它的范围，但是页面没有报错
//当筛选完再点击上下一页会出现问题
$nextpage = $page + 1;
$prepage = $page - 1;
if ($page < 1) {
  header('Location:/admin/posts.php?page=1' . $search);
}
$size = 20;
//计算总页数
//分类筛选时候这里要加上where条件,还要注意代码执行顺序的问题
$total_count = (int)xiu_fetch_one("select count(1) as num from posts 
  inner join categories on posts.category_id = categories.id
  inner join users on posts.user_id = users.id
  where {$where};")['num'];
//ceil()得到数据类型是float，要转换成int
$total_page = (int)ceil($total_count / $size);
if ($page > $total_page) {
  header('Location:/admin/posts.php?page=' . $total_page . $search);
}


//筛选功能
$categories = xiu_fetch_all('select * from categories;');
//分页
$offset = ($page - 1) * $size;
$posts = xiu_fetch_all("select
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  users.nickname as user_name,
  categories.name as category_name
  from posts 
  inner join categories on posts.category_id = categories.id
  inner join users on posts.user_id = users.id
  where {$where}
  order by posts.created desc
  limit {$offset}, {$size};");


//处理页码

//计算页码
$visiables = 5;
$region = ($visiables - 1) / 2;
$begin = $page - $region;
$end = $begin + $visiables;

//要保证$begin>0
// if ($begin < 1) {
//   $begin = 1;
//   //保证两者之间差4
//   $end = $begin + $visiables;
// }
$begin = $begin < 1 ? 1 : $begin;
$end = $begin + $visiables;
//同时$end必须小于最大页数
//当页面小于5页时，$begin会等于-1，所以要再次判断其合理性
// if ($end > $total_page + 1) {
//   $end = $total_page + 1;
//   $begin = $end - $visiables;
//   if ($begin < 1) {
//     $begin = 1;
//   }
// }
$end = $end > $total_page + 1 ? $total_page + 1 : $end;
$begin = $end -$visiables;
$begin = $begin < 1 ? 1 : $begin;
//转换函数将英文状态转换成中文状态
function convert_status($status) {
  $dict = array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
 );
  return isset($dict[$status]) ? $dict[$status] : '未知状态'; 
}
function convert_time($created) {
  $timestamp = strtotime($created);
  //br不能直接用，r特殊符号要用转移符
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
            <option value="<?php echo $item['id'] ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : '' ?>><?php echo $item['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : '' ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="?page=<?php echo $prepage ?>">上一页</a></li>
          <?php for ($i = $begin; $i < $end; $i++): ?>
          <!-- class前面一定要加空格，否则报错 -->
          <!-- 点击链接会传递?page参数 -->
          <li<?php echo $i === $page ? ' class="active"' : ''; ?>><a href="?page=<?php echo $i . $search ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>
          <li><a href="?page=<?php echo $i . $search ?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
            <tr>
              <td class="text-center"><input type="checkbox"></td>
              <td><?php echo $item['title'] ?></td>
              <td><?php echo $item['user_name'] ?></td>
              <td><?php echo $item['category_name'] ?></td>
              <td class="text-center"><?php echo convert_time($item['created']) ?></td>
              <td class="text-center"><?php echo convert_status($item['status']) ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="/admin/posts_delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
