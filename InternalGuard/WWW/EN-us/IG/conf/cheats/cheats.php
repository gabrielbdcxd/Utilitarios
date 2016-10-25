<?php
# In this file you can configure permissions for third-party software on your server.
# Block all without exception = _Block_all
# Example adding exception to the Whitelist. Ex: $ Softwares_liberados = 'WGSF01, WTTAB, SPALSD02';
# WGSF01, WTTAB, SPALSD02 are identifiers of the programs. The identifiers can be obtained when the IG detects an illegal program.
$Softwares_liberados='_Block_all';

# Ban players?
# Ban after the first detection = 1
# Ban after the second detection = 2
# Never ban, just close the game = 3
$BANIR_JOGADOR="3";

echo "$BANIR_JOGADOR|$Softwares_liberados";

?>