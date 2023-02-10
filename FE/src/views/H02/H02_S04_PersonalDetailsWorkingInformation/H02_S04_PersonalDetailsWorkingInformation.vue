<template>
  <div class="workInfo">
    <div class="contentWork" :style="datas.length > 0 ? null : 'display:none'">
      <div class="application">
        <div class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
        </div>
        <div class="tblWork table-hg100 scrollbar" @scroll="onScroll">
          <table class="tableCustom">
            <thead>
              <tr>
                <th rowspan="2">施設略名</th>
                <th :colspan="phases.length">フェーズ</th>
              </tr>
              <tr>
                <th v-for="phase in phases" :key="phase">{{ phase.phase_name }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in datas" :key="item">
                <td>
                  <div class="tblWork-facility">
                    <div class="tblWork-content">
                      <p class="tblWork-company person-text-nomal log-icon" title_log="施設略名" @click="facility(item.facility_cd)">
                        <span style="color: #4a8ce0; cursor: pointer"> {{ item.facility_short_name }}</span>
                      </p>
                      <p class="tblWork-name">
                        <span class="item"><img src="@/assets/template/images/icon_user03.svg" alt="" /></span>
                        {{ item.user_name }}
                      </p>
                      <p class="tblWork-name">
                        <span class="item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                        {{ item.org_label }}
                      </p>
                    </div>
                    <div class="tblWork-label">
                      <ul v-for="icon in item.segment" :key="icon">
                        <li>
                          <span class="span-label span-label--violet">{{ icon.segment_name }}</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </td>
                <td v-for="p in phases" :key="p">
                  <p v-for="phaseName of item.item" :key="phaseName" class="tblWork-text">
                    {{ phaseName.phase_cd === p.phase_cd ? phaseName.product_name : '' }}
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <EmptyData v-if="datas.length === 0 && !loading" custom-class="custom-class-no-data" />

    <div v-if="datas.length > 0" class="pagination pagination-custom">
      <div class="paginationSearch-cases">全 {{ pagination.total_items }} 件</div>
      <div class="pagination-bord">
        <PaginationTable
          layout="prev, pager, next"
          :current-page="pagination.current_page"
          :total-page="pagination.total_pages"
          @current-change="handleChangePage"
        />
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
    :show-close="true"
    @close="closeModal()"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModalZ05S01" @handleConfirm="onCloseModal"></component>
    </template>
  </el-dialog>
</template>

<script>
import H02_S04_PersonalDetailsWorkingInformationService from '../../../api/H02/H02_S04_PersonalDetailsWorkingInformation';
import PaginationTable from '@/components/Pagination';
// import { markRaw } from 'vue';
import EmptyData from '@/components/EmptyData';
import H01S02FacilityDetailsBasicInformation from '../../H01/H01_S02_FacilityDetailsBasicInformation/_H01_S02_FacilityDetailsBasicInformation.vue';

export default {
  name: 'H02_S04_PersonalDetailsWorkingInformation',
  components: {
    PaginationTable,
    EmptyData,
    H01S02FacilityDetailsBasicInformation
  },
  props: {
    changetab: {
      type: String,
      default: '3'
    },
    checkLoading: [Boolean]
  },
  data() {
    return {
      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      },
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      datas: [],
      phases: [],
      loading: false,
      filter: { limit: 100, offset: 0 },
      showScrollLeft: false,
      showScrollRight: false
    };
  },
  watch: {
    changetab: function (value) {
      // eslint-disable-next-line eqeqeq
      if (value == 3) {
        this.getData(this.filter);
        this.getScreenData();
      }
    }
  },
  created() {
    this.$emit('changeLoading', true);
    // eslint-disable-next-line eqeqeq
    if (this.changetab == 3) {
      this.getData(this.filter);
      this.getScreenData();
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '個人詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    let area_rows = window.innerWidth;
    if (area_rows <= 1547) {
      this.showScrollRight = true;
    } else {
      this.showScrollRight = false;
    }
    window.addEventListener('resize', function () {
      let area_rows = window.innerWidth;
      if (area_rows <= 1547) {
        this.showScrollRight = true;
      } else {
        this.showScrollRight = false;
      }
    });
  },
  methods: {
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
        let content = document.querySelector('.tblWork');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    onScrollLeft() {
      let content = document.querySelector('.tblWork');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblWork');
      content.scrollLeft += 200;
    },
    /**Modal */
    facility(facility_cd) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '1' }
      });
    },
    getDataChildItem(data) {
      const value = Object.values(data);
      return value;
    },
    handleChangePage(page) {
      this.pagination.current_page = page;
      this.filter.offset = page - 1;
      this.getData(this.filter);
    },
    /** API */
    getData(params) {
      const person_cd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd || localStorage.getItem('person_cd_prev') : '';
      H02_S04_PersonalDetailsWorkingInformationService.fetchData({ person_cd, ...params })
        .then((res) => {
          if (res) {
            this.pagination = res.pagination;
            this.datas = res?.records;
            if (this.datas.length <= 3) {
              const application = document.querySelector('.application');
              application.style.height = 'auto';
            }
          }
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => this.$emit('changeLoading', false));
    },
    getScreenData() {
      H02_S04_PersonalDetailsWorkingInformationService.getScreenDataService().then((res) => {
        if (res) {
          this.phases = res.phase_name.map((s) => ({ ...s, product_name: null }));
        }
      });
    }
    /** End */
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.workInfo {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding-top: 24px;
  .contentWork {
    height: 90%;
    padding: 0 24px 8px;
    overflow: hidden;
  }
  .tblWork {
    thead {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      position: -webkit-sticky;
      position: sticky;
      top: 0;
      z-index: 3;
      tr {
        &:first-of-type {
          th {
            color: #fff;
            &:first-of-type {
              border-right: 1px solid #e3e3e3;
              font-size: 1rem;
              position: -webkit-sticky;
              position: sticky;
              top: 0;
              left: 0;
              z-index: 4;
              background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
              background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
              background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
              &::after,
              &::before {
                position: absolute;
                background: #e3e3e3;
                display: block;
                content: '';
              }
              &::after {
                top: 0;
                right: -1px;
                height: 100%;
                width: 1px;
              }
              &::before {
                bottom: -1px;
                left: 0;
                height: 1px;
                width: 100%;
              }
            }
            &:last-child {
              text-align: center;
            }
          }
        }
        &:last-child {
          th {
            background: #f2f2f2;
            text-align: center;
            position: relative;
            &:not(:last-child):after {
              content: '';
              width: 1px;
              background-color: #cad4db;
              height: calc(100% - 8px);
              position: absolute;
              top: 4px;
              right: 0;
              z-index: 1;
            }
            &::before {
              bottom: -1px;
              left: 0;
              height: 1px;
              width: 100%;
              position: absolute;
              background: #e3e3e3;
              display: block;
              content: '';
            }
          }
        }
        th {
          vertical-align: middle;
          font-size: 0.875rem;
          padding: 6px 32px;
        }
      }
    }
    tbody {
      tr {
        td {
          width: 1rem;
          min-width: 180px;
          background: #fff;
          padding: 12px 32px;
          &:nth-child(1) {
            min-width: 360px;
            position: -webkit-sticky;
            position: sticky;
            left: 0;
            z-index: 2;
            &::after {
              position: absolute;
              top: 0;
              right: -1px;
              content: '';
              height: 100%;
              width: 1px;
              background: #e3e3e3;
              display: block;
            }
          }
        }
      }
    }
    .tblWork-text {
      font-size: 1rem;
    }
    .tblWork-facility {
      display: flex;
      justify-content: space-between;
      .tblWork-content {
        .tblWork-company {
          font-size: 1.25rem;
          a {
            color: #448add;
            cursor: pointer;
          }
          a:hover {
            color: #5998e6;
          }
        }
        .tblWork-name {
          display: flex;
          margin-top: 4px;
          .item {
            min-width: 12px;
            margin: -2px 5px 0 0;
            img {
              width: 12px;
            }
          }
        }
      }
      .tblWork-label {
        padding-left: 10px;

        ul {
          li {
            padding: 2px 0;
            width: 100px;
            text-align: center;
          }
        }
      }
    }
  }
  .paginationWork {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 10px 24px 18px;
    .paginationSearch-cases {
      padding-right: 20px;
      color: #8e8e8e;
      font-weight: 500;
    }
  }
  .paginationSearch-cases {
    padding-right: 20px;
    color: #8e8e8e;
    font-weight: 500;
  }
}
.application {
  position: relative;
  height: 100%;
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
      position: absolute;
      top: calc(50% - 15px);
      height: 46px;
      width: 52px;
      padding: 0;
      background: rgba(63, 75, 86, 0.4);
      z-index: 1;
      &.btn--prev {
        left: 360px;
        border-radius: 0px 60px 60px 0px;
        z-index: 9;
      }
      &.btn--next {
        right: 0px;
        border-radius: 60px 0px 0px 60px;
        z-index: 5;
        .svg {
          transform: rotate(-180deg);
        }
      }
    }
  }

  .bd-top-none {
    border-top: none;
    margin-top: unset;
  }

  .carouselWrap {
    padding: 10px 20px;
    > ul {
      display: flex;
      flex-wrap: nowrap;
      > li {
        flex: 1 0 50%;
        border-right: 1px solid #e3e3e3;
        &:last-child {
          border-right: none;
        }
      }
    }
    .carouselHead {
      border-bottom: 1px solid #e3e3e3;
      padding: 0 24px 12px;
      text-align: center;
    }
    .carouselContent {
      padding: 0 24px;
    }
    .carouselContent-lst {
      margin-top: -8px;
      li {
        margin-top: 16px;
      }
      .carouselContent-name {
        font-weight: 700;
      }
      .carouselContent-add {
        display: flex;
        .carouselContent-item {
          min-width: 16px;
          margin-right: 5px;
          margin-top: -2px;
        }
      }
    }
  }
}
</style>
