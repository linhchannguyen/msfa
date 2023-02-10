<template>
  <div class="personalDetails">
    <div class="personal-row scrollbar">
      <div class="personalLeft scrollbar">
        <div class="personalLeft-content">
          <ul v-if="Object.keys(detail).length > 0">
            <li>
              <div class="personalLeft-title">医局名</div>
              <div class="personalLeft-info">
                <p class="medicalOffice">
                  <span class="medicalOffice-label">{{ detail.label_medical_office_name }}</span>
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="edit" @touchstart="edit">
                    <span class="btn-iconLeft">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pen01.svg')" alt="" />
                    </span>
                    編集
                  </button>
                </p>
              </div>
            </li>
            <li>
              <div class="personalLeft-title">開勤区分</div>
              <div class="personalLeft-info">
                <p class="personalLeft-label">
                  {{ detail.label_work_type }} <span v-if="detail.practice_year">（{{ detail.practice_year }}）</span>
                </p>
              </div>
            </li>
            <li>
              <div class="personalLeft-title">医師会</div>
              <div class="personalLeft-info">
                <p class="personalLeft-label">{{ detail.prefecture_name }}</p>
              </div>
            </li>
            <li>
              <div class="personalLeft-title">削除理由</div>
              <div class="personalLeft-info">
                <p class="personalLeft-label">
                  {{ detail.delete_reason_type_label }}
                </p>
              </div>
            </li>
          </ul>
          <div v-if="Object.keys(detail).length < 0 && !isLoadDefault" class="noData">
            <div class="noData-content">
              <p class="noData-text">該当するデータがありません。</p>
              <div class="noData-thumb">
                <img src="@/assets/template/images/data/amico.svg" alt="icon" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="personalRight">
        <div class="personalTabs">
          <div class="headTabs">
            <ul id="transform" class="nav">
              <li>
                <a class="active" data-toggle="tab" href="#medicalSubjects1" role="tab">診療科目</a>
              </li>
              <li>
                <a data-toggle="tab" href="#academicSociety1" role="tab">所属学会</a>
              </li>
              <li>
                <a data-toggle="tab" href="#classification1" role="tab">専門医資格名称／区分</a>
              </li>
            </ul>
          </div>

          <!-- <div class="head-tabs">
            <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
              <el-tab-pane label="診療科目" name="first">
                <div class="medicalGroup">
                  <div class="medicalHead">診療科目名</div>
                  <div class="medicalMain scrollbar listHover">
                    <ul v-if="medical_treatment_subjects.length > 0">
                      <li v-for="item of medical_treatment_subjects" :key="item">{{ item.medical_subjects_name }}</li>
                    </ul>
                    <div v-if="medical_treatment_subjects.length < 0 && !isLoading" class="noData">
                      <div class="noData-content">
                        <p class="noData-text">該当するデータがありません。</p>
                        <div class="noData-thumb">
                          <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </el-tab-pane>
              <el-tab-pane label="所属学会" name="second">
                <div class="medicalGroup">
                  <div class="medicalHead-col">
                    <ul>
                      <li>学会名</li>
                      <li>学会年度</li>
                    </ul>
                  </div>
                  <div class="medicalMain-col scrollbar listHover">
                    <ul v-if="list_academic_societies.length > 0">
                      <li v-for="item of list_academic_societies" :key="item">
                        <div class="societyName">{{ item.society_name }}</div>
                        <div class="academicYear">{{ item.entry_year }}</div>
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
              </el-tab-pane>
              <el-tab-pane label="専門医資格名称／区分" name="third">
                <div class="medicalGroup">
                  <div class="medicalHead-col">
                    <ul>
                      <li>専門医名</li>
                      <li>専門医種別</li>
                    </ul>
                  </div>
                  <div class="medicalMain-col scrollbar listHover">
                    <ul v-if="list_specialist_qualification.length > 0">
                      <li v-for="item of list_specialist_qualification" :key="item">
                        <div class="societyName">{{ item.specialist_name }}</div>
                        <div class="academicYear">{{ item.definition_label }}</div>
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
              </el-tab-pane>
            </el-tabs>
          </div> -->

          <div class="personalMain">
            <div class="tab-content">
              <div id="medicalSubjects1" class="tab-pane active" role="tabpanel">
                <div class="medicalGroup">
                  <div class="medicalHead">診療科目名</div>
                  <div class="medicalMain scrollbar listHover">
                    <ul v-if="medical_treatment_subjects.length > 0">
                      <li v-for="item of medical_treatment_subjects" :key="item">{{ item.medical_subjects_name }}</li>
                    </ul>
                    <div v-else-if="!isLoadDefault" class="noData">
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
              <div id="academicSociety1" class="tab-pane" role="tabpanel">
                <div class="medicalGroup">
                  <div class="medicalHead-col">
                    <ul>
                      <li>学会名</li>
                      <li>学会年度</li>
                    </ul>
                  </div>
                  <div class="medicalMain-col scrollbar listHover">
                    <ul v-if="list_academic_societies.length > 0">
                      <li v-for="item of list_academic_societies" :key="item">
                        <div class="societyName">{{ item.society_name }}</div>
                        <div class="academicYear">{{ item.entry_year }}</div>
                      </li>
                    </ul>
                    <div v-else-if="!isLoadDefault" class="noData">
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
              <div id="classification1" class="tab-pane" role="tabpanel">
                <div class="medicalGroup">
                  <div class="medicalHead-col">
                    <ul>
                      <li>専門医名</li>
                      <li>専門医種別</li>
                    </ul>
                  </div>
                  <div class="medicalMain-col scrollbar listHover">
                    <ul v-if="list_specialist_qualification.length > 0">
                      <li v-for="item of list_specialist_qualification" :key="item">
                        <div class="societyName">{{ item.specialist_name }}</div>
                        <div class="academicYear">{{ item.definition_label }}</div>
                      </li>
                    </ul>

                    <div v-else-if="!isLoadDefault" class="noData">
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
    custom-class="custom-dialog modal-fixed handle-close"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :before-close="handleBeforeClose"
    @close="clodeModal"
  >
    <template #default>
      <component :is="modalConfig.component" ref="modalChild" v-bind="{ ...modalConfig.props }" @onFinishScreen="onFinishScreen"></component>
    </template>
  </el-dialog>
</template>

<script>
import { markRaw } from 'vue';
import H02S02PersonalDetailsBasicInformationModal from './H02_S02_PersonalDetailsBasicInformation_Modal.vue';
import H02_S02_PersonalDetailsBasicInformation from '../../../api/H02/H02_S02_PersonalDetailsBasicInformation';
export default {
  name: '_H02_S02_PersonalDetailsBasicInformation',
  components: {
    H02S02PersonalDetailsBasicInformationModal
  },
  props: {
    personCd: {
      type: String,
      default: ''
    },
    checkLoading: [Boolean]
  },
  data() {
    return {
      activeName: 'first',
      person_cd: '',
      isLoading: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      department: [],
      detail: [],
      list_academic_societies: [],
      list_specialist_qualification: [],
      medical_office_name: [],
      medical_treatment_subjects: [],
      isLoadDefault: true
    };
  },
  watch: {
    changetab: function (value) {
      // eslint-disable-next-line eqeqeq
      if (value == 1) {
        this.getList(true);
      }
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '個人詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    // const first = document.getElementById('tab-first');
    // first.classList.add('is-active');
    // first.setAttribute('');
    setTimeout(() => {
      // active.classList.add('tab-active');
    });
    this.person_cd = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd : '' || this.personCd;
    this.getList(true);
    // this.emitter.on('changeTabH02', ({ personCd }) => {
    //   this.person_cd = personCd;
    //   this.getList();
    // });
    this.transitionAfter('#transform', 'li');
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    clodeModal() {
      this.changeFalseClassHeader();
    },
    getList(isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.$emit('changeLoading', true);
      const data = {
        person_cd: this.person_cd ? this.person_cd : localStorage.getItem('person_cd_prev')
      };
      H02_S02_PersonalDetailsBasicInformation.getBasicInformation(data)
        .then((res) => {
          if (res) {
            this.department = res.data.data.department;
            this.detail = res.data.data.detail;
            this.list_academic_societies = res.data.data.list_academic_societies;
            this.list_specialist_qualification = res.data.data.list_specialist_qualification;
            this.medical_office_name = res.data.data.medical_office_name;
            this.medical_treatment_subjects = res.data.data.medical_treatment_subjects;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.$emit('changeLoading', false);
          this.isLoadDefault = false;
        });
    },
    edit() {
      this.changeTrueClassHeader();
      this.modalConfig = {
        ...this.modalConfig,
        title: '医局名',
        isShowModal: true,
        component: markRaw(H02S02PersonalDetailsBasicInformationModal),
        props: { personCd: this.person_cd, departmentList: this.department, detail: this.detail },
        width: '40rem',
        destroyOnClose: true
      };
    },
    onFinishScreen(index) {
      this.changeFalseClassHeader();
      if (index === 1) {
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      } else {
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
        this.getList(false);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;

.personalDetails {
  height: 100%;
  padding-top: 35px;
  @media (max-width: $viewport_desktop) {
    padding-top: 20px;
  }
  .personal-row {
    display: flex;
    flex-wrap: wrap;
    height: 100%;
    @media (max-width: $viewport_desktop) {
      height: 100%;
    }
    .personalLeft {
      width: 50%;
      padding: 0 24px 20px;
      height: 100%;
      @media (max-width: $viewport_desktop) {
        height: 100%;
        width: 50%;
        .noData-thumb {
          img {
            max-width: 32%;
          }
        }
      }
    }
    .personalRight {
      width: 50%;
      height: 100%;
      background: #f7f7f7;
      box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
      border-radius: 20px 0 0 0;
      @media (max-width: $viewport_desktop) {
        height: 100%;
        width: 50%;
        border-radius: 20px 20px 0 0;
        .noData-thumb {
          img {
            max-width: 32%;
          }
        }
      }
    }
  }
  .personalLeft-content {
    position: relative;
    > ul {
      > li {
        display: flex;
        flex-wrap: wrap;
        margin-top: 24px;
      }
    }
    .personalLeft-title {
      font-size: 1rem;
      width: 92px;
      padding-right: 10px;
    }
    .personalLeft-info {
      width: calc(100% - 92px);
      .medicalOffice {
        display: flex;
        .medicalOffice-label {
          color: #2d3033;
          font-weight: 500;
          padding-right: 88px;
        }
        .btn {
          position: absolute;
          right: 10px;
          min-width: 73px;
          height: 32px;
          padding: 0;
          margin-top: -5px;
          .btn-iconLeft {
            margin: 0;
            .svg {
              width: 16px;
            }
          }
        }
      }
      .personalLeft-label {
        font-size: 1rem;
        color: #2d3033;
      }
    }
  }
  .personalTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    .headTabs {
      padding: 0 24px;
      .nav {
        border-bottom: 1px solid #b7c3cb;
        li {
          width: 33.333333%;
          a {
            padding: 15px 0 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            color: #99a5ae;
            font-size: 1.125rem;
            position: relative;
            line-height: 100%;
            &:hover {
              text-decoration: none;
              color: #5f6b73;
              font-weight: 700;
            }
            &::after {
              position: absolute;
              bottom: -1px;
              left: 0;
              content: '';
              width: 100%;
              height: 3px;
              display: block;
              transition: 400ms all;
            }
            &.active {
              color: #5f6b73;
              font-weight: 700;
            }
          }
        }
      }
    }
    .personalMain {
      height: 100%;
      padding: 20px 24px 28px;
      overflow: hidden;
      .tab-content,
      .tab-pane {
        height: 100%;
      }
    }
  }
  .medicalGroup {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    .medicalHead {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      padding: 16px 24px;
      font-size: 1rem;
      font-weight: 700;
      color: #fff;
    }
    .medicalMain {
      height: 100%;
      @media (max-width: $viewport_desktop) {
        height: 400px;
      }
      ul {
        li {
          padding: 16px 24px;
          box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
          font-size: 1rem;
          background: #fff;
        }
      }
    }
    .medicalHead-col {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          padding: 16px 32px;
          width: 50%;
          color: #fff;
          font-size: 1rem;
          font-weight: 700;
          position: relative;
          &::after {
            position: absolute;
            right: 0;
            top: 8px;
            content: '';
            width: 1px;
            height: calc(100% - 16px);
            background: #fff;
          }
          &:last-child {
            &::after {
              background: none;
            }
          }
        }
      }
    }
    .medicalMain-col {
      height: 100%;
      ul {
        li {
          display: flex;
          flex-wrap: wrap;
          box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
          background: #fff;
          .societyName,
          .academicYear {
            width: 50%;
            padding: 16px 32px;
          }
          .societyName {
            border-right: 1px solid #e3e3e3;
          }
        }
      }
    }
  }
}
/*** modal ***/
.formMedical {
  .formGroup {
    margin-bottom: 20px;
    .formGroup-label {
      margin-bottom: 6px;
    }
    .formGroup-control {
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          width: 50%;
          .btn {
            width: 100%;
          }
          &:nth-child(1) {
            .btn {
              border-radius: 4px 0 0 4px;
            }
          }
          &:nth-child(2) {
            margin-left: -1px;
            .btn {
              border-radius: 0 4px 4px 0;
            }
          }
        }
      }
    }
    .formGroup-controlInfo {
      margin-top: 20px;
    }
  }
  .formGroup-btn {
    display: flex;
    .btn {
      width: 50%;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.head-tabs {
  height: 100%;
}
</style>
