<?php
# In dieser Datei k�nnen Sie Einstellungen �ber die Drittanbieter Programme treffen.
# Um alle Drittanbieter Programme zu blockieren, benutzen Sie den Eintrag = _Block_all
# Sie k�nnen ein Programm zu einer Whitelist hinzuf�gen. Beispiel: $ Softwares_liberados = 'WGSF01, WTTAB, SPALSD02';
# WGSF01, WTTAB, SPALSD02 sind Identifikationsmuster der Programme. Wurde ein Illegales Programm von Internal Guard identifiziert, wird das Identifikationsmuster vermerkt.
$Softwares_liberados='_Block_all';

# Spieler bannen?
# Bei dem ersten Entdecken der Software bannen ? = 1
# Beim zweiten Versuch bannen = 2
# Niemals bannen. Es wird nur das Spiel beendet = 3
$BANIR_JOGADOR="3";

echo "$BANIR_JOGADOR|$Softwares_liberados";

?>