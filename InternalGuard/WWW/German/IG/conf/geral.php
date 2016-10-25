<?php
# Konfiguration
# Internal Guard Version
$VERSAO="3.8";
$DB_VERSAO="73.0.0.0";

# Nachrichten, die in der Systeminformationsablage (Systemtray) angezeigt wird
$MENSAGEM1="Internal Guard 3.7 - Hacking Prevent System";
$MENSAGEM2="Your first message here";
$MENSAGEM3="Your second message here";

# Systemablage Symbol anzeigen?
# 0 = Deaktivieren
# 1 = Aktivieren
$SYSTEMTRYMSG='1';

# Maximum number of windows allowed per PC?
$MAX_CLIENT_PER_PC="2";


# Pakete debuggen
$Debug="0";

# The kernel mode protection is an additional layer that protects your game against memory issues, no-delay and other cheats.
# Third-party software will not have access to the game process. (This includes RCX)
# Enable kernel mode protection (ring0)?
# 1 = Yes
# 0 = No.
# Note: If this option is enabled, tools such as RCX crashed on its server.
# Note: Function experimental only works on x86 architecture. X64 function will be disabled automatically.
$Kernelmode_protection="1";

# Porta do login
$LOGIN_PORT="6900";

echo "$VERSAO|$MENSAGEM1|$MENSAGEM2|$MENSAGEM3|$DB_VERSAO|$Debug|$MAX_CLIENT_PER_PC|$SYSTEMTRYMSG|$Kernelmode_protection|$LOGIN_PORT|";



?>