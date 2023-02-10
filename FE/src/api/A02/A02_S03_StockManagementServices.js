import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getAllFacilityCategoryAPI: '/api/stock/facility-category',
  getAllContentAPI: '/api/stock/content',
  editStockAPI: '/api/stock/edit',
  searchStockAPI: '/api/stock',
  deleteStockAPI: '/api/stock',
  AddStockAPI: '/api/stock/create'
};

const A02_S03_StockManagementServices = {
  getAllFacilityCategoryService() {
    return http.get(API_LIST.getAllFacilityCategoryAPI);
  },
  getAllContentService() {
    return http.get(API_LIST.getAllContentAPI);
  },
  editStockService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.editStockAPI, params);
  },
  searchStockService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.searchStockAPI, params);
  },
  deleteStockService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteStockAPI, { params });
  },
  AddStockService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.AddStockAPI, params);
  }
};

export default A02_S03_StockManagementServices;
