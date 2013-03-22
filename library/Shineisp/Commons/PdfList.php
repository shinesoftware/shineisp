<?php

/*
 * Shineisp_Commons_Pdf
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Commons_Pdf
* Purpose:  Class for the pdf files
* -------------------------------------------------------------
*/

define ( 'GRID_TOP', 20 );
define ( 'GRID_HEIGHT_ROWS', 20);
define ( 'GRID_HEIGHT_BETWEEN_ROWS', 5 );

class Shineisp_Commons_PdfList extends Shineisp_Commons_Pdf {
	protected $grid;
	
	/**
	 * Construct function inizialize the class
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Main public function that create the pdf file
	 */
	public function create($data, $filename = "file.pdf", $show = true) {

		// Add the data
		$this->data = $data;
		
		// Create the pdf page
		$this->create_page();
		
		// Create the datagrid
		$this->create_grid();

		// Get PDF document as a string 
		$contents = $this->pdf->render ();
		header ( "Content-type: application/x-pdf" );
		
		$tmpname = Shineisp_Commons_Utilities::GenerateRandomString();
		@mkdir ( PUBLIC_PATH . "/tmp/");
		
		$file = fopen ( PUBLIC_PATH . "/tmp/" . $tmpname . ".pdf", 'a+' );
		fputs ( $file, $contents );
		fclose ( $file );
		
		return json_encode(array('url' => "/tmp/" . $tmpname . ".pdf"));
	}	
	
	
	/**
	 * Create the table list header
	 */
	private function create_table_header() {
		$column = $this->check_columns_size( $this->data ['columns'] );
		
		$top_table_position = $this->h - GRID_TOP; // Top position of the table in the document
		$z = PAGE_BOTH_MARGIN; // Incremental space between the columns
		$top_record_rows_position = $top_table_position - GRID_HEIGHT_ROWS; // Top position of the records table under the columns header

		$this->set_font_size ( Zend_Pdf_Font::FONT_HELVETICA_BOLD, 8 );
		
		// Header table creation
		for($i = 0; $i < count ( $column ); $i ++) {
			$size = $column [$i] ['size']; // Size of the single column
			$align = ! empty ( $column [$i] ['align'] ) ? $column [$i] ['align'] : ""; // Alignment of the column
			
			// Create the lines
			$this->page->setLineWidth ( 0.5 );
			$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
			$this->page->drawLine ( $z, $top_table_position, $size + $z, $top_table_position );
			
			// write the header labels
			$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
			$this->writeText ( $this->translator->translate ( $column [$i] ['value'] ), $size, $z, $top_table_position + 3, $align, "Helvetica", 8 );
			$z += $size;
		}
		
		$this->h -= GRID_HEIGHT_ROWS;
	}
	
	
	/**
	 * CreateGrid
	 * Create the grid for the orders items
	 * @param $database
	 * @return void
	 */
	private function create_grid() {
		$new_h = "";
		$column = $this->check_columns_size( $this->data ['columns'] );
		$records = isset ( $this->data ['records'] ) ? $this->data ['records'] : array ();
		
		$top_table_position = $this->h - GRID_TOP; // Top position of the table in the document
		$z = PAGE_BOTH_MARGIN; // Incremental space between the columns
		
		// Top position of the records items within the data grid under the columns header
		$top_record_rows_position = $top_table_position - GRID_HEIGHT_ROWS; 
		
		// Creating of the table header
		$this->create_table_header();
		
		$this->set_font_size ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
		$h = $top_record_rows_position;
		
		// Rows and Columns building.
		// Looping the records
		for($j = 0; $j < count ( $records ); $j ++) {
			
			// Looping the columns
			for($i = 0; $i < count ( $column ); $i ++) {
				$size = $column [$i] ['size']; // Size of the column
				$align = ! empty ( $column [$i] ['align'] ) ? $column [$i] ['align'] : ""; // Alignment of the column
				
				// write a text in wordwrap mode and get the new height of 
				// the row in order to write the next row in the right position.
				if (! empty ( $records [$j] [$i] )) {
					$new_h [] = $this->writeText ( $records [$j] [$i], $size, $z, $h-7, $align, "Helvetica", 8 );
				}
				// Increment the cursor for the new column
				$z += $size;
			
			}
			
			// Get the min and not the max value for the new position of the row
			if (count ( $new_h ) > 0) {
				$h = min ( $new_h ) - GRID_HEIGHT_BETWEEN_ROWS;
			}
			
			// Create the split line between records
			$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#dddddd' ) );
			$this->page->drawLine ( PAGE_BOTH_MARGIN, $h + 8, $z, $h + 8 );
			
			// Check if the row is close to the end of the document
			If ($h < 50) {
				
				// write the label "continue the next page"
				$this->set_font_size ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC, 6 );
				$points = $this->width_to_points ( $this->translator->translate ( 'Continue' ), Zend_Pdf_Font::fontWithName ( 'Helvetica' ), 6 );
				$this->write ( $this->translator->translate ( 'Continue' ), PAGE_WIDTH - PAGE_BOTH_MARGIN - $points, 25 );
				
				// Create a new page
				$this->create_page ();
				// Creating of the table header
				$this->create_table_header();
				// Reset of the font style              
				$this->set_font_size ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
				// Reset the position of the new details rows
				$h = $this->h - GRID_TOP;
			}
			
			// Reset of the $z value for the new row
			$z = PAGE_BOTH_MARGIN;
			
			// Clear the $new_h variable
			$new_h = array ();
		}
		
		$this->h = $h;
	}
	
	/**
	 * Check the size of the columns
	 * @param array $columns
	 */
	private function check_columns_size($columns) {
		$width = 0;
		$items_with_size_not_set = array ();
		
		for($i = 0; $i < count ( $columns ); $i ++) {
			if (isset ( $columns [$i] ['size'] )) {
				$width += $columns [$i] ['size'];
			} else {
				$items_with_size_not_set [] = $i; // all the columns index without size set 
			}
		}
		
		foreach ( $items_with_size_not_set as $colindex ) {
			$columns [$colindex] ['size'] = intval ( (PAGE_WIDTH - (PAGE_BOTH_MARGIN * 2) - $width) / count ( $items_with_size_not_set ) );
		}
		
		return $columns;
	}	
	
}