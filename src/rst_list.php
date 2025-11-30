<?php
// ...existing code...
<?php
// 簡易サーバーサイド版：GET パラメータで検索・絞り込み・並び替え・ページングを行います。

$genres = [
  "うどん","そば","肉料理","定食","カレー","ファストフード",
  "焼肉","洋食","中華","その他","カフェ"
];

$mockStores = [
  ["id"=>1,"name"=>"丸亀製麺 九重大橋店","rating"=>4.0,"image"=>"https://images.unsplash.com/photo-1683431686868-bdb1c683cc6d?w=1080","tags"=>["うどん","和食"],"registeredBy"=>"九州健児","hasDiscount"=>false,"isFavorite"=>false],
  ["id"=>2,"name"=>"福工大前店","rating"=>3.0,"image"=>"https://images.unsplash.com/photo-1562560471-cb5b5f96c1ab?w=1080","tags"=>["和食","その他"],"registeredBy"=>"福工","hasDiscount"=>true,"isFavorite"=>true],
  ["id"=>3,"name"=>"カフェテリア","rating"=>4.5,"image"=>"https://images.unsplash.com/photo-1648808694138-6706c5efc80a?w=1080","tags"=>["カフェ","洋食"],"registeredBy"=>"田中","hasDiscount"=>false,"isFavorite"=>true],
  ["id"=>4,"name"=>"とんかつ専門店","rating"=>4.2,"image"=>"https://images.unsplash.com/photo-1625189657980-b419b768b0f6?w=1080","tags"=>["定食","肉料理"],"registeredBy"=>"山田","hasDiscount"=>true,"isFavorite"=>false],
];

// GET パラメータ取得
$keyword = trim((string)($_GET['keyword'] ?? ''));
$sortBy = in_array($_GET['sortBy'] ?? 'popular', ['popular','newest']) ? $_GET['sortBy'] : 'popular';
$selectedGenres = isset($_GET['genres']) ? (array)$_GET['genres'] : [];
$showDiscountOnly = isset($_GET['discount']) && ($_GET['discount'] === '1' || $_GET['discount'] === 'on');
$showFavoritesOnly = isset($_GET['favorite']) && ($_GET['favorite'] === '1' || $_GET['favorite'] === 'on');
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;

// フィルタリング
$filtered = array_filter($mockStores, function($s) use ($keyword, $selectedGenres, $showDiscountOnly, $showFavoritesOnly) {
    if ($keyword !== '') {
        $k = mb_strtolower($keyword);
        if (mb_stripos($s['name'],$k) === false && !array_filter($s['tags'], fn($t)=> mb_stripos($t,$k)!==false)) {
            return false;
        }
    }
    if ($showDiscountOnly && !$s['hasDiscount']) return false;
    if ($showFavoritesOnly && !$s['isFavorite']) return false;
    if (!empty($selectedGenres)) {
        // ジャンルのいずれかが含まれるものを残す
        $intersect = array_intersect($selectedGenres, $s['tags']);
        if (empty($intersect)) return false;
    }
    return true;
});

// 並び替え
if ($sortBy === 'popular') {
    usort($filtered, fn($a,$b) => $b['rating'] <=> $a['rating']);
} else { // newest -> id desc (サンプル)
    usort($filtered, fn($a,$b) => $b['id'] <=> $a['id']);
}

// ページング
$total = count($filtered);
$totalPages = (int)ceil($total / $perPage);
if ($currentPage > $totalPages && $totalPages>0) $currentPage = $totalPages;
$offset = ($currentPage - 1) * $perPage;
$visible = array_slice($filtered, $offset, $perPage);

// ヘルパー
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function build_query_with($overrides = []) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}
?><!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>店舗一覧 - Lunch Hunter</title>
<link rel="stylesheet" href="../styles/StoreListPage.css">
<style>
/* 最低限のスタイル（既存CSSがあれば上書き可） */
body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background:#f7f7f7}
.header{background:#fff;padding:12px 16px;border-bottom:1px solid #e5e5e5;display:flex;justify-content:space-between;align-items:center}
.header .title{font-size:20px}
.nav-buttons button{margin-left:8px}
.container{max-width:1100px;margin:20px auto;padding:0 16px}
.card{background:#fff;border-radius:6px;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:12px;margin-bottom:12px}
.store-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;margin-top:12px}
.store-image{width:100%;height:140px;object-fit:cover;border-radius:4px}
.badge{display:inline-block;background:#ff6b6b;color:#fff;padding:2px 6px;border-radius:4px;font-size:12px;margin-right:6px}
.store-header{display:flex;justify-content:space-between;align-items:center}
.store-tags{margin-top:8px}
.pagination{margin-top:12px}
.search-toggle{float:right}
.form-row{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
.checkbox-inline{display:inline-flex;align-items:center;gap:6px;margin-right:8px}
</style>
</head>
<body>
<header class="header">
  <div class="title">Lunch Hunter</div>
  <div class="nav-buttons">
    <form style="display:inline" method="post" action="/logout.php"><button type="submit">ログアウト</button></form>
    <button onclick="location.href='mypage.php'">MY PAGE</button>
    <button class="active">店舗一覧</button>
    <button onclick="location.href='register.php'">店舗登録</button>
  </div>
</header>

<main class="container">
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <h2>店舗一覧</h2>
      <button id="toggleSearch" class="search-toggle">店舗検索</button>
    </div>

    <div id="searchPanel" style="margin-top:12px;<?php if (!isset($_GET['keyword']) && !isset($_GET['genres'])) echo 'display:none;'; ?>">
      <form method="get" class="card" style="padding:12px">
        <div style="margin-bottom:8px">
          <label for="keyword">キーワード</label><br>
          <input id="keyword" name="keyword" value="<?=h($keyword)?>" placeholder="キーワード入力">
        </div>

        <div class="form-row">
          <div>
            <label>並び替え</label><br>
            <label class="checkbox-inline"><input type="radio" name="sortBy" value="popular" <?= $sortBy==='popular' ? 'checked':'' ?>> 人気順</label>
            <label class="checkbox-inline"><input type="radio" name="sortBy" value="newest" <?= $sortBy==='newest' ? 'checked':'' ?>> 新着順</label>
          </div>

          <div>
            <label>絞り込み</label><br>
            <label class="checkbox-inline"><input type="checkbox" name="discount" value="1" <?= $showDiscountOnly ? 'checked':'' ?>> 割引あり</label>
            <label class="checkbox-inline"><input type="checkbox" name="favorite" value="1" <?= $showFavoritesOnly ? 'checked':'' ?>> お気に入り登録</label>
          </div>
        </div>

        <div style="margin-top:8px">
          <label>ジャンル</label><br>
          <?php foreach($genres as $g): ?>
            <label class="checkbox-inline"><input type="checkbox" name="genres[]" value="<?=h($g)?>" <?= in_array($g, $selectedGenres) ? 'checked':'' ?>> <?=h($g)?></label>
          <?php endforeach; ?>
        </div>

        <div style="margin-top:12px">
          <button type="submit">決定</button>
          <a href="<?=h($_SERVER['PHP_SELF'])?>" style="margin-left:8px">リセット</a>
        </div>
      </form>
    </div>

    <div class="store-grid">
      <?php if (empty($visible)): ?>
        <div class="card">該当する店舗がありません。</div>
      <?php endif; ?>
      <?php foreach($visible as $s): ?>
        <div class="card" role="button" onclick="location.href='store.php?id=<?=h($s['id'])?>'">
          <div>
            <img src="<?=h($s['image'])?>" alt="<?=h($s['name'])?>" class="store-image">
            <?php if ($s['hasDiscount']): ?><span class="badge">割引</span><?php endif; ?>
            <?php if ($s['isFavorite']): ?><span class="badge" style="background:#ffd166;color:#000">★ お気に入り</span><?php endif; ?>
          </div>
          <div style="margin-top:8px">
            <div class="store-header">
              <h3 style="margin:0"><?=h($s['name'])?></h3>
              <div><?=h($s['rating'])?> ★</div>
            </div>
            <div class="store-tags">
              <?php foreach($s['tags'] as $t): ?>
                <span class="badge" style="background:#e0e0e0;color:#333">#<?=h($t)?></span>
              <?php endforeach; ?>
            </div>
            <p style="margin-top:8px;color:#666">登録者：<?=h($s['registeredBy'])?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="pagination" style="margin-top:12px">
      <?php if ($totalPages > 1): ?>
        <?php for($p=1;$p<=$totalPages;$p++): ?>
          <?php
            $qs = build_query_with(['page'=>$p]);
            $active = $p === $currentPage;
          ?>
          <a href="?<?=h($qs)?>" style="margin-right:6px; padding:6px 10px; border-radius:4px; background:<?= $active ? '#333' : '#fff'?>; color:<?= $active ? '#fff' : '#333'?>; text-decoration:none; border:1px solid #ddd"><?=h($p)?></a>
        <?php endfor; ?>
      <?php endif; ?>
    </div>

  </div>
</main>

<script>
document.getElementById('toggleSearch').addEventListener('click', function(){
  const p = document.getElementById('searchPanel');
  p.style.display = (p.style.display === 'none' || p.style.display==='') ? 'block' : 'none';
});
</script>
</body>
</html>
<?php
// ...existing code...
?>