<?php

class Valoracion extends Data {
    protected $rating;

    public function __construct($cod_user, $cod_card, $rating)
    {
        if ($rating == 0 || $rating==1) {
            $this->cod_user=$cod_user;
            $this->cod_card=$cod_card;
            $this->rating=$rating;
        }
    }

    public function getRating () {
        return $this->rating;
    }

    public function setRating ($rating) {
        if ($rating == 0 || $rating == 1) {
            $this->rating=$rating;
        }
    }
}