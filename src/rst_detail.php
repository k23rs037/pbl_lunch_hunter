
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>店舗詳細</h1>
    <div>
        <a href="">戻る(店舗一覧)</a>
        <button type="button">編集</button>
        <button>お気に入り</button>
    </div>
    <br>
    <div class="shopinfo">
        <h3>店舗名</h3>
        <table border="1">
            <tr>
                <td><div>住所</div></td>
                <td>あああああ</td>
            </tr>
            <tr>
                <td><div>電話番号</div></td>
                <td>aaa-aaaa-aa</td>
            </tr>
            <tr>
                <td><div>店休日</div></td>
                <td>ああああ</td>
            </tr>
            <tr>
                <td><div>営業時間</div></td>
                <td>あああああああああ</td>
            </tr>
            <tr>
                <td><div>ジャンル</div></td>
                <td>ああああ、あああああ</td>
            </tr>
            <tr>
                <td><div>支払方法</div></td>
                <td>あああああ</td>
            </tr>
            <tr>
                <td><div>URL</div></td>
                <td>https//あああ.html</td>
            </tr>
            
        </table>
        <div class="shop-phot">
            <img src="" alt="未登録">
        </div>
    </div>

    <div class="shop-point">
        <h3>評価</h3>
        <div>総評価人数</div>
        <ul class="gurafu">
            <li>1</li>
            <li>2</li>
            <li>3</li>
            <li>4</li>
            <li>5</li>
        </ul>
        <div>総評価平均 4.2</div>
        <div>★★★★</div>
        <div class="kuchikomi">
            <div>
                <h4>評価</h4>
                <button>★</button><br>
                <textarea name="comment" placeholder="コメントを入力してください"></textarea>
            </div>
            <div class="phot">
                <!-- 1枚目 -->
                <input type="file" id="imageInput0" accept="image/*">
                <div id="previewArea0" style="margin-top:10px; display:none;">
                    <img id="previewImage0" src="" style="max-width:200px;"><br>
                    <button id="deleteBtn0">選択解除</button>
                </div><br>
                <!-- 2枚目 -->
                <input type="file" id="imageInput1" accept="image/*">
                <div id="previewArea1" style="margin-top:10px; display:none;">
                    <img id="previewImage1" src="" style="max-width:200px;"><br>
                    <button id="deleteBtn1">選択解除</button>
                </div><br>
                <!-- 3枚目 -->
                <input type="file" id="imageInput2" accept="image/*">
                <div id="previewArea2" style="margin-top:10px; display:none;">
                    <img id="previewImage2" src="" style="max-width:200px;"><br>
                    <button id="deleteBtn2">選択解除</button>
                </div><br>
                <button>投稿</button>
                <button>削除</button>
            </div>
        </div>

        <div>
            <h2>口コミ</h2>
            <section>
                <div>名前</div>
                <div>評価</div>
                <div>コメント一部</div>
                <button>詳細</button>
            </section>
        </div>

        <script>
        // 画像3つ分をまとめて処理
        for (let i = 0; i < 3; i++) {

            const input = document.getElementById(`imageInput${i}`);
            const area = document.getElementById(`previewArea${i}`);
            const img = document.getElementById(`previewImage${i}`);
            const del = document.getElementById(`deleteBtn${i}`);

            // プレビュー表示
            input.addEventListener("change", function () {
                const file = this.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        img.src = e.target.result;
                        area.style.display = "block";
                    };

                    reader.readAsDataURL(file);
                }
            });

            // 削除ボタン
            del.addEventListener("click", function () {
                img.src = "";
                area.style.display = "none";
                input.value = ""; // 選択解除
            });
        }
        </script>
        
</body>
</html>