$(function($) {
    var is_first = true;
    var is_autosave_start = false;
    var timer_id;
    var editor_index = 0;
    
    if (is_first) {
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
            editor_index = $(this).data('medium-editor-editor-index') - 1;
        }
    });
    
    function autosave_start() {
        is_autosave_start = true;
        timer_id = setInterval(ajax_set_item, 15000);
    }
    
    function ajax_set_item () {
        var JSONdata = {
          index: editor_index,
          title: $('#title').val(),
          content: get_content()
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
                if ($('title') !== undefined ) {
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
