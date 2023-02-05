<?PHP 
// POSTデータ取得
$userId = $_POST['userId'];
$runDate = $_POST['runDate'];
$distance = $_POST['distance'];
$comment = $_POST['comment'];


// DB接続
require_once('funcs.php');
$pdo = db_conn();


// SQL文を用意（登録：INSERT）
$stmt = $pdo->prepare(
    "INSERT INTO running_distance ( runId, userId, runDate, distance, comment, indate ) 
    VALUES ( NULL, :userId, :runDate, :distance, :comment, sysdate() )"
);


// バインド変数を用意
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->bindValue(':runDate', $runDate, PDO::PARAM_STR); //date型はINTではなくSTR
$stmt->bindValue(':distance', $distance, PDO::PARAM_STR); //decimal型はINTではなくSTR
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);


// 実行
$status = $stmt->execute();


// データ登録処理後
if($status==false){
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
// リダイレクト
    redirect('index.php');
}

?>