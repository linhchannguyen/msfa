import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getColorUser: '/api/watch-user-color',
  getScreenData: '/api/watch-user-color/get-screen-data-calendar',
  getListUserByUserLogin: '/api/watch-user-color/get-list-by-user-login',
  getScheduleByDate: '/api/watch-user-color/get-list-by-user-and-type',
  changeColorUser: '/api/watch-user-color/change-color',
  deleteUser: '/api/watch-user-color/delete'
};
const A01_S02_CalendarService = {
  getScreenData() {
    return http.get(API_LIST.getScreenData);
  },

  getScheduleByDate(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getScheduleByDate, { params });
  },

  getColorUser() {
    return http.get(API_LIST.getColorUser);
  },

  changeColorUser(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.changeColorUser, params);
  },

  getListUserByUserLogin() {
    return http.get(API_LIST.getListUserByUserLogin);
  },

  deleteUser(watchUserCd) {
    watchUserCd = encodeData(watchUserCd);
    return http.delete(API_LIST.deleteUser + `/${watchUserCd}`);
  }
};

export default A01_S02_CalendarService;
