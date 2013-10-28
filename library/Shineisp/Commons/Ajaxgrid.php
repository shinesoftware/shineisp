<?php
class Shineisp_Commons_Ajaxgrid {
	protected $begin;
	protected $end;
	protected $controller;
	protected $config;
	protected $module;
	protected $id;
	protected $title;
	protected $css;
	protected $currentaction;
	protected $caption;
	protected $method = "post";
	protected $hiddencols = array();
	protected $action;
	protected $script;
	protected $jsbeforeinject;
	protected $massactions;
	protected $statuses;
	protected $jsinject;
	protected $jsendinject;
	protected $translator;
	protected $addfooterfilters;
	
	protected $scriptoptions = array ();
	protected $temp = array ();
	protected $columns = array ();

	public function __construct() {
		
		// default parameters
		$this->scriptoptions['bRetrieve'] = true;
		
		$this->id = "itemlist";
		$this->addfooterfilters = false;
		$this->css = "table table-striped ";
		$this->script = "";
		$this->title = "";
		$this->hiddencols = array();
		$this->massactions = array ();
		$this->statuses = array ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->currentaction = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		$this->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
	}
	
	/**
	 * create
	 * Create the data grid table
	 * @return string
	 */
	public function create() {
	
	    $this->Script ();
	    $table = $this->Begin ();
	    $table .= $this->Header ();
	    $table .= "<thead>";
	    $table .= $this->addTitle ();
	    $table .= $this->setHeaderColumns ();
	    $table .= "</thead>";
	    $table .= $this->addFooter ();
	    $table .= $this->End ();
	
	    return $table;
	}
	
	/**
	 * @return the $config
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * @param field_type $config
	 */
	public function setConfig($config) {
		$this->config = $config;
		return $this;
	}
	
	/**
	 * @return the $massactions
	 */
	public function getMassactions() {
		return $this->massactions;
	}

	/**
	 * @return the $statuses
	 */
	public function getStatuses() {
		return $this->statuses;
	}

	/**
	 * @param multitype: $massactions
	 */
	public function setMassactions($massactions) {
		$this->massactions = $massactions;
		return $this;
	}

	/**
	 * @param multitype: $statuses
	 */
	public function setStatuses($statuses) {
		$this->statuses = $statuses;
		return $this;
	}

	/**
	 * @return the $jsinject
	 */
	public function getJsinject() {
		return $this->jsinject;
	}

	/**
	 * @param field_type $jsinject
	 */
	public function setJsinject($jsinject) {
		$this->jsinject = $jsinject;
		return $this;
	}

	/**
	 * @return the $jsbeforeinject
	 */
	public function getJsbeforeinject() {
		return $this->jsbeforeinject;
	}

	/**
	 * @return the $jsendinject
	 */
	public function getJsendinject() {
		return $this->jsendinject;
	}

	/**
	 * @param field_type $jsbeforeinject
	 */
	public function setJsbeforeinject($jsbeforeinject) {
		$this->jsbeforeinject = $jsbeforeinject;
		return $this;
	}

	/**
	 * @param field_type $jsendinject
	 */
	public function setJsendinject($jsendinject) {
		$this->jsendinject = $jsendinject;
		return $this;
	}

	/**
	 * @return the $scriptoptions
	 */
	public function getScriptOptions() {
		return $this->scriptoptions;
	}

	/**
	 * @param field_type $scriptoptions
	 */
	public function setScriptOptions($scriptoptions) {
		$this->scriptoptions = $scriptoptions;
		return $this;
	}
	
	/**
	 * @return the $script
	 */
	public function getScript($tiny=false) {
		if($tiny){
			return str_replace("\n", " ", $this->script);
		}else{
			return $this->script;
		}
	}

	/**
	 * @param field_type $script
	 */
	public function setScript($script) {
		$this->script = $script;
		return $this;
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
	
	/**
	 * @return the $hasActions
	 */
	public function getHasActions() {
		return $this->hasActions;
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
	 * Set the default sort of the grid 
	 */
	public function setSort($name, $sort = "desc") {
		$this->sortname = $name;
		$this->sortorder = $sort;
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
	 * Begin
	 * html to inject begin the table
	 */
	private function Begin() {
		$this->begin .= '<div class="alert" id="mex" style="display:none"></div>';
		return $this->begin;
	}
	
	/*
	 * start
	 * start section of the table
	 */
	private function Header() {
		$head = "<table id=\"".$this->id."\" class=\"display ".$this->css."\">";
		return $head;
	}
	
	/*
	 * end
	 * end section of the table
	 */
	private function End() {
		$this->end .= "</table>";
		return $this->end;
	}
	
	/*
	 * Set the records within the grid
	*/
	public function loadRecords(array $params) {
		$config = $this->getConfig();
		
		$records = array();
		$rows = array();
		$iTotalRecords = 0;
		$iFilteredTotal = 0;
		$sOrder = "";

		// Get the common information in order to read the records and the columns of the list
		$columns = !empty($config['datagrid'] ['columns']) ? $config['datagrid'] ['columns'] : null;
		$dq = !empty($config['datagrid'] ['dqrecordset']) && is_object($config['datagrid']['dqrecordset']) ? $config['datagrid'] ['dqrecordset'] : null;
		$recordset = !empty($config ['datagrid']['recordset']) ? $config ['datagrid']['recordset'] : null;
		
		// Check if the doctrine object is active
		if(!empty($dq) && is_object($dq)){
			$iTotalRecords = $dq->count();
			$iFilteredTotal = $dq->count();
			
			$mainsearchvalue = !empty($params['sSearch']) ? $params['sSearch'] : null;
			if(!empty($params['iColumns'])){
				// Filter the records per each column field
				for ( $i=0 ; $i<intval( $params['iColumns'] ) ; $i++ ){
					
					if ( $params[ 'bSearchable_'.$i] == "true" ){
							
						$colsearchvalue = !empty($params['sSearch_' . $i]) ? $params['sSearch_' . $i] : null;
						if(!empty($columns[$i]['field'])){
							if($mainsearchvalue){
								if(Shineisp_Commons_Utilities::is_valid_date($mainsearchvalue)){
									$mainsearchvalue = Shineisp_Commons_Utilities::formatDateIn($mainsearchvalue);
									$dq->orWhere($columns[$i]['field'] . " = ?", $mainsearchvalue);
								}else{
									$dq->orWhere($columns[$i]['field'] . " like ?", "%$mainsearchvalue%");
								}
							}else{
								if($colsearchvalue){
									if(Shineisp_Commons_Utilities::is_valid_date($colsearchvalue)){
										$colsearchvalue = Shineisp_Commons_Utilities::formatDateIn($colsearchvalue);
										$dq->andWhere($columns[$i]['field'] . " = ?", $colsearchvalue);
									}else{
										$dq->andWhere($columns[$i]['field'] . " like ?", "%$colsearchvalue%");
									}
								}
							}
						}
					}
				}
			}
			
			$query = $dq->getSqlQuery();
			
			// Count the filtered records
			$temprs = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			
			$iFilteredTotal = count($temprs);
			
			// Paging of the records
			if ( isset( $params['iDisplayStart'] ) && $params['iDisplayLength'] != '-1' ){
				$dq->offset($params['iDisplayStart']);
				$dq->limit($params['iDisplayLength']);
			}
			
			// Sorting of the records
			if ( isset( $params['iSortCol_0'] ) ){
				for ( $i=0 ; $i<intval( $params['iSortingCols'] ) ; $i++ ){
					$j = 0;
					
					if ( $params[ 'bSortable_'.intval($params['iSortCol_'.$i]) ] == "true" ){
						foreach ( $columns as $column ){
							if(!empty($column['sortable']) && $column['sortable']) {
								if($j == ($params['iSortCol_0'] - 1)){
									$sOrder .= $column['field'] . " " . $params['sSortDir_'.$i] .", ";
								}
								$j++;
							}
						}
					}
				}
				
				$sOrder = substr_replace( $sOrder, "", -2 );
				if ( !empty($sOrder) ){
					$dq->orderBy($sOrder);
				}
			}
			

			#print_r($columns);
			#Zend_Debug::dump($sOrder);
			#print_r($dq->getDql());
			#Zend_Debug::dump($rs);
			#die;
			
			// Execute the doctrine object to get the record array
			$rs = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );

			// Complete the recordset with external values
			$rs = self::injectData($rs);

		}elseif(!empty($recordset) && is_array($recordset)){
			$iFilteredTotal = count ( $recordset );
			$rs = $recordset;
		}
		
		$i = 0;
		// For each record do ...
		foreach ( $rs as $record ) {
			$row = array();
			$row['Row'] = "row_$i";
			foreach ( $columns as $column ) {
				if(!empty($column['alias'])){
					$row[] = $record [$column ['alias']];
				}
			}
			$records[] = $row;
			$i++;
		}
		
		$output = array(
				"sEcho" => !empty($params['sEcho']) ? intval($params['sEcho']) : 0,
				"iTotalRecords" => $iTotalRecords,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => $records,
				"query" => $query,
		);
	
		die(json_encode($output));
		return $this;
	}
	
	
	/**
	 * Check for each column if there are external functions to be execute.
	 *
	 * @param unknown_type $records
	 * @return multitype:mixed
	 */
	private function injectData($records) {
		$config = $this->getConfig();
		$columns = !empty($config['datagrid'] ['columns']) ? $config['datagrid'] ['columns'] : null;
		$rs = array();
	
		foreach ( $records as $record ) {
				
			foreach ( $columns as $column ) {
				if (!empty($column ['type']) && $column ['type'] == "arraydata" && !empty($column ['run']) && !empty($column ['index'])) {
	
					$class = key($column ['run']);
					$method = $column ['run'][$class];
	
					// Execute a method and write the result in the cell
					// Alias must be the parameter that you'd like to pass to the actions
					if(class_exists($class) && method_exists($class, $method)){
						$record[$column ['alias']] = call_user_func("$class::$method", $record [$column ['index']]);
					}
				}
			}
			$rs[] = $record;
		}
	
		return $rs;
	}	
	
	/*
	 * parse columns
	 */
	private function parseColumns() {
		// Adding the index in all the rows
		$this->scriptoptions["fnRowCallback"] = "function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) { ";
		
		for($i = 0; $i < count ( $this->columns ); $i ++) {
			if(!empty($this->columns[$i]['attributes']['class'])){
				$this->scriptoptions["fnRowCallback"] .= "$('td:eq($i)', nRow).addClass( \"".$this->columns[$i]['attributes']['class']."\" );";
			}
		}
		
		$this->scriptoptions["fnRowCallback"] .= "var id = aData[0]; $(nRow).attr(\"id\", id);";
		$this->scriptoptions["fnRowCallback"] .= "return nRow;}";
		
		for($i = 0; $i < count ( $this->columns ); $i ++) {
			
			$item = array();
			
			// Adding the edit link
			if($i == 0){
				if(!empty($this->columns[$i]['type']) && $this->columns[$i]['type'] =="selectall"){
					$item = array("fnRender" => "function (o) {return '<input type=\"checkbox\" name=\"item[]\" value=\"'+o.aData[1] +'\">'}");
				}else{
					$item = array("fnRender" => "function (o) {return o.aData[0]; }");
				}
			}elseif($i == 1){
				$item['fnRender'] = "function (o) {return '<a class=\"editlink\" href=\"/" . $this->module . "/" . $this->controller . "/edit/id/' + o.aData[1] + '\">'+o.aData[1] +'</a>'}";
			}
			
			// Check the sortable property of the column 
			if(!empty($this->columns[$i]['searchable'])){
				$item['bSearchable'] = 'true';
			}else{
				$item['bSearchable'] = 'false';
			}
			
			// Check the searchable property of the column
			if(!empty($this->columns[$i]['sortable'])){
				$item['bSortable'] = 'true';
			}else{
				$item['bSortable'] = 'false';
			}
			
			$columns[] = $item;
		}
		
		$this->scriptoptions["aoColumns"] = $columns;
		
		return $this;
	}
	
	/**
	 * Enable or disable the display of a 'processing' indicator when the table is being processed (e.g. a sort). 
	 * This is particularly useful for tables with large amounts of data where it can take a noticeable amount of time to sort the entries.
	 * 
	 * @param boolean $state
	 */
	public function showProcessing($state=true) {
		$this->scriptoptions['bProcessing'] = $state;
		return $this;
	}
	
	/**
	 * Configure DataTables to use server-side processing. 
	 * Note that the sAjaxSource parameter must also be given in order to give DataTables a source to obtain the required data for each draw.
	 * 
	 * @param boolean $state
	 */
	public function isServerSide($uri, $state=true) {
		if(!empty($uri)){
			$this->scriptoptions['bServerSide'] = "'$state'";
			$this->scriptoptions['sAjaxSource'] = "\"$uri\"";
		}
		return $this;
	}
	
	/**
	 * Enable or disable state saving. When enabled a cookie will be used to save table display information such as pagination information, display length, filtering and sorting. 
	 * As such when the end user reloads the page the display will match what thy had previously set up.
	 * 
	 * @param boolean $state
	 */
	public function saveStateinCookies($state=true) {
		$this->scriptoptions['bStateSave'] = $state;
		return $this;
	}
	
	/**
	 * This would add a new header column and a new data column for each row with a checkbox
	 * 
	 * @param boolean $state
	 */
	public function setMultipleSelection($state=true) {
		$this->scriptoptions['multipleSelection'] = $state;
		return $this;
	}
	
	/**
	 * This method set the style of the paging
	 * 
	 * @param string $state [full_numbers, two_button]
	 */
	public function setPagingType($state="full_numbers") {
		$this->scriptoptions['sPaginationType'] = "'$state'";
		return $this;
	}
	
	/**
	 * This parameter allows you to readily specify the entries in the length drop down menu that DataTables shows when pagination is enabled. 
	 * It can be either a 1D array of options which will be used for both the displayed option and the value, or a 2D array which will use the array 
	 * in the first position as the value, and the array in the second position as the displayed options (useful for language strings such as 'All').
	 * 
	 * @param string $state [full_numbers, twobutton]
	 */
	public function setRowsList($rows = array ('10', '50', '100' ), $default=25) {
		
		if($rows){
			$sInfo = $this->translator->_('Got a total of _TOTAL_ entries to show (_START_ to _END_)');
			$sProcessing = $this->translator->_('Please wait, it is currently busy');
			$this->scriptoptions['oLanguage'] = "{\"sLengthMenu\": \"_MENU_\", \"sInfo\": \"$sInfo\", \"sProcessing\": \"$sProcessing\"}";
			$this->scriptoptions['aLengthMenu'] = "[[" . implode(",", $rows) . ",-1], [" . implode(",", $rows) . ",'" . $this->translator->translate('Show All') . "']]";
			$this->scriptoptions['iDisplayLength'] = "'$default'";
		}
		return $this;
	}
	
	
	/**
	 * Add the bulk actions and statuses action 
	 * 
	 * @param boolean $state
	 */
	public function addBulkActions() {
		$this->jsendinject .= '$("div.dataTables_length").before(\'<div id="bulkobject" class="dataTables_bulk">'.$this->addmassActions().'</div>\');';
		return $this;
	}
	
	
	/**
	 * Add the bulk actions and statuses action 
	 * 
	 * @param boolean $state
	 */
	public function addFooterFilters() {
		$this->jsbeforeinject .= 'var asInitVals = new Array();' . "\n";
		$this->addfooterfilters = true;
		$this->jsendinject .= '
				$("tfoot input").keyup( function (e) {
						if ( e.keyCode != 13 ) { return false; }
						/* Filter on the column (the index) of this element */
						oTable.fnFilter( this.value, $("tfoot input").index(this) + 1 );
					} );
					$("tfoot input").each( function (i) {
				        asInitVals[i] = this.value;
				    } );
				     
				    $("tfoot input").focus( function () {
				        if ( this.className == "search_init" )
				        {
				            this.className = "";
				            this.value = "";
				        }
				    } );
				     
				    $("tfoot input").blur( function (i) {
				        if ( this.value == "" )
				        {
				            this.className = "search_init";
				            this.value = asInitVals[$("tfoot input").index(this) + 1];
				        }
				   	});';
								
		return $this;
	}
	
	/*
	 * adding the script section
	 */
	private function Script() {
		$this->parseColumns();
		
		$this->script = "<script>\n$(document).ready(function() {\n";
			
			// Inject custom script
			$this->script .= $this->jsbeforeinject;
			
			// Create the table
			$this->script .= "var oTable = $('#".$this->id."').dataTable( {\n";
				
				// Adding the options
				foreach ($this->scriptoptions as $key => $option){
					if(is_array($option)){
						$this->script .= "\"$key\": [\n";
						
						foreach($option as $subkey => $suboption){
							$this->script .= "{";
							if(is_array($suboption)){
								foreach($suboption as $subsubkey => $subsuboption){
									$this->script .= "\"$subsubkey\": $subsuboption, ";
								}
							}else{
								$this->script .= "\"$subkey\": $suboption, \n";
							}
							$this->script .= "},\n";
						}
						$this->script .= "],\n";
					}else{
						$this->script .= "\"$key\": $option,\n";
					}
				}
				
				// Inject custom script
				$this->script .= $this->jsinject;
			
			$this->script .= "}).fnSetFilteringDelay(600);\n";
			
			// Inject custom script
			$this->script .= $this->jsendinject;
		
		$this->script .= "});\n";
		$this->script .= "</script>\n";
		
		return $this->script;
	}

	/*
	 * addFooter
	 * create the footer
	 */
	private function addFooter() {
		$footer = "<tfoot>";
		if($this->addfooterfilters){
			$footer .= $this->createFilters();
		}
		$footer .= "</tfoot>";
		return $footer;
	}

	/*
	 * addFooter
	 * create the footer
	 */
	private function createFilters() {
		$colnum = count ( $this->columns );
	
		$filters = "<tr>";
		$filters .= "<th></th>";
		for($i = 1; $i < count ( $this->columns ); $i ++) {
			$filters .= "<th ";
			$filters .= $this->addAttrColumns($i);
			$filters .= ">";
			$style = (empty($this->columns[$i]['searchable']) || $this->columns[$i]['searchable'] === false) ? "display:none" : Null;
			$value = (!empty($this->columns[$i]['searchable']) && $this->columns[$i]['searchable']) ? $this->translator->_('Search %s', $this->columns[$i]['label']) : Null;
			
			$filters .= "<input style=\"$style\" id=\"obj_".$this->columns[$i]['alias']."\" name=\"search_".$this->columns[$i]['alias']."\" title=\"". $value ."\" type=\"text\" class=\"search_init\">";
			$filters .= "</th>";
		}
		$filters .= "</tr>";
		return $filters;
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
	 * addmassActions
	*/
	private function addmassActions() {
		$data = "";
		
		if (empty ($this->massactions)) {
			return null;
		}
		
		$data .= '<div class="input-append"><select name="actions" id="actions">';
		$data .= '<option value="">' . $this->translator->translate ( 'Select action ...' ) . '</option>';
	
		foreach ($this->massactions as $name => $section){
			if(is_array($section)){
				$data .= '<optgroup label="' . $this->translator->translate ( ucfirst($name) ) . '">';
				foreach ($section as $action => $label){
					$data .= '<option value="' . $action . '">' . $this->translator->translate ( $label ) . '</option>';
				}
				$data .= '</optgroup>';
			}
		}
		
		$data .= '<input type="button" class="btn" rel="' . $this->controller . '" id="bulkactions" value="' . $this->translator->translate ( 'Execute' ) . '"></div>';
		return $data;
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
		$columns = "<tr>";
		for($i = 0; $i < count ( $this->columns ); $i ++) {
			if(!empty($this->columns[$i]['alias'])){
				// Check if the column has been set as hidden
				if(!in_array($this->columns[$i]['alias'], $this->getHiddencols() )){
					$columns .= "<th ";
					$columns .= $this->addAttrColumns ( $i );
					$columns .= ">";
					
					if(!empty($this->columns [$i] ['type'])){
						if($this->columns [$i] ['type'] == "selectall"){
							$columns .= "<input class=\"selectall\" type=\"checkbox\" value=\"\">";
						}else{
							$columns .= $this->columns [$i] ['label'];
						}
					}else{
						$columns .= $this->columns [$i] ['label'];
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
					$columns .= $this->columns [$i] ['label'];
				}
				$columns .= "</th>";
			}
		}
		$columns .= "</tr>";
		
		return $columns;
	}
	
	/*
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
     * create the html attribute
     */
	private function createAttributes($attributes) {
		$attrs = array ();
		
		if (! empty ( $attributes )) {
			foreach ( $attributes as $attribute => $value ) {
				$attrs [] = $attribute . "=\"" . $value . "\"";
			}
		}
		return implode ( " ", $attrs );
	}
	
	/*
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
	

}