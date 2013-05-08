<?php

/*
 * Shineisp_Commons_Pdf
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Commons_Pdf
* Purpose:  Class for the pdf files
* -------------------------------------------------------------
*/

define ( 'PAGE_WIDTH', 842 );
define ( 'PAGE_HEIGHT', 595 );
define ( 'PAGE_BOTH_MARGIN', 30 );
define ( 'HEADER_TOP', 550 );
define ( 'HEADER_LEFT', 30 );

class Shineisp_Commons_Pdf {
	protected $pdf;
	protected $font;
	protected $colors;
	protected $translator;
	protected $data;
	protected $h;
	
	/**
	 * Construct function inizialize the class
	 */
	public function __construct() {
		$this->h = PAGE_HEIGHT;
		$this->pdf = new Zend_Pdf ();
		$this->font = Zend_Pdf_Font::FONT_HELVETICA;
		$this->data = array ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
	}
	
	
	/**
	 * Create the page header
	 */
	public function create_page_header() {
		try {
			$header = isset ( $this->data ['headers'] ) ? $this->data ['headers'] : array ();
			$toppos = HEADER_TOP;
			
			if (! empty ( $header['title'] )) {
				$this->set_font_size ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC, 12 );
				$this->write ($header['title'] , HEADER_LEFT, $toppos );
			}
			
			if (! empty ( $header['subtitle'] )) {
				$this->set_font_size ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC, 6 );
				$points = $this->width_to_points ( $header['subtitle'], Zend_Pdf_Font::fontWithName ( 'Helvetica' ), 6 );
				$this->write ( $header['subtitle'], PAGE_WIDTH - PAGE_BOTH_MARGIN - $points, $toppos );
			}
			
			$this->page->setLineWidth ( 1 );
			$this->page->setFillColor ( new Zend_Pdf_Color_Html ( '#000000' ) );
			$toppos -= 5;
			$this->page->drawLine ( HEADER_LEFT, $toppos, PAGE_WIDTH - PAGE_BOTH_MARGIN, $toppos );
			
			$this->h = $toppos - 20;
		} catch ( exception $e ) {
			return $e->message ();
		}
	}	
	
	/**
	 * Create a simple pdf page
	 */
	public function create_page($size=Zend_Pdf_Page::SIZE_A4_LANDSCAPE) {
		$this->page = $this->pdf->newPage ( $size );
		$this->pdf->pages [] = $this->page;
		
		$this->create_page_header();
		
		// Creating the footer 
		$this->create_footer ();
	}	

	
	/**
	 * Create the footer of the page
	 */
	public function create_footer() {
		$toppos = 30;
		if (!empty ( $this->data ['footer']['text'] )){
			$this->set_font_size ( Zend_Pdf_Font::FONT_HELVETICA_ITALIC, 6 );
			$this->write ( $this->data ['footer']['text'], PAGE_BOTH_MARGIN, $toppos );
			$toppos -= 10;
		} 
	}
	
	/**
	 * Write a text
	 * 
	 * @param $text
	 * @param $x
	 * @param $y
	 * @param $color
	 * @return void
	 */
	protected function write($text, $x, $y, $color = "#000000") {
		if (! empty ( $color )) {
			$this->page->setLineColor ( new Zend_Pdf_Color_Html ( $color ) );
		}
		// Draw text        
		$this->page->drawText ( $text, $x, $y, 'UTF-8' );
	}	
	
	/**
	 * 
	 * @param $size
	 * @return void
	 */
	protected function set_font_size($font, $size) {
		// Set font 
		$this->page->setFont ( Zend_Pdf_Font::fontWithName ( $font ), $size );
	}
	

	/**
	 * writeText
	 * write the text in wordwrap mode
	 * @param $text
	 * @param $colsize
	 * @param $x
	 * @param $y
	 * @return integer
	 */
	protected function writeText($text, $colsize, $x, $y, $align = "left", $fontname = "Helvetica", $fontsize = "6") {
		
		try {
			if (! empty ( $text )) {
				$chars = intval ( $colsize * 63 / 220 );
				$text = wordwrap ( $text, $chars, "\n", false );
				$token = explode ( "\n", $text );
				$font = Zend_Pdf_Font::fontWithName ( $fontname );
				
				foreach ( $token as $row ) {
					if ($align == "left") {
						$this->write ( $row, $x, $y );
					} elseif ($align == "right") {
						$points = $this->width_to_points ( $row, $font, $fontsize ); //Conversion of the chars in points
						$this->write ( $row, $x + ($colsize - $points), $y );
					} elseif ($align == "center") {
						$points = $this->width_to_points ( $row, $font, $fontsize ); //Conversion of the chars in points
						$this->write ( $row, $x + (($colsize - $points) / 2), $y );
					} else {
						$this->write ( $row, $x, $y );
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
	protected function width_to_points($string, $font, $fontSize) {
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
}