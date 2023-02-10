<template>
  <div v-load-f5="true" class="custom-body modal-body-E01S02-AnswerRegistration">
    <div id="msfa-notify-E01S02-AnswerRegistration"></div>
    <div class="answer">
      <div class="answer-textarea">
        <el-input v-model="note" clearable class="form-control-textarea" rows="10" type="textarea" placeholder="回答入力" />
      </div>
      <div class="answer-btn answer-confirm">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <el-button type="primary" class="btn btn-primary" :loading="processingRegister" :disabled="processingRegister || !note" @click.prevent="registerAnswer">
          回答する
        </el-button>
      </div>
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
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModalConfirm" @handleYes="handleConfirmYes"></component>
    </template>
  </el-dialog>
</template>

<script>
import E01_S02_QaDetailsServices from '../../../api/E01/E01_S02_QaDetailsServices';
import { markRaw } from 'vue';

export default {
  name: 'E01_S02_QaDetails_AnswerRegistration_Modal',
  props: {
    question_id: {
      type: String,
      default: ''
    }
  },
  emits: ['onFinishScreen', 'registerBestAnswerSubmit'],
  data() {
    return {
      note: '',
      processingRegister: false,

      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        isShowClose: false,
        closeOnClickMark: false
      }
    };
  },

  methods: {
    confirmCancel() {
      if (this.note) {
        this.modalConfig = {
          ...this.modalConfig,
          title: null,
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1 }
        };
      } else {
        this.closeModal(false);
      }
    },

    handleConfirmYes() {
      this.closeModal();
    },

    onCloseModalConfirm() {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: false,
        isShowClose: false,
        customClass: 'custom-dialog'
      };
    },

    closeModal(isRegis) {
      this.$emit('onFinishScreen', { isReload: isRegis });
    },
    // submitModal() {
    //   this.$emit('registerBestAnswerSubmit');
    // },
    registerAnswer() {
      this.processingRegister = true;
      const data = {
        question_id: this.question_id,
        note: this.note
      };
      E01_S02_QaDetailsServices.registerAnswerForQuestion(data)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(async () => {
          this.processingRegister = false;
          this.closeModal(true);
        });
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.customBtn1 {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 24%;
    top: 29%;
  }
}

.answer-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-top: 15px;

  button {
    width: 150px;
  }
}
</style>
