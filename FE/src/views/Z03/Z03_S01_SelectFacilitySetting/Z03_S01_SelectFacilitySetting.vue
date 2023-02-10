<template>
  <div v-loading="loadingPage" v-load-f5="true" class="wrapContent">
    <div class="groupLayout">
      <div class="group-head">
        <div class="group-btn">
          <button
            type="button"
            class="btn btn-outline-primary btn-radius btn-custom"
            @click="isEditElement ? openConfirmEventSaveData('addNew') : handleAddFacilityGroup()"
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
          <div id="scrollTab" ref="facilityGroupRef" class="colSlider-tabs" @scroll="getElementInScreen">
            <ul class="nav navTabs-sider">
              <li v-if="positionScroll === 'top'" class="sticky sticky-top">
                <a class="tab-item active">
                  <span class="column-drag-handle left" />
                  <div class="right active" href="#tabs-content" data-toggle="tab" role="tab">
                    {{ facilityGroupSelected?.select_facility_group_id ? facilityGroupSelected?.select_facility_group_name : 'グループ名' }}
                  </div>
                </a>
              </li>
              <li v-if="positionScroll === 'bottom'" class="sticky sticky-bottom">
                <a class="tab-item active">
                  <span class="column-drag-handle left" />
                  <div class="right active" href="#tabs-content" data-toggle="tab" role="tab">
                    {{ facilityGroupSelected?.select_facility_group_id ? facilityGroupSelected?.select_facility_group_name : 'グループ名' }}
                  </div>
                </a>
              </li>
              <Container
                v-if="lstFacilityGroups.length > 0"
                drag-handle-selector=".column-drag-handle"
                lock-axis="y"
                behaviour="contain"
                :should-accept-drop="shouldAcceptDrop"
                :get-child-payload="getChildPayload"
                @drop="onDrop"
                @drag-start="onDragStart"
              >
                <Draggable
                  v-for="facilityGroup in lstFacilityGroups"
                  :id="`id_${facilityGroup.select_facility_group_id}`"
                  :ref="`reft_${facilityGroup.select_facility_group_id}`"
                  :key="facilityGroup.select_facility_group_id"
                  :drag-not-allowed="false"
                  :class="facilityGroup.selected ? 'shadow' : ''"
                >
                  <li>
                    <a class="tab-item" :class="facilityGroup.selected ? 'active' : ''">
                      <span class="column-drag-handle left" />
                      <div
                        class="right active"
                        href="#tabs-content"
                        data-toggle="tab"
                        role="tab"
                        @click="isEditElement ? openConfirmEventSaveData('changeRecord', facilityGroup) : selectedFacilityGroup(facilityGroup)"
                      >
                        {{ facilityGroup.select_facility_group_id ? facilityGroup.select_facility_group_name : 'グループ名' }}
                      </div>
                    </a>
                  </li>
                </Draggable>
              </Container>
            </ul>
          </div>
        </div>
        <div class="colContent">
          <div class="tab-content">
            <div id="tab-content" class="tab-pane active" role="tabpanel">
              <div v-if="checkFacilityGroupSelected() && !isEditMode" class="contentBody">
                <div class="contentBody-info">
                  <div class="title">
                    <h3 class="tlt">{{ facilityGroupSelected.select_facility_group_name }}</h3>
                  </div>
                  <div class="bodyContainer">
                    <div class="bodyContainer-title"><h4 class="tlt">施設</h4></div>
                    <div ref="refLstFacility" class="bodyContainer-main scrollbar">
                      <div class="lsInstitution">
                        <ul>
                          <li v-for="item in facilityGroupSelected.children" :key="item.facility_cd">
                            <a class="selectCopy-a person-text-nomal log-icon" title_log="施設名" @click="redirectToH01S02(item)">{{
                              item.facility_short_name
                            }}</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="contentBody-btn">
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmEventDelete()">削除</button>
                  <button type="button" class="btn btn-primary" @click="openEditMode()">編集</button>
                </div>
              </div>

              <div v-if="checkFacilityGroupSelected() && isEditMode" class="contentBody">
                <div class="contentBody-info">
                  <div class="groupName">
                    <div class="form-icon icon-right" :class="checkValidateGroupName() && isSubmit ? 'hasErr' : ''">
                      <span class="icon icon--cursor icon-custom icon-close" @click="clearGroupName" @touchstart="clearGroupName">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                      </span>
                      <el-input
                        ref="inputName"
                        v-model="modelUpdate.select_facility_group_name"
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
                      <h4 class="tlt">施設</h4>
                      <button
                        type="button"
                        class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                        @click="openModalZ05S03()"
                        @touchstart="openModalZ05S03()"
                      >
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" />
                        </span>
                        追加
                      </button>
                    </div>
                    <div ref="refLstFacilityEdit" class="bodyContainer-main scrollbar">
                      <div class="lsInstitution">
                        <ul>
                          <li v-for="item in modelUpdate.list_facility" :key="item.facility_cd">
                            <a
                              class="selectCopy-a person-text-nomal log-icon"
                              title_log="施設名"
                              @click="isEditElement ? openConfirmEventSaveData('redirectToH01S02', item) : redirectToH01S02(item)"
                              >{{ item.facility_short_name }}</a
                            >

                            <button class="btn btn--icon" @click="onRemoveFacility(item)">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                            </button>
                          </li>
                        </ul>
                      </div>
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
                  <el-button type="primary" class="btn btn-primary" :disabled="processingSave" @click.prevent="updateFacilityGroup()"> 保存 </el-button>
                </div>
              </div>

              <div v-if="!checkFacilityGroupSelected() && !loadingPage" class="no-data">
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
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="true"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="deleteFacilityGroup"
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
import Z03_S01_SelectFacilitySettingService from '@/api/Z03/Z03_S01_SelectFacilitySettingService';
import Z03S04SelectListCopy from '@/views/Z03/Z03_S04_SelectListCopy/Z03_S04_SelectListCopy';
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';

export default {
  name: 'Z03_S01_SelectFacilitySetting',
  components: { Container, Draggable, Z03S04SelectListCopy, Z05S03FacilitySelection },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      loadingPage: false,
      isEditMode: false,

      isEditElement: false,
      isNewChange: false,
      processingSave: false,
      isSubmit: false,

      lstFacilityGroups: [],
      lstFacilityGroupsOld: [],
      lstFacilityGroupsChange: [],
      facilityGroupSelected: {},
      lstFacilityGroupVisible: [],
      positionScroll: '',

      sort_order_lasted: 1,

      type_action: '',

      modelUpdate: {
        select_facility_group_id: '',
        select_facility_group_name: '',
        sort_order: null,
        list_facility: []
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
        isShowClose: false,
        closeOnClickMark: false
      },

      currentUser: null,

      isCheckScroll: true,
      scrollRecordChange: {},

      isAddNew: false,
      isEdited: false,
      isDeleted: false,
      onScrollTop: 0,
      redirect: false
    };
  },
  created() {
    this.getFacilityGroups(true);
    window.addEventListener('scrollTab', this.getElementInScreen);
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'セレクト施設管理',
      icon: 'icon_clipboard-setting_color.svg',
      isShowBack: false
    });
    this.currentUser = this.getCurrentUser();
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.Z03_S01_SelectFacilitySetting || 0;

    // Load scrollbar
    setTimeout(() => {
      this.js_pscroll(0.5);
    }, 500);
  },

  methods: {
    enCodeData(obj) {
      // eslint-disable-next-line no-undef
      let strData = Buffer.from(JSON.stringify(obj), 'utf8').toString('base64');
      return strData;
    },

    getFacilityGroups(isReload, isModalConfirm) {
      this.loadingPage = true;
      this.lstFacilityGroups = [];
      this.lstFacilityGroupsOld = [];

      Z03_S01_SelectFacilitySettingService.getFacilityGroups({ isCopy: false })
        .then(async (res) => {
          this.lstFacilityGroups = res?.data?.data?.facility_group || [];
          this.lstFacilityGroupsOld = res?.data?.data?.facility_group || [];
          this.sort_order_lasted = res?.data?.data?.sort_order;

          let time;
          const timeout = setTimeout(() => {
            if (time) {
              clearTimeout(timeout);
              return;
            }

            this.getElementInScreen();
            if (isReload) {
              this.facilityGroupSelected = {};
              let length = this.lstFacilityGroups.length;

              const isLoadingComponent = localStorage.getItem('isLoadingComponent');

              const facilityBefore = this.decodeData(localStorage.getItem('$_FG'));

              if (length > 0) {
                this.lstFacilityGroups.forEach((x, index) => {
                  x.selected = false;
                  if (isLoadingComponent === 'true') {
                    if (index === 0) {
                      x.selected = true;
                      this.facilityGroupSelected = { ...x };
                    }
                  } else {
                    if (facilityBefore) {
                      if (facilityBefore === x.select_facility_group_id) {
                        x.selected = true;
                        this.facilityGroupSelected = { ...x };

                        localStorage.removeItem('$_FG');
                      }
                    } else {
                      if (index === 0) {
                        x.selected = true;
                        this.facilityGroupSelected = { ...x };
                      }
                    }
                  }
                });
              }

              this.getElementInScreen();
            } else {
              if (this.type_action === 'addNew' || this.type_action === 'copy') {
                let length = this.lstFacilityGroups.length;
                if (length > 0) {
                  this.lstFacilityGroups.forEach((x, index) => {
                    x.selected = false;
                    if (index === length - 1) {
                      x.selected = true;
                      this.facilityGroupSelected = { ...x };
                      this.scrollToView(`id_${x.select_facility_group_id}`);
                    }
                  });
                }
              } else {
                this.lstFacilityGroups.forEach((x) => {
                  if (x.select_facility_group_id === this.facilityGroupSelected.select_facility_group_id) {
                    x.selected = true;
                    this.facilityGroupSelected = { ...x };
                    if (this.type_action !== 'changeRecord') {
                      this.scrollToView(`id_${x.select_facility_group_id}`);
                    }
                  }
                });
                if (this.type_action === 'changeRecord') {
                  this.scrollToView(`id_${this.scrollRecordChange.select_facility_group_id}`);
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

          if (this.$refs.refLstFacility) {
            if (this.onScrollTop) {
              this.$refs.refLstFacility.scrollTop = this.onScrollTop;
            } else {
              this.$refs.refLstFacility.scrollTop = 0;
            }
          }
          this.loadingPage = false;
        });
    },

    changeIndex(startIndex, endIndex) {
      this.lstFacilityGroupsChange = [];
      for (let index = startIndex; index <= endIndex; index++) {
        this.lstFacilityGroups[index].sort_order = index + 1;
        this.lstFacilityGroupsChange.push(this.lstFacilityGroups[index]);
      }

      let params = {
        facility: this.lstFacilityGroupsChange
      };

      Z03_S01_SelectFacilitySettingService.changeIndex(params)
        .then(() => {
          if (this.type_action === 'addNew') {
            this.facilityGroupSelected = {};
          }
          this.type_action = 'changeRecord';
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.getFacilityGroups(false);
        });
    },

    deleteFacilityGroup() {
      let params = {
        select_facility_group_id: this.facilityGroupSelected.select_facility_group_id
      };

      if (!this.isDeleted) {
        this.isDeleted = true;
        Z03_S01_SelectFacilitySettingService.deleteFacilityGroup(params)
          .then(() => {
            this.isDeleted = true;
            this.getFacilityGroups(true);
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

    updateFacilityGroup(isModalConfirm) {
      this.isSubmit = true;
      let params = {
        ...this.modelUpdate,
        list_facility: this.modelUpdate.list_facility && this.modelUpdate.list_facility.length > 0 ? this.modelUpdate.list_facility : []
      };

      delete params.children;

      if (!this.checkValidateGroupName()) {
        if (!this.isEdited) {
          this.processingSave = true;
          this.loadingPage = true;
          this.isEdited = true;
          Z03_S01_SelectFacilitySettingService.updateFacilityGroup(params)
            .then(() => {
              this.closeEditMode();
              this.isAddNew = false;
              this.isEdited = true;
              if (isModalConfirm) {
                if (this.modalConfig.props.type === 'changeRecord') {
                  this.facilityGroupSelected = this.modalConfig.props.item;
                }
                this.modalConfig = { ...this.modalConfig, isShowModal: false, destroyOnClose: true };
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
              this.getFacilityGroups(false, isModalConfirm);
            })
            .catch((err) => {
              this.$notify({ message: err.response.data.message, customClass: 'error' });
              this.isEdited = false;

              this.loadingPage = false;

              this.modalConfig = { ...this.modalConfig, isShowModal: false, destroyOnClose: true };
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

        this.modalConfig = { ...this.modalConfig, isShowModal: false, destroyOnClose: true };
        this.$refs.inputName.focus();
        if (this.type_action === 'copy') {
          this.emitter.emit('change-processingFlag', { flag: false });
          this.onCloseModal();
        }
      }
    },

    selectedFacilityGroup(item) {
      if (this.type_action === 'addNew' || this.type_action === 'copy') {
        this.cancelEventSave();
      }
      this.closeEditMode();
      this.isSubmit = false;
      this.lstFacilityGroups.forEach((x) => {
        x.selected = false;
        if (x.select_facility_group_id === item.select_facility_group_id) {
          x.selected = true;
          this.facilityGroupSelected = { ...x };
        }
      });

      localStorage.removeItem('ScrollTopScreen');

      if (this.$refs.refLstFacility) {
        this.$refs.refLstFacility.scrollTop = 0;
      }

      this.checkPositionItemScroll();
    },

    checkFacilityGroupSelected() {
      let length = Object.keys(this.facilityGroupSelected).length;
      return length > 0 ? true : false;
    },

    // drag drop
    shouldAcceptDrop() {
      if (this.isEditElement) {
        this.openConfirmEventSaveData('changeIndex');
      }
      return this.isEditElement ? false : true;
    },

    onDrop(dropResult) {
      this.lstFacilityGroups = applyDrag(this.lstFacilityGroups, dropResult);
      this.closeEditMode();
      this.isCheckScroll = true;

      let startIndex = dropResult.removedIndex > dropResult.addedIndex ? dropResult.addedIndex : dropResult.removedIndex;
      let endIndex = dropResult.removedIndex > dropResult.addedIndex ? dropResult.removedIndex : dropResult.addedIndex;
      if (startIndex !== endIndex) {
        this.changeIndex(startIndex, endIndex);
      }
    },

    getChildPayload(index) {
      let data = this.lstFacilityGroups[index];
      return data;
    },

    onDragStart(e) {
      let data = e.payload;
      this.scrollRecordChange = data;
      if (this.facilityGroupSelected.select_facility_group_id === data.select_facility_group_id) {
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
        destroyOnClose: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { mode: 0, type: 'delete', title: '選択した施設グループを完全に削除しますか？' },
        isShowClose: false
      };
    },

    openEditMode() {
      this.isEditMode = true;
      this.isEdited = false;
      this.modelUpdate = {
        ...this.facilityGroupSelected,
        list_facility: this.facilityGroupSelected.children
      };
    },

    closeEditMode() {
      this.isEdited = false;
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
        this.selectedFacilityGroup(item);
      }

      if (type === 'addNew') {
        if (this.isAddNew) {
          if (isClose) {
            if (!this.facilityGroupSelected.select_facility_group_id) {
              let length = this.lstFacilityGroups.length;
              this.lstFacilityGroups.splice(length - 1, 1);
              this.facilityGroupSelected = {};
              this.type_action = '';
              this.getElementInScreen();
              this.handleAddFacilityGroup();
            }
          }
        } else {
          this.handleAddFacilityGroup();
        }
      } else {
        if (type === 'edit' || this.type_action === 'addNew') {
          if (isClose) {
            if (!this.facilityGroupSelected.select_facility_group_id) {
              let length = this.lstFacilityGroups.length;
              this.lstFacilityGroups.splice(length - 1, 1);
              this.facilityGroupSelected = {};
              this.type_action = '';
              this.getElementInScreen();
              this.isAddNew = false;
            }
          }
        }

        if (type === 'copy') {
          this.openModalZ03S04();
        }

        if (type === 'redirectToH01S02') {
          this.redirectToH01S02(item);
        }
      }
    },

    async handleAddFacilityGroup() {
      let index = this.lstFacilityGroups.findIndex((x) => x.select_facility_group_id === '');
      if (index === -1) {
        this.isSubmit = false;
        this.type_action = 'addNew';
        this.isAddNew = true;
        this.isNewChange = true;
        this.modelUpdate = {
          select_facility_group_id: '',
          select_facility_group_name: '',
          sort_order: this.sort_order_lasted + 1,
          list_facility: [],
          children: [],
          selected: true
        };

        this.lstFacilityGroups.forEach((x) => {
          x.selected = false;
        });
        this.lstFacilityGroups.push(this.modelUpdate);

        this.facilityGroupSelected = { ...this.modelUpdate };
        this.openEditMode();
        await this.getElementInScreen();
        this.type_action = 'addNew';
        this.facilityGroupSelected = { ...this.modelUpdate };

        this.scrollToView(`id_${this.facilityGroupSelected.select_facility_group_id}`);
      }
    },

    cancelEventSave() {
      this.closeEditMode();
      this.handleEvent(true);
      // log
    },

    disabledAddnew() {
      let index = this.lstFacilityGroups.findIndex((x) => x.select_facility_group_id === '');
      return index > -1 ? true : false;
    },

    redirectToH01S02(item) {
      let scrollTop = this.$refs.refLstFacility ? this.$refs.refLstFacility.scrollTop : 0;
      this.setScrollScreen('Z03_S01_SelectFacilitySetting', scrollTop);
      this.redirect = true;
      localStorage.setItem('$_FG', this.enCodeData(this.facilityGroupSelected.select_facility_group_id));

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

    // Modal Z03-S04
    openModalZ03S04() {
      this.isEdited = false;
      this.modalConfig = {
        ...this.modalConfig,
        title: 'セレクトグループコピー',
        isShowModal: true,
        component: markRaw(Z03S04SelectListCopy),
        width: '60rem',
        props: { selectMode: 1 },
        isShowClose: true,
        destroyOnClose: true
      };
    },

    onResultModalZ03S04(e) {
      if (e?.facilityGroupSelected) {
        this.handleCopyFacilityGroup(e.facilityGroupSelected);
      } else {
        this.onCloseModal();
      }

      this.isEdited = true;
    },

    handleCopyFacilityGroup(group) {
      this.type_action = 'copy';
      this.modelUpdate = {
        ...group,
        select_facility_group_id: '',
        list_facility: group.children,
        sort_order: this.sort_order_lasted + 1,
        selected: true
      };

      this.lstFacilityGroups.forEach((x) => {
        x.selected = false;
      });

      this.facilityGroupSelected = { ...this.modelUpdate };

      this.updateFacilityGroup();
    },

    compareObjectChange() {
      this.isNewChange = false;

      this.facilityGroupSelected = {
        ...this.facilityGroupSelected,
        list_facility: this.facilityGroupSelected.children
      };

      this.isEditElement = !_.isEqual(this.modelUpdate, this.facilityGroupSelected);
    },

    validationGroupName() {
      let group_name = this.modelUpdate.select_facility_group_name || '';
      if (!group_name) {
        return '入力してください。';
      } else {
        if (group_name.length > 50) {
          return this.getMessage('MSFA0012', 'グループ名', 50);
        }
      }
      return '';
    },

    checkValidateGroupName() {
      if (this.validationGroupName() || !this.modelUpdate.select_facility_group_name) {
        return true;
      }
      return false;
    },

    clearGroupName() {
      this.modelUpdate.select_facility_group_name = '';
      this.$refs.inputName.focus();
    },

    // Modal Z05-S03
    openModalZ05S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設選択',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z05S03FacilitySelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        props: {
          mode: 'multiple',
          orgCD: this.currentUser.org_cd,
          userCD: this.currentUser.user_cd,
          userName: this.currentUser.user_name
        },
        destroyOnClose: true
      };
    },

    onResultModalZ05S03(e) {
      let result = e.map((x) => ({
        facility_cd: x.facility_cd,
        facility_short_name: x.facility_short_name
      }));

      let dataChil = [...this.modelUpdate.list_facility];

      result.forEach((x) => {
        if (!dataChil.some((i) => i.facility_cd === x.facility_cd)) {
          dataChil.push(x);
        }
      });

      this.modelUpdate = {
        ...this.modelUpdate,
        list_facility: dataChil
      };
      this.compareObjectChange();
      this.onCloseModal();
    },

    onRemoveFacility(item) {
      let dataChil = this.modelUpdate.list_facility.filter((x) => x.facility_cd !== item.facility_cd);
      this.modelUpdate = {
        ...this.modelUpdate,
        list_facility: dataChil
      };
      this.compareObjectChange();
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S03') {
          this.onResultModalZ05S03(e?.facilitySelectList);
        }
        if (e.screen === 'Z03_S04') {
          this.onResultModalZ03S04(e);
        }
      } else {
        this.onCloseModal();
      }
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, props: {}, destroyOnClose: true };
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
        this.lstFacilityGroupVisible = [];

        let itemScrollTab = document.getElementById('scrollTab');

        this.lstFacilityGroups.forEach((x) => {
          let element = document.getElementById(`id_${x.select_facility_group_id}`);

          if (!(element?.offsetTop - itemScrollTab.scrollTop + 12 < 0 || element?.offsetTop - itemScrollTab.scrollTop + 12 > itemScrollTab?.offsetHeight)) {
            this.lstFacilityGroupVisible.push(x);
          }
        });
        this.checkPositionItemScroll();
      }
    },

    scrollToTop() {
      let index = this.lstFacilityGroups.findIndex((x) => x.select_facility_group_id === this.lstFacilityGroupVisible[0]?.select_facility_group_id);
      let item = this.lstFacilityGroups[index - 1];
      if (this.lstFacilityGroupVisible[0]?.select_facility_group_id !== this.lstFacilityGroups[0]?.select_facility_group_id) {
        this.scrollToView(`id_${item?.select_facility_group_id}`);
        this.$refs.btnScrollTop.click();
      } else {
        this.scrollToView(`id_${this.lstFacilityGroups[0]?.select_facility_group_id}`);
        this.$refs.btnScrollTop.click();
      }
    },

    scrollToBottom() {
      let length = this.lstFacilityGroupVisible.length;

      let index = this.lstFacilityGroups.findIndex((x) => x.select_facility_group_id === this.lstFacilityGroupVisible[0]?.select_facility_group_id);
      let item = this.lstFacilityGroups[index + 1];

      if (this.lstFacilityGroupVisible[length - 1]?.sort_order !== this.sort_order_lasted) {
        this.scrollToView(`id_${item?.select_facility_group_id}`);
        this.$refs.btnScrollBottom.click();
      } else {
        this.scrollToView(`id_${this.lstFacilityGroupVisible[length - 1]?.select_facility_group_id}`);
        this.$refs.btnScrollBottom.click();
      }
    },

    checkPositionItemScroll() {
      this.positionScroll = '';
      let length = this.lstFacilityGroupVisible.length;
      if (this.lstFacilityGroupVisible[0]?.sort_order > this.facilityGroupSelected?.sort_order) {
        this.positionScroll = 'top';
      } else {
        if (this.lstFacilityGroupVisible[length - 1]?.sort_order < this.facilityGroupSelected?.sort_order) {
          this.positionScroll = 'bottom';
        }
      }
    },

    disableScrollTopBtn() {
      if (this.lstFacilityGroups.length === 0 || this.lstFacilityGroupVisible[0]?.sort_order === this.lstFacilityGroups[0]?.sort_order) {
        return true;
      }
      return false;
    },

    disableScrollBottomBtn() {
      let itemScrollTab = document.getElementById('scrollTab');

      if (this.lstFacilityGroups.length === 0 || itemScrollTab?.scrollTop + itemScrollTab?.offsetHeight >= itemScrollTab?.scrollHeight - 2) {
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
.lsInstitution {
  ul {
    li {
      border-bottom: 1px solid #cdd5db;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 6px 24px 6px 16px;
      min-height: 55px;
      a {
        font-size: 1rem;
        display: block;
        width: 100%;
        @media (max-width: $viewport_desktop) {
          font-size: 0.875rem;
        }
      }
      .btn {
        min-width: 38px;
      }
    }
    .selectCopy-a {
      color: #59a5ff;
      cursor: pointer;
    }
  }
}

.groupLayout .group-content .colSlider .colSlider-tabs .navTabs-sider li a::before {
  display: none;
}

.groupLayout .group-content .colSlider .colSlider-tabs .navTabs-sider li a.active {
  box-shadow: unset;
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
    text-align: center;
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

.btn.btn--icon {
  width: 38px;
  height: 37px;
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
