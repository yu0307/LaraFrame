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
            'DataHeight' => 260,
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
        $api_key = '6fad2b1b1bd87f79b9845d97f4e6e85e';
        $api_endpoint = 'https://api.darksky.net/forecast';
        $lat= $request->input('lat');
        $lat = (isset($lat) && is_numeric($lat) ? (float) ($lat) : null);
        $lon = $request->input('lat');
        $lon = (isset($lon) && is_numeric($lon) ? (float) ($lon) : null);
        $units = $request->input('units');
        $units = (isset($units) && strtolower($units) == 'si'  ? 'si' : 'us');

        $api_url = $api_endpoint . '/' . $api_key . '/' . $lat . ',' . $lon . '/?units=' . $units . '&exclude=minutely,hourly,alerts,flags';
        if (!isset($api_url)) {
            return (['status'=>'error','message'=>'no api URL found']);
        }
        if ($this->get_http_response_code($api_url) !== '200') {
            return (['status' => 'error', 'message' => 'URL format invalid']);
        }
        $api_data = file_get_contents($api_url);
        return json_decode($api_data);
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
