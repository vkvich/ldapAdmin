<?php

include 'options/options.php';

if(isset($_POST["lastlogon"])) {
	$link = mysqli_connect($ser_mysql,$mysqladmin,$mysqlpass);
	$res = mysqli_select_db ($link,'comps');

	$userid = mysqli_real_escape_string($link,$_POST["userid"]);
	$username = mysqli_real_escape_string($link,$_POST["username"]);
	$lastlogon = mysqli_real_escape_string($link,$_POST["lastlogon"]);
	$curpc = mysqli_real_escape_string($link,$_POST["curpc"]);
	
	$ask = mysqli_query($link,"select * from lastlogon where id='$userid';");
	
	if(mysqli_num_rows($ask) == 0) {
		$result = mysqli_query($link,"insert into lastlogon (id,account,time,pc) values ('$userid','$username','$lastlogon','$curpc');");
	}
	else {
		$result = mysqli_query ($link,"update lastlogon set account='$username',time='$lastlogon',pc='$curpc' where id='$userid';");
	}

	mysqli_close($link);
}

if(isset($_POST["cpu_num"])) {
	$link = mysqli_connect($ser_mysql,$mysqladmin,$mysqlpass);
	$res = mysqli_select_db ($link,'comps');

	$hostname = mysqli_real_escape_string($link,$_POST["hostname"]);
	$cpu_num = mysqli_real_escape_string($link,$_POST["cpu_num"]);
	$cpu_name = mysqli_real_escape_string($link,$_POST["cpu_name"]);
	$mem_total = mysqli_real_escape_string($link,$_POST["mem_total"]);
	$video = mysqli_real_escape_string($link,$_POST["video"]);
	$ubuntu_ver = mysqli_real_escape_string($link,$_POST["ubuntu_ver"]);
	$upd_info = mysqli_real_escape_string($link,$_POST["upd_info"]);
	
	$ask = mysqli_query($link,"select * from comps where hostname='$hostname';");
	
	if(mysqli_num_rows($ask) == 0) {
		$result = mysqli_query($link,"insert into comps (hostname,cpu_num,cpu_name,mem_total,video,ubuntu_ver,upd_info) values ('$hostname','$cpu_num','$cpu_name','$mem_total','$video','$ubuntu_ver','$upd_info');");
	}
	else {
		$result = mysqli_query ($link,"update comps set cpu_num='$cpu_num',cpu_name='$cpu_name',mem_total='$mem_total',video='$video',ubuntu_ver='$ubuntu_ver',upd_info='$upd_info' where hostname='$hostname';");
	}

	mysqli_close($link);
}

if(isset($_POST["user"])) {
	$link = mysqli_connect($ser_mysql,$mysqladmin,$mysqlpass);
	$res = mysqli_select_db ($link,'comps');
	
	$hostname = mysqli_real_escape_string($link,$_POST["hostname"]);
	$ipaddr = mysqli_real_escape_string($link,$_POST["ipaddr"]);
	$user = mysqli_real_escape_string($link,$_POST["user"]);
	$cached = mysqli_real_escape_string($link,$_POST["cached"]);
	$prn = mysqli_real_escape_string($link,$_POST["prn"]);
	$uptime = mysqli_real_escape_string($link,$_POST["uptime"]);
	$nowtime = mysqli_real_escape_string($link,$_POST["nowtime"]);
	
	$ld = ldap_connect ($ser_ldap);
	$ldap_res = ldap_search ($ld,$userbase,"uid=$user");
	$ent = ldap_get_entries($ld, $ldap_res);
	ldap_close($ld);
	$gecos = $ent[0]["displayname"][0] == "" ? $ent[0]["uid"][0] : $ent[0]["displayname"][0];

#	$prn = explode(" ",$prn);
			
	$ask = mysqli_query($link,"select * from stats where hostname='$hostname';");
	
	if(mysqli_num_rows($ask) == 0) {
		$result = mysqli_query($link,"insert into stats (hostname,ipaddr,user,gecos,cached,prn,uptime,nowtime) values ('$hostname','$ipaddr','$user','$gecos','$cached','$prn','$uptime','$nowtime');");
	}
	else {
		$result = mysqli_query ($link,"update stats set ipaddr='$ipaddr',user='$user',gecos='$gecos',cached='$cached',prn='$prn',uptime='$uptime',nowtime='$nowtime' where hostname='$hostname';");
	}
	
	mysqli_close($link);
}
	
?>

