<template>
  <div class="errorWap">
    <div class="errorInfo">
      <div class="error-text">
        <h1 class="error-title">500</h1>
        <p class="error-label">エラー</p>
        <p class="error-note">サーバ内部エラーが発生しました。</p>
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel btn-radius" @click="toHome">
          <span class="btn-iconLeft"> <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_arrow-back.svg')" alt="" /> </span
          >ホームページへ
        </button>
      </div>
    </div>
    <div class="errorThumb">
      <img v-svg-inline svg-inline :src="require('@/assets/template/images/data/img_error.svg')" alt="img_error" />
    </div>
  </div>
</template>
<script>
export default {
  name: 'SystemError',
  methods: {
    toHome() {
      this.$router.push({ name: 'Z02_S01_Home' });
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.errorWap {
  height: 100%;
  width: 100%;
  max-width: 1152px;
  margin: 0 auto;
  padding: 0 12px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  .errorInfo {
    width: 40%;
    padding: 0 50px 0 0;
    .error-text {
      text-align: center;
      .error-title,
      .error-label {
        color: #ffaf3f;
        letter-spacing: -0.02em;
        font-weight: 700;
        line-height: 140%;
      }
      .error-title {
        font-size: 64px;
      }
      .error-label {
        font-size: 36px;
      }
      .error-note {
        padding-top: 40px;
        letter-spacing: -0.02em;
        font-weight: 700;
        line-height: 140%;
        font-size: 24px;
      }
      .btn {
        margin-top: 35px;
      }
    }
  }
  .errorThumb {
    display: flex;
    align-items: center;
    width: 60%;
    height: 100%;
    text-align: center;
    padding: 20px 0;
    img {
      max-height: 600px;
      height: 100%;
    }
  }
}
</style>
