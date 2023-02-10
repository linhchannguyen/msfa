/* eslint-disable indent */
<template>
  <div>
    <div class="formChangePass modal-body-Z04S01-ChangePass">
      <div id="msfa-notify-Z04S01-ChangePass"></div>
      <div class="changePass">
        <label class="changePass-label">
          現在のパスワード
          <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
        </label>
        <div class="changePass-control">
          <div class="form-icon icon-right" :class="currentPassValid ? 'hasErr' : ''">
            <span class="icon icon--cursor" @click="handleShowPassword(1)">
              <img v-if="passwordCurrent.isHidden" src="@/assets/template/images/icon_eye.svg" alt="" />
              <img v-else src="@/assets/template/images/icon_eye-hide.svg" alt="" />
            </span>
            <input v-model="current_password" class="form-control" maxlength="255" :type="passwordCurrent.type" @input="checkValidateRegex" />
          </div>
          <div>
            <p v-if="currentPassValid" class="errors">
              <span>{{ currentPassValid }}</span>
            </p>
          </div>
        </div>
      </div>
      <div class="changePass">
        <label class="changePass-label">
          新しいパスワード
          <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
        </label>
        <div class="changePass-control">
          <div class="form-icon icon-right" :class="newPassValid ? 'hasErr' : ''">
            <span class="icon icon--cursor" @click="handleShowPassword(2)">
              <img v-if="passwordNew.isHidden" src="@/assets/template/images/icon_eye.svg" alt="" />
              <img v-else src="@/assets/template/images/icon_eye-hide.svg" alt="" />
            </span>
            <input v-model="new_password" class="form-control" maxlength="255" :type="passwordNew.type" @input="checkValidateRegex" />
          </div>
          <div>
            <p v-if="newPassValid" class="errors">
              {{ newPassValid }}
            </p>
          </div>
        </div>
      </div>
      <p class="changePass-note">{{ ObjRegex.message_first_login }}</p>
      <div class="changePass-btn">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <button type="button" class="btn btn-primary" @click="changePassword">パスワード変更</button>
      </div>
    </div>
    <!-- Popup Confirm -->
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
          @onFinishScreen="
            reloadAction(() => {
              modalConfig.isShowModal = false;
            }, 'reload')($event)
          "
          @handleYes="handleConfirmYes"
        />
      </template>
    </el-dialog>
  </div>
</template>
<script>
import Z04_S01_Services from '@/api/Z04/Z04_S01_AccountSettingServices';
import { required, validLengthPassword } from '@/utils/validate';
import { markRaw } from 'vue';

export default {
  name: 'ModalChangePassword',
  props: {
    checkLoading: [Boolean]
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
        isShowClose: false
      },
      currPass: '',
      current_password: '',
      new_password: '',
      passwordCurrent: {
        type: 'password',
        isHidden: true
      },
      passwordNew: {
        type: 'password',
        isHidden: true
      },
      ObjRegex: {},
      currentPassValid: '',
      newPassValid: ''
    };
  },
  computed: {
    validation() {
      return {
        currentPass: {
          required: required(this.current_password),
          validLengthPassword: validLengthPassword(this.current_password)
        },
        newPass: {
          required: required(this.new_password),
          validLengthPassword: validLengthPassword(this.new_password)
        }
      };
    }
  },
  mounted() {
    this.ObjRegex = JSON.parse(this.decodeData(localStorage.getItem('$_C')));
    this.resetForm();
  },
  methods: {
    confirmCancel() {
      if (this.current_password || this.new_password) {
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
        this.closeModal();
      }
    },
    handleConfirmYes() {
      this.closeModal();
    },
    handleShowPassword(option) {
      if (option === 1) {
        this.passwordCurrent = {
          type: this.passwordCurrent.type === 'text' ? 'password' : 'text',
          isHidden: !this.passwordCurrent.isHidden
        };
      } else {
        this.passwordNew = {
          type: this.passwordNew.type === 'text' ? 'password' : 'text',
          isHidden: !this.passwordNew.isHidden
        };
      }
    },

    closeModal() {
      this.$emit('onFinishScreen');

      this.emitter.emit('change-class-header-modal', {
        changeClass: false
      });
      this.resetForm();
    },

    checkValidateRegex() {
      let pattern = new RegExp(this.ObjRegex.regex.fe);

      if (this.current_password) {
        if (pattern.test(this.current_password)) {
          this.currentPassValid = '';
        } else {
          this.currentPassValid = this.ObjRegex?.message;
        }
      } else {
        if (this.isSubmit) {
          this.currentPassValid = '現在のパスワードを入力してください。';
        } else {
          this.currentPassValid = '';
        }
      }

      if (this.new_password) {
        if (this.current_password && this.current_password === this.new_password) {
          this.newPassValid = '新パスワードは現在のパスワードと同じです。異なるパスワードを入力してください。';
        } else {
          if (pattern.test(this.new_password)) {
            this.newPassValid = '';
          } else {
            this.newPassValid = this.ObjRegex?.message;
          }
        }
      } else {
        if (this.isSubmit) {
          this.newPassValid = '新しいパスワードを入力してください。';
        } else {
          this.newPassValid = '';
        }
      }
    },

    changePassword() {
      this.checkValidateRegex();
      if (!this.checkValidate() || this.currentPassValid || this.newPassValid) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-Z04S01-ChangePass',
          idNodeNotify: 'msfa-notify-Z04S01-ChangePass'
        });
        return;
      }
      let params = {
        current_password: this.current_password,
        new_password: this.new_password
      };
      Z04_S01_Services.changePasswordService(params)
        .then((res) => {
          this.notifyModal({
            message: res.data.message,
            type: 'success',
            classParent: 'modal-body-Z04S01-ChangePass',
            idNodeNotify: 'msfa-notify-Z04S01-ChangePass'
          });
          this.closeModal();
          this.currPass = '';
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-Z04S01-ChangePass',
            idNodeNotify: 'msfa-notify-Z04S01-ChangePass'
          });
        })
        .finally();
    },
    resetForm() {
      this.current_password = '';
      this.new_password = '';
      this.resetData();
    }
  }
};
</script>
<style lang="scss" scoped>
.errors {
  color: #ff746b;
}
.formChangePass {
  .changePass {
    margin-bottom: 24px;
    .changePass-label {
      font-size: 16px;
      margin-bottom: 8px;
    }
    .changePass-control {
      .form-control {
        height: 52px;
      }
    }
    input[type='password'] {
      font-size: 32px;
      color: #5f6b73;
      letter-spacing: -1.6px;
    }
  }
  .changePass-note {
    color: #ff746b;
  }
  .changePass-btn {
    display: flex;
    margin-top: 20px;
    .btn {
      width: 50%;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
</style>
