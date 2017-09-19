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


class cedGalleryParams
{

    const KEY_PREFIX = "v_";
    const NUMBER_MILLISECOND_PER_SECOND = 1000;
    const DEFAULT_SLIDE_SHOW_DELAY = 2000;
    const TRUE = 'true';
    const FALSE = 'false';

    public function getGalleryParameters(&$params)
    {
        $array = array();

        $allParameters = $params->toArray();

        $layout = $params->get('advancedLayout');
        if ($layout == "justifiedLayout") {
            $array[] = $this->addKeyValue("thumbnailWidth", "auto");
            $array[] = $this->addKeyValue("thumbnailHeight", $params->get('thumbnailHeight'));
        } else if ($layout == "cascadingGridLayout") {
            $array[] = $this->addKeyValue("thumbnailWidth", $params->get('thumbnailWidth'));
            $array[] = $this->addKeyValue("thumbnailHeight", "auto");
        } else {
            $array[] = $this->addKeyValue("thumbnailWidth", $params->get('thumbnailWidth'));
            $array[] = $this->addKeyValue("thumbnailHeight", $params->get('thumbnailHeight'));
        }

        foreach ($allParameters as $key => $value) {
            if ($this->startsWith($key, "v_viewerToolbar")) {
                continue;
            }

            if ($this->startsWith($key, self::KEY_PREFIX)) {
                $filteredKey = str_replace(self::KEY_PREFIX, "", $key);
                $filteredKey = str_replace("_", ".", $filteredKey);

                // keys with conversion from ms to seconds
                if ($filteredKey == 'slideshowDelay') {
                    $value = intval($value) * self::NUMBER_MILLISECOND_PER_SECOND;
                    if ($value < self::DEFAULT_SLIDE_SHOW_DELAY) {
                        $value = self::DEFAULT_SLIDE_SHOW_DELAY;
                    }
                }


                $array = $this->addKeyValueIntoArray($value, $filteredKey, $array);
            }
        }

        $viewerToolbar = array();
        foreach ($allParameters as $key => $value) {
            if ($this->startsWith($key, "v_viewerToolbar")) {

                $filteredKey = str_replace(self::KEY_PREFIX, "", $key);
                $filteredKey = str_replace("_", ".", $filteredKey);

                $viewerToolbar  = $this->addKeyValueIntoArray($value, $filteredKey, $viewerToolbar);
            }
        }
        if (sizeof($viewerToolbar) > 1) {
            //$array[] = "var viewerToolbar = {" . implode(",\n ", $viewerToolbar) . "};";
        }

        $array[] = "i18n : {'thumbnailImageDescription':'" . JText::_('MOD_CEDGALLERY_I18_VIEW_PHOTO') . "', 'thumbnailAlbumDescription':'" . JText::_('MOD_CEDGALLERY_I18_OPEN_ALBUM') . "'}";
        $array[] = "thumbnailLabel : {display:true,position:'overImageOnMiddle'}";

        $implode = implode(",\n ", $array);

        return $implode;
    }

    private function addKeyValue($key, $value)
    {
        if ($this->isInteger($value)) {
            return "$key: $value";
        }

        return "$key: '$value'";
    }

    // we can no use is_int() with string "123" would return false
    private function isInteger($input)
    {
        return (ctype_digit(strval($input)));
    }

    private function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    public function getScriptDeclaration($uuid, $parameters)
    {
        return  'jQuery(window).on("load",  function() {
                      jQuery("#cedgallery-' . $uuid . '").nanoGallery({' . $parameters . '});
                    });';

    }

    /**
     * @param $value
     * @param $filteredKey
     * @param $array
     * @return array
     */
    private function addKeyValueIntoArray($value, $filteredKey, $array)
    {
        if (isset($value)) {
            if (is_array($value)) {
                $array[] = "$filteredKey: '" . implode(",", $value) . "'";
                return $array;
            } else if ($value == self::TRUE) {
                $array[] = "$filteredKey: true";
                return $array;
            } else if ($value == self::FALSE) {
                $array[] = "$filteredKey: false";
                return $array;
            } else {
                $array[] = $this->addKeyValue($filteredKey, $value);
                return $array;
            }
        }
        return $array;
    }

}