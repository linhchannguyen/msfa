<template>
  <!-- Start  -->
  <div class="modal-body modal-roleEditing-single modal-body-I01S02-EditSingle">
    <div id="msfa-notify-I01S02-EditSingle"></div>
    <div class="roleEditingHead">
      <h2 class="tlt">{{ userName || userSingle || userSingle2 }}</h2>
      <div class="box-wrp">
        <ul class="tblSetting-add">
          <li class="custom-li-1">
            <span v-if="organization[0]?.org_label" class="item">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" />
            </span>
            {{ organization[0]?.org_label }}
          </li>
          <li class="custom-li-2">
            <span v-if="organization[0]?.definition_label" class="item">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_namecard.svg')" alt="" />
            </span>
            {{ organization[0]?.definition_label }}
          </li>
        </ul>
        <span v-if="organization.length - 1 !== 0 && organization.length - 1 > 0" class="box-toopil1">
          <span v-if="organization.length - 1 !== 0 && organization.length - 1 > 0">
            他所属：
            <span :class="organization.length - 1 < 10 ? 'number-i01-s02' : 'number-i01-s022'" class="number-i01-s02">{{ organization.length - 1 }}</span></span
          >
          <div v-if="organization.length - 1 !== 0 && organization.length - 1 > 0" class="box-toopil">
            <ul v-for="(item, index) of organization" :key="item" class="tblSetting-add-custom">
              <li :class="organization.length - organization.length === index ? 'hiden' : ''" class="custom1">
                <span class="item"> <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" /> </span
                >&nbsp;
                {{ item?.org_label }}
              </li>
              <li :class="organization.length - organization.length === index ? 'hiden' : ''" class="custom1">
                <span class="item">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_namecard.svg')" alt="" />
                </span>
                &nbsp;
                {{ item?.definition_label }}
              </li>
            </ul>
          </div>
        </span>
      </div>
    </div>
    <div class="roleEditing-content">
      <div class="roleEditing-dateTime">
        <span class="roleEditing-label">適用期間</span>
        <div class="roleEditing-control">
          <div class="form-icon icon-left form-icon--noBod" :class="isSubmit && !validation.startDate.required ? 'hasErr' : ''">
            <span class="icon">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
            </span>
            <el-date-picker
              v-model="startDate"
              :disabled-date="setDateInMouted"
              type="date"
              :editable="false"
              placeholder="日付選択"
              class="form-control-datePickerLeft"
              format="YYYY/M/D"
              :clearable="false"
              @change="changeStartDate"
            ></el-date-picker>
          </div>
        </div>
        &ensp;<span v-if="endDate === '9999-12-31'">{{ JapaneseTilde() }}</span
        >&ensp;
        <span>{{ endDate ? (endDate === '9999-12-31' ? '' : formatFullDate(endDate)) : '' }}</span>
      </div>

      <div class="invalid-feedback">
        <span v-if="isSubmit && !validation.startDate.required">{{ validateMessages.startDate.required }}</span>
      </div>
      <div class="roleEditing-tbl">
        <table v-loading="isLodaing">
          <thead>
            <tr>
              <th v-for="item of listScreenData" :key="item">
                <span class="roleEditing-head">{{ item.role_name }}</span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td v-for="item of listScreenData" :key="item" class="custom-td">
                <label class="custom-control custom-checkbox">
                  <input class="custom-control-input" type="checkbox" :checked="item.isCheck" @input="onCheck(item.role)" />
                  <span class="custom-control-indicator"></span>
                </label>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="roleEditing-btn">
        <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="update">
          <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
        </button>
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
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @handleYes="handleYes"
          @onFinishScreen="onResultModal"
          @overWrite="overWriteData"
        ></component>
      </template>
    </el-dialog>
  </div>
  <!-- End -->
</template>

<script>
import I01_S02_PermissionSetting from '../../../api/I01/I01_S02_PermissionSetting';
import moment from 'moment';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/I01/I01_S02_PermissionSetting';
import { markRaw } from 'vue';
import dialogOverwriteComfirm from '../dialog_overwriteComfirm.vue';
export default {
  name: 'I01_S01_ModalUserEdit',
  components: {
    dialogOverwriteComfirm
  },
  props: {
    typeModal: {
      type: String,
      default: ''
    },
    data: {
      type: Object,
      default() {}
    },
    userRole: {
      type: Object,
      default() {}
    },
    index3: {
      type: Number,
      default: 0
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
      isLodaing: false,
      listScreenData: [],
      dataCheck: [],
      user_cd: '',
      startDate: '',
      endDate: '',
      organization: [],
      arrRole: [],
      change_flag: 0,
      checkChange: 0,
      userRoles: [],
      oldLenghtUserRole: 0,
      startDateOld: this.tomorrow(),
      userName: '',
      userSingle: '',
      checkDataChange: [],
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
    setDateInMouted(date) {
      const dates = new Date();
      dates.setTime(dates.getTime() - 3600 * 1000 * 24);
      return dates > date.setDate(date.getDate());
    },
    getScreenData() {
      this.isLodaing = true;
      I01_S02_PermissionSetting.getScreen().then((res) => {
        this.listScreenData = res.data.data.role;
        if (this.userRole) {
          this.listScreenData.map((item) => {
            this.userRole.role.map((item2) => {
              if (item.role === item2.role) {
                item.isCheck = true;
              }
            });
          });
        }

        this.setScreen();
      });
    },
    setScreen() {
      if (this.data.length > 1) {
        this.data.forEach((element) => {
          if (element.isCheck) {
            this.organization = element.organization;
            this.userSingle = element.user_name;
          }
        });
      } else {
        if (Array.isArray(this.data)) {
          this.organization = this.data[0].organization;
        } else {
          this.organization = this.data.organization;
          this.userSingle2 = this.data.user_name;
        }
      }
      if (this.typeModal === 'create') {
        // this.dataCheck = this.userRole.role;
        if (this.userRole) {
          this.userRole.role.forEach((element) => {
            this.dataCheck.push(element.role);
          });
        } else {
          this.dataCheck = [];
        }

        this.startDate = this.tomorrow();
        this.endDate = '';
        this.userRoles = this.data.user_role;
      } else {
        this.dataCheck = this.userRole.role;
        this.startDate = this.formatFullDate(this.userRole.start_date);
        this.endDate = this.userRole.end_date;
        this.startDateOld = this.data.user_role[this.index3].start_date;
        this.data.user_role.forEach((element) => {
          element.change_flag = 0;
          element.roles;
          const data = [];
          element.role.forEach((element2) => {
            data.push(element2.role);
            element.roles = data;
          });
          this.oldLenghtUserRole = data.length;
        });
        this.userRoles = this.data.user_role;
        this.userRoles[this.index3].change_flag = 1;
      }
      this.user_cd = this.data.user_cd;
      this.userName = this.data.user_name;
      this.isLodaing = false;
    },
    overWriteData() {
      this.isOverWriteData = true;
      this.onResultModal();
      this.update();
    },
    checkOverWriteData() {
      if (this.isCurrentDateinArr && this.curentDate === this.formatFullDate(this.startDate)) {
        this.loading = false;
        this.callDialogOverWriteComfirm({ message: '開始日が同日のものが既に登録されています。上書きしてよろしいですか？' });
      } else {
        this.isOverWriteData = true;
        this.update();
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
    onCheck(item) {
      if (!this.checkDataChange.includes(item)) {
        this.checkDataChange.push(item);
      } else {
        this.checkDataChange.splice(this.checkDataChange.indexOf(item), 1);
      }
      if (this.typeModal === 'create') {
        if (this.dataCheck.includes(item)) {
          this.dataCheck.splice(this.dataCheck.indexOf(item), 1);
        } else {
          this.dataCheck.push(item);
        }
      } else {
        if (this.userRoles[this.index3].roles.includes(item)) {
          this.userRoles[this.index3].roles.splice(this.userRoles[this.index3].roles.indexOf(item), 1);
        } else {
          this.userRoles[this.index3].roles.push(item);
        }
        if (this.oldLenghtUserRole === this.userRoles[this.index3].roles.length) {
          // this.userRoles[this.index3].change_flag = 0;
        } else {
          // this.userRoles[this.index3].change_flag = 1;
        }
      }
    },
    update() {
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-I01S02-EditSingle',
          idNodeNotify: 'msfa-notify-I01S02-EditSingle',
          duration: 1500
        });
        return;
      }
      if (this.startDate === null) {
        this.startDate = '';
      }
      if (this.typeModal === 'update') {
        // Check show popup override
        const startDate = moment(this.startDate).format('YYYY-M-D');
        const currentDate = moment().format('YYYY-M-D');
        if (
          !this.isOverWriteData &&
          moment(this.startDateOld).format('YYYY-M-D') !== currentDate &&
          startDate === currentDate &&
          this.data.user_role.some((ele) => moment(ele.start_date).format('YYYY-M-D') === startDate)
        ) {
          this.callDialogOverWriteComfirm({ message: '開始日が同日のものが既に登録されています。上書きしてよろしいですか？' });
          return;
        }

        this.userRoles[this.index3].start_date = startDate;
        this.data.user_role.forEach((element) => {
          element.roles = element.roles.toString();
          if (element.change_flag === 1) {
            element.start_date_old = this.startDateOld;
          }
        });
        this.loading = true;
        const data = {
          user_cd: this.user_cd,
          user_role: this.data.user_role
        };
        I01_S02_PermissionSetting.update(data)
          .then((res) => {
            if (res) {
              this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
              this.$emit('onFinishScreen', 2, this.formatFullDate3(this.startDate));
            }
          })
          .catch((err) => {
            this.data.user_role.forEach((element) => {
              if (element.change_flag === 1) {
                element.start_date = this.startDateOld;
              }
            });
            this.notifyModal({
              message: err.response.data.message,
              type: 'error',
              classParent: 'modal-body-I01S02-EditSingle',
              idNodeNotify: 'msfa-notify-I01S02-EditSingle',
              duration: 1500
            });
          })
          .finally(() => {
            this.loading = false;
          });
      } else {
        if (this.data[0]) {
          this.data.forEach((element) => {
            if (element.isCheck === true) {
              this.user_cd = element.user_cd;
            }
          });
        } else {
          this.data.user_role.forEach((element) => {
            if (element.isCheck === true) {
              this.user_cd = element.user_cd;
            }
          });
        }
        this.loading = true;
        const data = {
          create_flag: 1,
          user_cd: this.user_cd,
          user_role: [
            {
              start_date: this.startDate === '' ? this.startDate : moment(this.startDate).format('YYYY-M-D'),
              end_date: this.endDate === '' ? '9999-12-31' : moment(this.endDate).format('YYYY-M-D'),
              roles: this.dataCheck.toString(),
              change_flag: 1
            }
          ]
        };
        if (!this.isOverWriteData) {
          this.checkOverWriteData();
        } else {
          I01_S02_PermissionSetting.update(data)
            .then((res) => {
              if (res) {
                this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
                this.$emit('onFinishScreen', 2, this.formatFullDate3(this.startDate));
              }
            })
            .catch((err) => {
              this.notifyModal({
                message: err.response.data.message,
                type: 'error',
                classParent: 'modal-body-I01S02-EditSingle',
                idNodeNotify: 'msfa-notify-I01S02-EditSingle',
                duration: 1500
              });
            })
            .finally(() => {
              this.loading = false;
              this.isOverWriteData = false;
            });
        }
      }
    },
    confirmCancel() {
      if (this.checkDataChange.length === 0 && this.formatFullDate2(this.startDate) === (this.startDateOld ? this.formatFullDate2(this.startDateOld) : null)) {
        this.$emit('onFinishScreen', 1);
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
    },

    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.custom-li-1 {
  padding: 0 15px 0 0;
}
.custom-li-2 {
  padding: 0 30px 0 0px;
}
.hiden {
  display: none !important;
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
.box-toopil1 {
  right: 10px;
  top: 21px;
  width: 80px;
  &:hover {
    .box-toopil {
      display: block;
    }
  }
}
.number-i01-s02 {
  cursor: default;
  position: absolute;
  right: 28px;
  width: 23px;
  height: 23px;
  background: #448add;
  border-radius: 50%;
  color: white;
  padding: 0px 0px 21px 7px;
  &:hover {
    .box-toopil {
      display: block;
    }
  }
}
.box-wrp {
  height: 40px;
  padding: 9px 0 0 0;
  display: flex;
  justify-content: end;
}
.box-toopil {
  display: none;
  position: absolute;
  background: antiquewhite;
  padding: 15px 30px;
  background: #d1e4ff;
  border-radius: 4px;
  right: 12px;
  top: 57px;
  max-height: 195px;
  overflow: auto;
  &::before {
    content: '';
    width: 20px;
    background: #d1e4ff;
    height: 20px;
    position: absolute;
    right: 18px;
    top: -14px;
    clip-path: polygon(50% 0%, 0% 71%, 125% 100%);
  }
  .tblSetting-add-custom {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 6px;
    li {
      font-size: 14px;
      display: flex;
      &:last-child {
        margin-right: 0;
      }
      .tblSetting-item {
        min-width: 13px;
      }
    }
  }
}
.number-i01-s022 {
  padding: 0px 0px 21px 3px !important;
}

.tblSetting-add {
  display: flex;
  flex-wrap: wrap;
  li {
    margin-right: 12px;
    display: flex;
    &:last-child {
      margin-right: 0;
    }
    .tblSetting-item {
      min-width: 13px;
      margin: -1px 5px 0 0;
    }
  }
}
.custom {
  width: 150px;
}
.custom1 {
  width: 145px;
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
  span {
    margin-left: 76px;
  }
}
.modal-roleEditing-single {
  .roleEditingHead {
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    padding: 12px 32px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 10px 10px 0 0;
    position: absolute;
    top: -31px;
    width: calc(100% + 64px);
    left: -32px;
    .tlt {
      font-size: 24px;
      font-weight: 700;
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
  .roleEditing-content {
    padding: 55px 0 0 0;
  }

  .roleEditing-dateTime {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    .roleEditing-label {
      padding-right: 20px;
      font-size: 1rem;
    }
    .roleEditing-control {
      width: 184px;
    }
  }
  .roleEditing-btn {
    margin-top: 20px;
    text-align: center;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
  .roleEditing-tbl {
    margin-top: 20px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
    tr {
      th {
        color: #fff;
        border-right: 1px solid #fff;
        padding: 8px 4px;
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        &:first-of-type {
          border-radius: 8px 0 0 0;
        }
        &:last-child {
          border-right: none;
          border-radius: 0 8px 0 0;
        }
        .roleEditing-head {
          width: 16px;
          margin: 0 auto;
          display: block;
          white-space: normal;
        }
      }
      td {
        padding: 18px 4px;
        text-align: center;
        .custom-control {
          padding-left: 19px;
        }
      }
    }
  }
}
</style>
