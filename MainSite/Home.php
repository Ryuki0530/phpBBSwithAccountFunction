<?php

date_default_timezone_set("Asia/Tokyo");

$commentArray = array();
$pdo = null;
$stmt = null;
$check = 0;

require_once '../Settings.php';
//DB接続
try{
    $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//セッション開始
session_start();

//ログインチェック
if (!isset($_SESSION['userName'])) {
    header("Location: ../login/login.php");
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
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

//DBからのデータ取得
$sql = "SELECT `id`, `userID`, `userName`, `comment`, `postDate` FROM `bbstable` ORDER BY `postDate` DESC;";
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
    <link rel="stylesheet" href="../css/Home.css">
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
                        <?php echo('<a href="userpage.php?user_Name='.$_SESSION['userName'].'">マイページ</a>'); ?><hr>
                        <a href="./userSettings/userSetting.php">ユーザー設定</a><hr>
                        <a href="../login/logout.php"><font color="red">ログアウト</font></a>
                    <li>
                <ul>
                </div>
            </div>
        </div>
    </div>
    
    
    <br>
    <br>
    
    <div class="boardWrapper">
    <form class="formWrapper" method="POST">
        <br>
            <div>
                <textarea class="commentTextArea" name="comment"></textarea>
            </div>
            <div>
                <input type="submit" value="投稿" name="submitButton">
                <label for=""></label>
            </div>
        </form>
        <section>
            <hr>
            <?php foreach ($commentArray as $comment) : ?>
                <article>
                    <div class="wrapper">
                        <div class="nameArea">
                            <?php
                            
                            ?>
                            <p class="username"><?php
                                $url = 'userpage.php?user_Name=' . $comment["userName"]; 
                                echo ('<a href="'.$url.'">'.$comment["userName"].'</a>');
                            ?></p>
                            <time><?php echo $comment["postDate"]; ?></time>

                        </div>
                        <p class="comment"><?php echo $comment["comment"]; ?></p>
                    </div>
                </article>
                <hr>
                
            <?php endforeach; ?>
        </section>

       
    </div>
    
    <?php echo("USER ID:".$_SESSION['userID']);?>
    <br>    
</body>


</html>
