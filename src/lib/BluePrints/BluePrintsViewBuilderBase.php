<?php

namespace feiron\felaraframe\lib\BluePrints;

use feiron\felaraframe\lib\BluePrints\contracts\BluePrintViewBuilderContract;

abstract class BluePrintsViewBuilderBase implements BluePrintViewBuilderContract {

    protected $ViewDefinition;
    protected $ModelList;

        
    private const DEFAULT=[
        "name" => "",
        "style" => "singular",
        "usage" => "display",
        "title" => "",
        "subtext" => "",
        "html" => "",
        "FieldList" => [],
        "headerSearch" => false,
        'withcrud'=>false
    ];

    public function __construct($ViewDefinition = null, $ModelList){
        $this->ViewDefinition=array_merge(self::DEFAULT,$ViewDefinition);
        $this->ModelList= $ModelList;
    }

    public abstract function BuildView():string;

    protected function generateFormControls($fieldDef){
        switch($fieldDef['dataType']){
            case "dateTime":
            case "dateTimeTz":
                return '
                        <div class="col-sm-12 col-md-4">
                            <div class="form-group">
                                <label for="crud_' . $fieldDef['name'] . '">' . ($fieldDef['label']?? $fieldDef['name']) . '</label>
                                <input class="form-control" type="datetime-local" value="' . ($fieldDef['default'] ?? '') . '" name="' . $fieldDef['name'] . '" id="crud_' . $fieldDef['name'] . '" />
                            </div>
                        </div>';
            case "date":
                return '
                        <div class="col-sm-12 col-md-4">
                            <div class="form-group">
                                <label for="crud_' . $fieldDef['name'] . '">' . ($fieldDef['label'] ?? $fieldDef['name']) . '</label>
                                <input class="form-control" type="date" value="' . ($fieldDef['default']??'') . '" name="' . $fieldDef['name'] . '" id="crud_'. $fieldDef['name']. '" />
                            </div>
                        </div>';
            case "binary":
                return '
                        <div class="col-sm-12 col-md-4">
                            <div class="form-group">
                                <div class="icheck-inline" id="crud_' . $fieldDef['name'] . '" type="radio">
                                    <div>' . ($fieldDef['label'] ?? $fieldDef['name']) . '</div>
                                    <label><input class="form-control" type="radio" name="' . $fieldDef['name'] . '" value="1"> Yes</label>
                                    <label><input class="form-control" type="radio" name="' . $fieldDef['name'] . '" value="0"> No</label>
                                </div>
                            </div>
                        </div>
                ';
            case "longText":
            case "mediumText":
            case "text":
                return '
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="crud_' . $fieldDef['name'] . '">' . ($fieldDef['label'] ?? $fieldDef['name']) . '</label>
                                <textarea class="form-control" rows="4" name="' . $fieldDef['name'] . '" id="crud_' . $fieldDef['name'] . '">' . ($fieldDef['default'] ?? '') . '</textarea>
                            </div>
                        </div>';
            default:
                return '
                        <div class="col-sm-12 col-md-4">
                            <div class="form-group">
                                <label for="crud_' . $fieldDef['name'] . '">' . ($fieldDef['label'] ?? $fieldDef['name']) . '</label>
                                <input class="form-control" type="text" value="' . ($fieldDef['default'] ?? '') . '" name="' . $fieldDef['name'] . '" id="crud_' . $fieldDef['name'] . '" />
                            </div>
                        </div>';
        }
    }
}