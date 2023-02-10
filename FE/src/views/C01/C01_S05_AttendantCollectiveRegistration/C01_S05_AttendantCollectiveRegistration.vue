<template>
  <!-- title: 出席者一括登録 -->
  <!-- width: 30%, 35rem -->
  <!-- props: convention_id -->
  <div class="AttendantCollectiveRegistration modal-body-C01S05">
    <div id="msfa-notify-C01S05"></div>
    <div class="attendant-upload">
      <label class="label-upload">ファイル選択</label>
      <div style="display: block">
        <div class="upload-btn-left">
          <button
            :disabled="processingUpload || isLoadingBtn || processingEX"
            type="button"
            class="btn btn-outline-primary btn-outline-primary--cancel"
            @click="handleChooseFile"
          >
            <span class="icon"><ImageSVG :src-image="'icon_upload.svg'" :alt-image="'icon_upload'" /></span>
            <span>ファイル選択</span>
          </button>
          <input id="upload-file" ref="fileUploadRef" style="display: none" type="file" accept="text/csv" @change="validFile()" />
          <span class="file-name" @click="download">{{ file_name }}<span v-if="!file_name" style="color: #2d3033">ファイル未選択</span></span>
        </div>
        <div class="upload-btn-right">
          <el-button
            v-if="!processingUpload || processingEX"
            type="primary"
            class="btn btn-outline-primary btn-outline-primary--cancel"
            :disabled="!file || isLoadingBtn"
            @click="importFile"
          >
            <span class="icon" style="margin-right: 5px"><ImageSVG :src-image="'icon_upload.svg'" :alt-image="'icon_upload'" /></span>
            <span>一括登録</span>
          </el-button>
          <el-button v-else type="primary" class="btn btn-outline-primary btn-outline-primary--cancel" loading disabled>
            <span>一括登録</span>
          </el-button>
        </div>
      </div>
    </div>
    <div class="attendant-message">
      <h1>処理結果</h1>
      <p v-if="total" class="message-total">
        <span>{{ total }}件中、</span>
        <span>{{ successNumber }}件を正常に取り込みました。</span>
      </p>
      <p v-if="total" class="message-fail">
        <span>{{ errorNumber }}件はエラーのため取り込めませんでした。</span>
        <span class="d-block">エラーの内容をご確認ください。</span>
      </p>
    </div>
    <div class="attendant-btn">
      <button
        type="button"
        :disabled="processingEX || processingUpload || isLoadingBtn"
        class="btn btn-outline-primary btn-outline-primary--cancel"
        @click="cancelBtn"
      >
        キャンセル
      </button>
      <el-button type="primary" class="btn btn-primary" :loading="processingEX || isLoadingBtn" :disabled="!errorNumber" @click="exportLog">
        <span class="icon"> エラー内容出力</span>
      </el-button>
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closePopup" @handleYes="closeModal" />
      </template>
    </el-dialog>
  </div>
</template>
<script>
import C01_S05_Service from '@/api/C01/C01_S05_AttendantCollectiveRegistrationServices';
import { encodeData } from '@/api/base64_api';
import axios from 'axios';
import { getCookie } from '@/utils/constants';
export default {
  name: 'C01_S05_AttendantCollectiveRegistration',
  props: {
    convention_id: {
      type: Number,
      default: 1,
      required: true
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      },
      isLoadingBtn: false,
      file: null,
      file_name: '',
      errorNumber: 0,
      successNumber: 0,
      total: 0,
      processingUpload: false,
      processingEX: false,
      json_file_name: '',
      checkDownload: false
    };
  },
  methods: {
    importFile() {
      if (!this.validFile()) return;
      this.processingUpload = true;
      let formData = new FormData();
      formData.append('csv_file', this.file);
      formData.append('convention_id', encodeData(this.convention_id));
      this.importCsv(formData);
    },
    exportLog() {
      if (!this.validFile()) return;
      this.processingEX = true;
      let formData = new FormData();
      formData.append('csv_file', this.file);
      formData.append('convention_id', encodeData(this.convention_id));
      formData.append('json_file_name', encodeData(this.json_file_name));
      // this.exportCsv(formData);

      let access_token = getCookie('accessToken');
      let originalToken = getCookie('originalToken');

      let config = {};
      if (access_token) {
        config = {
          Authorization: `Bearer ${access_token}`,
          'Content-Type': 'multipart/form-data'
        };
      }

      if (originalToken) {
        config = {
          OriginalToken: `Bearer ${originalToken}`,
          'Content-Type': 'multipart/form-data'
        };
      }
      axios({
        // eslint-disable-next-line no-undef
        url: `${process.env.VUE_APP_API_URL}/api/attendant-bulk-registration-error-content-output/export-csv`,
        headers: config,
        method: 'POST',
        responseType: 'blob',
        data: formData
      })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', `エラー内容_${this.$moment().format('YYYYMMDD')}.csv`);
          document.body.appendChild(link);
          link.click();
          this.processingEX = false;
          this.checkDownload = false;
        })
        .catch(() => {
          this.$notify({ message: '内部エラーが発生しました。システム管理者に連絡してください。', customClass: 'error' });
        })
        .finally(async () => {
          this.processingEX = false;
        });
    },
    validFile() {
      const element = document.getElementById('upload-file');
      const file = element.files[0];

      if (!file) return false;
      const isSize = file.size / 1024 / 1024 < 100;
      const isType = file.type === 'text/csv' || file.type === 'application/vnd.ms-excel';

      if (!isType) {
        this.notifyModal({
          message: this.getMessage('MSFA0038', '「.csv」'),
          type: 'error',
          classParent: 'modal-body-C01S05',
          idNodeNotify: 'msfa-notify-C01S05'
        });
        return false;
      }

      if (!isSize) {
        this.notifyModal({
          message: this.getMessage('MFSA0020', '100MB'),
          type: 'error',
          classParent: 'modal-body-C01S05',
          idNodeNotify: 'msfa-notify-C01S05'
        });
        return false;
      }

      if (isType && isSize) {
        this.file = file;
        this.file_name = file.name;
      }

      return isType && isSize;
    },
    handleChooseFile() {
      this.$refs.fileUploadRef.click();
      this.errorNumber = 0;
      this.successNumber = 0;
      this.total = 0;
      this.json_file_name = '';
    },
    cancelBtn() {
      if (!this.checkDownload) {
        this.closeModal();
      } else if (this.errorNumber) {
        this.openModal();
      } else {
        this.closeModal();
      }
    },
    download() {
      this.downloadFile(this.file, this.file_name, 'text/csv;charset=utf-8;');
    },
    // call api
    importCsv(params) {
      C01_S05_Service.importCsvService(params)
        .then((res) => {
          this.notifyModal({
            message: res.data.message || this.getMessage('MSFA0048'),
            type: 'success',
            classParent: 'modal-body-C01S05',
            idNodeNotify: 'msfa-notify-C01S05'
          });
          this.checkDownload = true;
          const dataRes = res.data.data;
          this.errorNumber = dataRes.errorNumber;
          this.successNumber = dataRes.successNumber;
          this.json_file_name = dataRes.json_file_name;
          this.total = dataRes.total;
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-C01S05',
            idNodeNotify: 'msfa-notify-C01S05'
          });
        })
        .finally(() => {
          this.processingUpload = false;
        });
    },
    exportCsv(params) {
      if (!this.errorNumber) return;
      this.isLoadingBtn = true;
      C01_S05_Service.exportCsvService(params)
        .then((res) => {
          this.isLoadingBtn = false;
          this.closeModal();
          const filename = `エラー内容_${this.$moment().format('YYYYMMDD')}.csv`;
          const typefile = 'text/csv;charset=utf-8;';
          this.downloadFile(res.data, filename, typefile);
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-C01S05',
            idNodeNotify: 'msfa-notify-C01S05'
          });
        })
        .finally();
    },
    // modal
    closeModal() {
      this.$emit('onFinishScreen', { screen: 'C01_S05' });
    },
    closePopup() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    openModal() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        component: this.markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          mode: 1,
          textCancel: 'いいえ',
          message: 'エラー内容を出力せずに画面を閉じますか？'
        }
      };
    }
  }
};
</script>
<style lang="scss" scoped>
.AttendantCollectiveRegistration {
  .attendant-upload {
    margin-right: -32px;
    .label-upload {
      font-size: 14px;
      font-weight: 400;
      color: #5f6b73;
      line-height: 20px;
      margin-bottom: 6px;
    }
    .upload-btn-right,
    .upload-btn-left {
      display: flex;
      align-items: baseline;
      button {
        height: 32px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .file-name {
        cursor: pointer;
        margin-left: 8px;
        font-size: 14px;
        color: #448add;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        flex: 1;
        margin-right: 25px;
      }
    }
    .upload-btn-right {
      display: flex;
      justify-content: left;
      margin-right: 32px;
      margin-top: 12px;
    }
  }
  .attendant-message {
    margin-top: 12px;
    border-radius: 20px 0px 0px 20px;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    padding: 12px 0 38px 20px;
    height: 164px;
    margin-right: -32px;
    h1 {
      color: #2d3033;
      font-weight: bold;
      font-size: 18px;
      margin-bottom: 22px;
    }
    .message-total {
      color: #5f6b73;
      font-size: 14px;
      line-height: 20px;
    }
    .message-fail {
      margin-top: 10px;
      color: #ea5d54;
      font-size: 14px;
      line-height: 20px;
    }
  }
  .attendant-btn {
    margin-top: 20px;
    margin-left: -32px;
    text-align: center;
    button {
      width: 180px;
      height: 40px;
      &:first-of-type {
        margin-right: 24px;
      }
    }
  }
}
</style>
