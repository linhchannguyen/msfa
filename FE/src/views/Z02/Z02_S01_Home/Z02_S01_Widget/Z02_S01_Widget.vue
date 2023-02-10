<script>
export default {
  name: 'Z02_S01_Widget',
  props: {
    data: [Object || Array]
  },
  data() {
    return {
      loader: true,
      widget: [],
      widgetOneTotal: 0,
      widgetThreeTotal: 0,
      widgetFour: 0
    };
  },
  computed: {
    roleUser() {
      return this.$store.state.auth.policyRole?.includes('R50') ? 'R50' : '';
    }
  },
  watch: {
    data(e) {
      this.widget = {
        ...e,
        knowledge_remand_total: e.knowledge_remand?.total_record,
        knowledge_id_list: e.knowledge_remand?.knowledge_id_list,
        knowledge_unapproved_total: e.knowledge_unapproved?.[0]?.total_record,
        un_knowledge_id_list: e.knowledge_unapproved?.[0]?.knowledge_id_list
      };
      this.sumTotal();
    }
  },
  mounted() {},
  methods: {
    sumTotal() {
      const {
        daily_report_remand,
        briefing_remand,
        convention_remand,
        knowledge_remand_total,
        unapproved,
        briefing_unapproved,
        convention_unapproved,
        knowledge_unapproved_total,
        inappropriate_report,
        person_unconfirm,
        facility_unconfirm
      } = this.widget;
      this.widgetOneTotal = [daily_report_remand, briefing_remand, convention_remand, knowledge_remand_total].reduce((a, b) => a + b, 0);
      this.widgetThreeTotal = [unapproved, briefing_unapproved, convention_unapproved, knowledge_unapproved_total].reduce((a, b) => a + b, 0);
      let user_role = this.$store.state.auth.policyRole?.includes('R50') ? 'R50' : '';
      if (user_role !== '') {
        this.widgetFour = [inappropriate_report, person_unconfirm, facility_unconfirm].reduce((a, b) => a + b, 0);
      } else {
        this.widgetFour = [person_unconfirm, facility_unconfirm].reduce((a, b) => a + b, 0);
      }
      this.loader = false;
    },
    routeGo(name, query, params) {
      localStorage.removeItem('$_D');
      this.$router.push({ name, query, params });
    },
    showDropdown(id) {
      setTimeout(() => {
        const expanded = document.getElementById(id).getAttribute('aria-expanded');
        const sub = document.getElementById(id).querySelector('.subject > span');
        const widget = document.getElementById(id);
        const dropdown = widget.closest('.btnNav').lastChild;
        // eslint-disable-next-line eqeqeq
        if (expanded == 'true') {
          sub.classList.add('upSlide');
          dropdown.classList.add('slideDown');
          dropdown.classList.remove('slideUp');
          sub.classList.remove('downSlide');
        } else {
          sub.classList.remove('upSlide');
          dropdown.classList.add('slideUp');
          setTimeout(() => {
            dropdown.classList.remove('slideUp');
            sub.classList.add('downSlide');
          }, 503);
          dropdown.classList.remove('slideDown');
        }
      });
    }
  }
};
</script>
<template>
  <ul>
    <li>
      <div class="btnNav btnNav--red">
        <div
          id="widget_1"
          class="btnNav-content"
          :data-toggle="widgetOneTotal > 0 ? 'dropdown' : ''"
          @click="widgetOneTotal > 0 ? showDropdown('widget_1') : ''"
        >
          <div class="btnNav-info">
            <p class="tlt">否認・差戻しあり</p>
            <p class="subject">
              <span class="downSlide">{{ widgetOneTotal }}件</span>
            </p>
          </div>
          <div class="btnNav-item">
            <img src="@/assets/template/images/icon_box-menu.svg" alt="" />
          </div>
        </div>
        <div class="dropdown-menu dropdown-menu--nav slideDown">
          <ul>
            <li class="widget_one" @click="routeGo('A03_S01_JapaneseDailyReportList', null, { back: false })">
              <a>差戻された報告が{{ widget?.daily_report_remand }}件あります。</a>
            </li>
            <li class="widget_one" @click="routeGo('B01_S01_BriefingSearch', null, { remand_flag: 1, back: false })">
              <a> 差戻された説明会が{{ widget?.briefing_remand }}件あります。</a>
            </li>
            <li class="widget_one" @click="routeGo('C01_S01_LectureList', null, { remand_flag: 1, back: false })">
              <a>差戻された講演会が{{ widget?.convention_remand }}件あります。</a>
            </li>
            <li class="widget_one" @click="routeGo('F01_S05_Pre_ReleaseKnowledgeManagement', null, { back: false, knowledge_id: widget.knowledge_id_list })">
              <a>差戻されたナレッジが{{ widget?.knowledge_remand_total }}件あります。</a>
            </li>
          </ul>
        </div>
      </div>
    </li>
    <li>
      <div class="btnNav btnNav--red">
        <div
          id="widget_2"
          class="btnNav-content"
          :data-toggle="widget.unsubmitted > 0 ? 'dropdown' : ''"
          @click="widget.unsubmitted > 0 ? showDropdown('widget_2') : ''"
        >
          <div class="btnNav-info">
            <p class="tlt">未提出あり</p>
            <p class="subject">
              <span class="downSlide">{{ widget.unsubmitted ? widget.unsubmitted : 0 }}件</span>
            </p>
          </div>
          <div class="btnNav-item">
            <img src="@/assets/template/images/icon_box-menu01.svg" alt="" />
          </div>
        </div>
        <div class="dropdown-menu dropdown-menu--nav slideDown">
          <ul>
            <li key="1" class="widget_two" @click="routeGo('A03_S01_JapaneseDailyReportList', null, { back: false })">
              <a>未提出の報告が{{ widget.unsubmitted }}件あります。</a>
            </li>
            <!-- <li><a href="#">未承認説明会が1件あります。</a></li>
            <li><a href="#">未承認講演会が1件あります。</a></li>
            <li><a href="#">未承認ナレッジが1件あります。</a></li> -->
          </ul>
        </div>
      </div>
    </li>
    <li>
      <div class="btnNav btnNav--red">
        <div
          id="widget_3"
          class="btnNav-content"
          :data-toggle="widgetThreeTotal > 0 ? 'dropdown' : ''"
          @click="widgetThreeTotal > 0 ? showDropdown('widget_3') : ''"
        >
          <div class="btnNav-info">
            <p class="tlt">未承認あり</p>
            <p class="subject">
              <span class="downSlide">{{ widgetThreeTotal }}件</span>
            </p>
          </div>
          <div class="btnNav-item">
            <img src="@/assets/template/images/icon_box-menu01.svg" alt="" />
          </div>
        </div>
        <div class="dropdown-menu dropdown-menu--nav slideDown">
          <ul>
            <li class="widget_three" @click="routeGo('A03_S03_NotApprovedList', null, { back: false })">
              <a>未承認の報告が{{ widget.unapproved }}件あります。</a>
            </li>
            <li class="widget_three" @click="routeGo('B01_S01_BriefingSearch', null, { approval_flag: 1, back: false })">
              <a>未承認の説明会が{{ widget.briefing_unapproved }}件あります。</a>
            </li>
            <li class="widget_three" @click="routeGo('C01_S01_LectureList', null, { approved_flag: 1, back: false })">
              <a>未承認の講演会が{{ widget.convention_unapproved }}件あります。</a>
            </li>
            <li
              class="widget_three"
              @click="
                routeGo(
                  'F01_S05_Pre_ReleaseKnowledgeManagement',
                  null,

                  { back: false, knowledge_id: widget.un_knowledge_id_list }
                )
              "
            >
              <a>未承認のナレッジが{{ widget.knowledge_unapproved_total }}件あります。</a>
            </li>
          </ul>
        </div>
      </div>
    </li>
    <li>
      <div class="btnNav btnNav--yellow">
        <div id="widget_4" class="btnNav-content" :data-toggle="widgetFour > 0 ? 'dropdown' : ''" @click="widgetFour > 0 ? showDropdown('widget_4') : ''">
          <div class="btnNav-info">
            <p class="tlt">確認依頼あり</p>
            <p class="subject">
              <span class="downSlide">{{ widgetFour }}件</span>
            </p>
          </div>
          <div class="btnNav-item">
            <img src="@/assets/template/images/icon_box-menu02.svg" alt="" />
          </div>
        </div>
        <div class="dropdown-menu dropdown-menu--nav slideDown">
          <ul>
            <li v-if="roleUser === 'R50'" class="widget_four" @click="routeGo('E01_S01_QaSearch', null, { back: false, unsuitable_report: 1 })">
              <a>QA問題報告の確認依頼が{{ widget.inappropriate_report }}件あります。</a>
            </li>
            <li class="widget_four" @click="routeGo('H02_S01_PersonalSearch', null, { precaution_id: 3, back: false })">
              <a> 個人注意事項の確認依頼が{{ widget.person_unconfirm }}件あります。</a>
            </li>
            <li class="widget_four" @click="routeGo('H01_S01_FacilitySearch', null, { precaution_id: 3, back: false })">
              <a>施設注意事項の確認依頼が{{ widget.facility_unconfirm }}件あります。</a>
            </li>
          </ul>
        </div>
      </div>
    </li>
  </ul>
</template>
<style lang="scss" scoped>
@import '../../../../assets/css/_animations.scss';
.widget_four,
.widget_three,
.widget_one,
.widget_two {
  cursor: pointer;
  a {
    color: #448add;
    &:hover {
      color: #448add;
    }
  }
}
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_mobile: 767px;
.page-zo {
  .navTop {
    > ul {
      margin-left: -2.5%;
      display: flex;
      flex-wrap: wrap;
      // z-index: 9;
      position: relative;
      transition: all 1s;

      > li {
        width: 25%;
        padding-left: 2.5%;
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        @media (max-width: $viewport_desktop) {
          width: 50%;
        }
      }
    }
  }
  .btnNav {
    width: 100%;
    border-radius: 16px;
    box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
    position: relative;
    transition: all 0.5s;
    &.show {
      z-index: 12;
      .dropdown-menu--nav {
        z-index: 1;
      }
    }
    &:hover {
      transform: scale(1.05);
      .btnNav-content {
        .btnNav-info {
          .subject {
            .downSlide {
              &:after {
                @include widget-icon-caret-down-shake;
              }
              &:hover {
                &:after {
                  @include widget-icon-caret-down-shake;
                }
              }
            }
            .upSlide {
              &:after {
                @include widget-icon-caret-up-shake;
              }
              &:hover {
                &:after {
                  @include widget-icon-caret-up-shake;
                }
              }
            }
          }
        }
      }
    }

    &--red {
      .btnNav-content {
        background: #ff728b;
      }
    }
    &--yellow {
      .btnNav-content {
        background: #ffca42;
      }
    }
    &.show {
      .btnNav-content {
        .btnNav-info {
          .subject {
            span {
              &:after {
                transform: scale(1.2) rotate(180deg);
              }
              &:hover {
                &:after {
                  transform: scale(1.2) rotate(180deg);
                }
              }
            }
          }
        }
      }
    }
    .btnNav-content {
      position: relative;
      padding: 16px 28px;
      min-height: 91px;
      height: 100%;
      overflow: hidden;
      border-radius: 16px;
      z-index: 2;
      @media (max-width: $viewport_desktop) {
        padding: 16px;
      }
      cursor: pointer;
      .btnNav-item {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 84px;
        text-align: right;
      }
      .btnNav-info {
        color: #fff;
        .tlt {
          font-size: 16px;
          font-weight: 700;
        }
        .subject {
          font-size: 24px;
          font-weight: 700;
          span {
            position: relative;
            padding-right: 26px;
            &:after {
              position: absolute;
              top: 17px;
              right: 0;
              content: '';
              border-left: 8px solid transparent;
              border-right: 8px solid transparent;
              border-top: 8px solid #fff;
              transition: 400ms all;
            }
          }
        }
      }
    }
    .dropdown-menu--nav {
      margin: 0;
      width: 100%;
      box-shadow: 0px 2px 10px 2px rgba(183, 195, 203, 0.8);
      border-radius: 16px;
      border: none;
      padding: 15px 24px;
      overflow-y: auto;
      max-height: 200px;
      z-index: 1;
      top: -25px !important;
      @media (min-width: 1367px) and (max-width: 1472px) {
        padding: 15px 12px;
      }

      ul {
        li {
          a {
            font-size: 14px;
            padding: 2px 0;
            display: block;
          }
        }
      }
    }
    .show {
      top: -25px !important;
      padding-top: 25px !important;
    }
  }
  .groupHome {
    padding-bottom: 20px;
  }
  .wrapColumn {
    display: flex;
    flex-wrap: wrap;
    margin-left: -2.5%;
    .wrapColumn-col4,
    .wrapColumn-col5,
    .wrapColumn-col6 {
      padding-left: 2.5%;
    }
    .wrapColumn-col5 {
      width: 50%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
    .wrapColumn-col4 {
      width: 40%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
    .wrapColumn-col6 {
      width: 60%;
      @media (max-width: $viewport_tablet) {
        width: 100%;
      }
    }
  }
  .bgBox {
    background: #fff;
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    border-radius: 16px;
    margin-top: 20px;
    padding: 15px 24px;
    &.bgBox--change {
      padding: 12px 4px 12px 0px;
      .title {
        padding: 8px 22px 20px;
        box-shadow: 0px 3px 6px #e3e3e3;
      }
    }
    .title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      .title-info {
        display: flex;
        align-items: center;
        .title-item {
          min-width: 30px;
          margin-right: 5px;
        }
        .title-tlt {
          font-weight: bold;
          font-size: 18px;
        }
      }
    }
  }
  .navTabs-main {
    ul {
      display: flex;
      flex-wrap: wrap;
      li {
        margin-left: -1px;
        &:first-of-type {
          a {
            border-radius: 4px 0 0 4px;
          }
        }
        &:last-child {
          a {
            border-radius: 0 4px 4px 0;
          }
        }
        a {
          padding: 9px 24px;
          color: #5f6b73;
          border: 2px solid #727f88;
          display: block;
          &.active {
            position: relative;
            background: #eef6ff;
            border: 2px solid #448add;
            color: #448add;
            font-weight: 700;
          }
        }
      }
    }
  }
  .lstMessage {
    padding: 0 24px;
    max-height: 200px;
    overflow-y: auto;
    ul {
      li {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #e3e3e3;
        padding: 12px 0;
        &:last-child {
          border-bottom: none;
        }
      }
    }
    .lstMessage-content {
      .lstMessage-tlt {
        font-size: 16px;
        font-weight: 700;
        a {
          color: #448add;
        }
        .lstMessage-has {
          background: #ff746b;
          width: 10px;
          height: 10px;
          border-radius: 50%;
          display: inline-block;
          margin-left: 5px;
          margin-top: 6px;
        }
      }
      .lstMessage-label {
        margin-top: 4px;
        color: #5f6b73;
      }
    }
    .lstMessage-info {
      text-align: right;
      .lstMessage-date {
        color: #5f6b73;
      }
    }
  }
  .paginMessage {
    padding: 8px 24px;
    box-shadow: 0px -3px 6px #e3e3e3;
    display: flex;
    justify-content: flex-end;
    ul {
      li {
        margin-left: -1px;
        border: 1px solid #f2f2f2;
        &:first-of-type {
          border-radius: 10px 0 0 10px;
        }
        &:last-child {
          border-radius: 0 10px 10px 0;
        }
      }
    }
  }
  .activityPlan {
    ul {
      padding-right: 8px;
      max-height: 609px;
      overflow-y: auto;
      &::-webkit-scrollbar {
        width: 8px;
        background-color: #f2f2f2;
        border-radius: 2.5px;
      }
      &::-webkit-scrollbar-thumb {
        background-color: #b7c3cb;
        border-radius: 5px;
      }
      li {
        display: flex;
        align-items: center;
        box-shadow: inset 0px -1px 0px #e3e3e3;
        border-bottom: 1px solid #e3e3e3;
        &:last-child {
          border-bottom: none;
          padding-bottom: 0;
        }
      }
    }
    .plan-date {
      color: #5f6b73;
      font-size: 16px;
      font-weight: bold;
      width: 20%;
      box-shadow: inset -1px 0px 0px #e3e3e3;
      padding-left: 10px;
    }
    .plan-content {
      display: flex;
      flex-wrap: wrap;
      margin-left: -2%;
      width: 80%;
      padding-left: 45px;
      padding-bottom: 20px;
      padding-top: 20px;

      padding-right: 15px;
      .plan-col-1,
      .plan-col-2,
      .plan-col-3 {
        padding-left: 2%;
      }
      .plan-col-1 {
        width: 30%;
      }
      .plan-col-2 {
        width: 25%;
      }
      .plan-col-3 {
        width: 45%;
        display: flex;
        align-items: center;
      }
      .plan-col-12 {
        display: flex;
        align-items: center;
      }
      .plan-item {
        margin-right: 22px;
      }
      .plan-info {
        display: flex;
        .plan-item {
          min-width: 20px;
          margin-right: 4px;
        }
        .plan-text {
          font-weight: 500;
          font-size: 16px;
        }
      }
      .plan-textLabel {
        font-size: 14px;
        span {
          font-weight: 700;
        }
        a {
          color: #5f6b73;
          font-weight: normal;
          font-size: 14px;
          &:hover {
            text-decoration: unset;
            color: #5f6b73;
          }
        }
      }
    }
  }
  .titleChart {
    display: flex;
    justify-content: space-between;
    padding-top: 9px;
    .titleChart-info {
      .titleChart-tlt {
        color: #252f40;
        font-size: 16px;
        font-weight: 700;
      }
      .titleChart-more {
        margin-top: 8px;
        a {
          color: #67748e;
          font-weight: 600;
          img {
            margin-right: 4px;
          }
        }
        span {
          color: #252f40;
          padding-left: 6px;
        }
      }
      .titleChart-lastWeek {
        margin-top: 8px;
        .titleChart-number {
          color: #7f8fa4;
          font-weight: 700;
          padding-right: 6px;
        }
      }
    }
  }
  .barChart {
    margin-top: 6px;
    img {
      width: 100%;
    }
  }
  .toolsChart {
    display: flex;
    margin-top: 24px;
    justify-content: center;
    .toolsChart-check {
      width: calc(100% - 71px);
      &.toolsChart-check--col4 {
        ul {
          li {
            width: 25%;
          }
        }
      }
      &.toolsChart-check--col5 {
        ul {
          li {
            width: 19.5%;
          }
        }
      }
      ul {
        display: flex;
        li {
          padding: 8px 2px;
          margin-left: -1px;
          text-align: center;
          color: #5f6b73;
          border: 1px solid #727f88;
          position: relative;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          &.active {
            border: 2px solid #448add;
            color: #448add;
            font-weight: 700;
            background: #eef6ff;
            z-index: 1;
          }
          &:first-of-type {
            border-radius: 4px 0 0 4px;
          }
          &:last-child {
            border-radius: 0 4px 4px 0;
          }
        }
      }
    }
    .toolsChart-select {
      width: 303px;
      select {
        height: 44px;
      }
    }
  }
  .externalLink {
    ul {
      height: 202px;
      overflow-y: auto;
      padding-right: 8px;
      &::-webkit-scrollbar {
        width: 8px;
        background-color: #f2f2f2;
        border-radius: 2.5px;
      }
      &::-webkit-scrollbar-thumb {
        background-color: #b7c3cb;
        border-radius: 5px;
      }
      li {
        padding: 10px 24px 10px 24px;
        border-bottom: 1px solid #e3e3e3;
        display: flex;
        justify-content: space-between;
        align-items: center;
        a {
          font-size: 16px;
        }
        .btn {
          margin-left: 10px;
        }
      }
    }
  }
  .custom-tbreport {
    padding: 6px 4px 12px 0px !important;
    .title {
      padding: 8px 22px 12px;
    }
  }
  .tbl-report {
    height: 200px;
    overflow-y: hidden;
    padding-right: 8px;
    &::-webkit-scrollbar {
      width: 8px;
      background-color: #f2f2f2;
      border-radius: 2.5px;
    }
    &::-webkit-scrollbar-thumb {
      background-color: #b7c3cb;
      border-radius: 5px;
    }
    tr {
      th,
      td {
        &:nth-child(1) {
          padding-left: 35px;
        }
      }
      th {
        font-size: 16px;
        font-weight: normal;
      }
      .report-gray {
        color: #99a5ae;
      }
    }
  }
  .bgBox-ranking {
    background: #fff;
    box-shadow: 0px 3px 30px rgba(0, 0, 0, 0.05);
    border-radius: 16px;
    margin-top: 20px;
  }
  .title-ranking {
    background: linear-gradient(69.9deg, #ff6847 -2.83%, rgba(255, 82, 71, 0.71) 43.43%, rgba(255, 168, 0, 0.5) 93.59%);
    border-radius: 0px 16px 16px 0px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
    border-radius: 16px 16px 0 0;
    padding: 24px;
    min-height: 70px;
    position: relative;
    &:after {
      position: absolute;
      bottom: -9px;
      right: 0;
      content: '';
      display: block;
      width: 86px;
      height: 67px;
      background: url(/img/icon-ranking.e4e7effe.svg) no-repeat;
    }
    .tlt {
      font-weight: 500;
      font-size: 22px;
      color: #fff;
    }
  }
  .groupRanking {
    padding: 18px 4px 12px 0px;
  }

  .list-manag {
    padding-bottom: 8px;
    > ul {
      > li {
        background: #fff;
        margin-top: 15px;
        padding: 12px 12px 12px 56px;
        box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.5);
        border-radius: 4px;
        position: relative;
        &.active {
          background: #e3e3e3;
          .list-title {
            .tlt {
              .svg {
                fill: #99a5ae;
              }
            }
          }
        }
      }
    }
    .list-hot {
      background: url(~@/assets/template/images/icon_hot-tag.png) no-repeat;
      width: 40px;
      height: 32px;
      display: block;
      position: absolute;
      top: -4px;
      left: 12px;
      font-size: 12px;
      color: #fcfcfc;
      font-weight: 700;
      padding: 4px 0 0 4px;
    }
    .list-title {
      .tlt {
        display: flex;
        span {
          min-width: 21px;
          margin-right: 8px;
        }
        a {
          color: #5f6b73;
          font-size: 22px;
          font-weight: 500;
        }
      }
    }
    .list-info {
      ul {
        display: flex;
        flex-wrap: wrap;
        li {
          padding-right: 36px;
          margin-top: 16px;
          &:last-child {
            padding-right: 0;
          }
          span {
            &.list-label {
              color: #99a5ae;
            }
          }
        }
      }
    }
  }
}
.slider-ranking {
  height: 454px;
  overflow-y: auto;
  &::-webkit-scrollbar {
    width: 8px;
    background-color: #f2f2f2;
    border-radius: 2.5px;
  }
  &::-webkit-scrollbar-thumb {
    background-color: #b7c3cb;
    border-radius: 5px;
  }
  .slick-arrow {
    width: 40px;
    height: 40px;
    display: block;
    top: initial;
    bottom: 0;
    &.slick-prev,
    &.slick-next {
      background: url(~@/assets/template/images/icon_slide-control-bule.svg) no-repeat;
    }
    &.slick-disabled {
      background: url(~@/assets/template/images/icon_slide-control-gray.svg) no-repeat;
      cursor: no-drop;
    }
    &.slick-prev {
      left: calc(50% - 60px);
      transform: rotate(180deg);
      &.slick-disabled {
        transform: rotate(0deg);
      }
    }
    &.slick-next {
      right: calc(50% - 60px);
      &.slick-disabled {
        transform: rotate(180deg);
      }
    }
  }
}

.slideDown {
  animation: 0.5s slideDown;
}
.slideUp {
  display: block !important;
  animation: 0.5s slideUp;
}
@keyframes slideDown {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(100px);
  }
}
@keyframes slideUp {
  0% {
    opacity: 1;
    transform: translateY(100px);
  }
  100% {
    opacity: 0;
    transform: translateY(30px);
  }
}
</style>
