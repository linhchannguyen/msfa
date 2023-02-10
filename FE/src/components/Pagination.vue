<template>
  <el-pagination
    :class="`pagination-bord ${classPagination}`"
    layout="prev, pager, next"
    background
    :current-page="pageCurrent"
    :page-size="pageSize"
    :total="totalPage"
    @current-change="changeCurrentPage"
  />
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    totalPage: {
      type: Number,
      required: true,
      default: 1
    },
    currentPage: {
      type: Number,
      required: true,
      default: 1
    },
    pageSize: {
      type: Number,
      required: false,
      default: 10
    },
    classPagination: {
      type: String,
      required: false,
      default: ''
    }
  },
  data() {
    return {
      pageCurrent: 1
    };
  },
  watch: {
    currentPage(page) {
      this.pageCurrent = page;
    }
  },

  methods: {
    changeCurrentPage(page) {
      this.pageCurrent = page;
    }
  }
};
</script>

<style lang="scss" scoped>
.pagination-bord ul li {
  cursor: pointer;
}
</style>
