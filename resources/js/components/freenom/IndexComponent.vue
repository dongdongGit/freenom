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
            <el-button icon="el-icon-refresh" type="primary" plain @click="sync()">同步</el-button>
            <el-button icon="el-icon-star-off" type="primary" plain @click="renews()" :disabled="allow">批量续费</el-button>
          </div>
          <div class="card-body">
            <el-table
              ref="multipleTable"
              :data="domains"
              style="width: 100%"
              @selection-change="handleSelectionChange"
            >
              <el-table-column type="selection" width="55"></el-table-column>
              <el-table-column fixed prop="domain" label="域名" ></el-table-column>
              <el-table-column prop="status" label="状态"></el-table-column>
              <el-table-column prop="type" label="类型">
                <template slot-scope="scope">
                  <span>{{ scope.row.type == 'free' ? '免费' : '收费'}}</span>
                </template>
              </el-table-column>
              <el-table-column label="注册时间">
                <template slot-scope="scope">
                  <span>{{ scope.row.register_date | moment('YYYY-MM-DD')}}</span>
                </template>
              </el-table-column>
              <el-table-column label="过期时间">
                <template slot-scope="scope">
                  <span>{{ scope.row.expires_date | moment('YYYY-MM-DD')}}</span>
                </template>
              </el-table-column>
              <el-table-column prop="enabled_auto_renew" label="是否自动续费">
                <template slot-scope="scope">
                  <el-switch
                    v-model="scope.row.enabled_auto_renew"
                    active-color="#13ce66"
                    inactive-color="#ff4949">
                  </el-switch>
                </template>
              </el-table-column>
              <el-table-column prop="renew" label="自动续费时长">
                <template slot-scope="scope">
                  <span>{{scope.row.renew + '个月'}}</span>
                </template>
              </el-table-column>
              <el-table-column label="操作" width="146">
                <template slot-scope="scope">
                  <el-button size="mini" @click="handleRenew(scope.$index, scope.row)">续费</el-button>
                  <!-- <el-popover
                    placement="top"
                    width="160"
                    v-model="visible">
                    <p>是否确定删除吗？</p>
                    <div style="text-align: right; margin: 0;border: 1px solid red">
                      <el-button size="mini" type="text" @click="visible = false">取消</el-button>
                      <el-button type="primary" size="mini" @click="handleDelete(scope.$index, scope.row)">确定</el-button>
                    </div>
                    <el-button slot="reference" type="danger" size="mini">删除</el-button>
                  </el-popover> -->
                  <el-button size="mini" type="danger" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
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
      meta: {
        count: 0,
        limit: 0,
        offset: 0,
        total: 0
      },
      loading: false,
      allow: true,
      multipleSelection: []
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
          var data = response.data;
          if (data.code === 200) {
            self.domains = data.data;
            self.meta = data.meta;
            console.log(self.meta);
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
    handleRenew(index, row) {
      var self = this;
      this.loading = true;
      return axios.post(this.GLOBAL.baseUri + 'admin/freenom/action', {
          action: 'renew',
          domains: [
            {
              'domain': row.domain,
              'domain_id': row.domain_id,
              'renew': row.renew
            }
          ]
        })
        .then(function (response) {
          self.loading = false;
          self.$message({
            message: '续费成功',
            type: 'success',
          });
          const date = new Date(self.domains[index].expires_date);
          self.domains[index].expires_date = self.$moment(date.setMonth(date.getMonth() * 1 + 1 + self.domains[index].renew * 1)).format('YYYY-MM-DD');
        })
        .catch(function (error) {
          self.loading = false;
          self.$message({
            showClose: true,
            message: '续费失败',
            type: 'error'
          });
        });
    },
    handleDelete(index, row) {
      var self = this;
      return axios.delete(this.GLOBAL.baseUri + 'admin/freenom/' + row.id)
        .then(function () {
          self.$message({
            message: '删除成功',
            type: 'success',
          });
          self.domains.splice(index, 1);  
        })
        .catch(function (error) {
          self.$message({
            showClose: true,
            message: '删除失败',
            type: 'error'
          });
        });
    },
    sync() {
      var self = this
      this.loading = true;
      return axios.post(this.GLOBAL.baseUri + 'admin/freenom/action', {
          action: 'sync'
        })
        .then(function (response) {
          self.loading = false;
          self.$message({
            message: '同步成功',
            type: 'success',
          });
          self.init();
        })
        .catch(function (error) {
          self.$message({
            showClose: true,
            message: '同步失败',
            type: 'error'
          });
        });
    },
    handleSelectionChange(val) {
      var self = this;
      val.forEach(row => {
        var today = new Date();
        
        if (today.setDate(today.getDate() * 1 + 15) < new Date(row.expires_date).getTime()) {
          this.$refs.multipleTable.toggleRowSelection(row);
        }
      });

      if (val.length > 0) {
        this.allow = false;
      } else {
        this.allow = true;
      }

      this.multipleSelection = val;
    },
    renews() {
      var domains = [];
      for (var row of this.multipleSelection) {
        domains.push({
          domain_id: row['domain_id'],
          renew: row['renew']
        });
      }
      this.loading = true;
      var self = this;
      return axios.post(this.GLOBAL.baseUri + 'admin/freenom/action', {
          action: 'renew',
          domains: domains 
        })
        .then(function (response) {
          self.loading = false;
          self.$message({
            message: '同步成功',
            type: 'success',
          });
          self.init();
        })
        .catch(function (error) {
          self.$message({
            showClose: true,
            message: '同步失败',
            type: 'error'
          });
        });
    }
  }
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
