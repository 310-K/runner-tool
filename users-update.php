<?PHP 
// SESSIONスタート
session_start();
// 関数を呼び出す
require_once('funcs.php');
// ログインチェック
kanriLoginCheck();

// POSTデータ取得
$name = $_POST['name'];
$lid = $_POST['lid'];
$lpw = $_POST['lpw'];
$kanri_flg = $_POST['kanri_flg'];
$id = $_POST['userId'];
$life_flg = 1;

// DB接続
require_once('funcs.php');
$pdo = db_conn();


// SQL文を用意（UPDATE テーブル名 SET 更新対象1=:更新データ ,更新対象2=:更新データ2,... WHERE id = 対象ID;）
$stmt = $pdo->prepare(
    "UPDATE user_list
    SET name=:name, lid=:lid, lpw=:lpw, kanri_flg=:kanri_flg, life_flg=$life_flg, indate=sysdate() 
    WHERE userId=:userId"
);



// バインド変数を用意
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':lpw', $lpw, PDO::PARAM_STR);
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

// 実行
$status = $stmt->execute();


// データ登録処理後
if($status==false){
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
// リダイレクト
    redirect('users-select.php');
}

?>