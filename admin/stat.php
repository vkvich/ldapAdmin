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
	<title>Список ПК. Сатурн-<?php print $region; ?></title>
	<script type="text/javascript" src="scripts/jquery-latest.js"></script>
	<script type="text/javascript" src="scripts/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="scripts/jquery.uitablefilter.js"></script>
</head>

<body>
	<?php include("top.php"); ?>
	<div style="position: fixed; top: 30px; right: 10px;"><button onclick="drawTable();">Обновить</button></div>
	<div style="margin-top: 10px;">
	<center><p>Фильтр:</p><input onkeyup="upd();" type="text" name="filter" id="fr"><br>
	<div id="loading"><p>Идет генерация таблицы</p><br><img src="loading.gif" alt="Loading..."></div></center>
	<div id="intab"></div>
	</div>
	<script>
		$(document).ready(function() {
//			setInterval("drawTable()", 10000);
			drawTable();
		});
		
		function drawTable() {
			
			$("#intab").hide();
			$("#loading").show();
			
			$.post("genlist2.php",
			function(data){
				$("#intab").html(data);
				$("#comps").tablesorter({sortList: [[0,0],[1,0]], widgets: ['zebra']});
				
				$("#intab").show();
				$("#loading").hide();
			
			});

		}
		
		function upd() {
			t = $("#comps");
			phrase = document.getElementById("fr").value;
			$.uiTableFilter( t, phrase );
			$("#comps").tablesorter({sortList: [[0,0],[1,0]], widgets: ['zebra']});
		}
		
		function sshrn(d,h,u) {				
			switch(d) {
				case "0": {
					desc = "Пересоздать wine-окружение";
					break;
				}
				case "1": {
					desc = "Убить все запущенные 1С";
					break;
				}
				case "2": {
					desc = "Перезагрузить компьютер";
					break;
				}
				case "3": {
					desc = "Выключить компьютер";
					break;
				}
			}
				
			var r=confirm(desc + " " + h + "?" + "\nПользователь: " + u);
			if(r == true) {
					$.post("sshrn.php", { dnum: d, pc: h } );
			}
		}
				
		
	</script>
	<?php include("bottom.php"); ?>
</body>
</html>

