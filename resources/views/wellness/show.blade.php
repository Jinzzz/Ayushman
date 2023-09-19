@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Wellness Details</h3>
    
    <div class="show-container">
    
            <p><strong>Wellness Name:</strong> {{$show->wellness_name}}</p>
            <p><strong>Wellness Description:</strong> {{ $show->wellness_description}}</p>
            <p><strong>Wellness Inclusions:</strong> {{ $show->wellness_inclusions}}</p>
            <p><strong>Terms and Conditions:</strong> {{ $show->wellness_terms_conditions}}</p>
            <p><strong>Branch:</strong> {{ $show->branch->branch_name }}</p>
            <p><strong>Wellness Cost:</strong> {{ $show->wellness_cost}}</p>
            <p><strong>Wellness Duration:</strong> {{ $show->wellness_duration}}</p>
            <p><strong>Remarks:</strong> {{ $show->remarks}}</p>
           
            
           <a class="btn btn-secondary ml-2" href="{{ route('wellness.index') }}"><i class="fa fa-times" aria-hidden="true"></i>Back</a>
       
</div>

@endsection
