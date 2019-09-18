<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="info-box bg-primary">
          <div class="icon-box">
            <i class="lni-user"></i>
          </div>
          <div class="info-box-content">
            <h4 class="number">{{count.users}}</h4>
            <p class="info-text">用户</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="info-box bg-success">
          <div class="icon-box">
            <i class="lni-home"></i>
          </div>
          <div class="info-box-content">
            <h4 class="number">{{count.domains}}</h4>
            <p class="info-text">域名</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 col-xs-12">
        <div class="info-box bg-purple">
          <div class="icon-box">
            <i class="lni-image"></i>
          </div>
          <div class="info-box-content">
            <h4 class="number">{{count.images}}</h4>
            <p class="info-text">图片</p>
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
        domains: 0,
        users: 0,
        images: 0,
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
      let countCache = this.$unit.getCache('count');
      if (countCache == null) {
        return this.axiosInstance
          .get("/admin/index")
          .then(function(response) {
            let data = response.data;
            if (data.code === 200) {
              self.count = data.data;
              self.$unit.setCache('count', self.count, 900);
            }
          })
          .catch(function(error) {
            self.$message({
              showClose: true,
              message: "加载失败",
              type: "error"
            });
          });
      } else {
        this.count = countCache;
      }
    }
  }
};
</script>