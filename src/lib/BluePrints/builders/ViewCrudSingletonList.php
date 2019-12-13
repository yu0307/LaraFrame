<?php

namespace feiron\felaraframe\lib\BluePrints\builders;

use feiron\felaraframe\lib\BluePrints\BluePrintsViewBuilderBase;

class ViewCrudSingletonList extends BluePrintsViewBuilderBase {

    public function __construct($MethodDefinition = null, $ModelList){
        parent::__construct(($MethodDefinition ?? []), $ModelList);
    }

    public function BuildView(): string{
        $baseModel = $this->ViewDefinition['FieldList'][0];
        $baseModel = $this->ModelList[$baseModel['modelName']];

        $content = '';
        $header = '';
        $tableContent = '';
        foreach (($this->ViewDefinition['FieldList'] ?? []) as $fieldDefinition) {
            foreach (($fieldDefinition['Fields'] ?? []) as $field) {
                $header .= ('
                            <th>' . ($field->label ?? $field->name) . '</th>');
                if (isset($field->name)) {
                    $tableContent .= '  
                                <td class="collection_component ' . ($field->container_class ?? '') . '" ' . ($field->container_attr ?? '') . '>
                                    <div class="collection_item ' . ($field->class ?? '') . '" ' . ($field->attr ?? '') . ' >
                                        {{$row[\'' . $field->name . '\']??""}}
                                    </div>
                                </td>';
                }
            }
            $tableContent .= '
                                <td class="collection_component">
                                    <a href="{{route(\'bpr_crudSingleton_' . $baseModel->getModelName() . '\').\'/\'.$row[\'' . $baseModel->getPrimary() . '\']}}" class="btn btn-primary btn-sm btn-mini">Edit</a>
                                    <a href="{{route(\'bpr_bp_crud_CRUD_' . $baseModel->getModelName() . '_Delete\').\'/?' . $baseModel->getPrimary() . '=\'.$row[\'' . $baseModel->getPrimary() . '\']}}" class="btn btn-danger btn-sm btn-mini">Delete</a>
                                </td>
            ';
        }
        $header.= ('<th width="160px">Options</th>');
        $content = '
        <div class="container-fluid collections">
            <div class="row col-md-12 col-sm-12">
                @if (session("message"))
                    <div class="alert alert-info">
                        {{ session("message") }}
                    </div>
                @endif
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            ' . $header . '
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collection as $row)
                            <tr>
                                ' . $tableContent . '
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        ';

        return $content;
    }
}