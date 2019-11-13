<?php

namespace feiron\felaraframe\commands;

use Illuminate\Console\Command;
use feiron\felaraframe\lib\helper\BluePrints;

class fe_BluePrints extends Command
{
    private $blueprintHandle;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bp:BuildSite {target=blueprints.bp}';

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

        $this->blueprintHandle = new BluePrints($this);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->blueprintHandle->start();
    }
}
