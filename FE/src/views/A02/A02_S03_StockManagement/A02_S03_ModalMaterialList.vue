<template>
  <!-- Modal -->
  <!-- width: 558
        screen name: 資材一覧 -->
  <!-- Start  -->
  <div class="modal-body modal-materialList">
    <div class="materialList-box">
      <ul class="scrollbar">
        <li v-for="document in document_list" :key="document">
          <span class="text_name">
            <span class="item">
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
                :src="require('@/assets/images/icon_document_spanner_small.svg')"
                alt=""
              />
            </span>
            <span class="a02s03Tod01s02" style="cursor: pointer" @click="toDocumentDetail(document)">{{ document.document_name }}</span>
          </span>
        </li>
      </ul>
    </div>
    <div class="materialList-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel el-dialog__headerbtn" @click="$emit('onFinishScreenA02S03')">
        閉じる
      </button>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :top="'10vh'"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
      </template>
    </el-dialog>
  </div>
  <!-- End -->
</template>

<script>
import D01_S02_ModalMaterialDetails from '@/views/D01/D01_S02_MaterialDetails/D01_S02_ModalMaterialDetails';
import { markRaw } from 'vue';
export default {
  name: 'A02_S03_ModalMaterialList',
  props: {
    inDocumentId: {
      type: Array,
      default: () => []
    }
  },
  emits: ['onFinishScreenA02S03'],
  data() {
    return {
      document_list: this.inDocumentId,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        customClass: 'custom-dialog custom-dialog-pd'
      }
    };
  },
  methods: {
    toDocumentDetail(document) {
      let document_id = document?.document_id;
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
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
/* Modal */
.modal-body {
  padding: 0px 0px 0px 0px;
}
.modal-materialList {
  .materialList-box {
    background: #fff;
    border-radius: 8px;
    box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
    padding: 8px 0;
    ul {
      padding: 0 12px;
      height: 328px;
      li {
        border-bottom: 1px solid #e3e3e3;
        .text_name {
          padding: 16px 12px;
          display: flex;
          align-items: center;
          color: #448add;
          .item {
            width: 17px;
            min-width: 17px;
            margin-right: 12px;
            .svg {
              width: 17px;
            }
          }
        }
      }
    }
  }
  .materialList-btn {
    text-align: center;
    margin-top: 20px;
    .btn {
      width: 150px;
    }
  }
  .el-dialog__headerbtn {
    position: unset;
  }
}
</style>
