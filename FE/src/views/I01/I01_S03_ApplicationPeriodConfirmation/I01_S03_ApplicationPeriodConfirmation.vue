<template>
  <div class="wrapContent">
    <div v-loading="loadingPage" v-load-f5="true" class="groupContent">
      <div class="wrapBtn wrapBtn--change">
        <div class="referenceDate">
          <div class="referenceDate-control">
            <span class="referenceDate-label">基準日</span>
            <div class="referenceDate-form log-icon" title_log="基準日">
              <div class="form-icon icon-left form-icon--noBod">
                <span class="icon">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="icon" />
                </span>
                <el-date-picker
                  v-model="formData.date"
                  type="date"
                  placeholder="日付選択"
                  class="form-control-datePickerLeft"
                  format="YYYY/M/D"
                  :clearable="false"
                  :editable="false"
                  value-format="YYYY/M/D"
                  @change="onFilterData(false)"
                ></el-date-picker>
              </div>
            </div>
          </div>
          <div class="referenceDate-lst">
            <ul>
              <li>
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input v-model="showUserInfo" class="custom-control-input" type="checkbox" @change="onScrollFixed(showUserInfo, 'scrollUserInfo')" />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">ユーザ情報</span>
                </label>
              </li>
              <li>
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input
                    v-model="showOrgIntelligence"
                    class="custom-control-input"
                    type="checkbox"
                    @change="onScrollFixed(showOrgIntelligence, 'scrollOrgIntelligence')"
                  />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">組織情報</span>
                </label>
              </li>
              <li>
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input
                    v-model="showAuthorityInfo"
                    class="custom-control-input"
                    type="checkbox"
                    @change="onScrollFixed(showAuthorityInfo, 'scrollAuthorityInfo')"
                  />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">権限情報</span>
                </label>
              </li>
              <li>
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input
                    v-model="showAcknowledgeReport"
                    class="custom-control-input"
                    type="checkbox"
                    @change="onScrollFixed(showAcknowledgeReport, 'scrollAcknowledgeReport')"
                  />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">承認（報告)</span>
                </label>
              </li>
              <li>
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input
                    v-model="showApprovalBriefingSession"
                    class="custom-control-input"
                    type="checkbox"
                    @change="onScrollFixed(showApprovalBriefingSession, 'scrollApprovalBriefingSession')"
                  />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">承認(説明会)</span>
                </label>
              </li>
              <li>
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input
                    v-model="showAcknowledgmentLecture"
                    class="custom-control-input"
                    type="checkbox"
                    @change="onScrollFixed(showAcknowledgmentLecture, 'scrollAcknowledgmentLecture')"
                  />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">承認(講演会)</span>
                </label>
              </li>
              <li>
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input
                    v-model="showApprovalknowledge"
                    class="custom-control-input"
                    type="checkbox"
                    @change="onScrollFixed(showApprovalknowledge, 'scrollApprovalknowledge')"
                  />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">承認(ナレッジ)</span>
                </label>
              </li>
            </ul>
          </div>
        </div>
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button class="btn btn-filter" type="button" @click="openModalFilter" @touchstart="openModalFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="icon" />
            </button>
            <div :class="['dropdown-menu dropdown-block dropdown-filter dropdown-UserManage', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter" @click="onCloseFilter" @touchstart="onCloseFilter">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="icon" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="formUserManage">
                <ul>
                  <li class="w-100">
                    <label class="formUserManage-label">ユーザ名</label>
                    <div class="formUserManage-control">
                      <el-input v-model="formData.user_name" clearable placeholder="名前入力" class="form-control-input" />
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="formUserManage-label">ユーザコード</label>
                    <div class="formUserManage-control">
                      <el-input v-model="formData.user_cd" clearable placeholder="コード入力" class="form-control-input" />
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="formUserManage-label">組織</label>
                    <div class="formUserManage-control">
                      <div class="form-icon icon-right">
                        <span class="icon icon--cursor log-icon" title_log="組織" @click="openModalZ05S01()" @touchstart="openModalZ05S01()">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="icon" />
                        </span>
                        <div v-if="formData?.orgList?.length > 0" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag v-for="item of formData.orgList" :key="item.org_cd" class="el-tag-custom" closable @close="handleRemoveFacility(item)">
                              {{ item.org_label }}
                            </el-tag>
                          </div>
                        </div>
                        <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="btnFilter-UserManage">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCloseFilter">キャンセル</button>
                <button type="button" class="btn btn-primary" @click="onFilterData(false)">検索</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="application">
        <div class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
        </div>
        <div :class="lstData.length === 0 ? 'application-tbl-none ' : ''" class="application-tbl table-hg100 scrollbar" @scroll="onScroll">
          <table>
            <thead>
              <tr>
                <th class="table-border-head">現行ユーザ情報</th>
                <th v-if="showUserInfo" class="table-border-head">ユーザ情報</th>
                <th v-if="showOrgIntelligence" class="table-border-head">組織情報</th>
                <th v-if="showAuthorityInfo" class="table-border-head">権限情報</th>
                <th v-if="showAcknowledgeReport" class="table-border-head">承認（報告）</th>
                <th v-if="showApprovalBriefingSession" class="table-border-head">承認(説明会)</th>
                <th v-if="showAcknowledgmentLecture" class="table-border-head">承認（講演会）</th>
                <th v-if="showApprovalknowledge" class="table-border-head">承認（ナレッジ）</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="data in lstData" :key="data.user_cd">
                <td>
                  <div class="application-user">
                    <p class="application-name">{{ data.user_name }}</p>
                    <p class="application-code">ユーザコード：{{ data.user_cd }}</p>
                    <ul>
                      <li v-if="data.mail_address" class="w-100">
                        <span class="application-item">
                          <img class="svg" src="@/assets/template/images/icon_mail01.svg" alt="icon" />
                        </span>
                        <p class="application-label">{{ data.mail_address }}</p>
                      </li>
                    </ul>
                    <ul v-for="item in data.advance_reservation" :key="item.main_flag">
                      <li class="w-80">
                        <span class="application-item">
                          <img class="svg" src="@/assets/template/images/icon_building.svg" alt="icon" />
                        </span>
                        <p class="application-label">
                          <span class="application-bold"> {{ item.main_flag ? '(' + item.main_flag + ')' : '' }}</span>
                          {{ item.org_label || '(所属なし)' }}
                        </p>
                      </li>
                      <li v-if="item.definition_label" class="w-20">
                        <span class="application-item">
                          <img class="svg" src="@/assets/template/images/icon_namecard.svg" alt="icon" />
                        </span>
                        <p class="application-label">{{ item.definition_label }}</p>
                      </li>
                    </ul>
                  </div>
                </td>

                <!-- col 1 ユーザ情報 -->
                <td v-if="showUserInfo" ref="scrollUserInfo">
                  <div v-for="info in data.user_information" :key="info.mail_address" class="application-user">
                    <p class="application-name">{{ info.user_name }}</p>
                    <ul>
                      <li v-if="info.mail_address" class="w-100">
                        <span class="application-item">
                          <img class="svg" src="@/assets/template/images/icon_mail01.svg" alt="icon" />
                        </span>
                        <p class="application-label">{{ info.mail_address }}</p>
                      </li>
                      <li v-if="info.start_date" class="w-100 mt-3">
                        <p class="application-label">
                          {{ formatFullDate(info.start_date) }}
                          <span>～</span>
                          {{ overDate(info.end_date) ? '' : formatFullDate(info.end_date) }}
                        </p>
                      </li>
                    </ul>
                  </div>
                </td>

                <!-- col 2 組織情報 -->
                <td v-if="showOrgIntelligence" ref="scrollOrgIntelligence">
                  <div v-for="item in data.organization_information" :key="item" class="application-user">
                    <ul>
                      <div v-for="org in item.organization" :key="org.org_cd">
                        <ul class="mb-0">
                          <li class="w-80">
                            <span class="application-item">
                              <img class="svg" src="@/assets/template/images/icon_building.svg" alt="icon" />
                            </span>
                            <p class="application-label">
                              <span class="application-bold">{{ org.main_flag ? '(' + org.main_flag + ')' : '' }}</span>
                              {{ org.org_label || '(所属なし)' }}
                            </p>
                          </li>
                          <li v-if="org.definition_label" class="w-20">
                            <span class="application-item">
                              <img class="svg" src="@/assets/template/images/icon_namecard.svg" alt="icon" />
                            </span>
                            <p class="application-label">{{ org.definition_label }}</p>
                          </li>
                        </ul>
                      </div>

                      <li v-if="item.start_date" class="w-100 mt-3">
                        <!-- <span class="application-item">
                          <img class="svg" src="@/assets/template/images/icon_calendar02.svg" alt="icon" />
                        </span> -->
                        <p class="application-label">
                          {{ formatFullDate(item.start_date) }}
                          <span>～</span>
                          {{ overDate(item.end_date) ? '' : formatFullDate(item.end_date) }}
                        </p>
                      </li>
                    </ul>
                  </div>
                </td>

                <!--Col 3 権限情報 -->
                <td v-if="showAuthorityInfo" ref="scrollAuthorityInfo">
                  <div v-for="item in data.role_information" :key="item" class="application-user">
                    <ul>
                      <li v-for="role in item.roles" :key="role.org_cd" class="w-100">
                        <p class="application-label">
                          {{ role.role_name }}
                        </p>
                      </li>
                      <li v-if="item.start_date" class="w-100 mt-3">
                        <p class="application-label">
                          {{ formatFullDate(item.start_date) }}
                          <span>～</span>
                          {{ overDate(item.end_date) ? '' : formatFullDate(item.end_date) }}
                        </p>
                      </li>
                    </ul>
                  </div>
                </td>

                <!-- Col 4 承認（報告） -->
                <td v-if="showAcknowledgeReport" ref="scrollAcknowledgeReport">
                  <div v-for="(item, index) in data.approval_daily_report" :key="item">
                    <div v-if="index === 0 || (index > 0 && moreAcknowledgeReport)" :class="index === 0 ? 'bd-top-none' : ''" class="application-tbl-dateTime">
                      {{ formatFullDate(item.start_date) }}
                      <span>～</span>
                      {{ overDate(item.end_date) ? '' : formatFullDate(item.end_date) }}
                    </div>
                    <div v-if="index === 0 || (index > 0 && moreAcknowledgeReport)" class="carouselWrap_period scrollbar">
                      <ul>
                        <li v-for="layer in item.approval_layer_num" :key="layer.approval_layer_num">
                          <div class="carouselHead">{{ layer.approval_layer_num }}次</div>
                          <div class="carouselContent">
                            <ul class="carouselContent-lst">
                              <li v-for="(user, subIndex) in layer.approval_user" :key="user.approval_user_cd">
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreAcknowledgeReport)" class="carouselContent-name">
                                  {{ user.user_name }} ({{ user.definition_label }})
                                </p>
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreAcknowledgeReport)" class="carouselContent-add">
                                  <span class="carouselContent-item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="icon" /> </span
                                  >{{ user.org_label || '(所属なし)' }}
                                </p>
                              </li>
                            </ul>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div v-if="checkMoreRecording(data?.approval_daily_report) && !moreAcknowledgeReport" class="carouselMore">
                    <span class="link" @click="moreAcknowledgeReport = true" @touchstart="moreAcknowledgeReport = true">
                      もっと見る ({{ countMoreRecording(data?.approval_daily_report) }})
                    </span>
                  </div>
                  <div v-if="checkMoreRecording(data?.approval_daily_report) && moreAcknowledgeReport" class="carouselMore">
                    <span class="active link" @click="moreAcknowledgeReport = false" @touchstart="moreAcknowledgeReport = false"> 元に戻す </span>
                  </div>
                </td>

                <!-- Col 5 承認(説明会) -->
                <td v-if="showApprovalBriefingSession" ref="scrollApprovalBriefingSession">
                  <div v-for="(item, index) in data.approval_briefing" :key="item">
                    <div
                      v-if="index === 0 || (index > 0 && moreApprovalBriefingSession)"
                      :class="index === 0 ? 'bd-top-none' : ''"
                      class="application-tbl-dateTime"
                    >
                      {{ formatFullDate(item.start_date) }}
                      <span>～</span>
                      {{ overDate(item.end_date) ? '' : formatFullDate(item.end_date) }}
                    </div>
                    <div v-if="index === 0 || (index > 0 && moreApprovalBriefingSession)" class="carouselWrap_period scrollbar">
                      <ul>
                        <li v-for="layer in item.approval_layer_num" :key="layer.approval_layer_num">
                          <div class="carouselHead">{{ layer.approval_layer_num }}次</div>
                          <div class="carouselContent">
                            <ul class="carouselContent-lst">
                              <li v-for="(user, subIndex) in layer.approval_user" :key="user.approval_user_cd">
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreApprovalBriefingSession)" class="carouselContent-name">
                                  {{ user.user_name }} ({{ user.definition_label }})
                                </p>
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreApprovalBriefingSession)" class="carouselContent-add">
                                  <span class="carouselContent-item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="icon" /> </span
                                  >{{ user.org_label || '(所属なし)' }}
                                </p>
                              </li>
                            </ul>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div v-if="checkMoreRecording(data?.approval_briefing) && !moreApprovalBriefingSession" class="carouselMore">
                    <span class="link" @click="moreApprovalBriefingSession = true" @touchstart="moreApprovalBriefingSession = true">
                      もっと見る ({{ countMoreRecording(data?.approval_briefing) }})
                    </span>
                  </div>
                  <div v-if="checkMoreRecording(data?.approval_briefing) && moreApprovalBriefingSession" class="carouselMore">
                    <span class="active link" @click="moreApprovalBriefingSession = false" @touchstart="moreApprovalBriefingSession = false"> 元に戻す </span>
                  </div>
                </td>

                <!-- Col 6 承認（講演会） -->
                <td v-if="showAcknowledgmentLecture" ref="scrollAcknowledgmentLecture">
                  <div v-for="(item, index) in data.approval_lecture" :key="item" class="boxLecture">
                    <div
                      v-if="index === 0 || (index > 0 && moreAcknowledgmentLecture)"
                      :class="index === 0 ? 'bd-top-none' : ''"
                      class="application-tbl-dateTime"
                    >
                      {{ formatFullDate(item.start_date) }}
                      <span>～</span>
                      {{ overDate(item.end_date) ? '' : formatFullDate(item.end_date) }}
                    </div>
                    <div v-if="index === 0 || (index > 0 && moreAcknowledgmentLecture)" class="carouselWrap_period scrollbar">
                      <ul>
                        <li v-for="layer in item.approval_layer_num" :key="layer.approval_layer_num">
                          <div class="carouselHead">{{ layer.approval_layer_num }}次</div>
                          <div class="carouselContent">
                            <ul class="carouselContent-lst">
                              <li v-for="(user, subIndex) in layer.approval_user" :key="user.approval_user_cd">
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreAcknowledgmentLecture)" class="carouselContent-name">
                                  {{ user.user_name }} ({{ user.definition_label }})
                                </p>
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreAcknowledgmentLecture)" class="carouselContent-add">
                                  <span class="carouselContent-item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="icon" /> </span
                                  >{{ user.org_label || '(所属なし)' }}
                                </p>
                              </li>
                            </ul>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div v-if="checkMoreRecording(data?.approval_lecture) && !moreAcknowledgmentLecture" class="carouselMore">
                    <span class="link" @click="moreAcknowledgmentLecture = true" @touchstart="moreAcknowledgmentLecture = true">
                      もっと見る ({{ countMoreRecording(data?.approval_lecture) }})
                    </span>
                  </div>

                  <div v-if="checkMoreRecording(data?.approval_lecture) && moreAcknowledgmentLecture" class="carouselMore">
                    <span class="active link" @click="moreAcknowledgmentLecture = false" @touchstart="moreAcknowledgmentLecture = false"> 元に戻す </span>
                  </div>
                </td>

                <!-- Col 7 承認（ナレッジ） -->
                <td v-if="showApprovalknowledge" ref="scrollApprovalknowledge">
                  <div v-for="(item, index) in data.approval_knowledge" :key="item.user_cd">
                    <div v-if="index === 0 || (index > 0 && moreApprovalknowledge)" :class="index === 0 ? 'bd-top-none' : ''" class="application-tbl-dateTime">
                      {{ formatFullDate(item.start_date) }}
                      <span>～</span>
                      {{ overDate(item.end_date) ? '' : formatFullDate(item.end_date) }}
                    </div>
                    <div v-if="index === 0 || (index > 0 && moreApprovalknowledge)" class="carouselWrap_period scrollbar">
                      <ul>
                        <li v-for="layer in item.approval_layer_num" :key="layer.approval_layer_num">
                          <div class="carouselHead">{{ layer.approval_layer_num }}次</div>
                          <div class="carouselContent">
                            <ul class="carouselContent-lst">
                              <li v-for="(user, subIndex) in layer.approval_user" :key="user.approval_user_cd">
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreApprovalknowledge)" class="carouselContent-name">
                                  {{ user.user_name }} ({{ user.definition_label }})
                                </p>
                                <p v-show="subIndex < 3 || (subIndex >= 3 && moreApprovalknowledge)" class="carouselContent-add">
                                  <span class="carouselContent-item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="icon" /> </span
                                  >{{ user.org_label || '(所属なし)' }}
                                </p>
                              </li>
                            </ul>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div v-if="checkMoreRecording(data?.approval_knowledge) && !moreApprovalknowledge" class="carouselMore">
                    <span class="link" @click="moreApprovalknowledge = true" @touchstart="moreApprovalknowledge = true">
                      もっと見る ({{ countMoreRecording(data?.approval_knowledge) }})
                    </span>
                  </div>
                  <div v-if="checkMoreRecording(data?.approval_knowledge) && moreApprovalknowledge" class="carouselMore">
                    <span class="active link" @click="moreApprovalknowledge = false" @touchstart="moreApprovalknowledge = false"> 元に戻す </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <EmptyData v-if="lstData.length === 0 && !isLoadDefault" />
        </div>
      </div>
    </div>
    <div v-if="lstData.length > 0" class="pagination pagination-custom">
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModalZ05S01"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
// import _ from 'lodash';
import { markRaw } from 'vue';
import Pagination from '../../../components/Pagination.vue';
import I01_S03_ApplicationPeriodConfirmationService from '@/api/I01/I01_S03_ApplicationPeriodConfirmationService';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'I01_S03_ApplicationPeriodConfirmation',
  components: { Pagination },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      loadingPage: false,
      // isShowPopupFilter: false,

      showScrollLeft: false,
      showScrollRight: true,

      //
      showUserInfo: true,
      showOrgIntelligence: true,
      showAuthorityInfo: true,
      showAcknowledgeReport: true,
      showApprovalBriefingSession: true,
      showAcknowledgmentLecture: true,
      showApprovalknowledge: true,

      moreAcknowledgeReport: false,
      moreApprovalBriefingSession: false,
      moreAcknowledgmentLecture: false,
      moreApprovalknowledge: false,

      //
      formData: {
        date: this.formatFullDate(new Date()),
        user_cd: '',
        user_name: '',
        org_cd: [],
        orgList: [],
        limit: 100,
        offset: 0
      },

      paramFilterOld: {},
      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
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

      paramsZ05S01: {},

      lstData: [],
      isScrolling: false,
      isLoadDefault: true
    };
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'ユーザ設定照会',
      icon: 'icon-i01.svg',
      isShowBack: false
    });
    this.onFilterData(true);
    this.paramsZ05S01 = {
      userFlag: 0,
      mode: 'multiple',
      userSelectFlag: 1,
      orgCdList: []
    };
  },

  methods: {
    overDate(date) {
      return date === '9999/12/31' || date === '9999-12-31' ? true : false;
    },
    onFilterData(isLoadDefault) {
      this.formData = {
        ...this.formData,
        offset: 0
      };

      this.pagination = {
        ...this.pagination,
        curentPage: 1,
        totalItems: 0
      };

      this.getDataRecommendPeriod(isLoadDefault);
    },

    getDataRecommendPeriod(isLoadDefault) {
      this.loadingPage = true;
      this.isLoadDefault = isLoadDefault;
      this.isShowPopupFilter = false;
      let params = {
        ...this.formData,
        org_cd: this.formData.orgList?.map((x) => x.org_cd),
        offset: this.pagination.curentPage - 1,
        orgList: null
      };
      this.paramFilterOld = params;
      I01_S03_ApplicationPeriodConfirmationService.getDataRecommendPeriod(params)
        .then((res) => {
          this.lstData = [];
          this.isShowPopupFilter = false;
          this.lstData = [...res.data?.data?.records];
          let pageined = { ...res.data?.data?.pagination };
          this.pagination = {
            ...this.pagination,
            totalItems: pageined.total_items
          };

          this.moreAcknowledgeReport = false;
          this.moreApprovalBriefingSession = false;
          this.moreAcknowledgmentLecture = false;
          this.moreApprovalknowledge = false;
        })
        .catch((err) => {
          this.$notify({ message: err.response?.data?.message, customClass: 'error' });
          this.lstData = [];
          if (err.response?.data?.message === '指定された画面に対するアクセス権がありません。') {
            this.$router.push('/home');
          }
        })
        .finally(() => {
          this.loadingPage = false;
          this.isLoadDefault = false;

          let content = document.querySelector('.application-tbl');
          if (content) {
            content.scrollTop = 1;
            content.scrollLeft = 1;
          }
          this.js_pscroll(0.5);
        });
    },

    handleCurrentChange(val) {
      this.onCloseFilter();
      this.pagination.curentPage = val;
      this.getDataRecommendPeriod();
    },

    // modal filter
    openModalFilter() {
      this.isShowPopupFilter = true;
    },

    onCloseFilter() {
      this.isShowPopupFilter = false;
    },

    // check form
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.paramsZ05S01 },
        width: '45rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (e) {
        this.paramsZ05S01.orgCdList = e.orgSelected.map((x) => x.org_cd);

        this.formData.orgList = e.orgSelected;
      }

      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    handleRemoveFacility(item) {
      this.formData.orgList = this.formData.orgList.filter((x) => x.org_cd !== item.org_cd);

      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        orgCdList: this.formData.orgList.map((x) => x.org_cd)
      };
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
        let content = document.querySelector('.application-tbl');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },

    onScrollLeft() {
      let content = document.querySelector('.application-tbl');
      content.scrollLeft -= 200;
    },

    onScrollRight() {
      let content = document.querySelector('.application-tbl');
      content.scrollLeft += 200;
    },

    onScrollFixed(value) {
      let content = document.querySelector('.application-tbl');
      if (value) {
        // content.scrollLeft += 450;
      } else {
        // content.scrollLeft -= 450;
      }
      // content.scrollLeft += 1;
      content.scrollLeft = 0;
      this.isScrolling = true;

      this.onScroll({ target: { scrollLeft: content.scrollLeft, clientWidth: content.clientWidth, scrollWidth: content.scrollWidth } });
    },

    scrollToView(refName) {
      this.$refs[refName]?.scrollIntoView({ block: 'end', scrollBehavior: 'smooth' });
      this.$refs.scrollUserInfo.scrollIntoView({ block: 'end', scrollBehavior: 'smooth' });
    },

    checkMoreRecording(lstDate) {
      if (lstDate && lstDate.length > 0) {
        let lstLayer = lstDate[0].approval_layer_num;
        for (let i = 0; i < lstLayer.length; i++) {
          const el = lstLayer[i];
          if (el.approval_user.length > 3) {
            return true;
          }
        }
      }
      return false;
    },

    countMoreRecording(lstDate) {
      let num = 0;
      if (lstDate && lstDate.length > 0) {
        let lstLayer = lstDate[0].approval_layer_num;
        for (let i = 0; i < lstLayer.length; i++) {
          const el = lstLayer[i];
          if (el.approval_user.length > 3) {
            if (el.approval_user.length - 3 > num) {
              num = el.approval_user.length - 3;
            }
          }
        }
      }
      return num;
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.wrapBtn--change {
  .referenceDate {
    width: calc(100% - 42px);
    padding-right: 20px;
  }
  .btnInfo {
    width: 42px;
  }
}
.referenceDate {
  display: flex;
  flex-wrap: wrap;
  padding: 16px 0 8px 0;
  .referenceDate-control {
    width: 215px;
    display: flex;
    .referenceDate-label {
      font-size: 18px;
      font-weight: 500;
      width: 80px;
      line-height: 40px;
      text-align: center;
    }
    .referenceDate-form {
      width: calc(100% - 80px);
    }
  }
  .referenceDate-lst {
    width: calc(100% - 215px);
    padding-left: 24px;
    @media (max-width: $viewport_desktop) {
      padding-left: 12px;
    }
    @media (max-width: $viewport_tablet) {
      width: 100%;
      padding-left: 81px;
    }
    ul {
      display: flex;
      flex-wrap: wrap;
      li {
        padding-right: 20px;
        margin-top: 4px;
        @media (max-width: $viewport_desktop) {
          padding-right: 12px;
        }
        &:last-child {
          padding-right: 0;
        }
      }
    }
  }
}
.application {
  position: relative;
  padding: 0 24px 8px;
  max-height: 100%;
  overflow: hidden;
  .application-btn {
    .btn {
      position: absolute;
      top: calc(50% - 23px);
      height: 46px;
      width: 52px;
      padding: 0;
      background: rgba(63, 75, 86, 0.4);
      z-index: 7;
      &.btn--prev {
        left: 324px;
        border-radius: 0px 60px 60px 0px;
      }
      &.btn--next {
        right: 24px;
        border-radius: 60px 0px 0px 60px;
        z-index: 7;
        .svg {
          transform: rotate(-180deg);
        }
      }
    }
  }

  .application-tbl {
    tr {
      th,
      td {
        min-width: 450px;
        max-width: 450px;
        &:nth-child(1) {
          position: -webkit-sticky;
          position: sticky;
          left: 0;
          z-index: 1;
          border-right-color: #ffff;
          width: 300px;
        }
        &:nth-child(1),
        &:nth-child(2),
        &:nth-child(3),
        &:nth-child(4) {
          min-width: 300px;
        }
      }
      th {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 2;
        color: #fff;
        padding-top: 7px;
        padding-bottom: 7px;
        border-bottom: none;
        vertical-align: middle;
        font-size: 0.875rem;
        white-space: nowrap;
        &.table-border-head {
          &::after {
            position: absolute;
            content: '';
            width: 1px;
            background-color: #fff;
            height: 28px;
            top: 5px;
            right: 0;
            z-index: 4;
          }
        }
        &:first-of-type {
          border-radius: 10px 0 0 0;
          z-index: 7;
          &::before {
            position: absolute;
            top: 0;
            left: calc(100% - 8px);
            content: '';
            width: 20px;
            height: 100%;
            background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
            background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
            background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
            z-index: 4;
          }
        }
        &:last-child {
          border-radius: 0 10px 0 0;
          border-right: none;
          &.table-border-head {
            &::after {
              content: unset;
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
          background: #fff;
          padding: 0;
          position: relative;
          z-index: 0;
          &:first-of-type {
            .application-user {
              position: relative;
              z-index: 1;
            }
            &::after {
              position: absolute;
              top: 0;
              right: 0;
              content: '';
              width: 100%;
              height: 100%;
              background: #fff;
              box-shadow: 2px 1px 9px 0px #dddddd;
            }
            &::before {
              position: absolute;
              top: 3px;
              left: 100%;
              content: '';
              width: 10px;
              height: calc(100% - 6px);
              background: #fff;
              z-index: -1;
            }
          }

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
      }
    }
  }

  .table-hg100 {
    overflow: hidden;
  }

  .application-tbl-none {
    tr {
      th,
      td {
        min-width: unset;
        max-width: unset;
        &:nth-child(1),
        &:nth-child(2),
        &:nth-child(3),
        &:nth-child(4) {
          min-width: unset;
          max-width: unset;
        }
      }
    }

    tr {
      td {
        &:first-of-type {
          &::after,
          &::before {
            display: none;
          }
        }
      }
    }
  }

  .application-user {
    padding: 10px 16px;
    .application-name,
    .application-bold {
      font-weight: 700;
    }
    ul {
      display: flex;
      flex-wrap: wrap;
      li {
        margin-right: 12px;
        margin-top: 4px;
        display: flex;
        flex-grow: 1;
        &.w-80 {
          width: 172px;
          min-width: 172px;
        }
        .application-item {
          min-width: 14px;
          margin-top: -1px;
          margin-right: 6px;
          img {
            width: 14px;
          }
        }
      }
    }
  }
  .application-tbl-dateTime {
    padding: 6px 16px;
    border-bottom: 1px solid #e3e3e3;
    border-top: 1px solid #e3e3e3;
    margin-top: 15px;
    height: 40px;
  }
  .bd-top-none {
    border-top: none;
    margin-top: unset;
  }

  .carouselWrap_period {
    padding: 10px 20px;
    overflow: hidden;
    margin-bottom: 42px;
    height: 100%;
    > ul {
      display: flex;
      flex-wrap: nowrap;
      > li {
        flex: 1 0 50%;
        border-right: 1px solid #e3e3e3;
        &:last-child {
          border-right: none;
        }
      }
    }
    .carouselHead {
      border-bottom: 1px solid #e3e3e3;
      padding: 0 24px 12px;
      text-align: center;
    }
    .carouselContent {
      padding: 0 24px;
    }
    .carouselContent-lst {
      margin-top: -8px;
      li {
        margin-top: 16px;
      }
      .carouselContent-name {
        font-weight: 700;
      }
      .carouselContent-add {
        display: flex;
        .carouselContent-item {
          min-width: 16px;
          margin-right: 5px;
          margin-top: -2px;
        }
      }
    }
  }
}

.carouselMore {
  text-align: center;
  padding: 8px;
  box-shadow: 0px -1px 4px 0px #f1f1f1e6;
  position: absolute;
  bottom: 2px;
  background: white;
  width: 100%;
  height: 42px;
  span {
    position: relative;
    padding-right: 20px;
    &.active {
      &::after {
        transform: rotate(180deg);
      }
    }
    &::after {
      position: absolute;
      top: 7px;
      right: 0;
      content: '';
      display: block;
      width: 13px;
      height: 7px;
      transition: 400ms all;
      background: url(~@/assets/template/images/icon_arrow-line-gray.svg) no-repeat;
    }
  }
}

.pagination-custom {
  right: 0;
  width: unset;
  left: unset;
}

.dropdown-block {
  box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
  right: 0;
  position: absolute;
  z-index: 8;
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

.item-filter {
  cursor: pointer;
}

.btn-filter:disabled {
  cursor: not-allowed;
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
ul ul {
  margin-bottom: 10px;
}

td {
  position: relative;
  .groupLecture {
    position: absolute;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    .carouselMore {
      flex: 0 0 40px;
    }
  }

  .boxLecture {
    position: relative;
    height: 100%;
    width: 100%;
    padding: 0 5px 5px;

    .carouselWrap_period {
      height: calc(100% - 66px);
    }
  }
}
</style>
