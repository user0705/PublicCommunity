<?php 

include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/page.inc.php';
$link=connect();
$user_id=is_login($link);
$is_manage_login=is_manage_login($link);

$query="select * from classroom where id={$_GET['id']}";
$result_father=execute($link, $query);
$data_father=mysqli_fetch_assoc($result_father);
$query="select * from news where classroom_id={$_GET['id']}";
$result_son=execute($link,$query);
$id_son='';
$son_list='';
while($data_son=mysqli_fetch_assoc($result_son)){
	$id_son.=$data_son['id'].',';
}
$id_son=trim($id_son,',');
if($id_son==''){
	$id_son='-1';
}
$query="select count(*) from kk_content where user_id in({$id_son})";
$count_all=num($link,$query);
$query="select count(*) from kk_content where user_id in({$id_son}) and time>CURDATE()";
$count_today=num($link,$query);


$template['title']=$data_father['class_name'];
$template['css']=array('style/public.css','style/list.css');
?>
<?php include 'inc/header.inc.php'?>
<div id="position" class="auto">
	 <a href="index.php">首页</a> &gt; <?php echo $data_father['class_name']?>
</div>
<div id="main" class="auto">
	<div id="left">
		<div class="box_wrap">
			<h3><?php echo $data_father['class_name']?></h3>
			<div class="num">
			</div>	<a class="btn publish"  href="../publish.php?classroom_id=<?php echo $_GET['id']?>" target="_blank">
				发布信息</a>
			<div class="pages_wrap">
			
				<div class="pages">
					<?php 
					$page=page($count_all,20);
					echo $page['html'];
					?>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div style="clear:both;"></div>
		<ul class="postsList">
		<?php $query="select 
kk_content.title,kk_content.id,kk_content.time,kk_content.times,
kk_content.user_id,user.name,news.id ssm_id 
from kk_content,user,news where  
kk_content.user_id=user.id 
 {$page['limit']}";
			$result_content=execute($link,$query);
			while($data_content=mysqli_fetch_assoc($result_content)){
			$data_content['title']=htmlspecialchars($data_content['title']);
// 			$query="select time from kk_con where content_id={$data_content['id']} order by id desc limit 1";
// 			$result_last_reply=execute($link, $query);
// 			if(mysqli_num_rows($result_last_reply)==0){
// 				$last_time='暂无';
// 			}else{
// 				$data_last_reply=mysqli_fetch_assoc($result_last_reply);
// 				$last_time=$data_last_reply['time'];
// 			}
// 			$query="select count(*) from kk_reply where content_id={$data_content['id']}";
			?>
			<li>
				
				<div class="subject">
			<div class="titleWrap">
				<h2><a target="_blank" href="show.php?id=<?php echo $data_content['id']?>"><?php echo $data_content['title']?>
					</a>
				</h2>
			</div>
					<p>
						发布人：<?php echo $data_content['name']?>&nbsp;<?php echo $data_content['time']?>&nbsp;&nbsp;&nbsp;&nbsp;<br />
						<?php 
						if(check_user($user_id,$data_content['user_id'],$is_manage_login)){
							$return_url=urlencode($_SERVER['REQUEST_URI']);
							$url=urlencode("content_delete.php?id={$data_content['id']}&return_url={$return_url}");
							$message="你真的要删除帖子 {$data_content['title']} 吗？";
							$delete_url="confirm.php?url={$url}&return_url={$return_url}&message={$message}";
							echo "<a href='content_update.php?id={$data_content['id']}&return_url={$return_url}'>编辑</a> <a href='{$delete_url}'>删除</a>";
						}
						?>
					</p>
				</div>
				<div class="count">
					<p>
						浏览<br /><span><?php echo $data_content['times']?></span>
					</p>
				</div>
				<div style="clear:both;"></div>
			</li>
<?php }?>
		</ul>
		<div class="pages_wrap">
			<a class="btn publish" href="publish.php?classroom_id=<?php echo $_GET['id']?>" target="_blank">00
			</a>
			<div class="pages">
				<?php 
				echo $page['html'];
				?>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
		<div style="clear:both;"></div>
</div>
<?php include 'inc/footer.inc.php'?>