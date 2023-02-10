<template>
  <div class="modal-body modal-attendees">
    <div class="attendees-tbl table-hg100 scrollbar" @scroll="onScroll">
      <div v-if="dataOtherAttendees.length > 0" class="application-btn">
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
            <th></th>
            <th>企画時出席予定</th>
            <th>出席</th>
            <th>情報交換会</th>
            <th>慰労会</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item of dataOtherAttendees" :key="item">
            <td>{{ item.occupation_name }}</td>
            <td>
              <div class="attendees-control">
                <div class="attendees-input">
                  <CurrencyInput v-model="item.other_headcount[0].headcount" class="form-control-input" />
                </div>
                <span class="item">人</span>
              </div>
            </td>
            <td>
              <div class="attendees-control">
                <div class="attendees-input">
                  <CurrencyInput v-model="item.other_headcount[6].headcount" class="form-control-input a" />
                </div>
                <span class="item">人</span>
              </div>
            </td>
            <td>
              <div class="attendees-control">
                <div class="attendees-input">
                  <CurrencyInput v-model="item.other_headcount[7].headcount" class="form-control-input" />
                </div>
                <span class="item">人</span>
              </div>
            </td>
            <td>
              <div class="attendees-control">
                <div class="attendees-input">
                  <CurrencyInput v-model="item.other_headcount[8].headcount" class="form-control-input" />
                </div>
                <span class="item">人</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="attendees-btn">
      <button :disabled="loading" type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
      <button :disabled="loading" type="button" class="btn btn-primary customBtn" @click="submit">
        <i :class="['el-icon-loading', loading ? 'inline-block' : '']"></i> 保存
      </button>
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
          @onFinishScreen="() => (modalConfig.isShowModal = false)"
          @handleYes="forceClose"
        />
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { markRaw } from 'vue';
import _ from 'lodash';

export default {
  name: 'C01_S03_AttendantManagementModal',
  components: {},
  filters: {
    value_number(value) {
      return value && value.replace('-1', '');
    }
  },
  props: {
    otherAttendees: {
      type: Array,
      default: null
    }
  },
  emits: ['onFinishScreenC01S03'],
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
      dataOtherAttendees: [],
      loading: false,
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false
    };
  },
  mounted() {
    this.dataOtherAttendees = JSON.parse(JSON.stringify(this.otherAttendees));
    this.js_pscroll();
  },
  methods: {
    confirmCancel() {
      if (!_.isEqual(this.otherAttendees, this.dataOtherAttendees)) {
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
        this.forceClose();
      }
    },
    forceClose() {
      this.$emit('onFinishScreenC01S03', 1);
    },
    submit() {
      this.loading = true;
      this.$emit('onFinishScreenC01S03', this.dataOtherAttendees);
      this.loading = false;
    },
    onScrollLeft() {
      let content = document.querySelector('.attendees-tbl');
      content.scrollLeft = content.scrollLeft - 200;
    },
    onScrollRight() {
      let content = document.querySelector('.attendees-tbl');
      content.scrollLeft = content.scrollLeft + 200;
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
        let content = document.querySelector('.attendees-tbl');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    }
  }
};
</script>
<style lang="scss" scoped>
@import './style.scss';
.modal-attendees {
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
        left: 259px;
        border-radius: 0px 60px 60px 0px;
        z-index: 9;
      }
      &.btn--next {
        right: 76px;
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
    left: 30%;
    top: 29%;
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
.attendees-tbl {
  min-height: 92px;
}
</style>
