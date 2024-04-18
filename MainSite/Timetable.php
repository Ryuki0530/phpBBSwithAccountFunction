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

//ログインチェック
if (!isset($_SESSION['userName'])) {
    header("Location: ../login/login.php");
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
    <title>時間割</title>
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
    <div class = "logo">
    <a href="Home.php"><img src="../images/icon/icon.png" width="30%" height="80%"><br></a>
            
        <div class = userInfo>
            <div class = "menu-container">
                <span class="menu-text" onmouseover="showMenu()">
                    <h3><?php echo($_SESSION['userName']."");?></h3>
                </span>
                <div class="menu" onmouseleave="hideMenu()">
                <ul>
                    <li>
                        <?php echo('<a href="userpage.php?user_Name='.$_SESSION['userName'].'">マイページ</a>'); ?><hr>
                        <a href="../userSettings/userSetting.php">ユーザー設定</a><hr>
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
           
    </div>
    
    <?php echo("<h3>Ver.alpha1.2</h3/<br>USER ID:".$_SESSION['userID']);?>
    <br>    
</body>


</html>


