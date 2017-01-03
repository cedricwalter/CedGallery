<?php
/**
 * @package     CedGallery
 * @subpackage  com_cedgallery
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id ${licenseId}
 */

// Don't allow direct access to the module.
defined('_JEXEC') or die('Restricted access');


class cedGalleryLibrary
{

    const GALLERY_VERSION = "5.3.0";

    var $styleSheet = array();

    var $script = array();

    function __construct($theme, $type, $base)
    {
        if ($type == 'local') {
            $this->script[] =  $base."media/com_cedgallery/js/jquery.nanogallery.min.js?v=1.4.1";
            if ($theme == 'default') {
                $this->styleSheet[] =  $base."media/com_cedgallery/css/nanogallery.min.css?v=1.4.1";
            } else {
                $this->styleSheet[] =  $base."media/com_cedgallery/css/themes/$theme/nanogallery_$theme.min.css?v=1.4.1";
            }
        } else {
            $this->script[] =  "//cdnjs.cloudflare.com/ajax/libs/nanogallery/". self::GALLERY_VERSION ."/jquery.nanogallery.min.js";
            if ($theme == 'default') {
                $this->styleSheet[] =  "//cdnjs.cloudflare.com/ajax/libs/nanogallery/". self::GALLERY_VERSION ."/css/nanogallery.min.css";
            } else {
                $this->styleSheet[] =  "//cdnjs.cloudflare.com/ajax/libs/nanogallery/". self::GALLERY_VERSION ."/css/themes/$theme/nanogallery_$theme.min.css";
            }
        }
    }

    /**
     * @return array
     */
    public function getStyleSheet()
    {
        return $this->styleSheet;
    }

    /**
     * @return array
     */
    public function getScript()
    {
        return $this->script;
    }

}