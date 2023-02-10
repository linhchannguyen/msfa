<template>
  <div v-loading="loadingPage" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo">
          <button v-if="checkPermission()" type="button" class="btn btn-sign msfa-custom-btn-create" @click="redirectToBriefingInputB01S02()">
            <span class="btn-iconLeft"> <i class="el-icon-plus"></i> <span>新規登録</span> </span>
          </button>
        </div>
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button class="btn btn-filter" type="button" :class="isShowPopupFilter ? 'btn-filter-none-shadow' : ''" @click="openModalFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter dropdown-lecture', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter" @click="onCloseFilter">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div ref="lectureForm_Filter" class="lectureForm scrollbar">
                <ul>
                  <!-- date -->
                  <li class="w-100">
                    <label class="lectureForm-label">開催日</label>
                    <div class="eventDate">
                      <div class="form-icon icon-left form-icon--noBod">
                        <span class="icon">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                        </span>
                        <el-date-picker
                          v-model="formData.start_date"
                          :disabled-date="disabledDateStart"
                          format="YYYY/M/D"
                          value-format="YYYY/M/D"
                          type="date"
                          :editable="false"
                          placeholder="開始日"
                          class="form-control-datePickerLeft"
                        ></el-date-picker>
                      </div>
                      <div class="eventDate-item">～</div>
                      <div class="form-icon icon-left form-icon--noBod">
                        <span class="icon">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                        </span>
                        <el-date-picker
                          v-model="formData.end_date"
                          :disabled-date="disabledDateEnd"
                          format="YYYY/M/D"
                          value-format="YYYY/M/D"
                          type="date"
                          :editable="false"
                          placeholder="終了日"
                          class="form-control-datePickerLeft"
                        ></el-date-picker>
                      </div>
                    </div>
                  </li>

                  <!-- user -->
                  <li class="w-100">
                    <label class="lectureForm-label">実施者</label>
                    <div class="form-icon icon-right">
                      <span class="icon icon--cursor log-icon" title_log="実施者" @click="openModalZ05S01(true)" @touchstart="openModalZ05S01(true)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="formData.lstUser?.length > 0" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag
                            v-for="user in formData.lstUser"
                            :key="user.user_cd"
                            class="el-tag-custom el-tag-icon-top"
                            closable
                            @close="handleRemoveUser(user)"
                          >
                            {{ user.user_name }}
                          </el-tag>
                        </div>
                      </div>

                      <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                    </div>
                  </li>

                  <!-- check flag -->
                  <li class="B01-S01-checkList">
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input
                        class="custom-control-input"
                        type="checkbox"
                        :checked="formData.remand_flag"
                        @click="formData.remand_flag = !formData.remand_flag"
                      />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">差戻しのみ</span>
                    </label>
                  </li>
                  <li class="B01-S01-checkList">
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input
                        class="custom-control-input"
                        type="checkbox"
                        :checked="formData.approval_flag"
                        @click="formData.approval_flag = !formData.approval_flag"
                      />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">承認者が自分</span>
                    </label>
                  </li>

                  <!-- product -->
                  <li class="w-100">
                    <label class="lectureForm-label">品目</label>
                    <div class="form-icon icon-right">
                      <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="formData.lstProduct?.length > 0" class="form-control d-flex align-items-center">
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
                  </li>

                  <!-- org -->
                  <li>
                    <label class="lectureForm-label">対象組織</label>
                    <div class="form-icon icon-right">
                      <span class="icon icon--cursor log-icon" title_log="対象組織" @click="openModalZ05S01(false)" @touchstart="openModalZ05S01(false)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="formData.lstOrg?.length > 0" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag v-for="org in formData.lstOrg" :key="org.org_cd" class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveOrg(org)">
                            {{ org.org_label }}
                          </el-tag>
                        </div>
                      </div>

                      <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                    </div>
                  </li>

                  <!-- facility  -->
                  <li>
                    <label class="lectureForm-label">対象施設</label>
                    <div class="form-icon icon-right">
                      <span class="icon icon--cursor log-icon" title_log="対象施設" @click="openModalZ05S03(false)" @touchstart="openModalZ05S03(false)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="formData.lstFacility.length > 0" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag
                            v-for="facility in formData.lstFacility"
                            :key="facility.facility_cd"
                            class="el-tag-custom el-tag-icon-top"
                            closable
                            @close="handleRemoveFacility(facility)"
                          >
                            {{ facility.facility_short_name }}
                          </el-tag>
                        </div>
                      </div>

                      <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                    </div>
                  </li>

                  <!--  info number -->
                  <li>
                    <label class="lectureForm-label">説明会番号</label>
                    <div class="lectureForm-control" :class="checkValidateForm('briefing_id', 20, '説明会番号') ? 'hasErr' : ''">
                      <el-input
                        v-model="formData.briefing_id"
                        type="number"
                        clearable
                        maxlength="21"
                        placeholder="内容入力"
                        onkeydown="
                          javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                          ? true : !isNaN(Number(event.key)) && event.code!=='Space'
                          "
                        class="form-control-input no-spin"
                      />
                    </div>
                    <span v-if="checkValidateForm('briefing_id', 20, '説明会番号')" class="invalid">
                      {{ validationForm('briefing_id', 20, '説明会番号') }}
                    </span>
                  </li>

                  <!-- briefing type -->
                  <li>
                    <label class="lectureForm-label">説明会区分</label>
                    <div class="lectureForm-control">
                      <el-select v-model="formData.briefing_type" placeholder="未選択" class="form-control-select">
                        <el-option value=""> 未選択</el-option>
                        <el-option
                          v-for="item in lstBriefingType"
                          :key="item.briefing_type_value"
                          :label="item.briefing_type_label"
                          :value="item.briefing_type_value"
                        >
                        </el-option>
                      </el-select>
                    </div>
                  </li>

                  <!-- status -->
                  <li class="w-100 B01-S01-status">
                    <label class="lectureForm-label">ステータス</label>
                    <div class="lectureForm-control">
                      <el-select v-model="formData.status_type" placeholder="未選択" class="form-control-select">
                        <el-option v-for="item in lstStatus" :key="item.status_type" :label="item.status_type_label" :value="item.status_type"> </el-option>
                      </el-select>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="lectureForm-btn">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCloseFilter">キャンセル</button>
                <button type="button" class="btn btn-primary" @click="onFilterData(true, false)">検索</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div ref="briefingContent" class="lectureContent scrollbar">
        <div v-if="lstBriefing.length > 0" class="lectureLst">
          <ul class="scrollbar">
            <li v-for="briefing in lstBriefing" :key="briefing.briefing_id">
              <div class="lectureLst-info">
                <span class="lectureLst-item mr-b10">
                  <img class="pd-r5" src="@/assets/template/images/icon_box_list.svg" alt="" />
                  <a class="lectureLst-title log-icon" title_log="説明会名" @click="redirectToBriefingInputB01S02(briefing)">{{ briefing.briefing_name }}</a>
                </span>

                <span class="lectureLst-dateTime mr-b10">
                  {{ formatFullDate(briefing.start_date) }}{{ formatFullDate(briefing.start_date) ? '(' + getDay(briefing.start_date) + ')' : '' }}　
                  {{ formatTimeHourMinute(briefing.start_time) }}～{{ formatTimeHourMinute(briefing.end_time) }}
                </span>
                <span
                  class="spanLabel mr-b10"
                  :style="{ background: checkColor(briefing.status_type).background, color: checkColor(briefing.status_type).color }"
                >
                  {{ briefing.status_label }}
                </span>
                <span v-if="briefing.remand_flag == 1" class="span-remand mr-b10">
                  <img class="item-close" src="@/assets/template/images/icon_close-circle.svg" alt="" />
                  差戻し
                </span>
              </div>
              <div class="lectureLst-label">
                <div class="lecture-left">
                  <ul>
                    <li>
                      <span class="lectureLst-tlt">品目：</span>
                      <span class="lectureLst-txt">{{ briefing.product_name }}</span>
                    </li>
                    <li>
                      <span class="lectureLst-tlt">対象施設：</span>
                      <span class="lectureLst-txt text-link log-icon" title_log="対象施設" @click="redirectToH01S02(briefing)">{{
                        briefing.facility_short_name
                      }}</span>
                    </li>
                    <li>
                      <span class="lectureLst-tlt">区分：</span>
                      <span class="lectureLst-txt">{{ briefing.briefing_label }}</span>
                    </li>
                  </ul>
                  <ul>
                    <li>
                      <span class="lectureLst-tlt">出席者：</span>
                      <span class="lectureLst-txt">
                        ［計画］
                        {{ +briefing.plan_headcount ? briefing.plan_headcount : '-' }}
                        {{ +briefing.plan_headcount ? '人' : '' }}
                        ／［結果］
                        {{ +briefing.result_headcount ? briefing.result_headcount : '-' }}
                        {{ +briefing.result_headcount ? '人' : '' }}
                      </span>
                    </li>
                    <li>
                      <span class="lectureLst-tlt">経費：</span>
                      <span class="lectureLst-txt">
                        ［計画］{{ +briefing.plan_amount ? '入力あり' : '-' }} ／［結果］{{ +briefing.result_amount ? '入力あり' : '-' }}
                      </span>
                    </li>
                  </ul>
                </div>
                <div class="lecture-right">
                  <div class="lectureLst-info mrt-10">
                    <span class="lectureLst-tlt">実施者：</span>
                    <span class="lectureLst-txt">{{ briefing.implement_user_name }} &ensp; {{ briefing.implement_user_org_label }}</span>
                  </div>
                  <div class="lectureLst-info mrt-10">
                    <span class="lectureLst-tlt">説明会番号：</span>
                    <span class="lectureLst-txt">{{ briefing.briefing_id }}</span>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <EmptyData v-else-if="!isLoadDefault" title="該当するデータがありません。" custom-class="nodata" />
      </div>
    </div>

    <div v-if="lstBriefing.length > 0" class="pagination pagination-custom">
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
      :custom-class="modalConfig.className"
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
/* eslint-disable indent */
/* eslint-disable max-len */
import { markRaw } from 'vue';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';

import B01_S01_BriefingSessionListService from '@/api/B01/B01_S01_BriefingSessionListService';

import { Auth } from '@/api';
import _ from 'lodash';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'B01_S01_BriefingSessionList',
  components: {},
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]

    // REQUIRED from announcement screen Home
    // briefing_id: number

    // **************
    // REQUIRED from alert screen Home
    // remand_flag: 1/0,
    // approval_flag: 1/0

    // **************
    // from other screen: not allow

    // **************
    // examp: this.$router.push({ name: 'B01_S01_BriefingSessionList', query: { briefing_id: briefing_id, remand_flag: 0, approval_flag: 1 } });
  },
  data: function () {
    return {
      loadingPage: false,
      isLoadDefault: true,

      lstStatus: [],
      lstBriefingType: [],

      lstBriefing: [],

      isUser: false,
      userRole: [],
      userLogin: {},

      formData: {
        start_date: '',
        end_date: '',
        org_cd: '',
        product_cd: '',
        briefing_id: '',
        briefing_type: '',
        status_type: '',
        remand_flag: false,
        approval_flag: false,
        facility_cd: '',
        lstUser: [],
        lstOrg: [],
        lstProduct: [],
        lstFacility: []
      },

      paramFilterOld: {},

      modalConfig: {
        title: '',
        className: 'custom-dialog',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      paramsZ05S01: {
        userFlag: 0,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: []
      },

      paramsZ05S06: {
        mode: 'multiple',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },

      paramsZ05S03: {
        selectGroupCd: '',
        userCD: '',
        userName: '',
        facilityCd: [],
        facilityName: []
      },

      isSearch: false,
      onScrollTop: 0
    };
  },
  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'B01_S01_BriefingSearch') {
          this.setRemadAndApprove();
          this.getScreenData();
        }
      }
    );
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '説明会検索',
      icon: 'icon_box_color.svg',
      isShowBack: false
    });

    this.userLogin = this.getCurrentUser();

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.B01_S01_BriefingSessionList || 0;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.B01_S01_BriefingSessionList || 1;

    this.paramsZ05S03 = {
      ...this.paramsZ05S03,
      userCD: this.userLogin?.user_cd,
      userName: this.userLogin?.user_name
    };

    this.setRemadAndApprove();
    this.getScreenData();
  },

  methods: {
    setRemadAndApprove() {
      let queryUrl = this._route('B01_S01_BriefingSearch')?.params;
      if (queryUrl && queryUrl?.briefing_id) {
        this.formData = {
          start_date: '',
          end_date: '',
          org_cd: '',
          product_cd: '',
          briefing_id: queryUrl?.briefing_id,
          briefing_type: '',
          status_type: '',
          remand_flag: false,
          approval_flag: false,
          facility_cd: '',
          lstUser: [],
          lstOrg: [],
          lstProduct: [],
          lstFacility: []
        };
      }

      if (queryUrl && queryUrl?.remand_flag) {
        this.formData = {
          start_date: '',
          end_date: '',
          org_cd: '',
          product_cd: '',
          briefing_id: '',
          briefing_type: '',
          status_type: '',
          remand_flag: queryUrl?.remand_flag == 1 ? true : false,
          approval_flag: false,
          facility_cd: '',
          lstUser: [
            {
              user_cd: this.userLogin?.user_cd,
              user_name: this.userLogin?.user_name,
              org_cd: this.userLogin?.org_cd,
              org_name: this.userLogin?.org_short_name
            }
          ],
          lstOrg: [],
          lstProduct: [],
          lstFacility: []
        };
      }

      if (queryUrl && queryUrl?.approval_flag) {
        this.formData = {
          start_date: '',
          end_date: '',
          org_cd: '',
          product_cd: '',
          briefing_id: '',
          briefing_type: '',
          status_type: '',
          approval_flag: queryUrl?.approval_flag == 1 ? true : false,
          remand_flag: false,
          facility_cd: '',
          lstUser: [],
          lstOrg: [],
          lstProduct: [],
          lstFacility: []
        };
      }

      if (this.formData.lstFacility.length > 0) {
        this.paramsZ05S03 = {
          ...this.paramsZ05S03,
          facilityCd: this.formData.lstFacility.map((x) => x.facility_cd)
        };
      }
    },
    getScreenData() {
      this.loadingPage = true;
      B01_S01_BriefingSessionListService.getScreenData()
        .then(async (res) => {
          let data = res.data.data;
          this.lstBriefingType = data.briefing_type;
          this.lstStatus = data.status_type.map((x) => ({
            ...x,
            selected: x.status_type ? false : true
          }));

          let user_cd = this.userLogin.user_cd;
          const permission = await Auth.getPolicyRoleService({ user_cd });

          const isLoadingComponent = localStorage.getItem('isLoadingComponent');

          let startDate = new Date().setDate(new Date().getDate() - 14);
          let endDate = new Date().setDate(new Date().getDate() + 14);

          if (permission?.data?.data?.includes('R20')) {
            if (isLoadingComponent === 'true') {
              this.formData = {
                start_date: this.formatFullDate(startDate),
                end_date: this.formatFullDate(endDate),
                org_cd: '',
                product_cd: '',
                briefing_id: '',
                briefing_type: '',
                status_type: '',
                remand_flag: false,
                approval_flag: false,
                facility_cd: '',
                lstUser: [
                  {
                    user_cd: this.userLogin?.user_cd,
                    user_name: this.userLogin?.user_name,
                    org_cd: this.userLogin?.org_cd,
                    org_name: this.userLogin?.org_short_name
                  }
                ],
                lstOrg: [],
                lstProduct: [],
                lstFacility: []
              };
              this.setRemadAndApprove();
            }
          } else {
            if (isLoadingComponent === 'true') {
              this.formData = {
                start_date: this.formatFullDate(startDate),
                end_date: this.formatFullDate(endDate),
                org_cd: '',
                product_cd: '',
                briefing_id: '',
                briefing_type: '',
                status_type: '',
                remand_flag: false,
                approval_flag: false,
                facility_cd: '',
                lstUser: [],
                lstOrg: [
                  {
                    org_cd: this.userLogin?.org_cd,
                    org_name: this.userLogin?.org_short_name,
                    org_label: this.userLogin?.org_label
                  }
                ],
                lstProduct: [],
                lstFacility: []
              };
            }
            this.setRemadAndApprove();
          }
          this.onFilterData(false, true);
        })
        .catch((err) => {
          this.loadingPage = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    getDataBriefing(isFilter, isLoadDefault) {
      this.loadingPage = true;
      this.isLoadDefault = isLoadDefault;

      let params = {
        ...this.formData,
        org_cd: this.formData.lstOrg.map((x) => x.org_cd).toString(),
        product_cd: this.formData.lstProduct.map((x) => x.product_cd).toString(),
        user_cd: this.formData.lstUser.map((x) => x.user_cd).toString(),
        facility_cd: this.formData.lstFacility.map((x) => x.facility_cd).toString(),
        remand_flag: this.formData.remand_flag ? 1 : 0,
        approval_flag: this.formData.approval_flag ? 1 : 0,
        lstOrg: null,
        lstProduct: null,
        lstUser: null,
        lstFacility: null,
        offset: this.pagination.curentPage - 1,
        limit: 100
      };

      this.paramFilterOld = { ...this.formData };
      this.isSearch = true;
      B01_S01_BriefingSessionListService.getDataByFilter(params)
        .then((res) => {
          this.isShowPopupFilter = false;
          let data = res.data.data;
          let pageined = res.data?.data?.pagination;
          this.lstBriefing = data.records.map((x) => {
            return {
              ...x,
              product_name: x.briefing_product.map((e) => e.product_name).join(', ')
            };
          });

          this.pagination = {
            ...this.pagination,
            totalItems: pageined.total_items
          };
          this.$router.replace({});
        })
        .catch((err) => {
          this.lstBriefing = [];
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.isSearch = false;
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 500));
          this.loadingPage = false;
          this.isSearch = false;
          this.isLoadDefault = false;

          this.setCurrentPageScreen('B01_S01_BriefingSessionList', this.pagination.curentPage);

          if (this.$refs.briefingContent) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.briefingContent.scrollTop = this.onScrollTop;
            } else {
              this.$refs.briefingContent.scrollTop = 0;
            }
          }

          this.js_pscroll(0.5);

          await localStorage.setItem('isLoadingComponent', false);

          const childRouter = JSON.parse(localStorage.getItem('router'));
          let routes = this.decodeData(localStorage.getItem('$_D')) || [];

          if (routes && routes.length > 0) {
            const indexRouteBriefing = routes.findIndex((x) => x.name === 'B01_S01_BriefingSearch');

            if (indexRouteBriefing > -1) {
              let routeBriefing = {
                ...routes[indexRouteBriefing],
                params: {}
              };

              routes[indexRouteBriefing] = routeBriefing;
              localStorage.setItem('$_D', this.enCodeData(routes));
            }
          } else {
            let routeBriefing = {
              name: 'B01_S01_BriefingSearch',
              params: {},
              path: childRouter.find((x) => x.name === 'B01_S01_BriefingSearch')?.path
            };

            routes[0] = routeBriefing;
            localStorage.setItem('$_D', this.enCodeData(routes));
          }
        });
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
        if (!this.checkValidateForm('briefing_id', 20, '説明会番号')) {
          this.getDataBriefing(isFilter, isLoadDefault);
          if (isFilter) {
            let time;
            const timeout = setTimeout(() => {
              if (time) {
                clearTimeout(timeout);
                return;
              }
              this.$router.push({ name: 'B01_S01_BriefingSearch', params: {} });
              let data = this.decodeData(localStorage.getItem('screen'));
              const index = data && data.length > 0 && data.findIndex((s) => s.screen_name === 'B01_S01_BriefingSessionList');

              if (index > -1) {
                data[index].formData = { ...this.formData };
              }

              localStorage.setItem('screen', this.enCodeData(data));
              time = 1;
            }, 0);
          }
        } else {
          this.loadingPage = false;
          this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        }
      } else {
        this.loadingPage = false;
      }
    },

    checkPermission() {
      const permission = this.$store.state.auth.policyRole?.includes('R20');
      return permission ? true : false;
    },

    handleCurrentChange(val) {
      this.onCloseFilter();
      this.pagination.curentPage = val;
      this.onScrollTop = 0;
      this.getDataBriefing(false, false);
    },

    // modal filter
    openModalFilter() {
      this.isShowPopupFilter = true;
      this.isSearch = false;

      let time;
      const timeout = setTimeout(() => {
        if (time) {
          clearTimeout(timeout);
          return;
        }
        if (this.$refs.lectureForm_Filter) {
          this.$refs.lectureForm_Filter.scrollTop = 1;
        }
        time = 1;
      }, 0);
    },

    onCloseFilter() {
      this.isShowPopupFilter = false;
    },

    disabledDateStart(time) {
      if (this.formData.end_date) {
        return time.getTime() > new Date(this.formData.end_date).getTime();
      }
    },

    disabledDateEnd(time) {
      if (this.formData.start_date) {
        return time.getTime() < new Date(this.formData.start_date).getTime();
      }
    },

    openModalZ05S01(isUser) {
      this.isUser = isUser;
      this.modalConfig = {
        ...this.modalConfig,
        className: `custom-dialog ${isUser ? '' : 'modal-fixed'}`,
        title: this.isUser ? 'ユーザ選択' : '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: {
          ...this.paramsZ05S01,
          userFlag: this.isUser ? 1 : 0,
          mode: 'multiple',
          orgCdList: this.isUser ? this.formData.lstUser.map((x) => x.org_cd) : this.formData.lstOrg.map((x) => x.org_cd),
          userCdList: this.isUser ? this.formData.lstUser : [],
          userSelectFlag: 1
        },
        width: this.isUser ? (this.currDevice() !== 'Desktop' ? '95%' : '65rem') : '45rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (this.isUser) {
        this.formData.lstUser = [...e.userSelected];
      } else {
        this.formData.lstOrg = [...e.orgSelected];
      }
    },

    handleRemoveUser(user) {
      this.formData.lstUser = this.formData.lstUser.filter((x) => x.user_cd !== user.user_cd);
    },

    handleRemoveOrg(org) {
      this.formData.lstOrg = this.formData.lstOrg.filter((x) => x.org_cd !== org.org_cd);
    },

    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        className: 'custom-dialog modal-fixed',
        isShowModal: true,
        component: markRaw(Z05S06MaterialSelection),
        width: '42rem',
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

    // Modal Z05-S03
    openModalZ05S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設選択',
        isShowModal: true,
        className: 'custom-dialog',
        component: markRaw(Z05S03FacilitySelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        props: {
          mode: 'multiple',
          userCD: this.paramsZ05S03.userCD,
          userName: this.paramsZ05S03.userName,
          facilityCd: this.paramsZ05S03.facilityCd,
          facilityName: this.paramsZ05S03.facilityName
        },
        destroyOnClose: true
      };
    },

    onResultModalZ05S03(e) {
      this.formData.lstFacility = e?.facilitySelectList;
      this.paramsZ05S03 = {
        ...e.filterData,
        facilityCd: e?.facilitySelectList.length > 0 ? e?.facilitySelectList.map((x) => x.facility_cd) : [],
        facilityName: [e.filterData.facility_name],
        userCD: '',
        userName: ''
      };
    },

    handleRemoveFacility(facility) {
      this.formData.lstFacility = this.formData.lstFacility.filter((x) => x.facility_cd !== facility.facility_cd);
      if (this.formData.lstFacility.length === 0) {
        this.paramsZ05S03 = {
          selectGroupCd: '',
          userCD: this.userLogin?.user_cd,
          userName: this.userLogin?.user_name,
          facilityCd: [],
          facilityName: []
        };
      } else {
        this.paramsZ05S03 = {
          selectGroupCd: '',
          userCD: '',
          userName: '',
          facilityCd: this.formData.lstFacility.map((x) => x.facility_cd),
          facilityName: []
        };
      }
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },

    validationForm(field, lengthNumber, name) {
      let data = this.formData[field];
      if (data && data.length > lengthNumber) {
        return this.getMessage('MSFA0012', name, lengthNumber);
      }
      return '';
    },

    checkValidateForm(field, lengthNumber, name) {
      if (this.validationForm(field, lengthNumber, name)) {
        return true;
      }
      return false;
    },

    checkColor(status_type) {
      let type = parseInt(status_type);
      let background = '';
      let color = '';
      switch (type) {
        case 10:
        case 40:
          background = '#DAF8DC';
          color = '#28A470';
          break;
        case 20:
        case 50:
          background = '#FFE2BA';
          color = '#FF7347';
          break;
        case 30:
        case 60:
          background = '#D1E4FF';
          color = '#448ADD';
          break;
        case 70:
          background = '#E1E1E1';
          color = '#707274';
          break;

        default:
          background = '';
          color = '';
          break;
      }

      return { background: background, color: color };
    },

    // register
    redirectToBriefingInputB01S02(item) {
      let scrollTop = this.$refs.briefingContent ? this.$refs.briefingContent.scrollTop : 0;
      this.setScrollScreen('B01_S01_BriefingSessionList', scrollTop);
      if (item) {
        let briefing_id = item.briefing_id;
        this.$router.push({ name: 'B01_S02_BriefingSessionInput', params: { briefing_id } });
      } else {
        this.$router.push({ name: 'B01_S02_BriefingSessionInput' });
      }
    },

    redirectToH01S02(item) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd: item.facility_cd
        },
        query: {
          tab: '1'
        }
      });
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
        if (e.screen === 'Z05_S03') {
          this.onResultModalZ05S03(e);
        }
      }
      this.onCloseModal();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.wrapBtn {
  padding: 0 24px 12px;
  .dropdown-lecture {
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
      padding-bottom: 10px;
      padding-left: 20px;
    }
    .lectureForm {
      padding: 0 20px 5px 20px;
      max-height: calc(100vh - 190px);
      .lectureForm-label {
        margin: 0 0 1px;
        color: #5f6b73;
      }
      .eventDate {
        display: flex;
        align-items: center;
        .eventDate-item {
          padding: 0 8px;
          color: #727f88;
          font-size: 1rem;
        }
      }
      > ul {
        margin-left: -10px;
        display: flex;
        flex-wrap: wrap;
        > li {
          width: 50%;
          padding-left: 10px;
          margin-top: 4px;
          .custom-control {
            margin-top: 4px;
            width: 100%;
            .custom-control-description {
              width: 100%;
            }
          }
        }
        ul {
          display: flex;
          flex-wrap: wrap;
          margin-bottom: 8px;

          &:last-child {
            margin-bottom: 0;
          }

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
  }
}
.lectureForm-btn {
  text-align: center;
  padding: 10px;
  margin-top: 10px;
  position: sticky;
  box-shadow: 1px -4px 7px 0px #b7c3cb80 !important;
  .btn {
    width: 142px;
    margin-right: 14px;
    &:last-child {
      margin-right: 0;
    }
  }
}
.lectureContent {
  height: 100%;
  padding: 5px 14px;
  .lectureLst {
    > ul {
      padding: 5px 10px;
      > li {
        margin-bottom: 10px;
        background: #fff;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        border-radius: 4px;
        padding: 24px 36px;
        @media (max-width: $viewport_tablet) {
          padding: 16px;
        }
        &:last-child {
          margin-bottom: 0;
        }
      }
    }
    .lectureLst-info {
      .lectureLst-item {
        margin-right: 7px;
        display: inline-flex;
      }
      .lectureLst-title,
      .lectureLst-dateTime,
      .spanLabel,
      .span-remand {
        margin-right: 20px;
        display: inline-flex;
        @media (max-width: $viewport_tablet) {
          margin-right: 16px;
        }
      }
      .lectureLst-title {
        font-size: 1.375rem;
        font-weight: 500;
        line-height: 1.5;
        color: #448add;
        cursor: pointer;
        overflow-wrap: anywhere;
        word-break: break-word;
      }
      .lectureLst-dateTime {
        font-size: 1rem;
        color: #2d3033;
        margin-top: 7px;
      }
      .spanLabel {
        font-size: 0.875rem;
        border-radius: 2px;
        padding: 2px 16px;
        margin-top: 7px;
        &.spanLabel--green {
          background: #daf8dc;
          color: #28a470;
        }
        &.spanLabel--yellow {
          background: #ffe2ba;
          color: #ff7347;
        }
      }
      .span-remand {
        background: #feddde;
        color: #ea5d54;
        font-size: 0.875rem;
        border-radius: 20px;
        padding: 2px 7px 2px 24px;
        position: relative;
        margin-top: 7px;
        .item-close {
          cursor: pointer;
          position: absolute;
          top: 4px;
          left: 7px;
          width: 16px;
          height: 16px;
        }
      }
    }
    .mrt-10 {
      margin-top: 10px;
    }
    .lectureLst-label {
      display: flex;
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          margin-right: 30px;
          margin-top: 10px;
          font-size: 0.875rem;
          @media (max-width: $viewport_tablet) {
            margin-right: 18px;
          }
          &:last-child {
            margin-right: 10px;
          }
          min-width: 80px;
        }
      }
    }
    .pd-t12 {
      padding-top: 12px;
    }
    .pd-r5 {
      padding-right: 5px;
    }
    .mr-b10 {
      margin-bottom: 10px;
    }
    .lectureLst-txt {
      color: #2d3033;
    }
    .text-link {
      color: #448add;
      cursor: pointer;
    }

    .lecture-left {
      flex-grow: 1;
    }
    .lecture-right {
      flex: 0 0 250px;
      text-align: right;

      .btn {
        height: 32px;
        line-height: 16px;
      }

      .btn .btn-iconLeft {
        margin: 0 4px 0 0;
        line-height: 16px;
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
  padding: 20px 0 0;
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
  font-size: 80%;
  color: #dc3545;
}
.pagination {
  box-shadow: 0px -3px 9px #0000001a;
}
</style>
