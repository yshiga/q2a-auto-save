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
            if ($this->template === 'ask'
                && qa_opt('editor_for_qs') === 'Medium Editor') {
                $this->output_toast();
                $ajax .= 'var resource = "question";';
                $ajax .= 'var post_id = "0000";';
                $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
                $this->output_script($ajax, $script);
            } elseif ($this->template === 'question'
                      && qa_opt('editor_for_as') == 'Medium Editor') {
                $this->output_toast();
                $post_id = $this->get_post_id();
                $ajax .= 'var resource = "answer";';
                $ajax .= 'var post_id ="'.$post_id.'";';
                $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
                $this->output_script($ajax, $script);
            }
        }
        qa_html_theme_base::body_footer();
    }

    private function output_script($ajax, $script)
    {
        $this->output('<script>'.$ajax.'</script>');
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
}
