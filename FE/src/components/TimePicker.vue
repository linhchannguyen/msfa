<template>
  <div class="form-icon icon-left form-icon--noBod timepicker" :class="disabled ? 'disabledForm' : ''">
    <span class="icon">
      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_time.svg')" alt="" />
    </span>
    <div class="timepicker_control">
      <div class="timepicker_control" style="padding: 0" @click="visibleDropdown">
        <div class="timepicker_control-item">
          <span v-show="time_start">{{ time_start }}</span>
          <span v-show="!time_start" class="text-holder">開始</span>
        </div>
        <div class="itemseg">～</div>
        <div class="timepicker_control-item">
          <span v-show="time_end">{{ time_end }}</span>
          <span v-show="!time_end" class="text-holder">終了</span>
        </div>
      </div>

      <div v-show="clearable" class="timepicker_control-close">
        <i class="el-icon-circle-close" @click="clearTime()"></i>
      </div>
    </div>
    <div :class="['timepicker_dropdown display-none']">
      <div class="col-start">
        <div class="title">開始時間</div>
        <div class="content">
          <div class="scrollTime scrollbarTime hour">
            <ul>
              <li
                v-for="hour in lstHours"
                :key="hour"
                :class="[hour == +valHourStart ? 'active' : '', lstHourStartDisabled.includes(hour) ? 'disabled' : '']"
                @click="selectedHourStart(hour)"
              >
                {{ hour }}
              </li>
            </ul>
          </div>
          <div class="scrollTime scrollbarTime minute">
            <ul>
              <li
                v-for="minute in lstMinutes"
                :key="minute"
                :class="[minute == +valMinuteStart ? 'active' : '', lstMinuteStartDisabled.includes(minute) ? 'disabled' : '']"
                @click="selectedMinuteStart(minute)"
              >
                {{ minute }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-end">
        <div class="title">終了時間</div>
        <div class="content">
          <div class="scrollTime scrollbarTime hour">
            <ul>
              <li
                v-for="hour in lstHours"
                :key="hour"
                :class="[hour == +valHourEnd ? 'active' : '', lstHourEndDisabled.includes(hour) ? 'disabled' : '']"
                @click="selectedHourEnd(hour)"
              >
                {{ hour }}
              </li>
            </ul>
          </div>
          <div class="scrollTime scrollbarTime minute">
            <ul>
              <li
                v-for="minute in lstMinutes"
                :key="minute"
                :class="[minute == +valMinuteEnd ? 'active' : '', lstMinuteEndDisabled.includes(minute) ? 'disabled' : '']"
                @click="selectedMinuteEnd(minute)"
              >
                {{ minute }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TimePicker',
  props: {
    modelValue: {
      type: String,
      required: false
    },
    startTime: {
      type: String,
      required: false,
      default() {
        return '';
      }
    },
    endTime: {
      type: String,
      required: false,
      default() {
        return '';
      }
    },
    className: {
      type: String,
      required: false
    },
    disabled: {
      type: Boolean,
      required: false
    },
    clearable: {
      type: Boolean,
      required: true
    }
  },

  emits: ['onResultTimepicker'],
  data() {
    return {
      isReload: true,
      time_start: '',
      time_end: '',
      lstHours: [],
      lstMinutes: [],

      lstHourStartDisabled: [],
      lstMinuteStartDisabled: [],
      lstHourEndDisabled: [],
      lstMinuteEndDisabled: [],

      valHourStart: '',
      valMinuteStart: '',
      valHourEnd: '',
      valMinuteEnd: ''
    };
  },

  updated: function () {
    this.$nextTick(function () {
      if (this.isReload) {
        this.setScrollTime();
      }
    });
  },

  mounted() {
    for (let i = 0; i < 60; i++) {
      let item = i < 10 ? '0' + i : i;
      if (i % 5 === 0) {
        this.lstMinutes.push(item);
      }
      if (i <= 23) {
        this.lstHours.push(item);
      }
    }
    if (this.startTime && this.endTime) {
      let [hourStart, minStart] = this.startTime.split(':');
      let [hourEnd, minEnd] = this.endTime.split(':');

      this.time_start = hourStart + ':' + minStart;
      this.time_end = hourEnd + ':' + minEnd;

      this.valHourStart = hourStart;
      this.valMinuteStart = minStart;
      this.valHourEnd = hourEnd;
      this.valMinuteEnd = minEnd;

      this.disabledTime();
    }
  },

  methods: {
    visibleDropdown() {
      if (!this.time_start && !this.time_end) {
        this.valHourStart = new Date().getHours();
        this.valMinuteStart = +new Date().getMinutes() - (+new Date().getMinutes() % 5);
        this.valHourEnd = new Date().getHours() >= 23 ? new Date().getHours() : new Date().getHours() + 1;
        this.valMinuteEnd = +new Date().getMinutes() - (+new Date().getMinutes() % 5);

        this.time_start = this.valHourStart + ':' + (+this.valMinuteStart < 10 ? '0' + this.valMinuteStart : this.valMinuteStart);
        this.time_end = this.valHourEnd + ':' + (+this.valMinuteEnd < 10 ? '0' + this.valMinuteEnd : this.valMinuteEnd);
      }

      this.setScrollTime();
      this.disabledTime();

      this.onResult();
    },

    setScrollTime() {
      setTimeout(() => {
        if (this.time_start) {
          setTimeout(() => {
            let elUl = document.querySelector('.col-start .scrollTime.hour');
            let el = document.querySelector('.col-start .scrollTime.hour li.active');
            if (elUl && el) {
              elUl.scrollTop = el.offsetTop - 40;
            }

            let elUlm = document.querySelector('.col-start .scrollTime.minute');
            let elm = document.querySelector('.col-start .scrollTime.minute li.active');
            if (elUlm && elm) {
              elUlm.scrollTop = elm.offsetTop - 40;
            }
          }, 1);
        }
        if (this.time_end) {
          setTimeout(() => {
            let elUl = document.querySelector('.col-end .scrollTime.hour');
            let el = document.querySelector('.col-end .scrollTime.hour li.active');
            if (elUl && el) {
              elUl.scrollTop = el.offsetTop - 40;
            }

            let elUlm = document.querySelector('.col-end .scrollTime.minute');
            let elm = document.querySelector('.col-end .scrollTime.minute li.active');
            if (elUlm && elm) {
              elUlm.scrollTop = elm.offsetTop - 40;
            }
          }, 1);
        }
      }, 1);
    },

    selectedHourStart(val) {
      this.isReload = false;
      if (val !== this.valHourStart) {
        this.valHourStart = val;
        if (this.time_start) {
          let [hourStart, minStart] = this.time_start.split(':');
          if (+this.valHourStart === +this.valHourEnd && +this.valMinuteStart > +this.valMinuteEnd) {
            minStart = this.valMinuteEnd;
            this.valMinuteStart = this.valMinuteEnd;
          }
          hourStart = this.valHourStart;
          this.time_start = hourStart + ':' + minStart;
        } else {
          this.time_start = this.valHourStart + ':';
        }

        setTimeout(() => {
          this.setScrollTime();
          this.onResult();
        }, 1);
        this.disabledTime();
      }
    },

    selectedMinuteStart(val) {
      this.isReload = false;
      this.valMinuteStart = val;
      if (this.time_start) {
        let [hourStart, minStart] = this.time_start.split(':');
        minStart = this.valMinuteStart;
        this.time_start = hourStart + ':' + minStart;
      } else {
        this.time_start = ':' + this.valMinuteStart;
      }

      setTimeout(() => {
        this.setScrollTime();
        this.onResult();
      }, 1);
      this.disabledTime();
    },
    //

    selectedHourEnd(val) {
      this.isReload = false;
      this.valHourEnd = val;
      if (this.time_end) {
        let [hourEnd, minEnd] = this.endTime.split(':');
        hourEnd = this.valHourEnd;
        if (+this.valHourStart === +this.valHourEnd && +this.valMinuteStart > +this.valMinuteEnd) {
          minEnd = this.valMinuteStart;
          this.valMinuteEnd = this.valMinuteStart;
        }
        this.time_end = hourEnd + ':' + minEnd;
      } else {
        this.time_end = this.valHourEnd + ':';
      }

      setTimeout(() => {
        this.setScrollTime();
        this.onResult();
      }, 1);
      this.disabledTime();
    },

    selectedMinuteEnd(val) {
      this.isReload = false;
      this.valMinuteEnd = val;
      if (this.time_end) {
        let [hourEnd, minEnd] = this.endTime.split(':');
        minEnd = this.valMinuteEnd;
        this.time_end = hourEnd + ':' + minEnd;
      } else {
        this.time_end = ':' + this.valMinuteEnd;
      }

      setTimeout(() => {
        this.setScrollTime();
        this.onResult();
      }, 1);
      this.disabledTime();
    },

    // type: start, end
    // timeType: hour, minute

    setDisabled(type, timeType, hour, minute) {
      switch (type) {
        case 'start':
          switch (timeType) {
            case 'hour':
              this.lstHourEndDisabled = [];

              for (let item of this.lstHours) {
                if (+item < hour) {
                  this.lstHourEndDisabled.push(item);
                }
              }

              break;
            case 'minute':
              this.lstMinuteEndDisabled = [];
              if (+this.valHourStart < +this.valHourEnd) {
                this.lstMinuteEndDisabled = [];
              } else {
                if (+this.valHourStart === +this.valHourEnd) {
                  for (let item of this.lstMinutes) {
                    if (+item < minute) {
                      this.lstMinuteEndDisabled.push(item);
                    }
                  }
                } else {
                  this.lstMinuteEndDisabled = this.lstMinutes;
                }
              }

              break;

            default:
              break;
          }

          break;
        case 'end':
          switch (timeType) {
            case 'hour':
              this.lstHourStartDisabled = [];

              for (let item of this.lstHours) {
                if (+item > hour) {
                  this.lstHourStartDisabled.push(item);
                }
              }

              break;
            case 'minute':
              this.lstMinuteStartDisabled = [];
              if (+this.valHourStart < +this.valHourEnd) {
                this.lstMinuteStartDisabled = [];
              } else {
                if (+this.valHourStart === +this.valHourEnd) {
                  for (let item of this.lstMinutes) {
                    if (+item > minute) {
                      this.lstMinuteStartDisabled.push(item);
                    }
                  }
                } else {
                  this.lstMinuteStartDisabled = this.lstMinutes;
                }
              }

              break;

            default:
              break;
          }

          break;

        default:
          break;
      }
    },

    disabledTime() {
      this.setDisabled('start', 'hour', this.valHourStart, this.valMinuteStart);
      this.setDisabled('start', 'minute', this.valHourStart, this.valMinuteStart);
      this.setDisabled('end', 'hour', this.valHourEnd, this.valMinuteEnd);
      this.setDisabled('end', 'minute', this.valHourEnd, this.valMinuteEnd);
    },

    clearTime() {
      this.time_start = '';
      this.time_end = '';
      this.valHourStart = '';
      this.valMinuteStart = '';
      this.valHourEnd = '';
      this.valMinuteEnd = '';
      this.onResult();
    },

    onResult() {
      this.$emit('onResultTimepicker', this.time_start, this.time_end);
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
@mixin scroll($bgThumb, $bg, $height: 10px, $width: 10px) {
  &::-webkit-scrollbar {
    width: $height;
    height: $width;
    cursor: pointer;
  }

  /* Track */
  &::-webkit-scrollbar-track {
    @if $bg {
      background: $bg;
    } @else {
      background: $bgThumb;
    }
    cursor: pointer;
  }

  /* Handle */
  &::-webkit-scrollbar-thumb {
    background: $bgThumb;
    cursor: pointer;
  }

  /* Handle on hover */
  &::-webkit-scrollbar-thumb:hover {
    background: #868787;
    cursor: pointer;
  }
}

.scrollbarTime {
  overflow: auto;
  height: 100%;
  @include scroll(transparent, transparent, 5px, 5px);
}

.timepicker {
  display: flex;
  align-items: center;
  border: 1px solid #727f88;
  color: #727f88;
  border-radius: 4px;
  height: 40px;
  background: #fff;

  &_control {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    flex-grow: 1;
    gap: 7px;
    padding-left: 30px;
    color: #727f88;
    cursor: pointer;
    padding-right: 25px;

    &-item {
      width: 100%;
      height: 40px;
      display: flex;
      align-content: center;
      align-items: center;
      justify-content: center;

      .itemseg {
        flex: 0 0 20px;
        text-align: center;
      }
    }

    &-close {
      opacity: 0;
      color: #bababb;
      margin-right: -15px;
    }
  }

  &:hover {
    .timepicker_control {
      &-close {
        opacity: 1;
      }
    }
  }
  .display {
    display: flex;
  }
  .display-none {
    display: none;
  }
  &_dropdown {
    position: absolute;
    top: 45px;
    left: calc(50% - 115px);
    background: #fff;
    border: 1px solid #d2d2d282;
    height: 240px;
    width: 230px;
    z-index: 1000;
    border-radius: 5px;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    display: flex;
    gap: 20px;
    padding: 10px;
    justify-content: center;
    align-content: center;
    flex-wrap: nowrap;
    align-items: flex-start;

    &:hover {
      .scrollbarTime {
        @include scroll(#cacdd1, transparent, 5px, 5px);
      }
    }

    &::after {
      content: '';
      position: absolute;
      top: -8px;
      left: calc(50% - 7px);
      border-left: 8px solid transparent;
      border-right: 8px solid transparent;
      border-bottom: 8px solid #fff;
    }

    .col-start,
    .col-end {
      flex: 0 0 calc(50% - 10px);
      text-align: center;
      .content {
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
        .scrollTime {
          flex: 0 0 50%;
          height: 195px;

          ul {
            padding: 0 5px;
            li {
              height: 28px;
              line-height: 28px;
              cursor: pointer;

              &:hover {
                background: #7c878f14;
              }

              &.active {
                color: #448add;
                font-weight: bold;
                background: #828e963d;
              }

              &.disabled {
                cursor: default;
                opacity: 0.5;
                pointer-events: none;
              }
            }
          }
        }
      }
      .title {
        font-size: 14px;
        height: 25px;
        line-height: 25px;
        color: #606266;
        margin-bottom: 5px;
      }
    }
  }

  &.disabledForm {
    cursor: not-allowed;
    pointer-events: none;
    opacity: 0.5;
    background: #ebeff5 !important;
    color: #c2ccde !important;
    border-color: #c5ccd4 !important;
  }
}

.text-holder {
  color: #c4c4d4;
}

@media (min-width: $viewport_tablet) and (max-width: $viewport_desktop) {
  .customLeft {
    .timepicker {
      &_dropdown {
        left: calc(50% - 80px) !important;

        &::after {
          left: calc(50% - 40px) !important;
        }
      }
    }
  }
}
</style>
