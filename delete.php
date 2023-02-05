<?php
// SESSIONスタート
session_start();
// 関数を呼び出す
require_once('funcs.php');
// ログインチェック
kanriLoginCheck();

// 対象のidを取得
$runId = $_GET['runId'];

// DB接続
require_once('funcs.php');
$pdo = db_conn();

// 削除SQL文を用意（DELETE）
$stmt = $pdo->prepare("DELETE FROM running_distance WHERE runId=:runId");

$stmt->bindValue(':runId', $runId, PDO::PARAM_INT);

// 実行
$status = $stmt->execute();

// データ削除処理後
if($status==false){
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
    redirect('select.php');
}

?>