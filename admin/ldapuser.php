<?php

include 'options/options.php';

define ('UPCASE', 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯABCDEFGHIKLMNOPQRSTUVWXYZ');
define ('LOCASE', 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяabcdefghiklmnopqrstuvwxyz');

#mb_internal_encoding('UTF-8');
setlocale(LC_ALL,"ru_RU.UTF-8");

function mb_str_split($str) {        
    preg_match_all('/.{1}|[^\x00]{1}$/us', $str, $ar);
    return $ar[0];
}
function mb_strtr($str, $from, $to) {return str_replace(mb_str_split($from), mb_str_split($to), $str);}
function lowercase($arg=''){return mb_strtr($arg, UPCASE, LOCASE);}
function uppercase($arg=''){return mb_strtr($arg, LOCASE, UPCASE);}


if (isset($_POST['user']) && isset($_POST['gecos']) && isset($_POST['pass'])) {
	$numm = '';
	if ($dsks == 3) {
		$numm = preg_match("/^[a-i]/iu",$_POST['user']) ? '' : ((preg_match("/^[j-r]/iu",$_POST['user'])) ? '2' : '3');
	}
	if ($dsks == 2) {
		$numm = preg_match("/^[a-n]/iu",$_POST['user']) ? '' : '2';
	}
	$dnumm = $numm;
	$user = $_POST['user'];
	$fam = $_POST['fam'];
	$nam = $_POST['nam'];
	$gecos = $_POST['gecos'];
	$pass = $_POST['pass'];
	$tel = $_POST['tel'];
	$telmob = $_POST['telmob'];
	$mail = $_POST['mail'];

	$newuid = getfirst("user");
#	$newgid = getfirst("group");
	
	$valu['uid'] = $user;
	$valu['cn'] = $user;
	$valu['sn'] = $fam;
	if($nam) { $valu['givenName'] = $nam; }
	$valu['gecos'] = $gecos;
	$valu['displayName'] = $gecos;
	$valu['objectClass'][0] = "inetOrgPerson";
	$valu['objectClass'][1] = "posixAccount";
	$valu['objectClass'][2] = "shadowAccount";
	$valu['userPassword'] = $pass;
	$valu['uidNumber'] = $newuid; 
	$valu['gidNumber'] = $newuid;
	$valu['homeDirectory'] = "$homemount$dnumm/$user";
	$valu['loginShell'] = '/bin/bash';
	if ($tel) {$valu['telephoneNumber'] = $tel;}
	if ($telmob) {$valu['mobile'] = $telmob;}
	if ($mail) { $valu['mail'] = $mail;}
	
	$grp_name = $user;
	
	$valg['cn'] = $grp_name;
	$valg['objectClass'][0] = "top";
	$valg['objectClass'][1] = "posixGroup";
	$valg['gidNumber'] = $newuid;	
	
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ld) {$ldapbind = ldap_bind($ld, $ldapadmin, $ldappass);}
	$newdnu = "uid=$user,$userbase";
	$usad = ldap_add ($ld,$newdnu,$valu);
	if($usad) {
		$newdng = "cn=$grp_name,$groupbase";
		$grad = ldap_add ($ld,$newdng,$valg);
	}
	
	if($usad && $grad) {
		$hand = @fsockopen ($ser_profile, 22);
		if ($hand) {
			$connection = ssh2_connect($ser_profile, 22);
			if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
				ssh2_exec($connection, "shopt -s dotglob");
				$ans1 = testErr(ssh2_exec($connection, "mkdir /data$numm/profiles/$user"));
				$ans2 = testErr(ssh2_exec($connection, "chown -R $user:$grp_name /data$numm/profiles/$user"));
				$ans3 = testErr(ssh2_exec($connection, "chmod -R o-rwx /data$numm/profiles/$user"));
			}
		}
		$hand = @fsockopen ($ser_file, 22);
		if ($hand) {
			$connection = ssh2_connect($ser_file, 22);
			if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
				ssh2_exec($connection, "shopt -s dotglob");
				$ans4 = testErr(ssh2_exec($connection, "satsamba add $user $pass"));
			}
		}
	}
	
	
	ldap_close($ld);
	
	echo json_encode (array(
		"us"=>$user,
		"gr"=>$grp_name,
		"ldapus"=>$usad,
		"ldapgr"=>$grad,
		"skel"=>$ans1,
		"own"=>$ans2,
		"mod"=>$ans3,
		"samba"=>$ans4,
		"stat"=>"ok"));
	
}


else if (isset($_POST['uidnumber'])) {
	
	$uidnum = $_POST['uidnumber'];
	
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ld) {$ldapbind = ldap_bind($ld, $ldapadmin, $ldappass);}
	$ldap_res = ldap_search ($ld,$userbase,"uidnumber=$uidnum");
	$ent = ldap_get_entries($ld, $ldap_res);
	$valu = $ent[0]['dn'];
	$user = $ent[0]['uid'][0];
	$gid = $ent[0]['gidnumber'][0];

	$numm = '';
	if ($dsks == 3) {
		$numm = preg_match("/^[a-i]/iu",$user) ? '' : ((preg_match("/^[j-r]/iu",$user)) ? '2' : '3');
	}
	if ($dsks == 2) {
		$numm = preg_match("/^[a-n]/iu",$user) ? '' : '2';
	}
	$dnumm = $numm;

	$ldap_res = ldap_search ($ld,$groupbase,"gidnumber=$gid");
	$ent = ldap_get_entries($ld, $ldap_res);
	$valg = $ent[0]['dn'];

	$usad = ldap_delete($ld,$valu);
	$grad = ldap_delete($ld,$valg);		
	
#	$u = lowercase($user);\
	$u = $gid;
	$ldap_res = ldap_search ($ld,$groupbase,"memberUid=$u");
	$ent = ldap_get_entries($ld, $ldap_res);
	if($ent["count"] > 0) {
		foreach ($ent as $val) {
			$gg=$val['cn'][0];
			ldap_mod_del($ld,"cn=$gg,$groupbase",array("memberUid" => "$u"));
		}
	}		
	
	$hand = @fsockopen ($ser_profile, 22);
	if ($hand) {
		$connection = ssh2_connect($ser_profile, 22);
		if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
			$ans = testErr(ssh2_exec($connection, "mv /data$numm/profiles/$user /data$numm/profiles/deleted_$user"));
		}
	}
	$hand = @fsockopen ($ser_file, 22);
	if ($hand) {
		$connection = ssh2_connect($ser_file, 22);
		if (ssh2_auth_password($connection, $sshadmin, $sshpass)) {
			$ans2 = testErr(ssh2_exec($connection, "satsamba del $user"));
			$ans3 = testErr(ssh2_exec($connection, "/usr/bin/setfacl -R -d -x u:$uidnum $sharemount"));
			$ans4 = testErr(ssh2_exec($connection, "/usr/bin/setfacl -R -x u:$uidnum $sharemount"));
		}
	}		
	
	echo json_encode (array(
		"us"=>$user,
		"ldapus"=>$usad,
		"ldapgr"=>$grad,
		"backup"=>$ans,
		"samba"=>$ans2,
		"acldef"=>$ans3,
		"acl"=>$ans4,
		"stat"=>"ok"));
}

else if (isset($_POST['uidnumpass']) && isset($_POST['passw'])) {
	
	$uidnum = $_POST['uidnumpass'];
	$pas = $_POST['passw'];
	
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ld) {$ldapbind = ldap_bind($ld, $ldapadmin, $ldappass);}
	$ldap_res = ldap_search ($ld,$userbase,"uidnumber=$uidnum");
	$ent = ldap_get_entries($ld, $ldap_res);
	$valu = $ent[0]['dn'];
	
	$res = ldap_mod_replace($ld,$valu,array("userPassword"=>$pas));
	
	echo json_encode (array(
		"stat"=>"ok",
		"res"=>$res));
}
	
else if (isset($_POST['uidnumgec']) && isset($_POST['gec'])) {
	
	$uidnum = $_POST['uidnumgec'];
	$gec = $_POST['gec'];
	$tel = $_POST['tel'];
	$telmob = $_POST['telmob'];
	$mail = $_POST['mail'];
	
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ld) {$ldapbind = ldap_bind($ld, $ldapadmin, $ldappass);}
	$ldap_res = ldap_search ($ld,$userbase,"uidnumber=$uidnum");
	$ent = ldap_get_entries($ld, $ldap_res);
	$valu = $ent[0]['dn'];
	
	$ch["gecos"] = $gec;
	$ch["displayName"] = $gec;
	if($tel) {$ch["telephoneNumber"] = $tel;}
	if($telmob) {$ch["mobile"] = $telmob;}
	$ch["mail"] = $mail;
	
	$res = ldap_mod_replace($ld,$valu,$ch);

	echo json_encode (array(
		"stat"=>"ok",
		"res"=>$res));
}

else if (isset($_POST["testuser"])) {
	$user = $_POST["testuser"];
	$ld = ldap_connect ($ser_ldap);
	$ldap_res = ldap_search ($ld,$userbase,"uid=$user");
	$ent = ldap_get_entries($ld, $ldap_res);
	$ent_num = ldap_count_entries ($ld ,$ldap_res);
	ldap_close($ld);
	$res = ($ent_num == 0)?true:false;
	echo json_encode (array("res"=>$res));
}

else if (isset($_POST['tggl_grp'])) {
	
	$grp = $_POST['tggl_grp'];
	$name = $_POST['tggl_usr'];
	
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ld) {$ldapbind = ldap_bind($ld, $ldapadmin, $ldappass);}
	
#	$u = lowercase($name);
	$u = $name;
	$ldap_res = ldap_search ($ld,"cn=$grp,$groupbase","(memberUid=$u)");
	$ent = ldap_get_entries($ld, $ldap_res);
	if($ent["count"] > 0) {
		$res = ldap_mod_del($ld,"cn=$grp,$groupbase",array("memberUid" => "$u"));
	}
	else {
		$res = ldap_mod_add($ld,"cn=$grp,$groupbase",array("memberUid" => "$u"));
	}
	
	echo json_encode (array(
		"stat"=>"ok",
		"res"=>$res));
}

else if (isset($_POST['grp'])) {
	$grp = $_POST['grp'];
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	$ldap_res = ldap_search ($ld,"$groupbase","(cn=$grp)");
	$ent = ldap_get_entries($ld, $ldap_res);
	$res = 	($ent["count"] > 0) ? 1 : 0;
	echo json_encode (array(
		"res"=>$res));
}

else if (isset($_POST['gid'])) {
	$gid = $_POST['gid'];
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	$ldap_res = ldap_search ($ld,"$groupbase","(gidnumber=$gid)");
	$ent = ldap_get_entries($ld, $ldap_res);
	$res = 	($ent["count"] > 0) ? 1 : 0;
	echo json_encode (array(
		"res"=>$res));
}

else if (isset($_POST['group_name'])) {
	$group = $_POST['group_name'];
	$gid = $_POST['group_id'];
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ld) {$ldapbind = ldap_bind($ld, $ldapadmin, $ldappass);}
	
	$vg['cn'] = $group;
	$vg['gidNumber'] = $gid;
	$vg['objectClass'][0] = "top";
	$vg['objectClass'][1] = "posixGroup";
	
	$newdng = "cn=$group,$groupbase";
	$gradd = ldap_add($ld,$newdng,$vg);
	
	ldap_unbind($ld);
	
	echo json_encode (array(
		"stat"=>$gradd,
		"name"=>$group));
}

else if (isset($_POST['delgrpname'])) {
	$group = $_POST['delgrpname'];
	
	$ld = ldap_connect ($ser_ldap);
	ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ld) {$ldapbind = ldap_bind($ld, $ldapadmin, $ldappass);}
	
	$ldap_res = ldap_search ($ld,$groupbase,"cn=$group");
	$ent = ldap_get_entries($ld, $ldap_res);
	$g = $ent[0]['dn'];
	
	$gdel_stat = ldap_delete($ld,$g);
	ldap_unbind($ld);
	
	echo json_encode (array(
		"stat"=>$gdel_stat));
}

	
	function getfirst($bas) {
		global $ser_ldap;
		global $ser_profile;
		global $ser_file;
		global $ser_mysql;
		global $start_uid;
		global $start_gid;
		global $userbase;
		global $groupbase;
		if ($bas == "user") {$ind = "uidnumber"; $base = $userbase; $start = $start_uid;}
		else {$ind = "gidnumber"; $base = $groupbase; $start = $start_gid;}
		$ld = ldap_connect ($ser_ldap);
		$ldap_res = ldap_search ($ld,$base,"cn=*");
		$ent = ldap_get_entries($ld, $ldap_res);
		$i = 0;
		for($u = 0; $u < $ent["count"]; $u++) {
			$numb[$i++] = $ent[$u][$ind][0];
		}
		sort($numb);
		$prev = $start-1;
		foreach($numb as $val) {
			if($val >= $start) {
				if($val-$prev > 1) {
					break;
				}
				$prev = $val;
			}
		}
		return(++$prev);
	}
	
	function testErr($stream) {
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		$err = stream_get_contents($errorStream);
		if ($err == "") {return true;}
		else {return false;}
	}
?>
