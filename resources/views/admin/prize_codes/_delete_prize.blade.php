@if($prize)
    {!! Form::open(['url' => 'admin/prizecodes/delete/'.$prize->id]) !!}

    <p>You are about to delete the prize code <strong>{{ $prize->name }}</strong>.</p>
    <p>Are you sure you want to delete <strong>{{ $prize->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Code', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid code selected.
@endif
