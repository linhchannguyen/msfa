import { A02_Types } from '@/store/modules/A02/mutationTypes';
import A02_S03_StockManagementServices from '@/api/A02/A02_S03_StockManagementServices';

const getAllFacilityCategoryAction = async (context) => {
  try {
    const res = await A02_S03_StockManagementServices.getAllFacilityCategoryService();
    context.commit(A02_Types.SET_ALL_FACILITY_CATEGORY, res.data.data);
    return res;
  } catch (err) {
    throw err;
  }
};

const getAllContentAction = async (context) => {
  try {
    const res = await A02_S03_StockManagementServices.getAllContentService();
    context.commit(A02_Types.SET_ALL_CONTENT, res.data.data);
    return res;
  } catch (err) {
    throw err;
  }
};
export default { getAllFacilityCategoryAction, getAllContentAction };
