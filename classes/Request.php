<?php
/**
 * Request class.
 * 
 * @package api-framework
 * @author  Vuong Leo <vuonghominh9x@gmail.com>
 */
class Request
{
    /**
     * URL elements.
     *
     * @var array
     */
    public $url_elements = array();
    
    /**
     * The HTTP method used.
     *
     * @var string
     */
    public $method;
    
    /**
     * Any paraemeters sent with the request.
     *
     * @var array
     */
    public $parameters;
}