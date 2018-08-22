@extends('layouts.app')

@section('content')
    <div class="jumbotron mt-3">
        <h1>Best rates</h1>
        <form 
            action="{{ route('home.post') }}"
            method="post"
        >
            {{ csrf_field() }}
            @include('form.select', [
                'field' => 'give', 
                'selected' => $give,
                'options' => $currencies])
            @include('form.select', [
                'field'=>'get', 
                'selected' => $get,
                'options' => $currencies])
            <button type="submit" class="btn btn-primary mb-2">See best rate</button>
        </form>  
            
        <p class="lead">
            @if($rate)
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Exchange</th>
                      <th scope="col">Give</th>
                      <th scope="col">Get</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{{ $rate->exchange->title}}</td>
                      <td>{{ $rate->rate_from }}</td>
                      <td>{{ $rate->rate_to }}</td>
                    </tr>
                  </tbody>
                </table>                
            @else
                no data
            @endif
        </p>
    </div>
@endsection
