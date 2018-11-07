<?php

namespace AvhGoogleTaxonomie;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use AvhGoogleTaxonomie\Bootstrap\Database;
use Doctrine\ORM\Tools\SchemaTool;

class AvhGoogleTaxonomie extends Plugin
{

    /***
     * @param InstallContext $context
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function install(InstallContext $context)
    {
        $database = new Database(
            $this->container->get('models')
        );

        $database->install();

        $em = $this->container->get('models');
        $schemaTool = new SchemaTool($em);
        $schemaTool->updateSchema(
            [$em->getClassMetadata(\AvhGoogleTaxonomie\Models\AvhTaxonomie::class)],
            true
        );

        $service = $this->container->get('shopware_attribute.crud_service');

        $service->update('s_export_attributes', 'Avh_Merchant_Export', 'boolean', [
            'label' => 'Google Merchant Exporter',
            'supportText' => 'Aktiviere die Erweiterung',
            'translatable' => false,
            'displayInBackend' => true
        ]);

        $service->update('s_articles_attributes','avh_google_title','string', [
            'label' => 'GM: Title',
            'supportText' => 'Alternativer Title für den Shopping Feed, wenn das Feld leer ist wird die Produktname verwendet',
            'displayInBackend' => true,
            'position' => 98,
            'custom' => true,
        ]);

        $service->update('s_articles_attributes','avh_google_description','text', [
            'label' => 'GM: Beschreibung',
            'supportText' => 'Alternative Beschreibung für den Shopping Feed, wenn das Feld leer ist wird die Produktbeschreibung verwendet',
            'displayInBackend' => true,
            'position' => 99,
            'custom' => true,
        ]);

        foreach (array('s_articles_attributes','s_categories_attributes') as $dbtable)
        {
            $service->update(
                $dbtable,
                'avh_taxonomie',
                'single_selection',
                [
                    'entity' => \AvhGoogleTaxonomie\Models\AvhTaxonomie::class,
                    'displayInBackend' => true,
                    'label' => 'GM: Google Taxonomie',
                    'supportText' => ' Durch die Kategorisierung Ihres Artikels stellen Sie sicher, dass Ihre Anzeige bei den richtigen Suchergebnissen ausgeliefert wird.',
                    'position' => 101
                ],
                null,
                true
            );

            $service->update($dbtable,'avh_google_itemconditon','combobox', [
                'label' => 'GM: Zustand',
                'supportText' => 'Erforderlich für jeden Artikel!',
                'displayInBackend' => true,
                'position' => 102,
                'custom' => true,
                'arrayStore' => [
                    ['key' => 'NewCondition', 'value' => 'Neu'],
                    ['key' => 'RefurbishedCondition', 'value' => 'Generalüberholt'],
                    ['key' => 'UsedCondition', 'value' => 'Gebraucht'],
                ],
            ]);

            $service->update($dbtable,'avh_google_energy_efficiency_class','combobox', [
                'label' => 'GM: Energie Effiziensklasse',
                'supportText' => 'Erforderlich, wenn ein Artikel Nacktheit enthält, sexuell anzüglich ist oder bewusst sexuelle Aktivitäten fördert.',
                'displayInBackend' => true,
                'position' => 103,
                'custom' => true,
                'arrayStore' => [
                    ['key' => 'A+++', 'value' => 'A+++'],
                    ['key' => 'A++', 'value' => 'A++'],
                    ['key' => 'A+', 'value' => 'A+'],
                    ['key' => 'A', 'value' => 'A'],
                    ['key' => 'B', 'value' => 'B'],
                    ['key' => 'C', 'value' => 'C'],
                    ['key' => 'D', 'value' => 'D'],
                    ['key' => 'E', 'value' => 'E'],
                    ['key' => 'F', 'value' => 'F'],
                    ['key' => 'G', 'value' => 'G']
                ],
            ]);

            $service->update($dbtable,'avh_google_age_group','combobox', [
                'label' => 'GM: Altersgruppe',
                'supportText' => 'Erforderlich für alle Artikel, die nach Alter variieren',
                'displayInBackend' => true,
                'position' => 104,
                'custom' => true,
                'arrayStore' => [
                    ['key' => 'Erwachsene', 'value' => 'Erwachsene'],
                    ['key' => 'Kinder', 'value' => 'Kinder'],
                    ['key' => 'Kleinkinder', 'value' => 'Kleinkinder'],
                    ['key' => 'Säuglinge', 'value' => 'Säuglinge'],
                    ['key' => 'Neugeborene', 'value' => 'Neugeborene']
                ],
            ]);

            $service->update($dbtable,'avh_google_gender','combobox', [
                'label' => 'GM: Geschlecht',
                'supportText' => 'Erforderlich für alle Artikel, die nach Geschlecht variieren',
                'displayInBackend' => true,
                'position' => 105,
                'custom' => true,
                'arrayStore' => [
                    ['key' => 'Herren', 'value' => 'Herren'],
                    ['key' => 'Kinder', 'value' => 'Damen'],
                    ['key' => 'Unisex', 'value' => 'Unisex']
                ],
            ]);

            $service->update($dbtable,'avh_google_material','string', [
                'label' => 'GM: Material',
                'supportText' => 'Erforderlich für alle Artikel, die nach Material variieren',
                'displayInBackend' => true,
                'position' => 106,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_color','string', [
                'label' => 'GM: Farbe',
                'supportText' => 'Erforderlich für alle Artikel, die in unterschiedlichen Farben erhältlich sind',
                'displayInBackend' => true,
                'position' => 107,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_pattern','string', [
                'label' => 'GM: Muster',
                'supportText' => 'Erforderlich für alle Artikel, die nach Muster variieren',
                'displayInBackend' => true,
                'position' => 108,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_size','string', [
                'label' => 'GM: Größe',
                'supportText' => 'Erforderlich für alle Artikel mit unterschiedlichen Größen',
                'displayInBackend' => true,
                'position' => 109,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_size_type','combobox', [
                'label' => 'GM: Größentyp',
                'supportText' => 'Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 110,
                'custom' => true,
                'arrayStore' => [
                    ['key' => 'regular', 'value' => 'Normalgröße'],
                    ['key' => 'petite', 'value' => 'Petite-Größe'],
                    ['key' => 'plus', 'value' => 'Übergröße Frauen'],
                    ['key' => 'big and tall', 'value' => 'Übergröße Männer'],
                    ['key' => 'maternity', 'value' => 'Umstandsgröße']
                ],
            ]);

            $service->update($dbtable,'avh_google_size_system','combobox', [
                'label' => 'GM: Größensystem',
                'supportText' => 'Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 111,
                'custom' => true,
                'arrayStore' => [
                    ['key' => 'DE', 'value' => 'DE'],
                    ['key' => 'UK', 'value' => 'UK'],
                    ['key' => 'US', 'value' => 'US'],
                    ['key' => 'EU', 'value' => 'EU'],
                    ['key' => 'FR', 'value' => 'FR'],
                    ['key' => 'IT', 'value' => 'IT'],
                    ['key' => 'AU', 'value' => 'AU'],
                    ['key' => 'BR', 'value' => 'BR'],
                    ['key' => 'CN', 'value' => 'CN'],
                    ['key' => 'JP', 'value' => 'JP'],
                    ['key' => 'MEX', 'value' => 'MEX']
                ],
            ]);

            $service->update($dbtable,'avh_google_multipack','integer', [
                'label' => 'GM: Multipack',
                'supportText' => 'Verpackungseinheit, Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 112,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_custom_label_0','string', [
                'label' => 'GM: Custom Label 0',
                'supportText' => 'Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 113,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_custom_label_1','string', [
                'label' => 'GM: Custom Label 1',
                'supportText' => 'Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 114,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_custom_label_2','string', [
                'label' => 'GM: Custom Label 2',
                'supportText' => 'Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 115,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_custom_label_3','string', [
                'label' => 'GM: Custom Label 3',
                'supportText' => 'Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 116,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_custom_label_4','string', [
                'label' => 'GM: Custom Label 4',
                'supportText' => 'Optional für jeden Artikel',
                'displayInBackend' => true,
                'position' => 117,
                'custom' => true,
            ]);

            $service->update($dbtable,'avh_google_adult','combobox', [
                'label' => 'GM: nicht_Jugendfrei',
                'supportText' => 'Optional!',
                'displayInBackend' => true,
                'position' => 117,
                'custom' => true,
                'arrayStore' => [
                    ['key' => 'Wahr', 'value' => 'Wahr'],
                    ['key' => 'Falsch', 'value' => 'Falsch']
                ],
            ]);
        } //end foreach

        $builder = $em->createQueryBuilder();

        $builder->select([
            'taxonomie'
        ]);
        $builder->from(\AvhGoogleTaxonomie\Models\AvhTaxonomie::class, 'taxonomie');

        //Ausführen wenn Model leer
        if(count($builder->getQuery()->getArrayResult())<1) {
            //Befüllt das Model mit den Google Taxonomien
            $this->getTaxonomieFromFile();
        }
    }

    /***
     * @param UninstallContext $context
     * @throws \Exception
     */
    public function uninstall(UninstallContext $context)
    {
        $service = $this->container->get('shopware_attribute.crud_service');


        $service->delete('s_export_attributes', 'Avh_Merchant_Export');
        $service->delete('s_articles_attributes', 'avh_google_title');
        $service->delete('s_articles_attributes', 'avh_google_description');

        foreach (array('s_articles_attributes','s_categories_attributes') as $dbtable)
        {
            $service->delete($dbtable, 'avh_taxonomie');
            $service->delete($dbtable, 'avh_google_itemconditon');
            $service->delete($dbtable, 'avh_google_multipack');
            $service->delete($dbtable, 'avh_google_adult');
            $service->delete($dbtable, 'avh_google_energy_efficiency_class');
            $service->delete($dbtable, 'avh_google_age_group');
            $service->delete($dbtable, 'avh_google_gender');
            $service->delete($dbtable, 'avh_google_material');
            $service->delete($dbtable, 'avh_google_pattern');
            $service->delete($dbtable, 'avh_google_size');
            $service->delete($dbtable, 'avh_google_size_type');
            $service->delete($dbtable, 'avh_google_size_system');
            $service->delete($dbtable, 'avh_google_custom_label_0');
            $service->delete($dbtable, 'avh_google_custom_label_1');
            $service->delete($dbtable, 'avh_google_custom_label_2');
            $service->delete($dbtable, 'avh_google_custom_label_3');
            $service->delete($dbtable, 'avh_google_custom_label_4');
        }

        $database = new Database(
            $this->container->get('models')
        );

        if ($context->keepUserData()) {
            return;
        }

        $database->uninstall();
    }

    /***
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Export_ExportResult_Filter_Fixed' => 'onFilterExportResult',
            'Enlight_Controller_Action_PreDispatch_Frontend_Detail' => 'onPreDispatch',
        ];
    }

    /***
     * @param \Enlight_Event_EventArgs $args
     * @throws \Enlight_Exception
     */
    public function onPreDispatch(\Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();
        $controller->View()->addTemplateDir(__DIR__.'/Resources/views');
        $articleID = $request->getParam('sArticle');
        $controller->View()->assign('SchemaCondition' , $this->getAttributeFromArticle($articleID,'avh_google_itemconditon'));
    }

    /***
     * @param \Enlight_Event_EventArgs $args
     * @return mixed
     */
    public function onFilterExportResult(\Enlight_Event_EventArgs $args)
    {
        $products = $args->getReturn();
        /** This is the id of the feed being exported */
        $feedId = $args->get('feedId');
        $i = 0;
        if($this->getFeedIsActivated($feedId) == 1)
        {
        $em = Shopware()->Models()->getRepository('AvhGoogleTaxonomie\Models\AvhTaxonomie');
            foreach ($products as &$product) {
                $i++;
                $taxoid = $this->getArticleFirst($this->getTaxoFromArticle($product['articleID']), $this->getTaxoFromCategory($product['articleID']));
                if ($taxoid) {

                    //Name & Description ersetzten wenn vorhanden

                    if(strlen( $this->getAttributeFromArticle($product['articleID'], 'avh_google_title'))>3) {
                        $product['name'] = $this->getAttributeFromArticle($product['articleID'], 'avh_google_title');
                    }
                    if(strlen( $this->getAttributeFromArticle($product['articleID'], 'avh_google_description'))>3) {
                        $product['description_long'] = $this->getAttributeFromArticle($product['articleID'], 'avh_google_description');
                    }

                    //Deutschland - Gibt die lokalisierte Google Taxonomie aus
                    $product['avh_google_taxonomie'] = $em->find($taxoid)->getName();
                    //International - Gibt die Google Taxonomie ID aus
                    $product['avh_google_taxonomieid'] = $em->find($taxoid)->getGoogleid();
                    //ItemCondition //todo: prüfen ob die ItemeCondition bei Kategerorien im Template und im Feed herauskommt
                    $product['avh_google_itemconditon'] = str_replace(array('RefurbishedCondition', 'UsedCondition', 'NewCondition'), array('generalüberholt', 'gebraucht', 'neu'), $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_itemconditon'));
                    //avh_google_adult
                    $product['avh_google_adult'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_adult');
                    //TODO 0 darf nicht ausgegeben werden bei avh_google_multipack
                    if($this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_multipack')>0)
                    {
                        $product['avh_google_multipack'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_multipack');
                    } else {
                        $product['avh_google_multipack'] = '';
                    }
                    $product['avh_google_efficiency_class'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_energy_efficiency_class');
                    $product['avh_google_age_group'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_age_group');
                    $product['avh_google_color'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_color');
                    $product['avh_google_gender'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_gender');
                    $product['avh_google_material'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_material');
                    $product['avh_google_pattern'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_pattern');
                    $product['avh_google_size'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_size');
                    $product['avh_google_size_type'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_size_type');
                    $product['avh_google_size_system'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_size_system');
                    $product['avh_google_custom_label0'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_custom_label_0');
                    $product['avh_google_custom_label1'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_custom_label_1');
                    $product['avh_google_custom_label2'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_custom_label_2');
                    $product['avh_google_custom_label3'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_custom_label_3');
                    $product['avh_google_custom_label4'] = $this->getAttributeFromArticleAndCategory($product['articleID'], 'avh_google_custom_label_4');
                }
            }
        }
        return $products;
    }

    /***
     * @param $id
     * @return string
     *
     * * Ermittelt die Id des Taxnomie eintrages im Model \AvhGoogleTaxonomie\Models\AvhTaxonomie
     */
    public function getTaxoFromCategory($id)
    {
        $q = "SELECT ca.avh_taxonomie from s_articles_categories AS c
                LEFT JOIN s_categories_attributes as ca
                ON c.categoryID = ca.categoryID
                WHERE c.articleID = $id
                ";
        return Shopware()->Db()->fetchOne($q);
    }

    /***
     * @param $id
     * @return string
     *
     * Ermittelt die Id des Taxnomie eintrages im Model \AvhGoogleTaxonomie\Models\AvhTaxonomie
     */
    public function getTaxoFromArticle($id)
    {
        $q = "SELECT avh_taxonomie FROM s_articles_attributes WHERE articleID = $id";
        return Shopware()->Db()->fetchOne($q);
    }

    /***
     * @param $id
     * @return string
     */
    public function getFeedIsActivated($id)
    {
        $q = "SELECT avh_merchant_export FROM s_export_attributes WHERE exportID = $id";
        return Shopware()->Db()->fetchOne($q);
    }

    /***
     * @param $articleID
     * @param $attribute
     * @return string
     *
     * Holt den Attribute des Articles und der Categorie, dabei wird Kategorie First beachtet
     */
    public function getAttributeFromArticleAndCategory($articleID,$attribute)
    {
        $articleAttribute = '';
        $categorieAttribute = '';

        if(!empty($articleID)) {
            $q1 = "SELECT $attribute FROM s_articles_attributes WHERE articleID = $articleID";
            $this->avh_debug($q1,1);
            $articleAttribute = Shopware()->Db()->fetchOne($q1);

            $q2 = "SELECT ca.$attribute from s_articles_categories AS c
                LEFT JOIN s_categories_attributes as ca
                ON c.categoryID = ca.categoryID
                WHERE c.articleID = $articleID";
            $categorieAttribute = Shopware()->Db()->fetchOne($q2);
        }

        if(!empty($articleAttribute)) {
            return $articleAttribute;
        } else {
            return $categorieAttribute;
        }
    }

    public function getAttributeFromArticle($articleID,$attribute)
    {
        if(!empty($articleID)) {
            $q1 = "SELECT $attribute FROM s_articles_attributes WHERE articleID = $articleID";
            $this->avh_debug($q1,1);
            $articleAttribute = Shopware()->Db()->fetchOne($q1);
            return $articleAttribute;
        }
    }

    /***
     * @param $articleAttr
     * @param $categoryAttr
     * @return mixed
     *
     * Für Taxonomie
     */
    public function getArticleFirst($articleAttr, $categoryAttr)
    {
        if ($articleAttr < 1) {
            return $categoryAttr;
        } else {
            return $articleAttr;
        }

    }

    #public function extendExtJS(\Enlight_Event_EventArgs $arguments)
    #{
    #    /** @var \Enlight_View_Default $view */
    #    $view = $arguments->getSubject()->View();
    #    $view->addTemplateDir($this->getPath() . '/Resources/views/');
    #    $view->extendsTemplate('backend/swag_attribute/Shopware.attribute.Form.js');
    #}

    /***
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * Import Google Taxonomies in das Model
     */
    public function getTaxonomieFromFile()
    {
        $em = Shopware()->Models();

        $i = 0;
        $file = fopen(__DIR__ . "/GoogleTaxonomie.csv", "r");
        while (!feof($file)) {
            $i++;
            $erg[$i] = fgetcsv($file, 2000, ";");
            #$taxo[] = ['key' => $erg[$i][0], 'value' => $erg[$i][1]];
            if($erg[$i][1])
            {
                $unit = new \AvhGoogleTaxonomie\Models\AvhTaxonomie;
                $unit->setName($erg[$i][1]);
                $unit->setGoogleid($erg[$i][0]);
                $em->persist($unit);
                $em->flush($unit);
            }
        }
        fclose($file);

        #$this->avh_debug($taxo);

        return true;
    }

    /***
     * @param $log
     * @param $newfile
     */
    public function avh_debug($log, $newfile)
    {
        $filename = __NAMESPACE__ . ".log";
        ob_start();
        print_r($log);
        if ($newfile == 1) //Leert die Log Datei vor dem Schreiben
        {
            file_put_contents($filename, ob_get_contents());
        } else {
            file_put_contents($filename, file_get_contents($filename) . "\n" . ob_get_contents());
        }
        ob_end_clean();
    }
}