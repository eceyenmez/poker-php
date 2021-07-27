<?php
echo "please enter the file name: ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
$filename = trim($line);

require_once ("Rank.php");

Rank::RankPokerHands($filename);



