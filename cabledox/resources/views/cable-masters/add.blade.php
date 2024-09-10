@extends('layouts.master')
@section('title', 'Add Cable Master')

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card"> 
            @include('layouts.messages')

            <div class="header"><h2>{{ __('Add Cable Master') }}</h2></div>

            <div class="body">
                <form method="POST" action="{{ route('cable-masters.store') }}" data-parsley-validate novalidate> 
                    @csrf <!-- cableTypes  cable-masters-->
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="cable_type_id" class="col-md-3 col-form-label text-md-right">{{ __('Cable Type') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="cable_type_id" id="cable_type_id" class="form-control @error('cable_type_id') is-invalid @enderror" value="{{ old('cable_type_id')}}" required autocomplete="cable_type_id" placeholder="Please enter cable type">
                                    @error('cable_type_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="cores" class="col-md-5 col-form-label text-md-right">{{ __('Cores') }}<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="number" name="cores" id="cores" class="form-control @error('cores') is-invalid @enderror" value="{{ old('cores') ?? 1}}" required autocomplete="cores" placeholder="Enter cores" min="1">
                                            @error('cores')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <label for="cores" class="col-form-label"><i class="fa fa-times"></i></label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="no_of_pair_triple_quad" id="no_of_pair_triple_quad" class="form-control @error('no_of_pair_triple_quad') is-invalid @enderror" value="{{ old('no_of_pair_triple_quad') ?? null}}" min="0" autocomplete="no_of_pair_triple_quad">
                                            @error('no_of_pair_triple_quad')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label for="cores" class="col-form-label" data-toggle="tooltip" data-placement="bottom" data-html="true" title="If using standard Cores <br/> E.G. 3 cores just put 3 in 1st box and leave 2nd box empty. <br/> OR <br/> If using pairs put 3 (no. of cores) in 1st box and 2 (pair) in the 2nd box (3x2)."><i class="fa fa-question-circle-o" style="font-size:21px;"></i></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="core_name_1_0" class="col-md-2 col-form-label text-md-left">{{ __('Core Details:') }}<span class="text-danger"> *</span></label>
                            <div class="col-md-12">
                                <div class="col-md-6 col-md-offset-2">
                                    <table class="table">
                                        <tbody class="htmlToAppend">&nbsp;</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{route('cable-masters.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('page-script')
<script>
    $(document).ready(function() {
    
        createCoreDetails();

        $(document).on('change', '#cores', function() {

            createCoreDetails();

            removeExtraCoresAndWires();
        });

        $(document).on('change', '#no_of_pair_triple_quad', function() {
            
            createCoreDetails();

            removeExtraCoresAndWires();
        });

        $(document).on('change', '.core_name', function() {
            $(this).each(function(k, v) {
                var _pairIndex = $(v).data('pair_index');
                var _thisVal   = $(v).val();

                if($('input[data-pair_index="'+_pairIndex+'"]').not($(v)).length) {
                    $('input[data-pair_index="'+_pairIndex+'"]').not($(v)).each(function (l, k) {
                        if($(k).val() == '') {
                            $(k).val(_thisVal);
                        }
                    })
                }
            });
        });

    });

    function createCoreDetails() {

        var _cores = ($('#cores').val() > 0)? $('#cores').val() : 1;
        var _no_of_pair_triple_quad = ($('#no_of_pair_triple_quad').val() > 0)? $('#no_of_pair_triple_quad').val() : 1;

        var _noOfPair  = _no_of_pair_triple_quad;
        var _html      = '';
        var _htmlArray = [];

        for(i=1; i <= _cores; i++) {
            for(k=0; k < _noOfPair; k++) {

                if($('.tr_data_'+i+'_'+k).length) {
                    continue;
                }

                _html = '<tr class="row_core_details core_'+i+' wire_'+k+' tr_data_'+i+'_'+k+'" data-cores="'+i+'" data-wires="'+k+'" >'+
                    '<td>Core Name/Colour</td>'+
                    '<td><input type="text" name="core_name['+i+']['+k+']" id="core_name_'+i+'_'+k+'" class="form-control core_name" value="" data-core_index="'+i+'" data-pair_index="'+k+'" required autocomplete="core_name" placeholder="Please enter core name/colour"></td>'+
                '</tr>';
                _htmlArray.push(_html);
            }
        }

        $('.htmlToAppend').append(_htmlArray);

        var trArray = [];
        if($('.htmlToAppend tr').length) {
            trArray = $('.htmlToAppend tr').toArray();

            trArray.sort(SortByName);
        }

        $('.htmlToAppend').html(trArray);
    }

    //This will sort your array
    function SortByName(a, b) {
      var aName = $(a).attr('class').toLowerCase();
      var bName = $(b).attr('class').toLowerCase(); 
      return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    }

    function removeExtraCoresAndWires() {

        var _cores = ($('#cores').val() > 0)? $('#cores').val() : 1;
        var _no_of_pair_triple_quad = ($('#no_of_pair_triple_quad').val() > 0)? $('#no_of_pair_triple_quad').val() : 1;

        var cores_num_rows = 1;
        if($('.row_core_details').length > 0) {
            cores_num_rows = Number($('.row_core_details').length);

            cores_num_rows = (cores_num_rows/_no_of_pair_triple_quad);
        }

        for (var i = 1; i <= cores_num_rows; i++) {
            var _tr     = $('.core_' + i);
            var _trCore = $(_tr).data('cores');

            if(_trCore > _cores) {
                _tr.remove();
            }
        }

        var wires_num_rows = 1;
        if($('.row_core_details').length > 0) {
            wires_num_rows = Number($('.row_core_details').length);

            wires_num_rows = ((wires_num_rows/_cores));
        }

        for (var k = 0; k < wires_num_rows; k++) {
            var _tr     = $('.wire_' + k);
            var _trWire = $(_tr).data('wires');

            if(_trWire > Number(_no_of_pair_triple_quad - 1)) {
                _tr.remove();
            }
        }
    }
</script>
@stop