<?php

namespace feiron\felaraframe\commands;

use Illuminate\Console\Command;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardMakeController;

class fe_BludePrintsMakeController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bp:makeController 
                            {name? : The name of the controller.} 
                            {--P|path=App/Http/Controllers : Path to store the controller, defaulted to root of the app controllers(/app/Http/Controllers).}
                            {--W|wizard : Creating controller using wizard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Making a controller and store at the designated location';

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
        (new bp_wizardMakeController($this))->build();
    }
}
