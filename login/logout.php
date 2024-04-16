<?php
    //セッション開始
    session_start();
    //セッション削除
    $_SESSION = array();
    //セッション破棄
    session_destroy();
    //ログインページにリダイレクト
    header("Location: login.php");
    exit();
?>

