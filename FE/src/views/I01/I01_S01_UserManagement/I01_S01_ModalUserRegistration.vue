<template>
  <!-- Start  -->
  <div class="modal-body modal-userEdit modal-body-I01S01-UserRegister">
    <div id="msfa-notify-I01S01-UserRegister"></div>
    <div class="form-userEdit">
      <ul>
        <li>
          <label class="form-userEdit-label">
            ユーザコード <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <div class="form-userEdit-control" :class="(isSubmit && !validation.userCd.required) || validateUserCD() ? 'hasErr' : ''">
            <el-input v-model="userCd" clearable placeholder="コード入力" class="form-control-input" @change="validateUserCD" />
          </div>
          <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.userCd.required">{{ validateMessages.userCd.required }}</span>
            <span v-if="validateUserCD()" class="invalid">{{ getMessage('MSFA0012', 'ユーザコード', 15) }}</span>
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
          <div class="form-userEdit-control" :class="(isSubmit && !validation.userEmail.required) || validateEmail() ? 'hasErr' : ''">
            <!-- style="text-transform: lowercase" -->
            <el-input v-model="userEmail" clearable placeholder="メール入力" class="form-control-input" @change="validateEmail" />
          </div>
          <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.userEmail.required">{{ validateMessages.userEmail.required }}</span>
            <span v-if="validateEmail()" class="invalid">{{ getMessage('MSFA0012', 'メールアドレス', 255) }}</span>
          </div>
        </li>
        <li>
          <label class="form-userEdit-label">
            適用開始日<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
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
                  :clearable="false"
                  type="date"
                  :editable="false"
                  placeholder="日付選択"
                  class="form-control-datePickerLeft"
                ></el-date-picker>
              </div>
            </div>
          </div>
          <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.startDate.required">{{ validateMessages.startDate.required }}</span>
          </div>
        </li>
      </ul>
    </div>
    <div class="userEdit-btn">
      <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
      <button :disabled="checkDisableBtn() || loading" type="button" class="btn btn-primary customBtn" @click="submitModal()">
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="handleYes"></component>
      </template>
    </el-dialog>
  </div>
  <!-- End -->
</template>

<script>
import moment from 'moment';
import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/I01/I01_S01_ModalUserRegistration';
export default {
  name: 'I01_S01_ModalUserRegistration',
  emits: ['onFinishScreen'],
  data() {
    return {
      loading: false,
      userCd: '',
      userName: '',
      userEmail: '',
      startDate: this.tomorrow(),
      validateMessages,
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        isShowClose: true,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },
  computed: {
    validation() {
      return {
        userCd: { required: required(this.userCd) },
        userName: { required: required(this.userName) },
        startDate: { required: required(this.startDate) },
        userEmail: { required: required(this.userEmail) }
      };
    }
  },
  methods: {
    validateUserCD() {
      let userCd = this.userCd;
      if (userCd.length > 15) {
        return true;
      }
      return false;
    },
    validateUserName() {
      let userName = this.userName;
      if (userName.length > 50) {
        return true;
      }
      return false;
    },
    validateEmail() {
      let userEmail = this.userEmail;
      if (userEmail.length > 255) {
        return true;
      }
      return false;
    },
    checkDisableBtn() {
      if (this.validateUserCD() || this.validateUserName() || this.validateEmail()) {
        return true;
      }
      return false;
    },
    confirmCancel() {
      if (this.startDate === null) {
        this.startDate = '';
      }
      if (this.userCd === '' && this.userName === '' && this.startDate === this.tomorrow() && this.userEmail === '') {
        this.$emit('onFinishScreen', 1);
      } else {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false
        };
      }
    },
    handleYes() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      this.$emit('onFinishScreen', 1);
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    setDateInMouted(date) {
      const dates = new Date();
      dates.setTime(dates.getTime() - 3600 * 1000 * 24);
      return dates > date.setDate(date.getDate());
    },
    submitModal() {
      if (this.valueDate === null) {
        this.startDate = '';
      }
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-I01S01-UserRegister',
          idNodeNotify: 'msfa-notify-I01S01-UserRegister',
          duration: 1500
        });
        return;
      }
      this.loading = true;
      const data = {
        user_cd: this.userCd,
        user_name: this.userName,
        mail_address: this.userEmail,
        start_date: moment(this.startDate).format('YYYY-M-D'),
        end_date: '9999-12-31'
      };
      I01_S01_UserManagementServices.postCreateUsser(data)
        .then((res) => {
          if (res) {
            this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
            this.$emit('onFinishScreen', 2);
            this.changeTabI01();
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-I01S01-UserRegister',
            idNodeNotify: 'msfa-notify-I01S01-UserRegister',
            duration: 1500
          });
        })
        .finally(() => (this.loading = false));
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.required-start {
  line-height: 18px;
  min-width: 9px;
  margin: 0 0 0 8px;
}
/* Modal */
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
