<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model extends CI_Model{

    var $_parent_name = '';


    var $siteview;

    var $siteauth;

    var $backview;

    var $backauth;

    var $category;

    var $pager;

    var $input;

    var $load;

    var $db;

    var $output;

    var $session;

    var $userdata;

    var $upload;

    var $uri;

    var $router;

    var $form_validation;

    var $_table;


    function Model()
    {

        $this->_assign_libraries( (method_exists($this, '__get') OR method_exists($this, '__set')) ? FALSE : TRUE );


        $this->_parent_name = ucfirst(get_class($this));

        log_message('debug', "Model Class Initialized");
    }

    function table_name($table=null){
        if(!is_null($table)){
            $this->_table=$table;
        }
        return $this->_table;
    }


    function _assign_libraries($use_reference = TRUE)
    {
        $CI =& get_instance();
        foreach (array_keys(get_object_vars($CI)) as $key)
        {
            if ( ! isset($this->$key) AND $key != $this->_parent_name)
            {

                if ($use_reference == TRUE)
                {
                    $this->$key = NULL;
                    $this->$key =& $CI->$key;
                }
                else
                {
                    $this->$key = $CI->$key;
                }
            }
        }
    }

}
