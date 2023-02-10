const decodeDataBase64 = (sample) => {
  let obj = sample;
  if (typeof sample === 'string') {
    try {
      // eslint-disable-next-line no-undef
      let buffer = Buffer.from(sample, 'base64').toString('utf8');
      obj = JSON.parse(buffer);
    } catch (err) {
      // console.log(err);
    }
  }
  return obj;
};

let data = JSON.parse(decodeDataBase64(localStorage.getItem('$_C')));
let enable_base64 = data?.enable_base64;

const randomString = (length = 5) => {
  let text = '';
  const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789|+=';

  for (var i = 0; i < length; i++) text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
};

const encodeData = (obj) => {
  if (typeof obj == 'boolean') {
    if (obj === true) {
      obj = true;
    } else {
      obj = false;
    }
  } else {
    obj = obj || obj === 0 ? obj : '';
  }

  let prefix = randomString(10);
  let suffix = randomString(10);
  const objs = JSON.stringify(obj);
  // eslint-disable-next-line no-undef
  let raw = Buffer.from(objs, 'utf8').toString('base64');
  let str = raw.split('').reverse().join('');
  let sample = prefix + str + suffix;

  if (enable_base64 && enable_base64 === 'off') {
    return obj;
  }
  return sample;
};

const decodeData = (sample) => {
  let obj = sample;
  if (typeof sample === 'string') {
    try {
      // remove prefix
      sample = sample.substring(10);
      // remove suffix
      sample = sample.slice(0, -10);
      let raw = sample.split('').reverse().join('');
      // eslint-disable-next-line no-undef
      let buffer = Buffer.from(raw, 'base64').toString('utf8');
      obj = JSON.parse(buffer);
    } catch (err) {
      // console.log(err);
    }
  }

  return obj;
};
const enCodeParams = (obj) => {
  if (obj) {
    const data = Object.keys(obj).map((p) => {
      // console.log(p);
      return {
        [p]: encodeData(obj[p])
      };
    });

    const result = data.reduce(function (result, item) {
      var key = Object.keys(item)[0]; //first property: a, b, c
      result[key] = item[key];
      return result;
    }, {});
    if (enable_base64 && enable_base64 === 'off') {
      return obj;
    }
    return result;
  }
};
export { decodeData, encodeData, enCodeParams };
