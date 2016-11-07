$(function($) {
    var is_autosave_start = false;
    var timer_id;
    
    ajax_get_item();
    
    $('#title').keyup(function(){
        if (!is_autosave_start) {
            autosave_start();
        }
    });
    
    $('.editable').keyup(function(){
        var elem_name = $(this).attr('name');
        if (!is_autosave_start && (elem_name === 'content' || elem_name === 'a_content')) {
            var editor_id = $(this).attr('medium-editor-textarea-id');
            autosave_start(elem_name, editor_id);
        }
    });
    
    $('#q_submit').click(function(){
        ajax_set_item();
    })
    
    function autosave_start(elem_name, editor_id) {
        is_autosave_start = true;
        timer_id = setInterval(ajax_set_item, 30000, elem_name, editor_id);
    }
    
    function editor_content(editor_id) {
        var editor_elm = document.getElementById(editor_id);
        var target = MediumEditor.getEditorFromElement(editor_elm);
        var allContents = target.serialize();
        var content = allContents[editor_id].value;
        return content;
    }
    
    function ajax_set_item(elem_name, editor_id) {
        var JSONdata = {
          name: elem_name,
          title: $('#title').val(),
          content: editor_content(editor_id)
        }; 
        $.ajax({
            url: ajax_url + resource,
            type: 'POST',
            dataType: 'json',
            cache : false,
            data: JSON.stringify(JSONdata)
        })
        .done(function(res) {
            console.log('Auto Save');
        })
        .fail(function(res) {
            console.log(res[0]);
        });
    }
        
    function ajax_get_item () {
        $.ajax({
            url: ajax_url + resource,
            type: 'GET',
            dataType: 'json',
            cache : false,
            data: {}
        })
        .done(function(res) {
            if (res[0] !== null && res[0] !== undefined) {
                if ($('title') !== undefined) {
                    $('#title').val(res[0].title);
                }
                var editor_elm = document.getElementsByName(res[0].name);
                var target = MediumEditor.getEditorFromElement(editor_elm[0]);
                target.setContent(res[0].content, 0);
            }
        })
        .fail(function(res) {
            console.log(res);
        });
    }
});
