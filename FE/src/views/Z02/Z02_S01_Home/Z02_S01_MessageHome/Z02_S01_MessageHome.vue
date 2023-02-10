<template>
  <div class="bgBox bgBox--change">
    <div class="title">
      <div class="title-info">
        <span class="title-item"><img src="@/assets/template/images/icon_mail.svg" alt="" /></span>
        <h2 class="title-tlt">メッセージ</h2>
      </div>
      <div class="navTabs-main">
        <ul class="nav mfsa-custom-tab-select2">
          <li :class="activeel === 0 ? 'active' : ''" data-toggle="tab" role="tab" @click="activeli(0)" @touchstart="activeli(0)">全て</li>
          <li :class="activeel === 1 ? 'active' : ''" data-toggle="tab" role="tab" @click="activeli(1)" @touchstart="activeli(1)">重要</li>
          <li :class="activeel === 2 ? 'active' : ''" data-toggle="tab" role="tab" @click="activeli(2)" @touchstart="activeli(2)">未読</li>
        </ul>
      </div>
    </div>

    <div class="tab-content">
      <div ref="messages" :class="activeel === 0 ? 'activeContent' : 'noActiveContent'" class="lstMessage scrollbar" @scroll="setScroll">
        <ul v-if="messages.length > 0">
          <li v-for="item of messages" :key="item.message_id">
            <div class="lstMessage-content">
              <h3
                class="lstMessage-tlt"
                @click="onclickShowModal(item.message_id, item.message_subject, item.message_contents, item.important_flag)"
                @touchstart="onclickShowModal(item.message_id, item.message_subject, item.message_contents, item.important_flag)"
              >
                <a class="log-icon" title_log="件名" href="#">{{ item.message_subject }}</a>
                <span v-if="item.message_read === 0" class="lstMessage-has"></span>
              </h3>
              <p class="lstMessage-label">{{ item.org_name }}</p>
            </div>
            <div class="lstMessage-info">
              <p class="lstMessage-impor"><span v-if="item.important_flag === 1" class="span-label span-label--yellow">重要</span></p>
              <p class="lstMessage-date">{{ item.date }}</p>
            </div>
          </li>
        </ul>
        <div v-else class="noData">
          <div v-if="!isLoading" class="noData-content">
            <div class="noData-thumb">
              <img src="@/assets/template/images/noDataHome.svg" alt="icon" />
            </div>
            <p class="noData-text">該当するデータがありません。</p>
          </div>
        </div>
      </div>
      <div ref="important" :class="activeel === 1 ? 'activeContent' : 'noActiveContent'" class="lstMessage scrollbar" @scroll="setScroll">
        <ul v-if="important.length > 0">
          <li v-for="item of important" :key="item.message_id">
            <div class="lstMessage-content">
              <h3
                class="lstMessage-tlt"
                @click="onclickShowModal(item.message_id, item.message_subject, item.message_contents, item.important_flag)"
                @touchstart="onclickShowModal(item.message_id, item.message_subject, item.message_contents, item.important_flag)"
              >
                <a class="log-icon" title_log="件名" href="#">{{ item.message_subject }}</a>
                <span v-if="item.message_read === 0" class="lstMessage-has log-icon"></span>
              </h3>
              <p class="lstMessage-label">{{ item.org_name }}</p>
            </div>
            <div class="lstMessage-info">
              <p class="lstMessage-impor"><span v-if="item.important_flag === 1" class="span-label span-label--yellow">重要</span></p>
              <p class="lstMessage-date">{{ item.date }}</p>
            </div>
          </li>
        </ul>
        <div v-else class="noData">
          <div v-if="!isLoading" class="noData-content">
            <div class="noData-thumb">
              <img src="@/assets/template/images/noDataHome.svg" alt="icon" />
            </div>
            <p class="noData-text">該当するデータがありません。</p>
          </div>
        </div>
      </div>
      <div ref="unread" :class="activeel === 2 ? 'activeContent' : 'noActiveContent'" class="lstMessage scrollbar" @scroll="setScroll">
        <ul v-if="unread.length > 0">
          <li v-for="item of unread" :key="item.message_id">
            <div class="lstMessage-content">
              <h3
                class="lstMessage-tlt"
                @click="onclickShowModal(item.message_id, item.message_subject, item.message_contents, item.important_flag)"
                @touchstart="onclickShowModal(item.message_id, item.message_subject, item.message_contents, item.important_flag)"
              >
                <a class="log-icon" title_log="件名" href="#">{{ item.message_subject }}</a>
                <span v-if="item.message_read === 0" class="lstMessage-has log-icon"></span>
              </h3>
              <p class="lstMessage-label">{{ item.org_name }}</p>
            </div>
            <div class="lstMessage-info">
              <p class="lstMessage-impor"><span v-if="item.important_flag === 1" class="span-label span-label--yellow">重要</span></p>
              <p class="lstMessage-date">{{ item.date }}</p>
            </div>
          </li>
        </ul>
        <div v-else class="noData">
          <div v-if="!isLoading" class="noData-content">
            <div class="noData-thumb">
              <img src="@/assets/template/images/noDataHome.svg" alt="icon" />
            </div>
            <p class="noData-text">該当するデータがありません。</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onFinishScreen"></component>
    </template>
  </el-dialog>
</template>

<script>
import ModalMessageHome from './ModalMessageHome.vue';
import Z02_S01_HomeServices from '@/api/Z02/Z02_S01_HomeServices';
import { markRaw } from 'vue';
import moment from 'moment';
export default {
  name: 'MessageHome',
  components: {
    ModalMessageHome
  },
  data() {
    return {
      showModal: false,
      id: '',
      important: [],
      messages: [],
      unread: [],
      isLoading: true,
      activeel: 0,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },
  created() {
    this.activeli(2);
    this.getListMessage();
    setTimeout(() => {
      this.activeli(0);
    }, 500);
  },
  mounted() {
    this.emitter.on('home-reload', () => {
      this.getListMessage();
    });
  },
  methods: {
    activeli(id) {
      this.activeel = id;
      setTimeout(() => {
        if (this.$refs.important) {
          this.$refs.important.scrollTop = 1;
        }
      }, 50);
      setTimeout(() => {
        if (this.$refs.unread) {
          this.$refs.unread.scrollTop = 1;
        }
      }, 50);
      setTimeout(() => {
        if (this.$refs.messages) {
          this.$refs.messages.scrollTop = 1;
        }
      }, 50);
    },
    setScroll() {
      if (this.$refs.messages && this.$refs.messages.scrollTop < 10) {
        this.$refs.messages.scrollTop = 1;
      }
      if (this.$refs.unread && this.$refs.unread.scrollTop < 10) {
        this.$refs.unread.scrollTop = 1;
      }
      if (this.$refs.messages && this.$refs.important.scrollTop < 10) {
        this.$refs.important.scrollTop = 1;
      }
    },
    onclickShowModal(message_id, message_subject, message_contents, important_flag) {
      this.showModal = true;
      this.id = message_id;
      this.modalConfig = {
        ...this.modalConfig,
        title: 'メッセージ詳細',
        isShowModal: true,
        component: markRaw(ModalMessageHome),
        props: { id: message_id, subject: message_subject, contents: message_contents, importantFlag: important_flag },
        width: '90%',
        destroyOnClose: true
      };
    },
    getListMessage() {
      Z02_S01_HomeServices.getListMessageService()
        .then((res) => {
          this.important = res.data.data.important;
          this.important.forEach((element) => {
            element.date = moment(element.last_update_datetime).format('YYYY/M/D');
          });
          this.messages = res.data.data.messages;
          this.messages.forEach((element) => {
            element.date = moment(element.last_update_datetime).format('YYYY/M/D');
          });
          this.unread = res.data.data.unread;
          this.unread.forEach((element) => {
            element.date = moment(element.last_update_datetime).format('YYYY/M/D');
          });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          setTimeout(() => {
            if (this.$refs.important) {
              this.$refs.important.scrollTop = 1;
            }
          }, 50);
          setTimeout(() => {
            if (this.$refs.unread) {
              this.$refs.unread.scrollTop = 1;
            }
          }, 50);
          setTimeout(() => {
            if (this.$refs.messages) {
              this.$refs.messages.scrollTop = 1;
            }
          }, 50);
          this.isLoading = false;
        });
    },
    onResultModal() {
      this.getListMessage();
    },
    handleCurrentChangeMessages(val) {
      this.pageMessages = val;
    },
    handleCurrentChangeImportant(val) {
      this.pageImportant = val;
    },
    handleCurrentChangeUnread(val) {
      this.pageUnread = val;
    },
    onFinishScreen() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      this.getListMessage();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_tablet: 991px;
$viewport_mobile: 767px;
.noData-content {
  display: flex;
  align-items: center;
  justify-content: space-evenly;
}
.noActiveContent {
  display: none;
}
.tab-content {
  padding: 12px 4px 12px 0px;
}
.page-zo {
  .navTop {
    > ul {
      margin-left: -2.5%;
      display: flex;
      flex-wrap: wrap;
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
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    position: relative;
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
                transform: rotate(180deg);
              }
            }
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
      padding: 0;
      .title {
        padding: 12px 24px;
        box-shadow: 0px 3px 6px #e3e3e3;
      }
    }
    .title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      .title-info {
        display: flex;
        .title-item {
          min-width: 30px;
          margin-right: 5px;
        }
        .title-tlt {
          font-weight: 700;
          font-size: 22px;
        }
      }
    }
  }
  .navTabs-main {
    ul {
      display: flex;
      flex-wrap: wrap;

      li {
        &:nth-child(1) {
          border-radius: 4px 0 0 4px;
        }
        &:nth-child(3) {
          border-radius: 0 4px 4px 0;
        }
        padding: 9px 24px;
        color: #5f6b73;
        border: 1px solid #727f88;
        display: block;
        margin-left: -1px;
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
  .lstMessage {
    padding: 0 24px;
    overflow-y: auto;
    height: 306px;
    &::-webkit-scrollbar {
      // Ipad not use default scroll
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
      li {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #e3e3e3;
        padding: 12px 0;
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
      .span-label--yellow {
        background: #ffeab6;
        color: #e2633b;
        font-weight: 500;
        text-align: center;
        border-radius: 12px;
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
  .activityPlan {
    padding-top: 10px;
    ul {
      li {
        padding: 12px 0 8px;
        border-bottom: 1px solid #e3e3e3;
        &:last-child {
          border-bottom: none;
          padding-bottom: 0;
        }
      }
    }
    .plan-date {
      color: #afabab;
      font-size: 18px;
      font-weight: 700;
      padding-bottom: 5px;
    }
    .plan-content {
      display: flex;
      flex-wrap: wrap;
      margin-left: -2%;
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
        font-size: 16px;
        span {
          font-weight: 700;
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
  }
  .toolsChart {
    display: flex;
    flex-wrap: wrap;
    margin-top: 24px;
    .toolsChart-check {
      width: calc(100% - 164px);
      padding-right: 28px;
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
            width: 20%;
          }
        }
      }
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          padding: 10px 2px;
          margin-left: -1px;
          text-align: center;
          color: #b7d4ff;
          border: 1px solid #b7d4ff;
          position: relative;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          &.active {
            border: 1px solid #448add;
            color: #448add;
            font-weight: 700;
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
      width: 164px;
    }
  }
  .externalLink {
    padding-top: 22px;
    ul {
      li {
        padding: 8px 24px 8px 0;
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
  .tbl-report {
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
    background: #fec07c;
    background: -moz-linear-gradient(top, #fec07c 0%, #ff6e54 100%);
    background: -webkit-linear-gradient(top, #fec07c 0%, #ff6e54 100%);
    background: linear-gradient(to bottom, #fec07c 0%, #ff6e54 100%);
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
    border-radius: 16px 16px 0 0;
    padding: 24px;
    min-height: 70px;
    position: relative;
    &:after {
      position: absolute;
      bottom: 0;
      right: 0;
      content: '';
      display: block;
      width: 82px;
      height: 67px;
      background: url(~@/assets/template/images/bgTitle-ranking.svg) no-repeat;
    }
    .tlt {
      font-weight: 500;
      font-size: 22px;
      color: #fff;
    }
  }
  .groupRanking {
    padding: 18px 0 22px;
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
</style>
