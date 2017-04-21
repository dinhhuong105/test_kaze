<?php
global $post;
$id = isset($post->ID)?$post->ID:$_GET['post'];
$param = array(
    'post_id'=> $id
);
$comments = get_comments($param);
$answer = array();
$comment_metas = array();
$question = array();
$number_answer = 0;
foreach ($comments as $comment) {
	$comment_metas[] = get_comment_meta($comment->comment_ID,'_question_comment',true);
}

foreach ($comment_metas as $key => $answ) {
	
	if($answ){
		$number_answer +=1;
		foreach ($answ as $id_ques => $ans_detail) {
			if(isset($question[$id_ques])){
				array_push($question[$id_ques],$ans_detail);
			}else{
				$question[$id_ques][0] = $ans_detail;
			}
		}
	}
}
$report_ans = array();
foreach ($question as $key => $value) {
	$_ans = array();
	foreach ($value as $v) {
		// if()
		foreach ($v as $type => $id_ans) {
			array_push($_ans,$id_ans);
		}
		
	}
	$report_ans[$key] = array_count_values($_ans);
}
// global $post_metas;
$post_metas = get_post_meta($id, '_question_type', TRUE);
$_limited_answer = get_metadata('post', $id, '_limited_answer');
$csv = array();
?>
<style type="text/css">
	.report{
	    padding: 20px;
    	border: 1px solid #ccc;
	}
	.report ul li{
		padding-left: 20px;
		list-style-type: decimal;
	}
	.page-title-action{
	    margin-left: 4px;
	    padding: 4px 8px;
	    position: relative;
	    top: -3px;
	    text-decoration: none;
	    border: none;
	    border: 1px solid #ccc;
	    -webkit-border-radius: 2px;
	    border-radius: 2px;
	    background: #f7f7f7;
	    text-shadow: none;
	    font-weight: 600;
	    font-size: 13px;
	    cursor: pointer;
	}
	.page-title-action:hover {
	    border-color: #008EC2;
	    background: #00a0d2;
	    color: #fff;
	}
	.postbox{
		padding: 20px;
		margin-top: 20px;
		position:relative;
	}
	.btn{
		position: absolute;
		right: 20px;
	}
</style>
<?php if($post_metas): ?>
<div class="row postbox" id="revisionsdiv">
<div class="btn">
<span id="loading"></span>
	<button class="btn-limit  page-title-action" data-post="<?=$id?>" data-status="<?=$_limited_answer[0]?>"><?=($_limited_answer[0] > 0)?'回答受付中':'停止中'?></button>
	<button class="btn-public page-title-action" data-post="<?=$id?>" data-status="<?=get_post_status($id)?>" >Publishing</button>
</div>
<h2 class="hndle ui-sortable-handle"><span>アンケート詳細</span></h2>
	<div id="frm_question">
	<?php 
		foreach ($post_metas[$id] as $key => $meta) {
	echo "<pre>"; print_r($post_meta); echo "</pre>";

				if($meta['type'] == 'checkbox'){
					echo '<div class="box-question"><a class="btn_remove">x</a>';
					echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
					echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目 </label>';
					echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><br/>';
					$i=0;
					foreach ($meta['answer'] as $answer) {
						echo '<input type="checkbox" name="posid_'. $key .'_answer_'. $id .'_' . $i . '"> 
						<input type="text" name="question['. $key .']['. $id .'][answer]['.$i.']" value="'.$answer.'"><br/>';
						$i++;
					}
					echo '</div>';
				}elseif($meta['type'] == 'radio'){
					echo '<div class="box-question"><a class="btn_remove">x</a>';
					echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
					echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
					echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><br/>';
					$i=0;
					foreach ($meta['answer'] as $answer) {
						echo '<input type="radio" name="posid_'. $key .'_answer_'. $id .'"> 
						<input type="text" name="question['. $key .']['. $id .'][answer]['.$i.']" value="'.$answer.'"><br/>';
						$i++;
					}
					echo '</div>';
				}elseif($meta['type'] == 'pulldown'){
					echo '<div class="box-question"><a class="btn_remove">x</a>';
					echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
					echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
					echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><br/>';
					$i=0;
					foreach ($meta['answer'] as $answer) {
						echo '<input type="text" name="question['. $key .']['. $id .'][answer]['.$i.']" value="'.$answer.'"><br/>';
						$i++;
					}
					echo '</div>';
				}elseif($meta['type'] == 'textbox'){
					echo '<div class="box-question"><a class="btn_remove">x</a>';
					echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
					echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
					echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><br/>';
					echo '</div>';
				}elseif($meta['type'] == 'textarea'){
					echo '<div class="box-question"><a class="btn_remove">x</a>';
					echo '<input type="hidden" name="question['. $key .']['. $id .'][type]" value="'.$meta['type'].'">';
					echo '<label for="posid_'. $key .'_question_' . $id . '">アンケート項目</label>';
					echo '<input id="posid_'. $key .'_question_' . $id . '" type="text" name="question['. $key .']['. $id .'][question]" value="'.$meta['question'].'" required><br/>';
					echo '</div>';
				}
				
		}
	?>

	
</div>
</div>
<div class="row postbox" id="revisionsdiv">

	<ul>
		<li class="report">
			<label>回答数</label><br/><b><?=$number_answer?></b>件
		</li>
		<?php 
		
		foreach ($post_metas[$id] as $key => $value): 
			$csv[$key]['question'] = $value['question'];
		?>
			<li class="report">
				<label>設問 <?=$key+1?></label><br/>
				<h2 class="hndle ui-sortable-handle"><?=$value['question']?></h2><br/>
				<ul>
				<?php 
				if(isset($value['answer'])) :
					foreach ($value['answer'] as $k_ques => $ans): 
						$csv[$key][$ans] = $report_ans[$key][$k_ques];
						?>
						<li><?=$ans?> ... <?=$report_ans[$key][$k_ques]?></li>
				<?php endforeach;
				else:
					?>
					<?php foreach ($report_ans[$key] as $answer => $count): 
						$csv[$key][$answer] = $count;
					?>
						<li><?=$answer?> ... <?=$count?></li>
					<?php endforeach ?>
				<?php
				endif
				?>
				</ul>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<div class="exportCSV"> 
	<a class="page-title-action" href="/wp-admin/admin-post.php?action=exportcsv&post=<?=$id?>"> CSV出力 </a>
</div>
<?php endif;
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.btn-limit').on('click',function(e){
		e.preventDefault();
		var post_id = $(this).attr('data-post');
		var status = $(this).attr('data-status');
		var $button = $(this);
		$button.parent().find('#loading').append('処理...');
		$button.attr("disabled", true);
		$.ajax({
			type: 'POST',
			url : ajaxurl,
			data:{
				'action' : 'limited_comment',
				'post_ID' : post_id,
				'status' : status
			},
			success:function(res){
				console.log(res);
				if(res['status'] < 0){
					$button.html('停止中');
				}else{
					$button.html('回答受付中');
				}
				$button.attr('data-status',res['status']);
				$button.attr("disabled", false);
				$button.parent().find('#loading').empty();
			}
		});
	});
	$('.btn-public').on('click',function(e){
		e.preventDefault();
		var post_id = $(this).attr('data-post');
		var status = $(this).attr('data-status');
		var $button = $(this);
		$button.parent().find('#loading').append('処理...');
		$button.attr("disabled", true);
		$.ajax({
			type: 'POST',
			url : ajaxurl,
			data:{
				'action' : 'post_status',
				'post_ID' : post_id,
				'status' : status
			},
			success:function(res){
				console.log(res);
				if(res['status'] == 'publish'){
					$button.html('publish');
				}else{
					$button.html('private');
				}
				$button.attr('data-status',res['status']);
				$button.attr("disabled", false);
				$button.parent().find('#loading').empty();
			}
		});
	});
});
</script>

