<x-app-layout>
    @section('title')
        Users List
    @endsection


    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Users List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li class="">
                    <a class="f-s-14 f-w-500" href="#">
                        <span>
                            <i class="ph-duotone  ph-hand-heart f-s-16"></i> Ready to use
                        </span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Users List</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb end -->

    <!-- ready to use table start -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Users List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display patients-list-table" id="exampledatatable">
                            <thead>
                                <tr>
                                    <th>Nik</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Department</th>
                                    <th>Email/th>
                                    <th>Roles</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-dark">
                                                <img alt="" class="img-fluid"
                                                    src="../assets/images/avtar/14.png">
                                            </div>
                                            <p class="mb-0 ps-2"> Airi Satou</p>
                                        </div>
                                    </td>

                                    <td>Apt. 138 81391 Lockman, Port Eliseo, FL 95685</td>
                                    <td class="f-w-500">AR 5896</td>
                                    <td class="text-success">+13013164820</td>
                                    <td>27</td>
                                    <td>1 jan 2024</td>
                                    <td>
                                        <span class="badge text-outline-danger">Cancel</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
