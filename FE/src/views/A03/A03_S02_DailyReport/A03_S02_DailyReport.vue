<template>
  <div v-loading="isLoading" class="wrapContent">
    <div class="dailyReport">
      <div v-if="status_report !== '差戻'" class="dailyStep dailyStep--blue">
        <!-- 1 未提出 || 未作成 -->
        <el-steps v-if="status_report === '未提出' || status_report === '未作成'" class="listSteps" align-center>
          <el-step v-for="status_type in list_status_type" :key="status_type?.definition_value" :title="status_type?.definition_label" />
        </el-steps>

        <!-- 2 提出済 -->
        <el-steps v-else-if="status_report === '提出済'" class="listSteps" :active="1" align-center finish-status="success">
          <el-step
            v-for="status_type in list_status_type"
            :key="status_type?.definition_value"
            :style="status_type?.definition_value === '20' ? 'cursor: pointer;' : ''"
            @click="handleClickStep(`step${status_type?.definition_value}`)"
          >
            <template #title>
              <p v-if="status_type?.definition_value !== '20'">{{ status_type?.definition_label }}</p>
              <el-dropdown v-else trigger="click">
                <p :ref="`step${status_type?.definition_value}`" style="cursor: pointer" :class="'icon_warning_status'">
                  {{ status_type?.definition_label }}
                  <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                </p>
                <template #dropdown>
                  <el-dropdown-menu class="el-dropdown-customDailyReport customDropdownlst">
                    <el-dropdown-item v-for="(item, index) in user_approval" :key="index">
                      <div v-if="user_approval.length > 1 && item.approval_layer_num" class="title_layer">{{ item.approval_layer_num }} 次</div>
                      <div class="content_layer">
                        <div v-for="(person, indexSub) in item.data" :key="indexSub" class="item_layer">
                          <div class="left">
                            <img
                              v-if="
                                person.approval_layer_num == active_layer_approval &&
                                person.status_type == '承認待ち' &&
                                !item.data.some((x) => x.status_type == '承認済' || x.status_type == '差戻し')
                              "
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_pending-circle.svg"
                              alt=""
                            />
                            <img
                              v-else-if="person.status_type === '承認済'"
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_check-circle.svg"
                              alt=""
                            />
                            <img
                              v-else-if="person.status_type === '差戻し'"
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_close-circle.svg"
                              alt=""
                            />
                            <span
                              :class="
                                (person.approval_layer_num != active_layer_approval && person.status_type === '承認待ち') ||
                                (person.status_type === '承認待ち' && item.data.some((x) => x.status_type == '承認済' || x.status_type == '差戻し'))
                                  ? 'img-approve-invis'
                                  : ''
                              "
                            >
                              {{ person.user_name }}
                            </span>
                          </div>
                          <button v-if="person.comment && person.user_name" type="button" class="btn-comment" @click="openModalComment(person)">
                            <ImageSVG :src-image="'icon_comment_circle.svg'" :alt-image="'icon_comment_circle'" />
                          </button>
                        </div>
                      </div>
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </template>
          </el-step>
        </el-steps>

        <!-- 4 承認済 -->
        <el-steps v-else-if="status_report === '承認済'" class="listSteps" :active="2" align-center finish-status="success">
          <el-step
            v-for="status_type in list_status_type"
            :key="status_type?.definition_value"
            :style="status_type?.definition_value === '30' ? 'cursor: pointer;' : ''"
            @click="handleClickStep(`step${status_type?.definition_value}`)"
          >
            <template #title>
              <p v-if="status_type?.definition_value !== '30'">{{ status_type?.definition_label }}</p>
              <el-dropdown v-else trigger="click">
                <p :ref="`step${status_type?.definition_value}`" style="cursor: pointer" :class="'icon_warning_status'">
                  {{ status_type?.definition_label }}
                  <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                </p>
                <template #dropdown>
                  <el-dropdown-menu class="el-dropdown-customDailyReport customDropdownlst">
                    <el-dropdown-item v-for="(item, index) in user_approval" :key="index">
                      <div v-if="user_approval.length > 1 && item.approval_layer_num" class="title_layer">{{ item.approval_layer_num }} 次</div>
                      <div class="content_layer">
                        <div v-for="(person, indexSub) in item.data" :key="indexSub" class="item_layer">
                          <div class="left">
                            <img
                              v-if="
                                person.approval_layer_num == active_layer_approval &&
                                person.status_type == '承認待ち' &&
                                !item.data.some((x) => x.status_type == '承認済' || x.status_type == '差戻し')
                              "
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_pending-circle.svg"
                              alt=""
                            />
                            <img
                              v-else-if="person.status_type === '承認済'"
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_check-circle.svg"
                              alt=""
                            />
                            <img
                              v-else-if="person.status_type === '差戻し'"
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_close-circle.svg"
                              alt=""
                            />
                            <span
                              :class="
                                (person.approval_layer_num != active_layer_approval && person.status_type === '承認待ち') ||
                                (person.status_type === '承認待ち' && item.data.some((x) => x.status_type == '承認済' || x.status_type == '差戻し'))
                                  ? 'img-approve-invis'
                                  : ''
                              "
                            >
                              {{ person.user_name }}
                            </span>
                          </div>
                          <button v-if="person.comment && person.user_name" type="button" class="btn-comment" @click="openModalComment(person)">
                            <ImageSVG :src-image="'icon_comment_circle.svg'" :alt-image="'icon_comment_circle'" />
                          </button>
                        </div>
                      </div>
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </template>
          </el-step>
        </el-steps>
      </div>
      <!-- 3 差戻 -->
      <div v-else class="dailyStep dailyStep--red">
        <el-steps class="listSteps listSteps--red" align-center>
          <el-step
            v-for="status_type in list_status_type"
            :key="status_type?.definition_value"
            :class="{ 'hasBox-red': status_type?.definition_value === '20' }"
            :style="status_type?.definition_value === '20' ? 'cursor: pointer;' : ''"
            @click="handleClickStep(`step${status_type?.definition_value}`)"
          >
            <template #title>
              <p v-if="status_type?.definition_value !== '20'">{{ status_type?.definition_label }}</p>
              <el-dropdown v-else trigger="click">
                <p :ref="`step${status_type?.definition_value}`" style="cursor: pointer" :class="'icon_warning_status'">
                  {{ status_type?.definition_label }}
                  <ImageSVG :src-image="'icon_warning_small.svg'" :alt-image="'icon_warning'" />
                </p>
                <template #dropdown>
                  <el-dropdown-menu class="el-dropdown-customDailyReport customDropdownlst">
                    <el-dropdown-item v-for="(item, index) in user_approval" :key="index">
                      <div v-if="user_approval.length > 1 && item.approval_layer_num" class="title_layer">{{ item.approval_layer_num }} 次</div>
                      <div class="content_layer">
                        <div v-for="(person, indexSub) in item.data" :key="indexSub" class="item_layer">
                          <div class="left">
                            <img
                              v-if="
                                person.approval_layer_num == active_layer_approval &&
                                person.status_type == '承認待ち' &&
                                !item.data.some((x) => x.status_type == '承認済' || x.status_type == '差戻し')
                              "
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_pending-circle.svg"
                              alt=""
                            />
                            <img
                              v-else-if="person.status_type === '承認済'"
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_check-circle.svg"
                              alt=""
                            />
                            <img
                              v-else-if="person.status_type === '差戻し'"
                              class="svg"
                              src="@/assets/template/images/icon_majesticons_close-circle.svg"
                              alt=""
                            />
                            <span
                              :class="
                                (person.approval_layer_num != active_layer_approval && person.status_type === '承認待ち') ||
                                (person.status_type === '承認待ち' && item.data.some((x) => x.status_type == '承認済' || x.status_type == '差戻し'))
                                  ? 'img-approve-invis'
                                  : ''
                              "
                            >
                              {{ person.user_name }}
                            </span>
                          </div>
                          <button v-if="person.comment && person.user_name" type="button" class="btn-comment" @click="openModalComment(person)">
                            <ImageSVG :src-image="'icon_comment_circle.svg'" :alt-image="'icon_comment_circle'" />
                          </button>
                        </div>
                      </div>
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </template>
          </el-step>
        </el-steps>
      </div>

      <div class="dailyContent">
        <div class="daily-colForm">
          <div class="daily-dateTime">
            <p class="daily-dateTime-tlt">
              <span>{{ report_detail.report_period_start_date && formatFullDateCustom(report_detail.report_period_start_date) }}</span>
              <span v-if="mode_week && report_detail.report_period_end_date">{{ '～' + formatFullDateCustom(report_detail.report_period_end_date) }}</span>
              <span v-if="vacation" class="vacation">休暇 </span>
            </p>
          </div>
          <div class="daily-content scrollbar">
            <div class="daily-form daily-form-control-A3S2">
              <label class="daily-form__label" :class="mode_screen.includes('新規登録モード') || mode_screen.includes('編集モード') ? '' : 'text-view'">
                提出者メモ
              </label>
              <div class="daily-form__control">
                <el-input
                  v-if="mode_screen.includes('新規登録モード') || mode_screen.includes('編集モード')"
                  v-model="report_detail.submission_remarks"
                  class="form-control-textarea"
                  :rows="2"
                  type="textarea"
                  placeholder="内容入力"
                />
                <p v-else style="white-space: pre-line" class="daily-form-text">{{ report_detail.submission_remarks }}</p>
              </div>
            </div>
            <div class="daily-form daily-form-control-A3S2" :class="isSubmit && !validation.comment.length ? 'hasErr' : ''">
              <label class="daily-form__label" :class="mode_screen.includes('承認モード') ? '' : 'text-view'"> 上長メモ </label>
              <div class="daily-form__control">
                <el-input
                  v-if="mode_screen.includes('承認モード')"
                  v-model="report_detail.comment"
                  clearable
                  class="form-control-textarea"
                  :rows="2"
                  type="textarea"
                />
                <p v-else style="white-space: pre-line" :class="`daily-form-text ${mode_screen.includes('新規登録モード') ? 'daily-form-text2' : ''}`">
                  {{ report_detail.comment }}
                </p>
                <p v-if="isSubmit && !validation.comment.length" class="text-error">{{ getMessage('MSFA0012', 'コメント', 200) }}</p>
              </div>
            </div>
          </div>
          <div class="daily-btn">
            <button
              v-if="mode_screen.length > 0"
              type="button"
              :class="`btn ${mode_screen.includes('提出済みモード') ? '' : ''} btn-outline-primary btn-outline-primary--cancel`"
              @click="confirmCancel"
            >
              キャンセル
            </button>

            <!-- Chế độ đăng ký mới -->
            <template v-if="mode_screen.includes('新規登録モード')">
              <button type="button" :hidden="mode_week" class="btn btn-outline-primary btn-outline-primary--cancel" @click="vacationBtn">休暇</button>
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="temporarySaveBtn">一時保存</button>
              <button type="button" class="btn btn-primary" @click="submitBtn">提出</button>
            </template>

            <!-- Chế độ chỉnh sửa -->
            <template v-if="mode_screen.includes('編集モード')">
              <button v-if="vacation" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="vacationCancelBtn(true)">
                休暇取消
              </button>
              <button v-else type="button" :hidden="mode_week" class="btn btn-outline-primary btn-outline-primary--cancel" @click="vacationBtn">休暇</button>
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="temporarySaveBtn">一時保存</button>
              <button type="button" class="btn btn-primary" @click="submitBtn">提出</button>
            </template>

            <!-- Chế độ đã gửi -->
            <template v-if="mode_screen.includes('提出済みモード') && active_layer_approval == 1">
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="submissionCancelBtn(true)">提出取消</button>
            </template>

            <!-- Chế độ phê duyệt -->
            <template v-if="mode_screen.includes('承認モード')">
              <button type="button" class="btn btn-primary" @click="approvalBtn(true)">承認</button>
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="remandBtn(true)">差戻</button>
            </template>

            <!-- Chế độ được phê duyệt  -->
            <template v-if="mode_screen.includes('承認済みモード') && checkLastUserApprove()">
              <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="remandBtn(true)">差戻</button>
            </template>
          </div>
        </div>
        <div :class="`daily-colInfo ${!list_schedule.length ? 'daily-colInfo--nodata' : ''}`">
          <div
            v-if="(mode_week && list_schedule.length > 0) || (!mode_week && list_schedule[0]?.schedule?.length > 0)"
            ref="timeLineContent"
            class="timeLine scrollbar"
          >
            <div class="dailyList">
              <ul v-for="(element, indexx) in list_schedule" :key="indexx">
                <div v-if="mode_week" class="dailyList-calendar">
                  <span class="label">{{ formatFullDate(convertDateSf(element.date)) + ' (' + getDay(convertDateSf(element.date)) + ')' }}</span>
                </div>
                <li v-for="(item, index) in element.schedule" :id="`element${item.schedule_id}_${indexx}_${index}`" :key="index" class="after">
                  <div class="dailyList-dateTime">
                    <span class="time">
                      {{
                        item.all_day_flag == 1
                          ? '終日'
                          : formatTimeHourMinute(convertDateSf(item.start_time, true)) + '～' + formatTimeHourMinute(convertDateSf(item.end_time, true))
                      }}
                    </span>
                    <div class="iconItem">
                      <span class="item">
                        <ImageSVG :src-image="returnIcon(item.schedule_type)" :alt-image="'Schedule_division_icon'" />
                      </span>
                    </div>
                  </div>
                  <!-- schedule_type === '10' -->
                  <div v-if="item.schedule_type === '10'" class="dailyList-content">
                    <div class="dailyList-box">
                      <div
                        v-if="!mode_screen.some((x) => x === '参照モード') || (mode_screen.includes('参照モード') && item.flg_changeMemo)"
                        class="dailyList-btnCmt"
                      >
                        <button
                          v-if="
                            isEditMemoAproval ||
                            (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                            mode_screen.includes('新規登録モード') ||
                            (!(
                              isEditMemoAproval ||
                              (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                              mode_screen.includes('新規登録モード')
                            ) &&
                              item.report_detail_note)
                          "
                          :id="`btn${item.schedule_id}_${indexx}_${index}`"
                          type="button"
                          :class="`btn btn--icon  ${item.flg_changeMemo ? 'btn--hasNoti' : ''}`"
                          @click="
                            buttonClick(
                              `btn${item.schedule_id}_${indexx}_${index}`,
                              `down_bubble${item.schedule_id}_${indexx}_${index}`,
                              `element${item.schedule_id}_${indexx}_${index}`,
                              `${item.schedule_id}_${indexx}_${index}`
                            )
                          "
                        >
                          <ImageSVG :src-image="'icon_comment.svg'" :alt-image="'icon_comment'" />
                        </button>
                      </div>
                      <div class="dailyList-label">
                        <span v-if="item.off_label_flag" class="span-label span-label--off">
                          <span class="span-label-item">
                            <ImageSVG :src-image="'icon_exclamation-circle.svg'" :alt-image="'icon_exclamation-circle'" />
                          </span>
                          <span>オフラベルあり</span>
                        </span>
                        <span v-if="item.channel_type" class="span-label span-label--knowledge">
                          <span class="span-label-item">
                            <ImageSVG :src-image="'icon_knowledge.svg'" :alt-image="'icon_knowledge'" />
                          </span>
                          <span>ナレッジ登録済</span>
                        </span>
                      </div>
                      <div class="dailyList-group">
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt interview">
                            <p class="dailyList-info-label dailyList-gray">面談先</p>
                          </div>
                          <div class="dailyList-info-text">
                            <span
                              :class="`dailyList-bold ${checkPersonMedicalStaff(item) ? '' : 'dailyList-spanBlue  point log-icon'}`"
                              title_log="日報提出状況"
                              @click="checkPersonMedicalStaff(item) ? '' : callScreen('H02S02', { person_cd: item.person_cd })"
                            >
                              <span v-if="item.department_name"> {{ item.department_name }} 　 </span>
                              <span v-if="item.person_name"> {{ item.person_name }}　</span>
                              <span v-if="item.display_position_name">{{ item.display_position_name }} </span>
                            </span>
                          </div>
                        </div>
                        <div class="daily-accordion">
                          <el-collapse>
                            <el-collapse-item v-for="(el, idx) in item.detail" :key="idx + index" :name="item.schedule_id + idx + index">
                              <template #title>
                                <h2 class="article-head detail">
                                  <span>{{ el.detail_order + '. ' + el.content_name_tran + '　' + el.product_name }}</span>
                                </h2>
                              </template>
                              <div class="article-body">
                                <div class="daily-article">
                                  <ul>
                                    <li>
                                      <span class="daily-article-tlt">フェーズ</span>
                                      <span class="daily-article-label">{{ el.phase }}</span>
                                    </li>
                                    <li>
                                      <span class="daily-article-tlt">反応</span>
                                      <span class="daily-article-label">{{ el.reaction }}</span>
                                    </li>
                                  </ul>
                                  <p class="daily-article-text">{{ el.note }}</p>
                                </div>
                              </div>
                            </el-collapse-item>
                          </el-collapse>
                        </div>
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt interview">
                            <p class="dailyList-info-label dailyList-gray">面談者</p>
                          </div>
                          <div class="dailyList-info-text">
                            <span
                              class="dailyList-spanBlue dailyList-bold pointer log-icon"
                              title_log="面談者"
                              @click="callScreen('Z04S01', { user_cd: report_detail?.user_cd })"
                              >{{ item.user_name }}</span
                            >
                            <p class="dailyList-add">
                              <span class="dailyList-item">
                                <ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" />
                              </span>
                              <span>{{ item.org_label || '(所属なし)' }}</span>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="viewMore">
                        <span class="link pointer log-icon" @click="callScreenA01_S04(item)"> 詳細を見る </span>
                      </div>
                    </div>
                    <div :id="`note${item.schedule_id}_${indexx}_${index}`" class="message_area">
                      <span :id="`down_bubble${item.schedule_id}_${indexx}_${index}`" class="down_bubble">
                        <el-input
                          v-if="isEditMemoAproval || (role.includes('作成者') && mode_screen.includes('編集モード')) || mode_screen.includes('新規登録モード')"
                          v-model="item.report_detail_note"
                          class="form-control-textarea"
                          :rows="2"
                          type="textarea"
                          @change="handleChangeMemo(element)"
                        />
                        <div v-else class="noteBox">
                          <p class="dailyNote-text text-note">
                            {{ item.report_detail_note }}
                          </p>
                        </div>
                      </span>
                    </div>
                  </div>
                  <!-- schedule_type === '30' -->
                  <div v-else-if="item.schedule_type === '30'" class="dailyList-content">
                    <div class="dailyList-box">
                      <div
                        v-if="!mode_screen.some((x) => x === '参照モード') || (mode_screen.includes('参照モード') && item.flg_changeMemo)"
                        class="dailyList-btnCmt"
                      >
                        <button
                          v-if="
                            isEditMemoAproval ||
                            (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                            mode_screen.includes('新規登録モード') ||
                            (!(
                              isEditMemoAproval ||
                              (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                              mode_screen.includes('新規登録モード')
                            ) &&
                              item.report_detail_note)
                          "
                          :id="`btn${item.schedule_id}_${indexx}_${index}`"
                          type="button"
                          :class="`btn btn--icon  ${item.flg_changeMemo ? 'btn--hasNoti' : ''}`"
                          @click="
                            buttonClick(
                              `btn${item.schedule_id}_${indexx}_${index}`,
                              `down_bubble${item.schedule_id}_${indexx}_${index}`,
                              `element${item.schedule_id}_${indexx}_${index}`,
                              `${item.schedule_id}_${indexx}_${index}`
                            )
                          "
                        >
                          <ImageSVG :src-image="'icon_comment.svg'" :alt-image="'icon_comment'" />
                        </button>
                      </div>
                      <div class="dailyList-label">
                        <span v-if="item.channel_type" class="span-label span-label--knowledge">
                          <span class="span-label-item">
                            <ImageSVG :src-image="'icon_knowledge.svg'" :alt-image="'icon_knowledge'" />
                          </span>
                          <span>ナレッジ登録済</span>
                        </span>
                      </div>
                      <div class="dailyList-group">
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt convention">
                            <p class="dailyList-info-label dailyList-gray">講演会名</p>
                          </div>
                          <div class="dailyList-info-text">
                            <span class="dailyList-spanBlue dailyList-bold pointer" @click="callScreen('C01S02', { schedule_id: item.schedule_id })">
                              {{ item.convention_name }}
                            </span>
                          </div>
                        </div>
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt convention">
                            <p class="dailyList-info-label dailyList-gray">講演会区分</p>
                          </div>
                          <div class="dailyList-info-text">
                            <p class="dailyList-spanGray">{{ item.definition_label }}</p>
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
                            <p class="dailyList-spanGray">{{ item.org_label }}</p>
                          </div>
                        </div>
                        <div class="daily-accordion">
                          <el-collapse accordion>
                            <el-collapse-item :name="item.schedule_id">
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
                    <div :id="`note${item.schedule_id}_${indexx}_${index}`" class="message_area">
                      <span :id="`down_bubble${item.schedule_id}_${indexx}_${index}`" class="down_bubble">
                        <el-input
                          v-if="isEditMemoAproval || (role.includes('作成者') && mode_screen.includes('編集モード')) || mode_screen.includes('新規登録モード')"
                          v-model="item.report_detail_note"
                          class="form-control-textarea"
                          :rows="2"
                          type="textarea"
                          @change="handleChangeMemo(element)"
                        />
                        <div v-else class="noteBox">
                          <p class="dailyNote-text text-note">
                            {{ item.report_detail_note }}
                          </p>
                        </div>
                      </span>
                    </div>
                  </div>
                  <!-- schedule_type === '20' -->
                  <div v-else-if="item.schedule_type === '20'" class="dailyList-content">
                    <div class="dailyList-box">
                      <div
                        v-if="!mode_screen.some((x) => x === '参照モード') || (mode_screen.includes('参照モード') && item.flg_changeMemo)"
                        class="dailyList-btnCmt"
                      >
                        <button
                          v-if="
                            isEditMemoAproval ||
                            (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                            mode_screen.includes('新規登録モード') ||
                            (!(
                              isEditMemoAproval ||
                              (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                              mode_screen.includes('新規登録モード')
                            ) &&
                              item.report_detail_note)
                          "
                          :id="`btn${item.schedule_id}_${indexx}_${index}`"
                          type="button"
                          :class="`btn btn--icon  ${item.flg_changeMemo ? 'btn--hasNoti' : ''}`"
                          @click="
                            buttonClick(
                              `btn${item.schedule_id}_${indexx}_${index}`,
                              `down_bubble${item.schedule_id}_${indexx}_${index}`,
                              `element${item.schedule_id}_${indexx}_${index}`,
                              `${item.schedule_id}_${indexx}_${index}`
                            )
                          "
                        >
                          <ImageSVG :src-image="'icon_comment.svg'" :alt-image="'icon_comment'" />
                        </button>
                      </div>
                      <div class="dailyList-label">
                        <span v-if="item.channel_type" class="span-label span-label--knowledge">
                          <span class="span-label-item">
                            <ImageSVG :src-image="'icon_knowledge.svg'" :alt-image="'icon_knowledge'" />
                          </span>
                          <span>ナレッジ登録済</span>
                        </span>
                      </div>
                      <div class="dailyList-group">
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt briefing">
                            <p class="dailyList-info-label dailyList-gray">説明会名</p>
                          </div>
                          <div class="dailyList-info-text">
                            <span class="dailyList-spanBlue dailyList-bold pointer" @click="callScreen('B01S02', { briefing_id: item.briefing_id })">
                              {{ item.briefing_name }}
                            </span>
                          </div>
                        </div>
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt briefing">
                            <p class="dailyList-info-label dailyList-gray">説明会区分</p>
                          </div>
                          <div class="dailyList-info-text">
                            <p class="dailyList-spanGray">{{ item.definition_label }}</p>
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
                            <p class="dailyList-spanGray">{{ item.facility_short_name }}</p>
                          </div>
                        </div>
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt briefing">
                            <p class="dailyList-info-label dailyList-gray">実施者</p>
                          </div>
                          <div class="dailyList-info-text">
                            <p class="dailyList-spanBlue dailyList-bold pointer" @click="callScreen('Z04S01', { user_cd: report_detail?.user_cd })">
                              {{ item.implement_user_name }}
                            </p>
                            <p class="dailyList-add">
                              <span class="dailyList-item">
                                <ImageSVG :src-image="'icon_building.svg'" :alt-image="'icon_building'" />
                              </span>
                              <span>{{ item.implement_user_org_label || '(所属なし)' }}</span>
                            </p>
                          </div>
                        </div>
                        <div class="daily-accordion">
                          <el-collapse accordion>
                            <el-collapse-item :name="item.schedule_id">
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
                    <div :id="`note${item.schedule_id}_${indexx}_${index}`" class="message_area">
                      <span :id="`down_bubble${item.schedule_id}_${indexx}_${index}`" class="down_bubble">
                        <el-input
                          v-if="isEditMemoAproval || (role.includes('作成者') && mode_screen.includes('編集モード')) || mode_screen.includes('新規登録モード')"
                          v-model="item.report_detail_note"
                          class="form-control-textarea"
                          :rows="2"
                          type="textarea"
                          @change="handleChangeMemo(element)"
                        />
                        <div v-else class="noteBox">
                          <p class="dailyNote-text text-note">
                            {{ item.report_detail_note }}
                          </p>
                        </div>
                      </span>
                    </div>
                  </div>
                  <!-- schedule_type === '40' || schedule_type === '50' -->
                  <div v-else-if="item.schedule_type === '40' || item.schedule_type === '50'" class="dailyList-content">
                    <div class="dailyList-box">
                      <div
                        v-if="!mode_screen.some((x) => x === '参照モード') || (mode_screen.includes('参照モード') && item.flg_changeMemo)"
                        class="dailyList-btnCmt"
                      >
                        <button
                          v-if="
                            isEditMemoAproval ||
                            (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                            mode_screen.includes('新規登録モード') ||
                            (!(
                              isEditMemoAproval ||
                              (role.includes('作成者') && mode_screen.includes('編集モード')) ||
                              mode_screen.includes('新規登録モード')
                            ) &&
                              item.report_detail_note)
                          "
                          :id="`btn${item.schedule_id}_${indexx}_${index}`"
                          type="button"
                          :class="`btn btn--icon ${item.flg_changeMemo ? 'btn--hasNoti' : ''}`"
                          @click="
                            buttonClick(
                              `btn${item.schedule_id}_${indexx}_${index}`,
                              `down_bubble${item.schedule_id}_${indexx}_${index}`,
                              `element${item.schedule_id}_${indexx}_${index}`,
                              `${item.schedule_id}_${indexx}_${index}`
                            )
                          "
                        >
                          <ImageSVG :src-image="'icon_comment.svg'" :alt-image="'icon_comment'" />
                        </button>
                      </div>
                      <div class="dailyList-label">
                        <span v-if="item.channel_type" class="span-label span-label--knowledge">
                          <span class="span-label-item">
                            <ImageSVG :src-image="'icon_knowledge.svg'" :alt-image="'icon_knowledge'" />
                          </span>
                          <span>ナレッジ登録済</span>
                        </span>
                      </div>
                      <div class="dailyList-group">
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt external">
                            <p class="dailyList-info-label dailyList-gray">社内予定区分</p>
                          </div>
                          <div class="dailyList-info-text">
                            <p class="dailyList-spanGray">{{ item.office_schedule_type_name }}</p>
                          </div>
                        </div>
                        <div class="dailyList-info">
                          <div class="dailyList-info-tlt external">
                            <p class="dailyList-info-label dailyList-gray">タイトル</p>
                          </div>
                          <div class="dailyList-info-text">
                            <p class="dailyList-spanGray">{{ item.title }}</p>
                          </div>
                        </div>
                        <div class="daily-accordion">
                          <el-collapse accordion>
                            <el-collapse-item :name="item.schedule_id">
                              <template #title>
                                <h2 class="article-head">
                                  <span>特記事項</span>
                                </h2>
                              </template>
                              <div :class="`article-body ${item?.note?.length > 0 ? '' : 'article-body-nodata'}`">
                                <div class="daily-article">
                                  <p class="daily-article-text">{{ item.remarks }}</p>
                                </div>
                              </div>
                            </el-collapse-item>
                          </el-collapse>
                        </div>
                      </div>
                    </div>
                    <div :id="`note${item.schedule_id}_${indexx}_${index}`" class="message_area">
                      <span :id="`down_bubble${item.schedule_id}_${indexx}_${index}`" class="down_bubble">
                        <el-input
                          v-if="isEditMemoAproval || (role.includes('作成者') && mode_screen.includes('編集モード')) || mode_screen.includes('新規登録モード')"
                          v-model="item.report_detail_note"
                          class="form-control-textarea"
                          :rows="2"
                          type="textarea"
                          @change="handleChangeMemo(element)"
                        />
                        <div v-else class="noteBox">
                          <p class="dailyNote-text text-note">
                            {{ item.report_detail_note }}
                          </p>
                        </div>
                      </span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div v-else-if="!list_schedule.length" class="nodata">
            <div v-if="!isLoading">
              <span>該当するデータがありません。</span>
              <ImageSVG :src-image="'amico.svg'" :alt-image="'amico'" />
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
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
    >
      <template #default>
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closeModal" @handleYes="handleConfirmYes"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
/* eslint-disable eqeqeq */
import A03_S02_Service from '@/api/A03/A03_S02_DailyReportServices';
import B01_S02_ModalComment from '@/views/B01/B01_S02_BriefingSessionInput/B01_S02_ModalComment.vue';
import { markRaw } from 'vue';
import _ from 'lodash';
import { validLength } from '@/utils/validate';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';
import Z05_S04_facilityCustomerService from '@/api/Z05/Z05_S04_facilityCustomerService';

export default {
  name: 'A03_S02_DailyReport',
  props: {
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreen'],
  data() {
    return {
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
      report_detail: {
        report_id: '',
        report_period_start_date: '', // A1
        report_period_end_date: '', // A2
        report_status_type: '',
        submission_remarks: '', // D1
        user_cd: '',
        user_name: '',
        org_cd: '',
        org_label: '',
        created_by: '',
        proxy_created_by: '',
        created_at: '',
        updated_by: '',
        proxy_updated_by: '',
        updated_at: '',
        comment: '' // D2
      },
      initParams: {
        report_period_start_date: '',
        report_period_end_date: '',
        report_id: ''
      },
      list_schedule: [],

      list_scheduleOld: [],
      report_detailOld: {},

      user_approval: [],
      user_check: false, // user_check === true => 承認者 (system)
      vacation: false, // true: 休暇取消, false: 休暇
      mode_screen: [], // 6 mode
      mode_week: false, // startDate === endDate ? mode date : mode week
      role: [],
      status_report: '',
      list_status_type: [],
      isLoading: false,
      list_scheduleChange: [],
      isChangeMemo: false,
      userCd: '',
      userName: '',
      orgCd: '',
      orgName: '',
      currentData: {},
      isEditMemoAproval: false,
      active_layer_approval: 1,
      currentUser: {},
      isLoginProxy: false,
      isUserApprover: false,
      propParamsDaily: {},
      idClickeds: [],
      lstScheduleFlat: [],
      typeButton: '',
      onScrollTop: 0,
      lstMedicalStaff: []
    };
  },
  computed: {
    validation() {
      return {
        comment: {
          length: validLength(this.report_detail.comment, 200)
        }
      };
    }
  },

  mounted() {
    this.emitter.emit('change-header', {
      title: '報告入力',
      icon: 'icon_speech-meeting-color.svg',
      isShowBack: true
    });
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.A03_S02_DailyReport_timeline || 0;

    this.propParamsDaily = this._route('A03_S02_DailyReport')?.params;

    this.initParams = {
      ...this.initParams,
      report_id: this.propParamsDaily?.reportId || '',
      report_period_start_date: this.propParamsDaily?.startDate || '',
      report_period_end_date: this.propParamsDaily?.endDate || ''
    };

    this.userCd = this.propParamsDaily?.user_cd || '';
    this.userName = this.propParamsDaily?.user_name || '';
    this.orgCd = this.propParamsDaily?.org_cd || '';
    this.orgName = this.propParamsDaily?.org_label || '';

    this.isLoginProxy = localStorage.getItem('isLoginProxy') || false;

    this.currentUser = this.getCurrentUser();

    this.getMedicalStaff();
  },
  methods: {
    setScrollTop() {
      let scrollTop = this.$refs.timeLineContent ? this.$refs.timeLineContent.scrollTop : 0;
      this.setScrollScreen('A03_S02_DailyReport_timeline', scrollTop);
    },

    // define func component
    buttonClick(icon_name, bubble_name, element_name, element_id) {
      if (this.idClickeds.includes(element_id)) {
        this.idClickeds = this.idClickeds.filter((x) => x !== element_id);
      } else {
        this.idClickeds.push(element_id);
      }

      var icon_class = document.getElementById(icon_name);
      var bubble_class = document.getElementById(bubble_name);
      var element_class = document.getElementById(element_name);
      if (!bubble_class.classList.contains('clicked')) {
        icon_class.classList.add('clicked');
        bubble_class.classList.add('clicked');
        element_class.classList.add('mrb-message');
      } else {
        icon_class.classList.remove('clicked');
        bubble_class.classList.remove('clicked');
        element_class.classList.remove('mrb-message');
      }
    },

    handleClickStep(step) {
      if (step === 'step20' || step === 'step30') this.$refs[step]?.[0].click();
    },
    checkRole() {
      let modeScreen = [];
      let role = [];
      this.mode_week = !(this.initParams.report_period_start_date === this.initParams.report_period_end_date);
      if (this.currentUser.user_cd === this.report_detail.user_cd) {
        role.push('作成者');
      }

      if (this.user_approval) {
        role.push('承認者');
      }

      if (!this.report_detail.report_id) {
        this.mode_screen.push('新規登録モード');
        role = ['作成者'];
        return;
      }

      role.forEach((x) => {
        switch (x) {
          case '作成者': {
            if (this.report_detail.report_status_type === '10') {
              modeScreen.push('編集モード');
            } else if (this.report_detail.report_status_type === '20') {
              modeScreen.push('提出済みモード');
              if (this.active_layer_approval == 1) {
                modeScreen.push('提出済みモード');
              } else {
                modeScreen.push('参照モード');
              }
            } else {
              modeScreen.push('参照モード');
            }
            break;
          }
          case '承認者': {
            if (this.report_detail.report_status_type === '20') {
              if (this.isUserApprover) {
                modeScreen.push('承認モード');
              } else {
                modeScreen.push('参照モード');
              }
            } else if (this.report_detail.report_status_type === '30') {
              modeScreen.push('承認済みモード');
            }
            break;
          }
          default:
            modeScreen.push('参照モード');
            break;
        }
      });

      if (modeScreen.includes('編集モード') && this.report_detail.report_status_type === '10' && this.currentUser.user_cd === this.report_detail.user_cd) {
        this.isEditMemoAproval = true;
      } else {
        this.isEditMemoAproval = false;
      }

      this.mode_screen = modeScreen;
      this.role = role;
    },
    returnIcon(id) {
      // 10 面談
      // 20 説明会
      // 30 講演会
      // 40 社内予定
      // 50 プライベート
      const obj = {
        10: 'icon_interview-daily.svg',
        20: 'icon_interview-daily02.svg',
        30: 'icon_interview-daily01.svg',
        40: 'icon-plan.svg', // require change (3rd)
        50: 'icon-plan.svg',
        承認待ち: 'icon_majesticons_pending-circle.svg', // chờ phê duyệt
        承認済: 'icon_majesticons_check-circle.svg', // đã phê duyệt
        差戻し: 'icon_majesticons_close-circle.svg' // remand
      };
      return obj[id];
    },
    callScreenA01_S04(item) {
      let params = {
        call_id: item.call_id,
        schedule_id: item.schedule_id
      };
      A01_S04_Service.checkInterviewDetailInput(params)
        .then(() => {
          this.callScreen('A01S04', params);
        })
        .catch(() => this.$notify({ message: '面談情報が削除されたため、詳細を見ることができません。', customClass: 'error' }))
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
      this.setScrollTop();
    },
    handleChangeMemo(objDateChange) {
      if (objDateChange.date) {
        this.isChangeMemo = true;
        const list_scheduleTemp = this.list_scheduleChange;
        const index = list_scheduleTemp.findIndex((el) => el.date === objDateChange.date);

        if (index === -1) {
          list_scheduleTemp.push({
            date: objDateChange.date,
            schedule: objDateChange.schedule
          });
        } else {
          list_scheduleTemp[index] = {
            date: objDateChange.date,
            schedule: objDateChange.schedule
          };
        }
        this.list_scheduleChange = list_scheduleTemp;
      } else {
        this.list_scheduleChange = [];
      }
    },
    closeModal() {
      this.modalConfig.isShowModal = false;
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
    // キャンセルボタン
    cancelBtn() {
      this.back();
    },

    // 一時保存ボタン
    temporarySaveBtn() {
      let params = {
        report_detail: [this.report_detail],
        list_schedule: this.isChangeMemo ? this.list_scheduleChange : this.list_schedule
      };
      if (!this.reportId) {
        params = {
          ...params,
          list_schedule: this.list_schedule
        };
      }
      this.temporarilyVacationReport(params);
    },
    // 提出ボタン
    submitBtn() {
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error', duration: 1500 });
        return;
      } else {
        const params = {
          report_detail: [
            {
              ...this.report_detail,
              report_status_type: '20'
            }
          ],
          list_schedule: this.isChangeMemo ? this.list_scheduleChange : this.list_schedule
        };
        this.submitReport(params);
      }
    },
    // 提出取り消しボタン || 提出取消
    submissionCancelBtn(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('cancelSubmit');
      } else {
        const params = {
          report_id: this.report_detail.report_id,
          report_status_type: this.report_detail.report_status_type
        };
        this.deleteReport(params);
      }
    },
    // 休暇ボタン
    vacationBtn() {
      this.registerVacationReport({
        report_detail: [this.report_detail],
        list_schedule: this.list_schedule
      });
    },
    // 休暇取り消しボタン || 休暇取消ボタン
    vacationCancelBtn(isConfirm) {
      if (isConfirm) {
        this.openModalConfirm('cancelVacation');
      } else {
        this.deleteVacationReport(this.report_detail.report_id);
      }
    },
    // 承認ボタン
    approvalBtn(isConfirm) {
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error', duration: 1500 });
        return;
      } else {
        if (isConfirm) {
          this.openModalConfirm('approval');
        } else {
          const params = {
            user_cd: this.report_detail.user_cd,
            report_id: this.report_detail.report_id,
            comment: this.report_detail.comment,
            report_status_type: '30',
            active_layer_approval: this.active_layer_approval
          };
          this.approvalReport(params);
        }
      }
    },
    // 差し戻しボタン
    remandBtn(isConfirm) {
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error', duration: 1500 });
        return;
      } else {
        if (isConfirm) {
          this.openModalConfirm('remand');
        } else {
          const params = {
            user_cd: this.report_detail.user_cd,
            report_id: this.report_detail.report_id,
            comment: this.report_detail.comment,
            report_status_type: '10',
            active_layer_approval: this.active_layer_approval
          };
          this.cancelApprovalReport(params);
        }
      }
    },
    // call api
    getMedicalStaff() {
      let params = {
        facility_cd: ''
      };
      this.isLoading = true;

      this.lstMedicalStaff = [];
      Z05_S04_facilityCustomerService.getMedicalStaff(params)
        .then((res) => {
          this.lstMedicalStaff = res?.data?.data.map((x) => x.medical_staff_cd);

          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => {
          this.isLoading = false;
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
    getDailyReportDetail(params) {
      A03_S02_Service.getDailyReportDetailService(params)
        .then((res) => {
          const dataRes = res.data.data;
          if (!dataRes.report_detail.report_id || dataRes.report_detail?.length)
            this.report_detail = {
              ...this.report_detail,
              report_id: '',
              report_period_start_date: this.initParams.report_period_start_date,
              report_period_end_date: this.initParams.report_period_end_date,
              report_status_type: '10',
              submission_remarks: this.report_detail.submission_remarks || '',
              comment: this.report_detail.comment || '',
              user_cd: this.userCd,
              user_name: this.userName,
              org_cd: this.orgCd,
              org_label: this.orgName
            };
          else {
            this.report_detail = {
              ...dataRes.report_detail,
              submission_remarks: dataRes.report_detail.submission_remarks || '',
              comment: dataRes.report_detail.comment || ''
            };

            this.active_layer_approval = dataRes.active_layer_approval;
          }
          this.list_schedule = dataRes.list_schedule.map((el) => ({
            ...el,
            schedule: el.schedule.map((it) => ({
              ...it,
              flg_changeMemo: it.report_detail_note.length ? true : false
            }))
          }));
          const notEmpty = (arr) => (arr.length ? arr : [undefined]);
          this.lstScheduleFlat = this.list_schedule.flatMap(({ date, schedule }, index) =>
            notEmpty(schedule).map((schedule, sindex) => ({ date, ...schedule, custom_id: `${schedule.schedule_id}_${index}_${sindex}` }))
          );
          this.report_detailOld = { ...this.report_detail };
          this.list_scheduleOld = JSON.parse(JSON.stringify(this.list_schedule));

          this.user_approval = dataRes.user_approval;
          this.user_check = dataRes.user_check;
          this.vacation = dataRes.vacation;
          this.status_report = dataRes.status_report;
          this.list_status_type = dataRes.list_status_type;
          this.currentData = { ...dataRes.report_detail, list_schedule: this.list_schedule };
          this.user_approval = this.convertUserApproval('approval_layer_num');

          this.checkLayerUserApproval();
          this.checkRole();
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));
          if (this.$refs.timeLineContent) {
            if (this.onScrollTop) {
              this.$refs.timeLineContent.scrollTop = this.onScrollTop;
            } else {
              this.$refs.timeLineContent.scrollTop = 0;
            }
          }

          this.js_pscroll();

          for (const i in this.idClickeds) {
            let id = this.idClickeds[i];
            let schedule = this.lstScheduleFlat.find((x) => x.custom_id === id);

            var icon_class = document.getElementById(`btn${id}`);
            var bubble_class = document.getElementById(`down_bubble${id}`);
            var element_class = document.getElementById(`element${id}`);

            if (schedule && schedule.report_detail_note) {
              icon_class.classList.add('clicked');
              bubble_class.classList.add('clicked');
              element_class.classList.add('mrb-message');
            } else {
              bubble_class?.classList.remove('clicked');
              element_class?.classList.remove('mrb-message');
              icon_class?.classList.remove('clicked');
            }
          }
          this.isLoading = false;
        });
    },

    convertUserApproval(pathName) {
      return _.chain(this.user_approval)
        .groupBy(pathName)
        .map((value, key) => {
          return { [pathName]: key, data: value };
        })
        .value();
    },

    checkLastUserApprove() {
      let layerLastApprove = this.user_approval.length;
      if (this.user_approval[layerLastApprove - 1].data.some((x) => x.approval_user_cd === this.currentUser?.user_cd)) {
        return true;
      }
      return false;
    },

    checkLayerUserApproval() {
      this.isUserApprover = false;

      if (this.user_approval.length === 0) {
        this.isUserApprover = false;
      } else {
        for (let x in this.user_approval) {
          let layer = this.user_approval[x];
          if (layer.approval_layer_num == this.active_layer_approval) {
            let userApprove = layer.data.find((x) => x.approval_user_cd === this.currentUser?.user_cd);
            if (layer.data.some((x) => x.status_type == '承認済' || x.status_type == '差戻し')) {
              this.isUserApprover = false;
            } else {
              if (userApprove && userApprove.status_type == '承認待ち') {
                this.isUserApprover = true;
              }
            }
          }
        }
      }
    },

    registerVacationReport(params) {
      this.isLoading = true;
      A03_S02_Service.registerVacationReportService(params)
        .then((res) => {
          this.initParams = { ...this.initParams, report_id: res.data.data.report_id };
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.isLoading = false));
    },
    deleteVacationReport(params) {
      this.isLoading = true;
      A03_S02_Service.deleteVacationReportService({ report_id: params })
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0050'), customClass: 'success' });
          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;
          this.onCloseModal();
        });
    },
    temporarilyVacationReport(params) {
      this.isLoading = true;
      A03_S02_Service.temporarilyVacationReportService(params)
        .then((res) => {
          this.initParams = { ...this.initParams, report_id: res.data.data.report_id };
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.isLoading = false));
    },
    submitReport(params) {
      this.isLoading = true;
      A03_S02_Service.submitReportService(params)
        .then((res) => {
          this.initParams = { ...this.initParams, report_id: res.data.data.report_id };
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.isLoading = false));
    },
    deleteReport(params) {
      this.isLoading = true;
      A03_S02_Service.deleteReportService(params)
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });
          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;
          this.onCloseModal();
        });
    },
    approvalReport(params) {
      this.isLoading = true;
      A03_S02_Service.approvalReportService(params)
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });

          let countItemNotApprovedList = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.A03_S03_NotApprovedList_count || 10;
          let currentPageNotApprovedList = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.A03_S03_NotApprovedList || 1;
          if (countItemNotApprovedList === 1) {
            this.setCurrentPageScreen('A03_S03_NotApprovedList', currentPageNotApprovedList > 1 ? currentPageNotApprovedList - 1 : 1);
          }

          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;
          this.onCloseModal();
        });
    },
    cancelApprovalReport(params) {
      this.isLoading = true;
      A03_S02_Service.cancelApprovalReportService(params)
        .then(() => {
          this.$notify({ message: this.getMessage('MSFA0049'), customClass: 'success' });

          let countItemNotApprovedList = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.A03_S03_NotApprovedList_count || 10;
          let currentPageNotApprovedList = +JSON.parse(localStorage.getItem('CurrentPageScreen'))?.A03_S03_NotApprovedList || 1;
          if (countItemNotApprovedList === 1) {
            this.setCurrentPageScreen('A03_S03_NotApprovedList', currentPageNotApprovedList > 1 ? currentPageNotApprovedList - 1 : 1);
          }

          this.getDailyReportDetail(this.initParams);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;
          this.onCloseModal();
        });
    },

    openModalConfirm(type, isDelete) {
      this.typeButton = type;
      let message = '';
      switch (this.typeButton) {
        case 'approval':
          message = 'この報告の提出を承認します。よろしいですか？';
          this.openConfirm(type, isDelete, message);
          break;
        case 'remand':
          message = 'この報告の提出を差し戻します。よろしいですか？';
          this.openConfirm(type, isDelete, message);
          break;
        case 'cancelSubmit':
          message = 'この報告の提出を取り消します。よろしいですか？';
          this.openConfirm(type, isDelete, message);
          break;
        case 'cancelVacation':
          message = 'この休暇を取り消します。よろしいですか？';
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
        props: { mode: isDelete ? 0 : 1, message: message, title: '' }
      };
    },

    handleConfirmYes() {
      switch (this.typeButton) {
        case 'approval':
          this.approvalBtn();
          break;
        case 'remand':
          this.remandBtn();
          break;
        case 'cancelSubmit':
          this.submissionCancelBtn();
          break;
        case 'cancelVacation':
          this.vacationCancelBtn();
          break;
        case 'cancel':
          this.cancelBtn();
          break;

        default:
          break;
      }
    },

    confirmCancel() {
      this.typeButton = 'cancel';
      let isEqual = !_.isEqual(this.report_detail, this.report_detailOld) || !_.isEqual(this.list_schedule, this.list_scheduleOld);
      if (isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1 },
          width: '35rem',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.cancelBtn();
      }
    },

    onCloseModal() {
      this.typeButton = '';
      this.modalConfig = { ...this.modalConfig, isShowModal: false, isShowClose: false, isShowModalConfirmInput: false, customClass: 'custom-dialog' };
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 1024px;
$viewport_tablet-mini: 786px;
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
.vacation {
  background: #e3e3e3;
  padding: 3px 12px;
  color: #5f6b73;
  border-radius: 20px;
  font-size: 14px;
  margin-left: 10px;
  font-weight: 500;
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

.timeLine {
  position: relative;
  height: 100%;
  padding: 0 20px 0 5px;
  &::before {
    position: absolute;
    top: 0;
    left: 165px;
    content: '';
    width: 2px;
    background: #b7c3cb;
    display: block;
    transition: all 0.2s;
    height: 100%;
  }
}

.dailyReport {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding-top: 28px;
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
  .dailyContent {
    height: 100%;
    display: flex;
    flex-wrap: wrap;
    padding: 18px;
    overflow: hidden;
    @media (max-width: $viewport_tablet-mini) {
      overflow-y: auto;
      height: initial;
      padding: 18px 24px 20px;
    }
    .daily-colForm {
      width: 40%;
      padding-right: 10px;
      height: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      @media (max-width: $viewport_tablet-mini) {
        width: 100%;
        height: initial;
        padding-right: 0;
      }
    }
    .daily-colInfo {
      width: 60%;
      height: 100%;
      background: #f7f7f7;
      box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
      border-radius: 20px 0px 0px 0px;
      padding: 20px 0;
      @media (max-width: $viewport_tablet-mini) {
        width: 100%;
        height: 400px;
        border-radius: 20px 20px 0px 0px;
      }
    }
    .daily-colInfo--nodata {
      padding: 0;
    }
  }
  .daily-dateTime {
    .daily-dateTime-tlt {
      font-size: 20px;
      font-weight: 700;
      display: flex;
      align-items: center;
      .item {
        min-width: 23px;
        margin-right: 8px;
        margin-top: -2px;
      }
    }
  }
  .daily-content {
    height: 100%;
  }
  .daily-btn {
    display: flex;
    justify-content: flex-end;
    padding: 10px 0;
    @media (min-width: 1024px) {
      display: block;
      text-align: end;
    }
    .btn {
      margin-bottom: 10px;
      width: 100px;
      font-size: 14px;
      padding: 5px;

      @media (max-width: 1080px) {
        width: 85px;
      }
    }

    .btn + .btn {
      margin-left: 5px;
    }
  }
  .daily-form {
    margin-top: 5px;
    .daily-form__label {
      margin-bottom: 5px;
      font-size: 1rem;
    }
    .daily-form__control {
      .form-control-textarea {
        .el-textarea__inner {
          height: 120px;
        }
      }
      .daily-form-text {
        color: #2d3033;
        height: 180px;
        overflow: auto;
        @media (max-height: 768px) {
          height: 140px;
        }
      }
      .daily-form-text2 {
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        background: #f7f7f7;
      }
    }
  }
  .dailyList {
    height: 100%;
    padding: 10px;
    .dailyList-calendar {
      width: 100%;
      .label {
        background: #d9e7fd;
        color: #285888;
        text-align: center;
        border-radius: 20px 0px 0px 20px;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        padding: 9px 10px;
        width: 150px;
        display: block;
        font-weight: 700;
        @media (max-width: $viewport_desktop) {
          padding: 6px 0;
        }
      }
    }
    > ul {
      display: grid;
      grid-template-rows: auto;
      gap: 15px;
      li:first-child {
        padding-top: 17px;
      }
      > li {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        position: relative;
        &::before {
          position: absolute;
          top: 0;
          left: 150px;
          content: '';
          width: 2px;
          background: #b7c3cb;
          display: block;
          transition: all 0.2s;
          height: 100%;
        }
        &.after::before {
          height: calc(100% + 26px);
        }

        .dailyList-dateTime {
          width: 220px;
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          padding-top: 20px;
          .time {
            width: 130px;
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
              right: -46px;
              content: '';
              height: 2px;
              width: 40px;
              background: #b7c3cb;
              display: block;
            }
            .item {
              position: relative;
              z-index: 1;
            }
          }
        }
        .dailyList-content {
          width: calc(100% - 220px);
          position: relative;
          z-index: 1;
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
                .pointer {
                  cursor: pointer;
                }
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
                    padding-top: 0;
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
          .dailyNote-text {
            height: 80px;
          }
        }
        .message {
          display: none;
        }
      }
    }
  }
}

.text-note {
  color: #225999;
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

.text-view {
  color: #727f88;
}

.title_layer {
  font-weight: bold;
  border-bottom: 1px solid #2d5999;
}

.content_layer {
  margin-top: 5px;
  margin-bottom: 15px;

  .item_layer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    .left {
      display: flex;
      align-items: center;
    }
  }
}
</style>
