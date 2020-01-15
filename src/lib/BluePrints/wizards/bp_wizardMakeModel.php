<?php

namespace feiron\felaraframe\lib\BluePrints\wizards;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardbase;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardMakeMigration;
use feiron\felaraframe\lib\BluePrints\BluePrintsModelFactory;
use Illuminate\Support\Facades\Artisan;

class bp_wizardMakeModel extends bp_wizardbase{
    
    protected $command;
    protected $storage;
    private $path;
    private $modelName;
    private $ModelFactory;
    public function __construct($Command){
        parent::__construct($Command);
        $this->path= ((empty($this->command->option('path')))?'/App/Model': $this->command->option('path'));
        $this->modelName=$this->command->argument('name')??null;
    }

    public function Build(){
        if (empty($this->modelName) && ($this->command->option('wizard') === false)) {
            $this->command->error('Model name is required or using flag -W to use wizard.');
        }else{
            if($this->command->option('wizard') !== false){
                $this->Wizard(true);
            }else{
                $this->ModelFactory = new BluePrintsModelFactory(['modelName' => ucfirst($this->modelName)]);
                $this->ModelFactory->SetPrimary($this->command->option('key') ?? null);
                if (!empty($this->command->option('datafields'))) {
                    $this->ModelFactory->extractDataFields($this->command->option('datafields'));
                }
                if ($this->command->option('migartion') !== false) {
                    $this->command->info('Building migrations ' . $this->modelName . ' ...');
                    $this->ModelFactory->buildMigrations();
                }
            }
            
            $path = '/' . str_replace('\\', "/", trim($this->path, '/')) . '/';
            $this->command->info('Building model ' . $this->modelName . ' ...');
            $this->ModelFactory->BuildModel($path);
            $this->command->info('Model file is created and stored at ' . $path . $this->modelName . '.php');

            if ($this->command->option('migartion') !== false) {
                if ($this->command->confirm('Perform the migration?') === true) {
                    $this->command->info('Now migrating to database ...');
                    Artisan::call('migrate');
                    $this->command->info("\nMigration completed.");
                }
            }
        }
    }

    private function Wizard($banner=false){
        if($banner===true){
            $this->command->comment("=====Welcome to BluePrints Model building utility.======");
            $this->command->comment("This wizard will walk you through the steps to create a model file.\nLet's get started...");
        }
        while(empty($this->modelName)){
            $this->modelName= $this->command->ask('Model Name(without ".php"):');
            if(empty($this->modelName)){
                $this->command->error('Model Name is required.');
            }else{
                $this->ModelFactory = new BluePrintsModelFactory(['modelName' => ucfirst($this->modelName)]);
            }
        }

        if($this->command->confirm('Do you need to create a Migration?')===true){
            $migrationWizard= new bp_wizardMakeMigration($this->command);
            $migrationWizard->Wizard(false, $this->ModelFactory);
            $this->command->info('Building migrations ' . $this->modelName . ' ...');
            $this->ModelFactory->buildMigrations();
        }

        if ($banner === true) {
            $this->command->comment("=====Thank you for using BluePrint Wizard.======");
        }
    }
}
