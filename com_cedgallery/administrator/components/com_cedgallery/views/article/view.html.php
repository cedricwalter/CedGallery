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

jimport('joomla.application.component.view');

class CedGalleryViewArticle extends JViewLegacy
{
    protected $form;
    protected $item;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {

        if ($this->getLayout() == 'cedgallery') {
            $eName = JFactory::getApplication()->input->get('e_name', null, 'string');
			$eName    = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);
            $document = JFactory::getDocument();
            $document->setTitle(JText::_('CedGallery'));
			$this->eName = &$eName;
            parent::display($tpl);
            return;
        }

        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->state	= $this->get('State');
        $this->canDo	= JHelperContent::getActions('com_content', 'article', $this->item->id);

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        if ($this->getLayout() == 'modal')
        {
            $this->form->setFieldAttribute('language', 'readonly', 'true');
            $this->form->setFieldAttribute('catid', 'readonly', 'true');
        }

        $this->addToolbar();
        parent::display($tpl);
    }



}
