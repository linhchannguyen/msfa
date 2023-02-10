<template>
  <!-- ターゲット施設管理 -->
  <div v-loading="isLoading" v-load-f5="true" class="wrapContent TargetFacilityManagement">
    <div class="groupContent">
      <div class="target-facility-form">
        <label>施設担当者</label>
        <div class="target-facility-select">
          <div class="form-icon icon-right">
            <span class="icon" @click="openModal_Z05_S01">
              <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
            </span>
            <div v-if="initParams.user_name" class="form-control d-flex align-items-center">
              <div class="block-tags">
                <el-tag class="m-0 el-tag-custom">
                  {{ initParams.user_name }}
                </el-tag>
              </div>
            </div>
            <el-input v-else clearable readonly placeholder="内容入力" class="form-control-input" />
          </div>
        </div>
      </div>

      <div class="application">
        <div class="application-btn">
          <button v-if="showScrollLeft && targetFacilityList.length > 0" type="button" class="btn btn--prev" @click="onScrollLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
          <button v-if="showScrollRight && targetFacilityList.length > 0" type="button" class="btn btn--next" @click="onScrollRight">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line-table.svg')" alt="icon" />
          </button>
        </div>
        <div ref="targetFacilityRef" :class="`targetFacility-tbl table-hg100 scrollbar `" @scroll="onScroll">
          <table>
            <thead>
              <tr>
                <th class="borderTable" rowspan="8">施設名</th>
                <th style="text-align: center" :colspan="targetFacilityHeaders.length || 8">ターゲット情報</th>
              </tr>
              <tr>
                <th v-for="(item, index) in targetFacilityHeaders" :key="index" class="borderRight">
                  <div class="flex">
                    <el-tooltip v-if="item.target_product_name?.length > 9" effect="dark" :content="item.target_product_name" placement="top-start">
                      <span class="box-th">{{ item.target_product_name }}</span>
                    </el-tooltip>
                    <span v-else>{{ item.target_product_name }}</span>
                    <span
                      :class="`icon-sort btn btn-toggle log-icon ${item.target_product_cd === initParams.order_value ? 'icon-sorted' : ''}`"
                      title_log="ソート"
                      @click="setConditionSort(item.target_product_cd)"
                    >
                      <img
                        v-if="item.target_product_cd === initParams.order_value"
                        :src="require('@/assets/template/images/icon_sorted.svg')"
                        alt="icon_sorted"
                      />
                      <img v-else :src="require('@/assets/template/images/icon_sort.svg')" alt="icon_sort" />
                    </span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in targetFacilityList" :key="index">
                <td :class="{ first: targetFacilityList.length }">
                  <div class="facilityName">
                    <p class="link log-icon" title_log="施設名" @click="callScreen('H01_S02', { facility_cd: user.facility_cd })">
                      {{ user.facility_short_name }}
                    </p>
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                      @click="callScreen('G01_S02', { facility_cd: user.facility_cd })"
                    >
                      <span class="btn-iconLeft">
                        <ImageSVG :src-image="'icon_openlink01.svg'" :alt-image="'icon_openlink01'" />
                      </span>
                      <span>選定</span>
                    </button>
                  </div>
                </td>
                <td v-for="(item, indexx) in targetFacilityHeaders" :key="indexx">
                  <span
                    v-if="user.target_product.some((el) => el.target_product_cd === item.target_product_cd)"
                    :class="`facilityCount ${
                      !user.target_product.find((el) => el.target_product_cd === item.target_product_cd)?.number_person_cd ? 'facilityCount2' : ''
                    }`"
                  >
                    <span v-if="user.target_product.find((el) => el.target_product_cd === item.target_product_cd)?.sub_target" class="facilityCount_name">
                      {{ user.target_product.find((el) => el.target_product_cd === item.target_product_cd)?.sub_target }}
                    </span>
                    <span class="icon">
                      <ImageSVG :src-image="'icon_humen.svg'" :alt-image="'icon_humen'" />
                      <span class="count">{{
                        ' x ' + user.target_product.find((el) => el.target_product_cd === item.target_product_cd)?.number_person_cd
                      }}</span>
                    </span>
                  </span>
                  <span v-else>&nbsp;</span>
                </td>
              </tr>
            </tbody>
          </table>
          <EmptyData v-if="targetFacilityList.length === 0 && !isLoadDefault" />
        </div>
      </div>
    </div>
    <div v-if="targetFacilityList.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ pagination.total_items }} 件</div>
      <PaginationTable :current-page="pagination.current_page" :total="pagination.total_items" :page-size="100" @current-change="handleChangePage" />
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog modal-fixed"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closeModal"></component>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import G01_S01_Service from '@/api/G01/G01_S01_TargetFacilityManagementServices';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import _ from 'lodash';

export default {
  name: 'G01_S01_TargetFacilityManagement',
  props: {
    userCd: {
      type: Number || String,
      default: null,
      require: true
    },
    orgCd: {
      type: Number || String,
      default: null,
      require: true
    },
    userName: {
      type: String,
      default: '',
      require: true
    },
    checkLoading: [Boolean]
  },
  data() {
    return {
      isLoadDefault: true,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      pagination: {
        limit: 100,
        total_items: 1,
        total_pages: 100,
        previous_page: 1,
        next_page: 1,
        current_page: 1,
        first_page: 1,
        last_page: 1
      },
      initParams: {
        user_name: this.userName,
        user_cd: this.userCd,
        paramsOrgCd: this.orgCd,
        paramsUserCd: this.userCd,
        order_value: '',
        limit: 100,
        offset: 0
      },
      isLoading: true,
      conditionSort: '',
      targetFacilityList: [],
      targetFacilityHeaders: [],

      showScrollLeft: false,
      showScrollRight: false,
      onScrollTop: 0
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'ターゲット施設管理',
      icon: 'icon_target-facility.svg',
      isShowBack: false
    });

    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.G01_S01_TargetFacilityManagement || 0;
    this.pagination.current_page = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.G01_S01_TargetFacilityManagement || 1;
    this.initParams.offset = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.G01_S01_TargetFacilityManagement - 1 || 0;

    if (!this.userCd) {
      const isLoadingComponent = localStorage.getItem('isLoadingComponent');
      if (isLoadingComponent === 'true') {
        const userLogin = this.getCurrentUser();
        this.initParams = {
          ...this.initParams,
          user_cd: userLogin.user_cd,
          user_name: userLogin.user_name,
          paramsOrgCd: userLogin.org_cd,
          paramsUserCd: {
            org_cd: userLogin.org_cd,
            user_cd: userLogin.user_cd
          }
        };
      } else {
        const paramsG1S1 = JSON.parse(localStorage.getItem('paramsG1S1'));
        this.initParams = {
          ...this.initParams,
          ...paramsG1S1
        };
      }
    }
    this.getScreen();
    this.js_pscroll();
  },
  methods: {
    // define func
    handleChangePage(page) {
      this.onScrollTop = 0;
      this.pagination.current_page = page;
      this.initParams.offset = page === 0 ? page : page - 1;
      this.targetFacilityManagement(this.initParams, true);
    },
    callScreen(screenID, props = {}) {
      this.setScrollTop();
      let objScreen = {
        G01_S02: 'G01_S02_In_FacilityTargetPersonalManagement'
      };
      if (screenID === 'H01_S02') {
        this.$router.push({
          name: 'H01_FacilityDetails',
          params: {
            facility_cd: props.facility_cd
          },
          query: {
            tab: '1'
          }
        });
        return;
      }
      this.$router.push({ name: objScreen[screenID], params: props });
    },
    setConditionSort(condition) {
      if (this.initParams.order_value === condition) {
        this.initParams.order_value = '';
      } else {
        this.initParams.order_value = condition;
      }
      this.initParams.offset = 0;
      this.targetFacilityManagement(this.initParams, true, false);
    },
    // call api
    getScreen() {
      G01_S01_Service.getScreenDataService()
        .then((res) => {
          const dataRes = res.data.data;
          this.targetFacilityHeaders = dataRes;
          this.targetFacilityManagement(this.initParams, false, true);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },
    targetFacilityManagement(params, isFilter, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.isLoading = true;
      G01_S01_Service.targetFacilityManagementService(params)
        .then((res) => {
          const dataRes = res.data.data;
          this.targetFacilityList = dataRes.records;
          this.pagination = dataRes.pagination;
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          this.isLoading = false;
          if (isLoadDefault) {
            this.$refs.targetFacilityRef.scrollTop = 1;
          }
          this.isLoadDefault = false;

          this.setCurrentPageScreen('G01_S01_TargetFacilityManagement', this.pagination.current_page);

          localStorage.setItem('paramsG1S1', JSON.stringify(this.initParams));

          if (this.$refs.targetFacilityRef) {
            if (this.onScrollTop && !isFilter) {
              this.$refs.targetFacilityRef.scrollTop = this.onScrollTop;
            } else {
              this.$refs.targetFacilityRef.scrollTop = 0;
            }
          }

          this.js_pscroll(0.5);

          await localStorage.setItem('isLoadingComponent', false);

          const childRouter = JSON.parse(localStorage.getItem('router'));
          let routes = this.decodeData(localStorage.getItem('$_D')) || [];

          if (routes && routes.length > 0) {
            const indexRouteTargetFacility = routes.findIndex((x) => x.name === 'G01_S01_TargetFacilityManagement');

            if (indexRouteTargetFacility > -1) {
              let routeTargetFacility = {
                ...routes[indexRouteTargetFacility],
                params: {}
              };

              routes[indexRouteTargetFacility] = routeTargetFacility;
              localStorage.setItem('$_D', this.enCodeData(routes));
            }
          } else {
            let routeTargetFacility = {
              name: 'G01_S01_TargetFacilityManagement',
              params: {},
              path: childRouter.find((x) => x.name === 'G01_S01_TargetFacilityManagement')?.path
            };

            routes[0] = routeTargetFacility;
            localStorage.setItem('$_D', this.enCodeData(routes));
          }
        });
    },

    onScrollLeft() {
      let content = document.querySelector('.targetFacility-tbl');
      content.scrollLeft -= 180;
    },

    onScrollRight() {
      let content = document.querySelector('.targetFacility-tbl');
      content.scrollLeft += 180;
    },

    onScroll: _.debounce(function ({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }
    }, 300),
    setScrollTop() {
      let scrollTop = this.$refs.targetFacilityRef ? this.$refs.targetFacilityRef.scrollTop : 0;
      this.setScrollScreen('G01_S01_TargetFacilityManagement', scrollTop);
    },

    // modal
    closeModal(data) {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      if (data && data.userSelected.length > 0) {
        this.initParams = {
          ...this.initParams,
          user_name: data.userSelected[0].user_name,
          user_cd: data.userSelected[0].user_cd
        };

        this.initParams.paramsOrgCd = data.userSelected[0].org_cd;
        this.initParams.paramsUserCd = data.userSelected[0];
        this.targetFacilityManagement(this.initParams, true, false);
      }
    },
    openModal_Z05_S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: this.markRaw(Z05_S01_ModalOrganization),
        width: '45rem',
        props: {
          mode: 'single',
          userFlag: 1,
          userSelectFlag: 1,
          orgCdList: [this.initParams.paramsOrgCd],
          userCdList: [this.initParams.paramsUserCd]
        }
      };
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.TargetFacilityManagement {
  .target-facility-form {
    display: flex;
    padding: 24px 24px 17px;
    label {
      color: #5f6b73;
      line-height: 40px;
      font-size: 16px;
      margin-right: 20px;
      font-weight: bold;
    }
    .target-facility-select {
      width: 250px;
      .icon {
        cursor: pointer;
      }
    }
  }
  .groupContent {
    height: 100%;
    position: relative;
    .application {
      position: relative;
      padding: 0 24px 8px;
      max-height: 100%;
      overflow: hidden;
    }
    .application-btn {
      .btn {
        position: absolute;
        top: calc(50% + 10px);
        height: 46px;
        width: 52px;
        padding: 0;
        background: rgba(63, 75, 86, 0.4);
        z-index: 1;
        &.btn--prev {
          left: 499px;
          border-radius: 0px 60px 60px 0px;
          @media (max-width: $viewport_desktop) {
            left: 370px;
          }
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
    .targetFacility-tbl-emp tbody tr {
      &:hover {
        td {
          &::after {
            border-width: 0 !important;
          }
        }
      }
      td {
        &:nth-child(1) {
          z-index: 0 !important;
        }
      }
    }
    .targetFacility-tbl {
      border-radius: 8px;
      padding: 0 0 6px;
      overflow: hidden;

      .icon-sort {
        cursor: pointer;
        margin-left: 5px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #ffffff;
        display: inline-block;
        line-height: 20px;
        position: relative;
        img {
          position: absolute;
          top: 5px;
          left: 5px;
        }
      }
      .icon-sorted {
        background: #448add;
      }
      tr {
        th,
        td {
          text-align: center;
          vertical-align: middle;
          min-width: 74px;
        }
      }
      thead {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 2;
        tr {
          &:first-of-type {
            th {
              border-bottom: 1px solid #f9f4f4;
              font-size: 1rem;
              font-weight: 700;
              text-align: left;
              &:nth-child(1) {
                min-width: 450px;
                max-width: 450px;
                padding-left: 32px;
                border: none;
              }
            }
          }
          th {
            color: #fff;
            padding-top: 7px;
            padding-bottom: 7px;
            border-bottom: none;
            vertical-align: middle;
            font-size: 0.875rem;
            white-space: nowrap;
            font-weight: normal;
          }
          th {
            position: relative;
            &.borderTable {
              background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
              background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
              background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
              position: -webkit-sticky;
              position: sticky;
              top: 0;
              left: 0;
              z-index: 3;

              &::after {
                position: absolute;
                content: '';
                width: 1px;
                background-color: #fff;
                height: calc(100% - 10px);
                top: 5px;
                right: 0;
              }
            }
            &.borderRight {
              min-width: 120px;
              max-width: 160px;
              .box-th {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                width: 80%;
                display: inline-block;
              }
              &::after {
                position: absolute;
                content: '';
                width: 1px;
                background-color: #fff;
                height: calc(100% - 10px);
                top: 5px;
                right: 0;
              }
              &:last-child::after {
                content: unset;
              }
            }
          }
        }
      }
      tbody {
        tr {
          &:hover {
            td {
              &::after {
                border-width: 2px 0 2px 0;
              }
            }
            td:nth-child(1) {
              &:after {
                border-left-width: 2px;
              }
            }
            td:last-child {
              &:after {
                border-right-width: 2px;
              }
            }
          }
          td {
            position: relative;
            z-index: 0;
            &:after {
              z-index: -1;
              position: absolute;
              width: 100%;
              height: 100%;
              border-style: solid;
              border-width: 0;
              border-color: #c3c0c0;
              content: '';
              top: 0;
              left: 0;
            }
            &:nth-child(1) {
              position: sticky;
              z-index: 1;
              left: 0;
              background-color: #fff;
              &::before {
                position: absolute;
                content: '';
                width: 1px;
                background-color: #d5d5d5;
                height: 100%;
                top: 0px;
                right: -1px;
              }
            }
          }
        }
        .facilityName {
          display: flex;
          justify-content: space-between;
          align-items: center;
          min-width: 450px;
          max-width: 450px;
          p {
            width: 80%;
            text-align: left;
            overflow-wrap: anywhere;
            word-break: break-word;
          }
          button {
            margin-left: 10px;
            width: 80px;
          }
        }
        .facilityCount {
          display: flex;
          justify-content: space-around;
          align-items: center;
          .facilityCount_name {
            background: #daf8dc;
            color: #28a470;
            font-weight: 500;
            padding: 3px 15px;
            border-radius: 12px;
          }
          .icon {
            font-size: 16px;
            .count {
              font-size: 18px;
            }
          }
        }
        .facilityCount2 {
          justify-content: center;
        }
      }

      @media (max-width: $viewport_desktop) {
        .borderTable,
        .first {
          max-width: 350px !important;
          min-width: 350px !important;
        }
        .facilityName {
          max-width: 324px !important;
          min-width: 324px !important;

          button {
            width: 90px !important;
          }
        }
      }

      @media (min-width: $viewport_tablet) and (max-width: $viewport_desktop) {
        td,
        th {
          max-width: calc((100vw - 60px - 48px - 350px) / 5) !important;
          min-width: calc((100vw - 60px - 48px - 350px) / 5) !important;
          width: calc((100vw - 60px - 48px - 350px) / 5) !important;
          padding: 6px !important;
        }
        th.borderRight {
          white-space: normal;

          .flex {
            display: flex;
            align-items: center;
            gap: 4px;

            .box-th {
              display: -webkit-box;
              -webkit-box-orient: vertical;
              -webkit-line-clamp: 2;
              white-space: normal;
            }

            .icon-sort {
              margin: 0 !important;
            }
          }
        }
      }
    }
  }
}

@media (hover: hover) {
  .icon-sort:hover {
    background: #448add !important;
    &:after {
      background: url(~@/assets/template/images/icon_sorted.svg) no-repeat center;
      position: absolute;
      content: '';
      width: 100%;
      height: 100%;
      top: 2px;
      left: 0;
    }
  }
}
@media (hover: none) {
  .icon-sort:active {
    background: #ffffff;
  }
}
</style>
