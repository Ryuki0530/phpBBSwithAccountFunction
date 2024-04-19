<?php

require_once '../Settings.php';

if(isset($_POST['signin'])){
    $userName = trim($_POST['userName']);
    $userPassword = password_hash(trim($_POST['userPassword']), PASSWORD_DEFAULT);
    try{
        //DB接続
        $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
        
        
        $stmt = $pdo->prepare("INSERT INTO `users` (`userName`, `password`) VALUES (:userName, :userPassword)");
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $stmt->bindParam(':userPassword', $userPassword, PDO::PARAM_STR);
        $stmt-> execute();
        $stmt = null;
        $pdo = null;
        header("Location:login.php");
        

       
    }catch(PDOException $e){
        echo $e->getMessage();
        
    }
}
?>


<html>
<head>
<link rel="shortcut icon" href="../images/icon/icon.png">
    <link rel="stylesheet" href = "../css/signinStyle.css">
    <meta charset ="utf-8">
    <title>パスワードリセット</title>
</head>
<body>
    <br>
    <div class = "signinPanel">
        <h1></h1>
        <form action="" method="POST">
            メールアドレス<input type = "text" name="mailAd" value = ""><br>
            <input type = "submit" name = "signin" value = "確認メールを送信">
            <br>
            <input type = "password" name="userPassword" value = ""><br>
            
            <font color="red" size="3px">他サービスで使用中のパスワードは絶対に入力しないでください。</font>
            <br>
            
        </form>
    </div>
</body>
</html>
