<template>
  <div class="custom-body">
    <ul class="custom">
      <li>
        <label style="color: #5f6b73" class="facility-label">
          施設 <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
        ></label>
        <div class="form-icon icon-right" :class="isSubmit && !validation.facilitySelect.required ? 'hasErr' : ''">
          <span class="icon" @click="openModal_Z05_S03">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
          </span>
          <div v-if="facilitySelect.length > 0" class="form-control d-flex align-items-center">
            <div class="block-tags">
              <el-tag v-for="(item, index) of facilitySelect" :key="item" class="m-0 el-tag-custom" closable @close="handleRemove(index)">
                {{ item.facility_short_name }}
              </el-tag>
            </div>
          </div>
          <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
        </div>
        <div v-if="isSubmit && !validation.facilitySelect.required" class="invalid-feedback invalid-feedback-custom">
          <span>{{ validateMessages.facilitySelect.required }}</span>
        </div>
      </li>
      <li>
        <label style="color: #5f6b73" class="facility-label">
          メディカルスタッフ <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
        ></label>
        <div class="form-userEdit-control" :class="isSubmit && !validation.medicalStaffI.required ? 'hasErr' : ''">
          <el-select v-model="medicalStaffI" placeholder="未選択" class="form-control-select">
            <el-option label="" value="">未選択</el-option>
            <el-option v-for="item of medicalStaff ? medicalStaff : []" :key="item" :label="item.medical_staff_name" :value="item.medical_staff_cd">
            </el-option>
          </el-select>
        </div>
        <div v-if="isSubmit && !validation.medicalStaffI.required" class="invalid-feedback invalid-feedback-custom">
          <span>{{ validateMessages.medicalStaffI.required }}</span>
        </div>
      </li>
      <li>
        <label style="color: #5f6b73" class="facility-label">
          氏名 <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
        ></label>
        <div class="form-userEdit-control" :class="isSubmit && !validation.personName.required ? 'hasErr' : ''">
          <el-input v-model="personName" clearable placeholder="名前入力" class="form-control-input" />
        </div>
        <div v-if="isSubmit && !validation.personName.required" class="invalid-feedback">
          <span>{{ validateMessages.personName.required }}</span>
        </div>
      </li>
    </ul>
    <div class="userEdit-btn">
      <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="submitModal(1)">キャンセル</button>
      <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="submitModal(2)">
        <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 設定
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
    :before-close="onCloseModal"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
    </template>
  </el-dialog>
</template>
<script>
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/C01/C01_S03_AttendantManagement';
import { markRaw } from 'vue';
export default {
  name: 'ModalOtherPersonalAddition',
  props: {
    medicalStaff: {
      type: Array,
      required: false,
      default() {
        return [];
      }
    }
  },
  emits: ['onFinishScreenMOPA'],
  data() {
    return {
      loading: false,
      validateMessages,
      medicalStaffI: '',
      personName: '',
      props: {
        userFlag: 0,
        mode: 'single',
        orgCdList: [],
        orgCd: '',
        userCdList: []
      },
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      facilitySelect: [],

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
        facilitySelect: { required: required(this.facilitySelect) },
        medicalStaffI: { required: required(this.medicalStaffI) },
        personName: { required: required(this.personName) }
      };
    }
  },
  mounted() {
    const userLogin = this.getCurrentUser();
    this.paramsZ05S03 = {
      ...this.paramsZ05S03,
      userCD: userLogin.user_cd,
      userName: userLogin.user_name
    };
  },
  methods: {
    openModal_Z05_S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設選択',
        isShowModal: true,
        component: markRaw(Z05S03FacilitySelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : this.props.mode === 'single' ? '55rem' : '65rem',
        props: {
          mode: this.props.mode,
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
        }
      };
    },
    submitModal(type) {
      if (type === 1) {
        this.$emit('onFinishScreenMOPA', 'close');
      } else {
        // if (!this.checkValidate()) return;
        if (!this.checkValidate()) {
          this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
          return;
        }
        this.loading = true;
        const data = {
          other_medical_staff_type: this.medicalStaffI,
          person_name: this.personName,
          facility_cd: this.facilitySelect[0].facility_cd,
          facility_short_name: this.facilitySelect[0].facility_short_name,
          facility_name_kana: this.facilitySelect[0].facility_name_kana
        };
        this.$emit('onFinishScreenMOPA', data);
        this.loading = false;
      }
    },
    onResultModal(e) {
      this.onCloseModal();
      if (e.screen === 'Z05_S03') {
        this.facilitySelect = e.facilitySelectList;

        this.paramsZ05S03 = {
          ...e.filterData,
          facility_cd: [e?.facilitySelectList[0].facility_cd],
          facility_name: [e.filterData.facility_name]
        };

        this.onCloseModal();
      }
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    handleRemove() {
      this.facilitySelect = [];
      this.paramsZ05S03.facility_cd = [];
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
.custom {
  li {
    .facility-label {
      margin-bottom: 8px;
    }
    .form-icon,
    .form-control-select {
      margin-bottom: 20px;
    }
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
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
}
.invalid-feedback-custom {
  position: relative;
  top: -15px;
}
.required-start {
  line-height: 18px;
  min-width: 9px;
  margin: 0 0 0 8px;
}
</style>
