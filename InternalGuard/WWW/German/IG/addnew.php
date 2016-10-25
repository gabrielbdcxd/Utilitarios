<?php
error_reporting(0);

include('class/sec.php');
require_once('conf/conexao.php');
$uid = mysql_escape_string($_REQUEST['h']);
$playerip = mysql_escape_string($_SERVER['REMOTE_ADDR']);

if (strlen($uid) === 0)
Exit();

mysql_connect($HOST, $USER, $PASSWORD);
mysql_select_db($DBNAME);


$sql = "SELECT *
FROM `jogadoresid`
WHERE `UniqueID` = '$uid'
LIMIT 0 , 1";

$resultado = mysql_query($sql) or die(mysql_error());
$numero = mysql_num_rows($resultado ); 

if( $numero > 0) {
MYSQL_QUERY("UPDATE `$DBNAME`.`jogadoresid` SET `IP` = '$playerip', `Data` = CURRENT_DATE(), `Hora` = CURRENT_TIME() WHERE `jogadoresid`.`UniqueID` = '$uid'");
}
else
{
MYSQL_QUERY(" INSERT INTO `$DBNAME`.`jogadoresid` (
`id` ,
`UniqueID` ,
`IP` ,
`Data` ,
`Hora`
)
VALUES (
NULL , '$uid', '$playerip', CURRENT_DATE( ) , CURRENT_TIME( )
)");
}


//logs
$sql2 = "SELECT *
FROM `logs`
WHERE `IP` = '$playerip'
LIMIT 0 , 1";

$resultado2 = mysql_query($sql2) or die(mysql_error());
$numero2 = mysql_num_rows($resultado2 ); 

if( $numero2 > 0) {
MYSQL_QUERY("UPDATE `$DBNAME`.`logs` SET `IP` = '$playerip', `Data` = CURRENT_DATE(), `Hora` = CURRENT_TIME() WHERE `logs`.`IP` = '$playerip'");
}
else
{
MYSQL_QUERY("INSERT INTO `$DBNAME`.`logs` (
`id` ,
`UniqueID` ,
`IP` ,
`Data` ,
`Hora`
)
VALUES (
NULL , '$uid', '$playerip', CURRENT_DATE( ) , CURRENT_TIME( )
)");
}





?>