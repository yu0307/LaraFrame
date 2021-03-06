<?php
namespace feiron\felaraframe\lib;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\contracts\feSettingControls;
use feiron\felaraframe\models\LF_MetaInfo;
use feiron\felaraframe\lib\helper\menuGenerator;
use feiron\felaraframe\lib\helper\Communication;
class FeFrame {

    public $menu;
    private $resourceList;
    private $siteSetting;
    private $siteSettingList;
    private $initBlocks=[];
    private $filterBlock=[];
    private $communication;
    public function __construct(){
        $this->siteSetting =(\Schema::hasTable('lf_site_metainfo')) ? ($this->siteSetting = LF_MetaInfo::where('meta_name', 'SiteSetting')->first()->meta_value ?? []):[];
        $this->menu= new menuGenerator();
        $this->communication = new Communication();
        $this->AppendGeneralSetting(new \feiron\felaraframe\lib\FeGeneralSetting());
        $this->resourceList=[
            'prepend'=>[],
            'push'=>[]
        ];
    }

    public function enqueueResource($resource,$location= 'headerstyles',$prepend=false){
        $tar= $prepend? 'prepend': 'push';
        if (false === array_key_exists($location, $this->resourceList[$tar])) {
            $this->resourceList[$tar][$location] = [];
        }
        if(false=== in_array($resource, $this->resourceList[$tar][$location])){
            $extension  = explode(".", $resource);
            $extension  = end($extension);
            if ($extension == 'js') {
                $asset = '<script type="text/javascript" src="' . $resource . '"></script>';
            } else {
                $asset = '<link href="' . $resource . '" rel="stylesheet">';
            }
            $this->resourceList[$tar][$location][$resource]= $asset;
        }
    }

    public function addInitBlock(\feiron\felaraframe\lib\contracts\feInitBlock $block){
        array_push($this->initBlocks,$block);
    }

    public function addFilterBlock(\feiron\felaraframe\lib\contracts\feFilterBlock $block){
        array_push($this->filterBlock,$block);
    }

    public function getInitBlocks(){
        return $this->initBlocks;
    }

    public function getFilterBlock(){
        return $this->filterBlock;
    }

    public function menuGenerator(){
        return $this->menu;
    }

    public function COMs(){
        return $this->communication;
    }

    public function AppendGeneralSetting(feSettingControls $setting){
        $this->siteSettingList[$setting->name()] = $setting;
    }

    public function GetSiteSettings(){
        return $this->siteSetting;
    }

    public function getResources(){
        return $this->resourceList;
    }

    public function GetProfileImage($size=60,$sourceOnly=false, $user_profile_pic = null,$alt_user=null){
        $user=$alt_user??Auth::user();
        
        $rst= !empty($user->profile_image) ? Storage::url($user->profile_image) : ($user_profile_pic ?? ("https://www.gravatar.com/avatar/".md5(strtolower( trim($user->email ) ))."?d=".(asset('/feiron/felaraframe/images/avatars/avatar7.png'))."&s=".($size??60)));
        if($sourceOnly===true){
            $rst= '<img src="'. $rst. '" alt="user image">';
        }
        return $rst;
    }

    public function RenderSiteSettings(){
        $SettingList='';
        foreach($this->siteSettingList as $name=>$Setting){
            $SettingList .= '<div class="SiteSettingGroup"><h2>'.$name.'</h2>'.($this->RenderSettings($Setting->Settings(), $this->siteSetting))."</div>";
        }
        return $SettingList;
    }

    private function RenderSettings($settingList,$valueList, $heading = 3):string{//html
        $html='';
        foreach($settingList as $key=>$settings){
            $heading=($heading>5)?5:$heading;
            if((false === array_key_exists('type', $settings))){
                $html.= '<div class="form-row row"><h'.$heading.'><strong>'.$key. '</strong></h' . $heading . '>'.$this->RenderSettings($settings, $valueList, $heading+1). '</div>';
            }else{
                $html .= '<div class="ThemeSettings col-md-4 col-sm-12">
                            <div class="ThemeSettingHeading">
                                <h6>'. ($settings['label']??$key).'</h6>
                            </div>      
                            ' . $this->BuildFormControl($settings, ($valueList[$settings['name']]??null)).'
                          </div>';
            }
        }
        return $html;
    }

    public function BuildFormControl($control,$value=null){
        $value= $value?? $control['default'];
        switch ($control['type']) { 
            case 'select':
                $options='';
                if(!empty($control['options']) && is_array($control['options'])){
                    foreach($control['options'] as $option){
                        $options.='<option value="'. $option.'" '.($option==$value?'SELECTED default':'').'>'.$option.'</option>';
                    }
                }
                return '<select class="form-control form-select" name="'. $control['name'].'">
                            '.$options.'
                        </select>';
            case 'switch':
                return '
                    <div class="form-check-inline form-switch me-2">
                        <input class="form-check-input form-control" type="checkbox" toggle '.(($control['options']??'false')=='false'?'':'checked').' name="'.$control['name'].'" >
                    </div>
                ';
            case 'radio':
                $options = '';
                if (!empty($control['options']) && is_array($control['options'])) {
                    foreach ($control['options'] as $option) {
                        $options .= '
                            <div class="form-check-inline me-2">
                                <input value="'.$option.'" class="form-check-input form-control" '.($option == $value ? 'checked' : '').' type="radio" name="'.$control['name'].'">
                                <label class="form-check-label">
                                '.$option.'
                                </label>
                            </div>
                        ';
                    }
                }
                return $options;
            case 'checkbox':
                $options = '';
                if (!empty($control['options']) && is_array($control['options'])) {
                    foreach ($control['options'] as $option) {
                        $options .='
                            <div class="form-check-inline me-2">
                                <input class="form-check-input form-control" '. ((is_array($value)?(in_array($option, $value)):($option == $value)) ? 'checked' : ''). ' type="checkbox" value="' . $option . '" name="' . $control['name'] . '">
                                <label class="form-check-label">' . $option . '</label>
                            </div>
                        ';
                    }
                }
                return $options;
            default:
                return '<div class="prepend-icon">
                            <input class="form-control form-white" type="'. $control['type']. '" name="' . $control['name'] . '" value="'. ($control['value']??$value).'">
                            <i class="fa fa-indent"></i>
                        </div>';
        }
    }

}
