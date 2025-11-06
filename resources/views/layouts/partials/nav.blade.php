<nav class="semi-nav dark-sidebar selected">
    <div class="app-logo">
        <a class="logo d-inline-block" href="{{ route('dashboard') }}">
            <img alt="#" src="{{ asset('assets') }}/images/logo/logohitam.png"></a>

        <span class="bg-light-primary toggle-semi-nav">
            <i class="ti ti-chevrons-right f-s-20"></i>
        </span>
    </div>
    <div class="app-nav" id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">
            <li class="menu-title">
                <span>Dashboard</span>
            </li>
            <li class="no-sub">
                <a class="" href="{{ route('dashboard') }}">
                    <i class="iconoir-home-alt"></i> Dashboard
                </a>
            </li>

            <li class="menu-title"><span>Master Data</span></li>
            <li>
                <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#master-data">
                    <i class="iconoir-database"></i> Master Data
                </a>
                <ul class="collapse" id="master-data">
                    <li><a href="{{ route('users.index') }}">Users</a></li>
                    <li><a href="{{ route('departments.index') }}">Department</a></li>
                    <li><a href="{{ route('customers.index') }}">Customer</a></li>
                    <li><a href="{{ route('permissions.index') }}">Permission</a></li>
                    <li><a href="{{ route('roles.index') }}">Role</a></li>
                </ul>
            </li>
            <li>
                <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#master-management">
                    <i class="iconoir-database"></i> Master Management
                </a>
                <ul class="collapse" id="master-management">
                    <li><a href="{{ route('requisition.path') }}">Approval Path</a></li>
                    <li><a href="{{ route('master.revision.index') }}">Revision</a></li>

                </ul>
            </li>

             <li class="menu-title"><span>Requisition Slip Form</span></li>
             <li>
                 <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#requisition-slip">
                     <i class="iconoir-google-docs"></i> Requisition Slip Form
                 </a>
                 <ul class="collapse" id="requisition-slip">
                     <li><a href="{{ route('sample-form.index') }}">Sample Form</a></li>
                     <li><a href="{{ route('complain-form.index') }}">Complain Form</a></li>
                     <li><a href="{{ route('freegoods-form.index') }}">Free Goods</a></li>
                 </ul>
             </li>
             <li class="menu-title"><span>Requisition Slip Report</span></li>
             <li>
                 <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#requisition-slip-report">
                     <i class="iconoir-stats-report"></i> Requisition Slip Report
                 </a>
                 <ul class="collapse" id="requisition-slip-report">
                     <li><a href="{{ route('sample-form.reports') }}">Sample Reports</a></li>
                     <li><a href="{{ route('complain.reports') }}">Complain Reports</a></li>
                     <li><a href="{{ route('freegoods-form.reports') }}">Free Goods Reports</a></li>
                 </ul>
             </li>
             <li class="menu-title"><span>Requisition Slip Approval</span></li>
             <li>
                 <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#requisition-slip-approval">
                     <i class="iconoir-stats-report"></i> Requisition Slip Approval
                 </a>
                 <ul class="collapse" id="requisition-slip-approval">
                     <li><a href="{{ route('sample-form.approval') }}">Sample Approval</a></li>
                     <li><a href="{{ route('complain.approval') }}">Complain Approval</a></li>
                     <li><a href="{{ route('freegoods-form.approval') }}">Free Goods Approval</a></li>
                 </ul>
             </li>
             <li class="menu-title"><span>Requisition Slip Approval</span></li>
             <li>
                 <a aria-expanded="false" class="" data-bs-toggle="collapse" href="#requisition-slip-log">
                     <i class="iconoir-stats-report"></i> Requisition Slip Log
                 </a>
                 <ul class="collapse" id="requisition-slip-log">
                     <li><a href="{{ route('sample-form.log') }}">Sample Log</a></li>
                     <li><a href="{{ route('complain.log') }}">Complain Log</a></li>
                     <li><a href="{{ route('freegoods-form.log') }}">Free Goods Log</a></li>
                 </ul>
             </li>
         </ul>
     </div>


    <div class="menu-navs">
        <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
        <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
    </div>

</nav>