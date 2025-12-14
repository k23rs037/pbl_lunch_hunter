<?php
require_once 'model.php';

$review = new Review();
$report = new Report();

// ファイルを読み込む（未選択なら null）
function readBlob($key)
{
    if (!empty($_FILES[$key]['tmp_name'])) {
        return file_get_contents($_FILES[$key]['tmp_name']);

    }
    return null;
}

// POSTキーが配列なら数字か文字列で呼び出せるよう統一
function safePost($key)
{
    return $_POST[$key] ?? null;
}

// モード取得
$mode = safePost('mode');

// ID取得
$review_id = safePost('review_id');
$rev_id    = safePost('rev_id');
$repo_id   = safePost('repo_id');
$rst_id    = safePost('rst_id');

// モード別処理
switch ($mode) {

    case 'update':
        if (!$review_id) exit('Invalid review_id');

        $data = [
            'eval_point'     => intval(safePost('eval_point')),
            'review_comment' => safePost('review_comment') ?? safePost('comment'),
            'rst_id'         => $rst_id,
            'user_id'        => safePost('user_id'),
            'rev_state'      => 1
        ];

        // 画像追加（未選択の場合はスキップ）
        for ($i = 0; $i < 3; $i++) {
            $blob = readBlob($i);
            if ($blob !== null) {
                $data["photo" . ($i + 1)] = $blob;
            }
        }

        $review->update($data, 'review_id=' . intval($review_id));
        header('Location:?do=rst_detail&rst_id=' . intval($rst_id));

        exit;
        break;

    case 'create':
        $data = [
            'eval_point'     => intval(safePost('eval_point')),
            'review_comment' => safePost('review_comment') ?? null,
            'rst_id'         => intval($rst_id),
            'user_id'        => $_SESSION['user_id'],
            'rev_state'      => 1
        ];

        // 画像追加
        for ($i = 0; $i < 3; $i++) {
            $blob = readBlob('photo' . ($i + 1));
            if ($blob !== null) {
                $data["photo" . ($i + 1)] = $blob;
            }
        }
        $review->insert($data);
        header('Location:?do=rst_detail&rst_id=' . intval($rst_id));
        exit;
        break;

    case 'cancel':
        if (!$rev_id) exit('Invalid rev_id');

        $review->update(['rev_state' => 1], 'review_id=' . intval($rev_id));
        $report->update(['report_state' => 3], 'review_id=' . intval($rev_id));
        header('Location:?do=rev_report');
        exit;
        break;

    case 'delete':
        if (!$rev_id) exit('Invalid rev_id');

        $review->update(['rev_state' => 0], 'review_id=' . intval($rev_id));
        $report->update(['report_state' => 2], 'review_id=' . intval($rev_id));
        header('Location:?do=rev_report');
        exit;
        break;

    case 'report':
        if (!$rev_id || !$rst_id) exit('Invalid request');

        $reason = 0;
        if (!empty($_POST['reason']) && is_array($_POST['reason'])) {
            $r = $_POST['reason'];
            if (in_array('1', $r) && in_array('2', $r)) $reason = 3;
            elseif (in_array('1', $r)) $reason = 1;
            elseif (in_array('2', $r)) $reason = 2;
        }

        $data = [
            'review_id'     => intval($rev_id),
            'user_id'       => $_SESSION['user_id'],
            'report_reason' => $reason,
            'report_state'  => 1,
        ];

        $report->insert($data);
        header('Location:?do=rst_detail&rst_id=' . intval($rst_id));
        exit;
        break;

    case 'my_delete':
        if (!isset($_POST['review_id'])) {
            exit('レビューIDが指定されていません。');
        }

        $review_id = $_POST['review_id'];
        $user_id   = $_SESSION['user_id'];

        // 自分のレビューだけ削除
        $review->delete([
            'review_id' => $review_id,
            'user_id'   => $user_id
        ]);

        // 削除後リダイレクト
        header('Location:?do=rst_detail&rst_id=' . intval($rst_id));
        exit;
        break;
    default:
        exit('Invalid mode');
}
