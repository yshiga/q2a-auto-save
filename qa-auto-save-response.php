<?php

require_once QA_INCLUDE_DIR.'db/metas.php';

class qa_auto_save_response_page {
    
    function match_request($request) {
        $parts = explode ( '/', $request );
        
        return $parts [0] == 'autosave'; //&& $parts [1] == 'v1'; //&& sizeof ( $parts ) > 1;
    }
    
    function process_request($request) {
        header ( 'Content-Type: application/json' );
            
        $parts = explode ( '/', $request );
        $resource = $parts [1];
        if (sizeof($parts) > 2) {
            $resource = 'invalid';
        }
        /* 
         * Internal security (non for third-party applications)
         * 
         * */
        if (!qa_is_logged_in()) {
            http_response_code ( 401 );
            
            $ret_val = array ();
            
            $json_object = array ();
            
            $json_object ['statuscode'] = '401';
            $json_object ['message'] = qa_lang_html('qa_as_lang/error_auth');
            $json_object ['details'] = qa_lang_html('qa_as_lang/error_msg_auth');
            
            array_push ( $ret_val, $json_object );
            echo json_encode ( $ret_val, JSON_PRETTY_PRINT );
            
            return;
        } 
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($resource) {
            case 'question' :
                if (strcmp($method, 'POST') == 0) {
                    $inputJSON = file_get_contents('php://input');
                    echo $this->post_draft(AS_KEY_QUESTION, $inputJSON);
                } else {
                    echo $this->get_draft(AS_KEY_QUESTION);
                }
                break;
            
            case 'answer' :
                if (strcmp($method, 'POST') == 0) {
                    $inputJSON = file_get_contents('php://input');
                    echo $this->post_draft(AS_KEY_ANSWER, $inputJSON);
                } else {
                    echo $this->get_draft(AS_KEY_ANSWER);
                }
                break;
            
            case 'blog' :
                if (strcmp($method, 'POST') == 0) {
                    $inputJSON = file_get_contents('php://input');
                    echo $this->post_draft(AS_KEY_BLOG, $inputJSON);
                } else {
                    echo $this->get_draft(AS_KEY_BLOG);
                }
                break;
            // case 'comment' :
            //     if (strcmp($method, 'POST') == 0) {
            //         $inputJSON = file_get_contents('php://input');
            //         
            //         echo $this->post_draft(AS_KEY_COMMENT, $inputJSON);
            //     } else {
            //         echo $this->get_draft(AS_KEY_COMMENT);
            //     }
            //     break;
            
            default :
                http_response_code ( 400 );
                
                $ret_val = array ();
                
                $json_object = array ();
                
                $json_object ['statuscode'] = '400';
                $json_object ['message'] = qa_lang_html('qa_as_lang/error_bad_request');
                $json_object ['details'] = qa_lang_html('qa_as_lang/error_msg_default');
                
                array_push ( $ret_val, $json_object );
                echo json_encode ( $ret_val, JSON_PRETTY_PRINT );
        }
    }
    
    function get_draft($key)
    {
        $ret_val = array();
        
        $json_object = array();
        $post_id = qa_get('postid');
        if (!empty($post_id)) {
            $key .= '_' . $post_id;
        }
        
        $userid = qa_get_logged_in_userid();
        $json = qa_db_usermeta_get($userid, $key);
        
        if (empty($json)) {
            http_response_code ( 204 );
            $json_object ['statuscode'] = '204';
            $json_object ['message'] = qa_lang_html('qa_as_lang/error_no_content');
            $json_object ['details'] = qa_lang_html('error_no_data');
            array_push ( $ret_val, $json_object );
            return json_encode ( $ret_val, JSON_PRETTY_PRINT );
        } else {
            $json_object = json_decode($json);
        }
        
        http_response_code(200);
        array_push($ret_val, $json_object);
        
        return json_encode($ret_val, JSON_PRETTY_PRINT);
    }
    
    function post_draft($key, $JSONdata)
    {
        $userid = qa_get_logged_in_userid();
        $json_data = json_decode($JSONdata, true);
        if (isset($json_data['post_id'])) {
            $key .= '_' . $json_data['post_id'];
        }
        $new_json = array(
            'name' => isset($json_data['name']) ? $json_data['name'] : '',
            'title' => isset($json_data['title']) ? $json_data['title'] : '',
            'content' => isset($json_data['content']) ? $json_data['content'] : '',
        );
        $save_data = json_encode($new_json, JSON_UNESCAPED_UNICODE);
        
        qa_db_usermeta_set($userid, $key, $save_data);
        
        $ret_val = array();
        
        $json_object = array();
        
        $json_object['statuscode'] = '200';
        $json_object['message'] = qa_lang_html('qa_as_lang/saved');
        $json_object['details'] = qa_lang_html('qa_as_lang/saved_msg');
        
        array_push($ret_val, $json_object);
        
        http_response_code(200);
        
        return json_encode($ret_val, JSON_PRETTY_PRINT);
    }
}
