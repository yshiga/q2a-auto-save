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
        if ($this->template === 'ask') {
            if (qa_opt('editor_for_qs') == 'Medium Editor') {
                $script = 'var ajax_url = "'.qa_path('autosave').'/"; ';
                $script .= 'var resource = "question";';
                $this->output('<script>'.$script.'</script>');
                $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave-medium.js';
                $this->output('<script src="'.$script.'"></script>');
            }
        }
        qa_html_theme_base::body_footer();
    }
}
