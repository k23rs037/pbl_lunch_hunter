<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザーログイン</title>
    <link rel="stylesheet" href="css/LoginPage.css"> <!-- 先ほど作ったCSS -->
</head>
<body>

<div class="login-container">

    <div class="login-card">

        <!-- ヘッダー -->
        <div class="login-header">
            <div class="login-header-spacing">
                
                <h1 class="login-title">ログイン</h1>
            </div>
        </div>

        <!-- フォーム -->
        <form class="login-form" action="?do=user_check" method="post">
            <div class="login-field">
                <label>ユーザ名</label>
                <input type="text" name="user_id" class="login-input" required>
            </div>

            <div class="login-field">
                <label>パスワード</label>
                <input type="password" name="user_password" class="login-input" required>
            </div>

            <div class="login-button-container">
                <button type="submit" class="login-button">送信</button>
                <button type="reset" class="login-button" style="background-color:#dc2626;">取消</button>
            </div>
        </form>

    </div>

</div>

</body>
</html>
