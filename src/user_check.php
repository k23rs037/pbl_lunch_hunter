<?php
//ログインチェック
require_once 'model.php';
$model = new User();

$user_id = $_POST['user_id'];
$user_password = $_POST['user_password'];
$where = ['user_id'=>$user_id ,'password' =>$user_password];
$user = $model->get_userDetail($where);
if($user){
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_account'] = $user['user_account'];
    $_SESSION['user_password'] = $user['password'];
    $_SESSION['usertype_id'] = $user['usertype_id'];
    header('Location:index.php');
} else {
    header('Location:?do=user_login');
}
?>