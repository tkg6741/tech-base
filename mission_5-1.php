<!--mission_5-1掲示板-->

<?php
//データベース接続設定
$dsn="データベース名";
$user="ユーザー名";
$password="パスワード";
$pdo=new PDO($dsn,$user,$password, 
     array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS mission5"
       ." ("
       . "id INT AUTO_INCREMENT PRIMARY KEY,"
       . "name char(32),"
       . "comment TEXT,"
       . "date TEXT,"
       . "password char(8)"
       .");";
$stmt = $pdo->query($sql);
//編集ボタンが押された際に
if(isset($_POST["編集submit"])){
 //編集対象番号とパスワードが入力されていれば
 if(!empty($_POST["編集num"]) && !empty($_POST["pass3"])){
  //データを読み込んで
  $sql = "SELECT * FROM mission5";
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  foreach ($results as $row){
   //番号とパスワードが一致した内容をフォームに呼び出す
   if($row["id"]==$_POST["編集num"] && $row["password"]==($_POST["pass3"])){ 
    $編集name=$row["name"];
    $編集comment=$row["comment"];
    $編集pass=$row["password"];
    $編集flag=$row["id"];
   }
  };
  //編集対象番号、パスワードが一致しなかった際のエラーメッセージを作成
  if(empty($編集flag)){
   $エラー="該当する書き込みが存在しないか、パスワードが違います。"; 
  }
  //フォームが未入力の際のエラーメッセージを作成
 }else{
   $エラー="編集対象番号とパスワードを入力してください。";   
  }
}
?>


<!--フォーム準備-->
<!Doctype html>
<html lang="ja">
  <head>
    <meta charaset="UTF-8">
    <title>mission_5-1</title>
  </head>
<body>
mission_5-1掲示板
<form method="post" action="">
<input type="text" name="name" placeholder="名前" value=<?php if(!empty($編集name)){echo $編集name;}?>><br>
<input type="text" name="comment" placeholder="コメント" value=<?php if(!empty($編集comment)){echo $編集comment;}?>><br>
<input type="password" name="pass1" placeholder="パスワード" value=<?php if(!empty($編集pass)){echo $編集pass;}?>>
<input type="submit" name="送信submit" value="送信"><br>
<input type="hidden" name="編集確定num" value=<?php if(!empty($編集flag)){echo $編集flag;}?>>
<input type="number" name="削除num" placeholder="削除対象番号"><br>
<input type="password" name="pass2" placeholder="パスワード">
<input type="submit" name="削除submit" value="削除"><br>
<input type="number" name="編集num" placeholder="編集対象番号"><br>
<input type="password" name="pass3" placeholder="パスワード">
<input type="submit" name="編集submit" value="編集">
</form>
</body>
</html>


<?php

//書き込みを取得した際の表示
if(!empty($編集flag)){
 echo $編集flag."番の書き込み内容を取得しました。";
};
//エラーメッセージの表示
if(!empty($エラー)){
 echo $エラー;
};

//送信ボタン処理
//送信ボタンが押された際に
if(isset($_POST["送信submit"])){
 //フォームがすべて入力されていた場合、
 if(!empty($_POST["name"]) && 
    !empty($_POST["comment"]) && !empty($_POST["pass1"])){
  //編集フラグがない時は
  if(empty($_POST["編集確定num"])){  
   //新規書き込みをして、       
   $sql =$pdo -> prepare("INSERT INTO mission5 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
   $sql -> bindParam(":name", $name, PDO::PARAM_STR);
   $sql -> bindParam(":comment", $comment, PDO::PARAM_STR);
   $sql -> bindParam(":date", $date, PDO::PARAM_STR);
   $sql -> bindParam(":password", $password, PDO::PARAM_STR);
   $name=$_POST["name"];
   $comment=$_POST["comment"];
   $date=date("Y/m/d H:i:s");
   $password=$_POST["pass1"];
   $sql -> execute();
   //内容を表示する。
   echo "書き込み成功！<br>";
   $sql = "SELECT * FROM mission5";
   $stmt = $pdo->query($sql);
   $results = $stmt->fetchAll();
   foreach ($results as $row){
    echo $row["id"]."  ";
    echo $row["name"]."  ";
    echo $row["comment"]."  ";
    echo $row["date"]."  ";
    echo "<hr>";
   }
   //編集フラグが存在する時は編集処理をして、  
  }else{
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y/m/d H:i:s");
    $password = $_POST["pass1"];
    $id = $_POST["編集確定num"];
    $sql = "UPDATE mission5 SET name=:name,comment=:comment, date=:date, password=:password WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
    $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
    $stmt->bindParam(":date", $date, PDO::PARAM_STR);
    $stmt->bindParam(":password", $password, PDO::PARAM_STR);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    //内容を表示する。
    echo "編集成功！<br>";
    $sql = "SELECT * FROM mission5";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
     echo $row["id"]."  ";
     echo $row["name"]."  ";
     echo $row["comment"]."  ";
     echo $row["date"]."  ";
     echo "<hr>";
    }
   }
  //フォームが未入力なら警告を表示
 }else{
   echo "フォームが未入力です。";
  }
};

//削除ボタン処理
//削除ボタンが押された際に、
if(isset($_POST["削除submit"])){
 //編集対象番号とパスワードが入力されていれば
 if(!empty($_POST["削除num"]) && !empty($_POST["pass2"])){
  //データを読み込んで
  $sql = "SELECT * FROM mission5";
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  foreach ($results as $row){
   //結果に応じたフラグを立てる。
   if($row["id"]==$_POST["削除num"] && $row["password"]==$_POST["pass2"]){
    $削除識別flag=0; 
    break;
   }else{
    $削除識別flag=1;     
    }
  };
  //それから、フラグ内容に応じて処理する
  if($削除識別flag==0){
   $id = $_POST["削除num"];
   $sql = 'delete from mission5 where id=:id';
   $stmt = $pdo->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_INT);
   $stmt->execute();
   echo "削除成功！<br>";
   $sql = "SELECT * FROM mission5";
   $stmt = $pdo->query($sql);
   $results = $stmt->fetchAll();
   foreach ($results as $row){
    echo $row["id"]."  ";
    echo $row["name"]."  ";
    echo $row["comment"]."  ";
    echo $row["date"]."  ";
    echo "<hr>";
   }
  };
  if($削除識別flag==1){
   echo "該当する書き込みが存在しないか、パスワードが違います。";
  }
  //フォームが未入力なら警告を表示
 }else{
   echo"削除対象番号とパスワードを入力してください。";
  }
}
?>
    