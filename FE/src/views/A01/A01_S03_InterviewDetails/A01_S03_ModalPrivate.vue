<template>
  <div class="custom-body modal-body-A01S03-Private">
    <div id="msfa-notify-A01S03-Private"></div>
    <div class="headInterview">
      <ul>
        <li class="dateTime">
          <span class="dateTime-item">
            <ImageSVG :src-image="'icon_calendar-check01.svg'" :alt-image="'icon_calendar-check01'" />
          </span>
          <span>{{ formatFullDateCustom(date) }}</span>
        </li>
        <li v-if="!all_day_flag" class="interview-label">
          <span>{{ formatTimeHourMinute(startTime) }}</span>
          <span>～</span>
          <span>{{ formatTimeHourMinute(endTime) }}</span>
        </li>
      </ul>
    </div>
    <div
      v-loading="isLoading"
      class="interviewForm"
      :class="(isSubmit && !validation.title.required) || (isSubmit && !validation.title.length) || responseErrors.title ? 'hasErr' : ''"
    >
      <div class="interviewGroup-position">
        <label v-if="edit_flg" class="interview-form__label">タイトル</label>
        <label v-else class="interview-form__label-view">タイトル</label>
        <el-input v-if="edit_flg" v-model="initData.title" clearable placeholder="タイトル入力" class="form-control-input" />
        <span v-else class="mode-view">{{ title }}</span>
        <span v-if="isSubmit && !validation.title.required" class="text-error">{{ getMessage('MSFA0001', 'タイトル') }}</span>
        <span v-if="isSubmit && !validation.title.length" class="text-error">{{ getMessage('MSFA0012', 'タイトル', 30) }}</span>
        <span v-else class="text-error">{{ responseErrors.title }}</span>
      </div>
      <div class="interviewGroup-datetime">
        <label v-if="edit_flg" class="interview-form__label">日時</label>
        <label v-else class="interview-form__label-view">日時</label>
        <div v-if="edit_flg" class="interviewGroup-datetime-content">
          <div class="interview-form__date">
            <div class="interviewGroup-dateTime">
              <div
                class="form-icon icon-left form-icon--noBod"
                :class="(isSubmit && !validation.start_date.required) || responseErrors.start_date ? 'hasErr' : ''"
              >
                <span class="icon">
                  <ImageSVG :src-image="'icon-calender-control.svg'" :alt-image="'icon-calender-control'" />
                </span>
                <el-date-picker
                  v-model="initData.start_date"
                  format="YYYY/M/D"
                  type="date"
                  :editable="false"
                  placeholder="日付選択"
                  class="form-control-datePickerLeft"
                  @change="selectDate"
                />
              </div>
              <span v-if="isSubmit && !validation.start_date.required" class="text-error">{{ getMessage('MSFA0040', '日時') }}</span>
              <span v-else class="text-error">{{ responseErrors.start_date }}</span>
            </div>
          </div>
          <div class="interviewGroup-checkBox">
            <label class="custom-control custom-checkbox custom-control--bordGreen">
              <input v-model="initData.all_day_flag" class="custom-control-input" type="checkbox" checked @change="setDataTimepicker" />
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">終日</span>
            </label>
          </div>
        </div>
        <div v-else class="mode-view">
          <span>{{ formatFullDate(initData.start_date) }}</span>
          <span v-if="!initData.all_day_flag">{{ start_time + '～' + end_time }}</span>
        </div>
      </div>

      <div
        v-if="edit_flg"
        class="interview-form__time"
        :class="
          (isSubmit && !validation.start_time.required) || (isSubmit && !validation.end_time.required) || responseErrors.start_time || responseErrors.end_time
            ? 'hasErr'
            : ''
        "
      >
        <TimePicker
          :key="keyComponentTimepicker"
          :start-time="initData.start_time"
          :end-time="initData.end_time"
          :disabled="initData.all_day_flag"
          @onResultTimepicker="onResultTimepicker"
        ></TimePicker>
        <div v-if="initData.all_day_flag === false">
          <span v-if="isSubmit && !validation.start_time.required" class="text-error">{{ getMessage('MSFA0040', '日時') }}</span>
          <span v-else-if="isSubmit && !validation.end_time.required" class="text-error">{{ getMessage('MSFA0040', '日時') }}</span>
          <span v-if="responseErrors.start_time" class="text-error">{{ responseErrors.start_time }}</span>
          <span v-else class="text-error">{{ responseErrors.end_time }}</span>
        </div>
      </div>

      <div v-if="edit_flg" class="interviewGroup-textarea" :class="edit_flg && isSubmit && !validation.remarks.length ? 'hasErr' : ''">
        <label class="interview-form__label">備考</label>
        <textarea v-model="initData.remarks" clearable class="form-control form-control-textarea" placeholder="内容入力" rows="6"></textarea>
        <span v-if="isSubmit && !validation.remarks.length" class="text-error"> {{ getMessage('MSFA0012', '備考', 200) }}</span>
      </div>

      <div v-else class="interviewGroup-textarea">
        <label class="interview-form__label-view">備考</label>
        <p style="white-space: break-spaces" class="mode-view">{{ initData.remarks }}</p>
      </div>

      <div class="interviewGroup-btn">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <el-button
          v-if="edit_flg"
          type="primary"
          class="btn btn-outline-primary btn-outline-primary--cancel"
          :loading="processing"
          @click="
            openModal({
              screenID: 'PopupConfirm',
              width: '35rem',
              classs: 'custom-dialog modal-fixed modal-fixed-min',
              props: {
                params: {},
                title: '選択したプライベートを完全に削除しますか？',
                message: '削除すると元に戻すことはできません。'
              }
            })
          "
        >
          削除
        </el-button>
        <el-button v-if="edit_flg" type="primary" class="btn btn-primary" :loading="processing" @click.prevent="saveBtn">保存</el-button>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="reloadAction(closeModal, 'reload')($event)"
          @handleConfirm="deleteBtn"
          @handleYes="handleConfirmYes"
        />
      </template>
    </el-dialog>
  </div>
</template>
<script>
import A01_S03_Service from '@/api/A01/A01_S03_InterviewDetailsServices';
import { required, validLength } from '@/utils/validate';
import { markRaw } from 'vue';
import _ from 'lodash';
export default {
  name: 'Private',
  props: {
    scheduleId: {
      type: Number,
      default: 4,
      required: true
    },
    date: {
      type: String,
      default: '2021/12/26',
      require: true
    },
    startTime: {
      type: String,
      default: '2021/12/26 09:00:00',
      require: true
    },
    endTime: {
      type: String,
      default: '2021/12/26 10:00:00',
      require: true
    },
    all_day_flag: {
      type: Boolean,
      default: false,
      require: false
    },
    editFlagProps: {
      type: Boolean,
      default: false,
      require: false
    },
    isEditFlag: {
      type: Number,
      default: 1,
      require: true
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      initData: {
        private_id: 0,
        title: '',
        start_date: '',
        start_time: '',
        end_time: '',
        all_day_flag: true,
        remarks: ''
      },

      dataDefault: {},

      isLoading: true,
      processing: false,
      edit_flg: true,
      start_time: '',
      end_time: '',
      start_time_default: '',
      end_time_default: '',
      keyComponentTimepicker: Math.random()
    };
  },
  computed: {
    validation() {
      return {
        title: { required: required(this.initData.title), length: validLength(this.initData.title, 30) },
        start_date: { required: required(this.initData.start_date) },
        start_time: this.initData.all_day_flag ? true : { required: required(this.initData.start_time) },
        end_time: this.initData.all_day_flag ? true : { required: required(this.initData.end_time) },
        remarks: { length: validLength(this.initData.remarks, 200) }
      };
    }
  },
  mounted() {
    this.isLoading = true;
    this.edit_flg = this.editFlagProps || false;
    this.privateSchedule(this.scheduleId);
  },
  methods: {
    // define func
    cancelBtn() {
      this.$emit('onFinishScreen', { screen: 'A01_S03_Private' });
      this.resetData();
    },

    confirmCancel() {
      let isEqual = !_.isEqual(this.initData, this.dataDefault);

      if (isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1 },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.handleConfirmYes();
      }
    },

    handleConfirmYes() {
      this.$emit('onFinishScreen');
    },

    saveBtn() {
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-A01S03-Private',
          idNodeNotify: 'msfa-notify-A01S03-Private'
        });
        return;
      }
      let obj = {
        start_time: '',
        end_time: ''
      };
      if (this.initData.all_day_flag && this.initData.start_date) {
        obj = {
          start_time: `${this.initData.start_date} 0:00:00`,
          end_time: `${this.initData.start_date} 23:59:59`
        };
      } else if (this.initData.start_date && this.initData.start_time && this.initData.end_time) {
        obj = {
          start_time: `${this.initData.start_date} ${this.initData.start_time}`,
          end_time: `${this.initData.start_date} ${this.initData.end_time}`
        };
      }
      this.savePrivate({
        ...this.initData,
        all_day_flag: this.initData.all_day_flag ? 1 : 0,
        ...obj,
        schedule_id: this.scheduleId
      });
    },

    deleteBtn() {
      this.modalConfig.isShowModal = false;
      this.deletePrivate({
        schedule_id: this.scheduleId,
        private_id: this.initData.private_id
      });
    },

    setDataTimepicker() {
      if (!this.initData.all_day_flag) {
        this.initData = {
          ...this.initData,
          start_time: this.start_time_default,
          end_time: this.end_time_default
        };
      }

      this.keyComponentTimepicker = Math.random();
    },

    onResultTimepicker(startTime, endTime) {
      if (startTime && endTime) {
        this.initData = {
          ...this.initData,
          start_time: startTime,
          end_time: endTime
        };
      } else {
        this.initData.all_day_flag = true;
      }
    },

    selectDate(date) {
      this.initData = {
        ...this.initData,
        start_date: date ? this.formatFullDate(date) : ''
      };
    },

    closeModal() {
      this.modalConfig.isShowModal = false;
    },

    openModal({ screenID, title = '', width = 0, props = {}, classs = 'custom-dialog' }) {
      let objScreen = {
        PopupConfirm: this.$PopupConfirm
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: title,
        isShowModal: true,
        customClass: classs,
        component: this.markRaw(objScreen[screenID]),
        props: props,
        width: width,
        isShowClose: false
      };
    },

    // call api
    privateSchedule(params) {
      this.isLoading = true;
      A01_S03_Service.privateScheduleService({ schedule_id: params })
        .then((res) => {
          const dataRes = res.data.data;
          if (Object.keys(dataRes).length > 0 || dataRes.length > 0) {
            this.initData = {
              ...dataRes,
              all_day_flag: dataRes.all_day_flag === 1,
              start_date: this.formatFullDate(dataRes.start_date),
              start_time: this.formatTimeHourMinute(dataRes.start_time),
              end_time: this.formatTimeHourMinute(dataRes.end_time)
            };
            this.dataDefault = { ...this.initData };

            this.start_time = this.formatTimeHourMinute(dataRes.start_time);
            this.end_time = this.formatTimeHourMinute(dataRes.end_time);

            this.start_time_default = dataRes.all_day_flag === 1 ? this.start_time_default : this.start_time;
            this.end_time_default = dataRes.all_day_flag === 1 ? this.end_time_default : this.end_time;

            if (this.isEditFlag === 0) {
              const userLogin = this.$store.state.auth.currentUser.user_cd;
              this.edit_flg = userLogin === dataRes.user_cd;
            }

            this.keyComponentTimepicker = Math.random();
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A01S03-Private',
            idNodeNotify: 'msfa-notify-A01S03-Private'
          });
        })
        .finally(() => (this.isLoading = false));
    },
    savePrivate(params) {
      this.processing = true;
      A01_S03_Service.savePrivateService(params)
        .then((res) => {
          this.$notify({ message: res.data.message || '正常に保存しました。', customClass: 'success' });

          this.cancelBtn();
        })
        .catch((err) => {
          this.processing = false;
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A01S03-Private',
            idNodeNotify: 'msfa-notify-A01S03-Private'
          });
        })
        .finally(() => {
          this.processing = false;
        });
    },
    deletePrivate(params) {
      this.processing = true;
      A01_S03_Service.deletePrivateService(params)
        .then((res) => {
          this.$notify({ message: res.data.message || '正常に削除しました。', customClass: 'success' });
          this.cancelBtn();
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A01S03-Private',
            idNodeNotify: 'msfa-notify-A01S03-Private'
          });
        })
        .finally(() => {
          this.processing = false;
          localStorage.setItem('reload', 1);
        });
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.interview-form__label-view {
  color: #99a5ae;
}
.mode-view {
  color: #606266;
  span {
    margin-right: 15px;
  }
}
.custom-body {
  margin: -32px;
}
.interview-form__time {
  width: calc(34% - 1px);
  margin-bottom: 24px;
  line-height: 40px;
}
.headInterview {
  box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
  padding: 20px 32px;
  ul {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    li {
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
      &.dateTime {
        font-size: 1.5rem;
        font-weight: 700;
        color: #000;
        display: flex;
        .dateTime-item {
          min-width: 24px;
          margin: -2px 12px 0 0;
        }
      }
      &.interview-label {
        font-size: 16px;
        display: flex;
      }
    }
  }
}
.interviewForm {
  padding: 32px;
  .interview-form__label {
    margin-bottom: 6px;
  }
  .interviewGroup-position {
    margin-bottom: 24px;
  }
  .interviewGroup-datetime {
    margin-bottom: 24px;
    .interviewGroup-datetime-content {
      display: flex;
      .interview-form__date {
        width: calc(34% - 1px);
        margin-right: 20px;
      }
      .interview-form__time {
        width: calc(34% - 1px);
      }
    }
  }
  .interviewGroup-checkBox {
    margin-top: 4px;
  }

  .interviewGroup-textarea {
    margin-bottom: 20px;
  }

  .interviewGroup-btn {
    text-align: center;
    .btn {
      width: 180px;
      &:nth-child(2) {
        margin-left: 24px;
        margin-right: 24px;
      }
    }
  }
}
</style>
