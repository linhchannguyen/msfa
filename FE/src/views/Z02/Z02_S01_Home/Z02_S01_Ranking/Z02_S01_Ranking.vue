<template>
  <div class="bgBox-ranking">
    <div class="title-ranking"><h2 class="tlt">実消化ランキング</h2></div>
    <div class="groupRanking">
      <div class="btnRanking">
        <ul class="mfsa-custom-tab-select2">
          <li
            v-for="item of organization_layer"
            :key="item.definition_value"
            :class="[item.definition_value == active ? 'active' : '']"
            @click="chooser(item.definition_value)"
            @touchstart="chooser(item.definition_value)"
          >
            {{ item.definition_label }}
          </li>
        </ul>
      </div>
      <div v-if="ranking.length > 0" class="slider slider-ranking">
        <div>
          <div class="listRanking">
            <ul>
              <li v-for="(dataItem, index) of ranking" :key="index" :class="[index + 1 >= 1 && index + 1 <= 3 ? `listRanking-no${index + 1}` : 'non']">
                <div class="listRanking-ordinal">{{ index + 1 }}</div>
                <div class="listRanking-text">{{ dataItem?.actual_sales_product_name }}</div>
                <div class="listRanking-number">{{ formatNumber(dataItem?.sales_amount || 0) }}</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <EmptyData v-if="ranking.length === 0 && canShowEmpty" custom-class="custom-class-no-data w-100" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'Z02_S01_Ranking',
  props: {
    data: [Object || Array],
    widget: [Object || Array],
    loadrangking: [Boolean]
  },
  emits: ['callApi'],
  data() {
    return {
      ranking: [],
      organization_layer: [],
      active: '1',
      canShowEmpty: false
    };
  },
  watch: {
    data(e) {
      this.ranking = e;
      this.canShowEmpty = true;
    },
    widget(e) {
      this.organization_layer = e.organization_layer;
    }
  },
  mounted() {},
  methods: {
    chooser(num) {
      this.active = num;
      this.$emit('callApi', num);
    },
    formatNumber(num) {
      num = Number(num).toFixed(0);
      num = num.toString();
      const pattern = /(-?\d+)(\d{3})/;
      while (pattern.test(num)) num = num.replace(pattern, '$1,$2');
      return num;
    }
  }
};
</script>

<style lang="scss" scoped>
@import '../../../../assets/css/_animations.scss';
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
    height: 692px;
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
  .btnRanking {
    ul {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      li {
        padding: 10px 26px;
        margin-left: -1px;
        text-align: center;
        color: #5f6b73;
        border: 1px solid #727f88;
        position: relative;
        cursor: pointer;
        &.active {
          border: 2px solid #448add;
          color: #448add;
          font-weight: 700;
          z-index: 1;
          background: #eef6ff;
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
  .listRanking {
    ul {
      li {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 16px;
        &:first-of-type {
          margin-top: 13px;
        }
        .listRanking-ordinal {
          width: 64px;
          height: 45px;
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          justify-content: center;
          padding: 0 6px;
          background: #e3e3e3;
          border-radius: 0px 30px 30px 0px;
          color: #2d3033;
          font-size: 24px;
          font-weight: 700;
        }
        .listRanking-text {
          width: calc(100% - 224px);
          padding: 0 10px 0 29px;
        }
        .listRanking-number {
          width: 160px;
          padding-right: 34px;
          text-align: right;
        }
        .listRanking-text,
        .listRanking-number {
          font-weight: 500;
          font-size: 22px;
        }
        &.listRanking-no1,
        &.listRanking-no2,
        &.listRanking-no3 {
          .listRanking-ordinal {
            height: 55px;
            color: #fff;
            font-size: 36px;
            font-weight: 700;
          }
        }
        &.listRanking-no1 {
          .listRanking-ordinal {
            background: linear-gradient(28.06deg, rgba(245, 193, 57, 0.9) 55.53%, rgba(254, 230, 146, 0.5) 95.13%);
          }
          .listRanking-text,
          .listRanking-number {
            color: #f1b86a;
            font-weight: 700;
          }
        }
        &.listRanking-no2 {
          .listRanking-ordinal {
            background: linear-gradient(30.15deg, rgba(138, 174, 186, 0.8) 54.55%, rgba(197, 222, 230, 0.8) 91.27%);
          }
          .listRanking-text,
          .listRanking-number {
            color: #99c0c0;
            font-weight: 700;
          }
        }
        &.listRanking-no3 {
          .listRanking-ordinal {
            background: linear-gradient(28.12deg, rgba(214, 134, 109, 0.8) 54.14%, rgba(253, 216, 143, 0.8) 92.17%);
          }
          .listRanking-text,
          .listRanking-number {
            color: #bda5a5;
            font-weight: 700;
          }
        }
      }
    }
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
  height: 546px;
  overflow-y: auto;
  padding-bottom: 0;
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
