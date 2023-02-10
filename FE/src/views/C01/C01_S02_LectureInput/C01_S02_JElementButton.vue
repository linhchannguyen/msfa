<template>
  <div class="lectureBtn">
    <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="openModalConfirm('cancel')">キャンセル</button>
    <button v-if="isDeleteBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="openModalConfirm(null, true)">削除</button>
    <button v-if="isCancelSubmitBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelSubmit(true)">提出取消</button>
    <button v-if="isPendingBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="pending(true)">中止</button>
    <button v-if="isRemandBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="openModalRemand(true)">差戻</button>
    <button v-if="isCopyBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="copyData(true)">コピー</button>
    <button v-if="isSaveBtn" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="saveData()">保存</button>
    <button v-if="isSubmitBtn" type="button" class="btn btn-primary" @click="submitData()">提出</button>
    <button v-if="isApproveBtn" type="button" class="btn btn-primary" @click="openModalApprove(true)">承認</button>
  </div>

  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="modalConfig.isShowClose"
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
import C01_S02_LectureInputService from '@/api/C01/C01_S02_LectureInputService';
import C01_S02_ModalApprove from './C01_S02_ModalApprove.vue';
import C01_S02_ModalRemand from './C01_S02_ModalRemand.vue';
import D01_S06_MaterialEvaluationInput from '@/views/D01/D01_S06_MaterialEvaluationInput/D01_S06_MaterialEvaluationInput';
import { encodeData } from '@/api/base64_api';

export default {
  name: 'J_Element_Button',
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
          isUserTargetOrg: false,
          isUserApprover: false,
          isUserPlaner: false,
          status_check: '',
          lstMode: [],
          isUserLoginApprovedOrRemanded: false,
          document_usage_type: null,
          active_layer_approval: 1
        };
      }
    }
  },
  emits: ['reload', 'submit', 'loadingTrue', 'loadingFalse', 'callApiCopyData', 'back', 'scrollTop'],
  data: function () {
    return {
      isSaveBtn: false,
      isDeleteBtn: false,
      isSubmitBtn: false,
      isCancelSubmitBtn: false,
      isPendingBtn: false,
      isCopyBtn: false,
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
    let copy = false;
    let CancelSubmit = false;
    if (this.permissions.policyRole) {
      copy = this.permissions.policyRole?.includes('R30') ? true : false;
      CancelSubmit = this.permissions.policyRole?.includes('R30') && (this.permissions.isUserPlaner || this.permissions.isUserTargetOrg) ? true : false;
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
          this.isCopyBtn = copy;
          break;
        case '30':
        case '40':
          this.isSaveBtn = true;
          this.isSubmitBtn = true;
          this.isPendingBtn = true;
          this.isCopyBtn = copy;
          break;
      }
    }

    if (lstMode.includes('approvalMode')) {
      switch (status) {
        case '20':
        case '50':
          this.isApproveBtn = this.permissions.isUserApprover;
          this.isRemandBtn = this.permissions.isUserApprover;
          this.isCopyBtn = copy;
          break;
      }
    }

    if (lstMode.includes('viewMode')) {
      switch (status) {
        case '10':
        case '30':
        case '40':
        case '60':
        case '70':
          this.isCopyBtn = copy;
          break;

        case '20':
        case '50':
          this.isCancelSubmitBtn = CancelSubmit;
          this.isCopyBtn = copy;
          break;
      }
    }
    this.js_pscroll();
  },

  created() {},

  methods: {
    convertFormData(formData, data, parentKey) {
      if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
        Object.keys(data).forEach((key) => {
          this.convertFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key);
        });
      } else {
        const value = data == null ? '' : data;

        formData.append(parentKey, value);
      }
    },

    jsonToFormData(data) {
      const formData = new FormData();

      this.convertFormData(formData, data);

      return formData;
    },

    // copy
    copyData(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('copy');
      } else {
        this.$emit('callApiCopyData');
        this.onCloseModal();
      }
    },

    // save
    convertParamsSave() {
      let timeStart = this.formData.start_date + ' ' + this.formData.start_time;
      let timeEnd = this.formData.start_date + ' ' + this.formData.end_time;
      let params = {
        ...this.formData,
        organization_layer: null,
        start_time: this.formData.start_time ? timeStart : '',
        end_time: this.formData.end_time ? timeEnd : '',
        area_expense: JSON.stringify(this.formData.area_expense)
      };

      Object.keys(params).forEach((x) => {
        if (x != 'convention_file') {
          params[x] = encodeData(params[x]);
        }
      });

      return params;
    },

    saveData() {
      this.$emit('submit');
      if (!this.checkValidMessageSum()) {
        if (!this.permissions.status_check || this.permissions.status_check == '10') {
          this.savePlan();
        }
        if (this.permissions.status_check == '30' || this.permissions.status_check == '40') {
          this.saveResult();
        }
      } else {
        if (this.checkValidMessageSum() !== 'err_option') {
          this.$emit('scrollTop');
        }
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        this.$emit('loadingFalse');
      }
    },

    async savePlan() {
      let params = { ...this.convertParamsSave() };

      this.$emit('loadingTrue');

      C01_S02_LectureInputService.savePlan(this.jsonToFormData(params))
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
          this.gotoBack();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.$emit('loadingFalse');
        });
    },

    saveResult() {
      let params = { ...this.convertParamsSave() };

      this.$emit('loadingTrue');
      C01_S02_LectureInputService.saveResult(this.jsonToFormData(params))
        .then((res) => {
          this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
          let data = res.data?.data;
          if (data.document_review == 0) {
            let convention_type = this.permissions.document_usage_type;
            let convention_id = data.convention_id;
            this.openModalReviewDocument(convention_type, convention_id);
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
    },

    // deleteConvention
    deleteConvention() {
      let params = {
        convention_id: this.formData.convention_id
      };
      C01_S02_LectureInputService.delete(params)
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
      if (!this.checkValidMessageSum()) {
        if (!this.permissions.status_check || this.permissions.status_check == '10') {
          this.submitPlan();
        }
        if (this.permissions.status_check == '30' || this.permissions.status_check == '40') {
          this.submitResult();
        }
      } else {
        if (this.checkValidMessageSum() !== 'err_option') {
          this.$emit('scrollTop');
        }
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
      }
    },

    submitPlan() {
      let params = { ...this.convertParamsSave() };
      this.$emit('loadingTrue');

      C01_S02_LectureInputService.submitPlan(this.jsonToFormData(params))
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

    submitResult() {
      let params = { ...this.convertParamsSave() };
      this.$emit('loadingTrue');

      C01_S02_LectureInputService.submitResult(this.jsonToFormData(params))
        .then((res) => {
          let data = res.data?.data;
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
          if (data.document_review == 0) {
            let convention_type = this.permissions.document_usage_type;
            let convention_id = data.convention_id;
            this.openModalReviewDocument(convention_type, convention_id);
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
    },

    // cancelSubmit
    cancelSubmit(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('cancelSubmit');
      } else {
        let params = {
          convention_id: this.formData.convention_id,
          status_type: parseInt(this.permissions.status_check) - 10
        };
        C01_S02_LectureInputService.cancelSubmit(params)
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
      this.$emit('loadingTrue');
      let params = {
        convention_id: this.formData.convention_id,
        status_type: (parseInt(this.permissions.status_check) - 10).toString(),
        convention_name: this.formData.convention_name,
        comment: comment,
        active_layer_approval: this.permissions.active_layer_approval
      };
      C01_S02_LectureInputService.remand(params)
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
      let params = {
        convention_id: this.formData.convention_id,
        status_type: (parseInt(this.permissions.status_check) + 10).toString(),
        convention_name: this.formData.convention_name,
        comment: comment,
        active_layer_approval: this.permissions.active_layer_approval
      };
      this.$emit('loadingTrue');
      C01_S02_LectureInputService.approval(params)
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
          convention_id: this.formData.convention_id
        };
        C01_S02_LectureInputService.pending(params)
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
          case 'copy':
            message = '';
            if (!_.isEqual(this.formData, this.oldFormData)) {
              this.openConfirm(type, isDelete, message);
            } else {
              this.copyData();
            }
            break;
          case 'approval':
            message = this.permissions.status_check === '20' ? 'この講演会計画を承認します。よろしいですか？' : 'この講演会結果を承認します。よろしいですか？';
            this.openConfirm(type, isDelete, message);
            break;
          case 'remand':
            message =
              this.permissions.status_check === '20'
                ? 'この講演会計画の提出を差し戻します。よろしいですか？'
                : 'この講演会結果の提出を差し戻します。よろしいですか？';
            this.openConfirm(type, isDelete, message);
            break;
          case 'cancelSubmit':
            message =
              this.permissions.status_check === '20'
                ? 'この講演会計画の提出を取り消します。よろしいですか？'
                : 'この講演会結果の提出を取り消します。よろしいですか？';
            this.openConfirm(type, isDelete, message);
            break;
          case 'pending':
            message = 'この講演会を中止にします。よろしいですか？';
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
        props: { mode: isDelete ? 0 : 1, message: isDelete ? '' : message, title: isDelete ? '選択した講演会を完全に削除しますか?' : '' }
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
        case 'copy':
          this.copyData();
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
      this.deleteConvention();
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
          component: markRaw(C01_S02_ModalApprove),
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
          component: markRaw(C01_S02_ModalRemand),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: {}
        };
      }
    },

    openModalReviewDocument(convention_type, convention_id) {
      this.isReviewDoc = true;
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材評価入力',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(D01_S06_MaterialEvaluationInput),
        width: '36rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          documentUsageType: convention_type,
          documentUsageId: convention_id
        }
      };

      const body = document.querySelector('.contentLecture');
      body.classList.remove('scrollbar');
      body.classList.remove('ps');
      body.classList.remove('ps--active-y');
    },

    handleClose() {
      if (this.isReviewDoc) {
        this.isReviewDoc = false;
        this.gotoBack();
      }
    },

    // back
    gotoBack() {
      this.$emit('back', true);
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'C01_S02_Approve') {
          this.approval(e.comment);
        }
        if (e.screen === 'C01_S02_Remand') {
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
      this.$emit('loadingFalse');

      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowModalConfirmInput: false, isShowClose: true };
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
      if (this.checkValidateForm(true, 'convention_name', '講演会名', 'MSFA0001', 'String', 100)) {
        message = this.validationMessageForm(true, 'convention_name', '講演会名', 'MSFA0001', 'String', 100);
      } else {
        if (this.checkValidateForm(true, 'start_date', '日', 'MSFA0040')) {
          message = this.validationMessageForm(true, 'start_date', '日', 'MSFA0040');
        } else {
          if (this.checkValidateForm(true, 'start_time', '時', 'MSFA0040')) {
            message = this.validationMessageForm(true, 'start_time', '時', 'MSFA0040');
          } else {
            if (this.checkValidateForm(false, 'place', '会場', null, 'String', 255)) {
              message = this.validationMessageForm(false, 'place', '会場', null, 'String', 255);
            } else {
              if (this.checkValidateForm(false, 'cohost_partner_name', '共催相手', null, 'String', 40)) {
                message = this.validationMessageForm(false, 'cohost_partner_name', '共催相手', null, 'String', 40);
              } else {
                if (this.checkValidateForm(false, 'cost_share_content', '開催費分担内容', null, 'String', 40)) {
                  message = this.validationMessageForm(false, 'cost_share_content', '開催費分担内容', null, 'String', 40);
                } else {
                  for (let index = 0; index < this.formData.area_expense.length; index++) {
                    const element = this.formData.area_expense[index];
                    for (let i = 0; i < element.layer_2.length; i++) {
                      const element_2 = element.layer_2[i];
                      if (
                        element_2.option_item_flag === 1 &&
                        element_2.plan_amount &&
                        (!element_2.option_item_name?.trim() || !element_2.option_item_content?.trim())
                      ) {
                        message = 'err_option';
                        break;
                      }
                    }
                  }
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
$viewport_tablet: 1024px;
$viewport_tablet_mini: 768px;

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
      width: 150px;
      margin-right: 16px;
    }
    @media (max-width: $viewport_tablet_mini) {
      width: 120px;
      margin-right: 10px;
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
