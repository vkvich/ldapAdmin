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
	<title>Доступ к общим сетевым ресурсам. ООО "Сатурн Сибирь" Филиал г.<?php print $region; ?></title>
	<script type="text/javascript" src="scripts/jquery-latest.js"></script>
	<script type="text/javascript" src="scripts/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="scripts/jquery.uitablefilter.js"></script>
</head>
<body>
<?php include("top.php"); ?>
<h1 style="margin-top: 20px; text-align: center;">Общие сетевые каталоги ОС Linux:</h1>
<div id="files" style="margin-top: 1%; margin-left: 10%; float: left;"></div>
<div id="context" style="margin-top: 1%; margin-left: 2%; float: left;"></div>
<br>
<br>
<br>
<?php include("bottom.php"); ?>
<script>

var dir = "<?php print $sharemount; ?>/";
var curdir = dir;
var active_elem = -1;
var cur_elem = -1;

$(document).ready(function() {
	draw_files();
} 
);

function draw_files() {
	$.post("genfiles.php", { dir: dir, curdir: curdir },
	function(data){
		$("#files").html(data);
		$("#filemanager").tablesorter({widgets: ['zebra']});
	});
}

function go_folder(folder) {
	$("#context").html("");
	active_elem = 0;
	curdir = curdir + folder + "/";
	draw_files();
}

function go_back() {
	$("#context").html("");
	active_elem = 0;
	curdir = curdir.substring(0,curdir.length - 1);
	curdir = curdir.substring(0, curdir.lastIndexOf('/')+1);
	draw_files();
}

function toggle_perm(mode,usr,oper,elem) {
	$.post("perm.php", { mode: mode, usr: usr, oper: oper, elem: elem }, function() {
		draw_files();
	});
}

function ch_own(uid,idd,element) {
	if (active_elem == 1) {
		$("#"+cur_elem).css("background-color","white");
		$("#context").html("");
		active_elem = 0;
	}
	else {
		$("#"+idd).css("background-color","#DADADA");
		$.post("genlistown.php", { mode: 'own', uid: uid, element: element }, function(data) {
			$("#context").html(data);
			$("#own").tablesorter({sortList: [[0,0]], widgets: ['zebra']});
		});
		active_elem = 1;
	}
	cur_elem = idd;
}

function set_own(uid,gid,element) {
	$.post("perm.php", { mode: 'own', uid: uid, gid: gid, element: element }, function() {
		draw_files();
		$("#context").html("");
	});
}

function show_acl(elem,idd) {
	if (active_elem == 1) {
		$("#"+cur_elem).css("background-color","white");
		$("#context").html("");
		active_elem = 0;
	}
	else {
		$("#"+idd).css("background-color","#DADADA");
		draw_acl(elem,idd);
		active_elem = 1;
	}
	cur_elem = idd;
}

function draw_acl(elem,idd) {
	$.post("perm.php", { mode: 'acl', elem: elem } , function(data) {
		$.post("genacllist.php", { data: data, elem: elem, idd: idd }, function (data) {
			$("#context").html(data);
			$("#acltable").tablesorter({sortList: [[0,0]], widgets: ['zebra'] });
		});
	});
}
	

function set_acl(elem,uid,r,w,idd) {
	$.post("perm.php", {mode: 'setacl', elem: elem, uid: uid, r: r, w: w }, function (data) {
		draw_acl(elem,idd);
	});
}

function mdir(cur) {
	catalog = $("#catalog").val();
	cat = cur+'/'+catalog;
	$.post("perm.php", { mode: 'mkdir', cat: cat }, function () {
		draw_files();
	});
} 

</script>

</body>
