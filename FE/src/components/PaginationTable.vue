<template>
  <el-pagination
    v-model="pageCurrent"
    layout="prev, pager, next"
    class="pagination-bord"
    :page-size="pageSize"
    :total="total"
    :current-page="currentPage"
    :pager-count="countPage"
    :hide-on-single-page="false"
    @current-change="current - change"
  >
  </el-pagination>
</template>

<script>
export default {
  name: 'PaginationTable',
  props: {
    total: {
      type: Number,
      default: 10,
      required: true
    },
    pageSize: {
      type: Number,
      default: 10,
      required: false
    },
    currentPage: {
      type: Number,
      default: 1,
      required: false
    },
    pagerCounts: {
      type: Number,
      required: false
    }
  },
  data() {
    return {
      pageCurrent: this.currentPage,
      countPage: 7
    };
  },
  mounted() {
    let width = screen.width;
    if (!this.pagerCounts) {
      if (width < 1025) {
        this.countPage = 5;
      } else {
        this.countPage = 7;
      }

      if (width < 769) {
        this.countPage = 3;
      }
    } else {
      this.countPage = this.pagerCounts;
    }
  }
};
</script>

<style lang="scss" scoped>
.notAllowed {
  cursor: no-drop;
}
</style>
