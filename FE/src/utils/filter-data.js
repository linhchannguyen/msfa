import { formGroup } from '@/utils/state-filter';

var isRerendering = false;

// ID screen
function randomIntFromInterval(min, max) {
  // min and max included
  return Math.floor(Math.random() * (max - min + 1) + min);
}

const saveFilter = {};
saveFilter.install = function install(_Vue) {
  _Vue.directive('load-f5', {
    created(el, binding) {
      customSaveFilter(el, binding);
      closeFilterByInput(el, binding);

      isRerendering = true;
    },
    // save filter for modal
    updated(el, binding) {
      setTimeout(() => {
        var modalBody = [...document.querySelectorAll('.el-dialog__body')];
        if (modalBody && modalBody.length === 0) {
          save(binding, false);
        }
        binding.value = false;
      }, 50);
    }
  });
};
const event = (name, s, binding) => {
  s.addEventListener(name, () => {
    const iconClear = [...document.querySelectorAll('.el-input__clear')];
    if (iconClear && iconClear.length > 0) {
      iconClear.forEach((elm) => {
        // turn off event click of icon
        const btn = elm.closest('span');
        btn.style.cursor = 'pointer';
        const filter = document.querySelector('.dropdown-filter, dropdown-menu');

        btn.addEventListener(
          'click',
          () => {
            setTimeout(() => {
              if (filter) {
                filter.className = `${filter.className} show`;
                save(binding, true);
              }
            });
          },
          false
        );
      });
    }
  });
};
const closeFilterByInput = (el, binding) => {
  const arr = [...el.querySelectorAll(formGroup)];
  arr.forEach((s) => {
    event('mouseover', s, binding);
    event('keypress', s, binding);
  });
};

const customSaveFilter = (el, binding) => {
  renderDataFilter(binding);
  const event = el.querySelector('.btn-filter');
  const eventSettingSaveFilterItem = el.querySelectorAll('.setting-save-filter-item');

  if (event) {
    event.addEventListener('click', () => {
      submitFilter(el, binding);
      isRerendering = false;
    });
  }

  if (eventSettingSaveFilterItem) {
    eventSettingSaveFilterItem.forEach((elm) => {
      elm.addEventListener('click', () => {
        submitFilter(el, binding);
        isRerendering = false;
      });
    });
  }
};



const submitFilter = (el, binding) => {
  const filter = el.querySelector('.dropdown-menu');
  const eventSaveFilter = el.querySelectorAll('.save-filter-item');
  if (eventSaveFilter) {
    eventSaveFilter.forEach((elm) => {
      // elm.addEventListener('click', () => {
      //   console.log('click');
      //   saveData(binding);
      // }, false);
      elm.addEventListener('click', saveData(binding), false);
    }
    );
  }
  if (filter) {
    const btn = [...el.querySelectorAll('.btn-primary')];
    if (btn)
      btn.forEach((button) =>
        button.addEventListener('click', () => {
          filter.classList.remove('show');
          save(binding, false, true);
        })
      );
  }
};
const encodeData = (obj) => {
  // eslint-disable-next-line no-undef
  let strData = Buffer.from(JSON.stringify(obj), 'utf8').toString('base64');
  return strData;
};
const dataString = (data) => encodeData(data);
const save = (binding, isRemove, submit) => {
  let data = decodeData(localStorage.getItem('screen'));
  const isArray = Array.isArray(data);
  data = isArray ? decodeData(localStorage.getItem('screen')) : [];
  const screenName = binding.instance.$['type']['name']?.trim();
  // eslint-disable-next-line eqeqeq
  const data_filter = {
    ...binding.instance.$data,
    loading: false,
    loadingPage: false,
    isLoading: false,
    isShowModal: false,
    screen_name: binding.instance.$['type']['name'],
    screenId: randomIntFromInterval(1, 100)
  };
  setTimeout(() => {
    const equas = isArray ? data && data?.some((s) => s.screen_name?.trim() === screenName) : false;
    const parentScreen = document.querySelector('.screen_app')?.className?.split(' ')?.includes('parent');
    if (isRemove) {
      if (submit) {
        const index = data.findIndex((item) => item.screen_name === screenName);
        data[index] = data_filter;
        localStorage.setItem('screen', dataString(data));
      }
    } else {
      if (parentScreen) {
        if (isRerendering) {
          data = [];
          data.push(data_filter);
          localStorage.setItem('screen', dataString(data));
        } else {
          if (submit) {
            data = [];
            data.push(data_filter);
            localStorage.setItem('screen', dataString(data));
          }
        }
      } else {
        if (!equas) {
          if (submit) {
            data.push(data_filter);
            localStorage.setItem('screen', dataString(data));
          }
        } else {
          const parentChildScreen = document.querySelector('.screen_app')?.className?.split(' ')?.includes('parent_children');
          const index = data.findIndex((item) => item.screen_name === screenName);
          data[index] = submit ? data_filter : parentChildScreen ? data[index] : data_filter;
          localStorage.setItem('screen', dataString(data));
        }
      }
    }
  }, 50);
};

const saveData = (binding) => {
  let data = decodeData(localStorage.getItem('screen'));
  const isArray = Array.isArray(data);
  data = isArray ? decodeData(localStorage.getItem('screen')) : [];
  const screenName = binding.instance.$['type']['name']?.trim();
  // eslint-disable-next-line eqeqeq
  const data_filter = {
    ...binding.instance.$data,
    loading: false,
    loadingPage: false,
    isLoading: false,
    isShowModal: false,
    screen_name: binding.instance.$['type']['name'],
    screenId: randomIntFromInterval(1, 100)
  };
  const equas = isArray ? data && data?.some((s) => s.screen_name?.trim() === screenName) : false;
  const parentScreen = document.querySelector('.screen_app')?.className?.split(' ')?.includes('parent');

  if (parentScreen) {
    if (isRerendering) {
      data = [];
      data.push(data_filter);
      localStorage.setItem('screen', dataString(data));
    } else {
      data = [];
      data.push(data_filter);
      localStorage.setItem('screen', dataString(data));
    }
  } else {
    if (!equas) {
      data.push(data_filter);
      localStorage.setItem('screen', dataString(data));
    } else {
      const index = data.findIndex((item) => item.screen_name === screenName);
      data[index] = data_filter;
      localStorage.setItem('screen', dataString(data));
    }
  }
};
const decodeData = (sample) => {
  let obj = sample;
  if (typeof sample === 'string') {
    try {
      // eslint-disable-next-line no-undef
      let buffer = Buffer.from(sample, 'base64').toString('utf8');
      obj = JSON.parse(buffer);
    } catch (err) {
      return;
    }
  }
  return obj;
};
const renderDataFilter = (binding) => {
  const data = decodeData(localStorage.getItem('screen'));
  if (data && data.length > 0) {
    const screenName = binding.instance.$['type']['name'].trim();
    const equas = data && data?.some((s) => s.screen_name.trim() === screenName);
    // eslint-disable-next-line eqeqeq
    if (equas) {
      const index = data.findIndex((item) => item.screen_name === screenName);
      // gobal map data
      const keys = Object.keys(data[index]);
      if (keys)
        keys.forEach((key) => {
          if (keyNames.includes(key)) {
            binding.instance.$data[key] = data[index][key];
          }
        });
    } else {
      save(binding);
    }
  }
};
const keyNames = [
  'filter',
  'formData',
  'filterData',
  'filterSearch',
  'target_product_cd',
  'select_group_id',
  'facility_category_type',
  'municipality_name',
  'prefecture_name',
  'facility_cd',
  'facility_name',
  'isRelatedFacilitie',
  'facilityList',
  'activeNameItemBlock',
  'getDataDisplay',
  'lstMasterialSelected',
  'initParams',
  'listLectures',
  'usersFromToLike',
  'paramsZ05S01',
  'muniOrPreList',
  'lstSegmentTypeChecked',
  'facility_short_name',
  'user_name',
  'listPrecautions',
  'ultmarc_delete_flag',
  'phone',
  'municipality_name',
  'prefecture_name',
  'userName',
  'userCd',
  'orgCd',
  'director',
  'startDate',
  'knowledge_status',
  'offset',
  'limit',
  'statePassword',
  'password_change',
  'considerationTypes',
  'channelTypeList',
  'knowledAuthorClass',
  'topLike',
  'isR50',
  'ultmarc_delete_flag',
  'questionStatus',
  'questionCategorys',
  'productCdList',
  'muniOrPreList',
  'personList',
  'paramsZ05S03',
  'paramZ05S04',
  'lstTypeChecked',
  'selectStockPlaned',
  'paginationStock',
  'selectGroup',
  'formFilter4',
];

export { saveFilter };
