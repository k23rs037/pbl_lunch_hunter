<?php
$repo = new Report();
$user = new User();
$rev = new Review();
$rst = new Restaurant();
$order = $_GET['order'] ?? null;
$filter = $_GET['filter'] ?? null;

if ($filter === "cancel") {
    $reports = $repo->getList("report_state = 3",$order);
} elseif ($filter === "hidden") {
    $reports = $repo->getList("report_state = 2",$order);
} else {
    $reports = $repo->getList("report_state != 2 AND report_state != 3",$order);
}
?>
<style>
    .header-row {
        width: 80%;
        margin: 20px auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        /* ← 横に分ける */
    }

    .header-row h1 {
        margin: 0;
    }

    .top-btn {
        display: flex;
        flex-direction: row;
        /* ← 横並びに変更 */
        gap: 10px;
    }

    .review-head-row {
        display: flex;
        gap: 30px;
        /* 2つの項目の間隔 */
        font-size: 20px;
        /* 大きくする */
        font-weight: bold;
    }

    .review-user,
    .review-store {
        font-size: 20px;
    }

    .review-head-row span {
        display: block;
        white-space: nowrap;
        /* 折り返し禁止（横並び維持） */
        margin: 0;
        padding: 0;
    }


    .report-card {
        width: 80%;
        padding: 20px;
        border: 1px solid #999;
        border-radius: 10px;
        margin: 20px auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: #fff;
    }

    .review-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .review-flex {
        display: flex;
        align-items: center;
        /* 上下中央揃え */
        gap: 30px;
        /* 評価と画像の間隔 */
    }

    .star-row {
        display: flex;
        align-items: center;
        font-size: 22px;
        gap: 8px;
    }

    .rate-num {
        font-size: 22px;
        margin-left: 8px;
    }

    .reason {
        padding: 10px;
        background: #fafafa;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .repo-photo {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .repo-photo img {
        width: 150px;
        height: auto;
        margin-right: 10px;
        border-radius: 6px;
    }

    .kome {
        margin-top: 10px;
        font-size: 19px;
        line-height: 1.6;
    }

    .star-rating {
        --rate: 0;
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
        overflow: hidden;
        white-space: nowrap;
    }
</style>

<div class="header-row">
    <h1 class="report_title">通報済み口コミ一覧表示</h1>

    <div class="top-btn">
        <?php if($filter!='cancel'){ ?>
        <a href="?do=rev_report&filter=cancel" class="btn btn-primary">通報取り消し一覧</a>
        <?php }else{ ?>
        <a href="?do=rev_report&filter" class="btn btn-primary">通報取り消し一覧</a>
        <?php } 

        if($filter!='hidden'){ ?>
        <a href="?do=rev_report&filter=hidden" class="btn btn-warning">非表示</a>
        <?php }else{ ?>
        <a href="?do=rev_report&filter" class="btn btn-warning">非表示</a>
        <?php } ?>
        <?php if(!empty($order)){ ?>
        <a href="?do=rev_report" class="btn btn-info">並び替え（古い順）</a>
        <?php }else{ ?>
        <a href="?do=rev_report&order=report_time desc" class="btn btn-info">並び替え（新着順）</a>
        <?php } ?>
    </div>
</div>


<div id="reportArea">
    <?php foreach ($reports as $report) : ?>
        <?php
        if (empty($report['review_id'])) continue;
        $rev_detail = $rev->get_RevDettail(['review_id' => $report['review_id']]);
        if (!$rev_detail) continue;

        $rev_useraccount = $user->get_Userdetail(['user_id' => $rev_detail['user_id']])['user_account'] ?? '不明';
        $rev_username = $user->get_Userdetail(['user_id' => $rev_detail['user_id']])['username'] ?? '不明';
        $repo_username = $user->get_Userdetail(['user_id' => $report['user_id']])['username'] ?? '不明';
        $rst_name = $rst->get_RstDetail(['rst_id' => $rev_detail['rst_id']])['rst_name'] ?? '不明';
        $rate = (float)$rev_detail['eval_point'];

        $reasonLabels = [1 => '写真', 2 => 'コメント', 3 => '両方'];
        $reason = $reasonLabels[$report['report_reason']] ?? '不明';
        ?>
        <section class="report-card">

            <div class="review-info">

                <!-- 投稿者 + 店舗名（横並び） -->
                <div class="review-head big">
                    <div class="review-head-row">
                        <span class="review-user">投稿者：<?= htmlspecialchars($rev_username) ?></span>
                        <span class="review-store">店舗名：<?= htmlspecialchars($rst_name ?? '不明') ?></span>
                    </div>
                </div>

                <div class="review-flex">
                    <!-- 評価 -->
                    <div class="star-row">
                        <span class="label-big">評価：</span>
                        <div class="star-rating" style="--rate: <?= $rate ?>"></div>
                        <span class="rate-num"><?= htmlspecialchars($rev_detail['eval_point']) ?></span>
                    </div>

                    <!-- 画像（3枚） -->
                    <div class="repo-photo">
                        <?php for ($i = 1; $i <= 3; $i++) : ?>
                            <?php if (!empty($rev_detail["photo{$i}"])) : ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($rev_detail["photo{$i}"]) ?>">
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- コメント -->
                <div class="kome">
                    <?= nl2br(htmlspecialchars($rev_detail['review_comment'])) ?>
                </div>


                <div class="reason">通報内容：<?= htmlspecialchars($reason) ?></div>
                <div class="user-meta small">
                    <p>投稿主：<?= htmlspecialchars($rev_username) ?></p>
                    <p>通報者：<?= htmlspecialchars($repo_username) ?></p>
                </div>
                <div class="actions">
                    <a href="?do=rev_detail&rev_id=<?= $report['review_id'] ?>" class="btn btn-info">詳細</a>

                    <form method="post" action="?do=rev_save">
                        <input type="hidden" name="mode" value="cancel">
                        <input type="hidden" name="rev_id" value="<?= $report['review_id'] ?>">
                        <input type="hidden" name="repo_id" value="<?= $report['report_id'] ?>">
                        <button type="submit" class="btn btn-primary">取り消し</button>
                    </form>

                    <button class="btn btn-warning" data-toggle="modal" data-target="#deleteModal-<?= $report['review_id'] ?>">
                        削除
                    </button>
                </div>

                <!-- 削除モーダル -->
                <div class="modal fade" id="deleteModal-<?= $report['review_id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">

                            <div class="modal-header">
                                <button class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">確認</h4>
                            </div>

                            <div class="modal-body">本当に削除しますか？</div>

                            <div class="modal-footer">
                                <form method="post" action="?do=rev_save">
                                    <input type="hidden" name="mode" value="delete">
                                    <input type="hidden" name="rev_id" value="<?= $report['review_id'] ?>">
                                    <input type="hidden" name="repo_id" value="<?= $report['report_id'] ?>">
                                    <button type="submit" class="btn btn-danger">削除する</button>
                                </form>

                                <button class="btn btn-default" data-dismiss="modal">キャンセル</button>
                            </div>

                        </div>
                    </div>
                </div>

        </section>
    <?php endforeach; ?>
</div>