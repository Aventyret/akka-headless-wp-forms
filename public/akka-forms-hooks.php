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

add_action('admin_enqueue_scripts', function () {
  wp_set_script_translations('akka-forms', 'akka-forms', dirname( plugin_basename ( __FILE__ ) ) . '/../languages');
});

add_action('init', function () {
  /**
   * Add translations text domain
   */
  load_plugin_textdomain('akka-forms', false, WP_LANG_DIR . '/plugins');
});
