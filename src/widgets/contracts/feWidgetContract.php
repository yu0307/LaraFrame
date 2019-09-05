<?php
namespace feiron\felaraframe\widgets\contracts;

interface feWidgetContract{

    //get WidgetName for the Widget Manager
    public function WidgetName(): string;

    //render widget contents
    public function render();

    //responsible for building widget specific data as part of the widget output. for parameter [WidgetData]
    public function dataFunction();

    //responsible for returning ajax data.
    public function renderAjax();

    //responsible for polymorphic classes to build their ajax data
    public function getAjaxData();

    //set the control ID
    public function SetID($name);
}
?>