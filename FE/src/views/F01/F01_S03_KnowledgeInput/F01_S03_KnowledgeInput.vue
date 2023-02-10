<template>
  <div v-loading="isLoadingPage" class="wrapContent cssF01S03">
    <div class="groupKnow">
      <div class="groupKnow-step">
        <div v-if="status.knowledge_status_type === '50' || status.knowledge_status_type === '60'" class="dailyStep daiyStep--noPublic">
          <el-steps v-if="statusList.length" class="listSteps" :active="3" align-center process-status="error">
            <el-step v-for="(item, index) in statusList" :key="index" :title="item.definition_label" />
          </el-steps>
        </div>
        <div v-else-if="status.knowledge_status_type !== '20' && status.knowledge_status_type !== '30'" class="dailyStep dailyStep--blue">
          <el-steps v-if="statusList.length" class="listSteps" :active="getActiveStatus()" align-center finish-status="success">
            <el-step
              v-for="(item, index) in statusList"
              :key="index"
              :style="`cursor: ${item.definition_value === status.knowledge_status_type && data_approval.approval_request_detail.length ? 'pointer' : ''}`"
              @click="handleClickStatus(`step${item.definition_value}`)"
            >
              <template #title>
                <p
                  v-if="
                    data_approval.approval_request_detail.length === 0 ||
                    (item.definition_value !== '10' && item.definition_value !== '40') ||
                    item.definition_value !== status.knowledge_status_type ||
                    +item.definition_value > +status.knowledge_status_type
                  "
                >
                  {{ item.definition_label }}
                </p>
                <div v-else>
                  <el-dropdown trigger="click">
                    <p :ref="`step${item.definition_value}`" style="cursor: pointer" :class="'icon_warning_status'">
                      {{ item.definition_label }}
                      <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                    </p>
                    <template #dropdown>
                      <el-dropdown-menu class="el-dropdown-customDailyReport custom-dropdown-list">
                        <el-dropdown-item
                          v-for="(itemSub, idxx) in data_approval.approval_request_detail"
                          v-show="item.definition_value === status.knowledge_status_type"
                          :key="idxx"
                        >
                          <div v-if="data_approval.approval_request_detail.length > 1 && itemSub.layer_num" class="title_layer">{{ itemSub.layer_num }} 次</div>
                          <div class="content_layer">
                            <div v-for="(person, indexSub) in itemSub.data" :key="indexSub" class="info">
                              <p class="left">
                                <img
                                  v-if="+person.layer_num === +active_layer_num && person.status_type == '0' && !itemSub.data.some((x) => x.status_type != 0)"
                                  class="svg"
                                  src="@/assets/template/images/icon_majesticons_pending-circle.svg"
                                  alt=""
                                />
                                <img
                                  v-else-if="person.status_type === '1'"
                                  class="svg"
                                  src="@/assets/template/images/icon_majesticons_check-circle.svg"
                                  alt=""
                                />
                                <img
                                  v-else-if="person.status_type === '2'"
                                  class="svg"
                                  src="@/assets/template/images/icon_majesticons_close-circle.svg"
                                  alt=""
                                />
                                <span
                                  :class="
                                    (+person.layer_num !== +active_layer_num && person.status_type == '0') ||
                                    (itemSub.data.some((x) => x.status_type != 0) && person.status_type == '0')
                                      ? 'img-approve-invis'
                                      : ''
                                  "
                                >
                                  {{ person.user_name }}
                                </span>
                              </p>
                              <button v-if="person.comment" type="button" class="btn-comment" @click="openModalComment(person)">
                                <ImageSVG :src-image="'icon_comment_circle.svg'" :alt-image="'icon_comment_circle'" />
                              </button>
                            </div>
                          </div>
                        </el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </div>
              </template>
            </el-step>
          </el-steps>
        </div>
        <div v-else class="dailyStep dailyStep--blue">
          <el-steps v-if="statusList.length" class="listSteps" :active="getActiveStatus()" align-center finish-status="success">
            <el-step
              v-for="(item, index) in statusList"
              :key="index"
              :style="`cursor: ${item.definition_value === status.knowledge_status_type && data_approval.approval_request_detail.length ? 'pointer' : ''}`"
              @click="handleClickStatus(`step${item.definition_value}`)"
            >
              <template #title>
                <p
                  v-if="
                    data_approval.approval_request_detail.length === 0 ||
                    (item.definition_value !== '20' && item.definition_value !== '30') ||
                    +item.definition_value > +status.knowledge_status_type
                  "
                >
                  {{ item.definition_label }}
                </p>
                <div v-else>
                  <el-dropdown trigger="click">
                    <p :ref="`step${item.definition_value}`" style="cursor: pointer" :class="'icon_warning_status'">
                      {{ item.definition_label }}
                      <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                    </p>
                    <template #dropdown>
                      <el-dropdown-menu class="el-dropdown-customDailyReport custom-dropdown-list">
                        <el-dropdown-item
                          v-for="(itemSub, idxx) in data_approval.approval_request_detail"
                          v-show="(item.definition_value === '20' && itemSub.layer_num == 1) || (item.definition_value === '30' && itemSub.layer_num == 2)"
                          :key="idxx"
                        >
                          <div v-for="(person, indexSub) in itemSub.data" :key="indexSub" class="layer-content">
                            <p>
                              <img
                                v-if="+person.layer_num === +active_layer_num && person.status_type == '0' && !itemSub.data.some((x) => x.status_type != 0)"
                                class="svg"
                                src="@/assets/template/images/icon_majesticons_pending-circle.svg"
                                alt=""
                              />
                              <img v-else-if="person.status_type === '1'" class="svg" src="@/assets/template/images/icon_majesticons_check-circle.svg" alt="" />
                              <img v-else-if="person.status_type === '2'" class="svg" src="@/assets/template/images/icon_majesticons_close-circle.svg" alt="" />
                              <span
                                :class="
                                  (+person.layer_num !== +active_layer_num && person.status_type == '0') ||
                                  (itemSub.data.some((x) => x.status_type != 0) && person.status_type == '0')
                                    ? 'img-approve-invis'
                                    : ''
                                "
                              >
                                {{ person.user_name }}
                              </span>
                            </p>
                            <button v-if="person.comment" type="button" class="btn-comment" @click="openModalComment(person)">
                              <ImageSVG :src-image="'icon_comment_circle.svg'" :alt-image="'icon_comment_circle'" />
                            </button>
                          </div>
                        </el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </div>
              </template>
            </el-step>
          </el-steps>
        </div>
      </div>
      <div class="groupKnow-content">
        <div class="knowTabs">
          <ul class="nav navTabs">
            <li>
              <a :class="tab_pane_default === 1 ? 'active' : ''" data-toggle="tab" href="#tabs-1" role="tab">
                <div class="navInfo">
                  <span class="navItem"><ImageSVG :src-image="'icon_basicInformation.svg'" :alt-image="'icon_info'" /></span>
                  ナレッジ情報
                </div>
              </a>
            </li>
            <li>
              <a :class="tab_pane_default === 2 ? 'active' : ''" data-toggle="tab" href="#tabs-2" role="tab">
                <div class="navInfo">
                  <span class="navItem"><ImageSVG :src-image="'icon_timeline.svg'" :alt-image="'icon_timeline'" /></span>
                  タイムライン
                </div>
              </a>
            </li>
            <li>
              <a data-toggle="tab" href="#tabs-3" role="tab">
                <div class="navInfo">
                  <span class="navItem"><ImageSVG :src-image="'icon_comment-line.svg'" :alt-image="'icon_comment-line'" /></span>
                  コメント
                </div>
              </a>
            </li>
          </ul>
          <!-- Tab panes -->
        </div>
        <div class="knowMain">
          <div class="tab-content">
            <div id="tabs-1" class="tab-pane" :class="tab_pane_default === 1 ? 'active' : ''" role="tabpanel">
              <div class="knowForm-box">
                <div class="knowForm scrollbar">
                  <div v-if="mode_screen == 'create' || mode_screen == 'edit' || mode_screen === 'admin'" class="knowForm-request">
                    <div class="knowForm-tlt">匿名希望</div>
                    <div class="knowForm-switch">
                      <div class="switch">
                        <label class="switch-label">
                          <input
                            v-model="inputKnowlegdeObj.anonymous_flag"
                            :disabled="!checkEditField('匿名希望')"
                            checked="checked"
                            class="switch-checkbox"
                            value="on"
                            type="checkbox"
                          />
                          <span class="switch-round"></span>
                        </label>
                      </div>
                    </div>
                    <div class="knowForm-note">※作成者名、施設名、個人名は全て匿名になります。</div>
                  </div>
                  <div class="knowForm-row">
                    <div class="knowForm-col">
                      <div :class="`${mode_screen == 'create' || mode_screen == 'edit' || mode_screen === 'admin' ? 'knowForm-group' : 'knowDetail'}`">
                        <ul v-if="mode_screen == 'create' || mode_screen == 'edit' || mode_screen === 'admin'">
                          <li>
                            <label class="knowForm-label">
                              タイトル<span class="knowForm-required"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                            </label>
                            <div
                              class="knowForm-control"
                              :class="checkValidField('title') || (isSubmit && !validation.title.length) || responseErrors.title ? 'hasErr' : ''"
                            >
                              <el-input
                                v-model="inputKnowlegdeObj.title"
                                clearable
                                :readonly="!checkEditField('タイトル')"
                                placeholder="タイトルを入力"
                                class="form-control-input"
                              />
                              <p v-if="checkValidField('title')" class="text-error">{{ getMessage('MSFA0001', 'タイトルを入力') }}</p>
                              <p v-if="isSubmit && !validation.title.length" class="text-error">{{ getMessage('MSFA0012', 'タイトル', 100) }}</p>
                              <p v-else class="text-error">{{ responseErrors.title }}</p>
                            </div>
                          </li>
                          <li>
                            <label class="knowForm-label">
                              カテゴリー<span class="knowForm-required"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                            </label>
                            <div
                              class="knowForm-control"
                              :class="checkValidField('knowledge_category_cd') || responseErrors.knowledge_category_cd ? 'hasErr' : ''"
                            >
                              <el-select
                                v-model="inputKnowlegdeObj.knowledge_category_cd"
                                :disabled="!checkEditField('カテゴリー')"
                                placeholder="未選択"
                                class="form-control-select"
                                @change="handleChangeCategory"
                              >
                                <el-option
                                  v-for="item in selectList.category"
                                  :key="item.knowledge_category_cd"
                                  :label="item.knowledge_category_name"
                                  :value="item.knowledge_category_cd"
                                >
                                </el-option>
                              </el-select>
                              <p v-if="checkValidField('knowledge_category_cd')" class="text-error">
                                {{ getMessage('MSFA0040', 'カテゴリー') }}
                              </p>
                              <p v-else class="text-error">{{ responseErrors.knowledge_category_cd }}</p>
                            </div>
                          </li>
                          <li>
                            <label class="knowForm-label"> 共同作成者 </label>
                            <div class="knowForm-control">
                              <div class="form-icon icon-right">
                                <span
                                  class="icon point"
                                  @click="
                                    openModal(
                                      {
                                        screen: 'Z05_S01',
                                        title: 'ユーザ選択',
                                        width: currDevice() !== 'Desktop' ? '95%' : '65rem',
                                        props: {
                                          mode: 'multiple',
                                          userFlag: 1,
                                          userSelectFlag: 1,
                                          userCdList: inputKnowlegdeObj.users.map((el) => ({
                                            org_cd: el.org_cd,
                                            user_cd: el.user_cd
                                          })),
                                          orgCdList: inputKnowlegdeObj.users.map((el) => el.org_cd)
                                        }
                                      },
                                      '共同作成者'
                                    )
                                  "
                                  @touchstart="
                                    openModal(
                                      {
                                        screen: 'Z05_S01',
                                        title: 'ユーザ選択',
                                        width: currDevice() !== 'Desktop' ? '95%' : '65rem',
                                        props: {
                                          mode: 'multiple',
                                          userFlag: 1,
                                          userSelectFlag: 1,
                                          userCdList: inputKnowlegdeObj.users.map((el) => ({
                                            org_cd: el.org_cd,
                                            user_cd: el.user_cd
                                          })),
                                          orgCdList: inputKnowlegdeObj.users.map((el) => el.org_cd)
                                        }
                                      },
                                      '共同作成者'
                                    )
                                  "
                                >
                                  <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                                </span>
                                <div v-if="inputKnowlegdeObj.users.length > 0" class="form-control d-flex align-items-center">
                                  <div class="block-tags">
                                    <el-tag
                                      v-for="(item, index) in inputKnowlegdeObj.users"
                                      :key="index"
                                      class="m-0 el-tag-custom"
                                      :closable="checkEditField('users')"
                                      @close="handleRemoveTag('users', item)"
                                    >
                                      {{ item.user_name }}
                                    </el-tag>
                                  </div>
                                </div>
                                <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                              </div>
                            </div>
                          </li>
                          <li>
                            <div class="knowForm-col2">
                              <ul>
                                <li>
                                  <label class="knowForm-label">施設個人</label>
                                  <div class="knowForm-control">
                                    <div class="knowForm-control">
                                      <div class="form-icon icon-right">
                                        <span
                                          class="icon log-icon"
                                          title_log="施設個人"
                                          @click="
                                            openModal(
                                              {
                                                screen: 'Z05_S04',
                                                title: '施設個人選択',
                                                width: currDevice() !== 'Desktop' ? '90%' : '55rem',
                                                props: { mode: 'single', propsPrams: props_Z05_S04 }
                                              },
                                              '施設個人'
                                            )
                                          "
                                          @touchstart="
                                            openModal(
                                              {
                                                screen: 'Z05_S04',
                                                title: '施設個人選択',
                                                width: currDevice() !== 'Desktop' ? '90%' : '55rem',
                                                props: { mode: 'single', propsPrams: props_Z05_S04 }
                                              },
                                              '施設個人'
                                            )
                                          "
                                        >
                                          <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                                        </span>
                                        <div v-if="inputKnowlegdeObj.person_name" class="form-control d-flex align-items-center">
                                          <div class="block-tags">
                                            <el-tag class="m-0 el-tag-custom" :closable="checkEditField('person')" @close="handleRemoveTag('person')">
                                              {{ inputKnowlegdeObj.person_name }}
                                            </el-tag>
                                          </div>
                                        </div>
                                        <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                                      </div>
                                    </div>
                                  </div>
                                </li>
                                <li>
                                  <label class="knowForm-label">
                                    施設<span class="knowForm-required"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                                  </label>
                                  <div class="knowForm-control">
                                    <div
                                      class="form-icon icon-right"
                                      :class="checkValidField('facility_short_name') || responseErrors.facility_short_name ? 'hasErr' : ''"
                                    >
                                      <span
                                        class="icon log-icon"
                                        title_log="施設"
                                        @click="
                                          openModal(
                                            {
                                              screen: 'Z05_S03',
                                              title: '施設選択',
                                              width: currDevice() !== 'Desktop' ? '95%' : '55rem',
                                              props: {
                                                mode: 'single',
                                                change_flag: checkFacility_cd ? true : false,
                                                facilityCd: checkFacility_cd
                                                  ? inputKnowlegdeObj.facility_cd
                                                    ? [inputKnowlegdeObj.facility_cd]
                                                    : [checkFacility_cd]
                                                  : inputKnowlegdeObj.facility_cd
                                                  ? [inputKnowlegdeObj.facility_cd]
                                                  : []
                                              }
                                            },
                                            '施設'
                                          )
                                        "
                                      >
                                        <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                                      </span>
                                      <div v-if="inputKnowlegdeObj.facility_short_name" class="form-control d-flex align-items-center">
                                        <div class="block-tags">
                                          <el-tag class="m-0 el-tag-custom" :closable="checkEditField('facility')" @close="handleRemoveTag('facility')">
                                            {{ inputKnowlegdeObj.facility_short_name }}
                                          </el-tag>
                                        </div>
                                      </div>
                                      <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                                    </div>
                                    <p v-if="checkValidField('facility_short_name')" class="text-error">
                                      {{ getMessage('MSFA0040', '施設') }}
                                    </p>
                                    <p v-else class="text-error">{{ responseErrors.facility_short_name }}</p>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </li>
                          <li>
                            <div class="knowForm-col2">
                              <ul>
                                <li>
                                  <label class="knowForm-label">
                                    診療科目分類
                                    <span class="knowForm-required"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                                  </label>
                                  <div
                                    class="knowForm-control"
                                    :class="checkValidField('medical_subjects_group_cd') || responseErrors.medical_subjects_group_cd ? 'hasErr' : ''"
                                  >
                                    <el-select
                                      v-model="inputKnowlegdeObj.medical_subjects_group_cd"
                                      :disabled="!checkEditField('診療科目分類')"
                                      placeholder="未選択"
                                      class="form-control-select"
                                      @change="handleChangeMedical"
                                    >
                                      <el-option
                                        v-for="item in selectList.medical_subjects"
                                        :key="item.medical_subjects_group_cd"
                                        :label="item.medical_subjects_group_name"
                                        :value="item.medical_subjects_group_cd"
                                      >
                                      </el-option>
                                    </el-select>
                                    <p v-if="checkValidField('medical_subjects_group_cd')" class="text-error">
                                      {{ getMessage('MSFA0040', '診療科目分類') }}
                                    </p>
                                    <p v-else class="text-error">{{ responseErrors.medical_subjects_group_cd }}</p>
                                  </div>
                                </li>
                                <li>
                                  <label class="knowForm-label">
                                    品目<span class="knowForm-required"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                                  </label>
                                  <div class="knowForm-control">
                                    <div
                                      key=""
                                      class="form-icon icon-right"
                                      :class="checkValidField('product_name') || responseErrors.product_name ? 'hasErr' : ''"
                                    >
                                      <span
                                        class="icon log-icon"
                                        title_log="品目"
                                        @click="
                                          openModal(
                                            {
                                              screen: 'Z05_S06',
                                              title: '品目選択',
                                              width: '33rem',
                                              customClass: 'custom-dialog modal-fixed modal-fixed-min',
                                              props: { mode: 'single', initDataCodes: inputKnowlegdeObj.product_cd ? [inputKnowlegdeObj.product_cd] : [] }
                                            },
                                            '品目'
                                          )
                                        "
                                        @touchstart="
                                          openModal(
                                            {
                                              screen: 'Z05_S06',
                                              title: '品目選択',
                                              width: '33rem',
                                              customClass: 'custom-dialog modal-fixed modal-fixed-min',
                                              props: { mode: 'single', initDataCodes: inputKnowlegdeObj.product_cd ? [inputKnowlegdeObj.product_cd] : [] }
                                            },
                                            '品目'
                                          )
                                        "
                                      >
                                        <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                                      </span>
                                      <div v-if="inputKnowlegdeObj.product_name" class="form-control d-flex align-items-center">
                                        <div class="block-tags">
                                          <el-tag class="m-0 el-tag-custom" :closable="checkEditField('product')" @close="handleRemoveTag('product')">
                                            {{ inputKnowlegdeObj.product_name }}
                                          </el-tag>
                                        </div>
                                      </div>
                                      <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                                    </div>
                                    <p v-if="checkValidField('product_name')" class="text-error">
                                      {{ getMessage('MSFA0040', '品目') }}
                                    </p>
                                    <p v-else class="text-error">{{ responseErrors.product_name }}</p>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </li>
                        </ul>
                        <ul v-else>
                          <li class="w-100 flex-custom">
                            <p class="knowDetail-tlt">タイトル</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ inputKnowlegdeObj.title }}</p>
                            </div>
                          </li>
                          <li class="w-100 flex-custom">
                            <p class="knowDetail-tlt">カテゴリー</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ inputKnowlegdeObj.knowledge_category_name }}</p>
                            </div>
                          </li>
                          <li class="w-100 flex-custom">
                            <p class="knowDetail-tlt">共同作成者</p>
                            <div class="knowDetail-text">
                              <p v-for="(item, index) in inputKnowlegdeObj.users" :key="index" class="knowDetail-label">
                                {{ item.org_label }}　 {{ item.user_name }}
                              </p>
                            </div>
                          </li>
                          <li class="w-100 flex-custom">
                            <p class="knowDetail-tlt">施設個人</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ inputKnowlegdeObj.facility_short_name }}　{{ inputKnowlegdeObj.person_name }}</p>
                            </div>
                          </li>
                          <li class="w-100 flex-custom">
                            <p class="knowDetail-tlt">診療科目分類</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ inputKnowlegdeObj.medical_subjects_group_name }}</p>
                            </div>
                          </li>
                          <li class="w-100 flex-custom">
                            <p class="knowDetail-tlt">品目</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ inputKnowlegdeObj.product_name }}</p>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="knowForm-col">
                      <div :class="`${mode_screen == 'create' || mode_screen == 'edit' || mode_screen === 'admin' ? 'knowForm-group' : 'knowDetail'}`">
                        <ul>
                          <li v-if="mode_screen == 'create' || mode_screen == 'edit' || mode_screen === 'admin'">
                            <label class="knowForm-label">
                              内容
                              <span class="knowForm-required"><ImageSVG :src-image="'icon_asterisk.svg'" :alt-image="'icon_asterisk'" /></span>
                            </label>
                            <div class="knowForm-control" :class="checkValidField('contents') || responseErrors.contents ? 'hasErr' : ''">
                              <el-input
                                id="area_rows"
                                v-model="inputKnowlegdeObj.contents"
                                :disabled="!checkEditField('内容')"
                                class="form-control-textarea"
                                :rows="rowContent"
                                type="textarea"
                                placeholder="内容を入力"
                              />
                              <p v-if="checkValidField('contents')" class="text-error">
                                {{ getMessage('MSFA0001', '内容') }}
                              </p>
                              <p v-else class="text-error">{{ responseErrors.contents }}</p>
                            </div>
                          </li>
                          <li v-else class="w-100 flex-custom">
                            <p class="knowDetail-tlt knowDetail-tlt-sm">内容</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ inputKnowlegdeObj.contents }}</p>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="tabs-2" class="tab-pane" :class="tab_pane_default === 2 ? 'active' : ''" role="tabpanel">
              <div class="knowTimeline">
                <div class="timeline-head">
                  <ul class="timeline-lst">
                    <li v-for="(el, idx) in note" :key="idx">
                      <span class="item">
                        <ImageSVG :src-image="returnIcon(el.definition_value)" :alt-image="'icon'" />
                      </span>
                      {{ el.definition_label }}
                    </li>
                  </ul>
                  <div v-if="checkEditTimeline()" class="timeline-btn">
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-radius"
                      @click="
                        openModal({
                          screen: 'F01_S04',
                          title: 'タイムライン選択',
                          width: getDevice() !== 'Desktop' ? '90%' : '68rem',
                          isClose: true,
                          props: {
                            facilityCd: inputKnowlegdeObj.facility_cd,
                            personCd: [{ person_cd: inputKnowlegdeObj.person_cd, person_name: inputKnowlegdeObj.person_name || '' }]
                          }
                        })
                      "
                    >
                      <span class="btn-iconLeft">
                        <ImageSVG :src-image="'icon_timeline.svg'" :alt-image="'icon_timeline'" />
                      </span>
                      タイムライン選択
                    </button>
                  </div>
                </div>
                <div class="timeline-content scrollbar">
                  <div v-if="time_line.length !== 0" class="dailyList">
                    <ul>
                      <li v-for="(el, idx) in time_line" v-show="showTimeline(idx)" :key="idx">
                        <div v-if="el.data.length > 0" class="dailyList-calendar">
                          <span class="label">{{ formatFullDate(el.date) + '（' + getDay(el.date) + '）' }}</span>
                        </div>
                        <ul class="dailyList-sub">
                          <li v-for="(item, index) in el.data" v-show="item.delete_flag !== 1" :key="index">
                            <div class="dailyList-flex">
                              <div class="dailyList-dateTime">
                                <span class="time">
                                  {{
                                    formatTimeHourMinute(item.start_datetime) === '0:00' && formatTimeHourMinute(item.end_datetime) === '23:59'
                                      ? '終日'
                                      : formatTimeHourMinute(item.start_datetime) + '～' + formatTimeHourMinute(item.end_datetime)
                                  }}
                                </span>
                                <div class="iconItem">
                                  <span class="item">
                                    <ImageSVG :src-image="returnIcon(item.channel_type)" :alt-image="'icon'" />
                                  </span>
                                </div>
                              </div>
                              <!-- channel_type === '10' (面談) -->
                              <div v-if="item.channel_type === '10'" class="row__custom">
                                <div class="dailyList-content col__custom">
                                  <div class="dailyList-box">
                                    <div class="dailyList-btnCmt">
                                      <button v-if="checkEditTimeline()" class="btn btn--icon" @click="deleteTimelineBtn(idx, index, item)">
                                        <ImageSVG :src-image="'icon_delete.svg'" :alt-image="'icon_delete'" />
                                      </button>
                                    </div>
                                    <div v-if="item.off_label_flag" class="dailyList-label">
                                      <span class="span-label span-label--off">
                                        <span class="span-label-item">
                                          <ImageSVG :src-image="'icon_exclamation-circle02.svg'" :alt-image="'icon_exclamation-circle02'" />
                                        </span>
                                        オフラベルあり
                                      </span>
                                    </div>
                                    <div class="dailyList-group">
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt interview">
                                          <p class="dailyList-info-label">面談先</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p
                                            :class="`dailyList-bold ${checkPersonMedicalStaff(item) ? '' : 'dailyList-spanBlue  point log-icon'}`"
                                            @click="checkPersonMedicalStaff(item) ? '' : callScreen('H02S02', { person_cd: item.person_cd })"
                                          >
                                            <span>{{ item.facility_short_name }} 　</span>
                                            <span v-if="item.department_name"> {{ item.department_name }} 　 </span>
                                            <span v-if="item.person_name"> {{ item.person_name }} </span>
                                          </p>
                                        </div>
                                      </div>
                                      <div class="daily-accordion">
                                        <el-collapse accordion>
                                          <el-collapse-item
                                            v-for="(elm, idxx) in item.channel_detail"
                                            :key="elm.channel_detail_id + idxx"
                                            :name="elm.channel_detail_id + idxx"
                                          >
                                            <template #title>
                                              <h2 class="article-head detail">
                                                <span>{{ elm.detail_order + '. ' + elm.content_name + '　' + elm.product_name }}</span>
                                              </h2>
                                            </template>
                                            <div class="article-body">
                                              <div class="daily-article">
                                                <ul>
                                                  <li>
                                                    <span class="daily-article-tlt">フェーズ</span>
                                                    <span class="daily-article-label">{{ elm.phase_name }}</span>
                                                  </li>
                                                  <li>
                                                    <span class="daily-article-tlt">反応</span>
                                                    <span class="daily-article-label">{{ elm.reaction_name }}</span>
                                                  </li>
                                                </ul>
                                                <p class="daily-article-text">{{ elm.note }}</p>
                                              </div>
                                            </div>
                                          </el-collapse-item>
                                        </el-collapse>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt interview">
                                          <p class="dailyList-info-label">面談者</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p
                                            class="dailyList-spanBlue dailyList-bold point log-icon"
                                            title_log="面談者"
                                            @click="callScreen('Z04S01', { user_cd: item.user_cd })"
                                          >
                                            {{ item.user_name }}
                                          </p>
                                          <p class="dailyList-add">
                                            <span class="dailyList-item"><ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" /></span>
                                            {{ item.org_label || '(所属なし)' }}
                                          </p>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="viewMore">
                                      <span class="link log-icon" @click="callScreenA01_S04(item)"> 詳細を見る </span>
                                    </div>
                                  </div>
                                </div>
                                <div class="dailyList-textarea col__custom">
                                  <div class="dailyList-form">
                                    <el-input
                                      v-if="checkEditTimeline()"
                                      v-model="item.comment"
                                      class="form-control-textarea form-hg40"
                                      type="textarea"
                                      resize="vertical"
                                      placeholder="内容入力"
                                    />
                                    <p v-else :class="{ 'form-view': !item.comment }">{{ item.comment }}</p>
                                  </div>
                                </div>
                              </div>
                              <!-- channel_type === '30' (講演会) -->
                              <div v-if="item.channel_type === '30'" class="row__custom">
                                <div class="dailyList-content col__custom">
                                  <div class="dailyList-box">
                                    <div class="dailyList-btnCmt">
                                      <button v-if="checkEditTimeline()" class="btn btn--icon" @click="deleteTimelineBtn(idx, index, item)">
                                        <ImageSVG :src-image="'icon_delete.svg'" :alt-image="'icon_delete'" />
                                      </button>
                                    </div>
                                    <div class="dailyList-group">
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt convention">
                                          <p class="dailyList-info-label dailyList-gray">講演会名</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanBlue dailyList-bold point" @click="callScreen('C01S02', { convention_id: item.channel_id })">
                                            {{ item.title }}
                                          </p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt convention">
                                          <p class="dailyList-info-label dailyList-gray">講演会区分</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.convention_type_name }}</p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt convention">
                                          <p class="dailyList-info-label dailyList-gray">品目</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.product_name }}</p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt convention">
                                          <p class="dailyList-info-label dailyList-gray">対象組織</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.org_label || '(所属なし)' }}</p>
                                        </div>
                                      </div>
                                      <div class="daily-accordion">
                                        <el-collapse accordion>
                                          <el-collapse-item :key="item" :name="item">
                                            <template #title>
                                              <h2 class="article-head">
                                                <span>特記事項</span>
                                              </h2>
                                            </template>
                                            <div :class="`article-body ${item?.note?.length > 0 ? '' : 'article-body-nodata'}`">
                                              <div class="daily-article">
                                                <p class="daily-article-text">
                                                  {{ item.note }}
                                                </p>
                                              </div>
                                            </div>
                                          </el-collapse-item>
                                        </el-collapse>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="dailyList-textarea col__custom">
                                  <div class="dailyList-form">
                                    <el-input
                                      v-if="checkEditTimeline()"
                                      v-model="item.comment"
                                      class="form-control-textarea form-hg40"
                                      type="textarea"
                                      placeholder="内容入力"
                                    />
                                    <p v-else :class="{ 'form-view': !item.comment }">{{ item.comment }}</p>
                                  </div>
                                </div>
                              </div>
                              <!-- channel_type === '20' (説明会) -->
                              <div v-if="item.channel_type === '20'" class="row__custom">
                                <div class="dailyList-content col__custom">
                                  <div class="dailyList-box">
                                    <div class="dailyList-btnCmt">
                                      <button v-if="checkEditTimeline()" class="btn btn--icon" @click="deleteTimelineBtn(idx, index, item)">
                                        <ImageSVG :src-image="'icon_delete.svg'" :alt-image="'icon_delete'" />
                                      </button>
                                    </div>
                                    <div class="dailyList-group">
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt briefing">
                                          <p class="dailyList-info-label dailyList-gray">説明会名</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanBlue dailyList-bold point" @click="callScreen('B01S02', { briefing_id: item.channel_id })">
                                            {{ item.title }}
                                          </p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt briefing">
                                          <p class="dailyList-info-label dailyList-gray">説明会区分</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.briefing_type_name }}</p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt briefing">
                                          <p class="dailyList-info-label dailyList-gray">品目</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.product_name }}</p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt briefing">
                                          <p class="dailyList-info-label dailyList-gray">対象施設</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.briefing_facility_short_name }}</p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt briefing">
                                          <p class="dailyList-info-label dailyList-gray">実施者</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanBlue dailyList-bold point" @click="callScreen('Z04S01', { user_cd: item.user_cd })">
                                            {{ item.user_name }}
                                          </p>
                                          <p class="dailyList-add">
                                            <span class="dailyList-item"><ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" /></span>
                                            {{ item.org_label || '(所属なし)' }}
                                          </p>
                                        </div>
                                      </div>
                                      <div id="accordion-3" class="daily-accordion">
                                        <el-collapse accordion>
                                          <el-collapse-item :key="item" :name="item">
                                            <template #title>
                                              <h2 class="article-head">
                                                <span>特記事項</span>
                                              </h2>
                                            </template>
                                            <div :class="`article-body ${item?.note?.length > 0 ? '' : 'article-body-nodata'}`">
                                              <div class="daily-article">
                                                <p class="daily-article-text">{{ item.note }}</p>
                                              </div>
                                            </div>
                                          </el-collapse-item>
                                        </el-collapse>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="dailyList-textarea col__custom">
                                  <div class="dailyList-form">
                                    <el-input
                                      v-if="checkEditTimeline()"
                                      v-model="item.comment"
                                      class="form-control-textarea form-hg40"
                                      type="textarea"
                                      placeholder="内容入力"
                                    />
                                    <p v-else :class="{ 'form-view': !item.comment }">{{ item.comment }}</p>
                                  </div>
                                </div>
                              </div>
                              <!-- channel_type === '40' (外部コンテンツ) -->
                              <div v-if="item.channel_type === '40'" class="row__custom">
                                <div class="dailyList-content col__custom">
                                  <div class="dailyList-box">
                                    <div class="dailyList-btnCmt">
                                      <button v-if="checkEditTimeline()" class="btn btn--icon" @click="deleteTimelineBtn(idx, index, item)">
                                        <ImageSVG :src-image="'icon_delete.svg'" :alt-image="'icon_delete'" />
                                      </button>
                                    </div>
                                    <div class="dailyList-group">
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt external">
                                          <p class="dailyList-info-label dailyList-gray">視聴者</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p
                                            :class="`dailyList-bold ${checkPersonMedicalStaff(item) ? '' : 'dailyList-spanBlue  point log-icon'}`"
                                            @click="checkPersonMedicalStaff(item) ? '' : callScreen('H02S02', { person_cd: item.person_cd })"
                                          >
                                            <span>{{ item.facility_short_name }} 　</span>
                                            <span v-if="item.department_name"> {{ item.department_name }} 　 </span>
                                            <span v-if="item.person_name"> {{ item.person_name }} </span>
                                          </p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt external">
                                          <p class="dailyList-info-label dailyList-gray">品目</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.product_name }}</p>
                                        </div>
                                      </div>
                                      <div class="dailyList-info">
                                        <div class="dailyList-info-tlt external">
                                          <p class="dailyList-info-label dailyList-gray">コンテンツ</p>
                                        </div>
                                        <div class="dailyList-info-text">
                                          <p class="dailyList-spanGray">{{ item.content }}</p>
                                        </div>
                                      </div>
                                      <div id="accordion-4" class="daily-accordion">
                                        <el-collapse accordion>
                                          <el-collapse-item :key="item" :name="item">
                                            <template #title>
                                              <h2 class="article-head">
                                                <span>特記事項</span>
                                              </h2>
                                            </template>
                                            <div :class="`article-body ${item?.note?.length > 0 ? '' : 'article-body-nodata'}`">
                                              <div class="daily-article">
                                                <p class="daily-article-text">{{ item.note }}</p>
                                              </div>
                                            </div>
                                          </el-collapse-item>
                                        </el-collapse>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="dailyList-textarea col__custom">
                                  <div class="dailyList-form">
                                    <el-input
                                      v-if="checkEditTimeline()"
                                      v-model="item.comment"
                                      class="form-control-textarea form-hg40"
                                      type="textarea"
                                      placeholder="内容入力"
                                    />
                                    <p v-else :class="{ 'form-view': !item.comment }">{{ item.comment }}</p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </li>
                    </ul>
                  </div>
                  <div v-else class="nodata">
                    <span>該当するデータがありません。</span>
                    <ImageSVG :src-image="'amico_small.svg'" :alt-image="'amico'" />
                  </div>
                </div>
              </div>
            </div>
            <div id="tabs-3" class="tab-pane" role="tabpanel">
              <div class="knowForm-box">
                <div class="knowForm scrollbar">
                  <div class="knowForm-row">
                    <div class="knowForm-col">
                      <div :class="`${!reference_mode ? 'knowForm-group' : 'knowDetail'}`">
                        <ul v-if="!reference_mode">
                          <li>
                            <label :class="`knowForm-label  ${checkEditField('提出者コメント') ? '' : 'knowDetail-tlt'}`">提出者コメント</label>
                            <div class="knowForm-control">
                              <el-input
                                v-if="checkEditField('提出者コメント')"
                                v-model="inputKnowlegdeObj.submit_comment"
                                class="form-control-textarea"
                                :rows="5"
                                type="textarea"
                                placeholder="提出者コメントを入力"
                              />
                              <el-input
                                v-else
                                v-model="inputKnowlegdeObj.submit_comment"
                                disabled
                                class="form-control-textarea"
                                :rows="5"
                                type="textarea"
                                placeholder=""
                              />
                            </div>
                          </li>
                          <li>
                            <label
                              :class="`knowForm-label  ${
                                +active_layer_num === +selectList.approval_layer_num &&
                                userLogin === obj_admin_comment.approval_user_cd &&
                                checkEditField('管理者コメント')
                                  ? ''
                                  : 'knowDetail-tlt'
                              }`"
                              >管理者コメント</label
                            >
                            <div
                              class="knowForm-control"
                              :class="(isSubmit && !validation.commentAdmin.length) || responseErrors.commentAdmin || hasErrReject ? 'hasErr' : ''"
                            >
                              <el-input
                                v-if="
                                  +active_layer_num === +selectList.approval_layer_num &&
                                  userLogin === obj_admin_comment.approval_user_cd &&
                                  checkEditField('管理者コメント')
                                "
                                v-model="obj_admin_comment.comment"
                                class="form-control-textarea"
                                :rows="5"
                                type="textarea"
                                placeholder="管理者コメントを入力"
                                @input="changeCommentAdmin"
                              />
                              <el-input
                                v-else
                                v-model="obj_admin_comment.comment"
                                disabled
                                class="form-control-textarea"
                                :rows="5"
                                type="textarea"
                                placeholder=""
                              />
                              <p v-if="isSubmit && !validation.commentAdmin.length" class="text-error">{{ getMessage('MSFA0012', '管理者コメント', 200) }}</p>
                              <p v-if="hasErrReject" class="text-error">{{ getMessage('MSFA0001', '管理者コメント') }}</p>
                              <p v-else class="text-error">{{ responseErrors.commentAdmin }}</p>
                            </div>
                          </li>
                        </ul>
                        <ul v-else>
                          <li class="w-100">
                            <p class="knowDetail-tlt">提出者コメント</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ inputKnowlegdeObj?.submit_comment }}</p>
                            </div>
                          </li>
                          <li class="w-100">
                            <p class="knowDetail-tlt">管理者コメント</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ obj_admin_comment?.comment }}</p>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="knowForm-col">
                      <div :class="`${!reference_mode ? 'knowForm-group' : 'knowDetail'}`">
                        <ul v-if="!reference_mode">
                          <li>
                            <label :class="`knowForm-label ${!checkEditField('承認者コメント') ? 'knowDetail-tlt' : ''}`">承認者コメント</label>
                            <div
                              class="knowForm-control"
                              :class="(isSubmit && !validation.commentApprove.length) || responseErrors.commentApprove ? 'hasErr' : ''"
                            >
                              <el-input
                                v-model="obj_approve_comment.comment"
                                :disabled="!checkEditField('承認者コメント')"
                                class="form-control-textarea"
                                :rows="5"
                                type="textarea"
                                :placeholder="!checkEditField('承認者コメント') ? '' : '承認者コメントを入力'"
                              />
                              <p v-if="isSubmit && !validation.commentApprove.length" class="text-error">{{ getMessage('MSFA0012', '承認者コメント', 200) }}</p>
                              <p v-else class="text-error">{{ responseErrors.commentApprove }}</p>
                            </div>
                          </li>
                          <li>
                            <div class="knowForm-col2">
                              <ul>
                                <li style="width: 60%">
                                  <label
                                    :class="`knowForm-label  ${
                                      mode_screen === 'admin' ? active_point_cd_flg : !checkEditField('活用度ポイント付与') ? 'knowDetail-tlt' : ''
                                    }`"
                                    >活用度ポイント付与</label
                                  >
                                  <div class="knowForm-control">
                                    <el-select
                                      v-model="inputKnowlegdeObj.active_point_cd"
                                      :disabled="mode_screen === 'admin' ? active_point_cd_flg : !checkEditField('活用度ポイント付与')"
                                      placeholder="未選択"
                                      class="form-control-select"
                                    >
                                      <el-option
                                        v-for="item in selectList.active_point"
                                        :key="item.definition_value"
                                        :label="item.definition_label"
                                        :value="item.definition_value"
                                      />
                                    </el-select>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </li>
                        </ul>
                        <ul v-else>
                          <li class="w-100">
                            <p class="knowDetail-tlt">承認者コメント</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">{{ obj_approve_comment?.comment }}</p>
                            </div>
                          </li>
                          <li class="w-100">
                            <p class="knowDetail-tlt">活用度ポイント付与</p>
                            <div class="knowDetail-text">
                              <p class="knowDetail-label">
                                {{ selectList.active_point.find((el) => el.definition_value === inputKnowlegdeObj?.active_point_cd)?.definition_label }}
                              </p>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="groupKnow-btn">
          <button v-for="(item, idex) in showBtn()" :key="idex" :class="`btn ${item.class}`" @click="item.event">
            {{ item.label }}
          </button>
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
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="closeModal"
          @handleYes="handleYesConfirm"
          @handleConfirm="handleConfirmDelete"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
import { required, validLength } from '@/utils/validate';
import F01_S03_Service from '@/api/F01/F01_S03_KnowledgeInputServices';
import F01_S04_TimelineSelection from '@/views/F01/F01_S04_TimelineSelection/F01_S04_TimelineSelection';
import Z05_S01_ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05_S03_FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';
import Z05_S04_FacilityCustomerSelection from '@/views/Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import B01_S02_ModalComment from '@/views/B01/B01_S02_BriefingSessionInput/B01_S02_ModalComment.vue';
import { markRaw } from 'vue';
import _ from 'lodash';
import { Device } from '@/utils/common-function.js';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';
import Z05_S04_facilityCustomerService from '@/api/Z05/Z05_S04_facilityCustomerService';
import moment from 'moment';

export default {
  name: 'F01_S03_KnowledgeInput',
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      isLoadingPage: false,

      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        isShowClose: true,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      props_Z05_S04: {
        non_facility_flag: 0, // reqq
        non_medical_flag: 1, //req
        user_cd: '',
        user_name: '',
        target_product_cd: '',
        definition_value: '',
        facility_category_type: '',
        prefecture_cd: '',
        prefecture_name: '',
        municipality_cd: '',
        municipality_name: '',
        facility_cd: [],
        facility_name: []
      },
      selectList: {
        status: [],
        category: [],
        medical_subjects: [],
        active_point: [],
        status_approval: [],
        approval_layer_num: '',
        approval_work_type: ''
      },
      reference_mode: false,
      // _________________NOTE MODE SCREEN__________________
      // 1. 新規作成モード create mode (create)
      // 2. 編集モード edit mode (edit)
      // 3. 承認モード approval mode (approval)
      // 4. 管理者編集モード admin edit mode (admin)
      // 5. 参照モード reference mode (reference_mode)
      mode_screen: '',
      // _________________NOTE ROLE__________________
      // 1. 制限なし no_limit
      // 2. ナレッジ元作成者 create_user_cd
      // 3. 最終承認者以外の承認者 other_last_approver_layer
      // 4. 最終承認者 last_approver_layer
      // 5. ナレッジ管理者 is_knowledge_admin
      role: null, // number 1-> 5
      status: {
        definition_label: '', // 10 作成中, 20 承認確認中, 30 公開待ち, (40 公開中 || 50 不採用 || 60 非公開)
        knowledge_status_type: '' // '10' -> ('40' || '50' || '60')
      },
      statusList: [],
      inputKnowlegdeObj: {
        anonymous_flag: false,
        title: '',
        knowledge_category_name: '',
        knowledge_category_cd: '',
        product_name: '',
        product_cd: '',
        facility_short_name: '',
        facility_cd: '',
        person_cd: '',
        facility_name: '',
        person_name: '',
        medical_subjects_group_cd: '',
        medical_subjects_group_name: '',
        users: [],
        contents: '',
        submit_comment: '',
        comment: [],
        active_point_cd: '',
        first_release_datetime: '',
        release_datetime: '',
        release_flag: '',
        definition_value: '',
        Last_Modified: ''
      },

      knowledge_id: null,
      create_user_cd: '',
      other_last_approver_layer: [],
      last_approver_layer: [],
      is_knowledge_admin: false,
      note: [],
      time_line: [],
      data_approval: {},
      active_point_cd_flg: false,
      checkFacility_cd: '',
      checkFacility_name: '',
      error_flg: false,
      obj_admin_comment: {},
      obj_approve_comment: {},
      layer_num: null,
      userLogin: '',

      inputKnowlegdeObjOld: {},
      time_lineOld: [],
      obj_admin_commentOld: {},
      obj_approve_commentOld: {},

      active_layer_num: 1,

      dataFlatTimeline: [],
      tab_pane_default: 1,
      typeButton: '',

      filterDataZ05S03: {
        selectGroupCd: '',
        facility_cd: [],
        facility_name: [],
        org_cd: '',
        user_cd: '',
        user_name: ''
      },

      lstMedicalStaff: [],
      fromF01S02: false,
      rowContent: 15,
      hasErrReject: false
    };
  },
  computed: {
    validation() {
      return {
        title: {
          required: required(this.inputKnowlegdeObj.title),
          length: validLength(this.inputKnowlegdeObj.title, 100)
        },
        knowledge_category_cd: { required: required(this.inputKnowlegdeObj.knowledge_category_cd) },
        facility_short_name: { required: required(this.inputKnowlegdeObj.facility_short_name) },
        medical_subjects_group_cd: { required: required(this.inputKnowlegdeObj.medical_subjects_group_cd) },
        product_name: { required: required(this.inputKnowlegdeObj.product_name) },
        contents: { required: required(this.inputKnowlegdeObj.contents) },
        commentApprove: { length: validLength(this.inputKnowlegdeObj.title, 200) },
        commentAdmin: { length: validLength(this.inputKnowlegdeObj.title, 200) }
      };
    }
  },
  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        if (toParams && toParams.knowledge_id && previousParams.knowledge_id && toParams.knowledge_id !== previousParams.knowledge_id) {
          window.location.reload();
        }
      }
    );
  },

  updated() {
    this.$nextTick(() => {
      let isEqual =
        !_.isEqual(this.inputKnowlegdeObj, this.inputKnowlegdeObjOld) ||
        !_.isEqual(this.time_line, this.time_lineOld) ||
        !_.isEqual(this.obj_admin_comment, this.obj_admin_commentOld) ||
        !_.isEqual(this.obj_approve_comment, this.obj_approve_commentOld);

      if (isEqual) {
        localStorage.setItem('checkChangTab', true);
      } else {
        localStorage.removeItem('checkChangTab');
      }
    });
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: 'ナレッジ入力',
      icon: 'icon_speech-meeting-color.svg',
      isShowBack: true
    });
    this.currentUser = this.getCurrentUser();
    const active = document.querySelector('.el-tabs__active-bar');
    setTimeout(() => {
      active?.classList.add('tab-active');
    });
    let time_line_knowledge = localStorage.getItem('time_line_knowledge');
    if (time_line_knowledge === 'true') {
      this.tab_pane_default = 2;
    }
    this.getMedicalStaff();
    this.knowledge_id = this._route('F01_S03_KnowledgeInput')?.params?.knowledge_id || '';
    this.fromF01S02 = this._route('F01_S03_KnowledgeInput')?.params?.fromF01S02 || false;
    this.userLogin = this.$store.state.auth.currentUser.user_cd;
    this.emitter.on('change-F01S03', this.trigerEmit);

    this.filterDataZ05S03 = {
      ...this.filterDataZ05S03,
      org_cd: this.currentUser.org_cd,
      user_cd: this.currentUser.user_cd,
      user_name: this.currentUser.user_name
    };

    this.props_Z05_S04 = {
      ...this.props_Z05_S04,
      user_cd: this.currentUser.user_cd,
      user_name: this.currentUser.user_name
    };

    this.checkRow();

    window.addEventListener('resize', function () {
      if (window.screen.availWidth < 1080 || window.innerWidth < 1080) {
        this.rowContent = 15;
      } else {
        this.rowContent = 18;
      }
      let area_rows = document.getElementById('area_rows');
      if (area_rows) {
        area_rows.setAttribute('rows', this.rowContent);
      }
    });
  },
  methods: {
    getDevice() {
      return Device();
    },

    checkRow() {
      if (window.screen.availWidth < 1080 || window.innerWidth < 1080) {
        this.rowContent = 15;
      } else {
        this.rowContent = 18;
      }
      let area_rows = document.getElementById('area_rows');
      if (area_rows) {
        area_rows.setAttribute('rows', this.rowContent);
      }
    },

    // define func
    trigerEmit() {
      this.getKnowledgeInput({
        knowledge_id: this.knowledge_id,
        approval_layer_num: this.selectList.approval_layer_num,
        approval_work_type: this.selectList.approval_work_type
      });
    },
    checkMode() {
      let userModeRole = { role: 5, mode: 'ref' };
      if (!this.knowledge_id) {
        userModeRole = { role: 1, mode: 'create' };
        this.reference_mode = false;
      } else {
        const userLogin = this.$store.state.auth.currentUser.user_cd;
        const status = this.status.knowledge_status_type;
        switch (status) {
          case '10': {
            if (+userLogin === +this.create_user_cd) userModeRole = { role: 2, mode: 'edit' };
            break;
          }
          case '20': {
            if (this.other_last_approver_layer.some((el) => el.approval_user_cd === userLogin)) userModeRole = { role: 3, mode: 'approval' };
            break;
          }
          case '30': {
            if (this.last_approver_layer.some((el) => el.approval_user_cd === userLogin)) userModeRole = { role: 4, mode: 'admin' };
            break;
          }
          case '40':
          case '50':
          case '60': {
            if (this.last_approver_layer.some((x) => x.approval_user_cd == userLogin) || this.is_knowledge_admin) userModeRole = { role: 4, mode: 'admin' };
            break;
          }
          default:
            userModeRole = { role: 5, mode: 'ref' };
            break;
        }
      }
      this.role = userModeRole.role;

      this.mode_screen = userModeRole.mode;
      if (userModeRole.mode === 'ref') this.reference_mode = true;
      this.showBtn();
    },
    showBtn() {
      const status = this.status.knowledge_status_type;
      const role = this.role;
      let arrBtn = [];
      arrBtn.push({
        label: 'キャンセル',
        event: this.confirmCancel,
        class: 'btn-outline-primary btn-outline-primary--cancel'
      });
      switch (status) {
        case '10': {
          if (role === 1) {
            arrBtn.push(
              {
                label: '一時保存',
                event: this.temporarilySaveBtn,
                class: 'btn-outline-primary btn-outline-primary--cancel'
              },
              {
                label: '提出',
                event: this.submitBtn,
                class: 'btn-primary'
              }
            );
          } else if (role === 2) {
            arrBtn.push(
              {
                label: '削除',
                event: this.deleteBtn,
                class: 'btn-outline-primary btn-outline-primary--cancel'
              },
              {
                label: '一時保存',
                event: this.temporarilySaveBtn,
                class: 'btn-outline-primary btn-outline-primary--cancel'
              },
              {
                label: '提出',
                event: this.submitBtn,
                class: 'btn-primary'
              }
            );
          }
          break;
        }
        case '20': {
          if (role === 3) {
            if (
              this.obj_approve_comment &&
              this.obj_approve_comment?.approval_user_cd === this.userLogin &&
              this.obj_approve_comment.layer_num === +this.active_layer_num
            ) {
              arrBtn.push(
                {
                  label: '差戻',
                  event: this.remandBtn,
                  class: 'btn-outline-primary btn-outline-primary--cancel'
                },
                {
                  label: '承認',
                  event: this.approvalBtn,
                  class: 'btn-primary'
                }
              );
            }
          }
          break;
        }
        case '30': {
          if (role === 4) {
            arrBtn.push(
              {
                label: '差戻',
                event: this.remandBtn,
                class: 'btn-outline-primary btn-outline-primary--cancel'
              },
              {
                label: '不採用',
                event: this.rejectionBtn,
                class: 'btn-outline-primary btn-outline-primary--cancel'
              },
              {
                label: '公開',
                event: this.confirmPublic,
                class: 'btn-primary'
              }
            );
          }
          break;
        }
        case '40': {
          if (role === 4 || role === 5) {
            if ((this.obj_admin_comment && this.obj_admin_comment?.approval_user_cd === this.userLogin) || this.is_knowledge_admin) {
              arrBtn.push({
                label: '非公開',
                event: this.noPublicBtn,
                class: 'btn-primary'
              });
            }
          }
          break;
        }
        case '50': {
          if (role === 4 || role === 5) {
            if ((this.obj_admin_comment && this.obj_admin_comment?.approval_user_cd === this.userLogin) || this.is_knowledge_admin) {
              arrBtn.push(
                {
                  label: '差戻',
                  event: this.remandBtn,
                  class: 'btn-outline-primary btn-outline-primary--cancel'
                },
                {
                  label: '公開',
                  event: this.confirmPublic,
                  class: 'btn-primary'
                }
              );
            }
          }
          break;
        }
        case '60': {
          if (role === 4 || role === 5) {
            if ((this.obj_admin_comment && this.obj_admin_comment?.approval_user_cd === this.userLogin) || this.is_knowledge_admin) {
              arrBtn.push({
                label: '公開',
                event: this.confirmPublic,
                class: 'btn-primary'
              });
            }
          }
          break;
        }
        default:
          break;
      }

      return arrBtn;
    },
    setAdminComment() {
      let isLastApprove = +this.active_layer_num === +this.selectList.approval_layer_num ? true : false;

      let comment = this.inputKnowlegdeObj?.comment_last_approval || [];
      const commentLastApprove = this.inputKnowlegdeObj?.comment_last_approval?.find((x) => x.approval_user_cd == this.userLogin) || {};

      const objTemp = this.$_.sortBy(comment, 'approval_datetime')[comment.length - 1];

      if (isLastApprove) {
        this.obj_admin_comment = { ...commentLastApprove } || {};
        let isUserComment = Object.keys(commentLastApprove).length > 0;
        if (!isUserComment && (this.role === 2 || this.mode_screen === 'admin' || this.mode_screen === 'ref')) {
          this.obj_admin_comment = objTemp || {};
        }
      } else {
        this.obj_admin_comment = objTemp || {};
      }

      this.obj_admin_commentOld = JSON.parse(JSON.stringify(this.obj_admin_comment));
    },

    setApproveComment() {
      let comment = this.inputKnowlegdeObj?.comment_orther_approval || [];

      const commentApprove = this.inputKnowlegdeObj?.comment_orther_approval?.find(
        (x) => x.approval_user_cd === this.userLogin && x.layer_num === +this.active_layer_num
      );

      this.obj_approve_comment = { ...commentApprove } || {};

      // show Latest comment with mode ref
      if (this.reference_mode || this.mode_screen === 'admin' || this.role === 2) {
        comment = comment.filter((el) => el.approval_datetime !== null);
        if (comment.length) {
          this.obj_approve_comment = this.$_.sortBy(comment, 'approval_datetime')[comment.length - 1];
        }
      }

      this.obj_approve_commentOld = JSON.parse(JSON.stringify(this.obj_approve_comment));
    },

    handleChangeMedical(val) {
      this.inputKnowlegdeObj.medical_subjects_group_name = this.selectList.medical_subjects.find(
        (el) => el.medical_subjects_group_cd === val
      ).medical_subjects_group_name;
    },

    handleChangeCategory(val) {
      this.inputKnowlegdeObj.knowledge_category_name = this.selectList.category.find((el) => el.knowledge_category_cd === val).knowledge_category_name;
    },

    checkPerson() {
      if (this.checkFacility_cd && this.inputKnowlegdeObj.facility_cd && this.checkFacility_cd !== this.inputKnowlegdeObj.facility_cd) {
        this.$notify({ message: '施設個人の施設は指定の施設と一致しません。', customClass: 'error' });
        this.error_flg = true;
      } else this.error_flg = false;
    },

    checkValidField(field) {
      return this.isSubmit && !this.validation[field].required;
    },

    checkEditField(fieldName) {
      let flg = false; // true: edit, false: not edit
      if (this.reference_mode) return false;
      const mode = this.mode_screen;
      const arrField = ['提出者コメント', '承認者コメント', '管理者コメント', '活用度ポイント付与'];
      if (!arrField.some((el) => el === fieldName)) fieldName = 'other';
      switch (mode) {
        case 'create':
        case 'edit': {
          if (fieldName === '提出者コメント' || fieldName === 'other') flg = true;
          break;
        }
        case 'approval': {
          if (fieldName === '承認者コメント') {
            if (
              this.obj_approve_comment &&
              this.obj_approve_comment?.approval_user_cd === this.userLogin &&
              this.obj_approve_comment.layer_num === +this.active_layer_num
            ) {
              flg = true;
            }
          }
          break;
        }
        case 'admin': {
          if (fieldName === '管理者コメント' || fieldName === 'other') flg = true;
          if (fieldName === '活用度ポイント付与' && !this.inputKnowlegdeObj.active_point_cd) flg = true;
          break;
        }
        default:
          break;
      }
      return flg;
    },

    checkEditTimeline() {
      return this.mode_screen === 'create' || this.mode_screen === 'edit' || this.mode_screen === 'admin';
    },

    getActiveStatus() {
      let active = 0;
      if (this.status.knowledge_status_type === '50' || this.status.knowledge_status_type === '60') active = 3;
      else active = this.status.knowledge_status_type / 10 - 1;
      return active;
    },
    setStatus(obj) {
      let status = [
        { definition_label: '作成中', definition_value: '10' },
        { definition_label: '承認確認中', definition_value: '20' },
        { definition_label: '公開待ち', definition_value: '30' },
        { definition_label: '公開中', definition_value: '40' }
      ];
      if (obj.definition_value === '50' || obj.definition_value === '60') status[3] = obj;
      this.statusList = status;
    },
    handleClickStatus(step) {
      if (this.$refs[step]) {
        this.$refs[step][0]?.click();
      }
    },
    handleRemoveTag(type, item) {
      if (this.checkEditField(type)) {
        if (type === 'users') {
          this.inputKnowlegdeObj.users = this.inputKnowlegdeObj.users.filter((el) => el.user_cd !== item.user_cd);
        } else if (type === 'person') {
          this.inputKnowlegdeObj.person_cd = '';
          this.inputKnowlegdeObj.person_name = '';
          this.checkFacility_cd = '';
          this.checkFacility_name = '';
          this.props_Z05_S04 = {
            ...this.paramZ05S04Default(),
            user_cd: this.currentUser.user_cd,
            user_name: this.currentUser.user_name
          };
          this.filterDataZ05S03 = {
            ...this.filterDataZ05S03,
            org_cd: this.inputKnowlegdeObj.facility_cd ? '' : this.currentUser.org_cd,
            user_cd: this.inputKnowlegdeObj.facility_cd ? '' : this.currentUser.user_cd,
            user_name: this.inputKnowlegdeObj.facility_cd ? '' : this.currentUser.user_name
          };
        } else if (type === 'facility') {
          this.confirmRemoveFacilityAndTimeline();
        } else if (type === 'product') {
          this.inputKnowlegdeObj.product_cd = '';
          this.inputKnowlegdeObj.product_name = '';
        }
      }
    },

    confirmRemoveFacilityAndTimeline() {
      if (this.time_line.length > 0) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1, message: '施設を変更するとタイムラインがリセットされます。よろしいですか？', params: { removeFacility: 'removeFacility' } },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.removeFacility();
      }
    },

    removeFacility() {
      this.inputKnowlegdeObj.facility_short_name = '';
      this.inputKnowlegdeObj.facility_cd = '';
      this.filterDataZ05S03 = {};
      this.props_Z05_S04.facility_cd = this.inputKnowlegdeObj.person_cd ? this.props_Z05_S04.facility_cd : [];
      this.filterDataZ05S03 = {
        selectGroupCd: '',
        facility_cd: [],
        facility_name: [],
        org_cd: this.inputKnowlegdeObj.person_cd ? '' : this.currentUser.org_cd,
        user_cd: this.inputKnowlegdeObj.person_cd ? '' : this.currentUser.user_cd,
        user_name: this.inputKnowlegdeObj.person_cd ? '' : this.currentUser.user_name
      };
      this.time_line = [];
      this.onCloseModal();
    },

    callScreenA01_S04(item) {
      let params = {
        call_id: item.call_id || item.channel_id,
        schedule_id: item.schedule_id
      };
      A01_S04_Service.checkInterviewDetailInput(params)
        .then(() => {
          this.callScreen('A01S04', params);
        })
        .catch(() => {
          this.$notify({ message: '面談情報が削除されたため、詳細を見ることができません。', customClass: 'error' });
        })
        .finally(() => {});
    },

    callScreen(screenID, props) {
      let objScreen = {
        A01S04: 'A01_S04_InterviewDetailedInput',
        Z04S01: 'Z04_S01_AccountSettings',
        H02S02: 'H02_PersonalDetails',
        C01S02: 'C01_S02_LectureInput',
        B01S02: 'B01_S02_BriefingSessionInput'
      };
      if (screenID === 'Z04S01' && !props) return;
      else if (screenID === 'Z04S01') {
        this.$router.push({ name: objScreen[screenID], params: props });
        return;
      }
      if (screenID === 'H02S02') {
        let person_cd = props.person_cd;
        this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
        return;
      }
      this.$router.push({ name: objScreen[screenID], params: props });
    },

    returnIcon(id) {
      const obj = {
        // define
        10: 'icon_interview-daily.svg',
        20: 'icon_interview-daily02.svg',
        30: 'icon_interview-daily01.svg',
        40: 'icon_interview-daily03.svg',
        // check at api getScreen (status_approval)
        0: 'icon_majesticons_pending-circle.svg', // 承認待ち
        1: 'icon_majesticons_check-circle.svg', // 承認済み
        2: 'icon_majesticons_close-circle.svg' // 差戻 remand
      };
      return obj[id];
    },

    flatTimeLine(data) {
      for (const i in data) {
        let element = data[i];
        if (element.data && element.data.length > 0) {
          this.flatTimeLine(element.data);
        } else {
          this.dataFlatTimeline.push(element);
        }
      }
    },

    deleteTimelineBtn(index_parent, index_child, item) {
      this.dataFlatTimeline = [];
      this.flatTimeLine(this.time_lineOld);

      let index = this.dataFlatTimeline.findIndex((x) => x.timeline_id === item.timeline_id);
      if (index > -1) {
        this.time_line[index_parent].data[index_child] = {
          ...this.time_line[index_parent].data[index_child],
          delete_flag: 1
        };
      } else {
        this.time_line[index_parent].data.splice(index_child, 1);
      }
    },

    showTimeline(index) {
      let flg = false;
      let arr = this.time_line.map((el) => ({
        ...el,
        data: el.data.filter((i) => i.delete_flag !== 1)
      }));
      if (arr[index]?.data?.length) flg = true;
      return flg;
    },

    cancelBtn() {
      localStorage.removeItem('checkChangTab');
      if (this.fromF01S02 === 'true') {
        this.$router.push({ name: 'F01_S02_KnowledgeDetails', params: { knowledge_id: this.knowledge_id } });
      } else {
        this.$router.push({ name: 'F01_S05_Pre_ReleaseKnowledgeManagement' });
      }
    },

    handleYesConfirm(data) {
      if (data) {
        if (data.knowledge === 'public') {
          this.publicBtn();
        } else {
          if (data.removeFacility === 'removeFacility') {
            this.removeFacility();
          } else {
            if (data.resultFacility === 'resultFacility') {
              this.resultFacility(data.dataResult);
            } else {
              this.handleConfirmYes();
            }
          }
        }
      } else {
        this.cancelBtn();
      }
    },

    confirmCancel() {
      this.hasErrReject = false;
      this.closeNoti(2);
      let isEqual =
        !_.isEqual(this.inputKnowlegdeObj, this.inputKnowlegdeObjOld) ||
        !_.isEqual(this.time_line, this.time_lineOld) ||
        !_.isEqual(this.obj_admin_comment, this.obj_admin_commentOld) ||
        !_.isEqual(this.obj_approve_comment, this.obj_approve_commentOld);

      if (isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1 },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.cancelBtn();
      }
    },

    deleteBtn() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { mode: 0, title: '以下のナレッジを完全に削除しますか?' }
      };
    },

    handleConfirmDelete() {
      const params = {
        mode_screen: this.mode_screen,
        knowledge_id: this.knowledge_id,
        create_user_cd: this.create_user_cd
      };
      this.deleteAPI(params);
    },

    submitBtn() {
      this.checkPerson();
      if (!this.checkValidate() || this.error_flg) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      this.hasErrReject = false;
      this.closeNoti(2);
      const params = {
        knowledge_id: this.knowledge_id,
        mode_screen: this.mode_screen,
        ...this.inputKnowlegdeObj,
        anonymous_flag: this.inputKnowlegdeObj.anonymous_flag ? 1 : 0,
        create_user_cd: this.create_user_cd,
        time_line: this.time_line,
        approval_layer_num: this.selectList.approval_layer_num,
        approval_work_type: this.selectList.approval_work_type,
        active_point_cd: ''
      };
      this.submitAPI(params);
    },

    approvalBtn(event, isConfirm) {
      this.hasErrReject = false;
      this.closeNoti(2);
      if (isConfirm) {
        const params = {
          mode_screen: this.mode_screen,
          knowledge_id: this.knowledge_id,
          create_user_cd: this.create_user_cd,
          approval_layer_num: this.selectList.approval_layer_num,
          approval_work_type: this.selectList.approval_work_type,
          request_id: this.data_approval.approval_request.request_id,
          comment_orther_approval: this.obj_approve_comment,
          active_layer_num: this.active_layer_num
        };
        this.approvalAPI(params);
      } else {
        this.openModalConfirm('approval');
      }
    },
    remandBtn(event, isConfirm) {
      this.hasErrReject = false;
      this.closeNoti(2);
      if (isConfirm) {
        let isLastRemand = +this.active_layer_num === +this.selectList.approval_layer_num ? true : false;
        const params = {
          mode_screen: this.mode_screen,
          knowledge_id: this.knowledge_id,
          create_user_cd: this.create_user_cd,
          approval_layer_num: this.selectList.approval_layer_num,
          approval_work_type: this.selectList.approval_work_type,
          ...this.inputKnowlegdeObj,
          anonymous_flag: this.inputKnowlegdeObj.anonymous_flag ? 1 : 0,
          time_line: this.time_line,
          request_id: this.data_approval.approval_request.request_id,
          comment_remand: isLastRemand ? this.obj_admin_comment : this.obj_approve_comment
        };

        delete params.comment_orther_approval;
        delete params.comment_last_approval;

        this.remandAPI(params);
      } else {
        this.openModalConfirm('remand');
      }
    },

    confirmPublic() {
      if (!this.inputKnowlegdeObj.active_point_cd) {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: {
            mode: 1,
            message:
              '活用度ポイントが選択されていません。ナレッジ提供者への評価として、活用度ポイントの付与をお願いします。活用ポイントを付与しないで公開しますか？',
            params: { knowledge: 'public' }
          },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.publicBtn();
      }
    },

    publicBtn() {
      this.hasErrReject = false;
      this.closeNoti(2);
      const params = {
        mode_screen: this.mode_screen,
        knowledge_id: this.knowledge_id,
        create_user_cd: this.create_user_cd,
        ...this.inputKnowlegdeObj,
        anonymous_flag: this.inputKnowlegdeObj.anonymous_flag ? 1 : 0,
        time_line: this.time_line,
        approval_layer_num: this.selectList.approval_layer_num,
        approval_work_type: this.selectList.approval_work_type,
        comment_last_approval: this.obj_admin_comment,
        request_id: this.data_approval.approval_request.request_id
      };
      delete params.comment_orther_approval;

      this.publicAPI(params);
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },

    rejectionBtn(event, isConfirm) {
      if (this.obj_admin_comment.comment && this.obj_admin_comment.comment?.trim()) {
        this.hasErrReject = false;
        if (isConfirm) {
          const params = {
            mode_screen: this.mode_screen,
            knowledge_id: this.knowledge_id,
            submit_comment: this.inputKnowlegdeObj.submit_comment,
            create_user_cd: this.create_user_cd,
            ...this.inputKnowlegdeObj,
            anonymous_flag: this.inputKnowlegdeObj.anonymous_flag ? 1 : 0,
            time_line: this.time_line,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type,
            request_id: this.data_approval.approval_request.request_id,
            comment_reject: this.obj_admin_comment
          };
          delete params.comment_orther_approval;
          delete params.comment_last_approval;
          this.rejectionAPI(params);
        } else {
          this.openModalConfirm('cancelSubmit');
        }
      } else {
        this.hasErrReject = true;
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
      }
    },

    noPublicBtn() {
      this.hasErrReject = false;
      this.closeNoti(2);
      const params = {
        mode_screen: this.mode_screen,
        knowledge_id: this.knowledge_id,
        create_user_cd: this.create_user_cd,
        approval_layer_num: this.selectList.approval_layer_num,
        approval_work_type: this.selectList.approval_work_type,
        request_id: this.data_approval.approval_request.request_id,
        ...this.inputKnowlegdeObj,
        anonymous_flag: this.inputKnowlegdeObj.anonymous_flag ? 1 : 0,
        comment_none_public: this.obj_admin_comment
      };
      delete params.comment_orther_approval;
      delete params.comment_last_approval;
      this.noPublicAPI(params);
    },

    temporarilySaveBtn() {
      this.checkPerson();
      if (!this.checkValidate() || this.error_flg) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      const params = {
        knowledge_id: this.knowledge_id,
        mode_screen: this.mode_screen,
        ...this.inputKnowlegdeObj,
        anonymous_flag: this.inputKnowlegdeObj.anonymous_flag ? 1 : 0,
        create_user_cd: this.create_user_cd,
        time_line: this.time_line,
        approval_work_type: this.selectList.approval_work_type,
        approval_layer_num: this.selectList.approval_layer_num
      };
      this.createUpdateAPI(params);
    },

    groupDate(arr) {
      return this.$_.chain(arr)
        .groupBy('start_date')
        .map((key, value) => ({ data: key, date: value }))
        .value();
    },

    sortData(arr, conditionSort, orderBy) {
      return this.$_.orderBy(arr, [conditionSort], [orderBy]);
    },

    // modal
    closeModal(data) {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
      if (data)
        if (data.screen === 'Z05_S01' && data.userSelected) {
          this.inputKnowlegdeObj.users = data.userSelected;
        } else {
          if (data.screen === 'Z05_S03' && data.facilitySelectList) {
            if (this.inputKnowlegdeObj.facility_cd !== data.facilitySelectList[0].facility_cd && this.time_line.length > 0) {
              this.modalConfig = {
                ...this.modalConfig,
                title: '',
                isShowModal: true,
                isShowClose: false,
                component: markRaw(this.$PopupConfirm),
                props: {
                  mode: 1,
                  message: '施設を変更するとタイムラインがリセットされます。よろしいですか？',
                  params: { resultFacility: 'resultFacility', dataResult: data }
                },
                width: '35rem',
                customClass: 'custom-dialog modal-fixed modal-fixed-min',
                destroyOnClose: true,
                closeOnClickMark: false
              };
            } else {
              this.resultFacility(data);
            }
          } else {
            if (data.screen === 'Z05_S04' && data.facilityPersonalSelected) {
              this.inputKnowlegdeObj.person_cd = data.facilityPersonalSelected[0].person_cd;
              this.inputKnowlegdeObj.person_name = data.facilityPersonalSelected[0].person_name;
              this.checkFacility_cd = data.facilityPersonalSelected[0].facility_cd;
              this.checkFacility_name = data.facilityPersonalSelected[0].facility_short_name;
              this.props_Z05_S04 = {
                ...data.filterData,
                facility_cd: data.facilityPersonalSelected.map((x) => x.facility_cd),
                person_cd: data.facilityPersonalSelected.map((x) => x.person_cd),
                facility_name: [data.filterData.facility_name]
              };
              if (!this.inputKnowlegdeObj.facility_cd) {
                this.inputKnowlegdeObj.facility_cd = data.facilityPersonalSelected[0].facility_cd;
                this.inputKnowlegdeObj.facility_short_name = data.facilityPersonalSelected[0].facility_short_name;
              }
            } else {
              if (data.screen === 'Z05_S06' && data.dataSelected) {
                this.inputKnowlegdeObj.product_cd = data.dataSelected[0].product_cd;
                this.inputKnowlegdeObj.product_name = data.dataSelected[0].product_name;
              } else {
                if (data.screen === 'F01_S04' && data.timeLineIDList) {
                  this.dataFlatTimeline = [];
                  this.flatTimeLine(this.time_line);
                  let dataTimelineConvert = data.timeLineIDList.map((x) => {
                    return {
                      ...x,
                      start_date: moment(new Date(x.start_date)).format('YYYY/MM/DD'),
                      channel_detail: x.detail
                    };
                  });

                  let dataFlat = this.$_.unionBy(this.dataFlatTimeline.concat(dataTimelineConvert), 'channel_id');

                  let result = this.sortData(dataFlat, 'start_datetime', 'desc');

                  let datas = this.groupDate(result);

                  for (let i = 0; i < datas.length; i++) {
                    let item = datas[i];
                    if (item.data.length > 0) {
                      item.data = this.sortData(item.data, 'all_day_flag', 'desc');
                    }
                  }
                  this.time_line = datas;
                }
              }
            }
          }
        }
      localStorage.removeItem('time_line_knowledge');

      this.checkPerson();
    },

    resultFacility(data) {
      this.inputKnowlegdeObj.facility_short_name = data.facilitySelectList[0].facility_short_name;
      this.inputKnowlegdeObj.facility_cd = data.facilitySelectList[0].facility_cd;
      this.filterDataZ05S03 = {
        ...data.filterData,
        facilityCd: data.facilitySelectList.map((x) => x.facility_cd),
        facilityName: [data.filterData.facility_name]
      };

      this.time_line = [];
      this.onCloseModal();
    },

    openModal({ screen, title, width, isShowClose = true, props = {}, customClass = 'custom-dialog' }, fieldName) {
      if ((fieldName && !this.checkEditField(fieldName)) || !screen) return;
      if (screen === 'Z05_S04') {
        props.propsPrams = {
          ...props.propsPrams,
          facility_cd: this.inputKnowlegdeObj?.facility_cd ? [this.inputKnowlegdeObj?.facility_cd] : this.checkFacility_cd ? [this.checkFacility_cd] : [],
          non_facility_flag: this.inputKnowlegdeObj?.facility_cd ? 1 : 0,
          user_cd: this.inputKnowlegdeObj?.facility_cd || this.inputKnowlegdeObj?.person_cd ? '' : props.propsPrams.user_cd,
          user_name: this.inputKnowlegdeObj?.facility_cd || this.inputKnowlegdeObj?.person_cd ? '' : props.propsPrams.user_name
        };
      }
      if (screen === 'Z05_S03') {
        props = {
          ...props,
          orgCD: this.inputKnowlegdeObj?.facility_cd || this.inputKnowlegdeObj?.person_cd ? '' : this.filterDataZ05S03.org_cd,
          userCD: this.inputKnowlegdeObj?.facility_cd || this.inputKnowlegdeObj?.person_cd ? '' : this.filterDataZ05S03.user_cd,
          userName: this.inputKnowlegdeObj?.facility_cd || this.inputKnowlegdeObj?.person_cd ? '' : this.filterDataZ05S03.user_name,
          selectGroupCd: this.filterDataZ05S03.select_group_id,
          targetProductCd: this.filterDataZ05S03.target_product_cd,
          facilityCategoryType: this.filterDataZ05S03.facility_category_type,
          prefectureCD: this.filterDataZ05S03.prefecture_cd,
          prefectureName: this.filterDataZ05S03.prefecture_name,
          municipalityCD: this.filterDataZ05S03.municipality_cd,
          municipalityName: this.filterDataZ05S03.municipality_name,
          facilityCd: props.facilityCd ? props.facilityCd : this.filterDataZ05S03.facilityCd,
          facilityName: this.filterDataZ05S03.facilityName
        };
      }

      if (screen === 'F01_S04') {
        if (!this.inputKnowlegdeObj?.facility_cd) {
          this.$notify({ message: 'タイムラインを追加するには、ナレッジ情報タブで施設を選択する必要があります。', customClass: 'error' });
          return;
        }

        localStorage.setItem('time_line_knowledge', true);
      }
      const obj = {
        F01_S04: F01_S04_TimelineSelection,
        Z05_S01: Z05_S01_ModalOrganization,
        Z05_S03: Z05_S03_FacilitySelection,
        Z05_S06: Z05_S06_Material_Selection,
        Z05_S04: Z05_S04_FacilityCustomerSelection
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: title,
        isShowModal: true,
        isShowClose: isShowClose,
        component: this.markRaw(obj[screen]),
        width: width,
        customClass: customClass,
        props: props
      };
    },

    openModalComment(item) {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'コメント',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        isShowModal: true,
        isShowClose: true,
        component: this.markRaw(B01_S02_ModalComment),
        width: '35rem',
        props: {
          comment: item.comment,
          status_type: item.status_type,
          user_name: item.user_name
        }
      };
    },

    // call api
    getMedicalStaff() {
      let params = {
        facility_cd: ''
      };
      this.isLoadingPage = true;

      this.lstMedicalStaff = [];
      Z05_S04_facilityCustomerService.getMedicalStaff(params)
        .then((res) => {
          this.lstMedicalStaff = res?.data?.data.map((x) => x.medical_staff_cd);
          this.getScreenData();
        })
        .catch((err) => {
          this.isLoadingPage = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S04', idNodeNotify: 'msfa-notify-Z05S04' });
        })
        .finally(() => {});
    },

    checkPersonMedicalStaff(person) {
      if (!person.person_cd || this.lstMedicalStaff.includes(person.person_cd)) {
        return true;
      }

      return false;
    },

    getScreenData() {
      localStorage.removeItem('time_line_knowledge');
      F01_S03_Service.getScreenDataService()
        .then((res) => {
          const dataRes = res.data.data;
          this.selectList = dataRes;
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
        })
        .catch((err) => {
          this.isLoadingPage = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally();
    },
    getKnowledgeInput(params) {
      this.isLoadingPage = true;
      F01_S03_Service.getKnowledgeInputService(params)
        .then((res) => {
          const dataRes = res.data.data;
          this.inputKnowlegdeObj = {
            ...this.inputKnowlegdeObj,
            ...dataRes.knowledgeTab1AndTab3,
            anonymous_flag: dataRes.knowledgeTab1AndTab3?.anonymous_flag === 1
          };

          this.active_point_cd_flg = dataRes.knowledgeTab1AndTab3?.active_point_cd ? true : false;
          this.active_layer_num = dataRes.knowledgeTab1AndTab3?.active_layer_num;
          this.status = dataRes.status;
          this.setStatus(dataRes.status);
          this.note = dataRes.note;
          this.time_line = dataRes.time_line;
          this.create_user_cd = dataRes.create_user_cd;
          this.is_knowledge_admin = dataRes.is_knowledge_admin;
          this.last_approver_layer = dataRes.last_approver_layer;
          this.other_last_approver_layer = dataRes.other_last_approver_layer;
          this.data_approval = {
            ...dataRes.data_approval,
            approval_request_detail: this.$_.chain(dataRes.data_approval?.approval_request_detail)
              .groupBy('layer_num')
              .map((key, value) => ({ data: key, layer_num: value }))
              .value()
          };

          this.inputKnowlegdeObjOld = JSON.parse(JSON.stringify(this.inputKnowlegdeObj));
          this.time_lineOld = JSON.parse(JSON.stringify(this.time_line));

          this.checkMode();
          this.setAdminComment();
          this.setApproveComment();
          this.checkMode();

          if (this.inputKnowlegdeObj.facility_cd) {
            this.filterDataZ05S03 = {
              ...this.filterDataZ05S03,
              facility_cd: [this.inputKnowlegdeObj.facility_cd],
              org_cd: '',
              user_cd: '',
              user_name: ''
            };

            this.props_Z05_S04 = {
              ...this.props_Z05_S04,
              facility_cd: [this.inputKnowlegdeObj.facility_cd],
              user_cd: '',
              user_name: ''
            };
          }

          if (
            this.status?.knowledge_status_type === '40' &&
            !(this.last_approver_layer.some((x) => x.approval_user_cd == this.userLogin) || this.is_knowledge_admin)
          ) {
            let id = this.inputKnowlegdeObj?.knowledge_id;
            this.$router.push({ name: 'F01_S02_KnowledgeDetails', params: { knowledge_id: id } });
          }
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoadingPage = false;
        });
    },
    createUpdateAPI(params) {
      F01_S03_Service.createUpdateService(params)
        .then((res) => {
          this.knowledge_id = res.data.data.knowledge_id;
          if (!this._route('F01_S03_KnowledgeInput')?.params?.knowledge_id) {
            this.$router.push({ name: 'F01_S03_KnowledgeInput', params: { knowledge_id: res.data.data.knowledge_id, fromF01S02: this.fromF01S02 } });
          }
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },
    deleteAPI(params) {
      F01_S03_Service.deleteService(params)
        .then((res) => {
          this.$notify({ message: res.data.message || this.getMessage('MSFA0050'), customClass: 'success' });
          this.cancelBtn();
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },
    submitAPI(params) {
      F01_S03_Service.submitService(params)
        .then((res) => {
          this.knowledge_id = res.data.data.knowledge_id;
          if (!this._route('F01_S03_KnowledgeInput')?.params?.knowledge_id) {
            this.$router.push({ name: 'F01_S03_KnowledgeInput', params: { knowledge_id: res.data.data.knowledge_id, fromF01S02: this.fromF01S02 } });
          }
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },
    approvalAPI(params) {
      F01_S03_Service.approvalService(params)
        .then((res) => {
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.onCloseModal();
        });
    },
    remandAPI(params) {
      F01_S03_Service.remandService(params)
        .then((res) => {
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.onCloseModal();
        });
    },
    publicAPI(params) {
      F01_S03_Service.publicService(params)
        .then((res) => {
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },
    rejectionAPI(params) {
      F01_S03_Service.rejectionService(params)
        .then((res) => {
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.onCloseModal();
        });
    },
    noPublicAPI(params) {
      F01_S03_Service.noPublicService(params)
        .then((res) => {
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },
    deleteTimelineAPI(params) {
      F01_S03_Service.deleteTimelineService(params)
        .then((res) => {
          this.getKnowledgeInput({
            knowledge_id: this.knowledge_id,
            approval_layer_num: this.selectList.approval_layer_num,
            approval_work_type: this.selectList.approval_work_type
          });
          this.$notify({ message: res.data.message || this.getMessage('MSFA0050'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },

    openModalConfirm(type, isDelete) {
      this.typeButton = type;
      let message = '';

      switch (this.typeButton) {
        case 'approval':
          message = 'このナレッジを承認します。よろしいですか？';
          this.openConfirm(type, isDelete, message);
          break;
        case 'remand':
          message = 'このナレッジの提出を差し戻します。よろしいですか？';
          this.openConfirm(type, isDelete, message);
          break;
        case 'cancelSubmit':
          message = 'このナレッジを不採用にします。よろしいですか？';
          this.openConfirm(type, isDelete, message);
          break;
      }
    },

    openConfirm(type, isDelete, message) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { mode: isDelete ? 0 : 1, message: message, title: '', params: { type: this.typeButton } }
      };
    },

    handleConfirmYes() {
      switch (this.typeButton) {
        case 'approval':
          this.approvalBtn(null, true);
          break;
        case 'remand':
          this.remandBtn(null, true);
          break;
        case 'cancelSubmit':
          this.rejectionBtn(null, true);
          break;

        default:
          break;
      }
    },
    onCloseModal() {
      this.typeButton = '';
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowClose: false, isShowModalConfirmInput: false };
    },

    changeCommentAdmin() {
      this.hasErrReject = false;
      const notification = document.querySelectorAll('.el-notification');
      notification?.forEach((el) => {
        el.remove();
      });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 1080px;
$viewport_sm: 767px;
/* layout */
.btn-comment {
  float: right;
  padding: 0;
  background: inherit;
  border-radius: 100%;
  color: #448add;
  background: #ffffff;
  box-shadow: 0px 4px 5px #1a3a691a, 0px 1px 10px #1a3a691a, 0px 2px 4px #1a3a691a;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;

  .svg {
    width: 13px;
  }
}
.form-view {
  height: 40px;
}
.nodata {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 20px;
  height: calc(100% - 40px);
  justify-content: center;
  span {
    margin-bottom: 20px;
  }
}
.groupKnow {
  padding-top: 28px;
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  .groupKnow-step {
    .dailyStep {
      padding: 12px 24px;
      &.dailyStep--blue {
        background: #eefbfa;
        background: -moz-linear-gradient(left, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
        background: -webkit-linear-gradient(left, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
        background: linear-gradient(to right, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
      }
      &.daiyStep--noPublic {
        background: linear-gradient(
          89.79deg,
          #e8edf3 -14.77%,
          rgba(219, 229, 241, 0.734225) 38.65%,
          rgba(219, 229, 241, 0.79) 92.06%,
          rgba(204, 207, 217, 0.08) 123.29%
        );
      }
    }
  }
  .groupKnow-content {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    .knowTabs {
      background: #fff;
      box-shadow: 0px 3px 30px rgba(0, 0, 0, 0.05);
      padding-top: 10px;
      .navTabs {
        li {
          &:hover {
            a:not(.active) {
              color: #5f6b73;
              font-weight: bold;
              text-decoration: none;
            }
          }
        }
      }
    }
    .knowMain {
      box-shadow: 0px 0px 5px #b7c3cb;
      height: 100%;
      overflow: hidden;
      position: relative;
      z-index: 1;
      .tab-content,
      .tab-pane {
        height: 100%;
        position: relative;
      }
      .knowForm-box {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        height: 100%;
        font-size: 16px;
      }
    }
  }
  .groupKnow-btn {
    padding: 20px 24px 20px;
    text-align: center;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
/* end layout */
.groupKnow {
  .knowForm {
    height: 100%;
    padding: 0 52px 20px;
    background: #fff;
    box-shadow: 0px 0px 5px #b7c3cb;
    position: relative;
    z-index: 1;
    @media (max-width: $viewport_tablet) {
      padding: 0 24px 8px;
    }
  }
  .knowForm-request {
    padding: 22px 0;
    border-bottom: 1px solid #cad4db;
    display: flex;
    flex-wrap: wrap;
    .knowForm-tlt {
      width: 90px;
      padding: 0 10px;
      font-size: 1rem;
    }
    .knowForm-switch {
      width: 80px;
    }
    .knowForm-note {
      width: calc(100% - 170px);
      font-size: 1rem;
    }
  }
  .knowForm-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -52px;
    @media (max-width: $viewport_tablet) {
      margin-left: -24px;
    }
    .knowForm-col {
      width: 50%;
      padding-left: 52px;
      @media (max-width: $viewport_tablet) {
        padding-left: 24px;
      }
    }
    .knowForm-group {
      padding-top: 8px;
      > ul {
        > li {
          margin-top: 12px;
        }
      }
      .knowForm-label {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 1rem;
        .knowForm-required {
          line-height: 10px;
          min-width: 9px;
          margin: 0 0 0 8px;
        }
      }
      .knowForm-col2 {
        > ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: -35px;
          @media (max-width: $viewport_tablet) {
            margin-left: -12px;
          }
          > li {
            width: 50%;
            padding-left: 35px;
            @media (max-width: $viewport_tablet) {
              padding-left: 12px;
            }
          }
        }
      }
    }
    .knowDetail {
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -12px;
        > li {
          width: 50%;
          margin-top: 30px;
          padding-left: 12px;
        }
      }
      .flex-custom {
        display: flex;
        .knowDetail-tlt {
          flex: 0 0 118px;
          color: #99a5ae;
        }
        .knowDetail-tlt-sm {
          flex: 0 0 55px !important;
        }
        .knowDetail-label {
          margin-top: 0 !important;
        }
      }
      .knowDetail-tlt {
        font-size: 1rem;
        line-height: 20px;
        color: #99a5ae;
      }
      .knowDetail-text {
        .knowDetail-label {
          margin-top: 10px;
          line-height: 20px;
          white-space: pre-line;
        }
      }
    }
  }
  .knowTimeline {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 100%;
  }
  .timeline-head {
    background: #fff;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    padding: 16px 24px;
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    .timeline-lst {
      display: flex;
      flex-wrap: wrap;
      padding-left: 18px;
      @media (max-width: $viewport_tablet) {
        padding-left: 0;
      }
      li {
        padding-right: 50px;
        margin-top: 2px;
        display: flex;
        font-size: 1rem;
        @media (max-width: $viewport_tablet) {
          padding-right: 30px;
        }
        &:last-child {
          padding-right: 0;
        }
        .item {
          min-width: 22px;
          margin: -2px 6px 0 0;
        }
      }
    }
    .timeline-btn {
      .btn {
        height: 32px;
        padding: 0 12px;
        .svg {
          width: 17px;
          height: initial;
        }
      }
    }
  }
  .timeline-content {
    height: 100%;
    background: #f7f7f7;
    box-shadow: 0px 0px 5px #b7c3cb;
    padding: 20px 24px 20px 20px;
  }
  .dailyList {
    .dailyList-sub {
      li {
        padding: 7px 0 13px;
      }
    }
    > ul {
      > li {
        position: relative;
        &::before {
          position: absolute;
          top: 0;
          left: 204px;
          content: '';
          height: 100%;
          width: 2px;
          background: #b7c3cb;
          display: block;
          @media (max-width: $viewport_desktop) {
            left: 140px;
          }
        }
        .dailyList-calendar {
          width: 100%;
          .label {
            background: #d9e7fd;
            color: #285888;
            text-align: center;
            border-radius: 20px 0px 0px 20px;
            box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
            padding: 9px 10px;
            width: 204px;
            display: block;
            font-weight: 700;
            @media (max-width: $viewport_desktop) {
              width: 140px;
              padding: 8px 0;
            }
          }
        }
        .dailyList-flex {
          display: flex;
          flex-wrap: wrap;
          align-items: flex-start;
          .dailyList-dateTime {
            width: 260px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            padding: 20px 0 0 83px;
            @media (max-width: $viewport_desktop) {
              width: 187px;
              padding: 20px 0 0 19px;
            }
            .time {
              width: 100px;
              color: #99a5ae;
              text-align: right;
              padding-right: 12px;
            }
            .iconItem {
              width: 44px;
              height: 44px;
              display: flex;
              align-items: center;
              justify-content: center;
              position: relative;
              &::after {
                position: absolute;
                top: 0;
                left: 0;
                content: '';
                width: 44px;
                height: 44px;
                display: block;
                background: #fff;
                border: 2px solid #727f88;
                border-radius: 5px;
                transform: rotate(45deg);
              }
              &::before {
                position: absolute;
                top: 21px;
                right: -36px;
                content: '';
                height: 2px;
                width: 30px;
                background: #b7c3cb;
                display: block;
              }
              .item {
                position: relative;
                z-index: 1;
              }
            }
          }
          .row__custom {
            width: calc(100% - 260px);
            display: flex;
            .col__custom {
              width: 50%;
            }
            .dailyList-content {
              position: relative;
              .dailyList-box {
                background: #fff;
                box-shadow: 1px 1px 4px rgba(183, 195, 203, 0.9);
                border-radius: 8px;
                padding: 12px 56px 12px 20px;
                @media (max-width: $viewport_desktop) {
                  padding: 12px 56px 12px 12px;
                }
                .dailyList-label {
                  margin-top: -4px;
                  .span-label {
                    margin-top: 4px;
                    margin-right: 12px;
                    padding: 1px 7px;
                    &:last-child {
                      margin-right: 0;
                    }
                    &.span-label--off {
                      background: #ffeab6;
                      color: #e2633b;
                    }
                    &.span-label--knowledge {
                      background: #daf8dc;
                      color: #28a470;
                    }
                    .span-label-item {
                      display: inline-flex;
                      margin: 3px 3px 0 0;
                    }
                  }
                }
              }
              .dailyList-group {
                padding-top: 4px;
                .dailyList-info {
                  display: flex;
                  flex-wrap: wrap;
                  margin-top: 4px;
                  .dailyList-info-tlt {
                    width: 80px;
                    padding-right: 5px;
                    .dailyList-gray {
                      color: #727f88;
                    }

                    &.interview {
                      width: 55px;
                    }
                    &.briefing,
                    &.convention,
                    &.external {
                      width: 80px;
                    }
                  }
                  .dailyList-info-text {
                    width: calc(100% - 96px);
                    .dailyList-spanBlue {
                      color: #448add;
                    }
                    .dailyList-spanGray {
                      color: #2d3033;
                    }
                    .dailyList-bold {
                      font-weight: 700;
                    }
                    .dailyList-add {
                      display: flex;
                      margin-top: 3px;
                      .dailyList-item {
                        min-width: 13px;
                        margin: -2px 5px 0 0;
                      }
                    }
                  }
                }
                .daily-accordion {
                  padding-top: 8px;
                  .article-head {
                    display: inline-flex;
                    color: #448add;
                    cursor: pointer;
                    position: relative;
                    padding-right: 20px;
                    &.detail {
                      margin-left: 14px;
                    }
                  }
                  .article-body {
                    background: #f7f7f7;
                    box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
                    border-radius: 4px;
                    padding: 8px 14px 12px;
                  }
                  .article-body-nodata {
                    height: 80px;
                  }
                  .daily-article {
                    ul {
                      li {
                        display: flex;
                        flex-wrap: wrap;
                      }
                    }
                    .daily-article-tlt {
                      width: 78px;
                      padding-right: 10px;
                      color: #727f88;
                    }
                    .daily-article-label {
                      width: calc(100% - 78px);
                    }
                    .daily-article-label,
                    .daily-article-text {
                      color: #2d3033;
                    }
                  }
                }
              }
              .viewMore {
                text-align: right;
                width: calc(100% + 47px);
                padding-top: 18px;
                span {
                  font-size: 1rem;
                  padding-right: 20px;
                  background: url(~@/assets/template/images/icon_arrow-right-blue.svg) no-repeat right;
                }
              }
              .dailyList-btnCmt {
                position: absolute;
                top: 12px;
                right: 12px;
                width: 42px;
                .btn {
                  position: relative;
                  .svg {
                    width: 20px;
                    height: initial;
                  }
                  &.btn--active {
                    background: #448add;
                  }
                  &.btn--hasNoti {
                    &::after {
                      position: absolute;
                      top: 9px;
                      right: 9px;
                      content: '';
                      display: block;
                      width: 9px;
                      height: 9px;
                      background: #ea5d54;
                      border-radius: 50%;
                      border: 2px solid #fff;
                    }
                  }
                }
              }
            }
            .dailyList-textarea {
              padding-left: 20px;
              .dailyList-form {
                background: #d1e4ff;
                border-radius: 4px;
                box-shadow: 2px 2px 5px rgba(145, 161, 171, 0.6);
                padding: 16px;
                position: relative;
                white-space: pre-line;
                &::after {
                  position: absolute;
                  top: calc(50% - 14px);
                  left: -14px;
                  content: '';
                  border-top: 14px solid transparent;
                  border-bottom: 14px solid transparent;
                  border-right: 14px solid #d1e4ff;
                }
              }
            }
          }
        }

        .dailyNote {
          background: #b7d4ff;
          border-radius: 4px;
          padding: 7px 12px;
          margin-top: 23px;
          color: #225999;
          position: relative;
          &::after {
            position: absolute;
            top: -12px;
            left: 60px;
            content: '';
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 12px solid #b7d4ff;
          }
        }
      }
    }
  }
}

.title_layer {
  font-weight: bold;
  border-bottom: 1px solid #2d5999;
}

.content_layer {
  margin-top: 5px;
  margin-bottom: 15px;

  .info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    .left {
      display: flex;
      align-items: center;
    }
  }
}

@media (max-width: $viewport_tablet) {
  .navTabs li .navInfo {
    padding: 7px 18px;
  }

  .groupKnow {
    padding-top: 0px;
    .groupKnow-step .dailyStep {
      padding: 8px 24px;
    }
    .knowForm-request {
      padding: 8px 0;
    }

    .groupKnow-btn {
      padding: 15px 24px 15px;
    }

    .knowForm-row .knowForm-group {
      padding-top: 0;
      > ul > li {
        margin-top: 7px;
      }
      .knowForm-label {
        margin-bottom: 1px;
      }
    }

    .timeline-content {
      padding: 10px 24px 10px 20px;
    }

    .dailyList .dailyList-sub li {
      padding: 7px 0 10px;
    }

    .dailyList > ul > li .dailyList-flex .row__custom {
      width: calc(100% - 187px);
      .dailyList-textarea {
        .dailyList-form {
          padding: 10px;
        }

        .dailyList-box {
          padding: 10px 56px 10px 10px;
        }
      }
    }
  }
}

.knowDetail-tlt {
  color: #99a5ae;
}
</style>
