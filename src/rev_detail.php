<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="kuchokomi">
        <h1>口コミ個別表示</h1>
        <button>戻る</button>
        <button popovertarget="confirm">通報</button>
        <div id="confirm" popover>
            <div>通報理由</div>
            <div>どちらか一つを選択してください。</div>
            <div class="pop-btn-area">
                <div>
                    <label>
                        <input type="radio">写真
                    </label>
                    <label>
                        <input type="radio">コメント
                    </label>
                </div>
                <div>本当に通報しますか。</div>    
                <button onclick="location.href='report.php?id=1'">yes</butto>
                <button popovertarget="confirm" popovertargetaction="hide">no</button>
            </div>
        </div>
    </div>
    <div>
        <h2>アカウント名</h2>
        <div>★★★★</div>
    </div>
    <div class="komennto">
        <textarea name="" id="" cols="30" rows="10"></textarea>
        <img src="" alt="未登録">
        <img src="" alt="未登録">
        <img src="" alt="未登録">
    </div>
    <button>ひとつ前へ</button>
    <button>次へ</button>
</body>
</html>
<!-- 上部ボタンなど -->
<style>
/* --- モーダル背景 --- */
#modalBg {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

/* --- モーダル本体 --- */
#modalBox {
    background: #fff;
    width: 300px;
    padding: 20px;
    border-radius: 10px;
    margin: 150px auto;
    text-align: center;
}
</style>
<h3>口コミ個別表示<h3>

<script>
// モーダルを開く
function openModal() {
    document.getElementById('modalBg').style.display = 'block';
}

// モーダルを閉じる
function closeModal() {
    document.getElementById('modalBg').style.display = 'none';
}

// YESボタン押下 → チェック確認 → 送信
function submitReport() {
    const c1 = document.getElementById("reason_comment").checked;
    const c2 = document.getElementById("reason_photo").checked;

    if (!c1 && !c2) {
        alert("通報理由を1つ以上選択してください。");
        return; // モーダル閉じずにそのまま
    }

    // hidden input を生成してフォームに追加（送信用）
    const form = document.getElementById("reportForm");
    if (c1) {
        form.insertAdjacentHTML('beforeend',
            '<input type="hidden" name="reason[]" value="comment">'
        );
    }
    if (c2) {
        form.insertAdjacentHTML('beforeend',
            '<input type="hidden" name="reason[]" value="photo">'
        );
    }

    form.submit();
}
</script>

<?php
echo '<button><a href="?do=rst_detail">戻る</a></button>';
if($_SESSION['usertype_id']==1){
    echo '<form id="reportForm" action="send_report.php" method="post">
    <button type="button" onclick="openModal()">通報する</button>
</form>';
}elseif($_SESSION['usertype_id']==9){
    echo '<button><a href="?do=rst_detail">非表示</a></button>';
}
# localhost\dashboard\pbl_lunch_hunter\src\rev_detail.php

?>
<div id="modalBg">
    <div id="modalBox">
        <p>通報理由（1つ以上選択してください）</p>

        <label><input type="checkbox" id="reason_comment"> コメント</label><br>
        <label><input type="checkbox" id="reason_photo"> 写真</label><br><br>

        <p>本当に通報しますか？</p>

        <button onclick="submitReport()">YES</button>
        <button onclick="closeModal()">NO</button>
    </div>
</div>
<?php
require_once('model.php');
$model = new Review();

//$review[] = $model -> getDetail("reviewid = 'rid'");
//print_r($review);
//echo $review['comment'];

echo "<table>";
echo "<tr><td>アカウント名</td></tr>";
echo "<tr><td>★★</td></tr>";
echo "<tr><td>コメント</td></tr>";
echo "</table>";

?>