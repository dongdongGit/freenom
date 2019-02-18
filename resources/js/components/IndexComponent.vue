<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6 col-md-6 col-xs-12">
        <div class="info-box bg-primary">
          <div class="icon-box">
            <i class="lni-user"></i>
          </div>
          <div class="info-box-content">
            <h4 class="number">{{count.user}}</h4>
            <p class="info-text">用户</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-xs-12">
        <div class="info-box bg-success">
          <div class="icon-box">
            <i class="lni-home"></i>
          </div>
          <div class="info-box-content">
            <h4 class="number">{{count.domain}}</h4>
            <p class="info-text">域名</p>
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
      count: {
        domain: 0,
        user: 0,
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
      var self = this;
      return axios
        .get(this.GLOBAL.baseUri + "admin/index")
        .then(function(response) {
          var data = response.data;
          if (data.code === 200) {
            self.count = data.data;
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