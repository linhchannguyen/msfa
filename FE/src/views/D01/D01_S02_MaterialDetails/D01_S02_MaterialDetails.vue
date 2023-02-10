<template>
  <div v-loading-container="loading" class="box-materia">
    <div class="box-wrap">
      <div class="materia-box-header">
        <div class="materia-header">
          <div class="select-doccument" :class="activeDrdInput ? 'active2' : ''">
            <div style="cursor: pointer" class="title-header" @click="activeDrd">
              <div v-if="documentTileFrist?.document_type == '10' && documentTileFrist?.file_type == '10'" class="icon">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_pdf-manag.svg')" alt="" />
              </div>
              <div v-if="documentTileFrist?.document_type == '10' && documentTileFrist?.file_type == '20'" class="icon">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_film.svg')" alt="" />
              </div>
              <div v-if="documentTileFrist?.document_type == '20'" class="icon">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/images/icon_document_spanner_small.svg')" alt="" />
              </div>
              <div style="cursor: pointer" class="text">{{ documentTileFrist?.document_title }}</div>
              <div v-if="documentTile.length > 0" :class="activeDrdInput ? 'active3' : 'active4'" class="icon-drw">
                <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/D01-S02-icon2.svg')" alt="" />
              </div>
            </div>
            <div class="content-drw" :class="activeDrdInput ? 'active' : ''">
              <ul>
                <li
                  v-for="item of documentTile"
                  :key="item"
                  :class="documentTileFrist.document_id === item.document_id ? 'active-title' : ''"
                  @click="documentTileDetail(item)"
                >
                  {{ item.document_title }}
                </li>
              </ul>
            </div>
          </div>
          <div class="rate-header">
            <span>総合評価: {{ scoreValues.sum_review ? scoreValues.sum_review : '0' }}件</span>
            <div class="rate"><el-rate v-model="scoreValues.avg_review" allow-half disabled disabled-void-color="#dcdcdc" /></div>
            <span>利用状況: {{ scoreValues.count_situation ? scoreValues.count_situation : '0' }}回<span> </span></span>
          </div>
        </div>
      </div>
      <div class="box-tab">
        <div class="lstUserManage">
          <ul class="nav navTabs navTabs--userManage">
            <li>
              <a class="active" href="#MaterialProfile" role="tab" data-toggle="tab">
                <div ref="abc" class="navInfo" @click="tab(1)">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/iconD01-S02-1.svg')" alt="" />
                  </span>
                  資材プロフィール
                </div>
              </a>
            </li>
            <li>
              <a href="#Evaluation" role="tab" data-toggle="tab">
                <div class="navInfo" @click="tab(2)">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/iconD01-S02-2.svg')" alt="" />
                  </span>
                  評価
                </div>
              </a>
            </li>
            <li v-if="documentTileFrist.document_type === '10'">
              <a href="#CustomCase" role="tab" data-toggle="tab">
                <div class="navInfo" @click="tab(3)">
                  <span class="navItem">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/iconD01-S02-3.svg')" alt="" />
                  </span>
                  カスタム事例
                </div>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div id="MaterialProfile" role="tabpanel" class="tab-pane active">
              <D01S02TabMaterialProfile :changetab="changetab" :doccumentid="doccumentid" @changeLoading="changeLoading" />
            </div>
            <div id="Evaluation" role="tabpanel" class="tab-pane">
              <D01S02Evaluation
                :changetab="changetab"
                :doccumentid="doccumentid"
                :doccumenttype="doccumenttype"
                :is-popup="isPopup"
                @changeLoading="changeLoading"
              />
            </div>
            <div id="CustomCase" role="tabpanel" class="tab-pane">
              <D01S02CustomCase :changetab="changetab" :doccumentid="doccumentid" :is-popup="isPopup" @changeLoading="changeLoading" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import D01S02TabMaterialProfile from './D01_S02_TabMaterialProfile.vue';
import D01_S02_MaterialDetailsService from '../../../api/D01/D01_S02_MaterialDetailsService';
import D01S02Evaluation from './D01_S02_Evaluation.vue';
import D01S02CustomCase from './D01_S02_CustomCase.vue';
import { mapGetters } from 'vuex';
import _ from 'lodash';
export default {
  name: 'D01_S02_MaterialDetails',
  components: { D01S02TabMaterialProfile, D01S02CustomCase, D01S02Evaluation },
  props: {
    documentId: {
      type: Number,
      default: 0
    },
    checkLoading: [Boolean]
  },

  data() {
    return {
      loading: false,
      changetab: 0,
      activeDrdInput: false,
      documentTile: [],
      scoreValues: {},
      documentTileFrist: {},
      doccumentid: 0,
      propsDoccumentId: 0,
      doccumenttype: '',
      isPopup: false,
      doccument_id: ''
    };
  },
  computed: {
    ...mapGetters({
      historyParams: 'router/isHistoryParams'
    })
  },
  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        this.changetab = 1;
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'D01_S02_MaterialDetails') {
          this.doccument_id = toParams.document_id;
          this.$refs.abc.click();
          this.getListSearch();
        }
      }
    );
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '資材詳細',
      icon: 'Material-icon.svg',
      isShowBack: true
    });
    let { search } = window.location;
    localStorage.removeItem('checkChangTab');
    if (search.match('document_id=')) {
      this.isPopup = true;
      let doc_id = search.slice(1).split('&')[0].split('=')[1];
      this.propsDoccumentId = doc_id || '';
    } else {
      if (this._route('D01_S02_MaterialDetails')) {
        this.isPopup = false;
        this.propsDoccumentId = this._route('D01_S02_MaterialDetails') ? this._route('D01_S02_MaterialDetails').params.document_id : '';
      } else {
        let { document_id } = this.historyParams;
        this.isPopup = false;
        this.propsDoccumentId = document_id;
      }
    }
    this.getListSearch();
  },
  methods: {
    changeLoading(value) {
      this.loading = value;
    },
    getListSearch() {
      const data = {
        document_id: this.doccument_id ? this.doccument_id : this.propsDoccumentId
      };
      D01_S02_MaterialDetailsService.getListSearch(data).then((res) => {
        let dataRes = null;

        let { data } = res.data;
        if (data.document_title.length > 0) {
          data.document_title.forEach((element) => {
            // eslint-disable-next-line eqeqeq
            if (element.document_id == this.propsDoccumentId) {
              dataRes = element;
            }
          });
        }
        this.documentTile = data?.document_title;
        this.scoreValues = {
          ...data?.score_values,
          avg_review: data?.score_values.avg_review === '5.0' ? '5' : data?.score_values.avg_review
        };
        this.documentTileFrist = data?.document_title.document_id ? data?.document_title : dataRes;
        if (data?.document_title.document_id) {
          this.doccumentid = data?.document_title.document_id;
        } else {
          if (dataRes.document_id) {
            this.doccumentid = dataRes.document_id;
          } else {
            this.doccumentid = this.propsDoccumentId;
          }
        }
        this.doccumenttype = data?.document_title.document_id ? data?.document_title.document_type : dataRes.document_type;
      });
    },
    tab(index) {
      this.changetab = index;
    },
    activeDrd() {
      if (this.documentTile.length > 0) {
        this.activeDrdInput = !this.activeDrdInput;
      }
    },
    documentTileDetail(item) {
      this.documentTileFrist = {};
      this.documentTileFrist = item;
      this.doccumentid = item.document_id;
      this.$router.replace({ params: { document_id: item.document_id } });
      // this.$forceUpdate();
      // this.$router.go();
      this.activeDrd();
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
.navTabs--userManage {
  li {
    min-width: 310px;

    @media (max-width: $viewport_tablet) {
      min-width: inherit;
      width: 33.333333%;
    }
    a {
      cursor: pointer;
      color: #99a5ae;
      &:hover {
        text-decoration: none;
        color: #5f6b73;
        font-weight: bold;
      }
    }
    .navItem {
      @media (max-width: $viewport_tablet) {
        min-width: 15px;
        margin-right: 6px;
      }
    }
  }
}
.active-title {
  background: #eef6ff;
  color: #2d3033 !important;
  cursor: default;
}
.navTabs li a.active {
  background: #fff;
  color: #448add !important;
  font-weight: 700;
  pointer-events: none;
  transition: 0.5s;
}
</style>
