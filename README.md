[![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)]() [![PHP](https://img.shields.io/badge/PHP-7.4+-green.svg)]() [![JoomlaVersion](https://img.shields.io/badge/Joomla-3.x-gray.svg)]() [![JoomlaVersion](https://img.shields.io/badge/Joomla-4.x-orange.svg)]() [![JoomlaVersion](https://img.shields.io/badge/Joomla-5.x-orange.svg)]() [![Version](https://img.shields.io/badge/Documentation-blue.svg)](https://web-tolk.ru/en/dev/joomla-plugins/jlsitemap-phoca-download-joomla-plugin?utm_source=github)
# JL Sitemap - Phoca-Download for Joomla
The plugin is a data provider of the PhocaDownload component for the JL Sitemap static XML site map component. Adds links to categories and Phoca Download files to the Joomla JL Sitemap XML sitemap, taking into account whether the file is published or not, the publication date, the file is confirmed or not.

- [JL Sitemap component](https://joomline.net/ru/extensions/jl-sitemap.html)
- [JL Sitemap on GitHub](https://github.com/Joomline/jlsitemap)

## Plugin versions
### Versions 0.9.x 
Versions 0.9.x were created for Joomla 3 using the old extension architecture. They will probably work on Joomla 4 and Joomla 5 with the backward compatibility plugin enabled
### Versions 1.x
Versions 1.x were created for Joomla 4 and Joomla 5 using the new extension architecture. If your site is on Joomla 4 and older, it is recommended to use the latest versions of the plugin.
## Settings of the JL Sitemap XML plugin for Phoca Download Joomla
### Adding links to file categories
When adding links to categories, it is taken into account whether they are published or not. Unpublished categories are not included in the site map.
### Adding links to files
In the configuration of the Phoca Download component, there is a Display File View parameter with 3 possible values:
- **Yes** - A detailed file information page will be enabled for files. The user and search robots will be able to navigate to this page.
- **No** - The file details page is not displayed. Information about the file is shown in a tooltip.
- **Yes (Only Modalbox - Category View)** - The file details page is not displayed. Information about the file is displayed in a pop-up (modal) window in the form of a category. Including files in the sitemap makes sense only if users have the opportunity to go to the detailed information page about the file. If this feature is not available or the information is shown only in a pop-up (modal) window, the files are not included in the sitemap.
The plugin monitors the status of this parameter and forcibly excludes links to files in cases where the Phoca Download Display File View component parameter is not set to Yes. If viewing detailed information about a file on a separate page is allowed, the plugin settings are taken into account and you can manually set whether to include links to files in the Joomla XML sitemap or not.
Also, those files that have expired or have not yet been published, or the uploaded file has not been authorized - are not added to the XML site map.