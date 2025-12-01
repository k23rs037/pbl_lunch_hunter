<?php
// データベース接続設定（$pdoオブジェクト）を読み込むと仮定
require_once 'db_connect.php';

$error_message = '';

// フォームがPOST送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    
    // 1. データの受け取りと結合
    $name = $_POST['store_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $url = $_POST['url'] ?? null; // 任意項目はnullを許可
    
    // 電話番号を結合 (例: 092-123-4567)
    $tel = ($_POST['tel1'] ?? '') . '-' . ($_POST['tel2'] ?? '') . '-' . ($_POST['tel3'] ?? '');

    // チェックボックスの項目をカンマ区切りの文字列に変換
    $holidays = implode(',', $_POST['holiday'] ?? []);
    $genres = implode(',', $_POST['genre'] ?? []);
    $payments = implode(',', $_POST['payment'] ?? []);
    
    // 営業時間
    $open_time = $_POST['open_time'] ?? '';
    $close_time = $_POST['close_time'] ?? '';
    $hours = $open_time . '～' . $close_time;

    // 2. 入力検証 (必須項目のチェック)
    if (empty($name) || empty($address) || empty($tel) || empty($holidays) || empty($genres) || empty($payments)) {
        $error_message = "全ての必須項目（*必須）を入力してください。";
    } 
    // TODO: 他の検証（電話番号の形式、時間の妥当性など）

    // 3. 画像ファイル処理 (13)
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['photo']['tmp_name'];
        $file_name = $_FILES['photo']['name'];
        $new_file_name = uniqid() . '-' . basename($file_name); // ユニークなファイル名を生成
        $upload_dir = 'uploads/'; // 保存先ディレクトリ
        
        if (move_uploaded_file($file_tmp_path, $upload_dir . $new_file_name)) {
            $photo_path = $upload_dir . $new_file_name;
        } else {
            $error_message = "ファイルのアップロードに失敗しました。";
        }
    }

    // 4. データベースへの挿入 (エラーがない場合のみ)
    if (empty($error_message)) {
        try {
            $sql = "INSERT INTO stores (name, address, tel, hours, genres, holidays, payments, url, photo_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $name, 
                $address, 
                $tel, 
                $hours,
                $genres,
                $holidays,
                $payments,
                $url,
                $photo_path
            ]);

            // 登録成功後、店舗一覧画面などへリダイレクト
            header('Location: store_list.php?message=registered');
            exit;

        } catch (PDOException $e) {
            $error_message = "データベースエラー: 登録に失敗しました。" . $e->getMessage();
            // TODO: アップロードしたファイルを削除するロールバック処理
        }
    }
}

// ... ここからHTMLコードが続く ...
?>

<main>
    <h2>店舗登録</h2>
    
    <form action="store_register.php" method="POST" enctype="multipart/form-data">
        
        <section id="left-column">
            <div>
                <label for="store_name">店舗名 *必須</label>
                <input type="text" id="store_name" name="store_name" value="<?php echo htmlspecialchars($_POST['store_name'] ?? ''); ?>" required maxlength="30">
            </div>
            
            <div>
                <label for="address">住所 *必須</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
            </div>

            <div>
                <p>店休日 *必須</p>
                <?php $days = ['日', '月', '火', '水', '木', '金', '土', '年中無休', '未定']; ?>
                <?php $selected_holidays = $_POST['holiday'] ?? []; ?>
                <?php foreach ($days as $day): ?>
                    <label>
                        <input type="checkbox" name="holiday[]" value="<?php echo $day; ?>" 
                               <?php echo in_array($day, $selected_holidays) ? 'checked' : ''; ?>> 
                        <?php echo $day; ?>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <div>
                <label for="open_time">営業時間 *必須</label>
                <select id="open_time" name="open_time" required>
                    <?php 
                    $start_time = 9; $end_time = 18; 
                    for ($i = 0; $i < 24; $i++): 
                        $time = sprintf('%02d:00', $i);
                    ?>
                        <option value="<?php echo $time; ?>" 
                                <?php echo ($_POST['open_time'] ?? '09:00') === $time ? 'selected' : ''; ?>>
                            <?php echo $time; ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <span>～</span>
                <select id="close_time" name="close_time" required>
                    </select>
            </div>
            
            <div>
                <label for="tel1">電話番号 *必須</label>
                <input type="tel" id="tel1" name="tel1" value="<?php echo htmlspecialchars($_POST['tel1'] ?? ''); ?>" required size="4" maxlength="4">-
                <input type="tel" name="tel2" value="<?php echo htmlspecialchars($_POST['tel2'] ?? ''); ?>" required size="4" maxlength="4">-
                <input type="tel" name="tel3" value="<?php echo htmlspecialchars($_POST['tel3'] ?? ''); ?>" required size="4" maxlength="4">
            </div>

            <div>
                <p>ジャンル *必須</p>
                <?php $genres = ['うどん', 'ラーメン', 'その他麺類', 'ファストフード', /*...*/, 'カレー', 'その他']; ?>
                <?php $selected_genres = $_POST['genre'] ?? []; ?>
                <?php foreach ($genres as $genre): ?>
                    <label>
                        <input type="checkbox" name="genre[]" value="<?php echo $genre; ?>"
                               <?php echo in_array($genre, $selected_genres) ? 'checked' : ''; ?>>
                        <?php echo $genre; ?>
                    </label>
                <?php endforeach; ?>
            </div>
            
        </section>
        
        <section id="right-column