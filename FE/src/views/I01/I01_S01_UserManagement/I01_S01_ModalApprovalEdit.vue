<template>
  <!-- Start  -->
  <div class="modal-body modal-approvalEdit modal-body-I01S01-ApproveEdit">
    <div id="msfa-notify-I01S01-ApproveEdit"></div>
    <div class="userHead">
      <h2 class="tlt">
        <span class="item">
          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user.svg')" alt="" />
        </span>
        {{ userName }}
        <span v-if="definitionLabel">（{{ definitionLabel }}）</span>
      </h2>
      <ul>
        <li>
          <span class="item">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" />
          </span>
          {{ orgLable ? orgLable : '(所属なし)' }}
        </li>
        <li>ユーザコード：{{ userCd }}</li>
      </ul>
    </div>
    <div class="userContent">
      <div class="approvalEdit-tbl scrollbar">
        <table style="height: 440px">
          <tbody>
            <tr style="height: 72px">
              <th style="vertical-align: middle">
                適用期間<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
              </th>
              <td>
                <div class="approvalEdit-date">
                  <div class="approvalEdit-control">
                    <div
                      class="form-icon icon-left form-icon--noBod form-icon--noBod-custom"
                      :class="isSubmit && !validation.startDate.required ? 'hasErr' : ''"
                    >
                      <span class="icon">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                      </span>
                      <el-date-picker
                        v-model="startDate"
                        :disabled-date="setDateInMouted"
                        format="YYYY/M/D"
                        type="date"
                        :editable="false"
                        placeholder="開始日"
                        class="form-control-datePickerLeft"
                        :clearable="false"
                      ></el-date-picker>
                    </div>
                  </div>

                  <div class="approvalEdit-label">
                    <span>～</span>
                    <span>{{ endDate === '9999-12-31' ? '未定' : endDate === '' ? '未定' : formatFullDate(endDate) }}</span>
                  </div>
                </div>
                <div class="invalid-feedback">
                  <span v-if="isSubmit && !validation.startDate.required">{{ validateMessages.startDate.required }}</span>
                </div>
              </td>
            </tr>
            <tr v-for="(item2, index) of dataTable.approval_layer_num" :key="item2">
              <th style="vertical-align: middle">
                <span v-if="dataTable.approval_layer_num.length === 1">
                  承認者<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
                ></span>
                <span v-if="dataTable.approval_layer_num.length > 1">
                  {{ item2.approval_layer_num }}次承認者
                  <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
                ></span>
                <div class="approvalEdit-addition">
                  <button
                    type="button"
                    class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                    @click="openModalZ05S07(index)"
                    @touchstart="openModalZ05S07(index)"
                  >
                    <span class="btn-iconLeft">
                      <i style="font-weight: bold" class="el-icon-plus"></i>
                    </span>
                    <span> 追加</span>
                  </button>
                </div>
              </th>
              <td class="custom-td">
                <div v-for="(item3, index2) of item2.approval_user" :key="item3" class="approvalEdit-lst">
                  <div v-if="item3.delete_flag === 0" class="boxx">
                    <div class="approvalEdit-info">
                      <p class="approvalEdit-name">
                        {{ item3?.user_name }} <span v-if="item3?.definition_label">（{{ item3?.definition_label }}）</span>
                      </p>
                      <p class="approvalEdit-add">
                        <span class="item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" />
                        </span>
                        {{ item3?.org_label ? item3?.org_label : '(所属なし)' }}
                      </p>
                    </div>
                    <button
                      class="btn btn--icon"
                      type="button"
                      @click="deleteRecord(item3.approval_user_cd, index, index2)"
                      @touchstart="deleteRecord(item3.approval_user_cd, index, index2)"
                    >
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                    </button>
                  </div>
                </div>
                <div class="invalid-feedback2">
                  <span :class="item2.isEmtry ? '' : 'none'"> {{ item2.isEmtry }}次承認者を1人以上選択して下さい。</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="userEdit-btn">
        <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <button
          v-if="typeModal === 'updateIsRecord'"
          :disabled="loading"
          type="button"
          class="btn btn-outline-primary btn-outline-primary--cancel"
          @click="deleteTab"
          @touchstart="deleteTab"
        >
          予約削除
        </button>
        <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="checkSubmit">
          <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
        </button>
      </div>
    </div>
  </div>
  <!-- End -->
  <!-- Modal Z05-S07 -->
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
        @onFinishScreen="onResultModalZ05S07"
        @handleYes="handleYes"
        @handleConfirm="deleteMessage"
        @overWrite="overWriteData"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
// import _ from 'lodash';
import { markRaw } from 'vue';
import Z05S07OrganizationMaster from '@/views/Z05/Z05_S07_Organization_Master/Z05_S07_Organization_Master';
import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import moment from 'moment';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/I01/I01_S01_ModalApprovalEdit';
import dialogOverwriteComfirm from '../dialog_overwriteComfirm.vue';
export default {
  name: 'I01_S01_ModalApprovalEdit',
  components: {
    Z05S07OrganizationMaster,
    dialogOverwriteComfirm
  },
  props: {
    typeModal: {
      type: String,
      default: ''
    },
    item: {
      type: Object,
      default() {}
    },
    itemApproval: {
      type: Object,
      default() {}
    },
    wordType: {
      type: Object,
      default() {}
    },
    istartDate: {
      type: String,
      default: ''
    },
    isCurrentDateinArr: {
      type: Boolean,
      default: false
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      loading: false,
      validateMessages,
      userName: '',
      definitionLabel: '',
      orgLable: '',
      userCd: '',
      dataTable: {},
      wordTypeModal: 0,
      startDateOld: '',
      startDate: this.tomorrow(),
      endDate: '',
      props: {
        userFlag: 1,
        mode: 'single',
        orgCdList: [],
        orgCd: '',
        userCdList: [],
        date: ''
      },
      userFlag1: 1,
      indexLayerNum: 0,
      dataTheFirtStartDate: null,
      checkEntrys: [],
      countArr: 0,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true,
        updatedApprovalLayerNum: [],
        dateZ05S07: ''
      },
      approvalLayerNum: [],
      arrDefault: [],
      curentDate: '',
      isOverWriteData: false
    };
  },
  computed: {
    validation() {
      return {
        startDate: { required: required(this.startDate) }
      };
    }
  },
  mounted() {
    this.curentDate = this.formatFullDate(new Date());
    this.getScreenData();
  },
  methods: {
    getScreenData() {
      I01_S01_UserManagementServices.getScreenData().then((res) => {
        this.approvalLayerNum = res.data.data.approval_layer_num;
        this.setMouted();
      });
    },
    setDateInMouted(date) {
      const dates = new Date();
      dates.setTime(dates.getTime() - 3600 * 1000 * 24);
      return dates > date.setDate(date.getDate());
    },
    setMouted() {
      this.userName = this.item.user_name ? this.item.user_name : '';
      this.definitionLabel = this.item.definition_label ? this.item.definition_label : '';
      this.orgLable = this.item.org_label ? this.item.org_label : '';
      this.userCd = this.item.user_cd;
      this.wordTypeModal = this.wordType;
      this.dateZ05S07 = this.item.approval_current[0] ? this.item.approval_current[0].start_date : '';
      if (this.typeModal === 'updateIsRecord') {
        this.dataTable = this.itemApproval;
        this.startDateOld = moment(this.dataTable.start_date).format('YYYY-M-D');
        this.startDate = moment(this.dataTable.start_date).format('YYYY-M-D');
        this.dataTheFirtStartDate = moment(this.dataTable.start_date).format('YYYY-M-D');
        this.endDate = moment(this.dataTable.end_date).format('YYYY-M-D');
        this.dataTable.approval_layer_num.forEach((element) => {
          element.approval_user.forEach((element) => {
            element.delete_flag = 0;
          });
        });
        this.updatedApprovalLayerNum = this.dataTable.approval_layer_num;
        this.approvalLayerNum.forEach((element) => {
          if (element.parameter_key === this.wordType.definition_label) {
            for (let i = this.updatedApprovalLayerNum.length + 1; i <= element.parameter_value; i++) {
              const data2 = {
                approval_layer_num: i,
                approval_user: []
              };
              this.dataTable.approval_layer_num.push(data2);
            }
          }
        });
        setTimeout(() => {
          this.arrDefault = JSON.parse(JSON.stringify(this.dataTable.approval_layer_num));
        }, 500);
      }
      if (this.typeModal === 'createIsRecord') {
        this.dataTheFirtStartDate = this.tomorrow();
        const data = {
          start_date: '',
          end_date: '',
          approval_layer_num: []
        };
        this.dataTable = data;
        this.approvalLayerNum.forEach((element) => {
          if (element.parameter_key === this.wordType.definition_label) {
            for (let i = 1; i <= element.parameter_value; i++) {
              const data2 = {
                approval_layer_num: i,
                approval_user: []
              };
              this.dataTable.approval_layer_num.push(data2);
            }
          }
        });
        setTimeout(() => {
          this.arrDefault = JSON.parse(JSON.stringify(this.dataTable.approval_layer_num));
        }, 500);
      }
    },
    confirmCancel() {
      if (this.countArr === 0 && this.startDate === this.dataTheFirtStartDate) {
        this.$emit('onFinishScreen', 1);
        this.changeFalseClassHeader();
      } else {
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
      }
    },
    handleYes() {
      this.$emit('onFinishScreen', 1);
      this.changeFalseClassHeader();
    },
    deleteTab() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        isShowClose: false,
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: `選択した${this.item.user_name}を完全に削除しますか？` }
      };
    },
    deleteMessage() {
      const data = {
        user_cd: this.item.user_cd,
        approval_work_type: this.wordTypeModal.definition_value,
        start_date: moment(this.itemApproval.start_date).format('YYYY-M-D'),
        end_date: moment(this.itemApproval.end_date).format('YYYY-M-D')
      };
      I01_S01_UserManagementServices.deleteapproval(data)
        .then((res) => {
          if (res) {
            this.$notify({ message: '正常に削除しました', customClass: 'success', duration: 1500 });
            this.$emit('onFinishScreen', 2);
            this.changeTabI01();
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-I01S01-ApproveEdit',
            idNodeNotify: 'msfa-notify-I01S01-ApproveEdit',
            duration: 1500
          });
        });
    },
    openModalZ05S07(index) {
      this.indexLayerNum = index;
      this.props.orgCdList = [];
      this.props.userCdList = [];
      if (this.dataTable.approval_layer_num[this.indexLayerNum].approval_user[0]) {
        this.dataTable.approval_layer_num[this.indexLayerNum].approval_user.forEach((element) => {
          if (element.delete_flag !== 1) {
            this.props.orgCdList.push(element.org_cd);
            this.props.userCdList.push({ org_cd: element.org_cd, user_cd: element.user_cd || element.approval_user_cd });
          }
        });
      }
      if (this.startDate === null) {
        this.startDate = '';
      }
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05S07OrganizationMaster),
        props: {
          userFlag: 1,
          mode: 'singles',
          orgCdList: this.props.orgCdList,
          orgCd: '',
          userCdList: this.props.userCdList,
          userSelectFlag: 1,
          date: moment(this.startDate).format('YYYY/M/D')
        },
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S07(e) {
      if (e) {
        this.props.orgCdList = [];
        this.props.userCdList = [];
        e.userSelected?.forEach((x) => {
          this.props.orgCdList.push(x.org_cd);
        });
        e.userSelected?.forEach((x) => {
          this.props.userCdList.push({
            org_cd: x.org_cd,
            user_cd: x.user_cd
          });
        });

        let dataDefaults = [...this.arrDefault[this.indexLayerNum].approval_user];

        const objResult = e.userSelected;

        objResult.forEach((element) => {
          this.countArr += 1;
          element.approval_user_cd = element.user_cd;
          element.definition_label = element.definition;
          element.delete_flag = 0;
        });

        // if (this.dataTable.approval_layer_num[this.indexLayerNum].approval_user[0]) {
        // this.dataTable.approval_layer_num[this.indexLayerNum].approval_user = [
        //   ...new Set([...this.dataTable.approval_layer_num[this.indexLayerNum].approval_user, ...objResult])
        // ];
        //   this.unique(this.dataTable.approval_layer_num[this.indexLayerNum].approval_user);
        dataDefaults.forEach((element) => {
          if (!objResult.some((x) => x.approval_user_cd === element.approval_user_cd && x.org_cd === element.org_cd)) {
            element.delete_flag = 1;
          }
        });

        objResult.forEach((element) => {
          if (!dataDefaults.some((x) => x.approval_user_cd === element.approval_user_cd && x.org_cd === element.org_cd)) {
            dataDefaults.push(element);
          }
        });
        this.dataTable.approval_layer_num[this.indexLayerNum].approval_user = dataDefaults;
        this.dataTable.approval_layer_num[this.indexLayerNum].isEmtry = '';
      }

      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    unique(arr) {
      const arrUniqueFalseDelete = [];
      const arrUniqueTrueDelete = [];
      var newArr = [];
      arr.forEach((element) => {
        if (element.delete_flag === 0) {
          arrUniqueFalseDelete.push(element);
        } else {
          arrUniqueTrueDelete.push(element);
        }
      });
      for (var i = 0; i < arrUniqueFalseDelete.length; i++) {
        if (!newArr.some((s) => s && s.user_cd === arrUniqueFalseDelete[i].user_cd)) {
          newArr.push(arrUniqueFalseDelete[i]);
        }
      }

      this.dataTable.approval_layer_num[this.indexLayerNum].approval_user = [...new Set([...newArr, ...arrUniqueTrueDelete])];
    },
    deleteRecord(approval_user_cd, index, index2) {
      let record = this.dataTable.approval_layer_num[index].approval_user[index2];
      let dataDefaults = [...this.arrDefault[index].approval_user];

      if (dataDefaults.some((x) => x.approval_user_cd === record.approval_user_cd && x.org_cd === record.org_cd)) {
        this.dataTable.approval_layer_num[index].approval_user[index2].delete_flag = 1;
      } else {
        this.dataTable.approval_layer_num[index].approval_user.splice(index2, 1);
      }

      this.countArr -= 1;
    },
    checkSubmit() {
      let checkEntry = [];
      this.dataTable.approval_layer_num.forEach((element) => {
        let counDeleteFlag = 0;
        element.approval_user.forEach((element) => {
          if (element.delete_flag === 0) {
            counDeleteFlag = counDeleteFlag + 1;
          }
        });
        if (counDeleteFlag === 0) {
          checkEntry.push(element.approval_layer_num);
          element.isEmtry = element.approval_layer_num;
        } else {
          element.isEmtry = '';
        }
      });

      if (checkEntry.length > 0) {
        this.checkEntrys = checkEntry;
        if (!this.checkValidate()) {
          this.notifyModal({
            message: '入力エラーを確認してください。',
            type: 'error',
            classParent: 'modal-body-I01S01-ApproveEdit',
            idNodeNotify: 'msfa-notify-I01S01-ApproveEdit',
            duration: 1500
          });
          return;
        }
      } else {
        this.checkEntrys = [];
        if (!this.checkValidate()) {
          this.notifyModal({
            message: '入力エラーを確認してください。',
            type: 'error',
            classParent: 'modal-body-I01S01-ApproveEdit',
            idNodeNotify: 'msfa-notify-I01S01-ApproveEdit',
            duration: 1500
          });
          return;
        }
        this.submit();
      }
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    overWriteData() {
      this.isOverWriteData = true;
      this.onResultModal();
      this.submit();
    },
    checkOverWriteData() {
      if (this.isCurrentDateinArr && this.curentDate === this.formatFullDate(this.startDate)) {
        this.loading = false;
        this.callDialogOverWriteComfirm({ message: '開始日が同日のものが既に登録されています。上書きしてよろしいですか？' });
      } else {
        this.isOverWriteData = true;
        this.submit();
      }
    },
    callDialogOverWriteComfirm(objProps) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(dialogOverwriteComfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        isShowClose: false,
        props: objProps
      };
    },
    submit() {
      this.loading = true;
      const data = {
        user_cd: this.userCd,
        approval_work_type: this.wordTypeModal.definition_value,
        start_date: moment(this.startDate).format('YYYY-M-D'),
        end_date: this.typeModal === 'createIsRecord' ? '9999-12-31' : moment(this.endDate).format('YYYY-M-D'),
        start_date_old: this.typeModal === 'createIsRecord' ? moment(this.startDate).format('YYYY-M-D') : moment(this.startDateOld).format('YYYY-M-D'),
        approval_layer_num: this.dataTable.approval_layer_num,
        flag_change: this.typeModal === 'updateIsRecord' ? 1 : 0,
        create_flag: this.typeModal === 'updateIsRecord' ? 0 : 1
      };
      if (!this.isOverWriteData) {
        this.checkOverWriteData();
      } else {
        I01_S01_UserManagementServices.updateapproval(data)
          .then((res) => {
            this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
            this.$emit('onFinishScreen', 2);
            this.changeFalseClassHeader();
          })
          .catch((err) => {
            this.notifyModal({
              message: err.response.data.message,
              type: 'error',
              classParent: 'modal-body-I01S01-ApproveEdit',
              idNodeNotify: 'msfa-notify-I01S01-ApproveEdit',
              duration: 1500
            });
          })
          .finally(() => {
            this.loading = false;
            this.isOverWriteData = false;
            this.changeTabI01();
          });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
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
@media only screen and (max-width: 992px) and (min-width: 768px) {
  .tab3 {
    padding-top: 85px !important;
  }
  .userHead {
    display: unset !important;
  }
}
.tab3 {
  padding-top: 30px;
  text-align: center;
}
/* Modal */
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25px;
  height: 20px;
}
.invalid-feedback2 {
  display: block;
  width: 100%;
  margin-top: 0.25px;
  padding-left: 12px;
  position: absolute;
  top: 50%;
  color: #dc3545;
}
.modal-approvalEdit {
  padding: 0;
  position: inherit;
  .userHead {
    position: absolute;
    top: calc(0px - 32px);
    width: calc(100% + 64px);
    right: calc(0px - 32px);
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    padding: 12px 32px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 10px 10px 0 0;
    .tlt {
      font-size: 24px;
      font-weight: 700;
      display: flex;
      position: relative;
      padding-left: 25px;
      .item {
        position: absolute;
        left: 0;
        top: -3px;
      }
    }
    ul {
      display: flex;
      flex-wrap: wrap;
      li {
        margin-right: 36px;
        display: flex;
        &:last-child {
          margin-right: 0;
        }
        .item {
          min-width: 16px;
          margin: -2px 6px 0 0;
        }
      }
    }
  }
  .userContent {
    padding-top: 15px;
  }
  .approvalEdit-tbl {
    background: #fff;
    box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    margin-top: 20px;
    max-height: 440px;
    min-height: 440px;
    tr {
      &:first-of-type {
        th,
        td {
          border-top: none;
        }
      }
      td,
      th {
        border: none;
        padding: 16px;
      }
      td {
        border-top: 1px solid #e3e3e3;
        padding: 16px;
      }
      .custom-td {
        padding: 16px 0 0 0;
        position: relative;
      }
      th {
        background: #41586f;
        font-weight: normal;
        color: #fff;
        width: 1rem;
        min-width: 135px;
        border-top: 1px solid #fff;
        text-align: center;
      }
    }
    .approvalEdit-addition {
      margin-top: 16px;
      display: flex;
      align-items: center;
      justify-content: start;
      padding: 0 0 0 7px;
      .btn {
        height: 32px;
        padding: 0 12px;
        display: flex;
        justify-content: center;
        align-items: center;
      }
    }
    .approvalEdit-date {
      display: flex;
      align-items: center;
      .approvalEdit-control {
        width: 176px;
      }
      .approvalEdit-label {
        span {
          padding-left: 8px;
        }
      }
    }
    .approvalEdit-lst {
      display: flex;
      justify-content: space-between;
      box-shadow: inset 0px -1px 0px #e3e3e3;
      .boxx {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
      }
      &:last-child {
        box-shadow: unset;
      }
      .approvalEdit-info {
        .approvalEdit-name {
          font-weight: 700;
        }
        .approvalEdit-add {
          display: flex;
          color: #99a5ae;
          margin-top: 2px;
          .item {
            min-width: 16px;
            margin: -2px 6px 0 0;
          }
        }
      }
      .btn {
        min-width: 42px;
        margin-left: 10px;
      }
    }
  }
  .userEdit-btn {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}

.modal {
  display: block;
}

.fade:not(.show) {
  opacity: unset;
}
.btn-iconLeft {
  top: 0;
  i {
    font-size: 15px;
  }
}
.none {
  display: none;
}
</style>
