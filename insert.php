<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$name    = $_POST["name"];
$url     = $_POST["url"];
$comment = $_POST["comment"];

try {
  $pdo = new PDO('mysql:dbname=book_db;charset=utf8mb4;host=localhost','root','');
} catch (PDOException $e) {
  exit('DB_CONNECT:'.$e->getMessage());
}

$sql = "INSERT INTO gs_bm_table(name, url, comment, indate) VALUES(:name, :url, :comment, sysdate())";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':url', $url, PDO::PARAM_STR);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);  // ←修正ここ！INTになってた
$status = $stmt->execute();

if ($status == false) {
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
} else {
  header("Location: index.php");
  exit();
}
?>
