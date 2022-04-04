<?php

final class EmployeeModel
{
    public ?int $employee_id;
    public string $name;
    public string $surname;
    public ?string $job;
    public ?int $wage;
    public ?int $room;


    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct(array $employeeData = [])
    {
        $id = $employeeData['employee_id'] ?? null;

        // if($id== null) throw new Error("jaj");


        if (is_string($id))
            $id = filter_var($id, FILTER_VALIDATE_INT);

        $room = $employeeData['room'] ?? null;
        if (is_string($room))
            $room = filter_var($room, FILTER_VALIDATE_INT);


        $wage = $employeeData['wage'] ?? null;
        if (is_string($wage))
            $wage = filter_var($wage, FILTER_VALIDATE_INT);
        $this->employee_id = $id;
        $this->name = $employeeData['name'] ?? "";
        $this->surname = $employeeData['surname'] ?? "";
        $this->job = $employeeData['job'] ?? null;
        $this->wage = $wage;
        $this->room = $room;
    }

    public function validate(): bool
    {
        $isOk = true;

        if (!$this->name) {
            $isOk = false;
            $this->validationErrors['name'] = "Name cannot be empty";
        }
        if (!$this->surname) {
            $isOk = false;
            $this->validationErrors['surname'] = "Surname cannot be empty";
        }
        if (!$this->job) {
            $isOk = false;
            $this->validationErrors['job'] = "Surname cannot be empty";
            $this->job = null;
        }
        if (!$this->wage) {
            $isOk = false;
            $this->validationErrors['wage'] = "Surname cannot be empty";
            $this->wage = null;
        }
        if (!$this->room) {
            $isOk = false;
            $this->validationErrors['room'] = "Surname cannot be empty";
            $this->room = null;
        }

        return $isOk;
    }
    // TODO : insert
    public function insert() : bool
    {
        $query = "INSERT INTO employee (name, surname, job, wage, room) VALUES (:name, :surname, :job, :wage, :room)";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);


        if (!$stmt->execute())
            return false;

        $this->employee_id = DB::getConnection()->lastInsertId();
        return true;
    }
    // TODO : update
    public function update(): bool
    {

        $query = "UPDATE employee SET name=:name, surname=:surname, job=:job, wage=:wage, room=:room WHERE employee_id=:employee_id;";
        // $query = "UPDATE employee SET name=\"{$this->name}\", surname=\"{$this->surname}\", job=\"{$this->job}\", wage={$this->wage}, room={$this->room} WHERE employee_id={$this->employee_id}";
        // dump($query);
        // dump($query);

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employee_id', $this->employee_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);
        // print($query);
        return $stmt->execute();
    }

    public function getKeys()
    {
        
        $query = "SELECT room_id, no, name, 1 as has FROM room where room_id in(SELECT room from `key` where employee = :employee1) UNION SELECT room_id, no, name, null FROM room where room_id not in(SELECT room from `key` where employee = :employee)";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employee1', $this->employee_id);
        $stmt->bindParam(':employee', $this->employee_id);


        $stmt->execute();
        return $stmt;
    }

    // TODO : delete this
    public function delete() : bool
    {
        return self::deleteById($this->employee_id);
    }

    // TODO : delete by id

    public static function deleteById(int $employee_id) : bool {

        $query = "DELETE FROM room WHERE employee_id=:employee";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employee', $employee_id);



        // echo($stmt->fullQuery);

        return $stmt->execute();
        // return true;
    }

    public static function findById($employee_id): ?EmployeeModel
    {
        $query = "SELECT * FROM employee WHERE employee_id=:employeeId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':employeeId', $employee_id);

        $stmt->execute();

        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dbData)
            return null;

        return new self($dbData);
    }


    public static function readPostData(): EmployeeModel
    {
        return new self($_POST); //není úplně košer, nefiltruju
    }
}
