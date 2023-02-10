<template>
  <div v-load-f5="true" class="btnInfo btnInfo--change">
    <div class="btnInfo-btn filter-wrapper">
      <button class="btn btn-filter" aria-expanded="false" @click="openFilter">
        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
      </button>
      <div :class="['dropdown-menu dropdown-filter dropdown-UserManage', isShowPopupFilter ? 'show' : '']">
        <div class="item-filter btn-close-filter" @click="closeUser">
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
                  <span title_log="組織" class="icon log-icon" @click="openModal_Z05_S01" @touchstart="openModal_Z05_S01">
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
            <li class="w-100">
              <label class="formUserManage-label">役職</label>
              <div class="formUserManage-control">
                <el-select v-model="director" placeholder="未選択" class="form-control-select">
                  <el-option class="form-control-select" label="" value=""> 未選択</el-option>
                  <el-option v-for="item in listScreenDataDirector" :key="item" :label="item.definition_label" :value="item.definition_value"> </el-option>
                </el-select>
              </div>
            </li>
            <li class="w-100">
              <label class="formUserManage-label">基準日</label>
              <div class="formUserManage-control">
                <div class="form-icon icon-left form-icon--noBod">
                  <span class="icon">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                  </span>
                  <el-date-picker
                    v-model="startDate"
                    :editable="false"
                    format="YYYY/M/D"
                    placeholder="日付選択"
                    class="form-control-datePickerLeft"
                  ></el-date-picker>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="btnFilter-UserManage">
          <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeUser">キャンセル</button>
          <button type="button" class="btn btn-primary btn-filter-submit" @click="submit">検索</button>
        </div>
      </div>
    </div>
  </div>
  <!-- <Z05S01Organization v-if="showModalZ05S01" :mode="props.mode" :user-flag="props.userFlag" :org-cd-list="props.orgCdList" @onResult="onResultModal" /> -->
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog modal-fixed"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModalZ05S01"></component>
    </template>
  </el-dialog>
</template>

<script>
import Z05S01ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import I01_S02_PermissionSetting from '../../../api/I01/I01_S02_PermissionSetting';
import { markRaw } from 'vue';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'I01_S02_FilterUserManagement',
  components: {
    Z05S01ModalOrganization
  },
  mixins: [filter_popup],
  emits: ['onFinishScreenFilter'],
  data() {
    return {
      // isShowPopupFilter: false,
      showModalZ05S01: false,
      orgCd: [],
      userName: '',
      userCd: '',
      startDate: '',
      director: '',
      listScreenDataDirector: [],
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      props: {
        mode: 'single',
        userFlag: 1,
        userSelectFlag: 1
      }
    };
  },
  mounted() {
    this.getScreen();
  },
  methods: {
    getScreen() {
      I01_S02_PermissionSetting.getScreen().then((res) => {
        this.listScreenDataDirector = res.data.data.director;
      });
    },
    openModal_Z05_S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01ModalOrganization),
        width: '45rem',
        props: {
          mode: 'multiple',
          userFlag: 0,
          userSelectFlag: 1,
          orgCdList: this.orgCd.map((x) => x.org_cd)
        }
      };
    },
    onCloseModalZ05S01(e) {
      if (e && e.screen === 'Z05_S01') {
        this.orgCd = e.orgSelected;
      }
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    openFilter() {
      this.isShowPopupFilter = !this.isShowPopupFilter;
    },
    closeUser() {
      this.isShowPopupFilter = false;
    },
    handleRemoveProduct(index) {
      this.orgCd.splice(index, 1);
    },
    submit() {
      let objOrgCd = [];
      this.orgCd.forEach((element) => {
        objOrgCd.push(element.org_cd);
      });
      const data = {
        user_name: this.userName,
        user_cd: this.userCd,
        org_cd: objOrgCd.toString(),
        reference_date: this.formatFullDate(this.startDate),
        director: this.director,
        limit: 100,
        offset: 0
      };
      localStorage.setItem('dataFilterI01S02', JSON.stringify(data));
      this.$emit('onFinishScreenFilter', data);
      this.isShowPopupFilter = false;
    }
  }
};
</script>

<style lang="scss" scoped>
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
