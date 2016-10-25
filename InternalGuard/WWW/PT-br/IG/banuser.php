<?php
error_reporting(0);
require_once('class/sec.php');
require_once('conf/conexao.php');
$uid = mysql_escape_string($_REQUEST['h']);
$motivo = mysql_escape_string($_REQUEST['m']);
$playerip = mysql_escape_string($_SERVER['REMOTE_ADDR']);

if (strlen($uid) === 0)
Exit();
if (strlen($motivo) === 0)
Exit();

mysql_connect($HOST, $USER, $PASSWORD);
mysql_select_db($DBNAME);


$sql = "SELECT *
FROM `banidos`
WHERE `UniqueID` = '$uid'
LIMIT 0 , 1";

$resultado = mysql_query($sql) or die(mysql_error());
$numero = mysql_num_rows($resultado ); 
echo $numero;
if( $numero == 0) {
MYSQL_QUERY("INSERT INTO `$DBNAME`.`banidos` (
`id` ,
`UniqueID` ,
`IP` ,
`Data` ,
`Hora` ,
`Motivo`
)
VALUES (
NULL , '$uid', '$playerip', CURRENT_DATE( ) , CURRENT_TIME( ) , '$motivo'
)") or die (mysql_error());
}




?>