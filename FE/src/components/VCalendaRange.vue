<template>
  <div :class="['card-calendar']">
    <div class="card-calendar-box">
      <div class="card-calendar-box--switch">
        <span>{{ mode === 'monthly' ? datePicker.year + '年' : `${dateRange.year}年 ${dateRange.month}月` }}</span>
        <div v-if="mode === 'day'" class="btn-group">
          <button class="btn_pre" @click="preMonth()"><i class="el-icon-arrow-left"></i></button>
          <button class="btn_next" @click="nextMonth()"><i class="el-icon-arrow-right"></i></button>
        </div>
        <div v-else class="btn-group">
          <button @click="preYear()"><i class="el-icon-arrow-left"></i></button>
          <button @click="nextYear()"><i class="el-icon-arrow-right"></i></button>
        </div>
        <div class="switch-group">
          <button :class="['button-m', mode === 'monthly' ? 'active' : '']" @click="changeMode('monthly', 'singleDate1')">月</button>
          <button :class="['button-d', mode === 'day' ? 'active' : '']" @click="changeMode('day', 'singleDate1')">日</button>
        </div>
      </div>
      <div v-if="mode === 'monthly'" class="card-calendar-box--content">
        <ul>
          <li v-for="item of 12" :key="item" :class="['day-item-range1']" :value="item">
            <div :value="item" :class="[datePicker.month === item ? 'selected' : '']">{{ item }}月</div>
          </li>
        </ul>
        <!-- {{ datePicker }} -->
      </div>
      <div v-else class="card-calendar-box--content">
        <div class="date_picker">
          <v-date-picker
            ref="calendar"
            v-model="selectedDate"
            header-title="hidden"
            title-position="center"
            mode="range"
            class="custom-date"
            locale="ja-JP"
            is-range
            :attributes="[{ dates: new Date(), key: 'today', dot: true, order: 0 }]"
            :title="`${dateRange.start}-${dateRange.end}`"
            @dayclick="onDateRangeChange"
            @drag="drag"
          >
          </v-date-picker>
          <div class="footer-date">
            <div class="form-group">
              <div class="sprin1"></div>
              <div class="sprin2"></div>
              <div class="range-group">
                <div class="range-input-1">
                  <el-input
                    v-model="dateRange.start"
                    format="YYYY/M/D"
                    clearable
                    readonly
                    style="background: #ffffff; line-height: 35px"
                    class="form-control-input"
                  />
                </div>
                <div class="range-input-2">
                  <el-input
                    v-model="dateRange.end"
                    format="YYYY/M/D"
                    clearable
                    readonly
                    style="background: #ffffff; line-height: 35px"
                    class="form-control-input"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import moment from 'moment';
import { isNull } from 'lodash';
const decodeData = (sample) => {
  let obj = sample;
  if (typeof sample === 'string') {
    try {
      // eslint-disable-next-line no-undef
      let buffer = Buffer.from(sample, 'base64').toString('utf8');
      obj = JSON.parse(buffer);
    } catch (err) {
      // console.log(err);
    }
  }
  return obj;
};
export default {
  name: 'CalendarRange',
  props: {
    mode: {
      type: String,
      default: 'monthly',
      require: false
    },
    selectedStart: {
      type: Number,
      default: null,
      require: false
    },
    selectedEnd: {
      type: Number,
      default: null,
      require: false
    },
    year: {
      type: String,
      default: '',
      require: true
    },
    rangeDate: [Object || String]
  },
  emits: ['changeMode', 'click'],

  data(e) {
    const getScreen = (name) => {
      const screen = decodeData(localStorage.getItem('screen'));
      if (screen && screen.length > 0) {
        const index = screen.findIndex((s) => s.screen_name === name);
        return screen[index];
      }
      return;
    };
    const data = getScreen('H02_S06_PersonalDetailsTimeline');
    let range = e && e.rangeDate;
    const start = (data) => (data && data.length > 0 ? new Date(data) : new Date(new Date().setMonth(new Date().getUTCMonth() - 1)));
    const end = (data) => (data && data.length > 0 ? new Date(data) : new Date());

    if (data && data.screen_name === 'H02_S06_PersonalDetailsTimeline') {
      if (JSON.parse(localStorage.getItem('isRedirectH02_S01'))) {
        // Set default calendar
        range = e && e.rangeDate;
      } else {
        range = data ? { start: start(data.filter.start_datetime), end: end(data.filter.end_datetime) } : e && e.rangeDate;
      }
    }
    const s05 = getScreen('H01_S05_FacilityDetailsTimeline');
    if (s05 && s05.screen_name === 'H01_S05_FacilityDetailsTimeline') {
      range = s05 ? { start: start(s05.filter.start_datetime), end: end(s05.filter.end_datetime) } : e && e.rangeDate;
    }
    return {
      mode1: 'range',
      formats: {
        input: ['YYYY-M-D']
      },

      activeDate: '',
      datePicker: {
        year: new Date().getFullYear(),
        start_date_month: '',
        end_date_month: '',
        fullYearStartMonth: '',
        fullYearEndMonth: '',
        month: '',
        fullDate: '',
        start: '',
        end: ''
      },
      isRange1: true,
      submit: false,
      selectedDate: range || { start: new Date(), end: new Date() },
      activeMonth: '',
      dateRange: {
        month: new Date().getMonth() + 1,
        year: new Date().getFullYear(),
        start: range && range.start ? moment(range.start).format('YYYY/M/D') : '',
        end: range && range.end ? moment(range.end).format('YYYY/M/D') : ''
      }
    };
  },

  mounted() {
    // this.changeMode('day', 'singleDate1');

    this.excecDateRange('day');

    this.changeMode('day', 'singleDate1');
    if (this.dateRange.month < this.formatMonth(this.dateRange.end)) {
      this.nextMonth();
    }
  },
  methods: {
    checkCallApiModeDate(event) {
      const el = event.el;
      const isCall = el.attributes[5];
      return isCall ? true : false;
    },
    onDateRangeChange(e) {
      if (typeof this.selectedDate.start === 'string') {
        this.dateRange.month = e.month;
        this.dateRange.year = e.year;
      }
      if (!this.selectedDate.end) {
        this.dateRange.start = typeof this.selectedDate.start == 'string' ? '' : '';
        this.dateRange.end = !this.selectedDate.end || typeof this.selectedDate.end == 'function' ? '' : moment(this.selectedDate.end).format('YYYY/M/D');
      } else {
        this.dateRange.start = typeof this.selectedDate.start == 'string' ? '' : moment(this.selectedDate.start).format('YYYY/M/D');

        this.dateRange.end = !this.selectedDate.end || typeof this.selectedDate.end == 'function' ? '' : moment(this.selectedDate.end).format('YYYY/M/D');
        const isCallApi = this.checkCallApiModeDate(e);
        if (isCallApi) {
          this.$emit('click', {
            mode: 'day',
            startDate: this.dateRange.start,
            endDate: this.dateRange.end,
            status: 'success',
            callApi: true,
            rangeDate: [this.selectedDate.start, this.selectedDate.end],

            getAll: false
          });
        }
      }
    },
    drag() {
      this.selectedDate.start = '';
      this.selectedDate.end = isNull;
      this.selectedDate.date = isNull;
      this.dateRange.start = '';
      this.dateRange.end = '';
    },

    preMonth() {
      // eslint-disable-next-line eqeqeq
      if (this.dateRange.month == 1) {
        this.dateRange.month = 12;
        // eslint-disable-next-line vue/no-mutating-props
        this.dateRange.year--;
      } else {
        this.dateRange.month--;
      }
      const calendar = this.$refs.calendar;
      setTimeout(() => {
        calendar.move({ month: this.dateRange.month, year: this.dateRange.year });
      });
    },
    nextMonth() {
      // eslint-disable-next-line eqeqeq
      if (this.dateRange.month == 12) {
        this.dateRange.month = 1;
        // eslint-disable-next-line no-unused-vars
        this.dateRange.year++;
      } else {
        this.dateRange.month++;
      }
      const calendar = this.$refs.calendar;
      calendar.move({ month: this.dateRange.month, year: this.dateRange.year });
    },
    /** event */
    choseMonth(item) {
      this.month = item;
    },
    changeMode(mode, filed) {
      this.activeMonth = '';
      const singleDate = filed;
      // eslint-disable-next-line vue/no-mutating-props
      this.$emit('changeMode', mode);
      if (mode === 'monthly') {
        setTimeout(() => {
          this.excecDateRange(singleDate);
          this.datePicker.month = '';
        });
      } else {
        this.isRange1 = true;
        const data = this.getScreen('H02_S06_PersonalDetailsTimeline');
        const start = (data) => (data && data.length > 0 ? new Date(data) : new Date(new Date().setMonth(new Date().getUTCMonth() - 1)));
        const end = (data) => (data && data.length > 0 ? new Date(data) : new Date());
        let range = this.rangeDate;
        if (data && data.screen_name === 'H02_S06_PersonalDetailsTimeline') {
          if (JSON.parse(localStorage.getItem('isRedirectH02_S01'))) {
            // Set default calendar
            range = this.rangeDate;
          } else {
            range = data ? { start: start(data.filter.start_datetime), end: end(data.filter.end_datetime) } : this.rangeDate;
          }
        }
        const s05 = this.getScreen('H01_S05_FacilityDetailsTimeline');
        if (s05 && s05.screen_name === 'H01_S05_FacilityDetailsTimeline') {
          range = s05 ? { start: start(s05.filter.start_datetime), end: end(s05.filter.end_datetime) } : this.rangeDate;
        }
        this.selectedDate.start = range.start;
        this.selectedDate.end = range.end;
        this.dateRange.start = moment(this.selectedDate.start).format('YYYY/M/D');
        this.dateRange.end = moment(this.selectedDate.end).format('YYYY/M/D');

        this.dateRange.month = this.selectedDate.start.getMonth() + 1;
        this.dateRange.year = this.selectedDate.start.getFullYear();

        this.eventNextPrev();
        this.styleClassBorder();
      }
    },
    eventNextPrev() {
      // mô phổng next prev từ thư viện
      setTimeout(() => {
        const next = document.querySelector('.is-right');
        if (next)
          next.addEventListener('click', () => {
            this.nextMonth();
          });

        const pev = document.querySelector('.is-left');
        if (pev)
          pev.addEventListener('click', () => {
            this.preMonth();
          });
      });
    },
    styleClassBorder() {
      // đường viền bottom  của lịch
      setTimeout(() => {
        const className = document.getElementsByClassName('vc-pane-container');
        if (className) {
          const div = document.createElement('div');
          if (div) {
            div.classList.add('vc-arrows-container');
            div.classList.add('vc-border-top');
            className[0].appendChild(div);
          }
        }
      });
    },
    /** END Mode Day */
    setUpCalendar() {
      // const singleDate = mode;
      let ul = document.querySelector('.card-calendar-box--content ul');
      for (let i = 0; i < ul?.childElementCount; i++) {
        let li = ul.querySelectorAll('.day-item-range1 > div');
        li[i]?.addEventListener('click', (e) => {
          let target = e?.composedPath() || e?.composedPath;
          const index = target[1]._value;
          this.activeMonth = index;
          this.datePicker.month = index;
          this.dateRange.month = index;
          this.dateRange.year = this.datePicker.year;

          const selected = li && li[index - 1]?.children[0]?.classList[0];
          setTimeout(() => {
            if (selected === 'selected') {
              li[index - 1].children[0].classList.remove('selected');
              this.$emit('click', {
                mode: 'day',
                startDate: '',
                endDate: '',
                status: 'error',
                callApi: false,
                rangeDate: [null, null],

                getAll: true
              });
            } else {
              li && li[index - 1]?.children[0]?.classList.add('selected');
              const fisrtDate = moment(new Date(this.datePicker.year, index - 1, 1)).format('YYYY/M/D');
              const endDate = moment(new Date(this.datePicker.year, new Date(fisrtDate).getMonth() + 1, 0)).format('YYYY/M/D');
              if (index === this.datePicker.month) {
                this.$emit('click', {
                  mode: 'day',
                  startDate: fisrtDate,
                  endDate: endDate,
                  status: 'success',
                  callApi: true,
                  rangeDate: [moment(new Date(this.datePicker.year, index, 1)), moment(new Date(this.datePicker.year, index + 1, 0))],

                  getAll: false
                });
              }
            }
          });
        });
      }
    },
    getMonth() {
      this.datePicker.start_date_month = this.selectedStart < 10 ? `0${this.selectedStart}` : `${this.selectedStart}`;
      this.datePicker.end_date_month = this.selectedEnd < 10 ? `0${this.selectedEnd}` : `${this.selectedEnd}`;
    },
    getMonthRange() {
      if (this.selectedStart < this.selectedEnd) {
        let ul = document.querySelector('.card-calendar-box--content ul');
        if (ul) {
          for (let i = 0; i < ul?.childElementCount; i++) {
            let li = ul.querySelectorAll('.day-item-range1 > div');
            let div = ul.querySelectorAll('div');
            if (i === this.selectedStart) {
              li[i - 1]?.setAttribute('class', 'background-s highlight');
              div[i - 1]?.setAttribute('class', 'selected');
            } else if (i === this.selectedEnd) {
              li[i - 2]?.setAttribute('class', 'background-e highlight');
              div[i - 1]?.setAttribute('class', 'selected');
            }
            if (i > this.selectedStart && i < this.selectedEnd) {
              li[i - 2]?.setAttribute('class', 'day-item-range1 highlight');
            }
          }
        }
      }
    },
    preYear() {
      if (this.activeMonth) {
        // Setup active month in Date Picker
        this.datePicker.month = this.datePicker.year - 1 === this.dateRange.year ? this.dateRange.month : '';
      }

      this.activeDate = document.querySelectorAll('.day');
      this.datePicker.year--;
      this.datePicker.fullYearEndMonth = `${this.datePicker.year}/${this.datePicker.end_date_month}`;
      this.datePicker.fullYearStartMonth = `${this.datePicker.year}/${this.datePicker.start_date_month}`;
      // this.monthInYear();
    },
    nextYear() {
      if (this.activeMonth) {
        // Setup active month in Date Picker
        this.datePicker.month = this.datePicker.year + 1 === this.dateRange.year ? this.dateRange.month : '';
      }

      this.activeDate = document.querySelectorAll('.day');
      this.datePicker.year++;
      this.datePicker.fullYearEndMonth = `${this.datePicker.year}/${this.datePicker.end_date_month}`;
      this.datePicker.fullYearStartMonth = `${this.datePicker.year}/${this.datePicker.start_date_month}`;
      // this.monthInYear();
    },
    monthInYear() {
      const fisrtDate = moment(new Date(this.datePicker.year, this.activeMonth - 1, 1)).format('YYYY/M/D');
      const endDate = moment(new Date(this.datePicker.year, new Date(fisrtDate).getMonth() + 1, 0)).format('YYYY/M/D');
      if (this.activeMonth) {
        this.$emit('click', {
          mode: 'day',
          startDate: fisrtDate,
          endDate: endDate,
          status: 'success',
          callApi: true,
          rangeDate: [moment(new Date(this.datePicker.year, this.activeMonth, 1)), moment(new Date(this.datePicker.year, this.activeMonth + 1, 0))],

          getAll: false
        });
      }
    },
    // run;
    excecDateRange(mode) {
      const singleDate = mode;
      this.setUpCalendar(singleDate);
      this.getMonth();
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_tablet: 991px;
$viewport_desktop: 1366px;
.card-calendar {
  position: relative;
  background-color: #f7f7f7;
  width: 100%;
  min-height: 350px;
  &-box {
    width: 100%;
    height: 100%;
    position: absolute;
    &--switch {
      @media (max-width: $viewport_tablet) {
        display: inline-flex;
        justify-content: left;
        .btn-group {
          margin-left: 20px !important;
        }
        .switch-group {
          position: absolute;
          right: 0;
        }
      }
      display: flex;
      width: 100%;
      min-height: 50px;
      justify-content: space-between;
      align-items: center;
      span {
        font-size: 18px;
        color: #4d97f1;
        font-weight: bold;
        width: 120px;
      }
      .btn-group {
        button {
          color: #4d97f1;
          width: 40px;
          height: 40px;
          background-color: #fcfcfc;
          border-radius: 95px 95px 95px 95px;
          padding: 5px;
          margin-right: 5px;
          box-shadow: 0px 3px 3px #b7c3cb;

          i {
            font-weight: bold;
          }
        }
        button:hover {
          background: #eef6ff !important;
        }
        button:active {
          outline: none;
        }
        .btn_pre:disabled {
          background-color: #fcfcfc;
          border: 1px solid #6e6e6e;
          color: #6e6e6e;
          display: none;
        }
        .btn_next:disabled {
          background-color: #fcfcfc;
          border: 1px solid #6e6e6e;
          color: #6e6e6e;
          display: none;
        }
      }
      .switch-group {
        display: inline-flex;
        box-sizing: border-box;
        button {
          width: 40px;
          height: 40px;
          padding: 0;
          color: #5f6b73;
          font-size: 0.875rem;
          border: 1px solid #cad4db;
          background: #fff;
          font-weight: 400;
          border-radius: 0.25em;
        }
        .button-d {
          border-top-left-radius: 0;
          border-bottom-left-radius: 0;
        }
        .button-m {
          border-top-right-radius: 0;
          border-bottom-right-radius: 0;
        }
        .button-m:hover:not(.active),
        .button-d:hover:not(.active) {
          background: #eef6ff !important;
          color: #5f6b73;
          font-weight: bold;
        }

        .active {
          border: 2px solid #448add !important;
          color: #448add !important;
          background: #eef6ff !important;
          font-weight: 700 !important;
        }
      }
    }
    &--content {
      display: flex;
      width: 100%;
      background-color: #fff;
      height: 85%;
      border-radius: 5px;
      box-shadow: 0px 0px 0px rgb(185, 185, 185), 0px -5px 5px rgba(173, 173, 173, 0.445);
      margin-top: 10px;
      ul {
        padding: 10px 0;
        width: 100%;
        margin: 0;
        animation-name: opacity;
        animation-duration: 0.2s;
        li,
        .li {
          list-style-type: none;
          display: inline-flex;
          width: 25%;
          margin-top: 35px;
          margin-bottom: 10px;
          font-size: 16px;
          color: #777;
          text-align: center;
          justify-content: center;
          align-content: center;

          div {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
            width: 45px;
            padding: 5px;
            border-radius: 90px 90px 90px 90px;
            user-select: none;
          }
          .selected {
            background: var(--el-color-primary);
            color: #fff;
          }
        }
        .highlight {
          background-color: #d9e7fd;
        }
        .background-s {
          background-image: linear-gradient(90deg, white 50%, #d9e7fd 50%);
        }
        .background-e {
          background-image: linear-gradient(-90deg, white 50%, #d9e7fd 50%);
        }
        li:nth-child(13),
        li:nth-child(14),
        li:nth-child(15),
        li:nth-child(16) {
          color: rgb(201, 201, 201);
        }
        div:hover {
          background-color: var(--el-color-primary-light-4);
          color: #fff;
        }
        div:hover:not(.selected) {
          background: var(--el-color-primary-light-4);
        }
        div:active,
        div:focus {
          background: var(--el-color-primary);
          color: #fff;
        }
        li:nth-child(13):hover > div,
        li:nth-child(14):hover > div,
        li:nth-child(15):hover > div,
        li:nth-child(16):hover > div {
          background-color: var(--el-color-primary-light-4);
          color: #fff;
        }
      }
      .date_picker {
        animation-name: opatity;
        animation-duration: 0.5s;
        width: 100%;
        .header-date {
          .date_event {
            display: flex;
            align-items: center;
            justify-content: center;
            .month {
              text-align: center;
              font-size: 16px;
            }
            button {
              padding-left: 10px;
              padding-right: 10px;
            }
          }

          ol {
            display: flex;
            flex-wrap: wrap;
            padding-left: 5px;
            li {
              list-style-type: none;
              width: 14%;
              height: 40px;
              font-size: 18px;
              text-align: center;
              line-height: 35px;
            }
          }
        }
        .body-date {
          ol {
            padding-left: 5px;
            width: 100%;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            .day {
              list-style-type: none;
              display: inline-flex;
              width: 14%;
              margin-bottom: 5px;
              font-size: 16px;
              color: #777;
              cursor: pointer;
              text-align: center;
              justify-content: center;
              align-content: center;
              &:hover {
                background-image: linear-gradient(to right, #57a5f8 0%, #57a5f8 51%, #57a5f8 100%);
                border-radius: 95px 95px 95px 95px;
                color: #fff !important;
              }
            }
            .disable_past_days {
              background-color: rgb(243, 242, 242) !important;
              color: rgb(197, 197, 197);
              pointer-events: none;
            }
            .event_none {
              pointer-events: none;
            }
            .enable_past_days {
              background-color: rgb(255, 255, 255) !important;
              color: #777;
              pointer-events: all !important;
            }
            .day_not {
              list-style-type: none;
              display: inline-flex;
              width: 14%;
              margin-bottom: 5px;
              font-size: 16px;
              color: #777;
              text-align: center;
              justify-content: center;
              align-content: center;
            }
            .active {
              background-image: linear-gradient(to right, #448add 0%, #448add 51%, #448add 100%);
              border-radius: 95px 95px 95px 95px;

              color: #fff !important;
            }
            .active-color {
              color: #448add;
            }
          }
        }
        .footer-date {
          border-top: 1px solid rgb(199, 199, 199);
          position: absolute;
          bottom: -3px;
          left: 0px !important;
          width: 100%;
          .form-group {
            display: inline-flex;
            flex-wrap: wrap;
            margin-right: 0px;
            margin-bottom: -10px !important;
            width: 100%;
            .sprin1 {
              width: 50%;
            }
            .sprin2 {
              width: 50%;
            }
            .input-avtive {
              border-top: 1px solid #448add;
            }
            .input-avtive1 {
              animation-duration: 0.5s;
              animation-name: slidein;
              border-top: 1px solid #448add;
            }
            .input-avtive2 {
              animation-duration: 0.5s;
              animation-name: slidein2;
              border-top: 1px solid #448add;
            }
            .timer-validate-red {
              animation-name: checkColor;
              animation-duration: 0.4s;
            }
          }
        }
      }
    }
  }
  @keyframes slidein {
    from {
      margin-left: 50%;
      width: 50%;
    }

    to {
      margin-left: 0%;
      width: 50%;
    }
  }
  @keyframes opacity {
    0% {
      opacity: 0%;
    }
    50% {
      opacity: 50%;
    }
    100% {
      opacity: 100%;
    }
  }
  @keyframes slidein2 {
    from {
      margin-left: -50%;
      width: 50%;
    }

    to {
      margin-left: 0%;
      width: 50%;
    }
  }
  @keyframes checkColor {
    0% {
      border-top: 1px solid #dd4444;
      opacity: 20%;
    }
    50% {
      border-top: 1px solid #f83706;
      opacity: 100%;
    }
    70% {
      border-top: 1px solid #ff1d1d;
      opacity: 20%;
    }
    100% {
      opacity: 100%;

      border-top: 1px solid #448add;
    }
  }
}
.custom-date {
  width: 100% !important;
  border: none;
  --blue-200: #d1e4ff !important;
  --blue-600: #448add !important;
  --gray-900: #777 !important;
  --blue-900: #777 !important;
  --font-bold: 600 !important;
  --gray-800: #777 !important;
  --gray-200: rgba(119, 119, 119, 0.041) !important;
  --gray-300: rgba(119, 119, 119, 0.158) !important;
  .vc-pane-container {
    background-color: navajowhite;
  }
}
.custom-date .vc-pane-container .vc-pane-layout .vc-pane .vc-weeks .vc-weekday {
  border-bottom: 1px solid #c9c9c9;
}
.v-date-picker-header {
  display: none;
}
.mode_day {
  padding: 0px !important;
}
</style>
