<?php
/**
 * Class Liga
 * AUTHOR Javier Sanz Roa
 * Date 21/04/2020
 */

class Liga extends Data {
    protected $nombre;
    protected $pais;
    protected $equipos;
    protected $creacion;

    public function __construct(string $nombre,$pais, $equipos, $creacion)
    {
        if (strlen($nombre)<=255) {
            $this->nombre = $this->cleanData($nombre);
            $this->pais = $pais;
            $this->equipos = $equipos;
            $this->creacion = $creacion;
        }
    }

    

    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get the value of pais
     */ 
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set the value of pais
     *
     * @return  self
     */ 
    public function setPais($pais)
    {
        $this->pais = $pais;
    }

    /**
     * Get the value of equipos
     */ 
    public function getEquipos()
    {
        return $this->equipos;
    }

    /**
     * Set the value of equipos
     *
     * @return  self
     */ 
    public function setEquipos($equipos)
    {
        $this->equipos = $equipos;
    }

    /**
     * Get the value of creacion
     */ 
    public function getCreacion()
    {
        return $this->creacion;
    }

    /**
     * Set the value of creacion
     *
     * @return  self
     */ 
    public function setCreacion($creacion)
    {
        $this->creacion = $creacion;
    }
}