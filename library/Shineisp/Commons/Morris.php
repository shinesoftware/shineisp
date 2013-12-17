<?php

class Shineisp_Commons_Morris {
	
	/**
	 * @api
	 * http://www.oesmith.co.uk/morris.js/lines.html
	 * @since 1.0.0
	 */
	const TYPE_AREA = 'Area';
	
	/**
	 * @api
	 * http://www.oesmith.co.uk/morris.js/bars.html
	 * @since 1.0.0
	 */
	const TYPE_BAR = 'Bar'; 
	
	/**
	 * @api
	 * http://www.oesmith.co.uk/morris.js/donuts.html
	 * @since 1.0.0
	 */
	const TYPE_DONUT = 'Donut';
	
	/**
	 * @api
	 * http://www.oesmith.co.uk/morris.js/lines.html
	 * @since 1.0.0
	 */
	const TYPE_LINE = 'Line';
	
	/**
	 * @var string[] An arry of the TYPE_* constants defined in this class
	 */
	private static $types = array(
								self::TYPE_AREA,
								self::TYPE_BAR,
								self::TYPE_DONUT,
								self::TYPE_LINE,
							);
	
	protected $element = "morris-";
	protected $data = array();
	protected $xkey;
	protected $ykeys;
	protected $parseTime = false;
	protected $options = array();
	protected $labels = array();
	protected $type = "Line";
	
	/**
	 * @return the $parseTime
	 */
	public function getParseTime() {
		return $this->parseTime;
	}

	/**
	 * @param field_type $parseTime
	 */
	public function setParseTime($parseTime = false) {
		$this->parseTime = $parseTime;
		return $this;
	}

	/**
	 * @return the $element
	 */
	public function getElement() {
		return $this->element;
	}

	/**
	 * @return the $data
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @return the $xkey
	 */
	public function getXkey() {
		return $this->xkey;
	}

	/**
	 * @return the $ykeys
	 */
	public function getYkeys() {
		return $this->ykeys;
	}

	/**
	 * @return the $labels
	 */
	public function getLabels() {
		return $this->labels;
	}
	
	/**
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * @param string $type
	 */
	public function setType($type) {
		
		if (!in_array($type, self::$types)) {
			throw new Exception("'$type' is not a valid chart type!");
		}
		
		$this->type = $type;
		return $this;
	}

	/**
	 * @param string $element
	 */
	public function setElement($element) {
		$this->element = $element;
		return $this;
	}

	/**
	 * @param multitype: $data
	 */
	public function setData($data) {
		$this->data = $data;
		return $this;
	}

	/**
	 * @param field_type $xkey
	 */
	public function setXkey($xkey) {
		$this->xkey = $xkey;
		return $this;
	}

	/**
	 * @param field_type $ykeys
	 */
	public function setYkeys($ykeys) {
		$this->ykeys = $ykeys;
		return $this;
	}

	/**
	 * @param field_type $labels
	 */
	public function setLabels($labels) {
		$this->labels = $labels;
		return $this;
	}
	
	/**
	 * @return the $options
	 */
	public function getOptions() {
		return $this->options;
	}
	
	/**
	 * @param field_type $options
	 */
	public function setOptions(array $options) {
		$this->options = array_merge($this->options, $options);
		return $this;
	}
	

	/**
	 * Constructor
	 */
	public function __construct() {}
	
	/**
	 * Convert the array data in a json string for the morris graph
	 */
	private function convertDataToJson(){
		$parsedata = array();
		$data = $this->getData();
		
		foreach ($data as $xvalue => $yvalues){
			if(is_array($yvalues)){
				$parsedata[] = array('xdata' => $xvalue) + $yvalues;
			}
		}
		
		$this->setData($parsedata);
		return true;
	}
	
	/**
	 * Get the YKeys indexes
	 */
	private function getYkeysIndexes(){
		$data = $this->getData();
		$keys = array();
		if(!empty($data)){
			$firstlevel = array_keys($data);
			
			if(!empty($firstlevel[0]) && is_array($firstlevel)){
				$keys = array_keys($data[$firstlevel[0]]);
			}
		}
		
		$this->setYkeys($keys);
	}
	
	/**
	 * Create the Javascript code
	 */
	private function createJs($graphdata){
		
		$output = "<script>";
		$output .= "new Morris." . $this->getType() . "({";
		$output .= substr($graphdata, 1);
		$output .= ")</script>";
		
		return $output;
	}
	
	/**
	 * Plot the data on screen
	 */
	public function plot($debug=false){
		
		$graph = null;
			
		$this->getYkeysIndexes();
		$this->convertDataToJson();
		
		$graph['element'] = $this->getElement();
		$graph['xkey'] = $this->getXkey();
		$graph['ykeys'] = $this->getYkeys();
		$graph['labels'] = $this->getLabels();
		$graph['data'] = $this->getData();
		$graph['parseTime'] = $this->getParseTime();
		
		foreach ($this->getOptions() as $option => $value) {
			$graph[$option] = $value;
		}
		
		$jsondata = json_encode($graph);
		
		if($debug){
			Zend_Debug::dump(Shineisp_Commons_Utilities::jsonBeautifier($jsondata));
		}
		return $this->createJs($jsondata);
	}
	
					
}

?>