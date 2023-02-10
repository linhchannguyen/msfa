<template>
  <div v-loading="isLoadingUsserMangement" class="group-tblUser">
    <div ref="tblUser" class="tblUser tblUserTab1 table-hg100 scrollbar" @scroll="onScroll">
      <div v-if="listUsserMangement.length > 0" class="application-btn">
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
            <th>ユーザコード</th>
            <th>現行</th>
            <th>先行予約</th>
          </tr>
        </thead>
        <tbody v-if="listUsserMangement.length > 0">
          <tr v-for="item of listUsserMangement" :id="`className--${item.user_cd}`" :ref="`row-${item.user_cd}`" :key="item.user_cd" style="cursor: default">
            <td>{{ item?.user_cd }}</td>
            <td v-if="item?.user_name">
              <div :id="`className--${item.user_cd}`" class="tblUser-dateTime tblUser--boxBg">
                {{ formatFullDate(item?.start_date) }} ～
                {{
                  item?.end_date === '9999/12/31' || item?.end_date === '9999-12-31' ? '' : formatFullDate(item?.end_date) ? formatFullDate(item?.end_date) : ''
                }}
              </div>
              <div class="tblUser-user">
                <ul class="tblUser-userLst">
                  <li>
                    <span class="tblUser-user-item">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user03.svg')" alt="" />
                    </span>
                    <span class="tblUser-user-text">{{ item?.user_name }}</span>
                  </li>
                  <li style="width: 50%">
                    <span class="tblUser-user-item">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_mail01.svg')" alt="" />
                    </span>
                    <span class="tblUser-user-text">{{ item?.mail_address }}</span>
                  </li>
                  <li v-if="item?.account_lock_remarks" class="w-100">
                    <span class="tblUser-user-item">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_lock01.svg')" alt="" />
                    </span>
                    <span class="tblUser-user-text">{{ item?.account_lock_remarks }}</span>
                  </li>
                </ul>
              </div>
            </td>
            <td v-else :class="item?.user_name ? '' : 'noDataI01S01'">
              <div style="bottom: 0; position: absolute; top: 39%; padding: 0 0 0 14px; font-weight: 700">未設定</div>
            </td>

            <td
              v-if="!item.advance_reservation[0]?.user_cd"
              class="vertical-middle custom"
              @click="openModalUpdateAUser('isNewCreate', item, 0, item)"
              @touchstart="openModalUpdateAUser('isNewCreate', item, 0, item)"
            >
              <div class="addition-label custom2">
                <span class="msfa-custom-no-btn-create">
                  <span><i class="el-icon-plus"></i> <span>追加</span> </span>
                </span>
              </div>
            </td>
            <td v-if="item.advance_reservation[0]?.user_cd" style="max-width: 100px">
              <el-tabs v-model="item.isActive" type="card" class="head-tabs--I01">
                <el-tab-pane v-for="itemAdvance of item.advance_reservation" :key="itemAdvance.user_cd" :label="itemAdvance.customDate">
                  <div class="tblUser-user tblUser-user2">
                    <ul class="tblUser-userLst">
                      <li>
                        <span class="tblUser-user-item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user03.svg')" alt="" />
                        </span>
                        <span class="tblUser-user-text tblUser-user-text2">{{ itemAdvance?.user_name }}</span>
                      </li>
                      <li>
                        <span class="tblUser-user-item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_mail01.svg')" alt="" />
                        </span>
                        <span class="tblUser-user-text tblUser-user-text2">{{ itemAdvance?.mail_address }}</span>
                      </li>
                      <li v-if="itemAdvance?.account_lock_remarks">
                        <span class="tblUser-user-item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_lock01.svg')" />
                        </span>
                        <span class="tblUser-user-text tblUser-user-text2">{{ itemAdvance?.account_lock_remarks }}</span>
                      </li>
                    </ul>
                    <div class="tblUserStatus-btn">
                      <button class="btn btn--icon" data-toggle="dropdown"><span></span><span></span><span></span></button>
                      <div class="dropdown-menu dropdown-tools">
                        <span class="btn-show">
                          <span></span>
                          <span></span>
                          <span></span>
                        </span>
                        <ul>
                          <li @click="openModalUpdateAUser('isEdit', itemAdvance, 1, item)" @touchstart="openModalUpdateAUser('isEdit', itemAdvance, 1, item)">
                            <span class="item">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
                            </span>
                            <span class="text-label">編集</span>
                          </li>
                          <li
                            @click="clickDelete(itemAdvance?.user_cd, itemAdvance?.start_date, itemAdvance?.end_date, itemAdvance?.user_name)"
                            @touchstart="clickDelete(itemAdvance?.user_cd, itemAdvance?.start_date, itemAdvance?.end_date, itemAdvance?.user_name)"
                          >
                            <span class="item">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" />
                            </span>
                            <span class="text-label">削除</span>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </el-tab-pane>
                <el-tab-pane disabled class="el-tab-custom">
                  <template #label>
                    <span class="create-icon">
                      <div><i style="font-weight: bold" class="el-icon-plus"></i></div>
                      <span
                        style="cursor: pointer"
                        @click="openModalUpdateAUser('createIsRecord', item.advance_reservation[item.advance_reservation.length - 1], 0, item)"
                        @touchstart="openModalUpdateAUser('createIsRecord', item.advance_reservation[item.advance_reservation.length - 1], 0, item)"
                      ></span>
                    </span>
                  </template>
                </el-tab-pane>
              </el-tabs>
            </td>
          </tr>
        </tbody>
        <tr v-if="listUsserMangement.length <= 0 && !isLoadDefault">
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
    <div v-if="listUsserMangement.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ pageSizelistUsserMangement }} 件</div>
      <PaginationTable
        :page-size="100"
        layout="prev, pager, next"
        :total="pageSizelistUsserMangement"
        :current-page="finishData ? finishData : currentPage"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="`handle-close ${modalConfig.customClass}`"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="modalConfig.isShowClose"
    :before-close="handleBeforeClose"
    @close="closeModal"
  >
    <template #default>
      <component
        :is="modalConfig.component"
        ref="modalChild"
        v-bind="{ ...modalConfig.props }"
        @onFinishScreen="onFinishScreenModalAUser"
        @handleConfirm="deleteMessage"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import I01S01ModalUserEdit from './I01_S01_ModalUserEdit.vue';
import PaginationTable from '@/components/PaginationTable';
import { markRaw } from 'vue';
import moment from 'moment';
export default {
  name: 'I01_S01_TabAUser',
  components: {
    I01S01ModalUserEdit,
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
      curentDate: '',
      isLoadingUsserMangement: false,
      isLoadDefault: false,
      listUsserMangement: [],
      pageSizelistUsserMangement: 0,
      idUser: '',
      currentPage: 1,
      total_pages: 0,
      dataDelete: {},
      nextPage: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      isScroll: false,
      finishData: '',
      changeNodata: false,
      firstChange: false,
      isOnFilter: false
    };
  },
  watch: {
    objtab: function () {
      this.getListUserManagement();
    },
    changetab: function (value) {
      if (value === 1) {
        this.isOnFilter = this.isfilter;
        this.currentPage = 1;
      }
    }
  },

  mounted() {
    this.isOnFilter = this.isfilter;
    this.curentDate = this.formatFullDate(new Date());
    this.firstChange = false;
    this.listUsserMangement = JSON.parse(this.decodeData(localStorage.getItem('dataStoreUserManaTab'))) || [];
    this.getListUserManagement();
    this.emitter.on('change-AllTabs', ({ changeTabs }) => {
      if (changeTabs) {
        this.getListUserManagement();
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
      let content = document.querySelector('.tblUserTab1');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblUserTab1');
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
        let content = document.querySelector('.tblUserTab1');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    getListUserManagement() {
      this.isLoadingUsserMangement = true;
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
      I01_S01_UserManagementServices.getListUsserMangement(data)
        .then((res) => {
          // if (!this.nextPage) {
          //   this.currentPage = 1;
          //   this.total_pages = 0;
          // }

          this.pageSizelistUsserMangement = res.data.data.pagination.total_items;
          this.listUsserMangement = res.data.data.records;
          this.listUsserMangement.forEach((element) => {
            element.isActive = '0';
            element.advance_reservation.forEach((element2) => {
              if (element2.end_date === '9999/12/31') {
                element2.end_date = '';
              }
              element2.customDate =
                this.formatFullDate(element2.start_date) +
                ' ' +
                this.JapaneseTilde() +
                ' ' +
                (this.formatFullDate(element2.end_date) ? this.formatFullDate(element2.end_date) : '');
            });
          });

          localStorage.setItem('dataStoreUserManaTab', this.enCodeData(JSON.stringify(this.listUsserMangement)));
          this.nextPage = false;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
          if (err.response.data.message === '指定された画面に対するアクセス権がありません。') {
            this.$router.push('/home');
          }
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 500));
          if (this.$refs.tblUser && this.isOnFilter) {
            this.$refs.tblUser.scrollTop = 1;
          }
          this.isScroll = false;
          this.js_pscroll();
          this.isOnFilter = false;

          this.isLoadingUsserMangement = false;
          this.changeNodata = false;
          this.firstChange = true;
          this.isLoadDefault = false;
        });
    },
    openModalUpdateAUser(type, record, numberModal, bigItem) {
      const isCurentDateinArray = this.formatFullDate(bigItem.start_date) === this.curentDate ?? true;
      this.changeTrueClassHeader();
      this.idUser = record.user_cd;
      this.getClassChangeBRG(record.user_cd, false, false);
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ編集',
        isShowModal: true,
        component: markRaw(I01S01ModalUserEdit),
        props: { typeModal: type, record: record, numberModal: numberModal, isCurrentDateinArr: isCurentDateinArray },
        width: '40rem',
        customClass: 'custom-dialog modal-fixed',
        destroyOnClose: true,
        isShowClose: true
      };
    },
    onFinishScreenModalAUser(e) {
      if (e === 1) {
        this.getClassChangeBRG(this.idUser, true, true);
        this.idUser = '';
        this.isScroll = true;
        this.finishData = this.currentPage;
        setTimeout(() => {
          this.changeNodata = true;
          this.getListUserManagement();
        }, 50);
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      } else {
        this.getClassChangeBRG(this.idUser, true, false);
        this.idUser = '';
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      }
    },
    clickDelete(user_cd, start_date, end_date, userName) {
      this.idUser = user_cd;
      this.changeTrueClassHeader();
      this.dataDelete = {
        user_cd: user_cd,
        start_date: moment(new Date(start_date)).format('YYYY-M-D'),
        end_date: end_date ? moment(new Date(end_date)).format('YYYY-M-D') : '9999/12/31'
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        isShowClose: false,
        props: { title: `選択した${userName}を完全に削除しますか？` }
      };
    },
    deleteMessage() {
      I01_S01_UserManagementServices.deleteUsser(this.dataDelete)
        .then((res) => {
          if (res) {
            this.getClassChangeBRG(this.dataDelete.user_cd, true, true);
            this.isScroll = true;
            this.finishData = this.currentPage;
            this.$notify({ message: '正常に削除しました。', customClass: 'success', duration: 1500 });
            this.changeNodata = true;
            this.getListUserManagement();
            this.dataDelete = {};
            this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        });
    },
    closeModal() {
      this.getClassChangeBRG(this.idUser, true, false);
      this.changeFalseClassHeader();
    },
    handleCurrentChange(val) {
      this.isOnFilter = true;
      this.finishData = '';
      this.nextPage = true;
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.changeNodata = true;
        this.getListUserManagement();
      } else {
        this.currentPage = val;
        this.changeNodata = true;
        this.getListUserManagement();
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
.tblUser-userLst {
  width: 100%;
  li {
    width: 50%;
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
.noDataI01S01 {
  position: relative;
  div {
    padding: 0px 0px 0px 12px;
    font-weight: 700;
  }
}
.tblUser-user2 {
  max-width: unset !important;
}
.tblUser-user {
  position: relative;
  max-width: 500px;
  padding: 16px 0px 16px 7px !important;
  .tblUserStatus-btn {
    position: absolute;

    right: 12px;
    top: 3px;
  }
}
.tblUser-user-text {
  width: 90%;
}
.tblUser-user-text2 {
  width: calc(100% - 65px);
}
</style>
