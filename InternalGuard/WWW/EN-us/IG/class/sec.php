<?php
  // Place flood protection code at the top of the script you want to protect.
  // You can write protection code into separate file and include it in every
  // page of your site.

  // Sample protection code starts here...

  // Include the class definition module.

  require_once ( 'class.floodblocker.php' );

  // In the following line write the full path to temporary directory in which
  // you want to store flood counters. It is good idea to create such folder
  // somewhere outside your documents directory, to make it unaccessable from Web.
  // Don't forget that the directory must have permissions to write files in it.
  // IMPORTANT!
  // All files in this folder (except those that start with dot, e.g.'.htaccess')
  // will be deleted by FloodBlocker, so don't keep anything there.

  $flb = new FloodBlocker ( 'class/tmp-ips/' );

  // Create as many rules as you want...

  $flb->rules = array (
    10=>10,    // rule 1 - maximum 10 requests in 10 secs
    60=>30,    // rule 2 - maximum 30 requests in 60 secs
    300=>50,   // rule 3 - maximum 50 requests in 300 secs
    3600=>200  // rule 4 - maximum 200 requests in 3600 secs
  );

  // At last call CheckFlood(), it will return FALSE if flood detected on any
  // of specified rules.

  if ( ! $flb->CheckFlood ( ) )
    die ( 'Muitas solicitações! Por favor, tente mais tarde.' );

  // ... that's all. Enjoy!

?>