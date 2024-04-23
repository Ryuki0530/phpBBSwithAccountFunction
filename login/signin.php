<?php

require_once '../Settings.php';


//セッション情報の確認とリダイレクト
if(isset($_SESSION['userName'])){
    header("Location: ../MainSite/Home.php");
    exit();
}


if(isset($_POST['signin'])){
    $userName = trim($_POST['userName']);
    $userPassword = password_hash(trim($_POST['userPassword']), PASSWORD_DEFAULT);
        if (empty($userName)||empty($userPassword)) {
            echo("ユーザー名　パスワードは必須です。");
        } else {
            
        try{
            //DB接続
            $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
            
            //既存ユーザーとの重複チェック
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `userName` = :userName");
            $stmt->bindParam(':userName',$userName,PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            /*メールアドレスはSSLの準備が完了次第。
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `mailAd` = :mailAd");
            $stmt->bindParam(':mailAd',$userName,PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            */

            if($count != 0){
                echo(
                "このユーザー名は既に使われています"
                );
            }else{
                $stmt = $pdo->prepare("INSERT INTO `users` (`userName`, `password`) VALUES (:userName, :userPassword)");
                $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
                $stmt->bindParam(':userPassword', $userPassword, PDO::PARAM_STR);
                $stmt-> execute();
                $stmt = null;
                $pdo = null;
                header("Location:login.php");
            }

        
        }catch(PDOException $e){
            echo $e->getMessage();
            
        }
    }
}
?>


<html>
<head>
<link rel="shortcut icon" href="../images/icon/icon.png">
    <link rel="stylesheet" href = "../css/signinStyle.css">
    <meta charset ="utf-8">
    <title>新規登録</title>
</head>
<body>
    <br>
    <div class = "signinPanel">
        <h1>ユーザー新規登録</h1>
        <form action="" method="POST">
            ユーザー名 <input type = "text" name="userName" value = "" maxlength="30" required><br>
            <!--(SSLの準備ができてから開始) メールアドレス<input type="email" name="mailAd"><br>-->
            パスワード <input type = "password" name="userPassword" value = "" maxlength="50" required><br>
            
            <font color="red" size="3px">他サービスで使用中のパスワードは絶対に入力しないでください。</font>
            <br>
            <input type = "submit" name = "signin" value = "登録">
        </form>
    </div>
</body>
</html>
