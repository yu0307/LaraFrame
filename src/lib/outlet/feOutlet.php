<?php

namespace feiron\felaraframe\lib\outlet;

use feiron\felaraframe\lib\outlet\feOutletContract;

class feOutlet implements feOutletContract
{
    protected $callback; //function call back for the outlet;
    protected $resource = []; //array of path to resource files;
    protected $view;
    protected $myName;
    private $params;
    // private $type;//outlet type

    public function __construct($params = null)
    {
        $this->callback = $params['callback'] ?? null;
        $this->resource = $params['reousrce'] ?? null;
        $this->view = $params['view'] ?? null;
        $this->myName = $params['myName'] ?? null;
        $this->params=$params;
        return $this;
    }

    public function __get($name){
        return $this->params[$name]??null;
    }

    public function setName($outletname)
    {
        $this->myName = $outletname;
        return $this;
    }

    public function MyName(): string
    {
        return $this->myName;
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function CallOutlet()
    {
        if (is_callable($this->callback)) {
            return $this->callback();
        }
        return false;
    }

    public function setResource($reousrce){
        $this->resource=$reousrce;
        return $this;
    }

    public function pushResource($reousrce)
    {
        array_push($this->resource, $reousrce);
        return $this;
    }

    public function getResource(): array
    {
        return $this->resource;
    }

    public function setView(\Illuminate\View\View $view)
    {
        $this->view = $view;
    }

    public function getView($flush = false)
    {
        $this->view=is_string($this->view)?view($this->view): $this->view;
        return ($flush === false) ? $this->view : $this->view->render();
    }
}

?>