$(function($) {
    var is_autosave_start = false;
    var elem_name;
    var snackbarContainer = document.querySelector('#autosave-toast');
    var title_length;
    var content_length;
    
    $(window).on('load', function(){
      ajax_get_item();
    });
    
    $('#title').keyup(function(){
        if (!is_autosave_start) {
            if (check_elem_name('content')) {
                elem_name = 'content';
            } else if (check_elem_name('a_content')) {
                elem_name = 'a_content';
            }
            autosave_start(elem_name);
        }
    });
    
    $('.editable').keyup(function(){
        elem_name = $(this).attr('name');
        if (!is_autosave_start && (elem_name === 'content' || elem_name === 'a_content')) {
            autosave_start(elem_name);
        }
    });
    
    function check_elem_name(name) {
        var result = document.getElementsByName(name);
        if (result.length > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    function autosave_start(elem_name) {
        is_autosave_start = true;
        autosave_timer_id = setInterval(ajax_set_item, 30000, elem_name);
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
            // console.log('Auto Save');
            addSnackbar(as_lang.saved_draft);
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
                    // すでにタイトルや本文が入力されている場合は反映しない
                    if (($('#title') !== undefined && $('#title').val().length > 0) || get_content_length > 0) {
                        return;
                    }
                    if (resource === 'question' || resource === 'blog') {
                        // 質問または飼育日誌
                        var confirmDialog = document.querySelector('#confirm-autosave');
                        if (! confirmDialog.showModal) {
                            dialogPolyfill.registerDialog(confirmDialog);
                        }
                        confirmDialog.showModal();
                        confirmDialog.querySelector('.ok').addEventListener('click', function() {
                            confirmDialog.close();
                            set_autosave_content(res[0]);
                        });
                        confirmDialog.querySelector('.no').addEventListener('click', function() {
                            confirmDialog.close();
                        });
                    } else {
                        set_autosave_content(res[0]);
                    }
                }
            }
        })
        .fail(function(res) {
            console.log(res);
        });
    }
    
    function addSnackbar(string) {
      var snackbarContainer = document.querySelector('#autosave-toast');
      var data = { message: string, timeout: 2000 };
      snackbarContainer.MaterialSnackbar.showSnackbar(data);
    }

    function set_autosave_content(res) {
        if ($('#title') !== undefined) {
            if ($('#title').val().length <= 0) { 
                $('#title').val(res.title);
            }
        }
        var editor_elm = document.getElementsByName(res.name);
        if (editor_elm.length > 0 && get_content_length() <= 0) {
            var target = MediumEditor.getEditorFromElement(editor_elm[0]);
            target.setContent(res.content, 0);
        }
        if (resource === 'blog') {
            update_blog_title_length();
            update_blog_content_length();
        } else {
            update_title_length();
            update_content_length();
            update_confirm_status();
        }
        
        addSnackbar(as_lang.read_draft);
    }

    function update_title_length(){
        var disabled = true;
        if($('#title').val()) {
            var parent = $('#title').parent().parent();
            var title_error = '<span id="title-error" class="mdl-color-text--red">'+as_lang.error_title_msg+'</span>';
            title_length = $('#title').val().length;
            if(title_length >= 20) {
                if($("#title-error")[0]) {
                    $("#title-error").remove();
                }
            } else {
                if(!$("#title-error")[0]) {
                    parent.append(title_error);
                    $("#title").addClass('error-title');
                }
            }
            update_confirm_status();
        }
    }

    function update_confirm_status(){
        var disabled = true;
        if(title_length >= 20 && content_length >= 20) {
          disabled = false;
        }
        $("#confirm-button").prop("disabled", disabled);
    }

    function update_content_length() {
        var disabled = true;
        content_length = get_content_length();
        if(content_length >= 20) {
            $("#content-error").hide();
            $(".editable").removeClass('error-content');
        } else {
            $("#content-error").show();
            $(".editable").addClass('error-content');
        }
        update_confirm_status();
    }

    function get_content_length() {
        var content = '';
        var editor_elm = document.getElementsByName('content');
        if (editor_elm.length > 0) {
            var target = MediumEditor.getEditorFromElement(editor_elm[0]);
            var allContents = target.serialize();
            var editorId = target.elements[0].id;
            var content = allContents[editorId].value;
        }
        return $.trim(jQuery(content).text()).length;
    }

    function update_blog_title_length() {
        var disabled = true;
        if($('#title').val()) {
            title_length = $('#title').val().length;
            if(title_length >= blog_title_min) {
                $("#form-title-error").hide();
                $("#title").removeClass('error-title');
            } else {
                $("#form-title-error").show();
                $("#title").addClass('error-title');
            }
        } else {
            $("#form-title-error").show();
            $("#title").addClass('error-title');
        }
    }

    function update_blog_content_length() {
        var disabled = true;
        content_length = get_content_length();
        if(content_length >= blog_content_min) {
            $("#form--error").hide();
            $(".editable").removeClass('error-content');
        } else {
            $("#form--error").show();
            $(".editable").addClass('error-content');
        }
    }
});