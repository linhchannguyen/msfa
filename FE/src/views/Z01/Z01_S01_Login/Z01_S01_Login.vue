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
        <h2 class="title-tlt">ログイン</h2>
        <div class="loginFrom">
          <label class="loginFrom-label">
            <span class="item"><img src="@/assets/template/images/icon_user.svg" alt="" /></span>
            ログインID</label
          >
          <div class="loginFrom-control" :class="errUser ? 'hasErr' : ''">
            <input v-model="formData.user_cd" class="form-control" type="text" placeholder="ログインID入力" @keyup.enter="handleLogin" />
          </div>
          <!-- <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.user_cd.required">{{ validateMessages.user_cd.required }}</span>
            <span v-else-if="isSubmit && responseErrors.user_cd">{{ responseErrors.user_cd }}</span>
          </div> -->
          <div class="invalid-feedback">
            <span>{{ errUser }}</span>
          </div>
        </div>
        <div class="loginFrom">
          <label class="loginFrom-label">
            <span class="item"><img src="@/assets/template/images/icon_key.svg" alt="" /></span>
            パスワード</label
          >
          <div class="loginFrom-control">
            <div class="form-icon icon-right" :class="errMess ? 'hasErr' : ''">
              <span class="icon icon--cursor" @click="showHidePassword()">
                <img v-if="isShowPassord.isHidden" src="@/assets/template/images/icon_eye.svg" alt="" />
                <img v-else class="svg" src="@/assets/template/images/icon_eye-hide.svg" alt="" />
              </span>
              <input v-model="formData.password_hash" class="form-control" :type="isShowPassord.type" placeholder="パスワード入力" @keyup.enter="handleLogin" />
            </div>
            <div class="invalid-feedback">
              <!-- <span v-if="isSubmit && !validation.password_hash.required">{{ validateMessages.password_hash.required }}</span>
              <span v-else-if="isSubmit && !validation.password_hash.validPasswordHalfFullWidth">
                {{ validateMessages.password_hash.validPasswordHalfFullWidth }}
              </span> -->
              <span>{{ errMess }}</span>
            </div>
          </div>
        </div>
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
import { mapGetters, mapActions } from 'vuex';
import { required, validPasswordHalfFullWidth } from '@/utils/validate';
import validateMessages from '@/utils/validateMessages/Z01/Z01_S01_Login';
import { getCookie } from '@/utils/constants';
import data from 'LibTest/system_name.svg';
export default {
  name: 'Z01_S01_Login',
  data() {
    return {
      // data,
      loadingTitle: false,
      loading: false,
      validateMessages,
      errMess: '',
      documentName: '',
      errUser: '',
      err: '',
      imgAvatar: '',
      formData: {
        user_cd: '',
        password_hash: ''
      },
      isShowPassord: { type: 'password', isHidden: true }
    };
  },
  computed: {
    ...mapGetters({
      currentUser: 'auth/currentUser'
    }),
    ...mapActions({
      logoutProxy: 'auth/clearProxy',
      logout: 'auth/logout'
    }),
    validation() {
      return {
        user_cd: { required: required(this.formData.user_cd) },
        password_hash: {
          required: required(this.formData.password_hash),
          validPasswordHalfFullWidth: validPasswordHalfFullWidth(this.formData.password_hash)
        }
      };
    }
  },
  mounted() {
    this.loadingTitle = true;
    setTimeout(() => {
      this.loadingTitle = false;
    }, 700);
    window.history.pushState({}, '', '/auth/login');
    let accessToken = getCookie('accessToken');
    let originalToken = getCookie('originalToken');
    if (this.currentUser?.require_change_flag && originalToken) {
      // first login proxy
      this.logoutProxy.then(() => {
        this.redirectHomepage();
      });
    }
    if (this.currentUser?.require_change_flag && accessToken) {
      // first login
      this.logout;
    }
    this.srcImg = data;
    // let btn = document.querySelectorAll('.btn');
    // let input = document.querySelectorAll('input.form-control');
    // let label = document.querySelectorAll('label.loginFrom-label');
    // let title = document.querySelectorAll('.title');
    // let subTitle = document.querySelectorAll('.title-tlt');
    // this.showProperty({ btn, input, label, title, subTitle });
  },
  updated() {
    this.$nextTick(async () => {
      let accessToken = getCookie('accessToken');
      let originalToken = getCookie('originalToken');
      let currentUser = await JSON.parse(localStorage.getItem('currentUser'));
      let first_login = Boolean(currentUser?.require_change_flag);
      if (accessToken) {
        if (first_login) {
          return this.$router.push({ name: 'Z01_S01_B_FirstLogin' });
        }
        //Handle push Homepage here
        let data = { user: currentUser, access_token: accessToken };
        await this.$store.dispatch('auth/afterLoginAction', data);
      }

      if (originalToken) {
        let currentUserProxy = await JSON.parse(localStorage?.getItem('currentUserProxy'));
        let params = {
          currentUser: currentUserProxy,
          access_token_proxy: accessToken,
          originalToken: originalToken
        };
        await this.$store.dispatch('auth/afterLoginProxyAction', params);
      }
      if (accessToken || originalToken) {
        return this.redirectHomepage();
      }
      return;
    });
  },
  methods: {
    handleLogin() {
      if (this.formData.user_cd === '' || this.formData.password_hash === '') {
        this.closeNoti(1);
        this.$notify({ message: 'ログインに失敗しました。ログインIDとパスワードをご確認ください。', customClass: 'error' });
      } else {
        this.closeNoti(2);
        this.loading = true;
        this.$store
          .dispatch('auth/loginAction', this.formData)
          .then(async () => {
            this.errMess = '';
            this.errUser = '';
          })
          .catch((err) => {
            this.errUser = '';
            this.errMess = '';

            // const errUser = err.response?.data?.data?.user_cd ? err.response?.data?.data?.user_cd[0] : '';
            // this.errMess = err.response?.data?.data?.password_hash ? err.response?.data?.data?.password_hash[0] : errUser;
            this.$notify({ message: err.response?.data?.message, customClass: 'error' });
          })
          .finally(async () => {
            this.loading = false;
            this.redirectHomepage();
          });
      }
    },
    showHidePassword() {
      this.isShowPassord = {
        type: this.isShowPassord.type === 'text' ? 'password' : 'text',
        isHidden: !this.isShowPassord.isHidden
      };
    },
    redirectHomepage() {
      const redirectTo = this.currentUser && this.currentUser.require_change_flag ? 'Z01_S01_B_FirstLogin' : 'Z02_S01_Home';
      this.$router.push({ name: redirectTo });
    },
    showProperty(elmtObj, property = 'font-family') {
      // elmtObj = { elmtKeys: elmtArray, ...elmtObj }
      if (!window) return;
      Object.keys(elmtObj).map((keys) => {
        elmtObj[keys].forEach((element) => {
          let props = window.getComputedStyle(element, null).getPropertyValue(property);
          element.innerText = `${element.innerText} - ${property} : ${props}`;
        });
      });
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
  background-color: #448add;
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
    margin-top: 24px;
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
