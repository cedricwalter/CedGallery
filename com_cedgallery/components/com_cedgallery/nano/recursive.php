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

JLoader::import('joomla.filesystem.folder');

require_once(JPATH_SITE . '/components/com_cedgallery/helpers/exif.php');
require_once(JPATH_SITE . '/components/com_cedgallery/helpers/iptc.php');
require_once(JPATH_SITE . '/components/com_cedgallery/sorting/sortingfactory.php');
require_once(JPATH_SITE . '/components/com_cedgallery/helpers/imageresizer.php');

class cedGalleryModelRecursive
{
    const fileExtensions = '\.png$|\.gif$|\.jpg$|\.jpeg$|\.PNG$|\.GIF$|\.JPG$|\.JPEG$';

    var $resize = 0;
    var $scanFolder;
    var $defaultThumbnail;

    function __construct($params, $cachePath)
    {
        $this->resize = new cedGalleryResizer($params, $cachePath);
        $folder = $params->get('folder');
        $this->scanFolder = JPATH_SITE . "/images/" . $folder."/" ;
        $this->defaultThumbnail =  JUri::root().$params->get('default-thumbnail');
    }

    private function fillModel($nodes, &$models)
    {
        foreach ($nodes->child as $node) {
            $model = self::createEmptyModel();
            $model->id = $node->id;

            if ($node->isDir) {
                $model->type = "album";
                $model->title = basename($node->fileName);
                $model->albumId = $nodes->albumId;
                $model->thumbnailsRoot = $this->defaultThumbnail;

                $models[] = $model;

                $this->fillModel($node, $models);
            } else {
                $relativeFilePath = str_replace(JPATH_SITE, "", $node->fileName);

                $model->originalFileRoot = JUri::root() . $this->cleanUrl($relativeFilePath);

                $clean = JPath::clean($node->fileName);

                $model->thumbnailsRoot = $this->resize->resize($clean);
                $model->originalFilePath = JPath::clean($node->fileName);
                $model->albumId = $nodes->id;

                $internationalPressTelecommunicationsCouncil = new cedGalleryIPTC($node->fileName);
                $title = $internationalPressTelecommunicationsCouncil->getTitle();
                if (!isset($title)) {
                    $exchangeableImageFileFormat = new cedGalleryExif($node->fileName);
                    $title = $exchangeableImageFileFormat->getTitle();
                }
                if (!isset($title)) {
                    $title = basename($node->fileName);
                }
                $model->title = $title;

                $caption = $internationalPressTelecommunicationsCouncil->getCaption();
                if (!isset($caption)) {
                    //todo can not read it is little endian iso encoded and reverted!
                    //$caption = $exif->getXPComment();
                }
                $model->caption = isset($caption) ? $caption : "";

                $models[] = $model;
            }
        }
    }

    public function scanRecursively($directory, &$id = 0, &$albumId = 0)
    {
        $contents = new stdClass();

        $contents->isDir = true;
        $contents->fileName = $directory;
        $contents->child = array();
        $contents->id = $id;
        $contents->albumId = $albumId;

        foreach (scandir($directory, SCANDIR_SORT_ASCENDING) as $node) {
            if ($node == '.' || $node == '..') continue;
            $filename = $directory. '/'. $node;
            $id++;

            $entry = new stdClass();
            $entry->dir = $directory;
            $entry->name = $node;
            $entry->id = $id;

            if (is_dir($filename)) {

                $entry->fileName = null;
                $entry->dirName = dirname($filename);
                $entry->isDir = true;
                $entry->isFile = false;
                $entry->albumId = $albumId;

                $contents->child[$node] = $this->scanRecursively($filename, $id, $albumId);
                $albumId = $albumId+1;
            } else {
                $entry->fileName = $filename;
                $entry->dirName = null;
                $entry->isDir = false;
                $entry->isFile = true;
                $entry->albumId = $albumId;

                $contents->child[] = $entry;
            }
        }

        return $contents;
    }

    public function getModel()
    {
        $models = array();

        $nodes = $this->scanRecursively($this->scanFolder);

        $this->fillModel($nodes, $models);

        $this->updateAlbumCover($models);

        return $models;
    }

    public function createEmptyModel()
    {
        $model = new stdClass();

        $model->type = "";
        $model->title = "";
        $model->originalFileRoot = "";
        $model->thumbnailsRoot = $this->defaultThumbnail;
        $model->caption = "";
        $model->id = "";
        $model->albumId = "";
        $model->originalFilePath = "";

        return $model;
    }

    private function updateAlbumCover($models)
    {
        foreach($models as $model) {
            if ($model->type == 'album') {
                $albumId = $model->id;
                $albumName = $model->title;

                $thumbnail = $this->getThumbnailsOfAlbumHavingSameTitleEndingWithJpeg($models, $albumId, $albumName);

                if (!isset($thumbnail)) {
                    $thumbnail = $this->getFirstThumbnailsOfAlbum($models, $albumId);
                }

                $model->thumbnailsRoot = $thumbnail;
            }
        }
    }

    public function cleanUrl($path, $ds = "/")
    {
        if (!is_string($path) && !empty($path))
        {
            throw new UnexpectedValueException('JPath::clean: $path is not a string.');
        }

        $path = trim($path);

        if (empty($path))
        {
            $path = JPATH_SITE;
        }
        // Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
        // If dealing with a UNC path don't forget to prepend the path with a backslash.
        elseif (($ds == '\\') && ($path[0] == '\\' ) && ( $path[1] == '\\' ))
        {
            $path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
        }
        else
        {
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $path;
    }

    /**
     * @param $models
     * @param $albumId
     * @param $model
     */
    private function getFirstThumbnailsOfAlbum($models, $albumId)
    {
        foreach ($models as $subModel) {
            $notAnAlbum = $subModel->type == '';
            $subModelIsChildOfAlbum = $subModel->albumId == $albumId;

            if ($subModelIsChildOfAlbum && $notAnAlbum) {
                return $subModel->thumbnailsRoot;
            }
        }
    }

    private function getThumbnailsOfAlbumHavingSameTitleEndingWithJpeg($models, $albumId, $albumName)
    {
        foreach ($models as $subModel) {
            $notAnAlbum = $subModel->type == '';
            $subModelIsChildOfAlbum = $subModel->albumId == $albumId;

            if ($subModelIsChildOfAlbum && $notAnAlbum && $subModel->title == $albumName.".jpg") {
                return $subModel->thumbnailsRoot;
            }
        }

        return null;
    }

}