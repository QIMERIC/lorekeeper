@extends('admin.layout')

@section('admin-title') Items @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Items' => 'admin/data/items', ($item->id ? 'Edit' : 'Create').' Item' => $item->id ? 'admin/data/items/edit/'.$item->id : 'admin/data/items/create']) !!}

<h1>{{ $item->id ? 'Edit' : 'Create' }} Item
    @if($item->id)
        <a href="#" class="btn btn-outline-danger float-right delete-item-button">Delete Item</a>
    @endif
</h1>

{!! Form::open(['url' => $item->id ? 'admin/data/items/edit/'.$item->id : 'admin/data/items/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $item->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 100px x 100px</div>
    @if($item->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            {!! Form::label('Item Category (Optional)') !!}
            {!! Form::select('item_category_id', $categories, $item->item_category_id, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            {!! Form::label('Item Rarity (Optional)') !!} {!! add_help('This should be a number.') !!}
            {!! Form::text('rarity', $item && $item->rarity ? $item->rarity : '', ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            {!! Form::label('Reference Link (Optional)') !!} {!! add_help('An optional link to an additional reference') !!}
            {!! Form::text('reference_url', $item->reference_url, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col">
    {!! Form::label('Item Artist (Optional)') !!} {!! add_help('Provide the artist\'s dA alias or, failing that, a link. If both are provided, the alias will be used as the display name for the link.') !!}
        <div class="row">
            <div class="col">
                <div class="form-group">
                    {!! Form::text('artist_alias', $item && $item->artist_alias ? $item->artist_alias : '', ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    {!! Form::text('artist_url', $item && $item->artist_url ? $item->artist_url : '', ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $item->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::label('Uses (Optional)') !!} {!! add_help('A short description of the item\'s use(s). Supports raw HTML if need be, but keep it brief.') !!}
    {!! Form::text('uses', $item && $item->uses ? $item->uses : '', ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('allow_transfer', 1, $item->id ? $item->allow_transfer : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('allow_transfer', 'Allow User → User Transfer', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is off, users will not be able to transfer this item to other users. Non-account-bound items can be account-bound when granted to users directly.') !!}
</div>

<h3>Availability Information</h3>

<div class="row">
    <div class="col">
        <div class="form-group">
            {!! Form::label('release', 'Original Source (Optional)') !!} {!! add_help('The original source of the item. Should be brief.') !!}
            {!! Form::text('release', $item && $item->source ? $item->source : '', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            {!! Form::label('shops[]', 'Purchase Location(s) (Optional)') !!} {!! add_help('You can select up to 10 shops at once.') !!}
            {!! Form::select('shops[]', $shops, $item && $item->data['shops'] ? $item->data['shops'] : '', ['id' => 'shopsList', 'class' => 'form-control', 'multiple']) !!}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            {!! Form::label('prompts[]', 'Drop Location(s) (Optional)') !!} {!! add_help('You can select up to 10 prompts at once.') !!}
            {!! Form::select('prompts[]', $prompts, $item && $item->data['prompts'] ? $item->data['prompts'] : '', ['id' => 'promptsList', 'class' => 'form-control', 'multiple']) !!}
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit($item->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($item->id)
    <h3>Item Tags</h3>
    <p>Item tags indicate extra functionality for the item. Click on the edit button to edit the specific item tag's data.</p>
    @if(count($item->tags))
        <table class="table">
            <thead>
                <tr>
                    <th>Tag</th>
                    <th>Active?</th>
                    <th></th>
                </tr>
            </thead>
            @foreach($item->tags as $tag)
                <tr>
                    <td>{!! $tag->displayTag !!}</td>
                    <td class="{{ $tag->is_active ? 'text-success' : 'text-danger' }}">{{ $tag->is_active ? 'Yes' : 'No' }}</td>
                    <td class="text-right"><a href="{{ url('admin/data/items/tag/'.$item->id.'/'.$tag->tag) }}" class="btn btn-outline-primary">Edit</a></td>
                </tr>
            @endforeach
        </table>
    @else 
        <p>No item tags attached to this item.</p> 
    @endif
    <div class="text-right">
        <a href="{{ url('admin/data/items/tag/'.$item->id) }}" class="btn btn-outline-primary">Add a Tag</a>
    </div>
    
    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._item_entry', ['imageUrl' => $item->imageUrl, 'name' => $item->displayName, 'description' => $item->parsed_description, 'searchUrl' => $item->searchUrl])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('#shopsList').selectize({
            maxItems: 10
        });

    $('#promptsList').selectize({
        maxItems: 10
    });
    
    $('.delete-item-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/items/delete') }}/{{ $item->id }}", 'Delete Item');
    });
});
    
</script>
@endsection