<?php

require_once '../Settings.php';

if(isset($_POST['signin'])){
    $userName = trim($_POST['userName']);
    $userPassword = password_hash(trim($_POST['userPassword']), PASSWORD_DEFAULT);
    try{
        //DB接続
        $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
        
        //既存ユーザーとの重複チェック
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `userName` = :userName");
        $stmt->bindParam(':userName',$userName,PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        

       
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
