<template>
  <!-- ストック編集 -->
  <!-- Start  -->
  <div class="modal-contentSetting modal-body-Z03S03">
    <div id="msfa-notify-Z03S03"></div>
    <div class="form-setting">
      <ul>
        <li>
          <label class="form-setting-label"> 面談内容 </label>
          <div class="form-setting-control">
            <el-select v-model="content_cd" placeholder="未選択" class="form-control-select">
              <el-option value="">未選択</el-option>
              <el-option v-for="item in lstContent" :key="item.content_cd" :label="item.content_name" :value="item.content_cd"> </el-option>
            </el-select>
          </div>
        </li>
        <li>
          <label class="form-setting-label">品目</label>
          <div class="form-setting-control">
            <div class="form-icon icon-right">
              <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
              </span>
              <div v-if="lstProduct.length > 0" class="form-control d-flex align-items-center">
                <div class="block-tags">
                  <el-tag v-for="item in lstProduct" :key="item.product_cd" class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveProduct(item)">
                    {{ item.product_name }}
                  </el-tag>
                </div>
              </div>

              <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div class="contentSetting-body">
      <div class="title">
        <h2 class="tlt">資材</h2>
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="openModalZ05S05()">
          <span class="btn-iconLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" />
          </span>
          追加
        </button>
      </div>
      <div class="contentSetting-box">
        <div v-if="lstDocument.length > 0" class="contentSetting-lst scrollbar">
          <ul>
            <li v-for="document in lstDocument" :key="document.document_id">
              <div class="contentSetting-link">
                <span class="item-setting">
                  <img
                    v-if="document.document_type == '10' && document.file_type == '10'"
                    v-svg-inline
                    svg-inline
                    class="svg"
                    :src="require('@/assets/template/images/icon_pdf-manag.svg')"
                    alt=""
                  />
                  <img
                    v-if="document.document_type == '10' && document.file_type == '20'"
                    v-svg-inline
                    svg-inline
                    class="svg"
                    :src="require('@/assets/template/images/icon_film.svg')"
                    alt=""
                  />
                  <img
                    v-if="document.document_type == '20'"
                    v-svg-inline
                    svg-inline
                    class="svg"
                    :src="require('@/assets/images/icon_document_spanner.svg')"
                    alt=""
                  />
                </span>
                <a class="tag-a log-icon" title_log="資材名" @click="redirectToD01S02(document)"> {{ document.document_name }} </a>
              </div>
              <button class="btn btn--icon" @click="removeDocument(document)">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
              </button>
            </li>
          </ul>
        </div>

        <div v-if="lstDocument.length === 0" class="no-data">
          <div class="no-data-content">
            <p class="no-data-text">該当するデータがありません。</p>
            <div class="no-data-thumb">
              <img class="svg" src="@/assets/template/images/data/amico.svg" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="contentSetting-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
      <button type="button" class="btn btn-primary" @click="onResultData">設定</button>
    </div>
  </div>
  <!-- End -->

  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :show-close="modalConfig.isShowClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :top="'10vh'"
    :before-close="onCloseModal"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="handleConfirmYes"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import Z03_S03_ConsultationContentSettingService from '@/api/Z03/Z03_S03_ConsultationContentSettingService';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05_S05_Choice_Of_Masterial from '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial';
import D01_S02_ModalMaterialDetails from '@/views/D01/D01_S02_MaterialDetails/D01_S02_ModalMaterialDetails';
import _ from 'lodash';

export default {
  name: 'Z03_S03_ConsultationContentSetting',
  components: { Z05S06MaterialSelection, Z05_S05_Choice_Of_Masterial },
  props: {
    checkLoading: [Boolean],

    facilityCategory: {
      type: Object,
      required: false
    },

    contentCd: {
      type: String,
      required: false,
      default: ''
    },

    contentName: {
      type: String,
      required: false,
      default: ''
    },

    products: {
      type: Array,
      required: false,
      default() {
        return [];
      }
    },

    documents: {
      type: Array,
      required: false,
      default() {
        return [];
      }
    }
  },
  emits: ['onFinishScreen'],

  data() {
    return {
      loadingPage: false,
      loadingContent: false,
      isSubmit: false,
      contentChange: false,
      content_cd: this.contentName || '',
      product_cd: this.products.length > 0 ? this.products.map((x) => x.product_cd.trim()) : [],
      lstProduct: [...this.products] || [],
      lstDocument: [...this.documents] || [],

      paramsZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: this.products.length > 0 ? this.products.map((x) => x.product_cd.trim()) : []
      },

      paramsZ05S05: {
        subMaterialSelectableFlag: 0, // 1: allow select child material || 0: not allow  (require)
        customMaterialFlag: 1, // 1: allow choice カスタム資材 || 0: not allow (require)
        initMaterials: [],
        materialType: '10',
        document_name: '',
        productCode: '',
        medicalSubjectsGroupCode: '',
        safetyInformationFlag: '',
        offLabelInformationFlag: '',
        date: null
      },

      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        isShowModalD01S02: false,
        isShowClose: true,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      lstContent: []
    };
  },

  mounted() {
    this.getDataScreen();
  },

  methods: {
    getDataScreen() {
      this.lstContent = [];

      Z03_S03_ConsultationContentSettingService.getDataScreen()
        .then((res) => {
          this.lstContent = res?.data?.data;
          this.content_cd = this.contentCd || '';
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z03S03', idNodeNotify: 'msfa-notify-Z03S03' });
        })
        .finally(() => {});
    },

    // Modal Z05-S06
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        component: markRaw(Z05S06MaterialSelection),
        width: '33rem',
        props: { ...this.paramsZ05S06 }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected;
      this.lstProduct = data;
      this.product_cd = data?.map((x) => x.product_cd);

      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        categoryCode: e.category.definition_value,
        classificationCode: e.classifications.product_group_cd,
        initDataCodes: data?.map((x) => x.product_cd)
      };
    },

    handleRemoveProduct(item) {
      this.lstProduct = this.lstProduct.filter((x) => x.product_cd !== item.product_cd);
      this.product_cd = this.lstProduct?.map((x) => x.product_cd);

      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        initDataCodes: this.product_cd
      };
    },

    // Modal Z05-S05
    openModalZ05S05() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材選択',
        customClass: 'custom-dialog custom-dialog-pd-none',
        isShowModal: true,
        component: markRaw(Z05_S05_Choice_Of_Masterial),
        width: '77rem',
        props: {
          mode: 'multiple',
          subMaterialSelectableFlag: 1,
          customMaterialFlag: 1,
          params: {
            ...this.paramsZ05S05,
            initMaterials: this.lstDocument.map((x) => x.document_id)
          }
        }
      };
    },

    onResultModalZ05S05(e) {
      this.lstDocument = [];
      e?.dataMasterialSelected.forEach((x) => {
        if (!this.lstDocument.some((e) => e.document_id?.toString() === x.document_id?.toString())) {
          this.lstDocument.push({
            document_id: x.document_id,
            document_name: x.document_name,
            document_type: x.document_type,
            file_type: x.file_type
          });
        }
      });

      this.onCloseModal();
    },

    removeDocument(document) {
      this.lstDocument = this.lstDocument.filter((x) => x.document_id !== document.document_id);
    },

    // Modal D02-S02
    openModalD01S02() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材詳細',
        isShowModalD01S02: true,
        width: '400px',
        customClass: 'custom-dialog'
      };
    },

    redirectToD01S02(item) {
      let document_id = item.document_id;
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        isShowClose: true,
        customClass: 'custom-dialog custom-dialog-pd custom-dialog-material',
        component: markRaw(D01_S02_ModalMaterialDetails),
        props: { documentId: document_id },
        width: '90%',
        destroyOnClose: true,
        closeOnClickMark: false
      };
    },

    // result modal children
    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S05') {
          this.onResultModalZ05S05(e);
        }
        if (e.screen === 'Z05_S06') {
          this.onResultModalZ05S06(e);
        }
      }

      this.onCloseModal();
    },

    // close modal children
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog', isShowClose: true };
    },

    validationContent() {
      let content = this.lstContent.find((x) => x.content_cd === this.content_cd);
      if (content && Object.keys(content).length > 0) {
        return '';
      } else {
        return '選択してください。';
      }
    },

    // close modal Z03-S03
    confirmCancel() {
      let isEqual = !_.isEqual(this.products, this.lstProduct) || !_.isEqual(this.documents, this.lstDocument) || !_.isEqual(this.contentCd, this.content_cd);

      if (isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1 },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.handleConfirmYes();
      }
    },

    handleConfirmYes() {
      this.$emit('onFinishScreen');
    },

    // return data to screen parent
    onResultData() {
      this.isSubmit = true;
      let result = {
        screen: 'Z03_S03',
        content: this.lstContent.find((x) => x.content_cd === this.content_cd),
        lstProduct: this.lstProduct,
        lstDocument: this.lstDocument
      };
      this.$emit('onFinishScreen', result);
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.modal-contentSetting {
  .form-setting {
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -20px;
      li {
        width: 50%;
        padding-left: 20px;
        margin-top: 12px;
      }
    }
    .form-setting-label {
      font-size: 1rem;
      font-weight: 700;
      margin-bottom: 10px;
    }
  }
  .contentSetting-body {
    .title {
      margin-top: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      .tlt {
        font-size: 1rem;
        font-weight: 700;
      }
      .btn {
        white-space: nowrap;
        padding: 0 12px;
        height: 32px;
        .btn-iconLeft {
          top: -2px;
        }
      }
    }
    .contentSetting-box {
      background: #fff;
      box-shadow: 0px 0px 5px rgba(183, 195, 203, 0.9);
      border-radius: 8px;
      margin-top: 12px;
      padding: 8px 12px;
      height: 350px;
      .contentSetting-lst {
        height: 300px;
        li {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 10px 14px;
          border-bottom: 1px solid #e3e3e3;
          .contentSetting-link {
            padding-right: 18px;
            display: flex;
            align-items: center;
            .item-setting {
              min-width: 17px;
              margin-right: 12px;
              img {
                max-width: 17px;
              }
            }
            .item-link {
              cursor: pointer;
              min-width: 16px;
              margin-left: 8px;
              img {
                max-width: 16px;
              }
            }
          }
          .btn {
            min-width: 42px;
          }
        }
      }
    }
  }
  .contentSetting-btn {
    margin-top: 20px;
    text-align: center;
    .btn {
      margin-right: 24px;
      width: 180px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}

.custom-input {
  position: relative;
}
.iconClear {
  cursor: pointer;
  position: absolute;
  right: 35px;
  top: 33%;
  width: 20px;
}

.no-data {
  padding-top: 10px;
  height: auto;
  text-align: center;
  .no-data-content {
    width: 100%;
    .no-data-text {
      font-size: 1rem;
    }
    .no-data-thumb {
      max-width: 400px;
      margin: 10px auto 0;
    }
  }
}

.tag-a {
  color: #59a5ff !important;
  cursor: pointer;
  &:hover {
    text-decoration: underline !important;
  }
}

.el-tag-custom {
  color: #225999;
  background: #d1e4ff;
  height: 27px;
  line-height: 23px;
  font-size: 14px;
  align-items: center;
  margin: 2px 10px 2px 0;
  border-radius: 24px;
}

.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}
</style>
