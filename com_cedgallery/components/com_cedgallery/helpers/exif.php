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

class cedGalleryExif
{

    var $exif_data = null;

    function __construct($file)
    {
        //ini_set('exif.encode_unicode', 'UTF-8');
//        ini_set('exif.decode_unicode_intel', 'UCS-2LE');
//      ini_set('exif.encode_unicode','ISO-8859-1');
//        ini_set('exif.encode_jis','ISO-8859-1');
//        ini_set('exif.decode_jis_intel','ISO-8859-1');

        //Only jpg support exif
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (strcasecmp($ext, 'jpg') == 0) {
            $this->exif_data = exif_read_data($file, 0, true);
        }
    }

    public function getTitle()
    {
        return $this->getFieldFromGroupIfExist('IFD0', 'ImageDescription');
    }

    public function getFileName()
    {
        return $this->getFieldFromGroupIfExist('FILE', 'FileName');
    }

    public function getXPComment()
    {
        $xpcomment = utf8_decode($this->getFieldFromGroupIfExist('WINXP', 'Comments'));
        return $xpcomment;
    }

    public function getCaption()
    {
        return $this->getFieldFromGroupIfExist('IFD0', 'ImageDescription');
    }

    public function getFieldFromGroupIfExist($group, $key)
    {
        if (isset($this->exif_data) && is_array($this->exif_data)) {
            if (@array_key_exists($key, $this->exif_data[$group])) {
                return $this->exif_data[$group][$key];
            }
        }

        return null;
    }


}