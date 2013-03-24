<?php

/*
 * Shineisp_Commons_PdfOrder
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Commons_PdfOrder
* Purpose:  Class for the orders/invoice pdf files
* -------------------------------------------------------------
*/

define ( 'PAGE_WIDTH', 595 );
define ( 'PAGE_HEIGHT', 842 );
define ( 'PAGE_BOTH_MARGIN', 30 );
define ( 'HEADER_TOP', 800 );
define ( 'HEADER_LEFT', 30 );
define ( 'GRID_TOP', 20 );
define ( 'GRID_HEIGHT_ROWS', 20 );
define ( 'GRID_HEIGHT_BETWEEN_ROWS', 4 );

class Shineisp_Commons_PdfOrder {
	protected $pdf;
	protected $page;
	protected $font;
	protected $colors;
	protected $locale;
	protected $data;
	protected $h;
	
	public function __construct() {
		$registry = Zend_Registry::getInstance ();
		
		$this->h = PAGE_HEIGHT;
		
		$this->pdf = new Zend_Pdf ();
		$this->font = Zend_Pdf_Font::FONT_HELVETICA;
		$this->data = array ();
		$this->locale = $registry->Zend_Translate;
	}
	
	/**
	 * @param $font the $font to set
	 */
	public function setFont($font) {
		$this->font = $font;
	}
	
	/**
	 * @return the $font
	 */
	public function getFont() {
		return $this->font;
	}
	
	/**
	 * @param $pdf the $pdf to set
	 */
	public function setPdf($pdf) {
		$this->pdf = $pdf;
	}
	
	/**
	 * @return the $pdf
	 */
	public function getPdf() {
		return $this->pdf;
	}
	
	/**
	 * 
	 * @param $header
	 * @return void
	 */
	private function CreateHeader() {
		$records = $this->data ['records'];
		
		if(empty($this->data ['records'])){
			return false;
		}
		
		$toppos = HEADER_TOP;
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 16 );
		$this->Write ( $records ['company'] ['name'], HEADER_LEFT, $toppos );
		
		if (! empty ( $records ['company'] ['slogan'] )) {
			$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC, 6 );
			$points = $this->widthForStringUsingFontSize ( $records ['company'] ['slogan'], Zend_Pdf_Font::fontWithName ( 'Helvetica' ), 6 );
			$this->Write ( $records ['company'] ['slogan'], PAGE_WIDTH - PAGE_BOTH_MARGIN - $points, $toppos );
		}
		
		$this->page->setLineWidth ( 1 );
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
		$toppos -= 5;
		$this->page->drawLine ( HEADER_LEFT, $toppos, PAGE_WIDTH - PAGE_BOTH_MARGIN, $toppos );
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC, 8 );
		$toppos -= 10;
		$this->Write ( $this->locale->translate ( 'Site Web' ) . ": " . $records ['company'] ['website'] . " - " . $this->locale->translate ( 'eMail' ) . ": " . $records ['company'] ['email'] . " - " . $this->locale->translate ( 'Telephone' ) . ": " . $records ['company'] ['telephone'] . " - " . $this->locale->translate ( 'FAX' ) . ": " . $records ['company'] ['fax'], HEADER_LEFT, $toppos );
		$toppos -= 10;
		$this->Write ( $this->locale->translate ( 'Address' ) . ": " . $records ['company'] ['address'] . " - " . $records ['company'] ['zip'] . " - " . $records ['company'] ['city'] . " - " . $records ['company'] ['country'], HEADER_LEFT, $toppos );
		$toppos -= 10;
		$this->Write ( $this->locale->translate ( 'VAT Number' ) . ": " . $records ['company'] ['vat'], HEADER_LEFT, $toppos );
		
		$this->h = $toppos - 20;
	}
	
	/**
	 * 
	 * @param $footer
	 * @return void
	 */
	private function CreateFooter($footer) {
		try {
		
		} catch ( exception $e ) {
			return $e->message ();
		}
	}
	
	/**
	 * 
	 * @param $size
	 * @return void
	 */
	private function setFontandSize($font, $size) {
		// Set font 
		$this->page->setFont ( Zend_Pdf_Font::fontWithName ( $font ), $size );
	}
	
	/**
	 * 
	 * @param $text
	 * @param $x
	 * @param $y
	 * @param $color
	 * @return void
	 */
	private function Write($text, $x, $y, $color = "#000000") {
		if (! empty ( $color )) {
			$this->page->setLineColor ( new Zend_Pdf_Color_Html ( $color ) );
		}
		// Draw text        
		$this->page->drawText ( $text, $x, $y );
	}
	
	/**
	 * Embellishments
	 * 
	 * @return void
	 */
	private function Embellishments() {
		
		$toppos = $this->h - 20;
		
		// Decoration
		$this->page->setLineWidth ( 2 );
		$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#eeeeee' ) );
		$this->page->drawLine ( 2, $toppos, 300, $toppos );
		
		$this->h = $toppos - 20;
	
	}
	
	private function QrCode(){
		
		$records = $this->data ['records'];
		
		if(empty($this->data ['records'])){
			return false;
		}
		
		// QRCode Image
		$code['order'] = $records ['order_number'];
		$code['customer'] = $records['customer'] ['customer_id'];
		$jcode = base64_encode(json_encode($code));
		
		$strCode = $_SERVER['HTTP_HOST'] . "/index/qrcode/q/$jcode";
		
		// QRCode Image
		$qrcode = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=$strCode&choe=UTF-8";
		file_put_contents(PUBLIC_PATH . '/tmp/qrcode'.$jcode,file_get_contents($qrcode));
		$logo = new Zend_Pdf_Resource_Image_Png(PUBLIC_PATH . '/tmp/qrcode'.$jcode);
		$this->page->drawImage($logo, (PAGE_WIDTH - PAGE_BOTH_MARGIN - 80), 100, PAGE_WIDTH - PAGE_BOTH_MARGIN + ($logo->getPixelWidth()/1.9) - 80, 100 + $logo->getPixelHeight()/1.9);
		@unlink(PUBLIC_PATH . '/tmp/qrcode'.$jcode);
		
		return true;
	}
	
	/**
	 * Create the ribbon
	 * @return void
	 */
	private function Ribbon($ribbon) {
		
		$toppos = $this->h-15;

		// Ribbon
		$this->page->setLineColor ( new Zend_Pdf_Color_Html ( $ribbon['border-color'] ) );
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( $ribbon['color'] ) );
		$x = array(400,430,430,415,400);
		$y = array(842,842,752,762,752);
		$this->page->drawPolygon ( $x, $y );
		
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 9 );
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#ffffff' ) );
		
		// Draw text        
		$this->page->rotate(411, 830, -M_PI/2);
		$this->page->drawText ( $ribbon['text'], 411, 830);		
		$this->page->rotate(411, 830, M_PI/2);
	}
	
	/**
	 * AddresseeBox
	 * Creation of the rectangle with the delivery information
	 * @return void
	 */
	private function AddresseeBox() {
		$records = isset ( $this->data ['records'] ) ? $this->data ['records'] : array ();
		
		$toppos = $this->h - 10;
		
		// Left Square
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_BOLD, 9 );
		$this->Write ( $this->locale->translate ( 'Addressee' ), 30, $toppos );
		
		$this->page->setLineWidth ( 0.5 );
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#ffffff' ) );
		$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#bbbbbb' ) );
		$this->page->drawRectangle ( 30, $toppos - 5, 280, $toppos - 105 );
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
		
		if (! empty ( $records ['customer'] ['company'] )) {
			$this->Write ( $records ['customer'] ['company'], 40, $toppos - 20 );
		} else {
			$this->Write ( $records ['customer'] ['firstname'] . " " . $records ['customer'] ['lastname'], 40, $toppos - 20 );
		}
		
		$records ['customer'] ['address'] = ! empty ( $records ['customer'] ['address'] ) ? $records ['customer'] ['address'] : "";
		$records ['customer'] ['code'] = ! empty ( $records ['customer'] ['code'] ) ? $records ['customer'] ['code'] : "";
		$records ['customer'] ['city'] = ! empty ( $records ['customer'] ['city'] ) ? $records ['customer'] ['city'] : "";
		$records ['customer'] ['country'] = ! empty ( $records ['customer'] ['country'] ) ? $records ['customer'] ['country'] : "";
		
		$this->Write ( $records ['customer'] ['address'], 40, $toppos - 30 );
		$this->Write ( $records ['customer'] ['code'] . " " . $records ['customer'] ['city'], 40, $toppos - 40 );
		$this->Write ( $records ['customer'] ['country'], 40, $toppos - 50 );
		
		$this->Write ( $this->locale->translate ( 'VAT Number' ) . ": " . $records ['customer'] ['vat'], 40, $toppos - 80 );
		$this->Write ( $this->locale->translate ( 'Tax payer number' ) . ": " . $records ['customer'] ['taxpayernumber'], 40, $toppos - 95 );
	
	}
	
	/**
	 * DestinationBox
	 * 
	 * @return void
	 */
	private function DestinationBox() {
		$records = isset ( $this->data ['records'] ) ? $this->data ['records'] : array ();
		
		$toppos = $this->h - 10;
		
		// Right Square     
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_BOLD, 9 );
		$this->Write ( $this->locale->translate ( 'Destination' ), 300, $toppos );
		
		$this->page->setLineWidth ( 0.5 );
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#ffffff' ) );
		$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#bbbbbb' ) );
		$this->page->drawRectangle ( 300, $toppos - 5, 550, $toppos - 105 );
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
		
		if (! empty ( $records ['customer'] ['company'] )) {
			$this->Write ( $records ['customer'] ['company'], 310, $toppos - 20 );
		} else {
			$this->Write ( $records ['customer'] ['firstname'] . " " . $records ['customer'] ['lastname'], 310, $toppos - 20 );
		}
		
		$records ['customer'] ['address'] = ! empty ( $records ['customer'] ['address'] ) ? $records ['customer'] ['address'] : "";
		$records ['customer'] ['code'] = ! empty ( $records ['customer'] ['code'] ) ? $records ['customer'] ['code'] : "";
		$records ['customer'] ['city'] = ! empty ( $records ['customer'] ['city'] ) ? $records ['customer'] ['city'] : "";
		$records ['customer'] ['country'] = ! empty ( $records ['customer'] ['country'] ) ? $records ['customer'] ['country'] : "";
		
		$this->Write ( $records ['customer'] ['address'], 310, $toppos - 30 );
		$this->Write ( $records ['customer'] ['code'] . " " . $records ['customer'] ['city'], 310, $toppos - 40 );
		$this->Write ( $records ['customer'] ['country'], 310, $toppos - 50 );
		
		$this->h -= 130;
	}
	
	/**
	 * CreateGrid
	 * Create the grid for the orders items
	 * @param $database
	 * @return void
	 */
	private function CreateGrid() {
		$new_h = "";
		$column = $this->CheckColumnSize ( $this->data ['columns'] );
		$records = isset ( $this->data ['records'] ) ? $this->data ['records'] : array ();
		
		$top_table_position = $this->h - GRID_TOP; // Top position of the table in the document
		$z = PAGE_BOTH_MARGIN; // Incremental space between the columns
		$top_record_rows_position = $top_table_position - GRID_HEIGHT_ROWS; // Top position of the records table under the columns header
		

		// Creating of the table header
		$this->CreateTableHeader ();
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
		$h = $top_record_rows_position;
		
		// Rows and Columns building.
		// Looping the records
		for($j = 0; $j < count ( $records ); $j ++) {
			
			// Looping the columns
			for($i = 0; $i < count ( $column ); $i ++) {
				$size = $column [$i] ['size']; // Size of the column
				$align = ! empty ( $column [$i] ['align'] ) ? $column [$i] ['align'] : ""; // Alignment of the column
				

				// Write a text in wordwrap mode and get the new height of 
				// the row in order to write the next row in the right position.
				if (! empty ( $records [$j] [$i] )) {
					$new_h [] = $this->WriteText ( $records [$j] [$i], $size, $z, $h, $align, "Helvetica", 8 );
				}
				// Increment the cursor for the new column
				$z += $size;
			
			}
			
			// Get the min and not the max value for the new position of the row
			if (count ( $new_h ) > 0) {
				$h = min ( $new_h ) - GRID_HEIGHT_BETWEEN_ROWS;
			}
			
			// Create the split line between records
			$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#cccccc' ) );
			$this->page->drawLine ( PAGE_BOTH_MARGIN, $h + 8, $z, $h + 8 );
			
			// Check if the row is close to the end of the document
			If ($h < 150) {
				// Write the label "continue the next page"
				$this->Write ( $this->locale->translate ( 'Continue' ), PAGE_BOTH_MARGIN, 25 );
				// Create a new page
				$this->CreatePage ();
				// Creating of the table header
				$this->CreateTableHeader ();
				// Reset of the font style              
				$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
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
	 * CreateTableHeader
	 * @return void
	 */
	private function CreateTableHeader() {
		$column = $this->CheckColumnSize ( $this->data ['columns'] );
		
		$top_table_position = $this->h - GRID_TOP; // Top position of the table in the document
		$z = PAGE_BOTH_MARGIN; // Incremental space between the columns
		$top_record_rows_position = $top_table_position - GRID_HEIGHT_ROWS; // Top position of the records table under the columns header
		

		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_BOLD, 8 );
		
		// Header table creation
		for($i = 0; $i < count ( $column ); $i ++) {
			$size = $column [$i] ['size']; // Size of the single column
			$align = ! empty ( $column [$i] ['align'] ) ? $column [$i] ['align'] : ""; // Alignment of the column
			

			// Create the lines
			$this->page->setLineWidth ( 0.5 );
			$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
			$this->page->drawLine ( $z, $top_table_position, $size + $z, $top_table_position );
			
			// Write the header labels
			$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
			$this->WriteText ( $this->locale->translate ( $column [$i] ['value'] ), $size, $z, $top_table_position + 3, $align, "Helvetica", 8 );
			$z += $size;
		}
		
		$this->h -= GRID_HEIGHT_ROWS;
	}
	
	/**
	 * WriteText
	 * Write the text in wordwrap mode
	 * @param $text
	 * @param $colsize
	 * @param $x
	 * @param $y
	 * @return integer
	 */
	private function WriteText($text, $colsize, $x, $y, $align = "left", $fontname = "Helvetica", $fontsize = "6") {
		
		try {
			if (! empty ( $text )) {
				$chars = intval ( $colsize * 63 / 220 );
				$text = wordwrap ( $text, $chars, "\n", false );
				$token = explode ( "\n", $text );
				$font = Zend_Pdf_Font::fontWithName ( $fontname );
				
				foreach ( $token as $row ) {
					if ($align == "left") {
						$this->Write ( $row, $x, $y );
					} elseif ($align == "right") {
						$points = $this->widthForStringUsingFontSize ( $row, $font, $fontsize ); //Conversion of the chars in points
						$this->Write ( $row, $x + ($colsize - $points), $y );
					} elseif ($align == "center") {
						$points = $this->widthForStringUsingFontSize ( $row, $font, $fontsize ); //Conversion of the chars in points
						$this->Write ( $row, $x + (($colsize - $points) / 2), $y );
					} else {
						$this->Write ( $row, $x, $y );
					}
					$y -= 10;
					$new_y [] = $y;
				}
				return min ( $new_y );
			} else {
				return $y;
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	
	}
	
	/**
	 * Returns the total width in points of the string using the specified font and
	 * size.
	 *
	 * This is not the most efficient way to perform this calculation. I'm
	 * concentrating optimization efforts on the upcoming layout manager class.
	 * Similar calculations exist inside the layout manager class, but widths are
	 * generally calculated only after determining line fragments.
	 * 
	 * @link http://devzone.zend.com/article/2525-Zend_Pdf-tutorial#comments-2535 
	 * @param string $string
	 * @param Zend_Pdf_Resource_Font $font
	 * @param float $fontSize Font size in points
	 * @return float
	 */
	function widthForStringUsingFontSize($string, $font, $fontSize) {
		try {
			$drawingString = iconv ( 'UTF-8', 'UTF-16BE//IGNORE', $string );
			$characters = array ();
			for($i = 0; $i < strlen ( $drawingString ); $i ++) {
				$characters [] = (ord ( $drawingString [$i ++] ) << 8) | ord ( $drawingString [$i] );
			}
			$glyphs = $font->glyphNumbersForCharacters ( $characters );
			$widths = $font->widthsForGlyphs ( $glyphs );
			$stringWidth = (array_sum ( $widths ) / $font->getUnitsPerEm ()) * $fontSize;
		} catch ( Exception $e ) {
			die ( $e->getMessage );
		}
		return $stringWidth;
	}
	
	/**
	 * Summary
	 * summary of the invoice
	 * @return void
	 */
	private function Summary() {
		$toppos = 244;
		$leftpos = 50;
		$records = isset ( $this->data ['records'] ) ? $this->data ['records'] : array ();
		
		$this->page->setLineWidth ( 0.5 );
		
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#ffffff' ) );
		$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#777777' ) );
		$leftspace = $leftpos;
		$this->page->drawRectangle ( $leftspace, $toppos, $leftspace + 100, $toppos - 20 );
		$leftspace += 100;
		$this->page->drawRectangle ( $leftspace, $toppos, $leftspace + 100, $toppos - 20 );
		$leftspace += 100;
		$this->page->drawRectangle ( $leftspace, $toppos, $leftspace + 100, $toppos - 20 );
		$leftspace += 100;
		$this->page->drawRectangle ( $leftspace, $toppos, $leftspace + 100, $toppos - 20 );
		$leftspace += 100;
		$this->page->drawRectangle ( $leftspace, $toppos, $leftspace + 100, $toppos - 20 );
		
		$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 5 );
		$leftspace = $leftpos;
		$this->Write ( $this->locale->translate ( "Order Number" ), $leftspace + 2, $toppos - 6 );
		$leftspace += 100;
		$this->Write ( $this->locale->translate ( "Payment Date" ), $leftspace + 2, $toppos - 6 );
		$leftspace += 100;
		$this->Write ( $this->locale->translate ( "Tax Free Total" ), $leftspace + 2, $toppos - 6 );
		$leftspace += 100;
		$this->Write ( $this->locale->translate ( "Tax Total" ), $leftspace + 2, $toppos - 6 );
		$leftspace += 100;
		$this->Write ( $this->locale->translate ( "Total with Tax" ), $leftspace + 2, $toppos - 6 );
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
		$leftspace = $leftpos;
		$this->Write ( sprintf("%03d", $records ['order_number']), $leftspace + 2, $toppos - 16 );
		
		$records ['payment_date'] = ! empty ( $records ['payment_date'] ) ? $records ['payment_date'] : "";
		
		$leftspace += 100;
		$this->Write ( $records ['payment_date'], $leftspace + 2, $toppos - 16 );
		$leftspace += 100;
		$this->Write ( $records ['subtotal'], $leftspace + 2, $toppos - 16 );
		$leftspace += 100;
		$this->Write ( $records ['vat'], $leftspace + 2, $toppos - 16 );
		$leftspace += 100;
		$this->Write ( $records ['grandtotal'], $leftspace + 2, $toppos - 16 );
		$this->h = $toppos - 20;
	}
	
	/**
	 * FooterDetails
	 * 
	 * @return void
	 */
	private function FooterDetails() {
		
		if ($this->h < 218) {
			$this->CreatePage ();
		}
		
		$toppos = 214;
		$records = isset ( $this->data ['records'] ) ? $this->data ['records'] : array ();
		
		$this->page->setLineWidth ( 0.5 );
		$this->page->setLineColor ( new Zend_Pdf_Color_Html ( '#333333' ) );
		$this->page->drawLine ( PAGE_BOTH_MARGIN, $toppos, PAGE_WIDTH - PAGE_BOTH_MARGIN, $toppos );
		
		$this->page->setLineWidth ( 1 );
		
		// Vertical lines
		$this->page->drawLine ( PAGE_BOTH_MARGIN + 295, $toppos - 5, PAGE_BOTH_MARGIN + 295, $toppos - 140 );
		$this->page->drawLine ( PAGE_BOTH_MARGIN + 442, $toppos - 5, PAGE_BOTH_MARGIN + 442, $toppos - 40 );
		
		// Horizontal Lines
		$toppos -= 40;
		$this->page->drawLine ( PAGE_BOTH_MARGIN, $toppos, PAGE_WIDTH - PAGE_BOTH_MARGIN, $toppos );
		
		$toppos -= 40;
		$this->page->drawLine ( PAGE_BOTH_MARGIN, $toppos, PAGE_BOTH_MARGIN + 295, $toppos );
		
		$toppos -= 30;
		$this->page->drawLine ( PAGE_BOTH_MARGIN + 295, $toppos, PAGE_WIDTH - PAGE_BOTH_MARGIN, $toppos );
		
		$toppos -= 30;
		$this->page->drawLine ( PAGE_BOTH_MARGIN, $toppos, PAGE_WIDTH - PAGE_BOTH_MARGIN, $toppos );
		$this->page->drawLine ( PAGE_BOTH_MARGIN + 430, $toppos, PAGE_BOTH_MARGIN + 430, $toppos - 30 ); // Vertical line
		

		$toppos -= 30;
		$this->page->drawLine ( PAGE_BOTH_MARGIN, $toppos, PAGE_WIDTH - PAGE_BOTH_MARGIN, $toppos );
		
		// Reset of the height for writing the labels
		$toppos = 214;
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 5 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Bank Name" ) ), PAGE_BOTH_MARGIN + 2, $toppos - 10 );
		$this->Write ( $this->locale->translate ( "IBAN" ), PAGE_BOTH_MARGIN + 300, $toppos - 10 );
		$this->Write ( $this->locale->translate ( "BIC" ), PAGE_BOTH_MARGIN + 445, $toppos - 10 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Company Name" ) ), PAGE_BOTH_MARGIN + 2, $toppos - 50 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Payment mode" ) ), PAGE_BOTH_MARGIN + 300, $toppos - 50 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Payment note" ) ), PAGE_BOTH_MARGIN + 300, $toppos - 80 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Customer Information" ) ), PAGE_BOTH_MARGIN + 2, $toppos - 90 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Transaction ID" ) ), PAGE_BOTH_MARGIN + 2, $toppos - 150 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Order Number" ) ), PAGE_BOTH_MARGIN + 300, $toppos - 120 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Invoice Number" ) ), PAGE_BOTH_MARGIN + 400, $toppos - 120 );
		$this->Write ( strtoupper ( $this->locale->translate ( "Payment Date" ) ), PAGE_BOTH_MARGIN + 300, $toppos - 150 );
		$this->Write ( strtoupper ( $this->locale->translate ( "EURO" ) ), PAGE_BOTH_MARGIN + 436, $toppos - 150 );
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 8 );
		$this->Write ( $records ['company'] ['bankname'], PAGE_BOTH_MARGIN + 2, $toppos - 24 );
		$this->Write ( $records ['company'] ['iban'], PAGE_BOTH_MARGIN + 300, $toppos - 24 );
		$this->Write ( $records ['company'] ['bic'], PAGE_BOTH_MARGIN + 445, $toppos - 24 );
		$this->Write ( $records ['company'] ['name'], PAGE_BOTH_MARGIN + 2, $toppos - 65 );
		$this->Write ( $records ['customer'] ['company'], PAGE_BOTH_MARGIN + 2, $toppos - 105 );
		
		$records ['customer'] ['address'] = ! empty ( $records ['customer'] ['address'] ) ? $records ['customer'] ['address'] : "";
		$records ['customer'] ['code'] = ! empty ( $records ['customer'] ['code'] ) ? $records ['customer'] ['code'] : "";
		$records ['customer'] ['city'] = ! empty ( $records ['customer'] ['city'] ) ? $records ['customer'] ['city'] : "";
		$records ['customer'] ['country'] = ! empty ( $records ['customer'] ['country'] ) ? $records ['customer'] ['country'] : "";
		
		$this->Write ( $records ['customer'] ['address'], PAGE_BOTH_MARGIN + 2, $toppos - 115 );
		$this->Write ( $records ['customer'] ['code'] . " " . $records ['customer'] ['city'], PAGE_BOTH_MARGIN + 2, $toppos - 125 );
		$this->Write ( $records ['customer'] ['country'], PAGE_BOTH_MARGIN + 2, $toppos - 135 );
		
		$this->Write ( sprintf("%03d", $records ['order_number']), PAGE_BOTH_MARGIN + 300, $toppos - 130 );
		$this->Write ( sprintf("%03d", $records ['invoice_number']), PAGE_BOTH_MARGIN + 400, $toppos - 130 );

		$records ['payment_date'] = ! empty ( $records ['payment_date'] ) ? $records ['payment_date'] : "";
		$records ['payment_transaction_id'] = ! empty ( $records ['payment_transaction_id'] ) ? $records ['payment_transaction_id'] : "";
		$records ['payment_description'] = ! empty ( $records ['payment_description'] ) ? $records ['payment_description'] : "";
		$records ['payment_mode'] = ! empty ( $records ['payment_mode'] ) ? $records ['payment_mode'] : "";
		
		$this->Write ( $records ['payment_date'], PAGE_BOTH_MARGIN + 300, $toppos - 162 );
		$this->Write ( $records ['payment_transaction_id'], PAGE_BOTH_MARGIN + 2, $toppos - 162 );
		$this->Write ( $records ['payment_description'], PAGE_BOTH_MARGIN + 299, $toppos - 90 );
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_BOLD, 9 );
		$this->Write ( $records ['grandtotal'], PAGE_BOTH_MARGIN + 436, $toppos - 162 );
		
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA, 10 );
		$this->Write ( $records ['payment_mode'], PAGE_BOTH_MARGIN + 299, $toppos - 65 );
		$this->h = $toppos - 210;
	}
	
	private function Footer() {
		$toppos = 30;
		$records = isset ( $this->data ['records'] ) ? $this->data ['records'] : array ();
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC, 6 );
		$this->Write ( $records ['company'] ['name'] . " - " . $this->locale->translate ( 'Site Web' ) . ": " . $records ['company'] ['website'] . " - " . $this->locale->translate ( 'eMail' ) . ": " . $records ['company'] ['email'] . " - " . $this->locale->translate ( 'Telephone' ) . ": " . $records ['company'] ['telephone'] . " - " . $this->locale->translate ( 'fax' ) . ": " . $records ['company'] ['fax'], PAGE_BOTH_MARGIN, $toppos );
		$toppos -= 10;
		$this->Write ( $this->locale->translate ( 'Address' ) . ": " . $records ['company'] ['address'] . " - " . $records ['company'] ['zip'] . " - " . $records ['company'] ['city'] . " - " . $records ['company'] ['country'] . " " . $this->locale->translate ( 'VAT Number' ) . ": " . $records ['company'] ['vat'], PAGE_BOTH_MARGIN, $toppos );
	
	}
	
	private function CreatePage() {
		$this->page = $this->pdf->newPage ( Zend_Pdf_Page::SIZE_A4 );
		$this->pdf->pages [] = $this->page;
		
		$this->CreateHeader ();
		
		// Invoice Number and Date
		$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_BOLD, 9 );
		
		if (is_numeric ( $this->data ['records'] ['invoice_number'] )) {
			$this->Write ( $this->locale->translate ( 'Invoice Number' ) . " " . $this->data ['records'] ['invoice_number'] . " " . $this->locale->translate ( 'dated' ) . " " . $this->data ['records'] ['date'], 400, $this->h - 20 );
		} else {
			$this->Write ( $this->locale->translate ( 'Order Number' ) . " " . $this->data ['records'] ['order_number'] . " " . $this->locale->translate ( 'dated' ) . " " . $this->data ['records'] ['date'], 400, $this->h - 20 );
		}
		
		$this->Embellishments ();
		
		// Creating the footer with the company information
		$this->Footer ();
	}
	
	/**
	 * CreatePDF
	 * Create a pdf file 
	 * array(3) {
		  ["header"] => array(1) {
		    ["label"] => string(28) "Invoice No. 001 - 24/02/2012"
		  }
		  ["columns"] => array(8) {
		    [0] => array(2) {
		      ["value"] => string(3) "SKU"
		      ["size"] => int(30)
		    }
		    [1] => array(1) {
		      ["value"] => string(11) "Description"
		    }
		    [2] => array(3) {
		      ["value"] => string(3) "Qty"
		      ["size"] => int(30)
		      ["align"] => string(6) "center"
		    }
		    [3] => array(2) {
		      ["value"] => string(4) "Unit"
		      ["size"] => int(30)
		    }
		    [4] => array(3) {
		      ["value"] => string(14) "Tax Free Price"
		      ["size"] => int(60)
		      ["align"] => string(5) "right"
		    }
		    [5] => array(3) {
		      ["value"] => string(9) "Setup fee"
		      ["size"] => int(60)
		      ["align"] => string(5) "right"
		    }
		    [6] => array(3) {
		      ["value"] => string(5) "Tax %"
		      ["size"] => int(40)
		      ["align"] => string(6) "center"
		    }
		    [7] => array(3) {
		      ["value"] => string(5) "Total"
		      ["size"] => int(50)
		      ["align"] => string(5) "right"
		    }
		  }
		  ["records"] => array(16) {
		    ["order_number"] => string(1) "1"
		    ["invoice_number"] => string(1) "1"
		    ["date"] => string(10) "24/02/2012"
		    ["customer"] => array(10) {
		      ["customer_id"] => string(1) "1"
		      ["company"] => string(14) "Shine Software"
		      ["firstname"] => string(8) "Giuseppe"
		      ["lastname"] => string(9) "Bucchieri"
		      ["vat"] => string(13) "IT03672170283"
		      ["email"] => string(27) "customers@shinesoftware.com"
		      ["address"] => string(11) "Via Caldera"
		      ["city"] => string(6) "Milano"
		      ["code"] => string(5) "20121"
		      ["country"] => string(5) "Italy"
		    }
		    ["payment_date"] => string(19) "23/02/2012 00:00:00"
		    ["payment_mode"] => string(17) "Bonifico Bancario"
		    ["payment_description"] => string(0) ""
		    ["payment_transaction_id"] => string(12) "XX1234567890"
		    ["company"] => array(14) {
		      ["name"] => string(8) "ShineISP"
		      ["vat"] => string(13) "IT01234567890"
		      ["bankname"] => string(7) "IWSmile"
		      ["iban"] => string(27) "IT28G0316501600000708754625"
		      ["bic"] => string(8) "IWBKITXX"
		      ["address"] => string(11) "17 rue Biot"
		      ["zip"] => string(5) "75017"
		      ["city"] => string(5) "Paris"
		      ["country"] => string(6) "France"
		      ["telephone"] => string(13) "+334097123985"
		      ["fax"] => string(13) "+334097123985"
		      ["website"] => string(23) "http://www.shineisp.com"
		      ["email"] => string(17) "info@shineisp.com"
		      ["slogan"] => string(18) "This is my slogan!"
		    }
		    ["subtotal"] => string(5) "93.90"
		    ["grandtotal"] => string(6) "113.62"
		    ["vat"] => string(5) "19.72"
		    ["delivery"] => int(0)
		    ["ribbon"] => array(3) {
		      ["text"] => string(5) "Payed"
		      ["color"] => string(7) "#009926"
		      ["border-color"] => string(7) "#00661A"
		    }
		    [0] => array(8) {
		      [0] => string(1) "1"
		      [1] => string(24) "Bronze Blog Hosting Plan"
		      [2] => string(1) "1"
		      [3] => string(2) "nr"
		      [4] => string(5) "84.00"
		      [5] => string(4) "0.00"
		      [6] => string(2) "21"
		      [7] => string(6) "101.64"
		    }
		    [1] => array(8) {
		      [0] => NULL
		      [1] => string(11) "company.com"
		      [2] => string(1) "1"
		      [3] => string(2) "nr"
		      [4] => string(4) "9.90"
		      [5] => string(4) "0.00"
		      [6] => NULL
		      [7] => string(4) "9.90"
		    }
		  }
		}
	 * @param array $data
	 * @return void
	 */
	public function CreatePDF($data, $filename = "file.pdf", $show = true, $path = "/documents", $force = FALSE) {
		try {
			if (! file_exists ( PUBLIC_PATH . "$path/$filename" ) || $force) {
				
				// Add new page to the document 
				$this->page = $this->pdf->newPage ( Zend_Pdf_Page::SIZE_A4 );
				$this->pdf->pages [] = $this->page;
				$this->data = $data;
				
				$this->CreateHeader ();
				
				// Invoice Number and Date
				$this->setFontandSize ( Zend_Pdf_Font::FONT_HELVETICA_BOLD, 9 );

				// Set the label of the document
				if(!empty($this->data ['header'] ['label'])){
					$this->Write ( $this->data ['header'] ['label'], 400, $this->h - 20 );
				}else{
					$this->Write ( "No label", 400, $this->h - 20 );
				}
				
				if (!empty ( $this->data ['records'] ['ribbon'] )) {
					$this->Ribbon($this->data ['records'] ['ribbon']);
					$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
				}
				
				$this->Embellishments ();
				
				$this->AddresseeBox ();
				$this->DestinationBox ();
				
				// Create the grid for all items
				$this->CreateGrid ();
				
				// Creating the summary section
				$this->Summary ();
				
				// Adding the QRCode
				$this->QrCode ();
				
				// Creating the footer details section
				$this->FooterDetails ();
				
				// Creating the footer with the company information
				$this->Footer ();
				
				// Get PDF document as a string 
				$contents = $this->pdf->render ();
				
				@mkdir ( PUBLIC_PATH . "/documents/" );
				@mkdir ( PUBLIC_PATH . "$path/" );
				$file = fopen ( PUBLIC_PATH . "$path/$filename", 'a' );
				fputs ( $file, $contents );
				fclose ( $file );
			
			} else {
				$handle = fopen ( PUBLIC_PATH . "$path/$filename", 'rb' );
				$contents = fread ( $handle, filesize ( PUBLIC_PATH . "$path/$filename" ) );
				fclose ( $handle );
			}
			
			if ($show) {
				header ( "Content-type: application/pdf" );
				header ( "Content-Disposition: attachment; filename=\"" . $filename . "\"" );
				die ( $contents );
			}
		
		} catch ( exception $e ) {
			Shineisp_Commons_Utilities::log($e->message ());
			return $e->message ();
		}
	}
	
	private function CheckColumnSize($columns) {
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