/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here.
    // For complete reference see:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config

    // The toolbar groups arrangement, optimized for one toolbar rows.
    config.toolbarGroups = [
        { name: 'clipboard', groups: [ 'undo', 'clipboard' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'forms', groups: [ 'forms' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
    ];
    config.removeButtons = 'Cut,Copy,Paste,PasteText,PasteFromWord,Underline,Subscript,Superscript,Anchor,HorizontalRule,SpecialChar,About';

    // Plugins
    config.embed_provider = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}';
    config.extraPlugins = 'embed,autoembed,image2';//embedsemantic,uploadimage,uploadfile',

    // filebrowserBrowseUrl: 'http://example.com/ckfinder/ckfinder.html',
    // filebrowserUploadUrl: 'http://example.com/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',

    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removePlugins = 'image'; // Remove the default image plugin because image2, which offers captions for images
    config.removeDialogTabs = 'image:advanced;link:advanced'; // Simplify the Image and Link dialog windows. The "Advanced" tab is not needed in most cases.

    // Set the most common block elements.
    config.bodyClass = 'article-editor';
    config.format_tags = 'p;h1;h2;h3;pre';
    config.height = 300; // Make the editing area bigger than default.

};