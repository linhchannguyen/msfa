<template>
  <div class="card-calendar">
    <div class="card-calendar-box">
      <div class="card-calendar-box--switch">
        <span>{{ mode === 'monthly' ? datePicker.year : isRange1 ? datePicker['singleDate1']?.year : datePicker['singleDate2']?.year }}年</span>
        <div class="btn-group">
          <button @click="preYear()"><i class="el-icon-arrow-left"></i></button>
          <button @click="nextYear()"><i class="el-icon-arrow-right"></i></button>
        </div>
        <div class="switch-group">
          <button
            :disabled="mode === 'monthly' ? true : false"
            :class="['button-m', mode === 'monthly' ? 'active' : '']"
            @click="changeMode('monthly', 'singleDate1')"
          >
            月
          </button>
          <button :disabled="mode === 'day' ? true : false" :class="['button-d', mode === 'day' ? 'active' : '']" @click="changeMode('day', 'singleDate1')">
            日
          </button>
        </div>
      </div>
      <div v-if="mode === 'monthly'" class="card-calendar-box--content">
        <ul>
          <li v-for="item of 12" :key="item" :class="['day-item-range1']" :value="item">
            <div :value="item">{{ item }}月</div>
          </li>
          <li v-for="item of 4" :key="item" readOnly="true" disabled="true" :value="item">
            <div>{{ item }}月</div>
          </li>
        </ul>
        <!-- {{ datePicker }} -->
      </div>
      <div v-else class="card-calendar-box--content">
        <div class="date_picker">
          <div class="header-date">
            <div class="date_event">
              <button @click="perMonth(isRange1 ? 'singleDate1' : 'singleDate2')"><i class="el-icon-arrow-left"></i></button>
              <div class="month">
                {{ datePicker[isRange1 ? 'singleDate1' : 'singleDate2']?.currentMonth }}月
                {{ isRange1 ? datePicker['singleDate1'].year : datePicker['singleDate2'].year }}年
              </div>
              <button @click="nextMonth(isRange1 ? 'singleDate1' : 'singleDate2')"><i class="el-icon-arrow-right"></i></button>
            </div>
            <ol>
              <li style="color: red">日</li>
              <!--Sun-->
              <li>月</li>
              <!--Mon-->
              <li>火</li>
              <!--Thir-->
              <li>水</li>
              <!--Web-->
              <li>木</li>
              <!--Thu-->
              <li>金</li>
              <!--Fri-->
              <li>土</li>
              <!--Sat-->
            </ol>
          </div>
          <div v-if="isRange1" class="body-date">
            <ol ref="data-date" class="date date-container">
              <li
                v-for="(item, index) of datePicker.singleDate1.daysInMonth"
                :id="idDatePicker(item, 'singleDate1')"
                :key="index"
                :class="[
                  item > 0 ? 'day day-item-range1' : 'day_not day-item-range1',
                  activeSingeDate(item, 'singleDate1', index),
                  item === 0 ? 'event_none' : ''
                ]"
                @click="dateSignle(item, 'singleDate1')"
              >
                {{ renderDatePicker(item) }}
              </li>
            </ol>
          </div>
          <div v-else class="body-date">
            <ol ref="data-date" class="date date-container">
              <li
                v-for="(item, index) of datePicker.singleDate2.daysInMonth"
                :id="idDatePicker(item, 'singleDate2')"
                :key="index"
                :class="[
                  item > 0 ? 'day day-item-range2' : 'day_not day-item-range2',
                  activeSingeDate(item, 'singleDate2', index),
                  item === 0 ? 'event_none' : ''
                ]"
                @click="dateSignle(item, 'singleDate2')"
              >
                {{ renderDatePicker(item) }}
              </li>
            </ol>
          </div>
          <div class="footer-date">
            <div class="form-group">
              <div class="sprin1 input-avtive"></div>
              <div class="sprin2"></div>
              <div class="range-group">
                <div class="range-input-1">
                  <el-input
                    v-model="datePicker.singleDate1.value"
                    :value="datePicker.singleDate1.value"
                    readonly
                    placeholder="開始日"
                    style="background: #ffffff"
                    class="form-control-input"
                    @click="rangeDate1()"
                  />
                  <span v-if="isRange1 && datePicker.singleDate1.value.length > 0" @click="removeDateInput('singleDate1')">X</span>
                </div>

                <div class="range-input-2">
                  <el-input
                    v-model="datePicker.singleDate2.value"
                    :value="datePicker.singleDate2.value"
                    readonly
                    placeholder="終了日"
                    style="background: #ffffff"
                    class="form-control-input"
                    @click="rangeDate2()"
                  />
                  <span v-if="!isRange1 && datePicker.singleDate2.value.length > 0" @click="removeDateInput('singleDate2')">X</span>
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
    }
  },
  emits: ['changeMode', 'click'],
  data() {
    return {
      attrs: [
        {
          highlight: {
            start: { fillMode: 'outline' },
            base: { fillMode: 'light' },
            end: { fillMode: 'outline' }
          },
          dates: { start: '', end: '' }
        }
      ],
      mode1: 'range',
      formats: {
        input: ['YYYY-M-D']
      },
      selectedDate: { start: new Date(), end: '' },
      activeDate: '',
      datePicker: {
        year: new Date().getFullYear(),
        start_date_month: '',
        end_date_month: '',
        fullYearStartMonth: '',
        fullYearEndMonth: '',
        singleDate1: {
          currentMonth: new Date().getMonth() + 1,
          daysInMonth: [],
          date: new Date().getDate(),
          currentDate: new Date().getDate(),
          dateString: '',
          year: new Date().getFullYear(),
          value: ''
        },
        singleDate2: {
          currentMonth: new Date().getMonth() + 1,
          daysInMonth: [],
          date: new Date().getDate() + 1,
          currentDate: new Date().getDate(),
          dateString: '',
          year: new Date().getFullYear(),
          value: ''
        }
      },
      isRange1: true,
      submit: false
    };
  },
  mounted() {
    var end = new Date();
    end.setDate(end.getDate() + 7);
    this.selectedDate.end = end;
    this.excecDateRange('singleDate1');
    const newDate = new Date(this.datePicker.singleDate1.dateString);
    newDate.setDate(newDate.getDate() + 1);
    const date = moment(newDate);
    this.datePicker.singleDate2.date = newDate.getDate();
    this.datePicker.singleDate2.dateString = date.format('YYYY/M/D');
    this.activeDate = document.querySelectorAll('.day');
  },
  methods: {
    /** Start Mode Day */
    disabeDateAll(range) {
      const startDate = this.datePicker.singleDate1.value.length === 0 ? this.datePicker.singleDate1.dateString : this.datePicker.singleDate1.value;
      const endDate = this.datePicker.singleDate2.value.length === 0 ? this.datePicker.singleDate2.dateString : this.datePicker.singleDate2.value;
      setTimeout(() => {
        this.activeDate.forEach((s) => {
          if (range === 2) {
            if (new Date(startDate).getTime() > new Date(s.id).getTime()) {
              s.classList.add('disable_past_days');
            }
            if (this.datePicker.singleDate1.value.length === 0) {
              s.classList.add('enable_past_days');
            }
          } else if (range === 1) {
            if (new Date(endDate).getTime() < new Date(s.id).getTime()) {
              s.classList.add('disable_past_days');
            }
            if (this.datePicker.singleDate2.value.length === 0) {
              s.classList.add('enable_past_days');
            }
          }
        });
      });
    },
    checkEndDate() {
      const rangeStart = new Date(this.datePicker.singleDate1.dateString).getTime();
      const rangeEnd = new Date(this.datePicker.singleDate2.dateString).getTime();
      const input2 = document.querySelector('.range-input-2 .form-control-input .el-input__inner');
      const sprin2 = document.querySelector('.sprin2');
      const sprint1 = document.querySelector('.sprin1');
      if (input2) {
        if (rangeStart > rangeEnd) {
          this.isRange1 = false;
          this.submit = false;
          this.datePicker['singleDate2'].daysInMonth = this.calendar(this.datePicker['singleDate2'].currentMonth, this.datePicker['singleDate2'].year);
          this.styleColorSunDay('singleDate2');
          input2.classList.add('validate_end_date');

          if (sprin2) {
            /**sprin Chạy [aninamtion] đến end date */

            sprin2.classList.add('sprin2 input-avtive');
            sprint1.classList.remove('input-avtive');
            sprint1.classList.remove('input-avtive1');
          }
        } else {
          this.submit = true;
          // this.isRange1 = true;
          input2.classList.remove('validate_end_date');
        }
      }
      // const sprin2 = document.querySelector('.sprin2');
      if (!this.isRange1 && !this.submit) {
        sprin2.setAttribute('class', 'sprin2 input-avtive2 timer-validate-red');
        setTimeout(() => {
          sprin2.setAttribute('class', 'sprin2 input-avtive');
        }, 200);
      }
    },

    /** Input Mode */
    rangeDate2() {
      this.checkEndDate();
      const sprin2 = document.querySelector('.sprin2');
      const sprin1 = document.querySelector('.sprin1');

      if (this.submit) {
        this.isRange1 = false;

        if (sprin2) {
          /**sprin Chạy [aninamtion] đến end date */
          sprin2.setAttribute('class', 'sprin2 input-avtive2');
          sprin1.setAttribute('class', 'sprin1');
        }
        this.styleColorSunDay('singleDate2');
        this.datePicker['singleDate2'].year = new Date(this.datePicker['singleDate2'].dateString).getFullYear() || new Date().getFullYear();
        const valueDate = this.datePicker['singleDate2'].value.length > 0 ? this.datePicker['singleDate2'].value : moment().format('YYYY/M/D');
        this.datePicker['singleDate2'].daysInMonth = this.calendar(new Date(valueDate).getMonth() + 1, new Date(valueDate).getFullYear());
        this.datePicker['singleDate2'].currentMonth = new Date(valueDate).getMonth() + 1;
        this.datePicker['singleDate2'].date = new Date(valueDate).getDate();
        this.mapActiveDate();
        this.resetActiveNextDate();
        this.disabeDateAll(2);
      }

      setTimeout(() => {
        this.activeDate = document.querySelectorAll('.day');
      });
    },
    rangeDate1() {
      this.checkEndDate();
      const inputRange1 = document.querySelector('.sprin1');
      const inputRange2 = document.querySelector('.sprin2');
      if (this.submit) {
        this.isRange1 = true;
        if (inputRange1) {
          /**sprin Chạy [aninamtion] đến start end date */
          inputRange1.setAttribute('class', 'sprin1 input-avtive1');
          inputRange2.setAttribute('class', 'sprin2');
        }
        this.styleColorSunDay('singleDate1');
        this.datePicker['singleDate1'].year = new Date(this.datePicker['singleDate1'].dateString).getFullYear() || new Date().getFullYear();
        const valueDate = this.datePicker['singleDate1'].value.length > 0 ? this.datePicker['singleDate1'].value : moment().format('YYYY/M/D');
        this.datePicker['singleDate1'].daysInMonth = this.calendar(new Date(valueDate).getMonth() + 1, new Date(valueDate).getFullYear());
        this.datePicker['singleDate1'].currentMonth = new Date(valueDate).getMonth() + 1;
        this.datePicker['singleDate1'].date = new Date(valueDate).getDate();
        this.mapActiveDate();
        this.resetActiveNextDate();
        this.disabeDateAll(1);
      }

      // if (!this.isRange1) {
      //   inputRange2.setAttribute('class', 'sprin2 input-avtive2 timer-validate-red');
      //   setTimeout(() => {
      //     inputRange2.setAttribute('class', 'sprin2 input-avtive');
      //   }, 200);
      // }

      setTimeout(() => {
        this.activeDate = document.querySelectorAll('.day');
      });
    },
    /**  */
    /** Input Mode */
    /** Date picker */
    removeDateInput(mode) {
      this.datePicker[mode].value = '';
      const rangeStart = new Date(this.datePicker.singleDate1.value).getTime();
      const rangeEnd = new Date(this.datePicker.singleDate2.vakue).getTime();
      const checkDate = rangeStart < rangeEnd;
      this.$emit('click', {
        mode: 'day',
        startDate: this.datePicker['singleDate1'].value,
        endDate: this.datePicker['singleDate2'].value,
        status: rangeStart < rangeEnd ? 'success' : 'error',
        callApi: rangeStart < rangeEnd ? true : false,
        rangeDate: [checkDate ? moment(this.datePicker['singleDate1'].value) : null, checkDate ? moment(this.datePicker['singleDate2'].value) : null],

        getAll: true
      });
      this.disabeDateAll(this.isRange1 ? 1 : 2);
    },

    idDatePicker(item, mode) {
      if (item !== 0) {
        const d = item < 10 ? `0${item}` : `${item}`;
        const m = this.datePicker[mode].currentMonth < 10 ? `0${this.datePicker[mode].currentMonth}` : `${this.datePicker[mode].currentMonth}`;
        const y = this.datePicker[mode].year;
        return `${y}/${m}/${d}`;
      } else {
        return 0;
      }
    },
    mapActiveDate() {
      // eslint-disable-next-line eqeqeq
      setTimeout(() => {
        this.activeDate.forEach((s) => {
          // eslint-disable-next-line eqeqeq
          if (s.id == moment().format('YYYY/M/D')) {
            if (new Date(s.id).getDate() === new Date().getDate()) {
              s.style.color = 'var(--el-color-primary)';
            } else {
              s.style.color = '';
            }
          }
        });
      });
    },
    resetActiveNextDate() {
      const date = new Date(this.datePicker[this.isRange1 ? 'singleDate1' : 'singleDate2'].value);
      const d = date.getDate() < 10 ? `0${date.getDate()}` : `${date.getDate()}`;
      const m = date.getMonth() + 1 < 10 ? `0${date.getMonth() + 1}` : `${date.getMonth() + 1}`;
      const y = date.getFullYear();
      const fullDate = `${y}/${m}/${d}`;
      setTimeout(() => {
        this.activeDate = document.querySelectorAll('.day');
        this.activeDate.forEach((s) => {
          if (s.id === fullDate) {
            if (new Date(s.id).getDate() === new Date(fullDate).getDate()) {
              s.setAttribute('class', 'day day-item-range1 active');
            } else {
              s.setAttribute('class', 'day day-item-range1');
            }
          } else {
            s.setAttribute('class', 'day day-item-range1');
          }
        });
      });
      this.activeDate.forEach((s) => {
        if (s.id === moment().format('YYYY/M/D')) {
          s.style.color = '';
        }
      });
    },
    activeSingeDate(date, mode) {
      const singleDate = mode;
      // eslint-disable-next-line eqeqeq
      const dateString = this.datePicker[singleDate].date < 10 ? `0${date}` : `${date}`;
      // const m = this.datePicker[mode].currentMonth < 10 ? `0${this.datePicker[mode].currentMonth}` : `${this.datePicker[mode].currentMonth}`;
      //   const y = this.datePicker[mode].year;
      const checkDate = new Date(this.datePicker[singleDate].value).getDate() === date || this.datePicker[singleDate].date === dateString;

      this.mapActiveDate();
      this.disabeDateAll(true, this.isRange1 ? 1 : 2);

      return checkDate ? 'active' : '';
    },
    renderDatePicker(date) {
      return date === 0 ? '' : date;
    },
    /** event */
    dateSignle(date, mode) {
      const singleDate = mode;
      this.datePicker[singleDate].daysInMonth.forEach((s) => {
        // const li = document.querySelectorAll('.day');
        this.datePicker[singleDate].date = date;
        const month =
          this.datePicker[singleDate].currentMonth < 10 ? `0${this.datePicker[singleDate].currentMonth}` : `${this.datePicker[singleDate].currentMonth}`;
        const monthYear = `${this.datePicker[singleDate].year}/${month}`;
        const date_ = this.datePicker[singleDate].date < 10 ? `0${date}` : `${date}`;
        this.datePicker[singleDate].dateString = `${monthYear}/${date_}`;
        // this.datePicker[singleDate].year = new Date(this.datePicker[singleDate].dateString).getFullYear();
        if (s === date) {
          this.datePicker[singleDate].value = this.datePicker[singleDate].dateString;

          // li[s - 1]?.setAttribute('class', mode === 'singleDate1' ? 'day day-item-range1 active' : 'day day-item-range2 active');
          // li[s - 1]?.classList.add('active');
        } else {
          // li[s - 1]?.setAttribute('class', mode === 'singleDate1' ? 'day day-item-range1' : 'day day-item-range2');
          // li[s - 1]?.classList.remove('active');
        }
      });

      /** emit event date */
      const rangeStart = new Date(this.datePicker.singleDate1.dateString).getTime();
      const rangeEnd = new Date(this.datePicker.singleDate2.dateString).getTime();
      const checkDate = rangeStart < rangeEnd;
      this.$emit('click', {
        mode: 'day',
        startDate: this.datePicker['singleDate1'].value,
        endDate: checkDate ? this.datePicker['singleDate2'].value : '',
        rangeDate: [checkDate ? moment(this.datePicker['singleDate1'].value) : null, checkDate ? moment(this.datePicker['singleDate2'].value) : null],
        status: rangeStart < rangeEnd ? 'success' : 'error',
        callApi: rangeStart < rangeEnd ? true : false,
        getAll: false
      });
      this.checkEndDate();
      /**sprin Chạy [aninamtion] đến end date và báo lỗi; chạy 1 lần duy nhất  */
      const input2 = document.querySelector('.range-input-2 .form-control-input .el-input__inner');
      if (!this.submit && input2) {
        const sprin2 = document.querySelector('.sprin2');
        const sprint1 = document.querySelector('.sprin1');
        input2.setAttribute('class', 'el-input__inner validate_end_date');
        if (sprin2) {
          if (!this.isRange1) {
            sprin2.setAttribute('class', 'sprin2 input-avtive2');
            sprint1.setAttribute('class', 'sprin1');
            setTimeout(() => {
              sprin2.setAttribute('class', 'sprin2 input-avtive');
            }, 200);
          }
        }
      }
      this.mapActiveDate();
    },
    eventClick() {
      const singleDate = this.isRange1 ? 'singleDate1' : 'singleDate2';
      this.$emit('click', {
        mode: 'monthly',
        year: this.datePicker.year,
        start: this.start,
        fullYearEndMonth: this.datePicker.fullYearEndMonth,
        fullYearStartMonth: this.datePicker.fullYearStartMonth,
        daysInMonth: this.datePicker[singleDate].daysInMonth,
        end: this.end
      });
    },
    /** xử lý show danh sách ngày trong tháng */
    zeller(D, M, Y) {
      var Day = '';

      if (M < 3) {
        M = M + 12;
        Y = Y - 1;
      }

      var C = Math.floor(Y / 100);
      var K = Y - 100 * C;

      var S = Math.floor(2.6 * M - 5.39) + Math.floor(K / 4) + Math.floor(C / 4) + D + K - 2 * C;

      var ans = S - 7 * Math.floor(S / 7);

      if (ans === 0) {
        Day = 1;
      } else if (ans === 1) {
        Day = 2;
      } else if (ans === 2) {
        Day = 3;
      } else if (ans === 3) {
        Day = 4;
      } else if (ans === 4) {
        Day = 5;
      } else if (ans === 5) {
        Day = 6;
      } else {
        Day = 7;
      }

      return Day;
    },
    daysIn(month, year) {
      return month === 2 ? 28 + this.isLeap(year) : 31 - (((month - 1) % 7) % 2);
    },
    isLeap(year) {
      if (year % 4 || (year % 100 === 0 && year % 400)) return 0;
      else return 1;
    },
    // viết lại cho dễ dùng
    calendar(month, year) {
      var startIndex = Math.trunc(this.zeller(1, month, year));
      var endIndex = this.daysIn(month, year);
      var result = Array.apply(0, Array(42)).map(function () {
        return 0;
      });
      for (var i = startIndex; i < endIndex + startIndex; i++) {
        result[i - 1] = i - startIndex + 1;
      }
      return result;
    },
    /** xử lý show danh sách ngày trong tháng */

    resetDate() {
      this.datePicker = {
        start: '',
        fullYearStartMonth: '',
        start_date_month: '',
        end_date_month: '',
        end: '',
        fullYearEndMonth: '',
        year: this.year,
        mode: 'monthly',
        singleDate1: {
          currentMonth: new Date().getMonth() + 1,
          daysInMonth: [],
          date: new Date().getDate(),
          currentDate: new Date().getDate(),
          dateString: '',
          year: new Date().getFullYear(),
          value: ''
        },
        singleDate2: {
          currentMonth: new Date().getMonth() + 1,
          daysInMonth: [],
          date: new Date().getDate() + 1,
          currentDate: new Date().getDate(),
          dateString: '',
          year: new Date().getFullYear(),
          value: ''
        }
      };
    },

    changeMode(mode, filed) {
      const singleDate = filed;
      // eslint-disable-next-line vue/no-mutating-props
      this.$emit('changeMode', mode);
      if (mode === 'monthly') {
        setTimeout(() => {
          this.excecDateRange(singleDate);
        });
      } else {
        this.isRange1 = true;
        setTimeout(() => {
          // this.datePicker[singleDate].daysInMonth = this.calendar(this.datePicker[singleDate].currentMonth, this.datePicker[singleDate].year);
          const valueDate = this.datePicker[singleDate].value.length > 0 ? this.datePicker[singleDate].value : moment().format('YYYY/MM:DD');
          this.datePicker[singleDate].currentMonth = new Date(valueDate).getMonth() + 1;
          this.datePicker[singleDate].date = new Date(valueDate).getDate();
          this.datePicker[singleDate].year = new Date(valueDate).getFullYear() || new Date().getFullYear();

          this.datePicker[singleDate].daysInMonth = this.calendar(new Date(valueDate).getMonth() + 1, this.datePicker[singleDate].year);

          this.eventClick();
          this.styleColorSunDay(singleDate);
          this.checkEndDate();
          // mode: trở về range 1
          if (this.datePicker[singleDate].value.length > 0 && this.submit) {
            this.isRange1 = true;
          }
          if (this.submit === false) {
            this.isRange1 = false;
          }
          this.activeDate = document.querySelectorAll('.day');
          this.mapActiveDate();
        });
      }
    },
    styleColorSunDay(mode) {
      const singleDate = mode;
      setTimeout(() => {
        const li = document.querySelectorAll('.day-item-range1');
        const li2 = document.querySelectorAll('.day-item-range2');

        this.datePicker[singleDate].daysInMonth.forEach((s, index) => {
          const indexp = index * 7;
          if (this.isRange1) {
            if (li[indexp]) {
              li[indexp].style.color = 'red';
            }
          } else {
            if (li2[indexp]) {
              li2[indexp].style.color = 'red';
            }
          }
        });
      });
    },
    getMonth() {
      this.datePicker.start_date_month = this.selectedStart < 10 ? `0${this.selectedStart}` : `${this.selectedStart}`;
      this.datePicker.end_date_month = this.selectedEnd < 10 ? `0${this.selectedEnd}` : `${this.selectedEnd}`;
    },
    perMonth(mode) {
      this.activeDate = document.querySelectorAll('.day');
      const singleDate = mode;

      // eslint-disable-next-line eqeqeq
      if (this.datePicker[singleDate].currentMonth == 1) {
        this.datePicker[singleDate].currentMonth = 12;
        // eslint-disable-next-line vue/no-mutating-props
        this.datePicker.year--;
        this.datePicker[singleDate].year--;
      } else {
        this.datePicker[singleDate].currentMonth--;
      }
      this.getDateMonthCurrent(singleDate);
      this.datePicker[singleDate].daysInMonth = this.calendar(this.datePicker[singleDate].currentMonth, this.datePicker[singleDate].year);
      // this.datePicker[singleDate].year = this.year;
      this.resetActiveNextDate();
      this.disabeDateAll(this.isRange1 ? 1 : 2);
    },
    nextMonth(mode) {
      this.activeDate = document.querySelectorAll('.day');
      const singleDate = mode;

      // eslint-disable-next-line eqeqeq
      if (this.datePicker[singleDate].currentMonth == 12) {
        this.datePicker[singleDate].currentMonth = 1;
        // eslint-disable-next-line vue/no-mutating-props
        this.datePicker.year++;
        this.datePicker[singleDate].year++;
      } else {
        this.datePicker[singleDate].currentMonth++;
      }
      this.getDateMonthCurrent(singleDate);
      this.datePicker[singleDate].daysInMonth = this.calendar(this.datePicker[singleDate].currentMonth, this.datePicker[singleDate].year);
      // this.datePicker[singleDate].year = this.datePicker.year;
      this.resetActiveNextDate();

      this.disabeDateAll(this.isRange1 ? 1 : 2);
    },
    getDateMonthCurrent(mode) {
      const singleDate = mode;

      if (
        this.year === new Date(this.datePicker[singleDate].dateString).getFullYear() &&
        this.datePicker[singleDate].currentMonth === new Date(this.datePicker[singleDate].dateString).getMonth() + 1
      ) {
        this.datePicker[singleDate].date = new Date(this.datePicker[singleDate].dateString).getDate();
      } else {
        this.datePicker[singleDate].date = '';
      }
    },
    /** END Mode Day */
    setUpCalendar(mode) {
      const singleDate = mode;
      let ul = document.querySelector('.card-calendar-box--content ul');
      let selecting, start, end, last;
      let toggleSelection = (i, li) => {
        if (selecting) {
          endSelection(i, li);
          if (end === i) {
            last = i;
          }
        } else {
          beginSelection(i);
        }
      };

      let beginSelection = (i) => {
        selecting = true;
        start = i;
        updateSelection(i);
      };

      let endSelection = (i = end) => {
        updateSelection(i);
        selecting = false;
      };

      let updateSelection = (i) => {
        if (selecting) {
          end = i;
          if (start === i) {
            if (i >= start && i <= end) {
              [...document.querySelectorAll('.card-calendar-box--content ul li div')].forEach((span, i) => {
                span.classList.toggle('selected', i >= start && i <= end);
              });
              [...document.querySelectorAll('.card-calendar-box--content ul li')].forEach((span, i) => {
                span.setAttribute('class', '');
                span.classList.toggle('background-s', i >= start && i <= end);
              });

              // eslint-disable-next-line vue/no-mutating-props
              // this.selectedStart = start + 1;

              if (this.selectedStart > 12) {
                // eslint-disable-next-line vue/no-mutating-props
                const selecting = this.selectedStart - 12;

                this.datePicker.fullYearStartMonth = `${this.year + 1}/${this.datePicker.start_date_month}`;
                this.$emit('click', {
                  mode: 'monthly',
                  year: this.datePicker.year,
                  start: selecting,
                  fullYearEndMonth: this.datePicker.fullYearEndMonth,
                  fullYearStartMonth: this.datePicker.fullYearStartMonth,
                  daysInMonth: this.datePicker[singleDate]?.daysInMonth,
                  end: this.end
                });
              } else {
                // eslint-disable-next-line vue/no-mutating-props
                // this.selectedStart = start + 1;
                this.datePicker.fullYearStartMonth = `${this.year}/${this.datePicker.start_date_month}`;
                this.$emit('click', {
                  mode: 'monthly',
                  year: this.datePicker.year,
                  start: start + 1,
                  fullYearEndMonth: this.datePicker.fullYearEndMonth,
                  fullYearStartMonth: this.datePicker.fullYearStartMonth,
                  daysInMonth: this.datePicker[singleDate]?.daysInMonth,
                  end: this.end
                });
              }
            } else {
              // this.resetDate();
            }
          } else {
            [...document.querySelectorAll('.card-calendar-box--content ul li')].forEach((span, i) => {
              span.classList.toggle('highlight', i >= start && i <= end);
            });
          }
        } else {
          if (last === i) {
            if (i >= start && i <= end) {
              [...document.querySelectorAll('.card-calendar-box--content ul li div')][last].classList.toggle('selected', i >= start && i <= end);
              [...document.querySelectorAll('.card-calendar-box--content ul li')][last].classList.toggle('background-e', i >= start && i <= end);
              // eslint-disable-next-line vue/no-mutating-props
              // this.selectedEnd = last + 1;
              if (this.selectedEnd > 12) {
                // eslint-disable-next-line vue/no-mutating-props
                const selecting = this.selectedEnd - 12;
                this.datePicker.fullYearEndMonth = `${this.year + 1}/${this.datePicker.end_date_month}`;
                this.$emit('click', {
                  mode: 'monthly',
                  year: this.datePicker.year,
                  start: this.start,
                  fullYearEndMonth: this.datePicker.fullYearEndMonth,
                  fullYearStartMonth: this.datePicker.fullYearStartMonth,
                  end: selecting
                });
              } else {
                // eslint-disable-next-line vue/no-mutating-props
                // this.selectedEnd = last + 1;
                this.datePicker.fullYearEndMonth = `${this.year}/${this.datePicker.end_date_month}`;
                this.$emit('click', {
                  mode: 'monthly',
                  year: this.datePicker.year,
                  start: this.start,
                  fullYearEndMonth: this.datePicker.fullYearEndMonth,
                  fullYearStartMonth: this.datePicker.fullYearStartMonth,
                  end: last + 1
                });
              }
            } else {
              // this.resetDate();
            }
          }
        }
        this.getMonth();
      };
      for (let i = 0; i < ul?.childElementCount; i++) {
        let li = ul.querySelectorAll('.day-item-range1');

        li[i]?.addEventListener('click', () => {
          toggleSelection(
            i,
            [...li].map((s) => s.children[0]).filter((s) => s.className === 'selected')
          );
        });
        li[i]?.addEventListener('mousemove', () => updateSelection(i));
      }
      // let ul1 = document.querySelector('.card-calendar-box--content ul li');
    },
    getMonthRange() {
      if (this.selectedStart < this.selectedEnd) {
        let ul = document.querySelector('.card-calendar-box--content ul');
        if (ul) {
          for (let i = 0; i < ul?.childElementCount; i++) {
            let li = ul.querySelectorAll('.day-item-range1');
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
      this.activeDate = document.querySelectorAll('.day');

      const singleDate = this.isRange1 ? 'singleDate1' : 'singleDate2';
      this.datePicker.year = this.year - 1;
      this.datePicker.fullYearEndMonth = `${this.datePicker.year}/${this.datePicker.end_date_month}`;
      this.datePicker.fullYearStartMonth = `${this.datePicker.year}/${this.datePicker.start_date_month}`;
      // this.dateSignle(this.datePicker[singleDate].date, singleDate);

      this.datePicker[singleDate].year = this.datePicker[singleDate].year - 1;
      this.datePicker[singleDate].daysInMonth = this.calendar(this.datePicker[singleDate].currentMonth, this.datePicker[singleDate].year);
      this.eventClick();
      this.resetActiveNextDate();
      this.disabeDateAll(this.isRange1 ? 1 : 2);
    },
    nextYear() {
      this.activeDate = document.querySelectorAll('.day');

      const singleDate = this.isRange1 ? 'singleDate1' : 'singleDate2';
      this.datePicker.year = this.year + 1;
      this.datePicker.fullYearEndMonth = `${this.datePicker.year}/${this.datePicker.end_date_month}`;
      this.datePicker.fullYearStartMonth = `${this.datePicker.year}/${this.datePicker.start_date_month}`;
      // this.dateSignle(this.datePicker[singleDate].date, singleDate);

      this.datePicker[singleDate].year = this.datePicker[singleDate].year + 1;
      this.datePicker[singleDate].daysInMonth = this.calendar(this.datePicker[singleDate].currentMonth, this.datePicker[singleDate].year);
      this.eventClick();

      this.resetActiveNextDate();
      this.disabeDateAll(this.isRange1 ? 1 : 2);
    },
    // run;
    excecDateRange(mode) {
      const singleDate = mode;
      this.setUpCalendar(singleDate);
      this.getMonthRange();
      this.getMonth();
      this.datePicker[singleDate].daysInMonth = this.calendar(this.datePicker[singleDate].currentMonth, this.datePicker[singleDate].year);
    }
  }
};
</script>
<style lang="scss" scoped>
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
      display: flex;
      width: 100%;
      min-height: 50px;
      justify-content: center;
      align-items: center;
      span {
        font-size: 19px;
        color: #4d97f1;
        font-weight: bold;
      }
      .btn-group {
        margin-left: 10px;
        button {
          color: #4d97f1;
          width: 45px;
          height: 40px;
          background-color: #fcfcfc;
          border: 1px solid #4d97f1;
          border-radius: 95px 95px 95px 95px;
          padding: 5px;
          margin-right: 8px;
        }
        button:hover {
          background-color: #4d97f1;
          color: #fff;
        }
      }
      .switch-group {
        margin-left: 50px;
        display: inline-flex;
        .button-d {
          color: #8d8d8d;
          width: 45px;
          height: 40px;
          background-color: #fcfcfc;
          border: 1px solid #6e6e6e;
          border-radius: 0px 5px 5px 0px;
        }
        .button-m {
          color: #8d8d8d;
          width: 45px;
          height: 40px;
          background-color: #fcfcfc;
          border: 1px solid #6e6e6e;
          border-radius: 5px 0px 0px 5px;
        }
        .button-m:active {
          border: 1px solid #4d97f1;
          color: #4d97f1;
        }
        .button-m:visited {
          border: 1px solid #4d97f1;
          color: #4d97f1;
        }
        .button-d:active {
          border: 1px solid #4d97f1;
          color: #4d97f1;
        }
        .button-d:visited {
          border: 1px solid #4d97f1;
          color: #4d97f1;
        }
        .active {
          border: 1px solid #4d97f1;
          color: #4d97f1;
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
        animation-duration: 0.5s;
        li,
        .li {
          list-style-type: none;
          display: inline-flex;
          width: 25%;
          margin-top: 15px;
          margin-bottom: 10px;
          font-size: 16px;
          color: #777;
          cursor: pointer;
          text-align: center;
          justify-content: center;
          align-content: center;

          div {
            text-align: center;
            width: 55px;
            padding: 10px;
            border-radius: 90px 90px 90px 90px;
            user-select: none;
          }
          .selected {
            background: #448add;
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
          background-color: #57a5f8;
          color: #fff;
        }
        div:hover:not(.selected) {
          background: #448add;
        }
        li:nth-child(13):hover > div,
        li:nth-child(14):hover > div,
        li:nth-child(15):hover > div,
        li:nth-child(16):hover > div {
          background-color: #a2cefd;
          color: #fff;
        }
      }
      .date_picker {
        padding: 5px;
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
          animation-name: opacity;
          animation-duration: 0.5s;
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
          bottom: 0px;
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
    10% {
      opacity: 10%;
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
</style>
