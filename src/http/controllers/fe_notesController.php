<?php

namespace feiron\felaraframe\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \feiron\felaraframe\models\LF_notes;

class fe_notesController extends Controller
{
    public function saveNotes(Request $Request){
        // fe_notes
        if($Request->filled('fe_notes')){
            LF_notes::create(['notes' => $Request->input('fe_notes')]);
        }
        if ($Request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'notes created.']);
        }
    }

    public function getNotesSelect(Request $Request,$column){
        return LF_notes::groupBy($column)->get($column)->pluck($column)->toArray() ;
    }
}
