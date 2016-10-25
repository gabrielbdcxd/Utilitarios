<?php
error_reporting(0);
require_once('class/sec.php');
require_once('conf/conexao.php');
$uid = mysql_escape_string($_REQUEST['h']);
$msg = mysql_escape_string($_REQUEST['msg']);
$playerip = mysql_escape_string($_SERVER['REMOTE_ADDR']);

if (strlen($uid) === 0)
Exit();
if (strlen($msg) === 0)
Exit();

mysql_connect($HOST, $USER, $PASSWORD);
mysql_select_db($DBNAME);

MYSQL_QUERY("INSERT INTO `$DBNAME`.`ig_logs` (
`id` ,
`UniqueID` ,
`IP` ,
`InternalMessage` ,
`Data` ,
`Hora`
)
VALUES (
NULL , '$uid', '$playerip', '$msg', CURRENT_DATE( ) , CURRENT_TIME( )
)");


?>