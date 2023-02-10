<template>
  <div class="modal-body modal-answerConfirm">
    <div class="answerConfirm">
      <h3 class="answerConfirm-tlt">{{ contentObj.header }}</h3>
      <p class="answerConfirm-ir" style="margin-top: 15px; display: flex">
        <span class="answerConfirm-item"><img src="@/assets/template/images/icon_exclamation-circle01.svg" alt="" /></span>
        {{ contentObj.content }}
      </p>
    </div>
    <div class="answerConfirm-btn">
      <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="closeModal">
        {{ contentObj.buttonRight ? contentObj.buttonRight : 'キャンセル' }}
      </button>
      <button type="button" class="btn btn-primary" @click="submitModal">{{ contentObj.buttonLeft ? contentObj.buttonLeft : 'ベストアンサーにする' }}</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'E01_S02_QaDetails_AnswerConfirm_Modal',
  props: {
    contentObj: {
      type: Object,
      default() {}
    }
  },
  emits: ['onFinishScreen', 'registerBestAnswerSubmit'],
  methods: {
    closeModal() {
      this.$emit('onFinishScreen');
    },
    submitModal() {
      this.$emit('registerBestAnswerSubmit');
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
</style>
