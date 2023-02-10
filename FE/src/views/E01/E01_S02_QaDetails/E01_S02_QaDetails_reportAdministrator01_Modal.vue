<template>
  <div class="modal-body modal-report modal-body-E01S02-Report1">
    <div id="msfa-notify-E01S02-Report1"></div>
    <div class="lstReport">
      <div class="lstReport-wrap">
        <div class="lstReport-admin">
          <div class="lstReport-thumb" @click="goAccountSetting(object.create_user_cd)" @touchstart="goAccountSetting(object.create_user_cd)">
            <img :src="object.file_url" alt="" />
          </div>
          <div class="lstReport-info">
            <h3 class="lstReport-tlt">{{ object.user_name }}</h3>
            <ul>
              <li>
                <span class="item">
                  <img src="@/assets/template/images/icon_building.svg" alt="" />
                </span>
                {{ object.org_label ? object.org_label : '(所属なし)' }}
              </li>
              <li>
                <span class="item">
                  <img src="@/assets/template/images/icon_clock-line.svg" alt="" />
                </span>
                {{ getTimeInterval(object.last_update_datetime) }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="lstReport-content">
        <p style="white-space: break-spaces" class="lstReport-text">
          {{ object.answer_note }}
        </p>
      </div>
    </div>
    <div class="report-control">
      <el-input
        v-model="comment"
        clearable
        class="form-control-textarea"
        :rows="6"
        type="textarea"
        placeholder="報告入力"
        @input="changeInput"
        @change="validateReport"
      />
    </div>
    <div class="invalid-feedback">
      <span v-if="validateReport()" class="invalid">{{ getMessage('MSFA0012', '問題報告', 100) }}</span>
    </div>
    <div class="report-btn">
      <button :disabled="isLoading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
      <button
        v-if="isModal === 'edit'"
        :disabled="isLoading"
        type="button"
        class="btn btn-outline-primary btn-outline-primary--cancel"
        @click="deletePostContent"
      >
        削除
      </button>
      <button :disabled="validateReport() || disableBtn || isLoading" type="button" class="btn btn-primary customBtn" @click="createORupdate">
        <i :class="['el-icon-loading', isLoading ? 'inline-block' : '']"></i>保存
      </button>
    </div>
    <!-- delete question -->
    <el-dialog
      v-model="modalConfigDelete.isShowModal"
      :custom-class="modalConfigDelete.customClass"
      :title="modalConfigDelete.title"
      :width="modalConfigDelete.width"
      :destroy-on-close="modalConfigDelete.destroyOnClose"
      :close-on-click-modal="modalConfigDelete.closeOnClickMark"
      :show-close="modalConfigDelete.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfigDelete.component"
          v-bind="{ ...modalConfigDelete.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="deleteQuestion"
          @handleYes="handleYes"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import E01_S02_QaDetailsServices from '../../../api/E01/E01_S02_QaDetailsServices';
import { markRaw } from 'vue';
export default {
  name: 'E01_S02_QaDetails_reportAdministrator01_Modal',

  props: {
    object: {
      type: Object,
      default() {}
    },
    isModal: {
      type: String,
      default: ''
    }
  },
  emits: ['onFinishScreen01', 'onFinishScreen03'],
  data() {
    return {
      comment: '',
      dataTheFirt: '',
      disableBtn: true,
      isLoading: false,
      modalConfigDelete: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      }
    };
  },
  mounted() {
    this.dataTheFirt = this.object.comment ? this.object.comment : '';
    this.comment = this.object.comment ? this.object.comment : '';
  },
  methods: {
    confirmCancel() {
      if (this.dataTheFirt === this.comment) {
        this.$emit('onFinishScreen01', 0);
      } else {
        this.modalConfigDelete = {
          ...this.modalConfig,
          isShowModal: true,
          title: null,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false,
          customClass: 'custom-dialog z05-s01 modal-fixed modal-fixed-min'
        };
      }
    },
    validateReport() {
      let comment = this.comment;
      if (comment.length > 100) {
        return true;
      }
      return false;
    },
    handleYes() {
      this.$emit('onFinishScreen01', 0);
    },
    onResultModal() {
      this.modalConfigDelete = { ...this.modalConfigDelete, isShowModal: false };
    },
    changeInput() {
      if (this.comment === '' || this.dataTheFirt === this.comment) {
        this.disableBtn = true;
      } else {
        this.disableBtn = false;
      }
    },
    goAccountSetting(item) {
      this.confirmCancel();
      this.$router.push({
        name: 'Z04_S01_AccountSettings',
        query: {
          user_cd: item
        }
      });
    },
    createORupdate() {
      this.isLoading = true;
      const data = {
        comment: this.comment
      };
      E01_S02_QaDetailsServices.registerAnswer(this.object.answer_id, data)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.$emit('onFinishScreen01', 1);
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-E01S02-Report1',
            idNodeNotify: 'msfa-notify-E01S02-Report1'
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    deletePostContent() {
      this.modalConfigDelete = {
        ...this.modalConfigDelete,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: 'この投稿内容を完全に削除しますか？' },
        isShowClose: false
      };
    },
    deleteQuestion() {
      E01_S02_QaDetailsServices.cancelInformAnswerUser(this.object.answer_id)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.$emit('onFinishScreen01', 1);
          // this.$router.go(-1);
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-E01S02-Report1',
            idNodeNotify: 'msfa-notify-E01S02-Report1'
          });
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
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
@import './style.scss';
</style>
