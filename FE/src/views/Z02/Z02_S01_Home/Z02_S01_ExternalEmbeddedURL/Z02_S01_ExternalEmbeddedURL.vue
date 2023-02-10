<script>
export default {
  name: 'Z02_S01_ExternalEmbeddedURL',
  props: {
    data: [Object || Array]
  },
  data() {
    return {
      loader: false,
      externalEUrl: [],
      fullscreen: false,
      embed: null,
      idx: null
    };
  },
  watch: {
    data(e) {
      if (e && e.external_embedded_url.length > 0)
        this.externalEUrl = e.external_embedded_url.map((s) => ({ ...s, external_url: s.external_url.replace('watch?v=', 'embed/') }));
    }
  },
  methods: {
    setFullScreen(isFull, data, idx) {
      this.fullscreen = isFull;
      this.embed = data;
      this.idx = idx + 1;
    }
  }
};
</script>
<template>
  <!-- <div v-if="externalUrl.length > 0" class="externalLink">
    <ul>
      <li v-for="item of externalUrl" :key="item.external_url_link_id">
        <a :href="item.external_url" target="_blank">{{ item.external_url_link_name }}</a>
      </li>
    </ul>
  </div>
  <EmptyData v-if="externalUrl.length === 0" custom-class="custom-class-no-data w-100" /> -->
  <div class="bgBox-p">
    <div v-for="(item, index) of externalEUrl" :key="item.external_embedded_url_id" class="bgBox bgBox--change custom-tbreport">
      <div class="title">
        <div class="title-info">
          <h2 class="title-tlt">URL埋め込み レーポト{{ index + 1 }}</h2>
        </div>
        <div class="title-btn">
          <button
            class="btn btn--icon"
            @click="setFullScreen(true, { ...item, index: index + 1 }, index)"
            @touchstart="setFullScreen(true, { ...item, index: index }, index)"
          >
            <img src="@/assets/template/images/icon_maximize.svg" alt="" />
          </button>
        </div>
      </div>
      <div class="tbl-report">
        <embed type="text/html" :src="item?.external_url" width="100%" height="240px" />
      </div>
    </div>
  </div>
  <div v-if="fullscreen" class="materialZoom">
    <div class="materialZoom-content">
      <div class="materialZoom-header">
        <h2>URL埋め込み レーポト {{ idx }}</h2>
        <div class="materialZoom-close">
          <button class="btn" @click="setFullScreen(false)" @touchstart="setFullScreen(false)">
            <!-- <ImageSVG :src-image="'icon_close-x01.svg'" :alt-image="'icon_close-x01'" /> -->
            <img src="@/assets/template/images/x.svg" alt="" />
          </button>
        </div>
      </div>
      <div class="materialZoom-thumb">
        <embed type="text/html" :src="embed?.external_url" width="100%" height="100%" />
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import '../../../../assets/css/_animations.scss';
$viewport_tablet: 991px;
$viewport_mobile: 767px;
.page-zo {
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

  .custom-tbreport {
    padding: 6px 4px 12px 0px !important;
    height: 100%;
    .title {
      padding: 8px 22px 12px;
    }
  }
  .tbl-report {
    height: 245px;
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
}
.materialZoom {
  border-radius: 0;
  padding: 0;
  // background: #464545bf;
  background-color: rgba(0,0,0,.5);
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
  .materialZoom-content {
    width: 90%;
    height: 90%;
    margin: auto;
  }
  .materialZoom-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 24px;

    h2 {
      font-weight: 500;
      font-size: 1.5rem;
      color: #fff;
      padding: 16px;
    }
  }
  .materialZoom-close {
    .btn {
      padding: 0;
      .svg {
        fill: #ffffff;
      }
    }
  }
  .materialZoom-thumb {
    background: #f0f0f0;
    height: 80%;
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 90%;
    left: 0;
    right: 0;
    margin: auto;
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
        width: 90%;
        position: relative;
        left: 0;
        right: 0;
        margin: auto;
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
</style>
