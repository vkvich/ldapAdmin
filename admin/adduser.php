<?php
session_start();
include 'options/options.php';
if(!isset($_SESSION['loguser'])) {header('Location: index.php'); exit();}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link type="text/css" href="base.css" rel="stylesheet">
	<meta name="author" content="c55fun">
	<title>Добавление пользователя ОС Linux. Сатурн-<?php print $region; ?>"</title>
	<script type="text/javascript" src="scripts/jquery-latest.js"></script>
	<script type="text/javascript" src="scripts/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="scripts/jquery.uitablefilter.js"></script>
</head>
<body>
<?php include("top.php"); ?>

<table>
	<tr class="noborder">
	<td></td>
	<td>
		<h1 style="text-align: center;">Список пользователей ОС Linux:</h1>
		<center><p>Фильтр:</p><input onkeyup="upd();" type="text" name="filter" id="fr"></center><br>
	</td>
	<td></td>
	</tr>
	<tr class="noborder">
	<td valign="top">
	<table>
		<tr class="noborder" id="l">
			<td><p style="text-align: right;">*Логин:</p></td>
			<td><input onkeyup="testUser();" id="user" type="text"><div id="userid"></div></td>
		</tr>
		<tr class="noborder" id="p1">
			<td><p style="text-align: right;">*Пароль:</p></td>
			<td><input onkeyup="testPass();" onblur="testPass('pass');" id="pass_1" type="password"></td>
		</tr>
		<tr class="noborder" id="p2">		
			<td><p style="text-align: right;">*Еще раз:</p></td>
			<td><input onkeyup="testPass('pass');" onblur="testPass('pass');" id="pass_2" type="password"><div id="pass_i"></div></td>
		</tr>
		<tr id="f" class="noborder">
			<td><p style="text-align: right;">*Фамилия:</p></td>
			<td><input id="fam" type="text"></td>
		</tr>
		<tr id="n" class="noborder">		
			<td><p style="text-align: right;">Имя:</p></td>
			<td><input onkeyup="testAll();" id="nam" type="text"></td>
		</tr>
		<tr class="noborder">
			<td><p style="text-align: right;">Телефон:</p></td>
			<td><input onkeyup="testTel();" id="tel" type="text"><div id="tel_i"></div></td>
		</tr>
		
		<tr class="noborder">
			<td><p style="text-align: right;">Мобильный:</p></td>
			<td><input onkeyup="testTel();" id="telmob" type="text"></td>
		</tr>
				
		<tr class="noborder">
			<td><p style="text-align: right;">e-Mail:</p></td>
			<td><input onkeyup="testMail();" id="mail" type="text"><div id="mail_i"></div></td>
		</tr>
		<tr class="noborder">
			<td></td>
			<td><br><button onclick="addUser();" id="but">Добавить</button></td>
		</tr>
		<tr class="noborder">
			<td><br><br></td>
		</tr>		
		<tr class="noborder">
			<td><p style="text-align: right;">Имя группы:</p></td>
			<td><input onkeyup="testGroup();" id="group" type="text"><div id="groupid"></div></td>
		</tr>
		<tr class="noborder">
			<td><p style="text-align: right;">GID группы:</p></td>
			<td><input onkeyup="testGID();" id="gid" type="text"><div id="gidid"></div></td>
		</tr>
		<tr class="noborder">
			<td colspan="2"><div id="grouplist"></div></td>
		</tr>
		<tr class="noborder">
			<td></td>
			<td><br><button onclick="addGroup();" id="but2">Добавить</button></td>
		</tr>			
		<tr class="noborder">
			
			<td colspan="2"><br><div id="monitor"></div></td>
		</tr>	
	</table>
	</td>
	<td>
	<div id="intab"></div>
	

	</td>
	</tr>
</table>
<?php include("bottom.php"); ?>

<script>

var lastid = "none";

$(document).ready(function() {
	var lastid = "none";
	drawTable();
	} 
);

function drawTable() { 
	$.post("genlist.php", { oper: 'user' },
	function(data){
		$("#intab").html(data);
		$("#users").tablesorter({sortList: [[0,0],[1,0]], widgets: ['zebra']});
	});
	$.post("genlist.php", { oper: 'group' },
	function(data){
		$("#grouplist").html(data);
		$("#groups").tablesorter({sortList: [[0,0]], widgets: ['zebra']});
	});
}

function upd() {
	t = $("#users");
	phrase = document.getElementById("fr").value;
	$.uiTableFilter( t, phrase );
	$("#users").tablesorter({sortList: [[0,0],[1,0]], widgets: ['zebra']});
}


function testPass(idd) {
	if($("#" + idd + "_1").val() != $("#" + idd + "_2").val()) {
		$("#" + idd + "_i").html('<p class="helperr">Пароли не совпадают!</p>');
		if(idd != "pass") {$("#" + idd + "_b").attr("disabled","disabled");}
	}
	else if ($("#" + idd + "_1").val() == "" && $("#" + idd + "_2").val() == "") {
		$("#" + idd + "_i").empty();
		if(idd != "pass") {$("#" + idd + "_b").attr("disabled","disabled");}
	}
	else {
		$("#" + idd + "_i").html('<p class="helpok">Пароли совпадают.</p>');
		if(idd != "pass") {$("#" + idd + "_b").attr("disabled","");}
	}
}

function testUser() {
	//$("#fam").val($("#user").val());
	var parm = $("#user").val();
	$.post("ldapuser.php",{testuser: parm}, function(data) {
		if(data.res == true) {
			$("#user").css("color", "black");
			$("#userid").empty();
		}
		else {
			$("#user").css("color", "red");
			$("#userid").html('<p class="helperr">Пользователь существует!</p>');
		}
	},"json");
}

function testTel() {
	var filter = /^[0-9]+$/;
	var tl = $("#tel").val();
	if(filter.test(tl) || tl == "") {$("#tel_i").empty();}
	else {$("#tel_i").html('<p class="helperr">Недопустимы номер телефона!</p>');}
}	

function testMail() {
	var filter = /^[a-zA-Z0-9\._]+\@[a-zA-Z\._]+\.[a-zA-Z]+$/;
	var ml = $("#mail").val();
	if(filter.test(ml) || ml == "") {$("#mail_i").empty();}
	else {$("#mail_i").html('<p class="helperr">Недопустимый адрес!</p>');}
}

function testGroup() {
	var parm = $("#group").val();
	$.post("ldapuser.php",{grp: parm}, function(data) {
		if(data.res == 0) {
			$("#group").css("color", "black");
			$("#groupid").empty();
		}
		else {
			$("#group").css("color", "red");
			$("#groupid").html('<p class="helperr">Группа существует!</p>');
		}
	},"json");
}
function testGID() {
	var parm = $("#gid").val();
	$.post("ldapuser.php",{gid: parm}, function(data) {
		if(data.res == 0) {
			$("#gid").css("color", "black");
			$("#gidid").empty();
		}
		else {
			$("#gid").css("color", "red");
			$("#gidid").html('<p class="helperr">GID существует!</p>');
		}
	},"json");
}


function addGroup() {
	var group_name = $("#group").val();
	var group_id = $("#gid").val();
	
	$.post("ldapuser.php", { group_name: group_name , group_id: group_id }, function(data) {
		var text = "";			
		text = text + ((data.stat == true)?'<p class="helpok">Группа ' + data.name + data.test + ' добавлена</p>':'<p class="helperr">Группа ' + data.name + data.test + ' не добавлена</p>');			
		$("#group").val('');
		$("#gid").val('');
		drawTable();
		$("#monitor").html(text);
	},"json");	
}

function addUser() {
	$("#monitor").html('');
	var user = $("#user").val();
	var pass = $("#pass_2").val();
	var nam = $("#nam").val();
	var fam = $("#fam").val();
	var tel = $("#tel").val();
	var telmob = $("#telmob").val();
	var mail = $("#mail").val();	
	var gecos = fam + " " +  nam;
	$.post("ldapuser.php", { user: user, fam: fam, nam: nam, gecos: gecos, pass: pass, tel: tel, telmob: telmob, mail: mail }, function(data) {
		var text = "";
		if(data.stat == "ok") {	
			text = text + ((data.ldapus == true)?'<p class="helpok">Пользователь ' + data.us + ' добавлен</p>':'<p class="helperr">Пользователь ' + data.us + ' не добавлен</p>');
			text = text + ((data.ldapgr == true)?'<p class="helpok">Группа ' + data.gr + ' добавлена</p>':'<p class="helperr">Группа ' + data.gr + ' не добавлена</p>');
			text = text + ((data.skel == true)?'<p class="helpok">Профиль скопирован без ошибок</p>':'<p class="helperr">При копировании профиля произошла ошибка</p>');
			text = text + ((data.own == true)?'<p class="helpok">Успешно назначен владелец профиля</p>':'<p class="helperr">Ошибка при назначении владельца профиля</p>');
			text = text + ((data.mod == true)?'<p class="helpok">Успешно применены права на файлы</p>':'<p class="helperr">Ошибка при назначение прав на файлы</p>');
			text = text + ((data.samba == true)?'<p class="helpok">Успешно добавлен к пользователям SAMBA</p>':'<p class="helperr">Ошибка при добавлении к пользователям SAMBA</p>');		
			$("#pass_1").val('');
			$("#pass_2").val('');
			$("#nam").val('');
			$("#fam").val('');
			$("#desc").val('');
			$("#user").val('');
			$("#pass_i").empty();
			$("#tel").val('');
			$("#telmob").val('');
			$("#mail").val('');
			drawTable();
			$("#monitor").html(text);
		}
		else {
			alert("Произошла ошибка!\nПользователь не добавлен!");
		}
	},"json");
}

function delUser(uidn) {
	$("#monitor").html('');
	var text = "";
	var gec = "#" + uidn + "_gecos";
	var r=confirm("Удалить пользователя " + $(gec).html() + "?");
	if(r == true) {
		$.post("ldapuser.php", { uidnumber: uidn }, function(data) {
			if(data.stat == "ok") {
				drawTable();
				text = text + ((data.ldapus == true)?'<p class="helpok">Пользователь ' + data.us + ' удален</p>':'<p class="helperr">Пользователь ' + data.us + ' не удален</p>');
				text = text + ((data.ldapgr == true)?'<p class="helpok">Группа пользователя удалена</p>':'<p class="helperr">Группа пользователя не удалена</p>');
				text = text + ((data.backup == true)?'<p class="helpok">Профиль успешно перенесен в резерв</p>':'<p class="helperr">Проблема с переносом профиля</p>');
				text = text + ((data.samba == true)?'<p class="helpok">Удален из пользователей SAMBA</p>':'<p class="helperr">Проблема удаления из пользователей SAMBA</p>');
				text = text + ((data.acl == true)?'<p class="helpok">Удалены права ACL</p>':'<p class="helperr">Проблема удаления прав ACL</p>');
				text = text + ((data.acldef == true)?'<p class="helpok">Удалены права ACL по-умолчанию</p>':'<p class="helperr">Проблема удаления прав ACL по-умолчанию</p>');
				$("#monitor").html(text);
				}
			else {alert("Ошибка!");}
		},"json");
	}
}

function openDiv(oper,uidn) {
	if(lastid != "none") { $(lastid).css("display", "none"); }
	var idd = "#" + uidn;
	if(lastid == idd) {
//		$(idd).css("display", "none");
		$(idd).hide();
		$(idd).html('');
		lastid = "none";
	}
	else {
		$("#monitor").html('');
		var text = "";
//		$(idd).css("display", "block");
		$(idd).show();
		if(oper == "pass") {
			$(idd).html('<table><tbody><tr class="noborder"><td><input type="password" size="16" onkeyup="testPass(\'' + uidn + '\');" id="' + uidn + '_1"></td><td rowspan="2"><button id="' + uidn + '_b" onclick="changePass(\'' + uidn + '\');" disabled>Изменить</button></td></tr><tr class="noborder"><td><input type="password" size="16" onkeyup="testPass(\'' + uidn + '\');" id="' + uidn + '_2"><div id="' + uidn + '_i"></div></td></tr></tbody></table>');
		}
		if(oper == "change") {
			var gec = "#" + uidn + "_gecos";
			var mail = "#" + uidn + "_mail";
			var tel = "#" + uidn + "_tel";
			var telmob = "#" + uidn + "_telmob";
			$(idd).html('<table><tbody><tr class="noborder">\
			<td><p style="text-align: right;">Описание:</p></td><td><input type="text" size="16" value="' + $(gec).html() + '" id="' + uidn + '_newgec"></td>\
			<td rowspan="3"><button onclick="changeGec(\'' + uidn + '\');">Изменить</button></td></tr>\
			<tr class="noborder"><td><p style="text-align: right;">Тел.:</p></td><td><input type="text" size="16" value="' + $(tel).html() + '" id="' + uidn + '_newtel"></td></tr>\
			<tr class="noborder"><td><p style="text-align: right;">Мобильный:</p></td><td><input type="text" size="16" value="' + $(telmob).html() + '" id="' + uidn + '_newtelmob"></td></tr>\
			<tr class="noborder"><td><p style="text-align: right;">e-Mail:</p></td><td><input type="text" size="16" value="' + $(mail).html() + '" id="' + uidn + '_newmail"></td></tr>\
			<tr class="noborder"><td colspan="3"><br></td></tr>\
			<tr class="noborder"><td rowspan="2"><p style="text-align: right;">Пароль:</p></td><td><input type="password" size="16" onkeyup="testPass(\'' + uidn + '\');" id="' + uidn + '_1"></td><td rowspan="2"><button id="' + uidn + '_b" onclick="changePass(\'' + uidn + '\');" disabled>Изменить</button></td></tr><tr class="noborder"><td><input type="password" size="16" onkeyup="testPass(\'' + uidn + '\');" id="' + uidn + '_2"><div id="' + uidn + '_i"></div></td></tr>\
			</tbody></table>');
			
//			$(idd).html('<table><tbody><tr class="noborder">\
//			<td><p style="text-align: right;">Описание:</p></td><td><input type="text" size="16" value="' + $(gec).html() + '" id="' + uidn + '_newgec"></td>\
//			<td><button onclick="changeGec(\'' + uidn + '\');">Изменить</button></td></tr>\
//			<tr class="noborder"><td><br></td></tr>\
//			<tr class="noborder"><td rowspan="2"><p style="text-align: right;">Пароль:</p></td><td><input type="password" size="16" onkeyup="testPass(\'' + uidn + '\');" id="' + uidn + '_1"></td><td rowspan="2"><button id="' + uidn + '_b" onclick="changePass(\'' + uidn + '\');" disabled>Изменить</button></td></tr><tr class="noborder"><td><input type="password" size="16" onkeyup="testPass(\'' + uidn + '\');" id="' + uidn + '_2"><div id="' + uidn + '_i"></div></td></tr>\
//			</tbody></table>');
		}
		lastid = idd;
	}
}

function changePass(uidn) {
	$("#monitor").html('');
	var text = "";
	var psw = $("#" + uidn + "_2").val();
	$.post("ldapuser.php", { uidnumpass: uidn, passw: psw }, function(data) {
		if(data.stat == "ok") {
			text = text + ((data.res == true)?'<p class="helpok">Пароль успешно сменен</p>':'<p class="helperr">Произошла ошибка при смене пароля</p>');
			$("#monitor").html(text);
			} 
		else {alert("Ошибка!");}
	},"json");
	drawTable();
}

function changeGec(uidn) {
	$("#monitor").html('');
	var text = "";
	var gec = $("#" + uidn + "_newgec").val();
	var tel = $("#" + uidn + "_newtel").val();
	var telmob = $("#" + uidn + "_newtelmob").val();
	var mail = $("#" + uidn + "_newmail").val();
	$.post("ldapuser.php", { uidnumgec: uidn, gec: gec, tel: tel, telmob: telmob, mail: mail }, function(data) {
		if(data.stat == "ok") {
			text = text + ((data.res == true)?'<p class="helpok">Описание успешно изменено</p>':'<p class="helperr">Произошла ошибка при изменении описания</p>');
			$("#monitor").html(text);
			} 
		else {alert("Ошибка!");}
	},"json");
	drawTable();
}

function toggle_group(grp,usr) {
	$("#monitor").html('');
	var text = "";
	$.post("ldapuser.php", { tggl_grp: grp, tggl_usr: usr }, function(data) {
		if(data.stat == "ok") {
			text = text + ((data.res == true)?'<p class="helpok">Операция произведена успешно!</p>':'<p class="helperr">Произошла ошибка при операции</p>');
			$("#monitor").html(text);
			}
		else {alert("Ошибка!");}
	},"json");
	drawTable();
}

function delGroup(grp_name) {
	$("#monitor").html('');
	var text = "";
	var r=confirm("Удалить группу " + grp_name + "?");
	if(r == true) {	
	$.post("ldapuser.php", { delgrpname: grp_name }, function(data) {
		text = text + ((data.stat == true)?'<p class="helpok">Группа успешно удалена</p>':'<p class="helperr">Ошибка при удалении группы</p>');
		$("#monitor").html(text); 
	},"json");
	drawTable();
	}
}

</script>
</body>
</html>
