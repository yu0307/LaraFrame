$(function (){

    var notes = notes || {};
    /* Display current datetime and hours */
    function CurrentDate(container){
        if($(container).html().length<=0){
            var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            var dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            var date = new Date();
            date.setDate(date.getDate() + 1);
            var day = date.getDate();
            var month = date.getMonth();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            var strTime = dayNames[date.getDay()] + " " + date.getDate() + ' ' + monthNames[date.getMonth()] + ', ' + hours + ':' + minutes + ' ' + ampm;
            $(container).text(strTime);
        }
    }
    notes.$container = $("#notes");
    $.extend(notes, {
        noTitleText: "No title",
        noDescriptionText: "(No content)",
        $currentNote: $(null),
        $currentNoteTitle: $(null),
        $currentNoteDescription: $(null),
        addNote: function () {
            var $note = $('<div class="note-item media current fade in"><button class="close">×</button><div><div><p class="note-name">Untitled</p></div><p class="note-desc hidden">No content.</p><p><small class="note-date"></small></p></div></div>');
            notes.$notesList.prepend($note);
            CurrentDate($note.find('.note-date:first')); 
            customScroll();
        },
        checkCurrentNote: function () {
            var $current_note = notes.$notesList.find('div.current').first();
            if ($current_note.length) {
                notes.$currentNote = $current_note;
                notes.$currentNoteTitle = $current_note.find('.note-name');
                notes.$currentNoteDescription = $current_note.find('.note-desc');
                var $space = notes.$currentNoteTitle.text().indexOf( "\r" );
                $note_title = notes.$currentNoteTitle.html();
                if($space == -1) {
                    $note_title = notes.$currentNoteTitle.append('&#13;').html();
                }
                var completeNote = ((notes.$currentNoteDescription.text() == 'No content.')?$note_title:'') + $.trim(notes.$currentNoteDescription.html());
                $space = $note_title.indexOf( "\r" );
                notes.$writeNote.val(completeNote).trigger('autosize.resize');

            } else {
                var first = notes.$notesList.find('div:first:not(.no-notes)');
                if (first.length) {
                    first.addClass('current');
                    notes.checkCurrentNote();
                } else {
                    notes.$writeNote.val('');
                    notes.$currentNote = $(null);
                    notes.$currentNoteTitle = $(null);
                    notes.$currentNoteDescription = $(null);
                }
            }
        },
        updateCurrentNoteText: function () {
            var text = $.trim(notes.$writeNote.val());
            if (notes.$currentNote.length) {
                var title = '',
                    description = '';
                if (text.length) {
                    var _text = text.split("\n"),
                        currline = 1;
                    for (var i = 0; i < _text.length; i++) {
                        if (_text[i]) {
                            if (currline == 1) {
                                title = _text[i];
                            } else
                            if (currline == 2) {
                                description = _text[i];
                            }
                            currline++;
                        }
                        if (currline > 2)
                            break;
                    }
                }
                notes.$currentNoteTitle.text(title.length ? title : notes.noTitleText);
                notes.$currentNoteDescription.text(description.length ? description : notes.noDescriptionText);
                
            } else
            if (text.length) {
                notes.addNote();
            }
        }
    });
    if (notes.$container.length > 0) {
        notes.$notesList = notes.$container.find('#notes-list');
        notes.$txtContainer = notes.$container.find('.note-write');
        notes.$writeNote = notes.$txtContainer.find('textarea');
        notes.$addNote = notes.$container.find('#add-note');
        notes.$addNote.on('click', function (ev) {
            notes.addNote();
            notes.$writeNote.val('');
        });
        $('#notes-list').on('click', '.close', function(){
            if ($(this).attr('tar_id')!=undefined){
                $fm = $("#quickview-sidebar .LF_form_notes:first");
                $fm.find('input[name="method"]').val('DELETE');
                $fm.find('input[name="CurrentNoteID"]').val($(this).attr('tar_id'));
                $fm.submit();
            }
            $currentNote = $(this).parent();
            $currentNote.addClass("animated bounceOutRight");
            window.setTimeout(function () {
               $currentNote.remove();
            }, 300);
        });
        $('#notes-list').on('click', '.note-item > div', function(){
            $('.list-notes').removeClass('current');
            $('.detail-note').addClass('current');
            $('.LF_form_notes input[name="CurrentNoteID"]').val($(this).parent('.note-item').attr('nid'));
            $('.note-subtitle').html($(this).find('.note-date:first').html());
        });
        $('.note-back').on('click', function(){
            $('.list-notes').addClass('current');
            $('.detail-note').removeClass('current');
        });

        notes.$writeNote.on('keyup', function (ev) {
            notes.updateCurrentNoteText();
        });
        notes.checkCurrentNote();
        notes.$notesList.on('click', '.note-item', function (ev) {
            ev.preventDefault();
            notes.$notesList.find('.note-item').removeClass('current');
            $(this).addClass('current');
            notes.checkCurrentNote();
        });
    }
    var messages_list = $('.list-notes');
    var message_detail = $('.detail-note');
    noteTextarea();
    ListNotesHeight();
    $('#go-back').on('click', function () {
        $('.list-notes').fadeIn();
        $('.detail-note').css('padding-left', '0');
        $('.detail-note').fadeOut();
    });
});

function noteTextarea(){
    $('.note-write textarea').height($(window).height() - 108);
}

function ListNotesHeight(){
    $('.list-notes').height($(window).height() - 50);
}
    

/****  On Resize Functions  ****/
$(window).resize(function () {
    noteTextarea();
    ListNotesHeight();

});

function printNotes(ID,Notes,TimeStamp){
    return '    <div class="note-item media fade in" nid="' + ID + '">' +
                    '<button class="close" tar_id="' + ID + '">×</button>' +
                    '<div>' +
                        '<div>' +
                            '<p class="note-name">' + Notes.substring(0, 20) + '</p>' +
                        '</div > ' +
                        '<p class="note-desc hidden">' + Notes + '</p>' +
                        '<p><small class="note-date">' + TimeStamp + '</small></p>' +
                    '</div > ' +
                '</div>';
}

$(document).ready(function(){
    $('form[form_type="LF_form_notes"]').on('lf_form_notes_post', function () { //events fired when note is saved.
        $('.list-notes').addClass('current');
        $('.detail-note').removeClass('current');
    });
});