<?php
/*
 *	Plugin Name: Theme Options Maker
 *	Plugin URI: 
 *	Description: Theme options framework and generator for WordPress Theme. Available as a plugin or library
 *	Author:  tonjoo
 *	Version: 1.0.4
 *	Author URI: https://tonjoo.com
 *  Contributor: Todi Adiyatmo Wijoyo, Lafif Astahdziq
 */

define('TOM_VERSION','1.0.4');
define('TOM_BASE_PATH',__DIR__);

// require_once( plugin_dir_path( __FILE__ ) . 'src/ajax.php');

//Included Files
include __DIR__.'/vendor/autoload.php';


// Plugin loaded
add_action('plugins_loaded', 'tom_generate_init');

function tom_generate_init()
{
	$tom = new Lotus\Almari\Container();
	$tom_option =  new Tonjoo\TOM\TOMOption($tom);	
	$tom_generate =  new Tonjoo\TOM\TOMGenerate($tom,$tom_option);

	$tom->register('tom',$tom);
	$tom->register('tom_option',$tom_option);
	$tom->register('tom_generate',$tom_generate);	

	// Load the alias mapper
	$aliasMapper = Lotus\Almari\AliasMapper::getInstance();

	// Create facade for TOM
	$alias['TOMOption'] = 'Tonjoo\TOM\Facade\TOMOptionFacade';
	$alias['TOMGenerate'] = 'Tonjoo\TOM\Facade\TOMGenerateFacade';
	
	$aliasMapper->facadeClassAlias($alias);

	//Register container to facade
	Tonjoo\TOM\Facade\TOMOptionFacade::setFacadeContainer($tom);
	Tonjoo\TOM\Facade\TOMGenerateFacade::setFacadeContainer($tom);

}

if( is_admin() ) {
	include __DIR__.'/hooks/tom-back-end.php';
} else {
	new Tonjoo\TOM\TOMShortcode;
}
