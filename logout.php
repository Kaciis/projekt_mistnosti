<?php

require "./includes/bootstrap.inc.php";

if(isset($_SESSION["loggedIn"])){
    session_destroy();
    header("Location: /index.php");
}