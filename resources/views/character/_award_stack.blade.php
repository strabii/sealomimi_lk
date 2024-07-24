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

    {!! Form::open(['url' => 'character/'.$character->slug.'/'.__('awards.awardcase').'/edit']) !!}
    <div class="card mt-2" style="border: 0px">
        <table class="table table-sm">
            <thead class="thead">
                <tr class="d-flex">
                    @if($user && !$readOnly &&
                    ($owner_id == $user->id || $has_power == TRUE))
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
                        @if($user && !$readOnly && ($owner_id == $user->id || $has_power == TRUE))
                            <td class="col-1">{!! Form::checkbox('ids[]', $awardRow->id, false, ['class' => 'award-check', 'onclick' => 'updateQuantities(this)']) !!}</td>
                        @endif

                        <td class="col">
                            <b>Source:</b><span class="ml-2 pr-1">{!! array_key_exists('data', $awardRow->data) ? ($awardRow->data['data'] ? $awardRow->data['data'] : 'No Logs') : 'No Logs' !!}</span>
                            <br/><b>Notes:</b><span class="ml-2 pr-1">{!! array_key_exists('notes', $awardRow->data) ? ($awardRow->data['notes'] ? $awardRow->data['notes'] : 'No Notes') : 'No Notes' !!}</span>
                        </td>
                        
                        @if($user && !$readOnly && ($owner_id == $user->id || $has_power == TRUE))
                            @if($awardRow->availableQuantity)
                                <td class="col-2">
                                    <div class="input-group">
                                        {!! Form::selectRange('', 1, $awardRow->availableQuantity, 1, ['class' => 'quantity-select input-group-prepend', 'type' => 'number', 'style' => 'min-width:40px;height:25px;']) !!}
                                        <div class="input-group-append">
                                            <div class="input-group-text" style="height:25px;">{{ $awardRow->availableQuantity }}</div>
                                        </div>
                                    </div>
                                </td>
                            @else
                                <td class="col-2">
                                    <div class="input-group">
                                        {!! Form::selectRange('', 0, 0, 0, ['class' => 'quantity-select input-group-prepend', 'type' => 'number', 'style' => 'min-width:40px;height:25px;', 'disabled']) !!}
                                        <div class="input-group-append">
                                            <div class="input-group-text" style="height:25px;">{{ $awardRow->availableQuantity }}</div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        @else
                            <td class="col-3">{!! $awardRow->count !!}</td>
                        @endif
                        <td class="col-1">
                            @if(!$awardRow->isTransferrable)
                                <i class="fas fa-lock" data-toggle="tooltip" title="{{ ucfirst(__('lorekeeper.character')) }}-bound {{ __('awards.awards') }} cannot be transferred but can be deleted."></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($user && !$readOnly &&
        ($owner_id == $user->id || $has_power == TRUE))
        <!--<div class="card mt-3"><div class="card-body">-->
        <div class="d-flex mt-3 justify-content-end">
            <div class="d-flex justify-content-end">
                @if($owner_id != null && ($award->is_transferrable || $user->hasPower('edit_inventories')) && $award->is_user_owned)
                    <a class="card-title h5 mr-1 btn btn-sm btn-outline-primary" data-toggle="collapse" href="#transferForm">@if($owner_id != $user->id) [ADMIN] @endif Transfer {{ ucfirst(__('awards.award')) }}</a></div>
                @endif
                    <a class="card-title h5 btn btn-sm btn-outline-primary" data-toggle="collapse" href="#deleteForm">@if($owner_id != $user->id) [ADMIN] @endif Delete {{ ucfirst(__('awards.award')) }}</a></div>
            </div>

            @if($owner_id != null && ($award->is_transferrable || $user->hasPower('edit_inventories')) && $award->is_user_owned)
                <div id="transferForm" class="collapse">
                    <hr/>
                    <p>This will transfer the selected {{ __('awards.awards') }} back to @if($owner_id != $user->id) this user's @else your user @endif  {{ __('awards.awardcase') }}.</p>
                    @if($user && $user->hasPower('edit_inventories'))
                        <p class="alert alert-warning my-2">Admin rank allows transferring {{ __('lorekeeper.character') }}-bound {{ __('awards.awards') }}.</p>
                    @endif
                    <div class="text-right">
                        {!! Form::button('Transfer', ['class' => 'btn btn-primary btn-sm', 'name' => 'action', 'value' => 'take', 'type' => 'submit']) !!}
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
        <!--</div></div>-->
    @endif
    {!! Form::close() !!}
@endif

<script>
    $(document).keydown(function(e) {
    var code = e.keyCode || e.which;
    if(code == 13)
        return false;
    });
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

