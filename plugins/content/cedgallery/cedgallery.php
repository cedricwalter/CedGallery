<?php
/**
 * @package     CedGallery
 * @subpackage  com_cedgallery
 *
 * @copyright   Copyright (C) 2013-2017 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * https://dev.twitter.com/docs/cards
 * https://dev.twitter.com/docs/cards/validation/validator
 * https://dev.twitter.com/docs/cards/types/summary-card
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

require_once(JPATH_SITE . '/components/com_cedgallery/nano/renderer.php');
require_once(JPATH_SITE . '/components/com_cedgallery/nano/model.php');
require_once(dirname(__FILE__) . '/parser.php');

class plgContentCedGallery extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * @param $context
     * @param $row
     * @param $params
     * @param int $page
     * @return bool
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        //Do not run in admin area and non HTML  (rss, json, error)
        $app = JFactory::getApplication();
        if ($app->isClient('administrator') || JFactory::getDocument()->getType() !== 'html')
        {
            return;
        }

        $canProceed = $context == 'com_content.article';
        if (!$canProceed) {
            return;
        }

        $parser = new plgContentCedGalleryParser();

        //simple performance check to determine whether bot should process further
        if (!$parser->active($row->text)) {
            return;
        }

        $row->text = $this->replaceText($row->text, $params);

        return;
    }

    private function replaceText($text, &$params)
    {
        $parser = new plgContentCedGalleryParser();
        $nano = new cedGalleryNano();

        $isSSLConnection = JFactory::getApplication()->isSSLConnection();
        $models = $parser->parse($text, $isSSLConnection);
        if ($models) {
            $galleryModel = new cedGalleryModel($params , "cache/CedGalleryPlugin");
            foreach ($models as $model) {

                if (isset($model->paginationMaxItemsPerPage)) {
                    //$params->set('paginationMaxItemsPerPage', )
                }

                $models = $galleryModel->getModelForFolder($model->folder, $params);
                $html = $nano->render($params, $models);

                $text = str_replace($model->match, $html, $text);
            }
        }

        return $text;
    }



}
