<?php

namespace feiron\felaraframe\widgets\lib\fe_Widgets;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetTable extends Widget{

    private $tableData=[];
    /*
        $viewParameters: 
        Extends @parent:$viewParameters
        Widget specific vars:
        $viewParameters['headers'] : table headers
    */
    public function __construct($viewParameters){
        //Widget Defaults 
        $defaultParameters['BaseWidget']='WidgetTable';
        $defaultParameters['Width'] = '5';
        $defaultParameters['HeaderIcon'] = 'th-list';
        $defaultParameters['headers']=[];
        parent::__construct(array_merge($defaultParameters, ($viewParameters ?? [])));
        $this->setView('fe_widgets::widgetTable');
        if(false===empty($this->viewParameters['widgetData'])){
            $this->tableData = (is_callable($this->viewParameters['widgetData'])) ? $this->viewParameters['widgetData']() : $this->viewParameters['widgetData'];
        }
    }

    public function setData($data){
        $this->tableData = (is_callable($data)) ? $data() : $data;
    }

    public function getAjaxData(){
        return $this->tableData;
    }

    public function dataFunction(){
        if(false===empty($this->tableData)){
            if(is_array($this->tableData)===false){
                return $this->tableData;
            }else{
                $content='';
                foreach($this->tableData as $row){
                    $content .= '<tr>';
                    foreach ($this->viewParameters['headers'] as $idx=>$header) {
                        $content.='<td>'. $row[$idx].'</td>';
                    }
                    $content .= '</tr>';
                }
                return $content;
            }
        }
        return '';
    }
}

?>