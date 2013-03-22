<?php

class Shineisp_Commons_ImgText {

    /**
     * create a text image
     *
     * @param   string $string
     * @return  img
     */
    static public function create($text, $width=400, $height=30, $fontname="arial.ttf", $fontsize=14, $align="c", $x=0, $y=0 )
    {
    	
		// Set the content-type
		header('Content-Type: image/png');
		
		// Create the image
		$im = imagecreatetruecolor($width, $height);
		
		// Create some colors
		$white = imagecolorallocate($im, 255, 255, 255);
		$grey = imagecolorallocate($im, 128, 128, 128);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagefilledrectangle($im, 0, 0, $width-1, $height-1, $white);
		
		// Replace path by your own font path
		$font = PUBLIC_PATH . "/resources/fonts/$fontname";
		
		//Calculate lineheight by common characters.
		/*
		 *  imagettfbbox
		 *  
		 *  0 	lower left corner, X position
			1 	lower left corner, Y position
			2 	lower right corner, X position
			3 	lower right corner, Y position
			4 	upper right corner, X position
			5 	upper right corner, Y position
			6 	upper left corner, X position
			7 	upper left corner, Y position
		 */
		
	    $dimensions = imagettfbbox($fontsize, 0, $font, $text); //use a custom string to get a fixed height.
	    $lineHeight = $dimensions[3] - $dimensions[5]; // get the heightof this line

	    $locY = $height/2 + $lineHeight/2;
		
    	if ($align != "l") {
            $dimensions = imagettfbbox($fontsize, 0, $font, $text);
        	$lineWidth = $dimensions[4] - $dimensions[0]; // get the length of this line
            if ($align == "r") { //If the align is Right
                $locX = $x + $width - $lineWidth;
            } else { //If the align is Center
                $locX = $x + ($width/2) - ($lineWidth/2);
            }
        } else { //if the align is Left
            $locX = $x;
        }
        
		// Add the text
		imagettftext($im, $fontsize, 0, $locX, $locY, $black, $font, $text);
		
		// Using imagepng() results in clearer text compared with imagejpeg()
		imagepng($im);
		imagedestroy($im);
    } 
       
}
    