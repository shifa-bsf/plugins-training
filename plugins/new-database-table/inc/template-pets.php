<?php

require_once plugin_dir_path(__FILE__) . 'get-pets.php';
$get_pets = new get_pets();

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Pet Adoption</h1>
    <div class="page-banner__intro">
      <p>Providing forever homes one search at a time.</p>
    </div>
  </div>  
</div>

<div class="container container--narrow page-section">

  <p>
    This page took <strong><?php echo timer_stop();?></strong> seconds to prepare. 
    Found <strong><?php echo $get_pets->count; ?></strong> results 
    (showing the first <?php echo count($get_pets->pets) ?>).
  </p>
  
  <table class="pet-adoption-table">
    <tr>
      <th>Name</th>
      <th>Species</th>
      <th>Weight</th>
      <th>Birth Year</th>
      <th>Hobby</th>
      <th>Favorite Color</th>
      <th>Favorite Food</th>
    </tr>
    <?php
      foreach($get_pets->pets as $pet) { ?>
        <tr>
          <td><?php echo $pet->petname; ?></td>
          <td><?php echo $pet->species; ?></td>
          <td><?php echo $pet->petweight; ?></td>
          <td><?php echo $pet->birthyear; ?></td>
          <td><?php echo $pet->favhobby; ?></td>
          <td><?php echo $pet->favcolor; ?></td>
          <td><?php echo $pet->favfood; ?></td>
          <?php if (current_user_can('administrator')) { ?>
            <td style="text-align: center;">
            <form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="POST">
              <input type="hidden" name="action" value="delete_pet">
              <input type="hidden" name="idtodelete" value="<?php echo $pet->id; ?>">
              <button class="delete-pet-button">X</button>
            </form>
          </td>
          <?php } ?>
        </tr>
      <?php }
    ?>
  </table>
  
  <?php 
    //checking the capability of current user
    if (current_user_can('administrator')) { ?>
      <!-- On form submit it creates a new hook named admin_post_create_pet -->
      <form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" class="create-pet-form" method="POST">
        <p>Enter just the name for a new pet. Its species, weight, and other details will be randomly generated.</p>
        <input type="hidden" name="action" value="create_pet">
        <input type="text" name="newpetname" placeholder="name...">
        <button>Add Pet</button>
      </form>
    <?php }
  ?>
</div>

<?php get_footer(); ?>