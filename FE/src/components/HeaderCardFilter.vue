<script>
export default {
  name: 'HeaderCardFilter',
  props: {showBack: Boolean, showAdd: Boolean, showFilter: Boolean, showSetting: Boolean},
  emits: ['onClick'],
  data: function () {
    return {};
  },
  created: function () {},
  methods: {
    onBack() {
      this.$emit('onClick', 'back');
    },

    onAddNew() {
      this.$emit('onClick', 'add');
    },

    onFilter() {
      this.$emit('onClick', 'filter');
    },

    onSetting() {
      this.$emit('onClick', 'setting');
    }
  }
};
</script>

<template>
  <div class="block">
    <div class="block-left">
      <div v-show="showBack" class="back" @click="onBack()">
        <img src="../assets/images/Vectorback.png" />
        <span>戻る</span>
      </div>
      <el-button v-show="showAdd" class="btn" @click="onAddNew()">
        <i class="el-icon-plus"><span>新規登録</span></i>
      </el-button>
    </div>
    <div class="block-right">
      <el-button v-show="showFilter" class="btn btn-filter" @click="onFilter()">
        <img src="../assets/images/VectorFilter.png" />
      </el-button>
      <el-button v-show="showSetting" class="btn" @click="onSetting()">
        <i class="el-icon-s-tools"><span>通知設定</span></i>
      </el-button>
    </div>
  </div>
</template>

<style lang="scss" scoped>
$text-grey-1: #99a5ae;
$text-grey-2: #5f6b73;

.block {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}

.block-left {
  align-items: center;
  display: flex;
}

.block-right {
  display: flex;
  flex-direction: row-reverse;
  align-items: stretch;
  grid-column-gap: 22px;
}

.btn {
  height: 42px;
  margin: 10px 0px;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  padding: 0px 25px 3px;
  border-radius: 0px 0px 8px 8px;
  font-weight: bold;
  letter-spacing: 0.03em;
  color: #59a5ff;
  background: #ffffff;
  box-shadow: 0px 2px 4px rgba(13, 94, 153, 0.1), 0.5px 1px 5px 1px rgba(203, 225, 241, 0.3);
  i {
    font-weight: bold;
    span {
      margin-left: 5px;
    }
  }
}

.btn-filter {
  width: 42px;
  padding: 0px 11px 3px;
  padding: auto;
}

.back {
  cursor: pointer;
  color: $text-grey-1;
  font-size: 14px;
  span {
    margin-left: 10px;
  }
}
</style>
