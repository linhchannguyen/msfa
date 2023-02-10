/*
 * common.js
 *
 *  version --- 3.6
 *  updated --- 2021/10/13
 */
/* !stack ------------------------------------------------------------------- */
'use strict';
import PerfectScrollbar from '@/assets/template/js/perfect-scrollbar.min.js';

window.addEventListener('load', function () {
  js_pscroll();
});
//
function js_pscroll() {
  let elements = document.querySelectorAll('.scrollbar');
  elements.forEach((el) => {
    let ps = new PerfectScrollbar(el, {
      useBothWheelAxes: false,
      suppressScrollX: true
    });
    window.addEventListener('resize', function () {
      ps.update();
    });
  });
}
