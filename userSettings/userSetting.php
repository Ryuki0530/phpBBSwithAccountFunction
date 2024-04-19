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

//ログイン状況のチェック
if (!isset($_SESSION['userName'])) {
    header("Location: login.php");
    exit();
}



//DB接続終了
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="../images/icon/icon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー設定</title>
    <link rel="stylesheet" href="../css/userSetting.css">
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
    <a href="../MainSite/Home.php"><img src="../images/icon/icon.png" width="30%" height="80%"><br></a>
        <div class = userInfo>
            <div class = "menu-container">
                <span class="menu-text" onmouseover="showMenu()">
                    <h3><?php echo($_SESSION['userName']);?></h3>
                </span>
                <div class="menu" onmouseleave="hideMenu()">
                <ul>
                    <li>
                        <?php echo('<a href="../MainSite/userpage.php?user_ID='.$_SESSION['userID'].'">マイページ</a>'); ?><hr>
                        <a href="../login/logout.php"><font color="red">ログアウト</font></a>
                    <li>
                <ul>
                </div>
            </div>
        </div>
    </div>
    
    
    <br>
    <br>
    <br>
   
    <div class="boardWrapper">
        <h1>ユーザー設定</h1>
        <h2><font color = RED>このページは開発中です。使用しないでください。</font></h2>
    </div>
    <div class="boardWrapper">
        <h2>ユーザー情報</h2>
        ユーザー名変更<br>
        <input type = "text" name="userName" value = ""><br>
        <input type = "submit" name = "login" value = "変更">
        パスワード変更<br>
        <input type = "password" name="userPassword1" value = ""><br>
        <input type = "password" name="userPassword2" value = ""><br>
        <input type = "submit" name = "login" value = "変更">
            

    </div>
    <div class="boardWrapper">
        <h2>表示</h2>
    </div>
    <h3>
    <font color = "White">final commit:2024/04/19
    <?php echo("<br>USER ID:".$_SESSION['userID']);?>
    </font>
    <h3>
    <br>    
</body>


</html>
