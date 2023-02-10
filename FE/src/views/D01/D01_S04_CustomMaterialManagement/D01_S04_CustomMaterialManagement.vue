<template>
  <div v-loading="loadingPage" v-load-f5="true" class="wrapContent">
    <div class="groupContent groupContent--management">
      <div class="wrapBtn">
        <div class="btnInfo">
          <button type="button" class="btn btn-sign msfa-custom-btn-create" @click="redirectToCustomMaterialCreationD01S05">
            <span class="btn-iconLeft"><i class="el-icon-plus"></i> <span>新規登録</span> </span>
          </button>
        </div>
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button type="button" class="btn btn-filter" :class="isShowPopupFilter ? 'btn-filter-none-shadow' : ''" @click="openModalFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter dropdown-management', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter" @click="onCloseFilter">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="form-management">
                <ul>
                  <li class="w-100">
                    <label class="form-management-label">利用状況</label>
                    <div class="form-management-control">
                      <ul class="form-management-select">
                        <li
                          v-for="item in lstDisuseFlag"
                          :id="item.value"
                          :key="item.value"
                          :class="formData.disuse_flag === item.value ? 'active' : ''"
                          @click="setSelectedDisuseFlag(item)"
                          @touchstart="setSelectedDisuseFlag(item)"
                        >
                          {{ item.title }}
                        </li>
                      </ul>
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="form-management-label">資材名</label>
                    <div class="form-management-control">
                      <el-input v-model="formData.document_name" clearable placeholder="資材名を入力" class="form-control-input" />
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="form-management-label">品目</label>
                    <div class="form-management-control">
                      <div class="form-icon icon-right">
                        <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                          <img src="@/assets/template/images/icon_popup.svg" alt="" />
                        </span>
                        <div v-if="formData.lstProduct.length > 0" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag
                              v-for="product in formData.lstProduct"
                              :key="product.product_cd"
                              class="el-tag-custom el-tag-icon-top"
                              closable
                              @close="handleRemoveProduct(product)"
                            >
                              {{ product.product_name }}
                            </el-tag>
                          </div>
                        </div>

                        <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                      </div>
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="form-management-label">対象疾患</label>
                    <div class="form-management-control">
                      <el-input v-model="formData.disease" clearable placeholder="内容入力" class="form-control-input" />
                    </div>
                  </li>
                  <li>
                    <label class="form-management-label">診療科</label>
                    <div class="form-management-control">
                      <el-select v-model="formData.medical_subjects_group_cd" placeholder="未選択" class="form-control-select">
                        <el-option value=""> 未選択</el-option>
                        <el-option
                          v-for="item in lstMedicalSubjectGroup"
                          :key="item.medical_subjects_group_cd"
                          :label="item.medical_subjects_group_name"
                          :value="item.medical_subjects_group_cd"
                        >
                        </el-option>
                      </el-select>
                    </div>
                  </li>
                  <li>
                    <label class="form-management-label">適用対象日</label>
                    <div class="form-management-control">
                      <div class="form-icon icon-left form-icon--noBod">
                        <span class="icon">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                        </span>
                        <el-date-picker
                          v-model="formData.applicable_date"
                          type="date"
                          :editable="false"
                          format="YYYY/M/D"
                          value-format="YYYY/M/D"
                          placeholder="日付選択"
                          class="form-control-datePickerLeft"
                        ></el-date-picker>
                      </div>
                    </div>
                  </li>
                  <li>
                    <label class="form-management-label">安全性情報</label>
                    <div class="form-management-control">
                      <ul class="form-management-select">
                        <li
                          v-for="item in lstInfoFlag"
                          :key="item.definition_value"
                          :class="formData.safety_information_flag === item.definition_value ? 'active' : ''"
                          @click="formData.safety_information_flag = item.definition_value"
                        >
                          {{ item.definition_label }}
                        </li>
                      </ul>
                    </div>
                  </li>
                  <li>
                    <label class="form-management-label">適応外情報</label>
                    <div class="form-management-control">
                      <ul class="form-management-select">
                        <li
                          v-for="item in lstInfoFlag"
                          :key="item.definition_value"
                          :class="formData.off_label_information_flag === item.definition_value ? 'active' : ''"
                          @click="formData.off_label_information_flag = item.definition_value"
                        >
                          {{ item.definition_label }}
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="btn-management text-center">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCloseFilter">キャンセル</button>
                <button type="button" class="btn btn-primary" @click="onFilterData(true, false)">検索</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div ref="lstDocument" class="list-management scrollbar">
        <ul v-if="lstDocument.length > 0">
          <li v-for="document in lstDocument" :key="document.document_id" :class="checkDocumentViewer(document) ? 'active' : ''">
            <h2 class="listTitle">
              <span class="link log-icon" title_log="資材名" @click="redirectToD01S02(document)">
                <span class="listTitle-item">
                  <img
                    v-if="document.document_type == '10' && document.file_type == '10'"
                    v-svg-inline
                    svg-inline
                    class="svg"
                    :src="require('@/assets/template/images/icon_pdf-manag.svg')"
                    alt=""
                  />
                  <img
                    v-if="document.document_type == '10' && document.file_type == '20'"
                    v-svg-inline
                    svg-inline
                    class="svg"
                    :src="require('@/assets/template/images/icon_film_1.svg')"
                    alt=""
                  />
                  <img
                    v-if="document.document_type == '20'"
                    v-svg-inline
                    svg-inline
                    class="svg"
                    :src="require('@/assets/template/images/icon_document_spanner_1.svg')"
                    alt=""
                  />
                </span>
                {{ document.document_name }}
              </span>
              <div v-if="checkOutsideValid(document.start_date, document.end_date)" class="outsite-valid">【有効期間外】</div>
              <div v-if="document.edit_mode === 1" class="notEdit-valid">編集不可</div>
            </h2>
            <div class="listContent">
              <ul>
                <li>
                  <span class="listContent-tlt">品目：</span>
                  <span class="listContent-label">{{ document.product_list }}</span>
                </li>
                <li>
                  <span class="listContent-tlt">対象疾患：</span>
                  <span class="listContent-label">{{ document.disease }}</span>
                </li>
                <li>
                  <span class="listContent-tlt">診療科：</span>
                  <span class="listContent-label">{{ document.medical_subjects_group_name }}</span>
                </li>
                <li>
                  <span class="listContent-tlt">安全性情報：</span>
                  <span class="listContent-label">{{ document.definition_label_a8 }}</span>
                </li>
                <li>
                  <span class="listContent-tlt">適応外情報：</span>
                  <span class="listContent-label">{{ document.definition_label_a9 }}</span>
                </li>
              </ul>
              <div class="listContent-btn">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="openModalMaterialViewerD01S07(document)">
                  <span class="btn-iconLeft">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slideshow.svg')" alt="" />
                  </span>
                  ビューワ
                </button>
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="copyDataDocument(document)">
                  <span class="btn-iconLeft">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_copy.svg')" alt="" />
                  </span>
                  コピー
                </button>
              </div>
            </div>
          </li>
        </ul>
        <EmptyData v-else-if="!isLoadDefault" title="該当するデータがありません。" custom-class="nodata" />
      </div>
    </div>

    <div v-if="lstDocument.length > 0" class="pagination pagination-custom">
      <div class="pagination-cases">全 {{ pagination.totalItems }} 件</div>
      <PaginationTable
        :page-size="pagination.pageSize"
        :total="pagination.totalItems"
        :current-page="pagination.curentPage"
        @current-change="handleCurrentChange"
      />
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
import { markRaw } from 'vue';
// import _ from 'lodash';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import D01_S07_MaterialViewer from '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer';

import D01_S04_CustomMaterialManagementService from '@/api/D01/D01_S04_CustomMaterialManagementService';
import moment from 'moment';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'D01_S04_CustomMaterialManagement',
  components: {},
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean],
    propsData: {
      type: Object,
      required: false,
      default() {
        return {
          disuse_flag: '', // '' , 0, 1
          document_name: '',
          medical_subjects_group_cd: '',
          lstProduct: [],
          disease: '',
          applicable_date: moment().format('YYYY/M/D'),
          safety_information_flag: '', // '', 1, 0
          off_label_information_flag: '' // '', 1, 0
        };
      }
    }
  },
  data: function () {
    return {
      loadingPage: false,
      isLoadDefault: true,
      // isShowPopupFilter: false,

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalD01S02: false,
        component: null,
        customClass: 'custom-dialog',
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      paramsZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      isSearch: false,
      isCopy: false,

      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },

      formData: {
        disuse_flag: '',
        document_name: this.propsData.document_name || '',
        medical_subjects_group_cd: this.propsData.medical_subjects_group_cd || '',
        lstProduct: this.propsData.lstProduct || [],
        disease: this.propsData.disease || '',
        applicable_date: this.propsData.applicable_date ? this.propsData.applicable_date : moment().format('YYYY/M/D'),
        safety_information_flag: this.propsData.safety_information_flag || '',
        off_label_information_flag: this.propsData.off_label_information_flag || '',
        offset: 0
      },

      paramFilterOld: {},

      lstMedicalSubjectGroup: [],
      lstInfoFlag: [],
      lstDisuseFlag: [
        {
          value: '',
          title: '全て'
        },
        {
          value: '0',
          title: '利用中'
        },
        {
          value: '1',
          title: '終了'
        }
      ],

      lstDocument: [],
      lstDocumentViewer: [],
      onScrollTop: 0
    };
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'カスタム資材管理',
      icon: 'Material-icon.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.D01_S04_CustomMaterialManagement || 0;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.D01_S04_CustomMaterialManagement || 1;

    this.getDataScreen();
    this.paramFilterOld = { ...this.formData };
  },

  methods: {
    setScrollTop() {
      let scrollTop = this.$refs.lstDocument ? this.$refs.lstDocument.scrollTop : 0;
      this.setScrollScreen('D01_S04_CustomMaterialManagement', scrollTop);
    },
    getDataScreen() {
      this.loadingPage = true;
      D01_S04_CustomMaterialManagementService.getDataScreen()
        .then((res) => {
          let data = res.data.data;
          this.lstMedicalSubjectGroup = data.medical_subjects_group;
          this.lstInfoFlag = data.variable_definition;
          this.lstInfoFlag.unshift({
            definition_value: '',
            definition_label: '全て'
          });
          const screen = this.decodeData(localStorage.getItem('screen'));
          if (this.propsData.disuse_flag) {
            const disuse_flag = screen.find((s) => s.screen_name === 'D01_S04_CustomMaterialManagementService');
            const formData = { value: disuse_flag && disuse_flag['formData'] ? disuse_flag['formData']['disuse_flag'] : '' };

            if (this.propsData.disuse_flag) {
              let item = this.lstDisuseFlag.find((x) => x.value == this.propsData.disuse_flag);

              this.setSelectedDisuseFlag(formData ? formData : item);
            } else {
              this.setSelectedDisuseFlag(formData ? formData : this.lstDisuseFlag[0]);
            }
          }
          this.onFilterData(false, true);
        })
        .catch((err) => {
          this.loadingPage = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    getDataDocumentByCondition(isFilter, isLoadDefault) {
      this.loadingPage = true;
      this.isLoadDefault = isLoadDefault;

      let params = {
        ...this.formData,
        product_cd: this.formData.lstProduct.map((x) => x.product_cd),
        lstProduct: null,
        offset: this.pagination.curentPage - 1,
        limit: this.pagination.pageSize
      };
      this.paramFilterOld = { ...this.formData };
      D01_S04_CustomMaterialManagementService.getDataByCondition(params)
        .then((res) => {
          this.isShowPopupFilter = false;
          this.isSearch = true;
          let data = res.data.data;
          let pageined = res.data?.data?.pagination;
          this.lstDocument = data.records;
          this.pagination = {
            ...this.pagination,
            totalItems: pageined.total_items
          };
        })
        .catch((err) => {
          this.lstDocument = [];
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.isSearch = false;
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 500));
          this.loadingPage = false;
          this.isLoadDefault = false;

          this.setCurrentPageScreen('D01_S04_CustomMaterialManagement', this.pagination.curentPage);

          if (this.$refs.lstDocument) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.lstDocument.scrollTop = this.onScrollTop;
            } else {
              this.$refs.lstDocument.scrollTop = 0;
            }
          }
          this.js_pscroll();
        });
    },

    copyDataDocument(document) {
      if (!this.isCopy) {
        this.setScrollTop();
        this.isCopy = true;
        this.$router.push({
          name: 'D01_S05_CustomMaterialCreation',
          params: { component: 'D01_S04_CustomMaterialManagement', copy_id: document.document_id }
        });
        this.isCopy = false;
      }
    },

    checkOutsideValid(start_date, end_date) {
      let today = new Date(new Date().setHours(0, 0, 0, 0));

      if (
        new Date(new Date(start_date).setHours(0, 0, 0, 0)).getTime() <= today.getTime() &&
        today.getTime() <= new Date(new Date(end_date).setHours(0, 0, 0, 0)).getTime()
      ) {
        return false;
      }
      return true;
    },

    onFilterData(isFilter, isLoadDefault) {
      if (isFilter) {
        this.pagination = {
          ...this.pagination,
          curentPage: 1,
          totalItems: 0
        };
      }

      if (!this.isSearch) {
        this.getDataDocumentByCondition(isFilter, isLoadDefault);
      }
    },

    handleCurrentChange(val) {
      this.onCloseFilter();
      this.onScrollTop = 0;
      this.pagination.curentPage = val;
      this.getDataDocumentByCondition(true);
    },

    // modal filter
    openModalFilter() {
      this.isShowPopupFilter = true;
      this.isSearch = false;
    },

    onCloseFilter() {
      this.isShowPopupFilter = false;
    },

    setSelectedDisuseFlag(item) {
      this.formData.disuse_flag = item.value;
      this.lstDisuseFlag.forEach((x) => {
        x.selected = false;
        if (x.value === item.value) {
          x.selected = true;
        }
      });
    },

    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05S06MaterialSelection),
        width: '33rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          ...this.paramsZ05S06,
          initDataCodes: this.formData.lstProduct?.map((x) => x.product_cd)
        }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected;
      this.formData.lstProduct = data;

      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        categoryCode: e.category.definition_value,
        classificationCode: e.classifications.product_group_cd,
        initDataCodes: data?.map((x) => x.product_cd)
      };
    },

    handleRemoveProduct(product) {
      this.formData.lstProduct = this.formData.lstProduct.filter((x) => x.product_cd !== product.product_cd);
    },

    // D01-S02: detail document
    redirectToD01S02(item) {
      this.setScrollTop();
      let document_id = item.document_id;
      this.$router.push({ name: 'D01_S02_MaterialDetails', params: { document_id } });
    },

    // D01-S07: document viewer
    openModalMaterialViewerD01S07(document) {
      this.setDocumentViewer(document);
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材ビューワ',
        isShowModal: true,
        component: markRaw(D01_S07_MaterialViewer),
        width: '60rem',
        customClass: 'custom-dialog',
        props: { documentId: document.document_id }
      };
    },

    setDocumentViewer(item) {
      if (!this.checkDocumentViewer(item)) {
        this.lstDocumentViewer.push(item);
      }
    },

    checkDocumentViewer(item) {
      return this.lstDocumentViewer.some((x) => x.document_id === item.document_id);
    },

    // MH D01-S05: create document
    redirectToCustomMaterialCreationD01S05() {
      this.$router.push({ name: 'D01_S05_CustomMaterialCreation', params: { component: 'D01_S04_CustomMaterialManagement', copy_id: 'create' } });
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S01') {
          this.onResultModalZ05S01(e);
        }
        if (e.screen === 'Z05_S06') {
          this.onResultModalZ05S06(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.wrapBtn {
  .dropdown-management {
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
    .form-management {
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        > li {
          padding-left: 10px;
          margin-top: 10px;
          width: 50%;
        }
      }
      .form-management-label {
        margin-bottom: 6px;
        color: #5f6b73;
      }
      .form-management-control {
        .form-management-select {
          display: flex;
          flex-wrap: wrap;
          margin-left: 1px;
          li {
            width: 33.333333%;
            margin-left: -1px;
            margin-bottom: 10px;
            cursor: pointer;
            border-radius: 0;
            padding: 0;
            height: 40px;
            text-align: center;
            vertical-align: middle;
            padding: 0.375rem 0.75rem;

            line-height: 1.5;
            color: #5f6b73;
            font-size: 0.875rem;
            border: 1px solid #727f88;

            &:first-of-type,
            &:nth-child(3n + 1) {
              border-radius: 4px 0 0 4px;
            }
            &:last-child,
            &:nth-child(3n + 0) {
              border-radius: 0 4px 4px 0;
            }
            &.active {
              border: 2px solid #448add;
              color: #448add;
              background: #eef6ff;
              font-weight: 700;
            }
            &:hover:not(.active) {
              background: #eef6ff;
              font-weight: 700;
              color: #5f6b73;
            }
          }
        }
      }
    }
    .btn-management {
      margin-top: 20px;
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
.list-management {
  padding: 10px 24px;
  height: 100%;
  > ul {
    > li {
      background: #fff;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 4px;
      padding: 14px;
      margin-bottom: 10px;
      &.active {
        background: #f2f2f2;
      }
      .listTitle {
        .link {
          display: inline-flex;
          font-size: 1.375rem;
          font-weight: 500;
          color: #448add;
          cursor: pointer;
          align-items: flex-end;

          .listTitle-item {
            min-width: 22px;
            margin-right: 10px;
            align-self: center;

            img {
              width: 22px;
            }
          }
        }
      }
    }
  }
  .listContent {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    margin-top: 5px;
    ul {
      display: flex;
      flex-wrap: wrap;
      padding-right: 10px;
      width: calc(100% - 220px);
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-right: 0;
      }
      li {
        margin: 5px 20px 0 0;
        &:last-child {
          margin-right: 10px;
        }
        .listContent-tlt {
          color: #99a5ae;
        }

        min-width: 80px;
      }
    }
    .listContent-btn {
      text-align: right;
      flex: 0 0 200px;
      margin-top: 5px;

      .btn {
        padding: 0 10px;
        margin-right: 6px;
        height: 32px;
        &:last-child {
          margin-right: 0;
        }
        .btn-iconLeft {
          margin-right: 2px;
          .svg {
            width: 12px;
          }
        }
      }
    }
  }
}

.dropdown-block {
  box-shadow: 0px 5px 8px 1px #b7c3cb80 !important;
  right: 0;
  position: absolute;
  z-index: 3;
  background: #f9f9f9;
  border-radius: 10px 0 10px 10px;
  width: 400px;
  padding: 20px;
  border: none;
  transform: inherit !important;
  left: inherit !important;
  margin: 0;
  min-height: 100px;
  font-size: 14px;
  min-width: 10rem;
  will-change: transform;
  top: 0px;
}

.btn-filter-none-shadow {
  box-shadow: unset !important;
}
.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}
.pagination {
  box-shadow: 0px -3px 9px #0000001a;
}
.outsite-valid {
  width: 125px;
  padding: 0 5px;
  line-height: 35px;
  color: red;
  margin-left: 17px;
  font-size: 16px;
  display: inline-block;
}

.notEdit-valid {
  margin-left: 17px;
  width: 80px;
  border-radius: 2px;
  padding: 5px 10px;
  line-height: 20px;
  height: 30px;
  background: rgb(225, 225, 225);
  color: rgb(112, 114, 116);
  display: inline-block;
  text-align: center;
}
</style>
