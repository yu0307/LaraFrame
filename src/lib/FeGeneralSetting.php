<?php
namespace feiron\felaraframe\lib;

use feiron\felaraframe\lib\contracts\feSettingControls;

class FeGeneralSetting implements feSettingControls {

    public function name(): string{
        return 'General Site Settings';
    }

    public function Settings(): array{
        return [
            'Theme Settings' => [
                'Force Bootstrap' => [
                    'type' => 'checkbox',
                    'label' => 'Force Bootstrap Components',
                    'options' => ['Jquery', 'JqueryUI', 'Bootstrap','fontAwesome'],
                    'default' => '',
                    'name' => 'tm_force_bootstrap'
                ]
            ]
        ];
    }
}