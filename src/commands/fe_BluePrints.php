<?php

namespace feiron\felaraframe\commands;

use Illuminate\Console\Command;
use feiron\felaraframe\lib\BluePrints\BluePrints;

class fe_BluePrints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bp:BuildSite 
                            {target=blueprints.bp}
                            {--W|wizard : Creating BluePrints using wizard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the site base on the blueprint';

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
        (new BluePrints($this))->start();
    }
}
