<?php
date_default_timezone_set("Asia/Tokyo");

$thisUserName = isset($_GET['user_Name']) ? $_GET['user_Name'] : null;

$commentArray = array();
$pdo = null;
$stmt = null;
$check = 0;

require_once '../Settings.php';
//DB接続
try {
    $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
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

//投稿作成
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
            $stmt = $pdo->prepare("INSERT INTO `bbstable` (`userID`,`userName`, `comment`, `postDate`) VALUES (:userID,:userName, :comment, :postDate)");

            $stmt->bindParam(':userID',$_SESSION['userID'],PDO::PARAM_INT);
            $stmt->bindParam(':userName', $_SESSION['userName'], PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':postDate', $postDate, PDO::PARAM_STR);

            
            $stmt->execute();

            //リダイレクト処理
            header("Location: Home.php");
            exit();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

//DBからのデータ取得
$sql = "SELECT `id`, `userName`, `comment`, `postDate` FROM `bbstable` WHERE `userName` = :thisUserName ORDER BY `postDate` DESC;";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':thisUserName', $thisUserName, PDO::PARAM_STR);
$stmt->execute();
$commentArray = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <link rel="stylesheet" href="../css/userpage.css">
</head>


<script>
    function showMenu() {
  document.querySelector('.menu').style.display = 'block';
}

function hideMenu() {
  document.querySelector('.menu').style.display = 'none';
}
</script>



<body>
<div class = "logo"><br>
    <a href="Home.php"><img src="../images/icon/icon.png" width="30%" height="80%"><br></a>
        <div class = userInfo>
            <div class = "menu-container">
                <span class="menu-text" onmouseover="showMenu()">
                    <h3><?php echo($_SESSION['userName']);?></h3>
                </span>
                <div class="menu" onmouseleave="hideMenu()">
                <ul>
                    <li>
                        <a href="./userSettings/userSetting.php">ユーザー設定</a><hr>
                        <hr><a href="../login/logout.php"><>ログアウト<></a>
                    <li>
                <ul>
                </div>
            </div>
        </div>
    </div>


    <hr>
    <br>
    <br>
    <br>
    <br>
    
    <div class="boardWrapper">
    
    <?php
        if($thisUserName == $_SESSION['userName']){
            echo('
                <form class="formWrapper" method="POST">
                <div>
                    <textarea class="commentTextArea" name="comment"></textarea>
                </div>
                <div>
                    <input type="submit" value="記入" name="submitButton">
                        <label for=""></label>
                </div>
                </form>
            ');
        }
    ?>
    
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

       
    </div>
    <form method="POST" style="text-align: center;">
        <input type="submit" name="logout" value="ログアウト">
    </form>
    <?php echo("USER ID:".$_SESSION['userID']);?>
    <br>    
</body>


</html>
