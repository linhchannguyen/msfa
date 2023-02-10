<template>
  <div class="wrapBody">
    <div class="wrapContent">
      <div class="groupRow groupQa-detail scrollbar">
        <div v-loading="loadingInfoQuestion" class="group-col1">
          <div class="headDetail">
            <div class="headeTop">
              <div class="headDetail-label">
                <span
                  :class="info_question.question_status_type_label === '回答受付中' ? 'spanLabel--accepting' : 'spanLabel--accepting2'"
                  class="spanLabel spanLabel--accepting"
                >
                  {{ info_question.question_status_type_label }}</span
                >
                <span class="spanLabel spanLabel--category">{{ info_question.qa_category_name }}</span>
              </div>
              <div class="head-tag">
                <ul>
                  <li v-if="info_question.status_new === 1"><img src="@/assets/template/images/icon_ribbon-tag.svg" alt="" /></li>
                  <li v-if="info_question.status_hot === 1"><img src="@/assets/template/images/icon_ribbon-tag-hot.svg" alt="" /></li>
                </ul>
              </div>
            </div>
            <h2 class="headDetail-title">
              <span class="headDetail-item"><img src="@/assets/template/images/icon_qa01.svg" alt="" /></span>
              {{ info_question.title }}
            </h2>
            <div class="headAcc">
              <div class="headAcc-info">
                <div style="cursor: pointer" class="headAcc-thumb" @click="goAccountSetting(info_question.create_user_cd)">
                  <img class="img-smooth" :src="info_question.file_url" alt="" />
                </div>
                <div class="headAcc-content">
                  <h3 class="headAcc-title">{{ info_question.user_name }}</h3>
                  <p class="headAcc-label">
                    <span class="headAcc-item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                    {{ info_question.org_label ? info_question.org_label : '(所属なし)' }}
                  </p>
                </div>
              </div>
              <div class="headAcc-btn">
                <p class="headAcc-date">
                  <span class="headAcc-date-item"><img src="@/assets/template/images/icon_clock-line.svg" alt="" /></span>
                  {{ getTimeInterval(info_question.last_update_datetime) }}
                </p>
                <span v-if="checkBtnInfoQuestionText === '問題報告' || checkBtnInfoQuestionText2 === '問題報告'">
                  <button
                    v-if="checkBtnInfoQuestion"
                    :disabled="checkLoading"
                    type="button"
                    class="btn btn-outline-primary btn-radius btn-outline-primary--cancel"
                    @click="clickQuestion(info_question, 'create')"
                  >
                    <span class="btn-iconLeft">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_report.svg')" alt="" />
                    </span>
                    問題報告
                  </button>
                </span>
                <span v-if="checkBtnInfoQuestionText === '問題報告あり'">
                  <button
                    v-if="checkBtnInfoQuestion"
                    :disabled="checkLoading"
                    type="button"
                    class="btn btn-outline-danger btn-radius btn-outline-danger--cancel"
                    @click="clickQuestion(info_question, 'detailQA')"
                  >
                    <span class="btn-iconLeft-2">
                      <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_report01.svg')" alt="" />
                    </span>
                    問題報告あり
                  </button>
                </span>
                <span v-if="checkBtnInfoQuestionText === '報告解除済'">
                  <button
                    v-if="checkBtnInfoQuestion"
                    :disabled="checkLoading"
                    type="button"
                    class="btn custom-outline btn-outline-danger btn-radius btn-outline-danger--cancel"
                    @click="clickQuestion(info_question, 'detailQA')"
                  >
                    <span class="btn-iconLeft-2">
                      <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_report01.svg')" alt="" />
                    </span>
                    報告解除済
                  </button>
                </span>
                <span v-if="checkBtnInfoQuestionText === '報告済み' || checkBtnInfoQuestionText2 === '報告済み'">
                  <button
                    v-if="checkBtnInfoQuestion"
                    :disabled="checkLoading"
                    type="button"
                    class="btn btn-outline-danger btn-radius btn-outline-danger--cancel"
                    @click="clickQuestion(info_question, 'edit')"
                  >
                    <span class="btn-iconLeft-2">
                      <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_report01.svg')" alt="" />
                    </span>
                    報告済み
                  </button>
                </span>
              </div>
            </div>
          </div>
          <div class="detailContent scrollbar">
            <div>
              <p class="detailContent-txt" style="white-space: break-spaces">
                {{ info_question.delete_flag === 1 ? '不適切な表現が含まれているため削除されました。' : info_question.contents }}
              </p>
            </div>
          </div>
          <div v-if="info_question.status_prohibited !== 1 && info_question.question_status_type_label === '回答受付中'" hidden class="answer">
            <label class="answer-label">回答 </label>
            <div class="answer-textarea">
              <el-input
                v-model="note"
                clearable
                class="form-control-textarea"
                :rows="10"
                type="textarea"
                placeholder="回答入力"
                @input="checkChangeNoteInput"
              />
            </div>
            <div class="answer-btn">
              <button :disabled="note === '' || checkLoading" type="button" class="btn btn-primary customBtn1" @click="registerAnswer">回答する</button>
            </div>
          </div>
          <div class="detailQa-btn">
            <button :disabled="checkLoading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel customBtn2" @click="closeE01S02">
              <i :class="['el-icon-loading', checkLoading ? 'inline-block' : '']"></i> キャンセル
            </button>

            <button
              v-if="checkisCreateUser || checkRoleQA"
              :disabled="checkLoading"
              type="button"
              class="btn btn-outline-primary customBtn3 btn-outline-primary--cancel"
              @click="comfirmDelete"
            >
              削除
            </button>
            <button
              v-if="checkIsregisterAndNotRegister"
              :disabled="checkLoading"
              type="button"
              class="btn btn-primary customBtn4"
              @click="openAnswer(info_question.question_status_type)"
            >
              {{ info_question.question_status_type_label === '回答受付中' ? '回答受付終了' : '回答受付再開' }}
            </button>
          </div>
        </div>
        <div v-if="checkloading" class="group-col2">
          <div class="boxAswers">
            <div class="qaHeade">
              <ul>
                <li class="title"><h2 class="tlt">回答</h2></li>
                <li>
                  <button
                    v-if="info_question.status_prohibited !== 1 && info_question.question_status_type_label === '回答受付中'"
                    type="button"
                    class="btn btn-link btn-link--custom"
                    @click="openModalAnswerRegistration()"
                  >
                    <span class="btn-iconLeft">
                      <ImageSVG :src-image="'icon_plus03.svg'" :alt-image="'icon_plus03'" />
                    </span>
                    回答登録
                  </button>
                </li>
              </ul>
            </div>
            <div v-if="list_best_answer.length > 0 || list_answer.length > 0" class="box-answer">
              <div ref="QAList" class="lstAswers scrollbar">
                <div v-if="list_best_answer.length > 0" class="bestAnswer" style="margin-bottom: 20px">
                  <span class="bestAnswer-item">
                    <img src="@/assets/template/images/icon_crown01.svg" alt="" />
                  </span>
                  ベストアンサー
                </div>
                <ul v-loading="false">
                  <li v-for="item of list_best_answer" :key="item">
                    <div class="lstAswers-head">
                      <div class="lstAswers-content">
                        <div style="cursor: pointer" class="lstAswers-thumb" @click="goAccountSetting(item.create_user_cd)">
                          <img :src="item.file_url" alt="" />
                        </div>
                        <div class="lstAswers-control">
                          <h3 class="tlt">{{ item.user_name }}</h3>
                          <ul>
                            <li>
                              <span class="item">
                                <img src="@/assets/template/images/icon_building03.svg" alt="" />
                              </span>
                              {{ item.org_label ? item.org_label : '(所属なし)' }}
                            </li>
                            <li>
                              <span class="item">
                                <img src="@/assets/template/images/icon_clock-line.svg" alt="" />
                              </span>
                              {{ getTimeInterval(item.last_update_datetime) }}
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="lstAswers-tools">
                        <div class="lstAswers-btn">
                          <span v-if="item.checkBtnInfoBestAnswerText === '報告解除済'">
                            <button
                              type="button"
                              :disabled="checkLoading"
                              class="btn custom-outline btn-outline-danger btn-radius"
                              @click="clickAnswer(item, 'detailQA')"
                            >
                              <span class="btn-iconLeft-2">
                                <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_report01.svg')" alt="" />
                              </span>
                              報告解除済
                            </button>
                          </span>
                          <span v-if="item.checkBtnInfoBestAnswerText === '問題報告あり'">
                            <button type="button" :disabled="checkLoading" class="btn btn-outline-danger btn-radius" @click="clickAnswer(item, 'detailQA')">
                              <span class="btn-iconLeft-2">
                                <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_report01.svg')" alt="" /> </span
                              >問題報告あり
                            </button>
                          </span>
                          <span v-if="item.checkBtnInfoBestAnswerTextThreeDotText === '問題報告' || item.checkBtnInfoBestAnswerTextThreeDotText === '報告済み'">
                            <button
                              :class="item.className && item.checkRoleQA && item.number_unsuitable_report !== 0 ? 'change-btn' : ''"
                              :disabled="checkLoading"
                              class="btn btn--icon"
                              data-toggle="dropdown"
                              aria-expanded="false"
                            >
                              <span></span>
                              <span></span>
                              <span></span>
                            </button>
                            <div
                              :class="item.className && item.checkRoleQA && item.number_unsuitable_report !== 0 ? 'change-btn2' : ''"
                              class="dropdown-menu dropdown-tools"
                            >
                              <span class="btn-show">
                                <span></span>
                                <span></span>
                                <span></span>
                              </span>
                              <ul>
                                <li
                                  v-if="item.checkBtnInfoBestAnswerTextThreeDotText === '問題報告'"
                                  @click="clickAnswer(item, 'create')"
                                  @touchstart="clickAnswer(item, 'create')"
                                >
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_report.svg')" alt="" />
                                  </span>
                                  <span class="text-label">問題報告</span>
                                </li>
                                <li
                                  v-if="item.checkBtnInfoBestAnswerTextThreeDotText === '報告済み'"
                                  @click="clickAnswer(item, 'edit')"
                                  @touchstart="clickAnswer(item, 'edit')"
                                >
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_report01.svg')" alt="" />
                                  </span>
                                  <span style="color: #ea5d54" class="text-label">報告済み</span>
                                </li>
                              </ul>
                            </div>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div :class="item.isReport ? 'changeBGR' : ''" class="lstAswers-info">
                      <p style="white-space: break-spaces" class="lstAswers-txt">{{ item.answer_note }}</p>
                      <p v-if="item.delete_flag === 1" class="lstAswers-txt lstAswers-txt-2">不適切な表現が含まれているため削除されました。</p>
                    </div>
                  </li>
                </ul>
                <h2 v-if="list_best_answer.length > 0" class="titleOther">他の回答</h2>
                <ul v-loading="false">
                  <li v-for="item of list_answer" :id="`className--${item.answer_id}`" :ref="`row-${item.answer_id}`" :key="item">
                    <div class="lstAswers-head">
                      <div class="lstAswers-content">
                        <div style="cursor: pointer" class="lstAswers-thumb" @click="goAccountSetting(item.create_user_cd)">
                          <img :src="item.file_url" alt="" />
                        </div>
                        <div class="lstAswers-control">
                          <h3 class="tlt">{{ item.user_name }}</h3>
                          <ul>
                            <li>
                              <span class="item">
                                <img src="@/assets/template/images/icon_building03.svg" alt="" />
                              </span>
                              {{ item.org_label ? item.org_label : '(所属なし)' }}
                            </li>
                            <li>
                              <span class="item">
                                <img src="@/assets/template/images/icon_clock-line.svg" alt="" />
                              </span>
                              {{ getTimeInterval(item.last_update_datetime) }}
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="lstAswers-tools">
                        <div class="lstAswers-btn">
                          <span v-if="item.checkBtnInfoAnswerText === '報告解除済'">
                            <button
                              type="button"
                              :disabled="checkLoading"
                              class="btn custom-outline btn-outline-danger btn-outline-danger--cancel btn-radius"
                              @click="clickAnswer(item, 'detailQA')"
                            >
                              <span class="btn-iconLeft-2">
                                <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_report01.svg')" alt="" />
                              </span>
                              報告解除済
                            </button>
                          </span>
                          <span v-if="item.checkBtnInfoAnswerText === '問題報告あり'">
                            <button
                              type="button"
                              :disabled="checkLoading"
                              class="btn btn-outline-danger btn-outline-danger--cancel btn-radius"
                              @click="clickAnswer(item, 'detailQA')"
                            >
                              <span class="btn-iconLeft-2">
                                <img v-svg-inline svg-inline :src="require('@/assets/template/images/icon_report01.svg')" alt="" /> </span
                              >問題報告あり
                            </button>
                          </span>
                          <span
                            v-if="
                              item.checkBtnInfoAnswerTextThreeDotText === '問題報告' ||
                              item.checkBtnInfoAnswerTextThreeDotText === '報告済み' ||
                              checkisCreateUser
                            "
                          >
                            <button
                              type="button"
                              :disabled="checkLoading"
                              :class="item.className && item.checkRoleQA && item.number_unsuitable_report !== 0 ? 'change-btn' : ''"
                              class="btn btn--icon"
                              data-toggle="dropdown"
                              aria-expanded="false"
                            >
                              <span></span>
                              <span></span>
                              <span></span>
                            </button>
                            <div
                              :class="item.className && item.checkRoleQA && item.number_unsuitable_report !== 0 ? 'change-btn2' : ''"
                              class="dropdown-menu dropdown-tools"
                            >
                              <span class="btn-show">
                                <span></span>
                                <span></span>
                                <span></span>
                              </span>
                              <ul>
                                <li v-if="checkisCreateUser">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_crown.svg')" alt="" />
                                  </span>
                                  <span
                                    class="text-label"
                                    @click="ComfirmRegisterBestAnswer(item.answer_id)"
                                    @touchstart="ComfirmRegisterBestAnswer(item.answer_id)"
                                    >ベストアンサーにする</span
                                  >
                                </li>
                                <li
                                  v-if="item.checkBtnInfoAnswerTextThreeDotText === '問題報告'"
                                  @click="clickAnswer(item, 'create')"
                                  @touchstart="clickAnswer(item, 'create')"
                                >
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_report.svg')" alt="" />
                                  </span>
                                  <span class="text-label">問題報告</span>
                                </li>
                                <li
                                  v-if="item.checkBtnInfoAnswerTextThreeDotText === '報告済み'"
                                  @click="clickAnswer(item, 'edit')"
                                  @touchstart="clickAnswer(item, 'edit')"
                                >
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_report01.svg')" alt="" />
                                  </span>
                                  <span style="color: #ea5d54" class="text-label">報告済み</span>
                                </li>
                              </ul>
                            </div>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div :class="item.isReport ? 'changeBGR' : ''" class="lstAswers-info">
                      <p style="white-space: break-spaces" class="lstAswers-txt">{{ item.answer_note }}</p>
                      <p v-if="item.delete_flag === 1" class="lstAswers-txt lstAswers-txt-2">不適切な表現が含まれているため削除されました。</p>
                    </div>
                  </li>
                </ul>
              </div>
              <div v-if="pageSizelistData > 0" class="paginationAswers">
                <div class="pagination-cases">全 {{ +pageSizelistData + +list_best_answer.length }} 件</div>
                <PaginationTable
                  :page-size="100"
                  layout="prev, pager, next"
                  :total="pageSizelistData"
                  :current-page="currentPage"
                  @current-change="handleCurrentChange"
                />
              </div>
            </div>
            <div v-if="list_best_answer.length === 0 && list_answer.length === 0">
              <div class="noData noData-2">
                <div class="noData-content">
                  <p v-if="!loadingInfoBestAnswer && !loadingInfoAnswer" class="noData-text">該当するデータがありません。</p>
                  <div v-if="!loadingInfoBestAnswer && !loadingInfoAnswer" class="noData-thumb">
                    <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- delete question -->
    <el-dialog
      v-model="modalConfigDelete.isShowModal"
      :custom-class="modalConfigDelete.customClass"
      :title="modalConfigDelete.title"
      :width="modalConfigDelete.width"
      :destroy-on-close="modalConfigDelete.destroyOnClose"
      :close-on-click-modal="modalConfigDelete.closeOnClickMark"
      :show-close="modalConfigDelete.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfigDelete.component"
          v-bind="{ ...modalConfigDelete.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="deleteQuestion"
          @handleYes="handleYes"
        ></component>
      </template>
    </el-dialog>
    <!-- dang ky best answer -->
    <el-dialog
      v-model="modalConfigComfirmRegisterBestAnswer.isShowModal"
      custom-class="custom-dialog"
      :title="modalConfigComfirmRegisterBestAnswer.title"
      :width="modalConfigComfirmRegisterBestAnswer.width"
      :destroy-on-close="modalConfigComfirmRegisterBestAnswer.destroyOnClose"
      :close-on-click-modal="modalConfigComfirmRegisterBestAnswer.closeOnClickMark"
      :before-close="closeModalI"
      :show-close="modalConfigComfirmRegisterBestAnswer.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfigComfirmRegisterBestAnswer.component"
          v-bind="{ ...modalConfigComfirmRegisterBestAnswer.props }"
          @registerBestAnswerSubmit="registerBestAnswerSubmit"
          @onFinishScreen="closeModalI"
        ></component>
      </template>
    </el-dialog>
    <!-- E01_S02_QaDetails_ReportAdministrator_Modal -->
    <el-dialog
      v-model="modalConfigReportAdministrator.isShowModal"
      custom-class="custom-dialog handle-close"
      :title="modalConfigReportAdministrator.title"
      :width="modalConfigReportAdministrator.width"
      :destroy-on-close="modalConfigReportAdministrator.destroyOnClose"
      :close-on-click-modal="modalConfigReportAdministrator.closeOnClickMark"
      :before-close="handleBeforeCloseReport"
    >
      <template #default>
        <component
          :is="modalConfigReportAdministrator.component"
          ref="modalChildReport"
          v-bind="{ ...modalConfigReportAdministrator.props }"
          @onFinishScreenReport="onFinishScreenReport"
          @onFinishResultReport="onFinishResultReport"
        ></component>
      </template>
    </el-dialog>
    <!-- E01_S02_QaDetails_ReportAdministrator_Modal01 -->
    <el-dialog
      v-model="modalConfigReportAdministrator01.isShowModal"
      custom-class="custom-dialog handle-close modal-fixed"
      :title="modalConfigReportAdministrator01.title"
      :width="modalConfigReportAdministrator01.width"
      :destroy-on-close="modalConfigReportAdministrator01.destroyOnClose"
      :close-on-click-modal="modalConfigReportAdministrator01.closeOnClickMark"
      :before-close="handleBeforeCloseReport01"
    >
      <template #default>
        <component
          :is="modalConfigReportAdministrator01.component"
          ref="modalChildReport01"
          v-bind="{ ...modalConfigReportAdministrator01.props }"
          @onFinishScreen01="onFinishScreen01"
          @onFinishResult01="onFinishResult01"
        ></component>
      </template>
    </el-dialog>
    <!-- E01_S02_QaDetails_ReportAdministrator_Modal02 -->
    <el-dialog
      v-model="modalConfigReportAdministrator02.isShowModal"
      custom-class="custom-dialog handle-close modal-fixed"
      :title="modalConfigReportAdministrator02.title"
      :width="modalConfigReportAdministrator02.width"
      :destroy-on-close="modalConfigReportAdministrator02.destroyOnClose"
      :close-on-click-modal="modalConfigReportAdministrator02.closeOnClickMark"
      :before-close="handleBeforeCloseReport02"
      @close="closeModalAdmin02"
    >
      <template #default>
        <component
          :is="modalConfigReportAdministrator02.component"
          ref="modalChildReport02"
          v-bind="{ ...modalConfigReportAdministrator02.props }"
          @onFinishScreen02="onFinishScreen02"
          @onFinishResult02="onFinishResult02"
        ></component>
      </template>
    </el-dialog>
    <!-- E01_S02_QaDetails_ReportAdministrator_Modal03 -->
    <el-dialog
      v-model="modalConfigReportAdministrator03.isShowModal"
      custom-class="custom-dialog handle-close modal-fixed"
      :title="modalConfigReportAdministrator03.title"
      :width="modalConfigReportAdministrator03.width"
      :destroy-on-close="modalConfigReportAdministrator03.destroyOnClose"
      :close-on-click-modal="modalConfigReportAdministrator03.closeOnClickMark"
      :before-close="handleBeforeCloseReport03"
      @close="closeModalAdmin03"
    >
      <template #default>
        <component
          :is="modalConfigReportAdministrator03.component"
          ref="modalChildReport03"
          v-bind="{ ...modalConfigReportAdministrator03.props }"
          @onFinishScreen03="onFinishScreen03"
          @onFinishResult03="onFinishResult03"
        ></component>
      </template>
    </el-dialog>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass + ' handle-close'"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :show-close="modalConfig.isShowClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component :is="modalConfig.component" ref="modalChild" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModalAnsRegis"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import E01_S02_QaDetailsServices from '../../../api/E01/E01_S02_QaDetailsServices';
import { markRaw } from 'vue';
import PaginationTable from '@/components/PaginationTable';
import E01S02QaDetailsAnswerConfirmModal from '../E01_S02_QaDetails/E01_S02_QaDetails_AnswerConfirm_Modal.vue';
import E01S02QaDetailsReportAdministratorModal from '../E01_S02_QaDetails/E01_S02_QaDetails_ReportAdministrator_Modal.vue';
import E01S02QaDetailsReportAdministratorModal01 from '../E01_S02_QaDetails/E01_S02_QaDetails_reportAdministrator01_Modal.vue';
import E01S02QaDetailsReportAdministratorModal02 from '../E01_S02_QaDetails/E01_S02_QaDetails_ReportAdministrator02_Modal.vue';
import E01S02QaDetailsReportAdministratorModal03 from '../E01_S02_QaDetails/E01_S02_QaDetails_ReportAdministrator03_Modal.vue';
import E01_S02_QaDetails_AnswerRegistration_ModalVue from './E01_S02_QaDetails_AnswerRegistration_Modal.vue';
import { Auth } from '@/api';
export default {
  name: 'E01_S02_QaDetails',
  components: {
    E01S02QaDetailsAnswerConfirmModal,
    E01S02QaDetailsReportAdministratorModal,
    E01S02QaDetailsReportAdministratorModal01,
    E01S02QaDetailsReportAdministratorModal02,
    E01S02QaDetailsReportAdministratorModal03,
    PaginationTable
  },
  props: {},
  data() {
    return {
      pageSizelistData: 0,
      currentPage: 1,
      total_pages: 0,
      question_id: '',
      list_answer: [],
      list_best_answer: [],
      info_question: {},
      checkLoading: false,
      loadingInfoQuestion: false,
      loadingInfoBestAnswer: false,
      loadingInfoAnswer: false,
      isReload: false,
      checkisCreateUser: false,
      checkIsregisterAndNotRegister: false,
      note: '',
      answerId: '',
      checkBtnInfoQuestion: false,
      checkBtnInfoQuestionText: '',
      checkBtnInfoQuestionaction: '',
      checkBtnInfoQuestionText2: '',
      checkBtnInfoQuestionaction2: '',
      checkRoleQA: false,
      currentUser: '',
      checkloading: false,
      dataInfoAnswer: [],
      modalConfigDelete: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      },
      modalConfigComfirmRegisterBestAnswer: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      },
      modalConfigReportAdministrator: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      modalConfigReportAdministrator01: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      modalConfigReportAdministrator02: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      modalConfigReportAdministrator03: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },

      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        isShowClose: false,
        closeOnClickMark: false
      },
      permission: ''
    };
  },
  unmounted() {
    localStorage.removeItem('checkChangTab');
  },
  async mounted() {
    setTimeout(() => {
      this.checkloading = true;
    }, 1000);
    this.emitter.emit('change-header', {
      title: 'QA詳細',
      icon: 'E01-Qa.svg',
      isShowBack: true
    });
    this.currentUser = this.getCurrentUser()?.user_cd;
    let user_cd = this.currentUser;
    this.permission = await Auth.getPolicyRoleService({ user_cd });
    this.question_id = this._route('E01_S02_QaDetails')?.params?.question_id;
    this.getInfoQuestion();
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    handleBeforeCloseReport(done) {
      try {
        this.$refs.modalChildReport?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    handleBeforeCloseReport01(done) {
      try {
        this.$refs.modalChildReport01?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    handleBeforeCloseReport02(done) {
      try {
        this.$refs.modalChildReport02?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    handleBeforeCloseReport03(done) {
      try {
        this.$refs.modalChildReport03?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    checkChangeNoteInput() {
      if (this.note === '') {
        localStorage.removeItem('checkChangTab');
      } else {
        localStorage.setItem('checkChangTab', true);
      }
    },
    closeE01S02() {
      if (this.note === '') {
        this.back();
      } else {
        localStorage.setItem('checkChangTab', true);
        this.modalConfigDelete = {
          ...this.modalConfig,
          isShowModal: true,
          title: null,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false,
          customClass: 'custom-dialog z05-s01'
        };
      }
    },
    handleYes() {
      this.back();
    },
    getInfoQuestion() {
      this.loadingInfoQuestion = true;
      E01_S02_QaDetailsServices.infomationQuestion(this.question_id)
        .then((res) => {
          this.checkConditionQuestion(res.data.data);
          this.getInfoAnswer();
          this.getBestAnswer();
          this.$router.replace({ params: {} });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
          if (err.response.data.message === '該当するQAが存在しません。') {
            this.$router.push({ name: 'E01_S01_QaSearch' });
          }
          this.loadingInfoQuestion = false;
        })
        .finally(() => {});
    },
    checkConditionQuestion(res) {
      if (this.currentUser === res.create_user_cd) {
        this.checkBtnInfoQuestion = false;
        this.checkisCreateUser = true;
        this.checkIsregisterAndNotRegister = true;
      } else {
        this.checkBtnInfoQuestion = true;
        const createUserCdUnsuitableReport = res.create_user_cd_unsuitable_report ? res.create_user_cd_unsuitable_report.split(',') : [];
        if (createUserCdUnsuitableReport.includes(this.currentUser)) {
          this.checkBtnInfoQuestionText = '報告済み';
          this.checkBtnInfoQuestionaction = 'edit';
          const policyRole = this.$store.state.auth.policyRole ? this.$store.state.auth.policyRole : this.permission;
          policyRole.forEach((element) => {
            if (element === 'R50') {
              this.checkRoleQA = true;
            }
          });
          if (this.checkRoleQA) {
            if (res.number_unsuitable_report !== 0) {
              if (res.delete_flag === 1) {
                this.checkBtnInfoQuestionText = '問題報告あり';
                this.checkBtnInfoQuestionaction = 'detailQA';
              } else if (res.delete_flag === 0 && res.cancel_flag === 1) {
                this.checkBtnInfoQuestionText = '報告解除済';
                this.checkBtnInfoQuestion = true;
              } else if (res.delete_flag === 0 && res.cancel_flag === 0) {
                this.checkBtnInfoQuestionText = '問題報告あり';
                this.checkBtnInfoQuestionaction = 'detailQA';
              }
            } else {
              this.checkBtnInfoQuestion = false;
            }
            const createUserCdUnsuitableReport = res.create_user_cd_unsuitable_report ? res.create_user_cd_unsuitable_report.split(',') : [];
            if (createUserCdUnsuitableReport.includes(this.currentUser)) {
              this.checkBtnInfoQuestionText2 = '報告済み';
              this.checkBtnInfoQuestionaction2 = 'edit';
            } else {
              this.checkBtnInfoQuestionText2 = '問題報告';
              this.checkBtnInfoQuestionaction2 = 'create';
            }
          }
        } else {
          const policyRole = this.$store.state.auth.policyRole ? this.$store.state.auth.policyRole : this.permission;
          policyRole.forEach((element) => {
            if (element === 'R50') {
              this.checkRoleQA = true;
            }
          });
          if (this.checkRoleQA) {
            if (res.number_unsuitable_report !== 0) {
              if (res.delete_flag === 1) {
                this.checkBtnInfoQuestionText = '問題報告あり';
                this.checkBtnInfoQuestionaction = 'detailQA';
              } else if (res.delete_flag === 0 && res.cancel_flag === 1) {
                this.checkBtnInfoQuestionText = '報告解除済';
                this.checkBtnInfoQuestion = true;
              } else if (res.delete_flag === 0 && res.cancel_flag === 0) {
                this.checkBtnInfoQuestionText = '問題報告あり';
                this.checkBtnInfoQuestionaction = 'detailQA';
              }
            } else {
              const createUserCdUnsuitableReport = res.create_user_cd_unsuitable_report ? res.create_user_cd_unsuitable_report.split(',') : [];
              if (createUserCdUnsuitableReport.includes(this.currentUser)) {
                this.checkBtnInfoQuestionText2 = '報告済み';
                this.checkBtnInfoQuestionaction2 = 'edit';
              } else {
                this.checkBtnInfoQuestionText2 = '問題報告';
                this.checkBtnInfoQuestionaction2 = 'create';
              }
            }
          } else {
            const policyRole = this.$store.state.auth.policyRole ? this.$store.state.auth.policyRole : this.permission;
            policyRole.forEach((element) => {
              if (element === 'R50') {
                this.checkRoleQA = true;
              }
            });
            this.checkBtnInfoQuestionText = '問題報告';
            this.checkBtnInfoQuestionaction = 'create';
          }
        }
      }

      this.info_question = res;

      if (this.info_question?.file_url) {
        fetch(this.info_question.file_url).then((response) => {
          // eslint-disable-next-line eqeqeq
          if (!response.ok || response.status != 200) {
            this.info_question.file_url = this.avataDefault();
          }
        });
      } else {
        this.info_question.file_url = this.avataDefault();
      }
    },
    clickQuestion(item, IisModal) {
      if (IisModal === 'detailQA') {
        this.ReportAdministrator02(item, IisModal);
      }
      if (IisModal === 'create' || IisModal === 'edit') {
        this.ReportAdministrator(item, IisModal);
      }
    },
    clickAnswer(item, isModal) {
      if (isModal === 'detailQA') {
        this.ReportAdministrator03(item, isModal);
      }
      if (isModal === 'create' || isModal === 'edit') {
        this.ReportAdministrator01(item, isModal);
      }
    },
    onFinishScreen01(e) {
      if (e === 0) {
        this.modalConfigReportAdministrator01 = { ...this.modalConfigReportAdministrator01, isShowModal: false };
      } else {
        this.getInfoQuestion();
        this.modalConfigReportAdministrator01 = { ...this.modalConfigReportAdministrator01, isShowModal: false };
      }
    },
    closeModalAdmin02() {
      this.getInfoQuestion();
    },
    closeModalAdmin03() {
      this.getInfoQuestion();
    },
    onFinishScreen02(e) {
      if (e === 0) {
        this.modalConfigReportAdministrator02 = { ...this.modalConfigReportAdministrator02, isShowModal: false };
      } else {
        this.getInfoQuestion();
        this.modalConfigReportAdministrator02 = { ...this.modalConfigReportAdministrator02, isShowModal: false };
      }
    },
    onFinishScreen03(e) {
      if (e === 0) {
        this.modalConfigReportAdministrator03 = { ...this.modalConfigReportAdministrator03, isShowModal: false };
      } else {
        this.getInfoQuestion();
        this.modalConfigReportAdministrator03 = { ...this.modalConfigReportAdministrator03, isShowModal: false };
      }
    },
    onFinishScreenReport(e) {
      if (e === 0) {
        this.modalConfigReportAdministrator = { ...this.modalConfigReportAdministrator, isShowModal: false };
      } else {
        this.getInfoQuestion();
        this.modalConfigReportAdministrator = { ...this.modalConfigReportAdministrator, isShowModal: false };
      }
    },
    handleCurrentChange(val) {
      if (this.$refs.QAList) {
        this.$refs.QAList.scrollTop = 0;
      }
      if (this.total_pages <= this.currentPage) {
        this.currentPage = val;
        this.getInfoAnswer();
      } else {
        this.currentPage = val;
        this.getInfoAnswer();
      }
    },
    getBestAnswer() {
      this.loadingInfoBestAnswer = true;
      E01_S02_QaDetailsServices.InfoAnswer(this.question_id)
        .then((res) => {
          this.checkConditionBestAnswer(res.data.data.list_best_answer);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(() => {
          this.loadingInfoBestAnswer = false;
        });
    },
    async checkConditionBestAnswer(data) {
      let policyRole = this.$store.state.auth.policyRole ? this.$store.state.auth.policyRole : this.permission;
      data.forEach((element) => {
        if (this.currentUser === element.create_user_cd) {
          element.checkBtnInfoBestAnswer = false;
        } else {
          element.checkBtnInfoBestAnswer = true;
          const createUserCdUnsuitableReport = element.create_user_cd_unsuitable_report ? element.create_user_cd_unsuitable_report.split(',') : [];
          if (createUserCdUnsuitableReport.includes(this.currentUser)) {
            element.isReport = true;
            element.checkBtnInfoBestAnswerTextThreeDotText = '報告済み';
            element.checkBtnInfoBestAnswerTextThreeDotAction = 'edit';
            element.className = true;

            policyRole.forEach((element2) => {
              if (element2 === 'R50') {
                element.checkRoleQA = true;
              }
            });

            if (element.checkRoleQA) {
              if (element.number_unsuitable_report !== 0) {
                if (element.delete_flag === 1) {
                  element.checkBtnInfoBestAnswerText = '問題報告あり';
                  element.checkBtnInfoBestAnsweraction = 'detailQA';
                } else if (element.delete_flag === 0 && element.cancel_flag === 1) {
                  element.checkBtnInfoBestAnswer = true;
                  element.checkBtnInfoBestAnswerText = '報告解除済';
                } else if (element.delete_flag === 0 && element.cancel_flag === 0) {
                  element.checkBtnInfoBestAnswerText = '問題報告あり';
                  element.checkBtnInfoBestAnsweraction = 'detailQA';
                }
              } else {
                element.checkBtnInfoBestAnswer = false;
              }
            }
          } else {
            policyRole.forEach((element2) => {
              if (element2 === 'R50') {
                element.checkRoleQA = true;
              }
            });

            if (element.checkRoleQA) {
              const createUserCdUnsuitableReport = element.create_user_cd_unsuitable_report ? element.create_user_cd_unsuitable_report.split(',') : [];
              if (createUserCdUnsuitableReport.includes(this.currentUser)) {
                element.className = true;
                element.isReport = true;
                element.checkBtnInfoBestAnswerTextThreeDotText = '報告済み';
                element.checkBtnInfoBestAnswerTextThreeDotAction = 'edit';
              } else {
                element.className = true;
                element.checkBtnInfoBestAnswerTextThreeDotText = '問題報告';
                element.checkBtnInfoBestAnswerTextThreeDotAction = 'create';
              }
              if (element.number_unsuitable_report !== 0) {
                if (element.delete_flag === 1) {
                  element.checkBtnInfoBestAnswerText = '問題報告あり';
                  element.checkBtnInfoBestAnsweraction = 'detailQA';
                } else if (element.delete_flag === 0 && element.cancel_flag === 1) {
                  element.checkBtnInfoBestAnswer = true;
                  element.checkBtnInfoBestAnswerText = '報告解除済';
                } else if (element.delete_flag === 0 && element.cancel_flag === 0) {
                  element.checkBtnInfoBestAnswerText = '問題報告あり';
                  element.checkBtnInfoBestAnsweraction = 'detailQA';
                }
              } else {
                element.checkBtnInfoBestAnswer = false;
              }
            } else {
              element.checkBtnInfoBestAnswerTextThreeDotText = '問題報告';
              element.checkBtnInfoBestAnswerTextThreeDotAction = 'create';
              element.className = true;
            }
          }
        }
      });
      this.list_best_answer = data;

      let listImg = data.map((item) =>
        fetch(item.file_url).then((resp) => {
          // eslint-disable-next-line eqeqeq
          if (resp.ok && resp.status == 200) {
            return {
              ...item,
              file_url: item.file_url || this.avataDefault(),
              res: { ...resp }
            };
          } else {
            return {
              ...item,
              file_url: this.avataDefault(),
              res: { ...resp }
            };
          }
        })
      );
      await Promise.all(listImg).then(async (res) => {
        let data = await res;
        this.list_best_answer = data;
      });
      this.js_pscroll(0.4);
    },
    getInfoAnswer(isRegister) {
      this.loadingInfoAnswer = true;
      const data = {
        offset: isRegister ? 0 : this.currentPage === 0 ? this.currentPage : this.currentPage - 1,
        limit: 100
      };
      E01_S02_QaDetailsServices.InfoAnswer(this.question_id, data)
        .then((res) => {
          this.pageSizelistData = res.data.data.list_answer.pagination.total_items;
          this.dataInfoAnswer = res.data.data.list_answer.records;
          this.checkConditionAnswer(res.data.data.list_answer.records, isRegister);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(() => {
          this.loadingInfoAnswer = false;
          this.loadingInfoQuestion = false;
        });
    },
    async checkConditionAnswer(data, isRegister) {
      let policyRole = this.$store.state.auth.policyRole ? this.$store.state.auth.policyRole : this.permission;
      data.forEach((element) => {
        if (this.currentUser === element.create_user_cd) {
          element.checkBtnInfoAnswer = false;
        } else {
          element.checkBtnInfoQuestion = true;
          const createUserCdUnsuitableReport = element.create_user_cd_unsuitable_report ? element.create_user_cd_unsuitable_report.split(',') : [];
          if (createUserCdUnsuitableReport.includes(this.currentUser)) {
            element.isReport = true;
            element.checkBtnInfoAnswerTextThreeDotText = '報告済み';
            element.checkBtnInfoAnswerTextThreeDotAction = 'edit';
            element.className = true;

            policyRole.forEach((element2) => {
              if (element2 === 'R50') {
                element.checkRoleQA = true;
              }
            });

            if (element.checkRoleQA) {
              if (element.number_unsuitable_report !== 0) {
                if (element.delete_flag === 1) {
                  element.checkBtnInfoAnswerText = '問題報告あり';
                  element.checkBtnInfoAnsweraction = 'detailQA';
                } else if (element.delete_flag === 0 && element.cancel_flag === 1) {
                  element.checkBtnInfoAnswer = true;
                  element.checkBtnInfoAnswerText = '報告解除済';
                } else if (element.delete_flag === 0 && element.cancel_flag === 0) {
                  element.checkBtnInfoAnswerText = '問題報告あり';
                  element.checkBtnInfoAnsweraction = 'detailQA';
                }
              } else {
                element.checkBtnInfoAnswer = false;
              }
            }
          } else {
            policyRole.forEach((element2) => {
              if (element2 === 'R50') {
                element.checkRoleQA = true;
              }
            });
            if (element.checkRoleQA) {
              const createUserCdUnsuitableReport = element.create_user_cd_unsuitable_report ? element.create_user_cd_unsuitable_report.split(',') : [];
              if (createUserCdUnsuitableReport.includes(this.currentUser)) {
                element.isReport = true;
                element.checkBtnInfoAnswerTextThreeDotText = '報告済み';
                element.checkBtnInfoAnswerTextThreeDotAction = 'edit';
                element.className = true;
              } else {
                element.checkBtnInfoAnswerTextThreeDotText = '問題報告';
                element.checkBtnInfoAnswerTextThreeDotAction = 'create';
                element.className = true;
              }
              if (element.number_unsuitable_report !== 0) {
                if (element.delete_flag === 1) {
                  element.checkBtnInfoAnswerText = '問題報告あり';
                  element.checkBtnInfoAnsweraction = 'detailQA';
                } else if (element.delete_flag === 0 && element.cancel_flag === 1) {
                  element.checkBtnInfoAnswer = true;
                  element.checkBtnInfoAnswerText = '報告解除済';
                } else if (element.delete_flag === 0 && element.cancel_flag === 0) {
                  element.checkBtnInfoAnswerText = '問題報告あり';
                  element.checkBtnInfoAnsweraction = 'detailQA';
                }
              } else {
                element.checkBtnInfoAnswer = false;
              }
            } else {
              element.checkBtnInfoAnswerTextThreeDotText = '問題報告';
              element.checkBtnInfoAnswerTextThreeDotAction = 'create';
              element.className = true;
            }
          }
        }
      });
      this.list_answer = data;

      let listImg = data.map((item) =>
        fetch(item.file_url).then((resp) => {
          // eslint-disable-next-line eqeqeq
          if (resp.ok && resp.status == 200) {
            return {
              ...item,
              file_url: item.file_url || this.avataDefault(),
              res: { ...resp }
            };
          } else {
            return {
              ...item,
              file_url: this.avataDefault(),
              res: { ...resp }
            };
          }
        })
      );
      await Promise.all(listImg).then(async (res) => {
        let data = await res;
        this.list_answer = data;
      });
      this.js_pscroll(0.4);
      if (isRegister) {
        this.$refs.QAList.scrollTop = 0;
        this.getClassChangeBRG(this.dataInfoAnswer[0]?.answer_id, true, true);
        this.dataInfoAnswer = [];
        this.currentPage = 1;
      }
    },
    registerAnswer() {
      this.checkLoading = true;
      const data = {
        question_id: this.question_id,
        note: this.note
      };
      E01_S02_QaDetailsServices.registerAnswerForQuestion(data)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
          this.note = '';
          this.getInfoQuestion();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(async () => {
          this.checkLoading = false;
        });
    },
    comfirmDelete() {
      this.modalConfigDelete = {
        ...this.modalConfigDelete,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete',
        props: { title: 'このQAを完全に削除しますか？' }
      };
    },
    deleteQuestion() {
      this.checkLoading = true;
      const data = {
        delete_physics: 1
      };
      E01_S02_QaDetailsServices.deleteQuestion(this.question_id, data)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
          this.$router.go(-1);
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
        })
        .finally(async () => {
          this.checkLoading = false;
        });
    },
    closeModalI() {
      this.modalConfigComfirmRegisterBestAnswer = { ...this.modalConfigComfirmRegisterBestAnswer, isShowModal: false };
    },
    onResultModal() {
      this.modalConfigDelete = { ...this.modalConfigDelete, isShowModal: false };
    },
    openAnswer(item) {
      this.checkLoading = true;
      if (item === '10') {
        E01_S02_QaDetailsServices.stopAcceptAnswer(this.question_id)
          .then((res) => {
            this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
            this.getInfoQuestion();
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
          })
          .finally(async () => {
            this.checkLoading = false;
          });
      } else {
        E01_S02_QaDetailsServices.openAcceptAnswer(this.question_id)
          .then((res) => {
            this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
            this.getInfoQuestion();
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
          })
          .finally(async () => {
            this.checkLoading = false;
          });
      }
    },
    goAccountSetting(item) {
      this.$router.push({ name: 'Z04_S01_AccountSettings', params: { user_cd: item } });
    },
    ComfirmRegisterBestAnswer(item) {
      const contentObj = {
        header: 'この回答をベストアンサーにしますか？',
        content: 'ベストアンサーは取り消せません。'
      };
      this.modalConfigComfirmRegisterBestAnswer = {
        ...this.modalConfigComfirmRegisterBestAnswer,
        title: null,
        isShowModal: true,
        component: markRaw(E01S02QaDetailsAnswerConfirmModal),
        width: '550px',
        destroyOnClose: true,
        props: { contentObj: contentObj }
      };
      this.answerId = item;
    },
    registerBestAnswerSubmit() {
      E01_S02_QaDetailsServices.registerBestAnswer(this.answerId)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
          this.answerId = '';
          this.getInfoQuestion();
          this.modalConfigComfirmRegisterBestAnswer = { ...this.modalConfigComfirmRegisterBestAnswer, isShowModal: false };
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
          this.modalConfigComfirmRegisterBestAnswer = { ...this.modalConfigComfirmRegisterBestAnswer, isShowModal: false };
        });
    },
    ReportAdministrator(item, IisModal) {
      this.modalConfigReportAdministrator = {
        ...this.modalConfigReportAdministrator,
        title: '問題報告',
        isShowModal: true,
        component: markRaw(E01S02QaDetailsReportAdministratorModal),
        width: '60rem',
        props: { object: item, isModal: IisModal, questionId: this.question_id },
        destroyOnClose: true
      };
    },
    ReportAdministrator01(item, IisModal) {
      this.modalConfigReportAdministrator01 = {
        ...this.modalConfigReportAdministrator01,
        title: '問題報告',
        isShowModal: true,
        component: markRaw(E01S02QaDetailsReportAdministratorModal01),
        width: '45rem',
        destroyOnClose: true,
        props: { object: item, isModal: IisModal }
      };
    },
    ReportAdministrator02(item) {
      this.modalConfigReportAdministrator02 = {
        ...this.modalConfigReportAdministrator02,
        title: '問題報告',
        isShowModal: true,
        component: markRaw(E01S02QaDetailsReportAdministratorModal02),
        width: '45rem',
        props: { questionId: this.question_id, object: item },
        destroyOnClose: true
      };
    },
    ReportAdministrator03(item) {
      this.modalConfigReportAdministrator03 = {
        ...this.modalConfigReportAdministrator03,
        title: '問題報告',
        isShowModal: true,
        component: markRaw(E01S02QaDetailsReportAdministratorModal03),
        width: '45rem',
        props: { questionId: this.question_id, object: item },
        destroyOnClose: true
      };
    },

    openModalAnswerRegistration() {
      this.isReload = false;
      this.modalConfig = {
        ...this.modalConfig,
        title: '回答',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        isShowModal: true,
        isShowClose: true,
        component: markRaw(E01_S02_QaDetails_AnswerRegistration_ModalVue),
        width: '35rem',
        props: {
          question_id: this.question_id
        }
      };
    },

    onResultModalAnsRegis(data) {
      if (data.isReload) {
        this.isReload = true;
        this.getInfoAnswer(true);
      }
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: false,
        isShowClose: false,
        customClass: 'custom-dialog'
      };
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';

.change-btn {
  position: relative;
  left: 93px;
  top: 5px;
}
.change-btn2 {
  top: 46px !important;
  margin-right: 2px !important;
}
.changeBGR {
  background: #feddde;
}
.custom-outline {
  background: #fff;
  cursor: default !important;
  width: 136px;
}
.customBtn1 {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 24%;
    top: 29%;
  }
}
.customBtn2 {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 9%;
    top: 29%;
  }
}
.customBtn3 {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 26%;
    top: 29%;
  }
}
.customBtn4 {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 5%;
    top: 33%;
  }
}
.el-icon-loading {
  display: none;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.btn-iconLeft-2 {
  margin: 0 6px 0 0;
  position: relative;
  top: -2px;
  height: 24px;
  line-height: 24px;
  overflow: hidden;
}
.headAcc-btn {
  button {
    margin-right: 5px;
  }
}
.btn-outline-danger--cancel {
  .btn-iconLeft-2 {
    color: #ea5d54 !important;
    .svg {
      fill: #ea5d54;
    }
  }
}
.btn.btn-outline-danger:hover {
  background: unset !important;
  color: #ea5d54 !important;
  outline: 2px solid #ea5d5494;
  border: 1px solid #ea5d5494;
  .svg {
    fill: #ea5d54;
  }
}
.qaHeade {
  margin: -20px 0 0 -20px;
  ul {
    white-space: nowrap;
    padding-bottom: 20px;
    display: flex;
    justify-content: space-between;
    li {
      display: inline-block;
      margin-right: 20px;
      @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
        margin-right: 10px;
      }
      @media (max-width: $viewport_tablet) {
        margin-right: 15px;
        &.title {
          max-width: 180px;
        }
        .btn {
          padding: 0 10px !important;
          @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
            padding: 0 !important;
          }
          span.btn-iconLeft {
            margin-right: 3px !important;
          }
        }
      }
      &.title {
        width: 140px;
        .tlt {
          background: #eef6ff;
          box-shadow: 5px 5px 8px 3px rgba(145, 161, 171, 0.4);
          border-radius: 20px 0px 20px 0px;
          padding: 14px;
          display: block;
          font-size: 1.125rem;
          font-weight: 700;
          text-align: center;
        }
      }
      .btn {
        padding: 0 25px;
        border-radius: 0px 0px 8px 8px;
        @media only screen and (max-width: $viewport_desktop) and (min-width: $viewport_tablet) {
          padding: 0 12px;
        }
      }

      @media (hover: hover) {
        .btn-link--custom:hover {
          background: #007bff;
          color: #ffffff;
          .btn-icon--custom {
            stroke: #ffffff;
          }
          svg.svg,
          svg.svg path {
            fill: #ffffff !important;
          }
        }
      }
    }
  }
}

.box-answer {
  height: calc(100% - 105px);
}
</style>
