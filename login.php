<?php

require "./includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Prohlížeč databáze";

    protected function body(): string
    {
        $pass = filter_input(INPUT_POST, 'password');
        $userName = filter_input(INPUT_POST, 'name');
        if($this->loggedIn == true){
            header("Location: /index.php");
            exit;
        }elseif($userName != "") {
            if($this->checkLogin($userName, $pass)){
                $_SESSION["loggedIn"] = true;
                $_SESSION["username"] = $userName;
                $_SESSION["isAdmin"] = $this->checkAdmin($userName);
                header("Location: /index.php");
                exit;
            }else{
                return $this->m->render("login",["error" => "Uživatelské jméno nebo heslo není správné"]);
            }
        }
    // dump($_POST);
        return $this->m->render("login",[]);
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
    public function checkAdmin($userName) : bool{
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE login = :login");
        $stmt->execute([$userName]);
        $row = $stmt->fetch();

        if($row->admin == 1){
            return true;
        }
        return false;
    }

}

(new CurrentPage())->render();
