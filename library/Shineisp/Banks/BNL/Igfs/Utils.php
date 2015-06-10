<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Shine Software
 * @package    Epositivity
 * @copyright  Copyright (c) 2008 Shine Software (http://www.shinesoftware.com)
 */


class Shineisp_Banks_BNL_Igfs_Utils {

	public static function getSignature($ksig, $fields) {
		$data = "";
		foreach ($fields as $value) {
			$data .= $value;
		}
		
		return base64_encode(hash_hmac('sha256', $data, $ksig, true));
	}
	
	public static function getUniqueBoundaryValue() {
		return uniqid();
	}
	
	public static function parseResponseFields($nodes) {
		$fields = array();
		foreach ($nodes->children() as $item) {
			if (count($item) == 0) {
				$fields[$item->getName()] = (string)$item;
			} else {
				$fields[$item->getName()] = (string)$item->asXML();
			}
		}
		return $fields;
	}
	
	public static function startsWith($haystack,$needle,$case=true) {
	    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
	    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
	}
	
	public static function endsWith($haystack,$needle,$case=true) {
	    if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
	    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
	}

	public static function formatXMLGregorianCalendar($date) {
	    try {
		$format1 = date("Y-m-d", $date);
		$format2 = date("H:i:s", $date) . ".0";
		$format3 = date("P", $date);
		$sb = "";
		$sb .= $format1;
		$sb .= "T";
		$sb .= $format2;
		$sb .= $format3;
		return $sb;
	    } catch (Exception $e) {
	    }
	    return NULL;
	}

	public static function parseXMLGregorianCalendar($text) {
	    $count=1;
	    try {
	    $tmp = str_replace("T"," ",$text,$count);
	    return DateTime::createFromFormat("j-M-Y H:i:s.uP", $tmp);
	    } catch (Exception $e) {
		try {
	    $tmp = str_replace("T"," ",$text,$count);
		return DateTime::createFromFormat("j-M-Y H:i:s.u", $tmp);
		} catch (Exception $e) {
		}
	    }
	    return NULL;
	}

}

?>
