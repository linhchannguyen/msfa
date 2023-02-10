<template>
  <div class="tab02_container">
    <div class="pptCunstomer">
      <div v-if="sliders?.length > 0" ref="Ppt-left" class="pptCunstomer--left">
        <!-- Slider -->
        <div :class="['lstThumb', sliders.length > 0 ? '' : 'thumb_hover']">
          <ul>
            <li v-for="(item, i) of sliders" :key="item" class="link-click">
              <span class="lstThumb-number">{{ i + 1 }}</span>
              <!--Image-->
              <div
                v-if="item.file_type === '10' && item.isHtml"
                v-slide-loading="item.isHtml && activePhoto === i ? loadImg : false"
                :class="['lstThumb-photo', activeSlide(i) ? 'active' : '']"
                @click.prevent="changeSlider(item, i)"
              >
                <img v-if="item.file_type === '10'" :id="`imgTab2${i}`" />
              </div>
              <div
                v-if="item.file_type === '10' && !item.isHtml"
                v-slide-loading="!item.isHtml ? loadImg : false"
                :class="['lstThumb-photo', activeSlide(i) ? 'active' : '']"
                @click.prevent="changeSlider(item, i)"
              >
                <!--Image-->
                <img v-if="item.file_type === '10' && checkThumbnail(item)" :src="getThumbnail(item)" />
                <img v-else :id="`imgTab2${i}`" />
              </div>
              <!--Video-->
              <div v-if="item.file_type === '20'" :class="['lstThumb-photo', activeSlide(i) ? 'active' : '']" @click.prevent="changeSlider(item, i)">
                <img :id="`thumnail${i}`" style="position: absolute" />
                <div class="backgroundVideo">
                  <img class="svg" src="@/assets/template/images/play.svg" alt="icon" style="width: 45px" />
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="block-btn-slide">
          <button type="button" :class="['btnCreate top']" @click.prevent="openModal_D01_S05_Slider(`top,${activePhoto}`)">
            <img class="svg" src="@/assets/template/images/plus.png" alt="" />
          </button>
          <button type="button" :class="['btnCreate bot']" @click.prevent="openModal_D01_S05_Slider(`bot,${activePhoto + 1}`)">
            <img class="svg" src="@/assets/template/images/plus.png" alt="" />
          </button>
        </div>
      </div>
      <div v-else class="pptCunstomer--left scrollbar event-click" @click="openModal_D01_S05_Slider(`top,${activePhoto}`)">
        <!-- Create Slider -->
        <div :class="['lstThumb', sliders.length > 0 ? '' : 'thumb_hover']">
          <div class="create-ppt">
            <span class="create-text">
              <i style="font-size: 30px; cursor: pointer; color: #448add; font-weight: bold" class="el-icon-plus"></i>
              追加</span
            >
          </div>
        </div>
      </div>
      <ul v-if="sliders.length > 0"></ul>
      <div class="pptCunstomer--right">
        <D01S05Editor
          ref="S05Editor"
          :loading-preview="loadingPreview"
          :off-edit-ppt="offEditPpt"
          :link-slide="linkSlide"
          @save-ppt="savePptInFe"
          @delete="deleteSlide"
          @html="getIframe"
        />
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`custom-dialog-document ${modalConfig.customClass}`"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="true"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="resetInfor"></component>
      </template>
    </el-dialog>
  </div>
</template>
<script>
// import EditorPlugin from '@/components/EditorPlugin';
import { ColorPicker } from 'vue-color-kit';
import 'vue-color-kit/dist/vue-color-kit.css';
import { markRaw } from 'vue';
import D01S05ChooseSlider from './D01_S05_ChooseSlider.vue';
import D01S05Editor from './D01_S05_Tab2_Editor.vue';
import domtoimage from 'dom-to-image';
import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.js';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.entry';
export default {
  name: 'Tab2',
  components: {
    // EditorPlugin
    // eslint-disable-next-line vue/no-unused-components
    ColorPicker,
    D01S05Editor
  },
  props: {
    offEdit: {
      type: Boolean,
      default: false,
      required: false
    },
    listSlide: {
      type: Array,
      default: null,
      required: false
    },
    usageId: {
      type: Object,
      default: null,
      required: false
    },
    changetab: {
      type: String,
      default: null,
      required: false
    },
    contents: {
      type: Array
    },
    availableOrgCd: {
      type: String,
      default: null,
      required: false
    }
  },
  emits: ['iframe', 'save-ppt-all', 'slide-loading'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      isLoading: false,
      dataConfig: {
        link: '',
        ediors: []
      },

      sliders: [],
      linkSlide: {
        isEdit: 0,
        slideNum: 0,
        content: [],
        document_name: '',
        document_id: '',
        file_type: '',
        file_url: ''
      },
      activePhoto: 0,
      showDelete: false,
      docPreview: null,
      image: '',
      offEditPpt: false,
      loadImg: false,
      showCreate: true,
      loadingPreview: false,
      image1: [],
      originSlide: null,
      originSlideContent: [],
      orgSelected: ''
    };
  },
  computed: {},
  watch: {
    changetab(tab) {
      var PptLeft = this.$refs['Ppt-left'];
      // eslint-disable-next-line eqeqeq
      if (tab == 2) {
        setTimeout(() => {
          this.privewIfram(null, false, 0);
          if (PptLeft)
            PptLeft.addEventListener('click', () => {
              this.$refs.S05Editor.closeAll();
            });
        }, 500);
      }
    },
    offEdit(v) {
      this.offEditPpt = v;
    },
    availableOrgCd(availableOrgCd) {
      this.orgSelected = availableOrgCd;
    },
    listSlide(listSlide) {
      this.sliders = listSlide;
      this.sliders.forEach((item, num) => {
        if (item.file_type === '10') {
          this.PNGpdf(item.file_url, num);
        } else {
          this.ImageMp4(item.file_url, num);
        }
      });
      setTimeout(() => {
        const index = this.sliders.findIndex((s, i) => i === 0);
        if (index !== -1) {
          this.changeSlider(this.sliders[index], index);
        }
      }, 1000);
    },
    usageId(value) {
      if (value.list_document_usage_id) {
        this.showCreate = false;
      } else {
        this.showCreate = true;
      }
    }
  },
  mounted() {
    this.linkSlide.link = '';
  },
  methods: {
    getThumbnail(item) {
      const content = item && item.content && item.content[0];
      const src = content ? content.thumbnail : '';
      return src;
    },
    checkThumbnail(item) {
      const content = item && item.content && item.content[0];
      const src = content ? content.thumbnail : null;

      return src ? true : false;
    },
    ImageMp4(url, num) {
      var src = url;
      var video = document.createElement('video');
      video.src = src;
      video.width = 360;
      video.height = 240;
      setTimeout(() => {
        var canvas = document.createElement('canvas');
        var img = document.getElementById('thumnail' + num);
        canvas.width = 360;
        canvas.height = 240;
        var context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        var dataURI = canvas.toDataURL('image/jpeg');
        img.src = dataURI;
      });
    },
    getDocument(url) {
      var PDFJS = pdfjsLib;
      return PDFJS.getDocument(url);
    },
    PNGpdf(url, i) {
      this.loadImg = true;
      const PDFJS = pdfjsLib;
      PDFJS.GlobalWorkerOptions.workerSrc = pdfjsWorker;
      const loadingTask = this.getDocument(url);
      loadingTask.promise
        .then(async (re) => {
          const pdf = await re;
          const totalPages = pdf.numPages;
          for (let pageNumber = 1; pageNumber <= totalPages; pageNumber++) {
            pdf.getPage(pageNumber).then((res) => {
              const page = res;
              var scale = 1;
              var viewport = page.getViewport({ scale: scale });
              var canvas = document.createElement('canvas');
              // Prepare canvas using PDF page dimensions
              var context = canvas.getContext('2d');
              canvas.height = viewport.height;
              canvas.width = viewport.width;

              // Render PDF page into canvas context
              var renderContext = { canvasContext: context, viewport: viewport };
              var renderTask = page.render(renderContext);
              var img = document.getElementById('imgTab2' + i);
              if (img) {
                if (viewport.width > viewport.height) {
                  img.className = 'scale100';
                } else {
                  img.className = 'scale50';
                }
                renderTask.promise
                  .then(async () => {
                    img.src = canvas.toDataURL('image/png');
                    img.style.position = 'absolute';
                    img.style.width = '100%';
                    img.style.height = '100%';
                  })
                  .finally(() => {
                    this.loadImg = false;
                  });
              }
            });
          }
        })
        .finally(() => {
          this.loadImg = false;
        });
    },

    privewIfram(preview, onConvert, i) {
      setTimeout(() => {
        var doc = document.getElementById(`imgTab2${i}`);
        this.docPreview = document.getElementById('preView');
        const Edit = document.getElementById('editbtn');
        this.loadImg = true;

        if (onConvert) {
          if (Edit) {
            Edit && Edit.style ? (Edit.style.display = 'none') : null;
          }
          if (doc) {
            doc.style.width = '100%';
            doc.style.heigh = '100%';
            if (doc) {
              try {
                this.$emit('slide-loading', this.loadImg);
                // let imgs = [];
                let img = new Image();
                domtoimage
                  .toPng(preview || this.docPreview, { cacheBust: true })
                  .then(function (dataUrl) {
                    img.crossOrigin = 'Anonymous';
                    img.src = dataUrl;
                    // const list = y.src.length > 0;
                    img.style.position = 'absolute';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    doc.src = dataUrl;
                  })
                  .catch(function () {
                    this.loadImg = false;
                  })
                  .finally(() => {
                    Edit && Edit.style ? (Edit.style.display = 'block') : null;
                    this.loadImg = false;
                    this.$emit('slide-loading', this.loadImg);
                  });
              } catch (err) {
                // console.log(err);
              }
            }
          }
        } else {
          this.loadImg = false;
        }
      });
    },

    /** API */
    /** MODAL D01 S05  */

    openModal_D01_S05_Slider(i) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '追加ページ選択',
        isShowModal: true,
        component: markRaw(D01S05ChooseSlider),
        width: '65%',
        customClass: 'custom-dialog',
        props: { indexAdd: i, orgSelected: this.orgSelected }
      };
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    addSlide(e) {
      const indexAdd = e.indexAdd.split(',')[1];
      const docPage = e.data.sort((a, b) => b.original_document_page_num - a.original_document_page_num);

      docPage.forEach((s) => {
        indexAdd ? this.sliders.splice(Number(indexAdd), 0, s) : this.sliders.push(s);
      });
    },
    onResultModal(e) {
      // eslint-disable-next-line eqeqeq
      if (e && e.screen == 'D01_S05') {
        this.addSlide(e);
        this.orgSelected = this.orgSelected ? this.orgSelected : e.orgSelected;
        const thumbnail = e.data[0];
        this.coverSlide = thumbnail;
        setTimeout(() => {
          this.sliders.forEach((item, num) => {
            if (item.file_type === '10') {
              if (item.isHtml === false) {
                this.PNGpdf(item.file_url, num);
              }
            } else {
              this.ImageMp4(item.file_url, num);
            }
          });
          if (e.indexAdd.split(',')[0] === 'top') {
            if (this.activePhoto === -1) this.activePhoto = 0;
            this.changeSlider(this.sliders[this.activePhoto], this.activePhoto);
            this.activeSlide(this.activePhoto);
          } else {
            this.changeSlider(this.sliders[this.activePhoto + 1], this.activePhoto + 1);
            this.activeSlide(this.activePhoto + 1);
          }
        });
        const slider = this.sliders.map((s, i) => {
          const arr = s && s.content ? s.content : [];
          const new_content = arr.map((s) => ({ ...s }));
          const data = {
            document_name: s.document_name,
            content: new_content || [],
            file_url: s.file_url,
            page_num: i + 1,
            // link: img(i),
            thumbnail: s.thumbnail,
            original_document_id: s?.original_document_id,
            original_document_page_num: s?.original_document_page_num,
            list_document_usage_id: s.list_document_usage_id
          };
          return data;
        });
        this.$emit('save-ppt-all', slider);

        this.onCloseModal();
      } else {
        this.onCloseModal();
      }
    },

    getIframe(e) {
      this.docPreview = e;
    },
    // click slider
    changeSlider(e, i) {
      this.linkSlide.content = [];

      if (this.checkDateWasEdit()) {
        this.confirmSave(e, i);
      } else {
        const result = e && e.content ? e.content : [];
        const file_url = e && e.file_url;
        this.activePhoto = this.sliders.findIndex((s, index) => (s && s.file_url) === file_url && index === i);
        if (this.activePhoto === -1) this.activePhoto = this.sliders.findIndex((s) => s.file_url === e.file_url);
        const content = e && e.content && e.content[this.activePhoto];
        if (e)
          this.linkSlide = {
            file_url: content?.file_url || (e && e.file_url) || '',
            isEdit: e && e.cover_flag,
            isEditPpt: e && e.cover_flag === 0 ? false : true,
            thumbnail: (e && e.thumbnail) || (content && content.thumbnail),
            slideNum: this.activePhoto + 1, // update 27/01,
            original_document_page_num: e && e.original_document_page_num,
            original_document_id: e && e.original_document_id,
            content: result,
            isView: e && e.cover_flag === 1 ? true : false,
            document_name: e.document_name || this.usageId?.document_name,
            document_id: e.document_id || this.usageId.document_id,
            file_type: e.file_type || '10',
            index: this.activePhoto + 1,
            time_lable_flag: e?.time_lable_flag
          };
        this.setUpCreateSlide(i);
        var img = document.getElementById(this.linkSlide.index);
        if (img) img.src = '';
        this.activeSlide(i);
      }
    },
    setUpCreateSlide(i) {
      const scrollEvent = document.querySelector('.lstThumb');
      let slideClick = document.querySelectorAll('.link-click')[i];
      const btnSliteCreate = document.querySelector('.block-btn-slide');
      const offset = slideClick?.offsetTop - scrollEvent?.scrollTop ? slideClick?.offsetTop - scrollEvent?.scrollTop : 15;
      if (btnSliteCreate) {
        btnSliteCreate.style.top = `${offset}px`;
        if (offset > 0) {
          btnSliteCreate.style.display = 'block';
        }
        btnSliteCreate.querySelector('.top').style.display = 'block';
        btnSliteCreate.querySelector('.bot').style.display = 'block';
        scrollEvent.addEventListener('scroll', () => {
          const offset = slideClick?.offsetTop - scrollEvent?.scrollTop;
          btnSliteCreate.style.top = `${offset}px`;
          // if (scrollEvent.clientHeight > offset && offset > 502) {
          //   btnSliteCreate.querySelector('.bot').style.display = 'none';
          // } else {
          //   btnSliteCreate.querySelector('.bot').style.display = 'block';
          // }
          if (scrollEvent.clientHeight < offset && offset > 690) {
            btnSliteCreate.querySelector('.top').style.display = 'none';
          } else {
            btnSliteCreate.querySelector('.top').style.display = 'block';
          }
        });
      }
    },
    deleteSlide(slide) {
      const index = this.sliders.findIndex((s) => s.page_num === slide.slideNum);
      // remove
      this.sliders.splice(index, 1);
      // reload set page number
      if (this.linkSlide.slideNum === slide.slideNum) {
        this.linkSlide = {
          thumbnail: '',
          file_url: '',
          isEdit: 1,
          slideNum: 0,
          document_name: '',
          isView: false,
          document_id: '',
          file_type: '',
          time_lable_flag: 0
        };
      }
      // review
      if (this.sliders[index]) {
        this.changeSlider(this.sliders[index], index);
        // this.PNGpdf(this.sliders[ix].file_url, this.sliders[ix].original_document_page_num);
      } else {
        this.changeSlider(this.sliders[0], 0);
      }

      if (this.sliders.length === 0) {
        this.orgSelected = '';
      }
    },
    activeSlide(i) {
      return this.activePhoto === i;
    },

    savePptInFe(e) {
      // Save text slide
      // eslint-disable-next-line eqeqeq
      const index = this.sliders.findIndex((s, i) => s.file_url === e.file_url && i === e.index - 1);

      // const img = (i) => document.getElementById(`imgTab2${i}`).src;
      if (this.sliders[index]) {
        this.sliders[index].content = e.content;
        this.sliders[index].isHtml = true;
      }
      const slider = this.sliders.map((s, i) => {
        const arr = s && s.content ? s.content : [];
        const new_content = arr.map((s) => ({ ...s }));
        const data = {
          document_name: s.document_name,
          content: new_content || [],
          file_url: s.file_url,
          page_num: i + 1,
          // link: img(i),
          thumbnail: s.thumbnail,
          original_document_id: s?.original_document_id,
          original_document_page_num: s?.original_document_page_num,
          list_document_usage_id: s.list_document_usage_id
        };
        return data;
      });
      this.privewIfram(this.docPreview, true, e.index - 1);

      setTimeout(() => {
        this.linkSlide.content = slider[index]?.content.map((s) => ({ ...s, origin: s }));

        // this.changeSlider(slider[index], e.index - 1);
      }, 1000);
      this.$emit('save-ppt-all', slider);
    },

    confirmSave(e, i) {
      this.modalConfirmMove(() => {
        sessionStorage.removeItem('D01S05');
        this.changeSlider(e, i);
        this.onCloseModal();
      });
    },
    modalConfirmMove(callBack) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { mode: 1, textCancel: 'いいえ', params: { callBack } }
      };
    },
    resetInfor(e) {
      e.callBack();
    },
    checkDateWasEdit() {
      const data = sessionStorage.getItem('D01S05');
      return data != null ? true : false;
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;

.tab02_container {
  position: absolute;
  background-color: #ffffff;
  width: 100%;
  height: 100%;
  .pptCunstomer {
    height: 100%;
    width: 100%;
    position: relative;
    display: flex;
    flex-wrap: wrap;
    padding: 5px;

    @media (max-width: $viewport_tablet) {
      .create-ppt2 {
        width: 23% !important;
      }
    }
    .create-ppt2 {
      position: absolute;
      height: 140px;
      position: absolute;
      top: 6%;
      right: -45px;
      align-items: right;
      justify-content: right;
      display: flex;
      z-index: 1;
    }
    &--left {
      margin-top: 15px;
      position: relative;
      width: 23%;
      bottom: 0;
      height: 97%;
      padding: 5px 7px 0 0;
      justify-content: center;
      align-items: center;
      display: flex;

      .lstThumb {
        padding: 15px 0px 0 0px;
        max-height: 7;
        width: 85%;
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background: rgb(230, 230, 230);
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        border-radius: 0px 8px 8px 0px;
        overflow-y: auto;
        overflow-x: hidden;
        transition: box-shadow 0.55s, background 0.55s, transform 0.55s ease;

        ul {
          li {
            display: flex;
            flex-wrap: wrap;
            padding-bottom: 15px;
            .lstThumb-photo {
              position: relative;
              width: 73%;
              height: 140px;
              display: flex;
              align-items: center;
              justify-content: center;
              overflow: hidden;
              border: 1px solid #e3e3e3;
              box-shadow: 2px 2px 5px rgba(145, 161, 171, 0.6);
              border-radius: 4px;
              cursor: pointer;
              &.lstThumb-photo--active {
                border: 1px solid #448add;
              }
              img {
                height: 100%;
              }
              &:hover {
                outline: 2px solid #448add;
                box-shadow: 4px 4px 10px rgba(145, 161, 171, 0.6);
              }
              &.active {
                outline: 3px solid #448add;
                box-shadow: 4px 4px 10px rgba(145, 161, 171, 0.6);
                pointer-events: none;
              }
            }

            .lstThumb-number {
              width: 15%;
              text-align: center;
              font-size: 1.2rem;
              height: 140px;
              font-weight: 700;
            }
          }
        }
      }
      .thumb_hover {
        &:hover {
          background: #eef6ff;
          transition: box-shadow 0.55s, background 0.55s, transform 0.55s ease;
        }
      }
      .create-ppt {
        height: 100%;
        width: 100%;
        justify-content: center;
        display: flex;
        align-items: center;
        .create-text {
          font-size: 30px;
          cursor: pointer;
          color: #448add;
          font-weight: bold;
          img {
            width: 28px;
            margin-bottom: 5px;
          }
        }
      }

      .thumbBig {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
      }
      @media (max-width: $viewport_tablet) {
        height: 99%;
        .lstThumb {
          max-height: 990px !important;
        }
        .create-ppt2 {
          width: 23% !important;
        }
      }
    }
    &--right {
      position: absolute;
      width: 79%;
      height: 100%;
      right: 0px;
    }
  }
  .event-click {
    cursor: pointer;
  }
  @keyframes widthAnimation {
    from {
      width: 40px;
    }
    to {
      width: 45px;
    }
  }
  .block-btn-slide {
    position: absolute;
    right: 4rem;
    margin-top: -8px;
    z-index: 1;
    height: 180px;
    .bot {
      margin-top: 123px;
      display: block;
      cursor: pointer;
      img {
        &:active {
          animation-duration: 0.2s;
          animation-name: widthAnimation;
        }
      }
    }
    .plus-top {
      margin-top: 9em;
    }
    .top {
      display: block;
      margin-top: -15px;
      cursor: pointer;
      img {
        &:active {
          animation-duration: 0.2s;
          animation-name: widthAnimation;
        }
      }
    }
  }
}
.backgroundVideo {
  position: absolute;
  width: 100%;
  background: rgba(252, 249, 249, 0.342);
  opacity: 40;
  height: 100%;
  text-align: center;
}
.scale100 {
  transform: scale3d(1, 1, 1);
  image-rendering: -webkit-optimize-contrast;
}
.scale50 {
  object-fit: scale-down;
  image-rendering: -webkit-optimize-contrast;
}
</style>
