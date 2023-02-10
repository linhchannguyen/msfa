<template>
  <div v-if="isAuthorized" id="log-wrapper" v-log="true" class="wrapper">
    <Aside @changeMode="slideFunc"></Aside>
    <section v-setup-modal="true" class="main">
      <Header :type_screen="slide.type_screen" @changeMode="slideFunc" @typeScreen="typeScreen"></Header>
      <router-view v-slot="{ Component }" :check-loading="checkLoading">
        <transition :name="slide.name">
          <component :is="Component" ref="app" :class="['screen_app', slide.type_screen]" :props="slide.name" @changeMode="slideFunc" />
        </transition>
      </router-view>
    </section>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Aside from '@/layouts/aside/Aside';
import Header from '@/layouts/header/Header';
import _ from 'lodash';

export default {
  name: 'BaseLayout',
  components: {
    Aside,
    Header
  },
  props: {},
  data() {
    return {
      checkLoading: true,
      slide: {
        name: 'slide-right',
        type: 'Next',
        type_screen: 'parent'
      },
      routes: []
    };
  },
  computed: {
    ...mapGetters({
      isAuthorized: 'auth/isAuthorized',
      policyRole: 'auth/policyRole',
      policyPermission: 'auth/policyPermission',
      fromPath: 'router/fromPath',
      toPath: 'router/toPath'
    })
  },
  watch: {
    $route(e) {
      // event click cancel from component child
      let { toPath, fromPath } = this;
      const childRouter = JSON.parse(localStorage.getItem('router'));
      let fromPathChild = childRouter?.find((x) => x.name === 'F01_S03_KnowledgeInput')?.path || '';
      let pathchild = childRouter?.find((x) => x.name === 'F01_S05_Pre_ReleaseKnowledgeManagement')?.path || '';
      let fromPathChildS2 = childRouter?.find((x) => x.name === 'F01_S02_KnowledgeDetails')?.path || '';
      let pathchildS1 = childRouter?.find((x) => x.name === 'F01_S01_KnowledgeSearch')?.path || '';
      if (
        (e && e.params && e.params.back === 'true') ||
        (e &&
          e.params &&
          Object.keys(e.params).length === 0 &&
          e.name === 'F01_S05_Pre_ReleaseKnowledgeManagement' &&
          fromPath === fromPathChild &&
          toPath === pathchild) ||
        (e && e.params && Object.keys(e.params).length === 0 && e.name === 'F01_S01_KnowledgeSearch' && fromPath === fromPathChildS2 && toPath === pathchildS1)
      ) {
        this.slide.name = 'slide-left';
        this.slide.type = 'back';
        this.slide.type_screen = 'parent_children';
        if (e && e.params && e.params.back === 'true') {
          localStorage.removeItem('$_D');
        }
      }
      if (e && e.params && e.params.back === 'false') {
        this.slide.name = '';
        this.slide.type = 'NO';
        this.setQueryToParam(e);
      }
      // click back
      if (this.slide.type === 'back') {
        this.slide.type = 'Next';
        this.checkLoading = false;
      } else {
        if (this.slide.type === 'NO') {
          this.checkLoading = true;
          this.slide.type = 'Next';
        } else {
          this.checkLoading = true;
          this.slide.name = 'slide-right';
          this.slide.type_screen = 'children';
          this.setQueryToParam(e);
        }
      }
      // if (e.name !== 'H02_S01_PersonalSearch' && e.name !== 'H02_PersonalDetails' && e.name !== 'H01_FacilityDetails') {
      // localStorage.removeItem('DataH02');
      // localStorage.removeItem('DataResH02');
      // }
      if (
        e.name !== 'H01_S01_FacilitySearch' &&
        e.name !== 'H01_FacilityDetails' &&
        e.name !== 'H02_PersonalDetails' &&
        e.name !== 'Z04_S01_AccountSettings' &&
        e.name !== 'C01_S02_LectureInput' &&
        e.name !== 'C01_S03_AttendantManagement' &&
        e.name !== 'B01_S02_BriefingSessionInput' &&
        e.name !== 'D01_S02_MaterialDetails' &&
        e.name !== 'A01_S04_InterviewDetailedInput'
      ) {
        localStorage.removeItem('DataH01');
        localStorage.removeItem('DataResH01');
      }
      if (e.name !== 'A02_S03_StockManagement' && e.name !== 'C01_S02_LectureInput' && e.name !== 'B01_S02_BriefingSessionInput') {
        localStorage.removeItem('dataA02S03');
        localStorage.removeItem('dataA02S03Filter');
        localStorage.removeItem('checkRecordA02S03');
      }

      if (!(e.name === 'H01_FacilityDetails' || (fromPath.includes('facility-details') && toPath.includes('person-details')))) {
        localStorage.removeItem('DataH01_S03');
        localStorage.removeItem('DataResH01_S03');
      }
    }
  },
  mounted() {
    let currentUserLocalStorage = this.getCurrentUser();
    if (!this.policyRole || !this.policyPermission) {
      this.$store.dispatch('auth/setPolicyAction', currentUserLocalStorage.user_cd);
    }
    this.$nextTick(function () {
      this.js_pscroll();
    });
    // if (this.$route.name !== 'H02_S01_PersonalSearch' && this.$route.name !== 'H02_PersonalDetails') localStorage.removeItem('DataH02');
    // if (this.$route.name !== 'H01_S01_FacilitySearch' && this.$route.name !== 'H01_FacilityDetails') localStorage.removeItem('DataH01');
  },
  updated: function () {
    this.$nextTick(function () {
      this.js_pscroll();
    });
  },
  methods: {
    slideFunc(e) {
      this.slide = e;
    },
    typeScreen(e) {
      this.slide.type_screen = e;
    },
    enCodeData(obj) {
      // eslint-disable-next-line no-undef
      let strData = Buffer.from(JSON.stringify(obj), 'utf8').toString('base64');
      return strData;
    },
    setQueryToParam(to) {
      const routes = this.decodeData(localStorage.getItem('$_D')) || [];
      if (routes) {
        const index = routes && routes.findIndex((s) => s.name === to.name);
        const route = { name: to.name, params: to.params, path: to.path };
        if (index === -1) {
          routes.push(route);
          localStorage.setItem('$_D', this.enCodeData(routes));
        } else {
          const routeLocal = routes.find((s) => s.name === to['name']);
          if (_.isEqual(route, routeLocal)) {
            routes[index] = routeLocal;
            localStorage.setItem('$_D', this.enCodeData(routes));
          } else {
            routes[index] = route;
            localStorage.setItem('$_D', this.enCodeData(routes));
          }
        }
      }
    },
    decodeData(sample) {
      let obj = sample;
      if (typeof sample === 'string') {
        try {
          // eslint-disable-next-line no-undef
          let buffer = Buffer.from(sample, 'base64').toString('utf8');
          obj = JSON.parse(buffer);
        } catch (err) {
          // console.log(err);
        }
      }
      return obj;
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet_mini: 882px;
.screen_app {
  // overflow: overlay;
  .groupContent {
    min-width: 700px;
  }
}
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.75s ease-out;
  @media (max-width: $viewport_tablet_mini) {
    width: 90%;
  }
  @media (max-width: $viewport_desktop) {
    width: 95%;
  }
}

.slide-right-enter-to {
  position: absolute;
  right: 0;
}

.slide-right-enter-from {
  position: absolute;
  right: -100%;
}

.slide-right-leave-to {
  position: absolute;
  left: -100%;
}

.slide-right-leave-from {
  position: absolute;
  left: 0;
}
// left
.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.75s ease-out;

  @media (max-width: $viewport_tablet_mini) {
    width: 90%;
  }
  @media (max-width: $viewport_desktop) {
    width: 95%;
  }
}

.slide-left-enter-to {
  position: absolute;
  left: 0;
}

.slide-left-enter-from {
  position: absolute;
  left: -100%;
}

.slide-left-leave-to {
  position: absolute;
  right: -100%;
}

.slide-left-leave-from {
  position: absolute;
  right: 0;
}
</style>
