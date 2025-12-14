<?php
require_once 'model.php';

/* =========================
   OLD取得
========================= */
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

/* =========================
   rst_id取得
========================= */
$rst_id = $_GET['rst_id'] ?? null;
if (!$rst_id) {
    die("店舗IDが指定されていません");
}

/* =========================
   DB取得（ベース）
========================= */
$rst = new Restaurant();
$store_data = $rst->get_RstDetail(['rst_id' => $rst_id]);

if (!$store_data) {
    die("該当する店舗データが見つかりません");
}

/* =========================
   OLDがあれば上書き
========================= */
if (!empty($old)) {

    // テキスト系
    $store_data['rst_name']    = $old['store_name'] ?? $store_data['rst_name'];
    $store_data['rst_address'] = $old['address']    ?? $store_data['rst_address'];
    $store_data['start_time']  = $old['open_time']  ?? $store_data['start_time'];
    $store_data['end_time']    = $old['close_time'] ?? $store_data['end_time'];
    $store_data['rst_info']    = $old['url']        ?? $store_data['rst_info'];

    // 電話番号（分割入力）
    $tel1 = $old['tel_part1'] ?? '';
    $tel2 = $old['tel_part2'] ?? '';
    $tel3 = $old['tel_part3'] ?? '';

} else {
    // DBの電話番号を分割
    $tel_parts = explode('-', $store_data['tel_num']);
    $tel1 = $tel_parts[0] ?? '';
    $tel2 = $tel_parts[1] ?? '';
    $tel3 = $tel_parts[2] ?? '';
}

/* =========================
   チェックボックス用
========================= */

// 休日（OLD優先）
$holiday_selected = $old['holiday'] ?? $store_data['rst_holiday'];

// 支払い方法（OLD優先）
$payment_selected = $old['payment'] ?? explode(',', $store_data['rst_pay'] ?? []);

// ジャンル
$genre = new Genre();
if (!empty($old['genre'])) {
    $genre_selected = $old['genre']; // OLD（配列）
} else {
    // DB（genre_id配列に変換）
    $genre_rows = $genre->getList("rst_id = {$rst_id}");
    $genre_selected = array_column($genre_rows, 'genre_id');
}

/* =========================
   エラー情報
========================= */
$error = $_SESSION['error'] ?? false;
unset($_SESSION['error']);

/* =========================
   チェック用関数
========================= */
function is_checked($value, $flags)
{
    return ($flags & $value);
}

function is_array_checked($value, $selected_array)
{
    return in_array($value, (array)$selected_array);
}

/* =========================
   時間選択肢
========================= */
function generate_time_options($current_time)
{
    $options = '';
    $start = strtotime('0:00');
    $end   = strtotime('24:00');

    for ($time = $start; $time <= $end; $time += 30 * 60) {
        $time_str = date('G:i', $time);
        $selected = ($time_str == $current_time) ? 'selected' : '';
        $options .= "<option value=\"{$time_str}\" {$selected}>{$time_str}</option>\n";
    }
    return $options;
}


?>

<main>
    <h2>店舗詳細編集・削除</h2>
    <?php
    if (!empty($error)) {
        echo '<h2 style="color:red">必須項目が未入力です</h2>';
    }
    ?>

    <form id="updateForm" action="?do=rst_save" method="post" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="update">
        <input type="hidden" name="rst_id" value="<?= htmlspecialchars($rst_id) ?>">
        <input type="hidden" name="current_photo_path" value="<?= htmlspecialchars($store_data['photo1']) ?>">
        <input type="hidden" name="delete_photo_flag" id="deletePhotoFlag" value="0">

        <div class="registration-container">
            <div class="left-col">
                <!-- 店舗名 -->
                <div class="form-group">
                    <label for="store_name">店舗名</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="store_name" name="store_name" value="<?= htmlspecialchars($store_data['rst_name']) ?>" >
                </div>

                <!-- 住所 -->
                <div class="form-group">
                    <label for="address">住所</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($store_data['rst_address']) ?>" >
                </div>

                <!-- 定休日 -->
                <div class="form-group">
                    <label>定休日</label>
                    <span class="required-star">*必須</span><br>
                    <?php
                    $days = [1 => '日', 2 => '月', 4 => '火', 8 => '水', 16 => '木', 32 => '金', 64 => '土', 128 => '年中無休', 256 => '未定'];
                    foreach ($days as $val => $label) : ?>
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
                    <select name="open_time" ><?= generate_time_options($store_data['start_time']) ?></select>
                    <select name="close_time" ><?= generate_time_options($store_data['end_time']) ?></select>
                </div>

                <!-- 電話番号 -->
                <div class="form-group">
                    <label>電話番号</label>
                    <span class="required-star">*必須</span><br>
                    <input type="tel" name="tel_part1" value="<?= htmlspecialchars($tel1) ?>" > -
                    <input type="tel" name="tel_part2" value="<?= htmlspecialchars($tel2) ?>" > -
                    <input type="tel" name="tel_part3" value="<?= htmlspecialchars($tel3) ?>" >
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
                    $selected_ids = [];
                    if (!empty($genre_selected)) {
                        foreach ($genre_selected as $g) {
                            $selected_ids[] = $g['genre_id'] ?? $g;
                        }
                    }
                    foreach ($genres as $val => $label) : ?>
                        <label>
                            <input type="checkbox" name="genre[]" value="<?= $val ?>" <?= in_array($val, $selected_ids) ? 'checked' : '' ?>>
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
                    foreach ($payments as $val => $label) : ?>
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
                    <?php if (!empty($store_data['photo1'])) : ?>
                        <div id="photoPreviewWrapper" style="margin-top:10px;">
                            <img id="preview_img" src="<?= htmlspecialchars($store_data['photo1']) ?>" style="max-width:200px; border:1px solid #ccc;">
                            <button type="button" id="deletePhotoBtn">削除</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
    <div class="text-right">
    <button type="submit" form="updateForm" name="mode" value="update" class="btn btn-primary">更新</button>
        <form action="?do=rst_save" method="post" id="deleteForm" style="display:inline-block; margin-left:8px;">
            <input type="hidden" name="mode" value="delete">
            <input type="hidden" name="rst_id" value="<?= htmlspecialchars($rst_id) ?>">
            <button type="submit" class="btn btn-danger">削除</button>
        </form>
    </div>
</main>