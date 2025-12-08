# Lunch_Hunter

#### **a.** コードを開発環境に配置
```shell
C:/php/lampp-docker8/htdocs/
 ├ pbl_lunch_hunter/
   ├─ css/   CSSファイルが入っている
   ├─ docs/  データベースの構成等の説明が入っている
   ├─ src/   主要機能や画面の実装のPHPソースファイルが入っている
      ├─ model.php　データベース操作に関するクラス
      ・・・
   ├─ index.php　各機能への入り口。
   └─ README.md　このドキュメント
```

#### **b.** コーディング規約

**b1.** 変数名、列名、name属性名を統一
- _,小文字,数字のみ、分かりやすい英語名
    - 例）$sname(変数名),sname(列名),< imput type="text" name="sname" >(属性名)

**b2.** フォルダ名、ファイル名
- フォルダ名：小文字4字以内
    - 例）src
- ファイル名：_,小文字のみの分かりやすい英語名
    - 例）user_login.php


#### **c.** データベース検索用コード一覧

**データ確認用ソースコード**
- pg_lookdata.php
    - 検索した後のデータがどんな形で出力されるか確認できる
    - 以下のURLにアクセス

http://localhost/pbl_lunch_hunter/?do=pg_lookdata

**必須コード**
- require_once('model.php');

- $model = new User(); 利用したいデータに応じた宣言
    - ユーザの場合->$model = new User()
    - 店舗の場合->$model = new Restaurant()
    - 口コミの場合->$model = new Review()

**データ検索用コード**
- getList(): 特定のテーブルに対し一覧表示用データを検索し結果をすべて返す

- getDetail(): 特定のテーブルに対して詳細表示用データを検索し１件のみ返す
    
- insert($data): 特定のテーブルに対しデータを1行追加する。
    - 引数: $data, 配列, 例：['name'=>'foo', 'age'=>18, 'tel'=>'12345'] 

- update($data, $where): 特定のテーブルに対してデータを更新する。
    - 引数:
        - $data, 配列, 例：['name'=>'foo', 'age'=>18, 'tel'=>'12345']
        - $where, 条件を表す文字列, 例：'sid=k22rs999'
    
- delete($where): 特定のテーブルに対して条件を満たすデータを削除する。
     - 引数: $where, 条件を表す文字列, 例：'sid=k22rs999'

- get_favorite($user_id)ユーザお気に入り店舗検索
    - 引数:$user_id, ユーザID

- get_userlist($where, $orderby, $limit, $offset)ユーザリストを検索
    - 引数:
            - $where, 条件を表す文字列, 例：'sid=k22rs999'
            - $orderby, 並べ替えの条件(並べ替: ASC昇順（省略可）、DESC：降順), 例:'user_id ASC'
            - $limit, $offset,数値,表示範囲を指定できる

- get_Userdetail($where)ユーザ詳細
    - 引数: $where, 条件を表す文字列, 例：'sid=k22rs999'

- username($user)姓と名で分かれているユーザの本名を結合
    - 引数:$user_id, ユーザID
- userkana($user)姓と名で分かれているユーザのフリガナを結合
    - 引数:$user_id, ユーザID

- get_mylist($table,$user_id)ユーザが登録した情報の参照
    - 引数:
        - $table,テーブル名,登録店舗の場合't_rstinfo',口コミの場合't_review'
        - $user_id,ユーザID

- get_RstDetail($where)店舗詳細
    - 引数：$where, 条件を表す文字列, 例：'rst_id' = '1';

- save_genre($rst_id, $genres)ジャンル記録
    - 引数：
        - $rst_id, 登録する店舗のID, 例：'rst_id' = '1';
        - $genres, 登録するジャンルのID配列, 例：[1,2,3,4]

- get_RevDettail($where)口コミ詳細
    - 引数：$where, 条件を表す文字列, 例：'rev_id' = '1';

- get_RepoDettail($where)通報栗込み詳細
    - 引数：$where, 条件を表す文字列, 例：'report_id' = '1';