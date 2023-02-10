<template>
  <div v-load-f5="true" :class="['dropdown-menu dropdown-filter', activeFilter ? 'show' : '']">
    <button class="btn btn-filter background btn-close-filter" type="button" @click="closeFilter" @touchstart="closeFilter">
      <img class="svg" src="@/assets/template/images/icon_filter-white.svg" alt="" />
    </button>
    <h2 class="titleSearch">検索条件</h2>
    <div class="groupSearch">
      <label class="groupSearch-label">通知内容</label>
      <div class="groupSearch-control"><el-input v-model="content" clearable type="text" placeholder="内容入力" /></div>
      <div class="groupSearch-view">
        <label class="custom-control custom-checkbox custom-checkbox-1" :class="archive_flag === true || archive_flag === 'true' ? 'activeInput' : ''">
          <input v-model="archive_flag" class="custom-control-input" type="checkbox" checked />
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">アーカイブのみ表示</span>
        </label>
      </div>
    </div>
    <div class="groupSearch">
      <label class="groupSearch-label">通知日</label>
      <div class="groupSearch-dateTime">
        <div class="dateTime">
          <div class="form-icon icon-left form-icon--noBod">
            <span class="icon">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
            </span>
            <el-date-picker
              v-model="inform_datetime_from"
              format="YYYY/M/D"
              :editable="false"
              :disabled-date="disabledDateStart"
              type="date"
              placeholder="開始日"
            >
            </el-date-picker>
          </div>
        </div>
        <span class="item-label">～</span>
        <div class="dateTime">
          <div class="form-icon icon-left form-icon--noBod">
            <span class="icon">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
            </span>
            <el-date-picker v-model="inform_datetime_to" :editable="false" format="YYYY/M/D" :disabled-date="disabledDateEnd" type="date" placeholder="終了日">
            </el-date-picker>
          </div>
        </div>
      </div>
    </div>
    <div class="groupSearch">
      <label class="groupSearch-label">通知種別</label>
      <div class="groupSearch-channel">
        <ul>
          <li v-for="item of dataListCatelogyInform" :key="item.inform_category_cd">
            <label class="custom-control custom-checkbox" :class="item.checked === 1 ? 'activeInput' : ''">
              <input
                :checked="item.checked === 1 ? true : false"
                class="custom-control-input"
                :value="item.inform_category_cd"
                type="checkbox"
                @change="signalChange"
              />
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">{{ item.inform_category_name }}</span>
            </label>
          </li>
        </ul>
      </div>
    </div>
    <div class="groupSearch-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeFilter">キャンセル</button>
      <button type="button" class="btn btn-primary btn-filter-submit" @click="submit()">検索</button>
    </div>
  </div>
</template>

<script>
// import Z02_S03_NotificationServices from '../../../api/Z02/Z02_S03_NotificationServices';
import moment from 'moment';
export default {
  name: 'ModalNotificationFilter',
  props: {
    activefilter: {
      type: Boolean,
      require: true
    }
  },
  emits: ['closeFilter', 'loadData'],

  data() {
    return {
      dataListCatelogyInform: [],
      inform_datetime_from: '',
      inform_datetime_to: '',
      tempArray: [],
      arrUncheck: [],
      archive_flag: false,
      arrayInformCatelogy: [],
      content: ''
    };
  },
  created() {
    this.moment = moment;
  },
  mounted() {
    this.getDataListCatelogyInform();
  },

  methods: {
    disabledDateStart(time) {
      if (this.inform_datetime_to) {
        return time.getTime() > new Date(this.inform_datetime_to).getTime();
      }
    },

    disabledDateEnd(time) {
      if (this.inform_datetime_from) {
        return time.getTime() < new Date(this.inform_datetime_from).getTime();
      }
    },
    getDataListCatelogyInform() {
      setTimeout(() => {
        if (localStorage.getItem('dataListCatelogyInform')) {
          this.dataListCatelogyInform = JSON.parse(localStorage.getItem('dataListCatelogyInform'));
          this.dataListCatelogyInform.forEach((element) => {
            if (element.checked === 0) {
              this.arrUncheck.push(element.inform_category_cd);
            }
          });
          this.archive_flag = localStorage.getItem('archive_flag');
          this.inform_datetime_from = localStorage.getItem('inform_datetime_from');
          this.inform_datetime_to = localStorage.getItem('inform_datetime_to');
          this.content = localStorage.getItem('inform_contents');
        }
      }, 1000);
    },
    closeFilter() {
      this.$emit('closeFilter', false);
    },
    signalChange(evt) {
      this.tempArray = [];
      if (!evt.target.checked && !this.arrUncheck.includes(evt.target.value)) {
        this.arrUncheck.push(evt.target.value);
      } else {
        this.arrUncheck.splice(this.arrUncheck.indexOf(evt.target.value), 1);
      }
      this.dataListCatelogyInform.forEach((element) => {
        if (!this.arrUncheck.includes(element.inform_category_cd)) {
          this.tempArray.push(element.inform_category_cd);
        }
      });
      this.dataListCatelogyInform.forEach((element) => {
        if (element.inform_category_cd === evt.target.value) {
          if (element.checked === 1) {
            element.checked = 0;
          } else {
            element.checked = 1;
          }
        }
      });
    },
    submit() {
      if (this.inform_datetime_from === null) {
        this.inform_datetime_from = '';
      }
      if (this.inform_datetime_to === null) {
        this.inform_datetime_to = '';
      }
      this.dataListCatelogyInform.forEach((element, index) => {
        if (this.arrUncheck.includes(element.inform_category_cd)) {
          this.dataListCatelogyInform[index].checked = 0;
        } else {
          this.dataListCatelogyInform[index].checked = 1;
        }
      });
      localStorage.setItem('dataListCatelogyInform', JSON.stringify(this.dataListCatelogyInform));
      localStorage.setItem('archive_flag', this.archive_flag ? this.archive_flag : false);
      localStorage.setItem(
        'inform_datetime_from',
        this.inform_datetime_from ? moment(this.inform_datetime_from ? this.inform_datetime_from : '').format('YYYY/M/D') : ''
      );
      localStorage.setItem(
        'inform_datetime_to',
        this.inform_datetime_to ? moment(this.inform_datetime_to ? this.inform_datetime_to : '').format('YYYY/M/D') : ''
      );
      localStorage.setItem('inform_contents', this.content ? this.content : '');
      this.dataListCatelogyInform.forEach((element) => {
        if (element.checked === 1) {
          this.arrayInformCatelogy.push(element.inform_category_cd);
        }
      });
      this.closeFilter();
      this.$emit('loadData');
    }
  }
};
</script>

<style lang="scss" scoped>
.activeInput {
  border: 1px solid #28a470 !important;
  .custom-control-description {
    color: #28a470 !important;
  }
}
.custom-checkbox-1 {
  width: unset !important;
}
.custom-checkbox {
  border: 1px solid #727f88;
  padding: 7px 12px 7px 31px;
  border-radius: 5px;
  width: 100%;
  .control-indicator,
  .custom-control-indicator {
    margin-left: 7px;
    .custom-control-description {
      color: #5f6b73;
    }
  }
}
.groupSearch-channel {
  max-height: 200px;
  overflow-y: auto;
}
.dropdown-filter {
  z-index: 99 !important;
  top: 0;
  .btn-filter {
    background: #448add !important;
    position: absolute;
    top: 0;
    right: 0;
    border-radius: 0px 0px 0px 8px !important;
  }
}
.groupSearch-dateTime {
  width: 100%;
  position: relative;
  .iconStartDate {
    position: absolute;
    z-index: 9;
    right: 55%;
    top: 7px;
    border-left: 1px solid;
    padding: 6px 0px 6px 7px;
  }
  .iconEndDate {
    position: absolute;
    z-index: 9;
    right: 0;
    right: 17px;
    top: 7px;
    border-left: 1px solid;
    padding: 6px 0px 6px 7px;
  }
}
</style>
