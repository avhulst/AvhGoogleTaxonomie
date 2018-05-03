<?php
/**
 * Google Taxonomie Helfer
 *
 * @link http://www.vanhulst.de/GoogleTaxonomieHelfer
 * @copyright Copyright (c) 2015, Andreas van Hulst
 * @author Andreas van Hulst
 * @package Shopware
 * @subpackage Plugins
 */
class Shopware_Plugins_Backend_AvhGoogleTaxonomie_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
	private static $_moduleDesc = 'AvhGoogleTaxonomie';

	var $modulType  = 	"Standard" ;


	/**
	 * Info plugin method
	 *
	 */
	public function getInfo() {
		return array(
    		'version' => $this->getVersion(),
			'autor' => 'Andreas van Hulst',
			'label' => "Google Taxonomie Helfer ".$this->modulType ,
			'source' => "Default",
            'description' => utf8_encode('<p style="font-size:12px; font-weight: bold;">Andreas van Hulst <p></p> <p style="font-size:12px">Kopiert den Taxonomie Eintrag aus gew&auml;hlten Kategorie Freitextfeld in das ausgew&auml;hlte Artikel Freitextfeld</p>'),
            'license' => 'commercial',
    		'copyright' => '&copy; Andreas van Hulst',
			'support' => 'info@vh-networks.de',
			'link' => 'http://www.vh-networks.de/GoogleTaxonomieHelfer'
			);
	}

	/**
	 * Version plugin method
	 *
	 */	
	public function getVersion(){
	  return "1.0.1";
	}
	
	/**
	 * Install plugin method
	 *
	 * @return bool
	 */  
	public function install()
	{
	if (!$this->assertMinimumVersion("4.3.1")){
     		throw new Enlight_Exception("This Plugin needs min shopware 4.3.1");
		}
    
        $this->subscribeEvent(
            'Shopware\Models\Article\Article::postUpdate',
            'postUpdateDetail'
        );     
    
        $this->registerCronJobs();
    
	$form = $this->Form();
	
	
	$form->setElement('select', 'KategorieFreitextFeld', array(
             'label' => 'Kategorie Freitextfeld', 'value' => 1,
             'store' => array(
                 array(1, 'Freitextfeld (1)'), array(2, 'Freitextfeld (2)'), array(3, 'Freitextfeld (3)'), array(4, 'Freitextfeld (4)'), array(5, 'Freitextfeld (5)'), array(6, 'Freitextfeld (6)')
             )));
	$form->setElement('select', 'ArtikelFreitextfeld', array(
             'label' => 'Artikel Freitextfeld', 'value' => 1,
             'store' => array(
                 array(1, 'Freitextfeld (1)'), array(2, 'Freitextfeld (2)'), array(3, 'Freitextfeld (3)'), array(4, 'Freitextfeld (4)'), 
                 array(5, 'Freitextfeld (5)'), array(6, 'Freitextfeld (6)'), array(7, 'Freitextfeld (7)'),
                 array(8, 'Freitextfeld (8)'), array(9, 'Freitextfeld (9)'), array(10, 'Freitextfeld (10)'),
                 array(11, 'Freitextfeld (11)'), array(12, 'Freitextfeld (12)'), array(13, 'Freitextfeld (13)'),
                 array(14, 'Freitextfeld (14)'), array(15, 'Freitextfeld (15)'), array(16, 'Freitextfeld (16)'),
                 array(17, 'Freitextfeld (17)'), array(18, 'Freitextfeld (18)'), array(19, 'Freitextfeld (19)'), array(20, 'Freitextfeld (20)')
        )));
	$form->setElement('boolean', 'showres', array('label'=>'Logfile erzeugen', 'value' => true, 'description' => 'Eine Logdatei (GoogleTaxonomie.log) wird in das shoproot abgelegt '));
	
	return true;
	}

    /**
     * Update plugin method
     *
     * @return bool
     */  
    public function update()
    {
        //Notihng
        return true;
    }

    
    /**
     * Cronjob Registrierung
     *
     * @return bool
     */      
    private function registerCronJobs()
    {
        $this->subscribeEvent('Shopware_CronJob_AvhGoogleTaxonomie','GoogleTaxonomieCronjob');
             
        $this->createCronJob('Avh Google Taxonomie Helfer','Shopware_CronJob_AvhGoogleTaxonomie',86400,true);
     
    }  

    /**
     * Cronjob Job :-)
     *
     * @return bool
     */  
    public function GoogleTaxonomieCronjob(Shopware_Components_Cron_CronJob $job)
    {
        $this->copy();
        return true;
    }

     
    /**
     * Änderung an Artikel Details nach dem Speichern.
     *
     * @return bool
     */       
     public function postUpdateDetail(Enlight_Event_EventArgs $arguments) {
         $modelManager = $arguments->get('entityManager');
         $model = $arguments->get('entity');
         $articleID = $model->getId();
         $this->copyone($articleID);

    }
         
    /**
     * Kopiert die Kategorieattribute nach Alrtikelatribut
     * 
     * @return array
     */        
        public function copy()
        {
            $ret2[] = array();
            $katfeld = "attribute" . $this->Config()->KategorieFreitextFeld;
            $artfeld = "attr" . $this->Config()->ArtikelFreitextfeld;
            $getsql = "SELECT a.articleID, a.categoryID, b.$katfeld
                        from s_articles_categories AS a
                        JOIN  s_categories_attributes AS b ON a.categoryID = b.categoryID ";
            $getsql .= "WHERE LENGTH( b.$katfeld ) >1"; //Schliesst leere Einträge vom Update aus!

            $res1 = Shopware()->Db()->fetchAll($getsql);

            foreach ($res1 as $value1) {
                if(strlen($value1[$katfeld])>1)
                {
                    $articleID = $value1['articleID'];
                    $taxo = $value1[$katfeld];
                    $q1 = "SELECT id FROM `s_articles_details` WHERE articleID = $articleID";

                    $ret1 = Shopware()->Db()->fetchAll($q1);
                    foreach ($ret1 as $value2) {
                        $articledetailsID = $value2['id'];
                        $q2 = "UPDATE s_articles_attributes SET $artfeld = '$taxo' WHERE articledetailsID = $articledetailsID";
                        /* SCHEIB TAXANOMIE */
                        Shopware()->Db()->exec($q2);

                        $ret2[] = array('ArticleID' => "articleID: " . $articleID, 'Artikel Freitextfeld' => $artfeld, 'Taxonomie' => $taxo);
                    }
                }
            }
            $this->avh_debug($ret2);
        return true;
        }

        
    /**
     * Kopiert die Kategorieattribute nach Alrtikelatribut für einen Artikel
     * 
     * @return array
     */        
        public function copyone($articleID)
        {
            $ret[] = array();
            $katfeld = "attribute" . $this->Config()->KategorieFreitextFeld;
            $artfeld = "attr" . $this->Config()->ArtikelFreitextfeld;
            $getsql = "SELECT a.articleID, a.categoryID, b.$katfeld
                        from s_articles_categories AS a
                        JOIN  s_categories_attributes AS b ON a.categoryID = b.categoryID                        
                      ";     
            $getsql .= "WHERE a.articleID = $articleID ";
            $getsql .= "AND LENGTH( b.$katfeld ) >1"; //Schliesst leere Einträge vom Update aus!                      

            $res = Shopware()->Db()->fetchRow($getsql);

            if(strlen($res['attribute1'])>1)
                 {
                     $taxo = $res['attribute1'];
                     $q1 = "SELECT id FROM `s_articles_details` WHERE articleID = $articleID";
                     $ret1 = Shopware()->Db()->fetchAll($q1);
                     foreach ($ret1 as $value2) {
                         $articledetailsID = $value2['id'];
                         $q2 = "UPDATE s_articles_attributes SET $artfeld = '$taxo' WHERE articledetailsID = $articledetailsID";
                         try {
                             $res2 = Shopware()->Db()->exec($q2);
                         } catch (Exception $e) {
                             $err = $e->getMessage();
                         }
                         $ret[] = array('ArticleID' => "articleID: " . $articleID, 'Artikel Freitextfeld' => $artfeld, 'Taxonomie' => $taxo);
                     }
                 }

            $this->avh_debug($ret);
            return true;

        }
        
                        
    /**
     * Loggt dumps in eine Datei
     * Wenn $newfile == 1 wird das Log geleert
     */ 
    public function avh_debug ($log,$newfile)
    {
        if($this->Config()->showres == false)
        {
            return false;
        } else {
            ob_start();
            print_r($log);
            if($newfile==1) //Leert die Log Datei vor dem Schreiben
            {
                file_put_contents('GoogleTaxonomie.log', ob_get_contents());
            }
            else
            {
                file_put_contents('GoogleTaxonomie.log', file_get_contents('GoogleTaxonomie.log') . "\n" . ob_get_contents());
            }
            ob_end_clean();
        }
    }
             
}
 
