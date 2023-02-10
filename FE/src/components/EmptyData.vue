<template>
  <div :class="`noData ${customClass}`">
    <div class="noData-content">
      <p class="noData-text">{{ title }}</p>
      <div class="noData-thumb" :style="{ 'margin-top': thumbMarginTop }">
        <ImageSVG :src-image="`${icon}.svg`" :in-height="inHeight" :in-width="inWidth" :alt-image="'icon_amico'" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmptyData',
  props: {
    customClass: {
      type: String,
      required: false,
      default: ''
    },
    title: {
      type: String,
      required: false,
      default: '該当するデータがありません。'
    },
    icon: {
      type: String,
      required: false,
      default: 'amico_Z05S03'
    },
    inHeight: {
      type: String,
      required: false,
      default: ''
    },
    inWidth: {
      type: String,
      required: false,
      default: ''
    },
    thumbMarginTop: {
      type: String,
      required: false,
      default: '60px'
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_tablet: 991px;
$viewport_desktop: 1366px;
.noData {
  text-align: center;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  height: 100%;
  @media (max-width: $viewport_tablet) {
    height: inherit;
    padding: 30px 20px;
  }
  .noData-content {
    width: 100%;
  }
  .noData-text {
    color: #99a5ae;
  }
  .noData-thumb {
    margin-top: 60px;
  }
}
@media (max-width: $viewport_desktop) {
  .noData-thumb {
    margin-top: 20px !important;
    svg {
      height: 280px;
    }
  }
}
@media (min-width: $viewport_tablet) and (max-width: $viewport_desktop) {
  .noData.empty-contentStock-form {
    svg {
      height: 150px;
    }
  }
}
@media (min-width: $viewport_tablet) and (max-width: $viewport_desktop) {
  .noData.empty-facility-details-notes {
    svg {
      height: 230px;
    }
  }
}

.noData.evaluation-comments {
  height: calc(100% - 30px);
}
</style>
