
<!--
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
            <button onclick="location.href='rev_report.php?id=1'">yes</butto>
            <button popovertarget="confirm" popovertargetaction="hide">no</button>
        </div>
    </div>
</div>
-->
<!-- 通報ボタン -->
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
<h1>口コミ個別表示</h1>

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
            '<input type="hidden" name="reason[]" value="1">'
        );
    }
    if (c2) {
        form.insertAdjacentHTML('beforeend',
            '<input type="hidden" name="reason[]" value="1">'
        );
    }

    form.submit();
}
</script>
<?php
//確認リンク http://localhost/dashboard/pbl_lunch_hunter/index.php?do=rev_detail&rid=0001
require_once('model.php');
$modelR = new Review();
$modelU = new user();
//レビューをリンクから取得
$review_id = $_GET['rid'];
//レビューをリンクから取得
$review = $modelR -> getDetail("review_id =". $review_id);
print_r($review);
echo $review['review_id'];
//レビューから口コミ投稿者の名称を取得
$user = $modelU -> getDetail("user_id='".$review['user_id']."'");
?>

<?php
echo '<button><a href="?do=rst_detail&rst_id='.$review['rst_id'].'">戻る</a></button>';
if($_SESSION['usertype_id']==1){
    echo '<form id="reportForm" action=?do=rev_detail_rpsave&rid='.$review_id.'&rst_id='.$review['rst_id'].' method="post">
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

<div>
    <h3><?php echo $user['user_account']; ?>
    <div>
        <span style="font-size: 2em; color: yellow;">
        <?php
        for ($i=0; $i<$review['eval_pint']; $i++){
            echo '★';
        }
        for ($i=5; $i>$review['eval_pint']; $i--){
            echo '☆';
        }
        ?>
        </span>
    </div></h3>
</div>
<div>
    <?php
    echo $review['review_comment'];
    ?>
</div>
<div>
    <?php
    if (!empty($row['photo'])) {
        $img64 = base64_encode($review['photo1']);
        $mime  = $row['mime_type'];  // 例： image/jpeg, image/png

        echo '<img src="data:' . $mime . ';base64,' . $img64 . '" style="max-width:300px;" />';
    } else {
        echo "画像なし";
    }
    if (!empty($row['photo3'])) {
        $img64 = base64_encode($review['photo1']);
        $mime  = $row['mime_type'];  // 例： image/jpeg, image/png

        echo '<img src="data:' . $mime . ';base64,' . $img64 . '" style="max-width:300px;" />';
    } else {
        echo "画像なし";
    }
    if (!empty($row['photo3'])) {
        $img64 = base64_encode($review['photo1']);
        $mime  = $row['mime_type'];  // 例： image/jpeg, image/png

        echo '<img src="data:' . $mime . ';base64,' . $img64 . '" style="max-width:300px;" />';
    } else {
        echo "画像なし";
    }
    ?>

</div>

<button>ひとつ前へ</button>
<button>次へ</button>
