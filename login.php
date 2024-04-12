<?php

session_start();

//セッション情報の確認
if(isset($_SESSION['userName'])){
    header("Location: Home.php");
    exit();
}

class user {
    public function user(){
    }
}

if(isset($_POST['login'])){
    $userName = trim($_POST['userName']);
    $userPassword = trim($_POST['userPassword']); // ハッシュ化前のパスワードを取得

    try{
        $pdo = new PDO('mysql:host=localhost;dbname=sample', "root", "");
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `userName` = :userName");
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $stmt-> execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($userPassword, $user['password'])){ // パスワードを検証する
            $_SESSION['userName'] = $userName;
            
            // ユーザーのIDをセッションに追加
            $_SESSION['userID'] = $user['id'];
            echo "Hello ". $_SESSION['userName']."<br>WELCOME!!";
            header("Location:Home.php");
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
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href = "./css/loginStyle.css">
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
        </div>
    </body>
</html>