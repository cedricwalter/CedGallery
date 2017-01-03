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

class CedGalleryExifSorting
{

    public function sort($items)
    {
        return usort($items, array(&$this, 'compareExif'));
    }

    public function compareExif($firstItem, $secondItem)
    {
        $firstItemDate = $this->getFileUnixTimeStamp($firstItem);
        $secondItemDate = $this->getFileUnixTimeStamp($secondItem);

        if ($firstItemDate && $secondItemDate) {
            $firstItemDate = strtotime($firstItemDate);
            $secondItemDate = strtotime($secondItemDate);

            if ($firstItemDate == $secondItemDate) {
                return 0;
            } else if ($firstItemDate < $secondItemDate) {
                return -1;
            } else {
                return 1;
            }
        }

        return 0;
    }

    private function getFileUnixTimeStamp($file) {
        $exif_read_data = array();

        //Only jpg support exif
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (strcasecmp($ext, 'jpg') == 0) {
            $exif_read_data = exif_read_data($file);
        }

        if (is_array($exif_read_data) && array_key_exists('DateTimeOriginal', $exif_read_data)) {
            $unixTimeStamp = strtotime($exif_read_data['DateTimeOriginal']);
        } else {
            $unixTimeStamp = filemtime($file);
        }

        return $unixTimeStamp;
    }


}
