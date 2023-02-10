<template>
  <!-- Start  -->
  <!-- name: セレクトグループコピー -->
  <div class="modal-selectCopy modal-body-Z03S04">
    <div id="msfa-notify-Z03S04"></div>
    <div class="selectCopy-row">
      <div class="selectCopy-col-1">
        <div class="selectCopy-user">
          <label class="selectCopy-user-label">ユーザ</label>
          <div class="selectCopy-user-control">
            <div class="form-icon icon-right">
              <span class="icon icon--cursor log-icon" title_log="ユーザ" @click="openModalZ05S01()">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
              </span>
              <div v-if="formData.user_name" class="form-control d-flex align-items-center">
                <div class="block-tags">
                  <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveUser()">
                    {{ formData.user_name }}
                  </el-tag>
                </div>
              </div>
              <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
            </div>
          </div>
        </div>
        <h2 class="selectCopy-title">
          <span class="item">
            <img v-if="selectMode === 1" class="svg" src="@/assets/template/images/icon_hospital.svg" alt="" />
            <img v-if="selectMode === 2" class="svg" src="@/assets/template/images/icon_hospital-user.svg" alt="" />
          </span>
          {{ selectMode === 1 ? 'セレクト施設グループ' : 'セレクト施設個人グループ' }}
        </h2>
        <div class="colSlider">
          <div class="colSlider-btn">
            <button ref="btnScrollTopSub" type="button" class="btn btn-previous" :disabled="disableScrollTopBtn()" @click="scrollToTop()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line.svg')" alt="" />
            </button>
            <button ref="btnScrollBottomSub" type="button" class="btn btn-next" :disabled="disableScrollBottomBtn()" @click="scrollToBottom()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line.svg')" alt="" />
            </button>
          </div>
          <div id="scrollTabSub" class="colSlider-tabs" @scroll="getElementInScreen">
            <ul v-if="selectMode === 1" class="nav navTabs-sider">
              <li v-if="positionScroll === 'top'" class="sticky sticky-top">
                <a class="tab-item active">
                  <span class="column-drag-handle left" />
                  <div class="right active text-over" href="#tabs-content" data-toggle="tab" role="tab">
                    {{ facilityGroupSelected.select_facility_group_name }}
                  </div>
                </a>
              </li>
              <li v-if="positionScroll === 'bottom'" class="sticky sticky-bottom">
                <a class="tab-item active">
                  <span class="column-drag-handle left" />
                  <div class="right active text-over" href="#tabs-content" data-toggle="tab" role="tab">
                    {{ facilityGroupSelected.select_facility_group_name }}
                  </div>
                </a>
              </li>
              <li
                v-for="facilityGroup in lstFacilityGroups"
                :id="`subid_${facilityGroup.select_facility_group_id}`"
                :key="facilityGroup.select_facility_group_id"
                @click="selectedFacilityGroup(facilityGroup)"
              >
                <a :class="facilityGroup.selected ? 'active' : ''">
                  <div class="text-over" href="#tabs-content-1" data-toggle="tab" role="tab">
                    {{ facilityGroup.select_facility_group_name }}
                  </div>
                </a>
              </li>
            </ul>

            <ul v-else class="nav navTabs-sider">
              <li v-if="positionScroll === 'top'" class="sticky sticky-top">
                <a class="active">
                  <div class="text-over" href="#tabs-content-2" data-toggle="tab" role="tab">
                    {{ personGroupSelected.select_person_group_name }}
                  </div>
                </a>
              </li>
              <li v-if="positionScroll === 'bottom'" class="sticky sticky-bottom">
                <a class="active">
                  <div class="text-over" href="#tabs-content-2" data-toggle="tab" role="tab">
                    {{ personGroupSelected.select_person_group_name }}
                  </div>
                </a>
              </li>
              <div>
                <li
                  v-for="personGroup in lstPersonGroups"
                  :id="`subid_${personGroup.select_person_group_id}`"
                  :key="personGroup.select_person_group_id"
                  @click="selectedPersonGroup(personGroup)"
                >
                  <a :class="personGroup.selected ? 'active' : ''">
                    <div class="text-over" href="#tabs-content-2" data-toggle="tab" role="tab">
                      {{ personGroup.select_person_group_name }}
                    </div>
                  </a>
                </li>
              </div>
            </ul>
            <!-- Tab panes -->
          </div>
        </div>
      </div>
      <div class="selectCopy-col-2">
        <div class="tab-content">
          <!-- Facility Gr -->
          <div id="tabs-content-1" :class="selectMode === 1 ? 'active' : ''" class="tab-pane" role="tabpanel">
            <div v-if="checkFacilityGroupSelected()" class="selectCopy-content">
              <div class="selectCopy-head">
                <h2 class="selectCopy-head-title">{{ facilityGroupSelected.select_facility_group_name }}</h2>
              </div>
              <div class="selectCopy-main">
                <div class="selectCopy-mainTitle"><h3 class="selectCopy-mainTitle-tlt">施設</h3></div>
                <div class="selectCopy-mainBody scrollbar">
                  <div class="selectCopy-lst">
                    <div class="selectCopy-ul">
                      <ul>
                        <li v-for="item in facilityGroupSelected.children" :key="item.facility_cd">
                          <a class="selectCopy-a person-text-nomal log-icon" title_log="施設名" @click="redirectToH01S02(item)">
                            {{ item.facility_short_name }}
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="!checkFacilityGroupSelected()" class="no-data">
              <div class="no-data-content">
                <p class="no-data-text">該当するデータがありません。</p>
                <div class="no-data-thumb">
                  <img class="svg" src="@/assets/template/images/data/amico.svg" alt="" />
                </div>
              </div>
            </div>
          </div>

          <!-- Person Gr -->
          <div id="tabs-content-2" :class="selectMode === 2 ? 'active' : ''" class="tab-pane" role="tabpanel">
            <div v-if="checkPersonGroupSelected()" class="selectCopy-content">
              <div class="selectCopy-head">
                <h2 class="selectCopy-head-title">{{ personGroupSelected.select_person_group_name }}</h2>
              </div>
              <div class="selectCopy-main">
                <div class="selectCopy-mainTitle"><h3 class="selectCopy-mainTitle-tlt">施設医師</h3></div>
                <div class="selectCopy-mainBody scrollbar">
                  <template v-for="facilityCategory in personGroupSelected.children" :key="facilityCategory.facility_category_type">
                    <div v-if="facilityCategory.children.length > 0" class="selectCopy-lst">
                      <div class="selectCopy-lstHead">
                        <div class="facility-title">{{ facilityCategory.facility_category_name }}</div>
                        <div class="facility-label">
                          <ul>
                            <li v-if="facilityCategory.content_name">
                              <span class="facility-tlt">面談内容： </span>
                              <span class="facility-text">{{ facilityCategory.content_name }}</span>
                            </li>
                            <li v-if="facilityCategory.product_name" class="w-calc">
                              <span class="facility-tlt">品目： </span>
                              <span class="facility-text">{{ facilityCategory.product_name }}</span>
                            </li>
                            <li v-if="facilityCategory.document_name" class="w-100">
                              <span class="facility-tlt">資材： </span>
                              <span class="facility-text">{{ facilityCategory.document_name }}</span>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="selectCopy-ul">
                        <ul>
                          <li v-for="item in facilityCategory.children" :key="item.person_cd">
                            <div class="facility-list-info">
                              <span class="facility-list-label">{{ item.facility_short_name }}</span>
                              <span class="facility-list-label">{{ item.department_name }}</span>
                              <a class="facility-list-link person-text-nomal log-icon" title_log="個人名" @click="redirectToH02S02(item)">{{
                                item.person_name
                              }}</a>
                              <span class="facility-list-label">{{ item.position_name }}</span>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>
            <div v-if="!checkPersonGroupSelected()" class="no-data">
              <div class="no-data-content">
                <p class="no-data-text">該当するデータがありません。</p>
                <div class="no-data-thumb">
                  <img class="svg" src="@/assets/template/images/data/amico.svg" alt="" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="selectCopy-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" :disabled="processingFlag" @click="closeModal">キャンセル</button>
      <el-button
        type="primary"
        class="btn btn-primary"
        :loading="processingFlag"
        :disabled="processingFlag || checkDisabledSubmit()"
        @click.prevent="onResultData()"
      >
        選択
      </el-button>
    </div>
  </div>
  <!-- End -->

  <!-- Modal Z05-S01 -->
  <el-dialog
    v-model="modalConfig.isShowModal"
    custom-class="custom-dialog modal-fixed"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModalZ05S01"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import Z03_S04_SelectListCopyService from '@/api/Z03/Z03_S04_SelectListCopyService';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';

export default {
  name: 'Z03_S04_SelectListCopy',
  components: { Z05S01Organization },
  props: {
    // 1: select facility Group, 2: select person group
    selectMode: {
      type: Number,
      required: true,
      default: 2
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      loadingPage: false,
      processingFlag: false,

      formData: {
        user_cd: '',
        user_name: ''
      },

      paramsZ05S01: {
        userFlag: 1,
        mode: 'single',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
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

      lstFacilityGroupVisible: [],
      lstPersonGroupVisible: [],
      positionScroll: '',

      sort_order_lasted: 0,

      lstFacilityGroups: [],
      facilityGroupSelected: {},

      lstPersonGroups: [],
      personGroupSelected: {},

      isSelected: false,
      currentUser: null
    };
  },

  mounted() {
    this.emitter.on('copySelect-processingFlag', ({ flag }) => {
      this.processingFlag = flag;
    });

    this.currentUser = this.getCurrentUser();
    this.paramsZ05S01.orgCdList.push(this.currentUser.org_cd);

    this.paramsZ05S01.userCdList.push({
      org_cd: this.currentUser.org_cd,
      user_cd: this.currentUser.user_cd
    });

    this.formData.user_cd = this.currentUser.user_cd;
    this.formData.user_name = this.currentUser.user_name;

    if (this.selectMode === 1) {
      this.getFacilityGroups();
    } else {
      this.getPersonGroups();
    }
  },

  methods: {
    //Facility Group
    async getFacilityGroups() {
      this.lstFacilityGroups = [];
      this.facilityGroupSelected = {};

      let params = {
        user_cd: this.formData.user_cd,
        isCopy: true
      };
      this.loadingPage = true;
      Z03_S04_SelectListCopyService.getFacilityGroups(params)
        .then((res) => {
          this.lstFacilityGroups = res?.data?.data?.facility_group;
          this.lstFacilityGroups = this.lstFacilityGroups.map((x, i) => {
            return {
              ...x,
              sort_order_fix: i
            };
          });
          this.sort_order_lasted = this.lstFacilityGroups.length;
        })
        .catch((err) => {
          this.notifyModal({ message: err.response?.data.message, type: 'error', classParent: 'modal-body-Z03S04', idNodeNotify: 'msfa-notify-Z03S04' });
        })
        .finally(() => {
          let time;
          const timeout = setTimeout(() => {
            if (time) {
              clearTimeout(timeout);
              return;
            }

            if (this.lstFacilityGroups.length > 0) {
              this.getElementInScreen();
            }
            this.loadingPage = false;

            time = 1;
          }, 0);
        });
    },

    selectedFacilityGroup(item) {
      this.lstFacilityGroups.forEach((x) => {
        x.selected = false;
        if (x.select_facility_group_id === item.select_facility_group_id) {
          x.selected = true;
          this.facilityGroupSelected = { ...x };
        }
      });
      this.checkPositionItemScroll();
    },

    checkFacilityGroupSelected() {
      let length = Object.keys(this.facilityGroupSelected).length;
      return length > 0 ? true : false;
    },

    //  Person Group
    async getPersonGroups() {
      this.lstPersonGroups = [];
      this.personGroupSelected = {};

      let params = {
        user_cd: this.formData.user_cd,
        isCopy: true
      };

      this.loadingPage = true;
      Z03_S04_SelectListCopyService.getPersonGroups(params)
        .then((res) => {
          this.lstPersonGroups = res?.data?.data.person_group;
          this.lstPersonGroups = this.lstPersonGroups.map((x, i) => {
            return {
              ...x,
              sort_order_fix: i
            };
          });
          this.sort_order_lasted = this.lstPersonGroups.length;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          let time;
          const timeout = setTimeout(() => {
            if (time) {
              clearTimeout(timeout);
              return;
            }
            if (this.lstPersonGroups.length > 0) {
              this.getElementInScreen();
            }
            this.loadingPage = false;
            time = 1;
          }, 0);
        });
    },

    selectedPersonGroup(item) {
      this.lstPersonGroups.forEach((x) => {
        x.selected = false;
        if (x.select_person_group_id === item.select_person_group_id) {
          x.selected = true;
          this.personGroupSelected = { ...x };
        }
      });
      this.checkPositionItemScroll();
    },

    checkPersonGroupSelected() {
      let length = Object.keys(this.personGroupSelected).length;
      return length > 0 ? true : false;
    },

    // check form
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.paramsZ05S01 },
        width: '45rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (e) {
        this.paramsZ05S01.orgCdList = [];
        this.paramsZ05S01.userCdList = [];
        e.userSelected?.forEach((x) => {
          this.paramsZ05S01.orgCdList.push(x.org_cd);
        });
        e.userSelected?.forEach((x) => {
          this.paramsZ05S01.userCdList.push({
            org_cd: x.org_cd,
            user_cd: x.user_cd
          });
        });

        this.formData.user_cd = e.userSelected[0]?.user_cd;
        this.formData.user_name = e.userSelected[0]?.user_name;

        if (this.selectMode === 1) {
          this.getFacilityGroups();
        } else {
          this.getPersonGroups();
        }
      }

      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    handleRemoveUser() {
      this.formData.user_cd = '';
      this.formData.user_name = '';

      this.showModalZ05S01 = false;

      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        orgCdList: [],
        userCdList: []
      };

      this.lstFacilityGroups = [];
      this.lstPersonGroups = [];
      this.lstFacilityGroupVisible = [];
      this.lstPersonGroupVisible = [];
    },

    redirectToH01S02(item) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd: item.facility_cd
        },
        query: {
          tab: '1'
        }
      });
    },

    redirectToH02S02(item) {
      let person_cd = item.person_cd;
      this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
    },

    // return data to screen parent
    onResultData() {
      this.processingFlag = true;
      if (!this.isSelected) {
        let result = {
          screen: 'Z03_S04',
          facilityGroupSelected: this.facilityGroupSelected,
          personGroupSelected: this.personGroupSelected
        };
        this.isSelected = true;
        this.$emit('onFinishScreen', result);
      }
    },

    closeModal() {
      this.$emit('onFinishScreen');
    },

    // scroll
    scrollToView(id) {
      document.getElementById(id).scrollIntoView({
        behavior: 'smooth'
      });
      this.getElementInScreen();
    },

    getElementInScreen() {
      let itemScrollTab = document.getElementById('scrollTabSub');
      if (this.selectMode === 1) {
        this.lstFacilityGroupVisible = [];
        this.lstFacilityGroups.forEach((x) => {
          let element = document.getElementById(`subid_${x.select_facility_group_id}`);
          if (!(element?.offsetTop - itemScrollTab.scrollTop + 22 < 0 || element?.offsetTop - itemScrollTab.scrollTop + 24 > itemScrollTab?.offsetHeight)) {
            this.lstFacilityGroupVisible.push(x);
          }
        });
      } else {
        this.lstPersonGroupVisible = [];
        this.lstPersonGroups.forEach((x) => {
          let element = document.getElementById(`subid_${x.select_person_group_id}`);
          if (!(element?.offsetTop - itemScrollTab.scrollTop + 22 < 0 || element?.offsetTop - itemScrollTab.scrollTop + 24 > itemScrollTab?.offsetHeight)) {
            this.lstPersonGroupVisible.push(x);
          }
        });
      }

      this.checkPositionItemScroll();
    },

    scrollToTop() {
      if (this.selectMode === 1) {
        let index = this.lstFacilityGroups.findIndex((x) => x.select_facility_group_id === this.lstFacilityGroupVisible[0]?.select_facility_group_id);
        let item = this.lstFacilityGroups[index - 1];
        if (this.lstFacilityGroupVisible[0]?.select_facility_group_id !== this.lstFacilityGroups[0]?.select_facility_group_id) {
          this.scrollToView(`subid_${item?.select_facility_group_id}`);
          this.$refs.btnScrollTopSub.click();
        } else {
          this.scrollToView(`subid_${this.lstFacilityGroups[0]?.select_facility_group_id}`);
          this.$refs.btnScrollTopSub.click();
        }
      } else {
        let index = this.lstPersonGroups.findIndex((x) => x.select_person_group_id === this.lstPersonGroupVisible[0]?.select_person_group_id);
        let item = this.lstPersonGroups[index - 1];
        if (this.lstPersonGroupVisible[0]?.select_person_group_id !== this.lstPersonGroups[0]?.select_person_group_id) {
          this.scrollToView(`subid_${item?.select_person_group_id}`);
          this.$refs.btnScrollTopSub.click();
        } else {
          this.scrollToView(`subid_${this.lstPersonGroups[0]?.select_person_group_id}`);
          this.$refs.btnScrollTopSub.click();
        }
      }
    },

    scrollToBottom() {
      if (this.selectMode === 1) {
        let length = this.lstFacilityGroupVisible.length;

        let index = this.lstFacilityGroups.findIndex((x) => x.select_facility_group_id === this.lstFacilityGroupVisible[0]?.select_facility_group_id);
        let item = this.lstFacilityGroups[index + 1];

        if (this.lstFacilityGroupVisible[length - 1]?.sort_order_fix !== this.sort_order_lasted) {
          this.scrollToView(`subid_${item?.select_facility_group_id}`);
          this.$refs.btnScrollBottomSub.click();
        } else {
          this.scrollToView(`subid_${this.lstFacilityGroupVisible[length - 1]?.select_facility_group_id}`);
          this.$refs.btnScrollBottomSub.click();
        }
      } else {
        let length = this.lstPersonGroupVisible.length;
        let index = this.lstPersonGroups.findIndex((x) => x.select_person_group_id === this.lstPersonGroupVisible[0]?.select_person_group_id);
        let item = this.lstPersonGroups[index + 1];
        if (this.lstPersonGroupVisible[length - 1].sort_order_fix !== this.sort_order_lasted) {
          this.scrollToView(`subid_${item.select_person_group_id}`);
          this.$refs.btnScrollBottomSub.click();
        } else {
          this.scrollToView(`subid_${this.lstPersonGroupVisible[length - 1].select_person_group_id}`);
          this.$refs.btnScrollBottomSub.click();
        }
      }
    },

    checkPositionItemScroll() {
      this.positionScroll = '';
      if (this.selectMode === 1) {
        let length = this.lstFacilityGroupVisible.length;
        if (this.lstFacilityGroupVisible[0]?.sort_order_fix > this.facilityGroupSelected?.sort_order_fix) {
          this.positionScroll = 'top';
        } else {
          if (this.lstFacilityGroupVisible[length - 1]?.sort_order_fix < this.facilityGroupSelected?.sort_order_fix) {
            this.positionScroll = 'bottom';
          }
        }
      } else {
        let length = this.lstPersonGroupVisible.length;
        if (this.lstPersonGroupVisible[0]?.sort_order_fix > this.personGroupSelected?.sort_order_fix) {
          this.positionScroll = 'top';
        } else {
          if (this.lstPersonGroupVisible[length - 1]?.sort_order_fix < this.personGroupSelected?.sort_order_fix) {
            this.positionScroll = 'bottom';
          }
        }
      }
    },

    disableScrollTopBtn() {
      if (this.selectMode === 1) {
        if (this.lstFacilityGroups.length === 0 || this.lstFacilityGroupVisible[0]?.sort_order_fix === this.lstFacilityGroups[0]?.sort_order_fix) {
          return true;
        }
        return false;
      } else {
        if (this.lstPersonGroups.length === 0 || this.lstPersonGroupVisible[0]?.sort_order_fix === this.lstPersonGroups[0]?.sort_order_fix) {
          return true;
        }
        return false;
      }
    },

    disableScrollBottomBtn() {
      if (this.selectMode === 1) {
        let length = this.lstFacilityGroupVisible.length;
        if (
          this.lstFacilityGroups.length === 0 ||
          length === 0 ||
          this.lstFacilityGroupVisible[length - 1]?.sort_order_fix === this.sort_order_lasted ||
          this.lstFacilityGroupVisible[length - 1]?.sort_order_fix === this.lstFacilityGroups[this.lstFacilityGroups.length - 1]?.sort_order_fix
        ) {
          return true;
        }
        return false;
      } else {
        let length = this.lstPersonGroupVisible.length;
        if (
          this.lstPersonGroups.length === 0 ||
          length === 0 ||
          this.lstPersonGroupVisible[length - 1]?.sort_order_fix === this.sort_order_lasted ||
          this.lstPersonGroupVisible[length - 1]?.sort_order_fix === this.lstPersonGroups[this.lstPersonGroups.length - 1]?.sort_order_fix
        ) {
          return true;
        }
        return false;
      }
    },

    checkDisabledSubmit() {
      let disabled = false;
      if (this.selectMode === 1) {
        disabled = Object.keys(this.facilityGroupSelected).length > 0 ? false : true;
      } else {
        disabled = Object.keys(this.personGroupSelected).length > 0 ? false : true;
      }
      return disabled;
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.modal-selectCopy {
  .selectCopy-row {
    display: flex;
    flex-wrap: wrap;
    .selectCopy-col-1 {
      width: 40%;
    }
    .selectCopy-col-2 {
      width: 60%;
      box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
      border-radius: 10px;
      background: #fff;
      padding: 20px 28px;
      position: relative;
      z-index: 1;
      @media (max-width: $viewport_tablet) {
        padding: 20px 18px;
      }
    }
  }
  .selectCopy-user {
    padding: 0 32px 0 0;
    .selectCopy-user-label {
      font-size: 1rem;
      margin-bottom: 8px;
    }
  }
  .selectCopy-title {
    padding: 23px 32px 23px 0;
    font-size: 1.125rem;
    font-weight: 700;
    display: flex;
    color: #5f6b73;
    .item {
      min-width: 20px;
      margin-right: 6px;
      margin-top: -2px;
    }
  }
  .colSlider {
    height: 400px;
    overflow: hidden;
    position: relative;
    padding: 24px 0;
    background: #fcfcfc;
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    .colSlider-btn {
      .btn {
        position: absolute;
        left: 0;
        width: 100%;
        height: 24px;
        padding: 0;
        background: #fff;
        border-radius: 0;
        z-index: 3;
        &::after {
          position: absolute;
          top: 0;
          right: -2px;
          content: '';
          width: 1px;
          height: 100%;
          box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.9);
        }
        &:hover {
          background: #f2f2f2;
        }
        &.btn-previous {
          top: 0;
          box-shadow: 0px 3px 6px #e3e3e3;
          .svg {
            transform: rotate(180deg);
          }
        }
        &.btn-next {
          bottom: 0;
          box-shadow: 0px -3px 6px #e3e3e3;
        }
      }
    }
    .colSlider-tabs {
      height: 350px;
      overflow: hidden;
      padding: 1px 0;
      .navTabs-sider {
        display: block;
        li {
          border-bottom: 1px solid #ccd4da;
          a {
            padding: 18px 54px 18px 20px;
            display: block;
            color: #5f6b73;
            font-size: 1rem;
            border-left: 4px solid transparent;
            position: relative;
            letter-spacing: 0.05em;
            line-height: 1.5;
            cursor: pointer;
            @media (max-width: $viewport_tablet) {
              font-size: 0.875rem;
            }
            &::after {
              position: absolute;
              top: calc(50% - 6px);
              right: 30px;
              content: '';
              border-top: 6px solid transparent;
              border-bottom: 6px solid transparent;
              border-left: 6px solid #99a5ae;
            }
            &:hover {
              background: #fff;
              text-decoration: none;
            }
            &.active {
              background: #fff;
              z-index: 2;
              box-shadow: 1px 1px 12px 3px #b7c3cbe6;
              border-left: 4px solid #448add;
              color: #2d3033;
              font-weight: 700;
              &::after {
                border: none;
              }
            }
          }
        }
      }
    }
  }
  .tab-content,
  .tab-pane {
    height: 100%;
  }
  .selectCopy-content {
    .selectCopy-head {
      .selectCopy-head-title {
        padding-bottom: 12px;
        color: #5f6b73;
        font-size: 1.125rem;
        font-weight: bold;
        padding-left: 20px;
      }
    }
    .selectCopy-main {
      .selectCopy-mainTitle {
        font-size: 1rem;
        font-weight: 700;
        padding: 14px 20px;
        border-bottom: 1px solid #e3e3e3;
        h3 {
          color: #2d3033;
        }
      }
      .selectCopy-mainBody {
        height: 415px;
      }
    }
  }
  .selectCopy-lstHead {
    background: #f5f5f5;
    padding: 12px 20px;
  }
  .selectCopy-lst {
    .selectCopy-ul {
      ul {
        li {
          padding: 12px 20px;
          border-bottom: 1px solid #e3e3e3;
          display: flex;
          justify-content: space-between;
          align-items: center;
          min-height: 55px;
          padding: 10px 24px;

          .facility-list-info {
            width: 100%;
            padding-right: 10px;
            font-size: 1rem;
            .facility-list-label {
              padding-right: 15px;
              color: #2d3033;
              white-space: pre;
            }

            .facility-list-link {
              cursor: pointer;
              padding-right: 15px;
              color: #59a5ff;
              white-space: pre;
              &:hover {
                color: #59a5ff;
              }
            }
          }
        }
      }

      .selectCopy-a {
        color: #59a5ff;
        cursor: pointer;
      }
    }
  }
  .selectCopy-btn {
    margin-top: 20px;
    text-align: center;
    .btn {
      margin-right: 24px;
      width: 180px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}

.no-data {
  padding-top: 60px;
  overflow-y: auto;
  height: 100%;
  text-align: center;
  .no-data-content {
    width: 100%;
    .no-data-text {
      font-size: 1rem;
    }
    .no-data-thumb {
      max-width: 400px;
      margin: 46px auto 0;
    }
  }
}

.custom-input {
  position: relative;
}
.iconClear {
  cursor: pointer;
  position: absolute;
  right: 35px;
  top: 33%;
  width: 20px;
}

.tag-a {
  color: #59a5ff !important;
  cursor: pointer;
  &:hover {
    text-decoration: underline !important;
  }
}

.text-over {
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}

.btn {
  &:disabled {
    cursor: not-allowed;
  }
}

.modal-selectCopy .colSlider .colSlider-tabs {
  overflow: scroll;
  direction: rtl;
}

.modal-selectCopy .colSlider .colSlider-tabs .navTabs-sider li {
  direction: initial;
}

.sticky {
  position: -webkit-sticky;
  position: absolute;
  z-index: 3;
  box-shadow: -18px 0px 9px 5px #00000021 !important;
  width: 100%;
}

.sticky-top {
  top: 25px;
}

.sticky-bottom {
  bottom: 25px;
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

.selectCopy-lstHead {
  width: 100%;
  padding-right: 10px;
  display: flex;
  flex-wrap: wrap;
  @media (max-width: $viewport_tablet) {
    padding-right: 0;
  }
  .facility-title {
    color: #2d3033;
    font-size: 1rem;
    font-weight: 500;
    width: 94px;
    padding-right: 16px;
  }

  .facility-label {
    width: calc(100% - 95px);
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -10px;
      li {
        width: 248px;
        display: flex;
        padding-left: 10px;
        .facility-tlt {
          font-weight: normal;
          margin-right: 8px;
          min-width: 56px;
          display: inline-block;
        }
        .facility-text {
          color: #2d3033;
        }
      }

      .w-calc {
        min-width: 248px;
        width: calc(100% - 248px);
        @media (max-width: $viewport_tablet) {
          width: 100%;
        }
      }
    }
  }
}
</style>
