<?php

namespace FeIron\LaraFrame\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use FeIron\LaraFrame\models\LF_notes;

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
}
