<template>
  <div class="wrapContent">
    <div v-loading="loadingPage" v-load-f5="true" :class="`groupMain ${showDataStock ? 'pb-0' : ''}`">
      <div class="wrapBtn">
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn">
            <button
              v-if="!showDataStock"
              type="button"
              class="btn btn-filter btn-Wauto"
              @click="confirmChange('openStock')"
              @touchstart="confirmChange('openStock')"
            >
              <span class="btn-iconLeft">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_defaultbig.svg')" alt="" />
              </span>
              ストック登録
            </button>
          </div>

          <div class="btnInfo-btn filter-wrapper">
            <button type="button" class="btn btn-filter" :class="isShowPopupFilter ? 'btn-filter-none-shadow' : ''" @click="openModalFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter dropdown-InFacility', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter" @click="onCloseFilter">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="form-InFacility">
                <ul>
                  <!-- C-2 -->
                  <li>
                    <label class="form-InFacility-label">所属部科</label>
                    <div class="form-InFacility-control">
                      <el-select v-model="formData.department_cd" placeholder="未選択" class="form-control-select">
                        <el-option :value="''">未選択</el-option>
                        <el-option v-for="item in lstDepartment" :key="item.department_cd" :label="item.department_name" :value="item.department_cd">
                        </el-option>
                      </el-select>
                    </div>
                  </li>

                  <li>
                    <label class="form-InFacility-label">個人名</label>
                    <div class="form-InFacility-control">
                      <el-input v-model="formData.person_name" clearable placeholder="名前入力" class="form-control-input" />
                    </div>
                  </li>
                  <li>
                    <label class="form-InFacility-label">ターゲット品目</label>
                    <div class="form-InFacility-control">
                      <el-select v-model="formData.target_product_cd" placeholder="未選択" class="form-control-select">
                        <el-option :value="''">未選択</el-option>
                        <el-option
                          v-for="item in lstTargetProduct"
                          :key="item.target_product_cd"
                          :label="item.target_product_name"
                          :value="item.target_product_cd"
                        >
                        </el-option>
                      </el-select>
                    </div>
                  </li>
                  <li>
                    <label class="form-InFacility-label">セグメント</label>
                    <div class="form-InFacility-check">
                      <ul>
                        <li v-for="segment in lstSegment" :key="segment.segment_type">
                          <label class="custom-control custom-checkbox custom-control--bordGreen">
                            <input
                              class="custom-control-input"
                              type="checkbox"
                              :checked="getSegmentTypeChecked(segment.segment_type)"
                              @click="setSegmentTypeChecked(segment.segment_type)"
                            />
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">{{ segment.segment_name }}のみ</span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
                <div class="btn-InFacility">
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCloseFilter">キャンセル</button>
                  <button type="button" class="btn btn-primary" @click="onFilterData(true, false)">検索</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="!showDataStock"
        class="inFacility-content"
        :class="actionStock && !isLoadDefault ? (!showDataStock ? 'slide-right-child' : ' slide-left-child') : ''"
      >
        <div class="application-btn">
          <button v-if="showScrollLeft && lstDataTargetPerson.length > 0" type="button" class="btn btn--prev" @click="onScrollLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
          <button v-if="showScrollRight && lstDataTargetPerson.length > 0" type="button" class="btn btn--next" @click="onScrollRight">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
        </div>
        <div ref="inFacilityContent" class="table-inFacility table-hg100 scrollbar" @scroll="onScroll">
          <table>
            <thead>
              <tr>
                <th rowspan="2">施設個人</th>
                <th :colspan="lstSegment?.length">セグメント</th>
                <th :colspan="lstTargetProduct?.length">施設個人ターゲット</th>
              </tr>
              <tr>
                <th v-for="segment in lstSegment" :key="segment.segment_type">
                  <div class="inFacility-sort">
                    <span class="inFacility-label">{{ segment.segment_name }}</span>
                    <span
                      :class="`inFacility-item item_segment log-icon ${checkSegmentSort(segment.segment_type) ? 'icon-sorted' : ''}`"
                      title_log="ソート"
                      @click="handleSort('segment', segment.segment_type)"
                      @touchstart="handleSort('segment', segment.segment_type)"
                    >
                      <img v-if="checkSegmentSort(segment.segment_type)" src="@/assets/template/images/icon_sorted.svg" alt="icon_sorted" />
                      <img v-else src="@/assets/template/images/icon_sort.svg" alt="icon_sort" />
                    </span>
                  </div>
                </th>

                <th
                  v-for="product in lstTargetProduct"
                  :key="product.target_product_cd"
                  class="productCol"
                  @mouseenter="checkItemProductOver(`text_product_${product.target_product_cd}`)"
                >
                  <div class="inFacility-sort">
                    <div :id="`text_product_${product.target_product_cd}`" class="inFacility-label text-wrap">
                      {{ product.target_product_name }}
                    </div>
                    <span
                      :class="`inFacility-item log-icon ${checkTargetProductSort(product.target_product_cd) ? 'icon-sorted' : ''}`"
                      title_log="ソート"
                      @click="handleSort('target_product', product.target_product_cd)"
                      @touchstart="handleSort('target_product', product.target_product_cd)"
                    >
                      <img v-if="checkTargetProductSort(product.target_product_cd)" src="@/assets/template/images/icon_sorted.svg" alt="icon_sorted" />
                      <img v-else src="@/assets/template/images/icon_sort.svg" alt="icon_sort" />
                    </span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr class="inFacility-head">
                <td :colspan="lstSegment?.length + 1" :class="isStickyFacilityName ? 'position-sticky' : ''">
                  <div style="white-space: break-spaces; word-break: break-word">{{ facilityInfo?.facility_short_name }}</div>
                </td>
                <td v-for="product in lstTargetProduct" :key="product.target_product_cd">
                  <div class="inFacility-sort">
                    <span class="inFacility-label">{{ getTargetSubFacility(product) }}</span>
                  </div>
                </td>
              </tr>
              <tr v-for="(person, index) in lstDataTargetPerson" v-show="lstDataTargetPerson.length > 0" :key="person.person_cd" class="inFacility-content">
                <td class="sticky">
                  <p class="inFacility-title">{{ person.department_name }}</p>
                  <p class="inFacility-label">
                    <a class="inFacility-link log-icon" title_log="個人名" @click="confirmChange('H02S02', person)"> {{ person.person_name }} </a>　
                    <span class="inFacility-name">{{ person.position_name }}</span>
                  </p>
                </td>
                <!-- td segment -->
                <td
                  v-for="segment in lstSegment"
                  :key="segment.segment_type"
                  :class="`${changeValue('segment', person, index, null, segment) ? 'inFacility-active' : ''}`"
                >
                  <label class="custom-control custom-checkbox">
                    <input
                      class="custom-control-input"
                      type="checkbox"
                      :disabled="segment.uneditable_flag == 1"
                      :checked="person.segment_list?.some((x) => x.segment_type === segment.segment_type)"
                      @change="checkedSegment(segment, index)"
                    />
                    <span class="custom-control-indicator"></span>
                  </label>
                </td>
                <!--td target_product -->
                <td
                  v-for="product in lstTargetProduct"
                  :key="product.target_product_cd"
                  :class="`productCol ${changeValue('product', person, index, product, null) ? 'inFacility-active' : ''}`"
                >
                  <label class="custom-control custom-checkbox">
                    <input
                      class="custom-control-input"
                      type="checkbox"
                      :checked="person.target_product_list?.some((x) => x.target_product_cd === product.target_product_cd)"
                      :disabled="!checkPermission() && !checkDate(product.selection_start_date, product.selection_end_date)"
                      @change="checkedProduct(product, index)"
                    />
                    <span class="custom-control-indicator"></span>
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
          <EmptyData v-if="lstDataTargetPerson.length === 0 && !isLoadDefault" />
        </div>
      </div>

      <div v-show="!showDataStock" class="paginFooter">
        <div class="paginFooter-btn">
          <el-button type="primary" class="btn btn-primary" :loading="processingSave" :disabled="processingSave" @click.prevent="saveTargetPerson()">
            保存
          </el-button>
          <el-button
            type="primary"
            class="btn btn-outline-primary btn-outline-primary--cancel btn-hidden-sm"
            :loading="processingReport"
            :disabled="processingReport"
            @click.prevent="exportFile()"
          >
            一覧出力
          </el-button>
        </div>
        <div v-if="lstDataTargetPerson.length > 0" class="paginFooter-number">
          <div class="pagination-cases">全 {{ pagination.totalItems }} 件</div>
          <PaginationTable
            :page-size="pagination.pageSize"
            :total="pagination.totalItems"
            :pager-counts="currDevice() !== 'Desktop' ? 3 : 7"
            :current-page="pagination.curentPage"
            @current-change="handleCurrentChange"
          />
        </div>
      </div>
      <div
        v-show="showDataStock"
        class="inFacility-row scrollbar"
        :class="actionStock && !isLoadDefault ? (!showDataStock ? 'slide-right-child' : ' slide-left-child') : ''"
      >
        <div class="inFacility-left">
          <div class="inFacilityStock">
            <div
              ref="inFacilityStock"
              class="inFacilityStock-tbl table-hg100 scrollbar"
              :class="lstDataTargetPersonStock.length === 0 ? 'datastock-tbl-empty' : ''"
            >
              <table>
                <thead>
                  <tr>
                    <th rowspan="2">施設個人</th>
                    <th :colspan="lstSegment?.length">セグメント</th>
                    <th rowspan="2" class="colBtn"></th>
                  </tr>
                  <tr>
                    <th v-for="segment in lstSegment" :key="segment.segment_type">
                      <div class="inFacility-sort">
                        <span class="inFacility-label">{{ segment.segment_name }}</span>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="inFacilityStock-head">
                    <td :colspan="lstSegment?.length + 2">{{ facilityInfo?.facility_short_name }}</td>
                  </tr>
                  <tr
                    v-for="person in lstDataTargetPersonStock"
                    v-show="lstDataTargetPersonStock.length > 0"
                    :key="person.person_cd"
                    class="inFacilityStock-content"
                  >
                    <td>
                      <p class="inFacility-title">{{ person.department_name }}</p>
                      <p class="inFacility-label">
                        <a class="inFacility-link log-icon" title_log="個人名" @click="confirmChange('H02S02', person)"> {{ person.person_name }} </a>　
                        <span class="inFacility-name">{{ person.position_name }}</span>
                      </p>
                    </td>
                    <td v-for="segment in lstSegment" :key="segment.segment_type">
                      <label class="custom-control custom-checkbox">
                        <input
                          class="custom-control-input"
                          type="checkbox"
                          :checked="person.segment_list?.some((x) => x.segment_type === segment.segment_type)"
                          disabled
                        />
                        <span class="custom-control-indicator"></span>
                      </label>
                    </td>
                    <td>
                      <button
                        v-if="checkDataPersonInStock(person) && person.person_cd"
                        type="button"
                        class="btn btn--icon postion-re"
                        @click="removePersonStock(person)"
                      >
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack01.svg')" alt="" />
                      </button>
                      <button
                        v-if="!checkDataPersonInStock(person) && person.person_cd"
                        type="button"
                        class="btn btn--icon postion-re"
                        @click="addPersonStock(person)"
                      >
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack.svg')" alt="" />
                      </button>
                    </td>
                  </tr>
                  <tr v-if="lstDataTargetPersonStock.length === 0 && !isLoadDefault" class="noData_table">
                    <td :colspan="lstSegment?.length + 2">
                      <EmptyData />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-if="lstDataTargetPersonStock.length > 0" class="paginStock">
            <div class="pagination-cases">全 {{ paginationStock.totalItems }} 件</div>
            <PaginationTable
              :page-size="paginationStock.pageSize"
              :total="paginationStock.totalItems"
              :pager-counts="currDevice() !== 'Desktop' ? 3 : 7"
              :current-page="paginationStock.curentPage"
              @current-change="handleCurrentChange"
            />
          </div>
        </div>

        <div class="inFacility-right">
          <div class="titleStock">
            <h2 class="titleStock-tlt">ストック登録</h2>
            <span class="titleStock-more" @click="confirmCancelStock()" @touchstart="confirmCancelStock()">
              閉じる
              <img src="@/assets/template/images/icon_arrow-line02.svg" alt="" />
            </span>
          </div>
          <div class="contentStock">
            <div class="contentStock-box">
              <div v-if="lstDataPersonStock.length > 0" class="contentStock-lst scrollbar">
                <ul>
                  <li v-for="item in lstDataPersonStock" :key="item.person_cd">
                    <div class="contentStock-info">
                      <p class="contentStock-tlt">{{ item.department_name }}</p>
                      <p class="contentStock-txt">
                        <span class="contentStock-name"> {{ item.person_name }}</span>
                        <span class="contentStock-span">{{ item.position_name }}</span>
                      </p>
                    </div>
                    <button type="button" class="btn btn--icon" @click="removePersonStock(item)">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                    </button>
                  </li>
                </ul>
              </div>
              <div v-else class="hg">
                <EmptyData class="empty-facility-details-notes" :title="'人を選択して下さい。'" />
              </div>
              <div class="contentStock-form">
                <ul>
                  <li>
                    <label class="formLabel"> 面談内容 </label>
                    <div class="formControl">
                      <el-select v-model="paramStock.content_cd" placeholder="未選択" class="form-control-select">
                        <el-option :value="''">未選択</el-option>
                        <el-option v-for="content in lstContent" :key="content.content_cd" :label="content.content_name" :value="content.content_cd">
                        </el-option>
                      </el-select>

                      <span v-if="messageRequired && !paramStock.content_cd && paramStock.product_cd.length > 0" class="invalid">
                        {{ messageRequired }}
                      </span>
                    </div>
                  </li>
                  <li>
                    <label class="formLabel"> 品目 </label>
                    <div class="formControl">
                      <div class="form-icon icon-right">
                        <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                        </span>
                        <div v-if="paramStock.product_cd.length > 0" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag
                              v-for="product in paramStock.product_cd"
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
                      <span v-if="messageRequired && paramStock.product_cd.length === 0 && paramStock.content_cd" class="invalid">
                        {{ messageRequired }}
                      </span>
                    </div>
                  </li>
                </ul>
                <div class="formBtn">
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancelStock()">キャンセル</button>
                  <button type="button" class="btn btn-primary" :disabled="lstDataPersonStock.length === 0" @click="registerStock()">登録</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog modal-fixed modal-fixed-min"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :show-close="modalConfig.isShowClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="closeConfirm"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable indent */
import moment from 'moment';
import { markRaw } from 'vue';
import G01_S02_Service from '@/api/G01/G01_S02_In_FacilityTargetPersonalManagementService';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import _, { isEqual } from 'lodash';
import { required } from '@/utils/validate';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'G01_S02_In_FacilityTargetPersonalManagement',
  mixins: [filter_popup],
  props: {
    facility_cd: {
      type: String,
      default: '',
      require: true
    },
    checkLoading: [Boolean]
  },
  data() {
    return {
      isLoadDefault: true,
      loadingPage: false,
      actionStock: false,
      showFilter: false,

      showScrollLeft: false,
      showScrollRight: true,

      showDataStock: false,
      processingSave: false,
      processingReport: false,

      facilityInfo: {},
      lstDepartment: [],
      lstTargetProduct: [],
      lstSegment: [],
      lstSubTarget: [],

      dataTargetPerson: {},
      lstDataTargetPerson: [],
      lstDataTargetPersonStock: [],
      lstDataTargetPersonOld: [],

      lstDataPersonStock: [],

      lstSegmentTypeChecked: [],

      lstContent: [],

      formData: {
        facility_cd: '',
        department_cd: '',
        person_name: '',
        target_product_cd: '',
        segment_type: []
      },

      paramStock: {
        facility_cd: [],
        person_cd: [],
        product_cd: [],
        content_cd: '',
        stock_type: '',
        action_id: ''
      },

      paramStockOld: {},
      paramFilterOld: {},

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowClose: true,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },
      paginationStock: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },

      isSearch: false,

      paramsZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      lstConditionSort: [],
      isStickyFacilityName: true,
      isDisableTooltipProduct: false,

      isSubmitStock: false,
      onScrollTop: 0,
      messageRequired: ''
    };
  },
  computed: {
    validation() {
      return {
        person_cd: { required: required(this.lstDataPersonStock) }
      };
    }
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: '施設内ターゲット個人管理',
      icon: 'icon_target-facility.svg',
      isShowBack: true
    });

    this.router = this._route('G01_S02_In_FacilityTargetPersonalManagement')?.params;

    if (this.router?.facility_cd) {
      this.formData.facility_cd = this.router?.facility_cd;
    }

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.G01_S02_In_FacilityTargetPersonalManagement || 0;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.G01_S02_In_FacilityTargetPersonalManagement || 1;

    this.getScreenData();
    this.getDataContent();
    this.js_pscroll(0.5);
  },

  updated() {
    this.$nextTick(() => {
      if (!isEqual(this.lstDataTargetPerson, this.lstDataTargetPersonOld) || this.lstDataPersonStock.length > 0) {
        localStorage.setItem('checkChangTab', true);

        // addEventListener('beforeunload', this.beforeUnloadListener, { capture: true });
      } else {
        localStorage.removeItem('checkChangTab');
        // removeEventListener('beforeunload', this.beforeUnloadListener, { capture: true });
      }
    });
  },

  methods: {
    beforeUnloadListener(event) {
      event.preventDefault();
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        props: { mode: 1, params: { type: null, data: null } },
        width: '35rem',
        destroyOnClose: true,
        closeOnClickMark: false
      };
      return (event.returnValue = ' ');
    },

    checkPermission() {
      const permission = this.$store.state.auth.policyRole?.includes('R90');
      return permission ? true : false;
    },

    checkDate(start_date, end_date) {
      if (new Date(start_date).getTime() <= new Date().getTime() && new Date().getTime() <= new Date(end_date).getTime()) {
        return true;
      } else {
        return false;
      }
    },

    changeValue(type, person, index, product, segment) {
      if (type === 'product') {
        let isChecked = person.target_product_list?.some((x) => x.target_product_cd === product.target_product_cd);
        let isOldValue = this.lstDataTargetPersonOld[index].target_product_list?.some((x) => x.target_product_cd === product.target_product_cd);

        if (isChecked !== isOldValue) {
          return true;
        }
        return false;
      } else {
        let isChecked = person.segment_list?.some((x) => x.segment_type === segment.segment_type);
        let isOldValue = this.lstDataTargetPersonOld[index].segment_list?.some((x) => x.segment_type === segment.segment_type);
        if (isChecked !== isOldValue) {
          return true;
        }
        return false;
      }
    },

    getTargetSubFacility(product) {
      let item = this.lstSubTarget.find((x) => x.target_product_cd === product.target_product_cd);
      return item ? item.sub_target : '';
    },

    getScreenData() {
      this.loadingPage = true;
      let params = {
        facility_cd: this.formData.facility_cd
      };
      G01_S02_Service.getScreenDataService(params)
        .then(async (res) => {
          let data = res.data.data;

          this.facilityInfo = data.facilityInfo || {};
          this.lstDepartment = data.department_c2 || [];
          this.lstTargetProduct = data.targetProduct_a3 || [];
          this.lstSegment = data.segmentType_c7 || [];
          this.lstSubTarget = data.subTarget || [];
          this.paramStock.stock_type = data?.parameterAddStock?.definition_value || '';

          this.paramStockOld = { ...this.paramStock };

          this.onFilterData(false, true);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.loadingPage = false;
        })
        .finally(() => {
          this.loading = false;
        });
    },

    getDataContent() {
      A02_S03_StockManagementServices.getAllContentService()
        .then((res) => {
          this.lstContent = res.data.data;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    },

    getDataTargetPersonManagement(isFilter, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.loadingPage = true;
      let params = {
        ...this.formData,
        segment_type: this.lstSegmentTypeChecked?.length > 0 ? this.lstSegmentTypeChecked.toString() : '',
        sort_order: [...this.lstConditionSort],
        offset: this.pagination.curentPage - 1,
        limit: 100
      };
      G01_S02_Service.targetPersonManagementService(params)
        .then((res) => {
          this.showFilter = false;
          let data = res.data.data;
          if (!this.showDataStock) {
            this.formData.segment_type = this.lstSegmentTypeChecked;
            this.paramFilterOld = { ...this.formData };
            this.lstDataTargetPerson = [...data.records];

            this.lstDataTargetPersonOld = JSON.parse(JSON.stringify(data.records));
            this.lstDataTargetPersonStock = this.lstDataTargetPersonOld;
            let pageinedStock = res.data?.data?.pagination;
            this.paginationStock = {
              ...this.paginationStock,
              totalItems: pageinedStock.total_items
            };

            let pageined = res.data?.data?.pagination;
            this.pagination = {
              ...this.pagination,
              totalItems: pageined.total_items
            };
          } else {
            this.lstDataTargetPersonStock = [...data.records];
            let pageinedStock = res.data?.data?.pagination;
            this.paginationStock = {
              ...this.paginationStock,
              totalItems: pageinedStock.total_items
            };
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.isSearch = false;
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          if (!this.showDataStock) {
            this.setCurrentPageScreen('G01_S02_In_FacilityTargetPersonalManagement', this.pagination.curentPage);

            if (this.$refs.inFacilityContent) {
              if (this.onScrollTop && !isFilter) {
                this.$refs.inFacilityContent.scrollTop = this.onScrollTop;
              } else {
                this.$refs.inFacilityContent.scrollTop = 0;
              }
              this.checkScroll();
            }
          }

          this.isLoadDefault = false;
          this.loadingPage = false;
          this.isSearch = false;
          this.actionStock = false;
          this.js_pscroll(0.5);

          if (this.$refs.inFacilityStock) {
            this.$refs.inFacilityStock.scrollTop = 0;
          }
        });
    },

    saveTargetPerson() {
      this.loadingPage = true;
      this.processingSave = true;
      let params = {
        records: [...this.lstDataTargetPerson]
      };
      G01_S02_Service.saveTargetPersonService(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.getDataTargetPersonManagement(false, false);
        })
        .catch((err) => {
          this.loadingPage = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.processingSave = false;
        });
    },

    onScrollLeft() {
      let content = document.querySelector('.table-inFacility');
      content.scrollLeft -= 180;
    },

    onScrollRight() {
      let content = document.querySelector('.table-inFacility');
      content.scrollLeft += 180;
    },

    onScroll({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }

      if (this.isScrolling) {
        let content = document.querySelector('.table-inFacility');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
      this.checkScroll();
    },

    setScrollTop() {
      let scrollTop = this.$refs.inFacilityContent ? this.$refs.inFacilityContent.scrollTop : 0;
      this.setScrollScreen('G01_S02_In_FacilityTargetPersonalManagement', scrollTop);
    },

    checkScroll() {
      this.isStickyFacilityName = true;
      if (this.$refs.inFacilityContent && this.$refs.inFacilityContent.scrollTop !== 0) {
        this.isStickyFacilityName = false;
      }
    },

    checkItemProductOver(id) {
      let idItem = document.getElementById(id);
      this.isDisableTooltipProduct = true;

      if (idItem?.scrollWidth > 114) {
        this.isDisableTooltipProduct = false;
      }
    },

    handleSort(type, value) {
      let index = this.lstConditionSort.findIndex((x) => x.order_type === type);

      let item = null;

      if (index > -1) {
        item = this.lstConditionSort[index];
      }

      if (item && item.order_value === value) {
        this.lstConditionSort.splice(index, 1);

        this.pagination = {
          ...this.pagination,
          curentPage: 1,
          totalItems: 0
        };

        this.getDataTargetPersonManagement(true, false);
      } else {
        if ((item && item.order_value !== value) || !item) {
          this.lstConditionSort = this.lstConditionSort.filter((x) => x.order_type !== type);
          this.lstConditionSort.unshift({
            order_type: type,
            order_value: value
          });

          this.pagination = {
            ...this.pagination,
            curentPage: 1,
            totalItems: 0
          };
          this.getDataTargetPersonManagement(true, false);
        }
      }
    },

    checkSegmentSort(segment) {
      let item = this.lstConditionSort.find((x) => x.order_type === 'segment');

      if (item && item.order_value === segment) {
        return true;
      }
      return false;
    },

    checkTargetProductSort(product) {
      let item = this.lstConditionSort.find((x) => x.order_type === 'target_product');

      if (item && item.order_value === product) {
        return true;
      }
      return false;
    },

    handleCurrentChange(val) {
      this.onCloseFilter();
      if (this.showDataStock) {
        this.paginationStock.curentPage = val;
      } else {
        this.onScrollTop = 0;
        this.pagination.curentPage = val;
      }
      this.getDataTargetPersonManagement(true, false);
    },

    // modal filter
    openModalFilter() {
      this.isShowPopupFilter = true;
      this.isSearch = false;
    },

    onCloseFilter() {
      this.isShowPopupFilter = false;
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
        this.getDataTargetPersonManagement(isFilter, isLoadDefault);
      } else {
        this.actionStock = false;
      }
      this.onCloseFilter();
    },

    getSegmentTypeChecked(type) {
      let checked = this.lstSegmentTypeChecked?.some((x) => x === type);
      return checked;
    },

    setSegmentTypeChecked(type) {
      let checked = !this.getSegmentTypeChecked(type);
      if (checked) {
        this.lstSegmentTypeChecked.push(type);
      } else {
        this.lstSegmentTypeChecked = this.lstSegmentTypeChecked.filter((x) => x !== type);
      }
    },

    checkedSegment(segment, index) {
      let person = this.lstDataTargetPerson[index];
      if (person.segment_list.some((x) => x.segment_type === segment.segment_type)) {
        let index = person.segment_list.findIndex((x) => x.segment_type === segment.segment_type);

        person.segment_list.splice(index, 1);
      } else {
        person.segment_list.push(segment);
      }

      this.lstDataTargetPerson[index] = { ...person };
    },

    checkedProduct(target_product, index) {
      let person = this.lstDataTargetPerson[index];
      if (person.target_product_list.some((x) => x.target_product_cd === target_product.target_product_cd)) {
        person.target_product_list = person.target_product_list.filter((x) => x.target_product_cd !== target_product.target_product_cd);
      } else {
        person.target_product_list.push(target_product);
      }

      this.lstDataTargetPerson[index] = { ...person };
    },

    confirmChange(type, data) {
      if (!isEqual(this.lstDataTargetPerson, this.lstDataTargetPersonOld) || this.lstDataPersonStock.length > 0) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1, params: { type: type, data: data } },
          width: '35rem',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        switch (type) {
          case 'H02S02':
            this.redirectToH02S02(data);
            break;
          case 'openStock':
            this.openStock();
            break;

          default:
            break;
        }
      }
    },

    closeConfirm(data) {
      this.resetData();

      switch (data.type) {
        case 'H02S02':
          this.redirectToH02S02(data.data);
          break;
        case 'openStock':
          this.openStock();
          break;
        case 'closeStock':
          this.closeStock();
          break;

        default:
          break;
      }
      this.onCloseModal();
    },

    resetData() {
      this.lstDataPersonStock = [];
      this.lstDataTargetPerson = [...this.lstDataTargetPersonOld];
    },

    redirectToH02S02(item) {
      this.setScrollTop();
      let person_cd = item.person_cd;
      localStorage.setItem('activeMenu', 'G01_S02_In_FacilityTargetPersonalManagement');
      this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
    },

    // Stock
    openStock() {
      this.actionStock = true;
      this.messageRequired = '';
      if (!this.showDataStock) {
        this.showDataStock = true;
        this.isSubmitStock = false;
        setTimeout(() => {
          this.js_pscroll(0.5);
        });
      }
    },

    closeStock() {
      this.actionStock = true;
      this.messageRequired = '';
      if (this.showDataStock) {
        this.showDataStock = false;
        this.isSubmitStock = false;
        this.lstDataPersonStock = [];
        this.paramStock = {
          ...this.paramStock,
          facility_cd: [],
          person_cd: [],
          product_cd: [],
          content_cd: ''
        };
        this.formData = { ...this.paramFilterOld };
        this.lstSegmentTypeChecked = [];
        this.formData.segment_type.forEach((x) => {
          this.setSegmentTypeChecked(x);
        });
        this.lstDataTargetPersonStock = this.lstDataTargetPersonOld;

        setTimeout(() => {
          this.js_pscroll(0.5);
          if (this.$refs.inFacilityContent) {
            if (this.onScrollTop) {
              this.$refs.inFacilityContent.scrollTop = this.onScrollTop;
            } else {
              this.$refs.inFacilityContent.scrollTop = 0;
            }
            this.checkScroll();
          }
        });
      }
      this.onCloseModal();
    },

    confirmCancelStock() {
      if (_.isEqual(this.paramStock, this.paramStockOld) && this.lstDataPersonStock.length === 0) {
        this.closeStock();
      } else {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1, params: { type: 'closeStock' } },
          width: '35rem',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      }
    },

    // check person duplicate
    checkDataPersonInStock(item) {
      let checked = this.lstDataPersonStock.some((x) => x.person_cd === item.person_cd);

      return checked;
    },

    addPersonStock(item) {
      if (!this.checkDataPersonInStock(item)) {
        this.lstDataPersonStock.push(item);
      }
    },

    removePersonStock(item) {
      if (this.checkDataPersonInStock(item)) {
        this.lstDataPersonStock = this.lstDataPersonStock.filter((x) => x.person_cd !== item.person_cd);
      }
    },

    // register Stock
    registerStock() {
      this.isSubmitStock = true;

      if ((this.paramStock.product_cd.length === 0 && this.paramStock.content_cd) || (this.paramStock.product_cd.length > 0 && !this.paramStock.content_cd)) {
        this.messageRequired = '選択してください。';
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      } else {
        this.messageRequired = '';
        let params = {
          ...this.paramStock,
          facility_cd: this.lstDataPersonStock.map((x) => x.facility_cd),
          person_cd: this.lstDataPersonStock.map((x) => x.person_cd),
          product_cd: this.paramStock.product_cd.map((x) => x.product_cd)
        };
        A02_S03_StockManagementServices.AddStockService(params)
          .then((res) => {
            this.$notify({ message: res.data.message, customClass: 'success' });
            this.closeStock();
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          });
      }
    },

    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05S06MaterialSelection),
        width: '33rem',
        props: {
          ...this.paramsZ05S06,
          initDataCodes: this.paramStock.product_cd?.map((x) => x.product_cd)
        }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected;
      this.paramStock.product_cd = data;

      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        categoryCode: e.category.definition_value,
        classificationCode: e.classifications.product_group_cd,
        initDataCodes: data?.map((x) => x.product_cd)
      };
    },

    handleRemoveProduct() {
      this.paramStock.product_cd = [];
    },

    exportFile() {
      this.processingReport = true;
      var FileSaver = require('file-saver');
      let params = {
        ...this.formData,
        segment_type: this.lstSegmentTypeChecked?.length > 0 ? this.lstSegmentTypeChecked.toString() : '',
        sort_order: [...this.lstConditionSort],
        offset: this.pagination.curentPage - 1,
        limit: 100
      };
      G01_S02_Service.exportFile(params)
        .then((res) => {
          let data = res.data;

          let fileName = `ターゲット施設個人一覧_${moment(new Date()).format('YYYYMMDD')}.xlsx`;

          var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8' });
          FileSaver.saveAs(blob, `${fileName}`);
        })
        .catch(async (err) => {
          let blob = err.response.data;
          let responseText = JSON.parse(await blob.text());

          this.$notify({ message: responseText.message, customClass: 'error' });
        })
        .finally(() => {
          this.processingReport = false;
        });
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
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowClose: true };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.wrapBtn {
  padding-bottom: 20px;
  .dropdown-InFacility {
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
      cursor: pointer;
    }
    .title-filter {
      font-size: 1.125rem;
      font-weight: 700;
      color: #5f6b73;
      padding-bottom: 6px;
    }
    .form-InFacility {
      > ul {
        > li {
          margin-top: 10px;
        }
      }
      .form-InFacility-label {
        color: #5f6b73;
        margin-bottom: 6px;
      }
    }
    .form-InFacility-check {
      margin-top: -8px;
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -24px;
        li {
          margin-top: 8px;
          padding-left: 24px;
          width: 50%;
          .custom-control,
          .custom-control-description {
            width: 100%;
          }
        }
      }
    }
    .btn-InFacility {
      margin-top: 20px;
      text-align: center;
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
.groupMain {
  height: 100%;
  padding-bottom: 54px;
  display: flex;
  flex-direction: column;
  .inFacility-content {
    position: relative;
    padding: 0 24px 8px;
    max-height: 100%;
    overflow: hidden;
    .application-btn {
      .btn {
        position: absolute;
        top: calc(50% + 10px);
        height: 46px;
        width: 52px;
        padding: 0;
        background: rgba(63, 75, 86, 0.4);
        z-index: 5;
        &.btn--prev {
          left: 495px;
          border-radius: 0px 60px 60px 0px;
        }
        &.btn--next {
          right: 24px;
          border-radius: 60px 0px 0px 60px;
          z-index: 5;
          .svg {
            transform: rotate(-180deg);
          }
        }
      }
    }

    .table-inFacility {
      position: relative;
      z-index: 1;
      .inFacility-sort {
        display: flex;
        justify-content: center;
        align-items: center;
        .inFacility-item {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 24px;
          min-width: 24px;
          height: 24px;
          background: #fff;
          border-radius: 50%;
          margin-left: 10px;
          cursor: pointer;
          box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
          position: relative;
        }
        .icon-sorted {
          background: #448add;
        }

        @media (max-width: '1024px') {
          .inFacility-item.item_segment {
            display: none;
          }
        }
      }
      tr {
        th,
        td {
          padding: 12px 10px;
          vertical-align: middle;
        }
      }
      thead {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        position: -webkit-sticky !important;
        position: -moz-sticky !important;
        position: -o-sticky !important;
        position: -ms-sticky !important;
        position: sticky !important;
        top: 0;
        z-index: 5;
        th {
          color: #fff;
          position: relative;
          white-space: nowrap;
          min-width: 80px;
          font-size: 1rem;
          font-weight: 700;

          .inFacility-label {
            font-size: 14px;
          }
          &:not(:last-child):after {
            content: '';
            width: 1px;
            background-color: #fff;
            height: calc(100% - 8px);
            position: absolute;
            top: 4px;
            right: -1px;
          }
        }
        tr {
          &:first-of-type {
            th {
              &:nth-child(1) {
                width: 230px;
                min-width: 230px;
                max-width: 230px;
                background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                position: -webkit-sticky !important;
                position: -moz-sticky !important;
                position: -o-sticky !important;
                position: -ms-sticky !important;
                position: sticky !important;
                top: 0;
                left: 0;
                z-index: 5;
              }
              &:nth-child(2) {
                text-align: center;
                width: 240px;
                min-width: 240px;
                max-width: 240px;
                background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                position: -webkit-sticky !important;
                position: -moz-sticky !important;
                position: -o-sticky !important;
                position: -ms-sticky !important;
                position: sticky !important;
                top: 0;
                left: 230px;
                z-index: 4;
                &:not(:last-child):after {
                  height: calc(100% - 4px);
                }
              }
              &:nth-child(3) {
                text-align: center;
              }
            }
          }
          &:last-child {
            th {
              &:nth-child(1),
              &:nth-child(2),
              &:nth-child(3) {
                text-align: center;
                width: 80px;
                min-width: 80px;
                max-width: 80px;
                background: #42596f;
                position: -webkit-sticky !important;
                position: -moz-sticky !important;
                position: -o-sticky !important;
                position: -ms-sticky !important;
                position: sticky !important;
                top: 0;
                left: 230px;
                z-index: 4;
              }

              &:nth-child(1),
              &:nth-child(2) {
                &:after {
                  width: 2px;
                  right: -1px;
                }
              }

              &:nth-child(2) {
                left: 310px;
              }

              &:nth-child(3) {
                left: 390px;
                &:not(:last-child):after {
                  height: calc(100% - 4px);
                  top: 0;
                }
              }
              &::before {
                position: absolute;
                top: 0;
                left: 0;
                content: '';
                display: block;
                background: #fff;
                width: 100%;
                height: 1px;
              }
            }
          }
        }
      }
      tbody {
        tr {
          &:hover {
            td {
              &::after {
                border-width: 2px 0 2px 0;
              }
            }
            td:nth-child(1) {
              &:after {
                border-left-width: 2px;
              }
            }
            td:last-child {
              &:after {
                border-right-width: 2px;
              }
            }
          }

          &.inFacility-head {
            td {
              background: #d8f2fe;
              font-size: 1.125rem;
              font-weight: 700;
              &:nth-child(1) {
                position: -webkit-sticky !important;
                position: -moz-sticky !important;
                position: -o-sticky !important;
                position: -ms-sticky !important;
                position: sticky !important;
                left: 0;
                z-index: 1;
                &::before {
                  position: absolute;
                  display: block;
                  background: #d5d5d5;
                  content: '';
                  right: -1px;
                  top: 0;
                  height: 100%;
                  width: 1px;
                }
              }
            }
          }
          &.inFacility-content {
            td {
              text-align: center;
              &:nth-child(1) {
                text-align: left !important;
                position: -webkit-sticky !important;
                position: -moz-sticky !important;
                position: -o-sticky !important;
                position: -ms-sticky !important;
                position: sticky !important;
                top: 0;
                left: 0;
                z-index: 3;
                background: #fff;
                &::before {
                  position: absolute;
                  display: block;
                  background: #d5d5d5;
                  content: '';
                  right: -1px;
                  top: 0;
                  height: 100%;
                  width: 1px;
                }
              }

              &:nth-child(2),
              &:nth-child(3),
              &:nth-child(4) {
                position: -webkit-sticky !important;
                position: -moz-sticky !important;
                position: -o-sticky !important;
                position: -ms-sticky !important;
                position: sticky !important;
                top: 0;
                left: 230px;
                z-index: 3;
                background: #fff;
                &::before {
                  position: absolute;
                  display: block;
                  background: #d5d5d5;
                  content: '';
                  right: -1px;
                  top: 0;
                  height: 100%;
                  width: 1px;
                }
              }

              &:nth-child(3) {
                left: 310px;
              }
              &:nth-child(4) {
                left: 390px;
              }
            }
          }

          td {
            position: relative;
            z-index: 0;
            &:after {
              z-index: -1;
              position: absolute;
              width: 100%;
              height: 100%;
              border-style: solid;
              border-width: 0;
              border-color: #c3c0c0;
              content: '';
              top: 0;
              left: 0;
            }

            &.inFacility-active {
              background: #fff5db !important;
            }
            .inFacility-label {
              .inFacility-link {
                font-size: 1.25rem;
                color: #448add;
                cursor: pointer;
              }
              .inFacility-name {
                vertical-align: text-bottom;
              }
            }
          }
        }
      }
    }
  }
  .paginFooter {
    padding: 4px 24px 12px;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    .paginFooter-btn {
      .btn {
        width: 180px;
        margin-right: 24px;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .paginFooter-number {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      .pagination-cases {
        padding-right: 10px;
        color: #8e8e8e;
        font-weight: 500;
      }
    }

    @media (max-width: 768px) {
      flex-wrap: nowrap;
      .paginFooter-btn {
        .btn {
          width: 140px;
          margin-right: 15px;
        }
      }
    }
  }

  .inFacility-row {
    height: 100%;
    display: flex;
    flex-wrap: wrap;
    .inFacility-left {
      width: 60%;
      padding: 0 22px 0 20px;
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 0 20px;
        height: initial;
      }
    }
    .inFacility-right {
      width: 40%;
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 0 10px 0 10px;
      }
    }
  }
  .inFacilityStock {
    height: 100%;
    overflow: hidden;
    padding-bottom: 8px;
    .inFacilityStock-tbl {
      height: 100%;
      &.datastock-tbl-empty {
        height: calc(100% - 45px);
        tr {
          td {
            border: none;
          }
          &:hover {
            td:after {
              border: none;
            }
          }
        }
      }
      @media (max-width: $viewport_tablet) {
        max-height: 450px;
      }
      tr {
        th,
        td {
          padding: 12px 16px;
          vertical-align: middle;
        }
        .colBtn {
          min-width: 90px !important;
          width: 90px;
        }
      }
      thead {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        position: sticky;
        position: -webkit-sticky;
        top: 0;
        z-index: 5;
        th {
          color: #fff;
          position: relative;
          white-space: nowrap;
          min-width: 70px;
          font-size: 1rem;
          font-weight: 700;
          text-align: center;
          &:not(:last-child):after {
            content: '';
            width: 1px;
            background-color: #fff;
            height: calc(100% - 8px);
            position: absolute;
            top: 4px;
            right: -1px;
          }
        }
        tr {
          &:first-of-type {
            th {
              &:nth-child(1) {
                text-align: left;
                min-width: 230px;
                background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                position: sticky;
                top: 0;
                z-index: 5;
              }
              &:nth-child(3) {
                min-width: 200px;
              }
              &:last-child {
                &::after {
                  content: '';
                  width: 1px;
                  background-color: #fff;
                  height: calc(100% - 8px);
                  position: absolute;
                  top: 4px;
                  left: 0;
                }
              }
            }
          }
          &:last-child {
            th {
              &:nth-child(3) {
                &:not(:last-child):after {
                  height: calc(100% - 4px);
                  top: 0;
                }
              }
              &::before {
                position: absolute;
                top: 0;
                left: 0;
                content: '';
                display: block;
                background: #fff;
                width: 100%;
                height: 1px;
              }
            }
          }
        }
      }
      tbody {
        tr {
          &:hover {
            td {
              &::after {
                border-width: 2px 0 2px 0;
              }
            }
            td:nth-child(1) {
              &:after {
                border-left-width: 2px;
              }
            }
            td:last-child {
              &:after {
                border-right-width: 2px;
              }
            }
          }

          td {
            position: relative;
            z-index: 0;
            &:after {
              z-index: -1;
              position: absolute;
              width: 100%;
              height: 100%;
              border-style: solid;
              border-width: 0;
              border-color: #c3c0c0;
              content: '';
              top: 0;
              left: 0;
            }
          }

          &.noData_table {
            td {
              border: none;
            }
          }
          &.inFacilityStock-head {
            td {
              background: #d8f2fe;
              font-size: 1.125rem;
              font-weight: 700;
            }
          }
          &.inFacilityStock-content {
            td {
              &:nth-child(2),
              &:nth-child(3),
              &:nth-child(4) {
                text-align: center;
              }
              &:nth-child(5) {
                text-align: center;
              }
              .inFacility-label {
                .inFacility-link {
                  font-size: 1.25rem;
                  color: #448add;
                  cursor: pointer;
                }
                .inFacility-name {
                  vertical-align: text-bottom;
                }
              }
              .btn--icon {
                .svg {
                  width: 26px;
                  height: initial;
                }
              }
            }
          }
        }
      }
    }
  }
  .paginStock {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 4px 0 12px;
    .pagination-cases {
      padding-right: 10px;
      color: #8e8e8e;
      font-weight: 500;
    }
  }
  .titleStock {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px 0 32px;
    .titleStock-tlt {
      font-size: 1.375rem;
      font-weight: 500;
    }
    .titleStock-more {
      font-size: 1rem;
      color: #448add;
      cursor: pointer;
    }
  }
  .contentStock {
    height: 100%;
    overflow: hidden;
    padding: 14px 0 54px 10px;
    @media (max-width: $viewport_tablet) {
      padding: 14px 10px 20px;
    }
    .contentStock-box {
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      background: #fff;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 8px 0 0 8px;
      @media (max-width: $viewport_tablet) {
        border-radius: 8px;
      }
    }
    .contentStock-lst {
      height: calc(100% - 175px);
      ul {
        li {
          display: flex;
          justify-content: space-between;
          padding: 10px 16px 10px 22px;
          border-bottom: 1px solid #e3e3e3;
          align-items: center;
          .btn {
            min-width: 42px;
            margin-left: 10px;
          }
          .contentStock-info {
            .contentStock-txt {
              .contentStock-name {
                margin-right: 10px;
                font-weight: bold;
                font-size: 1rem;
              }
              .contentStock-span {
                vertical-align: text-bottom;
                line-height: 27px;
              }
            }
          }
        }
      }
    }
  }
  .contentStock-form {
    background: #f9f9f9;
    box-shadow: 0 -3px 6px rgba(0, 0, 0, 0.1);
    border-radius: 0 0 0 8px;
    position: relative;
    padding: 12px 20px 24px;
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -20px;
      li {
        width: 50%;
        padding-left: 20px;
        .formLabel {
          margin-bottom: 8px;
        }
      }
    }
    .formBtn {
      text-align: center;
      margin-top: 24px;
      .btn {
        width: 150px;
        margin-right: 10px;
        &:last-child {
          margin-right: 0;
        }
      }
    }
  }
}
// custom filter
.dropdown-block {
  box-shadow: 0px 5px 8px 1px #b7c3cb80 !important;
  right: 0;
  position: absolute;
  z-index: 6;
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

.noDataTargetStock {
  height: 100% !important;
  svg {
    width: 150px;
    height: 150px;
  }
}

.hg {
  height: calc(100% - 175px);
}

.text-wrap {
  white-space: pre-wrap;
  overflow: hidden;
  word-break: break-word;
  font-size: 14px;
  display: block;
}
.text-over {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 14px;
  display: block;
}

.productCol {
  min-width: 170px !important;
  max-width: 170px !important;
  z-index: 1;
}

.line-text-over {
  overflow: hidden;
  white-space: normal;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  text-shadow: none;
  text-transform: capitalize;
}
.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}

.postion-re {
  position: relative;
}
.sticky {
  position: -webkit-sticky !important;
  position: -moz-sticky !important;
  position: -o-sticky !important;
  position: -ms-sticky !important;
  position: sticky !important;
  top: 0;
  left: 0;
  z-index: 2;
  background: #fff;
}

.paginFooter {
  padding: 4px 24px 12px;
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  position: fixed;
  bottom: 0;
  width: calc(100% - 60px);
  .paginFooter-btn {
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
  .paginFooter-number {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    .pagination-cases {
      padding-right: 10px;
      color: #8e8e8e;
      font-weight: 500;
    }
  }
}

.slide-left-child {
  animation-name: slideLeftChil;
  animation-direction: alternate;
  animation-iteration-count: 1;
  animation-duration: 0.8s;
  animation-fill-mode: backwards;
}
.slide-right-child {
  animation-name: slideRightChil;
  animation-direction: alternate;
  animation-iteration-count: 1;
  animation-duration: 0.8s;
  animation-fill-mode: backwards;
}

@keyframes slideLeftChil {
  0% {
    transform: translateX(60%);
  }
  100% {
    transform: translateX(0);
  }
}
@keyframes slideRightChil {
  0% {
    transform: translateX(-60%);
  }
  100% {
    transform: translateX(0);
  }
}

@media (hover: hover) {
  .icon-sort:hover {
    background: #448add !important;
    &:after {
      background: url(~@/assets/template/images/icon_sorted.svg) no-repeat center;
      position: absolute;
      content: '';
      width: 100%;
      height: 100%;
      top: 2px;
      left: 0;
    }
  }
}
@media (hover: none) {
  .icon-sort:active {
    background: #ffffff;
  }
}

@media (max-width: 1080px) {
  .btn-hidden-sm {
    display: none;
  }
}
</style>
