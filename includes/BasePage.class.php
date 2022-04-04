<?php

abstract class BasePage
{
    protected MustacheRunner $m;
    protected string $title;
    protected array $extraHeaders = [];
    protected bool $loggedIn = false;

    public function __construct()
    {
        $this->m = new MustacheRunner();
        if(!isset($_SESSION["loggedIn"])){
            $_SESSION["loggedIn"] = false;
        }
        if($_SESSION["loggedIn"] == true){
            $this->loggedIn = true;
        }
    }

    public function content($templateName, $context = []){
        if($this->loggedIn){
            return $this->m->render($templateName,$context);
        }else{
            return $this->m->render("needToLogin",[]);
        }
    }

    public function render() : void {

        // try {
            $this->setUp();

            $html = $this->header();
            $html .= $this->body();
            $html .= $this->footer();
            echo $html;

            $this->wrapUp();
        // } catch (RequestException $e) {
        //     $errPage = new ErrorPage($e->getStatusCode());
        //     $errPage->render();
        // } catch (Exception $e) {
        //     $errPage = new ErrorPage();
        //     $errPage->render();
        // }
        exit;
    }

    protected function setUp() : void {}

    protected function header() : string {
        if($this->loggedIn){
            return $this->m->render("head", ["title" => $this->title, "extraHeaders" => $this->extraHeaders, "loggedIn" => $_SESSION['username']]);
        }else{
            return $this->m->render("head", ["title" => $this->title, "extraHeaders" => $this->extraHeaders]);
        }
    }

    abstract protected function body() : string;

    protected function footer() : string {
        return $this->m->render("foot");
    }

    protected function wrapUp() : void {}
}