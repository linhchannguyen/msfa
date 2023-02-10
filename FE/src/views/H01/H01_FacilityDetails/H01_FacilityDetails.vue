<template>
  <div v-loading-container="loading" class="wrapContent">
    <div class="groupPersonal">
      <div class="personalDetails">
        <!-- start Head Details -->
        <div class="personalHead">
          <div class="personalHead__user">
            <span class="personalHead-item">
              <img src="@/assets/template/images/icon_medical-hospital.svg" alt="" />
            </span>
            <div class="personalHead-content">
              <div class="personalHead-label">
                <span class="name">{{ information.facility_short_name }}</span>
                <button v-if="information.break_flag === 1" type="button" class="btn btn-danger">削除</button>
              </div>
              <p class="personalHead-text">{{ information.facility_short_name_kana }}</p>
            </div>
          </div>
          <div class="personalHead__info">
            <ul>
              <li v-if="information.ultmarc_delete_flag === 1">削除理由：{{ information.delete_reason_type_label }}</li>
              <li>施設コード：{{ information.facility_cd }}</li>
            </ul>
          </div>
        </div>
        <!-- end Head Details -->
        <!-- start Tabs -->
        <div class="personalTabs">
          <ul class="nav navTabs navTabs--personal width-tabs cstab">
            <li>
              <a id="a1" role="tab" @click="goTab('1')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_basicInformation.svg')" alt="" />
                  </span>
                  基本情報
                </div>
              </a>
            </li>
            <li>
              <a id="a2" role="tab" @click="goTab('2')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_workInformation.svg')" alt="" />
                  </span>
                  勤務個人一覧
                </div>
              </a>
            </li>
            <li>
              <a id="a3" role="tab" @click="goTab('3')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_notes.svg')" alt="" />
                  </span>
                  注意事項
                </div>
              </a>
            </li>
            <li>
              <a id="a4" role="tab" @click="goTab('4')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_timeline.svg')" alt="" />
                  </span>
                  タイムライン
                </div>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div v-if="changetab == '1'" id="tab_1" role="tabpanel" class="tab-pane" :class="['tab-pane', changetab == '1' ? 'active' : '']">
              <H01S02FacilityDetailsBasicInformation :check-loading="checkLoading" :changetab="changetab" @changeLoading="changeLoading" />
            </div>
            <div v-if="changetab == '2'" id="tab_2" role="tabpanel" class="tab-pane" :class="['tab-pane', changetab == '2' ? 'active' : '']">
              <H01S03FacilityDetailsWorkingIndividual
                :dropdown-filter="classActive['class2']"
                :changetab="changetab"
                :onfinishsubmit="onfinishsubmit"
                @changeLoading="changeLoading"
              />
            </div>
            <div v-if="changetab == '3'" id="tab_3" role="tabpanel" class="tab-pane" :class="['tab-pane', changetab == '3' ? 'active' : '']">
              <H01S04FacilityDetailsNotes
                :facility_name="information.facility_short_name"
                :dropdown-filter="classActive['class3']"
                :changetab="changetab"
                :onfinishsubmit="onfinishsubmit"
                @changeLoading="changeLoading"
              />
            </div>
            <div v-if="changetab == '4'" id="tab_4" role="tabpanel" class="tab-pane" :class="['tab-pane', changetab == '4' ? 'active' : '']">
              <H01S05FacilityDetailsTimeline :dropdown-filter="classActive['class4']" :changetab="changetab" @changeLoading="changeLoading" />
            </div>
          </div>
        </div>
        <!-- end Tabs -->
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
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="handleConfirm"
          @handleYes="handleYes"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import H01S03FacilityDetailsWorkingIndividual from '../H01_S03_FacilityDetailsWorkingIndividual/_H01_S03_FacilityDetailsWorkingIndividual.vue';
import H01S04FacilityDetailsNotes from '../H01_S04_FacilityDetailsNotes/H01_S04_FacilityDetailsNotes.vue';
import H01_S02_FacilityDetailsBasicInformation from '../../../api/H01/H01_S02_FacilityDetailsBasicInformation';
import H01S02FacilityDetailsBasicInformation from '../H01_S02_FacilityDetailsBasicInformation/_H01_S02_FacilityDetailsBasicInformation.vue';
import H01S05FacilityDetailsTimeline from '../H01_S05_FacilityDetailsTimeline/H01_S05_FacilityDetailsTimeline.vue';
export default {
  name: 'H01_FacilityDetails',
  components: {
    H01S03FacilityDetailsWorkingIndividual,
    H01S04FacilityDetailsNotes,
    H01S02FacilityDetailsBasicInformation,
    H01S05FacilityDetailsTimeline
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        isShowClose: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      loading: false,
      changetab: '0',
      facility_cd: '',
      indexs: '',
      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      },
      checkLoader: false,

      information: {},
      dropdownFilter: 'dropdown-menu dropdown-filter',

      classActive: {
        class1: '',
        class2: '',
        class3: '',
        class4: '',
        class5: '',
        class6: ''
      }
    };
  },
  watch: {
    $route(value) {
      this.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';
      if (this.facility_cd) {
        this.getDetail();
      }
      if (value.query && value.query.tab) {
        this.changetab = value.query.tab;
        this.reloadActiveTab(value.query.tab);
      }
    }
  },
  mounted() {
    this.isLoading = this.$props.checkLoading;

    this.emitter.emit('change-header', {
      title: '施設詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';
    this.activeTab();
    this.getDetail();
    localStorage.removeItem('checkChangTab');
    sessionStorage.removeItem('H01S04');
    for (let i = 0; i < 6; i++) {
      // eslint-disable-next-line eqeqeq
      if (i + 1 == this.$route.query.tab) {
        this.classActive[`class${i + 1}`] = this.dropdownFilter;
      } else {
        this.classActive[`class${i + 1}`] = '';
      }
    }
  },
  methods: {
    changeLoading(value) {
      this.loading = value;
    },
    reloadActiveTab(goTab) {
      const activeTab = document.querySelectorAll('.navTabs--personal li a');
      activeTab.forEach((s) => {
        if (s.id === `a${goTab}`) {
          s.classList.add('active');
        } else {
          s.classList.remove('active');
        }
      });
    },
    checkDateWasEdit(filed) {
      const data = sessionStorage.getItem(filed);
      return data != null ? true : false;
    },
    handleYes() {
      localStorage.removeItem('checkChangTab');
      sessionStorage.removeItem('H01S04');
      this.changetab = this.indexs;

      // Update class active
      for (const key in this.classActive) {
        if (Object.hasOwnProperty.call(this.classActive, key)) {
          this.classActive[key] = key === `class${this.changetab}` ? this.dropdownFilter : '';
        }
      }

      this.reloadActiveTab(this.indexs);
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd: this.facility_cd
        },
        query: {
          tab: this.indexs
        }
      });
      this.onResultModal();
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    goTab(index) {
      this.indexs = index;
      if (JSON.parse(localStorage.getItem('checkChangTab')) || this.checkDateWasEdit('H01S04')) {
        // openModal({ screen: 'PopupConfirm', width: '35rem', isShowClose: false, props: { mode: 1, textCancel: 'いいえ' } })
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' }
        };
        return;
      } else {
        this.changetab = index;
        this.reloadActiveTab(index);

        this.$router.push({
          name: 'H01_FacilityDetails',
          params: {
            facility_cd: this.facility_cd
          },
          query: {
            tab: index
          }
        });
      }
      for (let i = 0; i < 6; i++) {
        // eslint-disable-next-line eqeqeq
        if (i + 1 == index) {
          this.classActive[`class${i + 1}`] = this.dropdownFilter;
        } else {
          this.classActive[`class${i + 1}`] = '';
        }
      }
    },
    getDetail() {
      const data = {
        facility_cd: this.facility_cd || localStorage.getItem('facility_cd_prev')
      };
      H01_S02_FacilityDetailsBasicInformation.getDetail(data)
        .then((res) => {
          if (res) {
            localStorage.setItem('facility_cd_prev', res.data.data.facility_cd);
            this.information = res.data.data;
          }
        })
        // .catch((err) => {
        //   this.$notify({ message: err.response.data.message, customClass: 'error' });
        // })
        .finally(() => {});
    },
    activeTab() {
      const tabIndex = this.$route.query?.tab;

      this.changetab = tabIndex ? tabIndex : '1';
      // const active = ' active';
      setTimeout(() => {
        if (this.changetab > '0' && this.changetab <= '4') {
          const tabActive = document.querySelector('#a' + this.changetab);
          tabActive.className = 'active';
        } else {
          this.$router.push({ name: 'NotFound' });
        }
      }, 1000);
    }
  },
  match: {}
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet_mini: 768px;

.navTabs li a.active {
  background: #fff;
  color: #448add !important;
  font-weight: 700;
  pointer-events: none;
  transition: 0.5s;
}

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
      width: 550px;
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
        img {
          width: 21px;
        }
      }
      .personalHead-content {
        .personalHead-label {
          .name {
            font-size: 24px;
            font-weight: 700;
            line-height: 140%;
            overflow: hidden;
          }
          .btn {
            &.btn-light {
              border-radius: 2px;
              width: 60px;
              height: 24px;
              padding: 0 6px;
              margin: 6px 0 0 20px;
            }
            &.btn-danger {
              height: 24px;
              border-radius: 2px;
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
      width: calc(100% - 550px);
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
          color: #99a5ae;
          @media (max-width: $viewport_desktop) {
            margin-right: 20px;
          }
          &:last-child {
            margin-right: 0;
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
        min-width: 310px;
        @media (max-width: $viewport_desktop) {
          min-width: 225px;
        }
        @media (max-width: $viewport_tablet) {
          min-width: inherit;
          width: 24%;
        }
        a {
          cursor: pointer;
          color: #99a5ae;
          &:hover {
            color: #5f6b73;
            font-weight: bold;
          }
        }
        .navItem {
          @media (max-width: $viewport_tablet) {
            min-width: 15px;
            margin-right: 6px;
          }
        }
      }
    }
    .tab-content {
      background: #fff;
      height: 100%;
      box-shadow: 0px 0px 5px #b7c3cb;
      position: relative;
      overflow: hidden;
      .tab-pane {
        height: 100%;
      }
    }
  }
}
/** end css layout **/

@media (max-width: $viewport_tablet_mini) {
  .navInfo {
    padding: 12px 10px !important ;
  }
}
@media (max-width: 991px) {
  .navInfo {
    font-size: 12px;
  }
}
</style>
