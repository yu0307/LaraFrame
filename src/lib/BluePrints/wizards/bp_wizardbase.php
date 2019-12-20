<?php

namespace feiron\felaraframe\lib\BluePrints\wizards;
use Illuminate\Support\Facades\Storage;

abstract class bp_wizardbase
{
    protected $command;
    protected $storage;

    public function __construct($Command)
    {
        $this->command=$Command;
        $this->storage = Storage::createLocalDriver(['root' => base_path()]);
    }

    public abstract function Build();
}
