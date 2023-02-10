<template>
  <!-- modal -->
  <div class="modal-body modalStock modal-body-A02S01">
    <div id="msfa-notify-A02S01"></div>
    <div class="groupRow">
      <div class="groupLeft">
        <div class="boxStock">
          <div class="stockHead">説明会出席者</div>
          <div class="stockList scrollbar">
            <ul v-if="dataA01S02.length > 0">
              <li v-for="item of dataA01S02" :key="item">
                <div class="stockList-info">
                  <p class="stockList-txt">{{ item.facility_short_name }}　{{ item.department_name }}</p>
                  <p class="stockList-txt">
                    <a class="stockList-link" href="#">{{ item.person_name }}</a
                    >{{ item.display_position_name }}
                  </p>
                </div>
                <div class="stockList-btn">
                  <button v-if="item.isAdd === false" type="button" class="btn btn--icon" @click="Add(true, item.convention_attendee_id)">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack.svg')" alt="" />
                  </button>
                  <button v-if="item.isAdd === true" class="btn btn--icon" type="button" @click="deleteAdd(item.convention_attendee_id)">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack01.svg')" alt="" />
                  </button>
                </div>
              </li>
            </ul>
            <div v-else class="noData">
              <div class="noData-content">
                <p class="noData-text">該当するデータがありません。</p>
                <div class="noData-thumb">
                  <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                </div>
              </div>
            </div>
          </div>
          <div
            v-if="dataA01S02.length > 0"
            class="paginationFacility"
            style="
              display: flex;
              justify-content: flex-end;
              padding: 8px 32px 12px 24px;
              align-items: center;
              box-shadow: 0.5px 0.5px 6px rgb(183 195 203 / 90%);
            "
          >
            <div style="margin-right: 11px" class="pagination-cases">全 {{ totalRecord2 }} 件</div>
            <PaginationTable
              :page-size="100"
              layout="prev, pager, next"
              :total="totalRecord2"
              :current-page="currentPage2"
              @current-change="handleCurrentChange2"
            />
          </div>
        </div>
      </div>
      <div class="groupRight">
        <div class="stockTitle">ストック登録</div>
        <div class="stockRegis">
          <div v-if="checkData" class="stockRegis-list scrollbar">
            <ul v-for="item of dataA01S02" :key="item">
              <li v-if="item.isAdd === true">
                <div class="stockRegis-info">
                  <p class="stockRegis-txt">{{ item.facility_short_name }}　{{ item.department_name }}</p>
                  <p class="stockRegis-txt">
                    <span class="stockRegis-span">{{ item.person_name }}</span
                    >{{ item.display_position_name }}
                  </p>
                </div>
                <div class="stockRegis-btn">
                  <button class="btn btn--icon" type="button" @click="deleteAdd(item.convention_attendee_id)">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                  </button>
                </div>
              </li>
            </ul>
          </div>
          <div v-else class="noData">
            <div class="noData-content">
              <p class="noData-text">該当するデータがありません。</p>
              <div class="noData-thumb">
                <img src="@/assets/template/images/data/amico.svg" alt="icon" />
              </div>
            </div>
          </div>
          <div class="stockRegis-form">
            <ul>
              <li>
                <label class="stockRegis-label">面談内容</label>
                <div class="stockRegis-control">
                  <el-select v-model="content" placeholder="未選択" class="form-control-select">
                    <el-option label="" value="">未選択 </el-option>
                    <el-option v-for="item in dataSelect" :key="item" :label="item.content_name" :value="item.content_cd"> </el-option>
                  </el-select>
                </div>
              </li>
              <li>
                <label class="stockRegis-label">品目</label>
                <div class="stockRegis-control">
                  <div class="form-icon icon-right">
                    <span class="icon log-icon" title_log="品目" @click="openModalZ05S06" @touchstart="openModalZ05S06">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                    </span>
                    <div v-if="dataProduct.length > 0" class="form-control d-flex align-items-center">
                      <div class="block-tags">
                        <el-tag v-for="(item, index) in dataProduct" :key="item" class="m-0 el-tag-custom" closable @close="handleRemove(index)">
                          {{ item.product_name }}
                        </el-tag>
                      </div>
                    </div>
                    <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="stockBtn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
      <button type="button" :disabled="checkBtnStock" class="btn btn-primary" @click="checkSubmit">設定</button>
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
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
import PaginationTable from '@/components/PaginationTable';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import { markRaw } from 'vue';
import C01_S03_AttendantManagement from '../../../api/C01/C01_S03_AttendantManagement';
export default {
  name: '_A02_S01_Stock',
  components: {
    PaginationTable
  },
  props: {
    conventionId: {
      type: Number
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      dataA01S02: [],
      content: '',
      dataProduct: [],
      checkData: false,
      totalRecord2: 0,
      currentPage2: 1,
      total_pages2: 0,
      checkBtnStock: true,
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
      props: {
        mode: 'multiple',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      }
    };
  },
  mounted() {
    this.getList();
  },
  methods: {
    getList() {
      A02_S03_StockManagementServices.getAllContentService()
        .then((res) => {
          this.dataSelect = res.data.data;
          this.getList2();
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A02S01',
            idNodeNotify: 'msfa-notify-A02S01'
          });
        });
    },
    getList2() {
      const data = {
        convention_id: 517,
        limit: 100,
        offset: this.currentPage2 === 0 ? this.currentPage2 : this.currentPage2 - 1
      };
      C01_S03_AttendantManagement.getList(data).then((res) => {
        this.dataA01S02 = res.data.data.records;
        this.totalRecord2 = res.data.data.pagination.total_items;
        this.dataA01S02.forEach((element) => {
          element.isAdd = false;
        });
      });
    },
    Add(isAdd, convention_attendee_id) {
      this.checkBtnStock = false;
      this.dataA01S02.forEach((element) => {
        if (element.convention_attendee_id === convention_attendee_id) {
          this.checkData = true;
          element.isAdd = isAdd;
        }
      });
    },
    deleteAdd(convention_attendee_id) {
      let countFlagTrue = 0;
      this.dataA01S02.forEach((element) => {
        if (element.convention_attendee_id === convention_attendee_id) {
          element.isAdd = false;
        }
        if (element.isAdd) {
          countFlagTrue = countFlagTrue + 1;
        }
      });
      if (countFlagTrue === 0) {
        this.checkData = false;
        this.checkBtnStock = true;
      }
    },
    handleCurrentChange(val) {
      this.pagelist = val;
    },
    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05_S06_Material_Selection),
        width: this.props.mode === 'single' ? '33rem' : '42rem',
        customClass: this.props.mode === 'single' ? 'custom-dialog modal-fixed modal-fixed-min' : 'custom-dialog modal-fixed',
        props: {
          mode: this.props.mode,
          categoryCode: this.props.categoryCode,
          classificationCode: this.props.classificationCode,
          initDataCodes: this.props.initDataCodes
        }
      };
    },
    onResultModal(e) {
      if (e) {
        this.props.initDataCodes = [];
        this.props.classificationCode = e.classifications?.product_group_cd;
        this.props.categoryCode = e.category?.definition_value;
        e.dataSelected?.forEach((x) => {
          this.props.initDataCodes.push(x.product_cd);
        });
        this.dataProduct = e.dataSelected;
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      } else {
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      }
    },
    handleRemove(index) {
      this.dataProduct.splice(index, 1);
    },
    checkSubmit() {
      let arrAddFacility_cd = [];
      let arrAddPerson_cd = [];
      let arrdataProduct = [];
      let stockTypes = [];
      let actionIds = [];
      this.dataA01S02.forEach((element) => {
        if (element.isAdd) {
          arrAddFacility_cd.push(element.facility_cd);
          arrAddPerson_cd.push(element.person_cd);
          stockTypes.push(element.schedule_type);
          if (element.schedule_type === '10' || element.schedule_type === '20') {
            actionIds.push(element.schedule_id);
          } else {
            actionIds.push('');
          }
        }
      });
      this.dataProduct.forEach((element) => {
        arrdataProduct.push(element.product_cd);
      });
      this.submitStock(arrAddFacility_cd, arrAddPerson_cd, this.content, arrdataProduct, stockTypes, actionIds);
    },
    submitStock(arrAddFacility_cd, arrAddPerson_cd, content, arrdataProduct, stockTypes) {
      const data = {
        facility_cd: arrAddFacility_cd,
        person_cd: arrAddPerson_cd === [null] ? [] : arrAddPerson_cd,
        content_cd: content,
        product_cd: arrdataProduct,
        stock_type: stockTypes.toString(),
        action_id: this.conventionId
      };
      A02_S03_StockManagementServices.AddStockService(data)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.closeModal();
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A02S01',
            idNodeNotify: 'msfa-notify-A02S01'
          });
        });
    },
    closeModal() {
      this.$emit('onFinishScreen');
    },
    handleCurrentChange2(val) {
      if (this.total_pages2 <= this.currentPage2) {
        this.currentPage2 = val;
        this.getList2();
      } else {
        this.currentPage2 = val;
        this.getList2();
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.stockRegis-txt {
  display: flex;
  align-items: center;
  .stockRegis-span {
    margin-right: 11px;
  }
}
.stockList {
  .noData {
    .noData-thumb {
      margin-top: 46px;
    }
  }
}
.stockRegis {
  .noData {
    .noData-thumb {
      margin-top: 10px;
      img {
        width: 39%;
      }
    }
  }
}

.modalStock {
  padding: 32px 0 32px 32px;
  .groupRow {
    display: flex;
    flex-wrap: wrap;
    height: 503px;
    .groupLeft {
      width: 58%;
      padding-right: 20px;
      @media (max-width: $viewport_tablet) {
        width: 50%;
      }
    }
    .groupRight {
      width: 42%;
      @media (max-width: $viewport_tablet) {
        width: 50%;
      }
    }
  }
  .boxStock {
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 10px;
    overflow: hidden;
    .stockHead {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      padding: 16px 32px;
      font-size: 1.125rem;
      font-weight: 700;
      color: #fff;
    }
    .stockList {
      height: 356px;
      padding: 0 12px;
      ul {
        li {
          display: flex;
          flex-wrap: wrap;
          padding: 8px 28px 8px 16px;
          border-bottom: 1px solid #e3e3e3;
          @media (max-width: $viewport_tablet) {
            padding: 8px 12px;
          }
          &:last-child {
            border-bottom: none;
          }
          .stockList-info {
            width: calc(100% - 42px);
            padding-right: 12px;
            .stockList-link {
              font-size: 1rem;
              font-weight: 700;
              margin-right: 11px;
            }
          }
          .stockList-btn {
            width: 42px;
            .svg {
              width: 28px;
            }
          }
        }
      }
    }
    .stockPagination {
      background: #ffffff;
      box-shadow: 0px -3px 6px rgba(0, 0, 0, 0.1);
      border-radius: 0px 0px 8px 8px;
      padding: 10px;
      display: flex;
      justify-content: center;
      .pagination-bord {
        box-shadow: none;
      }
    }
  }
  .stockTitle {
    font-size: 1.125rem;
    font-weight: 700;
    padding: 16px 18px;
  }
  .stockRegis {
    height: calc(100% - 100px);
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    background: #fff;
    border-radius: 8px 0 0 8px;
    overflow: hidden;
    position: relative;
    .stockRegis-form {
      padding: 20px 20px 24px;
      background: #f9f9f9;
      box-shadow: 0px -3px 6px rgba(0, 0, 0, 0.1);
    }
    .stockRegis-form {
      position: absolute;
      bottom: 0;
      width: 100%;
      border-radius: 8px 0 0 8px;
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -20px;
        li {
          width: 50%;
          padding-left: 20px;
          .stockRegis-label {
            margin-bottom: 8px;
            font-size: 14px;
          }
        }
      }
    }
  }
  .stockRegis-list {
    padding: 0 8px;
    height: 295px;
    ul {
      li {
        padding: 8px 16px 8px 11px;
        display: flex;
        border-bottom: 1px solid #e3e3e3;
        &:last-child {
          border-bottom: none;
        }
        .stockRegis-info {
          width: calc(100% - 42px);
          padding-right: 12px;
          .stockRegis-span {
            font-size: 1rem;
            font-weight: 700;
          }
        }
        .stockRegis-btn {
          width: 42px;
        }
      }
    }
  }
  .stockBtn {
    display: flex;
    justify-content: center;
    margin-top: 20px;
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
