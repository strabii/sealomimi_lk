@extends('user.layout')

@section('profile-title') {{ $user->name }}'s {{ucfirst(__('awards.awardcase'))}} @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, ucfirst(__('awards.awardcase')) => $user->url . '/awardcase']) !!}

<h1>
    {!! $user->displayName !!}'s {{ ucfirst(__('awards.awardcase')) }}
    @if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
        <a href="http://127.0.0.1:8000/admin/grants/awards" class="float-right btn btn-outline-info" id="grantButton"><i class="fas fa-cog"></i> Admin</a>
    @endif
</h1>

@if(count($awards))

@foreach($awards as $categoryId=>$categoryAwards)
    <div class="card mb-3 awardcase-category">
        <h5 class="card-header awardcase-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
            <!--<a class="small awardcase-collapse-toggle collapse-toggle " href="#{!! isset($categories[$categoryId]) ? str_replace(' ', '', $categories[$categoryId]->name) : 'miscellaneous' !!}" data-toggle="collapse">Show</a></h3>-->
        </h5>
        <div class="card-body awardcase-body collapse show" id="{!! isset($categories[$categoryId]) ? str_replace(' ', '', $categories[$categoryId]->name) : 'miscellaneous' !!}">
            @foreach($categoryAwards->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $awardId=>$stack)
                        <div class="col-sm-3 col-6 text-center case-award" data-id="{{ $stack->first()->pivot->id }}" data-name="{{ $stack->first()->name }} {{ __('awards.award') }}"> <!--data-name="{{ $user->name }}'s {{ $stack->first()->name }}"-->
                            <div class="mb-1">
                                <a href="#" class="awardcase-stack {{ $stack->first()->is_featured ? 'alert alert-success' : '' }}"><img src="{{ $stack->first()->imageUrl }}" alt="{{ $stack->first()->name }}"  /></a>
                            </div>
                            <div>
                                <a href="#" class="awardcase-stack awardcase-stack-name">@if($stack->first()->user_limit != 1) [x{{ $stack->sum('pivot.count') }}]@endif {{ $stack->first()->name }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach

@else
    <div class="card mb-3 card-body awards-body">
        <div>No {{ __('awards.awards') }} earned.</div>
    </div>
@endif

<div class="text-right">
    <a href="{{ url($user->url.'/'.__('awards.award').'-logs') }}">[view {{__('awards.awards')}} logs]</a>
</div>

@endsection

@section('scripts')
<script>

$( document ).ready(function() {
    $('.awardcase-stack').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent().parent();
        loadModal("{{ url(__('awards.awardcase')) }}/" + $parent.data('id'), $parent.data('name'));
    });
});

</script>
@endsection
