<?php
namespace feiron\felaraframe\lib;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\contracts\feTheme;
use feiron\felaraframe\lib\felaraframeTheme;
use feiron\felaraframe\models\LF_MetaInfo;

class FeFrame {

    private $theme; //feTheme
    private $themeList; //array of feTheme
    private $themeSetting;
    public function __construct(){
        $theme = LF_MetaInfo::where('meta_name', 'theme')->first()->meta_value??config('felaraframe.appconfig.theme');
        $this->themeSetting = LF_MetaInfo::where('meta_name', 'themeSetting')->first()->meta_value ?? [];
        $theme = new $theme();
        if ($theme instanceof feTheme) {
            $this->theme = $theme;
        }else{
            $this->theme =new felaraframeTheme();
        }
        $this->themeList[$this->theme->name()]=$this->theme;
    }

    public function GetProfileImage($size=60,$sourceOnly=false, $user_profile_pic = null){
        $user=Auth::user();
        
        $rst= !empty($user->profile_image) ? Storage::url($user->profile_image) : ($user_profile_pic ?? ("https://www.gravatar.com/avatar/".md5(strtolower( trim($user->email ) ))."?d=".(asset('/feiron/felaraframe/images/avatars/avatar7.png'))."&s=".($size??60)));
        if($sourceOnly===true){
            $rst= '<img src="'. $rst. '" alt="user image">';
        }
        return $rst;
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

    public function RemoveTheme($themeName){
        unset($this->themeList[$themeName]);
    }

    public function GetSiteSettings(){
        return $this->themeSetting;
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

    public function RenderThemeSettings(){
        return $this->RenderSettings($this->theme->ThemeSettings());
    }

    private function RenderSettings($settingList, $heading = 3):string{//html
        $html='';
        foreach($settingList as $key=>$settings){
            $heading=($heading>5)?5:$heading;
            if((false === array_key_exists('type', $settings))){
                $html.= '<div class="form-row"><h'.$heading.'><strong>'.$key. '</strong></h' . $heading . '>'.$this->RenderSettings($settings, $heading+1). '</div>';
            }else{
                $html .= '<div class="ThemeSettings col-md-4 col-sm-12">
                            <div class="ThemeSettingHeading">
                                <h6>'. $key.'</h6>
                            </div>      
                            ' . $this->BuildFormControl($settings, ($this->themeSetting[$settings['name']]??null)).'
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
                                        <input type="checkbox" '. ($option == $value ? 'checked' : ''). ' name="' . $control['name'] . '" class="form-control" data-radio="icheckbox_square-blue" value="' . $option . '">' . $option . '</label>';
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
