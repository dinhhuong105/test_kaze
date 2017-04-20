<style type="text/css">
	.wp_comment_list{
		overflow-x: scroll;
	}
	.wp_comment_list tbody tr:nth-child(odd){
		background-color: #f9f9f9;
	}
	.wp_comment_list tbody tr td{ padding: 5px 10px }
	.wp_comment_list thead th{
		min-width: 130px;
		padding: 11px 0 0 3px;
		border-top: 1px solid #ccc;
		border-bottom: 1px solid #ccc;
	}
	.pagination-links{
	    text-align: right;
		margin: 20px;

	}
	.exportCSV{
		text-align: right;
		margin: 20px;
	}
	.pagination-links .page-numbers{
		display: inline-block;
	    min-width: 17px;
	    border: 1px solid #ccc;
	    padding: 7px 5px 7px;
	    background: #e5e5e5;
	    font-size: 14px;
	    line-height: 1;
	    font-weight: 400;
	    text-align: center;
	    text-decoration: none;
	}
	.small {
	    height: 36px;
	    overflow:hidden;
	}
	.big {
	    height: auto;
	}
</style>
<?php 
	define('DEFAULT_COMMENTS_PER_PAGE',5);
	$id=$post->ID;

	$page = isset($_GET['paged']) ? $_GET['paged'] : 1; 
	// $page=2;
	$limit = DEFAULT_COMMENTS_PER_PAGE;

	$offset = ($page * $limit) - $limit;

	$param = array(

	    /*'status'=>'approve',*/

	    'offset'=>$offset,

	    'post_id'=>$id,

	    'number'=>$limit,

	);
	$total_comments = get_comments(array('orderby' => 'post_date' ,

	            'order' => 'DESC',

	            'post_id'=>$id,

	           /*'status' => 'all',*/

	            'parent'=>0));

	$pages = ceil(count($total_comments)/DEFAULT_COMMENTS_PER_PAGE);
	$comments = get_comments($param );

	$args = array(

	'base'         => @add_query_arg('paged','%#%'),

	'format'       => '?paged=%#%',

	'total'        => $pages,

	'current'      => $page,

	'show_all'     => False,

	'end_size'     => 1,

	'mid_size'     => 2,

	'prev_next'    => True,

	'prev_text'    => __('‹'),

	'next_text'    => __('›'),

	'type'         => 'plain');

	$question_meta = get_post_meta($post->ID, '_question_type', TRUE);
	
	
?>
<div class="wp_comment_list">
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th>
				回答日
			</th>
			<th>
				ニックネーム
			</th>
			<?php
				for ($i=1; $i <= count($question_meta[$post->ID]); $i++) { 
					?>
					<th>
						設問<?=$i?>の回答 
					</th>
					<?php
				}
			?>
			<th style="min-width: 200px;">
				コメント
			</th>
			<th>
				削除
			</th>
		</tr>
	</thead>
	<tbody id="the-list">
		<?php foreach ($comments as $comment): ?>
		<tr>
			<td><?=$comment->comment_date?></td>
			<td><?=$comment->comment_author?></td>
			<?php
				$comment_metas = get_comment_meta($comment->comment_ID,'_question_comment',TRUE);
				
				for ($i=0; $i < count($question_meta[$post->ID]); $i++) { 					
					?>
					<td>
						<?php 
							if($comment_metas && count($comment_metas[$i]) != 0){
								foreach ($comment_metas[$i] as $id_answer => $val_answer) {
									echo ($question_meta[$post->ID][$i]['answer'][$val_answer])?$question_meta[$post->ID][$i]['answer'][$val_answer]:$val_answer;
									if(count($question_meta[$post->ID][$i]['answer']) > 2) echo ", ";
								}
							}else{
								echo "---";
							}
						?>
					</td>
					<?php
				}
			?>
			<td>
				<div class="wrapper">
    				<div class="small">
						<?=$comment->comment_content?> 
					</div>
					<?php if(strlen($comment->comment_content) > 65): ?>
					<a href="#">...Click to read more</a>
					<?php endif?>
				</div>
			</td>
			<td><button class="btn-public" data-status="<?=$comment->comment_approved?>" data-comment="<?=$comment->comment_ID?>"><?=($comment->comment_approved)?'公開停止':'公開中'?></button><span class="loading"></span></td>
		</tr>
		<?php endforeach ?>
		
		
	</tbody>
</table>
</div>

<div class="pagination-links">
	<?=paginate_links( $args )?>
</div>
<?php 
	wp_enqueue_script('jquery'); 
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.wrapper').on('click','a[href=#]', function (e) {
	    e.preventDefault();
	    $(this).closest('.wrapper').find('.small').toggleClass('big');
	    return false;
	});

	$('#the-list').on('click','.btn-public',function(e){
		e.preventDefault();
		var comment_ID = $(this).attr('data-comment');
		var status = $(this).attr('data-status');
		var $button = $(this);
		$button.parent().find('.loading').append('処理...');
		$button.attr("disabled", true);
		$.ajax({
			type: 'POST',
			url : ajaxurl,
			data:{
				'action' : 'update_comment',
				'comment_ID' : comment_ID,
				'status' : status
			},
			success:function(res){
				if(status == 1){
					$button.html('公開中');
					$button.attr('data-status',0);
				}else{
					$button.html('公開停止');
					$button.attr('data-status',1);
				}
				$button.attr("disabled", false);
				$button.parent().find('.loading').empty();
			}
		});
	});
	/*$('#btn-csv').on('click',function(e){
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url : ajaxurl,
			data:{
				'action' : 'exportcsv',
			},
			success:function(res){
				console.log(res);
				console.log(ajaxurl);
			}
		});
	})*/
});
</script>