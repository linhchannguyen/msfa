+
<template>
  <div v-loading="isLodaingTabOrganization" class="group-tblUser">
    <div ref="tblUser2" class="tblUserTab2-custom-eltab tblUser tblUserTab2 tblUser--org table-hg100 scrollbar" @scroll="onScroll">
      <div v-if="listTabOrganization.length > 0" class="application-btn">
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
            <th>ユーザ</th>
            <th>現行</th>
            <th>先行予約</th>
          </tr>
        </thead>
        <tbody v-if="listTabOrganization.length > 0">
          <tr v-for="(item, index) of listTabOrganization" :id="`classNameT2--${item.user_cd}`" :key="item.user_cd" style="cursor: default">
            <td class="vertical-middle">
              <p class="tblUser-name">{{ item?.user_name }}</p>
              <p>ユーザコード：{{ item?.user_cd }}</p>
            </td>
            <td style="position: relative">
              <div v-for="item2 of item?.active" :key="item2?.id" class="tab2">
                <div class="tblUser-dateTime tblUser--boxBg">
                  {{ formatFullDate(item2?.start_date) ? formatFullDate(item2?.start_date) : '' }} ～
                  {{ item2?.end_date === '9999/12/31' ? '' : formatFullDate(item2?.end_date) ? formatFullDate(item2?.end_date) : '' }}
                </div>
                <div class="tblUser-user tblUser-user-tab2">
                  <ul v-for="item3 of item2?.organization" :key="item3?.org_cd" class="tblUserLst tblUserLst-custom">
                    <li v-if="item3?.org_label" class="tblUserLst-custom1">
                      <span class="tblUserLst-item">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" />
                      </span>
                      <p class="tblUserLst-text">
                        <span class="tblUserLst-bold">({{ item3?.main_flag === 1 || item3?.main_flag ? '主' : '副' }})</span> {{ item3?.org_label }}
                      </p>
                    </li>
                    <li v-if="item3?.definition_label" class="tblUserLst-custom2">
                      <span class="tblUserLst-item">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_namecard.svg')" alt="" />
                      </span>
                      <p class="tblUserLst-text">{{ item3?.definition_label }}</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div v-if="item?.active.length === 0" style="bottom: 0; position: absolute; top: 39%; padding: 0 0 0 14px; font-weight: 700">未設定</div>
            </td>
            <td
              v-if="!item.advance_reservation[0]?.start_date"
              class="vertical-middle custom"
              @click="openModalUpdateAUser('create', item, item?.user_cd, '', 0, item?.user_name, '', '', item)"
            >
              <div class="addition-label custom2">
                <span class="msfa-custom-no-btn-create">
                  <span><i style="margin-right: 5px" class="el-icon-plus"></i><span>追加</span></span>
                </span>
              </div>
            </td>
            <td v-if="item.advance_reservation[0]?.start_date" style="max-width: 100px">
              <el-tabs v-model="item.isActive" type="card">
                <el-tab-pane v-for="(item4, index4) of item?.advance_reservation" :key="item4?.id" :label="item4.customDate">
                  <div class="tblUser-user tblUser-user-custom">
                    <ul v-for="item5 of item4?.organization" :key="item5?.org_cd" class="tblUserLst tblUserLst-custom2 tblUserLst-custom22">
                      <li v-if="item5?.org_label" class="w-10">
                        <span class="tblUserLst-item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" />
                        </span>
                        <span style="font-weight: 700"> {{ item5?.main_flag === 1 || item5?.main_flag ? '(主)' : '(副)' }}</span> &nbsp;
                        <p class="tblUserLst-text">{{ item5?.org_label }}</p>
                      </li>
                      <li v-if="item5?.definition_label" class="w-50">
                        <span class="tblUserLst-item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_namecard.svg')" alt="" />
                        </span>
                        <p class="tblUserLst-text">{{ item5?.definition_label }}</p>
                      </li>
                    </ul>
                    <div class="tblUserStatus-btn">
                      <button
                        class="btn btn--icon log-icon"
                        type="button"
                        title_log="編集"
                        @click="
                          openModalUpdateAUser('edit', item4, item?.user_cd, item4.organization[0].org_cd, 1, item?.user_name, index4, index, item),
                            passStartDate(item)
                        "
                      >
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit01.svg')" alt="" />
                      </button>
                    </div>
                  </div>
                </el-tab-pane>
                <el-tab-pane disabled class="el-tab-custom">
                  <template #label>
                    <span class="create-icon">
                      <div><i style="font-weight: bold" class="el-icon-plus"></i></div>
                      <span style="cursor: pointer" @click="openModalUpdateAUser('create', item, item?.user_cd, '', 0, item?.user_name, '', '', item)"></span>
                    </span>
                  </template>
                </el-tab-pane>
              </el-tabs>
            </td>
          </tr>
        </tbody>
        <tr v-if="listTabOrganization.length === 0 && !changeNodata">
          <td colspan="16">
            <div class="noData">
              <div class="noData-content">
                <p class="noData-text">該当するデータがありません。</p>
                <div class="noData-thumb">
                  <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
    <div v-if="listTabOrganization.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ pageSizelistTabOrganization }} 件</div>
      <PaginationTable
        :page-size="100"
        layout="prev, pager, next"
        :total="pageSizelistTabOrganization"
        :current-page="currentPage"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog handle-close"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :before-close="handleBeforeClose"
    @close="closeModal"
  >
    <template #default>
      <component :is="modalConfig.component" ref="modalChild" v-bind="{ ...modalConfig.props }" @onFinishScreen="onFinishScreenModalAUser"></component>
    </template>
  </el-dialog>
</template>

<script>
import I01S01ModalUserAffiliationEdit from './I01_S01_ModalUserAffiliationEdit.vue';
import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import { markRaw } from 'vue';
import PaginationTable from '@/components/PaginationTable';
export default {
  name: 'I01_S01_TabOrganization',
  components: {
    I01S01ModalUserAffiliationEdit,
    PaginationTable
  },
  props: {
    data: {
      type: Number,
      default: 0
    },
    objtab: {
      type: Object,
      default() {}
    },
    changetab: {
      type: Number,
      default: 0
    },
    isfilter: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      // tabIndex: '',
      // tabIndex4: '',
      isLoadDefault: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      isLodaingTabOrganization: false,
      listTabOrganization: [],
      pageSizelistTabOrganization: 0,
      currentPage: 1,
      total_pages: 0,
      userID: '',
      nextPage: false,
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      isScroll: false,
      finishData: '',
      updateStartDate: '',
      curentDate: '',
      changeNodata: false,
      isOnFilter: false
    };
  },
  watch: {
    objtab: function () {
      this.getTabOrganization();
    },
    changetab: function (value) {
      if (value === 2) {
        this.isOnFilter = this.isfilter;
        this.currentPage = 1;
      }
    }
  },
  mounted() {
    this.isOnFilter = this.isfilter;
    this.curentDate = this.formatFullDate(new Date());
    this.listTabOrganization = JSON.parse(this.decodeData(localStorage.getItem('dataStoreOrgTab'))) || [];
    this.getTabOrganization();
    this.emitter.on('change-AllTabs', ({ changeTabs }) => {
      if (changeTabs) {
        this.getTabOrganization();
      }
    });
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    onScrollLeft() {
      let content = document.querySelector('.tblUserTab2');
      content.scrollLeft = content.scrollLeft - 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblUserTab2');
      content.scrollLeft = content.scrollLeft + 200;
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
        let content = document.querySelector('.tblUserTab2');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    getTabOrganization() {
      this.isLodaingTabOrganization = true;
      this.isLoadDefault = !this.isOnFilter;
      const data = {
        user_cd: this.objtab?.user_cd ? this.objtab?.user_cd : '',
        user_name: this.objtab?.user_name ? this.objtab?.user_name : '',
        org_cd: this.objtab?.org_cd ? this.objtab?.org_cd : '',
        layer_num: this.objtab?.layer_num ? this.objtab?.layer_num : '',
        org_local: this.objtab?.org_local ? this.objtab?.org_local : '',
        limit: 100,
        offset: this.finishData ? +this.finishData - 1 : this.nextPage ? this.currentPage - 1 : this.objtab?.offset
      };
      I01_S01_UserManagementServices.getOrganization(data)
        .then((res) => {
          // if (!this.nextPage) {
          //   this.currentPage = 1;
          //   this.total_pages = 0;
          // }
          this.pageSizelistTabOrganization = res.data.data.pagination.total_items;
          this.listTabOrganization = res.data.data.records;
          this.listTabOrganization.forEach((element) => {
            element.isActive = '0';
            element.advance_reservation.forEach((element2) => {
              if (element2.end_date === '9999/12/31') {
                element2.end_date = '';
              }
              element2.customDate =
                (this.formatFullDate(element2.start_date) ? this.formatFullDate(element2.start_date) : '') +
                ' ' +
                this.JapaneseTilde() +
                ' ' +
                (this.formatFullDate(element2.end_date) ? this.formatFullDate(element2.end_date) : '');
            });
          });
          localStorage.setItem('dataStoreOrgTab', this.enCodeData(JSON.stringify(this.listTabOrganization)));

          this.nextPage = false;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 500));
          if (this.$refs.tblUser2 && this.isOnFilter) {
            this.$refs.tblUser2.scrollTop = 1;
          }
          this.js_pscroll();
          this.isOnFilter = false;
          this.isScroll = false;

          this.isLodaingTabOrganization = false;
          this.changeNodata = false;
          this.isLoadDefault = false;
        });
    },
    closeModal() {
      this.getClassChangeBRGRecord(this.userID, true);
      this.userID = '';
      this.changeFalseClassHeader();
      // this.getTabOrganization();
    },
    getClassChangeBRGRecord(id, removeEl, isActive) {
      if (isActive) {
        const className = 'classNameT2--';
        const trBackGround = document.getElementById(`${className}${id}`);
        if (removeEl) {
          trBackGround?.classList.remove('back-ground-active');
          trBackGround?.classList.add('back-ground-leave');
          setTimeout(() => {
            trBackGround?.classList.remove('back-ground-leave');
          }, 2500);
        } else {
          trBackGround?.classList.add('back-ground-active');
        }
      }
    },
    passStartDate(item) {
      const data = item.active[0]?.start_date;
      this.updateStartDate = data;
    },
    openModalUpdateAUser(type, record, usercd, orgCdOld, numberModal, user_name, index4, index, bigItem) {
      console.log('record', record);
      setTimeout(() => {
        const isCurentDateinArray = this.formatFullDate(bigItem.active[0]?.start_date) === this.curentDate ?? true;
        this.userID = usercd;
        this.getClassChangeBRGRecord(usercd, false, false);
        this.tabIndex4 = index4;
        this.tabIndex = index;
        this.modalConfig = {
          ...this.modalConfig,
          title: 'ユーザ所属編集',
          isShowModal: true,
          component: markRaw(I01S01ModalUserAffiliationEdit),
          props: {
            typeModal: type,
            record: record,
            userCd: usercd,
            orgCdOld: orgCdOld,
            numberModal: numberModal,
            userName: user_name,
            updateStartDate: this.updateStartDate,
            isCurrentDateinArr: isCurentDateinArray
          },
          width: '53rem',
          destroyOnClose: true
        };
        this.changeTrueClassHeader();
      }, 100);
    },
    onFinishScreenModalAUser(e) {
      if (e === 1) {
        this.getClassChangeBRGRecord(this.userID, true, false);
        this.userID = '';
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      } else {
        this.getClassChangeBRGRecord(this.userID, true, true);
        this.userID = '';
        this.isScroll = true;
        this.finishData = this.currentPage;
        setTimeout(() => {
          this.changeNodata = true;
          this.getTabOrganization();
        }, 50);
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    },
    handleCurrentChange(val) {
      this.isOnFilter = true;
      this.finishData = '';
      this.nextPage = true;
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.changeNodata = true;
        this.getTabOrganization();
      } else {
        this.currentPage = val;
        this.changeNodata = true;
        this.getTabOrganization();
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.tblUser {
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
.tblUser-user-tab2 {
  margin-top: 15px !important;
  display: block !important;
}

.tblUserLst-custom {
  margin-bottom: 15px !important;
  width: 100%;
  .tblUserLst-custom1 {
    width: 70% !important;
  }
  .tblUserLst-custom2 {
    width: 30% !important;
  }
}
.tblUserLst-custom22 {
  margin-bottom: 15px !important;
}
.tblUser-user-custom {
  padding: 8px 25px 8px 24px !important;
  display: unset !important;
  ul {
    margin-bottom: 8px;
    padding-left: 22px;
    li {
      width: 50% !important;
    }
  }
  .tblUserStatus-btn {
    position: absolute !important;
    top: 0px !important;
    right: 20px !important;
  }
}
.custom {
  &:hover {
    cursor: pointer;
    background: #eef6ff;
    box-shadow: inset 0px 1.3px 0px 0px #c3c0c0, inset 0px -1.3px 0px 0px #c3c0c0, inset -2px 0px 0px 0px #c3c0c0;
    transition: box-shadow 0.55s, background 0.55s, transform 0.55s ease;
  }
}
.custom2 {
  &:hover {
    cursor: pointer;
  }
}
.create-icon {
  position: relative;
  span {
    position: absolute;
    width: 54px;
    height: 40px;
    top: 0;
    right: 0;
    left: -20px;
  }
}
</style>
