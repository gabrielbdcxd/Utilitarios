<?php
#Neste arquivo voc� poder� configurar as permiss�es de Softwares de terceiros em seu servidor.



#Softwares liberados:
#Bloquear todos sem exce��o = _Block_all
#Exemplo adicionando exce��o. Ex: $Softwares_liberados='WGSF01,WTTAB,SPALSD02';
#WGSF01,WTTAB,SPALSD02 s�o identificadores dos programas. Os identificadores podem ser obtidos quando o IG detecta um programa ilegal.
$Softwares_liberados='_Block_all';

# Banir jogadores?
# Banir na primeira detec��o = 1
# Banir na segunda detec��o = 2
# Nunca banir, apenas fechar o jogo = 3
$BANIR_JOGADOR="3";

echo "$BANIR_JOGADOR|$Softwares_liberados";

?>