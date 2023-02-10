<template>
  <!-- mode 0: delete confirm -->
  <!-- mode 1: save confirm -->
  <div class="modal-confirm">
    <!-- width: 40% || 35rem -->
    <div v-if="mode === 0" class="confirmContent">
      <img :src="require(`@/assets/template/images/${iconAlert}.svg`)" alt="iconAlert" />
      <h3 class="title">{{ title }}</h3>
      <p class="text">
        <span class="item"><img src="@/assets/template/images/icon_exclamation-circle01.svg" alt="" /></span>
        <span>{{ message || 'このアクションは元に戻すことができません。' }}</span>
      </p>
    </div>
    <!-- props: { mode: 1, textCancel: 'いいえ', message: '' } -->
    <!-- width: 35% || 35rem -->
    <div v-else-if="mode === 1" class="confirmContent-1">
      <img :src="require(`@/assets/template/images/${iconAlert}.svg`)" alt="iconAlert" />
      <h3 v-if="!message" class="title">変更されている項目があります。</h3>
      <p class="title">{{ message || '現在編集中の内容が破棄されますが、よろしいでしょうか？' }}</p>
    </div>

    <!-- handle button -->
    <div class="confirmBtn">
      <button
        type="button"
        :disabled="processingFlag"
        :class="['btn btn-outline-primary--cancel btn-outline-primary', iconLoading ? 'eventNone-canncel' : '']"
        @click="close"
      >
        {{ textCancel }}
      </button>
      <el-button
        v-if="mode === 0"
        type="primary"
        :class="['btn btn-outline-danger', iconLoading ? 'eventNone-danger' : '']"
        :loading="processingFlag"
        :disabled="processingFlag"
        @click.prevent="confirm()"
      >
        <i :class="['el-icon-loading', iconLoading ? 'inline-block' : '']"></i><span class="textSubmit"> {{ textConfirm }}</span>
      </el-button>
      <el-button
        v-if="mode === 1"
        type="primary"
        :class="['btn btn-primary btn-yes-cancel no-log', iconLoading ? 'eventNone' : '']"
        :loading="processingFlag"
        :disabled="processingFlag"
        @click.prevent="sayYes()"
      >
        <i :class="['el-icon-loading', iconLoading ? 'inline-block' : '']"></i><span class="textSubmit">はい</span>
      </el-button>
      <!-- <button v-if="mode === 0" type="button" class="btn btn-outline-danger" @click="confirm">{{ textConfirm }}</button> -->
      <!-- <button v-else-if="mode === 1" type="button" class="btn btn-primary" @click="sayYes">はい</button> -->
    </div>
  </div>
</template>

<script>
export default {
  name: 'PopupConfirm',
  props: {
    mode: {
      type: Number,
      default: 0,
      require: false
    },
    iconAlert: {
      type: String,
      default: 'icon_alert',
      require: false
    },
    title: {
      type: String,
      default: '以下の面談先を完全に削除しますか？',
      require: false
    },
    message: {
      type: String,
      default: '',
      require: false
    },
    textCancel: {
      type: String,
      default: 'いいえ',
      require: false
    },
    textConfirm: {
      type: String,
      default: '削除',
      require: false
    },
    params: {
      type: Object,
      require: false,
      default: null
    }
  },
  emits: ['onFinishScreen', 'handleConfirm', 'handleYes'],
  data: () => {
    return {
      processingFlag: false,
      iconLoading: false
    };
  },
  mounted() {
    this.emitter.on('change-processingFlag', ({ flag }) => {
      this.processingFlag = flag;
      this.iconLoading = false;
    });
  },
  methods: {
    close() {
      this.$emit('onFinishScreen', this.params);
    },
    confirm() {
      this.iconLoading = true;
      this.$emit('handleConfirm', this.params);
    },
    sayYes() {
      this.iconLoading = true;
      this.$emit('handleYes', this.params);
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
