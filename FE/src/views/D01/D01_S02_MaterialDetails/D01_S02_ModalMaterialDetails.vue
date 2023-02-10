<template>
  <div :class="`modal-material-detail ${isLoadingPage ? 'pre-loader' : ''}`">
    <Preloader v-if="isLoadingPage" />

    <div id="popup" :class="`popupDetail ${isLoadingPage ? 'small-if' : ''}`">
      <iframe id="popupiframe"> </iframe>
      <div id="popupdarkbg"></div>
    </div>
  </div>
</template>
<script>
/* eslint-disable eqeqeq */

export default {
  name: 'D01_S02_ModalMaterialDetails',
  components: {},
  props: {
    documentId: {
      type: String,
      required: true
    }
  },
  // emits: ['onFinishScreen'],
  data: function () {
    return {
      isLoadingPage: false
    };
  },
  mounted() {
    // let document_id = item.document_id;
    this.isLoadingPage = true;
    let path = this.$router.getRoutes().filter((s) => s.name === 'D01_S02_MaterialDetails')[0].path;
    const elmt = document.getElementById('popup');
    const iframe = document.getElementById('popupiframe');
    let src = `${window.location.origin}${path}?document_id=${this.documentId}`;

    let time;

    document.getElementById('popupdarkbg').style.display = 'block';
    elmt.setAttribute('style', ' display: block; visibility: visible;opacity: 1; ');

    iframe.src = src; // Add url
    iframe.style.opacity = 0; // Transition

    // Interval debounce check element
    const timeOut = setInterval(() => {
      if (time) return clearInterval(timeOut);

      let body = iframe?.contentWindow?.document.body;
      let aside = iframe?.contentWindow?.document.querySelector('#aside');
      let asidebody = iframe?.contentWindow?.document.querySelector('.asideBar');
      let header = iframe?.contentWindow?.document.querySelector('.header');
      let main = iframe?.contentWindow?.document.querySelector('.main');
      let screen_app = iframe?.contentWindow?.document.querySelector('.box-materia.screen_app');
      if (aside) {
        aside.style.display = 'none';
        asidebody.style.display = 'none';
        header.style.display = 'none';
        main.style.padding = 0;
        screen_app.style.paddingTop = 0;
        if (aside && asidebody && header && body) {
          iframe.style.transition = 'all .2s';
          iframe.style.opacity = 1;
          time = true;

          this.isLoadingPage = false;
        }
      }
    }, 250);

    timeOut;

    return;
  },
  methods: {
    closeModal() {
      document.getElementById('popupdarkbg').onclick = function () {
        document.getElementById('popup').style.display = 'none';
        document.getElementById('popupdarkbg').style.display = 'none';
      };
    }
  }
};
</script>

<style lang="scss" scoped>
#popupiframe {
  height: 100%;
  max-height: unset;
  width: 100%;
}

.small-if {
  width: 1px;
}
</style>
