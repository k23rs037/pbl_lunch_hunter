INSERT INTO t_user
 (user_id,user_l_name,user_f_name,user_l_kana,user_f_kana,user_account,password,usertype_id)
VALUES
 ('test','テスト','ユーザ','テスト','ユーザ','テスト','1234','1')
,('admin','管理者','　','カンリシャ','　','管理者','5678','9')
;

INSERT INTO t_usertype
 (usertype_id,usertype)
VALUES
 ('1','社員')
,('9','管理者')
;

INSERT INTO t_genre
 (genre_id,genre)
VALUES
 ('1','うどん')
,('2','ラーメン')
,('3','その他麺類')
,('4','定食')
,('5','カレー')
,('6','ファストフード')
,('7','カフェ')
,('8','和食')
,('9','洋食')
,('10','焼肉')
,('11','中華')
,('12','その他')
;

INSERT INTO t_favorite
 (user_id,rst_id)
VALUES
 ('test','1')
;

INSERT INTO t_rstinfo
 (rst_name,rst_address,start_time,end_time,tel_num,rst_holiday,rst_pay,user_id,discount)
VALUES
 ('九産食堂','福岡県福岡市東区','08:00:00','17:00:00','000-000-0000','65','15','test',0)
;

INSERT INTO t_review
 (eval_point,review_comment,rst_id,user_id,rev_state)
VALUES
 ('4','良かった','1','test',1)
;

INSERT INTO t_rst_genre
 (rst_id,genre_id)
VALUES
 ('1','1')
,('1','2')
,('1','4')
;