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

class plgButtonCedGallery extends JPlugin
{

    protected $autoloadLanguage = true;

    public function onDisplay($name, $asset, $author)
    {
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $extension = $app->input->get('option');

        if ($asset == '')
        {
            $asset = $extension;
        }

        if (	$user->authorise('core.edit', $asset)
            ||	$user->authorise('core.create', $asset)
            ||	(count($user->getAuthorisedCategories($asset, 'core.create')) > 0)
            ||	($user->authorise('core.edit.own', $asset) && $author == $user->id)
            ||	(count($user->getAuthorisedCategories($extension, 'core.edit')) > 0)
            ||	(count($user->getAuthorisedCategories($extension, 'core.edit.own')) > 0 && $author == $user->id))
        {

            $link = 'index.php?option=com_cedgallery&amp;view=article&amp;layout=cedgallery&amp;template=component&amp;e_name=' . $name . '&amp;asset=' . $asset . '&amp;author=' . $author;
            JHtml::_('behavior.modal');
            $button = new JObject;
            $button->modal = true;
            $button->class = 'btn';
            $button->link = $link;
            $button->text = JText::_('CedGallery');
            $button->name = 'picture';
            $button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

            return $button;
        }
        else
        {
            return false;
        }
    }

}