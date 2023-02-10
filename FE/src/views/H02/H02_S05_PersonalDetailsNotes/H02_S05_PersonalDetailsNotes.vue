<template>
  <div v-load-f5="true" class="groupNotes">
    <div class="groupDetail-notes">
      <div class="wrapBtn wrapBtn-note">
        <div class="btnInfo">
          <button
            class="msfa-custom-btn-create"
            type="button"
            :disabled="showBoxCreate"
            :class="['btn btn-create-notes', showBoxCreate ? 'btn-create-disabled' : '']"
            @click="showBoxCreateFunc()"
            @touchstart="showBoxCreateFunc()"
          >
            <span class="btn-iconLeft"> <i class="el-icon-plus"></i> <span>新親登録</span> </span>
          </button>
        </div>
        <div class="groupNotes-filter">
          <div class="groupNotes-info">
            <div class="btnInfo-btn filter-wrapper">
              <button class="btn btn-link btn-filter" type="button" @click="showPopup()">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
              </button>
              <div :class="['dropdown-menu dropdown-filter', ' dropdown-filter--Notes', isShowPopupFilter ? 'show' : '']">
                <div class="item-filter btn-close-filter" @click="closePopup()">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
                </div>
                <h2 class="title-filter">検索条件</h2>
                <div class="groupFilter-notes">
                  <div class="formNotes">
                    <label class="formNotes-label">内容</label>
                    <div class="formNotes-control">
                      <el-input v-model="filter.consideration_contents" clearable placeholder="内容入力" class="form-control-input" />
                    </div>
                  </div>
                  <div class="formNotes">
                    <label class="formNotes-label">更新日</label>
                    <div class="formNotes-control d-flex">
                      <div class="formNotes-col">
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="filter.last_update_datetime_from"
                            :disabled-date="disabledDateStart"
                            type="date"
                            :editable="false"
                            placeholder="開始日"
                            format="YYYY/M/D"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                      </div>
                      <span class="formNotes-item">～</span>
                      <div class="formNotes-col">
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="filter.last_update_datetime_to"
                            :disabled-date="disabledDateEnd"
                            type="date"
                            :editable="false"
                            placeholder="終了日"
                            format="YYYY/M/D"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="formNotes-checkBox">
                    <label class="formNotes-label">注意事項種別</label>

                    <ul>
                      <li>
                        <label class="custom-control custom-checkbox custom-control--bordGreen">
                          <input
                            value="10"
                            class="custom-control-input"
                            type="checkbox"
                            :checked="considerationTypes.includes('10')"
                            @change="OnConsiderationTypes($event, 0)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">訪問規制</span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-control custom-checkbox custom-control--bordGreen">
                          <input
                            value="20"
                            class="custom-control-input"
                            type="checkbox"
                            :checked="considerationTypes.includes('20')"
                            @change="OnConsiderationTypes($event, 1)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">薬審</span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-control custom-checkbox custom-control--bordGreen">
                          <input
                            value="40"
                            class="custom-control-input"
                            type="checkbox"
                            :checked="considerationTypes.includes('40')"
                            @change="OnConsiderationTypes($event, 3)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">院内情報入手先</span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-control custom-checkbox custom-control--bordGreen">
                          <input
                            value="30"
                            class="custom-control-input"
                            type="checkbox"
                            :checked="considerationTypes.includes('30')"
                            @change="OnConsiderationTypes($event, 2)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">継続事項</span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-control custom-checkbox custom-control--bordGreen">
                          <input
                            value="50"
                            class="custom-control-input"
                            type="checkbox"
                            :checked="considerationTypes.includes('50')"
                            @change="OnConsiderationTypes($event, 4)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">他社情報</span>
                        </label>
                      </li>
                      <li>
                        <label class="custom-control custom-checkbox custom-control--bordGreen">
                          <input
                            value="99"
                            class="custom-control-input"
                            type="checkbox"
                            :checked="considerationTypes.includes('99')"
                            @change="OnConsiderationTypes($event, 5)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">その他</span>
                        </label>
                      </li>
                    </ul>
                  </div>
                  <div class="btnFilter-Notes">
                    <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancelPopup()">キャンセル</button>
                    <button type="button" class="btn btn-primary btn-filter-submit" @click="getNotes(false)">検索</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="groupDetail-main">
        <div ref="detailScrollTop" :class="[showBoxCreate || data.length > 0 ? 'group-row w-500' : 'group-row']" class="scrollbar">
          <div class="group-col">
            <div v-show="showBoxCreate" :class="['boxNotes', showBoxCreate ? 'create-box' : 'hidden-create-box']">
              <div class="boxNotes-main">
                <div class="notesPd">
                  <div class="formSelect" :class="isSubmit && !validation.consideration_type.required ? 'hasErr' : ''">
                    <el-select v-model="personal.consideration_type" placeholder="未選択" class="form-control-select-c">
                      <el-option value=""> 未選択</el-option>
                      <el-option
                        v-for="item in options"
                        :key="item.consideration_type"
                        :label="item.consideration_type_name"
                        :value="item.consideration_type"
                        @click="checkValueData('consideration_type', item.consideration_type_name)"
                      >
                      </el-option>
                    </el-select>
                    <div class="invalid-feedback">
                      <span v-if="isSubmit && !validation.consideration_type.required">{{ validateMessages.consideration_type.required }}</span>
                    </div>
                  </div>
                  <div class="formDestination">
                    <label class="formDestination-label">確認依頼先</label>
                    <div class="formDestination-control">
                      <div class="form-icon icon-right">
                        <span class="icon log-icon" title_log="確認依頼先" @click="openModalZ05S01()" @touchstart="openModalZ05S01()">
                          <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                        </span>
                        <div v-if="userList.length > 0" class="form-control d-flex align-items-center">
                          <div class="block-tags">
                            <el-tag
                              v-for="item in userList"
                              :key="item.code"
                              :label="item.name"
                              :value="item.name"
                              class="el-tag-custom"
                              closable
                              @close="handleRemoveUser(item)"
                            >
                              {{ item.name }}
                            </el-tag>
                          </div>
                        </div>
                        <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                      </div>
                    </div>
                  </div>
                  <div class="formTextarea" :class="isSubmit && !validation.consideration_contents.required ? 'hasErr' : ''">
                    <el-input
                      v-model="personal.consideration_contents"
                      autosize
                      type="textarea"
                      placeholder="内容入力"
                      class="form-control-textarea"
                      @change="checkValueData('consideration_contents', personal.consideration_contents)"
                    />
                    <!-- <div class="invalid-feedback">
                      <span v-if="isSubmit && !validation.consideration_contents.required">{{ validateMessages?.consideration_contents?.required }}</span>
                    </div> -->
                    <div class="invalid-feedback">
                      <span v-if="isSubmit && !validation.consideration_contents.required">{{ validateMessages.consideration_contents.required }}</span>
                    </div>
                  </div>
                  <div class="formButton">
                    <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="closeCreate()">
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit02.svg')" alt="" />
                      </span>
                      キャンセル
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="onCreate()">
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_save.svg')" alt="" />
                      </span>
                      保存
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!--Create Box End -->
            <!-- List Left -->
            <template v-for="(item, index) of data" :key="item">
              <div v-if="showBoxEdit !== item?.person_consideration_id" :class="[index % 2 == 0 ? 'boxNotes' : '']">
                <template v-if="index % 2 == 0">
                  <div :id="`move-${index}`" :ref="`detailScroll2${index}`" class="boxNotes-main">
                    <div class="notesPd">
                      <div class="restrictions-head">
                        <h2 class="restrictions-title">{{ item.consideration_type_name }}</h2>
                        <div class="restrictions-info">
                          <p class="restrictions-date">
                            最終更新：<span> {{ item.last_update_datetime }} </span>
                          </p>
                          <ul>
                            <li>
                              <span class="restrictions-item">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user05.svg')" alt="" />
                              </span>
                              {{ item.create_user_name }}
                            </li>
                            <li>
                              <span class="restrictions-item">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building.svg')" alt="" />
                              </span>
                              {{ item.create_org_label }}
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="restrictions-content">
                        <div v-show="item?.consideration_contents?.length > 0" class="restrictions-text">
                          <p v-if="item.consideration_contents?.length > 100 && !item?.readMore">
                            <span>{{ item.consideration_contents?.slice(0, 100) }}</span>
                            <span>...</span>
                            <a :id="'detail' + item.facility_consideration_id" class="more" @click="item.readMore = true" @touchstart="item.readMore = true">
                              もっと見る
                            </a>
                          </p>
                          <p v-else>
                            <span> {{ item.consideration_contents }} </span>
                            <a
                              v-if="item.consideration_contents?.length > 100"
                              :id="'detail' + item.facility_consideration_id"
                              class="more"
                              @click="hideMore(item, index)"
                              @touchstart="item.readMore = false"
                            >
                              元に戻る</a
                            >
                          </p>
                        </div>
                        <div v-if="showEditDeleteBox(item)" class="restrictions-tools">
                          <div class="planningStatus-btn">
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
                                <!--Show Update Box-->
                                <li @click="showBoxEditFunc(item)" @touchstart="showBoxEditFunc(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
                                  </span>
                                  <span class="text-label">編集</span>
                                </li>
                                <!--Delete-->
                                <li @click="onDelete(item)" @touchstart="onDelete(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" />
                                  </span>
                                  <span class="text-label">削除</span>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-if="showPersonConfirm(item)" class="verification">
                    <div class="verification-text">通知先に設定されました。確認してください。</div>
                    <div class="verification-btn">
                      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="userConfirm(item)">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_check-circle.svg')" alt="" />
                        </span>
                        確認
                      </button>
                    </div>
                  </div>
                </template>
              </div>
              <!--Updtae Box-->
              <div v-if="index % 2 == 0 && showBoxEdit == item.person_consideration_id" class="boxNotes box-edit">
                <div class="boxNotes-main">
                  <div class="notesPd">
                    <div class="formSelect">
                      <el-select v-model="personal.consideration_type" placeholder="未選択" class="form-control-select-c">
                        <el-option value=""> 未選択</el-option>

                        <el-option
                          v-for="item2 in options"
                          :key="item2.consideration_type"
                          :label="item2.consideration_type_name"
                          :value="item2.consideration_type"
                          @click="checkValueData('consideration_type', item2.consideration_type_name)"
                        >
                        </el-option>
                      </el-select>
                    </div>
                    <div class="formDestination">
                      <label class="formDestination-label">確認依頼先</label>
                      <div class="formDestination-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="確認依頼先" @click="openModalZ05S01()" @touchstart="openModalZ05S01()">
                            <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                          </span>
                          <div v-if="userList.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item3 in userList"
                                :key="item3.code"
                                :label="item3.name"
                                :value="item3.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemoveUser(item)"
                              >
                                {{ item3.name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </div>
                    <div class="formTextarea" :class="isSubmit && !validation.consideration_contents.required ? 'hasErr' : ''">
                      <el-input
                        v-model="personal.consideration_contents"
                        autosize
                        type="textarea"
                        placeholder="内容入力"
                        class="form-control-textarea"
                        @change="checkValueData('consideration_contents', item.consideration_contents)"
                      />
                      <div class="invalid-feedback">
                        <span v-if="isSubmit && !validation.consideration_contents.required">{{ validateMessages?.consideration_contents.required }}</span>
                      </div>
                    </div>
                    <div class="formButton">
                      <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="closeBoxEdit(index)">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit02.svg')" alt="" />
                        </span>
                        キャンセル
                      </button>
                      <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="onSave()">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_save.svg')" alt="" />
                        </span>
                        保存
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!--Updtae Box End-->
            </template>
            <!-- List left End -->
          </div>
          <!--Card Right-->
          <div class="group-col">
            <!-- List Right -->
            <template v-for="(item, index) of data" :key="item">
              <div v-if="showBoxEdit !== item?.person_consideration_id" :class="[index % 2 != 0 ? 'boxNotes' : '']">
                <template v-if="index % 2 != 0">
                  <div :id="`move-${index}`" :ref="`detailScroll2${index}`" class="boxNotes-main">
                    <div class="notesPd">
                      <div class="restrictions-head">
                        <h2 class="restrictions-title">{{ item.consideration_type_name }}</h2>
                        <div class="restrictions-info">
                          <p class="restrictions-date">
                            最終更新：<span> {{ item.last_update_datetime }} </span>
                          </p>
                          <ul>
                            <li>
                              <span class="restrictions-item">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user05.svg')" alt="" />
                              </span>
                              {{ item.create_user_name }}
                            </li>
                            <li>
                              <span class="restrictions-item">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_building.svg')" alt="" />
                              </span>
                              {{ item.create_org_label }}
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="restrictions-content">
                        <div v-show="item?.consideration_contents?.length > 0" class="restrictions-text">
                          <p v-if="item.consideration_contents?.length > 100 && !item?.readMore">
                            <span>{{ item.consideration_contents?.slice(0, 100) }}</span>
                            <span>...</span>
                            <a :id="'detail' + item.facility_consideration_id" class="more" @click="item.readMore = true" @touchstart="item.readMore = true">
                              もっと見る
                            </a>
                          </p>
                          <p v-else>
                            <span> {{ item.consideration_contents }} </span>
                            <a
                              v-if="item.consideration_contents?.length > 100"
                              :id="'detail' + item.facility_consideration_id"
                              class="more"
                              @click="hideMore(item, index)"
                              @touchstart="item.readMore = false"
                            >
                              元に戻る</a
                            >
                          </p>
                        </div>
                        <div v-if="showEditDeleteBox(item)" class="restrictions-tools">
                          <div class="planningStatus-btn">
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
                                <!--Show Update Box-->
                                <li @click="showBoxEditFunc(item)" @touchstart="showBoxEditFunc(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
                                  </span>
                                  <span class="text-label">編集</span>
                                </li>
                                <!--Delete-->
                                <li @click="onDelete(item)" @touchstart="onDelete(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" />
                                  </span>
                                  <span class="text-label">削除</span>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-if="showPersonConfirm(item)" class="verification">
                    <div class="verification-text">通知先に設定されました。確認してください。</div>
                    <div class="verification-btn">
                      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="userConfirm(item)">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_check-circle.svg')" alt="" />
                        </span>
                        確認
                      </button>
                    </div>
                  </div>
                </template>
              </div>
              <!--Update Box-->
              <div v-if="index % 2 != 0 && showBoxEdit == item.person_consideration_id" class="boxNotes box-edit">
                <div class="boxNotes-main">
                  <div class="notesPd">
                    <div class="formSelect" :class="isSubmit && !validation.consideration_type.required ? 'hasErr' : ''">
                      <el-select v-model="personal.consideration_type" placeholder="未選択" class="form-control-select-c">
                        <el-option value=""> 未選択</el-option>

                        <el-option
                          v-for="item4 in options"
                          :key="item4.consideration_type"
                          :label="item4.consideration_type_name"
                          :value="item4.consideration_type"
                          @click="checkValueData('consideration_type', item5.consideration_type_name)"
                        >
                        </el-option>
                      </el-select>
                      <div class="invalid-feedback">
                        <span v-if="isSubmit && !validation.consideration_type.required">{{ validateMessages.consideration_type.required }}</span>
                      </div>
                    </div>
                    <div class="formDestination">
                      <label class="formDestination-label">確認依頼先</label>
                      <div class="formDestination-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="確認依頼先" @click="openModalZ05S01()" @touchstart="openModalZ05S01()">
                            <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                          </span>
                          <div v-if="userList.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item5 in userList"
                                :key="item5.code"
                                :label="item5.name"
                                :value="item5.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemoveUser(item5)"
                              >
                                {{ item5.name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </div>
                    <div class="formTextarea" :class="isSubmit && !validation.consideration_contents.required ? 'hasErr' : ''">
                      <el-input
                        v-model="personal.consideration_contents"
                        autosize
                        type="textarea"
                        placeholder="内容入力"
                        class="form-control-textarea"
                        @change="checkValueData('consideration_contents', item.consideration_contents)"
                      />
                      <div class="invalid-feedback">
                        <span v-if="isSubmit && !validation.consideration_contents.required">{{ validateMessages?.consideration_contents.required }}</span>
                      </div>
                    </div>
                    <div class="formButton">
                      <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="closeBoxEdit(index)">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit02.svg')" alt="" />
                        </span>
                        キャンセル
                      </button>
                      <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="onSave()">
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_save.svg')" alt="" />
                        </span>
                        保存
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!--Update Box End-->
            </template>
            <!--List Right End-->
          </div>
        </div>
        <EmptyData :class="isLoadDefault || showBoxCreate || data.length > 0 ? 'hide' : ''" custom-class="custom-class-no-data w-100" />
      </div>
    </div>
  </div>
  <!-- Modal Z05-S01 -->
  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="modalConfig.isShowClose"
    @close="closeModal()"
  >
    <template #default>
      <component
        :is="modalConfig.component"
        v-bind="{ ...modalConfig.props }"
        @onFinishScreen="onResultModalZ05S01"
        @handleConfirm="onCloseModal"
        @handleYes="resultConfirm"
      ></component>
    </template>
  </el-dialog>
  <!-- Modal  Delete Confirm -->
  <el-dialog
    v-model="modalConfig1.isShowModal"
    :custom-class="modalConfig1.customClass"
    :title="modalConfig1.title"
    :width="modalConfig1.width"
    :destroy-on-close="modalConfig1.destroyOnClose"
    :close-on-click-modal="modalConfig1.closeOnClickMark"
    :show-close="modalConfig1.isShowClose"
    @close="closeModal()"
  >
    <template #default>
      <component
        :is="modalConfig1.component"
        v-bind="{ ...modalConfig1.props }"
        @onFinishScreen="onCloseModalDelete"
        @handleConfirm="deletePersonGroup"
        @handleYes="resultConfirm"
      ></component>
    </template>
  </el-dialog>
</template>

<script>
import H02_S05_PersonalDetailsNotesServices from '@/api/H02/H02_S05_PersonalDetailsNotesServices';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import validateMessages from '../../../utils/validateMessages/H02/H02_S05_PersonalDetailsNotes';
import { required } from '../../../utils/validate';
import { markRaw } from 'vue';
import { encodeData } from '@/api/base64_api';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'H02_S05_PersonalDetailsNotes',
  components: { Z05S01Organization },
  mixins: [filter_popup],
  props: {
    changetab: {
      type: String,
      default: '4'
    },
    dropdownFilter: {
      type: String,
      default: ''
    },
    person_cd: [String],
    checkLoading: [Boolean],
    personname: [String]
  },
  emits: ['changeLoading'],

  data() {
    return {
      index: 0,
      isLoadDefault: true,
      dropdown: '',
      // isShowPopupFilter: false,
      validateMessages,
      isSubmit: false,
      validate: {},
      data: [],
      personInCharges: [],
      paramsZ05S01: {
        userFlag: 1,
        mode: 'multiple',
        userSelectFlag: 1,
        orgCdList: [],
        userCdList: [],
        userRawList: []
      },

      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        customClass: 'custom-dialog',
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },

      modalConfig1: {
        title: '',
        isShowModal: false,
        component: null,
        customClass: 'custom-dialog',
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      value1: '',
      textarea1: '',
      loading: false,
      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      },
      options: [],
      showBoxEdit: '',
      showBoxCreate: false,
      hideBoxEdit: false,
      personal: {
        consideration_type: '',
        consideration_contents: '',
        person_consideration_confirm: '',
        person_consideration_id: '',
        person_cd: ''
      },
      filter: {
        last_update_datetime_from: '',
        last_update_datetime_to: '',
        consideration_type: '10,20,30,40,50,99',
        consideration_contents: ''
      },
      considerationTypes: ['10', '20', '30', '40', '50', '99'],
      user_login_cd: '',
      userList: [],
      hideImage: false
    };
  },
  computed: {
    validation() {
      return {
        consideration_type: { required: required(this.personal.consideration_type) },
        consideration_contents: { required: required(this.personal.consideration_contents) }
      };
    }
  },
  watch: {
    changetab: function (value) {
      // eslint-disable-next-line eqeqeq
      if (value == 4) {
        this.getNotes(true);
        this.getScreenData();
        this.showBoxEdit = '';
        this.showBoxCreate = false;
        this.saveDateWasEdit('', false);
      }
    }
  },
  created() {
    this.user_login_cd = this.getCurrentUser().user_cd;
    // eslint-disable-next-line eqeqeq
    if (this.changetab == 4) {
      const checkNull = (field) => (field ? field : '');
      let array = this.decodeData(localStorage.getItem('screen')) ? this.decodeData(localStorage.getItem('screen')) : [];
      const data = array.find((s) => s.screen_name === 'H02_S05_PersonalDetailsNotes');
      this.filter.last_update_datetime_from = data ? checkNull(data.filter?.last_update_datetime_from) : '';
      this.filter.last_update_datetime_to = data ? data.filter.last_update_datetime_to : '';
      this.filter.consideration_type = data ? data.filter.consideration_type : '10,20,30,40,50,99';
      this.filter.consideration_contents = data ? data.filter.consideration_contents : '';
      this.getNotes(true);
      this.getScreenData();
      this.saveDateWasEdit('', false);
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '個人詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    // Load scroll
    setTimeout(() => {
      this.js_pscroll(0.5);
    }, 500);
  },

  methods: {
    disabledDateStart(time) {
      if (this.filter.last_update_datetime_to) {
        return time.getTime() > new Date(this.filter.last_update_datetime_to).getTime();
      }
    },

    disabledDateEnd(time) {
      if (this.filter.last_update_datetime_from) {
        return time.getTime() < new Date(this.filter.last_update_datetime_from).getTime();
      }
    },
    saveDateWasEdit(swicth, isSave) {
      if (isSave) {
        sessionStorage.setItem('H02S05', swicth);
      } else {
        sessionStorage.removeItem('H02S05');
      }
    },
    checkDateWasEdit() {
      const data = sessionStorage.getItem('H02S05');
      return data != null ? true : false;
    },
    checkValueData(key, value) {
      const data = `${key}:${value}`;
      this.saveDateWasEdit(data, true);
    },
    /** Modal Z05 - S03*/
    // Modal Z05-S03
    // check form
    openModalZ05S01() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'ユーザ選択',
        isShowModal: true,
        component: markRaw(Z05S01Organization),
        props: { ...this.paramsZ05S01 },
        width: this.currDevice() !== 'Desktop' ? '95%' : '65rem',
        customClass: 'custom-dialog',
        destroyOnClose: true,
        isShowClose: true
      };
      this.changeTrueClassHeader();
    },
    hideMore(item, index) {
      let scrollDiv = document.getElementById(`move-${index}`).offsetTop;
      this.$refs['detailScrollTop'].scrollTop = scrollDiv;
      item.readMore = false;
    },
    modalConfirmMove(callBack) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: { mode: 1, textCancel: 'いいえ', params: { callBack } },
        isShowClose: false
      };
    },
    resultConfirm(e) {
      e.callBack();
      this.onCloseModal();
      if (this.index !== 0) {
        let scrollDiv = document.getElementById(`move-${this.index}`).offsetTop;
        this.$refs['detailScrollTop'].scrollTop = scrollDiv;
        this.index = 0;
      } else {
        this.$refs['detailScrollTop'].scrollTop = 0;
      }
    },
    closeModal() {
      this.changeFalseClassHeader();
    },
    /** Modal model edmit data for H02_S05 */
    onResultModalZ05S01(e) {
      if (e && e?.userSelected && e?.userSelected.length > 0) {
        this.paramsZ05S01.orgCdList = [];
        this.paramsZ05S01.userCdList = [];
        e.userSelected?.forEach((x) => {
          this.paramsZ05S01.orgCdList.push(x.org_cd);
        });
        this.paramsZ05S01.userCdList = e.userSelected.map((s) => ({
          org_cd: s.org_cd,
          user_cd: s.user_cd
        }));
        const userSelected = e.userSelected.map((x) => ({ org_cd: x.org_cd, code: x.user_cd, name: x.user_name }));
        const userLists = this.paramsZ05S01.userRawList.concat(userSelected);
        let uniqueUsercd = userLists
          .map((v) => v['code'])
          .map((v, i, array) => array.indexOf(v) === i && i)
          .filter((v) => userLists[v])
          .map((v) => userLists[v]);
        if (uniqueUsercd.some((s) => s.code === this.user_login_cd)) {
          this.$notify({ message: '自分のアカウントでは追加できません。異なるユーザーを選択してください。', customClass: 'error' });
        } else {
          this.userList = uniqueUsercd;
        }
        this.checkValueData('user', uniqueUsercd.join());
        this.onCloseModal();
      } else {
        this.onCloseModal();
      }
    },
    handleRemoveUser(item) {
      this.userList = this.userList.filter((x) => x.code !== item.code);
      this.paramsZ05S01.userCdList = this.userList
        .filter((s) => s.code !== item.code)
        .map((s) => ({
          org_cd: s.org_cd,
          user_cd: s.code
        }));
      this.paramsZ05S01.userRawList = this.paramsZ05S01.userRawList.filter((x) => x.code !== item.code);
      this.checkValueData('user', this.userList.join());
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    onCloseModalDelete() {
      this.modalConfig1 = { ...this.modalConfig1, isShowModal: false, customClass: 'custom-dialog' };
    },
    onDelete(data) {
      this.modalConfig1 = {
        ...this.modalConfig1,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: '選択した注意事項を完全に削除しますか？' },
        isShowClose: false
      };
      this.personal = data;
    },
    deletePersonGroup() {
      this.deleted(this.personal);
      this.onCloseModalDelete();
    },

    /** Modal end */
    /** API Start*/
    created() {
      // if (!this.checkValidate()) return;
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      const id = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd : '';
      const body = new FormData();
      body.append('person_cd', encodeData(id));
      body.append('consideration_type', encodeData(this.personal.consideration_type));
      body.append('consideration_contents', encodeData(this.personal.consideration_contents));
      body.append('person_consideration_confirm', encodeData(this.personal.person_consideration_confirm));
      body.append('person_name', encodeData(this.personname));
      this.$emit('changeLoading', true);
      H02_S05_PersonalDetailsNotesServices.createNote(body)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.showBoxEdit = '';
          this.showBoxCreate = false;
          this.$notify({ message: message, customClass: 'success' });
          this.saveDateWasEdit('', false);
          localStorage.removeItem('DataResH02');
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }), this.$emit('changeLoading', false))
        .finally(() => this.$emit('changeLoading', false));
    },
    updated() {
      // if (!this.checkValidate()) return;
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      const body = {
        person_consideration_id: this.personal.person_consideration_id,
        consideration_contents: this.personal.consideration_contents,
        person_consideration_confirm: this.personal.person_consideration_confirm,
        consideration_type: this.personal.consideration_type,
        person_cd: this.personal.person_cd,
        person_name: this.personname
      };
      this.$emit('changeLoading', true);
      H02_S05_PersonalDetailsNotesServices.updateNote(body)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.showBoxEdit = '';
          this.$notify({ message: message, customClass: 'success' });
          this.saveDateWasEdit('', false);
          localStorage.removeItem('DataResH02');
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => this.$emit('changeLoading', false));
    },
    deleted(body) {
      const data = {
        person_consideration_id: body.person_consideration_id,
        person_cd: body.person_cd
      };
      H02_S05_PersonalDetailsNotesServices.deleteNote(data)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.$notify({ message: message, customClass: 'success' });
          this.saveDateWasEdit('', false);
          localStorage.removeItem('DataResH02');
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }));
    },
    userConfirm(item) {
      const id = item.person_consideration_id;
      H02_S05_PersonalDetailsNotesServices.updateConsiderationConfirm(id)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.$notify({ message: message, customClass: 'success' });
          localStorage.removeItem('DataResH02');
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }));
    },
    getNotes(isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      this.$emit('changeLoading', true);

      const id = this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd || localStorage.getItem('person_cd_prev') : '';
      this.filter.last_update_datetime_from = this.formatFullDate(this.filter.last_update_datetime_from);
      this.filter.last_update_datetime_to = this.formatFullDate(this.filter.last_update_datetime_to);
      H02_S05_PersonalDetailsNotesServices.fetchNotesService(id, this.filter)
        .then((s) => {
          if (s) {
            this.data = s?.notes.map((item) => ({ ...item, last_update_datetime: this.formatFullDate(item.last_update_datetime) }));
            this.personInCharges = s?.person_in_charge_list;
            this.resetModel();
          }
        })
        .catch((err) => {
          const message = err.response.data.message;
          this.$notify({ message: message, customClass: 'error' });
          this.resetModel();
        })
        .finally(() => {
          if (this.$refs['detailScrollTop']) {
            this.$refs['detailScrollTop'].scrollTop = 0;
          }
          this.$emit('changeLoading', false);
          this.isLoadDefault = false;
        });
    },
    getScreenData() {
      H02_S05_PersonalDetailsNotesServices.getScreenDataService()
        .then((s) => {
          if (s) {
            const considerationType = s?.considerationType ? s.considerationType : [];
            this.options = considerationType;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    },
    /** API End*/
    /** Filter checkbox  ConsiderationTypes group*/
    OnConsiderationTypes(event, index) {
      if (event.target.checked) {
        this.considerationTypes[index] = event.target.value;
      } else {
        this.considerationTypes[index] = '';
      }
      this.filter.consideration_type = this.considerationTypes.filter((s) => s !== '').join();
    },
    onCreate() {
      // this.$msgboxConfirm({
      //   title: '',
      //   message: 'This is create data?',
      //   callback: (action) => {
      //     if (action === 'confirm') {
      //       //   this.postPasswordReissue(body);

      //     }
      //   }
      // });
      this.personal.person_consideration_confirm = this.userList.map((s) => s.code).join();
      this.created();
    },

    onSave() {
      this.personal.person_consideration_confirm = this.userList.map((s) => s.code).join();
      this.updated();
    },
    modalLeaveBoxEdit(item) {
      const id = item.person_consideration_id;
      if (this.checkDateWasEdit()) {
        this.modalConfirmMove(() => {
          sessionStorage.removeItem('H02S05');
          this.userList = item?.user_confirm_list?.map((s) => ({ org_cd: s.org_cd, code: s.user_cd, name: s.user_name }));
          this.paramsZ05S01.userRawList = item?.user_confirm_list?.map((s) => ({ org_cd: s.org_cd, code: s.user_cd, name: s.user_name }));
          this.showBoxEdit = id;
          this.showBoxCreate = false;
          this.hideBoxEdit = true;
          this.personal = {
            consideration_type: item.consideration_type,
            person_cd: item.person_cd,
            consideration_type_name: item.consideration_type_name,
            person_consideration_id: item.person_consideration_id,
            consideration_contents: item.consideration_contents,
            person_consideration_confirm: item?.user_confirm_list?.map((s) => s.user_cd).join()
          };
          this.saveDateWasEdit('', false);
        });
      } else {
        this.showBoxCreate = false;
        this.showBoxEdit = id;
        this.userList = item?.user_confirm_list?.map((s) => ({ org_cd: s.org_cd, code: s.user_cd, name: s.user_name }));
        this.paramsZ05S01.userRawList = item?.user_confirm_list?.map((s) => ({ org_cd: s.org_cd, code: s.user_cd, name: s.user_name }));
        this.personal = {
          consideration_type: item.consideration_type,
          person_cd: this._route('H02_PersonalDetails') ? this._route('H02_PersonalDetails').params.person_cd || localStorage.getItem('person_cd_prev') : '',
          consideration_type_name: item.consideration_type_name,
          person_consideration_id: item.person_consideration_id,
          consideration_contents: item.consideration_contents,
          person_consideration_confirm: item?.user_confirm_list?.map((s) => s.user_cd).join()
        };
      }
    },
    modalLeaveBoxCreate() {
      if (this.checkDateWasEdit()) {
        this.modalConfirmMove(() => {
          sessionStorage.removeItem('H02S05');
          this.showBoxCreate = true;
          this.resetModel();
          this.showBoxEdit = '';
          this.saveDateWasEdit('', false);
        });
      } else {
        this.showBoxCreate = true;
        this.resetModel();
        this.$refs['detailScrollTop'].scrollTop = 0;
      }
    },
    emitChangeEdit(mode) {
      this.emitter.emit('boxH02S05', {
        isBoxChange: mode
      });
    },
    /** Show A2 Confirm Button */
    showPersonConfirm(item) {
      const user_confirm = item.user_confirm_list?.find((data) => data.user_cd === this.user_login_cd);
      const flag = user_confirm?.confirmed_flag;
      return flag === 0 ? true : false;
    },
    /** Show A6 Edit/Delete */
    showEditDeleteBox(item) {
      const isPersonInCharges = this.personInCharges.some((s) => s.user_cd === this.user_login_cd);
      const isCreateUserCd = this.user_login_cd === item.create_user_cd;
      // const isUpdaloadBy = this.user_login_cd === item.updated_by;
      if (isPersonInCharges) {
        return true;
      } else {
        if (isCreateUserCd) {
          return true;
        } else {
          return false;
        }
      }
    },
    /** show All text */
    showDetail(item) {
      const ellipsis = document.getElementById(item?.person_consideration_id);
      const a = document.getElementById(`detail${item?.person_consideration_id}`);
      if (a.innerText === 'もっと見る') {
        ellipsis.classList.remove('ellipsis');
        a.innerText = '元に戻る';
      } else {
        a.innerText = 'もっと見る';
        ellipsis.classList.add('ellipsis');
        if (this.$refs['detailScrollTop']) {
          this.$refs['detailScrollTop'].scrollTop = this.$refs['detailScrollTop'].scrollTop * 0.9;
        }
      }
    },
    /** end */
    showBoxEditFunc(item) {
      this.modalLeaveBoxEdit(item);
    },
    showBoxCreateFunc() {
      this.modalLeaveBoxCreate();
    },
    closeCreate() {
      if (this.checkDateWasEdit()) {
        this.modalConfirmMove(() => {
          sessionStorage.removeItem('H02S05');
          this.personal = {
            consideration_type: '',
            consideration_contents: '',
            person_consideration_confirm: '',
            person_consideration_id: '',
            person_cd: '',
            consideration_type_name: ''
          };
          this.showBoxCreate = false;
          this.showBoxEdit = '';

          this.closeBoxEdit();
          this.saveDateWasEdit('', false);
          this.resetModel();
        });
      } else {
        this.showBoxCreate = false;
      }
    },
    closeBoxEdit(index) {
      if (this.checkDateWasEdit()) {
        this.index = index ? index : 0;
        this.modalConfirmMove(() => {
          sessionStorage.removeItem('H02S05');
          this.showBoxEdit = '';
          this.saveDateWasEdit('', false);
        });
      } else {
        this.showBoxEdit = '';
        sessionStorage.removeItem('H02S05');
        if (index) {
          let scrollDiv = document.getElementById(`move-${index}`)?.offsetTop;
          this.$refs['detailScrollTop'].scrollTop = scrollDiv;
        } else {
          this.$refs['detailScrollTop'].scrollTop = 0;
        }
      }
    },
    showPopup() {
      this.isShowPopupFilter = true;
    },
    closePopup() {
      this.isShowPopupFilter = false;
    },
    cancelPopup() {
      this.closePopup();
    },

    resetModel() {
      // reset model trong box
      this.paramsZ05S01.userCdList = [];
      this.paramsZ05S01.orgCdList = [];
      this.paramsZ05S01.userRawList = [];
      this.userList = [];
      this.showBoxEdit = '';
      this.personal = {
        consideration_type: '',
        consideration_contents: '',
        person_consideration_confirm: '',
        person_consideration_id: '',
        person_cd: '',
        consideration_type_name: ''
      };

      this.closePopup();
      this.resetData();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.show {
  display: block !important;
  top: 0px;
}
/** start css layout  **/
.groupPersonal {
  height: 100%;
  padding-top: 28px;
  background: #f2f2f2;
  @media (max-width: $viewport_tablet) {
    top: 16px;
  }
  .personalDetails {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .personalHead {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    padding: 12px 24px 0;
    .personalHead__user {
      width: 350px;
      padding-right: 10px;
      display: flex;
      align-items: flex-start;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding-right: 0;
      }
      .personalHead-item {
        min-width: 22px;
        padding: 4px 8px 0 0;
      }
      .personalHead-content {
        .personalHead-label {
          .name {
            font-size: 24px;
            font-weight: 700;
            line-height: 140%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 220px;
            display: inline-block;
          }
          .btn {
            &.btn-light {
              border-radius: 2px;
              width: 60px;
              height: 24px;
              padding: 0 6px;
              margin: 6px 0 0 20px;
            }
          }
        }
        .personalHead-text {
          font-size: 16px;
          line-height: 120%;
        }
      }
    }
    .personalHead__info {
      width: calc(100% - 350px);
      @media (max-width: $viewport_tablet) {
        width: 100%;
        padding: 10px 0 0;
      }
      ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        @media (max-width: $viewport_tablet) {
          justify-content: flex-start;
        }
        li {
          margin-right: 36px;
          display: flex;
          @media (max-width: $viewport_desktop) {
            margin-right: 20px;
          }
          &:last-child {
            margin-right: 0;
          }
          .item {
            min-width: 20px;
            margin: -2px 5px 0 0;
          }
        }
      }
    }
  }
  .personalTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-top: 12px;
    .navTabs--personal {
      li {
        min-width: 195px;
        @media (max-width: $viewport_desktop) {
          min-width: 140px;
        }
        @media (max-width: $viewport_tablet) {
          min-width: inherit;
        }
        .navInfo {
          @media (max-width: $viewport_desktop) {
            padding: 12px 8px;
          }
          @media (max-width: $viewport_tablet) {
            font-size: 0.75rem;
          }
        }
        .navItem {
          @media (max-width: $viewport_tablet) {
            min-width: 15px;
            margin-right: 6px;
          }
        }
        .navItem {
          .svg {
            @media (max-width: $viewport_tablet) {
              width: 15px;
            }
          }
        }
      }
    }
    .tab-content {
      background: #fff;
      height: 100%;
      box-shadow: 0px 0px 5px #b7c3cb;
      position: relative;
      z-index: 1;
      overflow: hidden;
      .tab-pane {
        height: 100%;
      }
    }
  }
}
/** end css layout **/
/*** start H02_S05_PersonalDetailsNotes ***/
.groupNotes {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  .groupNotes-filter {
    padding: 0 24px 10px;
    position: relative;

    .groupNotes-info {
      position: relative;
      display: flex;
      justify-content: flex-end;
      box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    }
    .btn {
      &.btn-filter {
        padding: 0;
        width: 42px;
        height: 42px;
        border-radius: 0px 0px 8px 8px;
      }
    }
    .dropdown-filter--Notes {
      width: 500px;
      .item-filter {
        background: #448add;
        box-shadow: 0px 2px 4px rgba(13, 94, 153, 0.1), 0.5px 1px 5px 1px rgba(203, 225, 241, 0.3);
        border-radius: 0px 0px 0px 8px;
        width: 42px;
        height: 42px;
        right: 0;
        top: 0;
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .title-filter {
        font-size: 1.125rem;
        font-weight: 700;
        color: #5f6b73;
        padding-bottom: 6px;
      }
      .formNotes {
        margin-bottom: 6px;
        .formNotes-label {
          color: #5f6b73;
          margin-bottom: 6px;
        }
      }
      .formNotes-control {
        align-items: center;
        .formNotes-col {
          width: 50%;
        }
        .formNotes-item {
          padding: 0 8px;
          color: #727f88;
          font-size: 1rem;
        }
      }
      .formNotes-checkBox {
        .formNotes-label {
          color: #5f6b73;
          margin-bottom: 6px;
        }
        ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: -20px;
          li {
            width: 33.333333%;
            padding-left: 20px;
            margin-top: 5px;
            .custom-control {
              width: 100%;
              .custom-control-description {
                white-space: nowrap;
                width: 100%;
              }
            }
          }
        }
      }
      .btnFilter-Notes {
        text-align: center;
        margin-top: 24px;
        .btn {
          margin-right: 14px;
          width: 142px;
          &:last-child {
            margin-right: 0;
          }
        }
      }
    }
  }
}
.groupDetail-notes {
  background: #f9f9f9;
  height: 100%;
  overflow: hidden;
  padding: 12px 0;
  padding-top: 0px;
  border-radius: 35px 35px 0 0;
  margin: 16px;
  .groupDetail-main {
    padding: 8px 24px;
    padding-right: 10px;
    height: calc(100% - 60px);
  }
  .w-500 {
    height: 100%;
  }
  .group-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -18px;
    padding-right: 15px;
    padding-bottom: 15px;
    .group-col {
      width: 50%;
      padding-left: 18px;
      @media (max-width: $viewport_tablet) {
        width: 100%;
        &:nth-child(2) {
          margin-top: 20px;
        }
      }
      .boxNotes {
        &:last-child {
          margin-bottom: 0;
        }
      }
    }
    &::-webkit-scrollbar {
      display: none;
    }
  }
  .boxNotes {
    margin-bottom: 20px;
    background: #f2f2f2;
    box-shadow: inset 0px -3px 10px rgba(0, 0, 0, 0.05), inset 0px 3px 10px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    .boxNotes-main {
      background: #ffffff;
      border: 0.5px solid #e3e3e3;
      box-sizing: border-box;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      border-radius: 4px;
    }
  }

  .hidden-create-box {
    animation-name: hiddenCreate;
    animation-duration: 1s;
  }
  @keyframes hiddenCreate {
    0% {
      opacity: 100%;
    }
    50% {
      opacity: 50%;
    }
    100% {
      opacity: 0%;
      display: none;
    }
  }
  @keyframes opacityCreate {
    0% {
      opacity: 0%;
    }
    50% {
      opacity: 50%;
    }
    100% {
      opacity: 100%;
    }
  }
  .addNew {
    padding: 55px 10px;
    text-align: center;
    cursor: pointer;
    a {
      font-size: 2.25rem;
      color: #448add;
      line-height: 140%;
      font-weight: 700;
    }
    .svg {
      position: relative;
      top: -4px;
    }
  }
  .notesPd {
    padding: 12px;
  }
  .formSelect {
    padding: 0 12px 12px;
    border-bottom: 1px solid #e3e3e3;
    .form-control-select-c {
      max-width: 200px;
    }
  }
  .formDestination {
    padding: 14px 12px 12px;
    border-bottom: 1px solid #e3e3e3;
    .formDestination-label {
      margin-bottom: 18px;
      font-weight: 700;
    }
  }
  .formTextarea {
    padding: 12px;
  }
  .formButton {
    text-align: right;
    padding-right: 12px;
    .btn {
      height: 32px;
      padding: 0 12px;
      margin-right: 20px;
      &.btn-outline-primary {
        .svg {
          width: 13px;
        }
      }
      &:first-of-type {
        font-weight: 500;
        font-size: 0.8125rem;
      }
      &:last-child {
        margin-right: 0;
      }
    }
  }
  .verification {
    display: flex;

    align-items: center;
    padding: 12px 24px;
    .verification-text {
      width: calc(100% - 80px);
      padding-right: 10px;
      font-weight: 700;
    }
    .verification-btn {
      .btn {
        height: 32px;
        font-size: 0.9375em;
      }
    }
  }
  .restrictions-head {
    border-bottom: 1px solid #e3e3e3;
    padding: 0 12px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    .restrictions-title {
      font-size: 1.125rem;
      font-weight: 700;
      padding-right: 10px;
    }
    .restrictions-info {
      text-align: right;
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          display: flex;
          margin-right: 22px;
          &:last-child {
            margin-right: 0;
          }
          .restrictions-item {
            min-width: 14px;
            margin: -2px 5px 0 0;
          }
        }
      }
    }
  }
  .restrictions-content {
    display: flex;
    flex-wrap: wrap;
    padding-top: 12px;
    .restrictions-text {
      width: calc(100% - 42px);
      padding-right: 20px;
      white-space: pre-line;

      p > .ellipsis {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        line-height: 20px;
        max-height: 200px;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
      }
    }
    .restrictions-tools {
      width: 42px;
      .planningStatus-btn {
        position: relative;
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
        width: 130px;
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
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
}
.no-data {
  padding-top: 10px;
  overflow-y: auto;
  height: 350px;
  text-align: center;
  .no-data-content {
    width: 100%;
    .no-data-text {
      font-size: 1rem;
    }
    .no-data-thumb {
      max-width: 400px;
      margin: 10px auto 0;
    }
  }
}
.more {
  color: #448add !important;
  cursor: pointer;
  &:hover {
    color: #1c63b9 !important;
    box-shadow: #b7c3cb;
  }
}
.btn-create-disabled {
  pointer-events: none;
}
.wrapBtn-note {
  margin-top: 15px;
  background: #f9f9f9;
  border-radius: 35px 35px 0 0;

  box-shadow: inset 0px 7px 3px -3px #e7e7e7;
  margin-top: 0;
}
.w-100 {
  width: 100%;
}
.btn-radius--ounline {
  &:hover {
    background: #ffffff !important;
    outline: 2px solid #76aff5 !important;
    color: #448add;
  }
}
.hide {
  display: none !important;
}
</style>
