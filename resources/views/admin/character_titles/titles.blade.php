@extends('admin.layout')

@section('admin-title') Design Names @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Design Names' => 'admin/data/character-titles']) !!}

<h1>Design Names</h1>

<p>This is a list of design names that can be applied to characters. Design names are optional, and pre-set design names may be created here for ease of use, or custom design names can be given to individual characters when editing their traits, etc.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/character-titles/create') }}"><i class="fas fa-plus"></i> Create New Design Name</a></div>
@if(!count($titles))
    <p>No design names found.</p>
@else
    <table class="table table-sm title-table">
        <tbody id="sortable" class="sortable">
            @foreach($titles as $title)
                <tr class="sort-item" data-id="{{ $title->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        {!! $title->displayName !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/character-titles/edit/'.$title->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/data/character-titles/sort']) !!}
        {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
        {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>

$( document ).ready(function() {
    $('.handle').on('click', function(e) {
        e.preventDefault();
    });
    $( "#sortable" ).sortable({
        items: '.sort-item',
        handle: ".handle",
        placeholder: "sortable-placeholder",
        stop: function( event, ui ) {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        },
        create: function() {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        }
    });
    $( "#sortable" ).disableSelection();
});
</script>
@endsection
