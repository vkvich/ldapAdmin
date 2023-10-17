<?php
session_start();
$errortext = "";

include 'options/options.php';

if(isset($_POST["log_in"])) {
	$user = $_POST['loguser'];
	$pass = $_POST['logpass'];
	if($user == $username && $pass == $userpass) {
		$_SESSION['loguser'] = $_POST['loguser'];
		header('Location: adduser.php');
	}
	else {unset($_SESSION['loguser']); $errortext="<p class='helperr'>Введите правильный логин/пароль.</p>";}
}	
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link type="text/css" href="base.css" rel="stylesheet">
	<meta name="author" content="c55fun">
	<title>Вход в систему</title>
</head>
<body>
	<?php
	if($errortext != "") print $errortext;
	?>
<br>
<center>
</center>
<form method="post"> 
<table>
<tbody>
<tr class="noborder">
	<td><p style="text-align: right;">Логин: </p></td>
	<td><input type="text" name="loguser"></td>
</tr><tr class="noborder">
	<td><p style="text-align: right;">Пароль: </p></td>
	<td><input type="password" name="logpass"></td>
</tr><tr class="noborder">
	<td></td>
	<td><br><input type="submit" name="log_in" value="Войти"></td>
</tr>
</tbody>
</table>
</form>
<?php include("bottom.php"); ?>
</body>
</html>
