<?php

class Card
{

    public $cardSuit;
    public $cardRank;

    private const SUITS = array("filler0", "♠", "♥", "♣", "♦");
    private const RANKS = array("filler0", "filler1", "2", "3", "4",
        "5", "6", "7", "8", "9", "10", "J", "Q", "K", "A");

    public function __construct($card)
    {
        $card = trim($card," \n");
        $rank = array_search(mb_substr($card, 0,-1,"utf-8"), self::RANKS);//get rank convert to int
        $suit = array_search(mb_substr($card, -1,1,"utf-8"), self::SUITS);//get the suit icon and convert to int

        $this->cardRank = $rank;
        $this->cardSuit = $suit;
    }

    public function getSuit()
    {
        return $this->cardSuit;
    }

    public function getRank()
    {
        return $this->cardRank;
    }

    public function toString()
    {
        return (self::RANKS[$this->cardRank] . self::SUITS[$this->cardSuit]);
    }

}