<?php
date_default_timezone_set("Asia/Tokyo");

$thisUserID = isset($_GET['user_ID']) ? $_GET['user_ID'] : null;

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

//ログイン状況のチェック
/*if (!isset($_SESSION['userName'])) {
    header("Location: ../login/login.php");
    exit();
}*/ 

//ログアウト処理
if(isset($_POST['logout'])){
    //セッション破棄
    session_destroy();
    //ログインページにリダイレクト
    header("Location: login.php");
    exit();
}
// 投稿作成
if (!empty($_POST["submitButton"])) {
    $username = $_SESSION['userName'];
    $comment = trim($_POST["comment"]);

    // バリデーションチェック
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

            // リダイレクト処理
            header("Location: Home.php");
            exit();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

//IDからユーザー情報を取得
$sql = "SELECT username FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $thisUserID, PDO::PARAM_INT);
$stmt->execute();

// 結果を取得
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// 結果がある場合、ユーザー名を代入
if ($result) {
    $thisUserName = $result['username'];
} else {
    $thisUserName = null; // あるいはエラー処理
}


// DBからのデータ取得
$sql = "SELECT `id`, `userName`, `comment`, `postDate` FROM `bbstable` WHERE `userID` = :thisUserID ORDER BY `postDate` DESC;";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':thisUserID', $thisUserID, PDO::PARAM_STR);
$stmt->execute();
$commentArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title><?php echo($thisUserName)."のユーザーページ" ?></title>
    <link rel="stylesheet" href="../css/userpage.css">
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
                    <h3><?php
                        if(!empty($_SESSION['userName'])){
                            echo($_SESSION['userName']);
                        }else{
                            echo('<a href="../login/login.php" Style = ";"><font color="red">ログイン</font></a>');
                        }
                    ?></h3>
                </span>
                <div class="menu" onmouseleave="hideMenu()">
                <ul>
                    <li>
                        <?php
                            if(!empty($_SESSION['userName'])){
                                echo('<a href="userpage.php?user_ID='.$_SESSION['userID'].'">マイページ</a><hr>');
                                echo('<a href="../userSettings/userSetting.php">ユーザー設定</a><hr>');
                                echo(' <a href="../login/logout.php"><font color="red">ログアウト</font></a>');
                            }
                        ?>
                    <li>
                <ul>
                </div>
            </div>
        </div>
    </div>



    <hr>
    
    <div class="boardWrapper">
        
    <br>
    <br>
    <br>
        <h1><?php echo("　 ".$thisUserName) ?></h1>
    </div>

    <div class="boardWrapper">
    
    <?php
        if(!empty($_SESSION['userName'])){
        if($thisUserName == $_SESSION['userName']){
            echo('
            <form class="formWrapper" method="POST">
        
            
            
            <textarea class="commentTextArea" name="comment" style="width: 100%; box-sizing: border-box; font-size: 200% " value="幅100%"  maxlength="60" oninput="mojiCount(this)"></textarea>
            
            <br>
            <br>
            <input class = "submitButton" type="submit" value="投稿" name="submitButton">
            　文字数：<span id="mojiCountReturn">0</span>/60
            
            </form>
            <hr>
            ');
        }}
    ?>
    
        <section>
            <?php foreach ($commentArray as $comment) : ?>
                <article>
                    <div class="wrapper">
                        <div class="nameArea">
                            <p class="username"><?php echo("　".$thisUserName); ?></p><hr>
                            <time><?php echo($comment["postDate"]); ?></time>
                        </div>
                        <p class="comment"><?php echo($comment["comment"]); ?></p>
                    </div>
                </article>
                <hr>
            <?php endforeach; ?>
        </section>
            
       
    </div>
    
    <br>    
</body>


</html>
