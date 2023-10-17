<?php

include 'options/options.php';

?>
<p>&nbsp;</p>
<table id="comps" class="tablesorter">
	<thead>
		<tr style="background-color: #fff8af;">
		<th><p>№:</p></th><th><p>ПК:</p></th><th><p>Пользователь:</p></th><th><p>ОС:</p></th></tr>
	</thead>
	<tbody>

<?php
$link = mysqli_connect($ser_mysql,$mysqladmin,$mysqlpass);
$res = mysqli_select_db ($link,'comps');

$count_of_comps = 0;

$pcs = mysqli_query($link,"select hostname from comps;");
while ($p=mysqli_fetch_assoc($pcs)) {
	$pc = $p["hostname"];
	$result = mysqli_query($link,"select * from comps where hostname='$pc';");
	$result2 = mysqli_query($link,"select * from stats where hostname='$pc';");
	$result = mysqli_fetch_assoc($result);
	$result2 = mysqli_fetch_assoc($result2);
	
	$tm=time();
	$i = $tm - $result2["nowtime"];
	if ($i < 500) {
	$count_of_comps = $count_of_comps + 1;
	$cpu = $result["cpu_name"];
	$cpu_num = $result["cpu_num"];
		
	$mem_total = $result["mem_total"];
	$mem_total = $mem_total/1024;
	$mem_total = preg_replace('/^(\d*).*$/','$1',$mem_total);
		
	$video = $result["video"];
		
	$prn = $result2["prn"];
	$prn = explode(" ",$prn);
	$prn_count = count($prn);
		
		
	$upd = $result["upd_info"];
	$user = $result2["gecos"];
	$ubuntu_ver = $result["ubuntu_ver"];
	$cached = $result2["cached"];
		
	$uptime = $result2["uptime"];
	
		?>
		<tr>
			<td><p><?php print $count_of_comps; ?></p></td>
			<td><p><?php print $pc; ?></p></td>
			<td><p><?php print $user; ?></p></td>
			<td>
				<?php if($cpu_num) { ?>
				<div style="float: left;"><p>&nbsp;</p><p>Процессор: <?php print $cpu; ?></p><p>Ядер: <?php print $cpu_num; ?></p><p>Память: <?php print $mem_total; ?></p><p>Видео: <?php print $video; ?></p><p>&nbsp;</p>
				<p>OC: <?php print $ubuntu_ver; ?></p><p>Время работы: <?php print $uptime; ?></p><p>Обновления: <?php print $upd; ?></p><p>&nbsp;</p>
				</div>
				
				<?php } ?>
			</td>
		</tr>
		<?php
	}
}
mysqli_close($link);
?>
	</tbody>
</table>
<?php
function comm($command) {
	global $connection;
	$stream = ssh2_exec($connection, "$command");
	stream_set_blocking($stream, true);
	$result = stream_get_contents($stream);
	return $result;
}
?>

