<template>
  <div class="modal-body modal-contentSetting">
    <div class="form-setting">
      <ul>
        <li>
          <label class="form-setting-label">面談内容</label>
          <div class="form-setting-control">
            <el-select v-model="content_cd" placeholder="未選択" class="form-control-select">
              <el-option label="" value="">未選択</el-option>
              <el-option v-for="item in allContent" :key="item" :label="item.content_name" :value="item.content_cd"> </el-option>
            </el-select>
          </div>
        </li>
        <li>
          <label class="form-setting-label">品目</label>
          <div class="form-setting-control">
            <div class="form-icon icon-right">
              <span title_log="品目" class="icon log-icon" @click="openModalZ05_S06_Material_Selection" @touchstart="openModalZ05_S06_Material_Selection">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
              </span>
              <div v-if="product_list.length > 0" class="form-control d-flex align-items-center">
                <div class="block-tags">
                  <el-tag v-for="item in product_list" :key="item" class="m-0 el-tag-custom" closable @close="handleRemoveProduct(item)">
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
        <button
          type="button"
          class="msfa-custom-btn-create btn btn-outline-primary btn-outline-primary--cancel btn-radius"
          @click="openModalZ05_S05_Choice_Of_Masterial"
        >
          <span class="btn-iconLeft"> <i class="el-icon-plus"></i> <span>新規登録</span> </span>
        </button>
      </div>
      <div class="contentSetting-box">
        <div class="contentSetting-lst scrollbar">
          <ul v-if="document_list.length > 0">
            <li v-for="document in document_list" :key="document">
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
                <a class="text_name">
                  <span class="text">{{ document.document_name }}</span>
                </a>
                <span class="item-link">
                  <img class="svg" src="@/assets/template/images/icon_openlink.svg" alt="" />
                </span>
              </div>
              <button type="button" class="btn btn--icon" @click="handleRemoveDocument(document)">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
              </button>
            </li>
          </ul>
          <EmptyData v-else custom-class="custom-class-no-data" in-height="120" in-width="120" title="資料を選択してください。" thumb-margin-top="20px" />
        </div>
      </div>
    </div>
    <div class="contentSetting-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel()">キャンセル</button>
      <button type="button" class="btn btn-primary" @click="saveButton">保存</button>
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :show-close="modalConfig.isShowClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="handleConfirmYes"></component>
    </template>
  </el-dialog>
</template>

<script>
import { mapGetters } from 'vuex';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05_S05_Choice_Of_Masterial from '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial';
import EmptyData from '@/components/EmptyData';
import { markRaw } from 'vue';
import _ from 'lodash';

export default {
  name: 'A02_S03_ModalEditStock',
  components: {
    Z05_S06_Material_Selection,
    Z05_S05_Choice_Of_Masterial,
    EmptyData
  },
  props: {
    inStockId: {
      type: Array,
      default: () => []
    },
    inContentCd: {
      type: String,
      default: ''
    },
    inProductList: {
      type: Array,
      default: () => []
    },
    inDocumentId: {
      type: Array,
      default: () => []
    },
    inFacilityCategoryType: {
      type: Array,
      default: () => []
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      stock_id: this.inStockId,
      content_cd: this.inContentCd,
      product_list: [...this.inProductList],
      document_list: [...this.inDocumentId],
      facility_category_type_list: this.inFacilityCategoryType,
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowClose: false,
        component: null,
        props: {},
        width: 0,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      props_Z05_S06_Material_Selection: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
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
      }
    };
  },
  computed: {
    ...mapGetters({
      facilityCategory: 'A02/a02_S03_all_facility_category',
      allContent: 'A02/a02_S03_all_content'
    })
  },
  mounted() {
    if (!this.facilityCategory || !this.allContent) {
      this.getScreenData();
    }
    this.js_pscroll();
  },
  methods: {
    getScreenData() {
      this.$store.dispatch('A02/getAllFacilityCategoryAction');
      this.$store.dispatch('A02/getAllContentAction');
    },

    formatArrayString(paramName, arrayObject) {
      return arrayObject.map((item) => item[paramName]);
    },

    handleRemoveProduct(item) {
      this.product_list = this.product_list.filter((x) => x.product_cd !== item.product_cd);
    },

    handleRemoveDocument(item) {
      this.document_list = this.document_list.filter((x) => x.document_id !== item.document_id);
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowClose: false };
    },

    openModalZ05_S05_Choice_Of_Masterial() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材選択',
        customClass: 'custom-dialog custom-dialog-pd-none',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z05_S05_Choice_Of_Masterial),
        width: '77rem',
        props: {
          mode: 'multiple',
          subMaterialSelectableFlag: 1,
          customMaterialFlag: 1,
          params: {
            ...this.paramsZ05S05,
            initMaterials: this.document_list.map((x) => x.document_id)
          }
        }
      };
    },

    openModalZ05_S06_Material_Selection() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z05_S06_Material_Selection),
        props: {
          ...this.props_Z05_S06_Material_Selection,
          initDataCodes: this.formatArrayString('product_cd', this.product_list)
        },
        width: '33rem'
      };
    },

    onResultModal(data) {
      if (data && data.screen === 'Z05_S06') {
        this.onResultModal_Z05_S06_Material_Selection(data);
      }
      if (data && data.screen === 'Z05_S05') {
        this.onResultModal_Z05_S05_Choice_Of_Masterial(data);
      }
      this.onCloseModal();
    },

    onResultModal_Z05_S06_Material_Selection(data) {
      this.props_Z05_S06_Material_Selection = {
        ...this.props_Z05_S06_Material_Selection,
        classificationCode: data.classifications.product_group_cd || '',
        categoryCode: data.category.definition_value || '',
        initDataCodes: data.dataSelected
      };
      this.product_list = data.dataSelected;
    },

    onResultModal_Z05_S05_Choice_Of_Masterial(data) {
      this.document_list = [];
      data.dataMasterialSelected.map((document) => {
        if (!this.document_list.some((item) => parseInt(item.document_id) === parseInt(document.document_id))) {
          this.document_list.push(document);
        }
      });
    },

    saveButton() {
      let result = {
        screen: 'A02_S03_ModalEditStock',
        stock_id: this.stock_id,
        content_cd: this.content_cd,
        product_list: this.product_list,
        document_list: this.document_list,
        facility_category_type_list: this.facility_category_type_list
      };
      this.$emit('onFinishScreen', result);
    },

    confirmCancel() {
      let isEqual =
        !_.isEqual(this.inProductList, this.product_list) || !_.isEqual(this.inDocumentId, this.document_list) || !_.isEqual(this.inContentCd, this.content_cd);
      if (isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1 },
          width: '35rem',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.handleConfirmYes();
      }
    },

    handleConfirmYes() {
      this.$emit('onFinishScreen');
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.modal-body {
  padding: 0px 0px 0px 0px;
}
.custom-class-no-data {
  height: 256px;
}

.el-tag-custom {
  height: 30px;
  line-height: 26px;
  font-size: 14px;
  align-items: center;
  margin-right: 10px !important;
  background: #b7d4ff;
  border-radius: 24px;
  color: #225999;
}
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
      box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
      border-radius: 8px;
      margin-top: 12px;
      padding: 8px 12px;
      height: 286px;
      .contentSetting-lst {
        max-height: 256px;
        li {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 15px 14px;
          border-bottom: 1px solid #e3e3e3;
          .contentSetting-link {
            padding-right: 10px;
            display: flex;
            align-items: center;
            max-width: 300px;
            .item-setting {
              min-width: 17px;
              margin-right: 12px;
              img {
                max-width: 17px;
              }
            }
            .text_name {
              color: #448add;
            }
            .item-link {
              display: none;
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
</style>
