@extends('layouts.master')

@php
    use App\Models\Setting;
    $logo = Setting::get('company_logo');
    @endphp

@section('css')
<style>
    @media print {
        /* Hide navigation and buttons when printing */
        .no-print, .no-print * {
            display: none !important;
        }
        .main-header, .main-footer, .main-sidebar {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .invoice-box {
            margin: 0;
            padding: 0;
            max-width: 100%;
        }
        body {
            background: white !important;
        }
        @page {
            margin: 0.5cm;
        }
    }

    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 16px;
        line-height: 24px;
        direction: rtl;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: right;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .information {
        background: #f8f9fa;
        padding: 20px;
        margin-bottom: 20px;
    }

    .details table {
        border-collapse: collapse;
        margin-top: 20px;
    }

    .details th, .details td {
        border: 1px solid #dee2e6;
        padding: 8px;
    }

    .details th {
        background-color: #f8f9fa;
    }

    .total {
        margin-top: 20px;
        border-top: 2px solid #dee2e6;
        font-weight: bold;
    }

    .company-logo {
        max-width: 200px;
        max-height: 100px;
        object-fit: contain;
    }

    .company-info {
        margin-top: 20px;
    }

    .company-info p {
        margin: 5px 0;
    }

    .invoice-header {
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }

    .invoice-footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #dee2e6;
    }
</style>
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between no-print">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.rentals') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.invoice') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <div class="mb-3 mb-xl-0">
            <button onclick="window.print()" class="btn btn-primary no-print">
                <i class="mdi mdi-printer"></i> {{ __('messages.print') }}
            </button>
            <a href="{{ route('rentals.index') }}" class="btn btn-secondary no-print">
                <i class="mdi mdi-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')
<div class="invoice-box bg-white">
    <!-- Invoice Header with Company Info -->
    <div class="invoice-header">
        <table>
            <tr>
                <td style="width: 50%;">
                    @php
                     
                        $logo = Setting::get('company_logo');
                    @endphp
                    
                    <img src="{{ $logo ? asset('settings/' . $logo) : asset('assets/img/brand/logo.png') }}" style="width: 100px; height: 100px;"   alt="Company Logo" class="logo-1">

                    <div class="company-info">
                        <p>{{ Setting::get('company_address') }}</p>
                        <p>{{ __('messages.phone') }}: {{ Setting::get('company_phone') }}</p>
                        <p>{{ __('messages.email') }}: {{ Setting::get('company_email') }}</p>
                        <p>{{ __('messages.tax_number') }}: {{ Setting::get('tax_number') }}</p>
                        <p>{{ __('messages.commercial_number') }}: {{ Setting::get('commercial_number') }}</p>
                    </div>
                </td>
                <td style="width: 50%; text-align: left;">
                    <h1 class="title">{{ __('messages.invoice') }}</h1>
                    <p class="fa-1x">{{ __('messages.invoice_number') }}: {{ Setting::get('invoice_prefix') }}{{ $rental->id }}</p>
                    <p class="fa-1x">{{ __('messages.date') }}: {{ now()->format('Y-m-d') }}</p>
                    <p class="fa-1x">{{ __('messages.rental_status') }}: {{ __('messages.' . $rental->status) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Customer Information -->
    <div class="information">
        <table>
            <tr>
                <td style="width: 50%;">
                    <h4>{{ __('messages.customer_information') }}</h4>
                    <p><strong>{{ __('messages.name') }}:</strong> {{ $rental->customer->name }}</p>
                    <p><strong>{{ __('messages.phone') }}:</strong> {{ $rental->customer->phone }}</p>
                    <p><strong>{{ __('messages.email') }}:</strong> {{ $rental->customer->email }}</p>
                    <p><strong>{{ __('messages.id_number') }}:</strong> {{ $rental->customer->id_number }}</p>
                </td>
                <td style="width: 50%;">
                    <h4>{{ __('messages.rental_information') }}</h4>
                    <p><strong>{{ __('messages.start_time') }}:</strong> {{ $rental->start_time->format('Y-m-d H:i') }}</p>
                    <p><strong>{{ __('messages.end_time') }}:</strong> {{ $rental->end_time->format('Y-m-d H:i') }}</p>
                    <p><strong>{{ __('messages.duration') }}:</strong> {{ $rental->duration }} {{ __('messages.days') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Rental Details -->
    <div class="details">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.rate') }}</th>
                    <th>{{ __('messages.days') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ __('messages.car_rental') }} ({{ $rental->car->brand }} {{ $rental->car->model }})
                        <br>
                        <small>{{ __('messages.plate_number') }}: {{ $rental->car->plate_number }}</small>
                    </td>
                    <td>{{ number_format($rental->price_per_day, 2) }}</td>
                    <td>{{ $rental->duration }}</td>
                    <td>{{ number_format($rental->price_per_day * $rental->duration, 2) }}</td>
                </tr>
                @if($rental->driver)
                <tr>
                    <td>
                        {{ __('messages.driver_service') }}
                        <br>
                        <small>{{ $rental->driver->name }}</small>
                    </td>
                    <td>{{ number_format($rental->driver_price_per_day, 2) }}</td>
                    <td>{{ $rental->duration }}</td>
                    <td>{{ number_format($rental->driver_price_per_day * $rental->duration, 2) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                @if(config('settings.invoice.show_tax'))
                <tr>
                    <td colspan="3" style="text-align: left;">{{ __('messages.subtotal') }}:</td>
                    <td>{{ number_format($rental->total_cost / (1 + config('settings.invoice.tax_rate') / 100), 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: left;">{{ __('messages.tax') }} ({{ config('settings.invoice.tax_rate') }}%):</td>
                    <td>{{ number_format($rental->total_cost - ($rental->total_cost / (1 + config('settings.invoice.tax_rate') / 100)), 2) }}</td>
                </tr>
                @endif
                <tr class="total">
                    <td colspan="3" style="text-align: left;">{{ __('messages.total_cost') }}:</td>
                    <td>{{ number_format($rental->total_cost, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: left;">{{ __('messages.paid_amount') }}:</td>
                    <td>{{ number_format($rental->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: left;">{{ __('messages.remaining_amount') }}:</td>
                    <td>{{ number_format($rental->remaining_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Notes -->
    @if($rental->notes)
    <div style="margin-top: 20px;">
        <h4>{{ __('messages.notes') }}</h4>
        <p>{{ $rental->notes }}</p>
    </div>
    @endif

    <!-- Terms and Invoice Footer -->
    <div class="invoice-footer">
        <div style="margin-bottom: 40px;">
            <h4>{{ __('messages.terms_and_conditions') }}</h4>
            <ol>
                <li>{{ __('messages.term_1') }}</li>
                <li>{{ __('messages.term_2') }}</li>
                <li>{{ __('messages.term_3') }}</li>
            </ol>
        </div>

        <p class="text-center">{{ config('settings.invoice.footer_text') }}</p>

        <table style="margin-top: 50px;">
            <tr>
                <td style="width: 50%;">
                    <p>_________________________</p>
                    <p>{{ __('messages.customer_signature') }}</p>
                </td>
                <td style="width: 50%; text-align: left;">
                    <p>_________________________</p>
                    <p>{{ __('messages.company_signature') }}</p>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection
