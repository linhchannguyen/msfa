<template>
  <div class="wrapContent">
    <div class="groupMetarial">
      <div class="materialCreation">
        <!-- end Head Details -->
        <!-- start Tabs -->
        <div class="metarialTabs">
          <ul class="nav navTabs navTabs--metarial width-tabs">
            <li>
              <a id="a1" class="tab_person" @click="goTab('1')" @touchstart="goTab('1')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_profile02.svg')" alt="" />
                  </span>
                  資材プロフィール
                </div>
              </a>
            </li>
            <li>
              <a id="a2" class="tab_person" @click="goTab('2')" @touchstart="goTab('2')">
                <div class="navInfo">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/icon_grid.svg')" alt="" />
                  </span>
                  資材構成
                </div>
              </a>
            </li>
          </ul>
          <div v-loading="isLoading" class="tab-content z-index-child">
            <div id="basicInformation" role="tabpanel" :class="['tab-pane', changetab == '1' ? 'active' : '']">
              <D01S05Tab1
                ref="tab1"
                :key="keyTab1"
                :changetab="`${changetab}`"
                :is-submit="isSubmit"
                :validation="validation"
                :custom_document="custom_document"
                :type_modal="_route('D01_S05_CustomMaterialCreation')?.params?.copy_id ? _route('D01_S05_CustomMaterialCreation')?.params?.copy_id : null"
                @onSenData="dataTab1"
              />
            </div>
            <div id="workHistory" ref="parentPpx" role="tabpanel" :class="['tab-pane', changetab == '2' ? 'active' : '']">
              <D01S05Tab2
                ref="savePpt"
                :key="keyTab2"
                :off-edit="offEdit"
                :list-slide="list_custom_document_slide"
                :usage-id="custom_document"
                :available-org-cd="custom_document.available_org_cd"
                :changetab="`${changetab}`"
                @save-ppt-all="savePptAll"
                @slide-loading="disableSubmit"
              />
            </div>
            <div class="footer-pane">
              <div class="groupBtn">
                <button
                  type="button"
                  :disabled="isDisableSubmit"
                  :class="['btn btn-outline-primary btn-outline-primary--cancel', isDisableSubmit ? 'event-none-cancel' : '']"
                  @click="eventGroup()"
                >
                  {{ nameEvent }}
                </button>
                <button v-show="showDeleteBtn()" type="button" class="btn btn-outline-primary--cancel btn-outline-primary" @click="delelteDoc">削除</button>
                <button type="button" :class="['btn btn-primary', isDisableSubmit ? 'event-none-submit' : '']" @click.prevent="savePpt()">
                  <span class="textSubmit">保存</span>
                </button>
              </div>
            </div>
          </div>

          <!-- end Tabs -->
        </div>
      </div>
    </div>
    <el-dialog
      v-model="modalConfig.isShowModal"
      custom-class="custom-dialog-document custom-dialog modal-fixed modal-fixed-min"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :show-close="false"
    >
      <template #default>
        <component
          :is="modalConfig.component"
          v-bind="{ ...modalConfig.props }"
          @onFinishScreen="onCloseModal"
          @handleYes="onExece"
          @handleConfirm="onExece"
        ></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import D01S05Tab1 from './D01_S05_Tabs/D01_S05_Tab1.vue';
import D01S05Tab2 from './D01_S05_Tabs/D01_S05_Tab2.vue';
// import domtoimage from 'dom-to-image';
import D01_S05_CustomMaterialCreationServices from '@/api/D01/D01_S05_CustomMaterialCreationService';
import { required } from '../../../utils/validate';
import { markRaw } from 'vue';
import { encodeData } from '@/api/base64_api';

export default {
  name: 'D01_S05_CustomMaterialCreation',
  components: {
    D01S05Tab1,
    D01S05Tab2
  },

  props: {
    idCopy: {
      type: String,
      default: null,
      required: false
    },
    checkLoading: [Boolean]
  },
  data() {
    return {
      isDisableSubmit: false,
      isSubmit: null,
      changetab: '0',
      classActive: {},
      pagination: {
        current_page: 0,
        total_pages: 0,
        total_items: 0
      },
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      body: {
        // tab1
        document_name: '',
        product_cd: '',
        product_name: '',
        document_contents: '',
        disease: '',
        medical_subjects_group_cd: '',
        safety_information_flag: '1',
        off_label_information_flag: '0'
      },
      // when have get doc by id send data tab 1
      custom_document: {
        available_org_cd: '',
        create_user_cd: '',
        disease: '',
        disuse_flag: 0,
        document_id: null,
        document_name: '',
        document_type: '',
        end_date: '',
        file_type: '',
        medical_subjects_group_cd: '',
        off_label_information_flag: '',
        product_cd: '',
        safety_information_flag: '',
        source_document_id: 0,
        start_date: '',
        document_contents: ''
      },
      oldBody: {
        document_name: '',
        product_cd: '',
        product_name: '',
        document_contents: '',
        disease: '',
        medical_subjects_group_cd: '',
        safety_information_flag: '1',
        off_label_information_flag: '0'
      },
      // when have get doc by id send data tab 2
      list_custom_document_slide: [],
      list_custom_orign: [],
      documentID: null,
      documentIDCopy: null,
      isLoading: false,
      disuse_flag: 0,
      showDelete: false,
      nameEvent: 'キャンセル',
      offEdit: false,
      type: '',
      document_slideReq: [],
      document_slideReq_old: [],
      keyTab1: Math.random(),
      keyTab2: Math.random()
    };
  },
  computed: {
    validation() {
      return {
        document_name: { required: required(this.body.document_name) },
        product_name: { required: required(this.body.product_name) },
        safety_information_flag: { required: required(this.body.safety_information_flag) },
        off_label_information_flag: { required: required(this.body.off_label_information_flag) }
        // medical_subjects_group_cd: { required: required(this.body.medical_subjects_group_cd) },
        // disease: { required: required(this.body.disease) }
      };
    }
  },
  watch: {},
  mounted() {
    this.emitter.emit('change-header', {
      title: 'カスタム資材作成',
      icon: 'Material-icon.svg',
      isShowBack: true
      // oldRoute: this.componentHistory().name
    });
    setTimeout(() => {
      this.activeTab();
    });
    this.documentIDCopy = this._route('D01_S05_CustomMaterialCreation')?.params?.copy_id
      ? this._route('D01_S05_CustomMaterialCreation')?.params?.copy_id
      : null;
    this.documentID = this.documentID = this._route('D01_S05_CustomMaterialCreation')?.params?.document_id
      ? this._route('D01_S05_CustomMaterialCreation')?.params?.document_id
      : null;

    const id = this.documentIDCopy ? this.documentIDCopy : this.documentID;
    this.type = id ? Object.keys(this._route('D01_S05_CustomMaterialCreation')?.params)[1] : '';
    this.getDocumentByCase();
  },

  methods: {
    modalConfirmRoute(callBack) {
      this.modalConfig = {
        ...this.modalConfig,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        props: {
          mode: 1,
          params: { callBack }
        }
      };
    },
    disableSubmit(e) {
      this.isDisableSubmit = e;
    },
    getDocumentByCase() {
      if (this.documentIDCopy === 'create') {
        return;
      }
      if (this.documentIDCopy) {
        this.getDocumentById(this.documentIDCopy);
      } else if (this.documentID) {
        this.getDocumentById(this.documentID);
      }
      //getDocumentUpdate
    },
    /** API */
    changeDisueFlagStatus(status) {
      this.isLoading = true;
      D01_S05_CustomMaterialCreationServices.changeDisueFlagStatus(this.documentID, status)
        .then((s) => {
          const message = s.data.message;
          const flag = s.data.data.disuse_flag;
          flag === 1 ? (this.nameEvent = '利用終了') : (this.nameEvent = '利用再開');

          this.$notify({ message: message, customClass: 'success' });
        })
        .catch((err) => this.$notify({ message: err?.response?.data?.message, customClass: 'error' }))
        .finally(() => (this.isLoading = false));
    },
    // API* get doc by id
    getDocumentById(id) {
      this.isLoading = true;
      D01_S05_CustomMaterialCreationServices.getDocumentUpdate(id)
        .then((res) => {
          const data = res?.data?.data || [];
          this.custom_document = data.custom_document ? { ...data.custom_document } : null;
          this.oldBody = data.custom_document;
          this.list_custom_document_slide = data.list_custom_document_slide.map((s) => ({
            ...s,
            content: this.decodeData(s.cover_html).map((s) => ({ ...s, origin: s }))
          }));
          this.disuse_flag = data.custom_document.disuse_flag;
          this.document_slideReq = this.list_custom_document_slide;
          this.document_slideReq_old = JSON.parse(JSON.stringify(this.list_custom_document_slide));

          this.keyTab1 = Math.random();
          this.changeNameEvent();
        })
        .catch((err) => {
          this.$notify({ message: err?.response?.data?.message, customClass: 'error' });
          this.loadingPage = false;
        })
        .finally(() => ((this.isLoading = false), (this.isDisableSubmit = false)));
    },
    //API* create doc
    createDoc(params) {
      // tạo mới và tạo dưới dạng copy
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        this.isDisableSubmit = false;
        this.isLoading = false;
        return;
      }
      D01_S05_CustomMaterialCreationServices.create(params)
        .then((s) => {
          const message = s.data.message;
          this.$notify({ message: message, customClass: 'success' });
          // eslint-disable-next-line eqeqeq
          this.$router.push({ name: 'D01_S04_CustomMaterialManagement' });
        })
        .catch((err) => this.$notify({ message: err?.response?.data?.message, customClass: 'error' }))
        .finally(() => ((this.isLoading = false), (this.isDisableSubmit = false)));
      this.isLoading = false;
    },
    // API update doc
    updateDoc(params) {
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        this.isDisableSubmit = false;
        this.isLoading = false;
        return;
      }
      D01_S05_CustomMaterialCreationServices.update(this.documentID, params)
        .then((s) => {
          const message = s.data.message;
          this.$notify({ message: message, customClass: 'success' });
          this.$router.push({ name: 'D01_S04_CustomMaterialManagement' });
        })
        .catch((err) => this.$notify({ message: err?.response?.data?.message, customClass: 'error' }))
        .finally(() => (this.isLoading = false));
      this.isLoading = false;
    },
    delelteDoc() {
      this.modalConfirmDetele(() => {
        D01_S05_CustomMaterialCreationServices.delete(this.documentID)
          .then((s) => {
            const message = s.data.message;
            this.$notify({ message: message, customClass: 'success' });
            this.$router.push({ name: 'D01_S04_CustomMaterialManagement' });
          })
          .catch((err) => this.$notify({ message: err?.response?.data?.message, customClass: 'error' }))
          .finally(() => (this.isLoading = false));
        this.isLoading = true;
      });
    },
    modalConfirmDetele(callBack) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        props: { title: this.getMessage('MSFA0133'), params: { callBack } }
      };
    },
    // run event (api, code) on  modal when click submit (ok)
    onExece(e) {
      e.callBack();
      this.onCloseModal();
    },
    // close modal
    onCloseModal() {
      this.modalConfig = { ...this.modalConfig, isShowModal: false };
    },
    // event canncel btn
    cancelBtn() {
      this.back();
      // window.history.back();
    },

    dataTab1(e) {
      this.body = e;
    },

    showDeleteBtn() {
      // case 1 trường hợp nhận documentID;
      const docId = this.custom_document.document_id && this.type !== 'copy_id';
      const docIdCopy = this.custom_document.document_id && this.type === 'copy_id';
      const usage_id = this.custom_document.list_document_usage_id;
      const showBtn = usage_id === null ? true : false; // if  usage_id is null, rổng;
      if (docId) {
        return showBtn;
      } else if (docIdCopy) {
        return false;
      } else if (!docId && !this.documentIDCopy) {
        return false;
      }
    },
    // change Name text event
    changeNameEvent() {
      const docId = this.custom_document.document_id && this.type !== 'copy_id';
      const usage_id = this.custom_document.list_document_usage_id;
      const checkRequied = usage_id === null ? true : false;
      if (docId) {
        this.nameEvent = !checkRequied ? 'キャンセル' : '利用終了';

        if (this.disuse_flag === 0) {
          if (checkRequied) {
            this.nameEvent = '利用再開';
          }
        }
        if (this.disuse_flag === 1) {
          this.nameEvent = '利用終了';
        }
        if (this.disuse_flag === 0 && checkRequied) {
          this.nameEvent = 'キャンセル';
        }
      } else {
        this.nameEvent = 'キャンセル';
      }
    },
    checkEqual() {
      let {
        disease,
        document_contents,
        document_name,
        medical_subjects_group_cd,
        off_label_information_flag,
        product_cd,
        product_name,
        safety_information_flag
      } = this.oldBody;
      let productCd = product_cd ? product_cd : '';
      let productName = product_name ? product_name : '';
      let docContent = document_contents ? document_contents : '';
      // eslint-disable-next-line eqeqeq
      let offFlag = off_label_information_flag != undefined ? off_label_information_flag : '0';
      // eslint-disable-next-line eqeqeq
      let safeFlag = safety_information_flag != undefined ? safety_information_flag : '1';
      const checkEqual =
        this.$_.isEqual(
          {
            disease,
            document_contents: docContent,
            document_name,
            medical_subjects_group_cd,
            off_label_information_flag: offFlag.toString(),
            product_cd: productCd,
            product_name: productName,
            safety_information_flag: safeFlag.toString()
          },
          this.body
        ) && this.$_.isEqual(this.document_slideReq, this.document_slideReq_old);
      return checkEqual;
    },
    eventGroup() {
      switch (this.nameEvent) {
        case '利用再開':
          this.disuse_flag = 1;
          this.changeDisueFlagStatus(this.disuse_flag);
          break;
        case '利用終了':
          this.disuse_flag = 0;
          this.changeDisueFlagStatus(this.disuse_flag);
          break;
        default:
          if (this.checkEqual()) {
            this.cancelBtn();
          } else {
            this.modalConfirmRoute(() => {
              this.cancelBtn();
            });
          }
      }
    },
    // data to get active, if is active will show color blue
    getActive(index) {
      // eslint-disable-next-line eqeqeq
      return this.changetab == index ? 'active' : '';
    },
    // func check exits data if exits show popup
    checkDateWasEdit(filed) {
      const data = sessionStorage.getItem(filed);
      return data != null ? true : false;
    },
    // reload tab
    reloadActiveTab(goTab) {
      const activeTab = document.querySelectorAll('.navTabs--metarial li a');
      activeTab.forEach((s) => {
        if (s.id === `a${goTab}`) {
          s.classList.add('active');
        } else {
          s.classList.remove('active');
        }
      });
    },
    // change tab
    goTab(index) {
      this.changetab = index;
      this.reloadActiveTab(index);
    },
    // This is active tab when click
    activeTab(tab) {
      const tabIndex = tab ? tab : this.$route.query?.tab;
      this.changetab = tabIndex ? tabIndex : '1';
      const active = ' active';
      if (this.changetab > '0' && this.changetab <= '6') {
        const tabActive = document.querySelector('#a' + this.changetab);
        tabActive.classList.add('active');
      } else {
        this.$router.push({ name: 'NotFound' });
      }
      switch (tabIndex) {
        case '1':
          this.classActive.tab1 += active;
          break;
        case '2':
          this.classActive.tab2 += active;
          break;
        default:
          this.goTab(1);
          break;
      }
    },
    encodeData(obj) {
      // eslint-disable-next-line no-undef
      let strData = Buffer.from(JSON.stringify(obj), 'utf8').toString('base64');
      return strData;
    },
    // save data from tab 2 (FE)
    savePptAll(e) {
      this.document_slideReq = e;
    },
    // setup total data then call api save (BE)
    savePpt() {
      this.isLoading = true;
      this.isDisableSubmit = true;
      const tab2 = this.$refs['savePpt'];
      tab2.$refs['S05Editor'].saveContentPpt();

      setTimeout(() => {
        const array = this.document_slideReq.map((s, y) => {
          let arr = s.content && s.content.length > 0 ? s.content : [];
          const img = (index) => document.getElementById(`imgTab2${index}`);
          arr = typeof arr === 'object' ? arr : [];
          const new_content = arr.map((s) => ({ ...s }));
          const data = {
            ...s,
            link: img(y) && img(y).src ? this.dataURLtoFile(img(y)?.src, `imgTab2${y}.png`) : '',
            content: this.encodeData(new_content),
            page_num: y + 1
          };
          return data;
        });
        const slider = JSON.stringify(array);
        // const img = () => document.getElementById(`imgTab2${0}`);
        this.onOffEditPtt();
        const params = {
          ...this.body,
          document_custom_slides: slider
        };
        this.onSaveTotal(
          params,
          array.map((s) => s.link)
        );
      }, 1500);
    },
    // convert image src to file
    dataURLtoFile(dataurl, filename) {
      if (dataurl && dataurl.length > 0) {
        var arr = dataurl.split(','),
          mime = arr[0].match(/:(.*?);/)[1],
          bstr = atob(arr[1]),
          n = bstr.length,
          u8arr = new Uint8Array(n);

        while (n--) {
          u8arr[n] = bstr.charCodeAt(n);
        }

        return new File([u8arr], filename, { type: mime });
      } else {
        return null;
      }
    },
    // API* save or create a doc custom
    onSaveTotal(params, image) {
      let formData = new FormData();
      const key = Object.keys(params);
      for (let i = 0; i <= key.length; i++) {
        if (key[i] !== undefined) formData.append(key[i], Object.values(params)[i] ? encodeData(Object.values(params)[i]) : encodeData(''));
      }
      image.forEach((item, i) => {
        formData.append(`slide[${i}]`, item);
      });

      if (this.documentIDCopy && this.documentIDCopy !== 'create') {
        formData.append('type_create_copy', encodeData(1));
        formData.append('document_copy', encodeData(this.documentIDCopy));
        this.createDoc(formData); // tạo dưới dạng copy doc khác
      } else if (this.documentID) {
        this.onUpdateDoc(formData);
      } else if ((!this.documentIDCopy && !this.documentID) || this.documentIDCopy === 'create') {
        formData.append('type_create_copy', encodeData(0));
        formData.append('document_copy', encodeData(''));
        this.createDoc(formData); // tạo mới
      }
    },
    //API* update doc
    onUpdateDoc(params) {
      if (!this.checkValidate()) {
        this.$notify({ message: '入力エラーを確認してください。', customClass: 'error' });
        this.isDisableSubmit = false;
        this.isLoading = false;
        return;
      }
      const usage_id = this.custom_document.list_document_usage_id;
      const checkRequied = usage_id === null ? true : false;
      if (checkRequied) {
        this.updateDoc(params);
      } else {
        this.confirmToCreate(params);
      }
    },
    // MODAL //
    modalConfirmCreate(callBack) {
      this.modalConfig = {
        ...this.modalConfig,
        title: null,
        isShowModal: true,
        component: markRaw(this.$PopupConfirm),
        width: '35rem',
        props: { title: this.getMessage('MSF0131'), params: { callBack } }
      };
    },
    confirmToCreate(params) {
      this.modalConfirmCreate(() => {
        this.createDoc(params);
      });
    },
    confirmToDelete() {
      this.modalConfirmDetele(() => {
        this.delelteDoc();
      });
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
    },
    onOffEditPtt() {
      this.offEdit = true;
    }
  },

  match: {}
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.navTabs li a.active {
  background: #fff;
  color: #448add !important;
  font-weight: 700;
  pointer-events: none;
}
/** start css layout  **/
.groupMetarial {
  height: 100%;
  padding-top: 28px;
  background: #f2f2f2;
  @media (max-width: $viewport_tablet) {
    top: 16px;
  }
  .materialCreation {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .metarialTabs {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-top: 12px;
    .navTabs--metarial {
      li {
        .navInfo {
          @media (max-width: $viewport_desktop) {
            padding: 12px 8px;
          }
        }
        a {
          cursor: pointer;
          color: #99a5ae;
        }
        .navItem {
          @media (max-width: $viewport_tablet) {
            min-width: 15px;
            margin-right: 6px;
          }
        }
        .navItem {
          .svg {
            @media (max-width: $viewport_tablet) {
              width: 15px;
            }
          }
        }
      }
    }
    .width-tabs {
      @media (max-width: $viewport_tablet) {
        li {
          width: 240px;

          .navInfo {
            padding: 5px 2px !important;
          }
        }
      }
      li {
        width: 280px;
        a {
          padding: 0px !important;
        }
        .navInfo {
          padding: 10px 8px;
        }
      }
    }

    .tab-content {
      height: 100%;
      position: relative;
      overflow: hidden;
      border: 1px solid #e3e3e3;
      box-shadow: 2px 2px 5px rgba(145, 161, 171, 0.6);
      .tab-pane {
        position: relative;
        height: 87%;
      }
      .footer-pane {
        height: 13%;
        box-shadow: 0px 0px 5px #b7c3cb;
        position: relative;
        z-index: 2;
        bottom: 0px;
        background-color: #f2f2f2;
        .groupBtn {
          display: flex;
          padding: 10px 15px;
          align-items: center;
          justify-content: center;

          button {
            margin-top: 10px;
            width: 205px;
            margin-right: 30px;
          }
        }
      }
      @media (max-width: $viewport_tablet) {
        .tab-pane {
          height: 92%;
        }
        .footer-pane {
          height: 8%;
        }
      }
    }
  }
}
.event-none-cancel {
  pointer-events: none;
  background: #ffffff;
  color: #dfdedec7;
  border-color: #dfdedec7;
}
.event-none-submit {
  pointer-events: none;
  background: #90c3ff;
  color: #fff9f9c7;
  border-color: #90c3ff;
}
.inline-block {
  display: inline-block !important;
  pointer-events: none;
  color: #fff9f9c7;
}
.el-icon-loading {
  margin-right: 45px;
  display: none;
}
.btn-primary {
  position: relative;
  .textSubmit {
    margin-left: 5px;
    top: 0;
    right: 40%;
    justify-content: center;
    display: flex;
    height: 100%;
    position: absolute;
    align-items: center;
  }
}
</style>
