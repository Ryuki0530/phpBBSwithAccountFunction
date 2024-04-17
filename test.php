<?php

try
{

$dsn='{エンドポイント}';
$user='{ユーザ名}';
$password='{パスワード}';
$dbh=new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql='SELECT code,name FROM staff WHERE 1';
$stmt=$dbh->prepare($sql);
$stmt->execute();

$dbh=null;

while(true)
{
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false)
        {
                break;
        }
        print $rec['code'];
        print $rec['name'];

}

}
catch (Exception $e)
{
        print 'データーベース接続エラー発生';
        exit();
}

?>