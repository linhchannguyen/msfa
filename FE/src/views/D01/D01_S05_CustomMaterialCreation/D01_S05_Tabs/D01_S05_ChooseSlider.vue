<template>
  <!-- title: 資材ビューワ -->
  <div class="modal-body modal-materialViewer modal-body-D01S05-ChooseSlider">
    <div id="msfa-notify-D01S05-ChooseSlider"></div>
    <div class="materialViewer-title">
      <div class="basicForm">
        <label class="basicForm-label"> 資材 </label>
        <div class="basicForm-control">
          <div class="form-icon icon-right">
            <span class="icon icon--cursor" @click="openModalZ05S05()" @touchstart="openModalZ05S05()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
            </span>
            <div v-if="data" class="form-control d-flex align-items-center">
              <div class="block-tags">
                <el-tag :key="data" class="m-0 el-tag-custom">
                  {{ data.document_name }}
                </el-tag>
              </div>
            </div>
            <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
          </div>
        </div>
      </div>
    </div>
    <div class="slideViewer">
      <div class="slideViewer-info">
        <div class="lstThumb scrollbar">
          <ul>
            <li v-for="(item, index) of sliders" :key="index" @click="priviewClick(item)" @touchstart="priviewClick(item)">
              <div class="select-thumb-1">
                <div :class="['lstThumb-number', item?.checked ? 'lstThumb-number--active' : '']">{{ index + 1 }}</div>
                <div class="checked-btn">
                  <button :class="['btn btn-checkBox', item?.checked ? 'checked' : '']">
                    <svg
                      version="1.1"
                      xmlns="http://www.w3.org/2000/svg"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                      x="0px"
                      y="0px"
                      viewBox="0 0 76.887 75.552"
                      style="enable-background: new 0 0 76.887 75.552"
                      xml:space="preserve"
                    >
                      <g id="Layer_15">
                        <polygon
                          :style="{ fill: item?.checked ? '#FFFFFF' : '#28A470' }"
                          points="34.373,49.541 76.887,5.889 70.739,0 28.426,43.458 6.078,20.632 0,26.585 22.488,49.557 
		22.488,49.557 28.517,55.552 28.426,55.467 28.517,55.552 34.373,49.541 	"
                        />
                      </g>
                      <g id="Layer_1"></g>
                    </svg>
                  </button>
                </div>
              </div>
              <div v-slide-loading="isLoading" :class="['lstThumb-photo', item.checked ? 'lstThumb-photo--active' : '']" style="position: relative">
                <img v-show="item.file_type === '10'" :id="`imgChoose${index}`" />
                <!--Video-->
                <img v-show="item.file_type === '20'" :id="`thumnailChoose${index}`" style="position: absolute" />
                <div v-show="item.file_type === '20'" class="backgroundVideo">
                  <img class="svg" src="@/assets/template/images/play.svg" alt="icon" style="width: 35px" />
                </div>
                <!-- <div v-html="item.svg"></div> -->
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="slideViewer-content">
        <div v-if="sliders.length > 0" class="thumbBig">
          <iframe
            v-show="priview.file_type === '10'"
            :src="priview.file_url + '#zoom=fit,50,70,100&toolbar=0&navpanes=0&embedded=false&scrollbar=0'"
            type="application/pdf"
            allowfullscreen
            width="100%"
            height="100%"
          ></iframe>
          <iframe
            v-show="priview.file_type === '20'"
            :src="priview.file_url + '?autoplay=1'"
            allowfullscreen="true"
            webkitallowfullscreen="true"
            mozallowfullscreen="true"
            width="100%"
            height="100%"
          ></iframe>
        </div>
        <div v-else class="thumbBig">
          <div class="slideViewer-empty">
            <EmptyData title="該当するデータがありません。" in-width="300px" icon="amico_D01S05" custom-class="nodata-ppt-2" />
          </div>
        </div>
      </div>
    </div>
    <div class="materialViewer-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">閉じる</button>
      <button type="button" class="btn btn-primary" :disabled="disabledAdd" @click="onCreateSlide()">追加</button>
    </div>
  </div>
  <!--  -->
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog custom-dialog-pd-none"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="true"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import Z05S05ChoiceOfMasterialn from '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial';
import D01S05CustomMaterialCreationServices from '@/api/D01/D01_S05_CustomMaterialCreationService';
import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.js';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.entry';
export default {
  props: {
    indexAdd: {
      type: String,
      default: '',
      required: false
    },
    orgSelected: {
      type: String,
      default: '',
      required: false
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      data: null,
      isLoading: false,
      viewLoading: true,
      sliders: [],
      priview: '',
      disabledAdd: true,
      paramsZ05S05: {
        subMaterialSelectableFlag: 1, // 1: allow select child material || 0: not allow  (require)
        customMaterialFlag: 0, // 1: allow choice カスタム資材 || 0: not allow (require)
        initMaterials: [],
        materialType: '10',
        document_name: '',
        productCode: '',
        medicalSubjectsGroupCode: '',
        safetyInformationFlag: '',
        offLabelInformationFlag: '',
        date: null
      }
    };
  },
  mounted() {
    window.$('#materialViewer').modal('show');
    window.$('#materialZoom').modal('show');
  },
  methods: {
    ImageMp4(url, num) {
      var src = url;
      var video = document.createElement('video');

      video.src = src;
      video.width = 360;
      video.height = 240;

      setTimeout(() => {
        var canvas = document.createElement('canvas');
        var img = document.getElementById('thumnailChoose' + num);
        canvas.width = 360;
        canvas.height = 240;
        var context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        var dataURI = canvas.toDataURL('image/jpeg');
        img.src = dataURI;
        this.isLoading = false;
      });
    },
    pdfjsLib(url) {
      var PDFJS = pdfjsLib;

      return PDFJS.getDocument(url);
    },
    PNGpdf(url, imageNum) {
      var PDFJS = pdfjsLib;

      PDFJS.GlobalWorkerOptions.workerSrc = pdfjsWorker;
      var loadingTask = this.pdfjsLib(url);
      loadingTask.promise.then(function (pdf) {
        var totalPages = pdf.numPages;
        for (let pageNumber = 1; pageNumber <= totalPages; pageNumber++) {
          pdf.getPage(pageNumber).then(function (page) {
            var scale = 2;
            var viewport = page.getViewport({ scale: scale });

            var canvas = document.createElement('canvas');
            var img = document.getElementById('imgChoose' + imageNum);

            // Prepare canvas using PDF page dimensions
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            // Render PDF page into canvas context
            var renderContext = { canvasContext: context, viewport: viewport };

            var renderTask = page.render(renderContext);
            renderTask.promise.then(function () {
              img.src = canvas.toDataURL('image/png');
            });
          });
        }
      });
    },
    openModalZ05S05() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材選択',
        isShowModal: true,
        component: markRaw(Z05S05ChoiceOfMasterialn),
        width: '70rem',
        props: {
          customMaterialFlag: 0,
          mode: 'single',
          subMaterialSelectableFlag: 1,
          params: {
            ...this.paramsZ05S05,
            initMaterials: [this.data?.document_id]
          },
          screen: 'D01_S05',
          orgSelected: this.orgSelected
        }
      };
    },
    onResultModal(e) {
      // eslint-disable-next-line eqeqeq
      if (e?.screen == 'Z05_S05') {
        this.paramsZ05S05 = { ...this.paramsZ05S05, ...e.filterData };
        this.data = e.dataMasterialSelected[0];
        this.isLoading = true;

        this.getDocumentById();
        this.onCloseModal();
      }
      this.onCloseModal();
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    closeModal() {
      this.$emit('onFinishScreen', null);
    },

    getDocumentById() {
      D01S05CustomMaterialCreationServices.contentOriginalDocumentService(this.data?.document_id)
        .then((res) => {
          if (res) {
            const data = res?.data.data;
            this.sliders = data.map((s) => {
              const item = { ...s, isView: s.cover_flag === 1 ? true : false };
              return item;
            });
            this.priview = this.sliders.length > 0 ? this.sliders[0] : [];
            this.sliders.forEach((item, num) => {
              if (item.file_type === '10') {
                this.PNGpdf(item.file_url, num);
              } else {
                this.ImageMp4(item.file_url, num);
              }
            });
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err?.response?.data?.message,
            type: 'error',
            classParent: 'modal-body-D01S05-ChooseSlider',
            idNodeNotify: 'msfa-notify-D01S05-ChooseSlider'
          });
        })
        .finally(() => (this.isLoading = false));
    },
    priviewClick(item) {
      this.priview = item;
      // this.priviewPDF(this.priview.file_url, 1, 'thumb');
      if (item.checked) {
        item.checked = false;
      } else {
        item.checked = true;
      }
      const unCheck = this.sliders.filter((s) => s.checked === true);
      if (unCheck.length === 0) {
        this.disabledAdd = true;
      } else {
        this.disabledAdd = false;
      }
    },
    // https://thumbs.dreamstime.com/z/farm-animals-24414133.jpg
    // https://thumbs.dreamstime.com/z/farm-animals-cow-sheep-chickens-hen-cock-horse-group-farmland-collage-179260089.jpg
    onCreateSlide() {
      let result = this.sliders.filter((s) => s.checked === true);
      const img = (i) => document.getElementById(`imgChoose${i}`);
      const arr = result.map((s, i) => {
        return { ...s, isHtml: false, index: i + 1, thumbnail: img(s.original_document_page_num - 1) && img(s.original_document_page_num - 1).src };
      }); //hard
      this.$emit('onFinishScreen', {
        screen: 'D01_S05',
        data: arr,
        indexAdd: this.indexAdd,
        orgSelected: this.sliders.filter((s) => s.available_org_cd)[0]?.available_org_cd
      });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet1: 1024px;
.modal-materialViewer {
  .materialViewer-title {
    display: flex;
    justify-content: space-between;
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
      width: 23%;
      padding: 5px 7px 0 0;
    }
    .slideViewer-content {
      width: 75% !important;
      height: 550px;
      overflow: hidden;
      margin-left: 2%;
      margin-top: 10px;
      background: #ffffff;
      border: 1px solid #e3e3e3;
      border-radius: 4px;
    }
  }
  .lstThumb {
    padding: 25px 10px 0 13px;
    max-height: 540px;
    ul {
      li {
        display: flex;
        padding-bottom: 15px;
        justify-content: center;
        .lstThumb-photo {
          width: 9rem;
          height: 120px;
          display: flex;
          align-items: center;
          justify-content: center;
          overflow: hidden;
          border: 1px solid #e3e3e3;
          box-shadow: 2px 2px 5px rgba(145, 161, 171, 0.6);
          border-radius: 4px;

          cursor: pointer;
          &.lstThumb-photo--active {
            outline: 2px solid #28a470;
          }
          img {
            max-height: 130px;
          }
        }
        .lstThumb-number {
          padding-right: 10px;
          text-align: center;
          font-size: 1.2rem;
          font-weight: 700;
        }
        .lstThumb-number--active {
          color: #28a470;
        }
        .checked-btn {
          height: 70%;
          width: 100%;
          display: flex;
          justify-content: center;
          align-items: center;
          padding-right: 10px;
        }
      }
    }
  }
  .thumbBig {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    .slideViewer-empty {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
      border: 1px solid #e3e3e3;
      border-radius: 4px;
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
      margin-left: 13px;
    }
  }
}

/* materialZoom  */
.materialZoom {
  border-radius: 0;
  padding: 0;
  // background: #41586f;
  background-color: rgba(0, 0, 0, 0.5);
  position: relative;
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
    .slideThumb {
      height: 100%;
      .el-carousel__item {
        display: flex;
        justify-content: center;
        align-items: center;
      }
      img {
        height: 100%;
      }
    }
  }
}
.custom-dialog .el-dialog__body {
  padding: 10px !important;
}
.basicForm {
  display: flex;
  position: relative;
  justify-content: left;
  align-items: center;
  width: 100%;
  button {
    margin: 5px auto;
    position: absolute;
    bottom: 20px;
    right: 0px;
  }
  .basicForm-label {
    padding-right: 10px;
    font-size: 16px;
    margin-bottom: 15px;
    font-weight: bold;
  }
  .basicForm-control {
    margin-bottom: 15px;
    width: 25rem;
    .dateTime {
      display: block;
      flex-wrap: wrap;
      margin-left: -24px;
      li {
        padding-left: 24px;
        width: 50%;
      }
    }
  }
}
.select-thumb-1 {
  position: relative;
  width: 40%;
  .btn-checkBox {
    border-radius: 55px 55px 55px 55px;
    width: 45px;
    background: #ffffff;
    color: #28a470;
    border: 1px solid #d8d8d8;
    height: 45px;
    &.checked {
      background: #28a470;
      color: #fff;
    }
    &:hover {
      border: 1px solid #28a470;
      box-shadow: 2px 2px 5px rgba(145, 161, 171, 0.6);
    }
    svg {
      height: 15px !important;
    }
  }
}
.lstThumb-photo {
  width: 60%;
}
::-webkit-scrollbar {
  width: 8px;
  height: 10px;
  display: none;
}
::-webkit-scrollbar-button:start:decrement,
::-webkit-scrollbar-button:end:increment {
  height: 30px;
  background-color: transparent;
}
::-webkit-scrollbar-track-piece {
  background-color: #3b3b3b;
  -webkit-border-radius: 16px;
}
::-webkit-scrollbar-thumb:vertical {
  height: 50px;
  background-color: #666;
  border: 1px solid #eee;
}
.backgroundVideo {
  position: absolute;
  width: 100%;
  background: rgba(252, 249, 249, 0.342);
  opacity: 40;
  text-align: center;
}
</style>
