<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Výpis zaměstnanců";

    protected function body(): string
    {

        $clovekID = filter_input(INPUT_GET, "employee_id");


        $stmt = $this->pdo->prepare("SELECT e.name as jmeno, e.surname as prijmeni, e.job, e.wage, r.name as `room_name`, r2.name as `jmeno_klice`, r2.room_id 
        FROM employee AS `e` 
        Inner join room as `r` on (e.room = r.room_id) 
        Inner join `key` as `k` on (k.employee = e.employee_id) 
        Inner join room as r2 on (k.room = r.room_id) 
        WHERE e.employee_id = ?;");

        $stmt->execute([$clovekID]);
        $row = $stmt->fetch();
        

        return $this->m->render("employeeDetail", ["clovek" => $row, "klice" => $stmt]);
    }
}

(new CurrentPage())->render();
