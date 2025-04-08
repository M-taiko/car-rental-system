<!-- Sidebar-right -->
<div class="sidebar sidebar-left sidebar-animate">
    <div class="panel panel-primary card mb-0 box-shadow">
        <div class="tab-menu-heading border-0 p-3">
            <div class="card-title mb-0">{{ __('messages.notifications') }}</div>
            <div class="card-options mr-auto">
                <a href="#" class="sidebar-remove"><i class="fe fe-x"></i></a>
            </div>
        </div>
        <div class="panel-body tabs-menu-body latest-tasks p-0 border-0">
            <div class="tabs-menu">
                <!-- Tabs -->
                <ul class="nav panel-tabs">
                    <li><a href="#side1" class="active" data-toggle="tab"><i class="ion ion-md-notifications tx-18 ml-2"></i> {{ __('messages.notifications') }}</a></li>
                    <li><a href="#side2" data-toggle="tab"><i class="fas fa-wallet tx-18 ml-2"></i> {{ __('messages.quick_summary') }}</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <!-- Tab 1: Notifications -->
                <div class="tab-pane active" id="side1">
                    @if(!empty($notifications) && is_array($notifications) && count($notifications) > 0)
                        @foreach($notifications as $notification)
                            <div class="list d-flex align-items-center border-bottom p-3">
                                <div>
                                    <span class="avatar bg-{{ $notification['color'] }} brround avatar-md">
                                        <i class="{{ $notification['icon'] }}"></i>
                                    </span>
                                </div>
                                <a class="wrapper w-100 mr-3" href="#">
                                    <p class="mb-0 d-flex">
                                        <b>{{ $notification['message'] }}</b>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-clock text-muted ml-1"></i>
                                            <small class="text-muted ml-auto">{{ $notification['time'] }}</small>
                                            <p class="mb-0"></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="list d-flex align-items-center p-3">
                            <p class="mb-0">{{ __('messages.no_notifications') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Tab 2: Quick Summary -->
                <div class="tab-pane" id="side2">
                    @php
                        $month = date('Y-m');
                        $startDate = date('Y-m-01 00:00:00', strtotime($month));
                        $endDate = date('Y-m-t 23:59:59', strtotime($month));

                        $totalIncome = \App\Models\Account::where('type', 'income')
                                                        ->whereBetween('date', [$startDate, $endDate])
                                                        ->sum('amount');
                        $totalExpenses = \App\Models\Account::where('type', 'expense')
                                                           ->whereBetween('date', [$startDate, $endDate])
                                                           ->sum('amount');
                        $balance = $totalIncome - $totalExpenses;
                    @endphp
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <div class="ml-3">
                                <i class="fas fa-arrow-up text-success tx-18"></i>
                            </div>
                            <div>
                                <strong>{{ __('messages.total_income') }}</strong>
                                <div class="small text-muted">
                                    {{ $totalIncome }}
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <div class="ml-3">
                                <i class="fas fa-arrow-down text-danger tx-18"></i>
                            </div>
                            <div>
                                <strong>{{ __('messages.total_expenses') }}</strong>
                                <div class="small text-muted">
                                    {{ $totalExpenses }}
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <div class="ml-3">
                                <i class="fas fa-balance-scale text-primary tx-18"></i>
                            </div>
                            <div>
                                <strong>{{ __('messages.balance') }}</strong>
                                <div class="small text-muted">
                                    {{ $balance }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Sidebar-right -->
