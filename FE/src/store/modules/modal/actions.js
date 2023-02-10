import { ModalTypes } from '@/store/modules/modal/mutationTypes';

const setModal = async ({ commit }, data) => {
  commit(ModalTypes.SET_MODAL_STATUS, data.status);
  commit(ModalTypes.SET_MODAL_DATA, data.data);
};

export default { setModal };
