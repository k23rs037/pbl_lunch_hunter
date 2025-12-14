<?php
$error = $_SESSION['error'] ?? false;
$msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['error']);
unset($_SESSION['error_msg']);
?>
<style>
    /* Login Page Styles */

    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(to bottom right, #f0fdf4, #ffffff, #f0fdf4);
        padding: 1rem;
    }

    .login-card {
        width: 100%;
        max-width: 28rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .login-header {
        padding-bottom: 2rem;
        text-align: center;
    }

    .login-header-spacing {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .login-icon-container {
        display: flex;
        justify-content: center;
    }

    .login-icon-bg {
        background-color: #16a34a;
        color: white;
        padding: 1rem;
        border-radius: 9999px;
    }

    .login-icon {
        width: 3rem;
        height: 3rem;
    }

    .login-title {
        font-size: 2.25rem;
        line-height: 2.5rem;
    }

    .login-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .login-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .login-input {
        height: 3rem;
    }

    .login-button-container {
        padding-top: 1rem;
    }

    .login-button {
        width: 100%;
        height: 3rem;
        background-color: #16a34a;
    }

    .login-button:hover {
        background-color: #15803d;
    }

    .login-demo-info {
        padding-top: 1rem;
        text-align: center;
        font-size: 0.875rem;
        line-height: 1.25rem;
        color: hsl(var(--muted-foreground));
        border-top: 1px solid hsl(var(--border));
    }

    .login-demo-info-text {
        margin-top: 1rem;
    }
</style>
<div class="login-container">

    <div class="login-card">
        <!-- ヘッダー -->
        <div class="login-header">
            <div class="login-header-spacing">
                <h1 class="login-title">ログイン</h1>
            </div>
        </div>
        <?php
        if (!empty($error)) {
            echo '<h2 style="color:red">'.$msg.'</h2>';
        }
        ?>
        <!-- フォーム -->
        <form class="login-form" action="?do=user_check" method="post">
            <div class="login-field">
                <label>ログインID</label>
                <input type="text" name="user_id" class="login-input" value="<?= htmlspecialchars($old['store_name'] ?? '') ?>">
            </div>

            <div class="login-field">
                <label>パスワード</label>
                <input type="password" name="user_password" class="login-input">
            </div>

            <div class="login-button-container">
                <button type="submit" class="login-button">送信</button>
                <button type="reset" class="login-button" style="background-color:#dc2626;">取消</button>
            </div>
        </form>

    </div>

</div>