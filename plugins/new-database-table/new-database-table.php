<?php

/*
  Plugin Name: Pet Adoption (New DB Table)
  Version: 1.0
  Author: shifa
  Author URI: #
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'inc/generate-pet.php';

class Pet_adoption_table_plugin {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . "pets";
    add_action('activate-new-database-table/new-database-table.php', array($this, 'on_activate'));
    // add_action('admin_head', array($this, 'populate_fast'));
    add_action('admin_post_create_pet', array($this, 'create_pet'));
    add_action('admin_post_nopriv_create_pet', array($this, 'create_pet'));
    add_action('admin_post_delete_pet', array($this, 'delete_pet'));
    add_action('admin_post_nopriv_delete_pet', array($this, 'delete_pet'));
    add_action('wp_enqueue_scripts', array($this, 'load_assets'));
    add_filter('template_include', array($this, 'load_template'), 99);
  }

  //creating custom table
  function on_activate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename (
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      birthyear smallint(5) NOT NULL DEFAULT 0,
      petweight smallint(5) NOT NULL DEFAULT 0,
      favfood varchar(60) NOT NULL DEFAULT '',
      favhobby varchar(60) NOT NULL DEFAULT '',
      favcolor varchar(60) NOT NULL DEFAULT '',
      petname varchar(60) NOT NULL DEFAULT '',
      species varchar(60) NOT NULL DEFAULT '',
      PRIMARY KEY  (id)
    ) $this->charset;"); 
  }

  function on_admin_refresh() {
    global $wpdb;
    // insert data to table (here adding just one data)
    $wpdb->insert($this->tablename, generate_pet());
  }

  function load_assets() {
    if (is_page('pet-adoption')) {
      wp_enqueue_style('petadoptioncss', plugin_dir_url(__FILE__) . 'pet-adoption.css',array(),"1.0.0");
    }
  }

  function load_template($template) {
    if (is_page('pet-adoption')) {
      return plugin_dir_path(__FILE__) . 'inc/template-pets.php';
    }
    return $template;
  }

  //inserting multiple data to table once
  function populate_fast() {
    $query = "INSERT INTO $this->tablename (`species`, `birthyear`, `petweight`, `favfood`, `favhobby`, `favcolor`, `petname`) VALUES ";
    $numberofpets = 50;
    for ($i = 0; $i < $numberofpets; $i++) {
      $pet = generate_pet();
      $query .= "('{$pet['species']}', {$pet['birthyear']}, {$pet['petweight']}, '{$pet['favfood']}', '{$pet['favhobby']}', '{$pet['favcolor']}', '{$pet['petname']}')";
      if ($i != $numberofpets - 1) {
        $query .= ", ";
      }
    }
    /*
    Never use query directly like this without using $wpdb->prepare in the
    real world. I'm only using it this way here because the values I'm 
    inserting are coming fromy my innocent pet generator function so I
    know they are not malicious, and I simply want this example script
    to execute as quickly as possible and not use too much memory.
    */
    global $wpdb;
    $wpdb->query($query);
  }

  // inserting new pet data to the table
  function create_pet(){
    if(current_user_can('administrator')){
      $pet = generate_pet();
      $pet['petname'] = sanitize_text_field($_POST['newpetname']);
      global $wpdb;
      $wpdb->insert($this->tablename, $pet);
      wp_safe_redirect(site_url('/pet-adoption'));
    }
    else{
      wp_redirect(site_url());
    }
  }
  function delete_pet() {
    if (current_user_can('administrator')) {
      $id = sanitize_text_field($_POST['idtodelete']);
      global $wpdb;
      $wpdb->delete($this->tablename, array('id' => $id));
      wp_safe_redirect(site_url('/pet-adoption'));
    } else {
      wp_safe_redirect(site_url());
    }
    exit;
  }

}

$pet_adoption_table_plugin = new Pet_adoption_table_plugin();