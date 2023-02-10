const loadingCustom = {};
loadingCustom.install = function install(_Vue) {
  _Vue.directive('loading', {
    // listen event
    created(el, binding) {
      const fixedLoading = document.createElement('div');
      fixedLoading.classList.add('fixed-loading');
      const loading_layer = document.createElement('div');
      loading_layer.classList.add('loading_layer');
      loading_layer.style.display = 'block';
      const wrapper = document.createElement('div');
      wrapper.classList.add('wrapper');
      const circle = document.createElement('div');
      circle.classList.add('circle');
      const shadow = document.createElement('div');
      shadow.classList.add('shadow');
      let info = document.createElement('div');
      info.classList.add('infor-loading');
      const text = document.createElement('span');
      text.classList.add('text-loading');
      text.innerText = 'Now loading';
      const dot = document.createElement('div');
      dot.innerText = '.';
      dot.classList.add('dot-loading');
      // appen
      const childNodesDot = [text, dot, dot, dot];
      childNodesDot.forEach((item) => {
        info.appendChild(item.cloneNode(true));
      });
      const childNodes = [circle, circle, circle, shadow, shadow, shadow, info];
      childNodes.forEach((el) => {
        wrapper.appendChild(el.cloneNode(true));
      });
      loading_layer.appendChild(wrapper);
      fixedLoading.appendChild(loading_layer);
      const checkLoader = binding.instance.checkLoading;
      !checkLoader ? (fixedLoading.id = 'no-loading') : (fixedLoading.id = 'loading...');
      if (binding.value && checkLoader) {
        loading_layer.style.visibility = 'visible';
        fixedLoading.style.visibility = 'visible';
      }
      setRenderLoadingA01S02(el);
      hideChartTootbar(el);
      setLoadChartZ02S01(el);
      setTimeout(() => {
        el.appendChild(fixedLoading);
      });
    },
    // update event
    updated(el, binding) {
      setTimeout(() => {
        var fixed_loading = [...(el && el.querySelectorAll('.fixed-loading'))];
        const loading_layer = [...(el && el.querySelectorAll('.loading_layer'))];
        if (binding.value) {
          if (fixed_loading.id === 'no-loading') {
            loading_layer.forEach((s) => (s.style.visibility = 'hidden'));
            fixed_loading[1].style.visibility = 'hidden';
            fixed_loading.forEach((s) => (s.style.visibility = 'hidden'));
          } else {
            loading_layer.forEach((s) => (s.style.visibility = 'visible'));
            fixed_loading.forEach((s) => (s.style.visibility = 'visible'));
          }
        } else {
          setTimeout(() => {
            loading_layer.forEach((s) => (s.style.visibility = 'hidden'));
            fixed_loading.forEach((s) => (s.style.visibility = 'hidden'));

            fixed_loading.id = 'loading...';
          });
        }
      });
    }
  });
};

const setLoadChartZ02S01 = (el) => {
  const chart = [...el.querySelectorAll('.barChart')];
  if (chart)
    chart.forEach((s) => {
      const el = s && s.firstChild;
      el.style.position = 'absolute';
      el.style.display = 'none';
    });
  setTimeout(() => {
    // reload element
    if (chart)
      chart.forEach((s) => {
        const el = s && s.firstChild;
        el.style.position = null;
        el.style.display = null;
      });
  }, 15000);
};
const hideChartTootbar = (el) => {
  const chart = el.querySelectorAll('.apexcharts-toolbar');
  chart ? chart.forEach((s) => s.remove()) : null;
};
const slideLoader = {};
slideLoader.install = function install(_Vue) {
  _Vue.directive('slide-loading', {
    // listen event
    created(el, binding) {
      const slider = document.createElement('div');
      slider.classList.add('slider-load');
      slider.style.display = binding.value ? 'block' : 'none';
      const wrapper = document.createElement('div');
      wrapper.classList.add('wrapper');
      const circle = document.createElement('div');
      circle.classList.add('circle');
      const childNodes = [circle, circle, circle];
      childNodes.forEach((el) => {
        wrapper.appendChild(el.cloneNode(true));
      });
      slider.appendChild(wrapper);
      el.appendChild(slider);
    },
    // update event
    updated(el, binding) {
      const loading_layer = el.querySelector('.slider-load');

      if (!binding.value) {
        setTimeout(() => {
          loading_layer.style.display = 'none';
        }, 1200);
      } else {
        loading_layer.style.display = 'block';
      }
    }
  });
};

let loadCalendar = {};
loadCalendar.install = function install(_Vue) {
  _Vue.directive('load-calendar', {
    // listen event
    created() {
      setRenderLoadingA01S02();
    },
    updated() {
      setRenderLoadingA01S02();
    }
  });
};
const setRenderLoadingA01S02 = () => {
  setTimeout(() => {
    const row = document.querySelectorAll('.fc-col-header, .fc-daygrid-body, .fc-daygrid-body table, .fc-timegrid-body, .fc-timegrid-body table');
    row.forEach((s) => (s.style.width = '100%'));
  });
};

const loadingContainer = {};
loadingContainer.install = function install(_Vue) {
  _Vue.directive('loading-container', {
    // listen event
    created(el, binding) {
      const fixedLoading = document.createElement('div');
      fixedLoading.classList.add('fixed-loading-container');
      const loading_layer = document.createElement('div');
      loading_layer.classList.add('loading_layer-container');
      loading_layer.style.display = 'block';
      const wrapper = document.createElement('div');
      wrapper.classList.add('wrapper');
      const circle = document.createElement('div');
      circle.classList.add('circle');
      const shadow = document.createElement('div');
      shadow.classList.add('shadow');
      let info = document.createElement('div');
      info.classList.add('infor-loading');
      const text = document.createElement('span');
      text.classList.add('text-loading');
      text.innerText = 'Now loading';
      const dot = document.createElement('div');
      dot.innerText = '.';
      dot.classList.add('dot-loading');
      // appen
      const childNodesDot = [text, dot, dot, dot];
      childNodesDot.forEach((item) => {
        info.appendChild(item.cloneNode(true));
      });
      const childNodes = [circle, circle, circle, shadow, shadow, shadow, info];
      childNodes.forEach((el) => {
        wrapper.appendChild(el.cloneNode(true));
      });
      loading_layer.appendChild(wrapper);
      fixedLoading.appendChild(loading_layer);

      if (binding.value) {
        loading_layer.style.visibility = 'visible';
        fixedLoading.style.visibility = 'visible';
      } else {
        loading_layer.style.visibility = 'hidden';
        fixedLoading.style.visibility = 'hidden';
      }
      setTimeout(() => {
        el.appendChild(fixedLoading);
      });
    },
    // update event
    updated(el, binding) {
      setTimeout(() => {
        const fixed_loading = el.querySelector('.fixed-loading-container');
        const loading_layer = el.querySelector('.loading_layer-container');
        
        if (binding.value === binding.oldValue) return;

        if (binding.value) {
          loading_layer.style.visibility = 'visible';
          fixed_loading.style.visibility = 'visible';
        } else {
          setTimeout(() => {
            loading_layer.style.visibility = 'hidden';
            fixed_loading.style.visibility = 'hidden';
          });
        }
      });
    }
  });
};

export { loadingCustom, loadCalendar, slideLoader, loadingContainer };
