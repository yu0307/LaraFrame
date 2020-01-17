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
                [
                    "name" => "",
                    "title"=>'',//Displayed in title tag
                    "subtext"=>'',//Displayed after HTML contents as text and wrapped in H5 tag
                    'style' => 'Singular', //table, accordion, collection, Singular, crud
                    "model" => [
                        // "name"=> "",
                        // "fields"=> "all" | ["name"=>"Field Name", "caption"=>"label displayed"]
                        // (Optional)"with"=> [
                            // [
                            //     "name"=> "",
                            //     "fields"=> "all"
                            // ]
                        // ]
                        // (Optional)"join": [
                        //     {
                        //         "type": "left|full|...etc",
                        //         "name": "Model Name",
                        //         "fields": "all"| ["name"=>"Field Name", "caption"=>"label displayed"],
                        //         "on": [
                        //             "name(Local),name(Foreign"
                        //         ],
                        //         "modifier"(Optional): [
                        //             {
                        //                 "name": "fieldName",
                        //                 "symbol": "Operators(<,>,<>,=,etc...)",
                        //                 "value": "on value"
                        //             }
                        //         ]
                        //     }
                        // ]
                    ],
                    'routes' => [
                        // [
                            //  "name"=> "Route_Name",
                            //  "slug"=> used as URL(optional),
                            //  "type"=> POST | GET(default),
                            //  "input"(optional)=> [
                            //                 [
                            //                     "name"=> "field_name",
                            //                     "onModel"=> "Model Name" (Optional, As Variable if not on model)
                            //                     "optional"=> false | true
                            //                 ],
                            //                 ...
                            //             ]
                        // ]
                    ],
                    // For Style="table" Only---------------------

                    // "tableFilter"=>[
                        // {
                        //     "name"=> "on Model Name",
                        //     "fields"=> [
                        //         "all"| ["field name",...]
                        //     ]
                        // }
                    // ],
                    // "headerSearch"=>true | false,

                    // -------------------------------------------

                    'html' => '',//Html Contents before the page components
                    "visible" => [ ]
                ]
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
                    // $this->buildTemplate();
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
                }
            } catch (Exception $e) {
                $this->command->error($e->getMessage());
            }
        }
    }

    private function buildTemplate($template=null){
        $template= $template??$this->template;
        $path = dirname($this->storage->path($this->targetFile));
        $this->storage->put($this->targetFile, json_encode($this->template, JSON_PRETTY_PRINT));
        try {
            if ($this->storage->exists($path . '/resources') === false) {
                mkdir($path . '/resources');
            }
            if ($this->storage->exists($path . '/models') === false) {
                mkdir($path . '/models');
            }
        } catch (\Exception $ex) {
            var_dump($ex);
        }
    }

    private function Wizard(){
        $this->command->info("Running BluePrints Wizard...");
        $this->command->info("This wizard will guide you through the process of creating your awesome site.\nLeave blank for anything that does not apply\nLet's get started:");
        $this->template['siteName'] = $this->command->ask('What is the Name of the website?');
        $this->template['siteAuthor'] = $this->command->ask('What is the Name of the author?');
        $this->template['siteTitle'] = $this->command->ask('What is the Title of the website?');
        $this->template['siteFooter'] = $this->command->ask('Any footer text?');
        $this->template['favIcon'] = $this->command->ask('Add a shortcut image? Absolute path to the image from within the folder of the blueprint loaded.');
        $this->template['siteFooter'] = empty($this->template['siteFooter'])? ("<div class=\"footer_text\"><span>Copyright <span class=\"copyright\">\u00a9<\/span> {{date(\"Y\")}} <\/span> <span>{{config(\"app.name\")}}<\/span>. <span>All rights reserved. <\/span><\/div>"):$this->template['siteFooter'];
        $this->command->info("Good. Now let's setup some models to use with the system.");
        $pageBuilder=new bp_wizardMakePage($this->command);
        $pageBuilder->ModelSetup();
        $pageBuilder->Wizard();
        // $this->buildTemplate();
        $this->command->info("A blueprint is generated by the template and stored at:" . str_replace("\\", '/', $this->storage->path($this->targetFile)));
        $this->command->info("Please head over to that file and make the adjustments needed. Run this command again when finished.");
    }
}

?>