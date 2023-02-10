<template>
  <div id="accSetting" ref="mainRef" v-loading="isLoading" class="wrapContent page-accSetting">
    <div class="accSetting">
      <div class="row-container">
        <div class="col-container-3">
          <div class="groupProfile">
            <div class="userProfile">
              <div class="userProfile__thumb">
                <div class="imgThumb">
                  <img :src="user_content.avatar_image_data" alt="img-avatar" />
                </div>
                <span v-if="!guest" class="itemPen" @click="handleChooseImage" @touchstart="handleChooseImage">
                  <ImageSVG :src-image="'icon_pen.svg'" :alt-image="'icon_pen'" />
                  <input
                    ref="uploadImage"
                    style="display: none"
                    accept="image/png, image/jpeg, image/jpe, image/jpg"
                    type="file"
                    @change="previewUploadImage($event)"
                  />
                </span>
              </div>
              <div class="userProfile__content">
                <p class="name">{{ user_content.user_name }}</p>
                <p class="text">
                  <span class="spanLabel">活用度ポイント</span>
                  <span class="text-span">{{ user_content.total_points + ' P' }}</span>
                </p>
                <p class="mail">
                  <span class="icon"><img :src="require('@/assets/template/images/icon_mail02.svg')" alt="img-avatar" /></span>
                  <span class="text-mail">{{ user_content.mail_address }}</span>
                </p>
              </div>
            </div>
            <div class="organization">
              <p class="title">所属組織</p>
              <ul class="scrollbar">
                <li v-for="(item, index) in user_content.definition" :key="index">
                  <p class="item-top">
                    <span class="item">
                      <ImageSVG :src-image="'icon_namecard.svg'" :alt-image="'icon_namecard'" />
                    </span>
                    <span class="item-title">{{ item.definition }}</span>
                  </p>
                  <p>
                    <span class="item item-icon">
                      <ImageSVG :src-image="'icon_filled.svg'" :alt-image="'icon_filled'" />
                    </span>
                    <span class="item-content">{{ item.org_name || '(所属なし)' }}</span>
                  </p>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-container-9" @touchstart="childFocusOut">
          <div class="title-tab">
            <span>プロフィール情報</span>
            <button
              v-if="!guest"
              type="button"
              :class="isEdit ? 'btn btn-outline-primary btn-outline-primary--cancel activeBtn' : 'btn btn-outline-primary btn-outline-primary--cancel'"
              @click="editField"
              @touchstart="editField"
            >
              <img src="@/assets/template/images/icon_pen01.svg" alt="'icon_pen'" />
              編集
            </button>
          </div>
          <div class="profileForm">
            <div class="profileForm-content1">
              <div class="groupForm groupForm1">
                <label v-if="isEdit" class="groupForm-label">自己紹介</label>
                <div v-if="isEdit" class="groupForm-control" :class="validField(user_content.note_1) ? 'hasErr' : ''">
                  <textarea v-model="user_content.note_1" class="form-control" placeholder="内容入力"></textarea>
                  <p v-if="validField(user_content.note_1)" class="text-error">{{ getMessage('MSFA0012', '自己紹介', '200') }}</p>
                </div>
                <div v-else class="box">
                  <p class="title-field">自己紹介</p>
                  <div
                    class="field-content scroll-child scrollbar"
                    @mouseenter="childFocus"
                    @mouseleave="childFocusOut"
                    @touchend="childFocusOut"
                    @touchstart="childFocus"
                  >
                    <p v-if="user_content.note_1?.length > 0" style="white-space: break-spaces" class="field-content-view">{{ user_content.note_1 }}</p>
                    <div v-else class="field-nodata">まだ情報が登録されていません。</div>
                  </div>
                </div>
              </div>
              <div class="groupForm groupForm1">
                <label v-if="isEdit" class="groupForm-label">現在の業務</label>
                <div v-if="isEdit" class="groupForm-control" :class="validField(user_content.note_2) ? 'hasErr' : ''">
                  <textarea v-model="user_content.note_2" class="form-control" placeholder="内容入力"></textarea>
                  <p v-if="validField(user_content.note_2)" class="text-error">{{ getMessage('MSFA0012', '現在の業務', '200') }}</p>
                </div>
                <div v-else class="box">
                  <p class="title-field">現在の業務</p>
                  <div
                    class="field-content scroll-child scrollbar"
                    @mouseenter="childFocus"
                    @mouseleave="childFocusOut"
                    @touchend="childFocusOut"
                    @touchstart="childFocus"
                  >
                    <p v-if="user_content.note_2?.length > 0" style="white-space: break-spaces" class="field-content-view">{{ user_content.note_2 }}</p>
                    <div v-else class="field-nodata">まだ情報が登録されていません。</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="profileForm-content2">
              <div class="groupForm">
                <label v-if="isEdit" class="groupForm-label">趣味・特技</label>
                <div v-if="isEdit" class="groupForm-control" :class="validField(user_content.note_3) ? 'hasErr' : ''">
                  <textarea v-model="user_content.note_3" class="form-control" placeholder="内容入力"></textarea>
                  <p v-if="validField(user_content.note_3)" class="text-error">{{ getMessage('MSFA0012', '趣味・特技', '200') }}</p>
                </div>
                <div v-else class="box box2">
                  <p class="title-field">趣味・特技</p>
                  <div
                    class="field-content scroll-child scrollbar field-content2"
                    @mouseenter="childFocus"
                    @mouseleave="childFocusOut"
                    @touchend="childFocusOut"
                    @touchstart="childFocus"
                  >
                    <p v-if="user_content.note_3?.length > 0" style="white-space: break-spaces" class="field-content-view">{{ user_content.note_3 }}</p>
                    <div v-else class="field-nodata">まだ情報が登録されていません。</div>
                  </div>
                </div>
              </div>
              <div class="groupForm">
                <label v-if="isEdit" class="groupForm-label">出身地・住んだことのある所</label>
                <div v-if="isEdit" class="groupForm-control" :class="validField(user_content.note_4) ? 'hasErr' : ''">
                  <textarea v-model="user_content.note_4" class="form-control" placeholder="内容入力"></textarea>
                  <p v-if="validField(user_content.note_4)" class="text-error">{{ getMessage('MSFA0012', '出身地・住んだことのある所', '200') }}</p>
                </div>
                <div v-else class="box box2">
                  <p class="title-field">出身地・住んだことのある所</p>
                  <div
                    class="field-content scroll-child scrollbar field-content2"
                    @mouseenter="childFocus"
                    @mouseleave="childFocusOut"
                    @touchend="childFocusOut"
                    @touchstart="childFocus"
                  >
                    <p v-if="user_content.note_4?.length > 0" style="white-space: break-spaces" class="field-content-view">{{ user_content.note_4 }}</p>
                    <div v-else class="field-nodata">まだ情報が登録されていません。</div>
                  </div>
                </div>
              </div>
              <div class="groupForm">
                <label v-if="isEdit" class="groupForm-label">その他</label>
                <div v-if="isEdit" class="groupForm-control" :class="validField(user_content.note_5) ? 'hasErr' : ''">
                  <textarea v-model="user_content.note_5" class="form-control" placeholder="内容入力"></textarea>
                  <p v-if="validField(user_content.note_5)" class="text-error">{{ getMessage('MSFA0012', 'その他', '200') }}</p>
                </div>
                <div v-else class="box box2">
                  <p class="title-field">その他</p>
                  <div
                    class="field-content scroll-child scrollbar field-content2"
                    @mouseenter="childFocus"
                    @mouseleave="childFocusOut"
                    @touchend="childFocusOut"
                    @touchstart="childFocus"
                  >
                    <p v-if="user_content.note_5?.length > 0" style="white-space: break-spaces" class="field-content-view">{{ user_content.note_5 }}</p>
                    <div v-else class="field-nodata">まだ情報が登録されていません。</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="isEdit" class="profileBtn">
            <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="confirmCancel">キャンセル</button>
            <el-button type="primary" class="btn btn-primary" :loading="processing" @click.prevent="submitForm">保存</el-button>
          </div>

          <div class="title-tab">
            <span>活用度ポイント獲得履歴</span>
          </div>
          <div class="groupHistory">
            <div class="groupHistory-head">
              <div class="groupHistory-select">
                <label class="groupHistory-label">コンテンツ</label>
                <el-select v-model="point_target_type" placeholder="未選択" class="form-control-select" @change="selectedTarget">
                  <el-option label="" value="">未選択</el-option>
                  <el-option v-for="item in selectList" :key="item.value" :label="item.label" :value="item.value"></el-option>
                </el-select>
              </div>
            </div>
            <div class="application-btn">
              <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
                <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
              </button>
              <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
                <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
              </button>
            </div>
            <div
              ref="listPointRef"
              class="tblHistory table-hg100 scrollbar scroll-child"
              @scroll="onScroll"
              @mouseenter="childFocus"
              @mouseleave="childFocusOut"
              @touchstart="childFocus"
              @touchend="childFocusOut"
            >
              <table class="tableBox tableCustom">
                <thead>
                  <tr>
                    <th>コンテンツ</th>
                    <th>アクション</th>
                    <th>活用度ポイント</th>
                    <th>獲得日時</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in point.pointList" :key="item.point_id">
                    <td>
                      <span class="span-label span-label--green">{{ item.point_target_type_title }}</span>
                    </td>
                    <td>
                      <span>{{ item.message }}</span>
                    </td>
                    <td>{{ item.active_point && item.active_point + 'pt' }}</td>
                    <td>
                      <span class="span-date">{{ formatFullDate(item.created_at) }}</span>
                      <span class="span-time">{{ formatTimeHourMinute(item.created_at) }}</span>
                    </td>
                  </tr>
                  <tr v-if="!point.pointList.length">
                    <td v-if="!isLoading" colspan="4">
                      <EmptyData />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="point.pointList.length > 0" class="pagin-institution">
              <div class="pagination-cases">全 {{ pagination.total_items }} 件</div>
              <Pagination :current-page="pagination.current_page" :total="pagination.total_items" :page-size="50" @current-change="handleChangePage" />
            </div>
          </div>
        </div>
      </div>
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
        <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="closeModal" @handleYes="closeForm"></component>
      </template>
    </el-dialog>

    <!-- start::confirm change data to redirect page -->
    <el-dialog
      v-model="modalConfirmRedirect.isShowModal"
      custom-class="custom-dialog modal-fixed modal-fixed-min"
      :title="modalConfirmRedirect.title"
      :width="modalConfirmRedirect.width"
      :destroy-on-close="modalConfirmRedirect.destroyOnClose"
      :close-on-click-modal="modalConfirmRedirect.closeOnClickMark"
      :show-close="modalConfirmRedirect.isShowClose"
    >
      <template #default>
        <component
          :is="modalConfirmRedirect.component"
          v-bind="{ ...modalConfirmRedirect.props }"
          @onFinishScreen="modalConfirmRedirect.isShowModal = false"
          @handleYes="confirmRedirect"
        ></component>
      </template>
    </el-dialog>
    <!-- end::confirm change data to redirect page -->
  </div>
</template>

<script>
import Z04_S01_Services from '@/api/Z04/Z04_S01_AccountSettingServices';
import { validImageUpload } from '@/utils/validate';
import _ from 'lodash';
import { markRaw } from 'vue';
import { encodeData } from '@/api/base64_api';
import PerfectScrollbar from '@/assets/template/js/perfect-scrollbar.min.js';

export default {
  name: 'Z04_S01_AccountSetting',
  beforeRouteLeave(to, from, next) {
    // data changed and confirmed
    this.nextPage = to.name;
    if (this.isChanged && !this.isConfirmRedirect) {
      this.popupConfirmRedirect();
      next(false);
    } else {
      next();
    }
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      psContainer: null,
      focusChild: false,
      guest: true,
      user_cd: '', // URL params
      user_content: {
        user_name: 'ミュートス 太郎',
        user_cd: '10002',
        mail_address: 'xxxxxx@mythos-jp.com',
        total_points: 2450,
        avatar_image_type: '',
        avatar_image_data: '',
        avatar_file_name: '',
        file_id: '',
        definition: [],
        org_list: [],
        note_1: '',
        note_2: '',
        note_3: '',
        note_4: '',
        note_5: ''
      },
      user_content_old: {},

      isLoading: false,
      processing: false,
      isEdit: false,
      point_target_type: '',
      selectList: [],
      point: {
        total_points: 0,
        pointList: []
      },
      pagination: {
        current_page: 1,
        total_pages: 100,
        first_page: 1,
        last_page: 1,
        next_page: 1,
        previous_page: 1,
        total_items: 0
      },
      modalConfig: {
        title: '',
        isShowModal: false,
        isShowClose: true,
        component: null,
        props: {},
        width: 0,
        customClass: 'custom-dialog',
        destroyOnClose: true,
        closeOnClickMark: false
      },
      showScrollLeft: false,
      showScrollRight: false,
      nextPage: '',
      isConfirmRedirect: false,
      modalConfirmRedirect: {
        title: null,
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false,
        isShowClose: false
      }
    };
  },
  computed: {
    isChanged() {
      return !_.isEqual(this.user_content, this.user_content_old);
    }
  },
  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'Z04_S01_AccountSettings') {
          this.user_cd = toParams?.user_cd || this.user_cd;
          this.getUserInfo();
          this.getListTarget();
        }
      }
    );
  },
  mounted() {
    let { user_cd } = this.$route.params;
    // user_cd = user_cd ? user_cd : this.$route.query.user_cd;
    this.user_cd = user_cd ? user_cd : this.getCurrentUser().user_cd;
    this.emitter.emit('change-header', {
      title: 'アカウント管理',
      icon: 'icon_profile.svg',
      isShowBack: true
    });
    this.user_content.avatar_image_data = this.avataDefault();
    this.getUserInfo();
    this.getListTarget();
    this.js_pscroll();

    const els = document.querySelectorAll('.scroll-child');
    const elMain = this.$refs.mainRef;
    els.forEach((el) => {
      el.onscroll = function () {
        elMain.classList.add('stop-scroll');
        return;
      };
    });

    // Add perfect scroll
    this.psContainer = new PerfectScrollbar('#accSetting', {
      wheelSpeed: 0.2
    });
  },

  methods: {
    validField(value) {
      if (value?.length > 200) {
        return true;
      }
      return false;
    },
    handleChooseImage() {
      if (this.guest) return;
      this.$refs.uploadImage.click();
    },
    handleChangePage(page) {
      this.pagination.current_page = page;
      this.getListPoint({
        point_target_type: this.point_target_type,
        user_cd: this.user_cd,
        limit: 50,
        offset: page === 0 ? page : page - 1
      });
    },

    onScrollLeft() {
      let content = document.querySelector('.tblHistory');
      content.scrollLeft -= 200;
    },

    onScrollRight() {
      let content = document.querySelector('.tblHistory');
      content.scrollLeft += 200;
    },

    onScroll({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }

      if (this.isScrolling) {
        let content = document.querySelector('.tblHistory');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },

    previewUploadImage(event) {
      const size = 1024 * 1024 * 2;
      const file = event.target.files[0];
      const validExtensions = /(\.jpg|\.jpeg|\.png|\.jpe)$/i;

      if (!validExtensions.exec(file.name)) {
        this.$notify({ message: this.getMessage('MSFA0038'), customClass: 'error' });
        return;
      }

      if (file.size > size) {
        this.$notify({ message: this.getMessage('MSFA0039', '2MB'), customClass: 'error' });
        return;
      }

      const fileReader = new FileReader();
      fileReader.onloadend = async (e) => {
        let arr = new Uint8Array(e.target.result).subarray(0, 4);
        let headerFile = '';
        for (let i = 0; i < arr.length; i++) {
          headerFile += arr[i].toString(16);
        }

        if (validImageUpload(headerFile) === '' || validImageUpload(headerFile) === 'unknown') {
          this.$notify({ message: this.getMessage('MSFA0038'), customClass: 'error' });
        } else {
          const formData = new FormData();
          formData.append('avatar', file);
          formData.append('avatar_file_name', encodeData(this.user_content.avatar_file_name));
          formData.append('file_id', encodeData(this.user_content.file_id));
          this.changeAvatar(formData);
        }
      };
      fileReader.readAsArrayBuffer(file);
    },
    editField() {
      this.isEdit = true;
    },

    closeForm() {
      this.getUserInfo();
      this.closeModal();
    },

    closeModal() {
      this.modalConfig.isShowModal = false;
    },

    confirmCancel() {
      if (this.isChanged) {
        this.modalConfig = {
          ...this.modalConfig,
          title: '',
          isShowModal: true,
          isShowClose: false,
          component: markRaw(this.$PopupConfirm),
          props: { mode: 1 },
          width: '35rem',
          customClass: 'custom-dialog modal-fixed modal-fixed-min',
          destroyOnClose: true,
          closeOnClickMark: false
        };
      } else {
        this.closeForm();
      }
    },

    selectedTarget(value) {
      this.point_target_type = value;
      this.getListPoint({
        point_target_type: value,
        user_cd: this.user_cd,
        limit: 50,
        offset: 0
      });
    },
    getUserInfo() {
      this.isLoading = true;
      Z04_S01_Services.getUserInfoService({ user_cd: this.user_cd })
        .then((res) => {
          const dataRes = res.data.data;
          this.isEdit = false;
          this.user_content = {
            ...dataRes,
            avatar_image_data: dataRes.avatar_image_data,
            org_list: dataRes.org_list
          };

          if (this.user_content.avatar_image_data) {
            fetch(this.user_content.avatar_image_data).then((response) => {
              // eslint-disable-next-line eqeqeq
              if (!response.ok || response.status != 200) {
                this.user_content.avatar_image_data = this.avataDefault();
                this.user_content_old.avatar_image_data = this.avataDefault();
              }
            });
          } else {
            this.user_content.avatar_image_data = this.avataDefault();
            this.user_content_old.avatar_image_data = this.avataDefault();
          }

          const userLogin = this.$store.state.auth.currentUser.user_cd;
          this.guest = userLogin !== this.user_content.user_cd;
          this.organization = {
            organization_1: dataRes?.definition[0]?.org_name || '',
            organization_2: dataRes?.definition[1]?.org_name || ''
          };

          this.user_content_old = { ...this.user_content };

          this.emitter.emit('change-header', {
            title: this.guest ? 'ユーザ情報' : 'アカウント管理',
            icon: 'icon_profile.svg',
            isShowBack: true
          });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => {
          this.isLoading = false;
        });
    },
    getListPoint(params) {
      this.js_pscroll();
      Z04_S01_Services.getListPointService(params)
        .then((res) => {
          this.point = {
            total_points: res.data.data.total_points,
            pointList: res.data.data.records
          };
          this.pagination = { ...res.data.data.pagination };
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          if (this.$refs.listPointRef) {
            this.$refs.listPointRef.scrollTop = 0;
          }
        });
    },
    submitForm() {
      let params = {
        note_1: this.user_content.note_1,
        note_2: this.user_content.note_2,
        note_3: this.user_content.note_3,
        note_4: this.user_content.note_4,
        note_5: this.user_content.note_5
      };
      this.processing = true;
      Z04_S01_Services.changeAccountInfoService(params)
        .then((res) => {
          this.isEdit = false;
          this.closeForm();
          this.$notify({ message: res.data.message, customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }))
        .finally(() => (this.processing = false));
    },
    getListTarget() {
      Z04_S01_Services.getListTargetTypeService()
        .then((res) => {
          this.selectList = res.data.data.map((item) => ({ ...item, label: item.description }));
          this.getListPoint({
            point_target_type: '',
            user_cd: this.user_cd,
            limit: 50,
            offset: 0
          });
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }));
    },
    changeAvatar(params) {
      Z04_S01_Services.changeAvatarService(params)
        .then((res) => {
          let objTemp = {
            avatar_image_data: res.data.data.file_url,
            avatar_image_type: res.data.data.mime_type,
            avatar_file_name: res.data.data.file_name,
            file_id: res.data.data.file_id
          };
          this.user_content = {
            ...this.user_content,
            ...objTemp
          };
          this.user_content_old = {
            ...this.user_content_old,
            ...objTemp
          };
          this.$notify({ message: res.data.message, customClass: 'success' });
          this.$store.dispatch('auth/updateAvatarUser', objTemp);
        })
        .catch((err) => this.$notify({ message: err.response.data.message, customClass: 'error' }));
    },
    popupConfirmRedirect() {
      this.modalConfirmRedirect = {
        isShowModal: true,
        title: null,
        component: this.markRaw(this.$PopupConfirm),
        width: '35rem',
        props: { mode: 1, textCancel: 'いいえ' },
        isShowClose: false
      };
    },
    confirmRedirect() {
      this.isConfirmRedirect = true;
      this.modalConfirmRedirect.isShowModal = false;
      this.$router.push({ name: this.nextPage });
    },
    childFocusOut() {
      const elMain = this.$refs.mainRef;
      if (elMain && elMain?.classList) {
        elMain?.classList?.remove('stop-scroll');

        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = new PerfectScrollbar('#accSetting', {
          wheelSpeed: 0.2
        });
      }
    },
    childFocus() {
      const els = document.querySelectorAll('.scroll-child');
      const elMain = this.$refs.mainRef;

      setTimeout(() => {
        if (this.psContainer) this.psContainer.destroy();
        this.psContainer = null;
      }, 0);

      els.forEach(() => {
        if (elMain && elMain?.classList) {
          elMain.classList.add('stop-scroll');
        }
        return;
      });
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_tablet: 991px;
.pagin-institution {
  margin: 15px 0;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  .pagination-cases {
    margin-right: 10px;
    color: #8e8e8e;
    font-weight: 500;
  }
}
.page-accSetting {
  height: 100%;
  background: #f6f6f6;
  box-sizing: border-box;
  .accSetting {
    height: 100%;
    .row-container {
      width: 100%;
      height: 100%;
      .col-container-3 {
        height: calc(100% - 45px);
        position: -webkit-sticky;
        position: fixed;
        top: 45px;
        width: 230px;
        padding: 28px 16px 20px 16px;
        background: #fcfcfc;
        box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
        z-index: 5;
        @media (min-width: 1190px) {
          width: 260px;
        }
        .groupProfile {
          height: 100%;
        }
        .userProfile {
          border-bottom: 1px solid #e3e3e3;
          padding-bottom: 20px;
          .userProfile__thumb {
            width: 100px;
            margin: 0 auto;
            position: relative;
            .itemPen {
              position: absolute;
              bottom: 0;
              right: 0;
              width: 32px;
              height: 32px;
              border-radius: 50%;
              background: #fff;
              cursor: pointer;
              box-shadow: 0px 2px 10px rgba(183, 195, 203, 0.5);
              display: flex;
              align-items: center;
              justify-content: center;

              &:hover {
                border: 2px solid #448add;
              }
            }
            .imgThumb {
              display: flex;
              width: 100px;
              height: 100px;
              overflow: hidden;
              border-radius: 50%;
              border: 1px solid #ff9519;
              img {
                width: 100%;
              }
            }
          }
          .userProfile__content {
            text-align: center;
            .name {
              font-weight: 700;
              font-size: 24px;
              line-height: 33.6px;
              color: #2d3033;
              margin: 10px 0;
              margin-top: 20px;
            }
            .text {
              margin: 0 auto;
              margin-top: 16px;
              background: #ea5d54;
              padding: 6px 12px 8px 12px;
              border-radius: 8px;
              color: white;
              display: flex;
              justify-content: space-between;
              font-size: 16px;
              @media (max-width: 1024px) {
                width: 100%;
                .spanLabel,
                .text-span {
                  font-size: 14px;
                  font-weight: 500;
                  line-height: 16px;
                }
              }
              @media (max-width: 991px) {
                width: 100%;
              }
              .spanLabel {
                font-size: 14px;
                font-weight: 500;
                line-height: 20.27px;
              }
              .text-span {
                font-size: 18px;
                font-weight: 700;
                line-height: 18px;
              }
            }
            .mail {
              margin-top: 20px;
              display: flex;
              flex-direction: row;
              justify-content: center;
              .icon {
                margin-right: 5px;
                img {
                  width: 16.67px;
                }
              }
              .text-mail {
                font-size: 16px;
                font-weight: 400;
              }
            }
          }
        }
        .organization {
          .title {
            margin: 16px 0 8px 0;
            white-space: nowrap;
            font-size: 14px;
            font-weight: 700;
            line-height: 20.27px;
          }
          ul {
            max-height: calc(100vh - 415px);
            @media (max-width: 1024px) {
              max-height: calc(100vh - 435px);
            }
            li {
              padding: 8px 0 12px 0;
              .item {
                min-width: 18px;
                margin-right: 5px;
              }
              .item-icon {
                margin-right: 4px;
                svg {
                  margin-left: -1px;
                }
              }
              .item-top {
                margin-bottom: 4px;
              }
              .item-title,
              .item-content {
                font-size: 14px;
                font-weight: 400;
              }
              p {
                display: flex;
              }
            }
          }
        }
      }
      .col-container-9 {
        background: #f6f6f6;
        width: calc(100% - 230px);
        float: right;
        padding-top: 15px;
        @media (min-width: 1191px) {
          width: calc(100% - 260px);
        }
        .title-tab {
          display: flex;
          flex-direction: row;
          justify-content: space-between;
          align-items: center;
          position: static;
          width: 440px;
          height: 52px;
          left: 0px;
          top: 0px;
          padding: 0px 24px 0px 52px;
          background: #eef6ff;
          box-shadow: 3px 3px 8px 0px rgba(145, 161, 171, 0.6);
          border-radius: 0px 40px 40px 0px;
          flex: none;
          order: 0;
          flex-grow: 0;
          span {
            position: static;
            width: 207px;
            height: 52px;
            left: calc(50% - 207px / 2 - 64.5px);
            top: calc(50% - 52px / 2);
            font-style: normal;
            font-weight: bold;
            font-size: 18px;
            line-height: 100%;
            display: flex;
            align-items: center;
          }
          svg {
            width: 18px;
            height: 18px;
          }
          .activeBtn {
            background: #448add;
            color: #ffffff;
            font-weight: normal;

            img {
              filter: invert(100%) sepia(2%) saturate(3%) hue-rotate(148deg) brightness(200%) contrast(100%);
            }

            &:hover {
              background: #0062cc !important;
              outline: none;
              color: #ffffff;
              img {
                filter: invert(100%) sepia(2%) saturate(3%) hue-rotate(148deg) brightness(200%) contrast(100%);
              }
            }
          }
          button {
            width: 85px;
            height: 35px;
            background: #ffffff;
            border: 1px solid #448add;
            font-weight: bold;
            font-size: 15px;
            border-radius: 24px;
            letter-spacing: 0.03em;
            color: #448add;
            position: relative;
            img {
              filter: invert(49%) sepia(60%) saturate(1896%) hue-rotate(192deg) brightness(92%) contrast(87%);
            }
            &:hover {
              background: #ffffff !important;
              outline: 2px solid #76aff5;
              color: #448add;

              img {
                filter: invert(49%) sepia(60%) saturate(1896%) hue-rotate(192deg) brightness(92%) contrast(87%);
              }
            }
          }
        }
        .profileBtn {
          padding: 10px 0 20px 0;
          text-align: center;
          .btn {
            width: 180px;
            margin-right: 24px;
            &:last-child {
              margin-right: 0;
            }
          }
        }
        .profileForm {
          padding: 15px 32px 8px;
          height: 515px;
          .profileForm-content1 {
            float: left;
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding-right: 10px;
            height: 100%;
            flex: none;
            order: 1;
            flex-grow: 1;
            gap: 10px;
          }
          .profileForm-content2 {
            float: left;
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding-left: 10px;
            height: 100%;
            flex: none;
            order: 1;
            flex-grow: 1;
            gap: 10px;
          }
          .groupForm {
            height: 100%;
            width: 100%;
            .groupForm-control {
              height: 100%;
              margin-top: 0;
              textarea {
                height: calc(100% - 30px);
              }

              &.hasErr {
                textarea {
                  height: calc(100% - 55px);
                }
              }
            }

            &.groupForm1 {
              .groupForm-control {
                &.hasErr {
                  textarea {
                    height: calc(100% - 53px);
                  }
                }
              }
            }

            .groupForm-label {
              height: 20px;
            }

            .box {
              padding: 10px;
              border-radius: 8px;
              box-shadow: 0.5px 0.5px 6px rgba(183, 195, 203, 0.9);
              background: #fff;
              height: 230px;
            }
            p.title-field {
              padding: 0 10px;
              height: 25px;
              color: #99a5ae;
            }
            .field-content {
              overflow: auto;
              height: calc(100% - 28px);
              background: #ffffff;
              padding: 5px 10px;
              div.field-nodata {
                height: 80%;
                display: flex;
                justify-content: center;
                align-items: center;
                font-weight: 500;
                font-size: 14px;
                color: #b7c3cb;
              }
            }
            .box2 {
              height: 146px;
            }
            .field-content2 {
              height: 90px;
              div.field-nodata {
                height: 65%;
              }
            }
          }
        }
      }
    }
  }
  .groupHistory {
    padding: 20px 22px 32px;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    margin-right: 10px;
    .groupHistory-head {
      display: flex;
      justify-content: space-between;
      padding-bottom: 24px;
      .groupHistory-select {
        display: flex;
        .groupHistory-label {
          font-size: 16px;
          white-space: nowrap;
          padding: 8px 40px 0 0;
        }
        .select-control {
          width: 284px;
        }
      }
      .points {
        padding-top: 8px;
        font-size: 16px;
        color: #727f88;
        .points-number {
          color: #2d3033;
          padding-left: 20px;
        }
      }
    }
    .application-btn {
      .btn {
        position: absolute;
        top: calc(50% - 23px);
        height: 46px;
        width: 52px;
        padding: 0;
        background: rgba(63, 75, 86, 0.4);
        z-index: 1;
        &.btn--prev {
          border-radius: 0px 60px 60px 0px;
        }
        &.btn--next {
          right: 20px;
          border-radius: 60px 0px 0px 60px;
          z-index: 5;
          .svg {
            transform: rotate(-180deg);
          }
        }
      }
    }
  }
  .pagin {
    padding: 16px 0 24px;
    display: flex;
    justify-content: flex-end;
    .pagination-number {
      border-radius: 10px;
      box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    }
  }
  .tblHistory {
    box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.9);
    border-radius: 10px 10px 0px 0px;
    overflow: auto;
    height: 488px;
    position: relative;
    .tableBox {
      tr {
        th,
        td {
          &:nth-child(1),
          &:nth-child(3),
          &:nth-child(4) {
            text-align: center;
          }
          &:nth-child(1),
          &:nth-child(3),
          &:nth-child(4) {
            width: 1rem;
            min-width: 110px;
          }
          &:nth-child(2) {
            min-width: 200px;
          }
        }
        td {
          border: none;
          border-bottom: 1px solid #e3e3e3;
          padding-top: 10px;
          padding-bottom: 10px;
          .span-label {
            min-width: 86px;
            display: inline-block;
          }
          .span-time {
            padding-left: 16px;
          }
        }
      }
    }
  }
}
.stop-scroll {
  overflow: hidden;
}

@media (max-width: 1024px) {
  .col-container-9 {
    padding-right: 20px;
  }

  .profileForm {
    padding: 15px 20px 8px 22px !important;
  }

  .groupHistory {
    padding: 20px 20px 32px 22px !important;
    margin-right: 0 !important;
  }
}

@media (min-width: 1233px) {
  .profileForm {
    .groupForm {
      .groupForm-control {
        &.hasErr {
          textarea {
            height: calc(100% - 40px) !important;
          }
        }
      }
      &.groupForm1 {
        .groupForm-control {
          &.hasErr {
            textarea {
              height: calc(100% - 41px) !important;
            }
          }
        }
      }
    }
  }
}

@media (max-width: 768px) {
  .profileForm {
    height: 549px !important;
    .groupForm {
      .groupForm-control {
        &.hasErr {
          textarea {
            height: calc(100% - 70px) !important;
          }
        }
      }
      &.groupForm1 {
        .groupForm-control {
          &.hasErr {
            textarea {
              height: calc(100% - 56px) !important;
            }
          }
        }
      }
    }
  }
}

@media (max-height: 768px) {
  .profileForm {
    height: 480px !important;
    padding: 10px 20px 8px 22px !important;
    .groupForm {
      .groupForm-control {
        height: calc(100% - 10px);
      }
      &.groupForm1 {
        .groupForm-control {
          &.hasErr {
            textarea {
              height: calc(100% - 35px) !important;
            }
          }
        }
      }

      .box {
        height: 210px !important;
      }

      .box2 {
        height: 133px !important;
      }
    }
  }
}
#accSetting {
  position: relative;
  overflow: hidden !important;
}
.text-error {
  line-height: 21px;
}
</style>
