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
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // PDOのエラーモードを設定して例外を投げるようにする
} catch (PDOException $e) {
    echo $e->getMessage();
}

//セッション開始
session_start();

//ログイン状況のチェック
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

foreach ($commentArray as &$comment) {
    $userId = $comment['userID'];
    $userSql = "SELECT `userName` FROM `users` WHERE `id` = :userId";
    $userStmt = $pdo->prepare($userSql);
    $userStmt->execute(['userId' => $userId]);
    $userName = $userStmt->fetchColumn();

    $comment['userName'] = $userName;
}
unset($comment);


//DB接続終了
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="../images/icon/icon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>
    <link rel="stylesheet" href="../css/Home.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>


<script>
//メニュー表示関数
function showMenu() {
    document.querySelector('.menu').style.display = 'block';
}
//メニュー非表示関数
function hideMenu() {
    document.querySelector('.menu').style.display = 'none';
}
//投稿ページの文字数カウンター
function mojiCount(textarea) {
    var charCount = textarea.value.length;
    document.getElementById('mojiCountReturn').textContent = charCount;
}
</script>



<body>
    <div class = "logo">
    <a href="Home.php"><img src="../images/icon/icon.png" ><br></a>
            
        <div class = userInfo>
            <div class = "menu-container">
                <span class="menu-text" onmouseover="showMenu()">
                    <h3><?php echo($_SESSION['userName']."");?></h3>
                </span>
                <div class="menu" onmouseleave="hideMenu()">
                <ul>
                    <li>
                        <?php echo('<a href="userpage.php?user_ID='.$_SESSION['userID'].'">マイページ</a>'); ?><hr>
                        <a href="../userSettings/userSetting.php">ユーザー設定</a><hr>
                        <a href="../login/logout.php"><font color="red">ログアウト</font></a>
                    <li>
                <ul>
                </div>
            </div>
        </div>
    </div>
    
    
    
    
    <div class="boardWrapper">
    <form class="formWrapper" method="POST">
        <br>
        <br>
        
        
        <textarea class="commentTextArea" name="comment" style="width: 100%; box-sizing: border-box; font-size: 200% " value="幅100%"  maxlength="60" oninput="mojiCount(this)"></textarea>
        
        <br>
        <br>
        <input class = "submitButton" type="submit" value="投稿" name="submitButton">
        　文字数：<span id="mojiCountReturn">0</span>/60
        
        
    </form>
    </div>
    <div class="boardWrapper">
        <section>
            <?php foreach ($commentArray as $comment) : ?>
                <article>
                    <div class="wrapper">
                        <div class="nameArea">
                            <?php
                            
                            ?>
                            <p class="username"><?php
                                $url = 'userpage.php?user_ID=' . $comment["userID"]; 
                                echo ("　".'<a href="'.$url.'">'.$comment["userName"].'</a>');
                            ?></p><hr><font size="2px">
                            <time><?php echo $comment["postDate"]; ?></time>
                            </font>

                        </div>
                        <p class="comment"><?php echo $comment["comment"]; ?></p>
                    </div>
                </article>
                <hr>
                
            <?php endforeach; ?>
        </section>

       
    </div>
    
    
    <br>    
</body>


</html>


