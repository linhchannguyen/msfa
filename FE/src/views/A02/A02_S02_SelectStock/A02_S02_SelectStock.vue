<template>
  <!-- modal -->
  <!-- screen name: ストック選択
        width: modal-dialog--mw1210 -->
  <!-- Start  -->
  <div class="modal-body modal-selectStock modal-body-A02S02">
    <div id="msfa-notify-A02S02"></div>
    <div class="selectStock-filter">
      <button type="button" class="btn btn-link btn-filter" @click="showFilter">
        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
      </button>
      <div v-click-outside-event="clickOutside" :class="`dropdown-menu dropdown-filter ${isShowFilter ? 'show' : ''}`">
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
                  <span class="icon" @click="openModalZ05_S06_Material_Selection" @touchstart="openModalZ05_S06_Material_Selection">
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
              <label class="formGoup-filter-label">個人名</label>
              <div class="formGoup-filter-control">
                <el-input v-model="filterSearch.person_name" clearable placeholder="名前入力" class="form-control-input" />
              </div>
            </li>
          </ul>
        </div>
        <div class="btnFilter">
          <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelFilter">キャンセル</button>
          <button type="button" class="btn btn-filter-submit" @click="search()">検索</button>
        </div>
      </div>
    </div>
    <!-- table -->
    <div ref="tblStock" class="tblStock scrollbar" @scroll="onScroll">
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
                    :class="countCheckbox > 0 && countCheckbox !== dataTable.length ? 'custom-checkbox-gmail' : ''"
                    class="custom-control-indicator custom-checkbox-k1"
                  >
                    <span :class="isCheckAll ? 'abcde' : ''" class="abc"></span>
                  </span>
                </label>
                <div :class="`checkAll-drop  checkAll-drop2 ${isShowDropdownTable ? 'show' : ''}`">
                  <span class="abcd"></span>
                  <button data-toggle="dropdown" type="button" :class="`btn btn--arrow ${isShowDropdownTable ? 'show' : ''}`" @click="showDropdownTable">
                    &nbsp;
                  </button>
                  <div v-click-outside-event="clickOutside" :class="`dropdown-menu dropdown-select ${isShowDropdownTable ? 'show' : ''}`">
                    <ul>
                      <li
                        :class="`${isCheckAll === true && isCheckAllCount === false ? 'btn-disabled' : ''}`"
                        @click="checkAllRecord(true)"
                        @touchstart="checkAllRecord(true)"
                      >
                        すべて選択
                      </li>
                      <li :class="`${isCheckAll === false ? 'btn-disabled' : ''}`" @click="checkAllRecord(false)" @touchstart="checkAllRecord(false)">
                        すべて解除
                      </li>
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
        <Preloader v-if="isLoadingTable" style="position: absolute; left: 50%; top: 50%" />
        <tbody v-if="!isLoadingTable">
          <tr v-for="data in dataTable" :key="data">
            <td style="cursor: pointer" @click="onCheckItem(data)" @touchstart="onCheckItem(data)">
              <label class="custom-control custom-checkbox custom-checkbox-c" @click="onCheckItem(data)" @touchstart="onCheckItem(data)">
                <input class="custom-control-input" type="checkbox" :checked="data.isCheck" @touchstart="onCheckItem(data)" @change="onCheckItem(data)" />
                <span class="custom-control-indicator" @touchstart="onCheckItem(data)" @click="onCheckItem(data)"></span>
              </label>
            </td>
            <td>
              <p>{{ data.facility_short_name }}病院　{{ data.department_name }}</p>
              <p class="tblStock-doctor">
                <span class="tblStock-bold">{{ data.person_name }}</span> 医師
              </p>
            </td>
            <td>{{ data.content_name }}</td>
            <td>
              <p>{{ showArrayListText('product_name', data.product_list) }}</p>
            </td>
            <td>
              <a v-if="data.document_list.length > 0" class="link material-child" @click="openModalA02_S03_ModalMaterialList(data)">あり</a>
              <p v-else>なし</p>
            </td>
            <td class="white-space-wrap">
              <span
                v-if="data.stock_type === '20'"
                class="link"
                @click="handleRouting({ name: 'C01_S02_LectureInput', params: { convention_id: data.action_id } })"
                >講演会</span
              >
              <span
                v-else-if="data.stock_type === '10'"
                class="link"
                @click="handleRouting({ name: 'B01_S02_BriefingSessionInput', params: { briefing_id: data.action_id } })"
              >
                説明会</span
              >

              <p v-else>ターゲット</p>
            </td>
            <td class="white-space-wrap">{{ formatFullDate(data.stock_date) }}</td>
            <td>
              <span v-if="data.status_type === 10 || data.status_type === '10'" class="span-label span-label--greenFl">未計画</span>
              <span v-else class="span-label span-label--gray">計画済</span>
            </td>
          </tr>
        </tbody>
      </table>
      <EmptyData v-if="dataTable.length === 0 && !isLoadingTable" custom-class="custom-class-no-data" in-height="200" in-width="200" />
    </div>
    <div class="btnStock">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" :disabled="isLoading" @click="$emit('onFinishScreen')">
        キャンセル
      </button>
      <button type="button" class="btn btn-primary" :disabled="isLoading" @click="returnData">
        <i :class="['el-icon-loading', isLoading ? 'inline-block' : '']"></i> 決定
      </button>
    </div>
  </div>
  <!-- End -->
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component
        :is="modalConfig.component"
        v-bind="{ ...modalConfig.props }"
        @onFinishScreen="onResultModal"
        @onFinishScreenA02S03="onResultModalA02S03"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
import { mapGetters } from 'vuex';
import { markRaw } from 'vue';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';
import A02_S03_ModalMaterialList from '@/views/A02/A02_S03_StockManagement/A02_S03_ModalMaterialList';

export default {
  name: 'A02_S02_SelectStock',
  components: {
    Z05_S06_Material_Selection
  },
  props: {
    inFacilityCd: {
      type: Array,
      default: () => []
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen', 'changePointerEvent'],
  data() {
    return {
      isShowFilter: false,
      filterSearch: {
        content_cd: '', //R
        product_list: [], //R
        status_type: 1, // '',0,1
        facility_cd: this.inFacilityCd.length > 0 ? [this.inFacilityCd] : [],
        facility_name: '',
        facility_category_type: '',
        person_name: '',
        limit: 100,
        offset: 0
      },
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        customClass: 'custom-dialog'
      },
      props_Z05_S06_Material_Selection: {
        mode: 'multiple',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },
      dataTable: [],
      isLoadingTable: false,
      isLoading: false,
      dataTableSelected: [],
      isCheckAll: false,
      isCheckAllCount: false,
      isShowDropdownTable: false,
      countCheckbox: 0,
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false
    };
  },
  computed: {
    ...mapGetters({
      facilityCategory: 'A02/a02_S03_all_facility_category',
      allContent: 'A02/a02_S03_all_content',
      filter: 'router/isHistoryFilter',
      modalStatus: 'router/isHistoryModal'
    })
  },
  watch: {
    filterSearch(value) {
      this.props_Z05_S06_Material_Selection.initDataCodes = this.formatArrayString('product_cd', value.product_list);
    },
    dataTable(value) {
      let countChecked = 0;
      this.dataTableSelected = value.filter((item) => {
        if (item.isCheck === true) {
          countChecked += 1;
          return item;
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
    }
  },
  created() {
    if (!this.facilityCategory || !this.allContent) {
      this.getScreenData();
    }
  },
  mounted() {
    if (this.modalStatus && this.filter) {
      let { status_type, person_name, content_cd, facility_cd } = this.filter;
      this.setStatusTypeFilter(status_type);
      this.filterSearch = { ...this.filterSearch, content_cd, person_name, facility_cd };
      this.search(this.filter);
    } else this.search();
    this.js_pscroll();
  },
  methods: {
    clickOutside() {
      if (this.isShowFilter) {
        this.cancelFilter();
      }
      if (this.isShowDropdownTable) {
        this.cancelDropdownTable();
      }
    },
    onScrollLeft() {
      let content = document.querySelector('.tblStock');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblStock');
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
        let content = document.querySelector('.tblStock');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    formatArrayString(paramName, arrayObject) {
      return arrayObject?.map((item) => item[paramName]);
    },
    getScreenData() {
      this.$store.dispatch('A02/getAllFacilityCategoryAction');
      this.$store.dispatch('A02/getAllContentAction');
    },
    //Handle Filter
    setStatusTypeFilter(status_type) {
      this.filterSearch = { ...this.filterSearch, status_type: status_type };
    },
    showFilter() {
      this.isShowFilter = !this.isShowFilter;
      this.$emit('changePointerEvent', !this.isShowFilter);
    },
    cancelFilter() {
      this.isShowFilter = false;
      this.$emit('changePointerEvent', true);
    },
    handleRemoveProduct(item) {
      this.filterSearch = { ...this.filterSearch, product_list: this.filterSearch.product_list.filter((x) => x.product_cd !== item.product_cd) };
    },
    //Modal
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    onResultModalA02S03() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    onResultModal(data) {
      if (data && data.screen === 'Z05_S06') {
        this.onResultModal_Z05_S06_Material_Selection(data);
      }
      this.onCloseModal();
    },
    openModalZ05_S06_Material_Selection() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05_S06_Material_Selection),
        props: {
          ...this.props_Z05_S06_Material_Selection
        },
        width: '42rem',
        customClass: 'custom-dialog modal-fixed'
      };
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
    openModalA02_S03_ModalMaterialList(data) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材一覧',
        isShowModal: true,
        component: markRaw(A02_S03_ModalMaterialList),
        props: {
          inDocumentId: data.document_list
        },
        width: '558px',
        customClass: 'custom-dialog modal-fixed modal-fixed-min'
      };
    },
    showDropdownTable() {
      this.isShowDropdownTable = !this.isShowDropdownTable;

      this.$emit('changePointerEvent', !this.isShowDropdownTable);
    },
    cancelDropdownTable() {
      this.isShowDropdownTable = false;
      this.$emit('changePointerEvent', true);
    },
    async search(filterList = null) {
      let filter;

      if (filterList) {
        filter = filterList;
      } else {
        filter = {
          ...this.filterSearch,
          product_cd: this.props_Z05_S06_Material_Selection.initDataCodes,
          product_list: null
        };
      }

      this.$store.dispatch('router/setScreenInfor', { filter });

      this.cancelFilter();
      this.isLoadingTable = true;
      A02_S03_StockManagementServices.searchStockService(filter)
        .then((res) => {
          this.dataTable = res.data.data.records.map((item) => {
            return { ...item, isCheck: false };
          });
          if (this.$refs.tblStock) {
            this.$refs.tblStock.scrollTop = 0;
          }
          this.checkAllRecord(false);
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A02S02',
            idNodeNotify: 'msfa-notify-A02S02'
          });
        })
        .finally(() => (this.isLoadingTable = false));
    },
    showArrayListText(itemName, arrayObject) {
      let text = '';
      arrayObject.map((item) => (text += item[itemName] + ', '));
      return text?.substring(0, text.length - 2);
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
        }
      });
      if (this.dataTable.length === this.countCheckbox) {
        this.countCheckbox = 0;
      }
    },
    checkAllRecord(status) {
      switch (status) {
        case true:
          this.countCheckbox = this.dataTable.length;
          this.isCheckAll = true;
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
      this.cancelDropdownTable();
    },
    returnData() {
      this.isLoading = true;
      if (this.dataTableSelected.length <= 0) {
        this.notifyModal({
          message: 'ストックを選択してください',
          type: 'error',
          classParent: 'modal-body-A02S02',
          idNodeNotify: 'msfa-notify-A02S02'
        });
      } else {
        this.$emit('onFinishScreen', { screen: 'A02_S02_SelectStock', dataSelected: this.dataTableSelected });
      }

      this.isLoading = false;
    },
    handleRouting(object) {
      let { name, params } = object;
      let modal = true;
      this.$store.dispatch('router/setScreenInfor', { name, params, modal });
      this.$router.push({ name, params });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.custom-checkbox-c {
  input {
    width: 61px;
    height: 59px;
    top: -18px;
    left: -21px;
    cursor: pointer !important;
  }
}
.tblStock {
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
        border-radius: 0px 60px 60px 0px;
        z-index: 9;
      }
      &.btn--next {
        right: 133px;
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

.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 23%;
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

.checkAll .checkAll-drop .dropdown-select ul li {
  color: #2d3033 !important;
  font-weight: 500 !important;
}
.checkAll-drop {
  position: relative;
  z-index: 9999;
  &.show {
    &:after {
      position: absolute;
      top: -4px;
      right: -19px;
      content: '';
      display: block;
      height: 27px;
      width: 44px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 4px;
    }
    .btn {
      &.btn--arrow {
        &:after {
          border-top: 6px solid #2d3033;
        }
      }
    }
  }
}
.checkAll {
  justify-content: center;
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
    padding: 0;
    border: none;
    height: 24px;
    width: 23px !important;
    position: absolute !important;
    top: -2px;
    &:after {
      right: 3px;
    }
  }
}
.white-space-wrap {
  white-space: nowrap;
}

.custom-class-no-data {
  height: 256px;
}
//disable
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
.modal-selectStock {
  padding: 0 32px 32px 32px;
  @media (max-width: $viewport_desktop) {
    padding: 0 16px 32px;
  }
}
.dropdown-filter {
  top: 0;
}
.selectStock-filter {
  text-align: right;
  position: relative;
  .btn {
    &.btn-filter {
      padding: 0;
      width: 42px;
      height: 42px;
      border-radius: 0 0 8px 8px;
    }
  }
  .dropdown-menu--filter {
    background: #f9f9f9;
    box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
    border-radius: 10px;
    width: 400px;
    border: none;
    left: inherit !important;
    right: 0px !important;
    transform: none !important;
    min-height: 350px;
    padding: 20px;
    margin: 0;
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
  .custom-control {
    .custom-control-input {
      display: none;
    }
  }
}
.tblStock {
  overflow: auto;
  height: 350px;
  box-shadow: 0 0 8px #e3e3e3;
  -moz-border-radius: 10px;
  border-radius: 10px;
  background: #fff;
  margin-top: 20px;
  tr {
    th,
    td {
      vertical-align: middle;
      &:nth-child(1) {
        width: 1rem;
        min-width: 69px;
        text-align: center;
      }
      &:nth-child(5) {
        min-width: 60px;
      }
    }
    td {
      &:nth-child(6),
      &:nth-child(7) {
        text-align: left;
      }
      .custom-control {
        padding-left: 19px;
      }
      .tblStock-doctor {
        .tblStock-bold {
          font-size: 1.125rem;
          font-weight: 700;
          vertical-align: baseline;
        }
      }
      .span-label {
        border-radius: 2px;
        min-width: 88px;
      }
    }
    th {
      &:nth-child(2) {
        width: 280px;
      }
      &:nth-child(3) {
        width: 130px;
      }
      &:nth-child(4) {
        width: 130px;
      }
      &:nth-child(5) {
        width: 64px;
      }
      &:nth-child(6) {
        width: 85px;
      }
      &:nth-child(7) {
        width: 100px;
      }
      &:nth-child(8) {
        width: 119px;
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
}
.btnStock {
  margin-top: 20px;
  text-align: center;
  .btn {
    width: 150px;
    margin-right: 10px;
    &:last-child {
      margin-right: 0;
    }
  }
}
.dropdown-menu.dropdown-filter,
.dropdown-menu.dropdown-select,
.btn.btn-filter {
  pointer-events: auto;
}

.link {
  color: #448add !important;
}
</style>
