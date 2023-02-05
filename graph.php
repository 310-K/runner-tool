<?php
// SESSIONスタート
session_start();
require_once('funcs.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>ジョグログ/走行距離グラフ</title>
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
            <a class="nav-link active" aria-current="page" href="index.php">走行距離入力</a>
            </li>
            <!-- <li class="nav-item">
            <a class="nav-link" href="users-index.php">ユーザー登録</a>
            </li> -->
            <!-- <li class="nav-item">
            <a class="nav-link" href="graph.php">走行距離グラフ</a>
            </li> -->
            <li class="nav-item">
            <a class="nav-link" href="login.php">ログイン</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="logout.php">ログアウト</a>
            </li>
            <?php 
              // 管理者用のメニュータブを管理者のみ表示されるように設定
                if(isset($_SESSION['kanri_flg']) && $_SESSION['kanri_flg'] == 1){
                  $html = 
                    '<li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      管理者向け
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <li><a class="dropdown-item" href="select.php">距離入力一覧</a></li>
                      <li><a class="dropdown-item" href="users-select.php">ユーザーリスト</a></li>
                    </ul>
                    </li>';
                }else{
                  $html ="";
                }
              // 
            echo $html;
            ?>
        </ul>
        </div>
    </div>
    </nav>
</header>
<main>

<h3 class="mx-auto" style="padding:10px;">走行距離一覧</h3>

<form action="graph.php" method="POST" class="mx-auto" style="width:90vw;">
    <select class="form-select mb-3" id="year" name="year" required></select>
    <select class="form-select mb-3" id="month" name="month" required></select>
    <input type="submit" name="search" value="検索" class="mb-3 btn btn-primary mx-auto" style="display:block;">
</form>

<canvas id="barGraph"></canvas>

</main>    
<footer>

</footer>

<?php
// 年月フィルターの設定
$year   = "";
$month  = "";
$filter = "";

if(isset($_POST['year']) && isset($_POST['month'])){
    $year  = $_POST['year'];
    $month = sprintf("%02d", $_POST['month']);

    $filter = $year."-".$month;
}

// ラベルの準備
    // ユーザーリストからユーザーを取得
        // DB接続
        require_once('funcs.php');
        $pdo = db_conn();

        // SQL文を用意（SELECT）
        $stmt = $pdo->prepare("SELECT * FROM user_list WHERE kanri_flg = 0");

        // 実行
        $status = $stmt->execute();

        // データ表示
        $users = [];
        if($status==false){
            // SQL実行時にエラーがある場合
            $error = $stmt->errorInfo();
            exit("ErrorMassage:".$error[2]);
        }else{
            while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $users[] = $result['name'];
            }
        }
    // ラベルをJSONに変換
    $x  = $users;
    $jx = json_encode($x);

// データの準備
    // ランニングの記録から、userIDごとに距離を合計する
    // ユーザーごとの距離を全員分、配列に突っ込む
    $data = [];

    // 全ユーザーIDを取得
        // SQL文を用意（SELECT）
        $stmt = $pdo->prepare("SELECT * FROM user_list");
        // 実行
        $status = $stmt->execute();
        // データ表示
        if($status==false){
            // SQL実行時にエラーがある場合
            $error = $stmt->errorInfo();
            exit("ErrorMassage:".$error[2]);
        }else{
            while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $sum = sumDistance($result['userId'], $filter);
                $data[] = $sum;
            }
        }

    // ラベルをJSONに変換
    $y  = $data;
    $jy = json_encode($y);

?>


<!-- jquery -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<!-- bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<!-- chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// 年月フィルターの選択肢を用意
    // 年
    let yearHTML = "<option selected>年</option>";
    for(let i=2022; i <2026; i++){
        yearHTML +=   `<option value="${i}">${i}</option>`;
    };
    $("#year").html(yearHTML);

    // 月
    let monthHTML = "<option selected>月</option>";
    for(let i=1; i <13; i++){
        monthHTML +=   `<option value="${i}">${i}</option>`;
    };
    $("#month").html(monthHTML);
// 

// グラフの設定
    const ctx = $("#barGraph");
    let x = JSON.parse('<?= $jx?>');
    let y = JSON.parse('<?= $jy?>');

    const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: x,
        datasets: [{
        label: 'My First dataset',
        backgroundColor: 'rgb(255, 99, 132)',
        borderColor: 'rgb(255, 99, 132)',
        data: y,
        }]
    },
    options: {
        indexAxis: 'y',
        title: {              // タイトルの設定
            display: true,         // 表示設定
            pocision: "top",       // 表示位置
            fontSize: 18,          // フォントサイズ
            fontColor: "green",    // 文字の色
            text: "タイトル凡例Ａ" // タイトル文字列
        }
    }
    });
// 

</script>

</body>
</html>