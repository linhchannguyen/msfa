<template>
  <!-- modal -->
  <div>
    <div v-load-f5="true" class="modal-body-Z05S03">
      <div id="msfa-notify-Z05S03"></div>
      <div :class="`modalFacility ${mode === 'single' ? '' : 'modalFacility--change'}`">
        <div class="facility-control">
          <h2 class="facility-title">検索条件</h2>
          <ul class="facility-form">
            <li class="w-100">
              <label class="facility-form__label">担当者</label>
              <div class="facility-form__input">
                <div class="form-icon icon-right">
                  <span class="icon log-icon" title_log="担当者" @click="openModal_Z05_S01">
                    <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                  </span>
                  <div v-if="user_name" class="form-control d-flex align-items-center custom-input">
                    <div class="block-tags">
                      <el-tag class="m-0 el-tag-custom" closable @close="resetField(1)">
                        {{ user_name }}
                      </el-tag>
                    </div>
                  </div>
                  <el-input v-else clearable readonly placeholder="未選択" class="form-control-input custom-input" />
                </div>
              </div>
            </li>
            <li>
              <label class="facility-form__label">ターゲット品目</label>
              <div class="facility-form__input">
                <el-select v-model="target_product_cd" placeholder="未選択" class="form-control-select">
                  <el-option :value="''">未選択</el-option>
                  <el-option
                    v-for="item in target_productList"
                    :key="item.target_product_cd"
                    :value="item.target_product_cd"
                    :label="item.target_product_name"
                  ></el-option>
                </el-select>
              </div>
            </li>
            <li>
              <label class="facility-form__label">セレクトリスト</label>
              <div class="facility-form__input">
                <el-select v-model="select_group_id" placeholder="未選択" class="form-control-select">
                  <el-option :value="''">未選択</el-option>
                  <el-option
                    v-for="item in select_groupList"
                    :key="item.select_group_id"
                    :value="item.select_group_id"
                    :label="item.select_group_name"
                  ></el-option>
                </el-select>
              </div>
            </li>
            <li>
              <label class="facility-form__label">施設分類</label>
              <div class="facility-form__input">
                <el-select v-model="facility_category_type" placeholder="未選択" class="form-control-select">
                  <el-option :value="''">未選択</el-option>
                  <el-option
                    v-for="item in facility_categoryList"
                    :key="item.facility_category_type"
                    :value="item.facility_category_type"
                    :label="item.facility_category_name"
                  ></el-option>
                </el-select>
              </div>
            </li>
            <li>
              <label class="facility-form__label">所在地</label>
              <div class="facility-form__input">
                <div class="form-icon icon-right">
                  <span class="icon log-icon" title_log="所在地" @click="openModal_Z05_S02">
                    <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                  </span>
                  <div v-if="prefecture_name || municipality_name">
                    <div class="form-control d-flex align-items-center custom-input">
                      <div class="block-tags">
                        <el-tag v-if="municipality_name" class="el-tag-custom el-tag-icon-top" closable @close="resetField(2)">
                          {{ municipality_name }}
                        </el-tag>
                        <el-tag v-else class="el-tag-custom el-tag-icon-top" closable @close="resetField(2)">
                          {{ prefecture_name }}
                        </el-tag>
                      </div>
                    </div>
                  </div>
                  <el-input v-else clearable readonly placeholder="未選択" class="form-control-input custom-input" />
                </div>
              </div>
            </li>
            <li class="w-100">
              <label class="facility-form__label">施設コード</label>
              <div class="facility-form__input" :class="validationFacility_cd() ? 'hasErr' : ''">
                <el-input
                  v-model="facility_cd"
                  placeholder="コード入力"
                  class="form-control-input custom-input"
                  :readonly="change_flag"
                  :disabled="change_flag"
                  clearable
                  @change="validationFacility_cd()"
                />

                <span v-if="validationFacility_cd()" class="text-error">{{ getMessage('MSFA0012', '施設コード', '25') }}</span>
              </div>
            </li>
            <li class="w-100">
              <label class="facility-form__label">施設名</label>
              <div class="facility-form__input" :class="validationFacility_name() ? 'hasErr' : ''">
                <el-input
                  v-model="facility_name"
                  clearable
                  placeholder="施設名入力"
                  class="form-control-input custom-input"
                  @change="validationFacility_name()"
                />
                <span v-if="validationFacility_name()" class="text-error">{{ getMessage('MSFA0012', '施設名', '25') }}</span>
              </div>
            </li>
          </ul>
          <div class="facility-btnSearch">
            <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="search">
              <span class="btn-iconLeft">
                <ImageSVG :src-image="'icon_search.svg'" :alt-image="'icon_search'" />
              </span>
              <span>検索</span>
            </button>
          </div>
        </div>
        <div class="facility-content">
          <div class="institution">
            <div class="institution-head">
              <h2 class="title">施設</h2>
              <label class="custom-control custom-checkbox custom-control--bordGreen">
                <input v-model="isRelatedFacilitie" class="custom-control-input" type="checkbox" @click="handleCheckbox" />
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">関連施設も表示</span>
              </label>
            </div>
            <div :class="`lst-institution ${mode === 'single' ? 'lst-institution--box' : ''} ${isLoading ? 'loader' : ''}`">
              <Preloader v-if="isLoading" />
              <ul v-else-if="facilityList.length">
                <li v-for="item in facilityList" :key="item.facility_cd">
                  <div
                    :id="`container${item.facility_cd}`"
                    :class="`institution-header ${mode === 'single' && checkExist(item.facility_cd) ? 'selected' : ''} 
                    ${isRelatedFacilitie && item.children.length !== 0 ? 'none' : 'isIconDropdown'} 
                    ${mode !== 'single' && item.children.length === 0 ? 'non-pointer' : ''}
                    ${mode !== 'single' ? 'uncusor' : ''}`"
                    @click="mode === 'single' ? onAddItemSingle(item) : ''"
                  >
                    <span
                      :class="`${isRelatedFacilitie && item.children.length !== 0 ? 'none' : 'isIconDropdown-span'}`"
                      class="custom-span"
                      @click="onAddItem(item)"
                    ></span>
                    <h2 class="header-title">{{ item.facility_short_name }}</h2>
                    <ButtonAdd
                      v-if="mode !== 'single'"
                      :disabled="checkExist(item.facility_cd)"
                      :label="!checkExist(item.facility_cd) ? '追加' : '追加済'"
                      :class-name="`${
                        checkExist(item.facility_cd) ? 'btn--custom btn-add-w' : 'btn-add-w btn btn-outline-primary btn-outline-primary--cancel'
                      }`"
                      @click="_onAddItem(item)"
                    />
                  </div>
                  <div v-show="item.children.length > 0" :id="`wrap${item.facility_cd}`" class="customDiv">
                    <ul :id="`list${item.facility_cd}`" class="institution-sub">
                      <li
                        v-for="sbitem in item.children"
                        :key="sbitem.facility_cd"
                        :class="{ selectedchild: checkExist(sbitem.facility_cd) && mode === 'single', pointer: mode === 'single' }"
                        @click="onAddItem(sbitem)"
                      >
                        <span>{{ sbitem.facility_short_name }} {{ sbitem.definition_label ? `(${sbitem.definition_label})` : '' }}</span>
                        <ButtonAdd
                          v-if="mode !== 'single'"
                          :disabled="checkExist(sbitem.facility_cd)"
                          :label="!checkExist(sbitem.facility_cd) ? '追加' : '追加済'"
                          :class-name="`${
                            checkExist(sbitem.facility_cd) ? 'btn--custom btn-add-w' : 'btn-add-w btn btn-outline-primary btn-outline-primary--cancel'
                          }`"
                          @click="_onAddItem(sbitem)"
                        />
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
              <div v-else :class="{ emtydata: mode !== 'single' }" class="noData_Z05S03">
                <p v-if="mode !== 'single'">該当するデータがありません。</p>
                <EmptyData v-else custom-class="customClassEmpty" thumb-margin-top="20px" />
              </div>
            </div>
            <div v-if="facilityList.length" class="pagin-institution">
              <PaginationTable
                :pager-counts="currDevice() !== 'Desktop' ? 3 : 7"
                :current-page="pagination.current_page"
                :total="pagination.total_items"
                :page-size="100"
                @current-change="handleChangePage"
              />
            </div>
          </div>
        </div>

        <!-- multiple -->
        <div v-if="mode !== 'single'" class="facility-selectList">
          <div class="selectList-head">
            <h2 class="selectList-head-title">選択リスト</h2>
          </div>
          <div class="selectList">
            <div v-if="facilitySelectList.length > 0" class="lstSelect">
              <ul>
                <li v-for="item in facilitySelectList" :key="item.facility_cd">
                  <span class="text">{{ item.facility_short_name }}</span>
                  <el-button class="btn-circle btn--icon btn" type="button" @click="onRemoveItem(item.facility_cd)">
                    <i class="el-icon-close"></i>
                  </el-button>
                </li>
              </ul>
            </div>
            <EmptyData v-else :title="'施設を選択して下さい。'" custom-class="customClassEmpty customClassEmpty1 noData_Z05S03_mul" />
          </div>
        </div>
      </div>

      <!-- control -->
      <div class="facilityBtn">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
        <button type="button" class="btn btn-primary" :disabled="!facilitySelectList.length" @click="returnData">決定</button>
      </div>
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onCloseModalChild"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import Z05_S03_Service from '@/api/Z05/Z05_S03_FacilitySelectionService';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05_S02_ModalAreaSelection from '@/views/Z05/Z05_S02_Area_Selection/Z05_S02_Area_Selection';

export default {
  name: 'Z05_S03_FacilitySelection',
  components: {
    Z05_S01_ModalOrganization,
    Z05_S02_ModalAreaSelection
  },
  props: {
    mode: {
      type: String,
      required: false,
      default: 'single'
    },
    change_flag: {
      type: Boolean,
      required: false,
      default: false
    },
    orgCD: {
      type: String,
      required: false,
      default: ''
    },
    userCD: {
      type: String,
      required: false,
      default: ''
    },
    userName: {
      type: String,
      required: false,
      default: ''
    },
    targetProductCd: {
      type: String,
      required: false,
      default: ''
    },
    selectGroupCd: {
      type: String,
      required: false,
      default: ''
    },
    facilityCategoryType: {
      type: String,
      required: false,
      default: ''
    },
    prefectureCD: {
      type: String,
      required: false,
      default: ''
    },
    prefectureName: {
      type: String,
      required: false,
      default: ''
    },
    municipalityCD: {
      type: String,
      required: false,
      default: ''
    },
    municipalityName: {
      type: String,
      required: false,
      default: ''
    },
    facilityCd: {
      type: Array,
      required: false,
      default: () => []
    },
    facilityName: {
      type: Array,
      required: false,
      default: () => []
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      isRelatedFacilitie: true,
      isLoading: false,
      // props Z05-S01
      userFlag: 1,
      userSelectFlag: 1,
      userCdList: [],
      orgCdList: [],
      // props Z05-S02
      hierarchySelected: 'municipality',
      // list
      select_groupList: [],
      target_productList: [],
      facility_categoryList: [],
      facilityList: [],
      facilitySelectList: [],
      facility_cdList: this.facilityCd,
      facility_nameList: this.facilityName,
      // field
      user_cd: this.userCD,
      user_name: this.userName,
      target_product_cd: this.targetProductCd,
      select_group_id: this.selectGroupCd,
      prefecture_cd: this.prefectureCD,
      prefecture_name: this.prefectureName,
      municipality_cd: this.municipalityCD,
      municipality_name: this.municipalityName,
      facility_category_type: this.facilityCategoryType,
      facility_cd: this.facilityCd?.join(' '),
      facility_name: this.facilityName?.join(' '),
      // pagination
      pagination: {
        current_page: 1,
        first_page: 1,
        last_page: 1,
        next_page: 1,
        previous_page: 1,
        total_items: 6,
        total_pages: 1
      },
      filterData: {}
    };
  },
  mounted() {
    this.facility_cdList = this.facilityCd;
    this.facility_nameList = this.facilityName;

    this.user_cd = this.userCD;
    this.user_name = this.userName;
    this.target_product_cd = this.targetProductCd;
    this.select_group_id = this.selectGroupCd;
    this.prefecture_cd = this.prefectureCD;
    this.prefecture_name = this.prefectureName;
    this.municipality_cd = this.municipalityCD;
    this.municipality_name = this.municipalityName;
    this.facility_category_type = this.facilityCategoryType;
    this.facility_cd = this.facilityCd.join(' ');
    this.facility_name = this.facilityName.join(' ');

    this.filterData = {
      user_cd: this.userCD,
      user_name: this.userName,
      target_product_cd: this.targetProductCd,
      select_group_id: this.selectGroupCd,
      prefecture_cd: this.prefectureCD,
      prefecture_name: this.prefectureName,
      municipality_cd: this.municipalityCD,
      municipality_name: this.municipalityName,
      facility_category_type: this.facilityCategoryType,
      facility_cd: this.facilityCd?.join(' '),
      facility_name: this.facilityName?.join(' ')
    };

    if (this.userCD && this.orgCD) {
      this.orgCdList = [this.orgCD];
      this.user_name = this.userName;
      this.userCdList = [
        {
          org_cd: this.orgCD,
          user_cd: this.userCD
        }
      ];
    }
    this.getListArea();
  },
  methods: {
    checkFilter() {
      let data = {
        user_cd: this.userCD,
        user_name: this.userName,
        target_product_cd: this.targetProductCd,
        select_group_id: this.selectGroupCd,
        prefecture_cd: this.prefectureCD,
        prefecture_name: this.prefectureName,
        municipality_cd: this.municipalityCD,
        municipality_name: this.municipalityName,
        facility_category_type: this.facilityCategoryType,
        facility_cd: this.facilityCd.join(' '),
        facility_name: this.facilityName.join(' ')
      };

      let valid = Object.values(data).filter((x) => (x && typeof x === 'string') || ((Object.keys(x).length > 0 || x.length > 0) && typeof x === 'object'));

      if (valid.length > 0) {
        return true;
      }
      return false;
    },
    // call api
    getListArea() {
      Z05_S03_Service.getListAreaService({ facility_flag: 1 })
        .then((res) => {
          if (res.data.status === '200') {
            const dataRes = res.data.data;
            this.select_groupList = dataRes.select_group;
            this.target_productList = dataRes.target_product;
            this.facility_categoryList = dataRes.facility_category;

            if (this.checkFilter()) {
              this.search();
            }
          }
        })
        .catch((err) =>
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S03', idNodeNotify: 'msfa-notify-Z05S03' })
        )
        .finally();
    },
    getListAreaFacility(params) {
      this.isLoading = true;
      this.filterData = { ...this.filterData, ...params };
      Z05_S03_Service.getListAreaFacilityService(params)
        .then((res) => {
          if (res.data.status === '200') {
            const dataRes = res.data.data;
            res.data.data.records.forEach((element) => {
              if (element.children.length === 1) {
                element.children.push({
                  facility_category_name: '大学病院',
                  facility_category_type: '01',
                  facility_cd: '454020014',
                  facility_short_name: '武井内科病院（新富町）',
                  relation_name: '子施設',
                  relation_type: '10'
                });
              }
            });
            this.facilityList = dataRes.records;

            if (this.facilityCd && this.facilityCd.length > 0) {
              let sitem = this.facilityList.find((x) => x.facility_cd === this.facilityCd[0]);
              if (this.mode === 'single' && sitem) {
                this.onAddItemSingle(sitem);
              } else {
                this.facilityCd.forEach((el) => {
                  let item = this.facilityList.find((x) => x.facility_cd === el);
                  if (item) {
                    this._onAddItem(item);
                  }
                });
              }
            }

            this.pagination = dataRes.pagination;
          }
        })
        .catch((err) =>
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S03', idNodeNotify: 'msfa-notify-Z05S03' })
        )
        .finally(() => (this.isLoading = false));
    },
    // define function component
    validationFacility_cd() {
      this.facility_cd = this.facility_cd.replace(/\s+/, ' ');
      this.facility_cd = this.convertToHalf(this.facility_cd);
      let facility_cd = this.facility_cd.split(' ');
      for (let index = 0; index < facility_cd.length; index++) {
        const element = facility_cd[index];
        if (element.length > 25) {
          return true;
        }
      }
      return false;
    },
    validationFacility_name() {
      this.facility_name = this.facility_name.replace(/\s+/, ' ');
      this.facility_name = this.convertToHalf(this.facility_name);
      let facility_name = this.facility_name.split(' ');
      for (let index = 0; index < facility_name.length; index++) {
        const element = facility_name[index];
        if (element.length > 25) {
          return true;
        }
      }
      return false;
    },
    checkExist(facility_cd) {
      return this.facilitySelectList.some((el) => el.facility_cd === facility_cd);
    },
    search() {
      this.facilityList = [];
      this.getListAreaFacility({
        facility_cd: this.facility_cd
          ?.replace(/^\s+|\s+$/gm, '')
          ?.split(' ')
          ?.toString(),
        select_group_id: this.select_group_id,
        target_product_cd: this.target_product_cd,
        prefecture_cd: this.prefecture_cd,
        municipality_cd: this.municipality_cd,
        facility_category_type: this.facility_category_type,
        facility_name: this.facility_name
          ?.replace(/^\s+|\s+$/gm, '')
          ?.split(' ')
          ?.toString(),
        user_cd: this.user_cd,
        limit: 100,
        offset: 0
      });
    },
    resetField(option) {
      if (option === 1) {
        this.user_cd = '';
        this.user_name = '';
        this.orgCdList = [];
        this.userCdList = [];

        this.filterData = {
          ...this.filterData,
          org_cd: [],
          user_name: ''
        };
      } else {
        this.prefecture_cd = '';
        this.prefecture_name = '';
        this.municipality_cd = '';
        this.municipality_name = '';

        this.filterData = {
          ...this.filterData,
          municipality_cd: '',
          municipality_name: '',
          prefecture_cd: '',
          prefecture_name: ''
        };
      }
    },
    handleCheckbox() {
      this.isRelatedFacilitie = !this.isRelatedFacilitie;
      let els = document.querySelectorAll('.customDiv');
      if (!this.isRelatedFacilitie) {
        els.forEach((el) => {
          el.classList.remove('open');
          el.style.height = 0;
        });
      }
    },
    handleChangePage(page) {
      this.pagination.current_page = page;
      this.getListAreaFacility({
        facility_cd: this.facility_cd
          ?.replace(/^\s+|\s+$/gm, '')
          ?.split(' ')
          .toString(),
        select_group_id: this.select_group_id,
        target_product_cd: this.target_product_cd,
        prefecture_cd: this.prefecture_cd,
        municipality_cd: this.municipality_cd,
        facility_category_type: this.facility_category_type,
        facility_name: this.facility_name
          ?.replace(/^\s+|\s+$/gm, '')
          ?.split(' ')
          .toString(),
        user_cd: this.user_cd,
        limit: 100,
        offset: page === 0 ? page : page - 1
      });
    },
    onAddItemSingle(value) {
      const index = this.facilitySelectList.findIndex((val) => val.facility_cd === value.facility_cd);
      if (this.mode === 'single') {
        const { facility_cd, facility_short_name, facility_type, facility_name_kana } = value;
        if (index === -1) this.facilitySelectList = [{ facility_cd, facility_short_name, facility_type, facility_name_kana }];
        else this.onRemoveItem(value.facility_cd);
      }
    },

    onAddItem(value) {
      const index = this.facilitySelectList.findIndex((val) => val.facility_cd === value.facility_cd);
      if (this.mode === 'single') {
        const { facility_cd, facility_short_name, facility_type, facility_name_kana } = value;
        if (index === -1) this.facilitySelectList = [{ facility_cd, facility_short_name, facility_type, facility_name_kana }];
        else this.onRemoveItem(value.facility_cd);
      }
      let c = document.getElementById(`container${value.facility_cd}`);
      let w = document.getElementById(`wrap${value.facility_cd}`);
      let l = document.getElementById(`list${value.facility_cd}`);
      setTimeout(() => {
        if (this.isRelatedFacilitie) {
          if (w.classList.contains('open')) {
            c?.classList?.remove('expand');
            w.classList.remove('open');
            w.style.height = 0;
          } else {
            c?.classList?.add('expand');
            w.classList.add('open');
            w.style.height = `${l.clientHeight}px`;
          }
        }
      }, 100);
    },
    _onAddItem(value) {
      const index = this.facilitySelectList.findIndex((val) => val.facility_cd === value.facility_cd);
      if (index === -1) {
        const { facility_cd, facility_short_name, facility_type, facility_name_kana } = value;
        this.facilitySelectList.push({ facility_cd, facility_short_name, facility_type, facility_name_kana });
      }
    },
    onRemoveItem(facility_cd) {
      this.facilitySelectList = this.facilitySelectList.filter((item) => item.facility_cd !== facility_cd);
    },
    // modal
    onCloseModalChild(data) {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      if (data?.screen === 'Z05_S01' && data.userSelected) {
        this.user_cd = data.userSelected[0].user_cd;
        this.user_name = data.userSelected[0].user_name;
        this.userCdList = data.userSelected.map((el) => ({
          user_cd: el.user_cd,
          org_cd: el.org_cd
        }));

        this.orgCdList = data.userSelected.map((el) => el.org_cd);

        this.filterData = {
          ...this.filterData,
          org_cd: this.orgCdList[0],
          user_name: this.user_name
        };
      } else if (data?.screen === 'Z05_S02' && data.prefectureSelected) {
        this.prefecture_cd = data.prefectureSelected.prefecture_cd;
        this.prefecture_name = data.prefectureSelected.prefecture_name;
        this.municipality_cd = data.municipalitySelected.municipality_cd;
        this.municipality_name = data.municipalitySelected.municipality_name;
        this.filterData = {
          ...this.filterData,
          municipality_cd: this.municipality_cd,
          municipality_name: this.municipality_name,
          prefecture_cd: this.prefecture_cd,
          prefecture_name: this.prefecture_name
        };
      }
    },
    openModal_Z05_S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: this.markRaw(Z05_S01_ModalOrganization),
        width: '45rem',
        customClass: 'custom-dialog modal-fixed',
        props: {
          mode: 'single',
          userFlag: this.userFlag,
          userSelectFlag: this.userSelectFlag,
          userCdList: this.userCdList,
          orgCdList: this.orgCdList
        }
      };
    },
    openModal_Z05_S02() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '地区選択',
        isShowModal: true,
        component: this.markRaw(Z05_S02_ModalAreaSelection),
        width: '47rem',
        customClass: 'custom-dialog modal-fixed',
        props: {
          hierarchySelected: 'municipality',
          prefectureCode: this.prefecture_cd,
          municipalityCode: this.municipality_cd
        }
      };
    },
    closeModal() {
      this.$emit('onFinishScreen');
    },
    // return Data
    returnData() {
      let result = {
        screen: 'Z05_S03',
        facilitySelectList: this.facilitySelectList,
        filterData: {
          ...this.filterData
        }
      };
      if (this.facilitySelectList.length) this.$emit('onFinishScreen', result);
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_tablet: 768px;
.emtydata {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  p {
    color: #99a5ae;
  }
}
.loader {
  background: aliceblue;
  display: flex;
  justify-content: center;
  align-items: center;
}
.expand {
  &::after {
    transform: rotate(180deg);
  }
}
.custom-input {
  position: relative;
  margin-bottom: 4px;
}
.non-pointer {
  cursor: unset !important;
}
.isIconDropdown::after {
  content: unset !important;
}
.customClassEmpty {
  height: inherit;
}
.customClassEmpty1 {
  height: 100%;
}
.icon {
  cursor: pointer;
}
.selected {
  box-shadow: 0px -2px 5px rgba(183, 195, 203, 0.45), 0px 2px 5px rgba(183, 195, 203, 0.45);
  background: #eef6ff;
  border-left: 4px solid #448add !important;
  h2.header-title {
    font-weight: 700 !important;
  }
}
.selectedchild {
  background: #eef6ff;
  border-left: 4px solid #448add !important;
  padding-left: 16px !important;
}
.modalFacility {
  display: flex;
  flex-wrap: nowrap;
  height: 470px;
  &.modalFacility--change {
    .facility-control {
      width: 30%;
      @media (max-width: $viewport_tablet) {
        width: 35%;
      }
    }
    .facility-content {
      width: 40%;
      height: 468px;
      @media (max-width: $viewport_tablet) {
        width: 40%;
        padding-right: 20px;
      }
    }
    .facility-selectList {
      padding-left: 20px;
      width: 30%;
      @media (max-width: $viewport_tablet) {
        padding: 20px 0 0;
        padding-top: 0px;
      }
    }
    .selectList-head {
      padding: 12px 20px;
      height: 55px;
      align-items: center;
      display: flex;
      flex-direction: row;
      .selectList-head-title {
        font-size: 16px;
        font-weight: bold;
      }
    }
    .selectList {
      box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
      border-radius: 8px 0 0 8px;
      background: #fff;
      height: calc(100% - 56px);
      max-height: 415px;

      .lstSelect {
        height: 100%;
        overflow-y: auto;
        ul {
          li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 13px;
            border-bottom: 1px solid #dfe0e0;
            .text {
              padding-right: 10px;
            }
            .btn {
              min-width: 32px;
            }
          }
        }
      }
    }
  }
  .facility-control {
    width: 45%;
    .facility-title {
      font-size: 18px;
      font-weight: 700;
      padding-bottom: 14px;
    }
    .facility-form {
      display: flex;
      flex-wrap: wrap;
      margin-left: -10px;
      overflow-y: auto;
      overflow-x: hidden;
      max-height: 430px;
      padding-right: 5px;
      li {
        width: 50%;
        margin-top: 6px;
        padding-left: 10px;
      }
      .facility-form__label {
        margin-bottom: 6px;
      }
    }
    .facility-btnSearch {
      text-align: right;
      margin-top: 20px;
      .btn {
        padding: 0 12px;
        height: 32px;
        .btn-iconLeft {
          top: -2px;
        }
      }
    }
  }
  .facility-content {
    width: 55%;
    padding-left: 20px;
    height: 468px;
  }
  .institution {
    height: 100%;
    max-height: 510px;
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    background: #fff;
    .institution-head {
      background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
      display: flex;
      justify-content: space-between;
      padding: 12px 20px;
      border-radius: 8px 8px 0 0;
      height: 55px;
      .title {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
      }
      .custom-control {
        .custom-control-description {
          color: #fff;
        }
      }
    }
  }
  .lst-institution {
    overflow-y: auto;
    height: 360px;
    &--box {
      > ul {
        > li {
          .institution-header {
            &[aria-expanded='true'] {
              box-shadow: 0px -2px 5px rgba(183, 195, 203, 0.45), 0px 2px 5px rgba(183, 195, 203, 0.45);
              background: #eef6ff;
              border-left: 4px solid #448add;
              .header-title {
                font-weight: 700;
              }
              &:after {
                transform: rotate(180deg);
              }
            }
          }
        }
      }
    }
    > ul {
      > li {
        border-bottom: 1px solid #dfe0e0;
        .institution-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 12px 30px 12px 12px;
          border-left: 4px solid #fff;
          position: relative;
          cursor: pointer;
          &[aria-expanded='true'] {
            &:after {
              transform: rotate(180deg);
            }
          }
          &:after {
            position: absolute;
            top: calc(50% - 3px);
            right: 10px;
            content: '';
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #5f6b73;
            transition: 400ms all;
          }
          .header-title {
            color: #5f6b73;
            font-weight: normal;
            padding-right: 10px;
          }
          .btn {
            padding: 0 8px;
            height: 32px;
            white-space: nowrap;
            .btn-iconLeft {
              top: -2px;
            }
          }
        }
        .institution-sub {
          border-top: 1px solid #dfe0e0;
          li {
            display: flex;
            justify-content: space-between;
            padding: 12px 20px;
            padding-right: 30px;
            border-bottom: 1px solid #dfe0e0;
            &:last-child {
              border: none;
            }
            span {
              display: block;
              width: 100%;
              padding: 0 10px 0 34px;
              color: #5f6b73;
              position: relative;
              &::after {
                position: absolute;
                top: 2px;
                left: 0;
                content: '';
                display: block;
                width: 19px;
                height: 16px;
                background: url(~@/assets/template/images/icon_arrow-institution.svg) no-repeat;
              }
            }
            .btn {
              padding: 0 8px;
              height: 32px;
              white-space: nowrap;
              .btn-iconLeft {
                top: -2px;
              }
            }
          }
        }
      }
    }
    .customDiv {
      height: 0;
      transition: height 0.7s ease-in-out;
      overflow: hidden;
    }
    .close {
      display: none;
    }
  }
  .pagin-institution {
    padding: 12px 8px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    box-shadow: 0px -3px 6px #e3e3e3;
    height: 53px;
  }
}
.facilityBtn {
  text-align: center;
  margin-top: 15px;
  .btn {
    margin-right: 24px;
    min-width: 180px;
    &:last-child {
      margin: 0;
    }
  }
}
.custom-span {
  position: absolute;
  background: unset;
  width: 25px;
  height: 30px;
  right: 0;
  z-index: 9;
  cursor: pointer;
}
.isIconDropdown-span {
  display: none;
}
.uncusor {
  cursor: unset !important;
}
.modal-body-Z05S03 {
  margin: -10px;
  margin-right: -32px;
}
.btn-circle {
  padding: 0;
  background: inherit;
  border-radius: 100%;
  color: #99a5ae;
  box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 8px 0;
  i {
    font-weight: bold;
    font-size: 15px;
    margin-top: 1px;
  }
  .el-icon-close:before {
    color: unset;
  }
}
</style>
