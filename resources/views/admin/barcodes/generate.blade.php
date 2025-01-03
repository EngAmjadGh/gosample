@extends('layouts.master')
@section('content')
    <style>
        #barcode_area svg,
        #barcode_area img {
            margin: 0 auto;
        }

        #barcode_area svg {
            margin-bottom: 10px;
        }

        text#code {
            font-size: 15px;
            margin-bottom: -3px !important;
        }
    </style>
    <div class="container-fluid">
        <div class="row justify-content-center pull-up  px-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body pt-2">
                        <form method="POST" action="{{ route('admin.barcodes.generateBarcodes') }}"
                            enctype="multipart/form-data">

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="start">Total Count</label>
                                    <input type="number" name="range" class="form-control" id="range"
                                        placeholder="How many barcode to generate?">
                                </div>
                                <div class="form-group col">
                                    <label for="start">Type</label>
                                    <select name="type" class="form-control">
                                        <option value="bag">Bag</option>
                                        <option value="sample">Sample</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">

                                @csrf
                                <button type="submit" class="btn  btn-dark">
                                    <i class="mdi mdi-barcode-scan text-light" style="font-size: 20px;"></i>
                                    Generate barcode
                                </button>

                                <button role="button" class="btn btn-info" onclick="printReport()">
                                    <i class="mdi mdi-printer text-light" style="font-size: 20px;"></i>
                                    Print Barcode
                                </button>
                        </form>
                    </div>


                    <div id="barcode_area" class="text-center">

                        @for ($i = $start; $i < $start + $sequence; $i++)
                            @if ($type == 'location')
                                <div wire:key="{{ uniqid() }}" class="pt-2 ">
                                    {!! DNS1D::getBarcodeSVG($sequence . '-' . $type, 'C128', 4, 55) !!}
                                </div>
                            @elseif ($type == 'container')
                                <div wire:key="{{ uniqid() }}" class="pt-2">
                                    {!! DNS1D::getBarcodeSVG($sequence . '-' . $type, 'C128', 4, 55) !!}
                                </div>
                            @elseif ($type == 'bag')
                                <div wire:key="{{ uniqid() }}" class="pt-2">
                                    {!! DNS1D::getBarcodeSVG($i . '-' . $type, 'C128', 6, 280) !!}
                                </div>
                            @else
                                <div wire:key="{{ uniqid() }}" class="pt-5">
                                    {!! DNS1D::getBarcodeSVG(str_pad($i, 10, '0', STR_PAD_LEFT), 'C128', 4, 55) !!}
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection


@section('script')
    <script>
        function printReport() {
            var prtContent = document.getElementById("barcode_area");
            var WinPrint = window.open();
            WinPrint.document.write(
                `<div id='barcode_area' style='width:100%;margin-top:50px;margin:0 auto; text-align:center'>
        <style>@page {    size:  auto; 
    margin-top: 0.04in;
    margin-left: 0.68in;
    margin-right: 0.56in;
    margin-bottom: 0.03in;} svg{margin-top:180px}</style>`

                +
                prtContent.innerHTML +
                `</div>`
            );
            WinPrint.document.close();
            WinPrint.focus();
            WinPrint.print();
            WinPrint.close();

        };
    </script>
@endsection
