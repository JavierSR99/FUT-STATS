<?php
/**
 * Class Usuario
 * AUTHOR Javier Sanz Roa
 * Date 17/04/2020
 * Crea un usuario con sus datos necesarios y los trata de forma que puedan ser insertados/modificados en la base de datos
 */


class Usuario extends Data {
    protected $username;
    protected $email;
    protected $password;

    /**
     * crea el objeto Usuario, validando la longitud del nombre de usuario e email. Posteriormente asigna
     * los valores pasados en caso de ser correctos, con la contraseña ya encriptada
     */
    public function __construct (string $username, string $email, string $password)
    {
        if (strlen($username)<=255 && strlen($email)<=255) {
            $this->username=$username;
            $this->email=$email;
            $this->password=$this->encryptPassword($password);
        }
    }

    /**
     * encripta el valor que se le pase como parámetro
     */
    public function encryptPassword ($password) {
        $encryptedP=hash('md5', $password);

        return $encryptedP;
    }


    public function getUsername () {
        return $this->username;
    }

    public function getEmail () {
        return $this->email;
    }

    public function getPassword () {
        return $this->password;
    }

    public function setUsername ($username) {
        if (strlen($username)<=255) {
            $this->username=$username;
        }
    }

    public function setEmail ($email) {
        if (strlen($email)<=255) {
            $this->email=$email;
        }
    }

    public function setPassword ($password) {
        $this->password=$this->encryptPassword($password);
    }
}