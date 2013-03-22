<?php

/**
 * This class read the ISP Panels configuration 
 * @version 1.0
 * @author Shine Software
 */

class Shineisp_Commons_IspPanels {
	
	/**
	 * get the panel configuration
	 */
	public static function get_fields_list($panel, $method) {
		$path = PUBLIC_PATH . "/../library/Shineisp/Api/Panels/$panel";
		if (file_exists ( $path . "/config.xml" )) {
			$config = simplexml_load_file ( $path . "/config.xml" );
			$fields = $config->xpath ( "methods/$method/fieldslist" );
			if (! empty ( $fields [0] )) {
				foreach ( $fields [0] as $name => $value ) {
					echo $name;
					die;
				}
			}
		}
	}
}