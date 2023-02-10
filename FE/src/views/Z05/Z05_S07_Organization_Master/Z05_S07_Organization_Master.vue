<template>
  <!-- name: 組織選択（マスタ管理用) -->
  <div class="modal-body-custom modal-body-Z05S07">
    <div id="msfa-notify-Z05S07"></div>
    <el-breadcrumb separator-class="el-icon-d-arrow-right" class="breadcrumb-custom">
      <ol class="breadcrumb">
        <el-breadcrumb-item
          v-for="(org, index) in listOrgLabel"
          :key="org.org_cd"
          class="breadcrumb-item-custom"
          aria-current="page"
          @click="(isValidLayerNum(org) ? getDataOrgByHierarchyNum(org.org_cd, true, org) : '') || onActiveOrgItem($event)"
        >
          {{ listLabelDisplayConfig.length > index ? listLabelDisplayConfig[index]?.org_label : listLabelDisplayConfigTitle[index]?.definition_label }}
        </el-breadcrumb-item>
      </ol>
    </el-breadcrumb>

    <div class="pb-2 date-content">
      <span>基準日: {{ formatFullDate(date) }}</span>
    </div>

    <!-- single Org -->
    <div v-if="mode === 'single' && userFlag === 0" class="single-block">
      <div class="block">
        <li class="block-title" hidden>
          <span>{{ listOrgLabel[listOrgLabel.length - 2]?.org_label }}</span>
        </li>
        <li
          v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])"
          class="block-define-name block-title border-none-bottom pd s07-list-group-item-action block-define-name-custom"
          @click="onToBack() || onActiveOrgItem($event)"
        >
          <div v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])" class="left">
            <el-button class="btn-circle btn-custom-none">
              <img v-svg-inline svg-inline class="svg icon-blue" :src="require(`@/assets/images/icon_arrow_upfloor.svg`)" alt="" />
            </el-button>
          </div>
          <div class="right">
            <span>上の階層に戻る</span>
          </div>
        </li>
        <div :class="`s07-list-group-wrapper ${loadingPage ? 'pre-loader h-452' : ''}`">
          <Preloader v-if="loadingPage" />
          <div
            v-else
            ref="lstOrgScroll"
            class="s07-list-group-inner"
            :class="!(listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])) ? 'bd-radius h-452' : ''"
          >
            <div class="s07-list-group">
              <li
                v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])"
                class="s07-list-group-item s07-list-group-item-action block-item"
                :class="isSelectedNotBelow ? 'active' : ''"
                @click="onActiveOrgItem"
              >
                <div class="left" @click="notSelected()">
                  <span class="text">（以下指定なし）</span>
                </div>
              </li>
              <li
                v-for="org in listOrganization"
                :key="org.org_cd"
                :class="org.selected ? 'active' : ''"
                class="s07-list-group-item s07-list-group-item-action block-item"
                @click="onActiveOrgItem"
              >
                <div
                  class="left"
                  @click="
                    org.children && org.children.length > 0 && isValidLayerNum(org)
                      ? getDataChildren(org.children[0].org_cd, org)
                      : isValidLayerNumSelect(org.layer_num)
                      ? selectedOrg(org)
                      : ''
                  "
                >
                  <span class="text">{{ org.org_label }}</span>
                </div>
              </li>
              <div v-if="listOrganization.length === 0 && !loadingPage" class="noData">
                <div class="noData-content">
                  <p class="noData-text">該当するデータがありません。</p>
                  <div class="noData-thumb">
                    <img src="@/assets/template/images/data/amico.svg" alt="" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- multi Org -->
    <div v-if="mode !== 'single' && userFlag === 0" class="single-block row">
      <!-- org -->
      <div class="col-md-7 mb-2 pr-0">
        <div class="block">
          <li class="block-title-multi bg-dark-custom">
            <div class="left"><span>組織</span></div>
            <div v-if="isValidLayerNumSelect(layerNumValid) && !loadingPage" class="right">
              <el-button class="btn-add btn btn-outline-primary btn-outline-primary--cancel" @click="onAddAllOrg()">
                <i class="el-icon-plus"><span>全て追加</span></i>
              </el-button>
            </div>
          </li>
          <li
            v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])"
            class="block-define-name border-none-bottom pd s07-list-group-item-action"
            @click="onToBack()"
          >
            <div v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])" class="left">
              <el-button class="btn-circle btn-custom-none">
                <img v-svg-inline svg-inline class="svg icon-blue" :src="require(`@/assets/images/icon_arrow_upfloor.svg`)" alt="" />
              </el-button>
            </div>
            <div class="right">
              <span>上の階層に戻る</span>
            </div>
          </li>
          <div :class="`s07-list-group-wrapper ${loadingPage ? 'pre-loader h-452' : ''}`">
            <Preloader v-if="loadingPage" />
            <div v-else class="s07-list-group-inner" :class="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2]) ? '' : 'h-452'">
              <div class="s07-list-group">
                <li
                  v-if="
                    listOrgLabel.length > 1 &&
                    isValidLayerNum(listOrgLabel[listOrgLabel.length - 2]) &&
                    isValidLayerNumSelect(listOrgLabel[listOrgLabel.length - 2]?.layer_num)
                  "
                  class="s01-list-group-item s01-list-group-item-action block-item cusor-default"
                >
                  <div class="left">
                    <span class="text">（以下指定なし）</span>
                  </div>
                  <div class="right">
                    <el-button
                      class="btn-add btn-add-w btn btn-outline-primary btn-outline-primary--cancel"
                      :disabled="checkOrgSelected(listOrgLabel[listOrgLabel.length - 2])"
                      @click="notSelectedMultiOrg()"
                    >
                      <i class="el-icon-plus">
                        <span>{{ checkOrgSelected(listOrgLabel[listOrgLabel.length - 2]) ? '追加済' : '追加' }}</span>
                      </i>
                    </el-button>
                  </div>
                </li>
                <li
                  v-for="item in listOrganization"
                  :key="item.org_cd"
                  class="s07-list-group-item s07-list-group-item-action block-item"
                  :class="item?.children?.length > 0 && isValidLayerNum(item) ? '' : 'cusor-default'"
                >
                  <div class="left" @click="item?.children?.length > 0 && isValidLayerNum(item) ? getDataChildrenMulti(item.children[0].org_cd, item) : ''">
                    <span class="text">{{ item.org_label }}</span>
                  </div>
                  <div v-if="isValidLayerNumSelect(item.layer_num)" class="right">
                    <el-button
                      class="btn-add btn-add-w btn btn-outline-primary btn-outline-primary--cancel"
                      :disabled="checkOrgSelected(item)"
                      @click="onAddItemOrg(item)"
                    >
                      <i class="el-icon-plus">
                        <span>{{ checkOrgSelected(item) ? '追加済' : '追加' }}</span>
                      </i>
                    </el-button>
                  </div>
                </li>
                <div v-if="listOrganization.length === 0 && !loadingPage" class="noData">
                  <div class="noData-content">
                    <p class="noData-text">該当するデータがありません。</p>
                    <div class="noData-thumb">
                      <img src="@/assets/template/images/data/amico.svg" alt="" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- org selected -->
      <div class="col-md-5 block-multi mb-2 pl-0 --right">
        <div class="block">
          <li class="block-title-multi border-0 bg-transparent"><span>選択リスト</span></li>
          <div :class="`s07-list-group-wrapper ${loadingPage ? 'pre-loader h-452' : ''}`">
            <Preloader v-if="loadingPage" />
            <div v-else class="s07-list-group-inner h-452 bd-top-w">
              <div class="s07-list-group">
                <li v-for="item in listOrganizationSelected" :key="item.org_cd" class="s07-list-group-item s07-list-group-item-action block-item cusor-default">
                  <div class="left">
                    <span class="text">{{ item.org_label }}</span>
                  </div>
                  <div class="right">
                    <el-button class="btn-circle" @click="onRemoveOrg(item)">
                      <i class="el-icon-close"></i>
                    </el-button>
                  </div>
                </li>
                <div v-if="listOrganizationSelected.length === 0" class="noData h-450">
                  <div class="noData-content">
                    <p class="noData-text">組織を選択して下さい。</p>
                    <div class="noData-thumb">
                      <img src="@/assets/template/images/data/amico.svg" alt="" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Single User -->
    <div v-if="userFlag === 1 && mode === 'single'" class="block-multi mb-2">
      <div class="row">
        <!-- org  -->
        <div class="col-md-6 mb-2">
          <div class="block">
            <li class="block-title-multi bg-dark-custom"><span>組織</span></li>
            <li
              v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])"
              class="block-define-name border-none-bottom pd s07-list-group-item-action block-define-name-custom"
              @click="onToBack() || onActiveOrgItem($event)"
            >
              <div v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])" class="left">
                <el-button class="btn-circle btn-custom-none">
                  <img v-svg-inline svg-inline class="svg icon-blue" :src="require(`@/assets/images/icon_arrow_upfloor.svg`)" alt="" />
                </el-button>
              </div>
              <div class="right">
                <span>上の階層に戻る</span>
              </div>
            </li>
            <div :class="`s07-list-group-wrapper ${loadingPage ? 'pre-loader h-452' : ''}`">
              <Preloader v-if="loadingPage" />
              <div
                v-else
                ref="lstOrgScroll"
                class="s07-list-group-inner"
                :class="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2]) ? '' : 'h-452'"
              >
                <div class="s07-list-group">
                  <li
                    v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])"
                    class="s07-list-group-item s07-list-group-item-action block-item arrow-fill"
                    :class="isSelectedNotBelow ? 'active' : ''"
                    @click="onActiveOrgItem"
                  >
                    <div class="left" @click="notSelected()">
                      <span class="text">（以下指定なし）</span>
                    </div>
                  </li>
                  <li
                    v-for="item in listOrganization"
                    :key="item.org_cd"
                    :class="item.selected ? ' active' : ''"
                    class="s07-list-group-item s07-list-group-item-action block-item arrow-fill"
                    @click="onActiveOrgItem"
                  >
                    <div
                      class="left"
                      @click="
                        item?.children?.length > 0 && isValidLayerNum(item)
                          ? getDataChildrenMulti(item.children[0].org_cd)
                          : getUserByOrganization(false, false, item.org_cd, item)
                      "
                    >
                      <span class="text">{{ item.org_label }}</span>
                    </div>
                  </li>
                  <div v-if="listOrganization.length === 0 && !loadingPage" class="noData">
                    <div class="noData-content">
                      <p class="noData-text">該当するデータがありません。</p>
                      <div class="noData-thumb">
                        <img src="@/assets/template/images/data/amico.svg" alt="" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- user -->
        <div class="col-md-6 mb-2">
          <div class="block">
            <li class="block-title-multi bg-dark-custom">
              <div class="left"><span>ユーザ</span></div>
            </li>
            <div :class="`s07-list-group-wrapper ${loadingUser ? 'pre-loader h-452' : ''}`">
              <Preloader v-if="loadingUser" />
              <div v-else ref="lstUserScroll" class="s07-list-group-inner-user h-452">
                <div class="s07-list-group-user">
                  <li
                    v-for="item in listUserByOrg"
                    :key="item.user_cd"
                    :class="item.selected ? 'active' : ''"
                    class="s07-list-group-item-user s07-list-group-item-action block-item"
                    @click="onActiveUserItem"
                  >
                    <div class="left" @click="isValidLayerNumSelect(item.layer_num) ? convertDataUser(false, item) : ''">
                      <span class="text">{{ item.user_name }} ({{ item.definition }})</span>
                    </div>
                  </li>
                  <div v-if="listUserByOrg.length === 0 && !loadingUser" class="noData h-450">
                    <div class="noData-content">
                      <p class="noData-text">該当するデータがありません。</p>
                      <div class="noData-thumb">
                        <img src="@/assets/template/images/data/amico.svg" alt="" />
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

    <!-- Multi User -->
    <div v-if="userFlag === 1 && mode !== 'single'" class="block-multi mb-2">
      <div class="row multi-user-container">
        <!-- org  -->
        <div class="col-org mb-2">
          <div class="block">
            <li class="block-title-multi bg-dark-custom"><span>組織</span></li>
            <li
              v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])"
              class="block-define-name border-none-bottom pd s07-list-group-item-action"
              @click="onToBack() || onActiveOrgItem($event)"
            >
              <div v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])" class="left">
                <el-button class="btn-circle btn-custom-none">
                  <img v-svg-inline svg-inline class="svg icon-blue" :src="require(`@/assets/images/icon_arrow_upfloor.svg`)" alt="" />
                </el-button>
              </div>
              <div class="right">
                <span>上の階層に戻る</span>
              </div>
            </li>
            <div :class="`s07-list-group-wrapper ${loadingPage ? 'pre-loader h-452' : ''}`">
              <Preloader v-if="loadingPage" />
              <div
                v-else
                ref="lstOrgScroll"
                class="s07-list-group-inner"
                :class="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2]) ? '' : 'h-452'"
              >
                <div class="s07-list-group">
                  <li
                    v-if="listOrgLabel.length > 1 && isValidLayerNum(listOrgLabel[listOrgLabel.length - 2])"
                    class="s07-list-group-item s07-list-group-item-action block-item arrow-fill"
                    :class="isSelectedNotBelow ? 'active' : ''"
                    @click="onActiveOrgItem"
                  >
                    <div class="left" @click="notSelected()">
                      <span class="text">（以下指定なし）</span>
                    </div>
                  </li>
                  <li
                    v-for="item in listOrganization"
                    :key="item.org_cd"
                    :class="item.selected ? ' active' : ''"
                    class="s07-list-group-item s07-list-group-item-action block-item arrow-fill"
                    @click="onActiveOrgItem"
                  >
                    <div
                      class="left"
                      @click="
                        item?.children?.length > 0 && isValidLayerNum(item)
                          ? getDataChildrenMulti(item.children[0].org_cd, item)
                          : getUserByOrganization(false, false, item.org_cd, item)
                      "
                    >
                      <span class="text">{{ item.org_label }}</span>
                    </div>
                  </li>
                  <div v-if="listOrganization.length === 0 && !loadingPage" class="noData">
                    <div class="noData-content">
                      <p class="noData-text">該当するデータがありません。</p>
                      <div class="noData-thumb">
                        <img src="@/assets/template/images/data/amico.svg" alt="" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- user -->
        <div class="col-user mb-2 pr-0">
          <div class="block">
            <li class="block-title-multi bg-dark-custom">
              <div class="left"><span>ユーザ</span></div>
              <div v-if="isValidLayerNumSelect(layerNumValid)" class="right">
                <el-button class="btn-add btn btn-outline-primary btn-outline-primary--cancel" @click="onAddAllUser()">
                  <i class="el-icon-plus"><span>全て追加</span></i>
                </el-button>
              </div>
            </li>
            <div :class="`s07-list-group-wrapper ${loadingUser ? 'pre-loader h-452' : ''}`">
              <Preloader v-if="loadingUser" />
              <div v-else class="s07-list-group-inner-user h-452">
                <div class="s07-list-group-user">
                  <li v-for="item in listUserByOrg" :key="item.user_cd" class="s07-list-group-item-user s07-list-group-item-action block-item cusor-default">
                    <div class="left">
                      <span class="text">{{ item.user_name }} ({{ item.definition }})</span>
                    </div>
                    <div v-if="isValidLayerNumSelect(layerNumValid)" class="right">
                      <el-button
                        class="btn-add btn-add-w btn btn-outline-primary btn-outline-primary--cancel"
                        :disabled="checkUserSelected(item)"
                        @click="onAddItemUser(item)"
                      >
                        <i class="el-icon-plus">
                          <span>{{ checkUserSelected(item) ? '追加済' : '追加' }}</span>
                        </i>
                      </el-button>
                    </div>
                  </li>
                  <div v-if="listUserByOrg.length === 0 && !loadingUser" class="noData h-450">
                    <div class="noData-content">
                      <p class="noData-text">該当するデータがありません。</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- user selected -->
        <div class="col-user-select block-multi mb-2 pl-0 --right">
          <div class="block">
            <li class="block-title-multi border-0 bg-transparent"><span>選択リスト</span></li>
            <div :class="`s07-list-group-wrapper ${loadingUserSelected ? 'pre-loader h-452' : ''}`">
              <Preloader v-if="loadingUserSelected" />
              <div v-else class="s07-list-group-inner-user h-452 bd-top-w">
                <div class="s07-list-group-user">
                  <li
                    v-for="item in listUserByOrgSelected"
                    :key="item.user_cd"
                    class="s07-list-group-item-user s07-list-group-item-action block-item cusor-default"
                  >
                    <div class="left" style="flex-direction: column">
                      <div class="org-text-nomal">{{ item.org_label }}</div>
                      <div class="text person-text-bold">{{ item.user_name }}</div>
                    </div>
                    <div class="right">
                      <el-button class="btn-circle" @click="onRemoveUser(item)">
                        <i class="el-icon-close"></i>
                      </el-button>
                    </div>
                  </li>
                  <div v-if="listUserByOrgSelected.length === 0" class="noData h-450">
                    <div class="noData-content">
                      <p class="noData-text">ユーザを選択して下さい。</p>
                      <div class="noData-thumb">
                        <img src="@/assets/template/images/data/amico.svg" alt="" />
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

    <div class="block-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
      <button type="button" class="btn btn-primary" :disabled="checkDisabledSubmit()" @click="onResultData">決定</button>
    </div>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
import moment from 'moment';
import Z05_S07_OrganizationMasterService from '@/api/Z05/Z05_S07_OrganizationMasterService';

export default {
  name: 'Z05_S07_OrganizationMaster',
  props: {
    // select user = 1 | org = 0
    userFlag: {
      type: Number,
      required: true,
      default: 0
    },

    // mode single = 'single' | multi = 'multiple'
    mode: {
      type: String,
      required: true,
      default: 'single'
    },

    // userSelectFlag = 1 user select full (1->5) || = 0 select layer_min -> layer_max
    userSelectFlag: {
      type: Number,
      required: true,
      default: 1
    },

    date: {
      type: String,
      required: true,
      default: moment(new Date()).format('YYYY/M/D')
    },

    // max hierarchy
    layerMax: {
      type: Number,
      required: false,
      default: 5
    },

    // min hierarchy
    layerMin: {
      type: Number,
      required: false,
      default: 1
    },

    // require while org selected | user selected | userSelectFlag = 0
    orgCdList: {
      type: Array,
      required: false,
      default() {
        return [];
      }
    },

    userCdList: {
      type: Array,
      required: false,
      default() {
        return [
          // {
          //   org_cd:  org_cd,
          //   user_cd:  user_cd
          // }
        ];
      }
    }
  },
  emits: { onFinishScreen: null },
  data() {
    return {
      loadingPage: false,
      loadingUser: false,
      loadingUserSelected: false,
      lstDataOrg: [], // all data
      lstDataOrgFlat: [], // all data flat
      listOrgLabel: [], // list hierarchy org

      listOrganization: [],
      listOrganizationSelected: [],

      listUserByOrg: [],
      listUserByOrgSelected: [],

      orgSelected: {},
      userSelected: {},

      listLabelDisplay: [], // display label (old)
      listLabelDisplayConfigTitle: [], // display label title (new)
      listLabelDisplayConfig: [], // display label (new)

      // min max layer_num
      layer_min: 1,
      layer_max: 1,

      paramGetUserOld: {},

      layerNumValid: 1, // layer current org
      layerNumCheckDefault: 1, // layer child current org (checked select （以下選択なし) )

      isSelectedNotBelow: false,

      maxLayerData: 1
    };
  },
  mounted() {
    document.querySelectorAll('.notation').forEach((el) => el.remove());
    this.getOrganizations(true);
  },

  methods: {
    isValidLayerNum(org) {
      if (org.layer_num < this.layer_max) {
        return true;
      }
      return false;
    },

    isValidLayerNumSelect(layerNumValid) {
      if (this.layer_min <= layerNumValid && layerNumValid <= this.layer_max) {
        return true;
      }
      return false;
    },

    // add when select multi full hierarchy num
    flaternDataOrg(node) {
      this.lstDataOrgFlat.push(node);
      if (+this.maxLayerData < +node.layer_num) {
        this.maxLayerData = +node.layer_num;
      }
      if (node?.children?.length > 0) {
        for (let i = 0; i < node.children.length; i++) {
          this.flaternDataOrg(node.children[i]);
        }
      }
    },

    getAddressOrg(data, label = null, name = null) {
      let address = [];
      for (let item of data) {
        let newData = {};
        newData = { ...item };
        if (label) {
          newData.full_label = `${label}/${item.org_label}`;
        } else {
          newData.full_label = `${item.org_label}`;
        }
        if (name) {
          newData.full_name = `${name}/${item.org_name}`;
        } else {
          newData.full_name = `${item.org_name}`;
        }

        if (item?.children?.length > 0) {
          let child = this.getAddressOrg(item?.children, newData.full_label, newData.full_name);
          newData.children = child;
        }
        address.push(newData);
      }
      return address;
    },

    // get all Org
    getOrganizations(isLoadDefault) {
      let params = {
        date: this.date
      };
      this.loadingPage = isLoadDefault ? true : false;
      this.loadingUser = isLoadDefault ? (this.userFlag == 1 ? true : false) : false;
      this.loadingUserSelected = isLoadDefault ? (this.userFlag == 1 ? true : false) : false;

      this.lstDataOrg = [];
      this.lstDataOrgFlat = [];
      this.listLabelDisplayConfig = [];
      this.listLabelDisplayConfigTitle = [];
      this.listOrganizationSelected = [];
      this.listOrgLabel = [];
      this.listLabelDisplay = [];
      this.listUserByOrgSelected = [];
      this.listUserByOrg = [];

      Z05_S07_OrganizationMasterService.getOrganizations(params)
        .then(async (res) => {
          this.listLabelDisplayConfigTitle = res?.data?.data?.title; // get from db
          this.loadingPage = false;
          this.lstDataOrg = this.getAddressOrg(res?.data?.data?.org_obj);

          for (let i = 0; i < this.lstDataOrg.length; i++) {
            this.flaternDataOrg(this.lstDataOrg[i]);
          }

          // min max layer_num
          this.layer_min = this.userSelectFlag === 1 ? 1 : this.layerMin;
          this.layer_max = this.userSelectFlag === 1 ? this.maxLayerData : this.layerMax < this.layerMin ? this.layerMin : this.layerMax;

          if (this.lstDataOrg.length > 0) {
            if (this.orgCdList.length > 0 && !(isLoadDefault && this.mode !== 'single' && this.userFlag === 1)) {
              let num = 5;
              let code_init = this.orgCdList[0];
              let orgCrrent = '';
              for (let i = 0; i < this.lstDataOrgFlat.length; i++) {
                let element = this.lstDataOrgFlat[i];
                if (this.orgCdList.some((e) => e === element.org_cd)) {
                  this.layerNumValid = element.layer_num;
                  if (num > element.layer_num) {
                    num = element.layer_num;
                    code_init = element.org_cd;
                    orgCrrent = element;
                  }
                }
              }

              // check select (以下選択なし) when org selected is not last layer in mode single org
              if (this.mode === 'single' && this.userFlag === 0 && this.layerNumValid < this.layer_max && orgCrrent && orgCrrent.children.length > 0) {
                this.layerNumCheckDefault = orgCrrent.children[0].layer_num;
                await this.getDataChildren(orgCrrent.children[0].org_cd, orgCrrent);
                this.isSelectedNotBelow = true;
              } else {
                await this.getDataChildren(code_init, null, isLoadDefault);
              }

              this.listOrganizationSelected = [];
              for (let i = 0; i < this.lstDataOrgFlat.length; i++) {
                let element = this.lstDataOrgFlat[i];
                element.selected = false;
                if (this.mode === 'single') {
                  if (code_init === element.org_cd) {
                    element.selected = true;
                    this.listOrganizationSelected.push(element);
                    this.removeLabel(element.layer_num);
                    this.addLabel(element);
                  }
                } else {
                  if (this.orgCdList.some((e) => e === element.org_cd)) {
                    element.selected = true;
                    this.listOrganizationSelected.push(element);
                  }
                }
              }

              /* */

              if (this.userFlag === 1) {
                this.getUserByOrganization(isLoadDefault, false, code_init);
                if (this.mode !== 'single') {
                  this.listUserByOrgSelected = [];
                  for (let i = 0; i < this.orgCdList.length; i++) {
                    this.getUserByOrganization(isLoadDefault, true, this.orgCdList[i]);
                  }
                }
              }
            } else {
              this.listOrgLabel = [this.lstDataOrg[0]];
              this.listOrganization = this.lstDataOrg;

              if (this.userFlag === 1) {
                this.getUserByOrganization(isLoadDefault, false, '');
                this.listUserByOrgSelected = [];
              }

              this.convertDataOrg();
            }
          }
        })
        .catch((err) => {
          this.loadingPage = false;
          this.loadingUser = false;
          this.loadingUserSelected = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S07', idNodeNotify: 'msfa-notify-Z05S07' });
        })
        .finally(() => {
          this.onActiveOrgItem(null, isLoadDefault);
        });
    },

    // get tree org selected
    getDataOrgLabel(node, code) {
      if (node.org_cd === code) {
        this.listOrgLabel.push(node);
        return true;
      } else {
        if (node?.children) {
          for (let i = 0; i < node?.children.length; i++) {
            if (this.getDataOrgLabel(node.children[i], code)) {
              this.listOrgLabel.push(node);
              return true;
            }
          }
        }
      }
      return false;
    },

    // get data on layer
    getDataOrgByHierarchyNum(code, isLabel, org) {
      let index = this.listOrgLabel.findIndex((x) => x.org_cd === code);
      if (this.userFlag === 1) {
        this.listOrganizationSelected = [];
      }

      if (isLabel) {
        this.isSelectedNotBelow = false;
        this.paramGetUserOld = {};

        if (this.userFlag === 1) {
          this.listUserByOrg = [];
          this.listOrganizationSelected = [];
          if (index > 0) {
            this.getUserByOrganization(false, false, this.listOrgLabel[index - 1].org_cd);
          } else {
            this.getUserByOrganization(false, false, '');
          }
        }

        this.getDataChildren(this.listOrgLabel[index].org_cd, null, true); // label of org
        this.layerNumValid = org?.layer_num;

        document.querySelectorAll('.s07-list-group-user .notation').forEach((el) => el.remove());
      } else {
        this.listOrganization = this.listOrgLabel[index - 1].children;
      }

      // set data parrent to selected
      if (this.mode === 'single') {
        this.listOrganizationSelected = [];
        let length = this.listLabelDisplay.length;
        if (length > 1) {
          this.listOrganizationSelected.push(this.listLabelDisplay[length - 2]);
        }
      }
    },

    async getDataChildren(code, orgParent, isLoadDefault) {
      this.isSelectedNotBelow = false;
      this.listOrgLabel = [];
      await this.lstDataOrg.forEach(async (e) => {
        this.getDataOrgLabel(e, code);
      });

      this.listOrgLabel = this.listOrgLabel.reverse();
      this.listLabelDisplay = [];
      let length = this.listOrgLabel.length;
      for (let i = 0; i < length; i++) {
        this.listLabelDisplay.push(this.listOrgLabel[i]);
      }

      if (isLoadDefault) {
        this.listLabelDisplayConfig = [];
        for (let i = 0; i < length - 1; i++) {
          this.addLabel(this.listOrgLabel[i]);
        }
      } else {
        if (orgParent) {
          this.removeLabel(orgParent.layer_num);
          this.addLabel(orgParent);
          if (this.userFlag === 1) {
            this.getUserByOrganization(false, false, orgParent.org_cd);
          }
        } else {
          let childCode = this.lstDataOrgFlat.find((x) => x.org_cd === code);
          let orgParentLabel = this.getParrent(childCode);

          if (orgParentLabel) {
            this.removeLabel(orgParentLabel.layer_num);
            this.addLabel(orgParentLabel);
            if (this.userFlag === 1) {
              this.getUserByOrganization(false, false, orgParentLabel.org_cd);
            }
          }
        }
      }

      if (length === 0) {
        this.listOrganization = [];
      }
      if (length === 1) {
        this.listOrganization = this.lstDataOrg;
      }
      if (length !== 0 && length !== 1) {
        this.getDataOrgByHierarchyNum(code);
      }

      this.listOrganization.forEach((element) => {
        element.selected = false;
      });
    },

    // mode multi org
    getDataChildrenMulti(code, org) {
      this.isSelectedNotBelow = false;

      if (this.userFlag === 1) {
        this.layerNumValid = org?.layer_num;
        this.listOrganizationSelected = [];
      } else {
        this.layerNumValid = org?.layer_num + 1;
        if (this.mode === 'single') {
          this.listOrganizationSelected = [];
        }
      }
      this.getDataChildren(code);
      this.listUserByOrg = [];
    },

    getParrent(childCode) {
      let data = this.lstDataOrgFlat.filter((x) => x.layer_num === childCode.layer_num - 1);
      let parent = null;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.children.forEach((x) => {
          if (x.org_cd === childCode.org_cd) {
            parent = element;
            return parent;
          }
        });
      }
      return parent;
    },

    convertDataOrg(orgCode, org) {
      this.listOrganizationSelected = [];
      if (org && Object.keys(org).length > 0) {
        this.layerNumValid = org?.layer_num;
      }
      if (this.userFlag === 1) {
        this.listOrganization.forEach((element) => {
          element.selected = false;
          if (element.org_cd === orgCode) {
            if (this.userSelectFlag === 1 && org && Object.keys(org).length > 0) {
              if (this.isValidLayerNumSelect(org.layer_num)) {
                element.selected = true;
                this.listOrganizationSelected.push(element);
              }
            } else {
              element.selected = true;
              this.listOrganizationSelected.push(element);
            }
          }
        });
      } else {
        this.listOrganization.forEach((element) => {
          element.selected = false;
          if (this.orgCdList.some((e) => e === element.org_cd)) {
            element.selected = true;
            this.listOrganizationSelected.push(element);
          }
        });
      }
    },

    //org selected, single org mode
    selectedOrg(item) {
      this.isSelectedNotBelow = false;
      this.listOrganizationSelected = [];
      this.listOrganization.forEach((element) => {
        if (element.org_cd === item.org_cd) {
          element.selected = !element.selected;
          if (element.selected) {
            this.listOrganizationSelected.push(element);
            this.addLabel(element);
          } else {
            let length = this.listLabelDisplay.length;
            if (length > 1) {
              this.listOrganizationSelected.push(this.listLabelDisplay[length - 2]);
            }
            this.removeLabel(element.layer_num);
          }
        } else {
          element.selected = false;
        }
      });
    },

    addLabel(org) {
      this.listLabelDisplayConfig = this.listLabelDisplayConfig.filter((x) => x.layer_num < org.layer_num);
      this.listLabelDisplayConfig.push(org);
    },

    removeLabel(num) {
      this.listLabelDisplayConfig = this.listLabelDisplayConfig.filter((x) => x.layer_num < num);
    },

    //to back
    onToBack() {
      document.querySelectorAll('.s07-list-group .notation').forEach((el) => el.remove());
      document.querySelectorAll('.s07-list-group-user .notation').forEach((el) => el.remove());
      this.isSelectedNotBelow = false;

      if (this.mode === 'single') {
        this.listOrganizationSelected = [];
      }
      if (this.userFlag === 1) {
        this.listUserByOrg = [];
        this.listOrganizationSelected = [];
      }
      let length = this.listOrgLabel.length;
      let num = this.listOrgLabel[length - 1].layer_num;
      this.removeLabel(num);
      if (length > 1) {
        this.removeLabel(num - 1);
        if (this.userFlag === 1) {
          this.layerNumValid = this.listOrgLabel[length - 3]?.layer_num;
          if (!this.layerNumValid) {
            this.getUserByOrganization(false, false, '');
            this.layerNumValid = 1;
          }
        } else {
          this.layerNumValid = this.listOrgLabel[length - 2]?.layer_num;
        }
        let semiLastIndex = this.listOrgLabel[length - 2].org_cd;
        this.getDataChildren(semiLastIndex);
      }
    },

    // checked single org, user selected when click（以下指定なし）
    notSelected() {
      document.querySelectorAll('.s07-list-group-user .notation').forEach((el) => el.remove());
      this.isSelectedNotBelow = true;

      let length = this.listOrgLabel.length;
      let num = this.listOrgLabel[length - 1].layer_num;
      this.removeLabel(num);
      if (length > 0) {
        this.layerNumValid = this.listOrgLabel[length - 1]?.layer_num;
      }

      if (this.userFlag === 1) {
        this.paramGetUserOld = {};
        let lengthOrgChild = this.listOrganization.length;
        if (lengthOrgChild > 0) {
          this.listOrganization.forEach((x) => {
            x.selected = false;
          });
        }
        this.listUserByOrg = [];
        this.listOrganizationSelected = [];
        this.getUserByOrganization(false, false, this.listOrgLabel[length - 2].org_cd, this.listOrgLabel[length - 2]);
        this.isSelectedNotBelow = true;
      } else {
        let lengthOrgChild = this.listLabelDisplay.length;
        if (lengthOrgChild > 1) {
          let lengSelected = this.listOrganizationSelected.length;
          for (let i = lengSelected - 1; i >= 0; i--) {
            const element = this.listOrganizationSelected[i];
            if (this.listOrganization.some((e) => e.org_cd === element.org_cd)) {
              this.listOrganizationSelected.splice(i, 1);
              let index = this.listOrganization.findIndex((j) => j.org_cd === element.org_cd);
              this.listOrganization[index].selected = false;
            }
          }
          if (
            !this.listOrganizationSelected.some((x) => x.org_cd === this.listLabelDisplay[lengthOrgChild - 2].org_cd) &&
            this.isValidLayerNumSelect(this.listLabelDisplay[lengthOrgChild - 2]?.layer_num)
          ) {
            this.listOrganizationSelected.push(this.listLabelDisplay[lengthOrgChild - 2]);
          }
        }
      }
    },

    // checked multi org  selected when click（以下指定なし）
    notSelectedMultiOrg() {
      let length = this.listOrgLabel.length;
      let orgParent = this.listOrgLabel[length - 2];
      if (!this.checkOrgSelected(orgParent)) {
        this.onAddItemOrg(orgParent);
      }
    },

    // Check Org
    checkOrgSelected(item) {
      let index = this.listOrganizationSelected.findIndex((x) => x.org_cd === item.org_cd);

      return index > -1 ? true : false;
    },

    onAddItemOrg(item) {
      if (!this.checkOrgSelected(item)) {
        this.listOrganizationSelected.push(item);
      }
    },

    onAddAllOrg() {
      this.listOrganization.forEach((e) => {
        if (!this.checkOrgSelected(e)) {
          this.listOrganizationSelected.push(e);
        }
      });
    },

    onRemoveOrg(item) {
      let index = this.listOrganizationSelected.findIndex((x) => x.org_cd === item.org_cd);
      this.listOrganizationSelected.splice(index, 1);
    },

    checkOrgChild() {
      for (let i = 0; i < this.listOrganization.length; i++) {
        if (this.listOrganization[i]?.children?.length > 0) {
          return false;
        }
      }
      return true;
    },

    // get User by org
    getUserByOrganization(isLoadDefault, isLoadAllUserByOrgs, orgCode, org) {
      if (!isLoadDefault) {
        this.isSelectedNotBelow = false;
      }

      let params = {
        org_cd: orgCode,
        date: this.date
      };
      this.paramGetUserOld = params;
      this.listUserByOrg = [];
      this.loadingUser = true;

      if (this.mode === 'single') {
        if (this.listUserByOrgSelected.length > 0) {
          if (this.listUserByOrgSelected[0].org_cd !== orgCode) {
            this.listUserByOrgSelected = [];
          }
        }
      }

      if (!isLoadAllUserByOrgs) {
        this.convertDataOrg(orgCode, org);
      }

      Z05_S07_OrganizationMasterService.getUserByOrganization(params)
        .then((res) => {
          let datas = res?.data?.data;
          this.loadingUser = false;
          this.loadingUserSelected = false;

          if (isLoadAllUserByOrgs) {
            for (let i = 0; i < datas.length; i++) {
              let element = datas[i];

              let isInit = this.userCdList.some((e) => e.user_cd == element.user_cd && e.org_cd == element.org_cd);
              let isSelect = this.listUserByOrgSelected.some((x) => x.user_cd == element.user_cd && x.org_cd == element.org_cd);

              if (isInit && !isSelect) {
                this.listUserByOrgSelected.push(element);
              }
            }
          } else {
            this.listUserByOrg = datas;
            if (this.mode === 'single') {
              if (this.listUserByOrgSelected.length > 0) {
                this.convertDataUser(isLoadDefault, this.listUserByOrgSelected[0]);
              } else {
                this.convertDataUser(isLoadDefault);
              }
            } else {
              this.convertDataUser(isLoadDefault);
            }
          }

          let orgLabel = this.lstDataOrgFlat.find((x) => x.org_cd === orgCode);
          if (orgLabel) {
            this.removeLabel(orgLabel.layer_num);
            this.addLabel(orgLabel);
          }
        })
        .catch((err) => {
          this.loadingUser = false;
          this.loadingUserSelected = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S07', idNodeNotify: 'msfa-notify-Z05S07' });
        })
        .finally(() => {
          this.onActiveUserItem(null, isLoadDefault);
        });
      // }
    },

    convertDataUser(isLoadDefault, item) {
      if (item) {
        this.listUserByOrg.forEach((element) => {
          if (element.user_cd === item.user_cd) {
            element.selected = !element.selected;
            if (element.selected) {
              if (this.mode === 'single') {
                this.listUserByOrgSelected = [];
              }
              this.listUserByOrgSelected.push(element);
            }
          } else {
            element.selected = false;
          }
        });
      } else {
        if (isLoadDefault) {
          this.paramGetUserOld = {};
          for (let i = 0; i < this.listUserByOrg.length; i++) {
            let element = this.listUserByOrg[i];

            let isSelect = this.listUserByOrgSelected.some((x) => x.user_cd == element.user_cd && x.org_cd == element.org_cd);

            if (this.mode === 'single') {
              if (this.userCdList[0]?.user_cd === element.user_cd && this.userCdList[0]?.org_cd === element.org_cd && !isSelect) {
                element.selected = true;
                this.listUserByOrgSelected.push(element);
              }
            } else {
              if (this.userCdList.some((e) => e.user_cd === element.user_cd && e.org_cd === element.org_cd) && !isSelect) {
                element.selected = true;
                this.listUserByOrgSelected.push(element);
              }
            }
          }
        } else {
          document.querySelectorAll('.s07-list-group-user .notation').forEach((el) => el.remove());
        }
      }
    },

    getLabelUser(user) {
      let lstLabel = [];
      this.lstDataOrg.forEach(async (e) => {
        this.getLabel(e, user.org_cd, lstLabel);
      });
      let label = '';
      lstLabel.reverse().forEach((x) => {
        label = label + ' ' + x.org_name;
      });
      return label;
    },

    getLabel(node, code, lstLabel) {
      if (node.org_cd === code) {
        lstLabel.push(node);
        return true;
      } else {
        if (node?.children) {
          for (let i = 0; i < node?.children.length; i++) {
            if (this.getLabel(node.children[i], code, lstLabel)) {
              lstLabel.push(node);
              return true;
            }
          }
        }
      }
      return false;
    },

    checkUserSelected(item) {
      let index = this.listUserByOrgSelected.findIndex((x) => x.user_cd === item.user_cd && x.org_cd === item.org_cd);

      return index > -1 ? true : false;
    },

    onAddItemUser(item) {
      if (!this.checkUserSelected(item)) {
        this.listUserByOrgSelected.push(item);
      }
    },

    onAddAllUser() {
      this.listUserByOrg.forEach((e) => {
        if (!this.checkUserSelected(e)) {
          this.listUserByOrgSelected.push(e);
        }
      });
    },

    onRemoveUser(item) {
      let index = this.listUserByOrgSelected.findIndex((x) => x.user_cd === item.user_cd && x.org_cd === item.org_cd);
      this.listUserByOrgSelected.splice(index, 1);
    },

    checkDisabledSubmit() {
      if (this.userFlag === 1) {
        return this.listUserByOrgSelected.length > 0 ? false : true;
      } else {
        return this.listOrganizationSelected.length > 0 ? false : true;
      }
    },

    onActiveOrgItem(event, isLoadDefault) {
      // Remove all notations
      document.querySelectorAll('.s07-list-group .notation').forEach((el) => el.remove());

      let item = null;
      if (event) {
        item = event?.target?.closest('.s07-list-group-item.active');
      } else {
        item = document.getElementsByClassName('s07-list-group-item active')[0];
      }

      let itemHeight = item?.offsetHeight || 0;
      let itemTop = item?.offsetTop || 0;

      if (item) {
        let listGroupEl = item.closest('.s07-list-group');
        let listGroupInnerEl = listGroupEl?.closest('.s07-list-group-inner');
        let offset = 0;
        let height = 0;
        if (event) {
          offset = item?.offsetTop - listGroupInnerEl?.scrollTop;
          height = listGroupInnerEl?.offsetHeight - itemHeight;
        } else {
          offset = itemTop;

          height = listGroupInnerEl?.offsetHeight - itemHeight;
          if (this.$refs.lstOrgScroll && isLoadDefault && offset > height) {
            this.$refs.lstOrgScroll.scrollTop = offset - itemHeight;
          }
        }

        // Insert notation
        let beforeNotation = document.createElement('div');
        beforeNotation.classList.add('notation-before');
        beforeNotation.classList.add('notation');
        let afterNotation = document.createElement('div');
        afterNotation.classList.add('notation-after');
        afterNotation.classList.add('notation');
        beforeNotation.style.height = `${itemHeight}px`;
        afterNotation.style.height = `${itemHeight}px`;
        beforeNotation.style.top = `${offset}px`;
        afterNotation.style.top = `${offset}px`;
        listGroupEl?.prepend(beforeNotation);
        listGroupEl?.append(afterNotation);
        this.checkScroll(beforeNotation, afterNotation, offset, height);

        // Scrolling notation
        listGroupInnerEl?.addEventListener('scroll', () => {
          offset = item?.offsetTop - listGroupInnerEl?.scrollTop;
          beforeNotation.style.top = `${offset}px`;
          afterNotation.style.top = `${offset}px`;
          height = listGroupInnerEl?.offsetHeight - itemHeight;
          this.checkScroll(beforeNotation, afterNotation, offset, height);
        });
      }
    },

    onActiveUserItem(event, isLoadDefault) {
      // Remove all notations
      document.querySelectorAll('.s07-list-group-user .notation').forEach((el) => el.remove());

      let item = null;
      if (event) {
        item = event?.target?.closest('.s07-list-group-item-user.active');
      } else {
        item = document.getElementsByClassName('s07-list-group-item-user active')[0];
      }

      let itemHeight = item?.offsetHeight || 0;
      let itemTop = item?.offsetTop || 0;
      if (item) {
        let listGroupEl = item?.closest('.s07-list-group-user');
        let listGroupInnerEl = listGroupEl?.closest('.s07-list-group-inner-user');
        let offset = 0;
        let height = 0;

        if (event) {
          offset = item?.offsetTop - listGroupInnerEl?.scrollTop;
          height = listGroupInnerEl?.offsetHeight - itemHeight;
        } else {
          offset = itemTop;

          height = listGroupInnerEl?.offsetHeight - itemHeight;
          if (this.$refs.lstUserScroll && isLoadDefault && offset > height) {
            this.$refs.lstUserScroll.scrollTop = offset - itemHeight;
          }
        }

        // Insert notation
        let beforeNotation = document.createElement('div');
        beforeNotation.classList.add('notation-before');
        beforeNotation.classList.add('notation');
        let afterNotation = document.createElement('div');
        afterNotation.classList.add('notation-after');
        afterNotation.classList.add('notation');
        beforeNotation.style.height = `${itemHeight}px`;
        afterNotation.style.height = `${itemHeight}px`;
        beforeNotation.style.top = `${offset}px`;
        afterNotation.style.top = `${offset}px`;
        listGroupEl?.prepend(beforeNotation);
        listGroupEl?.append(afterNotation);
        this.checkScroll(beforeNotation, afterNotation, offset, height);

        // Scrolling notation
        listGroupInnerEl?.addEventListener('scroll', () => {
          offset = item?.offsetTop - listGroupInnerEl?.scrollTop;
          beforeNotation.style.top = `${offset}px`;
          afterNotation.style.top = `${offset}px`;
          height = listGroupInnerEl?.offsetHeight - itemHeight;
          this.checkScroll(beforeNotation, afterNotation, offset, height);
        });
      }
    },

    checkScroll(beforeNotation, afterNotation, offset, height) {
      if (offset < 0 || offset > height) {
        beforeNotation.classList.add('invisible-notation');
        afterNotation.classList.add('invisible-notation');
      } else {
        beforeNotation.classList.remove('invisible-notation');
        afterNotation.classList.remove('invisible-notation');
      }
    },

    // return data to screen parent
    onResultData() {
      if (this.userFlag === 1) {
        this.listOrganizationSelected = [];
        this.listUserByOrgSelected.forEach((e) => {
          if (!this.checkOrgSelected(e)) {
            this.listOrganizationSelected.push({
              layer_num: e.layer_num,
              org_cd: e.org_cd,
              org_name: e.org_name,
              org_label: e.org_label
            });
          }
        });
      }
      let result = {
        screen: 'Z05_S07',
        orgSelected: this.listOrganizationSelected,
        userSelected: this.userFlag === 1 ? this.listUserByOrgSelected : []
      };
      this.$emit('onFinishScreen', result);
    },

    closeModal() {
      this.$emit('onFinishScreen', null);
    }
  }
};
</script>

<style lang="scss">
@import '@/assets/css/pages/Z05';
.date-content {
  font-weight: 700;
  font-size: 18px;
}
</style>
