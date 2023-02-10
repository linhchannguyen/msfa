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
              <div :class="[dropdownFilter + ' dropdown-filter--Notes', isShowPopupFilter ? 'show' : '']">
                <div class="item-filter btn-close-filter" @click="closePopup()">
                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
                </div>
                <h2 class="title-filter">検索条件</h2>
                <div class="groupFilter-notes">
                  <div class="formNotes">
                    <label class="formNotes-label">内容</label>
                    <div class="formNotes-control">
                      <el-input v-model="filter.content" clearable placeholder="内容入力" class="form-control-input" />
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
                            v-model="filter.start_date"
                            :disabled-date="disabledDateStart"
                            format="YYYY/M/D"
                            type="date"
                            :editable="false"
                            placeholder="開始日"
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
                            v-model="filter.end_date"
                            format="YYYY/M/D"
                            :disabled-date="disabledDateEnd"
                            type="date"
                            :editable="false"
                            placeholder="終了日"
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
                            value="30"
                            class="custom-control-input"
                            type="checkbox"
                            :checked="considerationTypes.includes('30')"
                            @change="OnConsiderationTypes($event, 2)"
                          />
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">院内情報入手先</span>
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
        <div ref="detailScroll" :class="[showBoxCreate || data.length > 0 ? 'group-row w-500' : 'group-row']" class="scrollbar">
          <div class="group-col">
            <!-- Card Left -->
            <div v-if="showBoxCreate" :class="['boxNotes', showBoxCreate ? 'create-box' : 'hidden-create-box']">
              <div class="boxNotes-main">
                <div class="notesPd">
                  <div class="formSelect" :class="isSubmit && !validation.consideration_type?.required ? 'hasErr' : ''">
                    <el-select v-model="facility.consideration_type" placeholder="未選択" class="form-control-select">
                      <el-option value=""> 未選択</el-option>
                      <el-option
                        v-for="item in options"
                        :key="item.consideration_type"
                        :label="item.consideration_type_name"
                        :value="item.consideration_type"
                        @click="checkValueData('consideration_type', facility.consideration_type)"
                      >
                      </el-option>
                    </el-select>
                    <div class="invalid-feedback">
                      <span v-if="isSubmit && !validation.consideration_type?.required">{{ validateMessages.consideration_type?.required }}</span>
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
                  <div class="formTextarea" :class="isSubmit && !validation.consideration_contents?.required ? 'hasErr' : ''">
                    <el-input
                      v-model="facility.consideration_contents"
                      type="textarea"
                      placeholder="内容入力"
                      class="form-control-textarea textareas"
                      @change="checkValueData('consideration_contents', facility.consideration_contents)"
                    />
                    <div class="invalid-feedback">
                      <span v-if="isSubmit && !validation.consideration_contents.required">{{ validateMessages?.consideration_contents.required }}</span>
                    </div>
                  </div>
                  <div class="formButton btnkeep">
                    <button
                      style="width: 132px; margin-right: 6px"
                      type="button"
                      class="btn btn-outline-primary btn-radius btn-outline-primary--cancel"
                      @click="closeCreate()"
                    >
                      <span class="btn-iconLeft">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit02.svg')" alt="" />
                      </span>
                      キャンセル
                    </button>
                    <button type="button" class="customBtn btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="onCreate()">
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
              <div v-if="showBoxEdit !== item?.facility_consideration_id" :class="[index % 2 == 0 ? 'boxNotes' : '']">
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
                        <div v-show="item.consideration_contents?.length > 0" class="restrictions-text">
                          <p v-if="item.consideration_contents?.length > 100 && !item?.readMore">
                            <span>{{ item.consideration_contents?.slice(0, 100) }}</span>
                            <span>...</span>
                            <a :id="'detail' + item.facility_consideration_id" class="more" @click="seeMore(item)">もっと見る</a>
                          </p>
                          <p v-else>
                            <span> {{ item.consideration_contents }} </span>
                            <a
                              v-if="item.consideration_contents?.length > 100"
                              :id="'detail' + item.facility_consideration_id"
                              class="more"
                              @click="hideMore(item, index)"
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
                                <li @click="showBoxEditFunc(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
                                  </span>
                                  <span class="text-label">編集</span>
                                </li>
                                <!--Delete-->
                                <li @click="onDelete(item)">
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
                      <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="userConfirm(item)">
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
              <div v-if="index % 2 == 0 && showBoxEdit == item.facility_consideration_id" class="boxNotes box-edit">
                <div class="boxNotes-main">
                  <div class="notesPd">
                    <div class="formSelect" :class="isSubmit && !validation.consideration_type.required ? 'hasErr' : ''">
                      <el-select v-model="facility.consideration_type" placeholder="未選択" class="form-control-select">
                        <el-option value=""> 未選択</el-option>
                        <el-option
                          v-for="item_option in options"
                          :key="item_option.consideration_type"
                          :label="item_option.consideration_type_name"
                          :value="item_option.consideration_type"
                          @click="checkValueData('consideration_type', facility.consideration_type)"
                        >
                        </el-option>
                      </el-select>
                      <div class="invalid-feedback">
                        <span v-if="isSubmit && !validation.consideration_type.required">{{ validateMessages?.consideration_type.required }}</span>
                      </div>
                    </div>
                    <div class="formDestination">
                      <label class="formDestination-label">確認依頼先</label>
                      <div class="formDestination-control">
                        <div class="form-icon icon-right">
                          <span class="icon log-icon" title_log="確認依頼先" @click="openModalZ05S01()">
                            <img class="svg" src="@/assets/template/images/icon_popup.svg" alt="" />
                          </span>
                          <div v-if="userList.length > 0" class="form-control d-flex align-items-center">
                            <div class="block-tags">
                              <el-tag
                                v-for="item_tag in userList"
                                :key="item_tag.code"
                                :label="item_tag.name"
                                :value="item_tag.name"
                                class="el-tag-custom"
                                closable
                                @close="handleRemoveUser(item_tag)"
                              >
                                {{ item_tag.name }}
                              </el-tag>
                            </div>
                          </div>
                          <el-input v-else clearable style="background: #ffffff" readonly placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </div>
                    <div class="formTextarea" :class="isSubmit && !validation.consideration_contents.required ? 'hasErr' : ''">
                      <el-input
                        v-model="facility.consideration_contents"
                        type="textarea"
                        placeholder="内容入力"
                        class="form-control-textarea textareas"
                        @change="checkValueData('consideration_contents', facility.consideration_contents)"
                      />
                      <div class="invalid-feedback">
                        <span v-if="isSubmit && !validation.consideration_contents.required">{{ validateMessages?.consideration_contents.required }}</span>
                      </div>
                    </div>
                    <div class="formButton btnkeep">
                      <button
                        style="width: 132px; margin-right: 6px"
                        type="button"
                        class="btn btn-outline-primary btn-radius btn-outline-primary--cancel"
                        @click="closeBoxEdit()"
                      >
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit02.svg')" alt="" />
                        </span>
                        キャンセル
                      </button>
                      <el-button
                        class="btn btn-outline-primary btn-radius btn-outline-primary--cancel btn-update"
                        :loading="loadingUpdate"
                        :disabled="loadingUpdate"
                        @click.prevent="onSave()"
                      >
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_save.svg')" alt="" />
                        保存
                      </el-button>
                    </div>
                  </div>
                </div>
              </div>
              <!--Updtae Box End-->
            </template>
            <!-- List left End -->
          </div>
          <!--Card Right-->
          <div v-if="data.length > 0" class="group-col">
            <!-- List Right -->
            <template v-for="(item, index) of data" :key="item">
              <div v-if="showBoxEdit !== item?.facility_consideration_id" :class="[index % 2 != 0 ? 'boxNotes' : '']">
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
                            <a :id="'detail' + item.facility_consideration_id" class="more" @click="seeMore(item)" @touchstart="seeMore(item)">もっと見る</a>
                          </p>
                          <p v-else>
                            <span> {{ item.consideration_contents }} </span>
                            <a
                              v-if="item.consideration_contents?.length > 100"
                              :id="'detail' + item.facility_consideration_id"
                              class="more"
                              @click="hideMore(item, index)"
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
                                <li @click="showBoxEditFunc(item)">
                                  <span class="item">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />
                                  </span>
                                  <span class="text-label">編集</span>
                                </li>
                                <!--Delete-->
                                <li @click="onDelete(item)">
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
                      <button type="button" class="btn btn-outline-primary btn-radius btn-outline-primary--cancel" @click="userConfirm(item)">
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
              <div v-if="index % 2 != 0 && showBoxEdit == item?.facility_consideration_id" class="boxNotes box-edit">
                <div class="boxNotes-main">
                  <div class="notesPd">
                    <div class="formSelect" :class="isSubmit && !validation.consideration_type.required ? 'hasErr' : ''">
                      <el-select v-model="facility.consideration_type" placeholder="未選択" class="form-control-select">
                        <el-option value=""> 未選択</el-option>

                        <el-option
                          v-for="item in options"
                          :key="item.consideration_type"
                          :label="item.consideration_type_name"
                          :value="item.consideration_type"
                          @click="checkValueData('consideration_type', facility.consideration_type)"
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
                          <span class="icon" @click="openModalZ05S01()">
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
                        v-model="facility.consideration_contents"
                        type="textarea"
                        placeholder="内容入力"
                        class="form-control-textarea textareas"
                        @change="checkValueData('consideration_contents', facility.consideration_contents)"
                      />
                      <div class="invalid-feedback">
                        <span v-if="isSubmit && !validation.consideration_contents.required">{{ validateMessages?.consideration_contents.required }}</span>
                      </div>
                    </div>
                    <div class="formButton btnkeep">
                      <button
                        style="width: 132px; margin-right: 6px"
                        type="button"
                        class="btn btn-outline-primary btn-radius btn-outline-primary--cancel"
                        @click="closeBoxEdit()"
                      >
                        <span class="btn-iconLeft">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_edit02.svg')" alt="" />
                        </span>
                        キャンセル
                      </button>
                      <el-button
                        class="btn btn-outline-primary btn-radius btn-outline-primary--cancel btn-update"
                        :loading="loadingUpdate"
                        :disabled="loadingUpdate"
                        @click.prevent="onSave()"
                      >
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_save.svg')" alt="" />
                        保存
                      </el-button>
                    </div>
                  </div>
                </div>
              </div>
              <!--Update Box End-->
            </template>
            <!--List Right End-->
          </div>
          <EmptyData v-if="data.length === 0 && !isLoadDefault && !showBoxCreate" custom-class="empty-facility-details-notes w-100" />
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
      @close="closeModalHeader()"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onResultModal"
          @handleConfirm="onCloseModal"
          @handleYes="resetInfor"
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
      @close="closeModalHeader()"
    >
      <template #default>
        <component :is="modalConfig1.component" v-bind="{ ...modalConfig1.props }" @onFinishScreen="onCloseModalDelete" @handleConfirm="deleteNote"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import H01_S04_FacilityDetailsNotesServices from '@/api/H01/H01_S04_FacilityDetailsNotes';
import Z05S01Organization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import validateMessages from '../../../utils/validateMessages/H01/H01_S04_FacilityDetailsNotes';
import { required } from '../../../utils/validate';
import { markRaw } from 'vue';
import filter_popup from '@/utils/mixin_filter_popup';

export default {
  name: 'H01_S04_FacilityDetailsNotes',
  components: { Z05S01Organization },
  mixins: [filter_popup],
  props: {
    checkLoading: [Boolean],

    changetab: {
      type: String,
      default: '4'
    },
    dropdownFilter: {
      type: String,
      default: ''
    },
    facility_name: {
      type: String,
      default: ''
    }
  },

  data() {
    return {
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
      loadingUpdate: false,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },

      modalConfig1: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      loading: false,
      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      },
      options: [],
      showBoxEdit: '',
      showBoxCreate: false,
      isLoadDefault: true,
      facility: {
        consideration_type: '',
        consideration_contents: '',
        user_cd: [],
        facility_consideration_id: '',
        facility_cd: ''
      },
      filter: {
        facility_cd: '',
        start_date: '',
        end_date: '',
        consideration_type: '10,20,30,40,50,99',
        content: ''
      },
      considerationTypes: ['10', '20', '30', '40', '50', '99'],
      user_login_cd: '',
      userList: [],
      userDeleteRaw: [],
      confirmation_request_delete: [],
      rawUserCdList: []
    };
  },
  computed: {
    validation() {
      return {
        consideration_type: { required: required(this.facility.consideration_type) },
        consideration_contents: { required: required(this.facility.consideration_contents) }
      };
    }
  },
  watch: {
    changetab: function (value) {
      // eslint-disable-next-line eqeqeq
      if (value == 3) {
        this.getNotes(true);
        this.getScreenData();
        this.showBoxEdit = '';
        this.showBoxCreate = false;
        this.saveDateWasEdit('', false);
      }
    }
  },
  created() {},
  mounted() {
    this.emitter.emit('change-header', {
      title: '施設詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.user_login_cd = this.getCurrentUser().user_cd;
    if (this._route('H01_FacilityDetails')) this.facility.facility_cd = this._route('H01_FacilityDetails').params.facility_cd;

    // eslint-disable-next-line eqeqeq
    if (this.changetab == 3) {
      this.getNotes(true);
      this.getScreenData();
      this.saveDateWasEdit('', false);
      const checkNull = (field) => (field ? field : '');
      let array = this.decodeData(localStorage.getItem('screen')) ? this.decodeData(localStorage.getItem('screen')) : [];

      const data = array.find((s) => s.screen_name === 'H01_S04_FacilityDetailsNotes');
      this.filter.start_date = data ? checkNull(data.filter?.start_date) : '';
      this.filter.end_date = data ? data.filter.end_date : '';
    }

    this.js_pscroll(0.5);
  },

  methods: {
    disabledDateStart(time) {
      if (this.filter.end_date) {
        return time.getTime() > new Date(this.filter.end_date).getTime();
      }
    },

    disabledDateEnd(time) {
      if (this.filter.start_date) {
        return time.getTime() < new Date(this.filter.start_date).getTime();
      }
    },
    saveDateWasEdit(swicth, isSave) {
      if (isSave) {
        sessionStorage.setItem('H01S04', swicth);
      } else {
        sessionStorage.removeItem('H01S04');
      }
    },
    checkDateWasEdit() {
      const data = sessionStorage.getItem('H01S04');
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
        destroyOnClose: true,
        isShowClose: true
      };
      this.changeTrueClassHeader();
    },
    /** Modal model edmit data for H02_S05 */
    onResultModal(e) {
      if (e && e.userSelected && e?.userSelected.length > 0) {
        this.paramsZ05S01.userCdList = [];
        this.paramsZ05S01.orgCdList = [];

        this.userList = e.userSelected.map((x) => ({ code: x.user_cd, name: x.user_name, org_cd: x.org_cd }));

        this.checkValueData('user', this.userList.join());
        this.rawUserCdList = this.userList.map((s) => s);
        this.facility.user_cd = this.rawUserCdList.length > 0 ? this.rawUserCdList.map((s) => ({ user_cd: s.code })) : [];

        this.paramsZ05S01.orgCdList = this.userList.map((x) => x.org_cd);
        this.paramsZ05S01.userCdList = this.userList.map((x) => ({ org_cd: x.org_cd, user_cd: x.code }));

        this.onCloseModal();
      } else {
        this.onCloseModal();
      }
    },
    handleRemoveUser(item) {
      this.userList = this.userList.filter((x) => x.code !== item.code);
      const userRawcd = this.userList.length > 0 ? this.userList.map((s) => ({ user_cd: s.code })) : [];
      this.facility.user_cd = userRawcd;
      const userDeleteRaw = this.userDeleteRaw.filter((s) => s.user_cd === item.code);
      this.confirmation_request_delete.push(userDeleteRaw[0]);
      // if (index !== -1) this.userDeleteRaw[index] = null;
      // this.paramsZ05S01.userCdList = this.userList.filter((s) => s.code !== item.code).map((s) => s.code);
      // this.paramsZ05S01.userRawList = this.paramsZ05S01.userRawList.filter((x) => x.code !== item.code);
      this.paramsZ05S01.orgCdList = this.userList.map((x) => x.org_cd);
      this.paramsZ05S01.userCdList = this.userList.map((x) => ({ org_cd: x.org_cd, user_cd: x.code }));
    },
    onCloseModal() {
      this.changeFalseClassHeader();

      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    resetInfor(e) {
      e.callBack();
      this.onCloseModal();
    },
    onCloseModalDelete() {
      this.changeFalseClassHeader();
      this.modalConfig1 = { ...this.modalConfig1, isShowModal: false, customClass: 'custom-dialog' };
    },
    closeModalHeader() {
      this.changeFalseClassHeader();
    },
    deleteNote() {
      this.deleted(this.facility);
    },
    /** Modal end */
    /** API Start*/
    created() {
      if (this._route('H01_FacilityDetails')) this.facility.facility_cd = this._route('H01_FacilityDetails').params.facility_cd;
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      this.loading = true;
      this.$emit('changeLoading', true);
      const body = {
        facility_cd: this.facility.facility_cd || localStorage.getItem('facility_cd_prev'),
        consideration_type: this.facility.consideration_type,
        consideration_contents: this.facility.consideration_contents,
        confirmation_request_destination: this.facility.user_cd,
        facility_short_name: this.facility_name
      };

      H01_S04_FacilityDetailsNotesServices.createNote(body)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.showBoxEdit = '';
          this.showBoxCreate = false;
          this.$notify({ message: message, customClass: 'success' });
          this.saveDateWasEdit('', false);
          localStorage.removeItem('DataResH01');
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.$emit('changeLoading', false), this.onCloseModal()));
    },
    updated() {
      if (this._route('H01_FacilityDetails')) this.facility.facility_cd = this._route('H01_FacilityDetails').params.facility_cd;
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        return;
      }
      this.loadingUpdate = true;
      const body = {
        facility_consideration_id: this.facility.facility_consideration_id,
        consideration_contents: this.facility.consideration_contents,
        confirmation_request_destination: this.facility.user_cd,
        consideration_type: this.facility.consideration_type,
        facility_cd: this.facility.facility_cd || localStorage.getItem('facility_cd_prev'),
        confirmation_request_delete: this.confirmation_request_delete,
        facility_short_name: this.facility_name
      };
      H01_S04_FacilityDetailsNotesServices.updateNote(body)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.showBoxEdit = '';
          this.$notify({ message: message, customClass: 'success' });
          this.saveDateWasEdit('', false);
          this.confirmation_request_delete = [];
          localStorage.removeItem('DataResH01');
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.$emit('changeLoading', false), this.onCloseModal(), (this.loadingUpdate = false)));
    },
    deleted(body) {
      const data = {
        facility_consideration_id: body.facility_consideration_id,
        facility_cd: this.facility.facility_cd || localStorage.getItem('facility_cd_prev'),
        confirmed_flag: body.confirmed_flag
      };

      H01_S04_FacilityDetailsNotesServices.deleteNote(data)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.$notify({ message: message, customClass: 'success' });
          this.saveDateWasEdit('', false);
          this.closePopup();
          localStorage.removeItem('DataResH01');
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.$emit('changeLoading', false), this.onCloseModal(), this.onCloseModalDelete()));
    },
    userConfirm(item) {
      const id = item.facility_consideration_id;
      const body = {
        facility_consideration_id: id,
        confirmed_flag: 1
      };
      H01_S04_FacilityDetailsNotesServices.updateConsiderationConfirm(body)
        .then((s) => {
          const message = s.data.message;
          this.getNotes(false);
          this.$notify({ message: message, customClass: 'success' });
          localStorage.removeItem('DataResH01');
          this.closePopup();
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.$emit('changeLoading', false), this.closePopup()));
    },
    getNotes(isLoadDefault) {
      this.loading = true;
      this.$emit('changeLoading', true);
      this.isLoadDefault = isLoadDefault;
      this.filter.start_date = this.formatFullDate(this.filter.start_date);
      this.filter.end_date = this.formatFullDate(this.filter.end_date);
      this.filter.facility_cd = this.facility.facility_cd ? this.facility.facility_cd : this.filter.facility_cd || localStorage.getItem('facility_cd_prev');
      H01_S04_FacilityDetailsNotesServices.fetchNotesService(this.filter)
        .then((s) => {
          if (s) {
            this.data = s.map((s) => ({ ...s, last_update_datetime: this.formatFullDate(s.last_update_datetime) }));
            this.personInCharges = [];
            this.resetModel();
            this.showBoxCreate = false;
            this.closePopup();
          }
        })
        .catch((err) => {
          const message = err.response.data.message;
          this.$notify({ message: message, customClass: 'error' });
          this.resetModel();
        })
        .finally(() => {
          // let content = document.querySelector('.groupNotes .groupDetail-notes .scrollbar');
          // content.scrollTop = 0;
          // content.scrollLeft = 0;

          if (this.$refs['detailScroll']) {
            this.$refs['detailScroll'].scrollTop = 0;
          }
          this.resetModel();
          this.loading = false;
          this.$emit('changeLoading', false);
          this.isLoadDefault = false;
        });
    },
    getScreenData() {
      H01_S04_FacilityDetailsNotesServices.getScreenDataService()
        .then((s) => {
          if (s) {
            const considerationType = s ? s : [];
            this.options = considerationType;
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    },
    /** API End*/
    /** Filter checkbox  ConsiderationTypes group*/
    getAnnouncementLable(data, field) {
      const arr = data.map((s) => s[field]);
      const result = arr.length > 1 ? arr.join('/') : arr.join();
      return result;
    },
    OnConsiderationTypes(event, index) {
      if (event.target.checked) {
        this.considerationTypes[index] = event.target.value;
      } else {
        this.considerationTypes[index] = '';
      }
      this.filter.consideration_type = this.considerationTypes.filter((s) => s !== '').join();
    },
    onCreate() {
      this.facility.user_cd = this.userList.map((s) => s.code);
      this.created();
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
      this.facility = data;
    },
    onSave() {
      this.updated();
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
    modalLeaveBoxEdit(item) {
      const id = item.facility_consideration_id;
      if (this.checkDateWasEdit()) {
        this.modalConfirmMove(() => {
          sessionStorage.removeItem('H01S04');
          this.showBoxCreate = false;
          this.showBoxEdit = id;
          this.showBoxCreate = false;
          this.userList = item?.announcement?.map((s) => ({ code: s.user_cd, name: s.user_name, org_cd: s.org_cd }));
          this.paramsZ05S01.userRawList = item?.announcement?.map((s) => ({ code: s.user_cd, name: s.user_name }));
          const facility_name = item.facility_name;
          this.facility = {
            consideration_type: item.consideration_type,
            facility_cd: this.facility_cd,
            facility_name,
            consideration_type_name: item.consideration_type_name,
            facility_consideration_id: item.facility_consideration_id,
            consideration_contents: item.consideration_contents,
            user_cd: item?.announcement?.map((s) => ({
              user_cd: s.user_cd
            }))
          };
          this.userDeleteRaw = item?.announcement?.map((s) => ({
            user_cd: s.user_cd
          }));
          this.paramsZ05S01.orgCdList = this.userList.map((x) => x.org_cd);
          this.paramsZ05S01.userCdList = this.userList.map((x) => ({ org_cd: x.org_cd, user_cd: x.code }));

          this.saveDateWasEdit('', false);
        });
      } else {
        this.showBoxEdit = id;
        this.showBoxCreate = false;
        this.userList = item?.announcement?.map((s) => ({ code: s.user_cd, name: s.user_name, org_cd: s.org_cd }));
        this.rawUserCdList = this.userList;
        this.paramsZ05S01.userRawList = item?.announcement?.map((s) => ({ code: s.user_cd, name: s.user_name }));
        const facility_cd = this.facility_cd;
        const facility_name = item.facility_name;
        this.facility = {
          consideration_type: item.consideration_type,
          facility_cd: facility_cd,
          facility_name,
          consideration_type_name: item.consideration_type_name,
          facility_consideration_id: item.facility_consideration_id,
          consideration_contents: item.consideration_contents,
          user_cd: item?.announcement?.map((s) => ({
            user_cd: s.user_cd,
            delete_flag: 0,
            change_flag: 0
          }))
        };
        this.userDeleteRaw = item?.announcement?.map((s) => ({
          user_cd: s.user_cd
        }));
        this.paramsZ05S01.orgCdList = this.userList.map((x) => x.org_cd);
        this.paramsZ05S01.userCdList = this.userList.map((x) => ({ org_cd: x.org_cd, user_cd: x.code }));
      }
    },
    modalLeaveBoxCreate() {
      if (this.checkDateWasEdit()) {
        this.modalConfirmMove(() => {
          sessionStorage.removeItem('H01S04');
          this.showBoxCreate = true;
          this.resetModel();
          this.showBoxEdit = '';
          this.saveDateWasEdit('', false);
        });
      } else {
        this.showBoxCreate = true;
        this.resetModel();
      }
    },
    /** Show A2 Confirm Button */
    showPersonConfirm(item) {
      const isUserConfirm = item.announcement?.some((data) => data.user_cd === this.user_login_cd && data.confirmed_flag === 0);
      return isUserConfirm ? true : false;
    },
    /** Show A6 Edit/Delete */
    showEditDeleteBox(item) {
      const isCreateUserCd = this.user_login_cd === item.create_user_cd; // chính là ng tạo note
      const isUpdaloadBy = this.user_login_cd === item.updated_by; // ng update cuối cùng
      const isFacility = item.m_facility_user.some((s) => s.user_cd === this.user_login_cd); //check m_facility_user có user_cd_login hay k
      if (isFacility) {
        return true;
      } else {
        if (isUpdaloadBy && !isCreateUserCd) {
          return true;
        } else if (isCreateUserCd) {
          return true;
        } else {
          return false;
        }
      }
    },
    /** show All text */
    showDetail(item) {
      const ellipsis = document.getElementById(item?.facility_consideration_id);
      const a = document.getElementById(`detail${item?.facility_consideration_id}`);
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

    showBoxEditFunc(item) {
      this.modalLeaveBoxEdit(item);
    },
    showBoxCreateFunc() {
      this.modalLeaveBoxCreate();
    },
    seeMore(item) {
      item.readMore = true;
    },
    hideMore(item, index) {
      let scrollDiv = document.getElementById(`move-${index}`).offsetTop;
      this.$refs['detailScroll'].scrollTop = scrollDiv;
      item.readMore = false;
    },
    closeBoxEdit() {
      if (this.checkDateWasEdit()) {
        this.modalConfirmMove(() => {
          this.showBoxEdit = '';
          sessionStorage.removeItem('H01S04');
        });
      } else {
        this.showBoxEdit = '';
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
    resetFilter() {
      this.filter = {
        start_date: '',
        end_date: '',
        consideration_type: '',
        contents: '',
        facility_cd: ''
      };
    },
    closeCreate() {
      if (this.checkDateWasEdit()) {
        this.modalConfirmMove(() => {
          sessionStorage.removeItem('H01S04');
          this.facility = {
            consideration_type: '',
            consideration_contents: '',
            user_cd: '',
            facility_consideration_id: '',
            facility_cd: '',
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
    resetModel() {
      // reset model trong box
      this.paramsZ05S01.userCdList = [];
      this.paramsZ05S01.orgCdList = [];
      this.paramsZ05S01.userRawList = [];
      this.userList = [];
      this.showBoxEdit = '';
      this.facility = {
        consideration_type: '',
        consideration_contents: '',
        user_cd: '',
        facility_consideration_id: '',
        facility_cd: '',
        consideration_type_name: ''
      };

      this.resetData();
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.btnkeep {
  text-align: right;
  cursor: pointer;
  .btn {
    width: 100px;
    height: 32px;
    padding: 0;
    .svg {
      width: 13px;
    }
  }
}
.show {
  display: block !important;
  top: 0px;
}
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 6%;
    top: 30%;
  }
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
  padding: 10px;
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
            width: 50%;
            padding-left: 20px;
            margin-top: 5px;
            .custom-control {
              width: 100%;
              .custom-control-description {
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
  margin-top: 10px;
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
    overflow: auto;
    padding-right: 15px;
    padding-bottom: 15px;
    column-gap: 20px;
    @media (max-width: $viewport_tablet) {
      flex-direction: column;
      flex-wrap: unset;
    }
    .group-col {
      width: calc(50% - 10px);
      @media (max-width: $viewport_tablet) {
        width: 100%;
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
    .form-control-select {
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
      margin-right: 20px;
      &.btn-outline-primary {
        .svg {
          width: 13px;
        }
      }
      &:first-of-type {
        font-weight: 700;
        font-size: 0.9375rem;
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
      width: 180px;
      text-align: right;
      .btn {
        height: 32px;
        padding: 0 12px;
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
.ellipsis {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  line-height: 20px;
  max-height: 200px;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
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
.btn-update {
  padding: 0.375rem 0.75rem;
}
.textareas {
  width: 100%;
  min-height: 60px;
  border-radius: 4px;
  background: rgb(255, 255, 255);
  &:focus-visible {
    outline: none;
  }
}
</style>
