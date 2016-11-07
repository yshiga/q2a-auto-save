<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    function form($form)
    {
        if ($this->template === 'ask') {
            if (qa_opt('editor_for_qs') == 'Medium Editor') {
                $form['buttons']['ask']['tags'] .= ' id="q_submit"';
            }
        }
        qa_html_theme_base::form($form);
    }
    
    function body_footer()
    {
        $ajax = 'var ajax_url = "'.qa_path('autosave').'/"; ';
        if ($this->template === 'ask'
            && qa_opt('editor_for_qs') === 'Medium Editor') {
            $ajax .= 'var resource = "question";';
            $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
            $this->output_script($ajax, $script);
        } elseif ($this->template === 'question'
                  && qa_opt('editor_for_as') == 'Medium Editor') {
            $ajax .= 'var resource = "answer";';
            $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
            $this->output_script($ajax, $script);
        }
        qa_html_theme_base::body_footer();
    }
    
    private function output_script($ajax, $script)
    {
        $this->output('<script>'.$ajax.'</script>');
        $this->output('<script src="'.$script.'"></script>');
    }
}
