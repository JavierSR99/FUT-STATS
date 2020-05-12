<?php
/**
 * Class Equipo
 * AUTHOR Javier Sanz Roa
 * Date 21/04/2020
 */

class Equipo extends Data {
    protected $team;
    protected $fundation;
    protected $president;
    protected $league;

    public function __construct(string $team, $fundation, string $president, $league)
    {
        if (strlen($team) <= 255 && strlen($president) <= 255) {
            $this->team = $this->cleanData($team);
            $this->fundation = $fundation;
            $this->president = $this->cleanData($president);
            $this->league = $league;
        }
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

    /**
     * Get the value of fundation
     */ 
    public function getFundation()
    {
        return $this->fundation;
    }

    /**
     * Set the value of fundation
     *
     * @return  self
     */ 
    public function setFundation($fundation)
    {
        $this->fundation = $fundation;
    }

    /**
     * Get the value of president
     */ 
    public function getPresident()
    {
        return $this->president;
    }

    /**
     * Set the value of president
     *
     * @return  self
     */ 
    public function setPresident($president)
    {
        $this->president = $president;
    }

    /**
     * Get the value of league
     */ 
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * Set the value of league
     *
     * @return  self
     */ 
    public function setLeague($league)
    {
        $this->league = $league;
    }
}