const filte_popup = {
  data() {
    return {
      isShowPopupNoti: false
    };
  },
  mounted() {
    document.addEventListener('click', this.clickOutSidePopup);
  },
  methods: {
    clickOutSidePopup(e) {
      const elPopup = document.querySelector('.header-notif');
      // el-input__clear: Handling exception event clearable el-input
      if (!elPopup || elPopup.contains(e.target) || e.target.classList.contains('el-input__clear')) {
        return;
      } else {
        // begin::check child popup is opening
        const elDropdown = document.querySelector('.dropdown-notif');
        elDropdown?.classList.remove('show');
        this.isShowPopupNoti = false;
      }
    }
  }
};

export default filte_popup;
