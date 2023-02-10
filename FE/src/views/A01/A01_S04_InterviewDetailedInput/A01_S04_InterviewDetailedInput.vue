<template>
  <div v-loading="isLoading" class="wrapContent">
    <div class="groupInter InterviewDetailedInput">
      <div class="title-page">
        <div class="info-facility" style="display: flex; align-items: center">
          <ImageSVG :src-image="'icon_medical-hospital.svg'" :alt-image="'icon_medical-hospital'" />
          <span style="cursor: pointer" class="link" @click="facilityDetail(interviewDateTime.facility_cd)">{{ interviewDateTime.facility_short_name }}</span>
        </div>
        <div class="head-info">
          <h2 class="title">
            <ImageSVG :src-image="'fa-solid_user-md.svg'" :alt-image="'icon-doctor'" />
            <span
              :class="lstMedicalStaff.some((x) => x.medical_staff_cd === interviewDateTime.person_cd) ? 'tlt-no' : 'link log-icon'"
              title_log="個人名"
              @click="
                lstMedicalStaff.some((x) => x.medical_staff_cd === interviewDateTime.person_cd)
                  ? ''
                  : callScreen('H02_S02', { person_cd: interviewDateTime.person_cd })
              "
            >
              {{ interviewDateTime.person_name }}
            </span>
          </h2>
          <div class="info-date">{{ interviewDateTime.start_date && formatFullDateCustom(interviewDateTime.start_date) }}</div>
          <div v-if="interviewDateTime.start_time && interviewDateTime.end_time" class="info-time">
            <span>{{ formatTimeHourMinute(interviewDateTime.start_time) }}</span>
            <span>～</span>
            <span>{{ formatTimeHourMinute(interviewDateTime.end_time) }}</span>
          </div>
        </div>
      </div>
      <div id="mainInterview" ref="mainRefInterview" class="interview-details">
        <h3 class="interview-title">オフラベル</h3>
        <div class="details-wrap mb-4">
          <p class="text-center mb-3">オフラベルの情報提供はありましたか？</p>
          <div class="text-center mb-4 confirm">
            <el-radio-group v-if="!isGuest" v-model="interviewDateTime.off_label_flag">
              <el-radio-button :label="valOne" @click="showOffLabel(1)" @touchstart="showOffLabel(1)">はい</el-radio-button>
              <el-radio-button :label="valZero" @click="showOffLabel(0)" @touchstart="showOffLabel(0)">いいえ</el-radio-button>
            </el-radio-group>
            <p v-else class="content-view ml-3" style="white-space: break-spaces">
              <span v-if="interviewDateTime.off_label_flag">はい</span>
              <span v-else>いいえ</span>
            </p>
            <p v-if="isSubmit && interviewDateTime.off_label_flag === ''" class="text-error">{{ getMessage('MSFA0040') }}</p>
          </div>
          <div v-if="interviewDateTime.off_label_flag" class="off-label">
            <div class="label-left">
              <p class="mb-2">・オフラベルが含まれる内容であることを伝え承諾を得ていますか?</p>
              <div class="mb-4">
                <el-radio-group v-if="!isGuest" v-model="offLabel.off_label_concent_flag">
                  <el-radio-button :label="valOne">はい</el-radio-button>
                  <el-radio-button :label="valZero">いいえ</el-radio-button>
                </el-radio-group>
                <p v-else class="content-view ml-3" style="white-space: break-spaces">
                  <span v-if="offLabel.off_label_concent_flag">はい</span>
                  <span v-else>いいえ</span>
                </p>
              </div>
              <p class="mb-2">
                ・オフラベルが含まれる内容の時間はどの程度でしたか?
                <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
              </p>
              <div
                :class="[
                  'd-flex align-items-center',
                  (isSubmit &&
                    (!validation.off_label_call_time.required ||
                      !validation.off_label_call_time.length ||
                      responseErrors.off_label_call_time ||
                      !offLabel.off_label_call_time)) ||
                  offLabel.off_label_call_time === '0'
                    ? ''
                    : 'mb-4',
                  (interviewDateTime.off_label_flag === 1 &&
                    (checkValidField('off_label_call_time') || (isSubmit && !offLabel.off_label_call_time && offLabel.off_label_call_time !== 0))) ||
                  (interviewDateTime.off_label_flag === 1 && isSubmit && !validation.off_label_call_time.length) ||
                  offLabel.off_label_call_time >= 1000 ||
                  responseErrors.off_label_call_time
                    ? 'hasErr'
                    : ''
                ]"
              >
                <input
                  v-if="!isGuest"
                  v-model="offLabel.off_label_call_time"
                  type="number"
                  min="1"
                  class="form-control form-minutes"
                  onkeydown="
                        javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                        ? true : !isNaN(Number(event.key)) && event.code!=='Space'
                        "
                  :style="`width: 120px`"
                />
                <span v-else class="content-view ml-3" style="white-space: break-spaces">{{ offLabel.off_label_call_time }}</span>
                <span class="pl-2">分間</span>
              </div>
              <p
                v-if="
                  interviewDateTime.off_label_flag === 1 &&
                  (checkValidField('off_label_call_time') || (isSubmit && !offLabel.off_label_call_time && offLabel.off_label_call_time !== 0))
                "
                class="text-error mb-4"
              >
                {{ getMessage('MSFA0001', 'オフラベル面談時間') }}
              </p>
              <p
                v-else-if="
                  (interviewDateTime.off_label_flag === 1 && isSubmit && !validation.off_label_call_time.length) || offLabel.off_label_call_time >= 1000
                "
                class="text-error mb-4"
              >
                オフラベル面談時間は0～1000の間で入力してください
              </p>
              <p v-else class="text-error">{{ responseErrors.off_label_call_time }}</p>

              <p class="mb-2">
                ・オフラベルの内容を含んだ製品名
                <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
              </p>
              <div
                v-if="!isGuest"
                :class="
                  checkValidField('related_product') ||
                  (interviewDateTime.off_label_flag === 1 && isSubmit && !validation.related_product.length) ||
                  responseErrors.related_product
                    ? 'hasErr'
                    : ''
                "
              >
                <textarea v-model="offLabel.related_product" class="form-control" placeholder="内容を入力" rows="4"></textarea>
              </div>

              <span v-else class="content-view ml-3" style="white-space: break-spaces">{{ offLabel.related_product }}</span>
              <p v-if="checkValidField('related_product')" class="text-error mb-4">
                {{ getMessage('MSFA0001', 'オフラベル関連製品') }}
              </p>
              <p v-else-if="interviewDateTime.off_label_flag === 1 && isSubmit && !validation.related_product.length" class="text-error mb-4">
                {{ getMessage('MSFA0012', 'オフラベル関連製品', 50) }}
              </p>
              <p v-else class="text-error">{{ responseErrors.related_product }}</p>
            </div>
            <div class="label-right">
              <p class="mb-2">
                ・医師(薬剤師)から要求、質問された内容と受付日付
                <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
              </p>
              <div
                class="mb-2"
                :class="
                  checkValidField('question') || (interviewDateTime.off_label_flag === 1 && isSubmit && !validation.question.length) || responseErrors.question
                    ? 'hasErr'
                    : ''
                "
              >
                <textarea v-if="!isGuest" v-model="offLabel.question" class="form-control" rows="1" placeholder="内容を入力"></textarea>
                <span v-else class="content-view ml-3" style="white-space: break-spaces">{{ offLabel.question }}</span>
                <p v-if="checkValidField('question')" class="text-error mb-4">
                  {{ getMessage('MSFA0001', 'オフラベル質問内容') }}
                </p>
                <p v-else-if="interviewDateTime.off_label_flag === 1 && isSubmit && !validation.question.length" class="text-error mb-4">
                  {{ getMessage('MSFA0012', 'オフラベル質問内容', 300) }}
                </p>
                <p v-else class="text-error">{{ responseErrors.question }}</p>
              </div>
              <p class="mb-2">
                ・医師(薬剤師)へ回答および情報提供した内容
                <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
              </p>
              <div
                class="mb-2"
                :class="
                  checkValidField('answer') || (interviewDateTime.off_label_flag === 1 && isSubmit && !validation.answer.length) || responseErrors.answer
                    ? 'hasErr'
                    : ''
                "
              >
                <textarea v-if="!isGuest" v-model="offLabel.answer" class="form-control" rows="1" placeholder="内容を入力"></textarea>
                <span v-else class="content-view ml-3" style="white-space: break-spaces">{{ offLabel.answer }}</span>
                <p v-if="checkValidField('answer')" class="text-error mb-4">
                  {{ getMessage('MSFA0001', 'オフラベル回答内容') }}
                </p>
                <p v-else-if="interviewDateTime.off_label_flag === 1 && isSubmit && !validation.answer.length" class="text-error mb-4">
                  {{ getMessage('MSFA0012', 'オフラベル回答内容', 300) }}
                </p>
                <p v-else class="text-error">{{ responseErrors.answer }}</p>
              </div>
              <p class="mb-2">
                ・回答および情報提供した内容に対する質問、意見等
                <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
              </p>
              <div
                class="mb-2"
                :class="
                  checkValidField('re_question') ||
                  (interviewDateTime.off_label_flag === 1 && isSubmit && !validation.re_question.length) ||
                  responseErrors.re_question
                    ? 'hasErr'
                    : ''
                "
              >
                <textarea v-if="!isGuest" v-model="offLabel.re_question" class="form-control" rows="1" placeholder="内容を入力"></textarea>
                <span v-else class="content-view ml-3" style="white-space: break-spaces">{{ offLabel.re_question }}</span>
                <p v-if="checkValidField('re_question')" class="text-error mb-4">
                  {{ getMessage('MSFA0001', 'オフラベル回答に対する質問') }}
                </p>
                <p v-else-if="interviewDateTime.off_label_flag === 1 && isSubmit && !validation.re_question.length" class="text-error mb-4">
                  {{ getMessage('MSFA0012', 'オフラベル回答に対する質問', 300) }}
                </p>
                <p v-else class="text-error">{{ responseErrors.re_question }}</p>
              </div>
              <p class="mb-2">
                ・使用または提供した文献、資料名
                <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
              </p>
              <div
                class="mb-2"
                :class="
                  checkValidField('literature') ||
                  (interviewDateTime.off_label_flag === 1 && isSubmit && !validation.literature.length) ||
                  responseErrors.literature
                    ? 'hasErr'
                    : ''
                "
              >
                <textarea v-if="!isGuest" v-model="offLabel.literature" class="form-control" rows="1" placeholder="内容を入力"></textarea>
                <span v-else class="content-view ml-3" style="white-space: break-spaces">{{ offLabel.literature }}</span>
                <p v-if="checkValidField('literature')" class="text-error mb-4">
                  {{ getMessage('MSFA0001', 'オフラベル使用文献') }}
                </p>
                <p v-else-if="interviewDateTime.off_label_flag === 1 && isSubmit && !validation.literature.length" class="text-error mb-4">
                  {{ getMessage('MSFA0012', 'オフラベル使用文献', 100) }}
                </p>
                <p v-else class="text-error">{{ responseErrors.literature }}</p>
              </div>
            </div>
          </div>
        </div>
        <h3 class="interview-title">ディテール</h3>
        <div class="details-wrap">
          <div class="detail-content">
            <div class="detail-left">
              <table class="tableBox tableCustom">
                <colgroup>
                  <col width="14%" />
                  <col width="43%" />
                  <col width="43%" />
                </colgroup>
                <thead>
                  <tr>
                    <th>No</th>
                    <th>面談内容</th>
                    <th>品目</th>
                  </tr>
                </thead>
              </table>
              <div v-if="detailArea.length > 0" class="detail-leftContent" :style="`--dir: 0; --max: ${detailArea.length}`">
                <button :class="`btn-control btn-up ${btnTopFlg ? 'btn-disable' : ''}`" type="button" :disabled="btnTopFlg" @click="scrollTop">
                  <ImageSVG :src-image="'icon_arrow-line-down.svg'" :alt-image="'icon_arrow-line-down'" />
                </button>
                <div :class="`table-interview box  ${isGuest ? 'read-only' : ''}`">
                  <ul id="interviewId" ref="interviewRef" class="interview-list">
                    <Container v-if="!isGuest" drag-handle-selector=".column-drag-handle" lock-axis="y" behaviour="contain" @drop="onDrop">
                      <Draggable
                        v-for="(item, idx) in detailArea"
                        v-show="!item.delete_flag"
                        :key="item.detail_id_new"
                        :style="`--offset:${idx - current}`"
                        :class="selected?.detail_id_new === item?.detail_id_new ? 'box_shadow' : ''"
                      >
                        <div class="draggable-item" @click="selectRecord(item, idx)" @touchstart="selectRecord(item, idx)">
                          <li :class="selected?.detail_id_new === item?.detail_id_new ? 'active' : ''">
                            <div class="cols-1">
                              <ImageSVG class="column-drag-handle move" :src-image="'icon_dots.svg'" :alt-image="'icon_dots'" />
                              <span class="point">{{ item.detail_order }}</span>
                            </div>
                            <div class="cols-2 point">{{ item.content_name_tran }}</div>
                            <div class="cols-3 point">
                              <span class="prName">{{ item.product_name }}</span>
                              <div :class="selected?.detail_id_new === item?.detail_id_new || checkValidateItem(item, idx) ? 'icon-hidden' : 'icon_warning'">
                                <ImageSVG :src-image="'icon_warning.svg'" :alt-image="'icon_warning.svg'" />
                              </div>
                              <ImageSVG class="arrow" :src-image="'icon_arrow-right-gray.svg'" :alt-image="'icon_arrow-right-gray'" />
                            </div>
                          </li>
                        </div>
                      </Draggable>
                    </Container>
                    <li
                      v-for="(item, idx) in detailArea"
                      v-else
                      v-show="!item.delete_flag"
                      :key="item.detail_id_new"
                      :class="`point ${selected?.detail_id_new === item?.detail_id_new ? 'active' : ''}`"
                      @click="selectRecord(item, idx)"
                      @touchstart="selectRecord(item, idx)"
                    >
                      <div class="cols-1">
                        <ImageSVG :src-image="'icon_dots.svg'" :alt-image="'icon_dots'" />
                        <span>{{ item.detail_order }}</span>
                      </div>
                      <div class="cols-2">{{ item.content_name_tran }}</div>
                      <div class="cols-3">
                        <span class="prName">{{ item.product_name }}</span>
                        <div :class="selected?.detail_id_new === item?.detail_id_new || checkValidateItem(item, idx) ? 'icon-hidden' : 'icon_warning'">
                          <ImageSVG :src-image="'icon_warning.svg'" :alt-image="'icon_warning.svg'" />
                        </div>
                        <ImageSVG class="arrow" :src-image="'icon_arrow-right-gray.svg'" :alt-image="'icon_arrow-right-gray'" />
                      </div>
                    </li>
                  </ul>
                </div>
                <button
                  type="button"
                  :class="`btn-control btn-down ${btnBottomFlg ? 'btn-disable' : ''} ${isGuest ? 'custom-btn' : ''}`"
                  :disabled="btnBottomFlg"
                  @click="scrollBottom"
                >
                  <ImageSVG :src-image="'icon_arrow-line-down.svg'" :alt-image="'icon_arrow-line-down'" />
                </button>
                <div v-if="!isGuest" class="button-add">
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-add" @click="addDetailBtn">
                    <span class="icon-plus">+</span> 追加
                  </button>
                </div>
              </div>
              <div v-else class="detail-leftContent-nodata" :class="!isGuest ? '' : 'no_event'" @click="addDetailBtn">
                <button v-if="!isGuest" class="btn-add--custom" type="button"><span class="icon-plus">+</span> 追加</button>
              </div>
            </div>
            <div v-if="selected?.detail_id_new" class="detail-right">
              <div class="title-detail">
                <h2 class="title">詳細</h2>
                <button
                  v-if="!isGuest"
                  type="button"
                  class="btn btn-outline-primary btn-outline-primary--cancel"
                  :disabled="!selected?.detail_id_new"
                  @click="
                    openModal({
                      screenID: 'PopupConfirm',
                      width: '35rem',
                      props: {
                        params: {},
                        title: ' 選択したディテールを完全に削除しますか？',
                        message: '削除すると元に戻すことはできません。'
                      },
                      classs: 'custom-dialog modal-fixed modal-fixed-min'
                    })
                  "
                >
                  <ImageSVG :src-image="'icon_x.svg'" :alt-image="'icon_x'" />
                  ディテールリストから削除
                </button>
              </div>
              <div class="row row-40">
                <div
                  class="col-12 col-lg-6"
                  :class="(isSubmit && !validation.content_cd.required && indexValid !== index) || responseErrors.content_cd ? 'hasErr' : ''"
                >
                  <label class="mb-2">
                    面談内容
                    <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                  </label>
                  <el-select
                    v-if="!isGuest"
                    v-model="detailArea[index].content_cd"
                    placeholder="未選択"
                    class="form-control-select"
                    @change="selectChange(detailArea[index]?.content_cd, 1)"
                    @focus="focusSelection"
                  >
                    <el-option v-for="item in initDetailArea.interviewContent" :key="item.content_cd" :label="item.content_name" :value="item.content_cd" />
                  </el-select>
                  <div v-else class="content-view ml-3" style="white-space: break-spaces">{{ detailArea[index]?.content_name_tran }}</div>
                  <p v-if="isSubmit && !validation.content_cd.required && indexValid !== index" class="text-error">
                    {{ getMessage('MSFA0040', '面談内容') }}
                  </p>
                  <p v-else class="text-error">{{ responseErrors.content_cd }}</p>
                </div>
                <div class="col-12 col-lg-6">
                  <label class="mb-2">
                    品目
                    <span class="required"><img class="svg" src="@/assets/template/images/icon_asterisk.svg" alt="" /></span>
                  </label>
                  <div
                    v-if="!isGuest"
                    class="form-icon icon-right"
                    :class="(isSubmit && !validation.product_name.required && indexValid !== index) || responseErrors.product_name ? 'hasErr' : ''"
                  >
                    <span
                      class="icon log-icon"
                      title_log="品目"
                      @click="
                        openModal({
                          screenID: 'Z05_S06',
                          title: '品目選択',
                          width: '33rem',
                          props: {
                            ...paramsZ05S06,
                            initDataCodes: [detailArea[index].product_cd]
                          },
                          classs: 'custom-dialog modal-fixed modal-fixed-min'
                        })
                      "
                    >
                      <ImageSVG :src-image="'icon_popup.svg'" :alt-image="'icon_popup'" />
                    </span>
                    <div v-if="detailArea[index]?.product_name" class="form-control d-flex align-items-center">
                      <div class="block-tags">
                        <el-tag class="m-0 el-tag-custom" closable @close="removeProduct">
                          {{ detailArea[index]?.product_name }}
                        </el-tag>
                      </div>
                    </div>
                    <el-input v-else clearable readonly placeholder="未選択" class="form-control-input" />
                  </div>
                  <div v-else class="content-view ml-3" style="white-space: break-spaces">{{ detailArea[index]?.product_name }}</div>
                  <p v-if="isSubmit && !validation.product_name.required && indexValid !== index" class="text-error">{{ getMessage('MSFA0040', '品目') }}</p>
                  <p v-else class="text-error">{{ responseErrors.product_name }}</p>
                </div>
              </div>
              <div class="materials-wrap">
                <div class="materials-head">
                  <h2 class="title">資材</h2>
                  <!-- // detailAreaOld -->
                  <div>
                    <el-button
                      v-if="btnEvaluateDoc"
                      type="primary"
                      :disabled="isUpdateProcessing"
                      :class="`btn-evaluate-doc ${btnEvaluateDoc.class}`"
                      @click.prevent="btnEvaluateDoc.event"
                    >
                      {{ btnEvaluateDoc.label }}
                    </el-button>
                    <button
                      v-if="!isGuest"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius button-add"
                      @click="
                        openModal({
                          screenID: 'Z05_S05',
                          title: '資材選択',
                          width: '77rem',
                          props: {
                            mode: 'multiple',
                            subMaterialSelectableFlag: 1,
                            customMaterialFlag: 1,
                            selectParamFlag: true,
                            params: {
                              materialType: '10',
                              productCode: detailArea[index].product_cd,
                              productName: detailArea[index].product_name,
                              date: interviewDateTime.start_date,
                              initMaterials: detailArea[index].materials.filter((e) => e.delete_flag !== -1).map((x) => x.document_id)
                            }
                          },
                          classs: 'custom-dialog custom-dialog-pd-none'
                        })
                      "
                    >
                      <span class="icon-plus">+</span>
                      追加
                    </button>
                  </div>
                </div>
                <div class="scrollbar scroll-child" @mouseenter="childFocus" @mouseleave="childFocusOut" @touchend="childFocusOut" @touchstart="childFocus">
                  <ul class="materials-list">
                    <li v-for="(item, idx) in detailArea[index]?.materials" v-show="!item.delete_flag" :key="idx">
                      <div class="materials-name">
                        <span class="link pr-2 log-icon" title_log="資材" @click="callScreen('D01_S02', { document_id: item.document_id })">{{
                          item.document_name
                        }}</span>
                        <span v-if="checkOutsideTime(item.start_date, item.end_date)" class="outside">適用期間外</span>
                      </div>
                      <button v-if="!isGuest" type="button" class="btn btn--icon btn-remove" @click="deleteMaterial(item, idx)">
                        <ImageSVG :src-image="'icon_close.svg'" :alt-image="'icon_close'" />
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
              <div v-if="!isGuest">
                <div class="row row-40">
                  <div class="col-4">
                    <label class="mb-2"> 反応 </label>
                    <el-select
                      v-model="detailArea[index].reaction_cd"
                      placeholder="未選択"
                      class="form-control-select"
                      @change="selectChange(detailArea[index]?.reaction_cd, 2)"
                      @focus="focusSelection"
                    >
                      <el-option label="" value="">未選択</el-option>
                      <el-option v-for="item in initDetailArea.reaction" :key="item.reaction_cd" :label="item.reaction_name" :value="item.reaction_cd" />
                    </el-select>
                  </div>
                  <div class="col-4">
                    <label class="mb-2"> フェーズ </label>
                    <el-select
                      v-model="detailArea[index].phase_cd"
                      placeholder="未選択"
                      class="form-control-select"
                      @change="selectChange(detailArea[index]?.phase_cd, 3)"
                      @focus="focusSelection"
                    >
                      <el-option label="" value="">未選択</el-option>
                      <el-option v-for="item in initDetailArea.phase" :key="item.phase_cd" :label="item.phase_name" :value="item.phase_cd" />
                    </el-select>
                  </div>
                  <div class="col-4">
                    <div class="form-group">
                      <label class="mb-2">獲得症例</label>
                      <input
                        v-model="detailArea[index].prescription_count"
                        type="number"
                        min="1"
                        class="form-control form-minutes"
                        onkeydown="
                        javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) 
                        ? true : !isNaN(Number(event.key)) && event.code!=='Space'
                        "
                        placeholder="内容を入力"
                      />
                    </div>
                  </div>
                </div>
                <div class="form-group mb-3">
                  <label class="mb-2">特記事項（公開）</label>
                  <textarea v-model="detailArea[index].note" class="form-control" placeholder="内容を入力" rows="4"></textarea>
                </div>
                <div class="form-group mb-3">
                  <label class="mb-2">活動メモ（非公開）</label>
                  <textarea v-model="detailArea[index].remarks" class="form-control" placeholder="内容を入力" rows="4"></textarea>
                </div>
              </div>

              <div v-else class="mode-view mb-3">
                <div class="content-group mb-3">
                  <div class="content-group_item">
                    <p class="mb-2">反応</p>
                    <p class="content-view ml-3" style="white-space: break-spaces">{{ detailArea[index]?.reaction }}</p>
                  </div>
                  <div class="content-group_item">
                    <p class="mb-2">フェーズ</p>
                    <p class="content-view ml-3" style="white-space: break-spaces">{{ detailArea[index]?.phase }}</p>
                  </div>
                  <div class="content-group_item">
                    <p class="mb-2">獲得症例</p>
                    <p class="content-view ml-3" style="white-space: break-spaces">{{ detailArea[index]?.prescription_count }}</p>
                  </div>
                </div>

                <span class="d-block">特記事項（公開）</span>
                <p class="mb-3 content-view ml-3" style="white-space: break-spaces">{{ detailArea[index]?.note }}</p>
              </div>
            </div>
            <div v-else>
              <EmptyData v-if="!isLoading" title="該当するデータがありません。" icon="amico_A01S04" custom-class="customClassEmpty" />
            </div>
          </div>
          <div class="text-center group-button">
            <el-button v-for="(item, idex) in arrBtn" :key="idex" type="primary" :disabled="isUpdateProcessing" :class="item.class" @click.prevent="item.event">
              {{ item.label }}
            </el-button>
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
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="reloadAction(closeModal, 'reload')($event)"
          @handleConfirm="deleteDetail"
          @handleYes="cancelBtn"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { applyDrag } from '@/utils/dragitem.js';
import { Container, Draggable } from 'vue-dndrop';
import { required, validLength } from '@/utils/validate';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';
import Z05_S06_MaterialSelection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import Z05_S05_ChoiceOfMasterial from '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial';
import D01_S06_MaterialEvaluationInput from '@/views/D01/D01_S06_MaterialEvaluationInput/D01_S06_MaterialEvaluationInput';
import Z05_S04_facilityCustomerService from '@/api/Z05/Z05_S04_facilityCustomerService';
import PerfectScrollbar from '@/assets/template/js/perfect-scrollbar.min.js';

import { markRaw } from 'vue';
import _ from 'lodash';

export default {
  name: 'A01_S04_InterviewDetailedInput',
  components: {
    Container,
    Draggable
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        customClass: 'custom-dialog',
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      isLoading: false,
      processing: false,
      isGuest: false,
      arrBtn: [],
      btnEvaluateDoc: null,
      schedule_id: 2,
      call_id: 2,
      interviewDateTime: {
        act_flag: 0,
        call_id: 2,
        end_time: '',
        facility_cd: '',
        facility_short_name: '',
        off_label_flag: '',
        person_cd: '',
        person_name: '',
        report_id: 258,
        report_status_type: '',
        start_date: '',
        start_time: '',
        visit_id: 2,
        user_cd: ''
      },
      offLabel: {
        off_label_concent_flag: 0,
        related_product: '',
        question: '',
        answer: '',
        re_question: '',
        literature: '',
        off_label_call_time: null
      },
      detailArea: [],

      // value old
      interviewDateTimeOld: {},
      offLabelOld: {},
      detailAreaOld: [],

      index: 0,
      initDetailArea: {
        m_variable_definition: { definition_label: '', definition_value: '' },
        interviewContent: [
          {
            content_cd: '10',
            content_name: '情報提供'
          }
        ],
        reaction: [
          {
            reaction_cd: '',
            reaction_name: ''
          }
        ],
        phase: [
          {
            phase_cd: '',
            phase_name: ''
          }
        ]
      },
      selected: {
        detail_order: null,
        content_name_tran: '',
        product_name: '',
        detail_id: null,
        detail_id_new: null,
        change_flag: 0,
        delete_flag: 0,
        content_cd: '',
        product_cd: '',
        reaction_cd: '',
        phase_cd: '',
        prescription_count: '',
        note: '',
        remarks: '',
        materials: [
          {
            document_id: null,
            document_name: null,
            org_label: null,
            start_date: null,
            end_date: null,
            detail_id: null,
            change_flag: 0,
            delete_flag: 0
          }
        ]
      },
      length: 0,
      btnBottomFlg: false,
      btnTopFlg: true,
      add_flg: false,
      deleteArray: [],
      deleteMaterials: [],
      isUpdateProcessing: false,
      current: 0,

      lstMedicalStaff: [],
      indexValid: null,

      paramsZ05S06: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },

      valZero: 0,
      valOne: 1,
      isSaveBtn: false
    };
  },
  computed: {
    validation() {
      return {
        off_label_call_time: !this.interviewDateTime.off_label_flag
          ? true
          : { length: validLength(this.offLabel.off_label_call_time, 11), required: required(this.offLabel.off_label_call_time) },
        related_product: !this.interviewDateTime.off_label_flag
          ? true
          : { length: validLength(this.offLabel.related_product, 50), required: required(this.offLabel.related_product) },
        question: !this.interviewDateTime.off_label_flag
          ? true
          : { length: validLength(this.offLabel.question, 300), required: required(this.offLabel.question) },
        answer: !this.interviewDateTime.off_label_flag ? true : { length: validLength(this.offLabel.answer, 300), required: required(this.offLabel.answer) },
        re_question: !this.interviewDateTime.off_label_flag
          ? true
          : { length: validLength(this.offLabel.re_question, 300), required: required(this.offLabel.re_question) },
        literature: !this.interviewDateTime.off_label_flag
          ? true
          : { length: validLength(this.offLabel.literature, 100), required: required(this.offLabel.literature) },
        content_cd: !(this.detailArea.length > 0) ? true : { required: required(this.detailArea[this.index]?.content_cd) },
        product_name: !(this.detailArea.length > 0) ? true : { required: required(this.detailArea[this.index]?.product_name) }
      };
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '面談詳細入力',
      icon: 'icon_interview-color.svg',
      isShowBack: true
    });

    let queryUrl = this._route('A01_S04_InterviewDetailedInput')?.params;

    if (queryUrl?.call_id || queryUrl?.schedule_id) {
      this.call_id = queryUrl?.call_id;
      this.schedule_id = queryUrl?.schedule_id;
    }
    this.getScreenDetail();

    const els = document.querySelectorAll('.scroll-child');
    const elMain = this.$refs.mainRefInterview;
    els.forEach((el) => {
      el.onscroll = function () {
        elMain.classList.add('stop-scroll');
        return;
      };
    });

    this.psContainer = new PerfectScrollbar('#mainInterview', {
      wheelSpeed: 0.5
    });
  },
  methods: {
    checkIsUpdateDetail(created_at, updated_at) {
      if (new Date(created_at).getTime() === new Date(updated_at).getTime()) {
        return false;
      }
      return true;
    },
    // define func
    cancelBtn() {
      this.back({ schedule_id: this.schedule_id });
    },

    confirmCancel() {
      let isEqual =
        !_.isEqual(this.interviewDateTime, this.interviewDateTimeOld) ||
        !_.isEqual(this.offLabel, this.offLabelOld) ||
        !_.isEqual(this.detailArea, this.detailAreaOld);

      if (isEqual) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
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

    savePlanBtn() {
      this.savePlan({
        call_id: this.call_id,
        schedule_id: this.schedule_id
      });
    },

    materialBtn() {
      this.openModal({
        screenID: 'D01_S06',
        title: '資材評価入力',
        width: '40rem',
        classs: 'custom-dialog modal-fixed-min',
        props: {
          documentUsageId: this.call_id,
          documentUsageType: this.initDetailArea.m_variable_definition.definition_value
        }
      });
    },

    checkValidateItem(dataItem, index) {
      if (this.isSubmit && (!dataItem.content_cd || !dataItem.product_name) && index !== this.indexValid) {
        return false;
      }
      return true;
    },
    saveBtn() {
      this.indexValid = null;
      if (!this.checkValidate() || this.interviewDateTime.off_label_flag === '') {
        this.$notify({ message: '面談詳細情報を入力してください。', customClass: 'error' });
        return;
      } else {
        if (this.detailArea.length === 0) {
          this.$notify({ message: '面談詳細情報を入力してください。', customClass: 'error' });
          return;
        }
      }

      this.save({
        schedule_id: this.schedule_id,
        call_id: this.call_id,
        facility_cd: this.interviewDateTime.facility_cd,
        start_date: this.interviewDateTime.start_date,
        start_time: this.interviewDateTime.start_time,
        person_cd: this.interviewDateTime.person_cd || '',
        person_name: this.interviewDateTime.person_name || '',
        off_label_flag: this.interviewDateTime.off_label_flag || 0,
        off_label_concent_flag: this.offLabel.off_label_concent_flag || 0,
        related_product: this.offLabel.related_product || '',
        question: this.offLabel.question || '',
        answer: this.offLabel.answer || '',
        re_question: this.offLabel.re_question || '',
        literature: this.offLabel.literature || '',
        off_label_call_time: this.offLabel.off_label_call_time || 0,
        detailArea: this.detailArea.map((el) => ({
          ...el,
          delete_materials: this.$_.uniq(el.delete_materials)
        })),
        deleteArray: this.deleteArray
      });
    },
    addDetailBtn() {
      let arr = this.detailArea;
      arr.push({
        detail_id: '',
        detail_id_new: this.generateID(),
        detail_order: this.detailArea.length + 1,
        new_flg: true,
        content_cd: '',
        content_name_tran: '',
        product_cd: '',
        product_name: '',
        document_id: '',
        reaction_cd: '',
        reaction: '',
        phase_cd: '',
        phase: '',
        prescription_count: '',
        note: '',
        remarks: '',
        change_flag: 1,
        delete_flag: 0,
        materials: []
      });
      this.detailArea = arr;
      if (this.detailArea.length > 10) {
        this.length = this.detailArea.length - 9;
        this.btnBottomFlg = false;
      } else {
        this.btnBottomFlg = true;
      }
      this.selectRecord(arr[arr.length - 1], arr.length - 1);
      this.add_flg = true;

      this.indexValid = this.detailArea.length - 1;
    },
    onDrop(dropResult) {
      this.sortOrder(applyDrag(this.detailArea, dropResult, 2).result);
      this.index = applyDrag(this.detailArea, dropResult, 2).addedIndex;
      this.selected = this.detailArea[this.index];
    },
    selectRecord(item, index) {
      this.selected = item;
      this.index = index;
    },
    focusSelection() {
      document.querySelectorAll('.el-select-dropdown__item').forEach((el) => el.classList.remove('hover'));
    },
    selectChange(value, flg) {
      let name = '';
      if (flg === 1) {
        name = value ? this.initDetailArea.interviewContent.find((el) => el.content_cd === value)?.content_name : '';
        this.detailArea[this.index].content_name_tran = name;
      } else if (flg === 2) {
        name = value ? this.initDetailArea.reaction.find((el) => el.reaction_cd === value)?.reaction_name : '';
        this.detailArea[this.index].reaction = name;
      } else {
        name = value ? this.initDetailArea.phase.find((el) => el.phase_cd === value)?.phase_name : '';
        this.detailArea[this.index].phase = name;
      }
      if (
        this.detailArea[this.index].content_name_tran &&
        this.detailArea[this.index].product_name &&
        this.detailArea[this.index].phase &&
        this.detailArea[this.index].reaction
      )
        this.add_flg = false;
    },
    callScreen(screenID, props = {}) {
      let objScreen = {
        D01_S02: 'D01_S02_MaterialDetails'
      };
      if (screenID === 'H02_S02') {
        let person_cd = props.person_cd;
        this.$router.push({
          name: 'H02_PersonalDetails',
          params: {
            person_cd
          },
          query: { tab: '1' }
        });
        return;
      }
      this.$router.push({ name: objScreen[screenID], params: props });
    },
    showOffLabel(flg) {
      this.resetData();
      this.interviewDateTime.off_label_flag = flg;
    },
    checkOutsideTime(startDate, endDate) {
      let today = new Date(new Date().setHours(0, 0, 0, 0));
      if (
        new Date(new Date(startDate).setHours(0, 0, 0, 0)).getTime() <= today.getTime() &&
        today.getTime() <= new Date(new Date(endDate).setHours(0, 0, 0, 0)).getTime()
      ) {
        return false;
      }
      return true;
    },
    checkValidField(field) {
      return this.isSubmit && !this.validation[field].required;
    },
    removeProduct() {
      this.detailArea[this.index].product_name = '';
      this.detailArea[this.index].product_cd = '';
    },
    showButton() {
      let arr = [];
      const userLogin = this.$store.state.auth.currentUser.user_cd;
      this.btnEvaluateDoc = null;
      arr.push({
        label: 'キャンセル',
        class: 'btn btn-outline-primary btn-outline-primary--cancel',
        event: this.confirmCancel
      });
      if (this.interviewDateTime.user_cd === userLogin) {
        let inputed_actual = this.detailArea.length > 0 && new Date() >= new Date(this.interviewDateTime.start_date); // true: 実績入力済, false: 実績未入力
        if (this.interviewDateTime.report_status_type !== '承認済' && this.interviewDateTime.report_status_type !== '提出済') {
          if (inputed_actual) {
            if (this.interviewDateTime.act_flag === 1) {
              arr.push({
                label: '実績から除外',
                class: 'btn btn-outline-primary btn-outline-primary--cancel',
                event: this.savePlanBtn
              });
            }
            arr.push(
              {
                label: '資材評価追加',
                class: 'btn btn-outline-primary btn-outline-primary--cancel show_modal_parent',
                event: this.materialBtn
              },
              {
                label: '保存',
                class: 'btn btn-primary',
                event: this.saveBtn
              }
            );
            this.btnEvaluateDoc = {
              label: '資材評価追加',
              class: 'btn btn-outline-primary btn-outline-primary--cancel show_modal_parent',
              event: this.materialBtn
            };
          } else
            arr.push({
              label: '保存',
              class: 'btn btn-primary',
              event: this.saveBtn
            });
        }
      }
      this.arrBtn = arr;
    },
    checkPermission() {
      let edit_flg = false;
      const userLogin = this.$store.state.auth.currentUser.user_cd;
      if (
        this.interviewDateTime.user_cd === userLogin &&
        this.interviewDateTime.report_status_type !== '承認済' &&
        this.interviewDateTime.report_status_type !== '提出済'
      )
        edit_flg = true;
      this.isGuest = !edit_flg;
      return edit_flg;
    },
    deleteMaterial(item, index) {
      let arr = [...this.detailArea[this.index]?.materials];
      let arrDelete = [...(this.detailArea[this.index]?.delete_materials || [])];
      arr.splice(index, 1);
      arrDelete.push(item.document_id);
      this.detailArea[this.index].materials = arr;
      this.detailArea[this.index].delete_materials = arrDelete;
    },
    deleteDetail() {
      this.modalConfig.isShowModal = false;
      if (this.detailArea.length > 0) {
        this.deleteArray.push(this.detailArea[this.index]);
        this.detailArea.splice(this.index, 1);
        this.sortOrder(this.detailArea);
        if (this.index === 0) this.index = 0;
        else this.index -= 1;
        this.selected = this.detailArea[this.index];
        let count = 0;
        this.detailArea.forEach((element) => {
          if (element.new_flg) count += 1;
        });
        if (!count) this.add_flg = false;
      } else {
        this.index = 0;
        this.selected = {};
      }
    },
    sortOrder(arr, isDefault) {
      this.detailArea = arr.map((item, index) => ({
        ...item,
        detail_order: index + 1,
        note: item.note ? item.note : '',
        remarks: item.remarks ? item.remarks : ''
      }));
      if (isDefault) {
        this.detailAreaOld = JSON.parse(JSON.stringify(this.detailArea));
      }
    },
    scrollTop() {
      this.btnBottomFlg = false;
      this.length += 1;
      if (this.length === this.detailArea.length - 9) {
        this.btnTopFlg = true;
        return;
      }

      this.current = this.current - 1;
      let content = document.querySelector('.box');
      content.scrollTop -= 57;
    },
    scrollBottom() {
      this.btnTopFlg = false;
      if (this.length >= 0) this.length -= 1;
      if (this.length === 0) {
        this.btnBottomFlg = true;
        return;
      }
      this.current = this.current + 1;
      let content = document.querySelector('.box');
      content.scrollTop += 57;
    },
    // call api
    getMedicalStaff(facility_cd) {
      let params = {
        facility_cd: facility_cd
      };
      Z05_S04_facilityCustomerService.getMedicalStaff(params)
        .then((res) => {
          this.lstMedicalStaff = res?.data?.data;
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {});
    },

    getInterviewDetailedInput(params) {
      A01_S04_Service.getInterviewDetailedInputService(params)
        .then((res) => {
          const dataRes = res.data.data;
          this.interviewDateTime = dataRes.interviewDateTime;
          if (!this.checkIsUpdateDetail(dataRes.interviewDateTime.created_at, dataRes.interviewDateTime.updated_at)) {
            this.interviewDateTime.off_label_flag = '';
          }

          this.getMedicalStaff(this.interviewDateTime.facility_cd);

          this.offLabel = {
            ...dataRes.offLabel,
            related_product: dataRes.offLabel.related_product || '',
            question: dataRes.offLabel.question || '',
            answer: dataRes.offLabel.answer || '',
            re_question: dataRes.offLabel.re_question || '',
            literature: dataRes.offLabel.literature || ''
          };
          this.interviewDateTimeOld = { ...this.interviewDateTime };
          this.offLabelOld = { ...this.offLabel };

          this.sortOrder(
            dataRes.detailArea.map((item, idx) => ({
              ...item,
              new_flg: false,
              detail_id_new: this.generateID() + idx,
              delete_materials: []
            })),
            true
          );
          if (this.detailArea.length > 10) {
            this.length = this.detailArea.length - 9;
          } else {
            this.btnBottomFlg = true;
          }
          this.selected = this.detailArea?.length > 0 ? this.detailArea[0] : this.selected;
          this.index = this.detailArea?.length > 0 ? 0 : this.index;

          this.checkPermission();
          this.showButton();
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;

          if (this.psContainer) this.psContainer.destroy();
          this.psContainer = new PerfectScrollbar('#mainInterview', {
            wheelSpeed: 0.5
          });
        });
    },
    getScreenDetail() {
      this.isLoading = true;
      A01_S04_Service.getScreenDetailService()
        .then((res) => {
          const dataRes = res.data.data;
          this.initDetailArea = dataRes;

          this.getInterviewDetailedInput({
            call_id: this.call_id,
            schedule_id: this.schedule_id
          });
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally();
    },
    savePlan(params) {
      this.processing = true;
      this.isUpdateProcessing = true;
      this.isLoading = true;
      A01_S04_Service.savePlanService(params)
        .then((res) => {
          this.getInterviewDetailedInput({
            call_id: this.call_id,
            schedule_id: this.schedule_id
          });
          setTimeout(() => {
            this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
          }, 300);
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.processing = false;
          this.isUpdateProcessing = false;
        });
    },
    save(params) {
      this.processing = true;
      this.isUpdateProcessing = true;
      this.isLoading = true;
      this.isSaveBtn = false;
      A01_S04_Service.saveService(params)
        .then((res) => {
          let today = new Date();
          let startDate = new Date(params.start_date);
          let docOlds = [];
          this.detailAreaOld.forEach((d) => {
            if (d.materials.length > 0) {
              docOlds = [...docOlds, ...d.materials];
            }
          });
          let docNews = [];
          this.detailArea.forEach((d) => {
            if (d.materials.length > 0) {
              docNews = [...docNews, ...d.materials];
            }
          });
          let isEqualDoc = _.isEqual(docOlds, docNews);
          if (today > startDate || !isEqualDoc) {
            this.isSaveBtn = true;
            this.isLoading = false;
            this.materialBtn(true);
          } else {
            this.getInterviewDetailedInput({
              call_id: this.call_id,
              schedule_id: this.schedule_id
            });
            this.resetData();
          }
          setTimeout(() => {
            this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
          }, 300);
        })
        .catch((err) => {
          this.isLoading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.processing = false;
          this.isUpdateProcessing = false;
        });
    },
    // modal
    openModal({ screenID, title = '', width = 0, props = {}, classs = 'custom-dialog' }) {
      if (!screenID) return;

      console.log('props', props);
      let objScreen = {
        Z05_S06: Z05_S06_MaterialSelection,
        Z05_S05: Z05_S05_ChoiceOfMasterial,
        D01_S06: D01_S06_MaterialEvaluationInput,
        PopupConfirm: this.$PopupConfirm
      };
      this.modalConfig = {
        ...this.modalConfig,
        title: title,
        isShowModal: true,
        customClass: classs,
        component: this.markRaw(objScreen[screenID]),
        props: props,
        width: width,
        isShowClose: !(objScreen[screenID].name === 'PopupConfirm')
      };
    },
    closeModal(data) {
      this.modalConfig.isShowModal = false;
      switch (data?.screen) {
        case 'Z05_S05': {
          let oldData = [...this.detailArea[this.index].materials];
          let newData = [...data?.dataMasterialSelected];
          for (let i in oldData) {
            let element = oldData[i];
            if (!newData.some((x) => x.document_id === element.document_id)) {
              this.deleteMaterial(element, i);
            }
          }

          for (let i in newData) {
            let x = newData[i];
            let index = this.detailArea[this.index].materials.findIndex((e) => e.document_id?.toString() === x.document_id?.toString());
            if (index === -1) {
              this.detailArea[this.index].materials.push({
                ...x,
                detail_id: this.detailArea.detail_id,
                change_flag: 1,
                delete_flag: 0
              });
            } else {
              if (this.detailArea[this.index].materials.delete_flag === -1) {
                this.detailArea[this.index].materials.delete_flag = 0;
              }
            }
          }

          break;
        }
        case 'Z05_S06': {
          this.paramsZ05S06 = {
            ...this.paramsZ05S06,
            categoryCode: data.category.definition_value,
            classificationCode: data.classifications.product_group_cd
          };
          this.detailArea[this.index].product_cd = data.dataSelected[0].product_cd;
          this.detailArea[this.index].product_name = data.dataSelected[0].product_name;
          if (
            this.detailArea[this.index].content_name_tran &&
            this.detailArea[this.index].product_name &&
            this.detailArea[this.index].phase &&
            this.detailArea[this.index].reaction
          )
            this.add_flg = false;
          break;
        }
        case 'D01_S06': {
          if (data?.saveReview) {
            this.$notify({ message: this.getMessage('MSFA0048'), customClass: 'success' });
          }

          if (this.isSaveBtn) {
            this.isLoading = true;
            this.getInterviewDetailedInput({
              call_id: this.call_id,
              schedule_id: this.schedule_id
            });
            this.resetData();
            this.isSaveBtn = false;
          }
          break;
        }
        default:
          break;
      }
    },
    facilityDetail(facility_cd) {
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '1' }
      });
    },

    childFocusOut() {
      const elMain = this.$refs.mainRefInterview;
      if (elMain && elMain?.classList) {
        elMain?.classList?.remove('stop-scroll');

        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = new PerfectScrollbar('#mainInterview', {
          wheelSpeed: 0.5
        });
      }
    },

    childFocus() {
      const els = document.querySelectorAll('.scroll-child');
      const elMain = this.$refs.mainRefInterview;

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
$viewport_sm: 767px;
.custom-btn {
  bottom: 0 !important;
  border-radius: 0 0 4px 4px;
}
.box_shadow {
  box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
}
.point {
  cursor: pointer;
}
.move {
  cursor: move;
}
.content-view {
  color: #2d3033;
  position: relative;
}
.content-group {
  display: flex;
  justify-content: center;
  flex-direction: row;
  align-content: center;
  gap: 15px;

  &_item {
    flex: 0 0 calc(33% - 9px);
  }
}
.customClassEmpty {
  margin-top: 3%;
  width: 57%;
  margin: 0 auto;
}
.groupInter {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding-top: 28px;
}
.row-40 {
  margin-left: -20px;
  margin-right: -20px;
}
.row-40 > div {
  padding-left: 20px;
  padding-right: 20px;
}
.title-page {
  background-color: #fff;
  padding: 16px 24px;
  display: flex;
  flex-flow: wrap;
  align-items: center;
  justify-content: space-between;
}
.title-page .title {
  font-size: 24px;
  font-weight: 700;
  color: #448add;
  display: flex;
  align-items: center;
}
.title-page .title svg {
  margin-right: 12px;
}
.title-page .head-info {
  display: flex;
  align-items: center;
  flex-flow: wrap;
  font-size: 16px;
  .info-facility {
    display: flex;
    align-items: center;
    svg {
      margin-right: 7px;
    }
  }
  .title {
    .tlt-no {
      font-size: 1.5rem;
      font-weight: 700;
      color: #5f6b73;
    }
  }
}
.title-page .head-info .info-time,
.title-page .head-info .info-date {
  margin-left: 32px;
  font-size: 18px;
}

.interview-details {
  background-color: #fcfcfc;
  padding: 20px 0 30px;
  box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.16);
  height: 100%;
}
.interview-details .interview-title {
  background-color: #eef6ff;
  margin-bottom: 20px;
  padding: 10px 10px 10px 52px;
  color: #5f6b73;
  max-width: 440px;
  width: 90%;
  font-size: 18px;
  font-weight: 700;
  border-radius: 0 40px 40px 0;
  box-shadow: 2px 2px 5px rgba(145, 161, 171, 0.6);
}
.interview-details .details-wrap {
  padding: 0 25px 0 20px;
}
.interview-details .tabs-link {
  max-width: 400px;
  width: 100%;
  display: flex;
}
.interview-details .tabs-link li {
  width: 50%;
}
.interview-details .tabs-link li .btn {
  width: 100%;
}
.interview-details .tabs-link li:first-child .btn {
  border-radius: 4px 0 0 4px;
}
.interview-details .tabs-link li:last-child .btn {
  border-radius: 0 4px 4px 0;
}

.interview-details .off-label {
  display: flex;
  justify-content: space-between;
  flex-flow: wrap;
  gap: 20px;
}
.interview-details .off-label .label-left {
  flex: 0 0 calc(50% - 10px);
}
.interview-details .off-label .label-left .tabs-link {
  max-width: 320px;
}
.interview-details .off-label .label-left .form-minutes {
  height: 30px;
  width: 60px;
  padding: 5px;
}
.interview-details .off-label .label-right {
  flex: 0 0 calc(50% - 10px);
}
.interview-details .off-label .label-right textarea.form-control {
  min-height: 60px;
}

.interview-details .detail-content {
  display: flex;
  flex-flow: wrap;
  height: 100%;
}
.interview-details .detail-content .detail-left {
  width: 43%;

  table {
    height: 36px;
  }
}
.interview-details .detail-content .detail-left .detail-leftContent,
.interview-details .detail-content .detail-left .detail-leftContent-nodata {
  background-color: #fff;
  box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.16);
  border-radius: 0 0 4px 4px;
  position: relative;
  height: calc(100% - 36px);
}
.interview-details .detail-content .detail-left .detail-leftContent-nodata {
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;

  &:hover {
    background: #eef6ff;
    transition: box-shadow 0.55s, background 0.55s, transform 0.55s ease;
  }

  .btn-add--custom {
    border: none;
    color: #448add;
    font-style: normal;
    font-weight: bold;
    font-size: 36px;
    text-decoration: none;
    span {
      font-size: 45px;
      font-weight: bold;
    }
    &:hover {
      background: transparent;
    }
  }
}
.interview-details .detail-content .detail-left .detail-leftContent .button-add {
  position: absolute;
  height: 52px;
  width: 100%;
  position: absolute;
  bottom: 0;
  left: 0;
  border-radius: 0 0 4px 4px;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #fff;
  box-shadow: inset 0px -1px 0px #e3e3e3;
  z-index: 40;
  &::after {
    content: '';
    width: 5px;
    height: 100%;
    right: 0;
    top: 0;
    background-color: #fff;
    opacity: 1;
    position: absolute;
    box-shadow: inset -5px 0px 9px -4px rgba(183, 195, 203, 0.8);
    z-index: 42;
  }
}
.interview-details .detail-content .detail-left .detail-leftContent .button-add .btn-add {
  width: 100%;
  max-width: 200px;
}
.button-add .icon-plus {
  font-size: 24px;
  font-weight: 700;
  line-height: 20px;
}
.interview-details .detail-content .detail-left .detail-leftContent .btn-control {
  position: absolute;
  height: 25px;
  width: 100%;
  left: 0;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 41;
}
.interview-details .detail-content .detail-left .detail-leftContent .btn-up {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.16);
  top: 0;
  &::after {
    content: '';
    width: 5px;
    height: 100%;
    right: 0;
    top: 0;
    background-color: #fff;
    opacity: 1;
    position: absolute;
    box-shadow: inset -5px 0px 9px -4px rgba(183, 195, 203, 0.8);
    z-index: 42;
  }
}
.interview-details .detail-content .detail-left .detail-leftContent .btn-up .svg {
  transform: rotate(180deg);
}
.interview-details .detail-content .detail-left .detail-leftContent .btn-down {
  box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.16);
  bottom: 52px;
  &::after {
    content: '';
    width: 5px;
    height: 100%;
    right: 0;
    top: 0;
    background-color: #fff;
    opacity: 1;
    position: absolute;
    box-shadow: inset -5px 0px 9px -4px rgba(183, 195, 203, 0.8);
    z-index: 42;
  }
}

.interview-details .detail-content .detail-left .table-interview {
  height: calc(100% - 52px);
  padding: 25px 0;
  overflow: hidden;

  &.read-only {
    height: 100%;

    .interview-list {
      li {
        &.active::after {
          content: '';
          width: 35px;
          height: 100%;
          z-index: 10;
          right: -25px;
          top: 0;
          background: linear-gradient(to right, #fdfdfd, white);
          border-bottom: 1px solid #e3e3e3;
          opacity: 1;
          position: absolute;
          z-index: 40;
        }
      }
    }
  }
}
.interview-details .detail-content .detail-left .interview-list li {
  display: flex;
  position: relative;
}
.interview-details .detail-content .detail-left .interview-list li.active::before {
  content: '';
  width: 4px;
  height: 100%;
  left: 0;
  top: 0;
  background-color: #448add;
  opacity: 0;
  position: absolute;
}
.interview-details .detail-content .detail-left .interview-list .dndrop-draggable-wrapper {
  li {
    &.active::after {
      content: '';
      width: 35px;
      height: 93%;
      z-index: 10;
      right: -25px;
      top: 4px;
      background: linear-gradient(to right, #fdfdfd, white);
      border-bottom: 1px solid #e3e3e3;
      opacity: 1;
      position: absolute;
      z-index: 40;
    }
  }

  &:not(:nth-child(1)) {
    li {
      &.active::after {
        content: '';
        width: 35px;
        height: 100%;
        z-index: 10;
        right: -25px;
        top: 0;
        background: linear-gradient(to right, #fdfdfd, white);
        border-bottom: 1px solid #e3e3e3;
        opacity: 1;
        position: absolute;
        z-index: 40;
      }
    }
  }
}
.interview-details .detail-content .detail-left .interview-list li > div {
  padding: 16px 12px 16px 12px;
  border-bottom: 1px solid #e3e3e3;
}
.interview-details .detail-content .detail-left .interview-list li .cols-1 {
  width: 14%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.interview-details .detail-content .detail-left .interview-list li .cols-2,
.interview-details .detail-content .detail-left .interview-list li .cols-3 {
  width: 43%;
}
.interview-details .detail-content .detail-left .interview-list li .cols-3 {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-right: 22px;
  .arrow {
    flex: 0 0 22px;
  }
  .prName {
    flex-grow: 1;
  }
  .icon_warning {
    display: inherit;
  }
  .icon-hidden {
    display: none;
  }
}
.interview-details .detail-content .detail-left .interview-list li:hover,
.interview-details .detail-content .detail-left .interview-list li.active {
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.16);
}
.interview-details .detail-content .detail-left .interview-list li:hover::before,
.interview-details .detail-content .detail-left .interview-list li:hover::after,
.interview-details .detail-content .detail-left .interview-list li.active::before,
.interview-details .detail-content .detail-left .interview-list li.active::after {
  opacity: 1;
}
.interview-details .detail-content .detail-left .interview-list li.active .cols-3 .arrow {
  display: none;
}

.interview-details .detail-content .detail-right {
  width: 57%;
  box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
  border-radius: 10px;
  background-color: #fff;
  position: relative;
  z-index: 35;
  padding: 20px 24px 32px;
}
.interview-details .detail-content .detail-right .title-detail {
  display: flex;
  justify-content: space-between;
  flex-flow: wrap;
  margin-bottom: 20px;
}
.interview-details .detail-content .detail-right .title-detail .title {
  font-size: 18px;
  font-weight: 700;
  color: #2d3033;
}
.interview-details .detail-content .detail-right .title-detail .btn {
  border-radius: 40px;
}
.interview-details .detail-content .detail-right .materials-wrap {
  background-color: #f7f7f7;
  box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
  border-radius: 8px;
  min-height: 80px;
  max-height: 380px;
  padding: 20px;
  margin-top: 20px;
  margin-bottom: 20px;
}
.interview-details .detail-content .detail-right .materials-wrap .scrollbar {
  min-height: 10px;
  max-height: 300px;
  width: calc(100% + 20px);
  padding-right: 16px;
  padding-left: 4px;
  margin-left: -4px;
  padding-top: 3px;
}
.interview-details .detail-content .detail-right .materials-wrap .materials-head {
  display: flex;
  justify-content: space-between;
  flex-flow: wrap;
  margin-bottom: 10px;
  padding: 0 20px;
}
.interview-details .detail-content .detail-right .materials-wrap .title {
  font-size: 16px;
  font-weight: 700;
}
.interview-details .detail-content .detail-right .materials-wrap .materials-head .button-add {
  height: 40px;
  width: 73px;
  padding: 0;
}

.interview-details .detail-content .detail-right .materials-wrap .materials-list li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
  border-radius: 4px;
  padding: 10px 12px 10px 14px;
  margin-bottom: 12px;
}
.interview-details .detail-content .detail-right .materials-wrap .materials-list li:last-child {
  margin-bottom: 5px;
}
.interview-details .detail-content .detail-right .materials-wrap .materials-list li .materials-name {
  width: calc(100% - 50px);
  .link {
    font-size: 1rem;
  }
}
.outside {
  background-color: #feddde;
  border-radius: 2px;
  font-size: 14px;
  color: #ea5d54;
  padding: 2px 6px;
}
.interview-details .group-button {
  margin-top: 50px;
}
.interview-details .group-button .btn {
  width: 180px;
  margin: 5px 12px;
}

@media screen and (max-width: $viewport_tablet) {
  .interview-details .details-wrap,
  .interview-details .interview-title {
    padding-left: 20px;
    padding-right: 20px;
  }
  .interview-details .off-label {
    margin-right: -12px;
    margin-left: -12px;
  }
  .interview-details .off-label .label-left,
  .interview-details .off-label .label-right {
    padding-left: 12px;
    padding-right: 12px;
    width: 100%;
  }

  .interview-details .detail-content .detail-left,
  .interview-details .detail-content .detail-right {
    width: 100%;
    margin-bottom: 20px;
  }
}

.no_event {
  pointer-events: none;
}
.stop-scroll {
  overflow: hidden;
}

#mainInterview {
  position: relative;
  overflow: hidden !important;
}

.btn-evaluate-doc {
  text-align: center;
  padding: 0.375rem 0.75rem;
  border-radius: 24px;
  margin-right: 10px;
  &::before {
    content: ' ';
    background: url(~@/assets/template/images/icon_pdf_btn.svg) no-repeat left;
    height: 24px;
    padding: 0 10px;
  }
}

.btn-remove {
  width: 37px;
  height: 37px;
}
</style>
