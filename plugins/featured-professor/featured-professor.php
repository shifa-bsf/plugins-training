<?php

/*
  Plugin Name: Featured Professor Block Type
  Version: 1.0
  Author: Shifa
  Author URI: #
  Text Domain: featured-professor
  Domain Path: /languages
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'inc/generate-professor.php';
require_once plugin_dir_path(__FILE__) . 'inc/related-posts.php';

class FeaturedProfessor {
  function __construct() {
    add_action('init', [$this, 'fp_register_block']);
    add_action('rest_api_init', [$this, 'fp_html']);
    add_filter('the_content', [$this, 'fp_add_related_posts']);
  }

  function fp_register_block() {
    load_plugin_textdomain('featured-professor', false, dirname(plugin_basename(__FILE__)) . '/languages');

    register_meta('post', 'featuredprofessors', array(
      'show_in_rest' => true,
      'type' => 'number',
      'single' => false
    ));

    wp_register_script('featuredProfessorScript', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-i18n', 'wp-editor'),'1.0.0');
    wp_register_style('featuredProfessorStyle', plugin_dir_url(__FILE__) . 'build/index.css',array(),'1.0.0');
    wp_set_script_translations('featuredProfessorScript', 'featured-professor', plugin_dir_path(__FILE__) . '/languages');

    
    register_block_type('ourplugin/featured-professor', array(
      'render_callback' => [$this, 'fp_render_callback'],
      'editor_script' => 'featuredProfessorScript',
      'editor_style' => 'featuredProfessorStyle'
    ));
  }
  
  function fp_render_callback($attributes) {
    if ($attributes['profId']) {
      wp_enqueue_style('featuredProfessorStyle');
      return fp_generate_professor_html($attributes['profId']);
    } else {
      return NULL;
    }
  }

  function fp_html() {
    register_rest_route('featuredProfessor/v1', 'getHTML', array(
      'methods' => WP_REST_SERVER::READABLE,
      'callback' => [$this, 'fp_get_prof_HTML']
    ));
  }

  function fp_get_prof_HTML($data) {
    return fp_generate_professor_html($data['profId']);
  }

  function fp_add_related_posts($content) {
    if (is_singular('professor') && in_the_loop() && is_main_query()) {
      return $content . fp_related_posts_html(get_the_id());
    }
    return $content;
  }

}

$featuredProfessor = new FeaturedProfessor();