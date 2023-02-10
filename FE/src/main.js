import { createApp } from 'vue';
import App from '@/App.vue';
import { router, routerPush } from '@/router';
import store from '@/store';

import mixins from '@/utils/mixins';
import { loadingCustom, loadCalendar, slideLoader, loadingContainer } from '@/utils/loading-common';
import { modalConfig } from '@/utils/modal-common';

import { saveFilter } from '@/utils/filter-data';
import { logs } from '@/utils/api-logs';

import ElementPlus from 'element-plus';
import { ElMessageBox } from 'element-plus';
import Locale from 'element-plus/lib/locale/lang/ja';
import PopupConfirm from '@/components/PopupConfirm';
import CurrencyInput from '@/components/CurrencyInput';
import TimePicker from '@/components/TimePicker';
import BaseComponents from '@/components';
import { FilterConfig } from '@/utils/close_filter';
import { markRaw } from 'vue';
import VCalendar from 'v-calendar';
import 'jquery/src/jquery.js';
import 'popper.js/dist/popper.min.js';
import 'bootstrap/dist/js/bootstrap.min.js';
import '@/assets/template/js/tagsinput.js';
import '@/assets/template/js/slick.js';

import { ColorPicker } from 'vue-color-kit';

// import Vue plugin
import VueSvgInlinePlugin from 'vue-svg-inline-plugin';
// import polyfills for IE if you want to support it
import 'vue-svg-inline-plugin/src/polyfills';

import mitt from 'mitt';
import moment from 'moment';
import _ from 'lodash';

const emitter = mitt();
window.store = store;
window.$ = window.jQuery = require('jquery');

const app = createApp(App);

BaseComponents.register(app);

app.mixin(mixins);
app.config.globalProperties.emitter = emitter;
app.config.globalProperties.markRaw = markRaw;
app.config.globalProperties.$moment = moment;
app.config.globalProperties.$_ = _;
app.config.globalProperties.$PopupConfirm = PopupConfirm;
app.config.globalProperties.$CurrencyInput = CurrencyInput;
app.config.globalProperties.$TimePicker = TimePicker;

// use Vue plugin without options
app.use(VueSvgInlinePlugin);
// use Vue plugin with options
app.use(VueSvgInlinePlugin, {
  attributes: {
    data: ['src'],
    remove: ['alt']
  }
});

app
  .use(store)
  .use(router)
  .use(ElementPlus, {
    locale: Locale
  })
  .mount('#app');
// router push
app.config.globalProperties.$router.push = routerPush;

// Use v-calendar & v-date-picker components
app.use(VCalendar, {
  componentPrefix: 'vc'
});

app.use(ColorPicker);
app.config.productionTip = false;
// set loadingm page
app.use(loadingCustom);
app.use(loadingContainer);
app.use(loadCalendar);
app.use(slideLoader);
app.use(saveFilter);
app.use(logs);
app.use(FilterConfig);
app.use(modalConfig);

document.addEventListener('contextmenu', (event) => event.preventDefault());

app.config.globalProperties.$msgboxConfirm = (options) => {
  ElMessageBox({
    closeOnClickModal: false,
    showCancelButton: true,
    confirmButtonText: 'はい',
    cancelButtonText: 'キャンセル',
    customClass: 'custom-message-box',
    type: 'warning',
    beforeClose: (action, instance, done) => {
      instance.confirmButtonLoading = true;
      instance.cancelButtonClass = 'el-button--disabled';

      if (action === 'confirm') {
        setTimeout(() => {
          instance.confirmButtonLoading = false;
          done();
        }, 500);
      } else {
        done();
      }
    },
    ...options
  });
};
