<?php

namespace feiron\felaraframe\lib\traits;

trait crudActions{

    public function validateRequest(\Illuminate\Contracts\Validation\Validator $validator, \Illuminate\Http\Request $request){
        if ($validator->fails()) {
            return $request->ajax() ?  response()->json(['status' => 'error', 'message' => $validator->getMessageBag()->toArray()]) : redirect()
                ->back()
                ->withErrors($validator);
        }
        return true;
    }

    public function CRUD_Create(\Illuminate\Http\Request $request,$model){
        $model::create($request->all());
        return (["status" => "success", "message" => "Record successfully created."]);
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
        $withData = (["status" => "success", "message" => "Record successfully removed."]);
    }

}
?>