<template>
  <div class="modal-approve">
    <h2 class="approve-title text-center">承認しますか？</h2>
    <div class="approveForm">
      <label class="approveForm-label">コメント</label>
      <div class="approveForm-control" :class="isSubmit && !validation.comment.length ? 'hasErr' : ''">
        <el-input v-model="comment" clearable class="form-control-textarea" :rows="4" type="textarea" placeholder="コメント入力" />
        <p v-if="isSubmit && !validation.comment.length" class="text-error">{{ getMessage('MSFA0012', 'コメント', 200) }}</p>
      </div>
    </div>
    <div class="approveForm-btn text-center">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
      <button type="button" class="btn btn-primary" @click="approveConvention()">承認</button>
    </div>
  </div>
</template>
<script>
/* eslint-disable eqeqeq */
import { validLength } from '@/utils/validate';

export default {
  name: 'C01_S02_LectureInputApprove',
  components: {},
  props: {},
  emits: ['onFinishScreen'],
  data: function () {
    return {
      comment: ''
    };
  },
  computed: {
    validation() {
      return {
        comment: {
          length: validLength(this.comment, 200)
        }
      };
    }
  },
  methods: {
    closeModal() {
      this.$emit('onFinishScreen', null);
    },

    approveConvention() {
      if (!this.checkValidate()) {
        return;
      } else {
        let result = {
          screen: 'C01_S02_Approve',
          comment: this.comment
        };
        this.$emit('onFinishScreen', result);
      }
    }
  }
};
</script>
<style lang="scss" scoped>
.modal-approve {
  .approve-title {
    padding-top: 20px;
    color: #2d3033;
    font-size: 1.125rem;
    font-weight: 700;
  }
  .approveForm {
    margin-top: 5px;
    text-align: left;
    .approveForm-label {
      font-size: 1rem;
      margin-bottom: 10px;
    }
  }
  .approveForm-btn {
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
</style>
