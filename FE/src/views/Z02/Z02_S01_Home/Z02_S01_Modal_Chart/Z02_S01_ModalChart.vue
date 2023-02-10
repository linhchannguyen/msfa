<template>
  <div class="modal-body-Z02S01-Chart">
    <div id="msfa-notify-Z02S01-Chart"></div>
    <div v-if="type === 'line'" class="bgBox">
      <div class="titleChart">
        <div class="titleChart-info">
          <!-- <h2 class="titleChart-tlt">{{ '前同売上実績' }}</h2> -->
          <!-- <p class="titleChart-more" data-toggle="dropdown" aria-expanded="false">
          <a href="#"><img src="@/assets/template/images/icon_arrow-green.svg" alt="" />4% more</a>
          <span>in {{ yearActive }}</span>
        </p> -->
          <!-- <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a v-for="i of 10" :key="i" class="dropdown-item" @click="onChooseSaleMonth(getYear() - i + 1)">{{ getYear() - i + 1 }}年</a>
                          </div> -->
        </div>
      </div>
      <div class="barChart">
        <ApexLineChart v-if="series.length > 0" :series="series" :categories="categories" />

        <div v-else-if="!isLoading" class="noData">
          <div class="noData-content">
            <p class="noData-text">該当するデータがありません。</p>
            <div class="noData-thumb">
              <img src="@/assets/template/images/data/amico.svg" alt="" />
            </div>
          </div>
        </div>
      </div>
      <div class="toolsChart line">
        <div class="toolsChart-check toolsChart-check--col5">
          <ul class="ul-line mfsa-custom-tab-select2">
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
          <el-select v-model="actual_sales_product_cd" placeholder="未選択" class="form-control-select" @change="onChooseProduct">
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
    <div v-else class="bgBox">
      <div class="bgBox">
        <div class="barChart">
          <ApexBarChart v-if="seriesBar.length > 0" :categories="product_props" :series="seriesBar" />
          <div v-else-if="!isLoading" class="noData">
            <div class="noData-content">
              <p class="noData-text">該当するデータがありません。</p>
              <div class="noData-thumb">
                <img src="@/assets/template/images/data/amico.svg" alt="" />
              </div>
            </div>
          </div>
        </div>
        <div class="toolsChart">
          <div class="toolsChart-check toolsChart-check--col4">
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
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import ApexLineChart from '../../../../components/ApexLineChart.vue';
import Z02_S01_HomeServices from '@/api/Z02/Z02_S01_HomeServices';
import moment from 'moment';

export default {
  name: 'ModalChart',
  components: {
    ApexLineChart
  },
  props: {
    type: [String],
    widget: [Object],
    active_props: String,
    activeBar_props: String,
    actual_sales_product_cd_props: String,
    actual_sales_product_name_props: String
  },
  data() {
    return {
      actual_sales_product_cd: '',
      actual_sales_product_name: '',
      active: '',
      series: [],
      categories: [],
      yearActive: moment().format('YYYY'),
      sales_month: moment().format('YYYY-M-D'),
      seriesBar: [],
      product_props: [],
      activeBar: ''
    };
  },
  watch: {},
  mounted() {
    this.actual_sales_product_cd = this.actual_sales_product_cd_props;
    this.actual_sales_product_name = this.actual_sales_product_name_props;
    this.active = this.active_props;
    this.activeBar = this.activeBar_props;
    this.chooserBarChart(this.activeBar);
    this.chooser(this.active);
    this.onChooseProduct();
  },
  methods: {
    chooser(item) {
      this.active = item;
      this.getSaleChartLine({ aggregate_layer_num: this.active, actual_sales_product_cd: this.actual_sales_product_cd, sales_month: this.sales_month });
    },
    getSaleChartLine(params) {
      // this.loading = true;

      Z02_S01_HomeServices.getSaleChartLine(params)
        .then((res) => {
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
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-Z02S01-Chart',
            idNodeNotify: 'msfa-notify-Z02S01-Chart'
          });
        })
        .finally(() => (this.loading = false));
    },
    getActualDigestionChartBar(params) {
      Z02_S01_HomeServices.getActualDigestionChartBar(params)
        .then((res) => {
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
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-Z02S01-Chart',
            idNodeNotify: 'msfa-notify-Z02S01-Chart'
          });
        })
        .finally(() => (this.loading = false));
    },
    chooserBarChart(num) {
      this.activeBar = num;
      this.getActualDigestionChartBar({
        aggregate_layer_num: this.activeBar
      });
    },
    onChooseProduct() {
      this.getSaleChartLine({ aggregate_layer_num: this.active, actual_sales_product_cd: this.actual_sales_product_cd, sales_month: this.sales_month });
    }
  }
};
</script>

<style lang="scss" scoped>
@import '../../../../assets/css/_animations.scss';
$viewport_tablet: 991px;
$viewport_mobile: 767px;
.titleChart {
  display: flex;
  justify-content: space-between;
  padding-top: 9px;
  .titleChart-info {
    width: 20%;
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
  img {
    width: 100%;
  }
}
.line {
  width: 80%;
}
.toolsChart {
  width: 100%;
  margin-top: 0px;
  display: inline-flex;
  .toolsChart-check {
    width: 100%;
    &.toolsChart-check--col4 {
      ul {
        li {
          width: 25%;
          max-width: 120px;
        }
      }
    }
    &.toolsChart-check--col5 {
      ul {
        li {
          width: 19.5%;
          max-width: 120px;
        }
      }
    }
    .ul-line {
      justify-content: left;
    }
    ul {
      display: flex;
      justify-content: left;
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

.noData {
  .noData-content {
    height: 100%;
    width: auto;
  }
  .noData-thumb {
    height: calc(100% - 40px);
  }
}
</style>
