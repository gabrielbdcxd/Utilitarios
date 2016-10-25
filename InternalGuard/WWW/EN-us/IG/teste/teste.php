<?php
############################################################
$HOST = "127.0.0.1";
$USER = "root";
$PASSWORD = "vertrigo";
$DBNAME = "ig";
############################################################

@$conexao = mysql_pconnect("$HOST","$USER","$PASSWORD");

if ($conexao)  // if the connection to the database was made successfully THEN ...
{
echo "<CENTER><B>MySQL connection :<BR><FONT COLOR=blue>got connect to MySQL with the user ". $USER .". Congratulations!</B></FONT></CENTER><P>";  // Displays this message on the teste.php website
 }
else // Otherwise ...
{
echo "<CENTER><B>MySQL connection:<BR><FONT COLOR=red>Error Could not connect to MySQL server.<BR> Please check the MySQL User.</FONT></B></CENTER><P>";  // Displays this message on the teste.php website
}


# Selecting the database ...
@$selecao = mysql_select_db("$DBNAME");
 
if ($selecao)  // If the connection to the database was made successfully THEN ...
  {
    echo "<CENTER><B>Selecting the database::<BR><FONT COLOR=blue>was able to select the database named ". $DBNAME ." successfully Congratulations!</B></FONT></CENTER>";  // Displays this message on the teste.php website
  }
else // Otherwise ...
  {
    echo "<CENTER><B>Selecting the database::<BR><FONT COLOR=red>Error! Could not select database named ". $DBNAME .".<BR> Please check if this database exists in MySQL.</FONT></B></CENTER><BR>";  // Displays this message on the teste.php website
  }
 
echo "<CENTER><B>Testing permissions:<BR><BR>";

if ( substr(sprintf('%o', fileperms('../class/tmp-ips')), -4) == '777' )
{
Echo "<CENTER><B>Permission:<BR><FONT COLOR=blue>Permission [OK]</B></FONT></CENTER><BR><BR>";
}
else
{echo "<CENTER><B>Permission:<BR><FONT COLOR=blue>Error: No write permission in IG / class /> tmp-ips  !</B></FONT></CENTER><BR><BR>";}

echo "<CENTER><B>Test Complete!<BR>";
?>