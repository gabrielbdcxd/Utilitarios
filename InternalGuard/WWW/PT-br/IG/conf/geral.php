<?php
# Configura��o
# Vers�o do IG
$VERSAO="3.8";
$DB_VERSAO="73.0.0.0";

# Mensagem que � exibida perto do rel�gio / System tray message
$MENSAGEM1="Internal Guard 3.8";
$MENSAGEM2="";
$MENSAGEM3="";

# Ativar systemtry mensagem?
# 0 = Desativar
# 1 = Ativar
$SYSTEMTRYMSG='0';

# N�mero m�ximo de janelas liberadas por pc?
$MAX_CLIENT_PER_PC="2";


# Depura��o de pacotes
$Debug="0";

# A prote��o kernel mode � uma camada adicional que protege o seu jogo contra edi��es de m�moria, no-delay e outros cheats.
# Softwares de terceiros n�o v�o ter acesso ao processo do jogo. ( Isso inclu� RCX )
# Habilitar a prote��o Kernel mode ( ring0 )?
# 1 = Sim.
# 0 = N�o.
# Nota: Se esta op��o estiver ativa, ferramentas como RCX deixaram de funcionar em seu servidor.
# Nota: Fun��o experimental, funciona apenas na arquitetura x86. Em x64 a fun��o ser� desabilitada autom�ticamente.
$Kernelmode_protection="1";

# Porta do login
$LOGIN_PORT="6900";


echo "$VERSAO|$MENSAGEM1|$MENSAGEM2|$MENSAGEM3|$DB_VERSAO|$Debug|$MAX_CLIENT_PER_PC|$SYSTEMTRYMSG|$Kernelmode_protection|$LOGIN_PORT|";



?>