<main>
    <h2>店舗登録</h2>
    <?php
    // エラーメッセージ
    $error = $_SESSION['error'] ?? false;
    if (!empty($error)) {
        echo '<h2 style="color:red">必須項目が未入力です</h2>';
        unset($_SESSION['error']);
    }

    // 前回入力値
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);

    $old_open  = $old['open_time']  ?? null;
    $old_close = $old['close_time'] ?? null;
    $old_holiday = $old['holiday'] ?? [];
    $old_genre   = $old['genre'] ?? [];
    $old_payment = $old['payment'] ?? [];
    $old_tel1 = $old['tel_part1'] ?? '';
    $old_tel2 = $old['tel_part2'] ?? '';
    $old_tel3 = $old['tel_part3'] ?? '';

    // 初期値
    $default_open  = '9:00';
    $default_close = '22:00';

    // 時間リスト作成
    $times = [];
    for ($h = 0; $h <= 24; $h++) {
        $times[] = sprintf("%d:00", $h);
        $times[] = sprintf("%d:30", $h);
    }
    ?>

    <form action="?do=rst_save" method="post" enctype="multipart/form-data">

        <input type="hidden" name="mode" value="insert">
        <input type="hidden" name="current_photo_path" value="<?= htmlspecialchars($old['photo1'] ?? '') ?>">
        <input type="hidden" name="delete_photo_flag" id="delete_photo_flag" value="0">

        <div class="registration-container">
            <div class="left-col">

                <!-- 店舗名 -->
                <div class="form-group">
                    <label for="store_name">店舗名</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="store_name" name="store_name" value="<?= htmlspecialchars($old['store_name'] ?? '') ?>">
                </div>

                <!-- 住所 -->
                <div class="form-group">
                    <label for="address">住所</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($old['address'] ?? '') ?>">
                </div>

                <!-- 定休日 -->
                <div class="form-group">
                    <label>定休日</label>
                    <span class="required-star">*必須</span><br>
                    <?php
                    $days = [
                        1 => '日', 2 => '月', 4 => '火', 8 => '水', 16 => '木', 32 => '金', 64 => '土', 128 => '年中無休', 256 => '未定'
                    ];
                    foreach ($days as $val => $label) :
                    ?>
                        <label>
                            <input type="checkbox" name="holiday[]" value="<?= $val ?>" <?= in_array($val, $old_holiday) ? 'checked' : '' ?>>
                            <?= $label ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- 営業時間 -->
                <div class="form-group">
                    <label for="open_time">開店時間</label>
                    <span class="required-star">*必須</span><br>
                    <select name="open_time" id="open_time">
                        <?php foreach ($times as $time) :
                            $selected = ($old_open === $time || (!$old_open && $time === $default_open)) ? 'selected' : '';
                        ?>
                            <option value="<?= $time ?>" <?= $selected ?>><?= $time ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="close_time">閉店時間</label>
                    <span class="required-star">*必須</span><br>
                    <select name="close_time" id="close_time">
                        <?php foreach ($times as $time) :
                            $selected = ($old_close === $time || (!$old_close && $time === $default_close)) ? 'selected' : '';
                        ?>
                            <option value="<?= $time ?>" <?= $selected ?>><?= $time ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 電話番号 -->
                <div class="form-group">
                    <label>電話番号</label>
                    <span class="required-star">*必須</span><br>
                    <input type="tel" name="tel_part1" value="<?= htmlspecialchars($old_tel1) ?>" pattern="\d{2,5}"> -
                    <input type="tel" name="tel_part2" value="<?= htmlspecialchars($old_tel2) ?>" pattern="\d{1,4}"> -
                    <input type="tel" name="tel_part3" value="<?= htmlspecialchars($old_tel3) ?>" pattern="\d{3,4}">
                </div>


                <!-- ジャンル -->
                <div class="form-group">
                    <label>ジャンル</label>
                    <span class="required-star">*必須</span><br>
                    <?php
                    $genres = [
                        1 => 'うどん', 2 => 'ラーメン', 3 => 'その他麺類', 4 => '定食', 5 => 'カレー',
                        6 => 'ファストフード', 7 => 'カフェ', 8 => '和食', 9 => '洋食', 10 => '焼肉', 11 => '中華', 12 => 'その他'
                    ];
                    foreach ($genres as $val => $label) :
                    ?>
                        <label>
                            <input type="checkbox" name="genre[]" value="<?= $val ?>" <?= in_array($val, $old_genre) ? 'checked' : '' ?>>
                            <?= $label ?>
                        </label>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="right-col">
                <!-- 支払い方法 -->
                <div class="form-group">
                    <label>支払い方法</label>
                    <span class="required-star">*必須</span><br>
                    <?php
                    $payments = [1 => '現金', 2 => 'QRコード', 4 => '電子マネー', 8 => 'クレジットカード'];
                    foreach ($payments as $val => $label) :
                    ?>
                        <label>
                            <input type="checkbox" name="payment[]" value="<?= $val ?>" <?= in_array($val, $old_payment) ? 'checked' : '' ?>>
                            <?= $label ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- URL -->
                <div class="form-group">
                    <label for="url">URL</label>
                    <span class="optional-hash">#任意</span>
                    <input type="url" id="url" name="url" value="<?= htmlspecialchars($old['url'] ?? '') ?>">
                </div>

                <!-- 写真 -->

                <input type="file" id="imageInput1" name="photo_file" accept="image/*">

                <div id="previewArea1" style="display:none; margin-top:10px;">
                    <img id="previewImage1" style="max-width:200px;">
                    <button type="button" id="deleteBtn1">選択解除</button>
                </div>
                <script>
                document.addEventListener("DOMContentLoaded", function () {
                    console.log("JS 読み込みOK");

                    const input = document.getElementById("imageInput1");
                    const area  = document.getElementById("previewArea1");
                    const img   = document.getElementById("previewImage1");
                    const del   = document.getElementById("deleteBtn1");

                    console.log(input, area, img, del);

                    input.addEventListener("change", function () {
                        console.log("change 発火");

                        const file = this.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            img.src = e.target.result;
                            area.style.display = "block";
                        };
                        reader.readAsDataURL(file);
                    });

                    del.addEventListener("click", function () {
                        img.src = "";
                        area.style.display = "none";
                        input.value = "";
                    });
                });
                </script>
            </div>

            <button type="submit" name="register" style="float: right; margin-right: 10px;">登録</button>

        </div>
    </form>

</main>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("imageInput1");
    const area  = document.getElementById("previewArea1");
    const img   = document.getElementById("previewImage1");
    const del   = document.getElementById("deleteBtn1");

    if (!input || !area || !img || !del) {
        console.error("画像プレビュー用要素が見つかりません");
        return;
    }

    // プレビュー表示
    input.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            area.style.display = "block";
        };
        reader.readAsDataURL(file);
    });

    // 削除ボタン
    del.addEventListener("click", function () {
        img.src = "";
        area.style.display = "none";
        input.value = "";
    });

});
</script>