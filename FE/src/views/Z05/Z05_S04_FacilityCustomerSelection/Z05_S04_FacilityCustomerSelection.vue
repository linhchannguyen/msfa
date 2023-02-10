<template>
  <!-- modal -->
  <!-- Name: 施設個人選択 -->
  <!-- width: 
  multi: currDevice() !== 'Desktop' ? '95%' : '70rem'
  single: currDevice() !== 'Desktop' ? '95%' : '55rem'
   -->
  <div>
    <div v-load-f5="true" class="cssZ05S04 modal-body-Z05S04" :class="mode === 'single' ? '' : 'modal-body--changePd'">
      <div id="msfa-notify-Z05S04"></div>
      <div class="groupFacility" :class="mode === 'single' ? '' : 'groupFacility--change'">
        <div class="groupFacility-form">
          <div id="accordion" class="groupForm">
            <div class="selection selection-collapse">
              <el-collapse v-model="activeNameItemBlock" accordion class="custom-collapse-radius">
                <el-collapse-item class="accordionItem" name="search">
                  <template #title>
                    <h2 class="selection-head">
                      <span class="selection-head-title">検索条件</span>
                    </h2>
                  </template>
                  <div class="selection-body">
                    <ul class="facility-form">
                      <li class="w-100">
                        <label class="facility-form__label">担当者</label>
                        <div class="facility-form__input">
                          <div class="form-icon icon-right">
                            <span class="icon icon--cursor log-icon" title_log="担当者" @click="openModalZ05S01()">
                              <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                            </span>
                            <div v-if="formData.user_name" class="form-control d-flex align-items-center">
                              <div class="block-tags">
                                <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveUser()">
                                  {{ formData.user_name }}
                                </el-tag>
                              </div>
                            </div>

                            <el-input v-else style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                          </div>
                        </div>
                      </li>
                      <li>
                        <label class="facility-form__label">ターゲット品目</label>
                        <div class="facility-form__input">
                          <el-select v-model="formData.target_product_cd" placeholder="未選択" class="form-control-select">
                            <el-option :value="''">未選択</el-option>
                            <el-option
                              v-for="target in lstTargetItem"
                              :key="target.target_product_cd"
                              :label="target.target_product_name"
                              :value="target.target_product_cd"
                            ></el-option>
                          </el-select>
                        </div>
                      </li>
                      <li>
                        <label class="facility-form__label">セレクトリスト</label>
                        <div class="facility-form__input">
                          <el-select v-model="formData.select_group_id" placeholder="未選択" class="form-control-select">
                            <el-option :value="''">未選択</el-option>
                            <el-option v-for="item in lstSelectGroup" :key="item.select_group_id" :label="item.select_group_name" :value="item.select_group_id">
                            </el-option>
                          </el-select>
                        </div>
                      </li>
                      <li>
                        <label class="facility-form__label">施設分類</label>
                        <div class="facility-form__input">
                          <el-select v-model="formData.facility_category_type" placeholder="未選択" class="form-control-select">
                            <el-option :value="''">未選択</el-option>
                            <el-option
                              v-for="facility in lstFacilityCategory"
                              :key="facility.facility_category_type"
                              :label="facility.facility_category_name"
                              :value="facility.facility_category_type"
                            >
                            </el-option>
                          </el-select>
                        </div>
                      </li>
                      <li>
                        <label class="facility-form__label">所在地</label>
                        <div class="facility-form__input">
                          <div class="form-icon icon-right">
                            <span class="icon icon--cursor log-icon" title_log="所在地" @click="openModal_Z05_S02">
                              <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                            </span>

                            <div v-if="formData.municipality_name || formData.prefecture_name" class="form-control d-flex align-items-center">
                              <div class="block-tags">
                                <el-tag v-if="formData.municipality_name" class="el-tag-custom el-tag-icon-top" closable @close="handleRemovePrefecture()">
                                  {{ formData.municipality_name }}
                                </el-tag>
                                <el-tag v-else class="el-tag-custom el-tag-icon-top" closable @close="handleRemovePrefecture()">
                                  {{ formData.prefecture_name }}
                                </el-tag>
                              </div>
                            </div>

                            <el-input v-else style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                          </div>
                        </div>
                      </li>
                      <li class="w-100">
                        <label class="facility-form__label">施設コード</label>
                        <div class="facility-form__input" :class="validationFacility_cd() ? 'hasErr' : ''">
                          <el-input
                            v-model="formData.facility_cd"
                            clearable
                            placeholder="コード入力"
                            class="form-control-input custom-input"
                            :readonly="propsPrams.non_facility_flag == 1"
                            :disabled="propsPrams.non_facility_flag == 1"
                            @change="validationFacility_cd()"
                          />
                          <span v-if="validationFacility_cd()" class="invalid">
                            {{ getMessage('MSFA0012', '施設コード', 25) }}
                          </span>
                        </div>
                      </li>
                      <li class="w-100">
                        <label class="facility-form__label">施設名</label>
                        <div class="facility-form__input" :class="validationFacility_name() ? 'hasErr' : ''">
                          <el-input
                            v-model="formData.facility_name"
                            clearable
                            placeholder="施設名入力"
                            class="form-control-input custom-input"
                            @change="validationFacility_name()"
                          />
                          <span v-if="validationFacility_name()" class="invalid">
                            {{ getMessage('MSFA0012', '施設名', 255) }}
                          </span>
                        </div>
                      </li>
                    </ul>
                    <div class="facility-btnSearch">
                      <el-button
                        class="btn-add btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                        :disabled="checkDisableSearchPersonBtn()"
                        @click="onFilterData(false)"
                      >
                        <i class="el-icon-search">
                          <span>検索</span>
                        </i>
                      </el-button>
                    </div>
                  </div>
                </el-collapse-item>

                <el-collapse-item class="accordionItem" name="result">
                  <template #title>
                    <h2 class="selection-head">
                      <span class="selection-head-title">施設リスト</span>
                    </h2>
                  </template>
                  <div class="selection-body">
                    <div :class="`${loadingPageFacility ? 'pre-loader h-414' : ''}`">
                      <Preloader v-if="loadingPageFacility" />
                      <div v-else-if="lstConditionAreaFacility.length > 0" class="checkBox-facility">
                        <label class="custom-control custom-checkbox custom-control--bordGreen" :class="disabledFacilityRelation ? 'disabled-control' : ''">
                          <input v-model="checkedDisplayFacilityRelation" class="custom-control-input" type="checkbox" @change="displayFacilityRelated()" />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">関連施設も表示</span>
                        </label>
                      </div>
                      <div v-if="lstConditionAreaFacility.length > 0 && !loadingPageFacility" class="lst-institution lst-institution--box">
                        <ul>
                          <li v-for="(itemFacility, index) in lstConditionAreaFacility" :key="itemFacility.facility_cd">
                            <div :class="`facilityItem ${itemFacility.selected ? 'selected' : ''}`">
                              <div class="left" @click="setSelectedFacility(itemFacility)">
                                <span class="text">{{ itemFacility.facility_short_name }}</span>
                              </div>
                              <div
                                v-if="itemFacility?.department_line?.length > 0"
                                :class="`right ${itemFacility.isShowFacilityDepartment ? 'isCollapse' : ''}`"
                                @click="showFacilityDepartment(itemFacility, index)"
                              ></div>
                            </div>
                            <div :id="`wrap${itemFacility.facility_cd}`" class="customDiv close">
                              <ul :id="`list${itemFacility.facility_cd}`" class="institution-sub">
                                <li
                                  v-for="item in itemFacility.department_line"
                                  :key="item.facility_cd"
                                  :class="item.selected ? 'selected' : ''"
                                  @click="setSelectedFacility(itemFacility, item, true)"
                                >
                                  <span>{{ item.department_name }}</span>
                                </li>
                              </ul>
                            </div>
                            <div v-if="checkedDisplayFacilityRelation">
                              <ul class="institution-sub">
                                <li
                                  v-for="item in itemFacility.relation_facility"
                                  :key="item.facility_cd"
                                  :class="item.selected ? 'selected' : ''"
                                  @click="setSelectedFacility(itemFacility, item, true)"
                                >
                                  <span>{{ item.facility_short_name }}({{ item.relation_name }})</span>
                                </li>
                              </ul>
                            </div>
                          </li>
                        </ul>
                      </div>
                      <div v-if="lstConditionAreaFacility.length > 0 && !loadingPageFacility" class="pagin-institution">
                        <PaginationTable
                          class="pagination-bord"
                          :page-size="paginationFacility.pageSize"
                          :total="paginationFacility.totalItems"
                          :current-page="paginationFacility.curentPage"
                          @current-change="handleCurrentChangeFacility"
                        />
                      </div>
                      <div v-if="lstConditionAreaFacility.length === 0 && !loadingPageFacility" class="noData h-415">
                        <div class="noData-content">
                          <p class="noData-text">該当するデータがありません。</p>
                          <div class="noData-thumb">
                            <img src="@/assets/template/images/data/amico.svg" alt="" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </el-collapse-item>
              </el-collapse>
            </div>
          </div>
        </div>

        <!-- Mode single list person -->
        <div v-if="mode === 'single'" class="groupFacility-content">
          <div class="groupFacility-box">
            <el-tabs v-model="activeTabName" class="custom-tab-person" @tab-click="handleClickTab()">
              <el-tab-pane :disabled="!isFilter" label="施設個人" name="person">
                <div class="formSearch">
                  <ul>
                    <li>
                      <label class="formSearch-label">個人名</label>
                      <div class="formSearch-control">
                        <el-input v-model="personal_name" clearable placeholder="個人名入力" class="form-control-input" />
                      </div>
                    </li>
                    <li>
                      <label class="formSearch-label">セグメント</label>
                      <div class="formSearch-flex">
                        <div class="formSearch-select">
                          <el-select v-model="segment_type" placeholder="未選択" class="form-control-select">
                            <el-option :value="''">未選択</el-option>
                            <el-option v-for="segment in lstSegment" :key="segment.segment_type" :label="segment.segment_name" :value="segment.segment_type">
                            </el-option>
                          </el-select>
                        </div>
                        <div class="formSearch-btn">
                          <el-button
                            :disabled="!isFilter"
                            class="btn-add btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                            @click="seachPerson()"
                          >
                            <i class="el-icon-search">
                              <span>検索</span>
                            </i>
                          </el-button>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
                <div :class="`${loadingPagePerson ? 'pre-loader h-loadingperson' : ''}`">
                  <Preloader v-if="loadingPagePerson" />
                  <div v-else>
                    <div v-if="lstFacilityPersonal.length > 0" class="lstIndividual">
                      <ul>
                        <li
                          v-for="person in lstFacilityPersonal"
                          :key="person.person_cd"
                          :class="lstFacilityPersonalSelected.some((x) => x.person_cd === person.person_cd) ? 'active' : ''"
                          @click="setSelectedPersonal(person)"
                        >
                          <div class="lstIndividual-text">
                            <p class="text">
                              <span class="pr-2">{{ person.facility_short_name }}</span>
                              <span class="pr-2">{{ person.department_name }}</span>
                            </p>
                            <p class="text text-person">
                              <span class="pr-2 font-weight-bold person-text-bold">{{ person.person_name }}</span>
                              <span class="pr-2">{{ person.position_name }}</span>
                            </p>
                          </div>
                          <div class="lstIndividual-label">
                            <p v-for="segment in person.segment_name" :key="segment" class="label-text">
                              <span class="span-label span-label--violet label-seg">{{ segment }}</span>
                            </p>
                          </div>
                        </li>
                      </ul>
                    </div>

                    <div v-else class="noData h-loadingperson">
                      <div class="noData-content">
                        <p class="noData-text">該当するデータがありません。</p>
                        <div class="noData-thumb">
                          <img src="@/assets/template/images/data/amico.svg" alt="" />
                        </div>
                      </div>
                    </div>
                    <div v-if="lstFacilityPersonal.length > 0" class="pagin-institution">
                      <PaginationTable
                        class="pagination-bord"
                        :page-size="paginationPerson.pageSize"
                        :total="paginationPerson.totalItems"
                        :current-page="paginationPerson.curentPage"
                        @current-change="handleCurrentChangePerson"
                      />
                    </div>
                  </div>
                </div>
              </el-tab-pane>
              <el-tab-pane v-if="propsPrams.non_medical_flag === 0" :disabled="!isFilter || !facilitySelected" label="メディカルスタッフ" name="medicalStaff">
                <div :class="`${loadingPageMedical ? 'pre-loader h-loadingMedical' : ''}`">
                  <Preloader v-if="loadingPageMedical" />
                  <div v-else>
                    <div v-if="lstMedicalStaff.length > 0" class="lstStaff">
                      <ul>
                        <li
                          v-for="item in lstDataDisplayMedicalStaff"
                          :key="item.medical_staff_cd"
                          :class="item.selected ? 'active' : ''"
                          @click="setSelectedMedicalStaff(item)"
                        >
                          {{ item.medical_staff_name }}
                        </li>
                      </ul>
                    </div>

                    <div v-else class="noData h-466">
                      <div class="noData-content">
                        <p class="noData-text">該当するデータがありません。</p>
                        <div class="noData-thumb">
                          <img src="@/assets/template/images/data/amico.svg" alt="" />
                        </div>
                      </div>
                    </div>
                    <div v-if="lstMedicalStaff.length > 0" class="pagin-institution">
                      <PaginationTable
                        class="pagination-bord"
                        :page-size="pageSizeMedical"
                        :current-page="curentPageMedical"
                        :total="lstMedicalStaff.length"
                        @current-change="handleCurrentChangeMedical"
                      />
                    </div>
                  </div>
                </div>
              </el-tab-pane>
            </el-tabs>
          </div>
        </div>

        <!-- mode Multi list person -->
        <div v-else class="groupFacility-content">
          <div class="groupFacility-box">
            <el-tabs v-model="activeTabName" class="custom-tab-person" @tab-click="handleClickTab()">
              <el-tab-pane :disabled="!isFilter" label="施設個人" name="person">
                <div class="formSearch">
                  <ul>
                    <li>
                      <label class="formSearch-label">個人名</label>
                      <div class="formSearch-control">
                        <el-input v-model="personal_name" clearable placeholder="個人名入力" class="form-control-input" />
                      </div>
                    </li>
                    <li>
                      <label class="formSearch-label">セグメント</label>
                      <div class="formSearch-flex">
                        <div class="formSearch-select">
                          <el-select v-model="segment_type" placeholder="未選択" class="form-control-select">
                            <el-option :value="''">未選択</el-option>
                            <el-option v-for="segment in lstSegment" :key="segment.segment_type" :label="segment.segment_name" :value="segment.segment_type">
                            </el-option>
                          </el-select>
                        </div>
                        <div class="formSearch-btn">
                          <el-button
                            :disabled="!isFilter"
                            class="btn-add btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                            @click="seachPerson()"
                          >
                            <i class="el-icon-search">
                              <span>検索</span>
                            </i>
                          </el-button>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
                <div :class="`${loadingPagePerson ? 'pre-loader h-loadingperson' : ''}`">
                  <Preloader v-if="loadingPagePerson" />
                  <div v-else>
                    <div v-if="lstFacilityPersonal.length > 0" class="lstIndividual">
                      <ul>
                        <li v-for="person in lstFacilityPersonal" :key="person.person_cd" class="cusor-default">
                          <div class="lstIndividual-info">
                            <p class="lstIndividual-text">
                              <span class="pr-2">{{ person.facility_short_name }}</span>
                              <span class="pr-2">{{ person.department_name }}</span>
                            </p>
                            <p class="lstIndividual-text text-person">
                              <span class="pr-2 font-weight-bold person-text-bold">{{ person.person_name }}</span>
                              <span class="pr-2">{{ person.position_name }}</span>
                            </p>
                          </div>
                          <div class="lstIndividual-segment mr-2 ml-2">
                            <p v-for="segment in person.segment_name" :key="segment" class="label-text">
                              <span class="span-label span-label--violet label-seg">{{ segment }}</span>
                            </p>
                          </div>
                          <div class="lstIndividual-btn">
                            <el-button
                              class="btn-add btn-add-w btn btn-outline-primary btn-outline-primary--cancel"
                              :disabled="checkFacilityPersonalSelected(person)"
                              @click="addItemFacilityPersonal(person)"
                            >
                              <i class="el-icon-plus">
                                <span>{{ checkFacilityPersonalSelected(person) ? '追加済' : '追加' }}</span>
                              </i>
                            </el-button>
                          </div>
                        </li>
                      </ul>
                    </div>

                    <div v-else class="noData h-loadingperson">
                      <div class="noData-content">
                        <p class="noData-text">該当するデータがありません。</p>
                      </div>
                    </div>
                    <div v-if="lstFacilityPersonal.length > 0" class="pagin-institution">
                      <PaginationTable
                        class="pagination-bord"
                        :page-size="paginationPerson.pageSize"
                        :total="paginationPerson.totalItems"
                        :current-page="paginationPerson.curentPage"
                        @current-change="handleCurrentChangePerson"
                      />
                    </div>
                  </div>
                </div>
              </el-tab-pane>
              <el-tab-pane v-if="propsPrams.non_medical_flag === 0" :disabled="!isFilter || !facilitySelected" label="メディカルスタッフ" name="medicalStaff">
                <div :class="`${loadingPageMedical ? 'pre-loader h-loadingMedical' : ''}`">
                  <Preloader v-if="loadingPageMedical" />
                  <div v-else>
                    <div class="lstStaff-data">
                      <ul v-if="lstMedicalStaff.length > 0">
                        <li v-for="item in lstDataDisplayMedicalStaff" :key="item.medical_staff_cd" class="cusor-default">
                          <span class="lstStaff-text">{{ item.medical_staff_name }}</span>
                          <el-button
                            class="btn-add btn-add-w btn btn-outline-primary btn-outline-primary--cancel"
                            :disabled="checkMedicalStaffSelected(item)"
                            @click="addItemFacilityPersonal(item)"
                          >
                            <i class="el-icon-plus">
                              <span>{{ checkMedicalStaffSelected(item) ? '追加済' : '追加' }}</span>
                            </i>
                          </el-button>
                        </li>
                      </ul>
                      <div v-else class="noData h-466">
                        <div class="noData-content">
                          <p class="noData-text">該当するデータがありません。</p>
                        </div>
                      </div>
                    </div>
                    <div v-if="lstMedicalStaff.length > 0" class="pagin-institution">
                      <PaginationTable
                        class="pagination-bord"
                        :page-size="pageSizeMedical"
                        :current-page="curentPageMedical"
                        :total="lstMedicalStaff.length"
                        @current-change="handleCurrentChangeMedical"
                      />
                    </div>
                  </div>
                </div>
              </el-tab-pane>
            </el-tabs>
          </div>
        </div>

        <div v-if="mode !== 'single'" class="groupFacility-info">
          <h2 class="selectList-title">選択リスト</h2>
          <div class="selectList">
            <ul v-if="lstSelected.length > 0">
              <li v-for="person in lstSelected" :key="person">
                <div class="selectList-info">
                  <p>
                    <span class="pr-2">{{ person.facility_short_name }}</span>
                    <span v-if="person.person_cd" class="pr-2">{{ person.department_name }}</span>
                  </p>

                  <p v-if="person.person_cd" class="selectList-info__text">
                    <span class="pr-2 font-weight-bold person-text-bold">{{ person.person_name }}</span>
                    <span class="pr-2">{{ person.position_name }}</span>
                  </p>
                  <p v-else>
                    <span class="pr-2 font-weight-bold person-text-bold">{{ person.medical_staff_name }}</span>
                  </p>
                </div>
                <el-button class="btn-circle btn--icon btn" @click="onRemoveItem(person)">
                  <i class="el-icon-close"></i>
                </el-button>
              </li>
            </ul>
            <div v-if="lstSelected.length === 0" class="noData">
              <div v-if="!loadingSelected" class="noData-content">
                <p class="noData-text">施設個人を選択して下さい。</p>
                <div class="noData-thumb">
                  <img src="@/assets/template/images/data/amico.svg" alt="" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="facilityBtn">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelBtn">キャンセル</button>
        <button :disabled="checkDisableResultBtn()" type="button" class="btn btn-primary" @click="returnData()">決定</button>
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { markRaw } from 'vue';
import _ from 'lodash';
import Z05_S04_facilityCustomerService from '@/api/Z05/Z05_S04_facilityCustomerService';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05S02AreaSelection from '@/views/Z05/Z05_S02_Area_Selection/Z05_S02_Area_Selection';

export default {
  name: 'Z05_S04_FacilityCustomer',
  components: { Z05S01Organization, Z05S02AreaSelection },
  props: {
    mode: {
      type: String,
      require: true,
      default: 'single'
    },

    propsPrams: {
      type: Object,
      require: true,
      default() {
        return {
          // (require)
          non_facility_flag: 0, // (require) // =1 facility_cd not edit || =0 facility_cd edit
          non_medical_flag: 0, // (require) // =1 hidden tab メディカルスタッフ || =0 show tab メディカルスタッフ

          checked_facility_relation_flag: 1, // =1 checked show Facility Relation || = 0 unchecked show Facility Relation
          disabled_facility_relation_flag: 1, // =1 edit checkbox Facility Relation || = 0 un edit checkbox Facility Relation

          // form
          org_cd: '',
          user_cd: '',
          user_name: '',
          target_product_cd: '',
          select_group_id: '',
          facility_category_type: '',
          prefecture_cd: '',
          prefecture_name: '',
          municipality_cd: '',
          municipality_name: '',
          facility_cd: [],
          facility_name: [],
          person_cd: []
        };
      }
    }
  },
  emits: ['onResult', 'onFinishScreen'],
  data() {
    return {
      paramsZ05S01: {
        userFlag: 1,
        mode: 'single',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
      },

      paramsZ05S02: {
        hierarchySelected: 'municipality',
        prefectureCode: '',
        municipalityCode: ''
      },

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

      activeNameItemBlock: 'search',
      activeTabName: 'person',

      loadingPage: false,
      loadingPageFacility: false,
      loadingPagePerson: false,
      loadingPageMedical: false,
      loadingSelected: false,

      lstConditionArea: [],
      lstConditionAreaFacility: [],
      lstFacilityPersonal: [],
      lstMedicalStaff: [],
      lstDataDisplayMedicalStaff: [],
      lstFacilityPersonalSelected: [],
      lstMedicalStaffSelected: [],
      lstSelected: [],

      lstTargetItem: [],
      lstSelectGroup: [],
      lstFacilityCategory: [],
      lstSegment: [],

      // params filter Personal
      segment_type: '',
      personal_name: '',

      formData: {
        user_cd: '',
        user_name: '',
        target_product_cd: '',
        select_group_id: '',
        facility_category_type: '',
        prefecture_cd: '',
        prefecture_name: '',
        municipality_cd: '',
        municipality_name: '',
        facility_cd: '',
        facility_name: ''
      },

      lstFaciliti_cd: [],
      lstFaciliti_name: [],

      checkedDisplayFacilityRelation: true,
      disabledFacilityRelation: false,
      tabNumber: 1,

      lstFacilityFlat: [],

      facilitySelected: null,
      paramsPersonalOld: {},
      paramsMedicalOld: {},

      pageSizeMedical: 100,
      curentPageMedical: 1,

      paginationFacility: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },

      paginationPerson: {
        pageSize: 100,
        curentPage: 1,
        totalItems: 0
      },

      isFilter: false,
      lstDataFacilitySearch: '',
      showRelation: 1
    };
  },

  mounted() {
    this.formData = {
      ...this.formData,
      user_cd: this.propsPrams.user_cd || '',
      user_name: this.propsPrams.user_name || '',
      target_product_cd: this.propsPrams.target_product_cd || '',
      select_group_id: this.propsPrams.select_group_id || '',
      facility_category_type: this.propsPrams.facility_category_type || '',
      prefecture_cd: this.propsPrams.prefecture_cd || '',
      prefecture_name: this.propsPrams.prefecture_name || '',
      municipality_cd: this.propsPrams.municipality_cd || '',
      municipality_name: this.propsPrams.municipality_name || '',
      facility_cd: this.propsPrams.facility_cd?.join(' ') || '',
      facility_name: this.propsPramsfacility_name?.join(' ') || ''
    };

    this.checkedDisplayFacilityRelation = this.propsPrams.checked_facility_relation_flag === 0 ? false : true;
    this.disabledFacilityRelation = this.propsPrams.disabled_facility_relation_flag === 1 ? true : false;
    this.showRelation = this.checkedDisplayFacilityRelation ? 1 : 0;

    this.lstFaciliti_cd = this.propsPrams.facility_cd || [];
    this.lstFaciliti_name = this.propsPramsfacility_name || [];
    if (this.propsPrams.user_cd && this.propsPrams.org_cd) {
      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        orgCdList: [this.propsPrams.org_cd],
        userCdList: [
          {
            org_cd: this.propsPrams.org_cd,
            user_cd: this.propsPrams.user_cd
          }
        ]
      };
    }
    this.getConditionArea();
  },

  methods: {
    showFacilityDepartment(item, index) {
      let facility = {
        ...item,
        isShowFacilityDepartment: !item.isShowFacilityDepartment
      };
      this.lstConditionAreaFacility[index] = facility;

      let w = document.getElementById(`wrap${item.facility_cd}`);
      let l = document.getElementById(`list${item.facility_cd}`);

      if (w.classList.contains('open')) {
        w.classList?.remove('open');
        w.style.height = 0;
      } else {
        w.classList?.remove('close');
        w.classList?.add('open');
        w.style.height = `${l.clientHeight}px`;
      }
    },

    cancelBtn() {
      this.$emit('onFinishScreen');
    },

    displayFacilityRelated() {
      this.showRelation = this.checkedDisplayFacilityRelation ? 1 : 0;
      if (!this.checkedDisplayFacilityRelation) {
        if (!this.checkParentSelected()) {
          this.facilitySelected = null;

          this.lstConditionAreaFacility.forEach((x) => {
            x.selected = false;
            if (x.children?.length > 0) {
              x.children.forEach((x) => {
                x.selected = false;
              });
            }
          });
          if (this.activeTabName === 'person') {
            this.loadingPageMedical = false;
            this.getFacilityPersonal();
          } else {
            this.loadingPagePerson = false;
            this.getMedicalStaff();
          }
        }
      }
    },

    checkParentSelected() {
      let datas = this.lstConditionAreaFacility;
      for (let index = 0; index < datas.length; index++) {
        const element = datas[index];
        if (element.selected) {
          return true;
        }
      }
      return false;
    },

    // call api
    getConditionArea() {
      this.loadingPage = true;
      this.loadingPageFacility = true;
      this.loadingPagePerson = true;
      this.loadingPageMedical = true;
      Z05_S04_facilityCustomerService.getConditionArea({ person_flag: 1 })
        .then((res) => {
          this.lstTargetItem = res?.data?.data?.target_product;
          this.lstSelectGroup = res?.data?.data?.select_group;
          this.lstFacilityCategory = res?.data?.data?.facility_category;
          this.lstSegment = res?.data?.data?.segment;
          if (this.propsPrams.facility_cd && this.propsPrams.facility_cd.length > 0) {
            this.onFilterData(true);
          } else {
            this.loadingPageFacility = false;
            this.loadingPagePerson = false;
            this.loadingPageMedical = false;
          }
        })
        .catch((err) => {
          this.loadingPageFacility = false;
          this.loadingPagePerson = false;
          this.loadingPageMedical = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S04', idNodeNotify: 'msfa-notify-Z05S04' });
        })
        .finally(() => {
          this.loadingPage = false;
        });
    },

    onFilterData(isDefault) {
      this.paginationFacility = {
        ...this.paginationFacility,
        curentPage: 1,
        totalItems: 0
      };
      this.paginationPerson = {
        ...this.paginationPerson,
        curentPage: 1,
        totalItems: 0
      };

      if (this.checkedValidation()) {
        this.facilitySelected = null;
        this.lstConditionAreaFacility = [];
        this.lstFacilityFlat = [];
        this.lstFacilityPersonal = [];
        this.lstMedicalStaff = [];
        this.paramsPersonalOld = {};

        this.activeTabName = 'person';
        this.tabNumber = 1;

        this.getFacilityByCondition(isDefault);
      } else {
        this.notifyModal({
          message: '検索条件は1つ以上指定する必要があります。',
          type: 'error',
          classParent: 'modal-body-Z05S04',
          idNodeNotify: 'msfa-notify-Z05S04'
        });
        this.loadingPageFacility = false;
        this.loadingPagePerson = false;
        this.loadingPageMedical = false;
      }
    },

    getFacilityByCondition(isDefault) {
      this.loadingPageFacility = true;
      this.loadingPagePerson = true;
      this.loadingPageMedical = true;
      if (isDefault) {
        this.loadingSelected = true;
      }
      let facilityCodes = this.formData.facility_cd
        ?.replace(/^\s+|\s+$/gm, '')
        ?.split(' ')
        ?.toString();
      let facilityNames = this.formData.facility_name
        ?.replace(/^\s+|\s+$/gm, '')
        ?.split(' ')
        .toString();
      let params = {
        ...this.formData,
        facility_cd: facilityCodes,
        facility_name: facilityNames,
        offset: this.paginationFacility.curentPage - 1,
        limit: this.paginationFacility.pageSize,
        showRelation: 1
      };

      this.activeNameItemBlock = 'result';
      this.facilitySelected = null;
      this.lstConditionAreaFacility = [];
      this.lstFacilityFlat = [];
      Z05_S04_facilityCustomerService.getFacilityByCondition(params)
        .then((res) => {
          let data = res?.data?.data;
          let pageined = res.data?.data?.pagination;
          this.lstDataFacilitySearch = data?.list_facility;
          this.lstConditionAreaFacility = data.records;

          this.paginationFacility = {
            ...this.paginationFacility,
            totalItems: pageined.total_items
          };

          for (let i = 0; i < this.lstConditionAreaFacility.length; i++) {
            let item = this.lstConditionAreaFacility[i];
            let subChild = [];

            if (item.relation_facility && item.relation_facility.length > 0) {
              subChild = subChild.concat(item.relation_facility);
            }
            if (item.department_line && item.department_line.length > 0) {
              subChild = subChild.concat(item.department_line);
            }

            item.children = subChild;
            this.lstConditionAreaFacility[i] = item;
            this.flaternFacility(this.lstConditionAreaFacility[i]);
          }

          if (this.lstConditionAreaFacility.length > 0) {
            this.isFilter = true;
            if (this.lstConditionAreaFacility.length === 1) {
              this.setSelectedFacility(this.lstConditionAreaFacility[0]);
            }
            if (this.activeTabName === 'person') {
              this.loadingPageMedical = false;
              this.getFacilityPersonal(data?.list_facility, isDefault);
            } else {
              this.loadingPagePerson = false;
              this.getMedicalStaff(isDefault);
            }
          } else {
            this.loadingPagePerson = false;
            this.loadingPageMedical = false;
            this.loadingSelected = false;
            this.lstFacilityPersonal = [];
            this.lstMedicalStaff = [];
            this.paramsPersonalOld = {};
            this.isFilter = false;
          }
        })
        .catch((err) => {
          this.loadingPagePerson = false;
          this.loadingPageMedical = false;
          this.loadingSelected = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S04', idNodeNotify: 'msfa-notify-Z05S04' });
        })
        .finally(() => {
          this.loadingPageFacility = false;
        });
    },

    checkedValidation() {
      let dataFilterEmp = {
        user_cd: '',
        user_name: '',
        target_product_cd: '',
        select_group_id: '',
        facility_category_type: '',
        prefecture_cd: '',
        prefecture_name: '',
        municipality_cd: '',
        municipality_name: '',
        facility_cd: '',
        facility_name: ''
      };
      let checked = !_.isEqual(dataFilterEmp, this.formData);
      return checked;
    },

    flaternFacility(node) {
      if (!this.lstFacilityFlat.some((x) => x.facility_cd === node.facility_cd)) {
        this.lstFacilityFlat.push(node);
        if (node?.children?.length > 0) {
          for (let i = 0; i < node.children.length; i++) {
            this.flaternFacility(node.children[i]);
          }
        }
      }
    },

    seachPerson() {
      this.paginationPerson.curentPage = 1;
      this.getFacilityPersonal();
    },

    getFacilityPersonal(list_facility, isDefault) {
      let fc = this.lstDataFacilitySearch || '';
      let params = {
        facility_cd: list_facility ? list_facility : this.facilitySelected ? this.facilitySelected.facility_cd : fc,
        person_name: this.personal_name,
        segment_type: this.segment_type,
        tabNumber: this.tabNumber,
        offset: this.paginationPerson.curentPage - 1,
        limit: this.paginationPerson.pageSize,
        department_cd: this.facilitySelected?.department_cd ? this.facilitySelected?.department_cd : '',
        select_group_id: this.formData.select_group_id
      };
      if (isDefault || !this.isEqualObject(this.paramsPersonalOld, params)) {
        this.paramsPersonalOld = { ...params };
        this.lstFacilityPersonal = [];
        this.loadingPagePerson = true;
        Z05_S04_facilityCustomerService.getFacilityPersonal(params)
          .then((res) => {
            this.lstFacilityPersonal = res?.data?.data.records;
            let pageined = res.data?.data?.pagination;

            this.paginationPerson = {
              ...this.paginationPerson,
              totalItems: pageined?.total_items
            };

            if (isDefault) {
              if (this.propsPrams.person_cd && this.propsPrams.person_cd.length > 0) {
                if (this.mode === 'single') {
                  let sitem = this.lstFacilityPersonal.find((x) => x.person_cd === this.propsPrams.person_cd[0]);
                  if (sitem) {
                    this.setSelectedPersonal(sitem, true);
                  }
                } else {
                  this.propsPrams.person_cd.forEach((el) => {
                    let item = this.lstFacilityPersonal.find((x) => x.person_cd === el);
                    if (item) {
                      this.addItemFacilityPersonal(item);
                    }
                  });
                }
              } else {
                this.lstFacilityPersonalSelected = [];
                this.lstSelected = [];
              }
            }
          })
          .catch((err) => {
            this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S04', idNodeNotify: 'msfa-notify-Z05S04' });
          })
          .finally(() => {
            this.loadingPagePerson = false;
            this.loadingSelected = false;
          });
      }
    },

    getMedicalStaff() {
      let fc = this.lstDataFacilitySearch || '';

      let params = {
        facility_cd: this.facilitySelected ? this.facilitySelected.facility_cd : fc,
        tabNumber: this.tabNumber
      };
      if (!this.isEqualObject(this.paramsMedicalOld, params)) {
        this.lstMedicalStaff = [];
        this.loadingPageMedical = true;
        this.paramsMedicalOld = { ...params };
        Z05_S04_facilityCustomerService.getMedicalStaff(params)
          .then((res) => {
            this.lstMedicalStaff = res?.data?.data;
            this.curentPageMedical = 1;
            this.getDataDisplayMedical();
          })
          .catch((err) => {
            this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S04', idNodeNotify: 'msfa-notify-Z05S04' });
          })
          .finally(() => {
            this.loadingPageMedical = false;
            this.loadingSelected = false;
          });
      }
    },

    // click tab 1: 施設個人, 2: メディカルスタッフ
    handleClickTab(isReload) {
      if ((this.activeTabName === 'person' && this.tabNumber !== 1) || (this.activeTabName !== 'person' && this.tabNumber === 1) || isReload) {
        this.tabNumber = this.activeTabName === 'person' ? 1 : 2;
        if (this.mode === 'single') {
          this.lstMedicalStaffSelected = [];
          this.lstFacilityPersonalSelected = [];
          this.lstSelected = [];
        }

        if (this.tabNumber === 1) {
          this.getFacilityPersonal();
          this.paramsMedicalOld = {
            ...this.paramsMedicalOld,
            tabNumber: this.tabNumber
          };
          if (isReload) {
            this.paramsPersonalOld = {
              ...this.paramsPersonalOld,
              tabNumber: Math.random()
            };
          }
        } else {
          this.getMedicalStaff();

          this.paramsPersonalOld = {
            ...this.paramsPersonalOld,
            tabNumber: this.tabNumber
          };
          if (isReload) {
            this.paramsMedicalOld = {
              ...this.paramsMedicalOld,
              tabNumber: Math.random()
            };
          }
        }
      }
    },

    // check selected
    setSelectedFacility(facility, itemChild, isChild) {
      this.paginationPerson.curentPage = 1;
      this.facilitySelected = null;
      if (this.mode === 'single') {
        this.lstFacilityPersonalSelected = [];
        this.lstMedicalStaffSelected = [];
        this.lstSelected = [];
      }

      if (isChild) {
        this.lstConditionAreaFacility.forEach((x) => {
          x.selected = false;
        });
        facility.children.forEach((x) => {
          if (x.facility_cd === itemChild.facility_cd) {
            if (itemChild.department_cd) {
              if (itemChild.department_cd === x.department_cd) {
                x.selected = !x.selected;
                if (x.selected) {
                  this.facilitySelected = { ...x };
                }
              } else {
                x.selected = false;
              }
            } else {
              x.selected = !x.selected;
              if (x.selected) {
                this.facilitySelected = { ...x };
              }
            }
          } else {
            x.selected = false;
          }
        });
      } else {
        this.lstConditionAreaFacility.forEach((x) => {
          if (x.facility_cd === facility.facility_cd) {
            x.selected = !x.selected;
            if (x.selected) {
              this.facilitySelected = { ...x };
            }
          } else {
            x.selected = false;
          }

          if (x.children?.length > 0) {
            x.children.forEach((x) => {
              x.selected = false;
            });
          }
        });
      }

      if (!this.facilitySelected && this.lstConditionAreaFacility.length === 1) {
        this.setSelectedFacility(this.lstConditionAreaFacility[0]);
      }
      if (!this.facilitySelected) {
        this.activeTabName = 'person';
        this.tabNumber = 1;
      }
      this.handleClickTab(true);
    },

    setSelectedPersonal(item, isDefault) {
      this.lstFacilityPersonalSelected = [];
      this.lstSelected = [];
      this.lstFacilityPersonal.forEach((x) => {
        if (x.person_cd === item.person_cd && x.facility_cd === item.facility_cd) {
          if (isDefault) {
            x.selected = true;
          } else {
            x.selected = !x.selected;
          }
          if (x.selected) {
            this.lstFacilityPersonalSelected.push(x);
            this.lstSelected.push(x);
          }
        } else {
          x.selected = false;
        }
      });

      this.loadingPagePerson = false;
    },

    setSelectedMedicalStaff(item) {
      this.lstMedicalStaffSelected = [];
      this.lstSelected = [];
      this.lstMedicalStaff.forEach((x) => {
        if (x.medical_staff_cd === item.medical_staff_cd) {
          x.selected = !x.selected;
          if (x.selected) {
            this.lstMedicalStaffSelected.push(x);
            this.lstSelected.push(x);
          }
        } else {
          x.selected = false;
        }
      });
    },

    addItemFacilityPersonal(item) {
      if (item.person_cd) {
        if (!this.lstSelected.some((x) => x.person_cd === item.person_cd && x.facility_cd === item.facility_cd)) {
          this.lstFacilityPersonalSelected.push(item);
          this.lstSelected.push(item);
        }
      } else {
        if (!this.lstSelected.some((x) => x.medical_staff_cd === item.medical_staff_cd && x.facility_cd === item.facility_cd)) {
          let newItem = {
            ...item,
            facility_cd: this.facilitySelected.facility_cd,
            facility_short_name: this.facilitySelected.facility_short_name
          };
          this.lstMedicalStaffSelected.push(newItem);
          this.lstSelected.push(newItem);
        }
      }
    },

    checkFacilityPersonalSelected(item) {
      let index = this.lstFacilityPersonalSelected.findIndex((x) => x.person_cd === item.person_cd && x.facility_cd === item.facility_cd);

      return index > -1 ? true : false;
    },

    addItemMedicalStaff(item) {
      if (!this.lstMedicalStaffSelected.some((x) => x.medical_staff_cd === item.medical_staff_cd)) {
        this.lstMedicalStaffSelected.push(item);
      }
    },

    checkMedicalStaffSelected(item) {
      let index = this.lstMedicalStaffSelected.findIndex(
        (x) => x.medical_staff_cd === item.medical_staff_cd && this.facilitySelected && this.facilitySelected.facility_cd === x.facility_cd
      );

      return index > -1 ? true : false;
    },

    onRemoveItem(item) {
      if (item.person_cd) {
        let index = this.lstFacilityPersonalSelected.findIndex((x) => x.person_cd === item.person_cd && x.facility_cd === item.facility_cd);
        this.lstFacilityPersonalSelected.splice(index, 1);
        let indexS = this.lstSelected.findIndex((x) => x.person_cd === item.person_cd && x.facility_cd === item.facility_cd);
        this.lstSelected.splice(indexS, 1);
      } else {
        let index = this.lstMedicalStaffSelected.findIndex((x) => x.medical_staff_cd === item.medical_staff_cd && x.facility_cd === item.facility_cd);
        this.lstMedicalStaffSelected.splice(index, 1);
        let indexS = this.lstSelected.findIndex((x) => x.medical_staff_cd === item.medical_staff_cd && x.facility_cd === item.facility_cd);
        this.lstSelected.splice(indexS, 1);
      }
    },

    // page
    handleCurrentChangeFacility(page) {
      this.paginationFacility.curentPage = page;
      this.getFacilityByCondition();
    },

    async handleCurrentChangePerson(page) {
      this.paginationPerson.curentPage = page;
      this.getFacilityPersonal();
    },

    handleCurrentChangeMedical(page) {
      this.curentPageMedical = page;
      this.lstDataDisplayMedicalStaff = [];
      this.getDataDisplayMedical();
    },

    async getDataDisplayMedical() {
      let page = this.curentPageMedical - 1;
      this.lstDataDisplayMedicalStaff = await this.lstMedicalStaff.slice(this.pageSizeMedical * page, this.pageSizeMedical * this.curentPageMedical);
    },

    // compare object
    isEqualObject(object1, object2) {
      const keys1 = Object.keys(object1);
      const keys2 = Object.keys(object2);
      if (keys1.length !== keys2.length) {
        return false;
      }
      for (let key of keys1) {
        if (object1[key] !== object2[key]) {
          return false;
        }
      }
      return true;
    },

    // check form
    handleRemoveUser() {
      this.formData.user_cd = '';
      this.formData.user_name = '';

      this.showModalZ05S01 = false;

      this.paramsZ05S01 = {
        ...this.paramsZ05S01,
        orgCdList: [],
        userCdList: []
      };
    },

    handleRemovePrefecture() {
      this.formData.prefecture_cd = '';
      this.formData.prefecture_name = '';
      this.formData.municipality_cd = '';
      this.formData.municipality_name = '';

      this.showModalZ05S02 = false;

      this.paramsZ05S02 = {
        ...this.paramsZ05S02,
        prefectureCode: '',
        municipalityCode: ''
      };
    },

    validationFacility_cd() {
      this.formData.facility_cd = this.formData.facility_cd.replace(/\s+/, ' ');
      this.formData.facility_cd = this.convertToHalf(this.formData.facility_cd);
      let facility_cd = this.formData.facility_cd?.replace(/^\s+|\s+$/gm, '')?.split(' ');

      for (let index = 0; index < facility_cd.length; index++) {
        const element = facility_cd[index];
        if (element.length > 25) {
          return true;
        }
      }
      return false;
    },

    validationFacility_name() {
      this.formData.facility_name = this.formData.facility_name.replace(/\s+/, ' ');
      this.formData.facility_name = this.convertToHalf(this.formData.facility_name);
      let facility_name = this.formData.facility_name?.replace(/^\s+|\s+$/gm, '')?.split(' ');
      for (let index = 0; index < facility_name.length; index++) {
        const element = facility_name[index];
        if (element.length > 255) {
          return true;
        }
      }
      return false;
    },

    checkDisableSearchPersonBtn() {
      if (this.validationFacility_name() || this.validationFacility_cd()) {
        return true;
      }
      return false;
    },

    // MODAL Child
    // open Modal Z05 - S01
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.paramsZ05S01 },
        width: '45rem',
        customClass: 'custom-dialog modal-fixed',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      this.paramsZ05S01.orgCdList = [];
      this.paramsZ05S01.userCdList = [];
      e.userSelected?.forEach((x) => {
        this.paramsZ05S01.orgCdList.push(x.org_cd);
      });
      e.userSelected?.forEach((x) => {
        this.paramsZ05S01.userCdList.push({
          org_cd: x.org_cd,
          user_cd: x.user_cd
        });
      });

      this.formData.user_cd = e.userSelected[0]?.user_cd;
      this.formData.user_name = e.userSelected[0]?.user_name;

      this.showModalZ05S01 = false;
    },

    // open Modal Z05 - S02
    openModal_Z05_S02() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '地区選択',
        isShowModal: true,
        component: markRaw(Z05S02AreaSelection),
        width: this.paramsZ05S02.hierarchySelected === 'municipality' ? '47rem' : '35rem',
        customClass: `custom-dialog modal-fixed ${this.paramsZ05S02.hierarchySelected === 'municipality' ? '' : 'modal-fixed-min '}`,
        props: {
          hierarchySelected: this.paramsZ05S02.hierarchySelected,
          prefectureCode: this.paramsZ05S02.prefectureCode,
          municipalityCode: this.paramsZ05S02.municipalityCode
        }
      };
    },

    onResultModalZ05S02(e) {
      this.paramsZ05S02.prefectureCode = e.prefectureSelected?.prefecture_cd;
      this.paramsZ05S02.municipalityCode = e.municipalitySelected?.municipality_cd;

      this.formData.prefecture_cd = e.prefectureSelected?.prefecture_cd;
      this.formData.municipality_cd = e.municipalitySelected?.municipality_cd;
      this.formData.prefecture_name = e.prefectureSelected?.prefecture_name;
      this.formData.municipality_name = e.municipalitySelected?.municipality_name;
    },

    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S02') {
          this.onResultModalZ05S02(e);
        }
        if (e.screen === 'Z05_S01') {
          this.onResultModalZ05S01(e);
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    checkDisableResultBtn() {
      if (this.lstFacilityPersonalSelected.length > 0 || this.lstMedicalStaffSelected.length > 0) {
        return false;
      }
      return true;
    },

    // return Data
    returnData() {
      let lstFacilitySelected = [];
      this.lstFacilityFlat.forEach((x) => {
        if (this.lstFacilityPersonalSelected.some((e) => e.facility_cd === x.facility_cd)) {
          if (!lstFacilitySelected.some((ele) => ele.facility_cd === x.facility_cd)) {
            lstFacilitySelected.push(x);
          }
        }
        if (this.lstMedicalStaffSelected.some((e) => e.facility_cd === x.facility_cd)) {
          if (!lstFacilitySelected.some((ele) => ele.facility_cd === x.facility_cd)) {
            lstFacilitySelected.push(x);
          }
        }
      });

      let result = {
        screen: 'Z05_S04',
        facilitySelected: lstFacilitySelected,
        facilityPersonalSelected: this.lstFacilityPersonalSelected,
        medicalStaffSelected: this.lstMedicalStaffSelected,
        filterData: this.formData
      };
      this.$emit('onFinishScreen', result);
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet-mini: 1024px;
.customDiv {
  height: 0;
  transition: height 0.6s ease-in-out;
  overflow: hidden;
}
.close {
  display: none;
}
.modal-body--changePd {
  padding-top: 20px;
}
.groupFacility {
  display: flex;
  flex-wrap: wrap;
  .groupFacility-form {
    width: 45%;
  }
  .groupFacility-content {
    width: 55%;
    padding-left: 20px;
  }
}
.groupFacility--change {
  margin-right: -21px;
  .groupFacility-form {
    width: 31%;
    @media (max-width: $viewport_tablet) {
      width: 45%;
    }
  }
  .groupFacility-content {
    width: 42%;
    @media (max-width: $viewport_tablet) {
      width: 55%;
    }
  }
  .groupFacility-info {
    padding-left: 20px;
    width: 27%;
    @media (max-width: $viewport_tablet) {
      width: 100%;
      padding: 20px 0 0;
    }
    .noData {
      text-align: center;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      padding: 0 20px;
      height: 536px;
      @media (max-width: $viewport_tablet) {
        height: inherit;
        padding: 30px 20px;
      }
      .noData-content {
        width: 100%;
      }
      .noData-text {
        color: #99a5ae;
      }
      .noData-thumb {
        margin-top: 20px !important;
        display: flex;
        flex-direction: row;
        justify-content: center;
      }
    }

    .selectList-title {
      font-weight: 700;
      font-size: 1rem;
      padding: 12px 20px;
      height: 46px;
    }
    .selectList {
      background: #fff;
      box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
      border-radius: 8px 0 0 8px;
      ul {
        overflow-y: auto;
        height: 536px;

        li {
          padding: 10px 13px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          border-bottom: 1px solid #e3e3e3;
          .selectList-info {
            padding-right: 10px;
            &__text {
              display: flex;
              align-items: baseline;
            }
          }
          .btn {
            min-width: 32px;
          }
        }
      }
    }
  }
}

.el-collapse .el-collapse-item.is-active .selection-head {
  border-radius: 8px 8px 0 0;
}

.groupForm {
  margin: -20px;
  .selection {
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
    padding: 20px;
    .selection-head {
      background: #e9f8ff;
      padding: 14px 0;
      border-radius: 8px;

      .selection-head-title {
        padding: 14px 10px 14px 48px;
        font-size: 16px;
        font-weight: 700;
        position: relative;
        &::after {
          position: absolute;
          top: calc(50% - 6px);
          left: 22px;
          content: '';
          display: block;
          width: 18px;
          height: 12px;
          display: block;
          background: url(~@/assets/template/images/icon_arrow-line.svg) no-repeat 50%;
          transition: 400ms all;
        }

        &[aria-expanded='true'] {
          border-radius: 8px 8px 0 0;
          &::after {
            background: url(~@/assets/template/images/icon_arrow-line.svg) no-repeat 50%;
            transform: rotate(180deg);
          }
        }
      }
    }
  }

  .selection-collapse {
    box-shadow: unset;
    border-radius: 8px;
    background: transparent;
  }

  .facility-form {
    display: flex;
    flex-wrap: wrap;
    margin-left: -10px;
    padding: 0 10px 10px;
    height: 415px;
    margin-right: 5px;
    overflow-y: auto;
    li {
      width: 50%;
      margin-top: 12px;
      padding-left: 10px;
    }
    .facility-form__label {
      margin-bottom: 6px;
    }
  }
  .facility-btnSearch {
    text-align: right;
    padding: 10px 20px 10px;
    box-shadow: 0px -3px 6px #e3e3e3;
    .btn {
      padding: 0 12px;
      height: 32px;
      .btn-iconLeft {
        top: -2px;
      }
    }
  }
  .checkBox-facility {
    padding: 10px 18px;
    height: 52px;
  }
  .lst-institution {
    overflow-y: auto;
    overflow-x: hidden;
    height: 365px;
    &--box {
      > ul {
        > li {
          .institution-header {
            &[aria-expanded='true'] {
              box-shadow: 0px -2px 5px rgba(183, 195, 203, 0.45), 0px 2px 5px rgba(183, 195, 203, 0.45);
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
          padding: 14px 12px 14px 12px;
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
            right: 21px;
            content: '';
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #5f6b73;
            transition: 400ms all;
          }
          .header-title {
            color: #5f6b73;
            font-weight: normal;
            padding-right: 30px;
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
            cursor: pointer;
            justify-content: space-between;
            padding: 14px 20px;
            border-bottom: 1px solid #dfe0e0;
            &:last-child {
              border-bottom: none;
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
        .selected {
          background: #eef6ff;
          border-left: 4px solid #448add;
          box-shadow: 0px -2px 5px rgba(183, 195, 203, 0.45), 0px 2px 5px rgba(183, 195, 203, 0.45);

          .header-title {
            font-weight: bold;
          }
        }
      }
    }
  }
}
.groupFacility-box {
  background: #ffffff;
  box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
  border-radius: 8px;
  .navTabs-modal {
    padding: 0 8px;
    li {
      width: 50%;
      border-bottom: 1px solid #e3e3e3;
      text-align: center;
      a {
        padding: 14px 4px;
        display: block;
        font-size: 1.12rem;
        font-weight: 700;
        color: #b7c3cb;
        position: relative;
        @media (max-width: $viewport_desktop) {
          font-size: 1rem;
        }
        &:hover {
          text-decoration: none;
          color: #5f6b73;
        }
        &.active {
          color: #5f6b73;
          &::after {
            position: absolute;
            bottom: -1px;
            left: 0;
            content: '';
            width: 100%;
            height: 3px;
            background: #448add;
          }
        }
      }
    }
  }
  .formSearch {
    box-shadow: 0px 3px 6px #e3e3e3;
    background: #fff;
    padding: 10px 20px 15px;
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -10px;
      li {
        width: 40%;
        padding-left: 10px;
        &:last-child {
          width: 60%;
        }
      }
    }
    .formSearch-label {
      margin-bottom: 6px;
    }
    .formSearch-flex {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      .formSearch-select {
        width: calc(100% - 72px);
        padding-right: 20px;
        @media (max-width: $viewport_tablet) {
          padding-right: 10px;
        }
      }
      .formSearch-btn {
        width: 72px;
        .btn {
          padding: 0 10px;
          height: 32px;
          .btn-iconLeft {
            top: -2px;
          }
        }
      }
    }
  }
  .lstIndividual-data {
    overflow-y: auto;
    height: 343px;
    ul {
      li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 13px;
        border-bottom: 1px solid #e3e3e3;
        position: relative;
      }
    }

    .lstIndividual-text {
      padding-right: 10px;

      .span-label {
        margin: 6px 3px 0 0;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .lstIndividual-btn {
      .btn {
        height: 32px;
        padding: 0 9px;
        white-space: nowrap;
        .btn-iconLeft {
          top: -2px;
        }
      }
    }
  }
  .lstIndividual {
    overflow-y: auto;
    height: 398px;
    ul {
      li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 13px;
        border-bottom: 1px solid #e3e3e3;
        cursor: pointer;
        position: relative;
        &.active {
          font-weight: 700;
          background: #eef6ff;
          box-shadow: 0px -2px 5px rgba(183, 195, 203, 0.45), 0px 2px 5px rgba(183, 195, 203, 0.45);
          &::after {
            position: absolute;
            top: 0;
            left: 0;
            content: '';
            width: 4px;
            height: 100%;
            background: #448add;
          }
        }
      }
    }
    .lstIndividual-label {
      .label-text {
        margin-bottom: 2px;
        &:last-child {
          margin-bottom: 0;
        }
        .span-label {
          min-width: 58px;
        }
      }
    }

    .lstIndividual-info {
      flex-grow: 1;
    }
    .lstIndividual-segment {
      flex: 0 0 60px;
    }
    .lstIndividual-btn {
      flex: 0 0 90px;
    }
  }
  .lstStaff {
    overflow-y: auto;
    height: 480px;
    ul {
      li {
        padding: 10px 15px;
        border-bottom: 1px solid #e3e3e3;
        cursor: pointer;
        position: relative;
        &.active {
          font-weight: 700;
          background: #eef6ff;
          box-shadow: 0px -2px 5px rgba(183, 195, 203, 0.45), 0px 2px 5px rgba(183, 195, 203, 0.45);
          &::after {
            position: absolute;
            top: 0;
            left: 0;
            content: '';
            width: 4px;
            height: 100%;
            background: #448add;
          }
        }
      }
    }
  }
  .lstStaff-data {
    overflow-y: auto;
    height: 492px;
    ul {
      li {
        padding: 10px 15px;
        border-bottom: 1px solid #e3e3e3;
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        .lstStaff-text {
          padding-right: 10px;
        }
        .btn {
          height: 32px;
          padding: 0 9px;
          white-space: nowrap;
          .btn-iconLeft {
            top: -2px;
          }
        }
      }
    }
  }
}
.pagin-institution {
  padding: 16px 8px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  box-shadow: 0px -3px 6px #e3e3e3;
  height: 62px;
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

// not allow
.not-allow {
  cursor: not-allowed;
  color: #b7c3cb;
  a:hover {
    cursor: not-allowed;
    color: #b7c3cb !important;
  }
}

.btn-collaps {
  transition: 400ms all;
  background: transparent;
  border: none;
  height: 10px;
  flex-flow: column-reverse;
  line-height: 10px;
  min-height: 10px;
  padding: 0 20px;
  i {
    font-size: 18px;
    align-items: center;
    line-height: 10px;
    vertical-align: baseline;
  }
}
.icon-expand {
  transform: rotate(180deg);
}

.btn-add {
  justify-content: center;
  align-items: center;
  padding: 0px 12px;
  height: 32px;
  background: #ffffff;
  color: #448add;
  border: 1px solid #448add;
  box-sizing: border-box;
  border-radius: 24px;
  font-size: 15px;
  letter-spacing: 0.03em;
  font-weight: bold;
  &.is-disabled {
    color: var(--el-button-disabled-font-color);
    border-color: var(--el-button-disabled-border-color);
  }

  i {
    font-weight: bold;
    span {
      margin-left: 5px;
    }
  }
}

.btn-add-w {
  width: 90px;
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
    margin-top: 2px;
  }
  .el-icon-close:before {
    color: unset;
  }
}

.removeIconTriangle::after {
  content: unset !important;
}

.btn:disabled {
  cursor: not-allowed;
  &:hover {
    color: #59a5ff;
    background: #fff;
  }
}

.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  font-size: 80%;
  color: #dc3545;
}

// height
.h-22r {
  height: 22rem;
}
.h-27r {
  height: 27rem;
}
.h-415 {
  height: 467px;
}
.h-466 {
  height: 466px;
}

.label-seg {
  width: 58px;
  height: 24px;
  margin: 2px 0px;
}

.custom-input {
  position: relative;
}
.iconClear {
  cursor: pointer;
  position: absolute;
  right: 35px;
  top: 33%;
  width: 20px;
}

// tag input
.tag-a {
  color: #59a5ff !important;
  cursor: pointer;
  &:hover {
    text-decoration: underline !important;
  }
}

.el-tag-custom {
  color: #225999;
  background: #d1e4ff;
  height: 27px;
  line-height: 23px;
  font-size: 14px;
  align-items: center;
  margin: 2px 10px 2px 0;
  border-radius: 24px;
}

.accordionItem {
  padding-bottom: 0 !important;

  box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
  &:first-of-type {
    margin-bottom: 20px;
  }
}

.btn {
  line-height: 24px;
}

.h-414 {
  height: 467px;
}
.h-loadingperson {
  height: 448px;
  border-radius: 0 0 8px 8px;
}
.h-loadingMedical {
  height: 540px;
  border-radius: 0 0 8px 8px;
}

.facilityItem {
  display: flex;
  flex-flow: nowrap;
  justify-content: space-between;
  align-items: stretch;

  .left {
    cursor: pointer;
    display: flex;
    flex-grow: 1;
    padding: 10px 0 10px 13px;
    min-height: 59px;
    align-items: center;
    .text {
      align-items: center;
      color: #5f6b73;
      flex-grow: 1;
    }
  }

  .right {
    cursor: pointer;
    padding: 9px 20px 9px 5px;
    flex: 0 0 40px;
    background: url(~@/assets/images/icon_triangle.svg) no-repeat 50%;
    transition: transform 0.7s;
  }

  .isCollapse {
    background: url(~@/assets/images/icon_triangle.svg) no-repeat 50%;
    transform: rotate(180deg);
    transition: transform 0.7s;
  }
}

.selected .left,
.institution-sub li.selected {
  padding-left: 16px !important;
}

.customDivs {
  -webkit-animation-name: formVisit; /* Safari 4.0 - 8.0 */
  -webkit-animation-duration: 2s;
  animation-name: formVisit;
  animation-duration: 2s;
}

@-webkit-keyframes formVisit {
  0% {
    bottom: 10px;
  }
  100% {
    bottom: 0px;
  }
}
@keyframes formVisit {
  0% {
    bottom: 10px;
  }
  100% {
    bottom: 0px;
  }
}
.lstIndividual-text {
  align-items: center;
}

.text-person {
  display: flex;
  align-items: baseline;
}

.modal-body-Z05S04 {
  margin: -10px !important;
  padding-top: 0;
}

@media (max-width: 1024px) {
  .groupForm .facility-form {
    height: 300px;
  }

  .groupForm .lst-institution {
    height: 250px;
  }

  .h-415,
  .h-414 {
    height: 352px;
  }

  .groupFacility-box .lstIndividual {
    height: 283px;
  }

  .groupFacility-box .lstStaff {
    height: 394px;
  }

  .groupFacility-box .lstStaff-data {
    height: 375px;
  }

  .h-loadingperson {
    height: 333px;
  }

  .h-loadingMedical {
    height: 426px;
  }

  .groupFacility--change .groupFacility-info .noData,
  .groupFacility--change .groupFacility-info .selectList ul {
    height: 420px;
  }
}

.pagin-institution {
  padding: 10px 8px;
  height: 50px;
}

.disabled-control {
  pointer-events: none;
  opacity: 0.7;
}
</style>
