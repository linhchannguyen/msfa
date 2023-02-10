<template>
  <div v-loading="loadingCalendar" v-load-calendar="true" v-load-f5="true" class="wrapContent">
    <div class="wrapCalendar">
      <div class="calendarContent scrollbar">
        <div class="colTabs">
          <ul class="nav">
            <li @click="showModalFilter(true)">
              <a>
                <span class="visible-filter" :class="showFilter ? 'hide' : 'show'">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line.svg')" alt="" />
                </span>
              </a>
            </li>
            <li @click="showModalFilter(false, true)" @touchstart="showModalFilter(false, true)">
              <a :class="showFilter ? 'active' : ''"><span>表示設定</span></a>
            </li>
          </ul>
          <!-- Tab panes -->
        </div>
        <div class="colPanel">
          <div class="panelRow">
            <div class="calendar-col01" :hidden="!showFilter">
              <div class="filter-container">
                <div class="setting-filter">
                  <div class="settingsCheck setting-save-filter-item">
                    <ul>
                      <li v-for="item in lstScheduleType" :key="item.definition_value" :class="item.definition_value == '50' ? 'settingsCheck-last' : ''">
                        <label class="custom-control custom-checkbox custom-control--bordGreen">
                          <input
                            class="custom-control-input save-filter-item"
                            type="checkbox"
                            :checked="getTypeChecked(item.definition_value)"
                            @click="setTypeChecked(item.definition_value)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">{{ item.definition_label }}</span>
                        </label>
                      </li>
                    </ul>
                  </div>
                  <div class="btnUser">
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius btn-hg32"
                      @click="openModalZ05S01"
                      @touchstart="openModalZ05S01()"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user06.svg')" alt="" /> </span
                      >ユーザ選択
                    </button>
                  </div>
                </div>
                <div ref="listUserRef" class="group-user scrollbar">
                  <div class="settingUser">
                    <ul>
                      <div v-for="user in listUser" :key="user.watch_user_cd" :hidden="listUser.length === 0">
                        <li :class="user.display_flag == 1 ? 'active' : ''">
                          <div class="settingUser-info log-icon" title_log="表示ユーザ" @click="changeStatus(user)">
                            <span class="settingUser-box" :style="{ background: user.display_color }"></span>
                            <span class="settingUser-name">{{ user.user_name }}</span>
                          </div>
                          <button
                            class="btn btn--icon icon-setting-user"
                            @click="openModalColorSettingA01_S02(user)"
                            @touchstart="openModalColorSettingA01_S02(user)"
                          >
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_setting-nav.svg')" alt="" />
                          </button>
                        </li>
                      </div>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="calendar-col02" :class="showFilter ? '' : 'custom-w'">
              <FullCalendar ref="fullCalendar" class="fullcalendar-custom calendar-custom-header" :options="calendarOptions">
                <template #eventContent="arg">
                  <b>{{ arg.timeText }}</b>
                  <br v-if="arg.timeText && arg.view.type !== 'dayGridMonth'" />
                  <i>{{ arg.event.title }}</i>
                </template>
              </FullCalendar>
            </div>
          </div>
        </div>
      </div>
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`${modalConfig.customClass} handle-close`"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          ref="modalChild"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="reloadAction(onResultModal, 'reload')($event)"
          @onDeleteUser="onDeleteUser"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
import moment from 'moment';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import A01_S02_CalendarService from '@/api/A01/A01_S02_CalendarService';
import Z04_S01_Services from '@/api/Z04/Z04_S01_AccountSettingServices';

import { markRaw } from 'vue';
import B01_S02_BriefingSessionInput from '@/views/B01/B01_S02_BriefingSessionInput/B01_S02_BriefingSessionInput';
import C01_S02_LectureInput from '@/views/C01/C01_S02_LectureInput/C01_S02_LectureInput';
import Z05_S01_Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import A01_S02_SettingColor from './A01_S02_SettingColor.vue';
import A01_S03_InterviewDetails from '@/views/A01/A01_S03_InterviewDetails/A01_S03_InterviewDetails.vue';
import A01_S03_ModalInHouseSchedule from '@/views/A01/A01_S03_InterviewDetails/A01_S03_ModalInHouseSchedule.vue';
import A01_S03_ModalPrivate from '@/views/A01/A01_S03_InterviewDetails/A01_S03_ModalPrivate.vue';
import PerfectScrollbar from '@/assets/template/js/perfect-scrollbar.min.js';

let listEvent = [];
export default {
  name: 'A01_S02_Calendar',
  components: {
    FullCalendar,
    B01_S02_BriefingSessionInput,
    C01_S02_LectureInput,
    Z05_S01_Organization,
    A01_S02_SettingColor,
    A01_S03_InterviewDetails,
    A01_S03_ModalInHouseSchedule,
    A01_S03_ModalPrivate
  },
  props: {
    checkLoading: [Boolean]
  },
  data: function () {
    return {
      loadingCalendar: true,
      showFilter: false,

      // calendar
      timeZone: 'Asia/Jakarta',
      calendarOptions: {
        locale: 'ja',

        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        headerToolbar: {
          left: 'today prev,next',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        customButtons: {
          dayGridMonth: {
            text: '月',
            click: this.getCalendarMonth
          },
          timeGridWeek: {
            text: '週',
            click: this.getCalendarWeek
          },
          timeGridDay: {
            text: '日',
            click: this.getCalendarDay
          }
        },

        views: {
          timeGridWeek: {
            titleFormat: (date) => {
              let start = date.start;
              let end = date.end;
              let result = start.year + '年' + (start.month + 1) + '月' + ' ～ ' + end.year + '年' + (end.month + 1) + '月';
              if (start.year === end.year && start.month === end.month) {
                result = start.year + '年' + (start.month + 1) + '月';
              }
              return result;
            }
          }
        },

        buttonText: {
          today: '今日',
          month: '月',
          week: '週',
          day: '日'
        },

        allDayContent: '終日',
        moreLinkContent: (arg) => {
          let moreText = '他' + arg.num + '件';
          return moreText;
        },

        dayHeaderContent: (day) => {
          if (day.view.type === 'timeGridWeek') {
            let index = day.text.indexOf('/');
            let dayText = day.text.substring(index + 1, day.text.length);
            return dayText;
          }

          if (day.view.type === 'timeGridDay') {
            let text = moment(day.date).format('D') + `(${this.getDay(day.date)})`;
            return text;
          }
        },

        dayCellContent: (day) => {
          if (day.view.type === 'dayGridMonth') {
            let dayCellText = day.dayNumberText.substring(0, day.dayNumberText.length - 1);
            return dayCellText;
          }
        },

        dayPopoverFormat: (day) => {
          let dateDisplay = moment(day.date).format('YYYY年M月D日');
          return dateDisplay;
        },
        slotLabelFormat: [{ hour: 'numeric', minute: '2-digit', hour12: false }],
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false,
          meridiem: 'narrow'
        },

        // event change date
        datesSet: (dateTime) => {
          let dateStart = '';
          let dateEnd = '';
          if (dateTime.view.type === 'timeGridDay') {
            dateStart = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
            dateEnd = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
            this.setHoverToolBar('日');
            if (!this.isReload) {
              if (this.startDate !== dateStart && this.endDate !== dateEnd) {
                this.startDate = dateStart;
                this.endDate = dateEnd;
                this.modeView = 'timeGridDay';
                this.getScheduleByDate();
              }
            }
          }
          if (dateTime.view.type === 'timeGridWeek') {
            if (!this.isReload) {
              dateStart = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
              this.setHoverToolBar('週');
              this.modeView = 'timeGridWeek';
              this.getWeekByDate(dateStart);
            }
          }
          if (dateTime.view.type === 'dayGridMonth') {
            dateStart = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
            dateEnd = moment(new Date(dateTime.end)).format('YYYY/MM/DD');
            this.setHoverToolBar('月');
            if (!this.isReload) {
              if (this.startDate !== dateStart && this.endDate !== dateEnd) {
                this.startDate = dateStart;
                this.endDate = dateEnd;
                this.modeView = 'dayGridMonth';
                this.getScheduleByDate();
              }
            }
          }
        },

        initialView: 'timeGridWeek',
        scrollTime: '08:30:00',

        events: listEvent,
        editable: false,
        selectable: false,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        firstDay: 1,

        eventClick: this.handleEventClick,
        eventContent: function () {},
        handleWindowResize: true,
        windowResizeDelay: 200
      },

      currentEvents: [],

      eventData: {},
      // end calendar

      paramsZ05S01: {
        userFlag: 1,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
      },

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalInfo: false,
        isShowModalConfirm: false,
        component: null,
        customClass: 'custom-dialog',
        props: {},
        width: '',
        destroyOnClose: true,
        closeOnClickMark: false
      },

      lstScheduleType: [],
      lstTypeChecked: [],
      userInfo: {},
      listUser: [],

      lstColor: [],

      startDate: '',
      endDate: '',
      isReload: true,
      modeView: 'timeGridWeek'
    };
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'カレンダー',
      icon: 'icon_interview-color.svg',
      isShowBack: false
    });

    let currentUser = this.getCurrentUser();
    this.userInfo = {
      user_cd: currentUser.user_cd,
      user_name: currentUser.user_name
    };

    let classEl = ['btn', 'btn-outline-primary', 'btn-outline-primary--cancel'];
    let btnEl = ['next', 'prev', 'today'];

    for (const i in btnEl) {
      let item = btnEl[i];
      let classBtn = document.getElementsByClassName(`fc-${item}-button`)[0];
      classBtn?.setAttribute('id', `button${item}_id`);

      let classBtnId = document.getElementById(`button${item}_id`);
      for (const j in classEl) {
        let el = classEl[j];
        classBtnId?.classList.add(el);
      }
    }

    const isLoadingComponent = localStorage.getItem('isLoadingComponent');
    const start_date_active = localStorage.getItem('startActive');
    const end_date_active = localStorage.getItem('endActive');

    if (isLoadingComponent === 'true') {
      this.isReload = true;
      this.getScreenData(null, true);
      localStorage.setItem('mdView', this.modeView);
    } else {
      this.modeView = localStorage.getItem('mdView') || 'timeGridWeek';
      this.showFilter = JSON.parse(localStorage.getItem('showFilterA1S2')) || false;

      if (start_date_active) {
        this.startDate = moment(new Date(start_date_active)).format('YYYY-MM-DD');
        this.endDate = moment(new Date(end_date_active)).format('YYYY-MM-DD');
        let date = moment(new Date(start_date_active)).add(15, 'day').format('YYYY-MM-DD');

        let calendarApi = this.$refs.fullCalendar.getApi();
        calendarApi.changeView(this.modeView);

        if (this.modeView === 'dayGridMonth') {
          calendarApi.gotoDate(date);
        } else {
          calendarApi.gotoDate(this.startDate);
        }

        this.getScreenData(this.startDate, true);
      } else {
        this.getScreenData(null, true);
      }
    }

    this.resizeWindow();

    // Setup scroll for callendar
    let ele = document.getElementsByClassName('fc-scroller-liquid-absolute')[0];
    let ps = new PerfectScrollbar(ele, {
      useBothWheelAxes: false,
      suppressScrollX: true
    });
    window.addEventListener('resize', () => {
      ps.update();
    });
  },

  methods: {
    resizeWindow() {
      let calendarApi = this.$refs.fullCalendar.getApi();
      setTimeout(() => {
        calendarApi.updateSize();
      }, 1000);
    },
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },

    getCalendarMonth() {
      let calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.changeView('dayGridMonth');
      this.modeView = 'dayGridMonth';
      localStorage.setItem('mdView', this.modeView);
    },

    getCalendarWeek() {
      let calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.changeView('timeGridWeek');
      this.modeView = 'timeGridWeek';
      localStorage.setItem('mdView', this.modeView);
    },

    getCalendarDay() {
      let calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.changeView('timeGridDay');
      this.modeView = 'timeGridDay';
      localStorage.setItem('mdView', this.modeView);
    },

    getWeekByDate(date, notLoading) {
      let curr = new Date(date);
      let week = [];

      for (let i = 1; i <= 7; i++) {
        let first = curr.getDate() - curr.getDay() + i;
        let day = moment(new Date(curr.setDate(first))).format('YYYY/MM/DD');
        week.push(day);
      }

      if (!notLoading) {
        if (this.startDate !== week[0] || this.endDate !== week[6]) {
          this.startDate = week[0];
          this.endDate = week[6];
          this.getScheduleByDate(notLoading);
        }
      } else {
        this.startDate = week[0];
        this.endDate = week[6];
        this.getScheduleByDate(notLoading);
      }
    },

    setHoverToolBar(alt) {
      document.getElementsByClassName('fc-dayGridMonth-button')[0].setAttribute('title_log', '月');
      document.getElementsByClassName('fc-timeGridWeek-button')[0].setAttribute('title_log', '週');
      document.getElementsByClassName('fc-timeGridDay-button')[0].setAttribute('title_log', '日');

      document.getElementsByClassName('fc-prev-button')[0].setAttribute('title', `前へ ${alt}`);
      document.getElementsByClassName('fc-next-button')[0].setAttribute('title', `次へ ${alt}`);
      document.getElementsByClassName('fc-today-button')[0].setAttribute('title', `今 ${alt}`);
      document.getElementsByClassName('fc-prev-button')[0].setAttribute('title_log', `前へ ${alt}`);
      document.getElementsByClassName('fc-next-button')[0].setAttribute('title_log', `次へ ${alt}`);
      document.getElementsByClassName('fc-today-button')[0].setAttribute('title_log', `今 ${alt}`);
    },

    getScreenData(startDate) {
      A01_S02_CalendarService.getScreenData()
        .then((res) => {
          this.lstScheduleType = res.data.data.variable_definition;
          this.lstColor = res.data.data.watch_user_color;
          const isLoadingComponent = localStorage.getItem('isLoadingComponent');
          if (isLoadingComponent === 'true') {
            this.lstTypeChecked = res.data.data.variable_definition.map((x) => x.definition_value);
          }

          this.getListUserByUserLogin(true, startDate);
        })
        .catch((err) => {
          this.loadingCalendar = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          localStorage.setItem('isLoadingComponent', false);
        });
    },

    getListUserByUserLogin(isLoadDefault, startDate) {
      this.loadingCalendar = isLoadDefault ? true : false;
      A01_S02_CalendarService.getListUserByUserLogin()
        .then((res) => {
          this.listUser = res.data.data;
          let date = startDate ? moment(new Date(startDate)).format('YYYY/MM/DD') : moment(new Date()).format('YYYY/MM/DD');

          if (this.modeView === 'timeGridWeek') {
            this.getWeekByDate(date, true);
          } else {
            this.getScheduleByDate(isLoadDefault);
          }
        })
        .catch((err) => {
          this.loadingCalendar = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    getScheduleByDate(notLoading) {
      this.loadingCalendar = notLoading ? false : true;
      // eslint-disable-next-line eqeqeq
      let users = this.listUser.length > 0 ? this.listUser.filter((x) => x.display_flag == 1) : [];
      let params = {
        startDate: this.startDate,
        endDate: this.endDate,
        user_cd: users.length > 0 ? users.map((x) => x.watch_user_cd) : '',
        type: this.lstTypeChecked.length > 0 ? this.lstTypeChecked : ''
      };
      A01_S02_CalendarService.getScheduleByDate(params)
        .then((res) => {
          let data = res.data.data;
          this.listEvent = [];
          data.map((x) => {
            this.listEvent.push({
              id: x.schedule_id,
              title: x.title,
              start: this.convertDateTime(x.start_time),
              end: this.convertDateTime(x.end_time),
              start_time: x.start_time,
              end_time: x.end_time,
              start_date: x.start_date,
              allDay: x.all_day_flag === 1 ? true : false,
              color: x.display_color ? x.display_color : '#FFFF',
              schedule_type: x.schedule_type,
              user_cd: x.user_cd,
              display_color_cd: x.display_color_cd,
              className: 'log-icon'
            });
          });

          this.calendarOptions.events = this.listEvent;

          localStorage.setItem('startActive', this.startDate);
          localStorage.setItem('endActive', this.endDate);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.loadingCalendar = false;
          this.isReload = false;
          if (this.listEvent?.length > 0) {
            let classEvent = document.querySelectorAll('.fc-event');
            classEvent?.forEach((item) => {
              let schedule_type = item.fcSeg.eventRange.def.extendedProps.schedule_type;
              let title = 'イベント日付';
              switch (schedule_type) {
                case '10':
                  title = 'イベントボックス-面談';

                  break;
                case '20':
                  title = 'イベントボックス-説明会';

                  break;
                case '30':
                  title = 'イベントボックス-講演会';

                  break;
                case '40':
                  title = 'イベントボックス-社内予定';

                  break;
                case '50':
                  title = 'イベントボックス-プライベート';

                  break;
                default:
                  break;
              }

              item?.setAttribute('title_log', title);
            });
          }
        });
    },

    convertDateTime(dateTime) {
      if (dateTime.length >= 19) {
        let date = dateTime?.slice(0, 10)?.replaceAll('/', '-');
        let time = dateTime?.slice(11, 19);
        return date + 'T' + time;
      }
    },

    handleEventClick(eventInfo) {
      let type = eventInfo.event.extendedProps.schedule_type;
      if (type === '10') {
        this.redirectToInterViewDetail(eventInfo);
      }

      if (type === '40') {
        this.openModalA01S03InHouseSchedule(eventInfo);
      }

      if (type === '50') {
        this.openModalA01S03ModalPrivate(eventInfo);
      }

      if (type === '20') {
        this.redirectToB01S02(eventInfo);
      }

      if (type === '30') {
        this.redirectToC01S02(eventInfo);
      }
    },

    getTypeChecked(type) {
      let checked = this.lstTypeChecked.some((x) => x === type);
      return checked;
    },

    setTypeChecked(type) {
      let checked = !this.getTypeChecked(type);
      if (checked) {
        this.lstTypeChecked.push(type);
      } else {
        this.lstTypeChecked = this.lstTypeChecked.filter((x) => x !== type);
      }
      this.getScheduleByDate();
    },

    getUserInfo() {
      Z04_S01_Services.getUserInfoService()
        .then((res) => {
          this.userInfo = {
            user_cd: res.data.data.user_cd,
            user_name: res.data.data.user_name
          };
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },

    // form Filter
    showModalFilter(allowClose, isOpen) {
      if (allowClose) {
        this.showFilter = !this.showFilter;
        if (this.$refs.listUserRef) {
          this.$refs.listUserRef.scrollTop = 0;
        }
      } else {
        if (isOpen && !this.showFilter) {
          if (this.$refs.listUserRef) {
            this.$refs.listUserRef.scrollTop = 0;
          }
        }
        this.showFilter = true;
      }

      localStorage.setItem('showFilterA1S2', this.showFilter);
    },

    getTime(eventInfo) {
      let startTime = new Date(new Date(eventInfo.extendedProps.start_time).setHours(0, 0, 0, 0));
      let endTime = new Date(new Date(eventInfo.extendedProps.end_time).setHours(23, 59, 59, 59));

      return {
        startTime: startTime,
        endTime: endTime
      };
    },

    // Modal A01-S03: inhouse
    openModalA01S03InHouseSchedule(eventInfo) {
      let info = eventInfo.event;

      const userLogin = this.$store.state.auth.currentUser.user_cd;
      let edit_flg = userLogin === info.extendedProps.user_cd;

      let props = {
        scheduleId: parseInt(info.id),
        date: info.extendedProps.start_date,
        startTime: info.allDay ? this.getTime(info).startTime : info.extendedProps.start_time,
        endTime: info.allDay ? this.getTime(info).endTime : info.extendedProps.end_time,
        all_day_flag: info.allDay,
        editFlagProps: edit_flg,
        isEditFlag: 1
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: '社内予定',
        isShowModal: true,
        component: markRaw(A01_S03_ModalInHouseSchedule),
        width: '44rem',
        customClass: 'custom-dialog modal-fixed',
        props: { ...props },
        destroyOnClose: true
      };
    },

    onResultModalA01S03InHouseSchedule(e) {
      if (e) {
        this.getScheduleByDate();
      }
    },
    // Modal A01-S03: private
    openModalA01S03ModalPrivate(eventInfo) {
      let info = eventInfo.event;
      const userLogin = this.$store.state.auth.currentUser.user_cd;
      let edit_flg = userLogin === info.extendedProps.user_cd;
      let props = {
        scheduleId: parseInt(info.id),
        date: info.extendedProps.start_date,
        startTime: info.allDay ? this.getTime(info).startTime : info.extendedProps.start_time,
        endTime: info.allDay ? this.getTime(info).endTime : info.extendedProps.end_time,
        all_day_flag: info.allDay,
        editFlagProps: edit_flg,
        isEditFlag: 1
      };

      this.modalConfig = {
        ...this.modalConfig,
        title: 'プライベート',
        isShowModal: true,
        component: markRaw(A01_S03_ModalPrivate),
        width: '44rem',
        customClass: 'custom-dialog modal-fixed',
        props: { ...props },
        destroyOnClose: true
      };
    },

    onResultModalA01S03ModalPrivate(e) {
      if (e) {
        this.getScheduleByDate();
      }
    },

    // MH A01-S03: interview
    redirectToInterViewDetail(eventInfo) {
      let info = eventInfo.event;
      let schedule_id = parseInt(info.id);
      this.$router.push({ name: 'A01_S03_InterviewDetails', params: { schedule_id } });
    },

    // Modal C01-S02
    redirectToC01S02(eventInfo) {
      let info = eventInfo.event;
      let schedule_id = parseInt(info.id);
      this.$router.push({ name: 'C01_S02_LectureInput', params: { schedule_id } });
    },

    // Modal B01-S02
    redirectToB01S02(eventInfo) {
      let info = eventInfo.event;
      let schedule_id = parseInt(info.id);
      this.$router.push({ name: 'B01_S02_BriefingSessionInput', params: { schedule_id } });
    },

    // Modal Z05-S01
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05_S01_Organization),
        props: { ...this.paramsZ05S01 },
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (e) {
        let lstUser = [];
        e.userSelected.forEach((element) => {
          if (!this.listUser.some((x) => x.watch_user_cd === element.user_cd)) {
            lstUser.push(element.user_cd);
          }
        });

        let colors = this.checkColorOther();
        let colorsChecks = this.checkColorOther();
        if (colorsChecks.length < lstUser.length) {
          for (let index = 0; index < lstUser.length - colorsChecks.length; index++) {
            colors.push(this.lstColor[0].display_color_cd);
          }
        } else {
          if (colorsChecks.length > lstUser.length) {
            colors.splice(lstUser.length, colorsChecks.length);
          }
        }

        if (lstUser.length > 0) {
          this.changeColorUser(colors, lstUser, 1);
        }
        if (this.$refs.listUserRef) {
          this.$refs.listUserRef.scrollTop = 0;
        }
      }
    },

    checkColorOther() {
      let colors = [];
      for (const item of this.lstColor) {
        if (!this.listUser.some((x) => x.display_color_cd === item.display_color_cd)) {
          colors.push(item.display_color_cd);
        }
      }
      return colors;
    },

    // Modal Color setting
    openModalColorSettingA01_S02(user) {
      this.modalConfig = {
        ...this.modalConfig,
        title: user.user_name,
        isShowModal: true,
        component: markRaw(A01_S02_SettingColor),
        width: '28rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { user: user, lstColors: this.lstColor },
        destroyOnClose: true
      };
    },

    onResultModalColorSettingA01_S02(e) {
      if (e) {
        this.changeColorUser([e.color.display_color_cd], [e.user.watch_user_cd], e.user.display_flag);
      }
    },

    changeColorUser(lst_display_color_cd, lstUser, display_flag) {
      let params = {
        user_cd: lstUser,
        display_color_cd: lst_display_color_cd,
        display_flag: display_flag
      };
      A01_S02_CalendarService.changeColorUser(params)
        .then(() => {
          this.getListUserByUserLogin();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    changeStatus(user) {
      this.changeColorUser([user.display_color_cd], [user.watch_user_cd], user.display_flag == 1 ? 0 : 1);
    },

    onDeleteUser() {
      this.getListUserByUserLogin();
      this.onCloseModal();
      localStorage.setItem('reload', 1);
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'A01_S03_InHouseSchedule') {
          this.onResultModalA01S03InHouseSchedule(e);
        }
        if (e.screen === 'A01_S03_Private') {
          this.onResultModalA01S03ModalPrivate(e);
        }
        if (e.screen === 'Z05_S01') {
          this.onResultModalZ05S01(e);
        }
        if (e.screen === 'A01_S02_ColorSetting') {
          this.onResultModalColorSettingA01_S02(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog ' };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.wrapCalendar {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.headeCalendar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: 16px 24px 12px;
  .headeCalendar-year {
    width: calc(100% - 352px);
    padding-right: 12px;
    padding-left: 240px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    @media (max-width: $viewport_desktop) {
      padding-left: 0;
    }
    @media (max-width: $viewport_tablet) {
      width: 100%;
    }
    .btnToday {
      width: 74px;
      margin-right: 12px;
    }
    .btnSlide {
      width: 76px;
      display: flex;
      .btn {
        padding: 0;
        margin-right: 12px;
        width: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .calendarText {
      width: calc(100% - 162px);
      padding-left: 36px;
      color: #000;
      font-size: 1.5rem;
      font-weight: 700;
      @media (max-width: $viewport_desktop) {
        font-size: 1.25rem;
        padding-left: 14px;
      }
    }
  }
  .headeCalendar-tools {
    width: 352px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    @media (max-width: $viewport_tablet) {
      width: 100%;
      padding-top: 10px;
    }
    .btnInfo {
      .btn {
        margin-right: 14px;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .btnSelect {
      width: 120px;
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          width: 33.333333%;
          margin-left: -1px;
          &:first-of-type {
            .btn {
              border-radius: 4px 0 0 4px;
            }
          }
          &:last-child {
            .btn {
              border-radius: 0 4px 4px 0;
            }
          }
          .btn {
            width: 40px;
            height: 40px;
            padding: 0;
            border-radius: 0;
            &.active {
              position: relative;
            }
          }
        }
      }
    }
  }
}
/*** calendarContent ***/
.calendarContent {
  height: 100%;
  padding: 20px 24px 28px;
  display: flex;
  flex-wrap: wrap;
  .colTabs {
    width: 44px;
    height: 100%;
    margin-top: 80px;
    height: calc(100% - 80px);
    .nav {
      height: 100%;
      align-content: flex-start;
      li {
        height: 120px;
        padding: 7px 0;
        cursor: pointer;
        &:first-child {
          height: 60px;
          a {
            background: #fff;
            width: 44px;
          }
        }
        .visible-filter {
          &.show .svg {
            transform: rotate(-90deg);
            transition: transform 1s;
          }
          &.hide .svg {
            transform: rotate(90deg);
            transition: transform 1s;
          }
        }

        a {
          display: block;
          background: #f7f7f7;
          border-radius: 10px 0 0 10px;
          height: 100%;
          box-shadow: 0 0 5px #b7c3cb;
          color: #b7c3cb;
          font-size: 1rem;
          line-height: 18px;
          position: relative;
          &::after,
          &::before {
            position: absolute;
            right: 0;
            content: '';
            display: block;
            width: 11px;
            height: 11px;
          }
          &::after {
            background: url(~@/assets/template/images/bgTabs.png) no-repeat;
            bottom: -10px;
          }
          &::before {
            background: url(~@/assets/template/images/bgTabs01.png) no-repeat;
            top: -10px;
          }
          &.active {
            color: #448add;
            background: #fff;
            font-weight: bold;
            z-index: 3;
            &::after {
              background: url(~@/assets/template/images/bgTabs02.png) no-repeat;
              bottom: -10px;
            }
            &::before {
              background: url(~@/assets/template/images/bgTabs03.png) no-repeat;
              top: -10px;
            }
          }
          &:hover {
            background: #fff;
            color: #448add;
            font-weight: bold;
            text-decoration: none;
            &::after {
              background: url(~@/assets/template/images/bgTabs02.png) no-repeat;
              bottom: -10px;
            }
            &::before {
              background: url(~@/assets/template/images/bgTabs03.png) no-repeat;
              top: -10px;
            }
          }
          span {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 14px;
            height: 100%;
            position: relative;
            &::after {
              position: absolute;
              top: 0;
              right: -6px;
              content: '';
              height: 100%;
              width: 6px;
              background: #fff;
            }
          }
        }
      }
    }
  }
  .colPanel {
    height: 100%;
    width: calc(100% - 44px);
    position: relative;
    .panelRow {
      height: 100%;
      display: flex;
      flex-wrap: wrap;
      box-shadow: -11px 13px 7px -10px #b7c3cbc2;
    }

    .calendar-col01 {
      width: 240px;
      height: calc(100% - 81px);
      background: #ffff;
      box-shadow: 7px -1px 10px 0px #b7c3cba3;
      margin-top: 81px;
      z-index: 2;

      .filter-container {
        height: 100%;
        padding: 26px 16px;
        box-shadow: 6px 0px 8px #b7c3cb63;
        .title {
          font-size: 1.125rem;
          font-weight: 700;
        }
        .setting-filter {
          position: -webkit-sticky;
          position: sticky;
          top: 0;
          z-index: 4;
          height: 170px;
          background: #ffffff;
        }
        .settingsCheck {
          ul {
            display: flex;
            flex-wrap: wrap;
            margin-left: -12px;
            li {
              width: 50%;
              padding-left: 12px;
              margin-top: 6px;
              &.settingsCheck-last {
                width: 65%;
              }
              .custom-control,
              .custom-control-description {
                width: 100%;
              }
            }
          }
        }
        .btnUser {
          margin-top: 24px;
          .btn {
            width: 100%;
          }
        }
        .group-user {
          margin: -10px;
          padding: 10px;
          padding-top: 10px;
          margin-top: 10px;
          max-height: calc(100% - 170px);
        }
        .settingUser {
          box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
          border-radius: 4px;

          ul {
            border-radius: 4px;
            overflow: hidden;
            li {
              background: #f2f2f2;
              color: #b7c3cb;
              border-bottom: 1px solid #cad4db;
              display: flex;
              flex-wrap: wrap;
              padding-right: 12px;
              &.active {
                background: #fcfcfc;
                color: #5f6b73;
              }

              .settingUser-info {
                width: calc(100% - 32px);
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                cursor: pointer;
                .settingUser-box {
                  width: 20px;
                  display: block;
                  height: 100%;
                }
                .settingUser-name {
                  width: calc(100% - 20px);
                  padding: 8px;
                  font-size: 1rem;
                  font-weight: 700;
                  text-overflow: ellipsis;
                  overflow: hidden;
                  white-space: nowrap;
                }
              }
              .btn {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 8px 0;
                .svg {
                  width: 14px;
                  path {
                    fill: #448add;
                  }
                }
              }
            }
          }
        }
      }
    }
    .calendar-col02 {
      background: #fff;
      width: calc(100% - 240px);
      height: 100%;
      z-index: 1;
    }
    .custom-w {
      width: 100%;
    }
  }
}

.full-calendar-custom {
  display: flex;
  min-height: 100%;
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
  max-width: 1400px;
  margin: 0 auto;
}

.full-calendar-custom-sidebar {
  width: 200px;
  line-height: 1.5;
  background: #eaf9ff;
  border-right: 1px solid #d3e2e8;
}

.full-calendar-custom-sidebar-section {
  padding: 10px;
}

.full-calendar-custom-main {
  flex-grow: 1;
}

.fc .fc-view-harness {
  height: fit-content;
}

/*** modal ***/
.modal-colorSetting {
  padding: 0;
  .headSetting {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 32px;
    background: #fff;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px 10px 0 0;
    .headSetting-title {
      display: flex;
      align-items: center;
      font-size: 1.5rem;
      font-weight: 700;
      .headSetting-item {
        min-width: 20px;
        margin: -3px 8px 0 0;
        img {
          width: 20px;
        }
      }
    }
  }
  .lst-colorSetting {
    padding: 32px;
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -32px;
      margin-top: -20px;
      li {
        padding-left: 32px;
        width: 20%;
        margin-top: 20px;
        .itemBox {
          width: 42px;
          height: 42px;
          border-radius: 50%;
          border: 1px solid #fff;
          box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
          display: block;
          cursor: pointer;
        }
      }
    }
  }
}
</style>
