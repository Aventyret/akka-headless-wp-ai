(function () {
  window.wp.plugins.registerPlugin('akka-ai-plugin', {
    render: applyWithDispatch()(applyWithSelect()(AiMetaPanel))
  });

  function AiMetaPanel({ postType, postId, metaFields, setMetaFields }) {
    const [showSettings, setShowSettings] = window.React.useState(
      !!metaFields.akka_ai_prompt || !!metaFields.akka_ai_length
    );
    const [loading, setLoading] = window.React.useState(false);

    if ('post' !== postType) return null;
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
          value: metaFields.akka_ai_description || '',
          label: 'AI-genererad beskrivning',
          id: 'akka-ai-description',
          onChange: (value) => setMetaFields({ akka_ai_description: value })
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
              value: metaFields.akka_ai_length || '',
              placeholder: 160,
              label: 'Tecken-längd på beskrivning',
              id: 'akka-ai-length',
              onChange: (value) => setMetaFields({ akka_ai_length: value })
            })
          )
        : null,
      showSettings
        ? el(
            wp.components.PanelRow,
            { className: 'akka-ai-row' },
            el(wp.components.TextareaControl, {
              value: metaFields.akka_ai_prompt || '',
              label: 'Extra prompt',
              id: 'akka-ai-prompt',
              onChange: (value) => setMetaFields({ akka_ai_prompt: value })
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
            onClick: () => generateDescription(postId, metaFields, setMetaFields, setLoading)
          },
          'Generera beskrivning'
        )
      ),
      el(
        wp.components.PanelRow,
        {},
        el(wp.components.CheckboxControl, {
          label: 'Använd beskrivningen',
          checked: metaFields.akka_ai_use_description === '1',
          onChange: (checked) => setMetaFields({ akka_ai_use_description: checked ? '1' : '' })
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

  function generateDescription(postId, metaFields, setMetaFields, setLoading) {
    setLoading(true);
    let endpoint = `headless/v1/ai/meda_description/${postId}`;
    if (metaFields.akka_ai_length) {
      endpoint += `?length=${parseInt(metaFields.akka_ai_length, 10)}`;
    }
    if (metaFields.akka_ai_prompt) {
      endpoint += `${endpoint.includes('?') ? '&' : '?'}prompt=${encodeURI(metaFields.akka_ai_prompt)}`;
    }
    window.wp
      .apiFetch({ path: endpoint })
      .then((res) => {
        if (res.content) {
          setMetaFields({ akka_ai_description: res.content });
        }
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }

  function saveDescription() {}
})();
