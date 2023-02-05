<?php
// SESSIONスタート
session_start();
// 関数を呼び出す
require_once('funcs.php');
// 管理者ログインチェック
kanriLoginCheck();


// 登録ユーザーのリストを持ってきたい→users-selectのコピペ
    // DB接続
    $pdo = db_conn();

    // SQL文を用意（SELECT）
    $stmt = $pdo->prepare("SELECT * FROM user_list");

    // 実行
    $status = $stmt->execute();

    // データ表示
    $users = "";
    if($status==false){
        // SQL実行時にエラーがある場合
        $error = $stmt->errorInfo();
        exit("ErrorMassage:".$error[2]);
    }else{
    while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
        $kanri ="";
        if($result['kanri_flg']==0){
            // ユーザーのみを表示
            // <option value="1">One</option>
            $users .= "<option value='".$result['userId']."'>";
            $users .= $result['name'];
            $users .= "</option>";
        }
        }
    }


// 検索機能の実装
    $view="";
    // 検索ボタン押下時
    if(isset($_POST["search"])){
        $userId = "";
        $runDate = "";
        $select = "";

        // ユーザー名が入力されていて練習日が入力されていない時
        if(isset($_POST["userId"]) && empty($_POST["runDate"])){
            // データ取得
            $userId = $_POST["userId"];

            $select = "SELECT * FROM running_distance WHERE userId = $userId";

        // 練習日が入力されていてユーザー名が入力されていない時
        }else if(empty($_POST["userId"]) && isset($_POST["runDate"])){
            $runDate = $_POST["runDate"];

            $select = "SELECT * FROM running_distance WHERE runDate = DATE('$runDate')";

        // ユーザー名も練習日も入力されている時
        }else if(isset($_POST["userId"]) && isset($_POST["runDate"])){
            $userId = $_POST["userId"];
            $runDate = $_POST["runDate"];

            $select = "SELECT * FROM running_distance WHERE userId = $userId AND runDate = DATE('$runDate')";

        }else{
        // }else if(empty($_POST["userId"]) && empty($_POST["runDate"])){
            $select = "SELECT * FROM running_distance";
            echo "a";
        }

        $stmt = $pdo->prepare($select);
        
        //3. 実行
        $status = $stmt->execute();
        
        //4．データ表示
        if($status==false){
            // SQL実行時にエラーがある場合
            $error = $stmt->errorInfo();
            exit("ErrorMassage:".$error[2]);
        }else{
            while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $updateLocation = "location.href='detail.php?runId=".$result['runId']."'";
                $deleteLocation = "location.href='delete.php?runId=".$result['runId']."'";
        
                $view .= "<tr>";
                $view .= "<td>".userName($result['userId'])."</td>";
                $view .= "<td>".$result['runDate']."</td>";
                $view .= "<td>".$result['distance']."</td>";
                $view .= "<td>".$result['comment']."</td>";
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
    }else{
    // ボタンを押していない時
        // SQL文を用意（SELECT）
        $stmt = $pdo->prepare("SELECT * FROM running_distance");
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
                $updateLocation = "location.href='detail.php?runId=".$result['runId']."'";
                $deleteLocation = "location.href='delete.php?runId=".$result['runId']."'";

                $view .= "<tr>";
                $view .= "<td>".userName($result['userId'])."</td>";
                $view .= "<td>".$result['runDate']."</td>";
                $view .= "<td>".$result['distance']."</td>";
                $view .= "<td>".$result['comment']."</td>";
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
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>ジョグログ/ログ一覧</title>
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
            <li class="nav-item">
            <a class="nav-link" href="logout.php">ログアウト</a>
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

<!-- 検索欄 -->
<form class="mx-auto mb-3" style="max-width: 70vw;" method="POST" action="./select.php">
  <div class="mb-3">
    <label class="form-label">名前</label>
    <select class="form-select" aria-label="Default select example" id="name" name="userId" style="max-width: 20rem;">
      <option value="" selected>ユーザー選択</option>
      <?= $users?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">練習日</label>
    <input type="date" class="form-control" id="runDate" name="runDate" style="max-width: 15rem;">
  </div>
  <!-- <button type="submit" name="search" class="btn btn-primary">検索</button> -->
  <input type="submit" name="search" value="検索" class="mb-3 btn btn-primary" style="display:block;">
</form>

<!-- データ表示欄 -->
<div style="width: 100vw; overflow:scroll;">
    <table class="table" style="min-width: 720px;">
    <thead>
        <tr>
        <th scope="col">名前</th>
        <th scope="col">練習日</th>
        <th scope="col">走行距離</th>
        <th scope="col">コメント</th>
        <th scope="col">入力日時</th>
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