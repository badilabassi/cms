<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Legacy Code
 * 
 * @package Kirby CMS
 */

class Obj extends KirbyObject {}

class Page extends KirbyPage {}
class Pages extends KirbyPages {}
class Pagination extends KirbyPagination {}

class Uri extends KirbyUri {}
class UriQuery extends KirbyUriQuery {}
class UriParams extends KirbyUriParams {}
class UriPath extends KirbyUriPath {}

class File extends KirbyFile {}
class Files extends KirbyFiles {}
class Image extends KirbyImage {}
class Video extends KirbyFile {}

class Variable extends KirbyVariable {}