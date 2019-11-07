<?php
namespace feiron\felaraframe\lib;

use feiron\felaraframe\lib\contracts\feTheme;

class felaraframeTheme implements feTheme {

    private $myDomainName;
    private $mySettings;

    public function __construct()
    {
        $this->myDomainName= 'felaraframe';
        $this->mySettings=[
            "Layouts"=>[
                'Side Bar'=>[
                    // 'Location' => [
                    //     'type' => 'radio',
                    //     'options' => ['Left', 'Right'],
                    //     'default' => 'Left',
                    //     'name'=>'sb_location'
                    // ],
                    'Structures' => [
                        'type' => 'radio',
                        'options' => ['Normal', 'Condensed'],
                        'default' => 'Normal',
                        'name'=>'sb_structure'
                    ],
                    'Style' => [
                        'type' => 'radio',
                        'options' => ['Fixed', 'Fluid'],
                        'default' => 'Fixed',
                        'name' => 'sb_style'
                    ],
                    'Show On' => [
                        'type' => 'radio',
                        'options' => ['Hover','Always'],
                        'default' => 'Always',
                        'name' => 'sb_showon'
                    ],
                    'SubMenu Shown On' => [
                        'type' => 'radio',
                        'options' => ['Click', 'Hover'],
                        'default' => 'Click',
                        'name' => 'sb_subshowon'
                    ],
                    'Initial Behavior' => [
                        'type' => 'radio',
                        'options' => ['Normal', 'Collapsed'],
                        'default' => 'Normal',
                        'name' => 'sb_initbh'
                    ],
                    'Top Display' => [
                        'type' => 'radio',
                        'options' => ['Profile Image', 'Mini Profile Image', 'Icon','None'],
                        'default' => 'Icon',
                        'name' => 'sb_topdisplay'
                    ]
                ],
                'Top Bar' => [
                    'Location' => [
                        'type' => 'radio',
                        'options' => ['Fixed', 'Fluid'],
                        'default' => 'Fixed',
                        'name' => 'tb_location'
                    ]
                ],
                'Page'=>[
                    
                    'Color' => [
                        'type' => 'select',
                        'options' => ['Primary', 'Dark', 'red', 'green', 'orange', 'purple', 'blue'],
                        'default' => 'Dark',
                        'name' => 'page_color'
                    ],
                    'Background Color' => [
                        'type' => 'select',
                        'options' => ['Clean', 'Lighter', 'Light-default', 'Light-blue', 'Light-purple', 'Light-dark'],
                        'default' => 'Light-blue',
                        'name' => 'page_bgcolor'
                    ],
                    'Display' => [
                        'type' => 'radio',
                        'options' => ['Full Width', 'Boxed'],
                        'default' => 'Full Width',
                        'name' => 'page_display'
                    ],
                    'Template Style' => [
                        'type' => 'radio',
                        'options' => ['Dark 1', 'Dark 2', 'Light 1', 'Light 2'],
                        'default' => 'Dark 1',
                        'name' => 'page_template'
                    ]
                ]
            ]
        ];
    }

    public function name():string{
        return $this->myDomainName;
    }

    public function ThemeSettings():array{
        return $this->mySettings;
    }

}