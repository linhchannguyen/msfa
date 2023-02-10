<template>
  <div class="group-basicInfo">
    <div class="basicInfo-row scrollbar">
      <div class="basicInfo-colLeft scrollbar">
        <div v-if="isLoading2" class="basicInfo-group">
          <h3 class="title"><span>正式名称: </span>{{ detail.facility_name }}</h3>
          <div class="hospital">
            <ul>
              <li>
                <div class="hospital-add">
                  <span class="hospital-item">
                    <img src="@/assets/template/images/icon_maps.svg" alt="" />
                  </span>
                  {{ detail.address }}
                </div>
              </li>
              <li>
                <div class="hospital-info">
                  <span class="hospital-txt">(行政地区: {{ detail.prefecture_name }} {{ detail.municipality_name }})</span>
                </div>
              </li>
              <li>
                <div class="hospital-add">
                  <span class="hospital-item">
                    <img src="@/assets/template/images/icon_call.svg" alt="" />
                  </span>
                  {{ detail.phone }}
                </div>
              </li>
            </ul>
          </div>
          <div class="facility">
            <ul>
              <li>
                <div class="facility-title">施設代表者名:</div>
              </li>
              <li>
                <div class="facility-info">
                  <div class="facility-name">
                    <div class="facility-nameLeft">{{ detail.director_name }}</div>
                    <div class="facility-nameRight">
                      <span class="facility-tlt">経営体: </span>
                      <span class="facility-txt">{{ detail.management_body_name }}</span>
                    </div>
                  </div>
                </div>
              </li>
              <li>
                <div class="facility-info">
                  <div class="facility-name">
                    <div class="facility-nameRight">
                      <span class="facility-tlt">病院種別: </span>
                      <span class="facility-txt">{{ detail.infirmary_type_name }}</span>
                    </div>
                  </div>
                </div>
              </li>
              <li>
                <div class="facility-info">
                  <div class="facility-name">
                    <div class="facility-nameRight">
                      <span class="facility-tlt">施設分類: </span>
                      <span class="facility-txt">{{ detail.facility_category_name }}</span>
                    </div>
                  </div>
                </div>
              </li>
              <li>
                <div class="facility-info">
                  <div class="facility-name">
                    <div class="facility-nameRight">
                      <span class="facility-tlt">許可病床数: </span>
                      <span class="facility-txt">{{ detail.total_bed_count }}</span>
                    </div>
                  </div>
                </div>
                <div class="facility-info">
                  <div class="facility-grid">
                    <p class="facility-label">一般：{{ detail.general_bed_count }}</p>
                    <p class="facility-label">結核：{{ detail.tuberculous_bed_count }}</p>
                    <p class="facility-label">精神：{{ detail.mental_bed_count }}</p>
                    <p class="facility-label">感染：{{ detail.infection_bed_count }}</p>
                    <p class="facility-label">その他：{{ detail.other_bed_count }}</p>
                  </div>
                </div>
              </li>
              <li v-if="!showEdit" class="facility-edit">
                <div class="facility-title">診察時間:</div>
                <div class="facility-info" style="flex-grow: 1">
                  <div class="facility-timeExam">
                    <p class="facility-timeExamLabel" style="white-space: break-spaces; word-break: break-word">{{ detail.consultation_hours_note }}</p>
                    <button type="button" class="btn btn--icon" @click="edit(1)">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pen01.svg')" alt="" />
                    </button>
                  </div>
                </div>
              </li>
              <li v-if="showEdit" class="facility-edit">
                <div class="facility-title">診察時間:</div>
                <div class="facility-info" style="flex-grow: 1">
                  <el-input v-model="detail.consultation_hours_note" type="textarea" class="form-control-textarea textareas" @input="changeInput" />
                </div>
              </li>
            </ul>
          </div>
          <div v-if="showEdit" class="btnkeep">
            <button
              style="width: 132px; margin-right: 6px"
              type="button"
              class="btn btn-outline-primary btn-radius btn-outline-primary--cancel"
              :disabled="isloadingBtn"
              @click="closeCreate()"
            >
              <span class="btn-iconLeft">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit02.svg')" alt="" />
              </span>
              キャンセル
            </button>
            <button
              :disabled="isloadingBtn || checkBtn"
              type="button"
              class="btn btn-outline-primary btn-outline-primary--cancel customBtn btn-radius"
              @click="edit(2)"
            >
              <span class="btn-iconLeft">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_save.svg')" alt="" />
              </span>
              <i :class="['el-icon-loading', isloadingBtn ? 'inline-block' : '']"></i> 保存
            </button>
          </div>
        </div>
        <div v-if="!isLoading2 && !isLoading" class="noData">
          <div class="noData-content">
            <p class="noData-text">該当するデータがありません。</p>
            <div class="noData-thumb">
              <img src="@/assets/template/images/data/amico.svg" alt="icon" />
            </div>
          </div>
        </div>
      </div>

      <div class="basicInfo-colRight">
        <div class="basicGroup">
          <div class="basicInfo-tabs">
            <ul id="transform" class="nav">
              <li>
                <a class="active" data-toggle="tab" href="#facilityChargeHistory" role="tab">施設担当履歴</a>
              </li>
              <li>
                <a data-toggle="tab" href="#medicalSubjects" role="tab">診療科目</a>
              </li>
              <li>
                <a data-toggle="tab" href="#relatedFacilities" role="tab">関連施設</a>
              </li>
              <li>
                <a data-toggle="tab" href="#parentFacility" role="tab">親施設</a>
              </li>
            </ul>
          </div>
          <div class="basicInfo-content">
            <div class="tab-content">
              <div id="facilityChargeHistory" class="tab-pane active" role="tabpanel">
                <div class="chargeHistory">
                  <div class="groupMain">
                    <div class="lstHistory scrollbar">
                      <ul v-if="staff_history.length > 0">
                        <li v-for="item of staff_history" :key="item">
                          <div class="lstHistory-content">
                            <div
                              style="cursor: pointer"
                              class="lstHistory-thumb"
                              @click="accountSetting(item.user_cd)"
                              @touchstart="accountSetting(item.user_cd)"
                            >
                              <img style="width: 100%" class="lstHistory-icon" :src="item.file_url" alt="" />
                            </div>
                            <div class="lstHistory-info">
                              <span
                                style="color: #448add; font-weight: 700; cursor: pointer; margin-right: 12px"
                                @click="accountSetting(item.user_cd)"
                                @touchstart="accountSetting(item.user_cd)"
                                >{{ item.user_name }}</span
                              >
                            </div>
                            <div v-if="item.main_flg === 1" class="lstHistory-info-custom">
                              <span href="#">主担当</span>
                            </div>
                          </div>
                          <div class="lstHistory-dateTime">
                            {{ formatFullDate(item.start_date) }} ～ {{ item.end_date === '9999-12-31' ? '' : formatFullDate(item.end_date) }}
                          </div>
                        </li>
                      </ul>
                      <div v-if="staff_history.length === 0 && !isLoading" class="noData">
                        <div class="noData-content">
                          <p class="noData-text">該当するデータがありません。</p>
                          <div class="noData-thumb">
                            <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="medicalSubjects" class="tab-pane" role="tabpanel">
                <div class="chargeHistory">
                  <div class="groupMain">
                    <div class="lstMedical scrollbar">
                      <ul v-if="medical_subjects.length > 0">
                        <li v-for="item of medical_subjects" :key="item">{{ item.medical_subjects_name }}</li>
                      </ul>
                      <div v-else class="noData">
                        <div class="noData-content">
                          <p class="noData-text">該当するデータがありません。</p>
                          <div class="noData-thumb">
                            <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="relatedFacilities" class="tab-pane" role="tabpanel">
                <div class="chargeHistory">
                  <div class="groupMain">
                    <div class="lstMedical scrollbar">
                      <ul v-if="related.length > 0">
                        <li v-for="item of related" :key="item">
                          <span
                            style="color: #448add; font-weight: 700; cursor: pointer"
                            @click="detailRelated(item.relation_facility_cd)"
                            @touchstart="detailRelated(item.relation_facility_cd)"
                            >{{ item.facility_short_name }}</span
                          >
                          <span class="span-label-purple">{{ item.definition_label }}</span>
                        </li>
                      </ul>
                      <div v-else class="noData">
                        <div class="noData-content">
                          <p class="noData-text">該当するデータがありません。</p>
                          <div class="noData-thumb">
                            <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="parentFacility" class="tab-pane" role="tabpanel">
                <div class="chargeHistory">
                  <div class="groupMain">
                    <div class="lstMedical scrollbar">
                      <ul v-if="parent.length > 0">
                        <li v-for="item of parent" :key="item">
                          <span
                            style="color: #448add; font-weight: 700; cursor: pointer"
                            @click="detailParent(item.facility_cd)"
                            @touchstart="detailParent(item.facility_cd)"
                            >{{ item.facility_short_name }}</span
                          >
                        </li>
                      </ul>
                      <div v-else class="noData">
                        <div class="noData-content">
                          <p class="noData-text">該当するデータがありません。</p>
                          <div class="noData-thumb">
                            <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
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
  </div>
</template>

<script>
import H01_S02_FacilityDetailsBasicInformation from '../../../api/H01/H01_S02_FacilityDetailsBasicInformation';
export default {
  name: 'H01_S02_FacilityDetailsBasicInformation',
  components: {},
  props: {
    in_facility_cd: { type: String },
    checkLoading: [Boolean]
  },

  data() {
    return {
      facility_cd: '',
      isLoading: false,
      isLoading2: false,
      consultation_time: [],
      detail: {},
      medical_subjects: [],
      parent: [],
      related: [],
      staff_history: [],
      showEdit: false,
      isloadingBtn: false,
      checkBtn: true,
      modalConfig: {
        title: '',
        customClass: 'custom-dialog z05-s01',
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
  watch: {
    $route() {
      this.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';
      if (this.facility_cd) {
        this.getList();
      }
    }
  },
  // created() {
  //   const checkRouter = this.facility_cd;
  //   const { facility_cd } = this.$route.params;
  //   if (checkRouter !== facility_cd || this.in_facility_cd) {
  //     this.facility_cd = facility_cd || this.in_facility_cd;
  //     this.getList();
  //   }
  // },
  mounted() {
    this.emitter.emit('change-header', {
      title: '施設詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '' || this.in_facility_cd;
    this.getList();
    this.transitionAfter('#transform', 'li');
  },
  methods: {
    closeCreate() {
      if (this.checkBtn) {
        this.showEdit = !this.showEdit;
        // this.getList();
      } else {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          title: null,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false
        };
      }
    },
    handleYes() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      this.showEdit = !this.showEdit;
      this.checkBtn = true;
      this.getList();
    },
    changeInput() {
      if (this.detail.consultation_hours_note === '' || this.detail.consultation_hours_note === null) {
        this.checkBtn = true;
      } else {
        this.checkBtn = false;
      }
    },
    onResultModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    edit(e) {
      if (e === 1) {
        this.showEdit = !this.showEdit;
        if (this.detail.consultation_hours_note === '' || this.detail.consultation_hours_note === null) {
          this.checkBtn = true;
        }
      } else {
        this.isloadingBtn = true;
        const data = {
          facility_cd: this.detail.facility_cd || localStorage.getItem('facility_cd_prev'),
          consultation_hours_note: this.detail.consultation_hours_note
        };
        H01_S02_FacilityDetailsBasicInformation.saveList(data)
          .then(() => {
            this.$notify({ message: '正常に更新しました。', customClass: 'success' });
            this.showEdit = !this.showEdit;
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {
            this.isloadingBtn = false;
            this.checkBtn = true;
          });
      }
    },
    accountSetting(user_cd) {
      this.$router.push({ name: 'Z04_S01_AccountSettings', params: { user_cd: user_cd } });
    },
    detailRelated(facility_cd) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: { facility_cd },
        query: { tab: 1 }
      });
    },
    detailParent(facility_cd) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: { facility_cd },
        query: { tab: 1 }
      });
    },
    getList() {
      this.isLoading = true;
      this.$emit('changeLoading', true);
      this.isLoading2 = false;
      const data = {
        facility_cd: this.facility_cd || localStorage.getItem('facility_cd_prev')
      };
      H01_S02_FacilityDetailsBasicInformation.getList(data)
        .then(async (res) => {
          this.detail = res.data.data.detail;
          this.information = res.data.data.information;
          this.medical_subjects = res.data.data.medical_subjects;
          this.parent = res.data.data.parent;
          this.related = res.data.data.related;
          this.staff_history = res.data.data.staff_history;

          let listImg = res.data.data.staff_history.map((item) =>
            fetch(item.file_url).then((resp) => {
              // eslint-disable-next-line eqeqeq
              if (resp.ok && resp.status == 200) {
                return {
                  ...item,
                  file_url: item.file_url || this.avataDefault(),
                  res: { ...resp }
                };
              } else {
                return {
                  ...item,
                  file_url: this.avataDefault(),
                  res: { ...resp }
                };
              }
            })
          );
          await Promise.all(listImg).then(async (res) => {
            let data = await res;
            this.staff_history = data;
          });

          this.isLoading2 = true;
        })
        .finally(() => {
          this.isLoading = false;
          this.$emit('changeLoading', false);
        });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
/** start css layout  **/
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 6%;
    top: 30%;
  }
}
.el-icon-loading {
  display: none;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.textareas {
  width: 100%;
  min-height: 80px;
  border-radius: 4px;
  background: rgb(255, 255, 255);
  &:focus-visible {
    outline: none;
  }
}
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
        min-width: 263px;
        @media (max-width: $viewport_desktop) {
          min-width: 200px;
        }
        @media (max-width: $viewport_tablet) {
          min-width: inherit;
          width: 25%;
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
      z-index: 1;
      overflow: hidden;
      .tab-pane {
        height: 100%;
      }
    }
  }
}
/** end css layout **/
/** H01_S02_FacilityDetailsBasicInformation **/
.group-basicInfo {
  height: 100%;
  padding-top: 20px;
}
.basicInfo-row {
  display: flex;
  flex-wrap: wrap;
  height: 100%;
  overflow: hidden;
  @media (max-width: $viewport_tablet) {
    overflow: auto;
  }
  .basicInfo-colLeft {
    width: 50%;
    padding: 0 24px 24px;
    height: 100%;
    @media (max-width: $viewport_desktop) {
      padding: 0 16px 24px;
    }
    @media (max-width: $viewport_tablet) {
      width: 100%;
      height: initial;
    }
  }
  .basicInfo-colRight {
    width: 50%;
    background: #f7f7f7;
    box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
    border-radius: 20px 0 0 0;
    height: 100%;
    @media (max-width: $viewport_tablet) {
      width: 100%;
      margin: 0 16px;
      border-radius: 20px 20px 0 0;
      height: initial;
    }
  }
  .basicInfo-group {
    .title {
      padding-top: 23px;
      font-size: 18px;
      font-weight: 700;

      span {
        color: #2d3033;
      }
    }
    .hospital {
      padding: 17px 0 24px;
      border-bottom: 1px solid #b7c3cb;
      ul {
        li {
          margin-top: 7px;
          font-size: 1rem;
        }
      }
      .hospital-add {
        padding-right: 12px;
        display: flex;
        color: #2d3033;
        .hospital-item {
          min-width: 15px;
          margin: -2px 6px 0 0;
          img {
            max-width: 15px;
          }
        }
      }
      .hospital-info {
        display: flex;
        flex-wrap: wrap;
        .hospital-tlt {
          width: 90px;
        }
        .hospital-txt {
          padding-left: 21px;
          color: #2d3033;
          font-weight: 700;
        }
      }
    }
    .facility {
      padding-top: 4px;
      > ul {
        > li {
          margin-top: 8px;
          font-size: 16px;
          &.facility-edit {
            display: flex;
          }
        }
      }
      .facility-title {
        min-width: 85px;
        padding-right: 12px;
      }
      .facility-info {
        .facility-name {
          display: flex;
          flex-wrap: wrap;
          .facility-nameLeft {
            color: #2d3033;
          }
          .facility-nameRight {
            .facility-tlt {
              padding-right: 12px;
            }
            .facility-txt {
              color: #2d3033;
            }
          }
        }
      }
      .facility-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        color: #2d3033;

        &::before {
          content: '(';
        }
        &::after {
          content: ')';
        }
      }
      .facility-time {
        padding: 10px 10px 10px 16px;
        border: 1px solid #727f88;
        border-radius: 4px;
        font-size: 14px;
      }
      .facility-timeExam {
        position: relative;
        .facility-timeExamLabel {
          color: #2d3033;
          span {
            padding-right: 20px;
            &:last-child {
              padding-right: 0;
            }
          }
        }
        .btn {
          position: absolute;
          top: 30px;
          left: -70px;
        }
      }
    }
  }
  .btnkeep {
    margin-top: 24px;
    text-align: right;
    .btn {
      width: 100px;
      height: 32px;
      padding: 0;
      .svg {
        width: 13px;
      }
    }
  }
  .basicGroup {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    .basicInfo-tabs {
      padding: 0 24px;
      @media (max-width: $viewport_desktop) {
        padding: 0 16px;
      }
      .nav {
        border-bottom: 1px solid #b7c3cb;
        li {
          width: 25%;
          a {
            padding: 15px 0 13px;
            display: block;
            text-align: center;
            color: #99a5ae;
            font-size: 1rem;
            position: relative;
            &:hover {
              text-decoration: none;
              color: #5f6b73;
              font-weight: 700;
              &::after {
                background: #448add;
              }
            }
            &.active {
              color: #5f6b73;
              font-weight: 700;
              &::after {
                background: #448add;
              }
            }
          }
        }
      }
    }
    .basicInfo-content {
      height: 100%;
      overflow: hidden;
      .tab-content {
        background: none;
        box-shadow: none;
      }
      .chargeHistory {
        padding: 12px 0 20px;
        height: 100%;
      }
      .groupMain {
        height: 100%;
        margin: 0 24px;
        background: #fff;
        border-radius: 4px;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        @media (max-width: $viewport_desktop) {
          margin: 0 16px;
        }
        .lstHistory {
          height: 100%;
          width: calc(100% + 48px);
          margin-left: -24px;
          @media (max-width: $viewport_desktop) {
            width: calc(100% + 32px);
            margin-left: -16px;
          }
          @media (max-width: $viewport_tablet) {
            height: 400px;
          }
          ul {
            padding: 0 24px;
            @media (max-width: $viewport_desktop) {
              padding: 0 16px;
            }
            li {
              padding: 16px 24px;
              display: flex;
              justify-content: space-between;
              align-items: center;
              border-bottom: 1px solid #e3e3e3;
              &:last-child {
                border-bottom: none;
              }
            }
          }
          .lstHistory-content {
            padding-right: 10px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            .lstHistory-thumb {
              width: 32px;
              height: 32px;
              border-radius: 50%;
              overflow: hidden;
              display: flex;
              align-items: center;
              justify-content: center;
              .lstHistory-icon {
                width: 17px;
              }
            }
            .lstHistory-info-custom {
              background: #e2e4ff;
              color: #555fb0;
              border-radius: 20px;
              padding: 1px 16px;
              a {
                color: #555fb0;
              }
            }
            .lstHistory-info {
              padding-left: 10px;
              a {
                font-size: 1rem;
                font-weight: 700;
                margin-right: 10px;
              }
              .span-label-purple {
                background: #e2e4ff;
                border-radius: 20px;
                padding: 1px 16px;
                color: #555fb0;
                font-weight: 500;
                font-size: 0.875rem;
                display: inline-block;
              }
            }
          }
          .lstHistory-dateTime {
            font-size: 1rem;
            text-align: right;
          }
        }
        .lstMedical {
          height: 100%;
          width: calc(100% + 48px);
          margin-left: -24px;
          @media (max-width: $viewport_desktop) {
            width: calc(100% + 32px);
            margin-left: -16px;
          }
          @media (max-width: $viewport_tablet) {
            height: 400px;
          }
          ul {
            padding: 0 24px;
            @media (max-width: $viewport_desktop) {
              padding: 0 16px;
            }
            li {
              padding: 16px 24px;
              font-size: 1rem;
              border-bottom: 1px solid #e3e3e3;
              display: flex;
              justify-content: space-between;
              a {
                margin-right: 10px;
              }
              .span-label-purple {
                background: #e2e4ff;
                border-radius: 20px;
                padding: 1px 16px;
                color: #555fb0;
                font-weight: 500;
                font-size: 0.875rem;
                display: inline-block;
              }
            }
          }
        }
      }
    }
  }
}
</style>
