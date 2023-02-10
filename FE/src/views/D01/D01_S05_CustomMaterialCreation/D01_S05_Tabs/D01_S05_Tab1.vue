<template>
  <div class="tab01_container">
    <el-form label-position="left" class="card-from scrollbar">
      <div class="form-left">
        <div class="basicGroup-form">
          <div class="basicForm">
            <el-form-item>
              <label class="basicForm-label">
                資材名
                <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
              </label>
              <div class="basicForm-control" :class="isSubmit && !validation.document_name.required ? 'hasErr' : ''">
                <el-input v-model="body.document_name" clearable placeholder="資材名入力" class="form-control-input" @input="emitData()" />
              </div>
            </el-form-item>
            <div class="invalid-feedback" :style="isSubmit && !validation.document_name.required ? 'display: block' : 'display: none'">
              <span>入力してください。</span>
            </div>
          </div>
          <div class="basicForm m-t-10 min-h-150">
            <el-form-item>
              <label class="basicForm-label">
                品目
                <span class="required-start"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
              </label>
              <div class="basicForm-control">
                <div class="form-icon icon-right" :class="isSubmit && !validation.product_name.required ? 'hasErr' : ''">
                  <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                  </span>
                  <!--QC Note-->
                  <div v-if="body.product_name" class="form-control d-flex align-items-center">
                    <div class="block-tags">
                      <el-tag
                        class="el-tag-custom el-tag-icon-top"
                        closable
                        @close="
                          body.product_name = '';
                          body.product_cd = '';
                          emitData();
                        "
                      >
                        {{ body.product_name }}
                      </el-tag>
                    </div>
                  </div>
                  <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                </div>
              </div>
            </el-form-item>
            <div class="invalid-feedback" :style="isSubmit && !validation.product_name.required ? 'display: block' : 'display: none'">
              <span>
                {{ getMessage('MSFA0040', '品目') }}
              </span>
            </div>
          </div>
          <div class="basicForm min-h-150">
            <el-form-item>
              <label class="basicForm-label"> 対象疾患 </label>
              <div class="basicForm-control">
                <el-input v-model="body.disease" clearable placeholder="対象疾患を入力" class="form-control-input" @input="emitData()" />
              </div>
            </el-form-item>
          </div>
          <div class="basicForm basicForm-last-child min-h-150">
            <el-form-item>
              <label class="basicForm-label"> 診療科目分類 </label>
              <div class="basicForm-control">
                <el-select
                  v-model="body.medical_subjects_group_cd"
                  placeholder="未選択"
                  class="form-control-select selectpicker select-control"
                  @change="emitData()"
                >
                  <el-option label="" value=""> 未選択</el-option>
                  <el-option
                    v-for="item in datamap.medical_subject_groups"
                    :key="item?.medical_subjects_group_cd"
                    :label="item.medical_subjects_group_name"
                    :value="item?.medical_subjects_group_cd"
                  >
                    {{ item.medical_subjects_group_name }}
                  </el-option>
                </el-select>
              </div>
            </el-form-item>
          </div>
        </div>
      </div>
      <div class="form-right">
        <div class="basicGroup-form form--left">
          <div class="basicForm-col2">
            <ul>
              <li><label class="text-base"> 安全性情報 </label></li>
              <li><label class="text-base"> 適応外情報 </label></li>
              <li>
                <div class="basicForm">
                  <div class="basicForm-control">
                    <ul class="basicForm-btnSelect basicForm-btnSelect--col2 mfsa-custom-tab-select">
                      <li>
                        <button
                          type="button"
                          :class="['btn btn-select text-base', body.safety_information_flag == 1 ? 'active' : '']"
                          @click.prevent="choseDefinitions('1', 'safety_information_flag')"
                        >
                          あり
                        </button>
                      </li>
                      <li>
                        <button
                          type="button"
                          :class="['btn btn-select text-base', body.safety_information_flag == 0 ? 'active' : '']"
                          @click.prevent="choseDefinitions('0', 'safety_information_flag')"
                        >
                          なし
                        </button>
                      </li>
                    </ul>
                  </div>
                  <div
                    class="invalid-feedback"
                    :style="isSubmit && !validation.safety_information_flag.required ? 'display: block; padding-top: 5px' : 'display: none'"
                  >
                    <span>
                      {{ getMessage('MSFA0040', '安全性情報') }}
                    </span>
                  </div>
                </div>
              </li>
              <li>
                <div class="basicForm">
                  <div class="basicForm-control">
                    <ul class="basicForm-btnSelect basicForm-btnSelect--col2 mfsa-custom-tab-select">
                      <li>
                        <button
                          type="button"
                          :class="['btn btn-select text-base', body.off_label_information_flag == 1 ? 'active' : '']"
                          @click.prevent="choseDefinitions('1', 'off_label_information_flag')"
                        >
                          あり
                        </button>
                      </li>
                      <li>
                        <button
                          type="button text-base"
                          :class="['btn btn-select text-base', body.off_label_information_flag == 0 ? 'active' : '']"
                          @click.prevent="choseDefinitions('0', 'off_label_information_flag')"
                        >
                          なし
                        </button>
                      </li>
                    </ul>
                  </div>
                  <div
                    class="invalid-feedback"
                    :style="isSubmit && !validation.off_label_information_flag.required ? 'display: block;  padding-top: 5px' : 'display: none'"
                  >
                    <span>
                      {{ getMessage('MSFA0040', '適応外情報') }}
                    </span>
                  </div>
                </div>
              </li>
            </ul>
            <div
              v-if="validation.off_label_information_flag.required || validation.safety_information_flag.required"
              class="invalid-feedback"
              :style="'display: block'"
            ></div>
          </div>
          <div class="basicForm m-t-10">
            <el-form-item>
              <label class="basicForm-label"> 備考 </label>
              <div class="basicForm-control">
                <el-input
                  v-model="body.document_contents"
                  class="form-control-textarea custom-document-creation"
                  type="textarea"
                  placeholder="備考を入力"
                  @input="emitData()"
                />
              </div>
            </el-form-item>
          </div>
        </div>
      </div>
    </el-form>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog modal-fixed modal-fixed-min"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="true"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
    </template>
  </el-dialog>
</template>
<script>
import D01_S05_CustomMaterialCreationServices from '@/api/D01/D01_S05_CustomMaterialCreationService';
import { markRaw } from 'vue';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import { isEqual } from 'lodash';

export default {
  name: 'Tab1',
  props: {
    custom_document: {
      type: Object,
      default: null,
      required: false
    },
    validation: {
      type: Object,
      default: null,
      required: false
    },
    isSubmit: {
      type: Boolean,
      default: null,
      required: false
    },
    changetab: {
      type: String,
      default: '',
      required: false
    },
    type_modal: {
      type: String,
      default: '',
      required: false
    }
  },
  emits: ['onSenData'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      medical_subject_groups: [],
      variable_definitions: [],
      firstBody: '',
      body: {
        document_name: '',
        product_cd: '',
        product_name: '',
        document_contents: '',
        disease: '',
        medical_subjects_group_cd: '',
        safety_information_flag: '0',
        off_label_information_flag: '0'
      },
      datamap: {
        medical_subject_groups: [],
        products: [],
        variable_definitions: []
      },
      disuse_flag: 0,
      paramsZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      }
    };
  },
  watch: {
    custom_document() {
      this.body = this.custom_document;

      this.body.product_name = this.datamap?.products.find((s) => s?.product_cd === this.body.product_cd)?.product_name;
      this.emitData();
    }
  },

  mounted() {
    this.body = {
      ...this.custom_document
    };
    this.getScreenData();
    if (this.type_modal === 'create') {
      this.firstBody = {
        document_name: '',
        product_cd: '',
        product_name: '',
        document_contents: '',
        disease: '',
        medical_subjects_group_cd: '',
        safety_information_flag: '0',
        off_label_information_flag: '0'
      };
    }
  },
  methods: {
    emitData() {
      const params = {
        document_name: this.body.document_name,
        product_cd: this.body.product_cd,
        product_name: this.body.product_name,
        document_contents: this.body.document_contents,
        disease: this.body.disease,
        medical_subjects_group_cd: this.body.medical_subjects_group_cd ? this.body.medical_subjects_group_cd : '',
        safety_information_flag: this.body.safety_information_flag?.toString() ? this.body.safety_information_flag?.toString() : null,
        off_label_information_flag: this.body.off_label_information_flag?.toString() ? this.body.off_label_information_flag?.toString() : null
      };
      this.$emit('onSenData', params);
      if (!this.firstBody) {
        this.firstBody = params;
      }

      if (!isEqual(params, this.firstBody)) {
        localStorage.setItem('checkChangTab', true);
      } else {
        localStorage.removeItem('checkChangTab');
      }
    },
    choseDefinitions(value, type) {
      this.body[type] = value;
      this.emitData();
    },
    getTypeInformation_flag(index) {
      // eslint-disable-next-line eqeqeq
      return index % 2 == 0 ? 'off_label_information_flag' : 'safety_information_flag';
    },
    // tab1
    getScreenData() {
      D01_S05_CustomMaterialCreationServices.getScreenData()
        .then((res) => {
          let data = res?.data?.data;
          if (data) {
            this.datamap.medical_subject_groups = data.medical_subject_groups;
            (this.datamap.products = data.products), (this.datamap.variable_definitions = data.variable_definitions);
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
      setTimeout(() => {
        if (!this.body.medical_subjects_group_cd) {
          this.body.medical_subjects_group_cd = '';
        }
      }, 1000);
    },
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05S06MaterialSelection),
        width: '33rem',
        props: {
          ...this.paramsZ05S06,
          initDataCodes: [this.body.product_cd]
        }
      };
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    onResultModal(e) {
      // eslint-disable-next-line eqeqeq
      if (e?.screen == 'Z05_S06') {
        this.paramsZ05S06 = {
          ...this.paramsZ05S06,
          categoryCode: e.category.definition_value,
          classificationCode: e.classifications.product_group_cd
        };

        this.body.product_cd = e.dataSelected[0].product_cd;
        this.body.product_name = e.dataSelected[0].product_name;
        this.emitData();
        this.onCloseModal();
      }
      this.onCloseModal();
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.el-form-item {
  margin-bottom: 5px;
}
.tab01_container {
  position: absolute;
  background-color: #ffffff;
  width: 100%;
  height: 100%;
  padding: 20px 20px;
  @media (min-width: $viewport_desktop) {
    padding: 30px 40px;
  }
  .card-from {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    height: 100%;
    @media (max-width: $viewport_tablet) {
      flex-direction: column;
      flex-wrap: nowrap;
      .form-left,
      .form-right {
        width: 100% !important;
      }
    }
    .form-left {
      width: 40%;
      height: 100%;
    }
    .form-right {
      width: 60%;
      height: 100%;
      padding-left: 25px;

      @media (max-width: $viewport_tablet) {
        padding-top: 10px;
        padding-left: 0;
      }
    }
    .basicGroup-form {
      .basicForm-col2 {
        height: 90px;
        > ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: -24px;
          > li {
            padding-left: 24px;
            width: 50%;
          }
        }
      }
      .basicForm {
        min-height: 90px;
        .basicForm-label {
          font-size: 1rem;
          .asterisk {
            display: inline-flex;
            margin: 8px 0 0 8px;
          }
          &--switch {
            margin-bottom: 7px;
            font-size: 1rem;
          }
        }

        .basicForm-control {
          .dateTime {
            display: flex;
            flex-wrap: wrap;
            margin-left: -24px;
            li {
              padding-left: 24px;
              width: 50%;
            }
          }
        }
      }
      .basicForm-last-child {
        padding-top: 0px;
      }
      .basicForm-btnSelect {
        display: flex;
        flex-wrap: wrap;
        margin-left: 1px;
        &--col2 {
          li {
            width: 50%;
          }
        }
        &--col4 {
          li {
            width: 25%;
          }
        }
        &--col3-5 {
          li {
            width: 30%;
          }
        }
        li {
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
          margin-left: -1px;
          .btn {
            width: 100%;
            border-radius: 0;
            padding: 0;
          }
        }
      }
    }
  }
}
.invalid-feedback {
  width: 100%;
  margin-top: 0px;
  padding-left: 5px;
  height: 16px;
}

.m-t-10 {
  @media (min-width: $viewport_desktop) {
    margin-top: 30px;
  }
}

.min-h-150 {
  @media (min-width: $viewport_desktop) {
    min-height: 150px !important;
  }
}

.text-base {
  font-size: 1rem;
  line-height: 40px;
}
</style>
