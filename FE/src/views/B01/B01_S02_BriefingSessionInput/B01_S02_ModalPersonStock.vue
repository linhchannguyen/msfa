<template>
  <div class="wrapContent modal-body-B01S02-PersonStock">
    <div id="msfa-notify-B01S02-PersonStock"></div>
    <div class="groupMain">
      <div class="inFacility-row scrollbar">
        <div class="inFacility-left">
          <div class="inFacilityStock">
            <div class="inFacilityPersonStock-tbl table-hg100 scrollbar" :class="lstDataPerson.length == 0 ? 'height515' : ''">
              <table>
                <thead>
                  <tr>
                    <th>説明会出席者</th>
                  </tr>
                </thead>
                <tbody v-if="lstDataPerson.length > 0">
                  <tr v-for="person in getDataDisplayPerson" :key="person.person_cd" class="inFacilityStock-content">
                    <td>
                      <div class="stockContent">
                        <div class="inFacility-title">{{ person.facility_short_name }} {{ person.department_name }}</div>
                        <p class="inFacility-label">
                          <a v-if="person.other_person_flag == 0" class="inFacility-link"> {{ person.person_name }} </a>　
                          <span v-else class="text-name"> {{ person.person_name }} </span>　
                          <span class="inFacility-name">{{ person.display_position_name }}</span>
                        </p>
                      </div>
                      <div v-show="person.person_cd && person.other_person_flag == 0" class="stockBtn">
                        <button v-if="checkDataPersonInStock(person)" class="btn btn--icon" @click="removePersonStock(person)">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack01.svg')" alt="" />
                        </button>
                        <button v-else class="btn btn--icon" @click="addPersonStock(person)">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack.svg')" alt="" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <div v-else>
                  <EmptyData class="noDataPersonStock" />
                </div>
              </table>
            </div>
            <div v-if="lstDataPerson.length > 0" class="paginStock">
              <div class="pagination-cases">全 {{ lstDataPerson.length }} 件</div>
              <PaginationTable
                class="pagination-bord"
                :page-size="pagination.pageSize"
                :current-page="pagination.curentPage"
                :total="lstDataPerson.length"
                @current-change="handleCurrentChange"
              />
            </div>
          </div>
        </div>

        <div class="inFacility-right">
          <div class="titleStock">
            <h2 class="titleStock-tlt">ストック登録</h2>
          </div>
          <div class="contentStock">
            <div class="contentStock-box">
              <div class="contentStock-lst scrollbar">
                <ul v-if="lstDataPersonStock.length > 0">
                  <li v-for="person in lstDataPersonStock" :key="person.person_cd">
                    <div class="contentStock-info">
                      <p class="contentStock-tlt">{{ person.facility_short_name }} {{ person.department_name }}</p>
                      <p class="contentStock-txt">
                        <span class="contentStock-name">{{ person.person_name }}</span>
                        <span class="contentStock-span">{{ person.display_position_name }}</span>
                      </p>
                    </div>
                    <button class="btn btn--icon" @click="removePersonStock(person)">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                    </button>
                  </li>
                </ul>
                <div v-else>
                  <EmptyData class="noDataStock" thumb-margin-top="20px" />
                </div>
              </div>
              <div class="contentStock-form">
                <ul>
                  <li>
                    <label class="formLabel">
                      面談内容
                      <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /> </span>
                    </label>
                    <div class="formControl" :class="checkValidateForm('content_cd', '面談内容', 'MSFA0040') ? 'hasErr' : ''">
                      <el-select v-model="paramStock.content_cd" placeholder="未選択" class="form-control-select">
                        <el-option v-for="content in lstContent" :key="content.content_cd" :label="content.content_name" :value="content.content_cd">
                        </el-option>
                      </el-select>
                    </div>
                    <span v-if="checkValidateForm('content_cd', '面談内容', 'MSFA0040')" class="invalid">
                      {{ validationMessageForm('content_cd', '面談内容', 'MSFA0040') }}
                    </span>
                  </li>
                  <li>
                    <label class="formLabel">
                      品目
                      <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /> </span>
                    </label>
                    <div class="formControl">
                      <div class="form-icon icon-right" :class="checkValidateForm('product_cd', '品目', 'MSFA0040', 'array') ? 'hasErr' : ''">
                        <span class="icon icon--cursor" @click="openModalZ05S06()">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                        </span>
                        <div v-if="paramStock.product_cd.length > 0" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag
                              v-for="product in paramStock.product_cd"
                              :key="product.product_cd"
                              class="el-tag-custom el-tag-icon-top"
                              closable
                              @close="handleRemoveProduct(product)"
                            >
                              {{ product.product_name }}
                            </el-tag>
                          </div>
                        </div>
                        <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                      </div>
                    </div>
                    <span v-if="checkValidateForm('product_cd', '品目', 'MSFA0040', 'array')" class="invalid">
                      {{ validationMessageForm('product_cd', '品目', 'MSFA0040', 'array') }}
                    </span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="formBtn">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModalStock()">キャンセル</button>
        <el-button
          type="primary"
          class="btn btn-primary"
          :loading="processingRegister"
          :disabled="processingRegister || lstDataPersonStock.length === 0"
          @click.prevent="registerStock"
        >
          登録
        </el-button>
      </div>
    </div>
  </div>

  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog modal-fixed modal-fixed-min"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';

export default {
  name: 'B01_S02_ModalPersonStock',
  props: {
    briefingid: {
      type: String,
      required: true
    },

    lstDataPerson: {
      type: Array,
      required: true,
      defaul: []
    }
  },
  emits: ['onFinishScreen'],
  data: () => {
    return {
      processingRegister: false,

      paramStock: {
        facility_cd: [],
        person_cd: [],
        product_cd: [],
        content_cd: '',
        stock_type: '10',
        action_id: ''
      },

      lstDataPersonStock: [],

      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      pagination: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },

      paramsZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      isSubmitStock: false,

      lstContent: []
    };
  },
  computed: {
    getDataDisplayPerson() {
      let data = this.lstDataPerson.slice(
        this.pagination.pageSize * this.pagination.curentPage - this.pagination.pageSize,
        this.pagination.pageSize * this.pagination.curentPage
      );
      return data;
    }
  },
  mounted() {
    this.getDataContent();
    this.js_pscroll();
  },
  methods: {
    getDataContent() {
      A02_S03_StockManagementServices.getAllContentService()
        .then((res) => {
          this.lstContent = res.data.data;
          this.js_pscroll();
        })
        .catch((err) => {
          if (err.response?.data?.message) {
            this.$notify({ message: err.response?.data?.message, customClass: 'error' });
          }
        });
    },

    handleCurrentChange(page) {
      this.pagination.curentPage = page;
    },

    checkDataPersonInStock(item) {
      let checked = this.lstDataPersonStock.some((x) => x.person_cd === item.person_cd && x.facility_cd === item.facility_cd);

      return checked;
    },

    addPersonStock(item) {
      if (!this.checkDataPersonInStock(item)) {
        this.lstDataPersonStock.push(item);
      }
    },

    removePersonStock(item) {
      if (this.checkDataPersonInStock(item)) {
        this.lstDataPersonStock = this.lstDataPersonStock.filter((x) => x.person_cd !== item.person_cd);
      }
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
          initDataCodes: this.paramStock.product_cd?.map((x) => x.product_cd)
        }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected;
      this.paramStock.product_cd = data;

      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        categoryCode: e.category.definition_value,
        classificationCode: e.classifications.product_group_cd,
        initDataCodes: data?.map((x) => x.product_cd)
      };
    },

    handleRemoveProduct() {
      this.paramStock.product_cd = [];
    },

    registerStock() {
      this.isSubmitStock = true;
      this.processingRegister = true;
      let params = {
        ...this.paramStock,
        facility_cd: this.lstDataPersonStock.map((x) => x.facility_cd),
        person_cd: this.lstDataPersonStock.map((x) => x.person_cd),
        product_cd: this.paramStock.product_cd.map((x) => x.product_cd),
        action_id: this.briefingid
      };
      if (!this.checkValidMessageSum()) {
        A02_S03_StockManagementServices.AddStockService(params)
          .then((res) => {
            this.$notify({ message: res.data.message, customClass: 'success' });
            this.closeModalStock();
          })
          .catch((err) => {
            this.notifyModal({
              message: err.response.data.message,
              type: 'error',
              classParent: 'modal-body-B01S02-PersonStock',
              idNodeNotify: 'msfa-notify-B01S02-PersonStock'
            });
          })
          .finally(() => {
            this.processingRegister = false;
          });
      } else {
        this.notifyModal({
          message: '入力エラーを確認してください。',
          type: 'error',
          classParent: 'modal-body-B01S02-PersonStock',
          idNodeNotify: 'msfa-notify-B01S02-PersonStock'
        });
        setTimeout(() => {
          this.processingRegister = false;
        }, 300);
      }
    },

    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S06') {
          this.onResultModalZ05S06(e);
        }
      }
      this.onCloseModal();
    },

    // Validation
    validationMessageForm(field, name, msgNumber, type) {
      let data = this.paramStock[field];
      if (type === 'array') {
        if ((!data || (data && data.length === 0)) && this.isSubmitStock) {
          return this.getMessage(msgNumber, name);
        }
      } else {
        if (!data && data !== 0 && this.isSubmitStock) {
          return this.getMessage(msgNumber, name);
        }
      }
      return '';
    },

    checkValidateForm(field, name, msgNumber, type) {
      if (this.validationMessageForm(field, name, msgNumber, type)) {
        return true;
      }
      return false;
    },

    checkValidMessageSum() {
      let message = '';

      if (this.checkValidateForm('content_cd', '面談内容', 'MSFA0040')) {
        message = this.validationMessageForm('content_cd', '面談内容', 'MSFA0040');
      } else {
        if (this.checkValidateForm('product_cd', '品目', 'MSFA0040', 'array')) {
          message = this.validationMessageForm('product_cd', '品目', 'MSFA0040', 'array');
        }
      }

      return message;
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    closeModalStock() {
      let result = {
        screen: 'ModalStock'
      };
      this.$emit('onFinishScreen', result);
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;

.groupMain {
  padding-top: 32px;
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  .inFacility-row {
    height: 100%;
    display: flex;
    flex-wrap: wrap;
    overflow: hidden;
    .inFacility-left {
      width: 60%;
      padding: 0 22px 0 32px;
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 0 20px;
        height: initial;
      }
    }
    .inFacility-right {
      width: 40%;
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 0 10px 0 10px;
      }
    }
  }
  .inFacilityStock {
    height: 100%;
    overflow: hidden;
    border-radius: 8px;
    margin-bottom: 10px;
    box-shadow: 0.5px 0.5px 6px #b7c3cbe6;
    .inFacilityPersonStock-tbl {
      height: 450px;
      box-shadow: none;
      tr {
        border-bottom: 1px solid #e3e3e3;
        th,
        td {
          padding: 10px 16px;
          vertical-align: middle;
          min-height: 44px;
          font-size: 16px;
        }
        td {
          display: flex;
          align-items: center;
          border: none;
        }
      }
      thead {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 3;
        th {
          color: #fff;
          position: relative;
          white-space: nowrap;
          min-width: 100px;
          font-size: 1rem;
          font-weight: 700;
          text-align: left;
        }
      }
      tbody {
        tr {
          &.inFacilityStock-head,
          &.inFacilityStock-footer {
            td {
              background: #d8f2fe;
              font-size: 1.125rem;
              font-weight: 700;
            }
          }
        }
      }
    }
  }
  .paginStock {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    background: #fff;
    padding: 16px;
    box-shadow: 0.5px 0.5px 6px #b7c3cbe6;
    position: relative;
    .pagination-cases {
      padding-right: 10px;
      color: #8e8e8e;
      font-weight: 500;
    }
  }
  .titleStock {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px 0 32px;
    .titleStock-tlt {
      font-size: 1.375rem;
      font-weight: 500;
    }
    .titleStock-more {
      font-size: 1rem;
    }
  }
  .contentStock {
    height: 495px;
    overflow: hidden;
    padding: 14px 0 20px 10px;
    @media (max-width: $viewport_tablet) {
      padding: 14px 10px 20px;
    }
    .contentStock-box {
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      background: #fff;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 8px 0 0 8px;
      @media (max-width: $viewport_tablet) {
        border-radius: 8px;
      }
    }
    .contentStock-lst {
      height: calc(100% - 108px);
      ul {
        li {
          display: flex;
          justify-content: space-between;
          padding: 10px 16px 10px 22px;
          border-bottom: 1px solid #e3e3e3;
          .btn {
            min-width: 42px;
            margin-left: 10px;
          }
          .contentStock-info {
            font-size: 14px;
            .contentStock-txt {
              .contentStock-name {
                font-size: 1rem;
                font-weight: bold;
                margin-right: 20px;
              }
            }
          }
        }
      }
    }
  }
  .contentStock-form {
    background: #f9f9f9;
    box-shadow: 0 -3px 6px rgba(0, 0, 0, 0.1);
    border-radius: 0 0 0 8px;
    position: relative;
    padding: 12px 20px 24px;
    height: 108px;
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -20px;
      li {
        width: 50%;
        padding-left: 20px;
        .formLabel {
          margin-bottom: 8px;
        }
      }
    }
  }
  .formBtn {
    text-align: center;
    padding: 0 24px 24px;
    .btn {
      width: 180px;
      margin-right: 20px;
      &:last-child {
        margin-right: 0;
      }
    }
  }

  .inFacility-title {
    margin-bottom: 0;
  }
  .inFacility-link {
    cursor: pointer;
    color: #448add !important;
    font-weight: bold;
    font-size: 1rem;
    &:hover {
      text-decoration: underline !important;
    }
  }
}

.noDataPersonStock {
  height: 400px !important;
}

.noDataStock {
  height: 100% !important;
}

.stockContent {
  padding-right: 20px;
  flex-grow: 1;
  font-size: 14px;
}

.stockBtn {
  flex: 0 0 50px;
  align-items: center;
}

.height515 {
  height: 515px !important;
}
.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}
.text-name {
  font-size: 1rem;
  font-weight: bold;
}
</style>
