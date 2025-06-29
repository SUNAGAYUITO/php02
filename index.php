<?php
// DB接続設定
try {
  $pdo = new PDO('mysql:dbname=book_db;charset=utf8mb4;host=localhost','root','');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  exit('DB_CONNECT_ERROR:'.$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>本の検索＆ブックマーク登録</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { padding: 20px; }
    #book-list tr:hover { background-color: #f0f0f0; cursor: pointer; }
  </style>
</head>
<body>

<div class="container">
    <a href="select.php" class="btn btn-info" style="margin-bottom: 20px;">登録一覧を見る</a>
  <h1>Book検索</h1>
  <div class="form-inline">
    <input type="text" id="key" class="form-control" placeholder="キーワードを入力" />
    <button id="search-btn" class="btn btn-primary">検索</button>
  </div>

  <table id="book-list" class="table table-bordered table-striped" style="margin-top:20px;">
    <thead>
      <tr><th>書籍名</th><th>出版社</th><th>画像</th></tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- コメント入力モーダル -->
<div id="commentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
 <div class="modal-dialog">
   <div class="modal-content">
     <div class="modal-header">
       <h4 class="modal-title" id="modalLabel">コメントを登録</h4>
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
     </div>
     <div class="modal-body">
       <form id="bookmarkForm">
         <input type="hidden" id="bookName" name="name" />
         <input type="hidden" id="bookUrl" name="url" />
         <div class="form-group">
           <label for="comment">コメント</label>
           <textarea id="comment" name="comment" class="form-control" rows="4" required></textarea>
         </div>
         <button type="submit" class="btn btn-success">登録する</button>
       </form>
       <div id="formMsg" style="margin-top:10px;"></div>
     </div>
   </div>
 </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

<script>
$(function(){
  // 検索ボタン押下時
  $('#commentModal').on('shown.bs.modal', function () {
  $('#commentModal').find('input, button, textarea, select').filter(':visible:first').focus();
});

  $("#search-btn").on("click", function(){
    const keyword = $("#key").val().trim();
    if(!keyword){
      alert("キーワードを入力してください");
      return;
    }
    const url = "https://www.googleapis.com/books/v1/volumes?q=" + encodeURIComponent(keyword);

    $.ajax({
      url: url,
      dataType: "json"
    }).done(function(data){
      let html = "";
      if(!data.items || data.items.length === 0){
        html = '<tr><td colspan="3">該当する書籍がありません</td></tr>';
      } else {
        data.items.forEach(function(item){
          const title = item.volumeInfo.title || "タイトルなし";
          const publisher = item.volumeInfo.publisher || "出版社なし";
          const thumbnail = item.volumeInfo.imageLinks ? item.volumeInfo.imageLinks.thumbnail : "";
          const infoLink = item.volumeInfo.infoLink || "#";

          html += `<tr class="book-row" data-name="${title}" data-url="${infoLink}">
            <td>${title}</td>
            <td>${publisher}</td>
            <td>${thumbnail ? `<a href="${infoLink}" target="_blank"><img src="${thumbnail}" alt="${title}"></a>` : "画像なし"}</td>
          </tr>`;
        });
      }
      $("#book-list tbody").html(html);
    }).fail(function(){
      alert("検索に失敗しました。通信環境を確認してください。");
    });
  });

  // 書籍行クリックでコメント入力モーダル表示
  $(document).on("click", ".book-row", function(){
    const name = $(this).data("name");
    const url = $(this).data("url");

    $("#bookName").val(name);
    $("#bookUrl").val(url);
    $("#comment").val("");
    $("#formMsg").text("");
    $("#commentModal").modal("show");
  });

  // フォーム送信（登録）
  $("#bookmarkForm").on("submit", function(e){
    e.preventDefault();

    const formData = {
      name: $("#bookName").val(),
      url: $("#bookUrl").val(),
      comment: $("#comment").val()
    };

    $.ajax({
      url: "insert.php",  // ここは別ファイルでPOST受け取り＆DB登録処理を実装してください
      type: "POST",
      data: formData,
      dataType: "json"
    }).done(function(res){
      if(res.status === "success"){
        $("#formMsg").css("color", "green").text("登録が完了しました！");
        setTimeout(() => {
          $("#commentModal").modal("hide");
        }, 1500);
      } else {
        $("#formMsg").css("color", "red").text("登録に失敗しました: " + res.message);
      }
    }).fail(function(jqXHR, textStatus, errorThrown){
  $("#formMsg").css("color", "red").text("通信エラーが発生しました。");
  console.log("Error:", textStatus, errorThrown);
});

  });
});
</script>

</body>
</html>
