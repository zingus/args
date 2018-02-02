<?php
require_once 'args.wip.php';

$args=new args();
$args->debug=true;
$args->process();


/*
$args->parseCommandLine("./command.sh ok")

array (
  0 => './command.sh',
  1 => 'ok',
);
*/

/*?PRINT_R $args->parseCommandLine("./command.sh o\\k");
Array
(
    [0] => ./command.sh
    [1] => ok
)
*/

$argv=$args->parseWindowsCommandLine("command.exe ^ok");
var_export($argv);
