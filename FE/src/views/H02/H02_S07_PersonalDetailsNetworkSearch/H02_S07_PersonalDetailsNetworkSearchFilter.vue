<template>
  <div v-load-f5="true" class="personalSearch-filter filter-wrapper">
    <button title_log="検索条件" class="btn btn-link btn-filter log-icon" aria-expanded="false" @click="openFilter(1)">
      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
    </button>
    <div :class="[dropdownFilter, isShowPopupFilter ? 'showFilter' : '']" class="dropdown-filter--search">
      <div class="item-filter btn-close-filter" @click="isShowPopupFilter = false">
        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
      </div>
      <h2 class="title-filter">検索条件</h2>
      <div class="groupSearch">
        <div class="formSearch-checkBox">
          <ul v-for="item of listSelectCheckBoxs" :key="item">
            <li>
              <div class="formSearch-check">
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input v-model="prefecture_name" class="custom-control-input" type="checkbox" @change="prefecture_nameF()" />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">出身県</span>
                </label>
              </div>
              <span class="formSearch-label">{{ item.prefecture_name }}</span>
            </li>
            <li>
              <div class="formSearch-check">
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input v-model="university_name" class="custom-control-input" type="checkbox" @change="university_nameF()" />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">出身大学</span>
                </label>
              </div>
              <span class="formSearch-label">{{ item.university_name }}</span>
            </li>
            <li>
              <div class="formSearch-check">
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input v-model="graduation_year" class="custom-control-input" type="checkbox" @change="graduation_yearF()" />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">卒業年</span>
                </label>
              </div>
              <span class="formSearch-label">{{ item._graduation_year }}</span>
            </li>
            <li>
              <div class="formSearch-check">
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input v-model="medical_office_name" class="custom-control-input" type="checkbox" @change="medical_office_nameF()" />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">医局名</span>
                </label>
              </div>
              <span class="formSearch-label">{{
                item.medical_office_name ? item.medical_office_name : item.facilityDepartment_name === 0 ? '' : item.facilityDepartment_name
              }}</span>
            </li>
            <li>
              <div class="formSearch-check">
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input v-model="work_place_history" class="custom-control-input" type="checkbox" @change="work_place_historyF()" />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">勤務先履歴</span>
                </label>
              </div>
              <div class="unselectedSearch formSearch-label">
                <div class="form-icon icon-right">
                  <span title_log="未選択" class="icon log-icon" @click="showModal" @touchstart="showModal">
                    <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                  </span>
                  <div v-if="work_place_Name" class="form-control d-flex align-items-center">
                    <div class="block-tags">
                      <el-tag class="m-0 el-tag-custom" closable @close="handleRemove()">
                        {{ work_place_Name }}
                      </el-tag>
                    </div>
                  </div>
                  <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                </div>
              </div>
              <!-- <span class="">{{ work_place_Name }}</span> -->
            </li>
          </ul>
        </div>

        <p class="notesSearch">※ OR条件での検索となります。</p>
        <div class="btnFilter-search">
          <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="isShowPopupFilter = false">キャンセル</button>
          <button type="button" class="btn btn-primary btn-filter-submit" @click="submit">検索</button>
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
    @close="closeModal"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onFinishScreen"></component>
    </template>
  </el-dialog>
</template>

<script>
import H02_S07_PersonalDetailsNetworkSearchModalSearchFilter from './H02_S07_PersonalDetailsNetworkSearchModalSearchFilter.vue';
import { markRaw } from 'vue';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'H02_S07_PersonalDetailsNetworkSearchFilter',
  components: { H02_S07_PersonalDetailsNetworkSearchModalSearchFilter },
  mixins: [filter_popup],
  props: {
    personCd: {
      type: String,
      default: ''
    },
    dropdownFilter: {
      type: String,
      default: ''
    },
    listSelectCheckBoxs: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  emits: ['onFinishScreenFilter'],
  data() {
    return {
      // isShowPopupFilter: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      loadingCheckBox: false,
      prefecture_name: false,
      university_name: false,
      graduation_year: false,
      medical_office_name: false,
      work_place_history: false,
      prefecture_CD: '',
      university_CD: '',
      graduation_CD: '',
      medical_office_CD: '',
      work_place_CD: '',
      work_place_Name: '',
      work_place_CDTam: ''
    };
  },
  mounted() {},
  methods: {
    handleRemove() {
      this.work_place_Name = '';
    },
    closeModal() {
      this.changeFalseClassHeader();
    },
    showModal() {
      this.changeTrueClassHeader();
      this.modalConfig = {
        ...this.modalConfig,
        title: '勤務先履歴選択',
        isShowModal: true,
        component: markRaw(H02_S07_PersonalDetailsNetworkSearchModalSearchFilter),
        props: { personCd: this.personCd ? this.personCd : '01101001' },
        width: '80%',
        destroyOnClose: true
      };
    },
    openFilter() {
      this.isShowPopupFilter = !this.isShowPopupFilter;
    },
    prefecture_nameF() {
      if (this.prefecture_name) {
        this.prefecture_CD = this.listSelectCheckBoxs[0].home_prefecture_cd;
      } else {
        this.prefecture_CD = '';
      }
    },
    university_nameF() {
      if (this.university_name) {
        this.university_CD = this.listSelectCheckBoxs[0].graduation_university_cd;
      } else {
        this.university_CD = '';
      }
    },
    graduation_yearF() {
      if (this.graduation_year) {
        this.graduation_CD = this.listSelectCheckBoxs[0].graduation_year;
      } else {
        this.graduation_CD = '';
      }
    },
    medical_office_nameF() {
      if (this.medical_office_name) {
        this.medical_office_CD = this.listSelectCheckBoxs[0].medical_office_name;
      } else {
        this.medical_office_CD = '';
      }
    },
    work_place_historyF() {
      if (this.work_place_history) {
        this.work_place_CD = this.work_place_CDTam;
      } else {
        this.work_place_CD = '';
      }
    },
    submit() {
      const data = {
        facility_cd: this.work_place_CD,
        graduation_year: this.graduation_CD ? this.graduation_CD : this.graduation_year ? this.listSelectCheckBoxs[0].graduation_year : '',
        graduation_university_cd: this.university_CD ? this.university_CD : this.university_name ? this.listSelectCheckBoxs[0].graduation_university_cd : '',
        home_prefecture_cd: this.prefecture_CD ? this.prefecture_CD : this.prefecture_name ? this.listSelectCheckBoxs[0].home_prefecture_cd : '',
        medical_office_name: this.medical_office_CD ? this.medical_office_CD : this.medical_office_name ? this.listSelectCheckBoxs[0].medical_office_name : '',
        medical_office_facility_cd: this.listSelectCheckBoxs.medical_office_facility_cd,
        medical_office_department_cd: this.listSelectCheckBoxs.medical_office_department_cd
      };
      this.isShowPopupFilter = !this.isShowPopupFilter;
      this.$emit('onFinishScreenFilter', data);
    },
    onFinishScreen(e) {
      if (e === 1) {
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      } else {
        this.work_place_Name = e.facility_short_name;
        this.work_place_CD = e.facility_cd;
        this.work_place_CDTam = e.facility_cd;
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
/** start css layout  **/
.showFilter {
  display: block;
  position: absolute;
  top: 0;
}
.groupPersonal {
  height: 100%;
  padding-top: 28px;
  background: #f2f2f2;
  @media (max-width: $viewport_tablet) {
    top: 16px;
  }
  .personalDetails {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .personalHead {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    padding: 12px 24px 0;
    .personalHead__user {
      width: 350px;
      padding-right: 10px;
      display: flex;
      align-items: flex-start;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-right: 0;
      }
      .personalHead-item {
        min-width: 22px;
        padding: 4px 8px 0 0;
      }
      .personalHead-content {
        .personalHead-label {
          .name {
            font-size: 24px;
            font-weight: 700;
            line-height: 140%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 220px;
            display: inline-block;
          }
          .btn {
            &.btn-light {
              border-radius: 2px;
              width: 60px;
              height: 24px;
              padding: 0 6px;
              margin: 6px 0 0 20px;
            }
          }
        }
        .personalHead-text {
          font-size: 16px;
          line-height: 120%;
        }
      }
    }
    .personalHead__info {
      width: calc(100% - 350px);
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 10px 0 0;
      }
      ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        @media (max-width: $viewport_tablet) {
          justify-content: flex-start;
        }
        li {
          margin-right: 36px;
          display: flex;
          @media (max-width: $viewport_desktop) {
            margin-right: 20px;
          }
          &:last-child {
            margin-right: 0;
          }
          .item {
            min-width: 20px;
            margin: -2px 5px 0 0;
          }
        }
      }
    }
  }
  .personalTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-top: 12px;
    .navTabs--personal {
      li {
        min-width: 195px;
        @media (max-width: $viewport_desktop) {
          min-width: 140px;
        }
        @media (max-width: $viewport_tablet) {
          min-width: inherit;
        }
        .navInfo {
          @media (max-width: $viewport_desktop) {
            padding: 12px 8px;
          }
          @media (max-width: $viewport_tablet) {
            font-size: 0.75rem;
          }
        }
        .navItem {
          @media (max-width: $viewport_tablet) {
            min-width: 15px;
            margin-right: 6px;
          }
        }
        .navItem {
          .svg {
            @media (max-width: $viewport_tablet) {
              width: 15px;
            }
          }
        }
      }
    }
    .tab-content {
      background: #fff;
      height: 100%;
      box-shadow: 0px 0px 5px #b7c3cb;
      position: relative;
      z-index: 1;
      overflow: hidden;
      .tab-pane {
        height: 100%;
      }
    }
  }
}
/** end css layout **/
/*** Css H02_S07_PersonalDetailsNetworkSearch ***/
.personalSearch {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  .personalSearch-filter {
    position: relative;
    display: flex;
    justify-content: flex-end;
    margin: 0 24px;
    .btn {
      &.btn-filter {
        padding: 0;
        width: 42px;
        height: 42px;
        border-radius: 0px 0px 8px 8px;
      }
    }
    .dropdown-filter--search {
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
      .formSearch-checkBox {
        ul {
          li {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 12px;
            &:last-child {
              margin-bottom: 0;
            }
            .formSearch-check {
              width: 120px;
              .custom-control,
              .custom-control-description {
                width: 100%;
              }
            }
            .formSearch-label {
              padding-left: 20px;
              width: calc(100% - 120px);
              color: #2d3033;
            }
          }
        }
      }
      .unselectedSearch {
        margin-top: 6px;
      }
      .notesSearch {
        color: #5f6b73;
        margin-top: 16px;
      }
      .btnFilter-search {
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
  .contentSearch {
    height: 100%;
    padding: 14px 24px 8px;
    overflow: hidden;
    .tblSearch {
      tr {
        th {
          font-size: 1rem;
          min-width: 160px;
        }
        td {
          .tblSearch-link {
            a {
              font-size: 1rem;
            }
          }
          .tblSearch-label {
            color: #99a5ae;
          }
        }
      }
    }
  }
  .paginationSearch {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 10px 24px 18px;
    .paginationSearch-cases {
      padding-right: 10px;
      color: #8e8e8e;
      font-weight: 500;
    }
  }
}
/*** Css modal ***/
.modal-workHistory {
  padding: 20px 24px 32px;
  .tbl-workHistory {
    max-height: 420px;
    tr {
      &:hover {
        td {
          font-weight: 700;
          background: #eef6ff;
        }
      }
      th {
        font-size: 1rem;
        min-width: 120px;
      }
      td {
        padding: 12px;
        .workHistory-link {
          font-size: 1rem;
        }
      }
    }
  }
  .btn-workHistory {
    text-align: center;
    margin-top: 20px;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.show {
  display: block;
  position: absolute;
  top: 0;
}
.filter-wrapper {
  position: absolute !important;
  right: 0;
  z-index: 5;
}
</style>
