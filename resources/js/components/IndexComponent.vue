<template>
<div class="container-fluid">
    <div class="breadcrumb-wrapper row">
        <div class="col-12 col-lg-3 col-md-6">
            <h4 class="page-title">列表
            </h4>
        </div>
        <div class="col-12 col-lg-9 col-md-6">
            <ol class="breadcrumb float-right">
                <li><a href="#">Freenom</a></li>
                <li class="active"> / 域名</li>
            </ol>
        </div>
    </div>
    <div class="col-12 m-b-10">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">域名列表</h4>
            </div>
            <div class="card-body">
                <h4 class="mt-0 box-title"></h4>
                <p class="text-muted m-b-20 box-content">
                    Add <code>table-hover</code> to enable a hover state on table rows within a
                    <code>tbody</code>.
                </p>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Username</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                            </tr>
                        </tbody>
                    </table>
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
                loading: false,
                list: null,
                error: null,
                item:null
            }
        },
        created() {
            // 组件创建完后获取数据，
            // 此时 data 已经被 observed 了
            this.init()
        },
        watch: {
            // 如果路由有变化，会再次执行该方法
            '$route': 'init'
        },
        methods: {
            init() {
                this.error = null
                this.loading = true
                var self = this
                    // replace getPost with your data fetching util / API wrapper
                return axios.get(this.baseUri)
                    .then(function(response) {
                        self.loading = false;
                        self.list = response.data;
                    })
                    .catch(function(error) {
                        self.error = error;
                    })
            },
        }
}
