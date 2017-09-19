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
 * @id ${licenseId}
 */

// Don't allow direct access to the module.
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/params.php');
require_once(dirname(__FILE__) . '/library.php');

class cedGalleryNano
{

    public function render(&$params, $models)
    {
        $this->addLibrary($params);

        $useAlbum = $params->get('folder-album', 1);

        $uuid = uniqid();
        $albumName = "";
        $albumId = "";
        $id = "";

        $html = array();
        $html[] = "<div id=\"cedgallery-$uuid\">";
        $html[] = "<!-- Copyright (C) 2013-2017 galaxiis.com All rights reserved. -->";

        foreach ($models as $model) {
            if ($useAlbum) {
                $albumName = $model->type != "" ? "data-ngkind=\"album\"" : "";
                $albumId = "data-ngAlbumID=\"$model->albumId\"";
                $id = "data-ngID=\"$model->id\"";
            }
            $html[] = "<a $id $albumId $albumName href=\"$model->originalFileRoot\" data-ngthumb=\"$model->thumbnailsRoot\" data-ngdesc=\"$model->caption\">$model->title</a>\n";
        }
        $html[] = "</div>";

        $cedGalleryParams = new cedGalleryParams();

        $document = JFactory::getDocument();
        $document->addScriptDeclaration($cedGalleryParams->getScriptDeclaration($uuid, $cedGalleryParams->getGalleryParameters($params)));

        $implode = implode("\n", $html);
        return $implode;
    }

    /**
     * @param $params
     * @return JDocument
     */
    private function addLibrary(&$params)
    {
        JHtml::_('jquery.framework');

        $theme = $params->get('theme', 'default');
        $type = $params->get('library', 'cdn');

        $cedGalleryLibrary =  new cedGalleryLibrary($theme, $type, JUri::base());

        $document = JFactory::getDocument();

        foreach($cedGalleryLibrary->getScript() as $script) {
            $document->addScript($script);
        }

        foreach($cedGalleryLibrary->getStyleSheet() as $style) {
            $document->addStyleSheet($style);
        }

    }


}