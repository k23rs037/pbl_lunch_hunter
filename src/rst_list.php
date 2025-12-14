<?php
require_once('model.php');
$user_id = $_SESSION['user_id'] ?? null;

$rst = new Restaurant();
$user = new User();
$review = new Review();

// GETパラメータ取得
$keyword  = $_GET['q'] ?? '';
$selectedGenres = $_GET['genre'] ?? [];
$discount  = isset($_GET['discount']);
$favorite  = isset($_GET['favorite']);
$sort      = $_GET['sort'] ?? '';
$genres = isset($_GET['genre']) ? $_GET['genre'] : [];

// ベースURL（閉じるボタン用）
$baseUrl = strtok($_SERVER["REQUEST_URI"], '?');

// 店舗一覧取得
$rst_list_raw = $rst->getList();

//お気に入り検索
$user_FavoriteList = [];
if ($user_id) {
  $favorites = $user->get_favorite($user_id); // お気に入り店舗の詳細リスト
  $user_FavoriteList = array_column($favorites, 'rst_id'); // rst_idだけ取り出す
}

// 詳細データ取得＋検索条件でフィルタ
$rst_list_filtered = [];
foreach ($rst_list_raw as $r) {
  $detail = $rst->get_RstDetail(['rst_id' => $r['rst_id']]);

  // キーワード
  if ($keyword && stripos($detail['rst_name'], $keyword) === false) continue;

  // ジャンル
  if ($selectedGenres) {
    $genreList = array_column($detail['rst_genre'] ?? [], 'genre');
    if (!array_intersect($selectedGenres, $genreList)) continue;
  }

  // 割引
  if ($discount && intval($detail['discount']) === 0) continue;

  // お気に入り
  if ($favorite && !in_array($r['rst_id'], $user_FavoriteList)) continue;

  $rst_list_filtered[] = $detail;
}

// 並び順
if ($sort === 'popularity') {
  usort($rst_list_filtered, function ($a, $b) use ($review) {
    $ra = $review->getList("rst_id = " . intval($a['rst_id']));
    $rb = $review->getList("rst_id = " . intval($b['rst_id']));
    $avgA = $ra ? array_sum(array_column($ra, 'eval_point')) / count($ra) : 0;
    $avgB = $rb ? array_sum(array_column($rb, 'eval_point')) / count($rb) : 0;
    return $avgB <=> $avgA;
  });
} elseif ($sort === 'new') {
  usort($rst_list_filtered, function ($a, $b) {
    return strtotime($b['created_at'] ?? '1970-01-01') <=> strtotime($a['created_at'] ?? '1970-01-01');
  });
}

// ページネーション
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$limit = 15;
$totalItems = count($rst_list_filtered);
$totalPages = ceil($totalItems / $limit);
$start = ($page - 1) * $limit;
$displayList = array_slice($rst_list_filtered, $start, $limit);
?>
<style>
  .store-card {
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 15px;
    height: 300px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .store-card img {
    max-width: 100%;
    height: 150px;
    object-fit: cover;
    margin-bottom: 10px;
  }

  .rating {
    color: orange;
  }

  .star-rating {
  position: relative;
  display: inline-block;
  font-size: 20px;
  line-height: 1;
}

.star-rating::before {
  content: "★★★★★";
  color: #ccc; /* 空星 */
}

.star-rating::after {
  content: "★★★★★";
  color: gold; /* 塗りつぶし */
  position: absolute;
  left: 0;
  top: 0;
  width: calc(var(--rate) / 5 * 100%);
  overflow: hidden;
  white-space: nowrap;
}

.rating-num {
  margin-left: 6px;
  font-size: 14px;
  vertical-align: middle;
}

</style>
<!-- 検索ボタン -->
<div class="mb-3">
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#searchModal">
    検索
  </button>
</div>

<!-- 検索モーダル -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="get" action="">
        <!-- モーダルヘッダー -->
        <div class="modal-header">
          <h5 class="modal-title" id="searchModalLabel">店舗検索</h5>
        </div>
        <!-- モーダル本文（キーワード・ジャンル・割引など） -->
        <div class="modal-body">
          <div class="search-box">
            <label>キーワード入力:</label>
            <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>">
            <div style="margin-top:10px;">
              <button name="sort" value="popularity">人気順</button>
              <button name="sort" value="new">新着順</button>
            </div>

            <div style="margin-top:10px;"><strong>ジャンル:</strong></div>
            <div class="genre-grid">
              <?php foreach (['うどん', 'ラーメン', '定食', 'カレー', 'ファーストフード', 'カフェ', '焼肉', '和食', '洋食', '中華', 'その他'] as $g) : ?>
                <label><input type="checkbox" name="genre[]" value="<?= $g ?>" <?= in_array($g, $genres) ? 'checked' : '' ?>><?= $g ?></label>
              <?php endforeach; ?>
            </div>

            <div style="margin-top:10px;">
              <label><input type="checkbox" name="discount" <?= $discount ? 'checked' : '' ?>>割引有り</label>
              <label><input type="checkbox" name="favorite" <?= $favorite ? 'checked' : '' ?>>お気に入り店舗</label>
            </div>
          </div>
        </div>
        <!-- モーダルフッター（閉じる・検索ボタン） -->
        <div class="modal-footer">
          <a href="<?= $baseUrl ?>" class="btn btn-secondary">閉じる</a>
          <button type="submit" class="btn btn-primary">検索</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 店舗一覧表示 -->
<div class="container">
  <h2>店舗一覧</h2>

  <?php if (empty($displayList)) : ?>
    <p>0件</p>
  <?php else : ?>
    <div class="row">
      <?php foreach ($displayList as $s) : ?>
        <div class="col-md-4 mb-4">
          <div class="store-card p-3 h-100 border rounded shadow-sm">
            <img src="<?= htmlspecialchars($s['photo1'] ?? 'https://via.placeholder.com/300x150') ?>" class="img-fluid mb-3" alt="外観写真">
            <h4><a href="?do=rst_detail&rst_id=<?= intval($s['rst_id']) ?>"><?= htmlspecialchars($s['rst_name']) ?></a>
              <?php if (!empty($_SESSION['usertype_id']) && $_SESSION['usertype_id'] === '9') : ?>
                <!-- 管理者用：割引変更ボタン -->
                <div class="discount-label" style="position:absolute; top:10px; right:10px;">
                  <?php if ($s['discount']) : ?>
                    <form method="post" action="?do=rst_save" style="display:inline;">
                      <input type="hidden" name="mode" value="discount">
                      <input type="hidden" name="rst_id" value="<?= $s['rst_id'] ?>">
                      <input type="hidden" name="discount" value="0">
                      <button type="submit" class="btn btn-sm btn-warning">割引取り消し</button>
                    </form>
                  <?php else : ?>
                    <form method="post" action="?do=rst_save" style="display:inline;">
                      <input type="hidden" name="mode" value="discount">
                      <input type="hidden" name="rst_id" value="<?= $s['rst_id'] ?>">
                      <input type="hidden" name="discount" value="1">
                      <button type="submit" class="btn btn-sm btn-success">割引適用</button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php else : ?>
                <!-- 一般ユーザ用：割引表示のみ -->
                <?php if ($s['discount']) : ?>
                  <div class="discount-label" style="position:absolute; top:10px; right:10px; color:green; font-weight:bold;">
                    割引あり
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            </h4>

            <!-- 評価 -->
            <div class="rating mb-2">
              <?php
              $review_data = $review->getList("rst_id = " . intval($s['rst_id']));
              $rating = 0;
              $count = 0;

              if (!empty($review_data)) {
                foreach ($review_data as $r) {
                  if ($r['rev_state']) {
                    $rating += (float)$r['eval_point'];
                    $count++;
                  }
                }
              }

              $avg = ($count > 0) ? $rating / $count : 0;
              ?>


              <div class="star-rating" style="--rate: <?= $avg ?>;"></div>
              <span class="rating-num"><?= number_format($avg, 1) ?></span>
            </div>



            <p>ジャンル: <?= htmlspecialchars(implode(' ', array_column($s['rst_genre'] ?? [], 'genre')), ENT_QUOTES, 'UTF-8') ?></p>
            <p>登録者: <?= htmlspecialchars($user->get_Userdetail(['user_id' => $s['user_id']])['user_account'] ?? '') ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- ページネーション -->
  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++) :
        $qs = $_GET;
        $qs['page'] = $i;
        $url = '?' . http_build_query($qs);
      ?>
        <li class="<?= ($i == $page) ? 'active' : '' ?>"><a href="<?= $url ?>"><?= $i ?></a></li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>