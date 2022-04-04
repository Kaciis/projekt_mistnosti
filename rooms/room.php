<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected RoomModel $data;
    protected $var;

    public function __construct()
    {
        $roomId = filter_input(INPUT_GET, "room_id");

        $this->var = RoomModel::findById($roomId);
        
        $this->title = "Karta místnosti " . $this->var->no;
        parent::__construct();
    }
    protected function body(): string
    {
        if($this->loggedIn == false){
            throw new RequestException(403);
        }
        
        $roomId = filter_input(INPUT_GET, "room_id");


        // $var = RoomModel::findById($roomId);
        if($this->var == null){
            throw new RequestException(404);
        }

        $this->data = $this->var;
        $this->title = "Karta místnosti " . $this->data->no;


        $query = "SELECT ROUND(AVG(wage),2) as plat FROM employee WHERE employee.room = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$roomId]);

        $plat = $stmt->fetch(PDO::FETCH_ASSOC);





        $osoby = $this->pdo->prepare("SELECT employee.wage,employee.surname,CONCAT(LEFT(employee.name, 1),'.') as nameshort,employee_id FROM employee WHERE employee.room = ?");
        $osoby->execute([$roomId]);


        $klice = $this->pdo->prepare("SELECT room_id,r.no as roomNumber,  r.name as roomName, r.phone as roomPhone, CONCAT(LEFT(e.name, 1),'.') as jmeno, e.surname as prijmeni, e.employee_id
        FROM `room` AS r 
        INNER JOIN `key` as k ON r.room_id = k.room 
        INNER JOIN `employee` as e ON k.employee = e.employee_id 
        WHERE r.room_id = ?; ");
        $klice->execute([$roomId]);

        $this->title = "Karta místnosti " . $this->data->no;


        // if ($this->data->no == null) {
        //     throw new RequestException(404);
        // } else {
            return $this->content("roomDetail", ["osoby" => $osoby, "klic" => $klice, "data" => $this->data, "plat" => $plat]);
        // }
    }
}


(new CurrentPage())->render();
