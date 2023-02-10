<template>
  <div v-loading="isLoading" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <H01S01FacilityFilter @onFinishScreenFilter="onFinishFilter" />
      </div>
      <div class="facilityContent">
        <div ref="facilityTblRef" class="facilityTbl scrollbar table-hg100" @scroll="onScroll">
          <div v-if="listFacility.length > 0" class="application-btn">
            <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
              <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
            </button>
            <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
              <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
            </button>
          </div>
          <table class="tableBox tableCustom">
            <thead>
              <tr class="custom-tr">
                <th>施設名/施設コード</th>
                <th>注意事項更新日</th>
                <th>所在地／電話番号</th>
                <th>主担当</th>
              </tr>
            </thead>
            <tbody v-if="listFacility.length > 0">
              <tr v-for="item of listFacility" :key="item" class="custom-tr" :class="item.ultmarc_delete_flag === 1 ? 'changeBgrDelete' : ''">
                <td>
                  <div class="facilityCode">
                    <div class="facilityCode-info">
                      <p class="label-title">{{ item.facility_short_name }}</p>
                      <p class="label-code">施設コード：{{ item.facility_cd }}</p>
                    </div>
                    <div class="facilityCode-btn">
                      <button
                        type="button"
                        class="btn btn-outline-primary btn-outline-primary--cancel btn-radius btn-hg32"
                        @click="facilityDetailsNotes(item.facility_cd)"
                      >
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_folder-open.svg')" alt="" /> </span
                        >詳細
                      </button>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="facilityCode">
                    <div class="facilityCode-info facilityCode-info-custom">
                      <p v-if="checkCondition(item).updateTime" class="label-txt" style="margin-top: 2px; margin-right: 5px">
                        {{ formatFullDate(convertDateSf(item.last_update_datetime, true)) }}
                      </p>
                      <span v-if="checkCondition(item).iconNoColor" style="margin-bottom: 2px">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-warn-no-color.svg')" alt="" />
                      </span>
                      <span v-if="checkCondition(item).iconColor" style="margin-bottom: 2px">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-warn-color.svg')" alt="" />
                      </span>
                    </div>
                    <div class="facilityCode-btn">
                      <button
                        v-if="checkCondition(item).button"
                        type="button"
                        class="btn btn-outline-primary btn-outline-primary--cancel btn-radius btn-hg32"
                        @click="facilityDetailsBasicInformation(item.facility_cd)"
                      >
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_openlink01.svg')" alt="" /> </span
                        >確認
                      </button>
                    </div>
                  </div>
                </td>
                <td>
                  <p class="facilityItem">
                    <span class="item"><img class="svg" src="@/assets/template/images/icon_maps.svg" alt="" /></span>{{ item.address }}
                  </p>
                  <p class="facilityItem">
                    <span class="item"><img class="svg" src="@/assets/template/images/icon_call.svg" alt="" /></span>{{ item.phone }}
                  </p>
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
      <div v-if="listFacility.length > 0" class="pagination">
        <div class="pagination-cases">全 {{ totalRecord }} 件</div>
        <PaginationTable :page-size="100" layout="prev, pager, next" :total="totalRecord" :current-page="currentPage" @current-change="handleCurrentChange" />
      </div>
    </div>
  </div>
</template>

<script>
import PaginationTable from '@/components/PaginationTable';
import H01_S01_FacilitySearchServices from '../../../api/H01/H01_S01_FacilitySearch';
import H01S01FacilityFilter from './H01_S01_FacilityFilter.vue';
export default {
  name: 'H01_S01_FacilitySearch',
  components: {
    PaginationTable,
    H01S01FacilityFilter
  },
  props: {
    checkLoading: [Boolean]
  },

  data() {
    return {
      checkLoader: false,
      listFacility: [],
      isLoading: false,
      totalRecord: 0,
      currentPage: 1,
      total_pages: 0,
      dataFilter: {
        user_cd: [],
        user_name: [],
        facility_short_name: '',
        facility_cd: '',
        phone: '',
        prefecture_cd: '',
        municipality: '',
        ultmarc_delete_flag: 1,
        facility_consideration_options: '指定なし',
        limit: 100,
        offset: 0
      },
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      currentUser: '',
      currentCD: '',
      toZ01S02: false,
      onScrollTop: 0,
      isLoadDefault: true
    };
  },
  watch: {},
  mounted() {
    this.emitter.emit('change-header', {
      title: '施設検索',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.H01_S01_FacilitySearch || 0;
    this.currentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.H01_S01_FacilitySearch || 1;

    this.toZ01S02 = this.$route?.params?.precaution_id ? true : false;
    const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
    this.currentUser = currentUserProxy ? currentUserProxy?.user_name : JSON.parse(localStorage.getItem('currentUser'))?.user_name;
    this.currentCD = currentUserProxy ? currentUserProxy?.user_cd : JSON.parse(localStorage.getItem('currentUser'))?.user_cd;
    const data = JSON.parse(localStorage.getItem('DataH01'));
    const data2 = JSON.parse(localStorage.getItem('DataResH01'));

    this.isLoadDefault = true;
    this.isLoading = true;
    if (data) {
      if (data2) {
        this.listFacility = data2.records;
        this.totalRecord = data2.pagination.total_items;
        this.currentPage = data2.pagination.current_page;
        setTimeout(() => {
          if (this.$refs.facilityTblRef) {
            this.$refs.facilityTblRef.scrollTop = this.onScrollTop;
          }
          this.js_pscroll(0.4);
          this.isLoadDefault = false;
          this.isLoading = false;
        }, 1000);
      } else {
        this.prevPageForOffset(data);
      }
    } else {
      this.isLoadDefault = false;
      this.isLoading = false;
    }

    this.js_pscroll(0.4);
  },
  methods: {
    onScrollLeft() {
      let content = document.querySelector('.facilityTbl');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.facilityTbl');
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
        let content = document.querySelector('.facilityTbl');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    checkCondition(obj) {
      const dataLastUpdateTime = obj?.last_update_datetime;
      const dataFacConsiderationUserCD = [];
      const dataFacConsiderationUserCDLogin = [];
      const dataFacConsiderationComfirmFlag = [];
      const dataFacConsiderationComfirmFlagLogin = [];
      obj?.facility_consideration_confirm.forEach((element) => {
        if (this.currentCD === element.user_cd) {
          dataFacConsiderationUserCDLogin.push(element.user_cd);
          dataFacConsiderationComfirmFlagLogin.push(element.confirmed_flag);
        }
        dataFacConsiderationUserCD.push(element.user_cd);
        dataFacConsiderationComfirmFlag.push(element.confirmed_flag);
      });
      if (!dataLastUpdateTime) {
        const dataReturn = {
          updateTime: false,
          iconNoColor: false,
          iconColor: false,
          button: false
        };
        return dataReturn;
      } else {
        if (!dataFacConsiderationUserCD.includes(this.currentCD)) {
          const dataReturn = {
            updateTime: true,
            iconNoColor: false,
            iconColor: false,
            button: false
          };
          return dataReturn;
        } else {
          if (dataFacConsiderationComfirmFlagLogin.filter((el) => el === 1).length !== dataFacConsiderationUserCDLogin.length) {
            const dataReturn = {
              updateTime: true,
              iconNoColor: false,
              iconColor: true,
              button: true
            };
            return dataReturn;
          } else {
            const dataReturn = {
              updateTime: true,
              iconNoColor: true,
              iconColor: false,
              button: false
            };
            return dataReturn;
          }
        }
      }
    },

    setScrollTop() {
      let scrollTop = this.$refs.facilityTblRef ? this.$refs.facilityTblRef.scrollTop : 0;
      this.setScrollScreen('H01_S01_FacilitySearch', scrollTop);
    },

    getListFacility(isFilter, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.isLoading = true;
      const data = {
        user_cd: this.dataFilter.user_cd ? this.dataFilter.user_cd : '',
        facility_short_name: this.dataFilter.facility_short_name ? this.dataFilter.facility_short_name : '',
        facility_cd: this.dataFilter.facility_cd ? this.dataFilter.facility_cd : '',
        phone: this.dataFilter.phone ? this.dataFilter.phone : '',
        prefecture_cd: this.dataFilter.prefecture_cd ? this.dataFilter.prefecture_cd : '',
        municipality_cd: this.dataFilter.municipality_cd ? this.dataFilter.municipality_cd : '',
        ultmarc_delete_flag: this.dataFilter.ultmarc_delete_flag ?? 0,
        facility_consideration_options: this.dataFilter.facility_consideration_options ? this.dataFilter.facility_consideration_options : '指定なし',
        limit: 100,
        offset: this.currentPage === 0 ? this.currentPage : this.currentPage - 1
      };
      H01_S01_FacilitySearchServices.getFacility(data)
        .then((res) => {
          if (res) {
            this.listFacility = res.data.data.records;
            this.totalRecord = res.data.data.pagination.total_items;
            localStorage.setItem('DataResH01', JSON.stringify(res.data.data));
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.setCurrentPageScreen('H01_S01_FacilitySearch', this.currentPage);

          setTimeout(() => {
            if (this.$refs.facilityTblRef) {
              if (this.onScrollTop && !isFilter) {
                this.$refs.facilityTblRef.scrollTop = this.onScrollTop;
              } else {
                this.$refs.facilityTblRef.scrollTop = 0;
              }
            }
            this.js_pscroll(0.4);

            this.isLoadDefault = false;
            this.isLoading = false;
          }, 1000);
        });
    },
    handleCurrentChange(val) {
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        const data = JSON.parse(localStorage.getItem('DataH01'));
        data.offset = val - 1;
        if (data.user_cd.length > 0) {
          data.user_cd = data.user_cd.toString();
        }
        if (data.user_name.length > 0) {
          data.user_name = data.user_name.toString();
        }
        this.dataFilter = data;
        localStorage.setItem('DataH01', JSON.stringify(data));
        this.setScrollScreen('H01_S01_FacilitySearch', 0);
        this.onScrollTop = 0;
        this.getListFacility(true, false);
      } else {
        this.dataFilter.offset = val - 1;
        this.currentPage = val;
        this.setScrollScreen('H01_S01_FacilitySearch', 0);
        this.onScrollTop = 0;
        this.getListFacility(true, false);
      }
    },
    onFinishFilter(data) {
      this.currentPage = 1;
      this.setScrollScreen('H01_S01_FacilitySearch', 0);
      this.onScrollTop = 0;
      this.dataFilter = data;
      this.getListFacility(true, false);
    },
    prevPageForOffset(data) {
      this.currentPage = data.offset + 1;
      this.dataFilter.offset = data.offset + 1;
      this.dataFilter = data;
      this.getListFacility(false, true);
    },
    facilityDetailsBasicInformation(facility_cd) {
      this.setScrollTop();
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '3' }
      });
    },
    facilityDetailsNotes(facility_cd) {
      this.setScrollTop();
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '1' }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_desktop1: 1700px;
.facilityTbl {
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
.changeBgrDelete {
  background: #f7f7f7 !important;
}
.wrapBtn {
  .dropdown-facilitySearch {
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
    .facilityForm {
      margin-top: 8px;
      .facilityForm-label {
        color: #5f6b73;
        margin-bottom: 6px;
        line-height: 20px;
      }
      .facilityForm-btnSelect {
        display: flex;
        flex-wrap: wrap;
        li {
          width: 33.333333%;
          margin-left: -1px;
          &:first-of-type {
            .btn {
              border-radius: 4px 0 0 4px;
            }
          }
          &:last-child {
            .btn {
              border-radius: 0 4px 4px 0;
            }
          }
          .btn {
            width: 100%;
            padding: 0 4px;
            border-radius: 0;
            &.active {
              position: relative;
            }
          }
        }
      }
      .checkDeleted {
        display: flex;
        flex-wrap: wrap;
        margin: 10px 0 0 -24px;
        li {
          width: 50%;
          padding-left: 24px;
        }
        .custom-control,
        .custom-control-description {
          width: 100%;
        }
      }
    }
    .facilityForm-row {
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        li {
          padding-left: 10px;
          &:first-of-type {
            width: calc(100% - 120px);
          }
          &:last-child {
            width: 120px;
          }
        }
      }
    }
    .facilityForm-btn {
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
.facilityContent {
  height: 100%;
  padding: 0 24px 6px;
  overflow: hidden;
  .facilityTbl {
    tr {
      th {
        font-size: 1rem;
        min-width: 210px;
        &:nth-child(1) {
          width: 1rem;
          min-width: 535px;
        }
        &:nth-child(2) {
          width: 1rem;
          min-width: 135px;
          @media (max-width: $viewport_desktop) {
            min-width: 210px !important;
          }
          @media (max-width: $viewport_desktop1) {
            min-width: 190px;
          }
        }
        &:nth-child(3) {
          min-width: 230px;
          width: 1rem;
        }
        &:nth-child(4) {
          width: 1rem;
          min-width: 210px;
        }
      }
      td {
        padding: 16px 12px;
        vertical-align: middle;
        .facilityCode {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          .facilityCode-info-custom {
            display: flex;
            align-items: center;
            margin-right: 6px;
          }
          .facilityCode-info {
            width: calc(100% - 96px);
            .label-title {
              font-size: 1rem;
              font-weight: 700;
              line-height: 120%;
            }
            .label-txt {
              width: 76px;
            }
            .label-code {
              color: #99a5ae;
            }
          }
          .facilityCode-btn {
            width: 90px;
            margin-left: auto;
            text-align: right;
            .btn {
              .btn-iconLeft {
                max-width: 14px;
                .svg {
                  width: 14px;
                }
              }
            }
          }
        }
        .facilityItem {
          display: flex;
          .item {
            min-width: 18px;
            margin-right: 8px;
          }
        }
      }
    }
  }
}
@media (min-width: 769px) and (max-width: 1024px) {
  .facilityTbl {
    tr {
      th {
        &:nth-child(1) {
          min-width: 350px !important;
        }
        &:nth-child(2) {
          min-width: 130px !important;
        }
        &:nth-child(3) {
          min-width: 185px !important;
        }
        &:nth-child(4) {
          min-width: 125px !important;
        }
      }
    }
  }
  .facilityCode {
    justify-content: start;
    .facilityCode-info-custom {
      width: unset !important;
    }
    .facilityCode-btn {
      width: unset !important;
      margin-left: unset !important;
    }
  }
}
</style>
