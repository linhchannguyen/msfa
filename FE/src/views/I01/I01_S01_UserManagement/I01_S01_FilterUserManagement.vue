<template>
  <div v-load-f5="true" class="btnInfo btnInfo--change">
    <div class="btnInfo-btn filter-wrapper">
      <button type="button" class="btn btn-filter" aria-expanded="false" @click="openFilter">
        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
      </button>
      <div :class="['dropdown-menu dropdown-filter dropdown-UserManage', isShowPopupFilter ? 'show' : '']">
        <div class="item-filter btn-close-filter" @click="isShowPopupFilter = false">
          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
        </div>
        <h2 class="title-filter">検索条件</h2>
        <div class="formUserManage">
          <ul>
            <li class="w-100">
              <label class="formUserManage-label">ユーザ名</label>
              <div class="formUserManage-control">
                <el-input v-model="userName" clearable placeholder="名前入力" class="form-control-input" />
              </div>
            </li>
            <li class="w-100">
              <label class="formUserManage-label">ユーザコード</label>
              <div class="formUserManage-control">
                <el-input v-model="userCd" clearable placeholder="コード入力" class="form-control-input" />
              </div>
            </li>
            <li class="w-100">
              <label class="formUserManage-label">組織</label>
              <div class="formUserManage-control">
                <div class="form-icon icon-right">
                  <span
                    title_log="組織"
                    data-toggle="modal"
                    data-target="#modalOrganization_Z05_S01"
                    class="icon log-icon"
                    data-backdrop="static"
                    @click="openModalZ05S01"
                    @touchstart="openModalZ05S01"
                  >
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                  </span>
                  <div v-if="orgCd.length > 0" class="form-control d-flex align-items-center">
                    <div class="block-tags">
                      <el-tag v-for="(item, index) in orgCd" :key="item" class="m-0 el-tag-custom" closable @close="handleRemoveProduct(index)">
                        {{ item.org_label }}
                      </el-tag>
                    </div>
                  </div>
                  <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="btnFilter-UserManage">
          <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="isShowPopupFilter = false">キャンセル</button>
          <button type="button" class="btn btn-primary btn-filter-submit" @click="submit(true)">検索</button>
        </div>
      </div>
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModalZ05S01"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'I01_S01_FilterUserManagement',
  components: {
    Z05S01Organization
  },
  mixins: [filter_popup],
  props: {
    tab: {
      type: Number,
      default: 1
    }
  },
  emits: ['onFinishScreenFilter'],
  data() {
    return {
      // isShowPopupFilter: false,
      showModalZ05S01: false,
      userFlag: 0,
      userName: '',
      userCd: '',
      orgCd: [],
      orgCds: [],
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      props: {
        userFlag: 0,
        mode: 'multiple',
        orgCdList: []
      }
    };
  },
  mounted() {
    this.emitter.on('change-class-header', ({ changeClass }) => {
      if (changeClass) {
        this.isShowPopupFilter = false;
      }
    });
    // const dataLocal = JSON.parse(localStorage.getItem('screen')) ? JSON.parse(localStorage.getItem('screen')) : {};
    // this.userCd = dataLocal.userCd ? dataLocal.userCd : '';
    // this.userName = dataLocal.userName ? dataLocal.userName : '';
    // this.orgCd = dataLocal.orgCd ? dataLocal.orgCd : '';

    this.props.orgCdList = this.orgCd?.map((x) => x.org_cd);
    this.submit(false);
  },
  methods: {
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.props },
        width: '45rem',
        customClass: 'custom-dialog modal-fixed',
        destroyOnClose: true
      };
    },
    openFilter() {
      this.isShowPopupFilter = !this.isShowPopupFilter;
      // const dataLocal = JSON.parse(localStorage.getItem('screen')) ? JSON.parse(localStorage.getItem('screen')) : {};
      // this.userCd = dataLocal.userCd ? dataLocal.userCd : '';
      // this.userName = dataLocal.userName ? dataLocal.userName : '';
      // this.orgCd = dataLocal.orgCd ? dataLocal.orgCd : '';
    },
    closeUser() {
      this.isShowPopupFilter = false;
      this.userName = '';
      this.userCd = '';
      this.orgCd = [];
    },
    handleRemoveProduct(index) {
      this.orgCd?.splice(index, 1);
      this.props.orgCdList = this.orgCd?.map((x) => x.org_cd);
    },
    onResultModalZ05S01(e) {
      if (e) {
        this.orgCd = e.orgSelected;

        this.props.orgCdList = e.orgSelected.map((x) => x.org_cd);

        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      } else {
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      }
    },
    // add when select multi full hierarchy num
    flaternDataOrg(node) {
      if (!this.orgCds.includes(node.org_cd)) {
        this.orgCds.push(node.org_cd);
      }
      if (node?.children?.length > 0) {
        for (let i = 0; i < node.children.length; i++) {
          this.flaternDataOrg(node.children[i]);
        }
      }
    },
    submit(isSearch) {
      this.orgCds = [];
      let orgLocal = this.orgCd !== '' ? this.orgCd : [];
      if (orgLocal?.length > 0) {
        for (let i = 0; i < orgLocal.length; i++) {
          this.flaternDataOrg(orgLocal[i]);
        }
      }
      const data = {
        user_cd: this.userCd,
        user_name: this.userName,
        org_cd: this.orgCds.toString(),
        limit: 100,
        offset: 0,
        isSearch: isSearch
      };
      this.$emit('onFinishScreenFilter', this.tab, data);
      this.isShowPopupFilter = false;
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.el-tag-custom {
  height: 30px;
  line-height: 26px;
  font-size: 14px;
  align-items: center;
  margin-right: 10px !important;
  background: #b7d4ff;
  border-radius: 24px;
}
.showFilter {
  display: block;
  position: absolute;
  top: 0;
}
.iconClear {
  cursor: pointer;
  position: absolute;
  right: 35px;
  top: 33%;
  width: 20px;
}
.customInput {
  input {
    padding-right: 66px !important;
  }
}
.dropdown-filter {
  top: 0;
}
</style>
