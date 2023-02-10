<template>
  <el-button type="primary" class="btn" :loading="processing" @click.prevent="handleSubmit">Element UI Loading</el-button>
  <!-- <el-button type="primary" class="btn" @click.prevent="testRoles">Test Roles</el-button> -->
  <hr />
  <!-- <button class="btn btn-primary spinner spinner-left">spinner-left</button>
  <button class="btn btn-primary spinner spinner-right">spinner-right</button>
  <button class="btn btn-primary spinner spinner-left spinner-sm">spinner-sm</button>
  <button class="btn btn-primary spinner spinner-left spinner-lg">spinner-lg</button>
  <button class="btn btn-primary spinner spinner-left spinner-white">spinner-white</button>
  <button class="btn btn-primary spinner spinner-left spinner-primary">spinner-primary</button>
  <button class="btn btn-primary spinner spinner-left spinner-danger">spinner-danger</button>
  <button class="btn btn-primary spinner spinner-left spinner-warning">spinner-warning</button>
  <button class="btn btn-primary spinner spinner-left spinner-dark">spinner-dark</button> <br />
  <button class="btn btn-primary" :class="{ 'spinner spinner-white spinner-left': processing }" :disabled="processing" @click.prevent="handleSubmit">
    Sample submit
  </button> -->
  <hr />
  <div><h4>Example: Extract Imgage from PDF</h4></div>
  <input id="file-upload" type="file" class="btn" />
  <div style="padding: 15px; border: 1px solid gray; position: relative" class="SvgContrainer">
    <div id="renderSvg"></div>

    <canvas v-for="(data, i) of 12" :id="'the-canvas' + (i + 1)" :key="i"></canvas>
    <div v-for="(data, i) of 12" :key="i" :class="'textLayer' + ' contentText' + (i + 1)"></div>

    <el-button v-show="object.file" type="primary" class="btn" @click="loadImage">Load Image</el-button>
  </div>
  <div style="padding: 5px">
    <table class="tableBox table_hover">
      <thead>
        <tr>
          <th>Heading 1</th>
          <th>Heading 2</th>
          <th>Heading 3</th>
          <th>Heading 4</th>
          <th>Heading 5</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id" :ref="`row-${user.id}`" :class="`${user.id === 2 || user.id === 4 ? 'background-e3e3e3' : ''} `">
          <td>{{ user.col1 }}</td>
          <td>{{ user.col2 }}</td>
          <td>{{ user.col3 }}</td>
          <td>{{ user.col4 }}</td>
          <td>{{ user.col5 }}</td>
          <td>
            <el-button
              v-if="false"
              class="btn btn-danger"
              :loading="deleteProcessing[user.id]"
              :disabled="deleteProcessing[user.id]"
              width="30"
              @click.prevent="handleDestroy(user.id)"
            >
              <!-- <span v-if="deleteProcessing[user.id]" class="spinner spinner-white spinner-center"></span> -->
              <!-- <span><img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_delete.svg')" alt="" /></span> -->
            </el-button>
            <!-- :class="{ 'spinner spinner-white spinner-left': deleteProcessing[user.id] }" -->
            <el-button class="btn btn-danger" :disabled="deleteProcessing[user.id]" @click.prevent="handleDestroy([1, 2, 3])"> 削除 </el-button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <hr />

  <hr />
  <div style="padding: 5px">
    <table class="tableBox tableCustom1">
      <thead>
        <tr>
          <th>Heading 1</th>
          <th>Heading 2</th>
          <th>Heading 3</th>
          <th>Heading 4</th>
          <th>Heading 5</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users1" :key="user.id" :ref="`row-1-${user.id}`" :class="`${user.id === 2 || user.id === 4 ? 'background-e3e3e3' : ''} `">
          <td>{{ user.col1 }}</td>
          <td>{{ user.col2 }}</td>
          <td>{{ user.col3 }}</td>
          <td>{{ user.col4 }}</td>
          <td>{{ user.col5 }}</td>
          <td>
            <!-- :class="{ 'spinner spinner-white spinner-left': deleteProcessing[user.id] }" -->
            <el-button class="btn btn-danger" :disabled="deleteProcessingUser1[user.id]" @click.prevent="handleDestroyUser1(user.id)"> 削除 </el-button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <hr />
  <div class="slide_down">
    <button id="btn-toggle" class="btn btn-primary btn-toggle" @click="clickToggle">Click Toggle</button>
    <div class="dailyNote content" style="display: block; width: 600px; margin-left: 10px">
      <p class="dailyNote-text">
        資料の洗い出しに先駆けて、前回講師をして頂いた鈴木先生にメールで前回資料に関するアドバイスを頂き、それを踏まえて洗い出しに臨みました。
      </p>
      <p class="dailyNote-text">その結果、洗い出しがスムーズに進み、田中先生にも安心して頂けました。</p>
    </div>
  </div>

  <hr />
  <div class="showNotificationheader dropdown-notif">
    <div class="list-notif list-notif-custom">
      <ul>
        <li v-for="notify in notifications" :key="notify.id" :ref="`item-${notify.id}`">
          <span class="custom-cursor">{{ notify.content }}</span>
          <button v-if="notify.status === 0" :class="`btn btn-${notify.id} `" @click.prevent="handleDestroyNotify(notify.id)">
            <img :class="`svg ${deleteNotiProcessing[notify.id] ? 'item-rotation-360' : ''}`" src="@/assets/template/images/archive-in.svg" alt="" />
          </button>
          <button v-else :class="`btn btn-${notify.id} brg`" @click.prevent="handleDestroyNotify(notify.id)">
            <img :class="`svg ${deleteNotiProcessing[notify.id] ? 'item-rotation-360' : ''}`" src="@/assets/template/images/archive-out.svg" alt="" />
          </button>
        </li>
      </ul>
    </div>
  </div>
  <div class="showNotificationheader dropdown-notif">
    <div class="list-notif list-notif-custom">
      <ul>
        <li
          v-for="notify in notificationsUpdate"
          :key="notify.id"
          :ref="`item-update-${notify.id}`"
          :class="`${notify.status === 1 ? 'background-e3e3e3' : ''} ${updateNotiSuccess[notify.id] ? 'updatedColorItem' : ''}`"
        >
          <span class="custom-cursor">{{ notify.content }}</span>
          <button v-if="notify.status === 0" :class="`btn btn-${notify.id} `" @click.prevent="handlUpdateNotify(notify.id)">
            <img :class="`svg ${updateNotiProcessing[notify.id] ? 'item-rotation-360' : ''}`" src="@/assets/template/images/archive-in.svg" alt="" />
          </button>
          <button v-else :class="`btn btn-${notify.id} brg`" @click.prevent="handlUpdateNotify(notify.id)">
            <img :class="`svg ${updateNotiProcessing[notify.id] ? 'item-rotation-360' : ''}`" src="@/assets/template/images/archive-out.svg" alt="" />
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.js';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.entry';

export default {
  name: 'Sample',
  data() {
    return {
      pdfFile: '',
      object: {
        file: null,
        docs: []
      },
      processing: false,
      deleteProcessing: {},
      deleteProcessingUser1: {},
      deleteNotiProcessing: {},

      updateNotiProcessing: {},
      updateNotiSuccess: {},
      users: [
        { id: 1, col1: 'Column1', col2: 'Column1', col3: 'Column1', col4: 'Column1', col5: 'Column1' },
        { id: 2, col1: 'Column2', col2: 'Column2', col3: 'Column2', col4: 'Column2', col5: 'Column2' },
        { id: 3, col1: 'Column3', col2: 'Column3', col3: 'Column3', col4: 'Column3', col5: 'Column3' },
        { id: 4, col1: 'Column4', col2: 'Column4', col3: 'Column4', col4: 'Column4', col5: 'Column4' },
        { id: 5, col1: 'Column5', col2: 'Column5', col3: 'Column5', col4: 'Column5', col5: 'Column5' }
      ],
      users1: [
        { id: 1, col1: 'Column1', col2: 'Column1', col3: 'Column1', col4: 'Column1', col5: 'Column1' },
        { id: 2, col1: 'Column2', col2: 'Column2', col3: 'Column2', col4: 'Column2', col5: 'Column2' },
        { id: 3, col1: 'Column3', col2: 'Column3', col3: 'Column3', col4: 'Column3', col5: 'Column3' },
        { id: 4, col1: 'Column4', col2: 'Column4', col3: 'Column4', col4: 'Column4', col5: 'Column4' },
        { id: 5, col1: 'Column5', col2: 'Column5', col3: 'Column5', col4: 'Column5', col5: 'Column5' }
      ],
      notifications: [
        { id: 1, content: 'Remand Approval Report 1', status: 0 },
        { id: 2, content: 'Remand Approval Report 2', status: 0 },
        { id: 3, content: 'Remand Approval Report 3', status: 0 },
        { id: 4, content: 'Remand Approval Report 4', status: 0 },
        { id: 5, content: 'Remand Approval Report 5', status: 0 }
      ],
      notificationsUpdate: [
        { id: 1, content: 'Remand Approval Report 1', status: 0 },
        { id: 2, content: 'Remand Approval Report 2', status: 0 },
        { id: 3, content: 'Remand Approval Report 3', status: 0 },
        { id: 4, content: 'Remand Approval Report 4', status: 0 },
        { id: 5, content: 'Remand Approval Report 5', status: 0 }
      ]
    };
  },
  mounted() {
    const btnToggle = document.querySelector('#btn-toggle');
    btnToggle.addEventListener('click', function () {
      const _this = this;
      _this.parentNode.classList.toggle('show');
    });
    this.extractImgPDF();
  },
  methods: {
    extractImgPDF() {
      pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;
      const fileUpload = document.getElementById('file-upload');
      // import 1 file pdf
      fileUpload.addEventListener('change', (event) => {
        const file = event.target.files[0];
        const fileReader = new FileReader();
        const state = { file, docs: [] };
        // Extract image from pdf
        fileReader.onload = function () {
          var typedarray = new Uint8Array(this.result);
          var pages = [];
          state.docs.push({ name: file.name, pages });
          const loadingTask = pdfjsLib.getDocument(typedarray);
          loadingTask.promise.then((pdf) => {
            for (let p = 1; p <= pdf.numPages; p++) {
              const pageInfo = {
                number: p,
                name: file.name + '-' + p,
                images: [],
                svg: {}
              };
              pages.push(pageInfo);
              pdf.getPage(p).then((page) => {
                const canvas = document.getElementById('the-canvas' + p);
                const ctx = canvas.getContext('2d');
                page.getOperatorList().then(function (ops) {
                  const fns = ops.fnArray;
                  const args = ops.argsArray;

                  let imgsFound = 0;
                  args.forEach((arg, i) => {
                    //Not a JPEG resource:
                    if (fns[i] !== pdfjsLib.OPS.paintJpegXObject) {
                      return;
                    }
                    imgsFound++;
                    const imgKey = arg[0],
                      imgInfo = {
                        name: pageInfo.name + '-' + imgsFound + '.jpg',
                        url: ''
                      };
                    pageInfo.images.push(imgInfo);
                    page.objs.get(imgKey, (img) => {
                      imgInfo.url = img.src;
                    });
                  });
                });
                //Full SVG:

                // Get viewport (dimensions)
                const scale = 1.5;
                const viewport = page.getViewport({ scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                pageInfo.svg = {
                  w: 2880,
                  h: 1620,
                  doc: '',
                  contentText: ''
                };
                var renderContext = {
                  canvasContext: ctx,
                  viewport: viewport
                };
                const renderTask = page.render(renderContext);
                renderTask.promise
                  .then(() => {
                    return page.getTextContent();
                  })
                  .then((textContent) => {
                    const contentText = document.querySelector('.contentText' + p);
                    contentText.style.left = canvas.offsetLeft + 'px';
                    contentText.style.top = canvas.offsetTop + 'px';
                    contentText.style.height = canvas.offsetHeight + 'px';
                    contentText.style.width = canvas.offsetWidth + 'px';
                    pdfjsLib.renderTextLayer({
                      textContent: textContent,
                      container: contentText,
                      viewport: viewport,
                      textDivs: []
                    });
                  });
                // SVG rendering by PDF.js
                page
                  .getOperatorList()
                  .then((opList) => {
                    var svgGfx = new pdfjsLib.SVGGraphics(page.commonObjs, page.objs);
                    return svgGfx.getSVG(opList, viewport);
                  })
                  .then((svg) => {
                    pageInfo.svg.doc = svg;
                  });
              });
            }
          });
        };
        // result data
        this.object = state;
        // read file
        fileReader.readAsArrayBuffer(file);
      });
    },
    loadImage() {
      try {
        this.extractImgPDF();
        const idRender = document.getElementById('renderSvg');
        idRender.innerText = 'File: ' + this.object.file.name;
        const div = document.createElement('div');
        const title = document.createElement('div');
        for (let i = 0; i < this.object.docs.length; i++) {
          if (this.object.docs[i].pages.length > 0) {
            for (let y = 0; y < this.object.docs[i].pages.length; y++) {
              const page = this.object.docs[i].pages[y];
              title.innerText = y + 1 + '-Image: ' + 'Slide' + page.number;
              div.appendChild(title);
              div.appendChild(page?.svg?.doc);
            }
          } else {
            title.innerText = 'Image: No';
            div.appendChild(title);
          }
        }
        idRender.appendChild(div);
      } catch (err) {
        // console.log(err);
      }
    },
    handleSubmit() {
      this.processing = true;
      setTimeout(() => {
        this.processing = false;
        this.$notify({ message: 'Api response success', customClass: 'success' });
      }, 1000);
    },
    handleDestroy(id) {
      this.$msgboxConfirm({
        message: 'Do you want to delete this record?',
        loading: true,
        beforeClose: (action, instance, done) => {
          instance.confirmButtonLoading = true;
          if (action === 'confirm') {
            id.forEach((e) => {
              const itemAction = this.$refs[`row-${e}`];
              this.deleteProcessing = { [id]: e };

              setTimeout(() => {
                this.$notify({ message: 'Api response success', customClass: 'success' });
                instance.confirmButtonLoading = false;
                this.deleteProcessing = {};
                this.destroy(itemAction);
                done();
              }, 3000);
            });
          } else {
            done();
          }
        }
      });
    },
    handleDestroyUser1() {
      this.$msgboxConfirm({
        message: 'Do you want to delete this record?',
        loading: true,
        beforeClose: (action, instance, done) => {
          instance.confirmButtonLoading = true;
          if (action === 'confirm') {
            instance.cancelButtonClass = 'el-button--disabled';
          } else {
            done();
          }
        }
      });
    },
    async handleDestroyNotify(id) {
      this.deleteNotiProcessing = { [id]: id };
      await new Promise((resolve) => setTimeout(resolve, 1000));

      this.deleteProcessing = {};
      const itemAction = this.$refs[`item-${id}`];
      this.$notify({ message: 'Api response success', customClass: 'success' });

      this.notifications = this.notifications.map((item) => {
        if (item.id === id) {
          return { ...item, status: 1 };
        } else return item;
      });

      this.destroySuccess(itemAction);
    },
    async handlUpdateNotify(id) {
      // new code
      this.updateNotiProcessing = { [id]: id };

      await new Promise((resolve) => setTimeout(resolve, 1000));

      this.updateNotiProcessing = {};

      this.updateNotiSuccess = { [id]: id };

      const itemAction = this.$refs[`item-update-${id}`];

      this.$notify({ message: 'Api response success', customClass: 'success' });

      this.notificationsUpdate = this.notificationsUpdate.map((item) => {
        if (item.id === id) {
          return { ...item, status: item.status === 0 ? 1 : 0 };
        } else return item;
      });

      this.updateSuccess(itemAction);
    },

    destroy(itemAction) {
      itemAction.classList.toggle('deleted');
      itemAction.style.background = '#b7d4ff';
      itemAction.addEventListener('transitionend', () => {
        itemAction.style.display = 'none';
      });
    },
    async destroySuccess(itemAction) {
      // new code
      await new Promise((resolve) => setTimeout(resolve, 1000));
      itemAction[0].classList.toggle('deleted');
      itemAction[0].style.background = '#b7d4ff';
      itemAction[0].addEventListener('transitionend', () => {});
      await new Promise((resolve) => setTimeout(resolve, 1000));
      itemAction[0].style.display = 'none';
    },
    async updateSuccess(itemAction) {
      // New code
      await new Promise((resolve) => setTimeout(resolve, 1000));
      itemAction[0].classList.toggle('updatedColorItem');
      await new Promise((resolve) => setTimeout(resolve, 0));
      itemAction[0].classList.remove('updatedColorItem');
      this.updateNotiSuccess = {};
    }
  }
};
</script>

<style lang="scss" scoped>
@import '@/assets/template/scss/sample.scss';

.btn {
  margin: 5px;
}
span.spinner {
  width: 1.3rem;
  height: 1.3rem;
  display: inline-block;
}
.spinner {
  position: relative;
  &:before {
    content: '';
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    position: absolute;
    top: 50%;
    left: 0;
    border-radius: 50%;
    border: 2px solid #d1d3e0;
    border-right: 2px solid transparent;
    width: 1.3rem;
    height: 1.3rem;
    margin-top: -0.75rem;
    -webkit-animation: animation-spinner 0.5s linear infinite;
    animation: animation-spinner 0.5s linear infinite;
  }
}
.spinner.spinner-center:before {
  left: 50%;
  margin-left: -0.75rem;
}
.spinner.spinner-left:before {
  right: auto;
  left: 1rem;
}
.spinner.spinner-right:before {
  left: auto;
  right: 1rem;
}
.spinner.spinner-sm:before {
  width: 1.25rem;
  height: 1.25rem;
  margin-top: -0.625rem;
}
.spinner.spinner-sm.spinner-center:before {
  left: 50%;
  margin-left: -0.625rem;
}
.spinner.spinner-sm.spinner-left:before {
  right: auto;
}
.spinner.spinner-sm.spinner-right:before {
  left: auto;
}
.spinner.spinner-lg:before {
  width: 2rem;
  height: 2rem;
  margin-top: -1rem;
}
.spinner.spinner-lg.spinner-center:before {
  left: 50%;
  margin-left: -1rem;
}
.spinner.spinner-lg.spinner-left:before {
  right: auto;
}
.spinner.spinner-lg.spinner-right:before {
  left: auto;
}
.spinner.spinner-primary:before {
  border: 2px solid #3699ff;
  border-right: 2px solid transparent;
}
.spinner.spinner-secondary:before {
  border: 2px solid #e4e6ef;
  border-right: 2px solid transparent;
}
.spinner.spinner-success:before {
  border: 2px solid #1bc5bd;
  border-right: 2px solid transparent;
}
.spinner.spinner-info:before {
  border: 2px solid #8950fc;
  border-right: 2px solid transparent;
}
.spinner.spinner-warning:before {
  border: 2px solid #ffa800;
  border-right: 2px solid transparent;
}
.spinner.spinner-danger:before {
  border: 2px solid #f64e60;
  border-right: 2px solid transparent;
}
.spinner.spinner-light:before {
  border: 2px solid #f3f6f9;
  border-right: 2px solid transparent;
}
.spinner.spinner-dark:before {
  border: 2px solid #181c32;
  border-right: 2px solid transparent;
}
.spinner.spinner-white:before {
  border: 2px solid #fff;
  border-right: 2px solid transparent;
}
.spinner.spinner-right {
  padding-right: 3rem;
}
.spinner.spinner-right.spinner-sm {
  padding-right: 3rem;
}
.spinner.spinner-right.spinner-lg {
  padding-right: 4rem;
}
.spinner.spinner-left {
  padding-left: 3rem;
}
.spinner.spinner-left.spinner-sm {
  padding-left: 3rem;
}
.spinner.spinner-left.spinner-lg {
  padding-left: 4rem;
}
.spinner-grow {
  vertical-align: sub;
}
.spinner-border {
  vertical-align: sub;
}

@-webkit-keyframes animation-spinner {
  to {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@keyframes animation-spinner {
  to {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

.item-rotation-360 {
  display: inline-block;
  animation: animation-item-rotation-360 0.75s linear infinite;
}

@keyframes animation-item-rotation-360 {
  to {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

.dailyNote {
  background: #b7d4ff;
  border-radius: 4px;
  padding: 7px 12px;
  margin-top: 23px;
  color: #225999;
  position: relative;
  &::after {
    position: absolute;
    top: -12px;
    left: 60px;
    content: '';
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 12px solid #b7d4ff;
  }
}

.brg {
  background: #448add !important;
}

.background-e3e3e3 {
  background: #e3e3e3;
}

.right-to-left {
  animation-name: bg-slide-right-to-left;
  animation-duration: 1s;
  animation-fill-mode: both;
}
.left-to-right {
  animation-name: bg-slide-right-to-left;
  animation-duration: 1s;
  animation-fill-mode: alternate;
}

@keyframes bg-slide-right-to-left {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}
</style>
