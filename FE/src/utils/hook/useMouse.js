// mouse.js
import { ref, onMounted, onUnmounted } from 'vue';
import store from '@/store';
// by convention, composable function names start with "use"
export function useMouse() {
  // state encapsulated and managed by the composable
  const x = ref(0);
  const y = ref(0);
  // a composable can update its managed state over time.
  function update(event) {
    let target = event?.composedPath() || event?.composedPath;

    let timepicker;
    let timepickerScroll;
    let timepickerClose;
    let scrollList;

    let len = target.length;

    try {
      while (len--) {
        let item = target[len];
        if (item.classList?.contains('timepicker')) timepicker = item;
        if (item.classList?.contains('scrollbarTime')) timepickerScroll = item;
        if (item.classList?.contains('timepicker_control-close')) timepickerClose = item;
        if (item.classList?.contains('scrollbar')) scrollList = item; // get div.scrollbar
      }

      let el = document.querySelector('.timepicker_dropdown');

      if (!timepicker || timepickerClose) {
        el.classList.add('display-none');
      } else {
        if ((el && el.classList.contains('display-none')) || (el && el.classList.contains('display') && timepickerScroll)) {
          el.classList.remove('display-none');
          el.classList.add('display');
        } else {
          el.classList.add('display-none');
          el.classList.remove('display');
        }
      }
    } catch (err) {
      /**
       *
       */
    }

    /**
     * div.scrollbar
     * --- ul
     * ------ li scroll-attr="1"
     * ------ li scroll-attr="2"
     * ------ li scroll-attr="3"
     * ------ li scroll-attr="..."
     * ------ li scroll-attr="...."
     */
    if (!scrollList) return;
    let index = -1;

    try {
      index = parseInt(target?.find((item) => item?.hasAttribute('scroll-attr'))?.getAttribute('scroll-attr'));
    } catch (err) {
      index = -1;
    }

    if (index !== -1) {
      //dispatch (index)...
      let params = {
        x: event.pageX,
        y: event.pageY,
        index,
        screen: window.location.pathname
      };
      store.dispatch('router/itemHistoryIndex', params);
    }

    // await new Promise((resolve) => setTimeout(resolve, 500));

    let dialog = document.querySelectorAll('.el-dialog__body');
    if (dialog.length) {
      dialog[dialog.length - 1].addEventListener('click', update);
    }
  }

  onMounted(() => {
    document.addEventListener('click', update);
  });

  onUnmounted(() => {
    document.removeEventListener('click', update);
  });

  // expose managed state as return value
  return { x, y };
}
