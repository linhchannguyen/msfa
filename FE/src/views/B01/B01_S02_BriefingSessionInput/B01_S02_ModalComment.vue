<template>
  <div class="modal-comment">
    <div class="commentHead">
      <span class="commentHead-tlt">{{ user_name }}</span>
      <span v-if="status_type == 1 || status_type === '承認済'" class="spanLabel spanLabel--green">
        <img class="iconItem" src="@/assets/template/images/icon_check-circle01.svg" alt="" />
        確認
      </span>
      <span v-if="status_type == 2 || status_type === '差戻し'" class="spanLabel spanLabel--red">
        <img class="iconItem iconItem--pointer" src="@/assets/template/images/icon_close-circle.svg" alt="" />
        差戻し
      </span>
    </div>
    <div class="commentMain scrollbar">
      <p>
        {{ comment }}
      </p>
    </div>
    <div class="btnClose">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">閉じる</button>
    </div>
  </div>
</template>
<script>
/* eslint-disable eqeqeq */

export default {
  name: 'B01_S02_LectureInputComment',
  components: {},
  props: {
    comment: {
      type: String,
      required: true
    },

    status_type: {
      type: Number,
      required: true
    },

    user_name: {
      type: String,
      required: true
    }
  },
  emits: ['onFinishScreen'],
  data: function () {
    return {};
  },
  methods: {
    closeModal() {
      this.$emit('onFinishScreen', { screen: 'B01_S02_Modal_Comment' });
    }
  }
};
</script>

<style lang="scss" scoped>
.modal-comment {
  .commentHead {
    padding-top: 12px;
    .commentHead-tlt {
      font-size: 1.375rem;
      font-weight: 500;
      padding-right: 20px;
    }
    .spanLabel {
      border-radius: 20px;
      padding: 2px 6px 2px 28px;
      margin-top: 6px;
      min-height: 24px;
      position: relative;
      display: inline-block;
      line-height: 20px;
      &.spanLabel--red {
        background: #feddde;
        color: #ea5d54;
      }
      &.spanLabel--green {
        background: #daf8dc;
        color: #28a470;
      }
      .iconItem {
        position: absolute;
        top: 4px;
        left: 6px;
        width: 16px;
        height: 16px;
        &.iconItem--pointer {
          cursor: pointer;
        }
      }
    }
  }
  .commentMain {
    margin-top: 22px;
    background: #fff;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    padding: 15px;
    height: 200px;
  }
  .btnClose {
    text-align: center;
    margin-top: 20px;
    .btn {
      width: 180px;
    }
  }
}
</style>
