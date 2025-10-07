import { AkkaServerSideRender } from '@aventyret/akka-wp-editor';

const { registerBlockType } = window.wp.blocks;
const { __ } = window.wp.i18n;
const { InspectorControls, useBlockProps } = window.wp.blockEditor;
const { PanelBody, SelectControl } = window.wp.components;
const { useEffect, useState } = window.wp.element;
const apiFetch = window.wp.apiFetch;

const blockId = 'akka/form';

export default function () {
  registerBlockType(blockId, {
    title: __('Form', 'akka-forms'),
    apiVersion: 2,
    icon: 'feedback',
    category: 'formId',
    attributes: {
      formId: { type: 'number', default: 0 }
    },
    edit: (props) => {
      const blockProps = useBlockProps();
      const { attributes, setAttributes } = props;
      let { formId } = attributes;
      const [forms, setForms] = useState([]);

      useEffect(() => {
        const listForms = async () => {
          setForms(
            (await apiFetch({ path: '/wp/v2/akka_form' })).map((post) => ({
              title: post.title.rendered,
              id: post.id
            }))
          );
        };
        listForms();
      }, []);

      return (
        <>
          <div {...blockProps}>
            <AkkaServerSideRender
              block={blockId}
              attributes={{
                ...attributes
              }}
            />
          </div>
          <InspectorControls>
            <PanelBody title={__('Form', 'akka-forms')}>
              <SelectControl
                label={__('Form', 'akka-forms')}
                value={formId}
                options={[
                  {
                    value: '0',
                    label: __('Select a form', 'akka-forms')
                  },
                  ...forms.map((form) => ({ value: form.id, label: form.title }))
                ]}
                onChange={(value) => setAttributes({ formId: value })}
              />
            </PanelBody>
          </InspectorControls>
        </>
      );
    }
  });
}
