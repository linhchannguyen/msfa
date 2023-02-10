<template>
  <div ref="slideViewer" v-slide-loading="loading" class="slideViewer-priview">
    <div v-if="linkSlide.file_url.length > 0" ref="priview" class="slideViewer-content">
      <div id="preView" ref="exportHtml" :class="['thumbBig scrollbar', linkSlide.file_type === '10' ? '' : '']">
        <iframe
          v-if="linkSlide.file_type === '10' && linkSlide.isEdit === 0"
          :src="viewPdf(linkSlide) + '#view=Fit&zoom=FitH,75&toolbar=0&navpanes=0&scrollbar=0&pagemode=none'"
          seamless="seamless"
          type="application/pdf"
          frameborder="0"
          allowfullscreen
          class="video-thumb"
          style="overflow: hidden"
        >
        </iframe>
        <div v-if="linkSlide.file_type === '10' && linkSlide.isEdit === 1" class="pr-image">
          <div class="scale">
            <img v-show="linkSlide.file_type === '10' && linkSlide.isEdit === 1" :id="linkSlide.index" />
          </div>
        </div>
        <video v-if="linkSlide.file_type === '20'" controls allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" class="video-thumb">
          <source :src="linkSlide.file_url" type="video/mp4" />
        </video>
        <div
          v-for="(item, row) of textBox"
          :id="'drag' + item.id"
          ref="input"
          :key="item.id"
          :class="['drag-item', `origin${linkSlide.original_document_id}`]"
          :style="{ top: item.style.top, left: item.style.left }"
          @mousedown.passive="dragClick(`drag${item.id}`)"
          @mouseclick.passive="dragClick(`drag${item.id}`)"
        >
          <div v-show="isEditPPt" id="input-drag" class="input-drag"></div>
          <div v-show="isEditPPt" class="resize resizeLeftTop nw" @mousedown="resizeClick()"></div>
          <div v-show="isEditPPt" class="resize resizeLeftBot sw" @mousedown="resizeClick()"></div>
          <div v-show="isEditPPt" class="resize resizeRightTop ne" @mousedown="resizeClick()"></div>
          <div v-show="isEditPPt" class="resize resizeRightBot se" @mousedown="resizeClick()"></div>
          <div id="add-tag-input" class="add-tag-input">
            <div
              :id="'fontInput' + item.id"
              :style="{ width: item.size.width, height: item.size.height }"
              :class="['el-input__inner', isEditPPt ? 'ppt-border-style' : '']"
              :contenteditable="isEditPPt"
              @mouseup="selectText()"
              @focus="focus(`fontInput` + item.id)"
              @input.prevent="handleInput($event, row)"
            >
              <div :id="'textInput' + item.id" v-html="item.innerHtml"></div>
            </div>
          </div>
          <div id="input-drag" class="input-drag"></div>
          <button v-show="isEditPPt" type="button" class="remove-text btn btn--icon" @click="removeTexBox(item.id)">
            <span class="item"><img src="@/assets/template/images/icon_x.svg" alt="" /></span>
          </button>
        </div>

        <button
          v-show="linkSlide.isView"
          v-if="!isEditPPt && linkSlide.isEdit == 1"
          id="editbtn"
          type="button"
          :disabled="loading"
          class="btn btn-primary btnEditer"
          @click="editCoverPage()"
        >
          <span class="item"><img src="@/assets/template/images/icon_pen_white.svg" alt="" /></span>
        </button>
      </div>
      <!--Not Cover Page-->
    </div>
    <!-- Empty Data -->
    <div v-else class="slideViewer-empty">
      <EmptyData title="該当するデータがありません。" icon="amico_A01S03" custom-class="nodata-ppt h-100" />
    </div>

    <div v-if="linkSlide.file_url.length > 0" class="slideViewer-footer">
      <div v-if="!isEditPPt || linkSlide.isEdit == 0" class="group-info">
        <span class="link" @click="redirectToD01S02(linkSlide)">{{ linkSlide.document_name }}　</span>
        <span>( {{ linkSlide.original_document_page_num }}ページ目)</span>
        <span v-if="linkSlide?.time_lable_flag === 1" style="margin-left: 5px"> 適用期間外 </span>
        <button type="button" :class="['btn btn--icon remove-btn', linkSlide?.file_url.length === 0 ? 'disabled-x' : '']" @click="confirmDetelePage(linkSlide)">
          <span class="item"><img src="@/assets/template/images/icon_x.svg" alt="" /></span>
        </button>
      </div>

      <div v-if="isEditPPt && linkSlide.isEdit == 1" class="editor-toolbar">
        <button type="button" :class="['btn  font-style-B', bold.length > 0 ? 'active-btn' : '']" @click="Bclick()">
          <span class="item"><img src="@/assets/template/images/B-icon.svg" alt="" /></span>
        </button>
        <button type="button" :class="['btn  font-style-i', italic.length > 0 ? 'active-btn' : '']" @click="Iclick()">
          <span class="item"><img src="@/assets/template/images/i-icon.svg" alt="" /></span>
        </button>
        <button type="button" class="btn font-style-u" @click="Uclick()">
          <span class="item"><img src="@/assets/template/images/u-icon.svg" alt="" /></span>
        </button>
        <button type="button" class="btn font-style-strike" @click="Strikeclick()">
          <span class="item"><img src="@/assets/template/images/s-icon.svg" alt="" /></span>
        </button>
        <!--Color-->
        <div class="edit-group">
          <button type="button" class="btn font-style-color" :style="{ background: textforeColor }" @click="foreColor()">
            <span class="item"><img src="@/assets/template/images/a-icon.svg" alt="" /></span>
          </button>
          <div id="foreColor" ref="foreColor" class="dropdown-menu">
            <ColorPicker theme="light" color="#000" :colors-default="defaultColor" @changeColor="changeColor" />
          </div>
        </div>
        <div class="edit-group">
          <button type="button" class="btn font-style-color-hightlight" :style="{ background: hightColor }" @click="$highColorClick">
            <span class="pen05"><img src="@/assets/template/images/pen05-icon.svg" alt="" /></span>
          </button>
          <div id="ColorPickers" ref="ColorPicker" class="dropdown-menu">
            <ColorPicker theme="light" color="#000" :colors-default="defaultColor" @changeColor="changeHightColor" />
          </div>
        </div>

        <div class="edit-group">
          <button type="button" class="btn font-style-a dropdown-toggle" @click="fontSizeClick()">
            <span class="pen05"><img src="@/assets/template/images/icon_fontSize.svg" alt="" /></span>
          </button>
          <div id="fontSize" ref="fontSize" class="dropdown-menu">
            <ul class="list-fontSize scrollbar">
              <el-input
                v-model="size"
                clearable
                type="number"
                onkeydown="
                        javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                        ? true : !isNaN(Number(event.key)) && event.code!=='Space'
                        "
                minlength="1"
                @change="changeFontSize(size)"
              />
            </ul>
          </div>
        </div>
        <div class="edit-group">
          <button type="button" class="btn font-style-a dropdown-toggle" @click="alignGroup()">
            <span class="pen05"><img src="@/assets/template/images/icon-align-left.svg" alt="" /></span>
          </button>
          <div id="align" ref="align" class="dropdown-menu">
            <ul class="list-fontSize scrollbar">
              <button type="button" :class="['btn font-style-a btn-fontSize', activeAlign.left]" @click="changeAlign('justifyLeft', 'left')">
                <span class="pen05"><img src="@/assets/template/images/icon-align-left.svg" alt="" /></span>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize', activeAlign.right]" @click="changeAlign('justifyRight', 'right')">
                <span class="pen05"><img src="@/assets/template/images/icon-align-right.svg" alt="" /></span>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize', activeAlign.center]" @click="changeAlign('justifyCenter', 'center')">
                <span class="pen05"><img src="@/assets/template/images/icon-align-center.svg" alt="" /></span>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize', activeAlign.full]" @click="changeAlign('justifyFull', 'full')">
                <span class="pen05"><img src="@/assets/template/images/icon-align-center-center.svg" alt="" /></span>
              </button>
            </ul>
          </div>
        </div>
        <div class="edit-group">
          <button type="button" class="btn font-style-a dropdown-toggle" @click="orderedGroup()">
            <span class="pen05"><img src="@/assets/template/images/icon-order-type.svg" alt="" /></span>
          </button>
          <div id="ordered" ref="ordered" class="dropdown-menu">
            <ul class="list-fontSize scrollbar">
              <button type="button" :class="['btn font-style-a btn-fontSize', ordered.one]" @click="insertOrderedList('1')">1.</button>
              <button type="button" :class="['btn font-style-a btn-fontSize', ordered.a]" @click="insertOrderedList('a')">a.</button>
              <button type="button" :class="['btn font-style-a btn-fontSize', ordered.A]" @click="insertOrderedList('A')">A.</button>
              <button type="button" :class="['btn font-style-a btn-fontSize', ordered.i]" @click="insertOrderedList('i')">i.</button>
              <button type="button" :class="['btn font-style-a btn-fontSize', ordered.I]" @click="insertOrderedList('I')">I.</button>
              <button type="button" :class="['btn font-style-a btn-fontSize', ordered.none]" @click="removeOrderedList('none')">None</button>
            </ul>
          </div>
        </div>
        <div class="edit-group">
          <button type="button" class="btn font-style-a dropdown-toggle" @click="unorderedGroup()">
            <span class="pen05"><img src="@/assets/template/images/icon-unordered-type.svg" alt="" /></span>
          </button>
          <div id="unordered" ref="unordered" class="dropdown-menu">
            <ul class="list-fontSize scrollbar">
              <button type="button" :class="['btn font-style-a btn-fontSize', unordered['disc']]" @click="insertUnordered('disc')">
                <span class="pen05"><img src="@/assets/template/images/icon-disc.svg" alt="" /></span>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize', unordered['circle']]" @click="insertUnordered('circle')">
                <span class="pen05"><img src="@/assets/template/images/icon-circle.svg" alt="" /></span>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize', unordered['square']]" @click="insertUnordered('square')">
                <span class="pen05"><img src="@/assets/template/images/icon-square.svg" alt="" /></span>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize', unordered['starbullets']]" @click="insertUnordered('starbullets')">
                <span class="pen05"><img src="@/assets/template/images/icon-starbullets.svg" alt="" /></span>
              </button>
              <button
                type="button"
                :class="['btn font-style-a btn-fontSize arowbullets-2-btn', unordered['arowbullets-2']]"
                @click="insertUnordered('arowbullets-2')"
              >
                <h3 class="arowbullets-2"></h3>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize ', unordered['checkmark']]" @click="insertUnordered('checkmark')">
                <span class="checkmark-btn"><img src="@/assets/template/images/icon-checkmark.svg" alt="" /></span>
              </button>
              <button type="button" :class="['btn font-style-a btn-fontSize shadowed-square-btn', unordered.i]" @click="insertUnordered('shadowed-square')">
                <h3 class="shadowed-square"></h3>
              </button>

              <button type="button" :class="['btn font-style-a btn-fontSize', unordered['none']]" @click="insertUnordered('none')">None</button>
            </ul>
          </div>
        </div>
        <button type="button" :class="['btn  font-style-B', bold.length > 0 ? 'active-btn' : '']" @click="indentLeve()">
          <span class="item"><img src="@/assets/template/images/icon-indent.svg" alt="" /></span>
        </button>
        <button type="button" :class="['btn  font-style-B', bold.length > 0 ? 'active-btn' : '']" @click="outdentLeve()">
          <span class="item"><img src="@/assets/template/images/icon-outdent.svg" alt="" /></span>
        </button>
        <button type="button" class="btn" @click="addText()">
          <span class="pen05"><img src="@/assets/template/images/icon_addText.svg" alt="" /></span>
        </button>
        <button type="button" :class="['btn', !disabledUndo ? 'undo-btn' : '']" @click="undoClick()">
          <svg width="19" height="7" viewBox="0 0 19 7" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
            <path
              class="icon"
              d="M7.4 0.9C5.7 1.2 4.2 1.8 2.8 2.9L0 0V7H7L4.3 4.3C8 1.7 13.1 2.5 15.8 6.2C16 6.5 16.2 6.7 16.3 7L18.1 6.1C15.9 2.3 11.7 0.2 7.4 0.9Z"
              fill="#CAD4DB"
            />
          </svg>
        </button>
        <button type="button" :class="['btn', !disabledRedo ? 'redo-btn' : '']" @click="redoClick()">
          <svg width="18" height="7" viewBox="0 0 18 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M10.6 0.9C12.3 1.2 13.8 1.8 15.2 2.9L18 0V7H11L13.7 4.3C10 1.6 4.9 2.5 2.3 6.2C2.1 6.5 1.9 6.7 1.8 7L0 6.1C2.1 2.3 6.3 0.2 10.6 0.9Z"
              fill="#CAD4DB"
            />
          </svg>
        </button>

        <div class="starbullets"></div>
        <button id="saveDataHtml" type="button" class="btn btn--icon save-ppt-btn" @click="saveContentPpt()">
          <span class="pen05"><img src="@/assets/template/images/icon-save-ppt.svg" alt="" /></span>
        </button>
        <button type="button" class="btn btn--icon pen-x-btn" @click="closePen()">
          <span class="pen05"><img src="@/assets/template/images/icon-pen-canncel.svg" alt="" /></span>
        </button>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @handleYes="resetInfor"
          @onFinishScreen="onClose"
          @handleConfirm="resetInfor"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ColorPicker } from 'vue-color-kit';
import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.js';
import { markRaw } from 'vue';
import D01_S02_ModalMaterialDetails from '@/views/D01/D01_S02_MaterialDetails/D01_S02_ModalMaterialDetails';

// eslint-disable-next-line eqeqeq
export default {
  name: 'Tab2',
  components: {
    // EditorPlugin
    // eslint-disable-next-line vue/no-unused-components
    ColorPicker
  },
  props: {
    linkSlide: {
      type: Object,
      required: false,
      // eslint-disable-next-line vue/require-valid-default-prop
      default: null
    },

    coverSlides: {
      type: Number,
      required: false,
      default: 0
    },
    offEditPpt: {
      type: Boolean,
      required: false,
      default: false
    },
    loadingPreview: {
      type: Boolean,
      default: false
    }
  },
  emits: ['onSave', 'delete', 'html', 'save-ppt'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowClose: false,
        customClass: 'custom-dialog-document custom-dialog',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      size: '48',
      selectionText: '',
      textLayer: null,
      isLoading: false,
      privewThumnail: '',
      client: { pos1: 0, pos2: 0, pos3: 0, pos4: 0 },
      reSise: {
        currentResizer: null,
        isResizing: false,
        prevX: 0,
        prevY: 0,
        size: {
          ne: { width: 0, left: 0, height: 0, top: 0 },
          se: { width: 0, left: 0, height: 0, top: 0 },
          sw: { width: 0, left: 0, height: 0, top: 0 },
          nw: { width: 0, left: 0, height: 0, top: 0 }
        }
      },
      isData: true,
      isEditPPt: false,
      textBox: [{ type: 'text', id: 0, innerHtml: '', style: { top: '', left: '' }, size: { width: '', height: '', left: '' } }],
      textRaw: [],
      textBold: '',
      textforeColor: '',
      hightColor: '',
      bold: '',
      italic: '',
      underline: '',
      fontSizeA: '',
      activeAlign: { left: '', center: 'active-align', right: '', full: '' },
      ordered: { one: '', a: '', A: '', i: '', I: '', none: '' },
      unordered: { i: '' },
      disabledUndo: false,
      disabledRedo: true,
      innerHtml: [],
      defaultColor: [
        '#000000',
        '#FFFFFF',
        '#FF1900',
        '#F47365',
        '#FFB243',
        '#FFE623',
        '#6EFF2A',
        '#1BC7B1',
        '#00BEFF',
        '#2E81FF',
        '#5D61FF',
        '#FF89CF',
        '#FC3CAD',
        '#BF3DCE',
        '#8E00A7',
        'rgba(0,0,0,0)'
      ],
      listFontSize: [
        { name: 'Heading 1', size: '9px' },
        { name: 'Heading 2', size: '14px' },
        {
          name: 'Heading 3',
          size: '24px'
        },
        {
          name: 'Heading 4',
          size: '36px'
        },
        {
          name: 'Heading 5',
          size: '48px'
        },
        {
          name: 'Heading 6',
          size: '64px'
        },
        {
          name: 'Heading 7',
          size: '80px'
        }
      ],
      align: ['icon-align-left', 'icon-align-right', 'icon-align-center-center'],
      dragEl: document.getElementById('drag0'),
      undoRedo: { next: 0, pre: 0, redo: 0 },
      textId: '',
      changeColorListType: false,
      insertListType: false,
      hiddenEditBtn: false,
      loading: false,
      slider: {},
      originText: []
    };
  },
  computed: {},
  watch: {
    offEditPpt(e) {
      if (e) {
        this.closeEdit();
      }
    },
    linkSlide(e) {
      this.slider = e;
      this.isEditPPt = false;
      this.privewThumnail = '';
      this.textBox = [];
      this.dragEl = document.getElementById('drag0');
      this.PNGpdf(e);
      setTimeout(() => {
        // this.saveContentPpt();
        this.closeToolbarFromPrivew();
        this.closePen();
      });
      if (this.dragEl) {
        this.setUpEditor();
      }
    }
  },
  mounted() {
    this.dragEl = document.getElementById('drag0');
    sessionStorage.removeItem('D01S05');
    this.textBox = this.linkSlide?.content?.map((s) => ({ ...s, innerHtml: (s.innerHtml += '') }));
    this.PNGpdf(this.linkSlide);

    if (this.dragEl) {
      this.setUpEditor();
    }
    if (this.privewThumnail.length > 0) {
      setTimeout(() => {
        this.closeToolbarFromPrivew();
        this.closePen();
      });
    }
  },
  methods: {
    closeToolbarFromPrivew() {
      const privew = document.getElementById('preView');
      if (privew)
        privew.addEventListener('click', () => {
          this.closeAll();
        });
    },
    setUpEditor() {
      this.dragMoveEl();
      this.resizeEl();
      this.closeAll();
      this.selectText();
    },
    resetColorToolbar() {
      const buttonHightLight = document.querySelector('.font-style-color-hightlight');
      if (buttonHightLight) buttonHightLight.style.background = 'none';
      const fontColor = document.querySelector('.font-style-color');
      if (fontColor) fontColor.style.background = 'none';
    },
    confirmDetelePage(item) {
      this.modalConfirm(() => {
        this.removeSlide(item);
        this.onClose();
      }, item.document_name);
    },
    confirmRemoveDataWhenMove(linkSlide) {
      this.modalConfirmRoute(() => {
        if (this.textBox.length > 0) this.redirectToD01S02(linkSlide);
        this.onClose();
      });
    },
    resetInfor(e) {
      e.callBack();
    },
    modalConfirmRoute(callBack) {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: true,
        customClass: 'custom-dialog-document custom-dialog modal-fixed modal-fixed-min',
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        props: {
          mode: 1,
          params: { callBack }
        }
      };
    },
    modalConfirm(callBack, name) {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: true,
        customClass: 'custom-dialog-document custom-dialog modal-fixed modal-fixed-min',
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        props: {
          mode: 0,
          // textConfirm: '登録',
          // textCancel: 'キャンセル',
          params: { callBack },
          message: 'このアクションは元に戻すことができません。',
          title: `選択した${name}を完全に削除しますか？`
        }
      };
    },
    onClose() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog-document custom-dialog' };
    },
    getDocument(url) {
      const PDFJS = pdfjsLib;
      return PDFJS.getDocument(url);
    },
    viewPdf(item) {
      const isView = item.content && item.content[0] && item.content[0].file_url;
      if (isView) {
        return isView;
      } else {
        return item.file_url;
      }
    },
    PNGpdf(item) {
      let content = [];
      setTimeout(() => {
        this.getImage(item, (img) => {
          if (img && img.src) {
            const e = this.slider;
            const scale = img.naturalWidth > img.naturalHeight ? (img.naturalHeight > 600 ? '' : 'scale100') : 'scale50';
            img.className = scale;
            content = e && typeof e.content === 'object' ? e.content : [];
            this.textBox = content ? content?.map((s) => ({ ...s, innerHtml: (s.innerHtml += '') })) : [];
            this.textRaw = content ? content?.map((s) => s) : [];
          }
        });
      });
    },
    getImage(item, callBack) {
      var img = document.getElementById(this.linkSlide.index);
      if (img) img.src = item.thumbnail;
      callBack(img);
    },
    // setup hight light selection text
    selectword() {
      document.getSelection().removeAllRanges();
      if (this.selectionText.getRangeAt) document.getSelection().addRange(this.selectionText.getRangeAt);
    },
    // hight light selection
    selectText() {
      var text = document.getSelection().toString();
      var length = document.getSelection().toString().length;
      let select = document.getSelection();
      for (let i = 0; i < select.rangeCount; i++) {
        // selection word
        this.selectionText = {
          from: document.getSelection().baseOffset - 1,
          to: document.getSelection().extentOffset - 1,
          el: document.getSelection().baseNode,
          getRangeAt: select.getRangeAt(i)
        };
      }
      const id = this.dragEl.id.split('drag')[1];
      const input = document.getElementById(`fontInput${id}`);
      if (this.bold.length > 0 || (this.italic.length > 0 && text)) this.bold = '';
      this.italic = '';

      if (text) this.changeColorListType = true;
      this.insertListType = true;
      // eslint-disable-next-line eqeqeq
      if (length == input.innerText.length) {
        this.changeColorListType = true;
        this.insertListType = true;
      } else this.changeColorListType = false;
      this.insertListType = false;
    },
    // ramdom number id to create id for text box
    getRndInteger(max) {
      return Math.floor(Math.random() * max);
    },
    // remove an slide showing
    removeSlide() {
      this.$emit('delete', this.linkSlide);
      this.dragEl = document.getElementById('drag0');
      if (typeof this.linkSlide?.content === 'object') {
        this.textBox = this.linkSlide?.content?.map((s) => ({ ...s, innerHtml: (s.innerHtml += '') }));
        this.textRaw = this.linkSlide?.content?.map((s) => s);
        if (this.dragEl) {
          this.dragMoveEl();
          this.resizeEl();
          this.closeAll();
          this.selectText();
        }
      }
    },
    // edit cover page ( cover_flag = 1)
    editCoverPage() {
      if (this.isEditPPt === true) {
        this.isEditPPt = false;
      } else {
        this.isEditPPt = true;
      }
      this.textforeColor = '';
      this.hightColor = '';
      setTimeout(() => {
        this.resizeEl();
        this.dragClick(this.dragEl?.id);
        this.resetColorToolbar();
      });
    },
    // close edior
    closeEdit() {
      this.isEditPPt = false;
      setTimeout(() => {
        const editor = document.getElementById('editbtn');
        editor ? (editor.style.display = 'block') : null;
      }, 500);
      if (this.textRaw) this.textRaw.forEach((item, index) => this.reloadSizeDragById(index, item.id));

      this.emiterHtmlPreview();
    },
    closePen() {
      this.isEditPPt = false;
      setTimeout(() => {
        const editor = document.getElementById('editbtn');
        editor ? (editor.style.display = 'block') : null;
      }, 500);
      this.textBox = this.$props.linkSlide?.content?.map((s) => ({ ...s, innerHtml: (s.innerHtml += '') }));
      if (this.textRaw)
        this.textRaw.forEach((item, index) => {
          const input = document.getElementById('fontInput' + item.id);
          const child = this.textBox[index] && this.textBox[index].origin.innerHtml;
          const newText = this.originText[index] && this.originText[index].innerHtml;
          input.innerHTML = this.originText.length > 0 ? newText : child;
          this.reloadSizeDragById(index, item.id);
        });
      sessionStorage.removeItem('D01S05');
      this.emiterHtmlPreview();
    },
    mousedown(e) {
      this.reSise.currentResizer = e.target;
      this.reSise.isResizing = true;
      this.reSise.prevX = e.clientX;
      this.reSise.prevY = e.clientY;
      window.addEventListener('mousemove', this.mousemove);
      window.addEventListener('mouseup', this.mouseup);
    },
    mousemove(e) {
      // select query input element
      const el = this.dragEl.children[5].children[0];

      const rect = el.getBoundingClientRect();
      if (this.reSise.currentResizer?.classList.contains('se')) {
        el.style.width = rect.width - (this.reSise.prevX - e.clientX) + 'px';
        el.style.height = rect.height - (this.reSise.prevY - e.clientY) + 'px';
        this.reSise.size['se'].width = el.style.width;
        this.reSise.size['se'].height = el.style.height;
      } else if (this.reSise.currentResizer?.classList.contains('sw')) {
        el.style.width = rect.width + (this.reSise.prevX - e.clientX) + 'px';
        el.style.height = rect.height - (this.reSise.prevY - e.clientY) + 'px';
        el.style.left = rect.left - (this.reSise.prevX - e.clientX) + 'px';
        this.reSise.size['sw'].width = el.style.width;
        this.reSise.size['sw'].height = el.style.height;
      } else if (this.reSise.currentResizer?.classList.contains('ne')) {
        el.style.width = rect.width - (this.reSise.prevX - e.clientX) + 'px';
        el.style.height = rect.height + (this.reSise.prevY - e.clientY) + 'px';
        el.style.top = rect.top - (this.reSise.prevY - e.clientY) + 'px';
        this.reSise.size['ne'].width = el.style.width;
        this.reSise.size['ne'].height = el.style.height;
        this.reSise.size['ne'].top = el.style.top;
      } else {
        el.style.width = rect.width + (this.reSise.prevX - e.clientX) + 'px';
        el.style.height = rect.height + (this.reSise.prevY - e.clientY) + 'px';
        el.style.top = rect.top - (this.reSise.prevY - e.clientY) + 'px';
        el.style.left = rect.left - (this.reSise.prevX - e.clientX) + 'px';
        this.reSise.size['nw'].width = el.style.width;
        this.reSise.size['nw'].height = el.style.height;
        this.reSise.size['nw'].left = el.style.left;
        this.reSise.size['nw'].top = el.style.top;
      }

      this.reSise.prevX = e.clientX;
      this.reSise.prevY = e.clientY;
      this.$store.commit('size', {
        sizeInput: { id: this.dragEl.id, height: el.style.height, width: el.style.width }
      });
    },
    mouseup() {
      window.removeEventListener('mousemove', this.mousemove);
      window.removeEventListener('mouseup', this.mouseup);
      this.reSise.isResizing = false;
    },
    dragMouseDown(e) {
      e = e || window.event;
      e.preventDefault();
      // get the mouse cursor position at startup:
      this.client.pos3 = e.clientX;
      this.client.pos4 = e.clientY;
      document.onmouseup = this.closeDragElement;
      // call a function whenever the cursor moves:
      document.onmousemove = this.elementDrag;
    },
    closeDragElement() {
      // stop moving when mouse button is released:
      document.onmouseup = null;
      document.onmousemove = null;
    },
    elementDrag(e) {
      e = e || window.event;
      e.preventDefault();
      // calculate the new cursor position:
      this.client.pos1 = this.client.pos3 - e.clientX;
      this.client.pos2 = this.client.pos4 - e.clientY;
      this.client.pos3 = e.clientX;
      this.client.pos4 = e.clientY;
      // set the element's new position:
      // const top = this.dragEl.offsetTop - this.client.pos2;
      if (
        this.dragEl.offsetTop - this.client.pos2 > 0 && // Min Top
        this.dragEl.offsetTop - this.client.pos2 < 360 && // Max top
        this.dragEl.offsetLeft - this.client.pos1 > 0 && // Min Left
        this.dragEl.offsetLeft - this.client.pos1 < 1000 // Max Left
      ) {
        this.dragEl.style.top = this.dragEl.offsetTop - this.client.pos2 + 'px';
        this.dragEl.style.left = this.dragEl.offsetLeft - this.client.pos1 + 'px';
        this.$store.commit('drag', {
          dragMoveDown: { id: this.dragEl.id, top: this.dragEl.style.top, left: this.dragEl.style.left }
        });
        this.undoRedo.pre = 0;
        this.undoRedo.next = 0;
      }
    },
    dragMoveEl() {
      if ((this.dragEl && this.dragEl?.children[0]) || (this.dragEl && this.dragEl?.children[6])) {
        // if present, the header is where you move the DIV from:
        this.dragEl.children[0].onmousedown = this.dragMouseDown;
        this.dragEl.children[6].onmousedown = this.dragMouseDown;
      } else {
        // otherwise, move the DIV from anywhere inside the DIV:
        if (this.dragEl) this.dragEl.onmousedown = this.dragMouseDown;
      }
    },
    dragClick(el) {
      this.dragEl = document.getElementById(el);
      if (this.dragEl) this.dragMoveEl();
      this.emiterHtmlPreview();
    },
    resizeEl() {
      const resizers = document.querySelectorAll('.resize');

      for (let resizer of resizers) {
        resizer.addEventListener('mousedown', this.mousedown);
        this.emiterHtmlPreview();
      }
    },
    resizeClick() {
      this.resizeEl();
    },

    Bclick() {
      document.execCommand('bold', false, '');
      this.emiterHtmlPreview();
    },
    Iclick() {
      document.execCommand('italic', false, '');
      this.emiterHtmlPreview();
    },
    Uclick() {
      document.execCommand('underline', false, '');
      this.emiterHtmlPreview();
    },
    Strikeclick() {
      document.execCommand('strikeThrough', false, '');
      this.emiterHtmlPreview();
    },
    // open popup color table
    foreColor() {
      try {
        const foreColor = this.$refs.foreColor.children[0].childNodes;
        foreColor[foreColor.length - 1].addEventListener('selectstart', function (e) {
          e.preventDefault();
        });
        if (this.$refs.foreColor.classList[1] === 'show' || this.$refs.foreColor?.classList[1]) {
          this.$refs.foreColor.classList.remove('show');
        } else {
          this.$refs.foreColor.classList.add('show');
        }
        if (this.$refs.ColorPicker?.classList[1]) {
          this.$refs.ColorPicker.classList.remove('show');
        }
        if (this.$refs.fontSize.classList[1]) {
          this.$refs.fontSize.classList.remove('show-fontSize');
        }

        if (this.$refs.align.classList[1]) {
          this.$refs.align.classList.remove('show-align');
        }
        if (this.$refs.unordered?.classList[1]) {
          this.$refs.unordered?.classList.remove('show-unordered');
        }
        if (this.$refs.ordered?.classList[1]) {
          this.$refs.ordered?.classList.remove('show-ordered');
        }
      } catch (err) {
        // console.log(err);
      }
    },
    // changeTextColor
    changeColor(e) {
      // set color
      const { r, g, b, a } = e.rgba;
      const color = `rgba(${r}, ${g}, ${b}, ${a})`;
      this.textforeColor = color;
      document.execCommand('foreColor', false, this.textforeColor);
      this.changeColorListTypeF('ol');
      this.changeColorListTypeF('ul');
      this.textRaw.forEach((item, index) => {
        this.reloadSizeDragById(index, item.id);
      });
      // this.selectionText;
      this.emiterHtmlPreview();
    },
    $highColorClick() {
      if (this.$refs.ColorPicker.classList[1] === 'show' || this.$refs.ColorPicker?.classList[1]) {
        this.$refs.ColorPicker.classList.remove('show');
      } else {
        this.$refs.ColorPicker.classList.add('show');
      }
      if (this.$refs.foreColor?.classList[1]) {
        this.$refs.foreColor.classList.remove('show');
      }
      if (this.$refs.fontSize.classList[1]) {
        this.$refs.fontSize.classList.remove('show-fontSize');
      }
      if (this.$refs.align.classList[1]) {
        this.$refs.align.classList.remove('show-align');
      }
      if (this.$refs.unordered?.classList[1]) {
        this.$refs.unordered?.classList.remove('show-unordered');
      }
      if (this.$refs.ordered?.classList[1]) {
        this.$refs.ordered?.classList.remove('show-ordered');
      }
    },
    // change hight light color text
    changeHightColor(color) {
      // set color
      const { r, g, b, a } = color.rgba;
      const color1 = `rgba(${r}, ${g}, ${b}, ${a})`;
      this.hightColor = color1;
      document.execCommand('hiliteColor', false, color1);
      this.textRaw.forEach((item, index) => {
        this.reloadSizeDragById(index, item.id);
      });
      this.emiterHtmlPreview();
    },
    handleInput(e, index) {
      e.preventDefault();
      const el = e.srcElement;
      this.textRaw[index].text = el.innerText;
      this.textRaw[index].innerHtml = el.innerHTML;
      const checkData = el.innerText.length > 0;
      checkData ? sessionStorage.setItem('D01S05', el.innerText) : null;
      this.reloadSizeDrag(index);
      this.emiterHtmlPreview();
    },
    emiterHtmlPreview() {
      const prview = document.getElementById('preView');

      this.$emit('html', prview);
    },
    focus(ids) {
      const Input = document.getElementsByClassName('add-tag-input');
      const dragItem = document.getElementsByClassName('drag-item');
      if (Input) {
        [...Input].forEach((s) => {
          const input = s.children[0];
          if (input.id !== ids) {
            input.classList.remove('ppt-border-style');
            input.classList.remove('background-ppx');
          } else {
            input.classList.add('ppt-border-style');
            input.classList.add('background-ppx');
          }
        });
      }
      const resize = ['resize resizeLeftTop nw', 'resize resizeLeftBot sw', 'resize resizeRightTop ne', 'resize resizeRightBot se'];
      if (dragItem) {
        [...dragItem].forEach((s) => {
          const cutId = ids.split('fontInput')[1];
          const id = `drag${cutId}`;
          [...s.children].forEach((item, i) => {
            if (i >= 1 && i <= 4) {
              if (s.id === id) {
                s.children[i].setAttribute('class', resize[i - 1]);
              } else {
                s.children[i].setAttribute('class', `${resize[i - 1]} r-remove`);
              }
            }
            if (i === 7) {
              const display = s.id === id ? 'block' : 'none';
              s.children[i].style.display = display;
            }
          });
        });
      }
    },
    reloadSizeDrag(index1) {
      const drag = this.$store.state.D01.drag.filter((s) => s.dragMoveDown.id === this.dragEl.id);
      const size = this.$store.state.D01.sizes.filter((s) => s.sizeInput.id === this.dragEl.id);
      // eslint-disable-next-line eqeqeq

      if (drag && drag.length > 0) {
        const textRaw = drag[drag.length - 1].dragMoveDown;
        this.textRaw[index1].style = {
          top: textRaw.top,
          left: textRaw.left
        };
      }
      if (size && size.length > 0) {
        const sizeInput = size[size.length - 1].sizeInput;
        this.textRaw[index1].size = {
          height: sizeInput.height,
          width: sizeInput.width
        };
      }
    },
    reloadSizeDragById(index1, id) {
      const dragId = `drag${id}`;
      const drag = this.$store.state.D01.drag.filter((s) => s.dragMoveDown.id === dragId);
      const size = this.$store.state.D01.sizes.filter((s) => s.sizeInput.id === dragId);
      // eslint-disable-next-line eqeqeq

      if (drag && drag[drag.length - 1] && drag.length > 0) {
        const textRaw = drag[drag.length - 1].dragMoveDown;
        this.textRaw[index1].style = {
          top: textRaw.top,
          left: textRaw.left
        };
        this.textBox[index1].style.top = this.textRaw[index1].style.top;
        this.textBox[index1].style.left = this.textRaw[index1].style.left;
      }
      if (size && size.length > 0 && size[size.length - 1]) {
        const sizeInput = size[size.length - 1].sizeInput;
        this.textRaw[index1].size = {
          height: sizeInput.height,
          width: sizeInput.width
        };
        this.textBox[index1].size.height = this.textRaw[index1].size.height;
        this.textBox[index1].size.width = this.textRaw[index1].size.width;
      }
    },
    fontSizeClick() {
      if (this.$refs.fontSize.classList[1] === 'show-fontSize' || this.$refs.fontSize?.classList[1]) {
        this.$refs.fontSize.classList.remove('show-fontSize');
      } else {
        this.$refs.fontSize.classList.add('show-fontSize');
      }
      if (this.$refs.align.classList[1]) {
        this.$refs.align.classList.remove('show-align');
      }
      if (this.$refs.unordered?.classList[1]) {
        this.$refs.unordered?.classList.remove('show-unordered');
      }
      if (this.$refs.ordered?.classList[1]) {
        this.$refs.ordered?.classList.remove('show-ordered');
      }
      if (this.$refs.foreColor.classList[1] === 'show' || this.$refs.foreColor?.classList[1]) {
        this.$refs.foreColor.classList.remove('show');
      }
      if (this.$refs.ColorPicker.classList[1] === 'show' || this.$refs.ColorPicker?.classList[1]) {
        this.$refs.ColorPicker.classList.remove('show');
      }
    },
    execFontSize(size, unit) {
      var spanString = window
        .$('<font/>', {
          text: document.getSelection()
        })
        .css('font-size', size + unit)
        .prop('outerHTML');

      document.execCommand('insertHTML', false, spanString);
    },
    // font size
    changeFontSize(num) {
      this.selectword();
      let sel, range;
      if (window.getSelection()) {
        sel = window.getSelection();
        if (sel.rangeCount) {
          const textReplace = sel.toString();
          range = sel.getRangeAt(0);
          range.deleteContents();
          const node = document.createElement('span');

          node.style = 'font-size:' + num + 'px';
          node.innerHTML = textReplace.replace(/\n/g, '');
          range.insertNode(node);
        }
      } else if (document.selection && document.selection.createRange) {
        range = document.selection.createRange();
      }
      // closem popup
      // this.$refs.fontSize.classList.remove('show-fontSize');
      this.textRaw.forEach((item, index) => {
        this.reloadSizeDragById(index, item.id);
      });
      this.emiterHtmlPreview();
    },
    alignGroup() {
      if (this.$refs.align.classList[1] === 'show-align' || this.$refs.align?.classList[1]) {
        this.$refs.align.classList.remove('show-align');
      } else {
        this.$refs.align.classList.add('show-align');
      }
      if (this.$refs.fontSize.classList[1]) {
        this.$refs.fontSize.classList.remove('show-fontSize');
      }
      if (this.$refs.unordered?.classList[1]) {
        this.$refs.unordered?.classList.remove('show-unordered');
      }
      if (this.$refs.ordered?.classList[1]) {
        this.$refs.ordered?.classList.remove('show-ordered');
      }
      if (this.$refs.foreColor.classList[1] === 'show' || this.$refs.foreColor?.classList[1]) {
        this.$refs.foreColor.classList.remove('show');
      }
      if (this.$refs.ColorPicker.classList[1] === 'show' || this.$refs.ColorPicker?.classList[1]) {
        this.$refs.ColorPicker.classList.remove('show');
      }
    },
    changeAlign(mode, name) {
      document.execCommand(mode, false, '');
      this.activeAlign = {
        left: '',
        right: '',
        full: '',
        center: ''
      };
      this.textRaw.forEach((item, index) => {
        this.reloadSizeDragById(index, item.id);
      });
      setTimeout(() => {
        this.activeAlign[name] = 'active-align';
        this.$refs.align.classList.remove('show-align');
        this.emiterHtmlPreview();
      });
    },
    undoClick() {
      document.execCommand('undo', false, '');
      this.textRaw.forEach((item, index) => {
        this.reloadSizeDragById(index, item.id);
      });
      this.disabledRedo = false;
      this.emiterHtmlPreview();
    },
    redoClick() {
      document.execCommand('redo', false, '');
      this.textRaw.forEach((item, index) => {
        this.reloadSizeDragById(index, item.id);
      });
      this.emiterHtmlPreview();
    },
    addText() {
      const id = this.getRndInteger(500);
      this.textBox.push({ type: 'text', id, innerHtml: '', style: { top: '', left: '' }, size: { width: '400px', height: '200px', left: '' } });
      this.textRaw.push({ type: 'text', id, innerHtml: '', style: { top: '', left: '' }, size: { width: '', height: '', left: '' } });
      this.textRaw.forEach((item, index) => {
        this.reloadSizeDragById(index, item.id);
      });
      setTimeout(() => {
        const el = document.getElementById(`drag${id}`);

        if (el) {
          el.style.left = `${this.textBox.length}0` + 'px';
          el.style.top = `${this.textBox.length}0` + 'px';
        }
        el.onmousedown = this.dragClick(`drag${id}`);
      });
      if (this.dragEl) {
        this.dragMoveEl();
        this.resizeEl();
        this.emiterHtmlPreview();
      }
    },
    orderedGroup() {
      if (this.$refs.ordered.classList[1] === 'show-ordered' || this.$refs.ordered?.classList[1]) {
        this.$refs.ordered.classList.remove('show-ordered');
      } else {
        this.$refs.ordered.classList.add('show-ordered');
      }
      if (this.$refs.fontSize.classList[1]) {
        this.$refs.fontSize.classList.remove('show-fontSize');
      }
      if (this.$refs.unordered?.classList[1]) {
        this.$refs.unordered?.classList.remove('show-unordered');
      }
      if (this.$refs.foreColor?.classList[1]) {
        this.$refs.foreColor?.classList.remove('show');
      }
      if (this.$refs.fontSize?.classList[1]) {
        this.$refs.fontSize?.classList.remove('show-fontSize');
      }
      if (this.$refs.align?.classList[1]) {
        this.$refs.align?.classList.remove('show-align');
      }
      if (this.$refs.foreColor.classList[1] === 'show' || this.$refs.foreColor?.classList[1]) {
        this.$refs.foreColor.classList.remove('show');
      }
      if (this.$refs.ColorPicker.classList[1] === 'show' || this.$refs.ColorPicker?.classList[1]) {
        this.$refs.ColorPicker.classList.remove('show');
      }
    },
    unorderedGroup() {
      if (this.$refs.unordered.classList[1] === 'show-unordered' || this.$refs.unordered?.classList[1]) {
        this.$refs.unordered.classList.remove('show-unordered');
      } else {
        this.$refs.unordered.classList.add('show-unordered');
      }
      if (this.$refs.fontSize.classList[1]) {
        this.$refs.fontSize.classList.remove('show-fontSize');
      }
      if (this.$refs.ordered?.classList[1]) {
        this.$refs.ordered?.classList.remove('show-ordered');
      }
      if (this.$refs.foreColor?.classList[1]) {
        this.$refs.foreColor?.classList.remove('show');
      }
      if (this.$refs.fontSize?.classList[1]) {
        this.$refs.fontSize?.classList.remove('show-fontSize');
      }
      if (this.$refs.align?.classList[1]) {
        this.$refs.align?.classList.remove('show-align');
      }
    },
    changeColorListTypeF(mode) {
      if (this.dragEl && this.dragEl.id) {
        const id = this.dragEl.id.split('drag')[1];

        const font = document.getElementById(`fontInput${id}`);
        const styleList = font.getElementsByTagName(mode)[0];
        if (styleList) {
          const li = styleList.getElementsByTagName('li');

          if (styleList) {
            setTimeout(() => {
              if (font) {
                [...li].forEach((item) => {
                  const colorItem = item.getElementsByTagName('span')[0];
                  const fontChild = colorItem && colorItem.getElementsByTagName('font')[0];
                  const customColor = fontChild ? fontChild : colorItem;
                  if ((customColor && customColor.color) || (customColor && customColor.style)) {
                    const color = customColor.color ? customColor.color : customColor.style?.color;
                    let rgb = color.replace(/rgba/i, 'rgb');
                    rgb = rgb.replace(/\)/i, ',1)');
                    item.style.color = rgb;
                  }
                });
              }
            });
          }
        }

        this.textRaw.forEach((item, index) => {
          this.reloadSizeDragById(index, item.id);
        });
        this.emiterHtmlPreview();
      }
    },
    insertOrderedList(type) {
      document.execCommand('insertOrderedList', false, '');
      const id = this.dragEl.id.split('drag')[1];
      const font = document.getElementById(`fontInput${id}`);
      const ol = font.getElementsByTagName('ol')[0];
      if (ol) {
        ol.type = type;
      } else {
        setTimeout(() => {
          document.execCommand('insertOrderedList', false, '');
          const id = this.dragEl.id.split('drag')[1];
          const font = document.getElementById(`fontInput${id}`);
          const ol = font.getElementsByTagName('ol')[0];
          if (ol) {
            ol.type = type;
          }
        });
      }
      this.ordered = {
        one: '',
        none: '',
        a: '',
        A: '',
        i: '',
        I: ''
      };
      setTimeout(() => {
        // eslint-disable-next-line eqeqeq
        const name = type == '1' ? 'one' : type;
        this.ordered[name] = 'active-align';
        this.$refs.ordered.classList.remove('show-align');
        this.changeColorListTypeF('ol');
        this.textRaw.forEach((item, index) => {
          this.reloadSizeDragById(index, item.id);
        });
        this.emiterHtmlPreview();
      });
    },
    insertUnordered(type) {
      document.execCommand('insertUnorderedList', false, '');
      const id = this.dragEl.id.split('drag')[1];
      const font = document.getElementById(`fontInput${id}`);
      const ul = font.getElementsByTagName('ul')[0];
      const ol = font.getElementsByTagName('ol')[0];
      const modeOUl = ol ? ol : ul;
      if (modeOUl) {
        modeOUl.removeAttribute('class');
        modeOUl.setAttribute('class', type);
      } else {
        setTimeout(() => {
          document.execCommand('insertUnorderedList', false, '');
          const id = this.dragEl.id.split('drag')[1];
          const font = document.getElementById(`fontInput${id}`);
          const ul = font.getElementsByTagName('ul')[0];
          const ol = font.getElementsByTagName('ol')[0];
          const modeOUl = ol ? ol : ul;
          if (modeOUl) {
            modeOUl.removeAttribute('class');
            modeOUl.classList.add(type);
          }
        });
      }
      this.unordered = {
        disc: '',
        circle: '',
        square: '',
        starbullets: '',
        ['arowbullets-2']: '',
        checkmark: '',
        none: ''
      };
      setTimeout(() => {
        // eslint-disable-next-line eqeqeq
        this.unordered[type] = 'active-align';
        this.$refs.unordered.classList.remove('show-unordered');
        this.emiterHtmlPreview();
        this.changeColorListTypeF('ul');
      });
    },
    indentLeve() {
      document.execCommand('indent', false, null);
      this.emiterHtmlPreview();
    },
    outdentLeve() {
      document.execCommand('outdent', false, null);
      this.emiterHtmlPreview();
    },
    removeOrderedList(type) {
      const id = this.dragEl.id.split('drag')[1];
      const font = document.getElementById(`fontInput${id}`);
      if (font) {
        const ol = font.getElementsByTagName('ol')[0];
        if (type === 'none' && ol) {
          if (ol.type.length > 0) {
            ol.removeAttribute('type');
            ol.style.lsistStyleType = 'none';
          }
        }
      }
      this.ordered = {
        one: '',
        none: '',
        a: '',
        A: '',
        i: '',
        I: ''
      };
      setTimeout(() => {
        // eslint-disable-next-line eqeqeq
        const name = type == '1' ? 'one' : type;
        this.ordered[name] = 'active-align';
        this.$refs.ordered.classList.remove('show-align');
      });
      this.emiterHtmlPreview();
    },
    removeTexBox(id) {
      // eslint-disable-next-line eqeqeq
      const index = this.textBox.findIndex((s) => s.id == id);
      this.textBox.splice(index, 1);
      this.textRaw.splice(index, 1);
      sessionStorage.removeItem('D01S05');
      this.emiterHtmlPreview();
    },
    closeAll() {
      this.emiterHtmlPreview();
      if (this.$refs.ColorPicker?.classList[1]) {
        this.$refs.ColorPicker?.classList.remove('show');
      }
      if (this.$refs.foreColor?.classList[1]) {
        this.$refs.foreColor?.classList.remove('show');
      }
      if (this.$refs.fontSize?.classList[1]) {
        this.$refs.fontSize?.classList.remove('show-fontSize');
      }
      if (this.$refs.align?.classList[1]) {
        this.$refs.align?.classList.remove('show-align');
      }
      if (this.$refs.ordered?.classList[1]) {
        this.$refs.ordered?.classList.remove('show-ordered');
      }
      if (this.$refs.unordered?.classList[1]) {
        this.$refs.unordered.classList.remove('show-unordered');
      }
    },
    // redirectToD01S02(item) {
    //   let document_id = item.document_id;
    //   this.$router.push({ name: 'D01_S02_MaterialDetails', query: { document_id } });
    // },
    redirectToD01S02(item) {
      let document_id = item.original_document_id;
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        isShowClose: true,
        customClass: 'custom-dialog-document custom-dialog custom-dialog-pd custom-dialog-material',
        component: markRaw(D01_S02_ModalMaterialDetails),
        props: { documentId: document_id },
        width: '90%',
        destroyOnClose: true,
        closeOnClickMark: false
      };
    },
    contentPpt(ppt, content, e) {
      if (ppt) {
        ppt.forEach((s) => {
          const drag = document.getElementById(s.id);
          if (drag) {
            const id = s.id.split('drag')[1];
            if (!isNaN(id)) {
              const font = document.getElementById(`fontInput${id}`);
              const result = { id: Number(id), text: '', innerHtml: '', size: '', style: '', link: '', scaleClass: '' };

              if (font) {
                result.innerHtml = font?.innerHTML;
                result.text = font.innerText;
                const height = font?.style?.height;
                const width = font?.style?.width;
                // eslint-disable-next-line eqeqeq
                const img = ppt[0].src; // returm title-sliler and slider other
                const size = { width, height };
                result.size = size;
                result.link = img;
                result.thumbnail = img;
              }
              const top = drag.style.top;
              const left = drag.style.left;
              const style = { top, left };
              result.style = style;
              content.push({ ...result, history_id: e.original_document_id });
            }
          }
        });
        // this.loadPptToImage(null, i);
      }
    },
    saveContentPpt() {
      const save = document.getElementById('preView');
      this.resetColorToolbar();
      setTimeout(() => {
        const ppt = save?.childNodes;
        const content = [];
        const e = this.linkSlide;
        this.contentPpt(ppt, content, e);
        const linkSlide = {
          file_url: e.file_url,
          isEdit: e.cover_flag === 1 ? 1 : 0,
          isEditPpt: e.cover_flag === 0 ? false : true,
          slideNum: e.slideNum,
          content: content.map((s) => ({ ...s, file_url: e.file_url })),
          isView: e.cover_flag === 1 ? true : false,
          document_name: e.document_name,
          document_id: e.document_id,
          index: e.index
        };
        this.originText = content.map((s) => ({ ...s }));
        this.emiterHtmlPreview();
        this.isEditPPt = false;
        this.textRaw.forEach((item, index) => {
          // this.textBox[index].innerHtml = '';
          const el = document.getElementById(`fontInput${item.id}`);
          const textInput = el.querySelector('#textInput' + item.id);
          this.textBox[index].innerHtml = textInput ? textInput.innerHTML : el.innerHTML;
          this.reloadSizeDragById(index, item.id);
        });
        this.closeEdit();
        sessionStorage.removeItem('D01S05');
        this.$emit('save-ppt', linkSlide);
      }, 500);
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet2: 1202px;
@media screen and (max-width: $viewport_tablet2) {
  .pen-x-btn {
    bottom: 5rem;
  }
  .save-ppt-btn {
    bottom: 5rem;
  }
}
.slideViewer-priview {
  position: relative;
  width: 100%;
  height: 98%;
  padding: 30px 20px 0px 20px;
  .slideViewer-content {
    width: 100%;
    height: 90%;
    position: relative;
    right: 0px;
    background: #e9e9e9;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    overflow: auto;

    .thumbBig {
      position: absolute;
      width: 100%;
      display: flex;
      justify-content: right;
      align-items: end;
      height: 100%;
      right: 0;
      top: 0;
      .btnEditer {
        position: fixed;
        border-radius: 105px 105px 105px 105px;
        width: 75px;
        height: 75px;
        bottom: 20%;
        margin-right: 10px;
        margin-bottom: 10px;
      }
    }
  }
  .vhHeight {
    min-height: 80vh;
  }
  .slideViewer-empty {
    width: 100%;
    height: 100%;
    overflow: hidden;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
  }
  .slideViewer-footer {
    width: 100%;
    height: 10%;
    right: 0px;
    overflow: hidden;
    background: #ffffff;
    border-left: 1px solid #e3e3e3;
    border-right: 1px solid #e3e3e3;
    border-bottom: 1px solid #e3e3e3;
    border-radius: 0px 0px 10px 10px;

    .editor-toolbar {
      position: absolute;
      padding-left: 10px;
      justify-content: left;
      align-items: center;
      left: 40px;
      padding-left: 5px 5px;
      margin-top: 10px;
      width: 100%;
      display: inline-flex;
      .btn {
        margin-left: 4px;
      }
      .B-active {
        background-color: #bbbbbb;
      }
    }

    .redo-btn {
      svg {
        path {
          fill: #448add;
          &:hover {
            fill: #448add;
          }
        }
      }
    }
    .undo-btn {
      svg {
        path {
          fill: #448add;
          &:hover {
            fill: #448add;
          }
        }
      }
    }

    .group-info {
      width: 100%;
      height: 100%;
      padding: 10px;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      .remove-btn {
        position: absolute;
        right: 20px;
      }
      .disabled-x {
        pointer-events: none;
      }
      a {
        color: #448add;
        cursor: pointer;
        font-size: 16px;
      }
      span {
        font-weight: bold;
        font-size: 16px;
      }
      .button-x {
        position: absolute;
        right: 10px;
      }
      img {
        width: 25px;
        svg {
          transform: rotate(180deg);
          fill: brown;
        }
      }
      .pen05 {
        img {
          width: 35px;
        }
      }
    }
  }
}

.drag,
.drag-item {
  position: absolute;
  z-index: 9;
  text-align: center;
}
.input-drag {
  padding: 5px;
  cursor: move;
  z-index: 10;
  color: #fff;
}
.resizeLeftTop {
  position: absolute;
  top: 10px;
  left: 10px;
  height: 10px;
  width: 10px;
  cursor: se-resize;
  z-index: 1;
  border-right: 1px dotted #000;

  border-bottom: 1px dotted #000;
}
.resizeLeftBot {
  position: absolute;
  bottom: 10px;
  left: 10px;
  height: 10px;
  width: 10px;
  cursor: sw-resize;
  z-index: 1;
  border: 1px dotted #000;
}
.r-remove {
  border: none !important;
}
.resizeRightBot {
  position: absolute;
  bottom: 10px;
  right: 10px;
  height: 10px;
  width: 10px;

  cursor: nw-resize;
  z-index: 1;
  border: 1px dotted #000;
}
.r-nw .resizeRightTop {
  position: absolute;
  border-left: 1px dotted #000;
  border-bottom: 1px dotted #000;
  top: 10px;
  right: 10px;
  height: 10px;
  width: 10px;
  cursor: ne-resize;
  z-index: 1;
}
.remove-text {
  position: absolute;
  top: -10px;
  right: -10px;
  height: 25px;
  width: 25px;
  cursor: pointer;
  z-index: 1;
  span {
    img {
      width: 15px;
      margin-bottom: 2px;
    }
  }
}
.edit-group {
  position: relative;
  input {
    position: absolute;
    top: 0px;
    right: 0px;
    background-color: none !important;
    z-index: -1;
  }
}
.dropdown-filter--Notes {
  width: 500px;
  position: absolute;
}
.show {
  display: block !important;
  top: -300px;
  left: 50px;
  width: 220px;
}
.show-fontSize {
  display: block !important;
  left: 0px;
  top: -40px;
  min-width: 92px !important;
  height: 50px;
  padding: 5px;
}
.show-align {
  display: block !important;
  left: 0px;
  top: -120px;
  width: 50px;
  .list-fontSize {
    min-width: 40px !important;
    position: relative;
    .btn-fontSize {
      text-align: center;
      width: 45px;
      &:hover {
        background-color: rgb(218, 218, 218);
        cursor: pointer;
      }
    }
    .el-input {
      .el-input__inner {
        border: none;
      }
    }
  }
}
.show-ordered {
  display: block !important;
  left: 12px;
  top: -120px;
  width: 15rem;
  .list-fontSize {
    height: 100px;
    min-width: 0px !important;
    .btn-fontSize {
      text-align: center;
      width: 70px;
      &:hover {
        background-color: rgb(218, 218, 218);
        cursor: pointer;
      }
    }
    button:last-child {
      width: 70px;
    }
  }
}
.show-unordered {
  display: block !important;
  left: 12px;
  top: -120px;
  width: 15rem;
  .list-fontSize {
    height: 100px;
    min-width: 0px !important;
    .btn-fontSize {
      text-align: center;
      width: 70px;
      &:hover {
        background-color: rgb(218, 218, 218);
        cursor: pointer;
      }
    }
    button:last-child {
      width: 70px;
    }
  }
}
.list-fontSize {
  min-width: 90px !important;
  .btn-fontSize {
    text-align: left;
    width: 100%;
  }
  .btn-fontSize:hover {
    background-color: rgb(218, 218, 218);
  }
  .fontSizeActive {
    background-color: rgb(218, 218, 218);
  }
  li {
    margin-bottom: 10px;
    &:hover {
      background-color: rgb(218, 218, 218);
      cursor: pointer;
    }
  }
}
.active-align,
.active-ordered {
  background-color: rgb(218, 218, 218);
}
font {
  cursor: text;
}
.read-only-text {
  border: none !important;
}
.pen-x-btn {
  position: absolute;
  right: 5em;
}
.save-ppt-btn {
  position: absolute;
  right: 8em;
  margin-right: 10px;
}
.modal-body {
  padding: 0px !important;
}
.active-btn {
  background-color: rgb(230, 230, 230);
}
.starbullets {
  list-style-image: url('../../../../assets/template/images/icon-checkmark.svg');
}
.checkmark-btn {
  img {
    max-width: 60% !important;
  }
}
.custom-image {
  position: absolute;
  width: 100%;
  height: 100%;
}
.noData {
  position: relative;
  .noData-content {
    position: absolute;
    height: 100%;
    .noData-thumb {
      display: flex;
      margin-top: 0px;
      height: 100%;
      justify-content: center;
      align-items: center;
    }
  }
}
.image-pdf {
  position: absolute;
  width: 100%;
  height: 100%;
}
.pr-image {
  position: absolute;
  width: 100%;
  top: 0px;
  height: 100%;
  justify-content: center;
  display: flex;
  .scale {
    position: relative;
    top: 0px;
    width: 70%;
    height: 100%;
    img {
      position: absolute;
      width: 100%;
      height: 100%;
    }
    .scale100 {
      transform: scale3d(1, 1, 1);
      image-rendering: -webkit-optimize-contrast;
    }
    .scale50 {
      object-fit: scale-down;
      image-rendering: -webkit-optimize-contrast;
    }
  }
}
.h-100 {
  height: 100%;
}
.video-thumb {
  height: 100%;
  width: 100%;
}
</style>
