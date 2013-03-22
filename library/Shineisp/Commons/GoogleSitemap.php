<?php

/* google_sitemap.class.php

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

-------------------------------------------------------------------------------
$Id: $

*/

/** A class for generating simple google sitemaps
 *@author Svetoslav Marinov <svetoslav.marinov@gmail.com>
 *@copyright 2005
 *@version 0.1
 *@access public
 *@package google_sitemap
 *@link http://devquickref.com
 */
class Shineisp_Commons_GoogleSitemap {
	var $header = "<\x3Fxml version=\"1.0\" encoding=\"UTF-8\"\x3F>\n\t<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">";
	var $charset = "UTF-8";
	var $footer = "\t</urlset>\n";
	var $items = array ();
	
	/** Adds a new item to the channel contents.
	 *@param google_sitemap item $new_item
	 *@access public
	 */
	function add_item($new_item) {
		//Make sure $new_item is an 'google_sitemap item' object
		if (! is_a ( $new_item, "Shineisp_Commons_GoogleSitemap_Item" )) {
			//Stop execution with an error message
			trigger_error ( "Can't add a non-google_sitemap_item object to the sitemap items array" );
		}
		$this->items [] = $new_item;
	}
	
	/** Generates the sitemap XML data based on object properties.
	 *@param string $file_name ( optional ) if file name is supplied the XML data is saved in it otherwise returned as a string.
	 *@access public
	 *@return [void|string]
	 */
	function build($file_name = null) {
		$map = $this->header . "\n";
		
		foreach ( $this->items as $item ) {
			$item->loc = htmlentities ( $item->loc, ENT_QUOTES );
			$map .= "\t\t<url>\n\t\t\t<loc>$item->loc</loc>\n";
			
			// lastmod
			if (! empty ( $item->lastmod ))
				$map .= "\t\t\t<lastmod>$item->lastmod</lastmod>\n";
			
		// changefreq
			if (! empty ( $item->changefreq ))
				$map .= "\t\t\t<changefreq>$item->changefreq</changefreq>\n";
			
		// priority
			if (! empty ( $item->priority ))
				$map .= "\t\t\t<priority>$item->priority</priority>\n";
			
			$map .= "\t\t</url>\n\n";
		}
		
		$map .= $this->footer . "\n";
		
		if (! is_null ( $file_name )) {
			$fh = fopen ( $file_name, 'w' );
			fwrite ( $fh, $map );
			fclose ( $fh );
		} else {
			return $map;
		}
	}

}

/** A class for storing google_sitemap items and will be added to google_sitemap objects.
 *@author Svetoslav Marinov <svetoslav.marinov@gmail.com>
 *@copyright 2005
 *@access public
 *@package google_sitemap_item
 *@link http://devquickref.com
 *@version 0.1
 */
class Shineisp_Commons_GoogleSitemap_Item {
	/** Assigns constructor parameters to their corresponding object properties.
	 *@access public
	 *@param string $loc location
	 *@param string $lastmod date (optional) format in YYYY-MM-DD or in "ISO 8601" format
	 *@param string $changefreq (optional)( always,hourly,daily,weekly,monthly,yearly,never )
	 *@param string $priority (optional) current link's priority ( 0.0-1.0 )
	 */
	function __construct($loc, $lastmod = '', $changefreq = '', $priority = '') {
		$this->loc = $loc;
		$this->lastmod = $lastmod;
		$this->changefreq = $changefreq;
		$this->priority = $priority;
	}
}