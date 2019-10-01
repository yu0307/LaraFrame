<?php

namespace feiron\felaraframe\widgets\lib\fe_Widgets;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetChart extends Widget{

    private $ChartData=[];

    public function __construct($viewParameters)
    {
        $defaultParameters['BaseWidget'] = 'WidgetChart';
        $defaultParameters['Width'] = '5';
        $defaultParameters['HeaderIcon'] = 'line-chart';
        $defaultParameters['chartType'] = 'line';
        $defaultParameters['WidgetData'] ='';
        parent::__construct(array_merge($defaultParameters, ($viewParameters ?? [])));
        
        $this->setView('fe_widgets::widgetChart');

        if (false === empty($this->viewParameters['WidgetData'])) {
            $this->ChartData = (is_callable($this->viewParameters['WidgetData'])) ? $this->viewParameters['WidgetData']() : $this->viewParameters['WidgetData'];
        }
        $this->enqueueFooter(asset('/feiron/felaraframe/plugins/charts-morris/raphael.min.js'));
        $this->enqueueFooter(asset('/feiron/felaraframe/plugins/charts-morris/morris.min.js'));
        $this->enqueueFooter(asset('/feiron/felaraframe/widgets/WidgetChartGeneral.js'));
        $this->enqueueHeader(asset('/feiron/felaraframe/plugins/charts-morris/morris.min.css'));
        //additional settings for the chart widget can be set by using $this->UpdateWidgetSettings()
        //in according to Morris Json Setting option list
        //http://morrisjs.github.io/morris.js/lines.html
        //http://morrisjs.github.io/morris.js/bars.html
        //http://morrisjs.github.io/morris.js/donuts.html
        //eg: $this->UpdateWidgetSettings(['resize'=>true,'colors'=>['red','green','#0088cc']])
    }

    public function setData($data){
        $this->ChartData = (is_callable($data)) ? $data() : $data;
    }

    public function dataFunction()
    {
        return [
                "data" => ($this->ChartData)
                ];
    }

    public function getWidgetSettings():array{
        return array_merge(collect($this->viewParameters)->only($this->settingList)->toArray(), [
            "chartSetting"=> [
                "data" => ($this->ChartData)
            ]
        ]);
    }

}
