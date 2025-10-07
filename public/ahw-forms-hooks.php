<?php
add_action('enqueue_block_editor_assets', function() {
  wp_enqueue_script(
      'akka-forms',
      AKKA_HEADLESS_WP_FORMS_URI . 'dist/editor.js',
      ['editor', 'wp-data', 'wp-element', 'wp-components'],
      filemtime(AKKA_HEADLESS_WP_FORMS_DIR . '/dist/editor.js')
  );

  wp_enqueue_style(
      'akka-forms',
      AKKA_HEADLESS_WP_FORMS_URI . 'dist/editor.css',
      [],
      filemtime(AKKA_HEADLESS_WP_FORMS_DIR . '/dist/editor.css')
  );
});
