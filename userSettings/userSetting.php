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
//ユーザー名の変更
if(isset($_POST['nameButton'])){

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $newUserName = trim($_POST['newUserName']);
    $checkCount = 0;
    if ($newUserName == $_SESSION['userName']){
        echo('<script>alert("現在使用中のユーザー名です。");</script>');
        $checkCount++;
    } 
    if (empty($newUserName)) {
        echo('<script>alert("新しいユーザー名を入力してください。");</script>');
        $checkCount++;
    }

    if($checkCount==0){
        try{
            //既存ユーザーとの重複チェック
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `userName` = :userName");
            $stmt->bindParam(':userName',$newUserName,PDO::PARAM_STR);
            $stmt->execute();
            $checkCount2 = $stmt->fetchColumn();

            if($checkCount2 != 0){
                echo('<script>alert("このユーザー名は既に使われています。");</script>');
            }else{
                //変更処理
                $stmt = $pdo->prepare("UPDATE `users` SET `userName`= :newUserName  WHERE `userName` = :oldName");
                $stmt->bindParam(':newUserName',$newUserName,PDO::PARAM_STR);
                $stmt->bindParam(':oldName',$_SESSION['userName'],PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION['userName'] = $newUserName;
                echo('<script>alert("ユーザー名を'.$newUserName.'に変更しました。");</script>');

            }

        
        }catch(PDOException $e){
            echo $e->getMessage();
            
        }
    }
}


//パスワードの変更
if(isset($_POST['passButton'])){
    $oldUserPassword = ($_POST['oldUserPassword']);
    $newUserPassword = ($_POST['userPassword']);
    $passwordCheck = ($_POST['userPassword2']);
    $checkCount3 = 0;
    if (empty($oldUserPassword)) {
        $checkCount3++;
    }
    if (empty($newUserPassword)) {
        $checkCount3++;
    }
    if (empty($passwordCheck)) {
        $checkCount3++;
    }
    if($checkCount3==0){
        //現在のパスワードでの認証
        $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `userName` = :userName");
        $stmt->bindParam(':userName', $_SESSION['userName'], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && password_verify($oldUserPassword, $user['password'])){
            
            //変更パスワードの確認
            if($newUserPassword == $passwordCheck){
                $newUserPassword = password_hash(trim($_POST['userPassword']), PASSWORD_DEFAULT);
                try{
                    $stmt = $pdo->prepare("UPDATE `users` SET `password`= :newUserPassword  WHERE `id` = :userID");
                    $stmt->bindParam(':newUserPassword',$newUserPassword,PDO::PARAM_STR);
                    $stmt->bindParam(':userID',$_SESSION['userID'],PDO::PARAM_STR);
                    $stmt->execute();
                    echo('<script>alert("パスワードを変更しました。");</script>');
                }catch(PDOException $e){
                    echo $e->getMessage();
                }
            }else{echo('<script>alert("再入力されたパスワードが一致しません。");</script>');}
        }else{echo('<script>alert("現在のパスワードが一致しません。");</script>');}
    }
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
//メニュー表示関数
function showMenu() {
    document.querySelector('.menu').style.display = 'block';
}
//メニュー非表示関数
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
    </div>
    <div class="boardWrapper">
        <h2>ユーザー情報</h2>
        <hr>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <h3>ユーザー名変更</h3>
            新しいユーザー名　　　　　<input type="text" name="newUserName" value="" maxlength="30" required>   <br>
            <font size="2px">ユーザー名は大小文字のアルファベットと数字及び記号（._）のみ使用できます。</font><br>
            <input type="submit" name="nameButton" value="変更">
        </form>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <h3>パスワード変更　　</h3>
            現在のパスワード　　　　　<input type="password" name="oldUserPassword" value=""><br><br>
            新しいパスワード　　　　　<input type = "password" name="userPassword" pattern="[A-Za-z0-9._]*" value = "" maxlength="50" required><br>
            新しいパスワード(再入力)　<input type = "password" name="userPassword2" pattern="[A-Za-z0-9._]*" value = "" maxlength="50" required><br><br>
            <font size="2px">パスワードは大小文字のアルファベットと数字及び記号（._）のみ使用できます。</font><br>
            <font color="red" size="3px">他サービスで使用中のパスワードは絶対に入力しないでください。</font><br>
            <input type="submit" name="passButton" value="変更">
        </form>
 

    </div>
    <div class="boardWrapper">
        <h2>表示</h2>
    </div>
    <h3>
    <font color = "White">final commit:2024/04/24
    <?php echo("<br>USER ID:".$_SESSION['userID']);?>
    <br><br><a href="https://github.com/Ryuki0530/phpBBSwithAccountFunction" Style = "background-color: #f3eded;">このウェブサイトのリポジトリ</a>
    </font>
    <h3>
    <br>    
</body>


</html>
