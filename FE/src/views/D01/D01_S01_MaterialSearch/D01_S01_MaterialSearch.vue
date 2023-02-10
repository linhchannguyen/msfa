<template>
  <!-- 品目選択 -->
  <!-- add class 'custom-dialog-pd-none' to modal -->

  <div class="wrapContent">
    <div v-loading="loadingPage" class="groupContent groupContent--management">
      <div v-load-f5="true" class="wrapBtn">
        <div v-show="showRegistration()" class="btnInfo">
          <button v-if="device !== 'Tablet'" type="button" class="btn btn-sign msfa-custom-btn-create" @click="onclickShowD01S03()">
            <span class="btn-iconLeft"> <i class="el-icon-plus"></i> <span>新規登録</span></span>
          </button>
        </div>
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button
              class="btn btn-filter"
              type="button"
              :class="isShowPopupFilter ? 'btn-filter-none-shadow' : ''"
              :disabled="params.initMaterials?.length > 0"
              @click="openModalFilter()"
            >
              <img v-svg-inline svg-inline class="svg svg-filter" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter">
                <button type="button" class="btn btn-filter btn-filter-up btn-filter-none-shadow btn-close-filter" @click="onCloseFilter">
                  <img class="svg" src="@/assets/template/images/icon_filter-white.svg" alt="" />
                </button>
              </div>
              <h2 class="titleSearch">検索条件</h2>
              <div class="groupSearch block-search">
                <el-form label-position="left">
                  <label class="groupSearch-label">資材名</label>
                  <el-form-item :class="validationDocumentName() ? 'hasErr' : ''">
                    <el-input v-model="formData.document_name" clearable placeholder="対象疾患を入力"></el-input>
                    <span v-if="validationDocumentName()" class="invalid">
                      {{ getMessage('MSFA0012', '資材名', 50) }}
                    </span>
                  </el-form-item>

                  <label style="color: #5f6b73" class="form-management-label">品目</label>
                  <div class="form-management-control">
                    <div class="form-icon icon-right">
                      <span title_log="品目" class="log-icon icon icon--cursor" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                        <img src="@/assets/template/images/icon_popup.svg" alt="" />
                      </span>
                      <div v-if="lstProduct.length > 0" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag
                            v-for="product in lstProduct"
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
                  <label class="groupSearch-label">対象疾患</label>
                  <el-form-item>
                    <el-input v-model="formData.disease" clearable placeholder="対象疾患を入力"></el-input>
                  </el-form-item>
                  <div class="row">
                    <div class="col-6 pr-1">
                      <label class="groupSearch-label"> 診療科目分類</label>
                      <el-form-item>
                        <el-select v-model="formData.medical_subjects_group_cd" class="form-control-select selectpicker select-control" placeholder="未選択">
                          <el-option value=""> 未選択</el-option>
                          <el-option
                            v-for="item in lstmedicalSubjectGroups"
                            :key="item.medical_subjects_group_cd"
                            :label="item.medical_subjects_group_name"
                            :value="item.medical_subjects_group_cd"
                          >
                            {{ item.medical_subjects_group_name }}
                          </el-option>
                        </el-select>
                      </el-form-item>
                    </div>
                    <div class="col-6 pl-1">
                      <label class="groupSearch-label">適用対象日</label>
                      <el-form-item>
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="formData.date"
                            type="date"
                            :editable="false"
                            placeholder="日付選択"
                            format="YYYY/M/D"
                            value-format="YYYY/M/D"
                            style="width: unset"
                          >
                          </el-date-picker>
                        </div>
                      </el-form-item>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-6 pr-1">
                      <label class="groupSearch-label">安全性情報</label>
                      <el-form-item>
                        <div class="block-search_check">
                          <div
                            :class="formData.safety_information_flag === '' ? 'active' : ''"
                            class="check-item"
                            @click="formData.safety_information_flag = ''"
                            @touchstart="formData.safety_information_flag = ''"
                          >
                            <span>全て</span>
                          </div>
                          <div
                            :class="formData.safety_information_flag === '1' ? 'active' : ''"
                            class="check-item"
                            @click="formData.safety_information_flag = '1'"
                            @touchstart="formData.safety_information_flag = '1'"
                          >
                            <span>あり</span>
                          </div>
                          <div
                            :class="formData.safety_information_flag === '0' ? 'active' : ''"
                            class="check-item"
                            @click="formData.safety_information_flag = '0'"
                            @touchstart="formData.safety_information_flag = '0'"
                          >
                            <span>なし</span>
                          </div>
                        </div>
                      </el-form-item>
                    </div>
                    <div class="col-6 pl-1">
                      <label class="groupSearch-label">適応外情報</label>
                      <el-form-item>
                        <div class="block-search_check">
                          <div
                            :class="formData.off_label_information_flag === '' ? 'active' : ''"
                            class="check-item"
                            @click="formData.off_label_information_flag = ''"
                            @touchstart="formData.off_label_information_flag = ''"
                          >
                            <span>全て</span>
                          </div>
                          <div
                            :class="formData.off_label_information_flag === '1' ? 'active' : ''"
                            class="check-item"
                            @click="formData.off_label_information_flag = '1'"
                            @touchstart="formData.off_label_information_flag = '1'"
                          >
                            <span>あり</span>
                          </div>
                          <div
                            :class="formData.off_label_information_flag === '0' ? 'active' : ''"
                            class="check-item"
                            @click="formData.off_label_information_flag = '0'"
                            @touchstart="formData.off_label_information_flag = '0'"
                          >
                            <span>なし</span>
                          </div>
                        </div>
                      </el-form-item>
                    </div>
                  </div>
                </el-form>
              </div>

              <div class="groupSearch-btn">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCloseFilter">キャンセル</button>
                <button
                  type="button"
                  class="btn btn-primary"
                  :disabled="validationDocumentName()"
                  @click="
                    pagination.curentPage = 1;
                    search(false);
                  "
                >
                  検索
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Single -->
      <div ref="scrollbarDoc" class="list-management p-20 scrollbar">
        <div v-if="lstMasterial.length > 0" class="block-masterial">
          <div v-for="data in lstMasterial" :key="data.document_id" class="block-masterial_item">
            <div class="group-icon">
              <div v-show="data.new_icon == 1" class="list-orange">新着</div>
              <div v-show="data.update_icon == 1" class="list-green">更新</div>
            </div>
            <el-card class="mb-2" :class="data.selected ? 'material-item-card-active' : 'material-item-card'">
              <div class="block-body">
                <div class="block-left">
                  <div class="card-item_header">
                    <h2>
                      <span>
                        <img v-if="data.file_type == '10'" class="svg" src="@/assets/template/images/icon_pdf-manag.svg" alt="icon" />
                        <img v-else class="svg" src="@/assets/template/images/icon_film.svg" alt="icon" />
                      </span>

                      <a class="log-icon" title_log="資材名" @click="redirectToD01S02(data)"> {{ data.document_name }}</a>
                    </h2>
                    <div class="mt-2">
                      <span class="text-info-content"> 適用期間：</span>
                      <span class="list-label">{{ dataDateString(data) }}</span>
                    </div>
                  </div>
                  <div class="card-item_body">
                    <ul class="card-item_content"></ul>
                    <ul class="card-item_content">
                      <li>
                        <span>品目：</span>
                        <span class="list-label">{{ data.product_name }}</span>
                      </li>
                      <li>
                        <span>対象疾患：</span>
                        <span class="list-label">{{ data.disease }}</span>
                      </li>

                      <li>
                        <span> 診療科目分類: </span>
                        <span class="list-label">{{ data.medical_subjects_group_name }}</span>
                      </li>
                      <li>
                        <span>安全性情報：</span>
                        <span class="list-label">
                          {{ data.safety_information_flag === '' ? '全て' : data.safety_information_flag === 1 ? 'あり' : 'なし' }}
                        </span>
                      </li>
                      <li>
                        <span>適応外情報：</span>
                        <span class="list-label">
                          {{ data.off_label_information_label }}
                        </span>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="block-right">
                  <div class="card-item_body">
                    <ul class="card-item_content">
                      <el-rate v-model="data.point" allow-half disabled disabled-void-color="#dcdcdc" />
                      <span>
                        <img v-svg-inline svg-inline class="svg" :src="require(`@/assets/images/icon-comment.svg`)" alt="" />
                        {{ data.comment }}
                      </span>
                    </ul>
                  </div>
                </div>
              </div>
            </el-card>
          </div>
        </div>
        <EmptyData v-else-if="!isLoadDefault" />
      </div>
    </div>
    <!-- pagination left single -->
    <div v-if="lstMasterial.length > 0" class="pagination">
      <div class="pagination-cases">全{{ pagination.totalItems || 'O' }}件</div>
      <PaginationTable
        :page-size="pagination.pageSize"
        layout="prev, pager, next"
        :current-page="pagination.curentPage"
        :total="pagination.totalItems"
        @current-change="handleCurrentChange"
      />
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog custom-dialog-pd modal-fixed modal-fixed-min"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :top="'10vh'"
      :show-close="true"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
/* eslint-disable indent */
import { markRaw } from 'vue';
import PaginationTable from '../../../components/PaginationTable.vue';
import D01_S01_ListOfMaterialsService from '@/api/D01/D01_S01_ListOfMaterialsService';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import D01_S03_Registration from '@/views/D01/D01_S03_Registration/D01_S03_Registration';
import moment from 'moment';
import filter_popup from '@/utils/mixin_filter_popup';
import { Device } from '@/utils/common-function.js';

export default {
  name: 'D01_S01_MaterialSearch',
  components: { PaginationTable, Z05S06MaterialSelection, D01_S03_Registration },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean],

    params: {
      type: Object,
      required: true,
      default() {
        return {
          // (require)
          subMaterialSelectableFlag: 1, // 1: allow select child material || 0: not allow  (require)
          customMaterialFlag: 0, // 0: allow  || 1: not allow choice カスタム資材  (require)

          initMaterials: [], // if was => only allow array initMaterials
          materialType: '10', // '10': 原本資材,  '20': カスタム資材
          document_name: '',
          lstProduct: [],
          medicalSubjectsGroupCode: '',
          safetyInformationFlag: '', // '': 全て, 1: あり, 0: なし
          offLabelInformationFlag: '', // '': 全て, 1: あり, 0: なし
          date: moment().format('YYYY/M/D'),
          device: ''
        };
      }
    }
  },

  emits: { onFinishScreen: null },

  data() {
    return {
      loadingPage: false,
      isLoadDefault: true,
      // isShowPopupFilter: false,
      formData: {
        definition_value:
          this.params.initMaterials?.length > 0
            ? '10'
            : this.params.customMaterialFlag == 1
            ? '10'
            : this.params.materialType
            ? this.params.materialType
            : '10',
        document_name: this.params.initMaterials?.length > 0 ? '' : this.params.document_name,
        medical_subjects_group_cd: this.params.initMaterials?.length > 0 ? '' : this.params.medical_subjects_group_cd,
        safety_information_flag: this.params.initMaterials?.length > 0 ? '' : this.params.safetyInformationFlag ? this.params.safetyInformationFlag : '',
        off_label_information_flag: this.params.initMaterials?.length > 0 ? '' : this.params.offLabelInformationFlag ? this.params.offLabelInformationFlag : '',
        date: this.params.initMaterials?.length > 0 ? moment().format('YYYY/M/D') : this.params.date,
        disease: this.params.initMaterials?.length > 0 ? '' : this.params.disease,
        product_cd: this.lstProduct || ''
      },
      lstProduct: [],
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalD01S02: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      lstmedicalSubjectGroups: [],

      lstMasterial: [],
      lstMasterialSelected: [],

      propDataZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },
      paramFilterOld: {},
      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },
      reload: true,

      onScrollTop: 0
    };
  },

  computed: {},

  mounted() {
    this.emitter.emit('change-header', {
      title: '資材検索',
      icon: 'Material-icon.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.D01_S01_MaterialSearch;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.D01_S01_MaterialSearch;
    this.getDataScreen();
    this.getDevice();
  },
  methods: {
    getDevice() {
      this.device = Device();
    },
    dataDateString(data) {
      const startDate = data.start_date?.replaceAll('-', '/');
      const endDate = data.end_date?.replaceAll('-', '/');
      const endDateCustom =
        new Date(endDate).getFullYear() == '9999' ? `${this.JapaneseTilde()}` : `${this.JapaneseTilde()} ${moment(endDate).format('YYYY/M/D')}`;
      const result = moment(startDate).format('YYYY/M/D') + endDateCustom;
      return result;
    },
    showRegistration() {
      const isShow = this.$store.state.auth.policyRole?.includes('R40');
      return isShow ? true : false;
    },
    showFlagCreate(item) {
      let isFlag = true;
      const create_datetime_new = 14;
      const dayCurrent = new Date();
      const createTime = item.create_datetime ? new Date(item.create_datetime) : null;
      if (createTime) {
        createTime.setDate(createTime.getDate() + create_datetime_new);
        if (dayCurrent.getTime() > createTime.getTime()) {
          isFlag = false;
        }
      }
      return isFlag;
    },
    showFlagUpdate(item) {
      //
      let isFlag = true;
      const updateTimeNumber = 7; // 7;
      const updateTime = item.last_update_datetime ? new Date(item.last_update_datetime) : null; // 01;
      const currentDate = new Date().getTime();
      if (updateTime) {
        updateTime.setDate(updateTime.getDate() + updateTimeNumber);
        if (currentDate > updateTime.getTime()) {
          isFlag = false;
        }
      }
      return isFlag;
    },
    search(isLoadDefault) {
      this.getDataByFilter(true, isLoadDefault);

      this.isShowPopupFilter = false;
    },
    // get data initial
    getDataScreen() {
      this.loadingPage = true;
      D01_S01_ListOfMaterialsService.getDataScreen()
        .then((res) => {
          this.lstmedicalSubjectGroups = res?.data?.data?.medical_subjects_group;
          this.getDataByFilter(false, true);
        })
        .catch((err) => {
          this.$notify({ message: err?.response?.data?.message, customClass: 'error' });
          this.loadingPage = false;
        });
    },

    // Load data by filter
    getDataByFilter(isFilter, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      let params = {
        ...this.formData,
        offset: this.pagination.curentPage - 1,
        limit: this.pagination.pageSize,
        product_cd: this.lstProduct.length > 0 ? this.lstProduct[0].product_cd : ''
      };
      this.paramFilterOld = params;
      this.loadingPage = true;

      D01_S01_ListOfMaterialsService.getData(params)
        .then((res) => {
          this.isShowPopupFilter = false;

          const totalItems = res?.data?.data.pagination.total_items;
          const curentPage = res?.data?.data.pagination.current_page;

          this.pagination = {
            ...this.pagination,
            totalItems,
            curentPage
          };

          let datas = res?.data?.data.records.length > 0 ? res?.data?.data?.records : [];

          this.lstMasterial = datas.map((x) => {
            return {
              ...x,
              point: x.point === '5.0' ? '5' : x.point
            };
          });
        })
        .catch((err) => {
          this.lstMasterial = [];
          if (err?.response?.data?.message) {
            this.$notify({ message: err?.response?.data?.message, customClass: 'error' });
          } else {
            this.$notify({ message: '内部エラーが発生しました。システム管理者に連絡してください。', customClass: 'error' });
          }
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          this.loadingPage = false;
          this.isLoadDefault = false;

          this.setCurrentPageScreen('D01_S01_MaterialSearch', this.pagination.curentPage);

          if (this.$refs.scrollbarDoc) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.scrollbarDoc.scrollTop = this.onScrollTop;
            } else {
              this.$refs.scrollbarDoc.scrollTop = 0;
            }
          }
          this.js_pscroll(0.4);
        });
    },

    handleCurrentChange(val) {
      this.onScrollTop = 0;
      this.pagination.curentPage = val;
      this.search(false);
    },

    setSelectedMaterial(item) {
      this.lstMasterialSelected = [];
      this.lstMasterial.forEach((x) => {
        if (x.document_id === item.document_id) {
          x.selected = !x.selected;
          if (x.selected) {
            this.lstMasterialSelected.push(x);
          }
        } else {
          x.selected = false;
        }
      });
    },

    // modal filter
    openModalFilter() {
      this.isShowPopupFilter = true;
    },

    onCloseFilter() {
      this.isShowPopupFilter = false;
      // if (!this.isEqualObject(this.formData, this.paramFilterOld)) {
      //   this.formData = { ...this.paramFilterOld };
      // }
    },

    // check form
    validationDocumentName() {
      let document_name = this.formData.document_name;
      if (document_name?.length > 50) {
        return true;
      }
      return false;
    },

    handleRemoveProduct(product) {
      this.lstProduct = this.lstProduct.filter((x) => x.product_cd !== product.product_cd);

      this.showModalZ05S06 = false;

      this.propDataZ05S06 = {
        ...this.propDataZ05S06,
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      };
    } /** D01 S03 */,
    onclickShowD01S03() {
      localStorage.removeItem('$_D');
      this.$router.push({ name: 'D01_S03_Registration' });
    },
    redirectToD01S02(item) {
      let scrollTop = this.$refs.scrollbarDoc ? this.$refs.scrollbarDoc.scrollTop : 0;
      this.setScrollScreen('D01_S01_MaterialSearch', scrollTop);
      let document_id = item.document_id;
      this.$router.push({ name: 'D01_S02_MaterialDetails', params: { document_id } });
    },
    // open Modal Z05 - S06
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05S06MaterialSelection),
        width: '33rem',
        props: { ...this.propDataZ05S06 }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected?.length > 0 ? e?.dataSelected : [];
      // this.formData.product_cd = data?.product_cd;
      // this.formData.product_name = data?.product_name;
      this.lstProduct = data;
      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        categoryCode: e.category.definition_value,
        classificationCode: e.classifications.product_group_cd,
        initDataCodes: data?.map((x) => x.product_cd)
      };

      this.onCloseModal();
    },
    onResultModalD01S03() {
      this.search(false);
      this.onCloseModal();
    },
    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S06') {
          this.onResultModalZ05S06(e);
        }
        if (e.screen === 'D03_S03') {
          this.onResultModalD01S03(e);
        }
      } else {
        this.onCloseModal();
      }
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    // mode multi
    // Check data
    checkDataSelected(item) {
      let index = this.lstMasterialSelected.findIndex((x) => x.document_id === item.document_id);

      return index > -1 ? true : false;
    },

    onAddItem(item) {
      this.lstMasterialSelected.push(item);
    },

    onRemoveItem(item) {
      let index = this.lstMasterialSelected.findIndex((x) => x.document_id === item.document_id);
      this.lstMasterialSelected.splice(index, 1);
    },

    // compare object
    isEqualObject(object1, object2) {
      const keys1 = Object.keys(object1);
      const keys2 = Object.keys(object2);
      if (keys1.length !== keys2.length) {
        return false;
      }
      for (let key of keys1) {
        if (object1[key] !== object2[key]) {
          return false;
        }
      }
      return true;
    },

    // check disable button 選択
    checkDisabledSubmit() {
      return this.lstMasterialSelected.length > 0 ? false : true;
    },

    // return Data
    returnData(data) {
      if (data) {
        this.lstMasterialSelected = [];
        this.lstMasterialSelected.push(data);
      }
      let result = {
        screen: 'Z05_S05',
        dataMasterialSelected: this.lstMasterialSelected
      };

      this.$emit('onFinishScreen', result);
    },

    closeModal() {
      this.$emit('onFinishScreen');
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 1081px;
$viewport_tablet_mini: 1024px;

@mixin flexbox($align: center, $wrap: wrap, $justify: space-between) {
  display: flex;
  align-items: $align;
  flex-wrap: $wrap;
  justify-content: $justify;
}
$text-grey-1: #99a5ae;
$text-grey-2: #5f6b73;

.modal .modal-body {
  padding: 0;
}
.list-management {
  height: 100%;
}

.block-Filter {
  display: flex;
  height: 50px;
  padding: 0px 32px;
  padding-bottom: 8px;
  justify-content: space-between;
  align-items: center;
}

.el-card {
  border-radius: 8px;
}

.card-border-top {
  border-radius: 8px 8px 0 0;
}

.card-border-bottom {
  border-radius: 0 0 8px 8px;
}

.dropdown-menu {
  box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
  right: 0;
}

.block-search {
  .block-search_check {
    @include flexbox(center, nowrap, flex-start);
    .check-item {
      cursor: pointer;
      background: #ffffff;
      border: 1px solid #727f88;
      box-sizing: border-box;
      text-align: center;
      border-right: none;
      flex-grow: 1;
      height: 40px;

      &:first-child {
        border-radius: 4px 0px 0px 4px;
        height: 40px;
      }

      &:last-child {
        border-radius: 0px 4px 4px 0px;
        border: 1px solid #727f88;
        height: 40px;
      }
    }

    .check-item.active {
      border: 1px solid #448add;
      background: #eef6ff;
      span {
        color: #448add;
        font-weight: bold;
        font-size: 14px;
      }
    }

    @for $i from 1 through 12 {
      .check-item {
        width: calc(1 / $i);
      }
    }
  }
}

.el-form-item {
  margin-bottom: 10px;

  &label {
    padding: 0 5px 0 0 !important;
  }
}

.block-masterial-multi {
  border-top-right-radius: 10px;
}
@media (max-width: $viewport_tablet) {
  .wrapBtn {
    padding-left: 24px;
    padding-bottom: 15px;
    padding-right: 4.5%;
  }
}
.block-masterial {
  background: #f2f2f2;
  @media (max-width: $viewport_tablet) {
    .p-20 {
      padding: 0px !important;
    }

    .block-masterial_item {
      min-width: 700px !important;
      width: 700px;
    }
    .group-icon {
      left: 25px;
    }
  }
  @media (max-width: $viewport_tablet_mini) {
    height: auto;
    .group-icon {
      left: 25px !important;
    }
    .el-card {
      .el-card__body > .block-body {
        margin-left: 5px;
        .block-left {
          .tooltip {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
          }

          .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;

            /* Position the tooltip */
            position: absolute;
            z-index: 1;
          }

          .tooltip:hover .tooltiptext {
            visibility: visible;
          }
          .card-item_header a {
            white-space: nowrap;
            width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
          }
        }
      }
    }

    .block-masterial_item {
      padding: 0px 20px 5px 10px;
    }
    .p-20 {
      padding: 0px !important;
    }
  }
  .list-hot {
    background: url(~@/assets/template/images/icon_hot-tag.png) no-repeat;
    width: 40px;
    height: 32px;
    display: block;
    position: absolute;
    top: -2px;
    left: 15px;
    font-size: 12px;
    color: #fcfcfc;
    font-weight: 700;
    padding: 4px 0 0 4px;
  }
  .group-icon {
    display: flex;
    position: absolute;
    left: 15px;
    .list-green {
      background: url(~@/assets/template/images/icon-green-tag.png) no-repeat;
      width: 40px;
      height: 32px;
      font-size: 12px;
      color: #fcfcfc;
      font-weight: 700;
      padding: 4px 0 0 4px;
    }
    .list-orange {
      background: url(~@/assets/template/images/icon-orange-tag.png) no-repeat;
      width: 40px;
      height: 32px;
      font-size: 12px;
      color: #fcfcfc;
      font-weight: 700;
      padding: 4px 0 0 4px;
    }
  }

  .block-body {
    position: relative;
    margin-left: 20px;
    @include flexbox(stretch, nowrap);

    .block-left {
      margin-top: 7px;
      flex-grow: 1;
    }

    .block-right {
      position: absolute;
      right: 0px;
      bottom: 0px;
      text-align: right;
      width: 235px;

      .card-item_content {
        justify-content: flex-end;
        margin-top: 12px;
      }
    }

    @media (max-width: $viewport_tablet) {
      .block-left {
        .card-item_content {
          width: 75%;
        }
      }
      .block-right {
        bottom: -7px;
      }
      @media (max-width: 850px) {
        .block-right {
          bottom: 20px;
        }
      }
    }
  }

  .material-item-card-active {
    background: #eef6ff !important;
    background-color: #eef6ff !important;

    border-left: 8px solid #448add;
  }

  .material-item-card {
    margin-left: 8px;
  }

  .block-masterial_item {
    width: 100%;
    position: relative;

    .card-item_header {
      margin: 0 0 10px 0;
      color: #5f6b73;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      h2 {
        align-items: flex-end;
        font-weight: bold;
        font-size: 22px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        a {
          color: #448add;
          cursor: pointer;
        }
      }
      a {
        margin-left: 9px;
      }
      .mt-2 {
        flex: 0 0 377px;
        margin-top: 0px !important;
        margin-left: auto;
        text-align: end;
        .text-info-content {
          color: #5f6b73;
          font-size: 14px;
        }
        .list-label {
          color: #2d3033;
          font-size: 14px;
        }

        .comment {
          font-size: 14px;
          text-align: right;
          margin-right: 0;
        }
      }
    }

    .card-item_body {
      font-size: 14px;
    }

    .card-item_content {
      @include flexbox(none, wrap, unset);
      color: #5f6b73;
      li {
        margin-top: 0.25rem;
        margin-right: 10px;
        width: 200px;
      }
    }

    .list-label {
      color: #2d3033;
    }

    .comment {
      font-size: 14px;
      text-align: right;
      margin-right: 0;
    }
  }
}

// list
.block {
  border-radius: 0;
  display: flex;
  flex-direction: column;
  li {
    @include flexbox(center, nowrap);
    list-style: none;
    padding: 0;
    margin: 0px;
    cursor: pointer;
    color: #5f6b73;
  }

  .block-title {
    border-radius: 0;
    border-bottom: none;
    font-weight: bold;
    font-size: 16px;
    padding: 12px 18px !important;
    border: none;
    background: #f9f9f9;
    height: 46px;
  }
}

.block-item {
  .left {
    @include flexbox(flex-start, wrap);
    flex-direction: column;
    flex-grow: 1;
    padding: 10px 5px 10px 18px;
    .text {
      margin-bottom: 7px;
      align-items: center;
      letter-spacing: 0.05em;
      color: #5f6b73;
      flex-grow: 1;
    }
  }

  .right {
    padding: 10px 18px 10px 5px;
    flex-grow: unset;
  }
}

.list-group {
  height: 555px;
  overflow-y: scroll;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 8px 0 0 8px;
  background: #fff;
}

.list-group-item {
  border: none;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
  &:first-child {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
  }

  .item-file {
    @include flexbox(flex-start !important, nowrap);

    img {
      width: 15px;
      margin-right: 7px;
    }
  }
}

// btn
.block-btn {
  padding: 28px 32px;
  text-align: center;
  border-radius: 0 0 8px 0;
  .btn {
    width: 180px;
    margin-right: 14px;
  }
}

.el-button {
  min-height: 30px;
}

.btn-add {
  justify-content: center;
  align-items: center;
  padding: 0px 12px;
  height: 32px;
  background: #ffffff;
  color: #448add;
  border: 1px solid #448add;
  box-sizing: border-box;
  border-radius: 24px;
  font-size: 15px;
  letter-spacing: 0.03em;
  font-weight: bold;
  &.is-disabled {
    color: var(--el-button-disabled-font-color);
    border-color: var(--el-button-disabled-border-color);
  }

  i {
    span {
      margin-left: 5px;
    }
  }
}

.btn-check {
  background: #448add;
  color: #ffffff;
}

.btn-circle {
  padding: 0;
  background: inherit;
  border-radius: 100%;
  color: #99a5ae;
  box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 8px 0;
  i {
    font-weight: bold;
    font-size: 15px;
    margin-top: 2px;
  }
  .el-icon-close:before {
    color: unset;
  }
}

.pagination-custom {
  box-shadow: none;
  padding: 0;
  width: unset;
  height: unset;
  position: initial;
  left: 60px;
  display: flex;
  align-items: center;
  z-index: unset;
}

.pagination-custom-bg {
  background: #ffffff;
  border-radius: 0 8px 8px 0;

  .pagination-number li {
    border: 1px solid #f2f2f2;
    border-left-width: 0;

    &:first-of-type a {
      border-radius: 4px 0 0 4px;
      border-left: 1px solid #f2f2f2;
    }

    &:last-child a {
      border-radius: 0 4px 4px 0;
      border-right: 1px solid #f2f2f2;
    }
  }
}

.block-pagination {
  display: flex;
  padding: 16px 32px;
  box-shadow: inset 3px 2px 6px #e3e3e3;
  border-radius: 0 0 10px 10px;
  justify-content: right;
  align-items: center;
  .block-btn {
    background: transparent;
    padding: 0;
    margin-right: 1rem;
    .btn {
      width: 130px;
    }
  }
}

.block-pagination-multi {
  background: #f9f9f9;
  box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
  border-radius: 0px 0px 8px 0px;
  justify-content: flex-end;
  height: 54px;
}

.form-control[readonly] {
  background-color: transparent;
}

//tag
.el-tag {
  border: none;
  color: #606266;
  height: 30px;
  line-height: 26px;
  font-size: 14px;
  .el-icon-close {
    top: 20%;
    float: right;
  }
}

.dropdown-block {
  box-shadow: 0px 5px 8px 1px #b7c3cb80 !important;
  right: 0;
  position: absolute;
  z-index: 1;
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
.btn-filter:disabled {
  cursor: not-allowed;
}

.btn-filter-up:hover {
  background: #448add !important;
}

.noData {
  height: 560px;
}

.h-28r {
  height: 28rem;
}

.h-none {
  height: unset;
  min-height: 100%;
}

.text-info-content {
  color: #5f6b73;
}

.custom-input {
  position: relative;
}
.iconClear {
  cursor: pointer;
  position: absolute;
  right: 35px;
  top: 33%;
  width: 20px;
}

.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}

.modal-body {
  padding: 0;
}
.pagination {
  box-shadow: inset 0px 7px 12px #e3e3e3;
}
.p-20 {
  padding: 20px;
}
@media (max-width: $viewport_tablet) {
  .p-20 {
    padding: 0px !important;
  }
}
@import '@/assets/css/pages/Z05';
</style>
