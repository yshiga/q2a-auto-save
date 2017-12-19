<?php
// don't allow this page to be requested directly from browser
if (!defined('QA_VERSION')) {
    header('Location: ../../');
    exit;
}

require_once QA_INCLUDE_DIR.'db/metas.php';

class q2a_auto_save_event 
{
    function process_event($event, $post_userid, $post_handle, $cookieid, $params)
    {
        if ($event === 'q_post') {
            $key = AS_KEY_QUESTION . '_0000';
            qa_db_usermeta_clear($post_userid, $key);
        } elseif ($event === 'a_post') {
            $key = AS_KEY_ANSWER . '_' . $params['parentid'];
            qa_db_usermeta_clear($post_userid, $key);
        } elseif ($event === 'qas_blog_b_post') {
            $key = AS_KEY_BLOG . '_0000';
            qa_db_usermeta_clear($post_userid, $key);
        }
    }
}
