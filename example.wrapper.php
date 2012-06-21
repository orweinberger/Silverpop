<?php
require_once('silverpop.wrapper.class.php');
$silverpop = new SilverpopWrapper("MySilverpopUser","MySilverpopPass","http://api4.silverpop.com/servlet/XMLAPI");
$vars['JOB_ID'] = '12345678';
$arr = $silverpop->APICall("GetJobStatus",$vars);

print_r($arr);
?>