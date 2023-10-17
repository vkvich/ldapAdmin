<?php

include ("options/options.php");

$data = $_POST["data"];

$ld = ldap_connect ($ser_ldap);
$ldap_res = ldap_search ($ld,$userbase,"uid=*");
$ent = ldap_get_entries($ld, $ldap_res);
#asort($ent);

?>
	<div style="float: left;">
	<table id="acltable" class="tablesorter">
		<thead>
			<tr style="background-color: #fff8af;" class="noborder">
			<th><p>Пользователь:</p></th>
			<th><p>Права:</p></th>
			</tr>
		</thead>
		<tbody>
	<?php
	foreach ($ent as $value) {
		$uid = $value["uidnumber"][0];
		if ($uid) { 
		$rows = preg_split("/\n/",$data);
		$usercolor = $rc = $wc = "grey";
		$r = $w = 1;
		foreach ($rows as $val) {
			if (preg_match ("/^user:$uid:r/", $val)) { $rc = $usercolor = "red"; $r = 0; }
			if (preg_match ("/^user:$uid:.*w/", $val)) { $wc = $usercolor = "red"; $w = 0; }
		}
		?>
		<tr class="noborder">
			<td style="cursor: pointer;">
				<p style="color: <?php print $usercolor; ?>;"><?php print $value["gecos"][0]; ?></p>
			</td>
			<td>
				<span class="grp" style="color: <?php echo $rc; ?>;" onclick="set_acl('<?php print $_POST['elem']; ?>','<?php print $uid; ?>','<?php print $r; ?>','<?php print (($w + 1) % 2);?>','<?php print $_POST['idd']; ?>');">чтение</span>
				<span class="grp" style="color: <?php echo $wc; ?>;" onclick="set_acl('<?php print $_POST['elem']; ?>','<?php print $uid; ?>','<?php print (($r + 1) % 2); ?>','<?php print $w;?>','<?php print $_POST['idd']; ?>');">запись</span>
			</td>
		</tr>
		<?php
		$usercolor = "grey";
		}
	}
	?>
		</tbody>
	</table>
	</div>


