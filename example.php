<?php
require_once('silverpop.class.php');
$silverpop = new Silverpop("MySilverpopUser","MySilverpopPass","http://api4.silverpop.com/servlet/XMLAPI");

$arr = $silverpop->getJobStatus('12345678');

print_r($arr);

?>