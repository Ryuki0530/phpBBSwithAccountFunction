<?php

date_default_timezone_set("Asia/Tokyo");

$commentArray = array();
$pdo = null;
$stmt = null;
$check = 0;

//DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bbsd', "root", "");
} catch (PDOException $e) {
    echo $e->getMessage();
}

//セッション開始
session_start();

//ログインチェック
if (!isset($_SESSION['userName'])) {
    header("Location: login.php");
    exit();
}

//ログアウト処理
if(isset($_POST['logout'])){
    //セッション破棄
    session_destroy();
    //ログインページにリダイレクト
    header("Location: login.php");
    exit();
}

//フォーム入力時
if (!empty($_POST["submitButton"])) {
    $username = $_SESSION['userName'];
    $comment = trim($_POST["comment"]);

    //バリデーションチェック
    if (empty($comment)) {
        echo "コメントは必須です。";
    } else {
        // エスケープ処理
        $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

        $postDate = date("Y-m-d H:i:s");
        try {
            $stmt = $pdo->prepare("INSERT INTO `bbs-table` (`userName`, `comment`, `postDate`) VALUES (:userName, :comment, :postDate)");
            $stmt->bindParam(':userName', $username, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':postDate', $postDate, PDO::PARAM_STR);

            $stmt->execute();

            //リダイレクト処理
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

//DBからのデータ取得
$sql = "SELECT `id`, `userName`, `comment`, `postDate` FROM `bbs-table`;";
$commentArray = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

//DB接続終了
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="./images/icon/icon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>
    <link rel="stylesheet" href="./css/Home.css">
</head>

<body>
    <div class = "logo">
        <img src="./images/icon/icon.png" width="5%" height="5%">
    </div>
    <hr>
    <div class="boardWrapper">
        <section>
            <?php foreach ($commentArray as $comment) : ?>
                <article>
                    <div class="wrapper">
                        <div class="nameArea">
                            <p class="username"><?php echo $comment["userName"]; ?></p>
                            <time><?php echo $comment["postDate"]; ?></time>
                        </div>
                        <p class="comment"><?php echo $comment["comment"]; ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <form class="formWrapper" method="POST">
            <div>
                <input type="submit" value="記入" name="submitButton">
                <label for="">名前</label>
                <input type="text" name="username" value="<?php echo $_SESSION['userName']; ?>" disabled>
            </div>
            <div>
                <textarea class="commentTextArea" name="comment"></textarea>
            </div>
        </form>
    </div>
    <form method="POST" style="text-align: center;">
        <input type="submit" name="logout" value="ログアウト">
    </form>
    <br>    
</body>


</html>
