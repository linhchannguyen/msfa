<template>
  <div class="modal-body modalMedical modal-body-H02S02">
    <div id="msfa-notify-H02S02"></div>
    <div class="formMedical">
      <div class="formGroup">
        <label class="formGroup-label">
          大学病院<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
        ></label>
        <div class="formGroup-control">
          <div class="form-icon icon-right">
            <span title_log="大学病院" class="icon log-icon" @click="openZ05S03">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
            </span>

            <div v-if="facilityResult.length > 0" class="form-control d-flex align-items-center">
              <div class="block-tags">
                <el-tag v-for="(item, index) in facilityResult" :key="item" class="m-0 el-tag-custom" closable @close="handleRemove(index)">
                  {{ item.facility_short_name }}
                </el-tag>
              </div>
            </div>
            <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" :class="checkFacility() ? 'hasErr' : ''" />
          </div>
        </div>
        <div v-if="checkFacility()" class="invalid-feedback">
          <span>{{ validateMessages.facility_short_name.required }}</span>
        </div>
      </div>

      <div class="formGroup">
        <label class="formGroup-label">
          医局（所属部科）<span class="required-start" style="margin: 0 0 0 -6px">
            <ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
        ></label>
        <div class="formGroup-control">
          <ul class="mfsa-custom-tab-select">
            <li v-for="item of medicalOffice" :key="item">
              <button
                :class="nameaAiveMedicalOffice === item.name ? 'active' : ''"
                class="btn btn-select"
                @click="aciveMedicalOffice(item)"
                @touchstart="aciveMedicalOffice(item)"
              >
                {{ item.name }}
              </button>
            </li>
          </ul>
        </div>
        <div v-if="nameaAiveMedicalOffice === '所属部科'" class="formGroup-controlInfo">
          <el-select v-if="facilityResult.length > 0" v-model="department_cd" placeholder="未選択" class="form-control-select" @change="changeDepartment">
            <el-option v-for="item in department" :key="item" :label="item.department_name" :value="item.department_cd"> </el-option>
          </el-select>
          <el-select v-else v-model="department_cd" disabled placeholder="未選択" class="form-control-select disabled">
            <el-option disabled label="" value="">未選択</el-option>
          </el-select>
        </div>
        <div
          v-if="nameaAiveMedicalOffice === '手入力'"
          class="formGroup-controlInfo"
          :class="isSubmit && !validation.office_name.required && !isActive ? 'hasErr' : ''"
        >
          <el-input v-model="office_name" clearable placeholder="内容入力" class="form-control-input" @input="inputOfficeName()" />
        </div>
        <div class="invalid-feedback">
          <span v-if="isSubmit && !validation.office_name.required" :class="isActive ? 'none' : ''">{{ validateMessages.office_name.required }}</span>
        </div>
      </div>
      <div class="formGroup-btn">
        <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
        <button :disabled="checkBtn || loading" type="button" class="btn btn-primary customBtn" @click="submit">
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
          @onFinishScreen="onResultModal"
          @handleConfirm="deleteFacilityGroup"
          @handleYes="handleYes"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import _ from 'lodash';
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';
import H02_S02_PersonalDetailsBasicInformation from '../../../api/H02/H02_S02_PersonalDetailsBasicInformation';
import { markRaw } from 'vue';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/H02/H02_S02_PersonalDetailsBasicInformation';
export default {
  name: 'H02_S02_PersonalDetailsBasicInformation',
  components: {},
  props: {
    personCd: {
      type: String,
      default: ''
    },
    departmentList: {
      type: Array,
      default: null
    },
    detail: {
      type: Object,
      required: true,
      default() {
        return {};
      }
    },
    changetab: {
      type: String,
      default: '3'
    },
    checkLoading: [Boolean]
  },

  emits: ['onFinishScreen'],
  data() {
    return {
      loading: false,
      validateMessages,
      isActive: false,
      dataTheFirst: {},
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalConfirm: false,
        isShowClose: true,
        component: null,
        props: {},
        width: '',
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      facilityResult: [],
      checkBtn: true,
      medicalOffice: [
        {
          id: 1,
          name: '所属部科'
        },
        {
          id: 2,
          name: '手入力'
        }
      ],
      nameaAiveMedicalOffice: '',
      department: [],
      department_cd: '',
      office_name: '',
      paramsZ05S03: {
        selectGroupCd: '',
        facilityCd: [],
        facilityName: []
      }
    };
  },
  computed: {
    validation() {
      return {
        office_name: { required: required(this.office_name) },
        facility_short_name: { required: required(this.facility_short_name) }
      };
    }
  },
  mounted() {
    const data = {
      facility_cd: this.detail.facility_cd,
      facility_short_name: this.detail.facility_short_name
    };

    if (data.facility_cd === undefined) {
      this.facilityResult = [];
    } else {
      if (data.facility_cd === null && data.facility_short_name === null) {
        this.facilityResult = [];
      } else {
        this.facilityResult.push(data);
        this.getDepartment(data);
      }
    }
    if (this.detail?.department_cd) {
      this.nameaAiveMedicalOffice = '所属部科';
      this.department_cd = this.detail?.department_cd;
      this.office_name = this.detail?.medical_office_name;
    } else {
      this.nameaAiveMedicalOffice = '手入力';
      this.office_name = this.detail?.medical_office_name;
    }
    this.dataTheFirst = {
      person_cd: this.personCd,
      medical_office_facility_cd: this.facilityResult[0] ? this.facilityResult[0].facility_cd : '',
      medical_office_department_cd: this.department_cd,
      medical_office_name: this.office_name
    };
    const userLogin = this.getCurrentUser();
    this.paramsZ05S03 = {
      ...this.paramsZ05S03,
      facility_cd: this.facilityResult[0] ? [this.facilityResult[0].facility_cd] : [],
      userCD: userLogin.user_cd,
      userName: userLogin.user_name
    };
    setTimeout(() => {
      localStorage.setItem('dataH02S02', JSON.stringify(this.dataTheFirst));
    }, 300);
    this.changeDepartment();
  },
  unmounted() {
    localStorage.removeItem('dataH02S02');
  },
  methods: {
    changeDepartment() {
      if (this.nameaAiveMedicalOffice === '所属部科' && this.department_cd === '') {
        this.checkBtn = true;
      } else {
        this.checkBtn = false;
      }
    },
    inputOfficeName() {
      if (this.office_name === '') {
        this.checkBtn = true;
      } else {
        this.checkBtn = false;
      }
    },
    confirmCancel() {
      const theFirstData = JSON.parse(localStorage.getItem('dataH02S02'));
      const data = {
        person_cd: this.personCd,
        medical_office_facility_cd: this.facilityResult[0] ? this.facilityResult[0].facility_cd : '',
        medical_office_department_cd: this.department_cd,
        medical_office_name: this.office_name
      };
      if (!_.isEqual(theFirstData, data)) {
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
    aciveMedicalOffice(item) {
      this.nameaAiveMedicalOffice = item.name;
      this.isActive = true;

      if (this.nameaAiveMedicalOffice === '手入力') {
        if (this.office_name === '' || this.office_name === null) {
          this.checkBtn = true;
        } else {
          this.checkBtn = false;
        }
      } else {
        if (this.department_cd === '') {
          this.checkBtn = true;
        } else {
          this.checkBtn = false;
        }
      }
    },
    checkFacility() {
      if (this.facilityResult.length > 0) {
        return false;
      } else {
        return true;
      }
    },
    submit() {
      if (this.nameaAiveMedicalOffice === '所属部科') {
        this.office_name = '';
      } else {
        this.department_cd = '';
      }
      this.isActive = true;
      if (this.nameaAiveMedicalOffice === '手入力') {
        this.isActive = false;
        if (!this.checkValidate()) {
          this.notifyModal({
            message: '入力エラーを確認してください。',
            type: 'error',
            classParent: 'modal-body-H02S02',
            idNodeNotify: 'msfa-notify-H02S02'
          });
          return;
        }
      }
      this.checkFacility();
      this.loading = true;
      const data = {
        person_cd: this.personCd,
        medical_office_facility_cd: this.facilityResult[0] ? this.facilityResult[0].facility_cd : '',
        medical_office_department_cd: this.department_cd,
        medical_office_name: this.office_name
      };
      H02_S02_PersonalDetailsBasicInformation.updateMedicalOffice(data)
        .then((res) => {
          if (res) {
            this.$emit('onFinishScreen', 2);
          }
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-H02S02', idNodeNotify: 'msfa-notify-H02S02' });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    openZ05S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設選択',
        isShowModal: true,
        component: markRaw(Z05S03FacilitySelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '55rem',
        customClass: 'custom-dialog',
        props: {
          mode: 'single',
          ...this.paramsZ05S03,
          orgCD: this.paramsZ05S03.org_cd,
          userCD: this.paramsZ05S03.userCD,
          userName: this.paramsZ05S03.userName,
          selectGroupCd: this.paramsZ05S03.select_group_id,
          targetProductCd: this.paramsZ05S03.target_product_cd,
          facilityCategoryType: this.paramsZ05S03.facility_category_type,
          prefectureCD: this.paramsZ05S03.prefecture_cd,
          prefectureName: this.paramsZ05S03.prefecture_name,
          municipalityCD: this.paramsZ05S03.municipality_cd,
          municipalityName: this.paramsZ05S03.municipality_name,
          facilityCd: this.paramsZ05S03.facility_cd,
          facilityName: this.paramsZ05S03.facility_name
        },
        destroyOnClose: true
      };
    },
    onResultModal(e) {
      if (e) {
        this.facilityResult = e.facilitySelectList;
        this.paramsZ05S03 = {
          ...e.filterData,
          facility_cd: [e?.facilitySelectList[0].facility_cd],
          facility_name: [e.filterData.facility_name]
        };
        this.onCloseModal();
      } else {
        this.onCloseModal();
      }
      // this.department_cd = '';
      if (e) {
        this.getDepartment(e.facilitySelectList);
        this.department_cd = '';
      }
    },
    getDepartment(facilitySelectList) {
      const data = {
        facility_cd: facilitySelectList.facility_cd || facilitySelectList[0].facility_cd
      };
      H02_S02_PersonalDetailsBasicInformation.getDepartment(data)
        .then((res) => {
          if (res) {
            this.department = res.data.data;
          }
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-H02S02', idNodeNotify: 'msfa-notify-H02S02' });
        })
        .finally(() => {});
    },
    handleRemove(index) {
      this.facilityResult.splice(index, 1);
      this.department_cd = '';
      this.changeDepartment();
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;

.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 35%;
    top: 29%;
  }
}
.el-icon-loading {
  display: none;
}
.required-start {
  line-height: 16px;
  min-width: 9px;
  margin: 0 0 0 8px;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
}
.personalDetails {
  height: 100%;
  padding-top: 35px;
  @media (max-width: $viewport_desktop) {
    padding-top: 20px;
  }
  .personal-row {
    display: flex;
    flex-wrap: wrap;
    height: 100%;
    @media (max-width: $viewport_desktop) {
      height: initial;
    }
    .personalLeft {
      width: 50%;
      padding: 0 24px 20px;
      height: 100%;
      @media (max-width: $viewport_desktop) {
        height: initial;
        width: 100%;
      }
    }
    .personalRight {
      width: 50%;
      height: 100%;
      background: #f7f7f7;
      box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
      border-radius: 20px 0 0 0;
      @media (max-width: $viewport_desktop) {
        height: initial;
        width: 100%;
        margin: 0 24px;
        border-radius: 20px 20px 0 0;
      }
    }
  }
  .personalLeft-content {
    > ul {
      > li {
        display: flex;
        flex-wrap: wrap;
        margin-top: 24px;
      }
    }
    .personalLeft-title {
      font-size: 1rem;
      width: 92px;
      padding-right: 10px;
    }
    .personalLeft-info {
      width: calc(100% - 92px);
      .medicalOffice {
        display: flex;
        .medicalOffice-label {
          color: #2d3033;
          font-weight: 500;
          padding-right: 38px;
        }
        .btn {
          min-width: 73px;
          height: 32px;
          padding: 0;
          margin-top: -5px;
          .btn-iconLeft {
            margin: 0;
            .svg {
              width: 16px;
            }
          }
        }
      }
      .personalLeft-label {
        font-size: 1rem;
        color: #2d3033;
      }
    }
  }
  .personalTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    .headTabs {
      padding: 0 24px;
      .nav {
        border-bottom: 1px solid #b7c3cb;
        li {
          width: 33.333333%;
          a {
            padding: 15px 0 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            color: #99a5ae;
            font-size: 1.125rem;
            position: relative;
            line-height: 100%;
            &::after {
              position: absolute;
              bottom: -1px;
              left: 0;
              content: '';
              width: 100%;
              height: 3px;
              display: block;
              transition: 400ms all;
            }
            &:hover {
              text-decoration: none;
              color: #5f6b73;
              font-weight: 700;
              &::after {
                background: #448add;
              }
            }
            &.active {
              color: #5f6b73;
              font-weight: 700;
              &::after {
                background: #448add;
              }
            }
          }
        }
      }
    }
    .personalMain {
      height: 100%;
      padding: 20px 24px 28px;
      overflow: hidden;
      .tab-content,
      .tab-pane {
        height: 100%;
      }
    }
  }
  .medicalGroup {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    .medicalHead {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      padding: 16px 24px;
      font-size: 1rem;
      font-weight: 700;
      color: #fff;
    }
    .medicalMain {
      height: 100%;
      @media (max-width: $viewport_desktop) {
        height: 400px;
      }
      ul {
        li {
          padding: 16px 24px;
          box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
          font-size: 1rem;
          background: #fff;
        }
      }
    }
    .medicalHead-col {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          padding: 16px 32px;
          width: 50%;
          color: #fff;
          font-size: 1rem;
          font-weight: 700;
          position: relative;
          &::after {
            position: absolute;
            right: 0;
            top: 8px;
            content: '';
            width: 1px;
            height: calc(100% - 16px);
            background: #fff;
          }
          &:last-child {
            &::after {
              background: none;
            }
          }
        }
      }
    }
    .medicalMain-col {
      height: 100%;
      ul {
        li {
          display: flex;
          flex-wrap: wrap;
          box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
          background: #fff;
          .societyName,
          .academicYear {
            width: 50%;
            padding: 16px 32px;
          }
          .societyName {
            border-right: 1px solid #e3e3e3;
          }
        }
      }
    }
  }
}
/*** modal ***/
.formMedical {
  .formGroup {
    margin-bottom: 20px;
    .formGroup-label {
      margin-bottom: 6px;
    }
    .formGroup-control {
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          width: 50%;
          .btn {
            width: 100%;
          }
          &:nth-child(1) {
            .btn {
              border-radius: 4px 0 0 4px;
            }
          }
          &:nth-child(2) {
            margin-left: -1px;
            .btn {
              border-radius: 0 4px 4px 0;
            }
          }
        }
      }
    }
    .formGroup-controlInfo {
      margin-top: 20px;
    }
  }
  .formGroup-btn {
    display: flex;
    .btn {
      width: 50%;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.none {
  display: none;
}
</style>
