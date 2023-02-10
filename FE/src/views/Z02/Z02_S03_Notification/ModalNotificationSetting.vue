<template>
  <!-- <div id="exampleModalCenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog--mw755" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">通知設定</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div> -->
  <div class="modal-body modal-body-Z02S03-Setting">
    <div id="msfa-notify-Z02S03-Setting"></div>
    <div class="notifi-settings">
      <p v-if="!loadingData">チェックした通知種別のみ通知されます。</p>
      <div class="groupSettings">
        <label v-if="!loadingData" class="groupSettings-label">通知種別 </label>
        <div class="groupSettings-checkBox">
          <Preloader v-if="loadingData" style="position: absolute; left: 50%; top: 0" />
          <ul v-if="!loadingData">
            <li v-for="item of listSetting" :key="item.inform_category_cd">
              <label class="custom-control custom-checkbox" :class="item.checked ? 'activeInput' : ''">
                <input
                  :value="item.inform_category_cd"
                  class="custom-control-input"
                  type="checkbox"
                  :checked="item.checked === 1 ? true : false"
                  @change="signalChange"
                />
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">{{ item.inform_category_name }}</span>
              </label>
            </li>
          </ul>
        </div>
      </div>
      <div v-if="!loadingData" class="groupSettings-btn">
        <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="submit()">
          <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
        </button>
      </div>
    </div>
    <!-- Popup Confirm -->
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
          @onFinishScreen="
            reloadAction(() => {
              modalConfig.isShowModal = false;
            }, 'reload')($event)
          "
          @handleYes="handleConfirmYes"
        />
      </template>
    </el-dialog>
  </div>
  <!-- </div>
    </div>
  </div> -->
</template>

<script>
import Z02_S03_NotificationServices from '../../../api/Z02/Z02_S03_NotificationServices';
import _ from 'lodash';
import { markRaw } from 'vue';

export default {
  name: 'ModalNotificationSetting',
  props: {
    modal: {
      type: String,
      required: true
    },
    id: {
      type: Number,
      required: true
    },
    modalsetting: {
      type: Boolean,
      required: true
    }
  },
  emits: ['onFinishScreen', 'loadDataSetting'],
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
        isShowClose: true
      },
      listSetting: [],
      dataInit: [],
      checkIndicator: [],
      loading: false,
      loadingData: false
    };
  },
  mounted() {
    this.getListSetting();
  },

  methods: {
    confirmCancel() {
      let isEqual = _.isEqual(this.dataInit, this.listSetting);
      if (!isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1 },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.$emit('onFinishScreen');
      }
    },
    handleConfirmYes() {
      this.$emit('onFinishScreen');
    },
    signalChange(e) {
      if (!this.checkIndicator.includes(e.target.value)) {
        this.checkIndicator.push(e.target.value);
      } else {
        this.checkIndicator.splice(this.checkIndicator.indexOf(e.target.value), 1);
      }
      this.listSetting.forEach((element) => {
        if (element.inform_category_cd === e.target.value) {
          if (element.checked === 1) {
            element.checked = 0;
          } else {
            element.checked = 1;
          }
        }
      });
    },
    getListSetting() {
      this.loadingData = true;
      Z02_S03_NotificationServices.getInformInstalledService()
        .then((res) => {
          if (res) {
            this.listSetting = res.data.data;
            this.listSetting.forEach((element) => {
              if (element.checked === 0) {
                this.checkIndicator.push(element.inform_category_cd);
              }
            });

            // Clone data
            this.dataInit = JSON.parse(JSON.stringify(this.listSetting));
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-Z02S03-Setting',
            idNodeNotify: 'msfa-notify-Z02S03-Setting'
          });
        })
        .finally(() => (this.loadingData = false));
    },
    closeModal() {
      this.$emit('onFinishScreen');
    },
    submit() {
      this.loading = true;
      const data = {
        inform_category_cd: this.checkIndicator
      };
      Z02_S03_NotificationServices.postInformRegisterService(data)
        .then((res) => {
          if (res) {
            this.$notify({ message: res.data.message, customClass: 'success' });
            // this.$emit('loadDataSetting');
            this.$emit('onFinishScreen');
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-Z02S03-Setting',
            idNodeNotify: 'msfa-notify-Z02S03-Setting'
          });
        })
        .finally(() => (this.loading = false));
    }
  }
};
</script>

<style lang="scss" scoped>
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 30%;
    top: 30%;
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
.custom-checkbox {
  border: 1px solid #727f88;
  padding: 7px 12px 7px 31px;
  border-radius: 5px;
  width: 100%;
  .control-indicator,
  .custom-control-indicator {
    margin-left: 7px;
    .custom-control-description {
      color: #5f6b73;
    }
  }
}
.activeInput {
  border: 1px solid #28a470 !important;
  .custom-control-description {
    color: #28a470 !important;
  }
}
.notifi-settings .groupSettings {
  margin-top: 12px;
}
.notifi-settings .groupSettings .groupSettings-checkBox {
  margin-top: -2px;
}
.notifi-settings .groupSettings .groupSettings-checkBox ul {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  margin-left: -10px;
}
.notifi-settings .groupSettings .groupSettings-checkBox ul li {
  width: 25%;
  padding-left: 10px;
  margin-top: 8px;
}
.notifi-settings .groupSettings-btn {
  padding-top: 48px;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
}
.notifi-settings .groupSettings-btn .btn {
  width: 180px;
  margin-right: 24px;
}
.notifi-settings .groupSettings-btn .btn:last-child {
  margin-right: 0;
}
</style>
