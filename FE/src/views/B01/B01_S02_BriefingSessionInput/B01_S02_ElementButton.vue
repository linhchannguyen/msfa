<template>
  <div class="lectureBtn">
    <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="openModalConfirm('cancel')">キャンセル</button>
    <button v-if="isDeleteBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="openModalConfirm(null, true)">削除</button>
    <button v-if="isCancelSubmitBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelSubmit(true)">提出取消</button>
    <button v-if="isPendingBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="pending(true)">中止</button>
    <button v-if="isRemandBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="openModalRemand(true)">差戻</button>
    <button v-if="isSaveBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="saveData()">保存</button>
    <button v-if="isSubmitBtn" type="button" class="btn btn-primary" @click="submitData()">提出</button>
    <button v-if="isApproveBtn" type="button" class="btn btn-primary" @click="openModalApprove(true)">承認</button>
  </div>

  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :show-close="modalConfig.isShowClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    @close="handleClose"
  >
    <template #default>
      <component
        :is="modalConfig.component"
        v-bind="{ ...modalConfig.props }"
        @onFinishScreen="onResultModal"
        @handleConfirm="handleConfirmDelete"
        @handleYes="handleConfirmYes"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
/* eslint-disable eqeqeq */
import _ from 'lodash';
import { markRaw } from 'vue';
import B01_S02_BriefingSessionInputService from '@/api/B01/B01_S02_BriefingSessionInputService';
import B01_S02_ModalApprove from './B01_S02_ModalApprove.vue';
import B01_S02_ModalRemand from './B01_S02_ModalRemand.vue';
import D01_S06_MaterialEvaluationInput from '@/views/D01/D01_S06_MaterialEvaluationInput/D01_S06_MaterialEvaluationInput';

export default {
  name: 'ElementButton',
  components: {},
  props: {
    formData: {
      type: Object,
      required: true
    },

    oldFormData: {
      type: Object,
      required: true
    },

    permissions: {
      type: Object,
      required: true,
      default() {
        return {
          policyRole: [],
          isUserApprover: false,
          isUserPlaner: false,
          status_check: '',
          lstMode: [],
          isUserLoginApprovedOrRemanded: false,
          document_usage_type: null,
          request_type_plan: '',
          request_type_result: '',
          active_layer_approval: 1
        };
      }
    }
  },
  emits: ['reload', 'submit', 'loadingTrue', 'loadingFalse', 'back', 'scrollTop'],
  data: function () {
    return {
      isSaveBtn: false,
      isDeleteBtn: false,
      isSubmitBtn: false,
      isCancelSubmitBtn: false,
      isPendingBtn: false,
      isApproveBtn: false,
      isRemandBtn: false,
      isCancelBtn: true,

      typeButton: '',

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowClose: false,
        component: null,
        props: {},
        width: '',
        destroyOnClose: true,
        closeOnClickMark: false
      },

      isReviewDoc: false
    };
  },

  mounted() {
    let CancelSubmit = false;
    if (this.permissions.policyRole) {
      CancelSubmit = this.permissions.policyRole?.includes('R20') && this.permissions.isUserPlaner ? true : false;
    }

    let lstMode = this.permissions.lstMode;
    let status = this.permissions.status_check;

    if (lstMode.includes('createMode')) {
      this.isSaveBtn = true;
      this.isSubmitBtn = true;
    }

    if (lstMode.includes('editMode')) {
      switch (status) {
        case '10':
          this.isSaveBtn = true;
          this.isDeleteBtn = true;
          this.isSubmitBtn = true;
          break;
        case '30':
        case '40':
          this.isSaveBtn = true;
          this.isSubmitBtn = true;
          this.isPendingBtn = true;
          break;
      }
    }

    if (lstMode.includes('approvalMode')) {
      switch (status) {
        case '20':
        case '50':
          this.isApproveBtn = this.permissions.isUserApprover;
          this.isRemandBtn = this.permissions.isUserApprover;
          break;
      }
    }

    if (lstMode.includes('viewMode')) {
      switch (status) {
        case '20':
        case '50':
          this.isCancelSubmitBtn = CancelSubmit;
          break;
      }
    }
    this.js_pscroll();
  },

  created() {},

  methods: {
    // save
    convertParamsSave() {
      let timeStart = this.formData.start_date + ' ' + this.formData.start_time;
      let timeEnd = this.formData.start_date + ' ' + this.formData.end_time;

      let today = new Date(new Date().setHours(0, 0, 0, 0));
      let startDate = new Date(new Date(this.formData.start_date).setHours(0, 0, 0, 0));

      let request_type = this.permissions.request_type_plan;
      if (!this.permissions.status_check || this.permissions.status_check == '10') {
        request_type = this.permissions.request_type_plan;
      }
      if (this.permissions.status_check == '30' || this.permissions.status_check == '40') {
        request_type = this.permissions.request_type_result;
      }

      if (!this.permissions.status_check && startDate.getTime() <= today.getTime()) {
        request_type = this.permissions.request_type_result;
      }

      let params = {
        ...this.formData,
        start_time: this.formData.start_time ? timeStart : '',
        end_time: this.formData.end_time ? timeEnd : '',
        request_type: request_type
      };

      return params;
    },

    saveData() {
      this.$emit('submit');
      let statusType = this.formData.status_type;

      let today = new Date(new Date().setHours(0, 0, 0, 0));
      let startDate = new Date(new Date(this.formData.start_date).setHours(0, 0, 0, 0));

      if (!this.permissions.status_check && startDate.getTime() <= today.getTime()) {
        statusType = '40';
      }

      if (!this.checkValidMessageSum()) {
        let params = {
          ...this.convertParamsSave(),
          status_type: statusType
        };

        this.$emit('loadingTrue');

        B01_S02_BriefingSessionInputService.saveData(params)
          .then((res) => {
            this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
            let isPasted = !this.permissions.status_check && startDate.getTime() <= today.getTime();
            if (this.permissions.status_check == '30' || this.permissions.status_check == '40' || isPasted) {
              let data = res.data?.data;
              if (data.document_review == 0) {
                let briefing_type = this.permissions.document_usage_type;
                let briefing_id = data.briefing_id;
                this.openModalReviewDocument(briefing_type, briefing_id);
              } else {
                this.gotoBack();
              }
            } else {
              this.gotoBack();
            }
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {
            this.$emit('loadingFalse');
          });
      } else {
        this.$emit('scrollTop');
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
      }
    },

    // delete
    deleteBriefing() {
      let params = {
        briefing_id: this.formData.briefing_id
      };
      B01_S02_BriefingSessionInputService.delete(params)
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0050'), customClass: 'success' });
          this.gotoBack();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    // submit
    submitData() {
      this.$emit('submit');

      let status = this.formData.status_type;

      let today = new Date(new Date().setHours(0, 0, 0, 0));
      let startDate = new Date(new Date(this.formData.start_date).setHours(0, 0, 0, 0));

      if (!this.permissions.status_check || this.permissions.status_check == '10') {
        status = '20';
      }

      if (this.permissions.status_check == '30' || this.permissions.status_check == '40') {
        status = '50';
      }

      if (!this.permissions.status_check && startDate.getTime() <= today.getTime()) {
        status = '50';
      }

      if (!this.checkValidMessageSum()) {
        let params = {
          ...this.convertParamsSave(),
          status_type: status
        };
        this.$emit('loadingTrue');

        B01_S02_BriefingSessionInputService.submitData(params)
          .then((res) => {
            this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
            let isPasted = !this.permissions.status_check && startDate.getTime() <= today.getTime();
            if (this.permissions.status_check == '30' || this.permissions.status_check == '40' || isPasted) {
              let data = res.data?.data;
              if (data.document_review == 0) {
                let briefing_type = this.permissions.document_usage_type;
                let briefing_id = data.briefing_id;
                this.openModalReviewDocument(briefing_type, briefing_id);
              } else {
                this.gotoBack();
              }
            } else {
              this.gotoBack();
            }
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {
            this.$emit('loadingFalse');
          });
      } else {
        this.$emit('scrollTop');
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
      }
    },

    // cancelSubmit
    cancelSubmit(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('cancelSubmit');
      } else {
        let params = {
          briefing_id: this.formData.briefing_id,
          status_type: parseInt(this.permissions.status_check) - 10
        };
        B01_S02_BriefingSessionInputService.cancelSubmit(params)
          .then(() => {
            this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
            this.$emit('reload');
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {
            this.onCloseModal();
          });
      }
    },

    // remand
    remand(comment) {
      let request_type = this.permissions.request_type_plan;
      if (this.permissions.status_check == '20') {
        request_type = this.permissions.request_type_plan;
      }
      if (this.permissions.status_check == '50') {
        request_type = this.permissions.request_type_result;
      }
      let params = {
        briefing_id: this.formData.briefing_id,
        status_type: (parseInt(this.permissions.status_check) - 10).toString(),
        briefing_name: this.formData.briefing_name,
        comment: comment,
        request_type: request_type,
        active_layer_approval: this.permissions.active_layer_approval
      };
      this.$emit('loadingTrue');

      B01_S02_BriefingSessionInputService.remand(params)
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
          this.gotoBack();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.$emit('loadingFalse');
        });
    },

    // approval
    approval(comment) {
      let request_type = this.permissions.request_type_plan;
      if (this.permissions.status_check == '20') {
        request_type = this.permissions.request_type_plan;
      }
      if (this.permissions.status_check == '50') {
        request_type = this.permissions.request_type_result;
      }

      let params = {
        briefing_id: this.formData.briefing_id,
        status_type: (parseInt(this.permissions.status_check) + 10).toString(),
        briefing_name: this.formData.briefing_name,
        comment: comment,
        request_type: request_type,
        active_layer_approval: this.permissions.active_layer_approval
      };
      this.$emit('loadingTrue');
      B01_S02_BriefingSessionInputService.approval(params)
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
          this.gotoBack();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.$emit('loadingFalse');
        });
    },

    // pending
    pending(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('pending');
      } else {
        let params = {
          briefing_id: this.formData.briefing_id
        };
        B01_S02_BriefingSessionInputService.pending(params)
          .then(() => {
            this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
            this.gotoBack();
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {
            this.onCloseModal();
          });
      }
    },

    openModalConfirm(type, isDelete) {
      this.typeButton = type;
      let message = '';

      if (isDelete) {
        this.openConfirm(type, isDelete);
      } else {
        switch (this.typeButton) {
          case 'approval':
            message = this.permissions.status_check === '20' ? 'この説明会計画を承認します。よろしいですか？' : 'この説明会結果を承認します。よろしいですか？';
            this.openConfirm(type, isDelete, message);
            break;
          case 'remand':
            message =
              this.permissions.status_check === '20'
                ? 'この説明会計画の提出を差し戻します。よろしいですか？'
                : 'この説明会結果の提出を差し戻します。よろしいですか？';
            this.openConfirm(type, isDelete, message);
            break;
          case 'cancelSubmit':
            message =
              this.permissions.status_check === '20'
                ? 'この説明会計画の提出を取り消します。よろしいですか？'
                : 'この説明会結果の提出を取り消します。よろしいですか？';
            this.openConfirm(type, isDelete, message);
            break;
          case 'pending':
            message = 'この説明会を取り消します。よろしいですか？';
            this.openConfirm(type, isDelete, message);
            break;
          case 'cancel':
            message = '';
            if (!_.isEqual(this.formData, this.oldFormData)) {
              this.openConfirm(type, isDelete, message);
            } else {
              this.gotoBack();
            }
            break;
        }
      }
    },

    openConfirm(type, isDelete, message) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { mode: isDelete ? 0 : 1, message: isDelete ? '' : message, title: isDelete ? '選択した説明会を完全に削除しますか?' : '' }
      };
    },

    handleConfirmYes() {
      switch (this.typeButton) {
        case 'approval':
          this.openModalApprove();
          break;
        case 'remand':
          this.openModalRemand();
          break;
        case 'cancelSubmit':
          this.cancelSubmit();
          break;
        case 'pending':
          this.pending();
          break;
        case 'cancel':
          this.gotoBack();
          break;

        default:
          break;
      }
    },

    handleConfirmDelete() {
      this.deleteBriefing();
    },

    openModalApprove(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('approval');
      } else {
        this.modalConfig = {
          ...this.modalConfig,
          title: null,
          isShowModal: true,
          isShowClose: false,
          component: markRaw(B01_S02_ModalApprove),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: {}
        };
      }
    },

    openModalRemand(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('remand');
      } else {
        this.modalConfig = {
          ...this.modalConfig,
          title: null,
          isShowModal: true,
          isShowClose: false,
          component: markRaw(B01_S02_ModalRemand),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: {}
        };
      }
    },

    openModalReviewDocument(briefing_type, briefing_id) {
      this.isReviewDoc = true;
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(D01_S06_MaterialEvaluationInput),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          documentUsageType: briefing_type,
          documentUsageId: briefing_id
        }
      };

      const body = document.querySelector('.briefingContent');
      body.classList.remove('scrollbar');
      body.classList.remove('ps');
      body.classList.remove('ps--active-y');
    },

    // back
    gotoBack() {
      // this.back();
      this.$emit('back', true);
    },

    handleClose() {
      if (this.isReviewDoc) {
        this.isReviewDoc = false;
        this.gotoBack();
      }
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'B01_S02_Approve') {
          this.approval(e.comment);
        }
        if (e.screen === 'B01_S02_Remand') {
          this.remand(e.comment);
        }
        if (e.screen === 'D01_S06') {
          if (e.saveReview) {
            this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
          }
          this.isReviewDoc = false;
          this.gotoBack();
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.typeButton = '';
      this.processingCancelSubmit = false;
      this.processingPending = false;
      this.processingCopy = false;
      this.processingSave = false;
      this.processingSubmitData = false;
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowClose: false, isShowModalConfirmInput: false };
    },

    validationMessageForm(isRequire, field, name, msgNumber, type, lengNumber) {
      let data = this.formData[field];
      if (type == 'array') {
        if (!data || (data && data.length === 0) || (data && !data.some((x) => x.delete_flag === 0 || x.delete_flag == 1))) {
          return this.getMessage(msgNumber, name);
        }
      } else {
        if (!data && data !== 0 && isRequire) {
          return this.getMessage(msgNumber, name);
        } else {
          if (lengNumber && data && data.length > lengNumber) {
            return this.getMessage('MSFA0012', name, lengNumber);
          }
        }
      }
      return '';
    },

    checkValidateForm(isRequire, field, name, msgNumber, type, lengNumber) {
      if (this.validationMessageForm(isRequire, field, name, msgNumber, type, lengNumber)) {
        return true;
      }
      return false;
    },

    checkValidMessageSum() {
      let message = '';

      if (this.checkValidateForm(true, 'briefing_name', '説明会名', 'MSFA0001', 'String', 100)) {
        message = this.validationMessageForm(true, 'briefing_name', '説明会名', 'MSFA0001', 'String', 100);
      } else {
        if (this.checkValidateForm(true, 'start_date', '日', 'MSFA0040')) {
          message = this.validationMessageForm(true, 'start_date', '日', 'MSFA0040');
        } else {
          if (this.checkValidateForm(true, 'start_time', '時', 'MSFA0040')) {
            message = this.validationMessageForm(true, 'start_time', '時', 'MSFA0040');
          } else {
            if (this.checkValidateForm(true, 'implement_user_name', '実施者', 'MSFA0040')) {
              message = this.validationMessageForm(true, 'implement_user_name', '実施者', 'MSFA0040');
            } else {
              if (this.checkValidateForm(true, 'target_org_label', '対象組織', 'MSFA0040')) {
                message = this.validationMessageForm(true, 'target_org_label', '対象組織', 'MSFA0040');
              } else {
                if (this.checkValidateForm(false, 'place', '場所', null, 'String', 255)) {
                  message = this.validationMessageForm(false, 'place', '場所', null, 'String', 255);
                }
              }
            }
          }
        }
      }

      return message;
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;

.lectureBtn {
  text-align: center;
  margin-top: 28px;
  padding: 28px 52px 0;
  @media (max-width: $viewport_desktop) {
    padding: 28px 24px 0;
  }
  .btn {
    margin-right: 24px;
    width: 180px;
    margin-bottom: 15px;
    @media (max-width: $viewport_tablet) {
      width: 140px;
      margin-right: 16px;
    }
    &:last-child {
      margin-right: 0;
    }
  }
}

label.btn,
label.btn .btn-iconLeft {
  line-height: 30px;
  cursor: pointer;
}
</style>
