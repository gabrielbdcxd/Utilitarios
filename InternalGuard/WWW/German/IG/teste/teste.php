<?php
############################################################
$HOST = "127.0.0.1";
$USER = "root";
$PASSWORD = "vertrigo";
$DBNAME = "ig";
############################################################

@$conexao = mysql_pconnect("$HOST","$USER","$PASSWORD");

if ($conexao)  // falls die verbindung zum Server erfolgreich war, dann ...
{
echo "<CENTER><B>MySQL connection :<BR><FONT COLOR=blue> hat sich mit dem Benutzernamen ". $USER .". mit MYSQL verbunden. Herzlichen glückwunsch!</B></FONT></CENTER><P>";  // Zeige diese Nachricht in der teste.php an!
 }
else // Andernfalls ...
{
echo "<CENTER><B>MySQL connection:<BR><FONT COLOR=red>Error: Der Verbindungsaufbau zu MYSQL ist fehlgeschlagen.<BR> Bitte überprüfe dein MYSQL Benutzernamen.</FONT></B></CENTER><P>";  // Zeige diese Nachricht in der teste.php an!
}


# Wähle die Datenbank aus ...
@$selecao = mysql_select_db("$DBNAME");
 
if ($selecao)  // Falls die verbindung zur Datenbank erfolgreicxh war ...
  {
    echo "<CENTER><B>Verbindung zur Datenbank::<BR><FONT COLOR=blue>war erfolgreich. Datenbankname: ". $DBNAME .". Herzlichen Glückwunsch!</B></FONT></CENTER>";  // Zeige diese Nachricht in der teste.php an!
  }
else // Andernfalls ...
  {
    echo "<CENTER><B>Verbindung zur Datenbank::<BR><FONT COLOR=red>Fehlgeschlagen! Es konnte keine Verbindung zu der Datenbank ". $DBNAME .".<BR> aufgebaut werden. Bitte überprüfe deine Einstellungen.</FONT></B></CENTER><BR>";  // Zeige diese Nachricht in der teste.php an!
  }
 
echo "<CENTER><B>Teste Berechtigungen:<BR><BR>";

if ( substr(sprintf('%o', fileperms('../class/tmp-ips')), -4) == '777' )
{
Echo "<CENTER><B>Berechtigungen:<BR><FONT COLOR=blue>Berechtigung [OK]</B></FONT></CENTER><BR><BR>";
}
else
{echo "<CENTER><B>Berechtigungen:<BR><FONT COLOR=blue>Fehlgeschlagen: Es wurde keine Berechtigungen deklairt IG / class /> tmp-ips  !</B></FONT></CENTER><BR><BR>";}

echo "<CENTER><B>Test erfolgreich!<BR>";
?>