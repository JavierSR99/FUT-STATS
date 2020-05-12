<?php

class Data {

    protected $cod_user;
    protected $cod_card;


    public function getCodUser () {
        return $this->cod_user;
    }

    public function getCodPlayer () {
        return $this->cod_card;
    }


    /**
     * recibe un string como parámetro y lo devuelve limpio de código html, espacios en blanco y elementos 
     * que no deben ser introducidos en la base de datos
     */
    public function cleanData ($data) {
        $clean=strip_tags(html_entity_decode(htmlspecialchars(trim(($data)))));

        return $clean;
    }
}