<?php
// 1. DB接続
try {
  $pdo = new PDO('mysql:dbname=book_db;charset=utf8mb4;host=localhost', 'root', '');
} catch (PDOException $e) {
  exit('DB_CONNECT_ERROR:'.$e->getMessage());
}

// 2. データ取得SQL作成
$stmt = $pdo->prepare("SELECT * FROM gs_bm_table;");
$status = $stmt->execute();

// 3. エラーチェック
if ($status == false) {
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}

// 4. データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ブックマーク一覧</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">

  <style>div{padding: 10px; font-size: 16px;}</style>
</head>
<body>


<!-- Main -->
<div class="container">
<a href="index.php" class="btn btn-info" style="margin-bottom: 20px;">ブックマーク登録</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th><th>書籍名</th><th>URL</th><th>コメント</th><th>登録日時</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($values as $value){ ?>
        <tr>
          <td><?= htmlspecialchars($value["id"], ENT_QUOTES) ?></td>
          <td><?= htmlspecialchars($value["name"], ENT_QUOTES) ?></td>
          <td><a href="<?= htmlspecialchars($value["url"], ENT_QUOTES) ?>" target="_blank"><?= htmlspecialchars($value["url"], ENT_QUOTES) ?></a></td>
          <td><?= htmlspecialchars($value["comment"], ENT_QUOTES) ?></td>
          <td><?= htmlspecialchars($value["indate"], ENT_QUOTES) ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<script>
  // JSON確認用（開発者ツールのConsoleに表示）
  const json = '<?= $json ?>';
  console.log(JSON.parse(json));
</script>

</body>
</html>
