<template>
  <div v-loading="loadingCalendar" v-load-calendar="true" v-load-f5="true" class="wrapContent">
    <div class="wrapCalendar">
      <div class="calendarContent scrollbar">
        <div class="colTabs">
          <ul class="nav">
            <li @click="showModalFilter(true)">
              <a>
                <span class="visible-filter" :class="showFilter ? 'hide' : 'show'">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line.svg')" alt="" />
                </span>
              </a>
            </li>
            <li>
              <a
                :class="showFilter ? (tabNameActive === 'visit' ? 'active' : 'noactive') : 'noactive'"
                data-toggle="tab"
                href="#tabs-1"
                role="tab"
                @click="setTabType('visit', true)"
                @touchstart="setTabType('visit', true)"
              >
                <span>訪問先</span>
              </a>
            </li>
            <li>
              <a
                :class="showFilter ? (tabNameActive === 'interview' ? 'active' : 'noactive') : 'noactive'"
                data-toggle="tab"
                href="#tabs-2"
                role="tab"
                @click="setTabType('interview', true)"
                @touchstart="setTabType('interview', true)"
              >
                <span>面談以外</span>
              </a>
            </li>
            <li>
              <a
                :class="showFilter ? (tabNameActive === 'stock' ? 'active' : 'noactive') : 'noactive'"
                data-toggle="tab"
                href="#tabs-3"
                role="tab"
                @click="setTabType('stock', true)"
                @touchstart="setTabType('stock', true)"
              >
                <span>ストック</span>
              </a>
            </li>
            <li>
              <a
                :class="showFilter ? (tabNameActive === 'selectGroup' ? 'active' : 'noactive') : 'noactive'"
                data-toggle="tab"
                href="#tabs-4"
                role="tab"
                @click="setTabType('selectGroup', true)"
                @touchstart="setTabType('selectGroup', true)"
              >
                <span>セレクト</span>
              </a>
            </li>
            <li>
              <a
                :class="showFilter ? (tabNameActive === 'usage' ? 'active' : 'noactive') : 'noactive'"
                data-toggle="tab"
                href="#tabs-5"
                role="tab"
                @click="setTabType('usage', true)"
                @touchstart="setTabType('usage', true)"
              >
                <span>凡例</span>
              </a>
            </li>
          </ul>
          <!-- Tab panes -->
        </div>
        <div class="colPanel">
          <div class="panelRow">
            <div class="colInfo" :hidden="!showFilter">
              <div class="tab-content">
                <div id="tabs-1" :class="`${tabNameActive === 'visit' && showFilter ? 'active tab-pane' : 'noactive'}  `" role="tabpanel">
                  <div class="accordion-title" @click="showFormFilterVisit()">
                    <span :class="showFilterVisit ? '' : 'conditions--active'" class="conditions"> 検索条件 </span>
                  </div>

                  <div class="formVisit">
                    <ul>
                      <li>
                        <label class="formVisit-label">担当者</label>
                        <div class="formVisit-control">
                          <div class="form-icon icon-right">
                            <span class="icon icon--cursor log-icon" title_log="担当者" @click="openModalZ05S01()">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                            </span>
                            <div v-if="lstUser.length > 0" class="form-control d-flex align-items-center">
                              <div class="block-tags">
                                <el-tag
                                  v-for="user in lstUser"
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
                        </div>
                      </li>
                      <li>
                        <label class="formVisit-label">個人名</label>
                        <div class="formVisit-control">
                          <el-input v-model="formFilter.person_name" clearable placeholder="名前入力" class="form-control-input" />
                        </div>
                      </li>
                      <li>
                        <label class="formVisit-label">施設名</label>
                        <div class="formVisit-control">
                          <el-input v-model="formFilter.facility_name" clearable placeholder="施設名入力" class="form-control-input" />
                        </div>
                      </li>
                      <li>
                        <label class="formVisit-label">セグメント</label>
                        <div class="formVisit-control">
                          <el-select v-model="formFilter.segment_type" placeholder="未選択" class="form-control-select">
                            <el-option :value="''">未選択</el-option>
                            <el-option v-for="item in lstSegment" :key="item.segment_type" :label="item.segment_name" :value="item.segment_type"> </el-option>
                          </el-select>
                        </div>
                      </li>
                      <li>
                        <label class="formVisit-label">ターゲット品目</label>
                        <div class="formVisit-control">
                          <el-select v-model="formFilter.target_product_cd" placeholder="未選択" class="form-control-select">
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
                    </ul>
                    <div class="formVisit-btn">
                      <button
                        type="button"
                        class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                        @click="getDataVisit()"
                        @touchstart="getDataVisit()"
                      >
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_search.svg')" alt="" />
                        </span>
                        検索
                      </button>
                    </div>
                  </div>

                  <div ref="lstVisitScroll" class="lstVisit lstVisit-scroll scrollbar" :class="`${loadingVisit ? 'pre-loader' : ''}`">
                    <Preloader v-show="loadingVisit" />
                    <ul v-show="!loadingVisit">
                      <div id="eventVisit">
                        <div v-if="lstVisit.length > 0">
                          <li v-for="personVisit in lstVisit" :key="personVisit" class="fc-event-visit" :data-event="JSON.stringify(personVisit)">
                            <span class="item-hand"><img src="@/assets/template/images/icon_hand.svg" alt="" /></span>
                            <div class="lstVisit-content">
                              <p class="lstVisit-text">{{ personVisit.facility_short_name }}&ensp;{{ personVisit.department_name }}</p>
                              <p class="lstVisit-text lstVisit-text-label">
                                <span class="text-bold">{{ personVisit.person_name }}</span>
                                <span class="text-nomal"> {{ personVisit.position_name }}</span>
                              </p>
                            </div>
                          </li>
                        </div>
                      </div>
                      <div v-if="lstVisit.length == 0 && !isLoadingDefault" class="noData pd-lr5">
                        <div class="noData-content">
                          <p class="noData-text">該当するデータがありません。</p>
                          <div class="noData-thumb mr-t40">
                            <img src="@/assets/template/images/data/amico.svg" alt="" />
                          </div>
                        </div>
                      </div>
                    </ul>
                  </div>

                  <div v-if="lstVisit.length > 0 && !loadingVisit && !showFilterVisit" class="pagin-institution">
                    <PaginationTable
                      class="pagination-bord"
                      :page-size="paginationVisit.pageSize"
                      :total="paginationVisit.totalItems"
                      :current-page="paginationVisit.curentPage"
                      :pager-counts="3"
                      @current-change="handleCurrentChangeVisit"
                    />
                  </div>
                </div>
                <div id="tabs-2" :class="`${tabNameActive === 'interview' && showFilter ? 'active tab-pane' : 'noactive'}  `" role="tabpanel">
                  <div class="otherInterview">
                    <ul>
                      <div id="otherInterview">
                        <div v-if="lstTypeSchedule.length > 0">
                          <li
                            v-for="type in lstTypeSchedule"
                            :key="type.schedule_type"
                            class="fc-event-interview log-icon"
                            :data-event="JSON.stringify(type)"
                            :title_log="type.display_option_name"
                          >
                            <span class="otherInterview-box" :style="{ background: type.background }"></span>
                            <div class="otherInterview-info">
                              <span class="item"><img src="@/assets/template/images/icon_hand.svg" alt="" /></span>
                              <span class="text">{{ type.display_option_name }}</span>
                            </div>
                          </li>
                        </div>
                        <div v-if="lstTypeSchedule.length == 0 && !loadingCalendar" class="noData pd-lr5">
                          <div class="noData-content">
                            <p class="noData-text">該当するデータがありません。</p>
                            <div class="noData-thumb mr-t40">
                              <img src="@/assets/template/images/data/amico.svg" alt="" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </ul>
                  </div>
                </div>
                <div
                  id="tabs-3"
                  :class="`setting-save-filter-item ${tabNameActive === 'stock' && showFilter ? 'active tab-pane' : 'noactive'}  `"
                  role="tabpanel"
                >
                  <div class="stock-select">
                    <div class="stockSelect">
                      <ul>
                        <li
                          class="log-icon save-filter-item"
                          title_log="未計画"
                          :class="selectStockPlaned === 1 ? 'active' : ''"
                          @click="getDataStock(1)"
                          @touchstart="getDataStock(1)"
                        >
                          未計画
                        </li>
                        <li
                          class="log-icon save-filter-item"
                          title_log="計画済"
                          :class="selectStockPlaned === 0 ? 'active' : ''"
                          @click="getDataStock(0)"
                          @touchstart="getDataStock(0)"
                        >
                          計画済
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div
                    ref="listStock"
                    class="lstVisit lstVisit-stock list-stock-scroll scrollbar"
                    :class="`${loadingStock ? 'pre-loader' : ''}`"
                    @scroll="scrollData('listStock_')"
                  >
                    <Preloader v-show="loadingStock" />
                    <ul v-show="!loadingStock">
                      <div id="selectStock">
                        <div v-if="lstPersonStock.length > 0">
                          <li
                            v-for="personStock in lstPersonStock"
                            :key="personStock.stock_id"
                            class="fc-event-stock"
                            :data-event="JSON.stringify(personStock)"
                          >
                            <span class="item-hand"><img src="@/assets/template/images/icon_hand.svg" alt="" /></span>
                            <div class="lstVisit-content">
                              <p class="lstVisit-text">{{ personStock.facility_short_name }}&ensp;{{ personStock.department_name }}</p>
                              <p class="lstVisit-text lstVisit-text-label">
                                <span class="text-bold">{{ personStock.person_name }}</span>
                                <span class="text-nomal"> {{ personStock.position_name }}</span>
                              </p>
                            </div>
                            <div class="followStock">
                              <div class="followStock-main">
                                <el-popover placement="right" :popper-class="'popover-custom'" :width="270" trigger="click">
                                  <template #reference>
                                    <button
                                      :ref="`listStock_${personStock.stock_id}`"
                                      class="btn btn--info"
                                      type="button"
                                      @click="selectInfoItem(personStock.stock_id)"
                                    ></button>
                                  </template>
                                  <div class="dropdown-menu--follow">
                                    <ul>
                                      <li class="follow-title">{{ personStock.content_name }}</li>
                                      <li class="follow-text">{{ personStock.product_name_list }}</li>
                                      <li v-for="document in personStock.document_list" :key="document.document_id" class="follow-item">
                                        <span class="item">
                                          <img
                                            v-if="document.document_type == '10' && document.file_type == '10'"
                                            v-svg-inline
                                            svg-inline
                                            class="svg"
                                            :src="require('@/assets/template/images/icon_pdf-manag.svg')"
                                            alt=""
                                          />
                                          <img
                                            v-if="document.document_type == '10' && document.file_type == '20'"
                                            v-svg-inline
                                            svg-inline
                                            class="svg"
                                            :src="require('@/assets/template/images/icon_film.svg')"
                                            alt=""
                                          />
                                          <img
                                            v-if="document.document_type == '20'"
                                            v-svg-inline
                                            svg-inline
                                            class="svg"
                                            :src="require('@/assets/images/icon_document_spanner_small.svg')"
                                            alt=""
                                          />
                                        </span>
                                        {{ document.document_name }}
                                      </li>
                                    </ul>
                                  </div>
                                </el-popover>
                              </div>
                            </div>
                          </li>
                        </div>
                        <div v-if="lstPersonStock.length == 0" class="noData pd-lr5">
                          <div class="noData-content">
                            <p class="noData-text">該当するデータがありません。</p>
                            <div class="noData-thumb mr-t40">
                              <img src="@/assets/template/images/data/amico.svg" alt="" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </ul>
                  </div>
                  <div v-if="lstPersonStock.length > 0 && !loadingStock" class="pagin-institution save-filter-item">
                    <PaginationTable
                      class="pagination-bord"
                      :page-size="paginationStock.pageSize"
                      :total="paginationStock.totalItems"
                      :current-page="paginationStock.curentPage"
                      :pager-counts="3"
                      @current-change="handleCurrentChangeStock"
                    />
                  </div>
                </div>
                <div
                  id="tabs-4"
                  :class="`setting-save-filter-item ${tabNameActive === 'selectGroup' && showFilter ? 'active tab-pane' : 'noactive'}  `"
                  role="tabpanel"
                >
                  <div class="group-select">
                    <div class="stockSelect">
                      <ul>
                        <li
                          class="log-icon save-filter-item"
                          title_log="施設"
                          :class="selectGroup === 'f' ? 'active' : ''"
                          @click="getFacilityOrPersonGroup('f')"
                          @touchstart="getFacilityOrPersonGroup('f')"
                        >
                          施設
                        </li>
                        <li
                          class="log-icon save-filter-item"
                          title_log="施設個人"
                          :class="selectGroup === 'p' ? 'active' : ''"
                          @click="getFacilityOrPersonGroup('p')"
                          @touchstart="getFacilityOrPersonGroup('p')"
                        >
                          施設個人
                        </li>
                      </ul>
                    </div>
                    <div class="stockSelect">
                      <el-select
                        v-if="selectGroup === 'f'"
                        v-model="formFilter4.select_facility_group_id"
                        placeholder="グループを選択"
                        title_log="セレクトグループ選択"
                        class="form-control-select log-icon save-filter-item"
                        @change="handleCurrentChangeGroup(1)"
                      >
                        <el-option
                          v-for="group in lstGroup"
                          :key="group.select_facility_group_id"
                          class="custom-select-visit"
                          :label="group.select_facility_group_name"
                          :value="group.select_facility_group_id"
                        >
                        </el-option>
                      </el-select>
                      <el-select
                        v-if="selectGroup === 'p'"
                        v-model="formFilter4.select_person_group_id"
                        placeholder="グループを選択"
                        title_log="セレクトグループ選択"
                        class="form-control-select log-icon save-filter-item"
                        @change="handleCurrentChangeGroup(1)"
                      >
                        <el-option
                          v-for="group in lstGroup"
                          :key="group.select_person_group_id"
                          class="custom-select-visit save-filter-item"
                          :label="group.select_person_group_name"
                          :value="group.select_person_group_id"
                        >
                        </el-option>
                      </el-select>
                    </div>
                  </div>

                  <div
                    ref="listGroupSelect"
                    :class="`lstVisit lstVisit-stock list-group-select-scroll scrollbar  ${loadingGroup ? 'pre-loader' : ''}`"
                    @scroll="selectGroup === 'f' ? '' : scrollData('groupPerson_')"
                  >
                    <Preloader v-show="loadingGroup" />
                    <ul v-show="!loadingGroup">
                      <div id="selectGroupFacility">
                        <div v-if="selectGroup === 'f'">
                          <div v-if="lstFacility.length > 0">
                            <li v-for="facility in lstFacility" :key="facility.facility_cd" class="fc-event-facility" :data-event="JSON.stringify(facility)">
                              <span class="item-hand"><img src="@/assets/template/images/icon_hand.svg" alt="" /></span>
                              <div class="lstVisit-content">
                                <p class="lstVisit-text">
                                  <span class="lstVisit-item">
                                    {{ facility.facility_short_name }}
                                  </span>
                                </p>
                              </div>
                            </li>
                          </div>
                          <div v-if="lstFacility.length == 0 && !loadingGroup" class="noData pd-lr5">
                            <div class="noData-content">
                              <p class="noData-text">該当するデータがありません。</p>
                              <div class="noData-thumb mr-t40">
                                <img src="@/assets/template/images/data/amico.svg" alt="" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </ul>
                    <ul v-show="!loadingGroup">
                      <div id="selectGroupPerson">
                        <div v-if="selectGroup === 'p'">
                          <div v-if="lstPerson.length > 0">
                            <li v-for="person in lstPerson" :key="person.person_cd" class="fc-event-person" :data-event="JSON.stringify(person)">
                              <span class="item-hand"><img src="@/assets/template/images/icon_hand.svg" alt="" /></span>
                              <div class="lstVisit-content">
                                <p class="lstVisit-text">{{ person.facility_short_name }}&ensp;{{ person.department_name }}</p>
                                <p class="lstVisit-text lstVisit-text-label">
                                  <span class="text-bold">{{ person.person_name }}</span>
                                  <span class="text-nomal"> {{ person.position_name }}</span>
                                </p>
                              </div>
                              <div class="followStock">
                                <div class="followStock-main">
                                  <el-popover placement="right" :popper-class="'popover-custom'" :width="270" trigger="click">
                                    <template #reference>
                                      <button
                                        :ref="`groupPerson_${person.person_cd}`"
                                        type="button"
                                        class="btn btn--info"
                                        @click="selectInfoItem(person.person_cd)"
                                      ></button>
                                    </template>
                                    <div class="dropdown-menu--follow">
                                      <ul>
                                        <li class="follow-title">{{ person?.content_list ? person?.content_list[0]?.content_name : '' }}</li>
                                        <li class="follow-text">{{ person?.product_name_list }}</li>
                                        <li v-for="document in person.document_list" :key="document.document_id" class="follow-item">
                                          <span class="item">
                                            <img
                                              v-if="document.document_type == '10' && document.file_type == '10'"
                                              v-svg-inline
                                              svg-inline
                                              class="svg"
                                              :src="require('@/assets/template/images/icon_pdf-manag.svg')"
                                              alt=""
                                            />
                                            <img
                                              v-if="document.document_type == '10' && document.file_type == '20'"
                                              v-svg-inline
                                              svg-inline
                                              class="svg"
                                              :src="require('@/assets/template/images/icon_film.svg')"
                                              alt=""
                                            />
                                            <img
                                              v-if="document.document_type == '20'"
                                              v-svg-inline
                                              svg-inline
                                              class="svg"
                                              :src="require('@/assets/images/icon_document_spanner_small.svg')"
                                              alt=""
                                            />
                                          </span>
                                          {{ document.document_name }}
                                        </li>
                                      </ul>
                                    </div>
                                  </el-popover>
                                </div>
                              </div>
                            </li>
                          </div>
                          <div v-if="lstPerson.length == 0 && !loadingGroup" class="noData pd-lr5">
                            <div class="noData-content">
                              <p class="noData-text">該当するデータがありません。</p>
                              <div class="noData-thumb mr-t40">
                                <img src="@/assets/template/images/data/amico.svg" alt="" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </ul>
                  </div>

                  <div v-if="(lstFacility.length > 0 || lstPerson.length > 0) && !loadingGroup" class="pagin-institution save-filter-item">
                    <PaginationTable
                      class="pagination-bord"
                      :page-size="paginationGroup.pageSize"
                      :total="paginationGroup.totalItems"
                      :current-page="paginationGroup.curentPage"
                      :pager-counts="3"
                      @current-change="handleCurrentChangeGroup"
                    />
                  </div>
                </div>
                <div id="tabs-5" :class="`${tabNameActive === 'usage' && showFilter ? 'active tab-pane' : 'noactive'}  `" role="tabpanel">
                  <div class="classification-title"><h2 class="tlt">分類</h2></div>
                  <div ref="lstUsage" class="lstUsage lstUsage-scroll">
                    <ul>
                      <li v-for="option in lstDisplayOption" :key="option.display_option_type">
                        <div class="lstUsage-box" :style="{ background: option.background_color, border: `1px solid ${option.frame_border_color}` }"></div>
                        <div class="lstUsage-text">{{ option.display_option_name }}</div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="colCalendar" :class="showFilter ? '' : 'custom-w'">
              <FullCalendar ref="fullCalendar" class="fullcalendar-custom calendar-custom-header" :options="calendarOptions">
                <template #eventContent="arg">
                  <b>{{ arg.timeText }}</b>
                  <br v-if="arg.timeText && arg.view.type !== 'dayGridMonth'" />
                  <i>{{ arg.event.title }}</i>
                </template>
              </FullCalendar>
            </div>
          </div>
        </div>
      </div>
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`${modalConfig.customClass} handle-close`"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
      @close="handleCloseModal()"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          ref="modalChild"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="reloadAction(onResultModal, 'reload')($event)"
          @handleYes="handleConfirmAddEvent"
        ></component>
      </template>
    </el-dialog>

    <el-dialog
      v-model="modalConfig.isShowModalConfirm"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :show-close="false"
      :close-on-click-modal="modalConfig.closeOnClickMark"
    >
      <div class="modal-confirm">
        <div class="confirmContent">
          <img :src="require(`@/assets/template/images/icon_alert.svg`)" alt="iconAlert" />
          <h3 class="title">表示モードが変更されるのでコーピをキャンセルしてもよろしいでしょうか？</h3>
        </div>

        <div class="confirmBtn">
          <button type="button" class="btn btn-outline-primary mr-2" :disabled="processingRedirectView" @click="cancelCopy()" @touchstart="cancelCopy()">
            いいえ
          </button>
          <el-button
            type="primary"
            class="btn btn-primary"
            :loading="processingRedirectView"
            :disabled="processingRedirectView"
            @click.prevent="rederectToView()"
          >
            はい
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
/* eslint-disable indent */
import moment from 'moment';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import A01_S01_ScheduleInputService from '@/api/A01/A01_S01_ScheduleInputService';
import Z04_S01_Services from '@/api/Z04/Z04_S01_AccountSettingServices';

import { markRaw } from 'vue';
import _ from 'lodash';
import B01_S02_BriefingSessionInput from '@/views/B01/B01_S02_BriefingSessionInput/B01_S02_BriefingSessionInput';
import C01_S02_LectureInput from '@/views/C01/C01_S02_LectureInput/C01_S02_LectureInput';
import Z05_S01_Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import A01_S03_InterviewDetails from '@/views/A01/A01_S03_InterviewDetails/A01_S03_InterviewDetails.vue';
import A01_S03_ModalInHouseSchedule from '@/views/A01/A01_S03_InterviewDetails/A01_S03_ModalInHouseSchedule.vue';
import A01_S03_ModalPrivate from '@/views/A01/A01_S03_InterviewDetails/A01_S03_ModalPrivate.vue';
import PerfectScrollbar from '@/assets/template/js/perfect-scrollbar.min.js';

let listEvent = [];
export default {
  name: 'A01_S01_ScheduleInput',
  components: {
    FullCalendar,
    Draggable,
    B01_S02_BriefingSessionInput,
    C01_S02_LectureInput,
    Z05_S01_Organization,
    A01_S03_InterviewDetails,
    A01_S03_ModalInHouseSchedule,
    A01_S03_ModalPrivate
  },
  props: {
    // redirect from copy A01-S03
    schedule_id: {
      type: String,
      require: false,
      default: ''
    },
    dateStart: {
      type: String,
      require: false,
      default: ''
    },
    checkLoading: [Boolean]
  },
  data: function () {
    return {
      loadingCalendar: true,
      showFilter: false,
      isOpenDefaultFilter: false,
      showFilterVisit: false,
      loadingVisit: true,
      loadingStock: true,
      loadingGroup: true,

      processingRedirectView: false,

      // calendar
      timeZone: 'Asia/Jakarta',
      calendarOptions: {
        locale: 'ja',

        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        headerToolbar: {
          left: 'today prev,next',
          center: 'title',
          right: 'buttonCopy buttonPaste dayGridMonth,timeGridWeek,timeGridDay'
        },
        customButtons: {
          buttonCopy: {
            text: 'コピー',
            click: this.copySchedule
          },
          buttonPaste: {
            text: '貼りつけ',
            click: this.pasteSchedule
          },

          dayGridMonth: {
            text: '月',
            click: this.getCalendarMonth
          },
          timeGridWeek: {
            text: '週',
            click: this.getCalendarWeek
          },
          timeGridDay: {
            text: '日',
            click: this.getCalendarDay
          }
        },

        views: {
          timeGridWeek: {
            titleFormat: (date) => {
              let start = date.start;
              let end = date.end;
              let result = start.year + '年' + (start.month + 1) + '月' + ' ～ ' + end.year + '年' + (end.month + 1) + '月';
              if (start.year == end.year && start.month == end.month) {
                result = start.year + '年' + (start.month + 1) + '月';
              }

              return result;
            }
          }
        },

        buttonText: {
          today: '今日',
          month: '月',
          week: '週',
          day: '日'
        },

        allDayContent: '終日',
        moreLinkContent: (arg) => {
          let moreText = '他' + arg.num + '件';
          return moreText;
        },

        dayHeaderContent: (day) => {
          if (day.view.type === 'timeGridWeek') {
            let index = day.text.indexOf('/');
            let dayText = day.text.substring(index + 1, day.text.length);
            return dayText;
          }

          if (day.view.type === 'timeGridDay') {
            let text = moment(day.date).format('D') + `(${this.getDay(day.date)})`;
            return text;
          }
        },

        dayCellContent: (day) => {
          if (day.view.type === 'dayGridMonth') {
            let dayCellText = day.dayNumberText.substring(0, day.dayNumberText.length - 1);
            return dayCellText;
          }
        },

        dayPopoverFormat: (day) => {
          let dateDisplay = moment(day.date).format('YYYY年M月D日');
          return dateDisplay;
        },

        slotLabelFormat: [{ hour: 'numeric', minute: '2-digit', hour12: false }],
        eventTimeFormat: {
          hour: 'numeric',
          minute: '2-digit',
          hour12: false,
          meridiem: 'narrow'
        },

        // event change date
        datesSet: (dateTime) => {
          let dateStart = '';
          let dateEnd = '';
          if (dateTime.view.type === 'timeGridDay') {
            dateStart = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
            dateEnd = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
            this.setHoverToolBar('日');
            if (!this.isReload) {
              if (this.startDate !== dateStart && this.endDate !== dateEnd) {
                this.startDate = dateStart;
                this.endDate = dateEnd;
                this.getScheduleByDate();
              }
            }
          }
          if (dateTime.view.type === 'timeGridWeek') {
            if (!this.isReload) {
              dateStart = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
              this.setHoverToolBar('週');
              this.getWeekByDate(dateStart);
            }
          }
          if (dateTime.view.type === 'dayGridMonth') {
            dateStart = moment(new Date(dateTime.start)).format('YYYY/MM/DD');
            dateEnd = moment(new Date(dateTime.end)).format('YYYY/MM/DD');
            this.setHoverToolBar('月');
            if (!this.isReload) {
              if (this.startDate !== dateStart && this.endDate !== dateEnd) {
                this.startDate = dateStart;
                this.endDate = dateEnd;
                this.getScheduleByDate();
              }
            }
            this.displayHiddenCustomButton();
          }
        },

        initialView: 'timeGridWeek',
        scrollTime: '08:30:00',

        longPressDelay: 300,
        eventLongPressDelay: 300,
        selectLongPressDelay: 1,
        selectMinDistance: 20,
        events: listEvent,
        selectable: false,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        firstDay: 1,
        editable: true,
        droppable: true,
        eventDurationEditable: true, // Enable Resize

        eventClick: this.handleEventClick,
        eventContent: function () {},
        eventReceive: this.handleEventDropOther,
        eventDrop: this.handleEventDrop,
        eventResize: this.handleEventResizeStop,
        handleWindowResize: true,
        windowResizeDelay: 200
      },

      currentEvents: [],

      eventData: {},
      // end calendar

      paramsZ05S01: {
        userFlag: 1,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
      },

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalConfirm: false,
        component: null,
        props: {},
        width: '',
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },

      userInfo: {},

      startDate: '',
      endDate: '',

      startDateCopy: '',
      endDateCopy: '',
      isCopy: false,

      modeView: 'timeGridWeek',
      newModeView: '',

      // tab 1: 検索条件
      lstSegment: [],
      lstTargetProduct: [],
      lstVisit: [],

      lstUser: [],
      lstUserOld: [],
      formFilter: {
        user_cd: [],
        person_name: '',
        facility_name: '',
        segment_type: '',
        target_product_cd: ''
      },
      paramFilterVisitOld: {},
      isSearch: false,

      // tab 2
      lstTypeSchedule: [],

      // tab 3: ストック
      selectStockPlaned: 1, // 1: Un planed , 0: Planed,
      lstPersonStock: [],

      // tab 4: セレクト
      selectGroup: 'f', // f: group facility , p: group person facility,
      lstGroup: [],
      lstFacility: [],
      lstPerson: [],

      formFilter4: {
        select_person_group_id: '',
        select_facility_group_id: ''
      },

      selectInfoId: '',

      // tab 5
      lstDisplayOption: [],

      eventDropPerson: '',
      eventDragTabType: '',
      evenDropConfirm: {},

      // prop
      prop_schedule_id: '',
      prop_start_date: '',
      prop_zoom: false,
      isReload: true,

      paginationVisit: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },
      paginationStock: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },
      paginationGroup: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },

      tabNameActive: 'visit'
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'スケジュール',
      icon: 'icon_interview-color.svg',
      isShowBack: false
    });

    let currentUser = this.getCurrentUser();

    this.lstUser.push({
      user_cd: currentUser.user_cd,
      user_name: currentUser.user_name
    });
    this.lstUserOld = [...this.lstUser];
    this.formFilter.user_cd.push(currentUser.user_cd);

    this.paramsZ05S01 = {
      ...this.paramsZ05S01,
      orgCdList: [currentUser.org_cd],
      userCdList: [{ org_cd: currentUser.org_cd, user_cd: currentUser.user_cd }]
    };

    let queryUrl = this._route('A01_S01_ScheduleInput')?.params;

    if (queryUrl?.scheduleId) {
      this.prop_schedule_id = queryUrl.scheduleId;
      this.prop_zoom = localStorage.getItem('zoomBox');
      setTimeout(() => {
        localStorage.removeItem('zoomBox');
      }, 1000);
    }

    // setting drag other to calendar
    this.setupDraggable('eventVisit', '.fc-event-visit');
    this.setupDraggable('otherInterview', '.fc-event-interview');
    this.setupDraggable('selectGroupFacility', '.fc-event-facility');
    this.setupDraggable('selectGroupPerson', '.fc-event-person');
    this.setupDraggable('selectStock', '.fc-event-stock');

    // add id, className button
    let classEl = ['btn', 'btn-outline-primary', 'btn-outline-primary--cancel'];
    let btnEl = ['Paste', 'Copy', 'next', 'prev', 'today'];

    for (let item of btnEl) {
      if (item === 'Paste' || item === 'Copy') {
        let classBtn = document.getElementsByClassName(`fc-button${item}-button`)[0];
        classBtn?.setAttribute('id', `button${item}_id`);
        if (item === 'Paste') {
          document.getElementById(`button${item}_id`).disabled = true;
        }
      } else {
        let classBtn = document.getElementsByClassName(`fc-${item}-button`)[0];
        classBtn?.setAttribute('id', `button${item}_id`);
      }

      let classBtnId = document.getElementById(`button${item}_id`);
      for (let el of classEl) {
        classBtnId?.classList.add(el);
      }
    }

    // check
    const isLoadingComponent = localStorage.getItem('isLoadingComponent');
    const start_date_active = localStorage.getItem('startActive');
    const end_date_active = localStorage.getItem('endActive');

    if (isLoadingComponent === 'true') {
      if (queryUrl?.dateStart) {
        this.isReload = true;
        this.prop_start_date = queryUrl.dateStart;
        let calendarApi = this.$refs.fullCalendar.getApi();
        calendarApi.gotoDate(this.prop_start_date);
        this.getScreenData(this.prop_start_date, true);
      } else {
        this.getScreenData(null, true);
      }
      localStorage.setItem('mdView', this.modeView);
    } else {
      this.modeView = localStorage.getItem('mdView') || 'timeGridWeek';

      if (start_date_active) {
        this.startDate = moment(new Date(start_date_active)).format('YYYY-MM-DD');
        this.endDate = moment(new Date(end_date_active)).format('YYYY-MM-DD');
        let date = moment(new Date(start_date_active)).add(15, 'day').format('YYYY-MM-DD');

        let calendarApi = this.$refs.fullCalendar.getApi();
        calendarApi.changeView(this.modeView);
        if (this.modeView === 'dayGridMonth') {
          calendarApi.gotoDate(date);
        } else {
          calendarApi.gotoDate(this.startDate);
        }
        this.getScreenData(this.startDate, true);
      } else {
        this.getScreenData(null, true);
      }
    }

    if (isLoadingComponent !== 'true') {
      this.tabNameActive = localStorage.getItem('tabNameActive');
    }

    this.showModalFilter(isLoadingComponent === 'true' ? true : false);

    this.resizeWindow();

    // Setup scroll for callendar
    let ele = document.getElementsByClassName('fc-scroller-liquid-absolute')[0];
    let ps = new PerfectScrollbar(ele, {
      useBothWheelAxes: false,
      suppressScrollX: true
    });
    window.addEventListener('resize', () => {
      ps.update();
    });
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },

    resizeWindow() {
      let calendarApi = this.$refs.fullCalendar.getApi();
      setTimeout(() => {
        calendarApi.updateSize();
      }, 1000);
    },

    displayHiddenCustomButton() {
      document.querySelectorAll('.fc-buttonCopy-button').forEach((el) => el.remove());
      document.querySelectorAll('.fc-buttonPaste-button').forEach((el) => el.remove());
      this.isCopy = false;
    },

    setTabType(value, isOpen) {
      this.eventDragTabType = value;
      this.tabNameActive = value;
      localStorage.setItem('tabNameActive', value);
      if (isOpen) {
        this.showFilter = true;
        this.isOpenDefaultFilter = false;
      }
      if (value === 'usage') {
        this.$refs.lstUsage.scrollTop = 0;
      }

      if (value === 'visit') {
        this.$refs.lstVisitScroll.scrollTop = 0;
      }
    },

    getCalendarMonth() {
      this.newModeView = 'dayGridMonth';
      if (this.isCopy) {
        this.confirmCancelCopy();
      } else {
        let calendarApi = this.$refs.fullCalendar.getApi();
        calendarApi.changeView('dayGridMonth');
        this.modeView = 'dayGridMonth';
        this.isCopy = false;
        localStorage.setItem('mdView', this.modeView);
      }
    },

    getCalendarWeek() {
      this.newModeView = 'timeGridWeek';
      if (this.isCopy) {
        this.confirmCancelCopy();
      } else {
        let calendarApi = this.$refs.fullCalendar.getApi();
        calendarApi.changeView('timeGridWeek');
        this.modeView = 'timeGridWeek';
        this.isCopy = false;
        localStorage.setItem('mdView', this.modeView);
      }
    },

    getCalendarDay() {
      this.newModeView = 'timeGridDay';
      if (this.isCopy) {
        this.confirmCancelCopy();
      } else {
        let calendarApi = this.$refs.fullCalendar.getApi();
        calendarApi.changeView('timeGridDay');
        this.modeView = 'timeGridDay';
        this.isCopy = false;
        localStorage.setItem('mdView', this.modeView);
      }
    },

    setHoverToolBar(alt) {
      document.getElementsByClassName('fc-dayGridMonth-button')[0].setAttribute('title_log', '月');
      document.getElementsByClassName('fc-timeGridWeek-button')[0].setAttribute('title_log', '週');
      document.getElementsByClassName('fc-timeGridDay-button')[0].setAttribute('title_log', '日');

      document.getElementsByClassName('fc-prev-button')[0].setAttribute('title', `前へ ${alt}`);
      document.getElementsByClassName('fc-next-button')[0].setAttribute('title', `次へ ${alt}`);
      document.getElementsByClassName('fc-today-button')[0].setAttribute('title', `今 ${alt}`);
      document.getElementsByClassName('fc-prev-button')[0].setAttribute('title_log', `前へ ${alt}`);
      document.getElementsByClassName('fc-next-button')[0].setAttribute('title_log', `次へ ${alt}`);
      document.getElementsByClassName('fc-today-button')[0].setAttribute('title_log', `今 ${alt}`);
    },

    // drag event other element to calendar
    setupDraggable(idName, className) {
      new Draggable(document.getElementById(idName), {
        itemSelector: className,
        longPressDelay: 500,
        eventData: function (eventEl) {
          var json_event = eventEl.getAttribute('data-event');
          let data = JSON.parse(json_event);
          let event = {
            title: eventEl.innerText
          };

          switch (idName) {
            case 'eventVisit':
              event = {
                schedule_type: '10',
                title: data.facility_short_name + data.person_name,
                facility_cd: data.facility_cd,
                facility_short_name: data.facility_short_name,
                person_cd: data.person_cd,
                person_name: data.person_name,
                department_cd: data.department_cd,
                department_name: data.department_name,
                position_name: data.position_name,
                title_log: '訪問先追加'
              };
              break;
            case 'otherInterview':
              if (data.schedule_type === '20' || data.schedule_type === '30') {
                event = {
                  schedule_type: data.schedule_type,
                  title: data.display_option_name,
                  display_option_type: data.display_option_type,
                  display_option_name: data.display_option_name,
                  title_log: '面談以外イベント追加_' + data.display_option_name
                };
              }

              if (data.schedule_type === '40') {
                event = {
                  schedule_type: data.schedule_type,
                  title: '社内予定',
                  display_option_type: data.display_option_type,
                  office_schedule_type: '',
                  office_title: '社内予定',
                  title_log: '面談以外イベント追加_' + data.display_option_name
                };
              }

              if (data.schedule_type === '50') {
                event = {
                  schedule_type: data.schedule_type,
                  title: 'プライベート',
                  display_option_type: data.display_option_type,
                  private_title: 'プライベート',
                  title_log: '面談以外イベント追加_' + data.display_option_name
                };
              }
              break;
            case 'selectGroupFacility':
              event = {
                title: data.facility_short_name,
                schedule_type: '10',
                display_option_type: 'V99',
                facility_cd: data.facility_cd,
                facility_short_name: data.facility_short_name,
                title_log: 'セレクト追加'
              };
              break;
            case 'selectGroupPerson':
              event = {
                title: data.facility_short_name + data.person_name,
                schedule_type: '10',
                facility_cd: data.facility_cd,
                facility_short_name: data.facility_short_name,
                person_cd: data.person_cd,
                person_name: data.person_name,
                position_name: data.position_name,
                department_cd: data.department_cd,
                department_name: data.department_name,
                content_cd: data?.content_list ? data?.content_list[0]?.content_cd : '',
                content_name_tran: data?.content_list ? data?.content_list[0]?.content_name : '',
                product_list: data.product_list,
                document_list: data.document_list,
                title_log: 'セレクト追加'
              };
              break;
            case 'selectStock':
              event = {
                title: data.facility_short_name + data.person_name,
                schedule_type: '10',
                facility_cd: data.facility_cd,
                facility_short_name: data.facility_short_name,
                person_cd: data.person_cd,
                person_name: data.person_name,
                position_name: data.position_name,
                department_cd: data.department_cd,
                department_name: data.department_name,
                content_cd: data.content_cd,
                content_name_tran: data.content_name,
                product_list: data.product_list,
                document_list: data.document_list,
                stock_id: data.stock_id,
                status_type: data.status_type,
                title_log: 'ストック追加'
              };
              break;

            default:
              break;
          }

          return event;
        }
      });
    },

    convertDateTime(dateTime) {
      if (dateTime.length >= 19) {
        let date = dateTime?.slice(0, 10)?.replaceAll('/', '-');
        let time = dateTime?.slice(11, 19);
        return date + 'T' + time;
      }
    },

    getWeekByDate(date, isLoadingDefault) {
      let curr = new Date(date);
      let week = [];

      for (let i = 1; i <= 7; i++) {
        let first = curr.getDate() - curr.getDay() + i;
        let day = moment(new Date(curr.setDate(first))).format('YYYY/MM/DD');
        week.push(day);
      }

      if (this.modeView === 'timeGridWeek') {
        if (!isLoadingDefault) {
          if (this.startDate !== week[0] || this.endDate !== week[6]) {
            this.startDate = week[0];
            this.endDate = week[6];
            this.getScheduleByDate(false, isLoadingDefault);
          }
        } else {
          this.startDate = week[0];
          this.endDate = week[6];
          this.getScheduleByDate(false, isLoadingDefault);
        }
      } else {
        this.getScheduleByDate(false, isLoadingDefault);
      }
    },

    getUserInfo(startDate) {
      Z04_S01_Services.getUserInfoService()
        .then((res) => {
          this.userInfo = {
            user_cd: res.data.data.user_cd,
            user_name: res.data.data.user_name
          };
          this.lstUser.push({
            user_cd: this.userInfo.user_cd,
            user_name: this.userInfo.user_name
          });
          this.lstUserOld = [...this.lstUser];
          this.formFilter.user_cd.push(this.userInfo.user_cd);
          this.getScreenData(startDate, true);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },

    getScreenData(startDate, isLoadingDefault) {
      this.loadingCalendar = true;
      let params = {};
      A01_S01_ScheduleInputService.getScreenData(params)
        .then((res) => {
          let data = res.data.data;
          this.lstSegment = data.facility_person_segment_type;
          this.lstTargetProduct = data.target_product;
          this.lstDisplayOption = data.display_option;
          this.lstTypeSchedule = data.display_option.filter(
            (x) => x.display_option_type === 'B99' || x.display_option_type === 'C99' || x.display_option_type === 'O99' || x.display_option_type === 'P99'
          );
          this.lstTypeSchedule = this.lstTypeSchedule.map((x) => {
            return {
              schedule_type: x.display_option_type === 'B99' ? '20' : x.display_option_type === 'C99' ? '30' : x.display_option_type === 'O99' ? '40' : '50',
              display_option_name: x.display_option_name,
              display_option_type: x.display_option_type,
              background: x.background_color
            };
          });

          let date = startDate ? moment(new Date(startDate)).format('YYYY/MM/DD') : moment().format('YYYY/MM/DD');
          this.getDataVisit();
          this.getDataStock(this.selectStockPlaned);
          this.getFacilityOrPersonGroup('f');
          if (this.modeView === 'dayGridMonth') {
            this.getScheduleByDate();
          } else {
            this.getWeekByDate(date, isLoadingDefault);
          }
        })
        .catch((err) => {
          this.loadingCalendar = false;

          this.loadingVisit = true;
          this.loadingStock = true;
          this.loadingGroup = true;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          if (this.$refs.lstUsage) {
            this.$refs.lstUsage.scrollTop = 0;
          }
          localStorage.setItem('isLoadingComponent', false);
        });
    },

    getScheduleByDate(notLoading, isLoadingDefault) {
      this.loadingCalendar = notLoading ? false : true;
      let params = {
        schedule_date_from: this.startDate,
        schedule_date_to: this.endDate
      };
      A01_S01_ScheduleInputService.getScheduleByDate(params)
        .then((res) => {
          let data = res.data.data;
          this.listEvent = [];
          data.map((x) => {
            this.listEvent.push({
              id: x.schedule_id,
              title: x.title,
              start: this.convertDateTime(x.start_time),
              end: this.convertDateTime(x.end_time),
              allDay: x.all_day_flag === 1 ? true : false,
              backgroundColor: x.background_color ? x.background_color : '#FFFF',
              start_time: x.start_time,
              end_time: x.end_time,
              start_date: x.start_date,
              all_day_flag: x.all_day_flag,
              schedule_type: x.schedule_type,
              user_cd: x.user_cd,
              display_option_type: x.display_option_type,
              display_option_name: x.display_option_name,
              icon: x.icon,
              frame_border_color: x.frame_border_color,
              borderColor: x.frame_border_color && x.frame_border_color !== 'null' && x.frame_border_color !== '0' ? x.frame_border_color : x.background_color,
              person_cd: x.person_cd ? x.person_cd : '',
              isEditSchedule: true,
              className: [
                isLoadingDefault && this.prop_zoom ? (x.schedule_id == this.prop_schedule_id ? 'zoom-in-out-box log-icon' : 'log-icon') : 'log-icon',
                x.icon == 'icon_plan.svg'
                  ? 'icon_plan'
                  : x.icon == 'icon_result.svg'
                  ? 'icon_result'
                  : x.icon == 'icon_important.svg'
                  ? 'icon_important'
                  : x.icon == 'icon_plan-result.svg'
                  ? 'icon_plan-result'
                  : x.icon == 'icon_important_plan.svg'
                  ? 'icon_important_plan'
                  : x.icon == 'icon_important_result.svg'
                  ? 'icon_important_result'
                  : x.icon == 'icon_important_result_plan.svg'
                  ? 'icon_important_result_plan'
                  : ''
              ]
            });
          });
          this.calendarOptions.events = this.listEvent;

          localStorage.setItem('startActive', this.startDate);
          localStorage.setItem('endActive', this.endDate);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.loadingCalendar = false;
          this.isReload = false;
          this.cancelCopy();

          if (this.listEvent?.length > 0) {
            if (this.modeView !== 'dayGridMonth') {
              if (document.getElementById('buttonCopy_id')) {
                if (this.listEvent.some((x) => x.schedule_type == 10)) {
                  document.getElementById('buttonCopy_id').disabled = false;
                } else {
                  document.getElementById('buttonCopy_id').disabled = true;
                }
                if (this.isCopy) {
                  document.getElementById('buttonPaste_id').disabled = false;
                } else {
                  document.getElementById('buttonPaste_id').disabled = true;
                }
              }
            }

            let classEvent = document.querySelectorAll('.fc-event');
            classEvent?.forEach((item) => {
              let schedule_type = item.fcSeg.eventRange.def.extendedProps.schedule_type;
              let title = 'イベント日付';
              switch (schedule_type) {
                case '10':
                  title = 'イベントボックス-面談';

                  break;
                case '20':
                  title = 'イベントボックス-説明会';

                  break;
                case '30':
                  title = 'イベントボックス-講演会';

                  break;
                case '40':
                  title = 'イベントボックス-社内予定';

                  break;
                case '50':
                  title = 'イベントボックス-プライベート';

                  break;
                default:
                  break;
              }

              item?.setAttribute('title_log', title);
            });
          } else {
            document.getElementById('buttonCopy_id').disabled = true;
            if (this.isCopy) {
              document.getElementById('buttonPaste_id').disabled = false;
            } else {
              document.getElementById('buttonPaste_id').disabled = true;
            }
          }
        });
    },

    copySchedule() {
      this.startDateCopy = this.formatFullDate(this.startDate);
      this.endDateCopy = this.formatFullDate(this.endDate);
      this.isCopy = true;
      let message = this.startDateCopy + '～' + this.endDateCopy + 'のスケジュールをコピーしました。';
      if (this.modeView == 'timeGridDay') {
        message = this.startDateCopy + 'のスケジュールをコピーしました。';
      }
      this.$notify({ message: message, customClass: 'success' });

      document.getElementById('buttonPaste_id').disabled = false;
    },

    pasteSchedule() {
      if (this.isCopy) {
        document.getElementById('buttonPaste_id').disabled = true;
        let params = {
          schedule_date_from: this.startDateCopy,
          schedule_date_to: this.endDateCopy,
          start_date: this.startDate
        };
        A01_S01_ScheduleInputService.copySchedule(params)
          .then((res) => {
            let data = res.data;
            this.$notify({ message: data.message, customClass: 'success' });
            this.getScheduleByDate();
            this.isCopy = false;
            document.getElementById('buttonPaste_id').disabled = true;
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
            this.isCopy = true;
            document.getElementById('buttonPaste_id').disabled = false;
          })
          .finally(() => {});
      } else {
        this.$notify({ message: 'スケジュールを選択してください。', customClass: 'error' });
      }
    },

    confirmCancelCopy() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModalConfirm: true,
        width: '33rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        destroyOnClose: true,
        props: {}
      };
    },

    rederectToView() {
      this.isCopy = false;
      this.processingRedirectView = true;
      let calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.changeView(this.newModeView);
      this.modeView = this.newModeView;
      let pasteBtn = document.getElementById('buttonPaste_id');
      if (pasteBtn) {
        pasteBtn.disabled = true;
      }
      this.cancelCopy();
    },

    cancelCopy() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowModalConfirm: false, customClass: 'custom-dialog' };
      this.processingRedirectView = false;
    },

    handleEventClick(eventInfo) {
      let type = eventInfo.event.extendedProps.schedule_type;
      if (eventInfo.event.extendedProps.isEditSchedule) {
        if (type === '10') {
          this.redirectToInterViewDetail(eventInfo);
        }

        if (type === '40') {
          this.openModalA01S03InHouseSchedule(eventInfo);
        }

        if (type === '50') {
          this.openModalA01S03ModalPrivate(eventInfo);
        }

        if (type === '20') {
          this.redirectToB01S02(eventInfo);
        }

        if (type === '30') {
          this.redirectToC01S02(eventInfo);
        }
      }
    },

    confirmAddEvent() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          mode: 1,
          message: 'コピー中に別の操作が発生されるのでコーピをキャンセルしてもよろしいでしょうか？'
        }
      };
    },

    handleConfirmAddEvent() {
      this.isCopy = false;
      this.handleEventDropOther(this.eventDropPerson);
      this.onCloseModal();
    },

    // resize event
    handleEventResizeStop(eventInfo) {
      let endTime = new Date(new Date(eventInfo.event.start).setHours(23, 59, 59, 59)).getTime();
      let endTimeNew = new Date(eventInfo.event.end).getTime();
      if (endTimeNew > endTime) {
        eventInfo.revert();
      } else {
        if (eventInfo.event.extendedProps.isEditSchedule) {
          let params = {
            schedule_id: eventInfo.event.id,
            schedule_type: eventInfo.event.extendedProps.schedule_type,
            start_date: this.formatFullDate(eventInfo.event.start),
            start_time: this.formatFullDateTimePattern(eventInfo.event.start),
            end_time: this.formatFullDateTimePattern(eventInfo.event.end),
            all_day_flag: eventInfo.event.allDay ? 1 : 0
          };

          A01_S01_ScheduleInputService.updateInfoSchedule(params)
            .then((res) => {
              this.loadingStock = false;
              this.$notify({ message: res.data.message, customClass: 'success' });
              this.getScheduleByDate();
            })
            .catch((err) => {
              this.loadingStock = false;
              this.$notify({ message: err.response.data.message, customClass: 'error' });
            })
            .finally(() => {
              if (this.$refs.listStock) {
                this.$refs.listStock.scrollTop = 1;
              }
              this.finallyDropEvent();
            });
        } else {
          eventInfo.revert();
        }
      }
    },

    // update date time
    handleEventDrop(eventInfo) {
      let endDate = moment(new Date(eventInfo.event.end));

      if (moment(eventInfo.event.end).format('DD') !== moment(eventInfo.event.start).format('DD') || eventInfo.event.allDay) {
        endDate = moment(new Date(new Date(eventInfo.event.start).setHours(23, 59, 59, 59)));
      }

      if (eventInfo.oldEvent.extendedProps.all_day_flag == 1) {
        endDate = moment(new Date(new Date(eventInfo.event.start).setHours(new Date(eventInfo.event.start).getHours() + 1)));
      }

      if (eventInfo.event.extendedProps.isEditSchedule) {
        let params = {
          schedule_id: eventInfo.event.id,
          schedule_type: eventInfo.event.extendedProps.schedule_type,
          start_date: this.formatFullDate(eventInfo.event.start),
          start_time: this.formatFullDateTimePattern(eventInfo.event.start),
          end_time: this.formatFullDateTimePattern(endDate),
          all_day_flag: eventInfo.event.allDay ? 1 : 0
        };

        A01_S01_ScheduleInputService.updateInfoSchedule(params)
          .then((res) => {
            this.loadingStock = false;
            this.$notify({ message: res.data.message, customClass: 'success' });
            this.getScheduleByDate();
          })
          .catch((err) => {
            this.loadingStock = false;
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {
            if (this.$refs.listStock) {
              this.$refs.listStock.scrollTop = 1;
            }
            this.finallyDropEvent();
          });
      } else {
        eventInfo.revert();
      }
    },

    checkHasDailyreport(startDateCheck, scheduleTypeCheck, eventInfo) {
      let params = {
        start_date: startDateCheck,
        schedule_type: scheduleTypeCheck
      };
      A01_S01_ScheduleInputService.getScreenData(params)
        .then(() => {
          this.redirectToB01S02(eventInfo, true);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.finallyDropEvent();
        })
        .finally(() => {});
    },

    // create schedule
    handleEventDropOther(eventInfo) {
      this.eventDropPerson = eventInfo || '';
      let eventParams = {};
      let hoursPlus = eventInfo.event.allDay ? 23 : 1;

      let endDate = moment(new Date(new Date(eventInfo.event.start).setHours(new Date(eventInfo.event.start).getHours() + hoursPlus)));

      if (eventInfo.event.allDay) {
        endDate = moment(new Date(new Date(eventInfo.event.start).setHours(new Date(eventInfo.event.start).getHours(), 59, 0, 0)));
      }

      const main = document.querySelector('#app');
      let log = this.decodeData(main?.getAttribute('log'));
      if (log) {
        log.info_button_fe = eventInfo.event.extendedProps.title_log;
        main.setAttribute('log', this.enCodeData(log));
        localStorage.setItem('header', JSON.stringify(log));
      }

      if (!this.isCopy) {
        // tab 1
        if (this.eventDragTabType === 'visit') {
          eventParams = {
            schedule_type: eventInfo.event.extendedProps.schedule_type,
            title: eventInfo.event.title,
            start_date: this.formatFullDate(eventInfo.event.start),
            start_time: this.formatFullDateTimePattern(eventInfo.event.start),
            end_time: this.formatFullDateTimePattern(endDate),
            all_day_flag: eventInfo.event.allDay ? 1 : 0,
            facility_cd: eventInfo.event.extendedProps.facility_cd,
            facility_short_name: eventInfo.event.extendedProps.facility_short_name,
            person_cd: eventInfo.event.extendedProps.person_cd,
            person_name: eventInfo.event.extendedProps.person_name,
            department_cd: eventInfo.event.extendedProps.department_cd,
            department_name: eventInfo.event.extendedProps.department_name,
            display_position_name: eventInfo.event.extendedProps.position_name
          };
          this.addVisit(eventParams);
        }

        // tab 2
        if (this.eventDragTabType === 'interview') {
          if (eventInfo.event.extendedProps.schedule_type === '20') {
            let start_date = this.formatFullDate(eventInfo.event.start);
            this.checkHasDailyreport(start_date, '20', eventInfo);
          }

          if (eventInfo.event.extendedProps.schedule_type === '30') {
            this.redirectToC01S02(eventInfo, true);
          }

          if (eventInfo.event.extendedProps.schedule_type === '40') {
            eventParams = {
              schedule_type: eventInfo.event.extendedProps.schedule_type,
              title: eventInfo.event.title,
              start_date: this.formatFullDate(eventInfo.event.start),
              start_time: this.formatFullDateTimePattern(eventInfo.event.start),
              end_time: this.formatFullDateTimePattern(endDate),
              all_day_flag: eventInfo.event.allDay ? 1 : 0,
              display_option_type: eventInfo.event.extendedProps.display_option_type,
              office_schedule_type: eventInfo.event.extendedProps.office_schedule_type,
              office_title: eventInfo.event.extendedProps.office_title
            };
            this.addOtherInterView(eventParams);
          }

          if (eventInfo.event.extendedProps.schedule_type === '50') {
            eventParams = {
              schedule_type: eventInfo.event.extendedProps.schedule_type,
              title: eventInfo.event.title,
              start_date: this.formatFullDate(eventInfo.event.start),
              start_time: this.formatFullDateTimePattern(eventInfo.event.start),
              end_time: this.formatFullDateTimePattern(endDate),
              all_day_flag: eventInfo.event.allDay ? 1 : 0,
              display_option_type: eventInfo.event.extendedProps.display_option_type,
              private_title: eventInfo.event.extendedProps.private_title
            };
            this.addOtherInterView(eventParams);
          }
        }

        // tab 3
        if (this.eventDragTabType === 'stock') {
          eventParams = {
            title: eventInfo.event.title,
            schedule_type: eventInfo.event.extendedProps.schedule_type,
            start_date: this.formatFullDate(eventInfo.event.start),
            start_time: this.formatFullDateTimePattern(eventInfo.event.start),
            end_time: this.formatFullDateTimePattern(endDate),
            all_day_flag: eventInfo.event.allDay ? 1 : 0,
            facility_cd: eventInfo.event.extendedProps.facility_cd,
            facility_short_name: eventInfo.event.extendedProps.facility_short_name,
            person_cd: eventInfo.event.extendedProps.person_cd,
            person_name: eventInfo.event.extendedProps.person_name,
            display_position_name: eventInfo.event.extendedProps.position_name,
            department_cd: eventInfo.event.extendedProps.department_cd,
            department_name: eventInfo.event.extendedProps.department_name,
            content_cd: eventInfo.event.extendedProps.content_cd,
            content_name_tran: eventInfo.event.extendedProps.content_name_tran,
            product_list: eventInfo.event.extendedProps.product_list,
            document_list: eventInfo.event.extendedProps.document_list,
            stock_id: eventInfo.event.extendedProps.stock_id,
            status_type: eventInfo.event.extendedProps.status_type
          };
          this.addPersonStock(eventParams);
        }

        // tab 4
        if (this.eventDragTabType === 'selectGroup') {
          if (this.selectGroup === 'f') {
            eventParams = {
              title: eventInfo.event.title,
              schedule_type: eventInfo.event.extendedProps.schedule_type,
              display_option_type: eventInfo.event.extendedProps.display_option_type,
              facility_cd: eventInfo.event.extendedProps.facility_cd,
              facility_short_name: eventInfo.event.extendedProps.facility_short_name,
              start_date: this.formatFullDate(eventInfo.event.start),
              start_time: this.formatFullDateTimePattern(eventInfo.event.start),
              end_time: this.formatFullDateTimePattern(endDate),
              all_day_flag: eventInfo.event.allDay ? 1 : 0
            };
            this.addFacilitySelect(eventParams);
          }

          if (this.selectGroup === 'p') {
            eventParams = {
              title: eventInfo.event.title,
              schedule_type: eventInfo.event.extendedProps.schedule_type,
              start_date: this.formatFullDate(eventInfo.event.start),
              start_time: this.formatFullDateTimePattern(eventInfo.event.start),
              end_time: this.formatFullDateTimePattern(endDate),
              all_day_flag: eventInfo.event.allDay ? 1 : 0,
              facility_cd: eventInfo.event.extendedProps.facility_cd,
              facility_short_name: eventInfo.event.extendedProps.facility_short_name,
              person_cd: eventInfo.event.extendedProps.person_cd,
              person_name: eventInfo.event.extendedProps.person_name,
              display_position_name: eventInfo.event.extendedProps.position_name,
              department_cd: eventInfo.event.extendedProps.department_cd,
              department_name: eventInfo.event.extendedProps.department_name,
              content_cd: eventInfo.event.extendedProps.content_cd,
              content_name_tran: eventInfo.event.extendedProps.content_name_tran,
              product_list: eventInfo.event.extendedProps.product_list,
              document_list: eventInfo.event.extendedProps.document_list
            };
            this.addPersonSelect(eventParams);
          }
        }
      } else {
        this.confirmAddEvent();
      }
    },

    getTime(eventInfo) {
      let startTime = moment(new Date(new Date(eventInfo.extendedProps.start_time).setHours(0, 0, 0, 0)));
      let endTime = moment(new Date(new Date(eventInfo.extendedProps.end_time).setHours(23, 59, 59, 59)));

      return {
        startTime: startTime,
        endTime: endTime
      };
    },

    // Modal A01-S03: inhouse
    openModalA01S03InHouseSchedule(eventInfo) {
      let info = eventInfo.event;
      const userLogin = this.$store.state.auth.currentUser.user_cd;
      let edit_flg = userLogin === info.extendedProps.user_cd;
      let props = {
        scheduleId: parseInt(info.id),
        date: info.extendedProps.start_date,
        startTime: info.allDay ? this.getTime(info).startTime : info.extendedProps.start_time,
        endTime: info.allDay ? this.getTime(info).endTime : info.extendedProps.end_time,
        all_day_flag: info.allDay,
        editFlagProps: edit_flg,
        isEditFlag: 1
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: '社内予定',
        isShowModal: true,
        component: markRaw(A01_S03_ModalInHouseSchedule),
        width: '44rem',
        customClass: 'custom-dialog modal-fixed',
        props: { ...props },
        destroyOnClose: true
      };
    },

    onResultModalA01S03InHouseSchedule() {
      this.getScheduleByDate();
    },

    // Modal A01-S03: private
    openModalA01S03ModalPrivate(eventInfo) {
      let info = eventInfo.event;
      const userLogin = this.$store.state.auth.currentUser.user_cd;
      let edit_flg = userLogin === info.extendedProps.user_cd;
      let props = {
        scheduleId: parseInt(info.id),
        date: info.extendedProps.start_date,
        startTime: info.allDay ? this.getTime(info).startTime : info.extendedProps.start_time,
        endTime: info.allDay ? this.getTime(info).endTime : info.extendedProps.end_time,
        all_day_flag: info.allDay,
        editFlagProps: edit_flg,
        isEditFlag: 1
      };

      this.modalConfig = {
        ...this.modalConfig,
        title: 'プライベート',
        isShowModal: true,
        component: markRaw(A01_S03_ModalPrivate),
        width: '44rem',
        customClass: 'custom-dialog modal-fixed',
        props: { ...props },
        destroyOnClose: true
      };
    },

    onResultModalA01S03ModalPrivate() {
      this.getScheduleByDate();
    },

    // MH A01-S03: interview
    redirectToInterViewDetail(eventInfo) {
      let info = eventInfo.event;
      let schedule_id = parseInt(info.id);
      this.$router.push({ name: 'A01_S03_InterviewDetails', params: { schedule_id } });
    },

    // B01-S02
    redirectToB01S02(eventInfo, isCreate) {
      let info = eventInfo.event;
      let schedule_id = parseInt(info.id);
      let start = this.formatFullDateTime(info.start);
      if (isCreate) {
        if (info.allDay) {
          let allDay = info.allDay;
          this.$router.push({ name: 'B01_S02_BriefingSessionInput', params: { start, allDay } });
        } else {
          this.$router.push({ name: 'B01_S02_BriefingSessionInput', params: { start } });
        }
      } else {
        this.$router.push({ name: 'B01_S02_BriefingSessionInput', params: { schedule_id } });
      }
    },

    // C01-S02
    redirectToC01S02(eventInfo, isCreate) {
      let info = eventInfo.event;
      let schedule_id = parseInt(info.id);
      let start = this.formatFullDateTime(info.start);
      if (isCreate) {
        if (info.allDay) {
          let allDay = info.allDay;
          this.$router.push({ name: 'C01_S02_LectureInput', params: { start, allDay } });
        } else {
          this.$router.push({ name: 'C01_S02_LectureInput', params: { start } });
        }
      } else {
        this.$router.push({ name: 'C01_S02_LectureInput', params: { schedule_id } });
      }
    },

    // form Filter
    showModalFilter(isReload) {
      this.showFilter = !this.showFilter;
      if (this.showFilter) {
        this.setTabType(isReload ? 'visit' : this.tabNameActive);
        this.isOpenDefaultFilter = true;
      } else {
        this.eventDragTabType = '';
        this.isOpenDefaultFilter = false;
      }
    },

    // tabs 1: 検索条件
    showFormFilterVisit() {
      this.showFilterVisit = !this.showFilterVisit;
      this.$refs.lstVisitScroll.scrollTop = 0;
      if (!this.showFilterVisit) {
        if (!_.isEqual(this.formFilter, this.paramFilterVisitOld)) {
          this.formFilter = { ...this.paramFilterVisitOld };
          this.lstUser = [...this.lstUserOld];
        }
      }
      let div = document.querySelector('.formVisit');
      if (div.classList.contains('expand')) {
        div.classList.remove('expand');
      } else {
        div.classList.add('expand');
      }
    },

    handleRemoveUser(user) {
      this.lstUser = this.lstUser?.filter((x) => x.user_cd !== user.user_cd);
      this.formFilter.user_cd = this.lstUser?.map((x) => x.user_cd);
      let lstOrg = this.lstUser.map((x) => x.org_cd);
      lstOrg = [...new Set(lstOrg)];

      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        orgCdList: lstOrg,
        userCdList: this.lstUser
      };
    },

    checkedValidation() {
      let dataFilterEmp = {
        user_cd: [],
        person_name: '',
        facility_name: '',
        segment_type: '',
        target_product_cd: ''
      };
      let checked = !_.isEqual(dataFilterEmp, this.formFilter);
      return checked;
    },

    getDataVisit() {
      let params = {
        ...this.formFilter,
        offset: this.paginationVisit.curentPage - 1,
        limit: this.paginationVisit.pageSize
      };
      this.paramFilterVisitOld = { ...this.formFilter };
      this.lstUserOld = [...this.lstUser];
      this.isSearch = true;
      if (this.checkedValidation()) {
        this.showFilterVisit = false;
        this.loadingVisit = true;

        let div = document.querySelector('.formVisit');
        div.classList.remove('expand');

        A01_S01_ScheduleInputService.getDataVisit(params)
          .then((res) => {
            this.lstVisit = res.data.data.records;
            let pageined = res.data?.data?.pagination;
            this.paginationVisit = {
              ...this.paginationVisit,
              totalItems: pageined.total_items
            };
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {
            if (this.$refs.lstVisitScroll) {
              this.$refs.lstVisitScroll.scrollTop = 0;
            }
            this.loadingVisit = false;
          });
      } else {
        this.$notify({ message: '検索条件は1つ以上指定する必要があります', customClass: 'error' });
      }
    },

    handleCurrentChangeVisit(val) {
      this.paginationVisit.curentPage = val;
      this.getDataVisit();
    },

    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05_S01_Organization),
        props: { ...this.paramsZ05S01 },
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (e) {
        this.paramsZ05S01.orgCdList = [];
        this.paramsZ05S01.userCdList = [];
        e.userSelected?.forEach((x) => {
          this.paramsZ05S01.orgCdList.push(x.org_cd);
        });
        e.userSelected?.forEach((x) => {
          this.paramsZ05S01.userCdList.push({
            org_cd: x.org_cd,
            user_cd: x.user_cd
          });
        });

        this.lstUser = e.userSelected;
        this.formFilter.user_cd = e.userSelected?.map((x) => x.user_cd);
      }
    },

    addVisit(params) {
      A01_S01_ScheduleInputService.addVisit(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.finallyDropEvent();
        });
    },

    addOtherInterView(params) {
      A01_S01_ScheduleInputService.addOtherInterView(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.finallyDropEvent();
        });
    },

    // tab 3: ストック
    getDataStock(selectStockPlaned) {
      if (this.selectStockPlaned !== selectStockPlaned) {
        this.selectStockPlaned = selectStockPlaned;
        this.paginationStock = {
          ...this.paginationStock,
          curentPage: 1,
          totalItems: 0
        };
      }
      let params = {
        status_type: this.selectStockPlaned,
        offset: this.paginationStock.curentPage - 1,
        limit: this.paginationStock.pageSize
      };
      this.loadingStock = true;
      this.lstPerson = [];
      this.lstPersonStock = [];
      this.selectInfoId = '';
      A01_S01_ScheduleInputService.getDataStock(params)
        .then((res) => {
          let data = res.data.data.records;
          let pageined = res.data?.data?.pagination;
          this.paginationStock = {
            ...this.paginationStock,
            totalItems: pageined.total_items
          };

          this.lstPersonStock = data.map((x) => {
            return {
              ...x,
              document_list: JSON.parse(x.document_list),
              product_list: JSON.parse(x.product_list),
              product_name_list: JSON.parse(x.product_list)
                ?.map((x) => x.product_name)
                ?.toString(),
              showInfo: false
            };
          });
          this.loadingStock = false;
        })
        .catch((err) => {
          this.loadingStock = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          if (this.$refs.listStock) {
            this.$refs.listStock.scrollTop = 1;
          }
        });
    },

    handleCurrentChangeStock(val) {
      this.paginationStock.curentPage = val;
      this.getDataStock(this.selectStockPlaned);
    },

    changeShowInfoPersonStock(personStock) {
      this.lstPerson.forEach((x) => {
        x.showInfo = false;
        if (x.stock_id === personStock.stock_id) {
          x.showInfo = true;
        }
      });
    },

    addPersonStock(params) {
      A01_S01_ScheduleInputService.addPersonSelect(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          let contentCheck = params.content_cd;
          let productCheck = params.product_list;

          if (this.selectStockPlaned === 1 && contentCheck && productCheck) {
            this.editStock(params);
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.finallyDropEvent();
        });
    },

    editStock(params) {
      let paramsEdit = {
        stock_id: [params.stock_id],
        status_type: 0
      };
      A01_S01_ScheduleInputService.editStock(paramsEdit)
        .then(() => {
          this.getDataStock(this.selectStockPlaned);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    // tab 4: セレクト
    getFacilityOrPersonGroup(selectGroup) {
      this.selectGroup = selectGroup;
      let params = {
        select: this.selectGroup
      };
      this.loadingGroup = true;
      this.lstFacility = [];
      this.lstPerson = [];
      this.selectInfoId = '';
      A01_S01_ScheduleInputService.getFacilityOrPersonGroup(params)
        .then((res) => {
          let data = res.data.data;
          this.lstGroup = data;
          this.paginationGroup.curentPage = 1;
          this.paginationGroup.totalItems = 0;

          if (this.lstGroup.length > 0) {
            if (selectGroup === 'f') {
              this.formFilter4.select_facility_group_id = this.lstGroup[0].select_facility_group_id;
              this.getDataFacility();
            } else {
              this.formFilter4.select_person_group_id = this.lstGroup[0].select_person_group_id;
              this.getDataPerson();
            }
          } else {
            this.loadingGroup = false;
          }
        })
        .catch((err) => {
          this.loadingGroup = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          if (this.$refs.listGroupSelect) {
            this.$refs.listGroupSelect.scrollTop = 0;
          }
        });
    },

    getDataFacility() {
      this.lstFacility = [];
      this.selectInfoId = '';
      let params = {
        select_facility_group_id: this.formFilter4.select_facility_group_id,
        offset: this.paginationGroup.curentPage - 1,
        limit: this.paginationGroup.pageSize
      };
      this.loadingGroup = true;
      A01_S01_ScheduleInputService.getDataFacility(params)
        .then((res) => {
          let data = res.data.data.records;
          let pageined = res.data?.data?.pagination;
          this.paginationGroup = {
            ...this.paginationGroup,
            totalItems: pageined.total_items
          };
          this.lstFacility = data;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.loadingGroup = false;
          if (this.$refs.listGroupSelect) {
            this.$refs.listGroupSelect.scrollTop = 0;
          }
        });
    },

    getDataPerson() {
      this.lstPerson = [];
      this.selectInfoId = '';
      let params = {
        select_person_group_id: this.formFilter4.select_person_group_id,
        offset: this.paginationGroup.curentPage - 1,
        limit: this.paginationGroup.pageSize
      };
      this.loadingGroup = true;
      A01_S01_ScheduleInputService.getDataPerson(params)
        .then((res) => {
          let data = res.data.data.records;
          let pageined = res.data?.data?.pagination;
          this.paginationGroup = {
            ...this.paginationGroup,
            totalItems: pageined.total_items
          };

          this.lstPerson = data.map((x) => {
            return {
              ...x,
              content_list: JSON.parse(x.content_list),
              document_list: JSON.parse(x.document_list),
              product_list: JSON.parse(x.product_list),
              product_name_list: JSON.parse(x.product_list)
                ?.map((x) => x.product_name)
                ?.toString(),
              showInfo: false
            };
          });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.loadingGroup = false;
          if (this.$refs.listGroupSelect) {
            this.$refs.listGroupSelect.scrollTop = 0;
          }
        });
    },

    handleCurrentChangeGroup(val) {
      this.paginationGroup.curentPage = val;
      this.selectInfoId = '';

      if (this.selectGroup === 'f') {
        this.getDataFacility();
      } else {
        this.getDataPerson();
      }
    },

    addFacilitySelect(params) {
      A01_S01_ScheduleInputService.addFacilitySelect(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.finallyDropEvent();
        });
    },

    addPersonSelect(params) {
      A01_S01_ScheduleInputService.addPersonSelect(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.finallyDropEvent();
        });
    },

    selectInfoItem(id) {
      this.selectInfoId = id;
    },

    scrollData(refName) {
      let refs = this.$refs;

      for (const key in refs) {
        let id = key?.substring(key?.indexOf('_') + 1, key.length);
        if (key.includes(refName) && id == this.selectInfoId) {
          refs[key][0].click();
          this.selectInfoId = '';
        }
      }
    },

    ///
    finallyDropEvent() {
      localStorage.setItem('reload', 1);
      if (this.eventDropPerson) {
        this.eventDropPerson.revert();
      }
      this.eventDropPerson = '';
      this.getScheduleByDate();
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'A01_S03_InHouseSchedule') {
          this.onResultModalA01S03InHouseSchedule();
        }
        if (e.screen === 'A01_S03_Private') {
          this.onResultModalA01S03ModalPrivate(e);
        }
        if (e.screen === 'Z05_S01') {
          this.onResultModalZ05S01(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },

    handleCloseModal() {
      if (this.eventDropPerson) {
        this.eventDropPerson.revert();
        this.eventDropPerson = '';
      }
      this.onCloseModal();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.wrapCalendar {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.headeCalendar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: 16px 24px 12px;
  .headeCalendar-year {
    width: calc(100% - 352px);
    padding-right: 12px;
    padding-left: 284px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    @media (max-width: $viewport_desktop) {
      padding-left: 0;
    }
    @media (max-width: $viewport_tablet) {
      width: 100%;
    }
    .btnToday {
      width: 74px;
      margin-right: 12px;
    }
    .btnSlide {
      width: 76px;
      display: flex;
      .btn {
        padding: 0;
        margin-right: 12px;
        width: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .calendarText {
      width: calc(100% - 162px);
      padding-left: 36px;
      color: #000;
      font-size: 1.5rem;
      font-weight: 700;
      @media (max-width: $viewport_desktop) {
        font-size: 1.25rem;
        padding-left: 14px;
      }
    }
  }
  .headeCalendar-tools {
    width: 352px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    @media (max-width: $viewport_tablet) {
      width: 100%;
      padding-top: 10px;
    }
    .btnInfo {
      .btn {
        margin-right: 14px;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .btnSelect {
      width: 120px;
      ul {
        display: flex;
        flex-wrap: wrap;
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
            width: 40px;
            height: 40px;
            padding: 0;
            border-radius: 0;
            &.active {
              position: relative;
            }
          }
        }
      }
    }
  }
}
/*** calendarContent ***/
.calendarContent {
  height: 100%;
  padding: 20px 24px 28px;
  display: flex;
  flex-wrap: wrap;
  .colTabs {
    width: 44px;
    height: 100%;
    margin-top: 80px;
    height: calc(100% - 80px);
    .nav {
      height: 100%;
      li {
        height: calc((100% - 60px) / 5);
        padding: 7px 0;
        cursor: pointer;

        &:first-child {
          height: 60px;
          a {
            background: #fff;
            width: 44px;
          }
        }
        .visible-filter {
          &.show .svg {
            transform: rotate(-90deg);
            transition: transform 0.7s;
          }
          &.hide .svg {
            transform: rotate(90deg);
            transition: transform 0.7s;
          }
        }

        a {
          display: block;
          background: #f7f7f7;
          border-radius: 10px 0 0 10px;
          height: 100%;
          box-shadow: 0 0 5px #b7c3cb;
          color: #b7c3cb;
          font-size: 14px;
          line-height: 14px;
          position: relative;
          &::after,
          &::before {
            position: absolute;
            right: 0;
            content: '';
            display: block;
            width: 11px;
            height: 11px;
          }
          &::after {
            background: url(~@/assets/template/images/bgTabs.png) no-repeat;
            bottom: -10px;
          }
          &::before {
            background: url(~@/assets/template/images/bgTabs01.png) no-repeat;
            top: -10px;
          }
          &.active {
            color: #448add;
            background: #fff;
            font-weight: bold;
            z-index: 4;
            &::after {
              background: url(~@/assets/template/images/bgTabs02.png) no-repeat;
              bottom: -10px;
            }
            &::before {
              background: url(~@/assets/template/images/bgTabs03.png) no-repeat;
              top: -10px;
            }
          }
          &:hover {
            background: #fff;
            color: #448add;
            font-weight: bold;
            text-decoration: none;
            &::after {
              background: url(~@/assets/template/images/bgTabs02.png) no-repeat;
              bottom: -10px;
            }
            &::before {
              background: url(~@/assets/template/images/bgTabs03.png) no-repeat;
              top: -10px;
            }
          }
          span {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 14px;
            height: 100%;
            position: relative;
            word-break: break-all;
            &::after {
              position: absolute;
              top: 0;
              right: -6px;
              content: '';
              height: 100%;
              width: 6px;
              background: #fff;
            }
          }
        }
      }
    }
  }
  .colPanel {
    height: 100%;
    width: calc(100% - 44px);
    position: relative;
    .tab-content {
      position: relative;
      z-index: 2;
      height: 100%;
      padding: 5px;
      padding-top: 0;
    }

    .panelRow {
      height: 100%;
      display: flex;
      flex-wrap: wrap;
      box-shadow: -11px 13px 7px -10px #b7c3cbc2;
    }
    .colInfo {
      width: 240px;
      height: calc(100% - 80px);
      padding: 15px 0px 0px;
      background: #ffff;
      box-shadow: 7px -1px 10px 0px #b7c3cba3;
      margin-top: 80px;
      z-index: 2;
      .tab-pane {
        height: 100%;
      }
    }
    .colCalendar {
      width: calc(100% - 240px);
      height: 100%;
      z-index: 1;
    }

    .custom-w {
      width: 100%;
    }
  }
  .accordion-title {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 4;
    height: 40px;
    background: #ffffff;
    padding: 0 10px;
    cursor: pointer;

    .conditions {
      font-size: 1.125rem;
      font-weight: 700;
      position: relative;
      cursor: pointer;
      &::after {
        position: absolute;
        top: 11px;
        right: -16px;
        content: '';
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #5f6b73;
        transform: 0.5ms all;
        transition: transform 0.7s;
        transform: rotate(180deg);
      }
      &.conditions--active {
        &::after {
          transform: rotate(360deg);
          transition: transform 0.7s;
        }
      }
    }
  }
  .formVisit {
    ul {
      li {
        margin-bottom: 20px;
        &:last-child {
          margin-bottom: 0;
        }
        .formVisit-label {
          margin-bottom: 6px;
        }
      }
    }
    .formVisit-btn {
      margin-top: 30px;
      text-align: right;
      .btn {
        width: 73px;
        height: 32px;
        padding: 0 8px;
      }
    }
  }

  @-webkit-keyframes formVisit {
    0% {
      bottom: 10px;
    }
    100% {
      bottom: 0px;
    }
  }
  @keyframes formVisit {
    0% {
      bottom: 10px;
    }
    100% {
      bottom: 0px;
    }
  }

  @-webkit-keyframes lstVisit {
    0% {
      top: 10px;
    }
    100% {
      top: 0px;
    }
  }
  @keyframes lstVisit {
    0% {
      top: 10px;
    }
    100% {
      top: 0px;
    }
  }

  .lstVisit {
    border-top: 1px solid #e3e3e3;
    padding-top: 4px;
    position: relative;
    > ul {
      li {
        &:not(:first-child) {
          margin-top: 20px;
        }
        background: #fcfcfc;
        color: #5f6b73;
        border-radius: 4px;
        padding: 8px 10px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        cursor: move;
        .item-hand {
          width: 16px;
          min-width: 16px;
          cursor: move;
        }
        .lstVisit-content {
          padding-left: 12px;
          width: calc(100% - 16px);
          .lstVisit-text {
            display: flex;
            margin-bottom: 4px;
            &:last-child {
              margin-bottom: 0;
            }
            .lstVisit-item {
              min-width: 15px;
              margin-right: 6px;
              margin-top: -2px;
              img {
                max-width: 15px;
              }
            }
          }
        }
      }
    }
  }
  .lstVisit-scroll {
    padding: 10px;
    padding-right: 15px;
    padding-top: 40px;
    max-height: calc(100%);
    min-height: calc(100%);
    overflow-y: hidden;
    padding-bottom: 80px;
    background: #ffffff;
    ul {
      padding-top: 5px;
    }
  }

  .lstVisit-stock {
    margin-top: 0;
    border: none;
    > ul {
      li {
        position: relative;
        padding-right: 32px;
      }
    }
    .followStock {
      position: absolute;
      right: 10px;
      top: calc(50% - 9px);
      width: 18px;
      height: 18px;
    }
    .followStock-main {
      position: relative;
      top: -5px;
      width: 18px;
      height: 18px;
      &.show {
        .btn {
          background: url(~@/assets/template/images/icon_info-circle02.svg) no-repeat;
        }
      }
      .btn {
        width: 18px;
        height: 18px;
        padding: 0;
        border: none;
        background: url(~@/assets/template/images/icon_info-circle01.svg) no-repeat;
      }
    }
    .dropdown-menu--follow {
      background: #d1e4ff;
      border-radius: 4px;
      width: 270px;
      margin: 0;
      top: -6px !important;
      left: 28px !important;
      transform: inherit !important;
      padding: 15px 24px;
      &::after {
        position: absolute;
        top: 12px;
        left: -8px;
        content: '';
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid #d1e4ff;
      }
      ul {
        li {
          margin-bottom: 12px;
          color: #225999;
          &:last-child {
            margin-bottom: 0;
          }
          &.follow-title {
            font-weight: 700;
          }
          &.follow-item {
            display: flex;
            font-size: 1rem;
            .item {
              min-width: 14px;
              margin-right: 10px;
            }
          }
        }
      }
    }
  }
  .otherInterview {
    padding: 10px;
    ul {
      li {
        background: #fcfcfc;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        border-radius: 4px;
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
        &:last-child {
          margin-bottom: 0;
        }
        .otherInterview-box {
          width: 20px;
        }
        .otherInterview-info {
          width: calc(100% - 20px);
          padding: 8px 8px 8px 10px;
          display: flex;
          .text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 1rem;
            font-weight: 700;
          }
          .item {
            min-width: 16px;
            margin-right: 9px;
            margin-top: -1px;
            img {
              width: 16px;
            }
          }
        }
      }
    }
  }
  .stockSelect {
    ul {
      display: flex;
      flex-wrap: wrap;
      li {
        width: 50%;
        margin-left: -1px;
        min-height: 40px;
        color: #5f6b73;
        font-size: 0.875rem;
        border: 1px solid #727f88;
        padding: 0.375rem 0.75rem;
        cursor: pointer;
        text-align: center;
        &.active {
          border: 2px solid #448add;
          color: #448add;
          background: #eef6ff;
          font-weight: 700;
        }
        &:hover {
          background: #eef6ff;
          font-weight: 700;
          color: #5f6b73;
        }

        &:first-of-type {
          border-radius: 4px 0 0 4px;
        }
        &:last-child {
          border-radius: 0 4px 4px 0;
        }
        .btn {
          width: 100%;
          border-radius: 0;
          border: 1px solid #727f88;
          &.active {
            border: 2px solid #448add;
          }
        }
      }
    }
  }
  .stockSelect {
    margin-top: 12px;
  }
  .classification-title {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 4;
    height: 30px;
    background: #ffffff;
    padding: 0 10px;
    .tlt {
      font-weight: 700;
    }
  }
  .lstUsage {
    padding-top: 16px;

    ul {
      li {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
        &:last-child {
          margin-bottom: 0;
        }
        .lstUsage-box {
          width: 40px;
          height: 20px;
          border-radius: 4px;
          padding: 3px;
          display: flex;
          align-items: center;
          justify-content: center;
          &.active {
            border: 1.5px solid #ff8c67;
          }
        }
        .lstUsage-text {
          width: calc(100% - 40px);
          padding-left: 8px;
          font-size: 1rem;
          line-height: 120%;
        }
      }
    }
  }
  .lstUsage-scroll {
    padding: 10px;
    margin-top: 5px;
    padding-top: 10px;
    max-height: calc(100% - 40px);
    min-height: calc(100% - 40px);
    overflow-y: auto;
  }

  .stock-select {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 4;
    height: 40px;
    background: #ffffff;
    padding: 0 10px;
  }

  .list-stock-scroll {
    margin-top: 10px;
    max-height: calc(100% - 70px);
    min-height: calc(100% - 70px);
    overflow-y: hidden;
    padding: 10px 15px 70px 10px;
  }

  .group-select {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 4;
    height: 92px;
    background: #ffffff;
    padding: 0 10px;
  }
  .list-group-select-scroll {
    margin-top: 10px;
    max-height: calc(100% - 122px);
    min-height: calc(100% - 122px);
    overflow-y: hidden;
    padding: 10px 15px 70px 10px;
  }
}
.popover-custom {
  width: 270px;
  background: #d1e4ff !important;
  border-radius: 4px !important;

  .dropdown-menu--follow {
    ul {
      li {
        margin-bottom: 12px;
        color: #225999;
        &:last-child {
          margin-bottom: 0;
        }
        &.follow-title {
          font-weight: 700;
        }
        &.follow-item {
          display: flex;
          font-size: 1rem;
          .item {
            min-width: 14px;
            margin-right: 10px;
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

.el-select-dropdown {
  max-width: 210px;
}

.fc-event-visit,
.fc-event-facility,
.fc-event-person,
.fc-event-stock {
  background: #fcfcfc;
  color: #5f6b73;
  border-radius: 4px;
  padding: 8px 10px;
  box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  .item-hand {
    width: 16px;
    min-width: 16px;
    cursor: move;
  }
  .lstVisit-content {
    padding-left: 12px;
    width: calc(100% - 16px);
    cursor: move;
    .lstVisit-text {
      display: flex;
      margin-bottom: 4px;
      &:last-child {
        margin-bottom: 0;
      }
      .lstVisit-item {
        min-width: 15px;
        margin-right: 6px;
        margin-top: -2px;
        img {
          max-width: 15px;
        }
      }
    }
  }
}

.fc-event-interview {
  background: #fcfcfc;
  box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
  border-radius: 4px;
  margin-bottom: 24px;
  display: flex;
  flex-wrap: wrap;
  overflow: hidden;
  &:last-child {
    margin-bottom: 0;
  }
  .otherInterview-box {
    width: 20px;
    cursor: move;
  }
  .otherInterview-info {
    width: calc(100% - 20px);
    padding: 8px 8px 8px 10px;
    display: flex;
    cursor: move;
    .text {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 1rem;
      font-weight: 700;
    }
    .item {
      min-width: 16px;
      margin-right: 9px;
      margin-top: -1px;
      img {
        width: 16px;
      }
    }
  }
}

.pd-lr5 {
  padding: 10px 5px;
}

.mr-t40 {
  margin-top: 40px;
}

.confirm-dialog-custom {
  .content {
    font-size: 1rem;
    position: relative;
    margin-bottom: 30px;

    .status {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px !important;
    }

    .message {
      padding-left: 36px;
      padding-right: 12px;
    }
  }
}

.status.el-icon-warning {
  --el-messagebox-color: var(--el-color-warning);
  color: var(--el-messagebox-color);
}

.confirm-btn {
  text-align: center;

  .btn {
    width: 110px;
  }
}

.lstVisit-text-label {
  display: inline-block !important;

  .text-bold {
    font-weight: bold;
    margin-right: 10px;
    font-size: 16px;
    line-height: 24px;
  }
  .text-nomal {
    line-height: 24px;
    vertical-align: text-top;
  }
}
.tab-content {
  overflow: hidden;
}
.formVisit {
  background: #fff;
  padding-top: 25px;
  position: relative;
  padding: 10px;
  overflow-y: auto;
  max-height: calc(100% - 40px);
  min-height: calc(100% - 40px);
  transition: all 0.3s ease-out;
  transform: translateY(-100%);
  z-index: 0;
  padding-bottom: 30px;
}

.expand {
  transform: translate(0, 0);
}
.lstVisit {
  transition: all 0.3s ease-out;
}
.formVisit ~ .lstVisit {
  transform: translateY(-100%);
}
.expand ~ .lstVisit {
  transform: translate(0, 0);
}

.pagin-institution {
  position: sticky;
  bottom: -5px;
  margin: -10px;
  background: #ffffff;
  padding: 16px 8px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  box-shadow: 0px -3px 6px #e3e3e3;
  height: 62px;

  .pagination-cases {
    margin-right: 10px;
  }
}

.noactive {
  display: none;
}
</style>
