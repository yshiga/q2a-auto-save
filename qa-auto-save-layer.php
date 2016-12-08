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
        if (qa_is_logged_in()) {
            $ajax = 'var ajax_url = "'.qa_path('autosave').'/";';
            $warn_message = qa_lang_html('qa_as_lang/warn_message');
            if ($this->template === 'ask'
                && qa_opt('editor_for_qs') === 'Medium Editor') {
                $ajax .= 'var resource = "question";';
                $ajax .= 'var post_id = "0000";';
                $ajax .= 'var warn_message ="'.$warn_message.'";';
                $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
                $this->output_script($ajax, $script);
            } elseif ($this->template === 'question'
                      && qa_opt('editor_for_as') == 'Medium Editor') {
                $post_id = $this->get_post_id();
                $ajax .= 'var resource = "answer";';
                $ajax .= 'var post_id ="'.$post_id.'";';
                $ajax .= 'var warn_message ="'.$warn_message.'";';
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
}
