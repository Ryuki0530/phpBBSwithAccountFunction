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

//ログイン状況のチェック
/*if (!isset($_SESSION['userName'])) {
    header("Location: ../login/login.php");
    exit();
}*/



//投稿作成
if (!empty($_POST["submitButton"])) {
    $username = $_SESSION['userName'];
    $comment = trim($_POST["comment"]);

    //バリデーションチェック
    if (empty($comment)) {
        //echo('<script>alert("投稿内容を入力してください。")</script>');
    } else {
        // エスケープ処理
        $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

        $postDate = date("Y-m-d H:i:s");
        try {
            //ログイン時はユーザ―情報を登録
            if (!isset($_SESSION['userName'])) {
                $stmt = $pdo->prepare("INSERT INTO `bbstable` (`comment`, `postDate`) VALUES (:comment, :postDate)");
            }else{
                $stmt = $pdo->prepare("INSERT INTO `bbstable` (`userID`,`userName`, `comment`, `postDate`) VALUES (:userID,:userName, :comment, :postDate)");
                $stmt->bindParam(':userID',$_SESSION['userID'],PDO::PARAM_INT);
                $stmt->bindParam(':userName', $_SESSION['userName'], PDO::PARAM_STR);
            }
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':postDate', $postDate, PDO::PARAM_STR);

            $stmt->execute();

            //リダイレクト処理
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}


//DBからの投稿取得
$sql = "SELECT `id`, `userID`, `userName`, `comment`, `postDate` FROM `bbstable` ORDER BY `postDate` DESC;";
$commentArray = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

foreach ($commentArray as &$comment) {
    $userId = $comment['userID'];
    $userSql = "SELECT `userName` FROM `users` WHERE `id` = :userId";
    $userStmt = $pdo->prepare($userSql);
    $userStmt->execute(['userId' => $userId]);
    $userName = $userStmt->fetchColumn();

    $comment['userName'] = $userName;
}
unset($comment);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="../images/icon/icon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>
    <link rel="stylesheet" href="../css/Home.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    
    
    
    
    <div class="boardWrapper">
    <form class="formWrapper" method="POST">
        <hr>
        <br>
        <br>
        
        
        <textarea class="commentTextArea" name="comment" style="width: 100%; box-sizing: border-box; font-size: 200% " value="幅100%"  maxlength="60" oninput="mojiCount(this)"></textarea>
        
        <br>
        
        <font color="red" size = 2px>人が不快に感じる表現(下ネタ、政治的発言等)はしないでください。</font>
        <br>
        <input class = "submitButton" type="submit" value="投稿" name="submitButton">
        　文字数：<span id="mojiCountReturn">0</span>/60
        
        
    </form>
    </div>
    <div class="boardWrapper">
        <section>
            <?php foreach ($commentArray as $comment) : ?>
                <article>
                    <div class="wrapper">
                        <div class="nameArea">
                            <p class="username"><?php
                                if(empty($comment['userID'])){
                                    echo ("　ゲストユーザー");
                                }else{
                                    $url = 'userpage.php?user_ID=' . $comment["userID"]; 
                                    echo ("　".'<a href="'.$url.'">'.$comment["userName"].'</a>');
                                }
                            ?></p><hr><font size="2px">
                            <time><?php echo $comment["postDate"]; ?></time>
                            
                            </font>

                        </div>
                        <p class="comment"><?php echo $comment["comment"]; ?><hr>
                            <div class = PostTag>
                                <?php
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `fav` WHERE `masterID` = :mid AND `userID` = :usid");
                                    $stmt->bindParam(':mid',$comment["id"],PDO::PARAM_INT);
                                    $stmt->bindParam(':usid',$_SESSION["userID"],PDO::PARAM_INT);
                                    $stmt->execute();
                                    $favCount = $stmt->fetchColumn();
                                ?>
                                <form method="POST">
                                <input type="hidden" name="postId" value="<?php echo $comment["id"]; ?>">
                                <?php
                                    if(!empty($_SESSION["userName"])){
                                        if($favCount == 0){
                                            echo('<input class = "favButton" type="submit" value="♡" name="favButton">');
                                        }if($favCount == 1){
                                            echo('<input class = "refavButton" type="submit" value="♡" name="refavButton">');
                                        }
                                    }else{
                                        echo('<input class = "favButton" type="button"  value="♡" disabled>');
                                    }
                                ?>
                                
                                <?php
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `fav` WHERE `masterID` = :mid");
                                $stmt->bindParam(':mid',$comment["id"],PDO::PARAM_INT);
                                $stmt->execute();
                                $favCount = $stmt->fetchColumn();
                                echo(":".$favCount);
                                if(empty($_SESSION['userID'])){
                                }
                                
                                ?>
                                </form>
                                
                                
                            </div>
                        </p>
                    </div>
                </article>
                <hr>
                
            <?php endforeach; ?>
        </section>

       
    </div>
    
    
    <br>    
</body>


</html>

<?php

//いいね機能
//いいねデータ作成
if (!empty($_POST["favButton"])){
    
    

    $postID = $_POST["postId"];
    //echo('<script>alert($postID);</script>');
    try{
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `fav` WHERE `masterID` = :mid AND `userID` = :usid");
        $stmt->bindParam(':mid',$postID,PDO::PARAM_INT);
        $stmt->bindParam(':usid',$_SESSION["userID"],PDO::PARAM_INT);
        $stmt->execute();
        $favCount = $stmt->fetchColumn();
        if($favCount == 0){
            $stmt = $pdo->prepare("INSERT INTO `fav` (`masterID`, `userID`) VALUES (:mid, :usid)");
            $stmt->bindParam(':mid',$postID,PDO::PARAM_INT);
            $stmt->bindParam(':usid',$_SESSION["userID"],PDO::PARAM_INT);
            $stmt->execute();
            //リダイレクト処理
            
            echo('<script>location.reload();</script>');
        
            exit(); 
        }
    

    }catch (PDOException $e) {
        echo $e->getMessage();

    }
}
//いいねデータ削除
if (!empty($_POST["refavButton"])){

    $postID = $_POST["postId"];
    //echo('<script>alert($postID);</script>');
    try{
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `fav` WHERE `masterID` = :mid AND `userID` = :usid");
        $stmt->bindParam(':mid',$postID,PDO::PARAM_INT);
        $stmt->bindParam(':usid',$_SESSION["userID"],PDO::PARAM_INT);
        $stmt->execute();
        $favCount = $stmt->fetchColumn();
        if($favCount == 1){

            $stmt = $pdo->prepare("DELETE FROM `fav` WHERE `masterID` = :mid AND `userID` = :usid");
            $stmt->bindParam(':mid',$postID,PDO::PARAM_INT);
            $stmt->bindParam(':usid',$_SESSION["userID"],PDO::PARAM_INT);
            $stmt->execute();
            //リダイレクト処理
            echo('<script>location.reload();</script>');
            exit();
        }
    }catch (PDOException $e) {
        echo $e->getMessage();

    }
}
?>