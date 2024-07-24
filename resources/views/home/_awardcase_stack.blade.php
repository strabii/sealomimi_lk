@if(!$stack)
    <div class="text-center">Invalid {{ __('awards.awards') }} selected.</div>
@else
    <div class="text-center">
        @if($award->has_image)
            <div class="mb-4"><a href="{{ $award->idUrl }}"><img src="{{ $award->imageUrl }}" alt="{{ $award->name }}"/></a></div>
        @endif
        <!--<a href="{{ $award->idUrl }}">{{ $award->name }}</a>-->
    </div>

    @if($award->is_featured)
        <div class="alert alert-success mt-2">
            This {{ __('awards.award') }} is featured!
        </div>
    @endif

    <!--<h5>Owned Stacks</h5>-->

    {!! Form::open(['url' => __('awards.awardcase').'/edit']) !!}
    <div class="card mt-2" style="border: 0px">
        <table class="table table-sm">
            <thead class="thead">
                <tr class="d-flex">
                    @if($user && !$readOnly &&
                    ($stack->first()->user_id == $user->id || $user->hasPower('edit_inventories')))
                        <th class="col-1"><input id="toggle-checks" type="checkbox" onclick="toggleChecks(this)"></th>
                    @endif
                    <th class="col">Logs</th>
                    <th class="col-2">Quantity</th>
                    <th class="col-1"><i class="fas fa-lock invisible"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($stack as $awardRow)
                    <tr id ="awardRow{{ $awardRow->id }}" class="d-flex {{ $awardRow->isTransferrable ? '' : 'accountbound' }}">
                        @if($user && !$readOnly && ($stack->first()->user_id == $user->id || $user->hasPower('edit_inventories')))
                            <td class="col-1">{!! Form::checkbox('ids[]', $awardRow->id, false, ['class' => 'award-check', 'onclick' => 'updateQuantities(this)']) !!}</td>
                        @endif

                        <td class="col">
                            <b>Source:</b><span class="ml-2 pr-1">{!! array_key_exists('data', $awardRow->data) ? ($awardRow->data['data'] ? $awardRow->data['data'] : 'No Logs') : 'No Logs' !!}</span>
                            <br/><b>Notes:</b><span class="ml-2 pr-1">{!! array_key_exists('notes', $awardRow->data) ? ($awardRow->data['notes'] ? $awardRow->data['notes'] : 'No Notes') : 'No Notes' !!}</span>
                        </td>
                        
                        @if($user && !$readOnly && ($stack->first()->user_id == $user->id || $user->hasPower('edit_inventories')))
                            @if($awardRow->availableQuantity)
                                <td class="col-2 input-group">
                                    {!! Form::selectRange('', 1, $awardRow->availableQuantity, 1, ['class' => 'quantity-select', 'type' => 'number', 'style' => 'min-width:40px;max-height:25px;']) !!}
                                    <div class="input-group-append">
                                        <div class="input-group-text" style="height:25px;">/ {{ $awardRow->availableQuantity }}</div>
                                    </div>
                                    @if($awardRow->getOthers()) {{ $awardRow->getOthers() }} @endif
                                </td>
                            @else
                                <td class="col-2 input-group">
                                    {!! Form::selectRange('', 0, 0, 0, ['class' => 'quantity-select', 'type' => 'number', 'style' => 'min-width:40px;max-height:25px;', 'disabled']) !!}
                                    <div class="input-group-append">
                                        <div class="input-group-text" style="height:25px;">/ {{ $awardRow->availableQuantity }}</div>
                                    </div>
                                    @if($awardRow->getOthers()) {{ $awardRow->getOthers() }} @endif
                                </td>
                            @endif
                        @else
                            <td class="col-3">{!! $awardRow->count !!}</td>
                        @endif
                        <td class="col-1">
                            @if(!$awardRow->isTransferrable)
                                <i class="fas fa-lock" data-toggle="tooltip" title="Account-bound {{ __('awards.awards') }} cannot be transferred but can be deleted."></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($user && !$readOnly && ($stack->first()->user_id == $user->id || $user->hasPower('edit_inventories')))
        <div class="d-flex justify-content-end mt-3">

            <div class="d-flex justify-content-end">
                @if($award->is_character_owned)
                    <a class="card-title h5 mr-1 btn btn-sm btn-outline-primary" href="#characterTransferForm" data-toggle="collapse">@if($stack->first()->user_id != $user->id) [ADMIN] @endif Transfer {{ ucfirst(__('awards.award')) }} to {{ ucfirst(__('lorekeeper.character')) }}</a>
                @endif
                <a class="card-title h5 mr-1 btn btn-sm btn-outline-primary" href="#transferForm" data-toggle="collapse">@if($stack->first()->user_id != $user->id) [ADMIN] @endif Transfer {{ ucfirst(__('awards.award')) }}</a>
                <a class="card-title h5 mr-1 btn btn-sm btn-outline-primary" href="#deleteForm" data-toggle="collapse">@if($stack->first()->user_id != $user->id) [ADMIN] @endif Delete {{ ucfirst(__('awards.award')) }}</a>
            </div>
        </div>
            @if($award->is_character_owned)
                <div id="characterTransferForm" class="collapse">
                    <hr/>
                    <p>This will transfer the selected {{ __('awards.award') }}(s) to a {{ __('lorekeeper.character') }}'s {{__('awards.awardcase')}}.</p>
                    <div class="form-group">
                        {!! Form::select('character_id', $characterOptions, null, ['class' => 'form-control mr-2 default character-select']) !!}
                    </div>
                    <div class="text-right">
                        {!! Form::button('Transfer to Character', ['class' => 'btn btn-primary btn-sm', 'name' => 'action', 'value' => 'characterTransfer', 'type' => 'submit']) !!}
                    </div>
                </div>
            @endif

            @if($award->allow_transfer || ($user && $user->hasPower('edit_inventories')))
                <div id="transferForm" class="collapse">
                    <hr/>
                    <p>This will transfer the selected {{ __('awards.awards') }} to another user's  {{ __('awards.awardcase') }}.</p>
                    @if($user && $user->hasPower('edit_inventories'))
                        <p class="alert alert-warning my-2">Admin rank allows transferring account-bound {{ __('awards.awards') }}.</p>
                    @endif
                    <div class="form-group">
                        {!! Form::label('user_id', 'Recipient') !!} {!! add_help('You can only transfer '.__('awards.awards').' to verified users.') !!}
                        {!! Form::select('user_id', $userOptions, null, ['class'=>'form-control']) !!}
                    </div>
                    <div class="text-right">
                        {!! Form::button('Transfer to User', ['class' => 'btn btn-primary btn-sm', 'name' => 'action', 'value' => 'transfer', 'type' => 'submit']) !!}
                    </div>
                </div>
            @endif

            <div id="deleteForm" class="collapse">
                <hr/>
                <p>Deleting a {{ __('awards.award') }} is not reversible. Are you sure you want to delete the selected {{ __('awards.awards') }}?</p>
                <div class="text-right">
                    {!! Form::button('Delete', ['class' => 'btn btn-danger btn-sm', 'name' => 'action', 'value' => 'delete', 'type' => 'submit']) !!}
                </div>
            </div>

        
        
    @endif
    {!! Form::close() !!}
@endif

<script>
    $(document).keydown(function(e) {
    var code = e.keyCode || e.which;
    if(code == 13)
        return false;
    });
    $('.default.character-select').selectize();
    function toggleChecks($toggle) {
        $.each($('.award-check'), function(index, checkbox) {
            $toggle.checked ? checkbox.setAttribute('checked', 'checked') : checkbox.removeAttribute('checked');
            updateQuantities(checkbox);
        });
    }
    function updateQuantities($checkbox) {
        var $rowId = "#awardRow" + $checkbox.value
        $($rowId).find('.quantity-select').prop('name', $checkbox.checked ? 'quantities[]' : '')
    }
</script>

