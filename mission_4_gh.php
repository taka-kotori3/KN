<html lan="ja">
<head>
<title>mission</title></head>
<body>
<meta charset="utf-8">

<?php
header("Content-Type: text/html; charset=UTF-8");
//接続
$dsn = 'db名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//変数に代入 post
$name = $_POST["namae"];
$ndata = $_POST["toukou_data"];
$dt = date('Y/m/d G:i:s');
$passw = $_POST["pass"];//パスワード
$delete_no = $_POST["delete"];//消去番号
$hensyu_no = $_POST["cha_no1"];//編集番号実行用
$change_no = $_POST["cha_no2"];//編集番号読み取り用

if (empty($delete_no) and empty($hensyu_no) and empty($change_no)){ //消去編集変化番号が空
//データ投稿
if (empty($ndata) or empty($name) or empty($passw)){
  echo "名前 と コメント と パスワードを 入力してください";}

 elseif (!empty($ndata) and !empty($name) and !empty($passw)){


  $sql = $pdo -> prepare("INSERT INTO tb1(name,comment,dt,pas) VALUES(:name,:comment,:dt,:pas)");
  $sql -> bindParam(':name',$name,PDO::PARAM_STR);
  $sql -> bindParam(':comment',$ndata,PDO::PARAM_STR);
  $sql -> bindParam(':dt',$dt,PDO::PARAM_STR);
  $sql -> bindParam(':pas',$passw,PDO::PARAM_STR);
  $sql -> execute();
 }
}

//データ消去
elseif (!empty($delete_no) and !empty($passw) and empty($hensyu_no) and empty($change_no)){ //消去番号が入ってる

 $pass_ok = NULL;//正パスの初期化
 $sql = 'SELECT*FROM tb1 ORDER BY id ASC';
 $result = $pdo -> query($sql);
 
 foreach ($result as $row){
 
  if($delete_no == $row['id']){//消去番号がパス番号と一致の場合
	$pass_ok = $row['pas'];}//同じパス番号のパスを正解パスとする
//ここの閉じかっこはここでok？
	
  if($pass_ok != $passw and $delete_no == $row['id']){
	echo "番号か パスワードが違います";//echoを1回だけ出力させたい
  }//if のとじかっこ

  elseif($pass_ok == $passw and $delete_no == $row['id']){
	$sql = "delete from tb1 where id=$delete_no";//idが消去番号と同じ時、消去→消されずそのまま残ってる
	$result = $pdo->query($sql);

  }//elseif のとじかっこ
 }//foreachのとじかっこ
}

elseif(empty($passw)){
 echo "パスワードを入力してください";}

//取り出す(選ぶ)
elseif (!empty($change_no) and !empty($passw) and empty($hensyu_no) and empty($delete_no)){

 $pass_ok = NULL;//正パスの初期化
 $sql = 'SELECT*FROM tb1 ORDER BY id ASC';
 $result = $pdo -> query($sql);
 
 foreach ($result as $row){

   if($change_no == $row['id']){//変化番号がパス番号と一致の場合
	$pass_ok = $row['pas'];}//同じパス番号のパスを正解パスとする
//ここの閉じかっこはここでok？

	if ($pass_ok == $passw and $change_no == $row['id']){//編集番号と投稿番号が一致の時
	 $change_name = $row['name'];//名前を変数change_nameに代入
	 $change_txt = $row['comment'];//テキストを変数change_txtに代入
	}
     elseif($pass_ok != $passw and $change_no == $row['id']){
	echo "番号か パスワードが違います";
	}

 }//foreachの閉じ
}//eiseifの閉じ

//編集実行
elseif (!empty($hensyu_no) and !empty($passw) and empty($change_no) and empty($delete_no)){

 $sql = 'SELECT*FROM tb1 ORDER BY id ASC';
 $result = $pdo -> query($sql);
 
 foreach ($result as $row){

	if ($hensyu_no == $row['id']){
		$sql2 = "update tb1 set name='$name',comment='$ndata',dt='$dt',pas='$passw' where id=$hensyu_no";
		//update tb名 set カラム名='編集文字',・・・ where どこを指定するか
		$result = $pdo->query($sql2);
	}
 }//foreachの閉じ

 echo "編集が 完了しました<br>";
}//eiseifの閉じ


?>

<form action="" method="post">
    <input type ="text" name="namae" size="35" value="<?php echo $change_name;?>" /> <br><!--名前-->
    <textarea name="toukou_data" rows="3" cols="50" /><?php echo $change_txt;?></textarea> <br><!--コメント-->
    <input type ="text" name="pass" size="35" placeholder="パスワード" /> <br><!--パスワードvalue=<?php echo $change_pass;?>-->
	<input type ="hidden" name="cha_no1" value="<?php echo $change_no;?>"/> <!--編集番号-->
    <input type ="submit" value="送信" />
</form>

<form action="" method="post">
    <input type ="text" name="delete" size="15" placeholder="削除対象番号" /> <br>
    <input type ="text" name="pass" size="35" placeholder="パスワード" /> <br><!--パスワード-->
    <input type ="submit" value="削除" />
</form>

<form action="" method="post">
    <input type ="text" name="cha_no2" size="15" placeholder="編集対象番号" /> <br>
    <input type ="text" name="pass" size="35" placeholder="パスワード" /> <br><!--パスワード-->
    <input type ="submit" value="編集" />
</form>


<?php

$sql = 'SELECT*FROM tb1 ORDER BY id ASC';
$results = $pdo -> query($sql);
foreach($results as $row){
 echo $row['id'].' ';
 echo $row['name'].' ';
 echo $row['comment'].' ';
 echo $row['dt'].'<br>';

}

?>



</body>
</html>
