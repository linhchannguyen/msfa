<template>
  <div class="dropdown-menu dropdown-select">
    <ul>
      <li v-for="item of dataSelects" :key="item.id" :class="{ active: active_el === item.id }" :disabled="item.disabled" @click="activate(item.id)">
        <button :class="{ active: active_el === item.id }" :disabled="item.disabled" class="button-custom">{{ item.name }}</button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'ModalNotificationSelect',
  emits: ['closeSelect'],
  data() {
    return {
      dataSelects: [
        { id: 1, name: 'すべて選択', disabled: false },
        { id: 2, name: 'すべて解除', disabled: false },
        { id: 3, name: 'アーカイブ', disabled: false },
        { id: 4, name: 'アーカイブ取消', disabled: false }
      ],
      active_el: 0,
      checkNoti: false
    };
  },
  mounted() {
    this.dataSelects.forEach((element) => {
      if (this.active_el === 0) {
        element.disabled = true;
      }
      if (element.id === 1) {
        element.disabled = false;
      }
    });
    this.emitter.on('checkNoti', ({ checkNoti }) => {
      if (checkNoti) {
        this.dataSelects.forEach((element) => {
          element.disabled = false;
        });
      } else {
        this.dataSelects.forEach((element) => {
          element.disabled = true;
          this.active_el = 0;
          if (element.id === 1) {
            element.disabled = false;
          }
        });
      }
    });
    this.emitter.on('checkAll', ({ checkNoti }) => {
      if (checkNoti) {
        this.activate(1);
      } else {
        this.activate(2);
      }
    });
  },
  methods: {
    activate(el) {
      this.active_el = el;
      if (el === 1) {
        this.dataSelects.forEach((element) => {
          element.disabled = false;
        });
      }
      if (el === 2 || el === 3 || el === 4) {
        this.dataSelects.forEach((element) => {
          element.disabled = true;
          this.active_el = 0;
          if (element.id === 1) {
            element.disabled = false;
          }
        });
      }
      this.$emit('closeSelect', el);
    }
  }
};
</script>

<style lang="scss" scoped>
.button-custom {
  width: 100%;
  height: 100%;
  text-align: start;
}
li {
  &:hover {
    background: #eef6ff;
    color: #2d3033 !important;
  }
}
.active {
  background: #eef6ff;
  color: #2d3033 !important;
}
.dropdown-select {
  z-index: 9999;
}
.dropdown-menu {
  width: 100%;
  font-size: 14px;
  ul {
    li {
      padding: 4px 16px;
      font-weight: 500;
      font-size: 14px;
      line-height: 20px;
      color: #2d3033 !important;
      cursor: default;
      button {
        -moz-appearance: none;
        color: #2d3033 !important;
      }
    }
  }
}
</style>
