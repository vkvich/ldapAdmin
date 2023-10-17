<?php

include ("options/options.php");

$ld = ldap_connect ($ser_ldap);

if ($_POST["mode"] == "own") {
	$ldap_res = ldap_search ($ld,$userbase,"uid=*");
	$ent = ldap_get_entries($ld, $ldap_res);
	asort($ent);

	?>
	<table id="own" class="tablesorter">
		<thead>
			<tr style="background-color: #fff8af;" class="noborder">
			<th><p>Пользователь:</p></th>
			</tr>
		</thead>
		<tbody>
	<?php
	foreach ($ent as $value) {
		if ($value["uidnumber"][0] == $_POST["uid"]) { $colorown = "red"; }
		else {$colorown = "grey";}
		if ($value["gidnumber"][0] > 1000 ) { $str = "usergrp_".$value["gidnumber"][0]; }
		else {$str = $value["gidnumber"][0]; }
		?>
		<tr class="noborder">
			<td style="cursor: pointer;" onclick="set_own('<?php print $value["uid"][0]; ?>','<?php print $str; ?>','<?php print $_POST["element"]; ?>')">
				<p style="color: <?php print $colorown; ?>;"><?php print $value["gecos"][0]; ?></p>
			</td>
		</tr>
		<?php
	}
	?>
		</tbody>
	</table>
	<?php
}
?>
