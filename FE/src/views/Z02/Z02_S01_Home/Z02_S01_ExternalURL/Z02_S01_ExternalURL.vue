<script>
import { Auth } from '@/api';
export default {
  name: 'Z02_S01_ExternalURL',
  props: {
    data: [Object || Array]
  },
  data() {
    return {
      loader: true,
      externalUrl: [],
      currentUser: '',
      roleR10: false
    };
  },
  watch: {
    data(e) {
      this.externalUrl = e.external_url_link;
      if (e?.external_url_link) {
        setTimeout(() => {
          if (this.$refs.externalUrl) {
            this.$refs.externalUrl.scrollTop = 1;
          }
          this.loader = false;
        }, 100);
      }
    }
  },
  mounted() {
    this.checkPermission();

    this.js_pscroll();
  },
  methods: {
    async checkPermission() {
      const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
      this.currentUser = currentUserProxy ? currentUserProxy?.user_cd : JSON.parse(localStorage.getItem('currentUser'))?.user_cd;
      const permission = await Auth.getPolicyRoleService({ user_cd: this.currentUser });
      if (permission?.data?.data?.includes('R10')) {
        this.roleR10 = true;
      } else {
        this.roleR10 = false;
      }
    },
    setScroll({ target: { scrollTop, clientHeight, scrollHeight } }) {
      if (this.$refs.externalUrl && this.$refs.externalUrl.scrollTop < 10) {
        this.$refs.externalUrl.scrollTop = 1;
      }
      if (this.$refs.externalUrl && scrollTop === scrollHeight - clientHeight) {
        this.$refs.externalUrl.scrollTop = scrollTop - 1;
      }
    }
  }
};
</script>
<template>
  <div ref="externalUrl" class="scrollbar" :class="roleR10 ? 'externalLink' : 'externalLink1 externalLink'" @scroll="setScroll">
    <ul v-if="externalUrl.length > 0">
      <li v-for="item of externalUrl" :key="item.external_url_link_id">
        <a :href="item.external_url" target="_blank">{{ item.external_url_link_name }}</a>
      </li>
    </ul>
    <EmptyData v-else-if="!loader" :class="roleR10 ? 'w-192' : ''" custom-class="custom-class-no-data w-100 z01-s02-ex" />
  </div>
</template>
<style lang="scss" scoped>
@import '../../../../assets/css/_animations.scss';
$viewport_tablet: 991px;
$viewport_mobile: 767px;
.w-100 {
  height: 12.5rem;
}
.w-192 {
  height: 19.5rem !important;
}
.externalLink1 {
  height: 316px !important;
  &::-webkit-scrollbar {
    width: 0px !important;
  }
}
.externalLink {
  overflow: auto !important;
  height: 202px;
  &::-webkit-scrollbar {
    display: none;
    width: 8px;
    background-color: #f2f2f2;
    border-radius: 2.5px;
  }
  &::-webkit-scrollbar-thumb {
    background-color: #b7c3cb;
    border-radius: 5px;
  }
  ul {
    li {
      padding: 10px 24px 10px 24px;
      border-bottom: 1px solid #e3e3e3;
      display: flex;
      justify-content: space-between;
      align-items: center;
      a {
        font-size: 16px;
      }
      .btn {
        margin-left: 10px;
      }
    }
  }
}
.custom-tbreport {
  padding: 6px 4px 12px 0px !important;
  .title {
    padding: 8px 22px 12px;
  }
}
</style>
