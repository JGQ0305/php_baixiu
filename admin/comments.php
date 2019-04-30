<?php 
 require_once '../functions.php';

 xiu_get_current_user ();
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
          <!-- <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li> -->
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
         <!--  <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> -->
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <!-- 模板引擎用法 -->
  <script id="comments_tmpl" type="text/x-jsrender">
    {{for comments}}
        <tr{{if status == 'held'}} class="warning" {{else status == 'rejected'}} class="danger"{{/if}} data-id="{{:id}}">
          <td class="text-center"><input type="checkbox"></td>
          <td>{{:author}}</td>
          <td>{{:content}}</td>
          <td>《{{:posts_title}}》</td>
          <td>{{:created}}</td>
          <td>{{:status}}</td>
          <td class="text-center">
            {{if status == 'held'}}
            <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
            <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>
            {{/if}}
            <a href="javascript:;" class="btn btn-danger btn-xs class="btn-delete>删除</a>
          </td>
        </tr>
    {{/for}}
  </script>
  <script>
    //发送AJAX请求获取所需数据
    // $.get('/admin/api/comments.php', {}, function(res) {
    //   //请求得到响应后自动运行
    //   var html = $('#comments_tmpl').render({comments: res});
    //   $('tbody').html(html);
    // })

    //twbs-pagination的使用
    function loadPageData(page) {
      $('tbody').fadeOut();
      $.getJSON('/admin/api/comments.php', {page: page}, function(res) {
      //请求得到响应后自动运行
      $('.pagination').twbsPagination('destroy');
      $('.pagination').twbsPagination({
      totalePages: res.total_page,
      // visiablePages: 3,
      visiblePages: 3,
      //取消其第一次初始化
      initiateStartPageClick: false,
      onPageClick: function(e, page){
        //第一次初始化时就会触发执行一次
        loadPageData(page);
        // console.log(page);
      }
    })
      // console.log(res);
      var html = $('#comments_tmpl').render({comments: res.comments});
      $('tbody').html(html).fadeIn();
      currentPage = page;//接收当前页数，下面删除功能要用
    })
  }  
    loadPageData(1);
  //删除功能=======
  //由于删除按钮是动态添加的，而且执行动态添加的代码是在次之后执行的，过早注册不上，用其父级元素注册事件，通过子元素判定
  // $('.btn-delete').on('click', function(event) {
     
  // });$('.btn-delete').on('click', function(event) {
     
  // });
  //这是典型的闭包要多加复习
   $('tbody').on('click', '.btn-delete', function(event) {
     //1. 拿到要删除数据的ID
     var tr = $(this).parent().parent();
     var id = tr.data('id');
     //2. 发送AJAX请求告诉服务端要删除的数据
     $.get('/admin/api/comment-delete.php', {id: id}, function(res){
     //3. 根据服务端返回的是否删除成功决定是否在页面上删除元素
     if (!res) return;
     //重新加载这一页的数据，这样更加合理
     loadPageData(currentPage);
     //tr.remove();这是删除这条元素
     })
    
  });
  </script>
  <script>NProgress.done()</script>

</body>
</html>
