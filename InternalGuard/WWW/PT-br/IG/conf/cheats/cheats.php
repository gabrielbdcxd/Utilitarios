<?php
#Neste arquivo vocъ poderс configurar as permissѕes de Softwares de terceiros em seu servidor.



#Softwares liberados:
#Bloquear todos sem exceчуo = _Block_all
#Exemplo adicionando exceчуo. Ex: $Softwares_liberados='WGSF01,WTTAB,SPALSD02';
#WGSF01,WTTAB,SPALSD02 sуo identificadores dos programas. Os identificadores podem ser obtidos quando o IG detecta um programa ilegal.
$Softwares_liberados='_Block_all';

# Banir jogadores?
# Banir na primeira detecчуo = 1
# Banir na segunda detecчуo = 2
# Nunca banir, apenas fechar o jogo = 3
$BANIR_JOGADOR="3";

echo "$BANIR_JOGADOR|$Softwares_liberados";

?>