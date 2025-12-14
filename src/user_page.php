<?php
require_once('model.php');
$user = new User();
$rst = new Restaurant();
$review = new Review();

//userのセッションを確認
$user_id = $_SESSION['user_id'];

//userのデータを取得
$mydata = $user->get_UserDetail(['user_id' => $user_id]);
$favorites = $user->get_favorite($user_id);
//print_r($mydata);

?>
<style>
    .btn1 {
        margin-left: 80%;
    }

    .info {
        margin: 30px;
        display: flex;
        gap: 30%;
    }

    .info1 {
        margin: 30px;
        display: flex;
        gap: 30%;
    }

    .shop {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 10px;
    }

    .star {
        display: flex;
    }

    .item {
        display: flex;
        border-radius: 10px;
        border: 0.5px solid;
        margin-bottom: 10px;
        padding: 15px;
        gap: 30px;
    }

    .item:hover {
        box-shadow: 0.5px 0.5px 3px;
    }

    .star-rating {
        --rate: 0;
        /* 0〜5 の小数(0.1 刻みなど)を直接入れる */
        --size: 20px;
        --star-color: #ccc;
        --star-fill: gold;

        font-size: var(--size);
        font-family: "Arial", sans-serif;
        position: relative;
        display: inline-block;
        line-height: 1;
    }

    .star-rating::before {
        content: "★★★★★";
        color: var(--star-color);
    }

    .star-rating::after {
        content: "★★★★★";
        color: var(--star-fill);
        position: absolute;
        left: 0;
        top: 0;
        width: calc(var(--rate) * 20%);
        /* ★ 小数点をそのまま使用（0.1 → 2%） */
        overflow: hidden;
        white-space: nowrap;
    }
</style>
<div class="main">
    <div class="row" style="display: flex; align-items: center;">
        <div class="col-xs-6">
            <h2 style="margin: 0;">マイページ</h2>
        </div>
        <div class="col-xs-6 text-right">
            <button type="button" class="btn btn-default" onclick="location.href='?do=user_myedit'">
                ユーザ情報編集
            </button>
        </div>
    </div>
</div>
<div class="info-area">
    <!--アカウント情報-->
    <div class="info">
        <div>
            <div class="item1">社員番号ID:</div>
            <div><?php echo $mydata['user_id'] ?></div><br>
        </div>
        <div>
            <div class="item1">氏名:</div>
            <div><?php echo $mydata['username'] ?></div><br>
        </div>
    </div>
    <div class="info1">
        <div>
            <div class="item1">アカウント名:</div>
            <div><?php echo $mydata['user_account'] ?></div><br>
        </div>
        <div>
            <div class="item1">フリガナ:</div>
            <div><?php echo $mydata['userkana'] ?></div><br>
        </div>
    </div>
</div>
<!--投稿店舗-->
<div class="shop">
    <?php foreach ($favorites as $shop) : ?>
        <a href="?do=rst_detail&rst_id=<?= intval($shop['rst_id']) ?>" class="item-link">
            <div class="item">
                <div class="shopi">
                    <h4>店舗名:
                        <?php
                        echo $shop['rst_name']
                        ?>
                    </h4>
                    <div class="rating mb-2">
                        <?php
                        $review_data = $review->getList("rst_id = " . intval($shop['rst_id']));
                        $rating = 0;
                        $count = 0;

                        if (!empty($review_data)) {
                            foreach ($review_data as $r) {
                                // rev_state が true のものだけ計算に含める
                                if ($r['rev_state']) {
                                    $rating += intval($r['eval_point']);
                                    $count++;
                                }
                            }
                            if ($count > 0) {
                                $rating = $rating / $count;
                            } else {
                                $rating = 0; // 表示用に評価がない場合は 0
                            }
                        }

                        $stars = round($rating);
                        ?>
                        <?= str_repeat('★', $stars) ?><?= str_repeat('☆', 5 - $stars) ?> <?= $stars ?>
                    </div>
                    <div>ジャンル:
                        <br>
                        <?php
                        if (!empty($shop['rst_genre'])) {
                            $genre_names = array_map(fn ($g) => $g['genre'], $shop['rst_genre']);
                            echo implode(', ', $genre_names);
                        } else {
                            echo 'なし';
                        }
                        ?>
                    </div>

                    <div><?php echo $shop['discount_label'] ?></div>
                </div>
                <div class="phot">
                    <?php
                    if (!empty($shop['photo1'])) {
                        $img64 = base64_encode($shop['photo1']);
                        $mime  = 'image/webp';  // 例： image/jpeg, image/png

                        echo '<img src="data:' . $mime . ';base64,' . $img64 . '" style="max-width:100px;" />';
                    } else {
                        echo '<img src="png\noimage.png" style="max-width:100px;" />';
                    }
                    ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>