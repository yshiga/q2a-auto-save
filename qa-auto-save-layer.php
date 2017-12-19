<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    function main_parts($content)
    {
        if ($this->template === 'ask') {
            if (qa_opt('editor_for_qs') == 'Medium Editor') {
                if (isset($content['form'])) {
                    $content['form']['buttons']['ask']['tags'] .= ' id="q_submit"';
                }
            }
        } elseif ($this->template === 'question') {
            if (qa_opt('editor_for_qs') == 'Medium Editor') {
                if (isset($content['a_form']['buttons']['answer'])) {
                    $content['a_form']['buttons']['answer']['tags'] .= ' id="a_submit"';
                }
            }
        }
        qa_html_theme_base::main_parts($content);
    }

    function body_footer()
    {
        // ログインしているときだけscriptを読み込む
        if ((method_exists('qa_html_theme_layer', 'mdl_is_android_app') && !$this->mdl_is_android_app()) && qa_is_logged_in()) {
            $ajax = 'var ajax_url = "'.qa_path('autosave').'/";';
            $ajax .= 'var autosave_timer_id;';
            if ($this->template === 'ask'
                && qa_opt('editor_for_qs') === 'Medium Editor') {
                $this->output_toast();
                $ajax .= 'var resource = "question";';
                $ajax .= 'var post_id = "0000";';
                $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
                $this->output_script($ajax, $script);
                $this->output_dialog();
            } elseif ($this->template === 'question'
                      && qa_opt('editor_for_as') == 'Medium Editor') {
                $this->output_toast();
                $post_id = $this->get_post_id();
                $ajax .= 'var resource = "answer";';
                $ajax .= 'var post_id ="'.$post_id.'";';
                $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
                $this->output_script($ajax, $script);
            } elseif ($this->template === 'blog-new'
                      && qa_opt('qas_blog_editor_for_ps') == 'Medium Editor') {
                $this->output_toast();
                $post_id = $this->get_post_id();
                $ajax .= 'var resource = "blog";';
                $ajax .= 'var post_id ="'.$post_id.'";';
                $ajax .= 'var blog_title_min = '.qa_opt( 'qas_blog_min_len_post_title' ).';';
                $ajax .= 'var blog_content_min = '.qa_opt( 'qas_blog_min_len_post_content' ).';';
                $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
                $this->output_script($ajax, $script);
                $this->output_dialog();

            }
        }
        qa_html_theme_base::body_footer();
    }

    private function output_script($ajax, $script)
    {
        $lang_json = json_encode( $this->get_lang() );
        $vars =<<<EOS
<script>
        $ajax
        var as_lang = {$lang_json};
</script>
EOS;
        $this->output($vars);
        $this->output('<script src="'.$script.'"></script>');
    }

    private function get_post_id()
    {
        $post_id = qa_request_part(0);
        if (empty($post_id) || !is_numeric($post_id)) {
            $post_id = "0000";
        }
        return $post_id;
    }

    private function output_toast()
    {
        $this->output('<div id="autosave-toast" class="mdl-js-snackbar mdl-snackbar">');
        $this->output('<div class="mdl-snackbar__text"></div>');
        $this->output('<button class="mdl-snackbar__action" type="button"></button>');
        $this->output('</div>');
    }

    private function get_lang()
    {
        return array(
            'saved_draft' => qa_lang_html('qa_as_lang/saved_draft'),
            'read_draft' => qa_lang_html('qa_as_lang/read_draft'),
            'error_title_msg' => qa_lang_html('qa_as_lang/error_title_msg'),
        );
    }

    private function output_dialog()
    {
        $confirmDialog = file_get_contents(AS_DIR.'/html/confirm-dialog.html');
        $params = array(
            '^title' => qa_lang('qa_as_lang/confirm_dialog_title'),
            '^msg' => qa_lang('qa_as_lang/confirm_dialog_msg'),
        );
        $this->output(strtr($confirmDialog, $params));
    }
}
