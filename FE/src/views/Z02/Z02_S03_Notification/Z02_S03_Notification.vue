<template>
  <div v-loading="loadingTable" v-load-f5="true" class="wrapContent page-zo z02-s02">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn">
            <button
              type="button"
              style="display: flex; align-items: baseline"
              class="btn btn-filter-s btnInfo-btn2"
              @click="onclickShowModal('settingNotiffi', 0)"
              @touchstart="onclickShowModal('settingNotiffi', 0)"
            >
              <span class="btn-iconLeft">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_setting-solid.svg')" alt="" />
              </span>
              通知設定
            </button>
          </div>
          <div class="btnInfo-btn">
            <button class="btn btn-filter" type="button" @click="openFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <ModalNotificationFilter
              :class="{ show: activeFilter }"
              :activefilter="activeFilter"
              @loadData="loadData(true)"
              @closeFilter="activeFilter = false"
            />
          </div>
        </div>
      </div>
      <div class="facilityContent">
        <div ref="tblnotifi" class="tbl-notifi scrollbar table-hg100">
          <table class="table-custom">
            <thead>
              <tr class="custom-tr">
                <th class="custom-table">
                  <div class="checkAll">
                    <label class="custom-control custom-checkbox">
                      <input
                        v-model="isCheckAll"
                        readonly
                        class="custom-control-input"
                        type="checkbox"
                        @click="checkAll(isCheckAll, tempDatas.length)"
                        @touchstart="checkAll"
                      />
                      <span :class="isCheckAll ? 'checkAll1' : ''"></span>
                      <span
                        :class="!isCheckAll && tempDatas.length !== 0 ? 'custom-checkbox-gmail' : ''"
                        class="custom-control-indicator custom-checkbox-k1"
                        @click="unCheckAll"
                      >
                        <span :class="isCheckAll ? 'abcde' : ''" class="abc"></span>
                      </span>
                    </label>
                    <div class="checkAll-drop checkAll-drop2" :class="{ show: activeSelect }" @click="clickActiveSelect" @touchstart="clickActiveSelect">
                      <span class="abcd"></span>
                      <button class="btn btn--arrow" data-toggle="dropdown">&nbsp;</button>
                      <ModalNotificationSelect
                        :class="{ show: activeSelect }"
                        class="dropdown-menu"
                        :data-list-catelogy-inform="dataListCatelogyInform"
                        @closeSelect="setSelect"
                      />
                    </div>
                  </div>
                </th>
                <th class="custom-table-indx2">通知種別</th>
                <th>通知内容</th>
                <th>受信日時</th>
              </tr>
            </thead>
            <tbody v-if="dataListInformed.length > 0" class="body-gray">
              <tr
                v-for="data of dataListInformed"
                :key="data.id"
                :ref="`item-update-${data.inform_id}`"
                class="custom-tbody"
                :class="`${data.archive_flag === 0 ? 'custom-tr' : 'active'} ${updateNotiProcessing[data.inform_id] ? 'slide-right' : ''}`"
              >
                <td>
                  <label class="custom-control custom-checkbox custom-checkbox-c">
                    <input v-model="tempDatas" class="custom-control-input" type="checkbox" :value="data" @change="updateCheckall" />
                    <span class="custom-control-indicator indicator"></span>
                  </label>
                </td>
                <td class="box-td">
                  <div class="title-cate">
                    <span> {{ data.inform_category_name }}</span>
                  </div>
                </td>
                <td>
                  <a
                    title_log="通知内容"
                    :class="data.read_flag === 0 ? 'changeReadFlag' : ''"
                    class="titleLink log-icon"
                    @click="read(data.inform_id, data.url, data.url2, data.parameter_key, data.parameter_value, data.inform_category_cd)"
                    >{{ data.inform_contents }}</a
                  >
                </td>
                <td>
                  <div class="notifi-dateTime">
                    <span class="span-date">{{ moment(data.inform_datetime).format('YYYY/M/D H:mm') }}</span>
                    <button
                      v-if="data.archive_flag === 0"
                      :ref="'btn-' + data.inform_id"
                      type="button"
                      :disabled="disabledBtn"
                      class="btn btn--icon"
                      title_log="アイコン"
                      @click.prevent="handlUpdateNotify($event, data.inform_id, data.archive_flag)"
                      @touchstart="handlUpdateNotify($event, data.inform_id, data.archive_flag)"
                    >
                      <img
                        :class="`svg ${updateNotiProcessing[data.inform_id] ? 'item-rotation-360' : ''} `"
                        src="@/assets/template/images/archive-in.svg"
                        alt=""
                      />
                    </button>
                    <button
                      v-if="data.archive_flag === 1"
                      :ref="'btn-' + data.inform_id"
                      type="button"
                      :disabled="disabledBtn"
                      class="btn btn--icon"
                      title_log="アイコン"
                      @click.prevent="handlUpdateNotify($event, data.inform_id, data.archive_flag)"
                      @touchstart="handlUpdateNotify($event, data.inform_id, data.archive_flag)"
                    >
                      <img
                        :class="`svg ${updateNotiProcessing[data.inform_id] ? 'item-rotation-360' : ''}`"
                        src="@/assets/template/images/archive-out-2.svg"
                        alt=""
                      />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
            <tr v-else-if="!isLoadDefault">
              <td class="custom-td" colspan="16">
                <div class="noData">
                  <div class="noData-content">
                    <p v-if="!isLoadDefault" class="noData-text">該当するデータがありません。</p>
                    <div v-if="!isLoadDefault" class="noData-thumb">
                      <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div v-if="dataListInformed.length > 0" class="pagination pagination-cs">
      <div class="pagination-cases">全 {{ pageSizedataListInformed }} 件</div>
      <PaginationTable
        :page-size="100"
        layout="prev, pager, next"
        :total="pageSizedataListInformed"
        :current-page="currentPage"
        @current-change="handleCurrentChange"
      />
    </div>
    <ModalNotificationSetting v-if="showModal" :id="id" :modalsetting="showModal" data-backdrop="static" :modal="modal" @loadDataSetting="onResultModal" />
    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog modal-fixed handle-close"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          ref="modalChild"
          v-bind="{ ...modalConfig.props }"
          @loadDataSetting="onResultModal"
          @onFinishScreen="onFinishScreenModalSetting"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import PaginationTable from '@/components/PaginationTable';
import ModalNotificationFilter from './ModalNotificationFilter.vue';
import ModalNotificationSetting from './ModalNotificationSetting.vue';
import ModalNotificationSelect from './ModalNotificationSelect.vue';
import Z02_S03_NotificationServices from '../../../api/Z02/Z02_S03_NotificationServices';
import moment from 'moment';
import { markRaw } from 'vue';
export default {
  name: 'Z02_S03_UserNotificationManagement',
  components: {
    PaginationTable,
    ModalNotificationFilter,
    ModalNotificationSetting,
    ModalNotificationSelect
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      loadingTable: false,
      isCheckAll: false,
      tempDatas: [],
      showModal: false,
      modal: '',
      id: '',
      activeFilter: false,
      activeSelect: false,
      dataListInformed: [],
      arrayInformCatelogy: [],
      inform_id: [],
      dataListCatelogyInform: [],
      pageSizedataListInformed: 0,
      pagedataListInformed: 0,
      currentPage: 1,
      total_pages: 0,
      index: 0,
      updateNotiProcessing: {},
      updateNotiSuccess: {},
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      disabledBtn: false,
      onScrollTop: 0,
      isLoadDefault: true,
      disableLink: true
    };
  },
  created() {
    this.moment = moment;
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'ユーザ通知管理',
      icon: 'icon-notification-main.svg',
      isShowBack: true
    });

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.Z02_S03_UserNotificationManagement || 0;
    this.currentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.Z02_S03_UserNotificationManagement || 1;
    this.currentPageOld = this.currentPage;

    this.getListCatelogyInform();
    // this.setBtnFilter();
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },

    setBtnFilter(body) {
      body = this.$el.nextElementSibling;
      var el = body.querySelector('.dropdown-filter');
      var noClick = true;
      if (el) {
        el.addEventListener('mouseover', () => {
          noClick = true;
        });
        el.addEventListener('mouseout', () => {
          noClick = false;
        });
      }
      body.addEventListener('click', () => {
        var el = body.getElementsByClassName('dropdown-filter');
        if (noClick === false) {
          el[0]?.classList.remove('show');
        }
      });
      const btnFilter = body.querySelector('.btn-filter');
      if (btnFilter) {
        btnFilter.addEventListener('click', () => {
          var el = body.getElementsByClassName('dropdown-filter');
          setTimeout(() => {
            el[0]?.classList.add('show');
          });
        });
      }
    },
    onclickShowModal(e, message_id) {
      this.modal = e;
      this.id = message_id;
      this.modalConfig = {
        ...this.modalConfig,
        title: '通知設定',
        isShowModal: true,
        component: markRaw(ModalNotificationSetting),
        width: '48rem',
        destroyOnClose: true
      };
    },
    openFilter() {
      this.activeFilter = true;
    },
    clickActiveSelect() {
      this.activeSelect = this.activeSelect ? true : false;
    },
    onFinishScreenModalSetting() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    onResultModal() {
      this.showModal = false;
    },
    checkAll() {
      if (this.isCheckAll) {
        this.emitter.emit('checkAll', {
          checkNoti: false
        });
      } else {
        if (this.tempDatas.length !== 0) {
          this.isCheckAll = false;
          this.tempDatas = [];
          this.emitter.emit('checkAll', {
            checkNoti: false
          });
        } else {
          this.emitter.emit('checkAll', {
            checkNoti: true
          });
        }
      }
    },
    unCheckAll() {
      if (!this.isCheckAll && this.tempDatas.length !== 0) {
        setTimeout(() => {
          this.isCheckAll = false;
          this.tempDatas = [];
          this.emitter.emit('checkAll', {
            checkNoti: false
          });
        }, 10);
      }
    },
    updateCheckall(e) {
      if (!this.inform_id.includes(e.target._value.inform_id)) {
        this.inform_id.push(e.target._value.inform_id);
      } else {
        this.inform_id.splice(this.inform_id.indexOf(e.target._value.inform_id), 1);
      }
      if (this.tempDatas.length === this.dataListInformed.length) {
        this.isCheckAll = true;
      } else {
        this.isCheckAll = false;
      }
      if (this.tempDatas.length === 0) {
        this.emitter.emit('checkNoti', {
          checkNoti: false
        });
      } else {
        this.emitter.emit('checkNoti', {
          checkNoti: true
        });
      }
    },
    getListCatelogyInform() {
      if (localStorage.getItem('dataListCatelogyInform')) {
        this.getLisstMessage(true);
      } else {
        Z02_S03_NotificationServices.getInformCategoryService()
          .then((res) => {
            if (res) {
              localStorage.setItem('dataListCatelogyInform', JSON.stringify(res.data.data));
              this.getLisstMessage(true);
            }
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          });
      }
    },
    getLisstMessage(isLoadDefault) {
      this.loadingTable = true;
      this.isLoadDefault = isLoadDefault;
      const dataCallList = JSON.parse(localStorage.getItem('dataListCatelogyInform'));
      dataCallList.forEach((element) => {
        if (element.checked === 1) {
          this.arrayInformCatelogy.push(element.inform_category_cd);
        }
      });
      const data = {
        inform_category_cd: this.arrayInformCatelogy,
        inform_datetime_from: localStorage.getItem('inform_datetime_from') ?? '',
        inform_datetime_to: localStorage.getItem('inform_datetime_to') ?? '',
        archive_flag: localStorage.getItem('archive_flag') === 'true' ? 1 : 0,
        inform_contents: localStorage.getItem('inform_contents') ?? '',
        limit: 100,
        offset: this.currentPage === 0 ? this.currentPage : this.currentPage - 1
      };
      Z02_S03_NotificationServices.postInformService(data)
        .then((res) => {
          if (res) {
            this.pageSizedataListInformed = res.data.data.pagination.total_items;
            this.total_pages = res.data.data.pagination.total_pages;
            this.pagedataListInformed = res.data.data.pagination.current_page;
            this.arrayInformCatelogy = [];
            this.isCheckAll = false;
            this.updateNotiProcessing = {};
            const dataRes = res.data.data.records;
            dataRes.forEach((element) => {
              switch (element.parameter_key) {
                case 'report_id':
                  if (element.inform_category_cd === '10') {
                    element.url2 = 'A03_S03_NotApprovedList';
                  } else {
                    element.url2 = 'A03_S01_JapaneseDailyReportList';
                  }
                  break;
                case 'briefing_id':
                  element.url2 = 'B01_S01_BriefingSearch';
                  break;
                case 'question_id':
                  element.url2 = 'E01_S01_QaSearch';
                  break;
                case 'convention_id':
                  element.url2 = 'C01_S01_LectureList';
                  break;
                case 'knowledge_id':
                  if (element.inform_category_cd === '150' || element.inform_category_cd === '170' || element.inform_category_cd === '180') {
                    element.url2 = 'F01_S01_KnowledgeSearch';
                  } else {
                    element.url2 = 'F01_S05_Pre_ReleaseKnowledgeManagement';
                  }
                  break;
                case 'facility_cd':
                  element.url2 = 'H01_FacilityDetails';
                  break;
                case 'person_cd':
                  element.url2 = 'H02_PersonalDetails';
                  break;
                case 'calendar':
                  element.url2 = 'A01_S02_Calendar';
                  break;
                default:
                  element.url2 = '';
                  break;
              }
            });
            this.dataListInformed = dataRes;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          this.setCurrentPageScreen('Z02_S03_UserNotificationManagement', this.currentPage);
          await new Promise((resolve) => setTimeout(resolve, 1000));
          if (this.$refs.tblnotifi) {
            this.$refs.tblnotifi.scrollTop = this.onScrollTop;
          }
          this.js_pscroll(0.5);
          this.loadingTable = false;
          this.isLoadDefault = false;
        });
    },
    loadData(isFilter) {
      if (isFilter) {
        this.onScrollTop = 0;
        this.currentPage = 1;
      }
      this.getLisstMessage(false);
      this.isCheckAll = false;
      this.tempDatas = [];
    },
    setSelect(e) {
      // this.activeSelect = !this.activeSelect;
      if (e === 1) {
        this.isCheckAll = true;
        this.dataListInformed.forEach((element) => {
          if (!this.inform_id.includes(element.inform_id)) {
            this.inform_id.push(element.inform_id);
          }
        });
        this.tempDatas = [];
        for (let data in this.dataListInformed) {
          this.tempDatas.push(this.dataListInformed[data]);
        }
      }
      if (e === 2) {
        this.inform_id = [];
        this.tempDatas = [];
        this.isCheckAll = false;
      }
      if (e === 3) {
        if (this.isCheckAll) {
          this.onScrollTop = 0;
        } else {
          this.setScroll();
        }
        const data = {
          inform_id: this.inform_id,
          archive_flag: 1,
          home_screen_flag: 0
        };
        if (this.inform_id.length >= 1) {
          Z02_S03_NotificationServices.putInformArchivedService(data)
            .then((res) => {
              if (res) {
                this.$notify({ message: '正常にアーカイブにしました。', customClass: 'success' });
                this.inform_id = [];
                this.tempDatas = [];
                this.isCheckAll = false;
                this.currentPage = this.currentPage - 1 || 1;
                this.loadData();
              }
            })
            .catch((err) => {
              this.$notify({ message: err.response.data.message, customClass: 'error' });
            });
        }
      }
      if (e === 4) {
        if (this.isCheckAll) {
          this.onScrollTop = 0;
        } else {
          this.setScroll();
        }
        const data = {
          inform_id: this.inform_id,
          archive_flag: 0,
          home_screen_flag: 0
        };
        if (this.inform_id.length >= 1) {
          Z02_S03_NotificationServices.putInformArchivedService(data)
            .then((res) => {
              if (res) {
                this.$notify({ message: '正常にアーカイブ取消にしました。 ', customClass: 'success' });
                if (data && data.inform_id) {
                  this.inform_id = [];
                  this.tempDatas = [];
                  this.isCheckAll = false;
                  this.currentPage = this.currentPage - 1 || 1;
                  this.loadData();
                }
              }
            })
            .catch((err) => {
              this.$notify({ message: err.response.data.message, customClass: 'error' });
            });
        }
      }
    },
    archiveRecord(id, archive_flag) {
      this.setScroll();
      const data = {
        inform_id: [id],
        archive_flag: archive_flag === 1 ? 0 : 1,
        home_screen_flag: 0
      };
      Z02_S03_NotificationServices.putInformArchivedService(data)
        .then((res) => {
          if (res) {
            this.disabledBtn = false;
            this.$notify({ message: archive_flag === 1 ? '正常にアーカイブ取消にしました。 ' : '正常にアーカイブにしました。', customClass: 'success' });
            this.inform_id = [];
            this.tempDatas = [];
            this.isCheckAll = false;
            this.loadData();
            this.emitter.emit('checkNoti', {
              checkNoti: false
            });
            this.disableLink = true;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.disabledBtn = false;
        });
    },
    async handlUpdateNotify(e, id, archive_flag) {
      this.disableLink = false;
      this.disabledBtn = true;
      this.updateNotiProcessing = { [id]: id };
      await new Promise(() =>
        setTimeout(() => {
          this.dataListInformed = this.dataListInformed.filter((data) => data.inform_id !== id);
          this.archiveRecord(id, archive_flag);
        }, 1000)
      );

      this.updateNotiProcessing = {};
    },
    async updateSuccess(itemAction, archiveFlag, type = null) {
      let item = Array.isArray(itemAction) ? itemAction[0] : itemAction;

      if (localStorage.getItem('archive_flag') === 'true') {
        await new Promise((resolve) => setTimeout(resolve, 700));
        item.classList.toggle('deleted');
        item.style.background = '#7bd7d3';
        item.addEventListener('transitionend', () => {
          item.style.display = 'none';
        });
        this.updateNotiSuccess = {};
      } else {
        if (archiveFlag === 1) {
          this.slideLeft(item, true, type);
        } else if (typeof archiveFlag === 'undefined') {
          await this.slideLeft(item, true, type);
        } else {
          await this.slideRight(item, true, type);
          await new Promise((resolve) => setTimeout(resolve, 0));
          this.updateNotiSuccess = {};
        }
      }
    },
    handleCurrentChange(val) {
      this.onScrollTop = 0;
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.getLisstMessage(false);
      } else {
        this.currentPage = val;
        this.getLisstMessage(false);
      }
    },

    setScroll() {
      this.onScrollTop = this.$refs.tblnotifi ? this.$refs.tblnotifi.scrollTop : 0;
      this.setScrollScreen('Z02_S03_UserNotificationManagement', this.onScrollTop);
    },

    read(id, url, url2, parameter_key, parameter_value) {
      if (this.disableLink) {
        this.setScroll();
        Z02_S03_NotificationServices.readServies(id).then(() => {
          switch (parameter_key) {
            case 'facility_cd':
              this.$router.push({ name: `${url2}`, params: { facility_cd: parameter_value }, query: { tab: '3' } });
              break;
            case 'person_cd':
              this.$router.push({ name: `${url2}`, params: { person_cd: parameter_value }, query: { tab: '4' } });
              break;
            case 'convention_id':
              this.$router.push({ name: `${url2}`, params: { convention_id: parameter_value } });
              break;
            case 'knowledge_id':
              this.$router.push({ name: `${url2}`, params: { knowledge_id: parameter_value } });
              break;
            case 'briefing_id':
              this.$router.push({ name: `${url2}`, params: { briefing_id: parameter_value } });
              break;
            case 'question_id':
              this.$router.push({ name: `${url2}`, params: { question_id: parameter_value } });
              break;
            case 'calendar':
              this.$router.push({ name: `${url2}`, params: {} });
              break;
            case 'report_id':
              this.$router.push({ name: `${url2}`, params: { report_id: parameter_value } });
              break;
            default:
              this.$router.push({ name: '', params: {} });
              break;
          }
        });
      }
    },
    async slideLeft(item, updateFlag = false, type = null) {
      /**
       * right to left
       * logic:
       * 1 - Right or Default : have class bg-to-right or class background-e3e3e3
       * 2 - Left or Default: Have class bg-to-left or nothing
       * Check -> If ( Right || Default) -> Remove class bg-to-right -> add class bg-to-left
       * Check -> If (Left or Default) => return;
       */

      try {
        let defaultRight = item.classList.contains('bg-to-right') || item.classList.contains('active');
        let defaultLeft = item.classList.contains('bg-to-left') || !defaultRight;
        if (type === 3 && defaultRight) return;
        if (defaultRight) {
          await new Promise((resolve) => setTimeout(resolve, 0)); // 1st -> doing remove bg-to-right
          item.classList?.remove('bg-to-right');
          await new Promise((resolve) => setTimeout(resolve, 50)); // last -> remove bg
          item.classList?.remove('active');
          await new Promise((resolve) => setTimeout(resolve, 100)); // then -> add bg-to-left
          item.classList.add('bg-to-left');
          return;
        }
        if (defaultLeft || type === 4) {
          this.slideRight(item, updateFlag, type);
          return;
        }
      } catch (err) {
        // console.log(err);
      } finally {
        if (updateFlag) {
          await new Promise((resolve) => setTimeout(resolve, 0));
          this.updateNotiSuccess = {};
        }
      }
    },
    /**
     * Item : Selector
     * updateFlag: -> setState
     * type: setSelect(e)  => e : Value -> 0 || 1 || 2 || 3 || 4
     */
    async slideRight(item, updateFlag = false, type = null) {
      /**
       * Left to Right
       * logic:
       * 1 - Right or Default : have class bg-to-right or class background-e3e3e3
       * 2 - Left or Default: Have class bg-to-left or nothing
       * Check -> If ( Right || Default) -> return
       * Check -> If (Left or Default) => return;
       */
      try {
        let defaultRight = item.classList.contains('bg-to-right') || item.classList.contains('active');
        let defaultLeft = item.classList.contains('bg-to-left') || !defaultRight;
        if (type === 4 && defaultLeft) return;
        if (defaultRight && type === 4) return this.slideLeft(item, updateFlag, type);
        if (defaultLeft) {
          if (type === 3) {
            await new Promise((resolve) => setTimeout(resolve, 50)); // last -> remove bg
            item.classList?.remove('bg-to-left');
            await new Promise((resolve) => setTimeout(resolve, 100)); // then -> add bg-to-left
            item.classList.add('bg-to-right'); // Avoid multiple class
            await new Promise((resolve) => setTimeout(resolve, 150)); // 1st -> doing remove bg-to-left
            item.classList?.remove('active'); // Tranfer animation
            return;
          } else {
            await new Promise((resolve) => setTimeout(resolve, 0)); // 1st -> doing remove bg-to-left
            item.classList?.remove('active'); // Tranfer animation
            await new Promise((resolve) => setTimeout(resolve, 50)); // last -> remove bg
            item.classList?.remove('bg-to-left');
            await new Promise((resolve) => setTimeout(resolve, 100)); // then -> add bg-to-left
            item.classList.add('bg-to-right'); // Avoid multiple class
            return;
          }
        }
      } catch (err) {
        // console.log(err);
      } finally {
        if (updateFlag) {
          await new Promise((resolve) => setTimeout(resolve, 0));
          this.updateNotiSuccess = {};
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.custom-checkbox-c {
  input {
    width: 82px;
    height: 63px;
    top: -21px;
    left: -31px;
    z-index: 1 !important;
    cursor: pointer !important;
  }
}
.custom-checkbox-gmail {
  width: 19px;
  height: 18px;
  background: url(~@/assets/template/images/checkbox-i01-s02.svg) no-repeat !important;
  top: calc(50% - 7px);
}
.btn--arrow {
  position: absolute !important;
  top: -2px;
  left: -5px;
}
.checkAll {
  padding-left: unset;
  display: flex;
  justify-content: center;
}
.custom-tbody {
  td {
    &:nth-child(1) {
      text-align: center;
    }
  }
}
.custom-checkbox-k1 {
  cursor: pointer;
  &:hover {
    .abc {
      display: block;
    }
  }
}
.checkAll-drop {
  &.show {
    &:after {
      position: absolute;
      top: -4px;
      right: -24px;
      content: '';
      display: block;
      height: 28px;
      width: 48px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 4px;
    }
  }
}
.checkAll1 {
  width: 49px;
  height: 28px;
  background: rgba(255, 255, 255, 0.5);
  position: absolute;
  left: -6px;
  top: -4px;
  border-radius: 4px;
  opacity: 0.9;
}
.abc {
  width: 30px;
  height: 28px;
  background: rgba(255, 255, 255, 0.5);
  display: none;
  position: absolute;
  left: -6px;
  top: -6px;
  border-radius: 2px;
  opacity: 0.9;
}
.abcde {
  top: -4px;
}
.checkAll-drop2 {
  &:hover {
    .abcd {
      display: block;
    }
  }
  .abcd {
    width: 18px;
    height: 28px;
    background: rgba(255, 255, 255, 0.5);
    display: none;
    border-radius: 2px;
    position: absolute;
    left: 6px;
    opacity: 0.9;
    top: -4px;
  }
}
.facilityContent {
  height: 100%;
  overflow: hidden;
  padding: 0 24px 6px;
}

.changeReadFlag {
  font-weight: bold;
}
.pagination-cs {
  box-shadow: inset 0px 7px 12px #e3e3e3;
}
.box-td {
  text-align: center;
  .title-cate {
    span {
      background: #daf8dc;
      border-radius: 12px;
      padding: 4px 20px 5px 20px;
      color: #28a470;
      font-weight: 500;
      font-size: 14px;
    }
  }
}
.background-e3e3e3 {
  background: #e3e3e3;
}
.changeBgr {
  background: #fff !important;
  box-shadow: inset -1px 0px 0px rgba(0, 0, 0, 0.05);
}
.nochangeBgr {
  background: #e3e3e3 !important;
  box-shadow: inset -1px 0px 0px rgba(0, 0, 0, 0.05);
}
.btnInfo-btn {
  button {
    width: unset !important;
    padding: 7px !important;
  }
}
.btnInfo-btn2 {
  height: 42px !important;
}
.showFilter {
  display: block;
}
.noneButton {
  display: none;
}
.background {
  background: #448add !important;
}
.custom-tr {
  padding: 0 !important;
  background: #ffffff;
  .indicator {
    text-align: start !important;
    padding: unset !important;
  }
}
.custom-table {
  min-width: 84px !important;
}
.custom-table-indx2 {
  width: 200px;
  text-align: center;
}
.custom-category-name {
  padding: 2px 15px !important ;
  width: 63%;
  margin-left: 32px;
  text-align: center;
}
$viewport_tablet: 991px;
.tbl-notifi {
  min-height: 100%;
  table {
    box-shadow: 0 0 8px #e3e3e3;
    -moz-border-radius: 10px 10px 0 0;
    border-radius: 10px 10px 0 0;
  }
  thead {
    tr {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      position: -webkit-sticky;
      position: sticky;
      top: 0;
      z-index: 2;
      th {
        color: #fff;
        padding-top: 7px;
        padding-bottom: 7px;
        border-right: 1px solid #fff;
        border-bottom: none;
        vertical-align: middle;
        font-size: 14px;
        &:first-of-type {
          border-radius: 10px 0 0 0;
          width: 1rem;
          min-width: 200px;
        }
        &:last-child {
          border-radius: 0 10px 0 0;
          border: none;
          width: 1rem;
          min-width: 240px;
        }
      }
    }
  }
  tr {
    @keyframes animation-updated-color {
      to {
        background-position: right;
      }
    }
    &.deleted {
      transform: translateX(100%);
      -webkit-transform: translateX(100%);
    }
    &.updatedColorItem {
      background: linear-gradient(to left, #ffffff 50%, #e3e3e3 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }
    &.background-e3e3e3.updatedColorItem {
      background: linear-gradient(to left, #e3e3e3 50%, #ffffff 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }
    &.updatedColorItem.updatedColorItem1 {
      background: linear-gradient(to right, #ffffff 50%, #e3e3e3 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }
    &.updatedColorItem03 {
      background: linear-gradient(to left, #ffffff 50%, #e3e3e3 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }
    &.background-e3e3e3.updatedColorItem03 {
      background: linear-gradient(to left, #e3e3e3 50%, #ffffff 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }

    &.updatedColorItem1 {
      background: linear-gradient(to right, #ffffff 50%, #e3e3e3 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }
    &.background-e3e3e3.updatedColorItem1 {
      background: linear-gradient(to right, #e3e3e3 50%, #ffffff 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }
    &.updatedColorItem031 {
      background-image: linear-gradient(-90deg, #ffffff 50%, #e3e3e3 50%);
      background-size: 200%;
      animation: animation-updated-color 1s;
      animation-direction: reverse;
    }
    &.background-e3e3e3.updatedColorItem031 {
      background: linear-gradient(to right, #e3e3e3 50%, #ffffff 50%) left;
      background-size: 200%;
      animation: animation-updated-color 1s;
    }
    &.active {
      &:not(.bg-to-right),
      &:not(.bg-to-left) {
        background: #e3e3e3;
      }
    }
    .custom-td {
      position: absolute;
      width: 100%;
      height: 100%;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      margin: auto;
    }
    td {
      vertical-align: middle;
      padding-top: 10px;
      padding-bottom: 10px;

      .custom-control {
        margin-left: 8px;
        margin-right: 10px;
        padding-left: 18px;
      }
      .span-label {
        min-width: 114px;
        display: inline-block;
      }
      .titleLink {
        font-size: 16px;
        color: #448add !important;
        cursor: pointer;
      }
      .notifi-dateTime {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 90%;
        .span-date,
        .span-time {
          font-size: 16px;
          margin-right: 16px;
        }
      }
    }
  }
}
.item-rotation-360 {
  display: inline-block;
  animation: animation-item-rotation-360 0.75s linear 1;
}

.slide-right {
  animation: animation-slide-right 1s 1;
  background-color: #7bd7d3 !important;
}

@keyframes animation-item-rotation-360 {
  to {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@keyframes animation-slide-right {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(100%);
  }
}

.table-custom {
  transition: box-shadow 0.55s, background 0.55s, transform 0.55s ease;
  tbody {
    tr {
      &:after {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: unset;
        top: 0;
        left: 0;
        transition: all 0.12s ease-in-out;
        z-index: -1;
      }
      &:hover {
        box-shadow: inset 0 0 0 2px #c3c0c0;
        td {
          &:first-of-type {
            border-left: none;
          }
          &:last-child {
            border-right: none;
          }
        }
      }
    }
  }
}
table {
  border-spacing: 0 !important;
}
.body-gray {
  position: relative;
  &:before {
    position: absolute;
    content: '';
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #f2f2f2;
    z-index: -1;
  }
  tr {
    position: relative;
  }
}
.btn--icon {
  &:hover {
    border: 1px solid #448add;
  }
}
.bg-to-left {
  background: linear-gradient(to left, #e3e3e3 50%, #fff 50%) left;
  background-size: 200%;
  animation: slide-to 1s;
  animation-fill-mode: forwards;
  animation-delay: 0.08s;
}
.bg-to-right {
  background: linear-gradient(to left, #e3e3e3 50%, #fff 50%) left;
  background-size: 200%;
  animation: slide-to 1s;
  animation-fill-mode: forwards;
  animation-direction: reverse;
  animation-delay: 0.08s;
}
.active {
  background: #e3e3e3;
}
@keyframes slide-to {
  from {
    background-position: right;
  }
  to {
    background-position: left;
  }
}
</style>
