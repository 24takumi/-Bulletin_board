<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>

<?php
	//データベースへの接続(PDOオブジェクトの生成)
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn,$user,$password);

	//テーブルの作成
	$sql="CREATE TABLE tb1"
	."("
	."id INT,"
	."name char(32),"
	."comment TEXT,"
	."passwords TEXT"
	.");";
	$stmt=$pdo->query($sql);
	 ?>

	<!--編集する文字列を表示させるスキーム-->
	<?php
	if (!empty($_POST['edit_number'])) {
	//DBからの読み込み
	$editid=$_POST['edit_number'];
	$editpass=$_POST['editpassword'];
	$sql="SELECT * FROM tb1 WHERE id = $editid";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$result=$stmt->fetch(PDO::FETCH_ASSOC);
		if ($editpass == $result['passwords']) {
			//入力されたPWとDBのPWが一致したら
			$value1=$result['name'];
			$value2=$result['comment'];
			$value3=$result['passwords'];
		}else{
			echo "パスワードが違います";
		}
	}else {
		//編集モードでないとき
		$value1="";
		$value2="";
		$value3="";
	}
	$value4= $_POST['edit_number'];
?>
<body>
	<form method="post" action="mission_4.php">
		<input type="text" name="name" placeholder="名前" value="<?php echo $value1?>" size="20"><br>
		<input type="text" name="comments" placeholder="コメント" value="<?php echo $value2?>" size="40"><br>
		<input type="text" name="passwords" placeholder="パスワード" value="<?php echo $value3?>" size="40"><br>
		<input type="hidden" name="edit" value="<?php echo $value4?>"><br>
		<input type="submit" value="送信">
	</form>

	<form method="post" action="mission_4.php">
		<input type="text" name="deletenum" placeholder="削除対象番号" size="40"><br>
		<input type="text" name="deletepassword" placeholder="パスワード" value="" size="40"><br>
		<input type="submit" value="削除">
	</form>

	<form method="post" action="mission_4.php">
		<input type="text" name="edit_number" placeholder="編集対象番号" value="" size="40"><br>
		<input type="text" name="editpassword" placeholder="パスワード" value="" size="40"><br>
		<input type="submit" value="編集">
	</form>
</body>
<?php
	//入力機能
	if(empty($_POST['edit'])){
		if(!empty($_POST['comments'])){
			$sql=$pdo->prepare("INSERT INTO tb1(id,name,comment,passwords) VALUES(:id,:name,:comment,:passwords)");
			$sql->bindParam(':id',$id,PDO::PARAM_STR);
			$sql->bindParam(':name',$name,PDO::PARAM_STR);
			$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
			$sql->bindParam(':passwords',$passwords,PDO::PARAM_STR);

			$id_max = intval($pdo->query("SELECT max(id) FROM tb1")->fetchColumn());
			//echo $id_max;
			$id=$id_max+1;
			$name=$_POST['name'];
			$comment=$_POST['comments'];
			$passwords=$_POST['passwords'];
			$sql->execute();
		}
	}elseif($_POST['edit']>=0){
		//編集機能
		$id=$_POST['edit'];
		$name=$_POST['name'];
		$comment=$_POST['comments'];
		$passwords=$_POST['passwords'];
		$sql="update tb1 set name='$name',comment='$comment',passwords='$passwords'where id=$id";
		$result=$pdo->query($sql);
	}
	echo '<br>';
	//削除機能
	if(!empty($_POST['deletenum'])){
		$id=$_POST['deletenum'];
		$delpass=$_POST['deletepassword'];
		$sql="SELECT * FROM tb1 WHERE id = $id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$result=$stmt->fetch(PDO::FETCH_ASSOC);
		//echo $result['passwords'];
		if ($delpass==$result['passwords']) {
			$sql="delete from tb1 where id=$id";
			$result=$pdo->query($sql);
		}
	}
	//表示機能
	$sql="SELECT * FROM tb1 ORDER BY id ASC";
	$results=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	foreach((array)$results as $row){
	  echo $row['id'].',';
	  echo $row['name'].',';
	  echo $row['comment']/*.',';
	  echo $row['passwords']*/.'<br>';
		//↑PWがわからなくなったときはここのアウトコメントを外します。
	}
?>
