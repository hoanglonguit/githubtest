<?php
require_once('Config.php');
require_once ('Model/UserCrawler.php');

$crawler = new UserCrawler();
//$crawler->crawlUserBasicInfo('singapore');
$crawler->crawlUserDetail();
?>