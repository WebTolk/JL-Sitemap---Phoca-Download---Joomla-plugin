<?php
/**
 * @package    JLSitemap - Phoca Download Plugin
 * @version    1.0.0
 * @author     Sergey Tolkachyov - web-tolk.ru
 * @copyright  Copyright (c) 2024 Sergey Tolkachyov. All rights reserved.
 * @license    GNU General Public License v3.0
 * @link       https://web-tolk.ru/dev/joomla-plugins/jlsitemap-phoca-download-joomla-plugin
 */
namespace Joomla\Plugin\Jlsitemap\Phocadownload\Fields;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\SpacerField;
use Joomla\CMS\Language\Text;

class ComponentparamsField extends SpacerField
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
        $componentParams = ComponentHelper::getParams('com_phocadownload');
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