<template>
  <!-- <template>Mouse position is at: {{ x }}, {{ y }}</template> -->
  <router-view></router-view>
</template>

<script>
import data from 'LibTest/data.json';
import { mapActions } from 'vuex';
// import { ref } from 'vue';
import { useMouse } from '@/utils/hook/useMouse';
export default {
  name: 'App',
  setup() {
    const { x, y } = useMouse();
    return { x, y };
  },
  computed: {
    ...mapActions({
      logout: 'auth/logout'
    })
  },
  created() {
    localStorage.setItem('$_C', this.enCodeData(JSON.stringify(data)));
  }
};
</script>

<style lang="scss">
@import '~bootstrap/dist/css/bootstrap.min.css';
@import '~animate.css/animate.min.css';
@import '@/assets/css/_style.scss';
@import './assets/template/css/slick';
@import './assets/template/scss/styles.scss';
#app {
  height: 100%;
}
.modal-backdrop {
  display: none;
}
.modal {
  background-color: rgba(10, 10, 10, 0.45);
}
</style>
