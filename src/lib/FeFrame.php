<?php
namespace feiron\felaraframe\lib;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\contracts\feTheme;
use feiron\felaraframe\lib\contracts\feSettingControls;
use feiron\felaraframe\lib\felaraframeTheme;
use feiron\felaraframe\models\LF_MetaInfo;

class FeFrame {

    private $theme; //feTheme
    private $themeList; //array of feTheme
    private $themeSetting;
    private $siteSetting;
    private $siteSettingList;
    private $resourceList;
    public function __construct(){
        if (\Schema::hasTable('lf_site_metainfo')) {
            $theme = LF_MetaInfo::where('meta_name', 'theme')->first()->meta_value??(config('felaraframe.appconfig.theme')??felaraframeTheme::class);
            $this->themeSetting = LF_MetaInfo::where('meta_name', 'themeSetting')->first()->meta_value ?? [];
            $this->siteSetting = LF_MetaInfo::where('meta_name', 'SiteSetting')->first()->meta_value ?? [];
        }else{
            $theme =felaraframeTheme::class;
            $this->themeSetting=[];
            $this->siteSetting=[];
        }
        
        $theme = new $theme();
        if ($theme instanceof feTheme) {
            $this->theme = $theme;
        }else{
            $this->theme =new felaraframeTheme();
        }
        $this->themeList[$this->theme->name()]=$this->theme;
        if(false===array_key_exists('felaraframe', $this->themeList)){
            $this->AppendTheme(new felaraframeTheme());
        }
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

    public function requireResource($resource, $location = 'headerstyles'){
        $this->enqueueResource($resource, $location,true);
    }

    public function ThemeSetting($name){
        return $this->themeSetting[$name];
    }

    public function LoadTheme($themeName){
        $this->theme= $this->themeList[$themeName]?? $this->themeList['felaraframe'];
    }

    public function AppendTheme(feTheme $theme){
        $this->themeList[$theme->name()]= $theme;
    }

    public function AppendGeneralSetting(feSettingControls $setting){
        $this->siteSettingList[$setting->name()] = $setting;
    }

    public function RemoveTheme($themeName){
        unset($this->themeList[$themeName]);
    }

    public function GetThemeSettings(){
        return $this->themeSetting;
    }
    
    public function GetSiteSettings(){
        return $this->siteSetting;
    }

    public function GetCurrentTheme(){
        return $this->theme;
    }

    public function GetThemes(){
        return $this->themeList;
    }

    public function getThemeByName($name){
        return $this->themeList[$name];
    }

    public function getResources(){
        return $this->resourceList;
    }

    public function GetProfileImage($size=60,$sourceOnly=false, $user_profile_pic = null){
        $user=Auth::user();
        
        $rst= !empty($user->profile_image) ? Storage::url($user->profile_image) : ($user_profile_pic ?? ("https://www.gravatar.com/avatar/".md5(strtolower( trim($user->email ) ))."?d=".(asset('/feiron/felaraframe/images/avatars/avatar7.png'))."&s=".($size??60)));
        if($sourceOnly===true){
            $rst= '<img src="'. $rst. '" alt="user image">';
        }
        return $rst;
    }

    public function RenderThemeSettings(){
        return $this->RenderSettings($this->theme->ThemeSettings(), $this->themeSetting);
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
                $html.= '<div class="form-row"><h'.$heading.'><strong>'.$key. '</strong></h' . $heading . '>'.$this->RenderSettings($settings, $valueList, $heading+1). '</div>';
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
                        $options.='<option value="'. $option.'" '.($option==$value?'SELECTED':'').'>'.$option.'</option>';
                    }
                }
                return '<select class="form-control" name="'. $control['name'].'">
                            '.$options.'
                        </select>';
            break;
            case 'radio':
                $options = '';
                if (!empty($control['options']) && is_array($control['options'])) {
                    foreach ($control['options'] as $option) {
                        $options .= '<label><input type="radio" ' . ($option == $value ? 'checked' : '') . ' name="' . $control['name'] . '" class="form-control" data-radio="iradio_minimal-blue" value="' . $option . '">' . $option . '</label>';
                    }
                }
                return '<div class="icheck-inline">
                            '.$options.'
                        </div>';
            break;
            case 'checkbox':
                $options = '';
                if (!empty($control['options']) && is_array($control['options'])) {
                    foreach ($control['options'] as $option) {
                        $options .= '<label>
                                        <input type="checkbox" '. ((is_array($value)?(in_array($option, $value)):($option == $value)) ? 'checked' : ''). ' name="' . $control['name'] . '" class="form-control" data-radio="icheckbox_square-blue" value="' . $option . '">' . $option . '</label>';
                    }
                }
                return '<div class="icheck-inline">
                            ' . $options . '
                        </div>';
            break;
            default:
                return '<div class="prepend-icon">
                            <input class="form-control" type="'. $control['type']. '" name="' . $control['name'] . '" value="'. ($control['value']??$value).'">
                            <i class="fa fa-indent"></i>
                        </div>';
        }
    }
}
