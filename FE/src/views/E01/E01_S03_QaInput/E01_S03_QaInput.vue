<template>
  <div class="modal-body modal-questionRegist modal-body-E01S03">
    <div id="msfa-notify-E01S03"></div>
    <div class="formQuestion">
      <ul>
        <li>
          <label class="formQuestion-label">
            カテゴリ
            <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
          </label>
          <div class="formQuestion-control" :class="isSubmit && !validation.question_category_cd.required ? 'hasErr' : ''">
            <el-select v-model="qa.question_category_cd" placeholder="未選択" class="form-control-select" name="qa" :disabled="processingRegister">
              <el-option v-for="item in questionCategorys" :key="item.qa_category_cd" :label="item.qa_category_name" :value="item.qa_category_cd"> </el-option>
            </el-select>
          </div>
          <span v-if="isSubmit && !validation.question_category_cd.required" class="text-error">{{ getMessage('MSFA0040', 'カテゴリ') }}</span>
        </li>
        <li>
          <label class="formQuestion-label">
            タイトル
            <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
          ></label>
          <div class="formQuestion-control" :class="(isSubmit && !validation.title.required) || (isSubmit && !validation.title.length) ? 'hasErr' : ''">
            <el-input v-model="qa.title" clearable placeholder="質問のタイトルを入力" class="form-control-input" name="qa" :disabled="processingRegister" />
          </div>
          <span v-if="isSubmit && !validation.title.required" class="text-error">{{ getMessage('MSFA0001', 'タイトル') }}</span>
          <span v-if="isSubmit && !validation.title.length" class="text-error">{{ getMessage('MSFA0012', 'タイトル', 30) }}</span>
        </li>
        <li class="w-100">
          <div class="formQuestion-control" :class="isSubmit && !validation.contents.required ? 'hasErr' : ''">
            <el-input
              v-model="qa.contents"
              clearable
              class="form-control-textarea"
              :rows="6"
              type="textarea"
              name="qa"
              placeholder="質問内容を入力"
              :disabled="processingRegister"
            />
          </div>
          <span v-if="isSubmit && !validation.contents.required" class="text-error">{{ getMessage('MSFA0001', '質問内容') }}</span>
        </li>
        <li class="w-100">
          <label class="formQuestion-label"> 通知先 </label>
          <div class="formQuestion-control">
            <div class="form-icon icon-right" :class="processingRegister ? 'is-disable' : ''">
              <span title_log="通知先" class="icon log-icon" @click="openModalZ05S01">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
              </span>
              <div v-if="lstOrgPlan.length > 0" class="form-control d-flex align-items-center">
                <div class="block-tags">
                  <el-tag
                    v-for="org in lstOrgPlan"
                    :key="org.org_cd"
                    class="el-tag-custom el-tag-icon-top"
                    name="qa"
                    closable
                    @close="handleRemoveOrgPlan(org)"
                  >
                    {{ org.org_label || org.org_name }}
                  </el-tag>
                </div>
              </div>
              <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" :disabled="processingRegister" />
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="formQuestion-btn">
      <button
        type="button"
        :disabled="isDisableSubmit || processingRegister"
        :class="['btn btn-outline-primary btn-outline-primary--cancel', isDisableSubmit ? 'event-canncel' : '']"
        @click="confirmCancel()"
      >
        キャンセル
      </button>
      <el-button type="primary" class="btn btn-primary" :loading="processingRegister" :disabled="processingRegister" @click.prevent="createQa">
        登録
      </el-button>
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
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModalZ05S01" @handleYes="onClose"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import { required, validLength } from '@/utils/validate';
import E01S03_QaInputService from '@/api/E01/E01_S03_QaInputServices';

export default {
  props: {
    question_id: {
      type: String,
      default: null,
      required: false
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      isDisableSubmit: false,
      isSubmit: false,
      processingRegister: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        updatedApprovalLayerNum: [],
        dateZ05S07: '',
        isShowClose: false
      },
      paramsZ05S01: {
        userFlag: 0,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: []
      },
      lstOrgPlan: [],
      questionCategorys: [],
      qa: {
        question_category_cd: '',
        title: '',
        contents: '',
        org_cd: ''
      }
    };
  },
  computed: {
    validation() {
      return {
        question_category_cd: { required: required(this.qa.question_category_cd) },
        title: { required: required(this.qa.title), length: validLength(this.qa.title, 30) },
        contents: { required: required(this.qa.contents) }
      };
    }
  },
  watch: {
    question_id() {
      this.getScreenData();
    }
  },
  mounted() {
    this.getScreenData();
  },
  methods: {
    /** API */
    getScreenData() {
      E01S03_QaInputService.getDataScreen()
        .then((res) => {
          const data = res.data.data;
          this.questionCategorys = data.question_category;
        })
        .catch((err) => {
          this.notifyModal({ message: err?.response?.data?.message, type: 'error', classParent: 'modal-body-E01S03', idNodeNotify: 'msfa-notify-E01S03' });
        });
    },
    createQa() {
      this.processingRegister = true;
      const btnClose = document.querySelectorAll('.el-dialog__headerbtn')[1];
      btnClose.style.display = 'none';
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-E01S03',
          idNodeNotify: 'msfa-notify-E01S03'
        });
        this.processingRegister = false;
        btnClose.style.display = 'block';
        return;
      }
      let body = {
        ...this.qa,
        org_cd: this.lstOrgPlan.map((s) => s.org_cd)
      };
      const question_id = this.question_id;
      body = question_id ? { ...body, question_id } : body;
      this.isDisableSubmit = true;
      E01S03_QaInputService.createQa(body)
        .then((res) => {
          const message = res.data.message;
          this.$notify({ message: message, customClass: 'success' });
          this.$emit('onFinishScreen', 1);
          this.isDisableSubmit = false;
        })
        .catch((err) => {
          this.notifyModal({ message: err?.response?.data?.message, type: 'error', classParent: 'modal-body-E01S03', idNodeNotify: 'msfa-notify-E01S03' });
          this.isDisableSubmit = false;
        })
        .finally(async () => {
          this.processingRegister = false;
          btnClose.style.display = 'block';
        });
    },
    confirmCancel() {
      const check = Object.values({ ...this.qa, org_cd: this.lstOrgPlan.map((s) => s.org_cd) })
        .map((s) => s.length > 0)
        .includes(true);
      if (check) {
        this.modalConfirmRoute(() => {
          this.$emit('onFinishScreen');
        });
      } else {
        this.$emit('onFinishScreen');
      }
    },
    handleRemoveOrgPlan(org) {
      if (this.processingRegister) return;
      const index = this.lstOrgPlan.findIndex((s) => s.org_cd === org.org_cd);
      this.lstOrgPlan.splice(index, 1);
    },
    openModalZ05S01() {
      if (this.processingRegister) return;
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: {
          ...this.paramsZ05S01,
          mode: 'multiple',
          orgCdList: this.lstOrgPlan.map((x) => x.org_cd)
        },
        width: '45rem',
        customClass: 'custom-dialog modal-fixed',
        destroyOnClose: true,
        isShowClose: true
      };
    },
    onResultModalZ05S01(e) {
      if (e && e.orgSelected) this.lstOrgPlan = [...e.orgSelected];
      this.onCloseModal();
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    modalConfirmRoute(callBack) {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: true,
        isShowClose: false,
        title: '',
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          mode: 1,
          params: { callBack }
        }
      };
    },

    onClose(e) {
      e.callBack();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.modal-questionRegist {
  .formQuestion {
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -12px;
      li {
        padding-left: 12px;
        margin-bottom: 20px;
        &:nth-child(1) {
          width: 200px;
        }
        &:nth-child(2) {
          width: calc(100% - 200px);
        }
        .formQuestion-label {
          font-size: 0.875rem;
          margin-bottom: 6px;
        }
      }
    }
  }
  .formQuestion-btn {
    text-align: center;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.el-icon-loading {
  margin-right: 45px;
  display: none;
}
.btn-primary {
  position: relative;
  .textSubmit {
    margin-left: 5px;
    top: 0;
    right: 40%;
    justify-content: center;
    display: flex;
    height: 100%;
    position: absolute;
    align-items: center;
  }
}
.event-canncel {
  pointer-events: none;
  background: #ffffff;
  color: #dfdedec7;
  border-color: #dfdedec7;
}
.event-none-submit {
  pointer-events: none;
  background: #90c3ff;
  color: #fff9f9c7;
  border-color: #90c3ff;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
</style>
<style lang="scss">
.is-disable {
  div,
  span {
    background-color: var(--el-disabled-fill-base);
    border-color: var(--el-disabled-border-base);
    color: var(--el-disabled-color-base);
    cursor: not-allowed;
  }
}
</style>
