<?php
/**
 * @package    JLSitemap - Phoca Download Plugin
 * @version    1.0.0
 * @author     Sergey Tolkachyov - web-tolk.ru
 * @copyright  Copyright (c) 2024 Sergey Tolkachyov. All rights reserved.
 * @license    GNU General Public License v3.0
 * @link       https://web-tolk.ru/
 */
namespace Joomla\Plugin\Jlsitemap\Phocadownload\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

class Phocadownload extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     *
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   4.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onGetUrls' => 'onGetUrls',
        ];
    }

    /**
     * Method to get urls array
     *
     * @param Event $event
     *
     * @return void Urls array with attributes
     *
     * @since  0.9.0
     */
    public function onGetUrls(Event $event): void
    {
        /**
         * @param array $urls Urls array
         * @param Registry $config Component config
         */
        [$urls, $config] = $event->getArguments();
        $componentParams = ComponentHelper::getParams('com_phocadownload');
        $debug_mode = $this->getApplication()->getInput()->get('debug');
        $categoryExcludeStates = [
            0 => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_CATEGORY_UNPUBLISH'),
            -2 => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_CATEGORY_TRASH'),
        ];

        $filesExcludeStates = [
            0 => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE_UNPUBLISH'),
            -2 => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE_TRASH'),
            2 => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE_ARCHIVE')
        ];

        $multilanguage = $config->get('multilanguage');


        $db = $this->getDatabase();
        /**
         * Categories
         */
        if ($this->params->get('categories_enable', 0) == 1) {

            $query = $db->getQuery(true);
            $query->select('*')
                ->from('#__phocadownload_categories');

            if(!$debug_mode)
            {
                $query->where($db->quoteName('published') .' = '. $db->quote('1'));
            }

            $query->order('ordering');

            $db->setQuery($query);
            $rows = $db->loadObjectList();

            $nullDate = $db->getNullDate();
            $changefreq = $this->params->get('categories_changefreq', $config->get('changefreq', 'weekly'));
            $priority = $this->params->get('categories_priority', $config->get('priority', '0.5'));

            // Add categories to arrays
            $categories = [];
            $alternates = [];

            \JLoader::register('PhocaDownloadRoute', JPATH_ADMINISTRATOR . '/components/com_phocadownload/libraries/phocadownload/path/route.php');

            foreach ($rows as $row) {
                // Prepare loc attribute

                $loc = \PhocaDownloadRoute::getCategoryRoute($row->id . ':' . $row->alias);

                // Prepare exclude attribute
                $metadata = new Registry($row->metadata);
                $exclude = [];
                if (preg_match('/noindex/', $metadata->get('robots', $config->get('siteRobots')))) {
                    $exclude[] = array('type' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_CATEGORY'),
                        'msg' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_CATEGORY_ROBOTS'));
                }

                if (isset($categoryExcludeStates[$row->published])) {
                    $exclude[] = array('type' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_CATEGORY'),
                        'msg' => $categoryExcludeStates[$row->published]);
                }

                if (!in_array($row->access, $config->get('guestAccess', []))) {
                    $exclude[] = array('type' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_CATEGORY'),
                        'msg' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_CATEGORY_ACCESS'));
                }

                // Prepare lastmod attribute
                $lastmod = (!empty($row->date) && $row->date != $nullDate) ? $row->date : false;

                // Prepare category object
                $category = new \stdClass();
                $category->type = Text::_('PLG_PHOCADOWNLOAD_TYPES_CATEGORY');
                $category->title = $row->title;
                $category->loc = $loc;
                $category->changefreq = $changefreq;
                $category->priority = $priority;
                $category->lastmod = $lastmod;
                $category->exclude = (!empty($exclude)) ? $exclude : false;
                $category->alternates = ($multilanguage && !empty($row->association)) ? $row->association : false;

                // Add category to array
                $categories[] = $category;

                // Add category to alternates array
                if ($multilanguage && !empty($row->association) && empty($exclude)) {
                    if (!isset($alternates[$row->association])) {
                        $alternates[$row->association] = [];
                    }

                    $alternates[$row->association][$row->language] = $loc;
                }
            }

            // Add alternates to categories
            if (!empty($alternates)) {
                foreach ($categories as &$category) {
                    $category->alternates = ($category->alternates) ? $alternates[$category->alternates] : false;
                }
            }

            // Add categories to urls
            $urls = array_merge($urls, $categories);
            unset($alternates);
        }
        /**
         *  FILES
         */
        if ($this->params->get('files_enable', 0) == 1 && $componentParams->get('display_file_view','0') == 1) {
            $now = Factory::getDate('now', 'UTC')->toSql();
            $query = 'SELECT `id`, `catid`, `alias`, `date`, `metadata`, `published`, `approved`, `access`, `title` FROM ' . $db->quoteName('#__phocadownload', 'file');

            if (!$debug_mode) {
                $query .= 'WHERE ' . $db->quoteName('file.published') . ' = ' . $db->quote('1') . ' 
                            AND ' . $db->quoteName('file.catid') . ' IN (SELECT `id` FROM ' . $db->quoteName('#__phocadownload_categories') . ' WHERE ' . $db->quoteName('published') . ' = ' . $db->quote('1') . ')
                                AND (
                                    ' . $db->quoteName('file.publish_up') . ' = ' . $db->quote($db->getNullDate()) . '
                                        OR ' . $db->quoteName('file.publish_up') . ' <= ' . $db->quote($now) . ') 
                                AND (' . $db->quoteName('file.publish_down') . ' = ' . $db->quote($db->getNullDate()) . ' 
                                        OR ' . $db->quoteName('file.publish_down') . ' >= ' . $db->quote($now) . ')';
            }


            $db->setQuery($query);
            $rows = $db->loadObjectList();

            $nullDate = $db->getNullDate();
            $changefreq = $this->params->get('files_changefreq', $config->get('changefreq', 'weekly'));
            $priority = $this->params->get('files_priority', $config->get('priority', '0.5'));

            // Add categories to arrays
            $files = [];
            $alternates = [];

            foreach ($rows as $row) {
                // Prepare loc attribute
                /**
                 * $id, $catid = 0, $idAlias = '', $catidAlias = '', $sectionid = 0, $type = 'file'
                 */
                $loc = \PhocaDownloadRoute::getFileRoute($row->id, $row->catid, $row->alias);

                // Prepare exclude attribute
                $metadata = new Registry($row->metadata);
                $exclude = [];
                if (preg_match('/noindex/', $metadata->get('robots', $config->get('siteRobots')))) {
                    $exclude[] = ['type' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE'),
                        'msg' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE_ROBOTS')];
                }

                if (isset($filesExcludeStates[$row->published])) {
                    $exclude[] = ['type' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE'),
                        'msg' => $filesExcludeStates[$row->published]];
                }

                if ($row->approved == 0) {
                    $exclude[] = ['type' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE'),
                        'msg' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE_NOT_APPROVED')];
                }

                if (!in_array($row->access, $config->get('guestAccess', []))) {
                    $exclude[] = ['type' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE'),
                        'msg' => Text::_('PLG_PHOCADOWNLOAD_EXCLUDE_FILE_ACCESS')];
                }

                // Prepare lastmod attribute
                $lastmod = (!empty($row->date) && $row->date != $nullDate) ? $row->date : false;

                // Prepare category object
                $file = new \stdClass();
                $file->type = Text::_('PLG_PHOCADOWNLOAD_TYPES_FILE');
                $file->title = $row->title;
                $file->loc = $loc;
                $file->changefreq = $changefreq;
                $file->priority = $priority;
                $file->lastmod = $lastmod;
                $file->exclude = (!empty($exclude)) ? $exclude : false;
                $file->alternates = ($multilanguage && !empty($row->association)) ? $row->association : false;

                // Add category to array
                $files[] = $file;

                // Add category to alternates array
                if ($multilanguage && !empty($row->association) && empty($exclude)) {
                    if (!isset($alternates[$row->association])) {
                        $alternates[$row->association] = [];
                    }

                    $alternates[$row->association][$row->language] = $loc;
                }
            }

            // Add alternates to categories
            if (!empty($alternates)) {
                foreach ($files as &$file) {
                    $file->alternates = ($file->alternates) ? $alternates[$file->alternates] : false;
                }
            }

            // Add categories to urls
            $urls = array_merge($urls, $files);
        }

        $event->setArgument(0, $urls);
    }
}
