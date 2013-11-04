<?php
class Shineisp_Commons_Datagrid {
	protected $begin;
	protected $end;
	protected $massactions;
	protected $controller;
	protected $statuses;
	protected $id;
	protected $title;
	protected $css;
	protected $currentaction;
	protected $data = array ();
	protected $multiselect;
	protected $caption;
	protected $height;
	protected $rownum;
	protected $rowlist;
	protected $viewrecords;
	protected $sortname;
	protected $sortorder;
	protected $method = "post";
	protected $hiddencols = array();
	protected $hasActions = false;
	protected $hasfilter = false;
	protected $hassubrecords = false;
	protected $action;
	protected $currentpage;
	protected $paging;
	protected $basepath;
	protected $recordcount;
	protected $translator;
	protected $temp = array ();
	protected $columns = array ();

	public function __construct() {
		$this->height = "250";
		$this->id = "itemlist";
		$this->css = "grid";
		$this->title = "";
		$this->multiselect = "true";
		$this->viewrecords = true;
		$this->hiddencols = array();
		$this->hasActions = false;
		$this->hassubrecords = false;
		$this->sortname = "";
		$this->sortorder = "desc";
		$this->massactions = array ();
		$this->statuses = array ();
		$this->paging = "";
		$this->recordcount = 0;
		$this->rownum = 10;
		$this->currentpage = 1;
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->currentaction = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		$this->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->rowlist = array ('10', '50', '100', '1000' );
	}
	
	/**
	 * @return the $css
	 */
	public function getCss() {
		return $this->css;
	}

	/**
	 * @param field_type $css
	 */
	public function setCss($css) {
		$this->css = $css;
		return $this;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return the $hiddencols
	 */
	public function getHiddencols() {
		return $this->hiddencols;
	}

	/**
	 * @param field_type $hiddencols
	 */
	public function setHiddencols($hiddencols) {
		$this->hiddencols = $hiddencols;
		return $this;
	}
	
	/*
	 * Set the filterable form 
	 */
	public function setFilter($action, $method = "POST") {
		$this->hasfilter = true;
		$this->method = $method;
		$this->action = $this->basepath . $action;
		return $this;
	}
	
	/**
	 * @return the $hasActions
	 */
	public function getHasActions() {
		return $this->hasActions;
	}

	/**
	 * @param field_type $hasActions
	 */
	public function setHasActions($hasActions) {
		$this->hasActions = $hasActions;
		return $this;
	}

	/*
	 * Set the total of the records per page 
	 */
	public function setRowlist($rowlist) {
		$this->rowlist = $rowlist;
		return $this;
	}
	
	/*
	 * Set the total of the records per page 
	 */
	public function setRownum($rownum) {
		$this->rownum = $rownum;
		return $this;
	}
	
	/*
	 * Check if some field is filterable attached
	 */
	public function hasFilters() {
		foreach ( $this->columns as $column ) {
			if (! empty ( $column ['filterable'] ) && $column ['filterable']) {
				return true;
			}
		}
		return false;
	}
	
	/*
	 * Check if the grid has some custom mass actions enabled 
	 */
	public function hasMassActions() {
		if (! empty ( $this->massactions ) && count ( $this->massactions ) > 0) {
			return true;
		}
		return false;
	}
	
	/*
	 * Set the total of the records 
	 */
	public function setRecordcount($recordcount) {
		$this->recordcount = $recordcount;
		return $this;
	}
	
	/*
	 * Set the paging object 
	 */
	public function setPaging($paging) {
		$this->paging = $paging;
		return $this;
	}
	
	/*
	 * Set the default path of the links of the grid 
	 */
	public function setBasePath($basepath) {
		$this->basepath = $basepath;
		return $this;
	}
	
	/*
	 * Set the default sort of the grid 
	 */
	public function setSort($name, $sort = "desc") {
		$this->sortname = $name;
		$this->sortorder = $sort;
		return $this;
	}
	
	/*
	 * Set the record data of the grid
	 */
	public function setData(Doctrine_Query $dq, $page, $rowNum, array $arrSort, array $filters) {
		$this->getRecords ( $dq, $page, $rowNum, $arrSort, $filters );
		return $this;
	}
	
	/*
	 * Set the record data of the grid using a multidimentional array
	 */
	public function setArrayData($arraydata, $page=1, $rowNum=10, array $arrSort = array(), array $filters = array()) {
		$this->data = $arraydata;
		$this->paging = "";
		$this->recordcount = count ( $arraydata );
		return $this;
	}
	
	/*
	 * Set the name of the grid
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	/*
	 * Set if the dataset has a subrecords
	 */
	public function setHasSubrecords($value) {
		$this->hassubrecords = $value;
		return $this;
	}
	
	/*
	 * Set the mass actions of the grid
	 */
	public function setMassActions(array $actions) {
		$this->massactions = $actions;
		return $this;
	}
	
	/*
	 * Set the statuses of the grid
	 */
	public function setStatuses(array $statuses) {
		$this->statuses = $statuses;
		return $this;
	}
	
	/*
	 * Add a column to the main structure
	 */
	public function addColumn($column) {
		$this->columns [] = $column;
		return $this;
	}
	
	/*
	 * Set the columns structure
	 */
	public function addColumns($columns) {
		$this->columns = $columns;
		return $this;
	}
	
	/*
	 * Set the current page
	 */
	public function setCurrentPage($page) {
		$this->currentpage = $page;
		return $this;
	}
	
	/*
     * addMultiselect
     * set the checkbox for each row
     */
	public function addMultiselect($indexfield) {
		$column = array ('type' => 'checkbox', 'alias' => $indexfield, 'attributes' => array ('width' => 20, 'class' => 'checkbox' ), 'label' => '<input type="checkbox" class="selectall" />' );
		array_unshift ( $this->columns, $column );
		return $this;
	}
	
	/*
     * adddatagridActions
     * set the edit and delete action links
     */
	public function adddatagridActions(array $actions, $indexfield) {
		$links = array ();
		if (!empty($indexfield) && count ( $actions ) > 0) {
			foreach ( $actions as $item ) {
				$links [] = '<a title="' . $item ['label'] . '" class="actions ' . $item ['cssicon'] . '" href="' . $item ['action'] . '"></a>';
			}
			$this->addColumn ( array ('label' => 'Actions', 'alias' => $indexfield, 'type' => 'link', 'pattern' => $links ) );
			$this->setHasActions(true); 
		}
		
		return $this;
	}
	
	/**
	 * create
	 * Create the data grid table
	 * @return string
	 */
	public function create() {
		
		$table = $this->Begin ();
		$table .= $this->Header ();
		$table .= "<thead>";
		$table .= $this->addTitle ();
		$table .= $this->addHeaderControls ();
		$table .= $this->setHeaderColumns ();
		$table .= $this->setFilters ();
		$table .= "</thead>";
		$table .= $this->attachData ();
		$table .= $this->addFooter ();
		$table .= $this->End ();
		
		return $table;
	}
	
	/*
	 * Begin
	 * html to inject begin the table
	 */
	private function Begin() {
		$this->begin .= '<div id="alert" style="display:none"></div>';
		return $this->begin;
	}
	
	/*
	 * start
	 * start section of the table
	 */
	private function Header() {
		$head = "";
		if ($this->hasfilter) {
			$head .= $this->createForm ();
		}
		$head .= "<table id=\"".$this->id."\" class=\"".$this->css."\">";
		return $head;
	}
	
	/*
	 * end
	 * end section of the table
	 */
	private function End() {
		$this->end .= "</table>";
		$this->end .= $this->hasfilter ? "</form>" : "";
		return $this->end;
	}
	
	/*
	 * createForm
	 * create the header of the filter form
	 */
	private function createForm() {
		$form = '<form name="filterform" id="filterform" action="' . $this->basepath . $this->action . '" method="' . $this->method . '">';
		return $form;
	}
	
	/*
	 * addPaging
	 * create the pagination of the records
	 */
	private function addPaging() {
		return $this->paging;
	}
	
	/*
	 * addSort
	 * create the sort link
	 */
	private function addSort($column) {
		$sorts = Zend_Controller_Front::getInstance ()->getRequest ()->getParam ( 'sort' );
		if (! empty ( $column ['sortable'] ) && $column ['sortable']) {
			if(is_array($sorts)){
				$sort = explode ( ",", $sorts[0] );
			}else{
				$sort = explode ( ",", $sorts );
			}
			
			$class = "";
			$direction = ! empty ( $column ['direction'] ) ? $column ['direction'] : "asc";
			$icon = "";
			
			if (! empty ( $column ['alias'] )) {
				
				if ($sort [0] == $column ['field']) {
					if (! empty ( $sort [1] ) && $sort [1] == "asc") {
						$direction = "desc";
					} else {
						$direction = "asc";
					}
					$icon = '<span class="' . $direction . '">&nbsp;</span>';
					$class = "selected";
				}
				$data = '<a class="' . $class . '" href="' . $this->basepath . $this->currentaction . '/page/' . $this->currentpage . '/sort/' . $column ['field'] . ',' . $direction . '">' . $column ['label'] . ' ' . $icon . '</a>';
			} else {
				$data = $column ['label'];
			}
		
		} else {
			$data = $column ['label'];
		}
		return $data;
	}
	
	/**
	 * addRowSummary
	 * Create the summary of the grid
	 * In the column configuration array you have to add for instance:
	 * 'actions'=>array('CLASSNAME'=>'METHOD/FUNCTION') 
	 * The class must be a STATIC Class 
	 */
	private function addRowSummary() {
		$html = "";
		$value = "-";
		foreach ( $this->temp as $tempcolumn ) {
			$html .= "<tr>";
			foreach ( $this->columns as $column ) {
				if ($column ['alias'] == key ( $tempcolumn )) {
					
					if (class_exists ( key ( $tempcolumn [key ( $tempcolumn )] ['action'] ) )) {
						$class = key ( $tempcolumn [key ( $tempcolumn )] ['action'] );
						$method = $tempcolumn [key ( $tempcolumn )] ['action'] [$class];
						$values = $tempcolumn [key ( $tempcolumn )] ['values'];
						$value = call_user_func ( array ($class, $method ), $values );
					}
					
					$html .= "<td>" . $value . "</td>";
				} else {
					$html .= "<td>&nbsp;</td>";
				}
			}
			$html .= "</tr>";
		}
		return $html;
	}
	
	/*
	 * addFooter
	 * create the footer
	 */
	private function addFooter() {
		$colnum = count ( $this->columns );
	
		if ($this->hassubrecords) {
			$colnum++;	
		}
		
		$footer = "<tfoot>";
		
		// Add the summarize row
		$footer .= $this->addRowSummary ();
		if(!empty($this->paging)){
			$footer .= "<tr>";
			$footer .= "<td colspan='" . $colnum . "'>";
			$footer .= "<div class='pagination'>" . $this->paging . "</div>";
			$footer .= "</td>";
			$footer .= "</tr>";
			$footer .= "</tfoot>";
		}
		return $footer;
	}
	
	/*
	 * addTitle
	 * add the title of the grid
	 */
	private function addTitle() {
		$data = "";
		
		if(!empty($this->title) || $this->hasFilters ()){
			
			if(!empty($this->title)){
				$data = "<caption>";
				$data .= '<div class="title left">' . $this->title . '</div>';
				$data .= "</caption>";
			}
			
			
		}
		return $data;
	}
	
	/*
	 * addHeaderControls
	 * add the header button in order to control the grid
	 */
	private function addHeaderControls() {
	    $paginator = $this->addPaginator ();
	    $massactions = $this->addmassActions ();
	    $row = null;
	    
	    if(!empty($this->rowlist) && $this->hasMassActions ()){
    		$row = "<tr>";
    		$colnum = count ( $this->columns );
    		if ($this->hassubrecords) {
    			$colnum++;	
    		}
    		
    		$row .= "<th class='topgrid_header' colspan='" . $colnum . "'>";
    		$row .= "<table class='topgrid'><tr>";
    		
    		if(!empty($this->rowlist)){
    			$row .= "<td class=\"paginator_cell\">$paginator</td>";
    		}
    		
    		if ($this->hasMassActions ()) {	
    			$row .= "<td class=\"massaction_cell\" style=\"text-align:right\">$massactions</td>";
    		}
    						
    		$row .= "</tr></table>";
    		
    		$row .= "</th>";
    		$row .= "</tr>";
	    }
		return $row;
	}
	
	/*
	 * HeaderColumns
	 * create the header of the table
	 */
	private function setFilters() {
		$columns = "";
		if ($this->hasfilter) {
			$columns = "<tr class=\"filters\">";
			for($i = 0; $i < count ( $this->columns ); $i ++) {
				if(!empty($this->columns[$i]['type']) && $this->columns[$i]['type'] == "link"){
					if ($this->hasFilters ()) {
						$columns .= "<th>";
						$columns .= '<input type="submit" value="' . $this->translator->translate ( 'Search' ) . '">';
						$columns .= "</th>";
					}
				}else{
					$columns .= "<th>";
					$columns .= $this->addfilterField ( $i );
					$columns .= "</th>";
				}
			}
			
			$columns .= "</tr>";
		}
		return $columns;
	}
	
	/*
	 * create the columns
	 */
	private function setColumns() {
		$columns = "";
		for($i = 0; $i < count ( $this->columns ); $i ++) {
			if(!in_array($this->columns[$i]['alias'], $this->getHiddencols() )){
				if(!empty($this->columns[$i]['class'])){
					$columns .= "<col class='" . $this->columns[$i]['class'] . "'/>";
				}else{
					$columns .= "<col />";
				}
			}
		}
		return $columns;
	}
	
	/*
	 * HeaderColumns
	 * create the header of the table
	 */
	private function setHeaderColumns() {
		$columns = "<tr class=\"headings\">";
		for($i = 0; $i < count ( $this->columns ); $i ++) {
			if(!empty($this->columns[$i]['alias'])){
				// Check if the column has been set as hidden
				if(!in_array($this->columns[$i]['alias'], $this->getHiddencols() )){
					$columns .= "<th ";
					$columns .= $this->addAttrColumns ( $i );
					$columns .= ">";
					if (! empty ( $this->columns [$i] ['label'] )) {
						$columns .= $this->addSort ( $this->columns [$i] );
					}
					$columns .= "</th>";
				}elseif($this->columns[$i]['type'] == "link"){
					$columns .= "<th>Actions</th>";	
				}
			}else{
				$columns .= "<th ";
				$columns .= $this->addAttrColumns ( $i );
				$columns .= ">";
				if (! empty ( $this->columns [$i] ['label'] )) {
					$columns .= $this->addSort ( $this->columns [$i] );
				}
				$columns .= "</th>";
			}
		}
		
		if ($this->hassubrecords) {
			$columns .= "<th>Actions</th>";	
		}
		$columns .= "</tr>";
		
		return $columns;
	}
	
	/*
     * addAttrColumns
     * add the attributes at the cells header of the table
     */
	private function addAttrColumns($index) {
		if (! empty ( $this->columns [$index] ['attributes'] )) {
			return $this->createAttributes ( $this->columns [$index] ['attributes'] );
		} else {
			return null;
		}
	}
	
	/*
     * createAttributes
     * create the html attribute
     */
	private function createAttributes($attributes) {
		$attrs = array ();
		
		if (! empty ( $attributes )) {
			foreach ( $attributes as $attribute => $value ) {
				$attrs [] = $attribute . " = '" . $value . "'";
			}
		}
		return implode ( " ", $attrs );
	}
	
	/*
     * addfilterField
     * add the filter fields at the cells header of the table
     */
	private function addfilterField($index) {
		$field = "";
		if (! empty ( $this->columns [$index] ['filterable'] ) && ! empty ( $this->columns [$index] ['type'] )) {
			$filterable = $this->columns [$index] ['filterable'];
			$name = ! empty ( $this->columns [$index] ['alias'] ) ? $this->columns [$index] ['alias'] : "noname";
			$indexkey = ! empty ( $this->columns [$index] ['index'] ) ? $this->columns [$index] ['index'] : $name;
			$filterdata = ! empty ( $this->columns [$index] ['filterdata'] ) ? $this->columns [$index] ['filterdata'] : array ();
			$type = $this->columns [$index] ['type'];
			
			if ($filterable) {
				if ($type == "date") {
					$field = "<input id='" . $indexkey . "-from' name='" . $name . "[]' title='from:' class='input_date date' type='text' size='15'><br/>";
					$field .= "<input id='" . $indexkey . "-to' name='" . $name . "[]' title='to:' class='input_date date' type='text' size='15'>";
				} elseif ($type == "index") {
					$field = "<select id='" . $indexkey . "' name='" . $name . "' class='filter_select'>";
					$field .= "<option value=\"\"></option>";
					foreach ( $filterdata as $key => $value ) {
						$field .= "<option value='$key'>$value</option>";
					}
					$field .= "</select>";
				} else {
					$field = "<input id='" . $indexkey . "' name='" . $name . "' class='input' type='text'>";
				}
			}
		}
		return $field;
	}
	
	/*
	 * load the data in the grid
	 */
	private function attachData() {
		$data = $this->data;
		$html = "<tbody>";
		$index = 0;
		if (count ( $this->data ) > 0) {
			foreach ( $this->data as $record ) {
				
				$html .= "<tr class='datarow'>";
				$hiddenCols = $this->getHiddencols();
				$colindex = 0;
				
				foreach ( $this->columns as $column ) {
					if(!empty($column['alias'])){
						if(in_array($column['alias'], $hiddenCols)){
							if(!empty($column['type'])) {
								if($column['type'] == "link"){
									$html .= "<td ". $this->addAttrColumns ( $colindex ) .">";
									$html .= $this->addObject ( $record, $column );
									$html .= "</td>";
								}
							}
						}else {
							$html .= "<td ". $this->addAttrColumns ( $colindex ) .">";
							$html .= $this->addObject ( $record, $column );
							$html .= "</td>";
						}
					}else{
						$html .= "<td ". $this->addAttrColumns ( $colindex ) .">";
						$html .= $this->addObject ( $record, $column );
						$html .= "</td>";
					}
					$colindex++;
				}
				
				if ($this->hassubrecords) {
					$html .= "<td><span onclick=\"$('#".$this->id."_subdata_$index').toggle();\">" . $this->translator->translate ('more information') . "</span></td>";	
				}
				
				$html .= "</tr>";
				
				if (!empty($record ['subrecords']) && $this->hassubrecords) {
					$html .= $this->subRecords ( $index, $record ['subrecords'], $html );
				}
				$index++;
			}
		} else {
			$colnum = count ( $this->columns );
			$html .= "<tr><td colspan='" . $colnum . "'><center>" . $this->translator->translate ( 'No records found' ) . "</center></td></tr>";
		}
		
		$html .= "</tbody>";
		return $html;
	}
	
	/**
	 * Create the subrows in a table
	 * 
	 * @param integer $index
	 * @param array $records
	 * @param string $html
	 */
	private function subRecords($index, array $records, $html = "") {
		$columns = array();
		$class = "odd";
		$cols = count($this->columns)+1;
		
		$html = "<tr class='hidden' id='".$this->id."_subdata_$index'><td colspan='".$cols."'><table id='".$this->id."_subgrid' class='subgrid subgrid_".$this->id."'>";
		
		// Get the columns headers
		foreach(array_keys($records[0]) as $column){
			$columns[] = array('alias' => $column, 'attributes' => array('width'=>10)); 
		}
			
		$html .= $this->SubHeaderColumns($columns);
		
		foreach ( $records as $record ) {
			
			$class = ($class == "even") ? "odd" : "even";
			$html .= "<tr class='datarow pointer " . $class . "'>";
				
			if (! empty ( $record ['subrecords'] )) {
				$html .= $this->subRecords ( $record ['subrecords'] );
			}
			
			foreach ( $columns as $column ) {
				$html .= "<td class='" . $column ['alias'] . "'>";
				$html .= $this->addObject ( $record, $column );
				$html .= "</td>";
			}
			$html .= "</tr>\n";
		}
		$html .= "</table>";
		
		return $html;
	}
	
	/*
	 * SubHeaderColumns
	 * create the sub header of the table
	 */
	private function SubHeaderColumns($columns) {
		$html = "<tr class=\"headings\">";
		foreach($columns as $column) {
			$html .= "<th " . $this->createAttributes ($column['attributes']) . ">";
			$html .= $this->translator->translate (ucwords(str_replace("_", " ", $column['alias'])));
			$html .= "</th>";
		}
		$html .= "</tr>";
		return $html;
	}	
	
	/*
     * addPaginator
     */
	private function addPaginator() {
		$data = '<span class="pagingtools" />';
		$data .= $this->translator->translate ( 'Page' ) . " " . $this->currentpage . " | ";
		$data .= $this->translator->translate ( 'View' ) . " ";
		$data .= '<select id="" name="" onChange="location.href=\'' . $this->basepath . 'recordsperpage/id/\'+this.value">';
		$data .= '<option></option>';
		foreach ( $this->rowlist as $rowitem ) {
			$selected = ($this->rownum == $rowitem) ? "selected" : "";
			$data .= '<option ' . $selected . ' value="' . $rowitem . '">' . $rowitem . '</a> ';
		}
		$data .= '</select> | ';
		$data .= $this->translator->_ ( 'Total %s records found', $this->recordcount );
		$data .= '</span>';
		return $data;
	}
	
	/*
     * addmassActions
     */
	private function addmassActions() {
		$data = "";
		$data .= $this->translator->translate ( 'Actions' );
		$data .= ' <select name="actions" id="actions">';
		$data .= '<option value="">' . $this->translator->translate ( 'Select ...' ) . '</option>';
		
		if (! empty ( $this->massactions )) {
			$data .= '<optgroup label="' . $this->translator->translate ( 'Actions' ) . '">';
			foreach ( $this->massactions as $action => $label ) {
				$data .= '<option value="' . $action . '">' . $this->translator->translate ( $label ) . '</option>';
			}
		}
		
		if (! empty ( $this->statuses )) {
			$data .= '<optgroup label="' . $this->translator->translate ( 'Statuses' ) . '">';
			foreach ( $this->statuses as $status => $label ) {
				$data .= '<option value="set_statuses&status=' . $status . '">' . $this->translator->translate ( $label ) . '</option>';
			}
		}
		$data .= '</select> ';
		$data .= '<input type="button" rel="' . $this->controller . '" id="bulkactions" value="' . $this->translator->translate ( 'Execute' ) . '"><br/>';
		return $data;
	}
	
	/*
	 * addObject
	 */
	private function addObject($record, $column) {
		$attributes = "";
		
		if (! empty ( $column ['attributes'] )) {
			$attributes = $this->createAttributes ( $column ['attributes'] );
		}
		
		if (! empty ( $column ['actions'] ) && !empty ( $record [$column ['alias']] )) {
			$this->temp ['column'] [$column ['alias']] ['action'] = $column ['actions'];
			$this->temp ['column'] [$column ['alias']] ['values'] [] = $record [$column ['alias']];
		}
		
		if (! empty ( $column ['type'] )) {
			if ($column ['type'] == "checkbox") {
				if (! empty ( $record [$column ['alias']] )) {
					return "<input type='checkbox' name='item[]' " . $attributes . " value='" . $record [$column ['alias']] . "' />";
				} else {
					return null;
				}
			} elseif ($column ['type'] == "arraydata" && !empty($column ['run']) && !empty($column ['index'])) { 
				
				$class = key($column ['run']);
				$method = $column ['run'][$class];
				
				// Execute a method and write the result in the cell
				// Alias must be the parameter that you'd like to pass to the actions
				if(class_exists($class) && method_exists($class, $method)){
					return $class::$method($record [$column ['index']]);	
				}
				
			} elseif ($column ['type'] == "link") {
				$links = array ();
				if (! empty ( $column ['pattern'] ) && is_array ( $column ['pattern'] )) {
					foreach ( $column ['pattern'] as $pattern ) {
						$links [] = sprintf ( $pattern, $record [$column ['alias']] );
					}
					return implode ( "&nbsp;", $links );
				}
			}
		}
		if (!empty ( $column ['alias'] )) {
			if (isset ( $record [$column ['alias']] )) {
				return $record [$column ['alias']];
			}
		} else {
			return "field not found";
		}
	
	}
	
	/**
	 * getRecords
	 * Get records from the DB
	 * @return mixed
	 */
	public function getRecords(Doctrine_Query $dq, $currentPage = 1, $rowNum = 2, array $sort = array(), array $where = array()) {

		$uri = "";
		$module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		
		// Defining the url sort
		if(!empty($sort)){
			foreach ($sort as $s){
				$arrSort[] = "/sort/" . str_replace(" ", ",", $s);
			}
			
			$arrSort = array_unique($arrSort);
			$uri = implode("", $arrSort);
		}
		$pagerLayout = new Doctrine_Pager_Layout ( new Doctrine_Pager ( $dq, $currentPage, $rowNum ), new Doctrine_Pager_Range_Sliding ( array ('chunk' => 10 ) ), "/$module/$controller/list/page/{%page_number}" . $uri );
		
		// Get the pager object
		$pager = $pagerLayout->getPager ();
		
		// Set the Order criteria
		if(!empty($sort)){
			$pager->getQuery ()->orderBy ( implode(", ", $sort) );
		}
		
		if (isset ( $where ) && is_array ( $where )) {
			foreach ( $where as $filters ) {
				if (isset ( $filters [0] ) && is_array ( $filters [0] )) {
					foreach ( $filters as $filter ) {
						$method = $filter ['method'];
						$value = $filter ['value'];
						$criteria = $filter ['criteria'];
						$pager->getQuery ()->$method ( $criteria, $value );
					}
				} else {
					$method = $filters ['method'];
					$value = $filters ['value'];
					$criteria = $filters ['criteria'];
					$pager->getQuery ()->$method ( $criteria, $value );
				}
			}
		}
		
		$pagerLayout->setTemplate ( '<a href="{%url}">{%page}</a> ' );
		$pagerLayout->setSelectedTemplate ( '<a class="active" href="{%url}">{%page}</a> ' );
		
		$this->data = $pagerLayout->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		$this->paging = $pagerLayout->display ( null, true );
		$this->recordcount = $dq->count ();
		#print_r ( $this->data );
		return $this;
	}

}