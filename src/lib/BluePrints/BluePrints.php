<?php

namespace feiron\felaraframe\lib\BluePrints;
use Illuminate\Support\Facades\Storage;
use feiron\felaraframe\lib\BluePrints\BluePrintsFactory;
use feiron\felaraframe\lib\BluePrints\wizards\bp_wizardMakePage;
use Exception;
class BluePrints {
    private $command;
    private $storage;
    private $template;
    private $targetFile;
    private const PathPrefix='blueprints/';

    public function __construct(\feiron\felaraframe\commands\fe_BluePrints $command){
        $this->command=$command;
        $this->storage=Storage::disk('local');
        $this->template=[
            "siteName"=>"My Awsome Web Site",
            "siteAuthor" => "Lucas F. Lu",
            "siteTitle"=>"",
            "favIcon"=>'',
            "siteMetaTags" => [
                '<meta charset="utf-8">'
            ],
            "siteFooter" => [
                "footerText"=> '<div class="copyright footer_text p-0 pull-right" style="text-align: center;"><span>Copyright <span class="copyright">©</span> {{date("Y")}} </span> <span>{{config("app.name")}}</span>. <span>All rights reserved. </span></div>'
            ],
            "siteResources"=>[
                "inHeader"=>[],
                "inFooter"=>[]
            ],
            "pages" => [
            ]            
        ];
    }

    public function start(){
        if($this->storage->exists(self::PathPrefix)===false){
            $this->storage->makeDirectory(self::PathPrefix);
        }

        $this->command->comment("=====Welcome to BluePrints Site building utility======");
        if ($this->command->option('wizard') === false){
            $this->targetFile = self::PathPrefix . $this->command->argument('target');
            $this->command->info("Loading blueprints from target => " . str_replace("\\", '/', $this->storage->path($this->targetFile)));
            if (false === $this->storage->exists($this->targetFile)) {
                $this->command->error('We were unable to find a blueprint file in the target location.');
                if ($this->command->confirm("Would you like to create one with the wizard? It would be stored at:\n" . str_replace("\\", '/', $this->storage->path($this->targetFile))) !== false) {
                    $this->Wizard();
                }
            } else {
                $this->command->info("Blueprint found, now proceed to site building...");
                $this->build();
            }
        }else{
            $this->Wizard();
        }
        $this->command->comment("=====Thank you for using BluePrints=====");
    }

    private function check(){//implement later to check the integrity of the blueprint
        return true;
    }

    private function build(){
        if($this->check()){
            try {
                $factory= new BluePrintsFactory($this->targetFile,$this->storage,$this->command);
                $this->command->info("--> Building Page templates...");
                if(true===$factory->buildPageTemplate()){
                    $this->command->info("--> Building Models ...");
                    $factory->ImportModels();
                    $this->command->info("--> Extracting information and putting things together ...");
                    $factory->ExtractInfo();
                    $this->command->info("--> Building View Files ...");
                    $factory->BuildViews();
                    $this->command->info("--> Building Controller Files ...");
                    $factory->BuildControllers();
                    $this->command->info("--> Now generating Route File ...");
                    $factory->BuildRoutes();
                    //publish to cached live blueprint.
                    $factory->PublishTemplate();
                }
            } catch (Exception $e) {
                $this->command->error($e->getMessage());
            }
        }
    }

    private function Wizard(){
        $path = 'blueprints/cached/temp';
        $this->command->info("Running BluePrints Wizard...");
        $this->command->info("This wizard will guide you through the process of creating your awesome site.\nLeave blank for anything that does not apply\nLet's get started:");
        $this->command->comment(">Setting up wizard workspace...");
        try {
            $basePath= dirname($this->storage->path($path)) . '/temp/';
            if ($this->storage->exists($path.'/resources') === false) {
                mkdir($basePath.'resources', 0777,true);
            }
            if ($this->storage->exists($path.'/models') === false) {
                mkdir($basePath. 'models', 0777, true);
            }
        } catch (\Exception $ex) {
            dd($ex);
        }
        $this->command->comment(">Wizard workspace is setup at $basePath");

        $this->template['siteName'] = $this->command->ask('What is the Name of the website?');
        $this->template['siteAuthor'] = $this->command->ask('What is the Name of the author?');
        $this->template['siteTitle'] = $this->command->ask('What is the Title of the website?');
        $this->template['siteFooter'] = $this->command->ask('Any footer text?');
        $this->template['favIcon'] = $this->command->ask('Add a shortcut image? Absolute path to the image from within the folder of the blueprint loaded.');
        $this->template['siteFooter'] = empty($this->template['siteFooter'])? ("<div class=\"footer_text\"><span>Copyright <span class=\"copyright\">\u00a9<\/span> {{date(\"Y\")}} <\/span> <span>{{config(\"app.name\")}}<\/span>. <span>All rights reserved. <\/span><\/div>"):$this->template['siteFooter'];
        $this->command->info("Good. Now let's setup some models to be used with the system.");
        $pageBuilder=new bp_wizardMakePage($this->command);
        $pageBuilder->ModelSetup();
        $this->storage->put($path. '/models/ModelDefinition.mbp', $pageBuilder->ExportMyModels(true));
        $this->command->comment("Now, let's setup some pages for the site.");
        while(true){
            array_push($this->template['pages'], $pageBuilder->Wizard());
            if($this->command->confirm("Add another page?")===false){
                break 1;
            }
        }
        $this->storage->put($path . '/MyBluePrint.bp', json_encode($this->template, JSON_PRETTY_PRINT));
        $this->command->comment("Your complete blueprint has been generated and stored at: " . dirname($this->storage->path($path)));
        if($this->command->confirm('Do you need further manual adjustments to the blueprint? Choose "no" to proceed with building with the blueprint.',0)===false){
            $this->command->info("Please head over to that file and make the adjustments needed.  When finished, run 'php artisan bp:BuildSite cached/temp/MyBluePrint.bp'");
        }else{
            $this->targetFile= 'cached/temp/MyBluePrint.bp';
            $this->build();
        }
    }
}

?>