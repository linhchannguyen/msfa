import { ModalTypes } from '@/store/modules/modal/mutationTypes';

const mutations = {
  [ModalTypes.SET_MODAL_STATUS](state, payload) {
    state.status = payload;
  },
  [ModalTypes.SET_MODAL_DATA](state, payload) {
    state.data = payload;
  }
};

export default mutations;
