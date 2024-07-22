<?php
/**
 * @package     WebTolk plugin info field
 * @version     1.0.0
 * @Author 		Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2020 Sergey Tolkachyov
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use \Joomla\CMS\Factory;
use Joomla\Registry\Registry;
FormHelper::loadFieldClass('spacer');

class JFormFieldComponentparams extends JFormFieldSpacer
{

	protected $type = 'componentparams';

	/**
	 * Method to get the field input markup for a spacer.
	 * The spacer does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.7.0
	 */
	protected function getInput()
	{
        $componentParams = JComponentHelper::getParams('com_phocadownload');
        $display_file_view = $componentParams->get('display_file_view','0');
        $files_in_sitemap = 0;
        $css_class = 'danger';
        if($display_file_view == 1){
            $files_in_sitemap = 1;
            $css_class = 'success';
        }
        return $html = '
        </div>
		<div class="alert alert-info">
    		'.Text::sprintf('PLG_PHOCADOWNLOAD_COMPONENT_PARAM_DISPLAY_FILE_VIEW',Text::_('PLG_PHOCADOWNLOAD_COMPONENT_PARAM_DISPLAY_FILE_VIEW_'.$display_file_view)).'
		</div>
		<div class="alert alert-'.$css_class.'">
		'.Text::sprintf('PLG_PHOCADOWNLOAD_FILES_IN_SITEMAP',Text::_('PLG_PHOCADOWNLOAD_FILES_IN_SITEMAP_'.$files_in_sitemap)).'
        </div>
		<div>
	';
	}

	/**
	 * @return  string  The field label markup.
	 *
	 * @since   1.7.0
	 */
	protected function getLabel()
	{
       return ' ';

	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   1.7.0
	 */
	protected function getTitle()
	{
		return $this->getLabel();
	}

}
?>