<?php
require_once('class/Helper.php');
class Database
{
    /* ATTRIBUTs */
    public $mysql;

    /* METHODs */
    /**
     * connection - Inicializa la conexón.
     */
    public function connection()
    {
        include_once('dotenv.php');
        define("DB_HOST", getenv('DB_HOST'));
        define("DB_USERNAME", getenv('DB_USERNAME'));
        define("DB_PASSWORD", getenv('DB_PASSWORD'));
        define("DB_DATABASE", getenv('DB_DATABASE'));
        try {
            $this->mysql = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            if ($this->mysql->connect_errno) {
                exit();
            }
        } catch (Exception $error) {
            return $error;
        };
    }

    /**
     * responseError - Crea una instancia de la clase stdClass y devuelve un objeto con dos atributos.
     */
    public function responseError($status, $message)
    {
        $response = new stdClass();
        $response->status = $status;
        $response->message = $message;
        $response->autentication = false;
        $response->authorization = false;
        return $response;
    }

    /**
     * getTest - Printa la tabla test de la BBDD.
     */
    public function status()
    {
        $response = new stdClass();
        $test = $this->mysql->query('SELECT * FROM `test` WHERE testNum = 99');
        $row = $test->fetch_assoc();
        if (count($row) > 0 && $row['testString'] == true) {
            $response->available = true;
        } else {
            $response->available = false;
        }
        return $response;
    }

    /**
     * login - Verifica el email y password para devolver un JWT.
     */
    public function login($email, $password)
    {
        try {
            $query = $this->mysql->prepare('SELECT `id`, `email`, `password` FROM User WHERE email = ?');
            $query->bind_param('s', $email);
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if (isset($row['password']) && password_verify($password, $row['password'])) {
                include_once('class/Auth.php');
                return AUTH::createToken(array($row['email'], $row['id']));;
            } else {
                print_r(json_encode($this->responseError(403, 'Email or password incorrect')));
            }
        } catch (Exception $error) {
            return $error;
        }
    }

    /*   
     * register - Crea usuraio en la DB
     */
    public function register($nick, $userName, $lastUserName, $email, $description, $password, $dateBirth, $role, $dateJoined, $status)
    {
        try {
            $query = $this->mysql->prepare('SELECT count(`email`) AS mail FROM User WHERE email = ?');
            $query->bind_param('s', $email);
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if ($row['mail'] == 0) {
                $query = $this->mysql->prepare("INSERT INTO `User` ( `nick`, `name`, `lastName`, `email`, `description`, `password`, `dateBirth`, `role`, `dateJoined`, `status`)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $query->bind_param('sssssssssi', $nick, $userName, $lastUserName, $email, $description, $password, $dateBirth, $role, $dateJoined, $status);
                $query->execute();
                $result = $query->get_result();
            } else {
                return false;
            }
        } catch (Exception $error) {
            return $error;
        }
    }


    /**
     * getTest - Printa la tabla test de la BBDD.
     */
    public function getUsers()
    {
        $test = $this->mysql->query('Select * FROM User');
        $response = [];
        while ($row = $test->fetch_assoc()) {
            array_push($response, $row);
        }
        return $response;
    }

    /**
     * isAdmin() - Recibe el token desencriptado y mira si el usuario del token tiene rol de Administrador.
     */
    public function isAdmin($email)
    {
        $query = $this->mysql->prepare('SELECT `role` FROM User WHERE `email` = ?');
        $query->bind_param('s', $email);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($row) {
            if ($row['role'] === 'admin') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getRankings()
    {
        $query = $this->mysql->query('Select * FROM Ranking');
        $response = [];
        while ($row = $query->fetch_assoc()) {
            $obj = new stdClass();
            $obj->id = $row['id'];
            $obj->name = $row['name'];
            $obj->description = $row['description'];
            $obj->logo = fixingBlob($row['logo']);
            array_push($response, $obj);
        }
        print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * getRankingByUser - Genera json con los rankings por usuraio con un sub json que contiene los usuarios con su puntuacion para cada ranking 
     */

    public function getRankingsByUser($idUser)
    {
        $query = $this->mysql->prepare("SELECT * FROM `Ranking` WHERE id in (SELECT idRanking from RankingUser WHERE idUser = ?)");
        $query->bind_param('i', $idUser);
        $query->execute();
        $response = [];
        $response2 = [];
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
            $query2 = $this->mysql->prepare("SELECT `insiniaPoints`,`role` FROM `RankingUser` WHERE idUser = ? AND idRanking = ? ");
            $query2->bind_param('ii', $idUser, $row['id']);
            $query2->execute();
            $result2 = $query2->get_result();
            $row2 = $result2->fetch_assoc();
            $obj = new stdClass();
            if ($row2["role"] == 'moderator') {
                $obj->role = 'Moderador';
            } else {
                $obj->role = 'Participante';
            }
            $obj->rankingData = new stdClass();
            $obj->id = $row['id'];
            $obj->name = $row['name'];
            $obj->description = $row['description'];
            $obj->insiniaPoints = $row2['insiniaPoints'];
            $obj->logo = fixingBlob($row['logo']);
            $subQuery = $this->mysql->prepare("SELECT b.name,b.lastName,b.id as idUser,b.Responsabilidad,b.Cooperacion,b.Autonomia,b.Emocional,b.Inteligencia, c.id, a.points FROM RankingUser a INNER JOIN User b ON a.idUser = b.id INNER JOIN Ranking c ON a.idRanking = c.id AND a.idRanking IN (SELECT idRanking from RankingUser where idUser =?) AND c.id = ? AND a.role != 'moderator' ORDER BY a.points DESC");
            $subQuery->bind_param('ii', $idUser, $obj->id);
            $subQuery->execute();
            $result2 = $subQuery->get_result();
            $obj->rankingData = [];

            while ($row2 = $result2->fetch_assoc()) {
                $obj->rankingLast = new stdClass();
                $obj->rankingLast->Nombre = $row2['name'];
                $obj->rankingLast->Apellido = $row2['lastName'];
                $obj->rankingLast->idUser = $row2['idUser'];
                $obj->rankingLast->id = $row2['id'];
                $obj->rankingLast->Responsabilidad = $row2['Responsabilidad'];
                $obj->rankingLast->Cooperacion = $row2['Cooperacion'];
                $obj->rankingLast->Autonomia = $row2['Autonomia'];
                $obj->rankingLast->Emocional = $row2['Emocional'];
                $obj->rankingLast->Inteligencia = $row2['Inteligencia'];
                $obj->rankingLast->Puntos = $row2['points'];
                array_push($obj->rankingData, $obj->rankingLast);
            }
            array_push($response, $obj);
        }
        print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }


    /*
     * Obtenemos los rankings en los que el usuario es moderador
     */

    public function getRankingsByModerator($idUser)
    {
        $query = $this->mysql->prepare("SELECT * FROM `Ranking` WHERE id in (SELECT idRanking from RankingUser WHERE idUser = ? AND role = 'moderator')");
        $query->bind_param('i', $idUser);
        $query->execute();
        $response = [];
        $response2 = [];
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
            $obj = new stdClass();
            $obj->rankingData = new stdClass();
            $obj->id = $row['id'];
            $obj->name = $row['name'];
            $obj->description = $row['description'];
            $obj->logo = fixingBlob($row['logo']);
            $subQuery = $this->mysql->prepare("SELECT Users.name, Users.lastName, Users.id as idUser,Users.Responsabilidad,Users.Cooperacion,Users.Autonomia,Users.Emocional,Users.Inteligencia, Rankings.id, RankingsUser.points FROM RankingUser RankingsUser INNER JOIN User Users ON RankingsUser.idUser = Users.id INNER JOIN Ranking Rankings ON RankingsUser.idRanking = Rankings.id AND RankingsUser.idRanking IN (SELECT idRanking from RankingUser where idUser = ? ) AND Rankings.id = ? AND Users.id != ?  AND RankingsUser.role != 'moderator' ORDER BY `RankingsUser`.`points` DESC");
            $subQuery->bind_param('iii', $idUser, $obj->id, $idUser);
            $subQuery->execute();
            $result2 = $subQuery->get_result();
            $obj->rankingData = [];
            while ($row2 = $result2->fetch_assoc()) {
                $obj->rankingLast = new stdClass();
                $obj->rankingLast->Nombre = $row2['name'];
                $obj->rankingLast->isModerator = true;
                $obj->rankingLast->Apellido = $row2['lastName'];
                $obj->rankingLast->idUser = $row2['idUser'];
                $obj->rankingLast->id = $row2['id'];
                $obj->rankingLast->Responsabilidad = $row2['Responsabilidad'];
                $obj->rankingLast->Cooperacion = $row2['Cooperacion'];
                $obj->rankingLast->Autonomia = $row2['Autonomia'];
                $obj->rankingLast->Emocional = $row2['Emocional'];
                $obj->rankingLast->Inteligencia = $row2['Inteligencia'];
                $obj->rankingLast->Puntos = $row2['points'];
                array_push($obj->rankingData, $obj->rankingLast);
            }
            array_push($response, $obj);
        }
        print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
    /**
  /**
     * addRankingByCode - Añade un ranking al usuario por su codigo unico
     */
    public function addRankingByCode($code, $idUser)
    {
        try {
            $query = $this->mysql->prepare("INSERT INTO `RankingUser`(`idRanking`, `idUser`, `points`, `favourite`,`role`) VALUES ((SELECT id from Ranking WHERE joinCode = ? ),?,'0','1','user');");
            $query->bind_param('si', $code, $idUser);
            $query->execute();
        } catch (Exception $error) {
            return $error;
        }
    }



    /**
     * getRankingData - Obtiene los datos de cada ranking
     */

    public function getRankingData($idRanking, $idUser)
    {


        $query = $this->mysql->prepare("SELECT b.name,b.lastName,b.id as idUser,b.Responsabilidad,b.Cooperacion,b.Autonomia,b.Emocional,b.Inteligencia, c.id,c.joinCode, a.points,a.role,a.insiniaPoints FROM RankingUser a INNER JOIN User b ON a.idUser = b.id INNER JOIN Ranking c ON a.idRanking = c.id AND a.idRanking IN (SELECT idRanking from RankingUser where idUser =?) AND c.id = ?  ORDER BY a.points DESC");
        $query->bind_param('ii', $idUser, $idRanking);
        $query->execute();
        $result = $query->get_result();
        $response = [];
        $isModerator = false;
        $joinCode = "";

        while ($row = $result->fetch_assoc()) {
            $obj = new stdClass();

            if ($row['role'] == 'moderator' && $row['idUser'] == $idUser) {
                $isModerator = true;
                $joinCode = $row['joinCode'];
            } elseif ($row['role'] == 'moderator') {
            } else {
                $insiniaPoints = $row['insiniaPoints'];

                $obj->role = $row['role'];
                $obj->Nombre = $row['name'];
                $obj->Apellido = $row['lastName'];
                $obj->idUser = $row['idUser'];
                $obj->id = $row['id'];
                $obj->Responsabilidad = $row['Responsabilidad'];
                $obj->Cooperacion = $row['Cooperacion'];
                $obj->Autonomia = $row['Autonomia'];
                $obj->Emocional = $row['Emocional'];
                $obj->Inteligencia = $row['Inteligencia'];
                $obj->Puntos = $row['points'];

                array_push($response, $obj);
            }
        }
        $rankings = new stdClass();

        $rankings->insiniaPoints = $insiniaPoints;
        $rankings->joinCode = $joinCode;
        $rankings->moderator = $isModerator;
        $rankings->response =  $response;


        print_r(json_encode($rankings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function getProfile($id)
    {
        $query = $this->mysql->prepare("Select id, nick, name, lastName, email, description, dateBirth, avatar, role, dateJoined, status FROM User WHERE id = ?");
        $query->bind_param('i', $id);
        $query->execute();
        $response = [];
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
            $obj = new stdClass();
            $obj->id = $row['id'];
            $obj->nick = $row['nick'];
            $obj->name = $row['name'];
            $obj->lastName = $row['lastName'];
            $obj->email = $row['email'];
            $obj->description = $row['description'];
            $obj->dateBirth = $row['dateBirth'];
            $obj->avatar = fixingBlob($row['avatar']);
            $obj->role = $row['role'];
            $obj->dateJoined = $row['dateJoined'];
            $obj->status = $row['status'];
            array_push($response, $obj);
        };
        print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function createRanking($data, $idUser)
    {
        try {
            $query = $this->mysql->prepare("INSERT INTO `RankingUser`(`idRanking`, `idUser`, `points`, `favourite`) VALUES ((SELECT id from Ranking WHERE joinCode = ? ),?,'0','1');");
            $query->bind_param('si', $data, $idUser);
            $query->execute();
            $result = $query->get_result();
        } catch (Exception $error) {
            return $error;
        }
    }

    public function updateAvatarById($image, $idUser)
    {
        $query = $this->mysql->prepare("UPDATE `User` SET `avatar`= ? WHERE id = ?");
        $query->bind_param('si', $image, $idUser);
        $query->execute();
    }

    public function updateData($idRanking, $idUser, $points, $idUserModified, $idUserClient, $insinia, $oldValue)
    {
        $query = $this->mysql->prepare("UPDATE RankingUser SET points=? WHERE idRanking = ? AND idUser = ?");
        $query->bind_param('iii', $points, $idRanking, $idUser);
        $query->execute();
        $now = date("Y-m-d H:i:s");
        $query3 = $this->mysql->prepare("INSERT INTO `historial`(`evaluado`, `evaluador`, `ranking`, `puntos`, `insinia`,`oldValue`, `fecha`) VALUES (?,?,?,?,?,?,?)");
        $query3->bind_param('iiiisis', $idUserModified, $idUserClient, $idRanking, $points, $insinia, $oldValue, $now);
        $query3->execute();
        $result = $query3->get_result();
        print_r($result);
    }

    public function insertRanking($ranking)
    {
        $exist = $this->codeExists($ranking->code);
        $response = new stdClass();
        $response->done = false;
        if ($exist == 0) {
            $query = $this->mysql->prepare("INSERT INTO `Ranking`(`name`, `description`, `logo`, `joinCode`) VALUES (?, ?, ?, ?)");
            $query->bind_param('ssss', $ranking->name, $ranking->description, $ranking->image, $ranking->code);
            $query->execute();
            $rankingId = $this->mysql->insert_id;
            $query = $this->mysql->prepare("INSERT INTO `RankingUser`(`idRanking`, `idUser`, `points`, `favourite`, `role`, `insiniaPoints`) VALUES (?, ?, 0, 0, 'moderator', 0)");
            $query->bind_param('ii', $rankingId, $ranking->idUser);
            $query->execute();
            $response->done = true;
        }
        return $response;
    }

    public function emailExists($email)
    {
        $query = $this->mysql->prepare("SELECT email FROM `User` WHERE email = ? LIMIT 1");
        $query->bind_param('s', $email);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public function codeExists($code)
    {
        $response = new stdClass();
        $response->queryExists = false;
        $query = $this->mysql->prepare("SELECT id FROM `Ranking` WHERE `joinCode` = ? LIMIT 1");
        $query->bind_param('s', $code);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public function updateInsinia($idRanking, $idUserModified, $points, $insinia, $idUserClient,  $oldValue, $isModerator)
    {
        $query = $this->mysql->prepare("SELECT `insiniaPoints` as puntos FROM `RankingUser` WHERE idRanking = ? AND idUser = ?");
        $query->bind_param('ii', $idRanking, $idUserClient);
        $query->execute();
        $result = $query->get_result();
        $puntos = $result->fetch_assoc()['puntos'];
        $puntosMenosCliente = $puntos - $points;

        if (!$isModerator && $puntosMenosCliente < 0 || $points < 0) {
            return false;
        } else if (!$isModerator) {
            $query = $this->mysql->prepare("UPDATE User SET $insinia= $insinia  + ? WHERE id = ?");
            $query->bind_param('ii', $points, $idUserModified);
            $query->execute();

            $query2 = $this->mysql->prepare("UPDATE RankingUser SET insiniaPoints=? WHERE idRanking = ? AND idUser = ?");
            $query2->bind_param('iii', $puntosMenosCliente, $idRanking, $idUserClient);
            $query2->execute();
        } else if ($isModerator) {
            $query = $this->mysql->prepare("UPDATE User SET $insinia= ? WHERE id = ?");
            $query->bind_param('ii', $points, $idUserModified);
            $query->execute();
        }
        $now = date("Y-m-d H:i:s");
        $query3 = $this->mysql->prepare("INSERT INTO `historial`(`evaluado`, `evaluador`, `ranking`, `puntos`, `insinia`,`oldValue`, `fecha`) VALUES (?,?,?,?,?,?,?)");
        $query3->bind_param('iiiisis', $idUserModified, $idUserClient, $idRanking, $points, $insinia, $oldValue, $now);
        $query3->execute();
        $result = $query3->get_result();
        print_r($result);
    }

    public function updateProfile($profile, $id)
    {
        $queryString = "UPDATE `User` SET ";
        $keys =  array();
        $vars = get_object_vars($profile);
        foreach ($vars as $key => $value) {
            array_push($keys, "$key = '$value'");
        }
        $queryString = $queryString . implode(", ", $keys) . " WHERE id = $id";
        $query = $this->mysql->prepare($queryString);
        $query->execute();
    }

    public function  validateEmail($email)
    {
        $query = $this->mysql->prepare("SELECT COUNT(*) as COUNTA FROM User WHERE email = ?");
        $query->bind_param('s', $email);
        $query->execute();
        $result = $query->get_result();
        if ($result->fetch_assoc()['COUNTA'] > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getUsersByRanking($id)
    {
        $query = $this->mysql->prepare("SELECT u.email FROM RankingUser r JOIN User u WHERE idRanking = ? AND r.role <> 'moderator' AND r.idUser = u.id;");
        $query->bind_param('i', $id);
        $query->execute();
        $result = $query->get_result();
        $response = [];
        while ($row = $result->fetch_assoc()) {
            array_push($response, $row['email']);
        }
        print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function getUserByEmail($email)
    {
        $query = $this->mysql->prepare("SELECT id, email FROM User WHERE email = ?");
        $query->bind_param('s', $email);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $row;
    }

    public function deleteUserFromRanking($idUser, $idRanking)
    {
        $query = $this->mysql->prepare("DELETE FROM `RankingUser` WHERE  idUser = ? AND idRanking = ?");
        $query->bind_param('ii', $idUser, $idRanking);
        $query->execute();
    }

    public function insertUsersToRanking($users, $idRanking)
    {
        $queryString = "INSERT INTO `RankingUser`(`idRanking`, `idUser`, `points`, `favourite`, `role`, `insiniaPoints`) VALUES ";
        foreach ($users as $user) {
            $value = "($idRanking, $user, 0, 0, 'user', 1000) ";
            $queryString = $queryString . $value;
        };
        $parentesis = ") (";
        $parentesisWithComa = "), (";
        $queryString = (str_replace($parentesis, $parentesisWithComa, $queryString));
        $query = $this->mysql->prepare($queryString);
        $query->execute();
    }

    public function getHistory($id)
    {
        $query = $this->mysql->prepare("SELECT `id`, `evaluado`, `evaluador`, `ranking`, `puntos`, `insinia`,`oldValue`, `fecha` FROM `historial` WHERE ranking IN (SELECT idRanking FROM RankingUser where idUser = ?) ORDER BY fecha DESC");
        $query->bind_param('i', $id);
        $query->execute();
        $response = [];
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
            $obj = new stdClass();
            $obj->idHistory = $row['id'];
            $obj->idEvaluado = $row['evaluado'];
            $obj->idEvaluador = $row['evaluador'];
            $obj->idRanking = $row['ranking'];
            $obj->Puntos = $row['puntos'];
            $obj->Insinia = $row['insinia'];
            $obj->Fecha = $row['fecha'];

            $subQuery = $this->mysql->prepare("SELECT u.name AS evaluadoN, u.lastName AS evaluadoA , t.name AS evaluadorN, t.lastName AS evaluadorA, r.name AS rankingName  FROM User u, historial h, Ranking r JOIN User t WHERE (u.id = ? AND h.evaluado = ?) AND(t.id = ? AND h.evaluador = ?) AND r.id = ? AND h.id = ? ");
            $subQuery->bind_param('iiiiii', $obj->idEvaluado, $obj->idEvaluado, $obj->idEvaluador, $obj->idEvaluador, $obj->idRanking, $obj->idHistory);
            $subQuery->execute();
            $result2 = $subQuery->get_result();
            $obj->mainData = [];
            while ($row2 = $result2->fetch_assoc()) {
                $obj->historyData = new stdClass();
                $obj->historyData->Evaluador = $row2['evaluadorN'] . " " . $row2['evaluadorA'];
                $obj->historyData->Evaluado = $row2['evaluadoN'] . " " . $row2['evaluadoA'];
                $obj->historyData->Ranking = $row2['rankingName'];
                $obj->historyData->Puntos = $row['puntos'];
                $obj->historyData->oldValue = $row['oldValue'];
                $obj->historyData->idRanking = $row['ranking'];
                $obj->historyData->Insinia =  $obj->Insinia;
                $obj->historyData->Fecha =   $obj->Fecha;
                $obj->historyData->idHistory =   $obj->idHistory;
                $obj->historyData->idEvaluado =   $obj->idEvaluado;
                $obj->historyData->idEvaluador =   $obj->idEvaluador;

                array_push($obj->mainData, $obj->historyData);
            }
            array_push($response, $obj->historyData);
        }
        print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function  revertHistory($idHistory, $idUser, $idEvaluador, $puntos, $insinia, $oldValue, $idRanking, $idUserClient)
    {
        if ($insinia == 'puntos') {
            $query = $this->mysql->prepare("UPDATE `RankingUser` SET points =  ? WHERE idUser = ? AND idRanking = ?");
            $query->bind_param('iii', $oldValue, $idUser, $idRanking);
            $query->execute();

            $query = $this->mysql->prepare("DELETE FROM `historial` WHERE evaluado = ? AND ranking = ?");
            $query->bind_param('ii', $idUser, $idRanking);
            $query->execute();

            $now = date("Y-m-d H:i:s");
            $query3 = $this->mysql->prepare("INSERT INTO `historial`(`evaluado`, `evaluador`, `ranking`, `puntos`, `insinia`,`oldValue`, `fecha`) VALUES (?,?,?,?,?,?,?)");
            $query3->bind_param('iiiisis', $idUser, $idUserClient, $idRanking, $puntos, $insinia, $oldValue, $now);
            $query3->execute();
            $result = $query3->get_result();
        } else {

            $query = $this->mysql->prepare("UPDATE `User` SET $insinia = $insinia - ? WHERE id = ? ");
            $query->bind_param('ii', $puntos, $idUser);
            $query->execute();

            $query = $this->mysql->prepare("UPDATE `RankingUser` SET insiniaPoints = insiniaPoints + ? WHERE idUser = ? AND idRanking = ? ");
            $query->bind_param('iii', $puntos, $idEvaluador, $idRanking);
            $query->execute();

            $query = $this->mysql->prepare("DELETE FROM `historial` WHERE id = ? ");
            $query->bind_param('i', $idHistory);
            $query->execute();
        }
    }



    public function deleteRanking($idRanking)
    {
        $response = new stdClass();
        $query = $this->mysql->prepare("DELETE FROM `Ranking` WHERE id = ?");
        $query->bind_param('i', $idRanking);
        $query->execute();

        $query = $this->mysql->prepare("DELETE FROM `RankingUser` WHERE idRanking = ?");
        $query->bind_param('i', $idRanking);
        $query->execute();
    }

    public function exitRanking($idRanking, $idUser)
    {
        $query = $this->mysql->prepare("DELETE FROM `RankingUser` WHERE idRanking = ? AND idUser = ?");
        $query->bind_param('ii', $idRanking, $idUser);
        $query->execute();
    }



    public function renewJoinCode($idRanking)
    {
        $noRepeat = false;
        while ($noRepeat == false) {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 5; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            $query = $this->mysql->query("SELECT COUNT(joinCode) as con FROM `Ranking` WHERE joinCode = '$randomString'");
            $row = $query->fetch_assoc();
            if ($row["con"] == 0) {
                $noRepeat = true;
            }
        }


        $query = $this->mysql->prepare("UPDATE Ranking SET joinCode = '$randomString' WHERE id = ?");
        $query->bind_param('i', $idRanking);
        $query->execute();
    }
}
