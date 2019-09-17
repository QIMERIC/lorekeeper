@extends('character.design.layout')

@section('design-title') Design Approval Request (#{{ $request->id }}) :: Image @endsection

@section('design-content')
{!! breadcrumbs(['Design Approvals' => 'designs', 'Request (#' . $request->id . ')' => 'designs/' . $request->id, 'Masterlist Image' => 'designs/' . $request->id . '/image']) !!}

@include('character.design._header', ['request' => $request])

<h2>Masterlist Image</h2>

@if($request->has_image)
    <div class="card mb-3">
        <div class="card-body bg-secondary text-white">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3 class="text-center">Main Image</h3>
                    <div class="text-center">
                        <a href="{{ $request->imageUrl }}"><img src="{{ $request->imageUrl }}" class="mw-100" /></a>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="text-center">Thumbnail Image</h3>
                    <div class="text-center">
                        <a href="{{ $request->thumbnailUrl }}"><img src="{{ $request->thumbnailUrl }}" class="mw-100" /></a>
                    </div>
                </div>
            </div>
        </div>
            
        @if(!($request->status == 'Draft' && $request->user_id == Auth::user()->id))
            <div class="card-body">
                <h4 class="mb-3">Credits</h4>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4"><h5>Design</h5></div>
                    <div class="col-lg-8 col-md-6 col-8">
                        @foreach($request->designers as $designer)
                            <div>{!! $designer->displayLink() !!}</div>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4"><h5>Art</h5></div>
                    <div class="col-lg-8 col-md-6 col-8">
                        @foreach($request->artists as $artist)
                            <div>{!! $artist->displayLink() !!}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

@if($request->status == 'Draft' && $request->user_id == Auth::user()->id)
    <p>Select the image you would like to use on the masterlist and an optional thumbnail. Please only upload images that you are allowed to use AND are able to credit to the artist! Note that while staff members cannot edit your uploaded image, they may choose to recrop or upload a different thumbnail.</p>
    {!! Form::open(['url' => 'designs/'.$request->id.'/image', 'files' => true]) !!}
        <div class="form-group">
            {!! Form::label('Image') !!} {!! add_help('This is the image that will be used on the masterlist. Note that the image is not protected in any way, so take precautions to avoid art/design theft.') !!}
            <div>{!! Form::file('image', ['id' => 'mainImage']) !!}</div>
        </div>
        <div class="form-group">
            {!! Form::checkbox('use_cropper', 1, 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'useCropper']) !!}
            {!! Form::label('use_cropper', 'Use Image Cropper', ['class' => 'form-check-label ml-3']) !!} {!! add_help('A thumbnail is required for the upload (used for the masterlist). You can use the image cropper (crop dimensions can be adjusted in the site code), or upload a custom thumbnail.') !!}
        </div>
        <div class="card mb-3" id="thumbnailCrop">
            <div class="card-body">
                <div id="cropSelect">Select an image to use the thumbnail cropper.</div>
                <img src="#" id="cropper" class="hide" />
                {!! Form::hidden('x0', null, ['id' => 'cropX0']) !!}
                {!! Form::hidden('x1', null, ['id' => 'cropX1']) !!}
                {!! Form::hidden('y0', null, ['id' => 'cropY0']) !!}
                {!! Form::hidden('y1', null, ['id' => 'cropY1']) !!}
            </div>
        </div>
        <div class="card mb-3" id="thumbnailUpload">
            <div class="card-body">
                {!! Form::label('Thumbnail Image') !!} {!! add_help('This image is shown on the masterlist page.') !!}
                <div>{!! Form::file('thumbnail') !!}</div>
                <div class="text-muted">Recommended size: {{ Config::get('lorekeeper.settings.masterlist_thumbnails.width') }}px x {{ Config::get('lorekeeper.settings.masterlist_thumbnails.height') }}px</div>
            </div>
        </div>
        <p>
            This section is for crediting the image creators. The first box is for the designer's deviantART name (if any). If the designer has an account on the site, it will link to their site profile; if not, it will link to their dA page. The second is for a custom URL if they don't use dA. Both are optional - you can fill in the alias and ignore the URL, or vice versa. If you fill in both, it will link to the given URL, but use the alias field as the link name.
        </p>
        <div class="form-group">
            {!! Form::label('Designer(s)') !!}
            <div id="designerList">
                <?php $designerCount = count($request->designers); ?>
                @foreach($request->designers as $count=>$designer)
                    <div class="mb-2 d-flex">
                        {!! Form::text('designer_alias['.$designer->id.']', $designer->alias, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Alias']) !!}
                        {!! Form::text('designer_url['.$designer->id.']', $designer->url, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
                        
                        <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer"
                        @if($count != $designerCount - 1)
                            style="visibility: hidden;"
                        @endif
                        >+</a>
                    </div>
                @endforeach
                @if(!count($request->designers))
                    <div class="mb-2 d-flex">
                        {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Alias']) !!}
                        {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
                        <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
                    </div>
                @endif
            </div>
            <div class="designer-row hide mb-2">
                {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Alias']) !!}
                {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
                <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('Artist(s)') !!}
            <div id="artistList">
                <?php $artistCount = count($request->artists); ?>
                @foreach($request->artists as $count=>$artist)
                    <div class="mb-2 d-flex">
                        {!! Form::text('artist_alias['.$artist->id.']', $artist->alias, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
                        {!! Form::text('artist_url['.$artist->id.']', $artist->url, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                        <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist"
                        @if($count != $artistCount - 1)
                            style="visibility: hidden;"
                        @endif
                        >+</a>
                    </div>
                @endforeach
                @if(!count($request->artists))
                    <div class="mb-2 d-flex">
                        {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
                        {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                        <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist">+</a>
                    </div>
                @endif
            </div>
            <div class="artist-row hide mb-2">
                {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
                {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                <a href="#" class="add-artist btn btn-link mb-2" data-toggle="tooltip" title="Add another artist">+</a>
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        </div>
    
    {!! Form::close() !!}
@endif

@endsection

@section('scripts')
@include('widgets._image_upload_js')
@endsection