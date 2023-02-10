<template>
  <div v-loading="isLoading" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="approvedList">
        <div class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
        </div>
        <div ref="approvedList_tbl" class="approvedList-tbl table-hg100 scrollbar" @scroll="onScroll">
          <table class="tableBox tableCustom">
            <thead>
              <tr>
                <th>報告日付</th>
                <th>提出者</th>
                <th>提出日時</th>
                <th colspan="2">提出者メモ</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in listNotApproved" :key="item.report_id" :scroll-attr="index">
                <td>
                  <span v-if="mode_week">
                    {{ formatFullDateCustom(item.report_period_start_date) + ' (' + getDay(item.report_period_start_date) + ')' }}
                  </span>
                  <p v-if="mode_week" style="width: 120px; text-align: center">～</p>
                  <span>{{ formatFullDateCustom(item.report_period_end_date) + ' (' + getDay(item.report_period_end_date) + ')' }}</span>
                </td>
                <td>
                  <p class="approvedList-userName">{{ item.user_name }}</p>
                  <p class="approvedList-add">
                    <span class="item"><ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" /></span>
                    <span>{{ item.org_label }}</span>
                  </p>
                </td>
                <td>
                  {{ formatFullDate(item.request_datetime) + ' (' + getDay(item.request_datetime) + ')' + ' ' + formatTimeHourMinute(item.request_datetime) }}
                </td>
                <td>
                  <div class="textover">
                    {{ item.submission_remarks }}
                  </div>
                </td>
                <td>
                  <button
                    type="button"
                    class="btn btn-outline-primary btn-radius btn-outline-primary--cancel"
                    :scroll-attr="index"
                    @click="callScreen_A03_S02(item)"
                  >
                    <span class="btn-iconLeft">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_openlink01.svg')" alt="" />
                    </span>
                    <span>確認</span>
                  </button>
                </td>
              </tr>
              <tr v-if="!listNotApproved.length && !isLoading" class="not-hover">
                <td colspan="5">
                  <EmptyData />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div v-if="listNotApproved.length > 0" class="pagination">
        <div class="pagination-cases">全 {{ pagination.total_items }} 件</div>
        <PaginationTable
          :current-page="pagination.current_page"
          :total="pagination.total_items"
          :page-size="pagination.limit"
          @current-change="handleChangePage"
        />
      </div>
    </div>
  </div>
</template>

<script>
import A03_S03_Service from '@/api/A03/A03_S03_NotApprovedListServices';

export default {
  name: 'A03_S03_NotApprovedList',
  data() {
    return {
      listNotApproved: [],
      isLoading: false,
      mode_week: false,
      pagination: {
        limit: 100,
        offset: 0,
        total_items: 1,
        total_pages: 100,
        previous_page: 1,
        next_page: 1,
        current_page: 1,
        first_page: 1,
        last_page: 1
      },
      props: {
        checkLoading: [Boolean]
      },

      showScrollLeft: false,
      showScrollRight: false,
      onScrollTop: 0
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '未承認報告管理',
      icon: 'icon_speech-meeting-color.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.A03_S03_NotApprovedList;
    this.pagination.current_page = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.A03_S03_NotApprovedList || 1;
    this.pagination.offset = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.A03_S03_NotApprovedList - 1 || 0;

    this.getUnapprovedList(this.pagination);
  },
  methods: {
    handleChangePage(page) {
      this.pagination = {
        ...this.pagination,
        current_page: page,
        offset: page === 0 ? page : page - 1
      };
      this.onScrollTop = 0;
      this.getUnapprovedList(this.pagination);
    },
    readMore(index) {
      let arr = this.listNotApproved;
      arr[index] = {
        ...arr[index],
        readMoreActivated: !arr[index].readMoreActivated
      };
      this.listNotApproved = arr;
    },
    callScreen_A03_S02(item) {
      let scrollTop = this.$refs.approvedList_tbl ? this.$refs.approvedList_tbl.scrollTop : 0;
      this.setScrollScreen('A03_S03_NotApprovedList', scrollTop);
      this.setCurrentPageScreen('A03_S03_NotApprovedList_count', this.listNotApproved.length);
      this.$router.push({
        name: 'A03_S02_DailyReport',
        params: {
          ...item,
          reportId: item.report_id,
          startDate: item.report_period_start_date,
          endDate: this.mode_week ? item.report_period_end_date : item.report_period_start_date
        }
      });
    },
    onScrollLeft() {
      let content = document.querySelector('.approvedList-tbl');
      content.scrollLeft -= 180;
    },

    onScrollRight() {
      let content = document.querySelector('.approvedList-tbl');
      content.scrollLeft += 180;
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
        let content = document.querySelector('.approvedList-tbl');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    // call api
    getUnapprovedList(params) {
      this.isLoading = true;
      A03_S03_Service.getUnapprovedListService(params)
        .then((res) => {
          this.listNotApproved = [];
          const dataRes = res.data.data;
          let arr = dataRes.data.records.map((item) => ({
            ...item,
            readMoreActivated: false
          }));
          this.listNotApproved = dataRes.data.records.length > 0 ? arr : [];
          this.mode_week = dataRes.mode_week;
          this.pagination = {
            ...this.pagination,
            ...dataRes.data.pagination
          };
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          this.js_pscroll(0.5);
          this.setCurrentPageScreen('A03_S03_NotApprovedList', this.pagination.current_page);
          if (this.$refs.approvedList_tbl) {
            this.$refs.approvedList_tbl.scrollTop = this.onScrollTop;
          }
          this.isLoading = false;
          // this.goToIndex();
        });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.pagination {
  justify-content: flex-end;
  .pagination-cases {
    margin-right: 30px;
  }
}
.approvedList {
  height: 100%;
  padding: 28px 24px 0;
  position: relative;
  .application-btn {
    .btn {
      position: absolute;
      top: calc(50% - 23px);
      height: 46px;
      width: 52px;
      padding: 0;
      background: rgba(63, 75, 86, 0.4);
      z-index: 1;
      &.btn--prev {
        left: 24px;
        border-radius: 0px 60px 60px 0px;
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
  .approvedList-tbl {
    tr {
      th,
      td {
        vertical-align: middle;
        min-width: 120px;
      }
      td {
        &:nth-child(1) {
          width: 1rem;
          min-width: 168px;
        }
        &:nth-child(3) {
          width: 1rem;
          min-width: 180px;
        }
        &:nth-child(2) {
          width: 1rem;
          min-width: 200px;
        }
        &:nth-child(4) {
          min-width: 200px;
          max-width: 265px;
        }
        &:nth-child(5) {
          width: 1rem;
          min-width: 100px;
          text-align: center;
        }
        .approvedList-userName {
          font-size: 16px;
          font-weight: 700;
        }
        .approvedList-add {
          display: flex;
          .item {
            min-width: 13px;
            margin-right: 6px;
            margin-top: -2px;
          }
        }
        .btn {
          padding: 0 12px;
          height: 32px;
          white-space: nowrap;
          .btn-iconLeft {
            top: -2px;
          }
        }
      }
    }
  }
}

.textover {
  display: block;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
