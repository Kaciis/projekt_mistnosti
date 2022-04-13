<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {

    const STATE_FORM_REQUESTED = 1;
    const STATE_FORM_SENT = 2;
    const STATE_PROCESSED = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private int $state;
    private EmployeeModel $employee;
    private int $result = 0;

    //když nepřišla data a není hlášení o výsledku, chci zobrazit formulář
    //když přišla data
      //validuj
      //když jsou validní
        //ulož a přesměruj zpět (PRG)
        //jinak vrať do formuláře
    public function __construct()
    {
        parent::__construct();
        $this->title = "Upravit zaměstnance";
    }


    protected function setUp(): void
    {
        if ($this->loggedIn == false || $this->isAdmin == false) {
            throw new RequestException(403);
        }
        parent::setUp();

        $this->state = $this->getState();

        if ($this->state == self::STATE_PROCESSED) {
            //reportuju

        } elseif ($this->state == self::STATE_FORM_SENT) {
            //přišla data
            //načíst

            $this->employee = EmployeeModel::readPostData();
            
            //validovat
            $isOk = $this->employee->validate();

            //když jsou validní
            if ($isOk) {
                //uložit
                // dump($_POST['keys']);
                // $new = filter_input(INPUT_POST, 'keys');
                // dump($new);
                if ($this->employee->update()) {
                    $new = ($_POST['keys']);
                    foreach ($new as $item) {
                        if (is_string($item))
                        $item = filter_var($item, FILTER_VALIDATE_INT);
                        // dump($item);
                    }
                    $stmt = $this->pdo->prepare("DELETE FROM `key` where employee = :employee");
                    $stmt->execute([$this->employee->employee_id]);

                    $insert = "INSERT INTO `key` (employee, room) VALUES ";

                    foreach ($new as $item) {
                        $insert .= "(" . $this->employee->employee_id . ", " . $item . "),";
                    }
                    $insert = substr($insert, 0, -1);
                    // dump($insert);
                    $stmt = $this->pdo->prepare($insert);
                    $stmt->execute();


                    
                    //přesměruj, ohlas úspěch
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    //přesměruj, ohlas chybu
                    $this->redirect(self::RESULT_FAIL);
                }
            } else {
                $this->state = self::STATE_FORM_REQUESTED;
            }
        } else { //vyžádán formulář, nebylo updatováno
            $this->state = self::STATE_FORM_REQUESTED;
            $employee_id = filter_input(INPUT_POST, "employee_id");
            $this->employee = EmployeeModel::findById($employee_id);
            //neřešena chyba, pokud neexistuje v DB nebo nebylo ID vůbec posláno
        }

    }


    protected function body(): string
    {
        if ($this->state == self::STATE_FORM_REQUESTED){

            $rooms = $this->pdo->prepare("SELECT * FROM room;");
            $rooms->execute([]);
            // dump($rooms);



            return $this->content(
                "employeeForm",
                [
                    "rooms" => $rooms,
                    "keys" => $this->employee->getKeys(),
                    'employee' => $this->employee,
                    'errors' => $this->employee->getValidationErrors(),
                    'action' => "update"
                ]
            );
        }
        elseif ($this->state == self::STATE_PROCESSED){
            //vypiš výsledek zpracování
            if ($this->result == self::RESULT_SUCCESS) {
                return $this->content("employeeSuccess", ['message' => "Aktualizace zaměstnance byla úspěšná"]);
            } else {
                return $this->content("employeeFail", ['message' => "Aktualizace zaměstnance se nezdařila"]);
            }
        }
        return "";
    }

    protected function getState() : int
    {
        //když mám result -> zpracováno
        $result = filter_input(INPUT_GET, 'result', FILTER_VALIDATE_INT);

        if ($result == self::RESULT_SUCCESS) {
            $this->result = self::RESULT_SUCCESS;
            return self::STATE_PROCESSED;
        } elseif($result == self::RESULT_FAIL) {
            $this->result = self::RESULT_FAIL;
            return self::STATE_PROCESSED;
        }

        //nebo když mám post -> zvaliduju a buď uložím nebo form
        $action = filter_input(INPUT_POST, 'action');
        if ($action == "update"){
            return self::STATE_FORM_SENT;
        }
        //jinak chci form
        return self::STATE_FORM_REQUESTED;
    }

    private function redirect(int $result) : void {
        $location = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: {$location}?result={$result}");
        exit;
    }

}

(new CurrentPage())->render();
