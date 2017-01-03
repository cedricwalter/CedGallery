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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$script = 'function insertCedGallery() {' . "\n\t";

$script .= 'var folder = document.getElementById("folder").value;' . "\n\t";

$script .= 'var tag = "{cedgallery folder="+folder+"}";' . "\n\t";
$script .= 'window.parent.jInsertEditorText(tag, \'' . $this->eName . '\');' . "\n\t";
$script .= 'window.parent.SqueezeBox.close();' . "\n\t";
$script .= 'return false;' . "\n";
$script .= '}' . "\n";

JFactory::getDocument()->addScriptDeclaration($script);
?>
<form>
    <h4>Enter folder relative to /images/</h4>
    <table>
        <tr>
            <td class="key" align="right">
                <label for="folder">
                </label>
            </td>
            <td>
                /images/<input type="text" id="folder" name="folder" size="50" value="" placeholder=""/>
            </td>
        </tr>
    </table>
</form>
<button onclick="insertCedGallery();"><?php echo JText::_('Insert CedGallery'); ?></button>
