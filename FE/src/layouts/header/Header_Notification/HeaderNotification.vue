<template>
  <div class="header-notif">
    <button title_log="メッセージ絞込み" class="btn btn-notif" @click="informed">
      <img src="@/assets/template/images/icon-noti-not.svg" alt="" />
      <span v-if="uninformed_quantity !== 0" class="dot"></span>
      <!-- <div v-if="activeNotification" class="caret-notation"></div> -->
    </button>
    <div :class="[isShowPopupNoti ? 'show' : '']" class="dropdown-notif">
      <div class="caret-notation"></div>
      <div class="manageNotif">
        <div class="manageNotif-btn">
          <button
            :disabled="!unarchived_inform[0]"
            type="button"
            class="btn btn-outline-primary btn-radius btn btn-outline-primary btn1 btn-outline-primary--cancel"
            @click="archived(0)"
          >
            <span class="btn-iconLeft">
              <img style="margin-bottom: 2px" class="svg" src="@/assets/template/images/archive-in.svg" alt="" /> <span>全てアーカイブ</span></span
            >
          </button>
        </div>
        <div class="manageNotif-more">
          <button title_log="通知を管理する" type="button" class="custombtn" @click="gotoNotificationManager()">通知を管理する ></button>
        </div>
      </div>

      <div class="list-notif list-notif-custom">
        <Preloader v-if="isLoading" style="position: absolute; left: 50%; top: 50%" />
        <ul v-if="!isLoading">
          <li v-for="item of unarchived_inform" :key="item.inform_id" :ref="`item-${item.inform_id}`">
            <span
              :class="item.read_flag === 0 ? 'changeReadFlag' : ''"
              title_log="通知内容"
              class="custom-cursor log-icon"
              @click="read(item.inform_id, item.url, item.url2, item.parameter_key, item.parameter_value)"
              >{{ item.inform_contents }}</span
            >
            <button
              title_log="アイコン"
              :disabled="disabledBtn"
              :class="`custom btn btn-${item.inform_id} `"
              class="btn log-icon"
              @click.prevent="handleDestroyNotify(1, item.inform_id)"
            >
              <img
                :class="`svg ${deleteNotiProcessing[item.inform_id] ? 'item-rotation-360' : ''}`"
                class="svg"
                src="@/assets/template/images/archive-in.svg"
                alt=""
              />
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import Z02_S01_HomeServices from '../../../api/Z02/Z02_S01_HomeServices';
import Z02_S03_NotificationServices from '../../../api/Z02/Z02_S03_NotificationServices';
import mixin_noti from '@/utils/mixin_noti';
export default {
  name: 'HeaderNotification',
  mixins: [mixin_noti],
  data() {
    return {
      unarchived_inform: [],
      checkAllInform: 0,
      activeNotification: false,
      uninformed_quantity: 0,
      isLoading: false,
      updateNotiProcessing: {},
      deleteProcessing: {},
      deleteNotiProcessing: {},
      disabledBtn: false
    };
  },
  mounted() {
    this.getLisstMessage();
  },
  methods: {
    informed() {
      this.isShowPopupNoti = !this.isShowPopupNoti;
      this.changeNotification();
    },
    getLisstMessage() {
      this.isLoading = true;
      Z02_S01_HomeServices.getInformListService()
        .then((res) => {
          this.checkAllInform = res.data.data.unarchived_inform;
          this.uninformed_quantity = res.data.data.uninformed_quantity;
          const dataRes = res.data.data.unarchived_inform;
          dataRes.forEach((element) => {
            switch (element.parameter_key) {
              case 'report_id':
                if (element.inform_category_cd === '10') {
                  element.url2 = 'A03_S03_NotApprovedList';
                } else {
                  element.url2 = 'A03_S01_JapaneseDailyReportList';
                }
                break;
              case 'briefing_id':
                element.url2 = 'B01_S01_BriefingSearch';
                break;
              case 'question_id':
                element.url2 = 'E01_S01_QaSearch';
                break;
              case 'convention_id':
                element.url2 = 'C01_S01_LectureList';
                break;
              case 'knowledge_id':
                if (element.inform_category_cd === '150' || element.inform_category_cd === '170' || element.inform_category_cd === '180') {
                  element.url2 = 'F01_S01_KnowledgeSearch';
                } else {
                  element.url2 = 'F01_S05_Pre_ReleaseKnowledgeManagement';
                }
                break;
              case 'facility_cd':
                element.url2 = 'H01_FacilityDetails';
                break;
              case 'person_cd':
                element.url2 = 'H02_PersonalDetails';
                break;
              case 'calendar':
                element.url2 = 'A01_S02_Calendar';
                break;
              default:
                element.url2 = '';
                break;
            }
          });
          this.unarchived_inform = dataRes;
          this.isLoading = false;
        })
        .catch(() => {
          // console.log(err);
        });
    },
    handleDestroyNotify(e, id) {
      // const el = document.querySelector('.dropdown-menu');
      // console.log(el);
      // setTimeout(() => {
      //   el?.classList.add('show');
      // }, 10);
      this.disabledBtn = true;
      this.deleteNotiProcessing = { [id]: id };
      setTimeout(() => {
        this.deleteProcessing = {};
        const itemAction = this.$refs[`item-${id}`];
        // this.$notify({ message: 'Api response success', customClass: 'success' });
        setTimeout(() => {
          this.archived(e, id);
        }, 500);
        this.unarchived_inform = this.unarchived_inform.map((item) => {
          if (item.inform_id === id) {
            return { ...item };
          } else return item;
        });

        this.destroySuccess(itemAction);
      }, 1000);
    },
    destroySuccess(itemAction) {
      let item = Array.isArray(itemAction) ? itemAction[0] : itemAction;
      item?.classList.toggle('deleted');
      item.style.background = '#e3e3e3';
      item.addEventListener('transitionend', () => {});
      setTimeout(() => {
        item.style.display = 'none';
      }, 1000);
    },
    changeNotification() {
      Z02_S01_HomeServices.InformedApiService()
        .then((res) => {
          if (res) {
            this.getLisstMessage();
          }
        })
        .catch(() => {
          // console.log(err);
        });
    },
    gotoNotificationManager() {
      localStorage.setItem('isLoadingComponent', false);
      if (this.$router?.currentRoute?._value?.name !== 'Z02_S03_UserNotificationManagement') {
        localStorage.removeItem('dataListCatelogyInform');
        localStorage.removeItem('archive_flag');
        localStorage.removeItem('inform_datetime_from');
        localStorage.removeItem('inform_datetime_to');
        localStorage.removeItem('inform_contents');
      }
      this.$router.push({
        name: 'Z02_S03_UserNotificationManagement',
        params: {},
        query: {}
      });
      this.isShowPopupNoti = false;
      localStorage.setItem('activeMenu', 'Z02_S03_UserNotificationManagement');
    },
    read(id, url, url2, parameter_key, parameter_value) {
      Z02_S03_NotificationServices.readServies(id).then((res) => {
        if (res) {
          this.isShowPopupNoti = false;
          // this.getLisstMessage();
          // if (url === this.$route.fullPath) this.emitter.emit('change-F01S03');
          // else this.$router.push(url);
          switch (parameter_key) {
            case 'facility_cd':
              this.$router.push({ name: `${url2}`, params: { facility_cd: parameter_value }, query: { tab: '3' } });
              break;
            case 'person_cd':
              this.$router.push({ name: `${url2}`, params: { person_cd: parameter_value }, query: { tab: '4' } });
              break;
            case 'convention_id':
              this.$router.push({ name: `${url2}`, params: { convention_id: parameter_value } });
              break;
            case 'knowledge_id':
              this.$router.push({ name: `${url2}`, params: { knowledge_id: parameter_value } });
              break;
            case 'briefing_id':
              this.$router.push({ name: `${url2}`, params: { briefing_id: parameter_value } });
              break;
            case 'question_id':
              this.$router.push({ name: `${url2}`, params: { question_id: parameter_value } });
              break;
            case 'calendar':
              this.$router.push({ name: `${url2}`, params: {} });
              break;
            case 'report_id':
              this.$router.push({ name: `${url2}`, params: { report_id: parameter_value } });
              break;
            default:
              this.$router.push({ name: '', params: {} });
              break;
          }
        }
      });
    },
    archived(e, id) {
      if (e === 0) {
        const data = {
          home_screen_flag: 1
        };
        Z02_S01_HomeServices.archivedService(data)
          .then((res) => {
            if (res) {
              this.disabledBtn = false;
              this.$notify({ message: res.data.message, customClass: 'success' });
              this.getLisstMessage();
            }
          })
          .finally(() => {
            this.disabledBtn = false;
          });
      } else {
        const data = {
          inform_id: [id],
          archive_flag: 1
        };
        Z02_S01_HomeServices.archivedService(data)
          .then((res) => {
            if (res) {
              this.disabledBtn = false;
              this.$notify({ message: res.data.message, customClass: 'success' });
              this.getLisstMessage();
            }
          })
          .catch(() => {
            // console.log(err);
          })
          .finally(() => {
            this.disabledBtn = false;
          });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.item-rotation-360 {
  display: inline-block;
  animation: animation-item-rotation-360 0.75s linear 1;
}

@keyframes animation-item-rotation-360 {
  to {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@import '../../../assets/css/_animations.scss';
.dropdown-menu {
  right: -175px !important;
}
.changeReadFlag {
  font-weight: bold;
}
.custom-cursor {
  cursor: pointer;
  color: #448add;
  font-size: 14px;
}
.change-color {
  color: #59a5ff !important;
  cursor: pointer;
}
.noti-number {
  position: absolute;
  top: -37px;
  right: 0px;
  color: red;
  border-radius: 50%;
  font-size: 42px;
}
.active {
  display: none;
}
.header-notif {
  margin-top: 12px;
  width: 26px;
  position: relative;
  .btn-notif {
    padding: 0;
    position: relative;

    .dot {
      display: inline-block;
      background: #ff7347;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      position: absolute;
      top: 6px;
      right: 0;

      @include icon-bell-dot-pulse;
    }

    &:focus {
      box-shadow: none;
    }
  }
}
.dropdown-notif {
  width: 650px;
  background: #f2f2f2;
  border-radius: 10px;
  border: none;
  box-shadow: 3px 4px 11px -3px #888;
  border: 1px solid #ddd;
  padding: 13px 29px;
  position: absolute;
  top: 0;
  left: 0;
  z-index: -1;
  @include header-notification-list-hide;
  visibility: hidden;

  &.show {
    @include header-notification-list-show;
    visibility: visible;
  }

  .manageNotif {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding-top: 11px;
    .manageNotif-btn {
      .btn {
        padding: 3px 12px;
        font-size: 16px;
        height: inherit;
        font-weight: normal;
      }
    }
    .manageNotif-more {
      a {
        font-size: 16px;
        padding-right: 20px;
        background: url(~@/assets/template/images/icon_arrow-right-blue.svg) no-repeat center right;
      }
    }
  }
  .list-notif {
    border: 1px solid #b7c3cb;
    border-radius: 5px;
    background: #fff;
    min-height: 100px;
    margin-top: 20px;
    overflow-y: auto;
    max-height: 357px;
    ul {
      li {
        transition: background 0.75s, transform 0.75s ease;
        @keyframes animation-updated-color {
          to {
            background-position: right;
          }
        }
        &.deleted {
          transform: translateX(100%);
          -webkit-transform: translateX(100%);
        }
        &.updatedColorItem {
          background: linear-gradient(to left, #fff 50%, transparent 50%) left;
          background-size: 200%;
          animation: animation-updated-color 1s;
        }
        &.background-e3e3e3.updatedColorItem {
          background: linear-gradient(to left, #e3e3e3 50%, transparent 50%) left;
          background-size: 200%;
          animation: animation-updated-color 1s;
        }
        padding: 16px 16px 16px 24px;
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #e3e3e3;
        &:last-child {
          border-bottom: none;
        }
        a {
          color: #2d3033;
          font-weight: 500px;
          font-size: 14px;
        }
        .btn {
          box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
          width: 42px;
          height: 42px;
          min-width: 42px;
          border-radius: 50%;
          padding: 0;
          background: #fff;
          margin-left: 16px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }
    }
  }
}
.caret-notation {
  content: '';
  position: absolute;
  top: -14px;
  right: 175px;
  width: 26px;
  height: 26px;
  background-color: #f2f2f2;
  border-right: 1px solid #ddd;
  border-top: 1px solid #ddd;
  box-shadow: 3px 0px 4px -4px #888;
  transform: rotate(-45deg);
  z-index: -1;
  pointer-events: none;
  @include header-notification-list-hide;
  @include header-notification-list-show;
}
.custom {
  &:hover {
    border: 1px solid #448add;
  }
}
.btn-iconLeft {
  top: 0;
}
.custombtn {
  color: #448add;
  font-weight: 400;
  font-size: 16px;
}
</style>
