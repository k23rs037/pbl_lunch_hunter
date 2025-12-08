<?php
require_once 'model.php';
$model = new User();
//POSTを変数に格納
$user = array(
    'user_id' =>$_POST['user_account'],
    'password' =>$_POST['pass'],
    'user_l_name'=>$_POST['user_sei'], 
    'user_f_name'=>$_POST['user_mei'], 
    'user_l_kana'=>$_POST['user_kanasei'], 
    'user_f_kana'=>$_POST['user_kanamei'], 
    'user_account'=>$_POST['user_sei'],
    'usertype_id'=>1
);
//テストコード
//print_r($user);
//DBに登録
$model ->insert($user);
//header('Location:?do=user_input');
header('Location:?do=user_input');

?>