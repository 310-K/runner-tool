<?PHP
session_start();
require_once('funcs.php');
kanriLoginCheck();

// POSTデータ取得
$userId = $_POST['userId'];
$runDate = $_POST['runDate'];
$distance = $_POST['distance'];
$comment = $_POST['comment'];
$runId = $_POST['runId'];


// DB接続
require_once('funcs.php');
$pdo = db_conn();


// SQL文を用意（UPDATE テーブル名 SET 更新対象1=:更新データ ,更新対象2=:更新データ2,... WHERE id = 対象ID;）
$stmt = $pdo->prepare(
    "UPDATE running_distance 
    SET userId=:userId, runDate=:runDate, distance=:distance, comment=:comment, indate= sysdate() 
    WHERE runId=:runId"
);


// バインド変数を用意
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->bindValue(':runDate', $runDate, PDO::PARAM_STR); //date型はINTではなくSTR
$stmt->bindValue(':distance', $distance, PDO::PARAM_INT);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$stmt->bindValue(':runId', $runId, PDO::PARAM_INT);

// 実行
$status = $stmt->execute();


// データ登録処理後
if($status==false){
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
// リダイレクト
    redirect('select.php');
}

?>