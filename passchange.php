<?php
require "./includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Změna hesla";

    protected function body(): string
    {
        $new = filter_input(INPUT_POST, 'new');
        $old = filter_input(INPUT_POST, 'old');
        if($old != "" && $new != "") {
            if($this->checkLogin($_SESSION['username'], $old)){
                $this->changePass($_SESSION['username'], $new);
                return $this->m->render("passSucc");
            }else{
                return $this->m->render("changePassword",["error" => "Původní heslo není správné"]);
            }
        }
        if ($this->loggedIn == false) {
            return $this->m->render("needToLogin", []);
        }
        return $this->m->render("changePassword", []);
    }
    public function checkLogin($userName, $pass) : bool{


        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE login = :login");
        $stmt->execute([$userName]);
        $row = $stmt->fetch();
        // echo $pass . " " . $userName;
        // dump($row);
        if(password_verify($pass ,$row->password)){
            return true;
        }

        return false;
    }
    public function changePass($employee_login ,$new){
        $options = [
            'cost' => 12,
        ];
        $passHash = password_hash($new, PASSWORD_BCRYPT, $options);

        $query = "UPDATE employee SET password=:pass WHERE login=:employee_id;";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':employee_id', $employee_login);
        $stmt->bindParam(':pass', $passHash);
        
        return $stmt->execute();
    }
}
(new CurrentPage())->render();
