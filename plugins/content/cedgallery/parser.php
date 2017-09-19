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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgContentCedGalleryParser
{
    const PATTERN = "/{cedgallery\s+(.*?)}/i";
    const START = '{cedgallery';

    public function active($text) {
        if (strpos($text, self::START) === false) {
            return false;
        }

        return true;
    }

    public function parse($text)
    {
        $models = array();

        preg_match_all(self::PATTERN, $text, $matches, PREG_SET_ORDER);

        // plugin only processes if there are any instances of the plugin in the text
        if ($matches) {
            foreach ($matches as $match) {
                $inline_params = $match[1];
                $result = array();
                $pairs = explode(' ', trim($inline_params));
                foreach ($pairs as $pair) {
                    $pos = strpos($pair, "=");
                    $key = substr($pair, 0, $pos);
                    $value = substr($pair, $pos + 1);
                    $result[$key] = $value;
                }

                $folder = null;
                if (isset($result['folder'])) {
                    $folder = trim($result['folder']);
                }

                $maxItemsPerLine = null;
                if (isset($result['maxItemsPerLine'])) {
                    $maxItemsPerLine = trim($result['maxItemsPerLine']);
                }

                $paginationMaxItemsPerPage = null;
                if (isset($result['paginationMaxItemsPerPage'])) {
                    $paginationMaxItemsPerPage = trim($result['paginationMaxItemsPerPage']);
                }

                $paginationMaxLinesPerPage = null;
                if (isset($result['paginationMaxLinesPerPage'])) {
                    $paginationMaxLinesPerPage = trim($result['paginationMaxLinesPerPage']);
                }

/*
                <field name="maxItemsPerLine" type="integer" default="5" label="MaxItems / Line"
                       description="Maximum number of thumbnails per line. Ignored when thumbnailWidth='auto'. Default is 5."
                       first="1"
                       last="20"
                       step="1"/>
                <field name="paginationMaxItemsPerPage" type="integer" default="20"
                       label="Pagination Max Items / Page"
                       description="Maximum number of thumbnails per page (pagination) 0= pagination is disabled. Ignored when thumbnailWidth='auto' or thumbnailHeight='auto'."
                       first="1"
                       last="50"
                       step="1"/>
                <field name="paginationMaxLinesPerPage" type="integer" default="5"
                       label="Pagination Max Line / Page"
                       description="Maximum number of thumbnail lines per page (pagination) 0= pagination is disabled. Ignored when thumbnailWidth='auto' or thumbnailHeight='auto'."
                       first="1"
                       last="20"
                       step="1"/>
*/


                $model = new stdClass();
                $model->folder = $folder;
                $model->maxItemsPerLine = $maxItemsPerLine;
                $model->paginationMaxItemsPerPage = $paginationMaxItemsPerPage;
                $model->paginationMaxLinesPerPage = $paginationMaxLinesPerPage;
                $model->match = $match[0];

                $models[] = $model;
            }
        }

        return $models;
    }

    function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
}
