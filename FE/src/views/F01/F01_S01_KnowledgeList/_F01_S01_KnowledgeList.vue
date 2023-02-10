<template>
  <div class="wrapContent">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo">
          <button class="btn btn-sign">
            <span class="btn-iconLeft"> <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_plus.svg')" alt="" /> </span
            >新規登録
          </button>
        </div>
        <div class="btnInfo btnInfo--change">
          <div class="btnInfo-btn">
            <button class="btn btn-filter" data-toggle="dropdown">
              <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
            </button>
            <div class="dropdown-menu dropdown-filter dropdown-knowled">
              <div class="item-filter btn-close-filter">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
              </div>
              <h2 class="title-filter">検索条件</h2>
              <div class="filterKnowled scrollbar">
                <!--  -->
                <div class="form-knowled">
                  <ul>
                    <li class="w-100">
                      <label class="knowled-label">タイトル</label>
                      <div class="knowled-control">
                        <el-input clearable placeholder="タイトルを入力..." class="form-control-input" />
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">カテゴリ</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">ナレッジID</label>
                      <div class="knowled-control">
                        <el-input clearable placeholder="ナレッジIDを入力..." class="form-control-input" />
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">公開日</label>
                      <div class="knowled-dateTime">
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="value1"
                            format="YYYY/M/D"
                            type="date"
                            :editable="false"
                            placeholder="開始日"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                        <div class="dateTime-item">～</div>
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="value1"
                            format="YYYY/M/D"
                            type="date"
                            :editable="false"
                            placeholder="終了日"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">品目</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <el-input clearable placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <el-input clearable placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設個人</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <el-input clearable placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設区分分類</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">診療科目分類</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">地区</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <el-input clearable placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">ナレッジ元作成者</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <el-input clearable placeholder="未選択" class="form-control-input" />
                        </div>
                      </div>
                      <div class="knowledAuthor">
                        <ul>
                          <li class="knowledAuthor-yellow">
                            <div class="knowledAuthor-item">1</div>
                            <div class="knowledAuthor-txt">
                              <span class="knowledAuthor-label">大阪支店</span>
                              <span class="knowledAuthor-labelName">ミュートス 次郎</span>
                            </div>
                            <div class="knowledAuthor-like">
                              <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                              <span class="number-like">100</span>
                            </div>
                          </li>
                          <li class="knowledAuthor-blue">
                            <div class="knowledAuthor-item">2</div>
                            <div class="knowledAuthor-txt">
                              <span class="knowledAuthor-label">名古屋支店</span>
                              <span class="knowledAuthor-labelName">ミュートス 花子</span>
                            </div>
                            <div class="knowledAuthor-like">
                              <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                              <span class="number-like">80</span>
                            </div>
                          </li>
                          <li class="knowledAuthor-red">
                            <div class="knowledAuthor-item">3</div>
                            <div class="knowledAuthor-txt">
                              <span class="knowledAuthor-label">東京支店</span>
                              <span class="knowledAuthor-labelName">ミュートス 一郎</span>
                            </div>
                            <div class="knowledAuthor-like">
                              <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                              <span class="number-like">60</span>
                            </div>
                          </li>
                          <li>
                            <div class="knowledAuthor-item">4</div>
                            <div class="knowledAuthor-txt">
                              <span class="knowledAuthor-label">東京支店</span>
                              <span class="knowledAuthor-labelName">ミュートス 智子</span>
                            </div>
                            <div class="knowledAuthor-like">
                              <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                              <span class="number-like">50</span>
                            </div>
                          </li>
                          <li>
                            <div class="knowledAuthor-item">5</div>
                            <div class="knowledAuthor-txt">
                              <span class="knowledAuthor-label">北海道支店</span>
                              <span class="knowledAuthor-labelName">ミュートス 隆</span>
                            </div>
                            <div class="knowledAuthor-like">
                              <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                              <span class="number-like">40</span>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">表示順</label>
                      <div class="displayOrder">
                        <ul>
                          <li>
                            <button class="btn btn-select active">最新順</button>
                          </li>
                          <li>
                            <button class="btn btn-select">いいね件数順</button>
                          </li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>
                <!--  -->
                <div class="form-knowled">
                  <ul>
                    <li class="w-100">
                      <label class="knowled-label">ステータス</label>
                      <div class="knowled-status">
                        <ul>
                          <li>
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input class="custom-control-input" type="checkbox" checked />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">作成中</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input class="custom-control-input" type="checkbox" checked />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">承認確認中</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input class="custom-control-input" type="checkbox" />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">公開待ち</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input class="custom-control-input" type="checkbox" />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">公開中</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input class="custom-control-input" type="checkbox" checked />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">不採用</span>
                            </label>
                          </li>
                          <li>
                            <label class="custom-control custom-checkbox custom-control--bordGreen">
                              <input class="custom-control-input" type="checkbox" />
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">非公開</span>
                            </label>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">タイトル</label>
                      <div class="knowled-control">
                        <el-input clearable placeholder="内容入力..." class="form-control-input" />
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">ナレッジID</label>
                      <div class="knowled-control">
                        <el-input clearable placeholder="内容入力..." class="form-control-input" />
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">カテゴリ</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">ナレッジ元作成者</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <input class="form-control" type="text" placeholder="未選択" />
                        </div>
                      </div>
                    </li>
                    <li class="w-100">
                      <label class="knowled-label">公開日</label>
                      <div class="knowled-dateTime">
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="value1"
                            format="YYYY/M/D"
                            type="date"
                            :editable="false"
                            placeholder="開始日"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                        <div class="dateTime-item">～</div>
                        <div class="form-icon icon-left form-icon--noBod">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                          </span>
                          <el-date-picker
                            v-model="value1"
                            type="date"
                            :editable="false"
                            placeholder="終了日"
                            class="form-control-datePickerLeft"
                          ></el-date-picker>
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">品目</label>
                      <div class="knowled-control">
                        <div class="form-icon icon-right">
                          <span class="icon">
                            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_popup.svg')" alt="" />
                          </span>
                          <input class="form-control" type="text" placeholder="未選択" />
                        </div>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設個人</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">施設区分分類</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">診療科目分類</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                    <li>
                      <label class="knowled-label">地区</label>
                      <div class="knowled-control">
                        <el-select v-model="value" placeholder="未選択" class="form-control-select">
                          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
                        </el-select>
                      </div>
                    </li>
                  </ul>
                </div>
                <!--  -->
                <div class="knowled-btn">
                  <button type="button" class="btn btn-outline-primary">キャンセル</button>
                  <button type="button" class="btn btn-primary">検索</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="listKnowled scrollbar">
        <ul>
          <li>
            <div class="itemRibbon">
              <img src="@/assets/template/images/icon_ribbon-tag-new.svg" alt="" />
            </div>
            <div class="listKnowled-row">
              <div class="listKnowled-content">
                <div class="listKnowled-head">
                  <div class="listKnowled-left">
                    <h2 class="listKnowled-title">
                      <a href="#">
                        <span class="item"><img src="@/assets/template/images/icon_light-bulb01.svg" alt="" /></span>サンプルコンテンツ
                      </a>
                    </h2>
                    <p class="listKnowled-id">ナレッジID：<span class="idCode">10002</span></p>
                  </div>
                  <div class="listKnowled-right">
                    <div class="listKnowled-label">
                      <span class="spanLabel spanLabel--status">ステータス</span>
                      <span class="spanLabel spanLabel--category">カテゴリー</span>
                    </div>
                    <div class="viewLike">
                      <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                      <span class="number-like">100</span>
                    </div>
                  </div>
                </div>
                <div class="listKnowled-text">
                  <p class="listKnowled-txt">
                    サンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプ
                    ルサンプルサンプルサンプルサンプルサンプルサンプルルサンプルサンプルサンプルサンプルサンプルサ
                    ンプルルサンプルサンプルサンプルサンプルサンプルサンプルルサンプルサンプルサンプルサンプルサン
                    プルサンプルルサンプルサンプルサンプルサンプルサンプルサンプル...
                  </p>
                </div>
              </div>
              <div class="listKnowled-info">
                <ul>
                  <li class="listKnowled-li">
                    <div class="listKnowled-item"><span class="listKnowled-label">品目：</span>AAA</div>
                    <div class="listKnowled-subject"><span class="listKnowled-label">診療科目分類：</span>小児</div>
                  </li>
                  <li><span class="listKnowled-label">施設区分分類：</span>病院</li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_hospital-user.svg" alt="" /></span>
                    〇〇〇病院　田中 一郎　
                  </li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_maps.svg" alt="" /></span>
                    大阪府　大阪市中央区
                  </li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_user-edit01.svg" alt="" /></span>
                    〇〇〇〇〇〇〇〇〇〇〇店　ミュートス 太郎
                  </li>
                </ul>
              </div>
            </div>
          </li>
          <li>
            <div class="itemRibbon">
              <img src="@/assets/template/images/icon_ribbon-tag-update.svg" alt="" />
            </div>
            <div class="listKnowled-row">
              <div class="listKnowled-content">
                <div class="listKnowled-head">
                  <div class="listKnowled-left">
                    <h2 class="listKnowled-title">
                      <a href="#">
                        <span class="item"><img src="@/assets/template/images/icon_light-bulb01.svg" alt="" /></span>サンプルコンテンツ
                      </a>
                    </h2>
                    <p class="listKnowled-id">ナレッジID：<span class="idCode">10002</span></p>
                  </div>
                  <div class="listKnowled-right">
                    <div class="listKnowled-label">
                      <span class="spanLabel spanLabel--status">ステータス</span>
                      <span class="spanLabel spanLabel--category">カテゴリー</span>
                    </div>
                    <div class="viewLike">
                      <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                      <span class="number-like">100</span>
                    </div>
                  </div>
                </div>
                <div class="listKnowled-text">
                  <p class="listKnowled-txt">
                    サンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプ
                    ルサンプルサンプルサンプルサンプルサンプルサンプルルサンプルサンプルサンプルサンプルサンプルサ
                    ンプルルサンプルサンプルサンプルサンプルサンプルサンプルルサンプルサンプルサンプルサンプルサン
                    プルサンプルルサンプルサンプルサンプルサンプルサンプルサンプル...
                  </p>
                </div>
              </div>
              <div class="listKnowled-info">
                <ul>
                  <li class="listKnowled-li">
                    <div class="listKnowled-item"><span class="listKnowled-label">品目：</span>AAA</div>
                    <div class="listKnowled-subject"><span class="listKnowled-label">診療科目分類：</span>小児</div>
                  </li>
                  <li><span class="listKnowled-label">施設区分分類：</span>病院</li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_hospital-user.svg" alt="" /></span>
                    〇〇〇病院　田中 一郎　
                  </li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_maps.svg" alt="" /></span>
                    大阪府　大阪市中央区
                  </li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_user-edit01.svg" alt="" /></span>
                    〇〇〇〇〇〇〇〇〇〇〇店　ミュートス 太郎
                  </li>
                </ul>
              </div>
            </div>
          </li>
          <li>
            <div class="listKnowled-row">
              <div class="listKnowled-content">
                <div class="listKnowled-head">
                  <div class="listKnowled-left">
                    <h2 class="listKnowled-title">
                      <a href="#">
                        <span class="item"><img src="@/assets/template/images/icon_light-bulb01.svg" alt="" /></span>サンプルコンテンツ
                      </a>
                    </h2>
                    <p class="listKnowled-id">ナレッジID：<span class="idCode">10002</span></p>
                  </div>
                  <div class="listKnowled-right">
                    <div class="listKnowled-label">
                      <span class="spanLabel spanLabel--status">ステータス</span>
                      <span class="spanLabel spanLabel--category">カテゴリー</span>
                    </div>
                    <div class="viewLike">
                      <span class="item-like"><img src="@/assets/template/images/icon_like.svg" alt="" /></span>
                      <span class="number-like">100</span>
                    </div>
                  </div>
                </div>
                <div class="listKnowled-text">
                  <p class="listKnowled-txt">
                    サンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプルサンプ
                    ルサンプルサンプルサンプルサンプルサンプルサンプルルサンプルサンプルサンプルサンプルサンプルサ
                    ンプルルサンプルサンプルサンプルサンプルサンプルサンプルルサンプルサンプルサンプルサンプルサン
                    プルサンプルルサンプルサンプルサンプルサンプルサンプルサンプル...
                  </p>
                </div>
              </div>
              <div class="listKnowled-info">
                <ul>
                  <li class="listKnowled-li">
                    <div class="listKnowled-item"><span class="listKnowled-label">品目：</span>AAA</div>
                    <div class="listKnowled-subject"><span class="listKnowled-label">診療科目分類：</span>小児</div>
                  </li>
                  <li><span class="listKnowled-label">施設区分分類：</span>病院</li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_hospital-user.svg" alt="" /></span>
                    〇〇〇病院　田中 一郎　
                  </li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_maps.svg" alt="" /></span>
                    大阪府　大阪市中央区
                  </li>
                  <li class="listKnowled-liItem">
                    <span class="item"><img src="@/assets/template/images/icon_user-edit01.svg" alt="" /></span>
                    〇〇〇〇〇〇〇〇〇〇〇店　ミュートス 太郎
                  </li>
                </ul>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="pagination">
      <div class="pagination-cases">全○○○○○件</div>
      <el-pagination layout="prev, pager, next" :total="1000" class="pagination-bord"></el-pagination>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {}
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_sm: 767px;
.wrapBtn {
  .dropdown-knowled {
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
    .filterKnowled {
      width: calc(100% + 40px);
      height: calc(100vh - 200px);
      margin-left: -20px;
      padding: 0 20px;
    }
    .form-knowled {
      > ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        > li {
          width: 50%;
          padding-left: 10px;
          margin-top: 10px;
        }
      }
      .knowled-label {
        color: #5f6b73;
        font-size: 0.875rem;
        margin-bottom: 6px;
      }
      .knowled-dateTime {
        display: flex;
        align-items: center;
        .form-icon {
          width: calc(50% - 16px);
        }
        .dateTime-item {
          width: 32px;
          text-align: center;
          color: #727f88;
          font-size: 1rem;
        }
      }
      .knowledAuthor {
        margin-top: 8px;
        ul {
          li {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            background: #fff;
            border-bottom: 1px solid #f9f9f9;
            &.knowledAuthor-yellow {
              .knowledAuthor-item {
                color: #fff;
                background: linear-gradient(28.06deg, rgba(245, 193, 57, 0.9) 55.53%, rgba(254, 230, 146, 0.5) 95.13%);
              }
              .knowledAuthor-txt {
                color: #f1b86a;
              }
            }
            &.knowledAuthor-blue {
              .knowledAuthor-item {
                color: #fff;
                background: linear-gradient(30.15deg, rgba(138, 174, 186, 0.8) 54.55%, rgba(197, 222, 230, 0.8) 91.27%);
              }
              .knowledAuthor-txt {
                color: #99c0c0;
              }
            }
            &.knowledAuthor-red {
              .knowledAuthor-item {
                color: #fff;
                background: linear-gradient(28.12deg, rgba(214, 134, 109, 0.8) 54.14%, rgba(253, 216, 143, 0.8) 92.17%);
              }
              .knowledAuthor-txt {
                color: #bda5a5;
              }
            }
          }
        }
        .knowledAuthor-item {
          width: 30px;
          background: #e3e3e3;
          border-radius: 0px 30px 30px 0px;
          height: 26px;
          color: #5f6b73;
          font-size: 1rem;
          line-height: 120%;
          font-weight: 700;
          display: flex;
          justify-content: center;
          align-items: center;
        }
        .knowledAuthor-txt {
          width: calc(100% - 98px);
          padding: 0 10px 0 15px;
          color: #5f6b73;
          display: flex;
          flex-wrap: wrap;
          .knowledAuthor-label {
            width: 90px;
            padding-right: 10px;
          }
          .knowledAuthor-labelName {
            width: calc(100% - 90px);
          }
          .knowledAuthor-label,
          .knowledAuthor-labelName {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
          }
        }
        .knowledAuthor-like {
          width: 68px;
          display: flex;
          .item-like {
            min-width: 12px;
            margin: -2px 2px 0 0;
          }
          .number-like {
            color: #727f88;
            font-weight: 700;
          }
        }
      }
      .displayOrder {
        ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: 1px;
          li {
            width: 33.333333%;
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
    .knowled-btn {
      margin-top: 24px;
      text-align: center;
      .btn {
        width: 142px;
        margin-right: 14px;
        &:last-child {
          margin-right: 0;
        }
      }
    }
    .knowled-status {
      margin-top: -6px;
      ul {
        display: flex;
        flex-wrap: wrap;
        margin-left: -15px;
        li {
          margin-top: 6px;
          padding-left: 15px;
          width: 33.333333%;
          .custom-control,
          .custom-control-description {
            width: 100%;
          }
          .custom-control {
            padding-right: 6px;
          }
        }
      }
    }
  }
}
.listKnowled {
  height: 100%;
  padding: 8px 24px 0;
  > ul {
    > li {
      margin-bottom: 10px;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
      padding: 20px 20px 20px 40px;
      position: relative;
    }
  }
  .itemRibbon {
    position: absolute;
    top: -4px;
    left: 8px;
    width: 40px;
    height: 32px;
  }
  .listKnowled-row {
    display: flex;
    flex-wrap: wrap;
    .listKnowled-content {
      width: calc(100% - 356px);
      padding-right: 24px;
      border-right: 1px solid #e3e3e3;
      @media (max-width: $viewport_tablet) {
        width: calc(100% - 300px);
      }
    }
    .listKnowled-info {
      width: 356px;
      padding-left: 24px;
      @media (max-width: $viewport_tablet) {
        width: 300px;
      }
      ul {
        li {
          color: #2d3033;
          margin-bottom: 5px;
          &:last-child {
            margin-bottom: 0;
          }
          .listKnowled-label {
            color: #727f88;
          }
          &.listKnowled-li {
            display: flex;
            flex-wrap: wrap;
            .listKnowled-item {
              min-width: 130px;
              padding-right: 12px;
            }
          }
          &.listKnowled-liItem {
            display: flex;
            .item {
              min-width: 22px;
              margin: -2px 6px 0 0;
            }
          }
        }
      }
    }
    .listKnowled-head {
      display: flex;
      flex-wrap: wrap;
      .listKnowled-left {
        min-width: 300px;
        .listKnowled-title {
          font-size: 1.375rem;
          font-weight: 500;
          line-height: 100%;
          a {
            display: flex;
            .item {
              min-width: 25px;
              margin: -2px 7px 0 0;
            }
          }
        }
        .listKnowled-id {
          padding: 0 0 0 32px;
          color: #727f88;
          .idCode {
            color: #2d3033;
          }
        }
      }
      .listKnowled-right {
        min-width: 216px;
        margin-left: auto;
        padding-left: 10px;
        text-align: right;
        .listKnowled-label {
          .spanLabel {
            padding: 2px 16px;
            margin-right: 10px;
            display: inline-block;
            &:last-child {
              margin-right: 0;
            }
            &.spanLabel--status {
              background: #daf8dc;
              color: #28a470;
              border-radius: 2px;
            }
            &.spanLabel--category {
              font-weight: 500;
              background: #e2e4ff;
              color: #555fb0;
              border-radius: 20px;
            }
          }
        }
        .viewLike {
          display: flex;
          justify-content: flex-end;
          .item-like {
            min-width: 15px;
            margin: -2px 4px 0 0;
          }
          .number-like {
            color: #727f88;
            font-size: 1rem;
            font-weight: 700;
          }
        }
      }
    }
    .listKnowled-text {
      margin-top: 10px;
    }
  }
}
</style>
