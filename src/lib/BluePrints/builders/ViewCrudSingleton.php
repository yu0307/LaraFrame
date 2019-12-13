<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsViewBuilderBase;

class ViewCrudSingleton extends BluePrintsViewBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition ?? []), $ModelList);
    }

    public function BuildView(): string{
        $targetModel = $this->ViewDefinition['FieldList'][0];
        $targetModel = $this->ModelList[$targetModel['modelName']];
        return '
                @if ($errors->any())
                    <div class="alert-danger alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="post" action="{{route(\'bpr_bp_crud_CRUD_' . $targetModel->getModelName() . '_\'.(isset($' . $targetModel->getPrimary() . ')?\'Update\':\'Create\'))}}">
                    @csrf
                    '.join('
                    ',array_map(function($fieldDef){
                        if(
                            ($fieldDef['editable']??true)===false ||
                            ($fieldDef['primary'] ?? false) ===true ||
                            ($fieldDef['autoIncrement'] ?? false) === true ||
                            (in_array($fieldDef['dataType'], ['bigIncrements', 'tinyIncrements', 'smallIncrements', 'mediumIncrements', 'increments']))
                            ){
                                return '';    
                        }
                        $fieldDef['default']= '{{$' . $fieldDef['name'] . '??old("' . $fieldDef['name'] . '")??""}}';
                        return $this->generateFormControls($fieldDef);
                    }, ($targetModel->getFields()??[]))). '
                    <a href="{{url()->previous()}}" class="btn btn-success">Back</a>
                    @if(isset($' . $targetModel->getPrimary() . '))
                    <input type="hidden" class="form-control" name="' . $targetModel->getPrimary() . '" id="bp_' . $targetModel->getPrimary() . '"  value="{{$' . $targetModel->getPrimary() . '}}"/>
                    <button class="btn btn-primary crud_update pull-right">Update</button>
                    @else
                    <button class="btn btn-primary crud_create pull-right">Create</button>
                    @endif
                </form>
                <div class="clearfix"></div>';
    }
}