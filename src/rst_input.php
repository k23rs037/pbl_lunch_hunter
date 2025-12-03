<?php
$form_action_url = 'store_registration.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunch Hunter - 店舗登録</title>
    <style>
        .form-group { margin-bottom: 20px; }
        .required-star { color: red; margin-left: 5px; font-weight: bold; }
        .optional-hash { color: blue; margin-left: 5px; font-weight: bold; }
        .header-menu a { margin-right: 15px; }
        .registration-container {
            display: flex;
            gap: 40px;
        }
        .left-col, .right-col {
            flex: 1;
        }
    </style>
</head>
<body>

    <header>
        <h1>Lunch Hunter</h1>
        <nav class="header-menu">
            <button>ログアウト</button> <button>MY PAGE</button> <button>店舗一覧</button> <button>店舗登録</button> </nav>
        <hr>
    </header>

    <main>
        <h2>店舗登録</h2>

        <form action="<?php echo htmlspecialchars($form_action_url); ?>" method="post" enctype="multipart/form-data">
            
            <div class="registration-container">
                
                <div class="left-col">
                    <div class="form-group">
                        <label for="store_name">店舗名</label>
                        <span class="required-star">*必須</span>
                        <input type="text" id="store_name" name="store_name" value="マクドナルド ●▲店" required>
                    </div>

                    <div class="form-group">
                        <label for="address">住所</label>
                        <span class="required-star">*必須</span>
                        <input type="text" id="address" name="address" value="福岡県福岡市●▲区●-1-2-3" required>
                    </div>

                    <div class="form-group">
                        <label>定休日</label>
                        <span class="required-star">*必須</span><br>
                        <label><input type="checkbox" name="holiday[]" value="日"> 日</label>
                        <label><input type="checkbox" name="holiday[]" value="月"> 月</label>
                        <label><input type="checkbox" name="holiday[]" value="火"> 火</label>
                        <label><input type="checkbox" name="holiday[]" value="水"> 水</label>
                        <label><input type="checkbox" name="holiday[]" value="木"> 木</label>
                        <label><input type="checkbox" name="holiday[]" value="金"> 金</label>
                        <label><input type="checkbox" name="holiday[]" value="土"> 土</label>
                        <label><input type="checkbox" name="holiday[]" value="年中無休"> 年中無休</label>
                        <label><input type="checkbox" name="holiday[]" value="未定"> 未定</label>
                    </div>

                    <div class="form-group">
                        <label>営業時間</label>
                        <span class="required-star">*必須</span><br>
                        <select name="open_time" required>
                            <option value="9:00">9:00</option>
                            </select>
                        ～
                        <select name="close_time" required>
                            <option value="17:30">17:30</option>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="tel">電話番号</label>
                        <span class="required-star">*必須</span><br>
                        <input type="tel" name="tel_part1" size="3" required> -
                        <input type="tel" name="tel_part2" size="4" required> -
                        <input type="tel" name="tel_part3" size="4" required>
                    </div>

                    <div class="form-group">
                        <label>ジャンル</label>
                        <span class="required-star">*必須</span><br>
                        <label><input type="checkbox" name="genre[]" value="うどんラーメン"> うどんラーメン</label>
                        <label><input type="checkbox" name="genre[]" value="その他麺類"> その他麺類</label>
                        <label><input type="checkbox" name="genre[]" value="ファストフード"> ファストフード</label>
                        <label><input type="checkbox" name="genre[]" value="和食"> 和食</label>
                        <label><input type="checkbox" name="genre[]" value="洋食"> 洋食</label>
                        <label><input type="checkbox" name="genre[]" value="定食"> 定食</label>
                        <label><input type="checkbox" name="genre[]" value="焼肉"> 焼肉</label>
                        <label><input type="checkbox" name="genre[]" value="中華"> 中華</label>
                        <label><input type="checkbox" name="genre[]" value="カレー"> カレー</label>
                        <label><input type="checkbox" name="genre[]" value="その他"> その他</label>
                    </div>

                    <div class="form-group">
                        <label>時間選択</label>
                        <span class="required-star">*必須</span>
                        <select size="10" name="required_time" required>
                            <option value="0:00">0:00</option>
                            <option value="0:30">0:30</option>
                            <option value="1:00">1:00</option>
                            <option value="1:30" selected>1:30</option>
                            <option value="2:00">2:00</option>
                            <option value="2:30">2:30</option>
                            <option value="3:00">3:00</option>
                            <option value="3:30">3:30</option>
                            <option value="4:00">4:00</option>
                            <option value="4:30">4:30</option>
                        </select>
                    </div>

                </div><div class="right-col">
                    
                    <div class="form-group">
                        <label>支払い方法</label>
                        <span class="required-star">*必須</span><br>
                        <label><input type="checkbox" name="payment[]" value="現金"> 現金</label>
                        <label><input type="checkbox" name="payment[]" value="QRコード"> QRコード</label>
                        <label><input type="checkbox" name="payment[]" value="電子マネー"> 電子マネー</label>
                        <label><input type="checkbox" name="payment[]" value="クレジットカード"> クレジットカード</label>
                    </div>

                    <div class="form-group">
                        <label for="url">URL</label>
                        <span class="optional-hash">#任意</span>
                        <input type="url" id="url" name="url">
                    </div>

                    <div class="form-group">
                        <label for="photo_file">写真 (外観)</label>
                        <span class="optional-hash">#任意</span><br>
                        <input type="file" id="photo_file" name="photo_file">
                    </div>

                    <div class="form-group">
                        <div style="border: 1px solid #ccc; height: 150px; padding: 10px;">
                            プレビュー
                        </div>
                    </div>

                </div></div><button type="button" onclick="confirm('この店舗情報を削除しますか？')" style="float: right;">削除</button>

            <button type="submit" name="register" style="float: right; margin-right: 10px;">登録</button>
            
        </form>

    </main>
</body>
</html>