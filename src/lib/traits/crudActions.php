<?php

namespace feiron\felaraframe\lib\traits;

trait crudActions{

    public function validateRequest(\Illuminate\Contracts\Validation\Validator $validator, \Illuminate\Http\Request $request){
        if ($validator->fails()) {
            return $request->ajax() ?  response()->json(['status' => 'error', 'message' => $validator->getMessageBag()->toArray()]) : redirect()
                ->back()
                ->withInput()
                ->withErrors($validator)->send();
        }
        return true;
    }

    public function CRUD_Create(\Illuminate\Http\Request $request,$model){
        $newModel=$model::create($request->all());
        return (["status" => "success", "message" => "Record successfully created.", "Pk"=> $newModel->getKey()]);
    }

    public function CRUD_Update(\Illuminate\Http\Request $request, $IdentificationKey, $model)
    {
        try {
            $target = $model::findOrFail($request->input($IdentificationKey));
            $target->update($request->except([$IdentificationKey]));
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return (["status" => "error", "message" => "Record cannot be located by the identificaiton."]);
        }
        return (["status" => "success", "message" => "Record successfully updated."]);
    }

    public function CRUD_Delete(\Illuminate\Http\Request $request, $IdentificationKey, $model){
        try {
            $model::findOrFail($request->input($IdentificationKey))->delete();
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return (["status" => "error", "message" => "Record cannot be located by the identificaiton."]);
        }
        return (["status" => "success", "message" => "Record successfully removed."]);
    }

}
?>