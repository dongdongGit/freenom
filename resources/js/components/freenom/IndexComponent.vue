<template>
  <div v-loading="loading">
    <div class="container-fluid">
      <div class="breadcrumb-wrapper row">
        <div class="col-12 col-lg-3 col-md-6">
          <h4 class="page-title">域名列表</h4>
        </div>
        <div class="col-12 col-lg-9 col-md-6">
          <ol class="breadcrumb float-right">
            <li>
              <router-link to="/">首页</router-link>
            </li>
            <li class="active">/ 域名列表</li>
          </ol>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header border-bottom">
            <el-button
              icon="el-icon-refresh"
              type="primary"
              plain
              @click="sync()">
              同步
            </el-button>
          </div>
          <div class="card-body">
            <el-table :data="domains" style="width: 100%">
              <el-table-column fixed prop="domain" label="域名" ></el-table-column>
              <el-table-column prop="status" label="状态"></el-table-column>
              <el-table-column prop="type" label="类型"></el-table-column>
              <el-table-column prop="register_date" label="注册时间"></el-table-column>
              <el-table-column prop="expires_date" label="过期时间"></el-table-column>
              <el-table-column prop="enabled_auto_renew" label="自动续费"></el-table-column>
              <el-table-column prop="renew" label="自动续费时长"></el-table-column>
              <el-table-column label="操作" width="146">
                <template slot-scope="scope">
                  <el-button size="mini" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
                  <el-button
                    size="mini"
                    type="danger"
                    @click="handleDelete(scope.$index, scope.row)"
                  >删除</el-button>
                </template>
              </el-table-column>
            </el-table>
            <div class="row">
              <div class="col-sm-12 col-md-5"></div>
              <div class="col-sm-12 col-md-7">
                <el-pagination
                  layout="prev, pager, next"
                  :total="meta.total"
                  background
                  class="pagination-flex-end"
                ></el-pagination>
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
      domains: [],
      meta: {},
      loading: false
    };
  },
  created() {
    this.init();
  },
  watch: {
    '$route': 'init'
  },
  methods: {
    init() {
      var self = this
      this.loading = true;
      return axios.get(this.GLOBAL.baseUri + 'admin/freenom')
        .then(function (response) {
          self.loading = false;
          if (response.code === 200) {
            self.domains = response.data;
            self.meta = response.meta
          }
        })
        .catch(function (error) {
          self.$message({
            showClose: true,
            message: '加载失败',
            type: 'error'
          });
        });
    },
    sync() {
      var self = this
      this.loading = true;
      return axios.post(this.GLOBAL.baseUri + 'admin/freenom/sync')
        .then(function (response) {
          self.loading = false;
        })
        .catch(function (error) {
          self.$message({
            showClose: true,
            message: '同步失败',
            type: 'error'
          });
        });
    }
  },
};
</script>

<style lang="scss" scoped>
.pagination-flex-end {
  margin-top: 20px;
  padding: 0px;
  display: flex;
  justify-content: flex-end;
}
</style>
