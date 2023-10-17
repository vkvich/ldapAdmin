<?php

define ('UPCASE', 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯABCDEFGHIKLMNOPQRSTUVWXYZ');
define ('LOCASE', 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяabcdefghiklmnopqrstuvwxyz');

include 'options/options.php';

function mb_str_split($str) {        
    preg_match_all('/.{1}|[^\x00]{1}$/us', $str, $ar);
    return $ar[0];
}

function mb_strtr($str, $from, $to) {return str_replace(mb_str_split($from), mb_str_split($to), $str);}

function lowercase($arg=''){return mb_strtr($arg, UPCASE, LOCASE);}
function uppercase($arg=''){return mb_strtr($arg, LOCASE, UPCASE);}

$ld = ldap_connect ($ser_ldap);

$ldap_res = ldap_search ($ld,$userbase,"uid=*");
$ent = ldap_get_entries($ld, $ldap_res);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link type="text/css" href="base.css" rel="stylesheet">
        <meta name="author" content="c55fun">
        <title>Телефонный справочник Сатурн-<?php print $region; ?>"</title>
        <script type="text/javascript" src="scripts/jquery-latest.js"></script>
        <script type="text/javascript" src="scripts/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="scripts/jquery.uitablefilter.js"></script>
</head>
<body>


<table id="users" class="tablesorter">
	<thead>
		<tr style="background-color: #fff8af;">
		<th><p>Пользователь:</p></th>
		<th><p>Телефон:</p></th>
		<th><p>Мобильный:</p></th>
		<th><p>eMail:</p></th>
	</thead>
	<tbody>
<?php

foreach ($ent as $value) {
	if($value["uid"] != "") {
	$name = lowercase($value["uid"][0]);
	$ud = $value["uid"][0];

	?>
	<tr>
	<td><p id="<?php print ($value["uidnumber"][0] . "_gecos"); ?>"><?php print $value["gecos"][0]; ?></p></td>
	<td><p id="<?php print ($value["uidnumber"][0] . "_tel"); ?>"><?php print $value["telephonenumber"][0]; ?></p></td>
	<td><p id="<?php print ($value["uidnumber"][0] . "_telmob"); ?>"><?php print $value["mobile"][0]; ?></p></td>
	<td><p id="<?php print ($value["uidnumber"][0] . "_mail"); ?>"><?php print $value["mail"][0]; ?></p></td>
	</tr>
	<?php
}
}
?>
	</tbody>
</table>
<br><br>
<?php
ldap_close($ld);
$("#users").tablesorter({sortList: [[0,0],[1,0]], widgets: ['zebra']});
?>
