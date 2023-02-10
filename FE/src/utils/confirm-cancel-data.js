import { markRaw } from 'vue';

var listValidate = [];

const loadElenmet = (el, binding) => {
  const formGoup = [
    ...el.querySelectorAll(
      '.formGroup, .form-control-datePickerLeft, .custom-checkbox, .form-control-input, .form-control-select .el-input, .form-icon > .form-control, .form-control-time, .form-icon > .form-control > .block-tags'
    )
  ];
  const cancel = el.querySelector('.btn-outline-primary--cancel');
  binding.instance.closeModal = null;
  binding.instance.cancelBtn = null;
  localStorage.setItem('formData', JSON.stringify(binding.instance.$data));
  if (formGoup) setUpForm(formGoup, el, cancel, binding);
  addClass(formGoup, el, cancel, binding);
};
const closeModalAll = (el) => {
  const elOverlay = el.parentElement.closest('.el-overlay');
  setTimeout(() => {
    if (elOverlay) {
      elOverlay.classList.add('hiden_popup');
      elOverlay.classList.remove('show_popup');
    }
  });
};
const setFormValidate = (el) => {
  const checkvalidFeedback = [...el.querySelectorAll('.invalid-feedback, .text-error')].map(
    (s) => [...s.classList].includes('invalid-feedback') || [...s.classList].includes('text-error')
  );
  // parent hide
  if (checkvalidFeedback) {
    if (checkvalidFeedback.includes(true)) {
      return true;
    } else {
      return false;
    }
  }
};
// const eventListener = (eventName, item, i) => {
//   item.addEventListener(eventName, (e) => {
//     listValidate[i].value = e.target.value;
//     console.log(listValidate[i]);
//   });
// };
const addClass = (formGoup, el, cancel, binding) => {
  const btn = [...el.querySelectorAll('.add-confim > .btn-radius')];
  if (btn) {
    btn.forEach((elBtn) => {
      elBtn.addEventListener('click', () => {
        setTimeout(() => {
          formGoup = [
            ...el.querySelectorAll(
              '.formGroup, .form-control-datePickerLeft, .custom-checkbox, .form-control-input, .form-control-select .el-input, .form-icon > .form-control'
            )
          ];
          setUpForm(formGoup, el, cancel, binding);
        });
      });
    });
  }
  addChoice(formGoup, el, cancel, binding);
};
const addChoice = (formGoup, el, cancel, binding) => {
  const btn = [...el.querySelectorAll('.form-icon > .icon')];
  if (btn) {
    btn.forEach((elBtn) => {
      elBtn.addEventListener('click', () => {
        setTimeout(() => {
          const submit = [...document.querySelectorAll('.btn-primary')];
          submit.forEach((sub) => {
            sub.addEventListener('click', () => {
              const close = [...el.querySelectorAll('.el-icon-close')];
              close.forEach((item) => {
                item.addEventListener('click', () => {
                  setUpForm(formGoup, el, cancel, binding);
                });
              });
              setUpForm(formGoup, el, cancel, binding);
            });
          });
        });
      });
    });
  }
};
const setUpForm = (formGoup, el, cancel, binding) => {
  cancel = el.querySelector('.btn-outline-primary--cancel');

  formGoup = [
    ...el.querySelectorAll(
      '.formGroup, .form-control-datePickerLeft, .custom-checkbox, .form-control-input, .form-control-select .el-input, .form-icon > .form-control, .form-control-time, .form-icon > .form-control > .block-tags'
    )
  ];
  formGoup.forEach((item, y) => {
    item.setAttribute('defaultValue', item.children[0]['value'] ? item.children[0]['value'] : '');

    listValidate.push({ oldValue: item.getAttribute('defaultValue'), value: '', el: item });
    if (cancel) {
      cancel.classList.add('btn-cancel-confirm');
      cancel.addEventListener('click', () => {
        const newValue = item.children[0]['value'] ? item.children[0]['value'] : item.children[0].innerText.length > 0 ? item.children[0].innerText : '';
        if (item.getAttribute('defaultValue') !== newValue) {
          binding.instance.showConfirmCancel(() => {
            const isComponent = binding.instance.$data['modalConfig'];
            const configModala = {
              ...binding.instance.$data['modalConfig'],
              title: '',
              isShowModal: true,
              props: { mode: 1 },
              component: markRaw(binding.instance.$PopupConfirm)
            };
            isComponent ? (binding.instance.$data['modalConfig'] = configModala) : (binding.instance['modalConfig'] = configModala);
          });
          closeModalAll(el);
        } else {
          if (!listValidate.map((s) => s.oldValue === item.children[0]['value'])) {
            binding.instance.$emit('onFinishScreen', 2);
            binding.instance.cancelBtn ? binding.instance.$route.go(-1) : null;
          }
          if (setFormValidate(el)) {
            binding.instance.$emit('onFinishScreen', 2);
          }
        }
        setTimeout(() => {
          const subMitBtn = document.querySelector('.btn-yes-cancel');
          if (subMitBtn)
            subMitBtn.addEventListener('click', () => {
              const oldData = JSON.parse(localStorage.getItem('formData'));
              const keys = Object.keys(oldData);
              if (keys) {
                keys.forEach((key) => {
                  binding.instance.$data[key] = oldData[key];
                });
              }
              item.children[0].innerText = listValidate[y].oldValue;
              item.children[0].value = listValidate[y].oldValue;
              binding.instance.$emit('onFinishScreen', 2);
            });
        });
      });
    }
  });
};
const checkConfirm = {};
checkConfirm.install = function install(_Vue) {
  _Vue.directive('confirm', {
    created(el, binding) {
      listValidate = [];
      setTimeout(() => {
        loadElenmet(el, binding);
      });
    }
  });
};
export { checkConfirm };
