<template>
  <el-button :disabled="disabled" :class="`btn-add ${className}`">
    <i class="el-icon-plus">
      <span>{{ label }}</span>
    </i>
  </el-button>
</template>

<script>
export default {
  name: 'ButtonAdd',
  props: {
    disabled: {
      require: false,
      default: false,
      type: Boolean
    },
    label: {
      require: false,
      default: '追加',
      type: String
    },
    className: {
      require: false,
      default: '',
      type: String
    }
  }
};
</script>
<style lang="scss" scoped></style>
