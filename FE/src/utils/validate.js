/* eslint-disable */
function required(val) {
  // Convert html -> text
  let parser = new DOMParser();

  let stringText = parser.parseFromString(val, 'text/html')?.body?.firstChild?.textContent;

  if (stringText) return !!stringText.trim();

  // if (val) return !!val.toString().trim();
  return false;
}

function numMinMax(val, min, max) {
  let n = Math.floor(Number(val));
  if (n !== Infinity && n >= 0) {
    if (val >= min && val <= max) {
      return true;
    }
  }
  return false;
}

function isExternal(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  return /^(https?:|mailto:|tel:)/.test(val);
}

function validURL(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const reg =
    /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/;
  return reg.test(val);
}

function validLowerCase(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const reg = /^[a-z]+$/;
  return reg.test(val);
}

function validUpperCase(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const reg = /^[A-Z]+$/;
  return reg.test(val);
}

function validAlphabets(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const reg = /^[A-z]+$/;
  return reg.test(val);
}

function validHalfWidthAlphanumeric(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const reg = /^[0-9a-zA-Z\d\s_-]+$/;
  return reg.test(val);
}

function validAlphaJa(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re = /^[\s\A-z\ｦ-ﾟ\u30a0-\u30ff\u3040-\u309f\u3005-\u3006\u30e0-\u9fcf]+$/;
  return re.test(val);
}

function validKana(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re = /^[\s\ｦ-ﾟ\u30a0-\u30ff()（）0-9０-９]+$/;
  return re.test(val);
}

function validLength(val, length) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  return val.length <= length;
}

function validNumberLength(val, length) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined) return null;
  const re = /^[0-9\s]+$/;
  return re.test(val) && val.length === length;
}

function validZip(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re = /^\d{7}$/;
  return re.test(val);
}

function validTel(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re = /^\d{11}$|^\d{3,4}-\d{3,4}-\d{4}$/;
  return re.test(val);
}

function validEmail(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re =
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(val);
}

function validPassword(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re = /^(?=.*?[a-z])(?=.*?\d)[a-z\d\s]{8,50}$/i;
  return re.test(val);
}

function validLengthPassword(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re = new RegExp('^(?=.{8,25})');
  return re.test(val);
}

function validPasswordHalfFullWidth(val) {
  if (val) val = val.toString().trim();
  if (val === null || val === undefined || val === '') return true;
  const re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[a-zA-Z])[\x20-\x7E]{8,25}$/i;
  return re.test(val);
}

function validImageUpload(headerFileString) {
  let typeFile = '';
  switch (headerFileString.toLowerCase()) {
    case '89504e47':
      typeFile = 'image/png';
      break;
    case '47494638':
      typeFile = 'image/gif';
      break;
    case 'ffd8ffe0':
    case 'ffd8ffe1':
    case 'ffd8ffdb':
      typeFile = 'image/jpg';
      break;
    case 'ffd8ffe2':
    case 'ffd8ffe3':
    case 'ffd8ffe8':
      typeFile = 'image/jpeg';
      break;
    default:
      typeFile = 'unknown';
      break;
  }
  return typeFile;
}

export {
  required,
  numMinMax,
  isExternal,
  validURL,
  validLowerCase,
  validUpperCase,
  validAlphabets,
  validHalfWidthAlphanumeric,
  validAlphaJa,
  validKana,
  validLength,
  validNumberLength,
  validZip,
  validTel,
  validEmail,
  validPassword,
  validImageUpload,
  validLengthPassword,
  validPasswordHalfFullWidth
};
