<?php
// ダミーデータ（実際はDBから取得）
$accounts = [
    ['name' => '斎藤巧', 'kana' => 'サイトウ コウ', 'id' => 'u0001', 'account' => 'タックン'],
    ['name' => '田中花子', 'kana' => 'タナカ ハナコ', 'id' => '390002', 'account' => 'ハナちゃん'],
    ['name' => '田村時丸', 'kana' => 'タムラ トキマル', 'id' => '390003', 'account' => '坂上田村丸'],
    ['name' => '鈴木一郎', 'kana' => 'スズキ イチロウ', 'id' => 'u0004', 'account' => 'イチロー'],
    ['name' => '山田太郎', 'kana' => 'ヤマダ タロウ', 'id' => '550005', 'account' => 'タロウ'],
    ['name' => '佐藤二郎', 'kana' => 'サトウ ジロウ', 'id' => '660006', 'account' => 'ジロー'],
    ['name' => '中島美香', 'kana' => 'ナカジマ ミカ', 'id' => 'u0007', 'account' => 'ミカちゃん'],
    ['name' => '小林優子', 'kana' => 'コバヤシ ユウコ', 'id' => '880008', 'account' => '優子'],
    // ...最大10件表示
];

// --- 検索処理 ---
$search_key = $_GET['search'] ?? '';
$filtered_accounts = $accounts;

if (!empty($search_key)) {
    $key_lower = mb_strtolower($search_key, 'UTF-8');
    
    $filtered_accounts = array_filter($accounts, function($user) use ($key_lower) {
        $id_match = (
            isset($user['id']) && 
            mb_stripos(mb_strtolower($user['id'], 'UTF-8'), $key_lower) !== false
        );
        $account_match = (
            isset($user['account']) && 
            mb_stripos(mb_strtolower($user['account'], 'UTF-8'), $key_lower) !== false
        );
        return $id_match || $account_match;
    });
    
    $filtered_accounts = array_values($filtered_accounts);
}

// 実際にテーブルで表示するデータをフィルタ後の配列に設定
$accounts_to_display = $filtered_accounts;
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アカウント情報一覧</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .nav { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-box, .filter-box { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .pagination { text-align: center; }
        .pagination a { margin: 0 5px; text-decoration: none; }
    </style>
</head>
<body>

<h2>アカウント情報一覧</h2>

<div class="search-box">
    <form method="get">
        <label for="search">IDまたはアカウント名：</label>
        <input type="text" id="search" name="search" value="<?= htmlspecialchars($search_key) ?>">
        <button type="submit">検索</button>
    </form>
</div>

<div class="filter-box">
    <label><input type="radio" name="sort" value="id"> ユーザーID順</label>
    <label><input type="radio" name="sort" value="address"> 五十順</label>
    <label><input type="checkbox" name="suspended"> 停止済みアカウント</label>
</div>

<table>
    <thead>
        <tr>
            <th>お名前（フリガナ）</th>
            <th>ID</th>
            <th>アカウント名</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($accounts_to_display)): ?>
            <?php foreach ($accounts_to_display as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?>（<?= htmlspecialchars($user['kana']) ?>）</td>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['account']) ?></td>
                <td><a href="?do=user_edit&id=<?= urlencode($user['id']) ?>">編集</a></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">該当するアカウントは見つかりませんでした。</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <a href="#">1</a> ...
    <a href="#">3</a>
    <a href="#">4</a>
    <a href="#">5</a>
    <a href="#">6</a>
    <a href="#">7</a> ...
    <a href="#">10</a>
</div>

</body>
</html>