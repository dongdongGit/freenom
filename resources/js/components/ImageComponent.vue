<template>
  <div class="main-content">
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
        <div class="masonry">
          <div class="col-md-3 col-lg-3 col-xlg-3">
            <div class="card card-body">
              <div class="row align-items-center">
                <div class="col-md-12 col-lg-12 text-center">
                  <img
                    src="https://img.pc841.com/2018/0326/20180326051740414.jpg"
                    class="img-circle img-fluid"
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-lg-3 col-xlg-3">
            <div class="card card-body">
              <div class="row align-items-center">
                <div class="col-md-12 col-lg-12 text-center">
                  <img
                    src="http://pic.shiliyoupin.com/tupian/HTTP3d3dy5iaWFvYmFpanUuY29tL3VwbG9hZHMvMjAxODAxMTEvMDMvMTUxNTYxMTQxNi1yeXdUQm1Sa3pGLmpwZwloglog.jpg"
                    class="img-circle img-fluid"
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-lg-3 col-xlg-3">
            <div class="card card-body">
              <div class="row align-items-center">
                <div class="col-md-12 col-lg-12 text-center">
                  <img
                    src="https://pic2.zhimg.com/v2-3be05963f5f3753a8cb75b6692154d4a_1200x500.jpg"
                    class="img-circle img-fluid"
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-lg-3 col-xlg-3">
            <div class="card card-body">
              <div class="row align-items-center">
                <div class="col-md-12 col-lg-12 text-center">
                  <img
                    src="https://img.pc841.com/2018/0326/20180326051740414.jpg"
                    class="img-circle img-fluid"
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-lg-3 col-xlg-3">
            <div class="card card-body">
              <div class="row align-items-center">
                <div class="col-md-12 col-lg-12 text-center">
                  <img
                    src="http://pic.shiliyoupin.com/tupian/HTTP3d3dy5iaWFvYmFpanUuY29tL3VwbG9hZHMvMjAxODAxMTEvMDMvMTUxNTYxMTQxNi1yeXdUQm1Sa3pGLmpwZwloglog.jpg"
                    class="img-circle img-fluid"
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-lg-3 col-xlg-3">
            <div class="card card-body">
              <div class="row align-items-center">
                <div class="col-md-12 col-lg-12 text-center">
                  <img
                    src="https://pic2.zhimg.com/v2-3be05963f5f3753a8cb75b6692154d4a_1200x500.jpg"
                    class="img-circle img-fluid"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      images: []
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
          }
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "加载失败",
            type: "error"
          });
        });
    }
  }
};
</script>