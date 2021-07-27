<?php

require_once "Poker.php";
require_once "Card.php";

class Rank
{
    /**
     * @param $fileName
     * gets the name of the file that has poker hands
     * orders them and outputs the result
     */
    public static function RankPokerHands($fileName)
    {
        //open the file
        //read line by line
        //order them
        //output the ordered hands

        if (file_exists($fileName)) {
            //echo "The file $fileName exists".PHP_EOL;
        } else {
            echo "The file $fileName does not exist".PHP_EOL;
            die();
        }

        chmod($fileName, 0777);
        $handle = fopen($fileName, 'r') or die("can't open file");

        $arrayToOrder = [];

        if ($handle) {

            //for each line in input file
            while (($line = fgets($handle)) !== false) {
                $handArray = explode(" ", $line);

                $cards = array();

                //create a card array resembles a poker hand
                foreach ($handArray as $item) {

                    $card = new Card($item);

                    $cards[] = $card;
                }

                //calculate the value of hand
                $poker = new Poker($cards);
                $value = $poker->valueHand();

                //add the value and the hand to an array
                $arrayToOrder[$line] = $value;
            }

            fclose($handle);
        }

        //sort the hand array by the hand value and output
        arsort($arrayToOrder);
        foreach ($arrayToOrder as $x => $x_value) {
            echo trim($x, "\n");
            echo PHP_EOL;
        }


    }


}