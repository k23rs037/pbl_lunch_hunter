<?php
require_once('model.php');
$model = new User();
//エラー処理メソッド

//userのセッションを確認
$userold = $model ->getDetail("user_id='{$_SESSION['user_id']}'");

//myeditの入力を受け取り
$user = array(
    'user_account' => $_POST['user_account'],
    'password' => $_POST['newpass']??null,
);
$pass = $_POST['pass'];
$newpass = $_POST['newpass'];
$newpasscheck = $_POST['newpasscheck'];

//userのパスワードと一致しているかを確認
if($pass == $userold['password']){
    //新規パスワードが一致しているかを確認
    if($newpass == $newpasscheck){
        //送信データを保存
        if($user['password']==null){
            $model ->update(['user_account' => $user['user_account']],"user_id='{$userold['user_id']}'");
        }else{
            $model ->update($user,"user_id='{$userold['user_id']}'");
        }
        header('Location:?do=user_myedit');
        exit;
    }else{
        //エラー処理
        header('Location:?do=user_myedit&error=1');
        exit;
    }
}else{
//エラー処理
header('Location:?do=user_myedit&error=2');
exit;
}


//遷移
//header('Location:?do=user_myedit');


?>