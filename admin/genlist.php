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

$all_gr = array();
$ldap_gr = ldap_search ($ld,$groupbase,"cn=*");
$allgroups = ldap_get_entries($ld, $ldap_gr);
$i=0;
foreach($allgroups as $g) {
#	if( ! (preg_match('/^usergrp/',$g['cn'][0]) || preg_match('/^wine$/',$g['cn'][0]) || preg_match('/^root$/',$g['cn'][0]))) {
	if($g['gidnumber'][0] >= 10000) {
		$mem = array();
		foreach ($g["memberuid"] as $m) {
			array_push($mem,$m);
		}
		if ($g['cn'][0]) {
			$all_gr[$i] = array (
				0 => $g['cn'][0],
				1 => $g['gidnumber'][0],
				2 => $mem );
		}
		$i++;	
	}
}

if ($_POST["oper"] == "user" ) {

$ldap_res = ldap_search ($ld,$userbase,"uid=*");
$ent = ldap_get_entries($ld, $ldap_res);

$link = mysqli_connect($ser_mysql,$mysqladmin,$mysqlpass);
$res = mysqli_select_db ($link,'comps');
$result = mysqli_query($link,"select * from lastlogon;");
$lastl = array();
while ( $row = mysqli_fetch_array($result) ) $lastl[] = $row;

?>
<table id="users" class="tablesorter">
	<thead>
		<tr style="background-color: #fff8af;">
		<th><p>Пользователь:</p></th>
		<th><p>Домашняя папка:</p></th>
		<th><p>Телефон:</p></th>
		<th><p>Мобильный:</p></th>
		<th><p>eMail:</p></th>
		<th><p>Последний LOGON:</p></th>
		<th><p>Группы:</p></th>
		<th style="width: 440px;"><p>Действия:</p></th></tr>
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
	<td><p><?php print $value["homedirectory"][0]; ?></p></td>
	<td><p id="<?php print ($value["uidnumber"][0] . "_tel"); ?>"><?php print $value["telephonenumber"][0]; ?></p></td>
	<td><p id="<?php print ($value["uidnumber"][0] . "_telmob"); ?>"><?php print $value["mobile"][0]; ?></p></td>
	<td><p id="<?php print ($value["uidnumber"][0] . "_mail"); ?>"><?php print $value["mail"][0]; ?></p></td>
	<td><p id="<?php print ($value["uidnumber"][0] . "_logon"); ?>"><?php
		foreach ($lastl as $row) {
			if ($row['id'] == $value["uidnumber"][0] & $row['account'] == $value["uid"][0]) {
				print $row['time']." (".$row['pc'].")";
				break;
			}
		}
?>
	</p></td>
	<td>
	<?php
	foreach($all_gr as $grr) {
		$gr = $grr[0];
		$gr_res = ldap_search ($ld,$groupbase,"(&(cn=$gr)(memberuid=$ud))");
		$gr_entries = ldap_get_entries($ld, $gr_res);
		$col = ($gr_entries["count"] > 0) ? "red" : "#A1A1A1";
		?>
	<span class="grp" onclick="toggle_group('<?php print $gr;?>','<?php print $ud;?>');" style="color: <?php print $col; ?>"><?php print $gr; ?>
	</span>
	<?php } ?>
	</td>

	<td style="text-align: right;">
	<?php if($value["uid"][0] != "root") { ?>
		<span class="link" onclick="openDiv('change',<?php print $value["uidnumber"][0]; ?>);">подробнее...</span>
		<span class="link" onclick="delUser(<?php print $value["uidnumber"][0]; ?>);">удалить</span>
		<div id="<?php print $value["uidnumber"][0]; ?>" style="display: none; padding: 4px; text-align: center;">
		</div>
		<?php } ?>
	</td>
	</tr>
	<?php
}
}
?>
	</tbody>
</table>
<br><br>
<?php
}
if ( $_POST["oper"] == "group" ) {
?>
<br>
<table id="groups" class="tablesorter" style="width: 100%;">
	<thead>
		<tr style="background-color: #fff8af;">
		<th><p>Группа:</p></th>
		<th><p>GID:</p></th>
		<th><p>Действия:</p></th></tr>
	</thead>
	<tbody>
<?php
foreach ($all_gr as $gro) {
	?>
	<tr>
	<td><p id="<?php print ($gro[0] . "_name"); ?>"><?php print $gro[0]; ?></p></td>
	<td><p id="<?php print ($gro[1] . "_gid"); ?>"><?php print $gro[1]; ?></p></td>	
	<td style="text-align: right;">
		<span class="link" onclick="delGroup('<?php print $gro[0];?>')">удалить</span>
	</td>
	</tr>
	<?php
}

?>
	</tbody>
</table>
<?php
}
ldap_close($ld); ?>
