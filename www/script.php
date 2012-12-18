<?php

$genJson = "/home/scidb/genJson.sh";

$dim  = htmlspecialchars($_GET["dim"]);
$array = "";

switch( $dim )
{
    case 3:
        $array = B;  
        break;
    case 4:
        $array = C;  
        break;
    case 5:
        $array = D;  
        break;
}

$bash_command= "bash ".$genJson." ".$dim." ".$array;
system($bash_command, $retval);
