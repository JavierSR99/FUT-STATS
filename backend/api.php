<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: get, post, put, delete');
header('content-type: application/json; charset=utf-8');

include 'classes/Data.php';
include 'classes/Usuario.php';
include 'db/BBDD.php';
include 'classes/App.php';
include 'classes/Comentario.php';
include 'classes/Valoracion.php';
include 'classes/Liga.php';
include 'classes/Equipo.php';
include 'classes/Jugador.php';


$func = $_POST['func'] ?? null;

$username = $_POST['username'] ?? null;
$new_username = $_POST['newusername'] ?? null; //para el cambio de nombre de usuario
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$picture = $_FILES['picture'] ?? null;

$cod_card = $_POST['cod_card'] ?? null;
$cod_user = $_POST['cod_user'] ?? null;
$content = $_POST['content'] ?? null; //contenido de un comentario
$date = $_POST['date'] ?? null; //cualquier fecha a introducir en db
$rate = $_POST['rate'] ?? null; //valoración que manda el usuario

$leaguename = $_POST['league'] ?? null; //para el nombre o código de liga
$cod_nac = $_POST['nacio'] ?? null;
$teams = $_POST['teams'] ?? null; //para el número de equipos

$team_name = $_POST['teamname'] ?? null; //se usa para el nombre o el código del equipo
$president = $_POST['president'] ?? null;

$player_name = $_POST['player'] ?? null; //se usa para nombre o código de jugador
$player_surname = $_POST['splayer'] ?? null;
$nickname = $_POST['nickname'] ?? null;

$player_card = $_POST['ratingcard'] ?? null; //calidad de carta
$player_img = $_FILES['playerimg'] ?? null;
$stats = array(
    $_POST['pace'] ?? null, $_POST['shoot'] ?? null, $_POST['pass'] ?? null,
    $_POST['dribling'] ?? null, $_POST['defense'] ?? null, $_POST['physic'] ?? null
);

$stars = array($_POST['weakfoot'] ?? null, $_POST['skills'] ?? null);
$prizes = array($_POST['ps'] ?? null, $_POST['xbox'] ?? null, $_POST['pc'] ?? null);
$card_rate = $_POST['cardrate'] ?? null; //media de una carta

$limit = $_POST['limit'] ?? null; //para consultas limitadas



$app = new App();

//dependiendo de la función que solicite el usuario, realizamos la función correspondiente
switch ($func) {
        //case 0, intenta registrarse un nuevo usuario
    case 0:
        //validamos que se hayan recibido bien todos los datos
        if (isset($username) && isset($email) && isset($password)) {
            //llamamos a función que gestiona todo el registro y devolvemos información correspondiente con estructura JSON
            $new_user = new Usuario($username, $email, $password);
            echo json_encode($app->registerNewUser($new_user));
        } else {
            echo 'Datos insuficientes 0';
        }
        break;

        //case 1, el usuario intenta hacer un log in
    case 1:
        if (isset($email) && isset($password)) {
            echo json_encode($app->logIn($email, $password));
        } else {
            echo 'Datos insuficientes 1';
        }
        break;

        //case 2, el usuario ha solicitado un cambio de nombre de usuario
    case 2:
        if (isset($username) && isset($new_username)) {
            echo json_encode($app->changeUsername($username, $new_username));
        }
        break;

        // case 3, el usuario quiere cambiar la foto de perfil
    case 3:
        if (isset($cod_user) && isset($username) && isset($picture) && ($_FILES['picture']['type'] == "image/jpeg" ||
            $_FILES['picture']['type'] == "image/png")) {
            //guardamos la imagen en el directorio correspondiente
            @rename($_FILES['picture']['tmp_name'], "img/perfil/{$cod_user}.png");
            @$foto = "$cod_user.png";
            @$foto = trim($foto);
            echo json_encode($app->changePicture($username, $foto));
        } else {
            echo json_encode(array('cambio' => 'error', 'mensaje' => 'Formato de imagen inválido'));
        }
        break;

        // case 4, se añade un comentario
    case 4:
        if (isset($cod_card)) {
            $comment = new Comentario($cod_card, $cod_user, $content, $date);
            echo json_encode($app->insertComment($comment));
        }
        break;

        //case 5, se añade, anula o altera una valoración
    case 5:
        if (isset($rate) && isset($cod_user) && isset($cod_card)) {
            $rating = new Valoracion($cod_user, $cod_card, $rate);
            echo json_encode($app->insertRate($rating));
        } else {
            echo 'Error';
        }
        break;

        // case 6, el administrador inserta una liga en la bd
    case 6:
        if (isset($leaguename) && isset($date) && isset($teams) && isset($cod_nac)) {
            $league = new Liga($leaguename, $cod_nac, $teams, $date);
            echo json_encode($app->addLeague($league));
        } else {
            echo json_encode('mensaje : Faltan datos');
        }
        break;

        // case 7, el administrador inserta un equipo en la bd
    case 7:
        if (isset($team_name) && isset($president) && isset($date) && isset($leaguename)) {
            $newteam = new Equipo($team_name, $date, $president, $leaguename);
            echo json_encode($app->addTeam($newteam));
        }
        break;

        // case 8, el administrador añade un jugador
    case 8:
        if (
            isset($player_name) && isset($player_surname) && isset($nickname) && isset($date) && isset($cod_nac) && isset($team_name)
            && isset($player_img)
        ) {
            if ($player_img['type'] === 'image/png' || $player_img['type'] === 'image/jepg') {
                //guardamos la imagen en el directorio correspondiente
                @rename($player_img['tmp_name'], "img/jugadores/{$player_img['name']}");
                @$foto = $player_img['name'];
                @$foto = trim($foto);
                $player = new Jugador($player_name, $player_surname, $nickname, $date, $cod_nac, $team_name, $foto);
                echo json_encode($app->addPlayer($player));
            } else {
                echo json_encode(array('insercion' => 'error', 'mensaje' => 'Por favor, inserta una imagen jpeg o png.'));
            }
        } else {
            echo 'error al insertar jugador';
        }
        break;

        // case 9, el administrador añade una carta
    case 9:
        if (isset($stars) && isset($stats)) {
            $card = new Carta($player_name, $player_card, $prizes, $player_img, $stats, $stars);
            echo json_encode($app->addCard($card));
        }
        break;

        // case 10, se está buscando un jugador por nombre y se muestra la preview
    case 10:
        if (isset($player_name)) {
            $bbdd = new BBDD();
            echo json_encode($bbdd->selectPreviewData($player_name));
        }
        break;

        // case 11, se accede a la vista de una carta en específico y se devuelve toda su información
    case 11:
        //aunque se recoge "player_name", el valor es el código de la carta
        echo json_encode($app->showAllCardData($player_name));
        break;

        // case 12, Cuando el usuario quiere ver más comentarios
    case 12:
        $bbdd = new BBDD();
        echo json_encode($bbdd->selectComments($player_name, $limit));
        break;

        // case 13, el administrador accede a la interfaz de inserción de ligas
    case 13:
        $bbdd = new BBDD();
        echo json_encode($bbdd->allNations());
        break;

        //case 14, información de una cuenta de usuario
    case 14:
        $bbdd = new BBDD();
        echo json_encode($bbdd->userProfile($cod_user));
        break;

    case 15:
        $bbdd = new BBDD();
        echo json_encode($bbdd->allLeagues());
        break;

    case 16:
        $bbdd = new BBDD();
        echo json_encode(($bbdd->allTeams()));
        break;

    // devuelve el número de canciones para la paginación
    case 17:
        $bbdd = new BBDD();
        echo json_encode($bbdd->allSongs());
    break;

    // case 18, el cliente solicita una página de la playlist
    case 18:
        if (isset($_POST['min']) && isset($_POST['max'])) {
            $bbdd = new BBDD();
        echo json_encode($bbdd->playlist($_POST['min'], $_POST['max']));
        } else {
            echo json_encode(array('error' => 'error'));
        }
    break;

}
