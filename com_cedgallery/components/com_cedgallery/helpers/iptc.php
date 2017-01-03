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

class cedGalleryIPTC
{

    var $iptc = null;

    static $iptcHeaderArray = array
    (
        '2#005' => 'DocumentTitle',
        '2#010' => 'Urgency',
        '2#015' => 'Category',
        '2#020' => 'Subcategories',
        '2#040' => 'SpecialInstructions',
        '2#055' => 'CreationDate',
        '2#080' => 'AuthorByline',
        '2#085' => 'AuthorTitle',
        '2#090' => 'City',
        '2#095' => 'State',
        '2#101' => 'Country',
        '2#103' => 'OTR',
        '2#105' => 'Headline',
        '2#110' => 'Source',
        '2#115' => 'PhotoSource',
        '2#116' => 'Copyright',
        '2#120' => 'Caption',
        '2#122' => 'CaptionWriter'
    );

    function __construct($file)
    {
        getimagesize($file, $info);
        if (isset($info['APP13'])) {
            $this->iptc = iptcparse($info['APP13']);
        }
    }

    public function getCaption()
    {
        if (is_array($this->iptc) && array_key_exists("2#120", $this->iptc)) {
            return $this->iptc['2#120'][0];
        }

        return null;
    }

    public function getTitle()
    {
        if (is_array($this->iptc) && array_key_exists("2#105", $this->iptc)) {
            return $this->iptc['2#105'][0];
        }

        return null;
    }

} 