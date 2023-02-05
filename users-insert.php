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
$life_flg = 1;

// パスワードのハッシュ化
$hash_lpw = password_hash($lpw, PASSWORD_DEFAULT);


// DB接続
require_once('funcs.php');
$pdo = db_conn();


// SQL文を用意（登録：INSERT）
$stmt = $pdo->prepare(
    "INSERT INTO user_list ( userId, name, lid, lpw, kanri_flg, life_flg, indate ) 
    VALUES ( NULL, :name, :lid, :hash_lpw, :kanri_flg, :life_flg, sysdate())"
);


// バインド変数を用意
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
// $stmt->bindValue(':lpw', $lpw, PDO::PARAM_STR);
$stmt->bindValue(':hash_lpw', $hash_lpw, PDO::PARAM_STR);
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
$stmt->bindValue(':life_flg', $life_flg, PDO::PARAM_INT);


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