<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * This file sets all default options for Kirby
 * 
 * DON'T OVERWRITE OPTIONS HERE!!!
 * 
 * All this options can be overwritten with the 
 * site/config/config.php file or any environment specific
 * config files. (i.e. site/config/config.mydomain.com.php)
 * 
 * Changing stuff in this file might break 
 * your Kirby installation, since those are the 
 * fallback values!
 * 
 * @package   Kirby CMS
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://getkirby.com/license
 */
c::set(array(
  
  /**
   * Human-readable version
   */ 
  'cms.version.string' => '2.0',
  
  /**
   * Machine-readable version 
   */
  'cms.version.number' => 2.0,

  /**
   * Required panel version
   */ 
  'panel.min.version' => 2.0,

  /**
   * Make sure the toolkit is up to date
   */
  'toolkit.min.version' => 2.0,

  /**
    * The base url
    * 
    * If false, the url will be autodetected
    * You can hardwire the url with this option
    * if autodetection fails for some reason.
    */ 
  'url' => false,

  /**
    * Subfolder setup 
    * 
    * i.e. http://yourdomain.com/subfolder
    * If set to false, Kirby will try to 
    * autodetect the subfolder. 
    */ 
  'subfolder' => false,

  /**
   * URL rewriting
   * 
   * By default Kirby assumes that url rewriting
   * is available on the server. If for some reasons
   * URL rewriting is not possible (i.e. mod_rewrite is not activated)
   * it can be switched off with this option
   */
  'rewrite' => true,

  /**
   * Current URL
   * 
   * You can overwrite the currently active URL
   * with this option to force rendering of a specific 
   * page. This can be used to convert particular pages
   * to a static version for example
   */
  'currentURL' => null,

  /**
   * URI of the home page
   * 
   * You can rename the content folder of the 
   * home page and thus the URL that way, but 
   * you must make sure that the folder is located
   * in the main level of the content folder. 
   */
  'home' => 'home',

  /**
   * If set to true, the name of the home page folder
   * will be included in the URL for the home page. 
   * i.e. http://yourdomain.com/home
   * 
   * This is used for the kirby panel mainly,
   * but could come in handy in other setups as well
   */
  'home.keepurl' => false,

  /**
   * URI of the error page
   *
   * You can rename the content folder of the 
   * error page and thus the URL that way, but 
   * you must make sure that the folder is located
   * in the main level of the content folder.   
   */
  'error' => 'error',

  /**
   * Send 404 header on error pages?
   * 
   * By default Kirby will send a 404 header
   * when the error page is opened, but you 
   * can switch that off with this option
   */
  'error.header' => true,
  
  /**
   * Default template name
   * 
   * Change the name of the default 
   * template here if needed
   */
  'tpl.default' => 'default',

  /**
   * Default location for templates
   */
  'tpl.root' => KIRBY_SITE_ROOT_TEMPLATES,

  /**
   * Show/hide PHP errors
   * 
   * By default Kirby will hide all 
   * PHP errors. Switch errors on with this 
   * option for debugging. 
   */
  'debug' => false,

  /**
   * Troubleshoot modal
   * 
   * When running into setup issues it might
   * come in handy to show Kirby's troubleshoot page. 
   * The troubleshoot page has a list of possible errors
   * and an overview of all set options. 
   * Set this option to true, to show it and reload
   * your page afterwards.
   */
  'troubleshoot' => false,

  /**
   * General switch for the markdown parser
   * 
   * If set to false, Markdown will be disabled 
   * throughout all functions
   */
  'markdown' => true,

  /**
   * Kirby has a built in line break converter, 
   * which is normally not available with Markdown
   * You can switch it off with this option.
   */
  'markdown.breaks' => true,

  /**
   * Markdown Extra is available as an alternative
   * for the standard Markdown parser. 
   * You can enable it with this option
   */
  'markdown.extra' => false,
  
  /**
   * Additional settings for the Markdown parser
   */
  'markdown.blocktags.a' => 'ins|del|img',
  'markdown.blocktags.b' => 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|script|noscript|form|fieldset|iframe|math|figure',

  /**
   * Enable smartypants with this option
   */
  'smartypants' => false,
  
  /**
   * Additional smartypants options
   */
  'smartypants.attr' => 1,
  'smartypants.doublequote.open' => '&#8220;',
  'smartypants.doublequote.close' => '&#8221;',
  'smartypants.space.emdash' => ' ',
  'smartypants.space.endash' => ' ',
  'smartypants.space.colon' => '&#160;',
  'smartypants.space.semicolon' => '&#160;',
  'smartypants.space.marks' => '&#160;',
  'smartypants.space.frenchquote' => '&#160;',
  'smartypants.space.thousand' => '&#160;',
  'smartypants.space.unit' => '&#160;',
  'smartypants.skip' => 'pre|code|kbd|script|math',

  /**
   * Set the default width and height 
   * for embedded videos in Kirbytext here
   */
  'kirbytext.video.width' => 480,
  'kirbytext.video.height' => 358,

  /**
   * Markdown automatically wraps single images in a new line
   * with p tags. You can manually switch that behavior 
   * off with this parameter. 
   */
  'kirbytext.unwrapImages' => false,

  /**
    * Tiny url setup
    */
  'tinyurl.enabled' => true,
  
  /**
   * By default Kirby's tinyurls are built like this: 
   * http://yourdomain.com/x/akjshdkajs
   * 
   * You can change the folder name for tinyurls with 
   * this option
   */
  'tinyurl.folder' => 'x',

  /**
   * The default timezone 
   */
  'timezone' => 'UTC',

  /**
   * Set the default locale for all 
   * language specific PHP functions
   */
  'locale' => 'en_US', 
  
  /**
   * The pagination class will produce
   * URLs with this parameter name: 
   * http://yourdomain.com/list-of-items/page:1
   *
   * Especially for non-english sites it might
   * be useful to overwrite this variable name 
   */
  'pagination.variable' => 'page',
  
  /**
   * By default the pagination class will use 
   * url parameters to fetch the current page. 
   * You can switch to "query" here to produce urls like:
   * http://yourdomain.com/list-of-items/?page=1
   */
  'pagination.method' => 'params',

  /**
    * default file extension for content files
    */
  'content.file.extension' => 'txt',

  /**
   * A list of filenames to ignore when looking for content files
   */
  'content.file.ignore' => array(),

  /**
   * Global switch to enable/disable the built-in cache
   */
  'cache' => false,
  
  /**
   * When autoupdate is set to true, Kirby will check 
   * all subfolders of your content folder for changes
   * and use modifications to auto-flush the cache. 
   * To get better performance in some cases, you might 
   * want to switch this off. But you must flush the cache
   * manually in this case or rely on the Panel. 
   */
  'cache.autoupdate' => true,

  /**
   * Enable/disable html caching
   * When enabled, Kirby will cache 
   * the entire generated html for each page, 
   * which is not being ignored.
   */
  'cache.html' => true,

  /**
   * A list of pages, which should be ignored 
   * by the cache. Add the uri (relative urls) of each page to ignore. 
   */
  'cache.ignore.urls' => array(),

  /**
   * A list of templates, which should be ignored by the cache. 
   */
  'cache.ignore.templates' => array(),

  /**
    * Enable/disable multi-language support
    */ 
  'lang.support' => false,

  /**
   * If true, Kirby will try to detect the 
   * current language of the user and switch to 
   * that language if available.
   */
  'lang.detect' => true,

  /**
   * URL mode for multi-language websites
   * 
   * default: The language code will be prepended to the URI (i.e. http://yourdomain.com/en/my-subpage)
   * short: The language code will not be prepended for the default langugae, but for all others (i.e. http://yourdomain.com/my-subpage & http://yourdomain.com/de/my-subpage)
   */
  'lang.urls' => 'default',

  /**
   * An array with the configuration for all 
   * available languages 
   */
  'lang.config' => array(
    'en' => array(
      'code'      => 'en',
      'name'      => 'English',
      'default'   => true, 
      'locale'    => 'en_US', 
      'available' => true,
    ),
    'de' => array(
      'code'      => 'de',
      'name'      => 'Deutsch',
      'locale'    => 'de_DE',
      'available' => true,
    )
  ),
  
  /**
   * Define the root for the folder
   * where auto-loadable css files should be located
   * Auto-loadable files have to be named as the template
   * they are loaded for. 
   */
  'css.auto.root' => KIRBY_INDEX_ROOT . DS . 'assets' . DS . 'css' . DS . 'templates',

  /**
   * Define the url for the folder
   * where auto-loadable css files should be located.
   */
  'css.auto.url'  => 'assets' . DS . 'css' . DS . 'templates',

  /**
   * Define the root for the folder
   * where auto-loadable js files should be located
   * Auto-loadable files have to be named as the template
   * they are loaded for. 
   */
  'js.auto.root' => KIRBY_INDEX_ROOT . DS . 'assets' . DS . 'js' . DS . 'templates',

  /**
   * Define the url for the folder
   * where auto-loadable js files should be located.
   */
  'js.auto.url'  => 'assets' . DS . 'js' . DS . 'templates',

  /**
   * This array will define how files are 
   * organized. You can extend this to improve Kirby's 
   * file type and mime type detection
   * 
   * all files, which don't match the criteria below 
   * will be categorized as "other"
   */
  'fileinfo' => array(

    // images
    'jpg'      => array('type' => 'image', 'mime' => 'image/jpeg'),
    'jpeg'     => array('type' => 'image', 'mime' => 'image/jpeg'),
    'gif'      => array('type' => 'image', 'mime' => 'image/gif'),
    'png'      => array('type' => 'image', 'mime' => 'image/png'),
    'svg'      => array('type' => 'image', 'mime' => 'image/svg+xml'),
    'ico'      => array('type' => 'image', 'mime' => 'image/x-icon'),
    'tif'      => array('type' => 'image', 'mime' => 'image/tiff'),
    'tiff'     => array('type' => 'image', 'mime' => 'image/tiff'),
    'bmp'      => array('type' => 'image', 'mime' => 'image/bmp'),

    // documents
    'txt'      => array('type' => 'document', 'mime' => 'text/plain'),
    'mdown'    => array('type' => 'document', 'mime' => 'text/plain'),
    'md'       => array('type' => 'document', 'mime' => 'text/plain'),
    'markdown' => array('type' => 'document', 'mime' => 'text/plain'),
    'pdf'      => array('type' => 'document', 'mime' => 'application/pdf'),
    'doc'      => array('type' => 'document', 'mime' => 'application/msword'),
    'docx'     => array('type' => 'document', 'mime' => 'application/msword'),
    'xls'      => array('type' => 'document', 'mime' => 'application/msexcel'),
    'xlsx'     => array('type' => 'document', 'mime' => 'application/msexcel'),
    'ppt'      => array('type' => 'document', 'mime' => 'application/mspowerpoint'),

    // archives
    'zip'      => array('type' => 'archive', 'mime' => 'application/zip'),
    'tar'      => array('type' => 'archive', 'mime' => 'application/x-tar'),
    'gz'       => array('type' => 'archive', 'mime' => 'application/x-gzip'),
    'gzip'     => array('type' => 'archive', 'mime' => 'application/x-gzip'),
    'tgz'      => array('type' => 'archive', 'mime' => 'application/gnutar'),

    // code
    'js'       => array('type' => 'code', 'mime' => 'application/javascript'),
    'css'      => array('type' => 'code', 'mime' => 'text/css'),
    'scss'     => array('type' => 'code', 'mime' => 'text/css'),
    'htm'      => array('type' => 'code', 'mime' => 'text/html'),
    'html'     => array('type' => 'code', 'mime' => 'text/html'),
    'php'      => array('type' => 'code', 'mime' => 'text/php'),
    'xml'      => array('type' => 'code', 'mime' => 'application/xml'),
    'json'     => array('type' => 'code', 'mime' => 'application/json'),

    // videos
    'mov'      => array('type' => 'video', 'mime' => 'video/quicktime'),
    'avi'      => array('type' => 'video', 'mime' => 'video/avi'),
    'ogg'      => array('type' => 'video', 'mime' => 'video/ogg'),
    'ogv'      => array('type' => 'video', 'mime' => 'video/ogg'),
    'webm'     => array('type' => 'video', 'mime' => 'video/webm'),
    'flv'      => array('type' => 'video', 'mime' => 'video/x-flv'),
    'swf'      => array('type' => 'video', 'mime' => 'application/x-shockwave-flash'),
    'mp4'      => array('type' => 'video', 'mime' => 'video/mp4'),
    'mv4'      => array('type' => 'video', 'mime' => 'video/mv4'),

    // audio
    'mp3'      => array('type' => 'audio', 'mime' => 'audio/mp3'),
    'wav'      => array('type' => 'audio', 'mime' => 'audio/wav'),
    'aif'      => array('type' => 'audio', 'mime' => 'audio/aiff'),
    'aiff'     => array('type' => 'audio', 'mime' => 'audio/aiff'),

  ),

));
