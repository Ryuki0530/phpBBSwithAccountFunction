<?php
date_default_timezone_set("Asia/Tokyo");

$pdo = null;
$stmt = null;

require_once 'Settings.php';

// DB接続
try {
    $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// テーブル作成
try {
    $sql = "CREATE TABLE IF NOT EXISTS `bbs-table` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `userID` INT(11) NOT NULL,
        `userName` VARCHAR(11) NOT NULL,
        `comment` VARCHAR(60) NOT NULL,
        `postDate` DATETIME NOT NULL,
    )";

    $pdo->exec($sql);
    echo "Table created successfully";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}

// カラム追加
try {
    $sql = "ALTER TABLE `bbs-table`
            ADD COLUMN IF NOT EXISTS `userID` INT(11) NOT NULL,
            ADD COLUMN IF NOT EXISTS `userName` VARCHAR(11) NOT NULL,
            ADD COLUMN IF NOT EXISTS `comment` VARCHAR(60) NOT NULL,
            ADD COLUMN IF NOT EXISTS `postDate` DATETIME NOT NULL";

    $pdo->exec($sql);
    echo "Columns added successfully";
} catch (PDOException $e) {
    echo "Error adding columns: " . $e->getMessage();
}

$pdo = null;
$stmt = null;
?>



<?php
date_default_timezone_set("Asia/Tokyo");

$pdo = null;
$stmt = null;

require_once 'Settings.php';

// DB接続
try {
    $pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// テーブル作成
try {
    $sql = "CREATE TABLE IF NOT EXISTS `favorite` (
        `id` BIGINT(20) AUTO_INCREMENT PRIMARY KEY,
        `user_id` BIGINT(20) NOT NULL,
        `article_id` BIGINT(20) NOT NULL,
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL
    )";

    $pdo->exec($sql);
    echo "Table created successfully";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}

// カラム追加
try {
    $sql = "ALTER TABLE `favorite`
            ADD COLUMN IF NOT EXISTS `user_id` BIGINT(20) NOT NULL,
            ADD COLUMN IF NOT EXISTS `article_id` BIGINT(20) NOT NULL,
            ADD COLUMN IF NOT EXISTS `created_at` DATETIME NULL,
            ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL";

    $pdo->exec($sql);
    echo "Columns added successfully";
} catch (PDOException $e) {
    echo "Error adding columns: " . $e->getMessage();
}

$pdo = null;
$stmt = null;
?>
