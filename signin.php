<?php
    
    
    if(isset($_POST['signin'])){
        $userName = trim($_POST['userName']);
        $userPassword = password_hash(trim($_POST['userPassword']), PASSWORD_DEFAULT);
        try{
            $pdo = new PDO('mysql:host=localhost;dbname=sample', "root", "");
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
        <link rel="stylesheet" href = "./css/signinStyle.css">
        <meta charset ="utf-8">
        <title>新規登録</title>
    </head>
    <body>
        <br>
        <div class = "signinPanel">
            <h1>ユーザー新規登録</h1>
            <form action="" method="POST">
                ユーザー名 <input type = "text" name="userName" value = ""><br>
                パスワード <input type = "password" name="userPassword" value = ""><br>
                
                <font color="red" size="3px">他サービスで使用中のパスワードは絶対に入力しないでください。</font>
                <br>
                <input type = "submit" name = "signin" value = "登録">
            </form>
        </div>
    </body>
</html>