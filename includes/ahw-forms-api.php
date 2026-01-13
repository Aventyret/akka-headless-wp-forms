<?php
use \Akka_headless_wp_akka_blocks as AkkaBlocks;
use \Akka_headless_wp_content as Content;
use \Akka_headless_wp_resolvers as Resolvers;
use \Akka_headless_wp_utils as Utils;

class Akka_headless_wp_forms_api
{
  public static function submit_form($request)
  {
    $data = $request->get_params();
    $form_id = Utils::getRouteParam($data, 'form_id');
    $fields = Utils::getRouteParam($data, 'fields');

    if (!$form_id || !$fields) {
      return new \WP_REST_Response(['message' => 'Required parameters are missing'], 400);
    }

    $form_post = Content::get_akka_post($form_id);

    if (!$form_post || $form_post['post_type'] != 'akka_form') {
      return new \WP_REST_Response(['message' => 'Form is not found'], 404);
    }

    $form = apply_filters('ahw_post_data', $form_post);

    if (!self::validate_form($form, $fields)) {
      return new \WP_REST_Response(['message' => 'The submitted form fields are invalid'], 400);
    }

    do_action('ahw_form_submit', $form, $fields);
    if ($form['settings']['email_confirmation']) {
      self::send_email_confirmation($form, $fields);
    }
    if ($form['settings']['save_entries']) {
      self::save_entries($form, $fields);
    }
    return [
      'status' => 'OK',
    ];
  }

  private static function validate_form($form, $fields) {
    return array_reduce($form['form_fields'], function($all_fields_are_valid, $field) use($fields) {
      if ($field['required'] && self::field_is_empty($fields, $field['field_id'])) {
        $all_fields_are_valid = false;
      }
      return $all_fields_are_valid;
    }, true);
  }

  private static function field_is_empty($fields, $field_id) {
    if (!isset($fields[$field_id])) {
      return true;
    }
    $value = $fields[$field_id];
    if ($value === '0') {
      return false;
    }
    if ($value === 0) {
      return false;
    }
    return empty($value);
  }

  private static function send_email_confirmation($form, $fields) {
    $html =
      '<!DOCTYPE html>
<html>
<head>
    <title>' .
      $form['settings']['email_confirmation']['subject'] .
      '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
  <body>
    <div class="email-body" style="color: #000; font-family: Arial, sans-serif; font-size: 16px; line-height: 22px; max-width: 665px; margin: 0 auto; text-align: left; padding-top: 0;">';

    $html .= __('Form', 'akka-forms') . ' ' . $form['post_title'] . ' ' . __('was submitted.', 'akka-forms') . '
    <br/>
    <br/>';

    foreach($form['form_fields'] as $field) {
      if (!in_array($field['type'], ['file', 'select', 'heading'])) {
        $html .= $field['label'] . ':
        <br/>' . (Resolvers::resolve_field($fields, $field['field_id']) ?? '-') . '
        <br/>
        <br/>';
      }
      if ($field['type'] == 'select') {
        $choice_text = '-';
        $extra_information_label = null;
        $extra_information_text = '-';
        foreach($field['choices'] as $choice) {
          if ($choice['value'] == Resolvers::resolve_field($fields, $field['field_id'])) {
            $choice_text = $choice['text'];

            if (Resolvers::resolve_field($choice, 'extra_information')) {
              $extra_information_label = $choice['extra_information_label'];
              $extra_information_text = Resolvers::resolve_field($fields, $field['field_id'] . '_extra_information') ?? '-';
            }
          }
        }
        $html .= $field['label'] . ':
        <br/>' . $choice_text . '
        <br/>
        <br/>';

        if ($extra_information_label) {
          $html .=  $extra_information_label . '
          <br/>' . $extra_information_text . '
          <br/>
          <br/>';
        }
      }
      if ($field['type'] == 'file') {
        $html .= $field['label'] . '
        <br/>';

        $files = Resolvers::resolve_array_field($fields, $field['field_id']);
        foreach($files as $downloadUrl) {
          $html .= $downloadUrl . '
          <br/>';
        }
        if (empty($files)) {
          $html .= '-
          <br/>';
        }
        $html .= '
          <br/>';
      }
    }

    $html .= '
    </div>
  </body>
</html>';

    add_filter('wp_mail_content_type', function ($content_type) {
      return 'text/html';
    });

    wp_mail($form['settings']['email_confirmation']['to_address'], $form['settings']['email_confirmation']['subject'], $html);
  }

  private static function save_entries($form, $fields) {
    $content = '';
    foreach($form['form_fields'] as $field) {
      if (!in_array($field['type'], ['file', 'select', 'heading'])) {
        $content .= $field['label'] . ':
' . (Resolvers::resolve_field($fields, $field['field_id']) ?? '-') . '

';
      }
      if ($field['type'] == 'select') {
        $choice_text = '-';
        $extra_information_label = null;
        $extra_information_text = '-';
        foreach($field['choices'] as $choice) {
          if ($choice['value'] == Resolvers::resolve_field($fields, $field['field_id'])) {
            $choice_text = $choice['text'];

            if (Resolvers::resolve_field($choice, 'extra_information')) {
              $extra_information_label = $choice['extra_information_label'];
              $extra_information_text = Resolvers::resolve_field($fields, $field['field_id'] . '_extra_information') ?? '-';
            }
          }
        }
        $content .= $field['label'] . ':
' . $choice_text . '

';

        if ($extra_information_label) {
          $extra_information_text = '-';

          $content .=  $extra_information_label . '
' . $extra_information_text . '

';
        }
      }
      if ($field['type'] == 'file') {
        $content .= $field['label'] . ':
';
        $files = Resolvers::resolve_array_field($fields, $field['field_id']);
        foreach($files as $downloadUrl) {
          $content .= $downloadUrl . '
';
        }
        if (empty($files)) {
          $content .= '-
';
        }
        $content .= '
';
      }
    }

    $comment = [
        "comment_post_ID" => $form['post_id'],
        "comment_author" => sanitize_text_field(__('Form entry', 'akka-forms')),
        "comment_content" => sanitize_textarea_field($content),
        "comment_approved" => 1,
        "comment_type" => "comment",
        "comment_parent" => 0,
        "comment_meta" => [
          // TODO save files
          // "files" => []
          // "_files" => "field_files"
        ],
    ];
    $comment_id = wp_insert_comment($comment);
  }
}
