<?php
require_once 'model.php'; // ここでデータベース接続やクラスを読み込み

$rst_id = $_GET['rst_id'] ?? null;
if (!$rst_id) {
    die("店舗IDが指定されていません");
}

$rst = new Restaurant();
$store_data = $rst->get_RstDetail(['rst_id' => $rst_id]); // ここでDBから取得

if (!$store_data) {
    die("該当する店舗データが見つかりません");
}

// チェックボックス用関数（ビットフラグ）
function is_checked($value, $flags) {
    return ($flags & $value);
}

// チェックボックス用関数（配列）
function is_array_checked($value, $selected_array) {
    return in_array($value, $selected_array);
}

// エラーメッセージ
$error = $_SESSION['error'] ?? false;
if (!empty($error)) {
    echo '<h2 style="color:red">必須項目が未入力です</h2>';
    unset($_SESSION['error']);
}

// 時間リスト生成関数
function generate_time_options($current_time) {
    $options = '';
    $start = strtotime('0:00');
    $end = strtotime('24:00');
    for ($time = $start; $time <= $end; $time += 30*60) {
        $time_str = date('G:i', $time);
        $selected = ($time_str == $current_time) ? 'selected' : '';
        $options .= "<option value=\"{$time_str}\" {$selected}>{$time_str}</option>\n";
    }
    return $options;
}

// ジャンル配列（DBに保存されている場合は適宜変換）
$genre_selected = $store_data['genre_selected'] ?? []; // 例: [5,6]

?>

<main>
    <h2>店舗詳細編集・削除</h2>
    
    <form action="?do=rst_save" method="post" enctype="multipart/form-data">
        <input type="hidden" name="rst_id" value="<?= htmlspecialchars($rst_id) ?>">
        
        <div class="registration-container">
            <div class="left-col">
                <!-- 店舗名 -->
                <div class="form-group">
                    <label for="store_name">店舗名</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="store_name" name="store_name" value="<?= htmlspecialchars($store_data['rst_name']) ?>" required>
                </div>

                <!-- 住所 -->
                <div class="form-group">
                    <label for="address">住所</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($store_data['rst_address']) ?>" required>
                </div>

                <!-- 定休日 -->
                <div class="form-group">
                    <label>定休日</label>
                    <span class="required-star">*必須</span><br>
                    <?php
                    $days = [1=>'日',2=>'月',4=>'火',8=>'水',16=>'木',32=>'金',64=>'土',128=>'年中無休',256=>'未定'];
                    foreach ($days as $val=>$label): ?>
                        <label>
                            <input type="checkbox" name="holiday[]" value="<?= $val ?>" <?= is_checked($val, $store_data['rst_holiday']) ? 'checked' : '' ?>>
                            <?= $label ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- 営業時間 -->
                <div class="form-group">
                    <label>営業時間</label>
                    <span class="required-star">*必須</span><br>
                    <select name="open_time" required>
                        <?= generate_time_options($store_data['start_time']) ?>
                    </select>
                    <select name="close_time" required>
                        <?= generate_time_options($store_data['end_time']) ?>
                    </select>
                </div>

                <!-- 電話番号 -->
                <div class="form-group">
                    <label>電話番号</label>
                    <span class="required-star">*必須</span><br>
                    <input type="tel" name="tel_part1" value="<?= htmlspecialchars(substr($store_data['tel_num'],0,3)) ?>" required> -
                    <input type="tel" name="tel_part2" value="<?= htmlspecialchars(substr($store_data['tel_num'],3,4)) ?>" required> -
                    <input type="tel" name="tel_part3" value="<?= htmlspecialchars(substr($store_data['tel_num'],7,4)) ?>" required>
                </div>

                <!-- ジャンル -->
                <div class="form-group">
                    <label>ジャンル</label>
                    <span class="required-star">*必須</span><br>
                    <?php
                    $genres = [
                        1 => 'うどん', 2 => 'ラーメン', 3 => 'その他麺類', 4 => '定食',5 => 'カレー', 
                        6 => 'ファストフード', 7 => 'カフェ', 8 => '和食', 9 => '洋食', 10 => '焼肉', 11 => '中華',12 => 'その他'
                    ];
                    foreach($genres as $val=>$label): ?>
                        <label>
                            <input type="checkbox" name="genre[]" value="<?= $val ?>" <?= is_array_checked($val, $genre_selected) ? 'checked' : '' ?>>
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
                    $payments = [1=>'現金',2=>'QRコード',4=>'電子マネー',8=>'クレジットカード'];
                    foreach($payments as $val=>$label): ?>
                        <label>
                            <input type="checkbox" name="payment[]" value="<?= $val ?>" <?= is_checked($val, $store_data['rst_pay']) ? 'checked' : '' ?>>
                            <?= $label ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- URL -->
                <div class="form-group">
                    <label for="url">URL</label>
                    <span class="optional-hash">#任意</span>
                    <input type="url" name="url" value="<?= htmlspecialchars($store_data['rst_info']) ?>">
                </div>

                <!-- 写真 -->
                <div class="form-group">
                    <label for="photo_file">写真</label>
                    <span class="optional-hash">#任意</span><br>
                    <input type="file" name="photo_file" accept="image/*">
                    <?php if(!empty($store_data['photo1'])): ?>
                        <img src="<?= htmlspecialchars($store_data['photo1']) ?>" style="max-width:200px; border:1px solid #ccc;">
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <button type="submit" name="update">更新</button>
        <button type="button" id="deleteButton" style="background-color:red; color:white;">削除</button>
    </form>
</main>