$(function($) {
    var is_autosave_start = false;
    var timer_id;
    var editor_index = 0;
    
    var title = $('#title') ? $('#title').val() : '';
    var content = editor_content();
    content = content.replace(/(<([^>]+)>)/ig,"");
    if (title === '' && content === '') {
        ajax_get_item();
    }
    
    $('#title').keyup(function(){
        if (!is_autosave_start) {
            autosave_start();
        }
    });
    
    $('.editable').keyup(function(){
        if (!is_autosave_start) {
            autosave_start();
        }
    });
    
    $('#q_submit').click(function(){
        ajax_set_item();
    })
    
    function autosave_start() {
        is_autosave_start = true;
        timer_id = setInterval(ajax_set_item, 30000);
    }
    
    function editor_content() {
        var allContents = editor.serialize();
        var editorId = editor.elements[0].id;
        var content = allContents[editorId].value;
        return content;
    }
    
    function ajax_set_item () {
        editor_index = $('.editable').data('medium-editor-editor-index') - 1;
        var JSONdata = {
          index: editor_index,
          title: $('#title').val(),
          content: editor_content()
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
            console.log('Get Item');
            if (res[0] !== null && res[0] !== undefined) {
                if ($('title') !== undefined) {
                    $('#title').val(res[0].title);
                }
                editor.setContent(res[0].content, res[0].index);
            }
        })
        .fail(function(res) {
            console.log(res[0]);
        });
    }
});
