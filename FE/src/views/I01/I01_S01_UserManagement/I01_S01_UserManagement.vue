<template>
  <div class="wrapContent">
    <div class="groupUserManage">
      <div class="wrapBtn">
        <div class="btnInfo">
          <button v-show="showBtnTabAUser" class="btn btn-sign msfa-custom-btn-create" @click="openModalCreateAUser()">
            <span class="btn-iconLeft"> <i class="el-icon-plus"></i> <span>新規登録</span> </span>
          </button>
        </div>
        <I01S01FilterUserManagement :tab="typeTabs" @onFinishScreenFilter="onFinishFilter" />
      </div>
      <div class="lstUserManage">
        <ul class="nav navTabs navTabs--userManage">
          <li>
            <a class="active" href="#aUser" role="tab" data-toggle="tab">
              <div class="navInfo" @click="tab(1)" @touchstart="tab(1)">
                <span class="navItem">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user01.svg')" alt="" />
                </span>
                ユーザ
              </div>
            </a>
          </li>
          <li>
            <a href="#organization" role="tab" data-toggle="tab">
              <div class="navInfo" @click="tab(2)" @touchstart="tab(2)">
                <span class="navItem">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building01.svg')" alt="" />
                </span>
                組織
              </div>
            </a>
          </li>
          <li>
            <a href="#authorizer" role="tab" data-toggle="tab">
              <div class="navInfo" @click="tab(3)" @touchstart="tab(3)">
                <span class="navItem">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user02.svg')" alt="" />
                </span>
                承認者
              </div>
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="aUser" role="tabpanel" class="tab-pane active">
            <I01S01TabAUser :key="keyTab" :data="loadDataTabs" :isfilter="isFilter" :objtab="datatabs" :changetab="changetab" />
          </div>
          <div id="organization" role="tabpanel" class="tab-pane">
            <I01S01TabOrganization :key="keyTab" :data="loadDataTabs" :isfilter="isFilter" :objtab="datatabs" :changetab="changetab" />
          </div>
          <div id="authorizer" role="tabpanel" class="tab-pane">
            <I01S01TabAuthorizer :key="keyTab" :data="loadDataTabs" :isfilter="isFilter" :objtab="datatabs" :changetab="changetab" />
          </div>
        </div>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog modal-fixed handle-close"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component :is="modalConfig.component" ref="modalChild" v-bind="{ ...modalConfig.props }" @onFinishScreen="onFinishScreenModalAUser"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
// import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import I01S01TabAUser from './I01_S01_TabAUser.vue';
import I01S01TabOrganization from './I01_S01_TabOrganization.vue';
import I01S01TabAuthorizer from './I01_S01_TabAuthorizer.vue';
import I01S01FilterUserManagement from './I01_S01_FilterUserManagement.vue';
import I01S01ModalUserRegistration from './I01_S01_ModalUserRegistration.vue';
import { markRaw } from 'vue';

export default {
  name: 'S02_Message',
  components: {
    I01S01TabAUser,
    I01S01TabOrganization,
    I01S01TabAuthorizer,
    I01S01FilterUserManagement,
    I01S01ModalUserRegistration
  },
  data() {
    return {
      listUsserMangement: [],
      showBtnTabAUser: true,
      typeTabs: 1,
      loadDataTabs: 0,
      datatabs: {},
      changetab: 0,
      isFilter: false,
      changeAllTab: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      keyTab: Math.random()
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'ユーザ管理',
      icon: 'icon-i01.svg',
      isShowBack: false
    });
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    tab(index) {
      this.typeTabs = index;
      if (index === 1) {
        this.showBtnTabAUser = true;
      } else {
        this.showBtnTabAUser = false;
      }
      this.changetab = index;
    },
    openModalCreateAUser() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ登録',
        isShowModal: true,
        component: markRaw(I01S01ModalUserRegistration),
        width: '40rem',
        destroyOnClose: true
      };
    },
    onFinishScreenModalAUser(index) {
      if (index === 1) {
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      } else {
        this.loadDataTabs = 1;
        setTimeout(() => {
          this.loadDataTabs = 0;
        }, 300);
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    },
    onFinishFilter(e, data) {
      // this.loadDataTabs = e;
      this.datatabs = data;
      this.isFilter = data.isSearch;
      this.keyTab = Math.random();
    },
    onFinishChangeTab(e) {
      if (e) {
        this.changeAllTab = true;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.navTabs {
  color: #b7c3cb;
  text-decoration: none;
  &:hover {
    .navItem {
      color: #b7c3cb;
      text-decoration: none;

      .svg {
        fill: #5f6b73;
        path {
          fill: #5f6b73;
          stroke: #5f6b73;
        }
      }
    }
  }
}
</style>
