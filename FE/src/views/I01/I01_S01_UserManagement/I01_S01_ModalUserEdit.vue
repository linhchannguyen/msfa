<template>
  <!-- Start  -->
  <div class="modal-userEdit modal-body-I01S01-UserEdit">
    <div id="msfa-notify-I01S01-UserEdit"></div>
    <div class="form-userEdit">
      <ul>
        <li>
          <label class="form-userEdit-label">
            適用期間<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <div class="form-userEdit-control form-userEdit--flex">
            <div class="form-dateTime">
              <div class="form-icon icon-left form-icon--noBod" :class="isSubmit && !validation.startDate.required ? 'hasErr' : ''">
                <span class="icon">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                </span>
                <el-date-picker
                  v-model="startDate"
                  :disabled-date="setDateInMouted"
                  format="YYYY/M/D"
                  type="date"
                  :editable="false"
                  :clearable="false"
                  placeholder="開始日"
                  class="form-control-datePickerLeft"
                ></el-date-picker>
              </div>
            </div>
            <div class="form-dateTime-label">
              <span>～</span>
              <span v-if="typeModal === 'isEdit'">{{ endDate === '' ? '未定' : endDate }}</span>
              <span v-if="typeModal === 'createIsRecord' || typeModal === 'isNewCreate'">未定</span>
            </div>
          </div>
          <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.startDate.required">{{ validateMessages.startDate.required }}</span>
          </div>
        </li>
        <li>
          <label class="form-userEdit-label">
            氏名<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <div class="form-userEdit-control" :class="(isSubmit && !validation.userName.required) || validateUserName() ? 'hasErr' : ''">
            <el-input v-model="userName" clearable placeholder="名前入力" class="form-control-input" @change="validateUserName" />
          </div>
          <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.userName.required">{{ validateMessages.userName.required }}</span>
            <span v-if="validateUserName()" class="invalid">{{ getMessage('MSFA0012', '氏名', 50) }}</span>
          </div>
        </li>
        <li>
          <label class="form-userEdit-label">
            メールアドレス<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <div class="form-userEdit-control" :class="(isSubmit && !validation.userEmail.required) || validateuserEmail() ? 'hasErr' : ''">
            <!-- style="text-transform: lowercase" -->
            <el-input v-model="userEmail" clearable placeholder="メール入力" class="form-control-input" @change="validateuserEmail" />
          </div>
          <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.userEmail.required">{{ validateMessages.userEmail.required }}</span>
            <span v-if="validateuserEmail()" class="invalid">{{ getMessage('MSFA0012', 'メールアドレス', 255) }}</span>
          </div>
        </li>
        <li>
          <label class="form-userEdit-label">
            アカウントロック<span v-if="activeLockAcount === 1" class="required-start">
              <ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" />
            </span>
          </label>
          <div class="form-userEdit-control form-userEdit--flex form-userEdit--flex-custom">
            <div class="form-btnLock" @click="lockAcount">
              <button v-if="activeLockAcount === 1" class="btn btn-custom-lock">
                <img v-svg-inline svg-inline class="svg img-custom" :src="require('@/assets/template/images/icon-unlock.svg')" alt="" />
              </button>
              <button v-if="activeLockAcount === 0" class="btn btn-custom-lock">
                <img v-svg-inline svg-inline class="svg img-custom" :src="require('@/assets/template/images/icon-lock.svg')" alt="" />
              </button>
            </div>
            <Transition name="slide-fade" class="form-accountLock">
              <el-input v-if="activeLockAcount === 1" v-model="reason" clearable placeholder="理由入力" class="form-control-input" @input="change" />
            </Transition>
            <div class="invalid-feedback">
              <!-- <span class="invalid" style="margin-left: 54px">{{ 'アカウントロック200文字以下にしてください。' }}</span> -->
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="userEdit-btn">
      <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
      <button v-if="typeModal === 'isEdit'" :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="clickDelete">
        予約削除
      </button>
      <button :disabled="checkDisableBtn() || disableReason || loading" type="button" class="btn btn-primary customBtn" @click="submitModal()">
        <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
      </button>
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
          @onFinishScreen="onResultModal"
          @handleConfirm="deleteMessage"
          @handleYes="handleYes"
          @overWrite="overWriteData"
          @comfirmLockAcc="comfirmLockAcc"
        ></component>
      </template>
    </el-dialog>
  </div>
  <!-- End -->
</template>

<script>
import _ from 'lodash';
import moment from 'moment';
import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/I01/I01_S01_ModalUserEdit';
import { markRaw } from 'vue';
import dialogOverwriteComfirm from '../dialog_overwriteComfirm.vue';
import dialogComfirmLockAccount from '../dialog_comfirmLockAccount.vue';
export default {
  name: 'I01_S01_ModalUserEdit',
  components: {
    dialogOverwriteComfirm,
    dialogComfirmLockAccount
  },
  props: {
    typeModal: {
      type: String,
      default: ''
    },
    record: {
      type: Object,
      default() {}
    },
    numberModal: {
      type: Number,
      default: 0
    },
    isCurrentDateinArr: {
      type: Boolean,
      default: false
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      curentDate: '',
      loading: false,
      validateMessages,
      activeLockAcount: 0,
      startDate: '',
      endDate: '',
      userName: '',
      userEmail: '',
      reason: '',
      disableReason: false,
      dataTheFirt: {},
      isOverWriteData: false,
      modalConfig: {
        title: '',
        customClass: 'custom-dialog z05-s01',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      }
    };
  },

  computed: {
    validation() {
      return {
        startDate: { required: required(this.startDate) },
        userName: { required: required(this.userName) },
        userEmail: { required: required(this.userEmail) }
      };
    }
  },
  mounted() {
    this.setTheFirstData();
    this.curentDate = this.formatFullDate(new Date());
  },
  methods: {
    validateUserName() {
      let userName = this.userName;
      if (userName.length > 50) {
        return true;
      }
      return false;
    },
    validateuserEmail() {
      let userEmail = this.userEmail;
      if (userEmail.length > 255) {
        return true;
      }
      return false;
    },
    // validateAccountLock() {
    //   let reason = this.reason;
    //   if (reason.length > 200) {
    //     return true;
    //   }
    //   return false;
    // },
    checkDisableBtn() {
      if (this.validateUserName() || this.validateuserEmail()) {
        return true;
      }
      return false;
    },
    setTheFirstData() {
      this.dataTheFirt = {
        activeLockAcount: this.record.account_lock_flag === null ? 0 : this.record.account_lock_flag,
        startDate: this.typeModal === 'isEdit' ? this.record.start_date : this.tomorrow(),
        endDate: this.typeModal === 'isEdit' ? this.record.end_date : '',
        userName: this.record.user_name,
        userEmail: this.record.mail_address,
        reason: this.record.account_lock_remarks
      };
      this.activeLockAcount = this.record.account_lock_flag === null ? 0 : this.record.account_lock_flag;
      this.startDate = this.typeModal === 'isEdit' ? this.record.start_date : this.tomorrow();
      this.endDate = this.typeModal === 'isEdit' ? this.record.end_date : '';
      this.userName = this.record.user_name;
      this.userEmail = this.record.mail_address;
      this.reason = this.record.account_lock_remarks;
    },
    setDateInMouted(date) {
      const dates = new Date();
      dates.setTime(dates.getTime() - 3600 * 1000 * 24);
      return dates > date.setDate(date.getDate());
    },
    callDialogOverWriteComfirm(objProps) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(dialogOverwriteComfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        isShowClose: false,
        props: objProps
      };
    },
    overWriteData() {
      this.isOverWriteData = true;
      this.onResultModal();
      this.submitModal();
    },
    checkOverWriteData() {
      if (this.isCurrentDateinArr && this.curentDate === this.formatFullDate(this.startDate)) {
        this.loading = false;
        this.callDialogOverWriteComfirm({ message: '開始日が同日のものが既に登録されています。上書きしてよろしいですか？' });
      } else {
        this.isOverWriteData = true;
        this.submitModal();
      }
    },
    submitModal() {
      if (this.startDate === null) {
        this.start_date = '';
      }
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-I01S01-UserEdit',
          idNodeNotify: 'msfa-notify-I01S01-UserEdit',
          duration: 1500
        });
        return;
      }
      const dataReason = this.dataTheFirt.reason ? this.dataTheFirt.reason : '';
      this.loading = true;
      const data = {
        user_cd: this.record?.user_cd,
        start_date: moment(new Date(this.startDate)).format('YYYY-M-D'),
        start_date_old:
          this.typeModal === 'isEdit' ? moment(new Date(this.record?.start_date)).format('YYYY-M-D') : moment(new Date(this.startDate)).format('YYYY-M-D'),
        end_date: moment(new Date(this.endDate ? this.endDate : '9999/12/31')).format('YYYY-M-D'),
        user_name: this.userName,
        mail_address: this.userEmail,
        account_lock_flag: this.activeLockAcount,
        account_lock_remarks: this.activeLockAcount === 1 ? this.reason : '',
        flag_change: this.numberModal,
        create_flag: this.typeModal === 'isEdit' ? 0 : 1
      };
      if (this.typeModal === 'createIsRecord') {
        if (
          this.dataTheFirt.userEmail === data.mail_address &&
          this.dataTheFirt.userName === data.user_name &&
          this.dataTheFirt.activeLockAcount === data.account_lock_flag &&
          dataReason === data.account_lock_remarks
        ) {
          this.notifyModal({
            message: 'ユーザ情報に変更がありません。',
            type: 'error',
            classParent: 'modal-body-I01S01-UserEdit',
            idNodeNotify: 'msfa-notify-I01S01-UserEdit',
            duration: 1500
          });
          this.loading = false;
        } else {
          if (!this.isOverWriteData) {
            this.checkOverWriteData();
          } else {
            I01_S01_UserManagementServices.updateUser(data)
              .then((res) => {
                if (res) {
                  this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
                  this.$emit('onFinishScreen', 1);
                  this.changeFalseClassHeader();
                }
              })
              .catch((err) => {
                this.notifyModal({
                  message: err.response.data.message,
                  type: 'error',
                  classParent: 'modal-body-I01S01-UserEdit',
                  idNodeNotify: 'msfa-notify-I01S01-UserEdit',
                  duration: 1500
                });
              })
              .finally(() => {
                this.loading = false;
                this.isOverWriteData = false;
                this.changeTabI01();
              });
          }
        }
      } else {
        if (!this.isOverWriteData) {
          this.checkOverWriteData();
        } else {
          I01_S01_UserManagementServices.updateUser(data)
            .then((res) => {
              if (res) {
                this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
                this.$emit('onFinishScreen', 1);
                this.changeFalseClassHeader();
              }
            })
            .catch((err) => {
              this.notifyModal({
                message: err.response.data.message,
                type: 'error',
                classParent: 'modal-body-I01S01-UserEdit',
                idNodeNotify: 'msfa-notify-I01S01-UserEdit',
                duration: 1500
              });
            })
            .finally(() => {
              this.loading = false;
              this.isOverWriteData = false;
            });
        }
      }
    },
    change() {
      if (this.reason === '' || this.reason === null) {
        this.disableReason = true;
      } else {
        this.disableReason = false;
      }
    },
    clickDelete() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        isShowClose: false,
        props: { title: `選択した${this.userName}を完全に削除しますか？` }
      };
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    handleYes() {
      this.$emit('onFinishScreen', 2);
      this.changeFalseClassHeader();
      // this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    deleteMessage() {
      const data = {
        user_cd: this.record.user_cd,
        start_date: moment(new Date(this.record.start_date)).format('YYYY-M-D'),
        end_date: this.record.end_date ? moment(new Date(this.record.end_date)).format('YYYY-M-D') : '9999/12/31'
      };
      I01_S01_UserManagementServices.deleteUsser(data)
        .then((res) => {
          if (res) {
            this.$notify({ message: '正常に削除しました。', customClass: 'success', duration: 1500 });
            this.$emit('onFinishScreen', 1);
            this.changeTabI01();
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-I01S01-UserEdit',
            idNodeNotify: 'msfa-notify-I01S01-UserEdit',
            duration: 1500
          });
        });
    },
    confirmCancel() {
      // this.$emit('onFinishScreen', 2);
      // this.changeFalseClassHeader();
      if (this.startDate === null || this.startDate === '') {
        this.startDate = '';
      }
      const data = {
        activeLockAcount: this.activeLockAcount,
        startDate: this.startDate === '' ? '' : this.tomorrow(),
        endDate: this.endDate,
        userName: this.userName,
        userEmail: this.userEmail,
        reason: this.reason
      };
      if (!_.isEqual(data, this.dataTheFirt)) {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog  modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false
        };
      } else {
        this.$emit('onFinishScreen', 2);
      }
    },
    callDialogComfirmLockAccount(objProps) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(dialogComfirmLockAccount),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        isShowClose: false,
        props: objProps
      };
    },
    lockAcount() {
      if (this.activeLockAcount === 0) {
        this.callDialogComfirmLockAccount({
          message: 'ロック解除時点で有効な組織・権限が付与されていない場合、ログインができなくなります。設定の見直しをお願いします。'
        });
      } else {
        if (this.activeLockAcount === 1) {
          this.disableReason = false;
          this.activeLockAcount = 0;
        } else {
          if (this.reason === '' || this.reason === null) {
            this.disableReason = true;
          } else {
            this.disableReason = false;
          }
          this.activeLockAcount = 1;
        }
      }
    },
    comfirmLockAcc() {
      this.activeLockAcount = 1;
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
/* Modal */
.required-start {
  line-height: 18px;
  min-width: 9px;
  margin: 0 0 0 8px;
}
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 30%;
    top: 29%;
  }
}
.el-icon-loading {
  display: none;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
.btn-custom-lock {
  padding: unset;
  border: unset;
  position: relative;
  .img-custom {
    top: -10px;
    position: absolute;
    left: -10px;
  }
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
}
.modal-userEdit {
  .form-userEdit {
    ul {
      li {
        margin-bottom: 7px;
        &:last-child {
          margin-bottom: 0;
        }
      }
    }
    .form-userEdit-label {
      font-size: 1rem;
      margin-bottom: 8px;
    }
  }
  .form-userEdit--flex-custom {
    align-items: unset !important;
  }
  .form-userEdit--flex {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    .form-dateTime {
      width: 176px;
    }
    .form-dateTime-label {
      span {
        padding-left: 8px;
      }
    }
    .form-btnLock {
      width: 42px;
    }
    .form-accountLock {
      width: calc(100% - 42px);
      padding-left: 16px;
    }
  }
  .userEdit-btn {
    margin-top: 28px;
    display: flex;
    justify-content: center;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
</style>
