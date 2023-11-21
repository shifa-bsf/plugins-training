<?php
/*
  Plugin Name: Word filter
  Description: A amazing word filter plugin.
  Version: 1.0
  Author: Shifa
  Author URI: https://github.com/shifa-bsf
*/

if( !defined('ABSPATH') ) exit; 

class wordFilterPlugin{
    function __construct(){
    add_action('admin_menu', array($this,'menu'));
    }
    // Custom menu for the plugin
    function menu(){
        add_menu_page('Words to filter', 'Word filter', 'manage_options','wordfilter', array($this,'wordFilterPage'),'dashicons-smiley',15);
    }

    function wordFilterPage(){

    }
}



$wordFilterPlugin = new wordFilterPlugin();