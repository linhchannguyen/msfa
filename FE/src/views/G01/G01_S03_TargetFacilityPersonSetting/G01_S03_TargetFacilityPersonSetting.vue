<template>
  <div class="wrapContent">
    <div v-loading="loadingPage" v-load-f5="true" class="groupMain">
      <div class="wrapBtn">
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn">
            <button
              v-if="!showDataStock"
              type="button"
              class="btn btn-stock btn-Wauto"
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
            <button class="btn btn-filter" type="button" :class="isShowPopupFilter ? 'btn-filter-none-shadow' : ''" @click="openModalFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter dropdown-InFacility', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter" @click="onCloseFilter">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="form-InFacility">
                <ul>
                  <li>
                    <label class="form-InFacility-label">施設担当者</label>
                    <div class="form-InFacility-control">
                      <div class="form-icon icon-right">
                        <span class="icon icon--cursor log-icon" title_log="施設担当者選択" @click="openModalZ05S01()" @touchstart="openModalZ05S01()">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                        </span>
                        <div v-if="formData.user_name" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag class="el-tag-custom el-tag-icon-top">
                              {{ formData.user_name }}
                            </el-tag>
                          </div>
                        </div>

                        <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                      </div>
                    </div>
                  </li>

                  <li>
                    <label class="form-InFacility-label">施設区分（大区分）</label>
                    <div class="form-InFacility-control">
                      <el-select v-model="formData.facility_type_group_cd" placeholder="未選択" class="form-control-select">
                        <el-option :value="''">未選択</el-option>
                        <el-option
                          v-for="item in lstFacilityTypeGroup"
                          :key="item.facility_type_group_cd"
                          :label="item.facility_type_group_name"
                          :value="item.facility_type_group_cd"
                        >
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
                  <button type="button" class="btn btn-primary" @click="confirmChange('onFilterData')">検索</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div
        v-if="!showDataStockTimeout"
        class="inFacility-content"
        :class="actionStock && !isLoadDefault ? (!showDataStock ? 'slide-right-child' : ' slide-left-child') : ''"
      >
        <div class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
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
              <tr
                v-for="(person, index) in lstDataTargetPerson"
                v-show="lstDataTargetPerson.length > 0"
                :key="person.person_cd"
                :class="person.isFacility ? 'inFacility-head' : 'inFacility-content'"
              >
                <td v-if="person.isFacility" :colspan="lstSegment?.length + 1">
                  <div id="id_facility">
                    <span>{{ person.facility_short_name }}</span>
                  </div>
                </td>
                <!-- person -->
                <td v-if="!person.isFacility" class="firstCol">
                  <p class="inFacility-title">{{ person.department_name }}</p>
                  <p class="inFacility-label">
                    <a class="inFacility-link log-icon" title_log="個人名" @click="confirmChange('H02S02', person)"> {{ person.person_name }} </a>　
                    <span class="inFacility-name">{{ person.display_position_name }}</span>
                  </p>
                </td>
                <!-- td segment -->
                <td
                  v-for="segment in lstSegment"
                  v-show="!person.isFacility"
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
                  v-show="!person.isFacility"
                  :key="product.target_product_cd"
                  :class="`${changeValue('product', person, index, product, null) ? 'inFacility-active' : ''}`"
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

                <td v-for="product in lstTargetProduct" v-show="person.isFacility" :key="product.target_product_cd">
                  <div class="inFacility-sort">
                    <span class="inFacility-label">{{ getTargetSubFacility(product, person.sub_target) }}</span>
                  </div>
                </td>
                <span class="animation-hover animation-hover-top"></span>
                <span class="animation-hover animation-hover-left"></span>
                <span class="animation-hover animation-hover-bottom"></span>
                <span class="animation-hover animation-hover-right"></span>
              </tr>
            </tbody>
          </table>
          <div v-if="lstDataTargetPerson.length === 0 && !isLoadDefault" class="not-hover">
            <EmptyData />
          </div>
        </div>
      </div>
      <div
        v-if="!showDataStockTimeout"
        class="paginFooter"
        :class="actionStock && !isLoadDefault ? (!showDataStock ? 'slide-right-child' : ' slide-left-child') : ''"
      >
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
            :current-page="pagination.curentPage"
            :pager-counts="currDevice() !== 'Desktop' ? 3 : 7"
            @current-change="handleCurrentChange"
          />
        </div>
      </div>
      <div
        v-if="showDataStockTimeout"
        ref="mainRef"
        class="inFacility-row scrollbar"
        :class="actionStock && !isLoadDefault ? (!showDataStock ? 'slide-right-child-1' : ' slide-left-child1') : ''"
      >
        <div class="inFacility-left">
          <div class="inFacilityStock">
            <div
              ref="inFacilityStock"
              class="inFacilityStock-tbl table-hg100 scrollbar scroll-child"
              :class="lstDataTargetPerson.length === 0 ? 'datastock-tbl-empty' : ''"
              @mouseenter="childFocus"
              @mouseleave="childFocusOut"
              @touchstart="childFocus"
              @touchend="childFocusOut"
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
                  <tr
                    v-for="person in lstDataTargetPerson"
                    v-show="lstDataTargetPerson.length > 0"
                    :key="person.person_cd"
                    :class="person.isFacility ? 'inFacilityStock-head' : 'inFacilityStock-content'"
                  >
                    <!-- facility -->
                    <td v-if="person.isFacility" :colspan="lstSegment?.length + 2">
                      {{ person.facility_short_name }}
                    </td>

                    <!-- person -->

                    <td v-if="!person.isFacility">
                      <p class="inFacility-title">{{ person.department_name }}</p>
                      <p class="inFacility-label">
                        <a class="inFacility-link log-icon" title_log="個人名" @click="confirmChange('H02S02', person)"> {{ person.person_name }} </a>　
                        <span class="inFacility-name">{{ person.display_position_name }}</span>
                      </p>
                    </td>
                    <!-- td segment -->
                    <td v-for="segment in lstSegment" v-show="!person.isFacility" :key="segment.segment_type">
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
                    <td v-if="!person.isFacility">
                      <button v-if="checkDataPersonInStock(person) && person.person_cd" type="button" class="btn btn--icon" @click="removePersonStock(person)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack01.svg')" alt="" />
                      </button>
                      <button v-if="!checkDataPersonInStock(person) && person.person_cd" type="button" class="btn btn--icon" @click="addPersonStock(person)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack.svg')" alt="" />
                      </button>
                    </td>
                    <span class="animation-hover animation-hover-top"></span>
                    <span class="animation-hover animation-hover-left"></span>
                    <span class="animation-hover animation-hover-bottom"></span>
                    <span class="animation-hover animation-hover-right"></span>
                  </tr>
                  <tr v-if="lstDataTargetPerson.length === 0 && !isLoadDefault" class="not-hover">
                    <td :colspan="lstSegment?.length + 2">
                      <EmptyData />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="lstDataTargetPerson.length > 0" class="paginStock">
            <div class="pagination-cases">全 {{ pagination.totalItems }} 件</div>
            <PaginationTable
              :page-size="pagination.pageSize"
              :total="pagination.totalItems"
              :current-page="pagination.curentPage"
              :pager-counts="currDevice() !== 'Desktop' ? 3 : 7"
              @current-change="handleCurrentChange"
            />
          </div>
        </div>

        <div class="inFacility-right">
          <div class="titleStock">
            <h2 class="titleStock-tlt">ストック登録</h2>
            <span class="titleStock-more" @click="confirmCancelStock()">
              閉じる
              <img src="@/assets/template/images/icon_arrow-line02.svg" alt="" />
            </span>
          </div>
          <div class="contentStock">
            <div class="contentStock-box">
              <div
                v-if="lstDataPersonStock.length > 0"
                class="contentStock-lst scrollbar scroll-child"
                @mouseenter="childFocus"
                @mouseleave="childFocusOut"
                @touchstart="childFocus"
                @touchend="childFocusOut"
              >
                <ul>
                  <li v-for="item in lstDataPersonStock" :key="item">
                    <div class="contentStock-info">
                      <p class="contentStock-tlt">
                        <span class="contentStock-span"> {{ item.facility_short_name }}</span>
                        <span class="contentStock-span">{{ item.department_name }}</span>
                      </p>
                      <p class="contentStock-txt">
                        <span class="contentStock-name"> {{ item.person_name }}</span>
                        <span class="contentStock-span">{{ item.display_position_name }}</span>
                      </p>
                    </div>
                    <button type="button" class="btn btn--icon" @click="removePersonStock(item)">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                    </button>
                  </li>
                </ul>
              </div>
              <div v-else class="hg"><EmptyData class="empty-contentStock-form" /></div>
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
      :custom-class="modalConfig.customClass"
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
import G01_S03_Service from '@/api/G01/G01_S03_TargetFacilityPersonSettingService';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import _, { isEqual } from 'lodash';
import { required } from '@/utils/validate';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'G01_S03_TargetFacilityPersonSetting',
  mixins: [filter_popup],
  props: {
    org_cd: {
      type: String,
      default: '',
      require: true
    },
    user_cd: {
      type: String,
      default: '',
      require: true
    },
    user_name: {
      type: String,
      default: '',
      require: true
    },
    facility_type_group_cd: {
      type: String,
      default: '',
      require: true
    },
    person_name: {
      type: String,
      default: '',
      require: true
    },
    target_product_cd: {
      type: String,
      default: '',
      require: true
    },
    segment_type: {
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
      isShowPopupFilter: false,
      actionStock: false,

      showScrollLeft: false,
      showScrollRight: true,

      showDataStock: false,
      showDataStockTimeout: false,
      processingSave: false,
      processingReport: false,

      lstFacilityTypeGroup: [],
      lstTargetProduct: [],
      lstSegment: [],

      dataTargetPerson: {},
      lstDataTargetPerson: [],
      lstDataTargetPersonOld: [],
      oldLstDataTargetPerson: [],
      lstDataFacility: [],
      lstDataPersonDefault: [],

      lstDataChange: [],

      lstDataPersonStock: [],

      lstSegmentTypeChecked: [],

      lstContent: [],

      formData: {
        user_cd: '',
        org_cd: '',
        user_name: '',
        facility_type_group_cd: '',
        person_name: '',
        target_product_cd: '',
        segment_type: ''
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
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },

      pagination: {
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

      paramsZ05S01: {
        userFlag: 1,
        mode: 'single',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
      },

      lstConditionSort: [],
      onScrollTop: 0,
      isDisableTooltipProduct: false,
      messageRequired: ''
    };
  },
  computed: {
    validation() {
      return {
        person_cd: { required: required(this.lstDataPersonStock) },
        product_cd: { required: required(this.paramStock.product_cd) },
        content_cd: { required: required(this.paramStock.content_cd) }
      };
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'ターゲット施設個人設定',
      icon: 'icon_target-facility.svg',
      isShowBack: false
    });

    this.router = this._route('G01_S03_TargetFacilityPersonSetting')?.params;

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.G01_S03_TargetFacilityPersonSetting || 0;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.G01_S03_TargetFacilityPersonSetting || 1;

    this.getScreenData();
    this.getDataContent();
    this.js_pscroll(0.5);
  },
  methods: {
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

    setParams() {
      if (this.router?.org_cd) {
        this.formData.org_cd = this.router?.org_cd;
      }
      if (this.router?.user_name) {
        this.formData.user_name = this.router?.user_name;
      }
      if (this.router?.user_cd) {
        this.formData.user_cd = this.router?.user_cd;
      }

      if (this.router?.facility_type_group_cd) {
        this.formData.facility_type_group_cd = this.router?.facility_type_group_cd;
      }
      if (this.router?.person_name) {
        this.formData.person_name = this.router?.person_name;
      }
      if (this.router?.target_product_cd) {
        this.formData.target_product_cd = this.router?.target_product_cd;
      }
      if (this.router?.segment_type) {
        this.formData.segment_type = this.router?.segment_type;
      }
    },
    getTargetSubFacility(product, lstTargetSub) {
      let item = lstTargetSub.find((x) => x.target_product_cd === product.target_product_cd);
      return item ? item.sub_target : '';
    },

    getScreenData() {
      this.loadingPage = true;
      G01_S03_Service.getScreenDataService()
        .then(async (res) => {
          let data = res.data.data;

          const isLoadingComponent = localStorage.getItem('isLoadingComponent');
          if (isLoadingComponent === 'true') {
            let currentUser = this.getCurrentUser();
            this.formData = {
              ...this.formData,
              org_cd: currentUser.org_cd,
              org_name: currentUser.org_short_name,
              user_cd: currentUser.user_cd,
              user_name: currentUser.user_name
            };
          }
          this.setParams();

          this.lstFacilityTypeGroup = data.facilityTypeGroup || [];
          this.lstTargetProduct = data.targetProduct || [];
          this.lstSegment = data.segmentType || [];
          this.paramStock.stock_type = data?.parameterAddStock?.definition_value || '';

          this.paramStockOld = { ...this.paramStock };

          this.onFilterData(false, true);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.loadingPage = false;
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

    getDatafacility() {
      for (let i = 0; i < this.lstDataPersonDefault.length; i++) {
        let element = this.lstDataPersonDefault[i];
        if (!this.lstDataFacility.some((x) => x.facility_cd === element.facility_cd)) {
          this.lstDataFacility.push({
            facility_cd: element.facility_cd,
            facility_name: element.facility_name,
            facility_short_name: element.facility_short_name,
            sub_target: element.sub_target,
            isFacility: true
          });
        }
      }
    },

    convertDataPerson() {
      for (let i = 0; i < this.lstDataPersonDefault.length; i++) {
        let person1 = this.lstDataPersonDefault[i];
        let facilityTemp = this.lstDataFacility.find((x) => x.facility_cd === person1.facility_cd);
        if (i === 0) {
          this.lstDataTargetPerson.push(facilityTemp);
        } else {
          let personOld = this.lstDataPersonDefault[i - 1];
          if (person1.facility_cd !== personOld.facility_cd) {
            this.lstDataTargetPerson.push(facilityTemp);
          }
        }
        this.lstDataTargetPerson.push(person1);

        this.lstDataTargetPersonOld = JSON.parse(JSON.stringify(this.lstDataTargetPerson));
      }
    },

    getDataTargetFacilityPersonManagement(isFilter, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.loadingPage = true;
      this.isShowPopupFilter = false;
      let params = {
        ...this.formData,
        segment_type: this.lstSegmentTypeChecked?.length > 0 ? this.lstSegmentTypeChecked.toString() : '',
        sort_order: [...this.lstConditionSort],
        offset: this.pagination.curentPage - 1,
        limit: 100
      };
      this.paramFilterOld = { ...this.formData };
      G01_S03_Service.targetFacilityPersonManagementService(params)
        .then((res) => {
          this.lstDataPersonDefault = [];
          this.lstDataTargetPerson = [];
          let data = res.data.data;
          this.lstDataPersonDefault = [...data.records];

          let pageined = res.data?.data?.pagination;
          this.pagination = {
            ...this.pagination,
            totalItems: pageined.total_items
          };

          this.getDatafacility();
          this.convertDataPerson();

          this.$router.replace({});
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.isSearch = false;
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          this.isLoadDefault = false;
          this.loadingPage = false;
          this.isSearch = false;
          this.actionStock = false;

          this.setCurrentPageScreen('G01_S03_TargetFacilityPersonSetting', this.pagination.curentPage);

          if (this.$refs.inFacilityContent) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.inFacilityContent.scrollTop = this.onScrollTop;
            } else {
              this.$refs.inFacilityContent.scrollTop = 0;
            }
          }

          if (this.$refs.inFacilityStock) {
            this.$refs.inFacilityStock.scrollTop = 0;
          }
          this.js_pscroll(0.5);

          await localStorage.setItem('isLoadingComponent', false);

          const childRouter = JSON.parse(localStorage.getItem('router'));
          let routes = this.decodeData(localStorage.getItem('$_D')) || [];

          if (routes && routes.length > 0) {
            const indexRouteTargetFacilityPerson = routes.findIndex((x) => x.name === 'G01_S03_TargetFacilityPersonSetting');

            if (indexRouteTargetFacilityPerson > -1) {
              let routeTargetFacilityPerson = {
                ...routes[indexRouteTargetFacilityPerson],
                params: {}
              };

              routes[indexRouteTargetFacilityPerson] = routeTargetFacilityPerson;
              localStorage.setItem('$_D', this.enCodeData(routes));
            }
          } else {
            let routeTargetFacilityPerson = {
              name: 'G01_S03_TargetFacilityPersonSetting',
              params: {},
              path: childRouter.find((x) => x.name === 'G01_S03_TargetFacilityPersonSetting')?.path
            };

            routes[0] = routeTargetFacilityPerson;
            localStorage.setItem('$_D', this.enCodeData(routes));
          }
        });
    },

    saveTargetPerson() {
      this.loadingPage = true;
      this.processingSave = true;
      let params = {
        records: [...this.lstDataChange]
      };
      G01_S03_Service.saveTargetFacilityPersonService(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.getDataTargetFacilityPersonManagement(false, false);
        })
        .catch((err) => {
          this.loadingPage = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.processingSave = false;
        });
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

        this.getDataTargetFacilityPersonManagement(true, false);
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
          this.getDataTargetFacilityPersonManagement(true, false);
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

    checkItemProductOver(id) {
      let idItem = document.getElementById(id);
      this.isDisableTooltipProduct = true;

      if (idItem?.scrollWidth > 114) {
        this.isDisableTooltipProduct = false;
      }
    },

    handleCurrentChange(val) {
      if (!isEqual(this.lstDataTargetPerson, this.lstDataTargetPersonOld) || this.lstDataPersonStock.length > 0) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1, params: { type: 'change_page', page: val } },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.changePage(val);
      }
    },

    changePage(page) {
      this.lstDataChange = [];
      this.onCloseFilter();
      this.pagination.curentPage = page;
      this.onScrollTop = 0;
      this.getDataTargetFacilityPersonManagement(true, false);
    },

    onScrollLeft() {
      let content = document.querySelector('.table-inFacility');
      content.scrollLeft -= 180;
    },

    onScrollRight() {
      let content = document.querySelector('.table-inFacility');
      content.scrollLeft += 180;
    },

    onScroll: _.debounce(function ({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }
    }, 300),

    setScrollTop() {
      let scrollTop = this.$refs.inFacilityContent ? this.$refs.inFacilityContent.scrollTop : 0;
      this.setScrollScreen('G01_S03_TargetFacilityPersonSetting', scrollTop);
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
      this.lstDataChange = [];
      if (isFilter) {
        this.pagination = {
          ...this.pagination,
          curentPage: 1,
          totalItems: 0
        };
      }
      if (!this.isSearch) {
        this.getDataTargetFacilityPersonManagement(isFilter, isLoadDefault);
      } else {
        this.actionStock = false;
        this.loadingPage = false;
      }
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

      let indexChange = this.lstDataChange.findIndex((x) => x.person_cd === person.person_cd);
      if (indexChange > -1) {
        this.lstDataChange.splice(indexChange, 1, person);
      } else {
        this.lstDataChange.push(person);
      }
    },

    checkedProduct(target_product, index) {
      let person = this.lstDataTargetPerson[index];
      if (person.target_product_list.some((x) => x.target_product_cd === target_product.target_product_cd)) {
        person.target_product_list = person.target_product_list.filter((x) => x.target_product_cd !== target_product.target_product_cd);
      } else {
        person.target_product_list.push(target_product);
      }

      this.lstDataTargetPerson[index] = { ...person };

      let indexChange = this.lstDataChange.findIndex((x) => x.person_cd === person.person_cd);

      if (indexChange > -1) {
        this.lstDataChange.splice(indexChange, 1, person);
      } else {
        this.lstDataChange.push(person);
      }
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
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
        this.onCloseFilter();
      } else {
        switch (type) {
          case 'H02S02':
            this.redirectToH02S02(data);
            break;
          case 'openStock':
            this.openStock();
            break;
          case 'onFilterData':
            this.onFilterData(true, false);
            break;

          default:
            break;
        }
      }
    },

    closeConfirm(data) {
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
        case 'change_page':
          this.changePage(data.page);
          break;
        case 'onFilterData':
          this.onFilterData(true, false);
          break;

        default:
          break;
      }
      this.onCloseModal();
    },

    redirectToH02S02(item) {
      this.setScrollTop();
      let person_cd = item.person_cd;
      localStorage.setItem('activeMenu', 'G01_S03_TargetFacilityPersonSetting');
      this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
    },

    // Stock
    openStock() {
      this.actionStock = true;
      this.loadingPage = true;
      this.messageRequired = '';
      if (!this.showDataStock) {
        this.showDataStock = true;
        this.onFilterData(true, false);
        setTimeout(() => {
          this.showDataStockTimeout = true;
        }, 80);
      } else {
        this.loadingPage = false;
      }
    },

    closeStock() {
      this.actionStock = true;
      this.messageRequired = '';
      if (this.showDataStock) {
        this.loadingPage = true;
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
        this.onFilterData(false, false);

        setTimeout(() => {
          this.showDataStockTimeout = false;
        }, 80);
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
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      }
    },

    checkDataPersonInStock(item) {
      let checked = this.lstDataPersonStock.some((x) => x.person_cd === item.person_cd && x.facility_cd === item.facility_cd);

      return checked;
    },

    addPersonStock(item) {
      if (!this.checkDataPersonInStock(item)) {
        this.lstDataPersonStock.push(item);
      }
    },

    removePersonStock(item) {
      if (this.checkDataPersonInStock(item)) {
        let index = this.lstDataPersonStock.findIndex((x) => x.person_cd === item.person_cd && x.facility_cd === item.facility_cd);
        this.lstDataPersonStock.splice(index, 1);
      }
    },

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

    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: {
          ...this.paramsZ05S01,
          orgCdList: this.formData.org_cd ? [this.formData.org_cd] : [],
          userCdList: this.formData.user_cd
            ? [
                {
                  org_cd: this.formData.org_cd,
                  user_cd: this.formData.user_cd
                }
              ]
            : []
        },
        width: '45rem',
        customClass: 'custom-dialog modal-fixed',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (e) {
        this.paramsZ05S01.orgCdList = [];
        this.paramsZ05S01.userCdList = [];
        e.userSelected?.forEach((x) => {
          this.paramsZ05S01.orgCdList.push(x.org_cd);
          this.paramsZ05S01.userCdList.push({
            org_cd: x.org_cd,
            user_cd: x.user_cd
          });
        });

        this.formData.org_cd = e.userSelected[0]?.org_cd;
        this.formData.user_cd = e.userSelected[0]?.user_cd;
        this.formData.user_name = e.userSelected[0]?.user_name;
      }
    },

    handleRemoveUser() {
      this.formData.user_cd = '';
      this.formData.org_cd = '';
      this.formData.user_name = '';
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
      G01_S03_Service.exportFile(params)
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
        if (e.screen === 'Z05_S01') {
          this.onResultModalZ05S01(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowClose: true, customClass: 'custom-dialog' };
    },

    childFocusOut() {
      const elMain = this.$refs.mainRef;
      if (elMain && elMain?.classList) {
        elMain?.classList?.remove('stop-scroll');
      }
    },
    childFocus() {
      const els = document.querySelectorAll('.scroll-child');
      const elMain = this.$refs.mainRef;

      els.forEach(() => {
        if (elMain && elMain?.classList) {
          elMain.classList.add('stop-scroll');
        }
        return;
      });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.wrapBtn {
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
  position: relative;
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
        z-index: 9;
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
            td.firstCol {
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
            td {
              text-align: center;
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
    overflow: hidden;
    margin-bottom: -54px;
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
          min-width: 70px !important;
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

          &.inFacilityStock-head,
          &.inFacilityStock-footer {
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
                  margin-right: 10px;
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
          padding: 10px 16px 5px 22px;
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

.btn-stock {
  background: #fff;
  height: 42px;
  color: #448add;
  font-size: 15px;
  font-weight: 700;
  box-shadow: 0px 2px 4px rgba(13, 94, 153, 0.1), 0.5px 1px 5px 1px rgba(203, 225, 241, 0.3);
  border-radius: 0px 0px 8px 8px;
  &:hover {
    background: #448add;
    color: #fff;
    .btn-iconLeft {
      stroke: #ffffff;
      .svg {
        fill: #ffffff;
      }
    }
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
.first-child {
  border-color: transparent;
  background: transparent !important;
  &:before {
    width: 100%;
    height: 1px;
    bottom: 0;
    left: 0;
    transform: translateY(50%);
    background-color: #d5d5d5 !important;
  }
  &:after {
    width: 1px;
    height: 100%;
    top: 0;
    right: 0 !important;
    background-color: transparent !important;
  }
}
.flex-column {
  display: flex;
  flex-direction: column;
  justify-content: center;
  p {
    display: flex;
    align-items: center;
  }
}

.border-hover {
  td {
    &:first-child,
    &.firstCol {
      &:before {
        position: absolute;
        top: 0;
        left: -1px !important;
        height: 100% !important;
        width: 2px !important;
        background-color: #c3c0c0 !important;
        content: '';
      }
    }
    &:last-child {
      &:after {
        position: absolute;
        top: 0;
        right: 0;
        height: 100% !important;
        width: 2px !important;
        background-color: #c3c0c0 !important;
        content: '';
      }
    }
  }
  &:before,
  &:after {
    position: absolute;
    left: 0;
    width: 100%;
    height: 2px;
    content: '';
    background-color: #c3c0c0;
    z-index: 3;
  }
  &:before {
    top: 0;
  }
  &:after {
    bottom: 0;
  }
}

.inFacility-row {
  overflow: hidden !important;
}

.slide-left-child {
  animation-name: slideLeftChil;
  animation-direction: alternate;
  animation-iteration-count: 1;
  animation-duration: 0.8s;
  animation-fill-mode: backwards;
}
.slide-left-child1 {
  animation-name: slideLeftChil1;
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
.slide-right-child-1 {
  animation-name: slideRightChil1;
  animation-direction: alternate;
  animation-iteration-count: 1;
  animation-duration: 0.8s;
  animation-fill-mode: backwards;
}

@keyframes slideLeftChil {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(-100%);
  }
}

@keyframes slideLeftChil1 {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

@keyframes slideRightChil {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(0);
  }
}
@keyframes slideRightChil1 {
  from {
    transform: translateX(0%);
  }
  to {
    transform: translateX(100%);
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
