<template>
  <div v-loading="loadingPage" v-load-f5="true" class="wrapContent">
    <div class="groupLayout">
      <div class="group-head">
        <div class="group-btn">
          <button
            type="button"
            class="btn btn-outline-primary btn-radius btn-custom"
            @click="isEditElement ? openConfirmEventSaveData('addNew') : handleAddPersonGroup()"
          >
            <span class="btn-iconLeft">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" />
            </span>
            新規作成
          </button>
          <button
            type="button"
            class="btn btn-outline-primary btn-radius btn-custom"
            @click="isEditElement ? openConfirmEventSaveData('copy') : openModalZ03S04()"
          >
            <span class="btn-iconLeft">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_copy.svg')" alt="" />
            </span>
            コピー
          </button>
        </div>
      </div>
      <div class="group-content">
        <div class="colSlider">
          <div class="colSlider-btn">
            <button ref="btnScrollTop" type="button" class="btn btn-previous" :disabled="disableScrollTopBtn()" @click="scrollToTop()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line.svg')" alt="" />
            </button>
            <button ref="btnScrollBottom" type="button" class="btn btn-next" :disabled="disableScrollBottomBtn()" @click="scrollToBottom()">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-line.svg')" alt="" />
            </button>
          </div>
          <div id="scrollTab" class="colSlider-tabs" @scroll="getElementInScreen">
            <ul class="nav navTabs-sider">
              <li v-if="positionScroll === 'top'" class="sticky sticky-top">
                <a class="tab-item active">
                  <span class="column-drag-handle left" />
                  <div class="right active" href="#tabs-content" data-toggle="tab" role="tab">
                    {{ personGroupSelected?.select_person_group_id ? personGroupSelected?.select_person_group_name : 'グループ名' }}
                  </div>
                </a>
              </li>
              <li v-if="positionScroll === 'bottom'" class="sticky sticky-bottom">
                <a class="tab-item active">
                  <span class="column-drag-handle left" />
                  <div class="right active" href="#tabs-content" data-toggle="tab" role="tab">
                    {{ personGroupSelected?.select_person_group_id ? personGroupSelected?.select_person_group_name : 'グループ名' }}
                  </div>
                </a>
              </li>
              <Container
                v-if="lstPersonGroups.length > 0"
                drag-handle-selector=".column-drag-handle"
                lock-axis="y"
                behaviour="contain"
                :should-accept-drop="shouldAcceptDrop"
                :get-child-payload="getChildPayload"
                @drop="onDrop"
                @drag-start="onDragStart"
              >
                <Draggable
                  v-for="personGroup in lstPersonGroups"
                  :id="`id_${personGroup.select_person_group_id}`"
                  :key="personGroup.select_person_group_id"
                  :drag-not-allowed="false"
                  :class="personGroup.selected ? 'shadow' : ''"
                >
                  <li>
                    <a class="tab-item" :class="personGroup.selected ? 'active' : ''">
                      <span class="column-drag-handle left" />
                      <div
                        class="right active"
                        data-toggle="tab"
                        role="tab"
                        @click="isEditElement ? openConfirmEventSaveData('changeRecord', personGroup) : selectedPersonGroup(personGroup)"
                      >
                        {{ personGroup.select_person_group_id ? personGroup.select_person_group_name : 'グループ名' }}
                      </div>
                    </a>
                  </li>
                </Draggable>
              </Container>
            </ul>
            <!-- Tab panes -->
          </div>
        </div>
        <div class="colContent">
          <div class="tab-content">
            <div id="tab-content" class="tab-pane active" role="tabpanel">
              <div v-if="checkPersonGroupSelected() && !isEditMode" class="contentBody">
                <div class="contentBody-info">
                  <div class="title">
                    <h3 class="tlt">{{ personGroupSelected.select_person_group_name }}</h3>
                  </div>
                  <div class="bodyContainer">
                    <div class="bodyContainer-title"><h4 class="tlt">施設個人</h4></div>
                    <div ref="refLstFacilityPerson" class="bodyContainer-main scrollbar">
                      <template v-for="facilityCategory in personGroupSelected.children" :key="facilityCategory.facility_category_type">
                        <div v-if="facilityCategory.children.length > 0" class="facility">
                          <div class="facility-head">
                            <div class="facility-head__info">
                              <div class="facility-title">{{ facilityCategory.facility_category_name }}</div>
                              <div class="facility-label">
                                <ul>
                                  <li v-if="facilityCategory.content_name">
                                    <span class="facility-tlt">面談内容</span>
                                    <span class="facility-text">{{ facilityCategory.content_name }}</span>
                                  </li>
                                  <li v-if="facilityCategory.product_name" class="w-calc">
                                    <span class="facility-tlt">品目 </span>
                                    <span class="facility-text">{{ facilityCategory.product_name }}</span>
                                  </li>
                                  <li v-if="facilityCategory.document_name" class="w-100">
                                    <span class="facility-tlt">資材 </span>
                                    <span class="facility-text">{{ facilityCategory.document_name }}</span>
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                          <div class="facility-content">
                            <div class="facility-list">
                              <ul>
                                <li v-for="item in facilityCategory.children" :key="item">
                                  <div class="facility-list-info">
                                    <span class="facility-list-label">{{ item.facility_short_name }}</span>
                                    <span class="facility-list-label">{{ item.department_name }}</span>
                                    <button class="facility-list-link person-text-nomal log-icon" title_log="個人名" @click="redirectToH02S02(item)">
                                      {{ item.person_name }}
                                    </button>
                                    <span class="facility-list-label">{{ item.position_name }}</span>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </div>
                <div class="contentBody-btn">
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmEventDelete">削除</button>
                  <button type="button" class="btn btn-primary" @click="openEditMode()">編集</button>
                </div>
              </div>

              <div v-if="checkPersonGroupSelected() && isEditMode" class="contentBody">
                <div class="contentBody-info">
                  <div class="groupName">
                    <div class="form-icon icon-right" :class="checkValidateGroupName() && isSubmit ? 'hasErr' : ''">
                      <span class="icon icon--cursor icon-custom icon-close" @click="clearGroupName" @touchstart="clearGroupName">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                      </span>
                      <el-input
                        ref="inputNamePerson"
                        v-model="modelUpdate.select_person_group_name"
                        maxlength="51"
                        placeholder="グループ名入力"
                        class="form-control-input custom-input"
                        @change="compareObjectChange()"
                        @blur="compareObjectChange()"
                      />
                    </div>
                    <span v-if="checkValidateGroupName() && isSubmit" class="invalid">{{ validationGroupName() }}</span>
                  </div>
                  <div class="bodyContainer">
                    <div class="bodyContainer-title">
                      <h4 class="tlt">施設個人</h4>
                      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="openModalZ05S04()">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" />
                        </span>
                        追加
                      </button>
                    </div>
                    <div ref="refLstFacilityPersonEdit" class="bodyContainer-main scrollbar">
                      <template v-for="facilityCategory in modelUpdate.children" :key="facilityCategory.facility_category_type">
                        <div v-if="facilityCategory.children.length > 0" class="facility">
                          <div class="facility-head">
                            <div class="facility-head__info">
                              <div class="facility-title">{{ facilityCategory.facility_category_name }}</div>
                              <div class="facility-label">
                                <ul>
                                  <li v-if="facilityCategory.content_name">
                                    <span class="facility-tlt">面談内容 </span>
                                    <span class="facility-text">{{ facilityCategory.content_name }}</span>
                                  </li>
                                  <li v-if="facilityCategory.product_name" class="w-calc">
                                    <span class="facility-tlt">品目 </span>
                                    <span class="facility-text">{{ facilityCategory.product_name }}</span>
                                  </li>
                                  <li v-if="facilityCategory.document_name" class="w-100">
                                    <span class="facility-tlt">資材 </span>
                                    <span class="facility-text">{{ facilityCategory.document_name }}</span>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div class="facility-head-btn">
                              <button
                                type="button"
                                class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                                @click="openModalZ03S03(facilityCategory)"
                              >
                                <span class="btn-iconLeft">
                                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_setting-nav.svg')" alt="" />
                                </span>
                                面談内容設定
                              </button>
                            </div>
                          </div>
                          <div class="facility-content">
                            <div class="facility-list">
                              <ul>
                                <li v-for="item in facilityCategory.children" :key="item.person_cd">
                                  <div class="facility-list-info">
                                    <span class="facility-list-label">{{ item.facility_short_name }}</span>
                                    <span class="facility-list-label">{{ item.department_name }}</span>
                                    <a
                                      class="facility-list-link person-text-nomal log-icon"
                                      title_log="個人名"
                                      @click="isEditElement ? openConfirmEventSaveData('redirectToH02S02', item) : redirectToH02S02(item)"
                                      >{{ item.person_name }}</a
                                    >
                                    <span class="facility-list-label">{{ item.position_name }}</span>
                                  </div>
                                  <button type="button" class="btn btn--icon" @click="onRemovePerson(facilityCategory, item)">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                                  </button>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </div>
                <div class="contentBody-btn">
                  <button
                    type="button"
                    class="btn btn-outline-primary btn-outline-primary--cancel"
                    :disabled="processingSave"
                    @click="isEditElement ? openConfirmEventSaveData('edit') : cancelEventSave()"
                  >
                    キャンセル
                  </button>
                  <el-button type="primary" class="btn btn-primary" :disabled="processingSave" @click.prevent="updatePersonGroup()"> 保存 </el-button>
                </div>
              </div>

              <div v-if="!checkPersonGroupSelected() && !loadingPage" class="no-data">
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
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`${modalConfig.customClass} handle-close`"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          ref="modalChild"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="deletePersonGroup"
          @handleYes="cancelEventSave"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import _ from 'lodash';
import { markRaw } from 'vue';
import { Container, Draggable } from 'vue-dndrop';
import { applyDrag } from '@/utils/dragitem.js';
import Z03_S02_SelectFacilityPersonalSettingService from '@/api/Z03/Z03_S02_SelectFacilityPersonalSettingService';
import Z03S04SelectListCopy from '@/views/Z03/Z03_S04_SelectListCopy/Z03_S04_SelectListCopy';
import Z03_S03_ConsultationContentSetting from '@/views/Z03/Z03_S03_ConsultationContentSetting/Z03_S03_ConsultationContentSetting';
import Z05S04FacilityCustomerSelection from '@/views/Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection';

export default {
  name: 'Z03_S02_SelectFacilityPersonalSetting',
  components: { Container, Draggable, Z03S04SelectListCopy, Z05S04FacilityCustomerSelection, Z03_S03_ConsultationContentSetting },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      loadingPage: false,
      isEditMode: false,
      paramZ05S04: {
        non_facility_flag: 0, // req
        non_medical_flag: 1, //req
        user_cd: '',
        user_name: '',
        target_product_cd: '',
        facility_category_type: '',
        prefecture_cd: '',
        prefecture_name: '',
        municipality_cd: '',
        municipality_name: '',
        facility_cd: [],
        facility_name: [],
        person_cd: []
      },

      isEditElement: false,
      isNewChange: false,
      processingSave: false,

      lstPersonGroups: [],
      lstPersonGroupsOld: [],
      lstPersonGroupsChange: [],
      personGroupSelected: {},
      lstPersonGroupVisible: [],
      positionScroll: '',

      sort_order_lasted: 1,

      type_action: '',

      modelUpdate: {
        select_person_group_id: '',
        select_person_group_name: '',
        sort_order: null,
        children: []
      },

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalConfirm: false,
        component: null,
        props: {},
        width: '',
        customClass: 'custom-dialog',
        destroyOnClose: true,
        isShowClose: true,
        closeOnClickMark: false
      },

      currentUser: null,

      isCheckScroll: true,
      scrollRecordChange: {},
      isAddNew: false,
      isEdited: false,
      isDeleted: false,
      isSubmit: false,
      onScrollTop: 0
    };
  },
  created() {
    this.getPersonGroups(true);
    window.addEventListener('scrollTab', this.getElementInScreen);
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'セレクト施設個人管理',
      icon: 'icon_clipboard-setting_color.svg',
      isShowBack: false
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.Z03_S02_SelectFacilityPersonalSetting || 0;
    this.currentUser = this.getCurrentUser();
  },

  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },

    enCodeData(obj) {
      // eslint-disable-next-line no-undef
      let strData = Buffer.from(JSON.stringify(obj), 'utf8').toString('base64');
      return strData;
    },

    // api
    getPersonGroups(isReload, isModalConfirm) {
      this.loadingPage = true;
      this.lstPersonGroups = [];
      Z03_S02_SelectFacilityPersonalSettingService.getPersonGroups({ isCopy: false })
        .then(async (res) => {
          this.lstPersonGroups = res?.data?.data?.person_group;
          this.lstPersonGroupsOld = res?.data?.data?.person_group;
          this.sort_order_lasted = res?.data?.data?.sort_order;
          let time;
          const timeout = setTimeout(() => {
            if (time) {
              clearTimeout(timeout);
              return;
            }
            this.getElementInScreen();

            if (isReload) {
              const isLoadingComponent = localStorage.getItem('isLoadingComponent');

              const personBefore = this.decodeData(localStorage.getItem('$_PG'));

              this.personGroupSelected = {};
              let length = this.lstPersonGroups.length;
              if (length > 0) {
                this.lstPersonGroups.forEach((x, index) => {
                  x.selected = false;
                  if (isLoadingComponent === 'true') {
                    if (index === 0) {
                      x.selected = true;
                      this.personGroupSelected = { ...x };
                    }
                  } else {
                    if (personBefore) {
                      if (personBefore === x.select_person_group_id) {
                        x.selected = true;
                        this.personGroupSelected = { ...x };

                        localStorage.removeItem('$_PG');
                      }
                    } else {
                      if (index === 0) {
                        x.selected = true;
                        this.personGroupSelected = { ...x };
                      }
                    }
                  }
                });
              }
              this.getElementInScreen();
            } else {
              if (this.type_action === 'addNew' || this.type_action === 'copy') {
                let length = this.lstPersonGroups.length;
                if (length > 0) {
                  this.lstPersonGroups.forEach((x, index) => {
                    x.selected = false;
                    if (index === length - 1) {
                      x.selected = true;
                      this.personGroupSelected = { ...x };
                      this.scrollToView(`id_${x.select_person_group_id}`);
                    }
                  });
                }
              } else {
                this.lstPersonGroups.forEach((x) => {
                  if (x.select_person_group_id === this.personGroupSelected.select_person_group_id) {
                    x.selected = true;
                    this.personGroupSelected = { ...x };
                    if (this.type_action !== 'changeRecord') {
                      this.scrollToView(`id_${x.select_person_group_id}`);
                    }
                  }
                });
                if (this.type_action === 'changeRecord') {
                  this.scrollToView(`id_${this.scrollRecordChange.select_person_group_id}`);
                }
              }
            }

            if (isModalConfirm) {
              this.handleEvent();
            }
            this.type_action = '';
            localStorage.setItem('isLoadingComponent', false);
            time = 1;
          }, 0);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          if (this.$refs.refLstFacilityPerson) {
            if (this.onScrollTop) {
              this.$refs.refLstFacilityPerson.scrollTop = this.onScrollTop;
            } else {
              this.$refs.refLstFacilityPerson.scrollTop = 0;
            }
          }

          this.loadingPage = false;

          this.js_pscroll(0.5);
        });
    },

    changeIndex(startIndex, endIndex) {
      this.lstPersonGroupsChange = [];
      for (let index = startIndex; index <= endIndex; index++) {
        this.lstPersonGroups[index].sort_order = index + 1;
        this.lstPersonGroupsChange.push(this.lstPersonGroups[index]);
      }

      let params = {
        person: this.lstPersonGroupsChange
      };
      this.loadingPage = true;

      Z03_S02_SelectFacilityPersonalSettingService.changeIndex(params)
        .then(() => {
          if (this.type_action === 'addNew') {
            this.personGroupSelected = {};
          }
          this.type_action = 'changeRecord';
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          this.loadingPage = false;
        })
        .finally(() => {
          this.getPersonGroups(false);
        });
    },

    deletePersonGroup() {
      let params = {
        select_person_group_id: this.personGroupSelected.select_person_group_id
      };
      if (!this.isDeleted) {
        this.isDeleted = true;
        Z03_S02_SelectFacilityPersonalSettingService.deletePersonGroup(params)
          .then(() => {
            this.isDeleted = true;
            this.getPersonGroups(true);
            this.$notify({ message: '正常に削除しました', customClass: 'success' });
            this.onCloseModal();
          })
          .catch((err) => {
            this.isDeleted = false;
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {});
      }
    },

    updatePersonGroup(isModalConfirm) {
      this.isSubmit = true;
      let params = {
        ...this.modelUpdate
      };
      params.children.forEach((x, index) => {
        let chil = { ...params.children[index] };
        chil.product_cd = chil.product_cd ? chil.product_cd?.replace(/\s/g, '') : '';
        chil.document_id = chil.document_id ? chil.document_id?.replace(/\s/g, '') : '';
        params.children[index] = chil;
      });

      if (!this.checkValidateGroupName()) {
        if (!this.isEdited) {
          this.processingSave = true;
          this.loadingPage = true;
          this.isEdited = true;
          Z03_S02_SelectFacilityPersonalSettingService.updatePersonGroup(params)
            .then(() => {
              this.closeEditMode();
              this.isAddNew = false;
              this.isEdited = true;
              if (isModalConfirm) {
                if (this.modalConfig.props.type === 'changeRecord') {
                  this.personGroupSelected = this.modalConfig.props.item;
                }
                this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowModalConfirm: false };
              }
              if (this.type_action === 'addNew') {
                this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
              } else {
                if (this.type_action === 'copy') {
                  this.$notify({ message: '正常にコピーしました。', customClass: 'success' });
                } else {
                  this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
                }
              }
              this.getPersonGroups(false, isModalConfirm);
            })
            .catch((err) => {
              this.$notify({ message: err.response.data.message, customClass: 'error' });
              this.isEdited = false;
              this.loadingPage = false;

              this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowModalConfirm: false };
            })
            .finally(() => {
              this.processingSave = false;
              if (this.type_action === 'copy') {
                this.emitter.emit('change-processingFlag', { flag: false });
                this.onCloseModal();
              }
            });
        }
      } else {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });

        this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowModalConfirm: false };
        this.$refs.inputNamePerson.focus();
        if (this.type_action === 'copy') {
          this.emitter.emit('change-processingFlag', { flag: false });
          this.onCloseModal();
        }
      }
    },
    // end call api

    selectedPersonGroup(item) {
      this.isSubmit = false;
      if (this.type_action === 'addNew') {
        this.cancelEventSave();
      }
      this.closeEditMode();
      this.lstPersonGroups.forEach((x) => {
        x.selected = false;
        if (x.select_person_group_id === item.select_person_group_id) {
          x.selected = true;
          this.personGroupSelected = { ...x };
        }
      });
      localStorage.removeItem('ScrollTopScreen');

      if (this.$refs.refLstFacilityPerson) {
        this.$refs.refLstFacilityPerson.scrollTop = 0;
      }

      this.checkPositionItemScroll();
    },

    checkPersonGroupSelected() {
      let length = Object.keys(this.personGroupSelected).length;
      return length > 0 ? true : false;
    },

    shouldAcceptDrop() {
      if (this.isEditElement) {
        this.openConfirmEventSaveData('changeIndex');
      }
      return this.isEditElement ? false : true;
    },

    // drag drop
    onDrop(dropResult) {
      this.lstPersonGroups = applyDrag(this.lstPersonGroups, dropResult);
      this.closeEditMode();
      this.isCheckScroll = true;

      let startIndex = dropResult.removedIndex > dropResult.addedIndex ? dropResult.addedIndex : dropResult.removedIndex;
      let endIndex = dropResult.removedIndex > dropResult.addedIndex ? dropResult.removedIndex : dropResult.addedIndex;
      if (startIndex !== endIndex) {
        this.changeIndex(startIndex, endIndex);
      }
    },

    getChildPayload(index) {
      let data = this.lstPersonGroups[index];
      return data;
    },

    onDragStart(e) {
      let data = e.payload;
      this.scrollRecordChange = data;
      if (this.personGroupSelected.select_person_group_id === data.select_person_group_id) {
        this.positionScroll = '';
        this.isCheckScroll = false;
      }
    },
    // end drag drop

    confirmEventDelete() {
      this.isDeleted = false;
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: '選択した施設個人グループを完全に削除しますか？' }
      };
    },

    openEditMode() {
      this.isEditMode = true;
      this.isEdited = false;
      this.modelUpdate = { ...this.personGroupSelected };

      this.modalConfig = {
        ...this.modalConfig,
        props: { type: 'edit' }
      };
    },

    closeEditMode() {
      this.isEditMode = false;
      this.isEditElement = false;
      this.isNewChange = false;
      this.modelUpdate = {};
    },

    openConfirmEventSaveData(type, item) {
      this.isEdited = false;
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        destroyOnClose: true,
        props: { mode: 1, type: type, item: item }
      };
    },

    handleEvent(isClose) {
      let type = this.modalConfig.props.type;
      let item = this.modalConfig.props.item;

      if (type === 'changeRecord') {
        this.selectedPersonGroup(item);
      }

      if (type === 'addNew') {
        if (this.isAddNew) {
          if (isClose) {
            if (!this.personGroupSelected.select_person_group_id) {
              let length = this.lstPersonGroups.length;
              this.lstPersonGroups.splice(length - 1, 1);
              this.personGroupSelected = {};
              this.type_action = '';
              this.getElementInScreen();
              this.handleAddPersonGroup();
            }
          }
        } else {
          this.handleAddPersonGroup();
        }
      } else {
        if (type === 'edit' || this.type_action === 'addNew') {
          if (isClose) {
            if (!this.personGroupSelected.select_person_group_id) {
              let length = this.lstPersonGroups.length;
              this.lstPersonGroups.splice(length - 1, 1);
              this.personGroupSelected = {};
              this.type_action = '';
              this.getElementInScreen();
              this.isAddNew = false;
            }
          }
        }
      }

      if (type === 'copy') {
        this.openModalZ03S04();
      }

      if (type === 'redirectToH02S02') {
        this.redirectToH02S02(item);
      }
    },

    async handleAddPersonGroup() {
      let index = this.lstPersonGroups.findIndex((x) => x.select_person_group_id === '');
      if (index === -1) {
        this.isSubmit = false;
        this.type_action = 'addNew';
        this.isNewChange = true;
        this.isAddNew = true;
        this.modelUpdate = {
          select_person_group_id: '',
          select_person_group_name: '',
          sort_order: this.sort_order_lasted + 1,
          children: [],
          selected: true
        };

        this.lstPersonGroups.forEach((x) => {
          x.selected = false;
        });
        this.lstPersonGroups.push(this.modelUpdate);

        this.personGroupSelected = { ...this.modelUpdate };
        this.openEditMode();
        await this.getElementInScreen();
        this.type_action = 'addNew';
        this.personGroupSelected = { ...this.modelUpdate };

        this.scrollToView(`id_${this.personGroupSelected.select_person_group_id}`);
      }
    },

    cancelEventSave() {
      this.closeEditMode();
      this.handleEvent(true);
    },

    disabledAddnew() {
      let index = this.lstPersonGroups.findIndex((x) => x.select_person_group_id === '');
      return index > -1 ? true : false;
    },

    // H02S02
    redirectToH02S02(item) {
      let scrollTop = this.$refs.refLstFacilityPerson ? this.$refs.refLstFacilityPerson.scrollTop : 0;

      this.setScrollScreen('Z03_S02_SelectFacilityPersonalSetting', scrollTop);
      localStorage.setItem('$_PG', this.enCodeData(this.personGroupSelected.select_person_group_id));
      this.$router.push({
        name: 'H02_PersonalDetails',
        params: {
          person_cd: item.person_cd
        },
        query: {
          tab: 1
        }
      });
    },

    // Modal Z03-S04
    openModalZ03S04() {
      this.isEdited = false;
      this.modalConfig = {
        ...this.modalConfig,
        title: 'セレクトグループコピー',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z03S04SelectListCopy),
        width: '60rem',
        props: { selectMode: 2 },
        destroyOnClose: true
      };
    },

    onResultModalZ03S04(e) {
      if (e?.personGroupSelected) {
        this.handleCopyPersonGroup(e.personGroupSelected);
      }

      this.isEdited = true;
    },

    // Modal Z05-S04
    openModalZ05S04() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設個人選択',
        isShowModal: true,
        isShowClose: true,
        component: this.markRaw(Z05S04FacilityCustomerSelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '70rem',
        props: {
          mode: 'multiple',
          propsPrams: {
            ...this.paramZ05S04,
            org_cd: this.currentUser.org_cd,
            user_cd: this.currentUser.user_cd,
            user_name: this.currentUser.user_name
          }
        }
      };
    },

    onResultModalZ05S04(dataResult) {
      let datas = dataResult.facilityPersonalSelected;
      datas.forEach((person) => {
        let chilData = [...this.modelUpdate.children];
        let index = chilData.findIndex((x) => x.facility_category_type === person.facility_category_type);

        if (index > -1) {
          let data = { ...chilData[index] };
          let dataSub = [...chilData[index].children];
          if (!dataSub.some((x) => x.person_cd === person.person_cd)) {
            dataSub.push({
              department_cd: person.department_cd,
              department_name: person.department_name,
              facility_cd: person.facility_cd,
              facility_name: person.facility_name,
              facility_short_name: person.facility_short_name,
              person_cd: person.person_cd,
              person_name: person.person_name,
              position_cd: person.position_cd,
              position_name: person.position_name
            });
          }

          data = {
            ...data,
            children: dataSub
          };

          chilData[index] = data;
          this.modelUpdate = {
            ...this.modelUpdate,
            children: chilData
          };
        } else {
          let data = {
            content_cd: '',
            content_name: '',
            document_id: '',
            document_name: '',
            file_type: '',
            document_type: '',
            facility_category_name: person.facility_category_name,
            facility_category_type: person.facility_category_type,
            product_cd: '',
            product_name: '',
            select_person_group_id: this.modelUpdate.select_person_group_id,
            children: []
          };

          let dataSub = [...data.children];
          if (!dataSub.some((x) => x.person_cd === person.person_cd)) {
            dataSub.push({
              department_cd: person.department_cd,
              department_name: person.department_name,
              facility_cd: person.facility_cd,
              facility_name: person.facility_name,
              facility_short_name: person.facility_short_name,
              person_cd: person.person_cd,
              person_name: person.person_name,
              position_cd: person.position_cd,
              position_name: person.position_name
            });
          }

          data = {
            ...data,
            children: dataSub
          };
          chilData.push(data);

          this.modelUpdate = {
            ...this.modelUpdate,
            children: chilData
          };
        }
      });

      this.compareObjectChange();
    },

    // Modal Z03-S03
    openModalZ03S03(facilityCategory) {
      let products = [];
      let lstDocument = [];
      if (facilityCategory.product_cd) {
        let productCd = facilityCategory.product_cd?.split(',');
        let productName = facilityCategory.product_name?.split(',');
        productCd.forEach((x, index) => {
          products.push({
            product_cd: x.trim(),
            product_name: productName[index]
          });
        });
      }
      if (facilityCategory.document_id) {
        let documentCd = facilityCategory.document_id?.split(',');
        let documentName = facilityCategory.document_name?.split(',');
        let fileType = facilityCategory.file_type?.split(',');
        let documentType = facilityCategory.document_type?.split(',');
        documentCd.forEach((x, index) => {
          lstDocument.push({
            document_id: x.trim(),
            document_name: documentName[index],
            file_type: fileType[index],
            document_type: documentType[index]
          });
        });
      }

      this.modalConfig = {
        ...this.modalConfig,
        title: '面談内容設定',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z03_S03_ConsultationContentSetting),
        width: '44rem',
        customClass: 'custom-dialog modal-fixed',
        props: {
          facilityCategory: facilityCategory,
          contentCd: facilityCategory.content_cd,
          contentName: facilityCategory.content_name,
          products: products,
          documents: lstDocument
        },
        destroyOnClose: true
      };
    },

    onResultModalZ03S03(e) {
      let content = e.content;
      let lstProduct = e.lstProduct;
      let lstDocument = e.lstDocument;
      let facilityCategory = this.modalConfig.props.facilityCategory;

      let data = { ...this.modelUpdate };
      let lstFacilityCategory = [...data.children];
      let indexFacility = lstFacilityCategory.findIndex((x) => x.facility_category_type === facilityCategory.facility_category_type);

      lstFacilityCategory[indexFacility] = {
        ...lstFacilityCategory[indexFacility],
        content_cd: content?.content_cd || '',
        content_name: content?.content_name || '',
        document_id: lstDocument?.map((x) => x.document_id)?.toString(),
        document_name: lstDocument
          ?.map((x) => x.document_name)
          ?.toString()
          ?.replaceAll(',', ', '),
        document_type: lstDocument?.map((x) => x.document_type)?.toString(),
        file_type: lstDocument?.map((x) => x.file_type)?.toString(),
        product_cd: lstProduct?.map((x) => x.product_cd)?.toString(),
        product_name: lstProduct
          ?.map((x) => x.product_name)
          ?.toString()
          ?.replaceAll(',', ', ')
      };

      this.modelUpdate = {
        ...this.modelUpdate,
        children: lstFacilityCategory
      };

      this.isEditElement = true;
    },

    onRemovePerson(facilityCategory, item) {
      let data = { ...this.modelUpdate };
      let lstFacilityCategory = [...data.children];
      let indexFacility = lstFacilityCategory.findIndex((x) => x.facility_category_type === facilityCategory.facility_category_type);
      let lstPerson = lstFacilityCategory.find((x) => x.facility_category_type === facilityCategory.facility_category_type).children;
      lstPerson = lstPerson.filter((x) => x.person_cd !== item.person_cd);

      lstFacilityCategory[indexFacility] = {
        ...lstFacilityCategory[indexFacility],
        children: lstPerson
      };

      this.modelUpdate = {
        ...this.modelUpdate,
        children: lstFacilityCategory
      };

      this.isEditElement = true;
    },

    handleCopyPersonGroup(group) {
      this.type_action = 'copy';
      this.modelUpdate = {
        ...group,
        select_person_group_id: '',
        sort_order: this.sort_order_lasted + 1,
        selected: true
      };

      this.lstPersonGroups.forEach((x) => {
        x.selected = false;
      });

      this.personGroupSelected = { ...this.modelUpdate };

      this.updatePersonGroup();
    },

    compareObjectChange() {
      this.isNewChange = false;
      this.isEditElement = !_.isEqual(this.modelUpdate, this.personGroupSelected);
    },

    validationGroupName() {
      let group_name = this.modelUpdate.select_person_group_name || '';
      if (!group_name) {
        return '入力してください。';
      } else {
        if (group_name.length > 50) {
          return this.getMessage('MSFA0012', 'グループ名', 50);
        }
      }
      return '';
    },

    clearGroupName() {
      this.modelUpdate.select_person_group_name = '';
      this.$refs.inputNamePerson.focus();
    },

    checkValidateGroupName() {
      if (this.validationGroupName() || !this.modelUpdate.select_person_group_name) {
        return true;
      }
      return false;
    },

    validationContent() {
      let chil = [...this.modelUpdate.children];
      let message = '';
      for (let i = 0; i < chil.length; i++) {
        const element = chil[i];
        if (!element.content_cd) {
          message = '選択してください。';
          break;
        }
      }

      return message;
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S04') {
          this.onResultModalZ05S04(e);
        }
        if (e.screen === 'Z03_S04') {
          this.onResultModalZ03S04(e);
        }
        if (e.screen === 'Z03_S03') {
          this.onResultModalZ03S03(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    // scroll
    scrollToView(id) {
      document.getElementById(id)?.scrollIntoView({
        behavior: 'smooth'
      });
      this.getElementInScreen();
    },

    getElementInScreen() {
      if (this.isCheckScroll) {
        this.lstPersonGroupVisible = [];

        let itemScrollTab = document.getElementById('scrollTab');

        this.lstPersonGroups.forEach((x) => {
          let element = document.getElementById(`id_${x.select_person_group_id}`);

          if (!(element?.offsetTop - itemScrollTab?.scrollTop + 12 < 0 || element?.offsetTop - itemScrollTab?.scrollTop + 12 > itemScrollTab?.offsetHeight)) {
            this.lstPersonGroupVisible.push(x);
          }
        });
        this.checkPositionItemScroll();
      }
    },

    scrollToTop() {
      let index = this.lstPersonGroups.findIndex((x) => x.select_person_group_id === this.lstPersonGroupVisible[0]?.select_person_group_id);
      let item = this.lstPersonGroups[index - 1];
      if (this.lstPersonGroupVisible[0]?.select_person_group_id !== this.lstPersonGroups[0]?.select_person_group_id) {
        this.scrollToView(`id_${item?.select_person_group_id}`);
        this.$refs.btnScrollTop.click();
      } else {
        this.scrollToView(`id_${this.lstPersonGroups[0]?.select_person_group_id}`);
        this.$refs.btnScrollTop.click();
      }
    },

    scrollToBottom() {
      let length = this.lstPersonGroupVisible.length;
      let index = this.lstPersonGroups.findIndex((x) => x.select_person_group_id === this.lstPersonGroupVisible[0]?.select_person_group_id);
      let item = this.lstPersonGroups[index + 1];
      if (this.lstPersonGroupVisible[length - 1].sort_order !== this.sort_order_lasted) {
        this.scrollToView(`id_${item.select_person_group_id}`);
        this.$refs.btnScrollBottom.click();
      } else {
        this.scrollToView(`id_${this.lstPersonGroupVisible[length - 1].select_person_group_id}`);
        this.$refs.btnScrollBottom.click();
      }
    },

    checkPositionItemScroll() {
      this.positionScroll = '';
      let length = this.lstPersonGroupVisible.length;
      if (this.lstPersonGroupVisible[0]?.sort_order > this.personGroupSelected?.sort_order) {
        this.positionScroll = 'top';
      } else {
        if (this.lstPersonGroupVisible[length - 1]?.sort_order < this.personGroupSelected?.sort_order) {
          this.positionScroll = 'bottom';
        }
      }
    },

    disableScrollTopBtn() {
      if (this.lstPersonGroups.length === 0 || this.lstPersonGroupVisible[0]?.sort_order === this.lstPersonGroups[0]?.sort_order) {
        return true;
      }
      return false;
    },

    disableScrollBottomBtn() {
      let itemScrollTab = document.getElementById('scrollTab');

      if (this.lstPersonGroups.length === 0 || itemScrollTab?.scrollTop + itemScrollTab?.offsetHeight >= itemScrollTab?.scrollHeight - 2) {
        return true;
      }
      return false;
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.groupLayout {
  min-width: 768px;
  @media (max-width: $viewport_tablet) {
    min-width: 700px;
  }
}
.facility {
  .facility-head {
    background: #f9f9f9;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #cdd5db;
    gap: 10px;
    .facility-head__info {
      width: 100%;
      padding-right: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      @media (max-width: $viewport_tablet) {
        padding-right: 0;
      }
      .facility-title {
        color: #2d3033;
        font-size: 1rem;
        font-weight: 700;
        width: 80px;
      }

      .facility-label {
        width: calc(100% - 90px);
        word-break: break-word;
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
    .facility-head-btn {
      align-self: center;
      @media (max-width: $viewport_tablet) {
        text-align: right;
        margin-top: 6px;
      }
      .btn {
        height: 32px;
        padding: 0 12px;
        white-space: nowrap;
        .btn-iconLeft {
          top: -2px;
        }
      }
    }
  }
  .facility-list {
    ul {
      li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 55px;
        padding: 6px 24px;
        border-bottom: 1px solid #cdd5db;
        .facility-list-info {
          width: 100%;
          padding-right: 10px;
          font-size: 1rem;
          .facility-list-label {
            padding-right: 15px;
            color: #2d3033;
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
        .btn {
          min-width: 33px;
        }
      }
    }
  }
}

.tab-item {
  display: flex !important;
  flex-direction: row;
  flex-wrap: nowrap;
  padding: 0 !important;
  padding-left: 10px !important;
  cursor: pointer;
  min-height: 52px;

  .left {
    padding: 10px !important;
    cursor: move;
    background: url(~@/assets/template/images/icon_dots.svg) no-repeat;
    align-self: center;
  }
  .right {
    padding: 18px 10px;
    padding-right: 25px;
    flex-grow: 1;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
  }
}

.confirm-dialog-custom {
  .content {
    font-size: 1rem;
    position: relative;
    margin-bottom: 30px;

    .status {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px !important;
    }

    .message {
      text-align: center;
      .title {
        margin-top: 12px;
        color: #2d3033;
        font-size: 1.125rem;
        font-weight: 700;
      }
    }
  }
}

.facility-text {
  color: #2d3033;
}

.status.el-icon-warning {
  --el-messagebox-color: var(--el-color-warning);
  color: var(--el-messagebox-color);
}

.confirm-btn {
  text-align: center;

  .btn {
    width: 130px;
  }
}

.shadow {
  box-shadow: -9px 0px 9px 5px #00000026 !important;
}

.groupLayout .group-content .colSlider .colSlider-tabs .navTabs-sider li a.active {
  box-shadow: unset;
}

.groupLayout .group-content .colSlider .colSlider-tabs .navTabs-sider li a::before {
  display: none;
}

.groupLayout .group-content .colSlider .colSlider-tabs .navTabs-sider li a::after {
  right: 15px;
}

.groupLayout .group-content .colSlider .colSlider-tabs {
  overflow: scroll;
  direction: rtl;
}

.groupLayout .group-content .colSlider .colSlider-tabs .navTabs-sider {
  direction: initial;
}

.btn.btn--icon {
  width: 35px;
  height: 33px;
}

.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}

.btn {
  &:disabled {
    cursor: not-allowed;
  }
}

.sticky {
  position: -webkit-sticky;
  position: fixed;
  z-index: 3;
  box-shadow: -18px 0px 9px 5px #00000021 !important;
  width: 468px;
  a {
    cursor: default;
    .left {
      cursor: default;
    }
  }

  @media (max-width: $viewport_desktop) {
    width: 246px;
  }
  @media (max-width: $viewport_tablet) {
    width: 196px;
  }
}

.sticky-top {
  top: 143px;
}

.sticky-bottom {
  bottom: 25px;
}

.icon-close {
  width: 24px;
}

.icon-custom {
  border-left: unset !important;
}
</style>
