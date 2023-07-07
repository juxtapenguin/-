<?php
session_start();
require('dbconnect.php');

if(!empty($_POST)){
    if($_POST['name'] == ""){
        $error['name'] = 'blank';
    }
    if($_POST['email'] == ""){
        $error['email'] = 'blank';
    }else{
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if($record['cnt'] > 0){
            $error['email'] = 'duplicate';
        }
    }
    if($_POST['password'] == ""){
        $error['password'] = 'blank';
    }
    if($_POST['password2'] == ""){
        $error['password2'] = 'blank';
    }
    if(strlen($_POST['password']) < 6){
        $error['password'] = 'length';
    }
    if(($_POST['password'] != $_POST['password2']) && ($_POST['password2'] != "")){
        $error['password2'] = 'difference';
    }

    //送信後の処理(フォーム送信後にconfirm.phpに処理が受け渡される)
    if(empty($error)){
        $_SESSION['join'] = $_POST;
        header('Location: confirm.php');
        exit();
    }
}

//confirm.php(登録確認画面)で「修正する」を押された場合に実行される
//セッションに保存しておいたPOSTデータを取り出す
//これまで入力していた値が入った状態で登録フォームが表示される
if (isset($_SESSION['join']) && isset($_REQUEST['action']) && ($_REQUEST['action'] == 'rewrite')) {
    $_POST =$_SESSION['join'];
}
?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>会員登録ページ</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
        <style>
            .error{ color: red; font-size: 0.8em;}
        </style>
    </head>
    <body>
        <div class="container">
        <h1>会員登録する</h1>
        <form action="" method="post" class="registrationform">
            <label>
                名前
                <input type="text" name="name" style="width: 150px" value="<?php echo $_POST['name']??""; ?>">
                <?php if(isset($error['name']) && ($error['name'] == 'blank')): ?>
                <p class="error">名前を入力してください</p>
                <?php endif; ?>
            </label>
            <br>
            <label>
                email
                <input type="text" name="email" style="width: 150px" value="<?php echo $_POST['email']??""; ?>">
                <?php if(isset($error['email']) && ($error['email'] == 'blank')): ?>
                <p class="error">emailを入力してください</p>
                <?php endif; ?>
                <?php if(isset($error['email']) && ($error['email'] == 'duplicate')): ?>
                <p class="error">すでにそのemailは登録されています</p>
                <?php endif; ?>
            </label>
            <br>
            <label>
                パスワード
                <input type="password" name="password" style="width: 150px" value="<?php echo $_POST['password']??""; ?>">
                <?php if(isset($error['password']) && ($error['password'] == 'blank')): ?>
                <p class="error">パスワードを入力してください</p>
                <?php endif; ?>
                <?php if(isset($error['password']) && ($error['password'] == 'length')): ?>
                <p class="error">6文字以上で指定してください</p>
                <?php endif; ?>
            </label>
            <br>
            <label>
                パスワード再入力<span class="red">*</span>
                <input type="password" name="password2" style="width: 150px">
                <?php if(isset($error['password2']) && ($error['password2'] == 'blank')): ?>
                <p class="error">パスワードを入力してください</p>
                <?php endif; ?>
                <?php if(isset($error['password2']) && ($error['password2'] == 'difference')): ?>
                <p class="error">パスワードが上記と違います</p>
                <?php endif; ?>
            </label>
            <br>
            <input type="submit" value="確認する" class="button">
        </form>
        <a href="login.php">ログイン画面に戻る</a>
        </div>
    </body>
</html>