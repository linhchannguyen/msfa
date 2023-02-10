const filte_popup = {
  data() {
    return {
      isShowPopupFilter: false
    };
  },
  mounted() {
    document.addEventListener('click', this.clickOutSidePopup);
  },
  methods: {
    clickOutSidePopup(e) {
      const elPopup = document.querySelector('.filter-wrapper');

      // el-input__clear: Handling exception event clearable el-input
      if (!elPopup || elPopup.contains(e.target) || e.target.classList.contains('el-input__clear') || window.getSelection().type === 'Range') return;
      else {
        // begin::check child popup is opening
        const overlayEls = document.querySelectorAll('.el-overlay');
        let isChild = false;
        overlayEls.forEach((el) => {
          if (!el.style.display || el.style.display !== 'none') isChild = true;
        });
        if (isChild) return;
        // end::check
        const elDropdown = document.querySelector('.dropdown-filter');
        elDropdown?.classList.remove('show');
        this.isShowPopupFilter = false;
      }
    }
  }
};

export default filte_popup;
