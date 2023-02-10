<template>
  <div v-loading-container="loading" class="wrapContent">
    <div class="groupPersonal">
      <div class="personalDetails">
        <!-- start Head Details -->
        <div class="personalHead">
          <div class="personalHead__user">
            <span class="personalHead-item">
              <img src="@/assets/template/images/icon_user04.svg" alt="" />
            </span>
            <div class="personalHead-content">
              <div class="personalHead-label">
                <span class="name">{{ info.person_name }}</span>
                <button v-if="info.ultmarc_delete_flag === 1" type="button" class="btn btn-light">削除</button>
              </div>
              <p class="personalHead-text">{{ info.person_name_kana }}</p>
            </div>
          </div>
          <div class="personalHead__info">
            <ul>
              <li>
                <span class="item"><img src="@/assets/template/images/icon_birthday.svg" alt="" /></span>
                {{ info.label_birth_date }}
              </li>
              <li>
                <span class="item"><img src="@/assets/template/images/icon_home01.svg" alt="" /></span>
                {{ info.prefecture_name }}
              </li>
              <li>
                <span class="item"><img src="@/assets/template/images/icon_graduation.svg" alt="" /></span>
                {{ info.university_name }} {{ info.label_graduation_year }}
              </li>
              <li>個人コード：{{ info.person_cd }}</li>
            </ul>
          </div>
        </div>
        <!-- end Head Details -->
        <!-- start Tabs -->
        <div class="personalTabs">
          <ul class="nav navTabs navTabs--personal width-tabs">
            <li>
              <a id="h2_a1" role="tab" @click="goTab('1')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_basicInformation.svg')" alt="" />
                  </span>
                  基本情報
                </div>
              </a>
            </li>
            <li>
              <a id="h2_a2" role="tab" @click="goTab('2')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_workHistory.svg')" alt="" />
                  </span>
                  勤務先履歴
                </div>
              </a>
            </li>
            <li>
              <a id="h2_a3" role="tab" @click="goTab('3')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_workInformation.svg')" alt="" />
                  </span>
                  フェーズ進捗
                </div>
              </a>
            </li>
            <li>
              <a id="h2_a4" role="tab" @click="goTab('4')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_notes.svg')" alt="" />
                  </span>
                  注意事項
                </div>
              </a>
            </li>
            <li>
              <a id="h2_a5" role="tab" @click="goTab('5')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_timeline.svg')" alt="" />
                  </span>
                  タイムライン
                </div>
              </a>
            </li>
            <li>
              <a id="h2_a6" role="tab" @click="goTab('6')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_networkSearch.svg')" alt="" />
                  </span>
                  ネットワーク検索
                </div>
              </a>
            </li>
          </ul>
          <div class="tab-content z-index-child">
            <div v-if="changetab === '1'" id="basicInformation" role="tabpanel" :class="['tab-pane', getActive('1')]">
              <S02PersonalDetailsBasicInformation :changetab="changetab" @changeLoading="changeLoading" />
            </div>
            <div v-if="changetab === '2'" id="workHistory" role="tabpanel" :class="['tab-pane', getActive('2')]">
              <S03PersonalDetailsWorkingHistory :changetab="changetab" @changeLoading="changeLoading" />
            </div>
            <div v-if="changetab === '3'" id="workInformation" role="tabpanel" :class="['tab-pane', getActive('3')]">
              <S04PersonalDetailsWorkingInformation :changetab="changetab" @changeLoading="changeLoading" />
            </div>
            <div v-if="changetab === '4'" id="notes" role="tabpanel" :class="['tab-pane', getActive('4')]">
              <S05PersonalDetailsNotes
                :personname="info.person_name"
                :dropdown-filter="classActive['class4']"
                :changetab="changetab"
                @changeLoading="changeLoading"
              />
            </div>
            <div v-if="changetab === '5'" id="timeline" role="tabpanel" :class="['tab-pane', getActive('5')]">
              <S06PersonalDetailsTimeline :dropdown-filter="classActive['class5']" :changetab="changetab" @changeLoading="changeLoading" />
            </div>
            <div v-if="changetab === '6'" id="networkSearch" role="tabpanel" :class="['tab-pane', getActive('6')]">
              <S07PersonalDetailsNetworkSearch :changetab="changetab" :dropdown-filter="classActive['class6']" @changeLoading="changeLoading" />
            </div>
          </div>
        </div>
        <!-- end Tabs -->
      </div>
    </div>
    <!-- Modal  Delete Confirm -->
    <el-dialog
      v-model="modalConfig1.isShowModal"
      :custom-class="modalConfig1.customClass"
      :title="modalConfig1.title"
      :width="modalConfig1.width"
      :destroy-on-close="modalConfig1.destroyOnClose"
      :close-on-click-modal="modalConfig1.closeOnClickMark"
      :show-close="false"
      @close="closeModalHeader()"
    >
      <template #default>
        <component
          :is="modalConfig1.component"
          v-bind="{ ...modalConfig1.props }"
          @onFinishScreen="onCloseModal"
          @handleConfirm="onCloseModal"
          @handleYes="resetInfor"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import S02PersonalDetailsBasicInformation from '../H02_S02_PersonalDetailsBasicInformation/_H02_S02_PersonalDetailsBasicInformation.vue';
import S03PersonalDetailsWorkingHistory from '../H02_S03_PersonalDetailsWorkingHistory/_H02_S03_PersonalDetailsWorkingHistory.vue';
import S04PersonalDetailsWorkingInformation from '../H02_S04_PersonalDetailsWorkingInformation/H02_S04_PersonalDetailsWorkingInformation.vue';
import S06PersonalDetailsTimeline from '../H02_S06_PersonalDetailsTimeline/H02_S06_PersonalDetailsTimeline.vue';
import S05PersonalDetailsNotes from '../H02_S05_PersonalDetailsNotes/H02_S05_PersonalDetailsNotes.vue';
import S07PersonalDetailsNetworkSearch from '../H02_S07_PersonalDetailsNetworkSearch/_H02_S07_PersonalDetailsNetworkSearch.vue';
import H02_S02_PersonalDetailsBasicInformation from '../../../api/H02/H02_S02_PersonalDetailsBasicInformation';
import { markRaw } from 'vue';

export default {
  name: '_H02_PersonalDetails',
  components: {
    S02PersonalDetailsBasicInformation,
    S03PersonalDetailsWorkingHistory,
    S04PersonalDetailsWorkingInformation,
    S05PersonalDetailsNotes,
    S06PersonalDetailsTimeline,
    S07PersonalDetailsNetworkSearch
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      loading: false,
      changetab: '1',
      personname: '',
      info: {},
      person_cd: '',
      dropdownFilter: 'dropdown-menu dropdown-filter',
      classActive: {
        tab1: 'tab-pane',
        class1: '',
        tab2: 'tab-pane',
        class2: '',
        tab3: 'tab-pane',
        class3: '',
        tab4: 'tab-pane',
        class4: '',
        tab5: 'tab-pane',
        class5: '',
        tab6: 'tab-pane',
        class6: ''
      },
      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      },
      modalConfig1: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },
  watch: {
    // $route(value) {
    //   if (value.query && value.query.tab) {
    //     this.changetab = value.query.tab;
    //     this.reloadActiveTab(value.query.tab);
    //     for (let i = 0; i < 6; i++) {
    //       // eslint-disable-next-line eqeqeq
    //       if (i + 1 == this.changetab) {
    //         this.classActive[`class${i + 1}`] = this.dropdownFilter;
    //       } else {
    //         this.classActive[`class${i + 1}`] = '';
    //       }
    //     }
    //     this.resetSessionData('', false);
    //   }
    // }
  },

  mounted() {
    this.person_cd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd : '';

    this.emitter.emit('change-header', {
      title: '個人詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.emitter.on('changeTabH02', ({ personCd, goTab }) => {
      this.person_cd = personCd ? personCd : this.person_cd;
      this.reloadActiveTab(goTab);
      this.activeTab(goTab);
      this.getList();
      // this.goTab(goTab);
    });

    setTimeout(() => {
      this.activeTab();
      this.getList();
    });
    for (let i = 0; i < 6; i++) {
      // eslint-disable-next-line eqeqeq
      if (i + 1 == this.$route.query.tab) {
        this.classActive[`class${i + 1}`] = this.dropdownFilter;
      } else {
        this.classActive[`class${i + 1}`] = '';
      }
    }
    this.resetSessionData('', false);
  },

  methods: {
    changeLoading(value) {
      this.loading = value;
    },
    closeModalHeader() {
      this.changeFalseClassHeader();
    },
    resetSessionData(swicth, isSave) {
      if (isSave) {
        sessionStorage.setItem('H02S05', swicth);
      } else {
        sessionStorage.removeItem('H02S05');
      }
    },
    getActive(index) {
      // eslint-disable-next-line eqeqeq
      return this.changetab == index ? 'active' : '';
    },
    getList() {
      const data = {
        person_cd: this.person_cd ? this.person_cd : localStorage.getItem('person_cd_prev')
      };
      H02_S02_PersonalDetailsBasicInformation.getInformation(data)
        .then((res) => {
          if (res) {
            localStorage.setItem('person_cd_prev', res.data.data.person_cd);
            this.info = res.data.data;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },
    checkDateWasEdit(filed) {
      const data = sessionStorage.getItem(filed);
      return data != null ? true : false;
    },
    reloadActiveTab(goTab) {
      const activeTab = document.querySelectorAll('.navTabs--personal li a');
      activeTab.forEach((s) => {
        // eslint-disable-next-line eqeqeq
        if (s.id == `h2_a${goTab}`) {
          s.classList.add('active');
        } else {
          s.classList.remove('active');
        }
      });
    },
    modalConfirmMove(callBack) {
      this.modalConfig1 = {
        ...this.modalConfig1,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { mode: 1, textCancel: 'いいえ', params: { callBack } }
      };
    },
    goTab(index) {
      if (this.checkDateWasEdit('H02S05')) {
        this.modalConfirmMove(() => {
          this.changetab = index;
          this.reloadActiveTab(index);
          this.$router.push({
            name: 'H02_PersonalDetails',
            params: {
              person_cd: this.person_cd
            },
            query: {
              tab: index
            }
          });
          sessionStorage.removeItem('H02S05');
        });
      } else {
        this.changetab = index;
        this.reloadActiveTab(index);
        this.$router.push({
          name: 'H02_PersonalDetails',
          params: {
            person_cd: this.person_cd
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
    resetInfor(e) {
      e.callBack();
      this.onCloseModal();
    },
    onCloseModal() {
      this.modalConfig1 = { ...this.modalConfig1, isShowModal: false };
    },
    activeTab(tab) {
      const tabIndex = tab ? tab : this.$route.query?.tab;
      this.changetab = tabIndex ? tabIndex : '1';
      const active = ' active';
      if (this.changetab > '0' && this.changetab <= '6') {
        const tabActive = document.querySelector('#h2_a' + this.changetab);

        tabActive.classList.add('active');
      } else {
        this.$router.push({ name: 'NotFound' });
      }
      switch (tabIndex) {
        case '1':
          this.classActive.tab1 += active;
          break;
        case '2':
          this.classActive.tab2 += active;
          break;
        case '3':
          this.classActive.tab3 += active;
          break;
        case '4':
          this.classActive.tab4 += active;
          break;
        case '5':
          this.classActive.tab5 += active;
          break;
        case '6':
          this.classActive.tab6 += active;
          break;
        default:
          this.goTab(1);
          break;
      }
    }
  },

  match: {}
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
@media only screen and (max-width: 1024px) and (min-width: 768px) {
  .flex {
    width: 100% !important;
  }
}
.navTabs li a.active {
  background: #fff;
  color: #448add !important;
  font-weight: 700;
  pointer-events: none;
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
              pointer-events: none;
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
          width: 152px !important;
          .navInfo {
            font-size: 11px;
          }
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
        .navItem {
          .svg {
            @media (max-width: $viewport_tablet) {
              width: 15px;
            }
          }
        }
      }
    }
    .width-tabs {
      @media (max-width: $viewport_tablet) {
        width: 100%;
        li {
          width: 115px !important;
          padding: 0px 2px;

          .navInfo {
            padding: 5px 2px !important;
            font-size: 0.65rem;
          }
        }
      }
      li {
        width: 195px;
        a {
          padding: 0px !important;
        }
        .navInfo {
          padding: 10px 8px;
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
</style>
