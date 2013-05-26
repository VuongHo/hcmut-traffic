<?php
require_once("Database.php");
/**
 * Sample news controller.
 * 
 * @package api-framework
 * @author  Vuong Leo <vuonghominh9x@gmail.com>
 */
class SegmentdemoController extends AbstractController
{
	public function __construct(){
		parent::__construct(); // Init parent contructor
	}
    
    /**
     * GET method.
     * 
     * @param  Request $request
     * @return string
     */
    public function get($request)
    {
    	switch (count($request->url_elements)) {
            case 1:
        		return null;	
            break;
        }
    }
    
    /**
     * POST action.
     *
     * @param  $request
     * @return null
     */
    public function post($request)
    {
        switch (count($request->url_elements)) {
        	case 1:
        		if (trim($request->url_elements[0]) == "segmentdemo") {
	        		// get all parameters values from request
		        	$x1 = $request->parameters['top_lef_x'];		//A
					$y1 = $request->parameters['top_lef_y'];
					
					$x2 = $request->parameters['top_righ_x'];	//B	
					$y2 = $request->parameters['top_righ_y'];
					
					$x3 = $request->parameters['bot_righ_x'];	//C
					$y3 = $request->parameters['bot_righ_y'];
					
					$x4 = $request->parameters['bot_lef_x'];		//D	
					$y4 = $request->parameters['bot_lef_y'];		
					
	        		// cell_d is the length to split map into the cells
					$cell_d = 3;
					
					// cell_x0 , cell_y0 are the first coordinate of map's cell
					// cell_x0 , cell_y0 are the first coordinate of map's cell
					$arr_cell = $this->get_cell_root();
	        		$cell_x0 = $arr_cell[0];
	        		$cell_y0 = $arr_cell[1];
					
					// find four the coordinates to search segment 
					$min_x = min(array((int)($x1 - $cell_x0)/$cell_d, (int)($x2 - $cell_x0)/$cell_d, (int)($x3 - $cell_x0)/$cell_d, (int)($x4 - $cell_x0)/$cell_d));
					$max_x = max(array((int)($x1 - $cell_x0)/$cell_d, (int)($x2 - $cell_x0)/$cell_d, (int)($x3 - $cell_x0)/$cell_d, (int)($x4 - $cell_x0)/$cell_d));
					
					$min_y = min(array((int)($y1 - $cell_y0)/$cell_d, (int)($y2 - $cell_y0)/$cell_d, (int)($y3 - $cell_y0)/$cell_d, (int)($y4 - $cell_y0)/$cell_d));
					$max_y = max(array((int)($y1 - $cell_y0)/$cell_d, (int)($y2 - $cell_y0)/$cell_d, (int)($y3 - $cell_y0)/$cell_d, (int)($y4 - $cell_y0)/$cell_d));
					
	        		// Search all cell_id from cell table
	        		$cell_id = $this->select_cell(array(array($min_x, $min_y), array($max_x, $max_y)));
	        		
	        		// Find all street_id from segmentcell table
					$street_id = $this->find_street_id($cell_id);
					
	        		// Find all segment detail from street_id, cell_id
	        		$segment_id = $this->find_segment_id($cell_id);
	        		$arr_segment_id = join(',', $segment_id);
	        		$segment = $this->find_all_segmentdemo($street_id, $arr_segment_id);
					
					return $segment;
        		}
        		break;
        	case 2:
        		if (trim($request->url_elements[1]) == "speed"){
        			// get all parameters values from request
		        	$radius_x = $request->parameters['radius_x'];		//R
					$radius_y = $request->parameters['radius_y'];
					
					$radius = $request->parameters['radius']; // Radius 	
					
	        		// cell_d is the length to split map into the cells
					$cell_d = 3;
					
					// cell_x0 , cell_y0 are the first coordinate of map's cell
					// cell_x0 , cell_y0 are the first coordinate of map's cell
					$arr_cell = $this->get_cell_root();
	        		$cell_x0 = $arr_cell[0];
	        		$cell_y0 = $arr_cell[1];
					
					// find four the coordinates to search segment 
					$min_x = (int) (($radius_x - $radius - $cell_x0)/$cell_d);
					$max_x = (int) (($radius_x + $radius - $cell_x0)/$cell_d);
					
					$min_y = (int) (($radius_y - $radius - $cell_y0)/$cell_d);
					$max_y = (int) (($radius_y + $radius - $cell_y0)/$cell_d);
					
	        		// Search all cell_id from cell table
	        		$cell_id = $this->select_cell(array(array($min_x, $min_y), array($max_x, $max_y)));
	        		
	        		// Find all street_id from segmentcell table
					$street_id = $this->find_street_id($cell_id);
					
	        		// Find all segment_id from table's segmentcell
	        		$segment_id = $this->find_segment_id($cell_id);
	        		//echo date("Y-m-d H:i:s",time());
	        		$arr_segment_id = join(',', $segment_id);
	        		$segment = $this->find_speed_segmentdemo($street_id, $arr_segment_id);
					
					return $segment;
        		}
        		break;
        }
    }
}