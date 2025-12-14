<?php
require_once 'model.php';

$error = false;
$rst = new Restaurant();
$favorite = new Favorite();
$rev = new Review();
$repo = new Report();
$genre = new Genre();

$mode   = $_POST['mode'] ?? '';
$rst_id = $_POST['rst_id'] ?? null;
$rows = 0;
$total_rows = 0;
$tel_num = '';

if ($mode === 'insert' || $mode === 'update') {
    // 必須項目チェック
    $required_fields = ['store_name', 'address', 'open_time', 'close_time', 'tel_part1', 'tel_part2', 'tel_part3'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $error = true;
            break;
        }
    }
    if (empty($_POST['holiday']) || empty($_POST['genre']) || empty($_POST['payment'])) {
        $error = true;
    }

    // 電話番号結合
    $tel1 = $_POST['tel_part1'] ?? '';
    $tel2 = $_POST['tel_part2'] ?? '';
    $tel3 = $_POST['tel_part3'] ?? '';
    if (
        !isset($tel1) || strlen($tel1) < 2 || strlen($tel1) > 5 ||
        !isset($tel2) || strlen($tel2) < 1 || strlen($tel2) > 4 ||
        !isset($tel3) || strlen($tel3) < 3 || strlen($tel3) > 4
    ) {
        $error = true;
    } else {
        $tel_num = $tel1 . '-' . $tel2 . '-' . $tel3;
    }

    if ($error === false) {
        // ビットフラグ処理
        $holiday = array_sum(array_map('intval', $_POST['holiday']));
        $genre_sum = array_sum(array_map('intval', $_POST['genre']));
        $pay = array_sum($_POST['payment'] ?? []);

        // 写真処理
        $photo_file = $_POST['current_photo_path'] ?? '';
        $delete_photo = ($_POST['delete_photo_flag'] ?? '0') === '1';

        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if ($delete_photo && !empty($photo_file) && file_exists($photo_file)) {
            unlink($photo_file);
            $photo_file = '';
        }

        // 新規アップロード
        if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['photo_file']['tmp_name'];
            $ext = pathinfo($_FILES['photo_file']['name'], PATHINFO_EXTENSION);
            $new_name = $upload_dir . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($tmp_name, $new_name)) {
                // 古い写真が残っていたら削除
                if (!empty($photo_file) && file_exists($photo_file)) {
                    unlink($photo_file);
                }
                $photo_file = $new_name;
            }
        }

        // 登録データ
        $data = [
            'rst_name'    => $_POST['store_name'],
            'rst_address' => $_POST['address'],
            'start_time'  => $_POST['open_time'],
            'end_time'    => $_POST['close_time'],
            'tel_num'     => $tel_num,
            'rst_holiday' => $holiday,
            'rst_pay'     => $pay,
            'rst_info'    => $_POST['url'] ?? '',
            'photo1'      => $photo_file,
            'user_id'     => $_SESSION['user_id'],
            'discount'    => 0
        ];

        if ($mode === 'insert') {
            $rst_id = $rst->rst_insert($data);
        } elseif ($mode === 'update' && !empty($rst_id)) {
            $rows = $rst->update($data, ['rst_id' => $rst_id]);
        }

        // ジャンル保存

        $genre->delete(['rst_id' => $rst_id]);
        $genre->save_genre($rst_id, $_POST['genre'] ?? []);
    } else {
        $_SESSION['old'] = $_POST;
        $_SESSION['error'] = true;
        if ($mode === 'insert') {
            header('Location:?do=rst_input');
        } elseif ($mode === 'update' && !empty($rst_id)) {
            header("Location:?do=rst_edit&rst_id={$rst_id}");
        }
        exit();
    }
} elseif ($mode === 'discount' && !empty($rst_id)) {
    $discount = (int)($_POST['discount'] ?? 0);
    $rows = $rst->update(['discount' => $discount], "rst_id={$rst_id}");
} elseif ($mode === 'delete' && !empty($rst_id)) {
    $rst_id = (int)($_POST['rst_id'] ?? 0);
    if ($rst_id > 0) {

        $reviews = $rev->getList("rst_id = {$rst_id}"); // review_id を持つ配列が返る想定

        $report_ids = [];
        foreach ($reviews as $r) {
            $report_ids[] = $r['review_id'];
        }

        // レポート削除
        $rows_repo = 0;
        if (!empty($report_ids)) {
            foreach ($report_ids as $rid) {
                $rows_repo += $repo->delete(['review_id' => $rid]);
            }
        }
        $rows_rst  = $rst->delete(['rst_id' => $rst_id]);
        $rows_fav  = $favorite->delete(['rst_id' => $rst_id]);
        $rows_rev  = $rev->delete(['rst_id' => $rst_id]);
        $rows_genre = $genre->delete(['rst_id' => $rst_id]);

        $total_rows = $rows_repo + $rows_fav + $rows_rev + $rows_genre + $rows_rst;
    }
}

$message_rows = ($mode === 'delete') ? $total_rows : $rows;
$_SESSION['message'] = $message_rows > 0 ? "処理が完了しました。" : "処理に失敗しました。";
$_SESSION['delete'] = ($mode === 'delete' && $total_rows > 0)
    ? "全て削除されました"
    : (($mode === 'delete') ? "一部削除されていません" : '');
header('Location:?do=rst_list');
exit();
