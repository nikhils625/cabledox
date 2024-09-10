@extends('layouts.master')
@section('title', 'View Cable Master')

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">  
            @include('layouts.messages')
            <div class="header"><h2>{{ __('View Cable Master') }}</h2></div>
            <div class="body">
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="cable_type_id" class="col-md-3 col-form-label text-md-left"><strong>{{ __('Cable Type') }}</strong></label>
                                <div class="col-md-7 view-details">
                                    <span>{{$cableMaster->cable_type_id}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="cores" class="col-md-5 col-form-label text-md-left"><strong>{{ __('Cores') }}</strong></label>
                                <div class="col-md-7 view-details">
                                    <span>{{ @$cableMaster->cores}} @if(@$cableMaster->no_of_pair_triple_quad > 1) * {{ @$cableMaster->no_of_pair_triple_quad}} @endif</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 form-group row">
                            <label for="no_of_pair_triple_quad" class="col-md-2 col-form-label text-md-left"><strong>{{ __('Core Details') }}</strong></label>
                            <div class="col-md-12">
                                <div class="col-md-6 col-md-offset-2">
                                    <table class="table">
                                        <tbody class="htmlToAppend">
                                            @if(!$cableMaster->cableMasterCoreDetails->isEmpty())
                                                @foreach($cableMaster->cableMasterCoreDetails as $key => $detail)
                                                    <tr class="row_core_details">
                                                        <td>Core Name/Colour</td>
                                                        <td><span>{{ $detail->core_name }}</span></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <a href="{{route('cable-masters.index')}}" class="btn btn-default" title="Back">{{ __('Back') }}</a>

                    <a href="{{route('cable-masters.edit', $cableMaster->id)}}" class="btn btn-primary">{{__('Edit')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop