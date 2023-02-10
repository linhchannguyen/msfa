<template>
  <div class="wrapContent contentOpenD1S2">
    <div v-loading="loadingPage" class="briefingSession">
      <div id="briefingSession" ref="contentBriefingRef" class="briefingGroup">
        <div class="briefingHead">
          <div class="sessionNumber">説明会番号：{{ formData.briefing_id }}</div>
          <div v-if="!remand_flag" class="dailyStep dailyStep--blue">
            <!-- Create -->
            <el-steps v-if="!briefing_id_default && !schedule_id_default" class="listSteps" :active="0" align-center>
              <el-step v-for="item in lstStatusType" :key="item.status_type" :title="item.status_type_label">
                <template #title>
                  <p :style="{ color: item.status_type == '10' ? '#5F6B73' : '' }">{{ item.status_type_label }}</p>
                </template>
              </el-step>
            </el-steps>

            <!--  承認済 approve -->
            <el-steps
              v-if="(briefing_id_default || schedule_id_default) && status_check && status_check != 70"
              class="listSteps"
              :active="parseInt(status_check) / 10 - 1"
              align-center
              finish-status="success"
            >
              <el-step
                v-for="item in lstStatusType"
                :key="item.status_type"
                :title="item.status_type_label"
                :style="
                  (item.status_type == '20' && parseInt(status_check) >= 20 && briefing_plan.length > 0) ||
                  (item.status_type == '50' && parseInt(status_check) >= 50 && briefing_result.length > 0)
                    ? 'cursor: pointer;'
                    : 'pointer-events: none;'
                "
                @click="handleClickStatus(`step${item.status_type}`)"
              >
                <template #title>
                  <el-dropdown
                    v-if="
                      (item.status_type == '20' && parseInt(status_check) >= 20 && briefing_plan.length > 0) ||
                      (item.status_type == '50' && parseInt(status_check) >= 50 && briefing_result.length > 0)
                    "
                    trigger="click"
                  >
                    <p
                      :ref="`step${item.status_type}`"
                      :style="{ color: status_check == item.status_type ? '#5F6B73' : '#448add', cursor: 'pointer' }"
                      :class="'icon_warning_status'"
                    >
                      {{ item.status_type_label }}
                      <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                    </p>
                    <template #dropdown>
                      <el-dropdown-menu class="el-dropdown-customDailyReport customDropdownlst">
                        <el-dropdown-item v-for="(itemSub, index) in briefing_comment" :key="index">
                          <div v-if="briefing_comment.length > 1" class="title_layer">{{ itemSub.approval_layer_num }} 次</div>
                          <div class="content_layer">
                            <div v-for="(person, indexSub) in itemSub.data" :key="indexSub" class="info">
                              <div class="left">
                                <img
                                  v-if="
                                    person.approval_layer_num === active_layer_approval &&
                                    person.status_type === '0' &&
                                    !itemSub.data.some((x) => x.status_type == '1' || x.status_type == '2')
                                  "
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
                                    (person.approval_layer_num != active_layer_approval && person.status_type == '0') ||
                                    (person.status_type == '0' && itemSub.data.some((x) => x.status_type == '1' || x.status_type == '2'))
                                      ? 'img-approve-invis'
                                      : ''
                                  "
                                >
                                  {{ person.user_name }}
                                </span>
                              </div>

                              <!-- open modal comment -->
                              <button v-if="person.comment" class="btn-comment" type="button" @click="showModalComment(person)">
                                <img
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/template/images/icon_comment_circle.svg')"
                                  alt="icon_comment"
                                />
                              </button>
                            </div>
                          </div>
                        </el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>

                  <p
                    v-else
                    :style="{
                      color: parseInt(status_check) > parseInt(item.status_type) ? '#448add' : status_check == item.status_type ? '#5F6B73' : ''
                    }"
                  >
                    {{ item.status_type_label }}
                  </p>
                </template>
              </el-step>
            </el-steps>

            <!--  中止 pending -->
            <el-steps
              v-if="(briefing_id_default || schedule_id_default) && status_check && status_check == 70"
              class="listSteps step-gray"
              :active="6"
              align-center
              finish-status="wait"
            >
              <el-step v-for="item in lstStatusType" :key="item.status_type" :title="item.status_type_label">
                <template #title>
                  <p>{{ item.status_type_label }}</p>
                </template>
              </el-step>
            </el-steps>

            <!--  not detail -->
            <el-steps
              v-if="(briefing_id_default || schedule_id_default) && !status_check"
              class="listSteps step-gray"
              finish-status="wait"
              :active="7"
              align-center
            >
              <el-step v-for="item in lstStatusType" :key="item.status_type" :title="item.status_type_label">
                <template #title>
                  <p>{{ item.status_type_label }}</p>
                </template>
              </el-step>
            </el-steps>
          </div>

          <!--  差戻 remand -->
          <div v-else-if="(briefing_id_default || schedule_id_default) && status_check && status_check != 70" class="dailyStep dailyStep--red">
            <el-steps class="listSteps listSteps--red" :active="status_check == '10' ? 0 : 3" align-center finish-status="success">
              <el-step
                v-for="item in lstStatusType"
                :key="item.status_type"
                :class="{ 'hasBox-red': item.status_type == parseInt(status_check) + 10 }"
                :title="item.status_type_label"
                :style="
                  (item.status_type == '20' && parseInt(status_check) >= 10 && briefing_plan.length > 0) ||
                  (item.status_type == '50' && parseInt(status_check) >= 40 && briefing_result.length > 0)
                    ? 'cursor: pointer;'
                    : 'pointer-events: none;'
                "
                @click="handleClickStatus(`step${item.status_type}`)"
              >
                <template #title>
                  <el-dropdown
                    v-if="
                      (item.status_type == '20' && parseInt(status_check) >= 10 && briefing_plan.length > 0) ||
                      (item.status_type == '50' && parseInt(status_check) >= 40 && briefing_result.length > 0)
                    "
                    trigger="click"
                  >
                    <p
                      :ref="`step${item.status_type}`"
                      :class="'icon_warning_status'"
                      :style="{
                        color:
                          parseInt(status_check) >= 40
                            ? item.status_type == '50'
                              ? '#ea5d54'
                              : '#448add'
                            : parseInt(status_check) >= 10
                            ? '#ea5d54'
                            : '#448add',
                        cursor: 'pointer'
                      }"
                    >
                      {{ item.status_type_label }}
                      <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                    </p>
                    <template #dropdown>
                      <el-dropdown-menu class="el-dropdown-customDailyReport customDropdownlst">
                        <el-dropdown-item v-for="(itemSub, index) in briefing_comment" :key="index">
                          <div v-if="briefing_comment.length > 1" class="title_layer">{{ itemSub.approval_layer_num }} 次</div>
                          <div class="content_layer">
                            <div v-for="(person, indexSub) in itemSub.data" :key="indexSub" class="info">
                              <div class="left">
                                <img
                                  v-if="
                                    person.approval_layer_num === active_layer_approval &&
                                    person.status_type === '0' &&
                                    !itemSub.data.some((x) => x.status_type == '1' || x.status_type == '2')
                                  "
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
                                    (person.approval_layer_num != active_layer_approval && person.status_type == '0') ||
                                    (person.status_type == '0' && itemSub.data.some((x) => x.status_type == '1' || x.status_type == '2'))
                                      ? 'img-approve-invis'
                                      : ''
                                  "
                                >
                                  {{ person.user_name }}
                                </span>
                              </div>

                              <!-- open modal comment -->
                              <button v-if="person.comment" class="btn-comment" type="button" @click="showModalComment(person)">
                                <img
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/template/images/icon_comment_circle.svg')"
                                  alt="icon_comment"
                                />
                              </button>
                            </div>
                          </div>
                        </el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>

                  <p
                    v-else
                    :style="{
                      color: parseInt(status_check) > parseInt(item.status_type) ? '#448add' : status_check == item.status_type ? '#5F6B73' : ''
                    }"
                  >
                    {{ item.status_type_label }}
                  </p>
                </template>
              </el-step>
            </el-steps>
          </div>

          <div v-if="remand_flag && (briefing_id_default || schedule_id_default) && status_check && status_check == 70" class="dailyStep dailyStep--blue">
            <el-steps class="listSteps step-gray" :active="6" align-center finish-status="wait">
              <el-step v-for="item in lstStatusType" :key="item.status_type" :title="item.status_type_label">
                <template #title>
                  <p>{{ item.status_type_label }}</p>
                </template>
              </el-step>
            </el-steps>
          </div>
        </div>

        <div class="briefingContent">
          <div class="sessionInput">
            <div class="sessionInput-title">
              <h2 class="tlt">基本情報</h2>
            </div>
            <!-- D -->
            <!-- Mode Edit || Create -->
            <div v-if="lstMode.includes('editMode') || lstMode.includes('createMode')" class="sessionGroup">
              <div class="session-col">
                <div class="sessionForm">
                  <ul>
                    <li class="w-100">
                      <label class="sessionForm-label">
                        説明会名<span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                      </label>
                      <div class="sessionForm-control" :class="checkValidateForm(true, 'briefing_name', '説明会名', 'MSFA0001', 'String', 100) ? 'hasErr' : ''">
                        <el-input v-model="formData.briefing_name" clearable placeholder="内容入力" class="form-control-input" />
                      </div>
                      <span v-if="checkValidateForm(true, 'briefing_name', '説明会名', 'MSFA0001', 'String', 100)" class="invalid">
                        {{ validationMessageForm(true, 'briefing_name', '説明会名', 'MSFA0001', 'String', 100) }}
                      </span>
                    </li>
                    <li>
                      <label class="sessionForm-label"> 品目 </label>
                      <div class="sessionForm-control">
                        <div class="form-icon icon-right">
                          <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="formData.briefing_product.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="product in formData.briefing_product"
                                v-show="product.delete_flag != -1"
                                :key="product.product_cd"
                                class="el-tag-custom el-tag-icon-top"
                                closable
                                @close="handleRemoveProduct(product)"
                              >
                                {{ product.product_name }}
                              </el-tag>
                            </div>
                          </div>

                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="sessionForm-label"> 説明会区分</label>
                      <div class="sessionForm-control">
                        <el-select v-model="formData.briefing_type" placeholder="未選択" class="form-control-select">
                          <el-option value=""> 未選択</el-option>
                          <el-option
                            v-for="item in lstBriefingType"
                            :key="item.briefing_type_value"
                            :label="item.briefing_type_label"
                            :value="item.briefing_type_value"
                          >
                          </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="sessionForm-label">
                        日時<span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                      </label>
                      <div class="sessionForm-control">
                        <ul>
                          <li>
                            <div class="form-icon icon-left form-icon--noBod" :class="checkValidateForm(true, 'start_date', '日', 'MSFA0040') ? 'hasErr' : ''">
                              <span class="icon">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                              </span>
                              <el-date-picker
                                v-model="formData.start_date"
                                type="date"
                                :editable="false"
                                :disabled="checkDisabledDate()"
                                :clearable="false"
                                format="YYYY/M/D"
                                value-format="YYYY/M/D"
                                placeholder="日付選択"
                                class="form-control-datePickerLeft"
                                @change="checkReportApprove()"
                              ></el-date-picker>
                            </div>
                            <span v-if="checkValidateForm(true, 'start_date', '日', 'MSFA0040')" class="invalid">
                              {{ validationMessageForm(true, 'start_date', '日', 'MSFA0040') }}
                            </span>
                          </li>
                          <li :class="checkValidateForm(true, 'start_time', '時', 'MSFA0040') ? 'hasErr' : ''">
                            <TimePicker
                              :key="keyComponentTimepicker"
                              :start-time="formData.start_time"
                              :end-time="formData.end_time"
                              :clearable="false"
                              :disabled="checkDisabledDate()"
                              @onResultTimepicker="onResultTimepicker"
                            ></TimePicker>
                            <span v-if="checkValidateForm(true, 'start_time', '時', 'MSFA0040')" class="invalid">
                              {{ validationMessageForm(true, 'start_time', '時', 'MSFA0040') }}
                            </span>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li>
                      <label class="sessionForm-label">
                        実施者<span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                      </label>
                      <div class="sessionForm-control">
                        <div class="form-icon icon-right">
                          <span class="icon icon--cursor log-icon" title_log="実施者" @click="openModalZ05S01(true)" @touchstart="openModalZ05S01(true)">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="formData.implement_user_name" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveUser()">
                                {{ formData.implement_user_name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                      <span v-if="checkValidateForm(true, 'implement_user_name', '実施者', 'MSFA0040')" class="invalid">
                        {{ validationMessageForm(true, 'implement_user_name', '実施者', 'MSFA0040') }}
                      </span>
                    </li>
                    <li>
                      <label class="sessionForm-label">
                        対象組織
                        <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                      </label>
                      <div class="sessionForm-control">
                        <div class="form-icon icon-right">
                          <span class="icon icon--cursor log-icon" title_log="対象組織" @click="openModalZ05S01(false)" @touchstart="openModalZ05S01(false)">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="formData.target_org_label" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveTargetOrg()">
                                {{ formData.target_org_label }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                      <span v-if="checkValidateForm(true, 'target_org_label', '対象組織', 'MSFA0040')" class="invalid">
                        {{ validationMessageForm(true, 'target_org_label', '対象組織', 'MSFA0040') }}
                      </span>
                    </li>
                    <li>
                      <label class="sessionForm-label"> 対象施設 </label>
                      <div class="sessionForm-control">
                        <div class="form-icon icon-right">
                          <span class="icon icon--cursor log-icon" title_log="対象施設" @click="openModalZ05S03()">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <div v-if="formData.facility_short_name" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveFacility()">
                                {{ formData.facility_short_name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="sessionForm-label"> 対象診療科目分類 </label>
                      <div class="sessionForm-control">
                        <el-select v-model="formData.medical_subjects_group_cd" placeholder="未選択" class="form-control-select" @change="changeMedical()">
                          <el-option :value="''">未選択</el-option>
                          <el-option
                            v-for="item in lstMedicalSubjectsGroup"
                            :key="item.medical_subjects_group_cd"
                            :label="item.medical_subjects_group_name"
                            :value="item.medical_subjects_group_cd"
                          >
                          </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="sessionForm-label"> 場所 </label>
                      <div class="sessionForm-control">
                        <el-input v-model="formData.place" clearable placeholder="内容入力" class="form-control-input" />
                        <span v-if="checkValidateForm(false, 'place', '場所', null, 'String', 255)" class="invalid">
                          {{ validationMessageForm(false, 'place', '場所', null, 'String', 255) }}
                        </span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="session-col">
                <div class="sessionForm">
                  <ul>
                    <li class="w-100">
                      <label class="sessionForm-label"> 内容詳細 </label>
                      <div class="sessionForm-control">
                        <el-input v-model="formData.content" clearable class="form-control-textarea" :rows="8" type="textarea" placeholder="内容入力" />
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="materials">
                  <div class="materials-title">
                    <h3 class="tlt">資材</h3>
                    <button
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                      @click="openModalZ05S05()"
                      @touchstart="openModalZ05S05()"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" /> </span
                      >追加
                    </button>
                  </div>
                  <div class="materialsBox">
                    <div ref="documentRef" class="materialsLst scrollbar">
                      <ul v-if="formData.briefing_document.filter((x) => x.delete_flag != -1).length > 0">
                        <li v-for="item in formData.briefing_document" v-show="item.delete_flag != -1" :key="item.document_id">
                          <div class="materialsLst-txt">
                            <a class="title-link" @click="redirectToD01S02(item)">
                              <span class="materialsLst-item">
                                <img
                                  v-if="item.document_type == '10' && item.file_type == '10'"
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/template/images/icon_pdf-manag.svg')"
                                  alt=""
                                />
                                <img
                                  v-if="item.document_type == '10' && item.file_type == '20'"
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/template/images/icon_film.svg')"
                                  alt=""
                                />
                                <img
                                  v-if="item.document_type == '20'"
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/images/icon_document_spanner.svg')"
                                  alt=""
                                />
                              </span>
                              {{ item.document_name }}
                            </a>
                            <span v-if="checkOutsideValid(item.start_date, item.end_date)" class="spanLabel">適用期間外</span>
                          </div>
                          <div class="materialsLst-btn">
                            <button class="btn btn--icon" data-toggle="dropdown" aria-expanded="false">
                              <span></span>
                              <span></span>
                              <span></span>
                            </button>
                            <div class="dropdown-menu dropdown-tools">
                              <span class="btn-show">
                                <span></span>
                                <span></span>
                                <span></span>
                              </span>
                              <ul>
                                <li @click="openModalMaterialViewerD01S07(item)" @touchstart="openModalMaterialViewerD01S07(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_slideshow01.svg')" alt="" />
                                  </span>
                                  <span class="text-label">ビューア</span>
                                </li>
                                <li @click="deleteMaterial(item)" @touchstart="deleteMaterial(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" />
                                  </span>
                                  <span class="text-label">削除</span>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </li>
                      </ul>
                      <EmptyData v-else-if="!loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mode View || approvalMode -->
            <div v-if="(lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode')" class="sessionGroup">
              <div class="session-col">
                <div class="sessionForm">
                  <ul>
                    <li class="w-100">
                      <label class="sessionContent-label"> 説明会名 </label>
                      <div class="sessionContent-value">{{ formData.briefing_name }}</div>
                    </li>
                    <li>
                      <label class="sessionContent-label"> 品目 </label>
                      <div class="sessionContent-value">
                        <p>{{ formData?.briefing_product?.map((x) => x.product_name)?.join(', ') }}</p>
                      </div>
                    </li>
                    <li>
                      <label class="sessionContent-label"> 説明会区分 </label>
                      <div class="sessionContent-value">
                        {{ lstBriefingType.find((x) => x.briefing_type_value === formData.briefing_type)?.briefing_type_label }}
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="sessionContent-label"> 日時 </label>
                      <div class="sessionContent-value">
                        {{ formatFullDate(formData.start_date) }}　 {{ formData.start_time }} {{ formData.start_time ? ' ～ ' : '' }} {{ formData.end_time }}
                      </div>
                    </li>
                    <li>
                      <label class="sessionContent-label"> 実施者 </label>
                      <div class="sessionContent-value">
                        {{ formData.implement_user_name }}
                      </div>
                    </li>
                    <li>
                      <label class="sessionContent-label"> 対象組織 </label>
                      <div class="sessionContent-value">
                        {{ formData.target_org_label }}
                      </div>
                    </li>
                    <li>
                      <label class="sessionContent-label"> 対象施設 </label>
                      <div class="sessionContent-value">
                        {{ formData.facility_short_name }}
                      </div>
                    </li>
                    <li>
                      <label class="sessionContent-label"> 対象診療科目分類 </label>
                      <div class="sessionContent-value">
                        {{
                          lstMedicalSubjectsGroup.find((x) => x.medical_subjects_group_cd === formData.medical_subjects_group_cd)?.medical_subjects_group_name
                        }}
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="sessionContent-label"> 場所 </label>
                      <div class="sessionContent-value">
                        {{ formData.place }}
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="session-col">
                <div class="sessionForm">
                  <ul>
                    <li class="w-100">
                      <label class="sessionContent-label"> 内容詳細 </label>
                      <div
                        class="sessionContent-value content_item_scroll scrollbar scroll-child"
                        @mouseenter="childFocus"
                        @mouseleave="childFocusOut"
                        @touchend="childFocusOut"
                        @touchstart="childFocus"
                      >
                        {{ formData.content }}
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="materials">
                  <div class="materials-title">
                    <label class="sessionContent-label">資材</label>
                  </div>
                  <div class="materialsBox">
                    <div class="materialsLst scrollbar">
                      <ul v-if="formData.briefing_document.filter((x) => x.delete_flag != -1).length > 0">
                        <li v-for="item in formData.briefing_document" :key="item.document_id">
                          <div class="materialsLst-txt">
                            <a class="title-link" @click="redirectToD01S02(item)">
                              <span class="materialsLst-item">
                                <img
                                  v-if="item.document_type == '10' && item.file_type == '10'"
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/template/images/icon_pdf-manag.svg')"
                                  alt=""
                                />
                                <img
                                  v-if="item.document_type == '10' && item.file_type == '20'"
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/template/images/icon_film.svg')"
                                  alt=""
                                />
                                <img
                                  v-if="item.document_type == '20'"
                                  v-svg-inline
                                  svg-inline
                                  class="svg"
                                  :src="require('@/assets/images/icon_document_spanner.svg')"
                                  alt=""
                                />
                              </span>
                              {{ item.document_name }}
                            </a>
                            <span v-if="checkOutsideValid(item.start_date, item.end_date)" class="spanLabel">適用期間外</span>
                          </div>
                        </li>
                      </ul>
                      <EmptyData v-else-if="!loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- D-18 -> D-30 || E -->
          <div class="sessionInput">
            <div class="sessionInput-title sessionInput-title--change">
              <h2 class="tlt">出席者</h2>
            </div>
            <div class="sessionGroup">
              <!-- D-18 -> D-30 -->
              <div class="session-col">
                <div :class="['attendees-title', !(lstMode.includes('editMode') || lstMode.includes('createMode')) ? 'flex_col' : '']">
                  <h3 class="attendees-tlt">出席者一覧</h3>
                  <div class="attendees-btn">
                    <button
                      v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                      @click="openModalAddPersonOther(false)"
                      @touchstart="openModalAddPersonOther(false)"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" /> </span
                      >その他個人追加
                    </button>
                    <button
                      v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                      @click="openModalZ05S04()"
                      @touchstart="openModalZ05S04()"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" /> </span
                      >施設個人追加
                    </button>
                    <button
                      v-if="permission?.includes('R10') || permission?.includes('R90')"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                      @click="openModalStock()"
                      @touchstart="openModalStock()"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack02.svg')" alt="" /> </span
                      >ストック登録
                    </button>
                  </div>
                </div>
                <div class="attendeesBox">
                  <div class="attendeesLst scrollbar">
                    <ul v-if="formData.briefing_attendee.filter((x) => x.delete_flag != -1).length > 0">
                      <li v-for="(person, index) in formData.briefing_attendee" v-show="person.delete_flag != -1" :key="person.person_cd">
                        <div v-if="person.other_person_flag == 0" class="attendeesLst-info">
                          <p class="attendeesLst-tlt">{{ person.facility_short_name }} {{ person.department_name }}</p>
                          <p class="attendeesLst-txt">
                            <a class="attendeesLst-link" @click="redirectToH02S02(person)">{{ person.person_name }} </a>　
                            <span class="attendeesLst-label">{{ person.display_position_name }}</span>
                          </p>
                        </div>

                        <div v-if="person.other_person_flag == 1" class="attendeesLst-info">
                          <p class="attendeesLst-tlt">{{ person.facility_short_name }}</p>
                          <p class="attendeesLst-txt">
                            <span class="attendeesLst-name"> {{ person.person_name }} </span>　
                            <span class="attendeesLst-label">{{ person.display_position_name }}</span>
                          </p>
                        </div>
                        <div v-if="lstMode.includes('editMode') || lstMode.includes('createMode')" class="attendeesLst-btn">
                          <button
                            v-if="person.other_person_flag == 1"
                            type="button"
                            class="btn btn--icon"
                            @click="openModalAddPersonOther(true, person, index)"
                          >
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pen01.svg')" alt="" />
                          </button>
                          <button class="btn btn--icon" type="button" @click="removePerson(person, index)">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                          </button>
                        </div>
                      </li>
                    </ul>
                    <EmptyData v-else-if="!loadingPage" icon="amico_A01S03" custom-class="nodata nodata500" />
                  </div>
                </div>
              </div>

              <!-- E -->
              <div class="session-col">
                <div class="attendees-title">
                  <h3 class="attendees-tlt">出席者数</h3>
                </div>
                <div class="attendeesTbl scrollbar">
                  <table>
                    <thead>
                      <tr>
                        <th></th>
                        <th>計画</th>
                        <th>結果</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in dataNumberChange.briefing_attendee_count"
                        v-show="formData.briefing_attendee_count.length > 0"
                        :key="item.occupation_type"
                      >
                        <td>{{ item.occupation_name }}</td>
                        <td :class="status_check == 30 || status_check == 40 || (!briefing_id_default && isInputResult) ? 'attendeesTbl-people' : ''">
                          <el-input
                            v-if="
                              ((!briefing_id_default && !isInputResult) || status_check == 10) &&
                              (lstMode.includes('editMode') || lstMode.includes('createMode'))
                            "
                            :ref="`plan_headcount_${index}`"
                            v-model="item.plan_headcount"
                            class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                            :maxlength="maxlengthAmout"
                            onkeydown="
                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                "
                            @blur="
                              formatCurrency(
                                $event,
                                'briefing_attendee_count',
                                'plan_headcount',
                                index,
                                'plan',
                                'attendees',
                                maxlengthAmout,
                                `plan_headcount_${index}`,
                                '人'
                              )
                            "
                            @focus="focusInput(`plan_headcount_${index}`, '人')"
                          >
                            <template #suffix> {{ item.plan_headcount ? '人' : '0 人' }} </template>
                          </el-input>
                          <span v-else class="formCol-text"> {{ item.plan_headcount || 0 }} 人 </span>
                        </td>
                        <td :class="(!briefing_id_default && !isInputResult) || status_check == 10 ? 'attendeesTbl-people' : ''">
                          <el-input
                            v-if="
                              (status_check == 30 || status_check == 40 || (!briefing_id_default && isInputResult)) &&
                              (lstMode.includes('editMode') || lstMode.includes('createMode'))
                            "
                            :ref="`result_headcount_${index}`"
                            v-model="item.result_headcount"
                            :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix 
                             ${item.result_headcount != item.plan_headcount && Number(status_check) >= 40 ? 'color-number--red ' : ''}`"
                            :maxlength="maxlengthAmout"
                            onkeydown="
                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                "
                            @blur="
                              formatCurrency(
                                $event,
                                'briefing_attendee_count',
                                'result_headcount',
                                index,
                                'result',
                                'attendees',
                                maxlengthAmout,
                                `result_headcount_${index}`,
                                '人'
                              )
                            "
                            @focus="focusInput(`result_headcount_${index}`, '人')"
                          >
                            <template #suffix> {{ item.result_headcount ? '人' : '0 人' }} </template>
                          </el-input>

                          <span
                            v-else
                            class="formCol-text"
                            :class="item.result_headcount != item.plan_headcount && Number(status_check) >= 40 ? 'color-number--red' : ''"
                          >
                            {{ item.result_headcount || 0 }} 人
                          </span>
                        </td>
                      </tr>
                      <tr v-if="formData.briefing_attendee_count.length == 0">
                        <td :colspan="3">
                          <EmptyData v-if="!loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                        </td>
                      </tr>
                    </tbody>
                    <tfoot v-if="formData.briefing_attendee_count.length > 0">
                      <tr class="attendeesTbl-footer">
                        <td>合計</td>
                        <td
                          :class="
                            status_check == 30 || status_check == 40 || (!briefing_id_default && isInputResult)
                              ? 'attendeesTbl-people'
                              : lstMode.includes('editMode') || lstMode.includes('createMode')
                              ? ''
                              : 'attendeesTbl-view'
                          "
                        >
                          {{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(sumPlanHeadcount) }} 人
                        </td>
                        <td
                          :class="
                            (!briefing_id_default && !isInputResult) || status_check == 10
                              ? `attendeesTbl-people ${sumResultHeadcount != sumPlanHeadcount && Number(status_check) >= 40 ? 'color-number--red ' : ''}`
                              : lstMode.includes('editMode') || lstMode.includes('createMode')
                              ? `${sumResultHeadcount != sumPlanHeadcount && Number(status_check) >= 40 ? 'color-number--red ' : ''}`
                              : `attendeesTbl-view  ${sumResultHeadcount != sumPlanHeadcount && Number(status_check) >= 40 ? 'color-number--red ' : ''}`
                          "
                        >
                          {{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(sumResultHeadcount) }} 人
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- F | G -->
          <div class="sessionInput">
            <div class="sessionInput-title sessionInput-title--change">
              <h2 class="tlt">経費</h2>
            </div>
            <div class="expenses">
              <!-- F -->
              <div class="expensesTbl scrollbar">
                <table>
                  <thead>
                    <tr>
                      <th></th>
                      <th>計画</th>
                      <th>結果</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="formData.briefing_expense_item.length > 0">
                      <td></td>
                      <td>
                        <div class="expensesTbl-col">
                          <ul>
                            <li>単価</li>
                            <li>個数</li>
                          </ul>
                        </div>
                      </td>
                      <td>
                        <div class="expensesTbl-col">
                          <ul>
                            <li>単価</li>
                            <li>個数</li>
                          </ul>
                        </div>
                      </td>
                    </tr>

                    <tr
                      v-for="(item, index) in dataNumberChange.briefing_expense_item"
                      v-show="dataNumberChange.briefing_expense_item.length > 0"
                      :key="item.expense_item_cd"
                    >
                      <td>{{ item.expense_item_name }}</td>
                      <td>
                        <div
                          :class="status_check == 30 || status_check == 40 || (!briefing_id_default && isInputResult) ? 'expensesTbl-text' : ''"
                          class="expensesTbl-col"
                        >
                          <ul>
                            <li>
                              <el-input
                                v-if="
                                  ((!briefing_id_default && !isInputResult) || status_check == 10) &&
                                  item.unit_price_input_flag &&
                                  (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                "
                                :ref="`plan_unit_price_${index}`"
                                v-model="item.plan_unit_price"
                                :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix`"
                                :maxlength="maxlengthAmout"
                                onkeydown="
                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                "
                                @blur="
                                  formatCurrency(
                                    $event,
                                    'briefing_expense_item',
                                    'plan_unit_price',
                                    index,
                                    'plan',
                                    'expenses',
                                    maxlengthAmout,
                                    `plan_unit_price_${index}`,
                                    '円'
                                  )
                                "
                                @focus="focusInput(`plan_unit_price_${index}`, '円')"
                              >
                                <template #suffix>
                                  {{ item.plan_unit_price ? '円' : '0 円' }}
                                </template>
                              </el-input>

                              <span v-else class="formCol-text"> {{ item.plan_unit_price || 0 }} 円 </span>
                            </li>
                            <li>
                              <div v-if="item.quantity_input_flag">
                                <el-input
                                  v-if="
                                    ((!briefing_id_default && !isInputResult) || status_check == 10) &&
                                    item.quantity_input_flag &&
                                    (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                  "
                                  :ref="`plan_quantity_${index}`"
                                  v-model="item.plan_quantity"
                                  :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix`"
                                  :maxlength="maxlengthQuantity"
                                  onkeydown="
                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                "
                                  @blur="
                                    formatCurrency(
                                      $event,
                                      'briefing_expense_item',
                                      'plan_quantity',
                                      index,
                                      'plan',
                                      'expenses',
                                      maxlengthQuantity,
                                      `plan_quantity_${index}`,
                                      `${item.quantity_unit_label ? item.quantity_unit_label : '個'}`
                                    )
                                  "
                                  @focus="focusInput(`plan_quantity_${index}`, item.quantity_unit_label ? item.quantity_unit_label : '個')"
                                >
                                  <template #suffix>
                                    {{ item.plan_quantity ? item.quantity_unit_label : `0 ${item.quantity_unit_label ? item.quantity_unit_label : '個'}` }}
                                  </template>
                                </el-input>

                                <span v-else class="formCol-text"> {{ item.plan_quantity || 0 }} {{ item.quantity_unit_label }} </span>
                              </div>
                            </li>
                            <li>{{ item.plan_amount || 0 }} 円</li>
                          </ul>
                        </div>
                      </td>
                      <td>
                        <div class="expensesTbl-col" :class="(!briefing_id_default && !isInputResult) || status_check == 10 ? 'expensesTbl-text' : ''">
                          <ul>
                            <li>
                              <el-input
                                v-if="
                                  (status_check == 30 || status_check == 40 || (!briefing_id_default && isInputResult)) &&
                                  item.unit_price_input_flag &&
                                  (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                "
                                :ref="`result_unit_price_${index}`"
                                v-model="item.result_unit_price"
                                :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix ${
                                  item.result_unit_price != item.plan_unit_price && Number(status_check) >= 40 ? 'color-number--red ' : ''
                                }`"
                                :maxlength="maxlengthAmout"
                                onkeydown="
                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                "
                                @blur="
                                  formatCurrency(
                                    $event,
                                    'briefing_expense_item',
                                    'result_unit_price',
                                    index,
                                    'result',
                                    'expenses',
                                    maxlengthAmout,
                                    `result_unit_price_${index}`,
                                    '円'
                                  )
                                "
                                @focus="focusInput(`result_unit_price_${index}`, '円')"
                              >
                                <template #suffix>
                                  {{ item.result_unit_price ? '円' : '0 円' }}
                                </template>
                              </el-input>

                              <span
                                v-else
                                class="formCol-text"
                                :class="item.result_unit_price != item.plan_unit_price && Number(status_check) >= 40 ? 'color-number--red' : ''"
                              >
                                {{ item.result_unit_price || 0 }} 円
                              </span>
                            </li>
                            <li>
                              <div v-if="item.quantity_input_flag">
                                <el-input
                                  v-if="
                                    (status_check == 30 || status_check == 40 || (!briefing_id_default && isInputResult)) &&
                                    item.quantity_input_flag &&
                                    (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                  "
                                  :ref="`result_quantity_${index}`"
                                  v-model="item.result_quantity"
                                  :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix ${
                                    item.result_quantity != item.plan_quantity && Number(status_check) >= 40 ? 'color-number--red ' : ''
                                  }`"
                                  :maxlength="maxlengthQuantity"
                                  onkeydown="
                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                "
                                  @blur="
                                    formatCurrency(
                                      $event,
                                      'briefing_expense_item',
                                      'result_quantity',
                                      index,
                                      'result',
                                      'expenses',
                                      maxlengthQuantity,
                                      `result_quantity_${index}`,
                                      item.quantity_unit_label
                                    )
                                  "
                                  @focus="focusInput(`result_quantity_${index}`, item.quantity_unit_label)"
                                >
                                  <template #suffix>
                                    {{ item.result_quantity ? item.quantity_unit_label : `0 ${item.quantity_unit_label}` }}
                                  </template>
                                </el-input>

                                <span
                                  v-else
                                  class="formCol-text"
                                  :class="item.result_quantity != item.plan_quantity && Number(status_check) >= 40 ? 'color-number--red' : ''"
                                >
                                  {{ item.result_quantity || 0 }} {{ item.quantity_unit_label }}
                                </span>
                              </div>
                            </li>
                            <li :class="item.result_amount != item.plan_amount && Number(status_check) >= 40 ? 'color-number--red' : ''">
                              {{ item.result_amount || 0 }} 円
                            </li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="formData.briefing_expense_item.length == 0">
                      <td :colspan="3">
                        <EmptyData v-if="!loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                      </td>
                    </tr>
                  </tbody>
                  <tfoot v-if="formData.briefing_expense_item.length > 0">
                    <tr>
                      <td>合計</td>
                      <td>
                        <span
                          :class="
                            status_check == 30 || status_check == 40 || (!briefing_id_default && isInputResult) ? 'expensesTbl-circle' : 'expensesTbl-total'
                          "
                        >
                          {{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(sumPlanAmountExpense) }} 円
                        </span>
                      </td>
                      <td>
                        <span
                          :class="
                            (!briefing_id_default && !isInputResult) || status_check == 10
                              ? `expensesTbl-circle  ${sumResultAmountExpense != sumPlanAmountExpense && Number(status_check) >= 40 ? 'color-number--red' : ''}`
                              : `expensesTbl-total ${sumResultAmountExpense != sumPlanAmountExpense && Number(status_check) >= 40 ? 'color-number--red' : ''}`
                          "
                        >
                          {{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(sumResultAmountExpense) }} 円
                        </span>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <!-- G -->
              <div v-if="lstMode.includes('editMode') || lstMode.includes('createMode')">
                <div v-if="parseInt(status_check) > 30" class="bento">
                  <label class="bento-label">弁当処分方法</label>
                  <div class="bento-select">
                    <ul>
                      <li v-for="item in lstBentoDisposal?.filter((x) => x.bento_disposal_value)" :key="item.bento_disposal_value">
                        <button
                          :class="item.selected ? 'active' : ''"
                          class="btn btn-select"
                          type="button"
                          @click="setSelectedBentoDisposal(item.bento_disposal_value)"
                        >
                          {{ item.bento_disposal_label }}
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="expensesForm">
                  <ul>
                    <li v-if="parseInt(status_check) > 30">
                      <label class="expensesForm-label">差異理由および今後の対策</label>
                      <div class="expensesForm-control">
                        <el-input v-model="formData.reason" clearable class="form-control-textarea" :rows="6" type="textarea" placeholder="内容入力" />
                      </div>
                    </li>
                    <li>
                      <label class="expensesForm-label">特記事項</label>
                      <div class="expensesForm-control">
                        <el-input v-model="formData.note" clearable class="form-control-textarea" :rows="6" type="textarea" placeholder="内容入力" />
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              <div v-else>
                <div v-if="parseInt(status_check) > 30" class="bento">
                  <label class="bento-label sessionContent-label">弁当処分方法</label>
                  <div class="bento-select">
                    <p>{{ lstBentoDisposal.find((x) => x.bento_disposal_value === formData.disposal_technique)?.bento_disposal_label }}</p>
                  </div>
                </div>
                <div class="expensesForm">
                  <ul>
                    <li v-if="parseInt(status_check) > 30">
                      <label class="expensesForm-label sessionContent-label">差異理由および今後の対策</label>
                      <div
                        class="content_item_scroll scrollbar scroll-child pr-3"
                        @mouseenter="childFocus"
                        @mouseleave="childFocusOut"
                        @touchend="childFocusOut"
                        @touchstart="childFocus"
                      >
                        {{ formData.reason }}
                      </div>
                    </li>
                    <li>
                      <label class="expensesForm-label sessionContent-label">特記事項</label>
                      <div
                        class="content_item_scroll scrollbar scroll-child pr-3"
                        @mouseenter="childFocus"
                        @mouseleave="childFocusOut"
                        @touchend="childFocusOut"
                        @touchstart="childFocus"
                      >
                        {{ formData.note }}
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="expensesBtn">
              <ElementButton
                :key="keyComponentButton"
                :form-data="formData"
                :old-form-data="oldFormData"
                :permissions="{
                  policyRole: roleUser,
                  isUserApprover: isUserApprover,
                  isUserPlaner: isUserPlaner,
                  status_check: status_check,
                  lstMode: lstMode,
                  active_layer_approval: active_layer_approval,
                  document_usage_type: document_usage_type,
                  request_type_plan: request_type_plan,
                  request_type_result: request_type_result
                }"
                @reload="reloadData"
                @submit="submitButtonClick"
                @loadingTrue="loadingTrue"
                @loadingFalse="loadingFalse"
                @scrollTop="scrollTop"
                @back="goToBack"
              ></ElementButton>
            </div>
          </div>
          <!-- sessionInput -->
        </div>
      </div>
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :show-close="modalConfig.isShowClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      @close="onCloseModalStock()"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="saveData"></component>
      </template>
    </el-dialog>

    <!-- dialog confirm save-->
    <el-dialog
      v-model="modalConfig.isShowModalConfirmSave"
      custom-class="custom-dialog confirm-dialog-custom"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :show-close="modalConfig.isShowClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
    >
      <div>
        <div class="content">
          <img :src="require(`@/assets/template/images/icon_alert.svg`)" alt="iconAlert" />
          <div class="message">
            <h3 v-if="formData.briefing_id" class="title">変更されている項目があります。</h3>
            <p class="title">このこの講演会を保存しますか？</p>
          </div>

          <div class="confirm-btn">
            <button type="button" class="btn btn-outline-primary mr-2">いいえ</button>
            <button type="button" class="btn btn-primary">はい</button>
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
/* eslint-disable eqeqeq */
/* eslint-disable indent */
import { markRaw } from 'vue';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05S03FacilitySelection from '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection';
import Z05_S05_ChoiceOfMasterial from '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import D01_S07_MaterialViewer from '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer';
import D01_S02_ModalMaterialDetails from '@/views/D01/D01_S02_MaterialDetails/D01_S02_ModalMaterialDetails';
import Z05S04FacilityCustomerSelection from '@/views/Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection';
import B01_S02_ModalAddPersonOther from './B01_S02_ModalAddPersonOther.vue';
import B01_S02_ModalPersonStock from './B01_S02_ModalPersonStock.vue';
import ElementButton from './B01_S02_ElementButton.vue';
import B01_S02_ModalComment from './B01_S02_ModalComment.vue';

import B01_S02_BriefingSessionInputService from '@/api/B01/B01_S02_BriefingSessionInputService';
import { isEqual, chain } from 'lodash';
import { Auth } from '@/api';
import PerfectScrollbar from 'perfect-scrollbar';

export default {
  name: 'B01_S02_BriefingSessionInput',
  components: { ElementButton },
  props: {
    briefing_id: {
      type: String,
      require: false,
      default: ''
    },

    // redirect from  schedule
    schedule_id: {
      type: String,
      require: false,
      default: ''
    },

    // redirect from schedule: create schedule
    start: {
      type: String,
      require: false,
      default: ''
    },
    allDay: {
      type: String,
      require: false,
      default: ''
    },
    checkLoading: [Boolean]
  },
  data: function () {
    return {
      loadingPage: false,
      isSaveBeforeStock: false,
      psContainer: null,

      maxlengthAmout: 8,
      maxlengthQuantity: 4,

      paramZ05S04: {
        non_facility_flag: 0, // req
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
        facility_name: [],
        person_cd: []
      },

      paramsZ05S03: {
        selectGroupCd: '',
        facility_cd: [],
        facility_name: [],
        org_cd: '',
        user_cd: '',
        user_name: ''
      },

      lstStatusType: [], // A-2

      // check mode
      briefing_id_default: '',
      schedule_id_default: '',
      roleUser: [],
      isUserApprover: false,
      isUserPlaner: false,
      remand_flag: false,
      status_check: '',
      endDateInputBriefing: '',
      lstMode: [],
      keyComponentButton: Math.random(),
      keyComponentTimepicker: Math.random(),
      copyFlag: false,
      isSubmitBtn: false,

      isSelectUser: false, // open Z05-S01 mode user

      showBtnSave: false,

      status_report_approval: false,
      active_layer_approval: 1,
      layer_user_approval: [],

      request_type_plan: '',
      request_type_result: '',

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalConfirmInput: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        isShowClose: true,
        closeOnClickMark: false
      },

      paramsZ05S05: {
        subMaterialSelectableFlag: 0, // 1: allow select child material || 0: not allow  (require)
        customMaterialFlag: 1, // 1: allow choice カスタム資材 || 0: not allow (require)
        initMaterials: [],
        materialType: '10',
        document_name: '',
        productCode: '',
        medicalSubjectsGroupCode: '',
        safetyInformationFlag: '',
        offLabelInformationFlag: '',
        date: null
      },

      paramsZ05S06: {
        mode: 'multiple',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      paramsZ05S01: {
        userFlag: 0,
        mode: 'single',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: []
      },

      // B,C
      briefing_plan: [],
      briefing_result: [],
      briefing_comment: [],

      document_usage_type: null,

      // D
      lstMedicalSubjectsGroup: [],
      lstBriefingType: [],
      lstMaterialsDefault: [],
      lstProductDefault: [],
      lstPersonDefault: [],

      // E
      sumPlanHeadcount: 0,
      sumResultHeadcount: 0,

      // F
      sumPlanAmountExpense: 0,
      sumResultAmountExpense: 0,

      // I
      lstOtherMedicalStaff: [],

      // G
      lstBentoDisposal: [],

      indexEditPerson: null,

      userCreated: '',

      oldFormData: {},
      formData: {
        // D
        briefing_id: '',
        briefing_name: '',
        briefing_type: '',
        content: '',
        start_date: '',
        start_time: '',
        end_time: '',
        status_type: '',
        implement_user_org_cd: '',
        implement_user_org_label: '',
        implement_user_cd: '',
        implement_user_name: '',
        implement_user_post: '',
        implement_user_post_name: '',
        target_org_cd: '',
        target_org_label: '',
        facility_short_name: '',
        facility_cd: '',
        medical_subjects_group_cd: '',
        medical_subjects_group_name: '',
        place: '',
        briefing_document: [],
        briefing_product: [],
        briefing_attendee: [],

        // E
        briefing_attendee_count: [],

        //F
        briefing_expense_item: [],

        // G
        disposal_technique: '',
        reason: '',
        note: '',

        schedule_id: ''
      },

      dataNumberChange: {},

      router: null,
      permission: null,

      preTop: 0,
      isScroll: false,
      stepType: '',
      currentUser: null,
      isInputResult: false
    };
  },

  created() {
    this.currentUser = this.getCurrentUser();
    this.checkPermission();
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: '説明会入力',
      icon: 'icon_box_color.svg',
      isShowBack: true
    });

    this.router = this._route('B01_S02_BriefingSessionInput')?.params;

    if (this.router?.briefing_id) {
      this.briefing_id_default = this.router?.briefing_id;
    }

    if (this.router?.schedule_id) {
      this.schedule_id_default = this.router?.schedule_id;
    }

    if (this.router?.start) {
      this.schedule_id_default = '';
      let time = this.router?.start;
      let endDate = new Date(new Date(time).setHours(new Date(time).getHours() + 1));

      if (this.router?.allDay) {
        endDate = new Date(new Date(time).setHours(23, 59, 59, 0));
      }

      this.formData.start_date = this.formatFullDate(time);
      this.formData.start_time = this.formatTimeHourMinute(time);
      this.formData.end_time = this.formatTimeHourMinute(endDate);

      this.keyComponentTimepicker = Math.random();
    }

    this.paramsZ05S03 = {
      ...this.paramsZ05S03,
      org_cd: this.currentUser.org_cd,
      user_cd: this.currentUser.user_cd,
      user_name: this.currentUser.user_name
    };

    this.paramZ05S04 = {
      ...this.paramZ05S04,
      user_cd: this.currentUser.user_cd,
      user_name: this.currentUser.user_name
    };

    this.psContainer = new PerfectScrollbar('#briefingSession', {
      wheelSpeed: 0.5
    });
  },

  updated() {
    this.$nextTick(() => {
      if (!isEqual(this.formData, this.oldFormData)) {
        localStorage.setItem('checkChangTab', true);
      } else {
        localStorage.removeItem('checkChangTab');
      }
    });
  },

  methods: {
    focusInput(refName, label, checkCurrent, currency) {
      let el = this.$refs[refName][0].$el;
      let item = el.querySelector('.el-input__suffix-inner');
      item.innerHTML = label;

      let itemValue = el.querySelector('.el-input__inner');
      let value = itemValue.value;

      if (checkCurrent) {
        itemValue.value = currency;
      } else {
        itemValue.value = value ? value.toString().replace(/[^0-9.-]+/g, '') : '';
      }
    },

    isFullSizeCharacter(value) {
      for (let i = 0; i < value?.length; i++) {
        const code = value.charCodeAt(i);
        if ((code >= 0x0020 && code <= 0x1fff) || (code >= 0xff61 && code <= 0xff9f)) {
          return false;
        }
      }
      return !!(value && 'string' === typeof value) && true;
    },

    formatCurrency(event, data, field, index, type, category, maxLength, refName, label) {
      let value = event.target.value;
      let num = '';
      num = this.convertToHalf(value);
      num = Number(num.replace(/[^0-9.-]+/g, ''))
        ?.toString()
        ?.substring(0, maxLength);

      let number = num;
      let currency = num;

      currency = this.convertNumberToCurrency(currency);

      this.dataNumberChange[data][index][field] = currency;

      this.focusInput(refName, currency ? label : `0 ${label}`, true, currency);

      number = Number(number.replace(/[^0-9.-]+/g, ''));

      if (this.formData[data][index][field] != number) {
        this.formData[data][index][field] = number;

        if (category == 'attendees') {
          if (type === 'plan') {
            this.changeAttendeePlan();
          } else {
            this.changeAttendeeResult();
          }
        } else {
          if (type === 'plan') {
            this.changeAmountExpensePlan(index);
          } else {
            this.changeAmountExpenseResult(index);
          }
        }
      }
    },

    convertNumberToCurrency(number) {
      if (!number || number == '0') return '';
      let currency = number
        ?.toString()
        ?.replace(/\$\s?|(,*)/g, '')
        ?.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

      return currency;
    },

    convertCurrencyDataDefault(datas) {
      let data = { ...datas };
      for (let index = 0; index < data.briefing_attendee_count.length; index++) {
        let attend = data.briefing_attendee_count[index];

        attend.plan_headcount = this.convertNumberToCurrency(attend.plan_headcount);
        attend.result_headcount = this.convertNumberToCurrency(attend.result_headcount);
        data.briefing_attendee_count[index] = attend;
      }

      for (let index = 0; index < data.briefing_expense_item.length; index++) {
        let expense = data.briefing_expense_item[index];

        expense.plan_amount = this.convertNumberToCurrency(expense.plan_amount);
        expense.plan_quantity = this.convertNumberToCurrency(expense.plan_quantity);
        expense.plan_unit_price = this.convertNumberToCurrency(expense.plan_unit_price);

        expense.result_amount = this.convertNumberToCurrency(expense.result_amount);
        expense.result_quantity = this.convertNumberToCurrency(expense.result_quantity);
        expense.result_unit_price = this.convertNumberToCurrency(expense.result_unit_price);

        data.briefing_expense_item[index] = expense;
      }

      return data;
    },

    async checkPermission() {
      this.loadingPage = true;
      let user_cd = this.currentUser.user_cd;
      await Auth.getPolicyRoleService({ user_cd })
        .then((res) => {
          this.permission = res.data?.data;
          this.getScreenDataInfo();
        })
        .catch(() => {
          this.loadingPage = false;
        });
    },

    // Validation
    validationMessageForm(isRequire, field, name, msgNumber, type, lengNumber) {
      let data = this.formData[field];
      if (type == 'array') {
        if (!data || (data && data.length === 0) || (data && !data.some((x) => x.delete_flag === 0 || x.delete_flag == 1))) {
          return this.getMessage(msgNumber, name);
        }
      } else {
        if (!data && data !== 0 && isRequire) {
          return this.getMessage(msgNumber, name);
        } else {
          if (lengNumber && data && data.length > lengNumber) {
            return this.getMessage('MSFA0012', name, lengNumber);
          }
        }
      }
      return '';
    },

    checkValidateForm(isRequire, field, name, msgNumber, type, lengNumber) {
      if (this.lstMode.includes('createMode')) {
        if (this.validationMessageForm(isRequire, field, name, msgNumber, type, lengNumber) && this.isSubmitBtn) {
          return true;
        }
      } else {
        if (this.validationMessageForm(isRequire, field, name, msgNumber, type, lengNumber)) {
          return true;
        }
      }
      return false;
    },

    checkDisabledDate() {
      if (!this.endDateInputBriefing) {
        return false;
      }

      let startTime = new Date(new Date(this.oldFormData.start_date).setHours(0, 0, 0, 0))?.getTime();
      let today = new Date(new Date()?.setHours(0, 0, 0, 0))?.getTime();

      return startTime <= today;
    },

    // check Mode
    checkModeUser() {
      // viewMode, editMode, createMode, approvalMode
      this.lstMode = [];
      let today = new Date(new Date().setHours(0, 0, 0, 0))?.getTime();
      let startTime = new Date(new Date(this.formData.start_date).setHours(0, 0, 0, 0))?.getTime();
      let systemDate = new Date(new Date(this.endDateInputBriefing)?.setHours(23, 59, 59, 59))?.getTime();

      let datePeriod = this.endDateInputBriefing ? (today <= systemDate ? true : false) : true;

      if (datePeriod) {
        if (this.briefing_id_default || this.schedule_id_default) {
          if (this.status_check == '10' || this.status_check == '30' || this.status_check == '40') {
            if (this.permission?.includes('R20') && this.currentUser.user_cd == this.userCreated) {
              if (this.status_check == '30') {
                this.lstMode.push(startTime <= today ? 'editMode' : 'viewMode');
              } else {
                this.lstMode.push('editMode');
              }
            } else {
              this.lstMode.push('viewMode');
            }
            if (this.isUserApprover || (!this.permission?.includes('R20') && !this.isUserApprover)) {
              this.lstMode.push('viewMode');
            }
          } else {
            if (this.status_check == '20' || this.status_check == '50') {
              if (this.isUserApprover) {
                this.lstMode.push('approvalMode');
              }
              if (this.permission?.includes('R20') || (!this.permission?.includes('R20') && !this.isUserApprover)) {
                this.lstMode.push('viewMode');
              }
            } else {
              if (this.status_check == '60' || this.status_check == '70') {
                this.lstMode.push('viewMode');
              }
            }
          }
        } else {
          if (this.permission?.includes('R20')) {
            this.lstMode.push('createMode');
          }
        }
      } else {
        this.lstMode.push('viewMode');
      }

      this.keyComponentButton = Math.random();
    },

    getScreenDataInfo() {
      B01_S02_BriefingSessionInputService.getScreenDataInfo()
        .then((res) => {
          let data = res.data.data;

          this.lstStatusType = data.status_type;
          this.lstBriefingType = data.briefing_type;
          this.lstMedicalSubjectsGroup = data.medical_subjects_group;
          this.lstBentoDisposal = data.bento_disposal;
          this.lstOtherMedicalStaff = data.medical_staff;
          this.document_usage_type = data.document_usage_type;
          this.request_type_plan = data.request_type_plan;
          this.request_type_result = data.request_type_result;

          this.roleUser = this.$store.state.auth.policyRole;
          this.formData = {
            ...this.formData,
            briefing_expense_item: data.briefing_expense_item || [],
            briefing_attendee_count: data.briefing_attendee_count || []
          };

          if (!this.router?.briefing_id && !this.router?.schedule_id) {
            if (this.permission?.includes('R20')) {
              this.formData = {
                ...this.formData,
                target_org_cd: this.currentUser?.org_cd,
                target_org_label: this.currentUser?.org_label,

                implement_user_org_cd: this.currentUser?.org_cd,
                implement_user_org_label: this.currentUser?.org_label,
                implement_user_cd: this.currentUser?.user_cd,
                implement_user_name: this.currentUser?.user_name
              };
            }
          }
          this.oldFormData = JSON.parse(JSON.stringify(this.formData));
          this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));

          if (this.briefing_id_default) {
            this.getDataDetail('briefing', this.briefing_id_default);
          } else {
            if (this.schedule_id_default) {
              this.getDataDetail('schedule', this.schedule_id_default);
            } else {
              this.setSelectedBentoDisposal(this.lstBentoDisposal[0].bento_disposal_value);
              this.oldFormData = JSON.parse(JSON.stringify(this.formData));
              this.dataNumberChange = JSON.parse(JSON.stringify(this.formData));
              this.checkReportApprove();
              this.checkModeUser();
              this.loadingPage = false;

              this.$router.replace({ params: {} });
            }
          }
        })
        .catch((err) => {
          this.loadingPage = false;
          if (err.response?.data?.message) {
            this.$notify({ message: err.response?.data?.message, customClass: 'error' });
          }
        })
        .finally(() => {
          this.scrollTop();
          this.psContainer = new PerfectScrollbar('#briefingSession', {
            wheelSpeed: 0.5
          });
        });
    },

    getDataDetail(type, id) {
      this.loadingPage = true;
      let params = {
        briefing_id: type === 'briefing' ? id : '',
        schedule_id: type === 'schedule' ? id : ''
      };
      B01_S02_BriefingSessionInputService.getDataDetail(params)
        .then(async (res) => {
          let data = res.data.data;
          let detail = data.briefing_detail;

          this.briefing_plan = data.briefing_plan;
          this.briefing_result = data.briefing_result;
          this.status_report_approval = data.status_report_approval;
          this.active_layer_approval = data.active_layer_approval;
          this.layer_user_approval = data.layer_user_approval;

          this.isUserApprover = data.user_approval == 1 ? true : false;

          if (detail && detail?.briefing_id) {
            this.remand_flag = detail.remand_flag == 1 ? true : false;
            this.status_check = detail.status_type;
            this.endDateInputBriefing = detail.end_date;
            this.isUserPlaner = detail.implement_user_cd && detail.implement_user_cd === this.currentUser.user_cd ? true : false;
            this.lstPersonDefault = [...detail.briefing_attendee] || [];

            this.userCreated = detail.implement_user_cd;

            this.formData = {
              ...this.formData,
              briefing_id: detail.briefing_id,
              briefing_name: detail.briefing_name,
              briefing_type: detail.briefing_type,
              content: detail.content,
              start_date: this.formatFullDate(detail.start_date) || '',
              start_time: this.formatTimeHourMinute(detail.start_time),
              end_time: this.formatTimeHourMinute(detail.end_time),
              status_type: detail.status_type,
              facility_cd: detail.facility_cd,
              facility_short_name: detail.facility_short_name,
              implement_user_org_cd: detail.implement_user_org_cd,
              implement_user_org_label: detail.implement_user_org_label,
              implement_user_cd: detail.implement_user_cd,
              implement_user_name: detail.implement_user_name,
              implement_user_post: detail.implement_user_post,
              implement_user_post_name: detail.implement_user_post_name,
              target_org_cd: detail.target_org_cd,
              target_org_label: detail.target_org_label,
              medical_subjects_group_cd: detail.medical_subjects_group_cd,
              medical_subjects_group_name: detail.medical_subjects_group_name,
              place: detail.place,
              briefing_document: detail.briefing_document || [],
              briefing_product: detail.briefing_product || [],
              briefing_attendee: detail.briefing_attendee || [],

              briefing_attendee_count: detail.briefing_attendee_count || [],

              briefing_expense_item: detail.briefing_expense_item || [],

              disposal_technique: detail.disposal_technique,
              reason: detail.reason,
              note: detail.note,

              schedule_id: detail.schedule_id
            };

            if (detail.facility_cd) {
              this.paramsZ05S03 = {
                ...this.paramsZ05S03,
                facility_cd: [detail.facility_cd],
                org_cd: '',
                user_cd: '',
                user_name: ''
              };

              this.paramZ05S04 = {
                ...this.paramZ05S04,
                facility_cd: [detail.facility_cd],
                user_cd: '',
                user_name: ''
              };
            }

            this.changeAttendeePlan();
            this.changeAttendeeResult();
            if (this.formData.briefing_expense_item.length > 0) {
              this.changeAmountExpensePlan(0);
              this.changeAmountExpenseResult(0);
            }

            this.setSelectedBentoDisposal(detail.disposal_technique ? detail.disposal_technique : '');

            this.lstMaterialsDefault = JSON.parse(JSON.stringify(detail.briefing_document || []));
            this.lstProductDefault = JSON.parse(JSON.stringify(detail.briefing_product || []));

            await new Promise((resolve) => setTimeout(resolve, 100));

            this.oldFormData = JSON.parse(JSON.stringify(this.formData));

            this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));

            let startDate = new Date(new Date(this.formData.start_date).setHours(0, 0, 0, 0)).getTime();
            let today = new Date(new Date().setHours(0, 0, 0, 0)).getTime();
            if (startDate <= today) {
              this.isInputResult = true;
            }

            this.checkLayerUserApproval();
          } else {
            this.oldFormData = JSON.parse(JSON.stringify(this.formData));
            this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));
          }
          this.keyComponentTimepicker = Math.random();
          this.checkModeUser();
        })
        .catch((err) => {
          if (err.response?.data?.message) {
            this.$notify({ message: err.response?.data?.message, customClass: 'error' });
          }
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 200));
          this.scrollTop();

          this.loadingPage = false;

          this.psContainer = new PerfectScrollbar('#briefingSession', {
            wheelSpeed: 0.5
          });

          this.$router.replace({ params: {} });
        });
    },

    scrollTop() {
      if (this.$refs.contentBriefingRef) {
        this.$refs.contentBriefingRef.scrollTop = 1;
      }
      if (this.$refs.documentRef) {
        this.$refs.documentRef.scrollTop = 1;
      }
    },

    changeMedical() {
      let medical_subjects_group_name = this.lstMedicalSubjectsGroup.find(
        (x) => x.medical_subjects_group_cd === this.formData.medical_subjects_group_cd
      )?.medical_subjects_group_name;
      this.formData.medical_subjects_group_name = medical_subjects_group_name;
    },

    convertParamsSave() {
      let timeStart = this.formData.start_date + ' ' + this.formData.start_time;
      let timeEnd = this.formData.start_date + ' ' + this.formData.end_time;

      let statusType = this.formData.status_type;

      let today = new Date();
      let startDate = new Date(new Date(timeStart));

      if (!this.status_check && today.getTime() > startDate.getTime()) {
        statusType = '40';
      }

      let request_type = this.request_type_plan;
      if (!this.status_check || this.status_check == '10') {
        request_type = this.request_type_plan;
      }
      if (this.status_check == '30' || this.status_check == '40') {
        request_type = this.request_type_result;
      }

      if (!this.status_check && today.getTime() > startDate.getTime()) {
        request_type = this.request_type_result;
      }

      let params = {
        ...this.formData,
        start_time: this.formData.start_time ? timeStart : '',
        end_time: this.formData.end_time ? timeEnd : '',
        status_type: statusType,
        request_type: request_type
      };

      return params;
    },

    openModalStock() {
      if (this.formData.briefing_id) {
        this.openModalPersonStock(this.formData.briefing_id, false);
      } else {
        this.openConfirmSaveData();
      }
    },

    openConfirmSaveData() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          mode: 1,
          message: 'ストック登録画面を開く際、説明会IDが必要になります。 まだ、新規入力の状態になっているため説明会を一時保存しますか？'
        }
      };
    },

    saveData() {
      this.submitButtonClick();
      if (!this.checkValidMessageSum()) {
        let params = { ...this.convertParamsSave() };

        B01_S02_BriefingSessionInputService.saveData(params)
          .then((res) => {
            this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
            this.lstPersonDefault = [...this.formData.briefing_attendee];
            let briefingid = res.data.data.briefing_id;
            this.isSaveBeforeStock = true;
            this.openModalPersonStock(briefingid);
          })
          .catch((err) => {
            this.loadingPage = false;
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          })
          .finally(() => {});
      } else {
        this.scrollTop();
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        this.processingSave = false;
      }
      this.onCloseModal();
    },

    checkValidMessageSum() {
      let message = '';

      if (this.checkValidateForm(true, 'briefing_name', '説明会名', 'MSFA0001', 'String', 100)) {
        message = this.validationMessageForm(true, 'briefing_name', '説明会名', 'MSFA0001', 'String', 100);
      } else {
        if (this.checkValidateForm(true, 'start_date', '日', 'MSFA0040')) {
          message = this.validationMessageForm(true, 'start_date', '日', 'MSFA0040');
        } else {
          if (this.checkValidateForm(true, 'start_time', '時', 'MSFA0040')) {
            message = this.validationMessageForm(true, 'start_time', '時', 'MSFA0040');
          } else {
            if (this.checkValidateForm(true, 'implement_user_name', '実施者', 'MSFA0040')) {
              message = this.validationMessageForm(true, 'implement_user_name', '実施者', 'MSFA0040');
            } else {
              if (this.checkValidateForm(true, 'target_org_label', '対象組織', 'MSFA0040')) {
                message = this.validationMessageForm(true, 'target_org_label', '対象組織', 'MSFA0040');
              } else {
                if (this.checkValidateForm(false, 'place', '場所', null, 'String', 255)) {
                  message = this.validationMessageForm(false, 'place', '場所', null, 'String', 255);
                }
              }
            }
          }
        }
      }

      return message;
    },

    goToBack() {
      this.back();
    },

    // B
    handleClickStatus(step) {
      this.stepType = step;
      if (step == 'step20' || step == 'step50') {
        if (step == 'step20') {
          this.briefing_comment = [...this.briefing_plan];
        } else {
          this.briefing_comment = [...this.briefing_result];
        }
        this.briefing_comment = this.convertCommentByLayer('approval_layer_num', this.briefing_comment);

        if (this.$refs[step]) {
          this.$refs[step][0]?.click();
        }
      }
    },

    convertCommentByLayer(pathName, datas) {
      return chain(datas)
        .groupBy(pathName)
        .map((value, key) => {
          return { [pathName]: key, data: value };
        })
        .value();
    },

    checkLayerUserApproval() {
      this.finalLayerApproval = 1;
      this.layerApproval = 1;
      let layerBriefing = [];
      this.isUserApprover = false;

      if (this.layer_user_approval.length === 0) {
        this.isUserApprover = false;
      } else {
        if (this.status_check == '20' || this.status_check == '50') {
          if (this.status_check == '20') {
            layerBriefing = this.convertCommentByLayer('approval_layer_num', this.briefing_plan);
          }

          if (this.status_check == '50') {
            layerBriefing = this.convertCommentByLayer('approval_layer_num', this.briefing_result);
          }

          this.finalLayerApproval = layerBriefing.length;

          for (let x in layerBriefing) {
            let layer = layerBriefing[x];
            if (layer.approval_layer_num == this.active_layer_approval) {
              this.layerBriefingFlats = layer.data;
              let userApprove = layer.data.find((x) => x.approval_user_cd === this.currentUser?.user_cd);
              if (layer.data.some((x) => x.status_type == '1' || x.status_type == '2')) {
                this.isUserApprover = false;
              } else {
                if (userApprove && userApprove.status_type == '0') {
                  this.isUserApprover = true;
                }
              }
            }
          }
        } else {
          this.isUserApprover = false;
        }
      }
    },

    showModalComment(item) {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'コメント',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        isShowModal: true,
        isShowClose: false,
        component: markRaw(B01_S02_ModalComment),
        width: '35rem',
        props: {
          comment: item.comment,
          status_type: item.status_type,
          user_name: item.user_name
        }
      };
    },

    onResultModalComment() {
      this.handleClickStatus(this.stepType);
    },

    // D
    onResultTimepicker(startTime, endTime) {
      this.formData.start_time = startTime;
      this.formData.end_time = endTime;

      this.checkReportApprove();
    },

    checkReportApprove() {
      if (this.status_report_approval) {
        this.formData.start_date = this.oldFormData.start_date;
        this.formData.start_time = this.oldFormData.start_time;
        this.formData.end_time = this.oldFormData.end_time;
        this.$notify({ message: '報告が提出済みまたは承認済みの場合はプライベート以外の編集はできません。', customClass: 'error' });
        // 日報が提出済のため、日付は変更できません。日報を差戻してから変更してください。
      }

      let today = new Date(new Date().setHours(0, 0, 0, 0)).getTime();
      let startDate = new Date(new Date(this.formData.start_date).setHours(0, 0, 0, 0)).getTime();
      let isResult = false;

      if (startDate <= today) {
        isResult = true;
      }

      if (isResult != this.isInputResult && !this.briefing_id_default) {
        this.isInputResult = isResult;

        this.dataNumberChange.briefing_attendee_count = JSON.parse(JSON.stringify(this.oldFormData.briefing_attendee_count));
        this.formData.briefing_attendee_count = JSON.parse(JSON.stringify(this.oldFormData.briefing_attendee_count));

        this.dataNumberChange.briefing_expense_item = JSON.parse(JSON.stringify(this.oldFormData.briefing_expense_item));
        this.formData.briefing_expense_item = JSON.parse(JSON.stringify(this.oldFormData.briefing_expense_item));

        this.sumPlanHeadcount = 0;
        this.sumResultHeadcount = 0;
        this.sumPlanAmountExpense = 0;
        this.sumResultAmountExpense = 0;
      }
    },

    openModalZ05S01(isUser) {
      this.isSelectUser = isUser;
      this.modalConfig = {
        ...this.modalConfig,
        title: isUser ? 'ユーザ選択' : '組織選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        customClass: 'custom-dialog modal-fixed',
        props: {
          ...this.paramsZ05S01,
          mode: 'single',
          userFlag: isUser ? 1 : 0,
          orgCdList: isUser
            ? this.formData.implement_user_org_cd
              ? [this.formData.implement_user_org_cd]
              : []
            : this.formData.target_org_cd
            ? [this.formData.target_org_cd]
            : [],
          userCdList: isUser
            ? this.formData.implement_user_cd
              ? [
                  {
                    user_cd: this.formData.implement_user_cd,
                    org_cd: this.formData.implement_user_org_cd
                  }
                ]
              : []
            : []
        },
        width: '45rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (this.isSelectUser) {
        let data = e.userSelected[0];
        this.formData.implement_user_org_label = data.org_label;
        this.formData.implement_user_name = data.user_name;
        this.formData.implement_user_cd = data.user_cd;
        this.formData.implement_user_org_cd = data.org_cd;
      } else {
        let data = e.orgSelected[0];
        this.formData.target_org_cd = data.org_cd;
        this.formData.target_org_label = data.org_label;
      }
    },

    handleRemoveUser() {
      this.formData.implement_user_org_label = '';
      this.formData.implement_user_name = '';
      this.formData.implement_user_cd = '';
      this.formData.implement_user_org_cd = '';
    },

    handleRemoveTargetOrg() {
      this.formData.target_org_cd = '';
      this.formData.target_org_label = '';
    },

    openModalZ05S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設選択',
        isShowModal: true,
        component: markRaw(Z05S03FacilitySelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '55rem',
        customClass: 'custom-dialog',
        props: {
          mode: 'single',
          ...this.paramsZ05S03,
          orgCD: this.paramsZ05S03.org_cd,
          userCD: this.paramsZ05S03.user_cd,
          userName: this.paramsZ05S03.user_name,
          selectGroupCd: this.paramsZ05S03.select_group_id,
          targetProductCd: this.paramsZ05S03.target_product_cd,
          facilityCategoryType: this.paramsZ05S03.facility_category_type,
          prefectureCD: this.paramsZ05S03.prefecture_cd,
          prefectureName: this.paramsZ05S03.prefecture_name,
          municipalityCD: this.paramsZ05S03.municipality_cd,
          municipalityName: this.paramsZ05S03.municipality_name,
          facilityCd: this.paramsZ05S03.facility_cd,
          facilityName: this.paramsZ05S03.facility_name
        },
        destroyOnClose: true
      };
    },

    onResultModalZ05S03(e) {
      if (e?.facilitySelectList.length > 0) {
        let data = e?.facilitySelectList[0];
        this.formData.facility_short_name = data.facility_short_name;
        this.formData.facility_cd = data.facility_cd;

        this.paramsZ05S03 = {
          ...e.filterData,
          facility_cd: [e?.facilitySelectList[0].facility_cd],
          facility_name: [e.filterData.facility_name]
        };

        this.paramZ05S04 = {
          ...this.paramZ05S04,
          facility_cd: [e?.facilitySelectList[0].facility_cd],
          facility_name: [e.filterData.facility_name],
          user_cd: '',
          user_name: ''
        };
      }
    },

    handleRemoveFacility() {
      this.formData.facility_short_name = '';
      this.formData.facility_cd = '';

      this.paramsZ05S03 = {
        selectGroupCd: '',
        facility_cd: [],
        facility_name: [],
        org_cd: this.currentUser.org_cd,
        user_cd: this.currentUser.user_cd,
        user_name: this.currentUser.user_name
      };

      this.paramZ05S04 = {
        ...this.paramZ05S04,
        selectGroupCd: '',
        facility_cd: [],
        facility_name: [],
        user_cd: this.currentUser.user_cd,
        user_name: this.currentUser.user_name
      };
    },

    checkOutsideValid(start_date, end_date) {
      let today = new Date();
      if (new Date(start_date).getTime() <= today.getTime() && today.getTime() <= new Date(end_date).getTime()) {
        return false;
      }
      return true;
    },

    openModalZ05S05() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材選択',
        customClass: 'custom-dialog custom-dialog-pd-none',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z05_S05_ChoiceOfMasterial),
        width: '77rem',
        props: {
          mode: 'multiple',
          subMaterialSelectableFlag: 1,
          customMaterialFlag: 1,
          params: {
            ...this.paramsZ05S05,
            initMaterials: this.formData.briefing_document.filter((e) => e.delete_flag != -1).map((x) => x.document_id)
          }
        }
      };
    },

    onResultModalZ05S05(e) {
      let oldData = [...this.formData.briefing_document];
      let newData = [...e?.dataMasterialSelected];

      for (let i in oldData) {
        let element = oldData[i];
        if (!newData.some((x) => x.document_id == element.document_id)) {
          this.deleteMaterial(element);
        }
      }

      for (let i in newData) {
        let x = newData[i];
        let index = this.formData.briefing_document.findIndex((e) => e.document_id?.toString() === x.document_id?.toString());
        if (index === -1) {
          this.formData.briefing_document.push({
            document_id: x.document_id,
            document_name: x.document_name,
            file_type: x.file_type,
            document_type: x.document_type,
            start_date: x.start_date,
            end_date: x.end_date,
            delete_flag: 1,
            document_review: x.point && parseInt(x.point) > 0 ? 1 : 0
          });
        } else {
          if (this.formData.briefing_document[index].delete_flag === -1) {
            this.formData.briefing_document[index].delete_flag = 0;
          }
        }
      }
      this.scrollTop();
    },

    deleteMaterial(item) {
      let index = this.formData.briefing_document.findIndex((e) => e.document_id?.toString() === item.document_id?.toString());
      if (this.lstMaterialsDefault.some((x) => x.document_id == item.document_id)) {
        this.formData.briefing_document[index].delete_flag = -1;
      } else {
        this.formData.briefing_document = this.formData.briefing_document.filter((x) => x.document_id != item.document_id);
      }
    },

    openModalMaterialViewerD01S07(document) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材ビューワ',
        isShowModal: true,
        component: markRaw(D01_S07_MaterialViewer),
        width: '70%',
        customClass: 'custom-dialog',
        props: { documentId: document.document_id }
      };
    },

    redirectToD01S02(item) {
      let document_id = item.document_id;
      this.modalConfig = {
        ...this.modalConfig,
        title: '',
        isShowModal: true,
        isShowClose: true,
        customClass: 'custom-dialog custom-dialog-pd custom-dialog-material',
        component: markRaw(D01_S02_ModalMaterialDetails),
        props: { documentId: document_id },
        width: '90%',
        destroyOnClose: true,
        closeOnClickMark: false
      };
    },

    openModalZ05S06() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        customClass: 'custom-dialog modal-fixed',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z05S06MaterialSelection),
        width: '42rem',
        props: {
          ...this.paramsZ05S06,
          initDataCodes: this.formData.briefing_product.filter((x) => x.delete_flag != -1)?.map((x) => x.product_cd)
        }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected;
      this.formData.briefing_product = this.formData.briefing_product.filter((e) => e.delete_flag != 1);

      for (let i = 0; i < this.lstProductDefault.length; i++) {
        let element = this.lstProductDefault[i];
        let index = this.formData.briefing_product.findIndex((e) => e.product_cd == element.product_cd);

        if (!data.some((x) => x.product_cd == element.product_cd)) {
          this.formData.briefing_product[index].delete_flag = -1;
        } else {
          this.formData.briefing_product[index].delete_flag = 0;
        }
      }

      for (let j = 0; j < data.length; j++) {
        let element = data[j];
        let index = this.formData.briefing_product.findIndex((e) => e.product_cd == element.product_cd);
        if (index === -1) {
          this.formData.briefing_product.push({
            product_cd: element.product_cd,
            product_name: element.product_name,
            delete_flag: 1
          });
        }
      }

      this.paramsZ05S06 = {
        ...this.paramsZ05S06,
        categoryCode: e.category.definition_value,
        classificationCode: e.classifications.product_group_cd,
        initDataCodes: this.formData.briefing_product.filter((x) => x.delete_flag != -1)?.map((x) => x.product_cd)
      };
    },

    handleRemoveProduct(product) {
      let index = this.formData.briefing_product.findIndex((e) => e.product_cd?.toString() === product.product_cd?.toString());
      if (this.lstProductDefault.some((x) => x.product_cd == product.product_cd)) {
        this.formData.briefing_product[index].delete_flag = -1;
      } else {
        this.formData.briefing_product = this.formData.briefing_product.filter((x) => x.product_cd != product.product_cd);
      }
    },

    redirectToH02S02(person) {
      let person_cd = person.person_cd;
      this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
    },

    openModalAddPersonOther(isEdit, person, index) {
      this.indexEditPerson = index;
      let facility = {
        facility_short_name: this.formData.facility_short_name,
        facility_cd: this.formData.facility_cd
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: 'その他個人追加',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        isShowModal: true,
        component: markRaw(B01_S02_ModalAddPersonOther),
        width: '28rem',
        props: {
          isEdit: isEdit,
          facility: isEdit ? null : this.formData.facility_cd ? facility : null,
          person: isEdit ? person : null,
          lstOtherMedicalStaff: this.lstOtherMedicalStaff,
          currentUser: this.currentUser
        }
      };
    },

    onResultModalAddPersonOther(e) {
      let data = e?.personOther;
      if (e?.isEdit) {
        let person = {
          ...this.formData.briefing_attendee[this.indexEditPerson],
          facility_short_name: data.facility_short_name,
          facility_cd: data.facility_cd,
          person_cd: data.person_cd,
          person_name: data.person_name,
          medical_staff_cd: data.medical_staff_cd,
          display_position_name: data.medical_staff_name
        };
        this.formData.briefing_attendee[this.indexEditPerson] = person;

        this.indexEditPerson = null;
      } else {
        if (data) {
          this.formData.briefing_attendee.push({
            ...data,
            display_position_name: data.medical_staff_name,
            delete_flag: 1,
            other_person_flag: 1
          });
        }
      }
    },

    openModalPersonStock(briefing_id) {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ストック登録',
        customClass: 'custom-dialog custom-dialog-pd-none',
        isShowModal: true,
        component: markRaw(B01_S02_ModalPersonStock),
        width: '50rem',
        props: {
          lstDataPerson: [...this.lstPersonDefault],
          briefingid: briefing_id
        }
      };
    },

    openModalZ05S04() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設個人選択',
        isShowModal: true,
        isShowClose: true,
        customClass: 'custom-dialog',
        component: this.markRaw(Z05S04FacilityCustomerSelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '70rem',
        props: {
          mode: 'multiple',
          propsPrams: { ...this.paramZ05S04 }
        }
      };
    },

    onResultModalZ05S04(e) {
      if (e) {
        let data = e?.facilityPersonalSelected;
        if (data.length > 0) {
          data.forEach((x) => {
            this.formData.briefing_attendee.push({
              ...x,
              display_position_name: x.position_name,
              delete_flag: 1,
              other_person_flag: 0
            });
          });
        }
      }
    },

    removePerson(person, index) {
      if (this.lstPersonDefault.some((x) => x.person_cd == person.person_cd)) {
        this.formData.briefing_attendee[index].delete_flag = -1;
      } else {
        this.formData.briefing_attendee.splice(index, 1);
      }
    },

    // E
    changeAttendeePlan() {
      setTimeout(() => {
        this.sumPlanHeadcount = 0;
        for (let i = 0; i < this.formData.briefing_attendee_count.length; i++) {
          const element = this.formData.briefing_attendee_count[i];
          this.sumPlanHeadcount += Number(element.plan_headcount);
        }
      });
    },

    changeAttendeeResult() {
      setTimeout(() => {
        this.sumResultHeadcount = 0;
        for (let i = 0; i < this.formData.briefing_attendee_count.length; i++) {
          const element = this.formData.briefing_attendee_count[i];
          this.sumResultHeadcount += Number(element.result_headcount);
        }
      });
    },

    // F
    changeAmountExpensePlan(index) {
      setTimeout(() => {
        let item = this.formData.briefing_expense_item[index];
        item.plan_amount = item.quantity_input_flag ? item.plan_unit_price * item.plan_quantity : item.plan_unit_price;
        this.formData.briefing_expense_item[index] = { ...item };
        this.dataNumberChange.briefing_expense_item[index].plan_amount = this.convertNumberToCurrency(item.plan_amount);
        this.sumPlanAmountExpense = 0;
        for (let i = 0; i < this.formData.briefing_expense_item.length; i++) {
          const element = this.formData.briefing_expense_item[i];
          this.sumPlanAmountExpense += Number(element.plan_amount);
        }
      });
    },

    changeAmountExpenseResult(index) {
      setTimeout(() => {
        let item = this.formData.briefing_expense_item[index];
        item.result_amount = item.quantity_input_flag ? item.result_unit_price * item.result_quantity : item.result_unit_price;
        this.formData.briefing_expense_item[index] = { ...item };
        this.dataNumberChange.briefing_expense_item[index].result_amount = this.convertNumberToCurrency(item.result_amount);
        this.sumResultAmountExpense = 0;
        for (let i = 0; i < this.formData.briefing_expense_item.length; i++) {
          const element = this.formData.briefing_expense_item[i];
          this.sumResultAmountExpense += Number(element.result_amount);
        }
      });
    },

    // G
    setSelectedBentoDisposal(item) {
      if (item) {
        this.formData.disposal_technique = item;
        this.lstBentoDisposal.forEach((x) => {
          x.selected = false;
          if (x.bento_disposal_value === item) {
            x.selected = true;
          }
        });
      }
    },

    // H
    submitButtonClick() {
      this.isSubmitBtn = true;
    },

    loadingTrue() {
      this.loadingPage = true;
    },

    loadingFalse() {
      this.loadingPage = false;
    },

    async reloadData(e) {
      if (e && e.copyFlag) {
        let briefing = e.briefing_id;
        await this.$router.replace({ query: { briefing_id: briefing } });
        await this.$forceUpdate();
        this.$router.go();
      } else {
        if (this.briefing_id_default || this.schedule_id) {
          if (this.briefing_id_default) {
            this.getDataDetail('briefing', this.briefing_id_default);
          }
          if (this.schedule_id) {
            this.getDataDetail('schedule', this.schedule_id);
          }
        } else {
          this.setSelectedHoldForm(this.lstHoldingForm[0].hold_form_value);
          this.loadingPage = false;
          this.oldFormData = JSON.parse(JSON.stringify(this.formData));
          this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));
        }
      }
    },

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'Z05_S01') {
          this.onResultModalZ05S01(e);
        }
        if (e.screen === 'Z05_S03') {
          this.onResultModalZ05S03(e);
        }
        if (e.screen === 'Z05_S04') {
          this.onResultModalZ05S04(e);
        }
        if (e.screen === 'Z05_S05') {
          this.onResultModalZ05S05(e);
        }
        if (e.screen === 'Z05_S06') {
          this.onResultModalZ05S06(e);
        }
        if (e.screen === 'B01_S02_ModalAddOtherPerson') {
          this.onResultModalAddPersonOther(e);
        }
        if (e.screen === 'B01_S02_Modal_Comment') {
          this.onResultModalComment();
        }
      }

      if (e && e.screen === 'ModalStock') {
        this.onCloseModal();
        if (this.isSaveBeforeStock) {
          this.$router.go(-1);
        }
      } else {
        this.onCloseModal();
      }

      this.isSaveBeforeStock = false;
    },

    onCloseModalStock() {
      if (this.isSaveBeforeStock) {
        this.$router.go(-1);
      }
    },

    onCloseModal() {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: false,
        isShowModalConfirmInput: false,
        isShowClose: true
      };
    },

    childFocusOut() {
      const elMain = this.$refs.contentBriefingRef;
      if (elMain && elMain?.classList) {
        elMain?.classList?.remove('stop-scroll');

        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = new PerfectScrollbar('#briefingSession', {
          wheelSpeed: 0.5
        });
      }
    },
    childFocus() {
      const els = document.querySelectorAll('.scroll-child');
      const elMain = this.$refs.contentBriefingRef;

      setTimeout(() => {
        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = null;
      }, 0);

      els.forEach(() => {
        if (elMain && elMain?.classList) {
          elMain.classList.add('stop-scroll');
        }
        return;
      });
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_min_btn: 1316px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.briefingSession {
  padding-top: 28px;
  height: 100%;
  .briefingGroup {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
  }
  .briefingHead {
    .sessionNumber {
      padding: 12px 35px;
      font-size: 1.5rem;
      font-weight: 700;
      @media (max-width: $viewport_tablet) {
        padding: 12px 24px;
      }
    }
    .dailyStep {
      padding: 12px 24px;
      &.dailyStep--blue {
        background: #eefbfa;
        background: -moz-linear-gradient(left, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
        background: -webkit-linear-gradient(left, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
        background: linear-gradient(to right, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
      }
      &.dailyStep--red {
        background: #fff7f1;
        background: -moz-linear-gradient(left, #fff7f1 0%, #feeae9 75%, #f9eaf1 100%);
        background: -webkit-linear-gradient(left, #fff7f1 0%, #feeae9 75%, #f9eaf1 100%);
        background: linear-gradient(to right, #fff7f1 0%, #feeae9 75%, #f9eaf1 100%);
      }
    }
  }
  .briefingContent {
    height: 100%;
    padding-bottom: 32px;
  }
  .sessionInput {
    .sessionInput-title {
      max-width: 300px;
      padding-top: 20px;
      &--change {
        max-width: 440px;
        padding-top: 52px;
      }
      .tlt {
        background: #eef6ff;
        box-shadow: 0 5px 5px rgba(145, 161, 171, 0.4);
        border-radius: 0px 40px 40px 0px;
        font-size: 1.125rem;
        font-weight: bold;
        padding: 14px 12px 14px 52px;
      }
    }
  }
  .sessionGroup {
    padding: 0 32px;
    margin-left: -32px;
    display: flex;
    flex-wrap: wrap;
    @media (max-width: $viewport_tablet) {
      padding: 0 24px;
      margin-left: -24px;
    }
    .session-col {
      width: 50%;
      padding-left: 32px;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-left: 24px;
      }
    }
    .sessionForm {
      padding-top: 8px;
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -20px;
        > li {
          width: 50%;
          margin-top: 24px;
          padding-left: 20px;
        }
      }
      .sessionForm-label {
        margin-bottom: 10px;
        font-size: 1rem;
        display: flex;
        align-items: center;
        .required {
          margin: -2px 0 0 8px;
        }
      }
      .sessionForm-control {
        > ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: -20px;
          > li {
            width: 50%;
            padding-left: 20px;
          }
        }
      }
    }
    .materials {
      padding-top: 30px;
      .materials-title {
        padding: 0 12px 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        .tlt {
          font-size: 1rem;
        }
        .btn {
          &.btn-outline-primary {
            padding: 0 12px;
            height: 32px;
            .btn-iconLeft {
              margin-right: 4px;
              svg {
                width: 13px;
              }
            }
          }
        }
      }
      .materialsBox {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        margin-top: 12px;
        padding: 10px 0;
      }
      .materialsLst {
        padding: 0 12px;
        height: 285px;
        > ul {
          > li {
            padding: 15px 0;
            border-bottom: 1px solid #e3e3e3;
            display: flex;
            justify-content: space-between;
          }
        }
        .materialsLst-txt {
          display: flex;
          padding-top: 8px;
          align-items: flex-start;
          a {
            display: flex;
            font-size: 1rem;
            margin-right: 10px;
            .materialsLst-item {
              margin: 0 12px;
              min-width: 18px;
            }
          }
          .spanLabel {
            padding: 1px 6px;
            color: #ea5d54;
            background: #feddde;
            border-radius: 2px;
            min-width: 82px;
          }
        }
        .materialsLst-btn {
          position: relative;
          min-width: 42px;
          margin-left: 10px;
          .btn {
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
            width: 158px;
            min-width: inherit;
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
                padding: 6px 46px 6px 20px;
                cursor: pointer;
                color: #448add;
                font-weight: 500;
                font-size: 0.875rem;
                display: flex;
                &:hover {
                  background: #f2f2f2;
                }
                .item {
                  min-width: 18px;
                  margin-right: 4px;
                  margin-top: -2px;
                  text-align: center;
                }
              }
            }
          }
        }
      }
    }
  }
  .attendees-title {
    display: flex;
    justify-content: space-between;
    padding-top: 22px;

    .attendees-tlt {
      min-width: 120px;
      font-size: 1rem;
      padding-top: 6px;
    }
    .attendees-btn {
      .btn {
        height: 32px;
        padding: 0 12px;
        margin-left: 6px;
        margin-bottom: 6px;
        .btn-iconLeft {
          margin-right: 3px;
        }
        svg {
          width: 13px;
        }
      }
    }
  }
  @media (min-width: $viewport_tablet) and (max-width: $viewport_min_btn) {
    .attendees-title:not(.flex_col) {
      gap: 10px;
      flex-direction: column;
    }
  }

  .attendeesBox {
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    padding: 10px 0;
    margin-top: 10px;
    .attendeesLst {
      padding: 0 12px;
      max-height: 508px;
      min-height: 500px;
      ul {
        li {
          border-bottom: 1px solid #e3e3e3;
          display: flex;
          justify-content: space-between;
          padding: 10px 16px;
        }
      }
      .attendeesLst-info {
        .attendeesLst-tlt {
          line-height: 20px;
        }
        .attendeesLst-txt {
          .attendeesLst-link,
          .attendeesLst-name {
            font-size: 1rem;
            font-weight: bold;
          }
          .attendeesLst-link {
            color: #448add;
            cursor: pointer;
          }
          .attendeesLst-label {
            vertical-align: bottom;
          }
        }
      }
      .attendeesLst-btn {
        margin-left: 12px;
        text-align: right;
        min-width: 96px;
        .btn {
          min-width: 42px;
          margin-right: 12px;
          &:last-child {
            margin-right: 0;
          }
        }
      }
    }
  }
  .attendeesTbl {
    margin-top: 17px;
    box-shadow: 0 0 8px #e3e3e3;
    -moz-border-radius: 10px;
    border-radius: 10px;
    background: #fff;
    tr {
      th,
      td {
        vertical-align: middle;
        padding: 5px 24px;
        width: 33.333333%;
        font-size: 1rem;
      }
    }
    thead {
      tr {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        th {
          text-align: center;
          color: #fff;
          position: relative;
          &:not(:last-child):after {
            content: '';
            width: 1px;
            background-color: #fff;
            height: calc(100% - 8px);
            position: absolute;
            top: 4px;
            right: -1px;
          }

          &:first-of-type:after {
            background-color: transparent !important;
          }
        }
      }
    }
    tbody {
      position: relative;
      tr {
        td {
          border-top: none;
          border-bottom: none;
          text-align: right;
          &:first-child {
            text-align: left;
          }
          &.attendeesTbl-people {
            color: #b7c3cb;
          }
        }
      }
    }
    tfoot {
      position: relative;
      &::after {
        top: 0;
        left: 0;
        position: absolute;
        border: 10px solid #fff;
        border-top: none;
        width: 100%;
        height: 100%;
        content: '';
      }
      tr {
        background: #f9f9f9;
        td {
          border-top: none;
          border-bottom: none;
          padding: 15px 24px 25px;
          font-weight: 700;
          text-align: right;
          padding: 15px 27px 25px;
          &:first-child {
            text-align: left;
            padding: 15px 24px 25px;
          }
          &.attendeesTbl-people {
            color: #b7c3cb;
            padding-right: 23px;
          }
          &.attendeesTbl-view {
            padding-right: 23px;
          }
        }
      }
    }
    @media (max-width: 1181px) {
      tbody,
      tfoot {
        &::after,
        &::before {
          display: none;
        }
      }
    }
  }
  .expensesTbl {
    margin-top: 32px;
    box-shadow: 0 0 8px #e3e3e3;
    -moz-border-radius: 10px;
    border-radius: 10px;
    background: #fff;
    tr {
      th,
      td {
        vertical-align: middle;
        padding: 5px 24px;
        font-size: 1rem;
        &:nth-child(1) {
          min-width: 200px;
        }
        &:nth-child(2),
        &:nth-child(3) {
          width: 50%;
          min-width: 450px;
        }
      }
    }
    thead {
      tr {
        background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
        th {
          text-align: center;
          color: #fff;
          position: relative;
          &:not(:last-child):after {
            content: '';
            width: 1px;
            background-color: #fff;
            height: calc(100% - 8px);
            position: absolute;
            top: 4px;
            right: -1px;
          }
        }
      }
    }
    tbody,
    tfoot {
      position: relative;
      tr {
        td {
          border-top: none;
          border-bottom: none;
        }
      }
    }
    tfoot {
      position: relative;
      &::after {
        top: 0;
        left: 0;
        position: absolute;
        border: 10px solid #fff;
        border-top: none;
        width: 100%;
        height: 100%;
        content: '';
      }
      tr {
        background: #f9f9f9;
        td {
          padding: 15px 24px 25px;
          font-weight: 700;
          .expensesTbl-total,
          .expensesTbl-circle {
            text-align: right;
            display: block;
            padding-right: 6px;
          }
          .expensesTbl-circle {
            color: #99a5ae;
          }
        }
      }
    }
    .expensesTbl-col {
      &.expensesTbl-text {
        color: #b7c3cb;
      }
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -48px;
        li {
          width: 33.333333%;
          padding-left: 48px;
          padding-right: 6px;
          text-align: right;
        }
      }
    }

    @media (max-width: 1181px) {
      tbody,
      tfoot {
        &::after,
        &::before {
          display: none;
        }
      }
    }
  }
  .expenses {
    padding: 0 32px;
    @media (max-width: $viewport_tablet) {
      padding: 0 24px;
    }
  }
  .bento {
    padding-top: 32px;
    .bento-label {
      margin-bottom: 10px;
      font-size: 1rem;
    }
    .bento-select {
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          width: 165px;
          margin-left: -1px;
          &:first-of-type {
            .btn {
              border-radius: 4px 0 0 4px;
            }
          }
          &:last-child {
            .btn {
              border-radius: 0 4px 4px 0;
            }
          }
          .btn {
            width: 100%;
            border-radius: 0;
          }
        }
      }
    }
  }
  .expensesForm {
    padding-top: 20px;
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -52px;
      @media (max-width: $viewport_tablet) {
        margin-left: -24px;
      }
      li {
        padding-left: 52px;
        margin-top: 12px;
        width: 50%;
        @media (max-width: $viewport_tablet) {
          padding-left: 24px;
        }
      }
    }
    .expensesForm-label {
      font-size: 1rem;
      margin-bottom: 10px;
    }
  }
  .expensesBtn {
    text-align: center;
    margin-top: 64px;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}

.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}

.title-link {
  cursor: pointer;
  color: #448add !important;
  &:hover {
    text-decoration: underline !important;
  }
}

.sessionContent-label {
  color: #99a5ae;
}

.sessionContent-value {
  color: #5f6b73;
}

.content-readonly {
  min-height: 150px;
}

.customDropdownlst {
  background: #d1e4ff;
  min-width: 190px;
  max-width: 80vw;
  padding: 14px;
  max-height: 350px;
  min-height: 50px;
  overflow-y: auto;
}

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

.dropdown-block {
  position: relative;
  .dropdown-menu-section {
    animation: fadeIn 0.5s;
    visibility: hidden;
    background: #d1e4ff;
    padding: 14px;
    width: 200px;
    border-radius: 10px;
    border: none;
    box-shadow: 3px 4px 11px -3px #888;
    border: 1px solid #ddd;
    padding: 13px 29px;
    position: absolute;

    &.show {
      visibility: visible;
    }

    .dropdown-menu-item {
      cursor: unset;
      padding: 0;
      color: #225999;
      font-size: 16px;

      span {
        margin-left: 5px;
      }

      &:hover {
        background-color: unset;
        color: #225999;
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

#briefingSession {
  position: relative;
  overflow: hidden !important;
}

.stop-scroll {
  overflow: hidden !important;
}

.content_item_scroll {
  max-height: 150px;
  padding-right: 10px;
}
</style>
