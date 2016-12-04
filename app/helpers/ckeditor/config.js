/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	var appUrl = info_site.base_url;
	var siteUrl = info_site.app_folder;
	config.filebrowserBrowseUrl = appUrl + 'app/helpers/kcfinder/browse.php?type=files&site='+siteUrl;
	config.filebrowserImageBrowseUrl = appUrl + 'app/helpers/kcfinder/browse.php?type=images&site='+siteUrl;
	config.filebrowserFlashBrowseUrl = appUrl + 'app/helpers/kcfinder/browse.php?type=flash&site='+siteUrl;
	config.filebrowserUploadUrl = appUrl + 'app/helpers/kcfinder/upload.php?type=files&site='+siteUrl;
	config.filebrowserImageUploadUrl = appUrl + 'app/helpers/kcfinder/upload.php?type=images&site='+siteUrl;
	config.filebrowserFlashUploadUrl = appUrl + 'app/helpers/kcfinder/upload.php?type=flash&site='+siteUrl;
	config.height = 450;
	config.allowedContent = true;
	config.resize_enabled = false;
	/*
	config.entities_latin = false;
	config.entities_greek = false;
	config.entities = false;
	config.basicEntities = false;
	*/
	config.extraPlugins = 'widget,lineutils,codesnippet';
};
