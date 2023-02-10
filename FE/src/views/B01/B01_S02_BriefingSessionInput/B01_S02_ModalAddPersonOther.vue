<template>
  <!-- name: その他個人追加 -->
  <div class="modal-person modal-body-B01S02-OtherPerson">
    <div id="msfa-notify-B01S02-OtherPerson"></div>
    <div class="personForm">
      <ul>
        <li class="w-100">
          <label class="personForm-label">
            施設
            <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
          </label>
          <div class="personForm-control">
            <div class="form-icon icon-right" :class="isSubmit && !validation.facility_cd.required ? 'hasErr' : ''">
              <span class="icon icon--cursor" @click="openModalZ05S03()">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
              </span>
              <div v-if="formData.facility_short_name" class="form-control d-flex align-items-center">
                <div class="block-tags">
                  <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveFacility()">
                    {{ formData.facility_short_name }}
                  </el-tag>
                </div>
              </div>
              <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
            </div>
            <span v-if="isSubmit && !validation.facility_cd.required" class="text-error">{{ getMessage('MSFA0040', '施設') }}</span>
            <span v-else class="text-error">{{ responseErrors.facility_cd }}</span>
          </div>
        </li>
        <li class="w-100">
          <label class="personForm-label">
            メディカルスタッフ
            <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
          </label>
          <div class="personForm-control" :class="isSubmit && !validation.medical_staff_cd.required ? 'hasErr' : ''">
            <el-select v-model="formData.medical_staff_cd" placeholder="未選択" class="form-control-select">
              <el-option :value="''">未選択</el-option>
              <el-option v-for="item in lstOtherMedicalStaff" :key="item.medical_staff_cd" :label="item.medical_staff_name" :value="item.medical_staff_cd">
              </el-option>
            </el-select>
            <span v-if="isSubmit && !validation.medical_staff_cd.required" class="text-error">{{ getMessage('MSFA0040', 'メディカルスタッフ') }}</span>
            <span v-else class="text-error">{{ responseErrors.medical_staff_cd }}</span>
          </div>
        </li>
        <li class="w-100">
          <label class="personForm-label">
            氏名
            <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
          </label>
          <div class="personForm-control" :class="isSubmit && !validation.person_name.required ? 'hasErr' : ''">
            <el-input v-model="formData.person_name" clearable placeholder="内容入力" class="form-control-input" />
            <span v-if="isSubmit && !validation.person_name.required" class="text-error">{{ getMessage('MSFA0001', '氏名') }}</span>
            <span v-else class="text-error">{{ responseErrors.person_name }}</span>
          </div>
        </li>
      </ul>
    </div>
    <div class="personForm-btn text-center">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModalAdd">キャンセル</button>
      <el-button type="primary" class="btn btn-primary" :loading="processingSave" @click.prevent="addPersonOther"> 登録 </el-button>
    </div>
  </div>

  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :show-close="modalConfig.isShowClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
    </template>
  </el-dialog>
</template>
<script>
/* eslint-disable eqeqeq */
import { markRaw } from 'vue';
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';
import { required } from '@/utils/validate';

export default {
  name: 'B01_S02_ModalAddPersonOther',
  components: {},
  props: {
    isEdit: {
      type: Boolean,
      required: true,
      defaul: false // true : edit | false: create
    },
    person: {
      type: Object,
      required: false
    },
    lstOtherMedicalStaff: {
      type: Array,
      required: true
    },
    facility: {
      type: Object,
      required: null
    },
    currentUser: {
      type: Object,
      defaul: null
    }
  },
  emits: ['onFinishScreen'],
  data: function () {
    return {
      processingSave: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        isShowClose: true,
        closeOnClickMark: false
      },

      paramsZ05S03: {
        selectGroupCd: '',
        facility_cd: [],
        facility_name: [],
        org_cd: '',
        user_cd: '',
        user_name: ''
      },

      formData: {
        facility_short_name: '',
        facility_cd: '',
        person_cd: '',
        person_name: '',
        medical_staff_cd: '',
        medical_staff_name: ''
      }
    };
  },

  computed: {
    validation() {
      return {
        facility_cd: { required: required(this.formData.facility_cd) },
        person_name: { required: required(this.formData.person_name) },
        medical_staff_cd: { required: required(this.formData.medical_staff_cd) }
      };
    }
  },
  mounted() {
    if (this.isEdit) {
      this.setFormdata();
    } else {
      let isFaci = this.facility && Object.keys(this.facility).length > 0;
      if (isFaci) {
        this.formData = {
          ...this.formData,
          facility_short_name: this.facility.facility_short_name,
          facility_cd: this.facility.facility_cd
        };

        this.paramsZ05S03 = {
          selectGroupCd: '',
          facility_cd: [this.facility.facility_cd],
          facility_name: []
        };
      } else {
        this.paramsZ05S03 = {
          selectGroupCd: '',
          facility_cd: [],
          facility_name: [],
          org_cd: this.currentUser.org_cd,
          user_cd: this.currentUser.user_cd,
          user_name: this.currentUser.user_name
        };
      }
    }
  },

  methods: {
    setFormdata() {
      this.formData = {
        facility_short_name: this.person.facility_short_name,
        facility_cd: this.person.facility_cd,
        person_cd: this.person.person_cd,
        person_name: this.person.person_name,
        medical_staff_cd: this.person.medical_staff_cd
      };
    },

    openModalZ05S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設選択',
        isShowModal: true,
        component: markRaw(Z05S03FacilitySelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '55rem',
        props: {
          mode: 'single',
          ...this.paramsZ05S03,
          orgCD: this.paramsZ05S03.org_cd,
          userCD: this.paramsZ05S03.user_cd,
          userName: this.paramsZ05S03.user_name,
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

    onResultModalZ05S03(e) {
      if (e?.facilitySelectList.length > 0) {
        let data = e?.facilitySelectList[0];
        this.formData.facility_short_name = data.facility_short_name;
        this.formData.facility_cd = data.facility_cd;

        this.paramsZ05S03 = {
          ...e.filterData,
          facility_cd: [e?.facilitySelectList[0].facility_cd],
          facility_name: [e.filterData.facility_name]
        };
      }
    },

    handleRemoveFacility() {
      this.formData.facility_short_name = '';
      this.formData.facility_cd = '';
    },

    inValidForm() {
      if (this.formData.facility_cd == '' || this.formData.person_name == '') {
        return true;
      }
      return false;
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S03') {
          this.onResultModalZ05S03(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: false,
        customClass: 'custom-dialog'
      };
    },

    addPersonOther() {
      if (!this.checkValidate()) {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-B01S02-OtherPerson',
          idNodeNotify: 'msfa-notify-B01S02-OtherPerson'
        });
        return;
      } else {
        this.processingSave = true;
        let result = {
          screen: 'B01_S02_ModalAddOtherPerson',
          isEdit: this.isEdit,
          personOther: {
            ...this.formData,
            person_cd: this.formData.medical_staff_cd,
            medical_staff_name: this.lstOtherMedicalStaff.find((x) => x.medical_staff_cd == this.formData.medical_staff_cd)?.medical_staff_name
          }
        };

        this.$emit('onFinishScreen', result);
      }
    },

    closeModalAdd() {
      this.$emit('onFinishScreen', null);
    }
  }
};
</script>
<style lang="scss" scoped>
@import '../../../assets/css/_animations.scss';
.modal-person {
  .person-title {
    padding-top: 20px;
    color: #2d3033;
    font-size: 1.125rem;
    font-weight: 700;
  }
  .personForm {
    > ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -20px;
      > li {
        width: 50%;
        padding-left: 20px;
      }
      > li:not(:first-of-type) {
        margin-top: 24px;
      }
    }
    .personForm-label {
      margin-bottom: 10px;
      font-size: 1rem;
      display: flex;
      align-items: center;
      color: #5f6b73;
      .required {
        margin: -2px 0 0 8px;
      }
    }
    .personForm-control {
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -20px;
        > li {
          width: 50%;
          padding-left: 20px;
        }
      }
    }
  }
  .personForm-btn {
    margin-top: 24px;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
</style>
