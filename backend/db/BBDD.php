<?php

/**
 * Class BBDD
 * AUTHOR Javier Sanz Roa
 * Date 17/04/2020
 * Crea una conexión a la base de datos y trata todos los casos de validaciones y excepciones que pueden darse
 */
//INSERT INTO cartas (cod_jugador, media, ritmo, tiro, pase, regate, defensa, físico, pie_malo, 
//filigranas, cod_calidad, precio_ps, precio_xbox, precio_pc, imagen)
//VALUES (1, 91, 91, 83, 86, 94, 35, 66, 4, 4, 6, 93000, 79000, 109000, 'edenhazard_orounico.png')

class BBDD
{
    protected const HOST = "localhost";
    public const US = "root";
    public const PW = "";
    protected const BBDD = "fut_stats";
    protected $bbdd;

    public function __construct()
    {
        $this->bbdd = new mysqli();
        try {
            $this->bbdd->connect(self::HOST, self::US, self::PW, self::BBDD);
            $this->bbdd->query("SET NAMES 'utf8'");
        } catch (Exception $e) {
            $this->bbdd = null;
            throw new Exception("Error de conexión" . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->bbdd->close();
        $this->bbdd = null;
    }

    /**
     * Recibe como parámetro un usuario y devuelve true en caso de no estar ya en la base de datos, en caso de
     * que ese nombre de usuario ya exista en la base de datos, devolverá false
     */
    public function validateUsername(string $user)
    {
        $validated = true;
        $sql = $this->bbdd->query("SELECT username FROM usuarios WHERE username LIKE '$user'");

        //validamos la existencia de ese nombre de usuario mirando las filas que devuelve la query
        if ($sql->num_rows == 1) {
            $validated = false;
        }
        return $validated;
    }


    /**
     * Recibe como parámetro un email y devuelve true en caso de no estar ya en la base de datos, en caso de estar ya 
     * ese email en la bbdd, devolverá false
     */
    public function validateEmail(string $email)
    {
        $validated = true;
        $sql = $this->bbdd->query("SELECT email FROM usuarios WHERE email LIKE '$email'");

        //validamos la existencia de ese nombre de usuario mirando las filas que devuelve la query
        if ($sql->num_rows == 1) {
            $validated = false;
        }
        return $validated;
    }

    /**
     * recibe como parámetro un usuario cuyos datos han sido validados previamente y lo inserta en la base de datos
     */
    public function insertNewUser(Usuario $user)
    {
        $inserted = false;

        //sacamos los datos del usuarios (deben estar validados previamente)
        $username = $user->getUsername();
        $email = $user->getEmail();
        $password = $user->getPassword();


        $sql = $this->bbdd->query("INSERT INTO usuarios (username, email, clave, tipo, strikes, foto_perfil) VALUES ('$username',
        '$email', '$password', 'US', 0, 'defecto.png')");

        //validamos que la inserción sea correcta con la función affected rows
        if ($this->bbdd->affected_rows > 0) {
            $inserted = true;
        }
        return $inserted;
    }

    public function getUserCod ($username) {
        $cod = null;
        $sql = $this->bbdd->query("SELECT cod_usuario FROM usuarios WHERE username LIKE '$username'");

        foreach ($sql as $data) {$cod = $data['cod_usuario'];}
        return $cod;
    }

    /**
     * Devuelve true si el usuario pasado como parámetro existe en la base de datos, false en caso contrario
     */
    public function confirmUsername(string $username)
    {
        $confirmed = false;

        $sql = $this->bbdd->query("SELECT username FROM usuarios WHERE username LIKE '$username'");

        if ($sql->num_rows == 1) {
            $confirmed = true;
        }
        return $confirmed;
    }

    /**
     * cambia el nombre de usuario (nombres validados previamente). Devuelve true en caso de realizarse la 
     * actualización correctamente, false en caso contrario
     */
    public function insertNewName(string $username, string $new_username)
    {
        $changed = false;
        $sql = $this->bbdd->query("UPDATE usuarios SET username='$new_username' WHERE username LIKE '$username'");

        if ($this->bbdd->affected_rows == 1) {
            $changed = true;
        }
        return $changed;
    }


    /**
     * Valida que los datos introducidos al hacer log in son correctos
     */
    public function validateLogIn(string $email, string $password)
    {
        $data = null;

        $sql = $this->bbdd->query("SELECT cod_usuario, username, email, foto_perfil FROM usuarios 
        WHERE email LIKE '$email' AND clave LIKE '$password'");

        //si los datos son correctos...
        if ($sql->num_rows == 1) {
            //insertamos en un array los datos del usuario para devolverlos
            foreach ($sql as $udata) {
                $data = array('COD' => $udata['cod_usuario'], 'USER' => $udata['username'], 'EMAIL' => $udata['email'],
            'IMG' => $udata['foto_perfil']);
            }
        } else {
            $data = 'error';
        }
        return $data;
    }

    /**
     * Devuelve el nombre de la imagen de perfil del usuario para validar si ya la había cambiado previamente o no
     */
    public function getPictureName (string $user) {
        $result = null;

        $sql = $this->bbdd->query("SELECT foto_perfil FROM usuarios WHERE username LIKE '$user'");

        if ($sql->num_rows > 0) {
            foreach ($sql as $data) {
                $result = $data['foto_perfil'];
            } 
        } else {
            $result = 'error';
        }
        return $result;
    }

    /**
     * Actualiza la foto de perfil de la base de datos y devuelve el nombre de la foto que había anteriormente
     */
    public function updateProfilePicture(string $user, string $new_picture)
    {
        $result = '';

        $update = $this->bbdd->query("UPDATE usuarios SET foto_perfil='$new_picture' WHERE username LIKE '$user'");

        if ($this->bbdd->affected_rows == 1) {
            $result = 'ok';
        } else {
            $result = "error";
        }
        return $result;
    }

    /**
     * valida el código de carta/jugador contra la bd, devolviendo true si existe y false, si no
     */
    public function validateCodPlayer($cod_card)
    {
        $validated = false;

        $sql = $this->bbdd->query("SELECT cod_carta FROM cartas WHERE cod_carta LIKE '$cod_card'");

        if ($sql->num_rows == 1) {
            $validated = true;
        }
        return $validated;
    }

    /**
     * valida el código de usuario contra la bd, devolviendo true si existe y false, si no
     */
    public function validateCodUser($cod_user)
    {
        $validated = false;

        $sql = $this->bbdd->query("SELECT cod_usuario FROM usuarios WHERE cod_usuario LIKE '$cod_user'");

        if ($sql->num_rows == 1) {
            $validated = true;
        }
        return $validated;
    }

    /**
     * Inserta un nuevo comentario en la bd. Contenidos previamente validados. Devuelve true si se han insertado bien en la bd
     */
    public function newComment(Comentario $comment)
    {
        $result = false;

        //sacamos los datos
        $codUser = $comment->getCodUser();
        $codCard = $comment->getCodPlayer();
        $content = $comment->getContent();
        $date = $comment->getCDate();

        //los insertamos
        $sql = $this->bbdd->query("INSERT INTO comentarios (cod_usuario, cod_carta, contenido, fecha) VALUES
        ('$codUser', '$codCard', '$content', '$date')");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }

    /**
     * Borra un comentario cuyo código se ha pasado como parámetro
     */
    public function deleteComment ($cod_comment) {
        $result = false;

        $sql = $this->bbdd->query("DELETE FROM comentarios WHERE cod_comentario=$cod_comment");

        if ($this->bbdd->affected_rows == -1) {
            $result = true;
        }
        return $result;
    }

    /**
     * Obtiene la valoración actual de un usuario específico a una carta específica.
     */
    public function getRating($cod_user, $cod_card)
    {
        $result = null;

        //Preguntamos por la valoración
        $sql = $this->bbdd->query("SELECT valoracion FROM valoraciones WHERE cod_usuario LIKE '$cod_user' AND cod_carta LIKE '$cod_card'");

        //Si devuelve una fila...
        if ($sql->num_rows == 1) {
            //Nos guardamos el resultado para devolverlo
            foreach ($sql as $data) {
                $result = $data['valoracion'];
            }
        } else { //en caso de no devolver filas (el usuario no había valorado la carta previamente)
            //devolveremos una 'n', equivalente a null o nothing
            $result = 'n';
        }
        return $result;
    }

    /**
     * Inserta un nuevo registro de valoración
     */
    public function insertNewRating(Valoracion $rate)
    {
        $result = false;
        $cod_user = $rate->getCodUser();
        $cod_card = $rate->getCodPlayer();
        $rating = $rate->getRating();

        $sql = $this->bbdd->query("INSERT INTO valoraciones (cod_usuario, cod_carta, valoracion) 
        VALUES ('$cod_user', '$cod_card', '$rating')");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }

    /**
     * Actualiza una valoración, anulando la que estaba asignada
     */
    public function resetRating(Valoracion $rate)
    {
        $result = false;
        $cod_user = $rate->getCodUser();
        $cod_card = $rate->getCodPlayer();

        //asignamos la valoración a NULL
        $sql = $this->bbdd->query("UPDATE valoraciones SET valoracion = NUll WHERE cod_usuario = $cod_user AND cod_carta = $cod_card");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }

    /**
     * Cambia la valoración entre los dos valores posibles (0 y 1)
     */
    public function changeRating(Valoracion $rate)
    {
        $result = false;
        $cod_user = $rate->getCodUser();
        $cod_card = $rate->getCodPlayer();
        $rating = $rate->getRating();

        $sql = $this->bbdd->query("UPDATE valoraciones SET valoracion = $rating WHERE cod_usuario = $cod_user AND cod_carta = $cod_card");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }


    /**
     * Devuelve un array con la cantidad de "me gusta" y "no me gusta" que tiene.
     */
    public function likes($cod_card)
    {
        $result = null;

        $sql = $this->bbdd->query("SELECT me_gusta, no_me_gusta FROM cartas WHERE cod_carta = $cod_card");

        if ($sql->num_rows == 1) {
            foreach ($sql as $data) {
                $result = array('MG' => $data['me_gusta'], 'NMG' => $data['no_me_gusta']);
            }
        }
        return $result;
    }

    /**
     * Actualiza los "me gusta" y/o "no me gusta" de un jugador cuando el usuario selecciona una de las dos valoraciones.
     */
    public function playerLikes(Valoracion $rate, $current_rating)
    {
        //obtenemos datos necesarios
        $cod_card = $rate->getCodPlayer();
        $cod_user = $rate->getCodUser();
        $rating = $rate->getRating();

        //obtenemos las valoraciones del jugador
        $likes = $this->likes($cod_card);

        //si su valoración actual es NULL o no se ha valorado previamente...
        if ($current_rating == 'n') {
            //habrá dos casos
            switch ($rating) {
                    //case 0; es decir, el usuario ha seleccionado "me gusta"
                case 0:
                    // se le actualizarán los "me gusta", sumándole 1
                    $like = $likes['MG'] + 1;
                    $sql = $this->bbdd->query("UPDATE cartas SET me_gusta = $like 
                    WHERE cod_carta = $cod_card");
                    break;
                    //case 1, ha seleccionado "no me gusta"
                case 1:
                    // se le actualizarán los "no me gusta", sumándole 1
                    $dislike = $likes['NMG'] + 1;
                    $sql = $this->bbdd->query("UPDATE cartas SET no_me_gusta = $dislike
                     WHERE cod_carta = $cod_card");
                    break;
            }
            //en caso de que el usuario ya hubiera realizado una valoración previamente    
        } else {
            // dos posibles casos
            switch ($rating) {
                    // case 0, ha seleccionado "me gusta"
                case 0:
                    // si ha seleccionado la misma opción que ya había, quiere decir que la está anulando
                    if ($current_rating == $rating) {
                        // los "me gusta" disminuirán en uno
                        $like = $likes['MG'] - 1;
                        $sql = $this->bbdd->query("UPDATE cartas SET me_gusta = $like 
                        WHERE cod_carta = $cod_card");
                    }
                    // si ha seleccionado "no me gusta", cuando su valoración estaba en "me gusta" 
                    else if ($current_rating != $rating && $current_rating != NULL) {
                        $like = $likes['MG'] + 1;
                        $dislike = $likes['NMG'] - 1;
                        $sql = $this->bbdd->query("UPDATE cartas SET me_gusta = $like, no_me_gusta = $dislike 
                        WHERE cod_carta = $cod_card");
                    } else if ($current_rating != $rating && $current_rating == NULL) {
                        $like = $likes['MG'] + 1;
                        $sql = $this->bbdd->query("UPDATE cartas SET me_gusta = $like 
                        WHERE cod_carta = $cod_card");
                    }
                    break;
                case 1:
                    if ($current_rating == $rating) {
                        $dislike = $likes['NMG'] - 1;
                        $sql = $this->bbdd->query("UPDATE cartas SET no_me_gusta = $dislike 
                        WHERE cod_carta = $cod_card");
                    } else if ($current_rating != $rating && $current_rating != NULL) {
                        $like = $likes['MG'] - 1;
                        $dislike = $likes['NMG'] + 1;
                        $sql = $this->bbdd->query("UPDATE cartas SET me_gusta = $like, no_me_gusta = $dislike 
                        WHERE cod_carta = $cod_card");
                    } else if ($current_rating != $rating && $current_rating == NULL) {
                        $dislike = $likes['NMG'] + 1;
                        $sql = $this->bbdd->query("UPDATE cartas SET no_me_gusta = $dislike 
                        WHERE cod_carta = $cod_card");
                    }
                    break;
            }
        }
    }


    /**
     * Valida que una liga esté o no ya en la base de datos. Devuelve true si no lo está, false en caso contrario.
     */
    public function validateLeagueName(string $league)
    {
        $valid = true;

        $sql = $this->bbdd->query("SELECT nombre FROM ligas WHERE UPPER(nombre) = UPPER('$league')");

        //si devuelve una o más filas, quiere decir que ya existe
        if ($sql->num_rows > 0) {
            $valid = false;
        }
        return $valid;
    }

    /**
     * Valida que un equipo esté o no ya en la base de datos. Devuelve true si no lo está, false en caso contrario.
     */
    public function validateTeamName(string $team)
    {
        $valid = true;

        $sql = $this->bbdd->query("SELECT nombre FROM equipos WHERE UPPER(nombre) = UPPER('$team')");

        //si devuelve una o más filas, quiere decir que ya existe
        if ($sql->num_rows > 0) {
            $valid = false;
        }
        return $valid;
    }

    /**
     * Valida que un jugador esté o no ya en la base de datos. Devuelve true si no lo está, false en caso contrario.
     */
    public function validatePlayerName(Jugador $player)
    {
        $valid = true;

        $name = $player->getName();
        $surname = $player->getSurname();
        $nickname = $player->getNickname();

        $sql = $this->bbdd->query("SELECT cod_jugador FROM jugadores WHERE UPPER(nombre)=UPPER('$name') AND
        UPPER(apellidos)=UPPER('$surname') AND UPPER(apodo) = UPPER('$nickname')");
        
        //si devuelve una o más filas, quiere decir que ya existe
        if ($sql->num_rows > 0) {
            $valid = false;
        }
        return $valid;
    }

    /**
     * Valida que una carta esté o no ya en la base de datos. Devuelve true si no lo está, false en caso contrario.
     */
    public function validatePlayerCard (Carta $card) {
        $valid = true;

        $player = $card->getPlayer();
        $rate = $card->getCard();

        $sql = $this->bbdd->query("SELECT cod_carta FROM cartas WHERE cod_jugador=$player AND cod_calidad=$rate");

        //si devuelve una o más filas, quiere decir que ya existe
        if ($sql->num_rows > 0) {
            $valid = false;
        }
        return $valid;
    }

    /**
     * Devuelve un array con los códigos y nombre de los países de la base de datos.
     */
    public function allNations()
    {
        $result = null;

        $sql = $this->bbdd->query("SELECT pais FROM nacionalidades");

        foreach ($sql as $data) {
            $result[] = $data['pais'];
        }
        return $result;
    }

    public function searchNation ($name) {
        $result = null;

        $sql = $this->bbdd->query("SELECT cod_nacio FROM nacionalidades WHERE pais = '$name'");

        if ($sql->num_rows == 1) {
            foreach($sql as $data) { $result = $data['cod_nacio']; }
        }


        return $result;
    }

    /**
     * Devuelve un array con los códigos y nombre de las ligas de la base de datos.
     */
    public function allLeagues()
    {
        $result = null;

        $sql = $this->bbdd->query("SELECT cod_liga, nombre FROM ligas");

        foreach ($sql as $data) {
            $result[] = $data['nombre'];
        }
        return $result;
    }

    public function searchLeague ($name) {
        $result = null;

        $sql = $this->bbdd->query("SELECT cod_liga FROM ligas WHERE nombre = '$name'");

        if ($sql->num_rows == 1) {
            foreach($sql as $data) { $result = $data['cod_liga']; }
        }


        return $result;
    }

    public function searchTeam ($name) {
        $result = null;

        $sql = $this->bbdd->query("SELECT cod_equipo FROM equipos WHERE nombre = '$name'");

        if ($sql->num_rows == 1) {
            foreach($sql as $data) { $result = $data['cod_equipo']; }
        }


        return $result;
    }

    /**
     * Devuelve un array con los códigos y nombre de los equipos de la base de datos.
     */
    public function allTeams()
    {
        $result = null;

        $sql = $this->bbdd->query("SELECT cod_equipo, nombre FROM equipos");

        foreach ($sql as $data) {
            $result[] = $data['nombre'];
        }
        return $result;
    }

    public function searchPlayer ($name) {
        $result = null;

        $sql = $this->bbdd->query("SELECT cod_jugador FROM jugadores WHERE apodo = '$name'");

        if ($sql->num_rows == 1) {
            foreach($sql as $data) { $result = $data['cod_liga']; }
        }


        return $result;
    }

    /**
     *  Devuelve un array con los códigos y nombre de los jugadores de la base de datos.
     */
    public function allPlayer () {
        $result = null;

        $sql = $this->bbdd->query("SELECT cod_jugador, apodo FROM jugadores");

        foreach ($sql as $data) {
            $result[] = array('COD' => $data['cod_jugador'], 'PLAYER' => $data['nombre']);
        }
        return $result;
    }

    /**
     * Inserta en la base de datos una liga nueva (previamente validada). Devuelve true si la inserción se realiza
     * correctamente, false si no llega a producirse.
     */
    public function insertNewLeague(Liga $league)
    {
        $result = false;

        $name = $league->getNombre();
        $country = $league->getPais();
        $teams = $league->getEquipos();
        $date = $league->getCreacion();

        $sql = $this->bbdd->query("INSERT INTO ligas (nombre, cod_nacio, n_equipos, creacion) 
        VALUES ('$name', $country, $teams, '$date')");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }


    /**
     * Inserta en la base de datos un equipo nuevo (previamente validado). Devuelve true si la inserción se realiza
     * correctamente, false si no llega a producirse.
     */
    public function insertNewTeam(Equipo $team)
    {
        $result = false;

        $name = $team->getTeam();
        $fundation = $team->getFundation();
        $president = $team->getPresident();
        $league = $team->getLeague();

        $sql = $this->bbdd->query("INSERT INTO equipos (nombre, fundacion, presidente, cod_liga) 
        VALUES ('$name', '$fundation', '$president', $league)");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }

    /**
     * Inserta en la base de datos un jugador nuevo (previamente validado). Devuelve true si la inserción se realiza
     * correctamente, false si no llega a producirse.
     */
    public function insertNewPlayer(Jugador $player)
    {
        $result = false;

        $name = $player->getName();
        $surname = $player->getSurname();
        $nickname = $player->getNickname();
        $date = $player->getDate();
        $nac = $player->getCountry();
        $team = $player->getTeam();
        $img = $player->getImg();

        $sql = $this->bbdd->query("INSERT INTO jugadores (nombre, apellidos, apodo, fecha_nac, cod_nacio, cod_equipo, img)
        VALUES ('$name', '$surname', '$nickname', '$date', $nac, $team, '$img')");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }

    /**
     * Inserta en la base de datos una carta nueva (previamente validada). Devuelve true si la inserción se realiza
     * correctamente, false si no llega a producirse.
     */
    public function insertNewCard (Carta $card) {
        $result = false;

        $player = $card->getPlayer();
        $card_color = $card->getCard();
        $card_rate = $card->getCard_rate();
        $prize = $card->getPrize();
        $stats = $card->getStats();
        $stars = $card->getStars();
        $img = $card->getImg();

        $sql = $this->bbdd->query("INSERT INTO cartas (cod_jugador, media, ritmo, tiro, pase, regate, defensa, físico, pie_malo, 
        filigranas, cod_calidad, precio_ps, precio_xbox, precio_pc, imagen, me_gusta, no_me_gusta) VALUES ($player, $card_rate,
        $stats[0], $stats[1], $stats[2], $stats[3], $stats[4], $stats[5], $stars[0], $stars[1], $card_color, $prize[0], $prize[1],
        $prize[2], '$img', 0, 0)");

        if ($this->bbdd->affected_rows == 1) {
            $result = true;
        }
        return $result;
    }

    /**
     * Devuelve un array con los datos necesarios para mostrar la preview de un jugador, al buscar por su nombre
     */
    public function selectPreviewData (string $name) {
        $result = null;

        $sql = $this->bbdd->query("SELECT c.cod_carta, j.nombre, j.apellidos, j.apodo, j.img
        FROM cartas c JOIN jugadores j ON c.cod_jugador=j.cod_jugador WHERE apodo LIKE '%$name%'");

        
        foreach ($sql as $data) {
            $result[]=array('COD' => $data['cod_carta'],'NAME' => $data['nombre'], 'SURNAME' => $data['apellidos'], 
            'NICKNAME' => $data['apodo'],'IMG' => $data['img']);
        }
        return $result;
    } 

    /**
     * Devuelve un array con la información de la carta de un jugador cuyo código de carta ha mandado el usuario
     */
    public function selectCardData ($card_code) {
        $result = null;
        $sql = $this->bbdd->query("SELECT cod_jugador, cod_calidad, cod_posicion, precio_ps, precio_xbox, 
        precio_pc, me_gusta, no_me_gusta, img_carta FROM cartas WHERE cod_carta = $card_code");

        foreach ($sql as $data) {
            $result = $data;
        }
        return $result;
    }

    /**
     * Devuelve un array con la información personal de un jugador. Complementaría a la función "selectCardData"
     */
    public function selectPInfo ($player_code) {
        $result = null;

        $sql = $this->bbdd->query("SELECT nombre, apellidos, fecha_nac, cod_nacio, cod_equipo FROM jugadores
         WHERE cod_jugador = $player_code");

         foreach ($sql as $data) {
             $result  = $data;
         }
        return $result;
    }

    /**
     * Devuelve un array con las estadísticas de un jugador
     */
    public function selectCardStats ($card_code, $position) {
        $result = null;

        if ($position == 'POR') {
            $sql = $this->bbdd->query("SELECT media, estirada, parada, saque, reflejos, velocidad, posicion, pie_malo, filigranas
             FROM porteros WHERE cod_carta = $card_code");
        } else {
            $sql = $this->bbdd->query("SELECT media, ritmo, tiro, pase, regate, defensa, fisico, pie_malo, filigranas 
        FROM estadisticas WHERE cod_carta = $card_code");
        }

        foreach ($sql as $data) { $result = $data;}
        return $result;
    }

    /**
     * Devuelve un array con información secundaria de una carta seleccionada (posición, nacionalidad y calidad de carta).
     */
    public function selectOtherData ($player_code, $card_code) {
        $result = null;

        //Primera consulta, calidad de carta
        $sql = $this->bbdd->query("SELECT valor FROM calidades 
        WHERE cod_calidad=(SELECT cod_calidad FROM cartas WHERE cod_carta = $card_code)");

        //Segunda consulta, posición en la que juega la carta
        $sql_s = $this->bbdd->query("SELECT posicion FROM posiciones 
        WHERE cod_posicion=(SELECT cod_posicion FROM cartas WHERE cod_carta = $card_code)");

        //Tercera consulta, nacionalidad del jugador
        $sql_t = $this->bbdd->query("SELECT pais FROM nacionalidades 
        WHERE cod_nacio=(SELECT cod_nacio FROM jugadores WHERE cod_jugador = $player_code)");

        //Guardamos en el array a devolver los valores que nos interesan
        foreach ($sql as $data) {$result['calidad']=$data['valor'];}
        foreach ($sql_s as $data) {$result['posicion'] = $data['posicion'];}
        foreach ($sql_t as $data) {$result['pais'] = $data['pais'];}

        return $result;
    }

    /**
     * Devuelve un array multidimensional con los comentarios y sus fechas de una carta
     */
    public function selectComments ($cod_card, $limit) {
        $result = null;

        $sql = $this->bbdd->query("SELECT c.contenido, c.fecha, c.cod_comentario, u.foto_perfil, u.username
        FROM comentarios c JOIN usuarios u ON u.cod_usuario=c.cod_usuario WHERE cod_carta = $cod_card
        ORDER BY c.cod_comentario DESC LIMIT $limit");

        foreach ($sql as $data) { $result[] = array('DATE' => $data['fecha'], 'CONTENT' => $data['contenido'],
            'IMG' => $data['foto_perfil'], 'USER' => $data['username'], 'COD' => $data['cod_comentario']); }

        return $result;
    }

    /**
     * Devuelve la foto de perfil de un usuario
     */
    public function userProfile ($cod_user) {
        $result = null;

        $sql = $this->bbdd->query("SELECT foto_perfil FROM usuarios WHERE cod_usuario = $cod_user");

        if ($sql->num_rows == 1) {
            foreach ($sql as $data) { $result['pic'] =  $data['foto_perfil']; }
        } else {
            $result ['pic'] = 'error';
        }
        return $result;
    }

    /**
     * Devuelve el número de canciones de la base de datos
     */
    public function allSongs () {
        $result = null;

        $sql = $this->bbdd->query("SELECT COUNT(num) FROM playlist");

        foreach($sql as $data) {$result['songs'] = $data['COUNT(num)'];}

        return $result;
    }

    /**
     * Devuelve un array con la información de las canciones correspondiente según la información recibida del cliente.
     */
    public function playlist ($min, $max) {
        $result = null;

        //el mínimo y máximo se rigen según la página en la que se encuentre el usuario
        $sql = $this->bbdd->query("SELECT cod_cancion, titulo, artista FROM playlist WHERE num>=$min AND num<=$max");

        foreach ($sql as $data) {
            $result[] = array('cod' => $data['cod_cancion'], 'title' => $data['titulo'], 'artist' => $data['artista']);
        }
        return $result;
    }
}
