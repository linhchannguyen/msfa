<template>
  <div v-loading="loading" v-load-f5="true" class="facilityContent">
    <div class="facilityTbl scrollbar table-hg100" @scroll="onScroll">
      <div v-if="dataList.length > 0" class="application-btn">
        <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
          <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
        </button>
        <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
          <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
        </button>
      </div>
      <table class="tableBox tableCustom">
        <thead>
          <tr>
            <th>ターゲット品目</th>
            <th>選定期間</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item of dataList" :key="item">
            <td class="td-1">{{ item.target_product_name }}</td>
            <td v-if="!item.isEdit" class="td-2" style="width: 90%">
              <div class="flex">
                <span style="width: 60%"> {{ formatFullDate(item.selection_start_date) }} ～ {{ formatFullDate(item.selection_end_date) }} </span>

                <button
                  class="button-edit btn btn-outline-primary customBtn btn-radius btn-outline-primary--cancel"
                  style="margin-left: 5%"
                  @click="edit(item, item.selection_start_date, item.selection_end_date)"
                >
                  <img v-svg-inline svg-inline style="margin-right: 5px" class="svg" :src="require('@/assets/template/images/icon_edit.svg')" alt="" />編集
                </button>
              </div>
            </td>
            <td v-else class="td-2" style="width: 90%">
              <div class="flex">
                <div class="form-dateTime" style="margin-right: 7px">
                  <div class="form-icon icon-left form-icon--noBod">
                    <span class="icon">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                    </span>
                    <el-date-picker
                      v-model="item.selection_start_date"
                      :disabled-date="disabledDateStart"
                      format="YYYY/M/D"
                      type="date"
                      :editable="false"
                      placeholder="開始日"
                      class="form-control-datePickerLeft"
                    ></el-date-picker>
                  </div>
                </div>
                ～
                <div class="form-dateTime" style="margin-left: 7px">
                  <div class="form-icon icon-left form-icon--noBod" :class="item.showMess ? 'hasErr' : ''">
                    <span class="icon">
                      <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon-calender-control.svg')" alt="" />
                    </span>
                    <el-date-picker
                      v-model="item.selection_end_date"
                      :disabled-date="disabledDateEnd"
                      format="YYYY/M/D"
                      type="date"
                      :editable="false"
                      placeholder="終了日"
                      class="form-control-datePickerLeft"
                    ></el-date-picker>
                  </div>
                </div>
              </div>
              <div v-if="item.showMess" class="invalid-feedback">
                <span>選定開始日は選定期間以降の日付を指定してください。</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="isCountEdit" class="formMessage-btn">
      <button :disabled="isloadingBtn" class="btn btn-primary customBtn btn2" type="button" @click="postData">
        <i :class="['el-icon-loading', isloadingBtn ? 'inline-block' : '']"></i> 保存
      </button>
    </div>
  </div>
  <el-dialog> </el-dialog>
</template>

<script>
import G01_S04_TargetSelectionPeriodManagement from '@/api/G01/G01_S04_TargetSelectionPeriodManagement';
export default {
  name: 'G01_S04_TargetSelectionPeriodManagement',
  components: {},
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      dataList: [],
      isCountEdit: false,
      loading: false,
      release_end_date: '',
      release_start_date: '',
      IStartDate: '',
      IEndDate: '',
      countMess: 0,
      isloadingBtn: false,
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false
    };
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'ターゲット選定期間管理',
      icon: 'icon_target-facility.svg',
      isShowBack: false
    });
    this.getScreen();
  },
  methods: {
    onScrollLeft() {
      let content = document.querySelector('.facilityTbl');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.facilityTbl');
      content.scrollLeft += 200;
    },
    onScroll({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }

      if (this.isScrolling) {
        let content = document.querySelector('.facilityTbl');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    getScreen() {
      this.loading = true;
      this.isCountEdit = false;
      this.js_pscroll();
      G01_S04_TargetSelectionPeriodManagement.getList()
        .then((res) => {
          this.dataList = res.data.data;
          this.dataList.forEach((element) => {
            element.isEdit = false;
            element.showMess = false;
          });
          this.loading = false;
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 }))
        .finally();
    },
    edit(item, startDate, endDate) {
      this.release_end_date = endDate;
      this.release_start_date = startDate;
      this.dataList.forEach((element) => {
        if (item.target_product_cd === element.target_product_cd) {
          element.isEdit = true;
          this.isCountEdit = true;
        }
      });
    },
    postData() {
      this.dataList.forEach((element) => {
        if (element.showMess) {
          this.countMess = this.countMess + 1;
        }
      });
      if (this.countMess > 0) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error', duration: 1500 });
        this.countMess = 0;
      } else {
        this.dataList.forEach((element) => {
          element.selection_start_date = this.formatFullDate2(element.selection_start_date);
          element.selection_end_date = this.formatFullDate2(element.selection_end_date);
        });
        this.isloadingBtn = true;
        const data = {
          data: this.dataList
        };
        G01_S04_TargetSelectionPeriodManagement.postRecord(data)
          .then((res) => {
            this.$notify({ message: res.data.message, customClass: 'success', duration: 1500 });
            this.getScreen();
          })
          .catch((err) => {
            if (err.response.data.message === '{0}に{1}より後の日付を入力してください。') {
              this.$notify({ message: '開始日付に終了日付より後の日付を入力してください。', customClass: 'error', duration: 1500 });
            } else {
              this.$notify({ message: err.response.data.message, customClass: 'error', duration: 1500 });
            }
          })
          .finally(() => {
            this.isloadingBtn = false;
          });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.facilityTbl {
  position: relative;
  overflow: hidden;
  .application-btn {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    height: 0;
    margin: auto;
    z-index: 99;
    .btn {
      position: fixed;
      height: 46px;
      width: 52px;
      padding: 0;
      background: rgba(63, 75, 86, 0.4);
      z-index: 1;
      &.btn--prev {
        left: 84px;
        border-radius: 0px 60px 60px 0px;
        z-index: 9;
      }
      &.btn--next {
        right: 24px;
        border-radius: 60px 0px 0px 60px;
        z-index: 5;
        .svg {
          transform: rotate(-180deg);
        }
      }
    }
  }
}
.customBtn {
  position: relative;
  .el-icon-loading {
    position: absolute;
    margin: auto;
    left: 29%;
    top: 31%;
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
@media only screen and (max-width: 992px) and (min-width: 768px) {
  .flex {
    width: 100% !important;
  }
}
@media only screen and (max-width: 1024px) and (min-width: 768px) {
  .flex {
    width: 100% !important;
  }
}
.custom-tb {
  height: calc(100% - 60px);
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25;
  padding-left: 5px;
  height: 16px;
}

$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.facilityContent {
  height: 100%;
  padding: 28px 24px 66px;
  overflow: hidden;
  .facilityTbl {
    tr {
      th {
        font-size: 1rem;
        &:nth-child(1) {
          width: 1rem;
          min-width: 470px;
          text-align: center;
        }
        &:nth-child(2) {
          width: 1rem;
          min-width: 400px;
          text-align: center;
        }
      }
      .td-1 {
        text-align: center;
        color: #5f6b73;
        font-weight: normal;
        font-size: 14px;
        padding: 20px 0;
        vertical-align: middle;
      }
      .td-2 {
        color: #5f6b73;
        font-weight: normal;
        font-size: 14px;
        padding: 20px 32px;
      }
    }
  }
}
.button-edit {
  margin-left: 20%;
  border: 1px solid #448add;
  border-radius: 24px;
  padding: 4px 13px;
  color: #448add;
  font-weight: bold;
  font-size: 15px;
  cursor: pointer;
}
.flex {
  display: flex;
  align-items: center;
}
.formMessage-btn {
  text-align: center;
  margin: 12px 0 32px 0;
  button {
    width: 16%;
  }
}
</style>
