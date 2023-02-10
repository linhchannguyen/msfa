<template>
  <div class="Evaluation">
    <div class="Evaluation-wrap scrollbar">
      <div class="Evaluation-box1">
        <ul v-if="doccumenttype === '10'" class="facilityForm-btnSelect mfsa-custom-tab-select2">
          <li
            v-for="item of dataBtn"
            :key="item"
            class="li-1"
            :class="comprehensiveStatus === item.id ? 'active' : ''"
            @click="activeBtn(item.id)"
            @touchstart="activeBtn(item.id)"
          >
            {{ item.namebtn }}
          </li>
        </ul>
        <div v-if="distributionGraph.length > 0" class="feedback-box">
          <div class="feedback-box-wrap">
            <span class="number-big">{{ scoreValues.avg_review ? scoreValues.avg_review : '0' }}</span>
            <span class="number-dot">/</span><span class="number-medium">5</span>
          </div>
          <div class="start">
            <el-rate v-model="avg_review" allow-half disabled disabled-void-color="#dcdcdc" />
            <span title_log="評価件数" class="total cursor-pointer log-icon" @click="changeReview('')" @touchstart="changeReview('')">
              全 {{ scoreValues.sum_review ? scoreValues.sum_review : '0' }} 件</span
            >
          </div>
          <div class="list-feddback">
            <ul>
              <li v-for="item of distributionGraph" :key="item">
                <span class="number">{{ item.document_review }}</span>
                <span class="number-star"><img class="svg" :src="require('@/assets/template/images/Star.svg')" alt="" /></span>
                <span class="number-progress">
                  <el-progress :stroke-width="10" :percentage="(+item.count_review / scoreValues.sum_review) * 100" status="exception"> </el-progress
                ></span>
                <span
                  title_log="評価数リンク"
                  style="cursor: pointer"
                  class="log-icon total cursor-pointer"
                  @click="changeReview(item.document_review)"
                  @touchstart="changeReview(item.document_review)"
                >
                  {{ item.count_review }} 件</span
                >
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div v-if="records.length > 0" class="Evaluation-box2 scrollbar">
        <div v-for="item of records" :key="item" class="Evaluation-box2-wrap">
          <div class="content-box">
            <div class="title">
              <span class="img">
                <img
                  v-if="item?.document_type === '20'"
                  v-svg-inline
                  svg-inline
                  class="svg"
                  :src="require('@/assets/images/icon_document_spanner_small.svg')"
                  alt="" />
                <img
                  v-if="item?.document_type === '10' && item?.file_type === '10'"
                  v-svg-inline
                  svg-inline
                  class="svg"
                  :src="require('@/assets/template/images/icon_pdf-manag.svg')"
                  alt="" />
                <img
                  v-if="item?.document_type === '10' && item?.file_type === '20'"
                  v-svg-inline
                  svg-inline
                  class="svg"
                  :src="require('@/assets/template/images/icon_film.svg')"
                  alt="" /></span
              ><span class="title-2">{{ (item.document_name ? item.document_name : '') + ' ' + (item.document_version ? item.document_version : '') }}</span>
            </div>
            <div class="title-header">
              <div class="box-header-1">
                <div class="lstHistory-thumb">
                  <img style="width: 100%" class="lstHistory-icon" :src="item.file_url" alt="" />
                </div>
                <div class="list-2">
                  <div class="list-name1">
                    {{ item.review_user_name }}
                    <div>
                      <div class="list-name2">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" /><span>{{
                          item.review_org_label ? item.review_org_label : '(所属なし)'
                        }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="list-3">
                <div class="list-name3">
                  <el-rate v-model="item.document_review" allow-half disabled disabled-void-color="#dcdcdc" />
                  <div>
                    <div class="list-name4">{{ getTimeInterval(item.review_datetime) }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-bottom">
              <span style="white-space: break-spaces" class="text">{{ item.review_comment }}</span>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="Evaluation-box2">
        <div class="Evaluation-box2-wrap-2">
          <div class="noData">
            <div class="noData-content">
              <p class="noData-text">該当するデータがありません。</p>
              <div class="noData-thumb">
                <img src="@/assets/template/images/data/rafiki.svg" alt="icon" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="records.length > 0" :class="`pagination ${isPopup ? 'pagination-custom1-D1S2' : ''}`">
      <div class="pagination-cases">全 {{ totalRecord }} 件</div>
      <PaginationTable :page-size="100" layout="prev, pager, next" :total="totalRecord" :current-page="currentPage" @current-change="handleCurrentChange" />
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
      @close="closeModal"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onFinishScreen"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import D01_S02_MaterialDetailsService from '../../../api/D01/D01_S02_MaterialDetailsService';
import D01_S06_MaterialEvaluationInput from '../D01_S06_MaterialEvaluationInput/D01_S06_MaterialEvaluationInput.vue';
import { mapGetters } from 'vuex';

export default {
  name: 'D01_S02_Evaluation',
  components: { D01_S06_MaterialEvaluationInput },
  props: {
    changetab: {
      type: Number,
      default: 0
    },
    doccumentid: {
      type: Number,
      default: 0
    },
    doccumenttype: {
      type: String,
      default: ''
    },
    isPopup: {
      type: Boolean,
      default: false
    },
    checkLoading: [String]
  },

  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      dataList: {},
      scoreValues: {},
      comprehensiveStatus: 0,
      totalRecord: 0,
      currentPage: 1,
      total_pages: 0,
      distributionGraph: [],
      dataBtn: [
        {
          id: 0,
          namebtn: '総合評価'
        },
        {
          id: 1,
          namebtn: 'バージョン評価'
        }
      ],
      customDoccumentId: '',
      records: [],
      avg_review: null
    };
  },
  watch: {
    changetab: function (value) {
      if (value === 2) {
        this.getDataDetail();
      }
    },
    doccumentid: function () {
      this.getDataDetail();
    }
  },
  // eslint-disable-next-line vue/order-in-components
  computed: {
    ...mapGetters({
      historyParams: 'router/isHistoryParams'
    })
  },

  mounted() {},
  methods: {
    getDataDetail(doccumentReview) {
      this.$emit('changeLoading', true);
      if (this.comprehensiveStatus === 0) {
        let { search } = window.location;
        if (search.match('document_id=')) {
          let doc_id = search.slice(1).split('&')[0].split('=')[1];
          this.customDoccumentId = doc_id || '';
        } else {
          if (this._route('D01_S02_MaterialDetails')) {
            this.customDoccumentId = this._route('D01_S02_MaterialDetails') ? this._route('D01_S02_MaterialDetails').params.document_id : '';
          } else {
            let { document_id } = this.historyParams;
            this.customDoccumentId = document_id;
          }
        }
      } else {
        this.customDoccumentId = this.doccumentid;
      }

      let document = [
        {
          count_review: 0,
          document_review: 1,
          sum_review: '0'
        },
        {
          count_review: 0,
          document_review: 2,
          sum_review: '0'
        },
        {
          count_review: 0,
          document_review: 3,
          sum_review: '0'
        },
        {
          count_review: 0,
          document_review: 4,
          sum_review: '0'
        },
        {
          count_review: 0,
          document_review: 5,
          sum_review: '0'
        }
      ];

      const data = {
        document_id: this.customDoccumentId,
        comprehensive_status: this.comprehensiveStatus,
        document_review: doccumentReview ? doccumentReview : ''
      };
      D01_S02_MaterialDetailsService.getDataDetailT2(data)
        .then(async (res) => {
          this.dataList = res.data.data;
          this.scoreValues = res.data.data.score_values;
          this.avg_review = res.data.data.score_values.avg_review === '5.0' ? '5' : res.data.data.score_values.avg_review;
          this.records = res.data.data.list_review_comment.records;
          this.totalRecord = res.data.data.list_review_comment.pagination.total_items;
          document.forEach((element) => {
            res.data.data.distribution_graph.forEach((element2) => {
              if (element.document_review === element2.document_review) {
                element.document_review = element2.document_review;
                element.count_review = element2.count_review;
                element.sum_review = element2.sum_review;
              }
            });
          });
          this.distributionGraph = document;

          let listImg = res.data.data.list_review_comment.records.map((item) =>
            fetch(item.file_url).then((resp) => {
              // eslint-disable-next-line eqeqeq
              if (resp.ok && resp.status == 200) {
                return {
                  ...item,
                  file_url: item.file_url ? item.file_url : this.avataDefault(),
                  res: { ...resp }
                };
              } else {
                return {
                  ...item,
                  file_url: this.avataDefault(),
                  res: { ...resp }
                };
              }
            })
          );
          await Promise.all(listImg).then(async (res) => {
            let data = await res;
            this.records = data;
          });

          this.$emit('changeLoading', false);
        })
        .finally(() => {
          this.$emit('changeLoading', false);
        });
    },
    activeBtn(id) {
      this.comprehensiveStatus = id;
      this.getDataDetail();
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
    onFinishScreen(e) {
      if (e.screen === 'D01_S06') {
        if (e.saveReview) {
          this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
        }
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    },
    changeReview(doccumentReview) {
      this.getDataDetail(doccumentReview);
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet1: 1024px;
.active {
  color: #448add;
  background: #eef6ff;
  border: 2px solid #448add;
  font-weight: bold;
  font-size: 14px;
}
.Evaluation,
.Evaluation-wrap {
  height: 100%;
}
@media (max-width: $viewport_tablet1) {
  .Evaluation {
    padding-bottom: 0 !important;
  }
}
.Evaluation {
  padding-bottom: 54px;
}
.cursor-pointer {
  cursor: pointer;
}
.Evaluation-box1 {
  .li-1 {
    padding: 8px 2px;
    margin-left: -1px;
    text-align: center;
    color: #5f6b73;
    border: 1px solid #727f88;
    position: relative;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    &.active {
      border: 2px solid #448add;
      color: #448add;
      font-weight: 700;
      background: #eef6ff;
      z-index: 1;
      pointer-events: none;
    }
    &:first-of-type {
      border-radius: 4px 0 0 4px;
    }
    &:last-child {
      border-radius: 0 4px 4px 0;
    }
  }
}
</style>
