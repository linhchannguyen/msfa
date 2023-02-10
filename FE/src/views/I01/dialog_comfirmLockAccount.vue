<template>
  <div class="modal-confirm">
    <div class="confirmContent-1">
      <img :src="require(`@/assets/template/images/${iconAlert}.svg`)" alt="iconAlert" />
      <p class="title">{{ message }}</p>
    </div>
    <div class="confirmBtn">
      <button type="button" :class="['btn btn-outline-primary--cancel btn-outline-primary', iconLoading ? 'eventNone-canncel' : '']" @click="close">
        {{ textCancel }}
      </button>
      <el-button
        type="primary"
        :class="['btn btn-primary btn-yes-cancel no-log', iconLoading ? 'eventNone' : '']"
        :loading="processingFlag"
        :disabled="processingFlag"
        @click.prevent="sayYes()"
      >
        <i :class="['el-icon-loading', iconLoading ? 'inline-block' : '']"></i><span class="textSubmit">{{ textConfirm }}</span>
      </el-button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'dialogComfirmLockAccount',
  props: {
    iconAlert: {
      type: String,
      default: 'icon_alert',
      require: false
    },
    mode: {
      type: Number,
      default: 0,
      require: false
    },
    textCancel: {
      type: String,
      default: 'いいえ',
      require: false
    },
    message: {
      type: String,
      default: '開始日が同日の先行予約が既に●件登録されています。上書きしてよろしいですか？',
      require: false
    },
    textConfirm: {
      type: String,
      default: 'はい',
      require: false
    }
  },
  emits: ['onFinishScreen', 'comfirmLockAcc'],
  data: () => {
    return {
      iconLoading: false
    };
  },
  mounted() {},
  methods: {
    close() {
      this.$emit('onFinishScreen', this.params);
    },
    sayYes() {
      this.iconLoading = true;
      this.$emit('comfirmLockAcc', this.params);
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.modal-confirm {
  .confirmContent {
    text-align: center;
    .title {
      margin-top: 24px;
      color: #2d3033;
      font-size: 1.125rem;
      font-weight: 700;
    }
    .text {
      font-size: 0.9375rem;
      margin-top: 8px;
      display: flex;
      justify-content: center;
      .item {
        min-width: 20px;
        margin: -2px 6px 0 0;
      }
    }
  }
  .confirmContent-1 {
    text-align: center;
    .title {
      margin-top: 12px;
      color: #2d3033;
      font-size: 1.125rem;
      font-weight: 700;
    }
  }
  .confirmBtn {
    text-align: center;
    margin-top: 24px;
    .btn {
      width: 180px;
      margin-right: 24px;
      &:last-child {
        margin-right: 0;
      }
    }
  }
}
@media only screen and (max-width: 1024px) and (min-width: 768px) {
  .confirmBtn {
    display: flex !important;
    justify-content: center;
  }
}

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
.btn-outline-danger .el-icon-loading {
  margin-right: 5px;
  display: none;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.eventNone-danger {
  pointer-events: none;
  background: #ff9090;
  color: #fff9f9c7;
  border-color: #ff9090;
}
.eventNone {
  pointer-events: none;
  background: #90c3ff;
  color: #fff9f9c7;
  border-color: #90c3ff;
}
.eventNone-canncel {
  pointer-events: none;
  background-color: #f2f2f2a6;
  color: #a8a8a8;
  border-color: #c4c6c8;
}
</style>
