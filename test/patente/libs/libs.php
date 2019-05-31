<?php

//Open DB CS
function OpenDB_CS ()
{
	if (!$conn = pg_connect("dbname=sharengo host=185.58.119.117 user=cs password=gmjk51pa"))
	{
		echo "Can't connect to database";
    	exit(1);
	}
	return $conn;
};

//Open DB CS RedMine
function OpenDB_RM ()
{
	if (!$conn = pg_connect("dbname=redmine host=dev.omniaevo.it user=redmine password="))
	{
		echo "Can't connect to database";
    	exit(1);
	}
	return $conn;
};

//Set User
function user() {
$user = $_SERVER['PHP_AUTH_USER'];
return $user;
};

//Set Password
function password() {
$password = $_SERVER['PHP_AUTH_PW'];
return $password;
};

//Clear session

?>