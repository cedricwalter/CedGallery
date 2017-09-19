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

JLoader::import('joomla.filesystem.folder');

require_once(JPATH_SITE . '/components/com_cedgallery/helpers/exif.php');
require_once(JPATH_SITE . '/components/com_cedgallery/helpers/iptc.php');
require_once(JPATH_SITE . '/components/com_cedgallery/sorting/sortingfactory.php');
require_once(JPATH_SITE . '/components/com_cedgallery/helpers/imageresizer.php');


class cedGalleryModel
{
    const fileExtensions = '\.png$|\.gif$|\.jpg$|\.jpeg$|\.PNG$|\.GIF$|\.JPG$|\.JPEG$';

    var $sortingClass = null;
    var $scaler = null;

    function __construct($params, $cachePath)
    {
        $this->scaler = new cedGalleryResizer($params, $cachePath);
        $folder = $params->get('folder');
        $this->scanFolder = JPATH_SITE . "/images/" . $folder . "/";
        $this->defaultThumbnail = JUri::root() . $params->get('default-thumbnail');
        $this->sortingClass = CedGallerySortingFactory::getSorting($params->get('sorting', 'exif'));
    }

    public function getModel(&$params)
    {
        $folder = $params->get('folder');

        return $this->getModelForFolder($folder, $params);
    }

    public function getModelForFolder($folder, &$params)
    {
        $scanFolder = JPATH_SITE . "/images/" . $folder;
        $recursive = $params->get('recurse', true);
        $fileList = JFolder::files($scanFolder, self::fileExtensions, $recursive, true);

        $this->sortingClass->sort($fileList);

        $base_folder = rtrim(JURI::base(), '/');

        $models = array();
        try {
            foreach ($fileList as $file) {
                $model = new stdClass();

                $relativeFilePath = str_replace(JPATH_SITE, "", $file);
                $relativeFilePath = str_replace('\\', "/", $relativeFilePath);

                $model->originalFileRoot = $base_folder . "/$relativeFilePath";
                $model->thumbnailsRoot = $this->scaler->resize($file);
                $model->originalFilePath = JPath::clean($file);

                $iptc = new cedGalleryIPTC($file);
                $exif = new cedGalleryExif($file);

                $caption = $iptc->getCaption();
                if (!isset($caption)) {
                    //$caption = $exif->getXPComment();
                }
                $model->caption = isset($caption) ? $caption : "";

                $title = $iptc->getTitle();
                if (!isset($title)) {
                    $title = $exif->getTitle();
                }
                if (!isset($title)) {
                    $title = basename($file);
                }
                $model->title = $title;

                $model->album = "todo";

                $models[] = $model;
            }
        } catch (Exception $e) {
            $e = null;
        }

        return $models;
    }

    private function cleanUrl($path, $ds = "/")
    {
        if (!is_string($path) && !empty($path)) {
            throw new UnexpectedValueException('JPath::clean: $path is not a string.');
        }

        $path = trim($path);

        if (empty($path)) {
            $path = JPATH_SITE;
        }
        // Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
        // If dealing with a UNC path don't forget to prepend the path with a backslash.
        elseif (($ds == '\\') && ($path[0] == '\\') && ($path[1] == '\\')) {
            $path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
        } else {
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $path;
    }


}