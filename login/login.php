<?php

session_start();

//セッション情報の確認とリダイレクト
if(isset($_SESSION['userName'])){
    header("Location: ../MainSite/Home.php");
    exit();
}

require_once '.././Settings.php';

if(isset($_POST['login'])){
    $userName = trim($_POST['userName']);
    $userPassword = trim($_POST['userPassword']);

    try{
        $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `userName` = :userName");
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($userPassword, $user['password'])){
            $_SESSION['userName'] = $userName;
            $_SESSION['userID'] = $user['id'];
            header("Location:../MainSite/Home.php");
            exit();
        }else{
            echo "ユーザー名またはパスワードが間違っています。";
        }
        $stmt = null;
        $pdo = null;
    }catch(PDOException $e){
        echo $e->getMessage();
    }
}

?>

<html>
<head>
    <link rel="shortcut icon" href="../images/icon/icon.png">
    <link rel="stylesheet" href = "../css/loginStyle.css">
    <meta charset ="utf-8">
    <title>ログイン</title>
</head>
<body>
    <br>
    <div class = "loginPanel">
        <h1>ログイン</h1>
        <form action="" method="POST">
            ユーザー名 <input type = "text" name="userName" value = ""><br>
            パスワード <input type = "password" name="userPassword" value = ""><br>
            <input type = "submit" name = "login" value = "ログイン">
        </form>
        <a href = "signin.php">新規登録</a>
        <!--<a href = "../userSettings/passReset.php"><font color="red">パスワードのリセットはこちら</font></a>-->
    </div>
</body>
</html>
