/* eslint-disable no-case-declarations */
<template>
  <div v-loading="loading" class="wrapContent">
    <div class="groupAtten">
      <div class="wrapAtten">
        <div class="attenHead">
          <div class="attenHead-left">
            <span class="title" style="font-size: 22px">{{ formatTitle(convention_info.convention_name) }}</span>
            <span class="dateTime">
              {{ formatFullDate(convention_info.start_time) }}({{ getDay(convertDate(convention_info.start_time)) }})　
              {{ formatTimeHourMinute(convention_info.start_time) }}
              {{ JapaneseTilde() }} {{ formatTimeHourMinute(convention_info.end_time) }}</span
            >
            <span
              class="spanLabel"
              :style="{ background: checkColor(convention_info.status_type).background, color: checkColor(convention_info.status_type).color }"
              >{{ convention_info.definition_label }}</span
            >
          </div>
          <div class="attenHead-right">
            <span class="attenHead-item">品目</span>
            <span class="attenHead-label">{{ convention_info.product_name }}</span>
          </div>
        </div>
        <div class="attenContent">
          <div class="attenDirection">
            <div class="facilityStaff">
              <div class="facility-form">
                <ul>
                  <li class="facility-flex">
                    <label style="color: #5f6b73" class="facility-label">施設担当者</label>
                    <div class="form-icon icon-right" style="width: 198px">
                      <span class="icon" @click="openModal_Z05_S01">
                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                      </span>
                      <div v-if="user_name.length > 0" class="form-control d-flex align-items-center">
                        <div class="block-tags">
                          <el-tag v-for="(item, index) of user_name" :key="item" class="m-0 el-tag-custom" closable @close="handleRemoveUser(index)">
                            {{ item }}
                          </el-tag>
                        </div>
                      </div>
                      <el-input v-if="user_name.length === 0" readonly clearable placeholder="未選択" class="form-control-input" />
                    </div>
                  </li>
                  <li>
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input v-model="attendance" class="custom-control-input" type="checkbox" @change="changeFilter(1)" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">出席予定者のみ</span>
                    </label>
                  </li>
                  <li>
                    <label class="custom-control custom-checkbox custom-control--bordGreen">
                      <input v-model="unfollow" class="custom-control-input" type="checkbox" @change="changeFilter(2)" />
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">未フォローのみ</span>
                    </label>
                  </li>
                  <li v-if="checkRoleButtonC && buttonSave" class="display-attendeeBox-li">
                    <button v-if="!checkAddStock" type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="openEx">
                      <span class="btn-iconLeft">
                        <span class="icon">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user-check.svg')" alt="" />
                        </span>
                      </span>
                      一括登録
                    </button>
                  </li>
                  <li v-if="checkRoleButtonD" class="display-attendeeBox-li">
                    <button
                      v-if="!checkAddStock"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                      @click="modalOtherPersonalAddition()"
                    >
                      <span class="btn-iconLeft">
                        <span class="icon"><img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" /></span>
                      </span>
                      その他個人追加
                    </button>
                  </li>
                  <li v-if="checkRoleButtonD" class="display-attendeeBox-li">
                    <button
                      v-if="!checkAddStock"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                      @click="openModalZ05S04()"
                    >
                      <span class="btn-iconLeft">
                        <span class="icon"><img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" /></span>
                      </span>
                      施設個人追加
                    </button>
                  </li>
                  <li v-if="checkRoleButtonE">
                    <button v-if="!checkAddStock" type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="openA02S01">
                      <span class="btn-iconLeft">
                        <span class="icon"><img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack02.svg')" alt="" /></span>
                      </span>
                      ストック登録
                    </button>
                  </li>
                </ul>
              </div>
              <div v-if="!checkAddStock && ((checkRoleButtonC && buttonSave) || checkRoleButtonD)" class="display-attendeeBox">
                <div class="attendeeBox-btn">
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
                      <li v-if="checkRoleButtonC && buttonSave" class="log-icon" @click="openEx" @touchstart="openEx">
                        <span class="item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_user-check.svg')" alt="" />
                        </span>
                        <span class="text-label">一括登録</span>
                      </li>
                      <li v-if="checkRoleButtonD" class="log-icon" @click="modalOtherPersonalAddition()" @touchstart="modalOtherPersonalAddition()">
                        <span class="item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" />
                        </span>
                        <span class="text-label">その他個人追加</span>
                      </li>
                      <li v-if="checkRoleButtonD" class="log-icon" @click="openModalZ05S04()" @touchstart="openModalZ05S04()">
                        <span class="item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus01.svg')" alt="" />
                        </span>
                        <span class="text-label">施設個人追加</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <!--  -->
            <Transition name="fade-slide" mode="out-in" duration="250" @enter="handleEnter($event)" @leave="handleLeave($event)">
              <div v-if="!checkAddStock" class="slide-1">
                <div class="attenTabel">
                  <div ref="tblUser" class="attenTbl table-hg100 scrollbar" @scroll="onScroll">
                    <div v-if="dataZ05S04.length > 0" class="application-btn">
                      <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
                        <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
                      </button>
                      <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
                        <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
                      </button>
                    </div>

                    <table>
                      <thead>
                        <tr>
                          <th>施設名/医師名</th>
                          <th>役割</th>
                          <th>前回案内</th>
                          <th>前回出欠</th>
                          <th>企画時出席予定</th>
                          <th>案内</th>
                          <th>出欠予定</th>
                          <th>宿泊</th>
                          <th>旅券</th>
                          <th>チケット</th>
                          <th>出席</th>
                          <th>情報交換会</th>
                          <th>慰労会</th>
                          <th>フォロー</th>
                        </tr>
                        <tr>
                          <td>
                            <span class="attenTbl-total">合計</span>
                            <button
                              v-if="buttonSave"
                              type="button"
                              class="btn btn-outline-primary btn-outline-primary--cancel btn-radius"
                              @click="openModalC01S03"
                            >
                              <span class="btn-iconLeft">
                                <span class="icon">
                                  <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_gridicons.svg')" alt="" />
                                </span>
                              </span>
                              その他出席者数入力
                            </button>
                          </td>
                          <td></td>
                          <td>{{ dataCaculationHead.lastTimeInformation }}人</td>
                          <td>出席：{{ dataCaculationHead.lastTimeAttendance }}人</td>
                          <td>{{ dataCaculationHead.scheduledToAttendAtTheTimeOfPlanning }}人</td>
                          <td>{{ dataCaculationHead.guidance }}人</td>
                          <td>出席：{{ dataCaculationHead.scheduledToAttend }}人</td>
                          <td>{{ dataCaculationHead.stay }}人</td>
                          <td>{{ dataCaculationHead.passport }}人</td>
                          <td>{{ dataCaculationHead.ticket }}人</td>
                          <td>{{ dataCaculationHead.attendance }}人</td>
                          <td>{{ dataCaculationHead.informationExchangeMeeting }}人</td>
                          <td>{{ dataCaculationHead.consolationParty }}人</td>
                          <td>{{ dataCaculationHead.follow }}人</td>
                        </tr>
                      </thead>
                      <tbody v-if="totalRecord > 0">
                        <tr v-for="(item, indexDataZ05S04) of dataZ05S04" :key="indexDataZ05S04">
                          <template v-if="item.deleteFlag !== 1">
                            <td v-if="item.deleteFlag !== 1" :class="item.deleteFlag === 1 ? '' : 'height-100'">
                              <div style="display: flex; align-items: center; justify-content: space-between">
                                <span>
                                  <p class="attenTbl-title">{{ item.facility_short_name }}　{{ item.department_name }}</p>
                                  <p
                                    v-if="item.checkClickPerson || item.other_person_flag !== 1"
                                    class="attenTbl-label"
                                    style="display: flex; align-items: center; position: relative"
                                  >
                                    <span class="link attenTbl-name" @click="personDetail(item.person_cd)"> {{ item.person_name }} </span>　<span
                                      >{{ item.display_position_name }}
                                    </span>
                                  </p>
                                  <p v-else class="attenTbl-label" style="display: flex; align-items: center">
                                    <span class="attenTbl-name"> {{ item.person_name }} </span>　{{ item.display_position_name }}
                                  </p>
                                </span>
                                <span class="stockRegis-btn" style="margin-left: 10px">
                                  <button
                                    v-if="buttonSave"
                                    class="btn btn--icon"
                                    @click="deletedataZ05S04(item, indexDataZ05S04, item.checkClickPerson || item.other_person_flag !== 1 ? true : false)"
                                  >
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                                  </button>
                                </span>
                              </div>
                            </td>
                            <td v-if="item.deleteFlag !== 1">
                              <el-select
                                v-model="item.part_type"
                                :disabled="checkRoleTable"
                                placeholder="未選択"
                                class="form-control-select"
                                @change="changeSelect()"
                              >
                                <el-option label="" value="">未選択</el-option>
                                <el-option v-for="item2 in D14s" :key="item2" :label="item2.definition_label" :value="item2.definition_value"> </el-option>
                              </el-select>
                            </td>
                            <td v-if="item.deleteFlag !== 1">{{ item.series_convention_200 }}</td>
                            <td v-if="item.deleteFlag !== 1">{{ item.series_convention_700 }}</td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 0)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  :checked="item.convention_attendee_status_detail[0].status_item_value === '1' ? true : false"
                                  class="custom-control-input custom-control-input-3"
                                  type="checkbox"
                                  @change.stop="signalChange(indexDataZ05S04, 0)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[0].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[0].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 1)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  class="custom-control-input custom-control-input-2"
                                  type="checkbox"
                                  :checked="item.convention_attendee_status_detail[1].status_item_value === '1' ? true : false"
                                  @change.stop="signalChange(indexDataZ05S04, 1)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[1].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[1].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1">
                              <el-select
                                v-model="item.convention_attendee_status_detail[2].status_item_value"
                                :disabled="checkRoleTable"
                                placeholder="未選択"
                                class="form-control-select"
                                @change="changeSelect()"
                              >
                                <el-option label="" value="">未選択</el-option>
                                <el-option v-for="item3 in D18s" :key="item3" :label="item3.definition_label" :value="item3.definition_value"> </el-option>
                              </el-select>
                            </td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 3)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  class="custom-control-input custom-control-input-2"
                                  type="checkbox"
                                  :checked="item.convention_attendee_status_detail[3].status_item_value === '1' ? true : false"
                                  @change.stop="signalChange(indexDataZ05S04, 3)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[3].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[3].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 4)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  class="custom-control-input custom-control-input-2"
                                  type="checkbox"
                                  :checked="item.convention_attendee_status_detail[4].status_item_value === '1' ? true : false"
                                  @change.stop="signalChange(indexDataZ05S04, 4)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[4].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[4].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 5)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  class="custom-control-input custom-control-input-2"
                                  type="checkbox"
                                  :checked="item.convention_attendee_status_detail[5].status_item_value === '1' ? true : false"
                                  @change.stop="signalChange(indexDataZ05S04, 5)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[5].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[5].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 6)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  class="custom-control-input custom-control-input-2"
                                  type="checkbox"
                                  :checked="item.convention_attendee_status_detail[6].status_item_value === '1' ? true : false"
                                  @change.stop="signalChange(indexDataZ05S04, 6)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[6].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[6].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 7)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  class="custom-control-input custom-control-input-2"
                                  type="checkbox"
                                  :checked="item.convention_attendee_status_detail[7].status_item_value === '1' ? true : false"
                                  @change.stop="signalChange(indexDataZ05S04, 7)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[7].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[7].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1" @click.stop="signalChange(indexDataZ05S04, 8)">
                              <label class="custom-control custom-checkbox">
                                <input
                                  :disabled="checkRoleTable"
                                  class="custom-control-input custom-control-input-2"
                                  type="checkbox"
                                  :checked="item.convention_attendee_status_detail[8].status_item_value === '1' ? true : false"
                                  @change.stop="signalChange(indexDataZ05S04, 8)"
                                />
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">{{
                                  item.convention_attendee_status_detail[8].status_item_value === '1'
                                    ? formatMonthDate(item.convention_attendee_status_detail[8].update_date)
                                    : ''
                                }}</span>
                              </label>
                            </td>
                            <td v-if="item.deleteFlag !== 1">
                              <span v-if="item.follow_date !== 'ー'"> {{ formatMonthDate(item.follow_date) }} <br />済 </span>
                              <span v-if="item.follow_date === 'ー'"> {{ item.follow_date }} </span>
                            </td>
                          </template>
                        </tr>
                      </tbody>
                      <tr v-if="!totalRecord">
                        <td colspan="16">
                          <div class="noData">
                            <div class="noData-content">
                              <p v-if="!loading" class="noData-text">該当するデータがありません。</p>
                              <div v-if="!loading" class="noData-thumb">
                                <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="attenPagination">
                  <div class="attenPagination-btn">
                    <button :disabled="loadingSubmitAtten" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="cancel">
                      キャンセル
                    </button>
                    <button
                      v-if="checkRoleButtonExcel"
                      :disabled="loadingSubmitAtten"
                      type="button"
                      class="btn btn-outline-primary btn-outline-primary--cancel"
                      @click="exEcel"
                    >
                      出席者一覧出力
                    </button>
                    <button v-if="buttonSave" :disabled="loadingSubmitAtten" type="button" class="btn btn-primary customBtn" @click="submit">保存</button>
                  </div>
                  <div v-if="totalRecord > 0" class="attenPagination-number">
                    <div class="pagination-cases">全 {{ totalRecord }} 件</div>
                    <PaginationTable
                      :page-size="100"
                      layout="prev, pager, next"
                      :total="totalRecord"
                      :current-page="currentPage"
                      @current-change="handleCurrentChange"
                    />
                  </div>
                </div>
              </div>

              <div v-else-if="checkAddStock" class="slide-2">
                <div class="attenStock">
                  <div class="attenStock-colLeft">
                    <div class="lstFacility">
                      <div class="groupFacility">
                        <div class="boxFacility">
                          <div class="boxHeader">
                            <h2 class="tlt">施設名/個人名</h2>
                            <p class="total">合計</p>
                          </div>
                          <div v-if="dataA01S02.length > 0" ref="tblUser2" class="listStock scrollbar">
                            <ul>
                              <li v-for="item of dataA01S02" :key="item">
                                <div class="listStock-content">
                                  <p class="listStock-title">{{ item.facility_short_name }}　{{ item.department_name }}</p>
                                  <p v-if="item.other_person_flag !== 1" class="listStock-label">
                                    <span class="link listStock-name" @click="personDetail(item.person_cd)">{{ item.person_name }}</span
                                    >{{ item.display_position_name }}
                                  </p>
                                  <p v-if="item.other_person_flag === 1" class="listStock-label">
                                    <span class="listStock-name">{{ item.person_name }}</span
                                    >{{ item.display_position_name }}
                                  </p>
                                </div>
                                <div v-if="item.other_person_flag !== 1" class="listStock-btn">
                                  <button v-if="item.isAdd === false" class="btn btn--icon" @click="Add(true, item)">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack.svg')" alt="" />
                                  </button>
                                  <button v-if="item.isAdd === true" class="btn btn--icon" @click="deleteAdd(item)">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_stack01.svg')" alt="" />
                                  </button>
                                </div>
                              </li>
                            </ul>
                          </div>
                          <div v-if="dataA01S02.length <= 0" class="noData">
                            <div class="noData-content">
                              <p class="noData-text">該当するデータがありません。</p>
                              <div class="noData-thumb">
                                <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="dataA01S02.length > 0" class="paginationFacility">
                      <div class="pagination-cases">全 {{ totalRecord2 }} 件</div>
                      <PaginationTable
                        :page-size="100"
                        layout="prev, pager, next"
                        :total="totalRecord2"
                        :current-page="currentPage2"
                        @current-change="handleCurrentChange2"
                      />
                    </div>
                  </div>
                  <div class="attenStock-colRight">
                    <div class="stockRegis">
                      <div class="stockRegis-title">
                        <h2 class="title">ストック登録</h2>
                        <span :class="loadingStock ? 'disabled' : ''" class="closeUp link" @click="closeA02S01">閉じる</span>
                      </div>
                      <div class="stockRegis-content">
                        <div class="stockRegis-box">
                          <div v-if="dataCheckA02S01.length > 0" class="stockRegis-lst scrollbar">
                            <ul v-for="item of dataCheckA02S01" :key="item">
                              <li v-if="item.isAdd === true">
                                <div class="stockRegis-info">
                                  <p class="stockRegis-tlt">{{ item.facility_short_name }}　{{ item.department_name }}</p>
                                  <p class="stockRegis-label">
                                    <span class="stockRegis-name">{{ item.person_name }}</span
                                    >　{{ item.display_position_name }}
                                  </p>
                                </div>
                                <div class="stockRegis-btn">
                                  <button class="btn btn--icon" @click="deleteAdd(item)">
                                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_close.svg')" alt="" />
                                  </button>
                                </div>
                              </li>
                            </ul>
                          </div>
                          <div v-if="dataCheckA02S01.length <= 0" class="noData">
                            <div class="noData-content">
                              <p class="noData-text">該当するデータがありません。</p>
                              <div class="noData-thumb">
                                <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                              </div>
                            </div>
                          </div>
                          <div class="stockRegis-form">
                            <div class="formGroup">
                              <ul>
                                <li>
                                  <label class="formGroup-label">面談内容</label>
                                  <div class="formGroup-control">
                                    <el-select
                                      v-model="contentA01S02"
                                      :disabled="dataCheckA02S01.length === 0"
                                      placeholder="未選択"
                                      class="form-control-select"
                                    >
                                      <el-option label="" value="">未選択 </el-option>
                                      <el-option
                                        v-for="item in dataSelect"
                                        :key="item"
                                        :label="item.content_name"
                                        :value="item.content_cd"
                                        :class="item.content_name === contentA01S02 ? 'selected hover' : ''"
                                      >
                                      </el-option>
                                    </el-select>
                                  </div>
                                </li>
                                <li>
                                  <label class="formGroup-label">品目</label>
                                  <div class="formGroup-control">
                                    <div class="form-icon icon-right">
                                      <span class="icon" @click="openModalZ05S06">
                                        <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                                      </span>
                                      <div v-if="dataProduct.length > 0" class="form-control d-flex align-items-center">
                                        <div class="block-tags">
                                          <el-tag
                                            v-for="(item, index) in dataProduct"
                                            :key="item"
                                            :disabled="dataCheckA02S01.length === 0"
                                            class="m-0 el-tag-custom"
                                            closable
                                            @close="handleRemove(index)"
                                          >
                                            {{ item.product_name }}
                                          </el-tag>
                                        </div>
                                      </div>
                                      <el-input
                                        v-else
                                        clearable
                                        readonly
                                        :disabled="dataCheckA02S01.length === 0"
                                        placeholder="未選択"
                                        class="form-control-input"
                                      />
                                    </div>
                                  </div>
                                </li>
                              </ul>
                            </div>
                            <div class="formGroup-btn">
                              <span
                                :class="loadingStock ? 'disabled' : ''"
                                class="closeUp btn btn-outline-primary btn-outline-primary--cancel"
                                @click="closeA02S01"
                              >
                                キャンセル</span
                              >
                              <button
                                :disabled="dataCheckA02S01.length === 0 || loadingStock"
                                type="button"
                                class="btn btn-primary customBtn"
                                @click="checkSubmit"
                              >
                                <i :class="['el-icon-loading', loadingStock ? 'inline-block' : '']"></i> 登録
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--  -->
            </Transition>
          </div>
        </div>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="`${modalConfig.customClass} handle-close`"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig?.destroyOnClose"
      :close-on-click-modal="modalConfig?.closeOnClickMark"
      :show-close="modalConfig.isShowClose"
      :before-close="handleBeforeClose"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          ref="modalChild"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreenC01S03="onCloseModalC01S03"
          @onFinishScreen="onResultModal"
          @onFinishScreenMOPA="onFinishScreenMOPA"
          @handleYes="handleYes"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
// import PaginationTable from '@/components/PaginationTable';
import Z05S04FacilityCustomerSelection from '../../Z05/Z05_S04_FacilityCustomerSelection/Z05_S04_FacilityCustomerSelection.vue';
import Z05S01ModalOrganization from '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization';
import C01S03AttendantManagementModal from './C01_S03_AttendantManagementModal.vue';
import C01_S03_AttendantManagement from '../../../api/C01/C01_S03_AttendantManagement';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';
import { markRaw } from 'vue';
import Z05_S06_Material_Selection from '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection';
import A02S01Stock from '../../A02/A02_S01_Stock/_A02_S01_Stock.vue';
import ModalOtherPersonalAddition from './ModalOtherPersonalAddition.vue';
import C01_S05_AttendantCollectiveRegistration from '../C01_S05_AttendantCollectiveRegistration/C01_S05_AttendantCollectiveRegistration.vue';
import { Auth } from '@/api';
import moment from 'moment';
import _ from 'lodash';

export default {
  name: 'C01_S03_AttendantManagement',
  components: {
    Z05S04FacilityCustomerSelection,
    ModalOtherPersonalAddition,
    Z05S01ModalOrganization,
    C01S03AttendantManagementModal,
    A02S01Stock,
    Z05_S06_Material_Selection,
    C01_S05_AttendantCollectiveRegistration
  },
  props: {
    conventionId: {
      type: Number,
      default: 0
    },
    checkLoading: [Boolean]
  },
  data() {
    return {
      dataCheckA02S01: [],
      isFilter: false,
      isFilterCheckBox: false,
      buttonSave: false,
      loadingSubmitAtten: false,
      loadingStock: false,
      convention_info: {},
      convention_id: '',
      dataZ05S04: [],
      dataZ05S04Hide: [],
      other_person_flag: 0,
      user_cd: [],
      org_cd: '',
      user_name: [],
      //page C01-S03
      totalRecord: 0,
      currentPage: 1,
      total_pages: 0,
      //page A01-S02
      totalRecord2: 0,
      currentPage2: 1,
      total_pages2: 0,
      otherAttendee: [],
      other_attendees: [],
      checkData: false,
      medical_staff: [],
      dataProduct: [],
      dataA01S02: [],
      D14s: [],
      D18s: [],
      actionIdScreen: '',
      stockTypeScreen: '',
      // stockExistScreen: '',
      // checkDisableBTNEdit: true,
      isOrgListTarget: false,
      arrCheck: [],
      loading: false,
      attendance: false,
      unfollow: false,
      contentA01S02: '',
      checkAddStock: false,
      definition_valueD14: '',
      definition_valueD18: '',
      checkRoleTable: true,
      checkRoleButtonD: false,
      checkRoleButtonC: false,
      checkRoleButtonE: false,
      checkRoleButtonExcel: false,
      planOrgCd: '',
      planUserCd: '',
      userLoginOrgCd: '',
      inputdeadline: '',
      file: null,
      checkBtnStock: true,
      dataCaculationHead: {
        scheduledToAttendAtTheTimeOfPlanning: 0,
        guidance: 0,
        scheduledToAttend: 0,
        stay: 0,
        passport: 0,
        ticket: 0,
        attendance: 0,
        informationExchangeMeeting: 0,
        consolationParty: 0
      },
      lastTimeInformation: 0,
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      props: {
        mode: 'single',
        categoryCode: '',
        classificationCode: '',
        initDataCodes: []
      },
      paramZ05S04: {
        non_facility_flag: 0, // req
        non_medical_flag: 1, //req
        org_cd: '',
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
      CheckDayBefore3Months: false,
      theFistData: 0,
      changeSelectInput: false,
      checkClickPersonDetail: null,
      checkUserCd: false,
      userLogin: {},
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      addStock: false,
      planned: ['計画入力中', '計画承認待ち', '計画承認済'],
      Performed: ['結果入力中', '結果承認待ち', '結果承認済'],
      contentA01S02Id: '',
      removeUserIndex: false,
      isChangePage: null,
      orgCdList: [],
      userCdList: []
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '出席者管理',
      icon: 'icon-c01.svg',
      isShowBack: true
    });
    localStorage.removeItem('checkChangTab');
    this.convention_id = this._route('C01_S03_AttendantManagement') ? this._route('C01_S03_AttendantManagement').params.convention_id : '' || this.conventionId;
    this.js_pscroll(0.4);
    this.theFirst();
    this.userLogin = this.getCurrentUser();
  },
  methods: {
    formatTitle(title) {
      if (title) {
        return title.length > 50 ? `${title.substring(0, 50)}...` : title;
      }
    },
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    convertDate(date) {
      return moment(date).format('YYYY-MM-DDTHH:mm:ss');
    },
    async theFirst() {
      this.loading = true;
      let user_cd = this.userLogin.user_cd;

      const permission = await Auth.getPolicyRoleService({ user_cd });
      this.listRole = permission?.data?.data;
      if (permission?.data?.data?.includes('R30')) {
        this.checkUserCd = true;
      }

      if (this.checkUserCd) {
        this.user_cd.push(this.userLogin.user_cd);
        this.user_name.push(this.userLogin.user_name);

        this.orgCdList = [this.userLogin.org_cd];
        this.org_cd = this.userLogin.org_cd;
        this.userCdList = [{ org_cd: this.userLogin.org_cd, user_cd: this.userLogin.user_cd }];
      }
      this.getScreenData();
      this.getDataHide();
    },
    onScrollLeft() {
      let content = document.querySelector('.attenTbl');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.attenTbl');
      content.scrollLeft += 200;
    },
    onScroll: _.debounce(function ({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }
    }, 300),
    exEcel() {
      this.loading = true;
      var FileSaver = require('file-saver');
      let params = {
        convention_id: this.convention_id,
        user_cd: this.user_cd,
        attendance: this.attendance ? 1 : 0,
        unfollow: this.unfollow ? 1 : 0
      };
      C01_S03_AttendantManagement.expFile(params)
        .then((res) => {
          let data = res.data;
          let fileName = `出席者一覧_${this.convention_info.convention_name}_${this.$moment(this.convention_info.start_time).format('YYYYMMDD')}.xlsx`;
          var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8' });
          FileSaver.saveAs(blob, `${fileName}`);
          this.loading = false;
        })
        .catch(async (err) => {
          let blob = err.response.data;
          let responseText = JSON.parse(await blob.text());
          this.$notify({ message: responseText.message, customClass: 'error' });
        })
        .finally(() => {
          this.loading = false;
        });
    },
    openEx() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '出席者一括登録',
        isShowModal: true,
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        component: markRaw(C01_S05_AttendantCollectiveRegistration),
        width: '30rem',
        props: { convention_id: this.convention_id }
      };
    },
    deletedataZ05S04(item, index, checkPersonId) {
      if (item.convention_attendee_id !== '') {
        this.dataZ05S04[index].deleteFlag = 1;
        this.caculationHead(true);
        this.checkChangeRealTimeCheckBox(this.dataZ05S04);
      } else {
        this.dataZ05S04.splice(index, 1);
        this.caculationHead(true);
        this.checkChangeRealTimeCheckBox(this.dataZ05S04);
      }
      this.dataZ05S04Hide.forEach((element) => {
        if (checkPersonId && element.person_cd === item.person_cd) {
          element.deleteFlag = 1;
        } else if (!checkPersonId && element.other_medical_staff_type === item.other_medical_staff_type) {
          element.deleteFlag = 1;
        }
      });
      this.totalRecord -= 1;
    },
    onFinishScreenMOPA(e) {
      if (e === 'close') {
        this.onCloseModal();
      } else {
        let dataExist = 0;
        const data = [e];
        data.forEach((element) => {
          this.totalRecord = this.totalRecord + 1;
          let dataExist1 = 0;
          let isChangeDeleteFlag = false;
          if (this.dataZ05S04Hide.length > 0) {
            this.dataZ05S04Hide.forEach((element2) => {
              element.part_type = '';
              element.update_date = '2020-01-01';
              element.convention_attendee_id = '';
              element.convention_id = this.convention_id;
              element.display_position_cd = '';
              element.display_position_name = '';
              element.person_cd = element.other_medical_staff_type;
              element.person_name_kana = '';
              element.follow_date = 'ー';
              element.series_convention_200 = 'ー';
              element.series_convention_700 = 'ー';
              element.department_cd = '';
              element.department_name = '';
              element.convention_attendee_status_detail = [
                {
                  status_item_cd: '100',
                  status_item_value: '0'
                },
                {
                  status_item_cd: '200',
                  status_item_value: '0'
                },
                {
                  status_item_cd: '300',
                  status_item_value: ''
                },
                {
                  status_item_cd: '400',
                  status_item_value: '0'
                },
                {
                  status_item_cd: '500',
                  status_item_value: '0'
                },
                {
                  status_item_cd: '600',
                  status_item_value: '0'
                },
                {
                  status_item_cd: '700',
                  status_item_value: '0'
                },
                {
                  status_item_cd: '800',
                  status_item_value: '0'
                },
                {
                  status_item_cd: '900',
                  status_item_value: '0'
                }
              ];
              element.other_person_flag = this.other_person_flag;
              this.dataZ05S04.forEach((element3) => {
                if (
                  element.other_medical_staff_type === element2.other_medical_staff_type &&
                  element.other_medical_staff_type === element3.other_medical_staff_type &&
                  element2.other_medical_staff_type === element3.other_medical_staff_type
                ) {
                  if (element2.deleteFlag === 1) {
                    element2.deleteFlag = 0;
                    element3.deleteFlag = 0;
                    isChangeDeleteFlag = true;
                    this.onCloseModal();
                  } else {
                    dataExist = dataExist + 1;
                    dataExist1 = dataExist1 + 1;
                  }
                }
              });
            });
            if (dataExist === 0 && dataExist1 === 0 && !isChangeDeleteFlag) {
              this.dataZ05S04Hide.push(element);
              this.dataZ05S04.push(element);
              this.checkChangeModitor();
              this.onCloseModal();
            }
          } else {
            element.part_type = '';
            element.update_date = '2020-01-01';
            element.convention_attendee_id = '';
            element.convention_id = this.convention_id;
            element.display_position_cd = '';
            element.display_position_name = '';
            element.person_cd = element.other_medical_staff_type;
            element.person_name_kana = '';
            element.follow_date = 'ー';
            element.series_convention_200 = 'ー';
            element.series_convention_700 = 'ー';
            element.department_cd = '';
            element.department_name = '';
            element.convention_attendee_status_detail = [
              {
                status_item_cd: '100',
                status_item_value: '0'
              },
              {
                status_item_cd: '200',
                status_item_value: '0'
              },
              {
                status_item_cd: '300',
                status_item_value: ''
              },
              {
                status_item_cd: '400',
                status_item_value: '0'
              },
              {
                status_item_cd: '500',
                status_item_value: '0'
              },
              {
                status_item_cd: '600',
                status_item_value: '0'
              },
              {
                status_item_cd: '700',
                status_item_value: '0'
              },
              {
                status_item_cd: '800',
                status_item_value: '0'
              },
              {
                status_item_cd: '900',
                status_item_value: '0'
              }
            ];
            element.other_person_flag = this.other_person_flag;
            this.dataZ05S04Hide.push(element);
            this.dataZ05S04.push(element);
            this.checkChangeModitor();
            this.onCloseModal();
          }
        });
        if (dataExist > 0) {
          this.$notify({ message: `${dataExist}人の施設個人は既に登録されています。`, customClass: 'error' });
          return false;
        }
      }
    },
    handleYes() {
      //data A02-S01
      if (this.dataCheckA02S01.length > 0) {
        this.checkAddStock = false;
        this.dataCheckA02S01 = [];
        this.contentA01S02 = '';
        this.dataProduct = [];
        this.checkData = false;
        this.onCloseModal();
      }
      //filter close user
      else if (this.removeUserIndex) {
        this.user_cd = [];
        this.user_name = [];
        this.getScreenData();
        this.getList2();
        this.removeUserIndex = false;
        this.onCloseModal();
      }
      //choose z5s1 user
      else if (this.isFilter) {
        this.onCloseModal();
        this.modalConfig = {
          ...this.modalConfig,
          title: 'ユーザ選択',
          isShowModal: true,
          component: markRaw(Z05S01ModalOrganization),
          width: '44rem',
          customClass: 'custom-dialog modal-fixed',
          isShowClose: true,
          props: {
            mode: 'single',
            userFlag: 1,
            userSelectFlag: 1
          }
        };
        this.isFilter = false;
      }
      //two checkbox on filter
      else if (this.isFilterCheckBox) {
        if (this.isSearchAttendance) {
          this.attendance = !this.attendance;
          this.isSearchAttendance = false;
        } else if (this.isSearchUnfollow) {
          this.unfollow = !this.unfollow;
          this.isSearchUnfollow = false;
        }

        this.getScreenData();
        this.getList2();
        this.isFilterCheckBox = false;
        this.onCloseModal();
      }
      //click to monitoring H
      else if (this.checkClickPersonDetail !== null) {
        let person_cd = this.checkClickPersonDetail;
        this.$router.push({
          name: 'H02_PersonalDetails',
          params: {
            person_cd
          },
          query: { tab: '1' }
        });
        this.checkClickPersonDetail = null;
        this.onCloseModal();
      }

      //click to monitoring stock
      else if (this.addStock) {
        this.checkAddStock = !this.checkAddStock;
        this.getScreenData();
        this.getList2();
        this.addStock = false;

        this.onCloseModal();
      } else if (this.isChangePage !== null) {
        this.onCloseModal();
        if (this.$refs.tblUser) {
          this.$refs.tblUser.scrollTop = 1;
        }
        if (this.total_pages <= this.currentPage) {
          this.currentPage = this.isChangePage;
          this.getScreenData();
        } else {
          this.currentPage = this.isChangePage;
          this.getScreenData();
        }
        this.isChangePage = null;
      } else if (!this.removeUserIndex && !this.isFilter && !this.isFilterCheckBox && this.checkClickPersonDetail === null) {
        this.onCloseModal();
        this.back();
      }
      localStorage.removeItem('checkChangTab');
      this.changeSelectInput = false;
      this.arrCheck = [];
    },
    checkChangeBtn() {
      let isDelete = this.dataZ05S04.filter((e) => e.deleteFlag === 1);
      let isCheck = this.arrCheck.length > 0 || this.dataZ05S04.length !== this.theFistData || this.changeSelectInput || isDelete.length > 0 ? true : false;
      return isCheck;
    },
    cancel() {
      if (this.checkChangeBtn()) {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          title: null,
          isShowClose: false
        };
      } else {
        this.back();
      }
    },
    modalOtherPersonalAddition() {
      this.other_person_flag = 1;
      this.modalConfig = {
        ...this.modalConfig,
        title: 'その他個人追加',
        isShowModal: true,
        component: markRaw(ModalOtherPersonalAddition),
        props: { medicalStaff: this.medical_staff },
        width: '30rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min'
      };
    },
    changeFilter(item) {
      if (this.checkChangeBtn()) {
        if (item === 1) {
          this.isSearchAttendance = true;
          this.attendance = !this.attendance;
        } else {
          this.isSearchUnfollow = true;
          this.unfollow = !this.unfollow;
        }
        this.isFilterCheckBox = true;
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false,
          title: null
        };
      } else {
        this.getScreenData();
        this.getList2();
      }
    },
    Add(isAdd, item) {
      this.checkBtnStock = false;
      this.dataA01S02.forEach((element) => {
        if (element.person_cd === item.person_cd) {
          this.checkData = true;
          element.isAdd = isAdd;
          this.dataCheckA02S01.push(item);
        }
      });
    },
    deleteAdd(item) {
      let countFlagTrue = 0;
      this.dataCheckA02S01.forEach((element) => {
        if (element.person_cd === item.person_cd) {
          this.dataCheckA02S01.splice(this.dataCheckA02S01.indexOf(item), 1);
        }
        if (element.isAdd) {
          countFlagTrue = countFlagTrue + 1;
        }
      });
      this.dataA01S02.forEach((element) => {
        if (element.person_cd === item.person_cd) {
          element.isAdd = false;
        }
      });
      if (countFlagTrue === 0) {
        this.checkData = false;
        this.checkBtnStock = true;
      }
    },
    handleRemove(index) {
      this.dataProduct.splice(index, 1);
    },
    openA02S01() {
      if (this.planned.includes(this.convention_info.definition_label)) {
        this.contentA01S02 = '講演会案内';
      } else if (this.Performed.includes(this.convention_info.definition_label)) {
        this.contentA01S02 = '講演会フォロー';
      }
      if (this.checkChangeBtn()) {
        this.addStock = true;
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false,
          title: null
        };
      } else {
        this.checkAddStock = !this.checkAddStock;
        this.checkBtnStock = true;
        this.getList2();
      }
    },
    getList2(isNextPage) {
      if (!isNextPage) {
        this.currentPage2 = 1;
      }
      this.loading = true;
      const data = {
        convention_id: this.convention_id,
        user_cd: this.user_cd,
        attendance: this.attendance ? 1 : 0,
        unfollow: this.unfollow ? 1 : 0,
        limit: 100,
        offset: this.currentPage2 === 0 ? this.currentPage2 : this.currentPage2 - 1
      };

      C01_S03_AttendantManagement.getList(data)
        .then((res) => {
          this.dataA01S02 = res.data.data.records;
          this.totalRecord2 = res.data.data.pagination.total_items;
          this.dataA01S02.forEach((element2) => {
            element2.isAdd = false;
          });

          this.dataCheckA02S01.forEach((element) => {
            this.dataA01S02.forEach((element2) => {
              if (element.convention_attendee_id === element2.convention_attendee_id) {
                element2.isAdd = true;
              }
            });
          });
          this.loading = false;
        })
        .finally(() => {
          this.js_pscroll(0.4);
          this.loading = false;
        });
    },
    openModalZ05S06() {
      if (this.dataCheckA02S01.length === 0) return;
      this.modalConfig = {
        ...this.modalConfig,
        title: '品目選択',
        isShowModal: true,
        component: markRaw(Z05_S06_Material_Selection),
        width: '33rem',
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {
          mode: this.props.mode,
          categoryCode: this.dataProduct.length > 0 ? this.props.categoryCode : '',
          classificationCode: this.dataProduct.length > 0 ? this.props.classificationCode : '',
          initDataCodes: this.dataProduct.length > 0 ? this.props.initDataCodes : []
        }
      };
    },
    personDetail(person_cd) {
      if (this.checkChangeBtn()) {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          title: null,
          isShowClose: false
        };
        this.checkClickPersonDetail = person_cd;
      } else {
        // localStorage.setItem('activeMenu', 'C01_S03_AttendantManagement');
        this.$router.push({
          name: 'H02_PersonalDetails',
          params: {
            person_cd
          },
          query: { tab: '1' }
        });
      }
    },
    getScreenData() {
      this.loading = true;
      if (this.listRole) {
        this.listRole.forEach((element) => {
          if (element === 'R30') {
            this.checkUserCd = true;
          }
          if (element === 'R31') {
            this.checkRoleButtonC = true;
          }
          if (element === 'R10' || element === 'R90') {
            this.checkRoleButtonE = true;
          }
          if (element === 'R30' || element === 'R31') {
            this.checkRoleButtonExcel = true;
          }
        });
      }
      const data = {
        convention_id: this.convention_id
      };
      C01_S03_AttendantManagement.getScreenData(data)
        .then((res) => {
          this.other_attendees = res?.data?.data?.other_attendee;
          this.D14s = res?.data?.data?.part_list;
          this.D18s = res?.data?.data?.status_item_list;
          this.convention_info = res?.data?.data?.convention_info[0];
          this.medical_staff = res?.data?.data?.medical_staff;
          this.actionIdScreen = res?.data?.data?.action_id;
          this.stockTypeScreen = res?.data?.data?.stock_type;
          // this.stockExistScreen = res.data.data.stock_exist;
          this.inputdeadline = res?.data?.data.input_deadline?.parameter_value;
          this.planOrgCd = res?.data?.data?.convention_info[0]?.plan_org_cd;
          this.planUserCd = res?.data?.data?.convention_info[0]?.plan_user_cd;
          this.userLoginOrgCd = res?.data?.data?.user_login_org_cd;
          let dayBefore3MonthsToday = new Date();
          let dayBefore3Months = dayBefore3MonthsToday.setDate(dayBefore3MonthsToday.getDate() - +this.inputdeadline * 30);
          this.CheckDayBefore3Months = false;
          this.checkRoleButtonD = false;
          this.changeSelectInput = false;
          this.isChangePage = null;
          if (this.formatFullDate3(this.convention_info?.start_date) >= this.formatFullDate3(dayBefore3Months)) {
            this.CheckDayBefore3Months = true;
            // this.checkRoleButtonD = true;
          }
          if (this.planned.includes(this.convention_info?.definition_label)) {
            this.contentA01S02 = '講演会案内';
          } else if (this.Performed.includes(this.convention_info?.definition_label)) {
            this.contentA01S02 = '講演会フォロー';
          }
          this.caculationHead();
        })
        .catch((err) => {
          this.loading = false;
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    },
    getDataHide() {
      const data = {
        convention_id: this.convention_id,
        user_cd: [],
        attendance: 0,
        unfollow: 0,
        limit: 100,
        offset: 0
      };
      C01_S03_AttendantManagement.getList(data).then((res) => {
        this.dataZ05S04Hide = res.data.data.records;
        this.dataZ05S04Hide.forEach((element) => {
          element.deleteFlag = 0;
        });
      });
    },
    getList() {
      this.loading = true;
      const data = {
        convention_id: this.convention_id,
        user_cd: this.user_cd,
        attendance: this.attendance ? 1 : 0,
        unfollow: this.unfollow ? 1 : 0,
        limit: 100,
        offset: this.currentPage === 0 ? this.currentPage : this.currentPage - 1
      };
      C01_S03_AttendantManagement.getList(data).then((res) => {
        this.dataZ05S04 = res.data.data.records;
        this.theFistData = res.data.data.records.length;
        this.totalRecord = res.data.data.pagination.total_items ? res.data.data.pagination.total_items : 0;
        // let dayBefore3MonthsToday = new Date();
        // let dayBefore3Months = dayBefore3MonthsToday.setDate(dayBefore3MonthsToday.getDate() - +this.inputdeadline * 30);
        this.checkChangeRealTimeCheckBox(this.dataZ05S04);
        localStorage.setItem('dataTheFirst', JSON.stringify(this.dataZ05S04));
        this.checkRoles();
        this.loading = false;
      });
    },
    checkChangeRealTimeCheckBox(el) {
      el.forEach((element) => {
        if (element.deleteFlag !== 1) {
          element.convention_attendee_status_detail.forEach((element2) => {
            if (element2.status_item_value === '00') {
              element2.status_item_value = '';
            }
          });
          if (element.other_person_flag === 1) {
            element.checkClickPerson = false;
          } else {
            element.checkClickPerson = false;
          }
          if (element.series_convention_200 !== 'ー') {
            this.dataCaculationHead.lastTimeInformation = this.dataCaculationHead.lastTimeInformation + 1;
          }
          if (element.series_convention_700 !== 'ー') {
            this.dataCaculationHead.lastTimeAttendance = this.dataCaculationHead.lastTimeAttendance + 1;
          }
          if (element.follow_date !== 'ー') {
            this.dataCaculationHead.follow = this.dataCaculationHead.follow + 1;
          }
          element.convention_attendee_status_detail.forEach((element2) => {
            if (element2.status_item_cd === 100 || element2.status_item_cd === '100') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.scheduledToAttendAtTheTimeOfPlanning = this.dataCaculationHead.scheduledToAttendAtTheTimeOfPlanning + 1;
              }
            }
            if (element2.status_item_cd === 200 || element2.status_item_cd === '200') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.guidance = this.dataCaculationHead.guidance + 1;
              }
            }
            if (element2.status_item_cd === 300 || element2.status_item_cd === '300') {
              if (element2.status_item_value === '10') {
                this.dataCaculationHead.scheduledToAttend = this.dataCaculationHead.scheduledToAttend + 1;
              }
            }
            if (element2.status_item_cd === 400 || element2.status_item_cd === '400') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.stay = this.dataCaculationHead.stay + 1;
              }
            }
            if (element2.status_item_cd === 500 || element2.status_item_cd === '500') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.passport = this.dataCaculationHead.passport + 1;
              }
            }
            if (element2.status_item_cd === 600 || element2.status_item_cd === '600') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.ticket = this.dataCaculationHead.ticket + 1;
              }
            }
            if (element2.status_item_cd === 700 || element2.status_item_cd === '700') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.attendance = this.dataCaculationHead.attendance + 1;
              }
            }
            if (element2.status_item_cd === 800 || element2.status_item_cd === '800') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.informationExchangeMeeting = this.dataCaculationHead.informationExchangeMeeting + 1;
              }
            }
            if (element2.status_item_cd === 900 || element2.status_item_cd === '900') {
              if (element2.status_item_value === '1') {
                this.dataCaculationHead.consolationParty = this.dataCaculationHead.consolationParty + 1;
              }
            }
          });
        }
      });
    },
    checkRoles() {
      let userCdStore = this.$store.state.auth.currentUser.user_cd;
      const conventionTargetOrgList = this.convention_info.convention_target_org_list ? this.convention_info.convention_target_org_list.split(',') : [];
      if (conventionTargetOrgList.includes(this.userLoginOrgCd)) {
        this.isOrgListTarget = true;
      }
      if (this.CheckDayBefore3Months) {
        this.listRole.forEach((element) => {
          if (
            element === 'R30' &&
            (this.planUserCd === userCdStore || this.isOrgListTarget) &&
            (this.convention_info.status_type === '10' || this.convention_info.status_type === '30' || this.convention_info.status_type === '40')
          ) {
            this.checkRoleTable = false;
            this.buttonSave = true;
            this.checkRoleButtonD = true;
          }
        });
      }
    },
    checkColor(status_type) {
      let type = parseInt(status_type);
      let background = '';
      let color = '';
      switch (type) {
        case 10:
        case 40:
          background = '#DAF8DC';
          color = '#28A470';
          break;
        case 20:
        case 50:
          background = '#FFE2BA';
          color = '#FF7347';
          break;
        case 30:
        case 60:
          background = '#D1E4FF';
          color = '#448ADD';
          break;
        case 70:
          background = '#E1E1E1';
          color = '#707274';
          break;

        default:
          background = '';
          color = '';
          break;
      }

      return { background: background, color: color };
    },
    resetCaculation() {
      this.dataCaculationHead = {
        lastTimeInformation: 0,
        lastTimeAttendance: 0,
        follow: 0,
        scheduledToAttendAtTheTimeOfPlanning: 0,
        guidance: 0,
        scheduledToAttend: 0,
        stay: 0,
        passport: 0,
        ticket: 0,
        attendance: 0,
        informationExchangeMeeting: 0,
        consolationParty: 0
      };
    },
    caculationHead(isChangeCheck, isModal) {
      if (isModal) {
        this.resetCaculation();
        this.checkChangeRealTimeCheckBox(this.dataZ05S04);
      } else {
        this.resetCaculation();
      }
      this.other_attendees.forEach((element) => {
        element.other_headcount.forEach((element2) => {
          if (element2.status_item_cd === '100') {
            this.dataCaculationHead.scheduledToAttendAtTheTimeOfPlanning = this.dataCaculationHead.scheduledToAttendAtTheTimeOfPlanning + element2.headcount;
          }
          if (element2.status_item_cd === '200') {
            this.dataCaculationHead.guidance = this.dataCaculationHead.guidance + element2.headcount;
          }
          if (element2.status_item_cd === '300') {
            this.dataCaculationHead.scheduledToAttend = this.dataCaculationHead.scheduledToAttend + element2.headcount;
          }
          if (element2.status_item_cd === '400') {
            this.dataCaculationHead.stay = this.dataCaculationHead.stay + element2.headcount;
          }
          if (element2.status_item_cd === '500') {
            this.dataCaculationHead.passport = this.dataCaculationHead.passport + element2.headcount;
          }
          if (element2.status_item_cd === '600') {
            this.dataCaculationHead.ticket = this.dataCaculationHead.ticket + element2.headcount;
          }
          if (element2.status_item_cd === '700') {
            this.dataCaculationHead.attendance = this.dataCaculationHead.attendance + element2.headcount;
          }
          if (element2.status_item_cd === '800') {
            this.dataCaculationHead.informationExchangeMeeting = this.dataCaculationHead.informationExchangeMeeting + element2.headcount;
          }
          if (element2.status_item_cd === '900') {
            this.dataCaculationHead.consolationParty = this.dataCaculationHead.consolationParty + element2.headcount;
          }
        });
      });
      if (!isChangeCheck) {
        this.getListAllcontent();
      }
    },
    getListAllcontent() {
      this.loading = true;
      A02_S03_StockManagementServices.getAllContentService()
        .then((res) => {
          this.dataSelect = res.data.data;
          this.getList();
        })
        .catch((err) => {
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A02S01',
            idNodeNotify: 'msfa-notify-A02S01'
          });
          this.loading = false;
        });
    },
    signalChange(indexDataZ05S04, index) {
      if (!this.checkRoleTable) {
        this.checkChangeModitor(this.dataZ05S04[indexDataZ05S04].convention_attendee_status_detail[index]);
        if (this.dataZ05S04[indexDataZ05S04].convention_attendee_status_detail[index].status_item_value === '1') {
          this.dataZ05S04[indexDataZ05S04].convention_attendee_status_detail[index].status_item_value = '0';
          this.caculationHead(true);
          this.checkChangeRealTimeCheckBox(this.dataZ05S04);
        } else {
          this.dataZ05S04[indexDataZ05S04].convention_attendee_status_detail[index].status_item_value = '1';
          this.caculationHead(true);
          this.checkChangeRealTimeCheckBox(this.dataZ05S04);
        }
      }
    },
    checkChangeModitor(item) {
      if (item) {
        const element = `${item.status_item_cd}${item.convention_attendee_id}`;
        if (!this.arrCheck.includes(element)) {
          this.arrCheck.push(element);
        } else {
          this.arrCheck.splice(this.arrCheck.indexOf(element), 1);
        }
      }
      if (this.checkChangeBtn()) {
        localStorage.setItem('checkChangTab', true);
      } else {
        localStorage.removeItem('checkChangTab');
      }
    },
    changeSelect() {
      this.changeSelectInput = true;
      localStorage.setItem('checkChangTab', true);
      this.caculationHead(true);
      this.checkChangeRealTimeCheckBox(this.dataZ05S04);
    },
    submit() {
      this.loadingSubmitAtten = true;
      const data = {
        convention_id: this.convention_id,
        convention_attendee: this.dataZ05S04,
        other_attendee: this.otherAttendee[0] ? this.otherAttendee : this.other_attendees
      };
      C01_S03_AttendantManagement.postData(data)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.getScreenData();
          localStorage.removeItem('checkChangTab');
          this.changeSelectInput = false;
          this.arrCheck = [];
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          this.loadingSubmitAtten = false;
        });
    },
    openModalC01S03() {
      this.modalConfig = {
        ...this.modalConfig,
        title: 'その他出席者数入力',
        isShowModal: true,
        component: markRaw(C01S03AttendantManagementModal),
        width: '70%',
        props: { otherAttendees: this.other_attendees }
      };
    },
    onCloseModalC01S03(e) {
      if (e === 1) {
        this.onCloseModal();
      } else {
        this.onCloseModal();
        this.otherAttendee = e;
        this.other_attendees = e;
        this.caculationHead(true, true);
      }
    },
    openModalZ05S04() {
      this.other_person_flag = 0;
      this.modalConfig = {
        ...this.modalConfig,
        title: '施設個人選択',
        isShowModal: true,
        component: this.markRaw(Z05S04FacilityCustomerSelection),
        width: this.currDevice() !== 'Desktop' ? '95%' : '70rem',
        props: {
          mode: 'multiple',
          propsPrams: { ...this.paramZ05S04, org_cd: this.org_cd, user_cd: this.user_cd.toString(), user_name: this.user_name.toString() }
        }
      };
    },
    onResultModalZ05S04(data) {
      let dataExist = 0;
      data.facilityPersonalSelected.forEach((element) => {
        this.totalRecord = this.totalRecord + 1;
        let dataExist1 = 0;
        let isChangeDeleteFlag = false;
        if (this.dataZ05S04Hide.length > 0) {
          this.dataZ05S04Hide.forEach((element2) => {
            element.other_medical_staff_type = '';
            element.part_type = '';
            element.update_date = '';
            element.convention_attendee_id = '';
            element.convention_id = this.convention_id;
            element.follow_date = 'ー';
            element.series_convention_200 = 'ー';
            element.series_convention_700 = 'ー';
            element.display_position_cd = element.position_cd;
            element.display_position_name = element.position_name;
            element.convention_attendee_status_detail = [
              {
                status_item_cd: '100',
                status_item_value: '0'
              },
              {
                status_item_cd: '200',
                status_item_value: '0'
              },
              {
                status_item_cd: '300',
                status_item_value: ''
              },
              {
                status_item_cd: '400',
                status_item_value: '0'
              },
              {
                status_item_cd: '500',
                status_item_value: '0'
              },
              {
                status_item_cd: '600',
                status_item_value: '0'
              },
              {
                status_item_cd: '700',
                status_item_value: '0'
              },
              {
                status_item_cd: '800',
                status_item_value: '0'
              },
              {
                status_item_cd: '900',
                status_item_value: '0'
              }
            ];
            element.other_person_flag = this.other_person_flag;

            this.dataZ05S04.forEach((element3) => {
              if (element.person_cd === element2.person_cd && element.person_cd === element3.person_cd && element2.person_cd === element3.person_cd) {
                if (element2.deleteFlag === 1) {
                  element2.deleteFlag = 0;
                  element3.deleteFlag = 0;
                  isChangeDeleteFlag = true;
                  this.onCloseModal();
                } else {
                  dataExist = dataExist + 1;
                  dataExist1 = dataExist1 + 1;
                }
              }
            });
          });
          if (dataExist === 0 && dataExist1 === 0 && !isChangeDeleteFlag) {
            this.dataZ05S04 = [...this.dataZ05S04, element];
            this.dataZ05S04Hide.push(element);
            this.checkChangeModitor();
            this.onCloseModal();
          }
        } else {
          element.other_medical_staff_type = '';
          element.part_type = '';
          element.update_date = '';
          element.convention_attendee_id = '';
          element.convention_id = this.convention_id;
          element.follow_date = 'ー';
          element.series_convention_200 = 'ー';
          element.series_convention_700 = 'ー';
          element.display_position_cd = element.position_cd;
          element.display_position_name = element.position_name;
          element.convention_attendee_status_detail = [
            {
              status_item_cd: '100',
              status_item_value: '0'
            },
            {
              status_item_cd: '200',
              status_item_value: '0'
            },
            {
              status_item_cd: '300',
              status_item_value: ''
            },
            {
              status_item_cd: '400',
              status_item_value: '0'
            },
            {
              status_item_cd: '500',
              status_item_value: '0'
            },
            {
              status_item_cd: '600',
              status_item_value: '0'
            },
            {
              status_item_cd: '700',
              status_item_value: '0'
            },
            {
              status_item_cd: '800',
              status_item_value: '0'
            },
            {
              status_item_cd: '900',
              status_item_value: '0'
            }
          ];
          element.other_person_flag = this.other_person_flag;
          this.dataZ05S04 = [...this.dataZ05S04, element];
          this.dataZ05S04Hide.push(element);
          this.checkChangeModitor();
          this.onCloseModal();
        }
      });
      if (dataExist > 0) {
        this.$notify({ message: `${dataExist}人の施設個人は既に登録されています。`, customClass: 'error' });
        return false;
      }
    },
    onResultModal(data) {
      this.onCloseModal();
      if (data?.screen === 'C01_S05') {
        this.onCloseModal();
      }
      if (data?.screen === 'Z05_S01') {
        this.user_cd = [];
        this.user_name = [];
        this.orgCdList = [];
        this.userCdList = [];
        this.org_cd = data.orgSelected?.[0].org_cd;
        data.userSelected.forEach((element) => {
          this.user_cd.push(element.user_cd);
          this.user_name.push(element.user_name);
        });

        data.orgSelected?.forEach((x) => {
          this.orgCdList.push(x.org_cd);
        });
        data.userSelected?.forEach((x) => {
          this.userCdList.push({
            org_cd: x.org_cd,
            user_cd: x.user_cd
          });
        });

        this.onCloseModal();
        this.getScreenData();
        this.getList2();
        if (this.$refs.tblUser) {
          this.$refs.tblUser.scrollTop = 1;
        }
      }
      if (data?.screen === 'Z05_S06') {
        this.props.initDataCodes = [];
        this.props.classificationCode = data.classifications?.product_group_cd;
        this.props.categoryCode = data.category?.definition_value;
        data.dataSelected?.forEach((x) => {
          this.props.initDataCodes.push(x.product_cd);
        });
        this.dataProduct = data.dataSelected;
        this.onCloseModal();
      }
      if (data?.screen === 'Z05_S04') {
        this.onResultModalZ05S04(data);
      }
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
      this.removeUserIndex = false;
      this.isChangePage = null;
      this.isSearchAttendance = false;
      this.isFilter = false;
      this.addStock = false;
    },
    openModal_Z05_S01() {
      if (this.checkChangeBtn()) {
        this.isFilter = true;
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          title: null,
          isShowClose: false
        };
      } else {
        this.modalConfig = {
          ...this.modalConfig,
          title: 'ユーザ選択',
          isShowModal: true,
          component: markRaw(Z05S01ModalOrganization),
          width: '44rem',
          customClass: 'custom-dialog modal-fixed',
          props: {
            mode: 'single',
            orgCdList: this.orgCdList,
            userCdList: this.userCdList,
            userFlag: 1,
            userSelectFlag: 1
          }
        };
      }
    },
    handleRemoveUser(index) {
      if (this.checkChangeBtn()) {
        this.removeUserIndex = true;
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          title: null,
          isShowClose: false
        };
      } else {
        this.user_cd.splice(index, 1);
        this.user_name.splice(index, 1);
        this.getScreenData();
        this.getList2();
      }
    },
    handleCurrentChange(val) {
      if (this.checkChangeBtn()) {
        this.isChangePage = val;
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          title: null,
          isShowClose: false
        };
      } else {
        if (this.$refs.tblUser) {
          this.$refs.tblUser.scrollTop = 1;
        }
        if (this.total_pages <= this.currentPage) {
          this.currentPage = val;
          this.getScreenData();
        } else {
          this.currentPage = val;
          this.getScreenData();
        }
      }
    },
    handleCurrentChange2(val) {
      if (this.total_pages2 <= this.currentPage2) {
        if (this.$refs.tblUser2) {
          this.$refs.tblUser2.scrollTop = 1;
        }
        this.currentPage2 = val;
        this.getList2(true);
      } else {
        this.currentPage2 = val;
        this.getList2(true);
      }
    },
    checkSubmit() {
      let arrAddFacility_cd = [];
      let arrAddPerson_cd = [];
      let arrdataProduct = [];
      let stockTypes = [];
      let actionIds = [];
      this.dataCheckA02S01.forEach((element) => {
        if (element.isAdd) {
          arrAddFacility_cd.push(element.facility_cd);
          arrAddPerson_cd.push(element.person_cd);
          stockTypes.push(element.schedule_type);
        }
      });
      this.dataProduct.forEach((element) => {
        arrdataProduct.push(element.product_cd);
      });
      this.submitStock(arrAddFacility_cd, arrAddPerson_cd, this.contentA01S02, arrdataProduct, stockTypes, actionIds);
    },
    submitStock(arrAddFacility_cd, arrAddPerson_cd, contentA01S02, arrdataProduct) {
      this.loadingStock = true;
      if (contentA01S02 === '講演会案内') {
        this.contentA01S02Id = '40';
      } else if (contentA01S02 === '講演会フォロー') {
        this.contentA01S02Id = '50';
      }
      const data = {
        facility_cd: arrAddFacility_cd,
        person_cd: arrAddPerson_cd,
        content_cd: this.contentA01S02Id ? this.contentA01S02Id : this.contentA01S02,
        product_cd: arrdataProduct,
        stock_type: this.stockTypeScreen,
        action_id: this.actionIdScreen
      };
      A02_S03_StockManagementServices.AddStockService(data)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.contentA01S02 = '';
          this.contentA01S02Id = '';
          this.dataProduct = [];
          this.dataCheckA02S01 = [];
          this.openA02S01();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(async () => {
          this.loadingStock = false;
          this.checkData = false;
        });
    },
    handleEnter(event) {
      let slide1 = document.querySelector('.slide-1');
      if (slide1) {
        event.classList.add('ltr');
        this.js_pscroll(0.4);
      }
    },
    handleLeave(event) {
      let slide2 = document.querySelector('.slide-2');
      if (slide2) {
        event.classList.add('ltr');
        this.js_pscroll(0.4);
      }
    },
    closeA02S01() {
      if (this.dataCheckA02S01.length > 0) {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false,
          title: null
        };
      } else {
        this.checkAddStock = false;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
@media (max-width: 1024px) {
  .noData-content {
    img {
      width: 130px;
    }
  }
}
</style>
