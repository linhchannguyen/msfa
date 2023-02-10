<template>
  <div v-loading="isLoading" v-load-f5="true" class="wrapContent">
    <div class="postingUser">
      <div class="postingUser-left">
        <div class="slideTabs">
          <div class="slideTabs-btn btn-prev">
            <button style="height: 100%" class="btn" :disabled="current <= 0" @click="scrollTop">
              <ImageSVG :src-image="'icon_arrow-line-slider.svg'" :alt-image="'icon_arrow-line-slider'" />
            </button>
          </div>
          <div class="slideTabs-nav box">
            <div v-if="PostingProhibitList" class="nav sideLeft" :style="`--dir: 0; --max: ${listNavLength}`">
              <div
                v-for="(item, index) in PostingProhibitList"
                :key="index"
                class="sideItem"
                :style="`--offset:${index - current}`"
                @click="selectRecord(item, index)"
              >
                <span :class="`link ${active === (index ? index : 0) ? 'active' : ''}`">
                  <div class="slideTabs-thumb">
                    <img :src="item.avatar_image_data" alt="user-avatar" />
                  </div>
                  <div class="slideTabs-content">
                    <h2 class="slideTabs-title">{{ item.user_name }}</h2>
                    <p class="slideTabs-add">
                      <span class="slideTabs-item">
                        <ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" />
                      </span>
                      <span class="slideTabs-text">{{ item.org_label || '(所属なし)' }}</span>
                    </p>
                  </div>
                </span>
              </div>
            </div>
            <div v-if="PostingProhibitList.length === 0 && !isLoading" class="noData w100">
              <div class="noData-wrap">
                <p v-if="!isLoading" class="noData-note">該当するデータがありません。</p>
                <img src="@/assets/template/images/data/amico.svg" alt="" />
              </div>
            </div>
          </div>

          <div class="slideTabs-btn btn-next">
            <button style="height: 100%" class="btn" :disabled="current + maxItem >= listNavLength" @click="scrollBottom">
              <ImageSVG :src-image="'icon_arrow-line-slider.svg'" :alt-image="'icon_arrow-line-slider'" />
            </button>
          </div>
        </div>
      </div>
      <div class="postingUser-right">
        <div class="tab-content">
          <div class="tab-pane active">
            <div class="postHistory">
              <div v-if="UnsuitableReportList.length > 0" class="postHistory-head">
                <div class="postHistory-number">
                  <span class="item"><ImageSVG :src-image="'icon_history-line.svg'" :alt-image="'icon_history-line'" /></span>
                  投稿履歴確認 ({{ UnsuitableReportList.length }})
                </div>
                <div class="postHistory-date">
                  <span class="spanDate">投稿禁止日時:</span>
                  <span class="spanDate">{{ formatFullDate(objSelected.prohibited_datetime) }}</span>
                  <span class="spanDate">{{ formatTimeHourMinute(objSelected.prohibited_datetime) }}</span>
                  <button
                    v-show="UnsuitableReportList.length > 0"
                    type="button"
                    class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                    @click="
                      openModal({
                        screenID: 'PopupConfirm',
                        width: '35rem',
                        props: {
                          params: { user_cd: objSelected.user_cd },
                          message: objSelected.user_name + 'さんの投稿禁止設定を解除しますか？',
                          mode: 1,
                          textCancel: 'いいえ'
                        }
                      })
                    "
                  >
                    <span class="btn-iconLeft">
                      <ImageSVG :src-image="'icon_listremove.svg'" :alt-image="'icon_listremove'" />
                    </span>
                    解除
                  </button>
                </div>
              </div>
              <div v-if="UnsuitableReportList.length === 0 && !isLoading" class="noData scrollbar">
                <div class="noData-wrap">
                  <p v-if="!isLoading" class="noData-note">該当するデータがありません。</p>
                  <img src="@/assets/template/images/data/amico.svg" alt="" />
                </div>
              </div>
              <div v-else class="postList scrollbar">
                <ul>
                  <li v-for="(item, index) in UnsuitableReportList" :key="index">
                    <div class="postList-head">
                      <span class="date">{{ formatFullDate(item.created_at) }}</span>
                      <span class="view link log-icon" @click="callScreen('E01_S02', { question_id: item.question_id })">
                        詳細を見る <ImageSVG :src-image="'icon_arrow-line02.svg'" :alt-image="'icon_arrow-line02'" />
                      </span>
                    </div>
                    <div class="postList-content">
                      <p class="postList-text">{{ item._contents }}</p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closeModal" @handleYes="blockBtn" />
      </template>
    </el-dialog>
  </div>
</template>

<script>
import E01_S04_Service from '@/api/E01/E01_S04_PostingProhibitedUserManagementServices';
import avataReport from '@/assets/template/images/data/avata-reportList.png';
export default {
  name: 'E01_S04_PostingProhibitedUserManagement',
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      },
      avataReport,
      PostingProhibitList: [],
      UnsuitableReportList: [],
      objSelected: {
        avatar_image_data: '',
        avatar_image_type: '',
        org_label: '',
        prohibited_datetime: '',
        user_cd: '',
        user_name: ''
      },
      isLoading: true,
      btnBottomFlg: false,
      btnTopFlg: true,
      length: 0,
      active: 0,
      listNavLength: 0,
      current: 0,
      maxItem: 1
    };
  },
  // computed: {
  //   listNavLength: this.PostingProhibitList.length
  // },
  mounted() {
    this.emitter.emit('change-header', {
      title: '投稿禁止ユーザ管理',
      icon: 'icon_QA_color.svg',
      isShowBack: false
    });
    this.allPostingProhibited();
    this.active = 0;
  },

  methods: {
    // func define
    selectRecord(item, i) {
      if (this.objSelected.user_cd === item.user_cd) return;
      this.objSelected = item;
      this.active = i ? i : 0;
      this.getUnsuitableReport(item.user_cd);
    },

    callScreen(screenID, props) {
      let objScreen = {
        E01_S02: 'E01_S02_QaDetails'
      };
      this.$router.push({ name: objScreen[screenID], params: props });
    },

    scrollTop() {
      this.current = this.current - 1;
    },

    scrollBottom() {
      this.current = this.current + 1;
      // current + maxItem >= listNavLength
      console.log(this.current, this.maxItem, this.listNavLength);
    },
    calcItem() {
      let sideHeight = document.querySelector('.slideTabs-nav');
      let sideItem = sideHeight.querySelectorAll('.sideItem');
      let maxH = sideHeight.offsetHeight;
      let singleH = 0;

      sideItem.forEach((item) => {
        item.offsetHeight > singleH ? (singleH = item.offsetHeight) : '';
      });
      this.maxItem = Math.floor(maxH / singleH);
    },
    // modal
    closeModal() {
      this.modalConfig.isShowModal = false;
    },
    openModal({ screenID, title = '', width = 0, props = {} }) {
      if (!screenID) return;
      let objScreen = {
        PopupConfirm: this.$PopupConfirm
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: title,
        isShowModal: true,
        component: this.markRaw(objScreen[screenID]),
        props: props,
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        width: width
      };
    },
    readMore(index) {
      let arr = this.UnsuitableReportList;
      arr[index] = {
        ...arr[index],
        readMoreActivated: !arr[index].readMoreActivated
      };
      this.UnsuitableReportList = arr;
    },
    blockBtn(params) {
      this.closeModal();
      this.cancelPostingProhibited(params.user_cd);
    },
    // call api
    allPostingProhibited() {
      E01_S04_Service.allPostingProhibitedService()
        .then(async (res) => {
          const dataRes = res.data.data;
          this.PostingProhibitList = [];

          let listImg = dataRes.map((item) =>
            fetch(item.avatar_image_data).then((resp) => {
              // eslint-disable-next-line eqeqeq
              if (resp.ok && resp.status == 200) {
                return {
                  ...item,
                  avatar_image_data: item.avatar_image_data || this.avataDefault(),
                  res: { ...resp }
                };
              } else {
                return {
                  ...item,
                  avatar_image_data: this.avataDefault(),
                  res: { ...resp }
                };
              }
            })
          );
          await Promise.all(listImg).then(async (res) => {
            let data = await res;
            this.PostingProhibitList = data;
          });

          if (this.PostingProhibitList.length === 0) {
            this.UnsuitableReportList = [];
          }

          if (this.PostingProhibitList.length > 10) {
            this.length = this.PostingProhibitList.length - 9;
          } else {
            this.btnBottomFlg = true;
          }
          if (dataRes.length > 0) this.selectRecord(dataRes[0]);
          this.listNavLength = this.PostingProhibitList.length - 1;
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.calcItem();
          this.isLoading = false;
        });
    },
    getUnsuitableReport(user_cd) {
      this.isLoading = true;
      E01_S04_Service.getUnsuitableReportService({ user_cd })
        .then((res) => {
          const dataRes = res.data.data;
          this.UnsuitableReportList = dataRes.map((el) => ({
            ...el,
            readMoreActivated: false,
            _contents: el.key_type !== '20' ? el.contents : el.answer_note
          }));
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;
          this.calcItem();
        });
    },
    cancelPostingProhibited(user_cd) {
      E01_S04_Service.cancelPostingProhibitedService({ user_cd })
        .then((res) => {
          this.allPostingProhibited();
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.postingUser {
  height: 100%;
  padding-top: 28px;
  display: flex;
  flex-wrap: wrap;
  .postingUser-left {
    width: 400px;
    height: 100%;
    background: #fcfcfc;
    @media (max-width: $viewport_tablet) {
      width: 350px;
    }
  }
  .postingUser-right {
    width: calc(100% - 400px);
    height: 100%;
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    @media (max-width: $viewport_tablet) {
      width: calc(100% - 350px);
    }
    .tab-content,
    .tab-pane {
      height: 100%;
    }
  }
  .slideTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    .slideTabs-btn {
      position: relative;
      z-index: 2;
      &::after {
        position: absolute;
        top: 0;
        right: -10px;
        content: '';
        width: 10px;
        height: 100%;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        z-index: 4;
      }
      &.btn-prev {
        .btn {
          box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
          .svg {
            transform: rotate(180deg);
          }
        }
      }
      &.btn-next {
        .btn {
          box-shadow: 0px -3px 6px rgba(0, 0, 0, 0.1);
        }
      }
      .btn {
        background: #fff;
        width: 100%;
        height: 24px;
        padding: 0;
        border-radius: 0;
        &[disabled] {
          cursor: no-drop;
        }
      }
    }
    .slideTabs-nav {
      height: 100%;
      overflow: hidden;

      div.sideLeft {
        display: grid;
        div.sideItem {
          grid-row: 1/-1;
          grid-column: 1/-1;
          transform: translateY(calc(100% * var(--offset)));
          border-bottom: 1px solid #ccd4da;
          transition: transform 0.2s;

          a,
          span.link {
            display: flex;
            flex-wrap: wrap;
            padding: 20.9px 50px 18px 24px;
            @media only screen and (max-width: 1180px) {
              padding: 16.1px 50px 18px 24px;
            }
            @media only screen and (max-width: 1024px) {
              padding: 20.9px 50px 18px 24px;
            }
            position: relative;
            color: #5f6b73;
            &::after {
              position: absolute;
              top: calc(50% - 6px);
              right: 24px;
              content: '';
              border-top: 6px solid transparent;
              border-bottom: 6px solid transparent;
              border-left: 7px solid #5f6b73;
            }
            &:hover {
              text-decoration: none;
            }
            &.active {
              background: #fff;
              color: #2d3033;
              box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
              z-index: 1;
              &::after {
                display: none;
              }
              &::before {
                position: absolute;
                top: 0;
                left: 0;
                content: '';
                height: 100%;
                width: 4px;
                background: #448add;
              }
            }
          }
          .slideTabs-thumb {
            width: 50px;
            height: 50px;
            overflow: hidden;
            border-radius: 50%;
            border: 1px solid #cdced9;

            img {
              height: 100%;
              width: 100%;
            }
          }
          .slideTabs-content {
            width: calc(100% - 50px);
            padding-left: 12px;
            .slideTabs-title {
              font-size: 1rem;
              font-weight: 700;
            }
            .slideTabs-title,
            .slideTabs-text {
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
            }
            .slideTabs-add {
              font-size: 0.875rem;
              margin-top: 4px;
              display: flex;
              .slideTabs-item {
                min-width: 14px;
                margin: -2px 6px 0 0;
              }
            }
          }
        }
      }
    }
  }
  .noData-wrap {
    .noData-note {
      font-size: 1rem;
      margin-bottom: 46px;
    }
    img {
      width: 130%;
      max-width: 400px;
    }
  }
  .postHistory {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    .postHistory-head {
      padding-bottom: 16px;
      padding: 14px 0 16px 32px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      .postHistory-number {
        padding-right: 10px;
        font-size: 1.125rem;
        font-weight: 700;
        display: flex;
        min-width: 200px;
        .item {
          min-width: 20px;
          margin: -2px 6px 0 0;
        }
      }
      .postHistory-date {
        background: #eef6ff;
        border-radius: 40px 0px 0px 40px;
        padding: 10px 32px;
        text-align: right;
        min-width: 350px;
        margin-left: auto;
        .spanDate {
          display: inline-block;
          font-size: 1rem;
          margin-right: 12px;
          margin-top: 4px;
        }
        .btn {
          padding: 0 12px;
          height: 32px;
          .btn-iconLeft {
            margin-right: 4px;
          }
        }
      }
    }
    .postList {
      height: 100%;
      padding: 0 32px;
      > ul {
        > li {
          padding: 12px 0 16px;
          border-bottom: 1px solid #b7c3cb;
        }
      }
      .postList-head {
        display: flex;
        justify-content: space-between;
        font-size: 1rem;
        .date {
          color: #99a5ae;
        }
      }
      .postList-content {
        margin-top: 8px;
        .postList-text {
          line-height: 120%;
          font-size: 1rem;
          white-space: pre-wrap;
        }
      }
    }
  }
}
</style>
