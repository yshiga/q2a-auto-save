$(function($) {
    var is_autosave_start = false;
    var timer_id;

    $('#title').keyUp(function(){
        if (!is_autosave_start) {
            autosave_start();
        }
    });
    
    $('textarea[name="content"]').keyUp(function(){
        if (!is_autosave_start) {
            autosave_start();
        }
    });
    
    function autosave_start() {
        is_autosave_start = true;
        timer_id = setInterval(ajax_set_item, 10000);
    }
    
    function ajax_set_item (field, value) {
        // jQuery.post(ajax_url, { field: field, value: value }, function(response) {
        //     console.log(response);
        // });
        console.log('call ajax set item');
    }
    function ajax_get_item () {
        // $.ajax({
        //     url: ajax_url,
        //     type: 'GET',
        //     dataType: 'json',
        //     cache : false,
        //     data: {data}
        // })
        // .done(function(res) {
        //     console.log('Get Item');
        // });
    }

});
