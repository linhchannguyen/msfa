// import { encodeData } from '@/api/base64_api';
const logs = {};
let log_data = [];

logs.install = function install(_Vue) {
  _Vue.directive('log', {
    created() {
      const app = document.querySelector('#app');
      app.addEventListener('mouseover', () => {
        log_data = [];
        const event = queryEvent(app);
        if (event) {
          for (let i = 0; i < event.length; i++) {
            ['mouseover'].forEach((eve) => {
              event[i].addEventListener(eve, () => {
                const isLogIcon = [...event[i].classList].includes('log-icon');
                const outText = event[i].innerText ? event[i].innerText : event[i].getAttribute('title_log');
                const length = event[i]?.closest('a')?.href.split('/');
                const menuUrl = length ? length[length.length - 1] : false;
                const link_url_fe = menuUrl ? `/${menuUrl}` : window.location.pathname;
                const header = {
                  info_button_fe: isLogIcon ? event[i] && event[i].getAttribute('title_log') : outText,
                  link_url_fe
                };
                // eslint-disable-next-line no-undef
                let headerEncode = Buffer.from(JSON.stringify(header), 'utf8').toString('base64');
                if (header) {
                  app.setAttribute('log', headerEncode);
                  localStorage.setItem('header', JSON.stringify(header));
                }
              });
            });
          }
        }
      });
    }
  });
};

export const events = ['aside .nav-sub a', '.item-navSub > a > .item', 'button:not(.confirmBtn > button)', 'icon', '.log-icon'];
const queryEvent = (el) => [...el.querySelectorAll(events.join())];

export { logs, log_data };
