import getters from '@/store/modules/modal/getters';
import actions from '@/store/modules/modal/actions';
import mutations from '@/store/modules/modal/mutations';

const state = {
  status: false,
  data: null
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
