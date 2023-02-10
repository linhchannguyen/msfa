/* eslint-disable indent */
const FilterConfig = {};
FilterConfig.install = function install(_Vue) {
  _Vue.directive('click-outside-event', {
    mounted(el, binding) {
      el.clickOutsideEvent = function (event) {
        if (!(el === event.target || el.contains(event.target) || event.target.classList.contains('el-input__clear') || window.getSelection().type === 'Range')) {
          binding.value(event, el);
        }
      };
      document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
      document.body.removeEventListener('click', el.clickOutsideEvent);
    }
  });
};

export { FilterConfig };
