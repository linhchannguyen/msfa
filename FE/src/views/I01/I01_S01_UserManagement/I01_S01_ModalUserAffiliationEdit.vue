<template>
  <!-- Start  -->
  <div class="modal-body modal-userAffiliation modal-body-I01S01-UserAffiliationEdit">
    <div id="msfa-notify-I01S01-UserAffiliationEdit"></div>
    <div class="userHead">
      <h2 class="tlt">{{ userNames }}</h2>
      <ul>
        <li>ユーザコード：{{ userCD }}</li>
      </ul>
    </div>
    <div class="userContent">
      <div class="userMain">
        <label class="userMain-label">
          適用開始日<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
        ></label>
        <div class="userMain-date">
          <div class="form-icon icon-left form-icon--noBod" :class="isSubmit && !validation.date.required ? 'hasErr' : ''">
            <span class="icon">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
            </span>
            <el-date-picker
              v-model="date"
              format="YYYY/M/D"
              :disabled-date="setDateInMouted"
              :clearable="false"
              type="date"
              :editable="false"
              placeholder="日付選択"
              class="form-control-datePickerLeft"
            ></el-date-picker>
            <div class="approvalEdit-label" :class="record.end_date ? '' : 'right-25'">
              <span style="margin-right: 10px">～</span>
              <span>{{ record.end_date ? formatFullDate(record.end_date) : '未定' }}</span>
            </div>
          </div>
          <div class="invalid-feedback">
            <span v-if="isSubmit && !validation.date.required">{{ validateMessages.date.required }}</span>
          </div>
        </div>
      </div>
      <div class="userMain" :class="dataTable ? 'hasErr' : ''">
        <label class="userMain-label">
          所属/役職<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
        ></label>
        <div class="userMain-box">
          <div class="userMain-table scrollbar">
            <table class="tableBox tableCustom">
              <thead>
                <tr>
                  <th>主所属</th>
                  <th>所属組織</th>
                  <th colspan="2">役職</th>
                </tr>
              </thead>
              <tbody>
                <tr class="btnAdd-custom">
                  <td colspan="4" style="border-bottom: none">
                    <div :class="checkBtn ? '' : 'paddingBtn'" class="userAddition">
                      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="addRecord">
                        <span class="btn-iconLeft btn-iconLeft-2"> <i style="font-weight: bold" class="el-icon-plus"></i> </span><span> 行追加</span>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-for="(item, index) of dataAffiliation" :key="item.id">
                  <td v-if="item.delete_flag === 0" class="custom-td">
                    <label class="custom-control custom-checkbox">
                      <input v-model="item.main_flag" class="custom-control-input" type="checkbox" @input="onCheckItem(item, index)" />
                      <span class="custom-control-indicator"></span>
                    </label>
                  </td>
                  <td v-if="item.delete_flag === 0" style="padding-top: 21px">
                    <div class="form-icon icon-right" :class="item.messageOrg ? 'hasErr' : ''">
                      <span
                        title_log="所属組織"
                        class="icon log-icon"
                        @click="openModal_Z05_S07(index, item.org_cd)"
                        @touchstart="openModal_Z05_S07(index, item.org_cd)"
                      >
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="item.org_name" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag class="m-0 el-tag-custom" closable @close="handleRemoveProduct(index)">
                            {{ item.org_name }}
                          </el-tag>
                        </div>
                      </div>
                      <el-input v-else clearable style="background: #ffffff" placeholder="未選択" readonly class="form-control-input" />
                      <!-- <el-input v-model="item.org_label" readonly placeholder="" class="form-control-input" />
                      <i v-if="item.org_label" class="iconClear el-icon-circle-close" @click="handleRemoveProduct(index)"></i> -->
                    </div>
                    <div class="invalid-feedback">
                      <span v-if="item.messageOrg">選択してください。</span>
                    </div>
                  </td>
                  <td v-if="item.delete_flag === 0" :class="item.messagaUserPosstType ? 'hasErr' : ''" style="padding-top: 21px">
                    <el-select v-model="item.user_post_type" placeholder="未選択" class="form-control-select">
                      <el-option label="" value="">未選択 </el-option>
                      <el-option v-for="userPost in userPostTypeList" :key="userPost" :label="userPost.definition_label" :value="userPost.definition_value">
                      </el-option>
                    </el-select>
                    <div class="invalid-feedback">
                      <span v-if="item.messagaUserPosstType">選択してください。</span>
                    </div>
                  </td>
                  <td v-if="item.delete_flag === 0" class="custom-td" style="vertical-align: unset; padding-top: 21px">
                    <button class="btn btn--icon" @click="deleteRecord(index)" @touchstart="deleteRecord(index)">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="invalid-feedback">
        <span v-if="dataTable">選択してください。</span>
      </div>
      <div class="userEdit-btn">
        <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <button v-if="typeModal === 'edit'" :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="clickDelete">
          予約削除
        </button>
        <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="checkSubmit">
          <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
        </button>
      </div>
    </div>
  </div>
  <!-- End -->
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :before-close="onCloseModal"
    :show-close="modalConfig.isShowClose"
  >
    <template #default>
      <component
        :is="modalConfig.component"
        v-bind="{ ...modalConfig.props }"
        @onFinishScreen="onResultModal"
        @handleYes="handleYes"
        @handleConfirm="deleteMessage"
        @overWrite="overWriteData"
      ></component>
    </template>
  </el-dialog>
</template>

<!-- eslint-disable indent -->
<script>
import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import Z05_S07_Organization_Master from '@/views/Z05/Z05_S07_Organization_Master/Z05_S07_Organization_Master';
import { markRaw } from 'vue';
import moment from 'moment';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/I01/I01_S01_ModalUserAffiliationEdit';
import dialogOverwriteComfirm from '../dialog_overwriteComfirm.vue';
import _ from 'lodash';
export default {
  name: 'I01_S01_ModalUserAffiliationEdit',

  components: {
    Z05_S07_Organization_Master,
    dialogOverwriteComfirm
  },
  props: {
    typeModal: {
      type: String,
      default: ''
    },
    record: {
      type: Object,
      default() {}
    },
    userCd: {
      type: String,
      default: ''
    },
    userName: {
      type: String,
      default: ''
    },
    orgCdOld: {
      type: String,
      default: ''
    },
    numberModal: {
      type: Number,
      default: 0
    },
    updateStartDate: {
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
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      indexRecordZ05: 0,
      showModalZ05S01: false,
      userFlag: 0,
      userPostTypeList: [],
      userCD: '',
      userNames: '',
      dataAffiliation: [],
      checkBtn: false,
      date: '',
      date2: '',
      mainFlag: true,
      orgCd: '',
      dataTable: false,
      abc: false,
      dataTheFirtDate: null,
      countData: 0,
      props: {
        userFlag: 0,
        mode: 'single',
        orgCdList: [],
        userCdList: [],
        date: ''
      },
      curentDate: '',
      isOverWriteData: false,
      dataAffiliationDefault: []
    };
  },
  computed: {
    validation() {
      return {
        date: { required: required(this.date) }
      };
    }
  },
  mounted() {
    this.curentDate = this.formatFullDate(new Date());
    this.js_pscroll();
    this.getScreenData();
    this.dataTheFirtDate = this.typeModal === 'edit' ? this.record.start_date : this.tomorrow();
    if (this.typeModal === 'edit') {
      this.userCD = this.userCd;
      this.userNames = this.userName;
      this.date = this.record.start_date;
      this.checkBtn = true;
      this.setDatadataAffiliation();
    } else {
      this.userCD = this.userCd;
      this.userNames = this.userName;
      this.date = this.tomorrow();
      this.dataAffiliation = [];
      this.checkBtn = false;
      this.countData = 0;
    }
  },
  methods: {
    setDateInMouted(date) {
      const dates = new Date();
      dates.setTime(dates.getTime() - 3600 * 1000 * 24);
      return dates > date.setDate(date.getDate());
    },
    setDatadataAffiliation() {
      const tempData = this.record?.organization;
      console.log('tempData', tempData);
      tempData.forEach((element) => {
        if (element.main_flag === 1 || element.main_flag === true) {
          element.main_flag = true;
          element.delete_flag = 0;
          element.org_cd_old = element.org_cd;
        } else {
          element.main_flag = false;
          element.delete_flag = 0;
          element.org_cd_old = element.org_cd;
        }
      });
      this.dataAffiliation = tempData;
      console.log('dfdfdfdf', this.dataAffiliation);
      this.dataAffiliation = this.dataAffiliation.map((x) => {
        return {
          ...x,
          id: this.generateID()
        };
      });
      this.countData = this.dataAffiliation.length;
      this.dataAffiliationDefault = JSON.parse(JSON.stringify(this.dataAffiliation));
    },
    getScreenData() {
      I01_S01_UserManagementServices.getScreenData().then((res) => {
        this.userPostTypeList = res.data.data.user_post_type;
      });
    },
    onCheckItem(item, index) {
      this.dataAffiliation.forEach((element) => {
        element.main_flag = false;
        this.dataAffiliation[index].main_flag = true;
      });
    },
    checkSubmit() {
      let countDeleteFlag = 0;
      let countMainFlag = 0;
      let countOrgLabel = 0;
      let countUserPostType = 0;
      this.dataAffiliation.forEach((element) => {
        if (element.delete_flag) {
          countDeleteFlag = countDeleteFlag + 1;
        }
        if (element.delete_flag === 0) {
          if (element.main_flag) {
            element.main_flag ? countMainFlag++ : element;
          }
          if (element.org_cd === '') {
            element.messageOrg = true;
            countOrgLabel = countOrgLabel + 1;
          } else {
            element.messageOrg = false;
            countOrgLabel === 0 ? countOrgLabel : countOrgLabel - 1;
          }
          if (element.user_post_type === '') {
            element.messagaUserPosstType = true;
            countUserPostType = countUserPostType + 1;
          } else {
            element.messagaUserPosstType = false;
            countUserPostType === 0 ? countUserPostType : countUserPostType - 1;
          }
        }
      });
      if (countDeleteFlag === this.dataAffiliation.length || this.dataAffiliation.length === 0) {
        this.dataTable = true;
        if (!this.checkValidate()) {
          this.notifyModal({
            message: '入力エラーを確認してください。',
            type: 'error',
            classParent: 'modal-body-I01S01-UserAffiliationEdit',
            idNodeNotify: 'msfa-notify-I01S01-UserAffiliationEdit',
            duration: 1500
          });
          return;
        } else {
          this.notifyModal({
            message: '入力エラーを確認してください。',
            type: 'error',
            classParent: 'modal-body-I01S01-UserAffiliationEdit',
            idNodeNotify: 'msfa-notify-I01S01-UserAffiliationEdit',
            duration: 1500
          });
          return;
        }
      } else {
        if (!this.checkValidate()) {
          this.notifyModal({
            message: '入力エラーを確認してください。',
            type: 'error',
            classParent: 'modal-body-I01S01-UserAffiliationEdit',
            idNodeNotify: 'msfa-notify-I01S01-UserAffiliationEdit',
            duration: 1500
          });
          return;
        }
        if (countMainFlag < 1) {
          this.notifyModal({
            message: '主所属を選択してください。',
            type: 'error',
            classParent: 'modal-body-I01S01-UserAffiliationEdit',
            idNodeNotify: 'msfa-notify-I01S01-UserAffiliationEdit',
            duration: 1500
          });
          return;
        } else {
          if (countOrgLabel === 0 && countUserPostType === 0) {
            const arrNew = [];
            this.dataAffiliation.forEach((element) => {
              if (element.isNew) {
                if (element.delete_flag === 0 && element.org_cd !== '' && element.user_post_type !== '') {
                  arrNew.push(element);
                }
              } else {
                if (element.delete_flag === 0 || element.delete_flag === 1) {
                  arrNew.push(element);
                }
              }
            });
            this.dataAffiliation = arrNew;
            this.submit();
          }
        }
      }
    },
    overWriteData() {
      this.isOverWriteData = true;
      this.onResultModal();
      this.submit();
    },
    checkOverWriteData() {
      if (this.isCurrentDateinArr && this.curentDate === this.formatFullDate(this.date)) {
        this.loading = false;
        this.callDialogOverWriteComfirm({ message: '開始日が同日のものが既に登録されています。上書きしてよろしいですか？', textConfirm: 'はい' });
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
        start_date: this.typeModal === 'create' ? moment(this.date).format('YYYY-M-D') : moment(this.date).format('YYYY-M-D'),
        start_date_old: this.typeModal === 'create' ? moment(this.date).format('YYYY-M-D') : moment(this.record.start_date).format('YYYY-M-D'),
        end_date: this.typeModal === 'create' ? '9999-12-31' : this.record.end_date === '' ? '9999-12-31' : moment(this.record.end_date).format('YYYY-M-D'),
        organization: this.dataAffiliation,
        flag_change: this.numberModal,
        create_flag: this.typeModal === 'create' ? 1 : 0
      };
      console.log('data', data);
      if (!this.isOverWriteData) {
        this.checkOverWriteData();
      } else {
        I01_S01_UserManagementServices.updateOrganization(data)
          .then((res) => {
            if (res) {
              this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
              this.$emit('onFinishScreen', 2);
              this.changeFalseClassHeader();
            }
          })
          .catch((err) => {
            this.notifyModal({
              message: err.response.data.message,
              type: 'error',
              classParent: 'modal-body-I01S01-UserAffiliationEdit',
              idNodeNotify: 'msfa-notify-I01S01-UserAffiliationEdit',
              duration: 1500
            });
          })
          .finally(() => {
            this.loading = false;
            this.isOverWriteData = false;
            this.changeTabI01();
          });
      }
    },
    addRecord() {
      this.dataTable = false;
      this.dataAffiliation = [
        { definition_label: '', main_flag: false, org_cd: '', org_cd_old: '', org_label: '', org_name: '', user_post_type: '', delete_flag: 0, isNew: true },
        ...this.dataAffiliation
      ];
      this.checkBtn = true;
    },

    deleteRecord(index) {
      let record = this.dataAffiliation[index];

      if (record.id) {
        let oldRecord = this.dataAffiliationDefault.find((x) => x.id === record.id);
        oldRecord.delete_flag = 1;
        this.dataAffiliation[index] = { ...oldRecord };
      } else {
        this.dataAffiliation.splice(index, 1);
      }

      let countDeleteFlag = 0;
      this.dataAffiliation.forEach((element) => {
        if (element.delete_flag) {
          countDeleteFlag = countDeleteFlag + 1;
        } else {
          countDeleteFlag = countDeleteFlag - 1;
        }
      });
      if (countDeleteFlag === this.dataAffiliation.length) {
        this.checkBtn = false;
      } else {
        this.checkBtn = true;
      }
    },
    checkDate() {
      if (this.date === null || this.date === '') {
        this.date = '';
      } else {
        this.date = moment(this.date).format('YYYY/MM/DD');
      }
      if (this.date) {
        if (moment(this.date).format('YYYY/MM/DD') !== this.dataTheFirtDate) {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    },
    confirmCancel() {
      let countDeleteFlag = 0;
      this.dataAffiliation.forEach((element) => {
        if (element.delete_flag === 1) {
          countDeleteFlag = countDeleteFlag + 1;
          // this.dataAffiliation.splice(element, 1);
        }
      });
      if (
        countDeleteFlag > 0 ||
        this.countData !== this.dataAffiliation.length ||
        this.checkDate() ||
        !_.isEqual(this.dataAffiliation, this.dataAffiliationDefault)
      ) {
        this.modalConfig = {
          ...this.modalConfig,
          title: null,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false
        };
      } else {
        this.$emit('onFinishScreen', 1);
        this.changeFalseClassHeader();
      }
    },
    handleYes() {
      this.$emit('onFinishScreen', 1);
      this.changeFalseClassHeader();
    },
    clickDelete() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        isShowClose: false,
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: `選択した${this.userNames}を完全に削除しますか？` }
      };
    },
    deleteMessage() {
      const data = {
        user_cd: this.userCd,
        start_date: moment(this.record.start_date).format('YYYY-M-D'),
        end_date: this.record.end_date === '' ? '9999-12-31' : moment(this.record.end_date).format('YYYY-M-D')
      };
      I01_S01_UserManagementServices.deleteOrganization(data)
        .then((res) => {
          if (res) {
            this.$notify({ message: '正常に削除しました。', customClass: 'success', duration: 1500 });
            this.$emit('onFinishScreen', 3, true);
            this.changeTabI01();
          }
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-I01S01-UserAffiliationEdit',
            idNodeNotify: 'msfa-notify-I01S01-UserAffiliationEdit',
            duration: 1500
          });
        });
    },
    openModal_Z05_S07(index, org_cd) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択（マスタ管理用)',
        isShowModal: true,
        component: markRaw(Z05_S07_Organization_Master),
        width: '45rem',
        customClass: 'custom-dialog modal-fixed ',
        props: {
          mode: this.props.mode,
          userFlag: this.userFlag1,
          orgCdList: this.props.orgCdList.length > 0 ? this.props.orgCdList : org_cd ? [org_cd] : [],
          orgCd: this.props.orgCd,
          userCdList: this.props.userCdList,
          date: moment(this.date).format('YYYY/M/D')
          // this.typeModal === 'create'
          //   ? this.record?.active[0]
          //     ? this.record?.active[0]?.start_date
          //     : moment(new Date()).format('YYYY/M/D')
          //   : this.updateStartDate !== ''
          //   ? this.updateStartDate
          //   : moment(new Date()).format('YYYY/M/D')
        }
      };
      this.indexRecordZ05 = index;
    },
    onResultModal(e) {
      if (e) {
        this.props.orgCdList = [];
        this.props.userCdList = [];
        e.orgSelected?.forEach((x) => {
          this.props.orgCdList.push(x.org_cd);
        });
        e.userSelected?.forEach((x) => {
          this.props.userCdList.push({
            org_cd: x.org_cd,
            user_cd: x.user_cd
          });
        });
        console.log(e);
        this.dataAffiliation[this.indexRecordZ05].org_cd = e.orgSelected[0].org_cd;
        this.dataAffiliation[this.indexRecordZ05].org_label = e.orgSelected[0].org_label;
        this.dataAffiliation[this.indexRecordZ05].org_name = e.orgSelected[0].org_name;
        this.onCloseModal();
      } else {
        this.onCloseModal();
      }
    },
    handleRemoveProduct(index) {
      this.dataAffiliation[index].org_cd = '';
      this.dataAffiliation[index].org_label = '';
      this.dataAffiliation[index].org_name = '';
    },
    //close z05-s01
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.approvalEdit-label {
  position: absolute;
  top: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  right: -107px;
  justify-content: end;
}
.right-25 {
  right: -60px;
}
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
.btnAdd-custom {
  position: sticky;
  top: 37px;
  z-index: 3;
  background: #fff;
  &:hover {
    box-shadow: none;
  }
  td {
    border-top: none;
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
/* Modal */
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
}
.iconClear {
  cursor: pointer;
  position: absolute;
  right: 35px;
  top: 33%;
  width: 20px;
}
.custom-td {
  width: 7%;
}
.modal-userAffiliation {
  padding: 0;
  position: inherit;
  .userHead {
    position: absolute;
    top: calc(0px - 62px);
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
    }
  }
  .userContent {
    margin-top: 30px;
  }
  .userMain {
    padding-top: 20px;
    .userMain-label {
      font-size: 1rem;
      margin-bottom: 8px;
    }
    .userMain-date {
      max-width: 230px;
    }
  }
  .userEdit-btn {
    margin-top: 28px;
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
  .userMain-box {
    min-height: 294px;
    box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    background: #fff;
    .userAddition {
      display: flex;
      justify-content: center;
      .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 200px;
        height: 32px;
        padding: 0 12px;
      }
    }
  }
  .userMain-table {
    padding-bottom: 15px;
    max-height: 294px;
    border-radius: 8px 8px 0 0;
    thead {
      height: 37px;
      th,
      td {
        vertical-align: middle;
        &:nth-child(1) {
          border-bottom: none;
        }
      }
    }
    tr {
      th,
      td {
        vertical-align: middle;
        &:nth-child(1) {
          text-align: center;
          width: 15%;
        }
        &:nth-child(2) {
          width: 50%;
        }
        &:nth-child(3) {
          width: 1rem;
          min-width: 200px;
        }
      }
      td {
        .custom-control {
          padding-left: 19px;
        }
      }
    }
  }
}
.form-control-select {
  width: 100% !important;
  display: inline-block !important;
}
.btn-iconLeft-2 {
  margin-right: 5px;
  top: 0;
}
</style>
