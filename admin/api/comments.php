<?php 

 require_once '../../functions.php';
 
 $page = empty($_GET['page']) ? 1 :intval($_GET['page']);
 $length = 2;
 $offset = ($page-1) * $length;
 //查询数据库所有评论
 $sql = sprintf('select 
 	comments.*, 
 	posts.title as posts_title
 	from comments
 	inner join posts on comments.post_id = posts.id
 	order by comments.created desc
 	limit %d, %d;', $offset, $length);

 $comments = xiu_fetch_all($sql);
 //输出的comment:false  不知道什么原因
 // $comments = xiu_fetch_all('select 
 // 	comments.*, 
 // 	posts.title as posts_title
 // 	from comments
 // 	inner join posts on comments.post_id = posts.id
 // 	order by comments.created desc
 // 	limit {$offset}, {length};');

 $total_count = xiu_fetch_one('select count(1) as count
 	from comments
 	inner join posts on comments.post_id = posts.id;')['count'];

 $total_page = ceil($total_count / $length);

 //网络之间只能传递字符串，将数据转换成字符串（序列化）
 $json = json_encode(array(
 	'total_page' => $total_page, 
	'comments' => $comments
));

 //设置响应的响应体类型为json
 header('Content-Type: application/json');

 //响应给客户端
 echo $json;
