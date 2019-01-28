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
            <el-table :data="tableData" style="width: 100%">
              <el-table-column fixed prop="date" label="日期" ></el-table-column>
              <el-table-column prop="name" label="姓名"></el-table-column>
              <el-table-column prop="province" label="省份"></el-table-column>
              <el-table-column prop="city" label="市区"></el-table-column>
              <el-table-column prop="address" label="地址"></el-table-column>
              <el-table-column prop="zip" label="邮编"></el-table-column>
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
                  :total="50"
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
      tableData: [
        {
          date: "2016-05-03",
          name: "王小虎",
          province: "上海",
          city: "普陀区",
          address: "上海市普陀区金沙江路 1518 弄",
          zip: 200333
        },
        {
          date: "2016-05-02",
          name: "王小虎",
          province: "上海",
          city: "普陀区",
          address: "上海市普陀区金沙江路 1518 弄",
          zip: 200333
        },
        {
          date: "2016-05-04",
          name: "王小虎",
          province: "上海",
          city: "普陀区",
          address: "上海市普陀区金沙江路 1518 弄",
          zip: 200333
        },
        {
          date: "2016-05-01",
          name: "王小虎",
          province: "上海",
          city: "普陀区",
          address: "上海市普陀区金沙江路 1518 弄",
          zip: 200333
        }
      ],
      loading: false
    };
  },
  created () {
    this.init();
  },
  methods: {
    init () {
      var self = this
      this.loading = true;
      return axios.get(this.GLOBAL.baseUri + 'admin/freenom')
        .then(function (response) {
          self.loading = false;
        })
        .catch(function (error) {
          console.log('test');
        });
    },
    handleClick(row) {
      30;
      console.log(row);
    },
    sync() {
      console.log('sync');
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
