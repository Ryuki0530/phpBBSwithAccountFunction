<?php
    //セッション破棄とリダイレクト
    session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit();
?>

