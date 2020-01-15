<?php

namespace feiron\felaraframe\commands;

use Illuminate\Console\Command;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardMakeModel;

class fe_BludePrintsMakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bp:makeModel 
                            {name? : The name of the model.} 
                            {--P|path=/app/Model : Path to store the models, defaulted to root of the app controllers(/app/Model).}
                            {--D|datafields= : Field definitions seperated by ";" Format: "fieldname:dataType(option)[modifier,...]<default>" Example: -D "name:string(100)[nullable,editable=false]<John>;age:set(1,2,3)"}
                            {--M|migartion : Creating the model with migration.}
                            {--K|key= : Primary Key for the model.}
                            {--W|wizard : Creating model using wizard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Making a model and store at the designated location. Creating migrations if needed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new bp_wizardMakeModel($this))->build();
    }
}
