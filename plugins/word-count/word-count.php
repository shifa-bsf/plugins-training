<?php

/*
  Plugin Name: Word count
  Description: A amazing plugin.
  Version: 1.0
  Author: Shifa
  Author URI: https://github.com/shifa-bsf
  Text Domain: wcpdomain
  Domain Path:/languages
*/
class WordCountPlugin {
  function __construct(){
    add_action('wp_enqueue_scripts', array($this,'callback_for_setting_up_scripts'));
    add_action('admin_menu', array($this,'settingsPage'));
    add_action('admin_init', array($this,'settings'));
    add_filter('the_content', array($this, 'ifWrap'));
    add_filter('init', array($this, 'languages'));
  }

  // 
  function languages(){
    load_plugin_textdomain('wcpdomain', false, dirname(plugin_basename(__FILE__)).'/languages');
  }
  // Add plugin stylesheet
  function callback_for_setting_up_scripts() {
    wp_register_style( 'wcp_style', plugins_url( 'style.css', __FILE__ ), array(), "1.0.0" );
    wp_enqueue_style( 'wcp_style' );
  }


  // creating custom option page under settings
  function settingsPage(){
    add_options_page('Word count settings',__('Word count','wcpdomain'),'manage_options','word-count-settings',array($this,'SettingsHTML'));
    // add_options_page( page_title, menu_title, capability, menu_slug, function with page html, position )
  }

  // HTML for settings page
  function SettingsHTML(){
    ?>
    <div class="wrap">
      <h1>Word Count Settings</h1>
        <form action="options.php" method="POST">
          <?php
          settings_fields('wordcountplugin');
          do_settings_sections('word-count-settings');
          submit_button();
          ?>
        </form>
    </div>
    <?php
  }
  
  // Define settings sections and fields
  function settings() {
    add_settings_section('wcp_first_section', null, null, 'word-count-settings');

    add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));

    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));

    add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), 'word-count-settings', 'wcp_first_section', array('theName' => 'wcp_wordcount'));
    register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    add_settings_field('wcp_charactercount', 'Character Count', array($this, 'checkboxHTML'), 'word-count-settings', 'wcp_first_section', array('theName' => 'wcp_charactercount'));
    register_setting('wordcountplugin', 'wcp_charactercount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), 'word-count-settings', 'wcp_first_section', array('theName' => 'wcp_readtime'));
    register_setting('wordcountplugin', 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
  }

  //location field validation
  function sanitizeLocation($input) {
    if ($input != '0' AND $input != '1') {
      add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end.');
      return get_option('wcp_location');
    }
    return $input;
  }

  // reusable checkbox function
  function checkboxHTML($args) { ?>
    <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
  <?php }

  //Headline field
  function headlineHTML() { ?>
    <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>" required>
  <?php }

  //Location field
  function locationHTML() { ?>
    <select name="wcp_location">
      <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
      <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
    </select>
  <?php }

  //conditionally display custom content
  function ifWrap($content){
    if( is_main_query() AND 
        is_single() AND 
        get_post_type() === 'post' AND
        (
          get_option('wcp_wordcount', '1') OR
          get_option('wcp_charactercount', '1') OR
          get_option('wcp_readtime', '1') 
        )
    ){
      return $this->displayContent($content);
    }
    else{
      return $content;
    }
  }
  // Set html content to display 
  function displayContent($content){
    $html = '<div class="wcp-main"><h5>'.get_option('wcp_headline','Post Statistics').'</h5><ul>';
    // get word count 
    if (get_option('wcp_wordcount', '1') OR get_option('wcp_readtime', '1')) {
      $wordCount = str_word_count(strip_tags($content));
    }
    if (get_option('wcp_wordcount', '1')) {
      $html .= '<li>'.__('This post has','wcpdomain').' '. $wordCount .' '.__('words','wcpdomain').'.</li>';
    }
    if (get_option('wcp_charactercount', '1')) {
      $html .= '<li>This post has ' . strlen(strip_tags($content)) . ' characters.</li>';
    }
    if (get_option('wcp_readtime', '1')) {
      $html .= '<li>This post will take about ' . round($wordCount/225) . ' minute(s) to read.</li>';
    }
    $html .= '</ul></div>';

    if(get_option('wcp_location','0')=='0'){
      return $html.$content;
    }
    else{
      return $content.$html;
    }
  }

}


$WordCountPlugin = new WordCountPlugin();




