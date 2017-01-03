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

class CedGallerySortingFactory
{

    public function __construct($config = array())
    {
    }

    /**
     * @param $type
     * @return mixed
     * @throws Exception
     */
    public static function getSorting($type)
    {
        $filename = dirname(__FILE__) . '/' . strtolower($type) . '.php';
        if (include_once($filename)) {
            $className = 'CedGallery' . $type . 'Sorting';
            return new $className;
        } else {
            throw new Exception("rendering $type not found");
        }
    }

}
