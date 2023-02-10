<template>
  <div class="header" :class="isChangeClass ? 'header-custom' : ''" @click="closeNoti">
    <div class="title">
      <ul>
        <li>
          <span v-if="isShowBack" class="title-item iconBack log-icon" :title_log="title + '_戻るボタン'" @click="goBack">
            <ImageSVG :src-image="'icon_arrow-back.svg'" :alt-image="'icon_arrow-back'" />
          </span>
          <span class="title-item">
            <img v-if="icon" style="width: 40px" :src="require(`@/assets/template/images/${icon}`)" :alt="icon" />
          </span>
          <span :class="`title-text ${isShowBack ? 'show-icon-back' : ''}`">{{ title }}</span>
        </li>
      </ul>
    </div>
    <div :class="`header-info ${isLoginProxy ? 'info-proxy' : ''}`">
      <HeaderNotification />
      <HeaderSetting />
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
        @onFinishScreen="onResultModal"
        @handleConfirm="handleConfirm"
        @handleYes="handleYes"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
import ImageSVG from '@/components/ImageSVG';
import HeaderNotification from './Header_Notification/HeaderNotification';
import HeaderSetting from './Header_Setting/HeaderSetting';
import mixin_noti from '@/utils/mixin_noti';

export default {
  name: 'Header',
  components: {
    HeaderNotification,
    HeaderSetting,
    ImageSVG
  },
  mixins: [mixin_noti],
  props: {
    type_screen: [String]
  },
  emits: ['changeMode', 'typeScreen'],
  data() {
    return {
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        isShowClose: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      title: '',
      icon: '',
      isShowBack: false,
      isChangeClass: false,
      isRouterH01H02: false,
      data: null,
      oldRoute: null,
      currentUser: '',
      nextRouterName: '',
      menus: []
    };
  },
  computed: {
    isLoginProxy() {
      return Boolean(this.$store.state.auth.isLoginProxy);
    }
  },
  watch: {
    $route(value) {
      if (value && value.params && value.params.icon) {
        this.isShowBack = value.params.icon;
      }
      if (
        value.name === 'H01_S01_FacilitySearch' ||
        value.name === 'H02_PersonalDetails' ||
        value.name === 'H01_FacilityDetails' ||
        value.name === 'H02_S01_PersonalSearch'
      ) {
        this.isRouterH01H02 = true;
      } else this.isRouterH01H02 = false;
    }
  },

  mounted() {
    this.emitter.on('change-header', ({ title, icon, isShowBack, oldRoute, data }) => {
      this.title = title;
      this.icon = icon;
      this.isShowBack = isShowBack ? true : false;
      this.$emit('typeScreen', this.isShowBack ? 'children' : this.$props.type_screen);

      this.data = data;
      this.oldRoute = oldRoute;
    });
    const activeMenu = localStorage.getItem('activeMenu');
    if (activeMenu === 'H01_S01_FacilitySearch' || activeMenu === 'H02_S01_PersonalSearch') {
      this.isRouterH01H02 = true;
    }

    this.emitter.on('change-class-header', ({ changeClass }) => {
      this.isChangeClass = changeClass;
    });
  },
  methods: {
    closeNoti() {
      this.isShowPopupNoti = !this.isShowPopupNoti;
    },
    reloadFunction() {
      setInterval(() => {
        this.currentUser = this.getCurrentUser();
        if (!this.currentUser) {
          this.$router.push({ name: 'Z01_S01_Login' });
        }
      }, 4000);
    },
    handleYes() {
      localStorage.removeItem('checkChangTab');
      sessionStorage.removeItem('H01S04');
      sessionStorage.removeItem('H02S05');
      this.onResultModal();
      this.goBack();
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowClose: false, customClass: 'custom-dialog' };
    },
    checkDateWasEdit(filed) {
      const data = sessionStorage.getItem(filed);
      return data != null ? true : false;
    },
    goBack() {
      this.menus = JSON.parse(localStorage.getItem('router'));
      this.$emit('typeScreen', 'parent_children');

      const activeMenu = localStorage.getItem('activeMenu');

      const path = window.location.pathname;
      if (
        (activeMenu === 'F01_S05_Pre_ReleaseKnowledgeManagement' && path === '/knowledge-input') ||
        (activeMenu === 'F01_S01_KnowledgeSearch' && path === '/knowledge-details')
      ) {
        if (JSON.parse(localStorage.getItem('checkChangTab'))) {
          this.modalConfig = {
            ...this.modalConfig,
            isShowModal: true,
            component: this.markRaw(this.$PopupConfirm),
            width: '35rem',
            customClass: 'custom-dialog modal-fixed modal-fixed-min',
            props: { mode: 1, textCancel: 'いいえ' },
            isShowClose: false
          };
        } else {
          this.$router.push({ name: activeMenu });
        }
      } else {
        if (JSON.parse(localStorage.getItem('checkChangTab')) || this.checkDateWasEdit('H01S04') || this.checkDateWasEdit('H02S05')) {
          this.modalConfig = {
            ...this.modalConfig,
            isShowModal: true,
            component: this.markRaw(this.$PopupConfirm),
            width: '35rem',
            customClass: 'custom-dialog modal-fixed modal-fixed-min',
            props: { mode: 1, textCancel: 'いいえ' },
            isShowClose: false
          };
        } else {
          this.$emit('changeMode', { name: 'slide-left', type: 'back', type_screen: 'parent_children' });

          let activeMenu = localStorage.getItem('activeMenu');
          let activeUrl = this.menus.find((x) => x.name === activeMenu)?.path;
          let activeParent = localStorage.getItem('activeParent');

          let historyRoute = window.history.state.back;
          let crrRoute = window.history.state.current;

          if (historyRoute) {
            historyRoute = historyRoute.slice(0, historyRoute.indexOf('?') > -1 ? historyRoute.indexOf('?') : historyRoute.length);
          }
          crrRoute = crrRoute.slice(0, crrRoute.indexOf('?') > -1 ? crrRoute.indexOf('?') : crrRoute.length);

          if (this.isRouterH01H02) {
            if (historyRoute === crrRoute) {
              this.$router.push({ name: activeMenu });
            } else {
              if (crrRoute === activeUrl) {
                this.$router.push({ name: activeParent });
              } else {
                this.$router.go(-1);
              }
            }
          } else {
            // way 1: use component history from component child to back
            if (this.oldRoute) {
              this.$router.push({ name: this.oldRoute });
            } else {
              // way 2: use go -1 to back
              if (window?.history?.state?.back === '/' || !window?.history?.state?.back) {
                return this.$router.push('/home');
              }
              if (crrRoute === activeUrl) {
                if (!activeParent) {
                  this.$router.go(-1);
                } else {
                  this.$router.push({ name: activeParent });
                }
              } else {
                if (this.$route.name === 'H02_PersonalDetails' && this.isRouterH01H02) {
                  this.$router.push({
                    name: localStorage.getItem('activeMenu')
                  });
                } else if (this.$route.name === 'H01_FacilityDetails' && this.isRouterH01H02) {
                  this.$router.push({
                    name: localStorage.getItem('activeMenu')
                  });
                } else if (this.$route.path === this.$router.options.history.state.back) {
                  this.$router.go(-2);
                } else {
                  this.$router.go(-1);
                }
              }
            }
          }
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.iconBack {
  padding-right: 2px;
  cursor: pointer;
}
.header-custom {
  z-index: -99;
}
</style>
