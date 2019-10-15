<?php

namespace feiron\felaraframe\lib\outlet;
use feiron\felaraframe\lib\outlet\feOutletManagerContract;
use feiron\felaraframe\lib\outlet\feOutletContract;

class frameOutlet implements feOutletManagerContract
{
    private $OutletList;

    public function __construct(){
        //Outlets are array of arrays that when called will loop through and implement all in turn. 
        $this->OutletList=[];
        return $this;
    }

    public function FetchOutlet($outletName){
        return $this->OutletList[$outletName]??false;
    }

    public function registerOutlet($outletName){
        $this->OutletList[$outletName]=[];
        return $this;
    }

    public function getOutlet($outletName){
        return $this->OutletList[$outletName]??[];
    }

    public function getAvailableOutlets(){
        return array_keys($this->OutletList);
    }

    public function hasOutlet($outletName){
        return (array_key_exists($outletName,$this->OutletList));
    }

    public function bindOutlet($outletName, feOutletContract $outlet){
        $name= ($outlet->MyName()?? $outlet->setName(($outletName.'_'.count($this->OutletList[$outletName])+1))->MyName());
        $this->OutletList[$outletName][$name]=$outlet;
        return $outlet;
    }

    public function replaceOutlet($outletName, feOutletContract $outlet, $target=null){
        if(!empty($target) && array_key_exists($target,$this->OutletList[$outletName])){
            $this->OutletList[$outletName][$target]=$outlet;
        }else{
            $this->OutletList[$outletName]=[$outlet];
        }
        return $this;
    }

    public function removeOutlet($outletName, $target){
        if(array_key_exists($outletName, $this->OutletList) && array_key_exists($target, $this->OutletList[$outletName])){
            unset($this->OutletList[$outletName][$target]);
        }
        return $this;
    }

    public function CreateOulet($outletName,$outletParams){
        if(false===$this->hasOutlet($outletName)){
            $this->registerOutlet($outletName);
        }
        $this->bindOutlet($outletName, new feOutletContract($outletParams));
        return $this;
    }

    public function OutletCalls($outletName,$params){
        foreach($this->OutletList[$outletName] as $key=>$outlet){
            $outlet->CallOutlet($params);
        }
    }

    public function OutletResources($outletName,$target=false,$formater=null){
        $resources = [];
        if($target===false){
            foreach($this->OutletList[$outletName] as $key=>$outlet){
                $outlet= $outlet->getResource();
                if(!empty($outlet) && is_array($outlet)){
                    foreach($outlet as $res){
                        array_push($resources, $this->toHTML(asset($res)));
                    }
                }
            }
        }else{
            $outlet = $this->OutletList[$outletName][$target]->getResource();
            if (!empty($outlet) && is_array($outlet)) {
                foreach ($outlet as $res) {
                    array_push($resources, $this->toHTML(asset($res)));
                }
            }
        }

        return (is_callable($formater)? $formater($resources): $resources);
    }

    private function toHTML($asset){
        $extension  = explode(".", $asset);
        $extension  = end($extension);
        if ($extension == 'js') {
            return '<script type="text/javascript" src="' . $asset . '"></script>';
        } else {
            return '<link href="'. $asset.'" rel="stylesheet">';
        }
    }

    public function OutletRenders($outletName,$asObjects=true){
        $view= $asObjects?[]:'';
        foreach($this->OutletList[$outletName] as $key=>$outlet){
            if($asObjects){
                array_push($view, $outlet->getView());
            }else{
                $view .= $outlet->getView($asObjects)->render();
            }
        }
        return $view;
    }
}
