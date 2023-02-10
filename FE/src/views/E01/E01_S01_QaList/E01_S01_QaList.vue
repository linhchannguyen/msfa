<template>
  <div v-loading="loading" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn" :class="posting_prohibited === 1 ? 'posting-prohibited' : ''">
        <button v-if="posting_prohibited !== 1" type="button" class="btn btn-signUp btn-signUp2 btn-sign msfa-custom-btn-create" @click="openE01S03">
          <span style="margin: 0" class="btn-iconLeft"><i style="margin-right: 5px" class="el-icon-plus"></i><span>新規登録</span></span>
        </button>
        <div class="head-filter filter-wrapper">
          <button type="button" class="btn btn-filter" @click="openFilter">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
          </button>
          <div :class="['dropdown-menu dropdown-filter', isShowPopupFilter ? 'show' : '']">
            <button class="item-filter btn-close-filter" type="button" @click="openFilter">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
            </button>
            <h2 class="title-filter">検索条件</h2>
            <div class="form-group">
              <label class="form-label">カテゴリ</label>
              <div class="form-cont w-100">
                <el-select v-model="filterData.qa_category_cd" placeholder="未選択" class="form-control-select">
                  <el-option :value="''">未選択</el-option>
                  <el-option v-for="item in questionCategorys" :key="item.qa_category_cd" :label="item.qa_category_name" :value="item.qa_category_cd">
                  </el-option>
                </el-select>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">ステータス</label>
              <div class="form-cont">
                <ul class="checkbox-list">
                  <li v-for="item of questionStatus" :key="item">
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input
                        class="custom-control-input"
                        :checked="filterData.question_status_type.includes(item.definition_value)"
                        type="checkbox"
                        @click="clickQuestionStatus(item.definition_value)"
                      />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">{{ item.definition_label }}</span>
                    </label>
                  </li>
                  <li>
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input v-model="filterData.new_arrival" class="custom-control-input" type="checkbox" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">新着</span>
                    </label>
                  </li>
                  <li>
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input v-model="filterData.question_hot" class="custom-control-input" type="checkbox" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">HOT</span>
                    </label>
                  </li>
                </ul>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">キーワード</label>
              <div class="form-cont">
                <el-input v-model="filterData.contents" clearable placeholder="内容入力" class="form-control-input" />
              </div>
            </div>
            <div v-if="isR50" class="form-group">
              <label class="form-label">問題報告</label>
              <div class="form-cont">
                <ul class="checkbox-list checkbox-list-ct">
                  <li>
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input v-model="filterData.unsuitable_report" :checked="filterData.unsuitable_report" class="custom-control-input" type="checkbox" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">あり</span>
                    </label>
                  </li>
                </ul>
              </div>
            </div>
            <div class="filter-button">
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="isShowPopupFilter = false">キャンセル</button>
              <button type="button" class="btn btn-primary btn-filter-submit" @click="submit(true, false)">検索</button>
            </div>
          </div>
        </div>
      </div>
      <div ref="messageList" class="lectureContent qa-wrap wrap scrollbar" style="padding: 0">
        <div class="messageList wrap">
          <ul v-if="listData.length > 0" class="qa-list">
            <li v-for="item of listData" :id="`className--${item.question_id}`" :key="item" :ref="`row-${item.question_id}`">
              <span v-if="item.new_arrival === 1" class="ribbon-stt">
                <img src="@/assets/template/images/ribbon-tag-new.png" alt="" />
              </span>
              <span v-if="item.qa_hot >= 5" class="ribbon-stt" :class="item.qa_hot >= 5 && item.new_arrival === 1 ? 'left' : ''">
                <img src="@/assets/template/images/ribbon-tag-hot.png" alt="" />
              </span>
              <div class="question-area">
                <h3 class="question-title">
                  <img style="margin-bottom: 5px; margin-right: 8px" src="@/assets/template/images/icon-qa.svg" alt="" />
                  <span title_log="タイトル" class="log-icon" style="cursor: pointer" @click="openE01S02(item.question_id)">{{ item.title }}</span>
                </h3>
                <div class="qa-cont">
                  <div class="qa-box">
                    <div class="stt">{{ item.quantity_answer }}</div>
                    <span class="stt-text">回答</span>
                  </div>
                  <div style="white-space: pre-line" class="qa-text">
                    {{ item.contents }}
                  </div>
                </div>
              </div>
              <div class="answer-area">
                <div class="answer-head">
                  <div class="time">最終更新日 : {{ formatFullDate(convertDateSf(item.last_update_datetime, true)) }}</div>
                  <span :class="item.definition_label === '回答受付終了' ? 'spanLabel--accepting2' : ''" class="accepting">{{ item.definition_label }}</span>
                  <span class="category">{{ item.qa_category_name }}</span>
                </div>
                <div v-if="item.answer_note.length > 0" class="qa-cont qa-cont-custom">
                  <div class="qa-box">
                    <div class="stt"><img src="@/assets/template/images/icon-crown.svg" alt="" /></div>
                    <span class="stt-text">ベストアンサー</span>
                  </div>
                  <!-- <div class="qa-text">
                  {{ item.answer_note }}
                </div> -->

                  <span v-for="item2 of item.answer_note" :key="item2" style="white-space: pre-line" class="custom-span qa-text">{{ item2.answer_note }} </span>
                </div>
                <div v-if="item.answer_note.length === 0" class="qa-cont">
                  <p>ベストアンサーがありません。この質問に解答しませんか？</p>
                  <button
                    type="button"
                    style="padding: 4px 12px 0px 14px"
                    class="btn btn-radius btn-outline-primary btn-outline-primary--cancel btn-answer"
                    @click="openE01S02(item.question_id)"
                  >
                    <span style="margin-right: 1px" class="btn-iconLeft">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-send.svg')" alt="" /> 回答する
                    </span>
                  </button>
                  <div class="thumb-none"><img src="@/assets/template/images/qa-none.png" alt="" /></div>
                </div>
              </div>
            </li>
          </ul>
          <div v-if="listData.length === 0 && !isLoadDefault" class="noData">
            <div class="noData-content">
              <p class="noData-text">該当するデータがありません。</p>
              <div class="noData-thumb">
                <img src="@/assets/template/images/data/amico.svg" alt="icon" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="listData.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ pageSizelistUsserMangement }} 件</div>
      <PaginationTable
        :page-size="100"
        layout="prev, pager, next"
        :total="pageSizelistUsserMangement"
        :current-page="currentPage"
        @current-change="handleCurrentChange"
      />
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`${modalConfig.customClass} handle-close`"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component :is="modalConfig.component" ref="modalChild" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<!-- eslint-disable eqeqeq -->
<script>
import E01_S01_QASearchServices from '../../../api/E01/E01_S01_QASearchServices';
import { markRaw } from 'vue';
import E01S03QaInput from '../E01_S03_QaInput/E01_S03_QaInput.vue';
import PaginationTable from '@/components/PaginationTable';
import { Auth } from '@/api';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'E01_S01_QASearch',
  components: { E01S03QaInput, PaginationTable },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      isLoadDefault: true,
      // isShowPopupFilter: false,
      questionCategorys: [],
      questionStatus: [],
      listData: [],
      loading: true,
      posting_prohibited: 0,
      pageSizelistUsserMangement: 0,
      currentPage: 1,
      total_pages: 0,
      isR50: false,
      filterData: {
        qa_category_cd: '',
        question_status_type: ['10'],
        question_hot: true,
        new_arrival: true,
        contents: '',
        unsuitable_report: false
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
      userLogin: null,

      onScrollTop: 0
    };
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'QA検索',
      icon: 'E01-Qa.svg',
      isShowBack: false
    });

    this.userLogin = this.getCurrentUser();

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.E01_S01_QASearch || 0;
    this.currentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.E01_S01_QASearch || 1;

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
    setScrollTop() {
      let scrollTop = this.$refs.messageList ? this.$refs.messageList.scrollTop : 0;
      this.setScrollScreen('E01_S01_QASearch', scrollTop);
    },
    getScreenData() {
      E01_S01_QASearchServices.getDataScreen()
        .then(async (res) => {
          const data = res.data.data;
          this.questionCategorys = data.question_category;
          this.questionStatus = data.question_status;
          this.posting_prohibited = data.posting_prohibited;
          // this.getList();

          let user_cd = this.userLogin.user_cd;

          const permission = await Auth.getPolicyRoleService({ user_cd });

          if (permission?.data?.data?.includes('R50')) {
            this.isR50 = true;
          }

          if (this.$route && this.$route.params) {
            if (this.$route.params?.unsuitable_report) {
              this.filterData = {
                qa_category_cd: '',
                question_status_type: [],
                question_hot: false,
                new_arrival: false,
                contents: '',
                unsuitable_report: this.$route.params.unsuitable_report == 1 ? true : false
              };
            }
          }

          this.submit(false, true);
        })
        .catch((err) => {
          this.$notify({ message: err?.response?.data?.message, customClass: 'error' });
        });
    },
    clickQuestionStatus(definitionValue) {
      if (!this.filterData.question_status_type.includes(definitionValue)) {
        this.filterData.question_status_type.push(definitionValue);
      } else {
        this.filterData.question_status_type.splice(this.filterData.question_status_type.indexOf(definitionValue), 1);
      }
    },
    openFilter() {
      this.isShowPopupFilter = !this.isShowPopupFilter;
    },
    onCloseModal(e) {
      if (e) {
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
        if (e === 1) {
          this.setCurrentPageScreen('E01_S01_QASearch', 1);
          this.onScrollTop = 0;
          this.currentPage = 1;
          this.getList(false, false, true);
        }
      } else {
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      }
    },
    submit(isFilter, isLoadDefault) {
      if (isFilter) {
        this.pageSizelistUsserMangement = 0;
        this.currentPage = 1;
        this.total_pages = 0;
      }
      this.getList(isFilter, isLoadDefault);

      this.isShowPopupFilter = false;
    },
    openE01S03() {
      if (this.posting_prohibited !== 1)
        this.modalConfig = {
          ...this.modalConfig,
          title: '質問登録',
          isShowModal: true,
          component: markRaw(E01S03QaInput),
          width: '40rem',
          customClass: 'custom-dialog modal-fixed',
          props: { question_id: '' }
        };
    },
    openE01S02(id) {
      this.setScrollTop();
      let question_id = id;
      this.$router.push({ name: 'E01_S02_QaDetails', params: { question_id } });
    },
    handleCurrentChange(val) {
      this.onScrollTop = 0;
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.getList(true, false);
      } else {
        this.currentPage = val;
        this.getList(true, false);
      }
    },
    getList(isFilter, isLoadDefault, isCreate) {
      this.isLoadDefault = isLoadDefault;
      this.loading = true;
      const data = {
        question_category_cd: this.filterData.qa_category_cd,
        question_status_type: this.filterData.question_status_type,
        question_hot: this.filterData.question_hot ? 1 : 0,
        new_arrival: this.filterData.new_arrival ? 1 : 0,
        contents: this.filterData.contents,
        unsuitable_report: this.isR50 ? (this.filterData.unsuitable_report ? 1 : 0) : 0,
        limit: 100,
        offset: this.currentPage === 0 ? this.currentPage : this.currentPage - 1
      };
      E01_S01_QASearchServices.getData(data)
        .then((res) => {
          const data = res.data.data;
          this.listData = data.records;
          this.pageSizelistUsserMangement = data.pagination.total_items;
          this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
        })
        .catch((err) => {
          this.listData = [];
          this.$notify({ message: err?.response?.data?.message, customClass: 'error' });
        })
        .finally(async () => {
          if (isCreate) {
            const id = this.listData[0].question_id;
            this.getClassChangeBRG(id, true, true);
          }
          await new Promise((resolve) => setTimeout(resolve, 1000));
          this.loading = false;
          this.isLoadDefault = false;

          this.setCurrentPageScreen('E01_S01_QASearch', this.currentPage);

          if (this.$refs.messageList) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.messageList.scrollTop = this.onScrollTop;
            } else {
              this.$refs.messageList.scrollTop = 0;
            }
          }

          this.js_pscroll(0.5);

          localStorage.setItem('isLoadingComponent', false);

          const childRouter = JSON.parse(localStorage.getItem('router'));
          let routes = this.decodeData(localStorage.getItem('$_D')) || [];

          if (routes && routes.length > 0) {
            const indexRoute = routes.findIndex((x) => x.name === 'E01_S01_QaSearch');

            if (indexRoute > -1) {
              let routeQASearch = {
                ...routes[indexRoute],
                params: {}
              };

              routes[indexRoute] = routeQASearch;
              localStorage.setItem('$_D', this.enCodeData(routes));
            }
          } else {
            let routeQASearch = {
              name: 'E01_S01_QASearch',
              params: {},
              path: childRouter.find((x) => x.name === 'E01_S01_QASearch')?.path
            };

            routes[0] = routeQASearch;
            localStorage.setItem('$_D', this.enCodeData(routes));
          }
        });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.posting-prohibited {
  justify-content: end !important;
}
.btn-answer {
  position: absolute;
  margin: auto;
  left: 29%;
  top: 50%;
}
.wrapContent {
  // overflow: hidden;
}
.pagination {
  background: #f2f2f2;
  z-index: 99;
}
@media only screen and (max-width: $viewport_tablet) {
  .qa-list {
    li {
      position: relative !important;
      display: block !important;
      .question-area {
        width: unset !important;
      }
      .qa-cont {
        margin-bottom: 15px !important;
        &::after {
          display: none !important;
        }
      }
      .answer-area {
        width: unset !important;
        padding-left: 0 !important;
        .answer-head {
          position: absolute;
          top: 10px;
          right: 10px;
        }
        .btn-answer {
          left: 20% !important;
        }
      }
    }
  }
  .question-title {
    margin-top: 15px;
    width: 100%;
    white-space: nowrap;
    overflow: hidden !important;
    text-overflow: ellipsis;
  }
}
.left {
  left: 53px !important;
}
.wrap {
  padding: 0 25px;
}
.wrap {
  display: flex;
  justify-content: space-between;
}

.btn-filter,
.btn-signUp {
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(13, 94, 153, 0.1);
  height: 42px;
  border-radius: 0 0 8px 8px;
  padding: 0.375rem 0.975rem 0.375rem 0.895rem;
  color: #448add;
}
.btn-signUp2 {
  background-color: #fff;
  height: 42px;
  font-weight: 700;
  box-shadow: 0px 2px 4px rgba(13, 94, 153, 0.1), 0.5px 1px 5px 1px rgba(203, 225, 241, 0.3);
  border-radius: 0px 0px 8px 8px;
  padding: 0.375rem 0.975rem 0.375rem 0.895rem;
  color: #448add;
  &:hover {
    background: #448add;
    color: #fff;
    .svg {
      fill: #fff;
    }
  }
}

.btn-filter {
  padding-left: 8px;
  padding-right: 12px;
  &:hover {
    background: #448add;
    color: #fff;
    .svg {
      fill: #fff;
    }
  }
}

.head-filter {
  position: relative;
}
.showFilter {
  display: block;
  position: absolute;
  top: 0;
}

.w-100 {
  width: 100% !important;
}

.h-100 {
  height: 100% !important;
}

.dropdown-filter {
  width: 450px;
  top: 0;
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
    margin-bottom: 8px;
  }
  .form-group {
    display: flex;
    flex-flow: wrap;
    .form-label {
      color: #5f6b73;
      margin-bottom: 6px;
      line-height: 20px;
      width: 100px;
      padding-top: 5px;
    }
    .form-cont {
      width: calc(100% - 100px);
    }
  }
  .checkbox-list-ct {
    padding-left: 1px;
  }
  .checkbox-list {
    padding-right: 8px;
    display: flex;
    justify-content: space-between;
    flex-flow: wrap;
    .custom-control,
    .custom-control-description {
      width: 100%;
    }
    li {
      margin-bottom: 5px;
      width: calc(50% - 15px);
    }
  }
  .filter-button {
    text-align: center;
    margin-top: 24px;

    .btn {
      width: 142px;
      margin: 0 12px;
    }
  }
}
.qa-cont-custom {
  align-items: center;
}
.qa-list {
  padding: 12px 0 20px 0px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
  li {
    display: flex;
    justify-content: space-between;
    padding: 30px 40px 22px 40px;
    background-color: #fff;
    border-radius: 4px;
    box-shadow: 0px 0px 6px rgba(183, 195, 203, 0.9);
    position: relative;
    .ribbon-stt {
      position: absolute;
      top: -4px;
      left: 6px;
      z-index: 3;
    }
    .qa-cont {
      display: flex;
      justify-content: space-between;
      .qa-box {
        width: 100px;
        height: 60px;
        display: flex;
        flex-flow: column;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #28a470;
        border-radius: 6px;
        padding: 7px 2px 5px;
        .stt {
          font-size: 32px;
          color: #28a470;
          font-weight: 700;
          line-height: 100%;
          display: block;
          margin-bottom: 2px;

          img {
            display: block;
          }
        }
        .stt-text {
          font-size: 12px;
          color: #28a470;
          display: block;
          line-height: 100%;
        }
      }
      .qa-text {
        width: calc(100% - 100px);
        padding-left: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 150%;
        .custom-span {
          display: block;
          width: calc(100% - 1px);
          white-space: nowrap;
          overflow: hidden !important;
          text-overflow: ellipsis;
        }
      }
    }
    .question-area {
      display: flex;
      flex-flow: column;
      justify-content: center;
      width: 50%;
      padding-right: 24px;

      .question-title {
        font-size: 22px;
        font-weight: 500;
        color: #448add;
        margin-bottom: 24px;
      }
    }
    .answer-area {
      display: flex;
      flex-flow: column;
      justify-content: space-between;
      padding-left: 24px;
      width: 50%;
      .qa-cont {
        position: relative;
        &::after {
          position: absolute;
          content: '';
          width: 1px;
          height: 64px;
          left: -24px;
          top: 50%;
          margin-top: -32px;
          background-color: #b7c3cb;
        }
      }
      .answer-head {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 10px;
        .time {
          font-size: 12px;
          color: #727f88;
        }
        .accepting {
          background-color: #daf8dc;
          display: flex;
          align-items: center;
          line-height: 100%;
          justify-content: center;
          padding: 7px 11px;
          font-size: 14px;
          color: #28a470;
          margin-left: 12px;
          font-weight: 500;
        }
        .spanLabel--accepting2 {
          color: #5f6b73;
          background: #e3e3e3;
          border-radius: 2px;
        }
        .category {
          display: flex;
          align-items: center;
          justify-content: center;
          line-height: 100%;
          background: #e2e4ff;
          border-radius: 20px;
          font-size: 14px;
          color: #555fb0;
          margin-left: 12px;
          font-weight: 500;
          padding: 7px 11px;
        }
      }
      .qa-cont {
        .qa-box {
          border-color: #ff8c67;
          .stt-text {
            color: #ff8c67;
          }
        }
      }
    }
    &.answer-none {
      .qa-cont {
        display: flex;
        justify-content: space-between;
        align-items: center;
        .info-none {
          width: calc(100% - 150px);
          p {
            color: #99a5ae;
            text-align: center;
            padding: 5px;
          }
          .btn-answer {
            height: 32px;
            padding: 0;
            width: 105px;
            border-radius: 20px;
            background-color: #fff;
            color: #448add;
          }
        }
        .thumb-none {
          width: 100px;
        }
      }
    }
  }
}
.pagination {
  box-shadow: 0 -3px 6px rgba(0, 0, 0, 0.1);
}
.info-none {
  width: calc(100% - 150px);
  p {
    color: #99a5ae;
    text-align: center;
    padding: 5px;
  }
  .btn-answer {
    height: 32px;
    padding: 0;
    width: 105px;
    border-radius: 20px;
    background-color: #fff;
    color: #448add;
  }
}
.wrapperContent {
  display: grid;
  grid-template-rows: calc(100vh - 168px) 54px;
  padding-top: 10px;
  .qa-wrap {
    margin: 0;
  }
}
.wrapContent {
  // overflow: overlay;
  min-width: 700px;
  .qa-wrap {
    margin-bottom: 20px;
  }
}
.messageList {
  width: 100%;
  .noData {
    width: 100%;
  }
}
</style>
