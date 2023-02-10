<template>
  <div v-loading="isLoadingTable" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button type="button" class="btn btn-filter" data-toggle="dropdown" @click="showFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="`dropdown-menu dropdown-filter ${isShowPopupFilter ? 'show' : ''}`">
              <div class="item-filter btn-close-filter" @click="showFilter">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="formGoup-filter">
                <ul>
                  <li>
                    <label class="formGoup-filter-label">面談内容</label>
                    <div class="formGoup-filter-control">
                      <el-select v-model="filterSearch.content_cd" placeholder="未選択" class="form-control-select">
                        <el-option label="" value="">未選択</el-option>
                        <el-option v-for="item in allContent" :key="item" :label="item.content_name" :value="item.content_cd"> </el-option>
                      </el-select>
                    </div>
                  </li>
                  <li>
                    <label class="formGoup-filter-label">品目</label>
                    <div class="formGoup-filter-control">
                      <div class="form-icon icon-right">
                        <span class="icon log-icon" title_log="品目" @click="openModalZ05_S06_Material_Selection">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                        </span>
                        <div v-if="filterSearch.product_list.length > 0" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag v-for="item in filterSearch.product_list" :key="item" class="m-0 el-tag-custom" closable @close="handleRemoveProduct(item)">
                              {{ item.product_name }}
                            </el-tag>
                          </div>
                        </div>
                        <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                      </div>
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="formGoup-filter-label">ステータス</label>
                    <div class="formGoup-filter-control">
                      <ul class="btnSelect mfsa-custom-tab-select3">
                        <li
                          class="btn-select"
                          :class="{ active: filterSearch.status_type === '' }"
                          @click="setStatusTypeFilter('')"
                          @touchstart="setStatusTypeFilter('')"
                        >
                          全て
                        </li>
                        <li
                          class="btn-select"
                          :class="{ active: filterSearch.status_type === 1 }"
                          @click="setStatusTypeFilter(1)"
                          @touchstart="setStatusTypeFilter(1)"
                        >
                          未計画
                        </li>
                        <li
                          class="btn-select"
                          :class="{ active: filterSearch.status_type === 0 }"
                          @click="setStatusTypeFilter(0)"
                          @touchstart="setStatusTypeFilter(0)"
                        >
                          計画済
                        </li>
                      </ul>
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="formGoup-filter-label">施設名</label>
                    <div class="formGoup-filter-control">
                      <el-input v-model="filterSearch.facility_name" clearable placeholder="施設名入力" class="form-control-input" />
                    </div>
                  </li>
                  <li>
                    <label class="formGoup-filter-label">施設分類</label>
                    <div class="formGoup-filter-control">
                      <el-select v-model="filterSearch.facility_category_type" placeholder="未選択" class="form-control-select">
                        <el-option label="" value="">未選択</el-option>
                        <el-option v-for="item in facilityCategory" :key="item" :label="item.facility_category_name" :value="item.facility_category_type">
                        </el-option>
                      </el-select>
                    </div>
                  </li>
                  <li>
                    <label class="formGoup-filter-label">個人名</label>
                    <div class="formGoup-filter-control">
                      <el-input v-model="filterSearch.person_name" clearable placeholder="名前入力" class="form-control-input" />
                    </div>
                  </li>
                </ul>
              </div>
              <div class="btnFilter">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelFilter">キャンセル</button>
                <button type="button" class="btn btn-primary btn-filter-submit" @click="search(true)">検索</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div ref="tblStock" class="tblStock-manage scrollbar" @scroll="onScroll">
        <div v-if="dataTable.length > 0" class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
        </div>
        <table class="tableBox tableCustom">
          <thead>
            <tr>
              <th>
                <div class="checkAll checkAll--change">
                  <label class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" readonly :checked="isCheckAll" @click="checkAllRecord('btnCheckAll')" />
                    <span :class="isCheckAll ? 'checkAll1' : ''"></span>
                    <span
                      :class="countCheckbox > 0 && countCheckbox != dataTable.length ? 'custom-checkbox-gmail' : ''"
                      class="custom-control-indicator custom-checkbox-k1"
                    >
                      <span :class="isCheckAll ? 'abcde' : ''" class="abc"></span>
                    </span>
                  </label>
                  <div class="checkAll-drop checkAll-drop2">
                    <span class="abcd"></span>
                    <button class="btn btn--arrow" data-toggle="dropdown">&nbsp;</button>
                    <div class="dropdown-menu dropdown-select">
                      <ul>
                        <li :class="`${isCheckAll === true && isCheckAllCount === false ? 'btn-disabled' : ''}`" @click="checkAllRecord(true)">すべて選択</li>
                        <li :class="`${isCheckAll === false ? 'btn-disabled' : ''}`" @click="checkAllRecord(false)">すべて解除</li>
                        <li @click="editMultiStock" @touchstart="editMultiStock">一括編集</li>
                        <li @click="deleteMultiStock()">一括削除</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </th>
              <th>施設名/個人名</th>
              <th>面談内容</th>
              <th>品目</th>
              <th>資材</th>
              <th>入力画面</th>
              <th>ストック日</th>
              <th>計画状況</th>
            </tr>
          </thead>
          <tbody class="body-gray body-dropdown">
            <tr v-for="(data, i) in dataTable" v-show="dataTable.length > 0" :id="`className--${data.stock_id}`" :ref="`row-${data.stock_id}`" :key="data">
              <td @click="onCheckItem(data)">
                <label class="custom-control custom-checkbox">
                  <input class="custom-control-input" type="checkbox" :checked="data.isCheck" @input="onCheckItem(data)" />
                  <span class="custom-control-indicator" @click="onCheckItem(data)"></span>
                </label>
              </td>
              <td>
                <p>{{ data.facility_short_name }}病院　{{ data.department_name }}</p>
                <p>
                  <span class="tblStock-manage-doctor">{{ data.person_name }}</span> <span style="vertical-align: bottom">医師</span>
                </p>
              </td>
              <td>{{ data.content_name }}</td>
              <td>
                <p>{{ showArrayListText('product_name', data.product_list) }}</p>
              </td>
              <td>
                <a v-if="data.document_list.length > 0" href="#" @click="openModalA02_S03_ModalMaterialList(data)">あり</a>
                <p v-else>なし</p>
              </td>
              <td>
                <span v-if="data.stock_type === '20'" class="link" @click="redirectConvention(data)"> 講演会</span>
                <span v-else-if="data.stock_type === '10'" class="link" @click="redirectBriefing(data)">説明会 </span>
                <p v-else>ターゲット</p>
              </td>
              <td>{{ formatFullDate(data.stock_date) }}</td>
              <td>
                <div class="planningStatus">
                  <span v-if="data.status_type === 10 || data.status_type === '10'" class="span-label span-label--greenFl">未計画</span>
                  <span v-else class="span-label span-label--gray">計画済</span>
                  <div class="planningStatus-btn">
                    <button
                      class="btn btn--icon"
                      type="button"
                      @click="handleClick($event, data, i, dataTable.length - 1)"
                      @touchstart="handleTouchStart($event, data, i, dataTable.length - 1)"
                      @touchend="handleTouchEnd($event, data, i, dataTable.length - 1)"
                      @touchmove="handleTouchMove($event, data, i, dataTable.length - 1)"
                    >
                      <span></span><span></span><span></span>
                    </button>
                  </div>
                </div>
              </td>
            </tr>
            <tr v-if="dataTable.length === 0 && !isLoadDefault" class="not-hover">
              <td colspan="8">
                <EmptyData />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div id="dropdown-menu" style="display: none">
      <ul class="list-item">
        <li @mousedown="HOFClosePopup(openEditRowStockModal)(dropdownData.data)">
          <span class="item">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
          </span>
          <span class="text-label">編集</span>
        </li>
        <li v-if="dropdownData.data.status_type !== '10'" @mousedown="HOFClosePopup(checkPlanStock)(dropdownData.data)">
          <span class="item">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_return.svg')" alt="" />
          </span>
          <span class="text-label">未計画に戻す</span>
        </li>
        <li @mousedown="HOFClosePopup(confirmEventDelete)([dropdownData.data.stock_id], dropdownData.data.stock_id)">
          <span class="item">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" />
          </span>
          <span class="text-label">削除</span>
        </li>
      </ul>
    </div>
    <div v-if="dataTable.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ pagination.total_items || 'O' }} 件</div>
      <PaginationTable
        layout="prev, pager, next"
        :page-size="filterSearch.limit"
        :total="pagination.total_items"
        :current-page="pagination.current_page"
        @current-change="handleCurrentChange"
      />
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass + ' handle-close'"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
      :before-close="handleBeforeClose"
      @close="closeModalStock"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          ref="modalChild"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="comfirmDelete"
          @onFinishScreenA02S03="onFinishScreenA02S03"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import PaginationTable from '@/components/PaginationTable';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import A02_S03_ModalEditStock from './A02_S03_ModalEditStock.vue';
import A02_S03_ModalMaterialList from './A02_S03_ModalMaterialList.vue';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';
import { markRaw } from 'vue';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'A02_S03_StockManagement',
  components: {
    Z05_S06_Material_Selection,
    A02_S03_ModalEditStock,
    A02_S03_ModalMaterialList,
    PaginationTable
  },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      deleteProcessing: {},
      countCheckbox: 0,
      filterSearch: {
        content_cd: '', //R
        product_list: [], //R
        status_type: 1, // '',0,1
        facility_cd: [],
        facility_name: '',
        facility_category_type: '',
        person_name: '',
        limit: 20,
        offset: 0
      },
      props_Z05_S06_Material_Selection: {
        mode: 'multiple',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },
      dataTable: [],
      isLoadingTable: false,
      isLoadDefault: true,
      pagination: {},
      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      isCheckAll: false,
      isCheckAllCount: false,
      isActiveEditAll: false,
      arrayStockId: [],
      arrayStockId2: [],
      idStockEdit: '',
      dropdownData: {
        data: [],
        index: null
      },
      touch: {
        start: 0,
        end: 0
      },
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      ids: [],
      tempStockId: [],
      flagDelete: false,
      onScrollTop: 0,
      changePage: false
    };
  },
  computed: {
    ...mapGetters({
      facilityCategory: 'A02/a02_S03_all_facility_category',
      allContent: 'A02/a02_S03_all_content'
    })
  },

  watch: {
    filterSearch(value) {
      this.props_Z05_S06_Material_Selection.initDataCodes = this.formatArrayString('product_cd', value.product_list);
    },
    dataTable(value) {
      let countChecked = 0;
      value.map((item) => {
        if (item.isCheck === true) {
          countChecked += 1;
        }
      });
      if (countChecked === value.length) {
        this.isCheckAllCount = false;
        if (value.length === 0) {
          this.isCheckAll = false;
        } else this.isCheckAll = true;
      } else {
        if (countChecked === 0) {
          this.isCheckAll = false;
        } else {
          this.isCheckAll = true;
          this.isCheckAllCount = true;
        }
      }
      this.isActiveEditAll = this.checkStatusEditAll(value);
    }
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'ストック管理',
      icon: 'icon-stock-management.svg',
      isShowBack: false
    });

    localStorage.removeItem('checkRecordA02S03');
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.A02_S03_StockManagement;
    this.pagination.current_page = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.A02_S03_StockManagement || 1;

    let data2 = JSON.parse(localStorage.getItem('dataA02S03Filter'));
    if (data2) {
      this.filterSearch = data2;
    }

    this.search();
  },
  created() {
    this.getScreenData();
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    redirectConvention(item) {
      let scrollTop = this.$refs.tblStock ? this.$refs.tblStock.scrollTop : 0;
      this.setScrollScreen('A02_S03_StockManagement', scrollTop);
      this.$router.push({ name: 'C01_S02_LectureInput', params: { convention_id: item.action_id } });
    },

    redirectBriefing(item) {
      let scrollTop = this.$refs.tblStock ? this.$refs.tblStock.scrollTop : 0;
      this.setScrollScreen('A02_S03_StockManagement', scrollTop);
      this.$router.push({ name: 'B01_S02_BriefingSessionInput', briefing_id: item.action_id });
    },

    onScrollLeft() {
      let content = document.querySelector('.tblStock-manage');
      content.scrollLeft -= 200;
    },

    onScrollRight() {
      let content = document.querySelector('.tblStock-manage');
      content.scrollLeft += 200;
    },

    onScroll({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }

      if (this.isScrolling) {
        let content = document.querySelector('.tblStock-manage');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
      let scrollTop = this.$refs.tblStock ? this.$refs.tblStock.scrollTop : 0;
      this.setScrollScreen('A02_S03_StockManagement', scrollTop);
    },

    handleCurrentChange(val) {
      this.onScrollTop = 0;
      this.pagination = { ...this.pagination, current_page: val };
      this.search();
      this.changePage = true;
    },

    getScreenData() {
      this.$store.dispatch('A02/getAllFacilityCategoryAction');
      this.$store.dispatch('A02/getAllContentAction');
    },

    formatArrayString(paramName, arrayObject) {
      return arrayObject.map((item) => item[paramName]);
    },

    showArrayListText(itemName, arrayObject) {
      let text = '';
      arrayObject.map((item) => (text += item[itemName] + ', '));
      return text?.substring(0, text.length - 2);
    },

    //HandleModal
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      this.getClassChangeBRG(this.idStockEdit, true);
      this.getClassChangeBRG(this.ids, true);
      this.getClassChangeBRG(this.arrayStockId2, true);
    },
    onFinishScreenA02S03() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    openModalA02_S03_ModalMaterialList(data) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材一覧',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        component: markRaw(A02_S03_ModalMaterialList),
        props: {
          inDocumentId: data.document_list
        },
        width: '558px',
        isShowClose: true
      };
    },

    openModalZ05_S06_Material_Selection() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed',
        component: markRaw(Z05_S06_Material_Selection),
        props: {
          ...this.props_Z05_S06_Material_Selection
        },
        width: '42rem'
      };
    },

    openEditStockModal() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ストック編集',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed',
        component: markRaw(A02_S03_ModalEditStock),
        width: '636px'
      };
    },

    onResultModal(data) {
      if (data && data.screen === 'Z05_S06') {
        this.onResultModal_Z05_S06_Material_Selection(data);
      }
      if (data && data.screen === 'A02_S03_ModalEditStock') {
        this.onResultModal_A02_S03_StockManagement(data);
      }
      this.onCloseModal();
    },

    onResultModal_Z05_S06_Material_Selection(data) {
      this.props_Z05_S06_Material_Selection = {
        ...this.props_Z05_S06_Material_Selection,
        classificationCode: '',
        categoryCode: '',
        initDataCodes: data.dataSelected
      };
      this.filterSearch = { ...this.filterSearch, product_list: data.dataSelected };
    },

    closeModalStock() {
      this.getClassChangeBRG(this.idStockEdit, true);
      this.getClassChangeBRG(this.ids, true);
      this.getClassChangeBRG(this.arrayStockId2, true);
      setTimeout(() => {
        this.idStockEdit = [];
        this.ids = [];
        this.arrayStockId2 = [];
      }, 2000);
    },

    onResultModal_A02_S03_StockManagement(data) {
      let params = {
        stock_id: data.stock_id,
        content_cd: data.content_cd,
        product_cd: this.formatArrayString('product_cd', data.product_list),
        document_id: this.formatArrayString('document_id', data.document_list),
        facility_category_type: data.facility_category_type_list
      };
      this.editStock(params, data);
    },

    //Handle Filter

    setStatusTypeFilter(status_type) {
      this.filterSearch = { ...this.filterSearch, status_type: status_type };
    },

    showFilter() {
      this.isShowPopupFilter = !this.isShowPopupFilter;
    },

    cancelFilter() {
      this.isShowPopupFilter = false;
    },
    //Handle DataTable

    search(isClickSearch) {
      this.isLoadingTable = true;
      this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.A02_S03_StockManagement;
      if (isClickSearch) {
        this.onScrollTop = 0;
        this.isLoadDefault = false;
      }
      if (!this.flagDelete) {
        this.tempStockId = JSON.parse(localStorage.getItem('checkRecordA02S03')) ? JSON.parse(localStorage.getItem('checkRecordA02S03')) : [];
      } else {
        this.tempStockId = [];
      }
      this.countCheckbox = this.tempStockId.length;
      this.isShowPopupFilter = false;
      A02_S03_StockManagementServices.searchStockService({
        ...this.filterSearch,
        product_cd: this.props_Z05_S06_Material_Selection.initDataCodes,
        product_list: null,
        facility_category_type: this.filterSearch.facility_category_type,
        offset: isClickSearch ? 0 : this.pagination.current_page - 1
      })
        .then((res) => {
          this.dataTable = res.data.data.records.map((item) => {
            return { ...item, isCheck: false };
          });
          this.dataTable.forEach((element) => {
            this.tempStockId.forEach((element2) => {
              // eslint-disable-next-line eqeqeq
              if (element.stock_id == element2) {
                element.isCheck = true;
              }
            });
          });
          localStorage.setItem('dataA02S03', JSON.stringify(res.data));
          localStorage.setItem('dataA02S03Filter', JSON.stringify(this.filterSearch));
          this.pagination = res.data.data.pagination;
          if (this.tempStockId.length === 0) {
            this.checkAllRecord(false);
          }
          if (res.data.data.records.length === 0 && this.filterSearch.offset > 0) {
            this.filterSearch.offset = this.filterSearch.offset--;
            this.search();
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(async () => {
          if (this.$refs.tblStock && this.changePage) {
            this.$refs.tblStock.scrollTop = 1;
          }
          await new Promise((resolve) => setTimeout(resolve, 500));
          this.setCurrentPageScreen('A02_S03_StockManagement', this.pagination.current_page);
          if (this.$refs.tblStock && !this.changePage) {
            this.$refs.tblStock.scrollTop = this.onScrollTop;
          }
          this.changePage = false;

          this.js_pscroll();
          this.isLoadingTable = false;
          this.isLoadDefault = false;
        });
    },

    handleRemoveProduct(item) {
      this.filterSearch = { ...this.filterSearch, product_list: this.filterSearch.product_list.filter((x) => x.product_cd !== item.product_cd) };
    },

    checkAllRecord(status) {
      switch (status) {
        case true:
          this.isCheckAll = true;
          this.countCheckbox = this.dataTable.length;
          this.dataTable = this.dataTable.map((item) => {
            return { ...item, isCheck: true };
          });
          break;
        case false:
          this.countCheckbox = 0;
          this.isCheckAll = false;
          this.dataTable = this.dataTable.map((item) => {
            return { ...item, isCheck: false };
          });
          break;
        case 'btnCheckAll':
          if (this.isCheckAll === false) {
            this.checkAllRecord(true);
          } else {
            this.checkAllRecord(false);
          }
          break;
        default:
          break;
      }
    },

    onCheckItem(data) {
      this.countCheckbox = 0;
      this.dataTable = this.dataTable.map((item) => {
        if (data.stock_id === item.stock_id) return { ...item, isCheck: !item.isCheck };
        return item;
      });
      this.dataTable.forEach((element) => {
        if (element.isCheck) {
          this.countCheckbox = this.countCheckbox + 1;
          if (!this.tempStockId.includes(element.stock_id)) {
            this.tempStockId.push(element.stock_id);
          }
        } else {
          if (this.tempStockId.includes(element.stock_id)) {
            this.tempStockId.splice(this.tempStockId.indexOf(element.stock_id), 1);
          }
        }
      });
      if (this.dataTable.length === this.countCheckbox) {
        this.countCheckbox = 0;
      }
      localStorage.setItem('checkRecordA02S03', JSON.stringify(this.tempStockId));
    },

    openEditRowStockModal(data) {
      this.idStockEdit = data.stock_id;
      this.getClassChangeBRG(data.stock_id, false);
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ストック編集',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed',
        component: markRaw(A02_S03_ModalEditStock),
        width: '636px',
        isShowClose: true,
        props: {
          inStockId: [data.stock_id],
          inContentCd: data.content_cd,
          inProductList: data.product_list,
          inDocumentId: data.document_list,
          inFacilityCategoryType: [data.facility_category_type]
        }
      };
    },

    editStock(params, dataRaw) {
      A02_S03_StockManagementServices.editStockService(params)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
          let content_new = this.allContent.find((item) => item.content_cd === dataRaw.content_cd);
          this.dataTable = this.dataTable.map((data) => {
            if (params.stock_id.some((record) => record === data.stock_id)) {
              return {
                ...data,
                content_cd: content_new?.content_cd || '',
                content_name: content_new?.content_name || '',
                product_list: dataRaw.product_list,
                document_list: dataRaw.document_list
              };
            }
            return data;
          });
          this.tempStockId = [];
          this.search();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(() => {
          this.getClassChangeBRG(this.idStockEdit, true);
          this.getClassChangeBRG(this.ids, true);
        });
    },

    checkStatusEditAll(dataTable) {
      const arrayEditStock = dataTable.filter((data) => data.isCheck === true);
      let isDifferentFacilityType = false;
      let firstFacilityType = '';
      if (arrayEditStock.length > 1) {
        firstFacilityType = arrayEditStock[0].facility_category_type;
      } else {
        return;
      }
      arrayEditStock.map((data) => {
        if (data.facility_category_type !== firstFacilityType) {
          isDifferentFacilityType = true;
        }
      });
      return isDifferentFacilityType;
    },

    editMultiStock() {
      if (this.isActiveEditAll) {
        this.$notify({ message: '異なる施設分類の施設が選択されているため、一括編集できません', customClass: 'error', duration: 1500 });
      } else {
        const arrayEditStock = this.dataTable.filter((data) => data.isCheck === true);
        let isDifferentFacilityType = false;
        let firstFacilityType = '';
        let firstContentCd = '';
        let productListEdit = [];
        let documentListEdit = [];
        let stockListEdit = [];

        if (arrayEditStock.length > 0) {
          firstFacilityType = arrayEditStock[0].facility_category_type;
          firstContentCd = arrayEditStock[0].content_cd;
        } else {
          return;
        }

        arrayEditStock.map((data) => {
          if (data.facility_category_type !== firstFacilityType) {
            isDifferentFacilityType = true;
          }
          if (data.content_cd !== firstContentCd) {
            firstContentCd = '';
          }
        });

        if (isDifferentFacilityType) {
          return;
        } else {
          arrayEditStock.map((item) => {
            stockListEdit = [...stockListEdit, item.stock_id];
            productListEdit = this.mergeTwoArrayObject(productListEdit, item.product_list, 'product_cd');
            documentListEdit = this.mergeTwoArrayObject(documentListEdit, item.document_list, 'document_id');
          });

          this.modalConfig = {
            ...this.modalConfig,
            title: 'ストック編集',
            isShowModal: true,
            customClass: 'custom-dialog modal-fixed',
            component: markRaw(A02_S03_ModalEditStock),
            width: '636px',
            props: {
              inStockId: this.tempStockId.length > 0 ? this.tempStockId : stockListEdit,
              inContentCd: firstContentCd,
              inProductList: productListEdit,
              inDocumentId: documentListEdit,
              inFacilityCategoryType: [firstFacilityType]
            }
          };
          this.getClassChangeBRG(stockListEdit, false);
          this.ids = stockListEdit;
        }
      }
    },

    mergeTwoArrayObject(arrayData1, arrayData2, paramName) {
      let arrayAllData = arrayData1;
      arrayData2.map((data) => {
        if (!arrayAllData.some((item) => item[paramName] === data[paramName])) {
          arrayAllData.push(data);
        }
      });
      return arrayAllData;
    },

    checkPlanStock(data) {
      let status_type_new = 1;
      A02_S03_StockManagementServices.editStockService({
        stock_id: [data.stock_id],
        status_type: status_type_new
      })
        .then((res) => {
          this.dataTable = this.dataTable.map((item) => {
            if (data.stock_id === item.stock_id) return { ...item, status_type: status_type_new };
            else return item;
          });
          this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
          this.search();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        });
    },

    comfirmDelete() {
      this.deleteStock();
    },

    async deleteStock() {
      try {
        let data = [];

        this.arrayStockId.forEach((element) => {
          data.push(element);
        });
        const res = await A02_S03_StockManagementServices.deleteStockService({
          stock_id: this.arrayStockId[0][0] ? [this.arrayStockId[0][0]] : data
        });

        for await (let e of this.arrayStockId) {
          const itemAction = this.$refs[`row-${e}`];
          let item = Array.isArray(itemAction) ? itemAction[0] : itemAction;

          this.deleteProcessing = { [this.arrayStockId]: e };

          await new Promise((resolve) => setTimeout(resolve, 100));

          this.deleteProcessing = {};

          this.slideLeft(item);

          await new Promise((resolve) => setTimeout(resolve, 25));
        }

        await new Promise((resolve) => setTimeout(resolve, 1000));
        this.flagDelete = true;
        this.search();
        setTimeout(() => {
          this.flagDelete = false;
        }, 1000);

        this.checkAllRecord(false);

        this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
      } catch (err) {
        this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        this.deleteProcessing = {};
        this.deleteFaild();
      } finally {
        this.onCloseModal();
      }
    },

    destroy(itemAction) {
      const className = 'className--';
      const trBackGround = document.getElementById(`${className}${this.idStockEdit}`);
      trBackGround?.classList.remove('back-ground-active');
      trBackGround?.classList.add('back-ground-remove');
      itemAction?.classList?.toggle('deleted');
    },

    deleteFaild() {
      const className = 'className--';
      const trBackGround = document.getElementById(`${className}${this.idStockEdit}`);
      trBackGround.classList.remove('back-ground-active');
      trBackGround.classList.add('back-ground-remove-fail');
      setTimeout(() => {
        trBackGround.classList.remove('back-ground-remove-fail');
      }, 2500);
    },

    confirmEventDelete(arrayStockId, stockId) {
      this.idStockEdit = stockId;
      this.getClassChangeBRG(stockId, false);
      this.arrayStockId = arrayStockId;
      this.modalConfig = {
        ...this.modalConfig,

        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed mdal-fixed-min',
        isShowClose: false,
        props: {
          title: '選択したストックを完全に削除しますか？'
        }
      };
    },

    deleteMultiStock() {
      const arrayStockSelect = this.dataTable.filter((data) => data.isCheck === true);
      if (arrayStockSelect.length === 0) {
        return;
      }
      const arrayStockId = this.formatArrayString('stock_id', arrayStockSelect);
      this.arrayStockId2 = [...arrayStockId];
      this.getClassChangeBRG(arrayStockId, false);
      this.confirmEventDelete(this.tempStockId ? this.tempStockId : arrayStockId);
    },

    handleClick(e, data, index, lastIndex) {
      let _ref = this;
      this.dropdownData = {
        data,
        index
      };

      let elmt = e?.target?.offsetParent || e?.changedTouches.target?.offsetParent || e?.srcElement?.offsetParent;

      let dropdown = document.querySelector('#dropdown-menu'); // get dropdown item

      if (!elmt) return;

      let currentPosition = elmt?.getBoundingClientRect(); // get position

      let w = elmt?.offsetWidth;
      let { top, left } = currentPosition;

      dropdown.setAttribute('style', `--top:${top}px; --left:${left}px; --width:${w}px;`);

      if (elmt.classList.contains('active')) {
        // Already Active
        elmt.classList?.remove('active');
        dropdown.classList.contains('menu-hide') ? dropdown.classList.remove('menu-hide') : '';
        dropdown.classList.contains('menu-show-default') || dropdown.classList.contains('menu-show-last')
          ? dropdown.classList.remove('menu-show-default') || dropdown.classList.remove('menu-show-last')
          : '';
      } else {
        elmt.classList?.add('active');
        if (index === lastIndex && index > 0) {
          dropdown.classList.remove('menu-show-last');
          dropdown.classList?.contains('menu-show-default') ? '' : dropdown.classList.add('menu-show-default');
        } else {
          dropdown.classList.remove('menu-show-default');
          dropdown.classList?.contains('menu-show-last') ? '' : dropdown.classList.add('menu-show-last');
        }
      }

      window.addEventListener('mousemove', handleEvent);
      function handleEvent(ev) {
        let dropdown = document.querySelector('#dropdown-menu'); // get dropdown item
        let { x, y, width, height } = dropdown.getBoundingClientRect();
        let PosX = x + width;
        let PosY = y + height;
        let RectX = ev.x;
        let RectY = ev.y;
        if (RectX > x && RectX < PosX && RectY > y && RectY < PosY) {
          return;
        } else {
          window.removeEventListener('mousemove', handleEvent);
          _ref.handleHideDropdown();
        }
      }
    },

    handleTouchStart(e) {
      this.touch.start = e.targetTouches[0];
      this.touch.end = e.targetTouches[0];
    },

    handleTouchMove(e) {
      this.touch.end = e.targetTouches[0];
    },

    handleTouchEnd(e, data, index, lastIndex) {
      e.preventDefault();
      if (e.type !== 'touchend') {
        return;
      }
      this.dropdownData = {
        data,
        index
      };

      let elmt = e?.target?.offsetParent || e?.changedTouches.target?.offsetParent || e?.srcElement?.offsetParent;

      let dropdown = document.querySelector('#dropdown-menu'); // get dropdown item

      let { x, y, width, height, top, left } = elmt?.getBoundingClientRect(); // button position

      let elmtRight = x + width;
      let elmtBtm = y + height;
      let elmtLeft = x;
      let elmtTop = y;

      // Touch position - Cursor

      let cursorTop = this.touch.start.clientY;
      let cursorBottom = this.touch.end.clientY;
      let cursorLeft = this.touch.start.clientX;
      let cursorRight = this.touch.end.clientX;

      let elmtW = elmt?.offsetWidth;

      let swipeTopToBtm = this.swipeEvent(cursorTop, cursorBottom);
      let swipeLeftToRight = this.swipeEvent(cursorLeft, cursorRight);

      let touch = false; // false === Swipe || True === Click

      if (!elmt) return;

      if (swipeTopToBtm || swipeLeftToRight) {
        touch = false;
      } else {
        touch = true;
      }
      dropdown.setAttribute('style', `--top:${top}px; --left:${left}px; --width:${elmtW}px;`);

      if (touch) {
        // Click
        if (elmt.classList.contains('active')) {
          // Handle Deactive
          elmt.classList?.remove('active');
          dropdown.classList.contains('menu-hide') ? dropdown.classList.remove('menu-hide') : '';
          dropdown.classList.contains('menu-show-default') || dropdown.classList.contains('menu-show-last')
            ? dropdown.classList.remove('menu-show-default') || dropdown.classList.remove('menu-show-last')
            : '';
        } else {
          // Handle -> Active
          elmt.classList?.add('active');
          if (index === lastIndex && index > 0) {
            dropdown.classList?.remove('menu-show-last');
            dropdown.classList?.contains('menu-show-default') ? '' : dropdown.classList.add('menu-show-default');
          } else {
            dropdown.classList?.remove('menu-show-default');
            dropdown.classList?.contains('menu-show-last') ? '' : dropdown.classList.add('menu-show-last');
          }
          window.addEventListener('touchmove', handleTouchedEvent);
          window.addEventListener('touchstart', handleTouchedEvent);
          window.addEventListener('touchend', handleTouchedEvent);
        }
        return;
      }
      // swipe
      if (cursorTop > elmtTop && cursorBottom < elmtBtm && cursorLeft > elmtLeft && cursorRight < elmtRight) {
        if (index === lastIndex && index > 0) {
          dropdown.classList?.contains('menu-show-default') ? '' : dropdown.classList.add('menu-show-default');
        } else {
          dropdown.classList?.contains('menu-show-last') ? '' : dropdown.classList.add('menu-show-last');
        }
      } else {
        return;
      }

      function handleTouchedEvent(ev) {
        let { x, y, width, height } = dropdown.getBoundingClientRect();
        let PosX = x + width;
        let PosY = y + height;
        let RectX = ev.changedTouches[0].clientX; // current touch left
        let RectY = ev.changedTouches[0].clientY; // current touch top

        if (RectX > x && RectX < PosX && RectY > y && RectY < PosY) {
          return;
        } else {
          handleHideDropdown();
          window.removeEventListener('touchmove', handleTouchedEvent);
          window.removeEventListener('touchstart', handleTouchedEvent);
          window.removeEventListener('touchend', handleTouchedEvent);
        }
      }
      function handleHideDropdown() {
        if (elmt.classList.contains('active')) {
          // Already Active
          elmt.classList?.remove('active');
          dropdown.classList.contains('menu-hide') ? dropdown.classList.remove('menu-hide') : '';
          dropdown.classList.contains('menu-show-default') || dropdown.classList.contains('menu-show-last')
            ? dropdown.classList.remove('menu-show-default') || dropdown.classList.remove('menu-show-last')
            : '';
        }
      }
    },

    handleHideDropdown() {
      let elmt = document.querySelectorAll('.planningStatus-btn');
      let dropdown = document.querySelector('#dropdown-menu'); // get dropdown item
      if (!elmt) return;

      let len = elmt.length;

      while (len--) {
        if (elmt[len]?.classList.contains('active')) {
          // Already Active
          elmt[len]?.classList?.remove('active');
        }
      }

      dropdown.classList.contains('menu-hide') ? dropdown.classList.remove('menu-hide') : '';
      dropdown.classList.contains('menu-show-default') || dropdown.classList.contains('menu-show-last')
        ? dropdown.classList.remove('menu-show-default') || dropdown.classList.remove('menu-show-last')
        : '';
    },

    swipeEvent(touchStart, touchEnd) {
      const RANGE_DEFINE = 40;
      if (touchStart - touchEnd > RANGE_DEFINE || touchStart - touchEnd < -RANGE_DEFINE) return true;
      return false;
    },

    HOFClosePopup(fn) {
      return async (...params) => {
        this.handleHideDropdown();
        return await fn(...params);
      };
    },

    async slideLeft(item) {
      /**
       * right to left
       * logic:
       * 1 - Right or Default : have class bg-to-right or class background-e3e3e3
       * 2 - Left or Default: Have class bg-to-left or nothing
       * Check -> If ( Right || Default) -> Remove class bg-to-right -> add class bg-to-left
       * Check -> If (Left or Default) => return;
       */
      let result = null;
      try {
        await new Promise((resolve) => setTimeout(resolve, 0)); // 1st -> doing remove bg-to-right

        item.classList?.remove('bg-to-right');

        await new Promise((resolve) => setTimeout(resolve, 50)); // last -> remove bg

        item.classList?.remove('active');

        await new Promise((resolve) => setTimeout(resolve, 100)); // then -> add bg-to-left

        item.classList.add('bg-to-left');

        return;
      } catch (err) {
        // console.log(err);
      } finally {
        result = true;
      }
      return result;
    },
    /**
     * Item : Selector
     * updateFlag: -> setState
     * type: setSelect(e)  => e : Value -> 0 || 1 || 2 || 3 || 4
     */
    async slideRight(item) {
      /**
       * Left to Right
       * logic:
       * 1 - Right or Default : have class bg-to-right or class background-e3e3e3
       * 2 - Left or Default: Have class bg-to-left or nothing
       * Check -> If ( Right || Default) -> return
       * Check -> If (Left or Default) => return;
       */
      try {
        await new Promise((resolve) => setTimeout(resolve, 0)); // 1st -> doing remove bg-to-left
        item.classList?.remove('active'); // Tranfer animation
        await new Promise((resolve) => setTimeout(resolve, 50)); // last -> remove bg
        item.classList?.remove('bg-to-left');
        await new Promise((resolve) => setTimeout(resolve, 100)); // then -> add bg-to-left
        item.classList.add('bg-to-right'); // Avoid multiple class
        return;
      } catch (err) {
        // console.log(err);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.wrapContent {
  height: calc(100% - 54px);
}
.tblStock-manage {
  position: relative;
  overflow: hidden;
  .application-btn {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    height: 0;
    margin: auto;
    z-index: 99;
    .btn {
      position: fixed;
      height: 46px;
      width: 52px;
      padding: 0;
      background: rgba(63, 75, 86, 0.4);
      z-index: 1;
      &.btn--prev {
        left: 84px;
        border-radius: 0px 60px 60px 0px;
        z-index: 9;
      }
      &.btn--next {
        right: 24px;
        border-radius: 60px 0px 0px 60px;
        z-index: 5;
        .svg {
          transform: rotate(-180deg);
        }
      }
    }
  }
}
.custom-checkbox-gmail {
  width: 19px;
  height: 18px;
  background: url(~@/assets/template/images/checkbox-i01-s02.svg) no-repeat !important;
  top: calc(50% - 7px);
}
.custom-checkbox-k1 {
  cursor: pointer;
  &:hover {
    .abc {
      display: block;
    }
  }
}
.pagination {
  box-shadow: inset 0px 7px 12px #e3e3e3;
  background: #f2f2f2;
  z-index: 9;
}
.checkAll1 {
  width: 43px;
  height: 28px;
  background: rgba(255, 255, 255, 0.5);
  position: absolute;
  left: -6px;
  top: -5px;
  border-radius: 4px;
  opacity: 0.9;
}

.checkAll-drop {
  position: relative;
  z-index: 9999;
  &.show {
    &:after {
      position: absolute;
      top: -4px;
      right: -21px;
      content: '';
      display: block;
      height: 28px;
      width: 46px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 2px;
    }
    .btn {
      &.btn--arrow {
        &:after {
          border-top: 6px solid #2d3033;
        }
      }
    }
  }
  .btn {
    &.btn--arrow {
      padding: 0;
      border: none;
      height: 24px;
      width: 30px;
      position: relative;
      &:after {
        position: absolute;
        top: 11px;
        right: 2px;
        content: '';
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid #fff;
        z-index: 1;
      }
    }
    &:focus {
      box-shadow: none;
    }
  }
}

.abc {
  width: 28px;
  height: 28px;
  background: rgba(255, 255, 255, 0.5);
  display: none;
  position: absolute;
  left: -6px;
  top: -6px;
  border-radius: 4px;
  opacity: 0.9;
}
.abcde {
  top: -4px;
}
.checkAll-drop2 {
  &:hover {
    .abcd {
      display: block;
    }
  }
  .abcd {
    width: 15px;
    height: 27px;
    background: rgba(255, 255, 255, 0.5);
    display: none;
    border-radius: 4px;
    position: absolute;
    left: 4px;
    opacity: 0.9;
    top: -4px;
  }
}
.checkAll .checkAll-drop .dropdown-select ul li {
  color: #2d3033 !important;
  font-weight: 500 !important;
}
.checkAll-drop {
  li {
    &:hover {
      background: #eef6ff !important;
      color: #2d3033 !important;
      font-weight: 500 !important;
    }
  }
  .btn--arrow {
    width: 8px !important;
    left: 12px;
    top: -3px;
    position: absolute !important;
  }
}
.btn-disabled,
.btn-disabled[disabled] {
  opacity: 0.4;
  cursor: default !important;
  pointer-events: none;
}

.el-tag-custom {
  height: 30px;
  line-height: 26px;
  font-size: 14px;
  align-items: center;
  margin-right: 10px !important;
  background: #d1e4ff;
  border-radius: 24px;
  color: #225999;
}

.dropdown-filter {
  top: 0;
  &.show {
    z-index: 1002;
  }
  .item-filter {
    background: #448add;
    box-shadow: 0px 2px 4px rgba(13, 94, 153, 0.1), 0.5px 1px 5px 1px rgba(203, 225, 241, 0.3);
    border-radius: 0px 0px 0px 8px;
    width: 42px;
    height: 42px;
    right: 0;
    top: 0;
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .title-filter {
    font-size: 1.125rem;
    font-weight: 700;
    color: #5f6b73;
    padding-bottom: 6px;
  }
  .formGoup-filter {
    > ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -10px;
      > li {
        width: 50%;
        padding-left: 10px;
        margin-top: 10px;
        .formGoup-filter-label {
          font-size: 0.875rem;
          color: #5f6b73;
          margin-bottom: 6px;
        }
      }
    }
  }
  .btnSelect {
    display: flex;
    flex-wrap: wrap;
    li {
      width: 33.333333%;
      margin-left: -1px;
      margin-bottom: 10px;
      cursor: pointer;
      border-radius: 0;
      padding: 0;
      height: 40px;
      text-align: center;
      vertical-align: middle;
      padding: 0.375rem 0.75rem;
      line-height: 1.5;
      color: #5f6b73;
      font-size: 0.875rem;
      border: 1px solid #727f88;
      background: #fff;
      &:first-of-type {
        border-radius: 4px 0 0 4px;
      }
      &:last-child {
        border-radius: 0 4px 4px 0;
      }
      &.btn-select {
        position: relative;
        &.active {
          z-index: 1;
          border: 2px solid #448add;
          color: #448add;
          background: #eef6ff;
          font-weight: 700;
        }
        &:hover {
          background: #eef6ff;
          font-weight: 700;
          color: #5f6b73;
        }
      }
    }
  }
  .btnFilter {
    text-align: center;
    margin-top: 24px;
    .btn {
      width: 142px;
      margin-right: 14px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.checkAll {
  display: flex;
  justify-content: center;
  .custom-control {
    .custom-control-input {
      display: none;
    }
  }
}
.tblStock-manage {
  overflow: auto;
  max-height: 100%;
  min-height: 100%;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
  border-radius: 10px;
  margin: 0 24px;
  background: transparent;
  @media (max-width: $viewport_tablet) {
    margin: 0 12px;
  }
  tr {
    &:last-child {
      border-radius: 0 0 10px 10px;
      td {
        .dropdown-tools {
          top: inherit !important;
          bottom: 0 !important;
          .btn-show {
            top: inherit;
            bottom: 0px !important;
          }
        }
      }
    }
    &:first-of-type {
      td {
        .dropdown-tools {
          top: 0 !important;
          bottom: inherit !important;
          .btn-show {
            top: 0;
          }
        }
      }
    }
    th,
    td {
      vertical-align: middle;
      min-width: 120px;
      &:nth-child(1) {
        width: 1rem;
        min-width: 60px;
        text-align: center;
      }
      &:nth-child(2) {
        text-align: left;
        width: 1rem;
        min-width: 320px;
        @media (max-width: $viewport_tablet) {
          min-width: 120px;
        }
        @media (max-width: $viewport_desktop) {
          min-width: 180px;
        }
      }
      &:nth-child(3),
      &:nth-child(5) {
        text-align: left;
        width: 1rem;
        min-width: 80px;
        @media (max-width: $viewport_tablet) {
          min-width: 60px;
        }
        @media (max-width: $viewport_desktop) {
          min-width: 60px;
        }
      }
      &:nth-child(4),
      &:nth-child(6) {
        text-align: left;
        width: 1rem;
        @media (max-width: $viewport_tablet) {
          min-width: 80px;
        }
        @media (max-width: $viewport_desktop) {
          min-width: 80px;
        }
      }
      &:nth-child(7) {
        text-align: left;
        width: 1rem;
        min-width: 100px;
        @media (max-width: $viewport_tablet) {
          min-width: 85px;
        }
        @media (max-width: $viewport_desktop) {
          min-width: 85px;
        }
      }
      &:nth-child(8) {
        width: 1rem;
        padding-right: 16px;
        @media (max-width: $viewport_tablet) {
          min-width: 85px;
        }
        @media (max-width: $viewport_desktop) {
          min-width: 85px;
        }
      }
    }
    td {
      @media (max-width: $viewport_tablet) {
        padding: 5px 8px;
      }
      &:nth-child(6),
      &:nth-child(7) {
        text-align: left;
      }
      .custom-control {
        padding-left: 19px;
      }
      .tblStock-manage-doctor {
        font-size: 1.125rem;
        font-weight: 700;
      }
      .span-label {
        border-radius: 2px;

        @media (max-width: $viewport_desktop) {
          padding: 6px;
        }
      }
      .planningStatus {
        display: flex;
        align-items: center;
        justify-content: space-between;
        .planningStatus-btn {
          min-width: 42px;
          position: relative;
          margin-left: 20px;
          z-index: 1;
          @media (max-width: $viewport_tablet) {
            margin-left: 8px;
          }
          &.show {
            z-index: 2;
          }
          .btn {
            &.btn--icon {
              display: flex;
              justify-content: center;
              align-items: center;
              span {
                width: 4px;
                height: 4px;
                background: #448add;
                border-radius: 50%;
                display: inline-block;
                margin: 0 2px;
              }
            }
          }
        }
      }
      .dropdown-tools {
        left: inherit !important;
        right: 0 !important;
        transform: none !important;
        margin: 0;
        padding: 0;
        background: #ffffff;
        box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
        border-radius: 20px 24px 20px 20px;
        border: none;
        min-height: 42px;
        width: 186px;
        .btn-show {
          box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
          background: #448add;
          padding: 0;
          width: 42px;
          height: 42px;
          border-radius: 50%;
          display: flex;
          justify-content: center;
          align-items: center;
          position: absolute;
          top: 0;
          right: 0;
          span {
            width: 4px;
            height: 4px;
            background: #fff;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
          }
        }
        ul {
          border-radius: 20px 24px 20px 20px;
          overflow: hidden;
          li {
            padding: 6px 46px 6px 20px;
            cursor: pointer;
            color: #448add;
            font-weight: 500;
            font-size: 0.875rem;
            display: flex;
            &:hover {
              background: #f2f2f2;
            }
            .item {
              min-width: 18px;
              margin-right: 4px;
              text-align: center;
            }
          }
        }
      }
    }
  }

  .tblStock-more {
    text-align: center;
    margin-top: 10px;
    a {
      padding-right: 20px;
      position: relative;
      &.active {
        &::after {
          transform: rotate(180deg);
        }
      }
      &::after {
        position: absolute;
        top: 6px;
        right: 0;
        content: '';
        width: 12px;
        height: 8px;
        display: block;
        background: url(~@/assets/template/images/icon_arrow-line-slider.svg) no-repeat;
        background-size: 12px auto;
        transition: 400ms all;
      }
    }
  }
  .ps__rail-y {
    z-index: 2;
  }
}

.groupContent {
  .ps__rail-y {
    margin-top: 50px;
    margin-bottom: 20px;
    right: 10px;
  }
}
table {
  border-spacing: 0 !important;
  .cellFake {
    opacity: 0;
    height: 150px;
    td {
      border: none;
    }
  }
}
.body-gray {
  position: relative;
  &:before {
    position: absolute;
    content: '';
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #f2f2f2;
    z-index: -1;
  }
  tr {
    background-color: #fff !important;
  }
}

.active {
  z-index: 2 !important;
  .btn {
    background: #448add;
    span {
      background: #fff !important;
    }
  }
}
#dropdown-menu {
  width: 184px;
  background: #fff;
  box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
  border-radius: 20px 20px 20px 20px;
  z-index: 1;
  position: fixed;
  display: block;
  ul {
    border-radius: 20px 20px 20px 20px;
    overflow: hidden;
    li {
      padding: 6px 46px 6px 20px;
      cursor: pointer;
      color: #448add;
      font-weight: 500;
      font-size: 0.875rem;
      display: flex;
      &:hover {
        background: #f2f2f2;
      }
      .item {
        min-width: 18px;
        margin-right: 4px;
        margin-top: -2px;
        text-align: center;
      }
    }
  }
}
.menu-show-default {
  opacity: 1;
  visibility: visible;
  top: var(--top);
  left: var(--left);
  transform: translateX(calc(-100% + var(--width))) translateY(calc(-100% + var(--width)));
}
.menu-show-last {
  opacity: 1;
  visibility: visible;
  display: block;
  top: var(--top);
  left: var(--left);
  transform: translateX(calc(-100% + var(--width)));
}
.menu-hide {
  opacity: 0;
  visibility: hidden;
}

.bg-to-left {
  background-size: 200%;
  animation: slide-to 1s;
  animation-fill-mode: forwards;
  animation-delay: 0.08s;
  transform: translateX(0);
}
.bg-to-right {
  background-size: 200%;
  animation: slide-to 1s;
  animation-fill-mode: forwards;
  animation-direction: reverse;
  animation-delay: 0.08s;
  transform: translateX(0);
}
tr.active {
  background: #e3e3e3;
}

@keyframes slide-to {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(100%);
  }
}
</style>
