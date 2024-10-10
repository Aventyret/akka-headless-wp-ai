(function () {
  window.wp.plugins.registerPlugin('akka-ai-plugin', {
    render: applyWithDispatch()(applyWithSelect()(AiMetaPanel))
  });

  const enabledPostTypes = window.akkaAi?.postTypes || ['post', 'page'];

  function AiMetaPanel({ postType, postId, metaFields, setMetaFields }) {
    console.log({ metaFields });

    const [showSettings, setShowSettings] = window.React.useState(
      !!metaFields.akka_ai?.prompt || !!metaFields.akka_ai?.length
    );
    const [loading, setLoading] = window.React.useState(false);

    function setAkkaAiField(field, value) {
      console.log({ field, value });
      setMetaFields({ akka_ai: { ...(metaFields.akka_ai || {}), [field]: value } });
    }

    if (!enabledPostTypes.includes(postType)) return null;
    const el = window.wp.element.createElement;
    return el(
      wp.editPost.PluginDocumentSettingPanel,
      {
        title: 'AI-verktyg'
      },
      el(
        wp.components.PanelRow,
        { className: 'akka-ai-row' },
        el(wp.components.TextareaControl, {
          value: metaFields.akka_ai?.description || '',
          label: 'AI-genererad beskrivning',
          id: 'akka-ai-description',
          onChange: (value) => setAkkaAiField('description', value)
        })
      ),
      el(
        wp.components.PanelRow,
        {},
        el(wp.components.CheckboxControl, {
          label: 'Visa inställningar',
          checked: showSettings,
          onChange: setShowSettings
        })
      ),
      showSettings
        ? el(
            wp.components.PanelRow,
            { className: 'akka-ai-row' },
            el(wp.components.TextControl, {
              value: metaFields.akka_ai?.length || '',
              placeholder: 160,
              label: 'Tecken-längd på beskrivning',
              id: 'akka-ai-length',
              onChange: (value) => setAkkaAiField('length', value)
            })
          )
        : null,
      showSettings
        ? el(
            wp.components.PanelRow,
            { className: 'akka-ai-row' },
            el(wp.components.TextareaControl, {
              value: metaFields.akka_ai?.prompt || '',
              label: 'Extra prompt',
              id: 'akka-ai-prompt',
              onChange: (value) => setAkkaAiField('prompt', value)
            })
          )
        : null,
      el(
        wp.components.PanelRow,
        {},
        el(
          wp.components.Button,
          {
            variant: 'secondary',
            disabled: loading,
            onClick: () => generateDescription(postId, metaFields, setAkkaAiField, setLoading)
          },
          'Generera beskrivning'
        )
      ),
      el(
        wp.components.PanelRow,
        {},
        el(wp.components.CheckboxControl, {
          label: 'Använd beskrivningen',
          checked: !!metaFields.akka_ai?.use_description,
          onChange: (checked) => setAkkaAiField('use_description', checked)
        })
      )
    );
  }

  function applyWithSelect() {
    return window.wp.data.withSelect((select) => {
      return {
        metaFields: select('core/editor').getEditedPostAttribute('meta'),
        postType: select('core/editor').getCurrentPostType(),
        postId: select('core/editor').getCurrentPostId()
      };
    });
  }

  function applyWithDispatch() {
    return window.wp.data.withDispatch((dispatch) => {
      return {
        setMetaFields(newValue) {
          dispatch('core/editor').editPost({ meta: newValue });
        }
      };
    });
  }

  function generateDescription(postId, metaFields, setAkkaAiField, setLoading) {
    setLoading(true);
    let endpoint = `headless/v1/ai/meda_description/${postId}`;
    if (metaFields.akka_ai?.length) {
      endpoint += `?length=${parseInt(metaFields.akka_ai?.length, 10)}`;
    }
    if (metaFields.akka_ai?.prompt) {
      endpoint += `${endpoint.includes('?') ? '&' : '?'}prompt=${encodeURI(metaFields.akka_ai?.prompt)}`;
    }
    window.wp
      .apiFetch({ path: endpoint })
      .then((res) => {
        if (res.content) {
          setAkkaAiField('description', res.content);
        }
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }

  function saveDescription() {}
})();
