<template>
  <!-- title: 資材評価入力 -->
  <!-- width: 55%, 40rem -->
  <!-- props: documentUsageType, documentUsageId -->
  <div class="modal-material modal-body-D01S06">
    <div id="msfa-notify-D01S06"></div>
    <div v-if="documentList.length > 0" class="remark">
      <div>※</div>
      <div>使用した資材への反応や使用感は如何だったでしょうか？あなたの評価・コメントをお聞かせください。</div>
    </div>
    <div v-loading="isLoading" class="lstMaterial scrollbar">
      <ul v-if="documentList.length > 0">
        <li v-for="item in documentList" :key="item.document_id">
          <h2 class="lstMaterial-tlt">
            <span class="link log-icon" title_log="資材名" @click="callScreen(item)">{{ item.document_name }}</span>
          </h2>
          <div class="lstMaterial-row">
            <div class="lstMaterial-col1">
              <label class="lstMaterial-label">評価</label>
              <div class="lstStar">
                <el-rate v-model="item.document_review" :disabled="item.checkEdit" disabled-void-color="#dcdcdc" @change="handleChangeRate" />
              </div>
            </div>
            <div class="lstMaterial-col2">
              <label class="lstMaterial-label">評価コメント</label>
              <div class="lstMaterial-control">
                <el-input
                  v-model="item.review_comment"
                  :readonly="item.checkEdit || !item.document_review"
                  :class="`form-control-textarea ${item.checkEdit || !item.document_review ? 'disabledArea' : ''}`"
                  :rows="3"
                  type="textarea"
                  placeholder="評価コメント入力"
                  @input="handleChangeComment"
                />
              </div>
            </div>
          </div>
        </li>
      </ul>
      <EmptyData v-else-if="!isLoading" custom-class="customClassEmpty" thumb-margin-top="20px" />
    </div>
    <div class="material-btn text-center">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelBtn">キャンセル</button>
      <button type="button" class="btn btn-primary" :disabled="!edit_flg" @click="saveBtn">保存</button>
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModalConfirm" @handleYes="closeModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import D01_S06_Service from '@/api/D01/D01_S06_MaterialEvaluationInputServices';
import D01_S02_ModalMaterialDetails from '@/views/D01/D01_S02_MaterialDetails/D01_S02_ModalMaterialDetails';

import { markRaw } from 'vue';
import _ from 'lodash';

export default {
  name: 'D01_S06_MaterialEvaluationInput',
  props: {
    documentUsageType: {
      type: Number || String,
      default: 0,
      required: true
    },
    documentUsageId: {
      type: Number,
      default: 2,
      required: true
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      isLoading: true,
      initParams: {
        document_usage_id: '',
        document_usage_type: ''
      },
      documentList: [],
      documentListOld: [],
      edit_flg: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowClose: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },

  mounted() {
    this.initParams = {
      document_usage_id: this.documentUsageId,
      document_usage_type: this.documentUsageType
    };
    this.getList(this.initParams);
  },

  methods: {
    // define func
    handleChangeComment() {
      this.edit_flg = true;
    },

    handleChangeRate() {
      this.edit_flg = true;
    },

    cancelBtn() {
      if (_.isEqual(this.documentList, this.documentListOld)) {
        this.closeModal(false);
      } else {
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
      }
    },

    closeModal(save) {
      this.$emit('onFinishScreen', { screen: 'D01_S06', saveReview: save });
    },

    onCloseModalConfirm() {
      this.modalConfig = {
        ...this.modalConfig,
        customClass: 'custom-dialog',
        isShowModal: false
      };
    },

    saveBtn() {
      if (this.edit_flg) if (this.documentList.length > 0) this.register(this.documentList);
    },

    callScreen(item) {
      let document_id = item.document_id;
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        isShowClose: true,
        customClass: 'custom-dialog-pd-none custom-dialog custom-dialog-pd custom-dialog-material',
        component: markRaw(D01_S02_ModalMaterialDetails),
        props: { documentId: document_id },
        width: '90%',
        destroyOnClose: true,
        closeOnClickMark: false
      };
    },

    // call api
    getList(params) {
      this.isLoading = true;
      D01_S06_Service.getListService(params)
        .then((res) => {
          const dataRes = res.data.data;
          this.documentList = dataRes.map((item) => ({
            ...item,
            review_customize_document_id: '',
            document_review: item.document_review ? item.document_review : 0,
            checkEdit: item.document_review && item.review_comment
          }));

          this.documentListOld = JSON.parse(JSON.stringify(this.documentList));
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-D01S06', idNodeNotify: 'msfa-notify-D01S06' });
        })
        .finally(() => {
          this.isLoading = false;

          this.js_pscroll();
        });
    },

    register(params) {
      D01_S06_Service.registerItemService({ data: params })
        .then(() => {
          this.edit_flg = false;
          this.closeModal(true);
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-D01S06', idNodeNotify: 'msfa-notify-D01S06' });
        })
        .finally();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.modal-material {
  padding: 20px 0 0;
  margin: -32px;

  background: white;
  border-radius: 10px;
  .lstMaterial {
    padding: 6px 32px 0;
    height: 500px;
    > ul {
      > li {
        background: #fff;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 20px 32px;
        text-align: left;
      }
    }
    .lstMaterial-tlt {
      font-size: 1.375rem;
      font-weight: 500;
    }
    .lstMaterial-row {
      display: flex;
      flex-wrap: wrap;
      .lstMaterial-col1 {
        width: 182px;
      }
      .lstMaterial-col2 {
        width: calc(100% - 182px);
      }
      .lstMaterial-label {
        margin: 20px 0 8px;
      }
    }
    .customClassEmpty {
      height: 462px;
    }
  }
  .material-btn {
    padding: 20px 32px 32px;
    background: #f7f7f7;
    box-shadow: 0px -3px 6px rgba(141, 82, 82, 0.1);
    border-radius: 0 0 10px 10px;
    position: relative;
    z-index: 2;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }

  .remark {
    padding: 2px 32px 10px;
    color: #ed5e54;
    font-weight: 500;
    display: flex;
  }
}
</style>
