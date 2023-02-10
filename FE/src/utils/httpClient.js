/* eslint-disable eqeqeq */
import axios from 'axios';
import store from '@/store';
import { getCookie } from './constants';
import { routerPush } from './../router';
import { enCodeParams } from '@/api/base64_api';

const decodeDatas = (sample) => {
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

const header = JSON.parse(localStorage.getItem('header'));
const configConsole = JSON.parse(decodeDatas(localStorage.getItem('$_C')));

const axiosInstance = axios.create({
  // eslint-disable-next-line no-undef
  baseURL: process.env.VUE_APP_API_URL,
  headers: { Accept: 'application/json' }
});

axiosInstance.interceptors.request.use(
  (config) => {
    const main = document.querySelector('#app');
    const log = enCodeParams(decodeDatas(main?.getAttribute('log')));
    const { accessToken, originalToken } = store.state.auth;
    const token = accessToken && `Bearer ${accessToken}`;
    const original_token = originalToken && `Bearer ${originalToken}`;
    if (token) config.headers.Authorization = token;
    if (original_token) config.headers.OriginalToken = original_token;
    config.params = { ...config.params, ...log };
    return config;
  },
  (err) => {
    Promise.reject(err);
  }
);

axiosInstance.interceptors.response.use(
  (response) => {
    let clg = configConsole?.enable_console_log;
    if (!clg || clg === 'off') {
      console.clear();
    }

    return response;
  },
  async (error) => {
    if (error?.response?.data.status == 404) {
      routerPush('/page-not-found');
    }

    const original_token = getCookie('originalToken');
    const access_token = getCookie('accessToken');
    let exclude = ['/auth/login', '/', '/auth/developer-login'];
    let { pathname } = window.location;

    if (!access_token && !original_token && error?.response?.data?.status == 403) {
      if (exclude.some((url) => url !== window.location.pathname)) {
        window.location.url = pathname || '/auth/login';
      }
    }

    /** Cutsom error message */
    let errCustom = {};
    if (error && error.response) {
      errCustom = error;
    } else if (error.request) {
      errCustom = { response: { data: { message: '内部エラーが発生しました。システム管理者に連絡してください。' } } };
    }
    await new Promise((resolve) => setTimeout(resolve, 1000));

    let clg = configConsole?.enable_console_log;
    if (!clg || clg === 'off') {
      console.clear();
    }

    return Promise.reject(errCustom);
  }
);
axiosInstance.prototype.headers = header;
export default axiosInstance;
