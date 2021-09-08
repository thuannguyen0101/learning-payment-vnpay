@extends('.Admin.layout.index')
@section('title')
    Profile - Mazer Admin Dashboard
@endsection
@section('content')
    <div class="page-heading">
        <h3>Thống kê dịch bệnh</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tình hình covid</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-profile-visit"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
