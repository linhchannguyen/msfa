<template>
  <div :style="styles" class="spinner spinner--circle-2"></div>
</template>
<script>
export default {
  props: {
    size: {
      type: String,
      require: false,
      default: '42px'
    },
    background: {
      type: String,
      require: false,
      default: 'rgb(183 200 192)'
    },
    color: {
      type: String,
      require: false,
      default: '#35495e'
    },
    stroke: {
      type: String,
      require: false,
      default: '2px'
    }
  },
  computed: {
    styles() {
      return {
        width: this.size,
        height: this.size,
        border: this.stroke + ' solid ' + this.background,
        borderTopColor: this.color
      };
    }
  }
};
</script>
<style lang="scss" scoped>
.spinner {
  box-sizing: border-box;
  border-radius: 100%;
  border: 10px solid rgba(255, 255, 255, 0.2);
  animation: loading-rotate 2s linear infinite;
}
@keyframes circle-2-spin {
  100% {
    transform: rotate(360deg);
  }
}
</style>
