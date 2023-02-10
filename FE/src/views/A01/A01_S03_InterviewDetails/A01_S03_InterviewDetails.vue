<template>
  <div v-loading="isLoading" class="wrapContent cssA01S03">
    <div class="groupInterview">
      <div class="headInterview">
        <ul>
          <li class="dateTime">
            <span class="dateTime-item">
              <ImageSVG :src-image="'icon_calendar-check01.svg'" :alt-image="'icon_calendar-check01'" />
            </span>
            <span>{{ activity_day.start_date && formatFullDateCustom(activity_day.start_date) }}</span>
          </li>
          <li v-if="activity_day.start_time && activity_day.all_day_flag !== 1" class="interview-label">
            <span>{{ formatTimeHourMinute(activity_day.start_time) }}</span>
            <span>～</span>
            <span>{{ formatTimeHourMinute(activity_day.end_time) }}</span>
          </li>
          <li v-if="activity_day.definition_label === '面談'" class="interview-label">
            <span class="interview-item"><ImageSVG :src-image="'icon_medical-hospital.svg'" :alt-image="'icon_medical-hospital'" /></span>
            <span style="cursor: pointer" class="link" @click="facilityDetail(activity_day.facility_cd)">{{ activity_day.facility_short_name }}</span>
          </li>
          <li v-if="activity_day.definition_label === '面談' && activity_day.important_flag">
            <span class="span-label span-label--yellow">重要</span>
          </li>
        </ul>
      </div>
      <div class="mainInterview scrollbar">
        <div class="interview-colForm">
          <div class="interview-title"><h2 class="tlt">日時設定</h2></div>
          <div ref="interviewFormRef" class="interviewForm scrollbar">
            <div class="interviewGroup">
              <label class="interviewGroup-label">日時</label>
              <div v-if="!checkUserOther" class="interviewGroup-dateTime">
                <div class="interviewGroup-input" :class="isSubmit && !validation.start_date.required ? 'hasErr' : ''">
                  <div class="form-icon icon-left form-icon--noBod">
                    <span class="icon">
                      <ImageSVG :src-image="'icon-calender-control.svg'" :alt-image="'icon-calender-control'" />
                    </span>
                    <el-date-picker
                      v-model="interviewDate.start_date"
                      format="YYYY/M/D"
                      type="date"
                      :editable="false"
                      placeholder="日付選択"
                      class="form-control-datePickerLeft"
                      @change="selectDate"
                    />
                  </div>
                </div>
                <div class="interviewGroup-checkBox">
                  <label class="custom-control custom-checkbox custom-control--bordGreen">
                    <input v-model="interviewDate.all_day_flag" class="custom-control-input" type="checkbox" checked @change="setDataTimepicker" />
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">終日</span>
                  </label>
                </div>
                <span v-if="isSubmit && !validation.start_date.required" class="text-error">{{ getMessage('MSFA0001', '日時') }}</span>
                <span v-else class="text-error">{{ responseErrors.start_date }}</span>
              </div>
              <div v-else class="content-view">
                <span>{{ formatFullDate(interviewDate?.start_date) }}</span>
                <span v-if="!interviewDate.all_day_flag">{{ start_time + '～' + end_time }}</span>
              </div>
              <div v-if="!checkUserOther" class="interviewGroup-time">
                <div
                  class="interviewGroup-input customLeft"
                  :class="
                    (isSubmit && !validation.start_time.required) ||
                    (isSubmit && !validation.end_time.required) ||
                    responseErrors.start_time ||
                    responseErrors.end_time
                      ? 'hasErr'
                      : ''
                  "
                >
                  <TimePicker
                    :key="keyComponentTimepicker"
                    :start-time="interviewDate.start_time"
                    :end-time="interviewDate.end_time"
                    :disabled="interviewDate.all_day_flag"
                    @onResultTimepicker="onResultTimepicker"
                  ></TimePicker>
                </div>
              </div>
              <div v-if="interviewDate.all_day_flag === false">
                <span v-if="isSubmit && !validation.start_time.required" class="text-error">{{ getMessage('MSFA0040', '日時') }}</span>
                <span v-else-if="isSubmit && !validation.end_time.required" class="text-error">{{ getMessage('MSFA0040', '日時') }}</span>

                <span v-if="responseErrors.start_time" class="text-error">{{ responseErrors.start_time }}</span>
                <span v-else class="text-error">{{ responseErrors.end_time }}</span>
              </div>
            </div>
            <div class="interviewGroup">
              <label class="interviewGroup-label">社内同行者</label>
              <div v-if="!checkUserOther" class="interviewGroup-form-control">
                <div class="form-icon icon-right">
                  <span
                    class="icon"
                    @click="
                      openModal({
                        screenID: 'Z05_S01',
                        title: 'ユーザ選択',
                        width: currDevice() !== 'Desktop' ? '95%' : '65rem',
                        props: { ...props_Z05_S01, orgCdList, userCdList }
                      })
                    "
                    @touchstart="
                      openModal({
                        screenID: 'Z05_S01',
                        title: 'ユーザ選択',
                        width: currDevice() !== 'Desktop' ? '95%' : '65rem',
                        props: { ...props_Z05_S01, orgCdList, userCdList }
                      })
                    "
                  >
                    <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                  </span>
                  <div v-if="interviewDate.accompanying_user.length > 0" class="form-control d-flex align-items-center">
                    <div class="block-tags">
                      <el-tag
                        v-for="(item, index) in interviewDate.accompanying_user"
                        v-show="item.delete_flag !== 1"
                        :key="index"
                        class="m-0 el-tag-custom"
                        closable
                        @close="handleRemoveTag(item, index)"
                      >
                        {{ item.org_label + ' ' + item.user_name }}
                      </el-tag>
                    </div>
                  </div>
                  <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                </div>
              </div>
              <div v-else class="content-view">
                <span v-for="(item, index) in interviewDate.accompanying_user" v-show="item.delete_flag !== 1" :key="index">
                  {{ item.org_label + ' ' + item.user_name }}{{ interviewDate.accompanying_user.length - 1 === index ? '' : ', ' }}
                </span>
              </div>
            </div>
            <div class="interviewGroup">
              <label class="interviewGroup-label">備考</label>
              <div
                v-if="!checkUserOther"
                class="interviewGroup-formControl"
                :class="(isSubmit && !validation.remarks.length) || responseErrors.title ? 'hasErr' : ''"
              >
                <el-input
                  id="area_rows"
                  v-model="interviewDate.remarks"
                  clearable
                  class="form-control-textarea"
                  :rows="rowNote"
                  type="textarea"
                  placeholder="内容を入力"
                />
                <span v-if="isSubmit && !validation.remarks.length" class="text-error">{{ getMessage('MSFA0012', '備考', 200) }}</span>
                <span v-else class="text-error">{{ responseErrors.title }}</span>
              </div>
              <div v-else style="white-space: break-spaces" class="content-view">{{ interviewDate?.remarks }}</div>
            </div>
            <div v-if="!checkUserOther" class="interviewGroup-checkBox mb30">
              <label class="custom-control custom-checkbox custom-control--bordGreen">
                <input v-model="interviewDate.important_flag" class="custom-control-input" type="checkbox" checked />
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">重要</span>
              </label>
            </div>
            <div :class="`interviewGroup-btn ${checkUserOther ? 'interviewGroup-btn-custom' : ''}`">
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
              <el-button v-if="checkShowBtn()" type="primary" class="btn btn-primary custom-pd" @click.prevent="createPlanSchedule">
                自分の予定として作成
              </el-button>
              <el-button
                v-if="!checkUserOther"
                type="primary"
                class="btn btn-outline-primary btn-outline-primary--cancel"
                @click="
                  openModal({
                    screenID: 'PopupConfirm',
                    width: '35rem',
                    classs: 'custom-dialog modal-fixed modal-fixed-min',
                    props: {
                      params: { option: 'deleteSchedule' },
                      title: '選択した面談を完全に削除しますか？',
                      message: '削除すると元に戻すことはできません。'
                    }
                  })
                "
              >
                削除
              </el-button>
              <el-button v-show="!checkUserOther" type="primary" class="btn btn-primary" :disabled="isUpdateProcessing" @click.prevent="updateInterview">
                保存
              </el-button>
            </div>
          </div>
        </div>
        <div class="interview-colTable">
          <div class="interviewMain">
            <div class="interviewHeade">
              <ul class="scrollbar">
                <li class="title"><h2 class="tlt">面談先</h2></li>
                <li>
                  <button
                    v-if="!checkUserOther"
                    type="button"
                    class="btn btn-link btn-link--custom"
                    @click="
                      openModal({
                        screenID: 'Z05_S04',
                        title: '施設個人選択',
                        width: currDevice() !== 'Desktop' ? '95%' : '70rem',
                        props: { mode: 'multiple', propsPrams: props_Z05_S04 }
                      })
                    "
                    @touchstart="
                      openModal({
                        screenID: 'Z05_S04',
                        title: '施設個人選択',
                        width: currDevice() !== 'Desktop' ? '95%' : '70rem',
                        props: { mode: 'multiple', propsPrams: props_Z05_S04 }
                      })
                    "
                  >
                    <span class="btn-iconLeft">
                      <ImageSVG :src-image="'icon_plus03.svg'" :alt-image="'icon_plus03'" />
                    </span>
                    面談先追加
                  </button>
                </li>
                <li>
                  <button
                    v-if="!checkUserOther"
                    type="button"
                    class="btn btn-link btn-link--custom"
                    @click="
                      openModal({
                        screenID: 'A02_S02',
                        title: 'ストック選択',
                        width: '80%',
                        props: props_A02_S02,
                        classs: 'custom-dialog custom-dialog-pd-none'
                      })
                    "
                    @touchstart="
                      openModal({
                        screenID: 'A02_S02',
                        title: 'ストック選択',
                        width: '80%',
                        props: props_A02_S02,
                        classs: 'custom-dialog custom-dialog-pd-none'
                      })
                    "
                  >
                    <span class="btn-iconLeft">
                      <ImageSVG :src-image="'icon_plus03.svg'" :alt-image="'icon_plus03'" />
                    </span>
                    ストックから追加
                  </button>
                </li>
                <li>
                  <button
                    v-if="!checkUserOther"
                    type="button"
                    class="btn btn-link btn-link--custom"
                    :disabled="interviewsContents?.length == 0"
                    @click="
                      openModal({
                        screenID: 'PopupConfirm',
                        width: '35rem',
                        classs: 'custom-dialog modal-fixed modal-fixed-min',
                        props: {
                          params: { option: 'deleteAll' },
                          title: '選択した面談先を完全に削除しますか？',
                          message: '削除すると元に戻すことはできません。'
                        }
                      })
                    "
                    @touchstart="
                      openModal({
                        screenID: 'PopupConfirm',
                        width: '35rem',
                        classs: 'custom-dialog modal-fixed modal-fixed-min',
                        props: {
                          params: { option: 'deleteAll' },
                          title: '選択した面談先を完全に削除しますか？',
                          message: '削除すると元に戻すことはできません。'
                        }
                      })
                    "
                  >
                    <span class="btn-iconLeft btn-icon--custom">
                      <ImageSVG :src-image="'icon_delete.svg'" :alt-image="'icon_delete'" />
                    </span>
                    面談先全件削除
                  </button>
                </li>
              </ul>
            </div>
            <div v-if="interviewsContents?.length > 0" id="interviewContent" ref="contentRefInterview" class="interviewContent scrollbar">
              <div v-for="(item, index) in interviewsContents" :key="index" class="interviewBox">
                <div class="interviewBox-heade">
                  <div class="interviewBox-info">
                    <ul>
                      <li class="title log-icon" title_log="個人名">
                        <h3
                          :class="lstMedicalStaff.some((x) => x.medical_staff_cd === item.person_cd) ? 'tlt-no' : 'tlt'"
                          @click="
                            lstMedicalStaff.some((x) => x.medical_staff_cd === item.person_cd) ? '' : callScreen('H02_S02', { person_cd: item.person_cd })
                          "
                        >
                          {{ item.person_name }}
                        </h3>
                      </li>
                      <li class="interview-label">
                        <span class="item"><ImageSVG :src-image="'icon_namecard.svg'" :alt-image="'icon_namecard'" /></span>
                        <span>{{ item.position_name }}</span>
                      </li>
                      <li class="interview-label">
                        <span class="item"><ImageSVG :src-image="'icon_medical.svg'" :alt-image="'icon_medical'" /></span>
                        <span>{{ item.department_name }}</span>
                      </li>
                      <li v-if="interviewInputStatus(item.plan_flag, item.act_flag)?.icon">
                        <span :class="`label-span label-span--${interviewInputStatus(item.plan_flag, item.act_flag)?.classItem}`">
                          <span class="label-span-item">
                            <ImageSVG :src-image="`${interviewInputStatus(item.plan_flag, item.act_flag)?.icon}.svg`" :alt-image="'icon_'" />
                          </span>
                          <span>{{ interviewInputStatus(item.plan_flag, item.act_flag)?.interview }}</span>
                        </span>
                      </li>
                    </ul>
                  </div>
                  <div class="interviewBox-btn">
                    <button class="btn btn--icon" data-toggle="dropdown" aria-expanded="false">
                      <span></span>
                      <span></span>
                      <span></span>
                    </button>
                    <div class="dropdown-menu dropdown-tools">
                      <span class="btn-show">
                        <span></span>
                        <span></span>
                        <span></span>
                      </span>
                      <ul>
                        <li
                          class="log-icon"
                          :title_log="
                            checkUserOther ? '詳細 ' : new Date(activity_day.start_date).getTime() > new Date().getTime() ? '計画を入力' : '実績を入力'
                          "
                          @click="callScreenA01_S04(item)"
                          @touchstart="callScreenA01_S04(item)"
                        >
                          <span class="item"><ImageSVG :src-image="'icon_edit03.svg'" :alt-image="'icon_edit03'" /></span>
                          <span class="text-label">{{
                            checkUserOther ? '詳細 ' : new Date(activity_day.start_date).getTime() > new Date().getTime() ? '計画を入力' : '実績を入力'
                          }}</span>
                        </li>
                        <li
                          v-if="!checkUserOther"
                          @click="
                            openModal({
                              screenID: 'PopupConfirm',
                              width: '35rem',
                              classs: 'custom-dialog modal-fixed modal-fixed-min',
                              props: {
                                params: { ...item, option: 'delete' },
                                title: '選択した面談先を完全に削除しますか？',
                                message: '削除すると元に戻すことはできません。'
                              }
                            })
                          "
                          @touchstart="
                            openModal({
                              screenID: 'PopupConfirm',
                              width: '35rem',
                              classs: 'custom-dialog modal-fixed modal-fixed-min',
                              props: {
                                params: { ...item, option: 'delete' },
                                title: '選択した面談先を完全に削除しますか？',
                                message: '削除すると元に戻すことはできません。'
                              }
                            })
                          "
                        >
                          <span class="item"><ImageSVG :src-image="'icon_delete.svg'" :alt-image="'icon_delete'" /></span>
                          <span class="text-label">削除</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div
                  v-if="item.dataTable?.length > 0"
                  class="interviewTbl scrollbar table-hg100 scroll-child"
                  @mouseenter="childFocus"
                  @mouseleave="childFocusOut"
                  @touchend="childFocusOut"
                  @touchstart="childFocus"
                >
                  <table class="tableBox tableCustom">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>面談内容</th>
                        <th>品目</th>
                        <th>資材</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(el, idx) in item.dataTable" :key="idx">
                        <td class="text-center">{{ el.detail_order }}</td>
                        <td>{{ el.content_name }}</td>
                        <td class="text-center">{{ el.product_name }}</td>
                        <td :class="{ 'no-padding': el?.data.length > 1 }">
                          <div v-for="(it, index2) in el.data" :key="index2" :class="`materials ${el?.data.length > 1 ? 'materials-padding' : ''}`">
                            <div class="materials-info">
                              <span
                                class="link materials-link log-icon doc-name"
                                title_log="資材名"
                                @click="callScreen('D01_S02', { document_id: it.document_id })"
                              >
                                {{ it.document_name }}
                              </span>
                              <span v-if="checkOutsideTime(it.start_date, it.end_date)" class="label-span"> 適用期間外 </span>
                            </div>
                            <button
                              type="button"
                              class="btn btn--icon"
                              @click="openModal({ screenID: 'D01_S07', title: '資材ビューワ', width: '60rem', props: { documentId: it.document_id } })"
                            >
                              <ImageSVG :src-image="'icon_slideshow.svg'" :alt-image="'icon_slideshow'" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div v-else>
              <EmptyData v-if="!isLoading" title="該当するデータがありません。" icon="amico_A01S03" custom-class="nodata" thumb-margin-top="20px" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`${modalConfig.customClass}${hasPointerEvent ? '' : ' pointer-none'}`"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
      @close="handleClose()"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="reloadAction(closeModal, 'reload')($event)"
          @handleConfirm="deleteBtn"
          @handleYes="handleYes"
          @changePointerEvent="changePointerEvent"
        />
      </template>
    </el-dialog>
  </div>
</template>
<script>
import A01_S03_Service from '@/api/A01/A01_S03_InterviewDetailsServices';
import A02_S02_ModalSelectStock from '@/views/A02/A02_S02_SelectStock/A02_S02_SelectStock';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05_S04_FacilityCustomerSelection from '@/views/Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection';
import D01_S07_MaterialViewer from '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer';
import Z05_S04_facilityCustomerService from '@/api/Z05/Z05_S04_facilityCustomerService';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';
import PerfectScrollbar from '@/assets/template/js/perfect-scrollbar.min.js';

import { required, validLength } from '@/utils/validate';
import { markRaw } from 'vue';
import { mapGetters } from 'vuex';
import _ from 'lodash';

export default {
  name: 'A01_S03_InterviewDetails',
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      hasPointerEvent: true,
      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      props_A02_S02: {
        inFacilityCd: ['011010020']
      },
      props_Z05_S01: {
        mode: 'multiple',
        userFlag: 1,
        userSelectFlag: 1
      },
      schedule_id: 2, // params URL
      props_Z05_S04: {
        non_facility_flag: 1, // req
        non_medical_flag: 0, //req
        checked_facility_relation_flag: 0,
        disabled_facility_relation_flag: 1,
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
      checkUserOther: true, // true: only view, false: edit content
      isLoading: false,
      processing: false,
      isUpdateProcessing: false,
      activity_day: {
        report_status_type: '',
        important_flag: 0,
        start_date: '',
        start_time: '',
        end_time: '',
        facility_short_name: '',
        facility_cd: '',
        schedule_type: '',
        schedule_id: null,
        display_option_type: '',
        visit_id: ''
      },
      interviewsContents: [],
      interviewDate: {
        accompanying_user: [],
        start_date: '',
        start_time: '',
        end_time: '',
        all_day_flag: true,
        important_flag: true,
        visit_id: null,
        accompanying_id: '',
        remarks: '',
        user_cd: ''
      },

      interviewDateOld: {},

      orgCdList: [],
      userCdList: [],
      start_time: '',
      end_time: '',

      lstMedicalStaff: [],
      start_time_default: '',
      end_time_default: '',
      keyComponentTimepicker: Math.random(),
      rowNote: 2,
      lstPersonDefault: []
    };
  },
  computed: {
    ...mapGetters({
      historyModal: 'router/isHistoryModal',
      historyName: 'router/isHistoryParams',
      historyParams: 'router/isHistoryComponentName',
      fromPath: 'router/fromPath',
      toPath: 'router/toPath'
    }),
    validation() {
      return {
        remarks: { length: validLength(this.interviewDate.remarks, 200) },
        start_date: { required: required(this.interviewDate.start_date) },
        start_time: this.interviewDate.all_day_flag ? true : { required: required(this.interviewDate.start_time) },
        end_time: this.interviewDate.all_day_flag ? true : { required: required(this.interviewDate.end_time) }
      };
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'スケジュール詳細設定',
      icon: 'icon_interview-color.svg',
      isShowBack: true
    });

    if (this._route('A01_S03_InterviewDetails')?.params?.schedule_id) {
      this.schedule_id = this._route('A01_S03_InterviewDetails')?.params?.schedule_id;
    }
    this.getActiveDate(this.schedule_id, true);
    if (this.checkHistoryRouting) {
      if (this.historyModal && this.historyName) {
        this.openModal({
          screenID: 'A02_S02',
          title: 'ストック選択',
          width: '80%',
          props: this.props_A02_S02,
          classs: 'custom-dialog custom-dialog-pd-none'
        });
      }
    }

    this.checkRow();

    window.addEventListener('resize', function () {
      if (window.screen.availHeight < 780 || window.innerHeight < 780) {
        this.rowNote = 2;
      } else {
        this.rowNote = 6;
      }
      let area_rows = document.getElementById('area_rows');
      if (area_rows) {
        area_rows.setAttribute('rows', this.rowNote);
      }
    });

    const els = document.querySelectorAll('.scroll-child');
    const elMain = this.$refs.contentRefInterview;
    els.forEach((el) => {
      el.onscroll = function () {
        elMain.classList.add('stop-scroll');
        return;
      };
    });
  },

  updated() {
    this.$nextTick(() => {
      if (!_.isEqual(this.interviewDate, this.interviewDateOld)) {
        localStorage.setItem('checkChangTab', true);
      } else {
        localStorage.removeItem('checkChangTab');
      }
    });
  },

  methods: {
    checkRow() {
      if (window.screen.availHeight < 780 || window.innerHeight < 780) {
        this.rowNote = 2;
      } else {
        this.rowNote = 6;
      }
      let area_rows = document.getElementById('area_rows');
      if (area_rows) {
        area_rows.setAttribute('rows', this.rowNote);
      }
    },

    changePointerEvent(value) {
      this.hasPointerEvent = value;
    },
    checkHistoryRouting() {
      let { fromPath, toPath } = this;
      if (fromPath === '/convention-input' && toPath === '/schedule-details-setting') {
        return true;
      } else {
        this.$store.dispatch('router/clearHistoryModal');
        return false;
      }
    },
    // define func
    cancelBtn() {
      this.back();
    },

    confirmCancel() {
      let isEqual = !_.isEqual(this.interviewDate, this.interviewDateOld);

      if (isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1, params: { isCancel: true } },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.cancelBtn();
      }
    },

    checkShowBtn() {
      const userLogin = this.$store.state.auth.currentUser.user_cd;
      return this.interviewDate.accompanying_user.some((el) => el.user_cd === userLogin);
    },

    checkOutsideTime(startDate, endDate) {
      let today = new Date(new Date().setHours(0, 0, 0, 0));
      if (
        new Date(new Date(startDate).setHours(0, 0, 0, 0)).getTime() <= today.getTime() &&
        today.getTime() <= new Date(new Date(endDate).setHours(0, 0, 0, 0)).getTime()
      ) {
        return false;
      }
      return true;
    },

    handleRemoveTag(item, index) {
      if (this.checkUserOther) return;
      this.interviewDate.accompanying_user.splice(index, 1);
      let orgCdListTemp = [];
      let userCdListTemp = [];
      this.interviewDate.accompanying_user.forEach((el) => {
        orgCdListTemp.push(el.org_cd);
        userCdListTemp.push({
          org_cd: el.org_cd,
          user_cd: el.user_cd
        });
      });
      this.orgCdList = Array.from(new Set(orgCdListTemp));
      this.userCdList = Array.from(new Set(userCdListTemp));
    },

    interviewInputStatus(plan_flag, act_flag) {
      let interview = '';
      let icon = '';
      let classItem = '';
      switch (plan_flag) {
        case 0:
          if (act_flag === 0) {
            // null
          } else {
            interview = '実績';
            icon = 'icon_briefcase';
            classItem = 'yellow';
          }
          break;
        case 1:
          if (act_flag === 0) {
            interview = '計画';
            icon = 'icon_clock-filled';
            classItem = 'blue';
          } else {
            interview = '計画＆実績';
            icon = 'icon_briefcase-clock';
            classItem = 'green';
          }
          break;
        default:
          break;
      }
      return { interview, icon, classItem };
    },

    setDataTimepicker() {
      if (!this.interviewDate.all_day_flag) {
        this.interviewDate = {
          ...this.interviewDate,
          start_time: this.start_time_default,
          end_time: this.end_time_default
        };
      }
      this.keyComponentTimepicker = Math.random();
    },

    onResultTimepicker(startTime, endTime) {
      if (startTime && endTime) {
        this.interviewDate = {
          ...this.interviewDate,
          start_time: startTime,
          end_time: endTime
        };
      } else {
        this.interviewDate.all_day_flag = true;
      }
    },

    selectDate(date) {
      this.interviewDate = {
        ...this.interviewDate,
        start_date: date ? this.formatFullDate(date) : ''
      };
    },

    updateInterview() {
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      if (this.checkUserOther) return;
      let obj = {
        start_time: '',
        end_time: ''
      };
      if (this.interviewDate.all_day_flag && this.interviewDate.start_date) {
        obj = {
          start_time: `${this.interviewDate.start_date} 00:00:00`,
          end_time: `${this.interviewDate.start_date} 23:59:59`
        };
      } else if (this.interviewDate.start_date && this.interviewDate.start_time && this.interviewDate.end_time) {
        obj = {
          start_time: `${this.interviewDate.start_date} ${this.interviewDate.start_time}`,
          end_time: `${this.interviewDate.start_date} ${this.interviewDate.end_time}`
        };
      }
      this.saveDateTimeSetting({
        ...this.interviewDate,
        schedule_id: this.schedule_id,
        all_day_flag: this.interviewDate.all_day_flag ? 1 : 0,
        important_flag: this.interviewDate.important_flag ? 1 : 0,
        facility_short_name: this.activity_day.facility_short_name,
        display_option_type: this.activity_day.display_option_type,
        ...obj,
        start_date_old: this.interviewDateOld.start_date
      });
    },

    deleteAllBtn() {
      this.modalConfig.isShowModal = false;
      this.deleteAllInterview({
        schedule_id: this.schedule_id,
        visit_id: this.activity_day.visit_id
      });
    },

    deleteBtn(item) {
      this.modalConfig.isShowModal = false;
      if (item.option === 'delete') {
        const params = {
          schedule_id: this.schedule_id,
          facility_short_name: this.activity_day.facility_short_name,
          visit_id: item.visit_id,
          person_cd: item.person_cd,
          person_name: item.person_name,
          display_option_type: this.activity_day.display_option_type,
          call_id: item.call_id
        };
        this.deleteInterviewer(params);
      } else if (item.option === 'deleteSchedule') {
        this.deleteSchedule();
      } else {
        this.deleteAllBtn();
      }
    },

    callScreenA01_S04(item) {
      let params = {
        call_id: item.call_id,
        schedule_id: this.schedule_id
      };
      A01_S04_Service.checkInterviewDetailInput(params)
        .then(() => {
          this.callScreen('A01_S04', params);
        })
        .catch(() => {
          this.$notify({ message: '面談情報が削除されたため、詳細を見ることができません。', customClass: 'error' });
        })
        .finally(() => {});
    },

    callScreen(screenID, props = {}) {
      let objScreen = {
        A01_S04: 'A01_S04_InterviewDetailedInput',
        D01_S02: 'D01_S02_MaterialDetails'
      };
      if (screenID === 'H02_S02') {
        let person_cd = props.person_cd;
        localStorage.setItem('activeMenu', 'A01_S03_InterviewDetails');
        this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
        return;
      }
      this.$router.push({ name: objScreen[screenID], params: props });
    },

    // call api
    getActiveDate(params, reload) {
      this.isLoading = true;
      A01_S03_Service.getActiveDateService({ schedule_id: params })
        .then((res) => {
          const dataRes = res.data.data;
          this.activity_day = dataRes;
          this.props_Z05_S04.facility_cd = [dataRes.facility_cd];
          this.props_A02_S02.inFacilityCd = dataRes.facility_cd;
          this.getDateTimeSetting(this.schedule_id, reload);
        })
        .catch((err) => {
          if (+err.response.data.status !== 404) {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
            this.cancelBtn();
          }
        })
        .finally();
    },

    getMedicalStaff(facility_cd) {
      let params = {
        facility_cd: facility_cd
      };
      Z05_S04_facilityCustomerService.getMedicalStaff(params)
        .then((res) => {
          this.lstMedicalStaff = res?.data?.data;

          this.getInterviewContent(this.schedule_id);
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    getInterviewContent(params) {
      A01_S03_Service.getInterviewContentService({ schedule_id: params })
        .then((res) => {
          const dataRes = res.data.data;
          this.interviewsContents = dataRes;
          this.lstPersonDefault = dataRes;
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 300));
          this.isLoading = false;
          this.js_pscroll();
          this.psContainer = new PerfectScrollbar('#interviewContent', {
            wheelSpeed: 0.5
          });
        });
    },

    getDateTimeSetting(params, reload) {
      A01_S03_Service.getDateTimeSettingService({ schedule_id: params })
        .then((res) => {
          const dataRes = res.data.data;
          this.interviewDate = {
            ...this.interviewDate,
            ...dataRes,
            start_date: this.formatFullDate(dataRes.start_date),
            start_time: this.formatTimeHourMinute(dataRes.start_time),
            end_time: this.formatTimeHourMinute(dataRes.end_time),
            all_day_flag: dataRes.all_day_flag === 1,
            important_flag: dataRes.important_flag === 1
          };

          this.interviewDateOld = JSON.parse(JSON.stringify(this.interviewDate));

          this.start_time = this.formatTimeHourMinute(dataRes.start_time);
          this.end_time = this.formatTimeHourMinute(dataRes.end_time);

          let orgCdListTemp = [];
          let userCdListTemp = [];
          if (dataRes.accompanying_user.length > 0) {
            dataRes.accompanying_user.forEach((el) => {
              orgCdListTemp.push(el.org_cd);
              userCdListTemp.push({
                org_cd: el.org_cd,
                user_cd: el.user_cd
              });
            });
          }

          this.orgCdList = Array.from(new Set(orgCdListTemp));
          this.userCdList = Array.from(new Set(userCdListTemp));

          this.start_time_default = reload ? this.start_time : dataRes.all_day_flag === 1 ? this.start_time_default : this.start_time;
          this.end_time_default = reload ? this.end_time : dataRes.all_day_flag === 1 ? this.end_time_default : this.end_time;

          const userLogin = this.$store.state.auth.currentUser.user_cd;
          this.checkUserOther = true;
          if (userLogin === dataRes.user_cd) {
            if (dataRes.report_status_type === '提出済' || dataRes.report_status_type === '承認済') this.checkUserOther = true;
            else this.checkUserOther = false;
          }

          this.keyComponentTimepicker = Math.random();
          this.getMedicalStaff(this.activity_day.facility_cd);
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          if (this.$refs.interviewFormRef) {
            this.$refs.interviewFormRef.scrollTop = 1;
          }
        });
    },

    saveDateTimeSetting(params) {
      this.processing = true;
      this.isUpdateProcessing = true;
      this.isLoading = true;
      A01_S03_Service.saveDateTimeSettingService(params)
        .then((res) => {
          this.getActiveDate(this.schedule_id);
          this.resetData();
          setTimeout(() => {
            this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
          }, 300);
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.processing = false;
          this.isUpdateProcessing = false;
        });
    },

    deleteInterviewer(params) {
      A01_S03_Service.deleteInterviewerService(params)
        .then((res) => {
          this.$notify({ message: res.data.message || this.getMessage('MSFA0050'), customClass: 'success' });
          this.getActiveDate(this.schedule_id);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          localStorage.setItem('reload', 1);
        });
    },

    deleteAllInterview(params) {
      A01_S03_Service.deleteAllInterviewService(params)
        .then((res) => {
          this.$notify({ message: res.data.message || this.getMessage('MSFA0050'), customClass: 'success' });
          this.getActiveDate(this.schedule_id);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          localStorage.setItem('reload', 1);
        });
    },

    addInterviewDestination(params) {
      A01_S03_Service.addInterviewDestinationService(params)
        .then((res) => {
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
          this.getActiveDate(this.schedule_id);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },

    checkPersonCd(params) {
      let paramRequest = {
        visit_id: params.visit_id,
        schedule_id: params.schedule_id,
        dataStock: params.stock
      };
      A01_S03_Service.checkPersonCdService(paramRequest)
        .then((res) => {
          const dataRes = res.data.data;
          if (dataRes.message)
            this.openModal({
              screenID: 'PopupConfirm',
              width: '35rem',
              classs: 'custom-dialog modal-fixed modal-fixed-min',
              props: {
                mode: 1,
                textCancel: 'いいえ',
                message: dataRes.message,
                params: params
              }
            });
          else this.addStock(params);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },

    addStock(params) {
      A01_S03_Service.addStockService(params)
        .then((res) => {
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
          this.getActiveDate(this.schedule_id);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },

    createPlanSchedule() {
      this.processing = true;
      const params = {
        schedule_id: this.activity_day.schedule_id,
        visit_id: this.activity_day.visit_id
      };
      A01_S03_Service.createPlanScheduleService(params)
        .then((res) => {
          let schedule_id = res.data.data.schedule_id;
          let dateStart = this.activity_day.start_date;
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });

          localStorage.setItem('zoomBox', true);

          this.$router.push({ name: 'A01_S01_ScheduleInput', params: { scheduleId: schedule_id, dateStart: dateStart } });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.processing = false));
    },

    deleteSchedule() {
      this.processing = true;
      A01_S03_Service.deleteScheduleService({
        schedule_id: this.schedule_id,
        visit_id: this.activity_day.visit_id,
        start_date_old: this.interviewDateOld.start_date
      })
        .then((res) => {
          this.cancelBtn();
          this.$notify({ message: res.data.message || this.getMessage('MSFA0050'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.processing = false));
    },

    // modal
    openModal({ screenID, title = '', width = 0, props = {}, classs = 'custom-dialog' }) {
      if (!screenID) return;
      if (screenID === 'Z05_S04') {
        props.propsPrams.facility_cd = [this.activity_day.facility_cd];
      }
      let objScreen = {
        A02_S02: A02_S02_ModalSelectStock,
        D01_S07: D01_S07_MaterialViewer,
        Z05_S01: Z05_S01_ModalOrganization,
        Z05_S04: Z05_S04_FacilityCustomerSelection,
        PopupConfirm: this.$PopupConfirm
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: title,
        isShowModal: true,
        customClass: classs,
        component: this.markRaw(objScreen[screenID]),
        props: props,
        width: width,
        isShowClose: !(objScreen[screenID].name === 'PopupConfirm')
      };
      if (screenID === 'A02_S02') {
        this.$store.dispatch('router/setScreenInfor', { modal: true });
      }
    },

    handleYes(params) {
      if (params?.isCancel) {
        this.cancelBtn();
      } else {
        this.addStock(params);
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: false,
        isShowClose: true
      };
    },

    closeModal(data) {
      this.$store.dispatch('router/setScreenInfor', { modal: false });
      this.$store.dispatch('router/clearHistoryModal');
      switch (data?.screen) {
        case 'Z05_S01': {
          let arrTemp = [];
          let orgCdListTemp = [];
          let userCdListTemp = [];
          let arrReject = [];
          const userLogin = this.$store.state.auth.currentUser.user_cd;
          data.userSelected.forEach((el) => {
            if (el.user_cd !== userLogin) {
              arrTemp.push({
                change_flag: 1,
                delete_flag: 0,
                org_cd: el.org_cd,
                org_label: el.org_label,
                org_short_name: el.org_name,
                accompanying_id: '',
                user_cd: el.user_cd,
                user_name: el.user_name
              });
              orgCdListTemp.push(el.org_cd);
              userCdListTemp.push({
                org_cd: el.org_cd,
                user_cd: el.user_cd
              });
            } else arrReject.push(el.user_name);
          });
          if (arrReject.length) this.$notify({ message: this.getMessage('MSFA0123'), customClass: 'error' });
          this.orgCdList = Array.from(new Set(orgCdListTemp));
          this.userCdList = Array.from(new Set(userCdListTemp));
          this.interviewDate.accompanying_user = arrTemp;
          break;
        }
        case 'A02_S02_SelectStock': {
          const params = {
            visit_id: this.activity_day.visit_id,
            schedule_id: this.schedule_id,
            stock: data.dataSelected
          };
          this.checkPersonCd(params);
          break;
        }
        case 'Z05_S04': {
          let params = {};
          let person = [];
          if (data?.facilityPersonalSelected?.length > 0) {
            for (let item of data.facilityPersonalSelected) {
              if (!this.lstPersonDefault.some((x) => x.person_cd === item.person_cd)) {
                person.push({
                  person_cd: item.person_cd,
                  person_name: item.person_name,
                  department_cd: item.department_cd,
                  department_name: item.department_name
                });
              }
            }
          }
          if (data?.medicalStaffSelected?.length > 0) {
            for (let item of data.medicalStaffSelected) {
              if (!this.lstPersonDefault.some((x) => x.person_cd === item.medical_staff_cd)) {
                person.push({
                  person_cd: item.medical_staff_cd,
                  person_name: item.medical_staff_name,
                  department_cd: null,
                  department_name: null
                });
              }
            }
          }

          params = {
            visit_id: this.activity_day.visit_id,
            schedule_id: this.schedule_id,
            facility_short_name: this.activity_day.facility_short_name,
            facility_cd: this.activity_day.facility_cd,
            display_option_type: this.activity_day.display_option_type,
            person: person
          };

          if (person.length > 0) {
            this.addInterviewDestination(params);
          }
          break;
        }
        default:
          break;
      }
      this.onCloseModal();
    },

    handleClose() {
      this.$store.dispatch('router/clearHistoryModal');
    },

    facilityDetail(facility_cd) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '1' }
      });
    },

    childFocusOut() {
      const elMain = this.$refs.contentRefInterview;
      if (elMain && elMain?.classList) {
        elMain?.classList?.remove('stop-scroll');

        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = new PerfectScrollbar('#interviewContent', {
          wheelSpeed: 0.5
        });
      }
    },

    childFocus() {
      const els = document.querySelectorAll('.scroll-child');
      const elMain = this.$refs.contentRefInterview;

      setTimeout(() => {
        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = null;
      }, 0);

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
.content-view {
  color: #2d3033;
  span:first-of-type {
    margin-right: 10px;
  }
}
.nodata {
  height: 100%;
}
.interviewGroup-checkBox {
  width: calc(100% - 223px);
  @media (min-width: $viewport_tablet) and (max-width: $viewport_desktop) {
    width: calc(100% - 180px);
  }
  &.mb30 {
    margin-bottom: 20px;
  }
  .custom-control {
    margin-right: 10px;
    &:last-child {
      margin-right: 0;
    }
  }
}
.groupInterview {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding-top: 28px;
  .headInterview {
    background: #fff;
    padding: 13px 24px;
    ul {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      li {
        margin-right: 24px;
        &:last-child {
          margin-right: 0;
        }
        &.dateTime {
          font-size: 1.5rem;
          font-weight: 700;
          color: #000;
          display: flex;
          .dateTime-item {
            min-width: 24px;
            margin: -2px 12px 0 0;
          }
        }
        &.interview-label {
          font-size: 16px;
          display: flex;
          .interview-item {
            min-width: 15px;
            margin: -2px 6px 0 0;
          }
        }
      }
    }
  }
  .mainInterview {
    background: #fff;
    height: 100%;
    overflow: hidden;
    box-shadow: 0px 0px 5px #b7c3cb;
    display: flex;
    flex-wrap: wrap;
    padding-top: 10px;
    @media (max-width: $viewport_tablet) {
      overflow-y: auto;
    }
    .interview-colForm {
      width: 494px;
      height: 100%;
      padding: 8px 0 20px 0;
      display: flex;
      flex-direction: column;
      @media (max-width: $viewport_desktop) {
        width: 30%;
      }
      @media (max-width: $viewport_tablet) {
        width: 100%;
        height: auto;
      }
    }
    .interview-colTable {
      width: calc(100% - 494px);
      height: 100%;
      padding-top: 8px;
      @media (max-width: $viewport_desktop) {
        width: 70%;
      }
      @media (max-width: $viewport_tablet) {
        width: 100%;
        height: auto;
        padding: 8px 24px 0;
      }
      .interviewMain {
        background: #f7f7f7;
        box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
        border-radius: 20px 0px 0px 0px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        height: 100%;
        @media (max-width: $viewport_tablet) {
          border-radius: 20px 20px 0px 0px;
        }
      }
    }
    .interview-title {
      max-width: 200px;
      @media (max-width: $viewport_tablet) {
        max-width: 180px;
        .tlt {
          padding-left: 40px !important;
        }
      }
      .tlt {
        background: #eef6ff;
        box-shadow: 0 2px 8px 3px rgba(145, 161, 171, 0.6);
        border-radius: 0px 40px 40px 0px;
        font-size: 1.125rem;
        font-weight: bold;
        padding: 14px 12px 14px 52px;
      }
    }
    .interviewForm {
      padding: 0 52px;
      margin-top: 20px;
      @media (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
        padding: 0 12px;
        margin-top: 12px;
      }
      .interviewGroup {
        margin-bottom: 15px;
        .interviewGroup-label {
          margin-bottom: 5px;
          font-size: 1rem;
          line-height: 120%;
        }
        .interviewGroup-dateTime,
        .interviewGroup-time {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          .interviewGroup-input {
            width: 200px;
            @media (min-width: $viewport_tablet) and (max-width: $viewport_desktop) {
              width: 160px;
            }
          }
          .interviewGroup-checkBox {
            width: calc(100% - 223px);
            padding-left: 16px;

            @media (min-width: $viewport_tablet) and (max-width: $viewport_desktop) {
              width: calc(100% - 160px);
            }
            .custom-control {
              margin-right: 10px;
              &:last-child {
                margin-right: 0;
              }
            }
          }
        }
        .interviewGroup-time {
          margin-top: 10px;
        }
      }
      .interviewGroup-btn {
        text-align: right;
        .btn {
          width: 110px !important;
          margin-left: 10px;
          margin-bottom: 10px;
          padding: 4px;
        }
        &.interviewGroup-btn-custom .btn {
          width: 180px !important;
          padding: 4px;
        }

        @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
          display: flex;
          justify-content: center;
          gap: 5px;
          .btn {
            margin-left: 0;
            width: auto;
          }
        }
      }
    }
    .interviewHeade {
      ul {
        white-space: nowrap;
        padding-bottom: 20px;
        li {
          display: inline-block;
          margin-right: 20px;
          @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
            margin-right: 10px;
          }
          @media (max-width: $viewport_tablet) {
            margin-right: 15px;
            &.title {
              max-width: 180px;
              .tlt {
                padding-left: 40px !important;
              }
            }
            .btn {
              padding: 0 10px !important;
              @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
                padding: 0 !important;
              }
              span.btn-iconLeft {
                margin-right: 3px !important;
              }
            }
          }
          &.title {
            width: 200px;
            .tlt {
              background: #eef6ff;
              box-shadow: 5px 5px 8px 3px rgba(145, 161, 171, 0.4);
              border-radius: 20px 0px 20px 0px;
              padding: 14px 52px;
              display: block;
              font-size: 1.125rem;
              font-weight: 700;
            }
            @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
              width: auto;
            }
          }
          .btn {
            padding: 0 25px;
            border-radius: 0px 0px 8px 8px;
            @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
              padding: 0 12px;
            }
          }

          @media (hover: hover) {
            .btn-link--custom:hover {
              background: #007bff;
              color: #ffffff;
              .btn-icon--custom {
                stroke: #ffffff;
              }
              svg.svg,
              svg.svg path {
                fill: #ffffff !important;
              }
            }
          }
        }
      }
    }
    .interviewContent {
      height: 100%;
      padding: 0px 0 4px 20px;
      @media (max-width: $viewport_tablet) {
        height: 400px;
        padding: 4px 20px;
      }
      .interviewBox {
        background: #ffffff;
        border-radius: 12px 0px 0px 12px;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        padding: 24px;
        margin-bottom: 20px;
        margin-top: 4px;
        @media (max-width: $viewport_tablet) {
          border-radius: 12px;
        }
      }
      .interviewBox-heade {
        display: flex;
        flex-wrap: wrap;
        .interviewBox-info {
          width: calc(100% - 42px);
          padding-right: 10px;
          ul {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            li {
              margin-right: 24px;
              &:last-child {
                margin-right: 0;
              }
              &.title {
                .tlt {
                  cursor: pointer;
                  color: #448add;
                  font-size: 1.5rem;
                  font-weight: 700;
                }
                .tlt-no {
                  font-size: 1.5rem;
                  font-weight: 700;
                }
              }
              &.interview-label {
                font-size: 1rem;
                display: flex;
                .item {
                  min-width: 16px;
                  margin: -1px 5px 0 0;
                }
              }
              .label-span {
                padding: 2px 16px;
                border-radius: 2px;
                display: inline-block;
                &.label-span--yellow {
                  color: #d88166;
                  background: #ffe2ba;
                }
                &.label-span--green {
                  color: #28a470;
                  background: #daf8dc;
                }
                &.label-span--blue {
                  color: #348aae;
                  background: #ceedff;
                }
                .label-span-item {
                  margin-right: 4px;
                  margin-top: -1px;
                  display: inline-block;
                }
              }
            }
          }
        }
        .interviewBox-btn {
          width: 42px;
          position: relative;
          .btn {
            &.btn--icon {
              display: flex;
              justify-content: center;
              align-items: center;
              span {
                width: 4px;
                height: 4px;
                background: #448add;
                border-radius: 50%;
                display: inline-block;
                margin: 0 2px;
              }
            }
          }
          .dropdown-tools {
            left: inherit !important;
            right: 0 !important;
            transform: none !important;
            margin: 0;
            padding: 0;
            background: #ffffff;
            box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
            border-radius: 20px 24px 20px 20px;
            border: none;
            min-height: 42px;
            width: 170px;
            min-width: inherit;
            .btn-show {
              box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
              background: #448add;
              padding: 0;
              width: 42px;
              height: 42px;
              border-radius: 50%;
              display: flex;
              justify-content: center;
              align-items: center;
              position: absolute;
              top: 0;
              right: 0;
              span {
                width: 4px;
                height: 4px;
                background: #fff;
                border-radius: 50%;
                display: inline-block;
                margin: 0 2px;
              }
            }
            ul {
              border-radius: 20px 24px 20px 20px;
              overflow: hidden;
              li {
                padding: 6px 46px 6px 20px;
                cursor: pointer;
                color: #448add;
                font-weight: 500;
                font-size: 0.875rem;
                display: flex;
                min-height: 42px;
                line-height: 30px;
                &:hover {
                  background: #f2f2f2;
                }
                .item {
                  min-width: 18px;
                  margin-right: 4px;
                  margin-top: -2px;
                  text-align: center;
                }
              }
            }
          }
        }
      }
      .interviewTbl {
        background: #ffffff;
        box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
        border-radius: 8px;
        margin-top: 18px;
        max-height: 315px;
        tr {
          &:last-child {
            td {
              border-bottom: none;
            }
          }
          th {
            &:nth-child(1) {
              width: 60px;
              text-align: center;
            }
            &:nth-child(2) {
              width: 1rem;
            }
            &:nth-child(3) {
              width: 1rem;
              text-align: center;
            }
            &:nth-child(4) {
              width: 1rem;
            }
          }
          td {
            padding: 12px;
            .materials {
              display: flex;
              flex-wrap: wrap;
              align-items: center;
              .materials-info {
                width: calc(100% - 42px);
                padding-right: 10px;
                display: flex;
                gap: 16px;
                align-items: center;
                .materials-link {
                  font-size: 1rem;
                }
                .label-span {
                  display: inline-block;
                  background: #feddde;
                  border-radius: 2px;
                  color: #ea5d54;
                  padding: 2px 6px;
                  word-break: keep-all;
                  .label-span-item {
                    display: inline-block;
                    margin: -2px 4px 0 0;
                    cursor: pointer;
                  }
                }
              }
              &.materials-padding {
                padding: 12px;
                border-bottom: 1px solid #e3e3e3;
                &:last-of-type {
                  border-bottom: none;
                }
              }
            }
          }
          td.no-padding {
            padding: 0;
          }
        }
      }
    }
  }
}
.tableCustom tbody {
  tr {
    td:nth-child(1) {
      width: 5%;
    }
    td:nth-child(2) {
      width: 20%;
    }
    td:nth-child(3) {
      width: 15%;
    }
    td:last-child {
      width: 60%;
      max-width: 0vw;
    }
  }
}
.doc-name {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
#interviewContent {
  position: relative;
  overflow: hidden !important;
}
.stop-scroll {
  overflow: hidden;
}
</style>
