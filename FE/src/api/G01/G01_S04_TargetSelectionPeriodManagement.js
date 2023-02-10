import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getList: '/api/target-selection-period-management',
  postRecord: '/api/target-selection-period-management/save'
};
const G01_S04_TargetSelectionPeriodManagement = {
  getList() {
    return http.get(API_LIST.getList);
  },
  postRecord(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.postRecord, params);
  }
};

export default G01_S04_TargetSelectionPeriodManagement;
