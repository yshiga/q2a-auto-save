<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    function body_footer()
    {
        
        if ($this->template === 'ask') {
            $script = QA_HTML_THEME_LAYER_URLTOROOT.'js/autosave.js';
            $this->output('<script src="'.$script.'"></script>');
        }
        qa_html_theme_base::body_footer();
    }
}
