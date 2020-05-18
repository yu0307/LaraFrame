<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

abstract class WidgetAbstract implements Widget{

    protected $viewParameters;
    protected $view=false;
    protected $settingList;
    protected $headerscripts;
    protected $headerstyles;
    protected $footerscripts;
    protected $footerstyles;

    public function __construct($viewParameters){

        $this->viewParameters=[
            'WidgetName'        =>($viewParameters['WidgetName'] ?? ''),
            'ID'                =>($viewParameters['ID'] ?? ($this->MyName() . '_' . rand(1000, 9000))),
            'DisableHeader'     =>false,
            'DisableFooter'     => true,
            'WidgetBackground'  => 'bg-white',
            'HeaderBackground'  => 'bg-primary',
            'FooterBackground'  => 'bg-dark',
            'HeaderIcon'        => 'star',
            'Widget_header'     => '',
            'Widget_footer'     => '',
            'DisableControls'   => false,
            'Width'             => '3',
            'col'             => '',
            'DataHeight'        => '400',
            'Widget_contents'   => '',
            'WidgetData'        => false,//[false: Display contents only, no data to load. function:function to get data. dataset:var of data]
            'AjaxLoad'          =>false
        ];
        $this->setWidgetType($this->MyName());
        $this->viewParameters['Ajax']['AjaxURL'] = route('WidgetsAjaxPost', ['tarWidget' => $this->MyName(), 'tarControl' => $this->MyID()]); //URL for the generic widget
        $this->viewParameters['Ajax']['AjaxInterval'] = false; //false->load once only, true->global interval with everyone else, number->milliseconds to have it's own timer.
        $this->viewParameters['Ajax']['AjaxType'] = 'POST'; //Request type
        $this->viewParameters = array_merge($this->viewParameters, ($viewParameters ?? []));
        $this->settingList = array_merge(['ID', 'DataHeight', 'hasSettingOutlet'], (($this->viewParameters['AjaxLoad'] !== false)? ['AjaxLoad', 'Ajax']:[]));
        $this->headerscripts = collect([]);
        $this->headerstyles=collect([]);
        $this->footerscripts=collect([]);
        $this->footerstyles=collect([]);
        if (($this->viewParameters['AjaxLoad'] ?? false) !== false && !empty($this->viewParameters['Ajax']['AjaxJS'])) {
            $this->enqueueHeader($this->viewParameters['Ajax']['AjaxJS']);
        }
    }

    public function enqueueHeader($file,$duplicate=false){
        $extension  = explode(".", $file);
        $extension  = end($extension);
        if ($extension == 'css') {
            $this->headerstyles->push(['file' => $file, 'duplicate' => false]);
            // array_push($this->headerstyles,['file'=>$file,'duplicate'=>false]);
        } else {
            $this->headerscripts->push(['file' => $file, 'duplicate' => $duplicate]);
            // array_push($this->headerscripts, ['file' => $file, 'duplicate' => $duplicate]);
        }
    }
    public function enqueueFooter($file, $duplicate = false){
        $extension  = explode(".", $file);
        $extension  = end($extension);
        if ($extension == 'css') {
            $this->footerstyles->push(['file' => $file, 'duplicate' => false]);
            // array_push($this->footerstyles, ['file' => $file, 'duplicate' => false]);
        } else {
            $this->footerscripts->push(['file' => $file, 'duplicate' => $duplicate]);
            // array_push($this->footerscripts, ['file' => $file, 'duplicate' => $duplicate]);
        }
    }

    public function removeResource($file){
        $extension  = explode(".", $file);
        $extension  = end($extension);
        if ($extension == 'css') {
            $this->footerstyles->forget($this->footerstyles->search(function ($item, $key) use ($file) {
                return ($item['file'] == $file);
            }));
            $this->headerstyles->forget($this->headerstyles->search(function ($item, $key) use ($file) {
                return ($item['file'] == $file);
            }));
        } else {
            $this->headerscripts->forget($this->headerscripts->search(function ($item, $key) use ($file) {
                return ($item['file'] == $file);
            }));
            $this->footerscripts->forget($this->footerscripts->search(function ($item, $key) use ($file) {
                return ($item['file'] == $file);
            }));
        }
    }
    
    public function getHeaderScripts():array{
        return $this->headerscripts->toArray();
    }
    public function getHeaderStyle(): array{
        return $this->headerstyles->toArray();
    }
    public function getFooterScripts(): array{
        return $this->footerscripts->toArray();
    }
    public function getFooterStyle(): array{
        return $this->footerstyles->toArray();
    }

    public function MyName(): string{
        return (new \ReflectionClass($this))->getShortName();
    }

    public function WidgetName():string{
        return $this->viewParameters['WidgetName'];
    }

    public function MyID(): string{
        return $this->viewParameters['ID'];
    }

    public function WidgetType(): string{
        return $this->viewParameters['Type'];
    }

    public function setID($name){
        $this->viewParameters['ID']= $name?? $this->viewParameters['ID'];
        return $this;
    }

    protected function setWidgetType($type){
        $this->viewParameters['Type']= $type;
        $this->setAjaxJS($type);   
        return $this;
    }

    protected function setAjaxJS($name,$path= '/feiron/felaraframe/widgets/'){
        $path='/'. trim(trim($path,'/'),'\\').'/';
        $this->viewParameters['Ajax']['AjaxJS'] = asset($path . $name . '.js');
        return $this;
    }

    protected function setWidgetContents($content){
        $this->viewParameters['Widget_contents'] = $content;
        return $this;
    }

    protected function setView($viewName){
        $this->view=view($viewName);
        return $this;
    }

    public function setData($data){
        $this->viewParameters['WidgetData'] = (is_callable($data)) ? $data() : $data;
        return $this;
    }

    //render final widget html to the pipeline
    public function render(){
        
        if($this->viewParameters['AjaxLoad']===false && $this->viewParameters['WidgetData']!==false){
            $this->viewParameters['WidgetData'] = (is_callable($this->viewParameters['WidgetData'])) ? $this->viewParameters['WidgetData']() : $this->dataFunction();
            if (empty($this->viewParameters['WidgetData'])) {
                $this->setWidgetContents('<h4 class="c-primary text-center text-capitalize align-middle">No data is available...</h4>');
            }
        }
        $this->viewParameters['WidgetName'] = $this->WidgetName();
        $this->viewParameters['headerscripts'] = $this->getHeaderScripts();
        $this->viewParameters['headerstyles'] = $this->getHeaderStyle();
        $this->viewParameters['footerscripts'] = $this->getFooterScripts();
        $this->viewParameters['footerstyles'] = $this->getFooterStyle();
        $this->viewParameters['usrSettings']= $this->userSettingOutlet();
        $this->viewParameters['widgetConfig'] = $this->getWidgetSettings();
        $this->viewParameters['ID'] = $this->viewParameters['usr_key']?? $this->viewParameters['ID'];

        return (false=== $this->view? View::make('fe_widgets::widgetFrame', $this->viewParameters): $this->view->with($this->viewParameters))->render();
    }

    //send ajax data to the client
    public function renderAjax($request){
        $rsp= $this->getAjaxData($request);
        if(isset($rsp['status']) && $rsp['status']===false){//if bypass is enabled.
            return response()->json($rsp['data']);
        }
        return response()->json(['target'=>$this->MyID(),'widget_type'=>$this->WidgetType(),'data' => $rsp]);
    }

    public function UpdateWidgetSettings($Settings=[]){
        $this->viewParameters=array_merge($this->viewParameters,$Settings);
        $this->settingList=array_merge($this->settingList,array_keys($Settings));
    }

    public function getWidgetSettings(){
        return collect($this->viewParameters)->only($this->settingList)->toArray();
    }

    //responsible for polymorphic classes to build their ajax data
    public function getAjaxData($request){
        return $this->dataFunction();
    }

    //front end settings available to users.
    public static function userSettingOutlet(){
        return [];
    }
    
    //responsible for building widget specific data as part of the widget output. for parameter [WidgetData]
    public abstract function dataFunction();
}

?>