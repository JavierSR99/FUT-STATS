<?php

class Carta {
    protected $player;
    protected $card_rate;
    protected $card; //calidad de carta
    protected $prize; // será array con precios de las distintas plataformas
    protected $img;
    protected $stats; //será array con ritmo, tiro, pase, regate, defensa y físico
    protected $stars; //será array con pierna mala y filigranas


    public function __construct($player, $card, $prize, $img, $stats, $stars)
    {
        if (is_array($prize) && is_array($stats) && is_array($stars)) {
            $this->player = $player;
            $this->card = $card;
            $this->prize = $prize;
            $this->img = $img;
            $this->stats = $stats;
            $this->stars = $stars;
        }
    }

    /**
     * Get the value of player
     */ 
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get the value of card
     */ 
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Get the value of prize
     */ 
    public function getPrize()
    {
        return $this->prize;
    }

    /**
     * Get the value of img
     */ 
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Get the value of stats
     */ 
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Get the value of stars
     */ 
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Get the value of card_rate
     */ 
    public function getCard_rate()
    {
        return $this->card_rate;
    }
}