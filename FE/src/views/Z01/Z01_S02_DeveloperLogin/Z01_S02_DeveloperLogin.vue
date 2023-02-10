<template>
  <div class="login">
    <div class="loginWrap">
      <div class="loginContent">
        <h1 v-if="!loadingTitle" class="title"><img :src="srcImg" alt="" /></h1>
        <div v-if="loadingTitle" class="lds-ellipsis">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>
        <h2 class="title-tlt">開発者ログイン</h2>
        <div class="loginFrom">
          <label class="loginFrom-label">
            <span class="item"><img src="@/assets/template/images/icon_user.svg" alt="" /></span>
            開発者ID</label
          >
          <div class="loginFrom-control" :class="messDev ? 'hasErr' : ''">
            <input v-model="formData.developer_cd" class="form-control" type="text" placeholder="開発者ID入力" />
          </div>
          <div class="invalid-feedback">
            <span>{{ messDev }}</span>
          </div>
        </div>
        <div class="loginFrom">
          <label class="loginFrom-label">
            <span class="item"><img src="@/assets/template/images/icon_key.svg" alt="" /></span>
            パスワード</label
          >
          <div class="loginFrom-control">
            <div class="form-icon icon-right" :class="messpass ? 'hasErr' : ''">
              <span class="icon icon--cursor" @click="showHidePassword()">
                <img v-if="isShowPassord.isHidden" class="svg" src="@/assets/template/images/icon_eye.svg" alt="" />
                <img v-if="!isShowPassord.isHidden" class="svg" src="@/assets/template/images/icon_eye-hide.svg" alt="" />
              </span>
              <input v-model="formData.password_hash" class="form-control" :type="isShowPassord.type" placeholder="パスワード入力" value="Abc123456" />
            </div>
            <div class="invalid-feedback">
              <span>{{ messpass }}</span>
            </div>
          </div>
        </div>
        <div class="loginFrom">
          <label class="loginFrom-label">
            <span class="item"><img src="@/assets/template/images/icon_multiple-users.svg" alt="" /></span>
            ユーザID</label
          >
          <div class="loginFrom-control" :class="messUser ? 'hasErr' : ''">
            <input v-model="formData.user_cd" class="form-control" type="text" placeholder="ユーザID入力" />
          </div>
          <div class="invalid-feedback">
            <span>{{ messUser }}</span>
          </div>
        </div>
        <p class="loginNote">※操作履歴として代行ログインした開発者の情報が記録されます。</p>
        <div class="loginBtn">
          <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="handleLogin()">
            <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> ログイン
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/Z01/Z01_S02_DeveloperLogin';
import data from 'LibTest/system_name.svg';
export default {
  name: 'Z01_S02_DeveloperLogin',
  data() {
    return {
      // data,
      loadingTitle: false,
      loading: false,
      validateMessages,
      messDev: '',
      messpass: '',
      messUser: '',
      imgAvatar: '',
      formData: {
        developer_cd: '',
        user_cd: '',
        password_hash: ''
      },
      isShowPassord: { type: 'password', isHidden: true }
    };
  },
  computed: {
    validation() {
      return {
        developer_cd: { required: required(this.formData.developer_cd) },
        user_cd: { required: required(this.formData.user_cd) }
      };
    }
  },
  mounted() {
    this.loadingTitle = true;
    setTimeout(() => {
      // this.imgAvatar = this.data.systemName;
      this.loadingTitle = false;
    }, 700);
    this.srcImg = data;
  },
  methods: {
    handleLogin() {
      // if (!this.checkValidate()) return;
      if (this.formData.developer_cd === '' || this.formData.user_cd === '' || this.formData.password_hash === '') {
        if (this.formData.developer_cd === '') {
          this.messDev = '開発者IDを入力してください。';
        } else {
          this.messDev = '';
        }
        if (this.formData.user_cd === '') {
          this.messUser = 'ユーザIDを入力してください。';
        } else {
          this.messUser = '';
        }
        if (this.formData.password_hash === '') {
          this.messpass = 'パスワードを入力してください。';
        } else {
          this.messpass = '';
        }
      } else {
        this.loading = true;
        this.$store
          .dispatch('auth/loginDeveloperAction', this.formData)
          .then(() => {
            const redirectTo = 'Z02_S01_Home';
            this.$router.push({ name: redirectTo });
            // this.$notify({ message: res.data.message, customClass: 'success' });
            this.messDev = '';
            this.messpass = '';
            this.messUser = '';
          })
          .catch((err) => {
            this.messDev = '';
            this.messpass = '';
            this.messUser = '';
            // this.messUser = err.response.data.message;
            this.$notify({ message: err.response?.data?.message, customClass: 'error' });
          })
          .finally(() => {
            this.loading = false;
          });
      }
    },
    showHidePassword() {
      this.isShowPassord = {
        type: this.isShowPassord.type === 'text' ? 'password' : 'text',
        isHidden: !this.isShowPassord.isHidden
      };
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
    left: 23%;
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
input[type='text'] {
  font-family: 'Noto Sans Japanese';
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
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
    padding: 30px 70px;
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
    margin-top: 20px;
  }
  .loginBtn {
    margin-top: 30px;
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
.lds-ellipsis {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 40px;
}
.lds-ellipsis div {
  position: absolute;
  top: 33px;
  width: 13px;
  height: 13px;
  border-radius: 50%;
  background: #fff;
  animation-timing-function: cubic-bezier(0, 1, 1, 0);
}
.lds-ellipsis div:nth-child(1) {
  left: 8px;
  animation: lds-ellipsis1 0.6s infinite;
}
.lds-ellipsis div:nth-child(2) {
  left: 8px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(3) {
  left: 32px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(4) {
  left: 56px;
  animation: lds-ellipsis3 0.6s infinite;
}
@keyframes lds-ellipsis1 {
  0% {
    transform: scale(0);
  }
  100% {
    transform: scale(1);
  }
}
@keyframes lds-ellipsis3 {
  0% {
    transform: scale(1);
  }
  100% {
    transform: scale(0);
  }
}
@keyframes lds-ellipsis2 {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(24px, 0);
  }
}
</style>
