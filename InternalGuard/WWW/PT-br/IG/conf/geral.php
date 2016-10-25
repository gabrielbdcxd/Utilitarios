<?php
# Configuração
# Versão do IG
$VERSAO="3.8";
$DB_VERSAO="73.0.0.0";

# Mensagem que é exibida perto do relégio / System tray message
$MENSAGEM1="Internal Guard 3.7 - Hacking Prevent System";
$MENSAGEM2="<Sua mensagem aqui 1>";
$MENSAGEM3="<Sua mensagem aqui 2>";

# Ativar systemtry mensagem?
# 0 = Desativar
# 1 = Ativar
$SYSTEMTRYMSG='1';

# Número máximo de janelas liberadas por pc?
$MAX_CLIENT_PER_PC="2";


# Depuração de pacotes
$Debug="0";

# A proteção kernel mode é uma camada adicional que protege o seu jogo contra edições de mémoria, no-delay e outros cheats.
# Softwares de terceiros não vão ter acesso ao processo do jogo. ( Isso incluí RCX )
# Habilitar a proteção Kernel mode ( ring0 )?
# 1 = Sim.
# 0 = Não.
# Nota: Se esta opção estiver ativa, ferramentas como RCX deixaram de funcionar em seu servidor.
# Nota: Função experimental, funciona apenas na arquitetura x86. Em x64 a função será desabilitada automáticamente.
$Kernelmode_protection="1";

# Porta do login
$LOGIN_PORT="6900";


echo "$VERSAO|$MENSAGEM1|$MENSAGEM2|$MENSAGEM3|$DB_VERSAO|$Debug|$MAX_CLIENT_PER_PC|$SYSTEMTRYMSG|$Kernelmode_protection|$LOGIN_PORT|";



?>