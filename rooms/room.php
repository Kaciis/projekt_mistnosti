<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Karta místnosti";
    }
    protected function body(): string
    {
        $roomId = filter_input(INPUT_GET, "room_id");

        $query = "SELECT ROUND(AVG(wage),2) as plat FROM employee WHERE employee.room = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$roomId]);
        
        $plat = $stmt->fetch(PDO::FETCH_ASSOC);

        
        

        $roomId = filter_input(INPUT_GET, "room_id");

        $osoby = $this->pdo->prepare("SELECT employee.wage,employee.surname,CONCAT(LEFT(employee.name, 1),'.') as nameshort,employee_id FROM employee WHERE employee.room = ?");
        $osoby->execute([$roomId]);

        $data = RoomModel::findById($roomId);

        $klice = $this->pdo->prepare("SELECT room_id,r.no as roomNumber,  r.name as roomName, r.phone as roomPhone, e.name as jmeno, e.surname as prijmeni, e.employee_id
        FROM `room` AS r 
        INNER JOIN `key` as k ON r.room_id = k.room 
        INNER JOIN `employee` as e ON k.employee = e.employee_id 
        WHERE r.room_id = ?; ");
        $klice->execute([$roomId]);
        
        

        return $this->m->render("roomDetail", ["osoby" => $osoby, "klic" => $klice,"data" => $data,"plat" => $plat]);
    }
}


(new CurrentPage())->render();
