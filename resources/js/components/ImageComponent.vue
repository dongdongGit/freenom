<template>
  <div class="main-content" v-loading="loading">
    <div class="container-fluid">
      <div class="breadcrumb-wrapper row">
        <div class="col-12 col-lg-3 col-md-6">
          <h4 class="page-title">图片</h4>
        </div>
        <div class="col-12 col-lg-9 col-md-6">
          <ol class="breadcrumb float-right">
            <li>
              <router-link to="/">首页</router-link>
            </li>
            <li class="active">/ 图片</li>
          </ol>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="card">
          <div class="card-header border-bottom">
            <el-button icon="el-icon-setting" type="primary" plain @click="action()">操作</el-button>
          </div>
          <div class="card-body">
            <div class="masonry">
              <div
                class="col-md-12 col-lg-12 col-xlg-12 masonry-item"
                v-for="(image, index) in images"
                :key="index"
              >
                <img class="img-fluid" :src="image.path" @click="popImage(index, image)">
              </div>
            </div>
            <paginate
              :data="images"
              :meta="meta"
              :url="this.GLOBAL.baseUri + 'admin/image'"
              @listen-paginate="getPaginate"
            ></paginate>
          </div>
        </div>
      </div>
    </div>
    <el-dialog title="图片" :visible.sync="dialog_visible" width="50%">
      <div class="row">
        <div class="col-md-12 col-xl-12">
          <div class="card the-card">
            <img class="img-fluid" :src="select_image.path">
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialog_visible = false">取 消</el-button>
        <el-button type="primary" @click="deleteImg()">删 除</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      images: [],
      meta: {
        count: 0,
        limit: 0,
        offset: 0,
        total: 0
      },
      loading: true,
      dialog_visible: false,
      select_image: {}
    };
  },
  created() {
    this.init();
  },
  watch: {
    $route: "init"
  },
  methods: {
    init() {
      var self = this;
      return axios
        .get(this.GLOBAL.baseUri + "admin/image")
        .then(function(response) {
          var data = response.data;
          if (data.code === 200) {
            self.images = data.data;
            self.meta = data.meta;
          }
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "加载失败",
            type: "error"
          });
        });
    },
    getPaginate(parse) {
      this.images = parse.data;
      this.meta = parse.meta;
    },
    action() {
      console.log("action");
    },
    popImage(index, image) {
      this.dialog_visible = true;
      this.select_image = image;
    },
    deleteImg() {
      var self = this;
      this.$confirm("确认删除？")
        .then(_ => {
          return axios
            .delete(self.GLOBAL.baseUri + "admin/image/" + self.select_image.id)
            .then(function(response) {
              var data = response.data;
              
              if (data.code === 200) {
                self.images.splice(self.images.indexOf(self.select_image), 1);
                self.dialog_visible = false
              }
            })
            .catch(function(error) {
              self.$message({
                showClose: true,
                message: "删除失败",
                type: "error"
              });
            });
        })
        .catch(_ => {});
    }
  }
};
</script>

<style scoped lang="scss">
@import "~@/admin/image";
</style>