<template>
  <div class="boxBody">
    <div v-loading="isLoading" class="wrapContent">
      <div class="groupKnowled scrollbar">
        <div class="groupKnowled-left">
          <div class="knowled-head">
            <div class="knowled-label">
              <div class="head-label">
                <span class="spanLabel spanLabel--category">{{ knowledgeDetail?.knowledge_category_name }}</span>
                <!--A4-->
                <span class="span-date">{{ getTimeInterval(knowledgeDetail?.release_datetime) }}</span>
                <!--A7-->
              </div>
              <div class="head-tag">
                <ul>
                  <li v-show="knowledgeDetail?.new_label == '1'">
                    <img src="@/assets/template/images/icon_ribbon-tag.svg" alt="" />
                  </li>
                  <li v-show="knowledgeDetail?.update_label == '1'">
                    <img src="@/assets/template/images/icon_ribbon_add_st.svg" alt="" />
                  </li>
                </ul>
              </div>
            </div>
            <div class="knowled-title">
              <h2 class="knowled-tlt">
                <span class="knowled-item">
                  <img src="@/assets/template/images/icon_article.svg" alt="" />
                </span>
                <p class="break-spaces">{{ knowledgeDetail?.title }}</p>
                <!--A5-->
              </h2>
              <p class="knowled-id">
                ナレッジID：{{ knowledgeDetail?.knowledge_id }}
                <!--A6-->
              </p>
            </div>
          </div>
          <div ref="groupKnowledInfo" class="Knowled-content scrollbar" @scroll="setScroll">
            <div class="KnowledLst">
              <ul>
                <li v-if="knowledgeDetail?.product_name">
                  <span class="KnowledLst-tlt">品目</span>
                  <span class="KnowledLst-label">{{ knowledgeDetail?.product_name }}</span>
                  <!--A10-->
                </li>
                <li
                  v-if="
                    knowledgeDetail?.facility_short_name ||
                    knowledgeDetail?.person_name ||
                    knowledgeDetail?.department_name ||
                    knowledgeDetail?.dsiplay_position_name
                  "
                >
                  <!--A11-->
                  <span class="KnowledLst-tlt">施設個人</span>
                  <!--A12-->
                  <div class="KnowledLst-label">
                    <p>
                      <span v-if="knowledgeDetail?.facility_short_name" class="KnowledLst-blue" @click="facilityDetail(knowledgeDetail.facility_cd)">{{
                        knowledgeDetail.facility_short_name
                      }}</span>
                      <span v-if="knowledgeDetail?.department_name">{{ knowledgeDetail?.department_name }} </span>
                    </p>
                    <p style="margin-top: 8px">
                      <span v-if="knowledgeDetail?.person_name" class="KnowledLst-blue" @click="goPersonDetail(knowledgeDetail)">{{
                        knowledgeDetail.person_name
                      }}</span>
                      <span v-if="knowledgeDetail?.dsiplay_position_name">{{ knowledgeDetail.dsiplay_position_name }}</span>
                    </p>
                  </div>
                </li>
                <li v-if="knowledgeDetail?.facility_type_group_name">
                  <span class="KnowledLst-tlt">施設区分分類</span>
                  <!--A13-->
                  <span class="KnowledLst-label">{{ knowledgeDetail?.facility_type_group_name }}</span>
                </li>
                <li v-if="knowledgeDetail?.medical_subjects_group_name">
                  <span class="KnowledLst-tlt">診療科目分類</span>
                  <!--A14-->
                  <span class="KnowledLst-label">{{ knowledgeDetail?.medical_subjects_group_name }}</span>
                </li>
                <li v-if="knowledgeDetail?.prefecture_name || knowledgeDetail?.municipality_name">
                  <span class="KnowledLst-tlt">施設所在地</span>
                  <!--A15-->
                  <span class="KnowledLst-label">{{ knowledgeDetail?.prefecture_name }}　{{ knowledgeDetail?.municipality_name }}</span>
                </li>
              </ul>
            </div>
            <div v-if="knowledgeDetail?.contents" class="KnowledContents">
              <p class="KnowledContents-label">内容</p>
              <!--A16-->
              <p class="KnowledContents-text" style="white-space: pre-wrap">
                {{ knowledgeDetail?.contents }}
              </p>
            </div>
            <div class="custom-k">
              <div class="knowledThumb">
                <ul v-if="knowledgeDetail?.group_user_file.length">
                  <container class="container-custom">
                    <li
                      v-for="(item, index) of knowledgeDetail?.group_user_file"
                      :key="item"
                      :style="`--offset: ${knowledgeDetail?.group_user_file.length - index}`"
                    >
                      <!--Image-->
                      <img v-show="item.anonymous_flag === 0" class="thumb-border" :src="item.avatar_image_data" alt="" />
                      <img v-show="item.anonymous_flag === 1" class="thumb-border" src="@/assets/template/images/anonymous.svg" alt="" />
                    </li>
                  </container>
                </ul>
                <ul v-else>
                  <li>
                    <!--Image-->
                    <img v-show="knowledgeDetail?.anonymous_flag === 0" class="thumb-border" :src="knowledgeDetail?.avatar_image_data" alt="" />
                    <img v-show="knowledgeDetail?.anonymous_flag === 1" class="thumb-border" src="@/assets/template/images/anonymous.svg" alt="" />
                  </li>
                </ul>
              </div>
              <div class="knowledAuthor">
                <h3 class="knowledAuthor-title">
                  <span class="knowledAuthor-item">
                    <img src="@/assets/template/images/icon_user-edit01.svg" alt="" />
                  </span>
                  <span class="link" @click="redirectAccountSetting(knowledgeDetail?.create_user_cd)">
                    {{ knowledgeDetail?.create_user_name }}
                  </span>
                  <!--A8-->
                </h3>
                <div v-if="getA9(knowledgeDetail?.create_user_cd_together)?.length > 0" class="knowledAuthor-label">
                  <div class="knowledAuthor-tlt">
                    共同作成者:
                    <!--A9-->
                  </div>
                  <div class="knowledAuthor-name">
                    <p v-for="item of getA9(knowledgeDetail?.create_user_cd_together)" :key="item" class="knowledAuthor-txt">
                      <span :class="item.user_cd ? 'link' : ''" @click="item.user_cd ? redirectAccountSetting(item.user_cd) : ''">
                        <span v-if="item.org_label">{{ item.org_label }}　</span>
                        <span>{{ item.user_name }} </span>
                      </span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!---D2-->
          <div class="Knowled-btn">
            <button v-if="showEditKnowledBtn()" type="button" class="btn btn-primary" @click="goToKnowledDetail()">編集</button>
            <button v-else type="button" class="btn btn-primary" @click="showRegisterBox()">要望・要求事項を送信</button>
          </div>
        </div>
        <div class="groupKnowled-right">
          <div class="groupTabs">
            <div class="groupTabs-nav">
              <div class="head-tabs head-tabs--f02">
                <el-tabs v-model="activeName" class="demo-tabs custom-tab" @tab-click="handleClick">
                  <el-tab-pane name="first">
                    <template #label>
                      <span class="custom-tabs-label">
                        <span class="item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_timeline.svg')" alt="" />
                        </span>
                        <span>タイムライン</span>
                      </span>
                    </template>
                    <div id="tabs-1" class="tab-pane active" role="tabpanel">
                      <div v-if="knowledgeDetail?.timelines.length > 0" ref="knowledgeDetail" class="dailyList scrollbar" @scroll="setScroll">
                        <ul>
                          <li v-for="(item, index) in knowledgeDetail?.timelines" :key="item">
                            <div class="dailyList-calendar">
                              <span class="label"> {{ formatFullDate(item.start_datetime) }} ({{ getWeek(item.start_datetime) }}） </span
                              ><!--B1-->
                            </div>
                            <ul class="dailyList-sub">
                              <template v-for="(itemChild, i) of item.content" :key="itemChild">
                                <!--面談-->
                                <li v-if="itemChild.channel_type === '10'">
                                  <div class="dailyList-flex">
                                    <div class="dailyList-dateTime">
                                      <span class="time">{{ getformatTimeHourMinute(itemChild) }} </span>
                                      <div class="iconItem">
                                        <span class="item"><img src="@/assets/template/images/icon_interview-daily.svg" alt="" /></span>
                                      </div>
                                    </div>
                                    <div class="dailyList-content">
                                      <div class="dailyList-box">
                                        <div v-if="itemChild.off_label_flag === 1" class="dailyList-label">
                                          <span class="span-label span-label--off">
                                            <span class="span-label-item"><img src="@/assets/template/images/icon_exclamation-circle.svg" alt="" /></span>
                                            オフラベルあり
                                          </span>
                                          <span class="span-label span-label--knowledge">
                                            <span class="span-label-item"><img src="@/assets/template/images/icon_knowledge.svg" alt="" /></span>
                                            ナレッジ登録済
                                          </span>
                                        </div>
                                        <div class="dailyList-group">
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt interview">
                                              <p class="dailyList-info-label">面談先</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanBlue dailyList-bold" @click="goPersonDetail(itemChild)">
                                                {{ itemChild.department_name }} {{ itemChild.person_name }} {{ itemChild.display_position_name }}
                                              </p>
                                            </div>
                                          </div>
                                          <div
                                            v-for="(dataProtuct, y) of itemChild.channel_detail"
                                            :id="'accordion' + y"
                                            :key="dataProtuct"
                                            class="daily-accordion"
                                          >
                                            <h2
                                              class="article-head detail"
                                              data-toggle="collapse"
                                              :data-target="'#article-' + index + y + dataProtuct.detail_order + dataProtuct.channel_detail_id"
                                            >
                                              <span> {{ dataProtuct.detail_order }}. {{ dataProtuct.content_name }} {{ dataProtuct.product_name }}</span>
                                            </h2>
                                            <div
                                              :id="'article-' + index + y + dataProtuct.detail_order + dataProtuct.channel_detail_id"
                                              class="collapse article-body"
                                              :data-parent="'#accordion' + y"
                                            >
                                              <div class="daily-article">
                                                <ul>
                                                  <li>
                                                    <span class="daily-article-tlt">フェーズ</span>
                                                    <span class="daily-article-label">{{ dataProtuct.phase_name }}</span>
                                                  </li>
                                                  <li>
                                                    <span class="daily-article-tlt">反応</span>
                                                    <span class="daily-article-label">{{ dataProtuct.reaction_name }}</span>
                                                  </li>
                                                </ul>
                                                <p class="daily-article-text m-8">
                                                  {{ dataProtuct.note }}
                                                </p>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt interview">
                                              <p class="dailyList-info-label">面談者</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanBlue dailyList-bold" @click="goAccountSetting(itemChild)">
                                                {{ itemChild.user_name }}
                                                <span v-if="itemChild.user_post_type">({{ itemChild.user_post_type }})<span> </span></span>
                                              </p>
                                              <p class="dailyList-add">
                                                <span class="dailyList-item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                                                {{ itemChild.org_label }}
                                              </p>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="viewMore">
                                          <a class="dailyList-spanBlue dailyList-bold" @click="callScreenA01_S04(itemChild)">詳細を見る</a>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </li>
                                <!--説明会-->
                                <li v-if="itemChild.channel_type === '20'">
                                  <div class="dailyList-flex">
                                    <div class="dailyList-dateTime">
                                      <span class="time">{{ getformatTimeHourMinute(itemChild) }}</span>
                                      <div class="iconItem">
                                        <span class="item"><img src="@/assets/template/images/icon_interview-daily02.svg" alt="" /></span>
                                      </div>
                                    </div>
                                    <div class="dailyList-content">
                                      <div class="dailyList-box">
                                        <div class="dailyList-group">
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt briefing">
                                              <p class="dailyList-info-label dailyList-gray">説明会名</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanBlue dailyList-bold" @click="goBriefingSessionInput(item)">
                                                {{ itemChild?.title }}
                                              </p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt briefing">
                                              <p class="dailyList-info-label dailyList-gray">説明会区分</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.briefing_type_name }}</p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt briefing">
                                              <p class="dailyList-info-label dailyList-gray">品目</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.product_name }}</p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt briefing">
                                              <p class="dailyList-info-label dailyList-gray">対象施設</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.briefing_facility_short_name }}</p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt briefing">
                                              <p class="dailyList-info-label dailyList-gray">実施者</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanBlue dailyList-bold" @click="goAccountSetting(itemChild)">
                                                {{ itemChild.user_name }} ({{ itemChild.user_post_type }})
                                              </p>
                                              <p class="dailyList-add">
                                                <span class="dailyList-item"><img src="@/assets/template/images/icon_building.svg" alt="" /></span>
                                                {{ itemChild.org_label }}
                                              </p>
                                            </div>
                                          </div>
                                          <div v-show="itemChild.note && itemChild?.note.length > 0" id="accordion-3" class="daily-accordion">
                                            <h2 class="article-head" data-toggle="collapse" :data-target="'#article-3' + index + i">
                                              <span>特記事項</span>
                                            </h2>
                                            <div
                                              v-show="itemChild.note && itemChild?.note.length > 0"
                                              :id="'article-3' + index + i"
                                              class="collapse article-body"
                                              data-parent="#accordion-3"
                                            >
                                              <div class="daily-article">
                                                <p class="daily-article-text">
                                                  {{ itemChild.note }}
                                                </p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </li>
                                <!-- 講演会 -->
                                <li v-if="itemChild.channel_type === '30'">
                                  <div class="dailyList-flex">
                                    <div class="dailyList-dateTime">
                                      <span class="time"></span>
                                      <div class="iconItem">
                                        <span class="item"><img src="@/assets/template/images/icon_interview-daily01.svg" alt="" /></span>
                                      </div>
                                    </div>
                                    <div class="dailyList-content">
                                      <div class="dailyList-box">
                                        <div class="dailyList-group">
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt convention">
                                              <p class="dailyList-info-label dailyList-gray">講演会名</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanBlue dailyList-bold" @click="goLectureInput(itemChild)">
                                                {{ itemChild?.title }}
                                              </p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt convention">
                                              <p class="dailyList-info-label dailyList-gray">講演会区分</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.convention_type_name }}</p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt convention">
                                              <p class="dailyList-info-label dailyList-gray">品目</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.product_name }}</p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt convention">
                                              <p class="dailyList-info-label dailyList-gray">対象組織</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.org_label }}</p>
                                            </div>
                                          </div>
                                          <div v-show="itemChild.note && itemChild?.note.length > 0" id="accordion-2" class="daily-accordion">
                                            <h2 class="article-head" data-toggle="collapse" :data-target="'#article-2' + index + i"><span>特記事項</span></h2>
                                            <div
                                              v-show="itemChild.note && itemChild?.note.length > 0"
                                              :id="'article-2' + index + i"
                                              class="collapse article-body"
                                              data-parent="#accordion-2"
                                            >
                                              <div class="daily-article">
                                                <p class="daily-article-text">
                                                  {{ itemChild.note }}
                                                </p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </li>

                                <!--外部サービス-->
                                <li v-if="itemChild.channel_type == '40'">
                                  <div class="dailyList-flex">
                                    <div class="dailyList-dateTime">
                                      <span class="time">{{ getformatTimeHourMinute(itemChild) }}</span>
                                      <div class="iconItem">
                                        <span class="item"><img src="@/assets/template/images/icon_interview-daily03.svg" alt="" /></span>
                                      </div>
                                    </div>
                                    <div class="dailyList-content">
                                      <div class="dailyList-box">
                                        <div class="dailyList-group">
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt external">
                                              <p class="dailyList-info-label dailyList-gray">視聴者</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanBlue dailyList-bold" @click="goPersonDetail(itemChild)">
                                                {{ itemChild.department_name }} {{ itemChild.person_name }} {{ itemChild.display_position_name }}
                                              </p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt external">
                                              <p class="dailyList-info-label dailyList-gray">品目</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.product_name }}</p>
                                            </div>
                                          </div>
                                          <div class="dailyList-info">
                                            <div class="dailyList-info-tlt external">
                                              <p class="dailyList-info-label dailyList-gray">コンテンツ</p>
                                            </div>
                                            <div class="dailyList-info-text">
                                              <p class="dailyList-spanGray">{{ itemChild.content }}</p>
                                            </div>
                                          </div>
                                          <div v-show="itemChild.note && itemChild?.note.length > 0" id="accordion-4" class="daily-accordion">
                                            <h2 class="article-head" data-toggle="collapse" :data-target="'#article-4' + index + i"><span>特記事項</span></h2>
                                            <div
                                              v-show="itemChild.note && itemChild?.note.length > 0"
                                              :id="'article-4' + index + i"
                                              class="collapse article-body"
                                              data-parent="#accordion-4"
                                            >
                                              <div class="daily-article">
                                                <p class="daily-article-text">
                                                  {{ itemChild.note }}
                                                </p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </li>
                              </template>
                            </ul>
                          </li>
                        </ul>
                      </div>
                      <EmptyData v-else custom-class="customClassEmpty" thumb-margin-top="20px" />
                    </div>
                  </el-tab-pane>

                  <el-tab-pane name="second">
                    <template #label>
                      <span class="custom-tabs-label">
                        <span class="item">
                          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_comment-line.svg')" alt="" />
                        </span>
                        <span>評価コメント({{ knowledgeDetail?.total_evaluation_comment }})</span>
                      </span>
                    </template>
                    <div class="groupTabs-content">
                      <div class="groupEval">
                        <div ref="groupEvalRef" class="groupEval-info scrollbar">
                          <div class="btnLike">
                            <button v-if="knowledgeDetail?.knowledge_niked === 1" class="btn btn-like active">
                              <!--Endable-->
                              <span class="btn-iconLeft stroke-like">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_like01.svg')" alt="" /> </span
                              >いいね！
                            </button>
                            <button v-else class="btn btn-like stroke-like" @click.once="registerLike" @touchstart.once="registerLike">
                              <span class="btn-iconLeft">
                                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_like01.svg')" alt="" /> </span
                              >いいね！
                            </button>

                            <span class="btnLike-number">{{ knowledgeDetail?.total_knowledge_nice }}件</span>
                          </div>
                          <div v-if="evaluation_comments.length > 0" class="listEval">
                            <ul>
                              <li v-for="item of evaluation_comments" :key="item">
                                <div class="listEval-head">
                                  <div class="listEval-info">
                                    <div class="listEval-thumb">
                                      <img :src="item.avatar_image_data" alt="" />
                                    </div>
                                    <div class="listEval-tlt">
                                      <h2 class="tlt">{{ item.user_name }} {{ item.org_label }}</h2>
                                    </div>
                                  </div>
                                  <div class="listEval-btn">
                                    <button
                                      v-show="showBtnEdit(item.nice_user_cd)"
                                      class="btn btn--icon"
                                      style="margin-right: 5px"
                                      @click="showEditBox(item)"
                                      @touchstart="showEditBox(item)"
                                    >
                                      <img src="@/assets/template/images/icon_pen01.svg" alt="" />
                                    </button>
                                    <button
                                      v-show="showBtnDelete(item.nice_user_cd)"
                                      class="btn btn--icon"
                                      @click="onDelete(item.nice_id)"
                                      @touchstart="onDelete(item.nice_id)"
                                    >
                                      <img src="@/assets/template/images/icon_delete.svg" alt="" />
                                    </button>
                                  </div>
                                </div>
                                <div class="listEval-cmt">
                                  <p :id="item.nice_id" class="listEval-txt text-see-more">
                                    {{ item.comment }}
                                  </p>
                                  <span
                                    v-show="contentTextSeeMore(i, item.comment)"
                                    :id="'hide' + item.nice_id"
                                    class="link"
                                    @click="hideDetail(item)"
                                    @touchstart="hideDetail(item)"
                                  >
                                    <!-- 元に戻る -->
                                  </span>
                                  <span
                                    v-show="contentTextSeeMore(i, item.comment)"
                                    :id="'show' + item.nice_id"
                                    class="link"
                                    @click="showDetail(item)"
                                    @touchstart="showDetail(item)"
                                  >
                                    もっと見る
                                  </span>
                                </div>
                              </li>
                            </ul>
                          </div>
                          <EmptyData v-if="evaluation_comments.length === 0" custom-class="evaluation-comments" thumb-margin-top="20px" />
                        </div>

                        <div v-if="evaluation_comments.length" class="paginationEval">
                          <div class="pagination-cases">全{{ pagination.totalItems || 'O' }}件</div>
                          <PaginationTable
                            :page-size="pagination.pageSize"
                            :total="pagination.totalItems"
                            :current-page="pagination.curentPage"
                            @current-change="handleCurrentChange"
                          />
                        </div>
                      </div>
                    </div>
                  </el-tab-pane>
                </el-tabs>
              </div>
              <!-- Tab panes -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal  Delete Confirm -->
    <el-dialog
      v-model="modalConfig1.isShowModal"
      :custom-class="modalConfig1.customClass"
      :title="modalConfig1.title"
      :width="modalConfig1.width"
      :destroy-on-close="modalConfig1.destroyOnClose"
      :close-on-click-modal="modalConfig1.closeOnClickMark"
      :show-close="true"
      @close="closeModal()"
    >
      <template #default>
        <!-- Modal -->

        <div class="modal-content" style="background: unset; border: unset">
          <!-- Start  -->
          <div style="padding: 0" class="modal-body modal-evaluation">
            <div class="evaluation-textarea">
              <el-input
                v-model="params_editComment.comment"
                clearable
                class="form-control-textarea"
                :rows="6"
                type="textarea"
                placeholder="要望・要求事項入力"
              />
            </div>
            <div class="evaluation-btn">
              <button
                type="button"
                :class="['btn btn-outline-primary--cancel btn-outline-primary', iconLoading ? 'eventNone-canncel' : '']"
                @click="confirmCancel()"
              >
                キャンセル
              </button>
              <button
                type="button"
                :class="['btn btn-primary btn-yes-cancel no-log', iconLoading ? 'eventNone' : '']"
                @click="isEditComment ? registerComment() : registerKnowledge()"
              >
                <i :class="['el-icon-loading', iconLoading ? 'inline-block' : '']"></i><span class="textSubmit">保存</span>
              </button>
            </div>
          </div>
          <!-- End -->
        </div>
      </template>
    </el-dialog>

    <!-- Modal  Delete Confirm -->
    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="false"
      @close="closeModal()"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onCloseModal"
          @handleConfirm="deleteLike"
          @handleYes="resultConfirm"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<!-- eslint-disable eqeqeq -->
<script>
import F01S02Service from '@/api/F01/F01_S02_KnowledgeDetailsServices';
import PaginationTable from '../../../components/PaginationTable.vue';
import { markRaw } from 'vue';
import { Auth } from '@/api';
import moment from 'moment';
import A01_S04_Service from '@/api/A01/A01_S04_InterviewDetailedInputService';

export default {
  name: 'F01_S02_KnowledgeDetails',
  components: {
    PaginationTable
  },
  data() {
    return {
      activeName: 'first',
      iconLoading: false,
      knowledgeDetail: null,
      registerCommentEdit: null,
      nice_id: '',
      evaluation_comments: [],
      params_editComment: { comment: '', demand_note: '' },
      dataInit: '',
      title: '',
      isEditComment: false,
      isLoading: false,
      user_login_cd: '',
      knowledge_id: '',
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        customClass: 'custom-dialog',
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      modalConfig1: {
        title: '',
        isShowModal: false,
        component: null,
        customClass: 'custom-dialog modal-fixed modal-fixed-min',
        props: {},
        width: '33rem',
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: true
      },
      pagination: {
        pageSize: 20,
        curentPage: 1,
        totalItems: 0
      },
      filter: {
        limit: '20',
        offset: '0'
      },
      role: false,
      onScrollTop: 0,
      onScrollTopTimeline: 0,
      isRegisterLike: false
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'ナレッジ詳細',
      icon: 'icon-light-group.svg',
      isShowBack: true
    });
    localStorage.removeItem('checkChangTab');
    this.onScrollTop = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.F01_S02_KnowledgeDetails || 0;
    this.onScrollTopTimeline = +JSON.parse(localStorage.getItem('ScrollTopScreen'))?.F01_S02_KnowledgeDetails_Timeline || 0;

    window.addEventListener('resize', function () {
      let el = document.querySelectorAll('.el-tabs__active-bar');
      let elTab = document.querySelector('.el-tabs__item');
      el[0].style.width = elTab.clientWidth + 'px';
    });

    if (this._route('F01_S02_KnowledgeDetails')) this.knowledge_id = this._route('F01_S02_KnowledgeDetails').params.knowledge_id;

    this.user_login_cd = this.getCurrentUser().user_cd;
    this.getDetail(this.knowledge_id, this.filter);
    this.checkPermission();
  },
  methods: {
    resultConfirm(e) {
      e?.callBack();
      this.onCloseModal();
      this.modalConfig1.isShowModal = false;
    },
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false, customClass: 'custom-dialog' };
    },
    onDelete(nice_id) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '33rem',
        customClass: 'custom-dialog modaldelete modal-fixed modal-fixed-min',
        props: { title: 'このコメントを完全に削除しますか？' }
      };
      this.nice_id = nice_id;
    },
    deleteLike() {
      this.deleteNice(this.nice_id);
      this.onCloseModal();
    },
    closeModal() {
      this.changeFalseClassHeader();
    },
    /**API */
    getDetail(id, filter) {
      this.isLoading = true;
      F01S02Service.getDetail(id, filter)
        .then(async (res) => {
          const data = res && res.data?.data;
          this.knowledgeDetail = { ...data, release_datetime: moment(data.release_datetime).format('YYYY/M/D') };
          this.evaluation_comments = data.evaluation_comments.records;
          const totalItems = data.evaluation_comments.pagination.total_items;
          const curentPage = data.evaluation_comments.pagination.current_page;
          this.pagination = {
            ...this.pagination,
            totalItems,
            curentPage
          };

          if (this.knowledgeDetail?.knowledge_niked === 1) {
            this.isRegisterLike = true;
          }

          if (this.knowledgeDetail.group_user_file?.length > 0) {
            let listImg = this.knowledgeDetail.group_user_file.map((item) =>
              fetch(item.avatar_image_data).then((resp) => {
                if (resp.ok && resp.status == 200) {
                  return {
                    ...item,
                    avatar_image_data: item.avatar_image_data || this.avataDefault(),
                    res: { ...resp }
                  };
                } else {
                  return {
                    ...item,
                    avatar_image_data: this.avataDefault(),
                    res: { ...resp }
                  };
                }
              })
            );
            await Promise.all(listImg).then(async (res) => {
              let data = await res;
              this.knowledgeDetail.group_user_file = data;
            });
          } else {
            if (this.knowledgeDetail?.avatar_image_data) {
              fetch(this.knowledgeDetail?.avatar_image_data).then((response) => {
                if (!response.ok || response.status != 200) {
                  this.knowledgeDetail.avatar_image_data = this.avataDefault();
                }
              });
            } else {
              this.knowledgeDetail.avatar_image_data = this.avataDefault();
            }
          }

          if (this.evaluation_comments?.length > 0) {
            let listImg = this.evaluation_comments.map((item) =>
              fetch(item.avatar_image_data).then((resp) => {
                if (resp.ok && resp.status == 200) {
                  return {
                    ...item,
                    avatar_image_data: item.avatar_image_data || this.avataDefault(),
                    res: { ...resp }
                  };
                } else {
                  return {
                    ...item,
                    avatar_image_data: this.avataDefault(),
                    res: { ...resp }
                  };
                }
              })
            );
            await Promise.all(listImg).then(async (res) => {
              let data = await res;
              this.evaluation_comments = data;
            });
          }

          this.checkEnableLike();
          this.showEditKnowledBtn();
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(async () => {
          await new Promise((resolve) => setTimeout(resolve, 1000));

          if (this.$refs.groupKnowledInfo) {
            this.$refs.groupKnowledInfo.scrollTop = this.onScrollTop;
          }

          if (this.$refs.knowledgeDetail) {
            this.$refs.knowledgeDetail.scrollTop = this.onScrollTopTimeline;
          }

          this.js_pscroll(0.5);

          this.isLoading = false;
        });
    },

    setScroll() {
      this.onScrollTop = this.$refs.groupKnowledInfo ? this.$refs.groupKnowledInfo.scrollTop : 0;
      this.onScrollTopTimeline = this.$refs.knowledgeDetail ? this.$refs.knowledgeDetail?.scrollTop : 0;
      this.setScrollScreen('F01_S02_KnowledgeDetails', this.onScrollTop);
      this.setScrollScreen('F01_S02_KnowledgeDetails_Timeline', this.onScrollTopTimeline);
    },

    showEditBox(data) {
      this.registerCommentEdit = data;
      this.params_editComment.comment = this.registerCommentEdit.comment;
      this.dataInit = this.params_editComment.comment;
      this.title = '評価コメント';
      this.isEditComment = true;
      this.modalConfig1.title = this.title;
      this.modalConfig1.isShowModal = true;
      this.modalConfig1.isShowClose = true;
    },
    showRegisterBox() {
      this.params_editComment.comment = '';
      this.dataInit = '';
      this.params_editComment.demand_note = this.params_editComment.comment;
      this.title = '要望・要求事項を送信';
      this.isEditComment = false;
      this.modalConfig1.title = this.title;
      this.modalConfig1.isShowModal = true;
      this.modalConfig1.isShowClose = true;
    },
    goToKnowledDetail() {
      this.setScroll();
      this.$router.push({
        name: 'F01_S03_KnowledgeInput',
        params: { knowledge_id: this.knowledgeDetail?.knowledge_id, fromF01S02: 'true' }
      });
    },
    confirmCancel() {
      if (this.params_editComment.comment !== this.dataInit) {
        // Show confirm
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          title: null,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: { mode: 1, textCancel: 'いいえ' },
          isShowClose: false
        };
      } else {
        this.modalConfig1.isShowModal = false;
      }
    },
    forceCloseModal() {
      this.modalConfig1.isShowModal = false;
    },
    registerLike() {
      if (!this.isRegisterLike) {
        this.isRegisterLike = true;
        const param = {
          knowledge_id: this.knowledgeDetail?.knowledge_id,
          create_user_cd: this.knowledgeDetail?.create_user_cd
        };
        F01S02Service.registerLike(param)
          .then((res) => {
            this.getDetail(this.knowledge_id, this.filter);
            this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
            const item = {
              knowledge_id: this.knowledge_id
            };
            this.showEditBox(item);
          })
          .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
          .finally();
      }
    },
    registerKnowledge() {
      this.iconLoading = true;
      const param = {
        knowledge_id: this.knowledgeDetail?.knowledge_id,
        demand_note: this.params_editComment.comment
      };
      F01S02Service.registerKnowledge(param)
        .then((res) => {
          this.getDetail(this.knowledge_id, this.filter);
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
          this.params_editComment.comment = '';
          this.forceCloseModal();
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.iconLoading = false));
    },
    registerComment() {
      this.iconLoading = true;
      const param = {
        knowledge_id: this.knowledgeDetail?.knowledge_id,
        comment: this.params_editComment.comment
      };
      F01S02Service.registerComment(param)
        .then((res) => {
          this.getDetail(this.knowledge_id, this.filter);
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
          this.forceCloseModal();
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.iconLoading = false));
    },
    deleteNice(nice_id) {
      const param = { nice_id: nice_id };
      F01S02Service.deleteNice(param)
        .then((res) => {
          this.getDetail(this.knowledge_id);
          this.$notify({ message: res.data.message || this.getMessage('MSFA0048'), customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally();
    },
    handleCurrentChange(val) {
      this.pagination.curentPage = val;
      this.filter = { ...this.filter, offset: this.pagination.curentPage - 1, limit: 20 };
      if (this.$refs.groupEvalRef) {
        this.$refs.groupEvalRef.scrollTop = 0;
      }
      this.getDetail(this.knowledge_id, this.filter);
    },

    async checkPermission() {
      let user_cd = this.getCurrentUser().user_cd;

      const permission = await Auth.getPolicyRoleService({ user_cd });
      if (permission?.data?.data?.includes('R60')) {
        this.role = true;
      } else {
        this.role = false;
      }
    },
    showBtnDelete() {
      let showDelete = false;
      const approval_user = this.knowledgeDetail?.approval_user_cd.map((s) => s.approval_user_cd);
      const approval_user_cd = approval_user && approval_user.includes(this.user_login_cd);
      if (this.role || approval_user_cd) showDelete = true;
      return showDelete;
    },
    showBtnEdit(nice_user_cd) {
      let showEdit = false;
      // if user login is nice use cd
      if (this.checkUserLogin(nice_user_cd)) showEdit = true;
      return showEdit;
    },
    checkUserLogin(nice_user_cd) {
      const isUseLogin = nice_user_cd === this.user_login_cd;
      return isUseLogin;
    },
    checkEnableLike() {
      const listUserLike = this.evaluation_comments && this.evaluation_comments.map((item) => item.nice_user_cd);
      const isUseLoginLike = listUserLike?.includes(this.user_login_cd);
      return isUseLoginLike;
    },
    getA9(data) {
      if (typeof data === 'string') {
        return [
          {
            user_cd: '',
            user_name: data,
            org_label: ''
          }
        ];
      } else {
        return data;
      }
    },
    showEditKnowledBtn() {
      let isEdit = false;
      const approval_user_cd = this.knowledgeDetail?.approval_user_cd.map((s) => s.approval_user_cd);
      if (this.role || (approval_user_cd && approval_user_cd.includes(this.user_login_cd))) {
        isEdit = true;
      } else {
        isEdit = false;
      }
      return isEdit;
    },
    showDetail(item) {
      const ellipsis = document.getElementById(item?.nice_id);
      ellipsis.classList.remove('text-see-more');

      const show = document.getElementById(`show${item?.nice_id}`);
      const hide = document.getElementById(`hide${item?.nice_id}`);
      show.innerText = '';
      hide.innerText = '元に戻る';
    },
    hideDetail(item) {
      const ellipsis = document.getElementById(item?.nice_id);
      const show = document.getElementById(`show${item?.nice_id}`);
      const hide = document.getElementById(`hide${item?.nice_id}`);
      show.innerText = 'もっと見る';
      hide.innerText = '';
      ellipsis.classList.add('text-see-more');
    },
    contentTextSeeMore(i, content) {
      const word = content;
      if (word && word.length > 0) {
        const count = word?.length > 0 && word?.length < 120;
        return count ? false : true;
      } else return false;
    },
    getformatTimeHourMinute(itemChild) {
      return `${this.formatTimeHourMinute(itemChild.start_datetime)} ～ ${this.formatTimeHourMinute(itemChild.end_datetime)}`;
    },
    getWeek(item) {
      let date = moment(item).format('YYYY-MM-DDTHH:mm:ss');

      const week = new Date(date).getDay();
      if (item) {
        switch (week) {
          case 0:
            return '日';
          case 1:
            return '月';
          case 2:
            return '火';
          case 3:
            return '水';
          case 4:
            return '木';
          case 5:
            return '金';
          case 6:
            return '土';
        }
      }
    },

    goPersonDetail(item) {
      this.setScroll();
      this.$router.push({
        name: 'H02_PersonalDetails',
        params: {
          person_cd: item.person_cd
        },
        query: {
          tab: '1'
        }
      });
    },
    facilityDetail(facility_cd) {
      this.setScroll();
      this.$router.push({
        name: 'H01_FacilityDetails',
        params: {
          facility_cd
        },
        query: { tab: '1' }
      });
    },
    callScreenA01_S04(item) {
      let params = {
        call_id: item.channel_id,
        schedule_id: item.schedule_id
      };
      A01_S04_Service.checkInterviewDetailInput(params)
        .then(() => {
          this.goInterViewDetail(params);
        })
        .catch(() => {
          this.$notify({ message: '面談情報が削除されたため、詳細を見ることができません。', customClass: 'error' });
        })
        .finally(() => {});
    },

    goInterViewDetail(params) {
      this.setScroll();
      this.$router.push({
        name: 'A01_S04_InterviewDetailedInput',
        params: params
      });
    },
    goAccountSetting(item) {
      this.setScroll();
      this.$router.push({
        name: 'Z04_S01_AccountSettings',
        query: {
          user_cd: item.user_cd
        }
      });
    },
    goLectureInput(item) {
      this.setScroll();
      this.$router.push({
        name: 'C01_S02_LectureInput',
        params: {
          convention_id: item?.convention_id,
          schedule_id: item.schedule_id
        }
      });
    },
    goBriefingSessionInput(item) {
      this.setScroll();
      this.$router.push({
        name: 'B01_S02_BriefingSessionInput',
        params: {
          call_id: item.channel_id,
          schedule_id: item.schedule_id
        }
      });
    },

    redirectAccountSetting(user_cd) {
      this.setScroll();
      this.$router.push({ name: 'Z04_S01_AccountSettings', params: { user_cd: user_cd } });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.btn-primary {
  position: relative;
  .textSubmit {
    margin-left: 5px;
    top: 0;
    right: 38%;
    justify-content: center;
    display: flex;
    height: 100%;
    position: absolute;
    align-items: center;
  }
}
.el-icon-loading {
  margin-right: 45px;
  display: none;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.eventNone-canncel {
  pointer-events: none;
  background-color: #f2f2f2a6;
  color: #a8a8a8;
  border-color: #c4c6c8;
}
.eventNone {
  pointer-events: none;
  background: #90c3ff;
  color: #fff9f9c7;
  border-color: #90c3ff;
}
.demo-tabs .custom-tabs-label span {
  margin-left: 7px;
}
.btn.btn-like.active {
  pointer-events: none !important;
}

.modal-evaluation {
  .evaluation-btn {
    text-align: center;
    margin-top: 20px;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
.boxBody {
  background: #fcfcfc;
  height: 100%;
}
.groupKnowled {
  display: flex;
  flex-wrap: wrap;
  height: 100%;
  padding-top: 28px;
  .groupKnowled-left {
    margin-top: -28px;
    width: 33%;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    @media (max-width: $viewport_tablet) {
      width: 100%;
      height: initial;
    }
  }
  .groupKnowled-right {
    width: 67%;
    height: 100%;
    background: #f7f7f7;
    box-shadow: inset 0px -3px 10px rgba(0, 0, 0, 0.05), inset 0px 3px 10px rgba(0, 0, 0, 0.05);
    border-radius: 20px 0px 0px 0px;
    @media (max-width: $viewport_tablet) {
      width: 100%;
      height: initial;
      margin-left: 24px;
      margin-top: 24px;
    }
  }
  .knowled-head {
    border-bottom: 1px solid #b7c3cb;
    margin: 0 34px;
    @media (max-width: $viewport_desktop) {
      margin: 0 16px;
    }
    .knowled-label {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding-left: 30px;
      gap: 10px;
      margin-bottom: 10px;
      @media (max-width: $viewport_desktop) {
        padding-left: 0;
        align-items: center;
      }
      .head-label {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        column-gap: 16px;
        .spanLabel {
          padding: 2px 16px;
          display: inline-block;
          &.spanLabel--category {
            background: #e2e4ff;
            border-radius: 20px;
            color: #555fb0;
            font-weight: 500;
          }
        }
        .span-date {
          color: #99a5ae;
          font-size: 1rem;
        }
      }

      .head-tag {
        min-width: 74px;
        ul {
          display: flex;
          flex-wrap: wrap;
          justify-content: flex-end;
          li {
            margin-right: 8px;
            &:last-child {
              margin: 0;
            }
          }
        }
      }
    }
  }
  .knowled-title {
    .knowled-tlt {
      display: flex;
      line-height: 140%;
      font-size: 1.5rem;
      .knowled-item {
        min-width: 24px;
        margin: -2px 6px 0 0;
      }
    }
    .knowled-id {
      padding-left: 30px;
      font-size: 1rem;
    }
  }

  .custom-k {
    margin-top: 16px;
    padding: 16px 0;
    border-top: 1px solid #b7c3cb;
  }
  .knowledThumb {
    ul {
      display: flex;
      flex-wrap: wrap;
      padding-left: 12px;
      li {
        display: flex;
        position: relative;
        height: 50px;
        width: 50px;
        border-radius: 50%;
        border: 1px solid #e3e3e3;
        background: #ffff;
        margin-left: -12px;
        z-index: var(--offset);
        &:nth-child(1) {
          margin-left: -12px;
          border: 1px solid #ff8b25;
        }
        .thumb-border {
          border-radius: 25px;
          display: block;
          margin: auto;
          height: 48px;
        }
      }
    }
  }
  .knowledAuthor {
    .knowledAuthor-title {
      padding-top: 16px;
      display: flex;
      color: #448add;
      font-weight: 700;
      font-size: 1.125rem;
      .knowledAuthor-item {
        min-width: 22px;
        margin: -2px 6px 0 0;
      }
    }
    .knowledAuthor-label {
      display: flex;
      flex-wrap: wrap;
      padding-top: 13px;
      .knowledAuthor-tlt {
        width: 88px;
        font-size: 1rem;
        line-height: 120%;
      }
      .knowledAuthor-name {
        width: calc(100% - 88px);
        padding-left: 5px;
      }
    }
  }
  .Knowled-content {
    height: 80vh;
    margin: 0 34px;
    padding-right: 12px;
    @media (max-width: $viewport_desktop) {
      margin: 0 16px;
    }
    @media (max-width: $viewport_tablet) {
      height: 50vh;
    }
    .KnowledLst {
      padding: 16px 0;
      margin-bottom: 16px;
      border-bottom: 1px solid #b7c3cb;
      ul {
        li {
          display: flex;
          flex-wrap: wrap;
          margin-bottom: 6px;
          font-size: 1rem;
          &:last-child {
            margin-bottom: 0;
          }
          .KnowledLst-tlt {
            width: 124px;
            padding-right: 24px;
          }
          .KnowledLst-label {
            width: calc(100% - 124px);
            color: #2d3033;
            .KnowledLst-blue {
              color: #448add;
              cursor: pointer;

              span {
                padding: 0 8px;

                &:first-child {
                  padding-left: 0;
                }
              }
            }
          }
        }
      }
    }
    .KnowledContents {
      .KnowledContents-label,
      .KnowledContents-text {
        font-size: 1rem;
      }
      .KnowledContents-text {
        color: #2d3033;
        margin-top: 8px;
      }
    }
  }
  .Knowled-btn {
    padding: 12px 34px 0;
    text-align: right;
    @media (max-width: $viewport_desktop) {
      padding: 12px 24px 0;
    }
    .btn {
      width: 190px;
    }
  }
  .groupTabs {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 100%;
    .groupTabs-nav {
      margin: 0 24px;
      @media (max-width: $viewport_desktop) {
        margin: 0 12px;
      }
      .listEval > ul {
        li {
          width: 100%;
        }
      }
    }
    .groupTabs-content {
      overflow: hidden;
      padding: 20px 0 10px;
    }
    .groupTabs-content,
    .tab-content,
    .tab-pane {
      height: 100%;
    }
    .groupEval {
      display: flex;
      flex-direction: column;
      overflow: hidden;
      height: 100%;
      .groupEval-info {
        height: 100%;
      }
      .btnLike {
        display: flex;
        flex-wrap: wrap;
        padding: 0 0px;
        @media (max-width: $viewport_desktop) {
          padding: 0 12px;
        }
        .btn {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 100px;
          margin-right: 10px;
        }
        .btnLike-number {
          padding: 3px 0 0;
          color: #99a5ae;
          font-size: 1rem;
        }
      }
      .listEval {
        padding: 20px 0 8px 0px;
        @media (max-width: $viewport_desktop) {
          padding: 20px 0 8px 12px;
        }
        > ul {
          > li {
            background: #ffffff;
            box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
            border-radius: 10px 0px 0px 10px;
            padding: 16px 20px 20px;
            margin-bottom: 12px;
            &:last-child {
              margin-bottom: 0;
            }
          }
        }
        .listEval-head {
          display: flex;
          justify-content: space-between;
          border-bottom: 1px solid #b7c3cb;
          padding-bottom: 16px;
          .listEval-info {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            .listEval-thumb {
              width: 50px;
              height: 50px;
              overflow: hidden;
              border-radius: 50%;
              border: 1px solid #cdced9;
              img {
                width: 100%;
              }
            }
            .listEval-tlt {
              width: calc(100% - 50px);
              padding-left: 12px;
              .tlt {
                line-height: 120%;
                font-weight: 700;
                font-size: 1rem;
              }
            }
          }
          .listEval-btn {
            min-width: 42px;
            margin-left: 10px;
            img {
              width: 19px;
            }
          }
        }
        .listEval-cmt {
          padding-top: 16px;
        }
      }
      .paginationEval {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 10px 24px 10px;
        @media (max-width: $viewport_desktop) {
          padding: 10px 12px 10px;
        }
        .pagination-cases {
          color: #8e8e8e;
          font-weight: 500;
          padding-right: 10px;
        }
      }
    }
  }
  .dailyList {
    height: 100%;
    padding: 4px 24px 0;
    @media (max-width: $viewport_desktop) {
      padding: 0 12px;
    }

    .dailyList-sub {
      li {
        padding: 7px 0 13px;
      }
    }
    > ul {
      position: relative;
      min-height: 100%;
      &::before {
        position: absolute;
        left: 204px;
        content: '';
        height: 100%;
        width: 2px;
        background: #b7c3cb;
        display: block;
        @media (max-width: $viewport_desktop) {
          left: 140px;
          top: 0;
        }
      }
      > li {
        .dailyList-flex {
          display: flex;
          flex-wrap: wrap;
          align-items: flex-start;
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
        .dailyList-content {
          width: calc(100% - 260px);
          position: relative;
          @media (max-width: $viewport_desktop) {
            width: calc(100% - 187px);
          }
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
                width: 96px;
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
                  cursor: pointer;
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
                &::after {
                  position: absolute;
                  top: 5px;
                  right: 0px;
                  content: '';
                  border-left: 6px solid transparent;
                  border-right: 6px solid transparent;
                  border-top: 6px solid #448add;
                  transition: 400ms all;
                }
                &[aria-expanded='true'] {
                  &::after {
                    transform: rotate(180deg);
                  }
                }
              }
              .article-body {
                background: #f7f7f7;
                box-shadow: inset 0px 0px 10px rgba(183, 195, 203, 0.3);
                border-radius: 4px;
                padding: 8px 14px 12px;
                width: calc(100% + 47px);
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
            a {
              padding-right: 20px;
              background: url(~@/assets/template/images/icon_arrow-right-blue.svg) no-repeat right;
              color: #448add;
              cursor: pointer;
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
        }
      }
    }
  }
}
.text-see-more {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  line-height: 20px;
  max-height: 190px;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  position: relative;
}
.m-8 {
  margin-top: 8px;
}
.stroke-like {
  stroke: #fe6e54 !important;
}
.container-custom {
  display: flex;
  justify-content: start;
  flex-wrap: wrap;
}
.listEval-txt,
.KnowledContents-text {
  white-space: break-spaces !important;
}
.break-spaces {
  min-width: 0;
  white-space: break-spaces;
}
</style>
