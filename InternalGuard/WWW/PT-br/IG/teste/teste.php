<?php
############################################################
$HOST = "127.0.0.1";
$USER = "root";
$PASSWORD = "vertrigo";
$DBNAME = "bra_internalguard";
############################################################








@$conexao = mysql_pconnect("$HOST","$USER","$PASSWORD");

if ($conexao)  // SE a conexao ao banco de dados foi efetuada com sucesso ENTAO...
{
echo "<CENTER><B>Conex�o MySQL:<BR><FONT COLOR=blue>Consegui me conectar ao MySQL com o usu�rio ". $USER .". Parab�ns!</B></FONT></CENTER><P>";  // exibe esta mensagem no navegador web
 }
else // SENAO...
{
echo "<CENTER><B>Conex�o MySQL:<BR><FONT COLOR=red>Erro! N�o pude me conectar ao servidor MySQL.<BR>Por favor, cheque se o mesmo est� rodando no servidor.</FONT></B></CENTER><P>";  // exibe esta mensagem no navegador web
}


# Selecionando o banco de dados...
@$selecao = mysql_select_db("$DBNAME");
 
if ($selecao)  // SE a selecao ao banco de dados foi efetuada com sucesso ENTAO...
  {
    echo "<CENTER><B>Selecionando o banco de dados::<BR><FONT COLOR=blue>Consegui selecionar o banco de dados chamado ". $DBNAME ." com sucesso. Parab�ns!</B></FONT></CENTER>";  // exibe esta mensagem no navegador web
  }
else // SENAO...
  {
    echo "<CENTER><B>Selecionando o banco de dados::<BR><FONT COLOR=red>Erro! N�o pude selecionar o banco de dados chamado ". $DBNAME .".<BR>Por favor, cheque se este banco de dados existe no servidor MySQL.</FONT></B></CENTER><BR>";  // exibe esta mensagem no navegador web
  }
 
echo "<CENTER><B>Testando permiss�es:<BR><BR>";

if ( substr(sprintf('%o', fileperms('../class/tmp-ips')), -4) == '777' )
{
Echo "<CENTER><B>Permiss�o:<BR><FONT COLOR=blue>Permiss�o [OK]</B></FONT></CENTER><BR><BR>";
}
else
{echo "<CENTER><B>Permiss�o:<BR><FONT COLOR=blue>Erro:Sem permiss�o de escrita em IG/class/>tmp-ips<  !</B></FONT></CENTER><BR><BR>";}

echo "<CENTER><B>Testes completos!<BR>";
?>