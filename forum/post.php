<?php
session_start();
require('dbconnect.php');

//ログイン状態のチェック
//$_SESSION['id']があり、かつセッション時間が1時間以内ならmembersテーブルからidと一致する鵜member情報を取得する
if(isset($_SESSION['id']) && ($_SESSION['time']+3600 > time())){
    $_SESSION['time'] == time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members-> execute(array($_SESSION['id']));
    $member = $members->fetch();
}else{
    header('Location: login.php');
    exit();
}

if (!empty($_POST)) {
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $post=$db->prepare('INSERT INTO posts SET created_by=?, post=?, created=NOW()');
        $post->execute(array($member['id'] , $_POST['post']));
        header('Location: post.php');
        exit();
    } else {
        header('Location: login.php');
        exit();
    }
}

//データベースからデータを取得
//members、postsテーブルをm.id=p.created_byの時結合する
//これにより、各投稿について作成者の情報が取得できる
$posts=$db->query('SELECT m.name, p.* FROM members m  JOIN posts p ON m.id=p.created_by ORDER BY p.created DESC');

//セッショントークンの発行
$TOKEN_LENGTH = 16;
$tokenByte = openssl_random_pseudo_bytes($TOKEN_LENGTH);
$token = bin2hex($tokenByte);
$_SESSION['token'] = $token;
?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>投稿画面</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
        <!--ログアウト-->
        <header>
            <div class="head">
                <h1>投稿画面</h1>
                <span class="logout"><a href="login.php">ログアウト</a></span>
            </div>
        </header>
        <form accept="" method="post">
            <input type="hidden" name="token" value="<?=$token?>">
            <?php if (isset($error['login']) &&  ($error['login'] =='token')): ?>
                <p class="error">不正なアクセスです。</p>
            <?php endif; ?>
            <div class="edit">
                <p>
                    <?php echo htmlspecialchars($member['name'], ENT_QUOTES); ?>さん、ようこそ
                </p>
                <textarea name="post" cols='50' rows='10'>
                    <?php echo htmlspecialchars($post??"", ENT_QUOTES); ?>
                </textarea>
            </div>

            <input type="submit" value="投稿する" class="button02">
        </form>

        <?php foreach($posts as $post): ?>
            <div class="post">
                <?php echo htmlspecialchars($post['post'], ENT_QUOTES); ?> |
            <span class="name">
                <?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?> |
                <?php echo htmlspecialchars($post['created'], ENT_QUOTES); ?> |

                <!--追加、削除-->
                <!--セッションIDが投稿者のIDと一致するものに「削除」が表示される-->
                <?php if($_SESSION['id'] == $post['created_by']): ?>
                [<a href="delete.php?id=<?php echo htmlspecialchars($post['id'], ENT_QUOTES); ?>">削除</a>]
                <?php endif; ?>

            </span>
            </div>
            <?php endforeach; ?>
        </div>
    </body>
</html>