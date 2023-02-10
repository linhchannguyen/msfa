<template>
  <div v-load-f5="true" class="personalInfo scrollbar">
    <!-- <img src="@/assets/template/images/data/calendar.png" alt="" /> -->

    <div class="personalInfo-calendar">
      <VCalendaRange
        :mode="datePicker.mode"
        :range-date="rangeDate"
        :selected-start="datePicker.start"
        :selected-end="datePicker.end"
        :year="datePicker.year"
        @click="onBind"
        @touchstart="onBind"
        @changeMode="changeMode"
      />
    </div>
    <div class="personalInfo-box">
      <div class="daily-colInfo">
        <div class="daily-filter filter-wrapper">
          <button class="btn btn-link btn-filter" type="button" @click="showPopup()">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
          </button>
          <div :class="[dropdownFilter + ' dropdown-filter--personal', isShowPopupFilter ? 'show' : '']">
            <div class="item-filter btn-close-filter" @click="closePopup()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
            </div>
            <h2 class="title-filter">検索条件</h2>
            <div class="groupForm">
              <div class="groupForm-personal">
                <label class="groupForm-label">施設個人</label>
                <div class="groupForm-control">
                  <div class="form-icon icon-right">
                    <span title_log="施設個人" class="icon log-icon" @click="openModalZ05S04()">
                      <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
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
                          @close="handleRemoveFacility(item)"
                        >
                          {{ item.name }}
                        </el-tag>
                      </div>
                    </div>
                    <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                  </div>
                </div>
              </div>
              <div class="groupForm-personal">
                <label class="groupForm-label">品目</label>
                <div class="groupForm-control">
                  <div class="form-icon icon-right">
                    <span title_log="品目" class="icon log-icon" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                      <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                    </span>
                    <div v-if="productList.length > 0" class="form-control d-flex align-items-center">
                      <div class="block-tags">
                        <el-tag
                          v-for="item in productList"
                          :key="item.code"
                          :label="item.name"
                          :value="item.name"
                          class="el-tag-custom"
                          closable
                          @close="handleRemoveProduct(item)"
                        >
                          {{ item.name }}
                        </el-tag>
                      </div>
                    </div>
                    <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                  </div>
                </div>
              </div>

              <div class="groupForm-personal">
                <label class="groupForm-label">チャネル</label>
                <div class="groupForm-controlChannel">
                  <ul>
                    <li>
                      <label class="custom-control custom-checkbox custom-control--bordGreen">
                        <input
                          class="custom-control-input"
                          type="checkbox"
                          :checked="channelTypeList.includes('10')"
                          value="10"
                          @change="onChannelType($event, 0)"
                        />
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">面談</span>
                      </label>
                    </li>
                    <li>
                      <label class="custom-control custom-checkbox custom-control--bordGreen">
                        <input
                          class="custom-control-input"
                          type="checkbox"
                          :checked="channelTypeList.includes('20')"
                          value="20"
                          @change="onChannelType($event, 1)"
                        />
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">説明会</span>
                      </label>
                    </li>
                    <li>
                      <label class="custom-control custom-checkbox custom-control--bordGreen">
                        <input
                          class="custom-control-input"
                          type="checkbox"
                          :checked="channelTypeList.includes('30')"
                          value="30"
                          @change="onChannelType($event, 2)"
                        />
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description custom-control--bordGreen">講演会</span>
                      </label>
                    </li>
                    <li>
                      <label class="custom-control custom-checkbox custom-control--bordGreen">
                        <input
                          class="custom-control-input"
                          type="checkbox"
                          :checked="channelTypeList.includes('40')"
                          value="40"
                          @change="onChannelType($event, 3)"
                        />
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">動画視聴</span>
                      </label>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="btnFilter-personal">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelPopup">キャンセル</button>
                <button type="button" class="btn btn-primary btn-filter-submit" @click="onSearch(false)">検索</button>
              </div>
            </div>
          </div>
        </div>
        <div :class="['dailyList', timelines && timelines.length === 1 ? '' : '']">
          <ul ref="detailScrollTop" class="dailyList_ul scrollbar">
            <li v-for="(item, index) in timelines" :id="index" :key="item" class="dailyList_li">
              <div class="dailyList-calendar">
                <span class="label">{{ formatFullDate(item.start_datetime) }} ({{ getDay(item.start_datetime) }})</span>
              </div>
              <ul class="dailyList-sub">
                <template v-for="(itemChild, i) of item.content" :key="itemChild">
                  <!--面談-->
                  <li v-if="itemChild.channel_type === '10'">
                    <div class="dailyList-flex">
                      <div class="dailyList-dateTime">
                        <span
                          v-if="formatTimeHourMinute(itemChild.start_datetime) !== '0:00' && formatTimeHourMinute(itemChild.start_datetime) !== '23:59'"
                          class="time"
                          >{{ formatTimeHourMinute(itemChild.start_datetime) }}～{{ formatTimeHourMinute(itemChild.end_datetime) }}
                        </span>
                        <span v-else class="time" style="text-align: center">終日</span>
                        <div class="iconItem">
                          <span class="item"><img src="@/assets/template/images/icon_interview-daily.svg" alt="" /></span>
                        </div>
                      </div>
                      <div class="dailyList-content">
                        <div class="dailyList-box">
                          <div v-if="itemChild.off_label_flag === 1" class="dailyList-label">
                            <span class="span-label span-label--off">
                              <span class="span-label-item"><img src="@/assets/template/images/icon_exclamation-circle.svg" alt="" /></span>
                              オフラベルあり
                            </span>
                            <span class="span-label span-label--knowledge">
                              <span class="span-label-item"><img src="@/assets/template/images/icon_knowledge.svg" alt="" /></span>
                              ナレッジ登録済
                            </span>
                          </div>
                          <div class="dailyList-group">
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt interview">
                                <p class="dailyList-info-label">面談先</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p v-if="itemChild.department_name" class="dailyList-spanBlue dailyList-bold" @click="goPersonDetail(itemChild)">
                                  {{ itemChild.department_name }} {{ itemChild.person_name }}
                                </p>
                                <p v-else class="dailyList-spanBlue dailyList-bold" style="color: #5f6b73; cursor: default">
                                  {{ itemChild.department_name }} {{ itemChild.person_name }}
                                </p>
                              </div>
                            </div>
                            <div v-for="(dataProtuct, y) of itemChild.channel_detail" :id="'accordion' + y" :key="dataProtuct" class="daily-accordion">
                              <h2
                                class="article-head detail"
                                data-toggle="collapse"
                                :data-target="'#article-' + index + y + dataProtuct.detail_order + dataProtuct.channel_detail_id"
                              >
                                <span> {{ dataProtuct.detail_order }}. {{ dataProtuct.content_name }} {{ dataProtuct.product_name }}</span>
                              </h2>
                              <div
                                :id="'article-' + index + y + dataProtuct.detail_order + dataProtuct.channel_detail_id"
                                class="collapse article-body"
                                :data-parent="'#accordion' + y"
                              >
                                <div class="daily-article">
                                  <ul>
                                    <li>
                                      <span class="daily-article-tlt">フェーズ</span>
                                      <span class="daily-article-label">{{ dataProtuct.phase_name }}</span>
                                    </li>
                                    <li>
                                      <span class="daily-article-tlt">反応</span>
                                      <span class="daily-article-label">{{ dataProtuct.reaction_name }}</span>
                                    </li>
                                  </ul>
                                  <p class="daily-article-text">
                                    {{ dataProtuct.note }}
                                  </p>
                                </div>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt interview">
                                <p class="dailyList-info-label">面談者</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanBlue dailyList-bold">
                                  <span @click="goAccountSetting(itemChild)"> {{ itemChild.user_name }} ({{ itemChild.user_post_type }})</span>
                                </p>
                                <p class="dailyList-add">
                                  <span class="dailyList-item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                                  {{ itemChild.org_label }}
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="viewMore">
                            <a class="dailyList-spanBlue dailyList-bold" @click="callScreenA01_S04(itemChild)"> 詳細を見る </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                  <!--説明会-->
                  <li v-if="itemChild.channel_type === '20'">
                    <div class="dailyList-flex">
                      <div class="dailyList-dateTime">
                        <span
                          v-if="formatTimeHourMinute(itemChild.start_datetime) !== '0:00' && formatTimeHourMinute(itemChild.start_datetime) !== '23:59'"
                          class="time"
                          >{{ formatTimeHourMinute(itemChild.start_datetime) }}～{{ formatTimeHourMinute(itemChild.end_datetime) }}
                        </span>
                        <span v-else class="time" style="text-align: center">終日</span>
                        <div class="iconItem">
                          <span class="item"><img src="@/assets/template/images/icon_interview-daily02.svg" alt="" /></span>
                        </div>
                      </div>
                      <div class="dailyList-content">
                        <div class="dailyList-box">
                          <div class="dailyList-group">
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt briefing">
                                <p class="dailyList-info-label dailyList-gray">説明会名</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanBlue dailyList-bold">
                                  <span @click="goBriefingSessionInput(itemChild)"> {{ itemChild?.title }}</span>
                                </p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt briefing">
                                <p class="dailyList-info-label dailyList-gray">説明会区分</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.briefing_type_name }}</p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt briefing">
                                <p class="dailyList-info-label dailyList-gray">品目</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.product_name }}</p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt briefing">
                                <p class="dailyList-info-label dailyList-gray">対象施設</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.briefing_facility_short_name }}</p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt briefing">
                                <p class="dailyList-info-label dailyList-gray">実施者</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanBlue dailyList-bold">
                                  <span @click="goAccountSetting(itemChild)">
                                    {{ itemChild.user_name }} <span v-show="itemChild.user_post_type?.length"> ({{ itemChild.user_post_type }}) </span></span
                                  >
                                </p>
                                <p class="dailyList-add">
                                  <span class="dailyList-item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                                  {{ itemChild.org_label }}
                                </p>
                              </div>
                            </div>
                            <div v-if="itemChild.note && itemChild?.note.length > 0" id="accordion-3" class="daily-accordion">
                              <h2 class="article-head" data-toggle="collapse" :data-target="'#article-3' + index + i"><span>特記事項</span></h2>
                              <div
                                v-show="itemChild.note && itemChild?.note.length > 0"
                                :id="'article-3' + index + i"
                                class="collapse article-body"
                                data-parent="#accordion-3"
                              >
                                <div class="daily-article">
                                  <p class="daily-article-text">
                                    {{ itemChild.note }}
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                  <!-- 講演会 -->
                  <li v-if="itemChild.channel_type === '30'">
                    <div class="dailyList-flex">
                      <div class="dailyList-dateTime">
                        <span
                          v-if="formatTimeHourMinute(itemChild.start_datetime) !== '0:00' && formatTimeHourMinute(itemChild.start_datetime) !== '23:59'"
                          class="time"
                          >{{ formatTimeHourMinute(itemChild.start_datetime) }}～{{ formatTimeHourMinute(itemChild.end_datetime) }}
                        </span>
                        <span v-else class="time" style="text-align: center">終日</span>
                        <div class="iconItem">
                          <span class="item"><img src="@/assets/template/images/icon_interview-daily01.svg" alt="" /></span>
                        </div>
                      </div>
                      <div class="dailyList-content">
                        <div class="dailyList-box">
                          <!-- <div class="dailyList-btnCmt">
                            <button class="btn btn--icon btn--hasNoti">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_comment.svg')" alt="" />
                            </button>
                          </div> -->
                          <div class="dailyList-group">
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt convention">
                                <p class="dailyList-info-label dailyList-gray">講演会名</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanBlue dailyList-bold">
                                  <span @click="goLectureInput(itemChild)"> {{ itemChild?.title }} </span>
                                </p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt convention">
                                <p class="dailyList-info-label dailyList-gray">講演会区分</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.convention_type_name }}</p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt convention">
                                <p class="dailyList-info-label dailyList-gray">品目</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.product_name }}</p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt convention">
                                <p class="dailyList-info-label dailyList-gray">対象組織</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.org_label }}</p>
                              </div>
                            </div>
                            <div v-if="itemChild.note && itemChild?.note.length > 0" id="accordion-2" class="daily-accordion">
                              <h2 class="article-head" data-toggle="collapse" :data-target="'#article-2' + index + i"><span>特記事項</span></h2>
                              <div
                                v-show="itemChild.note && itemChild?.note.length > 0"
                                :id="'article-2' + index + i"
                                class="collapse article-body"
                                data-parent="#accordion-2"
                              >
                                <div class="daily-article">
                                  <p class="daily-article-text">
                                    {{ itemChild.note }}
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>

                  <!--外部サービス-->
                  <li v-if="itemChild.channel_type == '40'">
                    <div class="dailyList-flex">
                      <div class="dailyList-dateTime">
                        <span
                          v-if="formatTimeHourMinute(itemChild.start_datetime) !== '0:00' && formatTimeHourMinute(itemChild.start_datetime) !== '23:59'"
                          class="time"
                          >{{ formatTimeHourMinute(itemChild.start_datetime) }}～{{ formatTimeHourMinute(itemChild.end_datetime) }}
                        </span>
                        <span v-else class="time" style="text-align: center">終日</span>
                        <div class="iconItem">
                          <span class="item"><img src="@/assets/template/images/icon_interview-daily03.svg" alt="" /></span>
                        </div>
                      </div>
                      <div class="dailyList-content">
                        <div class="dailyList-box">
                          <div class="dailyList-group">
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt external">
                                <p class="dailyList-info-label dailyList-gray">視聴者</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanBlue dailyList-bold">
                                  <span @click="goPersonDetail(itemChild)"> {{ itemChild.department_name }} {{ itemChild.person_name }} </span>
                                </p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt external">
                                <p class="dailyList-info-label dailyList-gray">品目</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.product_name }}</p>
                              </div>
                            </div>
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt external">
                                <p class="dailyList-info-label dailyList-gray">コンテンツ</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanGray">{{ itemChild.content }}</p>
                              </div>
                            </div>
                            <div v-if="itemChild.note && itemChild?.note.length > 0" id="accordion-4" class="daily-accordion">
                              <h2 class="article-head" data-toggle="collapse" :data-target="'#article-4' + index + i"><span>特記事項</span></h2>
                              <div
                                v-show="itemChild.note && itemChild?.note.length > 0"
                                :id="'article-4' + index + i"
                                class="collapse article-body"
                                data-parent="#accordion-4"
                              >
                                <div class="daily-article">
                                  <p class="daily-article-text">
                                    {{ itemChild.note }}
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </template>
              </ul>
            </li>
            <!-- No data -->
            <EmptyData v-if="timelines.length === 0 && !isLoadDefault" title="該当するデータがありません。" icon="amico" custom-class="customClassEmpty" />
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!--Modal-->
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="true"
    @close="closeModal()"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleConfirm="onCloseModal"></component>
    </template>
  </el-dialog>
</template>

<script>
import H01_S05_FacilityDetailsTimeline from '@/api/H01/H01_S05_FacilityDetailsTimeline';
import { markRaw } from 'vue';
import _H02PersonalDetails from '../../H02/H02_PersonalDetails/H02_PersonalDetails.vue';
import _A01S04InterviewDetailedInput from '../../A01/A01_S04_InterviewDetailedInput/A01_S04_InterviewDetailedInput.vue';
import Z04S01AccountSetting from '../../Z04/Z04_S01_AccountSetting/Z04_S01_AccountSetting.vue';
import C01S02LectureInput from '../../C01/C01_S02_LectureInput/C01_S02_LectureInput.vue';
import B01S02BriefingSessionInput from '../../B01/B01_S02_BriefingSessionInput/B01_S02_BriefingSessionInput.vue';
import Z05S04FacilityCustomerSelection from '../../Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection.vue';
import Z05S06MaterialSelection from '../../Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection.vue';
import VCalendaRange from '../../../components/VCalendaRange.vue';
import moment from 'moment';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'H01_S05_FacilityDetailsTimeline',
  components: {
    _H02PersonalDetails, // scr
    _A01S04InterviewDetailedInput, //scr
    Z04S01AccountSetting, // scr
    C01S02LectureInput, // scr
    B01S02BriefingSessionInput, // scr
    Z05S04FacilityCustomerSelection, //popup
    Z05S06MaterialSelection, //popup
    VCalendaRange
  },
  mixins: [filter_popup],
  props: {
    changetab: {
      type: String,
      default: '4'
    },
    dropdownFilter: {
      type: String,
      default: 'dropdown-menu dropdown-filter'
    },
    checkLoading: [Boolean]
  },

  data() {
    return {
      isLoadDefault: true,
      loading: false,
      // isShowPopupFilter: false,
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
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      user_login_cd: '',
      filter: {
        facility_cd: '',
        person_cd: '',
        product_cd: '',
        start_datetime: '',
        end_datetime: '',
        channel_type: '10,20,30,40'
      },
      personList: [],
      channelTypeList: ['10', '20', '30', '40'],
      productList: [],
      timelines: [],
      screenData: null,
      rangeDate: { start: new Date(new Date().setMonth(new Date().getUTCMonth() - 1)), end: new Date() },
      datePicker: {
        end: null,
        start: null,
        year: new Date().getFullYear(),
        start_date_month: '',
        end_date_month: '',
        fullYearStartMonth: '',
        fullYearEndMonth: '',
        mode: 'day',
        singleDate: {
          startDate: '',
          endDate: ''
        }
      },
      onTop: 0
    };
  },
  watch: {
    changetab: function (value) {
      // eslint-disable-next-line eqeqeq
      if (value == 4) {
        this.filter.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';
        this.filter.channel_type = '10,20,30,40';
        this.filter.start_datetime = this.datePicker.singleDate.startDate;
        this.getTimeLine(this.filter, true);
        this.getScreenData();
      }
    }
  },
  created() {
    this.user_login_cd = this.getCurrentUser().user_cd;
    this.filter.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';
    // eslint-disable-next-line eqeqeq
    if (this.changetab == 4) {
      const data = this.getScreen('H01_S05_FacilityDetailsTimeline');
      const checkValid = (date) => (date ? moment(date).format('YYYY/M/D') : moment().format('YYYY/M/D'));
      if (data) {
        // eslint-disable-next-line no-undef
        const checkNull = (name) => data['filter'] && data['filter'][name] && data['filter'][name].length > 0;
        this.filter.start_datetime = checkNull('start_datetime') ? data['filter']['start_datetime'] : checkValid(this.rangeDate.start);
        this.filter.end_datetime = checkNull('end_datetime') ? data['filter']['end_datetime'] : checkValid(this.rangeDate.end);

        this.getTimeLine(this.filter, true);
        this.getScreenData();
      } else {
        this.filter.start_datetime = checkValid(this.rangeDate.start);
        this.filter.end_datetime = checkValid(this.rangeDate.end);
        this.getTimeLine(this.filter, true);
        this.getScreenData();
      }
    }
    this.setHeighTimeline();
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '施設詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    const scroll = document.querySelector('.dailyList');
    scroll.addEventListener('scroll', () => {
      const ul = document.querySelector('.dailyList > ul');
      ul.style.height = 'auto';
    });
    this.onTop = JSON.parse(localStorage.getItem('ScrollTopScreen'));
  },
  methods: {
    setHeighTimeline() {
      const scroll = document.querySelector('.scrollbar');
      if (scroll) {
        scroll.addEventListener('scroll', () => {
          const heighTimeline = document.querySelector('.dailyList > ul');
          heighTimeline.style.height = 'auto';
        });
      }
    },
    /** Date picker */
    onBind(event) {
      this.setScrollScreen('h01S05TimeLine', 0);
      this.onTop = JSON.parse(localStorage.getItem('ScrollTopScreen'));
      this.filter.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';
      this.filter.channel_type = this.channelTypeList.filter((s) => s !== '').join();
      this.filter.product_cd = this.productList.map((s) => s.code).join();
      this.filter.person_cd = this.personList.length > 0 ? this.personList.map((s) => s.code).join() : '';
      if (event.mode === 'monthly') {
        this.datePicker.year = event.year;
        this.datePicker.fullYearEndMonth = event.fullYearEndMonth;
        this.datePicker.fullYearStartMonth = event.fullYearStartMonth;
      } else {
        if (event.callApi && event.startDate && event.endDate) {
          this.filter.start_datetime = event.startDate;
          this.filter.end_datetime = event.endDate;
          this.search(false);
        } else {
          if (event.getAll) {
            this.getTimeLine(this.filter, false);
          }
        }

        this.datePicker.singleDate = event;
      }
    },
    changeMode(event) {
      this.datePicker.mode = event;
    },
    onSearch(isLoadDefault) {
      this.filter.start_datetime = this.datePicker.singleDate.startDate || moment(this.rangeDate.start).format('YYYY/M/D');
      this.filter.end_datetime = this.datePicker.singleDate.endDate || moment(this.rangeDate.end).format('YYYY/M/D');
      this.filter.product_cd = this.productList.map((s) => s.code).join();
      this.filter.person_cd = this.personList.length > 0 ? this.personList.map((s) => s.code).join() : '';
      this.search(isLoadDefault);
      if (this.$refs['detailScrollTop']) {
        this.$refs['detailScrollTop'].scrollTop = 0;
      }
    },
    /** Date picker */
    /** API Start */
    getTimeLine(param, isLoadDefault) {
      if (!param.facility_cd) {
        param.facility_cd = localStorage.getItem('facility_cd_prev');
      }

      this.isLoadDefault = isLoadDefault;
      this.loading = true;
      this.$emit('changeLoading', true);
      H01_S05_FacilityDetailsTimeline.fetchTimeLineService(param)
        .then((res) => {
          if (res) {
            const ul = document.querySelector('.dailyList > ul');
            ul.style.height = '100%';
            this.timelines = [];
            this.timelines = res;
            this.cancelPopup();
          }
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          if (this.onTop && this.$refs['detailScrollTop']) {
            this.$refs['detailScrollTop'].scrollTop = this.onTop.h01S05TimeLine ? this.onTop.h01S05TimeLine : 0;
          }
          this.loading = false;
          this.$emit('changeLoading', false);
          this.isLoadDefault = false;
          this.js_pscroll(0.4);
        });
    },
    getScreenData() {
      H01_S05_FacilityDetailsTimeline.getScreenDataService()
        .then((res) => {
          if (res) {
            this.screenData = res;
          }
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }));
    },
    search(isLoadDefault) {
      this.filter.start_datetime = this.formatFullDate(this.filter.start_datetime);
      this.filter.end_datetime = this.formatFullDate(this.filter.end_datetime);
      this.filter.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';
      this.filter.channel_type = this.channelTypeList.filter((s) => s !== '').join();
      this.getTimeLine(this.filter, isLoadDefault);
    },
    getWeek(item) {
      const week = new Date(item).getDay();
      if (item) {
        switch (week) {
          case 0:
            return '日';
          case 1:
            return '月';
          case 2:
            return '火';
          case 3:
            return '水';
          case 4:
            return '木';
          case 5:
            return '金';
          case 6:
            return '土';
        }
      }
    },
    /** API end */

    /** Modal */
    goPersonDetail(item) {
      this.setScrollTop();
      this.$router.push({
        name: 'H02_PersonalDetails',
        params: {
          person_cd: item.person_cd
        },
        query: {
          tab: '1'
        }
      });
    },

    callScreenA01_S04(item) {
      let params = {
        call_id: item.channel_id,
        schedule_id: item.schedule_id
      };
      A01_S04_Service.checkInterviewDetailInput(params)
        .then(() => {
          this.goInterViewDetail(params);
        })
        .catch(() => {
          this.$notify({ message: '面談情報が削除されたため、詳細を見ることができません。', customClass: 'error' });
        })
        .finally(() => {});
    },

    goInterViewDetail(params) {
      this.setScrollTop();
      this.$router.push({
        name: 'A01_S04_InterviewDetailedInput',
        params: params
      });
    },

    goAccountSetting(item) {
      this.setScrollTop();
      this.$router.push({ name: 'Z04_S01_AccountSettings', params: { user_cd: item.user_cd } });
    },
    goLectureInput(item) {
      this.setScrollTop();
      this.$router.push({
        name: 'C01_S02_LectureInput',
        params: {
          convention_id: item?.convention_id,
          schedule_id: item.schedule_id
        }
      });
    },
    goBriefingSessionInput(item) {
      this.setScrollTop();
      this.$router.push({
        name: 'B01_S02_BriefingSessionInput',
        params: {
          // call_id: item.channel_id,
          briefing_id: item.briefing_id
        }
      });
    },

    openModalZ05S04() {
      this.paramZ05S04.facility_cd = [this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : ''];

      this.modalConfig = {
        ...this.modalConfig,
        title: '施設個人選択',
        isShowModal: true,
        component: markRaw(Z05S04FacilityCustomerSelection),
        props: {
          propsPrams: this.paramZ05S04,
          mode: 'single'
        },
        width: this.currDevice() !== 'Desktop' ? '90%' : '55rem',
        customClass: 'custom-dialog',
        destroyOnClose: true
      };

      // this.changeTrueClassHeader();
    },
    closeModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        component: markRaw(Z05S06MaterialSelection),
        props: {
          mode: 'single',
          categoryCode: '',
          classificationCode: ''
        },
        width: '33rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        destroyOnClose: true
      };
    },
    onResultModal(data) {
      const screen = data?.screen;
      switch (screen) {
        case 'Z05_S04':
          // eslint-disable-next-line no-case-declarations
          let facilitySelected = data.facilityPersonalSelected;
          this.personList = facilitySelected.map((s) => ({ code: s.person_cd, name: s.person_name }));
          this.filter.person_cd = facilitySelected.length > 0 ? facilitySelected.map((s) => s.person_cd).join() : '';

          this.paramZ05S04 = {
            ...data.filterData,
            facility_cd: data.facilityPersonalSelected.map((x) => x.facility_cd),
            person_cd: data.facilityPersonalSelected.map((x) => x.person_cd),
            facility_name: [data.filterData.facility_name]
          };

          this.onCloseModal();
          break;
        case 'Z05_S06':
          // eslint-disable-next-line no-case-declarations
          let datas = data?.dataSelected?.length > 0 ? data?.dataSelected : {};
          // this.filter.product_cd = data?.product_cd;
          this.productList = datas.map((s) => ({ code: s.product_cd, name: s.product_name }));
          this.filter.product_cd = datas.map((s) => s.product_cd).join();
          this.onCloseModal();
          break;
        default:
          this.onCloseModal();
          break;
      }
    },
    /** End Modal */
    /** Filter Mode */
    handleRemoveProduct(item) {
      this.productList = this.productList.filter((s) => s.code !== item.code);
      this.filter.product_cd = this.productList.map((s) => s.code).join();
    },
    handleRemoveFacility(item) {
      this.personList = this.personList.filter((s) => s.code !== item.code);
      this.filter.person_cd = this.personList.length > 0 ? this.personList.map((s) => s.code).join() : '';
      this.paramZ05S04 = this.paramZ05S04Default();
    },
    onChannelType(event, index) {
      if (event.target.checked) {
        this.channelTypeList[index] = event.target.value;
      } else {
        this.channelTypeList[index] = '';
      }
      this.filter.channel_type = this.channelTypeList.filter((s) => s !== '').join();
    },
    showPopup() {
      this.isShowPopupFilter = true;
    },
    closePopup() {
      this.isShowPopupFilter = false;
    },
    cancelPopup() {
      this.closePopup();
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    resetFilter() {
      this.filter = {
        facility_cd: '',
        person_cd: '',
        product_name: '',
        start_datetime: '',
        end_datetime: '',
        channel_type: ' '
      };
    },
    setScrollTop() {
      let scrollTop = this.$refs.detailScrollTop ? this.$refs.detailScrollTop.scrollTop : 0;
      this.setScrollScreen('h01S05TimeLine', scrollTop);
    }
  }
  /** Filter Mode end */
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.dailyList_li {
  position: relative;
}
.w-60 {
  width: 60px !important;
}
.show {
  display: block !important;
  top: 0px;
}
.no-data {
  padding-top: 60px;
  overflow-y: auto;
  height: 100%;
  text-align: center;
  .no-data-content {
    width: 100%;
    .no-data-text {
      font-size: 1rem;
    }
    .no-data-thumb {
      max-width: 400px;
      margin: 46px auto 0;
      img {
        width: 100%;
      }
    }
  }
}
/** start css layout  **/
.groupPersonal {
  height: 100%;
  padding-top: 28px;
  background: #f2f2f2;
  @media (max-width: $viewport_tablet) {
    top: 16px;
  }
  .personalDetails {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .personalHead {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    padding: 12px 24px 0;
    .personalHead__user {
      width: 350px;
      padding-right: 10px;
      display: flex;
      align-items: flex-start;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-right: 0;
      }
      .personalHead-item {
        min-width: 22px;
        padding: 4px 8px 0 0;
        img {
          width: 21px;
        }
      }
      .personalHead-content {
        .personalHead-label {
          .name {
            font-size: 24px;
            font-weight: 700;
            line-height: 140%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 220px;
            display: inline-block;
          }
          .btn {
            &.btn-light {
              border-radius: 2px;
              width: 60px;
              height: 24px;
              padding: 0 6px;
              margin: 6px 0 0 20px;
            }
            &.btn-danger {
              height: 24px;
              border-radius: 2px;
              padding: 0 6px;
              margin: 6px 0 0 20px;
            }
          }
        }
        .personalHead-text {
          font-size: 16px;
          line-height: 120%;
        }
      }
    }
    .personalHead__info {
      width: calc(100% - 350px);
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 10px 0 0;
      }
      ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        @media (max-width: $viewport_tablet) {
          justify-content: flex-start;
        }
        li {
          margin-right: 36px;
          color: #99a5ae;
          @media (max-width: $viewport_desktop) {
            margin-right: 20px;
          }
          &:last-child {
            margin-right: 0;
          }
        }
      }
    }
  }
  .personalTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-top: 12px;
    .navTabs--personal {
      li {
        min-width: 263px;
        @media (max-width: $viewport_desktop) {
          min-width: 200px;
        }
        @media (max-width: $viewport_tablet) {
          min-width: inherit;
          width: 25%;
        }
        .navItem {
          @media (max-width: $viewport_tablet) {
            min-width: 15px;
            margin-right: 6px;
          }
        }
      }
    }
    .tab-content {
      background: #fff;
      height: 100%;
      box-shadow: 0px 0px 5px #b7c3cb;
      position: relative;
      z-index: 1;
      overflow: hidden;
      .tab-pane {
        height: 100%;
      }
    }
  }
}
/** end css layout **/
/** H01_S05_FacilityDetailsTimeline **/
.personalInfo {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  padding: 25px 0 0 52px;
  height: 100%;
  @media (max-width: $viewport_desktop) {
    padding-left: 12px;
    margin-top: 12px;
  }
  @media (max-width: $viewport_tablet) {
    padding: 20px 24px 0;
    overflow-y: auto;
  }
  .personalInfo-calendar {
    background: #f7f7f7;
    width: 380px;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    margin-bottom: 20px;
    padding: 20px 32px 32px;
    @media (max-width: $viewport_desktop) {
      padding: 12px 12px 32px;
      width: 320px;
    }
    @media (max-width: $viewport_tablet) {
      width: 100%;
      padding: 20px 32px 32px;
    }
  }
  .personalInfo-box {
    width: calc(100% - 380px);
    padding-left: 90px;
    height: 100%;
    @media (max-width: $viewport_desktop) {
      padding-left: 30px;
      width: calc(100% - 320px);
    }
    @media (max-width: $viewport_tablet) {
      width: 100%;
      padding-left: 0;
      height: auto;
    }
  }
  .daily-colInfo {
    height: 100%;
    background: #f7f7f7;
    box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
    border-radius: 20px 0px 0px 0px;
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    @media (max-width: $viewport_tablet) {
      width: 100%;
      height: 400px;
      border-radius: 20px 20px 0px 0px;
    }
  }
  .daily-filter {
    margin: 0 34px 10px 0;
    position: relative;
    display: flex;
    justify-content: flex-end;
    .btn {
      &.btn-filter {
        padding: 0;
        width: 42px;
        height: 42px;
        border-radius: 0px 0px 8px 8px;
      }
    }
    .dropdown-filter--personal {
      width: 380px;
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
      .groupForm-personal {
        margin-bottom: 10px;
        .groupForm-label {
          color: #5f6b73;
          margin-bottom: 6px;
        }
        .groupForm-controlDate {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          .item {
            width: 32px;
            text-align: center;
            color: #727f88;
            font-size: 1rem;
          }
          .form-icon {
            width: calc(50% - 16px);
          }
        }
        .groupForm-controlChannel {
          ul {
            display: flex;
            flex-wrap: wrap;
            margin-left: -20px;
            li {
              width: 50%;
              padding-left: 20px;
              margin-top: 5px;
              .custom-control {
                width: 100%;
                .custom-control-description {
                  width: 100%;
                }
              }
            }
          }
        }
        .formNotes-checkBox {
          .formNotes-label {
            color: #5f6b73;
            margin-bottom: 6px;
          }
          ul {
            display: flex;
            flex-wrap: wrap;
            margin-left: -20px;
            li {
              width: 50%;
              padding-left: 20px;
              margin-top: 5px;
              .custom-control {
                width: 100%;
                .custom-control-description {
                  width: 100%;
                }
              }
            }
          }
        }
      }
      .btnFilter-personal {
        text-align: center;
        padding-top: 24px;
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
  .dailyList {
    height: calc(100% - 36px);
    padding: 4px 34px 0 44px;
    margin-top: 24px;
    @media (max-width: $viewport_desktop) {
      padding: 0 6px 0 12px;
    }
    @media (max-width: $viewport_tablet) {
      padding: 0 32px;
    }

    .dailyList-sub {
      li {
        padding: 7px 5px 13px 0;
      }
    }
    > ul {
      height: 100%;
      position: relative;

      > li {
        &::before {
          position: absolute;
          left: 204px;
          content: '';
          height: 245%;
          @media only screen and (max-width: 1180px) {
            height: 198%;
          }
          @media only screen and (max-width: 1024px) {
            height: 180%;
          }
          width: 2px;
          background: #b7c3cb;
          display: block;
          @media (max-width: $viewport_desktop) {
            left: 131px;
          }
        }
        .dailyList-flex {
          display: flex;
          flex-wrap: wrap;
          align-items: flex-start;
        }
        .dailyList-calendar {
          width: 100%;
          .label {
            background: #d9e7fd;
            color: #285888;
            text-align: center;
            border-radius: 20px 0px 0px 20px;
            box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
            padding: 9px 10px;
            width: 204px;
            display: block;
            font-weight: 700;
            @media (max-width: $viewport_desktop) {
              width: 133px;
              padding: 8px 0;
            }
          }
        }
        .dailyList-dateTime {
          z-index: 1;
          width: 260px;
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          padding: 20px 0 0 83px;
          @media (max-width: $viewport_desktop) {
            width: 170px;
            padding: 20px 0 0 12px;
          }
          .time {
            width: 100px;
            color: #99a5ae;
          }
          .iconItem {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            &::after {
              position: absolute;
              top: 0;
              left: 0;
              content: '';
              width: 44px;
              height: 44px;
              display: block;
              background: #fff;
              border: 2px solid #727f88;
              border-radius: 5px;
              transform: rotate(45deg);
            }
            &::before {
              position: absolute;
              top: 19px;
              right: -36px;
              content: '';
              height: 2px;
              width: 30px;
              background: #b7c3cb;
              display: block;
            }
            @media (max-width: $viewport_desktop) {
              width: 40px;
              height: 40px;
              &::after {
                width: 40px;
                height: 40px;
              }
            }
            .item {
              position: relative;
              z-index: 1;
            }
          }
        }
        .dailyList-content {
          z-index: 1;
          width: calc(100% - 260px);
          position: relative;
          @media (max-width: $viewport_desktop) {
            width: calc(100% - 170px);
          }
          .dailyList-box {
            background: #fff;
            box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
            border-radius: 8px;
            padding: 12px 22px 12px 22px;
            @media (max-width: $viewport_desktop) {
              padding: 12px 15px 12px 15px;
            }
            .dailyList-label {
              margin-top: -4px;
              .span-label {
                margin-top: 4px;
                margin-right: 12px;
                &:last-child {
                  margin-right: 0;
                }
                &.span-label--off {
                  background: #ffeab6;
                  color: #e2633b;
                }
                &.span-label--knowledge {
                  background: #daf8dc;
                  color: #28a470;
                }
                .span-label-item {
                  display: inline-flex;
                  margin: 3px 3px 0 0;
                }
              }
            }
          }
          .dailyList-group {
            padding-top: 4px;
            .dailyList-info {
              display: flex;
              flex-wrap: wrap;
              margin-top: 4px;
              .dailyList-info-tlt {
                width: 80px;
                padding-right: 5px;
                .dailyList-gray {
                  color: #727f88;
                }

                &.interview {
                  width: 55px;
                }
                &.briefing,
                &.convention,
                &.external {
                  width: 80px;
                }
              }
              .dailyList-info-text {
                width: calc(100% - 80px);
                .dailyList-spanBlue {
                  color: #448add;
                }
                .dailyList-spanGray {
                  color: #2d3033;
                }
                .dailyList-bold {
                  font-weight: 700;
                  cursor: pointer;
                }
                .dailyList-add {
                  display: flex;
                  margin-top: 3px;
                  .dailyList-item {
                    min-width: 13px;
                    margin: -2px 5px 0 0;
                  }
                }
              }
            }
            .daily-accordion {
              padding-top: 8px;
              .article-head {
                display: inline-flex;
                color: #448add;
                cursor: pointer;
                position: relative;
                padding-right: 20px;
                &.detail {
                  margin-left: 14px;
                }
                &::after {
                  position: absolute;
                  top: 5px;
                  right: 0px;
                  content: '';
                  border-left: 6px solid transparent;
                  border-right: 6px solid transparent;
                  border-top: 6px solid #448add;
                  transition: 400ms all;
                }
                &[aria-expanded='true'] {
                  &::after {
                    transform: rotate(180deg);
                  }
                }
              }
              .article-body {
                background: #f7f7f7;
                box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
                border-radius: 4px;
                padding: 8px 14px 12px;
                width: calc(100% + 47px);
              }
              .daily-article {
                ul {
                  li {
                    display: flex;
                    flex-wrap: wrap;
                  }
                }
                .daily-article-tlt {
                  width: 78px;
                  padding-right: 10px;
                  color: #727f88;
                }
                .daily-article-label {
                  width: calc(100% - 78px);
                }
                .daily-article-label,
                .daily-article-text {
                  color: #2d3033;
                }
              }
            }
          }
          .viewMore {
            text-align: right;
            width: 100%;
            padding-top: 18px;
            @media (max-width: $viewport_desktop) {
              width: 100%;
            }
            a {
              padding-right: 20px;
              background: url(~@/assets/template/images/icon_arrow-right-blue.svg) no-repeat right;
            }

            .dailyList-spanBlue {
              color: #448add;
            }
            .dailyList-spanGray {
              color: #2d3033;
            }
            .dailyList-bold {
              cursor: pointer;
            }
          }
          .dailyList-btnCmt {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 42px;
            .btn {
              position: relative;
              &.btn--active {
                background: #448add;
              }
              &.btn--hasNoti {
                &::after {
                  position: absolute;
                  top: 9px;
                  right: 9px;
                  content: '';
                  display: block;
                  width: 9px;
                  height: 9px;
                  background: #ea5d54;
                  border-radius: 50%;
                  border: 2px solid #fff;
                }
              }
            }
          }
        }
        .dailyNote {
          background: #b7d4ff;
          border-radius: 4px;
          padding: 7px 12px;
          margin-top: 23px;
          color: #225999;
          position: relative;
          &::after {
            position: absolute;
            top: -12px;
            left: 60px;
            content: '';
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 12px solid #b7d4ff;
          }
        }
      }
    }
  }
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

.filter-wrapper {
  position: absolute !important;
  right: 0;
  z-index: 5;
}
.dailyList_ul.scrollbar {
  position: static;
}
</style>
