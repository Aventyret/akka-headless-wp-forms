(() => {
  // node_modules/@aventyret/akka-wp-editor/dist/client/index.mjs
  function debounceAsync(func, waitMs = 200) {
    let timer = null;
    return (...args) => {
      clearTimeout(timer);
      return new Promise((resolve) => {
        timer = setTimeout(() => resolve(func(...args)), waitMs);
      });
    };
  }
  var { useState, useEffect, useCallback } = wp.element;
  var { InnerBlocks } = window.wp.blockEditor;
  var { PanelBody, TextControl, Spinner } = window.wp.components;
  var { withSelect } = window.wp.data;
  var apiFetch = window.wp.apiFetch;
  function AkkaServerSideRender({ postId, block, clientId, attributes }) {
    const [renderedHtml, setRenderedHtml] = useState(null);
    const renderBlock = useCallback(
      debounceAsync((attributes2) => {
        return apiFetch({
          path: "headless/v1/editor/block",
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          data: { postId, blockType: block, attributes: attributes2 }
        });
      }),
      [postId, block, apiFetch]
    );
    useEffect(() => {
      const blockResponse = renderBlock(attributes).then((blockResponse2) => {
        if (!blockResponse2.rendered) {
          console.error("Missing html");
          setRenderedHtml("Something went wrong...");
        } else {
          setRenderedHtml(blockResponse2.rendered);
        }
      }).catch((error) => {
        console.error(error);
        setRenderedHtml("Something went wrong...");
      });
    }, [attributes]);
    if (renderedHtml === null) {
      return /* @__PURE__ */ React.createElement(Spinner, null);
    }
    return /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement("div", { className: "wp-block-splx", dangerouslySetInnerHTML: { __html: renderedHtml || "" } }));
  }
  var AkkaServerSideRender_default = withSelect((select) => {
    const { getCurrentPostId } = select("core/editor");
    return {
      postId: getCurrentPostId()
    };
  })(AkkaServerSideRender);

  // src/blocks/form-block.js
  var { registerBlockType } = window.wp.blocks;
  var { __ } = window.wp.i18n;
  var { InspectorControls, useBlockProps } = window.wp.blockEditor;
  var { PanelBody: PanelBody2, SelectControl } = window.wp.components;
  var { useEffect: useEffect2, useState: useState2 } = window.wp.element;
  var apiFetch2 = window.wp.apiFetch;
  var blockId = "akka/form";
  function form_block_default() {
    registerBlockType(blockId, {
      title: __("Form", "akka-forms"),
      apiVersion: 2,
      icon: "feedback",
      category: "formId",
      attributes: {
        formId: { type: "number", default: 0 }
      },
      edit: (props) => {
        const blockProps = useBlockProps();
        const { attributes, setAttributes } = props;
        let { formId } = attributes;
        const [forms, setForms] = useState2([]);
        useEffect2(() => {
          const listForms = async () => {
            setForms(
              (await apiFetch2({ path: "/wp/v2/akka_form" })).map((post) => ({
                title: post.title.rendered,
                id: post.id
              }))
            );
          };
          listForms();
        }, []);
        console.log({ forms });
        return /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement("div", { ...blockProps }, /* @__PURE__ */ React.createElement(
          AkkaServerSideRender_default,
          {
            block: blockId,
            attributes: {
              ...attributes
            }
          }
        )), /* @__PURE__ */ React.createElement(InspectorControls, null, /* @__PURE__ */ React.createElement(PanelBody2, { title: __("Form", "akka-forms") }, /* @__PURE__ */ React.createElement(
          SelectControl,
          {
            label: __("Form", "akka-forms"),
            value: formId + '',
            options: [
              {
                value: "0",
                label: __("Select a form", "akka-forms")
              },
              ...forms.map((form) => ({ value: form.id + '', label: form.title }))
            ],
            onChange: (value) => setAttributes({ formId: parseInt(value, 10) })
          }
        ))));
      }
    });
  }

  // src/editor.js
  form_block_default();
})();
