<template>
  <!-- Start  -->
  <div class="modal-body modal-workHistory modal-body-H02S07">
    <div id="msfa-notify-H02S07"></div>
    <div class="tbl-workHistory table-hg100 scrollbar">
      <table class="tableBox tableCustom">
        <thead>
          <tr>
            <th>施設コード</th>
            <th>施設略名</th>
            <th>所属部科</th>
            <th>施設役職</th>
            <th>大学職位</th>
            <th>開始日</th>
            <th>終了日</th>
          </tr>
        </thead>
        <tbody v-loading="isLoading">
          <tr v-for="(item, index) of listDatas" :key="item" @click="active(index, item)" @touchstart="active(index, item)">
            <td :class="activeIndex === index ? 'active' : ''">{{ item.facility_cd }}</td>
            <td :class="activeIndex === index ? 'active' : ''">
              <span class="workHistory-link link" @click="facilityDetail(item.facility_cd)">{{ item.facility_short_name }}</span>
            </td>
            <td :class="activeIndex === index ? 'active' : ''">{{ item.department_name }}</td>
            <td :class="activeIndex === index ? 'active' : ''">{{ item.hospital_position_name }}</td>
            <td :class="activeIndex === index ? 'active' : ''">{{ item.academic_position_name }}</td>
            <td :class="activeIndex === index ? 'active' : ''">{{ formatFullDate(item.start_date) }}</td>
            <td :class="activeIndex === index ? 'active' : ''">{{ item.end_date === '9999-12-31' ? '' : formatFullDate(item.end_date) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="btn-workHistory">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
      <button :disabled="disableBtn" type="button" class="btn btn-primary" @click="submit">決定</button>
    </div>
  </div>
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
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="handleYes"></component>
    </template>
  </el-dialog>
  <!-- End -->
</template>

<script>
import H02_S07_PersonalDetailsNetworkSearch from '../../../api/H02/H02_S07_PersonalDetailsNetworkSearch';
export default {
  name: 'H02_S07_PersonalDetailsNetworkSearchModalSearchFilter',
  components: {},
  emits: ['onFinishScreen'],
  data() {
    return {
      listDatas: [],
      activeItem: '',
      activeIndex: '',
      personCd: '',
      disableBtn: true,
      isLoading: false,
      modalConfig: {
        title: '',
        customClass: 'custom-dialog z05-s01 modal-fixed modal-fixed-min',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      }
    };
  },

  mounted() {
    this.personCd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd || localStorage.getItem('person_cd_prev') : '';
    this.getListData();
  },
  methods: {
    getListData() {
      this.js_pscroll();
      this.isLoading = true;
      const data = {
        person_cd: this.personCd
      };
      H02_S07_PersonalDetailsNetworkSearch.getListModal(data.person_cd)
        .then((res) => {
          if (res) {
            this.listDatas = res.data.data;
          }
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-H02S07', idNodeNotify: 'msfa-notify-H02S07' });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    handleYes() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      this.$emit('onFinishScreen', 1);
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    active(index, item) {
      this.activeIndex = index;
      this.activeItem = item;
      this.disableBtn = false;
    },
    closeModal() {
      if (this.disableBtn) {
        this.$emit('onFinishScreen', 1);
      } else {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          title: null,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false
        };
      }
    },
    submit() {
      const data = {
        facility_short_name: this.activeItem.facility_short_name,
        facility_cd: this.activeItem.facility_cd
      };
      this.$emit('onFinishScreen', data);
    },
    facilityDetail(facility_cd) {
      this.closeModal();
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
/** start css layout  **/
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
/*** Css H02_S07_PersonalDetailsNetworkSearch ***/
.active {
  font-weight: 700;
  background: #eef6ff;
}
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
    padding: 14px 24px 8px;
    overflow: hidden;
    .tblSearch {
      tr {
        th {
          font-size: 1rem;
          min-width: 160px;
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
  padding: 0 !important;
  .tbl-workHistory {
    max-height: 420px;
    min-height: 91px;
    tr {
      &:hover {
        td {
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
