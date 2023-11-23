<?php
function generateProfessorHTML($id) {
  $profPost = get_post($id);

  if ($profPost) {
      ob_start(); ?>
      <div class="professor-callout">
          <div class="professor-callout__photo" style="background-image: url(<?php echo get_the_post_thumbnail_url($profPost, 'professorPortrait'); ?>)"></div>
          <div class="professor-callout__text">
              <h5><?php echo esc_html($profPost->post_title); ?></h5>
              <p><?php echo wp_trim_words($profPost->post_content, 30); ?></p>

              <?php
              $relatedPrograms = get_field('related_program', $id);
              if ($relatedPrograms) { ?>
                  <p><?php echo esc_html($profPost->post_title); ?> teaches:
                      <?php foreach ($relatedPrograms as $key => $program) {
                          echo get_the_title($program);
                          if ($key != array_key_last($relatedPrograms) && count($relatedPrograms) > 1) {
                              echo ', ';
                          }
                      } ?>.
                  </p>
              <?php }
              ?>

              <p><strong><a href="<?php echo esc_url(get_permalink($profPost)); ?>">Learn more about <?php echo esc_html($profPost->post_title); ?> &raquo;</a></strong></p>
          </div>
      </div>
      <?php
      return ob_get_clean();
  }
}
