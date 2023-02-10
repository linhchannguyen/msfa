export default {
  isHaveHistory: (state) => {
    // console.log('getter', state);
    return state;
  },
  isHistoryModal: (state) => {
    // console.log('getter', state);
    return state.modal;
  },
  isHistoryParams: (state) => {
    // console.log('getter', state);
    return state.params;
  },
  isHistoryComponentName: (state) => {
    // console.log('getter', state);
    return state.name;
  },
  isHistoryFilter: (state) => {
    // console.log('getter', state);
    return state.filter;
  },
  fromPath: (state) => {
    // console.log('getter', state);
    return state.fromPath;
  },
  toPath: (state) => {
    // console.log('getter', state);
    return state.toPath;
  },
  getListHistory: (state) => {
    return state.listHistory;
  },
  getItemIndex: (state) => {
    return state.itemIndex;
  }
};
