<?php
error_reporting(0);
require_once('class/sec.php');

require_once('conf/conexao.php');
$uid = mysql_escape_string($_REQUEST['h']);
$playerip = mysql_escape_string($_SERVER['REMOTE_ADDR']);

if (strlen($uid) === 0)
Exit();

mysql_connect($HOST, $USER, $PASSWORD);
mysql_select_db($DBNAME);


$sql = "SELECT *
FROM `banidos`
WHERE `UniqueID` = '$uid'
LIMIT 0 , 1";

$resultado = MYSQL_QUERY($sql) or die (mysql_error());
$n = mysql_num_rows($resultado);

if ( $n > 0  ){
while ($row = mysql_fetch_array($resultado)) {
echo "$row[IP]|$row[Data]|$row[Hora]|$row[Motivo]|";
}

}
else
{
echo '0';
}


?>