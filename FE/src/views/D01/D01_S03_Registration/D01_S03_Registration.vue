<template>
  <div class="wrapContent D01S03-custom">
    <div class="groupResign">
      <div class="resignWrap scrollbar">
        <div v-loading="loading" class="resignRow">
          <div class="resignCol">
            <div class="resignForm-group">
              <div class="col1">
                <div class="resignForm">
                  <label class="resignForm-label">
                    タイトル<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                  </label>
                  <div class="resignForm-control" :class="(isSubmit && !validation.document_nameI.required) || validateDocumentNameI() ? 'hasErr' : ''">
                    <el-input v-model="document_nameI" clearable placeholder="タイトルを入力" class="form-control-input" @input="changeInputSave" />
                  </div>
                </div>
                <div class="invalid-feedback">
                  <span v-if="validateDocumentNameI()" class="invalid">{{ getMessage('MSFA0012', 'タイトル', 50) }}</span>
                  <span v-if="isSubmit && !validation.document_nameI.required">{{ validateMessages.document_nameI.required }}</span>
                </div>
              </div>
              <div class="col2">
                <div class="resignForm">
                  <label class="resignForm-label">
                    バージョン<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
                  ></label>
                  <div class="resignForm-control" :class="(isSubmit && !validation.document_versionI.required) || validateDocumentVersionI() ? 'hasErr' : ''">
                    <el-input v-model="document_versionI" clearable placeholder="バージョンを入力" class="form-control-input" @input="changeInputSave" />
                  </div>
                </div>
                <div class="invalid-feedback">
                  <span v-if="validateDocumentVersionI()" class="invalid">{{ getMessage('MSFA0012', 'バージョン', 20) }}</span>
                  <span v-if="isSubmit && !validation.document_versionI.required">{{ validateMessages.document_versionI.required }}</span>
                </div>
              </div>
            </div>
            <div class="resignForm">
              <label class="resignForm-label">
                資材アップロード <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                <el-tooltip class="box-item" effect="dark" content=" PDF、動画　（MP4）の登録が可能です。" placement="right-start">
                  <span class="resignForm-item"><img src="@/assets/template/images/icon_question.svg" alt="" /></span>
                </el-tooltip>
              </label>
              <div class="resignForm-control">
                <div class="resignUpload">
                  <!-- :disabled="activeUploadType" -->
                  <button
                    :disabled="loadingDownload"
                    type="button"
                    class="btn btn-outline-primary btn-outline-primary--cancel custom-btn"
                    @click="handleChooseFile"
                  >
                    <span class="icon"><ImageSVG :src-image="'icon_upload.svg'" :alt-image="'icon_upload'" /></span>
                    <span style="margin-left: 5px; padding: 3px 0 0px 0px">ファイル選択</span>
                  </button>
                  <input
                    id="upload-file"
                    ref="fileUploadD01S03"
                    style="display: none"
                    type="file"
                    accept="application/pdf, video/mp4"
                    @input="changeInputSave"
                    @change="validFile"
                  />
                  <span class="file-name">
                    <span v-if="!loadingDownload" :class="checkBtn ? 'cursor-pointer' : ''" @click="downloadFileS3()" @touchstart="downloadFileS3()">
                      {{ file_name }}
                    </span>
                    <span v-if="loadingDownload">
                      <div class="lds-ellipsis">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                    </span>
                  </span>

                  <div class="invalid-feedback">{{ errValidateFile }}</div>
                </div>
              </div>
            </div>

            <div class="resignForm">
              <label class="resignForm-label">親資材紐付</label>
              <div class="resignForm-control">
                <div class="form-icon icon-right">
                  <span title_log="親資材紐付" class="icon log-icon" @click="openZ05S05" @touchstart="openZ05S05">
                    <img src="@/assets/template/images/icon_popup.svg" alt="" />
                  </span>
                  <div v-if="dataResultZ05S05.document_name" class="form-control d-flex align-items-center">
                    <div class="block-tags">
                      <el-tag class="m-0 el-tag-custom" closable @close="handleRemoveUserZ05S05">
                        {{ dataResultZ05S05.document_name }}
                      </el-tag>
                    </div>
                  </div>
                  <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                </div>
              </div>
            </div>
            <div class="invalid-feedback"></div>
            <div class="resignForm">
              <label class="resignForm-label">対象疾患</label>
              <div class="resignForm-control" :class="validatediseaseI() ? 'hasErr' : ''">
                <el-input
                  v-model="diseaseI"
                  clearable
                  placeholder="対象疾患を入力"
                  class="form-control-input"
                  @input="changeInputSave"
                  @change="validateMessageD01S03"
                />
              </div>
            </div>
            <div class="invalid-feedback custom-feedback">
              <span v-if="validatediseaseI()" class="invalid">{{ getMessage('MSFA0012', '対象疾患', 50) }}</span>
            </div>
            <div class="resignForm">
              <label class="resignForm-label">
                資材種別<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
              ></label>
              <div class="resignForm-control" :class="isSubmit && !validation.document_categoryI.required ? 'hasErr' : ''">
                <el-select v-model="document_categoryI" class="form-control-select" @change="changeInputSave">
                  <!-- <el-option label="" value="00">未選択</el-option> -->
                  <el-option v-for="item in document_category" :key="item" :label="item.document_category_name" :value="item.document_category_cd"> </el-option>
                </el-select>
              </div>
            </div>
            <div class="invalid-feedback">
              <span v-if="isSubmit && !validation.document_categoryI.required">{{ validateMessages.document_categoryI.required }}</span>
            </div>
            <div class="resignForm">
              <label class="resignForm-label">資材概要</label>
              <div class="resignForm-control" :class="validateDocumentContents() ? 'hasErr' : ''">
                <el-input
                  v-model="document_contentsI"
                  clearable
                  class="form-control-textarea"
                  :rows="4"
                  type="textarea"
                  placeholder="資材概要を入力"
                  @input="changeInputSave"
                />
              </div>
            </div>
            <div class="invalid-feedback">
              <span v-if="validateDocumentContents()" class="invalid">{{ getMessage('MSFA0012', '資材概要', 300) }}</span>
            </div>
          </div>
          <div class="resignCol">
            <div class="resignForm">
              <label class="resignForm-label">
                適用期間<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
              ></label>
              <div class="resignForm-calendar">
                <div class="form-icon icon-right" :class="isSubmit && !validation.release_start_date.required ? 'hasErr' : ''">
                  <span class="icon icon-custom">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                  </span>
                  <el-date-picker
                    v-model="startDate"
                    :disabled-date="disabledDateStart"
                    format="YYYY/M/D"
                    type="date"
                    :editable="false"
                    placeholder="開始日"
                    class="form-control-datePickerLeft"
                    @change="checkDateNull"
                  ></el-date-picker>
                </div>

                <div class="resignForm-item">{{ JapaneseTilde() }}</div>
                <div class="form-icon icon-right" :class="isSubmit && !validation.release_end_date.required ? 'hasErr' : ''">
                  <span class="icon icon-custom">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                  </span>
                  <el-date-picker
                    v-model="endDate"
                    :disabled-date="disabledDateEnd"
                    format="YYYY/M/D"
                    type="date"
                    :editable="false"
                    placeholder="終了日"
                    class="form-control-datePickerLeft"
                    @input="changeInputSave"
                    @change="checkDateNull"
                  ></el-date-picker>
                </div>
              </div>
            </div>
            <div style="display: flex; justify-content: space-between">
              <div class="invalid-feedback">
                <span v-if="isSubmit && !validation.release_start_date.required">{{ validateMessages.release_start_date.required }}</span>
              </div>
              <div class="invalid-feedback" style="padding-left: 26px">
                <span v-if="isSubmit && !validation.release_end_date.required">{{ validateMessages.release_end_date.required }}</span>
              </div>
            </div>
            <div class="resignForm">
              <label class="resignForm-label">
                品目<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
              ></label>
              <div class="resignForm-control">
                <div class="form-icon icon-right" :class="isSubmit && !validation.dataProduct.required ? 'hasErr' : ''">
                  <span title_log="品目" class="icon log-icon" @click="openModalZ05S06" @touchstart="openModalZ05S06">
                    <img src="@/assets/template/images/icon_popup.svg" alt="" />
                  </span>
                  <div v-if="dataProduct.product_name" class="form-control d-flex align-items-center">
                    <div class="block-tags">
                      <el-tag class="m-0 el-tag-custom" closable @close="handleRemoveZ05S06">
                        {{ dataProduct.product_name }}
                      </el-tag>
                    </div>
                  </div>
                  <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                </div>
              </div>
            </div>
            <div class="invalid-feedback">
              <span v-if="isSubmit && !validation.dataProduct.required">{{ validateMessages.dataProduct.required }}</span>
            </div>
            <div class="resignForm">
              <label class="resignForm-label">
                使用可能組織<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
              ></label>
              <div class="resignForm-control">
                <div class="organizations">
                  <div class="organizations-checkBox">
                    <label class="custom-control custom-checkbox">
                      <input v-model="availabledepartments" class="custom-control-input" type="checkbox" checked @input="checkkend" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">設定あり</span>
                    </label>
                  </div>
                  <div class="form-icon icon-right" :class="availabledepartments && isSubmit && !dataResultZ05S01.org_name ? 'hasErr' : ''">
                    <span title_log="使用可能組織" class="icon log-icon" @click="openModal_Z05_S01" @touchstart="openModal_Z05_S01">
                      <img src="@/assets/template/images/icon_popup.svg" alt="" />
                    </span>
                    <div v-if="dataResultZ05S01.org_label" class="form-control d-flex align-items-center">
                      <div class="block-tags">
                        <el-tag class="m-0 el-tag-custom" closable @close="handleRemoveZ05S01">
                          {{ dataResultZ05S01.org_label }}
                        </el-tag>
                      </div>
                    </div>
                    <el-input v-else clearable :disabled="!availabledepartments" readonly placeholder="未選択" class="form-control-input" />
                  </div>
                  <div style="margin-left: 90px" class="invalid-feedback">
                    <span v-if="availabledepartments && isSubmit && !dataResultZ05S01.org_name">選択してください。</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="resignForm">
              <label class="resignForm-label"> 診療科目分類</label>
              <div class="resignForm-control">
                <el-select v-model="medical_subjects_groupI" placeholder="未選択" class="form-control-select" @change="changeInputSave">
                  <el-option label="" value=""> 未選択 </el-option>
                  <el-option
                    v-for="item in medical_subjects_group"
                    :key="item"
                    :label="item.medical_subjects_group_name"
                    :value="item.medical_subjects_group_cd"
                  >
                  </el-option>
                </el-select>
              </div>
            </div>
            <div class="invalid-feedback"></div>
            <div class="resignForm">
              <label class="resignForm-label">
                安全性情報<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
              ></label>
              <div class="resignForm-control">
                <ul class="resignForm-select mfsa-custom-tab-select">
                  <li v-for="item of safety_information" :key="item">
                    <button
                      type="button"
                      :class="safety_information_flag === item.safety_value ? 'active' : ''"
                      class="btn btn-select"
                      @click="safetyInformation(item)"
                    >
                      {{ item.safety_label }}
                    </button>
                  </li>
                </ul>
              </div>
            </div>
            <div class="invalid-feedback"></div>
            <div class="resignForm">
              <label class="resignForm-label">
                適応外情報<span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span
              ></label>
              <div class="resignForm-control">
                <ul class="resignForm-select mfsa-custom-tab-select">
                  <li v-for="item of not_applicable_information" :key="item">
                    <button
                      type="button"
                      :class="off_label_information_flag === item.not_applicable_value ? 'active' : ''"
                      class="btn btn-select"
                      @click="notApplicableInformation(item)"
                    >
                      {{ item.not_applicable_label }}
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="resignBtn text-center">
          <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeD01S03">キャンセル</button>
          <!-- v-if="documentId" -->
          <button v-if="activeUploadType" :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="deleteRecord">
            削除
          </button>
          <button :disabled="checkDisableBtn() || loading" type="button" class="btn btn-primary customBtn" @click="saveRecord">保存</button>
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
        :show-close="modalConfig.isShowClose"
      >
        <template #default>
          <component
            :is="modalConfig.component"
            v-bind="{ ...modalConfig.props }"
            @onFinishScreen="onResultModal"
            @handleConfirm="deleteDoc"
            @handleYes="handleYes"
          ></component>
        </template>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import D01_S03_ResignationService from '@/api/D01/D01_S03_ResignationService';
import { markRaw } from 'vue';
import Z05S05ChoiceOfMasterial from '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import { required } from '../../../utils/validate';
import validateMessages from '../../../utils/validateMessages/D01/D01_S03_Registration';
import axios from 'axios';
import { encodeData } from '@/api/base64_api';
export default {
  name: 'D01_S03_Registration',
  components: {
    Z05S05ChoiceOfMasterial,
    Z05_S06_Material_Selection,
    Z05_S01_ModalOrganization
  },
  props: {
    documentId: {
      type: Number,
      default: null
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      validateMessages,
      loading: false,
      checkBtnSave: false,
      checkBtn: false,
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        isShowClose: true,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      props: {
        userFlag: 0,
        mode: 'single',
        orgCdList: [],
        orgCd: '',
        userCdList: [],

        params: {
          subMaterialSelectableFlag: 0, // 1: allow select child material || 0: not allow  (require)
          customMaterialFlag: 0, // 1: allow choice カスタム資材 || 0: not allow (require)
          initMaterials: [],
          materialType: '10',
          document_name: '',
          productCode: '',
          medicalSubjectsGroupCode: '',
          safetyInformationFlag: '',
          offLabelInformationFlag: '',
          date: ''
        }
      },
      document_category: [],
      medical_subjects_group: [],
      not_applicable_information: [],
      safety_information: [],
      document_categoryI: '',
      medical_subjects_groupI: '',
      startDate: '',
      endDate: '',
      dataResultZ05S05: {
        document_id: '',
        document_name: ''
      },
      document_nameI: '',
      document_versionI: '',
      diseaseI: '',
      document_contentsI: '',
      dataProduct: {
        product_cd: '',
        product_name: ''
      },
      availabledepartments: true,
      checkAvailabledepartments: false,
      dataResultZ05S01: {
        org_cd: '',
        org_name: '',
        org_label: ''
      },
      safety_information_flag: '',
      off_label_information_flag: '',
      file_name: '',
      file: null,
      typeFile: 0,
      activeUploadType: false,
      fileS3: {},
      dataTheFirst: {},
      loadingDownload: false,
      errValidateFile: ''
    };
  },
  computed: {
    validation() {
      return {
        document_nameI: { required: required(this.document_nameI) },
        document_versionI: { required: required(this.document_versionI) },
        document_categoryI: { required: required(this.document_categoryI) },
        release_start_date: { required: required(this.startDate) },
        release_end_date: { required: required(this.endDate) },
        dataProduct: { required: required(this.dataProduct.product_name) }
      };
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '資材登録',
      icon: 'Material-icon.svg',
      isShowBack: true
    });
    if (this.documentId || this._route('D01_S03_Registration') ? this._route('D01_S03_Registration').params.document_id : '') {
      this.checkBtn = true;
    }
    this.getScreenData();
  },
  methods: {
    checkDateNull() {
      if (!this.startDate) {
        this.startDate = '';
      }

      if (!this.endDate) {
        this.endDate = '';
      }
      this.changeInputSave();
    },
    changeInputSave() {
      const data = {
        document_nameI: this.document_nameI,
        document_versionI: this.document_versionI,
        file_name: this.file_name,
        dataResultZ05S05Document_id: this.dataResultZ05S05.document_id,
        startDate: this.startDate,
        endDate: this.endDate,
        diseaseI: this.diseaseI,
        document_categoryI: this.document_categoryI,
        document_contentsI: this.document_contentsI,
        dataProductProduct_name: this.dataProduct.product_name,
        dataResultZ05S01Org_name: this.dataResultZ05S01.org_name,
        medical_subjects_groupI: this.medical_subjects_groupI,
        safety_information_flag: +this.safety_information_flag,
        off_label_information_flag: +this.off_label_information_flag,
        availabledepartments: this.availabledepartments
      };
      if (!this.documentId || !this._route('D01_S03_Registration').params.document_id) {
        if (
          !data.availabledepartments ||
          data.dataProductProduct_name !== '' ||
          data.dataResultZ05S01Org_name !== '' ||
          data.dataResultZ05S05Document_id !== '' ||
          data.diseaseI !== '' ||
          data.document_categoryI !== '' ||
          data.document_contentsI !== '' ||
          data.document_nameI !== '' ||
          data.document_versionI !== '' ||
          data.endDate !== '' ||
          data.file_name !== '' ||
          data.medical_subjects_groupI !== '' ||
          data.off_label_information_flag !== 0 ||
          data.safety_information_flag !== 0 ||
          data.startDate !== ''
        ) {
          localStorage.setItem('checkChangTab', true);
        } else {
          localStorage.removeItem('checkChangTab');
        }
      } else {
        if (
          this.dataTheFirst.document_name === data.document_nameI &&
          this.dataTheFirst.document_version === data.document_versionI &&
          (this.dataTheFirst.parent_document_id ? this.dataTheFirst.parent_document_id : this.dataTheFirst.document_id) === data.dataResultZ05S05Document_id &&
          this.dataTheFirst.start_date === data.startDate &&
          this.dataTheFirst.end_date === data.endDate &&
          this.dataTheFirst.disease === data.diseaseI &&
          this.dataTheFirst.document_category_cd === data.document_categoryI &&
          (this.dataTheFirst.document_contents ? this.dataTheFirst.document_contents : '') === data.document_contentsI &&
          this.dataTheFirst.product_name === data.dataProductProduct_name &&
          this.dataTheFirst.org_name === data.dataResultZ05S01Org_name &&
          this.dataTheFirst.medical_subjects_group_cd === data.medical_subjects_groupI &&
          this.dataTheFirst.off_label_information_flag === data.off_label_information_flag &&
          this.dataTheFirst.safety_information_flag === data.safety_information_flag &&
          (this.dataTheFirst.available_org_cd ? true : false) === data.availabledepartments
        ) {
          localStorage.removeItem('checkChangTab');
        } else {
          localStorage.setItem('checkChangTab', true);
        }
      }
    },
    canleBtn() {
      this.back({ document_id: this.documentId || this.$route.query.document_id });
    },
    validateDocumentContents() {
      let document_contentsI = this.document_contentsI ? this.document_contentsI : '';
      if (document_contentsI.length > 300) {
        return true;
      }
      return false;
    },
    validateDocumentNameI() {
      let document_nameI = this.document_nameI ? this.document_nameI : '';
      if (document_nameI.length > 50) {
        return true;
      }
      return false;
    },
    validateDocumentVersionI() {
      let document_versionI = this.document_versionI ? this.document_versionI : '';
      if (document_versionI.length > 20) {
        return true;
      }
      return false;
    },
    validatediseaseI() {
      let diseaseI = this.diseaseI ? this.diseaseI : '';
      if (diseaseI.length > 50) {
        return true;
      }
      return false;
    },
    checkDisableBtn() {
      if (this.validateDocumentContents() || this.validateDocumentNameI() || this.validateDocumentVersionI() || this.validatediseaseI()) {
        return true;
      }
      return false;
    },
    validFile() {
      this.errValidateFile = '';
      const element = document.getElementById('upload-file');
      const file = element.files[0];
      this.checkBtn = false;
      if (!file) return true;
      if (file.type === 'video/mp4') {
        if (!this.document_category.includes('90')) {
          this.document_category = this.document_category.filter((s) => s.document_category_cd !== '90');
        }
        this.file = file;
        this.file_name = file.name;
        this.typeFile = 20;
      } else {
        let checkArr = 0;
        this.document_category.forEach((element) => {
          if (element.document_category_cd !== '90') {
            checkArr = checkArr + 1;
          }
        });
        if (checkArr > 0) {
          this.document_category = this.document_category.filter((s) => s.document_category_cd !== '90');
          this.document_category.push({ document_category_cd: '90', document_category_name: '表紙・目次' });
        }
        this.file = file;
        this.file_name = file.name;
        this.typeFile = 10;
      }
      this.fileS3 = {};
      const isSize = file.size / 1024 / 1024 < 100;
      const isType = this.document_categoryI === '90' ? file.type === 'application/pdf' : file.type === 'video/mp4' || file.type === 'application/pdf';
      if (!isType) {
        if (this.document_categoryI === '90') {
          this.$notify({ message: '画像の資材では資材種別を表紙・目次として使用できません。', customClass: 'error' });
          this.errValidateFile = '画像の資材では資材種別を表紙・目次として使用できません。';
        } else {
          this.$notify({ message: this.getMessage('MSFA0038', '「PDF、動画　（MP4）」'), customClass: 'error' });
          this.errValidateFile = '拡張子が「PDF、動画　（MP4）」のファイルのみ有効です。';
        }
        return false;
      }
      if (!isSize) {
        this.$notify({ message: this.getMessage('MFSA0020', '100MB'), customClass: 'error' });
        this.errValidateFile = 'ファイルサイズが大きすぎます。アップロードできるファイルは、最大100MBバイト以内でお願いします';
        return false;
      }

      return isType && isSize;
    },
    handleChooseFile() {
      this.$refs.fileUploadD01S03.click();
    },
    handleRemoveUserZ05S05() {
      this.dataResultZ05S05.document_id = '';
      this.dataResultZ05S05.document_name = '';
      this.changeInputSave();
    },
    handleRemoveZ05S06() {
      this.dataProduct.product_cd = '';
      this.dataProduct.product_name = '';
      this.changeInputSave();
    },
    checkkend() {
      if (this.availabledepartments) {
        this.handleRemoveZ05S01();
      }
      this.changeInputSave();
    },
    handleRemoveZ05S01() {
      this.dataResultZ05S01.org_cd = '';
      this.dataResultZ05S01.org_name = '';
      this.dataResultZ05S01.org_label = '';
      this.changeInputSave();
    },
    notApplicableInformation(item) {
      this.off_label_information_flag = item.not_applicable_value;
      this.changeInputSave();
    },
    safetyInformation(item) {
      this.safety_information_flag = item.safety_value;
      this.changeInputSave();
    },
    getScreenData() {
      D01_S03_ResignationService.getRegistration().then((res) => {
        this.document_category = res.data.data.document_category;
        this.medical_subjects_group = res.data.data.medical_subjects_group;
        this.not_applicable_information = res.data.data.not_applicable_information;
        this.safety_information = res.data.data.safety_information;
        this.safety_information_flag = res.data.data.safety_information[0].safety_value;
        this.off_label_information_flag = res.data.data.not_applicable_information[0].not_applicable_value;
        if (this.documentId || this._route('D01_S03_Registration') ? this._route('D01_S03_Registration').params.document_id : '') {
          this.getDocumentById();
          this.activeUploadType = true;
        } else {
          this.activeUploadType = false;
        }
      });
    },
    getDocumentById() {
      this.fileS3 = {};
      D01_S03_ResignationService.getRegistrationDetail({
        document_id: this.documentId || this._route('D01_S03_Registration') ? this._route('D01_S03_Registration').params.document_id : ''
      }).then((res) => {
        this.dataTheFirst = res.data.data;
        this.fileS3 = res.data.data;
        this.file_name = res.data.data.display_name;
        this.document_nameI = res.data.data.document_name;
        this.document_versionI = res.data.data.document_version;
        this.diseaseI = res.data.data.disease;
        this.startDate = res.data.data.start_date;
        this.endDate = res.data.data.end_date === '9999-12-31' ? '' : res.data.data.end_date;
        this.document_categoryI = res.data.data.document_category_cd;
        this.document_contentsI = res.data.data.document_contents ? res.data.data.document_contents : '';
        this.medical_subjects_groupI = res.data.data.medical_subjects_group_cd;
        this.safety_information_flag = res.data.data.safety_information_flag.toString();
        this.off_label_information_flag = res.data.data.off_label_information_flag.toString();
        this.availabledepartments = res.data.data.available_org_cd ? true : false;
        this.dataResultZ05S01.org_cd = res.data.data.org_cd ? res.data.data.org_cd : res.data.data.available_org_cd;
        this.dataResultZ05S01.org_name = res.data.data.org_name;
        this.dataResultZ05S01.org_label = res.data.data.org_label;
        this.dataProduct.product_cd = res.data.data.product_cd;
        this.dataProduct.product_name = res.data.data.product_name;
        this.dataResultZ05S05.document_id = res.data.data.parent_document_id ? res.data.data.parent_document_id : res.data.data.document_id;
        this.dataResultZ05S05.document_name = res.data.data.parent_document_name ? res.data.data.parent_document_name : res.data.data.document_name;

        if (this.file_name.split('.').includes('mp4') || this.file_name.split('.').includes('m4v')) {
          this.document_category = this.document_category.filter((res) => res.document_category_cd !== '90');
        }
      });
    },
    downloadFileS3() {
      if (this.checkBtn) {
        this.loadingDownload = true;
        if (this.fileS3.file_id) {
          axios({
            url: `${this.fileS3.file_name}`,
            method: 'GET',
            responseType: 'blob' // important
          })
            .then((response) => {
              const url = window.URL.createObjectURL(new Blob([response.data]));
              const link = document.createElement('a');
              link.href = url;
              link.setAttribute('download', `${this.fileS3.display_name}`); //or any other extension
              document.body.appendChild(link);
              link.click();
              this.loadingDownload = false;
            })
            .catch(async (err) => {
              let blob = err.response.data;
              let responseText = JSON.parse(await blob.text());
              this.$notify({ message: err.response.data.message || responseText.message, customClass: 'error' });
            })
            .finally(async () => {
              this.loadingDownload = false;
            });
        }
      }
    },
    disabledDateStart(time) {
      if (this.endDate) {
        return time.getTime() > new Date(this.endDate).getTime();
      }
    },
    disabledDateEnd(time) {
      if (this.startDate) {
        return time.getTime() < new Date(this.startDate).getTime();
      }
    },
    openZ05S05() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材選択',
        customClass: 'custom-dialog custom-dialog-pd-none',
        isShowModal: true,
        component: markRaw(Z05S05ChoiceOfMasterial),
        width: '70rem',
        props: {
          mode: this.props.mode,
          subMaterialSelectableFlag: 0,
          customMaterialFlag: 0,
          params: {
            ...this.props.params,
            initMaterials: [this.dataResultZ05S05.document_id],
            materialType: '10'
          }
        }
      };
    },
    onResultModal(data) {
      if (data?.screen === 'Z05_S01') {
        this.dataResultZ05S01.org_cd = data.orgSelected[0].org_cd;
        this.dataResultZ05S01.org_name = data.orgSelected[0].org_name;
        this.dataResultZ05S01.org_label = data.orgSelected[0].org_label;
        data.orgSelected?.forEach((x) => {
          this.props.orgCdList.push(x.org_cd);
        });
        this.changeInputSave();
      }
      if (data?.screen === 'Z05_S05') {
        this.dataResultZ05S05.document_id = data.dataMasterialSelected[0].document_id;
        this.dataResultZ05S05.document_name = data.dataMasterialSelected[0].document_name;
        this.props.params = { ...this.props.params, ...data.filterData };
        this.changeInputSave();
      }
      if (data?.screen === 'Z05_S06') {
        this.props.initDataCodes = [];
        this.props.classificationCode = data.classifications?.product_group_cd;
        this.props.categoryCode = data.category?.definition_value;
        data.dataSelected?.forEach((x) => {
          this.props.initDataCodes.push(x.product_cd);
        });
        this.dataProduct.product_cd = data.dataSelected[0].product_cd;
        this.dataProduct.product_name = data.dataSelected[0].product_name;
        this.changeInputSave();
      }
      this.onCloseModal();
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        component: markRaw(Z05_S06_Material_Selection),
        width: '33rem',
        props: {
          mode: this.props.mode,
          categoryCode: this.props.categoryCode,
          classificationCode: this.props.classificationCode,
          initDataCodes: this.props.initDataCodes
        }
      };
    },
    openModal_Z05_S01() {
      if (this.availabledepartments) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '組織選択',
          isShowModal: true,
          customClass: 'custom-dialog modal-fixed',
          component: markRaw(Z05_S01_ModalOrganization),
          width: '45rem',
          props: {
            mode: this.props.mode,
            userFlag: this.userFlag1,
            orgCdList: this.props.orgCdList,
            orgCd: this.props.orgCd,
            userCdList: this.props.userCdList
          }
        };
      }
    },
    closeD01S03() {
      const data = {
        document_nameI: this.document_nameI,
        document_versionI: this.document_versionI,
        file_name: this.file_name,
        dataResultZ05S05Document_id: this.dataResultZ05S05.document_id,
        startDate: this.startDate,
        endDate: this.endDate,
        diseaseI: this.diseaseI,
        document_categoryI: this.document_categoryI,
        document_contentsI: this.document_contentsI,
        dataProductProduct_name: this.dataProduct.product_name,
        dataResultZ05S01Org_name: this.dataResultZ05S01.org_name,
        medical_subjects_groupI: this.medical_subjects_groupI,
        safety_information_flag: +this.safety_information_flag,
        off_label_information_flag: +this.off_label_information_flag,
        availabledepartments: this.availabledepartments
      };
      if (!this.documentId || !this._route('D01_S03_Registration').params.document_id) {
        if (
          !data.availabledepartments ||
          data.dataProductProduct_name !== '' ||
          data.dataResultZ05S01Org_name !== '' ||
          data.dataResultZ05S05Document_id !== '' ||
          data.diseaseI !== '' ||
          data.document_categoryI !== '' ||
          data.document_contentsI !== '' ||
          data.document_nameI !== '' ||
          data.document_versionI !== '' ||
          data.endDate !== '' ||
          data.file_name !== '' ||
          data.medical_subjects_groupI !== '' ||
          data.off_label_information_flag !== 0 ||
          data.safety_information_flag !== 0 ||
          data.startDate !== ''
        ) {
          localStorage.setItem('checkChangTab', true);
          this.modalConfig = {
            ...this.modalConfig,
            isShowModal: true,
            component: this.markRaw(this.$PopupConfirm),
            width: '35rem',
            customClass: 'custom-dialog modal-fixed modal-fixed-min',
            props: { mode: 1, textCancel: 'いいえ' },
            isShowClose: false
          };
        } else {
          this.canleBtn();
          localStorage.removeItem('checkChangTab');
        }
      } else {
        if (
          this.dataTheFirst.document_name === data.document_nameI &&
          this.dataTheFirst.document_version === data.document_versionI &&
          (this.dataTheFirst.parent_document_id ? this.dataTheFirst.parent_document_id : this.dataTheFirst.document_id) === data.dataResultZ05S05Document_id &&
          this.dataTheFirst.start_date === data.startDate &&
          this.dataTheFirst.end_date === data.endDate &&
          this.dataTheFirst.disease === data.diseaseI &&
          this.dataTheFirst.document_category_cd === data.document_categoryI &&
          (this.dataTheFirst.document_contents ? this.dataTheFirst.document_contents : '') === data.document_contentsI &&
          this.dataTheFirst.product_name === data.dataProductProduct_name &&
          this.dataTheFirst.org_name === data.dataResultZ05S01Org_name &&
          this.dataTheFirst.medical_subjects_group_cd === data.medical_subjects_groupI &&
          this.dataTheFirst.off_label_information_flag === data.off_label_information_flag &&
          this.dataTheFirst.safety_information_flag === data.safety_information_flag &&
          (this.dataTheFirst.available_org_cd ? true : false) === data.availabledepartments
        ) {
          this.canleBtn();
          localStorage.removeItem('checkChangTab');
        } else {
          localStorage.setItem('checkChangTab', true);
          this.modalConfig = {
            ...this.modalConfig,
            isShowModal: true,
            component: this.markRaw(this.$PopupConfirm),
            width: '35rem',
            customClass: 'custom-dialog modal-fixed modal-fixed-min',
            props: { mode: 1, textCancel: 'いいえ' },
            isShowClose: false
          };
        }
      }
    },
    handleYes() {
      this.canleBtn();
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    saveRecord() {
      this.checkAvailabledepartments = false;
      // if (!this.checkValidate()) return;
      if (!this.checkValidate() || (this.availabledepartments && (this.dataResultZ05S01.org_name === '' || this.dataResultZ05S01.org_name === null))) {
        if (this.availabledepartments && (this.dataResultZ05S01.org_name === '' || this.dataResultZ05S01.org_name === null)) {
          this.checkAvailabledepartments = true;
        }
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      if (!this.validFile()) return;
      this.loading = true;
      let formData = new FormData();
      formData.append('file_type', encodeData(this.fileS3.file_type ? this.fileS3.file_type : this.typeFile));
      formData.append('file_id', encodeData(this.fileS3.file_id ? this.fileS3.file_id : ''));
      formData.append('document_file', this.file ? this.file : '');
      formData.append('document_id', encodeData(this._route('D01_S03_Registration') ? this._route('D01_S03_Registration').params.document_id : ''));
      formData.append('document_name', encodeData(this.document_nameI));
      formData.append('document_contents', encodeData(this.document_contentsI));
      formData.append('document_version', encodeData(this.document_versionI));
      formData.append('parent_document_id', encodeData(this.dataResultZ05S05.document_id));
      formData.append('start_date', encodeData(this.formatFullDate(this.startDate)));
      formData.append('end_date', encodeData(this.endDate === '' ? '9999-12-31' : this.formatFullDate(this.endDate)));
      formData.append('available_org_cd', encodeData(this.dataResultZ05S01.org_cd ? this.dataResultZ05S01.org_cd : ''));
      formData.append('org_name', encodeData(this.dataResultZ05S01.org_name));
      formData.append('org_label', encodeData(this.dataResultZ05S01.org_label));
      formData.append('product_cd', encodeData(this.dataProduct.product_cd));
      formData.append('disease', encodeData(this.diseaseI));
      formData.append('medical_subjects_group_cd', encodeData(this.medical_subjects_groupI));
      formData.append('document_category_cd', encodeData(this.document_categoryI));
      formData.append('safety_information_flag', encodeData(this.safety_information_flag));
      formData.append('off_label_information_flag', encodeData(this.off_label_information_flag));

      D01_S03_ResignationService.saveRegistration(formData)
        .then((res) => {
          if (res) {
            this.$notify({ message: res.data.message, customClass: 'success' });
            this.canleBtn();
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          this.loading = false;
        });
    },
    deleteRecord() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '40rem',
        customClass: 'custom-dialog modaldelete modal-fixed',
        props: { title: 'この資材を完全に削除しますか？' }
      };
    },
    deleteDoc() {
      const data = {
        document_id: this.documentId || this._route('D01_S03_Registration') ? this._route('D01_S03_Registration').params.document_id : ''
      };
      D01_S03_ResignationService.deleteRegistration(data)
        .then(() => {
          this.$notify({ message: '正常に削除しました。', customClass: 'success' });
          this.$router.push({ name: 'D01_S01_MaterialSearch' });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.modalConfig = {
            ...this.modalConfig,
            title: null,
            isShowModal: false,
            component: markRaw(this.$PopupConfirm),
            width: '40rem',
            customClass: 'custom-dialog modaldelete modal-fixed',
            props: { title: 'この資材を完全に削除しますか？' }
          };
        });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.cursor-pointer {
  cursor: pointer;
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
.required-start {
  line-height: 18px;
  min-width: 9px;
  margin: 0 0 0 8px;
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: auto;
  min-height: 18px;
}
.custom-btn {
  border-radius: 24px;
}
.file-name {
  margin-left: 8px;
  font-size: 14px;
  color: #448add;
  text-overflow: ellipsis;
  overflow: hidden;
  width: calc(100% - 145px);
  white-space: nowrap;
}
.icon-custom {
  border-left: none !important;
  left: 5px;
  width: 25px;
}
.groupResign {
  height: 100%;
  .resignWrap {
    background: #fff;
    height: 100%;
    padding: 0 28px 10px;
  }
  .resignRow {
    margin-left: -5%;
    display: flex;
    flex-wrap: wrap;
    @media (max-width: $viewport_tablet) {
      margin-left: -2%;
    }
    .resignCol {
      width: 50%;
      padding-left: 5%;
      @media (max-width: $viewport_tablet) {
        padding-left: 2%;
      }
    }
  }
  .resignForm {
    margin-top: 7px;
    .resignForm-label {
      font-size: 1rem;
      .resignForm-item {
        margin-left: 5px;
      }
    }
    .resignUpload {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      .resignUpload-btn {
        width: 140px;
        .btn {
          height: 32px;
          padding: 0 12px;
          .btn-iconLeft {
            margin-right: 3px;
          }
        }
      }
      .resignUpload-txt {
        width: calc(100% - 140px);
        padding-left: 8px;
        color: #448add;
        p {
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
        }
      }
    }
    .resignForm-control {
      > .resignForm-select {
        display: flex;
        flex-wrap: wrap;
        margin-left: 1px;
        width: 100%;
        > li {
          min-width: 150px;
          margin-left: -1px;
          &:first-of-type {
            .btn {
              border-radius: 4px 0 0 4px;
            }
          }
          &:last-child {
            .btn {
              border-radius: 0 4px 4px 0;
            }
          }
          .btn {
            width: 100%;
            border-radius: 0;
          }
        }
      }
    }
  }
  .resignForm-group {
    display: flex;
    flex-wrap: wrap;
    .col1 {
      width: calc(100% - 200px);
      padding-right: 12px;
    }
    .col2 {
      width: 200px;
    }
  }
  .resignForm-calendar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    .form-icon {
      width: calc(50% - 20px);
    }
    .resignForm-item {
      width: 40px;
      text-align: center;
      font-size: 1rem;
    }
  }
  .organizations {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    .organizations-checkBox {
      width: 80px;
    }
    .form-icon {
      width: calc(100% - 80px);
      padding-left: 10px;
    }
  }
  .resignBtn {
    margin-top: 15px;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.lds-ellipsis {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 18px;
}
.lds-ellipsis div {
  position: absolute;
  top: 3px;
  width: 13px;
  height: 13px;
  border-radius: 50%;
  background: #b7c3cb;
  animation-timing-function: cubic-bezier(0, 1, 1, 0);
}
.lds-ellipsis div:nth-child(1) {
  left: 8px;
  animation: lds-ellipsis1 0.6s infinite;
}
.lds-ellipsis div:nth-child(2) {
  left: 8px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(3) {
  left: 32px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(4) {
  left: 56px;
  animation: lds-ellipsis3 0.6s infinite;
}
@keyframes lds-ellipsis1 {
  0% {
    transform: scale(0);
  }
  100% {
    transform: scale(1);
  }
}
@keyframes lds-ellipsis3 {
  0% {
    transform: scale(1);
  }
  100% {
    transform: scale(0);
  }
}
@keyframes lds-ellipsis2 {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(24px, 0);
  }
}
</style>
