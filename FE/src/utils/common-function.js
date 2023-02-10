/* eslint-disable no-useless-escape */
/* eslint-disable max-len */
const Device = () => {
  let device = 'Desktop';
  let userAgent = window.navigator.userAgent;

  let isIOS = (/iPad|iPhone|iPod/.test(window.navigator.platform) ||
    (window.navigator.platform === 'MacIntel' && window.navigator.maxTouchPoints > 1)) &&
    !window.MSStream;

  // if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(userAgent)) {
  //   device = 'Tablet';
  // }
  // else if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(userAgent)) {
  //   device = 'Mobile';
  // }

  if (isIOS) {
    if (window.screen.width > 700) {
      device = 'Tablet';
    } else device = 'Mobile';
  } else {
    if (
      /(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|w3c|acs\-|alav|alca|amoi|audi|avan|benq|bird|blac|blaz|brew|cell|cldc|cmd\-|dang|doco|eric|hipt|inno|ipaq|java|jigs|kddi|keji|leno|lg\-c|lg\-d|lg\-g|lge\-|maui|maxo|midp|mits|mmef|mobi|mot\-|moto|mwbp|nec\-|newt|noki|palm|pana|pant|phil|play|port|prox|qwap|sage|sams|sany|sch\-|sec\-|send|seri|sgh\-|shar|sie\-|siem|smal|smar|sony|sph\-|symb|t\-mo|teli|tim\-|tosh|tsm\-|upg1|upsi|vk\-v|voda|wap\-|wapa|wapi|wapp|wapr|webc|winw|winw|xda|xda\-) /i.test(
        userAgent
      )
    )
      if (/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i.test(userAgent)) device = 'Tablet';
      else device = 'Mobile';
    else if (/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i.test(userAgent)) device = 'Tablet';
  }

  return device;
};

const getBrowserName = () => {
  const userAgent = window.navigator.userAgent.toLowerCase();
  switch (true) {
    case userAgent.indexOf('edge') > -1: return 'MS Edge';
    case userAgent.indexOf('edg/') > -1: return 'Edge (chromium based)';
    case userAgent.indexOf('opr') > -1 && !!window.opr: return 'Opera';
    case userAgent.indexOf('chrome') > -1 && !!window.chrome: return 'Chrome';
    case userAgent.indexOf('trident') > -1: return 'MS IE';
    case userAgent.indexOf('firefox') > -1: return 'Mozilla Firefox';
    case userAgent.indexOf('safari') > -1: return 'Safari';
    default: return 'Unknown';
  }
};
const fnBrowserDetect = () => {
  let userAgent = navigator.userAgent || window.navigator.userAgent;
  let browserName = 'Unknown';

  if (userAgent.match(/chrome|chromium|crios/i)) {
    browserName = 'Chrome';
  } else if (userAgent.match(/firefox|fxios/i)) {
    browserName = 'Firefox';
  } else if (userAgent.match(/safari/i)) {
    browserName = 'Safari';
  } else if (userAgent.match(/opr\//i)) {
    browserName = 'Opera';
  } else if (userAgent.match(/edg/i)) {
    browserName = 'Edge';
  }
  return browserName;
};

const UUID = () => {
  let dt = new Date().getTime();
  const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
    const r = (dt + Math.random() * 16) % 16 | 0;
    dt = Math.floor(dt / 16);
    return (c === 'x' ? r : (r & 0x3) | 0x8).toString(16);
  });
  return uuid;
};

export {
  Device,
  UUID,
  getBrowserName,
  fnBrowserDetect
};