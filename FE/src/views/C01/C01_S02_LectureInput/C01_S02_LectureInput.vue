<template>
  <div class="wrapContent contentOpenD1S2">
    <div class="groupLecture">
      <div v-loading="loadingPage || saveLoading" class="wrapLecture">
        <div class="headLecture">
          <div class="lectureNumber">講演会番号：{{ formData.convention_id }}</div>
          <div v-if="!remand_flag" class="lectureStep lectureStep--blue">
            <!-- Create -->
            <el-steps v-if="!convention_id_default && !schedule_id_default" class="listSteps" :active="0" align-center>
              <el-step v-for="item in lstStatusType" :key="item.status_type" :title="item.status_type_label">
                <template #title>
                  <p :style="{ color: item.status_type == '10' ? '#5F6B73' : '' }">{{ item.status_type_label }}</p>
                </template>
              </el-step>
            </el-steps>

            <!--  承認済 approve -->
            <el-steps
              v-if="(convention_id_default || schedule_id_default) && status_check && status_check != 70"
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
                  (item.status_type == '20' && parseInt(status_check) >= 20 && convention_plan.length > 0) ||
                  (item.status_type == '50' && parseInt(status_check) >= 50 && convention_result.length > 0)
                    ? 'cursor: pointer;'
                    : 'pointer-events: none;'
                "
                @click="handleClickStatus(`step${item.status_type}`)"
              >
                <template #title>
                  <el-dropdown
                    v-if="
                      (item.status_type == '20' && parseInt(status_check) >= 20 && convention_plan.length > 0) ||
                      (item.status_type == '50' && parseInt(status_check) >= 50 && convention_result.length > 0)
                    "
                    trigger="click"
                  >
                    <p
                      :ref="`step${item.status_type}`"
                      :class="'icon_warning_status'"
                      :style="{ color: status_check == item.status_type ? '#5F6B73' : '#448add', cursor: 'pointer' }"
                    >
                      {{ item.status_type_label }}
                      <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                    </p>
                    <template #dropdown>
                      <el-dropdown-menu class="el-dropdown-customDailyReport customDropdownlst">
                        <el-dropdown-item v-for="(itemSub, index) in convention_comment" :key="index">
                          <div v-if="convention_comment.length > 1" class="title_layer">{{ itemSub.approval_layer_num }} 次</div>
                          <div class="content_layer">
                            <div v-for="(person, indexSub) in itemSub.data" :key="indexSub" class="info">
                              <div class="left">
                                <img
                                  v-if="
                                    itemSub.approval_layer_num == active_layer_approval &&
                                    person.status_type == '0' &&
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
              v-if="(convention_id_default || schedule_id_default) && status_check && status_check == 70"
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
              v-if="(convention_id_default || schedule_id_default) && !status_check"
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
          <div v-else-if="(convention_id_default || schedule_id_default) && status_check && status_check != 70" class="lectureStep lectureStep--red">
            <el-steps class="listSteps listSteps--red" :active="status_check == '10' ? 0 : 3" align-center finish-status="success">
              <el-step
                v-for="item in lstStatusType"
                :key="item.status_type"
                :class="{ 'hasBox-red': item.status_type == parseInt(status_check) + 10 }"
                :title="item.status_type_label"
                :style="
                  (item.status_type == '20' && parseInt(status_check) >= 10 && convention_plan.length > 0) ||
                  (item.status_type == '50' && parseInt(status_check) >= 40 && convention_result.length > 0)
                    ? 'cursor: pointer;'
                    : 'pointer-events: none;'
                "
                @click="handleClickStatus(`step${item.status_type}`)"
              >
                <template #title>
                  <el-dropdown
                    v-if="
                      (item.status_type == '20' && parseInt(status_check) >= 10 && convention_plan.length > 0) ||
                      (item.status_type == '50' && parseInt(status_check) >= 40 && convention_result.length > 0)
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
                        <el-dropdown-item v-for="(itemSub, index) in convention_comment" :key="index">
                          <div v-if="convention_comment.length > 1" class="title_layer">{{ itemSub.approval_layer_num }} 次</div>
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
                              <button
                                v-if="person.comment"
                                type="button"
                                class="btn-comment"
                                @click="showModalComment(person)"
                                @touchstart="showModalComment(person)"
                              >
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

          <div v-if="remand_flag && (convention_id_default || schedule_id_default) && status_check && status_check == 70" class="lectureStep lectureStep--blue">
            <el-steps class="listSteps step-gray" :active="6" align-center finish-status="wait">
              <el-step v-for="item in lstStatusType" :key="item.status_type" :title="item.status_type_label">
                <template #title>
                  <p>{{ item.status_type_label }}</p>
                </template>
              </el-step>
            </el-steps>
          </div>
        </div>

        <div id="conventionSession" ref="contentLectureRef" class="contentLecture" @scroll="onScroll">
          <div class="sectionLecture">
            <div class="titleLecture titleLecture--mw300">
              <h2 class="tlt">基本情報</h2>
            </div>
            <!-- D -->
            <div class="basicRow">
              <div class="basic-col">
                <!-- basicGroup-form -->
                <div class="basicGroup-form">
                  <!-- Mode Edit -->
                  <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm-col2">
                    <ul>
                      <li class="w-100">
                        <ul class="d-flex">
                          <!-- D-1 -->
                          <li class="w-50 prc-12">
                            <div class="basicForm">
                              <label class="basicForm-label"> 開催区分 </label>
                              <div class="basicForm-control">
                                <ul class="basicForm-btnSelect basicForm-btnSelect--col2">
                                  <li
                                    v-for="type in lstHoldingType"
                                    :key="type.hold_type_value"
                                    :class="type.selected ? 'active' : ''"
                                    @click="setSelectedHoldingType(type.hold_type_value)"
                                  >
                                    {{ type.hold_type_label }}
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </li>

                          <!-- D-2 -->
                          <li v-if="formData.hold_type == '10'" class="w-50 plc-12">
                            <div class="basicForm">
                              <label class="basicForm-label"> シリーズ登録 </label>
                              <div class="basicForm-control">
                                <ul class="basicForm-btnSelect basicForm-btnSelect--col2">
                                  <li
                                    :class="formData.parent_series_flag == 1 ? 'active' : ''"
                                    @click="formData.parent_series_flag = 1"
                                    @touchstart="formData.parent_series_flag = 1"
                                  >
                                    する
                                  </li>
                                  <li
                                    :class="formData.parent_series_flag == 0 ? 'active' : ''"
                                    @click="formData.parent_series_flag = 0"
                                    @touchstart="formData.parent_series_flag = 0"
                                  >
                                    しない
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </li>

                          <!-- D-3 -->
                          <li v-if="formData.hold_type == '20'" class="w-50 plc-12">
                            <div class="basicForm">
                              <label class="basicForm-label"> 講演会シリーズ </label>
                              <div class="basicForm-control">
                                <div class="form-icon icon-right">
                                  <span
                                    class="icon icon--cursor log-icon"
                                    title_log="講演会シリーズ"
                                    @click="openModalLectureSeriesC01S04()"
                                    @touchstart="openModalLectureSeriesC01S04()"
                                  >
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                                  </span>
                                  <div v-if="formData.series_convention_name" class="form-control d-flex align-items-center">
                                    <div class="block-tags">
                                      <el-tag class="el-tag-custom el-tag-icon-top" closable @close="handleRemoveLectureSeries()">
                                        {{ formData.series_convention_name }}
                                      </el-tag>
                                    </div>
                                  </div>

                                  <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                                </div>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </li>

                      <!-- D-13 -->
                      <li>
                        <div class="basicForm">
                          <label class="basicForm-label"> 品目 </label>
                          <div class="basicForm-control">
                            <div class="form-icon icon-right">
                              <span class="icon icon--cursor log-icon" title_log="品目" @click="openModalZ05S06()" @touchstart="openModalZ05S06()">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                              </span>
                              <div v-if="formData.convention_product.length > 0" class="form-control d-flex align-items-center">
                                <div class="block-tags">
                                  <el-tag
                                    v-for="product in formData.convention_product"
                                    v-show="product.delete_flag !== -1"
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
                        </div>
                      </li>

                      <!-- D-4 -->
                      <li>
                        <div class="basicForm">
                          <label class="basicForm-label"> 講演会区分 </label>
                          <div class="basicForm-control">
                            <el-select v-model="formData.convention_type" placeholder="未選択" class="form-control-select">
                              <el-option
                                v-for="item in lstConventionType"
                                :key="item.convention_type"
                                :label="item.convention_type_label"
                                :value="item.convention_type"
                              >
                              </el-option>
                            </el-select>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>

                  <!-- D-5 -->
                  <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm">
                    <label class="basicForm-label"> 開催方法 </label>
                    <div class="basicForm-control">
                      <ul class="basicForm-btnSelect basicForm-btnSelect--col3-5">
                        <li
                          v-for="method in lstHoldMethod"
                          :key="method.hold_method_value"
                          :class="method.selected ? 'active' : ''"
                          @click="setSelectedHoldMethod(method.hold_method_value)"
                        >
                          {{ method.hold_method_label }}
                        </li>
                      </ul>
                    </div>
                  </div>

                  <!-- D-6 -->
                  <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm">
                    <label class="basicForm-label">
                      講演会名
                      <span class="asterisk"><img src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                    </label>
                    <div class="basicForm-control" :class="checkValidateForm(true, 'convention_name', '講演会名', 'MSFA0001', 'String', 100) ? 'hasErr' : ''">
                      <el-input v-model="formData.convention_name" clearable placeholder="題名入力" class="form-control-input" />
                      <span v-if="checkValidateForm(true, 'convention_name', '講演会名', 'MSFA0001', 'String', 100)" class="invalid">
                        {{ validationMessageForm(true, 'convention_name', '講演会名', 'MSFA0001', 'String', 100) }}
                      </span>
                    </div>
                  </div>

                  <!-- D-7, D-8, D-9-->
                  <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm">
                    <label class="basicForm-label">
                      日時
                      <span class="asterisk"><img src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                    </label>
                    <div class="basicForm-control">
                      <ul class="dateTime">
                        <li>
                          <div class="form-icon icon-left form-icon--noBod" :class="checkValidateForm(true, 'start_date', '日', 'MSFA0040') ? 'hasErr' : ''">
                            <span class="icon">
                              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                            </span>
                            <el-date-picker
                              v-model="formData.start_date"
                              type="date"
                              format="YYYY/M/D"
                              value-format="YYYY/M/D"
                              placeholder="日付選択"
                              class="form-control-datePickerLeft"
                              :editable="false"
                              :disabled="checkDisabledDate()"
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
                            :disabled="checkDisabledDate()"
                            @onResultTimepicker="onResultTimepicker"
                          ></TimePicker>
                          <span v-if="checkValidateForm(true, 'start_time', '時', 'MSFA0040')" class="invalid">
                            {{ validationMessageForm(true, 'start_time', '時', 'MSFA0040') }}
                          </span>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <!-- D-10 -->
                  <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm">
                    <label class="basicForm-label"> 会場 </label>
                    <div class="basicForm-control" :class="checkValidateForm(false, 'place', '会場', null, 'String', 255) ? 'hasErr' : ''">
                      <el-input v-model="formData.place" clearable placeholder="内容入力" class="form-control-input" />
                      <span v-if="checkValidateForm(false, 'place', '会場', null, 'String', 255)" class="invalid">
                        {{ validationMessageForm(false, 'place', '会場', null, 'String', 255) }}
                      </span>
                    </div>
                  </div>

                  <!-- D-11 -->
                  <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm">
                    <label class="basicForm-label"> 開催目的 </label>

                    <div class="basicForm-control">
                      <el-input v-model="formData.hold_purpose" clearable class="form-control-textarea" :rows="7" type="textarea" placeholder="内容入力" />
                    </div>

                    <!-- D-12 -->
                    <div class="basicForm">
                      <label class="basicForm-label"> 備考 </label>
                      <div class="basicForm-control">
                        <el-input v-model="formData.remarks" clearable class="form-control-textarea" :rows="7" type="textarea" placeholder="内容入力" />
                      </div>
                    </div>
                  </div>

                  <!-- Mode view -->
                  <!-- basicGroup-tex -->
                  <div v-show="(lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode')" class="basicGroup-tex">
                    <ul>
                      <li class="w-100">
                        <ul class="d-flex">
                          <li class="w-50">
                            <p class="basicGroup-title basicGroup-title-view">開催区分</p>
                            <div class="basicGroup-lable">
                              <p class="basicGroup-txt">
                                {{ lstHoldingType.find((x) => x.hold_type_value === formData.hold_type)?.hold_type_label }}
                              </p>
                            </div>
                          </li>
                          <li v-if="formData.hold_type === '10'" class="w-50">
                            <p class="basicGroup-title basicGroup-title-view">シリーズ登録</p>
                            <div class="basicGroup-lable">
                              <p class="basicGroup-txt">
                                {{ formData.parent_series_flag == 1 ? 'する' : 'しない' }}
                              </p>
                            </div>
                          </li>
                          <li v-if="formData.hold_type === '20'" class="w-50">
                            <p class="basicGroup-title basicGroup-title-view">講演会シリーズ</p>
                            <div class="basicGroup-lable">
                              <p class="basicGroup-txt">
                                {{ formData.series_convention_name }}
                              </p>
                            </div>
                          </li>
                        </ul>
                      </li>
                      <li>
                        <p class="basicGroup-title basicGroup-title-view">品目</p>
                        <div class="basicGroup-lable">
                          <p>{{ formData?.convention_product?.map((x) => x.product_name)?.join(', ') }}</p>
                        </div>
                      </li>
                      <li>
                        <p class="basicGroup-title basicGroup-title-view">講演会区分</p>
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">
                            {{ lstConventionType.find((x) => x.convention_type == formData.convention_type)?.convention_type_label }}
                          </p>
                        </div>
                      </li>
                      <li class="w-100">
                        <p class="basicGroup-title basicGroup-title-view">開催方法</p>
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">
                            {{ lstHoldMethod.find((x) => x.hold_method_value === formData.hold_method)?.hold_method_label }}
                          </p>
                        </div>
                      </li>
                      <li class="w-100">
                        <p class="basicGroup-title basicGroup-title-view">講演会名</p>
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">{{ formData.convention_name }}</p>
                        </div>
                      </li>
                      <li class="w-100">
                        <p class="basicGroup-title basicGroup-title-view">日時</p>
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">
                            {{ formatFullDate(formData.start_date) }}　 {{ formData.start_time }}
                            <span v-if="formData.end_time">～</span>
                            {{ formData.end_time }}
                          </p>
                        </div>
                      </li>
                      <li class="w-100">
                        <p class="basicGroup-title basicGroup-title-view">会場</p>
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">{{ formData.place }}</p>
                        </div>
                      </li>
                      <li class="w-100">
                        <p class="basicGroup-title basicGroup-title-view">開催目的</p>
                        <div
                          class="basicGroup-txt content_item_scroll scrollbar scroll-child"
                          @mouseenter="childFocus"
                          @mouseleave="childFocusOut"
                          @touchend="childFocusOut"
                          @touchstart="childFocus"
                        >
                          {{ formData.hold_purpose }}
                        </div>
                      </li>
                      <li class="w-100">
                        <p class="basicGroup-title basicGroup-title-view">備考</p>
                        <div
                          class="basicGroup-txt content_item_scroll scrollbar scroll-child"
                          @mouseenter="childFocus"
                          @mouseleave="childFocusOut"
                          @touchend="childFocusOut"
                          @touchstart="childFocus"
                        >
                          {{ formData.remarks }}
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="basic-col">
                <!-- D-14 -> D-18 -->
                <div class="groupSection">
                  <div class="title">
                    <h2
                      class="title-tlt"
                      :class="
                        (lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode') ? 'basicGroup-title-view' : ''
                      "
                    >
                      資材
                    </h2>
                    <button
                      v-show="lstMode.includes('editMode') || lstMode.includes('createMode')"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius btn-addDoc"
                      @click="openModalZ05S05()"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" />
                      </span>
                      追加
                    </button>
                  </div>
                  <div class="boxMaterials">
                    <div ref="documentRef" class="listMaterials scrollbar">
                      <ul v-if="formData.convention_document.filter((x) => x.delete_flag !== -1).length > 0">
                        <li v-for="item in formData.convention_document" v-show="item.delete_flag !== -1" :key="item.document_id">
                          <div class="listMaterials-info">
                            <a class="listTitle title-link log-icon" title_log="資材名" @click="redirectToD01S02(item)">
                              <span class="listItem">
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
                            <span v-if="checkOutsideValid(item.start_date, item.end_date)" class="spanLabel spanLabel--red">適用期間外</span>
                          </div>
                          <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="listStatus-btn">
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
                                <li @click="deleteMaterial(item)">
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

                <!-- D-19 -> D-22 -->
                <div class="groupSection">
                  <div class="title title--pt36">
                    <h2
                      class="title-tlt"
                      :class="
                        (lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode') ? 'basicGroup-title-view' : ''
                      "
                    >
                      対象組織
                    </h2>
                  </div>
                  <div v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="lectureTarget">
                    <div class="lectureTarget-se">
                      <ul class="basicForm-btnSelect">
                        <li
                          v-for="item in lstOrgLayer"
                          :key="item.organization_layer_value"
                          :class="`${item.organization_layer_value == formData.organization_layer ? 'active' : ''} 
                          ${!item.organization_layer_value && !select_national_flag ? 'disabledOrg' : ''}`"
                          @click="selectOrgLayer(item.organization_layer_value)"
                        >
                          {{ item.organization_layer_label }}
                        </li>
                      </ul>
                    </div>
                    <div class="lectureTarget-add">
                      <button type="button" class="btn btn-outline-primary--cancel btn-outline-primary btn-radius" @click="openModalZ05S01()">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" />
                        </span>
                        追加
                      </button>
                    </div>
                  </div>
                  <div class="boxTarget">
                    <div class="listTarget scrollbar">
                      <ul v-if="formData.convention_target_org.filter((x) => x.delete_flag !== -1).length > 0">
                        <li v-for="item in formData.convention_target_org" v-show="item.delete_flag !== -1" :key="item.org_cd">
                          <span class="listTarget-tlt">{{ item.org_label }}</span>
                          <button v-show="lstMode.includes('editMode') || lstMode.includes('createMode')" class="btn btn--icon" @click="deleteOrg(item)">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                          </button>
                        </li>
                      </ul>
                      <EmptyData v-else-if="!loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                    </div>
                  </div>
                </div>

                <!-- D-23, D-24: File -->
                <div class="groupSection">
                  <div class="title title--pt36">
                    <h2
                      class="title-tlt"
                      :class="
                        (lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode') ? 'basicGroup-title-view' : ''
                      "
                    >
                      関連資料アップロード
                    </h2>

                    <input id="importFile" type="file" max-file-size="1024" :accept="fileTypes.toString()" multiple hidden @change="changeFile" />

                    <label
                      v-show="lstMode.includes('editMode') || lstMode.includes('createMode')"
                      class="btn btn-outline-primary--cancel btn-outline-primary btn-radius"
                      for="importFile"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_upload.svg')" alt="" />
                      </span>
                      ファイル選択
                    </label>
                  </div>
                  <div class="boxRelated">
                    <div :class="`listRelated ${loadingFileUpload ? 'pre-loader h-390' : ''}`">
                      <Preloader v-if="loadingFileUpload" />
                      <div v-else>
                        <ul v-if="formData.convention_file.filter((x) => x.delete_flag !== -1).length > 0">
                          <li
                            v-for="file in formData.convention_file"
                            v-show="file.delete_flag !== -1"
                            :key="file.display_name"
                            :class="`file ${file.downloading ? 'loadingDownload' : ''}`"
                          >
                            <span class="file-name" @click="downLoadFileS3(file)">{{ file.display_name }}</span>
                            <i
                              v-show="lstMode.includes('editMode') || lstMode.includes('createMode')"
                              :class="` ${file.downloading ? 'el-icon-loading' : 'el-icon-close'}`"
                              @click="removeFile(file)"
                            />
                          </li>
                        </ul>
                        <div v-else-if="!loadingPage" class="noData" style="height: 90px">
                          <div class="noData-content">
                            <p class="noData-text">該当するデータがありません。</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- E -->
            <div class="titleLecture titleLecture--mt40 titleLecture--mw440"><h2 class="tlt">開催形態</h2></div>
            <div class="basicRow">
              <div class="basic-col">
                <div class="basicGroup-form">
                  <!-- E-1 -->
                  <div class="holdingForm">
                    <label
                      class="holdingForm-label"
                      :class="
                        (lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode') ? 'basicGroup-title-view' : ''
                      "
                    >
                      開催形態
                    </label>

                    <ul v-if="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm-btnSelect">
                      <li
                        v-for="item in lstHoldingForm"
                        :key="item.hold_form_value"
                        :class="item.selected ? 'active' : ''"
                        @click="setSelectedHoldForm(item.hold_form_value)"
                      >
                        {{ item.hold_form_label }}
                      </li>
                    </ul>
                    <div v-else>
                      <div class="basicGroup-tex">
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">{{ lstHoldingForm.find((x) => x.hold_form_value === formData.hold_form)?.hold_form_label }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- E-2 -->
                  <div class="holdingTextarea">
                    <label
                      class="holdingTextarea-label"
                      :class="
                        (lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode') && !lstMode.includes('editMode')
                          ? 'basicGroup-title-view'
                          : ''
                      "
                      >共催相手</label
                    >
                    <div
                      v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                      class="holdingTextarea-control"
                      :class="checkValidateForm(false, 'cohost_partner_name', '共催相手', null, 'String', 40) ? 'hasErr' : ''"
                    >
                      <el-input
                        v-model="formData.cohost_partner_name"
                        :disabled="formData.hold_form == '10'"
                        class="form-control-textarea"
                        :rows="3"
                        type="textarea"
                        placeholder="内容入力"
                      />
                      <span v-if="checkValidateForm(false, 'cohost_partner_name', '共催相手', null, 'String', 40)" class="invalid">
                        {{ validationMessageForm(false, 'cohost_partner_name', '共催相手', null, 'String', 40) }}
                      </span>
                    </div>
                    <div v-else>
                      <div class="basicGroup-tex">
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">{{ formData.cohost_partner_name }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="basic-col">
                <div class="basicGroup-form">
                  <!-- E-3 -->
                  <div class="holdingForm">
                    <label
                      class="holdingForm-label"
                      :class="
                        (lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode') ? 'basicGroup-title-view' : ''
                      "
                    >
                      開催費分担
                    </label>
                    <ul v-if="lstMode.includes('editMode') || lstMode.includes('createMode')" class="basicForm-btnSelect">
                      <li
                        v-for="item in lstShareCost"
                        :key="item.cost_share_type_value"
                        :class="formData.hold_form == '10' ? 'disabled' : item.selected ? 'active' : ''"
                        @click="setSelectedCostShare(item.cost_share_type_value)"
                      >
                        {{ item.cost_share_type_label }}
                      </li>
                    </ul>
                    <div v-else>
                      <div class="basicGroup-tex">
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">
                            {{ lstShareCost.find((x) => x.cost_share_type_value === formData.cost_share_type)?.cost_share_type_label }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- E-4 -->
                  <div class="holdingTextarea">
                    <label
                      class="holdingTextarea-label"
                      :class="
                        (lstMode.includes('viewMode') || lstMode.includes('approvalMode')) && !lstMode.includes('editMode') ? 'basicGroup-title-view' : ''
                      "
                    >
                      開催費分担内容
                    </label>

                    <div
                      v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                      class="holdingTextarea-control"
                      :class="checkValidateForm(false, 'cost_share_content', '開催費分担内容', null, 'String', 40) ? 'hasErr' : ''"
                    >
                      <el-input
                        v-model="formData.cost_share_content"
                        :disabled="formData.hold_form == '10'"
                        class="form-control-textarea"
                        :rows="3"
                        type="textarea"
                        placeholder="内容入力"
                      />
                      <span v-if="checkValidateForm(false, 'cost_share_content', '開催費分担内容', null, 'String', 40)" class="invalid">
                        {{ validationMessageForm(false, 'cost_share_content', '開催費分担内容', null, 'String', 40) }}
                      </span>
                    </div>
                    <div v-else>
                      <div class="basicGroup-tex">
                        <div class="basicGroup-lable">
                          <p class="basicGroup-txt">
                            {{ formData.cost_share_content }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- F -->
            <div class="titleLecture titleLecture--mt40 titleLecture--mw440">
              <h2 class="tlt">出席者数</h2>
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="gotoScreenC01S03()">
                <span class="btn-iconLeft">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_multiple-users.svg')" alt="" />
                </span>
                出席者入力
              </button>
            </div>

            <div class="attendees">
              <div class="attendeesTbl scrollbar">
                <table>
                  <thead>
                    <tr>
                      <th></th>
                      <th v-for="item in lstAttendeesTitle" :key="item.status_item_cd">{{ item.status_item_name }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in lstAttendeesType" v-show="lstAttendeesType.length > 0" :key="item.occupation_type">
                      <td>{{ item.occupation_name }}</td>
                      <td v-for="itemSub in item.data_item" :key="itemSub.status_item_cd">
                        {{ itemSub.sum_count_user }} {{ itemSub.sum_count_user || parseInt(itemSub.sum_count_user) >= 0 ? '人' : '' }}
                      </td>
                    </tr>
                    <tr v-if="lstAttendeesType.length == 0 && !loadingPage">
                      <td :colspan="lstAttendeesTitle.length + 1">
                        <EmptyData v-if="!loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                      </td>
                    </tr>
                  </tbody>
                  <tfoot v-if="lstAttendeesType.length > 0">
                    <tr>
                      <td>合計</td>
                      <td v-for="sum in occupationSum" :key="sum.status_item_cd">{{ sum.sum }}人</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <!-- G -->
            <div class="titleLecture titleLecture--mt40 titleLecture--mw440">
              <h2 class="tlt">役割者</h2>
            </div>
            <div class="rolePerson scrollbar">
              <div
                v-for="(item, index) in dataNumberChange.convention_attendee"
                v-show="formData.convention_attendee.length > 0"
                :key="item.convention_attendee_id"
                class="role-row"
              >
                <div class="role-colLeft">
                  <ul>
                    <li class="w-100">
                      <p class="role-tlt">{{ item.facility_short_name }} {{ item.department_name }}</p>
                    </li>
                    <li>
                      <p class="role-name">
                        <a v-if="item.other_person_flag != 1" class="role-link role-name-first log-icon" title_log="個人名" @click="redirectToH02S02(item)">
                          {{ item.person_name }}
                        </a>
                        <a v-if="item.other_person_flag == 1" class="role-name-first">{{ item.person_name }} </a>
                        {{ item.display_position_name }}
                      </p>
                    </li>
                    <li>
                      <p class="role-label">
                        役割：<span class="role-span">{{ item.definition_label }} </span>
                      </p>
                    </li>
                  </ul>
                </div>
                <div class="role-colRight">
                  <div class="roleForm">
                    <div class="roleForm-title">謝金（税込）</div>
                    <div class="roleForm-info">
                      <ul class="roleForm-col3">
                        <li v-if="lstMode.includes('editMode') || lstMode.includes('createMode')">
                          <el-input
                            :ref="`gratuity_${index}`"
                            v-model="item.gratuity"
                            class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                            :maxlength="maxlengthGratuity"
                            :min="minPrice"
                            :max="maxPrice"
                            onkeydown="
                              javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                              ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                              "
                            @blur="formatCurrencyAttendee($event, index, minPrice, maxPrice, maxlengthGratuity, `gratuity_${index}`, '円')"
                            @focus="focusInput(`gratuity_${index}`, '円')"
                          >
                            <template #suffix> {{ item.gratuity ? '円' : '0 円' }} </template>
                          </el-input>
                        </li>
                        <li v-else>
                          <span class="roleForm-number"> {{ item.gratuity || 0 }} 円 </span>
                        </li>
                        <li>
                          <span class="roleForm-label">源泉取得税</span>
                          <span class="roleForm-number">{{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(item.withholding_tax) }} 円</span>
                        </li>
                        <li>
                          <span class="roleForm-label">手渡額</span>
                          <span class="roleForm-number">{{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(item.give_amount) }} 円</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="roleForm">
                    <div class="roleForm-title">演題</div>
                    <div class="roleForm-info">
                      <ul class="roleForm-col1">
                        <li v-if="(lstMode.includes('editMode') || lstMode.includes('createMode')) && item.part_type == '10'">
                          <el-input v-model="item.subject" clearable placeholder="題名入力" class="form-control-input-outline" />
                        </li>
                        <li v-else>
                          <span class="roleForm-number">{{ item.subject }}</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <EmptyData v-if="formData.convention_attendee.length == 0 && !loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
            </div>

            <!-- H -->
            <div class="titleLecture titleLecture--mt40 titleLecture--mw440"><h2 class="tlt">経費</h2></div>
            <div class="expenses">
              <div class="expenses-head">
                <div class="expenses-tabs">
                  <ul class="nav expensesTabs">
                    <li>
                      <a :class="!status_check || parseInt(status_check) < 30 ? 'active' : ''" data-toggle="tab" href="#tabs-1" role="tab">
                        <span>{{ lstExpense.find((x) => x.expense_classification_value === '10')?.expense_classification_label }}</span>
                      </a>
                    </li>
                    <li>
                      <a :class="parseInt(status_check) > 20 ? 'active' : 'disabled'" data-toggle="tab" href="#tabs-2" role="tab">
                        <span>{{ lstExpense.find((x) => x.expense_classification_value === '20')?.expense_classification_label }}</span>
                      </a>
                    </li>
                  </ul>
                </div>

                <!-- expense_num -->
                <div class="expenses-expense">
                  <span class="expenses-label">経費番号</span>
                  <div class="expenses-input">
                    <div class="groupName">
                      <div class="form-icon">
                        <el-input
                          v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                          v-model="formData.expense_num"
                          maxlength="51"
                          placeholder="番号入力"
                          clearable
                          class="form-control-input"
                        />
                        <span v-else class="roleForm-number"> {{ formData.expense_num }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="expenses-content">
                <div class="tab-content">
                  <!-- Plan -->
                  <div id="tabs-1" :class="!status_check || parseInt(status_check) < 30 ? 'active' : ''" class="tab-pane" role="tabpanel">
                    <div v-show="formData.area_expense.length > 0" class="formGroup">
                      <div class="expensesForm scrollbar">
                        <el-collapse v-model="activeNameItemPlan" class="custom-collapse-radius">
                          <el-collapse-item
                            v-for="(itemExpense, index) in dataNumberChange.area_expense"
                            :key="itemExpense.expense_item_cd"
                            class="accordionForm"
                            :name="itemExpense.expense_item_name"
                          >
                            <template #title>
                              <div class="accordionHead" :class="activeNameItemPlan.includes(itemExpense.expense_item_name) ? 'accordionHead--show' : ''">
                                <h3 class="accordion-tlt">{{ itemExpense.expense_item_name }}</h3>
                                <p class="accordion-number">
                                  小計&ensp;{{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(itemExpense.plan_amount) }}円
                                </p>
                              </div>
                            </template>

                            <div class="accordionContent" :class="activeNameItemPlan.includes(itemExpense.expense_item_name) ? 'accordionContent--show' : ''">
                              <div class="groupExp">
                                <div
                                  v-for="(itemLayer2, index2) in itemExpense.layer_2"
                                  v-show="itemLayer2.option_item_flag != 1"
                                  :key="itemLayer2.expense_item_cd"
                                  :class="checkLayer3(index) ? 'formgroupExp' : 'formgroupExp borderbt-none'"
                                >
                                  <div class="formgroupEx-left">{{ itemLayer2.layer_3 ? itemLayer2.expense_item_name : '' }}</div>
                                  <div class="formgroupEx-right">
                                    <div v-if="itemLayer2.layer_3">
                                      <div v-if="checkInputQuanlity(itemLayer2.layer_3)">
                                        <div v-for="(itemLayer3, index3) in itemLayer2.layer_3" :key="itemLayer3.expense_item_cd" class="formWrap">
                                          <div class="formCol-label">{{ itemLayer3.expense_item_name }}</div>
                                          <div class="formCol">
                                            <el-input
                                              v-if="
                                                itemLayer3.unit_price_input_flag &&
                                                itemLayer3.quantity_input_flag &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`quanlity_itemLayer3_plan_unit_price_${index}${index2}${index3}`"
                                              v-model="itemLayer3.plan_unit_price"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseUnitPrice"
                                              onkeydown="
                                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_unit_price',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  itemLayer3.quantity_input_flag,
                                                  3,
                                                  false,
                                                  maxlengthExpenseUnitPrice,
                                                  `quanlity_itemLayer3_plan_unit_price_${index}${index2}${index3}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quanlity_itemLayer3_plan_unit_price_${index}${index2}${index3}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer3.plan_unit_price ? '円' : '0 円' }} </template>
                                            </el-input>

                                            <span
                                              v-if="
                                                itemLayer3.unit_price_input_flag &&
                                                itemLayer3.quantity_input_flag &&
                                                (!(lstMode.includes('editMode') || lstMode.includes('createMode')) ||
                                                  (status_check && parseInt(status_check) >= 30))
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer3.plan_unit_price || 0 }} 円
                                            </span>
                                          </div>
                                          <div class="formCol">
                                            <el-input
                                              v-if="
                                                itemLayer3.quantity_input_flag &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`quanlity_itemLayer3_plan_quantity_${index}${index2}${index3}`"
                                              v-model="itemLayer3.plan_quantity"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseQuantity"
                                              onkeydown="
                                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_quantity',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  itemLayer3.unit_price_input_flag,
                                                  3,
                                                  false,
                                                  maxlengthExpenseQuantity,
                                                  `quanlity_itemLayer3_plan_quantity_${index}${index2}${index3}`,
                                                  itemLayer3.quantity_unit_label
                                                )
                                              "
                                              @focus="
                                                focusInput(`quanlity_itemLayer3_plan_quantity_${index}${index2}${index3}`, itemLayer3.quantity_unit_label)
                                              "
                                            >
                                              <template #suffix>
                                                {{ itemLayer3.plan_quantity ? itemLayer3.quantity_unit_label : `0 ${itemLayer3.quantity_unit_label}` }}
                                              </template>
                                            </el-input>

                                            <span
                                              v-if="
                                                itemLayer3.quantity_input_flag &&
                                                (!(lstMode.includes('editMode') || lstMode.includes('createMode')) ||
                                                  (status_check && parseInt(status_check) >= 30))
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer3.plan_quantity || 0 }}
                                              {{ itemLayer3.quantity_unit_label }}
                                            </span>
                                          </div>
                                          <div class="formCol">
                                            <span
                                              v-if="
                                                (itemLayer3.unit_price_input_flag && itemLayer3.quantity_input_flag) ||
                                                !(lstMode.includes('editMode') || lstMode.includes('createMode')) ||
                                                (status_check && parseInt(status_check) >= 30)
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer3.plan_amount || 0 }} 円
                                            </span>
                                            <el-input
                                              v-if="
                                                (!itemLayer3.unit_price_input_flag || !itemLayer3.quantity_input_flag) &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`quanlity_itemLayer3_plan_amount_${index}${index2}${index3}`"
                                              v-model="itemLayer3.plan_amount"
                                              :class="`form-control-input-outline formFs-16 formText-right customCurrency-suffix`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  false,
                                                  3,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `quanlity_itemLayer3_plan_amount_${index}${index2}${index3}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quanlity_itemLayer3_plan_amount_${index}${index2}${index3}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer3.plan_amount ? '円' : '0 円' }} </template>
                                            </el-input>
                                          </div>
                                        </div>
                                      </div>

                                      <div v-else>
                                        <div v-for="(itemLayer3, index3) in itemLayer2.layer_3" :key="itemLayer3.expense_item_cd" class="formWrap">
                                          <div class="formg-labelCircle">{{ itemLayer3.expense_item_name }}</div>
                                          <div class="formCircle">
                                            <el-input
                                              v-if="
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`itemLayer3_plan_amount_${index}${index2}${index3}`"
                                              v-model="itemLayer3.plan_amount"
                                              :class="`form-control-input-outline formFs-16 formText-right customCurrency-suffix`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  false,
                                                  3,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `itemLayer3_plan_amount_${index}${index2}${index3}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`itemLayer3_plan_amount_${index}${index2}${index3}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer3.plan_amount ? '円' : '0 円' }} </template>
                                            </el-input>

                                            <span v-else class="formCol-text"> {{ itemLayer3.plan_amount || 0 }} 円 </span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div v-else>
                                      <div v-if="checkInputQuanlity(itemExpense.layer_2)">
                                        <div class="formWrap">
                                          <div class="formCol-label">{{ itemLayer2.expense_item_name }}</div>
                                          <div class="formCol">
                                            <el-input
                                              v-if="
                                                itemLayer2.unit_price_input_flag &&
                                                itemLayer2.quantity_input_flag &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`quantity_itemLayer2_plan_unit_price_${index}${index2}`"
                                              v-model="itemLayer2.plan_unit_price"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseUnitPrice"
                                              onkeydown="
                                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_unit_price',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  itemLayer2.quantity_input_flag,
                                                  2,
                                                  false,
                                                  maxlengthExpenseUnitPrice,
                                                  `quantity_itemLayer2_plan_unit_price_${index}${index2}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quantity_itemLayer2_plan_unit_price_${index}${index2}`, '円')"
                                            >
                                              <template #suffix>
                                                {{ itemLayer2.plan_unit_price ? '円' : '0 円' }}
                                              </template>
                                            </el-input>

                                            <span
                                              v-if="
                                                itemLayer2.unit_price_input_flag &&
                                                itemLayer2.quantity_input_flag &&
                                                (!(lstMode.includes('editMode') || lstMode.includes('createMode')) ||
                                                  (status_check && parseInt(status_check) >= 30))
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer2.plan_unit_price || 0 }} 円
                                            </span>
                                          </div>
                                          <div class="formCol">
                                            <el-input
                                              v-if="
                                                itemLayer2.quantity_input_flag &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`quantity_itemLayer2_plan_quantity_${index}${index2}`"
                                              v-model="itemLayer2.plan_quantity"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseQuantity"
                                              onkeydown="
                                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_quantity',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  itemLayer2.unit_price_input_flag,
                                                  2,
                                                  false,
                                                  maxlengthExpenseQuantity,
                                                  `quantity_itemLayer2_plan_quantity_${index}${index2}`,
                                                  itemLayer2.quantity_unit_label
                                                )
                                              "
                                              @focus="focusInput(`quantity_itemLayer2_plan_quantity_${index}${index2}`, itemLayer2.quantity_unit_label)"
                                            >
                                              <template #suffix>
                                                {{ itemLayer2.plan_quantity ? itemLayer2.quantity_unit_label : `0 ${itemLayer2.quantity_unit_label}` }}
                                              </template>
                                            </el-input>

                                            <span
                                              v-if="
                                                itemLayer2.quantity_input_flag &&
                                                (!(lstMode.includes('editMode') || lstMode.includes('createMode')) ||
                                                  (status_check && parseInt(status_check) >= 30))
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer2.plan_quantity || 0 }}
                                              {{ itemLayer2.quantity_unit_label }}
                                            </span>
                                          </div>
                                          <div class="formCol">
                                            <span
                                              v-if="
                                                (itemLayer2.unit_price_input_flag && itemLayer2.quantity_input_flag) ||
                                                !(lstMode.includes('editMode') || lstMode.includes('createMode')) ||
                                                (status_check && parseInt(status_check) >= 30)
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer2.plan_amount || 0 }} 円
                                            </span>
                                            <el-input
                                              v-if="
                                                (!itemLayer2.unit_price_input_flag || !itemLayer2.quantity_input_flag) &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`quantity_itemLayer2_plan_amount_${index}${index2}`"
                                              v-model="itemLayer2.plan_amount"
                                              :class="`form-control-input-outline formFs-16 formText-right customCurrency-suffix`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  false,
                                                  2,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `quantity_itemLayer2_plan_amount_${index}${index2}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quantity_itemLayer2_plan_amount_${index}${index2}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer2.plan_amount ? '円' : '0 円' }} </template>
                                            </el-input>
                                          </div>
                                        </div>
                                      </div>

                                      <div v-else>
                                        <div class="formWrap">
                                          <div class="formg-labelCircle">{{ itemLayer2.expense_item_name }}</div>
                                          <div class="formCircle">
                                            <el-input
                                              v-if="
                                                (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                                (!status_check || parseInt(status_check) < 30)
                                              "
                                              :ref="`itemLayer2_plan_amount_${index}${index2}`"
                                              v-model="itemLayer2.plan_amount"
                                              :class="`form-control-input-outline formFs-16 formText-right customCurrency-suffix`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'plan',
                                                  'plan_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  false,
                                                  2,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `itemLayer2_plan_amount_${index}${index2}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`itemLayer2_plan_amount_${index}${index2}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer2.plan_amount ? '円' : '0 円' }} </template>
                                            </el-input>

                                            <span v-else class="formCol-text"> {{ itemLayer2.plan_amount || 0 }} 円 </span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <div
                                  v-for="(itemLayer2, index2) in itemExpense.layer_2"
                                  v-show="
                                    itemLayer2.option_item_flag == 1 &&
                                    (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                    (!status_check || parseInt(status_check) < 30)
                                  "
                                  :key="itemLayer2.expense_item_cd"
                                  class="formothers"
                                >
                                  <div class="formothers-col1" :class="itemLayer2.plan_amount && !itemLayer2.option_item_name?.trim() ? 'hasErr' : ''">
                                    <el-input
                                      v-model="itemLayer2.option_item_name"
                                      placeholder="任意項目"
                                      class="form-control-input-outline formFs-16"
                                      @input="changeOptionItem(index, index2, 'option_item_name')"
                                    />
                                    <span v-if="itemLayer2.plan_amount && !itemLayer2.option_item_name?.trim()" class="invalid">
                                      {{ getMessage('MSFA0001') }}
                                    </span>
                                  </div>
                                  <div class="formothers-col1" :class="itemLayer2.plan_amount && !itemLayer2.option_item_content?.trim() ? 'hasErr' : ''">
                                    <el-input
                                      v-model="itemLayer2.option_item_content"
                                      placeholder="内容"
                                      class="form-control-input-outline formFs-16"
                                      @input="changeOptionItem(index, index2, 'option_item_content')"
                                    />
                                    <span v-if="itemLayer2.plan_amount && !itemLayer2.option_item_content?.trim()" class="invalid">
                                      {{ getMessage('MSFA0001') }}
                                    </span>
                                  </div>
                                  <div class="formothers-col2">
                                    <el-input
                                      :ref="`other_itemLayer2_plan_amount_${index}${index2}`"
                                      v-model="itemLayer2.plan_amount"
                                      :class="`form-control-input-outline formFs-16 formText-right customCurrency-suffix`"
                                      :maxlength="maxlengthExpenseAmount"
                                      onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                      @blur="
                                        formatCurrency(
                                          $event,
                                          'area_expense',
                                          'plan',
                                          'plan_amount',
                                          item,
                                          index,
                                          index2,
                                          null,
                                          false,
                                          2,
                                          true,
                                          maxlengthExpenseAmount,
                                          `other_itemLayer2_plan_amount_${index}${index2}`,
                                          '円'
                                        )
                                      "
                                      @focus="focusInput(`other_itemLayer2_plan_amount_${index}${index2}`, '円')"
                                    >
                                      <template #suffix> {{ itemLayer2.plan_amount ? '円' : '0 円' }} </template>
                                    </el-input>
                                  </div>
                                </div>

                                <div
                                  v-for="itemLayer2 in itemExpense.layer_2"
                                  v-show="
                                    itemLayer2.option_item_flag == 1 &&
                                    (!(lstMode.includes('editMode') || lstMode.includes('createMode')) || (status_check && parseInt(status_check) >= 30)) &&
                                    (itemLayer2.option_item_content || itemLayer2.option_item_name || itemLayer2.plan_amount)
                                  "
                                  :key="itemLayer2.expense_item_cd"
                                  class="formothers"
                                >
                                  <div class="formothers-col1">
                                    <span class="formCol-text">
                                      {{ itemLayer2.option_item_name }}
                                    </span>
                                  </div>
                                  <div class="formothers-col1">
                                    <span class="formCol-text">
                                      {{ itemLayer2.option_item_content }}
                                    </span>
                                  </div>
                                  <div class="formothers-col2">
                                    <span class="formCol-text"> {{ itemLayer2.plan_amount || 0 }}円 </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </el-collapse-item>
                        </el-collapse>
                      </div>
                    </div>

                    <!-- formTotal Plan -->
                    <div v-show="formData.area_expense.length > 0" class="formTotal">
                      <div class="formTotal-label">{{ formData.area_expense_sum.expense_item_name }}</div>
                      <div class="formTotal-number">
                        {{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(formData.area_expense_sum.plan_amount) }} 円
                      </div>
                    </div>

                    <EmptyData v-if="formData.area_expense.length == 0 && !loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                  </div>

                  <!-- Result -->
                  <div id="tabs-2" :class="parseInt(status_check) > 20 ? 'active' : ''" class="tab-pane" role="tabpanel">
                    <div v-show="formData.area_expense.length > 0" class="formGroup">
                      <div class="expensesForm scrollbar">
                        <el-collapse v-model="activeNameItemResult" class="custom-collapse-radius">
                          <el-collapse-item
                            v-for="(itemExpense, index) in dataNumberChange.area_expense"
                            :key="itemExpense.expense_item_cd"
                            class="accordionForm"
                            :name="itemExpense.expense_item_name"
                          >
                            <template #title>
                              <div class="accordionHead" :class="activeNameItemResult.includes(itemExpense.expense_item_name) ? 'accordionHead--show' : ''">
                                <h3 class="accordion-tlt">{{ itemExpense.expense_item_name }}</h3>
                                <p class="accordion-number" :class="itemExpense.result_amount !== itemExpense.plan_amount ? 'accordion-number--red' : ''">
                                  小計&ensp;{{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(formData.area_expense?.[index]?.result_amount) }}円
                                </p>
                              </div>
                            </template>

                            <div class="accordionContent" :class="activeNameItemResult.includes(itemExpense.expense_item_name) ? 'accordionContent--show' : ''">
                              <div class="groupExp">
                                <div
                                  v-for="(itemLayer2, index2) in itemExpense.layer_2"
                                  v-show="itemLayer2.option_item_flag != 1"
                                  :key="itemLayer2.expense_item_cd"
                                  :class="itemLayer2.layer_3 ? 'formgroupExp' : 'formgroupExp borderbt-none'"
                                >
                                  <div class="formgroupEx-left">{{ itemLayer2.layer_3 ? itemLayer2.expense_item_name : '' }}</div>
                                  <div class="formgroupEx-right">
                                    <div v-if="itemLayer2.layer_3">
                                      <!-- one quantity_input_flag === 1 -->
                                      <div v-if="checkInputQuanlity(itemLayer2.layer_3)">
                                        <div v-for="(itemLayer3, index3) in itemLayer2.layer_3" :key="itemLayer3.expense_item_cd" class="formWrap">
                                          <div class="formCol-label">{{ itemLayer3.expense_item_name }}</div>

                                          <!-- layer 3 -->
                                          <!-- unit_price_input_flag -->
                                          <div class="formCol">
                                            <el-input
                                              v-if="
                                                itemLayer3.unit_price_input_flag &&
                                                itemLayer3.quantity_input_flag &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              :ref="`quanlity_itemLayer3_result_unit_price_${index}${index2}${index3}`"
                                              v-model="itemLayer3.result_unit_price"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseUnitPrice"
                                              onkeydown="
                                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_unit_price',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  itemLayer3.quantity_input_flag,
                                                  3,
                                                  false,
                                                  maxlengthExpenseUnitPrice,
                                                  `quanlity_itemLayer3_result_unit_price_${index}${index2}${index3}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quanlity_itemLayer3_result_unit_price_${index}${index2}${index3}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer3.result_unit_price ? '円' : '0 円' }} </template>
                                            </el-input>

                                            <span
                                              v-if="
                                                itemLayer3.unit_price_input_flag &&
                                                itemLayer3.quantity_input_flag &&
                                                !(lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer3.result_unit_price || 0 }} 円
                                            </span>
                                          </div>

                                          <!-- quantity_input_flag -->
                                          <div class="formCol">
                                            <el-input
                                              v-if="itemLayer3.quantity_input_flag && (lstMode.includes('editMode') || lstMode.includes('createMode'))"
                                              :ref="`quanlity_itemLayer3_result_quantity_${index}${index2}${index3}`"
                                              v-model="itemLayer3.result_quantity"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseQuantity"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_quantity',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  itemLayer3.unit_price_input_flag,
                                                  3,
                                                  false,
                                                  maxlengthExpenseQuantity,
                                                  `quanlity_itemLayer3_result_quantity_${index}${index2}${index3}`,
                                                  itemLayer3.quantity_unit_label
                                                )
                                              "
                                              @focus="
                                                focusInput(`quanlity_itemLayer3_result_quantity_${index}${index2}${index3}`, itemLayer3.quantity_unit_label)
                                              "
                                            >
                                              <template #suffix>
                                                {{ itemLayer3.result_quantity ? itemLayer3.quantity_unit_label : `0 ${itemLayer3.quantity_unit_label}` }}
                                              </template>
                                            </el-input>

                                            <span
                                              v-if="itemLayer3.quantity_input_flag && !(lstMode.includes('editMode') || lstMode.includes('createMode'))"
                                              class="formCol-text"
                                            >
                                              {{ itemLayer3.result_quantity || 0 }}
                                              {{ itemLayer3.quantity_unit_label }}
                                            </span>
                                          </div>

                                          <!-- amount -->
                                          <div class="formCol">
                                            <span
                                              v-if="
                                                (itemLayer3.unit_price_input_flag && itemLayer3.quantity_input_flag) ||
                                                !(lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              class="formCol-text"
                                              :class="itemLayer3.result_amount !== itemLayer3.plan_amount ? 'accordion-number--red' : ''"
                                            >
                                              {{ itemLayer3.result_amount || 0 }} 円
                                            </span>
                                            <el-input
                                              v-if="
                                                (!itemLayer3.unit_price_input_flag || !itemLayer3.quantity_input_flag) &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              :ref="`quanlity_itemLayer3_result_amount_${index}${index2}${index3}`"
                                              v-model="itemLayer3.result_amount"
                                              :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix ${
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.layer_3?.[index3]?.result_amount !==
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.layer_3?.[index3]?.plan_amount
                                                  ? 'accordion-number--red'
                                                  : ''
                                              }`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  false,
                                                  3,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `quanlity_itemLayer3_result_amount_${index}${index2}${index3}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quanlity_itemLayer3_result_amount_${index}${index2}${index3}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer3.result_amount ? '円' : '0 円' }} </template>
                                            </el-input>
                                          </div>
                                        </div>
                                      </div>

                                      <!-- all quantity_input_flag === 0 -->
                                      <div v-else>
                                        <div v-for="(itemLayer3, index3) in itemLayer2.layer_3" :key="itemLayer3.expense_item_cd" class="formWrap">
                                          <div class="formg-labelCircle">{{ itemLayer3.expense_item_name }}</div>
                                          <div class="formCircle">
                                            <el-input
                                              v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                                              :ref="`itemLayer3_result_amount_${index}${index2}${index3}`"
                                              v-model="itemLayer3.result_amount"
                                              :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix ${
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.layer_3?.[index3]?.result_amount !==
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.layer_3?.[index3]?.plan_amount
                                                  ? 'accordion-number--red'
                                                  : ''
                                              }`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  index3,
                                                  false,
                                                  3,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `itemLayer3_result_amount_${index}${index2}${index3}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`itemLayer3_result_amount_${index}${index2}${index3}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer3.result_amount ? '円' : '0 円' }} </template>
                                            </el-input>

                                            <span
                                              v-else
                                              class="formCol-text"
                                              :class="itemLayer3.result_amount !== itemLayer3.plan_amount ? 'accordion-number--red' : ''"
                                            >
                                              {{ itemLayer3.result_amount || 0 }} 円
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div v-else>
                                      <div v-if="checkInputQuanlity(itemExpense.layer_2)">
                                        <div class="formWrap">
                                          <div class="formCol-label">{{ itemLayer2.expense_item_name }}</div>
                                          <div class="formCol">
                                            <el-input
                                              v-if="
                                                itemLayer2.unit_price_input_flag &&
                                                itemLayer2.quantity_input_flag &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              :ref="`quantity_itemLayer2_result_unit_price_${index}${index2}`"
                                              v-model="itemLayer2.result_unit_price"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseUnitPrice"
                                              onkeydown="
                                                javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_unit_price',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  itemLayer2.quantity_input_flag,
                                                  2,
                                                  false,
                                                  maxlengthExpenseUnitPrice,
                                                  `quantity_itemLayer2_result_unit_price_${index}${index2}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quantity_itemLayer2_result_unit_price_${index}${index2}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer2.result_unit_price ? '円' : '0 円' }} </template>
                                            </el-input>

                                            <span
                                              v-if="
                                                itemLayer2.unit_price_input_flag &&
                                                itemLayer2.quantity_input_flag &&
                                                !(lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              class="formCol-text"
                                            >
                                              {{ itemLayer2.result_unit_price || 0 }} 円
                                            </span>
                                          </div>
                                          <div class="formCol">
                                            <el-input
                                              v-if="itemLayer2.quantity_input_flag && (lstMode.includes('editMode') || lstMode.includes('createMode'))"
                                              :ref="`quantity_itemLayer2_result_quantity_${index}${index2}`"
                                              v-model="itemLayer2.result_quantity"
                                              class="input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix"
                                              :maxlength="maxlengthExpenseQuantity"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_quantity',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  itemLayer2.unit_price_input_flag,
                                                  2,
                                                  false,
                                                  maxlengthExpenseQuantity,
                                                  `quantity_itemLayer2_result_quantity_${index}${index2}`,
                                                  itemLayer2.quantity_unit_label
                                                )
                                              "
                                              @focus="focusInput(`quantity_itemLayer2_result_quantity_${index}${index2}`, itemLayer2.quantity_unit_label)"
                                            >
                                              <template #suffix>
                                                {{ itemLayer2.result_quantity ? itemLayer2.quantity_unit_label : `0 ${itemLayer2.quantity_unit_label}` }}
                                              </template>
                                            </el-input>

                                            <span
                                              v-if="itemLayer2.quantity_input_flag && !(lstMode.includes('editMode') || lstMode.includes('createMode'))"
                                              class="formCol-text"
                                            >
                                              {{ itemLayer2.result_quantity || 0 }}
                                              {{ itemLayer2.quantity_unit_label }}
                                            </span>
                                          </div>
                                          <div class="formCol">
                                            <span
                                              v-if="
                                                (itemLayer2.unit_price_input_flag && itemLayer2.quantity_input_flag) ||
                                                !(lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              class="formCol-text"
                                              :class="
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.result_amount !==
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.plan_amount
                                                  ? 'accordion-number--red'
                                                  : ''
                                              "
                                            >
                                              {{ itemLayer2.result_amount || 0 }} 円
                                            </span>

                                            <el-input
                                              v-if="
                                                (!itemLayer2.unit_price_input_flag || !itemLayer2.quantity_input_flag) &&
                                                (lstMode.includes('editMode') || lstMode.includes('createMode'))
                                              "
                                              :ref="`quantity_itemLayer2_result_amount_${index}${index2}`"
                                              v-model="itemLayer2.result_amount"
                                              :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix ${
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.result_amount !==
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.plan_amount
                                                  ? 'accordion-number--red'
                                                  : ''
                                              }`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  false,
                                                  2,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `quantity_itemLayer2_result_amount_${index}${index2}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`quantity_itemLayer2_result_amount_${index}${index2}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer2.result_amount ? '円' : '0 円' }} </template>
                                            </el-input>
                                          </div>
                                        </div>
                                      </div>

                                      <div v-else>
                                        <div class="formWrap">
                                          <div class="formg-labelCircle">{{ itemLayer2.expense_item_name }}</div>
                                          <div class="formCircle">
                                            <el-input
                                              v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                                              :ref="`itemLayer2_result_amount_${index}${index2}`"
                                              v-model="itemLayer2.result_amount"
                                              :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix ${
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.result_amount !==
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.plan_amount
                                                  ? 'accordion-number--red'
                                                  : ''
                                              }`"
                                              :maxlength="maxlengthExpenseAmount"
                                              onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                              @blur="
                                                formatCurrency(
                                                  $event,
                                                  'area_expense',
                                                  'result',
                                                  'result_amount',
                                                  item,
                                                  index,
                                                  index2,
                                                  null,
                                                  false,
                                                  2,
                                                  true,
                                                  maxlengthExpenseAmount,
                                                  `itemLayer2_result_amount_${index}${index2}`,
                                                  '円'
                                                )
                                              "
                                              @focus="focusInput(`itemLayer2_result_amount_${index}${index2}`, '円')"
                                            >
                                              <template #suffix> {{ itemLayer2.result_amount ? '円' : '0 円' }} </template>
                                            </el-input>

                                            <span
                                              v-else
                                              class="formCol-text"
                                              :class="
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.result_amount !==
                                                formData.area_expense?.[index]?.layer_2?.[index2]?.plan_amount
                                                  ? 'accordion-number--red'
                                                  : ''
                                              "
                                            >
                                              {{ itemLayer2.result_amount || 0 }} 円
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <div
                                  v-for="(itemLayer2, index2) in itemExpense.layer_2"
                                  v-show="itemLayer2.option_item_flag == 1"
                                  :key="itemLayer2.expense_item_cd"
                                  class="formothers"
                                >
                                  <div class="formothers-col1" :class="itemLayer2.plan_amount && !itemLayer2.option_item_name?.trim() ? 'hasErr' : ''">
                                    <el-input
                                      v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                                      v-model="itemLayer2.option_item_name"
                                      placeholder="任意項目"
                                      class="form-control-input-outline formFs-16"
                                      @input="changeOptionItem(index, index2, 'option_item_name')"
                                    />
                                    <span v-else class="formCol-text">
                                      {{ itemLayer2.option_item_name }}
                                    </span>
                                    <span
                                      v-if="
                                        (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                        itemLayer2.plan_amount &&
                                        !itemLayer2.option_item_name?.trim()
                                      "
                                      class="invalid"
                                    >
                                      {{ getMessage('MSFA0001') }}
                                    </span>
                                  </div>
                                  <div class="formothers-col1" :class="itemLayer2.plan_amount && !itemLayer2.option_item_content?.trim() ? 'hasErr' : ''">
                                    <el-input
                                      v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                                      v-model="itemLayer2.option_item_content"
                                      placeholder="内容"
                                      class="form-control-input-outline formFs-16"
                                      @input="changeOptionItem(index, index2, 'option_item_content')"
                                    />
                                    <span v-else class="formCol-text">
                                      {{ itemLayer2.option_item_content }}
                                    </span>
                                    <span
                                      v-if="
                                        (lstMode.includes('editMode') || lstMode.includes('createMode')) &&
                                        itemLayer2.plan_amount &&
                                        !itemLayer2.option_item_content?.trim()
                                      "
                                      class="invalid"
                                    >
                                      {{ getMessage('MSFA0001') }}
                                    </span>
                                  </div>
                                  <div class="formothers-col2">
                                    <el-input
                                      v-if="lstMode.includes('editMode') || lstMode.includes('createMode')"
                                      :ref="`other_itemLayer2_result_amount_${index}${index2}`"
                                      v-model="itemLayer2.result_amount"
                                      :class="`input-currency form-control-input-outline formFs-16 formText-right customCurrency-suffix ${
                                        formData.area_expense?.[index]?.layer_2?.[index2]?.result_amount !==
                                        formData.area_expense?.[index]?.layer_2?.[index2]?.plan_amount
                                          ? 'accordion-number--red'
                                          : ''
                                      }`"
                                      :maxlength="maxlengthExpenseAmount"
                                      onkeydown="
                                                  javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                                                  ? true : !isNaN(Number(event.key)) && event.code!=='Space' 
                                                  "
                                      @blur="
                                        formatCurrency(
                                          $event,
                                          'area_expense',
                                          'result',
                                          'result_amount',
                                          item,
                                          index,
                                          index2,
                                          null,
                                          false,
                                          2,
                                          true,
                                          maxlengthExpenseAmount,
                                          `other_itemLayer2_result_amount_${index}${index2}`,
                                          '円'
                                        )
                                      "
                                      @focus="focusInput(`other_itemLayer2_result_amount_${index}${index2}`, '円')"
                                    >
                                      <template #suffix> {{ itemLayer2.result_amount ? '円' : '0 円' }} </template>
                                    </el-input>

                                    <span
                                      v-else
                                      class="formCol-text"
                                      :class="
                                        formData.area_expense?.[index]?.layer_2?.[index2]?.result_amount !==
                                        formData.area_expense?.[index]?.layer_2?.[index2]?.plan_amount
                                          ? 'accordion-number--red'
                                          : ''
                                      "
                                    >
                                      {{ itemLayer2.option_item_content && itemLayer2.option_item_name ? `${itemLayer2.result_amount || 0} 円` : '' }}
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </el-collapse-item>
                        </el-collapse>
                      </div>
                    </div>
                    <!-- formTotal Result-->
                    <div v-show="formData.area_expense.length > 0" class="formTotal">
                      <div class="formTotal-label">{{ formData.area_expense_sum.expense_item_name }}</div>
                      <div
                        class="formTotal-number"
                        :class="formData.area_expense_sum.result_amount !== formData.area_expense_sum.plan_amount ? 'formTotal-number--red' : ''"
                      >
                        {{ new Intl.NumberFormat('ja-JP', { currency: 'JPY' }).format(formData.area_expense_sum.result_amount) }} 円
                      </div>
                    </div>

                    <EmptyData v-if="formData.area_expense.length == 0 && !loadingPage" icon="amico_A01S03" custom-class="nodata noData200" />
                  </div>
                </div>
              </div>
            </div>

            <!-- I -->
            <div class="bento">
              <div v-if="lstMode.includes('editMode') || lstMode.includes('createMode')" class="bentoForm">
                <ul>
                  <li v-if="parseInt(status_check) >= 30" class="w-100">
                    <div class="holdingForm">
                      <label class="holdingForm-label">弁当処分方法</label>
                      <ul class="basicForm-btnSelect basicForm-btnSelect--col4">
                        <li
                          v-for="item in lstBentoDisposal"
                          :key="item.bento_disposal_value"
                          :class="item.selected ? 'active' : ''"
                          @click="setSelectedBentoDisposal(item.bento_disposal_value)"
                        >
                          {{ item.bento_disposal_label }}
                        </li>
                      </ul>
                    </div>
                  </li>
                  <li>
                    <label class="bentoForm-label">差異理由および今後の対策</label>
                    <div class="bentoForm-control">
                      <el-input
                        v-model="formData.reason"
                        clearable
                        class="form-control-textarea"
                        :rows="6"
                        :disabled="!status_check || parseInt(status_check) < 30"
                        type="textarea"
                        placeholder="内容入力"
                      />
                    </div>
                  </li>
                  <li>
                    <label class="bentoForm-label">特記事項</label>
                    <div class="bentoForm-control">
                      <el-input v-model="formData.note" clearable class="form-control-textarea" rows="6" type="textarea" placeholder="内容入力" />
                    </div>
                  </li>
                </ul>
              </div>
              <div v-else class="basicGroup-tex">
                <ul>
                  <li v-if="parseInt(status_check) >= 30" class="w-100">
                    <p class="basicGroup-title basicGroup-title-view">弁当処分方法</p>
                    <div class="basicGroup-lable">
                      <p class="basicGroup-txt">
                        {{ lstBentoDisposal.find((x) => x.bento_disposal_value === formData.disposal_technique)?.bento_disposal_label }}
                      </p>
                    </div>
                  </li>
                  <li>
                    <p class="basicGroup-title basicGroup-title-view">差異理由および今後の対策</p>
                    <div
                      class="basicGroup-txt content_item_scroll scrollbar scroll-child"
                      @mouseenter="childFocus"
                      @mouseleave="childFocusOut"
                      @touchend="childFocusOut"
                      @touchstart="childFocus"
                    >
                      {{ formData.reason }}
                    </div>
                  </li>
                  <li>
                    <p class="basicGroup-title basicGroup-title-view">特記事項</p>
                    <div
                      class="basicGroup-txt content_item_scroll scrollbar scroll-child"
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

            <!-- J -->
            <JElementButton
              :key="keyComponentButton"
              :form-data="formData"
              :old-form-data="oldFormData"
              :permissions="{
                policyRole: roleUser,
                isUserTargetOrg: isUserTargetOrg,
                isUserApprover: isUserApprover,
                isUserPlaner: isUserPlaner,
                status_check: status_check,
                lstMode: lstMode,
                document_usage_type: document_usage_type,
                active_layer_approval: active_layer_approval
              }"
              @reload="reloadData"
              @submit="submitButtonClick"
              @loadingTrue="loadingTrue"
              @loadingFalse="loadingFalse"
              @callApiCopyData="copyData"
              @scrollTop="scrollTop"
              @back="goToBack"
            ></JElementButton>
          </div>
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
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="handleConfirmYesC1S3"></component>
      </template>
    </el-dialog>

    <!-- Dialog confirm change D-3 -->
    <el-dialog
      v-model="modalConfig.isShowModalConfirmInput"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :show-close="false"
      :close-on-click-modal="modalConfig.closeOnClickMark"
    >
      <div class="modal-confirm">
        <div class="confirmContent">
          <img :src="require(`@/assets/template/images/icon_alert.svg`)" alt="iconAlert" />
          <h3 class="title">
            基本情報をシリーズの内容で更新します。<br />
            既に入力済の項目があれば上書きされます。よろしいですか？
          </h3>
        </div>

        <div class="confirmBtn">
          <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelChangeData()">いいえ</button>
          <button type="button" class="btn btn-primary mr-0" @click="handleConfirmYesChangeData()">はい</button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
/* eslint-disable eqeqeq */
/* eslint-disable max-len */
import { markRaw } from 'vue';
import Z05S06MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import C01_S04_LectureSeriesSelection from '@/views/C01/C01_S04_LectureSeriesSelection/C01_S04_LectureSeriesSelection';
import Z05_S01_Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import Z05_S05_Choice_Of_Masterial from '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial';
import D01_S07_MaterialViewer from '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer';
import D01_S02_ModalMaterialDetails from '@/views/D01/D01_S02_MaterialDetails/D01_S02_ModalMaterialDetails';
import C01_S02_ModalComment from './C01_S02_ModalComment.vue';
import JElementButton from './C01_S02_JElementButton.vue';
import C01_S02_LectureInputService from '@/api/C01/C01_S02_LectureInputService';
import { encodeData } from '@/api/base64_api';

import { isEqual, chain } from 'lodash';
import { Auth } from '@/api';
import axios from 'axios';
import { getCookie } from '@/utils/constants';
import PerfectScrollbar from 'perfect-scrollbar';

export default {
  name: 'C01_S02_LectureInput',
  components: { JElementButton, C01_S02_ModalComment },

  props: {
    checkLoading: [Boolean],

    convention_id: {
      type: String,
      require: false,
      default: ''
    },

    // redirect from  copy convention
    convention_copy: {
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
    }
  },

  data: function () {
    return {
      loadingPage: false,
      saveLoading: false,
      currentUser: null,
      psContainer: null,

      maxlengthGratuity: 8,
      maxlengthExpenseAmount: 12,
      maxlengthExpenseQuantity: 4,
      maxlengthExpenseUnitPrice: 8,

      lstStatusType: [], // A-2

      lstHoldingType: [], // D-1
      lstConventionType: [], // D-4
      lstHoldMethod: [], // D-5
      lstOrgLayer: [], // D-19

      lstHoldingForm: [], // E-1
      lstShareCost: [], // E-3

      lstAttendeesTitle: [], // F
      lstAttendeesType: [], // F
      lstAttendees: [], // F
      occupationSum: [],

      lstMaterialsDefault: [],
      lstProductDefault: [],
      lstFileDefault: [],
      lstOrgsDefault: [],
      orgLayerDefault: '',

      // I
      lstBentoDisposal: [], // I-1

      // H
      lstExpense: [],
      tabType: '',
      tabName: '',
      minPrice: 0,
      maxPrice: 0,

      // G
      lstTaxRate: [],

      // check mode
      curentUser: {},
      convention_id_default: '',
      schedule_id_default: '',
      endDateInputConvention: '',
      roleUser: [],
      isUserTargetOrg: false,
      isUserApprover: false,
      isUserPlaner: false,
      remand_flag: false,
      status_check: '',
      lstMode: [],
      activeNameItemPlan: '',
      activeNameItemResult: '',
      keyComponentButton: Math.random(),
      keyComponentTimepicker: Math.random(),
      copyFlag: false,
      isSubmitBtn: false,
      layer_user_approval: [],
      finalLayerApproval: 1,
      layerApproval: 1,

      showBtnSave: false,

      modalConfig: {
        title: '',
        isShowModal: false,
        isShowModalConfirmInput: false,
        isShowModalConfirmSave: false,
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
        mode: 'multiple',
        userSelectFlag: 0,
        layerMax: 5,
        layerMin: 1,
        orgCdList: []
      },

      status_report_approval: false,
      active_layer_approval: 1,

      convention_plan: [],
      convention_result: [],
      convention_comment: [],

      document_usage_type: null,

      oldFormData: {},
      formDataDefault: {},
      dataNumberChange: {},
      formData: {
        convention_id: '',
        hold_type: '', // D-1
        parent_series_flag: 0, // D-2
        series_convention_id: '', // D-3
        series_convention_name: '', // D-3
        convention_type: '', // D-4
        hold_method: '', // D-5
        convention_name: '', // D-6
        start_date: '', // D-7
        start_time: '', // D-8
        end_time: '', // D-9
        place: '', // D-10
        hold_purpose: '', // D-11
        remarks: '', // D-12
        convention_product: [], // D-13
        convention_document: [], // D-15
        convention_target_org: [], // D-20
        organization_layer: '',
        convention_file: [],

        // E
        hold_form: '', // E-1
        cohost_partner_name: '', // E-2
        cost_share_type: '', // E-3
        cost_share_content: '', // E-4

        // G
        convention_attendee: [],

        // H
        expense_num: null,
        area_expense: [],
        area_expense_sum: {},

        // I
        disposal_technique: '', // I-1
        note: '', // I-3
        reason: '', // I-2,

        schedule_id: ''
      },

      dataSeries: {},
      dataSeriesItemChange: {},
      dataLabel: [],

      fileTypes: [
        '.doc',
        '.docx',
        '.xml',
        'application/msword',
        'text/plain',
        'application/pdf',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/png',
        'image/jpg',
        'image/jpeg',
        'image/gif',
        'image/x-icon',
        'image/svg+xml',
        'audio/mpeg',
        'audio/mp3',
        'video/mp4'
      ],
      loadingFileUpload: false,
      isGoToC1S3: false,
      permission: null,
      lstOrgSelectAll: [],
      select_national_flag: false,
      stepType: '',

      onScrollTop: 0
    };
  },

  watch: {
    formData() {
      this.keyComponentButton = Math.random();
    }
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

  mounted() {
    this.emitter.emit('change-header', {
      title: '講演会入力',
      icon: 'icon_lecture_color.svg',
      isShowBack: true
    });

    this.currentUser = this.getCurrentUser();

    if (this.convention_id) {
      this.convention_id_default = this.convention_id;
      this.copyFlag = false;
    }

    let router = this._route('C01_S02_LectureInput')?.params;

    if (router?.convention_id) {
      this.convention_id_default = router?.convention_id;
      this.copyFlag = false;
    }

    if (router?.convention_copy) {
      this.convention_id_default = router?.convention_copy;
      this.copyFlag = true;
    }

    if (router?.schedule_id) {
      this.schedule_id_default = router?.schedule_id;
    }

    if (router?.start) {
      this.convention_id_default = '';
      this.schedule_id_default = '';
      let time = router?.start;
      let endDate = new Date(new Date(time).setHours(new Date(time).getHours() + 1));

      if (router?.allDay) {
        endDate = new Date(new Date(time).setHours(23, 59, 59, 0));
      }

      this.formData.start_date = this.formatFullDate(time);
      this.formData.start_time = this.formatTimeHourMinute(time);
      this.formData.end_time = this.formatTimeHourMinute(endDate);
      this.keyComponentTimepicker = Math.random();
    }
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.C01_S02_LectureInput;
    this.checkPermission();

    this.psContainer = new PerfectScrollbar('#conventionSession', {
      wheelSpeed: 0.5
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

    async formatCurrencyAttendee(event, index, minValue, maxValue, maxLength, refName, label) {
      let value = event.target.value;
      let num = await this.convertToHalf(value);
      num = Number(num.replace(/[^0-9.-]+/g, ''))
        ?.toString()
        ?.substring(0, maxLength);

      let number = num;
      let currency = num;

      number = Number(number.replace(/[^0-9.-]+/g, ''));
      if (number < minValue) {
        number = minValue;
      }
      if (number > maxValue) {
        number = maxValue;
      }

      currency = this.convertNumberToCurrency(number);

      this.dataNumberChange.convention_attendee[index].gratuity = currency;

      this.focusInput(refName, currency ? label : `0 ${label}`, true, currency);

      if (this.formData.convention_attendee[index].gratuity !== number) {
        this.formData.convention_attendee[index].gratuity = number;
        this.changeAmountTax(this.formData.convention_attendee[index]);
      }
    },

    async formatCurrency(event, data, type, field, item, index, index2, index3, isChange, layerNumber, calcSum, maxLength, refName, label) {
      let value = event.target.value;
      let num = await this.convertToHalf(value);
      num = Number(num.replace(/[^0-9.-]+/g, ''))
        ?.toString()
        ?.substring(0, maxLength);

      let number = num;
      let currency = num;

      currency = this.convertNumberToCurrency(currency);

      number = Number(number.replace(/[^0-9.-]+/g, ''));

      if (data == 'area_expense') {
        let change = false;
        switch (layerNumber) {
          case 2:
            this.dataNumberChange[data][index].layer_2[index2][field] = currency;
            if (this.formData[data][index].layer_2[index2][field] !== number) {
              change = true;
              this.formData[data][index].layer_2[index2][field] = number;
            }
            break;
          case 3:
            this.dataNumberChange[data][index].layer_2[index2].layer_3[index3][field] = currency;
            if (this.formData[data][index].layer_2[index2].layer_3[index3][field] !== number) {
              change = true;
              this.formData[data][index].layer_2[index2].layer_3[index3][field] = number;
            }
            break;
        }

        this.focusInput(refName, currency ? label : `0 ${label}`, true, currency);

        if (change) {
          if (type === 'plan') {
            if (isChange) {
              this.changeAmountExpensePlan(index, index2, index3);
            }
            if (calcSum) {
              this.sumPlan(index);
            }
          } else {
            if (isChange) {
              this.changeAmountExpenseResult(index, index2, index3);
            }
            if (calcSum) {
              this.sumResult(index);
            }
          }
        }
      }
    },

    convertCurrencyDataDefault(datas) {
      let data = { ...datas };
      data.area_expense.forEach((item, index) => {
        let layer1 = item;
        layer1.layer_2.forEach((sitem, sindex2) => {
          let layer2 = sitem;
          layer2.plan_unit_price = this.convertNumberToCurrency(layer2.plan_unit_price);
          layer2.plan_quantity = this.convertNumberToCurrency(layer2.plan_quantity);
          layer2.plan_amount = this.convertNumberToCurrency(layer2.plan_amount);

          layer2.result_unit_price = this.convertNumberToCurrency(layer2.result_unit_price);
          layer2.result_quantity = this.convertNumberToCurrency(layer2.result_quantity);
          layer2.result_amount = this.convertNumberToCurrency(layer2.result_amount);

          if (layer2.layer_3) {
            layer2.layer_3.forEach((ssitem, sindex3) => {
              let layer3 = ssitem;

              layer3.plan_unit_price = this.convertNumberToCurrency(layer3.plan_unit_price);
              layer3.plan_quantity = this.convertNumberToCurrency(layer3.plan_quantity);
              layer3.plan_amount = this.convertNumberToCurrency(layer3.plan_amount);

              layer3.result_unit_price = this.convertNumberToCurrency(layer3.result_unit_price);
              layer3.result_quantity = this.convertNumberToCurrency(layer3.result_quantity);
              layer3.result_amount = this.convertNumberToCurrency(layer3.result_amount);

              layer2.layer_3[sindex3] = layer3;
            });
          }

          layer1.layer_2[sindex2] = layer2;
        });
        data.area_expense[index] = layer1;
      });

      let dataCheck = this.convertCurrencyAttendeeDefault(data);

      return dataCheck;
    },

    convertCurrencyAttendeeDefault(datas) {
      let data = { ...datas };
      data.convention_attendee.forEach((item, index) => {
        let attend = item;
        attend.gratuity = this.convertNumberToCurrency(attend.gratuity);
        data.convention_attendee[index] = attend;
      });

      return data;
    },

    convertNumberToCurrency(number) {
      if (!number || number == '0') return '';
      let currency = number
        ?.toString()
        ?.replace(/\$\s?|(,*)/g, '')
        ?.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

      return currency;
    },

    onScroll({ target: { scrollTop } }) {
      this.setScrollScreen('C01_S02_LectureInput', scrollTop);
    },
    async checkPermission() {
      this.loadingPage = true;
      let user_cd = this.currentUser?.user_cd;
      await Auth.getPolicyRoleService({ user_cd })
        .then((res) => {
          this.permission = res.data?.data;
          this.getScreenDataInfo();
        })
        .catch(() => {
          this.loadingPage = false;
        });
    },
    goToBack() {
      this.back();
    },

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

    disabledDateStart(time) {
      let date = new Date(new Date(time).setHours(0, 0, 0, 0));
      if (!this.convention_id_default) {
        return date.getTime() <= new Date(new Date().setHours(0, 0, 0, 0)).getTime();
      }
    },

    checkDisabledDate() {
      if (!this.endDateInputConvention) {
        return false;
      }

      let startTime = new Date(new Date(this.oldFormData.start_date).setHours(0, 0, 0, 0))?.getTime();
      let today = new Date(new Date()?.setHours(0, 0, 0, 0))?.getTime();

      return startTime <= today;
    },

    checkModeUser() {
      // viewMode, editMode, createMode, approvalMode
      this.lstMode = [];
      let startTime = new Date(new Date(this.formData.start_date).setHours(0, 0, 0, 0))?.getTime();
      let systemTime = new Date(new Date(this.endDateInputConvention)?.setHours(23, 59, 59, 59))?.getTime();
      let today = new Date(new Date()?.setHours(0, 0, 0, 0))?.getTime();

      let datePeriod = this.endDateInputConvention ? (systemTime < startTime ? true : false) : true;

      if (datePeriod) {
        if (this.convention_id_default || this.schedule_id_default) {
          if (this.status_check == '30' || this.status_check == '40') {
            if (today <= startTime) {
              if (this.permission?.includes('R31') && (this.isUserPlaner || this.isUserTargetOrg)) {
                this.lstMode.push('editMode');
              } else {
                this.lstMode.push('viewMode');
              }
            } else {
              if (this.permission?.includes('R30') && (this.isUserPlaner || this.isUserTargetOrg)) {
                this.lstMode.push('editMode');
              } else {
                this.lstMode.push('viewMode');
              }
              if (this.isUserApprover) {
                this.lstMode.push('viewMode');
              }
            }
          } else {
            if (this.status_check == '10') {
              if (this.permission?.includes('R30')) {
                if (this.isUserPlaner || this.isUserTargetOrg) {
                  this.lstMode.push('editMode');
                } else {
                  this.lstMode.push('viewMode');
                }
              } else {
                this.lstMode.push('viewMode');
              }

              if (this.isUserApprover) {
                this.lstMode.push('viewMode');
              }
            } else {
              if (this.status_check == '20' || this.status_check == '50') {
                if (this.isUserApprover) {
                  this.lstMode.push('approvalMode');
                }

                if (
                  (this.permission?.includes('R30') && (this.isUserPlaner || this.isUserTargetOrg)) ||
                  (!this.permission?.includes('R30') && !this.isUserApprover)
                ) {
                  this.lstMode.push('viewMode');
                } else {
                  this.lstMode.push('viewMode');
                }
              } else {
                if (this.status_check == '60' || this.status_check == '70') {
                  this.lstMode.push('viewMode');
                }
              }
            }
          }
        } else {
          if (this.permission?.includes('R30')) {
            this.lstMode.push('createMode');
          }
        }
      } else {
        this.lstMode.push('viewMode');
      }

      this.keyComponentButton = Math.random();
    },

    getScreenDataInfo() {
      C01_S02_LectureInputService.getScreenDataInfo()
        .then((res) => {
          let data = res.data.data;

          this.select_national_flag = data.select_national_flag;

          this.lstStatusType = data.status_type;

          this.lstHoldingType = data.hold_type;
          this.lstConventionType = data.convention_type;
          this.lstHoldMethod = data.hold_method;
          this.lstOrgLayer = data.organization_layer;

          this.lstOrgLayer.unshift({
            organization_layer_value: '',
            organization_layer_label: '全国'
          });

          this.lstHoldingForm = data.hold_form;
          this.lstShareCost = data.cost_share_type;

          this.lstAttendeesTitle = data.convention_status_item;

          this.lstTaxRate = this.getTaxRate();

          this.lstExpense = data.expense_classification;

          this.lstBentoDisposal = data.bento_disposal;

          this.document_usage_type = data.document_usage_type;

          this.roleUser = this.$store.state.auth.policyRole;

          this.rangePrice();

          this.setTabType(this.lstExpense[0].expense_classification_value);
          this.getTabName(this.lstExpense[0].expense_classification_value);
          this.oldFormData = JSON.parse(JSON.stringify(this.formData));
          this.formDataDefault = JSON.parse(JSON.stringify(this.formData));
          this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));

          if (this.convention_id_default) {
            if (this.copyFlag) {
              this.copyData();
            } else {
              this.getDataDetail('convention', this.convention_id_default);
            }
          } else {
            if (this.schedule_id_default) {
              this.getDataDetail('schedule', this.schedule_id_default);
            } else {
              if (this.lstOrgLayer.length > 0) {
                this.formData.organization_layer = this.currentUser?.layer_num;
                this.orgLayerDefault = this.currentUser?.layer_num;
                let orgs = [
                  {
                    delete_flag: 1,
                    layer_num: this.currentUser?.layer_num,
                    org_cd: this.currentUser?.org_cd,
                    org_label: this.currentUser?.org_label
                  }
                ];
                this.formData = {
                  ...this.formData,
                  convention_target_org: orgs,
                  area_expense: data.convention_detail.area_expense || [],
                  area_expense_sum: data.convention_detail.area_expense_sum || {}
                };
              }
              this.setSelectedHoldingType(this.lstHoldingType[0].hold_type_value);
              this.setSelectedHoldMethod(this.lstHoldMethod[0].hold_method_value);
              this.setSelectedHoldForm(this.lstHoldingForm[0].hold_form_value);
              this.oldFormData = JSON.parse(JSON.stringify(this.formData));
              this.formDataDefault = JSON.parse(JSON.stringify(this.formData));
              this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));
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
          this.psContainer = new PerfectScrollbar('#conventionSession', {
            wheelSpeed: 0.5
          });
          if (this.$refs.contentLectureRef) {
            this.$refs.contentLectureRef.scrollTop = this.onScrollTop;
          }
        });
    },

    getDataDetail(type, id) {
      this.loadingPage = true;
      let params = {
        convention_id: type === 'convention' ? id : '',
        schedule_id: type === 'schedule' ? id : ''
      };
      C01_S02_LectureInputService.getDataDetail(params)
        .then(async (res) => {
          let data = res.data.data;
          let detail = data.convention_detail;

          this.convention_plan = data.convention_plan;
          this.convention_result = data.convention_result;

          this.isUserApprover = data.user_approval == 1 ? true : false;
          this.layer_user_approval = data.layer_user_approval;
          this.status_report_approval = data.status_report_approval;
          this.active_layer_approval = data.active_layer_approval;

          if (detail) {
            this.isUserPlaner = detail.plan_user_cd && detail.plan_user_cd === this.currentUser.user_cd ? true : false;
            this.isUserTargetOrg = detail.user_org;
            this.status_check = detail.status_type;
            this.remand_flag = detail.remand_flag == 1 ? true : false;

            this.endDateInputConvention = detail.end_date;
            this.convention_id_default = detail.convention_id;

            let lstFile =
              detail.convention_file.map((x) => {
                return {
                  ...x,
                  old_name: x.display_name,
                  downloading: false
                };
              }) || [];

            this.formData = {
              ...this.formData,
              convention_id: detail.convention_id,
              hold_type: detail.hold_type,
              parent_series_flag: detail.parent_series_flag,
              series_convention_id: detail.series_convention_id,
              series_convention_name: detail.series_convention_name,
              convention_type: detail.convention_type,
              hold_method: detail.hold_method,
              convention_name: detail.convention_name,
              start_date: this.formatFullDate(detail.start_date) || '',
              start_time: this.formatTimeHourMinute(detail.start_time),
              end_time: this.formatTimeHourMinute(detail.end_time),
              place: detail.place,
              hold_purpose: detail.hold_purpose,
              remarks: detail.remarks,
              convention_product: detail.convention_product || [],
              convention_document: detail.convention_document || [],
              convention_target_org: detail.convention_target_org || [],
              convention_file: lstFile,
              hold_form: detail.hold_form,
              cohost_partner_name: detail.cohost_partner_name,
              cost_share_type: detail.cost_share_type,
              cost_share_content: detail.cost_share_content,
              expense_num: detail.expense_num,

              convention_attendee: detail.convention_attendee || [],
              convention_occupation_type: detail.convention_occupation_type || [],

              area_expense: detail.area_expense || [],
              area_expense_sum: detail.area_expense_sum || {},

              disposal_technique: detail.disposal_technique,
              note: detail.note,
              reason: detail.reason,

              schedule_id: detail.schedule_id,
              status_type: detail.status_type
            };

            this.lstAttendeesType = detail.convention_occupation_type || [];
            this.caclSumOccupation();

            this.setSelectedHoldingType(detail.hold_type ? detail.hold_type : this.lstHoldingType[0].hold_type_value);
            this.setSelectedHoldMethod(detail.hold_method ? detail.hold_method : this.lstHoldMethod[0].hold_method_value);
            this.setSelectedHoldForm(detail.hold_form ? detail.hold_form : this.lstHoldingForm[0].hold_form_value);
            this.setSelectedCostShare(detail.cost_share_type ? detail.cost_share_type : this.lstShareCost[0].cost_share_type_value);
            this.setSelectedBentoDisposal(detail.disposal_technique ? detail.disposal_technique : '');

            if (detail.convention_target_org?.length > 0) {
              this.formData.organization_layer = detail.convention_target_org[0].layer_num;
              this.orgLayerDefault = detail.convention_target_org[0].layer_num;
              this.lstOrgsDefault = [...detail.convention_target_org];
            }

            if (detail.area_expense?.length > 0) {
              this.activeNameItemPlan = detail.area_expense[0].expense_item_name;
              this.activeNameItemResult = detail.area_expense[0].expense_item_name;
            }

            this.lstMaterialsDefault = JSON.parse(JSON.stringify(detail.convention_document || []));
            this.lstProductDefault = JSON.parse(JSON.stringify(detail.convention_product || []));
            this.lstFileDefault = JSON.parse(JSON.stringify(lstFile));

            await new Promise((resolve) => setTimeout(resolve, 100));

            this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));
            this.checkLayerUserApproval();
          } else {
            this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));
          }

          this.calcSumHDefault(+this.status_check > 20 ? 'result' : 'plan');
          await new Promise((resolve) => setTimeout(resolve, 100));
          this.oldFormData = JSON.parse(JSON.stringify(this.formData));
          this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));

          if (this.copyFlag) {
            this.copyData();
          }

          this.keyComponentTimepicker = Math.random();

          this.checkModeUser();
        })
        .catch((err) => {
          this.loadingPage = false;
          if (err.response?.data?.message) {
            this.$notify({ message: err.response?.data?.message, customClass: 'error' });
          }
        })
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 200));
          this.psContainer = new PerfectScrollbar('#conventionSession', {
            wheelSpeed: 0.5
          });
          if (this.$refs.contentLectureRef) {
            this.$refs.contentLectureRef.scrollTop = this.onScrollTop;
          }
          if (this.$refs.documentRef) {
            this.$refs.documentRef.scrollTop = 1;
          }
          if (!this.copyFlag) {
            this.loadingPage = false;
          }

          this.$router.replace({ params: {} });
        });
    },

    scrollTop() {
      this.onScrollTop = 0;
      if (this.$refs.contentLectureRef) {
        this.$refs.contentLectureRef.scrollTop = 0;
      }
      if (this.$refs.documentRef) {
        this.$refs.documentRef.scrollTop = 1;
      }
    },

    copyData() {
      this.loadingPage = true;
      let params = {
        convention_id: this.convention_id_default
      };

      C01_S02_LectureInputService.copyData(params)
        .then((res) => {
          let detail = res.data.data;
          this.convention_id_default = '';
          this.status_check = '';
          this.schedule_id_default = '';
          this.endDateInputConvention = '';
          this.remand_flag = false;
          this.isSubmitBtn = false;

          this.formData = {
            ...this.formDataDefault,
            ...detail,
            area_expense_sum: detail.area_expense_sum || {}
          };

          this.lstAttendeesType = detail.convention_occupation_type || [];
          this.caclSumOccupation();

          this.setSelectedHoldForm(detail.hold_form ? detail.hold_form : this.lstHoldingForm[0].hold_form_value);
          this.setSelectedCostShare(detail.cost_share_type ? detail.cost_share_type : this.lstHoldingForm[0].cost_share_type_value);
          this.setSelectedHoldingType(this.lstHoldingType[0].hold_type_value);
          this.setSelectedHoldMethod(this.lstHoldMethod[0].hold_method_value);
          this.setSelectedHoldForm(this.lstHoldingForm[0].hold_form_value);

          if (detail.area_expense?.length > 0) {
            this.activeNameItemPlan = detail.area_expense[0].expense_item_name;
            this.activeNameItemResult = detail.area_expense[0].expense_item_name;
          }

          this.oldFormData = JSON.parse(JSON.stringify(this.formData));
          this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));
          this.checkModeUser();
          this.$router.replace({ params: {} });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.copyFlag = false;
          this.loadingPage = false;

          this.scrollTop();
        });
    },

    // B
    handleClickStatus(step) {
      this.stepType = step;
      this.convention_comment = [];
      if (step == 'step20' || step == 'step50') {
        if (step == 'step20') {
          this.convention_comment = [...this.convention_plan];
        } else {
          this.convention_comment = [...this.convention_result];
        }
        this.convention_comment = this.convertCommentByLayer('approval_layer_num', this.convention_comment);
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
      let layerConventions = [];
      this.isUserApprover = false;

      if (this.layer_user_approval.length === 0) {
        this.isUserApprover = false;
      } else {
        if (this.status_check == '20' || this.status_check == '50') {
          if (this.status_check == '20') {
            layerConventions = this.convertCommentByLayer('approval_layer_num', this.convention_plan);
          }

          if (this.status_check == '50') {
            layerConventions = this.convertCommentByLayer('approval_layer_num', this.convention_result);
          }

          this.finalLayerApproval = layerConventions.length;

          for (let x in layerConventions) {
            let layer = layerConventions[x];
            if (layer.approval_layer_num == this.active_layer_approval) {
              this.layerConventionsFlats = layer.data;
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

    // J
    submitButtonClick() {
      this.isSubmitBtn = true;
    },

    loadingTrue() {
      // this.loadingPage = true;
      this.saveLoading = true;
    },

    loadingFalse() {
      // this.loadingPage = false;
      this.saveLoading = false;
    },

    async reloadData(e) {
      if (e && e.copyFlag) {
        let convention = e.convention_id;
        this.getDataDetail('convention', convention);
      } else {
        if (this.convention_id_default || this.schedule_id) {
          if (this.convention_id_default) {
            this.getDataDetail('convention', this.convention_id_default);
          }
          if (this.schedule_id) {
            this.getDataDetail('schedule', this.schedule_id);
          }
        } else {
          if (this.lstOrgLayer.length > 0) {
            this.formData.organization_layer = this.lstOrgLayer[0].organization_layer_value;
            this.orgLayerDefault = this.lstOrgLayer[0].organization_layer_value;
            this.lstOrgsDefault = [];
          }
          this.setSelectedHoldingType(this.lstHoldingType[0].hold_type_value);
          this.setSelectedHoldMethod(this.lstHoldMethod[0].hold_method_value);
          this.setSelectedHoldForm(this.lstHoldingForm[0].hold_form_value);
          this.loadingPage = false;
          this.oldFormData = JSON.parse(JSON.stringify(this.formData));
          this.dataNumberChange = this.convertCurrencyDataDefault(JSON.parse(JSON.stringify(this.formData)));
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
        component: markRaw(C01_S02_ModalComment),
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
    // D-1
    setSelectedHoldingType(item) {
      this.formData.hold_type = item;
      this.lstHoldingType.forEach((x) => {
        x.selected = false;
        if (x.hold_type_value === item) {
          x.selected = true;
        }
      });
    },

    // D-3
    openModalLectureSeriesC01S04() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '講演会シリーズ選択',
        customClass: 'custom-dialog modal-fixed',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(C01_S04_LectureSeriesSelection),
        width: '45rem',
        props: {}
      };
    },

    onResultModalLectureSeriesC01S04(e) {
      this.onCloseModal();
      this.dataSeriesItemChange = e;
      this.selectionSeries(e.convention_id, e);
    },

    selectionSeries(convention_id) {
      let params = {
        convention_id: convention_id
      };
      C01_S02_LectureInputService.selectionSeries(params)
        .then((res) => {
          let data = res.data.data;
          let convention_product = data.convention_product.map((x) => {
            return {
              ...x,
              delete_flag: 1
            };
          });
          this.dataSeries = {
            ...data,
            convention_product: convention_product
          };
          let keyFormData = {
            D: {
              content: '基本情報',
              field: {
                convention_id: '',
                hold_type: '開催区分', // D-1
                parent_series_flag: 'シリーズ登録', // D-2
                series_convention_id: '講演会シリーズ', // D-3
                series_convention_name: '講演会シリーズ', // D-3
                convention_type: '講演会区分', // D-4
                hold_method: '開催方法', // D-5
                convention_name: '講演会名', // D-6
                start_date: '開催日', // D-7
                start_time: '開始時刻', // D-8
                end_time: '終了時刻', // D-9
                place: '会場', // D-10
                hold_purpose: '開催目的', // D-11
                remarks: '備考', // D-12
                convention_product: '品目', // D-13
                convention_document: '資材', // D-15
                convention_target_org: '対象組織', // D-20
                convention_file: '関連資料アップロード'
              }
            },
            E: {
              content: '開催形態',
              field: {
                hold_form: '開催形態', // E-1
                cohost_partner_name: '共催相手', // E-2
                cost_share_type: '開催費分担', // E-3
                cost_share_content: '開催費分担内容' // E-4
              }
            }
          };
          let D = keyFormData['D'];
          let E = keyFormData['E'];
          this.dataLabel = [];

          Object.keys(this.dataSeries).forEach((x) => {
            Object.keys(D['field']).forEach((d) => {
              let index = this.dataLabel.findIndex((lab) => lab.parent === D['content']);
              if (d == x && x !== 'convention_id') {
                if (index === -1) {
                  this.dataLabel.push({
                    parent: D['content'],
                    child: [D['field'][d]]
                  });
                } else {
                  this.dataLabel[index].child.push(D['field'][d]);
                }
              }
            });

            Object.keys(E['field']).forEach((e) => {
              let index = this.dataLabel.findIndex((lab) => lab.parent === E['content']);
              if (e == x && x !== 'convention_id') {
                if (index === -1) {
                  this.dataLabel.push({
                    parent: E['content'],
                    child: [E['field'][e]]
                  });
                } else {
                  this.dataLabel[index].child.push(E['field'][e]);
                }
              }
            });
          });

          if (Object.keys(this.dataSeries).length > 0) {
            this.openModalConfirmInput();
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    openModalConfirmInput() {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        customClass: 'custom-dialog modal-confirm-custom modal-fixed modal-fixed-min',
        isShowModalConfirmInput: true,
        isShowModal: false,
        isShowClose: false,
        destroyOnClose: true,
        width: '36rem',
        props: {}
      };
    },

    handleConfirmYesChangeData() {
      this.onCloseModal();

      this.formData = {
        ...this.formData,
        ...this.dataSeries,
        convention_id: this.formData.convention_id,
        series_convention_id: this.dataSeriesItemChange.convention_id,
        series_convention_name: this.dataSeriesItemChange.convention_name
      };

      this.setSelectedHoldingType(this.dataSeries.hold_type ? this.dataSeries.hold_type.toString() : this.lstHoldingType[0].hold_type_value);
      this.setSelectedHoldMethod(this.dataSeries.hold_method ? this.dataSeries.hold_method.toString() : this.lstHoldMethod[0].hold_method_value);
      this.setSelectedHoldForm(this.dataSeries.hold_form ? this.dataSeries.hold_form.toString() : this.lstHoldingForm[0].hold_form_value);
      this.setSelectedCostShare(this.dataSeries.cost_share_type ? this.dataSeries.cost_share_type.toString() : this.lstHoldingForm[0].cost_share_type_value);

      if (this.dataSeries.convention_product && this.dataSeries.convention_product.length > 0) {
        this.formData.convention_product = [...this.lstProductDefault];
        for (let i = 0; i < this.lstProductDefault.length; i++) {
          let element = this.lstProductDefault[i];
          let index = this.formData.convention_product.findIndex((e) => e.product_cd == element.product_cd);
          if (!this.dataSeries.convention_product.some((x) => x.product_cd == element.product_cd)) {
            this.formData.convention_product[index].delete_flag = -1;
          } else {
            this.formData.convention_product[index].delete_flag = 0;
          }
        }

        for (let j = 0; j < this.dataSeries.convention_product.length; j++) {
          let element = this.dataSeries.convention_product[j];
          let index = this.formData.convention_product.findIndex((e) => e.product_cd == element.product_cd);
          if (index === -1) {
            this.formData.convention_product.push({
              product_cd: element.product_cd,
              product_name: element.product_name,
              delete_flag: 1
            });
          }
        }
      }

      if (this.dataSeries.convention_target_org && this.dataSeries.convention_target_org.length > 0) {
        this.formData.convention_target_org = [...this.lstOrgsDefault];
        this.orgLayerDefault = this.dataSeries.convention_target_org[0].layer_num;
        this.formData.organization_layer = this.dataSeries.convention_target_org[0].layer_num;

        for (let i = 0; i < this.lstOrgsDefault.length; i++) {
          let element = this.lstOrgsDefault[i];
          let index = this.formData.convention_target_org.findIndex((e) => e.org_cd == element.org_cd);
          if (!this.dataSeries.convention_target_org.some((x) => x.org_cd == element.org_cd)) {
            this.formData.convention_target_org[index].delete_flag = -1;
          } else {
            this.formData.convention_target_org[index].delete_flag = 0;
          }
        }

        for (let j = 0; j < this.dataSeries.convention_target_org.length; j++) {
          let element = this.dataSeries.convention_target_org[j];
          let index = this.formData.convention_target_org.findIndex((e) => e.org_cd == element.org_cd);
          if (index === -1) {
            this.formData.convention_target_org.push({
              org_cd: element.org_cd,
              org_label: element.org_label,
              layer_num: element.layer_num,
              delete_flag: 1
            });
          }
        }
      }
    },

    cancelChangeData() {
      this.onCloseModal();
    },

    handleRemoveLectureSeries() {
      this.formData.series_convention_id = '';
      this.formData.series_convention_name = '';
    },

    // D-13
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
          initDataCodes: this.formData.convention_product.filter((x) => x.delete_flag !== -1)?.map((x) => x.product_cd)
        }
      };
    },

    onResultModalZ05S06(e) {
      let data = e?.dataSelected;
      this.formData.convention_product = this.formData.convention_product.filter((e) => e.delete_flag !== 1);

      for (let i = 0; i < this.lstProductDefault.length; i++) {
        let element = this.lstProductDefault[i];
        let index = this.formData.convention_product.findIndex((e) => e.product_cd == element.product_cd);

        if (!data.some((x) => x.product_cd == element.product_cd)) {
          this.formData.convention_product[index].delete_flag = -1;
        } else {
          this.formData.convention_product[index].delete_flag = 0;
        }
      }

      for (let j = 0; j < data.length; j++) {
        let element = data[j];
        let index = this.formData.convention_product.findIndex((e) => e.product_cd == element.product_cd);
        if (index === -1) {
          this.formData.convention_product.push({
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
        initDataCodes: this.formData.convention_product.filter((x) => x.delete_flag !== -1)?.map((x) => x.product_cd)
      };
    },

    handleRemoveProduct(product) {
      let index = this.formData.convention_product.findIndex((e) => e.product_cd?.toString() === product.product_cd?.toString());
      if (this.lstProductDefault.some((x) => x.product_cd == product.product_cd)) {
        this.formData.convention_product[index].delete_flag = -1;
      } else {
        this.formData.convention_product = this.formData.convention_product.filter((x) => x.product_cd !== product.product_cd);
      }
    },

    // D-5
    setSelectedHoldMethod(item) {
      this.formData.hold_method = item;
      this.lstHoldMethod.forEach((x) => {
        x.selected = false;
        if (x.hold_method_value === item) {
          x.selected = true;
        }
      });
    },

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
      }
    },

    // D-14
    openModalZ05S05() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材選択',
        customClass: 'custom-dialog custom-dialog-pd-none',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(Z05_S05_Choice_Of_Masterial),
        width: '77rem',
        props: {
          mode: 'multiple',
          subMaterialSelectableFlag: 1,
          customMaterialFlag: 1,
          params: {
            ...this.paramsZ05S05,
            initMaterials: this.formData.convention_document.filter((e) => e.delete_flag != -1).map((x) => x.document_id)
          }
        }
      };
    },

    async onResultModalZ05S05(e) {
      let oldData = [...this.formData.convention_document];
      let newData = [...e?.dataMasterialSelected];

      for (let i in oldData) {
        let element = oldData[i];
        if (!newData.some((x) => x.document_id == element.document_id)) {
          this.deleteMaterial(element);
        }
      }

      for (let i in newData) {
        let x = newData[i];
        let index = this.formData.convention_document.findIndex((e) => e.document_id?.toString() === x.document_id?.toString());
        if (index === -1) {
          this.formData.convention_document.push({
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
          if (this.formData.convention_document[index].delete_flag === -1) {
            this.formData.convention_document[index].delete_flag = 0;
          }
        }
      }
      if (this.$refs.documentRef) {
        this.$refs.documentRef.scrollTop = 0;
      }
    },

    deleteMaterial(item) {
      let index = this.formData.convention_document.findIndex((e) => e.document_id?.toString() === item.document_id?.toString());
      if (this.lstMaterialsDefault.some((x) => x.document_id == item.document_id)) {
        this.formData.convention_document[index].delete_flag = -1;
      } else {
        this.formData.convention_document = this.formData.convention_document.filter((x) => x.document_id != item.document_id);
      }
    },

    checkOutsideValid(start_date, end_date) {
      let today = new Date();
      if (new Date(start_date).getTime() <= today.getTime() && today.getTime() <= new Date(end_date).getTime()) {
        return false;
      }
      return true;
    },

    // D01-S07: document viewer
    openModalMaterialViewerD01S07(document) {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材ビューワ',
        isShowModal: true,
        customClass: 'custom-dialog',
        component: markRaw(D01_S07_MaterialViewer),
        width: '60rem',
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

    // D-19
    selectOrgLayer(item) {
      this.formData.organization_layer = item;
      this.formData.convention_target_org = [];
      if (item == this.orgLayerDefault) {
        this.formData.convention_target_org = [...this.lstOrgsDefault];
      } else {
        this.lstOrgsDefault.forEach((x) => {
          this.formData.convention_target_org.push({
            ...x,
            delete_flag: -1
          });
        });
      }
    },

    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '組織選択',
        isShowModal: true,
        isShowClose: true,
        customClass: 'custom-dialog modal-fixed',
        component: markRaw(Z05_S01_Organization),
        props: {
          ...this.paramsZ05S01,
          orgCdList: this.formData.convention_target_org.filter((e) => e.delete_flag !== -1).map((x) => x.org_cd),
          userSelectFlag: this.formData.organization_layer ? 0 : 1,
          layerMax: this.formData.organization_layer,
          layerMin: this.formData.organization_layer
        },
        width: '45rem',
        destroyOnClose: true
      };
    },

    onResultModalZ05S01(e) {
      if (e) {
        let data = e?.orgSelected;
        this.formData.convention_target_org = this.formData.convention_target_org?.filter((e) => e?.delete_flag !== 1);

        for (let i = 0; i < this.lstOrgsDefault.length; i++) {
          let element = this.lstOrgsDefault[i];
          let index = this.formData.convention_target_org.findIndex((e) => e.org_cd == element.org_cd);

          if (!data.some((x) => x.org_cd == element.org_cd)) {
            this.formData.convention_target_org[index].delete_flag = -1;
          } else {
            this.formData.convention_target_org[index].delete_flag = 0;
          }
        }

        for (let j = 0; j < data.length; j++) {
          let element = data[j];
          let index = this.formData.convention_target_org.findIndex((e) => e.org_cd == element.org_cd);
          if (index === -1) {
            this.formData.convention_target_org.push({
              layer_num: element.layer_num,
              org_cd: element.org_cd,
              org_label: element.org_label,
              org_name: element.org_name,
              delete_flag: 1
            });
          }
        }
      }
    },

    deleteOrg(item) {
      let index = this.formData.convention_target_org.findIndex((e) => e.org_cd === item.org_cd);
      if (this.lstOrgsDefault.some((x) => x.org_cd == item.org_cd)) {
        this.formData.convention_target_org[index].delete_flag = -1;
      } else {
        this.formData.convention_target_org = this.formData.convention_target_org.filter((x) => x.org_cd != item.org_cd);
      }
    },

    // D-23
    validFileType(files) {
      let isValid = false;
      let fileName = '';
      let type = '';
      for (let element of files) {
        if (!this.fileTypes.includes(element.type)) {
          isValid = true;
          fileName = element.name;
          type = this.returnFileSize(element.type);
          break;
        }
      }
      return {
        isValid: isValid,
        fileName: fileName,
        type: type
      };
    },

    validFileSize(files) {
      let isValid = false;
      let fileName = '';
      let size = '';
      for (let element of files) {
        if (element.size >= 104857600) {
          isValid = true;
          fileName = element.name;
          size = this.returnFileSize(element.size);
          break;
        }
      }
      return {
        isValid: isValid,
        fileName: fileName,
        size: size
      };
    },

    returnFileSize(number) {
      if (number < 1024) {
        return number + 'bytes';
      } else if (number >= 1024 && number < 1048576) {
        return (number / 1024).toFixed(1) + 'KB';
      } else if (number >= 1048576) {
        return (number / 1048576).toFixed(1) + 'MB';
      }
    },

    changeFile(e) {
      const elementFile = document.getElementById('importFile');
      let files = elementFile.files;
      let fileChecked = [];

      for (let element of files) {
        let nameArr = element.name?.split('.') || [];

        fileChecked.push({
          type: element.type ? element.type : `.${nameArr[nameArr.length - 1]}`,
          name: element.name,
          size: element.size
        });
      }

      let validSize = this.validFileSize(fileChecked);
      let validType = this.validFileType(fileChecked);

      this.loadingFileUpload = true;

      let lengthDef = this.formData.convention_file.filter((x) => x.delete_flag !== -1).length;

      let leng = files.length + lengthDef;
      if (leng <= 5) {
        if (validType.isValid) {
          this.$notify({ message: `${validType.fileName}対応していないファイル形式です。`, customClass: 'error' });
          this.loadingFileUpload = false;
        } else {
          if (validSize.isValid) {
            this.$notify({ message: `${validSize.fileName}は100MB以下のファイルにしてください。`, customClass: 'error' });
            this.loadingFileUpload = false;
          } else {
            const arrFiles = [...files];
            arrFiles?.forEach((x) => {
              let index = this.formData.convention_file.findIndex((e) => e.old_name === x.name && e.delete_flag != -1);

              if (index === -1) {
                this.formData.convention_file.push({
                  file_id: '',
                  display_name: x.name,
                  old_name: x.name,
                  file: x,
                  delete_flag: 1,
                  downloading: false
                });
              }
            });
            this.loadingFileUpload = false;
          }
        }
      } else {
        this.$notify({ message: this.getMessage('MSFA0140', '講演', '5'), customClass: 'error' });
        this.loadingFileUpload = false;
      }
      e.target.value = '';
    },

    renameFile(name) {
      let newName = '';
      let count = 0;
      for (const i in this.formData.convention_file) {
        let element = this.formData.convention_file[i];
        if (element.old_name === name) {
          count++;
        }
      }
      let splitname = name.split('.');
      if (splitname.length > 0) {
        newName = splitname.map((slice, index) => {
          if (index === splitname.length - 2) {
            return slice + `(${count})`;
          }
          return slice;
        });
      }
      return newName.join('.');
    },

    removeFile(item) {
      let index = this.formData.convention_file.findIndex((e) => e.display_name === item.display_name && e.delete_flag != -1);
      if (this.lstFileDefault.some((x) => x.display_name == item.display_name)) {
        this.formData.convention_file[index].delete_flag = -1;
      } else {
        this.formData.convention_file = this.formData.convention_file.filter((x) => x.display_name !== item.display_name);
      }
    },

    downLoadFileS3(file) {
      var FileSaver = require('file-saver');

      this.formData.convention_file.forEach((x, i) => {
        if (x.display_name === file.display_name) {
          this.formData.convention_file[i] = {
            ...x,
            downloading: true
          };
        }
      });
      let access_token = getCookie('accessToken');
      let originalToken = getCookie('originalToken');

      let config = {};
      if (access_token) {
        config = {
          Authorization: `Bearer ${access_token}`
        };
      }

      if (originalToken) {
        config = {
          OriginalToken: `Bearer ${originalToken}`
        };
      }

      if (file.file_id) {
        axios({
          url: `${file.url_path}`,
          headers: config,
          method: 'GET',
          responseType: 'blob'
        })
          .then((res) => {
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `${file.display_name}`);
            document.body.appendChild(link);
            link.click();
          })
          .catch(() => {
            this.$notify({ message: '内部エラーが発生しました。システム管理者に連絡してください。', customClass: 'error' });
          })
          .finally(async () => {
            this.formData.convention_file.forEach((x, i) => {
              if (x.display_name === file.display_name) {
                this.formData.convention_file[i] = {
                  ...x,
                  downloading: false
                };
              }
            });
          });
      } else {
        var blobs = new Blob([file.file], { type: `${file.file.type};charset=utf-8` });
        FileSaver.saveAs(blobs, `${file.display_name}`);
        this.formData.convention_file.forEach((x, i) => {
          if (x.display_name === file.display_name) {
            this.formData.convention_file[i] = {
              ...x,
              downloading: false
            };
          }
        });
      }
    },

    // E-1
    setSelectedHoldForm(item) {
      this.formData.hold_form = item;
      this.lstHoldingForm.forEach((x) => {
        x.selected = false;
        if (x.hold_form_value === item) {
          x.selected = true;
        }
      });

      if (this.formData.hold_form == '10') {
        this.setSelectedCostShare('');
        this.formData.cohost_partner_name = '';
        this.formData.cost_share_content = '';
      }
    },

    // E-3
    setSelectedCostShare(item) {
      this.formData.cost_share_type = item;
      this.lstShareCost.forEach((x) => {
        x.selected = false;
        if (x.cost_share_type_value === item) {
          x.selected = true;
        }
      });
    },

    // F-1
    redirectToAttendantManagementC01S03(convention) {
      let convention_id = convention;
      if (convention) {
        this.$router.push({ name: 'C01_S03_AttendantManagement', params: { convention_id } });
      } else {
        this.onCloseModal();
      }
    },

    caclSumOccupation() {
      this.occupationSum = [];
      for (let i = 0; i < this.lstAttendeesTitle.length; i++) {
        let sum = 0;
        const title = this.lstAttendeesTitle[i];
        for (let j = 0; j < this.lstAttendeesType.length; j++) {
          const type = this.lstAttendeesType[j];
          sum += Number(type.data_item[i].sum_count_user);
        }
        this.occupationSum.push({
          status_item_cd: title.status_item_cd,
          sum: sum
        });
      }
    },

    gotoScreenC01S03() {
      this.isGoToC1S3 = false;
      if (this.formData.convention_id) {
        if (!isEqual(this.formData, this.oldFormData)) {
          this.openConfirmSaveData({ mode: 1 });
          this.isGoToC1S3 = true;
        } else {
          this.redirectToAttendantManagementC01S03(this.formData.convention_id);
        }
      } else {
        let props = {
          mode: 1,
          message: '入力中の講演会を一時保存して続行します。よろしいですか？'
        };
        this.openConfirmSaveData(props);
      }
    },

    openConfirmSaveData(props) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        isShowClose: false,
        component: markRaw(this.$PopupConfirm),
        width: '36rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { ...props }
      };
    },

    handleConfirmYesC1S3() {
      if (this.isGoToC1S3) {
        this.redirectToAttendantManagementC01S03(this.formData.convention_id);
        this.isGoToC1S3 = false;
      } else {
        this.saveData();
      }
    },

    convertFormData(formData, data, parentKey) {
      if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
        Object.keys(data).forEach((key) => {
          this.convertFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key);
        });
      } else {
        const value = data == null ? '' : data;

        formData.append(parentKey, value);
      }
    },

    jsonToFormData(data) {
      const formData = new FormData();

      this.convertFormData(formData, data);

      return formData;
    },

    convertParamsSave() {
      let timeStart = this.formData.start_date + ' ' + this.formData.start_time;
      let timeEnd = this.formData.start_date + ' ' + this.formData.end_time;
      let params = {
        ...this.formData,
        organization_layer: null,
        start_time: this.formData.start_time ? timeStart : '',
        end_time: this.formData.end_time ? timeEnd : '',
        area_expense: JSON.stringify(this.formData.area_expense)
      };

      Object.keys(params).forEach((x) => {
        if (x != 'convention_file') {
          params[x] = encodeData(params[x]);
        }
      });

      return params;
    },

    saveData() {
      this.submitButtonClick();
      if (!this.checkValidMessageSum()) {
        if (!this.status_check || this.status_check == '10') {
          this.savePlan();
        }
        if (this.status_check == '30' || this.status_check == '40') {
          this.saveResult();
        }
        this.onCloseModal();
      } else {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        this.onCloseModal();
        if (this.checkValidMessageSum() !== 'err_option') {
          this.scrollTop();
        }
      }
    },

    savePlan() {
      let params = { ...this.convertParamsSave() };

      C01_S02_LectureInputService.savePlan(this.jsonToFormData(params))
        .then(async (res) => {
          this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });

          let convention_id = res.data.data.convention_id;
          await this.$router.replace({ params: { convention_id } });
          this.redirectToAttendantManagementC01S03(convention_id);
        })
        .catch((err) => {
          this.loadingPage = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    saveResult() {
      let params = { ...this.convertParamsSave() };

      C01_S02_LectureInputService.saveResult(this.jsonToFormData(params))
        .then(async (res) => {
          this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });

          let convention_id = res.data.data.convention_id;
          await this.$router.replace({ params: { convention_id } });
          this.redirectToAttendantManagementC01S03(convention_id);
        })
        .catch((err) => {
          this.loadingPage = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    checkValidMessageSum() {
      let message = '';
      if (this.checkValidateForm(true, 'convention_name', '講演会名', 'MSFA0001', 'String', 100)) {
        message = this.validationMessageForm(true, 'convention_name', '講演会名', 'MSFA0001', 'String', 100);
      } else {
        if (this.checkValidateForm(true, 'start_date', '日', 'MSFA0040')) {
          message = this.validationMessageForm(true, 'start_date', '日', 'MSFA0040');
        } else {
          if (this.checkValidateForm(true, 'start_time', '時', 'MSFA0040')) {
            message = this.validationMessageForm(true, 'start_time', '時', 'MSFA0040');
          } else {
            if (this.checkValidateForm(false, 'place', '会場', null, 'String', 255)) {
              message = this.validationMessageForm(false, 'place', '会場', null, 'String', 255);
            } else {
              if (this.checkValidateForm(false, 'cohost_partner_name', '共催相手', null, 'String', 40)) {
                message = this.validationMessageForm(false, 'cohost_partner_name', '共催相手', null, 'String', 40);
              } else {
                if (this.checkValidateForm(false, 'cost_share_content', '開催費分担内容', null, 'String', 40)) {
                  message = this.validationMessageForm(false, 'cost_share_content', '開催費分担内容', null, 'String', 40);
                }
              }
            }
          }
        }
      }
      if (!message) {
        for (let index = 0; index < this.formData.area_expense.length; index++) {
          const element = this.formData.area_expense[index];
          for (let index = 0; index < element.layer_2.length; index++) {
            const element_2 = this.formData.area_expense[index];
            if (
              element_2.option_item_flag === 1 &&
              ((element_2.plan_amount && !element_2.option_item_name) || (element_2.plan_amount && !element_2.option_item_content))
            ) {
              message = 'err_option';
              break;
            }
          }
        }
      }

      return message;
    },

    // G

    redirectToH02S02(item) {
      let person_cd = item.person_cd;
      localStorage.setItem('activeMenu', 'C01_S02_LectureInput');
      this.$router.push({ name: 'H02_PersonalDetails', params: { person_cd }, query: { tab: 1 } });
    },

    changeAmountTax(item) {
      let time;
      const timeout = setTimeout(() => {
        if (time) {
          clearTimeout(timeout);
          return;
        }
        let gratuityChange = item.gratuity > this.maxPrice ? this.maxPrice : item.gratuity;
        let itemChange = {
          ...item,
          gratuity: gratuityChange,
          withholding_tax: this.calcTaxRate(gratuityChange).toFixed(0),
          give_amount: gratuityChange - this.calcTaxRate(gratuityChange).toFixed(0)
        };
        let index = this.formData.convention_attendee.findIndex((x) => x.convention_attendee_id === item.convention_attendee_id);

        this.formData.convention_attendee[index] = { ...itemChange };

        let itemCurr = { ...itemChange };
        itemCurr.gratuity = this.convertNumberToCurrency(itemCurr.gratuity);
        this.dataNumberChange.convention_attendee[index] = { ...itemCurr };

        time = 1;
      }, 0);
    },

    calcTaxRate(gratuity) {
      let index = this.lstTaxRate.findIndex((x) => Number(x.min_amount) <= gratuity && gratuity <= Number(x.max_amount));

      let withholding_tax = 0;

      for (let i = 0; i <= index; i++) {
        let itemTax = this.lstTaxRate[i];
        let tax_rate = Number(itemTax?.tax_rate);

        if (index === 0) {
          withholding_tax = Math.floor(gratuity * tax_rate);
        } else {
          let itemTaxOld = this.lstTaxRate[i - 1];
          if (i === 0) {
            withholding_tax = Math.floor(Number(itemTax.max_amount) * tax_rate);
          } else {
            if (i < index) {
              withholding_tax += Math.floor((Number(itemTax.max_amount) - Number(itemTaxOld.max_amount)) * tax_rate);
            } else {
              withholding_tax += Math.floor((gratuity - Number(itemTaxOld.max_amount)) * tax_rate);
            }
          }
        }
      }

      return withholding_tax;
    },

    rangePrice() {
      if (this.lstTaxRate.length > 0) {
        this.minPrice = Number(this.lstTaxRate[0]?.min_amount);
        this.maxPrice = Number(this.lstTaxRate[this.lstTaxRate.length - 1]?.max_amount);
      }
    },

    // H
    setTabType(type) {
      this.tabType = type;
      this.tabName = this.lstExpense.find((x) => x.expense_classification_value === type)?.expense_classification_label;
    },

    getTabName(type) {
      this.tabName = this.lstExpense.find((x) => x.expense_classification_value === type)?.expense_classification_label;
    },

    removeExpenseNum() {
      this.formData.expense_num = null;
    },

    checkInputQuanlity(lstItem) {
      let isInputQuanlity = false;
      if (lstItem) {
        for (let i = 0; i < lstItem?.length; i++) {
          const element = lstItem[i];
          if (element.quantity_input_flag === 1) {
            isInputQuanlity = true;
            break;
          }
        }
      }

      return isInputQuanlity;
    },

    checkLayer3(index) {
      let itemExpense = { ...this.formData.area_expense[index] };
      for (let idx2 = 0; idx2 < itemExpense.layer_2?.length; idx2++) {
        const ItemLayer2 = itemExpense.layer_2[idx2];
        if (ItemLayer2.layer_3) {
          return true;
        }
      }
      return false;
    },

    changeOptionItem(index, index2, field) {
      this.formData['area_expense'][index].layer_2[index2][field] = this.dataNumberChange['area_expense'][index].layer_2[index2][field];
    },

    calcSumHDefault(type) {
      let dataExpense = [...this.formData.area_expense];
      for (let i = 0; i < dataExpense.length; i++) {
        if (type === 'plan') {
          this.sumPlan(i);
        } else {
          this.sumResult(i);
        }
      }
    },

    // plan
    changeAmountExpensePlan(index, index2, index3) {
      let time;
      const timeout = setTimeout(() => {
        if (time) {
          clearTimeout(timeout);
          return;
        }
        let itemExpense = { ...this.formData.area_expense[index] };
        let ItemLayer2 = { ...itemExpense.layer_2[index2] };

        let itemExpenseChange = { ...this.dataNumberChange.area_expense[index] };
        let ItemLayer2Change = { ...itemExpenseChange.layer_2[index2] };

        if (index3 != null) {
          let ItemLayer3 = { ...ItemLayer2.layer_3[index3] };
          let ItemLayer3Change = { ...ItemLayer2Change.layer_3[index3] };

          if (ItemLayer3.plan_unit_price && ItemLayer3.plan_quantity) {
            ItemLayer3.plan_amount = ItemLayer3.plan_unit_price * ItemLayer3.plan_quantity;
          } else {
            ItemLayer3.plan_amount = 0;
          }
          ItemLayer3Change.plan_amount = this.convertNumberToCurrency(ItemLayer3.plan_amount);

          ItemLayer2.layer_3[index3] = { ...ItemLayer3 };
          ItemLayer2Change.layer_3[index3] = { ...ItemLayer3Change };
        } else {
          if (ItemLayer2.plan_unit_price && ItemLayer2.plan_quantity) {
            ItemLayer2.plan_amount = ItemLayer2.plan_unit_price * ItemLayer2.plan_quantity;
          } else {
            ItemLayer2.plan_amount = 0;
          }
          ItemLayer2Change.plan_amount = this.convertNumberToCurrency(ItemLayer2.plan_amount);
        }

        itemExpense.layer_2[index2] = { ...ItemLayer2 };
        itemExpenseChange.layer_2[index2] = { ...ItemLayer2Change };
        this.formData.area_expense[index] = { ...itemExpense };
        this.dataNumberChange.area_expense[index] = { ...itemExpenseChange };
        this.sumPlan(index);
        time = 1;
      }, 0);
    },

    sumPlan(index) {
      let time;
      const timeout = setTimeout(() => {
        if (time) {
          clearTimeout(timeout);
          return;
        }
        let itemExpense = { ...this.formData.area_expense[index] };

        let sumLayer2 = 0;
        for (let idx2 = 0; idx2 < itemExpense.layer_2?.length; idx2++) {
          const ItemLayer2 = itemExpense.layer_2[idx2];

          let sumLayer3 = 0;
          for (let idx3 = 0; idx3 < ItemLayer2.layer_3?.length; idx3++) {
            const ItemLayer3 = ItemLayer2.layer_3[idx3];
            sumLayer3 += Number(ItemLayer3.plan_amount);
          }
          itemExpense.layer_2[idx2] = {
            ...ItemLayer2,
            plan_amount: ItemLayer2.layer_3 ? sumLayer3 : ItemLayer2.plan_amount
          };

          sumLayer2 += ItemLayer2.layer_3 ? sumLayer3 : ItemLayer2.plan_amount;
        }

        this.formData.area_expense[index] = {
          ...itemExpense,
          plan_amount: sumLayer2
        };

        this.dataNumberChange.area_expense[index].plan_amount = sumLayer2;

        let sumLayer1 = 0;
        for (let idx1 = 0; idx1 < this.formData.area_expense?.length; idx1++) {
          const ItemLayer1 = this.formData.area_expense[idx1];
          sumLayer1 += ItemLayer1.plan_amount;
        }

        this.formData.area_expense_sum.plan_amount = sumLayer1;
        time = 1;
      }, 0);
    },

    // result

    changeAmountExpenseResult(index, index2, index3) {
      let time;
      const timeout = setTimeout(() => {
        if (time) {
          clearTimeout(timeout);
          return;
        }
        let itemExpense = { ...this.formData.area_expense[index] };
        let ItemLayer2 = { ...itemExpense.layer_2[index2] };

        let itemExpenseChange = { ...this.dataNumberChange.area_expense[index] };
        let ItemLayer2Change = { ...itemExpenseChange.layer_2[index2] };

        if (index3 != null) {
          let ItemLayer3 = { ...ItemLayer2.layer_3[index3] };
          let ItemLayer3Change = { ...ItemLayer2Change.layer_3[index3] };

          if (ItemLayer3.result_unit_price && ItemLayer3.result_quantity) {
            ItemLayer3.result_amount = ItemLayer3.result_unit_price * ItemLayer3.result_quantity;
          } else {
            ItemLayer3.result_amount = 0;
          }
          ItemLayer3Change.result_amount = this.convertNumberToCurrency(ItemLayer3.result_amount);

          ItemLayer2.layer_3[index3] = { ...ItemLayer3 };
          ItemLayer2Change.layer_3[index3] = { ...ItemLayer3Change };
        } else {
          if (ItemLayer2.result_unit_price && ItemLayer2.result_quantity) {
            ItemLayer2.result_amount = ItemLayer2.result_unit_price * ItemLayer2.result_quantity;
          } else {
            ItemLayer2.result_amount = 0;
          }
          ItemLayer2Change.result_amount = this.convertNumberToCurrency(ItemLayer2.result_amount);
        }

        itemExpense.layer_2[index2] = { ...ItemLayer2 };
        itemExpenseChange.layer_2[index2] = { ...ItemLayer2Change };
        this.formData.area_expense[index] = { ...itemExpense };
        this.dataNumberChange.area_expense[index] = { ...itemExpenseChange };
        this.sumResult(index);
        time = 500;
      }, 100);
    },

    sumResult(index) {
      let time;
      const timeout = setTimeout(() => {
        if (time) {
          clearTimeout(timeout);
          return;
        }
        let itemExpense = { ...this.formData.area_expense[index] };
        let sumLayer2 = 0;
        for (let idx2 = 0; idx2 < itemExpense.layer_2?.length; idx2++) {
          const ItemLayer2 = itemExpense.layer_2[idx2];

          let sumLayer3 = 0;
          for (let idx3 = 0; idx3 < ItemLayer2.layer_3?.length; idx3++) {
            const ItemLayer3 = ItemLayer2.layer_3[idx3];
            sumLayer3 += Number(ItemLayer3.result_amount);
          }

          itemExpense.layer_2[idx2] = {
            ...ItemLayer2,
            result_amount: ItemLayer2.layer_3 ? sumLayer3 : ItemLayer2.result_amount
          };

          sumLayer2 += ItemLayer2.layer_3 ? sumLayer3 : ItemLayer2.result_amount;
        }

        this.formData.area_expense[index] = {
          ...itemExpense,
          result_amount: sumLayer2
        };

        let sumLayer1 = 0;
        for (let idx1 = 0; idx1 < this.formData.area_expense?.length; idx1++) {
          const ItemLayer1 = this.formData.area_expense[idx1];
          sumLayer1 += ItemLayer1.result_amount;
        }

        this.formData.area_expense_sum.result_amount = sumLayer1;

        this.dataNumberChange.area_expense[index].result_amount = sumLayer2;
        time = 200;
      }, 100);
    },

    // I
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

    // result modal
    onResultModal(e) {
      if (e) {
        if (e.screen === 'C01_S04') {
          if (e.convention_id) {
            this.onResultModalLectureSeriesC01S04(e);
          }
        }
        if (e.screen === 'Z05_S06') {
          this.onResultModalZ05S06(e);
        }
        if (e.screen === 'Z05_S05') {
          this.onResultModalZ05S05(e);
        }
        if (e.screen === 'Z05_S01') {
          this.onResultModalZ05S01(e);
        }
        if (e.screen === 'C01_S02_Modal_Comment') {
          this.onResultModalComment();
        }
      }
      this.onCloseModal();
    },

    onCloseModal() {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: false,
        isShowModalConfirmInput: false,
        isShowModalConfirmSave: false,
        isShowClose: true
      };
    },

    childFocusOut() {
      const elMain = this.$refs.contentLectureRef;
      if (elMain && elMain?.classList) {
        elMain?.classList?.remove('stop-scroll');

        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = new PerfectScrollbar('#conventionSession', {
          wheelSpeed: 0.5
        });
      }
    },
    childFocus() {
      const els = document.querySelectorAll('.scroll-child');
      const elMain = this.$refs.contentLectureRef;

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
$viewport_tablet: 991px;

.groupLecture {
  height: 100%;
  padding-top: 28px;
  .wrapLecture {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
  }
  .headLecture {
    padding: 12px 0 10px;
    .lectureNumber {
      padding: 0 52px 12px;
      font-size: 1.5rem;
      font-weight: 700;
      @media (max-width: $viewport_desktop) {
        padding: 0 24px 12px;
      }
    }
    .lectureStep {
      padding: 12px 24px;
      &.lectureStep--blue {
        background: #eefbfa;
        background: -moz-linear-gradient(left, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
        background: -webkit-linear-gradient(left, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
        background: linear-gradient(to right, #eefbfa 0%, #d3efff 75%, #eaf1fe 100%);
      }
      &.lectureStep--red {
        background: #fff7f1;
        background: -moz-linear-gradient(left, #fff7f1 0%, #feeae9 75%, #f9eaf1 100%);
        background: -webkit-linear-gradient(left, #fff7f1 0%, #feeae9 75%, #f9eaf1 100%);
        background: linear-gradient(to right, #fff7f1 0%, #feeae9 75%, #f9eaf1 100%);
      }
    }
  }
  .contentLecture {
    height: 100%;
    padding-bottom: 32px;
  }
  .sectionLecture {
    padding: 10px 0 0;
    .titleLecture {
      background: #eef6ff;
      box-shadow: 2px 5px 8px rgba(145, 161, 171, 0.5);
      border-radius: 0px 40px 40px 0px;
      padding: 10px 24px 10px 52px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      min-height: 54px;
      @media (max-width: $viewport_desktop) {
        padding: 10px 24px 10px;
      }
      &--mw300 {
        max-width: 300px;
      }
      &--mw440 {
        max-width: 440px;
      }
      &--mt40 {
        margin-top: 40px;
      }
      .tlt {
        font-size: 1.125rem;
        font-weight: 700;
      }
      .btn {
        padding: 0 12px;
        height: 32px;
        .svg {
          width: 16px;
        }
      }
    }
    .basicRow {
      display: flex;
      flex-wrap: wrap;
      padding: 0 52px;
      margin-left: -52px;
      @media (max-width: $viewport_desktop) {
        margin-left: -24px;
        padding: 0 24px;
      }
      .basic-col {
        width: 50%;
        padding-left: 52px;
        @media (max-width: $viewport_desktop) {
          padding-left: 24px;
        }
        @media (max-width: $viewport_tablet) {
          width: 100%;
        }
      }
    }
  }
  .basicGroup-form {
    padding-top: 12px;
    .basicForm-col2 {
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -24px;
        > li {
          padding-left: 24px;
          width: 50%;
        }
      }
    }
    .basicForm {
      padding-top: 20px;
      .basicForm-label {
        font-size: 1rem;
        margin-bottom: 10px;
        .asterisk {
          display: inline-flex;
          margin: 8px 0 0 8px;
        }
      }
      .basicForm-control {
        .dateTime {
          display: flex;
          flex-wrap: wrap;
          margin-left: -24px;
          li {
            padding-left: 24px;
            width: 50%;
          }
        }
      }
    }
  }
  .basicForm-btnSelect {
    display: flex;
    margin-left: 1px;
    &--col2 {
      li {
        width: 50%;
      }
    }
    &--col4 {
      li {
        width: 25%;
      }
    }
    &--col3-5 {
      li {
        width: 30%;
      }
    }
    li {
      cursor: pointer;
      width: 100%;
      border-radius: 0;
      padding: 0;
      min-height: 40px;
      text-align: center;
      vertical-align: middle;
      padding: 0.5rem 0.75rem;

      line-height: 1.5;
      color: #5f6b73;
      font-size: 0.875rem;
      border: 1px solid #727f88;

      display: flex;
      align-items: center;
      justify-content: center;

      &:first-of-type {
        border-radius: 4px 0 0 4px;
      }
      &:last-child {
        border-radius: 0 4px 4px 0;
      }
      margin-left: -1px;

      &.active {
        border: 2px solid #448add;
        color: #448add;
        background: #eef6ff;
        font-weight: 700;
      }
      &:hover:not(.active) {
        background: #eef6ff;
        font-weight: 700;
        color: #5f6b73;
      }
      &.disabled {
        pointer-events: none;
        cursor: not-allowed;
        background: #f5f7fa;
        color: #c0c4cc;
        border-color: #e4e7ed;
      }
    }
  }
  .groupSection {
    .title {
      padding: 16px 12px 10px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      &--pt36 {
        padding-top: 36px;
      }
      .title-tlt {
        font-size: 1rem;
      }
      .btn {
        padding: 0 12px;
        height: 32px;
        .btn-iconLeft {
          margin-right: 3px;
          .svg {
            width: 13px;
          }
        }
      }
    }
    .boxMaterials {
      background: #fff;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 8px;
      padding: 10px 0;
      .listMaterials {
        height: 285px;
        padding: 0 12px;
        > ul {
          > li {
            padding: 15px 16px 15px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e3e3e3;
            min-height: 56px;
            &:last-child {
              .dropdown-tools {
                top: initial !important;
                bottom: 0 !important;
              }
              .btn-show {
                top: initial;
                bottom: 0;
              }
            }
          }
        }
        .listMaterials-info {
          display: inline-flex;
          flex-wrap: wrap;
          .listTitle {
            margin: 0 10px 0 0;
            font-size: 1rem;
            display: flex;
            .listItem {
              margin: -2px 10px 0 0;
              min-width: 18px;
            }
          }
          .spanLabel {
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 0.875rem;
            display: inline-flex;
            &--red {
              background: #feddde;
              color: #ea5d54;
            }
          }
        }
        .listStatus-btn {
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
  .lectureTarget {
    display: flex;
    flex-wrap: wrap;
    .lectureTarget-se {
      width: calc(100% - 76px);
      padding-right: 8px;
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: 1px;
        li {
          width: calc(100% / 6);
          padding: 5px;
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
            padding: 0 4px;
          }
        }
      }
    }
    .lectureTarget-add {
      width: 76px;
      text-align: right;
      .btn {
        padding: 0 10px;
        height: 32px;
        .btn-iconLeft {
          margin-right: 3px;
          .svg {
            width: 13px;
          }
        }
      }
    }
  }
  .boxTarget {
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    padding: 10px 0;
    margin-top: 10px;
    .listTarget {
      padding: 0 12px;
      height: 326px;
      ul {
        li {
          padding: 12px 16px 12px 12px;
          border-bottom: 1px solid #e3e3e3;
          display: flex;
          justify-content: space-between;
          align-items: center;
          min-height: 67px;
          .listTarget-tlt {
            font-size: 1rem;
          }
          .btn {
            min-width: 42px;
            margin-left: 10px;
          }
        }
      }
    }
  }
  .boxRelated {
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    padding: 24px 20px;
    min-height: 142px;
    .listRelated {
      ul {
        margin: -10px 0 0 -15px;
        display: inline-flex;
        flex-wrap: wrap;
        li {
          margin: 10px 0 0 15px;
          padding: 0 12px;
          background: #d1e4ff;
          color: #225999;
          font-size: 0.875rem;
          border-radius: 24px;
          position: relative;
          .file-name {
            cursor: pointer;
            padding-right: 10px;
          }
          .close-x {
            position: absolute;
            top: 4px;
            right: 6px;
            cursor: pointer;
          }
        }
      }
    }
  }
  .basicGroup-tex {
    > ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -10px;
      > li {
        width: 50%;
        margin-top: 20px;
        padding-left: 10px;
      }
    }
    .basicGroup-title,
    .basicGroup-txt {
      font-size: 1rem;
      white-space: pre-line;
    }
  }
  .holdingForm {
    max-width: 300px;
    padding-top: 20px;
    .holdingForm-label {
      font-size: 1rem;
      margin-bottom: 10px;
    }
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: 1px;
      li {
        width: 50%;
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
  .holdingTextarea {
    padding-top: 20px;
    .holdingTextarea-label {
      font-size: 1rem;
      margin-bottom: 10px;
    }
  }
  .attendees {
    padding: 32px 52px 0;
    @media (max-width: $viewport_desktop) {
      padding: 32px 24px 0;
    }
    .attendeesTbl {
      min-height: 100px;
      overflow: auto;
      box-shadow: 0 0 8px #e3e3e3;
      -moz-border-radius: 10px;
      border-radius: 10px;
      background: #fff;
      padding-bottom: 10px;
      tr {
        th,
        td {
          font-size: 14px;
          min-width: 100px;
        }
        th {
          background: -moz-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
          background: -webkit-linear-gradient(top, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
          background: linear-gradient(to bottom, #5c6f83 0%, #4d6277 20%, #41586e 30%, #465d72 100%);
          position: relative;
          color: #fff;
          white-space: nowrap;
          padding: 10px 22px;
          &:first-of-type:after {
            background-color: transparent !important;
          }
          &:not(:last-child):after {
            content: '';
            width: 1px;
            background-color: #fff;
            height: calc(100% - 8px);
            position: absolute;
            top: 4px;
            right: 0px;
          }
          &:nth-child(1) {
            text-align: right;
          }
        }
      }
      tbody,
      tfoot {
        position: relative;
        &::after,
        &::before {
          position: absolute;
          top: 0;
          content: '';
          width: 10px;
          height: 100%;
          background: #fff;
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
      tbody {
        position: relative;
        &::after {
          left: 0;
        }
        &::before {
          right: 0;
        }
        tr {
          &:last-child {
            td {
              border-bottom: none;
            }
          }
          td {
            border-left: none;
            padding: 12px 22px;
            &:not(:first-of-type) {
              border-right: none;
            }
            &:nth-child(1) {
              width: 1rem;
              min-width: 160px;
            }
          }
        }
      }
      tfoot {
        position: relative;
        &::after {
          left: 0;
        }
        &::before {
          right: 0;
        }
        tr {
          td {
            font-size: 16px;
            font-weight: 700;
            background: #f9f9f9;
            border: none;
            padding: 12px 22px;
            &:first-of-type {
              border-right: 1px solid #e3e3e3;
            }
          }
        }
      }
    }
  }
  .rolePerson {
    background: #fff;
    box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
    border-radius: 8px;
    margin: 32px 52px 0;
    padding: 20px;
    max-height: 450px;
    @media (max-width: $viewport_desktop) {
      margin: 32px 24px 0;
    }
    .role-row {
      display: flex;
      flex-wrap: wrap;
      border-bottom: 1px dashed #e3e3e3;
      padding: 20px 0;
      &:last-child {
        border: none;
      }
      .role-colLeft {
        width: 35%;
        padding-right: 12px;
        @media (max-width: $viewport_tablet) {
          width: 100%;
          padding-right: 0;
        }
        > ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: -10px;
          > li {
            padding-left: 10px;
            font-size: 0.875rem;
            width: 50%;
          }
          .role-tlt {
            margin-bottom: 12px;
          }
          .role-name {
            .role-link {
              color: #448add;
              cursor: pointer;
            }
            .role-name-first {
              font-size: 1.125rem;
              font-weight: 700;
              margin-right: 5px;
            }
          }
          .role-label {
            margin-top: 5px;
            .role-span {
              color: #727f88;
            }
          }
        }
      }
      .role-colRight {
        width: 65%;
        @media (max-width: $viewport_tablet) {
          width: 100%;
        }
        .roleForm {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          margin-bottom: 20px;
          @media (max-width: $viewport_tablet) {
            margin: 10px 0 0;
          }
          &:last-child {
            margin-bottom: 0;
            @media (max-width: $viewport_tablet) {
              margin: 10px 0 0;
            }
          }
          .roleForm-title {
            width: 120px;
          }
          .roleForm-info {
            width: calc(100% - 120px);
            .roleForm-col3 {
              display: flex;
              flex-wrap: wrap;
              align-items: center;
              margin-left: -3%;
              li {
                padding-left: 3%;
                width: 33.333333%;
                display: flex;
                justify-content: space-around;
              }
            }
            .roleForm-label {
              padding-right: 10px;
            }
          }
        }
      }
    }
  }
  .expenses {
    padding: 32px 52px 0;
    @media (max-width: $viewport_desktop) {
      padding: 32px 24px 0;
    }
    .expenses-head {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      .expenses-tabs {
        .expensesTabs {
          padding: 0 5px;
          li {
            width: 50%;
            text-align: center;
            padding: 0 5px;
            a {
              background: #f9f9f9;
              border-radius: 10px 10px 0px 0px;
              box-shadow: 0 0 6px #b7c3cb;
              color: #b7c3cb;
              font-size: 1rem;
              display: block;
              position: relative;
              min-width: 252px;
              @media (max-width: $viewport_tablet) {
                min-width: 150px;
              }
              &:after,
              &:before {
                position: absolute;
                bottom: 0;
                content: '';
                width: 9px;
                height: 9px;
                display: block;
              }
              &:before {
                background: url(~@/assets/template/images/bgTabs-gray-left.png) no-repeat;
                left: -9px;
              }
              &:after {
                background: url(~@/assets/template/images/bgTabs-gray-right.png) no-repeat;
                right: -9px;
              }
              &.active {
                background: #fff;
                color: #448add;
                font-weight: 700;
                z-index: 10;
                &:before {
                  background: url(~@/assets/template/images/bgTabs-white-left.png) no-repeat;
                  left: -9px;
                }
                &:after {
                  background: url(~@/assets/template/images/bgTabs-white-right.png) no-repeat;
                  right: -9px;
                }
                span {
                  &::after {
                    position: absolute;
                    bottom: -6px;
                    left: -5px;
                    content: '';
                    width: calc(100% + 10px);
                    height: 6px;
                    display: block;
                    background: #fff;
                  }
                }
              }
              span {
                padding: 12px 6px;
                display: block;
                position: relative;
              }
            }
          }
        }
      }
      .expenses-expense {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        flex: 0 0 300px;
        @media (max-width: $viewport_tablet) {
          flex: 0 0 250px;
        }
        .expenses-input {
          min-width: 100px;
        }

        .expenses-label {
          font-size: 1rem;
          margin-right: 20px;
          white-space: nowrap;
        }
      }
    }
    .expenses-content {
      padding: 4px 0 0;
      background: #fff;
      box-shadow: 0px 0px 5px #b7c3cb;
      border-radius: 0px 8px 8px 8px;
    }
  }
  .expensesForm {
    padding: 20px;
    .accordionForm {
      box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
      border-radius: 8px;
      background: #fff;
      margin-bottom: 16px;
      &:last-child {
        margin-bottom: 0;
      }
      .accordionHead {
        background: #e9f8ff;
        border-radius: 8px;
        box-shadow: 0px 2px 5px rgba(227, 227, 227, 0.9);
        padding: 16px 20px 16px 22px;
        width: 100%;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        &.accordionHead--show {
          .accordion-tlt {
            &::after {
              transform: rotate(180deg);
            }
          }
        }
        .accordion-tlt,
        .accordion-number {
          font-size: 1.125rem;
          font-weight: 700;
          line-height: 100%;
        }
        .accordion-tlt {
          position: relative;
          padding-left: 24px;
          &::after {
            position: absolute;
            top: 7px;
            left: 0;
            content: '';
            background: url(~@/assets/template/images/icon_arrow-line03.svg) no-repeat;
            width: 17px;
            height: 10px;
            display: block;
            transition: 400ms all;
          }
        }
        .accordion-number {
          padding-left: 10px;
          &.accordion-number--red {
            color: #ea5d54;
          }
        }
      }
      .accordionContent {
        display: none;
        &.accordionContent--show {
          display: block;
        }
      }
    }
  }
  .groupExp {
    padding: 5px 20px;
    min-height: 50px;
    .borderbt-none {
      border-bottom: none !important;
    }
    .formgroupExp {
      border-bottom: 1px dashed #b7c3cb;
      padding: 15px 0;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      &:last-child {
        border-bottom: none;
      }
      .formgroupEx-left {
        width: 120px;
        padding-right: 10px;
        font-size: 1rem;
        color: #2d3033;
      }
      .formgroupEx-right {
        width: calc(100% - 120px);
        .formWrap {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          margin-bottom: 22px;
          &:last-child {
            margin-bottom: 0;
          }
          .formg-labelCircle {
            width: calc(100% - 300px);
            padding-right: 20px;
            text-align: right;
            font-size: 1rem;
          }
          .formCircle {
            width: 300px;
            text-align: right;
          }
          .formCol-label {
            width: 40%;
            text-align: right;
            font-size: 1rem;
          }
          .formCol {
            width: 20%;
            padding-left: 20px;
            .formCol-text {
              display: block;
              text-align: right;
              font-size: 1rem;
              padding: 0 6px;
            }
          }
        }
      }
    }
    .formothers {
      display: flex;
      flex-wrap: wrap;
      padding: 15px 0;
      @media (max-width: $viewport_tablet) {
        padding-left: 0;
      }
      &:last-child {
        padding-bottom: 15px;
      }
      .formothers-col1 {
        width: 40%;
        padding-right: 20px;
      }
      .formothers-col2 {
        width: 20%;
        text-align: right;
      }
    }
  }
  .formTotal {
    background: #ffffff;
    box-shadow: 0px -3px 6px rgba(0, 0, 0, 0.1);
    border-radius: 0px 0px 8px 8px;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
    .formTotal-label {
      color: #2d3033;
      font-size: 1.125rem;
      font-weight: 700;
    }
    .formTotal-number {
      color: #2d3033;
      font-size: 24px;
      font-weight: 700;
      &.formTotal-number--red {
        color: #ea5d54;
      }
    }
  }
  .bento {
    padding: 20px 52px 0;
    @media (max-width: $viewport_desktop) {
      padding: 0 24px;
    }
    .bentoForm {
      .bentoForm-label {
        padding: 32px 0 10px;
        font-size: 1rem;
      }
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -40px;
        @media (max-width: $viewport_desktop) {
          margin-left: -24px;
        }
        > li {
          width: 50%;
          padding-left: 40px;
          @media (max-width: $viewport_desktop) {
            padding-left: 24px;
          }
          @media (max-width: $viewport_tablet) {
            width: 100%;
          }
        }
      }
      .bentoForm-select {
        max-width: 342px;
        display: flex;
        flex-wrap: wrap;
        margin-left: 1px;
        li {
          width: 50%;
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
}

label.btn,
label.btn .btn-iconLeft {
  line-height: 30px;
  cursor: pointer;
}

.prc-12 {
  padding-right: 12px;
}
.plc-12 {
  padding-left: 12px;
}
.basicGroup-title-view {
  color: #99a5ae;
}

.confirm {
  padding: 20px;
  text-align: left;
  > ul {
    list-style: disc;
    padding-left: 100px;
    max-height: 200px;
    ::marker {
      font-size: 2em;
      line-height: 31px;
    }

    > li {
      line-height: 31px;
      .confirmtitle {
        font-weight: bold;
      }
      > ul {
        list-style: circle;
        margin-left: 23px;
        ::marker {
          font-size: 1.5em;
          line-height: 31px;
        }
      }
    }
  }
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

.expensesTabs .disabled,
.btn.btn-select:disabled {
  cursor: default;
  text-decoration: none;
}

.invalid {
  width: 100%;
  padding-left: 5px;
  margin-top: 0.25rem;
  color: #dc3545;
}

.accordion-number--red {
  color: #ea5d54;
}

.confirm-dialog-custom {
  .content {
    font-size: 1rem;
    position: relative;
    margin-bottom: 30px;
    text-align: center;

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

  .confirm-btn {
    text-align: center;

    .btn {
      width: 180px;
      margin-right: 24px;
    }
  }
}

.title-link {
  cursor: pointer;
  color: #448add !important;
  &:hover {
    text-decoration: underline !important;
  }
}

.pre-loader {
  min-height: 94px;
}

.btn.btn-select:disabled {
  pointer-events: none;
}

.disabledOrg {
  pointer-events: none;
  cursor: not-allowed;
  background: #f5f7fa !important;
  color: #c0c4cc !important;
  border-color: #e4e7ed !important;
}

.file {
  .el-icon-close:before,
  .el-icon-loading:before {
    color: #2259b0;
    cursor: pointer;
  }
}

.loadingDownload {
  pointer-events: none;
  opacity: 0.5;
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

#conventionSession {
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
