<template>
  <div class="modal-body width-520px background-F7F7F7 modal-body-Z01S03">
    <div id="msfa-notify-Z01S03"></div>
    <div class="proxyLogin">
      <span class="loginUser-label w-100">代行ログインユーザ</span>
      <div class="loginUser">
        <div class="form-icon icon-right w-100">
          <span class="icon icon--cursor log-icon" title_log="代行ログインユーザ" @click="openModalZ05S01">
            <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
          </span>
          <div v-if="userData.user_name" class="form-control d-flex align-items-center">
            <div class="block-tags">
              <el-tag class="m-0 el-tag-custom" closable @close="onRemoveUser()">
                {{ userData.user_name }}
              </el-tag>
            </div>
          </div>
          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
        </div>
      </div>
      <p class="proxyNote">※代行ユーザでログイン中の操作も履歴として代行ログインしたユーザの情報が記録されます。</p>
      <div class="proxyBtn">
        <button ref="btnClose" type="button" data-dismiss="modal" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCancelButton()">
          キャンセル
        </button>
        <button type="button" class="btn btn-primary customBtn" :disabled="userData.user_cd === '' || loading" @click="handleLogin()">
          <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> ログイン
        </button>
      </div>
    </div>
  </div>

  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="'custom-dialog modal-fixed'"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="true"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
    </template>
  </el-dialog>
</template>

<script>
import Z05_S01_Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import { markRaw } from 'vue';
import { getCookie } from '@/utils/constants';

export default {
  name: 'Z01_S03_ProxyUserSelection',
  components: { Z05_S01_Organization },
  emits: ['onFinishScreen'],
  data() {
    return {
      loading: false,
      showModalZ05S01: false,
      userData: {
        org_cd: '',
        user_cd: '',
        user_name: ''
      },
      paramsZ05S01: {
        userFlag: 1,
        mode: 'single',
        orgCdList: [],
        userCdList: []
      },
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },
  computed: {},
  methods: {
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05_S01_Organization),
        props: { ...this.paramsZ05S01 },
        width: '45rem',
        destroyOnClose: true
      };
    },

    onResultModal(data) {
      if (data) {
        this.paramsZ05S01.orgCdList = [];
        this.paramsZ05S01.userCdList = [];
        data.userSelected?.forEach((x) => {
          this.paramsZ05S01.orgCdList.push(x.org_cd);
        });
        data.userSelected?.forEach((x) => {
          this.paramsZ05S01.userCdList.push({
            org_cd: x.org_cd,
            user_cd: x.user_cd
          });
        });

        this.userData = { ...data.userSelected[0] };
      }

      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    onRemoveUser() {
      this.userData = { org_cd: '', user_cd: '', user_name: '' };
      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        orgCdList: [],
        userCdList: []
      };
    },

    onCancelButton() {
      this.userData = { org_cd: '', user_cd: '', user_name: '' };
      this.$refs.btnClose.click();
      this.$emit('onFinishScreen');
      this.emitter.emit('change-class-header-modal', {
        changeClass: false
      });
    },
    async handleLogin() {
      const originToken = getCookie('originalToken');
      if (originToken) {
        // Reload page when have login by Proxy
        return window.location.reload();
      } else {
        this.loading = true;
        this.$store
          .dispatch('auth/loginProxyAction', this.userData)
          .then(() => {
            this.userData = { org_cd: '', user_cd: '', user_name: '' };
            this.onCancelButton();
            if (this.$route.name === 'Z02_S01_Home') {
              this.emitter.emit('home-reload');
            } else {
              this.$router.push({ name: 'Z02_S01_Home' });
            }
          })
          .catch((err) => {
            this.notifyModal({
              message: err.response.data.message,
              type: 'error',
              classParent: 'modal-body-Z01S03',
              idNodeNotify: 'msfa-notify-Z01S03'
            });
          })
          .finally(() => {
            this.loading = false;
          });
      }
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
    top: 28%;
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
.width-520px {
  width: 520px;
}
.background-F7F7F7 {
  background: #f7f7f7;
}
.el-tag-custom {
  height: 30px;
  line-height: 26px;
  font-size: 14px;
  align-items: center;
  margin-right: 10px !important;
  background: #b7d4ff;
  border-radius: 24px;
  color: #225999;
}

.cursor-pointer {
  cursor: pointer;
}
.readonly-custom {
  background-color: #fff;
}
.proxyLogin {
  .loginUser-label {
    font-size: 16px;
    width: 172px;
    padding-top: 8px;
  }
  .proxyLogin-title {
    padding: 16px 33px 0;
    font-size: 16px;
    font-weight: 700;
    text-align: center;
  }
  .loginUser {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;

    .loginUser-control {
      width: calc(100% - 172px);
    }
  }
  .proxyNote {
    margin-top: 24px;
    color: #ff746b;
  }
  .proxyBtn {
    padding: 24px 26px 0;
    display: flex;
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
