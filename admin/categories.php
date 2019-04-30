<?php   
require_once '../functions.php';
xiu_get_current_user();
//判断是否编辑功能 
if (!empty($_GET['id'])) {
  //根据URL中是否有id参数来判断是更新请求还是编辑请求
  $current_edit_category = xiu_fetch_one('select * from categories where id = ' . $_GET['id']);
}
//添加功能
function add_category() {
  if (empty($_POST['name'])||empty($_POST['slug'])) {
    $GLOBALS['message'] = '请输入完整表格';
    $GLOBALS['success'] = false;
    return;
  }
  $name = $_POST['name'];
  $slug = $_POST['slug'];
  $rows = xiu_execute("insert into categories value(null, '{$name}', '{$slug}');");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ?'添加失败' : '添加成功';
}
//编辑功能
function edit_category() {
  //声明全局变量
  global $current_edit_category;
  //不用判断，默认显示原有的数据
  // if (empty($_POST['name'])||empty($_POST['slug'])) {
  //   $GLOBALS['message'] = '请输入完整表格';
  //   $GLOBALS['success'] = false;
  //   return;
  // }
  //判断如果没有更新数据就显示原来数据
  $id = $current_edit_category['id'];
  $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
  $current_edit_category['name'] = $name;
  $slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
  $current_edit_category['slug'] = $slug;
  $rows = xiu_execute("update categories set slug = '{$slug}', name = '{$name}' where id = {$id};");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ?'修改失败' : '修改成功';
}


if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (empty($_GET['id'])) {
    add_category();
  }else{
    edit_category();
  }
}

$categories = xiu_fetch_all('select * from categories');

// $arrayName = array('' => , );
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <?php if ($success): ?>
          <div class="alert alert-success">
          <strong>成功！</strong><?php echo $message ?>
          </div>
        <?php else: ?>
          <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message ?>
          </div>
        <?php endif ?>
      <?php endif ?>
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit_category)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['id'] ?>" method="post">
            <h2>编辑<<?php echo $current_edit_category['name'] ?>></h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称"
              value="<?php echo $current_edit_category['name'] ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug'] ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
          <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/categories_delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>

                <tr>
                <!-- 自定义属性data-  的形式 --> 
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>"></td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/categories_delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
                
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    // if(-1){
    //   console.log('-1 is true');
    // }else {
    //   console.log('-1 is false');
    // }
    $(function($) {
      //表格中任意一个checkbox被选中时的状态
      var $tbodyCheckboxs = $('tbody input');
      var $btndelete = $('#btn_delete');
      var allCheckeds = [];
      $tbodyCheckboxs.on('change', function(){
        //有任意一个CheckBox被选中就显示
        //versionⅠ不采用这种方法
        // $tbodyCheckboxs.each(function(i, item) {
        //   //attr 与 prop区别
        //   //attr访问的是元素属性
        //   //prop访问的是元素对应的DOM 对象的属性
        //   //编程时候要将DOM对象转换成jquery对象
        //   // console.log($(item).prop('checked'));
        // })
        var id = $(this).data('id');
        if ($(this).prop('checked')) {
          //这里存在一个bug，当先选中一个再选中全选是id重复
          // allCheckeds.indexOf(id) === -1 && allCheckeds.push(id);
          allCheckeds.includes(id) || allCheckeds.push(id);
        }else {
          allCheckeds.splice(allCheckeds.indexOf(id), 1);
        }
        //删除
        allCheckeds.length ? $btndelete.fadeIn() : $btndelete.fadeOut();
        // $btndelete.attr('href', '?id=' + allCheckeds);
        //a链接里的search属性
        $btndelete.prop('search', '?id=' + allCheckeds);
      })

      //全选按钮功能
      $('thead input').on('change', function(){
        //1. 获取选中当前状态
        var checked = $(this).prop('checked');
        //2. 将当前状态设置给每一个CheckBox
        $tbodyCheckboxs.prop('checked', checked).trigger('change');
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
