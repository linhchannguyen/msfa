<template>
  <div class="working">
    <div class="contentWorking">
      <div class="tblWorking table-hg100 scrollbar" @scroll="onScroll">
        <div v-if="listData.length > 0" class="application-btn">
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
              <th>施設コード</th>
              <th>勤務先</th>
              <th>所属部科</th>
              <th>施設役職</th>
              <th>大学職位</th>
              <th>開始日・終了日</th>
            </tr>
          </thead>
          <tbody v-if="listData.length > 0">
            <tr v-for="item of listData" :key="item">
              <td>{{ item.facility_cd }}</td>
              <td>
                <span class="link" style="color: #696b80 person-text-nomal" @click="facilityDetail(item.facility_cd)">{{ item.facility_short_name }}</span>
              </td>
              <td>{{ item.department_name }}</td>
              <td>{{ item.position_cd ? item.position_name : '' }}</td>
              <td>{{ item.academic_position_cd ? item.academic_position_name : '' }}</td>
              <td>{{ formatFullDate(item.start_date) }} {{ JapaneseTilde() }} {{ item.end_date === '9999-12-31' ? '' : formatFullDate(item.end_date) }}</td>
            </tr>
          </tbody>
          <tr v-else>
            <td colspan="16">
              <div class="noData">
                <div class="noData-content">
                  <p v-if="!loading" class="noData-text">該当するデータがありません。</p>
                  <div v-if="!loading" class="noData-thumb">
                    <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div v-if="listData.length > 0" class="paginationWork">
      <div class="paginationSearch-cases">全 {{ pageSizelistData }} 件</div>
      <PaginationTable
        :page-size="100"
        layout="prev, pager, next"
        :total="pageSizelistData"
        :current-page="currentPage"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
</template>

<script>
import H02S03PersonalDetailsWorkingHistory from '../../../api/H02/_H02_S03_PersonalDetailsWorkingHistory';
import PaginationTable from '@/components/PaginationTable';
export default {
  name: '_H02_S07_PersonalDetailsNetworkSearch',
  components: {
    PaginationTable
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      loading: false,
      listData: [],
      pageSizelistData: 0,
      currentPage: 1,
      total_pages: 0,
      person_cd: '',
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '個人詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.person_cd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd : '';
    this.getList();
  },
  methods: {
    onScrollLeft() {
      let content = document.querySelector('.tblWorking');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblWorking');
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
        let content = document.querySelector('.tblWorking');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    getList() {
      this.$emit('changeLoading', true);
      const data = {
        person_cd: this.person_cd ? this.person_cd : localStorage.getItem('person_cd_prev'),
        limit: 100,
        offset: this.currentPage === 0 ? this.currentPage : this.currentPage - 1
      };
      H02S03PersonalDetailsWorkingHistory.getList(data)
        .then((res) => {
          this.listData = res.data.data.records;
          this.pageSizelistData = res.data.data.pagination.total_items;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.$emit('changeLoading', false);
        });
    },
    facilityDetail(facilityCd) {
      const facility_cd = facilityCd;
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '1' }
      });
    },
    handleCurrentChange(val) {
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.getList();
      } else {
        this.currentPage = val;
        this.getList();
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.tblWorking {
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
.working {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding-top: 24px;
  .contentWorking {
    height: 100%;
    padding: 0 24px 8px;
    overflow: hidden;
  }
  .paginationWork {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 10px 24px 18px;
    .paginationSearch-cases {
      padding-right: 10px;
      color: #8e8e8e;
      font-weight: 500;
    }
  }
  .tblWorking {
    min-height: 71px;
    tr {
      th {
        &:first-of-type {
          width: 85px;
        }
        min-width: 120px;
      }
    }
  }
}
</style>
