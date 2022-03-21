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
        include_once('configuration.php');
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
            $query = $this->mysql->prepare("INSERT INTO User ( `nick`, `name`, `lastName`, `email`, `description`, `password`, `dateBirth`, `role`, `dateJoined`, `status`)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param('sssssssssi', $nick, $userName, $lastUserName, $email, $description, $password, $dateBirth, $role, $dateJoined, $status);
            $query->execute();
            $result = $query->get_result();
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
        if ($row['role'] === 'admin') {
            return true;
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
    
    public function getRankingByUser($idUser)
    {
        $query = $this->mysql->prepare("SELECT * FROM `Ranking` WHERE id in (SELECT idRanking from RankingUser WHERE idUser = ?)");
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
            $subQuery = $this->mysql->prepare("SELECT b.name,b.lastName,c.id, a.points FROM RankingUser a INNER JOIN User b ON a.idUser = b.id INNER JOIN Ranking c ON a.idRanking = c.id AND a.idRanking IN (SELECT idRanking from RankingUser where idUser =?) AND c.id = ? ORDER BY a.points DESC");
            $subQuery->bind_param('ii', $idUser,$obj->id);
            $subQuery->execute();
            $result2 = $subQuery->get_result();
            $obj->rankingData = [];
            while ($row2 = $result2->fetch_assoc()) {
                $obj->rankingLast = new stdClass();
                $obj->rankingLast->Nombre = $row2['name'];
                $obj->rankingLast->Apellido=$row2['lastName'];
                $obj->rankingLast->id=$row2['id'];
                $obj->rankingLast->Puntos=$row2['points'];
                array_push($obj->rankingData, $obj->rankingLast);
            }
            array_push($response, $obj);
        }
        print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * addRankingByCode - Añade un ranking al usuario por su codigo unico
     */
    public function addRankingByCode($code,$idUser){
        try {
            $query = $this->mysql->prepare("INSERT INTO `RankingUser`(`idRanking`, `idUser`, `points`, `favourite`) VALUES ((SELECT id from Ranking WHERE joinCode = ? ),?,'0','1');");
            $query->bind_param('si',$code,$idUser);
            $query->execute();
            $result = $query->get_result();
        } catch (Exception $error) {
            return $error;
        }

    }

    /**
     * getRankingData - Obtiene los datos de cada ranking
     */

    // public function getRankingData($idUser)
    // {
    //     $query = $this->mysql->prepare("SELECT b.nick, c.name,c.id, a.points FROM RankingUser a INNER JOIN User b ON a.idUser = b.id INNER JOIN Ranking c ON a.idRanking = c.id AND a.idRanking IN (SELECT idRanking from RankingUser where idUser = ?) ORDER BY c.name;");
    //     $query->bind_param('i', $idUser);
    //     $query->execute();
    //     $response = [];
    //     $result = $query->get_result();
    //     while ($row = $result->fetch_assoc()) {
    //         $obj = new stdClass();
    //         $obj->Usuarios = $row['nick'];
    //         $obj->Ranking = $row['name'];
    //         $obj->id = $row['id'];
    //         $obj->Puntos = $row['points'];
          
    //         // $obj->points = $row['points'];
    //         array_push($response, $obj);
    //     }
    //     print_r(json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    // }

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
}
