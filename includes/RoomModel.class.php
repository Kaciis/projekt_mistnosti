<?php

use Tracy\Dumper;

final class RoomModel
{
    public ?int $room_id;
    public string $name;
    public string $no;
    public ?string $phone;

    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct(array $roomData = [])
    {
        $id = $roomData['room_id'] ?? null;
        if (is_string($id))
            $id = filter_var($id, FILTER_VALIDATE_INT);

        $this->room_id = $id;
        $this->name = $roomData['name'] ?? "";
        $this->no = $roomData['no'] ?? "";
        $this->phone = $roomData['phone'] ?? null;
    }

    public function validate() : bool
    {
        $isOk = true;

        if (!$this->name) {
            $isOk = false;
            $this->validationErrors['name'] = "Name cannot be empty";
        }
        if (!$this->no) {
            $isOk = false;
            $this->validationErrors['no'] = "Number (no.) cannot be empty";
        }
        if (!$this->phone){
            $this->phone = null;
        }

        return $isOk;
    }

    public function insert() : bool
    {
        $query = "INSERT INTO room (name, no, phone) VALUES (:name, :no, :phone)";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':no', $this->no);
        $stmt->bindParam(':phone', $this->phone);

        if (!$stmt->execute())
            return false;

        $this->room_id = DB::getConnection()->lastInsertId();
        return true;
    }

    public function update() : bool
    {

        $query = "UPDATE room SET name=:name, no=:no, phone=:phone WHERE room_id=:roomId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':roomId', $this->room_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':no', $this->no);
        $stmt->bindParam(':phone', $this->phone);

        return $stmt->execute();
    }

    public function delete() : bool
    {
        return self::deleteById($this->room_id);
    }

    public static function deleteById(int $room_id) : bool {
        $check = "SELECT room FROM employee WHERE room =:roomId";
        $stmtCheck = DB::getConnection()->prepare($check);
        $stmtCheck->bindParam(':roomId', $room_id);
        $stmtCheck->execute();
        // dump($stmtCheck);
        if($stmtCheck->rowCount() == 0 ){
            $query = "DELETE FROM `key` WHERE room=:roomId";
            $query2 = "DELETE FROM room WHERE room_id=:roomId";
    
            $stmt = DB::getConnection()->prepare($query);
            $stmt2 = DB::getConnection()->prepare($query2);
    
            $stmt->bindParam(':roomId', $room_id);
        
            $stmt2->bindParam(':roomId', $room_id);
    
            
    
            // echo($stmt->fullQuery);
            $stmt->execute();
            $stmt2->execute();
            return true;
        }


        return false;
        // return true;
    }

    public static function findById(int $room_id) : ?RoomModel
    {
        $query = "SELECT * FROM room WHERE room_id=:roomId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':roomId', $room_id);

        $stmt->execute();

        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbData)
            return null;

        return new self($dbData);
    }


    public static function readPostData() : RoomModel
    {
        return new self($_POST); //není úplně košer, nefiltruju
    }
}