<?php
session_start();
require('dbconnect.php');

//セッション情報を確認する($_SESSION['join']がなければregister.phpに戻る)
//isset() = ()内の値があるかどうかを確認する関数
//Locationの後は移動先のURLを指定する
if (!isset($_SESSION['join'])) {
    header ('Location: register.php');
    exit();
}

//パスワードをセキュアにする
$hash = password_hash($_SESSION['join']['password'], PASSWORD_BCRYPT);//ハッシュ化
//送信
if(!empty($_POST)){
    //テーブルへの値セットの準備
    $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, created=NOW()');
    //セットする値を指定する
    $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        $hash));
    unset($_SESSION['join']);
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>ユーザ登録確認画面</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>ユーザ登録確認画面</h1>
        <form action="" method="post">

            <input type="hidden" name="action" value="submit">
            <p>
                名前
                <!--$_SESSION[‘join’][‘name’]の値を表示する
                    htmlspecialchars() = ()内の値を文字として認識する関数(<>等を文字として読み、フォームにコードが入力されても実行されないようにする)-->
                <span class="check"><?php echo (htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES)); ?></span>
            </p>
            <p>
                email
                <span class="check"><?php echo (htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?></span>
            </p>
            <p>
                パスワード
                <span class="check">[セキュリティのため非表示]</span>
            </p>
            <!--「修正する」を押すとregister.php?action=rewriteに処理が受け渡される-->
            <input type="button" onclick="event.preventDefault();location.href='register.php?action=rewrite'" value="修正する" name="rewrite" class="button02">
            <input type="submit" value="登録する" name="registration" class="button">
        </form>
    </body>
</html>