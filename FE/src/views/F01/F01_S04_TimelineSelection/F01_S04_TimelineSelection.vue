<template>
  <!-- modal -->
  <!-- title: タイムライン選択 -->
  <!-- width: 75rem -->
  <!-- props:  -->
  <div v-load-f5="true" class="modal-body modal-timeline cssF01S04 modal-body-F01S04">
    <div id="msfa-notify-F01S04"></div>
    <div class="timeline-row">
      <div class="timeline-col7">
        <div class="wrapBtn">
          <div class="btnInfo btnInfo--change">
            <div class="btnInfo-btn">
              <button :class="`btn btn-filter ${flg_filter ? 'active' : ''}`" @click="openFilter(false)">
                <ImageSVG :src-image="'icon_filter.svg'" :alt-image="'icon_filter'" />
              </button>
              <div v-click-outside-event="clickOutside" :hidden="!flg_filter" class="dropdown-menu dropdown-filter dropdown-Timeline">
                <h2 class="title-filter">検索条件</h2>
                <div class="form-InFacility">
                  <ul>
                    <li>
                      <label class="form-InFacility-label">実施者</label>
                      <div class="form-InFacility-control">
                        <div class="form-icon icon-right">
                          <span
                            class="icon log-icon"
                            title_log="実施者"
                            @click="openModal({ screenID: 'Z05_S01', title: 'ユーザ選択', width: currDevice() !== 'Desktop' ? '95%' : '65rem' })"
                          >
                            <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                          </span>
                          <div v-if="lstUser.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag v-for="(item, index) in lstUser" :key="index" class="m-0 el-tag-custom" closable @close="handleRemoveTag(item, 'user')">
                                {{ item.user_name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="form-InFacility-label">チャネル</label>
                      <div class="form-InFacility-check">
                        <ul>
                          <li v-for="(item, index) in channels" :key="index">
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input
                                class="custom-control-input"
                                type="checkbox"
                                :checked="initParams.channel_type.some((el) => el === item.channel_value)"
                                @change="checkedBox(item.channel_value)"
                              />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ item.channel_label }}</span>
                            </label>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li>
                      <label class="form-InFacility-label">開催日</label>
                      <div class="form-InFacility-date">
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon"> <ImageSVG :src-image="'icon-calender-control.svg'" :alt-image="'icon-calender-control'" /> </span>
                          <el-date-picker
                            v-model="initParams.start_datetime"
                            :disabled-date="checkDateStart"
                            format="YYYY/M/D"
                            value-format="YYYY/M/D"
                            type="date"
                            :editable="false"
                            placeholder="開始日"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                        <div class="form-InFacility-item">～</div>
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <ImageSVG :src-image="'icon-calender-control.svg'" :alt-image="'icon-calender-control'" />
                          </span>
                          <el-date-picker
                            v-model="initParams.end_datetime"
                            :disabled-date="checkDateEnd"
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
                    <li>
                      <label class="form-InFacility-label">品目</label>
                      <div class="form-InFacility-control">
                        <div class="form-icon icon-right">
                          <span
                            class="icon log-icon"
                            title_log="品目"
                            @click="
                              openModal({
                                screenID: 'Z05_S06',
                                title: '品目選択',
                                width: '42rem',
                                customClass: 'custom-dialog modal-fixed',
                                props: { mode: 'multiple', initDataCodes: initParams.product_name.map((el) => el.product_cd) }
                              })
                            "
                            @touchstart="
                              openModal({
                                screenID: 'Z05_S06',
                                title: '品目選択',
                                width: '42rem',
                                customClass: 'custom-dialog modal-fixed',
                                props: { mode: 'multiple', initDataCodes: initParams.product_name.map((el) => el.product_cd) }
                              })
                            "
                          >
                            <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                          </span>
                          <div v-if="initParams.product_name.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="(item, index) in initParams.product_name"
                                :key="index"
                                class="m-0 el-tag-custom"
                                closable
                                @close="handleRemoveTag(item, 'product')"
                              >
                                {{ item.product_name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                  </ul>
                  <div class="btn-InFacility">
                    <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="openFilter(false)">キャンセル</button>
                    <button type="button" class="btn btn-primary" @click="search">検索</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- time line -->
        <div :class="`boxTimeline scrollbar ${isLoading ? 'pre-loader' : ''}`">
          <Preloader v-if="isLoading" />
          <div v-else>
            <div v-if="isData" class="dailyList">
              <ul>
                <li v-for="(el, idx) in timeLineList" :key="idx">
                  <div v-if="el.data.length > 0" class="dailyList-calendar">
                    <span class="label">{{ formatFullDate(el.date) + '（' + getDay(el.date) + '）' }}</span>
                  </div>
                  <ul class="dailyList-sub">
                    <li v-for="(item, index) in el.data" :key="index">
                      <div class="dailyList-flex">
                        <div class="dailyList-dateTime">
                          <span class="time">{{
                            formatTimeHourMinute(item.start_datetime) === '0:00' && formatTimeHourMinute(item.end_datetime) === '23:59'
                              ? '終日'
                              : formatTimeHourMinute(item.start_datetime) + '～' + formatTimeHourMinute(item.end_datetime)
                          }}</span>
                          <div class="iconItem">
                            <span class="item"><ImageSVG :src-image="returnIcon(item.channel_type)" :alt-image="'icon'" /></span>
                          </div>
                        </div>
                        <!-- channel_type === '10' (面談) -->
                        <div v-if="item.channel_type === '10'" class="dailyList-content">
                          <div class="dailyList-box">
                            <div class="dailyList-tools">
                              <button
                                type="button"
                                :class="`btn btn--add btn-outline-${!checkExits(item) ? 'primary' : 'secondary btn--disabled '} btn-radius`"
                                @click="addList(el.date, item)"
                              >
                                <span class="btn-iconLeft">
                                  <ImageSVG :src-image="'icon_plus03.svg'" :alt-image="'icon_plus03'" />
                                </span>
                                {{ !checkExits(item) ? '追加' : '追加済' }}
                              </button>
                            </div>
                            <div v-if="item.off_label_flag" class="dailyList-label">
                              <span class="span-label span-label--off">
                                <span class="span-label-item">
                                  <ImageSVG :src-image="'icon_exclamation-circle.svg'" :alt-image="'icon_exclamation-circle'" />
                                </span>
                                オフラベルあり
                              </span>
                            </div>
                            <div class="dailyList-group">
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt interview">
                                  <p class="dailyList-info-label">面談先</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-bold">
                                    <span>{{ item.facility_short_name }} 　</span>
                                    <span v-if="item.department_name"> {{ item.department_name }} 　 </span>
                                    <span v-if="item.person_name"> {{ item.person_name }} </span>
                                  </p>
                                </div>
                              </div>
                              <div class="daily-accordion">
                                <el-collapse accordion>
                                  <el-collapse-item v-for="(itemDetail, idxx) in item.detail" :key="idxx" :name="idxx">
                                    <template #title>
                                      <h2 class="article-head detail">
                                        <span>{{ itemDetail.detail_order + '. ' + itemDetail.content_name + '　' + itemDetail.product_name }}</span>
                                      </h2>
                                    </template>
                                    <div class="article-body">
                                      <div class="daily-article">
                                        <ul>
                                          <li>
                                            <span class="daily-article-tlt">フェーズ</span>
                                            <span class="daily-article-label">{{ itemDetail.phase_name }}</span>
                                          </li>
                                          <li>
                                            <span class="daily-article-tlt">反応</span>
                                            <span class="daily-article-label">{{ itemDetail.reaction_name }}</span>
                                          </li>
                                        </ul>
                                        <p class="daily-article-text">
                                          {{ itemDetail.note }}
                                        </p>
                                      </div>
                                    </div>
                                  </el-collapse-item>
                                </el-collapse>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt interview">
                                  <p class="dailyList-info-label">面談者</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-bold">
                                    {{ item.user_name }}
                                  </p>
                                  <p class="dailyList-add">
                                    <span class="dailyList-item"><ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" /></span>
                                    {{ item.org_label || '(所属なし)' }}
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- channel_type === '30' (説明会) -->
                        <div v-if="item.channel_type === '30'" class="dailyList-content">
                          <div class="dailyList-box">
                            <div class="dailyList-tools">
                              <button
                                type="button"
                                :class="`btn btn--add btn-outline-${!checkExits(item) ? 'primary' : 'secondary btn--disabled '} btn-radius`"
                                @click="addList(el.date, item)"
                              >
                                <span class="btn-iconLeft">
                                  <ImageSVG :src-image="'icon_plus03.svg'" :alt-image="'icon_plus03'" />
                                </span>
                                {{ !checkExits(item) ? '追加' : '追加済' }}
                              </button>
                            </div>
                            <div class="dailyList-group">
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt convention">
                                  <p class="dailyList-info-label dailyList-gray">講演会名</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-bold">
                                    {{ item.title }}
                                  </p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt convention">
                                  <p class="dailyList-info-label dailyList-gray">講演会区分</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.convention_type_name }}</p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt convention">
                                  <p class="dailyList-info-label dailyList-gray">品目</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.product_name }}</p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt convention">
                                  <p class="dailyList-info-label dailyList-gray">対象組織</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.org_label || '(所属なし)' }}</p>
                                </div>
                              </div>
                              <div class="daily-accordion">
                                <el-collapse accordion>
                                  <el-collapse-item :name="index + 30">
                                    <template #title>
                                      <h2 class="article-head">
                                        <span>特記事項</span>
                                      </h2>
                                    </template>
                                    <div :class="`article-body ${item?.note?.length > 0 ? '' : 'article-body-nodata'}`">
                                      <div class="daily-article">
                                        <p class="daily-article-text">
                                          {{ item.note }}
                                        </p>
                                      </div>
                                    </div>
                                  </el-collapse-item>
                                </el-collapse>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- channel_type === '20' (講演会) -->
                        <div v-if="item.channel_type === '20'" class="dailyList-content">
                          <div class="dailyList-box">
                            <div class="dailyList-tools">
                              <button
                                type="button"
                                :class="`btn btn--add btn-outline-${!checkExits(item) ? 'primary' : 'secondary btn--disabled'} btn-radius`"
                                @click="addList(el.date, item)"
                              >
                                <span class="btn-iconLeft">
                                  <ImageSVG :src-image="'icon_plus03.svg'" :alt-image="'icon_plus03'" />
                                </span>
                                {{ !checkExits(item) ? '追加' : '追加済' }}
                              </button>
                            </div>
                            <div class="dailyList-group">
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt briefing">
                                  <p class="dailyList-info-label dailyList-gray">説明会名</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-bold">
                                    {{ item.title }}
                                  </p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt briefing">
                                  <p class="dailyList-info-label dailyList-gray">説明会区分</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.briefing_type_name }}</p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt briefing">
                                  <p class="dailyList-info-label dailyList-gray">品目</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.product_name }}</p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt briefing">
                                  <p class="dailyList-info-label dailyList-gray">対象施設</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.briefing_facility_short_name }}</p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt briefing">
                                  <p class="dailyList-info-label dailyList-gray">実施者</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-bold">
                                    {{ item.user_name }}
                                  </p>
                                  <p class="dailyList-add">
                                    <span class="dailyList-item"><ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" /></span>
                                    {{ item.org_label || '(所属なし)' }}
                                  </p>
                                </div>
                              </div>
                              <div class="daily-accordion">
                                <el-collapse accordion>
                                  <el-collapse-item :name="index + 20">
                                    <template #title>
                                      <h2 class="article-head">
                                        <span>特記事項</span>
                                      </h2>
                                    </template>
                                    <div :class="`article-body ${item?.note?.length > 0 ? '' : 'article-body-nodata'}`">
                                      <div class="daily-article">
                                        <p class="daily-article-text">
                                          {{ item.note }}
                                        </p>
                                      </div>
                                    </div>
                                  </el-collapse-item>
                                </el-collapse>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- channel_type === '40' (外部コンテンツ) -->
                        <div v-if="item.channel_type === '40'" class="dailyList-content">
                          <div class="dailyList-box">
                            <div class="dailyList-tools">
                              <button
                                type="button"
                                :class="`btn btn--add btn-outline-${!checkExits(item) ? 'primary' : 'secondary btn--disabled'} btn-radius`"
                                @click="addList(el.date, item)"
                              >
                                <span class="btn-iconLeft">
                                  <ImageSVG :src-image="'icon_plus03.svg'" :alt-image="'icon_plus03'" />
                                </span>
                                {{ !checkExits(item) ? '追加' : '追加済' }}
                              </button>
                            </div>
                            <div class="dailyList-group">
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt external">
                                  <p class="dailyList-info-label dailyList-gray">視聴者</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p :class="`dailyList-bold `">
                                    <span>{{ item.facility_short_name }} 　</span>
                                    <span v-if="item.department_name"> {{ item.department_name }} 　 </span>
                                    <span v-if="item.person_name"> {{ item.person_name }} </span>
                                  </p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt external">
                                  <p class="dailyList-info-label dailyList-gray">品目</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.product_name }}</p>
                                </div>
                              </div>
                              <div class="dailyList-info">
                                <div class="dailyList-info-tlt external">
                                  <p class="dailyList-info-label dailyList-gray">コンテンツ</p>
                                </div>
                                <div class="dailyList-info-text">
                                  <p class="dailyList-spanGray">{{ item.content }}</p>
                                </div>
                              </div>
                              <div class="daily-accordion">
                                <el-collapse accordion>
                                  <el-collapse-item :name="index + 40">
                                    <template #title>
                                      <h2 class="article-head">
                                        <span>特記事項</span>
                                      </h2>
                                    </template>
                                    <div :class="`article-body ${item?.note?.length > 0 ? '' : 'article-body-nodata'}`">
                                      <div class="daily-article">
                                        <p class="daily-article-text">
                                          {{ item.note }}
                                        </p>
                                      </div>
                                    </div>
                                  </el-collapse-item>
                                </el-collapse>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <div v-else-if="!isLoading" class="nodata">
              <span>該当するデータがありません。</span>
              <ImageSVG :src-image="'amico.svg'" :alt-image="'amico'" />
            </div>
          </div>
        </div>
      </div>
      <!-- timeline selected -->
      <div class="timeline-col3">
        <div class="selectionGroup">
          <h2 class="selectionTitle">選択リスト</h2>
          <div class="selectionList scrollbar">
            <ul v-if="timeLineListSelected.length > 0">
              <li v-for="(item, index) in timeLineListSelected" :key="index">
                <div class="selectionList-info">
                  <span class="selectionList-item">
                    <img v-show="item.channel_type == 10" class="svg" src="@/assets/template/images/icon_interview-daily.svg" alt="" />
                    <img v-show="item.channel_type == 20" class="svg" src="@/assets/template/images/icon_interview-daily02.svg" alt="" />
                    <img v-show="item.channel_type == 30" class="svg" src="@/assets/template/images/icon_interview-daily01.svg" alt="" />
                    <img v-show="item.channel_type == 40" class="svg" src="@/assets/template/images/icon_interview-daily03.svg" alt="" />
                  </span>
                  <div class="selectionList-content">
                    <p class="tlt">{{ getName(item) }}</p>
                    <p class="dateTime">
                      <span>{{ formatFullDate(item.date) + '（' + getDay(item.date) + '）' }}</span>
                      <span class="minute">{{
                        formatTimeHourMinute(item.start_datetime) === '0:00' && formatTimeHourMinute(item.end_datetime) === '23:59'
                          ? '終日'
                          : formatTimeHourMinute(item.start_datetime) + '～' + formatTimeHourMinute(item.end_datetime)
                      }}</span>
                    </p>
                  </div>
                </div>
                <button type="button" class="btn btn--icon" @click="removeList(index)">
                  <ImageSVG :src-image="'icon_close.svg'" :alt-image="'icon_close'" />
                </button>
              </li>
            </ul>

            <div v-if="timeLineListSelected.length === 0" class="noData">
              <div class="noData-content">
                <p class="noData-text">ユーザを選択して下さい。</p>
                <div class="noData-thumb">
                  <img src="@/assets/template/images/data/amico.svg" alt="" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="timeline-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelBtn">キャンセル</button>
      <button type="button" class="btn btn-primary" :disabled="!timeLineListSelected.length" @click="returnData">選択</button>
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="modalConfig.isShowClose"
    :style="`overflow:hidden`"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closeModal" />
    </template>
  </el-dialog>
</template>

<script>
import F01_S04_Service from '@/api/F01/F01_S04_TimelineSelectionServices';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05_S01_Organization from '../../Z05/Z05_S01_Organization/Z05_S01_Organization.vue';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';
import Z05_S04_facilityCustomerService from '@/api/Z05/Z05_S04_facilityCustomerService';
export default {
  name: 'F01_S04_TimelineSelection',
  props: {
    productName: {
      type: Array,
      default: () => [], // [{product_cd: '', product_name: ''}]
      require: false
    },
    startTime: {
      type: String,
      default: '',
      require: true
    },
    endTime: {
      type: String,
      default: '',
      require: true
    },
    facilityCd: {
      type: String,
      default: '',
      require: false
    },
    channelType: {
      type: Array,
      default: () => [], // array string
      require: false
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      isLoading: true,

      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog custom-overflow',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      paramsZ05S01: {
        userFlag: 1,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
      },
      flg_filter: false,
      timeLineList: [],
      timeLineListSelected: [],
      initParams: {
        start_datetime: this.startTime,
        end_datetime: this.endTime,
        org_cd: [],
        user_cd: [],
        product_name: [],
        channel_type: [],
        facility_cd: ''
      },
      channels: [],
      lstUser: [],
      isData: false,
      currentUser: {},
      lstMedicalStaff: []
    };
  },
  mounted() {
    this.currentUser = this.getCurrentUser();

    this.lstUser.push({
      org_cd: this.currentUser.org_cd,
      user_cd: this.currentUser.user_cd,
      user_name: this.currentUser.user_name
    });

    this.paramsZ05S01 = {
      ...this.paramsZ05S01,
      orgCdList: this.lstUser.map((x) => x.org_cd),
      userCdList: this.lstUser.map((x) => ({ org_cd: x.org_cd, user_cd: x.user_cd }))
    };

    let startDate = new Date().setMonth(new Date().getMonth() - 1);
    let endDate = new Date();

    this.initParams = {
      ...this.initParams,
      org_cd: this.currentUser.org_cd,
      user_cd: this.currentUser.user_cd,
      product_name: this.productName,
      channel_type: this.channelType.length ? this.channelType : ['10', '20', '30', '40'],
      start_datetime: this.formatFullDate(startDate),
      end_datetime: this.formatFullDate(endDate),
      facility_cd: this.facilityCd
    };

    this.getMedicalStaff();
  },
  methods: {
    clickOutside() {
      this.openFilter(true);
    },
    // define func
    checkDateStart(date) {
      if (this.initParams.end_datetime) {
        return date.getTime() > new Date(this.initParams.end_datetime).getTime();
      }
    },
    checkDateEnd(date) {
      if (this.initParams.start_datetime) {
        return date.getTime() < new Date(this.initParams.start_datetime).getTime();
      }
    },
    getName(item) {
      let name = '';
      switch (item.channel_type) {
        case '10':
        case '40':
          name = `${item.facility_short_name ? item.facility_short_name : ''}　${item.department_name ? item.department_name : ''}　${
            item.person_name ? item.person_name : ''
          }`;
          break;
        case '20':
        case '30':
          name = item.title;
          break;
        default:
          break;
      }
      return name;
    },
    cancelBtn() {
      this.$emit('onFinishScreen', { screen: 'F01_S04' });
    },
    getMedicalStaff() {
      let params = {
        facility_cd: ''
      };

      this.lstMedicalStaff = [];
      Z05_S04_facilityCustomerService.getMedicalStaff(params)
        .then((res) => {
          this.lstMedicalStaff = res?.data?.data.map((x) => x.medical_staff_cd);
          this.getIndexTimeline();
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S04', idNodeNotify: 'msfa-notify-Z05S04' });
        })
        .finally(() => {});
    },

    checkPersonMedicalStaff(person) {
      if (!person.person_cd || this.lstMedicalStaff.includes(person.person_cd)) {
        return true;
      }

      return false;
    },

    callScreenA01_S04(item) {
      let params = {
        call_id: item.channel_id,
        schedule_id: item.schedule_id
      };
      A01_S04_Service.checkInterviewDetailInput(params)
        .then(() => {
          this.callScreen('A01S04', params);
        })
        .catch(() => {
          this.notifyModal({
            message: '面談情報が削除されたため、詳細を見ることができません。',
            type: 'error',
            classParent: 'modal-body-F01S04',
            idNodeNotify: 'msfa-notify-F01S04'
          });
        })
        .finally(() => {});
    },

    callScreen(screenID, props) {
      let objScreen = {
        A01S04: 'A01_S04_InterviewDetailedInput',
        Z04S01: 'Z04_S01_AccountSettings',
        H02S02: 'H02_PersonalDetails',
        C01S02: 'C01_S02_LectureInput',
        B01S02: 'B01_S02_BriefingSessionInput'
      };
      if (screenID === 'Z04S01' && !props) return;
      else if (screenID === 'Z04S01') {
        this.$router.push({ name: objScreen[screenID], params: props });
        return;
      }
      if (screenID === 'H02S02') {
        let person_cd = props.person_cd;
        this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
        return;
      }
      this.$router.push({ name: objScreen[screenID], params: props });
    },
    search(isOpenFilter) {
      this.getListDataTimeline({
        ...this.initParams,
        org_cd: this.initParams.org_cd.toString(),
        user_cd: this.initParams.user_cd.toString(),
        product_name: this.initParams.product_name.map((el) => el.product_name).toString(),
        channel_type: this.initParams.channel_type.toString()
      });
      this.openFilter(isOpenFilter);
    },
    openFilter(isOpenFilter) {
      if (isOpenFilter) {
        this.flg_filter = false;
      } else {
        this.flg_filter = !this.flg_filter;
      }

      if (this.flg_filter) {
        document.getElementById('msfa-notify-F01S04')?.closest('.el-dialog.custom-dialog').classList.add('pointer-none');
      } else {
        document.getElementById('msfa-notify-F01S04')?.closest('.el-dialog.custom-dialog').classList.remove('pointer-none');
      }
    },
    checkedBox(value) {
      if (this.initParams.channel_type.some((el) => el === value)) {
        this.initParams.channel_type = this.initParams.channel_type.filter((el) => el !== value);
      } else {
        this.initParams.channel_type.push(value);
      }
    },
    handleRemoveTag(item, type) {
      if (type === 'user') {
        this.lstUser = this.lstUser.filter((el) => el.user_cd !== item.user_cd);
        this.initParams = {
          ...this.initParams,
          org_cd: this.lstUser.map((x) => x.org_cd),
          user_cd: this.lstUser.map((x) => x.user_cd)
        };

        this.paramsZ05S01.orgCdList = this.lstUser.map((x) => x.org_cd);
        this.paramsZ05S01.userCdList = this.lstUser.map((x) => ({ org_cd: x.org_cd, user_cd: x.user_cd }));
      } else {
        this.initParams.product_name = this.initParams.product_name.filter((el) => el.product_cd !== item.product_cd);
      }
    },
    addList(date, obj) {
      if (!this.checkExits(obj))
        this.timeLineListSelected.push({
          date,
          ...obj,
          comment: '',
          delete_flag: 0
        });
    },
    removeList(index) {
      this.timeLineListSelected.splice(index, 1);
    },
    checkExits(item) {
      return this.timeLineListSelected.some((el) => el.timeline_id === item.timeline_id);
    },
    returnData() {
      if (this.timeLineListSelected.length) this.$emit('onFinishScreen', { screen: 'F01_S04', timeLineIDList: this.timeLineListSelected });
    },
    returnIcon(id) {
      const obj = {
        10: 'icon_interview-daily.svg',
        20: 'icon_interview-daily02.svg',
        30: 'icon_interview-daily01.svg',
        40: 'icon_interview-daily03.svg'
      };
      return obj[id];
    },
    // modal
    openModal({ screenID, title = '', width = 0, props = {}, customClass = 'custom-dialog' }) {
      if (!screenID) return;
      if (screenID === 'Z05_S01') {
        props = {
          ...this.paramsZ05S01
        };
      }
      let objScreen = {
        Z05_S01: Z05_S01_Organization,
        Z05_S06: Z05_S06_Material_Selection
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: title,
        isShowModal: true,
        component: this.markRaw(objScreen[screenID]),
        props: props,
        customClass: customClass,
        width: width
      };
    },
    closeModal(data) {
      this.modalConfig.isShowModal = false;
      if (data)
        if (data?.screen === 'Z05_S01') {
          this.paramsZ05S01.orgCdList = [];
          this.paramsZ05S01.userCdList = [];

          data.userSelected?.forEach((x) => {
            this.paramsZ05S01.orgCdList.push(x.org_cd);
          });
          data.userSelected?.forEach((x) => {
            this.paramsZ05S01.userCdList.push({
              org_cd: x.org_cd,
              user_cd: x.user_cd
            });
          });

          this.lstUser = data.userSelected;

          this.initParams = {
            ...this.initParams,
            org_cd: this.lstUser.map((x) => x.org_cd),
            user_cd: this.lstUser.map((x) => x.user_cd)
          };
        } else {
          this.initParams.product_name = data.dataSelected;
        }
    },
    // call api
    getIndexTimeline() {
      F01_S04_Service.getIndexTimelineService()
        .then((res) => {
          const dataRes = res.data.data;
          this.channels = dataRes.channel;
          this.search(true);
        })
        .catch((err) => {
          this.isLoading = false;
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-F01S04',
            idNodeNotify: 'msfa-notify-F01S04'
          });
        })
        .finally();
    },
    getListDataTimeline(params) {
      this.isLoading = true;
      F01_S04_Service.getListDataTimelineService(params)
        .then(async (res) => {
          this.timeLineList = res.data.data;

          this.isData = false;
          for (let i = 0; i < this.timeLineList.length; i++) {
            const item = this.timeLineList[i];
            if (item.date && item.data.length > 0) {
              this.isData = true;
              break;
            }
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-F01S04',
            idNodeNotify: 'msfa-notify-F01S04'
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.nodata {
  display: flex;
  flex-direction: column;
  align-items: center;
  span {
    margin-bottom: 20px;
  }
}
.form-icon .icon {
  cursor: pointer;
}
.modal-timeline {
  margin: -32px;
  padding: 0 0 15px;
  .timeline-row {
    display: flex;
    flex-wrap: wrap;
    .timeline-col7 {
      width: 65%;
      padding-right: 20px;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-right: 24px;
      }
    }
    .timeline-col3 {
      width: 35%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-left: 24px;
      }
    }
  }
  .wrapBtn {
    padding: 0;
    .btn-filter {
      margin-bottom: 8px;
      &.active {
        z-index: 2;
        position: relative;
        background: #448add;
        svg {
          fill: #fff;
        }
      }
    }
    .dropdown-Timeline {
      top: 0;
      position: absolute;
      z-index: 1;
      display: block;
      .item-filter {
        text-align: right;
        button {
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
      .form-InFacility-date {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        .form-icon {
          width: calc(50% - 16px);
        }
        .form-InFacility-item {
          min-width: 32px;
          text-align: center;
          font-size: 1rem;
          color: #727f88;
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
  .mr-t50 {
    margin-top: 50px;
  }
  .boxTimeline {
    background: #f2f2f2;
    border-radius: 0px 8px 8px 0px;
    box-shadow: inset 0px -3px 10px rgba(0, 0, 0, 0.05), inset 0px 3px 10px rgba(0, 0, 0, 0.05);
    padding: 10px 20px;
    height: 476px;

    @media (max-height: 768px) {
      height: 446px;
    }
  }
  .dailyList {
    .dailyList-sub {
      li {
        padding: 7px 0 13px;
      }
    }
    > ul {
      > li {
        position: relative;
        &::before {
          position: absolute;
          top: 0;
          left: 204px;
          content: '';
          height: 100%;
          width: 2px;
          background: #b7c3cb;
          display: block;
          @media (max-width: $viewport_desktop) {
            left: 140px;
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
              width: 140px;
              padding: 8px 0;
            }
          }
        }
        .dailyList-dateTime {
          width: 260px;
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
            text-align: right;
            padding-right: 12px;
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
          width: calc(100% - 260px);
          position: relative;
          @media (max-width: $viewport_desktop) {
            width: calc(100% - 187px);
          }
          .dailyList-box {
            background: #fff;
            box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
            border-radius: 8px;
            padding: 8px 110px 8px 20px;
            @media (max-width: $viewport_desktop) {
              padding: 8px 105px 8px 12px;
            }
            .dailyList-tools {
              position: absolute;
              top: 12px;
              right: 12px;
              .btn {
                padding: 0 12px;
                height: 32px;
              }
              .btn--disabled {
                pointer-events: none;
              }
            }
            .dailyList-label {
              margin-top: -4px;
              .span-label {
                margin-top: 4px;
                margin-right: 12px;
                padding: 1px 7px;
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
                width: calc(100% - 96px);
                .dailyList-spanBlue {
                  color: #448add;
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
              padding-top: 3px;
              width: calc(100% + 45px);
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
              .article-body-nodata {
                height: 80px;
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
                  color: #2d3033;
                }
                .daily-article-text {
                  color: #2d3033;
                  align-self: center;
                  display: block;
                  display: -webkit-box;
                  -webkit-line-clamp: 2;
                  -webkit-box-orient: vertical;
                  overflow: hidden;
                  text-overflow: ellipsis;
                  margin: auto 0;
                }
              }
            }
          }
          .viewMore {
            text-align: right;
            width: calc(100% + 95px);
            padding-top: 18px;
            span {
              padding-right: 20px;
              background: url(~@/assets/template/images/icon_arrow-right-blue.svg) no-repeat right;
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
  .selectionGroup {
    padding-top: 48px;
    @media (max-width: $viewport_tablet) {
      padding-top: 30px;
    }
    .selectionTitle {
      padding: 0 20px 18px;
      font-size: 1rem;
      font-weight: 700;
    }
    .selectionList {
      background: #fff;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 8px 0px 0px 8px;
      height: 440px;

      @media (max-height: 768px) {
        height: 410px;
      }
      ul {
        li {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 10px 20px 10px 14px;
          border-bottom: 1px solid #e3e3e3;
          &:last-child {
            border-bottom: none;
          }
          .selectionList-info {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            .selectionList-item {
              width: 27px;
              text-align: center;
            }
            .selectionList-content {
              width: calc(100% - 27px);
              padding-left: 14px;
              .tlt {
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 1rem;
              }
              .dateTime {
                font-size: 0.875rem;
                span {
                  &:first-child {
                    font-weight: bold;
                  }
                }
              }
            }
          }
          .btn {
            min-width: 42px;
            margin-left: 10px;
          }
        }
      }
    }
  }
  .timeline-btn {
    text-align: center;
    padding-top: 17px;
    .btn {
      margin-right: 24px;
      width: 180px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.dropdown-menu.dropdown-filter,
.btn.btn-filter {
  pointer-events: auto;
}

/* End Modal */
</style>
