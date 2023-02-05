<?php
// SESSIONスタート
session_start();
// 関数を呼び出す
require_once('funcs.php');
// 管理者ログインチェック
kanriLoginCheck();


// DB接続
require_once('funcs.php');
$pdo = db_conn();


// SQL文を用意（SELECT）
$stmt = $pdo->prepare("SELECT * FROM user_list");


// 実行
$status = $stmt->execute();

// データ表示
$view = "";
if($status==false){
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("ErrorMassage:".$error[2]);
}else{
    while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
        $updateLocation = "location.href='users-detail.php?userId=".$result['userId']."'";
        $deleteLocation = "location.href='users-delete.php?userId=".$result['userId']."'";
        $kanri ="";
        if($result['kanri_flg']==0){
            $kanri = "ユーザー";
        }else{
            $kanri = "管理者";
        }

        $view .= "<tr>";
        $view .= "<td>".$result['name']."</td>";
        $view .= "<td>".$result['lid']."</td>";
        $view .= "<td>".$result['lpw']."</td>";
        $view .= "<td>".$kanri."</td>";
        $view .= "<td>".$result['life_flg']."</td>";
        $view .= "<td>".$result['indate']."</td>";
        $view .= '<td>';
        $view .= "<button onclick=".$updateLocation." class='btn btn-primary'>編集</button>";
        $view .= "</td>";
        $view .= '<td>';
        $view .= "<button onclick=".$deleteLocation." class='btn btn-danger'>削除</button>";
        $view .= "</td>";
        $view .= "</tr>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>ジョグログ/ユーザー一覧</title>
</head>
<body>
<header>
<nav class="navbar navbar-expand-sm navbar-light mb-3 bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Running Log</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="users-index.php">ユーザー登録</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="graph.php">走行距離グラフ</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                管理者向け
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="select.php">Log-List</a></li>
                <li><a class="dropdown-item" href="users-select.php">Users List</a></li>
              </ul>
            </li>
        </ul>
        </div>
    </div>
    </nav>
</header>
<main>

<h1>ユーザー一覧</h1>

<!-- データ表示欄 -->
<div style="width: 100vw; overflow:scroll;">
    <table class="table" style="min-width: 900px;">
    <thead>
        <tr>
        <th scope="col">名前</th>
        <th scope="col">ログインID</th>
        <th scope="col">パスワード</th>
        <th scope="col">管理者orユーザー</th>
        <th scope="col">アクティブ=1<br>退会=0</th>
        <th scope="col">登録・更新日</th>
        <th scope="col"></th>
        <th scope="col"></th>
        </tr>
    </thead>
    <tbody><?= $view?></tbody>
    </table>
</div>



</main>    
<footer>

</footer>

<!-- jquery -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<!-- bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</body>
</html>