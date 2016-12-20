<?php

if (!defined('QA_VERSION')) {
    require_once dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/../../../qa-include/qa-base.php';
}

// usermetas テーブルの auto save データ削除
qa_db_query_sub(
    'DELETE FROM ^usermetas WHERE title LIKE $',
    '%q2a_as_%'
);
