<script>
import Editor from '@tinymce/tinymce-vue';
export default {
  name: 'Editor',
  components: {
    editors: Editor
  },
  setup: () => {},
  data: function () {
    return {
      apiKey: 'ay5gn5b3m1zht9vgul3ptc1h8buzmhwe87g69qy2lvixd1jo',
      initConfig: {
        height: 160,
        placeholder: 'メッセージ入力',
        content_style: `
	  .mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before {
		  color: #B7C3CB;
	  }
	`,
        menubar: false,
        toolbar_mode: 'scrolling',
        contextmenu: false,
        plugins: 'image link lists advlist',
        toolbar:
          'fontselect | fontsizeselect bold italic underline strikethrough | \
          forecolor backcolor | alignleft aligncenter alignright \
          alignjustify | numlist bullist',
        font_formats:
          'Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats'
      }
    };
  }
};
</script>

<template>
  <editors :api-key="apiKey" :init="initConfig" />
</template>

<style lang="scss">
div.tox-statusbar {
  border-top: unset !important;
  display: none !important;
}
div.tox-statusbar__text-container {
  display: none !important;
}
div.tox {
  border-radius: 5px;
}
div.tox-notifications-container {
  display: none !important;
}
div.tox-tinymce {
  height: 317px !important;
  border: none !important;
  background: none !important;
}
div.tox-editor-header {
  margin-bottom: 8px !important;
  background: #ffffff !important;
  border: 1px solid #e3e3e3 !important;
  border-radius: 4px !important;
}
div.tox-toolbar__group {
  border-right: none !important;
}
div.tox-toolbar--scrolling {
  &::-webkit-scrollbar {
    height: 7px;
  }
}
div.tox-sidebar-wrap {
  background: #ffffff !important;
  border: 1px solid #e3e3e3 !important;
  box-sizing: border-box !important;
  border-radius: 4px !important;
}
div.tox-tinymce-aux {
  z-index: 3000 !important;
}
</style>
