<template>
  <div v-loading="isLoading" v-load-f5="true" class="wrapContent">
    <div class="reportList">
      <div class="reportList-form">
        <div class="form-icon icon-right">
          <span class="icon log-icon" title_log="組織選択" @click="openModal_Z05_S01">
            <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
          </span>
          <div v-if="initParams.org_label" class="form-control d-flex align-items-center">
            <div class="block-tags">
              <el-tag class="m-0 el-tag-custom">
                {{ initParams.org_label }}
              </el-tag>
            </div>
          </div>
          <el-input v-else clearable readonly placeholder="" class="form-control-input" />
        </div>
      </div>
      <div class="application">
        <div class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
        </div>
        <div ref="reportList" class="reportList-tbl table-hg100 scrollbar" @scroll="onScroll">
          <table>
            <thead>
              <tr>
                <th class="borderTable" rowspan="2">ユーザ名</th>
                <th class="borderTable" style="text-align: center" :colspan="checkColSpan()">{{ initParams.mode_week ? '前月' : '前週' }}</th>
                <th colspan="7">
                  <div class="reportList-datePicker log-icon" title_log="週選択" :class="initParams.mode_week ? '' : 'min'">
                    <div class="form-icon icon-left form-icon--noBod">
                      <span class="icon">
                        <ImageSVG :src-image="'icon-calender-control.svg'" :alt-image="'icon-calender-control'" />
                      </span>
                      <el-date-picker
                        v-model="target_date"
                        class="form-control-datePickerLeft datePicker-pdr"
                        :type="initParams.mode_week ? 'month' : 'week'"
                        :format="`[${show_target_date}]`"
                        :placeholder="initParams.mode_week ? '年月選択' : '週選択'"
                        :clearable="false"
                        :editable="false"
                        @change="selectDate"
                      ></el-date-picker>
                    </div>
                  </div>
                </th>
              </tr>
              <tr>
                <th v-for="(date, index) in dailyReportList" :key="index" class="borderTable_2" :hidden="!initParams.mode_week">
                  {{ formatMonthDate(date.startDate) + '～' }}
                </th>
                <th v-for="(date, index) in calendarList" :key="index" class="borderTable_2" :hidden="initParams.mode_week">
                  {{ date.calendar_date_show }}
                  <br class="break-line-date" />
                  {{ date.calendar_day_show }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in orgList" :key="index" :class="`${!checkPermission(user) ? 'cur-user' : ''}`">
                <td>
                  <div :class="`reportList-user ${!checkPermission(user) ? 'item-user' : ''}`">
                    <div class="reportList-user__thumb">
                      <img :src="user.file_url" alt="user-avatar" />
                    </div>
                    <div class="reportList-user__name">{{ user.user_name }}</div>
                  </div>
                  <div :class="`reportList-icon ${!checkPermission(user) ? 'item-user' : ''}`">
                    <div class="reportList-icon__content">
                      <ImageSVG :src-image="'icon_filled.svg'" :alt-image="'icon_filled'" />
                      <span>{{ user.org_label }}</span>
                    </div>
                    <div class="reportList-icon__content">
                      <ImageSVG :src-image="'icon_namecard.svg'" :alt-image="'icon_namecard'" />
                      <p>{{ user.definition }}</p>
                    </div>
                  </div>
                </td>
                <td v-for="(item, indexx) in user.dailyList" :key="indexx" :class="{ holiday: item?.holiday_flag || checkUser(item) }">
                  <span v-if="item?.status" :class="`reportList-item ${!checkPermission(item) ? 'item-user' : ''}`">
                    <button type="button" class="btn-img" title_log="日報提出状況" @click="callScreen_A03_S02(item)">
                      <img
                        v-if="checkPermission(item) && item?.status === '承認済'"
                        :src="require(`@/assets/template/images/${returnIcon(item?.status)}`)"
                        alt="icon_majesticons"
                      />
                      <img
                        v-else-if="user?.aproval && (item?.status === '承認済' || item?.status === '提出済')"
                        :src="require(`@/assets/template/images/${returnIcon(item?.status)}`)"
                        alt="icon_majesticons"
                      />
                      <img v-else-if="!checkPermission(item)" :src="require(`@/assets/template/images/${returnIcon(item?.status)}`)" alt="icon_majesticons" />
                    </button>
                  </span>
                  <span v-else>&nbsp;</span>
                </td>
              </tr>
              <tr v-if="dailyReportList.length">
                <td v-if="!isLoadDefault && !orgList.length" :colspan="initParams.mode_week ? dailyReportList.length + 1 : 15">
                  <EmptyData />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="reportList-note">
        <ul>
          <li>
            <span class="item"><img class="svg" src="@/assets/template/images/icon_majesticons.svg" alt="" /></span>差戻
          </li>
          <li>
            <span class="item"><img class="svg" src="@/assets/template/images/icon_majesticons-01.svg" alt="" /></span>承認済
          </li>
          <li>
            <span class="item"><img class="svg" src="@/assets/template/images/icon_majesticons-04.svg" alt="" /></span>未作成
          </li>
          <li>
            <span class="item"><img class="svg" src="@/assets/template/images/icon_majesticons-02.svg" alt="" /></span>未提出
          </li>
          <li>
            <span class="item"><img class="svg" src="@/assets/template/images/icon_majesticons-03.svg" alt="" /></span>提出済
          </li>
        </ul>
      </div>
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closeModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import A03_S01_Service from '@/api/A03/A03_S01_JapaneseDailyReportListServices';
import avataReport from '@/assets/template/images/data/avata-reportList.png';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import moment from 'moment';

export default {
  name: 'A03_S01_JapaneseDailyReportList',
  data() {
    return {
      isLoadDefault: true,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      avataReport,
      target_date: new Date(),
      show_target_date: '',
      // org_name: '',
      initParams: {
        report_period_start_date: '',
        report_period_end_date: '',
        org_cd: '',
        org_label: '',
        mode_week: 0 // 1: mode week, 0: mode date
      },
      calendarList: [],
      orgList: [],
      dailyReportList: [],
      isLoading: false,
      user_approval_list: [],
      showScrollLeft: false,
      showScrollRight: false,
      onScrollTop: null,
      currentUser: {}
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '報告管理',
      icon: 'icon_speech-meeting-color.svg',
      isShowBack: false
    });
    const isLoadingComponent = localStorage.getItem('isLoadingComponent');
    this.currentUser = this.getCurrentUser();

    let org_login = {
      org_cd: this.currentUser?.org_cd,
      org_name: this.currentUser?.org_short_name,
      org_label: this.currentUser?.org_label
    };

    if (isLoadingComponent === 'true') {
      this.target_date = new Date();
      localStorage.setItem('target_date', new Date());
      localStorage.setItem('target_org', JSON.stringify(org_login));
      this.initParams = {
        ...this.initParams,
        org_cd: org_login?.org_cd,
        org_label: org_login?.org_label
      };
    } else {
      this.target_date = new Date(localStorage.getItem('target_date'));
      let org = JSON.parse(localStorage.getItem('target_org'));
      this.initParams = {
        ...this.initParams,
        org_cd: org?.org_cd,
        org_label: org?.org_label
      };
    }

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.A03_S01_JapaneseDailyReportList;

    this.getMode(true);
  },
  methods: {
    getCalendarFE(fromDate, toDate) {
      let startDate = moment(new Date(fromDate)).subtract(7, 'days').format('YYYY/MM/DD');
      let startTime = new Date(startDate).getTime();
      let endTime = new Date(toDate).getTime();

      let dates = [];

      while (startTime <= endTime) {
        dates.push({
          calendar_date: startDate,
          holiday_flag: 0,
          calendar_date_show: `${this.formatMonthDate(startDate)}`,
          calendar_day_show: `(${this.getDay(startDate)})`
        });
        startDate = moment(new Date(startDate)).add(1, 'days').format('YYYY/MM/DD');
        startTime = new Date(startDate).getTime();
      }

      this.calendarList = dates;
    },

    // call api
    getCalendar(params, isLoadDefault) {
      this.isLoading = true;
      A03_S01_Service.getCalendarService(params)
        .then((res) => {
          const dataRes = res.data.data;
          this.calendarList = dataRes.map((d) => ({
            ...d,
            calendar_date_show: `${this.formatMonthDate(d.calendar_date)}`,
            calendar_day_show: `(${this.getDay(d.calendar_date)})`
          }));

          if (dataRes.length === 0) {
            this.getCalendarFE(params.startDate, params.endDate);
          }

          const org_cd = this.currentUser.org_cd || '';
          this.getListOrg(this.initParams.org_cd || org_cd, isLoadDefault);
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally();
    },

    getDailyReport(params) {
      this.isLoading = true;
      A03_S01_Service.getDailyReportService(params)
        .then((res) => {
          const dataRes = res.data.data;
          this.dailyReportList = dataRes;

          if (!this.initParams.mode_week) {
            this.orgList = this.orgList.map((element) => {
              let arr = this.calendarList.map((item) => {
                let obj = {
                  ...item,
                  report_id: null,
                  report_period_start_date: item.calendar_date,
                  report_period_end_date: item.calendar_date, // mode_week === false => startDate = endDate
                  status: this.checkPermission(element) ? '' : !this.checkdate(item.calendar_date) ? '未作成' : '',
                  user_cd: element.user_cd,
                  user_name: element.user_name,
                  org_cd: element.org_cd,
                  org_name: element.org_name,
                  org_label: element.org_label
                };
                this.dailyReportList.forEach((el) => {
                  if (element.user_cd === el?.user_cd && el.report_period_start_date === item.calendar_date) {
                    obj = {
                      ...obj,
                      ...el
                    };
                  }
                });
                return obj;
              });
              return {
                ...element,
                dailyList: arr
              };
            });
          } else {
            this.orgList = this.orgList.map((element) => {
              let arr = this.dailyReportList.map((item) => {
                let obj = {
                  ...item,
                  report_id: null,
                  report_period_start_date: item.startDate,
                  report_period_end_date: item.endDate,
                  status: this.checkPermission(element) ? '' : !this.checkdate(item.startDate) ? '未作成' : '',
                  user_cd: element.user_cd,
                  user_name: element.user_name,
                  org_cd: element.org_cd,
                  org_name: element.org_name,
                  org_label: element.org_label
                };
                if (item.list_report.length > 0)
                  item.list_report.forEach((el) => {
                    if (element.user_cd === el?.user_cd) {
                      obj = {
                        ...obj,
                        ...el
                      };
                    }
                  });
                return obj;
              });
              return {
                ...element,
                dailyList: arr
              };
            });
          }
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;
          let content = document.querySelector('.reportList-tbl');
          content.scrollLeft = 1;
        });
    },
    async getListOrg(params, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.isLoading = true;
      A03_S01_Service.getListOrgService({ org_cd: params })
        .then(async (res) => {
          const dataRes = res.data.data;
          this.orgList = [];
          let listImg = dataRes.map((item) =>
            fetch(item.file_url).then((resp) => {
              // eslint-disable-next-line eqeqeq
              if (resp.ok && resp.status == 200) {
                return {
                  ...item,
                  file_url: item.file_url ? item.file_url : this.avataDefault(),
                  id: item.user_cd,
                  aproval: this.getUserAporval(item),
                  res: { ...resp }
                };
              } else {
                return {
                  ...item,
                  file_url: this.avataDefault(),
                  id: item.user_cd,
                  aproval: this.getUserAporval(item),
                  res: resp
                };
              }
            })
          );
          await Promise.all(listImg).then(async (res) => {
            let data = await res;
            this.orgList = data;
          });

          this.getDailyReport(this.initParams);
        })
        .catch((err) => {
          this.orgList = [];
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          if (this.$refs.reportList) {
            this.$refs.reportList.scrollTop = this.onScrollTop;
          }
          this.js_pscroll();
          this.isLoadDefault = false;
        });
    },
    getMode(isLoadDefault) {
      this.isLoading = true;
      A03_S01_Service.getModeService()
        .then((res) => {
          const dataRes = res.data.data;
          this.initParams.mode_week = dataRes.mode_week;
          this.user_approval_list = dataRes.user_approval;

          const org_cd = this.initParams.org_cd || this.currentUser.org_cd || '';
          const org_label = this.initParams.org_label || this.currentUser.org_label || '';

          this.initParams = {
            ...this.initParams,
            ...this.getDate(),
            org_cd: org_cd,
            org_label: org_label
          };
          if (this.initParams.mode_week) {
            this.getListOrg(this.initParams.org_cd, isLoadDefault);
            this.showScrollRight = false;
          } else {
            this.getCalendar(this.getDate(), isLoadDefault);
          }
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          localStorage.setItem('isLoadingComponent', false);
        });
    },
    // define function
    getUserAporval(user) {
      let flg = false;
      if (this.user_approval_list.length) flg = this.user_approval_list.some((el) => el.request_user_cd === user.user_cd);
      return flg;
    },
    onScrollLeft() {
      let content = document.querySelector('.reportList-tbl');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.reportList-tbl');
      content.scrollLeft += 200;
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
        let content = document.querySelector('.reportList-tbl');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    handleClose() {
      this.getDailyReport(this.initParams);
    },
    checkColSpan() {
      if (this.initParams.mode_week) {
        let count = 0;
        const month = moment(new Date(this.dailyReportList[0]?.startDate)).format('MM');
        this.dailyReportList.forEach((el) => {
          if (moment(new Date(el.startDate)).format('MM') === month) count++;
        });
        return count;
      }
      return 7;
    },
    checkUser(item) {
      let flg_holiday = false;
      if (this.getUserAporval(item) && (item.status === '提出済' || item.status === '承認済') && item.vacation_flg) {
        flg_holiday = true;
      } else if (!this.checkPermission(item) && item.vacation_flg) {
        flg_holiday = true;
      } else if (item.status === '承認済' && item.vacation_flg) {
        flg_holiday = true;
      }
      return flg_holiday;
    },
    returnIcon(id) {
      const obj = {
        差戻: 'icon_majesticons.svg',
        承認済: 'icon_majesticons-01.svg',
        未提出: 'icon_majesticons-02.svg',
        提出済: 'icon_majesticons-03.svg',
        未作成: 'icon_majesticons-04.svg'
      };
      return obj[id];
    },

    selectDate(date) {
      if (!date) return;
      const _date = new Date(date);
      let params = {};
      if (this.initParams.mode_week) {
        params = {
          startDate: moment(_date).startOf('month').format('YYYY/M/D'),
          endDate: ''
        };
      } else {
        params = {
          startDate: this.formatFullDate(_date.setDate(_date.getDate())),
          endDate: this.formatFullDate(_date.setDate(_date.getDate() + 6))
        };
      }
      this.target_date = date;
      localStorage.setItem('target_date', date);
      this.initParams = {
        ...this.initParams,
        report_period_start_date: params.startDate,
        report_period_end_date: params.endDate
      };
      this.setValueCalendar(params);
      this.getCalendar(params, false);
    },

    getDate() {
      let curr = new Date(this.target_date);
      let week = [];
      let obj = {};

      for (let i = 0; i < 7; i++) {
        let first = curr.getDate() - curr.getDay() + i;
        let day = moment(new Date(curr.setDate(first))).format('YYYY/MM/DD');
        week.push(day);
      }

      let startDate = week[0];
      let endDate = week[6];

      if (this.initParams.mode_week) {
        obj = {
          startDate: moment(new Date(curr)).startOf('month').format('YYYY/M/D'),
          endDate: ''
        };
      } else {
        obj = {
          startDate: this.formatFullDate(startDate),
          endDate: this.formatFullDate(endDate)
        };
      }

      this.setValueCalendar(obj);
      return {
        ...obj,
        report_period_start_date: obj.startDate,
        report_period_end_date: obj.endDate
      };
    },
    setValueCalendar(objDate) {
      this.show_target_date = this.initParams.mode_week
        ? `${moment(new Date(objDate.startDate)).format('M')}月`
        : `${this.formatMonthDate(objDate.startDate)} ～ ${this.formatMonthDate(objDate.endDate)}`;
    },
    checkPermission(item) {
      const userLogin = this.currentUser.user_cd;
      return item.user_cd !== userLogin;
    },
    // future date check
    checkdate(inputDate) {
      const crDate = moment(new Date().getTime());
      const inDate = moment(new Date(inputDate).getTime());
      return inDate > crDate;
    },
    // modal
    closeModal(data) {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      if (data?.orgSelected?.length > 0) {
        let org = {
          org_cd: data.orgSelected[0].org_cd,
          org_label: data.orgSelected[0].org_label,
          org_name: data.orgSelected[0].org_name
        };
        this.initParams = {
          ...this.initParams,
          org_cd: data.orgSelected[0].org_cd,
          org_label: data.orgSelected[0].org_label
        };

        this.setScrollScreen('A03_S01_JapaneseDailyReportList', 0);
        localStorage.setItem('target_org', JSON.stringify(org));
        this.getMode(false);
      }
    },
    openModal_Z05_S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        component: this.markRaw(Z05_S01_ModalOrganization),
        width: '45rem',
        props: {
          mode: 'single',
          userFlag: 0,
          userSelectFlag: 1,
          userCdList: [],
          orgCdList: [this.initParams.org_cd]
        }
      };
    },
    callScreen_A03_S02(item) {
      let scrollTop = this.$refs.reportList ? this.$refs.reportList.scrollTop : 0;

      this.setScrollScreen('A03_S01_JapaneseDailyReportList', scrollTop);
      this.$router.push({
        name: 'A03_S02_DailyReport',
        params: {
          ...item,
          reportId: item.report_id,
          startDate: item.report_period_start_date,
          endDate: item.report_period_end_date
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.reportList {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding: 20px 24px 10px;
  .reportList-form {
    max-width: 250px;
    padding-bottom: 10px;
  }
  .application {
    position: relative;
    overflow: hidden;
    .application-btn {
      .btn {
        position: absolute;
        top: calc(50% - 23px);
        height: 46px;
        width: 52px;
        padding: 0;
        background: rgba(63, 75, 86, 0.4);
        z-index: 1;
        &.btn--prev {
          left: 232px;
          border-radius: 0px 60px 60px 0px;
        }
        &.btn--next {
          right: 0;
          border-radius: 60px 0px 0px 60px;
          z-index: 5;
          .svg {
            transform: rotate(-180deg);
          }
        }
      }
    }
    .reportList-tbl {
      tr {
        th,
        td {
          text-align: center;
          vertical-align: middle;
          min-width: 48px;
          max-width: 48px;
        }
      }
      thead {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 2;
        tr {
          &:first-of-type {
            th {
              border-bottom: 1px solid #f9f4f4;
              font-size: 1rem;
              font-weight: 700;
              text-align: left;
              &:nth-child(1) {
                width: 1rem;
                min-width: 232px;
              }
              &:first-of-type {
                border-bottom: none;
                position: -webkit-sticky;
                position: sticky;
                top: 0;
                left: 0;
                z-index: 3;
                background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
                background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
              }
            }
          }
          th {
            color: #fff;
            padding-top: 7px;
            padding-bottom: 7px;
            border-bottom: none;
            vertical-align: middle;
            font-size: 0.875rem;
            padding: 5px;
            font-weight: normal;
            &:last-child {
              border-right: none;
            }
            .reportList-datePicker {
              max-width: 100px;
              margin: 0 auto;
              &.min {
                max-width: 145px;
              }
            }
          }
          th {
            position: relative;
            &.borderTable {
              &::after {
                position: absolute;
                content: '';
                width: 1px;
                background-color: #fff;
                height: calc(100% - 10px);
                top: 5px;
                right: -1px;
              }
              &:nth-child(2) {
                &::after {
                  height: calc(100% - 5px);
                }
              }
            }
            &.borderTable_2 {
              &::after {
                position: absolute;
                content: '';
                width: 1px;
                background-color: #fff;
                height: calc(100% - 10px);
                top: 5px;
                right: 0;
              }
              &:nth-child(5) {
                &::after {
                  top: 0;
                  height: calc(100% - 5px);
                }
              }
              &::before {
                position: absolute;
                content: '';
                width: 100%;
                height: 1px;
                background-color: #fff;
                top: -1px;
                right: 0;
              }
              &:last-child::after {
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
            position: relative;
            z-index: 0;
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
            &:nth-child(1) {
              position: sticky;
              z-index: 1;
              left: 0;
              background-color: #fff;
              &::before {
                position: absolute;
                content: '';
                width: 1px;
                background-color: #d5d5d5;
                height: 100%;
                top: 0px;
                right: -1px;
              }
            }
            padding-top: 5px;
            padding-bottom: 5px;
            &:nth-child(1) {
              text-align: left;
            }
            .reportList-icon,
            .reportList-user,
            .reportList-item {
              opacity: 0.5;
              z-index: 9;
              cursor: pointer;
              .disabled:hover {
                cursor: not-allowed;
              }
            }
            .item-user {
              opacity: 1;
            }
            .reportList-user {
              display: flex;
              align-items: center;
              flex-wrap: wrap;
              .reportList-user__thumb {
                width: 24px;
                height: 24px;
                overflow: hidden;
                border-radius: 50%;
                border: 1px solid #cdced9;
                img {
                  width: 100%;
                }
              }
              .reportList-user__name {
                width: calc(100% - 24px);
                max-width: 200px;
                padding-left: 10px;
                font-size: 1rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
              }
            }
            .reportList-icon {
              .reportList-icon__content {
                display: flex;
                align-items: center;
                span {
                  margin-left: 5px;
                }
                p {
                  margin-left: 3px;
                }
              }
            }
          }
        }
        tr {
          td {
            &:nth-child(2),
            &:nth-child(3),
            &:nth-child(4),
            &:nth-child(5),
            &:nth-child(6),
            &:nth-child(7),
            &:nth-child(8) {
              background-color: #f9f9f9;
            }
          }
        }
        tr.cur-user {
          td {
            &:nth-child(2),
            &:nth-child(3),
            &:nth-child(4),
            &:nth-child(5),
            &:nth-child(6),
            &:nth-child(7),
            &:nth-child(8) {
              background-color: #f2f2f2;
            }
          }
        }
      }
    }
  }

  .reportList-note {
    padding-top: 13px;
    ul {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      li {
        display: flex;
        align-items: center;
        font-size: 1rem;
        margin-right: 30px;
        &:last-child {
          margin-right: 0;
        }
        .item {
          min-width: 14px;
          max-width: 14px;
          margin-right: 9px;
          margin-top: -2px;
        }
      }
    }
  }
}

.break-line-date {
  display: none;
  @media (max-width: 1365) {
    display: block;
  }
}

.btn-img {
  width: 29px;
  height: 33px;
  @media (max-width: 1080px) {
    width: 24px;
    height: 27px;
  }
}
</style>
