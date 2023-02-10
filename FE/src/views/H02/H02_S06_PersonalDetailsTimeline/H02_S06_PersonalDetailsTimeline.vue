<template>
  <div v-load-f5="true" class="personalInfo scrollbar">
    <div class="personalInfo-calendar">
      <VCalendaRange
        :mode="datePicker.mode"
        :selected-start="datePicker.start"
        :selected-end="datePicker.end"
        :year="datePicker.year"
        :range-date="rangeDate"
        @click="onBind"
        @changeMode="changeMode"
      />
    </div>

    <div class="personalInfo-box">
      <div class="daily-colInfo">
        <div class="daily-filter filter-wrapper">
          <button type="button" class="btn btn-link btn-filter" @click="showPopup()">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
          </button>
          <div :class="[dropdownFilter + ' dropdown-filter--personal', isShowPopupFilter ? 'show' : '']">
            <div class="item-filter btn-close-filter" @click="closePopup()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
            </div>
            <h2 class="title-filter">検索条件</h2>
            <div class="groupForm">
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
                        <span class="custom-control-description">講演会</span>
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
            <li v-for="(item, index) in timelines" :id="index" :key="index" class="dailyList_li">
              <div class="dailyList-calendar">
                <span class="label">{{ item.start_datetime }}({{ getWeek(item.start_datetime) }})</span>
              </div>
              <ul class="dailyList-sub">
                <template v-for="(itemChild, i) of item.detail" :key="itemChild">
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
                          <!-- <div class="dailyList-btnCmt">
                            <button class="btn btn--icon btn--active">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_comment.svg')" alt="" />
                            </button>
                          </div> -->
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
                                <p class="dailyList-bold">{{ itemChild.department_name }} {{ itemChild.person_name }} {{ itemChild.display_position_name }}</p>
                              </div>
                            </div>
                            <div v-for="(dataProtuct, y) of itemChild.detail" :id="'accordion' + y" :key="dataProtuct" class="daily-accordion">
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
                                <p class="dailyList-spanBlue dailyList-bold point_mouse" @click="goAccountSetting(itemChild)">
                                  {{ itemChild.user_name }} ({{ itemChild.user_post_type }})
                                </p>
                                <p class="dailyList-add">
                                  <span class="dailyList-item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                                  {{ itemChild.org_label }}
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="viewMore">
                            <a class="dailyList-spanBlue" @click="callScreenA01_S04(itemChild)">詳細を見る</a>
                          </div>
                        </div>
                        <!-- <div v-if="view" class="dailyNote">
                          <p class="dailyNote-text">
                            資料の洗い出しに先駆けて、前回講師をして頂いた鈴木先生にメールで前回資料に関するアドバイスを頂き、それを踏まえて洗い出しに臨みました。
                          </p>
                          <p class="dailyNote-text">その結果、洗い出しがスムーズに進み、田中先生にも安心して頂けました。</p>
                        </div> -->
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
                                <p class="dailyList-spanBlue dailyList-bold point_mouse" @click="goBriefingSessionInput(item)">
                                  {{ itemChild?.title }}
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
                                <p class="dailyList-spanGray">{{ itemChild.product_name }}BB</p>
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
                                <p class="dailyList-spanBlue dailyList-bold point_mouse" @click="goAccountSetting(itemChild)">
                                  {{ itemChild.user_name }} <span v-show="itemChild.user_post_type?.length"> ({{ itemChild.user_post_type }}) </span>
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
                          <div class="dailyList-group">
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt convention">
                                <p class="dailyList-info-label dailyList-gray">講演会名</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanBlue dailyList-bold point_mouse" @click="goLectureInput(itemChild)">
                                  {{ itemChild?.title }}
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
                          <!-- <div class="dailyList-btnCmt">
                            <button class="btn btn--icon">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_comment.svg')" alt="" />
                            </button>
                          </div> -->
                          <div class="dailyList-group">
                            <div class="dailyList-info">
                              <div class="dailyList-info-tlt external">
                                <p class="dailyList-info-label dailyList-gray">視聴者</p>
                              </div>
                              <div class="dailyList-info-text">
                                <p class="dailyList-spanBlue dailyList-bold point_mouse" @click="goPersonDetail(itemChild)">
                                  {{ itemChild.department_name }} {{ itemChild.person_name }} {{ itemChild.display_position_name }}
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
                                  <!-- <ul>
                                    <li>
                                      <span class="daily-article-tlt">フェーズ</span>
                                      <span class="daily-article-label">{{ itemChild.phase_name }}</span>
                                    </li>
                                    <li>
                                      <span class="daily-article-tlt">反応</span>
                                      <span class="daily-article-label">{{ itemChild.reaction_name }}</span>
                                    </li>
                                  </ul> -->
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
            <EmptyData v-if="timelines.length === 0 && !isLoadDefault" title_log="該当するデータがありません。" icon="amico" custom-class="customClassEmpty" />
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!--Modal-->
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog modal-fixed modal-fixed-min"
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
import VCalendaRange from '../../../components/VCalendaRange.vue';
import H02S06PersonalDetailsTimelineServices from '@/api/H02/H02_S06_PersonalDetailsTimeline';
import Z05S06MaterialSelection from '../../Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection.vue';
import { markRaw } from 'vue';
import moment from 'moment';
import filter_popup from '@/utils/mixin_filter_popup';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';

export default {
  name: 'H02_S06_PersonalDetailsTimeline',
  components: {
    VCalendaRange,
    Z05S06MaterialSelection //popup
  },
  mixins: [filter_popup],
  props: {
    changetab: {
      type: Number,
      default: 0
    },
    dropdownFilter: {
      type: String,
      default: ''
    },
    checkLoading: [Boolean]
  },
  data() {
    return {
      loading: false,
      isLoadDefault: true,
      showModalZ05S04: false,
      keycomponent: Math.random(),
      // isShowPopupFilter: false,

      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      },
      personList: [],
      channelTypeList: ['10', '20', '30', '40'],
      productList: [],
      timelines: [],
      screenData: null,
      datePicker: {
        end: '',
        start: '',
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
      person_cd: '',
      filter: {
        person_cd: '',
        end_datetime: '',
        start_datetime: '',
        channel_type: '',
        product_name: ''
      },
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      rangeDate: { start: new Date(new Date().setMonth(new Date().getUTCMonth() - 1)), end: new Date() },
      user_login_cd: '',
      onTop: 0,
      paramsZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      }
    };
  },
  watch: {
    changetab: function (value) {
      // eslint-disable-next-line eqeqeq
      if (value == 5) {
        this.filter.person_cd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd : '';
        this.getTimeLine(this.filter, true);
      }
    }
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: '個人詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.onTop = JSON.parse(localStorage.getItem('ScrollTopScreen'));
    this.filter.person_cd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd : '';
    this.user_login_cd = this.getCurrentUser().user_cd;
    this.datePicker.start_date_month = this.datePicker.start < 10 ? `0${this.datePicker.start}` : `${this.datePicker.start}`;
    this.datePicker.end_date_month = this.datePicker.end < 10 ? `0${this.datePicker.end}` : `${this.datePicker.end}`;
    this.datePicker.fullYearStartMonth = `${this.datePicker.year}/${this.datePicker.start_date_month}`;
    this.datePicker.fullYearEndMonth = `${this.datePicker.year}/${this.datePicker.end_date_month}`;
    // eslint-disable-next-line eqeqeq
    if (this.changetab == 5) {
      const data = this.getScreen('H02_S06_PersonalDetailsTimeline');
      const checkValid = (date) => (date ? moment(date).format('YYYY/M/D') : moment().format('YYYY/M/D'));
      if (data) {
        // eslint-disable-next-line no-undef
        const checkNull = (name) => data['filter'] && data['filter'][name] && data['filter'][name].length > 0;
        this.filter.start_datetime = checkNull('start_datetime') ? data['filter']['start_datetime'] : checkValid(this.rangeDate.start);
        this.filter.end_datetime = checkNull('end_datetime') ? data['filter']['end_datetime'] : checkValid(this.rangeDate.end);
        // this.getTimeLine(this.filter, true);
      }
    }
    this.setHeighTimeline();
    this.onSearch(true);
    // Load scrollbar
    this.js_pscroll(0.5);
  },
  methods: {
    setHeighTimeline() {
      const scroll = document.querySelector('.dailyList');
      scroll.addEventListener('scroll', () => {
        const ul = document.querySelector('.dailyList > ul');
        ul.style.height = 'auto';
      });
    },
    onBind(event) {
      this.filter.person_cd = this._route('H02_PersonalDetails')
        ? this._route('H02_PersonalDetails').params.person_cd || localStorage.getItem('person_cd_prev')
        : '';
      this.filter.channel_type = this.channelTypeList.filter((s) => s !== '').join();
      this.filter.product_name = this.productList.map((s) => s.name).join();
      if (event.mode === 'monthly') {
        this.datePicker.year = event.year;
        this.datePicker.fullYearEndMonth = event.fullYearEndMonth;
        this.datePicker.fullYearStartMonth = event.fullYearStartMonth;
      } else {
        if (event.callApi) {
          this.filter.start_datetime = event.startDate;
          this.filter.end_datetime = event.endDate;
          const checkNull = this.filter.start_datetime === '' || this.filter.end_datetime === '';
          if (!checkNull) {
            this.setDefaultScroll();
            this.search();
          }
        } else {
          if (event.getAll) {
            this.filter.start_datetime = '';
            this.filter.end_datetime = '';

            this.setDefaultScroll();
            this.getTimeLine(this.filter, false);
          }
        }

        this.datePicker.singleDate = event;
      }
    },
    onSearch(isLoadDefault) {
      this.datePicker.start = '';
      this.datePicker.end;
      this.search(isLoadDefault);
    },
    changeMode(event) {
      this.datePicker.mode = event;
    },
    setDefaultScroll() {
      this.setScrollScreen('h02S05TimeLine', 0);
      this.onTop = 0;
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
    /** API */
    search(isLoadDefault) {
      // Check is RedirectH02_S01
      if (JSON.parse(localStorage.getItem('isRedirectH02_S01'))) {
        // Set default calendar
        this.filter.start_datetime = this.formatFullDate(new Date(new Date().setMonth(new Date().getUTCMonth() - 1)));
        this.filter.end_datetime = this.formatFullDate(new Date());
        // Reset redirect
        localStorage.setItem('isRedirectH02_S01', false);
      } else {
        this.filter.start_datetime = this.formatFullDate(this.filter.start_datetime)
          ? this.formatFullDate(this.filter.start_datetime)
          : this.formatFullDate(new Date(new Date().setMonth(new Date().getUTCMonth() - 1)));
        this.filter.end_datetime = this.formatFullDate(this.filter.end_datetime)
          ? this.formatFullDate(this.filter.end_datetime)
          : this.formatFullDate(new Date());
      }

      this.filter.person_cd = this._route('H02_PersonalDetails')
        ? this._route('H02_PersonalDetails').params.person_cd || localStorage.getItem('person_cd_prev')
        : '';
      this.filter.channel_type = this.channelTypeList.filter((s) => s !== '').join();
      this.getTimeLine(this.filter, isLoadDefault);
    },
    getTimeLine(params, isLoadDefault) {
      if (!params.person_cd) {
        params.person_cd = localStorage.getItem('person_cd_prev');
      }

      this.isLoadDefault = isLoadDefault;

      this.$emit('changeLoading', true);
      H02S06PersonalDetailsTimelineServices.fetchTimeLineService(params)
        .then(async (res) => {
          if (res) {
            // let d = [];
            // let arr = [];
            // const data = res.map((s, index) => {
            // console.log(s, index);
            // const keys = Object.keys(s.detail);
            // arr[index] = arr[index] || [];
            // keys.forEach((v) => {
            //   s.detail[v].forEach((d, i, a) => {
            //     arr[index].push(a[i]);
            //   });
            // });
            // const content = keys.map((it) => {
            //   const obj = s.detail[it][0];
            //   const r1 = { channel_detail: [], ...obj };
            //   if (r1.channel_type === '10') {
            //     const detail = s.detail[it].map((s) => ({
            //       channel_detail_id: s.channel_detail_id,
            //       content_name: s.content_name,
            //       detail_order: s.detail_order,
            //       note: s.note,
            //       phase_name: s.phase_name,
            //       product_name: s.product_name,
            //       off_label_flag: s.off_label_flag,
            //       reaction_name: s.reaction_name,
            //       schedule_id: s.schedule_id
            //     }));
            //     r1.channel_detail = detail;
            //   }
            //   return r1;
            // });
            // return { start_datetime: this.formatFullDate(s.start_datetime), content };
            // });
            // console.log(232222, data);

            this.timelines = [];

            this.timelines = res;
            console.log(111111, this.timelines);

            this.cancelPopup();
          }
        })
        .catch((err) => {
          this.$emit('changeLoading', false);
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          if (this.onTop && this.$refs['detailScrollTop']) {
            this.$refs['detailScrollTop'].scrollTop = this.onTop.h02S05TimeLine ? this.onTop.h02S05TimeLine : 0;
          }
          this.$emit('changeLoading', false);
          this.isLoadDefault = false;
        });
    },
    setScrollTop() {
      let scrollTop = this.$refs.detailScrollTop ? this.$refs.detailScrollTop.scrollTop : 0;
      this.setScrollScreen('h02S05TimeLine', scrollTop);
    },
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
      this.$router.push({
        name: 'Z04_S01_AccountSettings',
        params: {
          user_cd: item.user_cd
        }
      });
    },
    goLectureInput(item) {
      this.setScrollTop();
      this.$router.push({
        name: 'C01_S02_LectureInput',
        params: {
          convention_id: item?.channel_id,
          schedule_id: item.schedule_id
        }
      });
    },
    goBriefingSessionInput(item) {
      this.setScrollTop();
      this.$router.push({
        name: 'B01_S02_BriefingSessionInput',
        params: {
          briefing_id: item.content[0].channel_id,
          schedule_id: item.schedule_id
        }
      });
    },

    closeModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        component: markRaw(Z05S06MaterialSelection),
        props: {
          ...this.paramsZ05S06,
          initDataCodes: this.productList.map((x) => x.code)
        },
        width: '33rem',
        destroyOnClose: true
      };
    },
    onResultModal(data) {
      const screen = data?.screen;
      switch (screen) {
        case 'Z05_S06':
          this.paramsZ05S06 = {
            ...this.paramsZ05S06,
            categoryCode: data.category.definition_value,
            classificationCode: data.classifications.product_group_cd
          };

          // eslint-disable-next-line no-case-declarations
          let datas = data?.dataSelected?.length > 0 ? data?.dataSelected : {};
          // this.filter.product_cd = data?.product_cd;
          this.productList = datas.map((s) => ({ code: s.product_cd, name: s.product_name }));
          this.filter.product_name = datas.map((s) => s.product_name).join();
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
      this.filter.product_name = this.productList.map((s) => s.name).join();
    },
    handleRemoveFacility(item) {
      this.personList = this.personList.filter((s) => s.code !== item.code);
      this.filter.person_cd = this.personList.length > 0 ? this.personList.map((s) => s.code).join() : '';
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
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
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
        width: 263px !important;

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
      height: 450px;
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
    height: calc(100% - 32px);
    padding: 0 34px 0 44px;
    margin-top: 24px;
    @media (max-width: $viewport_desktop) {
      padding: 0 12px 0 25px !important;
    }
    position: relative;

    .dailyList-sub {
      li {
        padding: 7px 5px 13px 0;
      }
      &::before {
        position: absolute;
        top: 0px;
        left: 248px;
        content: '';
        height: calc(100% + 8px);
        width: 2px;
        background: #b7c3cb;
        display: block;
        @media (max-width: $viewport_desktop) {
          left: 165px;
        }
      }
    }

    > ul {
      overflow: auto;
      height: 100%;
      > li {
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
              width: 140px;
              padding: 8px 0;
            }
          }
        }
        .dailyList-dateTime {
          width: 260px;
          z-index: 1;

          display: flex;
          flex-wrap: wrap;
          align-items: center;
          padding: 20px 0 0 83px;
          @media (max-width: $viewport_desktop) {
            width: 187px;
            padding: 20px 0 0 19px;
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
              top: 21px;
              right: -36px;
              content: '';
              height: 2px;
              width: 30px;
              background: #b7c3cb;
              display: block;
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
            width: calc(100% - 187px);
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
                width: 96px;
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
                width: calc(100% - 96px);
                .dailyList-spanBlue {
                  color: #448add;
                }
                .point_mouse {
                  cursor: pointer;
                }
                .dailyList-spanGray {
                  color: #2d3033;
                }
                .dailyList-bold {
                  font-weight: 700;
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
                width: 100%;
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
              cursor: pointer;
            }
            .dailyList-spanGray {
              color: #2d3033;
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
.custom-date-picker {
  display: block;
  .el-date-picker__header {
    // custom header style here
    background-color: blue;
    display: block;
  }
}
.vc-container {
  width: 100% !important;
  border: none !important;
}
.filter-wrapper {
  position: absolute !important;
  right: 0;
  z-index: 5;
}
.noData-thumb {
  max-height: 350px;
}
.dailyList_ul.scrollbar {
  position: static;
}
</style>
