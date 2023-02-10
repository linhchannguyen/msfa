import getters from '@/store/modules/A02/getters';
import actions from '@/store/modules/A02/actions';
import mutations from '@/store/modules/A02/mutations';

const state = {
  a02_S03_all_facility_category: '',
  a02_S03_all_content: ''
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
