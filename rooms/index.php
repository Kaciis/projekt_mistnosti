<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Výpis místností";

    protected function body(): string
    {
        if($this->loggedIn == false){
            return $this->m->render("needToLogin",[]);
        }

        // return $this->m->render("needToLogin",[]);

        $orderBy = "name ASC";

        $poradi = $_GET['poradi'] ?? "";
    
        $poradi_arr = explode('_', $poradi);
        // var_dump($poradi_dir);
    
        if(count($poradi_arr) === 2){
            switch($poradi_arr[0]){
                case "cislo":{
                    $orderBy = "no ";
                    break;
                }
                case "nazev":{
                    $orderBy = "name ";
                    break;
                }
                case "telefon":{
                    $orderBy = "phone ";
                    break;
                }
                default : {
                    $orderBy = "name";
                    break;
                }
            }
    
            switch($poradi_arr[1]){
                case "up":{
                    $orderBy .= " DESC";
                    break;
                }
                case "down":{
                    $orderBy .= " ASC";
                    break;
                }
                default:{
                    $orderBy .= " ASC";
                    break;
                }
            }
        }

        $stmt = $this->pdo->prepare("SELECT * FROM `room` ORDER BY {$orderBy};");
        $stmt->execute([]);

        $seradit[$poradi] = true;

        // return $this->m->render();
        if ($_SESSION["isAdmin"] == true) {
        return $this->m->render("roomList", ["rooms" => $stmt, "seradit" => $seradit, "admin" => 1]);
        }else{
            return $this->m->render("roomList", ["rooms" => $stmt, "seradit" => $seradit]);

        }
    }
}

(new CurrentPage())->render();
