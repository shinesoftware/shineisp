<?php

class Shineisp_Commons_Widgets{
	
	protected $id;
	protected $grid;
	protected $idxfield;
	protected $records;
	protected $basepath;
	protected $label;
	protected $icon;
	protected $columns;
	protected $buttons = array();

	public function __construct(){
		$this->grid = new Shineisp_Commons_Datagrid ();
	}
	
	/**
	 * @return the $icon
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @param field_type $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
		return $this;
	}

	/**
	 * @return the $idxfield
	 */
	public function getIdxfield() {
		return $this->idxfield;
	}

	/**
	 * @param field_type $idxfield
	 */
	public function setIdxfield($idxfield) {
		$this->idxfield = $idxfield;
		return $this;
	}

	/**
	 * @return the $columns
	 */
	public function getColumns() {
		return $this->columns;
	}
	
	/**
	 * Add a column to the grid
	 * 
	 * @param string $field
	 * @param string $label
	 * @param array $attributes
	 * @return Shineisp_Commons_Widgets
	 */
	public function setColumn($field, $label = null, $attributes = array()) {
		$this->columns [] = array ('label' => $label,
									'attributes' => !empty($attributes) ? $attributes : null,
									'field' => $field,
									'alias' => $field,
									'type' => 'string' );
		return $this;
	}	
	
	/**
	 * Set all the columns
	 * 
	 * @param unknown_type $columns
	 */
	public function setColumns(array $columns) {
		$this->columns = $columns;
		return $this;
	}	
	
	/**
	 * @return the $grid
	 */
	public function getGrid() {
		return $this->grid;
	}

	/**
	 * @param field_type $grid
	 */
	public function setGrid($grid) {
		$this->grid = $grid;
		return $this;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return the $records
	 */
	public function getRecords() {
		return $this->records;
	}

	/**
	 * @return the $basepath
	 */
	public function getBasepath() {
		return $this->basepath;
	}

	/**
	 * @return the $label
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @return the $buttons
	 */
	public function getButtons() {
		return $this->buttons;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param field_type $records
	 */
	public function setRecords($records) {
		$this->records = $records;
		return $this;
	}

	/**
	 * @param field_type $basepath
	 */
	public function setBasepath($basepath) {
		$this->basepath = $basepath;
		return $this;
	}

	/**
	 * @param field_type $label
	 */
	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	/**
	 * @param multitype: $buttons
	 */
	public function setButtons($buttons) {
		$this->buttons = $buttons;
		return $this;
	}
	
	/**
	 * Prepare the template
	 */
	private function createTemplate(){
		$template = '<div class="widget-wrapper">
						<div class="widget-header"><i class="pull-right fa fa-undo refresh"></i><h4><a href="{basepath}"><i class="{icon}"></i> {header}</a> </h4></div>
						<div class="widget-content">{content}</div>
					<div>';
		
		return $template;
	}

	public function create(){
		$html = $this->createTemplate();
		$data = $this->getRecords();
		
		if(!empty($data)){
			$grid = $this->grid->addColumns ( $this->getColumns() )
							->setCss('table table-striped table-hover')
							->setBasePath($this->getBasepath())
							->setRowlist(array())
							->adddatagridActions ( $this->getButtons(), $this->getIdxfield())
							->setArrayData ( $data )
							->create();
			
			
			$html = str_replace("{content}", $grid, $html);
		}
		
		$html = str_replace("{header}", $this->getLabel(), $html);
		$html = str_replace("{icon}", $this->getIcon(), $html);
		$html = str_replace("{basepath}", $this->getBasepath(), $html);
	
		return $html;
	}
	 
}