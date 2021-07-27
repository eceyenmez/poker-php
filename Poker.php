<?php
require "Card.php";

class Poker
{

    const STRAIGHT_FLUSH = 8000000;// + valueHighCard
    const FOUR_OF_A_KIND = 7000000;// + value of 4 card and value of single
    const FULL_HOUSE = 6000000;// + set card rank and pair rank
    const FLUSH = 5000000;// + value of the high cards
    const STRAIGHT = 4000000;// + value of the high cards
    const SET = 3000000;// + high card
    const TWO_PAIRS = 2000000;// + High2*14^4+ Low2*14^2 + card
    const ONE_PAIR = 1000000;// + high*14^2 + high2*14^1 + low

    private $handSortedByRank;
    private $handSortedBySuit;

    /**
     * Poker constructor
     */
    public function __construct($hand)
    {
        $this->handSortedByRank = $this->setSortByRank($hand);
        $this->handSortedBySuit = $this->setSortBySuit($hand);
    }

    /**
     * @return float|int|object
     * calculates and returns the value of a hand
     */
    public function valueHand()
    {
        if ($this->isFlush() && $this->isStraight()) {
            //echo "straight flush:".PHP_EOL;
            return $this->valueStraightFlush();
        } else if (self::is4s()) {
            //echo "4 of a kind".PHP_EOL;
            return $this->valueFourOfAKind();
        } else if ($this->isFullHouse()) {
            //echo "full house".PHP_EOL;
            return $this->valueFullHouse();
        } else if ($this->isFlush()) {
            //echo "flush".PHP_EOL;
            return $this->valueFlush();
        } else if ($this->isStraight()) {
            //echo "straight second".PHP_EOL;
            return $this->valueStraight();
        } else if ($this->is3s()) {
            //echo "s3 of a kind".PHP_EOL;
            return $this->valueSet();
        } else if ($this->is2Pairs()) {
            //echo "2 pairs".PHP_EOL;
            return $this->valueTwoPairs();
        } else if ($this->is2s()) {
            //echo "pair".PHP_EOL;
            return $this->valueOnePair();
        } else {
            //echo "high card".PHP_EOL;
            return $this->valueHighCard();
        }

    }

    /**
     * @return float|int
     * returns the value of flush hand
     */
    public function valueStraightFlush()
    {
        return self::STRAIGHT_FLUSH + self::valueHighCard();
    }

    /**
     * @return float|int
     * calculates value of flush hand and returns
     */
    public function valueFlush()
    {
        return self::FLUSH + self::valueHighCard();
    }

       /**
     * @return float|int
     * calculates and returns the value of a straight hand
     */
    public function valueStraight()
    {
        return self::STRAIGHT + self::valueHighCard();
    }


    /**
     * @return int
     * calculates and returns the value of a 4 of a kind hand
     * 4 of a kind ex: QS QH QC QD 5D
     */
    public function valueFourOfAKind(): int
    {
        $hand = $this->handSortedByRank;

        return self::FOUR_OF_A_KIND + $hand[0]->getRank() + pow($hand[2]->getRank(),2)+ $hand[4]->getRank();
    }


    /**
     * @return float|int|object
     * calculates and returns the value of a full house hand
     */
    public function valueFullHouse()
    {
        $hand = $this->handSortedByRank;

        return self::FULL_HOUSE + $hand[1]->getRank() + pow($hand[2]->getRank(), 2) + $hand[3]->getRank();
    }


    /**
     * @return int
     */
    public function valueSet(): int
    {
        $hand = $this->handSortedByRank;

        $val = 0;

        if($hand[0]->getRank() == $hand[1]->getRank() && $hand[1]->getRank() == $hand[2]->getRank()){
            $val = self::SET + pow($hand[2]->getRank(),2)+$hand[3]->getRank()+$hand[4]->getRank();
        }else{
            $val = self::SET + pow($hand[2]->getRank(),2)+$hand[0]->getRank()+$hand[1]->getRank();
        }

        return $val;
    }

    /**
     * @return float|int
     * calculates and returns the value of a Two-Pairs hand
     * ex: a a b b c - a a c b b - c a a b b
     */
    public function valueTwoPairs()
    {
        $val = 0;

        $hand = $this->handSortedByRank;

        if ($hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[2]->getRank() == $hand[3]->getRank())
            $val = 14 * 14 * $hand[2]->getRank() + 14 * $hand[0]->getRank() + $hand[4]->getRank();
        else if ($hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[3]->getRank() == $hand[4]->getRank())
            $val = 14 * 14 * $hand[3]->getRank() + 14 * $hand[0]->getRank() + $hand[2]->getRank();
        else
            $val = 14 * 14 * $hand[3]->getRank() + 14 * $hand[1]->getRank() + $hand[0]->getRank();

        return self::TWO_PAIRS + $val;
    }


    /**
     * @return float|int
     * calculates and returns the value of one pair hand
     */
    public function valueOnePair()
    {
        $hand = $this->handSortedByRank;

        if ($hand[0]->getRank() == $hand[1]->getRank())
            $val = 14 * 14 * 14 * $hand[0]->getRank() +
                +$hand[2]->getRank() + 14 * $hand[3]->getRank() + 14 * 14 * $hand[4]->getRank();
        else if ($hand[1]->getRank() == $hand[2]->getRank())
            $val = 14 * 14 * 14 * $hand[1]->getRank() +
                +$hand[0]->getRank() + 14 * $hand[3]->getRank() + 14 * 14 * $hand[4]->getRank();
        else if ($hand[2]->getRank() == $hand[3]->getRank())
            $val = 14 * 14 * 14 * $hand[2]->getRank() +
                +$hand[0]->getRank() + 14 * $hand[1]->getRank() + 14 * 14 * $hand[4]->getRank();
        else
            $val = 14 * 14 * 14 * $hand[3]->getRank() +
                +$hand[0]->getRank() + 14 * $hand[1]->getRank() + 14 * 14 * $hand[2]->getRank();

        return self::ONE_PAIR + $val;
    }


    /**
     * @return float|int
     * returns the value of high card
     * formula:  value =  14^4*highestCard     + 14^3*2ndHighestCard
     *                    + 14^2*3rdHighestCard+ 14^1*4thHighestCard+ LowestCard
     */
    public function valueHighCard()
    {
        $hand = $this->handSortedByRank;

        $valueHighCard = $hand[0]->getRank() + 14 * $hand[1]->getRank() + 14 * 14 * $hand[2]->getRank()
            + 14 * 14 * 14 * $hand[3]->getRank() + 14 * 14 * 14 * 14 * $hand[4]->getRank();

        return $valueHighCard;
    }

    ///***********************************************************
    // * Methods used to determine a certain Poker hand
    // ***********************************************************/

    /**
     * @return bool
     * returns true after sorted by rank, if the first 4 or the last 4
     * cards have the same rank
     */
    private function is4s(): bool
    {
        $hand = $this->handSortedByRank;

        $sameRankInBeginning = $hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[1]->getRank() == $hand[2]->getRank() &&
            $hand[2]->getRank() == $hand[3]->getRank();

        $sameRankAtEnd = $hand[1]->getRank() == $hand[2]->getRank() &&
            $hand[2]->getRank() == $hand[3]->getRank() &&
            $hand[3]->getRank() == $hand[4]->getRank();

        return ($sameRankInBeginning || $sameRankAtEnd);
    }

    /**
     * @return bool
     * checks if full house and returns true
     * ex: QD QS KD KS KC
     */
    public function isFullHouse(): bool
    {
        $hand = $this->handSortedByRank;

        // checking if sorted like this --> a a a b b
        $pairAtTheEnd = $hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[1]->getRank() == $hand[2]->getRank() &&
            $hand[3]->getRank() == $hand[4]->getRank();

        // checking if sorted like this --> b b a a a
        $pairAtTheBeginning = $hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[2]->getRank() == $hand[3]->getRank() &&
            $hand[3]->getRank() == $hand[4]->getRank();

        return ($pairAtTheEnd || $pairAtTheBeginning);
    }

    /**
     * @return bool
     * returns true if the hand is 3 of a kind
     */
    public function is3s(): bool
    {
        $hand = $this->handSortedByRank;

        $sameRankAtBeginning = $hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[1]->getRank() == $hand[2]->getRank();

        $sameRankAtMiddle = $hand[1]->getRank() == $hand[2]->getRank() &&
            $hand[2]->getRank() == $hand[3]->getRank();

        $sameRankAtEnd = $hand[2]->getRank() == $hand[3]->getRank() &&
            $hand[3]->getRank() == $hand[4]->getRank();

        return ($sameRankAtBeginning || $sameRankAtMiddle || $sameRankAtEnd);

    }


    /**
     * @return bool
     * checks if hand has 2 pairs
     */
    public function is2Pairs(): bool
    {
        $hand = $this->handSortedByRank;

        $pairType1 = $hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[2]->getRank() == $hand[3]->getRank();

        $pairType2 = $hand[0]->getRank() == $hand[1]->getRank() &&
            $hand[3]->getRank() == $hand[4]->getRank();

        $pairType3 = $hand[1]->getRank() == $hand[2]->getRank() &&
            $hand[3]->getRank() == $hand[4]->getRank();

        return ($pairType1 || $pairType2 || $pairType3);
    }


    /**
     * @return bool
     * checks if hand has one pair
     * ex: a a b c d -- b a a c d -- b c a a d -- b c d a a
     */
    public function is2s(): bool
    {
        $hand = $this->handSortedByRank;

        $pair1 = $hand[0]->getRank() == $hand[1]->getRank();
        $pair2 = $hand[1]->getRank() == $hand[2]->getRank();
        $pair3 = $hand[2]->getRank() == $hand[3]->getRank();
        $pair4 = $hand[3]->getRank() == $hand[4]->getRank();

        return ($pair1 || $pair2 || $pair3 || $pair4);
    }


    /**
     * @return bool
     * returns true if all cards have the same suit-->flush
     */
    public function isFlush(): bool
    {
        $hand = $this->handSortedBySuit;

        //check if all cars have the same suit
        return ($hand[0]->getSuit() == $hand[4]->getSuit());
    }


    /**
     * @return bool
     * returns true if the hand is straight
     * ex: 1 2 3 4 5 ---- A K Q J 10
     */
    public function isStraight(): bool
    {
        $hand = $this->handSortedByRank;

        //check for an ace
        if ($hand[4]->getRank() == 14) {

            //check for strait with ace
            $smallAceStraight = $hand[0]->getRank() == 2 && $hand[1]->getRank() == 3 &&
                $hand[2]->getRank() == 4 && $hand[3]->getRank() == 5;
            $bigAceStraight = $hand[0]->getRank() == 10 && $hand[1]->getRank() == 11 &&
                $hand[2]->getRank() == 12 && $hand[3]->getRank() == 13;

            //if ace with straight return true
            if ($smallAceStraight || $bigAceStraight) {
                return true;
            } else {
                return false;
            }

        } else {
            //check for consecutive increasing values
            for ($i = 0; $i < 4; $i++) {   //check n and n+1 is same
                if ($hand[$i]->getRank() + 1 != $hand[$i + 1]->getRank())
                    return false;
            }
            return true;
        }
    }


    /**
     * @param $cards
     * @return mixed
     * sorts cards by suits from min to max with selection sort
     */
    public function setSortBySuit($cards)
    {

        for ($i = 0; $i < count($cards); $i++) {

            $low = $i;
            for ($j = $i + 1; $j < count($cards); $j++) {
                // check current element is smaller than min
                if ($cards[$j]->getSuit() < $cards[$low]->getSuit()) {
                    $low = $j;
                }
            }

            //swap
            $tempCard = $cards[$i];
            $cards[$i] = $cards[$low];
            $cards[$low] = $tempCard;

        }

        return $cards;
    }

    /**
     * @param $cards
     * @return mixed
     * sorts cards by rank from min to max with selection sort
     */
    private static function setSortByRank($cards)
    {
        //selection sort
        for ($i = 0; $i < count($cards); $i++) {
            $low = $i;

            for ($j = $i + 1; $j < count($cards); $j++) {
                // check current element is smaller than min
                if ($cards[$j]->getRank() < $cards[$low]->getRank()) {
                    $low = $j;
                }
            }

            //swap
            $tempCard = $cards[$i];
            $cards[$i] = $cards[$low];
            $cards[$low] = $tempCard;
        }
        return $cards;

    }


}


