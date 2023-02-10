<template>
  <!-- name: 色設定 -->
  <div class="modal-colorSetting modal-body-A01S02-SettingColor">
    <div id="msfa-notify-A01S02-SettingColor"></div>
    <div class="headSetting">
      <h2 class="headSetting-title">
        <span class="headSetting-item"><img src="@/assets/template/images/icon_user03.svg" alt="" /></span>{{ user.user_name }}
      </h2>
      <el-button
        type="primary"
        class="btn btn-outline-primary btn-outline-primary--cancel btn-radius btn-hg32"
        :disabled="processingDelete"
        @click.prevent="onDeleteUser()"
      >
        <div :class="['btnDelete ', processingDelete ? 'icon-loading' : '']">
          <span v-if="!processingDelete" class="btn-iconLeft">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" />
          </span>
          <i v-if="processingDelete" class="el-icon-loading inline-block"></i>
          リストから削除
        </div>
      </el-button>
    </div>
    <div class="lst-colorSetting">
      <ul>
        <li v-for="color in lstColors" :key="color">
          <span
            class="itemBox log-icon"
            title_log="色"
            :class="color.display_color_cd === user.display_color_cd ? 'border-select' : ''"
            :style="{ background: color.display_color }"
            @click="onResultData(color)"
            @touchstart="onResultData(color)"
          >
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
import A01_S02_CalendarService from '@/api/A01/A01_S02_CalendarService';
export default {
  name: 'A01_S02_SettingColor',
  props: {
    user: {
      type: Object,
      required: true,
      default() {
        return {};
      }
    },
    lstColors: {
      type: Array,
      required: true,
      default() {
        return [];
      }
    }
  },
  emits: ['onFinishScreen', 'onDeleteUser'],
  data() {
    return {
      processingDelete: false
    };
  },
  methods: {
    // return data to screen parent

    onResultData(color) {
      let result = {
        screen: 'A01_S02_ColorSetting',
        color: color,
        user: this.user
      };
      this.$emit('onFinishScreen', result);
    },

    onDeleteUser() {
      this.processingDelete = true;
      A01_S02_CalendarService.deleteUser(this.user.watch_user_cd)
        .then((res) => {
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.$emit('onDeleteUser');
        })
        .catch((err) => {
          this.processingDelete = false;
          this.notifyModal({
            message: err.response.data.message,
            type: 'error',
            classParent: 'modal-body-A01S02-SettingColor',
            idNodeNotify: 'msfa-notify-A01S02-SettingColor'
          });
        })
        .finally(() => {});
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.modal-colorSetting {
  padding: 0;
  margin: -32px;
  .headSetting {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 32px;
    background: #fff;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px 10px 0 0;
    .headSetting-title {
      display: flex;
      align-items: center;
      font-size: 1.5rem;
      font-weight: 700;
      .headSetting-item {
        min-width: 20px;
        margin: -3px 8px 0 0;
        img {
          width: 20px;
        }
      }
    }
  }
  .lst-colorSetting {
    padding: 32px;
    ul {
      display: flex;
      flex-wrap: wrap;
      margin-left: -32px;
      margin-top: -20px;
      li {
        padding-left: 32px;
        width: 20%;
        margin-top: 20px;
        .itemBox {
          width: 42px;
          height: 42px;
          border-radius: 50%;
          border: 1px solid #fff;
          box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
          display: block;
          cursor: pointer;
          &:hover {
            border: 2px solid #448add !important;
          }
        }
      }
    }
  }
}

.border-select {
  border: 2px solid #448add !important;
}

.btnDelete {
  display: flex;
  align-items: center;
  font-size: 0.9375rem;
  font-weight: 700;
  width: 125px;
}
.icon-loading {
  .inline-block {
    color: #448add;
  }
  &:hover {
    .inline-block {
      color: #fff;
    }
  }
}
</style>
