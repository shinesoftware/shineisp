<?php

/**
 * Cronjobs
 *
 * This class handles all the ShineISP cronjobs
 * Please see the layout.xml file because it handles the requests of every job
 * 
 * @package    ShineISP
 *
 * @see /application/modules/system/layout.xml  
 * @author     Shine Software <info@shineisp.com>
*/
class Cronjobs{
	
	/**
	 * Check the expiring orders.
	 *
	 * Only the orders where renewal option has been set as
	 * false send an email to the client to inform him
	 * that his order has been set as deleted.
	 */
	public static function checkExpiringOrders() {
		$orders = Orders::find_all_expired_orders(date('Y-m-d'));
	
		foreach ( $orders as $order ) {
			$customer = Customers::getAllInfo($order ['customer_id']);
			$ISP      = ISP::getActiveIspById($customer['isp_id']);
	
			// Get the fastlink attached
			$link_exist = Fastlinks::findlinks ( $order ['order_id'], $order ['customer_id'], 'orders' );
			if (count ( $link_exist ) > 0) {
				$fastlink = $link_exist [0] ['code'];
			} else {
				$fastlink = Fastlinks::CreateFastlink ( 'orders', 'edit', json_encode ( array ('id' => $order ['order_id'] ) ), 'orders', $order ['order_id'], $customer ['customer_id'] );
			}
	
			$customer_url = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/$fastlink";
				
			Shineisp_Commons_Utilities::sendEmailTemplate($customer ['email'], 'order_expired', array(
					'orderid'        => $order['order_number']
					,':shineisp:'     => $customer
					,'url'            => $customer_url
			), null, null, null, $ISP, $customer['language_id'], Settings::findbyParam('cron_notify'));
				
			// Set the order as deleted
			Orders::set_status($order['order_id'], Statuses::id('deleted', 'orders'));
		}
	
		return true;
	}
	
	/**
	 * Check if a order must be payed or not and send an email to the customer
	 * with a reminder. Add late fee if needed
	 */
	public static function checkOrders() {
		$orders = Orders::find_all_not_paid_orders();
	
		foreach ( $orders as $order ) {
			$customer = Customers::getAllInfo($order ['customer_id']);
			// Get the template from the main email template folder
			if( $order['is_renewal'] ) {
				$template = 'order_reminder_renewal';
	
				// Try to add a late fee if not an exempt customer
				if ( ! (int)$customer['ignore_latefee'] ) {
					$order = array_merge($order, Orders::applyLateFee($order['order_id']));
				}
	
			}else{
				$template = 'order_reminder';
			}
	
			// Get the fastlink attached
			$link_exist = Fastlinks::findlinks ( $order ['order_id'], $order ['customer_id'], 'orders' );
			if (count ( $link_exist ) > 0) {
				$fastlink = $link_exist [0] ['code'];
			} else {
				$fastlink = Fastlinks::CreateFastlink ( 'orders', 'edit', json_encode ( array ('id' => $order ['order_id'] ) ), 'orders', $order ['order_id'], $customer ['customer_id'] );
			}
	
			$customer_url = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/$fastlink";
				
			Shineisp_Commons_Utilities::sendEmailTemplate($customer ['email'], $template, array(
					'orderid'    => !empty($order['order_number']) ? $order['order_number'] : $order['order_id']
					,'fullname' => $customer['fullname']
					,':shineisp:' => $customer
					,'url'        => $customer_url
			), null, null, null, null, $customer['language_id'], Settings::findbyParam('cron_notify'));
		}
		return true;
	}
	
	/**
	 * Clean all the obsolete orders without expiring date
	 * Set the obsolete orders as deleted
	 */
	public static function cleanNotPaidOrders() {
		$orders = Orders::find_all_not_paid_orders();
	
		foreach ( $orders as $order ) {
				
			if(empty($order['expiring_date'])){
				// Set all the order oldest more of 1 month as deleted
				$date1 = new DateTime($order['order_date']);
				$date2 = new DateTime(date('Y-m-d'));
			}else{
				// Set the expired orders as deleted
				$date1 = new DateTime($order['order_date']);
				$date2 = new DateTime($order['expiring_date']);
			}
				
			$interval = $date1->diff($date2);
				
			if((($interval->y) <= 0) || (($interval->m) <= 0)){
				$customer = Customers::getAllInfo($order ['customer_id']);
	
				// Get the fastlink attached
				$link_exist = Fastlinks::findlinks ( $order ['order_id'], $order ['customer_id'], 'orders' );
				if (count ( $link_exist ) > 0) {
					$fastlink = $link_exist [0] ['code'];
				} else {
					$fastlink = Fastlinks::CreateFastlink ( 'orders', 'edit', json_encode ( array ('id' => $order ['order_id'] ) ), 'orders', $order ['order_id'], $customer ['customer_id'] );
				}
					
				$customer_url = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/$fastlink";
					
				Shineisp_Commons_Utilities::sendEmailTemplate($customer ['email'], 'order_deleted', array(
						'orderid'    => $order['order_number']
						,':shineisp:' => $customer
						,'fullname' => $customer['fullname']
						,'url'        => $customer_url
				), null, null, null, null, $customer['language_id'], Settings::findbyParam('cron_notify'));
					
					
				// Set the order as deleted
				Orders::set_status($order['order_id'], Statuses::id('deleted', 'orders'));
			}
		}
		return true;
	}
	
	/**
	 * reminderAction
	 * This action send to every customers an email
	 * reminder about their expiring services and domains
	 */
	public static function remindersEmail() {
		$i = 0;
		$customers = array ();
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
	
		/* We have to start to get all the domains that them expiring date is today
		 then we have to create a custom array sorted by customerID in order to
		group services and domains of a particular customer.
		*/
	
		// Get all the active domains that expire in 1 day
		$domains = Domains::getExpiringDomainsByRange ( 0, 30, Statuses::id('active', 'domains') );
	
		if ($domains) {
			// Create the customer group list for the email summary
			foreach ( $domains as $domain ) {
				if ($domain ['reseller']) {
					$invoice_dest = Customers::getAllInfo ( $domain ['reseller'] );
					$customers [$domain ['customer_id']] ['id'] = $invoice_dest ['customer_id'];
					$customers [$domain ['customer_id']] ['fullname'] = $invoice_dest ['firstname'] . " " . $invoice_dest ['lastname'] . " " . $invoice_dest ['company'];
					$customers [$domain ['customer_id']] ['email'] = $invoice_dest ['email'];
					$customers [$domain ['customer_id']] ['language_id'] = $invoice_dest ['language_id'];
				} else {
					$customers [$domain ['customer_id']] ['id'] = $domain ['customer_id'];
					$customers [$domain ['customer_id']] ['fullname'] = $domain ['fullname'];
					$customers [$domain ['customer_id']] ['email'] = $domain ['email'];
					$customers [$domain ['customer_id']] ['language_id'] = $domain ['language_id'];
				}
				$customers [$domain ['customer_id']] ['products'] [$i] ['name'] = $domain ['domain'];
				$customers [$domain ['customer_id']] ['products'] [$i] ['type'] = "domain";
				$customers [$domain ['customer_id']] ['products'] [$i] ['renew'] = $domain ['renew'];
				$customers [$domain ['customer_id']] ['products'] [$i] ['expiring_date'] = $domain ['expiringdate'];
				$customers [$domain ['customer_id']] ['products'] [$i] ['days'] = $domain ['days'];
				$customers [$domain ['customer_id']] ['products'] [$i] ['oldorderitemid'] = $domain ['detail_id'];
				$i ++;
			}
		}
	
		/*
		 * Now we have to get the services expired and we have to sum the previous $customers array with these
		* new information.
		*/
	
		// Get all the services active that expire the day after
		$services = OrdersItems::getExpiringServicesByRange(0, 30, Statuses::id("complete", "orders") );
		if ($services) {
			// Create the customer group list for the email summary
			foreach ( $services as $service ) {
				if ($service ['reseller']) {
					$invoice_dest = Customers::getAllInfo ( $service ['reseller'] );
					$customers [$service ['customer_id']] ['id'] = $invoice_dest ['customer_id'];
					$customers [$service ['customer_id']] ['fullname'] = $invoice_dest ['firstname'] . " " . $invoice_dest ['lastname'] . " " . $invoice_dest ['company'];
					$customers [$service ['customer_id']] ['email'] = $customer_email = Contacts::getEmails($invoice_dest ['customer_id']);
					$customers [$service ['customer_id']] ['password'] = $invoice_dest ['password'];
					$customers [$service ['customer_id']] ['language_id'] = $invoice_dest ['language_id'];
				} else {
					$customers [$service ['customer_id']] ['id'] = $service ['id'];
					$customers [$service ['customer_id']] ['fullname'] = $service ['fullname'];
					$customers [$service ['customer_id']] ['email'] = $customer_email = Contacts::getEmails($service ['id']);
					$customers [$service ['customer_id']] ['password'] = $service ['password'];
					$customers [$service ['customer_id']] ['language_id'] = $service ['language_id'];
				}
				$customers [$service ['customer_id']] ['products'] [$i] ['name'] = $service ['product'];
				$customers [$service ['customer_id']] ['products'] [$i] ['type'] = "service";
				$customers [$service ['customer_id']] ['products'] [$i] ['renew'] = $service ['renew'];
				$customers [$service ['customer_id']] ['products'] [$i] ['expiring_date'] = $service ['expiringdate'];
				$customers [$service ['customer_id']] ['products'] [$i] ['days'] = $service ['days'];
				$customers [$service ['customer_id']] ['products'] [$i] ['oldorderitemid'] = $service ['detail_id'];
				$i ++;
			}
		}
	
		// EMAIL SUMMARY FOR ALL THE EXPIRED AND NOT AUTORENEWABLE DOMAINS/SERVICES
		// =========================================================================
		// Create the emailS for the customers
		if (count ( $customers ) > 0) {
			// For each client do ...
			foreach ( $customers as $customer ) {
				$items = array();
				
				$translator->setLocale(Languages::get_locale($customer['language_id']));
				
				// Check if there are some product to be expired
				if (count ( $customer ['products'] ) > 0) {
					$i = 0;
					// For each product do ...
					foreach ( $customer ['products'] as $product ) {
	
						$items[$i][$translator->translate("Expiry Date")] = $product ['expiring_date'];
						$items[$i][$translator->translate("Days")] = $product ['days'];
						$items[$i][$translator->translate("Description")] = $product ['name'];
						if ($product ['renew']) {
							$items[$i][$translator->translate("Auto Renewal")] = $translator->translate("Active");
						} else {
							$items[$i][$translator->translate("Auto Renewal")] = $translator->translate("Not Active");
						}
						$i++;
					}
				}
				$items = Shineisp_Commons_Utilities::array2table($items);
				if (! empty ( $items )) {
					Shineisp_Commons_Utilities::sendEmailTemplate($customer ['email'], 'reminder', array(
							'items'      => $items
							,'fullname' => $customer['fullname']
					), null, null, null, null, $customer['language_id'], Settings::findbyParam('cron_notify'));
				}
			}
		}
	
		return true;
	}
	
	
	/**
	 * CREATE THE ORDER FOR ALL THE AUTORENEWABLE DOMAINS/SERVICES
	 * Check all the services [domains, products] and create the orders for each customer only if the service has been set as renewable
	 * @return void
	 */
	public static function checkServices() {
		try {
			$i = 0;
			$customers = array ();
	
			/* We have to start to get all the domains that them expiring date is today
			 then we have to create a custom array sorted by customerID in order to
			group services and domains of a specific customer.
			*/
	
			// Get all the active domains that expire in 1 day
			$domains = Domains::getExpiringDomainsByDays ( 1, Statuses::id("active", "domains") );
	
			if ($domains) {
				Shineisp_Commons_Utilities::log("There are (".count($domains).") new domains to renew");
	
				// Create the customer group list for the email summary
				foreach ( $domains as $domain ) {
	
					if (is_numeric($domain ['reseller'])) {
						$invoice_dest = Customers::getAllInfo ( $domain ['reseller'] );
						$customers [$domain ['customer_id']] ['id'] = $invoice_dest ['customer_id'];
						$customers [$domain ['customer_id']] ['fullname'] = $invoice_dest ['firstname'] . " " . $invoice_dest ['lastname'] . " " . $invoice_dest ['company'];
						$customers [$domain ['customer_id']] ['email'] = $invoice_dest ['email'];
						$customers [$domain ['customer_id']] ['language_id'] = $invoice_dest ['language_id'];
					} else {
						$customers [$domain ['customer_id']] ['id'] = $domain ['customer_id'];
						$customers [$domain ['customer_id']] ['fullname'] = $domain ['fullname'];
						$customers [$domain ['customer_id']] ['email'] = $domain ['email'];
						$customers [$domain ['customer_id']] ['language_id'] = $domain ['language_id'];
					}
	
					$customers [$domain ['customer_id']] ['products'] [$i] ['name'] = $domain ['domain'];
					$customers [$domain ['customer_id']] ['products'] [$i] ['type'] = "domain";
					$customers [$domain ['customer_id']] ['products'] [$i] ['renew'] = $domain ['renew'];
					$customers [$domain ['customer_id']] ['products'] [$i] ['expiring_date'] = $domain ['expiringdate'];
					$customers [$domain ['customer_id']] ['products'] [$i] ['days'] = $domain ['days'];
	
					// Get the last old order item id
					if(!empty($domain['oldorders'])){
						// find the domain
						foreach ($domain['oldorders'] as $olditemorder){
	
							// Get all the information from the old order
							$olditem = OrdersItems::getAllInfo($olditemorder['orderitem_id']);
	
							// Check if the old order item refers to the domain selected
							if(!empty($olditem['parameters']) && !empty($olditem['Orders']['OrdersItemsDomains'][0]['Domains']['tld_id'])){
	
								// Get the old configuration parameters
								$params = json_decode($olditem['parameters'], true);
								// 								Zend_Debug::dump($olditem);
								// 								Zend_Debug::dump($params);
								// 								Zend_Debug::dump($domain['domain']);
								// Extract the domain name and match it with the domain selected
								if(!empty($params['domain']) && $params['domain']['name'] == $domain['domain']){
									$customers [$domain ['customer_id']] ['products'] [$i] ['oldorderitemid'] = $olditemorder['orderitem_id'];
								}
							}
						}
					}
					Shineisp_Commons_Utilities::log("- " . $domain ['domain']);
					$i ++;
				}
			}
	
			/*
			 * Now we have to get the services expired and we have to sum the previous $customers array with these
			* new information.
			*/
	
			// Get all the services active that expire the day after
			$services = OrdersItems::getExpiringServicesByDays ( 1, Statuses::id("complete", "orders") );
	
			if ($services) {
	
				Shineisp_Commons_Utilities::log("There are (".count($services).") new services to renew");
	
				// Create the customer group list for the email summary
				foreach ( $services as $service ) {
					if (is_numeric($service ['reseller'])) {
						$invoice_dest = Customers::getAllInfo ( $service ['reseller'] );
						$customers [$service ['customer_id']] ['id'] = $invoice_dest ['customer_id'];
						$customers [$service ['customer_id']] ['fullname'] = $invoice_dest ['firstname'] . " " . $invoice_dest ['lastname'] . " " . $invoice_dest ['company'];
						$customers [$service ['customer_id']] ['email'] = $invoice_dest ['email'];
						$customers [$service ['customer_id']] ['password'] = $invoice_dest ['password'];
						$customers [$service ['customer_id']] ['language_id'] = $invoice_dest ['language_id'];
					} else {
						$customers [$service ['customer_id']] ['id'] = $service ['id'];
						$customers [$service ['customer_id']] ['fullname'] = $service ['fullname'];
						$customers [$service ['customer_id']] ['email'] = $service ['email'];
						$customers [$service ['customer_id']] ['password'] = $service ['password'];
						$customers [$service ['customer_id']] ['language_id'] = $service ['language_id'];
					}
					$customers [$service ['customer_id']] ['products'] [$i] ['name'] = $service ['product'];
					$customers [$service ['customer_id']] ['products'] [$i] ['type'] = "service";
					$customers [$service ['customer_id']] ['products'] [$i] ['renew'] = $service ['renew'];
					$customers [$service ['customer_id']] ['products'] [$i] ['expiring_date'] = $service ['expiringdate'];
					$customers [$service ['customer_id']] ['products'] [$i] ['days'] = $service ['days'];
					$customers [$service ['customer_id']] ['products'] [$i] ['oldorderitemid'] = $service ['detail_id'];
	
					Shineisp_Commons_Utilities::log("- " . $service ['product']);
					$i ++;
				}
			}
	
			// Create the email messages for the customers
			if (count ( $customers ) > 0) {
	
				foreach ( $customers as $customer ) {
					$items = "";
	
					// **** CREATE THE ORDER FOR ALL THE AUTORENEWABLE DOMAINS/SERVICES ***
					// ============================================================
					// Renew all the services and domain where the customer has choosen the autorenew of the service.
					$orderID = Orders::renewOrder ( $customer ['id'], $customer ['products'] );
	
					if (is_numeric ( $orderID )) {
						$link = Fastlinks::findlinks ( $orderID, $customer ['id'], 'orders' );
	
						// Create the fast link to include in the email
						if (! empty ( $link [0] ['code'] )) {
							$url = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/" . $link [0] ['code'];
						} else {
							$url = "http://" . $_SERVER ['HTTP_HOST'];
						}
							
						Shineisp_Commons_Utilities::sendEmailTemplate($customer ['email'], 'order_renew', array(
								'fullname' => $customer['fullname'],
								':shineisp:' => $customer
								,'url'       => $url
						), null, null, null, null, $customer['language_id'], Settings::findbyParam('cron_notify'));
	
	
					}
				}
			}
	
			/*
			 * Now we have to set as expired all the domains records that the date is the date of the expiring of the domain
			* // Expired
			*/
			$dq = Doctrine_Query::create ()->update ( 'Domains d' )->set ( 'd.status_id', Statuses::id('expired', 'domains') )->where ( 'DATEDIFF(d.expiring_date, CURRENT_DATE) <= ?', 0 )->addWhere ( 'DATEDIFF(d.expiring_date, CURRENT_DATE) >= ?', 0 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
	
			/*
			 * Now we have to set as closed all the domains records that the date is older of -2 days
			* // Closed
			*/
			$dq = Doctrine_Query::create ()->update ( 'Domains d' )->set ( 'd.status_id', Statuses::id('suspended', 'domains') )->where ( 'DATEDIFF(d.expiring_date, CURRENT_DATE) <= ?', - 2 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
	
			/*
			 * Now we have to set as expired all the services records
			* // Expired
			*/
			$dq = Doctrine_Query::create ()->update ( 'OrdersItems oi' )->set ( 'oi.status_id', Statuses::id('expired', 'orders') )->where ( 'DATEDIFF(oi.date_end, CURRENT_DATE) <= ?', 0 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
	
			/*
			 * Now we have to set as deleted all the services records
			* // Deleted
			*/
			$dq = Doctrine_Query::create ()->update ( 'OrdersItems oi' )->set ( 'oi.status_id', Statuses::id('deleted', 'orders') )->where ( 'DATEDIFF(oi.date_end, CURRENT_DATE) <= ?', - 2 );
			$dq->execute ( null, Doctrine::HYDRATE_ARRAY );
	
			Shineisp_Commons_Utilities::sendEmailTemplate( null, 'cron', array('cronjob' => 'Check Services'), null, null, null, null, null, Settings::findbyParam('cron_notify'));
	
	
		} catch ( Exception $e ) {
			Shineisp_Commons_Utilities::logs ($e->getMessage(), "cron.log" );
			return false;
		}
	
		return true;
	}
	
	
	/**
	 * Send the newsletter to the queue
	 */
	public static function send_queue($test=FALSE, $id=NULL){
		$queue = NewslettersHistory::get_active_queue($test, $id);
	
		$isp = Isp::getActiveISP ();
		try{
			// Get the template from the main email template folder
			$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'newsletter' );
				
			if(!empty($retval) && !empty($queue)){
	
				$contents = Newsletters::fill_content();
					
				$subject = $retval ['subject'];
				$template =  $retval ['template'] ;
	
				$subject = str_replace ( "[subject]", $queue[0] ['Newsletters']['subject'], $subject );
				$template = str_replace ( "[subject]", $queue[0] ['Newsletters']['subject'], $template );
				$template = str_replace ( "[body]", $queue[0] ['Newsletters']['message'], $template );
	
				foreach ($contents as $name=>$content) {
					$template = str_replace ( "[" . $name . "]", $content, $template );
				}
	
				foreach ($isp as $field=>$value) {
					$template = str_replace ( "[" . $field . "]", $value, $template );
				}
	
				$template = str_replace ( "[url]", "http://" . $_SERVER ['HTTP_HOST'] . "/media/newsletter/" , $template );
	
				foreach ($queue as $item) {
						
					// Send a test of the newsletter to the default isp email otherwise send an email to the queue
					if ($test){
						$body = str_replace ( "[optout]", "#", $template);
						Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $isp ['email'], null, "<!--TEST --> " . $subject, $body, true);
						break;
					}else{
	
						// Create the optout link to be added in the email
						$body = str_replace ( "[optout]", '<a href="http://' . $_SERVER ['HTTP_HOST'] . "/newsletter/optout/id/" . MD5($item['NewslettersSubscribers'] ['email']) . '" >Unsubscribe</a>', $template );
	
						$result = Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $item['NewslettersSubscribers'] ['email'], null, $subject, $body, true);
						if($result === true){
							NewslettersHistory::set_status($item['subscriber_id'], $item['newsletter_id'], 1, "Mail Sent");
						}else{
							NewslettersHistory::set_status($item['subscriber_id'], $item['newsletter_id'], 0, $result['message']);
						}
						Newsletters::set_sending_date($item['news_id']);
					}
				}
			}
		}catch (Exception $e){
			echo $e->getMessage();
			return false;
		}
		return true;
	}
	
}