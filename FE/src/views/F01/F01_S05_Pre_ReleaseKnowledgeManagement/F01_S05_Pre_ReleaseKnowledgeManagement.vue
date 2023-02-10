<template>
  <div v-loading="loadingPage" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo">
          <button type="button" class="btn btn-sign btn-create-notes" @click="onclickShowF01S03()">
            <span class="btn-iconLeft"> <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" /> </span>
            新規登録
          </button>
        </div>
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button type="button" class="btn btn-filter" :class="isShowPopupFilter ? 'btn-filter-none-shadow' : ''" @click="openModalFilter">
              <img v-svg-inline svg-inline class="svg svg-filter" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter dropdown-knowled', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter">
                <button type="button" class="btn btn-filter btn-filter-up btn-filter-none-shadow btn-close-filter" @click="onCloseFilter">
                  <img class="svg" src="@/assets/template/images/icon_filter-white.svg" alt="" />
                </button>
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="filterKnowled">
                <div class="form-knowled scrollbar">
                  <ul>
                    <li class="w-100">
                      <label class="knowled-label">ステータス</label>
                      <div class="formNotes-checkBox-f">
                        <ul>
                          <li v-for="(item, i) of knowledge_status" :key="item.definition_value">
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input
                                class="custom-control-input"
                                type="checkbox"
                                :checked="filter.knowledge_status_type?.includes(item.definition_value)"
                                @change="chooseStatus($event, i)"
                              />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ item.definition_label }}</span>
                            </label>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">タイトル</label>
                      <div class="knowled-control">
                        <el-input
                          v-model="filter.title"
                          clearable
                          placeholder="タイトルを入力"
                          class="form-control-input"
                          @change="filter.title = convertToHalf(filter.title)"
                        />
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">ナレッジID</label>
                      <div class="knowled-control">
                        <el-input
                          v-model="filter.knowledge_id"
                          clearable
                          placeholder="ナレッジIDを入力"
                          class="form-control-input"
                          @change="filter.knowledge_id = convertToHalf(filter.knowledge_id)"
                        />
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">カテゴリ</label>
                      <div class="knowled-control">
                        <el-select v-model="filter.knowledge_category_cd" placeholder="未選択" class="form-control-select">
                          <el-option value=""> 未選択</el-option>

                          <el-option
                            v-for="item in knowledge_category"
                            :key="item.knowledge_category_cd"
                            :label="item.knowledge_category_name"
                            :value="item.knowledge_category_cd"
                          >
                          </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">ナレッジ元作成者</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="ナレッジ元作成者" @click="openModalZ05S01" @touchstart="openModalZ05S01">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="paramsZ05S01?.userCdList?.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item in paramsZ05S01.userCdList"
                                :key="item?.user_cd"
                                :label="item?.user_name"
                                :value="item?.user_cd"
                                class="el-tag-custom"
                                closable
                                @close="handleRemove('Z05_S01', item)"
                              >
                                {{ item.user_name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">公開日</label>
                      <div class="knowled-dateTime">
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="filter.release_datetime_from"
                            :disabled-date="disabledDateStart"
                            format="YYYY/M/D"
                            type="date"
                            :editable="false"
                            placeholder="開始日"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                        <div class="dateTime-item">～</div>
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="filter.release_datetime_to"
                            :disabled-date="disabledDateEnd"
                            format="YYYY/M/D"
                            type="date"
                            :editable="false"
                            placeholder="終了日"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">品目</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="品目" @click="openModalZ05S06" @touchstart="openModalZ05S06">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>

                          <div v-if="productCdList?.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item in productCdList"
                                :key="item.code"
                                :label="item.name"
                                :value="item.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemove('Z05_S06', item)"
                              >
                                {{ item.name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設所在地</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="施設所在地" @click="openModalZ05_S02" @touchstart="openModalZ05_S02">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="muniOrPreList?.code" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                :key="muniOrPreList.code"
                                :label="muniOrPreList.name"
                                :value="muniOrPreList.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemove('Z05_S02', muniOrPreList)"
                              >
                                {{ muniOrPreList.name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>

                    <li>
                      <label class="knowled-label">施設区分分類</label>
                      <div class="knowled-control">
                        <el-select v-model="filter.facility_type_group_cd" placeholder="未選択" class="form-control-select">
                          <el-option value=""> 未選択</el-option>

                          <el-option
                            v-for="item in facility_type_group"
                            :key="item.facility_type_group_cd"
                            :label="item.facility_type_group_name"
                            :value="item.facility_type_group_cd"
                          >
                          </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">診療科目分類</label>
                      <div class="knowled-control">
                        <el-select v-model="filter.medical_subjects_group_cd" placeholder="未選択" class="form-control-select">
                          <el-option value=""> 未選択</el-option>

                          <el-option
                            v-for="item in medical_subjects_group"
                            :key="item.medical_subjects_group_cd"
                            :label="item.medical_subjects_group_name"
                            :value="item.medical_subjects_group_cd"
                          >
                          </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="施設" @click="openModalZ05S03" @touchstart="openModalZ05S03">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="facilityList.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item in facilityList"
                                :key="item.code"
                                :label="item.name"
                                :value="item.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemove('Z05_S03', item)"
                              >
                                {{ item.name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設個人</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span
                            data-toggle="modal"
                            data-target="#Z05_S04_facilityCustomer"
                            data-backdrop="static"
                            class="icon log-icon"
                            title_log="施設個人"
                            @click="openModalZ05S04()"
                          >
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>

                          <div v-if="personList.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item in personList"
                                :key="item.code"
                                :label="item.name"
                                :value="item.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemove('Z05_S04', item)"
                              >
                                {{ item.name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>

                <div class="knowled-btn">
                  <button type="button" class="btn btn-outline-primary" @click="isShowPopupFilter = false">キャンセル</button>
                  <button type="button" class="btn btn-primary" @click="search(true, false)">検索</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div ref="listKnowledgeScroll" class="listKnowled scrollbar">
        <ul v-if="knowledgeList.length > 0">
          <li v-for="item of knowledgeList" :key="item.knowledge_id">
            <div class="itemRibbon"></div>
            <div class="listKnowled-row">
              <div class="listKnowled-head">
                <div class="listKnowled-left">
                  <span class="item"><img src="@/assets/template/images/icon_light-bulb01.svg" alt="" /></span>
                  <a class="log-icon" :title_log="item.title" @click="goToF01S03Detail(item.knowledge_id)">
                    {{ item.title }}
                  </a>
                </div>
                <div class="listKnowled-right">
                  <span v-show="item.knowledge_status_type?.length > 0" class="spanLabel spanLabel--status">{{ item.definition_label }}</span>
                  <span v-show="item.knowledge_status_type?.length > 0" class="spanLabel spanLabel--category">{{ item.knowledge_category_name }}</span>

                  <span class="listKnowled-id">
                    ナレッジID：<span class="idCode">{{ item.knowledge_id }}</span>
                  </span>
                </div>
              </div>
              <div class="listKnowled-content">
                <div v-show="item.contents.length > 0" class="listKnowled-text">
                  <p>
                    <span
                      :class="`knowled-content line-text-over ${
                        item.person_cd && item.release_datetime
                          ? 'line-plus1'
                          : (item.person_cd && !item.release_datetime) || (!item.person_cd && item.release_datetime)
                          ? 'line-plus'
                          : ''
                      }`"
                    >
                      {{ item.contents }}
                    </span>
                  </p>
                </div>
              </div>
              <div class="listKnowled-info">
                <ul>
                  <li v-if="item.release_datetime">
                    <span class="listKnowled-label">公開日：</span>
                    {{ formatFullDate(item.release_datetime) }}
                  </li>
                  <li class="listKnowled-li">
                    <div class="listKnowled-item"><span class="listKnowled-label">品目：</span>{{ item.product_name }}</div>
                    <div class="listKnowled-subject"><span class="listKnowled-label">診療科目分類：</span>{{ item.medical_subjects_group_name }}</div>
                  </li>
                  <li><span class="listKnowled-label">施設区分分類：</span>{{ item.facility_type_group_name }}</li>
                  <li class="listKnowled-liItem">
                    <span class="item">
                      <img class="svg" src="@/assets/template/images/icon_medical-hospital.svg" alt="" />
                    </span>
                    <p>
                      <span class="link" @click="redirectToH01S02(item)"> {{ item.facility_short_name }}</span>
                      <span v-if="item.department_name"> 　{{ item.department_name }} </span>
                    </p>
                  </li>
                  <li v-if="item.person_cd" class="listKnowled-liItem">
                    <span class="item">
                      <img src="@/assets/template/images/icon_user07.svg" alt="" />
                    </span>
                    <p>
                      <span v-if="item.person_name" class="link" @click="redirectToH02S02(item)">{{ item.person_name }}</span>
                      <span v-if="item.display_position_name"> 　{{ item.display_position_name }} </span>
                    </p>
                  </li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_maps.svg" alt="" /></span>
                    {{ item.prefecture_name }}　{{ item.municipality_name }}
                  </li>
                  <li v-if="getCreateUserName(item)?.length > 50" class="listKnowled-liItem" :title_log="getCreateUserName(item)">
                    <span class="item"><img src="@/assets/template/images/icon_user-edit01.svg" alt="" /></span>

                    {{ getCreateUserName(item).slice(0, 50) }}
                    {{ '...' }}
                  </li>

                  <li v-else class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_user-edit01.svg" alt="" /></span>

                    {{ getCreateUserName(item) }}
                  </li>
                </ul>
              </div>
            </div>
          </li>
        </ul>
        <EmptyData v-else-if="!isLoadDefault" custom-class="customClassEmpty" thumb-margin-top="20px" />
      </div>
    </div>
    <!-- pagination left single -->
    <div v-if="knowledgeList.length > 0" class="pagination pagination-custom">
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
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="true"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="onCloseModal"
          @handleYes="resetInfor"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import F01S05Service from '@/api/F01/F01_S05_Pre_ReleaseKnowledgeManagementService';

import PaginationTable from '../../../components/PaginationTable.vue';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05_S06_MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05S04FacilityCustomerSelection from '@/views/Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection';
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';
import Z05_S02_ModalAreaSelection from '@/views/Z05/Z05_S02_Area_Selection/Z05_S02_Area_Selection';
import { markRaw } from 'vue';
import moment from 'moment';
import { Device } from '@/utils/common-function.js';
import _ from 'lodash';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'F01_S05_Pre_ReleaseKnowledgeManagement',
  components: {
    PaginationTable,
    Z05S01Organization,
    Z05_S06_MaterialSelection,
    Z05S04FacilityCustomerSelection,
    Z05S03FacilitySelection,
    Z05_S02_ModalAreaSelection
  },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      isLoadDefault: true,
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalD01S02: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        customClass: 'custom-dialog'
      },
      knowledgeList: [],
      facility_type_group: [],
      knowledge_category: [],
      knowledge_status: [],
      medical_subjects_group: [],
      topLike: [],
      knowledAuthorClass: ['knowledAuthor-yellow', 'knowledAuthor-blue', 'knowledAuthor-red', '', ''],
      usersFromToLike: [],
      // modal result
      productCdList: [],
      facilityList: [],
      personList: [],

      muniOrPreList: null,
      isLoading: false,
      filter: {
        title: '',
        knowledge_category_cd: '',
        knowledge_id: '',
        user_cd: [],
        release_datetime_from: '',
        release_datetime_to: '',
        product_cd: [],
        facility_cd: '',
        person_cd: '',
        facility_type_group_cd: '',
        medical_subjects_group_cd: '',
        prefecture_cd: '',
        municipality_cd: '',
        knowledge_status_type: [],
        limit: '100',
        offset: '0'
      },
      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },
      loadingPage: false,
      paramsZ05S01: {
        userFlag: 1,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
      },
      pramsZ05S06: {
        mode: 'multiple',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      isShowPopup: false,
      paramZ05S04: {
        non_facility_flag: 1, // req
        non_medical_flag: 1, //req
        user_cd: '',
        user_name: '',
        target_product_cd: '',
        definition_value: '',
        facility_category_type: '',
        prefecture_cd: '',
        prefecture_name: '',
        municipality_cd: '',
        municipality_name: '',
        facility_cd: [],
        facility_name: [],
        person_cd: []
      },
      paramsZ05S06: {
        mode: 'multiple',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },
      keyPass: [],
      facilityCd: [],
      facilityName: [],

      paramsZ05S03: {
        selectGroupCd: '',
        facilityCd: [],
        facilityName: []
      },
      onScrollTop: 0,
      currentUser: null
    };
  },
  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'F01_S05_Pre_ReleaseKnowledgeManagement') {
          let queryUrl = this._route('F01_S05_Pre_ReleaseKnowledgeManagement')?.params;
          if (queryUrl && queryUrl?.knowledge_id) {
            this.resetFilter();
            this.filter.knowledge_id = queryUrl?.knowledge_id;
          }
          this.getScreenData();
          this.search(true, true);
        }
      }
    );
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: '公開前ナレッジ管理',
      icon: 'icon-light-group.svg',
      isShowBack: false
    });

    this.currentUser = this.getCurrentUser();
    const route = this.$route;
    localStorage.removeItem('time_line_knowledge');

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.F01_S05_Pre_ReleaseKnowledgeManagement || 0;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.F01_S05_Pre_ReleaseKnowledgeManagement || 1;

    const isLoadingComponent = localStorage.getItem('isLoadingComponent');
    if (isLoadingComponent === 'true') {
      this.resetFilter();
    }

    if (route && route.params && route.params.knowledge_id) {
      this.resetFilter();
      this.filter.knowledge_id = route.params.knowledge_id ? route.params.knowledge_id : this.filter.knowledge_id;
      this.getScreenData();

      this.search(false, true);
    }

    this.getScreenData();

    this.search(false, true);
  },
  methods: {
    setScrollTop() {
      let scrollTop = this.$refs.listKnowledgeScroll ? this.$refs.listKnowledgeScroll.scrollTop : 0;
      this.setScrollScreen('F01_S05_Pre_ReleaseKnowledgeManagement', scrollTop);
    },
    getDevice() {
      return Device();
    },

    disabledDateStart(time) {
      if (this.filter.release_datetime_to) {
        return time.getTime() > new Date(this.filter.release_datetime_to).getTime();
      }
    },

    disabledDateEnd(time) {
      if (this.filter.release_datetime_from) {
        return time.getTime() < new Date(this.filter.release_datetime_from).getTime();
      }
    },

    getNameStatus(item) {
      const knowledge_status = this.knowledge_status?.map((s) => (s.definition_value === item.knowledge_status_type ? s : null))[0];
      return knowledge_status && knowledge_status['definition_label'];
    },
    contentTextSeeMore(i, content) {
      if (content && content.length > 0) {
        const word = content;
        const count = word?.length > 0 && word?.length < 267;
        return count ? false : true;
      } else {
        return false;
      }
    },
    chooseStatus(event, index) {
      this.knowledge_status[index].checked = event.target.checked;
      this.filter.knowledge_status_type = this.knowledge_status.filter((s) => s.checked === true).map((s) => s.definition_value);
    },
    /** Modal */
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.paramsZ05S01 },
        width: '65rem',
        customClass: 'custom-dialog',
        destroyOnClose: true
      };
    },
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05_S06_MaterialSelection),
        props: {
          ...this.paramsZ05S06,
          initDataCodes: this.filter.product_cd,
          mode: 'multiple'
        },
        width: '42rem',
        customClass: 'custom-dialog modal-fixed',
        destroyOnClose: true
      };
    },
    // Modal Z05-S03
    openModalZ05S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設選択',
        isShowModal: true,
        component: markRaw(Z05S03FacilitySelection),
        width: this.getDevice() !== 'Desktop' ? '90%' : '55rem',
        props: {
          mode: 'single',
          ...this.paramsZ05S03,
          orgCD: this.paramsZ05S03.org_cd,
          userCD: this.paramsZ05S03.facility_cd?.length ? '' : this.currentUser?.user_cd,
          userName: this.paramsZ05S03.facility_cd?.length ? '' : this.currentUser?.user_name,
          selectGroupCd: this.paramsZ05S03.select_group_id,
          targetProductCd: this.paramsZ05S03.target_product_cd,
          facilityCategoryType: this.paramsZ05S03.facility_category_type,
          prefectureCD: this.paramsZ05S03.prefecture_cd,
          prefectureName: this.paramsZ05S03.prefecture_name,
          municipalityCD: this.paramsZ05S03.municipality_cd,
          municipalityName: this.paramsZ05S03.municipality_name,
          facilityCd: this.paramsZ05S03.facility_cd,
          facilityName: this.paramsZ05S03.facility_name
        },
        destroyOnClose: true,
        customClass: 'custom-dialog'
      };
    },
    openModalZ05S04() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設個人選択',
        isShowModal: true,
        component: markRaw(Z05S04FacilityCustomerSelection),
        props: {
          propsPrams: {
            ...this.paramZ05S04,
            user_cd: this.paramZ05S04.person_cd.length || this.filter.facility_cd ? '' : this.currentUser?.user_cd,
            user_name: this.paramZ05S04.person_cd.length || this.filter.facility_cd ? '' : this.currentUser?.user_name,
            non_facility_flag: this.filter.facility_cd ? 1 : 0
          },
          mode: 'single'
        },
        width: this.getDevice() !== 'Desktop' ? '90%' : '55rem',
        customClass: 'custom-dialog',
        destroyOnClose: true
      };
    },
    openModalZ05_S02() {
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
          prefectureCode: this.muniOrPreList?.prefecture_cd,
          municipalityCode: this.muniOrPreList?.municipality_cd
        }
      };
    },

    chooserUser(data, i) {
      const create_user_cd = data?.create_user_cd;
      const newData = { user_name: data.user_name, user_cd: data.create_user_cd };
      let color = document.querySelector(`#c_${create_user_cd}`);

      if (this.usersFromToLike.length > 0) {
        const index = this.usersFromToLike.findIndex((s) => s?.create_user_cd === create_user_cd);
        if (index !== -1) {
          this.usersFromToLike.splice(index, 1);

          setTimeout(() => {
            color.style.background = '';
          });
        } else this.usersFromToLike[i] = data;
        color.style.background = '#e4e4e4'; // add new
      } else {
        this.usersFromToLike[i] = data; // first add
        color.style.background = '#e4e4e4';
      }
      if (this.paramsZ05S01.userCdList.length > 0) {
        const index2 = this.paramsZ05S01.userCdList.findIndex((s) => s?.user_cd === create_user_cd);
        if (index2 !== -1) {
          this.paramsZ05S01.userCdList.splice(index2, 1);
          this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
        } else {
          this.paramsZ05S01.userCdList.push(newData);
          this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
        }
      } else {
        this.paramsZ05S01.userCdList.push(newData);
        this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
      }
    },
    onResultModalZ05S01(e) {
      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        userCdList: e.userSelected.concat(this.usersFromToLike.map((s) => ({ user_cd: s.create_user_cd, user_name: (s && s?.user_name) || 'text' })))
      };
      this.filter.user_cd = e.userSelected.map((s) => s.user_cd).concat(this.usersFromToLike.map((s) => s.create_user_cd));
    },
    onResultModalZ05S03(e) {
      this.facilityList = e?.facilitySelectList.map((s) => ({ code: s.facility_cd, name: s.facility_short_name }));
      this.filter.facility_cd = this.facilityList[0]?.code;
      this.facilityCd = [this.facilityList[0]?.code];

      this.paramsZ05S03 = {
        ...e.filterData,
        facility_cd: [e?.facilitySelectList[0].facility_cd],
        facility_name: [e.filterData.facility_name]
      };

      this.paramZ05S04.facility_cd = this.filter.facility_cd || this.filter.facility_cd.length > 0 ? [this.filter.facility_cd] : '';
    },
    onResultModalZ05S06(e) {
      let data = e?.dataSelected;
      this.productCdList = data.map((s) => ({ name: s.product_name, code: s.product_cd }));
      this.filter.product_cd = this.productCdList.map((s) => s.code);
      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        categoryCode: '',
        classificationCode: '',
        initDataCodes: data?.map((x) => x.product_cd)
      };
    },
    onResultModalZ05S04(e) {
      const data = e.facilityPersonalSelected;
      this.personList = data.map((s) => ({ code: s.person_cd, name: s.person_name }));
      this.filter.person_cd = this.personList[0].code;

      this.paramZ05S04 = {
        ...e.filterData,
        facility_cd: [data[0].facility_cd],
        person_cd: [data[0].person_cd]
      };

      this.facilityList = data.map((s) => ({ code: s.facility_cd, name: s.facility_short_name }));
      this.filter.facility_cd = this.filter.facility_cd ? this.filter.facility_cd : this.facilityList[0]?.code;
      this.facilityCd = this.facilityCd.length > 0 ? this.facilityCd : [this.facilityList[0]?.code];

      this.paramsZ05S03 = {
        ...this.paramsZ05S03,
        facility_cd: this.facilityCd
      };
    },
    onResultModalZ05S02(e) {
      const municipalitySelected = e.municipalitySelected && e.municipalitySelected.municipality_cd ? e.municipalitySelected : null;
      const prefectureSelected = e.prefectureSelected && e.prefectureSelected.prefecture_cd ? e.prefectureSelected : null;

      let prefecture_cd = prefectureSelected ? prefectureSelected.prefecture_cd : '';
      let prefecture_name = prefectureSelected ? prefectureSelected.prefecture_name : '';
      let municipality_cd = municipalitySelected ? municipalitySelected.municipality_cd : '';
      let municipality_name = municipalitySelected ? municipalitySelected.municipality_name : '';

      this.filter.prefecture_cd = prefecture_cd;
      this.filter.municipality_cd = municipality_cd;

      this.muniOrPreList = {
        prefecture_cd: prefecture_cd,
        prefecture_name: prefecture_name,
        municipality_cd: municipality_cd,
        municipality_name: municipality_name,
        code: municipality_cd ? municipality_cd : prefecture_cd,
        name: municipality_name ? municipality_name : prefecture_name
      };
    },
    onResultModal(e) {
      const screen = e && e.screen;
      if (screen === 'Z05_S06') {
        this.onResultModalZ05S06(e);
      }
      if (screen === 'Z05_S03') {
        this.onResultModalZ05S03(e);
      }
      if (screen === 'Z05_S04') {
        this.onResultModalZ05S04(e);
      }
      if (screen === 'Z05_S02') {
        this.onResultModalZ05S02(e);
      }
      if (screen === 'Z05_S01') {
        this.onResultModalZ05S01(e);
      }

      this.onCloseModal();
    },
    handleRemove(screen, item) {
      if (screen === 'Z05_S01') {
        const index = this.paramsZ05S01.userCdList.findIndex((s) => s.user_cd === item.user_cd);
        this.paramsZ05S01.userCdList.splice(index, 1);
        this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
        let color = document.querySelector(`#c_${item.user_cd}`);
        color ? (color.style.background = '') : '';
      }
      if (screen === 'Z05_S02') {
        this.muniOrPreList = null;
        this.filter.municipality_cd = '';
        this.filter.prefecture_cd = '';
      }
      if (screen === 'Z05_S03') {
        const index = this.facilityList.findIndex((s) => s.user_cd === item.user_cd);
        this.facilityList.splice(index, 1);
        this.filter.facility_cd = '';
        this.paramsZ05S03 = {
          selectGroupCd: '',
          facilityCd: [],
          facilityName: []
        };

        this.paramZ05S04.facility_cd = this.filter.person_cd ? this.paramZ05S04.facility_cd : [];
      }
      if (screen === 'Z05_S04') {
        const index = this.personList.findIndex((s) => s.user_cd === item.user_cd);
        this.personList.splice(index, 1);
        this.filter.person_cd = '';
        this.paramZ05S04 = this.paramZ05S04Default();
      }
      if (screen === 'Z05_S06') {
        const index = this.productCdList.findIndex((s) => s.code === item.code);
        this.productCdList.splice(index, 1);
        this.filter.product_cd = this.productCdList.map((s) => s.code);

        this.paramsZ05S06 = {
          ...this.paramsZ05S06,
          categoryCode: '',
          classificationCode: '',
          initDataCodes: this.filter.product_cd
        };
      }
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    sortBy(item) {
      this.filter.sort_by = item;
    },
    resetFilter() {
      this.filter = {
        title: '',
        knowledge_category_cd: '',
        knowledge_id: [],
        user_cd: [],
        release_datetime_from: '',
        release_datetime_to: '',
        product_cd: [],
        facility_cd: '',
        person_cd: '',
        facility_type_group_cd: '',
        medical_subjects_group_cd: '',
        prefecture_cd: '',
        municipality_cd: '',
        limit: '100',
        knowledge_status_type: [],
        offset: '0'
      };
      // modal result
      this.productCdList = [];
      this.facilityList = [];
      this.personList = [];
      this.paramsZ05S01.userCdList = [];
      this.muniOrPreList = null;
      this.knowledge_status = this.knowledge_status.map((s) => ({ ...s, checked: true }));
    },
    /** API */
    async getScreenData() {
      const data = this.getScreen('F01_S05_Pre_ReleaseKnowledgeManagement');

      try {
        const raw = await F01S05Service.getDataScreen();
        const res = raw.data;
        this.medical_subjects_group = res.data.medical_subjects_group;
        this.facility_type_group = res.data.facility_type_group;
        this.knowledge_category = res.data.knowledge_category;
        this.knowledge_status =
          data && data['knowledge_status'].length > 0 ? data['knowledge_status'] : res.data.knowledge_status.map((s) => ({ ...s, checked: true }));

        this.filter.knowledge_status_type =
          data && data['knowledge_status_type'].length > 0 ? data['knowledge_status_type'] : this.knowledge_status.map((s) => s.definition_value);
        this.topLike = data && data['top_like'].length > 0 ? data['top_like'] : res.data.top_like;
      } catch (err) {
        return;
      }
    },
    async getList(filter, isFilter, isLoadDefault) {
      this.loadingPage = true;
      this.isLoadDefault = isLoadDefault;
      try {
        const raw = await F01S05Service.getData(filter);
        const res = raw.data.data;

        const totalItems = res.pagination.total_items;
        const curentPage = res.pagination.current_page;
        this.knowledgeList = res.records;
        this.pagination = {
          ...this.pagination,
          totalItems,
          curentPage
        };
        this.$router.replace({});
      } catch (err) {
        if (err?.response?.data?.message) {
          this.$notify({ message: err?.response?.data?.message, customClass: 'error' });
        } else {
          this.$notify({ message: '内部エラーが発生しました。システム管理者に連絡してください。', customClass: 'error' });
        }
      } finally {
        await new Promise((resolve) => setTimeout(resolve, 500));
        this.contentTextSeeMore();

        this.setCurrentPageScreen('F01_S05_Pre_ReleaseKnowledgeManagement', this.pagination.curentPage);

        if (this.$refs.listKnowledgeScroll) {
          if (this.onScrollTop && !isFilter) {
            this.$refs.listKnowledgeScroll.scrollTop = this.onScrollTop;
          } else {
            this.$refs.listKnowledgeScroll.scrollTop = 0;
          }
        }

        this.loadingPage = false;
        this.isShowPopupFilter = false;
        this.isLoadDefault = false;

        this.js_pscroll(0.5);

        const childRouter = JSON.parse(localStorage.getItem('router'));
        let routes = this.decodeData(localStorage.getItem('$_D')) || [];
        localStorage.setItem('isLoadingComponent', false);

        if (routes && routes.length > 0) {
          const indexRouteKnowledge = routes.findIndex((x) => x.name === 'F01_S05_Pre_ReleaseKnowledgeManagement');

          if (indexRouteKnowledge > -1) {
            let routeKnowledge = {
              ...routes[indexRouteKnowledge],
              params: {}
            };

            routes[indexRouteKnowledge] = routeKnowledge;
            localStorage.setItem('$_D', this.enCodeData(routes));
          }
        } else {
          let routeKnowledge = {
            name: 'F01_S05_Pre_ReleaseKnowledgeManagement',
            params: {},
            path: childRouter.find((x) => x.name === 'F01_S05_Pre_ReleaseKnowledgeManagement')?.path
          };

          routes[0] = routeKnowledge;
          localStorage.setItem('$_D', this.enCodeData(routes));
        }
      }
    },
    showFlagUpdate(item) {
      let isFlag = true;
      const updateTimeNumber = item.last_update_datetime_updated ? Number(item.last_update_datetime_updated) : 0; // 14;
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
    showFlagCreate(item) {
      let isFlag = true;
      const create_datetime_new = item.create_datetime_new ? Number(item.create_datetime_new) : 0;
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
    getCreateUserName(item) {
      const createUser = item?.create_user_cd ? item?.create_user_cd : '';
      const createUserTogether = item?.create_user_cd_together ? item?.create_user_cd_together : '';
      const approval_user_cd = item?.knowledge_status_type === '30' && item?.approval_user_cd ? item?.approval_user_cd : '';
      let name = createUser;
      if (createUser.length > 0 && approval_user_cd.length > 0 && createUserTogether.length > 0)
        name = createUser + ', ' + createUserTogether + ', ' + approval_user_cd;
      if (createUser.length > 0 && approval_user_cd.length > 0 && createUserTogether.length === 0) name = createUser + ', ' + approval_user_cd;
      if (createUser.length === 0 && approval_user_cd.length > 0 && createUserTogether.length > 0) name = approval_user_cd + ', ' + createUserTogether;
      if (createUser.length > 0 && approval_user_cd.length === 0 && createUserTogether.length > 0) name = createUser + ', ' + createUserTogether;
      if (createUser.length === 0 && approval_user_cd.length === 0 && createUserTogether.length > 0) name = createUserTogether;
      if (createUser.length === 0 && approval_user_cd.length > 0 && createUserTogether.length === 0) name = approval_user_cd;

      return name;
    },
    onclickShowF01S03() {
      this.setScrollTop();
      this.$router.push({ name: 'F01_S03_KnowledgeInput' });
    },
    handleCurrentChange(val) {
      this.onScrollTop = 0;
      this.pagination.curentPage = val;
      this.filter = { ...this.filter, offset: this.pagination.curentPage - 1, limit: 100 };
      this.getList(this.filter, true, false);
    },
    goToF01S03Detail(id) {
      this.setScrollTop();
      this.$router.push({ name: 'F01_S03_KnowledgeInput', params: { knowledge_id: id, fromF01S02: 'false' } });
    },
    // modal filter
    openModalFilter() {
      this.isShowPopupFilter = true;
    },
    onCloseFilter() {
      this.isShowPopupFilter = false;
    },

    search(isFilter, isLoadDefault) {
      const from = moment(this.filter.release_datetime_from);
      const to = moment(this.filter.release_datetime_to);
      setTimeout(() => {
        this.filter.release_datetime_from = !from.isValid() ? '' : from.format('YYYY/M/D');
        this.filter.release_datetime_to = !to.isValid() ? '' : to.format('YYYY/M/D');
        this.getList(this.filter, isFilter, isLoadDefault);
      });
    },

    redirectToH01S02(item) {
      this.setScrollTop();
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

    redirectToH02S02(person) {
      this.setScrollTop();
      let person_cd = person.person_cd;
      localStorage.setItem('activeMenu', 'F01_S05_Pre_ReleaseKnowledgeManagement');
      this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet_mini: 1024px;

$viewport_sm: 767px;
.wrapBtn {
  .dropdown-knowled {
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
    .filterKnowled {
      width: calc(100% + 40px);
      height: calc(100vh - 200px);
      @media (max-width: $viewport_tablet) {
        height: calc(100vh - 450px);
      }
      margin-left: -20px;
    }
    .form-knowled {
      height: calc(100vh - 250px);
      @media (max-width: $viewport_tablet) {
        height: calc(100vh - 500px);
      }
      padding: 0 20px;
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        > li {
          width: 50%;
          padding-left: 10px;
          margin-top: 10px;
        }
      }
      .knowled-label {
        color: #5f6b73;
        font-size: 0.875rem;
        margin-bottom: 6px;
      }
      .knowled-dateTime {
        display: flex;
        align-items: center;
        .form-icon {
          width: calc(50% - 16px);
        }
        .dateTime-item {
          width: 32px;
          text-align: center;
          color: #727f88;
          font-size: 1rem;
        }
      }
      .knowledAuthor {
        margin-top: 8px;
        ul {
          li {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            background: #fff;
            border-bottom: 1px solid #f9f9f9;
            cursor: pointer;
            &.knowledAuthor-yellow {
              .knowledAuthor-item {
                color: #fff;
                background: linear-gradient(28.06deg, rgba(245, 193, 57, 0.9) 55.53%, rgba(254, 230, 146, 0.5) 95.13%);
              }
              .knowledAuthor-txt {
                color: #f1b86a;
              }
            }
            &.knowledAuthor-blue {
              .knowledAuthor-item {
                color: #fff;
                background: linear-gradient(30.15deg, rgba(138, 174, 186, 0.8) 54.55%, rgba(197, 222, 230, 0.8) 91.27%);
              }
              .knowledAuthor-txt {
                color: #99c0c0;
              }
            }
            &.knowledAuthor-red {
              .knowledAuthor-item {
                color: #fff;
                background: linear-gradient(28.12deg, rgba(214, 134, 109, 0.8) 54.14%, rgba(253, 216, 143, 0.8) 92.17%);
              }
              .knowledAuthor-txt {
                color: #bda5a5;
              }
            }
          }
        }
        .knowledAuthor-item {
          width: 30px;
          background: #e3e3e3;
          border-radius: 0px 30px 30px 0px;
          height: 26px;
          color: #5f6b73;
          font-size: 1rem;
          line-height: 120%;
          font-weight: 700;
          display: flex;
          justify-content: center;
          align-items: center;
        }
        .knowledAuthor-txt {
          width: calc(100% - 98px);
          padding: 0 10px 0 15px;
          color: #5f6b73;
          display: flex;
          flex-wrap: wrap;
          .knowledAuthor-label {
            width: 90px;
            padding-right: 10px;
          }
          .knowledAuthor-labelName {
            width: calc(100% - 90px);
          }
          .knowledAuthor-label,
          .knowledAuthor-labelName {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
          }
        }
        .knowledAuthor-like {
          width: 68px;
          display: flex;
          .item-like {
            min-width: 12px;
            margin: -2px 2px 0 0;
          }
          .number-like {
            color: #727f88;
            font-weight: 700;
          }
        }
      }
      .displayOrder {
        ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: 1px;
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
              border-radius: 0;
            }
          }
        }
      }
    }
    .knowled-btn {
      margin-top: 0px;
      text-align: center;
      height: 70px;
      display: flex;
      justify-content: center;
      align-items: center;
      .btn {
        width: 142px;
        margin-right: 14px;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .knowled-status {
      margin-top: -6px;
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -15px;
        li {
          margin-top: 6px;
          padding-left: 15px;
          width: 33.333333%;
          .custom-control,
          .custom-control-description {
            width: 100%;
          }
          .custom-control {
            padding-right: 6px;
          }
        }
      }
    }
  }
}
.listKnowled {
  height: 100%;
  padding: 8px 24px 0;
  > ul {
    > li {
      margin-bottom: 10px;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      padding: 20px 20px 20px 40px;
      position: relative;
    }
  }
  .itemRibbon {
    position: absolute;
    display: flex;
    top: -4px;
    left: 8px;
    width: 40px;
    height: 32px;
  }
  .listKnowled-title-row {
    display: flex;
    flex-wrap: wrap;

    .listKnowled-title-content {
      width: 100%;
      padding-right: 24px;
      padding-top: 15px;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
    .listKnowled-title-head {
      display: flex;
      flex-wrap: wrap;
      .listKnowled-left {
        .position-title {
          position: relative;
        }
        .listKnowled-title {
          font-size: 1.375rem;

          font-weight: 500;
          line-height: 100%;

          .listKnowled-event {
            display: flex;
          }
          a {
            display: flex;

            .item {
              min-width: 25px;
              margin: -2px 7px 0 0;
            }
          }
        }
      }
      .listKnowled-right {
        min-width: 216px;
        margin-left: auto;
        padding-left: 10px;
        text-align: right;
        .listKnowled-label {
          .spanLabel {
            padding: 2px 16px;
            margin-right: 10px;
            display: inline-block;
            &:last-child {
              margin-right: 0;
            }
            &.spanLabel--status {
              background: #daf8dc;
              color: #28a470;
              border-radius: 2px;
              width: 9.5rem;
              text-align: center;
            }
            &.spanLabel--category {
              font-weight: 500;
              background: #e2e4ff;
              color: #555fb0;
              border-radius: 20px;
              width: 9.5rem;
              text-align: center;
            }
          }
        }
        .viewLike {
          display: flex;
          justify-content: flex-end;
          .item-like {
            min-width: 15px;
            margin: -2px 4px 0 0;
          }
          .number-like {
            color: #727f88;
            font-size: 1rem;
            font-weight: 700;
          }
        }
      }
    }
    .listKnowled-text {
      margin-top: 10px;
    }
  }
  .listKnowled-row {
    display: flex;
    flex-wrap: wrap;
    .listKnowled-content {
      width: calc(100% - 356px);
      padding-right: 15px;
      border-right: 1px solid #e3e3e3;
      @media (max-width: $viewport_tablet) {
        width: calc(100% - 300px);
      }
    }
    .listKnowled-info {
      width: 356px;
      padding-left: 15px;
      @media (max-width: $viewport_tablet) {
        width: 300px;
      }
      ul {
        li {
          color: #2d3033;
          margin-bottom: 5px;
          &:last-child {
            margin-bottom: 0;
          }
          .listKnowled-label {
            color: #727f88;
          }
          &.listKnowled-li {
            display: flex;
            flex-wrap: wrap;
            .listKnowled-item {
              min-width: 130px;
              padding-right: 12px;
            }
          }
          &.listKnowled-liItem {
            display: flex;
            .item {
              min-width: 22px;
              margin: -2px 6px 0 0;
            }
          }
        }
      }
    }
    .listKnowled-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 10px;
      width: 100%;
      margin-bottom: 14px;

      .listKnowled-left {
        font-size: 22px;
        font-weight: 500;
        line-height: 24px;
        word-break: break-word;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: flex-start;
        align-items: flex-start;
        flex-grow: 1;

        .item {
          min-width: 25px;
          margin: -2px 7px 0 0;
          flex: 0 0 30px;
        }
        a {
          color: #448add;
          cursor: pointer;
        }

        @media (max-width: $viewport_tablet) {
          font-size: 1rem;
        }
      }

      .listKnowled-right {
        display: flex;
        justify-content: right;
        align-items: center;
        flex-direction: row;
        gap: 5px;
        .listKnowled-id {
          color: #727f88;
          min-width: 125px;
          margin-left: 5px;
          .idCode {
            color: #2d3033;
          }
        }
        .spanLabel {
          padding: 2px 8px;
          display: inline-block;
          &:last-child {
            margin-right: 0;
          }
          &.spanLabel--status {
            background: #daf8dc;
            color: #28a470;
            border-radius: 2px;
            text-align: center;
            width: auto;
            white-space: nowrap;
          }
          &.spanLabel--category {
            font-weight: 500;
            background: #e2e4ff;
            color: #555fb0;
            border-radius: 20px;
            text-align: center;
            width: auto;
            white-space: nowrap;
          }
        }

        @media (max-width: $viewport_tablet) {
          .spanLabel {
            &.spanLabel--status {
              text-align: center;
              font-size: 10px;
              min-width: 70px;
            }
            &.spanLabel--category {
              border-radius: 20px;
              text-align: center;
              font-size: 10px;
              min-width: 90px;
            }
          }
        }
        .viewLike {
          display: flex;
          justify-content: flex-end;
          .item-like {
            min-width: 15px;
            margin: -2px 4px 0 0;
          }
          .number-like {
            color: #727f88;
            font-size: 1rem;
            font-weight: 700;
          }
        }
      }
    }
    .listKnowled-text {
      margin-top: 10px;
    }
  }
}
.article-body {
  opacity: 0;
}
.show {
  transition: opacity 0.5s linear;
  opacity: 1;
}
.subject {
  cursor: pointer;
  position: absolute;
  font-size: 24px;
  font-weight: 700;
  top: 0;
  right: 0;
  width: 5px;
  span {
    position: relative;
    padding-right: 26px;
    &:after {
      position: absolute;
      top: 17px;
      right: 0;
      content: '';
      border-left: 8px solid transparent;
      border-right: 8px solid transparent;
      border-top: 8px solid #448add;
      transition: 400ms all;
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
.btn-filter:disabled {
  cursor: not-allowed;
}

.btn-filter-up:hover {
  background: #448add !important;
}

.tag-a {
  color: #59a5ff !important;
  cursor: pointer;
  &:hover {
    text-decoration: underline !important;
  }
}

.el-tag-custom {
  color: #225999;
  background: #d1e4ff;
  height: 27px;
  line-height: 23px;
  font-size: 14px;
  align-items: center;
  margin: 2px 10px 2px 0;
  border-radius: 24px;
}
.chooser {
  background-color: #e4e4e4 !important;
}
.un-chooser {
  background-color: #fff !important;
}
.listKnowled-txt {
  white-space: pre-line;
}
.text-see-more {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  line-height: 20px;
  max-height: 190px;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  position: relative;
}
.formNotes-checkBox-f {
  padding: 0px 0px 10px 20px;
  ul {
    display: flex;
    flex-wrap: wrap;
    margin-left: -24px;
    li {
      width: 33.33%;
      padding-left: 7px;
      margin-top: 5px;
      .custom-control,
      .custom-control-description {
        width: 100%;
      }
    }
  }
}
.knowled-content {
  position: relative;
  white-space: normal;
}

.line-text-over {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 5;
  -webkit-box-orient: vertical;
  text-shadow: none;
  text-transform: capitalize;

  &.line-plus {
    -webkit-line-clamp: 6;
  }

  &.line-plus1 {
    -webkit-line-clamp: 8;
  }
}

.link {
  font-weight: bold;
}
</style>
