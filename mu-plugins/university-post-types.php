<?php
function university_post_types() {
  register_post_type('event', array(
    'capability_type' => 'event',
    'map_meta_cap'=>true,
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'excerpt'),
    'rewrite' => array('slug' => 'events'),
    'has_archive' => true,
    'public' => true,
    'labels' => array(
      'name' => 'Events',
      'add_new_item' => 'Add New Event',
      'edit_item' => 'Edit Event',
      'all_items' => 'All Events',
      'singular_name' => 'Event'
    ),
    'menu_icon' => 'dashicons-calendar'
  ));

  //Programs post type
  register_post_type('program', array(
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'excerpt'),
    'rewrite' => array('slug' => 'programs'),
    'has_archive' => true,
    'public' => true,
    'labels' => array(
      'name' => 'programs',
      'add_new_item' => 'Add New program',
      'edit_item' => 'Edit program',
      'all_items' => 'All programs',
      'singular_name' => 'program'
    ),
    'menu_icon' => 'dashicons-awards'
  ));

    // Professor Post Type
    register_post_type('professor', array(
      'show_in_rest' => true,
      'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
      'public' => true,
      'labels' => array(
        'name' => 'Professors',
        'add_new_item' => 'Add New Professor',
        'edit_item' => 'Edit Professor',
        'all_items' => 'All Professors',
        'singular_name' => 'Professor'
      ),
      'menu_icon' => 'dashicons-welcome-learn-more'
    ));

     // Note Post Type
     register_post_type('Note', array(
      'capability_type' => 'note',
      'map_meta_cap'=>true,
      'show_in_rest' => true,
      'supports' => array('title', 'editor'),
      'public' => false,//to make it only available private for each user
      'show_ui'=>true,
      'labels' => array(
        'name' => 'Notes',
        'add_new_item' => 'Add New Note',
        'edit_item' => 'Edit Note',
        'all_items' => 'All Notes',
        'singular_name' => 'Note'
      ),
      'menu_icon' => 'dashicons-welcome-write-blog'
    ));

    // Like Post Type
    register_post_type('like', array(
      'supports' => array('title','author'),
      'public' => false,
      'show_ui' => true,
      'labels' => array(
        'name' => 'Likes',
        'add_new_item' => 'Add New Like',
        'edit_item' => 'Edit Like',
        'all_items' => 'All Likes',
        'singular_name' => 'Like'
      ),
      'menu_icon' => 'dashicons-heart'
    ));
}

add_action('init', 'university_post_types');
// echo $abc.' Testing break point';