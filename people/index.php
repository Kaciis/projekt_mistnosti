<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Výpis místností";

    protected function body(): string
    {
        if ($this->loggedIn == false) {
            return $this->m->render("needToLogin", []);
        }

        $orderBy = "e.surname ASC";

        $poradi = $_GET['poradi'] ?? "";

        $poradi_arr = explode('_', $poradi);

        if (count($poradi_arr) === 2) {
            switch ($poradi_arr[0]) {
                case "prijmeni": {
                        $orderBy = "e.surname ";
                        break;
                    }
                case "nazev": {
                        $orderBy = "r.name ";
                        break;
                    }
                case "telefon": {
                        $orderBy = "r.phone ";
                        break;
                    }
                case "pozice": {
                        $orderBy = "e.job ";
                        break;
                    }
                default: {
                        $orderBy = "e.surname";
                        break;
                    }
            }

            switch ($poradi_arr[1]) {
                case "up": {
                        $orderBy .= " DESC";
                        break;
                    }
                case "down": {
                        $orderBy .= " ASC";
                        break;
                    }
                default: {
                        $orderBy .= " ASC";
                        break;
                    }
            }
        }


        $stmt = $this->pdo->prepare("SELECT e.employee_id as `e_id`, e.name,e.surname, r.name as `room_name`, r.phone, e.job FROM `employee` e INNER JOIN room r ON e.room = r.room_id ORDER BY {$orderBy};");
        $stmt->execute([]);

        $seradit[$poradi] = true;

        if ($_SESSION["isAdmin"] == true) {
            return $this->content("employeeList", ["employees" => $stmt, "seradit" => $seradit, "admin" => 1]);
        }else{
            return $this->content("employeeList", ["employees" => $stmt, "seradit" => $seradit]);
        }
    }
}

(new CurrentPage())->render();
