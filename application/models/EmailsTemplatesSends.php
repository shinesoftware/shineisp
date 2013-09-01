<?php

/**
 * EmailsTemplatesSends
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class EmailsTemplatesSends extends BaseEmailsTemplatesSends
{

	/**
	 * get the email by id
	 *  
	 * @param integer $id
	 * @param string $fields
	 * @return Array
	 */
	public static function getById($id, $fields = '*') {
		if(is_numeric($id)){

			// create the dq query 
			$dq = Doctrine_Query::create ()
								->from ( 'EmailsTemplatesSends et' )
								->where ( 'et.id = ?', intval($id) )
								->limit(1);
			
			if(!empty($fields) && $fields != "*"){
				$dq->select ( $fields );
			}

			// execute the query
			$record = $dq->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			return !empty($record[0]) ? $record[0] : null;
		}
		
		return false;
		
	}

	/**
	 * get all the email of by the selected customer id
	 *  
	 * @param integer $customerID
	 * @param string $fields
	 * @return Array
	 */
	public static function getByCustomerID($customerID, $fields = '*') {
		if(is_numeric($customerID)){

			// create the dq query 
			$dq = Doctrine_Query::create ()
								->from ( 'EmailsTemplatesSends et' )
								->where ( 'et.customer_id = ?', intval($customerID) );
			
			if(!empty($fields) && $fields != "*"){
				$dq->select ( $fields );
			}

			// execute the query
			return $dq->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		}
		
		return false;
		
	}
	
	/**
	 * Save the email log history
	 * 
	 * @param integer $customerId
	 * @param array|string $fromname
	 * @param string $recipient
	 * @param string $subject
	 * @param array|string $cc
	 * @param array|string $bcc
	 * @param string $htmlbody
	 * @param string $textbody
	 * @param string $date
	 */
	public static function saveIt($customerId, $from, $recipient, $subject, $cc, $bcc, $htmlbody, $textbody, $date=null) {
		
		$EmailsTemplatesSends = new EmailsTemplatesSends();
		
		if(is_numeric($customerId)){
			$EmailsTemplatesSends->customer_id = $customerId;
			$EmailsTemplatesSends->fromname    = (is_array($from) && isset($from['name']))  ? $from['name']  : '';
			$EmailsTemplatesSends->fromemail   = (is_array($from) && isset($from['email'])) ? $from['email'] : $from;
			$EmailsTemplatesSends->subject     = $subject;
			$EmailsTemplatesSends->recipient   = $recipient;
			$EmailsTemplatesSends->cc          = (is_array($cc))  ? trim(implode(',', $cc),',')  : $cc;
			$EmailsTemplatesSends->bcc         = (is_array($bcc)) ? trim(implode(',', $bcc),',') : $bcc;
			$EmailsTemplatesSends->html        = $htmlbody;
			$EmailsTemplatesSends->text        = $textbody;
			$EmailsTemplatesSends->date        = !empty($date) ? $date : date('Y-m-d H:i:s');
			
			if($EmailsTemplatesSends->trySave()){
				return true;
			}
		}

		return false;
	}


}