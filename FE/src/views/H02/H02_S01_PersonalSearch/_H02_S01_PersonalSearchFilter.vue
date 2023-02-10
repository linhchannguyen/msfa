<template>
  <div v-load-f5="true" class="btnInfo btnInfo--change">
    <div class="btnInfo-btn filter-wrapper">
      <button class="btn btn-filter" type="button" @click="openFilter">
        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
      </button>
      <div :class="['dropdown-menu dropdown-filter dropdown-personalSearch', isShowPopupFilter ? 'show' : '']">
        <div class="item-filter btn-close-filter" @click="isShowPopupFilter = false">
          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
        </div>
        <h2 class="title-filter">検索条件</h2>
        <div class="facilitySearch">
          <div class="facilityForm">
            <label class="facilityForm-label">注意事項</label>
            <div class="facilityForm-control">
              <ul class="facilityForm-btnSelect mfsa-custom-tab-select">
                <li v-for="item of listPrecautions" :key="item">
                  <button
                    type="button"
                    class="btn btn-select"
                    :class="filter.option === item.name ? 'active' : ''"
                    @click="precaution(item)"
                    @touchstart="precaution(item)"
                  >
                    {{ item.name }}
                  </button>
                </li>
              </ul>
              <ul class="checkDeleted">
                <li>
                  <label class="custom-control custom-checkbox custom-control--bordGreen">
                    <input
                      :checked="filter.move_flag"
                      class="custom-control-input"
                      type="checkbox"
                      @change="filter.move_flag ? (filter.move_flag = false) : (filter.move_flag = true)"
                    />
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">異動あり</span>
                  </label>
                </li>
                <li>
                  <label class="custom-control custom-checkbox custom-control--bordGreen">
                    <input
                      class="custom-control-input"
                      type="checkbox"
                      :checked="filter.ultmarc_delete_flag"
                      @change="filter.ultmarc_delete_flag ? (filter.ultmarc_delete_flag = false) : (filter.ultmarc_delete_flag = true)"
                    />
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">削除個人も含める</span>
                  </label>
                </li>
              </ul>
            </div>
          </div>
          <div class="facilityForm">
            <label class="facilityForm-label">担当者</label>
            <div class="facilityForm-control">
              <div class="form-icon icon-right">
                <span title_log="担当者" class="icon log-icon" @click="openModal_Z05_S01(filter)" @touchstart="openModal_Z05_S01(filter)">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                </span>
                <div v-if="filter.user_name.length > 0" class="form-control d-flex align-items-center">
                  <div class="block-tags">
                    <el-tag v-for="(item, index) of filter.user_name" :key="item" class="m-0 el-tag-custom" closable @close="handleRemoveUser(index, item)">
                      {{ item }}
                    </el-tag>
                  </div>
                </div>
                <el-input v-if="filter.user_name.length === 0" readonly clearable placeholder="未選択" class="form-control-input" />
              </div>
            </div>
          </div>
          <div class="facilityForm-row">
            <ul>
              <li>
                <div class="facilityForm">
                  <label class="facilityForm-label">個人名</label>
                  <div class="facilityForm-control">
                    <el-input v-model="filter.personName" clearable placeholder="名前入力" class="form-control-input" />
                  </div>
                </div>
              </li>
              <li>
                <div class="facilityForm">
                  <label class="facilityForm-label">個人コード</label>
                  <div class="facilityForm-control">
                    <el-input v-model="filter.personCd" clearable placeholder="コード入力" class="form-control-input" />
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="facilityForm-row">
            <ul>
              <li>
                <div class="facilityForm">
                  <label class="facilityForm-label">施設略名</label>
                  <div class="facilityForm-control">
                    <el-input v-model="filter.facility_short_name" clearable placeholder="略名入力" class="form-control-input" />
                  </div>
                </div>
              </li>
              <li>
                <div class="facilityForm">
                  <label class="facilityForm-label">施設コード</label>
                  <div class="facilityForm-control">
                    <el-input v-model="filter.facility_cd" clearable placeholder="コード入力" class="form-control-input" />
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="facilityForm-btn">
            <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeFilter">キャンセル</button>
            <button type="button" class="btn btn-primary" @click="submit">検索</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModalChild"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'H02_S01_PersonalSearchFilter',
  components: {
    Z05_S01_ModalOrganization
  },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreenFilter'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      toZ01S02: false,
      listPrecautions: [
        {
          id: 1,
          name: '指定なし'
        },
        {
          id: 2,
          name: 'あり'
        },
        {
          id: 3,
          name: '通知あり未確認'
        }
      ],
      personName: '',
      personCd: '',
      move_flag: false,
      ultmarc_delete_flag: false,
      facility_cd: '',
      facility_short_name: '',
      user_cd: [],
      user_name: [],
      historyDataFilter: {},
      currentUser: '',
      currentUserName: '',
      currentorgCd: '',
      filter: {
        option: '指定なし',
        personName: '',
        personCd: '',
        move_flag: false,
        ultmarc_delete_flag: false,
        facility_cd: '',
        facility_short_name: '',
        user_cd: [],
        user_name: [],
        userSelected: [],
        orgCd: [],
        limit: 100,
        offset: 0
      }
    };
  },
  mounted() {
    this.toZ01S02 = this.$route?.params?.precaution_id ? true : false;
    const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
    this.currentUser = currentUserProxy ? currentUserProxy?.user_cd : JSON.parse(localStorage.getItem('currentUser'))?.user_cd;
    this.currentUserName = currentUserProxy ? currentUserProxy?.user_name : JSON.parse(localStorage.getItem('currentUser'))?.user_name;
    this.currentorgCd = currentUserProxy ? currentUserProxy?.org_cd : JSON.parse(localStorage.getItem('currentUser'))?.org_cd;
    const data = JSON.parse(localStorage.getItem('DataH02'));
    if (!data) {
      this.filter = {
        option: this.toZ01S02 ? '通知あり未確認' : '指定なし',
        personName: '',
        personCd: '',
        move_flag: false,
        ultmarc_delete_flag: false,
        facility_cd: '',
        facility_short_name: '',
        user_cd: this.toZ01S02 ? [] : [this.currentUser],
        user_name: this.toZ01S02 ? [] : [this.currentUserName],
        orgCd: [this.currentorgCd],
        userSelected: [],
        limit: 100,
        offset: 0
      };
      this.submit();
    } else {
      // this.filter = data;
      this.filter = {
        option: data.option,
        personName: data.personName,
        personCd: data.personCd,
        move_flag: data.move_flag,
        ultmarc_delete_flag: data.ultmarc_delete_flag,
        facility_cd: data.facility_cd,
        facility_short_name: data.facility_short_name,
        user_cd: data.user_cd.length === 0 ? [] : data?.user_cd.length > 0 ? data?.user_cd || data?.user_cd.split(',') : data?.user_cd,
        user_name: data.user_name.length === 0 ? [] : data?.user_name.length > 0 ? data?.user_name || data?.user_name.split(',') : data?.user_name,
        orgCd: [this.currentorgCd],
        userSelected: data.userSelected,
        limit: 100,
        offset: 0
      };
      // this.submit();
    }
  },
  methods: {
    precaution(item) {
      this.filter.option = item.name;
    },
    openFilter() {
      this.isShowPopupFilter = true;
      if (this.filter.user_cd.length === 0) {
        this.filter.user_cd = [];
      }
      if (this.filter.user_name.length === 0) {
        this.filter.user_name = [];
      }
    },
    closeFilter() {
      this.isShowPopupFilter = false;
    },
    openModal_Z05_S01(filter) {
      const data = [{ user_cd: filter.user_cd, org_cd: filter.orgCd }];
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05_S01_ModalOrganization),
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        props: {
          mode: 'multiple',
          userFlag: 1,
          userSelectFlag: 1,
          orgCdList: filter.user_cd ? filter.orgCd : [],
          userCdList: filter.userSelected.length > 0 ? filter.userSelected : data
        }
      };
    },
    onCloseModalChild(data) {
      if (data) {
        this.filter.orgCd = [];
        this.filter.user_cd = [];
        this.filter.user_name = [];
        this.filter.userSelected = [];
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
        if (data?.screen === 'Z05_S01') {
          this.filter.userSelected = data.userSelected;
          data.userSelected.forEach((element) => {
            this.filter.user_cd.push(element.user_cd);
            this.filter.user_name.push(element.user_name);
          });
          data.orgSelected.forEach((element) => {
            this.filter.orgCd.push(element.org_cd);
          });
          localStorage.setItem('DataH02', JSON.stringify(this.filter));
        }
      } else {
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    },
    handleRemoveUser(index, item) {
      this.filter.user_cd.splice(index, 1);
      this.filter.user_name.splice(index, 1);
      if (this.filter?.userSelected.length > 0) {
        this.filter.userSelected.forEach((element) => {
          if (element.user_name === item) {
            this.filter.userSelected.splice(this.filter.userSelected.indexOf(item), 1);
          }
        });
      }
      if (this.filter.user_cd.length === 0 && this.filter.user_name.length === 0) {
        this.filter.orgCd = [];
        this.filter.userSelected = [];
      }
    },
    submit() {
      const data = {
        option: this.filter.option,
        person_cd: this.filter.personCd,
        person_name: this.filter.personName,
        facility_short_name: this.filter.facility_short_name,
        facility_cd: this.filter.facility_cd,
        user_cd: this.filter.user_cd.toString(),
        user_name: this.filter.user_name.toString(),
        move_flag: this.filter.move_flag ? 1 : 0,
        ultmarc_delete_flag: this.filter.ultmarc_delete_flag ? 1 : 0,
        limit: 100,
        offset: 0
      };
      this.$emit('onFinishScreenFilter', data);
      localStorage.setItem('DataH02', JSON.stringify(this.filter));
      this.closeFilter();
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.showFilter {
  display: block;
  position: absolute;
  top: 0;
}
.wrapBtn {
  .dropdown-personalSearch {
    top: 0;
    .item-filter {
      background: #448add;
      box-shadow: 0px 2px 4px rgba(13, 94, 153, 0.1), 0.5px 1px 5px 1px rgba(203, 225, 241, 0.3);
      border-radius: 0px 0px 0px 8px;
      width: 42px;
      height: 42px;
      right: 0;
      top: 0;
      position: absolute;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .title-filter {
      font-size: 1.125rem;
      font-weight: 700;
      color: #5f6b73;
      padding-bottom: 6px;
    }
    .facilityForm {
      margin-top: 8px;
      .facilityForm-label {
        color: #5f6b73;
        margin-bottom: 6px;
        line-height: 20px;
      }
      .facilityForm-btnSelect {
        display: flex;
        flex-wrap: wrap;
        li {
          width: 33.333333%;
          margin-left: -1px;
          &:first-of-type {
            .btn {
              border-radius: 4px 0 0 4px;
            }
          }
          &:last-child {
            .btn {
              border-radius: 0 4px 4px 0;
            }
          }
          .btn {
            width: 100%;
            padding: 0 4px;
            border-radius: 0;
            &.active {
              position: relative;
            }
          }
        }
      }
      .checkDeleted {
        display: flex;
        flex-wrap: wrap;
        margin: 10px 0 0 -24px;
        li {
          width: 50%;
          padding-left: 24px;
        }
        .custom-control,
        .custom-control-description {
          width: 100%;
        }
      }
    }
    .facilityForm-row {
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        li {
          padding-left: 10px;
          &:first-of-type {
            width: calc(100% - 145px);
          }
          &:last-child {
            width: 145px;
          }
        }
      }
    }
    .facilityForm-btn {
      text-align: center;
      margin-top: 24px;
      .btn {
        width: 142px;
        margin-right: 14px;
        &:last-child {
          margin-right: 0;
        }
      }
    }
  }
}
.personalContent {
  height: 100%;
  padding: 0 24px 6px;
  overflow: hidden;
  .personalTbl {
    tr {
      th {
        font-size: 1rem;
        &:nth-child(1) {
          width: 1rem;
          min-width: 320px;
        }
        &:nth-child(2) {
          width: 1rem;
          min-width: 220px;
        }
        &:nth-child(3) {
          width: 1rem;
          min-width: 80px;
        }
        &:nth-child(4) {
          width: 1rem;
          min-width: 272px;
        }
        &:nth-child(5),
        &:nth-child(6) {
          width: 1rem;
          min-width: 108px;
        }
        &:nth-child(7) {
          width: 1rem;
          min-width: 152px;
        }
      }
      th,
      td {
        vertical-align: middle;
        &:nth-child(3),
        &:nth-child(5),
        &:nth-child(6) {
          text-align: center;
        }
      }
      td {
        padding: 16px 12px;
        &:nth-child(5),
        &:nth-child(6) {
          font-size: 1rem;
        }
        .personalCode {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          .personalCode-info {
            width: calc(100% - 90px);
            .label-title {
              font-weight: 700;
              line-height: 120%;
            }
            .label-code {
              color: #99a5ae;
            }
          }
          .personalCode-btn {
            width: 90px;
            margin-left: auto;
            text-align: right;
            .btn {
              .btn-iconLeft {
                max-width: 14px;
                .svg {
                  width: 14px;
                }
              }
            }
          }
        }
        .personal-text {
          font-size: 1rem;
        }
        .personal-code {
          color: #99a5ae;
        }
      }
    }
  }
}
</style>
