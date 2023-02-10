<template>
  <div class="bgBox bgBox--change">
    <div class="title">
      <div class="title-info">
        <span class="title-item"><img src="@/assets/template/images/icon_calender.svg" alt="" /></span>
        <h2 class="title-tlt">活動計画　{{ getTime('month') }}月{{ getTime('day') }}日</h2>
      </div>
    </div>
    <div ref="activityPlan" class="activityPlan scrollbar" @scroll="setScroll">
      <ul>
        <li v-for="(item, index) of activityPlans" :key="index">
          <p v-if="formatTimeHour(item.start_time) !== '0:00' && formatTimeHour(item.end_time) !== '23:59'" class="plan-date">
            {{ formatTimeHour(item.start_time) }}～{{ formatTimeHour(item.end_time) }}
          </p>
          <p v-else class="plan-date" style="text-align: left; padding-left: 0">終日</p>

          <div class="plan-content">
            <div class="plan-col-12">
              <div class="plan-icon">
                <!--10 Call-->
                <span v-show="item.schedule_type == '10'" class="plan-item plan-item-10">
                  <img class="min-width" src="@/assets/template/images/icon_home_calendar.svg" alt="" />
                </span>
                <!--40 Office_schedule-->
                <span v-show="item.schedule_type == '40'" class="plan-item type_40"><img src="@/assets/template/images/icon_home_org.svg" alt="" /></span>
                <!--50 Priavte-->
                <span v-show="item.schedule_type == '50'" class="plan-item type_50">
                  <img src="@/assets/template/images/icon_home_account.svg" alt="" />
                </span>
                <!--20 Briefing-->
                <span v-show="item.schedule_type == '20'" class="plan-item type_20">
                  <img src="@/assets/template/images/icon_interview-daily02.svg" alt="" />
                </span>
                <!--30 Convention-->
                <span v-show="item.schedule_type == '30'" class="plan-item type_30">
                  <img src="@/assets/template/images/icon_interview-daily01.svg" alt="" />
                </span>
              </div>

              <div class="plan-textLabel">
                <p v-show="item.schedule_type != '40' && item.schedule_type != '50'">{{ item.title }}</p>
                <a v-if="item.schedule_type == '10'" class="link" @click="callScreenA01_S04(item, 'call_list')">
                  <div v-show="item.call_list > 0">
                    <span> {{ item.call_attendee?.facility_short_name }} {{ item.call_attendee?.person_name }}{{ item.call_attendee?.department_name }} </span>
                    <span v-if="item.call_list > 1"> 他{{ item.call_list - 1 }}名 </span>
                  </div>
                </a>
                <a v-if="item.schedule_type == '40'" class="link" @click="goModal(item, 'office_schedule_list')">
                  <div v-show="item.office_schedule_list.length > 0">
                    <span>{{ item.office_schedule_list[0]?.title }}</span>
                  </div>
                </a>

                <a v-if="item.schedule_type == '50'" class="link" @click="goModal(item, 'private_list')">
                  <div v-show="item.private_list.length > 0">
                    <span>{{ item.private_list[0]?.title }}</span>
                  </div>
                </a>

                <a v-if="item.schedule_type == '20'" class="link" @click="goRoute(item, 'briefing_list')">
                  <div v-show="item.briefing_list > 0">
                    <span>
                      {{ item.briefing_attendee?.facility_short_name }}
                      {{ item.briefing_attendee?.person_name }} {{ item.briefing_attendee?.department_name }}
                    </span>
                    <span v-if="item.briefing_list > 1"> 他{{ item.briefing_list - 1 }}名 </span>
                  </div>
                </a>

                <a v-if="item.schedule_type == '30'" class="link" @click="goRoute(item, 'convention_list')">
                  <div v-show="item.convention_list > 0">
                    <span>
                      {{ item.convention_attendee?.facility_short_name }}
                      {{ item.convention_attendee?.person_name }} {{ item.convention_attendee?.department_name }}
                    </span>
                    <span v-if="item.convention_list > 1" style="margin-left: 10px"> 他{{ item.convention_list - 1 }}名 </span>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </li>
      </ul>
      <EmptyData v-if="activityPlans.length === 0" :style="{ height: '0px' }" custom-class="custom-class-no-data w-100" />
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog modal-fixed"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="true"
    @close="closeModal"
  >
    <template #default>
      <component
        :is="modalConfig.component"
        v-bind="{ ...modalConfig.props }"
        @onFinishScreen="reloadAction(closeModal, 'reload')($event)"
        @handleConfirm="closeModal"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import A01_S03_ModalPrivate from '@/views/A01/A01_S03_InterviewDetails/A01_S03_ModalPrivate.vue';
import A01_S03_ModalInHouseSchedule from '@/views/A01/A01_S03_InterviewDetails/A01_S03_ModalInHouseSchedule.vue';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';
import { Auth } from '@/api';

export default {
  name: 'Z02_S01_ActivityPlan',
  components: {
    A01_S03_ModalPrivate,
    A01_S03_ModalInHouseSchedule
  },
  props: {
    data: [Object || Array],
    loadingplan: [Boolean]
  },
  data() {
    return {
      loader: true,
      activityPlans: [],
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      currentUser: '',
      roleR10: false
    };
  },
  watch: {
    data() {
      this.activityPlans = this.data;
      setTimeout(() => {
        if (this.$refs.activityPlan) {
          this.$refs.activityPlan.scrollTop = 1;
        }
      }, 100);
    },
    loadingActivityPlan(load) {
      this.loader = load;
    }
  },
  mounted() {
    this.checkPermission();
    this.js_pscroll();
    if (this.roleR10) {
      this.getActivitiPlans();
      setTimeout(() => {
        if (this.$refs.activityPlan) {
          this.$refs.activityPlan.scrollTop = 1;
        }
      }, 50);
    }
  },

  methods: {
    async checkPermission() {
      const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
      this.currentUser = currentUserProxy ? currentUserProxy?.user_cd : JSON.parse(localStorage.getItem('currentUser'))?.user_cd;
      const permission = await Auth.getPolicyRoleService({ user_cd: this.currentUser });
      if (permission?.data?.data?.includes('R10')) {
        this.roleR10 = true;
      } else {
        this.roleR10 = false;
      }
    },
    getActivitiPlans() {
      this.activityPlans = this.data;
      if (this.activityPlans.length === 0 && !this.loadingplan) {
        setTimeout(() => {
          this.getActivitiPlans();
        }, 500);
      } else {
        this.loader = false;
      }
    },
    // 10 : A01_S03 , query: schedule_id=4142
    // 30 : C01-S02
    // 20 : B01-S2
    formatTimeHour(date) {
      return this.formatTimeHourMinute(date);
    },
    getTitle(data, type) {
      return ['10', '20', '30'].includes(data.schedule_type) ? this.fecthTitle(data, type) : this.fecthTitle_2(data, type);
    },
    // link, 10, 20, 30
    fecthTitle(datas, type) {
      const item = datas[type];

      const nameOther = `他${item?.length - 1}名`;
      if (item.length > 0) {
        if (item?.length === 1) {
          if (item[0].call_id === null) {
            return item[0].facility_short_name;
          } else {
            const person_name = item[0].person_name ? item[0].person_name : '';
            const department_name = item[0].department_name ? item[0].department_name : '';
            const fullName = person_name + ' ' + department_name;
            return item[0].facility_short_name + ' ' + fullName;
          }
        }
        if (item?.length > 1) {
          const person_name = item[0].person_name ? item[0].person_name : '';
          const department_name = item[0].department_name ? item[0].department_name : '';
          const fullName = person_name + ' ' + department_name;
          return item[0].facility_short_name + ' ' + fullName + ' ' + nameOther;
        }
      }
    },
    setScroll({ target: { scrollTop, clientHeight, scrollHeight } }) {
      if (this.$refs.activityPlan && this.$refs.activityPlan.scrollTop < 10) {
        this.$refs.activityPlan.scrollTop = 1;
      }
      if (this.$refs.activityPlan && scrollTop === scrollHeight - clientHeight) {
        this.$refs.activityPlan.scrollTop = scrollTop - 1;
      }
    },
    // modal , 40, 50
    fecthTitle_2(item, type) {
      if (Object.keys(item).includes(type)) {
        let title = '';
        if (item[type] && item[type].length > 0) {
          title = item[type][0].title;
        }

        return title;
      }
    },

    goModal(item, type) {
      if (Object.keys(item).includes(type)) {
        if (item[type] && item[type].length > 0) {
          const schedule_id = item.schedule_id;
          if (type === 'private_list') {
            this.openModalA01S03Private(schedule_id);
          } else {
            this.openModalA01S03Office(schedule_id);
          }
        }
      }
    },
    getTime(type) {
      return type === 'month' ? this.formatMonth(new Date().toLocaleDateString()) : this.formatDate(new Date().toLocaleDateString());
    },
    goRoute(item, type) {
      this.redirectTo(item, type);
    },
    redirectTo(item, type) {
      const schedule_id = item.schedule_id;
      const call_id = item.call_attendee?.call_id;
      if (type === 'call_list') {
        this.$router.push({ name: 'A01_S04_InterviewDetailedInput', params: { call_id: call_id, schedule_id: schedule_id } });
      } else if (type === 'briefing_list') {
        this.$router.push({ name: 'B01_S02_BriefingSessionInput', params: { schedule_id } });
      } else if (type === 'convention_list') {
        this.$router.push({ name: 'C01_S02_LectureInput', params: { schedule_id } });
      }
    },

    callScreenA01_S04(item, type) {
      let params = {
        call_id: item.call_attendee?.call_id,
        schedule_id: item.schedule_id
      };
      A01_S04_Service.checkInterviewDetailInput(params)
        .then(() => {
          this.redirectTo(item, type);
        })
        .catch(() => {
          this.$notify({ message: '面談情報が削除されたため、詳細を見ることができません。', customClass: 'error' });
        })
        .finally(() => {});
    },

    // Private
    openModalA01S03Private(schedule_id) {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'プライベート',
        isShowModal: true,
        component: markRaw(A01_S03_ModalPrivate),
        props: {
          scheduleId: schedule_id,
          isEditFlag: 0
        },
        width: '44rem',
        destroyOnClose: true
      };

      // this.changeTrueClassHeader();
    },
    // Office Modal
    openModalA01S03Office(schedule_id) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '社内予定',
        isShowModal: true,
        component: markRaw(A01_S03_ModalInHouseSchedule),
        props: {
          scheduleId: schedule_id,
          isEditFlag: 0
        },
        width: '44rem',
        destroyOnClose: true
      };

      // this.changeTrueClassHeader();
    },

    closeModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    }
  }
};
</script>

<style lang="scss" scoped>
@import '../../../../assets/css/_animations.scss';
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_mobile: 767px;
.page-zo {
  .navTop {
    > ul {
      margin-left: -2.5%;
      display: flex;
      flex-wrap: wrap;
      // z-index: 9;
      position: relative;
      transition: all 1s;

      > li {
        width: 25%;
        padding-left: 2.5%;
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        @media (max-width: $viewport_tablet) {
          width: 50%;
        }
      }
    }
  }
  .btnNav {
    width: 100%;
    border-radius: 16px;
    box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
    position: relative;
    transition: all 0.5s;
    &.show {
      z-index: 1;
      .dropdown-menu--nav {
        z-index: 1;
      }
    }
    &:hover {
      transform: scale(1.05);
      z-index: 1;
      .btnNav-content {
        .btnNav-info {
          .subject {
            span {
              &:after {
                @include widget-icon-caret-down-shake;
              }
              &:hover {
                &:after {
                  transform: scale(1.2);
                }
              }
            }
          }
        }
      }
    }

    &--red {
      background: #ff728b;
    }
    &--yellow {
      background: #ffca42;
    }
    &.show {
      .btnNav-content {
        .btnNav-info {
          .subject {
            span {
              &:after {
                transform: scale(1.2) rotate(180deg);
              }
              &:hover {
                &:after {
                  transform: scale(1.2) rotate(180deg);
                }
              }
            }
          }
        }
      }
    }
    .btnNav-content {
      position: relative;
      padding: 16px 28px;
      min-height: 91px;
      height: 100%;
      overflow: hidden;
      border-radius: 16px;
      @media (max-width: $viewport_desktop) {
        padding: 16px;
      }
      cursor: pointer;
      .btnNav-item {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 84px;
        text-align: right;
      }
      .btnNav-info {
        color: #fff;
        .tlt {
          font-size: 16px;
          font-weight: 700;
        }
        .subject {
          font-size: 24px;
          font-weight: 700;
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
              border-top: 8px solid #fff;
              transition: 400ms all;
            }
          }
        }
      }
    }
    .dropdown-menu--nav {
      margin: 0;
      width: 100%;
      box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
      border-radius: 16px;
      border: none;
      padding: 15px 24px 15px 24px;
      overflow-y: auto;
      max-height: 200px;
      z-index: 1;
      ul {
        li {
          a {
            font-size: 14px;
            padding: 2px 0;
            display: block;
          }
        }
      }
    }
  }
  .groupHome {
    padding-bottom: 20px;
  }
  .wrapColumn {
    display: flex;
    flex-wrap: wrap;
    margin-left: -2.5%;
    .wrapColumn-col4,
    .wrapColumn-col5,
    .wrapColumn-col6 {
      padding-left: 2.5%;
    }
    .wrapColumn-col5 {
      width: 50%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
    .wrapColumn-col4 {
      width: 40%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
    .wrapColumn-col6 {
      width: 60%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
  }
  .bgBox {
    background: #fff;
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    border-radius: 16px;
    margin-top: 20px;
    padding: 15px 24px;
    &.bgBox--change {
      padding: 0px 0px 12px 0px;
      .title {
        padding: 21px 22px 20px;
        box-shadow: 0px 3px 6px #e3e3e3;
      }
    }
    .title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      .title-info {
        display: flex;
        align-items: center;
        .title-item {
          min-width: 30px;
          margin-right: 5px;
        }
        .title-tlt {
          font-weight: bold;
          font-size: 18px;
        }
      }
    }
  }
  .navTabs-main {
    ul {
      display: flex;
      flex-wrap: wrap;
      li {
        margin-left: -1px;
        &:first-of-type {
          a {
            border-radius: 4px 0 0 4px;
          }
        }
        &:last-child {
          a {
            border-radius: 0 4px 4px 0;
          }
        }
        a {
          padding: 9px 24px;
          color: #5f6b73;
          border: 2px solid #727f88;
          display: block;
          &.active {
            position: relative;
            background: #eef6ff;
            border: 2px solid #448add;
            color: #448add;
            font-weight: 700;
          }
        }
      }
    }
  }
  .lstMessage {
    padding: 0 24px;
    max-height: 200px;
    overflow-y: auto;
    ul {
      li {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #e3e3e3;
        padding: 12px 0;
        &:last-child {
          border-bottom: none;
        }
      }
    }
    .lstMessage-content {
      .lstMessage-tlt {
        font-size: 16px;
        font-weight: 700;
        a {
          color: #448add;
        }
        .lstMessage-has {
          background: #ff746b;
          width: 10px;
          height: 10px;
          border-radius: 50%;
          display: inline-block;
          margin-left: 5px;
          margin-top: 6px;
        }
      }
      .lstMessage-label {
        margin-top: 4px;
        color: #5f6b73;
      }
    }
    .lstMessage-info {
      text-align: right;
      .lstMessage-date {
        color: #5f6b73;
      }
    }
  }
  .paginMessage {
    padding: 8px 24px;
    box-shadow: 0px -3px 6px #e3e3e3;
    display: flex;
    justify-content: flex-end;
    ul {
      li {
        margin-left: -1px;
        border: 1px solid #f2f2f2;
        &:first-of-type {
          border-radius: 10px 0 0 10px;
        }
        &:last-child {
          border-radius: 0 10px 10px 0;
        }
      }
    }
  }
  .noData {
    height: 622px !important;
  }
  .activityPlan {
    height: 622px;
    overflow: auto !important;
    padding: 0 10px 0 18px;
    &::-webkit-scrollbar {
      display: none;
      width: 8px;
      background-color: #f2f2f2;
      border-radius: 2.5px;
    }
    &::-webkit-scrollbar-thumb {
      background-color: #b7c3cb;
      border-radius: 5px;
    }
    ul {
      padding-right: 8px;
      li {
        display: flex;
        align-items: center;
        box-shadow: inset 0px -1px 0px #e3e3e3;
        border-bottom: 1px solid #e3e3e3;

        &:last-child {
          border-bottom: none;
          padding-bottom: 0;
        }
      }
    }
    .plan-date {
      color: #5f6b73;
      font-size: 16px;
      font-weight: bold;
      width: 120px;
      margin-left: 12px;
    }
    .plan-content {
      display: flex;
      flex-wrap: wrap;
      margin-left: -2%;
      width: 80%;
      padding-left: 30px;
      padding-bottom: 20px;
      padding-top: 20px;
      position: relative;
      padding-right: 15px;
      &::before {
        position: absolute;
        box-shadow: inset -1px 0px 0px #e3e3e3;
        content: '';
        top: 0;
        left: 0;
        height: 100%;
        width: 10px;
      }
      .plan-col-1,
      .plan-col-2,
      .plan-col-3 {
        padding-left: 2%;
      }
      .plan-col-1 {
        width: 30%;
      }
      .plan-col-2 {
        width: 25%;
      }
      .plan-col-3 {
        width: 45%;
        display: flex;
        align-items: center;
      }
      .plan-col-12 {
        display: flex;
        align-items: center;
        width: 100%;
        .plan-icon {
          width: 33px;
        }
      }
      .plan-item {
        margin-right: 22px;
        img {
          width: 24px;
        }
        .min-width {
          width: 20px !important;
        }
      }
      .plan-info {
        display: flex;
        .plan-item {
          min-width: 20px;
          margin-right: 4px;
        }
        .plan-text {
          font-weight: 500;
          font-size: 16px;
        }
      }
      .plan-textLabel {
        font-size: 14px;
        width: 90%;
        .link {
          color: #0d8add;
          &:hover {
            cursor: pointer;
            color: #0d8add;
          }
        }
        a {
          font-weight: normal;
          font-size: 14px;
          color: #67748e;

          &:hover {
            cursor: pointer;
            text-decoration: unset;
            color: #67748e;
          }
        }
      }
    }
  }
  .titleChart {
    display: flex;
    justify-content: space-between;
    padding-top: 9px;
    .titleChart-info {
      .titleChart-tlt {
        color: #252f40;
        font-size: 16px;
        font-weight: 700;
      }
      .titleChart-more {
        margin-top: 8px;
        a {
          color: #67748e;
          font-weight: 600;
          img {
            margin-right: 4px;
          }
        }
        span {
          color: #252f40;
          padding-left: 6px;
        }
      }
      .titleChart-lastWeek {
        margin-top: 8px;
        .titleChart-number {
          color: #7f8fa4;
          font-weight: 700;
          padding-right: 6px;
        }
      }
    }
  }
  .barChart {
    margin-top: 6px;
    img {
      width: 100%;
    }
  }
  .toolsChart {
    display: flex;
    margin-top: 24px;
    justify-content: center;
    .toolsChart-check {
      width: calc(100% - 71px);
      &.toolsChart-check--col4 {
        ul {
          li {
            width: 25%;
          }
        }
      }
      &.toolsChart-check--col5 {
        ul {
          li {
            width: 19.5%;
          }
        }
      }
      ul {
        display: flex;
        li {
          padding: 8px 2px;
          margin-left: -1px;
          text-align: center;
          color: #5f6b73;
          border: 1px solid #727f88;
          position: relative;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          &.active {
            border: 2px solid #448add;
            color: #448add;
            font-weight: 700;
            background: #eef6ff;
            z-index: 1;
          }
          &:first-of-type {
            border-radius: 4px 0 0 4px;
          }
          &:last-child {
            border-radius: 0 4px 4px 0;
          }
        }
      }
    }
    .toolsChart-select {
      width: 303px;
      select {
        height: 44px;
      }
    }
  }
  .externalLink {
    ul {
      height: 202px;
      overflow-y: auto;
      padding-right: 8px;
      &::-webkit-scrollbar {
        width: 8px;
        background-color: #f2f2f2;
        border-radius: 2.5px;
      }
      &::-webkit-scrollbar-thumb {
        background-color: #b7c3cb;
        border-radius: 5px;
      }
      li {
        padding: 10px 24px 10px 24px;
        border-bottom: 1px solid #e3e3e3;
        display: flex;
        justify-content: space-between;
        align-items: center;
        a {
          font-size: 16px;
        }
        .btn {
          margin-left: 10px;
        }
      }
    }
  }
  .custom-tbreport {
    padding: 6px 4px 12px 0px !important;
    .title {
      padding: 8px 22px 12px;
    }
  }
  .tbl-report {
    height: 200px;
    overflow-y: hidden;
    padding-right: 8px;
    &::-webkit-scrollbar {
      width: 8px;
      background-color: #f2f2f2;
      border-radius: 2.5px;
    }
    &::-webkit-scrollbar-thumb {
      background-color: #b7c3cb;
      border-radius: 5px;
    }
    tr {
      th,
      td {
        &:nth-child(1) {
          padding-left: 35px;
        }
      }
      th {
        font-size: 16px;
        font-weight: normal;
      }
      .report-gray {
        color: #99a5ae;
      }
    }
  }
  .bgBox-ranking {
    background: #fff;
    box-shadow: 0px 3px 30px rgba(0, 0, 0, 0.05);
    border-radius: 16px;
    margin-top: 20px;
  }
  .title-ranking {
    background: linear-gradient(69.9deg, #ff6847 -2.83%, rgba(255, 82, 71, 0.71) 43.43%, rgba(255, 168, 0, 0.5) 93.59%);
    border-radius: 0px 16px 16px 0px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
    border-radius: 16px 16px 0 0;
    padding: 24px;
    min-height: 70px;
    position: relative;
    &:after {
      position: absolute;
      bottom: -9px;
      right: 0;
      content: '';
      display: block;
      width: 86px;
      height: 67px;
      background: url(/img/icon-ranking.e4e7effe.svg) no-repeat;
    }
    .tlt {
      font-weight: 500;
      font-size: 22px;
      color: #fff;
    }
  }
  .groupRanking {
    padding: 18px 4px 12px 0px;
  }

  .list-manag {
    padding-bottom: 8px;
    > ul {
      > li {
        background: #fff;
        margin-top: 15px;
        padding: 12px 12px 12px 56px;
        box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.5);
        border-radius: 4px;
        position: relative;
        &.active {
          background: #e3e3e3;
          .list-title {
            .tlt {
              .svg {
                fill: #99a5ae;
              }
            }
          }
        }
      }
    }
    .list-hot {
      background: url(~@/assets/template/images/icon_hot-tag.png) no-repeat;
      width: 40px;
      height: 32px;
      display: block;
      position: absolute;
      top: -4px;
      left: 12px;
      font-size: 12px;
      color: #fcfcfc;
      font-weight: 700;
      padding: 4px 0 0 4px;
    }
    .list-title {
      .tlt {
        display: flex;
        span {
          min-width: 21px;
          margin-right: 8px;
        }
        a {
          color: #5f6b73;
          font-size: 22px;
          font-weight: 500;
        }
      }
    }
    .list-info {
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          padding-right: 36px;
          margin-top: 16px;
          &:last-child {
            padding-right: 0;
          }
          span {
            &.list-label {
              color: #99a5ae;
            }
          }
        }
      }
    }
  }
}
.slider-ranking {
  height: 454px;
  overflow-y: auto;
  &::-webkit-scrollbar {
    width: 8px;
    background-color: #f2f2f2;
    border-radius: 2.5px;
  }
  &::-webkit-scrollbar-thumb {
    background-color: #b7c3cb;
    border-radius: 5px;
  }
  .slick-arrow {
    width: 40px;
    height: 40px;
    display: block;
    top: initial;
    bottom: 0;
    &.slick-prev,
    &.slick-next {
      background: url(~@/assets/template/images/icon_slide-control-bule.svg) no-repeat;
    }
    &.slick-disabled {
      background: url(~@/assets/template/images/icon_slide-control-gray.svg) no-repeat;
      cursor: no-drop;
    }
    &.slick-prev {
      left: calc(50% - 60px);
      transform: rotate(180deg);
      &.slick-disabled {
        transform: rotate(0deg);
      }
    }
    &.slick-next {
      right: calc(50% - 60px);
      &.slick-disabled {
        transform: rotate(180deg);
      }
    }
  }
}
</style>
