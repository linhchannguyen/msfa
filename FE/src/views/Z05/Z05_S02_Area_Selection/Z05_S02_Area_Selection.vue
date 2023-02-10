<template>
  <!-- 地区選択 -->
  <div :class="`modal-body-Z05S02 ${loadingPage ? 'pre-loader pre-minH' : 'pre-minH'}`">
    <div id="msfa-notify-Z05S02"></div>
    <Preloader v-if="loadingPage" />
    <div v-show="!loadingPage" class="form-z05-s02">
      <div class="block-area mb-3">
        <div
          v-for="region in listRegion"
          :key="region.region_cd"
          :class="region.selected ? 'active' : ''"
          class="block-area-item"
          @click="getPrefectureByRegions(region) || onActivePrefectureItem($event)"
        >
          <span>{{ region.region_name }}</span>
        </div>
      </div>

      <div v-if="hierarchySelected !== 'municipality'" class="mb-1">
        <div class="block">
          <li class="block-title bg-dark-custom cusor-default"><span>都道府県</span></li>
          <div :class="`s02-list-group-wrapper ${loadingPrefecture ? 'pre-loader h-390' : ''}`">
            <Preloader v-if="loadingPrefecture" />
            <div v-else ref="lstPrefectureScroll" class="s02-list-group-inner">
              <div class="s02-list-group">
                <li
                  v-for="item in listPrefectures"
                  :key="item.prefecture_cd"
                  :class="item.selected ? 'arrow-fill active' : prefectureSelected.prefecture_cd !== item.prefecture_cd ? 'arrow-fill' : ''"
                  class="s02-list-group-item s02-list-group-item-action block-item"
                  @click="onActivePrefectureItem"
                >
                  <div class="left" @click="setPerfecttureSelected(item.prefecture_cd)">
                    <span class="text">{{ item.prefecture_name }}</span>
                  </div>
                </li>
                <div v-if="listPrefectures.length === 0" class="noData">
                  <div class="noData-content">
                    <p class="noData-text">該当するデータがありません。</p>
                    <div class="noData-thumb">
                      <img src="@/assets/template/images/data/amico.svg" alt="" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="hierarchySelected === 'municipality'" class="row">
        <div class="col-sm-5 mb-2 parentModal">
          <div class="block">
            <li class="bg-dark-custom block-title cusor-default"><span>都道府県</span></li>
            <div :class="`s02-list-group-wrapper ${loadingPrefecture ? 'pre-loader h-390' : ''}`">
              <Preloader v-if="loadingPrefecture" />
              <div v-else ref="lstPrefectureScroll" class="s02-list-group-inner">
                <div class="s02-list-group">
                  <li
                    v-for="item in listPrefectures"
                    :key="item.prefecture_cd"
                    :class="item.selected ? 'arrow-fill active' : prefectureSelected.prefecture_cd !== item.prefecture_cd ? 'arrow-fill' : ''"
                    class="s02-list-group-item s02-list-group-item-action block-item"
                    @click="onActivePrefectureItem"
                  >
                    <div class="left" @click="setPerfecttureSelected(item.prefecture_cd) || getMunicipalityByPrefectures(item, false, false)">
                      <span class="text">{{ item.prefecture_name }}</span>
                    </div>
                  </li>

                  <div v-if="listPrefectures.length === 0 && !loadingPrefecture" class="noData">
                    <div class="noData-content">
                      <p class="noData-text">該当するデータがありません。</p>
                      <div class="noData-thumb">
                        <img src="@/assets/template/images/data/amico.svg" alt="" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-7 mb-2">
          <div class="block">
            <li class="block-title block-right-title bg-dark-custom block-title-search cusor-default">
              <div class="block-search-control">
                <div class="form-icon">
                  <el-form class="form-flex">
                    <label>市区町村</label>
                    <el-form-item>
                      <el-input
                        v-model="textSearch"
                        clearable
                        class="form-control form-control-search"
                        type="text"
                        :placeholder="Object.keys(prefectureSelected).length > 0 ? '市区町村入力' : ''"
                        :readonly="Object.keys(prefectureSelected).length > 0 ? false : true"
                      />
                    </el-form-item>
                    <div class="formSearch-btn">
                      <el-button
                        :disabled="Object.keys(prefectureSelected).length == 0"
                        class="btn-add btn btn-outline-primary btn-outline-primary--cancel btn-radius btn-search-muni"
                        @click="getMunicipalityByPrefectures(prefectureSelected, false, true)"
                      >
                        <i class="el-icon-search">
                          <span>検索</span>
                        </i>
                      </el-button>
                    </div>
                  </el-form>
                </div>
              </div>
            </li>
            <div :class="`s02-list-group-wrapper ${loadingMunicipality ? 'pre-loader h-390' : ''}`">
              <Preloader v-if="loadingMunicipality" />
              <div v-else ref="lstMunicipalityScroll" class="s02-list-group-inner-municipality s02-list-group-right">
                <div class="s02-list-group-municipality">
                  <li
                    v-for="item in listMunicipalitys"
                    :key="item.municipality_cd"
                    :class="item.selected ? 'active' : ''"
                    class="s02-list-group-item-municipality s02-list-group-item-action block-item"
                    @click="onActiveMunicipalityItem"
                  >
                    <div class="left" @click="setMunicipalitySelected(item.municipality_cd)">
                      <span class="text">{{ item.municipality_name }}</span>
                    </div>
                  </li>
                  <div v-if="listMunicipalitys.length === 0 && !loadingMunicipality" class="noData">
                    <div class="noData-content">
                      <p class="noData-text">該当するデータがありません。</p>
                      <div class="noData-thumb">
                        <img src="@/assets/template/images/data/amico.svg" alt="" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="block-btn">
        <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click.once="closeModal">キャンセル</button>
        <button type="button" class="btn btn-primary" :disabled="checkDisabledSubmit()" @click.once="returnData">決定</button>
      </div>
    </div>
  </div>
</template>

<script>
import Z05_S02_AreaSelectionService from '@/api/Z05/Z05_S02_AreaSelectionService';

export default {
  name: 'Z05_S02_Area_Selection',
  props: {
    // hierarchySelected = 'municipality' -> show municipality || not show
    hierarchySelected: {
      type: String,
      required: true,
      default: 'municipality'
    },

    // required when has municipalityCode
    prefectureCode: {
      type: String,
      required: false,
      default: ''
    },

    municipalityCode: {
      type: String,
      required: false,
      default: ''
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      loadingPage: false,
      loadingPrefecture: false,
      loadingMunicipality: false,
      textSearch: '',
      message: '',
      listRegion: [],
      listPrefectures: [],
      listMunicipalitys: [],

      areaSelected: {},
      prefectureSelected: {},
      municipalitySelected: {},

      paramsPrefectureOld: {},
      paramsMunicipalityOld: {}
    };
  },
  mounted() {
    this.getRegions();
  },
  methods: {
    // get list Region
    getRegions() {
      let params = {};
      this.loadingPage = true;
      Z05_S02_AreaSelectionService.getRegions(params)
        .then((res) => {
          this.listRegion = res?.data?.data;
          this.listRegion.forEach((x) => {
            x.selected = false;
          });

          this.setAreaSelected('');

          let item = this.listRegion.find((x) => x.region_cd === '');
          this.getPrefectureByRegions(item, true);
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S02', idNodeNotify: 'msfa-notify-Z05S02' });
        })
        .finally(() => {
          this.loadingPage = false;
          document.querySelectorAll('.s02-list-group-municipality .notation').forEach((el) => el.remove());
        });
    },

    // get list Prefecture By Region
    getPrefectureByRegions(item, loadDefault) {
      let params = {
        region_cd: item.region_cd
      };

      if (!this.isEqualObject(this.paramsPrefectureOld, params)) {
        this.paramsPrefectureOld = params;
        this.loadingPrefecture = true;
        this.loadingMunicipality = loadDefault ? (this.hierarchySelected === 'municipality' ? true : false) : false;

        this.textSearch = '';
        this.listPrefectures = [];
        this.listMunicipalitys = [];
        document.querySelectorAll('.s02-list-group-municipality .notation').forEach((el) => el.remove());

        Z05_S02_AreaSelectionService.getPrefectureByRegions(params)
          .then((res) => {
            this.loadingPrefecture = false;
            this.loadingMunicipality = false;
            this.listPrefectures = res.data.data;
            this.paramsMunicipalityOld = {};
            if (loadDefault) {
              if (this.prefectureCode) {
                this.setPerfecttureSelected(this.prefectureCode);
                if (this.hierarchySelected === 'municipality') {
                  let item = this.listPrefectures.find((x) => x.prefecture_cd === this.prefectureCode);
                  this.getMunicipalityByPrefectures(item, loadDefault, false);
                } else {
                  this.listMunicipalitys = [];
                  this.municipalitySelected = {};
                }
              }
            } else {
              this.setAreaSelected(item.region_cd);
              this.prefectureSelected = {};
              this.listMunicipalitys = [];
              this.municipalitySelected = {};
            }
          })
          .catch((err) => {
            this.loadingPrefecture = false;
            this.loadingMunicipality = false;
            this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S02', idNodeNotify: 'msfa-notify-Z05S02' });
          })
          .finally(() => {
            document.querySelectorAll('.s02-list-group-municipality .notation').forEach((el) => el.remove());
            if (!loadDefault && this.$refs.lstPrefectureScroll) {
              this.$refs.lstPrefectureScroll.scrollTop = 0;
            }
            this.onActivePrefectureItem(null, loadDefault);
          });
      }
    },

    // get list Municipality By Prefecture
    getMunicipalityByPrefectures(item, loadDefault, isSearch) {
      let params = {
        prefecture_cd: item.prefecture_cd,
        municipality_name: this.textSearch
      };
      if (!this.isEqualObject(this.paramsMunicipalityOld, params)) {
        if (!isSearch) {
          this.textSearch = '';
        } else {
          document.querySelectorAll('.s02-list-group-municipality .notation').forEach((el) => el.remove());
        }
        this.loadingMunicipality = true;

        let params = {
          prefecture_cd: item.prefecture_cd,
          municipality_name: this.textSearch
        };

        this.paramsMunicipalityOld = params;
        Z05_S02_AreaSelectionService.getMunicipalityByPrefectures(params)
          .then((res) => {
            this.listMunicipalitys = res?.data?.data;
            if (loadDefault) {
              if (this.municipalityCode) {
                this.setMunicipalitySelected(this.municipalityCode);
              }
            } else {
              this.setPerfecttureSelected(item.prefecture_cd);
              this.municipalitySelected = {};
            }
            this.loadingMunicipality = false;
          })
          .catch((err) => {
            this.loadingMunicipality = false;
            this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S02', idNodeNotify: 'msfa-notify-Z05S02' });
          })
          .finally(() => {
            if (!loadDefault && this.$refs.lstMunicipalityScroll) {
              this.$refs.lstMunicipalityScroll.scrollTop = 0;
            }
            this.onActiveMunicipalityItem(null, loadDefault);
          });
      } else {
        this.loadingMunicipality = false;
      }
    },

    // set area selected
    setAreaSelected(region_cd) {
      this.listRegion.forEach((element) => {
        if (element.region_cd === region_cd) {
          element.selected = true;
          this.areaSelected = element;
        } else {
          element.selected = false;
        }
      });
    },

    // set Perfectture selected
    setPerfecttureSelected(prefecture_cd) {
      if (prefecture_cd) {
        this.listPrefectures.forEach((element) => {
          if (element.prefecture_cd === prefecture_cd) {
            if (this.hierarchySelected === 'municipality') {
              element.selected = true;
              this.prefectureSelected = element;
              // document.querySelectorAll('.s02-list-group-municipality .notation').forEach((el) => el.remove());
            } else {
              element.selected = !element.selected;
              if (element.selected) {
                this.prefectureSelected = element;
              } else {
                this.prefectureSelected = {};
              }
            }
          } else {
            element.selected = false;
          }
        });
      } else {
        this.listPrefectures.forEach((element) => {
          element.selected = false;
          this.prefectureSelected = {};
        });
      }
    },

    // set Municipality selected
    setMunicipalitySelected(municipality_cd) {
      if (municipality_cd) {
        this.listMunicipalitys.forEach((element) => {
          if (element.municipality_cd === municipality_cd) {
            element.selected = !element.selected;
            if (element.selected) {
              this.municipalitySelected = element;
            } else {
              this.municipalitySelected = {};
            }
          } else {
            element.selected = false;
          }
        });
      } else {
        this.listPrefectures.forEach((element) => {
          element.selected = false;
          this.municipalitySelected = {};
        });
      }
    },

    isEqualObject(object1, object2) {
      const keys1 = Object.keys(object1);
      const keys2 = Object.keys(object2);
      if (keys1.length !== keys2.length) {
        return false;
      }
      for (let key of keys1) {
        if (object1[key] !== object2[key]) {
          return false;
        }
      }
      return true;
    },

    // check disable button 決定
    checkDisabledSubmit() {
      return Object.keys(this.prefectureSelected).length > 0 ? false : true;
    },

    onActivePrefectureItem(event, loadDefault) {
      // Remove all notations
      document.querySelectorAll('.s02-list-group .notation').forEach((el) => el.remove());

      let item = document.getElementsByClassName('s02-list-group-item active')[0] || event?.target?.closest('.s02-list-group-item.active');
      let itemHeight = item?.offsetHeight;

      if (item) {
        let listGroupEl = item?.closest('.s02-list-group');
        let listGroupInnerEl = listGroupEl?.closest('.s02-list-group-inner');
        let offset = item?.offsetTop - listGroupInnerEl?.scrollTop;

        if (loadDefault && this.$refs.lstPrefectureScroll) {
          this.$refs.lstPrefectureScroll.scrollTop = offset - itemHeight;
        }

        // Insert notation
        let beforeNotation = document.createElement('div');
        beforeNotation.classList.add('notation-before');
        beforeNotation.classList.add('notation');
        let afterNotation = document.createElement('div');
        afterNotation.classList.add('notation-after');
        afterNotation.classList.add('notation');
        beforeNotation.style.top = `${offset}px`;
        afterNotation.style.top = `${offset}px`;
        beforeNotation.style.height = `${itemHeight}px`;
        afterNotation.style.height = `${itemHeight}px`;
        listGroupEl?.prepend(beforeNotation);
        listGroupEl?.append(afterNotation);
        let height = listGroupInnerEl?.offsetHeight - itemHeight;
        this.checkScroll(beforeNotation, afterNotation, offset, height);

        // Scrolling notation
        listGroupInnerEl?.addEventListener('scroll', () => {
          offset = item?.offsetTop - listGroupInnerEl?.scrollTop;
          beforeNotation.style.top = `${offset}px`;
          afterNotation.style.top = `${offset}px`;
          height = listGroupInnerEl?.offsetHeight - itemHeight;
          this.checkScroll(beforeNotation, afterNotation, offset, height);
        });
      }
    },

    onActiveMunicipalityItem(event, isLoadDefault) {
      // Remove all notations
      document.querySelectorAll('.s02-list-group-municipality .notation').forEach((el) => el.remove());
      let item =
        document.getElementsByClassName('s02-list-group-item-municipality active')[0] || event?.target?.closest('.s02-list-group-item-municipality.active');

      let itemHeight = item?.offsetHeight || 0;
      if (item) {
        let listGroupEl = item?.closest('.s02-list-group-municipality');
        let listGroupInnerEl = listGroupEl?.closest('.s02-list-group-inner-municipality');
        let offset = item?.offsetTop - listGroupInnerEl?.scrollTop;
        if (isLoadDefault && this.$refs.lstMunicipalityScroll) {
          this.$refs.lstMunicipalityScroll.scrollTop = offset - 120;
        }

        // Insert notation
        let beforeNotation = document.createElement('div');
        beforeNotation.classList.add('notation-before');
        beforeNotation.classList.add('notation');
        let afterNotation = document.createElement('div');
        afterNotation.classList.add('notation-after');
        afterNotation.classList.add('notation');
        beforeNotation.style.top = `${offset}px`;
        afterNotation.style.top = `${offset}px`;
        beforeNotation.style.height = `${itemHeight}px`;
        afterNotation.style.height = `${itemHeight}px`;
        listGroupEl?.prepend(beforeNotation);
        listGroupEl?.append(afterNotation);
        let height = listGroupInnerEl?.offsetHeight - itemHeight;
        this.checkScroll(beforeNotation, afterNotation, offset, height);

        // Scrolling notation
        listGroupInnerEl?.addEventListener('scroll', () => {
          offset = item?.offsetTop - listGroupInnerEl?.scrollTop;
          beforeNotation.style.top = `${offset}px`;
          afterNotation.style.top = `${offset}px`;
          height = listGroupInnerEl?.offsetHeight - itemHeight;
          this.checkScroll(beforeNotation, afterNotation, offset, height);
        });
      }
    },

    checkScroll(beforeNotation, afterNotation, offset, height) {
      if (offset < 0 || offset > height) {
        beforeNotation.classList.add('invisible-notation');
        afterNotation.classList.add('invisible-notation');
      } else {
        beforeNotation.classList.remove('invisible-notation');
        afterNotation.classList.remove('invisible-notation');
      }
    },

    // return Data
    returnData() {
      let result = {
        screen: 'Z05_S02',
        prefectureSelected: this.prefectureSelected,
        municipalitySelected: this.municipalitySelected
      };
      this.$emit('onFinishScreen', result);
    },

    closeModal() {
      this.$emit('onFinishScreen');
    }
  }
};
</script>

<style lang="scss" scoped>
@import '@/assets/css/pages/Z05';
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
@mixin flexbox($align: center, $wrap: wrap, $justify: space-between) {
  display: flex;
  align-items: $align;
  flex-wrap: $wrap;
  justify-content: $justify;
}

.modal-body {
  background: #f6f6f6;
}

.block-area {
  @include flexbox(stretch);
  position: relative;

  .block-area-item {
    width: calc(25%);
    cursor: pointer;
    background: #ffffff;
    padding: 5px 10px;
    box-sizing: border-box;
    text-align: center;
    color: #5f6b73;
    outline: 1px solid #99a5ae;

    display: flex;
    align-items: center;
    justify-content: center;

    span {
      font-weight: normal;
      font-size: 16px;
    }

    &:hover {
      color: #448add;
      background: #eef6ff;
    }
  }

  .active {
    background: #eef6ff;
    color: #448add;
    outline: 1px solid #448add;
    box-shadow: inset 0 0 0 1px #448add;
    z-index: 1;
  }

  .block-area-item:nth-child(1) {
    border-top-left-radius: 4px;
  }
  .block-area-item:nth-child(4) {
    border-top-right-radius: 4px;
  }
  .block-area-item:nth-child(5) {
    border-bottom-left-radius: 4px;
  }
  .block-area-item:nth-child(8) {
    border-bottom-right-radius: 4px;
  }
}

// list
.block {
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  li {
    @include flexbox();
    list-style: none;
    padding: 0;
    margin: 0px;
    cursor: pointer;
    color: #5f6b73;
  }

  .block-title {
    border-radius: 8px 8px 0 0;
    border-bottom: none;
    font-weight: bold;
    font-size: 16px;
    padding: 16px 28px;
    border: 1px solid rgba(0, 0, 0, 0.125);
    background: #ffffff;
    box-shadow: 0px 2px 11px 2px #b7c3cb80;
    z-index: 1;
  }

  .block-right-title {
    background: #ffffff;
    @include flexbox(stretch, wrap, flex-start);
    flex-direction: column;
  }

  .block-title-search {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
    padding-right: 5px !important;
  }
}

.block-item {
  @include flexbox(stretch);
  .left {
    display: flex;
    flex-grow: 1;
    padding: 16px 0 16px 28px;
    .text {
      align-items: center;
      letter-spacing: 0.05em;
      color: #5f6b73;
      flex-grow: 1;
    }

    .block-image {
      margin-right: 14px;
    }
  }

  .right {
    padding: 16px 28px 16px 5px;
  }
}

.h-390 {
  height: 350px;
}

.s02-list-group-right {
  min-height: 100%;
}

.s02-list-group,
.s02-list-group-municipality {
  display: flex;
  flex-direction: column;
  padding-left: 0;
  margin-bottom: 0;
  height: 100%;

  .noData {
    height: 100%;
    .noData-content {
      height: 100%;
    }
    .noData-thumb {
      height: calc(100% - 40px);
    }
  }
}

.s02-list-group-wrapper {
  position: relative;
}

/* This element make children can overlap it */
.s02-list-group-inner,
.s02-list-group-inner-municipality {
  overflow-y: auto;
  overflow-x: hidden;
  height: 350px;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 0 0 8px 8px;
  background: #ffff;
}

.s02-list-group,
.s02-list-group-municipality {
  .notation-before {
    display: block;
    box-sizing: border-box;
    background-color: #eef6ff;
    border-left: 4px solid #448add;
    width: 10px;
    position: absolute;
    left: -5px;
    top: 0;
    box-shadow: -4px 0px 9px #999;
  }

  .notation-after {
    display: block;
    width: 6px;
    position: absolute;
    right: -5px;
    top: 0;
    background-color: #eef6ff;
    z-index: 1;
    box-shadow: 3px 0px 6px #999;
  }
}

.s02-list-group-item,
.s02-list-group-item-municipality {
  border: none;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
  border-top: none;
  &:first-child {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
  }
  &:last-child {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-radius: 0;
  }
}

.s02-list-group-item.active,
.s02-list-group-item-municipality.active {
  background: #eef6ff;
  color: #5f6b73;
  font-weight: bold;
  border-color: #e3e3e3;
  z-index: 2;
  box-shadow: 0px 0px 9px #999;
}

.h-452 {
  height: 452px !important;
}

.noData {
  text-align: center;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  height: auto;
  @media (max-width: $viewport_tablet) {
    height: inherit;
    padding: 30px 20px;
  }
  .noData-content {
    width: 100%;
  }
  .noData-text {
    color: #99a5ae;
  }
  .noData-thumb {
    margin-top: 20px;
  }
}

.bg-dark-custom {
  background: linear-gradient(180deg, rgba(65, 88, 111, 0.85) 6.77%, #41586f 36.46%) !important;
  box-shadow: 0px 3px 6px #e3e3e3 !important;
  color: #ffffff !important;
  cursor: default !important;

  label {
    color: #ffffff !important;
    font-weight: bold;
    width: 72px;
  }
}

.form-flex {
  gap: 5px;
  @include flexbox(center, nowrap, flex-start);
  .el-form-item {
    margin-bottom: 0;
    flex-grow: 1;
  }

  .form-control {
    padding: 0;
    border: none;
  }

  button:disabled {
    cursor: not-allowed;
    background: #e5e5e5;
  }
}

.form-control:disabled,
.form-control[readonly] {
  background: #e5e5e5;
}
.parentModal {
  position: relative;
}

.pre-minH {
  height: 570px;
}
.btn-search-muni {
  padding: 5px 8px;
}

.form-control-search[readonly] {
  background: #f3f3f3;
}

.formSearch-btn {
  width: 72px;
  .btn {
    padding: 0 10px;
    height: 32px;
    opacity: 1;
    &:disabled {
      background: #f3f3f3;
      cursor: not-allowed;
    }
    .btn-iconLeft {
      top: -2px;
    }
  }
}

@media (max-width: 1024px) {
  .modal-body-Z05S02 {
    margin: -10px;
  }
  .pre-minH {
    height: 100%;
  }

  .h-390 {
    height: 300px;
  }

  .s02-list-group-inner,
  .s02-list-group-inner-municipality {
    height: 300px;
  }
  .block-btn {
    padding-top: 3px;
  }
}
</style>
