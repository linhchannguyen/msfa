<template>
  <!-- title: 講演会シリーズ選択  -->
  <!-- props: none -->
  <!-- width: 40%, 45rem -->
  <div v-load-f5="true" class="wrapContent modal-body-C01S04">
    <div id="msfa-notify-C01S04"></div>
    <div class="LectureSeriesSelection">
      <div class="lecture-left">
        <div class="lectureGroup">
          <label class="lectureGroup-label">品目</label>
          <div class="lectureGroup-form-control">
            <div class="form-icon icon-right">
              <span class="icon log-icon" title_log="品目" @click="openModal_Z05_S06">
                <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
              </span>
              <div v-if="initParams.product_name" class="form-control d-flex align-items-center">
                <div class="block-tags">
                  <el-tag class="m-0 el-tag-custom" closable @close="removeTag()">
                    {{ initParams.product_name }}
                  </el-tag>
                </div>
              </div>
              <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
            </div>
          </div>
        </div>
        <div class="lectureGroup">
          <label class="lectureGroup-label">講演会名</label>
          <el-input v-model="initParams.convention_name" clearable placeholder="講演会名を入力" class="form-control-input" />
        </div>
        <div class="lectureGroup">
          <label class="custom-control custom-checkbox custom-control--bordGreen">
            <input v-model="initParams.series_flag" class="custom-control-input" type="checkbox" checked />
            <span class="custom-control-indicator"></span>
            <span class="custom-control-description">シリーズのみ</span>
          </label>
        </div>
        <div class="lecture-btnSearch">
          <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="search">
            <span class="btn-iconLeft">
              <ImageSVG :src-image="'icon_search.svg'" :alt-image="'icon_search'" />
            </span>
            <span>検索</span>
          </button>
        </div>
      </div>
      <div v-if="listLectures.length > 0" v-loading="isLoading" class="lecture-right">
        <ul ref="listLectureRef" class="lecture-list scrollbar">
          <li
            v-for="(item, index) in listLectures"
            :key="index"
            :class="`lecture-item ${selectRecord.convention_id === item.convention_id ? 'active' : ''}`"
            @click="selected(item)"
          >
            {{ item.convention_name }}
          </li>
        </ul>
        <div class="lecture-pagination">
          <Pagination
            class-pagination="custom-pagi"
            :current-page="pagination.current_page"
            :total-page="pagination.total_items"
            :page-size="initParams.limit"
            @current-change="handleChangePage"
          />
        </div>
      </div>
      <EmptyData v-else custom-class="customClassEmpty" thumb-margin-top="20px" />
    </div>
    <div class="LectureSeriesSelection-btn">
      <button type="button" :disabled="processingRegister" class="btn btn-outline-primary btn-outline-primary--cancel" @click="onCloseModal()">
        キャンセル
      </button>
      <el-button
        type="primary"
        class="btn btn-primary"
        :loading="processingRegister"
        :disabled="processingRegister || !selectRecord.convention_id"
        @click.prevent="register()"
      >
        決定
      </el-button>
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closeModal"></component>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import C01_S04_Service from '@/api/C01/C01_S04_LectureSeriesSelectionServices';
import Z05_S06_MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
export default {
  name: 'C01_S04_LectureSeriesSelection',
  props: {
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      pagination: {
        current_page: 1,
        first_page: 1,
        last_page: 1,
        next_page: 1,
        previous_page: 1,
        total_items: 6,
        total_pages: 1
      },
      props_Z05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },
      listLectures: [],
      isLoading: false,
      initParams: {
        product_cd: '',
        product_name: '',
        convention_name: '',
        series_flag: true,
        limit: 100,
        offset: 0
      },
      selectRecord: {
        convention_id: null,
        convention_name: null,
        series_flag: null
      },
      processingRegister: false
    };
  },
  mounted() {
    // this.js_pscroll();
  },
  methods: {
    // define func
    search() {
      this.getList({
        ...this.initParams,
        series_flag: this.initParams.series_flag ? 1 : 0
      });
    },
    removeTag() {
      this.initParams.product_cd = '';
      this.initParams.product_name = '';
    },
    selected(record) {
      this.selectRecord = record;
    },
    handleChangePage(page) {
      this.pagination.current_page = page;
      this.getList({
        ...this.initParams,
        series_flag: this.initParams.series_flag ? 1 : 0,
        offset: page === 0 ? page : page - 1
      });
    },
    // modal
    openModal_Z05_S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: this.markRaw(Z05_S06_MaterialSelection),
        width: '33rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: this.props_Z05S06
      };
    },
    closeModal(data) {
      this.modalConfig.isShowModal = false;
      this.props_Z05S06 = {
        ...this.props_Z05S06,
        categoryCode: data.category.definition_value,
        classificationCode: data.classifications.product_group_cd,
        initDataCodes: data.dataSelected
      };
      this.initParams = {
        ...this.initParams,
        product_cd: data.dataSelected[0].product_cd,
        product_name: data.dataSelected[0].product_name
      };
    },
    onCloseModal(data_return) {
      this.$emit('onFinishScreen', { screen: 'C01_S04', ...data_return });
    },
    // call api
    getList(params) {
      // this.js_pscroll();
      this.isLoading = true;
      C01_S04_Service.getListService(params)
        .then((res) => {
          const dataRes = res.data.data;
          this.listLectures = dataRes.records;
          this.pagination = dataRes.pagination;
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-C01S04', idNodeNotify: 'msfa-notify-C01S04' });
        })
        .finally(() => {
          this.isLoading = false;
          if (this.$refs.listLectureRef) {
            this.$refs.listLectureRef.scrollTop = 0;
          }
        });
    },
    register() {
      if (!this.selectRecord.convention_id) return;
      this.processingRegister = true;
      C01_S04_Service.registerService({
        convention_id: this.selectRecord.convention_id,
        parent_series_flag: this.selectRecord.parent_series_flag,
        series_convention_id: this.selectRecord.series_convention_id
      })
        .then(() => {
          this.onCloseModal(this.selectRecord);
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-C01S04', idNodeNotify: 'msfa-notify-C01S04' });
        })
        .finally(() => {
          this.isLoading = false;
          this.processingRegister = true;
        });
    }
  }
};
</script>
<style lang="scss" scoped>
.wrapContent {
  .LectureSeriesSelection {
    display: flex;
    margin-right: -32px;
    .lecture-left {
      width: 35%;
    }
    .lecture-right {
      width: 65%;
    }
    .lecture-left {
      padding-right: 20px;
      .lectureGroup {
        margin-bottom: 12px;
        .lectureGroup-label {
          margin-bottom: 6px;
          color: #5f6b73;
        }
        .icon {
          cursor: pointer;
        }
        &:last-of-type {
          margin-bottom: 6px;
        }
      }
      .lecture-btnSearch {
        text-align: right;
        .btn {
          padding: 0 12px;
          height: 32px;
        }
      }
    }
    .lecture-right {
      height: 462px;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 10px 0px 0px 10px;
      background: #fff;
      .lecture-list {
        height: 400px;
        padding: 0 20px;
        margin: 3px;
        .lecture-item {
          height: 40px;
          cursor: pointer;
          padding-left: 20px;
          line-height: 30px;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          border-bottom: 1px solid #e3e3e3;
          font-size: 16px;
          font-weight: 400;
          color: #5f6b73;
          &.active {
            background: #eef6ff;
            font-weight: 700;
          }
        }
      }
      .lecture-pagination {
        text-align: center;
        height: 62px;
        padding-top: 12px;
        padding-bottom: 20px;
        box-shadow: 0px -3px 6px rgba(0, 0, 0, 0.1);
        .custom-pagi {
          justify-content: center;
          box-shadow: none;
        }
      }
    }
    .customClassEmpty {
      width: 65%;
      height: 462px;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 10px 0px 0px 10px;
    }
  }
  .LectureSeriesSelection-btn {
    display: flex;
    justify-content: center;
    padding-top: 20px;
    padding-right: 32px;
    button {
      width: 180px;
      height: 40px;
      &:first-of-type {
        margin-right: 24px;
      }
    }
  }
}
</style>
