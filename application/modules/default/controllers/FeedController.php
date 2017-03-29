<?php

class FeedController extends Shineisp_Controller_Default {
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
    public function atomAction() {
        $tag = $this->getRequest()->getParam('tag');

        echo '<?xml version="1.0" encoding="utf-8" ?>
                <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
                    <channel>
                        <atom:link href="http://www.shinesoftware.it/feed/atom/tag/'.$tag.'" rel="self" type="application/rss+xml"/>
                        <title>Shine Software Internet Solutions</title>
                        <link>http://www.shinesoftware.com/</link>
                        <description>Payment Solutions ('.$tag.')</description>
                        <language>it</language>
                        <lastBuildDate>Thu Jan 10 15:51:04 2017</lastBuildDate>
                        <ttl>0</ttl>
                          <item>
                            <title>Nuovo Aggiornamento</title>
                            <link>http://www.shinesoftware.com/</link>
                            <severity>1</severity>
                            <description><![CDATA[Richiedete il nuovo aggiornamento direttamente aprendo una segnalazione ed inserendo il tipo di modulo ed il dominio licenziato. Riceverete in breve l\'aggiornamento via email.]]></description>
                            <pubDate>Thu Jan 10 15:51:04 2017</pubDate>
                        </item>
                    </channel>
                </rss>';
        die;

        $tag = $this->getRequest()->getParam('tag');
        if(empty($tag)){
            die();
        }

        try {
            $ISP = Shineisp_Registry::get('ISP');
            $ns = new Zend_Session_Namespace ();
            $localeID = $ns->idlang;
            $locale = $ns->lang;

            $feed = new Zend_Feed_Writer_Feed ();
            $feed->setTitle ( $ISP->company );
            $feed->setLink ( $ISP->website );
            $feed->setLanguage( 'it' );
            $feed->setEncoding('UTF8');
            $feed->setDescription('Shine Software Magento Solutions');
            $feed->setDateModified ( time () );
            $feed->addHub ( $ISP->website );

            // Get all the cms pages
            $records = CmsPages::getRssPages($locale);

            foreach ( $records as $record ) {
                if(in_array($tag, array_map('trim', explode(',', $record ['keywords'])))){
                    $link = 'http://' . $_SERVER ['HTTP_HOST'] . '/cms/' . $record ['var'] . '.html';
                    self::createEntry($feed, $record ['title'], $record ['body'], $link);
                }
            }

            /**
             * Render the resulting feed to Atom 1.0 and assign to $out.
             * You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
             */
            $out = $feed->export ( 'rss' );

        } catch ( Zend_Feed_Exception $e ) {
            die ( $e->getMessage () );
        }
        die ( $out );
    }

    /**
     * Add one or more entries. Note that entries must
     * be manually added once created.
     */
    private function createEntry($feed, $title, $description, $link, $dateCreated=null, $dateModified=null){

        if (! empty ( $title )) {
            $entry = $feed->createEntry ();
            $entry->setTitle( $title );
            if(!empty($description)){
                $description = strip_tags($description);
                $entry->setDescription (Shineisp_Commons_Utilities::CropSentence($description, 300, "..."));
            }
            $entry->setLink ( $link );
            $entry->setDateModified ( !empty($dateModified) ? $dateModified : time () );
            $entry->setDateCreated ( !empty($dateCreated) ? $dateCreated : time ()  );
            $feed->addEntry ( $entry );
        }
        return $feed;
    }
}