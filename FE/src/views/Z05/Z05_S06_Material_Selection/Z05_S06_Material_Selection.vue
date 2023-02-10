<template>
  <!-- 品目選択 -->
  <!-- maxWidth: mode === 'single' ?  33rem' :  '42rem' -->

  <div :class="`modal-body-Z05S06 ${mode === 'single' ? '' : 'pr-0'}`">
    <div id="msfa-notify-Z05S06"></div>
    <div :class="mode === 'single' ? 'm-0' : ''" class="row mb-3">
      <div :class="mode === 'single' ? 'secsion-single' : 'col-sm-6 pt-2 pr-0'">
        <div class="block-select">
          <div>
            <p class="mb-1">カテゴリ</p>
            <el-select v-model="categorySelected" class="selectpicker select-control form-control-select" placeholder="未選択" @change="getDataByFilter()">
              <el-option
                v-for="category in listCategories"
                :key="category.definition_value"
                :label="category.definition_label"
                :value="category.definition_value"
              >
                {{ category.definition_label }}
              </el-option>
            </el-select>
          </div>
          <div>
            <p class="mb-1">品目分類</p>
            <el-select
              v-model="classificationSelected"
              class="selectpicker select-control form-control-select"
              placeholder="未選択"
              @change="getDataByFilter()"
            >
              <el-option
                v-for="classifi in listClassifications"
                :key="classifi.product_group_cd"
                :label="classifi.product_group_name"
                :value="classifi.product_group_cd"
              >
                {{ classifi.product_group_name }}
              </el-option>
            </el-select>
          </div>
        </div>

        <div class="mt20">
          <div class="block">
            <div :class="`s06-list-group-wrapper ${loadingPage ? 'pre-loader h-340' : ''}`">
              <Preloader v-if="loadingPage" />
              <div v-else ref="lstProductScroll" class="s06-list-group-inner bd-radius">
                <div class="s06-list-group">
                  <li
                    v-for="item in lstProduct"
                    :key="item.product_cd"
                    :class="mode === 'single' ? (item.selected ? 'active' : '') : 'cusor-default'"
                    class="s06-list-group-item s06-list-group-item-action block-item"
                    @click="onActiveMaterialItem"
                  >
                    <div class="left">
                      <div class="item-name" @click="mode === 'single' ? setDataSelected(item.product_cd) : ''">
                        <span class="text">{{ item.product_name }}</span>
                      </div>
                    </div>
                    <div v-if="mode !== 'single'" class="right">
                      <el-button
                        class="btn-add btn-add-w btn btn-outline-primary btn-outline-primary--cancel"
                        :disabled="checkDataSelected(item)"
                        @click="onAddItem(item)"
                      >
                        <i class="el-icon-plus">
                          <span>{{ checkDataSelected(item) ? '追加済' : '追加' }}</span>
                        </i>
                      </el-button>
                    </div>
                  </li>
                  <div v-if="lstProduct.length === 0 && !loadingPage" class="noData">
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

      <div v-if="mode !== 'single'" class="col-sm-6 pr-0 block-r">
        <div class="block block-multi h100">
          <li class="block-title block-title-custom"><span>選択リスト</span></li>
          <div :class="`s06-list-group-wrapper h100-60 ${loadingSelected ? 'pre-loader ' : ''}`">
            <Preloader v-if="loadingSelected" />
            <div v-else class="s06-list-group-inner list-right h100">
              <div class="s06-list-group h100">
                <li v-for="item in dataSelected" :key="item.product_cd" class="s06-list-group-item s06-list-group-item-action block-item cusor-default">
                  <div class="left">
                    <span class="text">{{ item.product_name }}</span>
                  </div>
                  <div class="right">
                    <el-button class="btn-circle" @click="onRemoveItem(item)">
                      <i class="el-icon-close"></i>
                    </el-button>
                  </div>
                </li>
                <div v-if="dataSelected.length === 0" class="noData">
                  <div class="noData-content">
                    <p class="noData-text">品目を選択して下さい。</p>
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
      <button type="button" class="btn btn-outline-primary mr-3 btn-outline-primary--cancel" @click="closeModal">キャンセル</button>
      <button type="button" class="btn btn-primary" :disabled="checkDisabledSubmit()" @click="returnData">決定</button>
    </div>
  </div>
</template>

<script>
import Z05_S06_MaterialSelectionService from '@/api/Z05/Z05_S06_MaterialSelectionService';

export default {
  name: 'Z05_S06_Material_Selection',
  props: {
    mode: {
      type: String,
      required: true,
      default: 'single'
    },

    categoryCode: {
      type: String,
      required: false,
      default: '10'
    },

    classificationCode: {
      type: String,
      required: false,
      default: ''
    },

    date: {
      type: String,
      required: false,
      default: ''
    },

    initDataCodes: {
      type: Array,
      required: false,
      default() {
        return [];
      }
    }
  },
  emits: { onFinishScreen: null },

  data() {
    return {
      loadingPage: false,
      loadingSelected: false,
      listCategories: [],
      listClassifications: [],
      lstProduct: [],

      categorySelected: '',
      classificationSelected: '',
      dataSelected: []
    };
  },

  mounted() {
    document.querySelectorAll('.notation').forEach((el) => el.remove());
    this.getDataScreen();
  },

  methods: {
    // get data initial
    getDataScreen() {
      this.loadingPage = true;
      this.loadingSelected = true;
      this.listCategories = [];
      this.listClassifications = [];
      this.dataSelected = [];
      Z05_S06_MaterialSelectionService.getDataScreen()
        .then((res) => {
          this.listCategories = res?.data?.data?.variable || [];
          this.listClassifications = res?.data?.data?.product_group;
          if (this.listCategories.length > 0) {
            this.categorySelected = this.categoryCode ? this.categoryCode : '10';
            this.classificationSelected = this.classificationCode ? this.classificationCode : '';
          }

          if (this.categorySelected || this.classificationSelected) {
            this.getDataByFilter(true);
          }
        })
        .catch((err) => {
          this.loadingPage = false;
          this.loadingSelected = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S06', idNodeNotify: 'msfa-notify-Z05S06' });
        })
        .finally(() => {});
    },

    // Load data by filter
    getDataByFilter(isLoadDefault) {
      let params = {
        product_group_cd: this.classificationSelected,
        definition_value: this.categorySelected
      };
      this.loadingPage = true;
      Z05_S06_MaterialSelectionService.getDataByFilter(params)
        .then((res) => {
          this.lstProduct = res?.data?.data;
          this.loadingPage = false;
          this.loadingSelected = false;

          if (isLoadDefault) {
            if (this.initDataCodes.length > 0) {
              this.lstProduct.forEach((element) => {
                if (this.mode === 'single') {
                  element.selected = false;
                  if (element.product_cd === this.initDataCodes[0]) {
                    this.dataSelected.push(element);
                    element.selected = true;
                  }
                } else {
                  if (this.initDataCodes.some((e) => e === element.product_cd)) {
                    this.dataSelected.push(element);
                  }
                }
              });
            }
          } else {
            if (this.mode === 'single') {
              this.dataSelected = [];
              this.lstProduct.forEach((element) => {
                element.selected = false;
              });
            }
          }
        })
        .catch((err) => {
          this.loadingPage = false;
          this.loadingSelected = false;
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S06', idNodeNotify: 'msfa-notify-Z05S06' });
        })
        .finally(() => {
          if (!isLoadDefault && this.$refs.lstProductScroll) {
            this.$refs.lstProductScroll.scrollTop = 0;
          }
          this.onActiveMaterialItem(null, isLoadDefault);
        });
    },

    // selected data
    // mode single
    setDataSelected(product_cd) {
      this.dataSelected = [];
      this.lstProduct.forEach((element) => {
        if (element.product_cd === product_cd) {
          element.selected = !element.selected;
          if (element.selected) {
            this.dataSelected.push(element);
          } else {
            this.dataSelected = [];
          }
        } else {
          element.selected = false;
        }
      });
    },

    // mode multi
    // Check data
    checkDataSelected(item) {
      let index = this.dataSelected.findIndex((x) => x.product_cd === item.product_cd);

      return index > -1 ? true : false;
    },

    onAddItem(item) {
      this.dataSelected.push(item);
    },

    onRemoveItem(item) {
      let index = this.dataSelected.findIndex((x) => x.product_cd === item.product_cd);
      this.dataSelected.splice(index, 1);
    },

    // register item  // delete function (no used)
    register(item) {
      let params = {
        product_cd: item.product_cd,
        status_type: item.select_product
      };
      Z05_S06_MaterialSelectionService.register(params)
        .then(() => {
          this.getDataByFilter(false);
        })
        .catch((err) => {
          this.notifyModal({ message: err.response.data.message, type: 'error', classParent: 'modal-body-Z05S06', idNodeNotify: 'msfa-notify-Z05S06' });
        })
        .finally(() => {
          this.loadingPage = false;
        });
    },

    // check disable button 決定
    checkDisabledSubmit() {
      return this.dataSelected.length > 0 ? false : true;
    },

    onActiveMaterialItem(event = null, loadDefault) {
      // Remove all notations
      document.querySelectorAll('.notation').forEach((el) => el.remove());
      let item = document.getElementsByClassName('s06-list-group-item active')[0] || event?.target?.closest('.s06-list-group-item.active');

      let itemHeight = item?.offsetHeight || 0;
      if (item) {
        let listGroupEl = item?.closest('.s06-list-group');
        let listGroupInnerEl = listGroupEl?.closest('.s06-list-group-inner');
        let offset = item?.offsetTop - listGroupInnerEl?.scrollTop;

        if (loadDefault && this.$refs.lstProductScroll) {
          this.$refs.lstProductScroll.scrollTop = offset - itemHeight;
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
      let classificationFinals = [];
      this.dataSelected.forEach((x) => {
        if (!classificationFinals.some((e) => e === x.product_group_cd)) {
          classificationFinals.push(x.product_group_cd);
        }
      });
      let length = classificationFinals.length;
      let category = length > 1 ? '10' : this.categorySelected;
      let classification = length > 1 ? '' : this.classificationSelected;
      let result = {
        screen: 'Z05_S06',
        dataSelected: this.dataSelected,
        category: this.listCategories.find((x) => x.definition_value === category),
        classifications: this.listClassifications.find((x) => x.product_group_cd === classification)
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
@mixin flexbox($align: center, $wrap: wrap, $justify: space-between) {
  display: flex;
  align-items: $align;
  flex-wrap: $wrap;
  justify-content: $justify;
}

.modal-body {
  border-radius: 10px;
  padding: 5px;
}

.el-select-dropdown__item.selected {
  font-weight: normal;
  color: #2d3033;
  background: #eef6ff;
}

.secsion-single {
  flex-grow: 1;
}

// list
.block {
  border-radius: 8px;
  position: relative;
  li {
    @include flexbox(center, nowrap);
    list-style: none;
    padding: 0;
    margin: 0px;
    color: #5f6b73;
  }

  .block-title {
    border-bottom: none;
    font-weight: bold;
    font-size: 16px;
    border: none;
    padding: 10px 20px;
    background: transparent;
    color: #5f6b73;
  }

  .block-title-custom {
    padding: 0 20px 10px 20px !important;
    min-height: 20px;
  }
}

.block-multi {
  .s06-list-group {
    border-radius: 8px 0 0 8px;
    max-height: 402px;
  }
  .block-item {
    .left {
      @include flexbox();

      flex-grow: 1;
      padding: 10px 5px 10px 20px;
      .text {
        align-items: center;
        letter-spacing: 0.05em;
        color: #5f6b73;
        flex-grow: 1;
      }
    }

    .right {
      padding: 10px 20px 10px 5px;
      flex-grow: unset;
    }
  }
}

.block-item {
  @include flexbox(stretch);
  .left {
    @include flexbox(center, nowrap, flex-start);
    flex-grow: 1;
    padding: 0;

    .item-btn {
      padding: 10px 0 10px 20px;
    }

    .item-name {
      padding: 15px 5px 20px 20px;
      flex-grow: 1;
    }
  }

  .right {
    padding: 10px 20px 10px 5px;
  }
}

.block-r {
  right: -17px;
}

.s06-list-group-item + .s06-list-group-item.active {
  margin-top: unset;
}

// btn
.block-btn {
  padding-top: 0;
  text-align: center;

  .btn {
    width: 180px;
  }
}

.el-button {
  min-height: 30px;
}

.btn-add {
  justify-content: center;
  align-items: center;
  padding: 0px 12px;
  height: 32px;
  background: #ffffff;
  color: #448add;
  border: 1px solid #448add;
  box-sizing: border-box;
  border-radius: 24px;
  font-size: 15px;
  letter-spacing: 0.03em;
  font-weight: bold;
  &.is-disabled {
    color: var(--el-button-disabled-font-color);
    border-color: var(--el-button-disabled-border-color);
  }

  i {
    font-weight: bold;
    span {
      margin-left: 5px;
    }
  }
}

.btn-circle {
  padding: 0;
  background: inherit;
  border-radius: 100%;
  color: #99a5ae;
  box-shadow: 0px 4px 5px rgba(26, 58, 105, 0.1), 0px 1px 10px rgba(26, 58, 105, 0.1), 0px 2px 4px rgba(26, 58, 105, 0.1);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
  i {
    font-weight: bold;
    font-size: 15px;
    margin-top: 2px;
  }
  .el-icon-close:before {
    color: unset;
  }
}

.list-right {
  border-radius: 8px 0 0 8px;
  height: 100%;
}
.h-340 {
  height: 350px;
}
.h100-60 {
  height: calc(100% - 40px);
}

.h100 {
  height: 100% !important;
}

.modal-body-Z05S06 {
  margin: -10px 0 -10px -10px;
}

.block-select {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}

.s06-list-group-inner:not(.h100) {
  height: 350px;
}

.block-title-custom {
  height: 40px;
}
.mt20 {
  margin-top: 20px;
}
</style>
