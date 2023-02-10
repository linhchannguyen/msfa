const formGroup = [
  '.form-control-input',
  '.formGoup-filter-control > .btnSelect',
  '.formNotes-checkBox-f > ul',
  '.groupForm-controlChannel > ul',
  '.knowledAuthor > ul',
  '.groupForm-controlChannel > ul',
  '.formNotes-checkBox > ul',
  '.displayOrder > ul',
  '.block-search_check',
  '.el-form-item > .el-form-item__content > div > input ',
  '.el-date-editor--date > input',
  '.form-control-select',
  '.form-icon > .icon',
  '.el-date-editor > .el-input__suffix > .el-input__suffix-inner > .el-input__icon',
  '.form-swicth-status > ul',
  '.form-flag-checkbox > label > input',
  '.block-search_check',
  '.form-management-control > .form-management-select',
  '.facilityForm-btnSelect',
  '.facilityForm-control > .checkDeleted',
  '.facilityForm-control > .form-icon > .icon',
  '.passwordChange-checkbox  input',
  '.form-InFacility-check > ul',
  '.form-cont > ul',
  '.form-cont > .checkbox-list',
  '.B01-S01-status > ul',
  '.B01-S01-checkList',
  '.groupSearch-control > input',
  '.groupSearch-view',
  '.groupSearch-channel > ul',
  '.btn-primary',
  '.lectureGroup'
].join(',');
// const formSelect = ['.el-select-dropdown > .el-scrollbar > div > ul > li'].join(',');
// const formChoice = {
//   datePicker: ['.el-picker__popper > .el-date-picker > div > .el-picker-panel__body > .el-picker-panel__content'].join(','),
//   close: ['.el-date-editor > .el-input__suffix > .el-input__suffix-inner > .el-input__icon'].join(','),
//   modal: [
//     '.el-overlay .btn-primary',
//     '.el-overlay .btn-outline-primary--cancel',
//     '.wrapContent .el-overlay .el-dialog__body .pre-minH .form-z05-s02',
//     '.form-z05-s02'
//   ].join(','),
//   tagClose: ['.block-tags > span > .el-icon-close']
// };
// const setD01S04 = (binding, datas) => {
//   /** 講演会番号 */

//   const formStatus = document.querySelector('.form-management-control > .form-management-select');
//   if (formStatus) {
//     formStatus.childNodes.forEach((el) => {
//       el.addEventListener('click', (event) => {
//         const disuse_flag = event.target.closest('li').id;
//         const data = { ...datas, formData: { ...datas.formData, disuse_flag: disuse_flag } };
//         localStorage.setItem(binding.instance.$['type']['name'], JSON.stringify(data));
//       });
//     });
//   }
// };
// const setC01S01 = (binding, datas) => {
//   /** 講演会番号 */

//   const formStatus = document.querySelector('.form-swicth-status > ul');
//   if (formStatus) {
//     formStatus.childNodes.forEach((el) => {
//       el.addEventListener('click', (event) => {
//         const status = event.target.closest('li').id;
//         const data = { ...datas, formData: { ...datas.formData, status_type: status } };
//         localStorage.setItem(binding.instance.$['type']['name'], JSON.stringify(data));
//       });
//     });
//   }

//   /**   <!-- check flag --> */
//   const checkBox = document.querySelectorAll('.form-flag-checkbox > label > input');
//   if (checkBox && checkBox[0]) {
//     let data = { ...datas };
//     const checkBoxFuc = (num, field) => {
//       [...checkBox][num].addEventListener('click', () => {
//         if (data['formData']['remand_flag']) {
//           data = { ...datas, formData: { ...datas.formData, [field]: false } };
//           localStorage.setItem(binding.instance.$['type']['name'], JSON.stringify(data));
//         } else {
//           data = { ...datas, formData: { ...datas.formData, [field]: true } };
//           localStorage.setItem(binding.instance.$['type']['name'], JSON.stringify(data));
//         }
//       });
//     };
//     checkBoxFuc(0, 'checkBoxFuc');
//     checkBoxFuc(1, 'approved_flag');
//   }

//   // console.log(s);
// };
export { formGroup };
