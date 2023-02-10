<template>
  <div v-loading="isLodaingTabAuthorizer" class="group-tblUser">
    <div class="tblUserSelect">
      <ul class="mfsa-custom-tab-select">
        <li v-for="item of userApproval" :key="item">
          <button type="button" :class="{ active: active_el === item.definition_value }" class="btn btn-select" @click="filterForApproval(item)">
            {{ item.definition_label }}
          </button>
        </li>
      </ul>
    </div>
    <div ref="tblUser3" class="tblUser tblUserTab3 tblUser--outhor table-hg100 scrollbar" @scroll="onScroll">
      <div v-if="listTabAuthorizer.length > 0" class="application-btn">
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
            <th>ユーザ</th>
            <th>現行</th>
            <th>先行予約</th>
          </tr>
        </thead>
        <tbody v-if="listTabAuthorizer.length > 0">
          <tr v-for="(item, idx1) of listTabAuthorizer" :id="`classNameT3--${item.user_cd}`" :key="item.user_cd" style="cursor: default">
            <td>
              <p class="tblUser-name">
                {{ item.definition_label ? item?.userNameDefinitionLabel : item.user_name }}
              </p>
              <p>ユーザコード：{{ item?.user_cd }}</p>
              <p class="tblUser-add">
                <span class="item-add">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" />
                </span>
                {{ item?.org_label ? item?.org_label : '(所属なし)' }}
              </p>
            </td>
            <td style="position: relative">
              <div v-for="item2 of item?.approval_current" :key="item2">
                <div class="tblUser-dateTime tblUser--boxBg">
                  {{ formatFullDate(item2?.start_date) ? formatFullDate(item2?.start_date) : '' }}{{ JapaneseTilde()
                  }}{{ item2?.end_date === '9999/12/31' ? '' : formatFullDate(item2?.end_date) ? formatFullDate(item2?.end_date) : '' }}
                </div>
                <div class="tblUser-user tblUser-user-custom">
                  <div class="sliderCurrent">
                    <div class="sliderCurrent-btn">
                      <button v-if="current_slide_number[idx1] != 0" class="btn btn--prev" @click="prevSlide(idx1)" @touchstart="prevSlide(idx1)">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slide-control-bule.svg')" alt="" />
                      </button>
                      <button
                        v-if="item2?.approval_layer_num.length - (item2?.approval_layer_num.length % 2 === 0 ? 2 : 1) != current_slide_number[idx1]"
                        class="btn btn--next"
                        @click="nextSlide(idx1)"
                        @touchstart="nextSlide(idx1)"
                      >
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slide-control-bule.svg')" alt="" />
                      </button>
                    </div>
                    <div class="sliderCurrent-wrap">
                      <div class="carouselWrap">
                        <ul>
                          <li
                            v-for="(item3, index) in item2?.approval_layer_num"
                            v-show="current_slide_number[idx1] === index"
                            :key="item3"
                            :class="({ 'is-active-slide': current_slide_number[idx1] === index }, { 'custom-li': isOnly(item2?.approval_layer_num) })"
                          >
                            <div class="carouselHead">{{ item3?.approval_layer_num }}次</div>
                            <div class="carouselContent">
                              <ul class="carouselContent-lst">
                                <li v-for="item4 of item3?.approval_user" :key="item4">
                                  <p class="carouselContent-name">
                                    {{ item4?.user_name }} <span v-if="item4?.definition_label">（{{ item4?.definition_label }}）</span>
                                  </p>
                                  <p class="carouselContent-add">
                                    <span class="carouselContent-item">
                                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" /> </span
                                    >{{ item4?.org_label ? item4?.org_label : '(所属なし)' }}
                                  </p>
                                </li>
                              </ul>
                            </div>
                          </li>
                          <li v-if="item2?.approval_layer_num[current_slide_number[idx1] + 1]">
                            <div class="carouselHead">{{ item2?.approval_layer_num[current_slide_number[idx1] + 1]?.approval_layer_num }}次</div>
                            <div class="carouselContent">
                              <ul class="carouselContent-lst">
                                <li v-for="item5 of item2?.approval_layer_num[current_slide_number[idx1] + 1]?.approval_user" :key="item5">
                                  <p class="carouselContent-name">
                                    {{ item5?.user_name }}<span v-if="item5?.definition_label">（{{ item5?.definition_label }}）</span>
                                  </p>
                                  <p class="carouselContent-add">
                                    <span class="carouselContent-item">
                                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" /> </span
                                    >{{ item5?.org_label ? item5?.org_label : '(所属なし)' }}
                                  </p>
                                </li>
                              </ul>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="item?.approval_current.length === 0" style="bottom: 0; position: absolute; top: 39%; padding: 0 0 0 14px; font-weight: 700">
                未設定
              </div>
            </td>
            <td
              v-if="!item.approval_reservation[0]?.start_date"
              class="vertical-middle custom"
              @click="openModalUpdateAUser('createIsRecord', {}, item, item2?.start_date)"
            >
              <div class="addition-label custom2">
                <span class="msfa-custom-no-btn-create">
                  <span><i class="el-icon-plus"></i> <span>追加</span></span>
                </span>
              </div>
            </td>
            <td v-if="item.approval_reservation[0]?.start_date">
              <el-tabs v-model="item.isActive" type="card" @tab-click="handleClick">
                <el-tab-pane v-for="itemApproval of item.approval_reservation" :key="itemApproval">
                  <template #label>
                    <span
                      >{{ formatFullDate(itemApproval?.start_date) ? formatFullDate(itemApproval?.start_date) : '' }} {{ JapaneseTilde() }}
                      {{
                        itemApproval?.end_date === '9999/12/31' ? '' : formatFullDate(itemApproval?.end_date) ? formatFullDate(itemApproval?.end_date) : ''
                      }}</span
                    >
                  </template>
                  <div class="tblUser-user tblUser-user-tab3">
                    <div class="sliderCurrent">
                      <div class="sliderCurrent-btn">
                        <button v-if="current_slide_number2[idx1] != 0" class="btn btn--prev" @click="prevSlide2(idx1)" @touchstart="prevSlide2(idx1)">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slide-control-bule.svg')" alt="" />
                        </button>
                        <button
                          v-if="
                            itemApproval?.approval_layer_num.length - (itemApproval?.approval_layer_num.length % 2 === 0 ? 2 : 1) != current_slide_number2[idx1]
                          "
                          class="btn btn--next"
                          @click="nextSlide2(idx1)"
                          @touchstart="nextSlide2(idx1)"
                        >
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slide-control-bule.svg')" alt="" />
                        </button>
                      </div>
                      <div class="sliderCurrent-wrap">
                        <div class="carouselWrap">
                          <ul class="carouselContent-lst">
                            <li
                              v-for="(item6, index) of itemApproval.approval_layer_num"
                              v-show="current_slide_number2[idx1] === index"
                              :key="item6"
                              :class="({ 'is-active-slide': current_slide_number2[idx1] === index }, { 'custom-li': isOnly(itemApproval.approval_layer_num) })"
                            >
                              <div class="carouselHead">{{ item6?.approval_layer_num }}次</div>
                              <div class="carouselContent">
                                <ul class="carouselContent-lst">
                                  <li v-for="item7 of item6?.approval_user" :key="item7">
                                    <p class="carouselContent-name">
                                      {{ item7?.user_name }} <span v-if="item7?.definition_label">（{{ item7?.definition_label }}）</span>
                                    </p>
                                    <p class="carouselContent-add">
                                      <span class="carouselContent-item">
                                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" /> </span
                                      >{{ item7?.org_label ? item7?.org_label : '(所属なし)' }}
                                    </p>
                                  </li>
                                </ul>
                              </div>
                            </li>
                            <li v-if="itemApproval?.approval_layer_num[current_slide_number2[idx1] + 1]">
                              <div class="carouselHead">{{ itemApproval?.approval_layer_num[current_slide_number2[idx1] + 1]?.approval_layer_num }}次</div>
                              <div class="carouselContent">
                                <ul class="carouselContent-lst">
                                  <li v-for="item8 of itemApproval?.approval_layer_num[current_slide_number2[idx1] + 1]?.approval_user" :key="item8">
                                    <p class="carouselContent-name">
                                      {{ item8?.user_name }} <span v-if="item8?.definition_label">（{{ item8?.definition_label }}）</span>
                                    </p>
                                    <p class="carouselContent-add">
                                      <span class="carouselContent-item">
                                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building02.svg')" alt="" /> </span
                                      >{{ item8?.org_label ? item8?.org_label : '(所属なし)' }}
                                    </p>
                                  </li>
                                </ul>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div class="tblUserStatus-btn mt-2">
                      <button class="btn btn--icon" data-toggle="dropdown"><span></span><span></span><span></span></button>
                      <div class="dropdown-menu dropdown-tools">
                        <span class="btn-show">
                          <span></span>
                          <span></span>
                          <span></span>
                        </span>
                        <ul>
                          <li @click="openModalUpdateAUser('updateIsRecord', itemApproval, item, item2?.start_date)">
                            <span class="item">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
                            </span>
                            <span class="text-label">編集</span>
                          </li>
                          <li @click="deleteAuthor(itemApproval, item)" @touchstart="deleteAuthor(itemApproval, item)">
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
                      <span style="cursor: pointer" @click="openModalUpdateAUser('createIsRecord', {}, item, item2?.start_date)"></span>
                    </span>
                  </template>
                </el-tab-pane>
              </el-tabs>
            </td>
          </tr>
        </tbody>
        <tr v-if="listTabAuthorizer.length === 0 && !isLoadDefault">
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
    <div v-if="listTabAuthorizer.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ pageSizelistTabAuthorizer }} 件</div>
      <PaginationTable
        :page-size="100"
        layout="prev, pager, next"
        :total="pageSizelistTabAuthorizer"
        :current-page="currentPage"
        @current-change="handleCurrentChange"
      />
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
import I01S01ModalApprovalEdit from './I01_S01_ModalApprovalEdit.vue';
import I01_S01_UserManagementServices from '../../../api/I01/I01_S01_UserManagement';
import { markRaw } from 'vue';
import PaginationTable from '@/components/PaginationTable';
import moment from 'moment';
export default {
  name: 'I01_S01_TabAuthorizer',
  components: {
    I01S01ModalApprovalEdit,
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
      isLoadDefault: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        isShowClose: true,
        destroyOnClose: true,
        closeOnClickMark: true,
        customClass: 'custom-dialog'
      },
      current_slide_number: [],
      current_slide_number2: [],
      isLodaingTabAuthorizer: false,
      currentPage: 1,
      total_pages: 0,
      pageSizelistTabAuthorizer: 0,
      listTabAuthorizer: [],
      userApproval: [],
      active_el: '1',
      active_el2: {},
      userApprovalLayerNum: [],
      userApprovalLayerNumKey: '',
      dataDelete: {},
      historyDataFilter: {},
      userID: '',
      nextPage: false,
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      isScroll: false,
      finishData: '',
      curentDate: '',
      changeNodata: false,
      isOnFilter: false
    };
  },
  watch: {
    objtab: function () {
      this.getScreenData();
    },
    changetab: function (value) {
      if (value === 3) {
        this.isOnFilter = this.isfilter;
        this.currentPage = 1;
      }
    }
  },
  mounted() {
    this.isOnFilter = this.isfilter;
    this.curentDate = this.formatFullDate(new Date());
    this.listTabAuthorizer = JSON.parse(this.decodeData(localStorage.getItem('dataStoreUserAuthTab'))) || [];
    this.getScreenData();
    this.emitter.on('change-AllTabs', ({ changeTabs }) => {
      if (changeTabs) {
        this.getScreenData();
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
      let content = document.querySelector('.tblUserTab3');
      content.scrollLeft = content.scrollLeft - 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblUserTab3');
      content.scrollLeft = content.scrollLeft + 200;
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
        let content = document.querySelector('.tblUserTab3');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    getScreenData() {
      this.historyDataFilter = {};
      I01_S01_UserManagementServices.getScreenData().then((res) => {
        this.userApproval = res.data.data.approval_title;
        this.userApprovalLayerNum = res.data.data.approval_layer_num;
        this.getListTabAuthorizer();
      });
    },
    filterForApproval(id) {
      this.active_el = id.definition_value;
      this.active_el2 = id ? id : this.userApproval[0];
      this.userApprovalLayerNum.forEach((element) => {
        if (id.definition_label === element.parameter_key) {
          this.userApprovalLayerNumKey = element.parameter_value;
          this.historyDataFilter.parameter_value = element.parameter_value;
        }
      });
      this.currentPage = 1;
      this.getListTabAuthorizer();
    },
    getListTabAuthorizer() {
      this.isLodaingTabAuthorizer = true;
      this.isLoadDefault = !this.isOnFilter;
      const data = {
        user_cd: this.historyDataFilter.user_cd ? this.historyDataFilter.user_cd : this.objtab?.user_cd ? this.objtab?.user_cd : '',
        user_name: this.historyDataFilter.user_name ? this.historyDataFilter.user_name : this.objtab?.user_name ? this.objtab?.user_name : '',
        approval_work_type: this.historyDataFilter.approval_work_type ?? this.active_el ? this.active_el : this.userApproval[0].definition_value,
        org_cd: this.historyDataFilter.org_cd ? this.historyDataFilter.org_cd : this.objtab?.org_cd ? this.objtab?.org_cd : '',
        parameter_value:
          this.historyDataFilter.parameter_value ??
          (this.userApprovalLayerNumKey === '' ? this.userApprovalLayerNum[0].parameter_value : this.userApprovalLayerNumKey),
        limit: 100,
        layer_num: this.historyDataFilter?.layer_num ? this.historyDataFilter?.layer_num : this.objtab?.layer_num,
        org_local: this.historyDataFilter?.org_local ? this.historyDataFilter?.org_local : this.objtab?.org_local,
        offset: this.finishData ? +this.finishData - 1 : this.nextPage ? this.currentPage - 1 : this.objtab?.offset
      };
      I01_S01_UserManagementServices.getapproval(data)
        .then((res) => {
          // if (!this.nextPage) {
          //   this.currentPage = 1;
          //   this.total_pages = 0;
          // }
          this.historyDataFilter = data;
          this.listTabAuthorizer = res?.data?.data?.records;
          this.listTabAuthorizer.forEach((element) => {
            element.isActive = '0';
            element.userNameDefinitionLabel = `${element.user_name} (${element.definition_label})`;
          });
          this.pageSizelistTabAuthorizer = res?.data?.data?.pagination?.total_items;
          this.current_slide_number = [];
          this.current_slide_number2 = [];
          for (let i = 0; i < this.pageSizelistTabAuthorizer; i++) {
            this.current_slide_number.push(0);
            this.current_slide_number2.push(0);
          }
          localStorage.setItem('dataStoreUserAuthTab', this.enCodeData(JSON.stringify(this.listTabAuthorizer)));
          this.nextPage = false;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 500));
          if (this.$refs.tblUser3 && this.isOnFilter) {
            this.$refs.tblUser3.scrollTop = 1;
          }
          this.js_pscroll();
          this.isOnFilter = false;
          this.isScroll = false;
          this.isLodaingTabAuthorizer = false;
          this.changeNodata = false;
          this.isLoadDefault = false;
        });
    },
    getClassChangeBRGRecord(id, removeEl, isActive) {
      if (isActive) {
        const className = 'classNameT3--';
        const trBackGround = document.getElementById(`${className}${id}`);
        if (removeEl) {
          trBackGround?.classList.remove('back-ground-active');
          trBackGround?.classList.add('back-ground-leave');
          setTimeout(() => {
            trBackGround?.classList.remove('back-ground-leave');
          }, 2500);
        } else {
          trBackGround?.classList.add('back-ground-active');
        }
      }
    },
    openModalUpdateAUser(typeModal, itemApproval, item, istartDate) {
      const isCurentDateinArray = this.formatFullDate(item?.approval_current[0]?.start_date) === this.curentDate ?? true;
      this.userID = item.user_cd;
      this.getClassChangeBRGRecord(item.user_cd, false, false);
      this.modalConfig = {
        ...this.modalConfig,
        title: '承認編集',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(I01S01ModalApprovalEdit),
        props: {
          typeModal: typeModal,
          item: item,
          itemApproval: itemApproval,
          wordType: this.active_el2.definition_value ? this.active_el2 : this.userApproval[0],
          istartDate: istartDate,
          isCurrentDateinArr: isCurentDateinArray
        },
        width: '70%',
        customClass: 'custom-dialog',
        destroyOnClose: true
      };
      this.changeTrueClassHeader();
    },
    closeModal() {
      this.getClassChangeBRGRecord(this.userID, true, false);
      this.userID = '';
      this.changeFalseClassHeader();
      // this.getListTabAuthorizer();
    },
    onFinishScreenModalAUser(e) {
      if (e === 1) {
        this.getClassChangeBRGRecord(this.userID, true, false);
        this.userID = '';
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      }
      if (e === 2) {
        this.getClassChangeBRGRecord(this.userID, true, true);
        this.userID = '';
        this.isScroll = true;
        this.finishData = this.currentPage;
        this.changeNodata = true;
        this.getListTabAuthorizer();
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      }
      if (e === 3) {
        this.deleteAuthor();
      } else {
        this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      }
    },
    nextSlide(idx) {
      this.current_slide_number[idx] += 2;
    },
    prevSlide(idx) {
      this.current_slide_number[idx] -= 2;
    },
    nextSlide2(idx) {
      this.current_slide_number2[idx] += 2;
    },
    prevSlide2(idx) {
      this.current_slide_number2[idx] -= 2;
    },
    handleCurrentChange(val) {
      this.isOnFilter = true;
      this.finishData = '';
      this.nextPage = true;
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.changeNodata = true;
        this.getListTabAuthorizer();
      } else {
        this.currentPage = val;
        this.changeNodata = true;
        this.getListTabAuthorizer();
      }
    },
    deleteAuthor(itemApproval, userCd) {
      this.userID = userCd.user_cd;
      this.getClassChangeBRGRecord(userCd.user_cd, false, false);
      this.changeTrueClassHeader();
      this.dataDelete = {
        user_cd: userCd.user_cd,
        approval_work_type: this.active_el ? this.active_el : this.userApproval[0].definition_value,
        start_date: moment(itemApproval.start_date).format('YYYY-M-D'),
        end_date: moment(itemApproval.end_date).format('YYYY-M-D')
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: `選択した${userCd.user_name}を完全に削除しますか？` }
      };
    },
    deleteMessage() {
      I01_S01_UserManagementServices.deleteapproval(this.dataDelete)
        .then((res) => {
          if (res) {
            this.getClassChangeBRGRecord(this.dataDelete.user_cd, true, true);
            this.isScroll = true;
            this.finishData = this.currentPage;
            this.$notify({ message: '正常に削除しました。', customClass: 'success', duration: 1500 });
            this.changeNodata = true;
            this.getListTabAuthorizer();
            this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        });
    },
    isOnly(arr) {
      if (arr.length === 1) return true;
      return false;
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.tblUser-user-tab3 {
  padding: 0px 25px 8px 0px !important;

  .sliderCurrent {
    padding-top: 4px;
  }
}
.el-carousel__item h3 {
  color: #475669;
  font-size: 18px;
  opacity: 0.75;
  line-height: 300px;
  margin: 0;
  text-align: center;
}

.el-carousel__item:nth-child(2n) {
  background-color: #99a9bf;
}

.el-carousel__item:nth-child(2n + 1) {
  background-color: #d3dce6;
}
.tblUser-user-custom {
  display: unset !important;
  padding: 0 !important;
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
.custom-li {
  width: 100% !important;
  min-width: 330px !important;
}
</style>
