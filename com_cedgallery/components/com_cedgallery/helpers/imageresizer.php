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

jimport('joomla.image.image');
jimport('joomla.filesystem.path');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * https://github.com/joomla/joomla-platform/blob/staging/docs/manual/en-US/chapters/packages/image.md
 */
class cedGalleryResizer
{

    /*
     *
     * http://automagical.rationalmind.net/2009/08/25/correct-photo-orientation-using-exif/
     *
     *
     */
    var $width = null;
    var $height = null;
    var $jpgQuality = null;
    var $pngQuality = null;
    var $scaleMethod = null;
    var $type = null;
    var $thumbnailsCaching = null;

    public function __construct($params, $cachePath)
    {
        $this->width = $params->get('thumbnailWidth', 230);
        $this->height = $params->get('thumbnailHeight', 154);
        $this->jpgQuality = $params->get('jpgquality', 154);
        $this->pngQuality = $params->get('pngquality', 9);
        $this->scaleMethod = $params->get('jimageResize', JImage::SCALE_FILL);
        $this->type = 2;
        $this->cachePath = $cachePath;
        $this->thumbnailsCaching = $params->get('thumbnails-caching', 1);
    }

    public function resize($originalFilePath, $path = JPATH_SITE)
    {
        $thumbnailFileName = pathinfo($originalFilePath, PATHINFO_FILENAME) . ".jpg";
        $thumbnailFileNamePath = JPath::clean(JPATH_SITE . "/" . $this->cachePath . "/" . $thumbnailFileName);

        $this->createDirectoryIfNotExist($thumbnailFileNamePath);

        //for performances
        if (JFile::exists($thumbnailFileNamePath) && $this->thumbnailsCaching) {
            return JUri::root() . $this->cachePath . "/" . $thumbnailFileName;
        }

        //https://github.com/joomla/joomla-framework-image
        try {
            $jImage = new JImage($originalFilePath);
            $resizeJImage = $jImage->resize($this->width, $this->height, true, intval($this->scaleMethod));

            $resizeJImage->toFile($thumbnailFileNamePath, $this->getType($this->type), $this->getOptions($this->type, $this->jpgQuality, $this->pngQuality));
        } catch (Exception $e) {
            $message = 'Can not resize image url \'' . $originalFilePath . '\' to be written in \'' . $thumbnailFileNamePath . '\' cause \'' . $e . '\'';
            error_log($message);
        } //finally { only php 5.5
        unset($jImage);
        //}

        return JUri::root() . $this->cachePath . "/" . $thumbnailFileName;
    }

    private function getOptions($type = 2, $jpgQuality = 85, $pngQuality = 9)
    {
        $options = array();
        switch ($type) {
            case 1:
                break;
            case 2:
                $options = array('quality' => $jpgQuality);
                break;
            case 3:
                $options = array('quality' => $pngQuality);
                break;
        }
        return $options;
    }

    private function getType($type = 2)
    {
        $extension = IMAGETYPE_JPEG;
        switch ($type) {
            case 1:
                $extension = IMAGETYPE_GIF;
                break;
            case 3:
                $extension = IMAGETYPE_PNG;
                break;
            case 2:
                $extension = IMAGETYPE_JPEG;
                break;
        }
        return $extension;
    }

    private function getExtension($type = 2)
    {
        $extension = ".jpg";
        switch ($type) {
            case 1:
                $extension = ".gif";
                break;
            case 3:
                $extension = ".png";
                break;
            case 2:
                $extension = ".jpg";
                break;
        }
        return $extension;
    }


    function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    /**
     * @param $thumbnailFileNamePath
     */
    private function createDirectoryIfNotExist($thumbnailFileNamePath)
    {
        $directory = dirname($thumbnailFileNamePath);
        if (!JFolder::exists($directory)) {
            JFolder::create($directory);
        }
    }

}
