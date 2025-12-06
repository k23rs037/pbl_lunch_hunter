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
            <p>本当に通報しますか？</p>
            <div class="pop-btn-area">
                <button onclick="location.href='report.php?id=1'">はい</butto>
                <button popovertarget="confirm" popovertargetaction="hide">いいえ</button>
            </div>
        </div>
    </div>
    <div>
        <h2>アカウント名</h2>
        <div>★★★★</div>
    </div>
    <div class="komennto">
        <textarea name="" id="" cols="30" rows="10"></textarea>
    </div>
    <div class="phot">
        <img src="" alt="未登録">
        <img src="" alt="未登録">
        <img src="" alt="未登録">
    </div>
    <button>ひとつ前へ</button>
    <button>次へ</button>
</body>
</html>