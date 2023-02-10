<template>
  <div class="modal-body modal-report modal-body-E01S02-Report3">
    <div id="msfa-notify-E01S02-Report3"></div>
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
        <div class="lstReport-btn">
          <button
            type="button"
            :disabled="isLoading || statusProhibited_FE === 1"
            class="btn btn-outline-danger btn-radius btn-outline-danger--cancel"
            @click="ComfirmRegisterBestAnswer"
          >
            <span class="btn-iconLeft-2"> <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_userbanned.svg')" alt="" /> </span
            >投稿禁止にする
          </button>
        </div>
      </div>
      <div class="lstReport-content">
        <p style="white-space: break-spaces" class="lstReport-text">{{ object.answer_note }}</p>
      </div>
    </div>
    <div class="infoReport">
      <h2 class="infoReport-title">問題報告 ({{ answerReports.length }})</h2>
      <div class="infoReport-lst scrollbar">
        <Preloader v-if="isLoadingTable" style="position: absolute; left: 50%; top: 50%" />
        <ul v-if="answerReports.length > 0 && !isLoadingTable">
          <li v-for="item of answerReports" :key="item">
            <div class="infoReport-tlt">
              <span class="item"><img src="@/assets/template/images/icon_chatwarning.svg" alt="" /></span>
              <span style="white-space: break-spaces"> {{ item.comment }} </span>
            </div>
            <ul>
              <li class="infoReport-bold">
                <span class="item"><img src="@/assets/template/images/icon_user-edit.svg" alt="" /></span>
                {{ item.user_name }}
              </li>
              <li>
                <span class="item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                {{ item.org_label ? item.org_label : '(所属なし)' }}
              </li>
            </ul>
          </li>
        </ul>
        <div v-if="answerReports.length === 0 && !isLoadingTable" class="noData noData-2">
          <div class="noData-content">
            <p v-if="!isLoadingTable" class="noData-text">該当するデータがありません。</p>
            <div v-if="!isLoadingTable" class="noData-thumb">
              <img style="height: 120px" src="@/assets/template/images/data/amico.svg" alt="icon" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="report-btn">
      <button :disabled="isLoading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
      <button
        :disabled="isLoading || object.delete_flag === 1 || answerReports.length === 0 || deleteFlag_FE"
        type="button"
        class="btn btn-outline-primary customBtn btn-outline-primary--cancel"
        @click="cancelReportQuestion"
      >
        <i :class="['el-icon-loading', isLoading ? 'inline-block' : '']"></i>通報を解除する
      </button>
      <button :disabled="isLoading || object.delete_flag === 1 || deleteFlag_FE" type="button" class="btn btn-primary" @click="deletePostContent">
        投稿内容を削除する
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
        ></component>
      </template>
    </el-dialog>
    <!-- dang ky best answer -->
    <el-dialog
      v-model="modalConfigComfirmNoPost.isShowModal"
      custom-class="custom-modal-03"
      :title="modalConfigComfirmNoPost.title"
      :width="modalConfigComfirmNoPost.width"
      :destroy-on-close="modalConfigComfirmNoPost.destroyOnClose"
      :close-on-click-modal="modalConfigComfirmNoPost.closeOnClickMark"
      :before-close="closeModalI"
      :show-close="modalConfigComfirmNoPost.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfigComfirmNoPost.component"
          v-bind="{ ...modalConfigComfirmNoPost.props }"
          @registerBestAnswerSubmit="notPost"
          @onFinishScreen="onFinishScreen"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import E01_S02_QaDetailsServices from '../../../api/E01/E01_S02_QaDetailsServices';
import E01S02QaDetailsAnswerConfirmModal from '../E01_S02_QaDetails/E01_S02_QaDetails_AnswerConfirm_Modal.vue';
import { markRaw } from 'vue';
export default {
  name: 'E01_S02_QaDetails_ReportAdministrator03_Modal',
  components: {
    E01S02QaDetailsAnswerConfirmModal
  },
  props: {
    questionId: {
      type: Number,
      default: 0
    },
    object: {
      type: Object,
      default() {}
    }
  },
  emits: ['onFinishScreen03'],
  data() {
    return {
      answerReports: [],
      isLoading: false,
      isLoadingTable: false,
      deleteFlag_FE: 0,
      statusProhibited_FE: 0,
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
      },
      modalConfigComfirmNoPost: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      }
    };
  },
  mounted() {
    this.deleteFlag_FE = 0;
    this.statusProhibited_FE = 0;
    this.listAnswerReport();
  },
  methods: {
    ComfirmRegisterBestAnswer(item) {
      const contentObj = {
        header: this.object.user_name + 'さんを投稿禁止にしますか？',
        content: '投稿禁止にすると' + this.object.user_name + 'さんは質問・回答の投稿が出来なくなります。',
        buttonRight: 'いいえ',
        buttonLeft: 'はい'
      };

      this.modalConfigComfirmNoPost = {
        ...this.modalConfigComfirmNoPost,
        title: null,
        isShowModal: true,
        component: markRaw(E01S02QaDetailsAnswerConfirmModal),
        width: '550px',
        destroyOnClose: true,
        props: { contentObj: contentObj }
      };
      this.answerId = item;
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
    onFinishScreen() {
      this.modalConfigComfirmNoPost = { ...this.modalConfigComfirmNoPost, isShowModal: false };
    },
    notPost() {
      E01_S02_QaDetailsServices.prohibitedPosting(this.object.create_user_cd)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.statusProhibited_FE = 1;
          // this.$emit('onFinishScreen03', 1);
          this.onFinishScreen();
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-E01S02-Report3',
            idNodeNotify: 'msfa-notify-E01S02-Report3'
          });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    listAnswerReport() {
      this.deleteFlag_FE = this.object.delete_flag;
      this.statusProhibited_FE = this.object.status_prohibited_user_this;
      this.isLoadingTable = true;
      E01_S02_QaDetailsServices.listAnswerReport(this.object.answer_id)
        .then((res) => {
          this.answerReports = res.data;
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-E01S02-Report3',
            idNodeNotify: 'msfa-notify-E01S02-Report3'
          });
        })
        .finally(() => {
          this.isLoadingTable = false;
        });
    },
    confirmCancel() {
      if (this.deleteFlag_FE === 1 || this.statusProhibited_FE === 1) {
        this.$emit('onFinishScreen03', 1);
      } else {
        this.$emit('onFinishScreen03', 0);
      }
    },
    cancelReportQuestion() {
      this.isLoading = true;
      E01_S02_QaDetailsServices.cancelInformAnswer(this.object.answer_id)
        .then((res) => {
          this.$emit('onFinishScreen03', 1);
          this.$notify({ message: res.data.message, customClass: 'success' });
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-E01S02-Report3',
            idNodeNotify: 'msfa-notify-E01S02-Report3'
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
      E01_S02_QaDetailsServices.deleteAnswer(this.object.answer_id)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.deleteFlag_FE = 1;
          // this.$emit('onFinishScreen03', 1);
          // this.$router.go(-1);
          this.onResultModal();
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-E01S02-Report3',
            idNodeNotify: 'msfa-notify-E01S02-Report3'
          });
        });
    },
    onResultModal() {
      this.modalConfigDelete = { ...this.modalConfigDelete, isShowModal: false };
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 10%;
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
.noData-2 {
  height: unset !important;
}
.btn-iconLeft-2 {
  margin: 0 6px 0 0;
  position: relative;
  top: -2px;
  height: 24px;
  line-height: 24px;
  overflow: hidden;
}
.btn-outline-danger--cancel {
  .btn-iconLeft-2 {
    color: #ea5d54 !important;
    .svg {
      fill: #ea5d54;
    }
  }
}
.btn.btn-outline-danger:hover {
  background: unset !important;
  color: #ea5d54 !important;
  outline: 2px solid #ea5d5494;
  border: 1px solid #ea5d5494;
  .svg {
    fill: #ea5d54;
  }
}
</style>
