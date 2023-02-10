<template>
  <div class="wrapContent">
    <div v-loading="isLoading" class="groupContent">
      <div class="wrapBtn">
        <H02S01PersonalSearchFilter @onFinishScreenFilter="onFinishFilter" />
      </div>
      <div class="personalContent">
        <div ref="tblUserContent" class="personalTbl scrollbar table-hg100" @scroll="onScroll">
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
                <th>注意事項更新日</th>
                <th class="left">異動</th>
                <th>施設略名／施設コード</th>
                <th class="left">所属部科</th>
                <th class="left">役職</th>
                <th>主担当</th>
              </tr>
            </thead>
            <tbody v-if="listData.length > 0">
              <tr v-for="item of listData" :key="item" :class="item.ultmarc_delete_flag === 1 ? 'active' : ''">
                <td>
                  <div class="personalCode">
                    <div class="personalCode-info personalCode-info-2">
                      <p class="label-title person-text-bold link" @click="clickDetail(item.person_cd)">
                        {{ item.person_name }}
                      </p>
                      <p class="label-code">個人コード：{{ item.person_cd }}</p>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="personalCode" style="flex-wrap: nowrap">
                    <div class="personalCode-info personalCode-info-custom">
                      <p v-if="checkCondition(item).updateTime" class="label-txt" style="margin-top: 2px; margin-right: 3px; width: 80px">
                        {{ formatFullDate(convertDateSf(item.last_update_datetime, true)) }}
                      </p>
                      <span v-if="checkCondition(item).iconNoColor">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-warn-no-color.svg')" alt="" />
                      </span>
                      <span v-if="checkCondition(item).iconColor">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-warn-color.svg')" alt="" />
                      </span>
                    </div>
                    <div class="flex">
                      <div v-if="checkCondition(item).button" class="personalCode-btn">
                        <button
                          type="button"
                          class="btn btn-outline-primary btn-outline-primary--cancel btn-radius btn-hg32"
                          @click="clickComfirm(item.person_cd)"
                        >
                          <span class="btn-iconLeft">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_openlink01.svg')" alt="" /> </span
                          >確認
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span v-if="item.move_flag === 1"><img class="svg" src="@/assets/template/images/icon_rotate-doctor.svg" alt="" /></span>
                </td>
                <td>
                  <p class="personal-text person-text-bold log-icon" title_log="施設略名" @click="facility(item.facility_cd)">
                    <span style="color: #448add; cursor: pointer">{{ item.facility_short_name }}</span>
                  </p>
                  <p class="personal-code">施設コード：{{ item.facility_cd }}</p>
                </td>
                <!-- <td>{{ item.academic_position_cd === '' ? '' : item.position_name }}</td>
                <td>{{ item.hospital_position_cd === '' ? '' : item.position_name }}</td> -->
                <td>{{ item.department_name }}</td>
                <td>
                  {{ item.academic_position_name }} <span v-if="item.academic_position_nam && item.hospital_position_name">／</span>
                  {{ item.hospital_position_name }}
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
      <div v-if="listData.length > 0" class="pagination">
        <div class="pagination-cases">全 {{ pageSizelistData }} 件</div>
        <PaginationTable
          :page-size="100"
          layout="prev, pager, next"
          :total="pageSizelistData"
          :current-page="currentPage"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>
  </div>
</template>

<script>
import H02S01PersonalSearchFilter from './_H02_S01_PersonalSearchFilter.vue';
import PaginationTable from '@/components/PaginationTable';
import H02_S01_PersonalSearchServiecs from '../../../api/H02/H02_S01_PersonalSearch';
export default {
  name: 'H02_S01_PersonalSearch',
  components: {
    H02S01PersonalSearchFilter,
    PaginationTable
  },
  props: {
    checkLoading: [Boolean]
  },
  // emits: ['onFinishScreenFilter'],
  data() {
    return {
      listData: [],
      isLoading: false,
      isLoadDefault: true,
      pageSizelistData: 0,
      currentPage: 1,
      total_pages: 0,
      dataFilter: {
        personName: '',
        personCd: '',
        move_flag: false,
        ultmarc_delete_flag: false,
        facility_cd: '',
        facility_short_name: '',
        user_cd: [],
        user_name: [],
        offset: 0
      },
      currentUser: '',
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      toZ01S02: false,
      onScrollTop: 0
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '個人検索',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.H02_S01_PersonalSearch || 0;
    this.currentPage = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.H02_S01_PersonalSearch || 1;
    this.toZ01S02 = this.$route?.params?.precaution_id ? true : false;
    const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
    this.currentUser = currentUserProxy ? currentUserProxy?.user_name : JSON.parse(localStorage.getItem('currentUser'))?.user_name;
    this.currentCD = currentUserProxy ? currentUserProxy?.user_cd : JSON.parse(localStorage.getItem('currentUser'))?.user_cd;
    const data = JSON.parse(localStorage.getItem('DataH02'));
    const data2 = JSON.parse(localStorage.getItem('DataResH02'));
    this.isLoadDefault = true;
    this.isLoading = true;
    if (data) {
      if (data2) {
        this.listData = data2.records;
        this.pageSizelistData = data2.pagination.total_items;
        this.currentPage = data2.pagination.current_page;
        setTimeout(() => {
          if (this.$refs.tblUserContent) {
            this.$refs.tblUserContent.scrollTop = this.onScrollTop;
          }
          this.isLoadDefault = false;
          this.isLoading = false;
          this.js_pscroll(0.4);
        }, 1000);
      } else {
        this.prevPageForOffset(data);
      }
    } else {
      this.isLoadDefault = false;
      this.isLoading = false;
    }
  },
  methods: {
    prevPageForOffset(data) {
      this.currentPage = data.offset + 1;
      this.dataFilter.offset = data.offset + 1;
      this.dataFilter = data;
      this.getList(false, true);
    },
    onScrollLeft() {
      let content = document.querySelector('.personalTbl');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.personalTbl');
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
        let content = document.querySelector('.personalTbl');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },

    setScrollTop() {
      let scrollTop = this.$refs.tblUserContent ? this.$refs.tblUserContent.scrollTop : 0;
      this.setScrollScreen('H02_S01_PersonalSearch', scrollTop);
    },
    checkCondition(obj) {
      const dataLastUpdateTime = obj?.last_update_datetime;
      const dataFacConsiderationUserCD = [];
      const dataFacConsiderationUserCDLogin = [];
      const dataFacConsiderationComfirmFlag = [];
      const dataFacConsiderationComfirmFlagLogin = [];
      obj?.person_consideration_confirm.forEach((element) => {
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

    getList(isFilter, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.isLoading = true;
      const data = {
        person_consideration_options: this.dataFilter.option ? this.dataFilter.option : this.toZ01S02 ? '通知あり未確認' : '指定なし',
        person_cd: this.dataFilter.person_cd || this.dataFilter.personCd ? this.dataFilter.person_cd || this.dataFilter.personCd : '',
        person_name: this.dataFilter.person_name || this.dataFilter.personName ? this.dataFilter.person_name || this.dataFilter.personName : '',
        facility_short_name: this.dataFilter.facility_short_name ? this.dataFilter.facility_short_name : '',
        facility_cd: this.dataFilter.facility_cd ? this.dataFilter.facility_cd : '',
        user_cd: this.dataFilter.user_cd ? this.dataFilter.user_cd.toString() : '',
        move_flag: this.dataFilter.move_flag ?? 0,
        ultmarc_delete_flag: this.dataFilter.ultmarc_delete_flag ?? 0,
        limit: 100,
        offset: this.currentPage === 0 ? this.currentPage : this.currentPage - 1
      };
      H02_S01_PersonalSearchServiecs.getListSearch(data)
        .then((res) => {
          if (res) {
            this.listData = res.data.data.records;
            this.pageSizelistData = res.data.data.pagination.total_items;
            localStorage.setItem('DataResH02', JSON.stringify(res.data.data));
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          this.setCurrentPageScreen('H02_S01_PersonalSearch', this.currentPage);

          if (this.$refs.tblUserContent) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.tblUserContent.scrollTop = this.onScrollTop;
            } else {
              this.$refs.tblUserContent.scrollTop = 0;
            }
          }
          this.js_pscroll(0.4);

          this.isLoading = false;
          this.isLoadDefault = false;
        });
    },
    handleCurrentChange(val) {
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        const data = JSON.parse(localStorage.getItem('DataH02'));
        data.offset = val - 1;
        if (data.user_cd.length > 0) {
          data.user_cd = data.user_cd.toString();
        }
        this.dataFilter = data;
        localStorage.setItem('DataH02', JSON.stringify(data));
        this.setCurrentPageScreen('H02_S01_PersonalSearch', 0);
        this.onScrollTop = 0;
        this.getList(true, false);
      } else {
        this.dataFilter.offset = val - 1;
        this.currentPage = val;
        this.setCurrentPageScreen('H02_S01_PersonalSearch', 0);
        this.onScrollTop = 0;
        this.getList(true, false);
      }
    },
    onFinishFilter(data) {
      this.currentPage = 1;
      this.setCurrentPageScreen('H02_S01_PersonalSearch', 0);
      this.onScrollTop = 0;
      this.dataFilter = data;
      this.getList(true, false);
    },
    clickComfirm(person_cd) {
      localStorage.setItem('isRedirectH02_S01', true);

      this.setScrollTop();
      this.$router.push({
        name: 'H02_PersonalDetails',
        params: {
          person_cd
        },
        query: { tab: '4' }
      });
    },
    clickDetail(person_cd) {
      localStorage.setItem('isRedirectH02_S01', true);

      this.setScrollTop();
      this.$router.push({
        name: 'H02_PersonalDetails',
        params: {
          person_cd
        },
        query: { tab: '1' }
      });
    },
    facility(facility_cd) {
      this.setScrollTop();
      const data = JSON.parse(localStorage.getItem('DataH02'));
      const data2 = JSON.parse(localStorage.getItem('DataResH02'));
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '1' }
      });
      setTimeout(() => {
        localStorage.setItem('DataH02', JSON.stringify(data));
        localStorage.setItem('DataResH02', JSON.stringify(data2));
      }, 500);
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.personalTbl {
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
.left {
  text-align: unset !important;
}
.active {
  background: #f7f7f7 !important;
}
.wrapBtn {
  .dropdown-personalSearch {
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
.personalContent {
  height: 100%;
  padding: 0 24px 6px;
  overflow: hidden;
  .personalTbl {
    tr {
      th {
        font-size: 1rem;
        &:nth-child(1) {
          width: 1rem;
          min-width: 318px;
          @media (max-width: $viewport_desktop) {
            min-width: 200px;
          }
        }
        &:nth-child(2) {
          width: 1rem;
          min-width: 150px;
          @media (max-width: $viewport_desktop) {
            width: 130px;
            min-width: 210px;
          }
        }
        &:nth-child(3) {
          width: 1rem;
          min-width: 80px;
          @media (max-width: $viewport_desktop) {
            min-width: 80px;
          }
        }
        &:nth-child(4) {
          width: 1rem;
          min-width: 500px;
          @media (max-width: $viewport_desktop) {
            min-width: 300px;
          }
        }
        &:nth-child(5) {
          width: 1rem;
          min-width: 200px;
          @media (max-width: $viewport_desktop) {
            min-width: 130px;
          }
        }
        &:nth-child(6) {
          width: 1rem;
          min-width: 130px;
          @media (max-width: $viewport_desktop) {
            min-width: 130px;
          }
        }
        &:nth-child(7) {
          width: 1rem;
          min-width: 200px;
          @media (max-width: $viewport_desktop) {
            min-width: 200px;
          }
        }
      }
      th,
      td {
        vertical-align: middle;
        &:nth-child(3),
        &:nth-child(5),
        &:nth-child(6) {
          text-align: center;
        }
      }
      td {
        padding: 16px 12px;
        &:nth-child(5),
        &:nth-child(6) {
          font-size: 1rem;
        }
        .personalCode {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          .flex {
            display: flex;
            gap: 8px;
            align-items: center;
            min-width: 90px;
          }
          .personalCode-info-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
          }
          .personalCode-info-2 {
            width: unset !important;
          }
          .personalCode-info {
            // width: calc(100% - 98px);
            .label-title {
              font-size: 1.2rem;
              font-weight: 700;
              line-height: 120%;
            }
            .label-code {
              color: #99a5ae;
            }
          }
          .personalCode-btn {
            width: 76px;
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
        .personal-text {
          font-size: 1rem;
        }
        .personal-code {
          color: #99a5ae;
        }
      }
      th {
        @media (min-width: 769px) and (max-width: 1024px) {
          &:first-child,
          &:nth-child(4) {
            width: 185px;
            min-width: 185px;
          }
          &:nth-child(2) {
            width: 130px;
            min-width: 130px;
          }
          &:nth-child(3) {
            width: 80px;
            min-width: 80px;
          }
          &:nth-child(5) {
            width: 120px;
            min-width: 120px;
          }
          &:nth-child(6) {
            width: 70px;
            min-width: 70px;
          }
          &:nth-child(7) {
            width: 134px;
            min-width: 134px;
          }
        }
      }
      td {
        @media (min-width: 769px) and (max-width: 1024px) {
          padding: 12px;
          .personalCode {
            flex-direction: column;
            gap: 8px;
            align-items: start;
            .personalCode-info {
              width: 100% !important;
              justify-content: start;
              // gap: 8px;
              margin-right: 5px;
              margin-bottom: 3px;
            }

            .personalCode-btn {
              width: 100%;
              text-align: start;
            }
            .flex {
              min-width: 98px;
            }
          }
        }
      }
    }
  }
}
@media (max-width: $viewport_desktop) {
  .personalCode {
    flex-direction: unset;
    gap: unset;
    align-items: unset;
    width: 130px;
  }
}
</style>
