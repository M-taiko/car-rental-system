@extends('layouts.master')

@section('title')
    {{ __('messages.maintenance_invoice') }} - {{ $maintenance->id }}
@endsection


    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        /* إخفاء العناصر غير المطلوبة عند الطباعة */
        @media print {
            body * {
                visibility: hidden;
            }
            .invoice-box, .invoice-box * {
                visibility: visible;
            }
            .invoice-box {
                position: absolute;
                left: 0;
                top: 0;
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 10px;
            }
            .print-buttons {
                display: none !important;
            }
        }

        /* الطباعة العادية بمقاس A4 */
        @media print and (min-width: 210mm) {
            .invoice-box {
                max-width: 100%;
                width: 210mm; /* A4 width */
                height: 297mm; /* A4 height */
                padding: 20mm;
            }
            .invoice-box table tr.top table td.title {
                font-size: 45px;
            }
        }

        /* الطباعة الحرارية بمقاس 80mm */
        @media print and (max-width: 80mm) {
            .invoice-box {
                max-width: 80mm;
                width: 100%;
                padding: 5mm;
                font-size: 12px;
            }
            .invoice-box table {
                width: 100%;
            }
            .invoice-box table tr td {
                padding: 2px;
            }
            .invoice-box table tr.top table td.title {
                font-size: 20px;
                line-height: 20px;
            }
            .invoice-box table tr.top table td.title img {
                max-width: 100px;
            }
            .invoice-box table tr.information table td {
                padding-bottom: 10px;
            }
            .invoice-box table tr.heading td {
                font-size: 12px;
            }
            .invoice-box table tr.item td {
                font-size: 12px;
            }
            .invoice-box table tr.total td {
                font-size: 12px;
            }
        }
    </style>


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.maintenance_invoice') }} #{{ $maintenance->id }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="invoice-box">
                            <table cellpadding="0" cellspacing="0">
                                <tr class="top">
                                    <td colspan="2">
                                        <table>
                                            <tr>
                                                <td class="title">
                                                    <img src="{{ asset('assets/img/brand/logo.png') }}" style="width: 100%; max-width: 200px;">
                                                </td>
                                                <td>
                                                    {{ __('messages.maintenance_invoice') }} #: {{ $maintenance->id }}<br>
                                                    {{ __('messages.date') }}: {{ $maintenance->date }}<br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="information">
                                    <td colspan="2">
                                        <table>
                                            <tr>
                                                <td>
                                                    Bike Rental System<br>
                                                    12345 Sunny Road<br>
                                                    Sunnyville, TX 12345
                                                </td>
                                                <td>
                                                    @if($maintenance->type === 'customer' && $maintenance->customer)
                                                        {{ $maintenance->customer->name }}<br>
                                                        {{ $maintenance->customer->email ?? '-' }}<br>
                                                        {{ $maintenance->customer->phone ?? '-' }}
                                                    @else
                                                        {{ __('messages.internal_maintenance') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="heading">
                                    <td>{{ __('messages.description') }}</td>
                                    <td>{{ __('messages.cost') }}</td>
                                </tr>
                                <tr class="item">
                                    <td>{{ $maintenance->description ?? __('messages.maintenance_for_bike') . ' ' . $maintenance->bike->name }}</td>
                                    <td>{{ $maintenance->cost }}</td>
                                </tr>
                                <tr class="total">
                                    <td></td>
                                    <td>{{ __('messages.total') }}: {{ $maintenance->cost }}</td>
                                </tr>
                            </table>
                            <div class="text-center mt-4 print-buttons">
                                <button onclick="printThermal()" class="btn btn-primary">{{ __('messages.print_thermal') }}</button>
                                <button onclick="printA4()" class="btn btn-primary">{{ __('messages.print_a4') }}</button>
                                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function printThermal() {
            // تعيين عرض الصفحة للطباعة الحرارية (80mm)
            document.body.classList.add('thermal-print');
            window.print();
        }

        function printA4() {
            // تعيين عرض الصفحة للطباعة العادية (A4)
            document.body.classList.remove('thermal-print');
            window.print();
        }
    </script>
@endsection
