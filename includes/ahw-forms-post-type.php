<?php
use \Akka_headless_wp_akka_post_types as PostTypes;
use \Akka_headless_wp_resolvers as Resolvers;

class Akka_headless_wp_forms_post_type
{
  private static $post_type_slug = 'akka_form';

  public static function hooks()
  {
    PostTypes::register_post_type(self::$post_type_slug, self::post_type_args(), [
      'acf_field_groups' => self::post_type_acf_field_groups(),
    ]);

    add_filter('ahw_post_data', function ($post_data) {
      if ($post_data['post_type'] != self::$post_type_slug) {
        return $post_data;
      }
      $post_data['form_fields'] = Resolvers::resolve_field($post_data, 'form_fields');

      $post_data['consent'] = Resolvers::resolve_boolean_field($post_data, 'require_consent') ? [
        'label' => Resolvers::resolve_field($post_data, 'consent_label'),
        'required_text' => __('Consent is required', 'akka-forms'),
      ] : null;
      $post_data['settings'] = [
        'email_confirmation' => Resolvers::resolve_boolean_field($post_data, 'email_confirmation') ? [
          'to_address' => Resolvers::resolve_field($post_data, 'email_confirmation_to_address'),
          'subject' => Resolvers::resolve_field($post_data, 'email_confirmation_subject')
        ] : null,
        'save_entries' => Resolvers::resolve_boolean_field($post_data, 'save_entries'),
      ];
      $post_data['texts'] = [
        'confirmation_text' => Resolvers::resolve_field($post_data, 'confirmation_text'),
        'submit_text' => Resolvers::resolve_field($post_data, 'submit_text'),
        'required_text' => __('is required', 'akka-forms'),
        'invalid_text' => __('is required', 'akka-forms'),
        'form_error_text' => __('The form has errors', 'akka-forms'),
        'form_failed_text' => __('Your submission failed. Please try again.', 'akka-forms'),
      ];

      return $post_data;
    });
  }

  private static function post_type_args()
  {
    return [
      'label' => __('Forms', 'akka-forms'),
      'labels' => [
        'name' => __('Forms', 'akka-forms'),
        'singular_name' => __('Form', 'akka-forms'),
        'add_new' => __('Add New Form', 'akka-forms'),
        'add_new_item' => __('Add New Form', 'akka-forms'),
      ],
      'hierarchical' => true,
      'menu_icon' => 'dashicons-feedback',
      'supports' => ['title', 'editor', 'comments'],
      'public' => false,
      'has_archive' => false,
      'rewrite' => false,
    ];
  }

  private static function post_type_acf_field_groups()
  {
    $field_type_choices = [
      'text' => __('Text', 'akka-forms'),
      'email' => __('Email', 'akka-forms'),
      'ssn' => __('Ssn', 'akka-forms'),
      'textarea' => __('Text area', 'akka-forms'),
      'select' => __('Dropdown', 'akka-forms'),
      'checkbox' => __('Checkbox', 'akka-forms'),
    ];
    if (env('AZURE_STORAGE_PRIVATE_ACCOUNT')) {
      $field_type_choices['file'] = __('File', 'akka-forms');
    }
    return [
      [
        'key' => self::$post_type_slug . '_fields',
        'title' => __('Form fields', 'akka-forms'),
        'fields' => [
          [
            'label' => __('Fields', 'akka-forms'),
            'name' => 'form_fields',
            'type' => 'repeater',
            'layout' => 'block',
            'button_label' => 'Add field',
            'sub_fields' => [
              [
                'key' => 'field_form_fields_type',
                'label' => __('Field type', 'akka-forms'),
                'name' => 'type',
                'type' => 'radio',
                'choices' => $field_type_choices,
                'default_value' => 'text',
                'return_format' => 'value',
                'parent_repeater' => 'field_form_fields',
              ],
              [
                'key' => 'field_form_fields_placebolder',
                'label' => __('Placeholder text', 'akka-forms'),
                'name' => 'placeholder',
                'type' => 'text',
                'conditional_logic' => [
                  [
                    [
                      'field' => 'field_form_fields_type',
                      'operator' => '==',
                      'value' => 'select',
                    ],
                  ],
                ],
                'parent_repeater' => 'field_form_fields',
              ],
              [
                'key' => 'field_form_fields_choices',
                'label' => __('Choices', 'akka-forms'),
                'name' => 'choices',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add choice',
                'sub_fields' => [
                  [
                    'key' => 'field_form_fields_choices_label',
                    'label' => __('Label', 'akka-forms'),
                    'name' => 'text',
                    'type' => 'text',
                    'parent_repeater' => 'field_form_fields_choices',
                  ],
                  [
                    'key' => 'field_form_fields_choices_value',
                    'label' => __('Id', 'akka-forms'),
                    'name' => 'value',
                    'type' => 'unique_id',
                    'parent_repeater' => 'field_form_fields_choices',
                  ],
                ],
                'conditional_logic' => [
                  [
                    [
                      'field' => 'field_form_fields_type',
                      'operator' => '==',
                      'value' => 'select',
                    ],
                  ],
                ],
                'parent_repeater' => 'field_form_fields',
              ],
              [
                'key' => 'field_form_fields_label',
                'label' => __('Label', 'akka-forms'),
                'name' => 'label',
                'required' => '1',
                'type' => 'text',
                'parent_repeater' => 'field_form_fields',
              ],
              [
                'key' => 'field_form_fields_field_id',
                'label' => __('Id', 'akka-forms'),
                'name' => 'field_id',
                'type' => 'unique_id',
                'parent_repeater' => 'field_form_fields',
              ],
              [
                'key' => 'field_form_fields_required',
                'label' => __('Required', 'akka-forms'),
                'name' => 'required',
                'type' => 'true_false',
                'parent_repeater' => 'field_form_fields',
              ],
            ],
          ],
          [
            'label' => __('Require consent', 'akka-forms'),
            'name' => 'require_consent',
            'type' => 'true_false',
            'default_value' => 0
          ],
          [
            'label' => __('Consent text', 'akka-forms'),
            'name' => 'consent_label',
            'type' => 'wysiwyg',
            'media_upload' => 0,
            'toolbar' => 'basic',
            'conditional_logic' => [
              [
                [
                  'field' => 'field_require_consent',
                  'operator' => '==',
                  'value' => '1',
                ],
              ],
            ],
          ],
          [
            'label' => __('Submit button text', 'akka-forms'),
            'name' => 'submit_text',
            'type' => 'text',
            'required' => '1',
          ],
        ],
        'position' => 'acf_after_title',
      ],
      [
        'key' => self::$post_type_slug . '_settings',
        'title' => __('Form settings', 'akka-forms'),
        'fields' => [
          [
            'label' => __('Confirmation message', 'akka-forms'),
            'name' => 'confirmation_text',
            'type' => 'wysiwyg',
            'media_upload' => 0,
            'toolbar' => 'basic',
            'required' => '1',
          ],
          [
            'label' => __('Email confirmation', 'akka-forms'),
            'name' => 'email_confirmation',
            'type' => 'true_false',
          ],
          [
            'label' => __('Email confirmation to address', 'akka-forms'),
            'name' => 'email_confirmation_to_address',
            'type' => 'text',
            'required' => '1',
            'conditional_logic' => [
              [
                [
                  'field' => 'field_email_confirmation',
                  'operator' => '==',
                  'value' => '1',
                ],
              ],
            ],
          ],
          [
            'label' => __('Email confirmation subject', 'akka-forms'),
            'name' => 'email_confirmation_subject',
            'type' => 'text',
            'conditional_logic' => [
              [
                [
                  'field' => 'field_email_confirmation',
                  'operator' => '==',
                  'value' => '1',
                ],
              ],
            ],
          ],
          [
            'label' => __('Save form entries', 'akka-forms'),
            'name' => 'save_entries',
            'type' => 'true_false',
          ],
        ],
        'position' => 'acf_after_title',
      ],
    ];
  }
}

Akka_headless_wp_forms_post_type::hooks();
