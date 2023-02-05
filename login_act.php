<?php
//最初にSESSIONを開始！！ココ大事！！
session_start();

//POST値
$lid = $_POST['lid'];
$lpw = $_POST['lpw'];

//1.  DB接続します
require_once('funcs.php');
$pdo = db_conn();

//2. データ検索SQL作成
$stmt = $pdo->prepare("SELECT * FROM user_list WHERE lid = :lid");
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$status = $stmt->execute();

//3. SQL実行時にエラーがある場合STOP
if($status==false){
    sql_error($stmt);
}

//4. 抽出データ数を取得
$val = $stmt->fetch();         //1レコードだけ取得する方法
//$count = $stmt->fetchColumn(); //SELECT COUNT(*)で使用可能()

//5. 該当レコードがあればSESSIONに値を代入
if(password_verify($lpw, $val["lpw"])){
// if( $val['lid'] != ""){
  //Login成功時
  $_SESSION['chk_ssid']  = session_id();//SESSION変数にidを保存
  $_SESSION['kanri_flg'] = $val['kanri_flg'];//SESSION変数に管理者権限のflagを保存
  $_SESSION['name']      = $val['name'];//SESSION変数にnameを保存
  $_SESSION['userId']    = $val['userId'];//SESSION変数にuserIdを保存
//   var_dump($_SESSION['userId']);
  redirect('index.php');
}else{
  //Login失敗時(Logout経由→誤って保存されたSESSIONも消去するため)
  redirect('logout.php');
}

exit();



?>