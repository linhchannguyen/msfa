/* eslint-disable no-undef */
import _ from 'lodash';
import moment from 'moment';
import Messages from '@/utils/messages';
import PerfectScrollbar from 'perfect-scrollbar';
import { TaxRate, roles } from '@/utils/constants';
import { getCookie, paramZ05S04Default } from '@/utils/constants';
import { mapGetters } from 'vuex';
import avatarDefault from './../assets/template/images/icon_user01.svg';
import { Device } from '@/utils/common-function.js';

export default {
  data() {
    return {
      isSubmit: false,
      responseErrors: {},
      isShowPopup: false,
      userLogin: '',
      permission: [],
      popupChild: null,
      showModalParentBody:
        '.button-add, .create-ppt, .list-title >.tlt , .interviewHeade > ul > li > .btn-link--custom, .facility-form > ul > li > .btn-radius, .form-icon > .icon, .btn-signUp, .btnInfo, .create-icon, .addition-label > a, .btn-custom, .bodyContainer-title .btn-radius, .facility-head-btn .btn-radius,  .addition-label, .event-click, .btn-filter, .btnCreate, .async-modal',
      showModalChildBody: [
        '.form-icon > .icon',
        '.btn-primary',
        '.approvalEdit-addition > .btn',
        '.contentSetting-body > .title > .btn-radius',
        '.LectureSeriesSelection-btn > .btn-primary',
        '.formMessage-btn > .btn2',
        '.confirmBtn > .btn-outline-primary'
      ]
    };
  },
  computed: {
    ...mapGetters({
      isLoginProxy: 'auth/isLoginProxy',
      itemIndex: 'router/getItemIndex',
      currentLocation: 'router/toPath'
    }),
    isInvalid() {
      let errors = {};
      _.forEach(this.validation, (rules, field) => {
        _.forEach(rules, (val) => {
          if (!val && !errors[`${field}`]) errors[`${field}`] = true;
        });
      });
      return !_.isEmpty(errors);
    },

    currentUser() {
      return this.$store.state.auth.currentUser;
    }
  },
  methods: {
    getCurrentUser() {
      // let isLoginProxy = JSON.parse(localStorage.getItem('isLoginProxy'));
      // const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
      // const currentUser = JSON.parse(localStorage.getItem('currentUser'));

      // return isLoginProxy ? currentUserProxy : currentUser;

      return this.$store.state.auth.currentUser;
    },
    componentHistory() {
      const componentChild = this.$route.matched[0].children;
      const path = window.history.state.back && window.history.state.back.length > 0 ? window.history.state.back.split('?')[0] : window.history.state.back;
      const component = componentChild.find((s) => s.path === path);
      return component ? component : { name: 'Z02_S01_Home' };
    },
    back() {
      // cancel event
      this.$router.go(-1);
      this.$emit('changeMode', { name: 'slide-left', type: 'back', type_screen: 'parent_children' });
    },
    decodeData(sample) {
      let obj = sample;
      if (typeof sample === 'string') {
        try {
          // eslint-disable-next-line no-undef
          let buffer = Buffer.from(sample, 'base64').toString('utf8');
          obj = JSON.parse(buffer);
        } catch (err) {
          return;
        }
      }
      return obj;
    },
    enCodeData(obj) {
      // eslint-disable-next-line no-undef
      let strData = Buffer.from(JSON.stringify(obj), 'utf8').toString('base64');
      return strData;
    },
    getScreen(name) {
      const screen = this.decodeData(localStorage.getItem('screen'));
      if (screen && screen.length > 0) {
        const index = screen.findIndex((s) => s.screen_name === name);
        return screen[index];
      }
      return;
    },
    _route(name = {}) {
      const routes = this.decodeData(localStorage.getItem('$_D'));
      let route;
      if (routes) {
        const nameScreen = name ? name : this.$['type']['name'];
        route = routes.find((s) => s.name === nameScreen);
        return route;
      }
    },
    checkValidate() {
      this.isSubmit = true;
      if (this.isInvalid) {
        // this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return false;
      }
      return true;
    },
    responseValidate(errors) {
      this.responseErrors = {};
      _.forEach(errors, (val, key) => {
        this.responseErrors[`${key}`] = _.first(val);
      });
      if (!_.isEmpty(this.responseErrors)) this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
    },
    showError(err) {
      const { data, message } = err.response.data;
      if (err.response.status === 400) this.responseValidate(data);
      else this.$notify({ message: message, customClass: 'error' });
    },
    resetData() {
      this.isSubmit = false;
      this.responseErrors = {};
    },
    generateID() {
      let dt = new Date().getTime();
      const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = (dt + Math.random() * 16) % 16 | 0;
        dt = Math.floor(dt / 16);
        return (c === 'x' ? r : (r & 0x3) | 0x8).toString(16);
      });
      return uuid;
    },
    getMessage(messageID, ...params) {
      return messageID ? Messages[messageID](...params) : 'error';
    },
    isValid(dateString) {
      return moment(new Date(dateString)).isValid();
    },
    formatFullDateCustom(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('ja-JP', options); // return ex: 2019年4月1日
    },
    formatFullDateTime(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('YYYY/M/D H:mm:ss') : null;
    },
    formatFullDateTimePattern(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('YYYY-M-D H:mm:ss') : null;
    },
    // formatFullDate(dateString) {
    //   if (dateString) {
    //     return this.isValid(dateString) ? moment(new Date(dateString)).format('YYYY/M/D') : null;
    //   } else {
    //     return null;
    //   }
    // },
    formatFullDate(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('YYYY/M/D') : null;
    },
    formatFullDate2(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('YYYY-M-D') : null;
    },
    formatFullDate3(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }
      return this.isValid(date) ? moment(new Date(date)).format('YYYY-MM-DD') : null;
    },
    formatYearMonth(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }
      return this.isValid(date) ? moment(new Date(date)).format('YYYY/M') : null;
    },
    formatMonthDate(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('M/D') : null;
    },
    formatFullTime(timeString) {
      let date = timeString;
      let isTimeValid = new Date(timeString) instanceof Date && !isNaN(new Date(timeString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (timeString.length > 10) {
          date = this.convertDateSf(timeString, true);
        } else {
          date = this.convertDateSf(timeString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('H:mm:ss') : null;
    },
    tomorrow() {
      const today = new Date();
      today.setDate(today.getDate());
      return moment(new Date(today)).format('YYYY/MM/DD');
    },
    formatTimeHourMinute(timeString) {
      let date = timeString;
      let isTimeValid = new Date(timeString) instanceof Date && !isNaN(new Date(timeString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (timeString.length > 10) {
          date = this.convertDateSf(timeString, true);
        } else {
          date = this.convertDateSf(timeString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('H:mm') : null;
    },
    getFullDate() {
      return moment().format('YYYY/M/D');
    },
    formatMonth(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('M') : null;
    },
    formatDate(dateString) {
      let date = dateString;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());
      if (!date) return null;

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return this.isValid(date) ? moment(new Date(date)).format('D') : null;
    },
    downloadFile(data, fileName = `downloadfile_${moment().format('YYYY-M-D')}`, type = 'text/csv;charset=utf-8;') {
      if (!data) return;
      const blob = new Blob([data], { type });
      if (navigator.msSaveBlob) {
        // IE 10+
        navigator.msSaveBlob(blob, fileName);
      } else {
        const link = document.createElement('a');
        if (link.download !== undefined) {
          const url = URL.createObjectURL(blob);
          link.setAttribute('href', url);
          link.setAttribute('download', fileName);
          link.style.visibility = 'hidden';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        }
      }
    },
    closeNoti(e) {
      this.$nextTick(async () => {
        const data = document.getElementsByClassName('el-notification');
        if (data) {
          if (e === 1) {
            const notification = document.getElementsByClassName('el-notification');
            if (notification.length >= 2) {
              document.getElementsByClassName('el-notification')[0].remove();
            }
          } else {
            document.getElementsByClassName('el-notification')[0]?.remove();
          }
        }
      });
    },
    getDay(dateString) {
      let options = {
        0: '日',
        1: '月',
        2: '火',
        3: '水',
        4: '木',
        5: '金',
        6: '土'
      };
      let date = dateString;
      if (!dateString) return null;
      let isTimeValid = new Date(dateString) instanceof Date && !isNaN(new Date(dateString).valueOf());

      if (!isTimeValid) {
        if (dateString.length > 10) {
          date = this.convertDateSf(dateString, true);
        } else {
          date = this.convertDateSf(dateString);
        }
      }

      return options[new Date(date).getDay()];
    },

    convertDateSf(dateString, isTime) {
      if (!dateString) {
        return;
      }

      let dateInput = dateString;
      let isSlash = dateString?.includes('/') ? true : false;
      let isSub = dateString?.includes('T') ? true : false;
      let timeInput = '';
      let indexS = dateString.indexOf(isSub ? 'T' : ' ');

      if (isTime) {
        dateInput = dateString.slice(0, indexS);
        timeInput = dateString.slice(indexS + 1, dateString.length);
      }

      let [year, month, date] = dateInput.split(isSlash ? '/' : '-');
      month = ('0' + month).slice(-2);
      date = ('0' + date).slice(-2);

      let dateResult = year + '-' + month + '-' + date;
      let dateTimeResult = dateResult + 'T' + timeInput;

      return isTime ? dateTimeResult : dateResult;
    },

    getRoleName(role) {
      return roles[role];
    },

    avataDefault() {
      return avatarDefault;
    },

    currDevice() {
      return Device();
    },

    //hidden
    hasHiddenElement(device, element, hidden) {
      if (hidden[device][0] === '*') {
        return true;
      }
      if (hidden[device].includes(element)) return true;
      else return false;
    },
    //permisions
    hasScreenPermission(action, element, permissions) {
      if (permissions[action][0] === '*') {
        return true;
      }
      if (typeof permissions[action][element] !== 'undefined') return true;
      else return false;
    },
    JapaneseTilde() {
      return ' ～ ';
    },
    js_pscroll(wheel_speed) {
      let elements = document.querySelectorAll('.scrollbar');
      elements.forEach((el) => {
        let ps = new PerfectScrollbar(el, {
          wheelSpeed: wheel_speed ? wheel_speed : 0.1
        });
        window.addEventListener('resize', function () {
          ps.update();
        });
      });
    },
    changeTrueClassHeader() {
      this.emitter.emit('change-class-header', {
        changeClass: true
      });
    },
    changeFalseClassHeader() {
      this.emitter.emit('change-class-header', {
        changeClass: false
      });
    },
    changeTabI01() {
      this.emitter.emit('change-AllTabs', {
        changeTabs: true
      });
    },
    getTaxRate() {
      return TaxRate;
    },
    getTimeInterval(date) {
      let newDate = new Date(moment(date).format('YYYY-MM-DDTHH:mm:ss'));
      let seconds = (Date.now() - newDate) / 1000;
      let unit = '秒';
      let direction = '前';
      if (seconds < 0) {
        seconds = -seconds;
        direction = '今から';
      }
      let value = seconds;
      if (seconds >= 31536000) {
        value = Math.floor(seconds / 31536000);
        unit = '年';
      } else if (seconds >= 86400) {
        value = Math.floor(seconds / 86400);
        unit = '日';
      } else if (seconds >= 3600) {
        value = Math.floor(seconds / 3600);
        unit = '時間';
      } else if (seconds >= 60) {
        value = Math.floor(seconds / 60);
        unit = '分';
      } else if (seconds < 60) {
        value = 1;
        unit = '分';
        direction = '以内';
      }
      return (value ? value : 0) + unit + direction;
    },
    //change brg BU5_MSFA-1001
    getClassChangeBRG(id, removeEl, isActive) {
      if (isActive || isActive === undefined) {
        if (typeof id !== 'string' && id?.length > 0) {
          id.forEach((element) => {
            const className = 'className--';
            const trBackGround = document.getElementById(`${className}${element}`);
            if (removeEl) {
              trBackGround?.classList.remove('back-ground-active');
              trBackGround?.classList.add('back-ground-leave');
              setTimeout(() => {
                trBackGround?.classList.remove('back-ground-leave');
              }, 2500);
            } else {
              trBackGround?.classList.add('back-ground-active');
            }
          });
        } else {
          const className = 'className--';
          const trBackGround = document.getElementById(`${className}${id}`);
          if (removeEl) {
            trBackGround?.classList.remove('back-ground-active');
            trBackGround?.classList.add('back-ground-leave');
            setTimeout(() => {
              trBackGround?.classList.remove('back-ground-leave');
            }, 2500);
          } else {
            trBackGround?.classList.add('back-ground-active');
          }
        }
      }
    },
    getClassChangeBRG2(id, removeEl, isActive) {
      if (isActive || isActive === undefined) {
        if (typeof id !== 'string' && id?.length > 0) {
          id.forEach((element) => {
            const className = 'className--';
            const trBackGround = document.getElementById(`${className}${element}`);
            if (removeEl) {
              trBackGround?.classList.remove('back-ground-active');
              trBackGround?.classList.add('back-ground-leave');
              setTimeout(() => {
                trBackGround?.classList.remove('back-ground-leave');
              }, 1300);
            } else {
              trBackGround?.classList.add('back-ground-active');
            }
          });
        } else {
          const className = 'className--';
          const trBackGround = document.getElementById(`${className}${id}`);
          if (removeEl) {
            trBackGround?.classList.remove('back-ground-active');
            trBackGround?.classList.add('back-ground-leave');
            setTimeout(() => {
              trBackGround?.classList.remove('back-ground-leave');
            }, 1300);
          } else {
            trBackGround?.classList.add('back-ground-active');
          }
        }
      }
    },

    // params modal
    paramZ05S04Default() {
      return paramZ05S04Default;
    },

    // Notify popup
    notifyModal({ title = '', message = '', type = 'success', duration = 3500, classParent, idNodeNotify }) {
      if (!classParent || !idNodeNotify) return;
      const main = document.getElementById(idNodeNotify);
      if (main) {
        if (document.getElementsByClassName('notify-custom').length >= 1) {
          document.getElementsByClassName('notify-custom')[0].remove();
        }
        const notify = document.createElement('div');
        // Auto remove notify
        // const autoRemoveId = setTimeout(function () {
        //   main?.removeChild(notify);
        // }, duration + 1000);

        // Remove notify when clicked
        notify.onclick = function (e) {
          if (e.target.closest('.notify-custom__close')) {
            main?.removeChild(notify);
            // clearTimeout(autoRemoveId);
          }
        };
        const delay = (duration / 1000).toFixed(2);
        const cssTextNotify = `
          position: absolute;
          top: -15px;
          right: -15px;
          z-index: 999999;
        `;

        const cssTextNotifyNonePd = `
          position: absolute;
          top: 10px;
          right: 15px;
          z-index: 999999;
        `;

        let classPdr = [
          'modal-body-Z05S05',
          'modal-body-A01S02-SettingColor',
          'modal-body-D01S06',
          'modal-body-A01S03-InHouse',
          'modal-body-A01S03-Private',
          'modal-body-A02S02',
          'modal-body-F01S04',
          'modal-body-B01S02-PersonStock'
        ];

        document.querySelector('.' + classParent).style.cssText = 'position: relative;';
        main.style.cssText = classPdr.includes(classParent) ? cssTextNotifyNonePd : cssTextNotify;

        notify.classList.add('notify-custom', `notify-custom--${type}`);
        notify.style.animation = `slideInLeft-nottify ease .3s, fadeOut-nottify linear 1s ${delay}s forwards`;

        notify.innerHTML = `
          <div class="notify-custom__body">
            <h3 class="notify-custom__title">${title}</h3>
            <p class="notify-custom__msg">${message}</p>
          </div>
          <div class="notify-custom__close el-icon-close"></div>
        `;
        main.appendChild(notify);
      }
    },
    transitionAfter(parentClass, child) {
      if (!parentClass) return;
      if (!child) return;

      let parent = document.querySelector(parentClass);
      let listElmt = parent.querySelectorAll(child);

      if (!parent.classList.contains('transform')) {
        parent.classList.add('transform');
      }

      parent.setAttribute('style', `--dir:${listElmt.length}; --offset:0`);

      listElmt.forEach((elmt, index) => {
        elmt.addEventListener('click', () => {
          parent.style.cssText = `--dir:${listElmt.length}; --offset: ${index}`;
        });
      });
    },

    reloadAction(fn, condition) {
      return async (...params) => {
        if (condition === 'reload') localStorage.setItem('reload', 1);

        return await fn(...params);
      };
    },
    async goToIndex() {
      let { index } = this.itemIndex;
      if (index !== -1 && this.currentLocation === window.location.pathname) {
        let item;
        let loop = true;
        while (loop) {
          await new Promise((resolve) => setTimeout(resolve, 50));
          item = document.querySelector(`[scroll-attr='${index}']`);
          if (item) {
            loop = false;
            break;
          }
        }
        if (item) item?.scrollIntoView();
        else return;
      }
    },

    convertToHalf(val) {
      // console.log(val);
      // e.value = e.value.normalize('NFKC');
      return val.replace(/[！-～]/g, (halfwidthChar) => {
        return String.fromCharCode(halfwidthChar.charCodeAt(0) - 0xfee0);
      });
    },

    convertToFull(val) {
      return val.replace(/[!-~]/g, (fullwidthChar) => {
        return String.fromCharCode(fullwidthChar.charCodeAt(0) + 0xfee0);
      });
    },
    setScrollScreen(nameScreen, val) {
      let objScrollTopScreen = JSON.parse(localStorage.getItem('ScrollTopScreen'));
      const obj = {
        ...objScrollTopScreen,
        [nameScreen]: val
      };
      localStorage.setItem('ScrollTopScreen', JSON.stringify(obj));
    },
    removeScrollScreen() {
      localStorage.removeItem('ScrollTopScreen');
    },
    setCurrentPageScreen(nameScreen, page) {
      let objCurrentPageScreen = JSON.parse(localStorage.getItem('CurrentPageScreen'));
      const obj = {
        ...objCurrentPageScreen,
        [nameScreen]: page
      };
      localStorage.setItem('CurrentPageScreen', JSON.stringify(obj));
    }
  },
  mounted() {
    // var el = document.querySelector('.dropdown-filter');
    // var noClick = true;
    // if (el) {
    //   el.addEventListener('mouseover', () => {
    //     noClick = true;
    //   });
    //   el.addEventListener('mouseout', () => {
    //     noClick = false;
    //   });
    // }
    // window
    //   .$('body')
    //   .on('click', function () {
    //     if (noClick === false) {
    //       el[0]?.classList.remove('show');
    //     }
    //   })
    //   .on('click', '.btn-filter', function (e) {
    //     var el = document.getElementsByClassName('dropdown-filter');

    //     el[0]?.classList.add('show');

    //     e.stopPropagation();
    //   })
    //   .on('click', '.btn-close-filter, .btn-outline-primary--cancel, .btn-filter-submit', function (e) {
    //     var el = document.getElementsByClassName('dropdown-filter');
    //     if (el[0]) el[0].classList.remove('show');
    //     e.stopPropagation();
    //   })
    //   .on('click', '.dropdown-filter ', function (e) {
    //     e.stopPropagation();
    //   });

    //
    this.userLogin = JSON.parse(localStorage.getItem('currentUser'));
    this.permission = this.$store.state.auth.policyRole;

    let { name } = this.$route;
    window.onbeforeunload = function () {
      /**
       * pagesVisited: Danh sách page đã truy cập
       * f_index: vị trí đầu tiên của page hiện tại trong danh sách pages đã truy cập
       * khi thoát, tắt tab thì xóa page hiện tại ra khỏi danh sách pages đã truy cập
       * kiểm tra danh sách page đã truy cập có data hay không?
       * - có: set lại giá trị những page đã truy cập
       * - không: xóa biến danh sách page đã truy cập
       */
      if (!['Z01_S01_Login', 'Z01_S02_DeveloperLogin'].includes(name)) {
        const pagesVisited = JSON.parse(localStorage.getItem('pagesVisited')) || [];
        let f_index = pagesVisited.indexOf(name);
        pagesVisited.splice(f_index, 1);
        if (pagesVisited.length > 0) {
          localStorage.setItem('pagesVisited', JSON.stringify(pagesVisited));
        } else {
          localStorage.removeItem('pagesVisited');
        }
        sessionStorage.setItem('isRefreshPage', true);
      }
    };
  },
  updated() {
    this.$nextTick(async () => {
      const data = document.getElementsByClassName('el-notification');
      if (data.length >= 2) {
        document.getElementsByClassName('el-notification')[0].remove();
      }
      // Carefull Loop infinity
      let accessToken = getCookie('accessToken');
      let originalToken = getCookie('originalToken');
      let currentUserProxy = await JSON.parse(localStorage?.getItem('currentUserProxy'));
      let params = {
        currentUser: currentUserProxy,
        access_token_proxy: accessToken,
        originalToken: originalToken
      };
      if (originalToken && accessToken) {
        // Store still not update
        if (!this.isLoginProxy) {
          await this.$store.dispatch('auth/afterLoginProxyAction', params);
          if (this.$route.name === 'Z02_S01_Home') {
            this.emitter.emit('home-reload');
          } else {
            this.$router.push({ name: 'Z02_S01_Home' });
          }
        }
      } else if (!originalToken && accessToken) {
        // Store still not update
        if (this.isLoginProxy) {
          await this.$store.dispatch('auth/clearProxy');

          this.emitter.emit('home-reload');
        }
      }
      return;
    });
  },
  async beforeUnmount() {
    let $_C = await localStorage.getItem('$_C');
    let data = {};
    let clg = '';
    if ($_C) {
      let decode = await this.decodeData($_C);
      if (decode && decode !== '{') {
        data = JSON.parse(decode);
        clg = data?.enable_console_log;
      }
    }
    await new Promise((resolve) => setTimeout(resolve, 2000));

    if (!clg || clg === 'off') {
      console.clear();
    }
  }
};
