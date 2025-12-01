-- Project Name : グルメ情報Webシステム
-- Date/Time    : 2025/12/01
-- RDBMS Type   : MySQL

-- 口コミ
crea table t_review(
    review_id SERIAL not null comment '口コミID'
    , eval_pint INT not null comment '評価点'
    , review_comment VARCHAR(250) comment 'コメント'
    , rst_id BIGINT not null comment ''
) comment '口コミ';
-- 店舗情報

-- ユーザ