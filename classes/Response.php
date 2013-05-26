<?php
/**
 * Response class factory.
 * 
 * @package api-framework
 * @author  Vuong Leo <vuonghominh9x@gmail.com>
 */
class Response
{
    /**
     * Constructor.
     *
     * @param string $data
     * @param string $format
     */
    public static function create($data, $format)
    {
        switch ($format) {
            case 'application/json':
            default:
                $obj = new ResponseJson($data);
            break;
        }
        return $obj;
    }
}