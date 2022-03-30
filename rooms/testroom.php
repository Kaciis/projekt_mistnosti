<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Test modelu";

    protected function body(): string
    {
//        $room = RoomModel::findById(16);
//        dump($room);
//        $room->name = "neco jineho";
//        $room->update();
//
//        $room2 = RoomModel::findById(16);
//        dumpe($room2);
        //dumpe(RoomModel::deleteById(17));

        $room  = new RoomModel();
        $room->name = "Hokus";
        $room->no = "65874";
        $room->phone = "12345679";
        try {
            $room->insert();
        } catch (Exception $e) {
            dumpe($e);
        }

        return "";
    }
}

(new CurrentPage())->render();
