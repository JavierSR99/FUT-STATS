<?php
/**
 * Class Comentario
 * AUTHOR Javier Sanz Roa
 * Date 20/04/2020
 */


class Comentario extends Data {
    protected $content;
    protected $cdate;

    public function __construct($cod_card, $cod_user, string $content, $cdate)
    {
        if (strlen($content)<=255) {
            $this->cod_card=$cod_card;
            $this->cod_user=$cod_user;
            $this->content=$content;
            $this->cdate=$cdate;
        }
    }

    public function getContent () {
        return $this->content;
    }

    public function setContent ($content) {
        if (strlen($content)<=255) {
            $this->content=$content;
        }
    }

    public function getCDate () {
        return $this->cdate;
    }
}