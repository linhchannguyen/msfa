import { A02_Types } from '@/store/modules/A02/mutationTypes';

const mutations = {
  [A02_Types.SET_ALL_FACILITY_CATEGORY](state, payload) {
    state.a02_S03_all_facility_category = payload;
  },
  [A02_Types.SET_ALL_CONTENT](state, payload) {
    state.a02_S03_all_content = payload;
  }
};

export default mutations;
