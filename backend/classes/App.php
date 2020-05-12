<?php

/**
 * Class App
 * AUTHOR Javier Sanz Roa
 * Date 17/04/2020
 * Genera el JSON correspondiente según la función que haya seleccionado el usuario con los datos que ha 
 * mandado el usuario
 */

class App
{

    /**
     * Recibe como parámetro un usuario y valida datos potencialmente repetidos. En caso correcto, inserta los datos en la
     * base de datos, guardando un nuevo usuario para la aplicación. En caso contrario, gestiona el error y devuelve la información
     * correspondiente para mostrarlo al usuario.
     */
    public function registerNewUser(Usuario $user)
    {
        $result = null; //alojará los datos resultantes de la función

        $bbdd = new BBDD();

        //validamos email y/o nombre de usuario repetido/s
        $validatedU = $bbdd->validateUsername($user->getUsername());
        $validatedE = $bbdd->validateEmail($user->getEmail());

        //si los datos introducidos no existen en la base de datos...
        if ($validatedU == true && $validatedE == true) {
            //limpiamos los datos a insertar en la bbdd
            $cleanName = $user->cleanData($user->getUsername());
            $cleanEmail = $user->cleanData($user->getEmail());

            //se los asignamos al objeto usuario
            $user->setUsername($cleanName);
            $user->setEmail($cleanEmail);


            //se llama a la función que inserta los datos en la base de datos
            $inserted = $bbdd->insertNewUser($user);
            $cod = $bbdd->getUserCod($user->getUsername());

            //si se completado la inserción...
            if ($inserted == true) {
                //se devuelve un mensaje afirmativo y los datos de usuario
                $result = array('insercion' => 'correcta', 'mensaje' => array(
                    'cod' => $cod,
                    'user' => $user->getUsername(),
                    'email' => $user->getEmail()
                ));
            } else {
                //si ha habido un error en la inserción, se devuelve dicho mensaje de error
                $result = array('insercion' => 'errorserve', 'mensaje' => 'Error al guardar los datos, inténtelo más tarde');
            }
        } else { //en caso de haber un dato repetido...
            //gestionamos el error dependiendo del valor ya existente en la base de datos
            if ($validatedE == true && $validatedU == false) {
                $result = array('insercion' => 'error', 'mensaje' => 'Nombre de usuario ya existente');
            } else if ($validatedE == false && $validatedU == true) {
                $result = array('insercion' => 'error', 'mensaje' => 'Email ya existente');
            } else if ($validatedE == false && $validatedU == false) {
                $result = array('insercion' => 'error', 'mensaje' => 'Email y nombre de usuario ya existentes');
            }
        }
        return $result;
    }


    /**
     * Realiza todas las operaciones necesarias para que un usuario haga Log In, devolviendo en un array los resultados
     */
    public function logIn(string $email, string $password)
    {
        $result = null;
        $npass = hash('md5', $password); //hacemos hash a la password pasada para validarla en la base de datos

        $bbdd = new BBDD();
        $valid = $bbdd->validateLogIn($email, $npass);

        //si los datos son erróneos...
        if ($valid === 'error') {
            //se devuelve la información del error
            $result = array('logueo' => 'incorrecto', 'mensaje' => 'Email o contraseña incorrectos');
        } else { //si son correctos...
            if ($email === "administrador@gmail.com") {
                $result = array('logueo' => 'correcto', 'usuario' => array('usuario' => 'ADMIN', 'img' => $valid['IMG']));
            } else {
                //devolvemos la información del usuario
            $result = array('logueo' => 'correcto', 'usuario' => array(
                'cod' => $valid['COD'],
                'username' => $valid['USER'], 'email' => $valid['EMAIL'],
                'img' => $valid['IMG']
            ));
            }
        }
        return $result;
    }


    /**
     * Realiza la operación de cambiar el nombre de usuario, manejando sus errores y posibles casos. Devuelve los resultados en un
     * array
     */
    public function changeUsername(string $username, string $new_username)
    {
        $result = null;

        //si ha introducido el mismo nombre de usuario en los dos campos...
        if ($username === $new_username) {
            //se notifica el error
            $result = array('cambio' => 'fallido', 'mensaje' => 'Introduce un nombre de usuario diferente, por favor.');
        } else { //en caso contrario...
            $bbdd = new BBDD();
            //validamos primero que su nombre de usuario actual es el correcto
            if ($bbdd->confirmUsername($username) == true) {
                //si está bien, validamos posteriormente el nuevo nombre, viendo si no existe ya en la bbdd
                if ($bbdd->validateUsername($new_username) == true) {
                    $changed = $bbdd->insertNewName($username, $new_username); //hacemos el cambio

                    if ($changed == true) { //si se ha cambiado correctamente, se notifica
                        $result = array('cambio' => 'correcto', 'mensaje' => 'Nombre de usuario cambiado correctamente');
                    } else { //si ha habido algún error, se notifica
                        $result = array('cambio' => 'fallido', 'mensaje' => 'Error al cambiar el nombre de usuario. Prueba más tarde');
                    }
                } else { //se notifica el tipo de error en caso de haber introducido mal algún dato
                    $result = array('cambio' => 'fallido', 'mensaje' => 'Nombre de usuario ya existente');
                }
            } else {
                $result = array('cambio' => 'fallido', 'mensaje' => 'Has introducido mal tu nombre de usuario');
            }
        }
        return $result;
    }


    /**
     * Realiza la lógica de cambiar la foto de perfil de un usuario. Devuelve un array con los resultados.
     */
    public function changePicture(string $username, string $picture)
    {
        $result = null;
        $bbdd = new BBDD();
        //obtenemos el nombre de la imagen anterior 
        $old_picture = $bbdd->getPictureName($username);

        //si la imagen anterior era la establecida por defecto...
        if ($old_picture === 'defecto.png') {
            $update = $bbdd->updateProfilePicture($username, $picture);

            if ($update == 'error') {
                $result = array('cambio' => 'error', 'mensaje' => 'Ha ocurrido un error al actualizar la imagen de perfil.');
            } else {
                //mensaje de que el cambio es correcto
            $result = array('cambio' => 'correcto', 'mensaje' => 'Imagen cambiada correctamente');
            }
        } else if ($old_picture === 'error') {
            //si ha habido un error, se notifica
            $result = array('cambio' => 'error', 'mensaje' => 'Ha ocurrido un error al actualizar la imagen de perfil.');
        }
        //por último, si ya se había cambiado la foto de perfil previamente... 
        else if ($old_picture != 'error' && $old_picture != 'defecto.png') {
            $result = array('cambio' => 'correcto', 'mensaje' => 'Imagen cambiada correctamente');
        }
        return $result;
    }

    /**
     * Devuelve true en caso de que el código de carta y de usuario sean válidos. Para evitar intentos maliciosos de entradas en 
     * la bd.
     */
    public function validateCods($cod_user, $cod_card)
    {
        $valid = false;
        $bbdd = new BBDD();

        $validateU = $bbdd->validateCodUser($cod_user);
        $validateP = $bbdd->validateCodPlayer($cod_card);

        if ($validateU == true && $validateP == true) {
            $valid = true;
        }
        return $valid;
    }


    /**
     * Realiza la lógica necesaria de insertar comentarios, haciendo validaciones y devolviendo un array con la información
     * específica de cada caso.
     */
    public function insertComment(Comentario $comment)
    {
        $result = null;

        //validamos que usuario que ha introducido comentario y jugador sobre el que se comenta existan
        $valid_cods = $this->validateCods($comment->getCodUser(), $comment->getCodPlayer());

        //si existen...
        if ($valid_cods == true) {
            //limpiamos contenido de código html, espacios en blanco...
            $cleanContent = $comment->cleanData($comment->getContent());
            $comment->setContent($cleanContent);

            //introducimos el comentario en la bd
            $bbdd = new BBDD();
            $inserted = $bbdd->newComment($comment);

            //si se ha insertado bien...
            if ($inserted == true) {
                //mensaje afirmativo
                $result = array('insercion' => 'correcta', 'mensaje' => 'Comentario agregado correctamente');
            } else {
                $result = array('insercion' => 'error', 'mensaje' => 'Error al insertar comentario');
            }
            //si el usuario y/o jugador son incorrectos...
        } else {
            //mensaje correspondiente de error
            $result = array('insercion' => 'error', 'mensaje' => 'El jugador y/o usuario no existen');
        }
        return $result;
    }


    /**
     * Realiza toda la lógica de valoraciones, validando si ya había una valoración asignada, si no la había, etc.
     * Devuelve un array con la información obtenida de cada caso específico
     */
    public function insertRate(Valoracion $rate)
    {
        $result = null;

        //validamos que usuario que ha introducido valorado y jugador sobre el que se valora existan
        $valid_cods = $this->validateCods($rate->getCodUser(), $rate->getCodPlayer());

        //si usuario y jugador son válidos...
        if ($valid_cods == true) {
            $bbdd = new BBDD();
            //obtenemos la valoración previa, en caso de haberla
            $currentRating = $bbdd->getRating($rate->getCodUser(), $rate->getCodPlayer());

            //si no había valoración asignada...
            if ($currentRating === 'n') {
                $bbdd->playerLikes($rate, $currentRating);
                //Creamos un registro con los datos pasados
                $insert = $bbdd->insertNewRating($rate);
                //si se inserta bien...
                if ($insert == true) {
                    //mensaje correspondiente
                    $result = array('insercion' => 'correcta', 'mensaje' => 'OK');
                } else {
                    $result = array('insercion' => 'error', 'mensaje' => 'Fallo con el servidor.');
                }
            } else { //si YA había una valoración en la bd
                $bbdd->playerLikes($rate, $currentRating);
                //validamos si la pasada es igual a la que ya había
                if ($currentRating == $rate->getRating()) {
                    //y reseteamos la valoración
                    $reset = $bbdd->resetRating($rate);
                    if ($reset == true) {
                        $result = array('insercion' => 'correcta', 'mensaje' => 'OK');
                    } else {
                        $result = array('insercion' => 'error', 'mensaje' => 'Fallo con el servidor.');
                    }
                } else { // si la valoración pasada es la contraria a la que había
                    $change = $bbdd->changeRating($rate);
                    //la cambiamos
                    if ($change == true) {
                        $result = array('insercion' => 'correcta', 'mensaje' => 'OK');
                    } else {
                        $result = array('insercion' => 'error', 'mensaje' => 'Fallo con el servidor.');
                    }
                }
            }
        } else { //si se intenta hacer una valoración sin tener usuario, mandamos mensaje correspondiente
            $result = array('insercion' => 'error', 'mensaje' => 'Inicia sesión para valorar una carta');
        }
        return $result;
    }


    /**
     * Devuelve un array con la información específica obtenida al intentar añadir una liga a la base de datos.
     */
    public function addLeague(Liga $league)
    {
        $result = null;
        $bbdd = new BBDD();
        $validName = $bbdd->validateLeagueName($league->getNombre()); //validamos que la liga no exista ya en la bd

        //si es válida...
        if ($validName == true) {
            $cod_nacio = $bbdd->searchNation($league->getPais());
            $league->setPais($cod_nacio);

            //insertamos
            $insert = $bbdd->insertNewLeague($league);

            //si se ha insertado bien...
            if ($insert == true) {
                //mensaje correspondiente
                $result = array('insercion' => 'ok', 'mensaje' => 'La liga se ha insertado correctamente en la base de datos');
            } else {
                //si hay un fallo en la inserción, se informa
                $result = array('insercion' => 'error', 'mensaje' => 'Error al insertar la liga en la base de datos');
            }
        } else {
            //si la liga ya estaba en la bd, se informa
            $result = array('insercion' => 'error', 'mensaje' => 'Esta liga ya existe en la BBDD');
        }
        return $result;
    }

    /**
     * Devuelve un array con la información específica de cada caso al insertar un equipo en la db
     */
    public function addTeam(Equipo $team)
    {
        $result = null;
        $bbdd = new BBDD();
        $validTeam = $bbdd->validateTeamName($team->getTeam()); //validamos que el equipo no esté ya en la bd

        //si es válido...
        if ($validTeam == true) {
            $cod_liga = $bbdd->searchLeague($team->getLeague());
            $team->setLeague($cod_liga);

            $insert = $bbdd->insertNewTeam($team); //insertamos

            //si se ha insertado bien...
            if ($insert == true) {
                //mensaje correspondiente
                $result = array('insercion' => 'ok', 'mensaje' => 'El equipo se ha insertado correctamente en la base de datos');
            } else {
                $result = array('insercion' => 'error', 'mensaje' => 'Error al insertar el equipo en la base de datos');
            }
        } else {
            //si el equipo ya estaba en la db, se informa
            $result = array('insercion' => 'error', 'mensaje' => 'Este equipo ya existe en la BBDD');
        }
        return $result;
    }

    /**
     * Devuelve un array con la información correspondiente al intentar insertar un jugador en la base de datos
     */
    public function addPlayer(Jugador $player)
    {
        $result = null;
        $bbdd = new BBDD();
        $validPlayer = $bbdd->validatePlayerName($player); //validamos que ese jugador no esté ya en la bd

        if ($validPlayer == true) {
            $cod_nac = $bbdd->searchNation($player->getCountry());
            $cod_team = $bbdd->searchTeam($player->getTeam());
            $player->setTeam($cod_team);
            $player->setCountry($cod_nac);

            $insert = $bbdd->insertNewPlayer($player); //insertamos

            if ($insert == true) {
                $result = array('insercion' => 'ok', 'mensaje' => 'El jugador se ha insertado correctamente en la base de datos');
            } else {
                $result = array('insercion' => 'error', 'mensaje' => 'Error al insertar el jugador en la base de datos');
            }
        } else {
            $result = array('insercion' => 'error', 'mensaje' => 'Este jugador ya existe en la BBDD');
        }
        return $result;
    }

    /**
     * Devuelve un array con la información correspondiente a cada caso al intentar insertar una carta nueva en la bd
     */
    public function addCard(Carta $card)
    {
        $result = null;
        $bbdd = new BBDD();
        $validCard = $bbdd->validatePlayerCard($card);

        if ($validCard == true) {
            $insert = $bbdd->insertNewCard($card);

            if ($insert == true) {
                $result = array('insercion' => 'ok', 'mensaje' => 'La carta se ha insertado correctamente en la base de datos');
            } else {
                $result = array('insercion' => 'error', 'mensaje' => 'Error al insertar ela carta en la base de datos');
            }
        } else {
            $result = array('insercion' => 'error', 'mensaje' => 'Esta carta ya existe en la BBDD');
        }
        return $result;
    }


    /**
     * Devuelve un array con toda la información completa que se debe tener al acceder a la vista de un jugador en concreto.
     */
    public function showAllCardData($card_code)
    {
        $result = null;

        $bbdd = new BBDD();
        $card_data = $bbdd->selectCardData($card_code); //primer array con información de la carta

        $cod_jugador = $card_data['cod_jugador'];

        $player_data = $bbdd->selectPInfo($cod_jugador);  //información personal del jugador
        $other_data = $bbdd->selectOtherData($cod_jugador, $card_code); // otros datos secundarios
        $stats = $bbdd->selectCardStats($card_code, $other_data['posicion']); // estadísticas de la carta
        $comments = $bbdd->selectComments($card_code, 5);

        //guardamos los arrays anteriores en uno general que contendrá toda la información
        $result['infocard'] = $card_data;
        $result['infoplayer'] = $player_data;

        if ($other_data['posicion'] == 'POR') {
            $result['stats'] = array('estirada' => $stats['estirada'], 'parada' => $stats['parada'], 'saque' => $stats['saque'],
            'reflejos' => $stats['reflejos'], 'velocidad' => $stats['velocidad'], 'posicion' => $stats['posicion']);
        } else {
            $result['stats'] = array(
                'ritmo' => $stats['ritmo'], 'tiro' => $stats['tiro'], 'pase' => $stats['pase'],
                'regate' => $stats['regate'], 'defensa' => $stats['defensa'], 'fisico' => $stats['fisico']
            );
        }
        $result['ostats'] = array('media' => $stats['media'], 'pmalo' => $stats['pie_malo'], 'skills' => $stats['filigranas']);
        $result['other'] = $other_data;
        $result['comments'] = $comments;

        return $result;
    }
}
