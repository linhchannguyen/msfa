<template>
  <div class="CustomCase">
    <div v-if="dataList.length > 0" class="CustomCase-content scrollbar">
      <div v-for="(item1, index) of dataList" :key="item1" class="CustomCase-wrap">
        <div class="CustomCase-box">
          <div class="box-1">
            <img
              v-if="item1.document_type === '20'"
              v-svg-inline
              svg-inline
              class="svg"
              :src="require('@/assets/images/icon_document_spanner_small.svg')"
              alt=""
            />
            <img
              v-if="item1.document_type === '10' && item1.file_type === '10'"
              v-svg-inline
              svg-inline
              class="svg"
              :src="require('@/assets/template/images/icon_pdf-manag.svg')"
              alt=""
            />
            <img
              v-if="item1.document_type === '10' && item1.file_type === '20'"
              v-svg-inline
              svg-inline
              class="svg"
              :src="require('@/assets/template/images/icon_film.svg')"
              alt=""
            />
            <span
              title_log="カスタム資材詳細情報 - カスタム資材名"
              class="title log-icon"
              style="cursor: pointer"
              @click="openDocumentCustom(item1.document_id)"
              @touchstart="openDocumentCustom(item1.document_id)"
            >
              {{ item1.document_name }}
            </span>
          </div>
          <div class="box-2">
            <span class="total">{{ item1.sum_review }}件</span>
            <span class="rate"><el-rate v-model="item1.avg_review" allow-half disabled disabled-void-color="#dcdcdc" /></span>
            <span v-if="item1.document_org_user === 1" class="total-2">
              <div class="tblUserStatus-btn">
                <button class="btn btn--icon" data-toggle="dropdown"><span></span><span></span><span></span></button>
                <div class="dropdown-menu dropdown-tools">
                  <span class="btn-show">
                    <span></span>
                    <span></span>
                    <span></span>
                  </span>
                  <ul>
                    <li @click="openModalMaterialViewerD01S07(item1)" @touchstart="openModalMaterialViewerD01S07(item1)">
                      <span class="item">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_copy.svg')" alt="" />
                      </span>
                      <span class="text-label">コピー</span>
                    </li>
                    <li v-if="device !== 'Tablet'" @click="copyDataDocument(item1)" @touchstart="copyDataDocument(item1)">
                      <span class="item">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slideshow.svg')" alt="" />
                      </span>
                      <span class="text-label">ビューア</span>
                    </li>
                  </ul>
                </div>
              </div>
            </span>
          </div>
        </div>
        <div class="CustomCase-content">
          <div class="box-1">
            <div class="CustomCase-lst">
              <ul>
                <li>
                  <span class="CustomCase-tlt">品目</span>
                  <span class="CustomCase-label">{{ item1.product_name }}</span>
                </li>
                <li>
                  <span class="CustomCase-tlt">対象疾患</span>
                  <span class="CustomCase-label">{{ item1.disease }}</span>
                </li>
                <li>
                  <span class="CustomCase-tlt">診療科</span>
                  <span class="CustomCase-label">{{ item1.medical_subjects_group_name }}</span>
                </li>
                <li>
                  <span class="CustomCase-tlt">安全性情報</span>
                  <span class="CustomCase-label">{{ item1.safety_information_value }}</span>
                </li>
                <li>
                  <span class="CustomCase-tlt">適応外情報</span>
                  <span class="CustomCase-label">{{ item1.off_label_information_value }}</span>
                </li>
              </ul>
            </div>
            <!-- <div class="box-title">
              <ul>
                <li class="title-input1">品目</li>
                <li class="title-input1">対象疾患</li>
                <li class="title-input1">診療科</li>
                <li class="title-input1">安全性情報</li>
                <li class="title-input1">適応外情報</li>
              </ul>
            </div>
            <div class="box-content">
              <ul>
                <li class="text-input1">{{ item1.product_name }}</li>
                <li class="text-input1">{{ item1.disease }}</li>
                <li class="text-input1">{{ item1.medical_subjects_group_name }}</li>
                <li class="text-input1">{{ item1.safety_information_value }}</li>
                <li class="text-input1">{{ item1.off_label_information_value }}</li>
              </ul>
            </div> -->
          </div>
          <div class="box-2">
            <div class="title">使用資材</div>
            <ul>
              <li v-if="!item1.readMores && item1.list_composition.length >= 1">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pdf-manag.svg')" alt="" />
                <span class="span-text">{{ item1.list_composition[0].original_document_page_num }}</span>
              </li>
              <li v-if="!item1.readMores && item1.list_composition.length >= 2">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pdf-manag.svg')" alt="" />
                <span class="span-text">{{ item1.list_composition[1].original_document_page_num }}</span>
              </li>
              <li v-if="!item1.readMores && item1.list_composition.length >= 3">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pdf-manag.svg')" alt="" />
                <span class="span-text">{{ item1.list_composition[2].original_document_page_num }}</span>
              </li>
              <li v-if="item1.readMores" style="display: block">
                <div v-for="item of item1.list_composition" :key="item" style="margin-bottom: 14px" class="w-100">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pdf-manag.svg')" alt="" />
                  <span class="span-text">{{ item.original_document_page_num }}</span>
                </div>
              </li>
            </ul>

            <div v-if="item1.list_composition.length > 3" class="see-more">
              <span v-if="!item1.readMores" class="span-1" style="cursor: default" @click="readMore(index)" @touchstart="readMore(index)">
                もっと見る ({{ item1.list_composition.length - 3 }})
                <img style="margin-left: 5px" class="svg" :src="require('@/assets/template/images/readMore1.svg')" alt="" />
              </span>

              <span v-if="item1.readMores" class="span-1" style="cursor: default" @click="readMore(index)" @touchstart="readMore(index)">
                元に戻る
                <img style="margin-left: 5px" class="svg router" :src="require('@/assets/template/images/readMore1.svg')" alt="" />
              </span>
            </div>
          </div>
          <div class="box-3">
            <div class="title">資材概要</div>
            <ul>
              <li>
                <span style="white-space: break-spaces">{{ item1.document_contents }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div v-if="dataList.length > 0" :class="`pagination ${isPopup ? 'pagination-custom-D1S2' : ''}`">
        <div class="pagination-cases">全 {{ totalRecord }} 件</div>
        <PaginationTable :page-size="50" layout="prev, pager, next" :total="totalRecord" :current-page="currentPage" @current-change="handleCurrentChange" />
      </div>
    </div>
    <div v-else class="noData">
      <div class="noData-content">
        <p class="noData-text">該当するデータがありません。</p>
        <div class="noData-thumb">
          <img src="@/assets/template/images/data/amico.svg" alt="icon" />
        </div>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import D01_S02_MaterialDetailsService from '../../../api/D01/D01_S02_MaterialDetailsService';
import { markRaw } from 'vue';
import _ from 'lodash';
import D01_S07_MaterialViewer from '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer';
import { Device } from '@/utils/common-function.js';
export default {
  name: 'D01_S02_CustomCase',
  components: { D01_S07_MaterialViewer },
  props: {
    changetab: {
      type: Number,
      default: 0
    },
    doccumentid: {
      type: Number,
      default: 0
    },
    isPopup: {
      type: Boolean,
      default: false
    },
    checkLoading: [String]
  },

  data() {
    return {
      dataList: {},
      lstDocumentViewer: [],
      isCopy: false,
      data: [],
      totalRecord: 0,
      currentPage: 1,
      total_pages: 0,
      doccument_id: '',
      device: '',
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalD01S02: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },
  watch: {
    changetab: function (value) {
      if (value === 3) {
        this.getDataDetail();
      }
    },
    doccumentid: function () {
      this.getDataDetail();
    }
  },
  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'D01_S02_MaterialDetails') {
          this.doccument_id = toParams.document_id;
          this.getDataDetail();
        }
      }
    );
  },

  mounted() {
    this.getDevice();
  },
  methods: {
    getDevice() {
      this.device = Device();
    },
    getDataDetail() {
      this.$emit('changeLoading', true);
      const data = {
        document_id: this.doccument_id ? this.doccument_id : this.doccumentid
      };
      D01_S02_MaterialDetailsService.getDataDetailT3(data).then((res) => {
        this.dataList = res.data.data.records.map((x) => {
          return {
            ...x,
            avg_review: x.avg_review === '5.0' ? '5' : x.avg_review
          };
        });
        this.totalRecord = res.data.data.pagination.total_items;
        this.dataList.forEach((element) => {
          element.readMores = false;
        });
        this.$emit('changeLoading', false);
      });
    },
    openDocumentCustom(document_id) {
      // this.$router.push({ name: 'D01_S01_MaterialSearch' });
      // setTimeout(() => {
      this.$router.push({ name: 'D01_S02_MaterialDetails', params: { document_id } });
      // }, 1000);
    },
    handleCurrentChange(val) {
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.getDataDetail();
      } else {
        this.currentPage = val;
        this.getDataDetail();
      }
    },
    openModalMaterialViewerD01S07(item1) {
      const copy_id = item1.document_id;
      if (!this.isCopy) {
        this.isCopy = true;
        this.$router.push({
          name: 'D01_S05_CustomMaterialCreation',
          params: { copy_id }
        });
        this.isCopy = false;
      }
    },
    copyDataDocument(item1) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材ビューア',
        isShowModal: true,
        component: markRaw(D01_S07_MaterialViewer),
        width: '70%',
        props: { documentId: item1.document_id }
      };
    },
    readMore(index) {
      this.dataList[index].readMores = !this.dataList[index].readMores;
    },
    onResultModal() {
      this.modalConfig.isShowModal = false;
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.router {
  transform: rotate(180deg);
}
.CustomCase-content {
  height: 100%;
  padding: 10px 24px;
}
.CustomCase-lst {
  > ul {
    > li {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 8px;
      .CustomCase-tlt {
        color: #5f6b73;
        width: 100px;
      }
      .CustomCase-label {
        color: #2d3033;
        width: calc(100% - 100px);
        padding-left: 10px;
      }
    }
  }
}
</style>
