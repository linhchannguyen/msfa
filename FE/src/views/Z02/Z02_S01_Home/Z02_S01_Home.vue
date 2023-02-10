<template>
  <div v-loading="loading" class="wrapContent page-zo" :class="checkZindex ? 'z02-s01' : ''">
    <div ref="tblUserContent" class="personalTbl-home wrap scrollbar" @scroll="onScroll">
      <div v-load-f5="true" class="groupHome">
        <div class="navTop">
          <Z02S01Widget :loading-child="loading" :data="NonSubmission" />
        </div>
        <div class="wrapColumn">
          <div class="wrapColumn-col5">
            <MessageHome :loading-child="loading" />
            <ActivityPlan v-if="roleR10" :loading-child="loading" :data="activityPlans" />
            <div v-if="!roleR10" class="bgBox bgBox--change">
              <div class="title">
                <div class="title-info">
                  <span class="title-item"><img src="@/assets/template/images/icon_link.svg" alt="" /></span>
                  <h2 class="title-tlt">外部リンク</h2>
                </div>
              </div>
              <!--URL-->
              <Z02S01ExternalURL :loading-child="loading" :data="externalUrl" />
            </div>
          </div>

          <div class="wrapColumn-col5">
            <div class="bgBox bgBox--type1">
              <div class="titleChart">
                <div class="titleChart-info">
                  <h2 class="titleChart-tlt">前同売上実績</h2>
                </div>
                <div class="titleChart-btn">
                  <button class="btn btn--icon" type="button" @click="onclickShowModal('line')">
                    <img src="@/assets/template/images/icon_maximize.svg" alt="" />
                  </button>
                </div>
              </div>
              <div class="barChart">
                <ApexLineChart v-if="series.length > 0" :loading-child="loading" :series="series" :categories="categories" />

                <div v-else-if="!loading" class="noData">
                  <div class="noData-content">
                    <p class="noData-text">該当するデータがありません。</p>
                    <div class="noData-thumb">
                      <img src="@/assets/template/images/data/amico.svg" alt="" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="toolsChart">
                <div class="toolsChart-check toolsChart-check--col5">
                  <ul class="mfsa-custom-tab-select2">
                    <li
                      v-for="item of widget?.organization_layer"
                      :key="item.definition_value"
                      :class="[item.definition_value == active ? 'active' : '']"
                      @click="chooser(item.definition_value)"
                      @touchstart="chooser(item.definition_value)"
                    >
                      {{ item.definition_label }}
                    </li>
                  </ul>
                </div>
                <div class="toolsChart-select">
                  <el-select id="zoom-select" v-model="actual_sales_product_cd" class="form-control-select" @change="onChooseProduct">
                    <el-option
                      v-for="(item, index) of widget?.actual_digestion_items"
                      :key="index"
                      :label="item.actual_sales_product_name"
                      :value="item.actual_sales_product_cd"
                    >
                      {{ item.actual_sales_product_name }}
                    </el-option>
                  </el-select>
                </div>
              </div>
            </div>
            <!--Bar chart-->
            <div class="bgBox bgBox--type1">
              <div class="titleChart">
                <div class="titleChart-info">
                  <h2 class="titleChart-tlt">実消化進捗状況</h2>
                </div>
                <div class="titleChart-btn">
                  <button class="btn btn--icon" type="button" @click="onclickShowModal('bar', '実消化進捗状況')">
                    <img src="@/assets/template/images/icon_maximize.svg" alt="" />
                  </button>
                </div>
              </div>
              <div class="barChart">
                <ApexBarChart v-if="seriesBar.length > 0" :loading-child="loading" :categories="product_props" :series="seriesBar" />
                <div v-else-if="!loading" class="noData">
                  <div class="noData-content">
                    <p class="noData-text">該当するデータがありません。</p>
                    <div class="noData-thumb">
                      <img src="@/assets/template/images/data/amico.svg" alt="" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="toolsChart">
                <div class="toolsChart-check toolsChart-check--col5">
                  <ul class="mfsa-custom-tab-select2">
                    <li
                      v-for="item of widget?.organization_layer"
                      :key="item.definition_value"
                      :class="[item.definition_value == activeBar ? 'active' : '']"
                      @click="chooserBarChart(item.definition_value)"
                      @touchstart="chooserBarChart(item.definition_value)"
                    >
                      {{ item.definition_label }}
                    </li>
                  </ul>
                </div>
                <div class="toolsChart-select"></div>
              </div>
            </div>

            <div v-if="roleR10" class="bgBox bgBox--change">
              <div class="title">
                <div class="title-info">
                  <span class="title-item"><img src="@/assets/template/images/icon_link.svg" alt="" /></span>
                  <h2 class="title-tlt">外部リンク</h2>
                </div>
              </div>
              <!--URL-->
              <Z02S01ExternalURL :loading-child="loading" :data="externalUrl" />
            </div>
          </div>
        </div>
        <div class="wrapColumn">
          <div class="wrapColumn-col4">
            <Z02S01Ranking :loading-child="loading" :data="ranking" :widget="widget" @callApi="callApiRanking" />
          </div>
          <div class="wrapColumn-col6">
            <Z02S01ExternalEmbeddedURL :loading-child="loading" :data="externalUrl" />
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
  </div>
</template>

<script>
import '@/assets/template/js/slick.js';
import MessageHome from './Z02_S01_MessageHome/Z02_S01_MessageHome';
import ActivityPlan from './Z02_S01_ActivityPlan/Z02_S01_ActivityPlan.vue';
import Z02S01ExternalURL from './Z02_S01_ExternalURL/Z02_S01_ExternalURL.vue';
import Z02_S01_HomeServices from '@/api/Z02/Z02_S01_HomeServices';
import Z02S01ExternalEmbeddedURL from './Z02_S01_ExternalEmbeddedURL/Z02_S01_ExternalEmbeddedURL.vue';
import Z02S01Widget from './Z02_S01_Widget/Z02_S01_Widget.vue';
import Z02S01Ranking from './Z02_S01_Ranking/Z02_S01_Ranking.vue';
import Z02S01ModalChart from './Z02_S01_Modal_Chart/Z02_S01_ModalChart.vue';

import ApexLineChart from '../../../components/ApexLineChart.vue';
import ApexBarChart from '../../../components/ApexBarChart.vue';
import moment from 'moment';
import { Auth } from '@/api';
import { markRaw } from 'vue';

export default {
  name: 'S01_Home',
  components: {
    MessageHome,
    ActivityPlan,
    Z02S01ExternalURL,
    Z02S01ExternalEmbeddedURL,
    Z02S01Widget,
    Z02S01Ranking,
    ApexLineChart,
    ApexBarChart
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      activityPlans: [],
      externalUrl: [],
      NonSubmission: null,
      ranking: [],
      widget: null,
      loading: false,
      dragSrcEl: '',
      htmlBox: '',
      categories: [],
      series: [],
      active: '1',
      activeBar: '1',
      sales_month: moment().format('YYYY-M-D'),
      actual_sales_product_cd: '',
      actual_sales_product_name: '',
      fullscreen: false,
      typeShow: 'line',
      product_props: [],
      seriesBar: [],
      yearActive: moment().format('YYYY'),
      roleR10: false,
      checktoken: '',
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      loadRangKing: false,
      checkZindex: true,
      onScrollTop: 0
    };
  },
  computed: {
    isLoginProxy() {
      return Boolean(this.$store.state.auth.isLoginProxy);
    }
  },
  watch: {
    isLoginProxy() {
      this.checkPermission('R10');
      this.loadAll();
      this.loadChart();
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'ホーム',
      icon: 'home-icon-color.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.Z02_S01_Home || 0;
    if (this.$refs.tblUserContent) {
      this.$refs.tblUserContent.scrollTop = this.onScrollTop;
    }
    console.log(this.onScrollTop);
    this.checkPermission('R10');
    setTimeout(() => {
      this.checkZindex = false;
    }, 1000);
    this.js_pscroll(0.5);
    this.loadAll();
    this.loadChart();
  },
  methods: {
    onScroll() {
      // let content = document.querySelector('.personalTbl-home');
      let scrollTop = this.$refs.tblUserContent.scrollTop;
      this.setScrollScreen('Z02_S01_Home', scrollTop);
    },
    async checkPermission(role) {
      const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
      const currentUser = JSON.parse(localStorage.getItem('currentUser'));
      let user_cd = currentUserProxy ? currentUserProxy.user_cd : currentUser.user_cd;
      const permission = await Auth.getPolicyRoleService({ user_cd });
      if (permission?.data?.data?.includes(role)) {
        this.roleR10 = true;
      } else {
        this.roleR10 = false;
      }
    },
    onclickShowModal(type, title) {
      this.showModal = true;
      this.modalConfig = {
        ...this.modalConfig,
        title: title || '前同売上実績',
        isShowModal: true,
        component: markRaw(Z02S01ModalChart),
        props: {
          type,
          widget: this.widget,
          actual_sales_product_cd_props: this.actual_sales_product_cd,
          actual_sales_product_name_props: this.actual_sales_product_name,
          active_props: this.active,
          activeBar_props: this.activeBar
        },
        width: '90%',
        destroyOnClose: true
      };
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    getYear() {
      const year = moment().format('YYYY');
      return year;
    },
    setIndexDropDown() {
      setTimeout(() => {
        const thumb = document.querySelector('#zoom-line-chart');
        const attributes = thumb.querySelector('.select-trigger');
        const elPoperId = attributes.attributes['aria-describedby'].value;
        const elPoper = document.getElementById(elPoperId);
        elPoper.style.zIndex = 10000;
      });
    },
    setFullScreen(isFull, type) {
      this.fullscreen = isFull;
      this.typeShow = type;
      this.typeShow === 'line'
        ? this.getSaleChartLine({ aggregate_layer_num: this.active, actual_sales_product_cd: this.actual_sales_product_cd, sales_month: this.sales_month })
        : this.getActualDigestionChartBar({ aggregate_layer_num: this.activeBar });
    },
    chooser(num) {
      this.active = num;
      this.getSaleChartLine({ aggregate_layer_num: this.active, actual_sales_product_cd: this.actual_sales_product_cd, sales_month: this.sales_month });
    },
    chooserBarChart(num) {
      this.activeBar = num;
      this.getActualDigestionChartBar({
        aggregate_layer_num: this.activeBar
      });
    },
    onChooseProduct() {
      this.getSaleChartLine({ aggregate_layer_num: this.active, actual_sales_product_cd: this.actual_sales_product_cd, sales_month: this.sales_month });
    },
    onChooseSaleMonth(data) {
      this.yearActive = data;
      this.sales_month = moment().set('year', data).format('YYYY-M-D');
      this.getSaleChartLine({ aggregate_layer_num: this.active, actual_sales_product_cd: this.actual_sales_product_cd, sales_month: this.sales_month });
    },
    async loadAll() {
      this.loading = true;
      const ActivityPlan = await this.getActivityPlan();
      const ExternalUrl = await this.getExternalURL();
      const NonSubmission = await this.getWidgetNonSub();
      const Ranking = await this.getRanKing({ aggregate_layer_num: 1 });
      const Widget = await this.getWidget();
      const promise = new Promise((resolve, reject) => {
        resolve({ Ranking, NonSubmission, ExternalUrl, ActivityPlan, Widget }), reject('Error');
      });
      promise
        .then((res) => {
          this.activityPlans = res['ActivityPlan'];
          this.externalUrl = res['ExternalUrl'];
          this.NonSubmission = res['NonSubmission'];
          this.ranking = res['Ranking'];
          this.widget = res['Widget'];
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    loadChart() {
      this.getSaleChartLine({ aggregate_layer_num: this.active, actual_sales_product_cd: this.actual_sales_product_cd, sales_month: this.sales_month });
      this.getActualDigestionChartBar({ aggregate_layer_num: this.activeBar });
    },
    handleDragStart(e, item) {
      this.dragSrcEl = item;
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/html', item.innerHTML);
    },
    dragWrapColunm() {
      this.htmlBox = document.querySelectorAll('.wrapColumn .bgBox, .wrapColumn .bgBox-p, .wrapColumn .bgBox-ranking');
      if (this.htmlBox)
        this.htmlBox.forEach((item) => {
          item.addEventListener('dragstart', (e) => this.handleDragStart(e, item));
          item.addEventListener('dragover', (e) => {
            if (e.preventDefault) {
              e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';

            return false;
          });
          item.addEventListener('dragenter', () => {
            item.classList.add('homeOver');
          });
          item.addEventListener('dragleave', () => {
            item.classList.remove('homeOver');
          });

          item.addEventListener('drop', (e) => {
            if (e.stopPropagation) {
              e.stopPropagation(); // stops the browser from redirecting.
            }
            if (this.dragSrcEl) {
              if (this.dragSrcEl !== item) {
                this.dragSrcEl.innerHTML = item.innerHTML;
                item.innerHTML = e.dataTransfer.getData('text/html');
                item.style.opacity = 1;
              }
            }
            return false;
          });
          item.addEventListener('dragend', () => {
            item.style.opacity = 1;
            item.classList.remove('homeOver');
          });
        });
    },
    getActivityPlan() {
      return Z02_S01_HomeServices.getactivityPlan()
        .then((s) => {
          return s.data.data;
        })
        .catch((err) => {
          return err;
        });
    },
    getExternalURL() {
      return Z02_S01_HomeServices.getExternalUrl()
        .then((item) => {
          return item.data.data;
        })
        .catch((err) => {
          return err;
        });
    },
    getWidgetNonSub() {
      return Z02_S01_HomeServices.getNonSubmission()
        .then((item) => {
          return item.data.data;
        })
        .catch((err) => {
          return err;
        });
    },
    getWidget() {
      return Z02_S01_HomeServices.getWidget()
        .then((item) => {
          const widget = item.data.data;
          this.actual_sales_product_cd = widget.actual_digestion_items[0].actual_sales_product_cd;
          this.actual_sales_product_name = widget.actual_digestion_items[0].actual_sales_product_name;
          this.onChooseProduct();
          return widget;
        })
        .catch((err) => {
          return err;
        });
    },
    getRanKing(params) {
      this.loadRangKing = true;
      return Z02_S01_HomeServices.getRanKing(params)
        .then((item) => {
          return item.data.data;
        })
        .catch((err) => {
          return err;
        })
        .finally(() => (this.loadRangKing = false));
    },
    callApiRanking(event) {
      this.getRanKing({ aggregate_layer_num: event })
        .then((res) => (this.ranking = res))
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    },
    getSaleChartLine(params) {
      Z02_S01_HomeServices.getSaleChartLine(params)
        .then((res) => {
          this.series = [];
          if (res && res.data && res.data.data && res.data.data.length > 0) {
            const previous_year_sales_amount = res.data.data.map((s) => s.previous_year_sales_amount);
            const sales_amount = res.data.data.map((s) => s.sales_amount);
            let label = res.data.data.map((s) => ({ month: new Date(s.sales_month).getMonth() + 1, year: new Date(s.sales_month).getFullYear() }));
            this.categories = label.map((x) => `${x.year}年${x.month}月`);
            this.series = [
              { name: '前年売上金額', data: previous_year_sales_amount },
              { name: '売上月', data: sales_amount }
            ];
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    },
    getActualDigestionChartBar(params) {
      Z02_S01_HomeServices.getActualDigestionChartBar(params)
        .then((res) => {
          this.seriesBar = [];
          if (res && res.data && res.data.data && res.data.data.length > 0) {
            const goal_amount = res.data.data.map((s) => s.goal_amount);
            const sales_amount = res.data.data.map((s) => s.sales_amount);
            let actual_sales_product_name = res.data.data.map((s) => s.actual_sales_product_name);
            this.seriesBar = [
              { name: '目標金額', data: goal_amount }, // 9
              { name: '売上金額', data: sales_amount } // 10
            ];
            this.product_props = actual_sales_product_name;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    }
  }
};
</script>

<style lang="scss" scoped>
@import '../../../assets/css/_animations.scss';
$viewport_tablet: 991px;
$viewport_mobile: 767px;
$viewport_tablet_mini: 1024px;

.wrapContent {
  z-index: unset;
  // overflow: overlay;
  &::-webkit-scrollbar {
    width: 1px;
    background-color: transparent;
    border-radius: 2.5px;
  }
  &::-webkit-scrollbar-thumb {
    background-color: transparent;
    border-radius: 5px;
  }
}

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
      .vue-apexcharts {
        min-height: 245px;
      }
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
      padding: 12px 4px 12px 0px;
      .title {
        padding: 8px 22px 20px;
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
  .activityPlan {
    height: 640px;
    background: #f8f8f8;
    ul {
      padding-right: 8px;
      background-color: #fff;

      height: 609px;
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
      width: 24%;
      box-shadow: inset -1px 0px 0px #e3e3e3;
      padding: 19px 22px 20px;
    }
    .plan-content {
      display: flex;
      flex-wrap: wrap;
      margin-left: -2%;
      width: 80%;
      padding: 19px 24px 20px 45px;
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
      }
      .plan-item {
        margin-right: 22px;
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
        span {
          font-weight: 700;
        }
        a {
          color: #5f6b73;
          font-weight: normal;
          font-size: 14px;
          &:hover {
            text-decoration: unset;
            color: #5f6b73;
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
        cursor: pointer;
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
    height: 245px;
    img {
      width: 100%;
    }

    .noData {
      .noData-content {
        height: 100%;
      }
      .noData-thumb {
        height: calc(100% - 40px);
      }
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
            @media (max-width: $viewport_tablet_mini) {
              width: 4em;
            }
            @media (max-width: $viewport_tablet) {
              width: 4.5em;
            }
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
            pointer-events: none;
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
.materialZoom {
  border-radius: 0;
  padding: 0;
  // background: #464545bf;
  background-color: rgba(0, 0, 0, 0.5);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10000;
  .toolsChart {
    width: 100%;
    margin-top: 0px;
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
      .ul-line {
        justify-content: left;
      }
      ul {
        display: flex;
        justify-content: right;
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
            pointer-events: none;
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

  .materialZoom-thumb {
    height: 100%;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    .header-chart {
      position: absolute;
      width: 100%;
      height: 100px;
      margin-top: -25px;
      h2 {
        color: white;
        font-size: 25px;
      }
      .materialZoom-close {
        width: 42px;
        height: 42px;
        position: absolute;
        z-index: 1;
        top: 15px;
        right: 0px;
        .btn {
          border-radius: 50%;
          padding: 0;
          width: 100%;
          height: 100%;
          background: #fff;
        }
      }
    }
    .card-chart {
      height: 830px;
      width: 1068px;
      position: relative;
      .bgBox {
        width: 100%;
        min-height: 450px;
        position: absolute;
      }
    }

    .perPage {
      font-size: 24px;
      color: #ffffff;
      position: absolute;
      bottom: 24px;
      right: 24px;
    }
    .slideThumb {
      height: 100%;
      .el-carousel__item {
        padding: 0 120px;
        overflow: hidden;
        .slideThumb-photo {
          height: 100%;
          display: flex;
          justify-content: center;
          align-items: center;
          flex-wrap: wrap;
          overflow: hidden;
          img {
            max-height: 100%;
          }
        }
      }
    }
  }
}
.zIndex {
  z-index: 10000 !important;
}
.dropdown-menu {
  min-width: 7rem;
  overflow: auto;
  height: 150px;
  .dropdown-item {
    font-size: 16px;

    cursor: pointer;
  }
}
.z02-s01 {
  // background: #f2f2f2 !important;
  // z-index: 9999 !important;
}

.scrollbar {
  overflow: overlay;
}
</style>
