<template>
  <img
    v-svg-inline
    svg-inline
    :class="`svg ${classImage}`"
    :src="require(`@/assets/template/images/${icon}`)"
    :alt="altImage"
    :height="inHeight"
    :width="inWidth"
  />
</template>
<script>
export default {
  name: 'ImageSVG',
  props: {
    srcImage: {
      type: String,
      default: '',
      required: true
    },
    altImage: {
      type: String,
      default: 'imgAlt',
      required: true
    },
    classImage: {
      type: String,
      default: '',
      required: false
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
    }
  },
  data() {
    return {
      icon: this.srcImage
    };
  },

  watch: {
    srcImage(value) {
      this.icon = value;
    }
  }
};
</script>
<style scoped></style>
