$(function($) {
    var is_autosave_start = false;
    var timer_id;
    var elem_name;
    
    ajax_get_item();
    
    $('#title').keyup(function(){
        if (!is_autosave_start) {
            autosave_start();
        }
    });
    
    $('.editable').keyup(function(){
        elem_name = $(this).attr('name');
        if (!is_autosave_start && (elem_name === 'content' || elem_name === 'a_content')) {
            autosave_start(elem_name);
        }
    });
    
    $('#q_submit').click(function(){
        if (elem_name !== '') {
            ajax_set_item(elem_name);
        }
    })
    
    function autosave_start(elem_name) {
        is_autosave_start = true;
        timer_id = setInterval(ajax_set_item, 30000, elem_name);
    }
    
    function editor_content(elem_name) {
        var content = '';
        var editor_elm = document.getElementsByName(elem_name);
        if (editor_elm.length > 0) {
            var target = MediumEditor.getEditorFromElement(editor_elm[0]);
            var allContents = target.serialize();
            var editorId = target.elements[0].id;
            var content = allContents[editorId].value;
        }
        return content;
    }
    
    function ajax_set_item(elem_name) {
        var JSONdata = {
            post_id: post_id,
            name: elem_name,
            title: $('#title').val(),
            content: editor_content(elem_name)
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
            console.log(res);
        });
    }
        
    function ajax_get_item () {
        $.ajax({
            url: ajax_url + resource,
            type: 'GET',
            dataType: 'json',
            cache : false,
            data: { postid: post_id }
        })
        .done(function(res, status, xhr) {
            if (xhr.status === 204) {
                console.log(xhr.statusText);
            } else {
                if (res[0] !== null && res[0] !== undefined) {
                    if ($('#title') !== undefined) {
                        $('#title').val(res[0].title);
                    }
                    var editor_elm = document.getElementsByName(res[0].name);
                    if (editor_elm.length > 0) {
                        var target = MediumEditor.getEditorFromElement(editor_elm[0]);
                        target.setContent(res[0].content, 0);
                    }
                }
            }
        })
        .fail(function(res) {
            console.log(res);
        });
    }
});
