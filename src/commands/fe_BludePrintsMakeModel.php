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
                            {--D|datafields= : List of field definition seperated by commas ",".(Format: [fieldname:dataType(option)] Example: [name:string(100),age:integer])}
                            {--M|migartion : Creating the model with migration.}
                            {--T|table= : Table name associate with the model file.}
                            {--K|key= : Primary Key for the model.}
                            {--F|fillable= : Create a list of Fillable/Mass assignable fields(seperate each with comma ",").Defaulted to char(100) if not defined in datafield -D.}
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
