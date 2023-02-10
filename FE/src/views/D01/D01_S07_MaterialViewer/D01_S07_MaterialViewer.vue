<template>
  <!-- title: 資材ビューワ -->
  <!-- width: '60%' || 60rem -->
  <!-- props: documentId -->
  <div v-if="!fullScreen" v-loading="isLoading" class="modal-materialViewer modal-body-D01S07">
    <div id="msfa-notify-D01S07"></div>
    <div class="materialViewer-title">
      <h2 v-if="document_type" class="tlt">
        <span class="tlt-item"><ImageSVG :src-image="returnIcon()" :alt-image="'icon_pdf-manag'" /></span>
        {{ document_name }}
      </h2>
      <button class="btn btn--icon" type="button" @click="setFullScreen"><ImageSVG :src-image="'icon_maximize.svg'" :alt-image="'icon_maximize'" /></button>
    </div>
    <div class="slideViewer">
      <div class="slideViewer-info">
        <div class="lstThumb scrollbar">
          <ul>
            <li v-for="item in contenDocumentList" :key="item.page_num" @click="setThumb(item)" @touchstart="setThumb(item)">
              <span :class="`lstThumb-number ${selectedThumb?.page_num === item.page_num ? 'lstThumb-number--active' : ''}`">{{ item.page_num }}</span>
              <div :class="`lstThumb-photo ${selectedThumb?.page_num === item.page_num ? 'lstThumb-photo--active' : ''}`">
                <img :src="item.thumbnail" alt="thumb" />
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="slideViewer-content">
        <div class="thumbBig">
          <iframe
            v-if="selectedThumb.file_type === '10' && !selectedThumb.cover_flag"
            :key="Math.random()"
            width="100%"
            height="80%"
            frameborder="0"
            :src="selectedThumb.file_url + toolbars1"
          />
          <video v-else-if="selectedThumb.file_type === '20'" class="videos-thumb" controls width="100%" height="100%" frameborder="0">
            <source :src="selectedThumb.file_url + toolbars1" type="video/mp4" />
          </video>
          <img v-else :src="selectedThumb.thumbnail" alt="thumb" />
        </div>
      </div>
    </div>
    <div class="block-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelBtn" @touchstart="cancelBtn">閉じる</button>
    </div>
  </div>
  <!-- full screen -->
  <div v-else class="materialZoom">
    <div class="materialZoom-close">
      <button type="button" class="btn" @click="setFullScreen"><ImageSVG :src-image="'icon_close-x01.svg'" :alt-image="'icon_close-x01'" /></button>
    </div>
    <div class="materialZoom-thumb">
      <span class="perPage">{{ perPage + '/' + contenDocumentList.length }}</span>
      <el-carousel height="100%" class="slideThumb" :loop="false" :initial-index="activeCarousel" :autoplay="false" @change="handleCarousel">
        <el-carousel-item v-for="item in contenDocumentList" :key="item.page_num - 1">
          <div class="slideThumb-photo">
            <iframe
              v-if="item.file_type === '10' && !item.cover_flag"
              width="100%"
              height="80%"
              frameborder="0"
              allowfullscreen
              :src="item.file_url + toolbars"
            />
            <video v-else-if="item.file_type === '20'" class="videos-thumb" controls width="100%" height="100%" frameborder="0">
              <source :src="item.file_url + toolbars1" type="video/mp4" />
            </video>
            <img v-else :src="item.thumbnail" alt="thumb" :class="item.scaleClass" />
          </div>
        </el-carousel-item>
      </el-carousel>
    </div>
  </div>
</template>

<script>
import D01_S07_Service from '@/api/D01/D01_S07_MaterialViewerServices';
export default {
  name: 'D01_S07_MaterialViewer',
  props: {
    documentId: {
      type: Number || String,
      default: 159,
      required: true
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      isLoading: false,
      fullScreen: false,
      selectedThumb: {
        cover_html: '',
        created_at: '',
        created_by: '',
        document_id: null,
        list_document_usage_id: null,
        original_document_id: null,
        original_document_page_num: null,
        page_num: null,
        proxy_created_by: '',
        proxy_updated_by: '',
        thumbnail: '',
        updated_at: '',
        updated_by: ''
      },
      document_name: '',
      document_type: '10',
      file_type: '',
      perPage: 1,
      toolbars: '#view=FitH&zoom=FitH,75&toolbar=0&navpanes=0&scrollbar=0&pagemode=none',
      toolbars1: '#view=Fit&zoom=FitH,75&toolbar=0&navpanes=0&scrollbar=0&pagemode=none',
      contenDocumentList: [],
      activeCarousel: 0
    };
  },
  mounted() {
    this.getListDocument(this.documentId);
  },
  methods: {
    // define func
    returnIcon() {
      let id = 10;
      this.document_type === '20' ? (id = 30) : this.document_type === '10' && this.file_type === '20' ? (id = 20) : (id = 10);
      const obj = {
        10: 'icon_pdf-manag.svg',
        20: 'icon_film.svg',
        30: 'icon_document_spanner_small.svg'
      };
      return obj[id];
    },
    handleCarousel(value) {
      this.perPage = value + 1;
    },
    setThumb(item) {
      this.selectedThumb = item;
      this.activeCarousel = item.page_num - 1;
      this.perPage = item.page_num;
    },
    setFullScreen() {
      this.fullScreen = !this.fullScreen;
      const lstThumb = document?.querySelector('.lstThumb');
      if (lstThumb !== null) {
        const img = lstThumb?.querySelectorAll('ul > li > div > img');
        let imgs = [...img].map((s) => ({
          hight: s.naturalHeight,
          width: s.naturalWidth,
          scale: s.naturalWidth > s.naturalHeight ? (s.naturalHeight > 600 ? '' : 'scale100') : 'scale50'
        }));
        this.contenDocumentList = this.contenDocumentList.map((el, i) => ({
          ...el,
          scaleClass: imgs[i].scale
        }));
      }
    },
    cancelBtn() {
      this.$emit('onFinishScreen', { screen: 'D01_S07' });
    },
    decodeData(sample) {
      let obj = sample;
      if (typeof sample === 'string') {
        try {
          // eslint-disable-next-line no-undef
          let buffer = Buffer.from(sample, 'base64').toString('utf8');
          obj = JSON.parse(buffer)[0];
        } catch (err) {
          // console.log(err);
        }
      }
      return obj;
    },
    // call api
    getListDocument(id) {
      this.isLoading = true;
      D01_S07_Service.getListDocumentService(id)
        .then((res) => {
          const dataRes = res.data.data;
          this.document_name = dataRes.info_document?.document_name || '';
          this.document_type = dataRes.info_document?.document_type || '';
          this.file_type = dataRes.info_document.file_type;
          if (this.document_type !== '10')
            this.contenDocumentList = dataRes.list_document.map((item) => ({
              ...item,
              thumbnail: this.decodeData(item.cover_html) ? this.decodeData(item.cover_html).thumbnail : item.thumbnail
            }));
          else this.contenDocumentList = dataRes.list_document;
          if (dataRes.list_document.length > 0) this.selectedThumb = this.contenDocumentList[0];
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-D01S07', idNodeNotify: 'msfa-notify-D01S07' });
        })
        .finally(() => (this.isLoading = false));
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet1: 1024px;
.scale100 {
  transform: scale3d(1.5, 1.3, 1);
  object-fit: scale-down;
  image-rendering: -webkit-optimize-contrast;
}
.scale50 {
  object-fit: scale-down;
  image-rendering: -webkit-optimize-contrast;
}
.modal-materialViewer {
  .materialViewer-title {
    display: flex;
    justify-content: space-between;
    padding: 0 0 12px 32px;
    align-items: center;
    .tlt {
      display: flex;
      font-size: 1.5rem;
      font-weight: 700;
      .tlt-item {
        min-width: 21px;
        margin: -2px 10px 0 0;
      }
    }
  }
  .slideViewer {
    display: flex;
    flex-wrap: wrap;
    .slideViewer-info {
      background: #fff;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 0px 8px 8px 0px;
      width: 284px;
      padding: 5px 7px 0 0;
    }
    .slideViewer-content {
      width: calc(100% - 304px);
      height: 600px;
      overflow: hidden;
      margin-left: 20px;
      background: #b7c3cb;
      border: 1px solid #e3e3e3;
      border-radius: 4px;
      @media (max-height: 768px) {
        height: 440px;
      }
    }
  }
  .lstThumb {
    padding: 15px 20px 0 0;
    max-height: 585px;
    @media (max-height: 768px) {
      height: 435px;
    }
    ul {
      li {
        display: flex;
        flex-wrap: wrap;
        padding-bottom: 15px;
        .lstThumb-photo {
          width: 200px;
          height: 100px;
          display: flex;
          align-items: center;
          justify-content: center;
          overflow: hidden;
          border: 1.5px solid #e3e3e3;
          box-shadow: 2px 2px 5px rgba(145, 161, 171, 0.6);
          border-radius: 4px;
          cursor: pointer;
          &.lstThumb-photo--active {
            border: 1.5px solid #448add;
          }
          img {
            max-height: 100px;
          }
        }
        .lstThumb-number {
          width: calc(100% - 200px);
          padding-right: 10px;
          text-align: right;
          font-size: 1rem;
          font-weight: 700;
          &.lstThumb-number--active {
            color: #448add;
          }
        }
      }
    }
  }
  .thumbBig {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    img {
      max-height: 440px;
    }
  }
  .materialViewer-btn {
    @media (max-width: $viewport_tablet1) {
      margin-top: 10px;
    }
    margin-top: 20px;
    text-align: center;
    .btn {
      width: 180px;
    }
  }
}
/* ** materialZoom ** */
.materialZoom {
  border-radius: 0;
  padding: 0;
  // background: #41586f;
  background-color: rgba(0, 0, 0, 0.5);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10000;
  .materialZoom-close {
    width: 42px;
    height: 42px;
    position: absolute;
    z-index: 1;
    top: 24px;
    right: 24px;
    .btn {
      border-radius: 50%;
      padding: 0;
      width: 100%;
      height: 100%;
      background: #fff;
    }
  }
  .materialZoom-thumb {
    height: 100%;
    position: relative;
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
/** View HTML */
.drag-item {
  position: absolute;
  z-index: 9;
  text-align: center;
}
.videos-thumb {
  width: 100%;
  height: 100%;
}

.modal-body-D01S07 {
  margin: -15px;

  .btn.btn--icon {
    width: 38px;
    height: 38px;
  }
}
</style>
