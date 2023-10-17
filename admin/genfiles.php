<?php

$dir = $_POST["dir"];
$curdir = $_POST["curdir"];
$prevdir = $_POST["prevdir"];
?>
<table id="filemanager" class="tablesorter">
	<thead>
		<tr style="background-color: #fff8af;">
		<th><p></p></th>
		<th><p>Имя:</p></th>
		<th><p>Владелец:</p></th>
		<th><p>Права владельца</p></th>
		<th><p>Права всех</p></th>
	</thead>
	<tbody>

<?php
if ($dh = opendir($curdir)) {
	$all = array (
		"files"  => array(),
		"dirs"   => array()
	);
	
	while (($element = readdir($dh)) == True) {
		if (filetype($curdir.$element) == "dir" && ($element != "." and $element != "..")) {
				array_push($all["dirs"], $element);
		}
		if (filetype($curdir.$element) == "file") {
			array_push($all["files"], $element);
		}
	}
	asort($all[dirs]);
	asort($all[files]);
	closedir($dh);
	
	if ($curdir != $dir) {
		?>
		<tr>
		<td><img src="folder.svg" alt="dir" onclick="go_back();" style="cursor: pointer;"></td>
		<td><p style="font-weight: bold; cursor: pointer;">..</p></td>
		<td></td>
		</tr>
	<?php
	}
	
	
	
	
	
	foreach ($all["dirs"] as $dr) {
		?>
		<tr>
		<td><img src="folder.svg" alt="dir" onclick="go_folder('<?php echo $dr; ?>')" style="cursor: pointer;"></td>
		<?php
			$owner = posix_getpwuid(fileowner($curdir.$dr));
			$randomid = rand(10000,99999);
		?>
		<td id="<?php echo 'elem'.$randomid; ?>" onclick="show_acl('<?php print $curdir.$dr; ?>','<?php echo 'elem'.$randomid; ?>');">
			<p style="cursor: pointer;"><?php echo $dr; ?></p>
		</td>
		<td id="<?php echo 'own'.$randomid; ?>" onclick="ch_own('<?php echo $owner["uid"];?>','<?php echo 'own'.$randomid; ?>','<?php print $curdir.$dr; ?>');" style="cursor: pointer;">
			<p><?php echo $owner["gecos"]; ?></p>
		</td>
		<td>
		<?php
			$perms= fileperms($curdir.$dr);
			clearstatcache();
			$readcolor = ($perms & 0x0100) ? "red":"grey";
			$writecolor = ($perms & 0x0080) ? "red":"grey";
		?>
			<span class="grp" style="color: <?php echo $readcolor; ?>;" onclick="toggle_perm('unix','own','read','<?php echo $curdir.$dr; ?>');">чтение</span>
			<span class="grp" style="color: <?php echo $writecolor; ?>;" onclick="toggle_perm('unix','own','write','<?php echo $curdir.$dr; ?>');">запись</span>
		</td>
		<td>
		<?php
			$readothercolor = ($perms & 0x0004) ? "red":"grey";
			$writeothercolor = ($perms & 0x0002) ? "red":"grey";
		?>
			<span class="grp" style="color: <?php echo $readothercolor; ?>;" onclick="toggle_perm('unix','other','read','<?php echo $curdir.$dr; ?>');">чтение</span>
			<span class="grp" style="color: <?php echo $writeothercolor; ?>;" onclick="toggle_perm('unix','other','write','<?php echo $curdir.$dr; ?>');">запись</span>		
		</td>
		</tr>
		<?php
	}
	
	
	
	
	foreach ($all["files"] as $file) {
		?>
		<tr>
		<td></td>
		<?php
			$owner = posix_getpwuid(fileowner($curdir.$file));
			$randomid = rand(10000,99999);
		?>
		<td id="<?php echo 'elem'.$randomid; ?>" onclick="show_acl('<?php print $curdir.$file; ?>','<?php echo 'elem'.$randomid; ?>');">
			<p style="cursor: pointer;"><?php echo $file; ?></p>
		</td>
		<td id="<?php echo 'own'.$randomid; ?>" onclick="ch_own('<?php echo $owner["uid"];?>','<?php echo 'own'.$randomid; ?>','<?php print $curdir.$file; ?>');" style="cursor: pointer;">
			<p>
		<?php
			echo $owner["gecos"];
		?>
		</p></td>
		<td>
		<?php
			$perms= fileperms($curdir.$file);
			clearstatcache();
			$readcolor = ($perms & 0x0100) ? "red":"grey";
			$writecolor = ($perms & 0x0080) ? "red":"grey";
		?>
			<span class="grp" style="color: <?php echo $readcolor; ?>;" onclick="toggle_perm('unix','own','read','<?php echo $curdir.$file; ?>');">чтение</span>
			<span class="grp" style="color: <?php echo $writecolor; ?>;" onclick="toggle_perm('unix','own','write','<?php echo $curdir.$file; ?>');">запись</span>
		</td>
		<td>
		<?php
			$readothercolor = ($perms & 0x0004) ? "red":"grey";
			$writeothercolor = ($perms & 0x0002) ? "red":"grey";
		?>
			<span class="grp" style="color: <?php echo $readothercolor; ?>;" onclick="toggle_perm('unix','other','read','<?php echo $curdir.$file; ?>');">чтение</span>
			<span class="grp" style="color: <?php echo $writeothercolor; ?>;" onclick="toggle_perm('unix','other','write','<?php echo $curdir.$file; ?>');">запись</span>		
		</td>
		</tr>
		<?php
		}
		?>
		<tr class="noborder">
			<td colspan="5"><br><p><input type="text" id="catalog"><input type="button" value="Создать каталог" style="float: right;" onclick="mdir('<?php
				print $curdir;
			?>');"></p></td>
		</tr>
	</tbody>
</table>

<?php
}
?>
