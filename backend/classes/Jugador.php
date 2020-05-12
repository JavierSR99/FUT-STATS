<?php
/**
 * Class Jugador
 * AUTHOR Javier Sanz Roa
 * Date 21/04/2020
 */

class Jugador extends Data {

    protected $name;
    protected $surname;
    protected $nickname;
    protected $date;
    protected $country;
    protected $team;
    protected $img;

    public function __construct(string $name, string $surname, string $nickname, $date, $country, $team, $img)
    {
        if (strlen($name) <= 255 && strlen($surname) <= 255 && strlen($nickname) <= 255) {
            $this->name = $this->cleanData($name);
            $this->surname = $this->cleanData($surname);
            $this->nickname = $this->cleanData($nickname);
            $this->date = $date;
            $this->country = $country;
            $this->team = $team;
            $this->img = $img;
        }
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the value of surname
     */ 
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set the value of surname
     *
     * @return  self
     */ 
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Get the value of nickname
     */ 
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set the value of nickname
     *
     * @return  self
     */ 
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;;
    }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get the value of country
     */ 
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @return  self
     */ 
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get the value of team
     */ 
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set the value of team
     *
     * @return  self
     */ 
    public function setTeam($team)
    {
        $this->team = $team;
    }

    public function getImg () {
        return $this->img;
    }

    public function setImg ($img) {
        $this->img = $img;
    }
}