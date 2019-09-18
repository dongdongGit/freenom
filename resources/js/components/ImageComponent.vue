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
            <el-dropdown split-button type="primary" @command="handleCommand">
              <span class="el-dropdown-link">
                <i class="el-icon-setting"></i> 操作
              </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="upload">上传</el-dropdown-item>
                <el-dropdown-item command="edit">编辑</el-dropdown-item>
                <el-dropdown-item command="select_all">全选</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
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
              :url="'/admin/image'"
              @listen-paginate="getPaginate"
            ></paginate>
          </div>
        </div>
      </div>
    </div>
    <!-- image -->
    <el-dialog title="图片" :visible.sync="dialog_visible" width="35%">
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
    <!-- upload -->
    <el-dialog title="上传图片" :visible.sync="dialog_upload_visible">
      <el-upload
        class="upload-demo"
        name="image"
        list-type="picture-card"
        :action="'/admin/image'"
        :data="data"
        :on-success="handleUploadSuccess"
        :on-error="handleUploadError"
        :on-preview="handlePreview"
        :on-remove="handleRemove"
        :before-remove="beforeRemove"
        multiple
        :limit="10"
        :on-exceed="handleExceed"
        :file-list="file_list"
      >
        <i class="el-icon-plus"></i>
      </el-upload>
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
      dialog_upload_visible: false,
      select_image: {},
      file_list: [],
      data: {
        _token: ""
      }
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
      let self = this;
      this.loading = true;
      console.log(this.$unit.getCache('token'));
      // this.getToken();

      return this.axiosInstance
        .get("/admin/image")
        .then(function(response) {
          self.loading = false;
          let data = response.data;
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
      let self = this;
      this.$confirm("确认删除？")
        .then(_ => {
          return this.axiosInstance
            .delete("/admin/image/" + self.select_image.id)
            .then(function(response) {
              let data = response.data;

              if (data.code === 200) {
                self.images.splice(self.images.indexOf(self.select_image), 1);
                self.dialog_visible = false;
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
    },
    handleCommand(command) {
      switch (command) {
        case "upload":
          this.dialog_upload_visible = true;
          break;
        case "select_all":
          console.log("select_all");
          break;
        case "edit":
          console.log("edit");
          break;
        default:
          break;
      }
    },
    handleRemove(file, fileList) {
      this.select_image = file.response.data;
      let self = this;
      return this.axiosInstance
        .delete("/admin/image/" + self.select_image.id)
        .then(function(response) {
          let data = response.data;

          if (data.code !== 200) {
            self.$message({
              showClose: true,
              message: data.message,
              type: "error"
            });
          } else {
            self.images.splice(self.images.indexOf(self.select_image), 1);
          }
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "删除失败",
            type: "error"
          });
        });
    },
    handlePreview(file) {
      console.log(file);
    },
    handleExceed(files, fileList) {
      this.$message.warning(
        `当前限制选择 3 个文件，本次选择了 ${
          files.length
        } 个文件，共选择了 ${files.length + fileList.length} 个文件`
      );
    },
    beforeRemove(file, fileList) {
      return this.$confirm(`确定移除 ${file.name}？`);
    },
    handleUploadSuccess(response, file, fileList) {
      this.images.push(file.response.data);
      this.data._token = "";
      this.$unit.removeCache('token');
      this.getToken();
    },
    handleUploadError(response, file, fileList) {
      this.getToken();
    },
    getToken() {
      let self = this;
      let token = this.$unit.getCache('token');

      if (token != null) {
        this.data._token = token;
      }

      if (this.data._token != "") {
        return;
      }

      this.axiosInstance
        .get("/admin/token")
        .then(function(response) {
          self.loading = false;
          let data = response.data;
          if (data.code === 200) {
            self.$unit.setCache('token', data.data, 0);
            self.data._token = data.data;
          }
        })
        .catch();
    }
  }
};
</script>

<style scoped lang="scss">
@import "~@/admin/image";
</style>