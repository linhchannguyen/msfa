<template>
  <div v-loading="loadingPage" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo">
          <button v-if="roleUser === 'R30'" type="button" class="btn btn-sign msfa-custom-btn-create" @click="redirectToLectureInputC01S02()">
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

                  <!-- org -->
                  <li>
                    <label class="lectureForm-label">対象組織</label>
                    <div class="form-icon icon-right">
                      <span class="icon icon--cursor log-icon" title_log="対象組織" @click="openModalZ05S01(false)" @touchstart="openModalZ05S01(false)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="formData.lstOrg.length > 0" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag v-for="org in formData.lstOrg" :key="org.org_cd" class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveOrg(org)">
                            {{ org.org_label }}
                          </el-tag>
                        </div>
                      </div>

                      <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                    </div>
                  </li>

                  <!-- org plan -->
                  <li>
                    <label class="lectureForm-label">企画組織</label>
                    <div class="form-icon icon-right">
                      <span class="icon icon--cursor log-icon" title_log="企画組織" @click="openModalZ05S01(true)" @touchstart="openModalZ05S01(true)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="formData.lstOrgPlan.length > 0" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag
                            v-for="org in formData.lstOrgPlan"
                            :key="org.org_cd"
                            class="el-tag-custom el-tag-icon-top"
                            closable
                            @close="handleRemoveOrgPlan(org)"
                          >
                            {{ org.org_label }}
                          </el-tag>
                        </div>
                      </div>

                      <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                    </div>
                  </li>

                  <!-- check flag -->
                  <li class="form-flag-checkbox">
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
                  <li class="form-flag-checkbox">
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input
                        class="custom-control-input"
                        type="checkbox"
                        :checked="formData.approved_flag"
                        @click="formData.approved_flag = !formData.approved_flag"
                      />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">承認者が自分</span>
                    </label>
                  </li>

                  <!-- product -->
                  <li>
                    <label class="lectureForm-label">品目</label>
                    <div class="form-icon icon-right">
                      <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
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
                  </li>

                  <!-- convention_type -->
                  <li>
                    <label class="lectureForm-label">講演会区分</label>
                    <div class="lectureForm-control">
                      <el-select v-model="formData.convention_type" placeholder="未選択" class="form-control-select">
                        <el-option value=""> 未選択</el-option>
                        <el-option v-for="item in lstLectureType" :key="item.convention_type" :label="item.convention_type_label" :value="item.convention_type">
                        </el-option>
                      </el-select>
                    </div>
                  </li>

                  <!-- Lecture name -->
                  <li>
                    <label class="lectureForm-label">講演会名</label>
                    <div class="lectureForm-control" :class="checkValidateForm('convention_name', 100, '講演会名') ? 'hasErr' : ''">
                      <el-input v-model="formData.convention_name" clearable maxlength="101" placeholder="内容入力" class="form-control-input" />
                    </div>
                    <span v-if="checkValidateForm('convention_name', 100, '講演会名')" class="invalid">
                      {{ validationForm('convention_name', 100, '講演会名') }}
                    </span>
                  </li>

                  <!-- Lecture number -->
                  <li>
                    <label class="lectureForm-label">講演会番号</label>
                    <div class="lectureForm-control" :class="checkValidateForm('convention_id', 20, '講演会番号') ? 'hasErr' : ''">
                      <el-input
                        v-model="formData.convention_id"
                        type="number"
                        clearable
                        maxlength="21"
                        onkeydown="
                        javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                        ? true : !isNaN(Number(event.key)) && event.code!=='Space'
                        "
                        placeholder="番号入力"
                        class="form-control-input no-spin"
                      />
                    </div>
                    <span v-if="checkValidateForm('convention_id', 20, '講演会番号')" class="invalid">
                      {{ validationForm('convention_id', 20, '講演会番号') }}
                    </span>
                  </li>

                  <!-- status -->
                  <li class="w-100 form-swicth-status">
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
      <div ref="lectureContent" class="lectureContent scrollbar">
        <div v-if="lstLecture.length > 0" class="lectureLst">
          <ul class="scrollbar">
            <li v-for="(lecture, i) in lstLecture" :key="lecture.convention_id" :scroll-attr="i">
              <div class="lecture-left">
                <div class="lectureLst-info">
                  <span class="lectureLst-item mr-b10">
                    <img class="pd-r5" src="@/assets/template/images/icon_menu-01.svg" alt="" />
                    <a class="lectureLst-title log-icon" title_log="講演会名" @click="redirectToLectureInputC01S02(lecture)">{{ lecture.convention_name }}</a>
                  </span>

                  <span class="lectureLst-dateTime mr-b10">
                    {{ formatFullDate(lecture.start_date) }}{{ formatFullDate(lecture.start_date) ? '(' + getDay(lecture.start_date) + ')' : '' }}　
                    {{ formatTimeHourMinute(lecture.start_time) }}{{ formatTimeHourMinute(lecture.start_time) ? '～' : ''
                    }}{{ formatTimeHourMinute(lecture.end_time) }}
                  </span>
                  <span
                    class="spanLabel mr-b10"
                    :style="{ background: checkColor(lecture.status_type).background, color: checkColor(lecture.status_type).color }"
                  >
                    {{ lecture.status_type_label }}
                  </span>
                  <span v-if="lecture.remand_flag == 1" class="span-remand mr-b10">
                    <img class="item-close" src="@/assets/template/images/icon_close-circle.svg" alt="" />
                    差戻し
                  </span>
                </div>
                <div class="lectureLst-label">
                  <ul>
                    <li>
                      <span class="lectureLst-tlt">対象組織：</span>
                      <span class="lectureLst-txt">{{ lecture.org_label }}</span>
                    </li>
                    <li>
                      <span class="lectureLst-tlt">企画組織：</span>
                      <span class="lectureLst-txt">{{ lecture.plan_org_label }}</span>
                    </li>
                    <li>
                      <span class="lectureLst-tlt">品目：</span>
                      <span class="lectureLst-txt">{{ lecture.product_name }}</span>
                    </li>
                    <li>
                      <span class="lectureLst-tlt">講演会区分：</span>
                      <span class="lectureLst-txt">{{ lecture.convention_type }}</span>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="lecture-right">
                <div class="lectureLst-info">
                  <span class="lectureLst-tlt">講演会番号：</span>
                  <span class="lectureLst-txt">{{ lecture.convention_id }}</span>
                </div>
                <div class="lectureLst-label pd-t12">
                  <button
                    type="button"
                    class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                    @click="redirectToAttendantManagementC01S03(lecture)"
                  >
                    <span class="btn-iconLeft">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_users.svg')" alt="" />
                    </span>
                    出席者一覧
                  </button>
                  <button
                    v-if="roleUser === 'R30'"
                    type="button"
                    class="btn btn-outline-primary btn-outline-primary--cancel btn-radius ml-2"
                    @click="redirectToLectureInputC01S02(lecture, true)"
                  >
                    <span class="btn-iconLeft">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_copy.svg')" alt="" />
                    </span>
                    コピー
                  </button>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <EmptyData v-else-if="!isLoadDefault" title="該当するデータがありません。" custom-class="nodata" />
      </div>
    </div>

    <div v-if="lstLecture.length > 0" class="pagination pagination-custom">
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
      custom-class="custom-dialog modal-fixed"
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
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import C01_S01_LectureListService from '@/api/C01/C01_S01_LectureListService';
import _ from 'lodash';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'C01_S01_LectureList',
  components: {},
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]

    // REQUIRED from announcement screen Home
    // convention_id: number

    // **************
    // REQUIRED from alert screen Home
    // remand_flag: 1/0,    // 1: true, 0: false
    // approved_flag: 1/0   // 1: true, 0: false

    // **************
    // from other screen: not allow

    // **************
    // examp: this.$router.push({ name: 'C01_S01_LectureList', query: { convention_id: convention_id, remand_flag: 0, approved_flag: 1 } });
  },
  data: function () {
    return {
      loadingPage: false,
      isLoadDefault: true,
      // isShowPopupFilter: false,

      lstStatus: [],
      lstLectureType: [],

      lstLecture: [],

      isOrgPlan: false,

      formData: {
        start_date: '',
        end_date: '',
        plan_org_cd: '',
        org_cd: '',
        product_cd: '',
        convention_name: '',
        convention_id: null,
        convention_type: '',
        status_type: '',
        remand_flag: false,
        approved_flag: false,
        lstOrg: [],
        lstOrgPlan: [],
        lstProduct: []
      },

      paramFilterOld: {},

      modalConfig: {
        title: '',
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

      isSearch: false,
      onScrollTop: 0
    };
  },

  computed: {
    roleUser() {
      return this.$store.state.auth.policyRole?.includes('R30') ? 'R30' : '';
    }
  },

  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'C01_S01_LectureList') {
          this.setRemadAndApprove();
          this.getScreenData();
        }
      }
    );
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: '講演会検索',
      icon: 'icon_lecture_color.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.C01_S01_LectureList || 0;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.C01_S01_LectureList || 1;

    this.setRemadAndApprove();
    this.getScreenData();
  },

  methods: {
    setScrollTop() {
      let scrollTop = 0;
      if (this.$refs.lectureContent) {
        scrollTop = this.$refs.lectureContent.scrollTop;
      }
      this.setScrollScreen('C01_S01_LectureList', scrollTop);
    },

    setRemadAndApprove() {
      let queryUrl = this._route('C01_S01_LectureList')?.params;

      if (queryUrl && queryUrl?.convention_id) {
        this.formData = {
          start_date: '',
          end_date: '',
          plan_org_cd: '',
          org_cd: '',
          product_cd: '',
          convention_name: '',
          convention_id: queryUrl?.convention_id,
          convention_type: '',
          status_type: '',
          remand_flag: false,
          approved_flag: false,
          lstOrg: [],
          lstOrgPlan: [],
          lstProduct: []
        };
      }

      if (queryUrl && queryUrl?.remand_flag) {
        this.formData = {
          start_date: '',
          end_date: '',
          plan_org_cd: '',
          org_cd: '',
          product_cd: '',
          convention_name: '',
          convention_id: null,
          convention_type: '',
          status_type: '',
          remand_flag: queryUrl?.remand_flag == 1 ? true : false,
          approved_flag: false,
          lstOrg: [],
          lstOrgPlan: [],
          lstProduct: []
        };
      }

      if (queryUrl && queryUrl?.approved_flag) {
        this.formData = {
          start_date: '',
          end_date: '',
          plan_org_cd: '',
          org_cd: '',
          product_cd: '',
          convention_name: '',
          convention_id: null,
          convention_type: '',
          status_type: '',
          remand_flag: false,
          approved_flag: queryUrl?.approved_flag == 1 ? true : false,
          lstOrg: [],
          lstOrgPlan: [],
          lstProduct: []
        };
      }
    },
    getScreenData() {
      this.loadingPage = true;
      C01_S01_LectureListService.getScreenData()
        .then((res) => {
          let data = res.data.data;
          this.lstLectureType = data.convention_type;
          this.lstStatus = data.status_type.map((x) => ({
            ...x,
            selected: x.status_type ? false : true
          }));

          let org = this.getCurrentUser();

          const isLoadingComponent = localStorage.getItem('isLoadingComponent');

          if (isLoadingComponent === 'true') {
            this.formData = {
              start_date: '',
              end_date: '',
              plan_org_cd: '',
              org_cd: '',
              product_cd: '',
              convention_name: '',
              convention_id: null,
              convention_type: '',
              status_type: '',
              remand_flag: false,
              approved_flag: false,
              lstOrg: [
                {
                  org_cd: org.org_cd,
                  org_name: org.org_short_name,
                  org_label: org.org_label
                }
              ],
              lstOrgPlan: [],
              lstProduct: []
            };

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

    getDataLecture(isFilter, isLoadDefault) {
      this.loadingPage = true;
      this.isLoadDefault = isLoadDefault;

      let params = {
        ...this.formData,
        plan_org_cd: this.formData.lstOrgPlan.length > 0 ? this.formData.lstOrgPlan[0].org_cd : '',
        org_cd: this.formData.lstOrg.map((x) => x.org_cd).toString(),
        product_cd: this.formData.lstProduct.map((x) => x.product_cd).toString(),
        remand_flag: this.formData.remand_flag ? 1 : 0,
        approved_flag: this.formData.approved_flag ? 1 : 0,
        lstOrg: null,
        lstOrgPlan: null,
        lstProduct: null,
        offset: this.pagination.curentPage - 1,
        limit: 100
      };
      this.paramFilterOld = { ...this.formData };
      this.isSearch = true;
      C01_S01_LectureListService.getDataByFilter(params)
        .then((res) => {
          this.isShowPopupFilter = false;
          let data = res.data.data;
          let pageined = res.data?.data?.pagination;
          this.lstLecture = data.records;
          this.pagination = {
            ...this.pagination,
            totalItems: pageined.total_items
          };
          this.$router.replace({});
        })
        .catch((err) => {
          this.lstLecture = [];
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.isSearch = false;
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 500));

          this.setCurrentPageScreen('C01_S01_LectureList', this.pagination.curentPage);

          if (this.$refs.lectureContent) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.lectureContent.scrollTop = this.onScrollTop;
            } else {
              this.$refs.lectureContent.scrollTop = 0;
            }
          }
          this.js_pscroll(0.5);
          localStorage.setItem('isLoadingComponent', false);

          const childRouter = JSON.parse(localStorage.getItem('router'));
          let routes = this.decodeData(localStorage.getItem('$_D')) || [];

          if (routes && routes.length > 0) {
            const indexRouteConvention = routes.findIndex((x) => x.name === 'C01_S01_LectureList');

            if (indexRouteConvention > -1) {
              let routeConvention = {
                ...routes[indexRouteConvention],
                params: {}
              };

              routes[indexRouteConvention] = routeConvention;
              localStorage.setItem('$_D', this.enCodeData(routes));
            }
          } else {
            let routeConvention = {
              name: 'C01_S01_LectureList',
              params: {},
              path: childRouter.find((x) => x.name === 'C01_S01_LectureList')?.path
            };

            routes[0] = routeConvention;
            localStorage.setItem('$_D', this.enCodeData(routes));
          }

          this.loadingPage = false;
          this.isSearch = false;
          this.isLoadDefault = false;
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
        if (!this.checkValidateForm('convention_id', 20, '講演会番号') && !this.checkValidateForm('convention_name', 100, '講演会名')) {
          this.getDataLecture(isFilter, isLoadDefault);
          if (isFilter) {
            let time;
            const timeout = setTimeout(() => {
              if (time) {
                clearTimeout(timeout);
                return;
              }
              this.$router.push({ name: 'C01_S01_LectureList', params: {} });

              let data = this.decodeData(localStorage.getItem('screen'));
              const index = data && data.length > 0 && data.findIndex((s) => s.screen_name === 'C01_S01_LectureList');

              if (index > -1) {
                data[index].formData = { ...this.formData };
              }

              localStorage.setItem('screen', this.enCodeData(data));
              time = 1;
            }, 0);
          }
        } else {
          this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        }
      }
    },

    handleCurrentChange(val) {
      this.onCloseFilter();
      this.onScrollTop = 0;
      this.pagination.curentPage = val;
      this.getDataLecture(false, false);
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

    openModalZ05S01(isOrgPlan) {
      this.isOrgPlan = isOrgPlan;
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: {
          ...this.paramsZ05S01,
          mode: this.isOrgPlan ? 'single' : 'single',
          orgCdList: this.isOrgPlan ? this.formData.lstOrgPlan.map((x) => x.org_cd) : this.formData.lstOrg.map((x) => x.org_cd)
        },
        width: '45rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (this.isOrgPlan) {
        this.formData.lstOrgPlan = [...e.orgSelected];
      } else {
        this.formData.lstOrg = [...e.orgSelected];
      }
    },

    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
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

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    handleRemoveOrg(org) {
      this.formData.lstOrg = this.formData.lstOrg.filter((x) => x.org_cd !== org.org_cd);
    },

    handleRemoveOrgPlan() {
      this.formData.lstOrgPlan = [];
    },

    handleRemoveProduct(product) {
      this.formData.lstProduct = this.formData.lstProduct.filter((x) => x.product_cd !== product.product_cd);
    },

    setSelectedStatusType(item) {
      this.formData.status_type = item.status_type;
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
    redirectToLectureInputC01S02(item, copyFlags) {
      this.setScrollTop();
      this.setScrollScreen('C01_S02_LectureInput', 0);
      if (item) {
        let convention = item.convention_id;
        if (copyFlags) {
          this.$router.push({ name: 'C01_S02_LectureInput', params: { convention_copy: convention } });
        } else {
          this.$router.push({ name: 'C01_S02_LectureInput', params: { convention_id: convention } });
        }
      } else {
        this.$router.push({ name: 'C01_S02_LectureInput' });
      }
    },

    // Attendee
    redirectToAttendantManagementC01S03(item) {
      this.setScrollTop();
      this.$router.push({ name: 'C01_S03_AttendantManagement', params: { convention_id: item.convention_id } });
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
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.wrapBtn {
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
      padding: 5px 20px;
      max-height: calc(100vh - 250px);

      .lectureForm-label {
        margin: 0 0 6px;
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
          margin-top: 10px;
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
  padding: 15px;
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
        display: flex;
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
    .lectureLst-label {
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

    .lecture-left {
      flex-grow: 1;
    }
    .lecture-right {
      flex: 0 0 230px;
      text-align: right;
      .lectureLst-info {
        height: 27px;
      }

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
  color: #dc3545;
}
.pagination {
  box-shadow: 0px -3px 9px #0000001a;
}
</style>
