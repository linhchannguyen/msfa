import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getUnapprovedList: '/api/unapproved-list'
};

const A03_S03_NotApprovedListServices = {
  getUnapprovedListService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getUnapprovedList, { params });
  }
};

export default A03_S03_NotApprovedListServices;
