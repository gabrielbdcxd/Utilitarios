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
echo "<CENTER><B>Conexão MySQL:<BR><FONT COLOR=blue>Consegui me conectar ao MySQL com o usuário ". $USER .". Parabéns!</B></FONT></CENTER><P>";  // exibe esta mensagem no navegador web
 }
else // SENAO...
{
echo "<CENTER><B>Conexão MySQL:<BR><FONT COLOR=red>Erro! Não pude me conectar ao servidor MySQL.<BR>Por favor, cheque se o mesmo está rodando no servidor.</FONT></B></CENTER><P>";  // exibe esta mensagem no navegador web
}


# Selecionando o banco de dados...
@$selecao = mysql_select_db("$DBNAME");
 
if ($selecao)  // SE a selecao ao banco de dados foi efetuada com sucesso ENTAO...
  {
    echo "<CENTER><B>Selecionando o banco de dados::<BR><FONT COLOR=blue>Consegui selecionar o banco de dados chamado ". $DBNAME ." com sucesso. Parabéns!</B></FONT></CENTER>";  // exibe esta mensagem no navegador web
  }
else // SENAO...
  {
    echo "<CENTER><B>Selecionando o banco de dados::<BR><FONT COLOR=red>Erro! Não pude selecionar o banco de dados chamado ". $DBNAME .".<BR>Por favor, cheque se este banco de dados existe no servidor MySQL.</FONT></B></CENTER><BR>";  // exibe esta mensagem no navegador web
  }
 
echo "<CENTER><B>Testando permissões:<BR><BR>";

if ( substr(sprintf('%o', fileperms('../class/tmp-ips')), -4) == '777' )
{
Echo "<CENTER><B>Permissão:<BR><FONT COLOR=blue>Permissão [OK]</B></FONT></CENTER><BR><BR>";
}
else
{echo "<CENTER><B>Permissão:<BR><FONT COLOR=blue>Erro:Sem permissão de escrita em IG/class/>tmp-ips<  !</B></FONT></CENTER><BR><BR>";}

echo "<CENTER><B>Testes completos!<BR>";
?>