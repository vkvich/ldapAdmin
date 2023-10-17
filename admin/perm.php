<?php

include ("options/options.php");

if ($_POST["mode"] == "unix") {
	$element = $_POST["elem"];
	$perms= fileperms("$element");
	
	$u = ($_POST["usr"] == "own") ? "u" : "o";
	$r = ($_POST["oper"] == "read") ? "r" : "w";
	
	$acl = "-m o:";
	
	if ($u == "u" and $r == "r") { $o = ($perms & 0x0100) ? "-":"+"; }
	if ($u == "u" and $r == "w") { $o = ($perms & 0x0080) ? "-":"+"; }
	if ($u == "o" and $r == "r") { $o = ($perms & 0x0004) ? "-":"+"; }
	if ($u == "o" and $r == "w") { $o = ($perms & 0x0002) ? "-":"+"; }
	
	$str = $u.$o.$r;
	$str2 = "/usr/bin/setfacl -R ";
	$d = "-d ";
	$el = " \"$element\"";

	if ($str == "o-r") { $acl .= "--x"; }
	if ($str == "o+r") { $acl .= "r-x"; }
	if ($str == "o-w") { $acl .= "r-x"; }
	if ($str == "o+w") { $acl .= "rwx"; }

	$connection = ssh2_connect($ser_file, 22);
	if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
                ssh2_exec($connection, $str2.$d.$acl.$el);
                ssh2_exec($connection, $str2.$acl.$el);
		ssh2_exec($connection, "/bin/chmod -R $str \"$element\"");
	}	
}

if ($_POST["mode"] == "own") {
	$element = $_POST["element"];
	$uid = $_POST["uid"];
	$gid = $_POST["gid"];
	$connection = ssh2_connect($ser_file, 22);
	if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
		ssh2_exec($connection, "/bin/chown -R $uid:$gid \"$element\"");
	}
}

if ($_POST["mode"] == "acl") {
	$element = $_POST["elem"];
	$connection = ssh2_connect($ser_file, 22);
	if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
		$list = ssh2_exec($connection, "/usr/bin/getfacl --numeric \"$element\"");
		stream_set_blocking($list, true);
		$list = stream_get_contents($list);
	}
	print $list;
}

if ($_POST["mode"] == "setacl") {
	
	$element = $_POST["elem"];
	$uid = $_POST["uid"];
	
	$str = "/usr/bin/setfacl -R ";
	$def = "-d ";
	if ($_POST['r'] == 0 && $_POST['w'] == 0) {
		$end = "-x u:$uid";
	}
	else {
		$end = "-m u:$uid:";
		if ($_POST['r'] == 1) {$end .= "r";}
		else {$end .= "-";}
		if ($_POST['w'] == 1) {$end .= "w";}
		else {$end .= "-";}
		$end .= "x";
	}
	$el = " \"$element\"";
	
	$connection = ssh2_connect($ser_file, 22);
	if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
		ssh2_exec($connection, $str.$def.$end.$el);
		ssh2_exec($connection, $str.$end.$el);
	}
}

if ($_POST["mode"] == "mkdir") {
	$connection = ssh2_connect($ser_file, 22);
	if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
		$comm = 'mkdir "'.$_POST['cat'].'"';
		ssh2_exec($connection, $comm);
	}
}


?>
