<template>
  <div v-loading="loadingPage" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button type="button" class="btn btn-filter" :class="isShowPopupFilter ? 'btn-filter-none-shadow' : ''" @click="openModalFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter dropdown-knowled', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter">
                <button type="button" class="btn btn-filter btn-filter-up btn-filter-none-shadow btn-close-filter" @click="onCloseFilter">
                  <img class="svg" src="@/assets/template/images/icon_filter-white.svg" alt="" />
                </button>
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="filterKnowled">
                <!--  -->
                <div ref="knowledgeScroll" class="form-knowled scrollbar">
                  <ul>
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

                          <div v-if="productCdList.length > 0" class="form-control d-flex align-items-center">
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
                          <div v-if="muniOrPreList.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item in muniOrPreList"
                                :key="item.code"
                                :label="item.name"
                                :value="item.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemove('Z05_S02', item)"
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
                          <span class="icon log-icon" title_log="施設個人" @click="openModalZ05S04()" @touchstart="openModalZ05S04()">
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
                    <li class="w-100">
                      <label class="knowled-label">ナレッジ元作成者</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="ナレッジ元作成者" @click="openModalZ05S01" @touchstart="openModalZ05S01">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="paramsZ05S01.userCdList.length > 0" class="form-control d-flex align-items-center">
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
                                {{ item?.user_name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                      <div class="knowledAuthor">
                        <ul>
                          <li
                            v-for="(item, i) of topLike"
                            :id="'c_' + item.create_user_cd"
                            :key="i"
                            :style="paramsZ05S01.userCdList.some((s) => s.user_cd === item.create_user_cd) ? 'background:#e4e4e4' : ''"
                            :class="[knowledAuthorClass[i]]"
                            @click="chooserUser(item, i)"
                            @touchstart="chooserUser(item, i)"
                          >
                            <div class="knowledAuthor-item">{{ i + 1 }}</div>
                            <div class="knowledAuthor-txt">
                              <span class="knowledAuthor-label">{{ item.user_name }}</span>
                              <span class="knowledAuthor-labelName">{{ item.org_label }}</span>
                            </div>
                            <div class="knowledAuthor-like">
                              <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                              <span class="number-like">{{ item.knowledge_like }}</span>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">表示順</label>
                      <div class="displayOrder">
                        <ul class="mfsa-custom-tab-select">
                          <li>
                            <button type="button" :class="['btn btn-select', filter.sort_by == '1' ? 'active' : '']" @click="sortBy('1')">最新順</button>
                          </li>
                          <li>
                            <button type="button" :class="['btn btn-select', filter.sort_by == '2' ? 'active' : '']" @click="sortBy('2')">いいね件数順</button>
                          </li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>

                <div class="knowled-btn">
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="isShowPopupFilter = false">キャンセル</button>
                  <button type="button" class="btn btn-primary" @click="search(true, false)">検索</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div ref="listKnowledContent" class="listKnowled scrollbar">
        <ul v-if="knowledgeList.length > 0" ref="ul-content">
          <li v-for="(item, i) of knowledgeList" :key="item.knowledge_id" :scroll-attr="i">
            <div class="itemRibbon">
              <img v-show="item.new_label == '1'" src="@/assets/template/images/icon_ribbon-tag-new.svg" alt="" />
              <img v-show="item.update_label == '1'" src="@/assets/template/images/icon_ribbon-tag-update.svg" alt="" />
            </div>
            <div class="listKnowled-row">
              <div class="listKnowled-head">
                <div class="listKnowled-left">
                  <h2 class="listKnowled-title">
                    <span class="item"><img src="@/assets/template/images/icon_light-bulb01.svg" alt="" /></span>
                    <a v-if="item?.title?.length > 50" :title_log="item.title" @click="goToF01S02Detail(item.knowledge_id)">
                      {{ item.title.slice(0, 50) }}
                      {{ '...' }}
                    </a>
                    <a v-else @click="goToF01S02Detail(item.knowledge_id)">
                      {{ item.title }}
                    </a>
                  </h2>
                </div>
                <div class="listKnowled-right">
                  <div class="listKnowled-label">
                    <span class="spanLabel spanLabel--category">{{ item.knowledge_category_name }}</span>
                  </div>
                  <p class="listKnowled-id">
                    ナレッジID：<span class="idCode">{{ item.knowledge_id }}</span>
                  </p>
                  <div class="viewLike">
                    <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                    <span class="number-like">{{ item.knowledge_like }}</span>
                  </div>
                </div>
              </div>

              <div class="listKnowled-content">
                <div v-show="item.contents.length > 0" :id="i" class="listKnowled-text">
                  <span
                    :class="`knowled-content line-text-over ${
                      item.person_cd && item.last_update_datetime
                        ? 'line-plus1'
                        : (item.person_cd && !item.last_update_datetime) || (!item.person_cd && item.last_update_datetime)
                        ? 'line-plus'
                        : ''
                    }`"
                  >
                    {{ item.contents }}
                  </span>
                </div>
              </div>
              <div class="listKnowled-info">
                <ul>
                  <li v-if="item.last_update_datetime">
                    <span class="listKnowled-label">公開日：</span>
                    {{ formatFullDate(item.last_update_datetime) }}
                  </li>
                  <li class="listKnowled-li">
                    <div class="listKnowled-item"><span class="listKnowled-label">品目：</span>{{ item.product_name }}</div>
                    <div class="listKnowled-subject"><span class="listKnowled-label">診療科目分類：</span>{{ item.medical_subjects_group_name }}</div>
                    <!-- A8-->
                  </li>

                  <li><span class="listKnowled-label">施設区分分類：</span>{{ item.facility_type_group_name }}</li>
                  <!-- A9-->
                  <li class="listKnowled-liItem">
                    <span class="item">
                      <img class="svg" src="@/assets/template/images/icon_medical-hospital.svg" alt="" />
                    </span>
                    <p>
                      <span class="link" @click="redirectToH01S02(item)"> {{ item.facility_short_name }}</span>
                      <span v-if="item.department_name"> 　{{ item.department_name }} </span>
                    </p>
                  </li>

                  <!-- 10 -11-->
                  <li v-if="item.person_cd" class="listKnowled-liItem">
                    <span class="item">
                      <img src="@/assets/template/images/icon_user07.svg" alt="" />
                    </span>

                    <p>
                      <span v-if="item.person_name" class="link" @click="redirectToH02S02(item)"> {{ item.person_name }}</span>
                      <span v-if="item.display_position_name"> 　{{ item.display_position_name }} </span>
                    </p>
                  </li>

                  <li>
                    <!-- A7-->
                    <span class="item"><img src="@/assets/template/images/icon_maps.svg" alt="" /></span>　

                    {{ item.prefecture_name }}　{{ item.municipality_name }}
                    <!-- A12-->
                  </li>
                  <li v-if="getCreateUserName(item)?.length > 50" class="listKnowled-liItem" :title="getCreateUserName(item)">
                    <span class="item"><img src="@/assets/template/images/icon_user-edit01.svg" alt="" /></span>
                    <!--13-->
                    {{ getCreateUserName(item).slice(0, 50) }}
                    {{ '...' }}
                  </li>

                  <li v-else class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_user-edit01.svg" alt="" /></span>
                    <!--13-->
                    {{ getCreateUserName(item) }}
                  </li>
                </ul>
              </div>
            </div>
          </li>
        </ul>
        <EmptyData v-if="!isLoadDefault && knowledgeList.length === 0" custom-class="customClassEmpty" thumb-margin-top="20px" />
      </div>
    </div>
    <!-- pagination left single -->
    <div v-if="knowledgeList.length > 0" class="pagination pagination-custom">
      <div class="pagination-cases">全{{ pagination.totalItems || 'O' }}件</div>
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
      :show-close="true"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleConfirm="onCloseModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import F01S01Service from '@/api/F01/F01_S01_KnowledgeListService';

import PaginationTable from '../../../components/PaginationTable.vue';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05_S06_MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05S04FacilityCustomerSelection from '../../Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection.vue';
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';
import Z05_S02_ModalAreaSelection from '@/views/Z05/Z05_S02_Area_Selection/Z05_S02_Area_Selection';
import { markRaw } from 'vue';
import moment from 'moment';
import _ from 'lodash';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'F01_S01_KnowledgeList',
  components: {
    PaginationTable,
    Z05S01Organization,
    Z05_S06_MaterialSelection,
    Z05S04FacilityCustomerSelection, //popup,
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
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
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

      muniOrPreList: [],
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
        sort_by: '1',
        limit: '100',
        offset: '0',
        user_cd_raw: []
      },
      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },
      loadingPage: false,
      // isShowPopupFilter: false,
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

      filterDataZ05S03: {},
      paramZ05S04: {
        non_facility_flag: 0, // req
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

      prefectureCode: '',
      municipalityCode: '',
      onScrollTop: 0
    };
  },
  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'F01_S01_KnowledgeSearch') {
          let queryUrl = this._route('F01_S01_KnowledgeSearch')?.params;
          if (queryUrl && queryUrl?.knowledge_id) {
            this.resetFilter();
            this.filter.knowledge_id = queryUrl?.knowledge_id;
          }
          this.getScreenData();
        }
      }
    );
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'ナレッジ検索',
      icon: 'icon-light-group.svg',
      isShowBack: false
    });

    const isLoadingComponent = localStorage.getItem('isLoadingComponent');
    if (isLoadingComponent === 'true') {
      this.resetFilter();
    }

    let queryUrl = this._route('F01_S01_KnowledgeSearch')?.params;
    if (queryUrl && queryUrl?.knowledge_id) {
      this.resetFilter();
      this.filter.knowledge_id = queryUrl?.knowledge_id;
    }

    this.filterDataZ05S03.facilityCd = [this.filter.facility_cd];
    this.paramZ05S04.facility_cd = this.filter.facility_cd ? [this.filter.facility_cd] : [];

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.F01_S01_KnowledgeList || 0;
    this.pagination.curentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.F01_S01_KnowledgeList || 1;

    this.getScreenData();
  },
  methods: {
    setScrollTop() {
      let scrollTop = this.$refs.listKnowledContent ? this.$refs.listKnowledContent.scrollTop : 0;
      this.setScrollScreen('F01_S01_KnowledgeList', scrollTop);
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

    showDetail(item) {
      const ellipsis = document.getElementById(item?.knowledge_id);
      const a = document.getElementById(`show${item?.knowledge_id}`);
      if (a.innerText === 'もっと見る') {
        ellipsis.classList.remove('text-see-more');
        a.innerText = '';
      }
    },
    hideDetail(item) {
      const show = document.getElementById(`show${item?.knowledge_id}`);
      const ellipsis = document.getElementById(item?.knowledge_id);
      show.innerText = 'もっと見る';
      ellipsis.classList.add('text-see-more');
    },
    contentTextSeeMore(i, content) {
      const word = content;
      const count = word?.length > 0 && word?.length < 267;
      return count ? false : true;
      //   return html;
    },

    redirectToH02S02(item) {
      this.setScrollTop();
      let person_cd = item.person_cd;
      localStorage.setItem('activeMenu', 'F01_S01_KnowledgeSearch');
      this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
    },

    /** Modal */
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.paramsZ05S01 },
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
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
        width: this.currDevice() !== 'Desktop' ? '95%' : '55rem',
        props: {
          mode: 'single',
          orgCD: this.filterDataZ05S03.org_cd,
          userCD: this.filterDataZ05S03.user_cd,
          userName: this.filterDataZ05S03.user_name,
          selectGroupCd: this.filterDataZ05S03.select_group_id,
          targetProductCd: this.filterDataZ05S03.target_product_cd,
          facilityCategoryType: this.filterDataZ05S03.facility_category_type,
          prefectureCD: this.filterDataZ05S03.prefecture_cd,
          prefectureName: this.filterDataZ05S03.prefecture_name,
          municipalityCD: this.filterDataZ05S03.municipality_cd,
          municipalityName: this.filterDataZ05S03.municipality_name,
          facilityCd: this.filterDataZ05S03.facilityCd,
          facilityName: this.filterDataZ05S03.facilityName
        },
        destroyOnClose: true
      };
    },
    openModalZ05S04() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設個人選択',
        isShowModal: true,
        component: markRaw(Z05S04FacilityCustomerSelection),
        props: {
          propsPrams: { ...this.paramZ05S04, non_facility_flag: this.filter.facility_cd ? 1 : 0 },
          mode: 'single'
        },
        width: this.currDevice() !== 'Desktop' ? '90%' : '55rem',
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
          prefectureCode: this.prefectureCode,
          municipalityCode: this.municipalityCode
        }
      };
    },

    chooserUser(data, i) {
      const create_user_cd = data?.create_user_cd;
      const newData = { user_name: data.user_name, user_cd: data.create_user_cd, org_cd: data.org_cd };
      let color = document.querySelector(`#c_${create_user_cd}`);

      if (this.usersFromToLike.length > 0) {
        const index = this.usersFromToLike.findIndex((s) => s?.create_user_cd === create_user_cd);
        // remove data if alrealy
        if (index !== -1) {
          this.usersFromToLike.splice(index, 1);
        } else this.usersFromToLike[i] = data;
      } else {
        this.usersFromToLike[i] = data; // first add
      }
      setTimeout(() => {
        if (this.paramsZ05S01.userCdList.length > 0) {
          const index2 = this.paramsZ05S01.userCdList.findIndex((s) => s?.user_cd === create_user_cd);
          if (index2 !== -1) {
            this.paramsZ05S01.userCdList.splice(index2, 1);
            this.paramsZ05S01.orgCdList = this.paramsZ05S01.userCdList.map((s) => s.org_cd);

            this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
            this.filter.user_cd_raw = this.paramsZ05S01.userCdList;

            setTimeout(() => {
              color.style.background = '';
            });
          } else {
            this.paramsZ05S01.userCdList.push(newData);
            this.paramsZ05S01.orgCdList = this.paramsZ05S01.userCdList.map((s) => s.org_cd);

            this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
            this.filter.user_cd_raw = this.paramsZ05S01.userCdList;

            color.style.background = '#e4e4e4'; // add new
          }
        } else {
          this.paramsZ05S01.userCdList.push(newData);
          this.paramsZ05S01.orgCdList = this.paramsZ05S01.userCdList.map((s) => s.org_cd);

          this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
          this.filter.user_cd_raw = this.paramsZ05S01.userCdList;

          color.style.background = '#e4e4e4';
        }
      });
    },
    onResultModalZ05S01(e) {
      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        userCdList: e.userSelected,
        orgCdList: e.orgSelected.map((s) => s.org_cd)
      };
      this.filter.user_cd = e.userSelected
        .map((s) => s.user_cd)
        .concat(this.usersFromToLike.map((s) => s.create_user_cd))
        .filter((s) => s !== null);
      this.filter.user_cd_raw = e.userSelected
        .map((s) => ({ user_cd: s.user_cd, user_name: (s && s?.user_name) || 'text' }))
        .concat(this.usersFromToLike.map((s) => ({ user_cd: s.create_user_cd, user_name: (s && s?.user_name) || 'text' })))
        .filter((s) => s !== null);
      const userNotOrg = this.usersFromToLike.filter((s) => !s.org_cd).map((s) => ({ ...s, user_cd: s.create_user_cd }));
      this.paramsZ05S01.userCdList = this.paramsZ05S01.userCdList.concat(userNotOrg);
    },
    unique(arr) {
      var newArr = [];
      for (var i = 0; i < arr.length; i++) {
        if (!newArr.some((s) => s && s.create_user_cd === arr[i].create_user_cd)) {
          newArr.push(arr[i]);
        }
      }
      return newArr;
    },
    onResultModalZ05S03(e) {
      this.facilityList = e?.facilitySelectList.map((s) => ({ code: s.facility_cd, name: s.facility_short_name }));
      this.filter.facility_cd = this.facilityList[0]?.code;
      this.paramZ05S04.facility_cd = [this.facilityList[0]?.code];
      this.filterDataZ05S03 = {
        ...e.filterData,
        facilityCd: this.facilityList.length > 0 ? [this.facilityList[0].code] : [],
        facilityName: [e.filterData.facility_name]
      };
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
        person_cd: [data[0].person_cd],
        facility_name: [e.filterData.facility_name]
      };

      this.facilityList = data.map((s) => ({ code: s.facility_cd, name: s.facility_short_name }));
      this.filter.facility_cd = this.facilityList[0]?.code;

      this.paramsZ05S03 = {
        ...this.paramsZ05S03,
        facility_cd: this.facilityCd
      };
    },
    onResultModalZ05S02(e) {
      const municipalitySelected = e.municipalitySelected && e.municipalitySelected.municipality_cd ? e.municipalitySelected : null;
      const prefectureSelected = e.prefectureSelected && e.prefectureSelected.prefecture_cd ? e.prefectureSelected : null;
      if (municipalitySelected && !prefectureSelected) this.muniOrPreList = [municipalitySelected];
      this.filter.municipality_cd = municipalitySelected?.municipality_cd;
      this.filter.prefecture_cd = '';
      if (!municipalitySelected && prefectureSelected) this.muniOrPreList = [prefectureSelected];
      this.filter.municipality_cd = '';
      this.filter.prefecture_cd = prefectureSelected?.prefecture_cd;
      if (municipalitySelected && prefectureSelected) this.muniOrPreList = [municipalitySelected];
      this.filter.municipality_cd = municipalitySelected?.municipality_cd;
      this.filter.prefecture_cd = prefectureSelected?.prefecture_cd;
      this.muniOrPreList = this.muniOrPreList.map((data) => ({
        code: data.municipality_cd || data.prefecture_cd,
        name: data.municipality_name || data.prefecture_name
      }));

      this.prefectureCode = e.prefectureSelected?.prefecture_cd;
      this.municipalityCode = e.municipalitySelected?.municipality_cd;
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
        this.paramsZ05S01.orgCdList = this.paramsZ05S01.userCdList.map((s) => s.org_cd);

        this.filter.user_cd = this.paramsZ05S01.userCdList.map((s) => s.user_cd);
        this.filter.user_cd_raw = this.paramsZ05S01.userCdList;

        let color = document.querySelector(`#c_${item.user_cd}`);
        color ? (color.style.background = '') : '';
      }
      if (screen === 'Z05_S02') {
        const index = this.muniOrPreList.findIndex((s) => s.code === item.code);
        this.muniOrPreList.splice(index, 1);
        this.filter.municipality_cd = '';
        this.filter.prefecture_cd = '';
        this.prefectureCode = '';
        this.municipalityCode = '';
      }
      if (screen === 'Z05_S03') {
        const index = this.facilityList.findIndex((s) => s.user_cd === item.user_cd);
        this.facilityList.splice(index, 1);
        this.filter.facility_cd = '';
        this.filterDataZ05S03 = {};

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
        sort_by: '1',
        limit: '100',
        offset: '0'
      };
      // modal result
      this.productCdList = [];
      this.facilityList = [];
      this.personList = [];
      this.paramsZ05S01.userCdList = [];
      this.muniOrPreList = [];
      this.usersFromToLike = [];
    },
    /** API */
    async getScreenData() {
      this.loadingPage = true;
      const data = this.getScreen('F01_S01_KnowledgeList');

      try {
        const raw = await F01S01Service.getDataScreen();
        const res = raw.data;
        this.medical_subjects_group = res.data.medical_subjects_group;
        this.facility_type_group = res.data.facility_type_group;

        this.knowledge_category = res.data.knowledge_category;

        this.knowledge_status = res.data.knowledge_status;
        this.topLike = data ? data['topLike'] : res.data.top_like;
        this.search(false, true);
      } catch (err) {
        return;
      }
    },
    async getList(filter, isFilter, isLoadDefault) {
      this.loadingPage = true;
      this.isLoadDefault = isLoadDefault;
      try {
        const raw = await F01S01Service.getData(filter);
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
        this.knowledgeList = [];
        if (err?.response?.data?.message) {
          this.$notify({ message: err?.response?.data?.message, customClass: 'error' });
        } else {
          this.$notify({ message: '内部エラーが発生しました。システム管理者に連絡してください。', customClass: 'error' });
        }
      } finally {
        await new Promise((resolve) => setTimeout(resolve, 500));
        this.contentTextSeeMore();

        this.setCurrentPageScreen('F01_S01_KnowledgeList', this.pagination.curentPage);

        if (this.$refs.listKnowledContent) {
          if (this.onScrollTop && !isFilter) {
            this.$refs.listKnowledContent.scrollTop = this.onScrollTop;
          } else {
            this.$refs.listKnowledContent.scrollTop = 0;
          }
        }
        this.isLoadDefault = false;
        this.loadingPage = false;
        this.isShowPopupFilter = false;
        this.js_pscroll(0.5);

        const childRouter = JSON.parse(localStorage.getItem('router'));
        let routes = this.decodeData(localStorage.getItem('$_D')) || [];

        localStorage.setItem('isLoadingComponent', false);

        if (routes && routes.length > 0) {
          const indexRouteKnowledge = routes.findIndex((x) => x.name === 'F01_S01_KnowledgeSearch');

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
            name: 'F01_S01_KnowledgeSearch',
            params: {},
            path: childRouter.find((x) => x.name === 'F01_S01_KnowledgeSearch')?.path
          };

          routes[0] = routeKnowledge;
          localStorage.setItem('$_D', this.enCodeData(routes));
        }
      }
    },
    showFlagUpdate(item) {
      // show update tag
      let isFlag = true;
      const updateTimeNumber = item.last_update_datetime_updated ? Number(item.last_update_datetime_updated) : 0; // 14;
      const updateTime = item.last_update_datetime ? new Date(item.last_update_datetime) : null; // 01;
      const curentDate = new Date().getTime();
      if (updateTime) {
        updateTime.setDate(updateTime.getDate() + updateTimeNumber);
        // if current date more than update date, tag will hide
        if (curentDate < updateTime.getTime()) {
          isFlag = false;
        }
      }
      return isFlag;
    },
    showFlagCreate(item) {
      let isFlag = false;
      const create_datetime_new = item.create_datetime_new ? Number(item.create_datetime_new) : 0;
      const dayCurrent = new Date();
      const createTime = item.create_datetime ? new Date(item.create_datetime) : null;
      if (createTime) {
        createTime.setDate(createTime.getDate() + create_datetime_new);
        if (dayCurrent.getTime() < createTime.getTime()) {
          isFlag = true;
        }
      }
      return isFlag;
    },
    getCreateUserName(item) {
      const createUser = item?.create_user_cd;
      const createUserTogether = item?.create_user_cd_together;
      let name = createUser;
      if (createUser.length > 0 && createUserTogether.length > 0) name = createUser + ', ' + createUserTogether;
      if (createUser.length === 0 && createUserTogether.length > 0) name = createUserTogether;

      return name;
    },
    handleCurrentChange(val) {
      this.onScrollTop = 0;
      this.pagination.curentPage = val;
      this.filter = { ...this.filter, offset: this.pagination.curentPage - 1, limit: 100 };
      // this.$refs['knowledgeScroll'].scrollTop = 0;

      this.getList(this.filter, true, false);
    },
    goToF01S02Detail(id) {
      this.setScrollTop();
      this.$router.push({ name: 'F01_S02_KnowledgeDetails', params: { knowledge_id: id } });
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
        height: calc(100vh - 400px);
      }
      margin-left: -20px;
    }
    .form-knowled {
      height: calc(100vh - 250px);
      @media (max-width: $viewport_tablet) {
        height: calc(100vh - 450px);
      }
      padding: 0 20px;
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        > li {
          width: 50%;
          padding-left: 10px;
          margin-top: 3px;
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
            &:hover {
              background: #eeeeee;
              cursor: pointer;
            }

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
            margin: -2px 2px 0 0;
            img {
              width: 15px;
              height: 15px;
            }
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
      margin-top: 24px;
      text-align: center;
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
      padding: 32px 20px 20px 40px;
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
            }
            &.spanLabel--category {
              font-weight: 500;
              background: #e2e4ff;
              color: #555fb0;
              border-radius: 20px;
            }
          }
        }
      }
    }
  }
  .viewLike {
    display: flex;

    justify-content: flex-end;
    align-items: flex-end;
    .item-like {
      min-width: 15px;
      margin: 0px 4px 2px 0;
    }
    .number-like {
      color: #727f88;
      font-size: 1rem;
      font-weight: 700;
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
      justify-content: space-between;
      width: 100%;
      margin-bottom: 20px;
      display: flex;

      .listKnowled-left {
        flex-grow: 1;
        .listKnowled-title {
          gap: 7px;
          display: flex;
          align-items: flex-start;
          align-content: flex-start;
          justify-content: flex-start;
          flex-wrap: nowrap;
          .item {
            flex: 0 0 24px;

            @media (max-width: 991px) {
              flex: 0 0 18px;
            }
          }
          a {
            color: #448add;
            font-size: 22px;
            cursor: pointer;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            @media (max-width: 991px) {
              font-size: 18px;
            }
          }
        }
      }
      .listKnowled-right {
        display: flex;
        min-width: 300px;
        justify-content: flex-end;
        gap: 10px;
        .listKnowled-id {
          color: #727f88;
          .idCode {
            color: #2d3033;
          }
        }
        .listKnowled-label {
          .spanLabel {
            padding: 2px 10px;
            &:last-child {
              margin-right: 0;
            }
            &.spanLabel--status {
              background: #daf8dc;
              color: #28a470;
              border-radius: 2px;
            }
            &.spanLabel--category {
              font-weight: 500;
              background: #e2e4ff;
              color: #555fb0;
              border-radius: 20px;
              width: 100%;
              text-align: center;
            }
          }
        }
        .viewLike {
          display: block;
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
    .listKnowled-id {
      color: #727f88;
      .idCode {
        color: #2d3033;
      }
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
.knowled-content {
  position: relative;
  white-space: normal;
}
.line-text-over {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 6;
  -webkit-box-orient: vertical;
  text-shadow: none;
  text-transform: capitalize;

  &.line-plus {
    -webkit-line-clamp: 7;
  }
  &.line-plus1 {
    -webkit-line-clamp: 8;
  }
}
.link {
  font-weight: bold;
}
</style>
