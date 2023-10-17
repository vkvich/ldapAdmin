<?php
global $err;
function testErr($stream) {
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	$err = stream_get_contents($errorStream);
	if ($err == "") {return true;}
	else {return false;}
}

$hand = @fsockopen ("109.202.0.14", "22222");
	if ($hand) {
		$connection = ssh2_connect("109.202.0.14", "22222");
                if (ssh2_auth_password($connection, "nsktime001", "123456")) {
			$stat = testErr(ssh2_exec($connection, "touch .need"));
		}
		if ($stat) { print "OK"; }
		else { print $err; }
	}

?>
