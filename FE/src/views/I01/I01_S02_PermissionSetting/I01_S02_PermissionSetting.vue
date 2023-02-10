<template>
  <div class="wrapContent">
    <div v-loading="isLoadingPermissionSetting" class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo btnInfo--change">
          <I01S02PermissionSettingFilter @onFinishScreenFilter="onFinishFilter" />
        </div>
      </div>
      <div ref="tblSetting" class="tblSetting table-hg100-custom scrollbar" :class="listPermissionSetting.length === 1 ? 'tableBox-2' : ''" @scroll="onScroll">
        <div v-if="listPermissionSetting.length > 0" class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
        </div>
        <table class="tableCustom tableBox">
          <thead>
            <tr>
              <th>
                <div class="tblSetting-user">
                  <div class="checkAll">
                    <label class="custom-control custom-checkbox">
                      <input v-model="ischeckAll" class="custom-control-input" readonly type="checkbox" @click="checkAllCheckBox" />
                      <span :class="ischeckAll ? 'checkAll1' : ''"></span>
                      <span class="custom-control-indicator custom-checkbox-k1" :class="countCheckbox > 0 ? 'custom-checkbox-gmail' : ''">
                        <span :class="ischeckAll ? 'abcde' : ''" class="abc"></span
                      ></span>
                    </label>

                    <div class="checkAll-drop checkAll-drop2">
                      <span class="abcd"></span>
                      <button class="btn btn--arrow" data-toggle="dropdown">&nbsp;</button>
                      <div class="dropdown-menu dropdown-select">
                        <ul>
                          <li v-for="item of dataSelect" :key="item" :class="{ active: active_el == item.id }">
                            <button :disabled="item.disabled" class="button-custom" type="button" @click="check(item.id, listPermissionSetting)">
                              {{ item.name }}
                            </button>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </th>
              <th>
                <span class="tblSetting-user-label">ユーザー</span>
              </th>
              <th>適用期間</th>
              <th v-for="(item, index) of listScreenData" :key="item">
                <el-tooltip v-model="item.visible" class="item" placement="top">
                  <template #content>
                    <span>{{ item.role_name }}</span>
                  </template>
                  <span class="item-tooltip" @mouseenter="checkMouseEnter(index)" @mouseleave="checkMouseLeave(index)">{{ item.role_short_name }}</span>
                </el-tooltip>
              </th>
            </tr>
          </thead>
          <tbody v-if="listPermissionSetting.length > 0">
            <tr v-for="(item, indexItem1) of listPermissionSetting" :id="`className--${item.user_cd}`" :key="item" style="background: #fff; cursor: default">
              <td style="position: relative" @click="onCheckItem(item, indexItem1)">
                <span class="custom-checkbox-span"></span>
                <div class="tblSetting-info">
                  <div class="tblSetting-check">
                    <label class="custom-control custom-checkbox">
                      <input :checked="item.isCheck" class="custom-control-input" type="checkbox" />
                      <span class="custom-control-indicator"></span>
                    </label>
                  </div>
                </div>
              </td>
              <td>
                <div class="tblSetting-content">
                  <p class="tblSetting-name">{{ item.user_name }}</p>
                  <p class="tblSetting-code">ユーザコード：{{ item.user_cd }}</p>
                  <ul v-for="item2 of item?.organization" :key="item2" class="tblSetting-add tblSetting-add-custom">
                    <li class="tblSetting-add-custom1">
                      <span class="tblSetting-item">
                        <img class="svg" src="@/assets/template/images/icon_building.svg" alt="" />
                      </span>
                      {{ item2.org_label ? item2.org_label : '(所属なし)' }}
                    </li>
                    <li class="tblSetting-add-custom2">
                      <span class="tblSetting-item">
                        <img class="svg" src="@/assets/template/images/icon_namecard.svg" alt="" />
                      </span>
                      {{ item2.definition_label }}
                    </li>
                  </ul>
                </div>
              </td>
              <td v-if="item?.user_role.length > 0">
                <div v-for="(item3, index3) of item?.user_role" v-show="current_slide_number[indexItem1] === index3" :key="item3" class="period">
                  <div class="period-date">
                    <div class="slideDate">
                      <div class="slideDate-btn">
                        <button v-if="current_slide_number[indexItem1] != 0" class="btn btn--prev" @click="prevSlide(indexItem1)">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slide-control-bule-2.svg')" alt="" />
                        </button>
                        <button v-if="item?.user_role.length - 1 != current_slide_number[indexItem1]" class="btn btn--next" @click="nextSlide(indexItem1)">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slide-control-bule.svg')" alt="" />
                        </button>
                      </div>
                      <div class="slideDate-time">
                        <p :class="item3?.end_date === '9999-12-31' ? 'notEndDate' : ''">
                          {{ formatFullDate(item3?.start_date) }} ～
                          {{ item3?.end_date === '9999-12-31' ? '' : formatFullDate(item3?.end_date) }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="period-btn">
                    <button class="btn btn--icon" data-toggle="dropdown"><span></span><span></span><span></span></button>
                    <div
                      class="dropdown-menu dropdown-tools"
                      :class="
                        (indexItem1 + 1 === 100 || indexItem1 + 1 === listPermissionSetting.length) && !item3.isCheckUpdate && listPermissionSetting.length > 1
                          ? 'custom-dropdown-tools'
                          : ''
                      "
                    >
                      <span class="btn-show">
                        <span></span>
                        <span></span>
                        <span></span>
                      </span>
                      <ul>
                        <li @click="openModalCreateAUser('create', item, item.user_role[item.user_role.length - 1], index3, indexItem1)">
                          <span class="item">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" />
                          </span>
                          <span class="text-label">追加</span>
                        </li>
                        <li v-if="!item3.isCheckUpdate && item?.user_role.length" @click="openModalCreateAUser('update', item, item3, index3, indexItem1)">
                          <span class="item">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
                          </span>
                          <span class="text-label">編集</span>
                        </li>
                        <li v-if="!item3.isCheckDelete && item?.user_role.length" @click="deleteItem(item, indexItem1, item3)">
                          <span class="item">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" />
                          </span>
                          <span class="text-label">削除</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </td>
              <td v-else>
                <div class="period">
                  <div class="period-date">
                    <div class="slideDate"></div>
                  </div>
                  <div class="period-btn">
                    <button class="btn btn--icon" data-toggle="dropdown"><span></span><span></span><span></span></button>
                    <div class="dropdown-menu dropdown-tools">
                      <span class="btn-show">
                        <span></span>
                        <span></span>
                        <span></span>
                      </span>
                      <ul>
                        <li @click="openModalCreateAUser('create', item, item.user_role[item.user_role.length - 1], index3, indexItem1)">
                          <span class="item">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" />
                          </span>
                          <span class="text-label">追加</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </td>
              <td v-for="item4 of listScreenData" :key="item4">
                <span v-for="item5 of item?.user_role[current_slide_number[indexItem1]]?.role" :key="item5">
                  <img v-if="item5.role === item4.role" class="svg" src="@/assets/template/images/icon_checkSetting.svg" alt="" />
                </span>
              </td>
            </tr>
          </tbody>
          <tr v-if="listPermissionSetting.length === 0 && !isLoadDefault">
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
    </div>
    <div v-if="listPermissionSetting.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ pageSizePermissionSetting }} 件</div>
      <PaginationTable
        v-if="pageSizePermissionSetting > 0"
        :page-size="100"
        layout="prev, pager, next"
        :total="pageSizePermissionSetting"
        :current-page="currentPage"
        @current-change="handleCurrentChange"
      />
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`${modalConfig.customClass} I01-S02-custom handle-close`"
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
  </div>
</template>
<script>
import I01S02ModalRoleEditingSingle from './I01_S02_ModalRoleEditingSingle.vue';
import I01S02ModalRoleEditingMulti from './I01_S02_ModalRoleEditingMulti.vue';
import I01_S02_PermissionSetting from '../../../api/I01/I01_S02_PermissionSetting';
import PaginationTable from '@/components/PaginationTable';
import { markRaw } from 'vue';
import I01S02PermissionSettingFilter from './I01_S02_PermissionSettingFilter.vue';
import _ from 'lodash';

export default {
  name: 'I01_S02_PermissionSetting',
  components: {
    I01S02ModalRoleEditingSingle,
    I01S02ModalRoleEditingMulti,
    PaginationTable,
    I01S02PermissionSettingFilter
  },
  data() {
    return {
      isLoadDefault: true,
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
      props: {
        userFlag: 0,
        mode: 'singles',
        orgCdList: []
      },
      countCheckbox: 0,
      visible: null,
      isLoadingPermissionSetting: false,
      listPermissionSetting: [],
      listScreenData: [],
      pageSizePermissionSetting: 0,
      currentPage: 1,
      total_pages: 0,
      dataFilter: {},
      current_slide_number: [],
      active_el: 0,
      ischeckAll: false,
      updateCheckAll: [],
      numberCheck: 0,
      checkUpdate: true,
      today: new Date(),
      toDayDate: '',
      itemData: '',
      item3Data: '',
      userId: '',
      idChangeBgr: [],
      indexDeleteData: 0,
      index1: [],
      arrStartDateOld: [],
      dataSelect: [
        {
          id: 1,
          name: 'すべて選択',
          disabled: false
        },
        {
          id: 2,
          name: 'すべて解除',
          disabled: false
        },
        {
          id: 3,
          name: '一括先行予約追加',
          disabled: false
        }
      ],
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      curentDate: ''
    };
  },
  mounted() {
    this.curentDate = this.formatFullDate3(new Date());
    this.toDayDate = new Date();
    this.dataSelect.forEach((element) => {
      if (this.active_el === 0) {
        element.disabled = true;
      }
      if (element.id === 1) {
        element.disabled = false;
      }
    });
    this.emitter.emit('change-header', {
      title: 'ユーザ権限設定',
      icon: 'icon-i01.svg',
      isShowBack: false
    });
    this.getScreen();
  },
  unmounted() {
    localStorage.removeItem('currentPageI01S02');
    localStorage.removeItem('dataFilterI01S02');
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
      let content = document.querySelector('.tblSetting');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tblSetting');
      content.scrollLeft += 200;
    },
    onScroll: _.debounce(function ({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }
    }, 300),
    getScreen() {
      this.js_pscroll(0.5);
      this.isLoadingPermissionSetting = true;
      I01_S02_PermissionSetting.getScreen()
        .then((res) => {
          this.listScreenData = res.data.data.role;
          this.listScreenData.forEach((element) => {
            element.visible = false;
          });
          this.getPermissionSetting(false, null, true);
        })
        .catch((err) => {
          this.isLoadingPermissionSetting = false;
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
          if (err.response.data.message === '指定された画面に対するアクセス権がありません。') {
            this.$router.push('/home');
          }
        });
    },
    getPermissionSetting(isSearch, date, isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      if (isSearch) {
        localStorage.removeItem('currentPageI01S02');
        this.currentPage = 1;
      }
      const data2 = JSON.parse(localStorage.getItem('dataFilterI01S02'));
      this.dataFilter = data2 ? data2 : {};
      const page = localStorage.getItem('currentPageI01S02');
      if (page) {
        this.currentPage = +page;
      }
      this.isLoadingPermissionSetting = true;
      const data = {
        user_name: this.dataFilter.user_name ? this.dataFilter.user_name : '',
        user_cd: this.dataFilter.user_cd ? this.dataFilter.user_cd : '',
        org_cd: this.dataFilter.org_cd ? this.dataFilter.org_cd : '',
        reference_date: this.dataFilter.reference_date ? this.dataFilter.reference_date : '',
        director: this.dataFilter.director ? this.dataFilter.director : '',
        limit: 100,
        offset: isSearch ? this.dataFilter.offset : this.currentPage - 1
      };
      I01_S02_PermissionSetting.getList(data)
        .then((res) => {
          this.pageSizePermissionSetting = res.data.data.pagination.total_items;
          this.listPermissionSetting = res.data.data.records.map((item) => {
            return { ...item, isCheck: false };
          });
          for (let i = 0; i < this.listPermissionSetting.length; i++) {
            const index = this.findClosestDate(this.listPermissionSetting[i].user_role);
            this.current_slide_number.push(index);
          }
          if (this.index1.length > 0) {
            this.index1.forEach((element) => {
              const index = this.findCloestDate2(this.listPermissionSetting[element].user_role, date);
              this.current_slide_number[element] = index;
            });
          }
          this.countCheckbox = 0;
          this.checkDate(res.data.data.records);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(() => {
          if (this.userId) {
            this.getClassChangeBRG2(this.userId, true, true);
          }
          if (this.idChangeBgr.length > 0) {
            this.getClassChangeBRG2(this.idChangeBgr, true, true);
          }
          this.isLoadDefault = false;
          this.isLoadingPermissionSetting = false;
          setTimeout(() => {
            this.index1 = [];
          }, 50);
          this.userId = '';
        });
    },
    checkDate(listPermissionSetting) {
      listPermissionSetting.forEach((element) => {
        element.user_role.forEach((element2) => {
          element2.isCheckUpdate = false;
          element2.isCheckDelete = false;
          if (this.formatFullDate3(element2.start_date) < this.formatFullDate3(this.today)) {
            element2.isCheckUpdate = true;
          }
          if (this.formatFullDate3(element2.start_date) <= this.formatFullDate3(this.today)) {
            element2.isCheckDelete = true;
          }
        });
      });
    },
    checkAllCheckBox() {
      if (this.ischeckAll) {
        this.check(2);
        this.index1 = [];
      } else {
        this.check(1);
        for (let i = 0; i < this.listPermissionSetting.length; i++) {
          this.index1.push(i);
        }
      }
    },
    findClosestDate(dates) {
      const temp = dates.map((d) => {
        return d.end_date >= this.formatFullDate3(this.toDayDate);
      });
      const idx = temp.indexOf(true);
      return idx;
    },
    findCloestDate2(dates, dateForAE) {
      const tamDateForAE = this.formatFullDate3(dateForAE);
      const temp = dates.map((d) => {
        return d.start_date === tamDateForAE;
      });
      const idx = temp.indexOf(true);
      return idx;
    },
    handleCurrentChange(val) {
      this.index1 = [];
      localStorage.setItem('currentPageI01S02', val);
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.ischeckAll = false;
        this.active_el = 0;
        this.listPermissionSetting.forEach((element) => {
          element.isCheck = false;
        });
        this.numberCheck = 0;
        this.dataSelect.forEach((element) => {
          if (element.id === 1) {
            element.disabled = false;
          } else {
            element.disabled = true;
          }
        });
        this.current_slide_number = [];
        this.getPermissionSetting(false, null, false);
        if (this.$refs.tblSetting) {
          this.$refs.tblSetting.scrollTop = 0;
        }
      } else {
        this.active_el = 0;
        this.currentPage = val;
        this.ischeckAll = false;
        this.listPermissionSetting.forEach((element) => {
          element.isCheck = false;
        });
        this.numberCheck = 0;
        this.dataSelect.forEach((element) => {
          if (element.id === 1) {
            element.disabled = false;
          } else {
            element.disabled = true;
          }
        });
        this.current_slide_number = [];
        this.getPermissionSetting(false, null, false);
        if (this.$refs.tblSetting) {
          this.$refs.tblSetting.scrollTop = 0;
        }
      }
    },
    onFinishFilter(data) {
      this.dataFilter = data;
      this.ischeckAll = false;
      this.isLoadDefault = false;
      this.active_el = 0;
      this.dataSelect.forEach((element) => {
        if (element.id === 1) {
          element.disabled = false;
        } else {
          element.disabled = true;
        }
      });
      this.current_slide_number = [];
      this.getPermissionSetting(true, null, false);
      if (this.$refs.tblSetting) {
        this.$refs.tblSetting.scrollTop = 0;
      }
    },
    nextSlide(idx) {
      this.current_slide_number[idx] += 1;
    },
    prevSlide(idx) {
      this.current_slide_number[idx] -= 1;
    },
    checkMouseEnter(index) {
      this.listScreenData[index].visible = true;
    },
    checkMouseLeave(index) {
      this.listScreenData[index].visible = false;
    },
    check(id, listPermissionSetting) {
      if (id === 1) {
        this.countCheckbox = 0;
        this.active_el = 1;
        this.ischeckAll = true;
        this.updateCheckAll = [];
        this.listPermissionSetting.forEach((element) => {
          element.isCheck = true;
        });
        this.numberCheck = this.listPermissionSetting.length;
        this.dataSelect.forEach((element) => {
          element.disabled = false;
          if (element.id === 1) {
            element.disabled = true;
          }
        });
      }
      if (id === 2) {
        this.countCheckbox = 0;
        this.active_el = 0;
        this.ischeckAll = false;
        this.listPermissionSetting.forEach((element) => {
          element.isCheck = false;
        });
        this.numberCheck = 0;
        this.dataSelect.forEach((element) => {
          if (element.id === 1) {
            element.disabled = false;
          } else {
            element.disabled = true;
          }
        });
      }
      if (id === 3) {
        let dataCheck = [];
        let checkStartDate = [];
        let arrStartDateOld = [];
        listPermissionSetting.forEach((element, index) => {
          if (element.isCheck === true) {
            dataCheck.push(element);
            this.idChangeBgr.push(element.user_cd);
            element.user_role.forEach((element2) => {
              if (element2.start_date >= this.curentDate) {
                checkStartDate.push(element2.start_date);
              }
            });
            arrStartDateOld.push(
              this.listPermissionSetting[index]?.user_role[this.current_slide_number[index]]?.start_date
                ? this.listPermissionSetting[index]?.user_role[this.current_slide_number[index]]?.start_date
                : ''
            );
          }
        });
        this.active_el = 0;
        this.modalConfig = {
          ...this.modalConfig,
          title: 'ユーザ権限編集',
          isShowModal: true,
          component: markRaw(dataCheck.length === 1 ? I01S02ModalRoleEditingSingle : I01S02ModalRoleEditingMulti),
          props: {
            typeModal: dataCheck.length === 1 ? 'create' : '',
            userRole: dataCheck.length === 1 ? dataCheck[0].user_role[dataCheck[0].user_role.length - 1] : [],
            index3: 1,
            data: listPermissionSetting,
            isCurrentDateinArr: checkStartDate,
            arrStartDateOld: arrStartDateOld.toString()
          },
          width: '90%',
          destroyOnClose: true,
          isShowClose: true
        };
      }
    },
    onCheckItem(item, index) {
      if (this.index1.includes(index)) {
        this.index1.splice(this.index1.indexOf(index), 1);
      } else {
        this.index1.push(index);
        this.index1.sort((a, b) => {
          return a - b;
        });
      }
      this.countCheckbox = 0;
      this.listPermissionSetting.forEach((element) => {
        if (element.user_cd === item.user_cd) {
          element.isCheck = !element.isCheck;
        }
      });
      this.listPermissionSetting.forEach((element) => {
        if (element.isCheck) {
          this.countCheckbox = this.countCheckbox + 1;
        }
      });
      if (item.isCheck) {
        this.numberCheck += 1;
      } else {
        this.numberCheck -= 1;
      }
      if (this.numberCheck === this.listPermissionSetting.length) {
        this.ischeckAll = true;
        this.countCheckbox = 0;
      } else {
        this.ischeckAll = false;
      }
      if (this.numberCheck !== 0) {
        this.dataSelect.forEach((element) => {
          element.disabled = false;
        });
      } else {
        this.dataSelect.forEach((element) => {
          if (element.id === 1) {
            element.disabled = false;
          } else {
            element.disabled = true;
          }
        });
      }
    },
    openModalCreateAUser(typeModal, data, user_role, index3, index1) {
      const isCurentDateinArray = this.formatFullDate(data?.user_role[index3]?.start_date) === this.curentDate ?? true;
      this.index1 = [index1];
      this.userId = data.user_cd;
      this.getClassChangeBRG(data.user_cd, false, false);
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ権限編集',
        isShowModal: true,
        component: markRaw(I01S02ModalRoleEditingSingle),
        props: { typeModal: typeModal, data: data, userRole: user_role, index3: index3, isCurrentDateinArr: isCurentDateinArray },
        width: '80%',
        destroyOnClose: true,
        isShowClose: true,
        customClass: 'I01-S02-custom custom-dialog'
      };
    },
    deleteItem(item, index1, item3) {
      this.itemData = item;
      this.item3Data = item3;
      this.userId = item.user_cd;
      this.indexDeleteData = index1;
      this.getClassChangeBRG(item.user_cd, false, false);
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '41rem',
        customClass: 'custom-dialog modaldelete modal-fixed',
        props: {
          title: `${item.user_name}の${this.formatFullDate(item3.start_date)}～${
            item3.end_date === '9999-12-31' ? '' : this.formatFullDate(item3.end_date)
          }の権限設定を完全に削除しますか？`
        },
        isShowClose: false
      };
    },
    deleteMessage() {
      const data = {
        user_cd: this.itemData.user_cd,
        start_date: this.item3Data.start_date,
        end_date: this.item3Data.end_date
      };
      I01_S02_PermissionSetting.delete(data)
        .then(() => {
          this.getClassChangeBRG(this.itemData.user_cd, true, true);
          this.$notify({ message: '正常に削除しました', customClass: 'success', duration: 1500 });
          setTimeout(() => {
            this.current_slide_number[this.indexDeleteData] -= 1;
            this.getPermissionSetting(false);
            this.indexDeleteData = 0;
          }, 50);
          this.itemData = '';
          this.item3Data = '';
          this.modalConfig = { ...this.modalConfig, isShowModal: false };
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        });
    },
    closeModal() {
      setTimeout(() => {
        this.userId = '';
        this.idChangeBgr = [];
      }, 500);
    },
    onFinishScreenModalAUser(e, date) {
      if (e === 2) {
        this.getClassChangeBRG(this.userId, true, true);
        this.getClassChangeBRG(this.idChangeBgr, true, true);
        this.getPermissionSetting(false, date);
        this.ischeckAll = false;
        this.active_el = 0;
        this.dataSelect.forEach((element) => {
          if (element.id === 1) {
            element.disabled = false;
          } else {
            element.disabled = true;
          }
        });
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      } else {
        this.userId = '';
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.tblSetting {
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
.table-hg100-custom {
  background: none;
  box-shadow: unset;
}
.custom-checkbox-gmail {
  width: 19px;
  height: 18px;
  background: url(~@/assets/template/images/checkbox-i01-s02.svg) no-repeat !important;
  top: calc(50% - 7px);
}
.checkAll {
  padding-left: unset;
}
.checkAll-drop {
  position: relative;
  z-index: 9999;
  &.show {
    &:after {
      position: absolute;
      top: -2px;
      right: -3px;
      content: '';
      display: block;
      height: 28px;
      width: 58px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 4px;
    }
    .btn {
      &.btn--arrow {
        &:after {
          border-top: 6px solid #2d3033;
        }
      }
    }
  }
  .btn {
    &.btn--arrow {
      padding: 0;
      border: none;
      height: 24px;
      position: absolute;
      left: -5px;
      top: -2px;
      &:after {
        position: absolute;
        top: 10px;
        right: 5px;
        content: '';
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid #fff;
        z-index: 1;
      }
    }
    &:focus {
      box-shadow: none;
    }
  }
  .dropdown-select {
    margin: 0;
    padding: 0;
    background: #fff;
    border: 1px solid #727f88;
    box-sizing: border-box;
    border-radius: 4px;
    font-size: 0.875rem;
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.5);
    width: 140px;
    left: -30px !important;
    ul {
      overflow: hidden;
      border-radius: 4px;
      li {
        padding: 6px 16px;
        cursor: pointer;
        color: #99a5ae;
        font-weight: normal;
        &:hover {
          background: #f2f2f2;
        }
        &.active {
          background: #eef6ff;
          color: #2d3033;
          font-weight: 500;
        }
      }
    }
  }
}
.notEndDate {
  position: relative;
  right: 33px;
}
.custom-checkbox-k1 {
  cursor: pointer;
  &:hover {
    .abc {
      display: block;
    }
  }
}
.checkAll1 {
  width: 50px;
  height: 42px;
  background: rgba(255, 255, 255, 0.5);
  position: absolute;
  left: -7px;
  top: -12px;
  border-radius: 4px;
  opacity: 0.9;
}
.abc {
  width: 28px;
  height: 41px;
  background: rgba(255, 255, 255, 0.5);
  display: none;
  position: absolute;
  left: -7px;
  top: -13px;
  border-radius: 4px;
  opacity: 0.9;
}
.abcde {
  top: -12px;
}
.checkAll-drop2 {
  &:hover {
    .abcd {
      display: block;
    }
  }
  .abcd {
    width: 20px;
    height: 41px;
    background: rgba(255, 255, 255, 0.5);
    display: none;
    border-radius: 4px;
    position: absolute;
    left: 4px;
    opacity: 0.9;
    top: -11px;
  }
}
.nodata {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 14%;
  span {
    margin-bottom: 50px;
  }
}
.checkAll-drop {
  position: relative;
  z-index: 9999;
  &.show {
    &:after {
      position: absolute;
      top: -11px;
      right: -25px;
      content: '';
      display: block;
      height: 41px;
      width: 51px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 4px;
    }
    .btn {
      &.btn--arrow {
        &:after {
          border-top: 6px solid #2d3033;
        }
      }
    }
  }
}
.button-custom {
  width: 100%;
  height: 100%;
  text-align: start;
}
.tblSetting {
  margin: 0 24px;
  min-height: 400px;
  tr {
    th,
    td {
      vertical-align: middle;
    }
    th {
      width: 1rem;
      min-width: 50px;
      @media only screen and (max-width: 1366px) and (min-width: 768px) {
        &:nth-child(1) {
          min-width: 77px !important;
        }
      }
      @media only screen and (max-width: 1388px) and (min-width: 768px) {
        &:nth-child(1) {
          min-width: 77px !important;
        }
      }
      &:nth-child(1) {
        width: 1rem;
        min-width: 70px;
      }
      &:nth-child(2) {
        width: 1rem;
        min-width: 650px;
        @media (max-width: $viewport_desktop) {
          min-width: 400px;
        }
      }
      &:nth-child(3) {
        width: 1rem;
        min-width: 235px;
      }
      .item-tooltip {
        width: 16px;
        margin: 0 auto;
        display: block;
        white-space: normal;
        cursor: pointer;
      }
      .tblSetting-user {
        display: flex;
        align-items: center;
        justify-content: center;
        .tblSetting-user-label {
          margin-left: 20px;
        }
      }
    }
    td {
      text-align: center;
      &:nth-child(1) {
        text-align: center;
      }
      &:nth-child(2) {
        text-align: left;
      }
    }
    .period {
      display: flex;
      justify-content: space-between;
      align-items: center;
      .period-date {
        max-width: 400px;
        text-align: center;
      }
    }
    .period-btn {
      min-width: 42px;
      position: relative;
      margin-left: auto;
      .btn {
        margin-left: 10px;
        &.btn--icon {
          display: flex;
          justify-content: center;
          align-items: center;
          span {
            width: 4px;
            height: 4px;
            background: #448add;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
          }
        }
      }
      .dropdown-tools {
        left: inherit !important;
        right: 0 !important;
        transform: none !important;
        margin: 0;
        padding: 0;
        background: #ffffff;
        box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
        border-radius: 20px 24px 20px 20px;
        border: none;
        min-height: 42px;
        min-width: 130px;
        width: 130px;
        z-index: 1;
        .btn-show {
          box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
          background: #448add;
          padding: 0;
          width: 42px;
          height: 42px;
          border-radius: 50%;
          display: flex;
          justify-content: center;
          align-items: center;
          position: absolute;
          top: 0;
          right: 0;
          span {
            width: 4px;
            height: 4px;
            background: #fff;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
          }
        }
        ul {
          border-radius: 20px 24px 20px 20px;
          overflow: hidden;
          li {
            padding: 10px 46px 10px 20px;
            cursor: pointer;
            color: #448add;
            font-weight: 500;
            font-size: 0.875rem;
            display: flex;
            &:hover {
              background: #eef6ff;
            }
            .item {
              min-width: 18px;
              margin-right: 4px;
              text-align: center;
            }
          }
        }
      }
    }
  }

  .tblSetting-add-custom {
    width: 100%;
    display: flex;
    .tblSetting-add-custom1 {
      width: 60% !important;
    }
    .tblSetting-add-custom2 {
      width: 40% !important;
    }
  }
  .tblSetting-name {
    font-weight: 700;
    font-size: 20px;
  }
  .tblSetting-info {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    .tblSetting-check {
      .custom-control {
        padding-left: 19px;
      }
    }

    .tblSetting-content {
      width: calc(100% - 32px);
      padding-left: 16px;
      .tblSetting-name {
        font-weight: 700;
      }

      .tblSetting-add {
        display: flex;
        flex-wrap: wrap;
        li {
          margin-right: 12px;
          display: flex;
          &:last-child {
            margin-right: 0;
          }
          .tblSetting-item {
            min-width: 13px;
            margin: -1px 5px 0 0;
          }
        }
      }
    }
  }
  tr {
    &:last-child {
      td {
        .period-btn {
          .dropdown-tools {
            .btn-show {
              top: inherit;
              bottom: 0px !important;
            }
          }
        }
      }
    }
  }
}
.slideDate {
  padding: 0 48px;
  min-height: 36px;
  overflow: hidden;
  position: relative;
  .slideDate-btn {
    .btn {
      padding: 0;
      border: none;
      width: 36px;
      height: 36px;
      position: absolute;
      top: calc(50% - 18px);
      .svg {
        width: 36px;
        height: 36px;
      }
      &.btn--prev {
        left: 0;
      }
      &.btn--next {
        right: 0;
      }
    }
  }
  .slideDate-time {
    overflow: hidden;
    padding-top: 7px;
    color: #2d3033;
    p {
      min-width: 200px;
    }
  }
}
.custom-dropdown-tools {
  top: -85px !important;
  .btn-show {
    top: 85px !important;
  }
}
.custom-checkbox-span {
  position: absolute;
  width: 100%;
  height: 100%;
  background: transparent;
  top: 0;
  left: 0;
  z-index: 2;
  cursor: pointer;
}
</style>
