@if(!empty($getTestParameter))
	<div class= "form-group row border">
		<div class="col-md-6 border test-result">
			<h5>Tests</h5>
		</div>
		<div class="col-md-6 border test-result">
			<h5>Output</h5>
		</div>
			@foreach($getTestParameter as $key => $testParameter)
			<div class="col-md-6 border test-result test-parameter">
				<h6>{{$testParameter['parameter_name']}}</h6>
				<input type ="hidden" name="test_parameter_id[]"value="{{$testParameter['id']}}"/>				     
				@php
					$testResultOutput = null;
				@endphp
				@if(isset($jobTestResult[$key]->id))
				@php
					$testResultOutput = $jobTestResult[$key]->output;
				@endphp
				<input type="hidden" name="test_result_id[]"	value="{{$jobTestResult[$key]->id}}"/>
				@endif
		    </div>	
			<div class="col-md-6 border test-result">		           
				<input type="text" name="output[]" class="form-control" value="{{$testResultOutput}}" readonly>
			</div>   
	@endforeach
	</div>
@endif