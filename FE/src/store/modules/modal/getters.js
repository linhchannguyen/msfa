export default {
  isModalOpen: (state) => {
    return state.status;
  },
  modalData: (state) => {
    return state.data;
  }
};
