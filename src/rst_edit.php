<?php
// PHPで仮の既存データを設定 (通常はデータベースから取得します)
$store_data = [
    'store_name' => 'マクドナルド ●▲店',
    'address' => '福岡県福岡市●▲区●●1-2-3',
    // 定休日はチェックボックスの値を合計したビットフラグを想定 (日=1, 火=4, 土=64)
    'holiday_flags' => 1 + 4 + 64, // 例: 日, 火, 土
    'open_time' => '9:30',
    'close_time' => '22:00',
    'tel_part1' => '092',
    'tel_part2' => '1234',
    'tel_part3' => '5678',
    // ジャンルもビットフラグまたは配列を想定 (ここでは配列を想定: 和食=5, 洋食=6)
    'genre_selected' => [5, 6],
    // 支払い方法もビットフラグを想定 (現金=1, QR=2, クレジット=8)
    'payment_flags' => 1 + 2 + 8,
    'url' => 'https://example.com/store',
    // photo_pathはDBに保存されているファイルパスを想定
    'photo_path' => 'uploads/store_photo_current.jpg' 
];

// PHPのチェックボックスヘルパー関数 (ビットフラグ用)
function is_checked($value, $flags) {
    return ($flags & $value);
}

// PHPのチェックボックスヘルパー関数 (配列用)
function is_array_checked($value, $selected_array) {
    return in_array($value, $selected_array);
}

// エラーメッセージの処理（ここでは仮のセッション処理を使用）
$error = $_SESSION['error'] ?? false;
if(!empty($error)){
    echo '<h2 style="color:red">必須項目が未入力です</h2>';
    // unset($_SESSION['error']); // 実際の環境でセッションをクリア
}
?>

<main>
    <h2>店舗詳細編集・削除</h2>
    
    <form action="?do=rst_update&id=123" method="post" enctype="multipart/form-data" id="editForm">
        
        <div id="deleteConfirmBox" style="
            position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: white; border: 2px solid red; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.5);
            display: none; z-index: 1000;">
            <p>店舗を削除しますか？</p>
            <button type="button" id="confirmDeleteYes" style="margin-right: 10px;">Yes (19)</button>
            <button type="button" id="confirmDeleteNo">No (20)</button>
        </div>


        <div class="registration-container">

            <div class="left-col">
                <div class="form-group">
                    <label for="store_name">店舗名</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="store_name" name="store_name" value="<?php echo htmlspecialchars($store_data['store_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">住所</label>
                    <span class="required-star">*必須</span>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($store_data['address']); ?>" required>
                </div>

                <div class="form-group">
                    <label>定休日</label>
                    <span class="required-star">*必須</span><br>
                    <label><input type="checkbox" name="holiday[]" value="1" <?php if(is_checked(1, $store_data['holiday_flags'])) echo 'checked'; ?>> 日</label>
                    <label><input type="checkbox" name="holiday[]" value="2" <?php if(is_checked(2, $store_data['holiday_flags'])) echo 'checked'; ?>> 月</label>
                    <label><input type="checkbox" name="holiday[]" value="4" <?php if(is_checked(4, $store_data['holiday_flags'])) echo 'checked'; ?>> 火</label>
                    <label><input type="checkbox" name="holiday[]" value="8" <?php if(is_checked(8, $store_data['holiday_flags'])) echo 'checked'; ?>> 水</label>
                    <label><input type="checkbox" name="holiday[]" value="16" <?php if(is_checked(16, $store_data['holiday_flags'])) echo 'checked'; ?>> 木</label>
                    <label><input type="checkbox" name="holiday[]" value="32" <?php if(is_checked(32, $store_data['holiday_flags'])) echo 'checked'; ?>> 金</label>
                    <label><input type="checkbox" name="holiday[]" value="64" <?php if(is_checked(64, $store_data['holiday_flags'])) echo 'checked'; ?>> 土</label>
                    <label><input type="checkbox" name="holiday[]" value="128" <?php if(is_checked(128, $store_data['holiday_flags'])) echo 'checked'; ?>> 年中無休</label>
                    <label><input type="checkbox" name="holiday[]" value="256" <?php if(is_checked(256, $store_data['holiday_flags'])) echo 'checked'; ?>> 未定</label>
                </div>

                <div class="form-group">
                    <label>営業時間</label>
                    <span class="required-star">*必須</span><br>
                    <select name="open_time" required>
                        <?php 
                        // 時刻オプションを生成する関数（実際のコードではループで生成することを推奨）
                        function generate_time_options($current_time) {
                            $options = '';
                            $start = strtotime('0:00');
                            $end = strtotime('24:00');
                            for ($time = $start; $time <= $end; $time += 30 * 60) {
                                $time_str = date('G:i', $time);
                                $selected = ($time_str == $current_time) ? 'selected' : '';
                                $options .= "<option value=\"{$time_str}\" {$selected}>{$time_str}</option>\n";
                            }
                            return $options;
                        }
                        echo generate_time_options($store_data['open_time']);
                        ?>
                    </select>

                    <select name="close_time" required>
                        <?php echo generate_time_options($store_data['close_time']); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tel">電話番号</label>
                    <span class="required-star">*必須</span><br>
                    <input type="tel" name="tel_part1" size="3" value="<?php echo htmlspecialchars($store_data['tel_part1']); ?>" required> -
                    <input type="tel" name="tel_part2" size="4" value="<?php echo htmlspecialchars($store_data['tel_part2']); ?>" required> -
                    <input type="tel" name="tel_part3" size="4" value="<?php echo htmlspecialchars($store_data['tel_part3']); ?>" required>
                </div>

                <div class="form-group">
                    <label>ジャンル</label>
                    <span class="required-star">*必須</span><br>
                    <label><input type="checkbox" name="genre[]" value="1" <?php if(is_array_checked(1, $store_data['genre_selected'])) echo 'checked'; ?>> うどん</label>
                    <label><input type="checkbox" name="genre[]" value="2" <?php if(is_array_checked(2, $store_data['genre_selected'])) echo 'checked'; ?>> ラーメン</label>
                    <label><input type="checkbox" name="genre[]" value="3" <?php if(is_array_checked(3, $store_data['genre_selected'])) echo 'checked'; ?>> その他麺類</label>
                    <label><input type="checkbox" name="genre[]" value="4" <?php if(is_array_checked(4, $store_data['genre_selected'])) echo 'checked'; ?>> ファストフード</label>
                    <label><input type="checkbox" name="genre[]" value="5" <?php if(is_array_checked(5, $store_data['genre_selected'])) echo 'checked'; ?>> 和食</label>
                    <label><input type="checkbox" name="genre[]" value="6" <?php if(is_array_checked(6, $store_data['genre_selected'])) echo 'checked'; ?>> 洋食</label>
                    <label><input type="checkbox" name="genre[]" value="7" <?php if(is_array_checked(7, $store_data['genre_selected'])) echo 'checked'; ?>> 定食</label>
                    <label><input type="checkbox" name="genre[]" value="8" <?php if(is_array_checked(8, $store_data['genre_selected'])) echo 'checked'; ?>> 焼肉</label>
                    <label><input type="checkbox" name="genre[]" value="9" <?php if(is_array_checked(9, $store_data['genre_selected'])) echo 'checked'; ?>> 中華</label>
                    <label><input type="checkbox" name="genre[]" value="10" <?php if(is_array_checked(10, $store_data['genre_selected'])) echo 'checked'; ?>> カレー</label>
                    <label><input type="checkbox" name="genre[]" value="11" <?php if(is_array_checked(11, $store_data['genre_selected'])) echo 'checked'; ?>> その他</label>
                </div>

            </div>
            <div class="right-col">

                <div class="form-group">
                    <label>支払い方法</label>
                    <span class="required-star">*必須</span><br>
                    <label><input type="checkbox" name="payment[]" value="1" <?php if(is_checked(1, $store_data['payment_flags'])) echo 'checked'; ?>> 現金</label>
                    <label><input type="checkbox" name="payment[]" value="2" <?php if(is_checked(2, $store_data['payment_flags'])) echo 'checked'; ?>> QRコード</label>
                    <label><input type="checkbox" name="payment[]" value="4" <?php if(is_checked(4, $store_data['payment_flags'])) echo 'checked'; ?>> 電子マネー</label>
                    <label><input type="checkbox" name="payment[]" value="8" <?php if(is_checked(8, $store_data['payment_flags'])) echo 'checked'; ?>> クレジットカード</label>
                </div>

                <div class="form-group">
                    <label for="url">URL</label>
                    <span class="optional-hash">#任意</span>
                    <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($store_data['url']); ?>">
                </div>

                <div class="form-group">
                    <label for="photo_file">写真</label>
                    <span class="optional-hash">#任意</span><br>
                    <input type="file" id="photo_file" name="photo_file" accept="image/*">
                </div>

                <div class="form-group" style="margin-top:10px;">
                    <img id="preview_img" 
                         src="<?php echo htmlspecialchars($store_data['photo_path']); ?>" 
                         alt="選択した写真のプレビュー" 
                         style="max-width:200px; <?php echo $store_data['photo_path'] ? 'display:inline-block;' : 'display:none;'; ?> border:1px solid #ccc;" 
                    />
                    <button id="delete_btn" type="button" 
                            style="<?php echo $store_data['photo_path'] ? 'display:inline-block;' : 'display:none;'; ?> margin-left:10px;">削除</button>
                    <input type="hidden" id="current_photo_path" name="current_photo_path" value="<?php echo htmlspecialchars($store_data['photo_path']); ?>">
                    <input type="hidden" id="delete_photo_flag" name="delete_photo_flag" value="0">
                </div>

            </div>
        </div>
        
        <button type="submit" name="update" style="float: right; margin-right: 10px;">更新</button>
        <button type="button" id="deleteButton" style="float: right; margin-right: 10px; background-color: red; color: white;">削除</button>

    </form>

</main>