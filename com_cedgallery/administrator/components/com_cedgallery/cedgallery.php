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

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . '/controller.php');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . '/media/com_cedgallery/css/smugmug.css');

$controller = JControllerLegacy::getInstance('CedGallery');
$task = JFactory::getApplication()->input->get('task', 'default', 'string');
$controller->execute($task);
$controller->redirect();
