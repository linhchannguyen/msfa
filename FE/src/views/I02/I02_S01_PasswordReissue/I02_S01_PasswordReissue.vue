<template>
  <div v-loading="loading" v-load-f5="true" class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn filter-wrapper">
            <button class="btn btn-filter" type="button" @click="showPopup()" @touchstart="showPopup()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div :class="['dropdown-menu dropdown-filter dropdown-UserManage', isShowPopupFilter ? 'show' : '']">
              <div class="item-filter btn-close-filter" @click="closePopup()" @touchstart="closePopup()">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="passwordChange-checkbox">
                <label class="custom-control custom-checkbox custom-control--bordGreen">
                  <input v-model="statePassword" class="custom-control-input" type="checkbox" />
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">パスワード変更要求中</span>
                </label>
              </div>
              <div class="formUserManage">
                <ul>
                  <li class="w-100">
                    <label class="formUserManage-label">ユーザ名</label>
                    <div class="formUserManage-control">
                      <el-input v-model="filter.user_name" clearable placeholder="名前入力" class="form-control-input" />
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="formUserManage-label">ユーザコード</label>
                    <div class="formUserManage-control">
                      <el-input v-model="filter.user_cd" clearable placeholder="コード入力" class="form-control-input" />
                    </div>
                  </li>
                  <li class="w-100">
                    <label class="formUserManage-label">組織</label>
                    <div class="formUserManage-control">
                      <div class="form-icon icon-right">
                        <span
                          title_log="組織"
                          data-toggle="modal"
                          data-target="#modalOrganization_Z05_S01"
                          class="icon v v v v v v v vv v log-icon"
                          data-backdrop="static"
                          @click="openModalZ05S01()"
                          @touchstart="openModalZ05S01()"
                          ><img
                            class="svg v v v v v v v vv v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v v"
                            src="@/assets/template/images/icon_popup.svg"
                            alt=""
                        /></span>
                        <div v-if="orgArr.length > 0" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag v-for="item in orgArr" :key="item" class="el-tag-custom" placeholder="未選択" closable @close="handleRemoveOrgItem(item)">
                              {{ item.org_label }}
                            </el-tag>
                          </div>
                          <!-- <i v-if="orgArr.length > 0" class="iconClear el-icon-circle-close" @click="handleRemoveOrg()"></i> -->
                        </div>
                        <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="btnFilter-UserManage">
                <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closePopup()">キャンセル</button>
                <button type="button" class="btn btn-primary btn-filter-submit" @click="search(false)">検索</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div ref="tblPassword" class="passReissue-tbl table-hg100 scrollbar">
        <table class="tableBox tableCustom">
          <thead>
            <tr>
              <th>ユーザ</th>
              <th>所属組織</th>
              <th>パスワード変更要求</th>
              <th>再発行</th>
            </tr>
          </thead>
          <tbody>
            <template v-if="items.length > 0">
              <tr v-for="(item, index) in items" :id="`td_userCd--${item.user_cd}`" :key="item.user_cd + index">
                <td>[ {{ item.user_cd }} ] {{ item.user_name }}（{{ item.definition_label }}）</td>
                <td>{{ item.org_label }}</td>
                <td>{{ item.require_change_flag == 0 ? '' : '要求中' }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-outline-primary btn-radius btn-hg32 btn-outline-primary--cancel"
                    @click="confirmChangePassword(item)"
                    @touchstart="confirmChangePassword(item)"
                  >
                    <span class="btn-iconLeft">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_key01.svg')" alt="" />
                    </span>
                    パスワード再発行
                  </button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
        <!-- <EmptyData v-if="items.length === 0" custom-class="customClassEmpty" /> -->

        <div v-if="items.length === 0" class="no-data">
          <div class="no-data-content">
            <p v-show="!isLoadDefault" class="no-data-text">該当するデータがありません。</p>
            <div v-if="!isLoadDefault" class="no-data-thumb">
              <img class="svg" src="@/assets/template/images/data/amico.svg" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="items.length > 0" class="pagination" style="display: inline-flex">
    <div class="pagination-cases">
      <PaginationTable
        layout="prev, pager, next"
        :current-page="pagination.current_page"
        :total-page="pagination.total_pages"
        @current-change="handleChangePage"
      />
      <span class="total-items"> 全 {{ pagination.total_items }} 件</span>
    </div>
  </div>

  <!-- Modal Z05-S01 -->
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
      <component
        :is="modalConfig.component"
        v-bind="{ ...modalConfig.props }"
        @onFinishScreen="onResultModalZ05S01"
        @handleConfirm="onCloseModal"
        @handleYes="resetInfor"
      ></component>
    </template>
  </el-dialog>
</template>
<script>
import I02_S01_PasswordReissueServices from '@/api/I02/I02_S01_PasswordReissueServices';
import PaginationTable from '@/components/Pagination';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import EmptyData from '@/components/EmptyData';
import { markRaw } from 'vue';
import { encodeData } from '@/api/base64_api';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'I02_S01_PasswordReissue',
  components: {
    PaginationTable,
    Z05S01Organization,
    EmptyData
  },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      isLoadDefault: true,
      paramsZ05S01: {
        userFlag: 0,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: [],
        userRawList: []
      },

      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      },
      loading: false,
      statePassword: null,
      isShowPopupFilter: false,
      showModalZ05S01: false,
      items: [],
      orgArr: '',
      filter: { limit: 100, offset: 0, user_cd: '', org_cd: '', user_name: '', password_change: '' },
      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      }
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'パスワード再発行',
      icon: 'icon-i01.svg',
      isShowBack: false
    });
    this.getListPasswordReissue(this.filter, true);
  },
  methods: {
    getByClassTrTag(userCd, removeEl) {
      const className = 'td_userCd--';
      const trBackGround = document.getElementById(`${className}${userCd}`);
      if (removeEl) {
        trBackGround?.classList.remove('back-ground-active');
        trBackGround?.classList.add('back-ground-leave');
        setTimeout(() => {
          trBackGround?.classList.remove('back-ground-leave');
        }, 2500);
      } else {
        trBackGround?.classList.add('back-ground-active');
      }
    },
    // Modal Z05-S03
    // check form
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.paramsZ05S01 },
        width: '45rem',
        customClass: 'custom-dialog modal-fixed',
        destroyOnClose: true,
        isShowClose: true
      };
    },
    /** Modal model edmit data for H02_S05 */
    onResultModalZ05S01(e) {
      if (e && e?.orgSelected && e?.orgSelected.length > 0) {
        this.paramsZ05S01.userCdList = [];
        const resultOrg = e.orgSelected.map((s) => s.org_cd);
        this.paramsZ05S01.orgCdList = resultOrg;
        this.orgArr = e.orgSelected;
        this.filter.org_cd = resultOrg;
        this.onCloseModal();
      } else {
        if (e && e.data && e?.data?.id) {
          this.getByClassTrTag(e?.data?.id, true);
        }
        this.onCloseModal();
      }
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    confirmChangePassword(item) {
      this.getByClassTrTag(item.user_cd, false);
      this.modalConfirm(
        () => {
          const body = {
            user_cd: item.user_cd,
            require_change_flag: item.require_change_flag
          };
          this.postPasswordReissue(body);
        },
        { id: item.user_cd }
      );
    },
    onResultModal(e) {
      const orgCdList = [...e.orgSelected];
      const resultOrg = orgCdList.map((s) => s.org_cd);
      this.orgArr = orgCdList;
      this.filter.org_cd = resultOrg;
    },
    handleRemoveOrg() {
      this.filter.org_cd = '';
      this.orgArr = '';
    },
    handleRemoveOrgItem(item) {
      this.filter = {
        ...this.filter,
        org_cd: this.filter.org_cd.filter((x) => x !== item.org_cd)
      };
      this.orgArr = this.orgArr.filter((x) => x.org_cd !== item.org_cd);
    },
    closeModalConfirm() {
      this.modalConfig.isShowModal = false;
    },
    search(isLoadDefault) {
      this.filter.password_change = this.statePassword ? 1 : 0;
      this.filter = {
        ...this.filter,
        offset: 0
      };
      this.pagination.current_page = this.filter.offset;
      let org_cd = this.filter.org_cd.length > 0 ? this.filter.org_cd.join() : '';

      this.getListPasswordReissue({ ...this.filter, org_cd }, isLoadDefault);

      this.closePopup();
    },
    showPopup() {
      this.isShowPopupFilter = true;
    },
    closePopup() {
      this.isShowPopupFilter = false;
    },

    handleChangePage(page) {
      this.pagination.current_page = page;
      this.filter.offset = page - 1;
      if (this.$refs.tblPassword) {
        this.$refs.tblPassword.scrollTop = 0;
      }
      this.getListPasswordReissue(this.filter, false);
    },

    // API
    getListPasswordReissue(params, isLoadDefault) {
      this.loading = true;
      this.isLoadDefault = isLoadDefault;
      I02_S01_PasswordReissueServices.getListPassowrdReissueService(params)
        .then((res) => {
          if (res) {
            this.items = res.data.data.records;
            this.pagination = { ...res.data.data.pagination, total_pages: res.data.data.pagination.total_pages * 10 };
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          if (err.response.data.message === '指定された画面に対するアクセス権がありません。') {
            this.$router.push('/home');
          }
        })
        .finally(() => {
          this.loading = false;
          this.isLoadDefault = false;
          if (this.$refs.tblPassword) {
            this.$refs.tblPassword.scrollTop = 0;
          }
        });
    },
    postPasswordReissue(item) {
      const formData = new FormData();
      formData.append('require_change_flag', encodeData(item.require_change_flag));
      formData.append('user_cd', encodeData(item.user_cd));
      I02_S01_PasswordReissueServices.sendPasswordReissueService(formData)
        .then((res) => {
          if (res.data.status === '200') {
            this.$notify({ message: res.data.message, customClass: 'success', duration: 2000 });
            const user = res.data.data;
            const index = this.items.findIndex((s) => s.user_cd === user.user_cd);
            this.items[index] = user;
            this.onCloseModal();
          }
        })
        .catch((err) => {
          this.getByClassTrTag(item.user_cd, true);
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 2000 });
        })
        .finally(() => {
          const className = 'td_userCd--';
          const trBackGround = document.getElementById(`${className}${item.user_cd}`);
          setTimeout(() => {
            trBackGround?.classList.remove('back-ground-active');
          }, 2500);
        });
    },
    modalConfirm(callBack, data) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { mode: 1, textCancel: 'いいえ', params: { callBack, data }, message: 'このユーザーのパスワードを再発行しますが、よろしいですか？' }
      };
    },
    resetInfor(e) {
      e.callBack();
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.show {
  display: block !important;
  top: 0px;
}
.passReissue-tbl {
  margin: 0 24px;
  tr {
    th,
    td {
      vertical-align: middle;
    }
    th {
      font-size: 14px;
    }
    td {
      font-size: 14px;
      padding-top: 9px;
      padding-bottom: 9px;
      min-width: 172px;
      &:nth-child(0) {
        text-align: center;
        width: 1rem;
        min-width: 110px;
      }
      &:last-child {
        width: 1.5rem;
        min-width: 214px;
        text-align: center;
      }
    }
  }
}
.modal-content {
  padding: 20px 0;
}
.content {
  padding: 0px 25px;
}
.profileBtn {
  padding: 20px 0;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  .btn {
    width: 180px;
    margin-right: 24px;
    &:last-child {
      margin-right: 0;
    }
  }
}
.pagination-cases {
  display: flex;
  flex-direction: row-reverse;
  width: 100%;
  align-items: center;
}
.total-items {
  margin-right: 10px;
}
.iconClear {
  cursor: pointer;
  position: absolute;
  right: 35px;
  top: 33%;
  width: 20px;
}
.no-data {
  padding-top: 10px;
  overflow-y: auto;
  height: 350px;
  text-align: center;
  .no-data-content {
    width: 100%;
    .no-data-text {
      font-size: 1rem;
    }
    .no-data-thumb {
      max-width: 400px;
      margin: 10px auto 0;
    }
  }
}
.tag-a {
  color: #59a5ff !important;
  cursor: pointer;
  &:hover {
    text-decoration: underline !important;
  }
}

.el-tag-custom {
  color: #225999;
  background: #d1e4ff;
  height: 27px;
  line-height: 23px;
  font-size: 14px;
  align-items: center;
  margin: 2px 10px 2px 0;
  border-radius: 24px;
}
.back-ground-active {
  animation-name: lightColor;
  animation-duration: 2s;
  background-color: #1bc5bd !important;
}
.back-ground-leave {
  animation-name: lightColorLeave;
  animation-duration: 1.5s;
  background-color: #1bc5bd !important;
}
@keyframes lightColor {
  0% {
    background-color: #9cf5f0;
  }
  50% {
    background-color: #64e0da;
  }
  100% {
    background-color: #1bc5bd;
  }
}
@keyframes lightColorLeave {
  0% {
    background-color: #1bc5bd;
  }
  50% {
    background-color: #64e0da;
  }
  50% {
    background-color: #9ef3ef;
  }
  100% {
    background-color: #ffff;
  }
}
</style>
