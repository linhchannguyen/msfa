<template>
  <!-- 資材選択 -->
  <!-- width: <1024: 95% || 1024 < single: 70rem,  multiple: 77rem  -->
  <!-- add class 'custom-dialog-pd-none' to modal -->
  <div class="modal-body modal-body-Z05S05">
    <div id="msfa-notify-Z05S05"></div>
    <div class="block-Filter">
      <div class="wrapContent page-zo">
        <div class="groupContent p-0">
          <div class="wrapBtn" :style="{ right: mode === 'single' ? '' : 'calc(33% + 20px)' }">
            <div class="btnInfo btnInfo--change">
              <div class="btnInfo-btn">
                <button class="btn btn-filter" type="button" :class="showFilter ? 'btn-filter-none-shadow' : ''" @click="openModalFilter">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
                </button>
                <div v-if="showFilter" v-click-outside-event="clickOutside" class="dropdown-block dropdown-filter">
                  <div class="item-filter btn-close-filter">
                    <button type="button" class="btn btn-filter btn-filter-up btn-filter-none-shadow" @click="onCloseFilter">
                      <img class="svg" src="@/assets/template/images/icon_filter-white.svg" alt="" />
                    </button>
                  </div>
                  <h2 class="titleSearch">検索条件</h2>
                  <div class="groupSearch block-search"></div>

                  <div class="form-management">
                    <ul>
                      <li class="w-100">
                        <label class="form-management-label">資材種別</label>
                        <div class="form-management-control">
                          <ul class="form-management-select">
                            <li
                              v-for="item in lstMasterialType"
                              :key="item.document_type"
                              :class="`${formData.document_type == item.document_type ? 'active' : ''} ${
                                customMaterialFlag == 0 && item.document_type == '20' ? 'disabled' : ''
                              }`"
                              @click="formData.document_type = item.document_type"
                            >
                              {{ item.document_label }}
                            </li>
                          </ul>
                        </div>
                      </li>
                      <li class="w-100" :class="validationDocumentName() ? 'hasErr' : ''">
                        <label class="form-management-label">資材名</label>
                        <el-input v-model="formData.document_name" clearable placeholder="資材名を入力"></el-input>
                        <span v-if="validationDocumentName()" class="invalid">
                          {{ getMessage('MSFA0012', '資材名', 50) }}
                        </span>
                      </li>

                      <li class="w-100">
                        <label class="form-management-label">品目</label>
                        <div class="form-management-control">
                          <div class="form-icon icon-right">
                            <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()">
                              <img class="svg" src="@/assets/template/images/icon_popup.svg" />
                            </span>
                            <div v-if="formData.product_name" class="form-control d-flex align-items-center">
                              <div class="block-tags">
                                <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveProduct()">
                                  {{ formData.product_name }}
                                </el-tag>
                              </div>
                            </div>
                            <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                          </div>
                        </div>
                      </li>
                      <li>
                        <label class="form-management-label">診療科目分類</label>
                        <div class="form-management-control">
                          <el-select v-model="formData.medical_subjects_group_cd" class="form-control-select selectpicker select-control" placeholder="未選択">
                            <el-option :value="''">未選択</el-option>
                            <el-option
                              v-for="item in lstmedicalSubjectGroups"
                              :key="item.medical_subjects_group_cd"
                              :label="item.medical_subjects_group_name"
                              :value="item.medical_subjects_group_cd"
                            >
                              {{ item.medical_subjects_group_name }}
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
                        </div>
                      </li>
                      <li>
                        <label class="form-management-label">安全性情報</label>
                        <div class="form-management-control">
                          <ul class="form-management-select">
                            <li
                              v-for="item in lstSafetyInfomation"
                              :key="item.safety_information_flag"
                              :class="`${formData.safety_information_flag == item.safety_information_flag ? 'active' : ''}`"
                              @click="formData.safety_information_flag = item.safety_information_flag"
                            >
                              {{ item.safety_information_label }}
                            </li>
                          </ul>
                        </div>
                      </li>
                      <li>
                        <label class="form-management-label">適応外情報</label>
                        <div class="form-management-control">
                          <ul class="form-management-select">
                            <li
                              v-for="item in lstOffLabelInfomation"
                              :key="item.off_label_information_flag"
                              :class="`${formData.off_label_information_flag == item.off_label_information_flag ? 'active' : ''}`"
                              @click="formData.off_label_information_flag = item.off_label_information_flag"
                            >
                              {{ item.off_label_information_label }}
                            </li>
                          </ul>
                        </div>
                      </li>
                    </ul>
                  </div>

                  <div class="groupSearch-btn">
                    <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCloseFilter">キャンセル</button>
                    <button type="button" class="btn btn-primary" :disabled="validationDocumentName()" @click="getDataByFilter(false)">検索</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Single -->
    <div v-if="mode === 'single'" class="row m-0">
      <div class="col-md-12 p-0">
        <div :class="`${loadingPage ? 'pre-loader h-510 single' : ''}`">
          <Preloader v-if="loadingPage" />
          <div v-else-if="lstMasterial.length > 0 || loadingPage" class="block-masterial single">
            <div class="contentDoc scrollbar">
              <div
                v-for="data in getDataDisplay"
                :key="data.document_id"
                :class="['block-masterial_item item--cusor', checkMaterial(data) ? '' : 'card-gray']"
                @click="checkMaterial(data) ? setSelectedMaterial(data.document_id, $event) : ''"
              >
                <div :class="['mb-2 card-z05S05', data.selected ? 'material-item-card-active' : 'material-item-card']">
                  <div class="block-header">
                    <div class="card-item_header">
                      <span class="item_icon">
                        <img
                          v-if="data.document_type == '10' && data.file_type == '10'"
                          v-svg-inline
                          svg-inline
                          class="svg"
                          :src="require('@/assets/template/images/icon_pdf_manag_1.svg')"
                          alt=""
                        />
                        <img
                          v-if="data.document_type == '10' && data.file_type == '20'"
                          v-svg-inline
                          svg-inline
                          class="svg"
                          :src="require('@/assets/template/images/icon_film.svg')"
                          alt=""
                        />
                        <img
                          v-if="data.document_type == '20'"
                          v-svg-inline
                          svg-inline
                          class="svg"
                          :src="require('@/assets/images/icon_document_spanner.svg')"
                          alt=""
                        />
                      </span>
                      <span class="title">{{ data.document_name }}</span>
                      <button title_log="資材名" type="button" class="btn btn--icon log-icon" @click="openModalMaterialViewerD01S07(data)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slideshow02.svg')" alt="" />
                      </button>
                    </div>
                    <el-button class="btn-add btn btn-outline-primary btn-outline-primary--cancel" :class="data.selected ? 'btn-check' : ''">
                      <img v-if="!data.selected" style="padding-bottom: 3px" class="svg" src="@/assets/template/images/icon-notcheck-z05-s05.svg" alt="icon" />
                      <img v-if="data.selected" style="padding-bottom: 3px" class="svg" src="@/assets/template/images/icon-check-z05-s05.svg" alt="icon" />
                      <span style="padding: 0 0 2px 4px">選択</span>
                    </el-button>
                  </div>
                  <div class="block-body">
                    <div class="block-left">
                      <div class="card-item_body">
                        <ul class="card-item_content">
                          <li>
                            <span>バージョン：</span>
                            <span class="list-label">{{ data.document_version }}</span>
                          </li>
                          <li>
                            <span>品目：</span>
                            <span class="list-label">{{ data.product_name }}</span>
                          </li>
                          <li>
                            <span>対象疾患：</span>
                            <span class="list-label">{{ data.disease }}</span>
                          </li>
                        </ul>
                        <ul class="card-item_content">
                          <li>
                            <span>安全性情報：</span>
                            <span class="list-label">
                              {{ data.safety_information_flag === '' ? '全て' : data.safety_information_flag === 1 ? 'あり' : 'なし' }}
                            </span>
                          </li>
                          <li>
                            <span>適応外情報：</span>
                            <span class="list-label">
                              {{ data.off_label_information_flag === '' ? '全て' : data.off_label_information_flag === 1 ? 'あり' : 'なし' }}
                            </span>
                          </li>
                          <li>
                            <span>診療科目分類：</span>
                            <span class="list-label">{{ data.medical_subjects_group_name }}</span>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="block-right">
                      <div class="mt-2">
                        <span class="text-info-content"> 適用期間：</span>
                        <span class="list-label">
                          {{ formatFullDate(data.start_date) }} ～
                          {{ formatFullDate(data.end_date) === '9999/12/31' ? '' : formatFullDate(data.end_date) }}</span
                        >
                      </div>
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
                </div>
              </div>
            </div>
          </div>
          <div v-if="lstMasterial.length === 0 && !loadingPage" class="noData h-custom">
            <div class="noData-content">
              <p class="noData-text">該当するデータがありません。</p>
              <div class="noData-thumb">
                <img src="@/assets/template/images/data/amico.svg" alt="" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- pagination left single -->
    <div v-if="mode === 'single'" class="block-pagination">
      <div class="block-btn">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
        <button type="button" class="btn btn-primary" :disabled="checkDisabledSubmit()" @click="returnData(data)">決定</button>
      </div>
      <div v-if="lstMasterial.length > 0" class="pagination pagination-custom">
        <div class="pagination-cases">全 {{ lstMasterial.length }} 件</div>
        <PaginationTable :page-size="pageSize" :current-page="curentPage" :total="lstMasterial.length" @current-change="handleCurrentChange" />
      </div>
    </div>

    <!-- multiple -->
    <div v-if="mode !== 'single'" class="row m-0">
      <!-- left -->
      <div class="col-md-8 p-0">
        <div v-if="lstMasterial.length > 0 || loadingPage" :class="`block-masterial block-masterial-multi ${loadingPage ? 'pre-loader h-564' : ''}`">
          <Preloader v-if="loadingPage" />
          <div v-else class="contentDoc scrollbar">
            <div v-for="data in getDataDisplay" :key="data.document_id" class="block-masterial_item">
              <div class="mb-2 card-z05S05">
                <div class="block-header">
                  <div class="card-item_header">
                    <span class="item_icon">
                      <img
                        v-if="data.document_type == '10' && data.file_type == '10'"
                        v-svg-inline
                        svg-inline
                        class="svg"
                        :src="require('@/assets/template/images/icon_pdf_manag_1.svg')"
                        alt=""
                      />
                      <img
                        v-if="data.document_type == '10' && data.file_type == '20'"
                        v-svg-inline
                        svg-inline
                        class="svg"
                        :src="require('@/assets/template/images/icon_film.svg')"
                        alt=""
                      />
                      <img
                        v-if="data.document_type == '20'"
                        v-svg-inline
                        svg-inline
                        class="svg"
                        :src="require('@/assets/images/icon_document_spanner.svg')"
                        alt=""
                      />
                    </span>
                    <span>{{ data.document_name }}</span>
                    <button title_log="資材名" type="button" class="btn btn--icon log-icon" @click="openModalMaterialViewerD01S07(data)">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slideshow02.svg')" alt="" />
                    </button>
                  </div>
                  <el-button
                    type="primary"
                    class="btn-add btn-add-w btn btn-outline-primary btn-outline-primary--cancel"
                    :disabled="checkDataSelected(data)"
                    @click="onAddItem(data.document_id)"
                  >
                    <i class="el-icon-plus">
                      <span>{{ checkDataSelected(data) ? '追加済' : '追加' }}</span>
                    </i>
                  </el-button>
                </div>
                <div class="block-body">
                  <div class="block-left">
                    <div class="card-item_body">
                      <ul class="card-item_content">
                        <li>
                          <span>バージョン：</span>
                          <span class="list-label">{{ data.document_version }}</span>
                        </li>
                        <li>
                          <span>品目：</span>
                          <span class="list-label">{{ data.product_name }}</span>
                        </li>
                        <li>
                          <span>対象疾患：</span>
                          <span class="list-label">{{ data.disease }}</span>
                        </li>
                      </ul>
                      <ul class="card-item_content">
                        <li>
                          <span>安全性情報：</span>
                          <span class="list-label">
                            {{ data.safety_information_flag === '' ? '全て' : data.safety_information_flag === 1 ? 'あり' : 'なし' }}
                          </span>
                        </li>
                        <li>
                          <span>適応外情報：</span>
                          <span class="list-label">
                            {{ data.off_label_information_flag === '' ? '全て' : data.off_label_information_flag === 1 ? 'あり' : 'なし' }}
                          </span>
                        </li>
                        <li>
                          <span>診療科目分類：</span>
                          <span class="list-label">{{ data.medical_subjects_group_name }}</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="block-right">
                    <div class="mt-2">
                      <span class="text-info-content"> 適用期間： </span>
                      <span class="list-label">
                        {{ formatFullDate(data.start_date) }} ～
                        {{ formatFullDate(data.end_date) === '9999/12/31' ? '' : formatFullDate(data.end_date) }}
                      </span>
                    </div>
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
              </div>
            </div>
          </div>
        </div>
        <div v-if="lstMasterial.length === 0 && !loadingPage" class="noData">
          <div class="noData-content">
            <p class="noData-text">該当するデータがありません。</p>
            <div class="noData-thumb">
              <img src="@/assets/template/images/data/amico.svg" alt="" />
            </div>
          </div>
        </div>

        <div v-if="lstMasterial.length > 0 && !loadingPage" class="block-pagination block-pagination-multi">
          <div v-if="lstMasterial.length > 0" class="pagination pagination-custom">
            <div class="pagination-cases">全{{ lstMasterial.length }}件</div>
            <PaginationTable :page-size="pageSize" :current-page="curentPage" :total="lstMasterial.length" @current-change="handleCurrentChange" />
          </div>
        </div>
      </div>

      <!-- right -->
      <div class="col-md-4 pl-4 pr-0 d-flex flex-column">
        <div class="block">
          <li class="block-title block-title-multi cusor-default"><span>選択リスト</span></li>
          <div :class="`${loadingPageDefault ? 'pre-loader h-505' : ''}`">
            <Preloader v-if="loadingPageDefault" />
            <div v-else class="list-group">
              <li
                v-for="item in lstMasterialSelected"
                :key="item.document_id"
                class="list-group-item list-group-item-action block-item item--nonecusor cusor-default"
              >
                <div class="left">
                  <div class="text item-file">
                    <span class="item-icon">
                      <img v-if="item.document_type == '10' && item.file_type == '10'" class="svg" src="@/assets/template/images/icon_pdf_manag_1.svg" alt="" />
                      <img v-if="item.document_type == '10' && item.file_type == '20'" class="svg" src="@/assets/template/images/icon_film.svg" alt="" />
                      <img v-if="item.document_type == '20'" class="svg" src="@/assets/images/icon_document_spanner.svg" alt="" />
                    </span>
                    <span class="item-name">{{ item.document_name }}</span>
                  </div>
                  <div :style="{ marginLeft: '22px' }">
                    <span> バージョン: </span>
                    <span class="list-label">{{ item.document_version }}</span>
                  </div>
                </div>
                <div class="right">
                  <el-button type="button" class="btn-circle" @click="onRemoveItem(item)">
                    <i class="el-icon-close"></i>
                  </el-button>
                </div>
              </li>
              <div v-if="lstMasterialSelected.length === 0" class="noData h-none">
                <div class="noData-content">
                  <p class="noData-text">資材を選択して下さい。</p>
                  <div class="noData-thumb">
                    <img src="@/assets/template/images/data/amico.svg" alt="" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="mode !== 'single'" class="block-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
      <button type="button" class="btn btn-primary" :disabled="checkDisabledSubmit()" @click="returnData()">決定</button>
    </div>
  </div>

  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :top="'10vh'"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
    </template>
  </el-dialog>
</template>

<script>
/* eslint-disable eqeqeq */
/* eslint-disable indent */
import { markRaw } from 'vue';
import PaginationTable from '../../../components/PaginationTable.vue';
import Z05_S05_ChoiceOfMasterialService from '@/api/Z05/Z05_S05_ChoiceOfMasterialService';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import D01_S02_ModalMaterialDetails from '@/views/D01/D01_S02_MaterialDetails/D01_S02_ModalMaterialDetails';
import D01_S07_MaterialViewer from '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer';

export default {
  name: 'Z05_S05_Choice_Of_Masterial',
  components: { PaginationTable, Z05S06MaterialSelection },
  props: {
    mode: {
      type: String,
      required: true,
      default: 'single'
    },
    // 1: allow select child material || 0: not allow  (require)
    subMaterialSelectableFlag: {
      type: Number,
      required: true,
      default: 1
    },
    // 1: allow choice カスタム資材 || 0: not  allow (require)
    customMaterialFlag: {
      type: Number,
      required: true,
      default: 1
    },
    //
    screen: {
      type: String,
      required: false,
      default: ''
    },
    orgSelected: {
      type: String,
      required: false,
      default: ''
    },
    selectParamFlag: {
      type: Boolean,
      required: false,
      default: false
    },
    params: {
      type: Object,
      required: true,
      default() {
        return {
          initMaterials: [], // if was => only allow array initMaterials
          materialType: '10', // '10': 原本資材,  '20': カスタム資材
          document_name: '',
          productCode: '',
          productName: '',
          medicalSubjectsGroupCode: '',
          safetyInformationFlag: '', // '': 全て, 1: あり, 0: なし
          offLabelInformationFlag: '', // '': 全て, 1: あり, 0: なし
          date: null
        };
      }
    }
  },

  emits: { onFinishScreen: null },

  data() {
    return {
      loadingPage: true,
      loadingPageDefault: true,
      showFilter: false,
      formData: {
        document_type:
          this.mode === 'single'
            ? this.params.materialType
              ? this.customMaterialFlag == 1
                ? this.params.materialType
                : '10'
              : ''
            : this.params.materialType
            ? this.customMaterialFlag == 1
              ? this.params.materialType
              : '10'
            : '',
        document_name: this.mode !== 'single' ? '' : this.params.document_name || '',
        product_cd: this.mode !== 'single' ? (this.selectParamFlag ? this.params.productCode || '' : '') : this.params.productCode || '',
        product_name: this.mode !== 'single' ? (this.selectParamFlag ? this.params.productName || '' : '') : this.params.productName || '',
        medical_subjects_group_cd: this.mode !== 'single' ? '' : this.params.medicalSubjectsGroupCode || '',
        safety_information_flag: this.mode !== 'single' ? '' : this.params.safetyInformationFlag ? this.params.safetyInformationFlag : '',
        off_label_information_flag: this.mode !== 'single' ? '' : this.params.offLabelInformationFlag ? this.params.offLabelInformationFlag : '',
        date: this.mode !== 'single' ? (this.selectParamFlag ? this.params.date || null : null) : this.params.date || null,
        document_id: this.params.initMaterials?.length > 0 ? this.params.initMaterials.toString() : '',
        subMaterialSelectableFlag: this.subMaterialSelectableFlag == 1 ? true : false
      },

      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        customClass: 'custom-dialog custom-dialog-pd'
      },

      lstmedicalSubjectGroups: [],

      lstMasterial: [],
      lstMasterialSelected: [],

      lstMasterialType: [],
      lstOffLabelInfomation: [],
      lstSafetyInfomation: [],

      propDataZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      paramFilterOld: {},
      pageSize: 100,
      curentPage: 1,

      lstOrgGroup: []
    };
  },

  computed: {
    getDataDisplay() {
      let data = this.lstMasterial.slice(this.pageSize * this.curentPage - this.pageSize, this.pageSize * this.curentPage).map((x) => {
        return {
          ...x,
          point: x.point === '5.0' ? '5' : x.point
        };
      });
      return data;
    }
  },

  mounted() {
    this.getDataScreen();
    if (this.screen === 'D01_S05' && this.orgSelected) {
      this.getOrgGroup(this.orgSelected);
    }
  },
  methods: {
    clickOutside() {
      this.onCloseFilter();
    },
    // get data initial
    getDataScreen() {
      this.loadingPage = true;
      this.loadingPageDefault = true;
      Z05_S05_ChoiceOfMasterialService.getDataScreen()
        .then((res) => {
          this.lstmedicalSubjectGroups = res?.data?.data?.medical_subjects;
          this.lstMasterialType = res?.data?.data?.material;
          this.lstOffLabelInfomation = res?.data?.data?.off_label;
          this.lstSafetyInfomation = res?.data?.data?.safety;
          this.getDataByFilter(true);
        })
        .catch((err) => {
          this.loadingPage = false;
          this.loadingPageDefault = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S05', idNodeNotify: 'msfa-notify-Z05S05' });
        });
    },

    // Load data by filter
    getDataByFilter(isLoadDefault) {
      let params = {
        ...this.formData,
        document_id: ''
      };
      this.loadingPage = true;
      this.loadingPageDefault = isLoadDefault;
      this.lstMasterial = [];
      this.onCloseFilter();
      Z05_S05_ChoiceOfMasterialService.getDataByFilter(params)
        .then((res) => {
          this.curentPage = 1;
          this.lstMasterial = res?.data?.data.length > 0 ? res?.data?.data : [];
          if (this.mode === 'single') {
            this.lstMasterialSelected = [];
          }

          if (isLoadDefault) {
            if (this.params.initMaterials?.length > 0) {
              if (this.mode === 'single') {
                for (let i = 0; i < this.lstMasterial.length; i++) {
                  const element = this.lstMasterial[i];
                  if (this.params.initMaterials.some((e) => e == element.document_id)) {
                    this.setSelectedMaterial(element.document_id);
                  }
                }
              } else {
                this.getDataByFilterMulti();
              }
            }
          }
          this.paramFilterOld = params;
        })
        .catch((err) => {
          this.loadingPage = false;
          this.loadingPageDefault = false;
          if (err?.response?.data?.message) {
            this.notifyModal({ message: err?.response?.data?.message, type: 'error', classParent: 'modal-body-Z05S05', idNodeNotify: 'msfa-notify-Z05S05' });
          } else {
            this.notifyModal({
              message: '内部エラーが発生しました。システム管理者に連絡してください。',
              type: 'error',
              classParent: 'modal-body-Z05S05',
              idNodeNotify: 'msfa-notify-Z05S05'
            });
          }
        })
        .finally(() => {
          if (!(isLoadDefault && this.params.initMaterials?.length > 0) || this.mode === 'single') {
            this.loadingPage = false;
            this.loadingPageDefault = false;
            this.onCloseFilter();
          }
        });
    },

    getDataByFilterMulti() {
      let params = {
        ...this.formData,
        document_type: '20',
        document_id: ''
      };

      Z05_S05_ChoiceOfMasterialService.getDataByFilter(params)
        .then((res) => {
          this.lstMasterialSelected = [];
          let datas = res?.data?.data.length > 0 ? res?.data?.data : [];
          let dataChecks = [...this.lstMasterial, ...datas];

          if (this.params.initMaterials?.length > 0) {
            for (let i = 0; i < this.params.initMaterials.length; i++) {
              const id = this.params.initMaterials[i];
              let index = dataChecks.findIndex((e) => e.document_id == id);
              if (index > -1) {
                this.lstMasterialSelected.push(dataChecks[index]);
              }
            }
          }
        })
        .catch((err) => {
          this.loadingPage = false;
          if (err?.response?.data?.message) {
            this.notifyModal({ message: err?.response?.data?.message, type: 'error', classParent: 'modal-body-Z05S05', idNodeNotify: 'msfa-notify-Z05S05' });
          } else {
            this.notifyModal({
              message: '内部エラーが発生しました。システム管理者に連絡してください。',
              type: 'error',
              classParent: 'modal-body-Z05S05',
              idNodeNotify: 'msfa-notify-Z05S05'
            });
          }
        })
        .finally(() => {
          this.loadingPage = false;
          this.loadingPageDefault = false;
        });
    },

    handleCurrentChange(val) {
      this.curentPage = val;
    },

    setSelectedMaterial(document_id, e) {
      let target = e?.composedPath() || e?.composedPath;
      if (
        target &&
        (target[0].classList?.contains('btn') || target[0].classList?.contains('svg') || target[1].classList?.contains('svg')) &&
        this.lstMasterialSelected.length > 0
      ) {
        return;
      }
      this.lstMasterialSelected = [];
      this.onCloseFilter();
      this.lstMasterial.forEach((x) => {
        if (x.document_id === document_id) {
          x.selected = !x.selected;
          if (x.selected) {
            this.lstMasterialSelected.push(x);
          }
        } else {
          x.selected = false;
        }
      });
    },

    // check material in group org
    checkMaterial(document) {
      if (this.screen === 'D01_S05') {
        if (!document.available_org_cd || this.lstOrgGroup.length == 0 || this.lstOrgGroup.includes(document.available_org_cd)) {
          return true;
        }
        return false;
      }
      return true;
    },

    // get array org in group
    getOrgGroup(available_org_cd) {
      let params = {
        org_cd: available_org_cd
      };

      Z05_S05_ChoiceOfMasterialService.getDataOrgGroup(params)
        .then((res) => {
          this.lstOrgGroup = res?.data.data?.list_org.split(',');
        })
        .catch((err) => {
          if (err?.response?.data?.message) {
            this.notifyModal({ message: err?.response?.data?.message, type: 'error', classParent: 'modal-body-Z05S05', idNodeNotify: 'msfa-notify-Z05S05' });
          } else {
            this.notifyModal({
              message: '内部エラーが発生しました。システム管理者に連絡してください。',
              type: 'error',
              classParent: 'modal-body-Z05S05',
              idNodeNotify: 'msfa-notify-Z05S05'
            });
          }
        })
        .finally(() => {});
    },

    // modal filter
    openModalFilter() {
      this.showFilter = true;
      document.getElementById('msfa-notify-Z05S05')?.closest('.el-dialog.custom-dialog').classList.add('pointer-none');
    },

    onCloseFilter() {
      this.showFilter = false;
      document.getElementById('msfa-notify-Z05S05')?.closest('.el-dialog.custom-dialog').classList.remove('pointer-none');
    },

    // check form
    validationDocumentName() {
      let document_name = this.formData.document_name;
      if (document_name?.length > 50) {
        return true;
      }
      return false;
    },

    handleRemoveProduct() {
      this.formData.product_cd = '';
      this.formData.product_name = '';

      this.showModalZ05S06 = false;

      this.propDataZ05S06 = {
        ...this.propDataZ05S06,
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      };
    },

    // open Modal Z05 - S06
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        component: markRaw(Z05S06MaterialSelection),
        width: '33rem',
        props: { ...this.propDataZ05S06 }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected?.length > 0 ? e?.dataSelected[0] : {};
      this.formData.product_cd = data?.product_cd;
      this.formData.product_name = data?.product_name;

      this.propDataZ05S06 = {
        ...this.propDataZ05S06,
        categoryCode: e.category.definition_value,
        classificationCode: e.classifications.product_group_cd,
        initDataCodes: [data?.product_cd]
      };

      this.onCloseModal();
    },

    redirectToD01S02(item) {
      let document_id = item.document_id;
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        isShowClose: true,
        customClass: 'custom-dialog custom-dialog-pd custom-dialog-material',
        component: markRaw(D01_S02_ModalMaterialDetails),
        props: { documentId: document_id },
        width: '90%',
        destroyOnClose: true,
        closeOnClickMark: false
      };
    },

    openModalMaterialViewerD01S07(document) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材ビューワ',
        isShowModal: true,
        component: markRaw(D01_S07_MaterialViewer),
        width: '70%',
        customClass: 'custom-dialog',
        props: { documentId: document.document_id }
      };
    },

    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S06') {
          this.onResultModalZ05S06(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog custom-dialog-pd' };
    },

    // mode multi
    // Check data
    checkDataSelected(item) {
      let index = this.lstMasterialSelected.findIndex((x) => x.document_id == item.document_id);

      return index > -1 ? true : false;
    },

    onAddItem(item) {
      let index = this.lstMasterial.findIndex((x) => x.document_id == item);
      if (index > -1) {
        this.lstMasterialSelected.push(this.lstMasterial[index]);
      }
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
      this.showFilter = false;
      if (data) {
        this.lstMasterialSelected = [];
        this.lstMasterialSelected.push(data);
      }
      let result = {
        screen: 'Z05_S05',
        dataMasterialSelected: this.lstMasterialSelected,
        filterData: {
          materialType: this.formData.document_type,
          document_name: this.formData.document_name,
          productCode: this.formData.product_cd,
          productName: this.formData.product_name,
          medicalSubjectsGroupCode: this.formData.medical_subjects_group_cd,
          safetyInformationFlag: this.formData.safety_information_flag,
          offLabelInformationFlag: this.formData.off_label_information_flag
        }
      };

      this.$emit('onFinishScreen', result);
    },

    closeModal() {
      this.showFilter = false;
      this.$emit('onFinishScreen');
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet_horizontal: 1024px;

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

.wrapBtn {
  padding: 0 13px;
  position: absolute;
  right: 16px;
  z-index: 1;
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
      border: 0.5px solid #dcdfe6;
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
        border-right: 0.5px solid #dcdfe6;
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

.block-item .left {
  display: flex;
  flex-wrap: nowrap !important;
  flex-direction: column !important;
  padding: 10px 4px 10px 10px !important;
}

.el-form-item {
  margin-bottom: 10px;

  &label {
    padding: 0 5px 0 0 !important;
  }
}

.block-masterial-multi {
  border-top-right-radius: 10px;
  padding-left: 5px;
}

.block-masterial {
  height: 510px;
  padding: 0;
  padding-right: 10px;
  background: #f2f2f2;
  .block-body {
    @include flexbox(stretch, nowrap);

    .block-left {
      flex-grow: 1;
      border-right: 1px solid #e3e3e3;
    }

    .block-right {
      text-align: right;
      flex: 0 0 244px;

      .card-item_content {
        justify-content: flex-end;
        margin-top: 12px;
      }
    }
  }

  .material-item-card-active {
    background: #eef6ff !important;
    background-color: #eef6ff !important;

    border-left: 8px solid #448add;
    transition: all 0.2s ease-out;
  }

  .material-item-card {
    margin-left: 8px;
  }

  .block-masterial_item {
    padding-right: 10px;

    .card-item_header {
      margin: 0 0 10px 0;
      color: #5f6b73;
      display: flex;
      align-items: flex-start;
      font-weight: bold;
      font-size: 22px;
      gap: 8px;

      .item_icon {
        margin-top: -3px;
      }

      .btn.btn--icon {
        flex: 0 0 32px;
        height: 32px;
        margin-left: 15px;
      }
    }

    .block-header {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .card-item_body {
      font-size: 14px;
    }

    .card-item_content {
      @include flexbox(center, wrap, unset);
      color: #5f6b73;
      li {
        margin-top: 0.25rem;
        padding-right: 30px;
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

.contentDoc {
  height: 100%;
  padding: 5px 10px;
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
  height: 505px;
  overflow-y: scroll;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 8px 0 0 8px;
  background: #fff;
}

.list-group-item {
  border: none;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
  &:not(.block-title-multi) {
    min-height: unset;
    height: auto;
  }
  &:first-child {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
  }

  .item-file {
    @include flexbox(flex-start !important, nowrap);

    img {
      width: 15px;
    }
  }
  .item-name {
    margin-left: 7px;
  }
  .item-icon {
    flex: 0 0 15px;
  }
}

// btn
.block-btn {
  padding: 15px;
  text-align: center;
  border-radius: 0 0 8px 0;
  .btn {
    width: 180px;
    margin-right: 14px;
    margin-bottom: 10px;
  }
}

.el-button {
  min-height: 30px;
}

.btn-add {
  flex: 0 0 75px;
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
  background: #448add !important;
  color: #ffffff !important;
  &:hover {
    background: #448add !important;
    color: #ffffff !important;
  }
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
  padding: 16px 20px;
  box-shadow: inset 3px 2px 6px #e3e3e3;
  border-radius: 0 0 10px 10px;
  justify-content: space-between;
  align-items: center;
  padding-right: 29px;
  .block-btn {
    background: transparent;
    padding: 0;
    margin-right: 0.5rem;
    .btn {
      width: 180px;
      margin-right: 10px;
      margin-bottom: 10px;
    }
  }
}

.block-pagination-multi {
  background: #f9f9f9;
  box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
  border-radius: 0px 0px 8px 0px;
  justify-content: flex-end;
  height: 54px;
  padding-right: 28px;
}

.form-control[readonly] {
  background-color: transparent;
}

//
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
.btn-filter:disabled,
.btn-select:disabled {
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

.h-custom {
  height: 510px !important;
}

.item--cusor {
  cursor: pointer;
}

.item--nonecusor {
  cursor: unset !important;
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
        cursor: pointer;
        border-radius: 0;
        padding: 0;
        height: 40px;
        text-align: center;
        vertical-align: middle;
        padding: 0.375rem 0.75rem;

        line-height: 25px;
        color: #5f6b73;
        font-size: 0.875rem;
        border: 1px solid #727f88;
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
        &.disabled {
          cursor: not-allowed;
          pointer-events: none;
          opacity: 0.5;
        }
      }
    }
  }
}

.h-505 {
  height: 505px;
}

.h-510 {
  height: 510px;
}
.h-564 {
  height: 564px;
}

.card-gray {
  background: #f2f2f2;
  opacity: 0.6;
  cursor: default;
  .material-item-card {
    background: #f2f2f2;
  }
  .link {
    color: #5f6b73;
  }
  button {
    pointer-events: none;
    &:hover {
      outline: unset;
      cursor: unset;
    }
  }
}

.dropdown-block.dropdown-filter,
.btn.btn-filter {
  pointer-events: auto;
}

@media (min-width: 769px) and (max-width: $viewport_tablet_horizontal) {
  .list-group {
    height: 380px;
  }

  .block-masterial {
    height: 386px;
  }

  .h-564 {
    height: 440px;
  }

  .h-505 {
    height: 380px;
  }

  .h-custom {
    height: 386px !important;
  }
  .h-510 {
    height: 386px !important;
  }
}

@media (max-width: 768px) {
  .block-masterial_item {
    min-width: 720px;
  }
}

@media (min-width: 1200px) and (max-height: 768px) {
  .block-masterial {
    height: 434px;
    &.single {
      height: 484px;
    }
  }
  .list-group {
    height: 428px;
  }
  .h-564 {
    height: 488px;
  }

  .h-505 {
    height: 428px;
  }
  .h-custom {
    height: 434px;
  }

  .h-510 {
    height: 434px;
    &.single {
      height: 484px;
    }
  }
}

.scrollbar {
  overflow: overlay;
}

@import '@/assets/css/pages/Z05';
</style>
