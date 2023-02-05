<?php
// SESSIONスタート
session_start();
// 関数を呼び出す
require_once('funcs.php');
// 管理者ログインチェック
kanriLoginCheck();

// 対象のidを取得
$userId = $_GET['userId'];

// DB接続
require_once('funcs.php');
$pdo = db_conn();

// 削除SQL文を用意（DELETE）
$stmt = $pdo->prepare("DELETE FROM user_list WHERE userId=:userId");

$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);


// 実行
$status = $stmt->execute();

// データ削除処理後
if($status==false){
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
    redirect('users-select.php');
}

?>