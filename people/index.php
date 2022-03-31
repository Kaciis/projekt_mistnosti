<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Výpis místností";

    protected function body(): string
    {

        $stmt = $this->pdo->prepare("SELECT e.employee_id as `e_id`, e.name,e.surname, r.name as `room_name`, r.phone, e.job FROM `employee` e INNER JOIN room r ON e.room = r.room_id;");
        $stmt->execute([]);

        return $this->m->render("employeeList", ["employees" => $stmt]);
    }
}

(new CurrentPage())->render();
