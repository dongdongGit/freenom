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
            <el-button
              icon="el-icon-star-off"
              type="primary"
              plain
              @click="renews()"
              :disabled="allow"
            >批量续费</el-button>
          </div>
          <div class="card-body">
            <el-table
              ref="multipleTable"
              :data="domains"
              style="width: 100%"
              @selection-change="handleSelectionChange"
              @select="handleSelect"
              @select-all="handleSetectAll"
            >
              <el-table-column type="selection" width="55"></el-table-column>
              <el-table-column fixed prop="domain" label="域名"></el-table-column>
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
                    inactive-color="#ff4949"
                    @change="handleSwitchChange(scope.$index, scope.row)"
                  ></el-switch>
                </template>
              </el-table-column>
              <el-table-column prop="renew" label="自动续费时长">
                <template slot-scope="scope">
                  <el-select
                    v-model="scope.row.renew"
                    placeholder="请选择"
                    @change="selectChangeRenew(scope.$index, scope.row)"
                  >
                    <el-option
                      v-for="option in options"
                      :key="option.value"
                      :label="option.label"
                      :value="option.value"
                    ></el-option>
                  </el-select>
                </template>
              </el-table-column>
              <el-table-column label="操作" width="148">
                <template slot-scope="scope">
                  <el-button size="mini" @click="handleRenew(scope.$index, scope.row)">续费</el-button>
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
                  @current-change="pageChange"
                  layout="prev, pager, next"
                  :total="meta.total"
                  :page-size="meta.limit"
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
      multipleSelection: [],
      options: []
    };
  },
  created() {
    this.init();
  },
  watch: {
    $route: "init"
  },
  mounted() {
    for (let i = 1; i < 13; i++) {
      this.options.push({
        value: i,
        label: i + " 个月"
      });
    }
  },
  methods: {
    init() {
      var self = this;
      this.loading = true;

      return axios
        .get(this.GLOBAL.baseUri + "admin/freenom")
        .then(function(response) {
          self.loading = false;
          var data = response.data;
          if (data.code === 200) {
            self.domains = data.data;
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
    handleRenew(index, row) {
      var self = this;
      this.loading = true;
      return axios
        .post(this.GLOBAL.baseUri + "admin/freenom/action", {
          action: "renew",
          domains: [
            {
              domain: row.domain,
              domain_id: row.domain_id,
              renew: row.renew
            }
          ]
        })
        .then(function(response) {
          self.loading = false;
          self.$message({
            message: "续费成功",
            type: "success"
          });
          const date = new Date(self.domains[index].expires_date);
          self.domains[index].expires_date = self
            .$moment(
              date.setMonth(
                date.getMonth() * 1 + 1 + self.domains[index].renew * 1
              )
            )
            .format("YYYY-MM-DD");
        })
        .catch(function(error) {
          self.loading = false;
          self.$message({
            showClose: true,
            message: "续费失败",
            type: "error"
          });
        });
    },
    handleDelete(index, row) {
      var self = this;
      return axios
        .delete(this.GLOBAL.baseUri + "admin/freenom/" + row.id)
        .then(function() {
          self.$message({
            message: "删除成功",
            type: "success"
          });
          self.domains.splice(index, 1);
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "删除失败",
            type: "error"
          });
        });
    },
    sync() {
      var self = this;
      this.loading = true;
      return axios
        .post(this.GLOBAL.baseUri + "admin/freenom/action", {
          action: "sync"
        })
        .then(function(response) {
          self.loading = false;
          self.$message({
            message: "同步成功",
            type: "success"
          });
          self.init();
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "同步失败",
            type: "error"
          });
        });
    },
    handleSwitchChange(index, row) {
      var self = this;
      this.loading = true;
      return axios
        .put(this.GLOBAL.baseUri + "admin/freenom/" + row.id, {
          enabled_auto_renew: row.enabled_auto_renew
        })
        .then(function(response) {
          self.loading = false;
          let data = response.data;
          if (data.code === 200) {
            self.$message({
              message: "操作成功",
              type: "success"
            });
            Vue.set(self.domains, index, data.data);
          }
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "操作失败",
            type: "error"
          });
        });
    },
    handleSelect(selection, row) {
      var self = this;
      self.expired(row);
    },
    handleSetectAll(selection) {
      var self = this;
      self.expired(selection);
    },
    handleSelectionChange(val) {
      var self = this;

      if (val.length > 0) {
        this.allow = false;
      } else {
        this.allow = true;
      }

      this.multipleSelection = val;
    },
    selectChangeRenew(index, row) {
      var self = this;
      this.loading = true;
      return axios
        .put(this.GLOBAL.baseUri + "admin/freenom/" + row.id, {
          renew: row.renew
        })
        .then(function(response) {
          self.loading = false;
          let data = response.data;
          if (data.code === 200) {
            self.$message({
              message: "操作成功",
              type: "success"
            });
            Vue.set(self.domains, index, data.data);
          }
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "操作失败",
            type: "error"
          });
        });
    },
    renews() {
      var domains = [];
      for (var row of this.multipleSelection) {
        domains.push({
          domain_id: row["domain_id"],
          renew: row["renew"]
        });
      }

      this.loading = true;
      var self = this;

      return axios
        .post(this.GLOBAL.baseUri + "admin/freenom/action", {
          action: "renew",
          domains: domains
        })
        .then(function(response) {
          self.loading = false;
          self.$message({
            message: "同步成功",
            type: "success"
          });
          self.init();
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "同步失败",
            type: "error"
          });
        });
    },
    pageChange(page_number) {
      this.loading = true;
      var self = this;
      let limit = this.meta.limit;
      let offset = this.meta.limit * (page_number - 1);

      return axios
        .get(this.GLOBAL.baseUri + "admin/freenom?limit=" + limit + "&offset=" + offset)
        .then(function(response) {
          self.loading = false;
          let data = response.data;
          if (data.code === 200) {
            self.loading = false;
            self.domains = data.data;
            self.meta = data.meta;
          }
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "操作失败",
            type: "error"
          });
        });
    },
    expired(selection) {
      var select = [];

      if (!Array.isArray(selection)) {
        let new_selection = [];
        new_selection.push(selection);
        selection = new_selection;
      }

      selection.forEach(row => {
        let today = new Date();
        let now = today.setDate(today.getDate() * 1 + 15);
        let expired_at = new Date(row.expires_date).getTime();

        if (now < expired_at) {
          select.push(row);
        }
      });

      select.forEach(element => {
        this.$refs.multipleTable.toggleRowSelection(element, false);
      });

      return select;
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
