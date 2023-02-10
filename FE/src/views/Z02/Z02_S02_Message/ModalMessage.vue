/* eslint-disable max-len */
<template>
  <div class="modal-body modal-body-Z02S02">
    <div id="msfa-notify-Z02S02"></div>
    <div class="formMessage">
      <div class="box1">
        <div class="formGroup">
          <div class="messages-error">
            <label class="formGroup-label">
              件名<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
            ></label>
            <div>
              <div v-if="isSubmit && !validation.messageSubject.required" class="invalid-feedback">
                <span>{{ validateMessages.messageSubject.required }}</span>
              </div>
              <div v-if="validateMessageSubject()" class="invalid-feedback">
                <span class="invalid">{{ getMessage('MSFA0012', '件名', 30) }}</span>
              </div>
            </div>
          </div>

          <div class="formGroup-input formGroup-flex formGroup-check-custom-box">
            <div class="formGroup-check formGroup-check-custom" :class="{ activeInput: important_flag }">
              <label class="custom-control custom-checkbox">
                <input v-model="important_flag" :disabled="checkRole" class="custom-control-input" type="checkbox" checked />
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">重要</span>
              </label>
            </div>
            <div class="formGroup-control custom-form" :class="(isSubmit && !validation.messageSubject.required) || validateMessageSubject() ? 'hasErr' : ''">
              <input
                v-model="messageSubject"
                :disabled="checkRole"
                class="form-control"
                type="text"
                placeholder="メッセージタイトルを入力"
                @change="validateMessageSubject"
              />
            </div>
          </div>
        </div>
        <div class="formGroup">
          <label class="formGroup-label">
            掲載期間<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <div class="formGroup-date">
            <div class="formGroup-date-control">
              <div class="form-dateTime">
                <div class="form-icon icon-left form-icon--noBod">
                  <span class="icon">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                  </span>
                  <el-date-picker
                    v-model="release_start_date"
                    :disabled="checkRole"
                    :disabled-date="disabledDateStart"
                    format="YYYY/M/D"
                    type="date"
                    :editable="false"
                    placeholder="開始日"
                    :class="['form-control-datePickerLeft', isSubmit && !validation.release_start_date.required ? 'hasErr' : '']"
                  ></el-date-picker>
                </div>
              </div>
              <div v-if="isSubmit && !validation.release_start_date.required" class="invalid-feedback">
                <span>{{ validateMessages.release_start_date.required }}</span>
              </div>
            </div>
            <span class="formGroup-date-item"> ～</span>
            <div class="formGroup-date-control reponsive">
              <div class="form-dateTime">
                <div class="form-icon icon-left form-icon--noBod">
                  <span class="icon">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                  </span>
                  <el-date-picker
                    v-model="release_end_date"
                    :disabled="checkRole"
                    :disabled-date="disabledDateEnd"
                    format="YYYY/M/D"
                    type="date"
                    :editable="false"
                    placeholder="終了日"
                    :class="['form-control-datePickerLeft', isSubmit && !validation.release_end_date.required ? 'hasErr' : '']"
                  ></el-date-picker>
                </div>
              </div>
              <div v-if="isSubmit && !validation.release_end_date.required" class="invalid-feedback">
                <span>{{ validateMessages.release_end_date.required }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="formGroup">
          <label class="formGroup-label">表示対象組織</label>
          <div class="form-icon icon-right">
            <span title_log="表示対象組織" class="icon log-icon" @click="openModal_Z05_S01">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
            </span>
            <div v-if="org_label" class="form-control d-flex align-items-center">
              <div class="block-tags">
                <el-tag class="m-0 el-tag-custom" closable @close="handleRemoveUser()">
                  {{ org_label }}
                </el-tag>
              </div>
            </div>
            <el-input v-else clearable :disabled="checkRole" style="background: #ffffff" readonly placeholder="未決定" class="form-control-input" />
          </div>
        </div>
        <div class="formGroup">
          <label class="formGroup-label">
            発信者<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <div :class="['formGroup-input', (isSubmit && !validation.sender_name.required) || validateSenderName() ? 'hasErr' : '']">
            <input v-model="sender_name" :disabled="checkRole" class="form-control" type="text" placeholder="事業本部など" @change="validateSenderName" />
          </div>
          <div v-if="isSubmit && !validation.sender_name.required" class="invalid-feedback">
            <span>{{ validateMessages.sender_name.required }}</span>
          </div>
          <div v-if="validateSenderName()" class="invalid-feedback">
            <span class="invalid">{{ getMessage('MSFA0012', '発信者', 50) }}</span>
          </div>
        </div>
      </div>
      <div class="box2">
        <div v-loading="loadingEditorPlugin" class="formGroup" :class="isSubmit && !validation.message_contents.required ? 'hasErr' : ''">
          <label class="formGroup-label">
            本文<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <EditorPlugin v-model="message_contents" :disabled="checkRole" />
          <div v-if="isSubmit && !validation.message_contents.required" class="invalid-feedback">
            <span>{{ validateMessages.message_contents.required }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="!checkRole" class="formMessage-btn">
    <button :disabled="loading" type="button" class="btn btn-outline-primary btn1 btn-outline-primary--cancel" data-dismiss="modal" @click="confirmCancel">
      キャンセル
    </button>
    <button
      v-if="modal === 'updateMessage'"
      :disabled="loading"
      type="button"
      class="btn btn-outline-primary btn2 btn-outline-primary--cancel"
      @click="confirmEvent"
    >
      削除
    </button>
    <button :disabled="checkDisableBtn() || loading" class="btn btn-primary btn2 customBtn" type="button" @click="saveMess">
      <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
    </button>
  </div>
  <div v-if="checkRole" class="formMessage-btn">
    <button type="button" class="btn btn-outline-primary btn1 btn-outline-primary--cancel" data-dismiss="modal" @click="confirmCancel">キャンセル</button>
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
        @onFinishScreen="onResultModal"
        @handleConfirm="deleteMessage"
        @handleYes="handleYes"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
import _ from 'lodash';
import EditorPlugin from '@/components/EditorPlugin';
import { markRaw } from 'vue';
import Z02_S02_MessageServices from '../../../api/Z02/Z02_S02_MessageServices';
import Z05S01ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/Z02/Z02_S02_Message';
import moment from 'moment';
export default {
  name: 'ModalMessage',
  components: {
    EditorPlugin,
    Z05S01ModalOrganization
  },
  props: {
    modal: {
      type: String,
      required: true
    },
    dataRaw: [Object]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      loading: false,
      validateMessages,
      important_flag: false,
      messageSubject: '',
      release_start_date: '',
      release_end_date: '',
      org_name: '',
      org_label: '',
      sender_name: '',
      message_contents: '',
      showModalZ05S01: false,
      loadingEditorPlugin: false,
      checkRole: false,
      dataTheFirst: {
        important_flag: 0,
        message_subject: '',
        release_start_date: null,
        release_end_date: null,
        org_name: '',
        sender_name: '',
        message_contents: ''
      },
      modalConfig: {
        title: '',
        customClass: 'custom-dialog z05-s01',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      },
      props: {
        userFlag: 0,
        mode: 'single',
        orgCdList: []
      },
      data: null
    };
  },
  computed: {
    validation() {
      return {
        message_contents: { required: required(this.message_contents) },
        messageSubject: { required: required(this.messageSubject) },
        release_start_date: { required: required(this.release_start_date) },
        release_end_date: { required: required(this.release_end_date) },
        sender_name: { required: required(this.sender_name) }
      };
    }
  },
  mounted() {
    if (this.$props && this.$props.dataRaw)
      this.$props.dataRaw.then((res) => {
        this.data = res;
        let currentUser = this.$store.state.auth.currentUser.user_cd;
        const policyRole = this.$store.state.auth.policyRole;

        policyRole.forEach((element) => {
          if (element === 'R90') {
            this.checkRole = false;
          } else if (element !== 'R90' && this.modal === 'createMessage') {
            this.checkRole = false;
          } else if ((this.modal === 'updateMessage' && currentUser === this.data?.data?.data[0]?.create_user_cd) || element === 'R90') {
            this.checkRole = false;
          } else {
            this.checkRole = true;
          }
        });
        if (this.data?.data?.data[0]?.message_id) {
          this.dataTheFirst = {
            important_flag: this.data?.data?.data[0]?.important_flag,
            message_subject: this.data?.data?.data[0]?.message_subject,
            release_start_date: this.formatFullDate(this.data?.data?.data[0]?.release_start_date),
            release_end_date: this.formatFullDate(this.data?.data?.data[0]?.release_end_date),
            org_name: this.data?.data?.data[0]?.release_org_cd ? this.data?.data?.data[0]?.release_org_cd : '',
            sender_name: this.data?.data?.data[0]?.sender_name,
            message_contents: this.data?.data?.data[0]?.message_contents
          };
          this.messageSubject = this.data?.data?.data[0]?.message_subject;
          this.important_flag = this.data.data.data[0].important_flag === 1 ? true : false;
          this.sender_name = this.data.data.data[0].sender_name;
          this.release_start_date = this.data.data.data[0].release_start_date;
          this.release_end_date = this.data.data.data[0].release_end_date;
          this.org_name = this.data.data.data[0].org_name;
          this.org_label = this.data.data.data[0].org_label;
          this.message_contents = this.data?.data?.data[0]?.message_contents;
          this.release_org_cd = this.data.data.data[0].release_org_cd;
          this.showModalZ05S01 = false;
          this.props.orgCdList = this.data.data.data[0]?.release_org_cd ? [this.data.data.data[0]?.release_org_cd] : [];
        }
      });
  },
  methods: {
    validateMessageSubject() {
      let messageSubject = this.messageSubject;
      if (messageSubject.length > 30) {
        return true;
      }
      return false;
    },
    validateSenderName() {
      let senderName = this.sender_name;
      if (senderName.length > 50) {
        return true;
      }
      return false;
    },
    checkDisableBtn() {
      if (this.validateMessageSubject() || this.validateSenderName()) {
        return true;
      }
      return false;
    },
    handleRemoveUser() {
      if (!this.checkRole) {
        this.org_cd = '';
        this.org_name = '';
        this.org_label = '';
        this.release_org_cd = '';
      }
    },
    changeInput() {
      this.messageSubject = '';
      this.important_flag = false;
      this.release_start_date = '';
      this.release_end_date = '';
      this.org_cd = '';
      this.org_name = '';
      this.org_label = '';
      this.sender_name = '';
      this.message_contents = '';
      this.release_org_cd = '';
      this.showModalZ05S01 = false;
      this.props.orgCdList = [];
    },
    saveMess() {
      if (this.release_start_date === null) {
        this.release_start_date = '';
      }
      if (this.release_end_date === null) {
        this.release_end_date = '';
      }
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-Z02S02',
          idNodeNotify: 'msfa-notify-Z02S02'
        });
        return;
      }
      if (this.modal === 'createMessage') {
        this.loading = true;
        const data = {
          important_flag: this.important_flag ? 1 : 0,
          message_subject: this.messageSubject,
          release_start_date: moment(this.release_start_date).format('YYYY/M/D'),
          release_end_date: moment(this.release_end_date).format('YYYY/M/D'),
          release_org_cd: this.release_org_cd ? this.release_org_cd : '',
          sender_name: this.sender_name,
          message_contents: this.message_contents
        };
        Z02_S02_MessageServices.createMessageService(data)
          .then((res) => {
            if (res) {
              this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
              this.$emit('onFinishScreen', 2);
              this.loading = false;
            }
          })
          .catch((err) => {
            this.loading = false;
            this.notifyModal({
              message: err.response.data.message,
              type: 'error',
              classParent: 'modal-body-Z02S02',
              idNodeNotify: 'msfa-notify-Z02S02'
            });
          });
      }
      if (this.modal === 'updateMessage') {
        this.loading = true;
        const data = {
          important_flag: this.important_flag ? 1 : 0,
          message_subject: this.messageSubject,
          release_start_date: moment(this.release_start_date).format('YYYY/M/D'),
          release_end_date: moment(this.release_end_date).format('YYYY/M/D'),
          release_org_cd: this.release_org_cd ? this.release_org_cd : '',
          sender_name: this.sender_name,
          message_contents: this.message_contents
        };
        Z02_S02_MessageServices.editMessageService(this.data?.data?.data[0]?.message_id, data)
          .then((res) => {
            if (res) {
              this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
              this.$emit('onFinishScreen', 2);
              this.loading = false;
            }
          })
          .catch((err) => {
            this.loading = false;
            this.notifyModal({
              message: err.response.data.message,
              type: 'error',
              classParent: 'modal-body-Z02S02',
              idNodeNotify: 'msfa-notify-Z02S02'
            });
          })
          .finally(() => {
            this.getClassChangeBRG(this.data?.data?.data[0]?.message_id, true);
          });
      }
    },
    confirmCancel() {
      const data = {
        important_flag: this.important_flag ? 1 : 0,
        message_subject: this.messageSubject,
        release_start_date: this.formatFullDate(this.release_start_date),
        release_end_date: this.formatFullDate(this.release_end_date),
        org_name: this.release_org_cd ? this.release_org_cd : '',
        sender_name: this.sender_name,
        message_contents: this.message_contents
      };
      if (!_.isEqual(this.dataTheFirst, data)) {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          title: null,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false
        };
      } else {
        this.$emit('onFinishScreen', 1);
        this.getClassChangeBRG(this.data?.data?.data[0]?.message_id, true);
      }
    },
    handleYes() {
      this.$emit('onFinishScreen', 1);
      this.getClassChangeBRG(this.data?.data?.data[0]?.message_id, true);
    },
    confirmEvent() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: '選択したメッセージを完全に削除しますか？' },
        isShowClose: false
      };
    },
    deleteMessage() {
      Z02_S02_MessageServices.deleteMessageService(this.data?.data?.data[0]?.message_id)
        .then((res) => {
          if (res) {
            this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
            this.$emit('onFinishScreen', 2);
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-Z02S02',
            idNodeNotify: 'msfa-notify-Z02S02'
          });
        });
    },
    openModal_Z05_S01() {
      if (!this.checkRole) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '組織選択',
          isShowModal: true,
          isShowClose: true,
          component: markRaw(Z05S01ModalOrganization),
          width: '45rem',
          customClass: 'custom-dialog modal-fixed',
          props: {
            mode: this.props.mode,
            userFlag: this.props.userFlag,
            orgCdList: this.props.orgCdList
          }
        };
      }
    },
    disabledDateStart(time) {
      if (this.release_end_date) {
        return time.getTime() > new Date(this.release_end_date).getTime();
      }
    },

    disabledDateEnd(time) {
      if (this.release_start_date) {
        return time.getTime() < new Date(this.release_start_date).getTime();
      }
    },

    onResultModal(e) {
      if (e === null) {
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      } else {
        this.org_name = e.orgSelected[0].org_name;
        this.org_label = e.orgSelected[0].org_label;
        this.release_org_cd = e.orgSelected[0].org_cd;
        this.props.orgCdList = [e.orgSelected[0].org_cd];
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.required-start {
  line-height: 18px;
  min-width: 9px;
  margin: 0 0 0 8px;
}
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 30%;
    top: 29%;
  }
}
.el-icon-loading {
  display: none;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.activeInput {
  border: 1px solid #28a470 !important;
  .custom-checkbox {
    span {
      color: #28a470;
    }
  }
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
.formGroup-check-custom-box {
  align-items: center;
}
.formGroup-check-custom {
  border: 1px solid #727f88;
  border-radius: 4px;
  height: 35px;
  margin-right: 12px;
  padding: 5px;
  padding-top: 5px !important;
}
.custom-form {
  width: calc(100% - 74px) !important;
}
.modal-body {
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  border-radius: unset !important;
  padding-bottom: 0;
}
.custom-feedback {
  padding: 0 79px !important;
}
.custom-modal {
  max-width: 1250px;
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
}

.invalid-feedback-date {
  display: flex;
  align-items: center;
  justify-content: space-around;
  color: #dc3545;
  font-size: 12px;
  .feedback-start-date {
    width: 100%;
    margin-left: 4px;
  }
  .feedback-end-date {
    width: 100%;
    margin-left: 25px;
  }
}
@media only screen and (max-width: 1024px) and (min-width: 768px) {
  .formMessage-btn {
    background: #f9f9f9;
    display: flex;
    justify-content: center;
    .btn1 {
      padding: 0 25px !important;
    }
    .btn2 {
      padding: 0 45px !important;
    }
    .btn3 {
      padding: 0 45px !important;
    }
  }
  .custom-feedback {
    padding: 0 !important;
  }
  .formGroup-date {
    display: block !important;
  }
  .invalid-feedback {
    font-size: 12px;
  }
  .formGroup-date-item {
    display: none;
  }
  .reponsive {
    margin-top: 25px;
  }
  .div.tox-tinymce {
    height: 443px !important;
  }
}
.formMessage-btn {
  background: #f9f9f9;
  display: flex;
  justify-content: center;
  .btn1 {
    padding: 0 47px;
    margin-right: 12px;
    margin-bottom: 32px;
  }
  .btn2 {
    padding: 0 69px;
    margin: 0 12px;
  }
  .btn3 {
    padding: 0 69px;
    margin-left: 12px;
  }
}
.formMessage {
  display: flex;
  justify-content: space-between;
  .box1 {
    width: 41%;
  }
  .box2 {
    width: 55%;
  }
  .formGroup {
    margin-bottom: 20px;
    .formGroup-label {
      margin-bottom: 10px;
    }
  }
  .formGroup-editor {
    margin-bottom: 8px;
    border-radius: 4px;
    border: 1px solid #e3e3e3;
    padding: 8px 15px;
    background: #fff;
  }
  .formGroup-flex {
    display: flex;
    flex-wrap: wrap;
    .formGroup-check {
      width: 62px;
      padding-top: 8px;
    }
    .formGroup-control {
      width: calc(100% - 62px);
    }
  }
  .formGroup--flex {
    display: flex;
    flex-wrap: wrap;
    margin-left: -2%;
    .formGroup-col-1 {
      width: 66.666666%;
      padding-left: 2%;
    }
    .formGroup-col-2 {
      width: 33.333333%;
      padding-left: 2%;
    }
  }
  .formGroup-date {
    display: flex;
    .formGroup-date-control {
      width: 100%;
      position: relative;
      .iconStartDate {
        position: absolute;
        z-index: 9;
        right: 55%;
        top: 7px;
        border-left: 1px solid;
        padding: 6px 0px 6px 7px;
      }
      .iconEndDate {
        position: absolute;
        z-index: 9;
        right: 0;
        right: 17px;
        top: 7px;
        border-left: 1px solid;
        padding: 6px 0px 6px 7px;
      }
    }
    .formGroup-date-item {
      min-width: 28px;
      text-align: center;
      padding-top: 10px;
    }
  }
}

.messages-error {
  display: flex;
  flex-direction: row;
  align-content: center;
  align-items: center;
  margin-bottom: 10px;

  label {
    flex: 0 0 75px;
    margin-bottom: unset !important;
  }
}
</style>
