<?php

namespace feiron\felaraframe\widgets\lib\stock;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class wg_weather extends Widget
{

    /*
        $viewParameters: 
        Extends @parent:$viewParameters
        Widget specific vars: none
    */
    public function __construct($viewParameters)
    {
        //Widget Defaults 
        $defaultParameters = [
            'WidgetName' => 'weather',
            'Width' => '4',
            'DataHeight' => 300,
            'HeaderBackground' => 'bg-transparent',
            'WidgetBackground' => 'bg-primary',
            
            'HeaderIcon' => false
            // 'AjaxLoad'=>true,
        ];
        parent::__construct(array_merge($defaultParameters, ($viewParameters ?? [])));
        // $this->viewParameters['Ajax']['AjaxInterval'] = false;
        $this->setView('fe_widgets::stock.wg_weather');
    }
    public function getAjaxData($request){
        $api_key = '13abbb52fe069d005b73bef3cd35b232';
        $api_endpoint = ($request->input('URLaction')== 'get5days')? 'https://api.openweathermap.org/data/2.5/forecast': 'https://api.openweathermap.org/data/2.5/weather';
        
        $api_url = $api_endpoint. $request->input('parameter') . '&appid=' . $api_key;
        if (!isset($api_url)) {
            return (['status'=>'error','message'=>'no api URL found']);
        }
        if ($this->get_http_response_code($api_url) !== '200') {
            return (['status' => 'error', 'message' => 'URL format invalid']);
        }
        $api_data = file_get_contents($api_url);
        return (['status' => false, 'message' => 'bypass for direct output', 'data' => json_decode($api_data)]);
    }

    private function get_http_response_code($url){
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    public function dataFunction()
    {
        return $this->viewParameters['Widget_contents'];
    }
}
