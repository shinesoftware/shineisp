<?php

class RssController extends Shineisp_Controller_Default {
	protected $translations;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}

	/**
	 * Create a RSS file with the CMS pages and Products
	 */
	public function indexAction() {
		$out = "";
		try {
			$ISP = Shineisp_Registry::get('ISP');
			$ns = new Zend_Session_Namespace ();
			$localeID = $ns->idlang;
			$locale = $ns->lang;

			$feed = new Zend_Feed_Writer_Feed ();
			$feed->setTitle ( $ISP->company );
			$feed->setLink ( $ISP->website );
			$feed->setFeedLink ( 'http://' . $_SERVER ['HTTP_HOST'] . '/rss', 'atom' );
			$feed->addAuthor ( array ('name' => $ISP->company, 'email' => $ISP->email, 'uri' => $ISP->website ) );
			$feed->setEncoding('UTF8');
			$feed->setDateModified ( time () );
			$feed->addHub ( $ISP->website );

			// Get all the cms pages
			$records = CmsPages::getRssPages($locale);

			foreach ( $records as $record ) {
				$link = 'http://' . $_SERVER ['HTTP_HOST'] . '/cms/' . $record ['var'] . '.html';
				self::createEntry($feed, $record ['title'], $record ['body'], $link);
			}

			// Get all the products
			$records = Products::getAllHighlighted($localeID);

			foreach ( $records as $record ) {
				$title = $record ['ProductsData'][0]['name'];
				$descritption = strip_tags($record ['ProductsData'][0]['shortdescription']);
				$inserted_at = !empty($record ['inserted_at']) ? strtotime($record ['inserted_at']) : null;
				$updated_at = !empty($record ['updated_at']) ? strtotime($record ['updated_at']) : null;
				$link = 'http://' . $_SERVER ['HTTP_HOST'] . '/' . $record ['uri'] . '.html';
				self::createEntry($feed, $title, $descritption, $link, $inserted_at, $updated_at);
			}

			/**
			 * Render the resulting feed to Atom 1.0 and assign to $out.
			 * You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
			 */
			$out = $feed->export ( 'atom' );

		} catch ( Zend_Feed_Exception $e ) {
			die ( $e->getMessage () );
		}
		die ( $out );
	}

	/**
	 * Add one or more entries. Note that entries must
	 * be manually added once created.
	 */	
	private function createEntry($feed, $title, $descritption, $link, $dateCreated=null, $dateModified=null){
	
		if (! empty ( $title )) {
			$entry = $feed->createEntry ();
			$entry->setTitle( $title );
			if(!empty($descritption)){
				$descritption = strip_tags($descritption);
				$entry->setDescription (Shineisp_Commons_Utilities::CropSentence($descritption, 300, "..."));
			}
			$entry->setLink ( $link );
			$entry->setDateModified ( !empty($dateModified) ? $dateModified : time () );
			$entry->setDateCreated ( !empty($dateCreated) ? $dateCreated : time ()  );
			$feed->addEntry ( $entry );
		}	
		return $feed;
	}
}