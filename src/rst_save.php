<?php
require_once 'model.php';

$error = false;
$rst = new Restaurant();
$favorite = new Favorite();
$rev = new Review();
$repo = new Report();
$mode   = $_POST['mode'] ?? 'insert';   // デフォルトは insert
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

    // チェックボックス系
    if (empty($_POST['holiday'])) $error = true;
    if (empty($_POST['genre'])) $error = true;
    if (empty($_POST['payment'])) $error = true;

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


    // エラーがなければ登録処理
    if (!$error) {

        // 定休日・ジャンル・支払方法の合計（ビットフラグ）
        $holiday = array_sum(array_map('intval', $_POST['holiday'] ?? []));
        $genre = array_sum(array_map('intval', $_POST['genre'] ?? []));
        $pay = isset($_POST['payment']) ? array_sum($_POST['payment']) : 0;

        // ファイル処理
        $photo_file = '';
        if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $photo_file = basename($_FILES['photo_file']['name']);
            move_uploaded_file($_FILES['photo_file']['tmp_name'], $upload_dir . $photo_file);
        }

        // 登録データ
        $data = [
            'rst_name' => $_POST['store_name'],
            'rst_address' => $_POST['address'],
            'start_time' => $_POST['open_time'],
            'end_time' => $_POST['close_time'],
            'tel_num' => $tel_num,
            'rst_holiday' => $holiday,
            'rst_pay' => $pay,
            'rst_info' => $_POST['url'] ?? '',
            'photo1' => $photo_file,
            'user_id' => $_SESSION['user_id'],
            'discount' => 0
        ];

        // データベースに登録
        if ($mode === 'insert') {
            $rst_id = $rst->rst_insert($data);
        } elseif ($mode === 'update' && !empty($rst_id)) {
            $rows = $rst->update($data, ['rst_id' => $rst_id]);
        }

        $genre_save = new Genre();
        // ジャンル保存
        $genre_array = $_POST['genre'] ?? [];
        if ($rst_id !== null) {
            $rows = $genre_save->save_genre($rst_id, $genre_array);
        }
    } else {
        // 入力エラー時はフォームに戻す
        $_SESSION['old'] = $_POST;
        $_SESSION['error'] = true;
        header('Location:?do=rst_input');
        exit();
    }
} elseif ($mode === 'discount' && !empty($rst_id)) {
    $discount = isset($_POST['discount']) ? (int)$_POST['discount'] : 0;
    $rows = $rst->update(['discount' => $discount], "rst_id={$rst_id}");
} elseif ($mode === 'delete' && !empty($rst_id)) {
    $rows_rst = $rst->delete(['rst_id' => $rst_id]);
    $rows_fav = $favorite->delete(['rst_id' => $rst_id]);
    $rows_rev = $rev->delete(['rst_id' => $rst_id]);
    $rows_repo = $repo->delete(['rst_id' => $rst_id]);

    $total_rows = $rows_rst + $rows_fav + $rows_rev + $rows_repo;
}

$message_rows = ($mode === 'delete') ? $total_rows : $rows;
$_SESSION['message'] = $message_rows > 0 ? "処理が完了しました。" : "処理に失敗しました。";
$_SESSION['delete'] = ($mode === 'delete' && $total_rows > 0)
    ? "全て削除されました"
    : (($mode === 'delete') ? "一部削除されていません" : '');
header('Location:?do=rst_list');
exit();
