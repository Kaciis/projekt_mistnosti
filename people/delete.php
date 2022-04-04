<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {

//    const STATE_FORM_REQUESTED = 1;
//    const STATE_FORM_SENT = 2;
    const STATE_DELETE_REQUESTED = 4;
    const STATE_PROCESSED = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private int $state;
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
        $this->title = "Smazání zaměstnance";
    }


    protected function setUp(): void
    {
        if($this->loggedIn == false){
            throw new RequestException(403);
        }
        
        parent::setUp();

        $this->state = $this->getState();

        if ($this->state == self::STATE_PROCESSED) {
            //reportuju

        } elseif ($this->state == self::STATE_DELETE_REQUESTED) {
            //přišla data
            //načíst

            $roomId = filter_input(INPUT_POST, "employee_id");
            
            // echo($roomId);

            // exit;
            //validovat
            //pokud nemám ID, měl bych hodit chybu

            //uložit
            if (EmployeeModel::deleteById($roomId)) {
                //přesměruj, ohlas úspěch
                $this->redirect(self::RESULT_SUCCESS);
            } else {
                //přesměruj, ohlas chybu
                $this->redirect(self::RESULT_FAIL);
            }
        }
    }


    protected function body(): string
    {
        if ($this->state == self::STATE_PROCESSED){
            //vypiš výsledek zpracování
            if ($this->result == self::RESULT_SUCCESS) {
                return $this->content("employeeSuccess", ['message' => "Smazání zaměstnance bylo úspěšné"]);
            } else {
                return $this->content("employeeFail", ['message' => "Smazání zaměstnance se nezdařilo"]);
            }
        }
        return "";
    }

    protected function getState() : int
    {
        //když mám result -> zpracováno
        $result = filter_input(INPUT_GET, 'result', FILTER_VALIDATE_INT);
        if ($result) {

            if ($result == self::RESULT_SUCCESS) {
                $this->result = self::RESULT_SUCCESS;
            } elseif($result == self::RESULT_FAIL) {
                $this->result = self::RESULT_FAIL;
            }

            return self::STATE_PROCESSED;

        } else {
            return self::STATE_DELETE_REQUESTED;
        }


    }

    private function redirect(int $result) : void {
        $location = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: {$location}?result={$result}");
        exit;
    }

}

(new CurrentPage())->render();
