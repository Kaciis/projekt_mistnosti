<?php

require "./includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Prohlížeč databáze";

    protected function body(): string
    {
        // if (isset($_SESSION["isAdmin"])) {
        //     echo $_SESSION["isAdmin"];
        // }
        return $this->content("index", []);
    }
}

(new CurrentPage())->render();
