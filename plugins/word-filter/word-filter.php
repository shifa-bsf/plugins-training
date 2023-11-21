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
        add_action('admin_menu', array($this,'Menu'));
        add_action('admin_init', array($this, 'ourSettings'));
        if (get_option('plugin_words_to_filter'))
            add_filter('the_content', array($this, 'filterLogic'));
    }
    // Custom menu for the plugin
    function Menu() {
        $mainPageHook = add_menu_page('Words To Filter', 'Word Filter', 'manage_options', 'word-filter', array($this, 'wordFilterPage'), plugin_dir_url(__FILE__).'custom.svg', 80);
        add_submenu_page('word-filter', 'Words To Filter', 'Words List', 'manage_options', 'word-filter', array($this, 'wordFilterPage'));
        add_submenu_page('word-filter', 'Word Filter Options', 'Options', 'manage_options', 'word-filter-options', array($this, 'optionsSubPage'));
        add_action("load-{$mainPageHook}", array($this, 'mainPageAssets'));
    }

    // Adding plugin stylesheet
    function mainPageAssets() {
        wp_enqueue_style('filterAdminCss', plugin_dir_url(__FILE__) . 'style.css');
    }
    // update option and print message
    function handleForm() {
        if (wp_verify_nonce($_POST['ourNonce'], 'saveFilterWords') AND current_user_can('manage_options')) {
          update_option('plugin_words_to_filter', sanitize_text_field($_POST['plugin_words_to_filter'])); 
          ?>
          <div class="updated">
            <p>Your filtered words saved successfully.</p>
          </div>
        <?php } else { ?>
          <div class="error">
            <p>Sorry, you do not have permission to perform that action.</p>
          </div>
        <?php } 
      }
     //Main admin page   
      function wordFilterPage() { ?>
        <div class="wrap">
          <h1>Word Filter</h1>
          <?php if (isset($_POST['justsubmitted']) && $_POST['justsubmitted'] == "true") $this->handleForm() ?>
          <form method="POST">
            <input type="hidden" name="justsubmitted" value="true">
            <!-- creating a hidden field with nonce --> 
            <?php wp_nonce_field('saveFilterWords', 'ourNonce') ?>
            <label for="plugin_words_to_filter"><p>Enter a <strong>comma-separated</strong> list of words to filter from your site's content.</p></label>
            <div class="word-filter__flex-container">
              <textarea name="plugin_words_to_filter" id="plugin_words_to_filter" placeholder="bad, mean, awful, horrible"><?php echo esc_textarea(get_option('plugin_words_to_filter')) ?></textarea>
            </div>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
          </form>
        </div>
      <?php }

    //sub page - options
    function optionsSubPage() { ?>
    <div class="wrap">
      <h1>Word Filter Options</h1>
      <form action="options.php" method="POST">
        <?php
          settings_errors();
          settings_fields('replacementFields');
          do_settings_sections('word-filter-options');
          submit_button();
        ?>
      </form>
    </div>
    <?php 
    }

    // settings
    function ourSettings() {
        add_settings_section('replacement-text-section', null, null, 'word-filter-options');
        register_setting('replacementFields', 'replacementText');
        add_settings_field('replacement-text', 'Filtered Text', array($this, 'replacementFieldHTML'), 'word-filter-options', 'replacement-text-section');
    }

    //Replacement field html
    function replacementFieldHTML() { ?>
        <input type="text" name="replacementText" value="<?php echo esc_attr(get_option('replacementText', '***')) ?>">
        <p class="description">Leave blank to simply remove the filtered words.</p>
    <?php
    }

    // Do filter logic
    function filterLogic($content) {
        $badWords = explode(',', get_option('plugin_words_to_filter'));
        $badWordsTrimmed = array_map('trim', $badWords);
        return str_ireplace($badWordsTrimmed, esc_html(get_option('replacementText', '****')), $content);
    }

    
}



$wordFilterPlugin = new wordFilterPlugin();