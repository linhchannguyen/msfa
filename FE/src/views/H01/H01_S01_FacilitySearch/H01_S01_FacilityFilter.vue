<template>
  <div v-load-f5="true" class="btnInfo btnInfo--change">
    <div class="btnInfo-btn btnInfo-btn-c filter-wrapper">
      <button class="btn btn-filter" type="button" @click="openFilter">
        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
      </button>
      <div :class="['dropdown-menu dropdown-filter dropdown-facilitySearch', isShowPopupFilter ? 'show' : '']">
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
                    :class="filter.facility_consideration_options === item.name ? 'active' : ''"
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
                      class="custom-control-input"
                      type="checkbox"
                      :checked="filter.ultmarc_delete_flag"
                      @change="filter.ultmarc_delete_flag ? (filter.ultmarc_delete_flag = false) : (filter.ultmarc_delete_flag = true)"
                    />
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">削除施設も含める</span>
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
                <el-input v-if="filter.user_name.length === 0" clearable readonly placeholder="未選択" class="form-control-input" />
              </div>
            </div>
          </div>
          <div class="facilityForm-row">
            <ul>
              <li>
                <div class="facilityForm">
                  <label class="facilityForm-label">施設名</label>
                  <div class="facilityForm-control">
                    <el-input v-model="filter.facility_short_name" clearable placeholder="略名入力" class="form-control-input" />
                  </div>
                </div>
              </li>
              <li>
                <div class="facilityForm">
                  <label class="facilityForm-label">施設コード</label>
                  <div class="facilityForm-control">
                    <el-input v-model="filter.facility_cd" placeholder="コード入力" clearable class="form-control-input" />
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="facilityForm">
            <label class="facilityForm-label">電話番号</label>
            <div class="facilityForm-control">
              <el-input v-model="filter.phone" type="text" clearable placeholder="番号入力" class="form-control-input no-spin" @keypress="onlyNumber" />
            </div>
            <p v-if="isSubmit && !validation.phone.length" class="text-error">{{ getMessage('MSFA0012', 'コメント', 15) }}</p>
          </div>
          <div class="facilityForm">
            <label class="facilityForm-label">所在地</label>
            <div class="facilityForm-control">
              <div class="form-icon icon-right">
                <span title_log="所在地" class="icon log-icon" @click="openModal_Z05_S02" @touchstart="openModal_Z05_S02">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                </span>
                <div v-if="filter.prefecture_name || filter.municipality_name" class="form-control d-flex align-items-center">
                  <div class="block-tags">
                    <el-tag class="m-0 el-tag-custom" closable @close="handleRemovePrefectureOrMunicipality()">
                      {{ filter.municipality_name ? filter.municipality_name : filter.prefecture_name }}
                    </el-tag>
                  </div>
                </div>
                <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
              </div>
            </div>
          </div>
          <div class="facilityForm-btn">
            <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="isShowPopupFilter = false">キャンセル</button>
            <button type="button" class="btn btn-primary btn-filter-submit" @click="submit">検索</button>
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModalChild"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { markRaw } from 'vue';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05_S02_ModalAreaSelection from '@/views/Z05/Z05_S02_Area_Selection/Z05_S02_Area_Selection';
import { validLength } from '@/utils/validate';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'H01_S01_FacilityFilter',
  components: {
    Z05_S01_ModalOrganization,
    Z05_S02_ModalAreaSelection
  },
  mixins: [filter_popup],
  emits: ['onFinishScreenFilter'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      municipality_cd: '',
      prefecture_cd: '',
      municipality_name: '',
      prefecture_name: '',
      user_cd: [],
      user_name: [],
      toZ01S02: false,
      phone: '',
      ultmarc_delete_flag: false,
      facility_cd: '',
      facility_short_name: '',
      historyDataFilter: {},
      currentUser: '',
      currentUserName: '',
      currentorgCd: '',
      filter: {
        user_cd: [],
        user_name: [],
        orgCd: [],
        userSelected: [],
        facility_short_name: '',
        facility_cd: '',
        phone: '',
        prefecture_cd: '',
        prefecture_name: '',
        municipality_cd: '',
        municipality_name: '',
        municipality: '',
        ultmarc_delete_flag: false,
        facility_consideration_options: '指定なし',
        limit: '',
        offset: ''
      },
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
      ]
    };
  },
  computed: {
    validation() {
      return {
        phone: {
          length: validLength(this.filter.phone, 15)
        }
      };
    }
  },

  mounted() {
    this.toZ01S02 = this.$route?.params?.precaution_id ? true : false;
    const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
    this.currentUser = currentUserProxy ? currentUserProxy?.user_cd : JSON.parse(localStorage.getItem('currentUser'))?.user_cd;
    this.currentUserName = currentUserProxy ? currentUserProxy?.user_name : JSON.parse(localStorage.getItem('currentUser'))?.user_name;
    this.currentorgCd = currentUserProxy ? currentUserProxy?.org_cd : JSON.parse(localStorage.getItem('currentUser'))?.org_cd;
    const data = JSON.parse(localStorage.getItem('DataH01'));
    if (!data) {
      this.filter = {
        user_cd: this.toZ01S02 ? [] : [this.currentUser],
        user_name: this.toZ01S02 ? [] : [this.currentUserName],
        orgCd: [this.currentorgCd],
        facility_short_name: '',
        facility_cd: '',
        phone: '',
        prefecture_cd: '',
        prefecture_name: '',
        municipality_cd: '',
        municipality_name: '',
        municipality: '',
        ultmarc_delete_flag: false,
        facility_consideration_options: this.toZ01S02 ? '通知あり未確認' : '指定なし',
        userSelected: []
      };
      this.submit();
    } else {
      this.filter = {
        user_cd: data.user_cd?.length === 0 ? [] : data?.user_cd.length > 0 ? data?.user_cd.split(',') : data?.user_cd,
        user_name: data.user_name?.length === 0 ? [] : data?.user_name.length > 0 ? data?.user_name.split(',') : data?.user_name,
        orgCd: [this.currentorgCd],
        facility_short_name: data.facility_short_name,
        facility_cd: data.facility_cd,
        phone: data.phone,
        prefecture_cd: data.prefecture_cd,
        prefecture_name: data.prefecture_name,
        municipality_cd: data.municipality_cd,
        municipality_name: data.municipality_name,
        municipality: data.municipality,
        ultmarc_delete_flag: data.ultmarc_delete_flag,
        facility_consideration_options: data.facility_consideration_options,
        userSelected: data.userSelected
      };
      // this.submit();
    }
  },
  methods: {
    precaution(item) {
      this.filter.facility_consideration_options = item.name;
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
    openModal_Z05_S01(filter) {
      const data = [{ user_cd: filter.user_cd, org_cd: filter.orgCd }];
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05_S01_ModalOrganization),
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        customClass: 'custom-dialog',
        props: {
          mode: 'multiple',
          userFlag: 1,
          userSelectFlag: 1,
          orgCdList: filter.user_cd ? filter.orgCd : [],
          userCdList: filter?.userSelected ? filter.userSelected : data
        }
      };
    },
    openModal_Z05_S02() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '地区選択',
        isShowModal: true,
        component: markRaw(Z05_S02_ModalAreaSelection),
        width: '47rem',
        customClass: 'custom-dialog modal-fixed',
        props: {
          mode: 'single',
          hierarchySelected: 'municipality',
          prefectureCode: this.filter.prefecture_cd,
          municipalityCode: this.filter.municipality_cd
        }
      };
    },
    onCloseModalChild(data) {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      if (data) {
        if (data?.screen === 'Z05_S01') {
          this.filter.orgCd = [];
          this.filter.user_cd = [];
          this.filter.user_name = [];
          this.filter.userSelected = [];
          this.filter.userSelected = data.userSelected;
          data.userSelected.forEach((element) => {
            this.filter.user_cd.push(element.user_cd);
            this.filter.user_name.push(element.user_name);
          });
          data.orgSelected.forEach((element) => {
            this.filter.orgCd.push(element.org_cd);
          });
          localStorage.setItem('DataH01', JSON.stringify(this.filter));
        } else if (data?.screen === 'Z05_S02') {
          this.filter.prefecture_cd = '';
          this.filter.prefecture_name = '';
          this.filter.municipality_cd = '';
          this.filter.municipality_name = '';
          this.filter.prefecture_cd = data.prefectureSelected.prefecture_cd;
          this.filter.prefecture_name = data.prefectureSelected.prefecture_name;
          this.filter.municipality_cd = data.municipalitySelected.municipality_cd;
          this.filter.municipality_name = data.municipalitySelected.municipality_name;
        }
      }
    },
    handleRemovePrefectureOrMunicipality() {
      this.filter.prefecture_cd = '';
      this.filter.prefecture_name = '';
      this.filter.municipality_cd = '';
      this.filter.municipality_name = '';
    },
    handleRemoveUser(index, item) {
      this.filter.user_cd.splice(index, 1);
      this.filter.user_name.splice(index, 1);
      this.filter.userSelected?.forEach((element) => {
        if (element.user_name === item) {
          this.filter.userSelected.splice(this.filter.userSelected.indexOf(item), 1);
        }
      });
      if (this.filter.user_cd.length === 0 && this.filter.user_name.length === 0) {
        this.filter.orgCd = [];
        this.filter.userSelected = [];
      }
    },
    onlyNumber($event) {
      const keyCodeArr = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-'];
      if (!keyCodeArr.includes($event.key)) {
        $event.preventDefault();
      }
    },
    submit() {
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      } else {
        const data = {
          user_cd: this.filter.user_cd.toString(),
          user_name: this.filter.user_name.toString(),
          facility_short_name: this.filter.facility_short_name,
          facility_cd: this.filter.facility_cd,
          phone: this.filter.phone,
          prefecture_cd: this.filter.prefecture_cd,
          prefecture_name: this.filter.prefecture_name,
          municipality_cd: this.filter.municipality_cd,
          municipality_name: this.filter.municipality_name,
          municipality: this.filter.municipality,
          ultmarc_delete_flag: this.filter.ultmarc_delete_flag ? 1 : 0,
          facility_consideration_options: this.filter.facility_consideration_options,
          limit: 100,
          offset: 0
        };
        this.$emit('onFinishScreenFilter', data);
        localStorage.setItem('DataH01', JSON.stringify(data));
        this.isShowPopupFilter = false;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.btnInfo-btn-c {
  margin-right: 3px !important;
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
.wrapBtn {
  .dropdown-facilitySearch {
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
.facilityContent {
  height: 100%;
  padding: 0 24px 6px;
  overflow: hidden;
  .facilityTbl {
    tr {
      th {
        font-size: 1rem;
        min-width: 210px;
        &:nth-child(1) {
          width: 1rem;
          min-width: 700px;
        }
        &:nth-child(2) {
          width: 1rem;
          min-width: 200px;
        }
      }
      td {
        padding: 16px 12px;
        vertical-align: middle;
        .facilityCode {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          .facilityCode-info {
            width: calc(100% - 90px);
            .label-title {
              font-size: 1rem;
              font-weight: 700;
              line-height: 120%;
            }
            .label-code {
              color: #99a5ae;
            }
          }
          .facilityCode-btn {
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
        .facilityItem {
          display: flex;
          .item {
            min-width: 18px;
            margin-right: 8px;
          }
        }
      }
    }
  }
}
.showFilter {
  display: block;
  position: absolute;
  top: 0;
}
.dropdown-filter {
  top: 0;
}
</style>
