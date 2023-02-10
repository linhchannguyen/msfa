<template>
  <!-- Start  -->
  <div class="modal-body modal-roleEditing-multi modal-body-I01S02-EditMulti">
    <div id="msfa-notify-I01S02-EditMulti"></div>
    <div class="roleEditing-flex">
      <div class="roleEditing-info">
        <div class="roleEditing-box">
          <ul class="scrollbar">
            <li v-for="(item, index) of dataMulti" :key="item" style="position: relative">
              <span v-if="item.isCheckErr" class="roleEditing-name-err"></span>
              <div class="roleEditing-name-box">
                <div>
                  <p class="roleEditing-name">{{ item.user_name }}</p>
                  <p class="roleEditing-add">
                    <span class="roleEditing-item">
                      <img class="svg" src="@/assets/template/images/icon_building.svg" alt="" />
                    </span>
                    {{ item.organization[0] ? item.organization[0].org_label : '(所属なし)' }}
                  </p>
                  <p class="roleEditing-add">
                    <span v-if="item.organization[0]" class="roleEditing-item">
                      <img class="svg" src="@/assets/template/images/icon_namecard.svg" alt="" />
                    </span>
                    {{ item.organization[0] ? item.organization[0].definition_label : '' }}
                  </p>
                </div>
                <div v-if="item.organization.length - 1 !== 0 && item.organization.length - 1 > 0" class="number-i01-s02">
                  他所属
                  <el-popover v-model="item.visible" :popper-class="'popover-customI01-S02'" placement="right" :width="340" trigger="hover">
                    <div v-if="item.visible" class="tootip-custom">
                      <div v-for="(item2, index2) of item.organization" :key="item2" class="tblSetting-add-custom">
                        <div :class="item.organization.length - item.organization.length === index2 ? 'hiden' : ''" class="custom1">
                          <span class="item">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" /> </span
                          >&nbsp;
                          {{ item2?.org_label }}
                        </div>
                        <div :class="item.organization.length - item.organization.length === index2 ? 'hiden' : ''" class="custom1 custom1-1">
                          <span class="item">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_namecard.svg')" alt="" />
                          </span>
                          &nbsp;
                          {{ item2?.definition_label }}
                        </div>
                      </div>
                    </div>
                    <template #reference>
                      <span class="span-1" @mouseenter="checkMouseEnter(index)" @mouseleave="checkMouseLeave(index)">{{ item.organization.length - 1 }}</span>
                    </template>
                  </el-popover>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div v-if="checkMessageInvalid" class="invalid-feedback invalid-feedback-1">
          <span>権限が付与されないユーザ（●印）があります。</span>
        </div>
      </div>
      <div class="roleEditing-content">
        <div class="roleEditing-dateTime">
          <span class="roleEditing-label">適用開始日</span>
          <div class="roleEditing-control">
            <div class="form-icon icon-left form-icon--noBod" :class="isSubmit && !validation.startDate.required ? 'hasErr' : ''">
              <span class="icon">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
              </span>
              <el-date-picker
                v-model="startDate"
                :disabled-date="setDateInMouted"
                format="YYYY/M/D"
                type="date"
                :editable="false"
                placeholder="日付選択"
                class="form-control-datePickerLeft"
                :clearable="false"
              ></el-date-picker>
            </div>
          </div>
        </div>
        <div class="invalid-feedback">
          <span v-if="isSubmit && !validation.startDate.required">{{ validateMessages.startDate.required }}</span>
        </div>
        <div class="roleEditing-tbl">
          <table>
            <thead>
              <tr>
                <th v-for="item of listScreenData" :key="item">
                  <span class="roleEditing-head">{{ item.role_name }}</span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td v-for="(item, index) of listScreenData" :key="item">
                  <div class="roleEditing-select">
                    <div class="roleEditing-check">
                      <label v-if="item.isCheck === true" class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" :checked="true" @click="checkBoxAll(item.isCheck, index)" />
                        <span class="custom-control-indicator"></span>
                      </label>
                      <label v-if="item.isCheck === false" class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" :checked="false" @click="checkBoxAll(item.isCheck, index)" />
                        <span class="custom-control-indicator"></span>
                      </label>
                      <label v-if="item.isCheck === '-'" class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" @click="checkBoxAll(item.isCheck, index)" />
                        <span class="custom-control-indicator custom"></span>
                      </label>
                    </div>
                    <div class="roleEditing-drop">
                      <button
                        class="btn btn--arrow"
                        type="button"
                        @click="openFilter(index, item.isActive, item.isCheck)"
                        @touchstart="openFilter(index, item.isActive, item.isCheck)"
                      >
                        &nbsp;
                      </button>
                      <div :class="{ showFilter: item.isActive === activeFilter }" class="dropdown-menu dropdown-select">
                        <ul>
                          <li :class="{ active: active_el === 'check' }" @click="check('check', index)" @touchstart="check('check', index)">権限あり</li>
                          <li :class="{ active: active_el === 'unCheck' }" @click="check('unCheck', index)" @touchstart="check('unCheck', index)">権限なし</li>
                          <li :class="{ active: active_el === 'dontChange' }" @click="check('dontChange', index)" @touchstart="check('dontChange', index)">
                            変更しない
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="roleEditing-btn">
          <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
          <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="submit">
            <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
          </button>
        </div>
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
          @onFinishScreen="onResultModal"
          @handleYes="handleYes"
          @overWrite="overWriteData"
        ></component>
      </template>
    </el-dialog>
  </div>
  <!-- End -->
</template>

<script>
import _ from 'lodash';
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
    data: {
      type: Object,
      default() {}
    },
    isCurrentDateinArr: {
      type: Array,
      default: () => []
    },
    arrStartDateOld: {
      type: Array,
      default: () => []
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      visible: null,
      loading: false,
      validateMessages,
      dataMulti: [],
      listScreenData: [],
      activeFilter: true,
      userCd: [],
      startDate: this.tomorrow(),
      active_el: '',
      checkMessageInvalid: false,
      dataTheFirst: [],
      startDateOld: [],
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
      curentDate2: '',
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
  unmounted() {
    localStorage.removeItem('dataI01S02');
  },
  mounted() {
    this.curentDate = this.formatFullDate(new Date());
    this.curentDate2 = this.formatFullDate3(new Date());
    this.getScreen();
  },
  methods: {
    checkMouseLeave(index) {
      this.dataMulti[index].visible = false;
    },
    checkMouseEnter(index) {
      this.dataMulti[index].visible = true;
    },
    setDateInMouted(date) {
      const dates = new Date();
      dates.setTime(dates.getTime() - 3600 * 1000 * 24);
      return dates > date.setDate(date.getDate());
    },
    getScreen() {
      I01_S02_PermissionSetting.getScreen().then((res) => {
        this.listScreenData = res.data.data.role;
        this.listScreenData.forEach((element) => {
          element.isActive = false;
          element.isCheck = false;
        });
        this.setData();
      });
      setTimeout(() => {
        localStorage.setItem('dataI01S02', JSON.stringify(this.listScreenData));
      }, 1000);
    },
    setData() {
      this.data.forEach((element) => {
        if (element.isCheck) {
          this.dataMulti.push(element);
          this.userCd.push(element.user_cd);
        }
      });
      let tempDataMulti = this.dataMulti.map((item) => ({
        user_role: [item?.user_role[item?.user_role.length - 1]]
      }));
      let lengthChild = tempDataMulti.length;
      let arr_new = [];
      this.listScreenData.forEach((element) => {
        let flg = 0;
        let flgArr = 0;
        tempDataMulti.forEach((el) => {
          if (el?.user_role?.length > 0) {
            el?.user_role.forEach((elChild) => {
              elChild?.role.forEach((element2) => {
                if (element?.role === element2?.role) {
                  flg = flg + 1;
                }
              });
            });
          } else {
            flgArr = flgArr + 1;
          }
        });
        if (flg === lengthChild - flgArr && lengthChild - flgArr !== 0) {
          arr_new.push(true);
        } else if (flg === 0) {
          arr_new.push(false);
        } else {
          arr_new.push('-');
        }
      });

      this.listScreenData.forEach((element, index) => {
        // arr_new.forEach((element2) => {
        this.listScreenData[index].isCheck = arr_new[index];
        // });
      });
      this.dataMulti.forEach((element) => {
        element.isCheckErr = false;
        element.visible = false;
      });
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
    checkBoxAll(item, index) {
      if (item === true || item === '-') {
        this.check2('unCheck', index);
      } else {
        this.check2('check', index);
      }
    },
    openFilter(index, isActive, isCheck) {
      if (isCheck === '-') {
        this.active_el = 'dontChange';
      } else if (isCheck === false) {
        this.active_el = 'unCheck';
      } else if (isCheck === true) {
        this.active_el = 'check';
      }
      if (!isActive) {
        this.listScreenData.forEach((element) => {
          element.isActive = false;
        });
      }

      this.listScreenData[index].isActive = !this.listScreenData[index].isActive;
    },
    check(typeCheck, index) {
      if (typeCheck === 'check') {
        this.active_el = 'check';
        this.listScreenData[index].isCheck = true;
        this.listScreenData[index].isActive = !this.listScreenData[index].isActive;
      }
      if (typeCheck === 'unCheck') {
        this.active_el = 'unCheck';
        this.listScreenData[index].isCheck = false;
        this.listScreenData[index].isActive = !this.listScreenData[index].isActive;
      }
      if (typeCheck === 'dontChange') {
        this.active_el = 'dontChange';
        this.listScreenData[index].isCheck = '-';
        this.listScreenData[index].isActive = !this.listScreenData[index].isActive;
      }
    },
    check2(typeCheck, index) {
      if (typeCheck === 'check') {
        this.active_el = 'check';
        this.listScreenData[index].isCheck = true;
      }
      if (typeCheck === 'unCheck') {
        this.active_el = 'unCheck';
        this.listScreenData[index].isCheck = false;
      }
      if (typeCheck === 'dontChange') {
        this.active_el = 'dontChange';
        this.listScreenData[index].isCheck = '-';
      }
    },
    confirmCancel() {
      const dataTheFirst = JSON.parse(localStorage.getItem('dataI01S02'));
      if (!_.isEqual(this.listScreenData, dataTheFirst) || this.formatFullDate2(this.startDate) !== this.formatFullDate2(this.tomorrow())) {
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
      }
    },
    handleYes() {
      this.$emit('onFinishScreen', 1);
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog z05-s01' };
    },
    overWriteData() {
      this.isOverWriteData = true;
      this.onResultModal();
      this.submit();
    },
    checkOverWriteData() {
      let countDuplicateStartDate = 0;
      this.isCurrentDateinArr.forEach((element) => {
        if (element === this.formatFullDate3(this.startDate)) {
          countDuplicateStartDate += 1;
        }
      });
      if (countDuplicateStartDate > 0) {
        this.loading = false;
        this.callDialogOverWriteComfirm({ message: '開始日が同日のものが既に登録されています。上書きしてよろしいですか？' });
      } else {
        this.isOverWriteData = true;
        this.submit();
      }
    },
    submit() {
      // if (!this.checkValidate()) return;
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-I01S02-EditMulti',
          idNodeNotify: 'msfa-notify-I01S02-EditMulti',
          duration: 1500
        });
        return;
      }
      if (this.startDate === null) {
        this.startDate = '';
      }

      const arrRole = [];
      this.listScreenData.forEach((element) => {
        if (element.isCheck === true) {
          arrRole.push(element.role);
        }
        if (element.isCheck === '-') {
          arrRole.push(element.role + '-');
        }
      });
      if (!arrRole[0]) {
        this.notifyModal({
          message: 'ユーザーロールを入力してください。',
          type: 'error',
          classParent: 'modal-body-I01S02-EditMulti',
          idNodeNotify: 'msfa-notify-I01S02-EditMulti',
          duration: 1500
        });
      } else {
        this.loading = true;
        const data = {
          create_flag: 1,
          user_cd: this.userCd.toString(),
          start_date_old: this.arrStartDateOld,
          user_role: [
            {
              start_date: moment(this.startDate).format('YYYY-M-D'),
              end_date: '9999-12-31',
              roles: arrRole.toString(),
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
              if (err.response.data.message !== 'unpermission_list') {
                this.notifyModal({
                  message: err.response.data.message,
                  type: 'error',
                  classParent: 'modal-body-I01S02-EditMulti',
                  idNodeNotify: 'msfa-notify-I01S02-EditMulti',
                  duration: 1500
                });
              } else {
                let dataErr = err.response.data.data;
                this.dataMulti.forEach((element) => {
                  dataErr.forEach((element2) => {
                    if (element.user_cd === element2) element.isCheckErr = true;
                  });
                });
                if (dataErr.length > 0) {
                  this.checkMessageInvalid = true;
                }
              }
            })
            .finally(() => {
              this.loading = false;
              this.isOverWriteData = false;
            });
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.number-i01-s02 {
  .span-1 {
    background: #448add;
    border-radius: 50%;
    padding: 3px 9px;
    color: #ffffff;
    cursor: default;
  }
}
.tootip-custom {
  position: absolute;
  background: antiquewhite;
  padding: 15px;
  background: #d1e4ff;
  border-radius: 4px;
  right: 1px;
  top: -1px;
  z-index: 99;
  width: 100%;
  &::before {
    content: '';
    width: 20px;
    background: #d1e4ff;
    height: 20px;
    position: absolute;
    left: -10px;
    top: 4px;
    clip-path: polygon(50% 0%, 0% 45%, 50% 100%);
  }
  .tblSetting-add-custom {
    display: flex;
    justify-content: flex-start;
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
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
  span {
    margin-left: 95px;
  }
}
.invalid-feedback-1 {
  margin-top: 17px;
  span {
    margin-left: unset;
  }
}
.custom1 {
  width: 180px;
}
.custom1-1 {
  width: 160px;
  padding: 0 15px;
}
.custom {
  width: 19px;
  height: 18px;
  background: url(~@/assets/template/images/checkbox-i01-s02.svg) no-repeat !important;
  top: calc(50% - 7px);
}
.showFilter {
  display: block;
  position: absolute;
  top: 0;
}
.modal-roleEditing-multi {
  padding: 20px 32px 0 0;
  .roleEditing-flex {
    display: flex;
    flex-wrap: wrap;
    .roleEditing-info {
      width: 30%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
    .roleEditing-content {
      width: 70%;
      padding-left: 32px;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-top: 20px;
      }
    }
  }
  .roleEditing-box {
    background: #ffffff;
    box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
    border-radius: 0 8px 8px 0;
    padding: 6px 0;
    ul {
      padding: 0 8px;
      height: 380px;
      @media (max-width: $viewport_tablet) {
        height: 250px;
      }
      li {
        padding: 6px 20px;
        border-bottom: 1px solid #e3e3e3;
      }
    }
    .roleEditing-name-err {
      position: absolute;
      margin-right: 8px;
      &::before {
        content: '';
        width: 15px;
        height: 15px;
        border-radius: 50%;
        right: 8px;
        top: 4px;
        position: absolute;
        opacity: 0.6;
        background-color: #ff0000;
      }
    }
    .roleEditing-name-box {
      display: flex;
      justify-content: space-between;
      .roleEditing-name {
        font-weight: 700;
      }
    }

    .roleEditing-add {
      display: flex;
      .roleEditing-item {
        min-width: 13px;
        max-width: 13px;
        margin: -1px 5px 0 0;
      }
    }
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
        &:last-child {
          .roleEditing-select {
            .roleEditing-drop {
              .dropdown-select {
                left: initial !important;
                right: 0 !important;
              }
            }
          }
        }
      }
    }
    .roleEditing-select {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      .roleEditing-check {
        .custom-control {
          padding-left: 19px;
        }
      }
      .roleEditing-drop {
        position: relative;
        .btn {
          &.btn--arrow {
            position: relative;
            padding: 0 3px;
            height: 18px;
            &::after {
              position: absolute;
              top: 1px;
              left: 0;
              content: '';
              border-left: 6px solid transparent;
              border-right: 6px solid transparent;
              border-top: 6px solid black;
            }
          }
        }
        .dropdown-select {
          margin: 0;
          padding: 0;
          background: #fff;
          border: 1px solid #727f88;
          box-sizing: border-box;
          border-radius: 4px;
          font-size: 0.875rem;
          box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.5);
          min-width: initial;
          width: 130px;
          top: 20px !important;
          left: -20px !important;
          transform: none !important;
          ul {
            overflow: hidden;
            border-radius: 4px;
            li {
              padding: 6px 16px;
              cursor: pointer;
              color: #000000;
              font-weight: normal;
              &:hover {
                background: #f2f2f2;
              }
              &.active {
                background: #eef6ff;
                color: #2d3033;
                font-weight: 500;
              }
            }
          }
        }
      }
    }
  }
}
</style>
