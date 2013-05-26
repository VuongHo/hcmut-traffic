<?php
/**
 * @package  api-framework
 * @author  Vuong Leo <vuonghominh9x@gmail.com>
 * @abstract
 */
abstract class AbstractController {
	public $data = "";
		
	const DB_SERVER = "localhost";
	const DB_USER = "root";
	const DB_PASSWORD = "";
	const DB = "demo2_traffic";
	
	private $db = NULL;
	
	public function __construct(){
		$this->dbConnect(); // Initiate Database connection
	}
	/*
	 *  Database connection 
	*/
	private function dbConnect(){
		$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
		if($this->db) mysql_select_db(self::DB,$this->db);
		else die("Could not connect: " . mysql_error());
	}
	
//Get coordinates of cell root
    public function get_cell_root(){
    	$cell_x0;
		$cell_y0;
    	
    	$sql = mysql_query("SELECT cell_lat, cell_lon FROM cell WHERE cell_x = 0 AND cell_y = 0 LIMIT 1", $this->db);
		if(mysql_num_rows($sql) > 0){
			while ($row = mysql_fetch_array($sql,MYSQL_ASSOC)) {
				$cell_x0 = (double)$row['cell_lat'];
				$cell_y0 = (double)$row['cell_lon'];
			}
		}
		
		return array($cell_x0, $cell_y0);
    }
    
    //Search all cell_id from cell table
    public function select_cell($arr_node){
    	$min_x = $arr_node[0][0];
    	$min_y = $arr_node[0][1];
    	$max_x = $arr_node[1][0];
    	$max_y = $arr_node[1][1];
    	
	    $cell_id = array();
	    
		for ($i = $min_x; $i <= $max_x; $i++) {
			for ($j = $min_y; $j < $max_y; $j++) {
				$sql = mysql_query("SELECT cell_id FROM cell WHERE cell_x = $i AND cell_y = $j LIMIT 1 ", $this->db);
				if(mysql_num_rows($sql) > 0){
					$row = mysql_fetch_array($sql,MYSQL_ASSOC);
					$cell_id[] = $row['cell_id'];
				}
			}
		}
		
		return $cell_id;
    }
    
    // Find all street_id from segmentcell table
    public function find_street_id($cell_id){
		$arr_cell_id = join(',', $cell_id);
    	$sql = mysql_query("SELECT street_id FROM segmentcell
							JOIN segment ON segmentcell.segment_id = segment.segment_id
							WHERE cell_id IN ($arr_cell_id)", $this->db);
    	if(mysql_num_rows($sql) > 0){
			while ($row = mysql_fetch_array($sql,MYSQL_ASSOC)){
				$street_id[] = $row['street_id'];
			}
		}
		$street_id = array_unique($street_id);
		return $street_id;
    }
    
    // Find all segment_id when we have a array's cell.
    public function find_segment_id($cell_id){
    	$arr_cell_id = join(',', $cell_id);
    	$sql = mysql_query("SELECT segment.segment_id FROM segmentcell
							JOIN segment ON segmentcell.segment_id = segment.segment_id
							WHERE cell_id IN ($arr_cell_id)", $this->db);
    	if(mysql_num_rows($sql) > 0){
			while ($row = mysql_fetch_array($sql,MYSQL_ASSOC)){
				$segment_id[] = $row['segment_id'];
			}
		}
		$segment_id = array_unique($segment_id);
		return $segment_id;
    }
    
    
    public function find_all_segment($street_id, $arr_segment_id){
    	$segment = array();
    	foreach ($street_id as $s_key => $s_val) {
			$sql = mysql_query("SELECT node_id_start, node_id_end
								FROM segment
								WHERE segment_id IN ($arr_segment_id) AND street_id = $s_val
								GROUP BY node_id_start", $this->db);
					
			if(mysql_num_rows($sql) > 0){
				$seg = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$seg[] = array('node_start' => $rlt['node_id_start'], 'node_end' => $rlt['node_id_end']);
				}
			}
			$segment[] = array('id_street' => $s_val, 'segment' => $seg);
			//http://stackoverflow.com/questions/2467945/how-to-generate-json-file-with-php
					
		}
		return $segment;
    }
    
    public function find_speed_segment($street_id, $arr_segment_id){
    	$segment = array();
    	foreach ($street_id as $s_key => $s_val) {
			$sql = mysql_query("SELECT node_id_start, node_id_end, speed
								FROM segment
								WHERE segment_id IN ($arr_segment_id) AND street_id = $s_val
								GROUP BY node_id_start", $this->db);
					
			if(mysql_num_rows($sql) > 0){
				$seg = array();
				$i = 0;
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$seg[] = array('node_start' => $rlt['node_id_start'], 'node_end' => $rlt['node_id_end'], 'speed' => $rlt['speed']);
					$i++;
				}
			}
			$segment[] = array('id_street' => (int) $s_val,'count' => $i, 'segment' => $seg);
			//http://stackoverflow.com/questions/2467945/how-to-generate-json-file-with-php
					
		}
		return $segment;
    }
    
    public function find_segment_by_street_id($street_id){
    	$segment = array();
    	$sql = mysql_query("SELECT node_id_start, node_id_end, speed
    						FROM segment
    						WHERE street_id = $street_id", $this->db);
    	if (mysql_num_rows($sql) > 0) {
    		$seg = array();
    		$i = 0;
    		while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
    			$seg[] = array('node_start' => $rlt['node_id_start'], 'node_end' => $rlt['node_id_end'], 'speed' => $rlt['speed']);
    			$i++;
    		}
    	}
    	$segment[] = array('id_street' => (int) $street_id,'count' => $i, 'segment' => $seg);
    	return $segment;
    }
    	
    
    // Function be use by SegmentdemoController
	public function find_speed_segmentdemo($street_id, $arr_segment_id){
    	$segment = array();
    	foreach ($street_id as $s_key => $s_val) {
			$sql = mysql_query("SELECT node_id_start, node_id_end, speed
								FROM segment
								WHERE segment_id IN ($arr_segment_id) AND street_id = $s_val
								GROUP BY node_id_start", $this->db);
					
			if(mysql_num_rows($sql) > 0){
				$seg = array();
				$i = 0;
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$seg[] = array('node_start' => $rlt['node_id_start'], 'node_end' => $rlt['node_id_end'], 'speed' => $rlt['speed']);
					$i++;
				}
			}
			$segment[] = array('id_street' => (int) $s_val,'count' => $i, 'segment' => $seg);
			//http://stackoverflow.com/questions/2467945/how-to-generate-json-file-with-php
					
		}
		return $segment;
    }
}