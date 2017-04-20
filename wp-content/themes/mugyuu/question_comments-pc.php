<style type="text/css">
    .answerList label.check:before{
        content: "✓";
    }
    .qaSingle .commentFormArea:before{
        top: 0!important;
    }
    .qaSingle .commentFormArea{
        margin-top:0px;
        padding-top: 20px;
    }
</style>
<?php 
    $limited = get_post_meta( $post->ID, '_limited_answer', true );
    $questions = get_post_meta( $post->ID, '_question_type', true );
    $GLOBALS['questions'] = $questions; 
    global $answers;

?>
<section class="commentArea">
	<label for="qaSort" class="sortWrap">
		<select id="qaSort" name="qaSort" class="sort">
            <option value="new" <?php if($_GET['comment_order_by'] == 'new') echo 'selected' ?>>新着順</option>
            <option value="old" <?php if($_GET['comment_order_by'] == 'old') echo 'selected' ?>>古い順</option>
            <option value="like_count" <?php if($_GET['comment_order_by'] == 'like_count') echo 'selected' ?>>共感順</option>
        </select>
	</label>
	<?php if(have_comments()): ?>
   　<ul class="commentList">
	   <?php 
        $args = array('type' =>'comment','callback' => 'question_comment');
        $resource = null;
        if (isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'new' )
            { $args['reverse_top_level'] = true; }
        elseif (isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'old' )
           { $args['reverse_top_level'] = false; }
        elseif (isset($_GET['comment_order_by']) && $_GET['comment_order_by'] == 'like_count' ) {
            global $wp_query;
            $comment_arr = $wp_query->comments;
            usort($comment_arr, 'comment_comparator');
            $resource = $comment_arr;
        }
        ($resource == null)?wp_list_comments($args):wp_list_comments($args,$resource); 
        ?>
	</ul>
	 <?php endif; ?>
	 <?php
	     if(get_comment_pages_count() > 1){
	         echo '<div style="margin-top:20px; text-align:center;" class="notice_pagination">';
	         //ページナビゲーションの表示
	         paginate_comments_links([
                'next_text'    => __('›'),
                'prev_text'    => __('‹')
                ]);
	         echo '</div>';
	     }
     ?>
</section>
<section id="send" class="commentFormArea">
    <div class="commentFormWrap">
        <div class="ttlArea">
            <h1>アンケートに答える</h1>
            <p>
                <span class="red">※</span>は必須項目になります。
            </p>
        </div>
        <form action="" id="formComment" method="POST">
            <ul class="answerInpotList" >
                <li>
                    <h3>ニックネーム<span class="red">※</span></h3>
                    <input required type="text" name="name" placeholder="ニックネームを入力してください">
                </li>
                <?php 
                    foreach ($questions[$post->ID] as $qkey => $question) {
                        if($question['type'] == 'checkbox'){
                            ?>
                            <li>
                                <h3><?=$question['question']?><span class="red">※</span></h3>
                                <div class="checkArea" >
                                    <?php foreach ($question['answer'] as $anskey => $ansval) {
                                        ?>
                                    <label>
                                        <input required value="<?=$anskey?>" name="answer[<?=$qkey?>][]" type="checkbox" id="option-<?=$anskey?>"><?=$ansval?>
                                    </label>
                                        <?php
                                    } ?>
                                </div>
                            </li>
                            <?php
                        }elseif($question['type'] == 'radio'){
                            ?>
                            <li>
                                <h3><?=$question['question']?><span class="red">※</span></h3>
                                <?php foreach ($question['answer'] as $anskey => $ansval) {
                                    ?>
                                    <label >
                                        <input required value="<?=$anskey?>" name="answer[<?=$qkey?>][]" type="radio" ><?=$ansval?>
                                    </label>
                                <?php
                                } ?>
                            </li>
                            <?php
                        }elseif($question['type'] == 'pulldown'){
                            ?>
                            <li>
                                <h3><?=$question['question']?><span class="red">※</span></h3>
                                <label for="select" class="selectArea">
                                    <select name="answer[<?=$qkey?>][]" id="select">
                                    <?php foreach ($question['answer'] as $anskey => $ansval) {
                                        ?>
                                        <option value="<?=$anskey?>"><?=$ansval?></option>
                                        <?php
                                    } ?>
                                    </select>
                                </label>
                            </li>
                            <?php
                        }elseif($question['type'] == 'textbox'){
                            ?>
                            <li>
                                <h3><?=$question['question']?><span class="red">※</span></h3>
                                <input required name="answer[<?=$qkey?>][textbox]" type="text" placeholder="回答を入力してください" >
                            </li>
                            <?php
                        }elseif($question['type'] == 'textarea'){
                            ?>
                            <li>
                                <h3><?=$question['question']?><span class="red">※</span></h3>
                                <textarea required name="answer[<?=$qkey?>][textarea]" placeholder="回答を入力してください"></textarea>
                            </li>
                            <?php
                        }
                    }
                 ?>
                <li>
                    <h3>コメント<span class="red">※</span></h3>
                    <p class="notes">
                        参考になるような意見を書いてね！誹謗中傷コメントは消しちゃうよ！的な注意コメント入れる
                    </p>
                    <div class="textArea" id="contentArea">
                        <textarea name="comment" id="thread_content" required cols="30" rows="10" name="comment"></textarea>
                        <label class="imgBtn">
                            <i class="fa fa-camera" aria-hidden="true"></i>画像を選択する
                            <input type="file" id="content_image" name="content_image">
                        </label>
                    </div>
                </li>
                <li>
                    <?php 
                        if(count($answers) < $limited){
                         ?>
                        <button type="submit" name="submitted" value="send" class="sendBtn">アンケートに回答する</button>
                    <?php } ?>
                </li>
            </ul>
        </form>
    </div>
</section>
<script type="text/javascript">
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
	var max_upload_picture = "<?php echo get_option('spc_options')['a_img_no']; ?>";

	$('button[type=submit]').on('click',function(){
        $cbx_group = $("input:checkbox[id^='option-']"); // name is not always helpful ;)
        $cbx_group.prop('required', true);
        if($cbx_group.is(":checked")){
          $cbx_group.prop('required', false);
        }
    });
</script>
<script src="<?php bloginfo('template_directory'); ?>/js/notice-board.js"></script>
<?php add_comment_on_questions(get_the_ID()) ?>