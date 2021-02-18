<?php
namespace feiron\felaraframe\lib;

use feiron\felaraframe\lib\contracts\feSettingControls;

class FeGeneralSetting implements feSettingControls {

    public function name(): string{
        return 'General Site Settings';
    }

    public function Settings(): array{
        return [
            'Site Settings' => [
            ]
        ];
    }
}