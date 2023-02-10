<template>
  <div id="aside">
    <aside
      :class="`asideBar ${isChangeModalHeader ? 'header-custom-modal' : ''} ${isChangeClass ? 'header-custom' : ''} ${showAside ? 'asideBar--change' : ''}`"
    >
      <div class="itemNav">
        <button class="btn btn-show" @click="handleShowAside">
          <ImageSVG :src-image="'icon_menu.svg'" :alt-image="'icon_menu'" />
        </button>
        <button class="btn btn-close" @click="handleShowAside">
          <ImageSVG :src-image="'icon_arrow-left.svg'" :alt-image="'icon_arrow-left'" />
        </button>
      </div>
      <div :class="`gNav ${showAside ? '' : 'gNav--hover'}`">
        <ul class="sideLists">
          <li v-for="menu in menus" :id="menu.id" :key="menu.id" class="item-navSub" @click="handleActiveMenu($event, menu)">
            <a
              v-if="(!showAside && menu?.url) || !menu?.url"
              :class="{ active: selectMenu.id === menu.id, removeIconTriangle: menu.subMenu.length === 1 }"
              @click="setActiveMenu($event, menu, null, true)"
            >
              <span class="item" :title_log="menu.menu_name">
                <ImageSVG :src-image="menu.icon" :alt-image="`icon${menu.id}`" />
              </span>
              <span class="text">{{ menu.menu_name }}</span>
            </a>

            <router-link
              v-if="menu?.url && showAside"
              :class="{ active: selectMenu.id === menu.id, removeIconTriangle: menu.subMenu.length === 1 }"
              :to="menu.url"
              @click="setActiveMenu($event, menu)"
            >
              <span class="item" :title_log="menu.menu_name">
                <ImageSVG :src-image="menu.icon" :alt-image="`icon${menu.id}`" />
              </span>
              <span class="text">{{ menu.menu_name }}</span>
            </router-link>

            <!-- submenu -->
            <div v-if="menu.subMenu.length > 0" class="nav-sub" :class="{ subMenu: menu?.url }">
              <router-link v-if="menu?.url" class="subMenu subMenuTitle nav-sub-title" :to="menu.url" @click="setActiveMenu($event, menu)">
                {{ menu.screen_name }}
              </router-link>
              <h2 v-else class="nav-sub-title">
                {{ menu.menu_name }}
              </h2>

              <ul v-if="menu.subMenu.length > 1">
                <li v-for="sbmenu in menu.subMenu" :key="sbmenu.id">
                  <router-link :to="sbmenu.url" :class="{ active: sbmenu.id === selectSubMenu.id }" @click="setActiveMenu($event, menu, sbmenu)">
                    {{ sbmenu.screen_name }}
                  </router-link>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </aside>
  </div>

  <el-dialog
    v-model="modalConfig.isShowModal"
    :custom-class="modalConfig.customClass"
    :title="modalConfig.title"
    :width="modalConfig.width"
    :destroy-on-close="modalConfig.destroyOnClose"
    :close-on-click-modal="modalConfig.closeOnClickMark"
    :show-close="modalConfig.isShowClose"
  >
    <template #default>
      <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onResultModal" @handleYes="handleYes"></component>
    </template>
  </el-dialog>
</template>

<script>
import { Device } from '@/utils/common-function.js';

export default {
  name: 'Aside',
  emits: ['changeMode'],
  data() {
    return {
      menus: [],
      isChangeModalHeader: false,
      isChangeClass: false,
      showAside: false,
      selectMenu: {
        id: '',
        name: '',
        title: '',
        icon: '',
        path: '',
        subMenu: []
      },
      selectSubMenu: {
        id: '',
        name: '',
        subTitle: '',
        subPath: ''
      },
      isChangeData: false,
      modalConfig: {
        title: '',
        customClass: 'custom-dialog',
        isShowModal: false,
        isShowClose: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },
  computed: {
    checkRole() {
      return this.$store.state.auth.policyPermission?.menu?.permissions;
    }
  },
  watch: {
    '$store.state.auth.menus': function () {
      this.setMenu();
    },
    $route() {
      this.checkActiveMenu();
    }
  },

  mounted() {
    this.setMenu();
    this.emitter.on('change-class-header', ({ changeClass }) => {
      this.isChangeClass = changeClass;
    });
    this.emitter.on('change-class-header-modal', ({ changeClass }) => {
      this.isChangeModalHeader = changeClass;
    });

    document.addEventListener('click', this.onClickOutside);
  },

  created() {
    this.$watch(
      () => localStorage.getItem('isChangeData'),
      () => {
        if (localStorage.getItem('isChangeData') === 'true') {
          this.isChangeData = true;
        } else {
          this.isChangeData = false;
        }
      }
    );
  },
  methods: {
    setMenu() {
      this.menus = this.$store.state.auth.menus;
      this.checkActiveMenu();
    },

    checkActiveMenu() {
      const path = this.$route.path;
      this.selectMenu = {};
      this.selectSubMenu = {};
      this.menus.forEach((el) => {
        if (el.url === path) {
          this.selectMenu = el;
        } else if (el.subMenu.length > 0) {
          el.subMenu.forEach((elm) => {
            if (elm.url === path) {
              this.selectMenu = el;
              this.selectSubMenu = elm;
            }
          });
        }
      });
    },

    handleYes(event, menu, subMenu) {
      this.setActiveMenu(event, menu, subMenu);
    },

    setActiveMenu(event, menu, subMenu, isShowSub) {
      // eslint-disable-next-line no-inner-declarations
      function handler(el, type) {
        if (!el) return;

        if (type === 1) {
          el.classList.remove('hoverless');
          el.classList.remove('hoverlessa');
        } else if (type === 2) {
          el.classList.remove('bgless');
        }
        el.removeEventListener('mouseout', handler);
      }

      if (!isShowSub && !this.showAside && (event.pointerType === 'touch' || this.getDevice() !== 'Desktop')) {
        let elmt = event?.composedPath() || event?.composedPath;

        let navSub, activeLink;

        if (menu) {
          // handle normal
          let { subMenu: menuChild } = menu;

          if (menuChild.length > 1) {
            navSub = elmt.find((item) => item?.classList?.contains('nav-sub') || item?.classList?.value?.includes('nav-sub'));

            let itemNavSub = elmt.find((item) => item?.classList?.contains('item-navSub'));

            activeLink = itemNavSub.children[0]; //
          } else {
            activeLink = elmt.find((item) => (item.nodeName === 'A' || item.localName === 'a') && item.href);

            let navLinkElmt = elmt.find((item) => item?.classList?.contains('item-navSub') || item?.classList?.value?.includes('item-navSub'));

            navSub = navLinkElmt.querySelector('.nav-sub');
          }
        }

        if (navSub) {
          navSub.classList.add('hoverless');

          navSub.addEventListener('mouseout', () => handler.call(this, navSub, 1));
        }
        if (activeLink) {
          setTimeout(() => {
            activeLink.classList.add('bgless');
          }, 1000);

          activeLink.addEventListener('mouseout', () => handler.call(this, activeLink, 2));
        }
      }

      let activeParentOld = localStorage.getItem('activeParent');

      if (subMenu) {
        localStorage.setItem('activeMenu', subMenu.component);
        localStorage.setItem('activeParent', subMenu.component);

        if (subMenu.component !== activeParentOld) {
          setTimeout(() => {
            localStorage.removeItem('checkChangTab');
          }, 50);
          localStorage.setItem('activeParentOld', activeParentOld);
          localStorage.setItem('isLoadingComponent', activeParentOld === subMenu.component ? false : true);
          this.$emit('changeMode', { name: '', type: 'NO', type_screen: 'parent' });
        }
      } else {
        localStorage.setItem('activeMenu', menu.component);
        localStorage.setItem('activeParent', menu.component);
        if (menu.component !== activeParentOld) {
          setTimeout(() => {
            localStorage.removeItem('checkChangTab');
          }, 50);
          localStorage.setItem('activeParentOld', activeParentOld);
          localStorage.setItem('isLoadingComponent', activeParentOld === menu.component ? false : true);
          this.$emit('changeMode', { name: '', type: 'NO', type_screen: 'parent' });
        }
      }

      localStorage.removeItem('screen');
      localStorage.removeItem('$_D');
      localStorage.removeItem('startActive');
      localStorage.removeItem('time_line_knowledge');
      localStorage.removeItem('ScrollTopScreen');
      localStorage.removeItem('CurrentPageScreen');
      localStorage.removeItem('checkChangTab');
      localStorage.removeItem('DataH02');
      localStorage.removeItem('DataResH02');
      localStorage.removeItem('mdView');
      localStorage.removeItem('showFilterA1S2');
      if (!menu.subMenu?.length) this.selectMenu = menu;
      if (subMenu) {
        this.selectMenu = menu;
        this.selectSubMenu = subMenu;
      }
    },

    confirmChangeMenu(event, menu, subMenu) {
      if (localStorage.getItem('isChangeData') === 'true') {
        this.modalConfig = {
          ...this.modalConfig,
          isShowModal: true,
          component: this.markRaw(this.$PopupConfirm),
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          props: {
            mode: 1,
            textCancel: 'いいえ',
            params: {
              event: event,
              menu: menu,
              subMenu: subMenu
            }
          }
        };
      } else {
        this.setActiveMenu(event, menu, subMenu);
      }
    },

    handleShowAside() {
      if (this.showAside) {
        // close menu action
        const elParents = document.querySelectorAll('.item-navSub') || [];
        elParents.forEach((elParent) => {
          elParent.classList.remove('active-li');
          const elChild = elParent.querySelector('.nav-sub');
          const elChildHref = elParent.querySelector('a');
          elChildHref?.classList.remove('menu-active', 'active-navSub');
          elChild?.classList.remove('active');
          elChild?.style.removeProperty('display');
        });
      } else {
        // open menu action
        const elSubMenuActive = document.querySelector('.active');
        elSubMenuActive?.classList.add('menu-active', 'active-navSub');
        elSubMenuActive?.parentElement?.classList.add('active-li');
        const elMenuActive = document.querySelectorAll('.active-li') || [];
        elMenuActive.forEach((el) => {
          const elChild = el.querySelector('.nav-sub');
          if (elChild) elChild.style.display = 'block';
        });
      }
      this.showAside = !this.showAside;
    },

    getDevice() {
      return Device();
    },
    onClickOutside(e) {
      const elPopup = document.querySelector('#aside');
      if (!elPopup || elPopup.contains(e.target)) return;
      else {
        if (this.showAside) this.handleShowAside();
      }
    },

    handleActiveMenu(event, menu) {
      this.removeScrollScreen();
      let target = event?.composedPath() || event?.composedPath;

      let item = target[0];
      let isSubmenu = item?.classList.contains('router-link-active');

      if (this.showAside && !isSubmenu) {
        let liParent = document.getElementById(menu.id);
        const elChil = liParent.querySelector('a');
        const elChilNavSub = liParent.querySelector('.nav-sub');
        const elMenuActiveLi = document.querySelectorAll('.active-li') || [];

        if (liParent.classList.contains('active-li')) {
          liParent.classList.remove('active-li');
          elChil?.classList.remove('menu-active', 'active-navSub');
          elChilNavSub?.classList.remove('active');
          elChilNavSub?.style.removeProperty('display');
        } else {
          elMenuActiveLi.forEach((item) => {
            const elChilItem = item.querySelector('a');
            const elChilNavSubItem = item.querySelector('.nav-sub');
            item.classList.remove('active-li');
            elChilItem?.classList.remove('menu-active', 'active-navSub');
            elChilNavSubItem?.style.removeProperty('display');
          });
          liParent.classList.add('active-li');
          elChil.classList.add('menu-active', 'active-navSub');
          elChilNavSub.style.display = 'block';
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
a.removeIconTriangle::before {
  content: unset !important;
}
.header-custom {
  z-index: -99;
}
.header-custom-modal {
  z-index: 5;
}
.text {
  color: #7d97ae;
}
.item-navSub {
  &:hover {
    .text {
      color: #ffffff;
    }
    svg {
      cursor: pointer;
    }
  }
}
.subMenu {
  display: inline-grid;
}

.gNav .nav-sub .subMenuTitle {
  padding: 10px 10px 10px 28px !important;
  width: 230px;
  border-radius: 0px 5px 5px 0px;
  &:hover {
    background: #171b23;
    text-decoration: none;
  }
}

.point_not_active {
  pointer-events: none;
}
</style>
