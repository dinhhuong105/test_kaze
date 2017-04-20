<?php
function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   // fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}


$param = array(
    'post_id'=>$post->ID
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
$post_metas = get_post_meta($_GET['post'],'_question_type', TRUE);
$csv = array();
 foreach ($post_metas[$_GET['post']] as $key => $value){
	$csv[$key]['設問 '.$key.'.'] = $value['question'];
	if(isset($value['answer'])){ 
		foreach ($value['answer'] as $k_ques => $ans){
			$csv[$key][$ans] = $report_ans[$key][$k_ques];
		}
	}else{
		foreach ($report_ans[$key] as $answer => $count){
			$csv[$key][$answer] = $count;
		}
	}
}



download_send_headers("data_export_" . date("Y-m-d") . ".csv");
$a = array ();

foreach ($csv as $value) {
	foreach ($value as $k => $v) {
		array_push($a,array($k,$v));
	}
}
echo array2csv($a);

		// print_r($a);
die();


