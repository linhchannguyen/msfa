var elScreen = [
  '.confirmBtn > button',
  '.formMessage-btn  > .btn-primary',
  '.block-btn > .btn-primary',
  '.block-btn > .btn-outline-primary--cancel',
  '.facilityBtn > .btn-primary',
  '.facilityBtn > .btn-outline-primary--cancel',
  '.materialList-btn > .btn-outline-primary--cancel',
  '.formGroup-btn >.btn-primary',
  'td > .planningStatus .dropdown-menu ul li',
  '.btnInfo > .btn-sign',
  '.headAcc-btn  .btn-outline-danger',
  '.confirmBtn > .btn-outline-danger',
  '.wrap >  .btn-signUp',
  '.el-tabs__item > .create-icon ',
  '.tblUserStatus-btn > .dropdown-menu > ul > li ',
  '.title > .btn-addDoc',
  '.lectureTarget-add > .btn-radius',
  '.show_modal_parent',
  '.attendees-btn > .btn-radius',
  '.button-add',
  '.create-ppt',
  '.list-title >.tlt',
  '.interviewHeade > ul > li > .btn-link--custom',
  '.facility-form > ul > li > .btn-radius',
  '.icon-right > .icon',
  '.btn-signUp',
  '.create-icon',
  '.addition-label > a',
  '.btn-custom',
  '.bodyContainer-title .btn-radius',
  '.facility-head-btn .btn-radius',
  '.addition-label',
  '.event-click',
  '.btnCreate',
  '.async-modal',
  '.block-left > .card-item_header > h2 > .link',
  '.contentSetting-lst > ul > li > .contentSetting-link > a',
  '.lstMaterial  > ul > li > .lstMaterial-tlt > .link',
  '.loginUser > .icon-right > .icon',
  '.dropdown-menu ul li button',
  '.btn--arrow',
  '.dropdown-select > li > .button-custom',
  '.material-child',
  '.a02s03Tod01s02',
  '.block-header > .card-item_header> .btn--icon',
];
var showModalChildBody = [
  '.icon-right > .icon',
  '.approvalEdit-addition > .btn',
  '.contentSetting-body > .title > .btn-radius',
  '.LectureSeriesSelection-btn > .btn-primary',
  '.confirmBtn > .btn-outline-primary',
  '.btn-delete',
  '.btn-test',
  'div:not(.handle-close) > div > .el-dialog__headerbtn',
  '.answer-btn:not(.answer-confirm) > .btn-outline-primary--cancel',
];

var modals = [];
var cancelName = ['キャンセル', 'いいえ', '閉じる'];

const targetParent = (element) => [...element.getElementsByClassName('el-overlay')].sort((a, b) => b.style.zIndex - a.style.zIndex);
const queryEvent = (element) => [...element.querySelectorAll(elScreen.join() + `, ${showModalChildBody.join()}`)];
const add = (element, name) => (element ? element.classList.add(name) : false);
const remove = (element, name) => (element ? element.classList.remove(name) : false);
const isSubmit = (element) => [...element.classList].includes('btn-primary');
const isCancel = (element) => [...element.classList].includes('btn-outline-primary--cancel');
const isClose = (element) => [...element.classList].includes('el-dialog__headerbtn');
const isConfirmDelete = (element) => [...element.classList].includes('btn-outline-danger');
const closeModal = (el) => {
  const checkCancel = isCancel(el) && cancelName.includes(el.innerText);
  const checkSubmit = isSubmit(el);
  const checkCloseHeader = isClose(el);
  const checkRemove = isConfirmDelete(el);
  if (checkCancel || checkCloseHeader || checkSubmit || checkRemove) {
    el.addEventListener('click', () => {
      endPopup(el.closest('.el-overlay'));
    });
  }
};

const openModal = (el, index) => {
  el.addEventListener('click', () => {
    startPopup(modals, index);
  });
  el.addEventListener(
    'touchstart',
    () => {
      startPopup(modals, index);
    },
    { passive: true }
  );
};
const endPopup = (modalActive) => {
  modals = modals.filter((s) => s.style.display === '');
  if (modalActive) {
    const index = modals.findIndex((s) => s.style.zIndex === modalActive.style.zIndex);
    const child = index + 1;
    if (child) {
      setShowPopup(modals[child]);
    } else {
      setShowPopup(modals[index]);
    }
    if (modals.length === 1) {
      resetGroup(modals[index]);
    }
  }
};
const startPopup = (modals, index) => {
  resetGroup(modals[index]);
  if (index !== 0) {
    setHidePopup(modals[index]);
  }
  modals = modals.filter((s) => s.style.display !== 'none');
  add(modals[0], 'show_popup');
  removeCache();
};
const resetGroup = (modal) => {
  remove(modal, 'hiden_popup');
  remove(modal, 'show_popup');
};
const setShowPopup = (modal) => {
  add(modal, 'show_popup');
  remove(modal, 'hiden_popup');
};
const setHidePopup = (modal) => {
  remove(modal, 'show_popup');
  add(modal, 'hiden_popup');
};

// remove conflig  hide and show class popup
const removeCache = () => {
  const checkCache = modals.some((s) => s.className.split(' ').includes('show_popup') && s.className.split(' ').includes('hiden_popup'));
  if (checkCache) {
    const i = modals.findIndex((s) => s.className.split(' ').includes('show_popup') && s.className.split(' ').includes('hiden_popup'));
    remove(modals[i], 'hiden_popup');
  }
};

const nextShowModal = (el, index, closeParent) => {
  openModal(el, index);
  closeParent(el);
};

const createGroups = (events, i) => {
  events = queryEvent(modals[i]);
  if (events && events.length > 0) {
    events.forEach((elEvent) => {
      nextShowModal(elEvent, i, closeModal);
    });
  }
};
var setEvent = (el) => {
  modals = targetParent(el);
  let events = [];
  for (let i = 0; i < modals.length; i++) {
    // eslint-disable-next-line no-unused-vars
    if (modals[i].style.display !== 'none') {
      createGroups(events, i);
    }
  }
};
const modalConfig = {};
modalConfig.install = function install(_Vue) {
  _Vue.directive('setup-modal', {
    updated(e) {
      setTimeout(() => {
        const screen = e.querySelector('.screen_app');
        if (screen?.firstChild) {
          screen?.firstChild.classList.add('minHeight');
        }
      }, 200);
    },
    created(el) {
      el.addEventListener('mouseover', () => {
        setEvent(el);
      });
      // click ESC
      window.addEventListener(
        'keydown',
        function (e) {
          if ((e.key === 'Escape' || e.key === 'Esc' || e.keyCode === 27) && e.target.nodeName === 'BODY') {
            e.preventDefault();
            e.stopPropagation();

            return false;
          }
        },
        true
      );
    }
  });
};

export { modalConfig };
