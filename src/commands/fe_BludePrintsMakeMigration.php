<?php

namespace feiron\felaraframe\commands;

use Illuminate\Console\Command;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardMakeMigration;

class fe_BludePrintsMakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bp:makeMigration
                            {name? : The name of the table.} 
                            {--D|datafields= : List of field definition seperated by ";" Format: "fieldname:dataType(option)[modifier,...]<default>" Example: -D "name:string(100)[nullable,editable=false]<John>;age:set(1,2,3,4)"}
                            {--M|migrate : Perform migration upon completion}
                            {--E|engine= : Specify the table storage engine (MySQL,Default:InnoDB). InnoDB, MyISam, etc...}
                            {--C|charset= : Specify a default character set for the table (MySQL,Default:utf8).}
                            {--O|collation= : Specify a default collation for the table (MySQL,Default:utf8_unicode_ci).}
                            {--S|timestamps : Automatically put in timestamp fields.}
                            {--T|types : List available field types}
                            {--F|modifiers : List available field types}
                            {--W|wizard : Creating migration using wizard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating a migration file and store at /database/migrations/. Perform the migration if needed.';

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
        (new bp_wizardMakeMigration($this))->build();
    }
}
