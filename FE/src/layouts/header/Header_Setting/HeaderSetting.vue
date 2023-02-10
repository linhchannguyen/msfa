<template>
  <div class="header-user">
    <a class="userDrop" href="#" data-toggle="dropdown">
      <div class="header-user__thumb">
        <img :src="avatarImage" alt="avatarHeader" />
      </div>
      <span class="header-user__name" data-toggle="tooltip" data-placement="left">
        <span class="proxy-text-red">{{ isLoginProxy === true ? '(代行)' : '' }}</span>
        {{ userName }}
      </span>
    </a>
    <div class="dropdown-menu dropdown-nav" :style="`right: 25px`">
      <ul>
        <li>
          <span class="customSpan" @click="call_Z04_S01">
            <span class="item">
              <ImageSVG class="item-img" :src-image="'icon_profile01.svg'" :alt-image="'icon_profile01'" />
            </span>
            <span>アカウント管理</span>
          </span>
        </li>
        <li>
          <span class="customSpan" @click="openModal">
            <span class="item">
              <ImageSVG :src-image="'icon_key02.svg'" :alt-image="'icon_key02'" />
            </span>
            <span>パスワード変更</span>
          </span>
        </li>
        <li v-if="roleUser === 'R90'">
          <button
            :disabled="isDevLogin || isLoginProxy"
            :class="isDevLogin || isLoginProxy ? 'btn-disabled' : ''"
            style="width: 100%"
            class="customSpan"
            @click="openZ01S03"
          >
            <span class="item">
              <ImageSVG :src-image="'icon_login-nav.svg'" :alt-image="'icon_login-nav'" />
            </span>
            <span>代行ログイン</span>
          </button>
        </li>
        <li v-if="!isLoginProxy">
          <span class="customSpan" @click.prevent="logOut()">
            <span class="item">
              <ImageSVG :src-image="'icon_logout-nav.svg'" :alt-image="'icon_logout-nav'" />
            </span>
            <span> ログアウト</span>
          </span>
        </li>
        <li v-else>
          <span class="customSpan" @click.prevent="exitProxy()">
            <span class="item">
              <ImageSVG :src-image="'icon_logout-nav.svg'" :alt-image="'icon_logout-nav'" />
            </span>
            <span> 代行ログイン終了 </span>
          </span>
        </li>
      </ul>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog handle-close modal-fixed modal-fixed-min"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component :is="modalConfig.component" ref="modalChild" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import ImageSVG from '@/components/ImageSVG';
// import { validURL } from '@/utils/validate';
import Z04_S01_ModalChangePassword from '@/views/Z04/Z04_S01_AccountSetting/Z04_S01_ModalChangePassword';
import Z01_S03_ProxyUserSelection from '@/views/Z01/Z01_S03_ProxyUserSelection/Z01_S03_ProxyUserSelection';
import { markRaw } from 'vue';

export default {
  name: 'HeaderSetting',
  components: {
    ImageSVG,
    Z04_S01_ModalChangePassword,
    Z01_S03_ProxyUserSelection
  },
  data() {
    return {
      isDevLogin: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      process: false,
      avatarImage: ''
    };
  },
  computed: {
    userName() {
      return this.$store.state.auth.currentUser.user_name;
    },
    isLoginProxy() {
      return Boolean(this.$store.state.auth.isLoginProxy);
    },
    roleUser() {
      return this.$store.state.auth.policyRole?.includes('R90') ? 'R90' : '';
    }
  },
  watch: {
    '$store.state.auth.currentUser.avatar_image_data': function () {
      this.getAvatar();
    }
  },
  mounted() {
    this.isDevLogin = this.$store.state.auth.currentUser.developer_cd ? true : false;

    this.getAvatar();
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
        this.emitter.emit('change-class-header-modal', {
          changeClass: false
        });
      }
    },
    getAvatar() {
      this.avatarImage = '';
      this.avatarImage = this.$store?.state?.auth?.currentUser?.avatar_image_data ? this.$store?.state?.auth?.currentUser?.avatar_image_data : '';

      if (this.avatarImage) {
        fetch(this.avatarImage).then((response) => {
          // eslint-disable-next-line eqeqeq
          if (!response.ok || response.status != 200) {
            // eslint-disable-next-line eqeqeq
            this.avatarImage = this.avataDefault();
          }
        });
      } else {
        this.avatarImage = this.avataDefault();
      }
    },

    openZ01S03() {
      if (!this.isDevLogin) {
        this.emitter.emit('change-class-header-modal', {
          changeClass: true
        });

        if (!this.isLoginProxy) {
          this.modalConfig = {
            ...this.modalConfig,
            title: '代行ログイン',
            isShowModal: true,
            component: markRaw(Z01_S03_ProxyUserSelection),
            width: '586px'
          };
        }
      }
    },
    logOut() {
      this.process = true;
      const isUserProxy = this.isLoginProxy;
      const isDevelopLogin = this.$store?.state?.auth?.currentUser?.developer_cd ? true : false;
      this.$store
        .dispatch('auth/logout')
        .then(() => {
          if (this.$route.name === 'Z02_S01_Home' && isUserProxy) {
            this.emitter.emit('home-reload');
          }
          if (isDevelopLogin) {
            this.$router.push({ name: 'Z01_S02_DeveloperLogin' });
          } else {
            setTimeout(() => {
              this.$router.push({ name: 'Z01_S01_Login' });
            }, 500);
          }
        })
        .finally(() => (this.process = false));
    },
    exitProxy() {
      this.$store.dispatch('auth/clearProxy');
      this.$router.push({ name: 'Z02_S01_Home' });
      this.emitter.emit('home-reload');
    },
    call_Z04_S01() {
      this.$router.push({ name: 'Z04_S01_AccountSettings', params: { user_cd: this.$store.state.auth.currentUser.user_cd } });
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    openModal() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'パスワード変更',
        isShowModal: true,
        component: markRaw(Z04_S01_ModalChangePassword),
        width: '474px'
      };
      this.emitter.emit('change-class-header-modal', {
        changeClass: true
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.classDis {
  cursor: not-allowed;
}
.item {
  .item-img {
    width: 16px;
  }
}
.customSpan {
  padding: 7px 6px 7px 24px;
  cursor: pointer;
  display: flex;
  color: #5f6b73;
  font-size: 16px;
}
.customSpan:hover {
  background: #eef6ff;
  color: #2d3033;
  font-weight: bold;
}
.customSpan span.item {
  min-width: 18px;
  height: 19px;
  line-height: 19px;
  margin-top: 3px;
  margin-right: 10px;
}
.btn-disabled,
.btn-disabled[disabled] {
  opacity: 0.4;
  cursor: default !important;
  pointer-events: none;
}
.proxy-text-red {
  color: red;
}
</style>
