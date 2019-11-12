<?php

namespace feiron\felaraframe\lib\helper;
use Illuminate\Support\Facades\Storage;
class BluePrints {
    private $command;
    private $storage;
    private $target;
    private const PathPrefix='blueprints/';

    public function __construct(\feiron\felaraframe\commands\fe_BluePrints $command){
        $this->command=$command;
        $this->storage=Storage::disk('local');
        $this->target=self::PathPrefix.$this->command->argument('target');
    }

    public function build(){
        if($this->storage->exists('blueprints')===false){
            $this->storage->makeDirectory('blueprints');
        }
        $this->command->comment("=====Welcome to BluePrints Site building utility.======");
        $this->command->info( "Loading blueprints from target => ". $this->storage->path($this->target));
        if(false===$this->storage->exists($this->target))
            var_dump($this->storage->files($this->target));
    }
}

?>