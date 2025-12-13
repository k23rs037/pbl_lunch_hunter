<?php
require_once('model.php');
$report = new Report();
$review_id = $_GET['rid'];
//理由を判定
if($_POST['reasoncomment']==1&&$_POST['reasonphoto']==1){
    $reason = 3;
}elseif($_POST['reasoncomment']==1){
    $reason = 1;
}elseif($_POST['reasonphoto']==1){
    $reason = 2;
}else{
    
}
//該当口コミのレストランを取得
$rst_id = $_GET['rst_id'];

$report_data= array(
    'review_id'=> $_GET['rid'], 
//    'report_time'=> 'NOW()',
    'user_id'=> $_SESSION['user_id'], 
    'report_reason'=> $reason, 
    'report_state'=> 1,
    );
//データベースに登録
$report ->insert($report_data);
header('Location:?do=rst_detail&rst_id='.$rst_id);
?>