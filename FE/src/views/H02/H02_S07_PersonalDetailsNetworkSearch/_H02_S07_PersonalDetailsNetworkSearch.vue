<template>
  <div class="personalSearch">
    <H02S07PersonalDetailsNetworkSearchFilter
      :list-select-check-boxs="listSelectCheckBoxs"
      :dropdown-filter="dropdownFilter"
      @onFinishScreenFilter="onFinishFilter"
    />
    <div class="contentSearch">
      <div ref="tblSearch" class="tblSearch table-hg100 scrollbar" @scroll="onScroll">
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
              <th>個人名／個人コード</th>
              <th>施設略名／施設コード</th>
              <th>医局名</th>
              <th>出身県</th>
              <th>卒大／卒年</th>
              <th>主担当</th>
            </tr>
          </thead>
          <tbody v-if="listData.length > 0">
            <tr v-for="item of listData" :key="item">
              <td>
                <p class="tblSearch-link log-icon person-text-nomal" title_log="個人名" @click="personalDeltail(item.person_cd)">
                  <span style="cursor: pointer; color: #448add">{{ item.person_name }}</span>
                </p>
                <p class="tblSearch-label">個人コード：{{ item.person_cd }}</p>
              </td>
              <td>
                <p class="tblSearch-link" @click="facilityDetail(item.facility_cd)">
                  <sapn style="color: #448add; cursor: pointer">{{ item.facility_short_name }}</sapn>
                </p>
                <p class="tblSearch-label">施設コード：{{ item.facility_cd }}</p>
              </td>
              <td>
                {{
                  item.medical_office_name
                    ? item.medical_office_name
                    : (item.facility_name ? item.facility_name : '') + ' ' + (item.department_name ? item.department_name : '')
                }}
              </td>
              <td>{{ item.prefecture_name }}</td>
              <td>
                <p>{{ item.university_name }}</p>
                <p class="tblSearch-label">{{ item._graduation_year }}</p>
              </td>
              <td>
                <p>{{ item.user_name }}</p>
                <p>{{ item.org_label ? item.org_label : '(所属なし)' }}</p>
              </td>
            </tr>
          </tbody>
          <tr v-else>
            <td colspan="16">
              <div class="noData">
                <div class="noData-content">
                  <p v-if="!isLoadDefault" class="noData-text">該当するデータがありません。</p>
                  <div v-if="!isLoadDefault" class="noData-thumb">
                    <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div v-if="listData.length > 0" class="paginationSearch">
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
import H02S07PersonalDetailsNetworkSearchFilter from './H02_S07_PersonalDetailsNetworkSearchFilter.vue';
import H02_S07_PersonalDetailsNetworkSearchServices from '../../../api/H02/H02_S07_PersonalDetailsNetworkSearch';
import PaginationTable from '@/components/PaginationTable';
export default {
  name: '_H02_S07_PersonalDetailsNetworkSearch',
  components: {
    H02S07PersonalDetailsNetworkSearchFilter,
    PaginationTable
  },
  props: {
    changetab: {
      type: Number,
      default: 0
    },
    dropdownFilter: {
      type: String,
      default: ''
    },
    checkLoading: [Boolean]
  },
  emits: ['changeLoading'],
  data() {
    return {
      isLoadDefault: false,
      loading: false,
      listData: [],
      pageSizelistData: 0,
      currentPage: 1,
      total_pages: 0,
      dataFilter: {},
      person_cd: '',
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      listSelectCheckBoxs: []
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '個人詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.person_cd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd || localStorage.getItem('person_cd_prev') : '';
    this.$emit('changeLoading', true);
    setTimeout(() => {
      this.$emit('changeLoading', false);
    }, 500);
    this.listSelectCheckBox();
  },
  methods: {
    onScrollLeft() {
      let content = document.querySelector('.tblSearch');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblSearch');
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
        let content = document.querySelector('.tblSearch');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    getList(isLoadDefault) {
      this.js_pscroll();
      this.$emit('changeLoading', true);
      this.isLoadDefault = isLoadDefault;
      const data = {
        facility_cd: this.dataFilter.facility_cd ? this.dataFilter.facility_cd : '',
        graduation_year: this.dataFilter.graduation_year ? this.dataFilter.graduation_year : '',
        graduation_university_cd: this.dataFilter.graduation_university_cd ? this.dataFilter.graduation_university_cd : '',
        home_prefecture_cd: this.dataFilter.home_prefecture_cd ? this.dataFilter.home_prefecture_cd : '',
        medical_office_name: this.dataFilter.medical_office_name ? this.dataFilter.medical_office_name : '',
        medical_office_facility_cd: this.dataFilter.medical_office_facility_cd ? this.dataFilter.medical_office_facility_cd : '',
        medical_office_department_cd: this.dataFilter.medical_office_department_cd ? this.dataFilter.medical_office_department_cd : '',
        limit: 100,
        offset: this.currentPage === 0 ? this.currentPage : this.currentPage - 1
      };
      H02_S07_PersonalDetailsNetworkSearchServices.getListSearch(data)
        .then((res) => {
          this.listData = res.data.data.records;
          this.pageSizelistData = res.data.data.pagination.total_items;
          if (this.$refs.tblSearch) {
            this.$refs.tblSearch.scrollTop = 0;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.$emit('changeLoading', false);
          this.isLoadDefault = false;
        });
    },
    onFinishFilter(data) {
      this.dataFilter = data;
      this.getList(false);
    },
    handleCurrentChange(val) {
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.getList(false);
      } else {
        this.currentPage = val;
        this.getList(false);
      }
    },
    personalDeltail(person_cd) {
      this.emitter.emit('changeTabH02', {
        personCd: person_cd,
        goTab: '1'
      });
      this.$router.push({
        name: 'H02_PersonalDetails',
        params: {
          person_cd: person_cd
        },
        query: {
          tab: 1
        }
      });
    },

    facilityDetail(facility_cd) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '2' }
      });
    },
    listSelectCheckBox() {
      H02_S07_PersonalDetailsNetworkSearchServices.getScreenData(this.person_cd).then((res) => {
        this.listSelectCheckBoxs = res.data.data.personal_detail;
        this.listSelectCheckBoxs.forEach((element) => {
          element.facilityDepartment_name = (element.facility_name ? element.facility_name : '') + (element.department_name ? element.department_name : '');
        });
      });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
/** start css layout  **/
.tblSearch {
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
.groupPersonal {
  height: 100%;
  padding-top: 28px;
  background: #f2f2f2;
  @media (max-width: $viewport_tablet) {
    top: 16px;
  }
  .personalDetails {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .personalHead {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    padding: 12px 24px 0;
    .personalHead__user {
      width: 350px;
      padding-right: 10px;
      display: flex;
      align-items: flex-start;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-right: 0;
      }
      .personalHead-item {
        min-width: 22px;
        padding: 4px 8px 0 0;
      }
      .personalHead-content {
        .personalHead-label {
          .name {
            font-size: 24px;
            font-weight: 700;
            line-height: 140%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 220px;
            display: inline-block;
          }
          .btn {
            &.btn-light {
              border-radius: 2px;
              width: 60px;
              height: 24px;
              padding: 0 6px;
              margin: 6px 0 0 20px;
            }
          }
        }
        .personalHead-text {
          font-size: 16px;
          line-height: 120%;
        }
      }
    }
    .personalHead__info {
      width: calc(100% - 350px);
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 10px 0 0;
      }
      ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        @media (max-width: $viewport_tablet) {
          justify-content: flex-start;
        }
        li {
          margin-right: 36px;
          display: flex;
          @media (max-width: $viewport_desktop) {
            margin-right: 20px;
          }
          &:last-child {
            margin-right: 0;
          }
          .item {
            min-width: 20px;
            margin: -2px 5px 0 0;
          }
        }
      }
    }
  }
  .personalTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-top: 12px;
    .navTabs--personal {
      li {
        min-width: 195px;
        @media (max-width: $viewport_desktop) {
          min-width: 140px;
        }
        @media (max-width: $viewport_tablet) {
          min-width: inherit;
        }
        .navInfo {
          @media (max-width: $viewport_desktop) {
            padding: 12px 8px;
          }
          @media (max-width: $viewport_tablet) {
            font-size: 0.75rem;
          }
        }
        .navItem {
          @media (max-width: $viewport_tablet) {
            min-width: 15px;
            margin-right: 6px;
          }
        }
        .navItem {
          .svg {
            @media (max-width: $viewport_tablet) {
              width: 15px;
            }
          }
        }
      }
    }
    .tab-content {
      background: #fff;
      height: 100%;
      box-shadow: 0px 0px 5px #b7c3cb;
      position: relative;
      z-index: 1;
      overflow: hidden;
      .tab-pane {
        height: 100%;
      }
    }
  }
}
/** end css layout **/
/** Css H02_S07_PersonalDetailsNetworkSearch **/
.personalSearch {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  .personalSearch-filter {
    position: relative;
    display: flex;
    justify-content: flex-end;
    margin: 0 24px;
    .btn {
      &.btn-filter {
        padding: 0;
        width: 42px;
        height: 42px;
        border-radius: 0px 0px 8px 8px;
      }
    }
    .dropdown-filter--search {
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
      .formSearch-checkBox {
        ul {
          li {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 12px;
            &:last-child {
              margin-bottom: 0;
            }
            .formSearch-check {
              width: 120px;
              .custom-control,
              .custom-control-description {
                width: 100%;
              }
            }
            .formSearch-label {
              padding-left: 20px;
              width: calc(100% - 120px);
              color: #2d3033;
            }
          }
        }
      }
      .unselectedSearch {
        margin-top: 6px;
      }
      .notesSearch {
        color: #5f6b73;
        margin-top: 16px;
      }
      .btnFilter-search {
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
  }
  .contentSearch {
    height: 100%;
    padding: 56px 24px 8px;
    overflow: hidden;
    .tblSearch {
      tr {
        th {
          font-size: 1rem;
          &:nth-child(1) {
            width: 30%;
            // min-width: 520px;
            @media (min-width: 769px) and (max-width: 1024px) {
              min-width: 200px;
            }
          }
          &:nth-child(2) {
            width: 30%;
            // min-width: 467px;
            @media (min-width: 769px) and (max-width: 1024px) {
              min-width: 240px;
            }
          }
          &:nth-child(3) {
            width: 20%;
            // min-width: 330px;
            @media (min-width: 769px) and (max-width: 1024px) {
              min-width: 180px;
            }
          }
          &:nth-child(4) {
            width: 90px;
            min-width: 90px;
            max-width: 90px !important;
          }
          &:nth-child(5) {
            width: 1rem;
            // min-width: 115px;
            @media (min-width: 769px) and (max-width: 1024px) {
              min-width: 90px;
            }
          }
          &:nth-child(6) {
            width: 20%;
            // min-width: 300px;
            @media (min-width: 769px) and (max-width: 1024px) {
              min-width: 100px;
            }
          }
        }
        td {
          .tblSearch-link {
            a {
              font-size: 1rem;
            }
          }
          .tblSearch-label {
            color: #99a5ae;
          }
        }
      }
    }
  }
  .paginationSearch {
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
}
/*** Css modal ***/
.modal-workHistory {
  padding: 20px 24px 32px;
  .tbl-workHistory {
    max-height: 420px;
    tr {
      &:hover {
        td {
          font-weight: 700;
          background: #eef6ff;
        }
      }
      th {
        font-size: 1rem;
        min-width: 120px;
      }
      td {
        padding: 12px;
        .workHistory-link {
          font-size: 1rem;
        }
      }
    }
  }
  .btn-workHistory {
    text-align: center;
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
