<?php
//ログインチェック
require_once 'model.php';
$model = new User();
if (!empty(trim($_POST['user_id'])) && !empty(trim($_POST['user_password']))) {
    $user_id = $_POST['user_id'];
    $user_password = $_POST['user_password'];
    $where = ['user_id' => $user_id, 'password' => $user_password];
    $user = $model->get_userDetail($where);
    if ($user) {
        if($user['usertype_id'] == 2){
            $_SESSION['error'] = true;
            $_SESSION['error_msg'] = '停止中のアカウントです';
            header('Location:?do=user_login');
            exit;
        } else {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_account'] = $user['user_account'];
        $_SESSION['user_password'] = $user['password'];
        $_SESSION['usertype_id'] = $user['usertype_id'];
        header('Location:index.php');
        exit;
        }
    } else {
        $_SESSION['error'] = true;
        $_SESSION['error_msg'] = 'ユーザ名またはパスワードが間違えています';
        header('Location:?do=user_login');
        exit;
    }
} else {
    $_SESSION['error'] = true;
    $_SESSION['error_msg'] = '必須項目が未入力です';
    header('Location:?do=user_login');
    exit;
}