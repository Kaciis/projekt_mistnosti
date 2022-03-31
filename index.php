<?php
require "./includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Prohlížeč databáze";

    protected function body(): string
    {
        return $this->m->render("index",[]);
    }
}

(new CurrentPage())->render();
