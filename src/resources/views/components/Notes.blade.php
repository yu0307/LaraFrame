<form class="LF_form_notes" form_type="LF_form_notes" action="{{(isset($custom_note_action)?$custom_note_action:route('LF_SaveNotes'))}}" id="{{$formID??'LF_notes_form'}}">
    <input type="hidden" name="method" value="post" />
    <input type="hidden" name="CurrentNoteID" value="" />
    <div class="tab-pane fade {{($active??false)?'active in':''}}" id="notes">
        <div class="list-notes withScroll" style="height: auto;">
            <div class="notes ">
                <div class="row">
                    <div class="col-md-12">
                        <div id="add-note">
                            <i class="fa fa-plus"></i>ADD A NEW NOTE
                        </div>
                    </div>
                </div>
                <div id="notes-list">
                </div>
            </div>
        </div>
        <div class="detail-note note-hidden-sm">
            <div class="note-header clearfix">
                <div class="note-back">
                    <i class="icon-action-undo"></i>
                </div>
                <div class="note-edit">Edit Note</div>
                <div class="note-subtitle"></div>
            </div>
            <div id="note-detail">
                <div class="note-write">
                    <textarea name="fe_notes" class="form-control" placeholder="Type your notes here ...">{{$default??''}}</textarea>
                    <div class="note_footer">
                        <button class="btn btn-primary btn-sm pull-left">save</button>
                        <button type="button" class="btn btn-danger btn-sm pull-right note-back">cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{$slot}}
</form>