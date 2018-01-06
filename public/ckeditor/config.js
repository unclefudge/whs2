/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
		{ name: 'styles', items: [ 'Styles', 'Format' ] },
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
		{ name: 'links', items: [ 'Link', 'Unlink' ] },
		{ name: 'insert', items: [ 'Image', 'EmbedSemantic', 'Table' ] },
		{ name: 'tools', items: [ 'Maximize' ] },
		{ name: 'editing', items: [ 'Scayt' ] },
		{ name: 'document', items: [ 'Source' ] }
	];

	//config.language = 'en';
	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';



	toolbar: [

	],
		extraPlugins: 'autoembed,embedsemantic,image2,',//uploadimage,uploadfile',
		// filebrowserBrowseUrl: 'http://example.com/ckfinder/ckfinder.html',
		// filebrowserUploadUrl: 'http://example.com/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		removePlugins: 'image', // Remove the default image plugin because image2, which offers captions for images
		height: 300, // Make the editing area bigger than default.
		bodyClass: 'article-editor',
		format_tags: 'p;h1;h2;h3;pre',
		removeDialogTabs: 'image:advanced;link:advanced', // Simplify the Image and Link dialog windows. The "Advanced" tab is not needed in most cases.
};
