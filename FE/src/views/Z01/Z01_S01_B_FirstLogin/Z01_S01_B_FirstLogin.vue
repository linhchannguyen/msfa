<template>
  <div class="login">
    <div class="loginWrap">
      <div class="loginContent">
        <div class="div-center"><img src="@/assets/template/images/z01_s01_b_first_login_icon_lock.svg" alt="" /></div>
        <h2 class="title-tlt txt-align-center">パスワード変更</h2>
        <p class="textReq txt-align-center">初回ログインにはパスワード変更が必要です。</p>
        <div class="loginFrom">
          <label class="loginFrom-label">
            <span class="item"><img src="@/assets/template/images/icon_key.svg" alt="" /></span>
            現在のパスワード</label
          >
          <div class="loginFrom-control">
            <div class="form-icon icon-right" :class="currentPassValid ? 'hasErr' : ''">
              <span class="icon icon--cursor" @click="showHidePassword('is_show_current_password')">
                <img v-if="is_show_current_password.isHidden" class="svg" src="@/assets/template/images/icon_eye.svg" alt="" />
                <img v-if="!is_show_current_password.isHidden" class="svg" src="@/assets/template/images/icon_eye-hide.svg" alt="" />
              </span>
              <input
                v-model="formData.current_password"
                class="form-control custom-input"
                :type="is_show_current_password.type"
                placeholder="現在のパスワード入力"
                required
                @input="checkValidateRegex"
              />
            </div>
            <div class="invalid-feedback">
              <span>{{ currentPassValid }}</span>
            </div>
          </div>
        </div>
        <div class="loginFrom">
          <label class="loginFrom-label">
            <span class="item"><img src="@/assets/template/images/icon_key.svg" alt="" /></span>
            新しいパスワード</label
          >
          <div class="loginFrom-control">
            <div class="form-icon icon-right" :class="newPassValid ? 'hasErr' : ''">
              <span class="icon icon--cursor" @click="showHidePassword('is_show_new_password')">
                <img v-if="is_show_new_password.isHidden" class="svg" src="@/assets/template/images/icon_eye.svg" alt="" />
                <img v-if="!is_show_new_password.isHidden" class="svg" src="@/assets/template/images/icon_eye-hide.svg" alt="" />
              </span>
              <input
                v-model="formData.new_password"
                :pattern="ObjRegex?.regex?.fe"
                class="form-control custom-input"
                :type="is_show_new_password.type"
                placeholder="新しいパスワード入力"
                required
                @input="checkValidateRegex"
              />
              <div class="invalid-feedback" :class="newPassValid ? 'textFeedback' : 'woFeedback'">
                <span>
                  {{ newPassValid }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <p class="loginNote">{{ ObjRegex.text_validate_first_login }}</p>
        <div class="loginBtn">
          <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="changePassword()">
            <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> パスワード変更
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { required, validPasswordHalfFullWidth } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/Z01/Z01_S01_B_FirstLogin';
import { getCookie } from '@/utils/constants';
import data from 'LibTest/data.json';
export default {
  name: 'Z01_S01_B_FirstLogin',
  data() {
    return {
      loading: false,
      currentPassValid: '',
      newPassValid: '',
      formData: {
        current_password: '',
        new_password: ''
      },
      is_show_current_password: { type: 'password', isHidden: true },
      is_show_new_password: { type: 'password', isHidden: true },
      validateMessages,
      ObjRegex: {},
      isSubmit: false
    };
  },
  computed: {
    validation() {
      return {
        new_password: { required: required(this.formData.new_password), validPasswordHalfFullWidth: validPasswordHalfFullWidth(this.formData.new_password) }
      };
    }
  },
  created() {
    localStorage.setItem('$_C', this.enCodeData(JSON.stringify(data)));
    localStorage.setItem('$_Flogin', 1);
  },
  mounted() {
    this.ObjRegex = JSON.parse(this.decodeData(localStorage.getItem('$_C')));
  },
  updated() {
    let accessToken = Boolean(getCookie('accessToken'));
    if (!accessToken) {
      this.$store.dispatch('auth/logout'); // Clear Store + Replace URL
      this.$router.push({ name: 'Z01_S01_Login' }); // Push Routing
    }
  },
  methods: {
    showHidePassword(passwordField) {
      if (passwordField === 'is_show_current_password') {
        this.is_show_current_password = {
          type: this.is_show_current_password.type === 'text' ? 'password' : 'text',
          isHidden: !this.is_show_current_password.isHidden
        };
      } else {
        this.is_show_new_password = {
          type: this.is_show_new_password.type === 'text' ? 'password' : 'text',
          isHidden: !this.is_show_new_password.isHidden
        };
      }
    },

    checkValidateRegex() {
      let pattern = new RegExp(this.ObjRegex.regex.fe);

      if (!this.formData.current_password) {
        if (this.isSubmit) {
          this.currentPassValid = '現在のパスワードを入力してください。';
        } else {
          this.currentPassValid = '';
        }
      } else {
        this.currentPassValid = '';
      }

      if (this.formData.new_password) {
        if (this.formData.current_password && this.formData.current_password === this.formData.new_password) {
          this.newPassValid = '新パスワードは現在のパスワードと同じです。異なるパスワードを入力してください。';
        } else {
          if (pattern.test(this.formData.new_password)) {
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
      this.isSubmit = true;
      this.checkValidateRegex();
      if (!this.checkValidate() || this.currentPassValid || this.newPassValid) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      this.loading = true;
      this.$store
        .dispatch('auth/changePasswordAction', this.formData)
        .then(() => {
          localStorage.removeItem('$_Flogin');
          const redirectTo = 'Z02_S01_Home';
          this.$router.push({ name: redirectTo });
          this.currentPassValid = '';
        })
        .catch((err) => {
          let messageCurrent = err.response.data.data.current_password ? true : false;
          this.currentPassValid = messageCurrent ? err.response.data.data.current_password[0] : '';
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    logOut() {
      const isDevelopLogin = this.$store.state.auth.currentUser.developer_cd ? true : false;
      this.$store
        .dispatch('auth/logout')
        .then(() => {
          if (isDevelopLogin) {
            this.$router.push({ name: 'Z01_S02_DeveloperLogin' });
          } else this.$router.push({ name: 'Z01_S01_Login' });
        })
        .catch(() => {})
        .finally(() => {});
    }
  }
};
</script>

<style lang="scss" scoped>
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 10%;
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
.div-center {
  display: flex;
  justify-content: center;
}
.txt-align-center {
  text-align: center;
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 100%;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.login {
  background: url(~@/assets/template/images/data/body-login.png) no-repeat fixed;
  background-size: cover;
  min-height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 12px;
  color: #2d3033;
  .loginWrap {
    width: 100%;
  }
  .loginContent {
    max-width: 560px;
    margin: 0 auto;
    background: rgba(231, 232, 220, 0.35);
    box-shadow: 0px 3px 30px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    padding: 60px 86px;
    .title {
      font-size: 36px;
      font-weight: 700;
    }
    .title-tlt {
      margin-top: 24px;
      font-size: 24px;
      font-weight: 700;
    }
    .loginFrom {
      margin-top: 24px;
      .loginFrom-label {
        font-size: 16px;
        display: flex;
        margin-bottom: 6px;
        .item {
          min-width: 20px;
          margin-right: 8px;
          margin-top: -2px;
        }
      }
      .loginFrom-control {
        .form-control {
          height: 52px;
        }
      }
    }
  }
  .textReq {
    margin-top: 10px;
    font-size: 16px;
  }
  .loginNote {
    color: #ed5e54;
    font-weight: 500;
    margin: 12px 0 0 0;
    padding-left: 5px;
  }
  .loginBtn {
    margin-top: 52px;
    display: flex;
    justify-content: center;
    .btn {
      width: 180px;
      margin-right: 27px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.form-icon .icon {
  height: calc(52px - 14px) !important;
}
.invalid-feedback {
  display: flex;
  line-height: 1.6;
  padding-top: 12px;
  &.woFeedback {
    height: 0;
    transition: all 0.2s;
    opacity: 0;
    visibility: hidden;
  }
  &.textFeedback {
    height: 100%;
    opacity: 1;
    visibility: visible;
  }
}
.custom-input {
  &:invalid {
    & ~ .feedback {
      opacity: 1;
      visibility: visible;

      height: 100%;
      margin: 0;
    }
  }
  &:valid {
    & ~ .feedback {
      opacity: 0;
      visibility: hidden;
      height: 0;
      transition: all 0.2s;
      margin: 0;
    }
  }
}
</style>
