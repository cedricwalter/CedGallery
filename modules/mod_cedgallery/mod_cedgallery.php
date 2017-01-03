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

require_once(JPATH_SITE . '/components/com_cedgallery/nano/renderer.php');


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$backLink = $params->get('backlink', 1);

$cachePath = "cache/CedGalleryModule-" . str_replace(" ", "-",JFile::makeSafe($module->title));

//require_once(JPATH_SITE . '/components/com_cedgallery/nano/model.php');
//$model = new cedGalleryModel($params , $cachePath);
//$models = $model->getModel($params);

require_once(JPATH_SITE . '/components/com_cedgallery/nano/recursive.php');
$model = new cedGalleryModelRecursive($params , $cachePath);
$models = $model->getModel();

$gallery = new cedGalleryNano();
$html = $gallery->render($params, $models);

$module->content = $html;

require JModuleHelper::getLayoutPath('mod_cedgallery', $params->get('layout', 'default'));
