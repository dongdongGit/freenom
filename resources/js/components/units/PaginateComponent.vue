<template>
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
</template>

<script>
export default {
  data() {
    return {};
  },
  props: ["data", "meta", "url"],
  methods: {
    pageChange(page_number) {
      this.$parent.loading = true;
      let self = this;
      let limit = this.meta.limit;
      let offset = this.meta.limit * (page_number - 1);

      return axios
        .get(this.url, {
          params: {
            limit: limit,
            offset: offset
          }
        })
        .then(function(response) {
          self.$parent.loading = false;
          let data = response.data;
          if (data.code === 200) {
            self.$parent.loading = false;
            self.$emit("listen-paginate", data);
          }
        })
        .catch(function(error) {
          self.$message({
            showClose: true,
            message: "操作失败",
            type: "error"
          });
        });
    }
  }
};
</script>

<style lang="scss" scoped>
@import "~@/units/paginate";
</style>