/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	var appUrl = info_site.app_url;
	var siteFile = encodeURIComponent(info_site.base_file + '/' + info_site.app_folder);
	var siteUrl = encodeURIComponent(info_site.base_url + '/' + info_site.app_folder);
	var sites = '&siteFile='+siteFile+'&siteUrl='+siteUrl;
	config.filebrowserBrowseUrl = appUrl + 'helpers/kcfinder/browse.php?type=files'+sites;
	config.filebrowserImageBrowseUrl = appUrl + 'helpers/kcfinder/browse.php?type=images'+sites;
	config.filebrowserFlashBrowseUrl = appUrl + 'helpers/kcfinder/browse.php?type=flash'+sites;
	config.filebrowserUploadUrl = appUrl + 'helpers/kcfinder/upload.php?type=files'+sites;
	config.filebrowserImageUploadUrl = appUrl + 'helpers/kcfinder/upload.php?type=images'+sites;
	config.filebrowserFlashUploadUrl = appUrl + 'helpers/kcfinder/upload.php?type=flash'+sites;
	config.height = 450;
	config.allowedContent = true;
	config.resize_enabled = false;
	config.extraPlugins = 'widget,lineutils,codesnippet';
};
